<?php

abstract class Zmz_View_Helper_JsFormAbstract extends ZendX_JQuery_View_Helper_JQuery
{
    protected $formName = null;
    protected $elementPrefix = null;

    public function __construct(Zend_Form $form)
    {
        parent::__construct();
        $this->view = Zend_Layout::getMvcInstance()->getView();

        $this->formName = $form->getName();

        $elementsBelongTo = $form->getElementsBelongTo();

        $this->elementPrefix = '';
        if ($elementsBelongTo) {
            $this->elementPrefix = Zmz_Utils::stripFormArrayNotation($elementsBelongTo);
            $this->elementPrefix .= '-';
        }

        $this->init();
    }

    protected function init()
    {
        throw new Exception('init() function must be declared');
    }
}

