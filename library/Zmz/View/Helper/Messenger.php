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
class Zmz_View_Helper_Messenger extends Zend_View_Helper_Abstract
{

    public function messenger()
    {
        $messenger = Zmz_Messenger::getInstance();
        $html = '';
        if ($messenger->count()) {
            foreach ($messenger->readMessages() as $k => $v) {
                $html .= $this->_draw($k, $v);
            }
        }
        return $html;
    }

    protected function _draw($id, $messages)
    {

        $html = '<div class="' . $id . '">' . "\n";
        $html .= "    <ul>\n";
        foreach ($messages as $k => $v) {
            $html .= '        <li>' . $v . "</li>\n";
        }
        $html .= "    </ul>\n";

        $html .= '</div>' . "\n";

        return $html;
    }

}

