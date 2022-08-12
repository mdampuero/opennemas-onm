PATH=$PATH:/home/opennemas/current/bin
dir="$(dirname $0)/.."
output_path="$dir/output"

[ -d $output_path ] && rm -rf $output_path/* || mkdir -p $output_path

for database in $(console core:instance:list -f BD_DATABASE | cut -d' ' -f 4); do
    echo "Updating database $database..."
    echo "$(echo 'SET @database='$database';' | cat - $dir/src/check.sql)" > $dir/src/checkDatabase.sql
    console database:execute-script $dir/src/changes.sql -d $database
    console database:execute-script $dir/src/checkDatabase.sql   -d $database > $output_path/out

    echo -e "\nChecking database $database..." >> $output_path/result
    grep -q "category	1	cover_id	1	cover_id	A	1	NULL	NULL	YES	BTREE		
result
TRUE
" $output_path/out \
        && { rm $output_path/out; echo "OK" >> $output_path/result; } \
        || { mv $output_path/out $output_path/$database.out; \
            echo "FAIL" >> $output_path/result; }
done
