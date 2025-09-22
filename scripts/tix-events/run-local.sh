#!/bin/bash
SCRIPT_DIR="$(dirname "$(readlink -f "$0")")"

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
    --outputformat json \
    --idprefix TIX \

if [ $? -ne 0 ]; then
    echo "FAILED to transform request"
fi