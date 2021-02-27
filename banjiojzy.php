<!-- banjiojzy.php -->
<!-- 班级OJ作业————显示一个班级布置的OJ平台练习题任务 -->
<?php
require_once("connections/conn.php");
mysqli_query($conn,"set names 'utf8'");
$sql="select id,name from banji";
$result = mysqli_query($conn,$sql);//查询有哪些班级
?>
<!-- 显示班级 -->
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>查询为班级布置的OJ题目</title>
</head>
<body>
	<form name="form1" id="form1" action="banjiojzychp.php" method="post">
		班级：<select name="banji" id="banji">
			<?php
			while($row = mysqli_fetch_assoc($result)){
				echo "<option value='".$row['id']."'>".$row['name']."</option>";
			}
			?>
		</select>&nbsp;&nbsp;&nbsp;&nbsp;
		课程：<select name="course" id="course">
			<option value="1">C/C++程序设计（一）</option>
			<option value="2">C/C++程序设计（二）</option>
		</select>&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="submit" value="查询" name="submit"/>
	</form>

</body>
</html>