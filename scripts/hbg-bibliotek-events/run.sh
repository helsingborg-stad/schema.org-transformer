#!/bin/bash
SCRIPT_DIR="$(dirname "$(readlink -f "$0")")"
TYPESENSE_PATH=${TYPESENSE_BASE_PATH}/collections/test-events/documents
TMPFILE=$(mktemp)
if [ -z ${AXIELL_EVENTS_URL} ]; then
    echo "Missing env variable AXIELL_EVENTS_URL"; exit 1
fi
which php >/dev/null
if [ $? -ne 0 ]; then
    echo "PHP command missing or not in path"; exit 1
fi
cd ${SCRIPT_DIR}

(
    flock -n 9 || exit 1

    # Retrieve and transform Axiell events data
    php ../../router.php \
        --source ${AXIELL_EVENTS_URL} \
        --transform axiell_events \
        --outputformat jsonl \
        --output ${TMPFILE} \
        --idprefix ax- \
        --externalbaseurl https://bibliotekfh.se/evenemang#/events/ \
        --excludetags "rådgivning"

    if [ $? -ne 0 ]; then
        echo "FAILED to transform request"
    else
        # Clear collection
        echo "Deleting documents"
        curl -X DELETE \
            -H "x-typesense-api-key: ${TYPESENSE_APIKEY}" \
            -H "Content-Type: application/json" \
            "${TYPESENSE_BASE_PATH}/collections/test-events/documents?filter_by=x-created-by:=municipio%3A%2F%2Fschema.org-transformer%2Faxiell-events"

        if [ $? -ne 0 ]; then
            echo "FAILED to delete documents"
        fi

        # POST result to typesense
        echo "Creating documents"
        curl ${TYPESENSE_PATH}/import?action=create -X POST --data-binary @${TMPFILE} -H "Content-Type: text/plain" -H "x-typesense-api-key: ${TYPESENSE_APIKEY}"

        if [ $? -ne 0 ]; then
            echo "FAILED to upload document"
        fi

        # Clear typesense cache
        echo "Clearing Typesense cache"
        curl -H "X-TYPESENSE-API-KEY: ${TYPESENSE_APIKEY}" -X POST ${TYPESENSE_BASE_PATH}/operations/cache/clear

        if [ $? -ne 0 ]; then
            echo "FAILED to clear Typesense cache"
        else
            # Call monitoring url if set
            if [ -n "$AXIELL_EVENTS_MONITOR_URL" ]; then curl -s "$AXIELL_EVENTS_MONITOR_URL" >/dev/null; fi
        fi

    fi
    # Remove temp file
    rm -f ${TMPFILE}
) 9>/tmp/hbg-bibliotek-events.lock