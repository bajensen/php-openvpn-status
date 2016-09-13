<?php
require_once 'parser.php';

$status = new OpenVPNStatus();
$status->loadFromFile('/etc/openvpn/openvpn-status.log');
$status->parse();


$updated = clone($status->getUpdated());
$updated->setTimezone(new DateTimeZone('America/Denver'));
$updated = $updated->format(DATE_RFC1036);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>VPN Status</title>
    <style>
        .latency {
            display: none;
        }
    </style>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"/>
    <script src="https://code.jquery.com/jquery-3.1.0.min.js"></script>
</head>
<body>
<div class="container">
    <h1>VPN Status</h1>
    <h4>Updated <?= $updated ?></h4>
    
    <div class="row">
        <?php /** @var OpenVPNClient $client */
        foreach ($status->getClients() as $client) : ?>
            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?= $client->name ?>
                        <span class="badge latency pull-right" data-ip="<?= $client->vpnIp; ?>">--</span>
                    </div>
                    <ul class="list-group">
                        <?php foreach ($client->getReadableArray() as $id => $attributes) : ?>
                            <li class="list-group-item">
                                <h4 class="list-group-item-heading">
                                    <span class="glyphicon glyphicon-<?= $attributes['icon'] ?>"></span>
                                    <?= $attributes['name'] ?>:
                                </h4>
                                <p class="list-group-item-text"><?= $attributes['value'] ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>
<script>
    $('.latency').each(function () {
        var elem = $(this);
        var ip = elem.data('ip');
        console.log('IP: ' + ip);
      
        $.get('ping.php', {'ip': ip}, function(data){
            if (data.length > 0) {
                elem.html(data + ' ms');
            }
            else {
                elem.html('NR');
            }
        }).fail(function() {
            elem.html('ERR');
        });
    });
</script>
</body>
</html>