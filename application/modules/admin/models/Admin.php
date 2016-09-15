<?php

class Admin_Model_Admin extends Zend_Db_Table_Abstract
{
     // Table name 
     protected $_name = 'users';
     // Schema name
     // protected $_use_adapter = "matjarna";
	 
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
            $this->setDbTable('Admin_Model_DbTable_Admin');
        }
        return $this->_dbTable;
    }
	
	  
	  
	  public function getAdmin() {
		$select= $this->select();
    	$result= $this->fetchAll($select);		
		return $result;		
    }
	
	public function addAdmin() {
    			
    	$added_date = date();
		$db = $this->getDbTable();
        $data = array(
            'ad_name' => $ad_name,
            'ad_email' => $ad_email,
			'ad_password' => $ad_password,
			'ad_acc_create_date' => $added_date,			
        );
        $db->insert($data);	
    }
	

  
	
	
	
	
}