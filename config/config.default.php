<?php
return [
    'log_file' => '/etc/openvpn/openvpn-status.log',
    'reload_freq' => 60 * 1000, // In ms
    'allowed_ping_regex' => '/192\.168\.250\.[0-9]{1,3}/',
    'timezone' => [
        'src' => 'America/New_York',
        'dst' => 'America/Denver'
    ]
];