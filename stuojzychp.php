<!-- stuojzychp.php -->
<!-- 显示某个班学生某课程某章OJ作业完成情况 -->
<?php
require_once("connections/conn.php");
mysqli_query($conn,"set names 'utf8'");
if(isset($_POST['banji']) && isset($_POST['course']) && isset($_POST['chapter']))
{
	$banjiid = $_POST['banji'];
	$courseid = $_POST['course']; 
	$chapterid = $_POST['chapter'];
	// echo "$banjiid,$courseid,$chapterid";
	$sql="select `coursename` from `course` where `id`=".$courseid;
	$result = mysqli_query($conn,$sql);
	$row = mysqli_fetch_assoc($result);
	$coursename = $row['coursename'];//得到课程名
	$sql="select name from banji where id=".$banjiid;
	$result = mysqli_query($conn,$sql);
	$row = mysqli_fetch_assoc($result);
	$banjiname = $row['name'];//得到班级名称
	//查询章名称、章号
	$sql = "select number,chaptername from chapter where id=".$chapterid;
	$result = mysqli_query($conn,$sql);
	$row = mysqli_fetch_array($result);
	$chpnumber = $row[0];//章号
	$chpname = $row[1];//章名称，章标题
	//查询为该班该章布置的题目数
	$sql = "select count(*) from chapojques where banjiid=".$banjiid." and chapterid=".$chapterid;
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
	<?php
	require_once("menu.php");
	?>
	<h3>
		<?php echo "$banjiname";?>&nbsp;<?php echo "$coursename";?>&nbsp;OJ练习任务完成情况表
	</h3>
	<h4>第<?php echo "$chpnumber"; ?>章&nbsp;<?php echo "$chpname"; ?>&nbsp; 布置了<?php echo "$timushu"; ?>道题</h4>


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
		$correctsql="select count(distinct questionid) from stuchallenged where questionid in(select quesid from chapojques where chapterid=$chapterid and banjiid=$banjiid) and stuno={$row[0]} and result='答案正确'";
		$correctresult = mysqli_query($conn,$correctsql);
		$correctresultrow =	mysqli_fetch_array($correctresult);
		echo "$correctresultrow[0]";
		?></td>
		<td><?php
		$subsql="select count(distinct questionid) from stuchallenged where questionid in(select quesid from chapojques where chapterid=$chapterid and banjiid=$banjiid) and stuno={$row[0]}";
		$subresult = mysqli_query($conn,$subsql);
		$subresultrow =	mysqli_fetch_array($subresult);
		echo "$subresultrow[0]";
		 ?></td>
		<td><?php
		$subcsql="select count(questionid) from stuchallenged where questionid in(select quesid from chapojques where chapterid=$chapterid and banjiid=$banjiid) and stuno={$row[0]}";
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