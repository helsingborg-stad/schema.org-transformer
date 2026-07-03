#!/bin/bash

# Required environment variables
variables=(
    "TYPESENSE_API_KEY" 
    "TYPESENSE_HOST"
    "TYPESENSE_PORT"
    "TYPESENSE_PROTOCOL"
    "REACHMEE_HELSINGBORG_PATH"
    "REACHMEE_HELSINGBORG_MONITOR_URL"
    "REACHMEE_INTRANAT_HELSINGBORG_PATH"
    "REACHMEE_INTRANAT_HELSINGBORG_MONITOR_URL"
    "WP_EVENTS_API_URL"
    "WP_EVENTS_MONITOR_URL"
    "WORDPRESS_EXHIBITION_EVENT_PATH"
    "WORDPRESS_EXHIBITION_EVENTS_MONITOR_URL"
    "ELEMENTARY_SCHOOL_API_URL"
    "ELEMENTARY_SCHOOL_MONITOR_URL"
    "PRE_SCHOOL_API_URL"
    "PRE_SCHOOL_MONITOR_URL"
    "WP_LEGACY_EVENTS_API_URL"
    "WP_LEGACY_EVENTS_MONITOR_URL"
    "AXIELL_EVENTS_URL"
    "AXIELL_EVENTS_MONITOR_URL"
    "TIX_EVENTS_API_URL"
    "TIX_EVENTS_MONITOR_URL"
    "STRATSYS_INNOVATION_PATH"
    "STRATSYS_INNOVATION_AUTH"
    "STRATSYS_INNOVATION_CLIENTID"
    "STRATSYS_INNOVATION_CLIENTSECRET"
    "STRATSYS_INNOVATION_MONITOR_URL"
)

# Check if each required environment variable is set
missing_variables=()

for var in "${variables[@]}"; do
    if [ -z "${!var}" ]; then
        missing_variables+=("$var")
    fi
done

if [ ${#missing_variables[@]} -gt 0 ]; then
    echo "Error: The following environment variables are not set:"
    printf ' - %s\n' "${missing_variables[@]}"
    exit 1
fi