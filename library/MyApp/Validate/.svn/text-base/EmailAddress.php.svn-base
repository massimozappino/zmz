<?php

class MyApp_Validate_EmailAddress extends Zend_Validate_EmailAddress
{
    const INVALID = 'emailInvalid';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID => "Email address is not valid",
    );
    private $projectConfig;

    public function __construct($options = array())
    {
        parent::__construct($options);
        $this->projectConfig = Zend_Registry::get('projectConfig');
    }

    /**
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        if (!parent::isValid($value)) {
            $this->_errors = array();
            $this->_messages = array();
            $this->_error(self::INVALID);
            return false;
        }

        return true;
    }

}
