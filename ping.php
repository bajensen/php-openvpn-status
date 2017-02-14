<?php
include(__DIR__ . '/bootstrap.php');

$ip = $_GET['ip'];

if (preg_match(\OpenVPN\Config::getValue('allowed_ping_regex'), $ip)) {
    $pinger = new \JJG\Ping($ip);
    echo $pinger->ping();
}
else {
    echo 'ERR';
}