#!/bin/bash
SCRIPT_DIR="$(dirname "$(readlink -f "$0")")"

if [ -z ${WP_EVENTS_API_URL} ]; then
    echo "Missing env variable WP_EVENTS_API_URL"; exit 1
fi
which php >/dev/null
if [ $? -ne 0 ]; then
    echo "PHP command missing or not in path"; exit 1
fi
cd ${SCRIPT_DIR}


# TODO: ADD --paginator wordpress \
php ../../router.php \
    --source ${WP_EVENTS_API_URL} \
    --transform wp_event \
    --outputformat jsonl \
    --idprefix WPH- \
    --paginator wordpress

if [ $? -ne 0 ]; then
    echo "FAILED to transform request"
fi