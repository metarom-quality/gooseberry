#!/usr/bin/env python3

import os

DATABASE="/home/tomate/Warehouse/syte/meta.db"
XLSDIR = "/mnt/c/Users/Natacha/Documents/TempDocs/progen/Formula/"

temp = [i for i in next(os.walk(XLSDIR))[2] if i.endswith("xlsx") or i.endswith("xls")]

flist = {}
for i in temp:
    name = i.split(" ")[0].split("-")[0].split(".")[0]
    if name.startswith("~") or name.startswith("PR") or name.startswith("FAB"):
        continue
    else:
        flist[name] = i
