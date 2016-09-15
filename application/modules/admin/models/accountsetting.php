<?php

class Admin_Model_Accountsetting extends Zend_Db_Table_Abstract
{
     // Table name 
     protected $_name = 'tblUsers';
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
            $this->setDbTable('Admin_Model_DbTable_Accountuser');
        }
        return $this->_dbTable;
    }
	
	
	public function updateProfile($data,$id){
        //echo'<pre>';print_r($data);die;
        $where[] = $this->getAdapter()->quoteInto('clientID = ?', clientID);
        $where[] = $this->getAdapter()->quoteInto('UserID  = ?', $id);
        $db = $this->getDbTable();
	    return $this->_db->update('tblUsers',$data,$where);
	}

  /*public function updatesecque($data,$id){
        $where[] = $this->getAdapter()->quoteInto('clientID = ?', clientID);
        $where[] = $this->getAdapter()->quoteInto('front_userid  = ?', $id);
        $db = $this->getDbTable();
       return $this->_db->update('users',$data,$where);
  }*/

  public function updatesecque($data,$id){
        //echo'<pre>';print_r($data);die;
        $filter = new Zend_Filter_Alnum();
        $chkadminrec = $select = $this->_db->select() ->from('users')->where('front_userid ='.$id)->where('clientID ='.clientID);
        $chkadminresult = $this->_db->fetchAll($chkadminrec);
        if(count($chkadminresult)>0){
            $where[] = $this->getAdapter()->quoteInto('clientID = ?', clientID);
            $where[] = $this->getAdapter()->quoteInto('front_userid  = ?', $id);
            $db = $this->getDbTable();
            return $this->_db->update('users',$data,$where);
        }else{
            $select = $this->_db->select() ->from('tblUsers')->where('UserID ='.$id)->where('clientID ='.clientID);         
            $Detailsofuser = $this->_db->fetchAll($select);
            $datains  =  array('clientID'=>clientID,
                            'email'=>$Detailsofuser[0]['Email'],
                            'secretquestion'=>$data['secretquestion'],
                            'answer'=>$filter->filter($data['answer']),
                            'id_role'=>3,                          
                            'front_userid'=>$id,
                            );
           // echo'<pre>';print_r($datains);die;
          return  $this->_db->insert('users', $datains);
        }
  }

    public function getProfile($id){
        $select = $this->_db->select() 
            ->from('tblUsers')
               ->where('UserID ='.$id)
               ->where('clientID ='.clientID);         
       return $result = $this->_db->fetchAll($select);
    
    }

    public function getProfilesec($id){
        $select = $this->_db->select() 
            ->from('users')
               ->where('front_userid ='.$id)
               ->where('clientID ='.clientID);         
       return $result = $this->_db->fetchAll($select);
    
    }

     public function chkPassword($id,$pwd)
     {
      //echo $pwd;
        $allrec = $this->_db->select()->from('tblUsers')
        ->where('UserID = ?', $id)->where('Pass = ?', $pwd)->where('clientID = ?', clientID);
        //echo $allrec->__toString(); exit;
        return count($this->_db->fetchAll($allrec));
    }
  
    public function update_globaltimsetting($expireTime)
    {
        $where[] = $this->getAdapter()->quoteInto('clientID = ?', clientID);
       
        $data = array('expireTime' => $expireTime);
        if($this->_db->update('tblAdminSettings', $data ,$where))
            {return true;}else{return false;}
    }

    public function siterss()

    {

        $select = $this->_db->select()      

            ->from(array('r' => 'tblRssSites'),array('r.ID','r.URL','r.Name','r.Country','Active','isdefault'))

                ->join(array('e' => 'tblRssCountries'), 'r.Country = e.ID',array('cname' => 'Name'))
                    //->where('r.Active =1')
                        ->where('r.clientID ='.clientID)
                            ->order('r.Country');
            
       $result = $this->_db->fetchAll($select);
       

       return $result;                       

   } 
   public function getcounty(){
   
   	$select = $this->_db->select() 
            ->from('tblRssCountries',array('ID','Name'))
               //->where('clientID ='.clientID)
                 ->order('ID');   
   	     
       $result = $this->_db->fetchAll($select);  
       return $result;
   }
   
   public function deletefeed($id)
   {   	
   	return $allrec = $this->_db->delete('tblRssSites',array('ID =' . (int)$id,'clientID =' . clientID));
   }
   public function deleteall($id)
   {   
   	return $allrec = $this->_db->delete('tblRssSites',array('clientID =' . clientID));
   }
   
   
public function activefeed($data,$id){
       
	    return $this->_db->update('tblRssSites',$data,array('ID =' . (int)$id,'clientID =' . clientID));
	}
   
 	
}