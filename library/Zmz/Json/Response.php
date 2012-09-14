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
class Zmz_Json_Response
{

    const SUCCESS = 1;
    const ERROR = 0;
    const DEFAULT_CODE = 0;

    protected $_data;
    protected $_error;
    protected $_status;
    protected $_code;

    public static function success($data, $code = null)
    {
        return new self($data, null, self::SUCCESS, $code);
    }

    public static function error($error = null, $code = null)
    {
        return new self(null, $error, self::ERROR, $code);
    }

    protected function __construct($data = null, $error = null, $status = null, $code = null)
    {
        $this->setData($data);
        $this->setError($error);
        $this->setStatus($status);
        $this->setCode($code);
    }

    public function __toString()
    {
        return $this->toJson();
    }

    public function toJson()
    {
        $status = $this->getStatus();
        $json = array();
        if ($status == self::SUCCESS) {
            $response['data'] = $this->getData();
        } else {
            $response['error'] = $this->getError();
        }

        $response['status'] = $status;
        $response['code'] = $this->getCode();
        $response['timestamp'] = time();

        $json = Zend_Json::encode($response);
        return $json;
    }

    public function getData()
    {
        return $this->_data;
    }

    public function getError()
    {
        return $this->_error;
    }

    public function getStatus()
    {
        return $this->_status;
    }

    public function getCode()
    {
        return $this->_code;
    }

    public function setData($data)
    {
        $this->_data = $data;
    }

    public function setError($error)
    {
        if (Zmz_Utils::isDebug()) {
            if ($error instanceof Exception) {
                $errorString = $error->getMessage();
            }
        } else {
            if ($error instanceof Zmz_Error_Exception) {
                $errorString = $error->getMessage();
            }
        }

        if (empty($errorString)) {
            $errorString = 'Application error';
        }
//
//        $errorString = (string) $error;
//
//
//        if ($error instanceof Zmz_Json_Response_Exception) {
//            $errorString = $error->getMessage();
//        }
//
//
//        if ($error instanceof Exception) {
//            $errorString = $error->getMessage();
//        } else {
//            $errorString = (string) $error;
//        }

        $this->_error = $errorString;
    }

    public function setStatus($status)
    {
        if (null === $status) {
            $status = self::SUCCESS;
        }

        $this->_status = $status;
    }

    public function setCode($code)
    {
        if (null === $code) {
            $code = self::DEFAULT_CODE;
        }
        $this->_code = $code;
    }

}
