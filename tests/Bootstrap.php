<?php

defined('BASE_PATH') ? '' : define('BASE_PATH', realpath(dirname(__FILE__) . '/../'));
defined('APPLICATION_PATH') ? '' : define('APPLICATION_PATH', BASE_PATH . '/application');
defined('APPLICATION_ENV') ? '' : define('APPLICATION_ENV', 'testing');

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
            realpath(APPLICATION_PATH . '/../library'),
//            '/usr/local/zend/share/ZendFramework/library',
            get_include_path(),
        )));

date_default_timezone_set('Europe/Rome');

require_once 'Zend/Loader/Autoloader.php';
$loader = Zend_Loader_Autoloader::getInstance();

require_once '../application/Bootstrap.php';
require_once 'ControllerTestCase.php';
