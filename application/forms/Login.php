<?php

class Form_Login extends Zmz_Form
{

    public function init()
    {
        parent::init();

        $projectConfig = Zend_Registry::get('projectConfig');
        $this->setName('form-login');
        $usernameOrEmail = new MyApp_Form_Element_Text('username_or_email');
        $usernameOrEmail->setLabel(Zmz_Translate::_('Username or email'))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->setAttrib('class', 'default-field')
                ->addValidator('NotEmpty')
                ->addDecorator(new Zmz_Form_Decorator_JqueryValidator(array(
                            'required' => 'true',
                        )));

        $password = new MyApp_Form_Element_Password('password');

        $password->addDecorator(new Zmz_Form_Decorator_JqueryValidator(array(
                    'required' => 'true',
                )));


        $remember = new Zend_Form_Element_Checkbox('remember');
        $remember->setLabel(Zmz_Translate::_('Keep me logged in'));

        $redirect = new Zend_Form_Element_Hidden('redirect');
        $redirect->addDecorator('Hidden');

        $submit = new Zend_Form_Element_Submit('submits');
        $submit->setLabel(Zmz_Translate::_('Login'));

        $this->addElements(array(
            $usernameOrEmail,
            $password,
            $remember,
            $redirect,
            $submit
        ));
    }

}