<?php

class MyApp_Form_Element_Email extends MyApp_Form_Element_Text
{

    public function init()
    {
        parent::init();

        $projectConfig = Zend_Registry::get('projectConfig');

        $this->setLabel(Zmz_Translate::_('Email'))
                ->setAttrib('maxlength', $projectConfig->max_email_length)
                ->setAttrib('class', 'default-field')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator(new MyApp_Validate_EmailAddress());
    }

}