<?php

include_once("include/curl_http_client.php");
//Именуем все колонки
$col_titles = array("pr" => array("PR", "Google PageRank", ""), "tcy" => array("тИЦ", "Тематический индекс цитирования", "http://yaca.yandex.ru/yca?yaca=1&text=[url]"), "yai" => array("Я.Индекс", "Количество страниц в индексе Яндекса", "http://yandex.ru/yandsearch?text=rhost%3A[yaurl].*%20|%20rhost%3A[yaurl]&lr=225"), "gi" => array("G.Индекс", "Количество страниц в индексе Google", "http://www.google.com/search?hl=en&safe=off&q=site%3A[url]&btnG=Search&aq=f&oq=&aqi="), "yi" => array("Y.Индекс", "Количество страниц в индексе Yahoo!", "http://siteexplorer.search.yahoo.com/advsearch?p=http%3A%2F%2F[url]%2F&bwmo=d&bwmf=s"), "ri" => array("R.Индекс", "Количество страниц в индексе Rambler", "http://nova.rambler.ru/srch?query=&and=1&dlang=0&mimex=0&st_date=&end_date=&news=0&limitcontext=0&exclude=&filter=[url]"), "ybl" => array("Y.Bl", "Беклинки по Yahoo", "http://siteexplorer.search.yahoo.com/advsearch?p=http%3A%2F%2F[url]%2F&bwm=i&bwmo=d&bwmf=s"), "yaca" => array("YaCa", "Наличие в Яндекс.Каталоге", "http://yaca.yandex.ru/yca?yaca=1&text=[url]"), "dmoz" => array("DMOZ", "Наличие в DMOZ", "http://search.dmoz.org/search/?q=u:[url]"), "alexarank" => array("Alexa", "Alexa Rank", "http://www.alexa.com/siteinfo/http%3A%2F%2F[url]"), "feedcount" => array("FeedBurner", "Количество подписчиков FeedBurner", "http://feedburner.google.com/fb/a/myfeeds"), "li_hits" => array("LI.hit", "Количество хитов за 24 часа LiveInternet", "http://www.liveinternet.ru/stat/[url]/"), "li_hosts" => array("LI.host", "Количество хостов за 24 часа LiveInternet", "http://www.liveinternet.ru/stat/[url]/"), "server" => array("Server", "Ответ сервера", ""), "registration" => array("Created", "Дата регистрации домена", "http://who.is/whois/[url]/"), "expiry" => array("Expiry", "Дата окончания регистрации домена", "http://who.is/whois/[url]/"), "last_check" => array("Проверка", "Дата последней проверки", ""), "dir" => array("Папка", "Папка сайта", ""), "host" => array("Хостинг", "Хостинг сайта", ""), "registrator" => array("Регистратор", "Регистратор домена", ""), "cms" => array("Движок", "CMS сайта", ""), "sape" => array("<a href=\"http://sape.ru/\" target=\"blank\"><img src=\"images/spacer.gif\" class=\"sape\" alt=\"Sape\" title=\"Sape\" /></a>", "Наличие сайта в SAPE"), "xap" => array("<a href=\"http://xap.ru/\" target=\"blank\"><img src=\"images/spacer.gif\" class=\"xap\" alt=\"XAP\" title=\"XAP\" /></a>", "Наличие сайта в XAP"), "setlinks" => array("<a href=\"http://setlinks.ru/\" target=\"blank\"><img src=\"images/spacer.gif\" class=\"sl\" alt=\"SetLinks\" title=\"SetLinks\" /></a>", "Наличие сайта в SetLinks"), "linkfeed" => array("<a href=\"http://linkfeed.ru/\" target=\"blank\"><img src=\"images/spacer.gif\" class=\"lf\" alt=\"LinkFeed\" title=\"LinkFeed\" /></a>", "Наличие сайта в LinkFeed"), "mainlink" => array("<a href=\"http://mainlink.ru/\" target=\"blank\"><img src=\"images/spacer.gif\" class=\"ml\" alt=\"MainLink\" title=\"MainLink\" /></a>", "Наличие сайта в MainLink"), "uniplace" => array("<a href=\"http://uniplace.ru/\" target=\"blank\"><img src=\"images/spacer.gif\" class=\"up\" alt=\"UniPlace\" title=\"UniPlace\" /></a>", "Наличие сайта в UniPlace"), "liex" => array("<a href=\"http://liex.ru/\" target=\"blank\"><img src=\"images/spacer.gif\" class=\"lx\" alt=\"Liex\" title=\"Liex\" /></a>", "Наличие сайта в Liex"), "seozavr" => array("<a href=\"http://seozavr.ru/\" target=\"blank\"><img src=\"images/spacer.gif\" class=\"sz\" alt=\"SeoZavr\" title=\"SeoZavr\" /></a>", "Наличие сайта в SeoZavr"), "adsense" => array("<a href=\"https://www.google.com/adsense/\" target=\"blank\"><img src=\"images/spacer.gif\" class=\"as\" alt=\"AdSense\" title=\"AdSense\" /></a>", "Сайт работает с Google AdSense"), "direct" => array("<a href=\"http://direct.yandex.ru/\" target=\"blank\"><img src=\"images/spacer.gif\" class=\"yd\" alt=\"Я.Директ\" title=\"Я.Директ\" /></a>", "Сайт работает с Яндекс.Директ"), "begun" => array("<a href=\"http://my.begun.ru/\" target=\"blank\"><img src=\"images/spacer.gif\" class=\"bg\" alt=\"Begun\" title=\"Begun\" /></a>", "Сайт работает с Begun"), "miralinks" => array("<a href=\"http://miralinks.ru/\" target=\"blank\"><img src=\"images/spacer.gif\" class=\"mr\" alt=\"MiraLinks\" title=\"MiraLinks\" /></a>", "Сайт работает с MiraLinks"), "be2me" => array("<a href=\"http://be2me.ru/\" target=\"blank\"><img src=\"images/spacer.gif\" class=\"bm\" alt=\"Be2Me\" title=\"Be2Me\" /></a>", "Сайт работает с Be2Me"), "ip" => array("IP", "IP-адрес сайта", "http://who.is/whois-ip/ip-address/[ip]/"), "comment" => array("Коммент.", "Ваш текстовый комментарий к сайту"), "age" => array("Возраст", "Возраст домена"));
//Перечисляем колонки без статистики
$static_cols = array("dir", "cms", "host", "registrator", "last_check", "sape", "xap", "setlinks", "linkfeed", "mainlink", "uniplace", "liex", "seozavr", "adsense", "direct", "begun", "miralinks", "be2me", "server", "registration", "expiry", "dmoz", "yaca", "ip", "comment", "age");
//Колонки, которые нельзя обновить
$superstatic_cols = array("dir", "cms", "host", "registrator", "last_check", "sape", "xap", "setlinks", "linkfeed", "mainlink", "uniplace", "liex", "seozavr", "adsense", "direct", "begun", "miralinks", "be2me", "comment", "age");
//Колонки со ссылками на проверку
$cols_links = array("tcy", "yai", "gi", "ri", "yi", "ybl", "yaca", "dmoz", "alexarank", "feedcount", "li_hits", "li_hosts", "registration", "expiry", "ip");
//Биржи
$birzha = array("sape", "xap", "setlinks", "linkfeed", "mainlink", "uniplace", "liex", "seozavr", "adsense", "direct", "begun", "miralinks", "be2me");

$parres = $db->sql_query("SELECT id, title, type FROM " . $prefix . "params ORDER BY id ASC");
$params = array();
while (list($parid, $partitle, $type) = $db->sql_fetchrow($parres)) {
    $static_cols[] = "param_" . $parid;
    $superstatic_cols[] = "param_" . $parid;
    $col_titles['param_' . $parid] = array($partitle, $partitle, "");
    $params['param_' . $parid] = array($partitle, $type, $parid);
}

// Отправка курлом get-методом
function urlPostContents($url, $post, $port = 80, $timeout = 40) {
    global $userconfig;
    $cUrl = new Curl_HTTP_Client(true);
    if (strpos($url, "yandex.ru") !== false OR strpos($url, "google.ru/search") !== false AND strpos($url, "xmlsearch.yandex.ru") === false) {
        $cUrl->store_cookies("writing/yacookie.txt");
        if (strlen($userconfig['proxies'])) {
            $proxies = explode("\n", $userconfig['proxies']);
            $i = rand(0, count($proxies) - 1);
            if (strlen(trim($proxies[$i])) > 0) {
                $proxy = explode("@", $proxies[$i]);
                if (count($proxy) > 1) {
                    $cUrl->set_proxy($proxy[1]);
                    $cUrl->set_proxyauth($proxy[0]);
                } else {
                    $cUrl->set_proxy($proxy[0]);
                }
            }
        }
    }
    if ($port != 80)
        $cUrl->set_port($port);
    $cUrl->set_user_agent("Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727)");
    $content = $cUrl->send_post_data($url, $post, null, $timeout);
    return $content;
}

//Отправка курлом post-методом
function urlGetContents($url, $port = 80, $timeout = 40, $getcode = 0) {
    global $userconfig;
    $cUrl = new Curl_HTTP_Client(true);
    if (strpos($url, "yandex.ru") !== false OR strpos($url, "google.ru/search") !== false) {
        $cUrl->store_cookies("writing/yacookie.txt");
        if (strlen($userconfig['proxies'])) {
            $proxies = explode("\n", $userconfig['proxies']);
            $i = rand(0, count($proxies) - 1);
            if (strlen(trim($proxies[$i])) > 0) {
                $proxy = explode("@", $proxies[$i]);
                if (count($proxy) > 1) {
                    $cUrl->set_proxy($proxy[1]);
                    $cUrl->set_proxyauth($proxy[0]);
                } else {
                    $cUrl->set_proxy($proxy[1]);
                }
            }
        }
    }
    if ($port != 80)
        $cUrl->set_port($port);
    $cUrl->set_user_agent("Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727)");
    $content = ($getcode == 0) ? $cUrl->fetch_url($url, null, $timeout) : $cUrl->get_response_only($url);
    $cUrl->close();
    return $content;
}

//Работа с антигейтом
function antigate($filename, $apikey, $is_verbose = true, $rtimeout = 5, $mtimeout = 120, $is_russian = 0, $is_phrase = 0, $is_regsense = 0, $is_numeric = 0, $min_len = 0, $max_len = 0) {
    if (!file_exists($filename)) {
        if ($is_verbose)
            addlog("file $filename not found\n");
        return false;
        addlog("Нет файла капчи");
    }
    $postdata = array(
        'method' => 'post',
        'key' => $apikey,
        'file' => '@' . $filename, // полный путь к файлу
        'phrase' => $is_phrase,
        'is_russian' => $is_russian,
        'regsense' => $is_regsense,
        'numeric' => $is_numeric,
        'min_len' => $min_len,
        'max_len' => $max_len,
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://antigate.com/in.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    addlog("антигейт курл");
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        if ($is_verbose)
            addlog("CURL returned error: " . curl_error($ch) . "\n");
        return false;
    }
    curl_close($ch);
    if (strpos($result, "ERROR") !== false) {
        if (strpos($result, "ERROR_NO_SLOT_AVAILABLE") !== false) {
            sleep(5);
            return antigate($filename, $apikey, $is_verbose, $rtimeout, $mtimeout, $is_russian, $is_phrase, $is_regsense, $is_numeric, $min_len, $max_len);
        } else {
            if ($is_verbose)
                addlog("server returned error: $result\n");
            return false;
        }
    }else {
        $ex = explode("|", $result);
        $captcha_id = $ex[1];
        if ($is_verbose)
            addlog("captcha sent, got captcha ID $captcha_id\n");
        $waittime = 0;
        if ($is_verbose)
            addlog("waiting for $rtimeout seconds\n");
        sleep($rtimeout);
        while (true) {
            $result = file_get_contents('http://antigate.com/res.php?key=' . $apikey . '&action=get&id=' . $captcha_id);
            if (strpos($result, 'ERROR') !== false) {
                if (strpos($result, "ERROR_NO_SLOT_AVAILABLE") !== false) {
                    sleep(5);
                    return antigate($filename, $apikey, $is_verbose, $rtimeout, $mtimeout, $is_russian, $is_phrase, $is_regsense, $is_numeric, $min_len, $max_len);
                } else {
                    if ($is_verbose)
                        addlog("server returned error: $result\n");
                    return false;
                }
            }
            if ($result == "CAPCHA_NOT_READY") {
                if ($is_verbose)
                    addlog("captcha is not ready yet\n");
                $waittime += $rtimeout;
                if ($waittime > $mtimeout) {
                    if ($is_verbose)
                        addlog("timelimit ($mtimeout) hit\n");
                    break;
                }
                if ($is_verbose)
                    addlog("waiting for $rtimeout seconds\n");
                sleep($rtimeout);
            }else {
                $ex = explode('|', $result);
                if (trim($ex[0]) == 'OK')
                    return trim($ex[1]);
            }
        }

        return false;
    }
    @unlink($filename);
}

