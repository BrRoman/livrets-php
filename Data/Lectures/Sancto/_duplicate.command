#!/bin/bash

#  Ce script duplique toutes les lectures du 14e vendredi per annum impair,
#+ en remplaçant le nom du fichier par le jour voulu,
#+ et en vidant le fichier.

find . -name '0722_Marie_Mad_*' | while read
do
	f=`echo "$REPLY"`
	fn=`echo "$REPLY" | sed 's/0722_Marie_Mad_/1007_ND_Rosaire_/g'`
    cp "$f" "$fn"
    echo "" > "$fn"
done
