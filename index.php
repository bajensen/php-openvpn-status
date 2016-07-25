<?php
require_once 'parser.php';

$status = new OpenVPNStatus();
$status->loadFromFile('/etc/openvpn/openvpn-status.log');
$status->parse();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>VPN Status</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
</head>
<body>
<div class="container">
    <h1>VPN Status</h1>
    <h4>Updated <?= date(DATE_RFC1036, $status->getUpdated()); ?></h4>

    <div class="row">
        <?php /** @var OpenVPNClient $client */
        foreach ($status->getClients() as $client) : ?>
            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
                <div class="panel panel-default">
                    <div class="panel-heading"><?= $client->name ?></div>
                    <ul class="list-group">
                        <?php foreach ($client->getReadableArray() as $key => $value) : ?>
                            <li class="list-group-item">
                                <h4 class="list-group-item-heading"><?= $key ?>:</h4>
                                <p class="list-group-item-text"><?= $value ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>
</body>
</html>