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
class Zmz_Mail extends Zend_Mail
{

    protected static $_email;
    protected static $_name;
    protected static $_defaultCharset = 'UTF-8';

    public function __construct($charset = 'UTF-8')
    {
        parent::__construct($charset);
        if (!is_null(self::$_email)) {
            parent::setFrom(self::$_email, self::$_name);
        }
    }

    public static function getInstance($charset = null)
    {
        if ($charset === null) {
            $charset = self::$_defaultCharset;
        }

        return new self($charset);
    }

    public static function setDefaultCharset($charset)
    {
        self::$_defaultCharset = $charset;
    }

    public static function setDefaultFrom($email, $name)
    {
        self::$_email = $email;
        self::$_name = $name;
    }

    public static function clearDefaultFrom()
    {
        self::$_email = null;
        self::$_name = null;
    }

    public function setFrom($email, $name = null)
    {
        if (!is_null(self::$_email)) {
            $this->_from = null;
            self::clearDefaultFrom();
            $this->_clearHeader('From');
        }

        return parent::setFrom($email, $name);
    }

}

