#!/bin/bash

#  Ce script duplique toutes les lectures du 14e vendredi per annum impair,
#+ en remplaçant le nom du fichier par le jour voulu,
#+ et en vidant le fichier.

cd "/media/fr_romain/FR_ROMAIN/Sites/livrets/Data/Lectures/"
#find . -name 'pa_14_*_1_2*' | sort | while read
find . -name 'pa_14_6_ev*' | sort | while read
do
	f=`echo "$REPLY"`
    #fn=`echo "$REPLY" | sed 's/pa_14_\(.\)_1_2/pa_22_\1_1_2/g'`
    fn=`echo "$REPLY" | sed 's/pa_14_6_ev/pa_22_5_ev/g'`
	#echo "$fn"
	cp "$f" "$fn"
	echo "" > "$fn"
done
