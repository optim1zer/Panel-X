<?php

$ipInfo = file_get_contents('http://ipinfo.io/json');
$ipInfo = json_decode($ipInfo);
if($ipInfo && $ipInfo->ip){
    file_put_contents('writing/ip.txt', $ipInfo->ip);
}
echo $ipInfo->ip;

?>