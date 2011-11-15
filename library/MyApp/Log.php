<?php

class MyApp_Log
{

    /**
     *
     * @param type $message
     * @param type $priority
     * @return Zend_Log 
     */
    public static function log($message, $priority)
    {

        $logger = self::getLogger();
        return $logger->log($message, $priority);
    }

    public static function getLogger()
    {
        $logger = Zend_Registry::get('logger');
        return $logger;
    }

}

