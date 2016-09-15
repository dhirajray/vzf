<?php

class Application_Model_Groups extends Application_Model_DbTable_Master
{
    
    protected $_name = null;
    
    
    
    protected function _setupTableName()
    {
        
        parent::_setupTableName();
        $storage    = new Zend_Auth_Storage_Session(); 
        $data      = $storage->read(); 
        $this->session_data = $data;
        
        $this->_name = $this->getTableName(GROUP);
        
    }
    
    
    
    public function getgrouptypes()
    {
        
        $select = $this->_db->select()        
       	 ->from($this->getTableName(GroupTypes),array('TypeID','TypeName'))    
       	 	->where("clientID = ?", clientID)
        		->order('Priority Desc');        
        return $this->_db->fetchAll($select); 
    }
        
    public function getfollowersgrouptypes($cookieuser,$limit='',$Offset='',$isvip=0)
    {        
        $select = $this->_db->select();      
	    $select->from(array('f' => $this->getTableName(FOLLOWS)))       
		        ->join(array('u' => $this->getTableName(USERS)), 'f.FollowedBy = u.UserID') 
			        ->where("u.clientID = ?", clientID)
			        	->where("f.User =?", $cookieuser)->where("u.role != ?", '1');
                        
                        if(($this->session_data['usertype']==0 || $this->session_data['usertype']==6) && isADMIN!=1){
                            $notvip = array('0','6');
                            $select->where("usertype IN(?)", $notvip);
                        }
                        if($this->session_data['usertype']==100 && isADMIN!=1){
                            $select->where("usertype = ?", 100);
                        }  
                        if(isADMIN!=1) 
                        {
                          $select->where("hideuser != ?", 1);
                        }
        if($limit!='') $select->limit($limit,$Offset);       
       return $this->_db->fetchAll($select);
    }
	
	 public function getfollowersgrouptypes2($cookieuser,$limit='',$Offset='',$isvip=0)
    {        
        $select = $this->_db->select();      
        $select->from(array('f' => $this->getTableName(FOLLOWS)))       
                ->join(array('u' => $this->getTableName(USERS)), 'f.FollowedBy = u.UserID') 
                    ->where("u.clientID = ?", clientID)
                        ->where("f.User =?", $cookieuser)->where("u.role != ?", '10');  
                        if(($this->session_data['usertype']==0 || $this->session_data['usertype']==6) && isADMIN!=1){
                            $notvip = array('0','6');
                            $select->where("usertype IN(?)", $notvip);
                        }
                        if($this->session_data['usertype']==100 && isADMIN!=1){
                            $select->where("usertype = ?", 100);
                        }  
                        if(isADMIN!=1) 
                        {
                          $select->where("hideuser != ?", 1);
                        }

        if($limit!='') $select->limit(30,$Offset);       
       return $this->_db->fetchAll($select);
    }
    
    public function getfollowinggrouptypes($cookieuser,$limit='',$Offset='',$isvip=0)
    {        
        $select = $this->_db->select();  
            
	    $select->from(array('f' => $this->getTableName(FOLLOWS)))       
		        ->join(array('u' => $this->getTableName(USERS)), 'f.User = u.UserID')
			    	->where("u.clientID = ?", clientID)
        				->where("f.FollowedBy =?", $cookieuser)->where("u.usertype != ?", '10'); 
                        if(($this->session_data['usertype']==0 || $this->session_data['usertype']==6) && isADMIN!=1){
                            $notvip = array('0','6');
                            $select->where("usertype IN(?)", $notvip);
                        }
                        if($this->session_data['usertype']==100 && isADMIN!=1){
                            $select->where("usertype = ?", 100);
                        }  
                        if(isADMIN!=1) 
                        {
                          $select->where("hideuser != ?", 1);
                        }
        if($limit!='') 
            $select->limit($limit,$Offset);                      
        return $this->_db->fetchAll($select);
    }
	
