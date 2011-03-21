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
class Zmz_Xml
{
    const XML_ATTRIBUTES = 'attributes';

    public static function encode($data, $rootNodeName = 'data')
    {
        $xml = self::_recursiveXmlEncode($data, $rootNodeName);
        $dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = false;

        return $dom->saveXML();
    }

    public static function prettyPrint($data, $rootNodeName = 'data')
    {
        $xml = self::_recursiveXmlEncode($data, $rootNodeName);
        $dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = true;

        return $dom->saveXML();
    }

    protected static function _recursiveXmlEncode($valueToEncode, $rootNodeName = 'data', &$xml = null)
    {
        if (null == $xml) {
            $xml = new SimpleXMLElement('<' . $rootNodeName . '/>');
        }


        foreach ($valueToEncode as $key => $value) {
            if (is_numeric($key)) {
                $key = $rootNodeName;
            }
            if ($key == self::XML_ATTRIBUTES) {
                foreach ($value as $attrName => $attrValue) {
                    $xml->addAttribute($attrName, $attrValue);
                }
            } else {
                // Filter non valid XML characters
                $key = preg_replace('/[^a-z0-9\-\_\.\:]/i', '', $key);

                if (is_array($value)) {

                    $node = self::_isAssoc($value) ? $xml->addChild($key) : $xml;

                    self::_recursiveXmlEncode($value, $key, $node);
                } else {
                    $value = htmlspecialchars($value, null, 'UTF-8');
//                    $value = htmlentities($value, null, 'UTF-8');
                    $xml->addChild($key, $value);
                }
            }
        }

        return $xml;
    }

    public static function toArray($obj, &$arr = null)
    {
        if (null == $arr) {
            $arr = array();
        }
        if (is_string($obj)) {
            $obj = new SimpleXMLElement($obj);
        }
        $attributes = $obj->attributes();
        foreach ($attributes as $attrib => $value) {
            $arr[self::XML_ATTRIBUTES][$attrib] = (string) $value;
        }

        $children = $obj->children();
        $executed = false;
        foreach ($children as $elementName => $node) {
            if ($arr[$elementName] != null) {
                if ($arr[$elementName][0] !== null) {
                    $i = count($arr[$elementName]);
                    self::toArray($node, $arr[$elementName][$i]);
                } else {
                    $tmp = $arr[$elementName];
                    $arr[$elementName] = array();
                    $arr[$elementName][0] = $tmp;
                    $i = count($arr[$elementName]);
                    self::toArray($node, $arr[$elementName][$i]);
                }
            } else {
                $arr[$elementName] = array();
                self::toArray($node, $arr[$elementName]);
            }
            $executed = true;
        }
        if (!$executed && $children->getName() == "" && !isset($arr[self::attr_arr_string])) {
            $arr = (String) $obj;
        }
        return $arr;
    }

    private static function _isAssoc($array)
    {
        return (is_array($array) && 0 !== count(array_diff_key($array, array_keys(array_keys($array)))));
    }

}