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
class Zmz_Translate
{

    /**
     * Instance for singleton
     *
     * @var Zmz_Translate
     */
    protected static $_instance;

    /**
     *
     * @var Zend_Translate
     */
    protected $translator;

    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    /**
     * Singleton
     */
    protected function __construct()
    {
        
    }

    public static function _($str, $escape = false)
    {
        $instance = self::getInstance();

        $translated = $instance->getTranslator()->_($str);

        if ($escape) {
            $translated = str_replace("'", "\'", $translated);
            $translated = str_replace('"', '\"', $translated);
        }

        return $translated;
    }

    /**
     * 
     * @param Zend_Translate $translator
     */
    public function setTranslator(Zend_Translate $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Get translator object.
     *
     * @return Zend_Translate $translator
     */
    public function getTranslator()
    {
        if (!$this->translator) {
            throw new Zmz_Translate_Exception('Translator is not set');
        }

        return $this->translator;
    }

}