	 public function getfollowinggrouptypes2($cookieuser,$limit='',$Offset='',$isvip=0)
    {        
        $select = $this->_db->select();        
        $select->from(array('f' => $this->getTableName(FOLLOWS)))       
                ->join(array('u' => $this->getTableName(USERS)), 'f.User = u.UserID')
                    ->where("u.clientID = ?", clientID)
                        ->where("f.FollowedBy =?", $cookieuser)->where("u.usertype != ?", '10');  
                        if(($this->session_data['usertype']==0 || $this->session_data['usertype']==6) && isADMIN!=1){
                            $notvip = array('0','6');
                            $select->where("usertype IN(?)", $notvip);
                        }
                        if($this->session_data['usertype']==100 && isADMIN!=1){
                            $select->where("usertype = ?", 100);
                        }  
                        if(isADMIN!=1) 
                        {
                          $select->where("hideuser != ?", 1);
                        }
        if($limit!='') $select->limit(30,$Offset);   
                     
        return $this->_db->fetchAll($select);
    }
    public function searchgrouptypes($keyword, $type,$usertype="")
    {
        $groupPrivacy = '';
        if(!isADMIN)
        {
            if($usertype==0 || $usertype==6) 
            {
                $groupPrivacy = 'AND tblGroups.GroupPrivacy!=4'; // only vip can see vip group
            }
        }       
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();       
        $SQL = "select * from tblGroups,tblUsers where GroupName LIKE '%" . trim($keyword) . "%'";      
        if ($type != '0')
            $SQL .= "AND GroupType=" . $type;       
        $SQL .= " AND tblGroups.GroupPrivacy!=2 ".$groupPrivacy."  AND tblGroups.User=tblUsers.UserID AND tblGroups.clientID=".clientID." order by ID";     

        $records = $db->fetchAll($SQL);       
        return $records;       
    }
    
    public function ChkFollowerNum($cookieuser, $UserID)
    {       
        $select = $this->_db->select()        
	        ->from('tblFollows')       
		        ->where("User =?", $cookieuser)
			        ->where("clientID = ?", clientID)
			        	->where("FollowedBy =" . $UserID . "");        
        return $this->_db->fetchAll($select);
    }
    
    public function Notification($UserID)
    {        
        $select = $this->_db->select()        
        	->from('tblNotificationSettings') 
	        	->where("clientID = ?", clientID)
	        		->where("User =?", $UserID);       
        return $this->_db->fetchAll($select);
    }
    
	public function usergroup()
    {        
        $select = $this->_db->select()        
            ->from('usersgroup',array('ugid','ugname')) 
                ->where("clientID = ?", clientID)    
					->order('ugid Desc');
        return $this->_db->fetchAll($select);
    }
	
	public function allsetuser($id)
    {        
        $select = $this->_db->select()        
            ->from('usersgroupid',array('id','ugid','userid')) 
                ->where("clientID = ?", clientID)->where("ugid = ?", $id);                          
        return $this->_db->fetchAll($select);
    }
	
    public function Notificationdont($UserID)
    {        
        $select = $this->_db->select()        
	        ->from('tblNotificationSettings') 
	        	->where("clientID = ?", clientID)
	        		->where("User = ?", $UserID);        
        return $this->_db->fetchAll($select);  
    }
    
    public function getusers($keyword,$userid='',$isadmin='',$isvip=0)
    {
        
        $myclientdetails = new Application_Model_Clientdetails();   
        $keyword  = $myclientdetails->customEncoding($keyword,'allusersAlphabat');
        $keyword    = str_replace('$', '\$',addslashes($keyword));
        $selecttype = $this->_db->select()        
            ->from('tblUserType') 
                ->where("clientID = ?", clientID);
        $viparray =  $this->_db->fetchAll($selecttype);        
        $vip="";
        $i=0;
        $count=count($viparray);
        foreach ($viparray as $key => $value) {
            if($count-1)
            {
             $vip.=$value['TypeID'].', ';
            }
            else
            {
             $vip.=$value['TypeID'];
            }

            $i++;
        }       
        $notvip      = array('0','6'); 
        $select = $this->_db->select()        
	        ->from('tblUsers',array('UserID','Name','lname','ProfilePic','Status','clientID')) 		     
		        ->where("Name LIKE '%$keyword%' OR Username LIKE '%$keyword%' OR full_name LIKE '%$keyword%'")->where('clientID = ?', clientID);
			        if(($this->session_data['usertype']==0 || $this->session_data['usertype']==6) && isADMIN!=1){
			        	$select->where("usertype IN(?)", $notvip);
			        }
                    if($this->session_data['usertype']==100 && isADMIN!=1){
                        $select->where("usertype = ?", 100);
                    }
				    if($userid!=''){ $select ->where("UserID != ?", $userid);}
				        	$select->where("Status = ?", '1');
                    if(isADMIN!=1)
                     {
                        $select->where('role != ?', 1)->where('usertype != ?', '10');                
                     } 
                     if(isADMIN!=1) 
                     {
                       $select->where("hideuser != ?", 1);
                     }      
				    //echo $select->__toString();                   
        return $this->_db->fetchAll($select);  
    }
   
