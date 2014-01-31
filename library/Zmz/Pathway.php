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
class Zmz_Pathway
{

    protected static $data = array();

    public static function addPath($title, $url = null)
    {
        self::$data[$title] = $url;
    }

    public static function setPathLink($title, $url)
    {
        if (array_key_exists($title, self::$data)) {
            self::$data[$title] = $url;
        }
    }

    /**
     * TODO
     * @param <type> $home
     * @return <type>
     */
    public static function display($home = 'Home page', $separator = ' &gt ')
    {
        $view = Zend_Layout::getMvcInstance()->getView();
        $html = "";
        $elements = array();
        $i = 0;

        if ($home) {
            $elements[$i++] = '<a href="' . $view->baseUrl() . '">' . $home . '</a>';
        }

        foreach (self::$data as $title => $url) {
            if (is_string($url)) {
                $elements[$i++] = '<a href="' . $url . '">' . $title . '</a>';
            } else {
                $elements[$i++] = $title;
            }
        }

        $html = implode($elements, $separator);

        return $html;
    }

}

