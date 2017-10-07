#!/bin/bash

#  Ce script duplique toutes les lectures du dimanche
#+ en remplaçant le nom du fichier par le jour voulu,
#+ et en vidant le fichier.

find . -name 'pa_12_A_0_*' | while read
do
	f=`echo "$REPLY"`
	fn=`echo "$REPLY" | sed 's/pa_12_A_0_/pa_27_A_0_/g'`
    cp "$f" "$fn"
    echo "" > "$fn"
done

exit


