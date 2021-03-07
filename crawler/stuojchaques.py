# 获取学生所有的挑战题目信息
# 包括时间、结果、代码等
# 写入数据库stuoj的stuquestionbh表中

from selenium import webdriver
# from selenium.webdriver.common.by import By
import pymysql
import re
from bs4 import BeautifulSoup
import connsql
# import loginzznuoj
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import  expected_conditions as EC
import ojtmxx
import time
import sys


# driver_path = "D:\\ChromeCoreDownloads\\chromedriver_win32\\chromedriver.exe"
# driver = webdriver.Chrome()
driver = webdriver.PhantomJS()
cur = connsql.conn.cursor()

# 得到OJ平台上某学生（按学号）提交某题目（按题号）的信息列表，并写入数据库
# 注意！这是在不登录OJ平台的情况下获取数据，如果登录，则会出错
def getOjQuesNo(stuno,quesno):# 学号，题目号
    stuquesUrl = "http://47.95.10.46/status.php?pid="+str(quesno)+"&uid="+str(stuno)+"&language=-1&judgeresult=-1"
    driver.get(stuquesUrl)
    # pidtxtbox = driver.find_element_by_name("pid") # 输入题目id的textbox的id
    # uidtxtbox = driver.find_element_by_name("uid") # 输入用户ID的textbox的id
    # pidtxtbox.send_keys(quesno) # 填入题目ID
    # uidtxtbox.send_keys(stuno) # 填入用户ID
    # driver.find_elements_by_xpath("//button[@class='btn btn-default']")[0].click()
    # button.click()
    # 得到学生提交的该题号的信息，若干行，每行有id，学号，题号，结果，占用内存，用时，语言，代码长度，挑战时间等。
    # 如果该次提交在数据库中已存在，则不写入
    questrs = driver.find_elements_by_xpath("//tbody/tr") # 包含多行，每行对应一次提交
    sql = "insert ignore into stuchallenged(challengeid,stuno,questionid,result,memory,timecost," \
          "language,codelength,challtime) values(%s,%s,%s,%s,%s,%s,%s,%s,%s)"
    if len(questrs)>0:
        # 逐行检查学生的提交信息
        for trbj in questrs:
            # 得到学生提交信息各字段的值
            print(trbj)
            print(trbj.text)
            trbjsplit = str(trbj.text).split(' ', 9)
            trbjsplit[1] = str(stuno)  # 有的学生用户名含学号但不是学号，修改为学号，以便写入数据库
            trbjsplit[8] = trbjsplit[8]+' '+trbjsplit[9]  # 合并日期和时间
            del trbjsplit[9]
            # trbjsplit[9] = trbjsplit[0]  # 将提交编号复制一份，用于查询该提交在数据库中是否存在，如果存在，则不写入
            print(trbjsplit)
            # trbjsplit[1] = '204215091027'
            cur.execute(sql, trbjsplit)  # 写入数据库
    # print(soup.text)
    # print(button.text)


def loginzznuoj():  # 登陆
    loginurl = 'http://47.95.10.46/loginpage.php'
    driver.get(loginurl)
    driver.find_element_by_name("username").send_keys('slp')
    driver.find_element_by_name("password").send_keys('slp123456')
    submitbutton = driver.find_element_by_tag_name("button")
    # print(submitbutton.text)
    submitbutton.click()
    # try:
    #     WebDriverWait(driver, 10).until(EC.title_is("ZZNUOJ"))
    # finally:
    #     pass
    

def getsubmitcode(quesno,submitno):# 由提交号，题目号得到提交的代码
    url = "http://47.95.10.46/problemsubmit.php?sid="+str(submitno)+"&pid="+str(quesno) # 访问提交页面
    driver.get(url)
    # 将显示代码的textarea的displays属性由none改为block,以获取其中的代码
    js = "document.getElementById('source').style.display='block';"
    driver.execute_script(js)
    codes = driver.find_element_by_name("source").text
    return codes
    # 调用js脚本print(codeee)


def getstuquestions(stuno):#按学号搜索学生通过题目数、挑战题目数
    # 访问郑州师范学院OJ平台的“排名”页面
    driver.get("http://47.95.10.46/ranklist.php")
    # 找到页面上的输入用户名的文本框
    driver.find_element_by_name("keyword").send_keys(stuno)  # 输入学生学号
    button = driver.find_elements_by_xpath("//button[@class='btn btn-default']")[1]  # .click() # 单击搜索按钮
    #  container.row.input-group.input-group-btn.btn.btn-default
    button.click()
    # 找到学生名字、通过题目数、提交数等的超链接
    link1 = driver.find_elements_by_xpath("//div[@class='col-md-12']/table/tbody/tr/td/a")
    i = 0
    link = link1[0]
    link.click()
    # 找到题号的超链接
    timuhaos = driver.find_elements_by_xpath("//div[@class='well collapse in']/a")
    # print(timuhaos)
    for tihaolink in timuhaos:
        # print(tihaolink.text) #输出题号
        # 将学生做的题号插入到数据库
        sql="insert into stuques(stuno,questionbh)values(%s,%s)"
        cur.execute(sql, (stuno, tihaolink.text))
        # print(stuno, tihaolink.text)


def getStuChallengenum(stuno):# 按学号获取学生挑战次数
    url = "http://47.95.10.46/ranklist.php?keyword="+str(stuno)
    driver.get(url)
    challed = driver.find_elements_by_xpath("//tbody//a")
    cnum=challed[3].text # 挑战数量
    return cnum
    # print(cnum)


