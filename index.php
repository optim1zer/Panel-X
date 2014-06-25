<?php
define("_MAIN", true);
include("include/config.php");
include_once("include/mysql.php");
include_once("include/check_logged.php");

	$userconfig = $db->sql_fetchrow($db->sql_query("SELECT * FROM " . $prefix . "users WHERE login='" . $_COOKIE['ad_login'] . "'"));
	$cols = explode("\n",$userconfig['columns']);
for($i=0;$i<count($cols);$i++) {
	$col_arr = explode(":",$cols[$i]);
	$mycols[$col_arr[0]] = array($col_arr[1],$col_arr[2]);
}
	$userconfig['columns'] = $mycols;

        list($cur_panels) = $db->sql_fetchrow($db->sql_query("SELECT columns FROM ".$prefix."users WHERE uid='".$userconfig['uid']."' LIMIT 1"));
        $cur_panels_arr = explode("\n", $cur_panels);
        $toplinks = "";
        for($i=0;$i<count($cur_panels_arr);$i++) {
            $cur_panel = explode(":",$cur_panels_arr[$i]);
            if(strlen($cur_panel[0])>0) $toplinks .= "<a class='noac' id=\"button_".$cur_panel[0]."\" href='javascript: void(0);' onclick=\"main_load('".$cur_panel[0]."');\" title='".$cur_panel[1]."'><b>".$cur_panel[1]."</b></a>";
        }
        $toplinks .= "<a class='noac' href='ajax.php?action=add_panel' title='Добавить вкладку' rel=\"fancybox\"><b style=\"padding-left:7px;padding-right:7px;\"><img src=\"images/plus.png\" alt=\"Добавить вкладку\" title=\"Добавить вкладку\" border=\"0\" style=\"margin-bottom:-5px;margin-top:7px;\"></b></a>";
$thefile = "\$r_file=\"".addslashes(file_get_contents("templates/index.html"))."\";";
eval($thefile);
echo stripslashes($r_file);


?>
