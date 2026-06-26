#!/bin/bash

set -euo pipefail

if [ "$#" -ne 2 ]; then
    echo "Usage: $0 <label> <script-path>" >&2
    exit 1
fi

label="$1"
scriptPath="$2"

source /etc/profile

/bin/bash "$scriptPath" \
    > >(sed "s/^/[$label] /" >> /proc/1/fd/1) \
    2> >(sed "s/^/[$label] /" >> /proc/1/fd/2)