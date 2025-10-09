#!/bin/bash
SCRIPT_DIR="$(dirname "$(readlink -f "$0")")"

if [ -z ${WP_EVENTS_API_URL} ]; then
    echo "Missing env variable WP_EVENTS_API_URL"; exit 1
fi
if [ -z ${TYPESENSE_APIKEY} ]; then
    echo "Missing env variable TYPESENSE_APIKEY"; exit 1
fi
if [ -z ${TYPESENSE_BASE_PATH} ]; then
    echo "Missing env variable TYPESENSE_BASE_PATH"; exit 1
fi
which php >/dev/null
if [ $? -ne 0 ]; then
    echo "PHP command missing or not in path"; exit 1
fi
cd ${SCRIPT_DIR}

TMPFILE=$(mktemp)
TYPESENSE_PATH=${TYPESENSE_BASE_PATH}/collections/events-dev/documents

# Retrieve and transform wordpress events to temp file
php -d memory_limit=1024M ../../router.php \
    --source ${WP_EVENTS_API_URL} \
    --paginator wordpress \
    --transform wp_event \
    --outputformat jsonl \
    --output ${TMPFILE} \
    --idprefix WPH-

if [ $? -ne 0 ]; then
    echo "FAILED to transform request to file ${TMPFILE}"
else
    # Clear collection
    echo "Deleting documents"
    curl -X DELETE \
        -H "x-typesense-api-key: ${TYPESENSE_APIKEY}" \
        -H "Content-Type: application/json" \
        "${TYPESENSE_BASE_PATH}/collections/events-dev/documents?filter_by=x-created-by:=municipio%3A%2F%2Fschema.org-transformer%2Fwp-headless"

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