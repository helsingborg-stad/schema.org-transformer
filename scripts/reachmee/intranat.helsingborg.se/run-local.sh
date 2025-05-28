#!/bin/bash
SCRIPT_DIR="$(dirname "$(readlink -f "$0")")"

TMPFILE="/tmp/reachmee.intranat.helsingborg.se.json"

if [ -z ${REACHMEE_INTRANAT_HELSINGBORG_PATH} ]; then
    echo "Missing env variable REACHMEE_INTRANAT_HELSINGBORG_PATH"; exit 1
fi

which php

if [ $? -ne 0 ]; then
    echo "PHP command missing or not in path"; exit 1
fi

cd ${SCRIPT_DIR}

# Retreive and transform Stratsys export
php ../../../router.php \
    --source ${REACHMEE_INTRANAT_HELSINGBORG_PATH} \
    --transform jobposting \
    --outputformat json \
    --output ${TMPFILE}

if [ $? -ne 0 ]; then
    echo "FAILED to transform request to file ${TMPFILE}"
fi
#https://site106.reachmee.com/Public/rssfeed/external.ashx
#7
#I017
#helsingborg
#SE
#1118

#https://site201.reachmee.com/api/public/v1/feed/9?lang=SE&customer=helsingborg&format=json&feed_key=meu4i8d6f1
#https://106.reachmee.com/api/public/v1/feed/7?lang=SE&customer=helsingborg&format=json&feed_key=meu4i8d6f1