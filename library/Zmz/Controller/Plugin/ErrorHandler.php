<?php

class Zmz_Controller_Plugin_ErrorHandler extends Zend_Controller_Plugin_Abstract
{

 public function routeShutdown (Zend_Controller_Request_Abstract $request)
    {
        $front = Zend_Controller_Front::getInstance();

        // Front controller has error handler plugin
        // if the request is an error request.
        // If the error handler plugin is not registered,
        // we will be unable which MCA to run, so do not continue.
        $errorHandlerClass = 'Zend_Controller_Plugin_ErrorHandler';
        if (!$front->hasPlugin($errorHandlerClass)) {
            return false;
        }

        // Determine new error controller module_ErrorController_ErrorAction
        $plugin = $front->getPlugin($errorHandlerClass);
        $errorController = $plugin->getErrorHandlerController();
        $errorAaction = $plugin->getErrorHandlerAction();
        $module = $request->getModuleName();

        // Create test request module_ErrorController_ErrorAction...
        $testRequest = new Zend_Controller_Request_Http();
        $testRequest->setModuleName($module)
            ->setControllerName($errorController)
            ->setActionName($errorAaction);

        // Set new error controller if available
        if ($front->getDispatcher()->isDispatchable($testRequest)) {
            $plugin->setErrorHandlerModule($module);
        } else {
            return false;
        }

        return true;
    }
}
