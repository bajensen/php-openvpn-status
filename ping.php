<?php
include('vendor/autoload.php');

$ip = $_GET['ip'];

if (preg_match('/192\.168\.250\.[0-9]{0,3}/', $ip)) {
    $pinger = new \JJG\Ping($ip);
    echo $pinger->ping();
}
else {
    echo 'ERR';
}