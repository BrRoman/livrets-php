#!/bin/bash

#  Ce script duplique toutes les oraisons indiquées dans le find
#+ en remplaçant le nom du fichier par la référence voulue,
#+ et en vidant le fichier.
#+ 909 : simple
#+ 910 : pc la/fr, so simple
#+ 911 : pc et so la/fr.

cd "/Users/frromain/Sites/Livrets/Data/Oraisons"
find . -name 'tp_1_1_*' | while read
do
	f=`echo "$REPLY"`
	fn=`echo "$REPLY" | sed 's/tp_1_1_/0421_/g'`
    cp "$f" "$fn"
    echo "" > "$fn"
done
