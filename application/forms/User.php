<?php

class Form_User extends Zmz_Form
{

    public function init()
    {
        parent::init();

        $projectConfig = Zend_Registry::get('projectConfig');

        $this->setName('formUser');

        $name = new MyApp_Form_Element_Text('name');
        $name->setLabel(Zmz_Translate::_('Name'))
                ->addValidator(new Zend_Validate_StringLength(array('max' => $projectConfig->max_name_length)))
                ->setAttrib('maxlength', $projectConfig->max_name_length)
                ->setRequired(true);

        $surname = new MyApp_Form_Element_Text('surname');
        $surname->setLabel(Zmz_Translate::_('Surname'))
                ->addValidator(new Zend_Validate_StringLength(array('max' => $projectConfig->max_name_length)))
                ->setAttrib('maxlength', $projectConfig->max_name_length)
                ->setRequired(true);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel(Zmz_Translate::_('Save'));

        $this->addElements(
                array(
                    $name,
                    $surname,
                    $submit
        ));
    }

}

