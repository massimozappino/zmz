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
class Zmz_View_Helper_CaptureJavascript extends Zend_View_Helper_Placeholder
{

    protected $_placeholder = null;
    protected $_buffer = null;

    public function captureJavascript()
    {
        return $this;
    }

    protected function getPlaceholder()
    {
        if (is_null($this->_placeholder)) {
            $this->_placeholder = parent::placeholder('customJavascript');
        }
        return $this->_placeholder;
    }

    protected function unsetPlaceholder()
    {
        $this->_placeholder = null;
    }

    public function start()
    {
        $placeholder = $this->getPlaceholder();
        $this->_buffer = $placeholder->captureStart(Zend_View_Helper_Placeholder_Container_Abstract::SET);
    }

    public function end()
    {
        $placeholder = $this->getPlaceholder();
        $placeholder->captureEnd();

        $this->unsetPlaceholder();

        return $placeholder->__toString();
    }

}
