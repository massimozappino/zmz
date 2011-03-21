<?php

class Form_Changepassword extends Zmz_Form
{

    public function init()
    {
        parent::init();

        $this->setName('formChangepassword');

        $username = new MyApp_Form_Element_String('username');
        $username->setLabel(Zmz_Translate::_('Username'));


        $email = new MyApp_Form_Element_String('email');
        $email->setLabel(Zmz_Translate::_('Email'));

        $oldPassword = new MyApp_Form_Element_Password('old_password');
        $oldPassword->setLabel(Zmz_Translate::_('Old password'))
                ->removeValidator('stringLength');


        $password = new MyApp_Form_Element_Password('password');
        $password->setLabel(Zmz_Translate::_('New password'));

        $confirmPassword = new MyApp_Form_Element_Password('confirmpassword');
        $confirmPassword->setLabel(Zmz_Translate::_('Confirm password'));

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel(Zmz_Translate::_('Change password'));

        $this->addElements(
                array(
                    $username,
                    $email,
                    $oldPassword,
                    $password,
                    $confirmPassword,
                    $submit
        ));
    }

}

