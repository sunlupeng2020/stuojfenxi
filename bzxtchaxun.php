<!-- bzxtchaxun.php -->
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>已布置OJ编程题目查询</title>
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
	<div>
		<div>
<?php
require_once "connections/conn.php";
mysqli_query($conn,"set names 'utf8'");
$sql = "select `id`,`name` from banji";
$result = mysqli_query($conn,$sql);
?>
<?php
    require_once("menu.php");
?>
	<h3>按课程、章布置OJ编程题目</h3>
	<form name="form1" method="post" action="">
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
		<input type="submit" value="查询"/>
	</form>
</div>
<!--显示查询结果-->
<div>
	<?php
	if(isset($_POST["banji"])){
		$banji = $_POST["banji"];
		$course = $_POST["course"];
		$chapter = $_POST["chapter"];
		$sql ="select id,title,accept,submit from zznuojques where id in(select quesid from chapojques where banjiid=$banji and chapterid=(select id from chapter where courseid=$course and number=$chapter))";
		$result = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($result)){
			echo $row['id'].":".$row['title'].','.$row['accept'].'/'.$row['submit']."<br/>";
		}
	}
	?>
<?php
mysqli_close($conn)
?>
</div>
</div>
</body>
</html>