<?php
if (!defined("_MAIN")) die("You have no access to this file");
define("PANEL_VERSION", "2.0.4");

define('ROOT_PATH', realpath(dirname( dirname(__FILE__) )).DIRECTORY_SEPARATOR);
define('WRITING_PATH', ROOT_PATH.'writing'.DIRECTORY_SEPARATOR);
define('TMP_PATH', ROOT_PATH.'tmp'.DIRECTORY_SEPARATOR);

$dbhost = "localhost";
$dbuname = "paneluser";
$dbpass = "qweasd";
$dbname = "panel";
$prefix = "";
?>