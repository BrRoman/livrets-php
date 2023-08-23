#!/bin/bash

#  Ce script duplique toutes les oraisons indiquées dans le find
#+ en remplaçant le nom du fichier par la référence voulue,
#+ et en vidant le fichier.

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
cd "$SCRIPT_DIR/../static/data/oraisons"
find . -type f -name 'bmv_16_*' | sort | while read
do
	f=`echo "$REPLY"`
	fn=`echo "$REPLY" | sed 's/bmv_16_/bmv_14_/g'`
    #echo "$fn"
    cp "$f" "$fn"
    echo "" > "$fn"
done

exit

