<?php

class MyApp_Form_Element_Username extends MyApp_Form_Element_Text
{

    public function init()
    {
        parent::init();

        $projectConfig = Zend_Registry::get('projectConfig');

        $this->setLabel(Zmz_Translate::_('Username'))
                ->setRequired(true)
                ->setAttrib('class', 'default-field')
                ->setAttrib('autocomplete', 'off')
                ->setAttrib('maxlength', $projectConfig->max_username_length)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty', true)
                ->addValidator(new MyApp_Validate_Username());
                
    }

}