<?php
class Application_Model_DbUserbiography extends Application_Model_DbTable_Master
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
			$this->setDbTable('Application_Model_DbTable_DbUserbiography');
		}
		return $this->_dbTable;
	}
	
	public function auserbiodetail($UserID)
	{     
	    $db = $this->getDbTable();
		$select = $db->select()->where('UserID = ?', $UserID)->where('clientID = ?', clientID);
		$result = $db->fetchAll($select);
		return $result->toArray();
	}
	
	public function editauserbiodetail($data)
	{	
		
	  $db = $this->getDbTable();
	  $UserID =  (int)$data['UserID'];
	  $result = $this->auserbiodetail($UserID);	 
	
	  if((count($result[0])>0)){
		$where	=	$this->getDbTable()->getAdapter()->quoteInto('UserID=?',$result[0]['UserID']); 
		$allrec	=	$this->getDbTable()->update($data,$where);
	  }
	  else{	  	
	    return $allrec	=	$this->getDbTable()->insert($data); 
	  }
	  
	}


	public function getbiofieldsname($id)
	{
		$select = $this->_db->select();
		$select->from('tbl_biofields')->where('clientID = ?', clientID)->where("id =?",$id);
		$data = $this->_db->fetchRow($select);
		return $data['name'];
	}

	public function getbiofieldsvalue2($UserID,$FieldId)
	{     
	    $select = $this->_db->select();
		$select->from('tblUserBiography')->where('UserID = ?', $UserID)->where('clientID = ?', clientID)->where('field_id = ?', $FieldId);
		$data = $this->_db->fetchRow($select);
		return $data['field_value'];
	}

	public function getbiofieldsvalue($UserID,$FieldCode)
    {
        $select = $this->_db->select()
            ->from(array('c' => 'tblUserBiography'),array('c.*'))
                ->join(array('d' => 'tbl_biofields'),'c.field_id = d.id', array('d.*'))
                    ->where("d.field_code = ?",$FieldCode)->where("c.clientID = ?",clientID)
                    ->where("c.UserID = ?",$UserID);
        $data = $this->_db->fetchRow($select); 
        return $data['field_value'];
    }

	 public function updateinfouser($data)
     {

        $result = $this->auserbiodetail($data['UserID']);

		if(!empty($result))
		{
			$where	=	$this->getDbTable()->getAdapter()->quoteInto('UserID=?',$data['UserID']); 
			$allrec	=	$this->getDbTable()->update($data,$where);
		}
		else{
			return $allrec	=	$this->getDbTable()->insert($data);
		}
        
    }

    public function updateExpertBio($UserID,$field_id,$field_value)
    {
    	$result = $this->auserbiodetail($UserID);
		if(!empty($result))
		{
			$where[] =	$this->getDbTable()->getAdapter()->quoteInto('UserID=?',$UserID); 
			$where[] =	$this->getDbTable()->getAdapter()->quoteInto('field_id=?',$field_id); 
			$where[] =	$this->getDbTable()->getAdapter()->quoteInto('clientID=?',clientID); 
			$this->getDbTable()->update(array('field_value'=>$field_value),$where);
		}else
		{
			$data['UserID'] = $UserID;
			$data['field_id'] = 3;
			$data['field_value'] = $field_value;
			$data['clientID'] = clientID;
			$this->getDbTable()->insert($data);
		}
    }
	
	
}