    public function getvipuser()
    {        
        $select = $this->_db->select()
         ->from('tblUserType')         
                ->where("Typeref = ?", 'vip')
                    ->where("clientID = ?", clientID);                            
        $result = $this->_db->fetchAll($select);
       foreach ($result as $data):
            $usertype[] = $data['TypeID'];
        endforeach;
        
        
        return $usertype;
    }

    public function getallusers($users)
    {        
        $select = $this->_db->select()
       	 ->from('tblUsers')        	
        		->where("UserID IN(?)", $users)
        			->where("clientID = ?", clientID)
        				->where("Status = ?", '1');       
        return $this->_db->fetchAll($select);
    }
    
    public function insertgroupdetail($data)
    {
        
        if ($this->_db->insert($this->_name, $data)) {
            
            return $this->_db->lastInsertId();
            
        }
        
        else {
            
            return 0;
            
        }
        
    }
    
    public function updategroupdetail($data)
    {
        $dataall = $data;
        $groupid = $data['ID'];
        if ($this->_db->update($this->getTableName(GROUP), $dataall, array(
            "ID='" . $groupid . "'"
        ))) {
            echo '1~#~0';
        } else {
            echo '0~#~1';
        }
    }
    
    public function groupuserdetail($group)
    {
        
        $select = $this->_db->select()        
	        ->from(array('g' => $this->getTableName(GROUP)))        
		        ->join(array('u' => $this->getTableName(USERS)), 'g.User = u.UserID')       
			        ->where("g.ID =?", $group)       
			        	->where("g.clientID =?", clientID);        
        return $this->_db->fetchAll($select);              
    }    
    
    public function userdetail($users)
    {      
        $select = $this->_db->select()
	        ->from('tblUsers')        
		        ->where("UserID IN(?)", $users)
		        	->where("clientID = ?", clientID);
     
        return $this->_db->fetchAll($select);
    }
        
    public function insertingroupmem($data)
    {
        if ($this->_db->insert('tblGroupMembers', $data)) {            
            return 1;            
        }else {            
            return 0;            
        }        
    }
    
    public function caltotalgroupnatification($cookieuser)
    {
        
        $select = $this->_db->select()
       			 ->from('tblGroupMembers')
        			->where("User =" . $cookieuser . " OR Owner =" . $cookieuser)
       				 	->where("clientID = ?", clientID)
        					->where("Status =?", '0');    
        return $this->_db->fetchAll($select);
    }
    
    public function calalltotalgroupnatification($cookieuser)
    {
        
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        
        $select = "select * from tblGroupMembers WHERE ((User=" . $cookieuser . " AND SentBy='Owner') OR (Owner=" . $cookieuser . " AND SentBy='Self')) AND Status=0 AND clientID=".clientID." order by JoinDate Desc";
        
        $result = $db->fetchAll($select);
        
        return $result;
        
    }
    
    public function caltotaluser($UserID)
    {        
        $select = $this->_db->select()
	        ->from('tblUsers')
		        ->where("UserID =?", $UserID)
		        	->where("clientID = ?", clientID);        
        return $this->_db->fetchAll($select);        
    }
    
    public function calgroup($GroupID)
    {        
        $select = $this->_db->select()      
	        ->from('tblGroups')        
		        ->where("ID =?", $GroupID)
		      		->where("clientID = ?", clientID);
        return $this->_db->fetchAll($select);  
    }
    
    public function upadategroupval($id)
    {        
        $data = array(
            'Status' => 1
        );
        if ($this->_db->update($this->getTableName(GROUP_MEMBER), $data, array(
            "ID='" . $id . "'"
        ))) {
            return 1;
        }
        else {
            return 0;
        }
    }
    public function upadategroupvalreq($gid,$gownerid,$userid)
    {
    	$data = array(
    			'Status' => 1
    	);
    	$updatearray = array(
    			'GroupID = ?' => $gid,
    			'Owner = ?' => $gownerid,
    			'User = ?' => $userid,
    			'SentBy = ?' => 'Self'
    	);
    	
    	if ($this->_db->update($this->getTableName(GROUP_MEMBER), $data,$updatearray)) {
    		return 1;
    	}
    	else {
    		return 0;
    	}
    }
    
