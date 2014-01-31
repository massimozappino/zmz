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
class Zmz_Culture_Adapter_Session extends Zmz_Culture_Adapter_Abstract
{

    protected $nameSpece = 'culture';

    public function setStorage($storage)
    {
        if ($storage instanceof Zend_Session_Namespace) {
            $this->storage = $storage;
        } else {
            throw new Zmz_Culture_Adapter_Exception();
        }
    }

    public function __set($name, $value)
    {
        $storage = $this->getStorage();
        $storage->$name = $value;
    }

    public function __get($name)
    {
        $storage = $this->getStorage();
        $value = $storage->$name;

        return $value;
    }

    public function __unset($name)
    {
        $storage = $this->getStorage();
        unset($storage->$name);
    }

    public function resetStorage()
    {
        $storage = $this->getStorage();
        $storage->unsetAll();
    }

}
