<?php
define("_MAIN", true);
include("include/config.php");
include_once("include/mysql.php");

$error = "";
if(!is_writable(WRITING_PATH."ip.txt")) $error .=  "Выставьте права на запись для файла <b>writing/ip.txt</b><br>";
if(!is_writable(WRITING_PATH."log.txt")) $error .=  "Выставьте права на запись для файла <b>writing/log.txt</b><br>";
if(!is_writable(WRITING_PATH."panel_update.txt")) $error .=  "Выставьте права на запись для файла <b>writing/panel_update.txt</b><br>";
if(!is_writable(WRITING_PATH."updates.txt")) $error .=  "Выставьте права на запись для файла <b>writing/updates.txt</b><br>";
if(!is_writable(WRITING_PATH."yacookie.txt")) $error .=  "Выставьте права на запись для файла <b>writing/yacookie.txt</b><br>";
if(!is_writable(WRITING_PATH."export.xls")) $error .=  "Выставьте права на запись для файла <b>writing/export.xls</b><br>";
if(!is_writable("tmp")) $error .=  "Выставьте права на запись для папки <b>tmp</b><br>";

if(!extension_loaded('curl')) $error .= "Установите расширение <b>CURL</b> для PHP для работы панели<br>";
if(!extension_loaded('sockets')) $error .= "Установите расширение <b>sockets</b> для PHP для работы панели<br>";

if(ini_get('allow_url_fopen') != 1) $error .= "Включите <b>allow_url_fopen</b> для PHP для работы панели<br>";


if(strlen($error)>0) {
    echo $error;
    echo "<b>Устраните указанные ошибки и повторите попытку установки/обновления панели</b>";
}


