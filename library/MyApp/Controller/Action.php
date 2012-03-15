<?php

class MyApp_controller_Action extends Zmz_Controller_Action
{

    /**
     *
     * @var Zmz_Translate
     */
    protected $_translate;

    /**
     *
     * @var Zmz_Breadcrumbs 
     */
    protected $_breadcrumbs;

    /**
     *
     * @var Zmz_Messenger
     */
    protected $_messenger;

    public function init()
    {
        parent::init();

        $this->_url = $this->_helper->getHelper('Url');

        $this->_messenger = Zmz_Messenger::getInstance();

        $this->_translate = Zmz_Translate::getInstance();

        $this->_breadcrumbs = Zmz_Breadcrumbs::getInstance()
                ->addElement($this->_translate->_('Home'), $this->view->url(array(), 'default', true));
    }

}