    public function deletgroupval($id)
    {
        
        if ($this->_db->delete($this->getTableName(GROUP_MEMBER), array(            
        	'ID = ?' => $id,
        	'clientID' => clientID
        ))) {
            return 1;
        }
        
        else {
            return 0;
        }
        
    }
    
    public function collectallgroups($user, $profileholder = 0)
    {
        $select = $this->_db->select()
	        ->from(array('gm' => $this->getTableName(GROUP_MEMBER)), array('GroupID'))
		        ->joinleft(array('g' => $this->getTableName(GROUP)), 'g.ID = gm.GroupID')
			        ->joinleft(array('gt' => $this->getTableName(GROUP_TYPE)), 'g.GroupType = gt.TypeID')
				        ->where("gm.User = ?", $user)
					        ->where("gm.Status= 1")
					        	->where("g.clientID = ".clientID);
        
        if ($profileholder != 1){
            $select->where("g.GroupPrivacy!= 2");
        	$select->where("g.GroupPrivacy!= 4");        	
        }
     
        return $this->_db->fetchAll($select);
    }
    
    public function collectallgroupbygroup($Groups)
    {        
        $select = $this->_db->select()       
        	->from('tblGroups')       
        		->where("ID IN(?)", $Groups)
        			->where("clientID = ?", clientID);
        return $this->_db->fetchAll($select);
    }
    
    public function collectallgroupbygroupdes($Groups)
    {        
        $select = $this->_db->select();    
		    $select->from('tblGroups')        
		       	 ->where("ID IN(?)", $Groups) 			       	 
			       		 ->where("GroupPrivacy != ?", '4')
			       			 ->order('GroupDate DESC');		  
        return $this->_db->fetchAll($select);
    }
    
    
    
    public function collectallgroupbyuser($user)
    {
        $select = $this->_db->select()       
	        ->from('tblGroups')        
		        ->where("User =?", $user) 
			        ->where("clientID = ?", clientID)
			        	->order('GroupDate DESC');      
        return $this->_db->fetchAll($select);
    }
    
    public function collectallgroupbyuserdes($user)
    {
        $select = $this->_db->select()      
        	->from(array('g' => $this->getTableName(GROUP)))        
       			 ->where("g.User =?", $user)
	       			 ->joinLeft(array('gt' => $this->getTableName(GROUP_TYPE)), 'g.GroupType = gt.TypeID',array('TypeName','TypeID'))
		       			 ->where("g.GroupPrivacy != ?", 4)->where("g.clientID = ?", clientID)
		        			->order('g.GroupDate DESC');        
        return $this->_db->fetchAll($select); 
    }
    public function allgroupscount($lastID="")
    {
        $select = $this->_db->select()      
            ->from(array('g' => $this->getTableName(GROUP)), array('ID'))              
                     ->joinLeft(array('gt' => $this->getTableName(GROUP_TYPE)), 'g.GroupType = gt.TypeID',array('TypeName','TypeID'))
                         ->where("g.GroupPrivacy != ?", 2)->where("g.clientID = ?", clientID);
                            if($lastID!="")
                            {
                            $select->where('g.ID < ?',$lastID);
                            }
                            $select->order('g.ID DESC');                                
        return count($this->_db->fetchAll($select)); 
    }
    public function allgroups($lastID="")
    {
        $select = $this->_db->select()      
            ->from(array('g' => $this->getTableName(GROUP)))              
                     ->joinLeft(array('gt' => $this->getTableName(GROUP_TYPE)), 'g.GroupType = gt.TypeID',array('TypeName','TypeID'))
                         ->where("g.GroupPrivacy != ?", 2)->where("g.clientID = ?", clientID);
                         if($lastID!="")
                            {
                            $select->where('g.ID < ?',$lastID);
                            } 
                            $select->order('g.ID DESC'); 
                            $select->limit(20); 
             // echo $lastID.' :sql:'.$select->__toString();
       // die;             
        return $this->_db->fetchAll($select); 
    }
    public function collectallgroupbygrouptype($Groups)
    {
    	$select = $this->_db->select();    
		    $select->from('tblGroups')        
		       	 ->where("ID IN(?)", $Groups) 
			       	 ->where("GroupPrivacy =?", '4')
			       		 ->where("clientID = ?", clientID)
			       			 ->order('GroupDate DESC');		
		 
        return $this->_db->fetchAll($select);
    	
    }
    
