<?php
require 'vendor/autoload.php';

if (file_exists('hook.php')) {
    require 'hook.php';
}

$config = include __DIR__ . '/config/config.php';

if ($config === null) {
    die('Configuration file missing.');
}

\OpenVPN\Config::setConfig($config);