<?php

class MyApp_Form_Element_Password extends Zend_Form_Element_Password
{

    public function init()
    {
        parent::init();

        $projectConfig = Zend_Registry::get('projectConfig');

        $this->setLabel(Zmz_Translate::_('Password'))
                ->setRequired(true)
                ->setAttrib('class', 'default-field')
                ->setAttrib('maxlength', $projectConfig->max_password_length)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty', true)
                ->addValidators(array(
                    array(
                        'validator' => 'stringLength',
                        'options' => array($projectConfig->min_password_length, $projectConfig->max_password_length)
                    )
                ));
    }

}