//Для гугля
function StrToNum($Str, $Check, $Magic) {
    $Int32Unit = 4294967296; // 2^32

    $length = strlen($Str);
    for ($i = 0; $i < $length; $i++) {
        $Check *= $Magic;
        //If the float is beyond the boundaries of integer (usually +/- 2.15e+9 = 2^31),
        // the result of converting to integer is undefined
        // refer to http://www.php.net/manual/en/language.types.integer.php
        if ($Check >= $Int32Unit) {
            $Check = ($Check - $Int32Unit * (int) ($Check / $Int32Unit));
            //if the check less than -2^31
            $Check = ($Check < -2147483648) ? ($Check + $Int32Unit) : $Check;
        }
        $Check += ord($Str{$i});
    }
    return $Check;
}

//genearate a hash for a url
function HashURL($String) {
    $Check1 = StrToNum($String, 0x1505, 0x21);
    $Check2 = StrToNum($String, 0, 0x1003F);

    $Check1 >>= 2;
    $Check1 = (($Check1 >> 4) & 0x3FFFFC0 ) | ($Check1 & 0x3F);
    $Check1 = (($Check1 >> 4) & 0x3FFC00 ) | ($Check1 & 0x3FF);
    $Check1 = (($Check1 >> 4) & 0x3C000 ) | ($Check1 & 0x3FFF);

    $T1 = (((($Check1 & 0x3C0) << 4) | ($Check1 & 0x3C)) << 2 ) | ($Check2 & 0xF0F );
    $T2 = (((($Check1 & 0xFFFFC000) << 4) | ($Check1 & 0x3C00)) << 0xA) | ($Check2 & 0xF0F0000 );

    return ($T1 | $T2);
}

//genearate a checksum for the hash string
function CheckHash($Hashnum) {
    $CheckByte = 0;
    $Flag = 0;

    $HashStr = sprintf('%u', $Hashnum);
    $length = strlen($HashStr);

    for ($i = $length - 1; $i >= 0; $i--) {
        $Re = $HashStr{$i};
        if (1 === ($Flag % 2)) {
            $Re += $Re;
            $Re = (int) ($Re / 10) + ($Re % 10);
        }
        $CheckByte += $Re;
        $Flag++;
    }

    $CheckByte %= 10;
    if (0 !== $CheckByte) {
        $CheckByte = 10 - $CheckByte;
        if (1 === ($Flag % 2)) {
            if (1 === ($CheckByte % 2)) {
                $CheckByte += 9;
            }
            $CheckByte >>= 1;
        }
    }

    return '7' . $CheckByte . $HashStr;
}

//return the pagerank checksum hash
function getch($url) {
    return CheckHash(HashURL($url));
}

//return the pagerank figure
function getpr($url) {
    $googlehost = 'toolbarqueries.google.com';
    $googleua = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.0.6) Gecko/20060728 Firefox/1.5';

    $ch = getch($url);
    $fp = fsockopen($googlehost, 80, $errno, $errstr, 30);
    if ($fp) {

        $out = "GET /tbr?client=navclient-auto&ch=$ch&features=Rank&q=info:$url HTTP/1.1\r\n";

//echo "<pre>$out</pre>\n"; //debug only
        $out .= "User-Agent: $googleua\r\n";
        $out .= "Host: $googlehost\r\n";
        $out .= "Connection: Close\r\n\r\n";

        fwrite($fp, $out);

//$pagerank = substr(fgets($fp, 128), 4); //debug only
//echo $pagerank; //debug only
        while (!feof($fp)) {
            $data = fgets($fp, 128);
//echo $data;
            $pos = strpos($data, "Rank_");
            if ($pos === false) {

            } else {
                $pr = substr($data, $pos + 9);
                $pr = trim($pr);
                $pr = str_replace("\n", '', $pr);
                return $pr;
            }
        }
//else { echo "$errstr ($errno)<br />\n"; } //debug only
        fclose($fp);
    }
}

//generate the graphical pagerank
function pagerank($url, $width=40, $method='style') {
    if (!preg_match('/^(http:\/\/)?([^\/]+)/i', $url)) {
        $url = 'http://' . $url;
    }
    $pr = getpr($url);
    $pagerank = "$pr <img src=\"/img/pr$pr.gif\" width=\"40\" height=\"5\" border=\"0\" align=\"absmiddle\">";

//The (old) image method
    if ($method == 'image') {
        $prpos = $width * $pr / 10;
        $prneg = $width - $prpos;
        $html = '<img src="http://www.google.com/images/pos.gif" width=' . $prpos . ' height=4 border=0 alt="' . $pagerank . '"><img src="http://www.google.com/images/neg.gif" width=' . $prneg . ' height=4 border=0 alt="' . $pagerank . '">';
    }
//The pre-styled method
    if ($method == 'style') {
        $prpercent = 100 * $pr / 10;
        $html = '<div style="position: relative; width: ' . $width . 'px; padding: 0; background: #D9D9D9;"><strong style="width: ' . $prpercent . '%; display: block; position: relative; background: #5EAA5E; text-align: center; color: #333; height: 4px; line-height: 4px;"><span></span></strong></div>';
    }

    $out = '' . $pagerank . '';
    return $out;
}

//Получаем тИЦ
function getTIC($url, $rettopic=0, $retregion=0) {
    $urlAddress = 'http://bar-navig.yandex.ru/u?ver=2&url=http%3A%2F%2F' . $url . '%2F&show=1&post=0';
    if (($sResp = urlGetContents($urlAddress)) === false) {
        $num = 0;
    } else {
        $arr = array();
        $sResp = iconv("WINDOWS-1251", "UTF-8", $sResp);
        preg_match("/value=\"(\d+)\"/", $sResp, $a); //Inlinks
        $num = (int) @$a[1];
        $arr[0] = $num;
        if (preg_match("/\"Тема:([\S\s]+?)\"/", $sResp, $topicinfo))
            $arr[1] = $topicinfo[1]; else
            $arr[1] = -1;
        if (preg_match("/Регион: ([\S\s]+?)\n/", $sResp, $reginfo))
            $arr[2] = $reginfo[1]; else
            $arr[2] = -1;
    }
    if ($rettopic == 0 AND $retregion == 0)
        return $num; else
        return $arr;
}

//Получаем число подписчиков
function getFBReadersCnt($sFeedUrl) {
# http://code.google.com/intl/ru/apis/feedburner/awareness_api.html#current_basic_feed_awareness_data
    $s = 'https://feedburner.google.com/api/awareness/1.0/GetFeedData?uri=' . $sFeedUrl . "";
    $num = -1;
    if (($sResp = urlGetContents($s, 443)) === false) {
        $num = -1;
    } else {
        preg_match('/circulation="(\d+)"/', $sResp, $a);
        $num = (int) @$a[1];
    }
    return $num;
}

//Получение апекса Feedburner
function getFBurl($url) {
    $buf = urlGetContents($url, 80, 25, 1);
    preg_match('/feedburner.com\/([a-zA-Z0-9\/\-\.]*)/si', $buf, $f);
    $feeduri = trim($f[1], "/");
    return($feeduri);
}

//Получаем Я.Индекс через XML
function YandexIndex_XML($url) {
    global $userconfig;
    $num = - 1;
    $url = preg_replace("/^www\./", "", $url);
    $url_arr = explode(".", $url);
    $addquery = "";
    for ($i = count($url_arr) - 1; $i >= 0; $i--) {
        $addquery .= $url_arr[$i];
        if ($i)
            $addquery .= '.';
    }
    if (strlen($addquery) > 0) {
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"
                . "<request>"
                . "<query>rhost:$addquery | rhost:$addquery.*</query>"
                . "<groupings>"
                . "<groupby attr=\"d\" mode=\"deep\" groups-on-page=\"10\"  docs-in-group=\"1\" />"
                . "</groupings>"
                . "</request>";
        $xmlcont = urlPostContents($userconfig['yandex_request'], $xml);
        if (preg_match('/Запрос пришёл с IP-адреса (.*), не входящего/', $xmlcont, $matches))
            return "needip: " . $matches[1];
        preg_match('/<found priority="phrase">(\d+)<\/found>/', $xmlcont, $matches);
        if (strlen($matches[1]) > 0)
            $num = $matches[1];
        else
            $num = 0;
    }
    return $num;
}

function yaquery($url) {
    $url = preg_replace("/^www\./", "", $url);
    $url_arr = explode(".", $url);
    $addquery = "";
    for ($i = count($url_arr) - 1; $i >= 0; $i--) {
        $addquery .= $url_arr[$i];
        if ($i)
            $addquery .= '.';
    }
    return $addquery;
}

// Я.Индекс основной
function YandexIndex($url, $stopping = 0) {
    global $userconfig;
    if ($userconfig['yandex_method'] == "XML") {
        return YandexIndex_XML($url);
    }
    if ($userconfig['yandex_method'] == "SIMPLE") {
        $url = preg_replace("/^www\./", "", $url);
        $url_arr = explode(".", $url);
        $addquery = "";
        for ($i = count($url_arr) - 1; $i >= 0; $i--) {
            $addquery .= $url_arr[$i];
            if ($i)
                $addquery .= '.';
        }
        $urlAddress = "http://yandex.ru/yandsearch?text=rhost%3A" . $addquery . ".*%20|%20rhost%3A" . $addquery . "&lr=225";
        if (($sResp = urlGetContents($urlAddress)) === false) {
            $num = - 1;
        } else {
            if (strpos($sResp, "http://yandex.ru/captchaimg") !== false) {
                addlog("============ <debug> ============\n");
                file_put_contents(realpath(dirname(__FILE__).'/../').'/writing/captcha_page.html', $sResp);
                preg_match('/<input[^>]+?(?:name="key"[^>]+?value="(.*)"|value="(.*)"[^>]+?name="key")>/U', $sResp, $cpmatches);
                preg_match('/<input[^>]+?(?:name="retpath"[^>]+?value="(.*)"|value="(.*)"[^>]+?name="retpath")>/U', $sResp, $pathmatches);
                preg_match('/(?:src="(.*)"[^>]+?class="b-captcha__image"|class="b-captcha__image"[^>]+?src="(.*)")/U',$sResp,$imgmatches);
                $imgfile = realpath(dirname(__FILE__).'/../').'/writing/'.gen_pass(32).'.gif';
                $fp = fopen($imgfile, "wb");
                fwrite($fp, urlGetContents($imgmatches[1]));
                fclose($fp);
                $key = antigate($imgfile, $userconfig['antigate_key'], true, 5, 120, 1);
                $formUrl = "http://yandex.ru/checkcaptcha?key=" . urlencode($cpmatches[1]) . "&retpath=" . urlencode(html_entity_decode($pathmatches[1])) . "&rep=" . urlencode($key);
                addlog("\ncpmatches: " . $cpmatches[1] . "\npathmatches: " . $pathmatches[1] . "\nkey: $key\nResult Url: $formUrl\n\n============ </debug> ============\n\n");
                $sResp = urlGetContents($formUrl);
                @unlink($imgfile);
            }

            preg_match('~(?:<div class="input__found">)(?:\s|&nbsp;|&mdash;){1,3}(\d+)(?:&nbsp;|\s){1,3}(?:ответ|страниц)~uis', $sResp, $a);
            $num = (int) @$a[1];
            if ($num == 0) {
                preg_match('~(?:<div class="input__found">)(?:\s|&nbsp;|&mdash;){1,3}(\d+)(?:&nbsp;|\s){1,3}тыс\.(?:&nbsp;|\s){1,3}(?:ответ|страниц)~uis', $sResp, $a);
                $num = ((int) @$a[1]) * 1000;
                if ($num == 0) {
                    preg_match('~(?:<div class="input__found">)(?:\s|&nbsp;|&mdash;){1,3}(\d+)(?:&nbsp;|\s){1,3}млн(?:&nbsp;|\s){1,3}(?:ответ|страниц)~uis', $sResp, $a);
                    $num = ((int) @$a[1]) * 1000000;
                }
            }

            return $num;
        }
    }
}

