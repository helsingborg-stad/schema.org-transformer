#!/bin/bash
SCRIPT_DIR="$(dirname "$(readlink -f "$0")")"
TYPESENSE_PATH=${TYPESENSE_BASE_PATH}/collections/pre-schools/documents
TMPFILE=$(mktemp)
if [ -z ${PRE_SCHOOL_API_URL} ]; then
    echo "Missing env variable PRE_SCHOOL_API_URL"; exit 1
fi
which php >/dev/null
if [ $? -ne 0 ]; then
    echo "PHP command missing or not in path"; exit 1
fi
cd ${SCRIPT_DIR}

(
    flock -n 9 || exit 1

    logger -t pre-school "FETCHING FROM API..."

    # Retrieve and transform pre-school data
    php ../../router.php \
        --source ${PRE_SCHOOL_API_URL} \
        --transform pre_school \
        --outputformat jsonl \
        --output ${TMPFILE} \
        --paginator wordpress \
        --idprefix R \
        --typesense_apikey "${TYPESENSE_APIKEY}" \
        --typesense_host "${TYPESENSE_HOST}" \
        --typesense_port "${TYPESENSE_PORT}"

    if [ $? -ne 0 ]; then
        echo "FAILED to transform request"
    else
        # Clear collection
        echo "Deleting documents"
        curl ${TYPESENSE_PATH}?filter_by=@type:PreSchool -X DELETE -H "x-typesense-api-key: ${TYPESENSE_APIKEY}"

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
            if [ ! -z ${PRE_SCHOOL_MONITOR_URL} ]; then curl -s ${PRE_SCHOOL_MONITOR_URL} >/dev/null; fi
        fi
    fi
    # Remove temp file
    rm -f ${TMPFILE}
) 9>/tmp/pre-school.lock