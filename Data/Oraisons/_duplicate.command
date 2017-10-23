#!/bin/bash

#  Ce script duplique toutes les oraisons de saint Cyrille d'Alexandrie
#+ en remplaçant le nom du fichier par le saint voulu,
#+ et en vidant le fichier.

cd "/Users/frromain/Sites/Livrets/Data/Oraisons"
find . -name '0628_Irenee_*' | while read
do
	f=`echo "$REPLY"`
	fn=`echo "$REPLY" | sed 's/0628_Irenee_/1028_Simon_Jude_/g'`
    cp "$f" "$fn"
    echo "" > "$fn"
done
