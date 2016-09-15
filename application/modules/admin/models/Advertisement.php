<?php
class Admin_Model_Advertisement extends Zend_Db_Table_Abstract
{
    protected $_name = 'tblAdvert';
	protected $_dbTable;		
	public function setDbTable($dbTable)
    {
		if (is_string($dbTable)) {
			$dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;		
        return $this;
    } 
    public function getDbTable()
    {
		if (null === $this->_dbTable) {
            $this->setDbTable('Admin_Model_Advertisement');
        }
        return $this->_dbTable;
    }
    public function advertInsert($data)
    {
        if ($this->_db->insert($data))
            return $this->_db->lastInsertId();
        else
            return false;
    }
}


