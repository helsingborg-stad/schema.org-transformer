#!/bin/bash
SCRIPT_DIR="$(dirname "$(readlink -f "$0")")"

if [ -z ${WP_LEGACY_EVENTS_API_URL} ]; then
    echo "Missing env variable WP_LEGACY_EVENTS_API_URL"; exit 1
fi
which php >/dev/null
if [ $? -ne 0 ]; then
    echo "PHP command missing or not in path"; exit 1
fi
cd ${SCRIPT_DIR}

# TODO: change day to month for less frequent updates

# Append param "start_date" with the value of todays date in format YYYY-MM-DD to the WP_LEGACY_EVENTS_API_URL if WP_LEGACY_EVENTS_API_URL is a url
if [[ ${WP_LEGACY_EVENTS_API_URL} == http* ]]; then
    START_DATE=$(date -d "-1 day" +%Y-%m-%d) # Default to one day back
    if [[ ${WP_LEGACY_EVENTS_API_URL} == *\?* ]]; then
        WP_LEGACY_EVENTS_API_URL="${WP_LEGACY_EVENTS_API_URL}&start_date=${START_DATE}"
    else
        WP_LEGACY_EVENTS_API_URL="${WP_LEGACY_EVENTS_API_URL}?start_date=${START_DATE}"
    fi
fi


# TODO: ADD --paginator wordpress \
php ../../router.php \
    --source ${WP_LEGACY_EVENTS_API_URL} \
    --transform wp_legacy_event \
    --outputformat jsonl \
    --idprefix L

if [ $? -ne 0 ]; then
    echo "FAILED to transform request"
fi