#!/bin/bash
# Script use to save the database collections
# To enable this script in GitHub's pre-commit hook
# add bash <path_to_script>/git_hook_precommit.sh 
# in .git/hooks/pre-commit. If there isn't the file .git/hooks/pre-commit
# copy and rename .git/hooks/pre-commit.sample.

db="sxapi"
out_dir="/var/www/html/startx/api/api-lib/tmp"
tmp_file="tmp_file"
echo "print('_ ' + db.getCollectionNames())" > $tmp_file
cols=`mongo $db $tmp_file | grep '_' | awk '{print $2}' | tr ',' ' '`
for c in $cols
do
	if [[ $c != "logs" ]] && [[ $c != "startx.logs" ]] && [[ $c != "sxapi.session" ]] ; then
		echo -n 'Exporting' $c ' : '
    	mongoexport -d $db -c $c -o "$out_dir/dump_${db}_${c}.json" > /dev/null
	fi
done
rm $tmp_file
exit 0;
