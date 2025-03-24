#!/bin/bash
SCRIPT_DIR="$(dirname "$(readlink -f "$0")")"

if [ -z ${WORDPRESS_LEGACY_EVENT_PATH} ]; then
    echo "Missing env variable WORDPRESS_LEGACY_EVENT_PATH"; exit 1
fi
which php >/dev/null
if [ $? -ne 0 ]; then
    echo "PHP command missing or not in path"; exit 1
fi
cd ${SCRIPT_DIR}

# Append param "start_date" with the value of todays date in format YYYY-MM-DD to the WORDPRESS_LEGACY_EVENT_PATH if WORDPRESS_LEGACY_EVENT_PATH is a url
if [[ ${WORDPRESS_LEGACY_EVENT_PATH} == http* ]]; then
    START_DATE=$(date -d "-1 month" +%Y-%m-%d) # Default to one month back
    if [[ ${WORDPRESS_LEGACY_EVENT_PATH} == *\?* ]]; then
        WORDPRESS_LEGACY_EVENT_PATH="${WORDPRESS_LEGACY_EVENT_PATH}&start_date=${START_DATE}"
    else
        WORDPRESS_LEGACY_EVENT_PATH="${WORDPRESS_LEGACY_EVENT_PATH}?start_date=${START_DATE}"
    fi
fi

php ../../../router.php \
    --source ${WORDPRESS_LEGACY_EVENT_PATH} \
    --transform wp_legacy_event \
    --outputformat jsonl \
    --paginator wordpress \
    --output /tmp/wordpress.json \
    --idprefix L

if [ $? -ne 0 ]; then
    echo "FAILED to transform request"
fi