#!/bin/bash

#  Ce script duplique toutes les oraisons indiquées dans le find
#+ en remplaçant le nom du fichier par la référence voulue,
#+ et en vidant le fichier.

cd "/home/frromain/Documents/Informatique/Prog/Web/Sites/livrets/Data/Oraisons"
find . -type f -name '0421*fr*' | while read
do
	f=`echo "$REPLY"`
	fn=`echo "$REPLY" | sed 's/\.txt/-old\.txt/g'`
    #echo "$fn"
    cp "$f" "$fn"
done
