#!/bin/bash

#  Ce script duplique toutes les lectures du 14e vendredi per annum impair,
#+ en remplaçant le nom du fichier par le jour voulu,
#+ et en vidant le fichier.

cd "/home/frromain/Documents/Informatique/Prog/Web/Sites/livrets/Data/Lectures/"
find . -name 'qua_1_1_*' | while read
do
	f=`echo "$REPLY"`
	fn=`echo "$REPLY" | sed 's/qua_1_1_/qua_6_3_/g'`
	cp "$f" "$fn"
	echo "" > "$fn"
done
