<?php

class Form_Signin extends Zmz_Form
{

    public function init()
    {
        parent::init();

        $projectConfig = Zend_Registry::get('projectConfig');

        $this->setName('formSignin');

        $username = new MyApp_Form_Element_Username('username');
        $username->setDescription(
                Zmz_String::format(
                        Zmz_Translate::_('Use {0} to {1} characters and start with a letter. You may use letters, numbers, underscores, and one dot (.)'),
                        $projectConfig->min_username_length, $projectConfig->max_username_length)
        );

        $password = new MyApp_Form_Element_Password('password');
        $password->setDescription(
                Zmz_String::format(
                        Zmz_Translate::_('Password is case sensitive, use {0} to {1} characters'),
                        $projectConfig->min_password_length, $projectConfig->max_password_length)
        );

        $confirmPassword = new MyApp_Form_Element_Password('confirmpassword');
        $confirmPassword->setLabel(Zmz_Translate::_('Confirm password'))
                        ->setDescription(Zmz_Translate::_("Rewrite your password"))
                ->removeValidator('stringLength');

        $email = new MyApp_Form_Element_Email('email');
        $email->setDescription(Zmz_Translate::_("A valid email address is required, we'll send you a confirmation, email will not be displayed to other users"));


        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel(Zmz_Translate::_('Sign in'));
        ;

        $this->addElements(
                array(
                    $username,
                    $password,
                    $confirmPassword,
                    $email,
                    $submit
        ));
    }

}

