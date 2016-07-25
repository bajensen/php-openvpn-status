<?php

class OpenVPNClient {
    public $name;
    public $realIp;
    public $realPort;
    public $vpnIp;
    public $bytesReceived;
    public $bytesSent;
    public $connectedSince;
    public $routingSince;

    private $destTimeZone;

    public function __construct () {
        $this->destTimeZone = new DateTimeZone('America/Denver');
    }

    public function getReadableArray () {
        return array(
            'VPN IP' => $this->vpnIp,
            'Real IP' => $this->realIp,
            'Real Port' => $this->realPort,
            'Bytes Received' => $this->sizeFormat($this->bytesReceived),
            'Bytes Sent' => $this->sizeFormat($this->bytesSent),
            'Connected Since' => $this->dateFormat($this->connectedSince),
            'Routing Since' => $this->dateFormat($this->routingSince),
        );
    }

    private function sizeFormat ($bytesize) {
        $i = 0;
        while (abs($bytesize) >= 1024) {
            $bytesize = $bytesize / 1024;
            $i++;
            if ($i == 4) {
                break;
            }
        }

        $units = array("Bytes", "KB", "MB", "GB", "TB");
        $newsize = round($bytesize, 2);
        return $newsize . ' ' . $units[$i];
    }

    /**
     * @param DateTime $date
     * @return string
     */
    private function dateFormat ($date) {
        $dateTime = clone($date);
        $dateTime->setTimezone($this->destTimeZone);
        return $dateTime->format(DATE_RFC1036);
    }
}

class OpenVPNStatus {
    private $contents;
    private $updated;
    private $clients;
    private $stats;

    private $srcTimeZone;

    public function __construct () {
        $this->srcTimeZone = new DateTimeZone('America/New_York');
    }

    public function parse () {
        $contents = $this->contents;

        preg_match('/OpenVPN CLIENT LIST(.*)Common.*Connected Since(.*)ROUTING TABLE.*Virtual.*Last Ref(.*)GLOBAL STATS(.*)END/s', $contents, $matches);

        $this->setUpdated($this->parseUpdated($matches[1]));
        $this->setClients($this->parseClients($matches[2]));
        $this->parseRouting($matches[3]);
        $this->setStats(preg_split('/\n|\r\n?/', trim($matches[4])));
    }

    /**
     * @param $string
     * @return DateTime
     */
    private function parseUpdated ($string) {
        $updated = explode(',', trim($string));
        $updated = array_pop($updated);
        return new DateTime($updated, clone($this->srcTimeZone));
    }

    private function parseClients ($string) {
        $clientLines = preg_split('/\n|\r\n?/', trim($string));
        $clients = array();

        foreach ($clientLines as $clientLine) {
            $fields = str_getcsv($clientLine);

            $client = new OpenVPNClient();

            // Name
            $client->name = $fields[0];

            // IP and Port
            preg_match('/(.*):([\d]+)/', $fields[1], $matches);
            $client->realIp = $matches[1];
            $client->realPort = $matches[2];

            // Other Fields
            $client->bytesReceived = $fields[2];
            $client->bytesSent = $fields[3];
            $client->connectedSince = new DateTime($fields[4], clone($this->srcTimeZone));

            $clients[] = $client;
        }

        return $clients;
    }

    private function parseRouting ($string) {
        $routingLines = preg_split('/\n|\r\n?/', trim($string));

        foreach ($routingLines as $routingLine) {
            $fields = str_getcsv($routingLine);
            $ip = $fields[0];
            $name = $fields[1];
            $dateTime = new DateTime($fields[3], clone($this->srcTimeZone));

            $client = $this->findClient($name);
            if ($client) {
                $client->vpnIp = $ip;
                $client->routingSince = $dateTime;
            }
        }
    }

    /**
     * @param string $name the common name of the client
     * @return bool|OpenVPNClient
     */
    private function findClient ($name) {
        /** @var OpenVPNClient $client */
        foreach ($this->getClients() as $client) {
            if ($client->name == $name) {
                return $client;
            }
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getClients () {
        return $this->clients;
    }

    /**
     * @param mixed $clients
     */
    public function setClients ($clients) {
        $this->clients = $clients;
    }

    public function loadFromFile ($fileName) {
        $contents = file_get_contents($fileName);

        if (! $contents) {
            throw new Exception('File not found or empty.');
        }

        $this->contents = $contents;
    }

    public function loadFromResource ($fileResource) {
        $contents = stream_get_contents($fileResource);

        if (! $contents) {
            throw new Exception('Resource not available or empty.');
        }

        $this->contents = $contents;
    }

    public function loadFromString ($contents) {
        if (! $contents) {
            throw new Exception('Given contents were empty.');
        }

        $this->contents = $contents;
    }

    /**
     * @return mixed
     */
    public function getContents () {
        return $this->contents;
    }

    /**
     * @param mixed $contents
     */
    public function setContents ($contents) {
        $this->contents = $contents;
    }

    /**
     * @return DateTime
     */
    public function getUpdated () {
        return $this->updated;
    }

    /**
     * @param DateTime $updated
     */
    public function setUpdated (DateTime $updated) {
        $this->updated = $updated;
    }

    /**
     * @return mixed
     */
    public function getStats () {
        return $this->stats;
    }

    /**
     * @param mixed $stats
     */
    public function setStats ($stats) {
        $this->stats = $stats;
    }


}