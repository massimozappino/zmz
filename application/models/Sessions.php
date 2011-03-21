<?php

class Model_Sessions extends Zend_Db_Table_Abstract
{

    protected $_primary = 'session_id';
    protected $_name = 'sessions';

    public function getSession($sessionId)
    {
        if (!$sessionId) {
            return false;
        }
        $select = $this->select()
                        ->from(array('s' => 'sessions'))
                        ->where('s.session_id = ?', $sessionId);
        $row = $this->fetchRow($select);

        return $row;
    }

    public function getSessionByIdUser($userId)
    {
        if (!$userId) {
            return false;
        }
        $select = $this->select()
                        ->from(array('s' => 'sessions'))
                        ->where('s.user_id = ?', $userId);
        $row = $this->fetchRow($select);

        return $row;
    }

    public function deleteSession($sessionId)
    {
        if (!$sessionId) {
            return false;
        }
        $where = $this->getAdapter()->quoteInto('session_id = ?', $sessionId);

        return $this->delete($where);
    }

    public function writeSession($data)
    {
        $result = $this->insert($data);

        return $result;
    }

}