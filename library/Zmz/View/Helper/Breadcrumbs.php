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
class Zmz_View_Helper_Breadcrumbs extends Zend_View_Helper_Abstract
{

    private $_data = array();

    public function breadcrumbs()
    {
        $this->_data = Zmz_Breadcrumbs::getInstance()->getData();
        $html = $this->getHtml();

        return $html;
    }

    public function getHtml()
    {
        $count = count($this->_data);
        $i = 0;
        $html = '';
        if (count($this->_data)) {
            $html .= '<ul class="breadcrumb">';
            foreach ($this->_data as $title => $value) {
                $i++;
                $active = @$value['active'] ? 'class="active"' : '';
                $html .= "<li {$active}>";
                if (@$value['url']) {
                    $html .= '<a href="' . @$value['url'] . '">' . $title . '</a>';
                } else {
                    $html .= $title;
                }
                $html .= '</li>';
                if ($i < $count) {
                    $html .= '<span class="divider">/</span>';
                }
            }
            $html .= '</ul>';
        }
        return $html;
    }

}

