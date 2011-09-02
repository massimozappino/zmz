<?php

/**
 * Zmz
 *
 * LICENSE
 *
 * This source file is subject to the GNU GPLv3 license that is bundled
 * with this package in the file COPYNG.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @copyright  Copyright (c) 2010-2011 Massimo Zappino (http://www.zappino.it)
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU GPLv3 License
 */
class Zmz_Host
{

    protected static $host;
    protected static $port;
    protected static $scheme;
    protected static $domainLevels;

    /**
     * Extract informations
     *
     * @return boolean
     */
    protected static function extract()
    {
        if (self::$host) {
            return true;
        }

        // scheme
        if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] === true)) {
            $scheme = 'https';
        } else {
            $scheme = 'http';
        }
        self::$scheme = $scheme;

        // port
        if (isset($_SERVER['SERVER_PORT']) && !empty($_SERVER['SERVER_PORT'])) {
            $port = $_SERVER['SERVER_PORT'];
        } else {
            $port = 80;
        }
        self::$port = $port;

        // host
        $host = 'Unknown';
        if (isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        } elseif (isset($_SERVER['SERVER_NAME'])) {
            $host = $_SERVER['SERVER_NAME'];
        }
        self::$host = $host;


        $levels = explode('.', self::$host);
        $count = count($levels);
        $tmpHost = str_replace('.', '', self::$host);
        self::$domainLevels = array();
        if (is_numeric($tmpHost)) {
            // hostname is IP address
        } else {
            foreach ($levels as $l) {
                self::$domainLevels[$count] = $l;
                $count--;
            }
        }
        return true;
    }

    /**
     *
     * @return string
     */
    public static function getHostname()
    {
        self::extract();

        return self::$host;
    }

    /**
     * Get the current port
     * 
     * @return int 
     */
    public static function getPort()
    {
        self::extract();

        return self::$port;
    }

    /**
     * Get the current scheme: http or https
     *
     * @return string
     */
    public static function getScheme()
    {
        self::extract();

        return self::$scheme;
    }

    public static function getServerUrl($level = null)
    {
        if ($level == null) {
            $hostname = self::getHostname();
        } else {
            $hostname = self::buildHostname($level);
        }

        $serverUrl = self::buildUrl($hostname, self::getScheme(), self::getPort());

        return $serverUrl;
    }

    public static function buildUrl($hostname, $scheme = null, $port = null)
    {
        if (is_null($scheme)) {
            $scheme = 'http';
        }

        $url = $scheme . '://' . $hostname;
        if ($port && $port != '80') {
            $url .= ':' . $port;
        }

        return $url;
    }

    /**
     * Get an array
     * 
     * @return array
     */
    public static function getDomainLevels()
    {
        self::extract();

        return self::$domainLevels;
    }

    /**
     *
     * @param int $level
     * @return mixed subdomain at given  
     */
    public static function getSubdomain($level)
    {
        self::extract();

        $domainLevels = self::$domainLevels;
        $level = (int) $level;
        if (isset($domainLevels[$level])) {
            return $domainLevels[$level];
        } else {
            return null;
        }
    }

    public static function getSubdomains()
    {
        self::extract();

        $domainLevels = self::$domainLevels;
        return $domainLevels;
    }

    public static function buildHostname($level)
    {
        if (!count(self::getSubdomains())) {
            return null;
        }
        $levels = array();
        for ($i = $level; $i >= 1; $i--) {
            $levels[$i] = self::getSubdomain($i);
        }

        return implode('.', $levels);
    }

    /**
     * Get the current user agent
     * 
     * @return string
     */
    public static function getUserAgent()
    {
        $userAgent = @$_SERVER['HTTP_USER_AGENT'];

        return $userAgent;
    }

    /**
     * Get current IP
     *
     * @return string
     */
    public static function getIp()
    {
        $rawIp = @getenv("REMOTE_ADDR");
        if (!$rawIp) {
            $rawIp = '127.0.0.1';
        }

        return $rawIp;
    }

    /**
     * Get current ip filtered. It will add a
     *
     * @return string
     */
    public static function getFilteredIp()
    {
        return self::formatIp(self::getIp());
    }

    public static function compareIp($ip1, $ip2)
    {
        $ip1 = self::formatIp($ip1);
        $ip2 = self::formatIp($ip2);

        return strcmp($ip1, $ip2);
    }

    public static function encodeIp($ip)
    {
        $tmp = explode('.', $ip);
        $ipEncoded = sprintf("%02x%02x%02x%02x", $tmp[0], $tmp[1], $tmp[2], $tmp[3]);

        return $ipEncoded;
    }

    public static function decodeIp($ip)
    {
        $hex = explode('.', chunk_split($ip, 2, '.'));
        $ipDecoded = hexdec($hex[0]) . '.' . hexdec($hex[1]) . '.' .
                hexdec($hex[2]) . '.' . hexdec($hex[3]);

        return $ipDecoded;
    }

    public static function formatIp($ip)
    {
        $ipFormatted = self::decodeIp(self::encodeIp($ip));

        return $ipFormatted;
    }

    /**
     * Get current url
     *
     * @return string
     */
    public static function getUrl()
    {
        $url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        $url = self::getHostname() . $url;

        return $url;
    }

    /**
     * Get the referer url
     *
     * @param string|null $default Url to use if no referer found
     * @return string
     */
    public static function getReferer($default = null)
    {
        if (isset($_SERVER['HTTP_REFERER'])) {
            return $_SERVER['HTTP_REFERER'];
        } else {
            if ($default) {
                return (string) $default;
            } else {
                return '';
            }
        }
    }

}

