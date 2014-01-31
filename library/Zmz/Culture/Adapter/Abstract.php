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
abstract class Zmz_Culture_Adapter_Abstract implements Zmz_Culture_Adapter_Interface
{

    protected $storage;

    public function __construct($storage = null)
    {
        if ($storage) {
            $this->setStorage($storage);
        }
    }

    public function getStorage()
    {
        if (!$this->storage) {
            throw new Zmz_Culture_Exception('No storage set');
        }

        return $this->storage;
    }

    public function setStorage($storage)
    {
        throw new Zmz_Culture_Exception('setStorage() function is not present in this class');
    }

    public function resetStorage()
    {
        throw new Zmz_Culture_Exception('resetStorage() function is not present in this class');
    }

}

