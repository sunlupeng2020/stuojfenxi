<!-- stuojzyalllist.php -->
<!-- 查询所有OJ作业完成情况，一共布置了多少道题，每个学生完成了多少道题 -->
<?php
require_once("connections/conn.php");
mysqli_query($conn,"set names 'utf8'");
if(isset($_POST['banji']) && isset($_POST['course']))
{
	$banjiid = $_POST['banji'];
	$courseid = $_POST['course']; 
	// echo "$banjiid,$courseid,$chapterid";
	$sql="select `coursename` from `course` where `id`=".$courseid;
	$result = mysqli_query($conn,$sql);
	$row = mysqli_fetch_assoc($result);
	$coursename = $row['coursename'];//得到课程名
	$sql="select name from banji where id=".$banjiid;
	$result = mysqli_query($conn,$sql);
	$row = mysqli_fetch_assoc($result);
	$banjiname = $row['name'];//得到班级名称
	//查询为该班该课程布置的题目数
	$sql = "select count(*) from chapojques where banjiid=".$banjiid;
	// echo "$sql";
	$result = mysqli_query($conn,$sql);
	$row = mysqli_fetch_array($result);
	$timushu = $row[0];
	$sql = "select stuno,name,gender from student where banjiid=".$banjiid;//查询学生信息
	$sturesult = mysqli_query($conn,$sql);
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>班级的OJ任务</title>
</head>
<body>
	<h3>
		<?php echo "$banjiname";?>&nbsp;<?php echo "$coursename";?>&nbsp;OJ练习任务完成情况表
	</h3>
	<h4>共布置了<?php echo "$timushu"; ?>道题</h4>
	<table id="banjiojtimu" border="1" >
		<tr><td>序号</td><td>学号</td><td>姓名</td><td>性别</td><td>正确题目数</td><td>提交题目数</td><td>提交次数</td></tr>
	<?php
	$stuxuhao=1;
	while($row = mysqli_fetch_array($sturesult))
	{
	?>
	<tr><td><?php echo "$stuxuhao";?></td>
		<td><?php echo $row[0];?></td>
		<td><?php echo $row[1];?></td>
		<td><?php echo $row[2];?></td>
		<td><?php 
		$correctsql="select count(distinct questionid) from stuchallenged where questionid in(select quesid from chapojques where chapterid in(select id from chapter where courseid=$courseid) and banjiid=$banjiid) and stuno={$row[0]} and result='答案正确'";
		$correctresult = mysqli_query($conn,$correctsql);
		$correctresultrow =	mysqli_fetch_array($correctresult);
		echo "$correctresultrow[0]";
		?></td>
		<td><?php
		$subsql="select count(distinct questionid) from stuchallenged where questionid in(select quesid from chapojques where  chapterid in(select id from chapter where courseid=$courseid) and banjiid=$banjiid) and stuno={$row[0]}";
		$subresult = mysqli_query($conn,$subsql);
		$subresultrow =	mysqli_fetch_array($subresult);
		echo "$subresultrow[0]";
		 ?></td>
		<td><?php
		$subcsql="select count(questionid) from stuchallenged where questionid in(select quesid from chapojques where  chapterid in(select id from chapter where courseid=$courseid) and banjiid=$banjiid) and stuno={$row[0]}";
		$subcresult = mysqli_query($conn,$subcsql);
		$subcresultrow =	mysqli_fetch_array($subcresult);
		echo "$subcresultrow[0]";
		 ?></td>
	</tr>
	<?php
	$stuxuhao++;
    }
	?>
	</table>
</body>
</html>
