<!-- 
	以班级为单位给学生布置各章的OJ编程题。
 -->
<?php
require "connections/conn.php"
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>布置OJ编程题目</title>
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
<?php
mysqli_query($conn,"set names 'utf8'");
$sql = "select `id`,`name` from banji";
$result = mysqli_query($conn,$sql);
?>
	<form name="form1" method="post" action="buzhi.php">
		班级：<select name="banji">
			<?php
			while($row = mysqli_fetch_assoc($result)){
				echo "<option value='".$row['id']."'>".$row['name']."</option>";
			}
			?>
		</select><br/>
		课程：<select name="course" id="course" Onchange="querychapter()">
			<option value="0" selected="selected">--选择课程--</option>
			<option value="1">C/C++程序设计（一）</option>
			<option value="2">C/C++程序设计（二）</option>
		</select><br/>
		章次：<select name="chapter" id="chapter">
			</select><br/>
		题号：<input type="text" name="timuids" title="可以输入多个题目编号，中间用英文逗号隔开"><br/>
		<input type="submit" value="提交"/>
		<input type="reset" value="重置"/>
	</form>
<?php
mysqli_close($conn)
?>
</body>
</html>
