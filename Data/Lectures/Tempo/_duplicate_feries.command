#!/bin/bash

#  Ce script duplique toutes les lectures du 14e vendredi per annum impair,
#+ en remplaçant le nom du fichier par le jour voulu,
#+ et en vidant le fichier.

cd "/Users/frromain/Sites/Livrets/Data/Lectures/Tempo"
find . -name 'pa_14_5_1_*' | while read
do
	f=`echo "$REPLY"`
	fn=`echo "$REPLY" | sed 's/pa_14_5_1_/pa_29_5_1_/g'`
    cp "$f" "$fn"
    echo "" > "$fn"
done
