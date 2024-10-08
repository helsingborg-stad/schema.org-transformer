#!/bin/bash
SCRIPT_DIR="$(dirname "$(readlink -f "$0")")"

if [ -z ${WORDPRESS_LEGACY_EVENT_PATH} ]; then
    echo "Missing env variable WORDPRESS_LEGACY_EVENT_PATH"; exit 1
fi
if [ -z ${TYPESENSE_APIKEY} ]; then
    echo "Missing env variable TYPESENSE_APIKEY"; exit 1
fi
if [ -z ${TYPESENSE_BASE_PATH} ]; then
    echo "Missing env variable TYPESENSE_BASE_PATH"; exit 1
fi
which php
if [ $? -ne 0 ]; then
    echo "PHP command missing or not in path"; exit 1
fi
cd ${SCRIPT_DIR}

# Retreive and transform Stratsys export
php ../../../router.php \
    --source ${WORDPRESS_LEGACY_EVENT_PATH} \
    --transform wp_legacy_event \
    --outputformat json \
    --idprefix L


if [ $? -ne 0 ]; then
    echo "FAILED to transform request"
fi