//Генерация рандомного текста
function gen_pass($m) {
	$m = intval($m);
	$pass = "";
	for ($i = 0; $i < $m; $i++) {
		$te = mt_rand(48, 122);
		if (($te > 57 && $te < 65) || ($te > 90 && $te < 97))
			$te = $te - 9;
		$pass .= chr($te);
	}
	return $pass;
}

//G.Индекс через Ajax API
function GoogleIndexAPI($url) {
    global $userconfig;
    $url2 = urlencode($url);
    $urlAddress = 'http://ajax.googleapis.com/ajax/services/search/web?v=1.0&q=site%3A' . $url2 . '&key=' . $userconfig['google_key'] . '';
    if (($sResp = urlGetContents($urlAddress)) === false) {
        $num = -1;
    } else {
        preg_match('/"estimatedResultCount":"(\d+)"/', $sResp, $a);
        $num = (int) @$a[1];
    }
    return $num;
}

//G.Индекс
function GoogleIndex($url) {
    global $userconfig;
    if (strlen($userconfig['google_key']) > 0)
        return GoogleIndexAPI($url);
    else {
        $url2 = urlencode($url);
        $urlAddress = 'http://www.google.com/search?hl=en&safe=off&q=site%3A' . $url2 . '&btnG=Search&aq=f&oq=&aqi=';
        if (($sResp = urlGetContents($urlAddress)) === false) {
            $num = -1;
        } else {
            //echo $sResp;
            $url = str_replace("/", "\/", $url);
            $sResp = str_replace(",", "", $sResp);
            //echo '/\<b\>(\d+)\<\/b\> from \<b\>'.$url.'\<\/b\>/ ';
            preg_match('/\<b\>(\d+)\<\/b\> from \<b\>' . $url . '\<\/b\>/', $sResp, $a);
            $num = (int) @$a[1];
            if ($num == 0) {
                preg_match('/About (\d+) results/', $sResp, $a);
                $num = (int) @$a[1];
            }
        }
        return $num;
    }
}

//G.Беки через Ajax API
function GoogleBacksAPI($url) {
    $url2 = urlencode($url);
    $urlAddress = 'http://ajax.googleapis.com/ajax/services/search/web?v=1.0&q=link%3A' . $url2 . '&key=' . G_KEY . '';
    if (($sResp = urlGetContents($urlAddress)) === false) {
        $num = -1;
    } else {
        preg_match('/"estimatedResultCount":"(\d+)"/', $sResp, $a);
        $num = (int) @$a[1];
    }
    return $num;
}

//G.Беки
function GoogleBacks($url) {
    if (strlen(G_KEY) > 0)
        return GoogleBacksAPI($url);
    else {
        $url2 = urlencode($url);
        $urlAddress = 'http://www.google.com/search?hl=en&safe=off&q=link%3A' . $url2 . '&btnG=Search&aq=f&oq=&aqi=';
        if (($sResp = urlGetContents($urlAddress, 80, 20)) === false) {
            $num = -1;
        } else {
            //echo $sResp;
            //$url=str_replace("/","\/",$url);
            //echo '/\<b\>(\d+)\<\/b\> from \<b\>'.$url.'\<\/b\>/';
            preg_match('/of <b>(\d+)<\/b> linking/', $sResp, $a); //Inlinks
            $num = (int) @$a[1];
            if ($num == 0) {
                preg_match('/of <b>(\d+)<\/b> linking/', $sResp, $a); //Inlinks
                $num1000 = (int) @$a[1];
                $num1 = (int) @$a[2];
                $num = $num1000 * 1000 + $num1;
            }
        }
        return $num;
    }
}

//Беклинки по Яху
function YahooBacks($url) {
    // $urlAddress = 'http://siteexplorer.search.yahoo.com/advsearch?p=http%3A%2F%2F'.$url.'%2F&bwm=i&bwmo=d&bwmf=u';
    $urlAddress = 'http://siteexplorer.search.yahoo.com/advsearch?p=http%3A%2F%2F' . $url . '%2F&bwm=i&bwmo=d&bwmf=s';
    if (($sResp = urlGetContents($urlAddress)) === false) {
        $num = -1;
    } else {
        //echo $sResp;
        //$url=str_replace("/","\/",$url);
        //echo '/\<b\>(\d+)\<\/b\> from \<b\>'.$url.'\<\/b\>/';
        preg_match('/Inlinks \((\d+)\)/', $sResp, $a); //Inlinks
        $num = (int) @$a[1];
        if ($num == 0) {
            preg_match('/Inlinks \((\d+),(\d+)\)/', $sResp, $a); //Inlinks
            $num1000 = (int) @$a[1];
            $num1 = (int) @$a[2];
            $num = $num1000 * 1000 + $num1;
            if ($num == 0) {
                preg_match('/Pages \((\d+),(\d+),(\d+)\)/', $sResp, $a); //Inlinks
                $num1000000 = (int) @$a[1];
                $num1000 = (int) @$a[2];
                $num1 = (int) @$a[2];
                $num = $num1000000 * 1000000 + $num1000 * 1000 + $num1;
            }
        }
    }
    return $num;
}

//Инфо о домене
function DomainInfo($url) {
    $url = preg_replace("/^www\./", "", $url);
    $info = getWhoisData($url);
    $date = strtotime($info[2]);
    $date2 = strtotime($info[1]);
    if ($date == -1)
        $date = "0-00-00";
    if ($date2 == -1)
        $date2 = "0-00-00";
    return array($date, $date2);
}

//Y.Индекс
function YahooIndex($url) {
    $urlAddress = 'http://siteexplorer.search.yahoo.com/advsearch?p=http%3A%2F%2F' . $url . '%2F&bwmo=d&bwmf=s';
    if (($sResp = urlGetContents($urlAddress)) === false) {
        $num = -1;
    } else {
        //echo $sResp;
        //$url=str_replace("/","\/",$url);
        //echo '/\<b\>(\d+)\<\/b\> from \<b\>'.$url.'\<\/b\>/';
        preg_match('/Pages \((\d+)\)/', $sResp, $a); //Inlinks
        $num = (int) @$a[1];
        if ($num == 0) {
            preg_match('/Pages \((\d+),(\d+)\)/', $sResp, $a); //Inlinks
            $num1000 = (int) @$a[1];
            $num1 = (int) @$a[2];
            $num = $num1000 * 1000 + $num1;
            if ($num == 0) {
                preg_match('/Pages \((\d+),(\d+),(\d+)\)/', $sResp, $a); //Inlinks
                $num1000000 = (int) @$a[1];
                $num1000 = (int) @$a[2];
                $num1 = (int) @$a[2];
                $num = $num1000000 * 1000000 + $num1000 * 1000 + $num1;
            }
        }
    }
    return $num;
}

//R.Индекс
function RamblerIndex($url) {
    $urlAddress = 'http://nova.rambler.ru/srch?query=&and=1&dlang=0&mimex=0&st_date=&end_date=&news=0&limitcontext=0&exclude=&filter=' . $url;
    if (($sResp = urlGetContents($urlAddress)) === false) {
        $num = -1;
    } else {
        //echo $sResp;
        //$url=str_replace("/","\/",$url);
        //echo '/\<b\>(\d+)\<\/b\> from \<b\>'.$url.'\<\/b\>/';
        $sResp = str_replace(" тыс.", "000", $sResp);
        preg_match('/(\d+) документов<\/div>/', $sResp, $a); //Inlinks
        $num = (int) @$a[1];
        if ($num == 0) {
            preg_match('/(\d+) (\d+) документов<\/div>/', $sResp, $a); //Inlinks
            $num1000 = (int) @$a[1];
            $num1 = (int) @$a[2];
            $num = $num1000 * 1000 + $num1;
        }
    }
    return $num;
}

//Получение статуса ответа сервера
function serverStat($url) {
    return urlGetContents($url, 80, 25, 1, 1);
}

//Проверка DMOZ
function getDMOZ($url) {
    $buf = urlGetContents('http://search.dmoz.org/search/?q=u:' . $url);
    if (preg_match('!DMOZ Sites!ism', $buf)) {
        return (1);
    } else {
        return (0);
    }
}

//Проверка Я.Каталог
function getYACA($site) {
    $buf = urlGetContents('http://yaca.yandex.ru/yca?yaca=1&text='.$site, 80, 15);
    // нигде не встречается
    if (preg_match('!<div class="z-counter">[^<]*?[1-9]+?[^<]*?</div>!is', $buf)) {
        return true;
    } else {
        return false;
    }
}

//Количество посетителей LI
function li_hosts($url) {
    $url = preg_replace("/^www\./", "", $url);
    $dayvis = "-1";
    $sCont = urlGetContents("http://counter.yadro.ru/values?site=$url");
    if (preg_match("/LI_day_vis = (\d+);/ism", $sCont, $listat))
        $dayvis = $listat[1];
    $sCont = urlGetContents("http://counter.yadro.ru/values?site=www.$url");
    if (preg_match("/LI_day_vis = (\d+);/ism", $sCont, $listat))
        $dayvis = $listat[1];
    return $dayvis;
}

//Количество просмотров LI
function li_hits($url) {
    $url = preg_replace("/^www\./", "", $url);
    $dayvis = "-1";
    $sCont = urlGetContents("http://counter.yadro.ru/values?site=$url");
    if (preg_match("/LI_day_hit = (\d+);/ism", $sCont, $listat))
        $dayvis = $listat[1];
    $sCont = urlGetContents("http://counter.yadro.ru/values?site=www.$url");
    if (preg_match("/LI_day_hit = (\d+);/ism", $sCont, $listat))
        $dayvis = $listat[1];
    return $dayvis;
}

//Alexa Rank
function alexa_rank($url) {
    $url = 'http://data.alexa.com/data?cli=10&dat=snbamz&url=' . $url;
    $v = urlGetContents($url);
    preg_match('/\<popularity url\="(.*?)" TEXT\="([0-9]+)"\/\>/si', $v, $r);
    return ($r[2]) ? $r[2] : '-1';
}

//Whois информация о домене
function getWhoisData($domain) {
    if (preg_match("/.ua$/", $domain))
        return uaWhois($domain);
    if (preg_match("/.name$/", $domain))
        return nameWhois($domain);
    if (preg_match("/.livejournal.com$/", $domain))
        return lj_whois($domain);
    $query = $domain;
    $output = 'object';

    include_once('include/phpwhois/whois.main.php');
    include_once('include/phpwhois/whois.utils.php');

    $whois = new Whois();

    $allowproxy = false;

    $result = $whois->Lookup($query);

    $winfo = '';

    if ($whois->Query['status'] < 0) {
        $winfo = implode($whois->Query['errstr'], "\n<br></br>");
    } else {
        $utils = new utils;
        $winfo = $utils->showObject($result);
    }

    $winfo = utf8_encode($winfo);

    //echo $winfo;

    $returnArr[] = substr($winfo, stripos($winfo, "registered->") + 12, 3); // "yes" or "no "
    $returnArr[] = substr($winfo, stripos($winfo, "created->") + 9, 10); //registration date
    $returnArr[] = substr($winfo, stripos($winfo, "expires->") + 9, 10); //expiration date
    $registrar = substr($winfo, stripos($winfo, "registrar->") + 11); //expiration date
    if (stripos($registrar, "&nbsp;") <= 0) {
        $pos = 25;
    } else {
        $pos = stripos($registrar, "&nbsp;");
    }
    $registrar = substr($registrar, 0, $pos);
    $returnArr[] = $registrar;
    return $returnArr;
}

