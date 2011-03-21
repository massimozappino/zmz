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
class Zmz_Cookie
{

    /**
     * Zend_Http_Cookie object
     *
     * @var Zend_Http_Cookie
     */
    protected $httpCookieObject;
    /**
     * Current cookie value
     *
     * @var array
     */
    protected $value = null;

    /**
     * Constructor
     *
     * @param Zend_Http_Cookie $httpCookieObject
     */
    public function __construct(Zend_Http_Cookie $httpCookieObject = null)
    {
        if ($httpCookieObject) {
            $this->setHttpCookieObject($httpCookieObject);
        }
    }

    /**
     * Set Zend_Http_Cookie object
     *
     * @param Zend_Http_Cookie $httpCookieObject
     */
    public function setHttpCookieObject(Zend_Http_Cookie $httpCookieObject)
    {
        $this->httpCookieObject = $httpCookieObject;
    }

    /**
     * Get Zend_Http_Cookie object
     *
     * @throws Zmz_Cookie_Exception
     * @return Zend_Http_Cookie
     */
    public function getHttpCookieObject()
    {
        if (!$this->httpCookieObject) {
            throw new Zmz_Cookie_Exception('HttpCookie is not set');
        }

        return $this->httpCookieObject;
    }

    /**
     * Set cookie with httpCookieObject parameters
     *
     * @return bool If output exists prior to calling this function,
     * setcookie will fail and return false. If
     * setcookie successfully runs, it will return true.
     * This does not indicate whether the user accepted the cookie.
     */
    public function write()
    {
        $name = $this->httpCookieObject->getName();
        $value = $this->httpCookieObject->getValue();
        $expire = $this->httpCookieObject->getExpiryTime();
        $path = $this->httpCookieObject->getPath();
        $domain = $this->httpCookieObject->getDomain();
        $secure = $this->httpCookieObject->isSecure();

        $this->value = $value;

        return @setcookie($name, $value, $expire, $path, $domain, $secure);
    }

    /**
     * Set new value and set cookie with "write" method
     *
     * @param array|string $value
     * @throws Zmz_Cookie_Exception
     * @return bool If output exists prior to calling this function,
     * setcookie will fail and return false. If
     * setcookie successfully runs, it will return true.
     * This does not indicate whether the user accepted the cookie.
     */
    public function setValue($value)
    {
        // Check if HttpCookieObject exists
        $this->getHttpCookieObject();

        if (is_array($value)) {
            $value = (string) serialize($value);
        } else {
            $value = (string) $value;
        }

        $name = $this->httpCookieObject->getName();
        $expire = $this->httpCookieObject->getExpiryTime();
        $path = $this->httpCookieObject->getPath();
        $domain = $this->httpCookieObject->getDomain();
        $secure = $this->httpCookieObject->isSecure();

        $httpCookieObject = new Zend_Http_Cookie($name, $value, $domain, $expire, $path, $secure);
        $this->setHttpCookieObject($httpCookieObject);

        return $this->write();
    }

    /**
     * Read value from current cookie.
     * Cookie name is specified by httpCookieObject.
     *
     * @throws Zmz_Cookie_Exception
     * @return string
     */
    public function getValue()
    {
        $this->getHttpCookieObject();
        $name = $this->httpCookieObject->getName();
        if ($this->value === null) {
            $requestHttp = new Zend_Controller_Request_Http();
            $this->value = $requestHttp->getCookie($name);
        }
        return $this->value;
    }

    public function unsetCookie()
    {
        // Check if HttpCookieObject exists
        $this->getHttpCookieObject();

        $name = $this->httpCookieObject->getName();
        $expire = $this->httpCookieObject->getExpiryTime();
        $path = $this->httpCookieObject->getPath();
        $domain = $this->httpCookieObject->getDomain();
        $secure = $this->httpCookieObject->isSecure();

        $value = null;

        $httpCookieObject = new Zend_Http_Cookie($name, $value, $domain, $expire, $path, $secure);
        $this->setHttpCookieObject($httpCookieObject);

        return $this->write();
    }

    /**
     * Get value from current cookie
     * Cookie name is specified by $key.
     * If no $key is passed, returns the entire $_COOKIE array.
     *
     * @param string $key
     * @return string
     */
    public static function get($key = null)
    {
        $value = Zend_Controller_Request_Http::getCookie($key);

        return $value;
    }

    public function __toString()
    {
        return $this->getValue();
    }

}