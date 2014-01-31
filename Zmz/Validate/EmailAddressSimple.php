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
require_once 'Zend/Validate/Abstract.php';

class Zmz_Validate_EmailAddressSimple extends Zend_Validate_Abstract
{
    const NOT_VALID = 'notValid';



    protected $_messageTemplates = array(
            self::NOT_VALID => 'E-mail address is not valid'
    );


    public function isValid($email)
    {
        $valid = true;

        $pattern = '/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])' .
                '(([a-z0-9-])*([a-z0-9]))+'
        . '(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i';

        if (!preg_match($pattern, $email)) {
            $valid = false;
        }

        if (!$valid) {
            $this->_error(self::NOT_VALID);
        }

        return $valid;
    }
}
