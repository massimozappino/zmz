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
class Zmz_Tax
{

    private $_taxRate = 21;
    private $_precision = 2;

    /**
     *
     * @param int $taxRate
     * @param int $precision 
     */
    public function _construct($taxRate = null, $precision = null)
    {
        if ($taxRate !== null) {
            $this->setTaxRate($taxRate);
        }

        if ($precision !== null) {
            $this->setPrecision($precision);
        }
    }

    /**
     * 
     * 
     * @param int $taxRate
     * @return Zmz_Tax 
     */
    public function setTaxRate($taxRate)
    {
        $this->_taxRate = $taxRate;
        return $this;
    }

    /**
     *
     * @return int 
     */
    public function getTaxRate()
    {
        return $this->_taxRate;
    }

    /**
     *
     * @param int $precision
     * @return Zmz_Tax 
     */
    public function setPrecision($precision)
    {
        $this->_precision = $precision;
        return $this;
    }

    /**
     *
     * @return int 
     */
    public function getPrecision()
    {
        return $this->_precision;
    }

    /**
     *
     * @param float $amountWithoutTax
     * @return float 
     */
    public function calculateTaxAmount($amountWithoutTax)
    {
        $value = $amountWithoutTax * $this->getTaxRate() / 100;

        return $this->_filterValue($value);
    }

    /**
     *
     * @param float $amountWithTax
     * @return float 
     */
    public function remvoveTax($amountWithTax)
    {
        $value = $amountWithTax * 100 / ($this->getTaxRate() + 100);

        return $this->_filterValue($value);
    }

    /**
     *
     * @param float $amountWithoutTax
     * @return float 
     */
    public function addTax($amountWithoutTax)
    {
        $value = $amountWithoutTax + $this->calculateTaxAmount($amountWithoutTax);
        return $this->_filterValue($value);
    }
    
    /**
     *
     * @param type $value
     * @return float 
     */
    protected function _filterValue($value)
    {
        $precision = $this->getPrecision();
        return round($value, $precision);
    }

}

