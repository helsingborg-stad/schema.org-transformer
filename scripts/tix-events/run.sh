#!/bin/bash
SCRIPT_DIR="$(dirname "$(readlink -f "$0")")"
TYPESENSE_PATH=${TYPESENSE_BASE_PATH}/collections/events-dev/documents
TMPFILE=$(mktemp)
if [ -z ${TIX_EVENTS_API_URL} ]; then
    echo "Missing env variable TIX_EVENTS_API_URL"; exit 1
fi
which php >/dev/null
if [ $? -ne 0 ]; then
    echo "PHP command missing or not in path"; exit 1
fi
cd ${SCRIPT_DIR}

# Retrieve and transform Tix events data
php ../../router.php \
    --source ${TIX_EVENTS_API_URL} \
    --transform tix_events \
    --outputformat jsonl \
    --output ${TMPFILE} \
    --paginator wordpress \
    --idprefix TIX \

if [ $? -ne 0 ]; then
    echo "FAILED to transform request"
else
    # Clear collection
    echo "Deleting documents"
    curl ${TYPESENSE_PATH}?filter_by=@type:Event%20%26%26%20%40id%3A%3DTIX%2A -X DELETE -H "x-typesense-api-key: ${TYPESENSE_APIKEY}"
    #curl ${TYPESENSE_PATH}?filter_by=@type:Event&filter_by=@id:=TIX* -X DELETE -H "x-typesense-api-key: ${TYPESENSE_APIKEY}"

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
    fi
fi
# Remove temp file
rm -f ${TMPFILE}