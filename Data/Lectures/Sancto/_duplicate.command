#!/bin/bash

#  Ce script duplique toutes les lectures de sainte Marie-Madeleine,
#+ en remplaçant le nom du fichier par le jour voulu,
#+ et en vidant le fichier.

cd "/Users/frromain/Sites/Livrets/Data/Lectures/Sancto"
find . -name '0722_Marie_Mad_*' | while read
do
	f=`echo "$REPLY"`
	fn=`echo "$REPLY" | sed 's/0722_Marie_Mad_/1028_Simon_Jude_/g'`
    cp "$f" "$fn"
    echo "" > "$fn"
done
