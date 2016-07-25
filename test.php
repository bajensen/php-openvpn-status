<?php
require_once 'parser.php';

$file = <<<LOGFILE
OpenVPN CLIENT LIST
Updated,Mon Jul 25 12:11:14 2016
Common Name,Real Address,Bytes Received,Bytes Sent,Connected Since
fox,45.56.27.101:56845,180385,203781,Mon Jul 25 03:19:10 2016
piii,75.162.210.48:50048,1018035,660587,Sun Jul 24 20:13:33 2016
giraffe,45.56.27.101:49580,11704,21964,Mon Jul 25 12:10:59 2016
aws,54.186.235.15:42003,535713,543220,Sun Jul 24 12:37:07 2016
zebra,75.162.210.48:49716,75198601,7380369,Sun Jul 24 12:37:08 2016
ROUTING TABLE
Virtual Address,Common Name,Real Address,Last Ref
192.168.250.8,aws,54.186.235.15:42003,Sun Jul 24 13:42:16 2016
192.168.250.12,zebra,75.162.210.48:49716,Mon Jul 25 12:10:51 2016
192.168.250.16,fox,45.56.27.101:56845,Mon Jul 25 03:19:10 2016
192.168.250.2,piii,75.162.210.48:50048,Sun Jul 24 20:54:37 2016
192.168.250.4,giraffe,45.56.27.101:49580,Mon Jul 25 12:11:13 2016
GLOBAL STATS
Max bcast/mcast queue length,1
END
LOGFILE;

$status = new OpenVPNStatus();
$status->loadFromString($file);
$status->parse();
print_r($status->getClients());

foreach($status->getClients() as $client) {
    echo $client->name . PHP_EOL;
    print_r($client->getReadableArray());
}