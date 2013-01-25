<?php
class Library_Test extends PHPUnit_Framework_TestSuite
{

    public static function suite()
    {
        require_once 'Zend/VersionTest.php';

        $suite = new self('Library');

        $suite->addTest(new PHPUnit_Framework_TestSuite('Zend_VersionTest'));

        return $suite;
    }
}
