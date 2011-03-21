<?php

class MyApp_Auth_Storage_NonPersistent extends Zend_Auth_Storage_NonPersistent
{
    public function read()
    {
        $data = array(
            'user_id' => $this->_data['user_id']
        );
        return $data;
    }
}
