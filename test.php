<?php
require_once 'parser.php';

$file = <<<LOGFILE
OpenVPN CLIENT LIST
Updated,Sun Jul 24 20:59:36 2016
Common Name,Real Address,Bytes Received,Bytes Sent,Connected Since
piii,75.162.210.48:50048,668679,302579,Sun Jul 24 20:13:33 2016
giraffe,45.56.27.101:58842,1886805,6567463,Sun Jul 24 13:09:27 2016
fox,45.56.27.101:49228,162895,197968,Sun Jul 24 12:37:08 2016
aws,54.186.235.15:42003,193113,196128,Sun Jul 24 12:37:07 2016
zebra,75.162.210.48:49716,6879386,1686581,Sun Jul 24 12:37:08 2016
ROUTING TABLE
Virtual Address,Common Name,Real Address,Last Ref
192.168.250.8,aws,54.186.235.15:42003,Sun Jul 24 13:42:16 2016
192.168.250.12,zebra,75.162.210.48:49716,Sun Jul 24 20:34:15 2016
192.168.250.16,fox,45.56.27.101:49228,Sun Jul 24 13:42:55 2016
192.168.250.2,piii,75.162.210.48:50048,Sun Jul 24 20:54:37 2016
192.168.250.4,giraffe,45.56.27.101:58842,Sun Jul 24 20:57:55 2016
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