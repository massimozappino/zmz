<?php
class TestSuite extends PHPUnit_Framework_TestSuite
{

    public static function suite()
    {
        set_include_path(
            implode(
                PATH_SEPARATOR,
                array(
                    realpath(APPLICATION_PATH . '/../tests/application'),
                    get_include_path(),
                )
            )
        );

        $suite = new PHPUnit_Framework_TestSuite('All tests');

        require_once 'library/Test.php';
        $suite->addTestSuite('Library_Test');
        
        
        return $suite;
    }
}
