<?php
header("Content-type:text/html;charset=utf-8");
echo "陈佳";
require 'Db.php';
$a = Grace_Database_Db::getInstance();
$link1 = $a->getConnLink(Grace_Database_Db::PATTERN_DEFAULT);
$link2 = $a->getConnLink(Grace_Database_Db::PATTERN_DEFAULT);
$link3 = $a->getConnLink(Grace_Database_Db::PATTERN_MASTER);
$link4 = $a->getConnLink(Grace_Database_Db::PATTERN_MASTER);
$link5 = $a->getConnLink(Grace_Database_Db::PATTERN_SLAVE);
$link6 = $a->getConnLink(Grace_Database_Db::PATTERN_SLAVE);
print_r($a->_conn_link);
$sql = 'SELECT * FROM sms_company';
$result = mysql_query($sql, $link1) or die("Invalid query: " . mysql_error());
$data = array();
while($row = mysql_fetch_array($result)) {
	$data[] = $row;
}
print_r($data);
print_r($a);
sleep(10);
?>