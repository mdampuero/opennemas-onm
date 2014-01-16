#!/bin/bash
if ! which inotifywait > /dev/null; then
    echo "You must install the inotify-tools package to use this script";

    exit 1;
fi

usage() {
    echo "generate-doc.sh [-c] [-h|?]";
}

# Absolute path to this script. /home/user/bin/foo.sh
SCRIPT=$(readlink -f $0)
# Absolute path this script is in. /home/user/bin
SCRIPTPATH=`dirname $SCRIPT`

while getopts "ch?" OPTION
do
     case $OPTION in
         h)
             usage
             exit 1
             ;;
         c)
             CI="-ci"
             ;;
     esac
done

while true; do
    inotifywait -qq -r -e modify $SCRIPTPATH/../app/ $SCRIPTPATH/../src/ $SCRIPTPATH/../vendor/Onm/ --excludei "(tpl|js|css|jpg|png|yml|yaml)$" &&
    clear &&
    ant phpdoc;
done