    public function calgroupmembers($ID)
    {
        $select = $this->_db->select()       
	        ->from(array('g' => $this->getTableName(GROUP_MEMBER)))  
		        ->join(array('gr' => $this->getTableName(GROUP)), 'g.GroupID =gr.ID')
			        ->join(array('u' => $this->getTableName(USERS)), 'g.User =u.UserID')       
				        ->where("g.GroupID =$ID")
				        	->where("g.clientID = ?", clientID)
				        		->where("g.Status ='1'");        
	        return $this->_db->fetchAll($select); 
    }
    
    public function checkuser($ID, $cookieuser)
    {        
        $select = $this->_db->select()        
	        ->from('tblGroupMembers')       
		        ->where("GroupID =?", $ID)        
			        ->where("User =?", $cookieuser) 
				        ->where("clientID = ?", clientID)
				        	->where("Status =?", '1');        
        return $this->_db->fetchAll($select); 
    }
    
    
    
    public function checkuserno($ID, $cookieuser)
    {        
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        
        $SQL = mysql_num_rows(mysql_query("select * from tblGroupMembers where GroupID=" . $ID . " AND (Owner=" . $cookieuser . " OR User=" . $cookieuser . ") AND Status=1", $conn));
        
        $result = $db->fetchAll($SQL);
        
        return $result;
        
    }
    
    
    public function allgroupdetailsuser($group)
    {
    
    	$select = $this->_db->select()
	    	->from('tblGroups',array('GroupName','GroupPrivacy','User'))
		    	->where("ID =?", $group)
		    		->where("clientID = ?", clientID);    	
    	$result = $this->_db->fetchAll($select);
    
    	return $result;
    
    }
    
    public function allgroupdetailsuserre($group, $cookieuser)
    {
        
        $select = $this->_db->select()        
	        ->from('tblGroupMembers')        
		        ->where("GroupID =?", $group)     
			        ->where("clientID = ?", clientID)
				       // ->where("Status = ?", 1)
				        	->where("User =?", $cookieuser); 
           
        $result = $this->_db->fetchAll($select);
        
        return $result;
        
    }
    
    
    
    public function selectgroupdetails($group)
    {   
        $select = $this->_db->select()        
	        ->from(array('g' => $this->getTableName(GROUP)))    
	        ->join(array('u' => $this->getTableName(USERS)), 'g.User = u.UserID',array('UserID','Name','title','company','Contacts','Username','usertype','ProfilePic','usertype','Fbname','Socialid','Socialtype','Signuptoken'))
	        ->joinLeft(array('gt' => $this->getTableName(GROUP_TYPE)), 'g.GroupType = gt.TypeID',array('TypeName','TypeID'))		      
			        ->where("g.clientID = ?", clientID)
			        	->where("g.ID = ?", $group);        
        return $this->_db->fetchAll($select);
    }
    
    
    
    public function selectgroupprivacy($group)
    {        
        $select = $this->_db->select()        
	        ->from($this->getTableName(GROUP))
		        ->where("clientID = ?", clientID)
		        	->where("ID = ?", $group);        
        return $this->_db->fetchAll($select);
    }
    
    public function selectgroupmem($group, $cookieuser)
    {
    	 $select = $this->_db->select()        
	        ->from($this->getTableName(GROUP_MEMBER))        
		        ->where("GroupID =?", $group) 
			        ->where("clientID = ?", clientID)
			        	->where("User =?", $cookieuser);
        return $this->_db->fetchAll($select);
    }
    public function selectallgroupmem($group,$loginid)
    {
    	 $select = $this->_db->select()
    		->from($this->getTableName(GROUP_MEMBER))
    			->where("GroupID =?", $group)
					->where("User !=?",$loginid)
						->where("Status =?",'1')
							->where("clientID = ?", clientID);
    	
    	return $this->_db->fetchAll($select);
    }

    public function selectallgroupmememail($group,$loginid)
    {
         $select = $this->_db->select()
            ->from(array('g' => $this->getTableName(GROUP_MEMBER)))
                ->join(array('n' => 'tblNotificationSettings'), 'g.User = n.User',array('User','Groups'))
                    ->where("n.Groups =?", '1')
                        ->where("g.GroupID =?", $group)
                            ->where("g.User !=?",$loginid)
                                ->where("g.Status =?",'1')
                                    ->where("g.clientID = ?", clientID);
        
        return $this->_db->fetchAll($select);
    }
    
