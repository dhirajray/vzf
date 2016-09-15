<?php
class Admin_Model_Advertrelation extends Zend_Db_Table_Abstract
{
    protected $_name = 'tblAdvertRelation';
	protected $_dbTable;		
	public function setDbTable($dbTable)
    {
		if (is_string($dbTable))
			$dbTable = new $dbTable();
        if (!$dbTable instanceof Zend_Db_Table_Abstract)
            throw new Exception('Invalid table data gateway provided');
        $this->_dbTable = $dbTable;		
        return $this;
    } 
    public function getDbTable()
    {
		if (null === $this->_dbTable)
            $this->setDbTable('Admin_Model_Advertrelation');
        return $this->_dbTable;
    }
    /**
     * insert advert raw data image link and slider parameter
     * @param data = array();
     * return last insert id or false
     */
    public function advertDataInsert($data)
    {
        if ($this->_db->insert($this->_name,$data))
            return $this->_db->lastInsertId();
        else
            return false;
    }
    public function deleteAdvert($advertID)
    {   
        return $this->_db->delete($this->_name, array(
            "clientID='" . clientID . "'","advertID='" . $advertID . "'"
        ));        
    }
     public function getRelationAdvert($advertID)
    {   
        $select = $this->_db->select()          
           ->from(array($this->_name))
           ->where("advertID = ?", $advertID)
           ->where("clientID = ?", clientID)
           ->order('id  DESC');
        return  $this->_db->fetchAll($select);         
    }
}


