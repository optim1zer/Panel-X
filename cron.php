<?php
// CRON backend для панели управления сайтами
define("_MAIN", true);
include("include/config.php");
include_once("include/mysql.php");
include_once("include/function.php");
error_reporting(0);
set_time_limit(0);
if (!isset($userconfig) OR !is_array($userconfig)) {
    $userconfig = $db->sql_fetchrow($db->sql_query("SELECT * FROM " . $prefix . "users WHERE uid='1'"));
    $cols = explode("\n", $userconfig['columns']);
    for ($i = 0; $i < count($cols); $i++) {
        $col_arr = explode(":", $cols[$i]);
        $mycols[$col_arr[0]] = array($col_arr[1], $col_arr[2]);
    }
    $userconfig['columns'] = $mycols;
}

$time_check = time() - intval($userconfig['time_between_checks']);
$result = $db->sql_query("SELECT id FROM " . $prefix . "sites WHERE last_check<$time_check ORDER BY id DESC LIMIT " . $userconfig['sites_per_query']);
$counter = 0;
while (list($site_id) = $db->sql_fetchrow($result)) {
    $cont = site_update($userconfig, $site_id);
    if (!is_array($cont))
        echo "$cont\n";
    $counter++;
}


if ($userconfig['send_alarms'] == "1") {
    $sites_alarm_query = "";
    $globalarm = "";
    $alarm_res = $db->sql_query("SELECT id, title, pr_more, pr_less, tcy_more, tcy_less, yai_more, yai_less, gi_more, gi_less, yi_more, yi_less, ri_more, ri_less, ybl_more, ybl_less, alexa_more, alexa_less, domain_more, domain_less FROM " . $prefix . "alarms WHERE last_sent<" . ( time() - 86400 ) . " AND uid='1'");
    while (list($alarmid, $alarm_title, $pr_more, $pr_less, $tcy_more, $tcy_less, $yai_more, $yai_less, $gi_more, $gi_less, $yi_more, $yi_less, $ri_more, $ri_less, $ybl_more, $ybl_less, $alexa_more, $alexa_less, $domain_more, $domain_less) = $db->sql_fetchrow($alarm_res)) {
        $adq = "";
        if ($domain_more != - 1)
            $domain_more = time() + 86400 * $domain_more;
        if ($domain_less != - 1)
            $domain_less = time() + 86400 * $domain_less;
        if ($pr_more != - 1)
            $adq .= " AND pr>$pr_more";
        if ($pr_less != - 1)
            $adq .= " AND pr<$pr_less";
        if ($tcy_more != - 1)
            $adq .= " AND tcy>$tcy_more";
        if ($tcy_less != - 1)
            $adq .= " AND tcy<$tcy_less";
        if ($yai_more != - 1)
            $adq .= " AND yai>$yai_more";
        if ($yai_less != - 1)
            $adq .= " AND yai<$yai_less";
        if ($gi_more != - 1)
            $adq .= " AND gi>$gi_more";
        if ($gi_less != - 1)
            $adq .= " AND gi<$gi_less";
        if ($yi_more != - 1)
            $adq .= " AND yi>$yi_more";
        if ($yi_less != - 1)
            $adq .= " AND yi<$yi_less";
        if ($ri_more != - 1)
            $adq .= " AND ri>$ri_more";
        if ($ri_less != - 1)
            $adq .= " AND ri<$ri_less";
        if ($ybl_more != - 1)
            $adq .= " AND ybl>$ybl_more";
        if ($ybl_less != - 1)
            $adq .= " AND ybl<$ybl_less";
        if ($alexa_more != - 1)
            $adq .= " AND alexarank>$alexa_more";
        if ($alexa_less != - 1)
            $adq .= " AND alexarank<$alexa_less";
        if ($domain_more != - 1)
            $adq .= " AND expiry>$domain_more AND expiry!='0-00-00'";
        if ($domain_less != - 1)
            $adq .= " AND expiry<$domain_less AND expiry!='0-00-00'";

        $thisalarm = "";
        if (strlen($adq) > 0) {
            $sres = $db->sql_query("SELECT id, url FROM sites WHERE uid='1'$adq");
            if ($db->sql_numrows($sres) > 0) {
                $thisalarm = "Будильник \"$alarm_title\" сработал на сайты:";
            }
            while (list($sid, $surl) = $db->sql_fetchrow($sres)) {
                $thisalarm .= " <b>$surl</b>,";
            }
        }
        if (strlen($thisalarm) > 0)
            $globalarm .= substr($thisalarm, 0, -1) . "<hr><br><br>";
        $db->sql_query("UPDATE " . $prefix . "alarms SET last_sent='" . time() . "' WHERE id='$alarmid'");
    }
    if (strlen($globalarm) > 0) {
        $subject = "Сработал будильник Panel-X";
        $message = "Здравствуйте. Ваша Panel-X обнаружила, что за последние сутки сработал один или несколько будильников. Вот они:<br><br>$globalarm";
        mail_send($userconfig['email'], $userconfig['email'], $subject, $message);
    }
}

echo "CRON sites update finished successfully after $counter site(s)";
?>