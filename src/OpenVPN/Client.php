<?php
namespace OpenVPN;

class Client {
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
        $this->destTimeZone = new \DateTimeZone(\OpenVPN\Config::getValue('timezone.dst'));
    }

    public function getReadableArray () {
        return [
            'vpn_ip' => [
                'name' => 'VPN IP',
                'icon' => 'cloud',
                'value' => $this->vpnIp,
            ],
            'real_ip' => [
                'name' => 'Real IP',
                'icon' => 'globe',
                'value' => $this->realIp,
            ],
            'bytes_rx' => [
                'name' => 'Bytes Received',
                'icon' => 'cloud-upload',
                'value' => $this->sizeFormat($this->bytesReceived),
            ],
            'bytes_tx' => [
                'name' => 'Bytes Sent',
                'icon' => 'cloud-download',
                'value' => $this->sizeFormat($this->bytesSent),
            ],
            'connected_since' => [
                'name' => 'Connected Since',
                'icon' => 'signal',
                'value' => $this->dateFormat($this->connectedSince),
            ],
            'routing_since' => [
                'name' => 'Routing Since',
                'icon' => 'random',
                'value' => $this->dateFormat($this->routingSince),
            ],
        ];
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
     * @param \DateTime $date
     * @return string
     */
    private function dateFormat ($date) {
        if (!$date) {
            return 'N/A';
        }

        $dateTime = clone($date);
        $dateTime->setTimezone($this->destTimeZone);
        return $dateTime->format(DATE_RFC1036);
    }
}