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

    /**
     * Get the current activation code and check if the date_code is valid
     *
     * @return string
     */
    public function checkCode()
    {
        $projectConfig = Zend_Registry::get('projectConfig');
        $validCode = true;

        $dateCode = Zmz_Date::getDateFromDb($this->date_code);
        $code = $this->code;

        if (strlen($code) != $projectConfig->code_length) {
            $validCode = false;
        }

        if (Zmz_Date::getDate() > $dateCode->addHour(24)) {
            $validCode = false;
        }

        if (!$validCode) {
            $code = null;
            $this->code = $code;
            $this->date_code = null;
            $this->save();
        }

        return $code;
    }

    /**
     * Set a new activation code for the user
     *
     * @return Model_Row_User
     */
    public function setCode($save = false)
    {
        $code = Model_Users::generateCode();
        $this->code = $code;
        $this->date_code = Zmz_Date::getSqlDateTime();
        if ($save) {
            $this->save();
        }
        return $this;
    }

    /**
     * Clear activation code
     * @return Model_Row_User 
     */
    public function clearCode($save = false)
    {
        $this->code = null;
        $this->date_code = null;
        if ($save) {
            $this->save();
        }

        return $this;
    }

    public function isPasswordValid($password)
    {
        $currentPassword = $this->password;
        $hashedPassword = Model_Users::hashPassword($password);

        if ($currentPassword == $hashedPassword) {
            return true;
        } else {
            return false;
        }
    }

}

