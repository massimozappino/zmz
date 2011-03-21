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

    protected $_data;
    protected $_error;
    protected $_status;

    public static function success($data)
    {
        return new self($data, null, self::SUCCESS);
    }

    public static function error($error = null)
    {
        if (empty($error)) {
            $error = 'Generic error';
        }
        return new self(null, $error, self::ERROR);
    }

    protected function __construct($data = null, $error = null, $status = null)
    {
        $this->setData($data);
        $this->setError($error);
        $this->setStatus($status);
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

    public function setData($data = null)
    {
        $this->_data = $data;
    }

    public function setError($error = null)
    {
        if ($error instanceof Exception) {
            $errorString = $error->getMessage();
        } else {
            $errorString = (string) $error;
        }

        $this->_error = $errorString;
    }

    public function setStatus($status = null)
    {
        if (null === $status) {
            $status = self::SUCCESS;
        }

        $this->_status = $status;
    }

}
