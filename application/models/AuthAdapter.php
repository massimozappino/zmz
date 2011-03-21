<?php

class Model_AuthAdapter implements Zend_Auth_Adapter_Interface
{

    protected $usernameOrEmail;
    protected $password;
    protected $isHashPassword;
    protected $remember;
    protected $user;
    protected $method;

    public function __construct($usernameOrEmail, $password, $isHashPassword = false, $remember = false, $method = 'void')
    {
        $this->usernameOrEmail = $usernameOrEmail;
        $this->password = $password;
        $this->isHashPassword = (bool) $isHashPassword;
        $this->remember = (bool) $remember;
        $this->user = new Model_Users();
        $this->method = $method;
    }

    public function authenticate()
    {
        $match = $this->user->findCredentials($this->usernameOrEmail, $this->password, $this->isHashPassword);

        if ($match && $match->status != Model_Users::STATUS_ACTIVE) {
            // check if user is confirmed
            $result = new Zend_Auth_Result(Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS, null);
        } elseif (!$match) {
            $result = new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, null);
        } else {
            $projectConfig = Zend_Registry::get('projectConfig');
            $sessionTimeDefault = (int) $projectConfig->session_time_default;
            $sessionTimeRemember = (int) $projectConfig->session_time_remember;

            $sessionTime = $this->remember ? $sessionTimeRemember : $sessionTimeDefault;
            switch ($this->method) {
                case 'session':

                    // Receive Zend_Session_Namespace object
                    require_once('Zend/Session/Namespace.php');
                    $session = new Zend_Session_Namespace('Zend_Auth');
                    // Set the time of user logged in
                    $session->setExpirationSeconds($sessionTime);

                    // If "remember" was marked
                    if ($this->remember == true) {
                        Zend_Session::rememberMe($sessionTime);
                    }
                    break;

                case 'database':
                    // creating cookie is delegated to MyApp_Auth_Storage_Database
                    break;

                case 'void':
                default:

                    break;
            }

            $match = new Zmz_Object(current($match), true);

            $user = array(
                'user_id' => $match->user_id,
                'username' => $match->username,
                'remember' => $this->remember
            );
            $result = new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $user);
        }

        return $result;
    }

}
