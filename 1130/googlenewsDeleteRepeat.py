# -*- coding: UTF-8-*-
import requests

from bs4 import BeautifulSoup
import pymysql
from headers import header
from ipproxies import proxiesList
import time
import datetime

db = pymysql.connect(host='60.249.6.104', port=33060, user='root', passwd='ncutim', db='Listing',
                                 charset='utf8')  # 連接資料庫
cursor = db.cursor()
cursor.execute("""SELECT * FROM `news` where ID = "5" """)
db.commit()
sid=cursor.fetchall()
print sid
db.close()