//Whois информация о домене в зонах .ua
function uaWhois($domain) {
    if (preg_match("/.pp.ua$/", $domain)) {
        $fp = fsockopen("whois.pp.ua", 43);
        fputs($fp, "$domain\r\n");
        $string = "";
        while (!feof($fp)) {
            $string.=fgets($fp, 128);
        }
        fclose($fp);
        if (preg_match("/Expiration Date:(.*)\n/i", $string, $sp)) {
            $returnArr[0] = "yes";
            $date = strtotime($sp[1]);
            $returnArr[2] = date("Y-m-d", $date);
            preg_match("/Created On:(.*)\n/i", $string, $sp);
            $date = strtotime($sp[1]);
            $returnArr[1] = date("Y-m-d", $date);
        } else {
            $returnArr[0] = "no";
        }
    } else {
        $fp = fsockopen("whois.net.ua", 43);
        fputs($fp, "$domain\r\n");
        $string = "";
        while (!feof($fp)) {
            $string.=fgets($fp, 128);
        }
        fclose($fp);
        if (preg_match("/status:(.*) OK-UNTIL ([\d]{14})\n/i", $string, $sp)) {
            $returnArr[0] = "yes";
            $returnArr[2] = substr($sp[2], 0, 4) . "-" . substr($sp[2], 4, 2) . "-" . substr($sp[2], 6, 2);
            $string = preg_replace("/% Domain Record:(.*)% Administrative Contact:/is", "", $string);
            preg_match("/changed:(.*)(\d{14})\n/i", $string, $sp);
            $returnArr[1] = substr($sp[2], 0, 4) . "-" . substr($sp[2], 4, 2) . "-" . substr($sp[2], 6, 2);
        } else {
            $returnArr[0] = "no";
        }
    }
    return $returnArr;
}

//Дата реги ЖЖ
function lj_whois($domain) {
    $data = urlGetContents("http://" . $domain . "/profile");
    if (preg_match("/Created on \d{4}-\d{2}-\d{2}/i", $data, $sp)) {
        $date = str_replace("Created on ", "", $sp[0]);
        $returnArr = array();
        $returnArr[] = "yes";
        $returnArr[] = $date;
        $returnArr[] = "0";
    } else {
        $returnArr[] = "no";
    }
    return $returnArr;
}

//Whois информация о домене в зоне .name
function nameWhois($domain) {
    $fp = fsockopen("whois.name", 43);
    fputs($fp, "domain=$domain\r\n");
    $string = "";
    while (!feof($fp)) {
        $string.=fgets($fp, 128);
    }
    fclose($fp);
    if (preg_match("/Expires On: (.*)T/i", $string, $sp)) {
        $returnArr[0] = "yes";
        $date = strtotime($sp[1]);
        $returnArr[2] = date("Y-m-d", $date);
        preg_match("/Created On: (.*)T/i", $string, $sp);
        $date = strtotime($sp[1]);
        $returnArr[1] = date("Y-m-d", $date);
    } else {
        $returnArr[0] = "no";
    }
    return $returnArr;
}

//Разница в датах
function dateDiff($date1, $date2) {
    if ($date1 < 1)
        return "";
    $blocks = array(
        array('name' => array("год", "год", "лет"), 'amount' => 60 * 60 * 24 * 365),
        array('name' => 'мес.', 'amount' => 60 * 60 * 24 * 31)
    );

    $diff = abs($date1 - $date2);

    $levels = 2;
    $current_level = 1;
    $result = array();
    foreach ($blocks as $block) {
        if ($current_level > $levels) {
            break;
        }
        if ($diff / $block['amount'] >= 1) {
            $amount = floor($diff / $block['amount']);
            $result[] = (is_array($block['name'])) ? declOfNum($amount, $block['name']) : $amount . '&nbsp;' . $block['name'];
            $diff -= $amount * $block['amount'];
            $current_level++;
        }
    }
    return implode('&nbsp;', $result);
}

