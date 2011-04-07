<?php

class MyApp_Utils
{

    public static function getSelectStringForSelect()
    {
        return array('' => '---' . strtolower(Zmz_Translate::_('Select')) . '---');
    }

}

