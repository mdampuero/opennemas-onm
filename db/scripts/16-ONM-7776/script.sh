PATH=$PATH:/home/opennemas/current/bin
dir="$(dirname $0)/.."
output_path="$dir/output"

[ -d $output_path ] && rm -rf $output_path/* || mkdir -p $output_path

echo "Updating database onm-instances..."

console database:execute-script $dir/src/changes.sql -d onm-instances
console database:execute-script $dir/src/check.sql   -d onm-instances > $output_path/out

echo -e "\nChecking database onm-instances..." >> $output_path/result
grep -q "Total: 3" $output_path/out \
    && { rm $output_path/out; echo "OK" >> $output_path/result; } \
    || { mv $output_path/out $output_path/onm-instances.out; \
        echo "FAIL" >> $output_path/result; }
