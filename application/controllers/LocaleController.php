<?php

class LocaleController extends MyApp_Controller_Action
{

    public function indexAction()
    {
        if ($this->isAjax()) {
            $this->disableLayout();
        } else {
            $this->view->title = Zmz_Translate::_('Choose your language');
        }

        $form = new Form_Locale();

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();

            if ($form->isValid($postData)) {
                $values = $form->getValidValues($postData);

                $locale = $values['locale'];
                $timezone = $values['timezone'];

                Zmz_Culture::setCulture($locale, $timezone, true);
                if (Model_Acl::isLogged()) {
                    $userRow = Model_Acl::getUserRow();
                    $userRow->saveCulture(Zmz_Culture::getLocale(true), Zmz_Culture::getTimezone(true));
                }
                $this->_redirect($this->getReferer());
            }
        }
        $this->view->form = $form;
    }

}
