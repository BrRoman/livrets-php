#!/bin/bash

#  Ce script duplique toutes les lectures du dimanche
#+ en remplaçant le nom du fichier par le jour voulu,
#+ et en vidant le fichier.

cd "/media/fr_romain/FR_ROMAIN/Sites/livrets/Data/Lectures/"
find . -name 'pa_12_0_A_*' | sort | while read
do
	f=`echo "$REPLY"`
	fn=`echo "$REPLY" | sed 's/pa_12_0_A_/pa_22_0_B_/g'`
    #echo "$fn"
    cp "$f" "$fn"
    echo "" > "$fn"
done

exit


