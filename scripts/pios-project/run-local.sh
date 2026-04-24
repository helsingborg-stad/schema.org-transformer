# !/bin/bash

SCRIPT_DIR="$(dirname "$(readlink -f "$0")")"

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

tmp=$(mktemp)
echo $(fetch_paginated) >> "$tmp"

php ../../router.php \
    --source "$tmp" \
    --transform pios_project \
    --outputformat json \
    --idprefix pios-

if [ $? -ne 0 ]; then
    echo "FAILED to transform request"
fi

rm "$tmp"
