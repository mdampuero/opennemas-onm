#!/bin/bash

PATH=$PATH:/home/opennemas/current/bin
dir=$(dirname $0)
output_path="$dir/output"

[ -d $output_path ] || mkdir -p $output_path
[ -f $output_path/result ] && rm $output_path/result

for database in $(console instance:list -f BD_DATABASE | cut -d' ' -f 4); do
    echo "Updating database $database..."

    console database:execute-script $dir/changes.sql -d $database
    console database:execute-script $dir/check.sql -d $database > $output_path/out

    echo -e "\nChecking database $database..." >> $output_path/result

    url=$(grep "url: " $output_path/out | sed -e "s/url: //g")
    translation_ids=$(grep "translation_ids: " $output_path/out | sed -e "s/translation_ids: //g")

    [ "$url" = "$translation_ids" ] \
        && echo "OK"  >> $output_path/result \
        || echo "FAIL" >> $output_path/result
done

[ -f $output_path/out ] && rm $output_path/out
