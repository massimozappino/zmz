<?php

/**
 * Zmz
 *
 * LICENSE
 *
 * This source file is subject to the GNU GPLv3 license that is bundled
 * with this package in the file COPYNG.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @copyright  Copyright (c) 2010-2011 Massimo Zappino (http://www.zappino.it)
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU GPLv3 License
 */
require_once 'ZendX/JQuery/Form/Element/DatePicker.php';

class Zmz_Form_Element_DatePicker extends ZendX_JQuery_Form_Element_DatePicker
{

    private $_i18nDirectory = '/jquery/i18n';
    private $_localization = false;

    public function setJqueryI18nDirectory($directory)
    {
        $this->_i18nDirectory = $directory;
        return $this;
    }

    public function getJqueryI18nDirectory()
    {
        return $this->_i18nDirectory;
    }

    public function setLocalization($localization)
    {
        $this->_localization = $localization;
        return $this;
    }

    public function getLocalization()
    {
        return $this->_localization;
    }

    public function render(Zend_View_Interface $view = null)
    {
        if ($this->getLocalization()) {
            if (!$view) {
                $view = Zend_Layout::getMvcInstance()->getView();
            }
            $view->headScript()->appendFile($this->getJqueryI18nDirectory() . '/jquery.ui.datepicker-' . Zmz_Culture::getLanguage() . '.js');
        }
        return parent::render($view);
    }

}
