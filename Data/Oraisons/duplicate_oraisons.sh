#!/bin/bash

#  Ce script duplique toutes les oraisons indiquées dans le find
#+ en remplaçant le nom du fichier par la référence voulue,
#+ et en vidant le fichier.

cd "/media/fr_romain/FR_ROMAIN/Sites/livrets/Data/Oraisons/"
find . -type f -name '0421_*' | sort | while read
do
	f=`echo "$REPLY"`
	fn=`echo "$REPLY" | sed 's/0421/0903/g'`
    #echo "$fn"
    cp "$f" "$fn"
    echo "" > "$fn"
done

exit

