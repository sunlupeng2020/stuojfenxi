<?php
set_time_limit(0);
//调用Python代码，爬取学生OJ作业完成情况
header("Content-type:text/html; charset=utf-8");
$output = system('python crawler/stuojchaques.py 8 2');//传递章号，班级
//echo "正在爬取，请等待......";
//echo $output;
$array = explode(',', $output);
foreach ($array as $value) {
#echo "\n";
echo $value;
echo "<br>";
}
?>