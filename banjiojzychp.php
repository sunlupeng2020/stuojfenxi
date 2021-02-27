<!-- banjiojzychp.php -->
<!-- 显示为某班布置的OJ作业题目 -->
<?php
require_once("connections/conn.php");
mysqli_query($conn,"set names 'utf8'");
if(isset($_POST['banji'])&&isset($_POST['course']))
{
	$banjiid = $_POST['banji'];
	$courseid = $_POST['course']; 
	// echo "$banjiid,$courseid";
	$sql="select `coursename` from `course` where `id`=".$courseid;
	$result = mysqli_query($conn,$sql);
	$row = mysqli_fetch_assoc($result);
	$coursename = $row['coursename'];//得到课程名
	$sql="select name from banji where id=".$banjiid;
	$result = mysqli_query($conn,$sql);
	$row = mysqli_fetch_assoc($result);
	$banjiname = $row['name'];//得到班级名称
	$sql="select chapter.number as c1,chapter.chaptername as c2,zznuojques.id as t1,zznuojques.title as t2,chapojques.teacher as tea,chapojques.bz_time as t3 from chapojques join chapter on chapter.id=chapojques.chapterid join zznuojques on chapojques.quesid=zznuojques.id where banjiid=$banjiid and courseid=$courseid order by chapter.number";
	$result = mysqli_query($conn,$sql);
	// mysqli_stmt_bind_param($stmt,'ii',$banjiid,$courseid);
	// $result = mysqli_stmt_execute($stmt);
	// var_dump($result);

}
else
{
	header("location:banjiojzy.php");
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>班级的OJ任务</title>
</head>
<body>
	<h2>
		<?php echo "$banjiname";?>&nbsp;<?php echo "$coursename";?>&nbsp;OJ练习任务表
	</h2>
	<table id="banjiojtimu" border="1" >
		<tr><td>章次</td><td>章名称</td><td>题号</td><td>题目标题</td><td>教师</td><td>布置时间</td></tr>
	<?php
	while($row = mysqli_fetch_array($result))
	{
	?>
	<tr><td><?php echo $row[0];?></td>
		<td><?php echo $row[1];?></td>
		<td><?php echo $row[2];?></td>
		<td><?php echo $row[3];?></td>
		<td><?php echo $row[4];?></td>
		<td><?php echo $row[5];?></td>
	</tr>
	<?php
    }
	?>
	</table>
</body>
</html>