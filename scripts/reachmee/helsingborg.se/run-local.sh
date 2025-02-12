#!/bin/bash
SCRIPT_DIR="$(dirname "$(readlink -f "$0")")"

if [ -z ${REACHMEE_HELSINGBORG_PATH} ]; then
    echo "Missing env variable REACHMEE_HELSINGBORG_PATH"; exit 1
fi

which php

if [ $? -ne 0 ]; then
    echo "PHP command missing or not in path"; exit 1
fi
cd ${SCRIPT_DIR}

# Retreive and transform Stratsys export
php ../../../router.php \
    --source ${REACHMEE_HELSINGBORG_PATH} \
    --transform jobposting \
    --outputformat json \
    --output /tmp/reachmee.json

if [ $? -ne 0 ]; then
    echo "FAILED to transform request to file ${TMPFILE}"
fi
