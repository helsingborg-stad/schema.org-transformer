#!/bin/bash
SCRIPT_DIR="$(dirname "$(readlink -f "$0")")"

if [ -z ${WORDPRESS_EXHIBITION_EVENT_PATH} ]; then
    echo "Missing env variable WORDPRESS_EXHIBITION_EVENT_PATH"; exit 1
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
TYPESENSE_PATH=${TYPESENSE_BASE_PATH}/collections/ExhibitionEvents/documents

# Retreive and transform Stratsys export
php ../../../router.php \
    --source ${WORDPRESS_EXHIBITION_EVENT_PATH} \
    --transform wp_exhibition_event \
    --outputformat jsonl \
    --paginator wordpress \
    --output ${TMPFILE} \
    --idprefix L \
    --logger terminal

if [ $? -ne 0 ]; then
    echo "FAILED to transform request to file ${TMPFILE}"
else
    # Clear collection
    echo "Deleting documents"
    curl ${TYPESENSE_PATH}?filter_by=@type:ExhibitionEvent -X DELETE -H "x-typesense-api-key: ${TYPESENSE_APIKEY}"

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
        if [ ! -z ${WORDPRESS_EXHIBITION_EVENTS_MONITOR_URL} ]; then curl -s ${WORDPRESS_EXHIBITION_EVENTS_MONITOR_URL} >/dev/null; fi
    fi
fi
# Remove temp file
rm -f ${TMPFILE}