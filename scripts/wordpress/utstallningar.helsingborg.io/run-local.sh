#!/bin/bash
SCRIPT_DIR="$(dirname "$(readlink -f "$0")")"

if [ -z ${WORDPRESS_EXHIBITION_EVENT_PATH} ]; then
    echo "Missing env variable WORDPRESS_EXHIBITION_EVENT_PATH"; exit 1
fi
which php >/dev/null
if [ $? -ne 0 ]; then
    echo "PHP command missing or not in path"; exit 1
fi
cd ${SCRIPT_DIR}

php ../../../router.php \
    --source ${WORDPRESS_EXHIBITION_EVENT_PATH} \
    --transform wp_exhibition_event \
    --outputformat json \
    --paginator wordpress \
    --output /tmp/exhibitions.json \
    --idprefix R

if [ $? -ne 0 ]; then
    echo "FAILED to transform request"
fi