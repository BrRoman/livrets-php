#!/bin/bash

#  Ce script duplique toutes les oraisons de saint Cyrille d'Alexandrie
#+ en remplaçant le nom du fichier par le saint voulu,
#+ et en vidant le fichier.

find . -name '0628_Irenee_*' | while read
do
	f=`echo "$REPLY"`
	fn=`echo "$REPLY" | sed 's/0628_Irenee_/1009_Denis_/g'`
    cp "$f" "$fn"
    echo "" > "$fn"
done
