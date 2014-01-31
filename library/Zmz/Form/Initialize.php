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
class Zmz_Form_Initialize
{

    public static function init(Zend_Form $form)
    {
        $form->addPrefixPath('Zmz_Form_', 'Zmz/Form/');

//        $form->addElementPrefixPath('Zmz_Form_Decorator_', 'ZMZ/Form/Decorator/', 'decorator');
    }

}

