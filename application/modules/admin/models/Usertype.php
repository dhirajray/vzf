<?php
class Admin_Model_Usertype  extends Zend_Db_Table_Abstract
{     
	protected $_name = 'tblUserType';	 
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
            $this->setDbTable('Admin_Model_Usertype');
        }
        return $this->_dbTable;
    }
	
    public function getusertype() 
    {
        $db = $this->getDbTable();
        $select = $db->select()
	        ->setIntegrityCheck( false )
		        ->from(array('ut'=>'tblUserType'),array('ut.TypeName','ut.TypeID','ut.Typeref','ut.defaultvip'))
                    ->joinleft( array('u' => 'tblUsers'), 'ut.TypeID=u.usertype', array(count(u.usertype)=>'usertype'))
	                    ->where('ut.defaultvip = ?','0')
	    		       		->where('ut.clientID = ?',clientID)
		    		       		->group('ut.TypeID')
		            				->order('ut.TypeID desc');         
        return $db->fetchAll($select);
    }
    public function chktype($type)
    {
    	$db = $this->getDbTable();
    	$select = $db->select()
	    	->setIntegrityCheck( false )
		    	->from('tblUserType',array('TypeName','TypeID','Typeref'))
			    	->where('TypeName = ?',$type)
			    		->where('clientID = ?',clientID);
    	$data = $db->fetchAll($select);
    	if(count($data)>0)
    		return $data;
    	else
    		return false;
    }
    public function addusrtype($data)
    {
    	 if ($this->_db->insert('tblUserType', $data))
            return $this->_db->lastInsertId();
        else
            return false;
    }
	public function dellusertype($usertype){
    	
    	$del =  $this->_db->delete('tblUserType', array('TypeID = '.$usertype));
    	return;
    
    }
    public function getusertypesearch($usertype)
    {
    	$select = $this->_db->select()
	    	->from('tblUserType',array('TypeID','TypeName'))
	    		->where('TypeName LIKE ?', "%$usertype%")
    				->where('clientID = ?', clientID);  
    	//echo $select->__toString();   	
    return	$data = $this->_db->fetchAll($select);
    
    }
    public function chkusertype($usertype,$typeid=0)
    {
    	$select = $this->_db->select();
	    	$select->from('tblUserType',array('TypeID','TypeName'));
	    		$select->where('TypeName = ?', $usertype);
	    		if($typeid!=0){
	    		$select->orWhere('TypeID = ?', $typeid);
    			}
    				$select->where('clientID = ?', clientID);
    	//echo $select->__toString();
    	
    	return	$data = $this->_db->fetchAll($select);
    	
    }

    

    public function updateusrtype($usertype,$id)
    {
    	return $allrec = $this->_db->update('tblUserType',$usertype,array(
        'TypeID = ?' => $id,
        'clientID = ?' => clientID
        ));
    }
    public function dellinkurl($id)
    {
        return $allrec = $this->_db->delete('tblUserType',array(
        'TypeID = ?' => $id,
        'clientID = ?' => clientID
        ));
    }
}