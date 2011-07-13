<?php

class AccountController extends Zmz_Controller_Action
{

    public function init()
    {
        parent::init();
        $layout = $this->getLayout();
        $view = $layout->getView();
        $this->getLayout()->sidemenu = $view->partial('account/_menu.phtml');
    }

    public function indexAction()
    {
        Model_Acl::requireLogin();
    }

    public function loginAction()
    {
        $this->view->title = Zmz_Translate::_('Login');

        $redirect = $this->_getParam('redirect');
        if (!$redirect) {
            $redirect = $this->getReferer();
        }
        if (substr_count($redirect, Zmz_Host::getServerUrl() . '/login')) {
            // prevent infinite loop
            $redirect = '/';
        }

        $baseurl = Zmz_Host::getServerUrl() . '/';
        $redirect = str_replace($baseurl, '', $redirect);

        if (Model_Acl::isLogged()) {
            $this->_redirect($redirect);
        }

        $loginForm = new Form_Login();
        $loginForm->getElement('redirect')->setValue($redirect);

        // check whether the login form is submitted
        if ($this->getRequest()->isPost()) {
            if ($loginForm->isValid($this->getRequest()->getPost())) {

                $values = $loginForm->getValues();

                $usernameOrEmail = $values['username_or_email'];
                $password = $values['password'];
                $remember = (bool) $values['remember'];

                $result = $this->_doLogin($usernameOrEmail, $password, false, $remember);
                if (!$result->isValid()) {
                    $usernameOrEmailElement = $loginForm->getElement('username_or_email');
                    $errorMessage = '';
                    switch ($result->getCode()) {
                        case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
                            $errorMessage = Zmz_Translate::_('Invalid username or password');
                            break;
                        case Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS:
                            $errorMessage = Zmz_Translate::_('Account is not activated yet');
                            break;
                    }
                    if ($errorMessage) {
                        Zmz_Messenger::getInstance()->addError($errorMessage);
                    }
                } else {
                    $redirect = $this->getRequest()->getParam('redirect');
                    $this->_redirect($redirect);
                }
            }
        }

        $this->view->loginForm = $loginForm;
    }

    /**
     * Logout and clear adapter
     */
    public function logoutAction()
    {
        $this->disableLayout();
        $this->setNoRender();

        $auth = Model_Acl::getAuthInstance();

        $storage = $auth->getStorage();
        $storage->clear();
        $auth->clearIdentity();
        Zmz_Culture::getAdapter()->resetStorage();

        $url = $this->getReferer();

        $this->_redirect('/');
    }

    public function lostpasswordAction()
    {
        $this->view->title = Zmz_Translate::_('Lost password');

        if (Model_Acl::isLogged()) {
            $this->_redirect('/');
        }

        $db = Zend_Registry::get('db');
        $usersModel = new Model_Users();

        $form = new Form_Lostpassword();

        if ($this->getRequest()->isPost()) {

            $valid = $form->isValid($this->getRequest()->getPost());
            $values = $form->getValues();

            $userRow = $usersModel->findByEmail($values['email']);

            // check if email exists
            if (!$userRow && $valid) {
                $elementEmail = $form->getElement('email');
                $elementEmail->addError(Zmz_Translate::getInstance()->_('Email address not found'));
                $elementEmail->markAsError();
                $form->markAsError();
            }

            // check user status
            if ($userRow && $userRow->status != Model_Users::STATUS_ACTIVE) {
                $form->addError(Zmz_Translate::_('Account is not activated yet'));
                $form->markAsError();
            }

            if (!$form->isErrors() && $valid) {

                try {
                    $db->beginTransaction();

                    $userRow->setCode(true);

                    // Mail activation
                    $view = $this->view;
                    $view->username = $userRow->username;
                    $view->user_id = $userRow->user_id;
                    $view->code = $userRow->code;
                    $view->email = $userRow->email;
                    $bodyText = $view->render('account/email/lostpassword.phtml');

                    $mail = Zmz_Mail::getInstance();
                    $mail->setBodyText($bodyText);
                    $mail->addTo($userRow->email);
                    $mail->setSubject(Zmz_Translate::_('Lost password'));
                    $mail->send();

                    $db->commit();
                } catch (Exception $e) {
                    $db->rollBack();
                    throw new Exception($e->getMessage());
                }
                $this->_redirect('account/lostpasswordcomplete');
            }
        }
        $this->view->form = $form;
    }

