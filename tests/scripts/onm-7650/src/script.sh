PATH=$PATH:/home/opennemas/current/bin
dir="$(dirname $0)/.."
output_path="$dir/output"

[ -d $output_path ] && rm -rf $output_path/* || mkdir -p $output_path

for instaceName in $(console core:instance:list -f BD_DATABASE | cut -d' ' -f 2 | sed -e 's/,$//'); do
  if [ $instaceName != 'diariodepontevedra' ] && [ $instaceName != 'elprogreso' ] && [ $instaceName != 'galiciae' ]
  then
    echo "Updating instance $instaceName..."
    console database:execute-script $dir/src/changes.sql -i $instaceName
    console database:execute-script $dir/src/check.sql   -i $instaceName > $output_path/out

    echo -e "\nChecking instance $instaceName..." >> $output_path/result
    grep -q "OK" $output_path/out \
        && { rm $output_path/out; echo "OK" >> $output_path/result; } \
        || { mv $output_path/out $output_path/$instaceName.out; \
            echo "FAIL" >> $output_path/result; }
  fi
done
