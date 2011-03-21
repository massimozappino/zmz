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
class Zmz_Controller_Action extends Zend_Controller_Action
{

    protected $_isJson = false;
    protected $_isXml = false;

    /**
     * Throw a 'Page not found' Exception, error 404
     *
     * @param string $message
     */
    public function error404($message = null)
    {
        $this->_helper->error404($message);
    }

    /**
     * Throw an 'Unauthorized' Exception, error 401
     *
     * @param string|null $message
     */
    public function error401($message = null)
    {
        $this->_helper->error401($message);
    }

    /**
     * Throw an 'Internal server error' Exception, error 500
     *
     * @param string|null $message
     */
    public function error500($message = null)
    {
        $this->_helper->error500($message);
    }

    /**
     * Current action must be an AJAX request
     */
    public function setAjax()
    {
        if (!$this->isAjax()) {
            $this->error404('Request is not AJAX');
        }
    }

    /**
     * Is the request a Javascript XMLHttpRequest?
     *
     * @return boolean
     */
    public function isAjax()
    {
        return $this->_request->isXmlHttpRequest();
    }

    public function isJson()
    {
        return $this->_isJson;
    }

    public function isXml()
    {
        return $this->_isXml;
    }

    public function setXml($ajax = false, $noRender = true)
    {
        $this->_isXml = true;
        if ($ajax) {
            $this->setAjax();
        }
        $this->disableLayout();
        $this->setContentType('text/xml');
        if ($noRender) {
            $this->setNoRender();
        }
    }

    public function setSsl()
    {
        $this->_helper->ssl(true);
    }

    public function unsetSsl()
    {
        $this->_helper->ssl(false);
    }

    /**
     * Specify that the current action return a JSON page.
     *
     * @param boolean $ajax
     * @param boolean $noRender
     */
    public function setJson($ajax = true, $noRender = true)
    {
        $this->_isJson = true;
        if ($ajax) {
            $this->setAjax();
        }
        $this->disableLayout();
        if ($this->getInvokeArg('env') == 'production') {
            $this->setContentType('text/json');
        }
        if ($noRender) {
            $this->setNoRender();
        }
    }

    /**
     * Set content type header
     *
     * @param string $value
     */
    public function setContentType($value = 'text/html')
    {
        $response = $this->getResponse();
        $response->setHeader('Content-type', $value);
    }

    /**
     * Disable view rendering for current action
     */
    public function setNoRender()
    {
        $this->_helper->viewRenderer->setNoRender(true);
    }

    /**
     * Enable view rendering for current action
     */
    public function enableRender()
    {
        $this->_helper->viewRenderer->setNoRender(false);
    }

    /**
     * Disable layout rendering
     */
    public function disableLayout()
    {
        $layout = $this->getLayout();
        $layout->disableLayout();
    }

    /**
     * Enable layout rendering
     */
    public function enableLayout()
    {
        $layout = $this->getLayout();
        $layout->enableLayout();
    }

    /**
     * Set layout rendering
     */
    public function setLayout($name, $enabled = true)
    {
        $layout = $this->getLayout();
        $layout->setLayout($name, $enabled);
    }

    /**
     * Retrieve MVC instance of Zend_Layout object
     *
     * @return Zend_Layout|null
     */
    public function getLayout()
    {
        $layout = Zend_Layout::getMvcInstance();

        return $layout;
    }

    /**
     * Get referer url
     *
     * @param string $default The default redirect url
     * @return string
     */
    public function getReferer($default = null)
    {
        return $this->_helper->getReferer($default);
    }

    /**
     * Get the current Environment
     * 
     * @return string 
     */
    public function getEnvironment()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        $environment = $bootstrap->getEnvironment();

        return $environment;
    }

}

