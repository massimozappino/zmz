<?php

class MyApp_Helper_Messenger extends Zend_View_Helper_Abstract
{

    public function messenger()
    {
        $messenger = Zmz_Messenger::getInstance();
        $html = '';
        if ($messenger->count()) {
            foreach ($messenger->readMessages() as $k => $v) {
                $html .= $this->_draw($k, $v);
            }
        }
        return $html;
    }

    protected function _draw($id, $messages)
    {
        $html = '<div class="alert ' . $id . '">' . "\n";
        $html .= '<a class="close" data-dismiss="alert">&times;</a>' . "\n";

        foreach ($messages as $k => $v) {
            $html .= '        <p class="bold">' . $v . "</p>\n";
        }

        $html .= '</div>' . "\n";

        return $html;
    }

}