def getstudentno(banjiid): # 根据班级ID得到学生学号列表
    sql = "select stuno from student where banjiid =" + str(banjiid)
    cur.execute(sql)
    results = cur.fetchall()  # 用于返回多条数据，得到全部学生学号
    # cur.close()
    return results




def getstudentxuehao():
    banjiids = tuple(range(1, 2))# 各个班级的Id元组
    for banjiid in banjiids:
        print(banjiid)
        sql = "select stuno from student where banjiid =" + str(banjiid)
        cur.execute(sql)
        results = cur.fetchall()  # 用于返回多条数据，得到全部学生学号
        for stuno in results:  # print(result[0])
            # 得到学生完成的题目数
            getstuquestions(stuno)


# getstudentxuehao()# 得到所有学生做的题号
# getOjQuesNo('204215091001', '1003')
# cur.close()


def getBanjiChallengeNum(banjino):# 得到一个班的学生的挑战数量，写入数据库
    stuojusernamelist = getbanjistuojusername(banjino) # 得到该班学生用户名
    sqlupdate = "update student set challenge=%s where ojusername=%s"
    # cur = connsql.conn.cursor()
    for stuojusername in stuojusernamelist:
        # print(stuojusername[0])
        # print(getStuChallengenum(stuojusername[0]))
        cur.execute(sqlupdate,(getStuChallengenum(stuojusername[0]),stuojusername[0]))# 更新学生挑战的次数

# 获取教师为某章布置的OJ平台作业题目
def getChapterOjQuesList(chapter, banjiid=1,teacher='100236'):
    sql = "select quesid from chapojques where chapterid=%s and banjiid=%s and teacher=%s"
    cur.execute(sql,(chapter,banjiid,teacher,))
    return cur.fetchall()


def getbanjistuojusername(banjino): # 根据班级id得到该班学生的用户名列表
    sql = "select ojusername from student where banjiid=%s"
    cur.execute(sql, (banjino,))
    return cur.fetchall()


# 得到学生的挑战信息,根据学生学号
def getstuchallenge():
    sql = "select `questionid`,`challengeid` from `stuchallenged` where `code` is null"
    cur.execute(sql)
    results = cur.fetchall()
    return results


def updatequescode(quesno, submitno, code): # 更新数据库中的代码
    sql = "update `stuchallenged` set `code`=%s where `questionid`=%s and `challengeid`=%s"
    cur.execute(sql,(code, quesno, submitno))


def get_QuesList_UserList_Submit(chapter, banji): # 得到某章某班学生做题情况
    # 测试：获取某章的题目
    questionnolist = getChapterOjQuesList(chapter)  # 得到第某章的OJ平台题目
    stuxhlist = getbanjistuojusername(banji)  # 得到班级的学生用户名列表
    # print(timulist)
    # print(stuxhlist)
    for stuno in stuxhlist:
        stuno1 = stuno[0] # 学生用户名
        for questionno in questionnolist:
            questionno0 = questionno[0]
            # print((stuno1, questionno0))
            getOjQuesNo(stuno1, questionno0)


if __name__ == '__main__':
    chapter = sys.argv[1]
    banji = sys.argv[2]
    #print(chapter)
    #print(banji)
    get_QuesList_UserList_Submit(chapter, banji) #章次，班级
    # cur = connsql.conn.cursor()  # 引用 connsql 中的conn变量
    # getBanjiChallengeNum(1)
    # getStuChallengenum('204215091001')
    # getOjQuesNo('204215091001', '1003')
    # print(getbanjistuojusername(1))
    # cur.close()
    # loginzznuoj() # 登陆校OJ平台
    # time.sleep(2)
    # codes = getsubmitcode(1000,1063215)
    # print(codes)
    # options.addArguments(
    #     "--user-data-dir=" + System.getenv("USERPROFILE") + "/AppData/Local/Google/Chrome/User Data/Default");
    # driver.get("http://47.95.10.46/problemsubmit.php?sid=1063215&pid=1000")

    # results = getstuchallenge()
    # for result in results:
    #     print(result)
    #     url = "http://47.95.10.46/problemsubmit.php?sid=" + str(result[1]) + "&pid=" + str(result[0])  # 访问提交页面
    #     # driver.get(url)
    #     codes = getsubmitcode(result[0], result[1])
    #     if len(codes) > 5000:
    #         codes = codes[0:5000]
    #     print(codes)
    #     updatequescode(result[0], result[1], str(codes).strip()) # 更新学生提交的代码

    # getsubmitcode('1003', '1068443')
    # 接下来，把每个学生提交的每道题都抓下来2021.1.17
    # stuxhlist = getbanjistuojusername(2) # 2班的学生学号列表
    # questionnolist= ojtmxx.getojallquesnofromdatabase() # 从数据库中得到题目ID
    # print(questionnolist)
    # for stuno in stuxhlist:
        # stuno1 = stuno[0] # 学生用户名
    # for i in range(10, 36):
    #     stuno1 = '2042150920'+str(i)
    #     # print(stuno1)
    #     # if int(stuno1) > 204215091003:
    #     for questionno in questionnolist:
    #         questionno0 = questionno[0]
    #         print((stuno1, questionno0))
    #         getOjQuesNo(stuno1, questionno0)
    #         # stuno1 = '204215091032'
    #         # for questionno0 in range(1000, 2200):
    #         #     print((stuno1, questionno0))
    #         #     getOjQuesNo(stuno1, questionno0)




cur.close()
driver.close()
