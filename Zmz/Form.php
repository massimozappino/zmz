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
class Zmz_Form extends ZendX_JQuery_Form
{

    protected $_jquery;
    protected $_jqueryValidatorOptions = array();
    protected $_enableRenderJqueryValidator = true;
    protected $_jqueryValidatePlugin = '/jquery/js/jquery.validate.pack.js';
    protected $_jqueryValidateExtendedPlugin = '/jquery/jquery-validate/additional-methods.js';
    protected $_jqueryFormPlugin = '/jquery/js/jquery.form.js';

    public function __construct($options = null)
    {
        Zmz_Form_Initialize::init($this);
        $jqueryHelper = new ZendX_JQuery_View_Helper_JQuery();
        $this->_jquery = $jqueryHelper->jQuery();
        parent::__construct($options);
    }

    /**
     * Set attribute "formId" for each element in the form.
     * The Zmz_Form_Decorator_JqueryValidator::render() function need the
     * "formId" attribute.
     *
     * @param string $formId
     * @return Zmz_Form
     */
    public function setFormIdOnElements()
    {
        foreach ($this->getElements() as $k => $v) {
            $v->setOptions(array(
                'formId' => $this->getName()
            ));
        }
        return $this;
    }

    /**
     * Render form
     *
     * @param  Zend_View_Interface $view
     * @return string
     */
    public function render(Zend_View_Interface $view = null)
    {
        if ($this->isEnableRenderJqueryValidator()) {
            $this->_jquery->addJavascriptFile($this->_jqueryValidatePlugin);
            $this->_jquery->addJavascriptFile($this->_jqueryValidateExtendedPlugin);

            $js = '' . $this->getJqueryValidatorVar() . ' = $("#' . $this->getId() . '").validate(' . $this->renderJqueryValidatorOptions() . ');';
            $this->_jquery->addOnLoad(@$js);
        }

        $content = parent::render($view);
        return $content;
    }

    public function getJqueryValidatorVar()
    {
        $formId = $this->getId();
        if (!$formId) {
            throw new Zend_Form_Exception("Cannot render jqueryValidator without the form ID");
        }

        return $formId . 'Validator';
    }

    public function setJqueryValidatorOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $this->addJqueryValidatorOption($key, $value);
        }

        return $this;
    }

    public function addJqueryValidatorOption($key, $value)
    {
        $this->_jqueryValidatorOptions[$key] = $value;
        $this->setEnableRenderJqueryValidator(true);

        return $this;
    }

    public function getJqueryValidatorOptions()
    {
        return $this->_jqueryValidatorOptions;
    }

    public function clearJqueryValidatorOptions()
    {
        $this->_jqueryValidatorOptions = array();

        return $this;
    }

    public function renderJqueryValidatorOptions()
    {
        $content = "";
        $options = $this->getJqueryValidatorOptions();
        $countOptions = count($options);
        if ($countOptions) {
            $content .= "{";
            $counter = 0;
            foreach ($this->getJqueryValidatorOptions() as $key => $value) {
                $counter++;
                $content .= "\n" . $key . ': ' . $value;
                if ($counter < $countOptions) {
                    $content .= ",";
                }
            }
            $content .= "\n}";
        }

        return $content;
    }

    public function setEnableRenderJqueryValidator($flag)
    {
        $this->_enableRenderJqueryValidator = (bool) $flag;
        return $this;
    }

    public function isEnableRenderJqueryValidator()
    {
        return $this->_enableRenderJqueryValidator;
    }

    public function ajaxSubmit($callback, $params = array())
    {
        $this->setEnableRenderJqueryValidator(true);
        $this->_jquery->addJavascriptFile($this->_jqueryFormPlugin);

        $dataType = "'json'";
        if (isset($params['dataType'])) {
            $dataType = $params['dataType'];
            unset($params['dataType']);
        }

        $submitHandlerString = "function(form) {
                            try {
				$(form).ajaxSubmit({\n";
        $submitHandlerString .= 'dataType:  ' . $dataType . ",\n";
        foreach ($params as $k => $v) {
            $submitHandlerString .= "$k: $v\n,";
        }
        $submitHandlerString .= "success:   $callback\n";
        $submitHandlerString .= "});
                            } catch(err) {
                                return false;
                            }
                        }";

        $this->addJqueryValidatorOption('submitHandler', $submitHandlerString);
        return $this;
    }

    public function setJqueryValidatePlugin($url)
    {
        $this->_jqueryValidatePlugin = $url;
    }

    public function setJqueryValidateExtendedPlugin($url)
    {
        $this->_jqueryValidateExtendedPlugin = $url;
    }

    public function setJqueryFormPlugin($url)
    {
        $this->_jqueryFormPlugin = $url;
    }

    public static function getSelectStringForSelect($string = null)
    {
        if ($string === null) {
            $string = strtolower(Zmz_Translate::_('Select'));
        }
        return array('' => '---' . $string . '---');
    }

}
