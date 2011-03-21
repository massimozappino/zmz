<?php

class MyApp_Form_Element_String extends Zmz_Form_Element_String
{

    public function init()
    {
        parent::init();

        $this->setAttrib('class', 'default-field');
    }

}