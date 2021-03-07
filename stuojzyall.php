<!-- stuojzyall.php -->
<!-- stuojzy.php -->
<!-- 查询学生OJ习题完成情况 -->
<?php
require_once("menu.php");
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
	<title>查询学生全部OJ作业完成情况</title>
	<script language="javascript" src="ajax.js"></script>
    <script language="javascript">
    function querychapter()
    {
        var courseid = document.getElementById("course").value;
        if(courseid!="")
        {
            createRequest('querychapter.php?courseid='+courseid);
        }
    }
    </script>
</head>
<body>
	<form name="form1" id="form1" action="stuojzyalllist.php" method="post">
		班级：<select name="banji" id="banji">
			<?php
			while($row = mysqli_fetch_assoc($result)){
				echo "<option value='".$row['id']."'>".$row['name']."</option>";
			}
			?>
		</select>&nbsp;&nbsp;&nbsp;&nbsp;
		课程：<select name="course" id="course" Onchange="querychapter()">
			<option value="0" selected="selected">--选择课程--</option>
			<option value="1">C/C++程序设计（一）</option>
			<option value="2">C/C++程序设计（二）</option>
		</select>&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="submit" value="查询" name="submit"/>
	</form>
</body>
</html>