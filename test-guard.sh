#!/bin/bash
if ! which inotifywait > /dev/null; then
    echo "You must install the inotify-tools package to use this script";

    exit 1;
fi

while true; do
    inotifywait -qq app/*/*/*.php src/*/*/*.php vendor/Onm/*/*/*.php app/tests/*/*/*.php &&
    clear &&
    ant phpunit;
done
