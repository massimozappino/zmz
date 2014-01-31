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
require_once 'Zend/Form/Element/Xhtml.php';

class Zmz_Form_Element_String extends Zend_Form_Element_Xhtml
{

    /**
     * Default form view helper to use for rendering
     * @var string
     */
    public $helper = 'formString';

    public function init()
    {
        parent::init();

        $this->setRequired(false)
                ->setIgnore(true);
    }

}
