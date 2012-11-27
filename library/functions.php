<?php

function dd($value, $vardump = false)
{
    debug($value, $vardump, true);
}

function d($value, $vardump = false)
{
    debug($value, $vardump);
}

function debug($value, $vardump = false, $die = false)
{
    if (APPLICATION_ENV != 'production') {
        echo "<pre>";
        if ($vardump) {
            var_dump($value);
        } else {
            print_r($value);
        }
        echo "</pre>";
        if ($die) {
            die();
        }
    }
}

function dlog($value, $priority = Zend_Log::ALERT)
{
    $log = Zend_Registry::get('log');
    $log->log($value, $priority);
}
