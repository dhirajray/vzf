<?php

class Admin_Model_Usergroup  extends Zend_Db_Table_Abstract
{
     
	protected $_name = 'usersgroup';
	 
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
            $this->setDbTable('Admin_Model_DbTable_User');
        }
        return $this->_dbTable;
    }
    
    function init(){
    
    	$this->myclientdetails = new Admin_Model_Clientdetails();
    }
	
    public function insertdata_group($data){    	
    	$insertdb = $this->_db->insert('usersgroup', $data);    	
    	return $last_insertedId=$this->_db->lastInsertId();
    }
    public function insertdata_addusergroup($data){
    	
    	//$insertdb = $this->_db->insert('usersgroupid', $data);
    	//return;
       $query = 'INSERT INTO usersgroupid ('.implode(',',array_keys($data)).') VALUES ('.implode(',',array_fill(1, count($data), '?')).') ON DUPLICATE KEY UPDATE '.implode(' = ?,',array_keys($data)).' = ?';
    	
        $this->_db->query($query,array_merge(array_values($data),array_values($data)));
    	return;
    }
    
    public function list_group($limit=20, $Offset=0){	
		$select = $this->_db->select()			
			->from(array('g'=>'usersgroup'))
				->joinLeft(array('gu' => 'usersgroupid'), 'g.ugid = gu.ugid', array('id',"cnt" => "COUNT(DISTINCT(gu.id))"))
					->where('g.clientID = ?', clientID)
						->group('g.ugid')
							->order('g.ugid Desc');
								//->limit($limit, $Offset);		
		return $result = $this->_db->fetchAll($select);			
    }
    
    public function list_groupall($limit=20, $Offset=0){
    	$select = $this->_db->select()
    	->from(array('g'=>'usersgroup'))
    	//->joinLeft(array('gu' => 'usersgroupid'), 'g.ugid = gu.ugid', array('id',"cnt" => "COUNT(DISTINCT(gu.id))"))
    	->where('g.clientID = ?', clientID)    	
    	->order('g.ugid Desc');
    	//->limit($limit, $Offset);       
    	return $result = $this->_db->fetchAll($select);
    }
    
    
    public function searchUser_group($query,$limit){   
        $select = $this->_db->select()          
            ->from(array('g'=>'usersgroup'))
                  ->where('ugname LIKE "'.$query.'%"')
	                  ->where('g.clientID = ?', clientID)
	                            ->limit($limit, 0);          
        return $result = $this->_db->fetchAll($select);         
    }
    
    public function groupdetailbyid($id,$limit=20, $Offset=0){
    	$select = $this->_db->select();
    		$select->distinct('u.UserID')->from( array('g'=>'usersgroup',array('ugname','ugid','ugdis')))
	    		->joinLeft(array('gu'=>'usersgroupid'), 'g.ugid = gu.ugid',array('userid'))
	    			->joinLeft(array('u'=>'tblUsers'), 'u.UserID = gu.userid',array('UserID','Email','Name','lname'))
	    				->where("g.ugid = ?", $id)
		    				->where('g.clientID = ?', clientID)
		    					->limit($limit, $Offset);
    	//echo $select->__toString();
    	return $result = $this->_db->fetchAll($select);
    }

    public function groupuserbygroup($id){
        $select = $this->_db->select();
            $select->from(array('gu'=>'usersgroupid',array('userid')))
                ->joinLeft(array('g'=>'usersgroup'), 'g.ugid = gu.ugid')                   
                        ->where("gu.ugid = ?", $id)
                            ->where('g.clientID = ?', clientID);
                                
        //echo $select->__toString();exit;
        return $result = $this->_db->fetchAll($select);
    }
    
    
    public function groupdetailbyidtotla($id){
    	$select = $this->_db->select()
    		->from( array('g'=>'usersgroup',array('ugname','ugid','ugdis')))
    			->joinLeft(array('gu'=>'usersgroupid'), 'g.ugid = gu.ugid',array('userid'))
    				->joinLeft(array('u'=>'tblUsers'), 'u.UserID = gu.userid',array('UserID','Email','Name','lname'))
    					->where("gu.ugid = ?", $id)->where("gu.clientID = ?", clientID);    
    	return $result = count($this->_db->fetchAll($select));
    }
    public function deleteusergroup($groupid, $userid)
    {
    	$this->_db->delete('usersgroupid', array(
    			"ugid='" . $groupid . "'",
    			"userid='" . $userid . "'",
    			"clientID='" . clientID . "'"
    	));
    	return true;
    }
    public function deletegroup($groupid)
    {
    	$this->_db->delete('usersgroup',"ugid='". $groupid."'");
    	$this->_db->delete('usersgroupid',"ugid='". $groupid."'");
    	return true;
    }
    public function deletegroupuser($groupid)
    {
    	$this->_db->delete('usersgroupid', array(
    			"ugid='" . $groupid ,
    				"clientID='" . clientID . "'"));
    	return true;
    }
    public function updategroup($groupname,$groupid)
    {
    	return $allrec = $this->_db->update('usersgroup',$groupname,'ugid='.$groupid);
    }

    public function searchgroupuseruser($keyword,$calling=''){
    	
    	$keyword ='%'.$keyword.'%';
    	$select = $this->_db->select()
	    	->from( array('u'=>'tblUsers',array('UserID','Name','lname','Username','ProfilePic','lname')))    
	    		->where("u.Status = ?", '1');
                    if($calling=='twitter')
                        $select->where('twitterTag = 0');
                    if($calling=='promoted')
                        $select->where('promoted = 0');
                        if(clientID==21){
                            $select->where('Socialtype = 5');
                        }else if($calling=='Roleani'){
                            $select->where('Socialtype = 0');
                        }
                        if($calling=='company'){
                        $select->where("u.company Like ?", $keyword)->where("u.clientID = ?", clientID);
                        }
                        else if($calling=='title'){
                        $select->where("u.title Like ?", $keyword)->where("u.clientID = ?", clientID);
                        }else{
		    		    $select->where("u.clientID = ?", clientID)
			    		   ->where("(u.Username Like ?", $keyword)
				    		  ->orWhere("u.full_name Like ?)", $keyword);
                        }
                            //  echo $select->__toString();die;
    	return $this->_db->fetchAll($select);
    }

    public function getusercompany()
    {
            $select = $this->_db->select();
            $select->from('tblusers',array('company')); 
            $select->where('clientID = ?', clientID);
            $select->group('company');
            $data = $this->_db->fetchAll($select); 
            //echo'<pre>';print_r($data);die;
            $usercompany = array();
            foreach($data as $user){
                //echo'<pre>';print_r($user);die;
                $usercompany['id']=$user['company'];
                $usercompany['text']=$user['company'];
                $userSub[] = $usercompany;
            }
            
            return json_encode($userSub);      
    }
    public function getusertitle()
    {
            $select = $this->_db->select();
            $select->from('tblusers',array('UserID','title'));            
            $select->where('clientID = ?', clientID);
            $data = $this->_db->fetchAll($select); 
            //echo'<pre>';print_r($data);die;
            $usertitle = array();
            foreach($data as $user){
                //echo'<pre>';print_r($user);die;
                $usertitle['id']=$user['UserID'];
                $usertitle['text']=$user['title'];
                $userSub[] = $usertitle;
            }
            
            return json_encode($userSub);      
    }

    public function searchuserrole($keyword){
        
        $keyword ='%'.$keyword.'%';
        $select = $this->_db->select()
            ->from( array('u'=>'tblUsers',array('UserID','Name','lname','Username','ProfilePic','lname')))    
                ->where("u.Status = ?", '1')
                  ->where('u.role NOT IN(?)', '1')
                    ->where("u.clientID = ?", clientID)
                        ->where("(u.Username Like ?", $keyword)
                            ->orWhere("u.full_name Like ?)", $keyword);
        return $result = $this->_db->fetchAll($select);
    }


    public function userGroupbyid($id){
        $select = $this->_db->select();
            $select->from( array('g'=>'usersgroup',array('ugname','ugid','ugdis')))
                ->join(array('gu'=>'usersgroupid'), 'g.ugid = gu.ugid',array('userid'))
                    ->join(array('u'=>'tblUsers'), 'u.UserID = gu.userid',array('UserID','Email','Name','lname'))
	                    ->where("gu.clientID = ?", clientID)
	                        ->where("gu.ugid = ?", $id);
            
        return $result = $this->_db->fetchRow($select);
    }
    
    public function getuserbytag($tag){
    	$select = $this->_db->select()
	    	->from('tblDbees','User')
		    	->where('DbTag = ?',$tag)
		    		->where('clientID = ?', clientID);    
    	//echo $select->__toString();	    	
    	 $result = $this->_db->fetchAll($select);
    	 foreach ($result as $data):
    	 	$useid[] = $data['User'];
    	 endforeach;
    	 
    	 return $useid;
    }
    
    public function getuserbytitle($title){
    	$select = $this->_db->select()
	    	->from('tblUsers','UserID')
		    	->where('title = ?',$title)
		    		->where('clientID = ?', clientID);    	
    	$result = $this->_db->fetchAll($select);
	    	foreach ($result as $data):
	    	$useid[] = $data['UserID'];
	    	endforeach;    	
    	return $useid;
    }
    
    public function getuserbycompany($company){
    	$select = $this->_db->select()
	    	->from('tblUsers','UserID')
		    	->where('company = ?',$company)
		    		->where('clientID = ?', clientID);
    	
    	$result = $this->_db->fetchAll($select);
    	foreach ($result as $data):
    	$useid[] = $data['UserID'];
    	endforeach;
    	
    	return $useid;
    }

    public function getuserbytaglist($tag){
       /* $select = $this->_db->select()
            ->from(array('c'=>'tblDbees',array('c.User')))
                ->joinInner(array('u' => 'tblUsers'), 'u.UserID = c.User',array('UserID','Name','lname','Username','ProfilePic','lname'))
                    ->where('c.DbTag = ?',$tag)
	                    ->where('c.clientID = ?', clientID)
	        				->group('c.User');    */
       // echo $select->__toString();   
         $sql = "SELECT DISTINCT(UserID), DbTag, User,UserID,Name,Username,ProfilePic FROM tblDbees AS c INNER JOIN tblUsers u ON u.UserID = c.User WHERE c.DbTag LIKE '".$tag."' AND c.clientID = ".clientID."";
        $select = $this->_db->query($sql);      
        
        return $select->fetchAll();        
          
    }
    
    public function getuserbytitlelist($title){
    	$select = $this->_db->select()
	    	->from( 'tblUsers',array('UserID','Name','lname','Username','ProfilePic','lname'))
		    	->where('title LIKE ?',$title)
		    		->where('clientID = ?', clientID); 
    	
    	return $result = $this->_db->fetchAll($select);
    	 
    }
    
    public function getuserbycompanylist($company){
    	$select = $this->_db->select()
	    	->from('tblUsers')
            ->where('clientID = ?', clientID)
		    	->where('company LIKE ?',$company);
			    
                    //echo $select->__toString();    	
    	return $result = $this->_db->fetchAll($select);
    
    }
    
    
    
}