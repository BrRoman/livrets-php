#!/bin/bash

#  Ce script duplique toutes les lectures du 14e vendredi per annum impair,
#+ en remplaçant le nom du fichier par le jour voulu,
#+ et en vidant le fichier.

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
cd "$SCRIPT_DIR/../static/data/lectures"
find . -name 'pa_10_3_*' | sort | while read
#find . -name 'pa_14_*_1_2*' | sort | while read
#find . -name 'pa_14_6_ev*' | sort | while read
do
	f=`echo "$REPLY"`
    fn=`echo "$REPLY" | sed 's/pa_10_3_/ste_famille_fer_/g'`
    #fn=`echo "$REPLY" | sed 's/pa_14_\(.\)_1_2/pa_13_\1_1_1/g'`
    #fn=`echo "$REPLY" | sed 's/pa_14_6_ev/pa_34_4_ev/g'`
	#echo "$fn"
	cp "$f" "$fn"
	echo "" > "$fn"
done
