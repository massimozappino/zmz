<?php

class Application_Form_Contact extends MyApp_Form
{

    public function init()
    {
        parent::init();

        $translator = Zmz_Translate::getInstance();

        $this->setName('formContact');
        $this->setMethod("post");
        $this->setAttrib("class", "form");

        $name = new Zend_Form_Element_Text('name');
        $name->setLabel(Zmz_Translate::_('Name'))
                ->setRequired(true)
                ->addValidator(new Zend_Validate_Digits())
                ->addDecorator(new Zmz_Form_Decorator_JqueryValidator(array(
                            'required' => 'true',
                        )));

        $email = new Zend_Form_Element_Text('email');
        $email->setLabel(Zmz_Translate::_('Email'))
                ->setRequired(true)
                ->addValidator(new Zmz_Validate_EmailAddressSimple())
                ->addDecorator(new Zmz_Form_Decorator_JqueryValidator(array(
                            'required' => 'true',
                            'email' => 'true',
                        )));

        $message = new Zend_Form_Element_Textarea('message');
        $message->setLabel(Zmz_Translate::_('Message'))
                ->setRequired(true)
                ->addDecorator(new Zmz_Form_Decorator_JqueryValidator(array(
                            'required' => 'true',
                        )));

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel(Zmz_Translate::_('Send'));

        $this->setElements(array(
            $name,
            $email,
            $message,
            $submit
        ));

        $this->setElementsBelongTo('contact');
    }

}
