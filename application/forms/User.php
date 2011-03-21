<?php

class Form_User extends Zmz_Form
{

    public function init()
    {
        parent::init();

        $projectConfig = Zend_Registry::get('projectConfig');

        $this->setName('formUser');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel(Zmz_Translate::_('Save'));

        $this->addElements(
                array(
                    $submit
        ));
    }

}