//Склонение числительных
//declOfNum(5, array('иностранный язык', 'иностранных языка', 'иностранных языков'))
function declOfNum($number, $titles) {
    $cases = array(2, 0, 1, 1, 1, 2);
    return $number . "&nbsp;" . $titles[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
}

//Генерация нумерации страниц
function genPagination($total, $limit, $currentPage, $baseLink, $nextPrev=true) {
    if (!$total OR !$currentPage) {
        return false;
    }

    //Total Number of pages
    $totalPages = ceil($total / $limit);

    //Show only 3 pages before current page(so that we don't have too many pages)
    $min = ($currentPage - 3 < $totalPages && $currentPage - 3 > 0) ? $currentPage - 3 : 1;

    //Show only 3 pages after current page(so that we don't have too many pages)
    $max = ($currentPage + 3 > $totalPages) ? $totalPages : $currentPage + 3;
    if ($max > $totalPages)
        $max = $totalPages;

    //Variable for the actual page links
    $pageLinks = "";

    //Loop to generate the page links
    for ($i = $min; $i <= $max; $i++) {
        if ($currentPage == $i) {
            //Current Page
            $pageLinks .= '<li class="active"><a><span>' . $i . '</span></a></b>';
        } else {
            $pageLinks .= '<li><a href="javascript:void(0)" onclick="main_load(\'' . $baseLink . '\',\'' . $i . '\')"><span>' . $i . '</span></a>';
        }
    }

    if ($nextPrev) {
        //Next and previous links
        $next = ($currentPage + 1 > $totalPages) ? false : '<li><a href="javascript:void(0)" onclick="main_load(\'' . $baseLink . '\',\'' . ($currentPage + 1) . '\')"><span>&gt;</span></a>';

        $prev = ($currentPage - 1 <= 0 ) ? false : '<li><a href="javascript:void(0)" onclick="main_load(\'' . $baseLink . '\',\'' . ($currentPage - 1) . '\')"><span>&lt;</span></a></li>';
    }

    return "<ul>" . $prev . $pageLinks . $next . "</ul>";
}

//Дебаг-функция - запись переменной в файл
function printrfile($var) {
    ob_start();
    print_r($var);
    $var = ob_get_contents();
    ob_end_clean();
    $fp = fopen('writing/log.txt', 'w');
    fputs($fp, $var);
    fclose($fp);
}

//Проверяем файл с данными об апдейтах PR, ЯВ, тИЦ и парсим pr-cy
function list_updates() {
    $last = filemtime("writing/updates.txt");
    if (!$last || (( time() - $last ) > 86400)) {
        $upCY = $upPR = $upYAV = '&mdash;';
        $sResp = file_get_contents("http://pr-cy.ru/updates.xml");
        if($updates = new SimpleXMLElement($sResp)){
            $upCY = $updates->cy;
            $upPR = $updates->pr;
            $upYAV = $updates->yav;
        }
        $content = '<b style="color:#646464;">PR</b>: <span class="count">'.$upPR.'</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.
                  '<b style="color:#646464;">тИЦ</b>: <span class="count">'.$upCY.'</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.
                  '<b style="color:#646464;">ЯВ</b>: <span class="count">'.$upYAV.'</span>';
        file_put_contents("writing/updates.txt", $content);
        return $content;
    }
    return file_get_contents("writing/updates.txt");
}

//Проверяем наличие обновлений панели
function panel_updates() {
    $last = filemtime("writing/panel_update.txt");
    if (!$last || (( time() - $last ) > 86400)) {
        $sResp = urlGetContents("https://raw.githubusercontent.com/optim1zer/Panel-X/master/writing/panel_update.txt");
        if($sResp){
            file_put_contents("writing/panel_update.txt", trim($sResp));
        }
    }
    $ret = file_get_contents("writing/panel_update.txt");
    $ret = trim($ret);
    $ip = file_get_contents("writing/ip.txt");
    if ($ret && $ret != PANEL_VERSION)
        $info = '<a href="https://github.com/optim1zer/Panel-X" target="_blank" style="color:red;">Доступно обновление панели</a>';
    else
        $info = "v" . PANEL_VERSION . " (ip: $ip)";
    return($info);
}

//Функция построения заголовка таблицы для Excel файла
function build_header_csv($user_config, $panel=1) {
    global $col_titles, $superstatic_cols, $static_cols, $cols_links, $birzha;
    $cols = explode(",", $user_config['columns'][$panel][1]);
    $header = array();
    $header[] = "URL";
    for ($i = 0; $i < count($cols); $i++) {
        $header[] = "" . $col_titles[$cols[$i]][0] . "";
    }
    return $header;
}

//Функция построения заголовка таблицы
function build_header($user_config, $panel=1) {
    global $col_titles, $superstatic_cols, $static_cols, $cols_links, $birzha;
    $cols = explode(",", $user_config['columns'][$panel][1]);
    $header = "";
    $fixer = "";
    if ($panel == "sites") {
        $header .= "<th class=\"{sorter: false}\" id=\"htd_0\" style=\"width:20px;\"></th><th id=\"htd_00\" class=\"{sorter: false}\"></th>";
        $fixer .= "<th  class=\"{sorter: false}\" id=\"htd_0\" style=\"width:20px;\"><div></div></th><th id=\"htd_00\" class=\"{sorter: false}\" style=\"width:34px;\"></th>";
    }
    $header .= "<th  class=\"{sorter: false}\" id=\"htd_1\"></th><th title=\"Сайт\" id=\"htd_2\">URL</th>";
    $fixer .= "<th id=\"ftd_1\"><div></div></th><th title=\"Сайт\" id=\"ftd_2\" class=\"header\"><div>URL</div></th>";
    for ($i = 0; $i < count($cols); $i++) {
        $fixer .= ( !in_array($cols[$i], $superstatic_cols)) ? "<th title=\"" . $col_titles[$cols[$i]][1] . "\" id=\"ftd_" . ($i + 3) . "\" class=\"header reloading\" id=\"reloader_" . $cols[$i] . "\"><div class=\"inf\">" . $col_titles[$cols[$i]][0] . "</div><div class=\"rel\"><img src=\"images/spacer.gif\" title=\"Обновить столбец\" alt=\"Обновить столбец\" onclick=\"refresh_col('" . $cols[$i] . "');\"></div></th>" : "<th title=\"" . $col_titles[$cols[$i]][1] . "\" id=\"ftd_" . ($i + 3) . "\" class=\"header\"><div>" . $col_titles[$cols[$i]][0] . "</div></th>";
        $header .= "<th id=\"htd_" . ($i + 3) . "\" title=\"" . $col_titles[$cols[$i]][1] . "\">" . $col_titles[$cols[$i]][0] . "</th>";
    }
    $fixer .= "";
    return array($header, $fixer);
}

//Функция вывода кнопок и меню фильтров
function main_buttons($panel=1) {
    global $prefix, $db, $userconfig;
    $uid = $userconfig['uid'];
    $buttons = "<div id=\"left\">"
            . "\n\t<a class=\"add\" href='ajax.php?action=add_site_form'\" rel=\"fancybox\"><span>Добавить сайт</span></a>"
            . "\n\t<span id=\"search\"><b id=\"qs\"></b></span>";
    if ($panel != 'sites') {
        $dirs_arr = $hosts_arr = $hosts_arr = $regs_arr = $cms_arr = $alarms_arr = array();
        $dirs_arr = @explode(":", preg_replace(array("/^:/", "/:$/"), array("", ""), $_COOKIE['filter_dirs']));
        $hosts_arr = @explode(":", preg_replace(array("/^:/", "/:$/"), array("", ""), $_COOKIE['filter_hosts']));
        $regs_arr = @explode(":", preg_replace(array("/^:/", "/:$/"), array("", ""), $_COOKIE['filter_regs']));
        $cms_arr = @explode(":", preg_replace(array("/^:/", "/:$/"), array("", ""), $_COOKIE['filter_cms']));
        $alarms_arr = @explode(":", preg_replace(array("/^:/", "/:$/"), array("", ""), $_COOKIE['filter_alarms']));
        $buttons .= "<ul id=\"nav\">"
                . "\n\t<li class=\"first\"><a>&nbsp;</a></li>"
                . "\n\t<li><a href=\"javascript: void(0);\"><img src=\"images/spacer.gif\" class=\"dir\" border=\"0\" alt=\"Папки\">Папки</a>"
                . "\n\t\t<ul>"
                . "\n\t\t\t<li><a href=\"javascript: void(0);\" onclick=\"page('dirs');\"><img src=\"images/confs.gif\" style=\"vertical-align:middle;width:16px;height:16px;padding:0;margin:0;margin-top:2px;margin-right:5px;\" border=\"0\" alt=\"Настроить папки\"> <b>Настроить</b></a></li>";
        if (strlen($_COOKIE['filter_dirs']) == 0 OR $_COOKIE['filter_dirs'] == ":")
            $buttons .= "\n\t\t\t<li class=\"active\"><a href=\"javascript: void(0);\" onclick=\"del_filter('dirs');\">Все</a></li>"; else
            $buttons .= "\n\t\t\t<li><a href=\"javascript: void(0);\" onclick=\"del_filter('dirs');\">Все</a></li>";
        $result = $db->sql_query("SELECT id, title FROM " . $prefix . "dirs WHERE uid='" . $uid . "' ORDER BY position ASC");
        while (list($id, $title) = $db->sql_fetchrow($result)) {
            if (!in_array($id, $dirs_arr))
                $buttons .= "\n\t\t\t<li><input type=\"checkbox\" id=\"dirs_$id\"><a href=\"javascript: void(0);\" onclick=\"add_filter('dirs','$id');\">" . stripslashes($title) . "</a></li>";
            else
                $buttons .= "\n\t\t\t<li class=\"active\"><input type=\"checkbox\" id=\"dirs_$id\" checked><a href=\"javascript: void(0);\" onclick=\"switch_filter('dirs','$id');\">" . stripslashes($title) . "</a></li>";
        }
        $buttons .= "\n\t\t</ul>\n\t</li>\n\t<li><a href=\"javascript: void(0);\"><img src=\"images/spacer.gif\" class=\"ser\" border=\"0\" alt=\"Хостинги\">Хостинги</a>"
                . "\n\t\t<ul>"
                . "\n\t\t\t<li><a href=\"javascript: void(0);\" onclick=\"page('hosts');\"><img src=\"images/confs.gif\" style=\"vertical-align:middle;width:16px;height:16px;padding:0;margin:0;margin-top:2px;margin-right:5px;\" border=\"0\" alt=\"Настроить хостинги\"> <b>Настроить</b></a></li>";
        if (strlen($_COOKIE['filter_hosts']) == 0 OR $_COOKIE['filter_hosts'] == ":")
            $buttons .= "\n\t\t\t<li class=\"active\"><a href=\"javascript: void(0);\" onclick=\"del_filter('hosts');\">Все</a></li>"; else
            $buttons .= "\n\t\t\t<li><a href=\"javascript: void(0);\" onclick=\"del_filter('hosts');\">Все</a></li>";
        $result = $db->sql_query("SELECT id, title FROM " . $prefix . "hosts WHERE uid='" . $uid . "' ORDER BY position ASC");
        while (list($id, $title) = $db->sql_fetchrow($result)) {
            if (!in_array($id, $hosts_arr))
                $buttons .= "\n\t\t\t<li><input type=\"checkbox\" id=\"hosts_$id\"><a href=\"javascript: void(0);\" onclick=\"add_filter('hosts','$id')\">" . stripslashes($title) . "</a></li>";
            else
                $buttons .= "\n\t\t\t<li class=\"active\"><input type=\"checkbox\" id=\"hosts_$id\" checked><a href=\"javascript: void(0);\" onclick=\"switch_filter('hosts','$id')\">" . stripslashes($title) . "</a></li>";
        }
        $buttons .= "\n\t\t</ul>\n\t</li>\n\t<li><a href=\"javascript: void(0);\"><img src=\"images/spacer.gif\" class=\"regs\" border=\"0\" alt=\"Регистраторы\">Регистраторы</a>"
                . "\n\t\t<ul>"
                . "\n\t\t\t<li><a href=\"javascript: void(0);\" onclick=\"page('regs');\"><img src=\"images/confs.gif\" style=\"vertical-align:middle;width:16px;height:16px;padding:0;margin:0;margin-top:2px;margin-right:5px;\" border=\"0\" alt=\"Настроить регистраторов\"> <b>Настроить</b></a></li>";
        if (strlen($_COOKIE['filter_regs']) == 0 OR $_COOKIE['filter_regs'] == ":")
            $buttons .= "\n\t\t\t<li class=\"active\"><a href=\"javascript: void(0);\" onclick=\"del_filter('regs');\">Все</a></li>"; else
            $buttons .= "\n\t\t\t<li><a href=\"javascript: void(0);\" onclick=\"del_filter('regs');\">Все</a></li>";
        $result = $db->sql_query("SELECT id, title FROM " . $prefix . "regs WHERE uid='" . $uid . "' ORDER BY position ASC");
        while (list($id, $title) = $db->sql_fetchrow($result)) {
            if (!in_array($id, $regs_arr))
                $buttons .= "\n\t\t\t<li><input type=\"checkbox\" id=\"regs_$id\"><a href=\"javascript: void(0);\" onclick=\"add_filter('regs','$id')\">" . stripslashes($title) . "</a></li>";
            else
                $buttons .= "\n\t\t\t<li class=\"active\"><input type=\"checkbox\" id=\"regs_$id\" checked><a href=\"javascript: void(0);\" onclick=\"switch_filter('regs','$id')\">" . stripslashes($title) . "</a></li>";
        }
        $buttons .= "\n\t\t</ul>\n\t</li>\n\t<li><a href=\"javascript: void(0);\"><img src=\"images/spacer.gif\" class=\"cms\" border=\"0\" alt=\"Движки\">Движки</a>"
                . "\n\t\t<ul>"
                . "\n\t\t\t<li><a href=\"javascript: void(0);\" onclick=\"page('cms');\"><img src=\"images/confs.gif\" style=\"vertical-align:middle;width:16px;height:16px;padding:0;margin:0;margin-top:2px;margin-right:5px;\" border=\"0\" alt=\"Настроить движки\"> <b>Настроить</b></a></li>";
        if (strlen($_COOKIE['filter_cms']) == 0 OR $_COOKIE['filter_cms'] == ":")
            $buttons .= "\n\t\t\t<li class=\"active\"><a href=\"javascript: void(0);\" onclick=\"del_filter('cms');\">Все</a></li>"; else
            $buttons .= "\n\t\t\t<li><a href=\"javascript: void(0);\" onclick=\"del_filter('cms');\">Все</a></li>";
        $result = $db->sql_query("SELECT id, title FROM " . $prefix . "cms WHERE uid='" . $uid . "' ORDER BY position ASC");
        while (list($id, $title) = $db->sql_fetchrow($result)) {
            if (!in_array($id, $cms_arr))
                $buttons .= "\n\t\t\t<li><input type=\"checkbox\" id=\"cms_$id\"><a href=\"javascript: void(0);\" onclick=\"add_filter('cms','$id')\">" . stripslashes($title) . "</a></li>";
            else
                $buttons .= "\n\t\t\t<li class=\"active\"><input type=\"checkbox\" id=\"cms_$id\" checked><a href=\"javascript: void(0);\" onclick=\"switch_filter('cms','$id')\">" . stripslashes($title) . "</a></li>";
        }
        $buttons .= "\n\t\t</ul>\n\t</li>\n\t<li><a href=\"javascript: void(0);\"><img src=\"images/spacer.gif\" class=\"ala\" border=\"0\" alt=\"Будильники\">Будильники</a>"
                . "\n\t\t<ul>"
                . "\n\t\t\t<li><a href=\"javascript: void(0);\" onclick=\"page('alarms');\"><img src=\"images/confs.gif\" style=\"vertical-align:middle;width:16px;height:16px;padding:0;margin:0;margin-top:2px;margin-right:5px;\" border=\"0\" alt=\"Настроить будильники\"> <b>Настроить</b></a></li>";
        if (strlen($_COOKIE['filter_alarms']) == 0 OR $_COOKIE['filter_alarms'] == ":")
            $buttons .= "\n\t\t\t<li class=\"active\"><a href=\"javascript: void(0);\" onclick=\"del_filter('alarms');\">Все</a></li>"; else
            $buttons .= "\n\t\t\t<li><a href=\"javascript: void(0);\" onclick=\"del_filter('alarms');\">Все</a></li>";
        $result = $db->sql_query("SELECT id, title FROM " . $prefix . "alarms WHERE uid='" . $uid . "' ORDER BY title ASC");
        while (list($id, $title) = $db->sql_fetchrow($result)) {
            if (!in_array($id, $alarms_arr))
                $buttons .= "\n\t\t\t<li><input type=\"checkbox\" id=\"alarms_$id\"><a href=\"javascript: void(0);\" onclick=\"add_filter('alarms','$id')\">" . stripslashes($title) . "</a></li>";
            else
                $buttons .= "\n\t\t\t<li class=\"active\"><input type=\"checkbox\" id=\"alarms_$id\" checked><a href=\"javascript: void(0);\" onclick=\"switch_filter('alarms','$id')\">" . stripslashes($title) . "</a></li>";
        }
        $buttons .= "\n\t\t</ul>";
    }
    return $buttons;
}

//Функция вывода нижних кнопок
function bottom_buttons() {
    global $db, $prefix;
    list($uid) = $db->sql_fetchrow($db->sql_query("SELECT uid FROM " . $prefix . "users WHERE login='" . $_COOKIE['ad_login'] . "'"));
    list($numsites, $ttcy, $tpr) = $db->sql_fetchrow($db->sql_query("select count(id), sum(tcy), sum(pr) from " . $prefix . "sites WHERE uid='$uid'"));
    $atcy = round($ttcy / $numsites);
    $apr = round($tpr / $numsites);

    $buttons2 = "<table border=\"0\" width=\"100%\"><tr><td valign=\"middle\"><a class=\"refresh\" href=\"javascript:void(0);\" onclick=\"update_all();\"><span>Обновить все</span></a> <div style=\"float:left;height:28px !important;position:relative;\" id=\"multibutton\"><a class=\"delet\" href=\"javascript:;\"><span>Выделенные</span></a><div id=\"submenu\"><a href=\"javascript:;\" onclick=\"update_selected();\" class=\"upd\"><span>Обновить выделенные</span></a><a href=\"javascript:;\" onclick=\"delete_selected();\" class=\"del\"><span>Удалить выделенные</span></a></div></div></td><td valign=\"middle\" align=\"center\"><big>&Sigma;</big><small> тИЦ:</small> <span class=\"count\">$ttcy</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<big>&mu;</big><small> тИЦ:</small> <span class=\"count\">$atcy</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<big>&Sigma;</big><small> PR:</small> <span class=\"count\">$tpr</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<big>&mu;</big><small> PR:</small> <span class=\"count\">$apr</span><br /><a href=\"http://pr-cy.ru/updates\" target=\"_blank\">Апдейты от pr-cy.ru</a>: " . list_updates() . "</td><td valign=\"middle\"><a class=\"line\"><span>Всего сайтов в панели: <b id=\"totalsites\">$numsites</b></span></a> <a class=\"xls\" href=\"ajax.php?action=export_csv\" target=\"_blank\" title=\"Экспортировать в Excel\" onmouseover=\"$(this).attr('href', 'ajax.php?action=export_csv&panel=' + $('#cur_panel').val());\">Экспортировать в Excel</a></td></tr></table><script type=\"text/javascript\">$('#multibutton').mouseover(function(){\$('#submenu').show();}).mouseout(function(){\$('#submenu').hide();});</script>";
    return $buttons2;
}

//Генерация строк с сайтами
function gen_site_rows($userconfig, $sid=0, $pagenum=1, $panel=1) {
    global $prefix, $db, $col_titles, $static_cols, $cols_links, $birzha, $params;
    list($uid) = $db->sql_fetchrow($db->sql_query("SELECT uid FROM " . $prefix . "users WHERE login='" . $_COOKIE['ad_login'] . "'"));
    $site_rows = "";
    $sites_db_query = "";
    $sites_more_query = "";
    $sites_history_query = "";
    $cols = explode(",", $userconfig['columns'][$panel][1]);
    for ($i = 0; $i < count($cols); $i++) {
        if (substr($cols[$i], 0, 5) != "param") {
            $sites_db_query .= ", s." . $cols[$i] . " AS site_" . $cols[$i];
            if (!in_array($cols[$i], $static_cols))
                $sites_history_query .= ", h." . $cols[$i] . " AS hist_" . $cols[$i];
        } else {
            $sites_more_query .= ", sm." . $cols[$i] . " AS " . $cols[$i];
        }
    }
    if (strlen($sites_more_query) > 0)
        $addmore = " LEFT JOIN ".$prefix."sites_more AS sm ON(s.id=sm.sid)"; else
        $addmore = "";
    $myfilters = "";
    if ($panel != "sites") {
        $dirs_arr = $hosts_arr = $hosts_arr = $regs_arr = $cms_arr = $alarms_arr = array();
        $dirs_arr = @explode(":", preg_replace(array("/^:/", "/:$/"), array("", ""), $_COOKIE['filter_dirs']));
        $hosts_arr = @explode(":", preg_replace(array("/^:/", "/:$/"), array("", ""), $_COOKIE['filter_hosts']));
        $regs_arr = @explode(":", preg_replace(array("/^:/", "/:$/"), array("", ""), $_COOKIE['filter_regs']));
        $cms_arr = @explode(":", preg_replace(array("/^:/", "/:$/"), array("", ""), $_COOKIE['filter_cms']));
        $alarms_arr = @explode(":", preg_replace(array("/^:/", "/:$/"), array("", ""), $_COOKIE['filter_alarms']));
        if (count($dirs_arr) > 0) {
            $sites_dir_query = " AND (";
            for ($i = 0; $i < count($dirs_arr); $i++) {
                if (strlen($dirs_arr[$i]) > 0)
                    $sites_dir_query .= "s.dir=" . $dirs_arr[$i] . " OR ";
            }
            if ($sites_dir_query != " AND (")
                $sites_dir_query = substr($sites_dir_query, 0, -4) . ")"; else
                $sites_dir_query = "";
        }
        if (count($hosts_arr) > 0) {
            $sites_host_query = " AND (";
            for ($i = 0; $i < count($hosts_arr); $i++) {
                if (strlen($hosts_arr[$i]) > 0)
                    $sites_host_query .= "s.host=" . $hosts_arr[$i] . " OR ";
            }
            if ($sites_host_query != " AND (")
                $sites_host_query = substr($sites_host_query, 0, -4) . ")"; else
                $sites_host_query = "";
        }
        if (count($regs_arr) > 0) {
            $sites_reg_query = " AND (";
            for ($i = 0; $i < count($regs_arr); $i++) {
                if (strlen($regs_arr[$i]) > 0)
                    $sites_reg_query .= "s.reg=" . $regs_arr[$i] . " OR ";
            }
            if ($sites_reg_query != " AND (")
                $sites_reg_query = substr($sites_reg_query, 0, -4) . ")"; else
                $sites_reg_query = "";
        }
        if (count($cms_arr) > 0) {
            $sites_cm_query = " AND (";
            for ($i = 0; $i < count($cms_arr); $i++) {
                if (strlen($cms_arr[$i]) > 0)
                    $sites_cm_query .= "s.cm=" . $cms_arr[$i] . " OR ";
            }
            if ($sites_cm_query != " AND (")
                $sites_cm_query = substr($sites_cm_query, 0, -4) . ")"; else
                $sites_cm_query = "";
        }
        if (count($alarms_arr) > 0) {
            $sites_alarm_query = " AND (";
            for ($i = 0; $i < count($alarms_arr); $i++) {
                $adq = "";
                if (strlen($alarms_arr[$i]) > 0) {
                    list($pr_more, $pr_less, $tcy_more, $tcy_less, $yai_more, $yai_less, $gi_more, $gi_less, $yi_more, $yi_less, $ri_more, $ri_less, $ybl_more, $ybl_less, $alexa_more, $alexa_less, $domain_more, $domain_less) = $db->sql_fetchrow($db->sql_query("SELECT pr_more, pr_less, tcy_more, tcy_less, yai_more, yai_less, gi_more, gi_less, yi_more, yi_less, ri_more, ri_less, ybl_more, ybl_less, alexa_more, alexa_less, domain_more, domain_less FROM " . $prefix . "alarms WHERE id='" . $alarms_arr[$i] . "' LIMIT 1"));
                    if ($domain_more != - 1)
                        $domain_more = time() + 86400 * $domain_more;
                    if ($domain_less != - 1)
                        $domain_less = time() + 86400 * $domain_less;
                    if ($pr_more != - 1)
                        $adq .= " AND s.pr>$pr_more";
                    if ($pr_less != - 1)
                        $adq .= " AND s.pr<$pr_less";
                    if ($tcy_more != - 1)
                        $adq .= " AND s.tcy>$tcy_more";
                    if ($tcy_less != - 1)
                        $adq .= " AND s.tcy<$tcy_less";
                    if ($yai_more != - 1)
                        $adq .= " AND s.yai>$yai_more";
                    if ($yai_less != - 1)
                        $adq .= " AND s.yai<$yai_less";
                    if ($gi_more != - 1)
                        $adq .= " AND s.gi>$gi_more";
                    if ($gi_less != - 1)
                        $adq .= " AND s.gi<$gi_less";
                    if ($yi_more != - 1)
                        $adq .= " AND s.yi>$yi_more";
                    if ($yi_less != - 1)
                        $adq .= " AND s.yi<$yi_less";
                    if ($ri_more != - 1)
                        $adq .= " AND s.ri>$ri_more";
                    if ($ri_less != - 1)
                        $adq .= " AND s.ri<$ri_less";
                    if ($ybl_more != - 1)
                        $adq .= " AND s.ybl>$ybl_more";
                    if ($ybl_less != - 1)
                        $adq .= " AND s.ybl<$ybl_less";
                    if ($alexa_more != - 1)
                        $adq .= " AND s.alexarank>$alexa_more";
                    if ($alexa_less != - 1)
                        $adq .= " AND s.alexarank<$alexa_less";
                    if ($domain_more != - 1)
                        $adq .= " AND s.expiry>$domain_more AND s.expiry!='0-00-00'";
                    if ($domain_less != - 1)
                        $adq .= " AND s.expiry<$domain_less AND s.expiry!='0-00-00'";
                }
                if (strlen($adq) > 0)
                    $sites_alarm_query .= " OR (" . substr($adq, 5) . ")";
                else
                    $sites_alarm_query .= "";
            }
            if ($sites_alarm_query != " AND (")
                $sites_alarm_query = " AND (" . substr($sites_alarm_query, 10) . ")"; else
                $sites_alarm_query = "";
        }
        $myfilters = $sites_dir_query . $sites_host_query . $sites_reg_query . $sites_cm_query . $sites_alarm_query;
    }
    $offset = (($pagenum - 1) * $userconfig['rows']);
    if ($sid == 0) {
        list($min_cur_position) = $db->sql_fetchrow($db->sql_query("SELECT position FROM " . $prefix . "sites ORDER BY position ASC LIMIT $offset, 1"));
        $site_rows .= "<input type=\"hidden\" id=\"min_cur_position\" value=\"$min_cur_position\">";
        $request = "SELECT SQL_CALC_FOUND_ROWS s.url, s.id, s.lhid" . $sites_db_query . ", s.position, h.sid, h.id AS hid" . $sites_history_query . $sites_more_query . " FROM " . $prefix . "sites AS s LEFT JOIN " . $prefix . "history AS h ON (s.id=h.sid)" . $addmore . " WHERE s.uid='$uid' AND h.id=s.lhid" . $myfilters . " ORDER BY s.position ASC LIMIT $offset, " . $userconfig['rows'];
    } else {
        $request = "SELECT SQL_CALC_FOUND_ROWS s.url, s.id, s.lhid" . $sites_db_query . ", s.position, h.sid, h.id AS hid" . $sites_history_query . $sites_more_query . " FROM " . $prefix . "sites AS s LEFT JOIN " . $prefix . "history AS h ON (s.id=h.sid)" . $addmore . " WHERE s.uid='$uid' AND h.id=s.lhid AND s.id='$sid' LIMIT 1";
    }
    $dir_result = $db->sql_query("SELECT id, title FROM " . $prefix . "dirs WHERE uid='$uid' OR uid='0' ORDER BY title ASC");
    while (list($id, $title) = $db->sql_fetchrow($dir_result)) {
        $dirs[$id] = $title;
    }
    $dirs[0] = "n/a";
    $host_result = $db->sql_query("SELECT id, title, descript FROM " . $prefix . "hosts WHERE uid='$uid' OR uid='0' ORDER BY title ASC");
    while (list($id, $title, $descript) = $db->sql_fetchrow($host_result)) {
        $data = explode("||", $descript);
        $hosts[$id] = array($title, $data[0], $data[1]);
    }
    $hosts[0] = array("n/a", "", "");
    $regs_result = $db->sql_query("SELECT id, title, descript FROM " . $prefix . "regs WHERE uid='$uid' OR uid='0' ORDER BY title ASC");
    while (list($id, $title, $descript) = $db->sql_fetchrow($regs_result)) {
        $data = explode("||", $descript);
        $regs[$id] = array($title, $data[0], $data[1]);
    }
    $regs[0] = array("n/a", "", "");
    $cms_result = $db->sql_query("SELECT id, title FROM " . $prefix . "cms WHERE uid='$uid' OR uid='0' ORDER BY title ASC");
    while (list($id, $title) = $db->sql_fetchrow($cms_result)) {
        $cmss[$id] = $title;
    }
    $cmss[0] = "n/a";
    $res = $db->sql_query($request);
    list($total) = $db->sql_fetchrow($db->sql_query("SELECT FOUND_ROWS()"));
    $pages = genPagination($total, $userconfig['rows'], $pagenum, "$panel");
    while ($row = $db->sql_fetchrow($res)) {

        $site_rows .= "<tr id=\"row_" . $row['id'] . "\" title=\"" . $row['url'] . "\" onclick=\"select_toggle(this);\">";
        if ($panel == "sites") {
            list($min_pos, $max_pos) = $db->sql_fetchrow($db->sql_query("SELECT MIN(position), MAX(position) FROM " . $prefix . "sites"));
            $poslinks = "<div class=\"poslinks\">";
            $poslinks .= ( $row['position'] > $min_pos) ? "<img src=\"images/spacer.gif\" class=\"arrup\" alt=\"\" title=\"На одну позицию выше\" onclick=\"position('up','" . $row['id'] . "');\"> " : "";
            $poslinks .= ( $row['position'] < $max_pos) ? "<a href=\"javascript:;\" onclick=\"position('down','" . $row['id'] . "');\"><img src=\"images/spacer.gif\" class=\"arrdn\" alt=\"\" title=\"На одну позицию ниже\"></a> " : "";
            $poslinks .= "</div>";
            $site_rows .= "<td class=\"dragHandle\"></td><td>$poslinks</td>";
        }
        $site_rows .= "<td><input class=\"myid\" type=\"hidden\" name=\"" . $row['url'] . "\" value=\"" . $row['id'] . "\"><img src=\"http://favicon.yandex.net/favicon/" . $row['url'] . "\" title=\"Favicon\" alt=\"Fi\"></td><td nowrap=\"nowrap\" style=\"text-align: left !important;\" class=\"myurl\"><a href=\"http://" . $row['url'] . "\" target=\"_blank\" class=\"goto\">" . $row['url'] . "</a><span class=\"options\"><a href=\"http://" . $row['url'] . "\" target=\"_blank\"><img src=\"images/spacer.gif\" alt=\"Перейти на сайт\" title=\"Перейти на сайт\" class=\"external\"></a> <a href=\"ajax.php?action=stats&sites=" . $row['id'] . "\" rel=\"fancybox\"><img src=\"images/spacer.gif\" alt=\"Статистика\" title=\"Статистика\" class=\"stats\"></a> <img src=\"images/spacer.gif\" alt=\"Обновить\" title=\"Обновить\" class=\"reload\" onclick=\"site_update('" . $row['id'] . "', '" . $row['url'] . "')\"> <a href=\"ajax.php?action=edit_site_form&id=" . $row['id'] . "\" rel=\"fancybox\"><img src=\"images/spacer.gif\" alt=\"Изменить\" title=\"Изменить\" class=\"edit\"></a> <img src=\"images/spacer.gif\" alt=\"Удалить\" title=\"Удалить\" class=\"delete\" onclick=\"site_delete('" . $row['id'] . "', '" . $row['url'] . "')\"></span></td>";
        for ($i = 0; $i < count($cols); $i++) {
            if (substr($cols[$i], 0, 5) != "param") {
                $val = $row['site_' . $cols[$i]];
                if ($val == "-1" OR strlen($val) == 0 OR $val == "0-00-00")
                    $val = "n/a";
                if ($cols[$i] == "yaca" OR $cols[$i] == "dmoz") {
                    $val = str_replace(array("0", "1"), array("<img class=\"inact\" src=\"images/spacer.gif\" alt=\"Нет\" title=\"Нет\">", "<img src=\"images/spacer.gif\" alt=\"Есть\" title=\"Есть\" class=\"act\">"), $val);
                }
                if (!in_array($cols[$i], $static_cols)) {
                    if ($row['site_' . $cols[$i]] > $row['hist_' . $cols[$i]])
                        $val .= "<span class=\"pluss\">+" . ($row['site_' . $cols[$i]] - $row['hist_' . $cols[$i]]) . "</span>"; elseif ($row['site_' . $cols[$i]] < $row['hist_' . $cols[$i]])
                        $val .= "<span class=\"minuss\">-" . ($row['hist_' . $cols[$i]] - $row['site_' . $cols[$i]]) . "</span>";
                }
                if ($cols[$i] == 'li_hits' OR $cols[$i] == 'li_hosts')
                    $value = "<a href=\"" . str_replace(array("[url]", "[ip]"), array($row['url'], $row['site_ip']), $col_titles[$cols[$i]][2]) . "\" target=\"_blank\" class=\"linet\" rel=\"http://counter.yadro.ru/logo;" . $row['url'] . "?29.1\">" . $val . "</a>"; else
                    $value = (in_array($cols[$i], $cols_links)) ? "<a href=\"" . str_replace(array("[url]", "[ip]"), array($row['url'], $row['site_ip']), $col_titles[$cols[$i]][2]) . "\" target=\"_blank\">" . $val . "</a>" : $val;
                if ($cols[$i] == "yai")
                    $value = "<a href=\"" . str_replace("[yaurl]", yaquery($row['url']), $col_titles[$cols[$i]][2]) . "\" target=\"_blank\">" . $val . "</a>";
                $addrow = "<td>$value</td>";
                if (in_array($cols[$i], $birzha))
                    $addrow = "<td id=\"" . $cols[$i] . "_" . $row['id'] . "\" onclick=\"javascript: change('" . $row['id'] . "', '" . $cols[$i] . "')\">" . str_replace(array("0", "1"), array("<img class=\"inact\" src=\"images/spacer.gif\" alt=\"Нет\" title=\"Нет\">", "<img src=\"images/spacer.gif\" alt=\"Есть\" title=\"Есть\" class=\"act\">"), $val) . "</td>";
                if ($cols[$i] == "server") {
                    if ($val == "200")
                        $addrow = "<td class=\"ok\">200</td>";
                    elseif ($val == "404" OR $val == "500")
                        $addrow = "<td class=\"error\">" . $val . "</td>";
                    else
                        $addrow = "<td class=\"sim\">" . $val . "</td>";
                }
                if ($cols[$i] == "expiry") {
                    $value = ($val == "n/a") ? "n/a" : date("d.m.Y", $val);
                    if ($val < (time() + 8460000) AND $val != "n/a")
                        $expclass = " class=\"error\""; else
                        $expclass = "";
                    $addrow = "<td$expclass>" . $value . "</td>";
                }
                if ($cols[$i] == "registration" OR $cols[$i] == "last_check") {
                    $value = ($val == "n/a") ? "n/a" : date("d.m.Y", $val);
                    $addrow = "<td>" . $value . "</td>";
                }
                if ($cols[$i] == "dir")
                    $addrow = "<td>" . $dirs[$val] . "</td>";
                if ($cols[$i] == "host") {
                    $bilhref = (strlen($hosts[$val][1]) > 0 AND $hosts[$val][1] != "http://") ? "<a href=\"" . $hosts[$val][1] . "\" target=\"_blank\"><img src=\"images/spacer.gif\" border=\"0\" alt=\"\" title=\"Биллинг панель\" class=\"billing\"></a>" : "";
                    $cphref = (strlen($hosts[$val][2]) > 0 AND $hosts[$val][2] != "http://") ? "<a href=\"" . $hosts[$val][2] . "\" target=\"_blank\"><img src=\"images/spacer.gif\" border=\"0\" alt=\"\" title=\"Контрольная панель\" class=\"cp\"></a>" : "";
                    $addrow = "<td nowrap>" . $bilhref . " " . $hosts[$val][0] . " " . $cphref . "</td>";
                }
                if ($cols[$i] == "cms")
                    $addrow = "<td>" . $cmss[$val] . "</td>";
                if ($cols[$i] == "registrator") {
                    $bilhref = (strlen($regs[$val][1]) > 0 AND $regs[$val][1] != "http://") ? "<a href=\"" . $regs[$val][1] . "\" target=\"_blank\"><img src=\"images/spacer.gif\" border=\"0\" alt=\"\" title=\"Биллинг панель\" class=\"billing\"></a>" : "";
                    $cphref = (strlen($regs[$val][2]) > 0 AND $regs[$val][2] != "http://") ? "<a href=\"" . $regs[$val][2] . "\" target=\"_blank\"><img src=\"images/spacer.gif\" border=\"0\" alt=\"\" title=\"Контрольная панель\" class=\"cp\"></a>" : "";
                    $addrow = "<td nowrap>" . $bilhref . " " . $regs[$val][0] . " " . $cphref . "</td>";
                }
            } else {
                $val = (strlen($row[$cols[$i]]) == 0 OR $row[$cols[$i]] == "-1") ? "n/a" : $row[$cols[$i]];
                if ($params[$cols[$i]][1] == 'yesno')
                    $addrow = "<td id=\"" . $cols[$i] . "_" . $row['id'] . "\" onclick=\"javascript: change('" . $row['id'] . "', '" . $cols[$i] . "')\">" . str_replace(array("0", "1"), array("<img class=\"inact\" src=\"images/spacer.gif\" alt=\"Нет\" title=\"Нет\">", "<img src=\"images/spacer.gif\" alt=\"Есть\" title=\"Есть\" class=\"act\">"), $val) . "</td>";
                else
                    $addrow = "<td>" . $val . "</td>";
            }
            $site_rows .= $addrow;
        }
        $site_rows .= "</tr>";
    }
    return array($site_rows, $pages);
}

//Генерация строк с сайтами для Excel
function gen_site_rows_csv($userconfig, $panel=1) {
    global $prefix, $db, $col_titles, $static_cols, $cols_links, $birzha;
    list($uid) = $db->sql_fetchrow($db->sql_query("SELECT uid FROM " . $prefix . "users WHERE login='" . $_COOKIE['ad_login'] . "'"));
    $site_rows = array();
    $sites_db_query = "";
    $cols = explode(",", $userconfig['columns'][$panel][1]);
    for ($i = 0; $i < count($cols); $i++) {
        if (substr($cols[$i], 0, 5) != "param") {
            $sites_db_query .= ", s." . $cols[$i] . " AS site_" . $cols[$i];
        }
    }
    $myfilters = "";
    if ($panel != "sites") {
        $dirs_arr = $hosts_arr = $hosts_arr = $regs_arr = $cms_arr = $alarms_arr = array();
        $dirs_arr = @explode(":", preg_replace(array("/^:/", "/:$/"), array("", ""), $_COOKIE['filter_dirs']));
        $hosts_arr = @explode(":", preg_replace(array("/^:/", "/:$/"), array("", ""), $_COOKIE['filter_hosts']));
        $regs_arr = @explode(":", preg_replace(array("/^:/", "/:$/"), array("", ""), $_COOKIE['filter_regs']));
        $cms_arr = @explode(":", preg_replace(array("/^:/", "/:$/"), array("", ""), $_COOKIE['filter_cms']));
        $alarms_arr = @explode(":", preg_replace(array("/^:/", "/:$/"), array("", ""), $_COOKIE['filter_alarms']));
        if (count($dirs_arr) > 0) {
            $sites_dir_query = " AND (";
            for ($i = 0; $i < count($dirs_arr); $i++) {
                if (strlen($dirs_arr[$i]) > 0)
                    $sites_dir_query .= "s.dir=" . $dirs_arr[$i] . " OR ";
            }
            if ($sites_dir_query != " AND (")
                $sites_dir_query = substr($sites_dir_query, 0, -4) . ")"; else
                $sites_dir_query = "";
        }
        if (count($hosts_arr) > 0) {
            $sites_host_query = " AND (";
            for ($i = 0; $i < count($hosts_arr); $i++) {
                if (strlen($hosts_arr[$i]) > 0)
                    $sites_host_query .= "s.host=" . $hosts_arr[$i] . " OR ";
            }
            if ($sites_host_query != " AND (")
                $sites_host_query = substr($sites_host_query, 0, -4) . ")"; else
                $sites_host_query = "";
        }
        if (count($regs_arr) > 0) {
            $sites_reg_query = " AND (";
            for ($i = 0; $i < count($regs_arr); $i++) {
                if (strlen($regs_arr[$i]) > 0)
                    $sites_reg_query .= "s.reg=" . $regs_arr[$i] . " OR ";
            }
            if ($sites_reg_query != " AND (")
                $sites_reg_query = substr($sites_reg_query, 0, -4) . ")"; else
                $sites_reg_query = "";
        }
        if (count($cms_arr) > 0) {
            $sites_cm_query = " AND (";
            for ($i = 0; $i < count($cms_arr); $i++) {
                if (strlen($cms_arr[$i]) > 0)
                    $sites_cm_query .= "s.cm=" . $cms_arr[$i] . " OR ";
            }
            if ($sites_cm_query != " AND (")
                $sites_cm_query = substr($sites_cm_query, 0, -4) . ")"; else
                $sites_cm_query = "";
        }
        if (count($alarms_arr) > 0) {
            $sites_alarm_query = " AND (";
            for ($i = 0; $i < count($alarms_arr); $i++) {
                if (strlen($alarms_arr[$i]) > 0) {
                    list($pr_more, $pr_less, $tcy_more, $tcy_less, $yai_more, $yai_less, $gi_more, $gi_less, $yi_more, $yi_less, $ri_more, $ri_less, $ybl_more, $ybl_less, $alexa_more, $alexa_less, $domain_more, $domain_less) = $db->sql_fetchrow($db->sql_query("SELECT pr_more, pr_less, tcy_more, tcy_less, yai_more, yai_less, gi_more, gi_less, yi_more, yi_less, ri_more, ri_less, ybl_more, ybl_less, alexa_more, alexa_less, domain_more, domain_less FROM " . $prefix . "alarms WHERE id='" . $alarms_arr[$i] . "' LIMIT 1"));
                    $adq = "";
                    if ($domain_more != - 1)
                        $domain_more = time() + 86400 * $domain_more;
                    if ($domain_less != - 1)
                        $domain_less = time() + 86400 * $domain_less;
                    if ($pr_more != - 1)
                        $adq .= " AND s.pr>$pr_more";
                    if ($pr_less != - 1)
                        $adq .= " AND s.pr<$pr_less";
                    if ($tcy_more != - 1)
                        $adq .= " AND s.tcy>$tcy_more";
                    if ($tcy_less != - 1)
                        $adq .= " AND s.tcy<$tcy_less";
                    if ($yai_more != - 1)
                        $adq .= " AND s.yai>$yai_more";
                    if ($yai_less != - 1)
                        $adq .= " AND s.yai<$yai_less";
                    if ($gi_more != - 1)
                        $adq .= " AND s.gi>$gi_more";
                    if ($gi_less != - 1)
                        $adq .= " AND s.gi<$gi_less";
                    if ($yi_more != - 1)
                        $adq .= " AND s.yi>$yi_more";
                    if ($yi_less != - 1)
                        $adq .= " AND s.yi<$yi_less";
                    if ($ri_more != - 1)
                        $adq .= " AND s.ri>$ri_more";
                    if ($ri_less != - 1)
                        $adq .= " AND s.ri<$ri_less";
                    if ($ybl_more != - 1)
                        $adq .= " AND s.ybl>$ybl_more";
                    if ($ybl_less != - 1)
                        $adq .= " AND s.ybl<$ybl_less";
                    if ($alexa_more != - 1)
                        $adq .= " AND s.alexarank>$alexa_more";
                    if ($alexa_less != - 1)
                        $adq .= " AND s.alexarank<$alexa_less";
                    if ($domain_more != - 1)
                        $adq .= " AND s.expiry>$domain_more AND s.expiry!='0-00-00'";
                    if ($domain_less != - 1)
                        $adq .= " AND s.expiry<$domain_less AND s.expiry!='0-00-00'";
                }
                if (strlen($adq) > 0)
                    $sites_alarm_query .= " OR (" . substr($adq, 5) . ")"; else
                    $sites_alarm_query .= "";
            }
            if ($sites_alarm_query != " AND (")
                $sites_alarm_query = " AND (" . substr($sites_alarm_query, 10) . ")"; else
                $sites_alarm_query = "";
        }
        $myfilters = $sites_dir_query . $sites_host_query . $sites_reg_query . $sites_cm_query . $sites_alarm_query;
    }

    $request = "SELECT SQL_CALC_FOUND_ROWS s.url, s.id, s.lhid" . $sites_db_query . ", s.position FROM " . $prefix . "sites AS s WHERE s.uid='$uid' " . $myfilters . " ORDER BY s.position ASC";

    $dir_result = $db->sql_query("SELECT id, title FROM " . $prefix . "dirs WHERE uid='$uid' OR uid='0' ORDER BY title ASC");
    while (list($id, $title) = $db->sql_fetchrow($dir_result)) {
        $dirs[$id] = $title;
    }
    $dirs[0] = "n/a";
    $host_result = $db->sql_query("SELECT id, title, descript FROM " . $prefix . "hosts WHERE uid='$uid' OR uid='0' ORDER BY title ASC");
    while (list($id, $title, $descript) = $db->sql_fetchrow($host_result)) {
        $data = explode("||", $descript);
        $hosts[$id] = array($title, $data[0], $data[1]);
    }
    $hosts[0] = array("n/a", "", "");
    $regs_result = $db->sql_query("SELECT id, title, descript FROM " . $prefix . "regs WHERE uid='$uid' OR uid='0' ORDER BY title ASC");
    while (list($id, $title, $descript) = $db->sql_fetchrow($regs_result)) {
        $data = explode("||", $descript);
        $regs[$id] = array($title, $data[0], $data[1]);
    }
    $regs[0] = array("n/a", "", "");
    $cms_result = $db->sql_query("SELECT id, title FROM " . $prefix . "cms WHERE uid='$uid' OR uid='0' ORDER BY title ASC");
    while (list($id, $title) = $db->sql_fetchrow($cms_result)) {
        $cmss[$id] = $title;
    }
    $cmss[0] = "n/a";
    $res = $db->sql_query($request);
    list($total) = $db->sql_fetchrow($db->sql_query("SELECT FOUND_ROWS()"));
    while ($row = $db->sql_fetchrow($res)) {
        $siteinfo = array();
        $siteinfo[] = $row['url'];
        for ($i = 0; $i < count($cols); $i++) {
            if (substr($cols[$i], 0, 5) != "param") {
                $val = $row['site_' . $cols[$i]];
                if ($val == "-1" OR strlen($val) == 0 OR $val == "0-00-00")
                    $val = "n/a";
                if ($cols[$i] == "yaca" OR $cols[$i] == "dmoz") {
                    $val = str_replace(array("0", "1"), array("-", "+"), $val);
                }
                $value = $val;
                $addrow = $value;
                if (in_array($cols[$i], $birzha))
                    $addrow = str_replace(array("0", "1"), array("-", "+"), $val);
                if ($cols[$i] == "expiry") {
                    $value = ($val == "n/a") ? "n/a" : date("d.m.Y", $val);
                    $addrow = $value;
                }
                if ($cols[$i] == "registration" OR $cols[$i] == "last_check") {
                    $value = ($val == "n/a") ? "n/a" : date("d.m.Y", $val);
                    $addrow = $value;
                }
                if ($cols[$i] == "dir")
                    $addrow = $dirs[$val];
                if ($cols[$i] == "host") {
                    $addrow = $hosts[$val][0];
                }
                if ($cols[$i] == "cms")
                    $addrow = $cmss[$val];
                if ($cols[$i] == "registrator") {
                    $addrow = $regs[$val][0];
                }
            } else {
                $val = (strlen($row[$cols[$i]]) == 0 OR $row[$cols[$i]] == "-1") ? "n/a" : $row[$cols[$i]];
                if ($params[$cols[$i]][1] == 'yesno')
                    $addrow = str_replace(array("0", "1"), array("-", "+"), $val);
                else
                    $addrow = $val;
            }
            $siteinfo[] = (is_int($addrow)) ? $addrow : "$addrow";
        }
        $site_rows[] = $siteinfo;
    }
    return $site_rows;
}

//Обновление информации о сайте
function site_update($userconfig, $sid, $panel=1, $relcolumn="") {
    global $prefix, $db, $col_titles, $static_cols;
    $s_row = $db->sql_fetchrow($db->sql_query("SELECT * FROM ".$prefix."sites WHERE id='$sid' LIMIT 1"));
    $h_row = $db->sql_fetchrow($db->sql_query("SELECT * FROM ".$prefix."history WHERE sid='$sid' ORDER BY thedate DESC LIMIT 1"));
    $cols = explode(",", $userconfig['tocheck']);
    $url = $s_row['url'];
    $updater = array();
    if ($relcolumn == "") {
        if (in_array("yai", $cols)) {
            $updater['yai'] = YandexIndex($url);
            if (strpos($updater['yai'], "captcha") !== false)
                return("captcha");
            if (strpos($updater['yai'], "needip") !== false)
                return($updater['yai']);
        }
        if (in_array("registration", $cols) OR in_array("expiry", $cols)) {
            $domaininfo = DomainInfo($url);
            $updater['registration'] = $domaininfo[1];
            $updater['age'] = dateDiff($domaininfo[1], time());
            $updater['expiry'] = $domaininfo[0];
        }
        if (in_array("pr", $cols))
            $updater['pr'] = getpr($url);
        if (in_array("tcy", $cols))
            $updater['tcy'] = getTIC($url);
        if (in_array("gi", $cols))
            $updater['gi'] = GoogleIndex($url);
        if (in_array("yi", $cols))
            $updater['yi'] = YahooIndex($url);
        if (in_array("ybl", $cols))
            $updater['ybl'] = YahooBacks($url);
        if (in_array("ri", $cols))
            $updater['ri'] = RamblerIndex($url);
        if (in_array("feedcount", $cols))
            $updater['feedcount'] = getFBReadersCnt($s_row['feeduri']);
        if (in_array("server", $cols))
            $updater['server'] = serverStat($url);
        if (in_array("dmoz", $cols))
            $updater['dmoz'] = getDMOZ($url);
        if (in_array("yaca", $cols))
            $updater['yaca'] = getYACA($url);
        if (in_array("alexarank", $cols))
            $updater['alexarank'] = alexa_rank($url);
        if (in_array("li_hits", $cols))
            $updater['li_hits'] = li_hits($url);
        if (in_array("li_hosts", $cols))
            $updater['li_hosts'] = li_hosts($url);
        $sql = "";
        foreach ($updater as $key => $val) {
            $sql .= ", $key='$val'";
        }
        $db->sql_query("INSERT INTO " . $prefix . "history (sid, thedate, pr, tcy, yai, gi, yi, ri, ybl, alexarank, feedcount, li_hits, li_hosts) VALUES ('$sid', '" . time() . "', '" . $updater['pr'] . "', '" . $updater['tcy'] . "', '" . $updater['yai'] . "', '" . $updater['gi'] . "', '" . $updater['yi'] . "', '" . $updater['ri'] . "', '" . $updater['ybl'] . "', '" . $updater['alexarank'] . "', '" . $updater['feedcount'] . "', '" . $updater['li_hits'] . "', '" . $updater['li_hosts'] . "')");
        list($lhid) = $db->sql_fetchrow($db->sql_query("SELECT id FROM " . $prefix . "history WHERE sid='$sid' ORDER BY id DESC LIMIT 1,1"));
//		$db->sql_query("UPDATE ".$prefix."sites SET lhid='".$lhid."' WHERE id='$sid'");

        $db->sql_query("UPDATE " . $prefix . "sites SET lhid='" . $lhid . "', last_check='" . time() . "', ip='" . gethostbyname($url) . "'" . $sql . " WHERE id='$sid'");
    } else {
        $db->sql_query("INSERT INTO " . $prefix . "history (sid, thedate, pr, tcy, yai, gi, yi, ri, ybl, alexarank, feedcount, li_hits, li_hosts) (SELECT sid, thedate, pr, tcy, yai, gi, yi, ri, ybl, alexarank, feedcount, li_hits, li_hosts FROM " . $prefix . "history WHERE sid='$sid' ORDER BY id DESC LIMIT 1)");
        $insid = $db->sql_nextid();
        list($lhid) = $db->sql_fetchrow($db->sql_query("SELECT id FROM " . $prefix . "history WHERE sid='$sid' ORDER BY id DESC LIMIT 1,1"));
        if ($relcolumn == "yai") {
            $updater['yai'] = YandexIndex($url);
            if (strpos($updater['yai'], "captcha") !== false)
                return("captcha");
            if (strpos($updater['yai'], "needip") !== false)
                return($updater['yai']);
        }
        if ($relcolumn == "registration" OR $relcolumn == "expiry") {
            $domaininfo = DomainInfo($url);
            $updater['registration'] = $domaininfo[1];
            $updater['age'] = ($updater['registration'] != "") ? dateDiff($domaininfo[1], time()) : "";
            $updater['expiry'] = $domaininfo[0];
        }
        if ($relcolumn == "pr")
            $updater['pr'] = getpr($url);
        if ($relcolumn == "tcy")
            $updater['tcy'] = getTIC($url);
        if ($relcolumn == "gi")
            $updater['gi'] = GoogleIndex($url);
        if ($relcolumn == "yi")
            $updater['yi'] = YahooIndex($url);
        if ($relcolumn == "ybl")
            $updater['ybl'] = YahooBacks($url);
        if ($relcolumn == "ri")
            $updater['ri'] = RamblerIndex($url);
        if ($relcolumn == "feedcount")
            $updater['feedcount'] = getFBReadersCnt($s_row['feeduri']);
        if ($relcolumn == "server")
            $updater['server'] = serverStat($url);
        if ($relcolumn == "dmoz")
            $updater['dmoz'] = getDMOZ($url);
        if ($relcolumn == "yaca")
            $updater['yaca'] = getYACA($url);
        if ($relcolumn == "alexarank")
            $updater['alexarank'] = alexa_rank($url);
        if ($relcolumn == "li_hits")
            $updater['li_hits'] = li_hits($url);
        if ($relcolumn == "li_hosts")
            $updater['li_hosts'] = li_hosts($url);
        $sql = "";
        foreach ($updater as $key => $val) {
            $sql .= ", $key='$val'";
        }
        if (!in_array($relcolumn, $static_cols))
            $hsql = $sql; else
            $hsql = "";
        $db->sql_query("UPDATE " . $prefix . "sites SET lhid='" . $lhid . "', last_check='" . time() . "', ip='" . gethostbyname($url) . "'" . $sql . " WHERE id='$sid'");
        $db->sql_query("UPDATE " . $prefix . "history SET thedate='" . time() . "'$hsql WHERE id='$insid'");
    }
    return gen_site_rows($userconfig, $sid, 0, $panel);
}

//Запись в лог
function addlog($txt) {
    $fp = fopen("writing/log.txt", "a");
    fwrite($fp, date("d/m/Y  H:i:s").'    '.$txt . "\n");
    fclose($fp);
}

//Получение ключа от индекса в массиве
function KeyName($myArray, $pos) {
    // $pos--;
    /* uncomment the above line if you */
    /* prefer position to start from 1 */

    if (($pos < 0) || ( $pos >= count($myArray) ))
        return "NULL";  // set this any way you like

        reset($myArray);
    for ($i = 0; $i < $pos; $i++)
        next($myArray);

    return key($myArray);
}

//Список файлов в папке
function getDirectoryList($directory) {
    $results = array();
    $handler = opendir($directory);
    while ($file = readdir($handler)) {
        if ($file != "." && $file != ".." && $file != ".htaccess") {
            $results[] = $file;
        }
    }
    closedir($handler);
    return $results;
}

//Отправка почты
function mail_send($email, $smail, $subject, $message, $id="", $pr="") {
    include("include/class.phpmailer.php");
    $email = $email;
    $smail = $smail;
    $mail = new PHPMailer();
    $mail->Mailer = "sendmail";
    $mail->ContentType = "text/html";
    $mail->CharSet = "utf-8";
    $mail->From = $smail;
    $mail->FromName = $smail;
    $mail->AddAddress($email, $email);
    $mail->AddReplyTo($smail, $smail);
    $mail->WordWrap = 200;                                 // set word wrap to 50 characters
    $mail->Subject = "=?utf-8?b?" . base64_encode($subject) . "?=";
    $mail->Body = $message;
    if (!$mail->Send()) {
        echo "E-mail Not Sent";
    }
    $mail->ClearAddresses();
    return true;
}

?>