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

    protected static $_emailFrom;
    protected static $_nameFrom;
    protected static $_defaultCharset = 'UTF-8';

    public function __construct($charset = 'UTF-8')
    {
        parent::__construct($charset);
        if (!is_null(self::$_emailFrom)) {
            parent::setFrom(self::$_emailFrom, self::$_nameFrom);
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

    public static function setDefaultFrom($email, $name = null)
    {
        self::$_emailFrom = $email;
        self::$_nameFrom = $name;
    }

    public static function clearDefaultFrom()
    {
        self::$_emailFrom = null;
        self::$_nameFrom = null;
    }

    public function setFrom($email, $name = null)
    {
        if (!is_null(self::$_emailFrom)) {
            $this->_from = null;
            self::clearDefaultFrom();
            $this->_clearHeader('From');
        }

        return parent::setFrom($email, $name);
    }

}