    public function selectpvtgroupmem($group, $cookieuser)
    {
        $select = $this->_db->select()        
	        ->from($this->getTableName(GROUP_MEMBER))        
		        ->where("GroupID =?", $group) 
			        ->where("clientID = ?", clientID)
				       ->where("User =?", $cookieuser)        
				       		->where("Status =?",'1');        
        return $this->_db->fetchAll($select);
    }
    
    
    public function selectgroupmembers($group)
    {        
        $select = $this->_db->select()        
	        ->from(array('g' => $this->getTableName(GROUP_MEMBER)))        
		        ->join(array('u' => $this->getTableName(USERS)), 'g.User = u.UserID')        
			        ->where("g.GroupID =?", $group)  
				        ->where("g.clientID = ?", clientID)
				       		->where("g.Status =?", '1');        
        return $this->_db->fetchAll($select);            
    }
    
    public function checkGroupMemberJoinStatus($group_id, $userid)
    {        
        $select = $this->_db->select()        
	        ->from($this->getTableName(GROUP_MEMBER))        
		        ->where("GroupID =?", $group_id)   
			        ->where("clientID = ?", clientID)
				        ->where("Status =?", '1')        
				        	->where("User =?", $userid);        
        return count($this->_db->fetchAll($select));
        
    }
    
    public function caltotaldbee($group)
    {        
        $select = $this->_db->select()
	        ->from($this->getTableName(DBEE))       
		        ->where("GroupID =?", $group)
		        	->where("clientID = ?", clientID);                
        return $this->_db->fetchAll($select);              
    }
    
    public function selectgroup($GroupID)
    {        
        $select = $this->_db->select()
	        ->from($this->getTableName(GROUP))
		        ->where("clientID = ?", clientID)
		        	->where("ID =?", $GroupID);        
        return $this->_db->fetchAll($select);                
    }
    
    public function selectdbeecat()
    {        
        $select = $this->_db->select()  
	        ->from($this->getTableName(CAT))  
		        ->where("clientID = ?", clientID)
		        	->order('Priority DESC');        
        return $this->_db->fetchAll($select);        
              
    }
    
    public function noofcommentdbeegroup($DbeeID)
    {        
        $select = $this->_db->select()     
        	->from($this->getTableName(DBEE))  
	        	->where("clientID = ?", clientID)
	        		->where("DbeeID =?", $DbeeID);      
        return $this->_db->fetchAll($select);        
    }
    
    public function selecttblPoll($DbeeID)
    {        
        $select = $this->_db->select()        
        ->from($this->getTableName(POLL_VOTES))
        	->where("clientID = ?", clientID)
        		->where("PollID = ?", $DbeeID);        
        return $this->_db->fetchAll($select);       
    }
    
    public function selectoptPoll($DbeeID)
    {        
        $select = $this->_db->select()        
	        ->from($this->getTableName(POLL_OPTION))  
		        ->where("clientID = ?", clientID)
		        	->where("PollID = ?", $DbeeID);                
        return $this->_db->fetchAll($select);      
    }
    
    public function selectuser($userid)
    {        
        $select = $this->_db->select()       
	        ->from($this->getTableName(USERS))        
		        ->where("UserID = ?", $userid)  
		       		->where("clientID = ?", clientID);
        return $this->_db->fetchAll($select);
	}
	    
    public function selectPollvotes($ID, $PollID)
    {        
        $select = $this->_db->select()       
	        ->from($this->getTableName(POLL_VOTES))        
		        ->where("Vote =?", $ID)
			        ->where("clientID = ?", clientID)
			        	->where("PollID =?", $PollID);        
        return $this->_db->fetchAll($select);       
    }
    
