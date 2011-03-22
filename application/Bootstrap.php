<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    protected function _initAutoload()
    {

        /* Zend Autoloader */
        require_once 'Zend/Loader/Autoloader.php';

        $loader = Zend_Loader_Autoloader::getInstance()
                        ->setFallbackAutoloader(true);

        $moduleLoader = new Zend_Application_Module_Autoloader(array(
                    'namespace' => '',
                    'basePath' => APPLICATION_PATH
                ));

        return $moduleLoader;
    }

    protected function _initCustomItems()
    {
        require_once APPLICATION_PATH . '/../library/functions.php';
    }

    protected function _initView()
    {
        $this->bootstrap('layout');
        $layout = $this->getResource('layout');

        $view = $layout->getView();
        $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer($view);
        Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);

        $view->setEncoding('UTF-8');
        $view->doctype('XHTML1_STRICT');
        $view->headMeta()->appendHttpEquiv(
                'Content-Type', 'text/html;charset=utf-8'
        );

        // add global script path
        $view->addScriptPath(APPLICATION_PATH . '/views/scripts');

        // add global helpers
        $view->addHelperPath(APPLICATION_PATH . '/views/helpers', 'Zend_View_Helper_');

        //add Jquery helpers
        $view->addHelperPath("ZendX/JQuery/View/Helper", "ZendX_JQuery_View_Helper");

        // add Zmz helpers
        $view->addHelperPath("Zmz/View/Helper", "Zmz_View_Helper");
    }

    protected function _initActionHelpers()
    {
        Zend_Controller_Action_HelperBroker::addPrefix('Zmz_Controller_Action_Helper');
    }

    /**
     * Add required routes to the router
     */
    protected function _initRoutes()
    {
        $this->bootstrap('frontController');
        $front = $this->frontController;
        $front->setBaseUrl('/');
        $router = $front->getRouter();

        $filename = APPLICATION_PATH . '/configs/routes.ini';
        if (file_exists($filename)) {
            $routerConfig = new Zend_Config_Ini($filename, $this->getEnvironment(), false);
            Zend_Registry::set('routerConfig', $routerConfig);
        } else {
            throw new Exception('File "' . $filename . '" not found');
        }

        if (isset($routerConfig->routes)) {
            $router->addConfig($routerConfig, 'routes');
        }
    }

    /**
     * Initialize the layout loader
     */
    protected function _initLayoutHelper()
    {
        $layout = Zend_Controller_Action_HelperBroker::addHelper(
                        new Zmz_Controller_Action_Helper_LayoutLoader());
    }

    /**
     * Add the config to the registry
     */
    protected function _initConfig()
    {
        Zend_Registry::set('config', $this->getOptions());
    }

    public function _initDatabase()
    {
//        $this->bootstrap('db');
//        $db = $this->getPluginResource('db')->getDbAdapter();
//        Zend_Db_Table::setDefaultAdapter($db);

        $config = $this->getOptions();
        $options = $config['resources']['db'];


        $params = $config['resources']['db']['params'];

        $options['params'] += $params;
        $dbResource = new Zend_Application_Resource_Db($options);
        $db = $dbResource->getDbAdapter();
        Zend_Db_Table::setDefaultAdapter($db);

        Zend_Registry::set('db', $db);
    }

    /**
     * Initialize culutre: locale and timezone
     */
    protected function _initCulture()
    {
        // cookie adapter
        /*
          $httpCookieObject = new Zend_Http_Cookie('culture', null, 'test');
          $cookie = new Zmz_Cookie($httpCookieObject);
          $adapter = new Zmz_Culture_Adapter_Cookie($cookie);
         */

        // session adapter
        $session = new Zend_Session_Namespace('culture');
        $adapter = new Zmz_Culture_Adapter_Session($session);

        Zmz_Culture::setAdapter($adapter);

        // set always default locale
        Zmz_Culture::setCulture(null, null, false);
    }

    /**
     * Initialize translation
     */
    public function _initTranslate()
    {
        $language = (string) Zmz_Culture::getLocale()->getLanguage();
        $languagePath = APPLICATION_PATH . '/../language';

        // default translator
        $translator = new Zend_Translate(
                        'gettext',
                        $languagePath . '/en/en.mo',
                        'en'
        );

        try {
            $translator->addTranslation(
                    $languagePath . '/' . $language . '/' . $language . '.mo',
                    $language
            );
        } catch (Exception $e) {
            // no translation found
        }

        Zmz_Translate::getInstance()->setTranslator($translator);
        Zend_Registry::set('Zend_Translate', $translator);
    }

    /**
     * Configure Mail component
     */
    protected function _initMail()
    {
        $filename = APPLICATION_PATH . '/configs/mail.ini';
        if (file_exists($filename)) {
            $mailConfig = new Zend_Config_Ini($filename, $this->getEnvironment());
            Zend_Registry::set('mailConfig', $mailConfig);
        } else {
            throw new Exception('File "' . $filename . '" not found');
        }

        $config = array(
            'auth' => $mailConfig->auth,
            'username' => $mailConfig->username,
            'password' => $mailConfig->password,
            'ssl' => $mailConfig->ssl,
            'port' => $mailConfig->port
        );

        $tr = new Zend_Mail_Transport_Smtp($mailConfig->mailserver, $config);
        Zmz_Mail::setDefaultTransport($tr);
        Zmz_Mail::setDefaultCharset($mailConfig->charset);
        Zmz_Mail::setDefaultFrom($mailConfig->sender_email, $mailConfig->sender_name);
    }

    /**
     * Read project configuration file and put data into a global variable
     */
    protected function _initProjectConfig()
    {
        $filename = APPLICATION_PATH . '/configs/project.ini';
        if (file_exists($filename)) {
            $projectConfig = new Zend_Config_Ini($filename, $this->getEnvironment());
            $config = $projectConfig->toArray();
        } else {
            throw new Exception('File "' . $filename . '" not found');
        }

        if (!is_array($config)) {
            throw new Exception('$tmpArrayConfig is not an array');
        }

        $projectConfig = new Zmz_Object($config);
        $projectConfig->setThrowException(true);

        Zend_Registry::set('projectConfig', $projectConfig);
    }

    protected function _initDefaultTimezone()
    {
        $projectConfig = Zend_Registry::get('projectConfig');
        $defaultTimezone = $projectConfig->default_timezone;

        date_default_timezone_set($defaultTimezone);
    }

    /**
     * Must be the last bootstrap method
     */
    protected function _initLastBootstrapMethod()
    {
        Model_Acl::initDefaultStorage();

        if (Model_Acl::isLogged()) {
            $userLocale = Model_Acl::getUserLocale();
            $userTimezone = Model_Acl::getUserTimezone();

            Zmz_Culture::setCulture($userLocale, $userTimezone, true);
        }
    }

}

