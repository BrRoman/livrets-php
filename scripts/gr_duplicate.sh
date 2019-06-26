#!/bin/bash

#  Ce script duplique les fichiers indiquées dans le find
#+ en remplaçant le nom du fichier par la référence voulue.

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
cd "$SCRIPT_DIR/../static/data/GR/communion"
find . -type f -name '152*' | while read
do
	f=`echo "$REPLY"`
	fn=`echo "$REPLY" | sed 's/152/423_2/g'`
    echo "$fn"
    #cp "$f" "$fn"
done

exit

