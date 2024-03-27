#!/usr/bin/python3

import sys
# get sys package for file arguments etc
if(len(sys.argv) != 4) :
  print ("Usage: histog.py col name where ; Nparams = ",sys.argv)
  sys.exit(-1)
import pymysql## using python do sql query
import numpy as np
import scipy.stats as sp
import matplotlib.pyplot as plt
import io
con = pymysql.connect(host='127.0.0.1', user='s2552113', passwd='CRy2022@123456', db='s2552113_website')
cur = con.cursor()# Create cursor objects

col1 = sys.argv[1]
col2 = sys.argv[3]
xname = sys.argv[2]
sql = "SELECT %s FROM Compounds where %s" % (col1,col2)
cur.execute(sql)# Execute SQL queries using cursors
nrows = cur.rowcount
ds = cur.fetchall()## get output
ads = np.array(ds)
num_bins = 20
# the histogram of the data
n, bins, patches = plt.hist(ads, num_bins, density=0, facecolor='blue', alpha=0.5)
plt.xlabel(xname)
plt.ylabel('N')
image = io.BytesIO()
plt.savefig(image,format='png')
sys.stdout.buffer.write(image.getvalue())
#plt.show()
con.close()# cloe connection
