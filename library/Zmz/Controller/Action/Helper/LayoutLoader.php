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
class Zmz_Controller_Action_Helper_LayoutLoader extends Zend_Controller_Action_Helper_Abstract
{

    public function preDispatch()
    {
        $bootstrap = $this->getActionController()->getInvokeArg('bootstrap');
        if ($bootstrap == null) {
            return;
        }
        $config = $bootstrap->getOptions();
        $module = $this->getRequest()->getModuleName();

        if (isset($config[$module]['resources']['layout']['layout'])) {
            $layoutScript = $config[$module]['resources']['layout']['layout'];
        } else {
            $layoutScript = $config['resources']['layout']['layout'];
        }

        $this->getActionController()
                ->getHelper('layout')
                ->setLayout($layoutScript);
    }

}