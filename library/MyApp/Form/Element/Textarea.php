<?php

class MyApp_Form_Element_Textarea extends Zend_Form_Element_Textarea
{

    public function init()
    {
        parent::init();
        $this->setAttrib('class', 'default-field');
        $this->setAttrib('rows', 10);
    }

}