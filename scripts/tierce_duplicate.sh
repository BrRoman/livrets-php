#!/bin/bash

#  Ce script duplique les fichiers indiquées dans le find
#+ en remplaçant le nom du fichier par la référence voulue.

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
cd "$SCRIPT_DIR/../static/data/tierce/antiennes"
find . -type f -name 'adjuva_me*' | while read
do
	f=`echo "$REPLY"`
	fn=`echo "$REPLY" | sed 's/adjuva_me/innuebant/g'`
    echo "$fn"
    #cp "$f" "$fn"
done

exit

