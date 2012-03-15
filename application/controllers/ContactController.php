<?php

class ContactController extends MyApp_Controller_Action
{

    public function preDispatch()
    {
        parent::preDispatch();
        $this->view->title = $this->_translate->_('Contact');
        $this->_breadcrumbs->addElement($this->view->title, $this->_helper->url(null), true);
    }

    public function indexAction()
    {
        $form = new Form_Contact();
        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();

            if ($form->isValid($postData)) {
                try {

                    $values = $form->getValidValues($postData);


                    $this->_redirect('url');
                } catch (Exception $e) {

                    throw $e;
                }
            } else {
                Zmz_Messenger::getInstance()->addError(Zmz_Translate::_('Form is not valid'));
            }
        }

        $this->view->form = $form;
    }

}

