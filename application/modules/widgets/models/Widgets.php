<?php
class Widgets_Model_Widgets 
{
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
	            $this->setDbTable('Widgets_Model_DbTable_Widgets');
	        }
	        return $this->_dbTable;
	    }

	    public function checkcode($codemod)
	    {
	        $db = $this->getDbTable();
        	$select = $db->select()->where('rand_code = ?', $codemod)->where('clientID = ?', clientID);
       		
       		$data = $db->fetchRow($select);
		    return $data;
	    }

    	

      public function getresult($randcode)
      {
        $db = $this->getDbTable();
        $select = $db->select()->where('rand_code = ?', $randcode)->where('clientID = ?', clientID);
        $rows = $db->fetchRow($select);

       

	        if(count($rows) > 0)
	        {
		        $keyid=$rows['key_id']; 

		        if($rows['type']==0)
		        {
		        	$selectgrp = $db->select()->setIntegrityCheck( false );
			        $selectgrp->from(array('a'=>'tblGroups'),array('a.ID','a.clientID','a.GroupName','a.GroupPic','a.GroupDesc'));       
			        $selectgrp->where('a.clientID= ?',clientID);
			        $selectgrp->where('FIND_IN_SET(ID,(?))', $keyid);
			        //$selectgrp->where("FIND_IN_SET('".$keyid."',a.ID)");
			        //echo $selectgrp->__toString();
       				//die; 
			        return $db->fetchAll($selectgrp);
		        }
		        if($rows['type']==1)
		        {
		        	$selectpost = $db->select()->setIntegrityCheck( false );
			        $selectpost->from(array('a'=>'tblDbees'),array('a.DbeeID','a.Text','a.Type','a.surveyTitle','a.Pic','a.PollText','a.dburl'));        
			        $selectpost->where('a.clientID= ?',clientID);
			        $selectpost->where('FIND_IN_SET(DbeeID,(?))', $keyid);
			        return $db->fetchAll($selectpost);
		        }
	    	}


      }
}


    
    

    
    

