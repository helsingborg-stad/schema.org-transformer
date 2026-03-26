#!/bin/bash
SCRIPT_DIR="$(dirname "$(readlink -f "$0")")"

if [ -z ${AXIELL_EVENTS_URL} ]; then
    echo "Missing env variable AXIELL_EVENTS_URL"; exit 1
fi
which php >/dev/null
if [ $? -ne 0 ]; then
    echo "PHP command missing or not in path"; exit 1
fi
cd ${SCRIPT_DIR}

# --source_json_path "$.hits[*].event" \

# Retrieve and transform Axiell events data
php ../../router.php \
    --source ${AXIELL_EVENTS_URL} \
    --transform axiell_events \
    --outputformat json \
    --idprefix AXIELL \

if [ $? -ne 0 ]; then
    echo "FAILED to transform request"
fi