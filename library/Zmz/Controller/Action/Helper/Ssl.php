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
require_once 'Zend/Controller/Action/Helper/Abstract.php';

class Zmz_Controller_Action_Helper_Ssl extends Zend_Controller_Action_Helper_Abstract
{

    public function direct($ssl)
    {
        $toScheme = $ssl ? 'https' : 'http';

        $scheme = Zmz_Host::getScheme();
        if ($scheme != $toScheme) {
            $request = $this->getRequest();
            $url = Zmz_Host::buildUrl(Zmz_Host::getHostname(), $toScheme, Zmz_Host::getPort());
            $url .= $request->getRequestUri();
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
            $redirector->gotoUrl($url);
        }
    }

}

