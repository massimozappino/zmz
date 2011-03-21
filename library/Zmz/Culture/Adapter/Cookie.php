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
class Zmz_Culture_Adapter_Cookie extends Zmz_Culture_Adapter_Abstract
{

    protected $tmpValue;

    public function setStorage($storage)
    {
        if ($storage instanceof Zmz_Cookie) {
            $this->storage = $storage;
        } else {
            throw new Zmz_Culture_Adapter_Exception();
        }
    }

    public function __set($name, $value)
    {
        $storage = $this->getStorage();
        $oldValue = $storage->getValue();
        $newValue = @unserialize($oldValue);
        if (!is_array($newValue)) {
            $newValue = array();
        } elseif (is_array($this->tmpValue)) {
            $newValue = array_merge($newValue, $this->tmpValue);
        }

        $newValue[$name] = $value;
        $this->tmpValue = $newValue;
        $storage->setValue($newValue);
    }

    public function __get($name)
    {
        $storage = $this->getStorage();
        $cookieValue = $storage->getValue();
        $cookieValue = @unserialize($cookieValue);
        if (!is_array($cookieValue)) {
            $cookieValue = array();
        }

        if (isset($cookieValue[$name])) {
            $value = $cookieValue[$name];
        } else {
            $value = null;
        }

        return $value;
    }

    public function __unset($name)
    {
        $storage = $this->getStorage();
        $oldValue = $storage->getValue();
        $newValue = @unserialize($oldValue);

        if (!is_array($newValue)) {
            $newValue = array();
        } elseif (isset($newValue[$name])) {
            unset($newValue[$name]);
        }

        $storage->setValue($newValue);
    }

    public function resetStorage()
    {
        $oldValue = $this->getStorage()->getvalue();
        $values = @unserialize($oldValue);
        foreach ($values as $v) {
            unset($this->$v);
        }
    }

}

