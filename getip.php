<?php

$content = file_get_contents("http://2ip.ru/");
preg_match("/<big>(.*)<\/big>/i", $content, $search);
if(strlen($search[1])>0) {
    $fp = fopen("writing/ip.txt","w");
    fwrite($fp, $search[1]);
    fclose($fp);
}
echo $search[1];
?>