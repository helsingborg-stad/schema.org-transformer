#!/bin/bash
SCRIPT_DIR="$(dirname "$(readlink -f "$0")")"

if [ -z ${PRE_SCHOOL_API_URL} ]; then
    echo "Missing env variable PRE_SCHOOL_API_URL"; exit 1
fi
which php >/dev/null
if [ $? -ne 0 ]; then
    echo "PHP command missing or not in path"; exit 1
fi
cd ${SCRIPT_DIR}

# Retrieve and transform pre-school data
php ../../router.php \
    --source ${PRE_SCHOOL_API_URL} \
    --transform pre_school \
    --outputformat json \
    --paginator wordpress \
    --idprefix R \
    --typesense_apikey "${TYPESENSE_APIKEY}" \
    --typesense_host "${TYPESENSE_HOST}" \
    --typesense_port "${TYPESENSE_PORT}"

if [ $? -ne 0 ]; then
    echo "FAILED to transform request"
fi