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

        if (Zmz_Date::getDate() > $dateCode->addHour($projectConfig->activation_link_duration)) {
            $validCode = false;
        }

        if (!$validCode) {
            $code = null;
            $this->clearCode(true);
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

    /**
     * Get the current confirm code and check if the date_code_email is valid
     *
     * @return string
     */
    public function checkEmailCode()
    {
        $projectConfig = Zend_Registry::get('projectConfig');
        $validCode = true;

        $dateCodeEmail = Zmz_Date::getDateFromDb($this->date_code_email);
        $code = $this->code_email;

        if (strlen($code) != $projectConfig->code_length) {
            $validCode = false;
        }

        if (!$this->new_email) {
            $validCode = false;
        }

        if (Zmz_Date::getDate() > $dateCodeEmail->addHour($projectConfig->activation_link_duration)) {
            $validCode = false;
            $this->clearNewEmail(true);
        }

        if (!$validCode) {
            $code = null;
        }

        return $code;
    }

    /**
     * Clear "Change email" parameters
     * @return Model_Row_User
     */
    public function clearNewEmail($save = false)
    {
        $this->new_email = null;
        $this->code_email = null;
        $this->date_code_email = null;
        if ($save) {
            $this->save();
        }

        return $this;
    }

    public function isPasswordValid($password)
    {
        $currentPassword = $this->password;
        $hashedPassword = Model_Users::hashPassword($password, $this->salt);

        if ($currentPassword == $hashedPassword) {
            return true;
        } else {
            return false;
        }
    }

    public function changePassword($password, $save = false)
    {
        $salt = Model_Users::generateSalt();
        $this->password = Model_Users::hashPassword($password, $salt);
        $this->salt = $salt;
        if ($save) {
            $this->save();
        }
    }

    /**
     * Check if user status is confirmed
     */
    public function isConfirmed()
    {
        if ($this->status == Model_Users::STATUS_ACTIVE) {
            return true;
        }
        return false;
    }

}

