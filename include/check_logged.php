<?php

if (!defined("_MAIN"))
    die("You have no access to this file");
include_once("include/config.php");
include_once("include/mysql.php");
//error_reporting(E_ALL);
if ($db->sql_numrows($db->sql_query("SELECT uid FROM " . $prefix . "users LIMIT 1")) == 0) {
    if (!isset($_POST['login'])) {
        $messages = "<div class=\"message-box alert\">Не создано ни одной учетной записи администратора. Создайте новую запись, используя форму выше</div>";
        $redirect = (strlen($_SERVER['HTTP_REFERER']) > 0) ? $_SERVER['HTTP_REFERER'] : $_SERVER['PHP_SELF'];
        $thefile = "\$r_file=\"" . addslashes(file_get_contents("templates/adm_create.html")) . "\";";
        eval($thefile);
        echo stripslashes($r_file);
    } else {
        $login = $_POST['login'];
        $password = $_POST['password'];
        $pass2 = $_POST['password2'];
        $mail = $_POST['adm_mail'];
        if (strlen($login) == 0 OR strlen($password) == 0 OR strlen($pass2) == 0 OR strlen($mail) == 0)
            $error = "<div class=\"message-box error\">Не заполнено одно из полей. Все поля являются обязательными к заполнению.</div>";
        if ($password != $pass2)
            $error = "<div class=\"message-box error\">Введенные пароли не совпадают</div>";
        if ($error) {
            $messages = "<div class=\"message-box alert\">Не создано ни одной учетной записи администратора. Создайте новую запись, используя форму выше</div>" . $error;
            $redirect = (strlen($_SERVER['HTTP_REFERER']) > 0) ? $_SERVER['HTTP_REFERER'] : $_SERVER['PHP_SELF'];
            $thefile = "\$r_file=\"" . addslashes(file_get_contents("templates/adm_create.html")) . "\";";
            eval($thefile);
            echo stripslashes($r_file);
            exit;
        } else {
//			die("INSERT INTO ".$prefix."users (login, password, email) VALUES('$login', '".md5($password)."', '$mail')");
            $db->sql_query("INSERT INTO " . $prefix . "users (`login`, `password`, `email`, `columns`, `tocheck`, `rows`, `send_alarms`, `time_between_checks`, `sites_per_query`, `yandex_method`) VALUES('$login', '" . md5($password) . "', '$mail', 'sites:Сайты:dir,host,registrator,cms,last_check\n1:Параметры:pr,tcy,yai,gi,dmoz,yaca,yi,ri,ybl,alexarank,feedcount,li_hits,li_hosts,age,comment,registration,expiry,last_check,ip,server,dir,host,registrator,cms', 'last_check,age,pr,tcy,yai,gi,yi,ri,ybl,yaca,dmoz,alexarank,feedcount,li_hits,li_hosts,server,registration,expiry,ip', '50', '0', '86400', '3', 'SIMPLE')");
            header("Location: " . $_SERVER['PHP_SELF']);
        }
    }

    exit;
}

function showLoginForm($error="") {
    $messages = $error;
    $redirect = (strlen($_SERVER['REQUEST_URI']) > 0) ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF'];
    $thefile = "\$r_file=\"" . addslashes(file_get_contents("templates/login.html")) . "\";";
    eval($thefile);
    echo stripslashes($r_file);
}

function generateCode($length=6) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
    $code = "";
    $clen = strlen($chars) - 1;
    while (strlen($code) < $length) {
        $code .= $chars[mt_rand(0, $clen)];
    }
    return $code;
}

function login() {
    global $db, $prefix;
    $password = isset($_COOKIE['ad_pass']) ? $_COOKIE['ad_pass'] : "";
    $login = isset($_COOKIE['ad_login']) ? $_COOKIE['ad_login'] : "";
    $hash = isset($_COOKIE['ad_hash']) ? $_COOKIE['ad_hash'] : "";
    if (strlen($password) > 0)
        $num = $db->sql_numrows($db->sql_query("SELECT uid FROM " . $prefix . "users WHERE login='$login' AND password='$password' AND auth_hash='$hash' LIMIT 1")); else
        $num = 0;
    if (!isset($_POST['password']) AND $num == 0) {
        $error = " ";
//		if(strlen($password)>0) $error .= "<div class=\"message-box error\">Неверно указан логин или пароль</div>";
        showLoginForm($error);
        exit();
    } else if (isset($_POST['password'])) {
        $pass = isset($_POST['password']) ? $_POST['password'] : '';
        $login = isset($_POST['login']) ? $_POST['login'] : '';
        $result = $db->sql_query("SELECT * FROM " . $prefix . "users WHERE login='$login' AND password='" . md5($pass) . "'");
        $num = $db->sql_numrows($result);
        if ($num == 0) {
            $error = " ";
            if (strlen($pass) > 0)
                $error .= "<div class=\"message-box error\">Неверно указан логин или пароль</div>";
            showLoginForm($error);
            exit();
        } else if (!isset($_REQUEST['logout'])) {
            $row = $db->sql_fetchrow($result);
            $auth_hash = md5(generateCode(10));
            $db->sql_query("UPDATE " . $prefix . "users SET ip='" . getenv("REMOTE_ADDR") . "', auth_hash='" . $auth_hash . "' WHERE uid='" . $row['uid'] . "'");
            setcookie("ad_pass", md5($pass));
            setcookie("ad_login", $login);
            setcookie("ad_hash", $auth_hash);
            header("Location: " . $_POST['redirect']);
        }
    }
}

if (filesize("writing/ip.txt") == 0 OR !file_exists("writing/ip.txt")) {
    $ipInfo = file_get_contents('http://ipinfo.io/json');
    $ipInfo = json_decode($ipInfo);
    if($ipInfo && $ipInfo->ip){
        file_put_contents("writing/ip.txt", $ipInfo->ip);
    }
}
login();
if (isset($_GET['logout'])) {
    $redirect = (strlen($_SERVER['REQUEST_URI']) > 0) ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF'];
    setcookie("ad_pass", "");
    setcookie("ad_login", "");
    setcookie("ad_hash", "");
    if (!eregi("logout", $redirect))
        header("Location: $redirect"); else
        header("Location: /");
}
?>