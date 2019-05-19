#!/usr/bin/env python3
# coding: utf-8

# This script is launched by cleaner.command.
# It replaces ends of lines by a space,
# and doubles spaces by a simple one.

import sys
import os
import re

file_path = sys.argv[1]
fichier = open(file_path, "r")
txt = fichier.read()
fichier.close()

txt = re.sub("\n", " ", txt)
txt = re.sub("     ", " ", txt)
txt = re.sub("    ", " ", txt)
txt = re.sub("   ", " ", txt)
txt = re.sub("  ", " ", txt)

fichier = open(file_path, "w")
fichier.write(txt)
fichier.close()



