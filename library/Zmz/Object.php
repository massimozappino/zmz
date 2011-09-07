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
class Zmz_Object implements Countable, Iterator, ArrayAccess
{

    /**
     * If set to true Exception will be thrown when an attribute is not found
     *
     * @var boolean
     */
    protected $_throwException;

    /**
     * Whether in-memory modifications to configuration data are allowed
     *
     * @var boolean
     */
    protected $_allowModifications;

    /**
     * Data structure where attributes will be stored
     *
     * @var array
     */
    protected $_data;

    /**
     * Iteration index
     *
     * @var integer
     */
    protected $_index;

    /**
     * Number of elements in configuration data
     *
     * @var integer
     */
    protected $_count;

    /**
     * Used when unsetting values during iteration to ensure we do not skip
     * the next element
     *
     * @var boolean
     */
    protected $_skipNextIteration;

    public function __construct($values = null, $throwException = true, $writable = true)
    {
        $this->resetAttributes()
                ->setThrowException($throwException)
                ->_setAllowModification($writable);
        if (!$values) {
            return;
        }
        foreach ($values as $key => $value) {
            if (is_array($value)) {
                $this->_data[$key] = new self($value, $this->_allowModifications);
            } else {
                $this->_data[$key] = $value;
            }
        }
        $this->_count = count($this->_data);
    }

    public static function getInstance($values = null, $throwException = true, $writable = true)
    {
        return new self($values, $throwException, $writable);
    }

    /**
     * Retrieve a value and return $default if there is no element set.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        $result = $default;
        if (array_key_exists($name, $this->_data)) {
            $result = $this->_data[$name];
        }
        return $result;
    }

    /**
     * Magic function so that $obj->value will work.
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * Only allow setting of a property if $allowModifications
     * was set to true on construction. Otherwise, throw an exception.
     *
     * @param  string $name
     * @param  mixed  $value
     * @throws Zend_Config_Exception
     * @return void
     */
    public function __set($name, $value)
    {
        if ($this->_allowModifications) {
            if (is_array($value)) {
                $this->_data[$name] = new self($value, true);
            } else {
                $this->_data[$name] = $value;
            }
            $this->_count = count($this->_data);
        } else {
            /** @see Zmz_Object_Exception */
            require_once 'Zmz/Object/Exception.php';
            throw new Zmz_Object_Exception('Zmz_Object is read only');
        }
    }

    public function __unset($name)
    {

        if ($this->_allowModifications) {
            unset($this->_data[$name]);
            $this->_count = count($this->_data);
            $this->_skipNextIteration = true;
        } else {
            /** @see Zmz_Object_Exception */
            require_once 'Zmz/Object/Exception.php';
            throw new Zmz_Object_Exception('Zmz_Object is read only');
        }
    }

    /**
     * Deep clone of this instance to ensure that nested Zend_Configs
     * are also cloned.
     *
     * @return void
     */
    public function __clone()
    {
        $array = array();
        foreach ($this->_data as $key => $value) {
            if ($value instanceof self) {
                $array[$key] = clone $value;
            } else {
                $array[$key] = $value;
            }
        }
        $this->_data = $array;
    }

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function toArray()
    {
        $array = array();
        $data = $this->_data;
        foreach ($data as $key => $value) {
            if ($value instanceof self) {
                $array[$key] = $value->toArray();
            } else {
                $array[$key] = $value;
            }
        }
        return $array;
    }

    public function resetAttributes()
    {
        $this->_index = 0;
        $this->_data = array();

        return $this;
    }

    public function getThrowException()
    {
        return $this->_throwException;
    }

    public function setThrowException($bool)
    {
        $this->_throwException = (bool) $bool;

        return $this;
    }

    public function setReadOnly()
    {
        $this->_setAllowModification(false);
        return $this;
    }

    public function setWritable()
    {
        $this->_setAllowModification(true);
        return $this;
    }

    protected function _setAllowModification($allowModification)
    {
        $this->_allowModifications = (bool) $allowModification;
        foreach ($this->_data as $key => $value) {
            if ($value instanceof self) {
                $value->_setAllowModification($allowModification);
            }
        }
        return $this;
    }

    /**
     * @deprecated
     */
    public function getIterator()
    {
        trigger_error(
                'Zmz_Object::getIterator() is deprecated as of 2.0; Zmz_Object is now an iterator', E_USER_NOTICE
        );
        return $this->getData();
    }

    public function getData()
    {
        $data = $this->_data;

        if ($data == null) {
            $data = array();
        }

        return $data;
    }

    public function readOnly()
    {
        return!$this->_allowModifications;
    }

    public function count()
    {
        return $this->_count;
    }

    public function current()
    {
        $this->_skipNextIteration = false;
        return current($this->_data);
    }

    public function key()
    {
        return key($this->_data);
    }

    public function next()
    {
        if ($this->_skipNextIteration) {
            $this->_skipNextIteration = false;
            return;
        }
        next($this->_data);
        $this->_index++;
    }

    public function rewind()
    {
        $this->_skipNextIteration = false;
        reset($this->_data);
        $this->_index = 0;
    }

    public function valid()
    {
        return $this->_index < $this->_count;
    }

    public function offsetExists($offset)
    {
        return isset($this->_index);
    }

    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->__set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        return $this->__unset($offset);
    }

}

