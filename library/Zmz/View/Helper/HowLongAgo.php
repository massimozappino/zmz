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
class Zmz_View_Helper_HowLongAgo extends Zend_View_Helper_Abstract
{

    public function howLongAgo($date, $ago = null)
    {
        if ($ago === null) {
            $ago = Zmz_Translate::_('ago');
        }
        $string = Zmz_Date::getHowLongAgo($date, $ago);
        $html = '<span title="'
                . Zmz_Date::printDate($date, Zmz_Date::getLocaleDateTimeFormat('medium'))
                . '">' . $string . '</span>';

        return $html;
    }

}

