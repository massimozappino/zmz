<?php

$environment = 'development';
//$environment = 'production';

if (!defined('APPLICATION_ENV')) {
    define('APPLICATION_ENV', $environment);
}
