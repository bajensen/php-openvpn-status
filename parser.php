<?php

/*
 * Sample:
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
 */

class OpenVPNClient {
    public $name;
    public $realIp;
    public $realPort;
    public $vpnIp;
    public $bytesReceived;
    public $bytesSent;
    public $connectedSince;
    public $routingSince;
}

class OpenVPNStatus {
    private $contents;
    private $updated;
    private $clients;
    private $stats;

    public function parse () {
        $contents = $this->contents;

        preg_match('/OpenVPN CLIENT LIST(.*)Common.*Connected Since(.*)ROUTING TABLE.*Virtual.*Last Ref(.*)GLOBAL STATS(.*)END/s', $contents, $matches);

        $this->setUpdated($this->parseUpdated($matches[1]));
        $this->setClients($this->parseClients($matches[2]));
        $this->parseRouting($matches[3]);
        $this->setStats(preg_split('/\n|\r\n?/', trim($matches[4])));
    }

    private function parseUpdated ($string) {
        $updated = explode(',', trim($string));
        $updated = array_pop($updated);
        return strtotime($updated);
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
            preg_match('/(.*):(\d)/', $fields[1], $matches);
            $client->realIp = $matches[1];
            $client->realPort = $matches[2];

            // Other Fields
            $client->bytesReceived = $fields[2];
            $client->bytesSent = $fields[3];
            $client->connectedSince = strtotime($fields[4]);

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
            $dateTime = strtotime($fields[3]);

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
     * @return mixed
     */
    public function getUpdated () {
        return $this->updated;
    }

    /**
     * @param mixed $updated
     */
    public function setUpdated ($updated) {
        $this->updated = $updated;
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