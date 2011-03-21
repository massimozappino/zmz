<?php

class MyApp_Auth_Storage_Database implements Zend_Auth_Storage_Interface
{

    private $_fieldIdUser = 'user_id';
    private $_fieldIdSession = 'session_id';
    private $_fieldHostname = 'hostname';
    private $_fieldIp = 'ip';
    private $_fieldRemember = 'remember';
    private $_fieldDateCreation = 'date_creation';
    private $_fieldDateLastAccess = 'date_last_access';
    private $_cookieNamespace;
    private $_model;
    private $_readSingleton = null;

    public function __construct()
    {
        $this->_model = new Model_Sessions();
        $this->_cookieNamespace = '_zmz_sess';
    }

    /**
     * Returns true if and only if storage is empty
     *
     * @throws Zend_Auth_Storage_Exception If it is impossible to determine whether storage is empty
     * @return boolean
     */
    public function isEmpty()
    {
        $sessionId = $this->_getCookieSessionId();
        $row = $this->_model->getSession($sessionId);
        if (empty($row)) {
            return true;
        }

        $projectConfig = Zend_Registry::get('projectConfig');

        $sessionTimeDefault = (int) $projectConfig->session_time_default;
        $sessionTimeRemember = (int) $projectConfig->session_time_remember;

        $localTimestamp = Zmz_Date::getDate()->getTimestamp();
        $lastAccessTimestamp = Zmz_Date::getDateFromDb($row[$this->_fieldDateLastAccess])->getTimestamp();

        $sessionTime = $row->remember ? $sessionTimeRemember : $sessionTimeDefault;

        if ($localTimestamp > $lastAccessTimestamp + $sessionTime) {
            $this->clear();
            return true;
        }
        $this->_updateLastAccess($row);
        return false;
    }

    /**
     * Returns the contents of storage
     *
     * Behavior is undefined when storage is empty.
     *
     * @throws Zend_Auth_Storage_Exception If reading contents from storage is impossible
     * @return mixed
     */
    public function read()
    {
        if ($this->_readSingleton === null) {
            $sessionId = $this->_getCookieSessionId();
            $row = $this->_model->getSession($sessionId);
            if (is_null($row)) {
                throw new Zend_Auth_Storage_Exception();
            }

            $data = array(
                'user_id' => $row->user_id
            );
            $this->_readSingleton = $data;
        }

        return $this->_readSingleton;
    }

    /**
     * Writes $contents to storage
     *
     * @param mixed $contents
     * @throws Zend_Auth_Storage_Exception If writing $contents to storage is impossible
     * @return void
     */
    public function write($contents)
    {
        if (is_array($contents)) {
            $userId = $contents[$this->_fieldIdUser];
        } else {
            $row = $this->_model->getSessionByIdUser($contents);
            if (!$row) {
                throw new Zend_Auth_Storage_Exception('Session not found for ' . "'{$contents}'");
            }
            $userId = $row[$this->_fieldIdUser];
        }

        $remember = isset($contents['remember']) ? (bool) $contents['remember'] : false;
        $nowSql = Zmz_Date::getSqlDateTime();
        $sessionId = $this->_generateSessionId();
        $data = array(
            $this->_fieldIdSession => $sessionId,
            $this->_fieldIdUser => $userId,
            $this->_fieldHostname => Zmz_Host::getHostname(),
            $this->_fieldIp => Zmz_Host::getFilteredIp(),
            $this->_fieldRemember => $remember,
            $this->_fieldDateCreation => $nowSql,
            $this->_fieldDateLastAccess => $nowSql
        );

        // Clear session
        $this->clear();

        try {
            $result = $this->_model->writeSession($data);
        } catch (Exception $e) {
            throw new Zend_Auth_Storage_Exception($e->getMessage());
        }

        if (!$result) {
            throw new Zend_Auth_Storage_Exception('Session was empty');
        }

        // set the cookie
        $cookie = $this->_getCookieObject($remember);
        $cookie->setValue($sessionId);
    }

    /**
     * Clears contents from storage
     *
     * @throws Zend_Auth_Storage_Exception If clearing contents from storage is impossible
     * @return void
     */
    public function clear()
    {
        try {
            $sessionId = $this->_getCookieSessionId();
            $this->_model->deleteSession($sessionId);

            // delete cookie
            $this->_getCookieObject()->setValue(null);
        } catch (Exception $e) {
            throw new Zend_Auth_Storage_Exception();
        }
    }

    protected function _getCookieSessionId()
    {
        $sessionId = $this->_getCookieObject()->getValue();

        return $sessionId;
    }

    protected function _generateSessionId()
    {
        list($usec, $sec) = explode(' ', microtime());
        mt_srand((float) $sec + ((float) $usec * 100000));
        $hash = md5(uniqid(mt_rand(), true));

        return $hash;
    }

    protected function _updateLastAccess(Zend_Db_Table_Row $row)
    {
        try {
            // update interval in seconds
            $updateInterval = 180;
            $localTimestamp = Zmz_Date::getDate()->getTimestamp();
            $lastAccessTimestamp = Zmz_Date::getDateFromDb($row[$this->_fieldDateLastAccess])->getTimestamp();

            if ($localTimestamp - $lastAccessTimestamp > $updateInterval) {
                $nowSql = Zmz_Date::getSqlDateTime();
                $row[$this->_fieldDateLastAccess] = $nowSql;
                $row->save();
            }
        } catch (Exception $e) {
            throw new Zend_Auth_Storage_Exception($e->getMessage());
        }
    }

    protected function _getCookieObject($remember = false)
    {
        $expires = null;
        if ($remember || 1) {
            $date = Zmz_Date::getDate()->addYear(1);
            $expires = $date->getTimestamp();
        }

        $domain = Zmz_Host::getSubdomain(2) . '.' . Zmz_Host::getSubdomain(1);
        $httpCookieObject = new Zend_Http_Cookie($this->_cookieNamespace, null, $domain, $expires);
        $cookie = new Zmz_Cookie($httpCookieObject);

        return $cookie;
    }

}

