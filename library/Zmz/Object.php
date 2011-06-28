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
class Zmz_Object implements ArrayAccess, Countable, IteratorAggregate
{

    /**
     * If set to true Exception will be thrown when attribute not found
     *
     * @var boolean
     */
    protected $_throwException;
    /**
     * Data structure where attributes will be stored
     *
     * @var stdClass
     */
    protected $_data;


    public function __construct($values = null, $throwException = true)
    {
        $this->resetAttributes()
                ->setThrowException($throwException);
        if (is_array($values)) {
            $this->setFromArray($values);
        } elseif ($values instanceof Zmz_Object) {
            $this->setFromArray($values->toArray());
        }
    }

    public static function getInstance($values = null, $throwException = true)
    {
        return new self($values, $throwException);
    }

    public function __get($key)
    {
        if (!isset($this->_data->$key)) {
            if ($this->_throwException) {
                throw new Zmz_Exception('Key ' . $key . ' is not set');
            } else {
                return null;
            }
        }

        return $this->_data->$key;
    }

    public function __set($key, $value)
    {
        $this->_data->$key = $value;
    }

    public function __isset($key)
    {
        return isset($this->_data->$key);
    }

    public function getData()
    {
        $attributes = $this->_data;

        if ($attributes == null) {
            $attributes = array();
        }

        return $attributes;
    }

    public function get($key)
    {
        return $this->__get($key);
    }

    public function set($key, $value)
    {
        $this->__set($key, $value);

        return $this;
    }

    public function resetAttributes()
    {
        $this->_data = new stdClass();

        return $this;
    }

    public function setFromArray(array $array)
    {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $v = new self($v, $this->getThrowException());
            }
            $this->_data->$k = $v;
        }

        return $this;
    }

    public function setThrowException($bool)
    {
        $this->_throwException = (bool) $bool;

        return $this;
    }

    public function toArray()
    {
        $attributes = $this->getData();
        $array = array();
        foreach ($attributes as $k => $v) {
            if ($v instanceof self) {
                $array[$k] = $v->toArray();
            } else {
                $array[$k] = $v;
            }
        }

        return $array;
    }

    /**
     *
     * @return boolean
     */
    public function getThrowException()
    {
        return $this->_throwException;
    }

    public function offsetExists($offset)
    {
        return isset($this->_data->$offset);
    }

    public function offsetGet($offset)
    {
        return $this->_data->$offset;
    }

    public function offsetSet($offset, $value)
    {
        $this->_data->$offset = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->_data->$offset);
    }

    public function count()
    {
        return count($this->toArray());
    }

    public function getIterator()
    {
        return $this->getData();
    }

}
