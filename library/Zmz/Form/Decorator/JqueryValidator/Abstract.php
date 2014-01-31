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
class Zmz_Form_Decorator_JqueryValidator_Abstract extends Zend_Form_Decorator_Abstract
{

    protected $_jquery;
    protected $_validators;

    public function __construct($options = null)
    {
        if (!is_array($options) || count($options) <= 0) {
            throw new Zend_Form_Decorator_Exception('$option is not array or there is\'t any value');
        }
        foreach ($options as $k => $v) {
            if (is_array($v)) {
                $this->_validators[(string) $k] = $v;
            } else {
                $this->_validators[null] = $options;
                break;
            }
        }

        $jqueryHelper = new ZendX_JQuery_View_Helper_JQuery();
        $this->_jquery = $jqueryHelper->jQuery();
    }

    public function render($content)
    {
        $this->_renderJquery();

        return $content;
    }

    public function setJquery(ZendX_JQuery_View_Helper_JQuery $jquery)
    {
        $this->_jquery = $jquery;
        return $this;
    }

    public function getJquery()
    {
        return $this->_jquery;
    }

    protected function _getElementId($suffix)
    {
        $element = $this->getElement();
        $elementId = $element->getId();
        if (strlen($suffix) > 0) {
            $elementId .= '-' . $suffix;
        }
        return $elementId;
    }

    protected function _renderJquery()
    {
        $options = $this->getOptions();
        $js = "";
        foreach ($this->_validators as $suffix => $options) {
            $js .= "$('#" . $this->_getElementId($suffix) . "').rules('add', {\n";
            $i = 0;
            foreach ($options as $k => $v) {
                $i++;

                if ($k == 'messages') {
                    $js .= "    $k: {\n";
                    $j = 0;
                    if (!is_array($v)) {
                        throw new ZendX_JQuery_Exception('"messages:" is not an array');
                    }
                    foreach ($v as $kMes => $kVal) {
                        $j++;
                        $js .= "        $kMes: jQuery.format('" . $kVal . "')";
                        if (count($v) != $j) {
                            $js .= ",";
                        }
                        $js .= "\n";
                    }
                    $js .= "    }\n";
                } else {
                    $js .= "    $k: " . $v;
                }
                if (count($options) != $i) {
                    $js .= ",";
                }
                $js .= "\n";
            }

            $js .= "});\n";
        }

        $this->_jquery->addOnLoad($js);
    }

}
