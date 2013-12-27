#!/bin/bash
if ! which inotifywait > /dev/null; then
    echo "You must install the inotify-tools package to use this script";

    exit 1;
fi

while getopts “c:r:p:v” OPTION
do
     case $OPTION in
         h)
             usage
             exit 1
             ;;
         c)
             CI="-ci"
             ;;
         ?)
             usage
             exit
             ;;
     esac
done

while true; do
    inotifywait -qq -r -e modify app/ src/ vendor/Onm/ --excludei "(tpl|js|css|jpg|png|yml|yaml)$" &&
    clear &&
    ant phpunit$CI;
done
