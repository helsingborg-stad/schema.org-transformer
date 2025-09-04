#!/bin/bash
SCRIPT_DIR="$(dirname "$(readlink -f "$0")")"

if [ -z ${ELEMENTARY_SCHOOL_PATH} ]; then
    echo "Missing env variable ELEMENTARY_SCHOOL_PATH"; exit 1
fi
which php >/dev/null
if [ $? -ne 0 ]; then
    echo "PHP command missing or not in path"; exit 1
fi
cd ${SCRIPT_DIR}

# Retreive and transform Stratsys export
php ../../router.php \
    --source ${ELEMENTARY_SCHOOL_PATH} \
    --transform elementary-school \
    --outputformat json \
    --paginator wordpress \
    --idprefix R

if [ $? -ne 0 ]; then
    echo "FAILED to transform request"
fi