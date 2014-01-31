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
class Zmz_Breadcrumbs
{

    protected static $_instance;
    private $_data = array();
    private $_ignoreSinglePage = false;

    private function __construct()
    {
        
    }

    /**
     * Get singleton instance
     * 
     * @return Zmz_Breadcrumbs 
     */
    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    /**
     *
     * @return array 
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     *
     * @param type $flag
     * @return \Zmz_Breadcrumbs 
     */
    public function setIgnoreSinglePage($flag)
    {
        $this->_ignoreSinglePage = (bool) $flag;
        return $this;
    }

    /**
     *
     * @return type 
     */
    public function getIgnoreSinglePage()
    {
        return $this->_ignoreSinglePage;
    }

    /**
     *
     * @param string $title
     * @param string $url
     * @param boolean $active
     * @return \Zmz_Breadcrumbs 
     */
    public function addElement($title, $url = null, $active = false)
    {
        $this->_data[$title] = array('url' => $url, 'active' => $active);
        if ($active) {
            $this->setActiveElement($title);
        }
        return $this;
    }

    /**
     *
     * @param string $title
     * @return \Zmz_Breadcrumbs 
     */
    public function setActiveElement($title = null)
    {
        foreach ($this->_data as $k => &$v) {
            if ($title && $k == $title) {
                $v['active'] = true;
            } else {
                $v['active'] = false;
            }
        }
        return $this;
    }

    /**
     * Clear all breadcrumbs
     * @return \Zmz_Breadcrumbs 
     */
    public function clear()
    {
        $this->_data = array();
        return $this;
    }

}