    public function caltotaldbresult($group,$start)
    {
        $select           = $this->_db->select()->from(array('g' => 'viewposts' ));
        $select->where("g.clientID = ?", clientID)->where("g.GroupID =?", $group)
                             ->where("g.Active =?", '1')        
                                ->order('PostDate DESC')->limit(PAGE_NUM,$start);

        /*$select = $this->_db->select()        
	        ->from(array('g' => $this->getTableName(DBEE)))      
		        ->join(array('u' => $this->getTableName(USERS)), 'g.User =u.UserID')
			        ->where("g.GroupID =?", $group)
						 ->where("g.clientID = ?", clientID)        
							 ->where("g.Active =?", '1')        
							 	->order('PostDate DESC')->limit(PAGE_NUM,$start);*/
        
       // echo $select->__toString();
       return $this->_db->fetchAll($select);
    }
    public function caltotaldbresultcnt($group,$start)
    {
    	$select = $this->_db->select()
    	->from(array('g' => $this->getTableName(DBEE)))
    	->join(array('u' => $this->getTableName(USERS)), 'g.User =u.UserID')
    	->where("g.GroupID =?", $group)
    	->where("g.clientID = ?", clientID)
    	->where("g.Active =?", '1')
    	->order('PostDate DESC')->limit(PAGE_NUM+1,$start);
    
    	// echo $select->__toString();
    	return $this->_db->fetchAll($select);
    }
    
    
    
    public function selectgrouptotaluser($group)
    {
        
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        
       $select = "select *,DATE_FORMAT(JoinDate, '%d-%m-%Y') AS GroupJoinDate from tblGroupMembers,tblUsers where tblGroupMembers.GroupID=" . $group . " AND tblGroupMembers.Status=1

	         AND tblGroupMembers.User=tblUsers.UserID AND tblGroupMembers.clientID=".clientID;
        
        $result = $db->fetchAll($select);
        
        return $result;
        
    }
    
    public function checkgroupmem($group, $UserID)
    {  
        $select = $this->_db->select()      
	        ->from($this->getTableName(GROUP_MEMBER))        
		        ->where("GroupID =?", $group)        
		        	->where("User =?", $UserID)
		       			 ->where("clientID = ?", clientID);        
	    return $this->_db->fetchAll($select);
    }
    
    
    public function deletememgroup($group, $user)
    {  
        if ($this->_db->delete($this->getTableName(GROUP_MEMBER), array(
            'GroupID= ?' => $group,
            'User= ?' => $user,
        	'clientID' => clientID
        ))) {
            return 1;
        }
    }
        
    public function grouprow($group)
    {        
        $select = $this->_db->select()        
	        ->from($this->getTableName(GROUP))  
		        ->where("clientID = ?", clientID)
		       		->where("ID = ?", $group);        
        return $this->_db->fetchRow($select);    
    }
    
    public function getgrouptype($groupid)
    {
        $select = $this->_db->select();        
        $select->from($this->getTableName(GROUP_TYPE));        
        $select->where("TypeID = ?", $groupid);        
        return $this->_db->fetchRow($select);  
    }
    
    public function selectsearchgroup($group, $owner, $user)
    {
        $select = $this->_db->select()
	        ->from($this->getTableName(GROUP_MEMBER))
		        ->where("GroupID =?", $group)
			        ->where("clientID = ?", clientID)
				        ->where("Owner =?", $owner)
				        	->where("User =?", $user);
        return $this->_db->fetchAll($select);
    }
    
    public function upadategroupallval($JoinDate, $group, $owner, $user)
    {        
        $data = array('JoinDate' => $JoinDate);        
        if ($this->_db->update($this->getTableName(GROUP_MEMBER), $data, array(
            "GroupID='" . $group . "'",
            "Owner='" . $owner . "'",
            "User='" . $user . "'",
            "SentBy='Self'"
        ))) {
            return 1;
        }
        
        else {
            return 0;
        }
        
    }
    
    public function groupstatus($groupid, $userid, $SentBy)
    {
        $select = $this->_db->select()
	        ->from('tblGroupMembers')
		        ->where("GroupID =?", $groupid)
			        ->where("clientID = ?", clientID)
			       		->where("User =?", $userid);
        return $this->_db->fetchAll($select);       
    }
	
	public function checkgroupsocialtoken($token)
    {
        $select = $this->_db->select()
	        ->from($this->getTableName(GROUPSOCIAL))
		        ->where("token = ?", $token)
		        	->where("clientID = ?", clientID);
        $result = $this->_db->fetchRow($select);
        if ($result)
            return '1';
        else
            return '0';
    }
	
	public function checkgroupsocial($groupid,$type,$token,$socialid)
    {
        $select = $this->_db->select()
	        ->from($this->getTableName(GROUPSOCIAL))
		        ->where("groupid = ?", $groupid)
			        ->where("type = ?", $type)
				        ->where("token = ?", $token)
				        	->where("clientID = ?", clientID);
        $result = $this->_db->fetchRow($select);
        if ($result)
            return '1';
        else
            return '0';
    }

