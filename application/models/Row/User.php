<?php

class Model_Row_User extends Zend_Db_Table_Row
{

    public function saveCulture($locale, $timezone)
    {
        $this->locale = $locale;
        $this->timezone = $timezone;
        $id = $this->save();

        return $id;
    }

}