mysql_query("CREATE TABLE IF NOT EXISTS `".$prefix."alarms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `pr_more` int(11) NOT NULL DEFAULT '-1',
  `pr_less` int(11) NOT NULL DEFAULT '-1',
  `tcy_more` int(11) NOT NULL DEFAULT '-1',
  `tcy_less` int(11) NOT NULL DEFAULT '-1',
  `yai_more` int(11) NOT NULL DEFAULT '-1',
  `yai_less` int(11) NOT NULL DEFAULT '-1',
  `gi_more` int(11) NOT NULL DEFAULT '-1',
  `gi_less` int(11) NOT NULL DEFAULT '-1',
  `yi_more` int(11) NOT NULL DEFAULT '-1',
  `yi_less` int(11) NOT NULL DEFAULT '-1',
  `ri_more` int(11) NOT NULL DEFAULT '-1',
  `ri_less` int(11) NOT NULL DEFAULT '-1',
  `ybl_more` int(11) NOT NULL DEFAULT '-1',
  `ybl_less` int(11) NOT NULL DEFAULT '-1',
  `alexa_more` int(11) NOT NULL DEFAULT '-1',
  `alexa_less` int(11) NOT NULL DEFAULT '-1',
  `domain_less` int(5) NOT NULL DEFAULT '-1',
  `domain_more` int(5) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8");
mysql_query("ALTER TABLE `".$prefix."alarms` ADD `last_sent` int(12) NOT NULL");

mysql_query("CREATE TABLE IF NOT EXISTS `".$prefix."cms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(5) NOT NULL,
  `title` varchar(255) NOT NULL,
  `descript` tinytext NOT NULL,
  `position` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8");

mysql_query("CREATE TABLE IF NOT EXISTS `".$prefix."dirs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(5) NOT NULL,
  `title` varchar(255) NOT NULL,
  `descript` tinytext NOT NULL,
  `position` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8");

mysql_query("CREATE TABLE IF NOT EXISTS `".$prefix."history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` int(5) NOT NULL,
  `chid` int(11) NOT NULL DEFAULT '0',
  `thedate` int(11) NOT NULL,
  `pr` tinyint(1) NOT NULL,
  `tcy` int(11) NOT NULL,
  `yai` int(20) NOT NULL,
  `gi` int(20) NOT NULL,
  `yi` int(20) NOT NULL,
  `ri` int(20) NOT NULL,
  `ybl` int(20) NOT NULL,
  `alexarank` int(20) NOT NULL,
  `feedcount` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8");

mysql_query("ALTER TABLE `".$prefix."history` ADD `li_hits` BIGINT(12) NOT NULL");
mysql_query("ALTER TABLE `".$prefix."history` ADD `li_hosts` BIGINT(12) NOT NULL");

mysql_query("CREATE TABLE IF NOT EXISTS `".$prefix."hosts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(5) NOT NULL,
  `title` varchar(255) NOT NULL,
  `descript` tinytext NOT NULL,
  `position` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8");

mysql_query("CREATE TABLE IF NOT EXISTS `".$prefix."params` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `type` enum('text','yesno') NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8");

mysql_query("CREATE TABLE IF NOT EXISTS `".$prefix."regs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(5) NOT NULL,
  `title` varchar(255) NOT NULL,
  `descript` tinytext NOT NULL,
  `position` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8");

mysql_query("CREATE TABLE IF NOT EXISTS `".$prefix."sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(5) NOT NULL,
  `url` varchar(255) NOT NULL,
  `dir` int(5) NOT NULL,
  `cms` int(5) NOT NULL,
  `host` int(5) NOT NULL,
  `last_check` int(11) NOT NULL,
  `lhid` int(11) NOT NULL DEFAULT '1',
  `pr` tinyint(1) NOT NULL DEFAULT '0',
  `tcy` int(11) NOT NULL DEFAULT '0',
  `yai` int(20) NOT NULL DEFAULT '0',
  `gi` int(20) NOT NULL DEFAULT '0',
  `yi` int(20) NOT NULL DEFAULT '0',
  `ri` int(20) NOT NULL DEFAULT '0',
  `ybl` int(20) NOT NULL DEFAULT '0',
  `sape` tinyint(4) NOT NULL DEFAULT '0',
  `xap` tinyint(4) NOT NULL DEFAULT '0',
  `setlinks` tinyint(4) NOT NULL DEFAULT '0',
  `linkfeed` tinyint(4) NOT NULL DEFAULT '0',
  `mainlink` tinyint(4) NOT NULL DEFAULT '0',
  `uniplace` tinyint(4) NOT NULL DEFAULT '0',
  `liex` tinyint(4) NOT NULL DEFAULT '0',
  `seozavr` tinyint(4) NOT NULL DEFAULT '0',
  `adsense` tinyint(4) NOT NULL DEFAULT '0',
  `direct` tinyint(4) NOT NULL DEFAULT '0',
  `begun` tinyint(4) NOT NULL DEFAULT '0',
  `indek` tinyint(4) NOT NULL DEFAULT '0',
  `miralinks` tinyint(4) NOT NULL DEFAULT '0',
  `be2me` tinyint(4) NOT NULL DEFAULT '0',
  `server` int(11) NOT NULL,
  `registration` varchar(25) NOT NULL,
  `expiry` varchar(25) NOT NULL,
  `dmoz` tinyint(4) NOT NULL DEFAULT '0',
  `yaca` tinyint(4) NOT NULL DEFAULT '0',
  `alexarank` int(20) NOT NULL DEFAULT '0',
  `feeduri` varchar(250) NOT NULL,
  `feedcount` int(16) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8");

mysql_query("ALTER TABLE `".$prefix."sites` ADD `registrator` INT(5) NOT NULL DEFAULT '0' AFTER `host`");
mysql_query("ALTER TABLE `".$prefix."sites` ADD `age` varchar(255) NOT NULL AFTER `registration`");
mysql_query("ALTER TABLE `".$prefix."sites` ADD `li_hits` bigint(12) NOT NULL");
mysql_query("ALTER TABLE `".$prefix."sites` ADD `li_hosts` bigint(12) NOT NULL");
mysql_query("ALTER TABLE `".$prefix."sites` ADD `ip` varchar(100) NOT NULL");
mysql_query("ALTER TABLE `".$prefix."sites` ADD `comment` text NOT NULL");
mysql_query("ALTER TABLE `".$prefix."sites` ADD `position` int(10) NOT NULL");
mysql_query("ALTER TABLE `".$prefix."sites` DROP `indek`");
mysql_query("ALTER TABLE  `".$prefix."sites` ADD INDEX (`position`)");
mysql_query("ALTER TABLE  `".$prefix."sites` ADD INDEX (`uid`)");
mysql_query("ALTER TABLE  `".$prefix."sites` ADD INDEX (`last_check`)");

mysql_query("CREATE TABLE IF NOT EXISTS `".$prefix."sites_more` (
  `sid` int(11) NOT NULL,
  PRIMARY KEY (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

mysql_query("CREATE TABLE IF NOT EXISTS `".$prefix."users` (
  `uid` int(5) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL,
  `password` varchar(32) NOT NULL,
  `email` varchar(255) NOT NULL,
  `columns` text NOT NULL,
  `rows` int(5) NOT NULL,
  `time_between_checks` int(10) NOT NULL,
  `sites_per_query` int(3) NOT NULL,
  `yandex_method` enum('XML','SIMPLE') NOT NULL,
  `antigate_key` varchar(255) NOT NULL,
  `yandex_request` text NOT NULL,
  `google_key` varchar(255) NOT NULL,
  `proxies` text NOT NULL,
  `auth_hash` varchar(32) NOT NULL,
  `ip` varchar(50) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8");

mysql_query("ALTER TABLE `".$prefix."users` ADD `tocheck` TEXT NOT NULL AFTER `columns`");
mysql_query("ALTER TABLE `".$prefix."users` ADD `proxies` TEXT NOT NULL AFTER `rows`");
mysql_query("ALTER TABLE `".$prefix."users` ADD `google_key` varchar(255) NOT NULL AFTER `rows`");
mysql_query("ALTER TABLE `".$prefix."users` ADD `yandex_request` TEXT NOT NULL AFTER `rows`");
mysql_query("ALTER TABLE `".$prefix."users` ADD `antigate_key` varchar(255) NOT NULL AFTER `rows`");
mysql_query("ALTER TABLE `".$prefix."users` ADD `yandex_method` enum('XML','SIMPLE') NOT NULL AFTER `rows`");
mysql_query("ALTER TABLE `".$prefix."users` ADD `sites_per_query` int(3) NOT NULL AFTER `rows`");
mysql_query("ALTER TABLE `".$prefix."users` ADD `time_between_checks` int(10) NOT NULL AFTER `rows`");
mysql_query("ALTER TABLE `".$prefix."users` ADD `send_alarms` int(3) NOT NULL AFTER `rows`");

$result = $db->sql_query("SELECT id FROM ".$prefix."sites ORDER BY id ASC");
$counter = 1;
while(list($id) = $db->sql_fetchrow($result)) {
    $db->sql_query("UPDATE ".$prefix."sites SET position='$counter' WHERE id='$id'");
    $db->sql_query("INSERT INTO ".$prefix."sites_more (sid) VALUES ('$id')");
    $counter++;
}

$user_res = $db->sql_query("SELECT uid FROM ".$prefix."users LIMIT 1");
if($db->sql_numrows($user_res)>0) {
    list($uid) = $db->sql_fetchrow($user_res);
    $db->sql_query("UPDATE ".$prefix."users SET columns='sites:Сайты:dir,host,registrator,cms,last_check\n1:Параметры:pr,tcy,yai,gi,dmoz,yaca,yi,ri,ybl,alexarank,feedcount,li_hits,li_hosts,age,comment,registration,expiry,last_check,ip,server,dir,host,registrator,cms', tocheck='last_check,age,pr,tcy,yai,gi,yi,ri,ybl,yaca,dmoz,alexarank,feedcount,li_hits,li_hosts,server,registration,expiry,ip', rows='50', send_alarms='0', time_between_checks='86400', sites_per_query='3', yandex_method='SIMPLE' WHERE uid='$uid'");
}

header("Location: ./");

?>