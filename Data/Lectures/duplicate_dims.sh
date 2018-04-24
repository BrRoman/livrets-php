#!/bin/bash

#  Ce script duplique toutes les lectures du dimanche
#+ en remplaçant le nom du fichier par le jour voulu,
#+ et en vidant le fichier.

cd "/home/frromain/Documents/Informatique/Prog/Web/Sites/livrets/Data/Lectures/"
find . -name 'pa_12_0_A_*' | while read
do
	f=`echo "$REPLY"`
	fn=`echo "$REPLY" | sed 's/pa_12_0_A_/qua_6_0_B_/g'`
    cp "$f" "$fn"
    echo "" > "$fn"
done

exit


