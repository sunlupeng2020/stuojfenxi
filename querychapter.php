<?php
// 根据课程号查询章的信息
require_once("connections/conn.php");
error_reporting(E_ERROR|E_WARNING|E_PARSE);
mysqli_query($conn,"set names 'utf8'");
$pid=$_GET["courseid"];//得到课程号
//根据课程号查询章的信息
$res=mysqli_query($conn,"select id,chaptername from chapter where courseid=$pid order by number");
header('Content-type: text/html;charset=utf-8');   //指定发送数据的编码格式为utf8
while($info=mysqli_fetch_array($res)){  
   $str.=$info[0].",".$info[1]; //章号和章名之间用逗号分隔
   $str.=";"; //章和章之间用分号隔开
}
echo $str;
