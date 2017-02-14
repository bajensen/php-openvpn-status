<?php
namespace OpenVPN;

class Config {
    private static $config;

    /**
     * @return array
     */
    public static function getConfig () {
        return self::$config;
    }

    /**
     * @param array $config
     */
    public static function setConfig ($config) {
        self::$config = $config;
    }

    public static function getValue ($path) {
        $loc = &self::$config;

        foreach (explode('.', $path) as $step) {
            $loc = &$loc[$step];
        }

        return $loc;
    }
}