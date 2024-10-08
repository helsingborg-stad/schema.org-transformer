#!/bin/bash
SCRIPT_DIR="$(dirname "$(readlink -f "$0")")"

if [ -z ${STRATSYS_INNOVATION_PATH} ]; then
    echo "Missing env variable STRATSYS_INNOVATION_PATH"; exit 1
fi
if [ -z ${STRATSYS_INNOVATION_AUTH} ]; then
    echo "Missing env variable STRATSYS_INNOVATION_AUTH"; exit 1
fi
if [ -z ${STRATSYS_INNOVATION_CLIENTID} ]; then
    echo "Missing env variable STRATSYS_INNOVATION_CLIENTID"; exit 1
fi
if [ -z ${STRATSYS_INNOVATION_CLIENTSECRET} ]; then
    echo "Missing env variable STRATSYS_CLIENTSECRET"; exit 1
fi
which php
if [ $? -ne 0 ]; then
    echo "PHP command missing or not in path"; exit 1
fi
cd ${SCRIPT_DIR}

# Retreive and transform Stratsys export
php ../../../router.php \
    --source ${STRATSYS_INNOVATION_PATH} \
    --authpath ${STRATSYS_INNOVATION_AUTH} \
    --authclientid ${STRATSYS_INNOVATION_CLIENTID} \
    --authclientsecret ${STRATSYS_INNOVATION_CLIENTSECRET} \
    --authscope exportview.read \
    --transform stratsys \
    --outputformat json \
    --output /tmp/stratsys.json
