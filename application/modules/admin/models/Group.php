<?php
class Admin_Model_Group  extends Zend_Db_Table_Abstract
{     
	protected $_name = 'tblGroups';	 
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
            $this->setDbTable('Admin_Model_Group');
        }
        return $this->_dbTable;
    }
	
    public function searchGroup($query,$limit,$vipgroup) 
    {
        $db = $this->getDbTable();
        $select = $db->select();
        $select->setIntegrityCheck( false );
        $select->from( array('group' => 'tblGroups'),array('ID'=>'group.ID','GroupName'=>'group.GroupName'));
        $select->where('GroupName LIKE "'.$query.'%"')->where('group.clientID = ?', clientID);
        if($vipgroup==1)
            $select->where('GroupType =?',4);
        $select->order('group.GroupDate DESC')->limit($limit,0);
        return $db->fetchAll($select);
    }
    public function getGroupByID($id)
    {
        $select = $this->_db->select();
        $select->from(array(
            'g' => 'tblGroups'
        ));
        $select->join(array(
            'u' => 'tblUsers'
        ), 'g.User =u.UserID');
        $select->where("g.ID =?", $id)->where('g.clientID = ?', clientID);
        return $this->_db->fetchRow($select);
    }
    public function getVIPGroupByID($id)
    {
        $select = $this->_db->select();
        $select->from(array(
            'g' => 'tblGroups'
        ));
        $select->where("g.ID =?", $id)->where('g.clientID = ?', clientID);
        return $this->_db->fetchRow($select);
    }
}