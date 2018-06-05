#!/bin/bash

#  Ce script duplique toutes les lectures de sainte Marie-Madeleine,
#+ en remplaçant le nom du fichier par le jour voulu,
#+ et en vidant le fichier.

cd "/media/fr_romain/FR_ROMAIN/Sites/livrets/Data/Lectures/"
find . -name '0101_*' | while read
do
	f=`echo "$REPLY"`
	fn=`echo "$REPLY" | sed 's/0101_/0629_/g'`
    #echo "$fn"
    cp "$f" "$fn"
    echo "" > "$fn"
done

exit

