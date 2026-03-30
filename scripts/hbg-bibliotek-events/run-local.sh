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

# Retrieve and transform Axiell events data
php ../../router.php \
    --source ${AXIELL_EVENTS_URL} \
    --transform axiell_events \
    --outputformat json \
    --idprefix ax- \
    --externalbaseurl https://bibliotekfh.se/evenemang#/events/ \
    --excludetags "rådgivning,inställt"

if [ $? -ne 0 ]; then
    echo "FAILED to transform request"
fi
