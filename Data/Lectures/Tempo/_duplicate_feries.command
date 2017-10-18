#!/bin/bash

#  Ce script duplique toutes les lectures du 14e vendredi per annum impair,
#+ en remplaçant le nom du fichier par le jour voulu,
#+ et en vidant le fichier.

find . -name 'pa_14_1_5_*' | while read
do
	f=`echo "$REPLY"`
	fn=`echo "$REPLY" | sed 's/pa_14_1_5_/pa_28_1_2_/g'`
    cp "$f" "$fn"
    echo "" > "$fn"
done