    public function lostpasswordcompleteAction()
    {
        $this->view->title = Zmz_Translate::_('Lost password');
        if (Model_Acl::isLogged()) {
            $this->_redirect('/');
        }
    }

    public function changepasswordAction()
    {
        $this->view->title = Zmz_Translate::_('Change password');
        Model_Acl::requireLogin();

        $userRow = Model_Acl::getUserRow();
        $form = new Form_Changepassword();

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();

            if ($form->isValid($postData)) {
                $values = $form->getValues();

                // check old password valid
                if ($userRow->password != Model_Users::hashPassword($values['old_password'], $userRow->salt)) {
                    $elementOldPassword = $form->getElement('old_password');
                    $elementOldPassword->addError(Zmz_Translate::_('Old password is not valid'));
                    $elementOldPassword->markAsError();
                    $form->markAsError();
                }

                // check confirm password
                if ($values['password'] != $values['confirmpassword']) {
                    $elementConfirmpassword = $form->getElement('confirmpassword');
                    $elementConfirmpassword->addError(Zmz_Translate::_('Password do not match'));
                    $elementConfirmpassword->markAsError();
                    $form->markAsError();
                }

                $db = Zend_Registry::get('db');
                if (!$form->isErrors()) {
                    try {
                        $db->beginTransaction();

                        $userRow->changePassword($values['password']);

                        // send notification email
                        $view = $this->view;
                        $view->username = $userRow->username;
                        $bodyText = $view->render('account/email/changepassword.phtml');
                        $mail = Zmz_Mail::getInstance();
                        $mail->setBodyText($bodyText);
                        $mail->addTo($userRow->email);
                        $mail->setSubject(Zmz_Translate::_('Password has changed'));
                        $mail->send();

                        $db->commit();
                        Zmz_Messenger::getInstance()->addSuccess(Zmz_Translate::_('Your password has been changed'), true);

                        $this->_redirect('account');
                    } catch (Exception $e) {
                        $db->rollBack();
                        throw new Exception($e->getMessage());
                    }
                }
            }
        }
        $form->getElement('username')->setValue($userRow->username);
        $form->getElement('email')->setValue($userRow->email);

