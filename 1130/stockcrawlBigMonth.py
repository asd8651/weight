# -*- coding: UTF-8_general_ci -*-
#上傳這個月大盤資料到bigmarket資料表
import requests
import request
import json
import pymysql
import datetime
import pandas as pd
import lxml,html5lib
from pandas import Series, DataFrame
import time
from headers import header


start = time.time()
starttime = int(time.strftime("%M", time.localtime()))
day = datetime.datetime.now().strftime("%Y%m%d")

try:
    db = pymysql.connect(host='60.249.6.104', port=33060, user='root', passwd='ncutim', db='Listing',
                         charset='utf8')
except:
    pass
cursor = db.cursor()

params = {"date": day}
headers = {'user-agent': "my-app/0.0.1"}
proxies = {'proxy': "http://192.168.1.3:8080"}
res = requests.get('http://www.twse.com.tw/exchangeReport/FMTQIK',
                   params=params, headers=headers, proxies=proxies)
try:
    bigData = json.loads(res.text)
except:
    pass;
print bigData

if (u'data' in bigData.keys()):
    _data = bigData[u'data']
    for i in range(len(_data)):
        insert = (
            """INSERT  INTO `bigmarket` (`date`, `tradedshares`, `turnover`,`strokecount`,`price`,`changerange`,`time`) VALUES (%s,%s,%s,%s,%s, %s, %s)""")
        da = (
            _data[i][0], _data[i][1], _data[i][2], _data[i][3], _data[i][4], _data[i][5],
            time.strftime("%Y-%m-%d %H:%M:%S", time.localtime()))
        cursor.execute(insert, da)
        try:
            db.commit()
        except:
            pass
print _data[i][0] + 'inserted'
db.close()
end = time.time()
print end - start
# 引入thread 同時上傳上市上貴資料
#ALTER TABLE `1101` ADD `market` VARCHAR(50) NOT NULL AFTER `name`, ADD `coe` VARCHAR(50) NOT NULL AFTER `market`;新增
# 新增欄位alter = """ALTER TABLE `"""+asid+"""` ADD `market` VARCHAR(50) NOT NULL AFTER `name`, ADD `coe` VARCHAR(50) NOT NULL AFTER `market`"""
# cursor.execute(alter)