#!/bin/bash

PATH=$PATH:/home/opennemas/current/bin
dir=$(dirname $0)
output_path="$dir/output"

[ -d $output_path ] || mkdir -p $output_path
[ -f $output_path/result ] && rm $output_path/result

database="onm-instances"
echo "Updating database $database..."

console database:execute-script $dir/changes.sql -d $database
console database:execute-script $dir/check.sql -d $database > $output_path/out

echo -e "\nChecking database $database..." >> $output_path/result
grep -q "extension: 1" $output_path/out \
    && grep -q "extension_meta: 4" $output_path/out \
    && grep -q "basic: 1" $output_path/out \
    && echo "OK"   >> $output_path/result \
    || echo "FAIL" >> $output_path/result

console cache:redis remove --pattern="*extension-54*"

[ -f $output_path/out ] && rm $output_path/out
