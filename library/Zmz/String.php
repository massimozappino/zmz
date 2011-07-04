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
class Zmz_String
{

    /**
     *
     * @param string $fullText
     * @param int $limit
     * @return string
     */
    public static function trimToWords($fullText, $limit)
    {
        $textfield = strtok($fullText, " ");
        $text = '';
        $words = 0;
        while ($textfield) {
            $text .= " $textfield";
            $words++;
            if ($words >= $limit) {
                break;
            }

//            if (($words >= $limit) && ((substr($textfield, -1) == "!")
//                    || (substr($textfield, -1) == "."))) {
//                break;
//            }
            $textfield = strtok(" ");
        }

        return trim($text);
    }

    /**
     * Format string using Regex Patterns
     *
     * @param string
     * @return string
     */
    public static function format($str)
    {
        //count arguments that our function received
        $count_args = func_num_args();

        //check if we have sufficient arguments
        if ($count_args == 1) {
            return $str;
        }

        //find all ocurrences that matches the pattern {(numbers)}
        //and copy them to an auxiliary array named $indexes
        //we'll use PREG_SET_ORDER so that we can get a pair of values
        //with all the matches found,
        //for example: array[y]=array([0]=>"{x}", [1]=>"x");
        preg_match_all('/\{(\d+)\}/', $str, $indexes, PREG_SET_ORDER);

        $count = sizeof($indexes);

        //looping through our $indexes will give us
        //the elements to replace with
        for ($i = 0; $i < $count; $i++) {
            $arr = $indexes[$i];

            //what will we replace, for example {x} (on which x=([0-9]+)
            $replace = $arr[0];

            //get argument value that will replace our {x}
            $arg_pos = $arr[1] + 1;

            // check if we have a valid argument
            if ($arg_pos > - 1 && $arg_pos < $count_args) {

                //get the argument value
                $arg_value = func_get_arg($arg_pos);

                //replace {x} with the value of specific argument position
                $str = str_replace($replace, $arg_value, $str);
            }
        }

        return $str;
    }

    /**
     * Format string using only str_replace
     *
     * @param string
     * @return string
     */
    public static function formatSimpler($str)
    {
        //count arguments that our function received
        $count_args = func_num_args();

        //check if we have sufficient arguments
        if ($count_args == 1) {
            return $str;
        }

        for ($i = 0; $i < $count_args - 1; $i++) {
            //get the argument value
            $arg_value = func_get_arg($i + 1);

            //replace {$i} with the value of specific argument position
            $str = str_replace("{{$i}}", $arg_value, $str);
        }

        return $str;
    }

    public static function slugify($text)
    {
        // replace all non letters or digits by -
        $text = preg_replace('/\W+/', '-', $text);

        // trim and lowercase
        $text = strtolower(trim($text, '-'));

        return $text;
    }

    /**
     * Determines whether the beginning of a string matches a specified string.
     * 
     * @param string $baseString
     * @param string $startString
     * @return boolean 
     */
    public static function startsWith($baseString, $startString)
    {
        if (substr($baseString, 0, strlen($startString)) == $startString) {
            return true;
        }
        return false;
    }

}