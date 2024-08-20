#!/bin/bash
SCRIPT_DIR="$(dirname "$(readlink -f "$0")")"

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
cd ${SCRIPT_DIR}

TMPFILE=$(mktemp)

# Retreive and transform Stratsys export
php ../router.php \
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
    # Clear collection
    echo "Deleting documents"
    curl ${TYPESENSE_PATH}?filter_by=@type:Article -X DELETE -H "x-typesense-api-key: ${TYPESENSE_APIKEY}"

    if [ $? -ne 0 ]; then
        echo "FAILED to delete documents"
    fi

    # POST result to typesense
    echo "Creating documents"
    curl ${TYPESENSE_PATH}/import?action=create -X POST --data-binary @${TMPFILE} -H "Content-Type: text/plain" -H "x-typesense-api-key: ${TYPESENSE_APIKEY}"

    if [ $? -ne 0 ]; then
        echo "FAILED to upload to ${TYPESENSE_PATH}"
    fi
fi
# Remove temp file
rm -f ${TMPFILE}