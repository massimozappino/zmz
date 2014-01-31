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
class Zmz_Messenger
{

    const ERROR = 'error';
    const WARNING = 'warning';
    const INFORMATION = 'information';
    const SUCCESS = 'success';

    protected static $_instance;
    protected $_prefix = 'msg_';
    protected $_messages;
    protected $_read;
    protected $_tmpSession;
    protected static $_typeError = self::ERROR;
    protected static $_typeWarning = self::WARNING;
    protected static $_typeInformation = self::INFORMATION;
    protected static $_typeSuccess = self::SUCCESS;

    /**
     * Get singleton instance
     * 
     * @return Zmz_Messenger 
     */
    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    protected function __construct()
    {
        $this->_messages = array();
        $this->_read = array();
        $this->_tmpSession = array();
        $this->_session = new Zend_Session_Namespace('Zmz_FlashMessenger');

        if (!(isset($this->_session->messages) && is_array($this->_session->messages))) {
            $this->_session->messages = array();
        }
        $this->_read = $this->_session->messages;
    }

    protected function _extract()
    {
        $allMessages = array_merge($this->_read, $this->_messages);
        $messages = array();

        foreach ($allMessages as $k => $v) {
            if (!isset($messages[$v['type']])) {
                $messages[$v['type']] = array($v['message']);
            } else {
                array_push($messages[$v['type']], $v['message']);
            }
        }
        return $messages;
    }

    public static function setTypeClass($typeConst, $className)
    {
        switch ($typeConst) {
            case self::ERROR:
                self::$_typeError = $className;
                break;
            case self::INFORMATION:
                self::$_typeInformation = $className;
                break;
            case self::SUCCESS:
                self::$_typeSuccess = $className;
                break;
            case self::WARNING:
                self::$_typeWarning = $className;
                break;
        }
    }

    protected function _getFilteredTypes()
    {
        $types = array(
            self::ERROR => self::$_typeError,
            self::INFORMATION => self::$_typeInformation,
            self::WARNING => self::$_typeWarning,
            self::SUCCESS => self::$_typeSuccess,
        );
        return $types;
    }

    protected function _getFilteredType($type)
    {
        $types = self::_getFilteredTypes();
        return @$types[$type];
    }

    public function readMessages($reset = true)
    {
        $messages = $this->_extract();
        $this->_resetSession();
        return $messages;
    }

    public function setPrefix($prefix)
    {
        $this->_prefix = $prefix;

        return $this;
    }

    public function getPrefix()
    {
        $prefix = $this->_prefix;

        return $prefix;
    }

    public function addError($message, $after = false)
    {
        $this->_addMessage(self::ERROR, $message, $after);

        return $this;
    }

    public function addWarning($message, $after = false)
    {
        $this->_addMessage(self::WARNING, $message, $after);

        return $this;
    }

    public function addInformation($message, $after = false)
    {
        $this->_addMessage(self::INFORMATION, $message, $after);

        return $this;
    }

    public function addSuccess($message, $after = false)
    {
        $this->_addMessage(self::SUCCESS, $message, $after);

        return $this;
    }

    public function count()
    {
        return count($this->_extract());
    }

    protected function _addMessage($type, $message, $after = false)
    {
        $filteredType = $this->_getFilteredType($type);
        $messageRow = array(
            'type' => $this->getPrefix() . $filteredType,
            'message' => $message
        );
        if ($after) {
            array_push($this->_tmpSession, $messageRow);
            array_push($this->_session->messages, $messageRow);
        } else {
            array_push($this->_messages, $messageRow);
        }
    }

    protected function _resetSession()
    {
        $this->_session->unsetAll();
        $this->_session->messages = $this->_tmpSession;
    }

}

