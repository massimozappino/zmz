<?php

class Zmz_View_Helper_Translate extends Zend_View_Helper_Abstract
{

    /**
     * Zmz_Translate object
     *
     * @var Zmz_Translate
     */
    protected $translate;
    /**
     * If true string will be escaped
     *
     * @var boolean
     */
    protected $escape = false;

    public function __construct()
    {
        $this->translate = Zmz_Translate::getInstance();
    }

    public function translate($encode = false)
    {
        $this->escape = (bool) $encode;
        return $this;
    }

    public function _($string)
    {
        return $this->translate->_($string, $this->escape);
    }

}