    public function checkgroupLocksocial($groupid,$type,$token,$socialid)
    {
        $select = $this->_db->select()
            ->from($this->getTableName(GROUPLOCK))
                ->where("groupid = ?", $groupid)
                    ->where("type = ?", $type)
                        ->where("token = ?", $token)
                            ->where("clientID = ?", clientID);
        $result = $this->_db->fetchRow($select);
        //echo $select->__toString();
        if ($result)
            return '1';
        else
            return '0';
    }
	
	public function insertgroupsocial($data)
    {
        if ($this->_db->insert($this->getTableName(GROUPSOCIAL), $data))
            return true;
        else
            return false;
    }

    public function insertgrouplock($data)
    {
        if ($this->_db->insert($this->getTableName(GROUPLOCK), $data))
            return true;
        else
            return false;
    }

    public function dellockgroupsocial($socialid,$groupid)
    {
        /*******Delete invitegroup*********/
        if($this->_db->delete($this->getTableName(GROUPLOCK), array(
             'socialid= ?' => $socialid,'groupid= ?' => $groupid, 'clientID= ?' => clientID                
        )))
         return true;
        else
         return false;  
    }
	
	public function insertgroup($data)
    {
        /*******Insert group *********/
        if ($this->_db->insert($this->getTableName(GROUP_MEMBER), $data))
            return $this->_db->lastInsertId();
        else
            return false;
    }
	public function delinvitegroupsocial($token)
    {
        /*******Delete invitegroup*********/
		if($this->_db->delete($this->getTableName(GROUPSOCIAL), array(
             'token= ?' => $token, 'clientID= ?' => clientID				
        )))
		 return true;
        else
         return false;	
    }
	
	 public function deletegroup($group,$user)
    {
        if ($this->_db->delete($this->getTableName(GROUP), array(
            'ID= ?' =>  $group, 'User= ?' => $user, 'clientID= ?' => clientID
        ))) {
            return 1;
        }
        
        else {
            return 0;
        }
    }
    
    public function deletedbeesgroup($group,$user)
    {
        if ($this->_db->delete($this->getTableName(DBEE), array(
            'GroupID= ?' =>  $group, 'User= ?' => $user, 'clientID= ?' => clientID
        ))) {
            return 1;
        }
        
        else {
            return 0;
        }
    }
    
    public function deleteallmemgroup($group,$user)
    {
        if ($this->_db->delete($this->getTableName(GROUP_MEMBER), array(
            'GroupID= ?' =>  $group, 'User= ?' => $user, 'clientID= ?' => clientID
        ))) {
            return 1;
        }
        else {
            return 0;
        }
    }
    
    public function deletactivity($group, $user)
    {
        $this->_db->delete($this->getTableName(activity), array(
            'act_typeId= ?' => $group,
            'act_userId= ?' => $user
        	//'clientID= ?' => clientID
        ));       
        return true;
        
    }

     public function getAllGroupMember($id)
    {        
        $select = $this->_db->select()        
          ->from(array('g' => 'tblGroupMembers'))      
            ->join(array('u' => $this->getTableName(USERS)), 'g.User =u.UserID')
             ->where("g.clientID = ?", clientID)              
               ->where("g.GroupID =?", $id)        
                ->order('g.ID DESC');   
                //echo $select->__toString();  exit;      
       return $this->_db->fetchAll($select);
    }
     public function selectgroupname()
    {        
        $select = $this->_db->select()
            ->from($this->getTableName(GROUP),array('ID','GroupName'))
                ->where("clientID = ?", clientID)
                ->where("GroupPrivacy != ?", 2)
                ->limit(11); 
            //echo $select->__toString();  exit;       
        return $this->_db->fetchAll($select);                
    }
	
	/*public function deleteallgrouprecord($group,$user)
	{
    	$sql = new Sql($this->adapter);
    	$select = $sql->delete();
    	$select->from(array('g' => $this->getTableName(GROUP)));
    	$select->join(array('d' => $this->getTableName(DBEE)), 'g.ID = d.GroupID' , 'g.User = d.User');
    	$select->join(array('gm' => $this->getTableName(GROUP_MEMBER)),'g.ID = gm.GroupID' , 'g.User = gm.User');
    	$select->where('g.ID= ?',$group)->where('g.User= ?',$user);
    	$result = $this->_db->fetchAll($select);
    	return $result;	
	}*/
    
  
}