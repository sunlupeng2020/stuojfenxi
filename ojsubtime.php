<!-- ojsubtime.php -->
<?php
require_once("connections/conn.php");
mysqli_query($conn,"set names 'utf8'");
for($i=0;$i<7;$i++)
{
	$subcount[$i]=0;
}
$sql = "select `challtime` from stuchallenged where stuno in(select stuno from student where banjiid=1)";
$result = mysqli_query($conn,$sql);
while($row = mysqli_fetch_array($result))
{
	//echo date('G',strtotime("$row[0]")),"<br/>";
	//echo $row[0]."<br/>";
	$subcount[date('w',strtotime("$row[0]"))]++;//按星期统计做题数量，将w改为H,则按小时统计做题数量
}
mysqli_close($conn);
foreach($subcount as $key=>$value)
{
	echo "$key".":".$value."<br/>";
}