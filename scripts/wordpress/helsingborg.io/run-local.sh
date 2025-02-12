#!/bin/bash
SCRIPT_DIR="$(dirname "$(readlink -f "$0")")"

if [ -z ${WORDPRESS_EVENT_PATH} ]; then
    echo "Missing env variable WORDPRESS_EVENT_PATH"; exit 1
fi
which php >/dev/null
if [ $? -ne 0 ]; then
    echo "PHP command missing or not in path"; exit 1
fi
cd ${SCRIPT_DIR}

# Retreive and transform Stratsys export
php ../../../router.php \
    --source ${WORDPRESS_EVENT_PATH} \
    --transform wp_release_event \
    --outputformat json \
    --paginator wordpress \
    --output /tmp/wordpress.json \
    --idprefix R


if [ $? -ne 0 ]; then
    echo "FAILED to transform request"
fi