#!/usr/bin/python3
import sys
# get sys package for file arguments etc
if(len(sys.argv) != 4) :
  print ("Usage: correlate3.py col1 col2 (selection); Nparams = ",sys.argv)
  sys.exit(-1)
import pymysql
import numpy as np
import scipy.stats as sp
con = pymysql.connect(host='127.0.0.1', user='s2552113', passwd='CRy2022@123456', db='s2552113_website')
cur = con.cursor()
col1 = sys.argv[1]
col2 = sys.argv[2]
sel  = sys.argv[3]
sql = "SELECT %s,%s FROM Compounds where %s" % (col1,col2,sel)
cur.execute(sql)
nrows = cur.rowcount
ds = cur.fetchall()
ads = np.array(ds)
print ("correlation is",sp.pearsonr(ads[:,0],ads[:,1])," over ",nrows,"data")
con.close()
