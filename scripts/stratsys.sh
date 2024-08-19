#!/bin/bash

if [ -z ${STRATSYS_PATH} ]; then
    echo "Missing env variable STRATSYS_PATH"; exit 1
fi
if [ -z ${STRATSYS_AUTH} ]; then
    echo "Missing env variable STRATSYS_AUTH"; exit 1
fi
if [ -z ${STRATSYS_CLIENTID} ]; then
    echo "Missing env variable STRATSYS_CLIENTID"; exit 1
fi
if [ -z ${STRATSYS_CLIENTSECRET} ]; then
    echo "Missing env variable STRATSYS_CLIENTSECRET"; exit 1
fi
if [ -z ${TYPESENSE_APIKEY} ]; then
    echo "Missing env variable TYPESENSE_APIKEY"; exit 1
fi
if [ -z ${TYPESENSE_PATH} ]; then
    echo "Missing env variable TYPESENSE_PATH"; exit 1
fi
TMPFILE=$(mktemp)

# Retreive and transform Stratsys export
php router.php \
    --source ${STRATSYS_PATH} \
    --authpath ${STRATSYS_AUTH} \
    --authclientid ${STRATSYS_CLIENTID} \
    --authclientsecret ${STRATSYS_CLIENTSECRET} \
    --authscope 'exportview.read' \
    --transform stratsys \
    --outputformat jsonl \
    --output ${TMPFILE}

if [ $? -ne 0 ]; then
    echo "FAILED to transform stratsys request to file ${TMPFILE}"
else
    # POST result to typesense
    curl ${TYPESENSE_PATH} -X POST --data-binary @${TMPFILE} -H "Content-Type: text/plain" -H "x-typesense-api-key: ${TYPESENSE_APIKEY}"

    if [ $? -ne 0 ]; then
        echo "FAILED to upload to ${TYPESENSE_PATH}"
    else
        echo "Upload SUCCEEDED"
    fi
fi
# Remove temp file
rm -f ${TMPFILE}