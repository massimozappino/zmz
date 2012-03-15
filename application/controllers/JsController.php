<?php

class JsController extends MyApp_Controller_Action
{

    public function preDispatch()
    {
        parent::preDispatch();
        $this->disableLayout();
        $this->setNoRender();
        $this->setContentType('application/javascript');
    }

    public function zmzAction()
    {
        $js = $this->view->render('js/zmz.phtml');
        $js = Zmz_Utils::clearScript($js);
        echo $js;
    }

}
