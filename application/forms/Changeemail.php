<?php

class Form_Changeemail extends Zmz_Form
{

    public function init()
    {
        parent::init();

        $this->setName('formChangeemail');

        $email = new MyApp_Form_Element_Email('email');
        $email->setLabel(Zmz_Translate::_('Email'))
                ->setDescription(Zmz_Translate::_('Will never be displayed'));

        $password = new MyApp_Form_Element_Password('password');
        $password->setLabel(Zmz_Translate::_('Password'))
                                ->setDescription(Zmz_Translate::_('Enter your account password'));



        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel(Zmz_Translate::_('Change email'));

        $this->addElements(
                array(
                    $email,
                    $password,
                    $submit
        ));
    }

}

