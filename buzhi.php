<!-- buzhi.php -->
<!-- 给学生布置OJ平台上的C语言编程题目 -->
<?php
require "connections/conn.php";
mysqli_query($conn,"set names 'utf8'");
if(isset($_POST['banji']))
{
	$result=false;
	$teacher = '100236';//教师用户名
	$banjiid = $_POST['banji'];
	$timuids = $_POST['timuids'];
	$chapterid = $_POST['chapter'];
	$timuidshuzu = explode(",", $timuids);
	$sql = "insert into chapojques(banjiid,chapterid,quesid,teacher) values(?,?,?,?)";
	$stmt = mysqli_prepare($conn,$sql);
	foreach ($timuidshuzu as $timuid)
	{
		//echo "$timuid";
		//将布置的题目信息写入数据库
		mysqli_stmt_bind_param($stmt,'iiis',$banjiid,$chapterid,$timuid,$teacher);
		$result = mysqli_stmt_execute($stmt);
	}
	if($result)
	{
		echo "<script>alert('布置成功！');</script>";
	}
	else
	{
		echo "<script>alert('布置失败！');</script>";
	}	
}
// else
// {
// 	header("location:buzhixiti.php");
// }
mysqli_close($conn);
?>
<meta http-equiv="refresh" content="1;url=buzhixiti.php">
