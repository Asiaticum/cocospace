<?php
// MySQLへの接続
$host = 'localhost';
$username = 'co-19-319.99sv-c';
$pw = 'b4W3AeM5';
$dbname = 'co_19_319_99sv_coco_com';
$link = mysqli_connect($host, $username, $pw, $dbname);
$dropTable = "drop table bbs";
mysqli_query($link, $dropTable);
?>