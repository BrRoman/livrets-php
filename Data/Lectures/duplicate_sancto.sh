#!/bin/bash

#  Ce script duplique toutes les lectures de sainte Marie-Madeleine,
#+ en remplaçant le nom du fichier par le jour voulu,
#+ et en vidant le fichier.

cd "/home/frromain/Documents/Informatique/Prog/Web/Sites/livrets/Data/Lectures/"
find . -name '0319_*' | while read
do
	f=`echo "$REPLY"`
	fn=`echo "$REPLY" | sed 's/0319_/0501_/g'`
    #echo "$fn"
    cp "$f" "$fn"
    echo "" > "$fn"
done

exit

