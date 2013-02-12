<?php

class MyApp_View_Helper_Title extends Zend_View_Helper_Abstract
{

    public function title()
    {
        $html = '';
        $view = Zend_Layout::getMvcInstance()->getView();
        if ($view->title) {
            $html = '<h1>' . $view->title . '</h1>';
        }
        
        return $html;
    }

}
