#!/bin/bash

find . -type f -name '0421_*fr-old.txt' | while read
do
	f=`echo "$REPLY"`
	fn=`echo "$REPLY" | sed 's/-old//g'`
	#echo $fn
	mv "$f" "$fn"
done
