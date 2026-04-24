#!/bin/bash
SCRIPT_DIR="$(dirname "$(readlink -f "$0")")"
TYPESENSE_PATH=${TYPESENSE_BASE_PATH}/collections/pios-projects/documents

if [ -z ${PIOS_API_KEY} ]; then
    echo "Missing env variable PIOS_API_KEY"; exit 1
fi
which php >/dev/null
if [ $? -ne 0 ]; then
    echo "PHP command missing or not in path"; exit 1
fi
cd ${SCRIPT_DIR}


# Fetch paginated data from PIOS API and transform it as if coming from a single request
function fetch_paginated() {
     pageNumber=1
     pageSize=100

     # loop over pages
     while :; do
          # fetch a response like {"records":[...],"totalRecords":123}
          response=$(curl \
               -sb \
               --header "Accept: text/plain" \
               --header "ApiKey: ${PIOS_API_KEY}" \
               --request GET \
               "https://pios.dimatech.se/api/pios/public/projects?filter.readyForExportOnly=false&pageNumber=${pageNumber}&pageSize=${pageSize}")

          # stop when no records are returned
          if jq -e '.records | length == 0' >/dev/null <<<"$response"; then
               break
          fi

          echo "$response"
          pageNumber=$((pageNumber + 1))
     done | jq -s '
          {
               records: ([.[].records] | add),
               totalRecords: (.[0].totalRecords)
          }
          ' # combine all records into a single array and take totalRecords from the first response
}

(
    flock -n 9 || exit 1

    TMPFILE=$(mktemp)
    tmpsource=$(mktemp)
    echo $(fetch_paginated) >> "$tmpsource"
    php ../../router.php \
        --source "$tmpsource" \
        --transform pios_project \
        --outputformat jsonl \
        --idprefix pios- \
        --output ${TMPFILE}

    rm -f "$tmpsource"

    if [ $? -ne 0 ]; then
        echo "FAILED to transform request"
    else
        # Clear collection
        echo "Deleting documents"
        curl -X DELETE \
            -H "x-typesense-api-key: ${TYPESENSE_APIKEY}" \
            -H "Content-Type: application/json" \
            "${TYPESENSE_BASE_PATH}/collections/pios-projects/documents?filter_by=x-created-by:=municipio%3A%2F%2Fschema.org-transformer%2Fpios-project"

        if [ $? -ne 0 ]; then
            echo "FAILED to delete documents"
        fi

        # POST result to typesense
        echo "Creating documents"
        curl ${TYPESENSE_PATH}/import?action=create -X POST --data-binary @${TMPFILE} -H "Content-Type: text/plain" -H "x-typesense-api-key: ${TYPESENSE_APIKEY}"

        if [ $? -ne 0 ]; then
            echo "FAILED to upload document"
        fi

        # Clear typesense cache
        echo "Clearing Typesense cache"
        curl -H "X-TYPESENSE-API-KEY: ${TYPESENSE_APIKEY}" -X POST ${TYPESENSE_BASE_PATH}/operations/cache/clear

        if [ $? -ne 0 ]; then
            echo "FAILED to clear Typesense cache"
        else
            # Call monitoring url if set
            if [ -n "$TIX_EVENTS_MONITOR_URL" ]; then curl -s "$TIX_EVENTS_MONITOR_URL" >/dev/null; fi
        fi

    fi
    # Remove temp file
    rm -f ${TMPFILE}
) 9>/tmp/pios-projects.lock