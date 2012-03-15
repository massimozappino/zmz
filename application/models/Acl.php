<?php

class Model_Acl extends Zend_Acl
{

    protected static $userRow;
    protected static $_identity;
    protected static $_storage;
    protected static $_identityLoaded = false;

    public function __construct()
    {
        // Roles
        $this->addRole(new Zend_Acl_Role('guest'));
        $this->addRole(new Zend_Acl_Role('user'), 'guest');
        $this->addRole(new Zend_Acl_Role('admin'), 'user');

        // Resources
        $this->add(new Zend_Acl_Resource('admin'));

        // Allows
//        $this->allow('guest', 'resource', 'view');
        $this->allow('admin', 'admin', 'all');
    }

    public static function initDefaultStorage()
    {
        $instance = MyApp_Auth::getInstance();
        $instance->setStorage(self::getStorage());

        return $instance;
    }

    /**
     *
     * @param <type> $resource
     * @param <type> $privilege
     * @return <type>
     */
    public static function isUserAllowed($resource = null, $privilege = null)
    {
        if (!self::isLogged()) {
            return false;
        }
        $userId = self::getIdUser();
        $usersModel = new Model_Users();
        $group = $usersModel->getGroupName($userId);

        $acl = new self();
        return $acl->isAllowed($group, $resource, $privilege);
    }

    public static function isLogged()
    {
//        return MyApp_Auth::getInstance()->hasIdentity();
        return !is_null(self::getIdentity());
    }

    public static function requireLogin()
    {
        if (!self::isLogged()) {
            $redirectHelper = new Zend_Controller_Action_Helper_Redirector();
            $url = new Zend_View_Helper_Url();
            $loginUrl = $url->url(array('controller' => 'account', 'action' => 'login'), 'default', true);

            $redirect = @ $_SERVER['REQUEST_URI'];
            if ($redirect == $loginUrl) {
                $redirect = null;
            }
            if ($redirect) {
                $loginUrl .= '?redirect=' . urlencode($redirect);
            }
            $redirectHelper->gotoUrlAndExit($loginUrl);
        }
    }

    public static function getAuthInstance()
    {
        return MyApp_Auth::getInstance();
    }

    public static function getIdentity()
    {
        if (!self::$_identityLoaded) {

            $instance = self::initDefaultStorage();
            $identity = $instance->getIdentity();

            self::$_identity = $identity;
            self::$_identityLoaded = true;
        }
        return self::$_identity;
    }

    public static function getStorage()
    {
        if (!self::$_storage) {
            // default storage
            return new MyApp_Auth_Storage_Database();
        }
        return self::$_storage;
    }

    public static function getUserLocale()
    {
        if (!self::isLogged()) {
            return false;
        }
        $row = self::getUserRow();

        return $row->locale;
    }

    public static function getUserTimezone()
    {
        if (!self::isLogged()) {
            return false;
        }
        $row = self::getUserRow();

        return $row->timezone;
    }

    /**
     * deprecated
     * @return type 
     */
    public static function getIdUser()
    {
        return self::getUserId();
    }

    public static function getUserId()
    {
        if (!self::isLogged()) {
            return false;
        }
        $identity = self::getIdentity();

        return $identity['user_id'];
    }

    public static function getUsername()
    {
        if (!self::isLogged()) {
            return false;
        }
        $row = self::getUserRow();

        return $row->username;
    }

    public static function getUserRow()
    {
        if (!self::$userRow) {
            $usersModel = new Model_Users();
            $id = self::getIdUser();
            $row = $usersModel->findById($id);
            self::$userRow = $row;
        }

        return self::$userRow;
    }

    public static function itsMe($userId)
    {
        if ($userId instanceof Zend_Db_Table_Row) {
            $userId = $userId->user_id;
        }

        $itsMe = false;
        if (self::isLogged()) {

            $loggedUserId = self::getIdUser();
            if ($loggedUserId == $userId) {
                $itsMe = true;
            }
        }

        return $itsMe;
    }

    public static function getSessionId($userId)
    {
        if ($userId instanceof Zend_Db_Table_Row) {
            $userId = $userId->user_id;
        }
        $authStorageDatabase = new MyApp_Auth_Storage_Database();
        $sessionId = $authStorageDatabase->getSessionId();
        return $sessionId;
    }

}