        $this->view->form = $form;
    }

    public function changeemailAction()
    {
        $this->view->title = Zmz_Translate::_('Change email');
        Model_Acl::requireLogin();

        $userRow = Model_Acl::getUserRow();
        $form = new Form_Changeemail();
        $form->getElement('old_email')->setValue($userRow->email);

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();

            if ($form->isValid($postData)) {
                $values = $form->getValues();

                $usersModel = new Model_Users();

                // check if email exists
                if ($usersModel->checkEmailExists($values['email'])
                        && ($userRow->new_email != $values['email'])) {
                    $elementEmail = $form->getElement('email');
                    $elementEmail->addError(Zmz_Translate::_('Email address already exist'));
                    $elementEmail->markAsError();
                    $form->markAsError();
                }

                // check password
                if (!$userRow->isPasswordValid($values['password'])) {
                    $elementPassword = $form->getElement('password');
                    $elementPassword->addError(Zmz_Translate::_('Password is not valid'));
                    $elementPassword->markAsError();
                    $form->markAsError();
                }

                $db = Zend_Registry::get('db');
                if (!$form->isErrors()) {
                    try {
                        $db->beginTransaction();

                        $userRow->new_email = $values['email'];
                        $userRow->code_email = Model_Users::generateCode();
                        $userRow->date_code_email = Zmz_Date::getSqlDateTime();
                        $userRow->save();

                        // send notification email
                        $view = $this->view;
                        $view->username = $userRow->username;
                        $view->user_id = $userRow->user_id;
                        $view->oldEmail = $userRow->email;
                        $view->newEmail = $userRow->new_email;
                        $view->code = $userRow->code_email;

                        $bodyText = $view->render('account/email/changeemail.phtml');

                        $mail = Zmz_Mail::getInstance();
                        $mail->setBodyText($bodyText);
                        $mail->addTo($userRow->email);
                        $mail->setSubject(Zmz_Translate::_('Confirm your email address'));
                        $mail->send();

                        $db->commit();
                        Zmz_Messenger::getInstance()->addSuccess(Zmz_Translate::_('We have received your request to change email. Please check the instructions that we sent to your inbox to activate your new email'), true);
                        $this->_redirect('account');
                    } catch (Exception $e) {
                        $db->rollBack();
                        throw new Exception($e->getMessage());
                    }
                }
            }
        }

        $this->view->form = $form;
    }

    public function confirmemailAction()
    {
        $this->view->title = Zmz_Translate::_('Confirm email');

        Model_Acl::requireLogin();
        try {
            $db = Zend_Registry::get('db');
            $projectConfig = Zend_Registry::get('projectConfig');

            $code = $this->getRequest()->getParam('code');
            $id = $this->getRequest()->getParam('id');
            if (!$id) {
                throw new Zend_Controller_Action_Exception('Id is null');
            }

            $usersModel = new Model_Users();
            $user = $usersModel->findById($id);

            if (strlen($code) != $projectConfig->code_length) {
                throw new Zend_Controller_Action_Exception('Activation code length is not valid');
            }

            if (!$user) {
                throw new Zend_Controller_Action_Exception('User not found');
            }

            if ($code != $user->checkEmailCode()) {
                throw new Zend_Controller_Action_Exception('Activation code is not valid');
            }
        } catch (Zend_Controller_Action_Exception $e) {
            $messenger = Zmz_Messenger::getInstance();
            $messenger->addError(Zmz_Translate::_("Confirmation code is not valid or has expired"), true);
            $this->_redirect($this->_helper->url('index', 'account'));
        }

        try {
            $db->beginTransaction();
            $user->email = $user->new_email;
            $user->clearNewEmail(false);
            $user->save();

            $db->commit();
            Zmz_Messenger::getInstance()->addSuccess(Zmz_Translate::_("Your email has been successfully changed"), true);

            $this->_redirect($this->_helper->url('changeemail', 'account'));
        } catch (Exception $e) {
            $db->rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function signinAction()
    {
        $this->view->title = Zmz_Translate::_('Sign in');
        if (Model_Acl::isLogged()) {
            $this->_redirect('/');
        }

        $db = Zend_Registry::get('db');

        $form = new Form_Signin();

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();

            if ($form->isValid($postData)) {
                $values = $form->getValidValues($postData);
                $usersModel = new Model_Users();

                // check if username exists
                $userRow = $usersModel->findByUsername($values['username']);
                if ($userRow) {
                    $elementUsername = $form->getElement('username');
                    $elementUsername->addError(Zmz_Translate::_('Username is not available'));
                    $elementUsername->markAsError();
                    $form->markAsError();
                }

                // check confirm password
                $elementConfirmpassword = $form->getElement('confirmpassword');
                if ($values['password'] != $values['confirmpassword']) {
                    $elementConfirmpassword->addError(Zmz_Translate::_('Password do not match'));
                    $elementConfirmpassword->markAsError();
                    $form->markAsError();
                }

                // check if email exists
                if ($usersModel->checkEmailExists($values['email'])) {
                    $elementEmail = $form->getElement('email');
                    $elementEmail->addError(Zmz_Translate::_('Email address already exist'));
                    $elementEmail->markAsError();
                    $form->markAsError();
                }

                if (!$form->isErrors()) {
                    try {
                        $db->beginTransaction();

                        $userRow = $usersModel->add($form->getValues());

                        // Mail activation
                        $view = $this->view;
                        $view->username = $userRow->username;
                        $view->email = $userRow->email;
                        $view->id = $userRow->user_id;
                        $view->code = $userRow->code;
                        $bodyText = $view->render('account/email/activation.phtml');

                        $mail = Zmz_Mail::getInstance();
                        $mail->setBodyText($bodyText);
                        $mail->addTo($userRow->email);
                        $mail->setSubject(Zmz_Translate::_('Sign in'));
                        $mail->send();

                        $db->commit();

                        $this->_redirect('account/registrationcomplete');
                    } catch (Exception $e) {
                        $db->rollBack();
                        throw new Exception($e->getMessage());
                    }
                }
            }
        }
        $this->view->form = $form;
    }

    public function registrationcompleteAction()
    {
        $this->view->title = Zmz_Translate::_('Registration complete');
        if (Model_Acl::isLogged()) {
            $this->_redirect('/');
        }
    }

    public function resetpasswordAction()
    {
        $this->view->title = Zmz_Translate::_('Reset password');
        if (Model_Acl::isLogged()) {
            $this->_redirect('/');
        }

        $db = Zend_Registry::get('db');
        $projectConfig = Zend_Registry::get('projectConfig');

        $id = $this->getRequest()->getParam('id');
        $code = $this->getRequest()->getParam('code');

        $usersModel = new Model_Users();
        // check if code and email are correct then proceed to change password
        if (strlen($code) == $projectConfig->code_length) {
            $userRow = $usersModel->findById($id);

            if ($userRow) {
                if ($userRow->checkCode() == $code) {
                    $form = new Form_Changepassword();
                    $form->setAction($this->_helper->url('resetpassword/id/' . $userRow->user_id . '/code/' . $code));
                    $form->removeElement('old_password');
                    $elementUsername = $form->getElement('username');
                    $elementUsername->setValue($userRow->username);
                    $elementEmail = $form->getElement('email');
                    $elementEmail->setValue($userRow->email);

                    // POST action
                    if ($this->getRequest()->isPost()) {
                        $postData = $this->getRequest()->getPost();

                        if ($form->isValid($postData)) {
                            $values = $form->getValidValues($postData);

                            // check confirm password
                            $elementConfirmpassword = $form->getElement('confirmpassword');
                            if ($values['password'] != $values['confirmpassword']) {
                                $elementConfirmpassword->addError(Zmz_Translate::_('Password do not match'));
                                $elementConfirmpassword->markAsError();
                                $form->markAsError();
                            }

                            if (!$form->isErrors()) {
                                try {
                                    $db->beginTransaction();

                                    $userRow->password = Model_Users::hashPassword($values['password']);
                                    $userRow->setCode();
                                    $userRow->save();

                                    // send notification email
                                    $view = $this->view;
                                    $view->username = $userRow->username;
                                    $bodyText = $view->render('account/email/passwordreset.phtml');

                                    $mail = Zmz_Mail::getInstance();
                                    $mail->setBodyText($bodyText);
                                    $mail->addTo($userRow->email);
                                    $mail->setSubject(Zmz_Translate::_('Password has changed'));
                                    $mail->send();

                                    $this->_doLogin($userRow->username, $userRow->password, true);

                                    $db->commit();

                                    Zmz_Messenger::getInstance()->addInformation(Zmz_Translate::_('Your password has been changed'), true);
                                    $this->_redirect('account/resetpasswordcomplete');
                                } catch (Exception $e) {
                                    $db->rollBack();
                                    throw new Exception($e->getMessage());
                                }
                            }
                        }
                    }
                    $this->view->form = $form;
                    return;
                } else {
                    $this->error404('There is no activation code');
                }
            } else {
                $this->error404('User not found');
            }
        } else {
            $this->error404('Link is not valid or code has expired');
        }
    }

    public function resetpasswordcompleteAction()
    {
        $this->view->title = Zmz_Translate::_('Reset password');

        if (Model_Acl::isLogged()) {
            $this->_redirect('/');
        }
    }

    public function activationAction()
    {
        $this->view->title = Zmz_Translate::_('Account activation');

        if (Model_Acl::isLogged()) {
            $this->_redirect('/');
        }
        try {
            $db = Zend_Registry::get('db');
            $projectConfig = Zend_Registry::get('projectConfig');

            $code = $this->getRequest()->getParam('code');
            $id = $this->getRequest()->getParam('id');
            if (!$id) {
                throw new Exception('Id is null');
            }

            $usersModel = new Model_Users();
            $user = $usersModel->findById($id);

            if (strlen($code) != $projectConfig->code_length) {
                throw new Exception('Activation code length is not valid');
            }

            if (!$user) {
                throw new Exception('User not found');
            }

            if ($user->status == Model_Users::STATUS_ACTIVE) {
                throw new Exception('User is already active');
            }

            if ($code != $user->checkCode()) {
                throw new Exception('Activation code is not valid');
            }
        } catch (Exception $e) {
            $messenger = Zmz_Messenger::getInstance();
            $messenger->addError(Zmz_Translate::_("Activation code has expired"), true);
            $this->_redirect('/');
        }

        try {
            $db->beginTransaction();
            $user->date_activation = Zmz_Date::getSqlDateTime();
            $user->clearCode();
            $user->status = Model_Users::STATUS_ACTIVE;
            $user->save();

            $this->_doLogin($user->username, $user->password, true);

            $db->commit();

            Zmz_Messenger::getInstance()->addSuccess(Zmz_Translate::_("Your account has been activated"), true);
            $this->_redirect($this->_helper->url('index', 'account'));
        } catch (Exception $e) {
            $db->rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function resendactivationAction()
    {
        $this->view->title = Zmz_Translate::_('Resend activation code');
        if (Model_Acl::isLogged()) {
            $this->_redirect('/');
        }
        $form = new Form_Activation();

        if ($this->getRequest()->isPost()) {
            $valid = $form->isValid($this->getRequest()->getPost());
            $values = $form->getValues();

            $usersModel = new Model_Users();

            $elementEmail = $form->getElement('email');

            // check old password valid
            $userRow = $usersModel->findByEmail($elementEmail->getValue());
            if (!$userRow) {
                $elementEmail->addError(Zmz_Translate::_('Email address not found'));
                $elementEmail->markAsError();
                $form->markAsError();
            }

            if ($userRow) {
                if ($userRow->status == Model_Users::STATUS_ACTIVE) {
                    $elementEmail->addError(Zmz_Translate::_('Account already activated'));
                    $form->markAsError();
                }
            }

            if (!$form->isErrors() && $valid) {
                // Mail activation
                $view = $this->view;
                $view->username = $userRow->username;
                $view->email = $userRow->email;
                $view->id = $userRow->user_id;
                $view->code = $userRow->code;
                $bodyText = $view->render('account/email/activation.phtml');

                $mail = Zmz_Mail::getInstance();
                $mail->setBodyText($bodyText);
                $mail->addTo($userRow->email);
                $mail->setSubject(Zmz_Translate::_('Sign in'));
                $mail->send();

                $this->_redirect('account/resendactivationcomplete');
            }
        }

        $this->view->form = $form;
    }

    public function resendactivationcompleteAction()
    {
        $this->view->title = Zmz_Translate::_('Resend activation code');
    }

    public function editAction()
    {
        $this->view->title = Zmz_Translate::_('Edit profile');
        Model_Acl::requireLogin();

        $userRow = Model_Acl::getUserRow();

        $form = new Form_User();
        if ($this->getRequest()->isPost()) {

            $postData = $this->getRequest()->getPost();

            if ($form->isValid($postData)) {
                try {
                    $db = Zend_Registry::get('db');
                    $db->beginTransaction();

                    $values = $form->getValidValues($postData);

                    $userRow->name = $values['name'];
                    $userRow->surname = $values['surname'];
                    $userRow->save();

                    Zmz_Messenger::getInstance()->addSuccess(Zmz_Translate::_('Your profile has been updated'), true);

                    $db->commit();
                    $this->_redirect('/account/edit');
                } catch (Exception $e) {
                    $db->rollback();
                    throw $e;
                }
            }
        } else {
            $form->populate($userRow->toArray());
        }


        $this->view->form = $form;
    }

    public function checkusernameAction()
    {
        $this->setJson();
        $username = $this->getRequest()->getParam('username');
        $form = new Form_Registration();
        $elementUsername = $form->getElement('username');


        $usernameValidator = new MyApp_Validate_Username();
        if (!$usernameValidator->isValid($username)) {
            $errors = $usernameValidator->getMessages();
            foreach ($errors as $e) {
                echo Zend_Json::encode($e);
                return;
            }
        }

        // check if username exists used
        $usersModel = new Model_Users();
        $userRow = $usersModel->findByUsername($username);
        if ($userRow) {
            $msg = Zmz_Translate::_('Username is not available');
            echo Zend_Json::encode($msg);
            return;
        }
        $msg = 'OK';
        echo Zend_Json::encode($msg);
        return;
    }

    /**
     *
     * @param string $usernameOrEmail
     * @param string $password
     * @param boolean $isHashPassword
     * @return Zend_Auth_Result
     */
    protected function _doLogin($usernameOrEmail, $password, $isHashPassword = false, $remember = false)
    {
        $auth = MyApp_Auth::getInstance();
        $auth->setStorage(new MyApp_Auth_Storage_Database());

        // Create object $authAdapter of class Model_AuthAdapter
        $authAdapter = new Model_AuthAdapter($usernameOrEmail, $password, (bool) $isHashPassword, $remember, 'database');

        // Try to authenticate and check whether its valid
        $result = $auth->authenticate($authAdapter);

        return $result;
    }

}
