<?php

class ContactController extends Zmz_Controller_Action
{

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

