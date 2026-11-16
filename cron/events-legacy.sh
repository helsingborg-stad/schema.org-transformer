#!/bin/sh
exec "$(dirname "$0")/run-job.sh" run/Event.legacy.php "$@"
