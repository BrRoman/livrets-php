#!/bin/bash

#  Ce script duplique toutes les lectures du dimanche
#+ en remplaçant le nom du fichier par le jour voulu,
#+ et en vidant le fichier.

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
cd "$SCRIPT_DIR/..//static/data/lectures/"
find . -name 'pa_12_0_A_*' | sort | while read
do
	f=`echo "$REPLY"`
	fn=`echo "$REPLY" | sed 's/pa_12_0_A_/tp_7_0_C_/g'`
    echo "$fn"
    #cp "$f" "$fn"
    #echo "" > "$fn"
done

exit


