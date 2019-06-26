#!/bin/bash

#  This script cleans the content of the data directory.

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
cd "`pwd`/../static/data"

#+ First, we add insecable spaces before (or after) special characters,
#+ and we replace strange accented vowels, which elsewhere don't exit accented:
find . \( -name '*.txt' -o -name '*.tex' \) | while read
do
	sed -i "s/ / /g" "$REPLY"
	sed -i "s/\([:;!?]\)/\~\1/g" "$REPLY"
	sed -i "s/«/«~/g" "$REPLY"
   	sed -i "s/«/«\~/g" "$REPLY"
   	sed -i "s/»/\~»/g" "$REPLY"
   	sed -i "s/\~\~/\~/g" "$REPLY"
   	sed -i "s/ \~/\~/g" "$REPLY"
   	sed -i "s/\~ /\~/g" "$REPLY"
   	sed -i "s/ \~/\~/g" "$REPLY"
   	sed -i "s/\~ /\~/g" "$REPLY"
   	sed -i "s/\~\~/\~/g" "$REPLY"
   	sed -i "s/\.\.\./…/g" "$REPLY"
   	sed -i "s/á/á/g" "$REPLY"
   	sed -i "s/é/é/g" "$REPLY"
   	sed -i "s/í/í/g" "$REPLY"
   	sed -i "s/ó/ó/g" "$REPLY"
   	sed -i "s/ú/ú/g" "$REPLY"	
done

## Then we delete unwanted files, such as logs etc.:
find . -type f \( -name '*.*log' -o -name '*.gtex' -o -name '*.glog' -o -name '*.*aux' -o -name '*.f*' \) -exec rm {} \;

# Now we clean the Lectures, replacing "\n" by " ", and then doubles spaces by simple spaces.
# But as 'sed' cannot replace ends of lines, we launch a separated Python script:
find . -name '*.txt' -path '*lectures*' | while read
do
	"$SCRIPT_DIR/cleaner.py" "$REPLY"
done
