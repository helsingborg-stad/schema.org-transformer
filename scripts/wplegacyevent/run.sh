#!/bin/bash
SCRIPT_DIR="$(dirname "$(readlink -f "$0")")"

if [ -z ${WP_LEGACY_EVENTS_API_URL} ]; then
    echo "Missing env variable WP_LEGACY_EVENTS_API_URL"; exit 1
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

# Append param "start_date" with the value of todays date in format YYYY-MM-DD to the WP_LEGACY_EVENTS_API_URL if WP_LEGACY_EVENTS_API_URL is a url
if [[ ${WP_LEGACY_EVENTS_API_URL} == http* ]]; then
    START_DATE=$(date -d "-1 month" +%Y-%m-%d) # Default to one month back
    if [[ ${WP_LEGACY_EVENTS_API_URL} == *\?* ]]; then
        WP_LEGACY_EVENTS_API_URL="${WP_LEGACY_EVENTS_API_URL}&start_date=${START_DATE}"
    else
        WP_LEGACY_EVENTS_API_URL="${WP_LEGACY_EVENTS_API_URL}?start_date=${START_DATE}"
    fi
fi

# Retreive and transform wordpress events to temp file
php -d memory_limit=1024M ../../router.php \
    --source ${WP_LEGACY_EVENTS_API_URL} \
    --paginator wordpress \
    --transform wp_legacy_event \
    --outputformat jsonl \
    --output ${TMPFILE} \
    --idprefix L

if [ $? -ne 0 ]; then
    echo "FAILED to transform request to file ${TMPFILE}"
else
    # Clear collection
    echo "Deleting documents"
    curl "${TYPESENSE_PATH}?filter_by=x-created-by:wp-legacy-transform" -X DELETE -H "x-typesense-api-key: ${TYPESENSE_APIKEY}"

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