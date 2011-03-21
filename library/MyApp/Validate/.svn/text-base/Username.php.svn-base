<?php

/**
 * @see Zend_Validate_Abstract
 */
require_once 'Zend/Validate/Abstract.php';

class MyApp_Validate_Username extends Zend_Validate_Abstract
{
    const INVALID = 'usernameInvalid';
    const RESERVED = 'usernameReserved';
    const TOO_SHORT = 'usernameTooShort';
    const TOO_LONG = 'usernameTooLong';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID => "Username is not valid",
        self::RESERVED => "Username is not available",
        self::TOO_SHORT => "Username is too short",
        self::TOO_LONG => "Username is too long"
    );

    private $projectConfig;

    public function __construct()
    {
        $this->projectConfig = Zend_Registry::get('projectConfig');
    }

    /**
     * 
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
//        if (!preg_match('/^[A-Za-z0-9_.]*$/', $value)) {
//            $this->_error(self::INVALID);
//            return false;
//        }
        $stringLengthValidatorMin = new Zend_Validate_StringLength(array('min' => $this->projectConfig->min_username_length));

        if (!$stringLengthValidatorMin->isValid($value)) {
            $this->_error(self::TOO_SHORT);
            return false;
        }

        $stringLengthValidatorMax = new Zend_Validate_StringLength(array('max' => $this->projectConfig->max_username_length));
        if (!$stringLengthValidatorMax->isValid($value)) {
            $this->_error(self::TOO_LONG);
            return false;
        }

                if (!preg_match('/^[A-Za-z][a-zA-Z0-9_]*\.?[a-zA-Z0-9_]*$/', $value)) {
            $this->_error(self::INVALID);
            return false;
        }




        if ($this->_isReservedUsername($value)) {
            $this->_error(self::RESERVED);
            return false;
        }

        return true;
    }

    /**
     * TODO cachable
     *
     * @param string $username
     * @return boolean
     */
    private function _isReservedUsername($username)
    {
        $invalidUsernames = array();

        $filename = APPLICATION_PATH . '/configs/reserved_usernames.ini';
        if (file_exists($filename)) {
            $reserved = file($filename, FILE_IGNORE_NEW_LINES);
            foreach ($reserved as $v) {
                $invalidUsernames[$v] = $v;
            }
        } else {
            throw new Exception('file "config/reserved_usernames.ini" not found');
        }

        // routes controller
        $routerConfig = Zend_Registry::get('routerConfig');
        foreach ($routerConfig->routes as $k => $v) {
            $routeName = explode('/', $v->route, 2);
            $invalidUsernames[$k] = $k;
            $invalidUsernames[$routeName[0]] = $routeName[0];
        }

        // controller for default module
        $controllersDirectory = APPLICATION_PATH . '/controllers';
        $directory = opendir($controllersDirectory);
        while ($entry = readdir($directory)) {
            $entry = strtolower($entry);
            $entry = str_replace('controller.php', '', $entry);
            $invalidUsernames[$entry] = $entry;
        }
        closedir($directory);

        // all other modules
        $modulesDirectory = APPLICATION_PATH . '/modules';
        $directory = opendir($modulesDirectory);
        while ($entry = readdir($directory)) {
            $invalidUsernames[$entry] = $entry;
        }
        closedir($directory);

        // check keywords
        ksort($invalidUsernames);
        foreach ($invalidUsernames as $compare) {
            if ($this->_isUsernameStartWith($username, $compare)) {
                return true;
            }
        }

        return false;
    }

    private function _isUsernameStartWith($username, $compare)
    {
        $compare = trim($compare);
        if (strlen($compare) < $this->projectConfig->min_username_length) {
            return false;
        }
        if (substr($username, 0, strlen($compare)) == $compare) {
            return true;
        }
        return false;
    }

}
