# selenium结合PhantomJS()访问郑州师范学院OJ平台，得到OJ平台上各题目的提交情况，通过数量等信息
# 写入数据库stuoj的zznuojques表

# 导入selenium的
from selenium import webdriver
from lxml import etree
import re
from selenium.webdriver.common.by import By
import connsql

def getQuesDescInfo(quesno): # 得到题号为quesno的题目描述的信息
    driver = webdriver.PhantomJS()
    quesUrl = "http://47.95.10.46/problem.php?pid="+str(quesno)
    quesinfo = {}
    driver.get(quesUrl)
    # 题目描述
    quesstr = "题目描述："
    quesinfo['desc'] = driver.find_elements_by_xpath("//div[@id='problemDesc']/pre")[0].text
    quesstr += quesinfo['desc']+"<br/>"
    # 输入
    quesinfo['input'] =driver.find_elements_by_xpath("//div[@id='problemInput']/pre")[0].text
    quesstr += "输入："+quesinfo['input']+"<br/>"
    # 输出
    quesinfo['output'] =driver.find_elements_by_xpath("//div[@id='problemOut']/pre")[0].text
    quesstr += "输出："+quesinfo['output']+"<br/>"
    # 样例输入
    quesinfo['datain'] =driver.find_element_by_id("dataInContent").text
    quesstr += "样例输入："+quesinfo['datain']+"<br/>"
    # 样例输出
    quesinfo['dataout'] = driver.find_element_by_id("dataOutContent").text
    quesstr += "样例输出："+quesinfo['dataout']
    # print(quesstr)
    # return quesinfo
    return quesstr

def isTimuExist(quesno): # 题目是否存在
    sql = "select count(*) from zznuojques where id=%s"
    cur.execute(sql,(quesno,))
    rest = cur.fetchone()
    return rest


def getojallquesnofromdatabase(): # 得到所有题目的id
    sql = "select id from zznuojques"
    cur1 = connsql.conn.cursor()
    cur1.execute(sql)
    results = cur1.fetchall()
    cur1.close()
    return results


def getAllQuestion():# 得到所有题号
    driver = webdriver.PhantomJS()
    url = "http://47.95.10.46/problemset.php?p="
    for i in range(21, 22):
        urlp = url + str(i)
        # 访问第i页
        driver.get(urlp)
        # 得到包含题目的
        # timuids = driver.find_elements_by_xpath("//tbody[@id='oj-ps-problemlist']/tr/td[2]/")
        # 题号
        timuids = driver.find_elements_by_xpath("//td[2]")
        # 题目的标题
        timutitles = driver.find_elements_by_xpath("//td[3]")
        # 题目的通过数、提交数等
        timutijiaoshus = driver.find_elements_by_xpath("//td[6]")
        # for timuid in timuids:
        #     print(timuid.text)
        # for timutijiaoshu in timutijiaoshus:
        #     print(timutijiaoshu.text)
        for i in range(0,len(timuids)):
            # 将题目信息写入数据库
            sql = "insert into zznuojques(ID,title,accept,submit,passlv,descr)values(%s,%s,%s,%s,%s,%s)"
            timuid = timuids[i].text # 题目ID
            timutitle = timutitles[i].text # 题目标题
            pattern = re.compile(r'\d+')
            timu3shuju =pattern.findall(timutijiaoshus[i].text) # 题目数据
            timuaccept = timu3shuju[0] # 题目通过次数
            timusubmit = timu3shuju[1] # 题目提交次数
            timudesc = getQuesDescInfo(timuid) #得到题目的描述信息
            print(timuid)
            if int(timusubmit) != 0:
                timupasslv = int(timuaccept)/int(timusubmit) # 题目通过率
            else:
                timupasslv = 0
            # print(timuid, timutile, timuaccept, timusubmit, timupasslv)
            if isTimuExist(timuid)[0] == 1:
                sql = "update zznuojques set accept=%s,submit=%s,passlv=%s,descr=%s where ID=%s"
                # print(sql)
                cur.execute(sql, (timuaccept,timusubmit,timupasslv,timudesc,timuid))
            else:
                cur.execute(sql, (timuid, timutitle, timuaccept, timusubmit, timupasslv,timudesc))

        # for trline in timustr:
        #     # 找到题号那一格
        #     timuidtd = trline.find_element_by_tag_name('td[1]')
        #     timuid=timuidtd.text
        #     # timuidtd
        #     print(timuid)

# aquesinfo = getQuesInfo(1000)
# print(aquesinfo)
# quesstr1=getQuesDescInfo(1001)
# print(quesstr1)
# print(isTimuExist(10000)[0])

if __name__ == '__main__':
    # print("hello")
    cur = connsql.conn.cursor()
    getAllQuestion()
    cur.close()

# sql = "update zznuojques set accept=%s,submit=%s,passlv=%s,describe=%s where ID=%s"
# print(sql)
# cur.execute(sql, (timuaccept, timusubmit, timupasslv, timudesc, timuid))
