<?php

class translations
{

    private function translations()
    {
        // default Zend_Validate messages
        // NotEmpty
        _("Value is required and can't be empty");

        // StringLength
        _("'%value%' is less than %min% characters long");
        _("'%value%' is more than %max% characters long");

        // EmailAddress
        _("Email address is not valid");

        // custom messages
        _("Username is not valid");
        _("Username is not available");
        _("Username is too short");
        _("Username is too long");

    }

}

