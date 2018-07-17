#!/usr/bin/env python3

import sqlite3 as lite
import pandas as pd
import backend.config as conf
import xlrd

class DatabaseAccessor(object):

    def __init__(self, dname):
        self.conn = lite.connect(dname)
        self.curs = self.conn.cursor()
        self.curs.execute(
                "SELECT name FROM sqlite_master WHERE type='table';")
        self.tables = [i[0] for i in self.curs.fetchall()]

    def __repr__(self):
        pres = "Database with tables < "
        for i in self.tables:
            pres += ("\"" + i + "\" ")
        return pres + ">"

    def __del__(self):
        print(str(self) + " is closed.")
        self.conn.close()
        del self

    def create(self, tname, payload):
        query = "CREATE TABLE IF NOT EXISTS {} (".format(tname)
        for i in zip(payload.keys(), payload.values()):
            query += "{0} {1},".format(*i)
        query = query[:-1] + ");"
        self.curs.execute(query)
        self.conn.commit()

    def delete(self, tname):
        query = "DROP TABLE {}".format(tname)
        self.curs.execute(query)
        self.conn.commit()

class TableAccessor(object):

    def __init__(self, dname, tname):
        self.conn = dname.conn
        self.curs = dname.curs
        self.tname = tname
        self.dname = dname
        self.curs.execute("PRAGMA table_info({})".format(tname))
        self.colnames = [i[1] for i in self.curs.fetchall()]

    def __repr__(self):
        pres = "Table {} with columns < ".format(self.tname)
        for i in self.colnames:
            pres += ("\"" + i + "\" ")
        return pres + ">"

    def primary(self, colname):
        try:
            self.dname.delete("temp")
        except:
            pass
        self.curs.execute("SELECT sql FROM sqlite_master WHERE tbl_name = '{}'".format(self.tname))
        query = "CREATE TABLE temp"
        query +=  self.curs.fetchall()[0][0].split(self.tname)[1]
        query = query[:-1] + ", PRIMARY KEY ({}));".format(colname)
        print(query)
        self.curs.execute(query)
        self.conn.commit()
        query = "INSERT INTO temp ({0}) SELECT {0} FROM {1};".format(','.join(self.colnames), self.tname)
        self.curs.execute(query)
        self.conn.commit()
        self.dname.delete(self.tname)
        self.curs.execute("ALTER TABLE temp RENAME TO {};".format(self.tname))
        self.conn.commit()

    def insert(self, payload):
        query = "INSERT INTO {} (".format(self.tname)
        for i in payload:
            query += "{},".format(i)
        query = query[:-1] + ") VALUES ("
        for i in payload:
            query += "{},".format(payload[i])
        query = query[:-1] + ");"
        self.curs.execute(query)
        self.conn.commit()

    def find(self, col, val):
        query = "SELECT * FROM {} WHERE {} LIKE \"{}\";".format(self.tname, col, val)
        self.curs.execute(query)
        return self.curs.fetchall()

    def delete(self, prod):
        query = "DELETE FROM {} WHERE product LIKE \"{}\";".format(self.tname, prod)
        self.curs.execute(query)
        self.conn.commit()

    def register(self, prod):
        try:
            temp = Prod(prod).gendata()
            for i in temp:
                self.insert(i)
        except KeyError:
            print("Failed to load {}, Key Error!".format(prod))
        except Exception as e:
            print("Failed to load {}, Content Error: {}".format(prod, e))

class Xls(object):

    def __init__(self, filepath, sname):
        self.path = filepath
        book = xlrd.open_workbook(self.path)
        slist = book.sheet_names()
        self.sname = sname
        if not self.sname in slist:
            print("XlsError: does not have sheet {}.".format(sname))
        else:
            self.sheet = book.sheet_by_name(sname)

    def __repr__(self):
        return "<Sheet {} in Book {}>".format(self.sname, self.path)

    def find(self, word, suppressed=False):
        for i in range(self.sheet.nrows):
            temprow = [str(v) for v in self.sheet.row_values(i)]
            for j in range(len(temprow)):
                if word in temprow[j]:
                    return (i, j)
        if not suppressed:
            print('XlsError: Key word "{}" not found.'.format(word))
        return None

    def gendata(self):
        pass

class Prod(Xls):

    def __init__(self, fname):
        self.name = fname
        super(Prod, self).__init__(conf.XLSDIR + conf.flist[fname], "PRODUCTION")
        try:
            self.descr = self.sheet.row_values(self.find(self.name)[0])[0]
        except:
            pass

    def gendata(self):
        startline = self.find("FORMULA", True)
        start = startline[0]
        endline = self.find("TOTAL", True) or self.find("Total", True)
        if not endline:
            print("Sheet is not properly formated!\ncheck failed for TOTAL row.")
            return None
        else:
            end = endline[0]
            payload = []
            refcol = self.sheet.row_values(start).index('REF')
            quantcol = self.sheet.row_values(start).index('FORMULA')
        for i in range(start + 1, end):
            temprow = self.sheet.row_values(i)
            rowinfo = {}
            if len(str(temprow[refcol]).split(" ")) > 3:
                rowinfo['instruction'] = '"{}"'.format(temprow[refcol])
            else:
                rowinfo['ingredient'] = '"{}"'.format(str(temprow[refcol]).split(".")[0])
                if "=" in rowinfo['ingredient']:
                    rowinfo['ingredient'] = '"{}"'.format(rowinfo['ingredient'].split(" ")[0].split("=")[0])
            rowinfo['quantity'] = temprow[quantcol] or 0
            rowinfo['product'] = '"{}"'.format(self.name)
            payload.append(rowinfo)
        return payload
