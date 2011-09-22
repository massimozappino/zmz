<?php

class IndexController extends Zmz_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $amountWithTax = 100;
        $tax = new Zmz_Tax();
        $tax->setTaxRate(21)->setPrecision(100);
        d($tax->remvoveTax($amountWithTax));
        dd($tax->addTax($tax->remvoveTax($amountWithTax)));
    }

}

