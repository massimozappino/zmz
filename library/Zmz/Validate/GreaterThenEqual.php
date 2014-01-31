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
require_once 'Zend/Validate/GreaterThan.php';

class Zmz_Validate_GreaterThenEqual extends Zend_Validate_GreaterThan
{
        const NOT_GREATER_EQUAL = 'notGreaterThanEqual';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_GREATER_EQUAL => "'%value%' is not greater than or equal to '%min%'",
    );

    
    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value is greater than min option
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->_setValue($value);

        if ($this->_min > $value) {
            $this->_error(self::NOT_GREATER_EQUAL);
            return false;
        }
        return true;
    }
}

