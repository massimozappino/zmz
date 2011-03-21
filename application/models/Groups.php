<?php

class Model_Groups extends Zend_Db_Table_Abstract
{
    const GUEST     = 1;
    const USER      = 2;
    const ADMIN     = 100;

    protected $_primary = 'group_id';
    protected $_name = 'groups';


}