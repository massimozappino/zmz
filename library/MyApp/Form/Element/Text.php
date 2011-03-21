<?php

class MyApp_Form_Element_Text extends Zend_Form_Element_Text
{

    public function init()
    {
        parent::init();
        $this->setAttrib('class', 'default-field');
    }

}