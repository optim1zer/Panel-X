<?php

define("_MAIN", true);
include("include/config.php");
include_once("include/mysql.php");
include_once("include/function.php");
include_once("include/check_logged.php");
require_once("include/JsHttpRequest.php");
if (!extension_loaded('zlib')) ob_start("ob_gzhandler");
set_time_limit(0);
$JsHttpRequest = new JsHttpRequest("utf-8");
$action = $_REQUEST['action'];
$userconfig = $db->sql_fetchrow($db->sql_query("SELECT * FROM " . $prefix . "users WHERE login='" . $_COOKIE['ad_login'] . "'"));
$cols = explode("\n", $userconfig['columns']);
for ($i = 0; $i < count($cols); $i++) {
    $col_arr = explode(":", $cols[$i]);
    $mycols[$col_arr[0]] = array($col_arr[1], $col_arr[2]);
}
$userconfig['columns'] = $mycols;

switch ($action) {
// Загрузка страницы папки/будильники/движки/хостинги/регистраторы
    case "page_load":
        //<editor-fold defaultstate="collapsed" desc="">
        list($uid, $rownum) = $db->sql_fetchrow($db->sql_query("SELECT uid, rows FROM " . $prefix . "users WHERE login='" . $_COOKIE['ad_login'] . "'"));
        $type = $_REQUEST['type'];
        if ($type != "alarms") {
            $buttons = "<div id=\"left\">"
                    . "\n\t<a class=\"add\" href='ajax.php?action=add_form&type=" . $type . "' rel=\"fancybox\"><span>Добавить</span></a><ul id=\"nav2\">"
                    . "\n\t<li class=\"first\"><a>&nbsp;</a></li>"
                    . "\n\t<li><a href=\"javascript: void(0);\" onclick=\"page('dirs');\"><img src=\"images/spacer.gif\" class=\"dir\" border=\"0\" alt=\"Папки\">Папки</a></li>\n\t<li><a href=\"javascript: void(0);\" onclick=\"page('hosts');\"><img src=\"images/spacer.gif\" class=\"ser\" border=\"0\" alt=\"Хостинги\">Хостинги</a></li>\n\t<li><a href=\"javascript: void(0);\" onclick=\"page('regs');\"><img src=\"images/spacer.gif\" class=\"regs\" border=\"0\" alt=\"Регистраторы\">Регистраторы</a></li>\n\t<li><a href=\"javascript: void(0);\" onclick=\"page('cms');\"><img src=\"images/spacer.gif\" class=\"cms\" border=\"0\" alt=\"Движки\">Движки</a></li>\n\t<li><a href=\"javascript: void(0);\" onclick=\"page('alarms');\"><img src=\"images/spacer.gif\" class=\"ala\" border=\"0\" alt=\"Будильники\">Будильники</a></li>\n\t\t</ul></div>";
            $result = $db->sql_query("SELECT id, title, descript, position FROM " . $prefix . $type . " WHERE uid='$uid' ORDER BY position ASC");
            $table = "<table class=\"tablesorter\" id=\"tablesorter\" cellpadding=\"0\" cellspacing=\"0\" style=\"width:50% !important;\"><thead><tr><th>№</th><th>Название</th>";
            if ($type == "hosts" OR $type == "regs") {
                $table .= "<th>Ссылка на биллинг</th>";
                $table .= "<th>Ссылка на контрольную панель</th>";
            }
            $table .= "<th>Порядок вывода</th><th class=\"{sorter: false}\">Опции</th></tr></thead><tbody id=\"panel_contain\">";
            while (list($id, $title, $descr, $position) = $db->sql_fetchrow($result)) {
                $table .= "<tr id=\"element_$id\"><td style=\"text-align:left !important;\">$id</td><td style=\"text-align:left !important;\">$title</td>";
                if ($type == "hosts" OR $type == "regs") {
                    $data = explode("||", $descr);
                    $bilhref = (strlen($data[0]) > 0) ? "<a href=\"" . $data[0] . "\" target=\"_blank\" title=\"Откроется в новом окне\">" . $data[0] . "</a>" : "";
                    $cphref = (strlen($data[1]) > 0) ? "<a href=\"" . $data[1] . "\" target=\"_blank\" title=\"Откроется в новом окне\">" . $data[1] . "</a>" : "";
                    $table .= "<td>$bilhref</td><td>$cphref</td>";
                }
                list($minpos, $maxpos) = $db->sql_fetchrow($db->sql_query("SELECT MIN(position), MAX(position) FROM " . $prefix . $type . ""));
                $poslinks = "<div class=\"poslinks\">";
                $poslinks .= ( $position > $minpos) ? "<img src=\"images/spacer.gif\" class=\"arrup\" alt=\"\" title=\"На одну позицию выше\" onclick=\"el_position('up','$type','" . $id . "');\"> " : "";
                $poslinks .= ( $position < $maxpos) ? "<a href=\"javascript:;\" onclick=\"el_position('down','$type','" . $id . "');\"><img src=\"images/spacer.gif\" class=\"arrdn\" alt=\"\" title=\"На одну позицию ниже\"></a> " : "";
                $poslinks .= "</div>";
                $table .= "<td>$poslinks</td><td nowrap=\"nowrap\"><a href=\"ajax.php?action=edit_form&id=$id&type=$type\" rel=\"fancybox\"><img src=\"images/spacer.gif\" alt=\"Изменить\" title=\"Изменить\" class=\"edit\"></a> <img src=\"images/spacer.gif\" alt=\"Удалить\" title=\"Удалить\" class=\"delete\" onclick=\"el_delete('$id', '$type')\"></td></tr>";
            }
            $table .= "</tbody></table>";
        } else {
            $buttons = "<div id=\"left\">"
                    . "\n\t<a class=\"add\" href='ajax.php?action=add_alarm' rel=\"fancybox\"><span>Добавить</span></a><ul id=\"nav2\">"
                    . "\n\t<li class=\"first\"><a>&nbsp;</a></li>"
                    . "\n\t<li><a href=\"javascript: void(0);\" onclick=\"page('dirs');\"><img src=\"images/spacer.gif\" class=\"dir\" border=\"0\" alt=\"Папки\">Папки</a></li>\n\t<li><a href=\"javascript: void(0);\" onclick=\"page('hosts');\"><img src=\"images/spacer.gif\" class=\"ser\" border=\"0\" alt=\"Хостинги\">Хостинги</a></li>\n\t<li><a href=\"javascript: void(0);\" onclick=\"page('regs');\"><img src=\"images/spacer.gif\" class=\"regs\" border=\"0\" alt=\"Регистраторы\">Регистраторы</a></li>\n\t<li><a href=\"javascript: void(0);\" onclick=\"page('cms');\"><img src=\"images/spacer.gif\" class=\"cms\" border=\"0\" alt=\"Движки\">Движки</a></li>\n\t<li><a href=\"javascript: void(0);\" onclick=\"page('alarms');\"><img src=\"images/spacer.gif\" class=\"ala\" border=\"0\" alt=\"Будильники\">Будильники</a></li>\n\t\t</ul></div>";
            $result = $db->sql_query("SELECT id, title, pr_more, pr_less, tcy_more, tcy_less, yai_more, yai_less, gi_more, gi_less, yi_more, yi_less, ri_more, ri_less, ybl_more, ybl_less, alexa_more, alexa_less, domain_more, domain_less FROM " . $prefix . "alarms WHERE uid='$uid' ORDER BY title ASC");
            $table = "<table class=\"tablesorter\" id=\"tablesorter\" cellpadding=\"8\" cellspacing=\"0\"><thead><tr><th class=\"{sorter:false}\"></th><th>Название</th><th>PageRank</th><th>тИЦ</th><th>В индексе Яндекса</th><th>В индексе Google</th><th>В индексе Yahoo</th><th>В индексе Rambler</th><th>Yahoo беклинков</th><th>Alexa Rank</th><th>Дней до истечения домена</th><th class=\"{sorter: false}\">Опции</th></tr></thead><tbody id=\"panel_contain\">";
            while (list($id, $title, $pr_more, $pr_less, $tcy_more, $tcy_less, $yai_more, $yai_less, $gi_more, $gi_less, $yi_more, $yi_less, $ri_more, $ri_less, $ybl_more, $ybl_less, $alexa_more, $alexa_less, $domain_more, $domain_less) = $db->sql_fetchrow($result)) {
                $pr = "";
                $tcy = "";
                $yai = "";
                $gi = "";
                $yi = "";
                $ri = "";
                $ybl = "";
                $alexa = "";
                $domain = "";
                if ($pr_less != "-1")
                    $pr .= "менее " . $pr_less . " ";
                if ($pr_more != "-1")
                    $pr .= "более " . $pr_more . " ";
                if ($tcy_less != "-1")
                    $tcy .= "менее " . $tcy_less . " ";
                if ($tcy_more != "-1")
                    $tcy .= "более " . $tcy_more . " ";
                if ($yai_less != "-1")
                    $yai .= "менее " . $yai_less . " ";
                if ($yai_more != "-1")
                    $yai .= "более " . $yai_more . " ";
                if ($gi_less != "-1")
                    $gi .= "менее " . $gi_less . " ";
                if ($gi_more != "-1")
                    $gi .= "более " . $gi_more . " ";
                if ($yi_less != "-1")
                    $yi .= "менее " . $yi_less . " ";
                if ($yi_more != "-1")
                    $yi .= "более " . $yi_more . " ";
                if ($ri_less != "-1")
                    $ri .= "менее " . $ri_less . " ";
                if ($ri_more != "-1")
                    $ri .= "более " . $ri_more . " ";
                if ($ybl_less != "-1")
                    $ybl .= "менее " . $ybl_less . " ";

                if ($ybl_more != "-1")
                    $ybl .= "более " . $ybl_more . " ";
                if ($alexa_less != "-1")
                    $alexa .= "менее " . $alexa_less . " ";
                if ($alexa_more != "-1")
                    $alexa .= "более " . $alexa_more . " ";
                if ($domain_less != "-1")
                    $domain .= "менее " . $domain_less . " ";
                if ($domain_more != "-1")
                    $domain .= "более " . $domain_more . " ";
                $table .= "<tr id=\"element_$id\"><td><img src=\"images/spacer.gif\" width=\"16\" height=\"16\" alt=\"Будильник\" class=\"ala\" /></td><td style=\"text-align:left !important;\">$title</td><td>$pr</td><td>$tcy</td><td>$yai</td><td>$gi</td><td>$yi</td><td>$ri</td><td>$ybl</td><td>$alexa</td><td>$domain</td><td nowrap=\"nowrap\"><img src=\"images/spacer.gif\" alt=\"Изменить\" title=\"Изменить\" class=\"edit\" onclick=\"$.facebox({ajax:'ajax.php?action=edit_alarm_form&id=$id', height:400});\"> <img src=\"images/spacer.gif\" alt=\"Удалить\" title=\"Удалить\" class=\"delete\" onclick=\"alarm_delete('$id')\"></td></tr>";
            }
        }

        $GLOBALS['_RESULT'] = array("table" => $table, "buttons" => $buttons);
        break;
    //</editor-fold>
// Форма добавления папки/движка/хостинга/регистратора
    case "add_form":
        //<editor-fold defaultstate="collapsed" desc="">
        $type = $_REQUEST['type'];
        echo "<table cellpadding=\"3\" cellspacing=\"5\" border=\"0\"><tr><td colspan=\"2\"><h2>Введите название</h2></td><tr>"
        . "<tr><td><b>Название</b>: </td><td><input type=\"text\" id=\"new_title\" style=\"width:250px;\" /></td></tr>";
        if ($type == "hosts" OR $type == "regs") {
            echo "<tr><td><b>Ссылка на биллинг</b>: </td><td><input type=\"text\" id=\"new_billing\" style=\"width:250px;\" value=\"http://\" /></td></tr>"
            . "<tr><td><b>Ссылка на панель управления</b>: </td><td><input type=\"text\" id=\"new_cp\" style=\"width:250px;\" value=\"http://\" /></td></tr>";
        }
        echo "<tr><td colspan=\"2\"><a class=\"but\" href=\"javascript:void(0);\" onclick=\"add('$type');\" style=\"float: left;\"><span>Добавить</span></a></td></tr></table>";
        ;
        break;
    case "add_alarm":
        echo "<table cellpadding=\"3\" cellspacing=\"10\" border=\"0\"><tr><td colspan=\"2\"><h2>Укажите настройки будильника</h2><div class=\"message-box info\">Пустые поля не учитываются в работе будильника</div></td><tr>"
        . "<tr><td><b>Название</b>: </td><td><input type=\"text\" id=\"new_title\" style=\"width:250px;\" /></td></tr>"
        . "<tr><td><b>PageRank</b>: </td><td>менее <input type=\"text\" id=\"pr_less\" size=\"2\" /> более <input type=\"text\" id=\"pr_more\" size=\"2\" /></td></tr>"
        . "<tr><td><b>тИЦ</b>: </td><td>менее <input type=\"text\" id=\"tcy_less\" size=\"2\" /> более <input type=\"text\" id=\"tcy_more\" size=\"2\" /></td></tr>"
        . "<tr><td><b>В индексе Яндекса</b>: </td><td>менее <input type=\"text\" id=\"yai_less\" size=\"2\" /> более <input type=\"text\" id=\"yai_more\" size=\"2\" /></td></tr>"
        . "<tr><td><b>В индексе Google</b>: </td><td>менее <input type=\"text\" id=\"gi_less\" size=\"2\" /> более <input type=\"text\" id=\"gi_more\" size=\"2\" /></td></tr>"
        . "<tr><td><b>В индексе Yahoo</b>: </td><td>менее <input type=\"text\" id=\"yi_less\" size=\"2\" /> более <input type=\"text\" id=\"yi_more\" size=\"2\" /></td></tr>"
        . "<tr><td><b>В индексе Rambler</b>: </td><td>менее <input type=\"text\" id=\"ri_less\" size=\"2\" /> более <input type=\"text\" id=\"ri_more\" size=\"2\" /></td></tr>"
        . "<tr><td><b>Yahoo беклинков</b>: </td><td>менее <input type=\"text\" id=\"ybl_less\" size=\"2\" /> более <input type=\"text\" id=\"ybl_more\" size=\"2\" /></td></tr>"
        . "<tr><td><b>Alexa Traffic Rank</b>: </td><td>менее <input type=\"text\" id=\"alexa_less\" size=\"2\" /> более <input type=\"text\" id=\"alexa_more\" size=\"2\" /></td></tr>"
        . "<tr><td><b>До истечения домена (осталось дней)</b>: </td><td>менее <input type=\"text\" id=\"domain_less\" size=\"2\" /> более <input type=\"text\" id=\"domain_more\" size=\"2\" /></td></tr>";
        echo "<tr><td colspan=\"2\"><a class=\"but\" href=\"javascript:void(0);\" onclick=\"add_alarm();\" style=\"float: left;\"><span>Добавить</span></a></td></tr></table>";
        ;
        break;
    //</editor-fold>
// Форма редактирования будильника
    case "edit_alarm_form":
        //<editor-fold defaultstate="collapsed" desc="">
        $id = $_REQUEST['id'];
        list($uid, $rownum) = $db->sql_fetchrow($db->sql_query("SELECT uid, rows FROM " . $prefix . "users WHERE login='" . $_COOKIE['ad_login'] . "'"));
        $result = $db->sql_query("SELECT id, title, REPLACE(pr_more, '-1', ''), REPLACE(pr_less, '-1', ''), REPLACE(tcy_more, '-1', ''), REPLACE(tcy_less, '-1', ''), REPLACE(yai_more, '-1', ''), REPLACE(yai_less, '-1', ''), REPLACE(gi_more, '-1', ''), REPLACE(gi_less, '-1', ''), REPLACE(yi_more, '-1', ''), REPLACE(yi_less, '-1', ''), REPLACE(ri_more, '-1', ''), REPLACE(ri_less, '-1', ''), REPLACE(ybl_more, '-1', ''), REPLACE(ybl_less, '-1', ''), REPLACE(alexa_more, '-1', ''), REPLACE(alexa_less, '-1', ''), REPLACE(domain_more, '-1', ''), REPLACE(domain_less, '-1', '') FROM " . $prefix . "alarms WHERE id='$id' AND uid='$uid' LIMIT 1");
        list($id, $title, $pr_more, $pr_less, $tcy_more, $tcy_less, $yai_more, $yai_less, $gi_more, $gi_less, $yi_more, $yi_less, $ri_more, $ri_less, $ybl_more, $ybl_less, $alexa_more, $alexa_less, $domain_more, $domain_less) = $db->sql_fetchrow($result);
        echo "<table cellpadding=\"3\" cellspacing=\"10\" border=\"0\"><tr><td colspan=\"2\"><h2>Укажите настройки будильника</h2><div class=\"message-box info\">Пустые поля не учитываются в работе будильника</div></td><tr>"
        . "<tr><td><b>Название</b>: </td><td><input type=\"text\" value=\"$title\" id=\"new_title\" style=\"width:250px;\" /></td></tr>"
        . "<tr><td><b>PageRank</b>: </td><td>менее <input type=\"text\" id=\"pr_less\" value=\"$pr_less\" size=\"2\" /> более <input type=\"text\" id=\"pr_more\" value=\"$pr_more\" size=\"2\" /></td></tr>"
        . "<tr><td><b>тИЦ</b>: </td><td>менее <input type=\"text\" id=\"tcy_less\" value=\"$tcy_less\" size=\"2\" /> более <input type=\"text\" id=\"tcy_more\" value=\"$tcy_more\" size=\"2\" /></td></tr>"
        . "<tr><td><b>В индексе Яндекса</b>: </td><td>менее <input type=\"text\" id=\"yai_less\" value=\"$yai_less\" size=\"2\" /> более <input type=\"text\" id=\"yai_more\" value=\"$yai_more\" size=\"2\" /></td></tr>"
        . "<tr><td><b>В индексе Google</b>: </td><td>менее <input type=\"text\" id=\"gi_less\" value=\"$gi_less\" size=\"2\" /> более <input type=\"text\" id=\"gi_more\" value=\"$gi_more\" size=\"2\" /></td></tr>"
        . "<tr><td><b>В индексе Yahoo</b>: </td><td>менее <input type=\"text\" id=\"yi_less\" value=\"$yi_less\" size=\"2\" /> более <input type=\"text\" id=\"yi_more\" value=\"$yi_more\" size=\"2\" /></td></tr>"
        . "<tr><td><b>В индексе Rambler</b>: </td><td>менее <input type=\"text\" id=\"ri_less\" value=\"$ri_less\" size=\"2\" /> более <input type=\"text\" id=\"ri_more\" value=\"$ri_more\" size=\"2\" /></td></tr>"
        . "<tr><td><b>Yahoo беклинков</b>: </td><td>менее <input type=\"text\" id=\"ybl_less\" value=\"$ybl_less\" size=\"2\" /> более <input type=\"text\" id=\"ybl_more\" value=\"$ybl_more\" size=\"2\" /></td></tr>"
        . "<tr><td><b>Alexa Traffic Rank</b>: </td><td>менее <input type=\"text\" id=\"alexa_less\" value=\"$alexa_less\" size=\"2\" /> более <input type=\"text\" id=\"alexa_more\" value=\"$alexa_more\" size=\"2\" /></td></tr>"
        . "<tr><td><b>До истечения домена (осталось дней)</b>: </td><td>менее <input type=\"text\" id=\"domain_less\" value=\"$domain_less\" size=\"2\" /> более <input type=\"text\" id=\"domain_more\" value=\"$domain_more\" size=\"2\" /></td></tr>";
        echo "<tr><td colspan=\"2\"><a class=\"but\" href=\"javascript:void(0);\" onclick=\"save_alarm('$id');\" style=\"float: left;\"><span>Сохранить</span></a></td></tr></table>";
        ;
        break;
    //</editor-fold>
// Сохранение отредактированного будильника
    case "edit_alarm_do":
        //<editor-fold defaultstate="collapsed" desc="">
        $id = $_REQUEST['id'];
        list($uid, $rownum) = $db->sql_fetchrow($db->sql_query("SELECT uid, rows FROM " . $prefix . "users WHERE login='" . $_COOKIE['ad_login'] . "'"));
        $values = explode("||", $_REQUEST['values']);
        $values = str_replace("xx", "-1", $values);
        $title = $_REQUEST['title'];
        // die("UPDATE ".$prefix."alarms SET title='$title', pr_more='".$values[0]."', pr_less='".$values[1]."', tcy_more='".$values[2]."', tcy_less='".$values[3]."', yai_more='".$values[4]."', yai_less='".$values[5]."', gi_more='".$values[6]."', gi_less='".$values[7]."', yi_more='".$values[8]."', yi_less='".$values[9]."', ri_more='".$values[10]."', ri_less='".$values[11]."', ybl_more='".$values[12]."', ybl_less='".$values[13]."', alexa_more='".$values[14]."', alexa_less='".$values[15]."' WHERE uid='$uid' AND id='$id' LIMIT 1");
        $db->sql_query("UPDATE " . $prefix . "alarms SET title='$title', pr_more='" . $values[0] . "', pr_less='" . $values[1] . "', tcy_more='" . $values[2] . "', tcy_less='" . $values[3] . "', yai_more='" . $values[4] . "', yai_less='" . $values[5] . "', gi_more='" . $values[6] . "', gi_less='" . $values[7] . "', yi_more='" . $values[8] . "', yi_less='" . $values[9] . "', ri_more='" . $values[10] . "', ri_less='" . $values[11] . "', ybl_more='" . $values[12] . "', ybl_less='" . $values[13] . "', alexa_more='" . $values[14] . "', alexa_less='" . $values[15] . "', domain_more='" . $values[16] . "', domain_less='" . $values[17] . "' WHERE uid='$uid' AND id='$id' LIMIT 1");
        $result = $db->sql_query("SELECT id, title, pr_more, pr_less, tcy_more, tcy_less, yai_more, yai_less, gi_more, gi_less, yi_more, yi_less, ri_more, ri_less, ybl_more, ybl_less, alexa_more, alexa_less, domain_more, domain_less FROM " . $prefix . "alarms WHERE id='$id' LIMIT 1");
        while (list($id, $title, $pr_more, $pr_less, $tcy_more, $tcy_less, $yai_more, $yai_less, $gi_more, $gi_less, $yi_more, $yi_less, $ri_more, $ri_less, $ybl_more, $ybl_less, $alexa_more, $alexa_less, $domain_more, $domain_less) = $db->sql_fetchrow($result)) {
            $pr = "";
            $tcy = "";
            $yai = "";
            $gi = "";
            $yi = "";
            $ri = "";
            $ybl = "";
            $alexa = "";
            $domain = "";
            if ($pr_less != "-1")
                $pr .= "менее " . $pr_less . " ";
            if ($pr_more != "-1")
                $pr .= "более " . $pr_more . " ";
            if ($tcy_less != "-1")
                $tcy .= "менее " . $tcy_less . " ";
            if ($tcy_more != "-1")
                $tcy .= "более " . $tcy_more . " ";
            if ($yai_less != "-1")
                $yai .= "менее " . $yai_less . " ";
            if ($yai_more != "-1")
                $yai .= "более " . $yai_more . " ";
            if ($gi_less != "-1")
                $gi .= "менее " . $gi_less . " ";
            if ($gi_more != "-1")
                $gi .= "более " . $gi_more . " ";
            if ($yi_less != "-1")
                $yi .= "менее " . $yi_less . " ";
            if ($yi_more != "-1")
                $yi .= "более " . $yi_more . " ";
            if ($ri_less != "-1")
                $ri .= "менее " . $ri_less . " ";
            if ($ri_more != "-1")
                $ri .= "более " . $ri_more . " ";
            if ($ybl_less != "-1")
                $ybl .= "менее " . $ybl_less . " ";
            if ($ybl_more != "-1")
                $ybl .= "более " . $ybl_more . " ";
            if ($alexa_less != "-1")
                $alexa .= "менее " . $alexa_less . " ";
            if ($alexa_more != "-1")
                $alexa .= "более " . $alexa_more . " ";
            if ($domain_less != "-1")
                $domain .= "менее " . $domain_less . " ";
            if ($domain_more != "-1")
                $domain .= "более " . $domain_more . " ";
            echo "<tr id=\"element_$id\"><td><img src=\"images/spacer.gif\" width=\"16\" height=\"16\" alt=\"Будильник\" class=\"ala\" /></td><td style=\"text-align:left !important;\">$title</td><td>$pr</td><td>$tcy</td><td>$yai</td><td>$gi</td><td>$yi</td><td>$ri</td><td>$ybl</td><td>$alexa</td><td>$domain</td><td nowrap=\"nowrap\"><img src=\"images/spacer.gif\" alt=\"Изменить\" title=\"Изменить\" class=\"edit\" onclick=\"$.facebox({ajax:'ajax.php?action=edit_alarm_form&id=$id', height:400});\"> <img src=\"images/spacer.gif\" alt=\"Удалить\" title=\"Удалить\" class=\"delete\" onclick=\"alarm_delete('$id')\"></td></tr>";
        }
        break;
    //</editor-fold>
// Добавление будильника
    case "add_alarm_do":
        //<editor-fold defaultstate="collapsed" desc="">
        list($uid, $rownum) = $db->sql_fetchrow($db->sql_query("SELECT uid, rows FROM " . $prefix . "users WHERE login='" . $_COOKIE['ad_login'] . "'"));
        $values = explode("||", $_REQUEST['values']);
        $values = str_replace("xx", "-1", $values);
        $title = $_REQUEST['title'];
        $db->sql_query("INSERT INTO " . $prefix . "alarms (uid, title, pr_more, pr_less, tcy_more, tcy_less, yai_more, yai_less, gi_more, gi_less, yi_more, yi_less, ri_more, ri_less, ybl_more, ybl_less, alexa_more, alexa_less, domain_more, domain_less) VALUES ('$uid', '$title', '" . $values[0] . "', '" . $values[1] . "', '" . $values[2] . "', '" . $values[3] . "', '" . $values[4] . "', '" . $values[5] . "', '" . $values[6] . "', '" . $values[7] . "', '" . $values[8] . "', '" . $values[9] . "', '" . $values[10] . "', '" . $values[11] . "', '" . $values[12] . "', '" . $values[13] . "', '" . $values[14] . "', '" . $values[15] . "', '" . $values[16] . "', '" . $values[17] . "')");
        $id = $db->sql_nextid();
        $result = $db->sql_query("SELECT id, title, pr_more, pr_less, tcy_more, tcy_less, yai_more, yai_less, gi_more, gi_less, yi_more, yi_less, ri_more, ri_less, ybl_more, ybl_less, alexa_more, alexa_less, domain_more, domain_less FROM " . $prefix . "alarms WHERE id='$id' LIMIT 1");
        while (list($id, $title, $pr_more, $pr_less, $tcy_more, $tcy_less, $yai_more, $yai_less, $gi_more, $gi_less, $yi_more, $yi_less, $ri_more, $ri_less, $ybl_more, $ybl_less, $alexa_more, $alexa_less, $domain_more, $domain_less) = $db->sql_fetchrow($result)) {
            $pr = "";
            $tcy = "";
            $yai = "";
            $gi = "";
            $yi = "";
            $ri = "";
            $ybl = "";
            $alexa = "";
            $domain = "";
            if ($pr_less != "-1")
                $pr .= "менее " . $pr_less . " ";
            if ($pr_more != "-1")
                $pr .= "более " . $pr_more . " ";
            if ($tcy_less != "-1")
                $tcy .= "менее " . $tcy_less . " ";
            if ($tcy_more != "-1")
                $tcy .= "более " . $tcy_more . " ";
            if ($yai_less != "-1")
                $yai .= "менее " . $yai_less . " ";
            if ($yai_more != "-1")
                $yai .= "более " . $yai_more . " ";
            if ($gi_less != "-1")
                $gi .= "менее " . $gi_less . " ";
            if ($gi_more != "-1")
                $gi .= "более " . $gi_more . " ";
            if ($yi_less != "-1")
                $yi .= "менее " . $yi_less . " ";
            if ($yi_more != "-1")
                $yi .= "более " . $yi_more . " ";
            if ($ri_less != "-1")
                $ri .= "менее " . $ri_less . " ";
            if ($ri_more != "-1")
                $ri .= "более " . $ri_more . " ";
            if ($ybl_less != "-1")
                $ybl .= "менее " . $ybl_less . " ";
            if ($ybl_more != "-1")
                $ybl .= "более " . $ybl_more . " ";
            if ($alexa_less != "-1")
                $alexa .= "менее " . $alexa_less . " ";
            if ($alexa_more != "-1")
                $alexa .= "более " . $alexa_more . " ";
            if ($domain_less != "-1")
                $domain .= "менее " . $domain_less . " ";
            if ($domain_more != "-1")
                $domain .= "более " . $domain_more . " ";
            echo "<tr id=\"element_$id\"><td><img src=\"images/spacer.gif\" width=\"16\" height=\"16\" alt=\"Будильник\" class=\"ala\" /></td><td style=\"text-align:left !important;\">$title</td><td>$pr</td><td>$tcy</td><td>$yai</td><td>$gi</td><td>$yi</td><td>$ri</td><td>$ybl</td><td>$alexa</td><td>$domain</td><td nowrap=\"nowrap\"><img src=\"images/spacer.gif\" alt=\"Изменить\" title=\"Изменить\" class=\"edit\" onclick=\"$.facebox({ajax:'ajax.php?action=edit_alarm_form&id=$id', height:400});\"> <img src=\"images/spacer.gif\" alt=\"Удалить\" title=\"Удалить\" class=\"delete\" onclick=\"alarm_delete('$id')\"></td></tr>";
        }
        break;
    //</editor-fold>
// Удаление будильника
    case "delete_alarm":
        //<editor-fold defaultstate="collapsed" desc="">
        $id = $_REQUEST['id'];
        list($uid, $rownum) = $db->sql_fetchrow($db->sql_query("SELECT uid, rows FROM " . $prefix . "users WHERE login='" . $_COOKIE['ad_login'] . "'"));
        $check = $db->sql_query("SELECT id FROM " . $prefix . "alarms WHERE id='$id' AND uid='$uid'");
        $row = "";
        if ($db->sql_numrows($check) > 0) {
            $db->sql_query("DELETE FROM " . $prefix . "alarms WHERE id='$id'");
            echo "Удаление прошло успешно";
        } else {
            echo "0";
            $row = "";
            $answer = "0";
        }
        break;
    //</editor-fold>
// Форма редактирования папки/движка/хостинга/регистратора
    case "edit_form":
        //<editor-fold defaultstate="collapsed" desc="">
        $type = $_REQUEST['type'];
        $id = $_REQUEST['id'];
        list($uid, $rownum) = $db->sql_fetchrow($db->sql_query("SELECT uid, rows FROM " . $prefix . "users WHERE login='" . $_COOKIE['ad_login'] . "'"));
        list($title, $position, $descr) = $db->sql_fetchrow($db->sql_query("SELECT title, position, descript FROM " . $prefix . $type . " WHERE id='$id'"));
        echo "<table cellpadding=\"3\" cellspacing=\"5\" border=\"0\"><tr><td colspan=\"2\"><h2>Введите название</h2></td><tr>"
        . "<tr><td><b>Название</b>: </td><td><input type=\"text\" id=\"new_title\" value=\"$title\" style=\"width:250px;\" /></td></tr>";
        if ($type == "hosts" OR $type == "regs") {
            $data = explode("||", $descr);
            echo "<tr><td><b>Ссылка на биллинг</b>: </td><td><input type=\"text\" id=\"new_billing\" style=\"width:250px;\" value=\"{$data[0]}\" /></td></tr>"
            . "<tr><td><b>Ссылка на панель управления</b>: </td><td><input type=\"text\" id=\"new_cp\" style=\"width:250px;\" value=\"{$data[1]}\" /></td></tr>";
        }
        echo "<tr><td><b>Порядок вывода</b>: </td><td><input type=\"text\" id=\"new_position\" value=\"$position\" /></td></tr>";
        echo "<tr><td colspan=\"2\"><a class=\"but\" href=\"javascript:void(0);\" onclick=\"save('$type','$id');\" style=\"float: left;\"><span>Сохранить</span></a></td></tr></table>";
        break;
    //</editor-fold>
// Добавление папки/движка/хостинга/регистратора
    case "add_element":
        //<editor-fold defaultstate="collapsed" desc="">
        $type = $_REQUEST['type'];
        $title = $_REQUEST['title'];
        $desc = $_REQUEST['desc'];
        list($uid, $rownum) = $db->sql_fetchrow($db->sql_query("SELECT uid, rows FROM " . $prefix . "users WHERE login='" . $_COOKIE['ad_login'] . "'"));
        $check = $db->sql_query("SELECT id FROM " . $prefix . $type . " WHERE title='$title' AND uid='$uid'");
        $row = "";
        if ($db->sql_numrows($check) == 0) {
            list($minpos, $maxpos) = $db->sql_fetchrow($db->sql_query("SELECT MIN(position), MAX(position) FROM " . $prefix . $type . ""));
            $maxpos = ($maxpos > 0) ? $maxpos : 0;
            $minpos = ($minpos > 0) ? $minpos : 0;
            $newpos = $maxpos + 1;
            if ($type == "regs" OR $type == "hosts")
                $db->sql_query("INSERT INTO " . $prefix . $type . " (uid, title, position, descript) VALUES('$uid', '$title', '$newpos', '$desc')");
            else
                $db->sql_query("INSERT INTO " . $prefix . $type . " (uid, title, position) VALUES('$uid', '$title', '$newpos')");
            $id = $db->sql_nextid();
            list($minpos, $maxpos) = $db->sql_fetchrow($db->sql_query("SELECT MIN(position), MAX(position) FROM " . $prefix . $type . ""));
            echo "<tr id=\"element_$id\"><td style=\"text-align:left !important;\">$id</td><td style=\"text-align:left !important;\">$title</td>";
            if ($type == "hosts" OR $type == "regs") {
                $data = explode("||", $desc);
                $bilhref = (strlen($data[0]) > 0) ? "<a href=\"" . $data[0] . "\" target=\"_blank\" title=\"Откроется в новом окне\">" . $data[0] . "</a>" : "";
                $cphref = (strlen($data[1]) > 0) ? "<a href=\"" . $data[1] . "\" target=\"_blank\" title=\"Откроется в новом окне\">" . $data[1] . "</a>" : "";
                echo "<td>$bilhref</td><td>$cphref</td>";
            }
            $poslinks = "<div class=\"poslinks\">";
            $poslinks .= ( $newpos > $minpos) ? "<img src=\"images/spacer.gif\" class=\"arrup\" alt=\"\" title=\"На одну позицию выше\" onclick=\"el_position('up','$type','" . $id . "');\"> " : "";
            $poslinks .= ( $newpos < $maxpos) ? "<a href=\"javascript:;\" onclick=\"el_position('down','$type','" . $id . "');\"><img src=\"images/spacer.gif\" class=\"arrdn\" alt=\"\" title=\"На одну позицию ниже\"></a> " : "";
            $poslinks .= "</div>";
            echo "<td>$poslinks</td><td nowrap=\"nowrap\"><a href=\"ajax.php?action=edit_form&id=$id&type=$type\" rel=\"fancybox\"><img src=\"images/spacer.gif\" alt=\"Изменить\" title=\"Изменить\" class=\"edit\"></a> <img src=\"images/spacer.gif\" alt=\"Удалить\" title=\"Удалить\" class=\"delete\" onclick=\"el_delete('$id', '$type')\"></td></tr>";
            $answer = "Добавление прошло успешно";
        } else {
            echo "0";
            $row = "";
            $answer = "0";
        }
        break;
    //</editor-fold>
// Сохранение папки/движка/хостинга/регистратора
    case "edit_element":
        //<editor-fold defaultstate="collapsed" desc="">
        $type = $_REQUEST['type'];
        $title = $_REQUEST['title'];
        $position = $_REQUEST['position'];
        $id = $_REQUEST['id'];
        list($uid, $rownum) = $db->sql_fetchrow($db->sql_query("SELECT uid, rows FROM " . $prefix . "users WHERE login='" . $_COOKIE['ad_login'] . "'"));
        $check = $db->sql_query("SELECT id FROM " . $prefix . $type . " WHERE id='$id' AND uid='$uid'");
        $row = "";
        if ($db->sql_numrows($check) > 0) {
            $db->sql_query("UPDATE " . $prefix . $type . " SET title='$title', position='$position' WHERE id='$id'");
            echo "<tr id=\"element_$id\"><td style=\"text-align:left !important;\">$id</td><td style=\"text-align:left !important;\">$title</td><td style=\"text-align:left !important;\">$position</td><td nowrap=\"nowrap\"><img src=\"images/spacer.gif\" alt=\"Изменить\" title=\"Изменить\" class=\"edit\" onclick=\"$.facebox({ajax:'ajax.php?action=edit_form&id=$id&type=$type', height:400});\"> <img src=\"images/spacer.gif\" alt=\"Удалить\" title=\"Удалить\" class=\"delete\" onclick=\"el_delete('$id', '$type')\"></td></tr>";
            $answer = "Изменение прошло успешно";
        } else {
            echo "0";
            $row = "";
            $answer = "0";
        }
        break;
    //</editor-fold>
// Удаление папки/движка/хостинга/регистратора
    case "delete_element":
        //<editor-fold defaultstate="collapsed" desc="">
        $type = $_REQUEST['type'];
        $id = $_REQUEST['id'];
        list($uid, $rownum) = $db->sql_fetchrow($db->sql_query("SELECT uid, rows FROM " . $prefix . "users WHERE login='" . $_COOKIE['ad_login'] . "'"));
        $check = $db->sql_query("SELECT id FROM " . $prefix . $type . " WHERE id='$id' AND uid='$uid'");
        $row = "";
        if ($db->sql_numrows($check) > 0) {
            $db->sql_query("DELETE FROM " . $prefix . $type . " WHERE id='$id'");
            $db->sql_query("UPDATE " . $prefix . "sites SET dir='0' WHERE uid='$uid' AND dir='$id'");
            echo "Удаление прошло успешно";
        } else {
            echo "0";
            $row = "";
            $answer = "0";
        }
        break;
    //</editor-fold>
// Единичное изменение позиции сайта
    case "elpos_update":
        //<editor-fold defaultstate="collapsed" desc="">
        $id = $_REQUEST['id'];
        $direction = $_REQUEST['direction'];
        $type = $_REQUEST['eltype'];
        $newposquery = ($direction == "up") ? "position < " : "position > ";
        $ord = ($direction == "up") ? "DESC" : "ASC";
        list($mypos) = $db->sql_fetchrow($db->sql_query("SELECT position FROM " . $prefix . $type . " WHERE id='$id' LIMIT 1"));
        list($anotherid, $newpos) = $db->sql_fetchrow($db->sql_query("SELECT id, position FROM " . $prefix . $type . " WHERE " . $newposquery . "$mypos ORDER BY position $ord LIMIT 1"));
        $db->sql_query("UPDATE " . $prefix . $type . " SET position='$newpos' WHERE id='$id'");
        $db->sql_query("UPDATE " . $prefix . $type . " SET position='$mypos' WHERE id='$anotherid'");
        list($min_pos, $max_pos) = $db->sql_fetchrow($db->sql_query("SELECT MIN(position), MAX(position) FROM " . $prefix . $type . ""));
        $poslinks = "";
        $poslinks .= ( $newpos > $min_pos) ? "<img src=\"images/spacer.gif\" class=\"arrup\" alt=\"\" title=\"На одну позицию выше\" onclick=\"el_position('up','" . $type . "','" . $id . "');\"> " : "";
        $poslinks .= ( $newpos < $max_pos) ? "<a href=\"javascript:;\" onclick=\"el_position('down','" . $type . "','" . $id . "');\"><img src=\"images/spacer.gif\" class=\"arrdn\" alt=\"\" title=\"На одну позицию ниже\"></a> " : "";
        $poslinks2 = "";
        $poslinks2 .= ( $mypos > $min_pos) ? "<img src=\"images/spacer.gif\" class=\"arrup\" alt=\"\" title=\"На одну позицию выше\" onclick=\"el_position('up','" . $type . "','" . $anotherid . "');\"> " : "";
        $poslinks2 .= ( $mypos < $max_pos) ? "<a href=\"javascript:;\" onclick=\"el_position('down','" . $type . "','" . $anotherid . "');\"><img src=\"images/spacer.gif\" class=\"arrdn\" alt=\"\" title=\"На одну позицию ниже\"></a> " : "";
        $GLOBALS['_RESULT'] = array("poslinks" => $poslinks, "poslinks2" => $poslinks2, "replace" => $replace, "newid" => $anotherid);
        break;
    //</editor-fold>
// Загрузка любой из вкладок с сайтами
    case "main_load":
        //<editor-fold defaultstate="collapsed" desc="">
        $section = urldecode($_REQUEST['section']);
        $pagenum = ($_REQUEST['pagenum'] > 0) ? $_REQUEST['pagenum'] : 1;
        $offset = (($pagenum - 1) * $userconfig['rows']);
        $main_request = gen_site_rows($userconfig, 0, $pagenum, $section);
        $pages = $main_request[1];
        $buttons = main_buttons($section);
        $theader = build_header($userconfig, $section);
        echo "<table class=\"tablesorter\" id=\"tablesorter\" cellpadding=\"0\" cellspacing=\"0\"><thead><tr>" . $theader[0] . "</tr></thead><tbody id=\"tbodysites\">" . $main_request[0] . "</tbody></table><input type=\"hidden\" id=\"cur_page\" value=\"$pagenum\">";
        $buttons2 = bottom_buttons();
        $pupdate = panel_updates();
        //$pupdate = 'v'.PANEL_VERSION.' (ip: '.file_get_contents("writing/ip.txt").')';
        $fixer = "<table class=\"tablesorter\" cellpadding=\"0\" cellspacing=\"0\"><thead><tr>" . $theader[1] . "<th style=\"background:#fff;border:0;\"><div><p style=\"width:35px;\"> </p></div></th></tr></thead></table>";
        $GLOBALS['_RESULT'] = array("buttons" => $buttons, "buttons2" => $buttons2, "pages" => $pages, "pupdate" => $pupdate, "fixer" => $fixer);
        break;
    //</editor-fold>
// Форма редактирования сайта
    case "edit_site_form":
        //<editor-fold defaultstate="collapsed" desc="">
        $site_id = $_REQUEST['id'];
        global $params;
        list($uid) = $db->sql_fetchrow($db->sql_query("SELECT uid FROM " . $prefix . "users WHERE login='" . $_COOKIE['ad_login'] . "'"));

        $adq = "";
        $parres2 = $db->sql_query("SELECT id, title, type FROM " . $prefix . "params ORDER BY id ASC");
        while (list($parid2, $partitle2, $type2) = $db->sql_fetchrow($parres2)) {
            if ($type2 == "text")
                $adq .= ", param_" . $parid2;
            $partitles['param_' . $parid2] = $partitle2;
        }
        $adq_arr = explode(", ", substr($adq, 2));
        if (strlen($adq) > 0) {
            $more_res = $db->sql_query("SELECT sid$adq FROM " . $prefix . "sites_more WHERE sid='$site_id' LIMIT 1");
            $more_row = $db->sql_fetchrow($more_res);
        }
        $result = $db->sql_query("SELECT url, dir, cms, host, registrator, feeduri, comment FROM " . $prefix . "sites WHERE id='$site_id' AND uid='$uid'");
        if ($db->sql_numrows($result) == 0)
            die("<div class=\"message-box error\">Такого сайта нет, либо он вам не принадлежит</div>");
        list($url, $dir, $cms, $host, $registrator, $feeduri, $comment) = $db->sql_fetchrow($result);
        echo "<table cellpadding=\"3\" cellspacing=\"5\" border=\"0\"><tr><td colspan=\"2\"><h2>Укажите новые настройки для $url</h2></td><tr>"
        . "<tr><td><b>Папка</b>: </td><td><select id=\"site_cat\" style=\"width:250px;\"><option value=\"0\">Не вкладывать</option>";
        $result = $db->sql_query("SELECT id, title FROM " . $prefix . "dirs WHERE uid='" . $uid . "' ORDER BY title ASC");
        while (list($id, $title) = $db->sql_fetchrow($result)) {
            echo "<option value=\"$id\"";
            if ($id == $dir)
                echo " selected";
            echo ">" . stripslashes($title) . "</option>";
        }
        echo "</select></td></tr><tr><td><b>Хостинг</b>: </td><td><select id=\"site_host\" style=\"width:250px;\"><option value=\"0\">Не указан</option>";
        $result = $db->sql_query("SELECT id, title FROM " . $prefix . "hosts WHERE uid='" . $uid . "' ORDER BY title ASC");
        while (list($id, $title) = $db->sql_fetchrow($result)) {
            echo "<option value=\"$id\"";
            if ($id == $host)
                echo " selected";
            echo ">" . stripslashes($title) . "</option>";
        }
        echo "</select></td></tr><tr><td><b>Регистратор</b>: </td><td><select id=\"site_registrator\" style=\"width:250px;\"><option value=\"0\">Не указан</option>";
        $result = $db->sql_query("SELECT id, title FROM " . $prefix . "regs WHERE uid='" . $uid . "' ORDER BY title ASC");
        while (list($id, $title) = $db->sql_fetchrow($result)) {
            echo "<option value=\"$id\"";
            if ($id == $registrator)
                echo " selected";
            echo ">" . stripslashes($title) . "</option>";
        }
        echo "</select></td></tr><tr><td><b>Движок</b>: </td><td><select id=\"site_cms\" style=\"width:250px;\"><option value=\"0\">Не указан</option>";
        $result = $db->sql_query("SELECT id, title FROM " . $prefix . "cms WHERE uid='" . $uid . "' ORDER BY title ASC");
        while (list($id, $title) = $db->sql_fetchrow($result)) {
            echo "<option value=\"$id\"";
            if ($id == $cms)
                echo " selected";
            echo ">" . stripslashes($title) . "</option>";
        }
        echo "</select><input type=\"hidden\" id=\"site_url\" value=\"$url\"></td></tr>
            <tr><td><b>Суффикс FeedBurner URI:</b><br /><small>(http://feeds.feedburner.com/<b>your_site</b>)</small></td><td><input type=\"text\" style=\"width:250px\" id=\"site_fburn\" value=\"$feeduri\"></td></tr>
            <tr><td><b>Комментарий</b>:</td><td><textarea rows=\"5\" id=\"site_comment\" style=\"width:250px;\">" . $comment . "</textarea></td></tr>";
        for ($i = 0; $i < count($adq_arr); $i++) {
            echo "<tr><td><b>" . $partitles[$adq_arr[$i]] . "</b>:</td><td><textarea rows=\"5\" id=\"" . $adq_arr[$i] . "\" style=\"width:250px;\" class=\"additional\">" . $more_row[$adq_arr[$i]] . "</textarea></td></tr>";
        }
        echo "<tr><td colspan=\"2\"><a class=\"but\" href=\"javascript:void(0);\" onclick=\"site_edit('$site_id');\" style=\"float: left;\"><span>Сохранить</span></a></td></tr></table>";
        break;
    //</editor-fold>
// Единичное изменение позиции сайта
    case "onepos_update":
        //<editor-fold defaultstate="collapsed" desc="">
        $sid = $_REQUEST['id'];
        $direction = $_REQUEST['direction'];
        $newposquery = ($direction == "up") ? "position < " : "position > ";
        $ord = ($direction == "up") ? "DESC" : "ASC";
        list($mypos) = $db->sql_fetchrow($db->sql_query("SELECT position FROM " . $prefix . "sites WHERE id='$sid' LIMIT 1"));
        list($anotherid, $newpos) = $db->sql_fetchrow($db->sql_query("SELECT id, position FROM " . $prefix . "sites WHERE " . $newposquery . "$mypos ORDER BY position $ord LIMIT 1"));
        $main_request = gen_site_rows($userconfig, $anotherid, 1, "sites");
        $replace = $main_request[0];
        $db->sql_query("UPDATE " . $prefix . "sites SET position='$newpos' WHERE id='$sid'");
        $db->sql_query("UPDATE " . $prefix . "sites SET position='$mypos' WHERE id='$anotherid'");
        list($min_pos, $max_pos) = $db->sql_fetchrow($db->sql_query("SELECT MIN(position), MAX(position) FROM " . $prefix . "sites"));
        $poslinks = "";
        $poslinks .= ( $newpos > $min_pos) ? "<img src=\"images/spacer.gif\" class=\"arrup\" alt=\"\" title=\"На одну позицию выше\" onclick=\"position('up','" . $sid . "');\"> " : "";
        $poslinks .= ( $newpos < $max_pos) ? "<a href=\"javascript:;\" onclick=\"position('down','" . $sid . "');\"><img src=\"images/spacer.gif\" class=\"arrdn\" alt=\"\" title=\"На одну позицию ниже\"></a> " : "";
        $poslinks2 = "";
        $poslinks2 .= ( $mypos > $min_pos) ? "<img src=\"images/spacer.gif\" class=\"arrup\" alt=\"\" title=\"На одну позицию выше\" onclick=\"position('up','" . $anotherid . "');\"> " : "";
        $poslinks2 .= ( $mypos < $max_pos) ? "<a href=\"javascript:;\" onclick=\"position('down','" . $anotherid . "');\"><img src=\"images/spacer.gif\" class=\"arrdn\" alt=\"\" title=\"На одну позицию ниже\"></a> " : "";
        $GLOBALS['_RESULT'] = array("poslinks" => $poslinks, "poslinks2" => $poslinks2, "replace" => $replace, "newid" => $anotherid);
        break;
    //</editor-fold>
//Обновление сортировки сайтов на всей странице.
//Надо будет дописать еще и обновление для каждого из них ячейки с перемещениями
    case "multipos_update":
        //<editor-fold defaultstate="collapsed" desc="">
        $first = $_REQUEST['firstpos'];
        $positions = explode(":", $_REQUEST['positions']);
        $status = "OK";
        list($lastpos, $firstpos) = $db->sql_fetchrow($db->sql_query("SELECT MAX(position), MIN(position) FROM " . $prefix . "sites"));
        for ($i = 0; $i < count($positions); $i++) {
            $sid = str_replace("row_", "", $positions[$i]);
            $pos = $first + $i;
            if ($pos == $firstpos OR $pos == $lastpos)
                $status = "needreload";
            $db->sql_query("UPDATE " . $prefix . "sites SET position='$pos' WHERE id='$sid'");
        }
        $GLOBALS['_RESULT'] = array("status" => $status);
        break;
    //</editor-fold>
// Удаление сайта
    case "delete_site":
        //<editor-fold defaultstate="collapsed" desc="">
        $site_id = $_REQUEST['id'];
        list($uid) = $db->sql_fetchrow($db->sql_query("SELECT uid FROM " . $prefix . "users WHERE login='" . $_COOKIE['ad_login'] . "'"));
        $result = $db->sql_query("SELECT id, position FROM " . $prefix . "sites WHERE uid='$uid' AND id='$site_id'");
        if ($db->sql_numrows($result) == 0)
            die();
        list($sid, $position) = $db->sql_fetchrow($result);
        $db->sql_query("DELETE FROM " . $prefix . "sites WHERE id='$site_id' LIMIT 1");
        $db->sql_query("DELETE FROM " . $prefix . "sites_more WHERE sid='$site_id' LIMIT 1");
        $db->sql_query("DELETE FROM " . $prefix . "history WHERE sid='$site_id'");
        $new_res = $db->sql_query("SELECT id FROM " . $prefix . "sites WHERE position > $position");
        while (list($id) = $db->sql_fetchrow($new_res)) {
            $db->sql_query("UPDATE " . $prefix . "sites SET position='$position' WHERE id='$id'");
            $position++;
        }

        $GLOBALS['_RESULT'] = array("answer" => "Сайт успешно удален");
        break;
    //</editor-fold>
// Обновление показателей сайта
    case "update_site":
        //<editor-fold defaultstate="collapsed" desc="">
        $stopping = 1;
        $site_id = $_REQUEST['id'];
        $panel = $_REQUEST['panel'];
        $main_request = site_update($userconfig, $site_id, $panel);
        if (!is_array($main_request)) {
            if ($main_request == 'captcha') {
                $GLOBALS['_RESULT'] = array("answer" => "captcha");
                exit;
            }
            if (strpos($main_request, "needip") !== false) {
                $ip = substr($main_request, 7);
                $GLOBALS['_RESULT'] = array("answer" => "needip", "ip" => $ip);
                exit;
            }
        }
        $table = $main_request[0];
        $GLOBALS['_RESULT'] = array("row" => $table, "answer" => "Сайт успешно обновлен");
        break;
    //</editor-fold>
// Обновление столбца для указанных сайтов. Все остальные не затрагиваются
    case "update_col":
        //<editor-fold defaultstate="collapsed" desc="">
        $site_id = $_REQUEST['id'];
        $col = $_REQUEST['col'];
        $panel = $_REQUEST['panel'];
        $main_request = site_update($userconfig, $site_id, $panel, $col);
        if (!is_array($main_request)) {
            if ($main_request == 'captcha') {
                $GLOBALS['_RESULT'] = array("answer" => "captcha");
                exit;
            }
            if (strpos($main_request, "needip") !== false) {
                $ip = substr($main_request, 7);
                $GLOBALS['_RESULT'] = array("answer" => "needip", "ip" => $ip);
                exit;
            }
        }
        $table = $main_request[0];
        $GLOBALS['_RESULT'] = array("row" => $table, "answer" => "Сайт успешно обновлен");
        break;
    //</editor-fold>
// Сохранение и обновление данных о сайте
    case "edit_site":
        //<editor-fold defaultstate="collapsed" desc="">
        $site_id = $_REQUEST['id'];
        $site_dir = $_REQUEST['dir'];
        $site_host = $_REQUEST['host'];
        $site_cms = $_REQUEST['cms'];
        $registrator = $_REQUEST['registrator'];
        $comment = $_REQUEST['comment'];
        $panel = $_REQUEST['panel'];
        $feeduri = $_REQUEST['fburn'];
        $addstr = $_REQUEST['addstr'];
        if (strlen($addstr) > 0) {
            $addstr_arr = explode("||", substr($addstr, 2));
            $mupd = "";
            for ($i = 0; $i < count($addstr_arr); $i++) {
                $mupd .= ", " . str_replace("::", "='", $addstr_arr[$i]) . "'";
            }
            if (strlen($mupd) > 0)
                $db->sql_query("UPDATE " . $prefix . "sites_more SET sid='$site_id'$mupd WHERE sid='$site_id'");
        }
        list($uid) = $db->sql_fetchrow($db->sql_query("SELECT uid FROM " . $prefix . "users WHERE login='" . $_COOKIE['ad_login'] . "'"));
        if ($db->sql_numrows($db->sql_query("SELECT id FROM " . $prefix . "sites WHERE uid='$uid' AND id='$site_id'")) == 0)
            die();
        $db->sql_query("UPDATE " . $prefix . "sites SET dir='$site_dir', host='$site_host', cms='$site_cms', feeduri='$feeduri', registrator='$registrator', comment='$comment' WHERE id='$site_id'");
        $mainres = gen_site_rows($userconfig, $site_id, 0, $panel);
        $table = $mainres[0];
        $GLOBALS['_RESULT'] = array("row" => $table, "answer" => "Сайт успешно сохранен");
        break;
    //</editor-fold>
    // Это вызов формы для добавления сайта(ов)
    case "add_site_form":
        //<editor-fold defaultstate="collapsed" desc="">
        list($uid) = $db->sql_fetchrow($db->sql_query("SELECT uid FROM " . $prefix . "users WHERE login='" . $_COOKIE['ad_login'] . "'"));
        echo "<h2>Добавление сайта(ов)</h2><div class=\"message-box info\">Вы можете одновременно добавить несколько сайтов. Для этого введите каждый сайт с новой строки</div>";
        echo "<table width=\"100%\" cellpadding=\"3\" cellspacing=\"5\" border=\"0\"><tr><td width=\"100\"><b>Папка</b>: </td><td><select id=\"site_cat\" style=\"width:250px;\"><option value=\"0\">Не вкладывать</option>";
        $result = $db->sql_query("SELECT id, title FROM " . $prefix . "dirs WHERE uid='" . $uid . "' ORDER BY position ASC");
        while (list($id, $title) = $db->sql_fetchrow($result)) {
            echo "<option value=\"$id\">" . stripslashes($title) . "</option>";
        }
        echo "</select></td></tr><tr><td><b>Хостинг</b>: </td><td><select id=\"site_host\" style=\"width:250px;\"><option value=\"0\">Не указан</option>";
        $result = $db->sql_query("SELECT id, title FROM " . $prefix . "hosts WHERE uid='" . $uid . "' ORDER BY position ASC");
        while (list($id, $title) = $db->sql_fetchrow($result)) {
            echo "<option value=\"$id\">" . stripslashes($title) . "</option>";
        }
        echo "</select></td></tr><tr><td><b>Регистратор</b>: </td><td><select id=\"site_registrator\" style=\"width:250px;\"><option value=\"0\">Не указан</option>";
        $result = $db->sql_query("SELECT id, title FROM " . $prefix . "registrators WHERE uid='" . $uid . "' ORDER BY position ASC");
        while (list($id, $title) = $db->sql_fetchrow($result)) {
            echo "<option value=\"$id\">" . stripslashes($title) . "</option>";
        }
        echo "</select></td></tr><tr><td><b>Движок</b>: </td><td><select id=\"site_cms\" style=\"width:250px;\"><option value=\"0\">Не указан</option>";
        $result = $db->sql_query("SELECT id, title FROM " . $prefix . "cms WHERE uid='" . $uid . "' ORDER BY position ASC");
        while (list($id, $title) = $db->sql_fetchrow($result)) {
            echo "<option value=\"$id\">" . stripslashes($title) . "</option>";
        }
        echo "</select></td></tr><tr><td><b>Домен(ы)</b>:</td><td>";
        echo "<textarea rows=\"10\" id=\"sites_form\" style=\"width:250px;\"></textarea></td></tr><tr><td><b>Комментарий</b>:</td><td>";
        echo "<textarea rows=\"5\" id=\"site_comment\" style=\"width:250px;\"></textarea></td></tr><tr><td><input type=\"checkbox\" id=\"noparams\"></td><td><b>Не проверять параметры при добавлении</b></td></tr><tr><td colspan=\"2\"><a class=\"but\" href=\"javascript:void(0);\" onclick=\"do_add()\" style=\"float: left;\"><span>Добавить</span></a></td></tr></table>";
        break;
    //</editor-fold>
// Добавление сайтов в панель
    case "add_sites":
        //<editor-fold defaultstate="collapsed" desc="">
        list($uid) = $db->sql_fetchrow($db->sql_query("SELECT uid FROM " . $prefix . "users WHERE login='" . $_COOKIE['ad_login'] . "'"));
        $url = trim($_REQUEST['domain']);
        $panel = $_REQUEST['panel'];
        if (strpos($url, "/") !== false) {
            $pos = strpos($url, "/");
            $url = substr($url, 0, $pos);
        }
        if ($db->sql_numrows($db->sql_query("SELECT id FROM " . $prefix . "sites WHERE uid='$uid' AND url='$url'")) == 0) {
            $dir = $_REQUEST['dir'];
            $host = $_REQUEST['host'];
            $registrator = $_REQUEST['registrator'];
            $cms = $_REQUEST['cms'];
            $comment = $_REQUEST['comment'];
            $checked = $_REQUEST['checked'];
            $min_cur = $_REQUEST['min_cur'];
            list($lastpos) = $db->sql_fetchrow($db->sql_query("SELECT position FROM " . $prefix . "sites ORDER BY position DESC LIMIT 1"));
            $lastpos++;
            $db->sql_query("INSERT INTO " . $prefix . "sites (uid, url, dir, cms, host, registrator, comment, position, last_check) VALUES ('$uid', '$url', '$dir', '$cms', '$host', '$registrator', '$comment', '$lastpos', '" . time() . "')");
            $sid = $db->sql_nextid();
            $db->sql_query("INSERT INTO " . $prefix . "sites_more (sid) VALUES ('$sid')");
            $db->sql_query("INSERT INTO " . $prefix . "history (sid, thedate) VALUES ('$sid', '" . time() . "')");
            $lhid = $db->sql_nextid();
            $db->sql_query("UPDATE " . $prefix . "sites SET lhid='$lhid' WHERE id='$sid'");
            if ($checked == "1") {
                $main_request = gen_site_rows($userconfig, $sid, 0, $panel);
                $row = $main_request[0];
            } else {
                $main_request = site_update($userconfig, $sid, $panel);
                if (!is_array($main_request)) {
                    if ($main_request == 'captcha') {
                        $GLOBALS['_RESULT'] = array("answer" => "captcha");
                        exit;
                    }
                    if (strpos($main_request, "needip") !== false) {
                        $ip = substr($main_request, 7);
                        $GLOBALS['_RESULT'] = array("answer" => "needip", "ip" => $ip);
                        exit;
                    }
                }
                $row = $main_request[0];
            }
            $answer = "Сайт успешно добавлен";
        } else {
            $row = "";
            $answer = "0";
        }
        if ($min_cur + $userconfig['rows'] > $lastpos)
            $row = "<tr style=\"display:none;\"></tr>";
        $GLOBALS['_RESULT'] = array("row" => $row, "answer" => $answer);
        break;
    //</editor-fold>
// Сохранение настроек панели
    case "settings_save":
        //<editor-fold defaultstate="collapsed" desc="">
        $uid = $userconfig['uid'];
        $rownum = $_REQUEST['rownum'];
        $tocheck = 'last_check,age'.trim($_REQUEST['tocheck']);
        $email = $_REQUEST['email'];
        $yandex_method = $_REQUEST['yandex_method'];
        $yandex_request = $_REQUEST['yandex_request'];
        $antigate_key = $_REQUEST['antigate_key'];
        $google_key = $_REQUEST['google_key'];
        $proxies = $_REQUEST['proxies'];
        $sites_per_query = $_REQUEST['sites_per_query'];
        $time_between_checks = $_REQUEST['time_between_checks'] * 3600;
        $send_alarms = $_REQUEST['send_alarms'];
        $db->sql_query("UPDATE ".$prefix."users SET rows='$rownum', email='$email', send_alarms='$send_alarms', tocheck='$tocheck', yandex_method='$yandex_method', yandex_request='$yandex_request', antigate_key='$antigate_key', google_key='$google_key', proxies='$proxies', time_between_checks='$time_between_checks', sites_per_query='$sites_per_query' WHERE uid='$uid'");
        if (strlen($_REQUEST['new_pass']) > 0)
            $db->sql_query("UPDATE " . $prefix . "users SET password='" . md5($new_pass) . "' WHERE uid='$uid'");
        echo "{\"result\": \"done\"}";
        break;
    //</editor-fold>
// Форма редактирования настроек панели
    case "settings_form":
        //<editor-fold defaultstate="collapsed" desc="">
        $uid = $userconfig['uid'];
        $tocheck = explode(",", $userconfig['tocheck']);
        $avail = array();
        for ($i = 0; $i < count($col_titles); $i++) {
            $key = KeyName($col_titles, $i);
            if (!in_array($key, $superstatic_cols))
                $avail[] = array($key, $col_titles[$key][0]);
        }
        echo "<div style=\"padding-right:20px;padding-left:20px;\"><table align=\"center\" cellspacing=\"4\" cellpadding=\"4\" width=\"500\">
			   <tr><td colspan=\"2\"><h2>Общие настройки для пользователя</h2></td></tr>
                   <tr><td><b>E-mail</b>: </td><td valign=\"top\"><input type=\"text\" id=\"email\" size=\"30\" value=\"" . $userconfig['email'] . "\" name=\"user_password\"></td></tr>
        	   <tr><td><b>Новый пароль</b>: </td><td valign=\"top\"><input type=\"text\" id=\"user_password\" size=\"30\" value=\"\" name=\"user_password\"></td></tr>
        	   <tr><td><b>Повторите пароль</b>: </td><td valign=\"top\"><input type=\"text\" id=\"user_password2\" size=\"30\" value=\"\" name=\"user_password2\"></td></tr>
    		   <tr><td><b>Число строк в панели</b>: </td><td valign=\"top\"><input type=\"text\" id=\"rownum\" size=\"30\" value=\"" . $userconfig['rows'] . "\" name=\"rows\"></td></tr>
    		   <tr><td colspan=\"2\"><h2>Проверяемые параметры</h2><br>
                    <table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" width=\"100%\"><tr>";
        $counter = 0;
        for ($i = 0; $i < count($avail); $i++) {
            $counter++;
            echo "<td width=\"33%\"><input type=\"checkbox\" class=\"tocheck\"";
            if (in_array($avail[$i][0], $tocheck))
                echo " checked";
            echo " id=\"" . $avail[$i][0] . "\"> " . $avail[$i][1] . "</td>";
            if ($counter == 3) {
                echo "</tr><tr>";
                $counter = 0;
            }
        }
        echo "</tr></table>
                    </td></tr>
                    <tr><td colspan=\"2\"><h2>Настройки парсинга Яндекса и Google</h2></td></tr>
                    <tr><td><b>Способ парсинга Яндекса</b>:<br><small>XML - разрешает делать больше запросов; Обычная выдача - точнее</small></td><td style=\"vertical-align:top\"><select id=\"yandex_method\" onchange=\"showhide_ya();\"><option value=\"XML\"";
        if($userconfig['yandex_method']=="XML") echo " selected";
        echo ">Через сервис Я.XML</option><option value=\"SIMPLE\"";
        if($userconfig['yandex_method']=="SIMPLE") echo " selected";
        echo ">Парсинг обычной выдачи</option></select></td></tr>
                    <tr class=\"yaxml\"";
        if($userconfig['yandex_method']=="XML") echo " style=\"display:table-row;\"";
        echo "><td><b>Адрес для запросов к Я.XML</b>:<br><small>Получите, зарегистрировав IP <b>".file_get_contents("writing/ip.txt")."</b> на странице <a href=\"http://xml.yandex.ru/settings.xml\" target=\"_blank\" style=\"color:red;text-decoration:underline;\">Я.XML</a></small></td><td style=\"vertical-align:top\"><input type=\"text\" id=\"yandex_request\" value=\"".$userconfig['yandex_request']."\"  size=\"50\"></td></tr>
            <tr class=\"yasimple\"";
        if($userconfig['yandex_method']=="SIMPLE") echo " style=\"display:table-row;\"";
        echo "><td><b>Ключ antigate.com</b>:<br><small>Позволяет избежать остановки парсинга из-за выдачи страницы Яндекса \"Вы - робот\"</small></td><td><input type=\"text\" id=\"antigate_key\" value=\"".$userconfig['antigate_key']."\" size=\"50\"></td></tr>
                    <tr><td><b>Ключ Google AJAX API</b>:<br><small>Позволяет избежать неверных результатов из-за выпадания капчи Google. Регистрируется для текущего домена <a href=\"http://code.google.com/intl/ru-RU/apis/ajaxsearch/signup.html\" target=\"_blank\" style=\"color:red;text-decoration:underline;\">здесь</a><br><b>Дает сильно отличающиеся от обычной выдачи результаты</b></small></td><td style=\"vertical-align:top !important;\"><input type=\"text\" id=\"google_key\" value=\"".$userconfig['google_key']."\" size=\"50\"></td></tr>
                    <tr><td valign=\"top\"><b>Список прокси-серверов</b>:<br><small>Парсинг выдачи Яндекса и Google будет произведен с их использованием. По одному на строку в формате: <b>[username:pass@]ip_addr[:port]</b> В квадратных скобках необязательные параметры.</small></td><td style=\"vertical-align:top !important;\"><textarea cols=\"50\" rows=\"5\" id=\"proxies\">".$userconfig['proxies']."</textarea></td></tr>
                    <tr><td colspan=\"2\"><h2>Настройки крона</h2></td></tr>
                    <tr><td><b>Количество проверяемых сайтов за один проход</b>:</td><td><input type=\"text\" id=\"sites_per_query\" value=\"".$userconfig['sites_per_query']."\"></td></tr>
                        <tr><td><b>Интервал в часах между проверками каждого сайта</b>:</td><td><input type=\"text\" id=\"time_between_checks\" value=\"".($userconfig['time_between_checks'] / 3600)."\"></td></tr>
                    <tr><td><b>Отправлять письма о срабатывании будильников</b>:</td><td><input type=\"checkbox\" id=\"send_alarms\"";
        if($userconfig['send_alarms']=="1") echo " checked";
        echo "></td></tr>
			   <tr><td colspan=\"2\"><a class=\"but\" href=\"javascript:void(0);\" onclick=\"settings_save()\" style=\"float: left;\"><span>Сохранить</span></a></td></tr>
    		   </table></div>";
        break;
    //</editor-fold>
// Форма редактирования настроек вкладки
    case "panel_settings_form":
        //<editor-fold defaultstate="collapsed" desc="">
        $uid = $userconfig['uid'];
        $panel = $_GET['pan'];
        if (strlen($panel) == 0 OR $panel == "sites") {
            echo "Для этой вкладки недоступна настройка.";
            exit;
        }
        $cols_arr = explode(",", $userconfig['columns'][$panel][1]);
        echo "<div style=\"padding-left:20px;padding-right:20px;\">";
        echo "<table align=\"center\" cellspacing=\"4\" cellpadding=\"4\" width=\"400\">
			   <tr><td colspan=\"2\"><h2>Настройка вкладки</h2></td></tr>
        	   <tr><td><b>Название</b>: </td><td valign=\"top\"><input type=\"text\" id=\"panel_title\" size=\"15\" value=\"" . $userconfig['columns'][$panel][0] . "\"></td></tr>
        	   <tr><td colspan=\"2\"><h2>Отображаемые столбцы</h2><br><table class=\"tablesorter\" id=\"sortingcols\" style=\"border:0;\"><tbody>";
        for ($i = 0; $i < count($cols_arr); $i++) {
            //$key = KeyName($col_titles, $i);
            echo "<tr><td><input type=\"checkbox\" class=\"adcol\" id=\"" . $cols_arr[$i] . "\" checked></td><td class=\"dragHandle\"></td><td style=\"text-align:left !important;\"><b>" . $col_titles[$cols_arr[$i]][1] . "</b></td></tr>";
        }
        for ($i = 0; $i < count($col_titles); $i++) {
            $key = KeyName($col_titles, $i);
            if (!in_array($key, $cols_arr))
                echo "<tr><td><input type=\"checkbox\" class=\"adcol\"  id=\"$key\"></td><td class=\"dragHandle\"></td><td style=\"text-align:left !important;\"><b>" . $col_titles[$key][1] . "</b></td></tr>";
        }
        echo "</tbody></table></td></tr><tr><td colspan=\"2\"><hr><a class=\"but\" href=\"javascript:void(0);\" onclick=\"save_panel('$panel')\" style=\"float: left;\"><span>Сохранить</span></a>&nbsp;<a class=\"but2\" href=\"javascript:void(0);\" onclick=\"save_panel('$panel','1')\" style=\"float: left;\"><span>Удалить</span></a></td></tr>
            <script type=\"text/javascript\">mydnd_sorting();</script>
    		   </table>";
        echo "</div>";
        break;
    //</editor-fold>
// Форма добавления вкладки
    case "add_panel":
        //<editor-fold defaultstate="collapsed" desc="">
        $uid = $userconfig['uid'];
        $panel = count($userconfig['columns']);
        echo "<div style=\"padding-left:20px;padding-right:20px;\">";
        echo "<table align=\"center\" cellspacing=\"4\" cellpadding=\"4\" width=\"400\">
			   <tr><td colspan=\"2\"><h2>Настройка вкладки</h2></td></tr>
        	   <tr><td><b>Название</b>: </td><td valign=\"top\"><input type=\"text\" id=\"panel_title\" size=\"15\" value=\"Вкладка $panel\"></td></tr>
        	   <tr><td colspan=\"2\"><h2>Отображаемые столбцы</h2><br><table class=\"tablesorter\" id=\"sortingcols\" style=\"border:0;\"><tbody>";
        for ($i = 0; $i < count($col_titles); $i++) {
            $key = KeyName($col_titles, $i);
            echo "<tr><td><input type=\"checkbox\" class=\"adcol\"  id=\"$key\"></td><td class=\"dragHandle\"></td><td style=\"text-align:left !important;\"><b>" . $col_titles[$key][1] . "</b></td></tr>";
        }
        echo "</tbody></table></td></tr><tr><td colspan=\"2\"><hr><a class=\"but\" href=\"javascript:void(0);\" onclick=\"add_panel('$panel')\" style=\"float: left;\"><span>Добавить</span></a></td></tr>
            <script type=\"text/javascript\">mydnd_sorting();</script>
    		   </table>";
        echo "</div>";
        break;
    //</editor-fold>
// Сохранение настроек панели
    case "panel_add_do":
        //<editor-fold defaultstate="collapsed" desc="">
        $uid = $userconfig['uid'];
        $panel = $_REQUEST['panel'];
        $title = $_REQUEST['title'];
        $cols = substr($_REQUEST['cols'], 0, -1);
        list($cur_panels) = $db->sql_fetchrow($db->sql_query("SELECT columns FROM " . $prefix . "users WHERE uid='$uid' LIMIT 1"));
        $newpanels = $cur_panels . "\n" . $panel . ":" . $title . ":" . $cols;
        $db->sql_query("UPDATE " . $prefix . "users SET columns='$newpanels' WHERE uid='$uid' LIMIT 1");
        $cur_panels_arr = explode("\n", $cur_panels);
        $cur_panels_arr[] = $panel . ":" . $title . ":" . $cols;
        $toplinks = "";
        for ($i = 0; $i < count($cur_panels_arr); $i++) {
            $cur_panel = explode(":", $cur_panels_arr[$i]);
            if (strlen($cur_panel[0]) > 0)
                $toplinks .= "<a class='noac' id=\"button_" . $cur_panel[0] . "\" href='javascript: void(0);' onclick=\"main_load('" . $cur_panel[0] . "');\" title='" . $cur_panel[1] . "'><b>" . $cur_panel[1] . "</b></a>";
        }
        $toplinks .= "<a class='noac' href='ajax.php?action=add_panel' rel='fancybox' title='Добавить вкладку'><b style=\"padding-left:7px;padding-right:7px;\"><img src=\"images/plus.png\" alt=\"Добавить панель\" title=\"Добавить панель\" border=\"0\" style=\"margin-bottom:-5px;margin-top:7px;\"></b></a>";
        echo $toplinks;
        break;
    //</editor-fold>
// Сохранение настроек панели
    case "panel_save":
        //<editor-fold defaultstate="collapsed" desc="">
        $uid = $userconfig['uid'];
        $panel = $_REQUEST['panel'];
        if (strlen($panel) == 0 OR $panel == "sites") {
            echo "Для этой вкладки недоступна настройка.";
            exit;
        }
        $del = $_REQUEST['del'];
        $title = $_REQUEST['title'];
        $cols = substr($_REQUEST['cols'], 0, -1);
        list($cur_panels) = $db->sql_fetchrow($db->sql_query("SELECT columns FROM " . $prefix . "users WHERE uid='$uid' LIMIT 1"));
        $cur_panels_arr = explode("\n", $cur_panels);
        $toplinks = "";
        for ($i = 0; $i < count($cur_panels_arr); $i++) {
            if (strpos($cur_panels_arr[$i], $panel . ":") !== false) {
                $cur_panels_arr[$i] = trim($cur_panels_arr[$i]);
                if ($del != "1")
                    $cur_panels_arr[$i] = $panel . ":" . $title . ":" . $cols; else
                    unset($cur_panels_arr[$i]);
            }
            $cur_panel = explode(":", $cur_panels_arr[$i]);
            if (strlen($cur_panel[0]) > 0)
                $toplinks .= "<a class='noac' id=\"button_" . $cur_panel[0] . "\" href='javascript: void(0);' onclick=\"main_load('" . $cur_panel[0] . "');\" title='" . $cur_panel[1] . "'><b>" . $cur_panel[1] . "</b></a>";
        }
        $toplinks .= "<a class='noac' href='ajax.php?action=add_panel' rel='fancybox' title='Добавить вкладку'><b style=\"padding-left:7px;padding-right:7px;\"><img src=\"images/plus.png\" alt=\"Добавить панель\" title=\"Добавить панель\" border=\"0\" style=\"margin-bottom:-5px;margin-top:7px;\"></b></a>";
        $newpanels = implode("\n", $cur_panels_arr);
        $newpanels = str_replace("\r", "", $newpanels);
        $db->sql_query("UPDATE " . $prefix . "users SET columns='$newpanels' WHERE uid='$uid' LIMIT 1");
        echo $toplinks;
        break;
    //</editor-fold>
// Экспорт содержимого панели в Excel
    case "export_csv":
        //<editor-fold defaultstate="collapsed" desc="">
        $uid = $userconfig['uid'];
        $panel = $_REQUEST['panel'];
        include("include/excel.php");

        $excel = new ExcelWriter("writing/export.xls");

        if ($excel == false)
            exit;

        $myArr = build_header_csv($userconfig, $panel);
        $excel->writeLine($myArr);
        $sites = gen_site_rows_csv($userconfig, $panel);
        for ($i = 0; $i < count($sites); $i++) {
            $excel->writeLine($sites[$i]);
        }

        $excel->close();
        header("Expires: Mon, 1 Apr 1974 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/x-msexcel");
        header("Content-Disposition: attachment; filename=export.xls");
        header("Content-Description: PHP Generated XLS Data");
        echo file_get_contents("writing/export.xls");
        break;
    //</editor-fold>
// Подготовка плацдарма для графиков по статам сайта/ов
    case "stats":
        //<editor-fold defaultstate="collapsed" desc="">
        $uid = $userconfig['uid'];
        $sites = $_REQUEST['sites'];
        echo "<div style=\"padding-left:20px;padding-right:20px;width:800px;\"><table cellspacing=\"10\" width=\"100%\">
            <tr><td valign=\"top\"><div id=\"graph_canvas\" style=\"margin-top:-50px;\"></div></td><td valign=\"top\" style=\"width:100px;\">";

        $fields = explode(",", $userconfig['tocheck']);
        for ($i = 0; $i < count($fields); $i++) {
            if (strpos($fields[$i], "param_") === false AND !in_array($fields[$i], $static_cols)) {
                echo "<p style=\"vertical-align:middle;padding:0;margin:0;border-bottom:1px dotted #bdbdbd;padding-bottom:2px;\"><input type=\"radio\" name=\"graph\" value=\"" . $fields[$i] . "\" onclick=\"showGraph('$sites','" . $i . "');\"";
                if ($i == 0)
                    echo " checked";
                echo "> " . $col_titles[$fields[$i]][0] . "</p>";
            }
        }
        echo "</td></tr></table></div><script type=\"text/javascript\">showGraph('$sites');</script>";
        break;
    //</editor-fold>
// Отображение графиков по статам сайта/ов
    case "graph":
        //<editor-fold defaultstate="collapsed" desc="">
        $uid = $userconfig['uid'];
        $sites = $_REQUEST['sites'];
        $fields = explode(",", $userconfig['tocheck']);
        $field = (strlen($_REQUEST['field']) > 0) ? $fields[$_REQUEST['field']] : $fields[0];
        $q = "";
        $grs = array();
        if (strpos($field, "param_") === false AND !in_array($field, $static_cols)) {
            $q .= ", " . $field;
            $grs[] = $field;
        }
        $res = $db->sql_query("SELECT DISTINCT FROM_UNIXTIME(thedate, '%d-%m-%Y') AS fdate$q FROM " . $prefix . "history WHERE sid='$sites' GROUP BY fdate ORDER BY thedate DESC");
        while ($obj = mysql_fetch_object($res)) {
            $arr[] = $obj;
        }
        header('Content-Type: text/xml');
        echo "<" . "?xml version=\"1.0\" encoding=\"UTF-8\"?" . ">\n";
        echo "<chart>\n";
        echo "<series>\n";
        // echo series
        for ($i = sizeof($arr) - 1; $i > -1; $i--) {
            echo "<value xid=\"$i\">" . $arr[$i]->fdate . "</value>\n";
        }
        echo "</series>\n";
        // echo graphs
        echo "<graphs>\n";
        for ($j = 0; $j < count($grs); $j++) {
            echo "<graph title=\"" . htmlentities($col_titles[$grs[$j]][0], ENT_QUOTES, 'UTF-8') . "\" gid=\"$j\">\n";
            for ($i = sizeof($arr) - 1; $i > -1; $i--) {
                $val = str_replace("-1", "", $arr[$i]->$grs[$j]);
                echo "<value xid=\"$i\">$val</value>\n";
            }
            echo "</graph>\n";
        }
        echo "</graphs>\n";
        echo "</chart>";
        break;
    //</editor-fold>
// Изменение статичного флага. Тоггл
    case "change_stat":
        //<editor-fold defaultstate="collapsed" desc="">
        $id = $_REQUEST['id'];
        $field = $_REQUEST['field'];
        $table = (strpos($field, "param_") === false) ? "sites" : "sites_more";
        $idfield = (strpos($field, "param_") === false) ? "id" : "sid";
        list($stat) = $db->sql_fetchrow($db->sql_query("SELECT $field FROM " . $prefix . $table . " WHERE $idfield='$id' LIMIT 1"));
        if ($stat == "1") {
            $db->sql_query("UPDATE " . $prefix . $table . " SET $field='0' WHERE $idfield='$id'");
            $answer = "<img class=\"inact\" src=\"images/spacer.gif\" alt=\"Нет\" title=\"Нет\">";
        } else {
            $db->sql_query("UPDATE " . $prefix . $table . " SET $field='1' WHERE $idfield='$id'");
            $answer = "<img class=\"act\" src=\"images/spacer.gif\" alt=\"Есть\" title=\"Есть\">";
        }
        echo "{answer: '$answer'}";
        break;
    //</editor-fold>
// Форма добавления и удаления дополнительных столбцов
    case "cols_additional":
        //<editor-fold defaultstate="collapsed" desc="">
        echo "<div style=\"padding-left:20px;padding-right:20px;width:400px;\">
             <h2>Добавить столбец</h2>
             <table cellspacing=\"5\" cellpadding=\"2\">
             <tr><td><b>Название столбца:</b></td><td><input type=\"text\" id=\"col_title\" size=\"40\"></td></tr>
             <tr><td><b>Тип столбца:</b></td><td><select id=\"col_type\"><option value=\"text\">Текст</option><option value=\"yesno\">Флажок \"Да/Нет\"</option></select></td></tr>
             <tr><td colspan=\"2\"><a class=\"but\" href=\"javascript:void(0);\" onclick=\"add_column();\" style=\"float: left;\"><span>Добавить</span></a></td></tr>
             </table>";
        $result = $db->sql_query("SELECT id, type, title FROM params ORDER BY id DESC");
        if ($db->sql_numrows($result) > 0) {
            echo "<br><br><h2>Удалить столбец</h2><br><table cellspacing=\"5\" cellpadding=\"2\">";
            while (list($pid, $ptype, $ptitle) = $db->sql_fetchrow($result)) {
                echo "<tr id=\"list_$pid\"><td><a href=\"javascript:;\" onclick=\"del_column('$pid')\"><img src=\"images/spacer.gif\" alt=\"Удалить\" title=\"Удалить\" class=\"delete\"></a></td><td>$ptitle</td></tr>";
            }
            echo "</table>";
        }
        echo "</div>";
        break;
    //</editor-fold>
// Добавление дополнительного столбца
    case "column_add":
        //<editor-fold defaultstate="collapsed" desc="">
        $title = $_REQUEST['title'];
        $type = $_REQUEST['type'];
        $db->sql_query("INSERT INTO " . $prefix . "params (title, type) VALUES ('$title', '$type')");
        $pid = $db->sql_nextid();
        if ($type == 'yesno')
            $db->sql_query("ALTER TABLE  " . $prefix . "sites_more ADD param_$pid INT( 5 ) NOT NULL DEFAULT  '0'");
        else
            $db->sql_query("ALTER TABLE  " . $prefix . "sites_more ADD param_$pid VARCHAR( 255 ) NOT NULL DEFAULT  ''");
        break;
    //</editor-fold>
// Удаление дополнительного столбца
    case "column_remove":
        //<editor-fold defaultstate="collapsed" desc="">
        $id = $_REQUEST['cid'];
        $db->sql_query("DELETE FROM " . $prefix . "params WHERE id='$id'");
        $db->sql_query("ALTER TABLE  " . $prefix . "sites_more DROP param_$id");
        $db->sql_query("UPDATE " . $prefix . "users SET columns=REPLACE(columns,'param_$id,','')");
        $db->sql_query("UPDATE " . $prefix . "users SET columns=REPLACE(columns,',param_$id','')");
        break;
    //</editor-fold>
}
?>