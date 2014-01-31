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
class Zmz_View_Helper_AddJquery extends ZendX_JQuery_View_Helper_UiWidget
{

    public function addJquery($js)
    {
        $script = (string) $js;
        $script = Zmz_Utils::clearScript($script);
        $this->jquery->addOnLoad($script);
    }

}