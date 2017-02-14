<?php
require 'vendor/autoload.php';

if (file_exists('hook.php')) {
    require 'hook.php';
}

$config = require __DIR__ . '/config/config.default.php';

$localConfig = include __DIR__ . '/config/config.local.php';

if ($localConfig) {
    $config = array_replace_recursive($config, $localConfig);
}

\OpenVPN\Config::setConfig($config);