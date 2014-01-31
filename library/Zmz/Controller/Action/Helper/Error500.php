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
class Zmz_Controller_Action_Helper_Error500 extends Zend_Controller_Action_Helper_Abstract
{

    public function direct($message = null)
    {
        if (!is_string($message)) {
            $message = 'Internal server error';
        }
        throw new Zend_Controller_Action_Exception($message, 500);
    }

}

