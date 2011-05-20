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
class Zmz_Utils
{

    /**
     * Get a tokenized string
     *
     * @param string $string
     * @param string $pattern The delimiter used when splitting up str.
     * @return array $toks
     */
    public static function tokenizer($string, $pattern = null)
    {
        if ($pattern == null) {
            $pattern = " ";
        }
        $toks = array();
        $string = (string) $string;
        $string = trim($string);
        if (strlen($string) == 0)
            return array();
        $tok = strtok($string, $pattern);
        while ($tok !== false) {
            $toks[] = $tok;
            $tok = strtok($pattern);
        }
        return $toks;
    }

    /**
     * Redirect to url
     *
     * @param string $url
     * @throws Zmz_Utils_Exception
     */
    public static function redirect($url)
    {
        if (!headers_sent()) {
            header('Location:' . (string) $url);
            exit();
        } else {
            throw new Zmz_Utils_Exception('Header already sent');
        }
    }

    /**
     * Add zero chars at the left of given integer
     *
     * @param int $int
     * @param int $count
     * @return string
     */
    public static function padInteger($int, $count = 1)
    {
        $int = (int) $int;
        if ($int < 10) {
            $zeros = '';
            for ($i = 0; $i < $count; $i++) {
                $zeros .= '0';
            }
            return (string) $zeros . $int;
        } else {
            return (string) $int;
        }
    }

    /**
     * Remove "script" tag at beginning and at the end of string
     *
     * @param string $js
     * @return string cleared string
     */
    public static function clearScript($js)
    {
        if (!is_string($js)) {
            throw new Zmz_Utils_Exception('"$js" must be a string');
        }
        $js = explode("\n", $js);
        foreach ($js as $k => $v) {
            $row = trim($v);
            if ($row == '' || substr($row, 0, 7) == '<script'
                    || substr($row, 0, 9) == '</script>') {
                unset($js[$k]);
            }
        }
        $result = implode("\n", $js);

        return $result;
    }

    /**
     * Filter string by removing accents
     *
     * @param string $string
     * @return string
     */
    public static function removeAccents($string)
    {
        $table = array(
            'Š' => 'S', 'š' => 's', 'Đ' => 'Dj', 'đ' => 'dj', 'Ž' => 'Z', 'ž' => 'z', 'Č' => 'C', 'č' => 'c', 'Ć' => 'C', 'ć' => 'c',
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
            'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O',
            'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e',
            'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o',
            'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'ý' => 'y', 'þ' => 'b',
            'ÿ' => 'y', 'Ŕ' => 'R', 'ŕ' => 'r',
        );

        return strtr($string, $table);
    }

    /**
     *
     * @param string $id
     * @return string
     */
    public static function stripFormArrayNotation($id)
    {
        if ('[]' == substr($id, -2)) {
            $id = substr($id, 0, strlen($id) - 2);
        }
        $id = str_replace('][', '-', $id);
        $id = str_replace(array(']', '['), '-', $id);
        $id = trim($id, '-');

        return $id;
    }

    public static function getQueryStringFromUrl($url)
    {
        if ($url) {
            list($base, $queryString) = explode('?', $url);
            return $queryString;
        }
        return null;
    }

    public static function getQueryStringArrayFromString($queryString)
    {
        $query = array();

        if ($queryString != '') {
            $array = explode("&", $queryString);
            foreach ($array as $k => $v) {
                $tmp = explode("=", $v, 2);
                $query[$tmp[0]] = $tmp[1];
            }
        }
        
        
        return $query;
    }

}