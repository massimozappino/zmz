<?php

class MyApp_Helper_Messenger extends Zmz_View_Helper_Messenger
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
        $html = '<div class="alert-message ' . $id . '">' . "\n";
        $html .= '<a class="close" href="#" onclick="javascript:closeMessenger();">x</a>' . "\n";

        foreach ($messages as $k => $v) {
            $html .= '        <p class="bold">' . $v . "</p>\n";
        }

        $html .= '</div>' . "\n";

        return $html;
    }

}

