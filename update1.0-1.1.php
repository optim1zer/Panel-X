<?php
define("_MAIN", true);
include("include/config.php");
include_once("include/mysql.php");

mysql_query("ALTER TABLE sites ADD feeduri VARCHAR( 250 ) NOT NULL AFTER alexarank, ADD feedcount INT( 16 ) NOT NULL AFTER feeduri");
mysql_query("ALTER TABLE history ADD feedcount INT NOT NULL");
mysql_query("ALTER TABLE cms ADD position INT NOT NULL");
mysql_query("ALTER TABLE hosts ADD position INT NOT NULL");
mysql_query("ALTER TABLE dirs ADD position INT NOT NULL");

$result = mysql_query("SELECT id, registration, expiry FROM sites");
while(list($id, $registration, $expiry) = mysql_fetch_array($result)) {
	if($registration != "0-00-00") {
		$reg_arr = explode("-", $registration);
		$registration = mktime(0,0,0,intval($reg_arr[1]),intval($reg_arr[2]),intval($reg_arr[0]));
	}
	if($expiry != "0-00-00") {
		$exp_arr = explode("-", $expiry);
		$expiry = mktime(0,0,0,intval($exp_arr[1]),intval($exp_arr[2]),intval($exp_arr[0]));
	}
	mysql_query("UPDATE sites SET registration='$registration', expiry='$expiry' WHERE id='$id'");
}

mysql_query("ALTER TABLE alarms ADD domain_less INT( 5 ) NOT NULL DEFAULT '-1', ADD domain_more INT( 5 ) NOT NULL DEFAULT '-1'");

?>