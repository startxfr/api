#!/bin/bash

db="sxapi"
out_dir="/var/www/html/startx/api/api-lib/tmp"
tmp_file="tmp_file"
echo "print('_ ' + db.getCollectionNames())" > $tmp_file
cols=`mongo $db $tmp_file | grep '_' | awk '{print $2}' | tr ',' ' '`
for c in $cols
do
    mongoexport -d $db -c $c -o "$out_dir/dump_${db}_${c}.json"
done
rm $tmp_file
