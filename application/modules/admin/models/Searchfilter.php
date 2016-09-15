<?php

class Admin_Model_Searchfilter  extends Zend_Db_Table_Abstract
{
     // Table name 
     protected $_name = 'tblSearchFilter';
   
	 
	 protected $_dbTable;
	 
	  protected $_dbTable1;
			
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
            $this->setDbTable('Admin_Model_DbTable_Searchfilter');
        }
        return $this->_dbTable;
    }
	
	 public function getDbTable1()
    {
		if (null === $this->_dbTable1) {
            $this->setDbTable('Admin_Model_DbTable_SearchfilterAttr');
        }
        return $this->_dbTable;
    }
	
	
	/*
		@ Function is responsible for :
		@ Taking All records according to searching query etc....
		@ Author Deepak Nagar
		@ Date 18 -Apr 2013
	*/ 
	public function insertfilter($data)  
	{
		$db = $this->getDbTable();
	
		$db->insert($data);
		$lastInsertId = $this->getAdapter()->lastInsertId();
		return $lastInsertId;
		
	}
	
	public function insertfilterattr($data)  
	{
		$db = $this->getDbTable1();
		$db->insert($data);
	}
	/*
		@ Function is responsible for :
		@ Taking All records according to searching query etc....
		@ Author Deepak Nagar
		@ Date 19 -Apr 2013
	*/ 
	public function loadFilters($userid='',$filter_tag='')  
	{
		$db = $this->getDbTable();

		if($filter_tag!='') $condi = 'filt.searchtag = 1'; else $condi = 'filt.searchtag = 0'; 
		
		$select 	= 	$db->select();

		
		$select->from( array('filt' => 'tblSearchFilter'));
		$select->setIntegrityCheck( false );
		//$select->join( array('attr' => 'tblSearchAttr'), 'filt.filter_id = attr.filter_id');
		$select->where('filt.filter_created_by =?',$userid);
		$select->where('filt.clientID =?',clientID);
		$select->where($condi);
		
		$result= $db->fetchAll($select)->toarray();
	 	
	 	return $result;	
		
	}

	public function loadFilterAttr($filter_id='')  
	{
		$db = $this->getDbTable1();
		
		$select 	= 	$db->select();
		
		$select->from( array('attr' => 'tblSearchAttr'));
		$select->setIntegrityCheck( false );
		//$select->join( array('attr' => 'tblSearchAttr'), 'filt.filter_id = attr.filter_id');
		$select->where('attr.filter_id =?',$filter_id);
		$select->where('attr.clientID =?',clientID);
		
		$result = $db->fetchAll($select)->toarray();
		
	 	
	 	return $result;	
		
	}
	
	
	
	
	
}