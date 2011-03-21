<?php

class Form_Lostpassword extends Zmz_Form
{
    public function __construct($options = null)
    {
        parent::__construct($options);
        $this->setName('form-lostpassword');

        $email = new MyApp_Form_Element_Email('email');
       
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel(Zmz_Translate::_('Send me instructions'));

        $this->addElements( array ($email, $submit));
    }
}