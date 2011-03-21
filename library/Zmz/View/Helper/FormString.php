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
require_once 'Zend/View/Helper/FormElement.php';

class Zmz_View_Helper_FormString extends Zend_View_Helper_Form
{

    public function formString($name, $value = null, $attribs = null)
    {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable
        if (isset($id)) {
            if (isset($attribs) && is_array($attribs)) {
                $attribs['id'] = $id;
            } else {
                $attribs = array('id' => $id);
            }
        }

        // build the element
        $disabled = '';
        if ($disable) {
            // disabled
            $disabled = ' disabled="disabled"';
        }


        $xhtml = '<span'
                . ' id="' . $this->view->escape($id) . '-string"'
                . $disabled
                . $this->_htmlAttribs($attribs)
                . '>'
                . $this->view->escape($value)
                . '</span>';

        return $xhtml . $this->_hidden($name, $value, $attribs);
    }

}
