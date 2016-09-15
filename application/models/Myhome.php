<?php
class Application_Model_Myhome extends Application_Model_DbTable_Master
{
    protected $_name = null;
    protected function _setupTableName()
    {
        parent::_setupTableName();
        $storage    = new Zend_Auth_Storage_Session();          
        $auth        =  Zend_Auth::getInstance();      
        if($auth->hasIdentity()) 
        {               
             $data      = $storage->read(); 
             $this->_userid = $data['UserID'];
             $this->session_data = $data;   
        }
        $this->_name = $this->getTableName(DBEE);        
        
    }
    public function searchGroup($id,$userid) 
    {
        $select = $this->_db->select();
        $select->from( array('group' => 'tblGroupMembers'),array('ID'=>'group.ID'));
        $select->where("GroupID =?",$id);
        $select->where("User =?",$userid)->where('clientID = ?', clientID);
        return $this->_db->fetchRow($select);
    }
    public function searchUser_group($id,$userid){   
        $select = $this->_db->select()          
            ->from(array('g'=>'usersgroupid'))
                  ->where("userid =?",$userid)->where('clientID = ?', clientID)
                  ->where("ugid =?",$id);
        return $this->_db->fetchRow($select);         
    }
    public function usersdbeecount($id) // to fetch total no. of dbs of a user
	{		        
		$select = $this->_db->select();
    	$select->from(array('dbs'=>'tblDbees'), array('tot' =>  new Zend_Db_Expr('count(dbs.DbeeID)')));
        $select->where("User =?",$id)->where('clientID = ?', clientID);
    	$result = $this->_db->fetchAll($select);
    	return $result[0]['tot'];
	}
    public function getCountry()
    {    
        $sql = "SELECT name,countryId as id ,code as codes from country as c ";
        return $this->_db->query($sql)->fetchAll();
    }
    public function getNameCountry($name)
    {    
        $sql = "SELECT name as countryname,countryId,code as codes from country as c where name = '".$name."'";
        return $this->_db->query($sql)->fetchAll();
    }
    public function getNameCity($name)
    {    
        $sql = "SELECT name as cityname , cityId as cityId from cities as c  
        where  name = '".$name."'";
        return $this->_db->query($sql)->fetchAll();
    }
    public function getidCountry($id)
    {    
        $sql = "SELECT name,countryId as id,code from country as c where countryId = '".$id."'";
        return $this->_db->query($sql)->fetchAll();
    }
    public function getCity($id)
    {    
        $sql = "SELECT name , cityId as id from cities as c  
        where  cityId = '".$id."'";
        return $this->_db->query($sql)->fetchAll();
    }
    public function getCountryAddress($code,$q)
    {    
        $sql = "SELECT name , cityId as id from cities as c  
        where c.countryId ='".strtolower($code)."' AND name REGEXP '^".$q."'";
        return $this->_db->query($sql)->fetchAll();
    }
    public function getGroupdbee($userid,$groupid)
    {
        $status           = 1;
        $blocluser_obj    = new Application_Model_Blockuser();
        $blockuser        = $blocluser_obj->getblockuser($userid);
        $gethiddenDbs  =    $this->gethiddendbee($userid);   
        $select           = $this->_db->select()->distinct('c.DbeeID')->from(array(
            'c' => $this->_name
        ))->joinInner(array(
            'u' => $this->getTableName(USERS)
        ), 'u.UserID = c.User AND u.clientID = c.clientID');
        $select->where("c.clientID = ?", clientID);
        if (count($blockuser) > 0)
            $select->where("c.User NOT IN(?)", $blockuser);
        if (count($gethiddenDbs) > 0) 
        	 $select->where("c.DbeeID NOT IN(?)", $gethiddenDbs);

        $select->where("c.GroupID = ?", $groupid);       
        $select->where("c.Active= ?", '1')->where("c.eventtype!= ?", '2')->where("c.Privategroup= ?", '0')->order(array(
            'c' => 'LastActivity DESC'
        ))->limit(PAGE_NUM, 0);
      
        return $this->_db->fetchAll($select);
    }

    public function getdbeealldbee($userid,$start='',$end=0,$videoevent='',$livevideoevent='',$UserType='')
    {
        //require_once 'includes/globalfile.php'; 
        $Offset = (int) $end;
        $status           = 1;
        $blocluser_obj    = new Application_Model_Blockuser();
        $blockuser        = $blocluser_obj->getblockuser($userid);
        $gethiddenDbs     =    $this->gethiddendbee($userid);  

        $select           = $this->_db->select()->from(array('c' => 'viewposts' ));
        $select->where("c.clientID = ?", clientID);
        if (count($blockuser) > 0)
            $select->where("c.User NOT IN(?)", $blockuser);
        if (count($gethiddenDbs) > 0) 
             $select->where("c.DbeeID NOT IN(?)", $gethiddenDbs);
     
        if($videoevent!="" && $videoevent==0)
        {
            $select->where("c.Type != ?", '6');
        }
        if($livevideoevent!="" && $livevideoevent==0)
        {
            $select->where("c.Type != ?", '15');
        }
        if(isADMIN!=1)
        {
            if($UserType!=100)
            {
              $select->where('c.usertype != ?', 100); 
            }
        }

        $select->where("c.Active= ?", '1')->where("c.eventtype!= ?", '2')->where("c.Privategroup= ?", '0')->order(array(
            'c' => 'LastActivity DESC'
        ))->limit(PAGE_NUM, $Offset);
        
        /*$select           = $this->_db->select()->distinct('c.DbeeID')->from(array(
            'c' => $this->_name
        ), $dbtablefields)->joinInner(array(
            'u' => $this->getTableName(USERS)
        ), 'u.UserID = c.User AND u.clientID = c.clientID');
        $select->where("c.clientID = ?", clientID);
        if (count($blockuser) > 0)
            $select->where("c.User NOT IN(?)", $blockuser);
        if (count($gethiddenDbs) > 0) 
             $select->where("c.DbeeID NOT IN(?)", $gethiddenDbs);
     
        $select->where("c.Active= ?", '1')->where("c.eventtype!= ?", '2')->where("c.Privategroup= ?", '0')->order(array(
            'c' => 'LastActivity DESC'
        ))->limit(PAGE_NUM, 0);*/
      
        return $this->_db->fetchAll($select);
    }
    public function getdbeereload($ds, $tr = null, $userid,$group='')
    {
        require_once 'includes/globalfile.php'; 
        $Offset = (int) $ds;
        
        $blocluser_obj = new Application_Model_Blockuser();        
        $blockuser = $blocluser_obj->getblockuser($userid);
        $gethiddenDbs  =    $this->gethiddendbee($userid);

        $select           = $this->_db->select()->from(array('c' => 'viewposts' ));
        $select->where("c.clientID = ?", clientID);
        if (count($blockuser) > 0)
            $select->where("c.User NOT IN(?)", $blockuser);
        if (count($gethiddenDbs) > 0) 
             $select->where("c.DbeeID NOT IN(?)", $gethiddenDbs);
         if (!empty($group)) {
            $select->where("c.GroupID= ?", $group);
        }
     
        $select->where("c.Active= ?", '1')->where("c.eventtype!= ?", '2')->where("c.Privategroup= ?", '0')->order(array(
            'c' => 'LastActivity DESC'
        ))->limit(PAGE_NUM, $Offset);

        /*$select = $this->_db->select()->distinct('c.DbeeID')->from(array(
            'c' => $this->_name
        ), $dbtablefields)->joinInner(array(
            'u' => $this->getTableName(USERS)
        ), 'u.UserID = c.User  AND u.clientID = c.clientID');
        $select->where("c.clientID = ?", clientID);
        if (count($blockuser) > 0) {            
            $select->where("c.User NOT IN(?)", $blockuser);            
        }
        if (count($gethiddenDbs) > 0) {
        	$select->where("c.DbeeID NOT IN(?)", $gethiddenDbs);
        }
        if (!empty($group)) {
        	$select->where("c.GroupID= ?", $group);
        }
        
        $select->where("c.Active= ?", '1')->where("c.eventtype!= ?", '2')->where("c.Privategroup= ?", '0');
        $select->order(array(
            'c' => 'LastActivity DESC'
        ))->limit(PAGE_NUM, $Offset);*/
      //  echo $select->__toString();
        return $this->_db->fetchAll($select);        
        
    }  

     public function geteventdbeereload($ds, $tr = null, $userid,$event='')
    {
        require_once 'includes/globalfile.php'; 
        $Offset = (int) $ds;
        
        $blocluser_obj = new Application_Model_Blockuser();        
        $blockuser = $blocluser_obj->getblockuser($userid);
        $gethiddenDbs  =    $this->gethiddendbee($userid);
        $select = $this->_db->select()->distinct('c.DbeeID')->from(array(
            'c' => $this->_name
        ), $dbtablefields)->joinInner(array(
            'u' => $this->getTableName(USERS)
        ), 'u.UserID = c.User  AND u.clientID = c.clientID');
        $select->where("c.clientID = ?", clientID);
        if (count($blockuser) > 0) {            
            $select->where("c.User NOT IN(?)", $blockuser);            
        }
        if (count($gethiddenDbs) > 0) {
            $select->where("c.DbeeID NOT IN(?)", $gethiddenDbs);
        }
        if (!$event) {
            $select->where("c.events= ?", $event);
        }
        
        $select->where("c.Active= ?", '1')->where("c.eventtype!= ?", '2')->where("c.Privategroup= ?", '0');
        $select->order(array(
            'c' => 'LastActivity DESC'
        ))->limit(PAGE_NUM, $Offset);
        return $this->_db->fetchAll($select);        
        
    }      
    
    public function getmydbee($ds, $tr = null, $userid,$calling='',$videoevent='',$livevideoevent='')
    {	
        require_once 'includes/globalfile.php'; 
    	$status           = 1;
        $blocluser_obj    = new Application_Model_Blockuser($userid);
        $blockuser        = $blocluser_obj->getblockuser($userid);
        
        $Offset           = (int) $ds;

        $select           = $this->_db->select()->from(array('c' => 'viewposts' ));
        if($calling=='expert')
        {
            $select->joinInner(array(
            'e' => 'tblexpert'
            ), 'c.DbeeID = e.dbid');
            $select->where("e.status = ?", '1');
        }
        $select->where("c.clientID = ?", clientID);

        if($videoevent!="" && $videoevent==0)
        {
            $select->where("c.Type != ?", '6');
        }
        if($livevideoevent!="" && $livevideoevent==0)
        {
            $select->where("c.Type != ?", '15');
        }
     
        $select->where("c.User = ?", $userid)->where("c.Active= ?", '1')->where("c.eventtype!= ?", '2')->where("c.Privategroup= ?", '0')->order(array(
            'c' => 'LastActivity DESC'
        ))->limit(PAGE_NUM, $Offset); 
      		
        return $this->_db->fetchAll($select);
    }

    public function getpromoteddbee()
    {   
     
        $select           = $this->_db->select()->from(array('c' => 'viewposts' ));
        $Offset=0;
        $select->where("c.clientID = ?", clientID);
        $select->where("c.QA = ?", 1)->order(array(
            'c' => 'LastActivity DESC'
        ))->limit(PAGE_NUM, $Offset); 
            
        return $this->_db->fetchAll($select);
    }

    public function gettaggedmydbee($ds, $tr = null, $userid,$calling='')
    {   
        require_once 'includes/globalfile.php'; 
        $status           = 1;
        $blocluser_obj    = new Application_Model_Blockuser($userid);
        $blockuser        = $blocluser_obj->getblockuser($userid);
        
        $Offset           = (int) $ds;
        $select           = $this->_db->select()->from(array('c' => 'viewposts' ));
        if($calling=='expert')
        {
            $select->joinInner(array(
            'e' => 'tblexpert'
            ), 'c.DbeeID = e.dbid');
            $select->where("e.status = ?", '1');
        }
       
        $select->where("c.clientID = ?", clientID);
        $select->where("FIND_IN_SET('".$userid."',c.TaggedUsers)")->where("c.Active= ?", '1')->order(array(
            'c' => 'LastActivity DESC'
        ))->limit(PAGE_NUM, $Offset);

        
            
        return $this->_db->fetchAll($select);
    }        
    
    public function groupdbee($ds, $tr = null, $userid,$groupid)
    {   
        require_once 'includes/globalfile.php'; 
        $Offset = (int) $ds;
        $select = $this->_db->select()->from(array(
            'c' => $this->_name
        ), $dbtablefields)->joinInner(array(
            'u' => $this->getTableName(USERS)
        ), 'u.UserID = c.User  AND u.clientID = c.clientID');
        $select->where("c.clientID = ?", clientID);
        $select->where("c.GroupID = ?", $groupid);
        $select->where("c.Active= ?", '1')->order(array(
            'c' => 'LastActivity DESC'
        ))->limit(PAGE_NUM, $Offset); 
        /*echo $select->__toString();
        exit; */  
        return $this->_db->fetchAll($select);
    } 

    public function getdbeesearchid($cat)
    {
        //echo $cat;echo'anil';
        $matchjobQry = $this->_db->select()->from($this->_name,array('DbeeID'))->where("clientID = ?", clientID)->where('Active=1')->where("FIND_IN_SET('$cat',Cats)");
        //echo $matchjobQry;exit;
        $rs = $this->_db->fetchAll($matchjobQry);
        
       /* foreach ($rs as $data):
            $mydata[] = $data['DbeeID'];
        endforeach;*/
        
        return $rs;
        
    }
    /*check special dbee invitation requestion request valid or not 
     @param token , authID , dbeeid 
     it will return row 
    */
    public function checkSpecialDBeeInvitationToken($token, $authID, $dbeeid)
    {
        $select = $this->_db->select()->from(array(
            'A' => $this->getTableName(DBEESPECIALINVITE)
        ), array(
            'A.*'
        ))->where("A.dbeeid = ?", $dbeeid)
        ->where("A.socialid = ?", $authID)
        ->where("A.token = ?", $token)->where('A.clientID = ?', clientID);
        
        return $this->_db->fetchRow($select);
    }
    public function fetchprofanityfilter()
    {    	
    	if($this->profanityfilterchk()==1){
        $select = $this->_db->select()->from(array(
            'A' => $this->getTableName(ProfanityFilter)
        ), array(
            'A.name'
        ))->where("A.status = ?", 1)->where("A.clientID = ?", clientID);
        return $this->_db->fetchAll($select);
    	 }else{
    		return;
    	} 
    }

    public function getfollowTagUser($text,$userid,$isvip=0)
    {
        $where='';
        $text=str_replace('mabirdnny#', '', $text); 
        if($isvip==1 || isADMIN==1){
        	$where = " AND (u.usertype = 0 OR u.usertype = 6) ";
        }

        if( isADMIN==1 ) 
        {
//            $sql = "select u.UserID,u.full_name,u.Name,u.ProfilePic,u.Username from tblUsers as u  where u.clientID='".clientID."'   AND (REPLACE(u.Name, 'mabirdnny#', '') LIKE '%".$text."%' OR REPLACE(u.full_name, 'mabirdnny#', '') LIKE '%".$text."%' OR REPLACE(u.Username, 'mabirdnny#', '') LIKE '%".$text."%' OR REPLACE(u.full_name, 'mabirdnny#', '') REGEXP '^".$text.".*$')";
             $sql = "select u.UserID,u.full_name,u.Name,u.ProfilePic,u.Username from tblUsers as u  where u.clientID='".clientID."'   AND (REPLACE(u.Name, 'mabirdnny#', '') LIKE '%".$text."%' OR REPLACE(u.full_name, 'mabirdnny#', '') LIKE '%".$text."%' OR REPLACE(u.Username, 'mabirdnny#', '') LIKE '%".$text."%') AND u.Status=1 LIMIT 10";
       }
        else
        {  
             $sql = "select u.UserID,u.full_name,u.ProfilePic,u.Username from tblUsers as u Left join tblFollows as f ON u.UserID = f.FollowedBy where f.clientID='".clientID."' AND f.User ='".$userid."' ".$where." AND (REPLACE(u.Name, 'mabirdnny#', '') LIKE '%".$text."%' OR REPLACE(u.full_name, 'mabirdnny#', '') LIKE '%".$text."%' OR REPLACE(u.Username, 'mabirdnny#', '') LIKE '%".$text."%' OR REPLACE(u.full_name, 'mabirdnny#', '') REGEXP '^".$text.".*$')
            UNION
        select u.UserID,u.full_name,u.ProfilePic,u.Username from tblUsers as u Left join tblFollows as f ON u.UserID = f.User where f.clientID='".clientID."' AND f.FollowedBy ='".$userid."' ".$where." AND (REPLACE(u.Name, 'mabirdnny#', '') LIKE '%".$text."%' OR REPLACE(u.full_name, 'mabirdnny#', '') LIKE '%".$text."%' OR REPLACE(u.Username, 'mabirdnny#', '') LIKE '%".$text."%' OR REPLACE(u.full_name, 'mabirdnny#', '') REGEXP '^".$text.".*$') AND u.Status=1 LIMIT 10";
        }
    
       return $this->_db->query($sql)->fetchAll();
    }
    
    public function profanityfilterchk()
    {
    	$select = $this->_db->select();
    	$select->from( array('tblAdminSettings'),'Profanityfilter')
    			->where('clientID = ?', clientID);    	
    	$rs = $this->_db->fetchRow($select);
        
    	if($rs['Profanityfilter'])
    	return true;
    	else 
    	return false;
    }
    
     public function fetchTwitterTaGUser($userid)
    {
        $select = $this->_db->select()->from(array(
            'A' => $this->getTableName(TwitterTagUser)
        ), array(
            'A.userID'
        ))->where("A.status = ?", 1)->where("A.clientID = ?", clientID)
        ->where("A.userID = ?", $userid);
        /*echo $select->__toString();
        exit;*/
        return count($this->_db->fetchAll($select));
    }
    /*check special dbee invitation requestion request valid or not 
     @param token , authID , dbeeid 
     it will return row 
    */
    public function checkUserOrNotJoined($userID, $dbeeID)
    {
        $select = $this->_db->select()->from(array(
            'A' => $this->getTableName(DbeeJoinedUser)
        ), array(
            'A.*'
        ))->where("A.userID = ?", $userID)
        ->where("A.dbeeID = ?", $dbeeID)->where('A.clientID = ?', clientID);
        
        return $this->_db->fetchRow($select);
    }
    // insert after joing for special dbee
    
    public function checkattendies($data)
    {    
    	$select = $this->_db->select()->from( $this->getTableName(DbeeJoinedUser))
	    	->where("userID = ?", $data['userID'])
		    	->where("dbeeID = ?", $data['dbeeID'])->where('clientID = ?', clientID);		    		  
    		$result = $this->_db->fetchRow($select);
	    	if(count($result)>0){
	    		return false;
	    	}else{
	    		return true;
	    	}
    }
    
    
    public function isexpert($token, $socalid)
    {
    	$select = $this->_db->select()->from('tblspecialdbinvite')
    	->where("token = ?", $token)
    	->where("socialid = ?", $socalid)->where('clientID = ?', clientID);
    	$result = $this->_db->fetchRow($select);
    	if(count($result)>0){
    		return false;
    	}else{
    		return true;
    	}
    }
    
    
    // delete invation request 
    public function deleteSpecialDbeeInvitation($data,$token)
    {
        if ($this->_db->update($this->getTableName(DBEESPECIALINVITE), $data ,array(
            "token='" . $token . "'","clientID='" . clientID . "'"
        )))
            return true;
        else
            return false;
    }
    public function showDBeeExpert($dbee,$expertid='')
    {
        $select = $this->_db->select()->from(array(
            'A' => $this->getTableName(EXPERT)
        ), array(
            'A.dbid','A.userid'
        ))->joinInner(array(
            'u' => $this->getTableName(USERS)
        ), 'u.UserID = A.userid', array(
            'u.UserID','u.Username','u.title','u.company',
            'u.Name','u.ProfilePic','u.Expert_Mail_Status'
        ))->joinLeft(array(
            'B' => $this->getTableName(USER_BIOGRAPHY)
        ), 'B.UserID = u.UserID', array(
            "field_value"
        ))->where("A.dbid = ?", $dbee)->where('A.clientID = ?', clientID)
        ->where("A.status = ?", 1);

        if($expertid!='')
            $select->where("A.userid = ?", $expertid);

        $select->group('u.UserID')->order('A.currentdate DESC');
       
        return $this->_db->fetchAll($select); 
    }    
    
    public function mydbeecat($start, $end, $dbee)
    {
        //require_once 'includes/globalfile.php';  
        $blocluser_obj    = new Application_Model_Blockuser($this->_userid);
        $blockuser        = $blocluser_obj->getblockuser($this->_userid);
        $gethiddenDbs  =    $this->gethiddendbee($this->_userid);
        if ($dbee) {
          
            $Offset = (int) $start;       
         
            $select = $this->_db->select()->from(array('c' => 'viewposts' ));
            $select->where("c.clientID = ?", clientID);
            
            if (!empty($blockuser))
                $select->where("c.User NOT IN(?)", $blockuser);
            if (count($gethiddenDbs) > 0) {
            	$select->where("c.DbeeID NOT IN(?)", $gethiddenDbs);
            }

            $select->where("c.clientID = ?", clientID);
            $select->where("c.DbeeID IN (?)", $dbee)->where("c.Active= ?", '1')->where("c.Privategroup= ?", '0')->where("c.eventtype!= ?", '2')->order('c.LastActivity DESC')->limit(PAGE_NUM, $Offset);
            //echo $select;
            return $this->_db->fetchAll($select);
        } else
            return false;        
    }
    
    public function mydbeesortby($start, $end)
    {
        
        $Offset = (int) $start;
        
        $pagenum = PAGE_NUM;
        
        $sql = "SELECT tblDbees.*,User,DbeeOwner,COUNT(CommentID) AS TotalComments from tblDbeeComments,tblDbees where tblDbeeComments.DbeeID=tblDbees.DbeeID AND tblDbees.Active=1 GROUP BY DbeeID ORDER BY TotalComments DESC limit $Offset,$pagenum";
        
        return $this->_db->query($sql)->fetchAll();
        
        
    }
    
    public function getTypeWiseDbee($start,$type,$uid)
    {
        $gethiddenDbs  =  $this->gethiddendbee($this->_userid);
        require_once 'includes/globalfile.php';  
        $skip = ($start-1)*PAGE_NUM;
        $select = $this->_db->select()->from(array('c' => 'viewposts' ));       
        $select->where("c.clientID = ?", clientID);
        
        if(count($gethiddenDbs) > 0)
            $select->where("c.DbeeID NOT IN(?)", $gethiddenDbs);
        if(adminID!=$uid)
           $select->where("c.eventtype!= ?", '2');



        $select->where("c.Type= ?", $type)->where("c.Active= ?", '1')->where("c.Privategroup= ?", '0')->order('c.LastActivity DESC')->limit(PAGE_NUM, $skip);
        //echo $select->__toString(); die;
        return $this->_db->fetchAll($select);
    }
    
    public function mydbeesortbydata($start, $end, $type,$uid)
    {
    	$gethiddenDbs  =    $this->gethiddendbee($this->_userid);
        require_once 'includes/globalfile.php';  
        $Offset = (int) $start;
        $status           = 1;        
        $select           = $this->_db->select()->from(array('c' => 'viewposts' ));       
        $select->where("c.clientID = ?", clientID);

        if (count($gethiddenDbs) > 0) {
        	$select->where("c.DbeeID NOT IN(?)", $gethiddenDbs);
        } 
        if(adminID!=$uid)
        {
           $select->where("c.eventtype!= ?", '2'); 
        }

        $select->where("(" . $type . ")")->where("c.Active= ?", '1')->where("c.Active= ?", '1')->where("c.Privategroup= ?", '0')->order('c.LastActivity DESC')->limit(PAGE_NUM, $Offset);
        return $this->_db->fetchAll($select);
    }

    public function mydbeesortbydata2($start, $end, $type,$uid)
    {
        $gethiddenDbs  =    $this->gethiddendbee($this->_userid);
        require_once 'includes/globalfile.php';  
        $Offset = (int) $start;
        $status           = 1;        
        $select           = $this->_db->select()->from(array('c' => 'viewposts' ));
       
        $select->where("c.clientID = ?", clientID);
        $select->join(array('m' => 'tblDbeeJoinedUser'), 'm.dbeeID =c.DbeeID',  array('timestamp'));  
        if (count($gethiddenDbs) > 0) {
            $select->where("c.DbeeID NOT IN(?)", $gethiddenDbs);
        }        
        $select->where("(" . $type . ")")->where("c.Active= ?", '1')->where("m.userID= ?", $uid)->where("c.Privategroup= ?", '0')->where("m.status= ?", '1')->order('c.LastActivity DESC')->limit(PAGE_NUM, $Offset);
        return $this->_db->fetchAll($select);
        
    }

    public function mydbeesortbydata3($start, $end, $type,$uid)
    {
        $gethiddenDbs  =    $this->gethiddendbee($this->_userid);
        require_once 'includes/globalfile.php';  
        $Offset = (int) $start;
        $status           = 1;        
        $select           = $this->_db->select()->from(array('c' => 'viewposts' ));       
        $select->where("c.clientID = ?", clientID);
        $select->join(array('m' => 'tblSurveyAnswer'), 'm.dbeeid =c.DbeeID',  array('surveyTime'));
        if (count($gethiddenDbs) > 0) {
            $select->where("c.DbeeID NOT IN(?)", $gethiddenDbs);
        } 
        if(adminID!=$uid)
        {
           $select->where("c.eventtype!= ?", '2'); 
        }       
        $select->where("(" . $type . ")")->where("c.Active= ?", '1')->where("m.UserID= ?", $uid)->where("c.Privategroup= ?", '0')->group('c.DbeeID')->order('c.LastActivity DESC')->limit(PAGE_NUM, $Offset);
        //echo 'user'.$uid.':'.$select->__toString(); die;
        return $this->_db->fetchAll($select);
        
    }
    
    
    
    public function dbfeedfilter($start, $end, $score)
    {
        require_once 'includes/globalfile.php';  
        $Offset = (int) $start;
       // $score  = (int) $score;
        $status           = 1;
        $blocluser_obj    = new Application_Model_Blockuser();
        $blockuser        = $blocluser_obj->getblockuser($this->_userid);
        $gethiddenDbs  =    $this->gethiddendbee($this->_userid);
        $select = $this->_db->select()->from(array(
            'c' => $this->_name
        ), $dbtablefields)->joinInner(array(
            'u' => $this->getTableName(USERS)
        ), 'u.UserID = c.User  AND u.clientID = c.clientID', array(
            'u.UserID',
            'u.Name',
            "usercnt" => "COUNT(DISTINCT(u.UserID))",'u.usertype','u.title','u.company'
        ))->joinLeft(array(
            's' => $this->getTableName(SCORING)
        ), 's.MainDB = c.DbeeID', array(
            "Score",
            "Scorecnt" => "count(*)"
        ));
        if (!empty($blockuser)) {
            $select->where("c.GroupID NOT IN(?)", $sub_select);
        }
        if (count($gethiddenDbs) > 0) {
        	$select->where("c.DbeeID NOT IN(?)", $gethiddenDbs);
        }
        $select->where("c.clientID = ?", clientID);
        $select->where( $score)->where("s.Type = ?", '1')->where("c.Active= ?", '1')->where("c.Privategroup= ?", '0')->where("c.eventtype!= ?", '2')->order('Scorecnt DESC')->limit(PAGE_NUM, $Offset);
        return $this->_db->fetchAll($select);
        
    }
    
    public function updatedbee($data, $id)
    {
        
        $this->update($data, array('DbeeID =' . $id,"clientID='" . clientID . "'"));
        
    }
    
    public function addredbee($data)
    {
        
        /*******Insert redbee *********/
        
        if ($this->_db->insert($this->getTableName(REDBEES), $data))
            return true;
        
        else
            return false;
        
    }
	
	 public function insertininviteexport($data)
    {
        /*******Insert inviteexport *********/
        if ($this->_db->insert($this->getTableName(INVITEXPORT), $data))
            return true;
        else
            return false;
    }
	
	
    public function updateGlobalLogin()
    {
        $data = array('logout_token' => md5(date('Y-m-d H:i:s')));
        $this->_db->update('tblAdminSettings', $data ,array(
             "clientID='" . clientID . "'"
        ));
       
    }
    public function destroyGlobalLogin()
    {
        $data = array('logout_token' => '');
        $this->_db->update('tblAdminSettings', $data ,array(
             "clientID='" . clientID . "'"
        ));
       
    }
    public function getLoginToken()
    {
        $select = $this->_db->select()->from('tblAdminSettings')->where('clientID = ?', clientID);
        $result = $this->_db->fetchRow($select);
        if (!empty($result))
            return $result['logout_token'];
        else
            return '0';
    }
    
	
    public function checkeexpertStatus($dbeeid)
    {
        $select = $this->_db->select()->from($this->getTableName(EXPERT))
                                        ->where("dbid = ?", $dbeeid)
                                        ->where('clientID = ?', clientID);
        return count($this->_db->fetchAll($select));
    }
	
	public function checkinviteexpert($dbeeid,$token)
    {
        $select = $this->_db->select()->from($this->getTableName(INVITEXPORT))
                                            ->where("dbeeid = ?", $dbeeid)
                                            ->where("used = ?", 0)
                                            ->where("token = ?", $token)
                                            ->where("clientID = ?", clientID);
        $result = $this->_db->fetchRow($select);
        if ($result)
            return '1';
        else
            return '0';
    }
    public function checkExistenceTokenexpert($dbeeid,$type)
    {
        $select = $this->_db->select()->from($this->getTableName(INVITEXPORT))
            ->where("dbeeid = ?", $dbeeid)
            ->where("type = ?", $type)
        	->where("used = ?", 0)
       		->where("clientID = ?", clientID);
        return $this->_db->fetchRow($select);
    }
    
    public function updateredbee($data, $dbee)
    {
        return $this->_db->update($this->getTableName(REDBEES), $data, array(
            "DbeeID='" . $dbee . "'","clientID='".clientID."'"
        ));
    }
    
    public function chkredbee($dbee, $user, $userid)
    {
        
        $select = $this->_db->select()->from($this->getTableName(REDBEES))->where("DbeeID = ?", $dbee)->where("DbeeOwner = ?", $user)->where("ReDBUser = ?", $userid)->where('clientID = ?', clientID);
        
        $result = $this->_db->fetchRow($select);
        
        if ($result['DbeeID'])
            return true;
        
        else
            return false;
        
    }
    
   
    
    public function insertmydb($data)
    {       
       
        $insertid = $this->_db->insert($this->getTableName(DBEE),$data); 
        return $last_insertedId=$this->_db->lastInsertId();
    }
    
    public function insertpol($data2, $insertid, $ChosenCount)
    {
        if ($data2['polloption3'] == '' && $data2['polloption4'] == ''){
            $j =2;   
        }elseif ($data2['polloption4'] == ''){
        	$j = 3;
        }else
            $j = 4;
        
        for ($i = 1; $i <= $j; $i++) {
            
            $dd = array(
                'OptionText' => $data2['polloption' . $i],
                
                'PollID' => $insertid,
                
                'ChosenCount' => $ChosenCount,
                'clientID' => clientID
                
            );
            
            
            
            if ($this->_db->insert($this->getTableName(POLL_OPTION), $dd));
            
        }
        
        return true;
        
    }
    
    public function getuserid($dbee)
    {
        $select = $this->_db->select()->from(array(
            'c' => $this->_name
        ), array(
            'c.User'
        ))->where("DbeeID = ?", $dbee)->where('clientID = ?', clientID);
        $result = $this->_db->fetchRow($select);
        return $result['User']; 
    }
    
    public function getuserdbee($dbee)
    { 
        $select = $this->_db->select()->from(array(
            'c' => $this->_name
        ))->where("c.DbeeID = ?", $dbee)->where('c.clientID = ?', clientID);
        
        return $this->_db->fetchRow($select);   
    }

    public function getTagdbee($tag)
    { 
        $select = $this->_db->select()->from(array(
            'c' => $this->_name
        ))->where("c.dbee_tag = ?", $tag)->where('c.clientID = ?', clientID);
        
        return $this->_db->fetchRow($select);   
    }
    
    public function getDbeeDetails($dbee)
    {
        $select = $this->_db->select()->from(array(
            'c' => $this->_name
        ))->where("c.DbeeID = ?",$dbee)->where("c.clientID =?",clientID);
        return $this->_db->fetchRow($select);
    }
    public function getcomment($dbee)
    {
        $select = $this->_db->select()->from(array(
            'c' => $this->_name
        ), array(
            'c.Comments'
        ))->where("DbeeID= ?", $dbee)
          ->where("c.clientID =?",clientID);
        $result = $this->_db->fetchRow($select);
        return $result['Comments'];
    }
	public function getdbeenotification($user,$activity,$userid)
	{		
		$gethiddenDbs  =    $this->gethiddendbee($user);
		$select = $this->_db->select()
			->from(array('c' => $this->getTableName(DBEE)))
				->join(array('u' => $this->getTableName(USERS)), 'u.UserID = c.User');						
						$select->where('c.User IN(?)',$user)->where("c.Privategroup= ?", '0')->where('c.clientID = ?', clientID);
						if (count($gethiddenDbs) > 0) {
							$select->where("c.DbeeID NOT IN(?)", $gethiddenDbs);
						}
						$select->where('c.PostDate > ?',$activity);	
		$result = $this->_db->fetchAll($select);
		return $result;
	}
    
    
    
    public function getdbeenotificationcnt($user, $activity)
    {
        
        $select = $this->_db->select()->from(array(
            'c' => $this->getTableName(DBEE)
        ))->join(array(
            'u' => $this->getTableName(USERS)
        ), 'u.UserID = c.User')->where('c.User IN(?)', $user)->where('c.PostDate > ?', $activity)->where('c.clientID = ?', clientID);
        
        return $this->_db->fetchAll($select)->count();
        
        
    }
    
    
    
    public function getdbeenotificationrow($user, $activity)
    {
        
        $select = $this->_db->select()->from(array(
            'c' => $this->getTableName(DBEE)
        ))->join(array(
            'u' => $this->getTableName(USERS)
        ), 'u.UserID = c.User')->where('c.User IN(?)', $user)->where('c.clientID = ?', clientID)->where('c.PostDate > ?', $activity)->order('c.PostDate DESC');
        
        return $this->_db->fetchRow($select);
        
    }
    
    
    
    public function dbeeusernotifi($id)
    {
        
        $select = $this->_db->select()->distinct()->from($this->getTableName(COMMENT), array(
            'DbeeID'
        ))->where('UserID = ?', $id)->where('clientID = ?', clientID)->group('DbeeID')->order('CommentDate Desc');
        
        $result = $this->_db->fetchAll($select);
        foreach ($result as $data):
            $useid[] = $data['DbeeID'];
        endforeach;
        
        return $useid;
        
    }
    
    public function mentionuser($id)
    {
    
    	$select = $this->_db->select()->distinct()->from($this->getTableName(MENTIONS), array(
    			'DbeeID'
    	))->where('UserMentioned = ?', $id)->where('clientID = ?', clientID);   
    
    	$result = $this->_db->fetchAll($select);
    	foreach ($result as $data):
    	$dbid[] = $data['DbeeID'];
    	endforeach;
    
    	return $dbid;
    
    }
    
    public function updatedbeecomment($data, $id)
    {
        
        $this->update($data,array(
            "DbeeID='" . $id . "'", "clientID='".clientID."'"
        ));
        
    }
    
    
    
    public function dbeechkdata($lastactivity, $cookieuser,$ShowVideoEvent)
    {
        $this->myclientdetails = new Application_Model_Clientdetails();
        $blocluser_obj = new Application_Model_Blockuser($cookieuser);        
        $blockuser = $blocluser_obj->getblockuser($cookieuser);
        $gethiddenDbs  =    $this->gethiddendbee($cookieuser);

        // checking for  scheduled post
        $Curractivity   = date('Y-m-d H:i:s');
        $select = $this->_db->select()->from(array(
            'c' => $this->getTableName(DBEE)
        ), 'DbeeID')->where('c.LastActivity < ?', $Curractivity)->where("c.Privategroup= ?", '0')->where("c.LastCommentUser != ?", $this->_userid)->where("c.Active= ?",2 )->where("c.eventtype!= ?", '2');
        $select->where("c.clientID = ?", clientID)->where("c.PrivatePost = ?", 0);
        //$select->where("c.event_type != ?", 3);
        if (!empty($blockuser))       
            $select->where("c.User NOT IN(?)", $blockuser);
        if (count($gethiddenDbs) > 0)
            $select->where("c.DbeeID NOT IN(?)", $gethiddenDbs);
         $select->order('c.LastActivity DESC')->limit('1');

        $countResDb =   $this->_db->fetchAll($select);
        $chkDBval     =   count($countResDb);
        if( $chkDBval==1)
        {
            $data     = array('Active' => '1');   
            $stUpdate = $this->myclientdetails->updatedata_global('tblDbees',$data,'DbeeID',$countResDb[0]['DbeeID']);
        }

        // checking for new post  
        $select = '';
        $select = $this->_db->select()->from(array(
            'c' => $this->getTableName(DBEE)
        ), 'DbeeID')->where('c.LastActivity > ?', $lastactivity)->where("c.Privategroup= ?", '0')->where("c.LastCommentUser != ?", $this->_userid)->where("c.Active= ?",1)->where("c.eventtype!= ?", '2');
        $select->where("c.clientID = ?", clientID)->where("c.PrivatePost = ?", 0);
        if (!empty($blockuser))
            $select->where("c.User NOT IN(?)", $blockuser);

        if($ShowVideoEvent==0){
            $select->where("c.Type != ?", 6);
        }
        if (count($gethiddenDbs) > 0) 
        	$select->where("c.DbeeID NOT IN(?)", $gethiddenDbs);
            $select->where("c.event_type != ?", 3);        
        $countRes =   $this->_db->fetchAll($select);
        return count($countRes);
        
    }
    


    public function dbeechkgroupdata($lastactivity, $cookieuser,$groupid)
    {
        $this->myclientdetails = new Application_Model_Clientdetails();
        $blocluser_obj = new Application_Model_Blockuser($cookieuser);        
        $blockuser = $blocluser_obj->getblockuser($cookieuser);
        $gethiddenDbs  =    $this->gethiddendbee($cookieuser);

        // checking for  scheduled post
        $Curractivity   = date('Y-m-d H:i:s');
        $select = $this->_db->select()->from(array(
            'c' => $this->getTableName(DBEE)
        ), 'DbeeID')->where('c.LastActivity < ?', $Curractivity)->where("c.Privategroup= ?", '0')->where("c.LastCommentUser != ?", $this->_userid)->where("c.Active= ?",2 )->where("c.eventtype!= ?", '2');
        $select->where("c.clientID = ?", clientID);
        $select->where("c.GroupID = ?", $groupid);
        if (!empty($blockuser))       
            $select->where("c.User NOT IN(?)", $blockuser);
        if (count($gethiddenDbs) > 0)
            $select->where("c.DbeeID NOT IN(?)", $gethiddenDbs);
         $select->order('c.LastActivity DESC')->limit('1');

        $countResDb =   $this->_db->fetchAll($select);
        $chkDBval     =   count($countResDb);
        if($chkDBval==1)
        {
            $data     = array('Active' => '1');   
            $stUpdate = $this->myclientdetails->updatedata_global('tblDbees',$data,'DbeeID',$countResDb[0]['DbeeID']);
        }

        // checking for new post 
        $select = '';
        $select = $this->_db->select()->from(array(
            'c' => $this->getTableName(DBEE)
        ), 'DbeeID')->where('c.LastActivity > ?', $lastactivity)->where("c.Privategroup= ?", '0')->where("c.LastCommentUser != ?", $this->_userid)->where("c.Active= ?",1)->where("c.eventtype!= ?", '2');
        $select->where("c.clientID = ?", clientID);
        $select->where("c.GroupID = ?", $groupid);
        if (!empty($blockuser))
            $select->where("c.User NOT IN(?)", $blockuser);
        if (count($gethiddenDbs) > 0) 
            $select->where("c.DbeeID NOT IN(?)", $gethiddenDbs);        
        $countRes =   $this->_db->fetchAll($select);
        return count($countRes);
        
    }
    
    public function gettblredbdbee($start, $end, $userid)
    {
        require_once 'includes/globalfile.php';  
        $Offset = (int) $start;
        
        $blocluser_obj = new Application_Model_Blockuser($userid);        
        $blockuser = $blocluser_obj->getblockuser($userid);
        $gethiddenDbs  =    $this->gethiddendbee($userid);
        $select = $this->_db->select()->distinct('c.DbeeID')->from(array(
            'c' => $this->_name
        ), $dbtablefields)->join(array(
            'r' => $this->getTableName(REDBEES)
        ), 'r.DbeeID = c.DbeeID AND r.clientID = c.clientID')->joinLeft(array(
            'u' => $this->getTableName(USERS)
        ), 'u.UserID = c.User  AND u.clientID = c.clientID');
        
        if (!empty($blockuser)) {            
            $select->where("c.User NOT IN(?)", $blockuser);
        }
        if (count($gethiddenDbs) > 0) {
        	$select->where("c.DbeeID NOT IN(?)", $gethiddenDbs);
        }      
        $select->where("c.clientID = ?", clientID);
        $select->where('r.ReDBUser = ?', $userid);
        
        $select->where("c.Active= ?", '1')->where("c.Privategroup= ?", '0')->where('c.clientID = ?', clientID)->where("c.eventtype!= ?", '2')->order('c.LastActivity DESC')->limit(PAGE_NUM, $Offset);
        
        return $this->_db->fetchAll($select);
    }
    
    public function attendies($dbeeid)
    {
        $select = $this->_db->select()->from(array(
            'U' => $this->getTableName(USERS),
            array(
                'U.*'
            )
        ))->join(array(
            'S' => $this->getTableName(DbeeJoinedUser)
        ), 'S.userID = U.UserID', array())->where('S.dbeeID= ?', $dbeeid)->where("S.status= ?", '1')->where('S.clientID = ?', clientID)->where('S.userID != ?', adminID);
        if(isADMIN!=1) 
        {
          $select->where("U.hideuser != ?", 1);
        }
   
        return $this->_db->fetchAll($select);
    }
    
    public function Userattendies($dbeeid,$UserID)
    {
         $select = $this->_db->select()
                            ->from($this->getTableName(DbeeJoinedUser))
                            ->where('dbeeID =' . $dbeeid)
                            ->where("userID= ?", $UserID)
                            ->where("status= ?", 1)->where('clientID = ?', clientID);
        return count($this->_db->fetchAll($select));
    }
    public function Userattendies2($dbeeid,$UserID)
    {
         $select = $this->_db->select()
                            ->from($this->getTableName(DbeeJoinedUser))
                            ->where('dbeeID =' . $dbeeid)
                            ->where("userID= ?", $UserID)->where('clientID = ?', clientID);
                            
        return count($this->_db->fetchAll($select));
    }
    
    public function gettblReDbeesbyid($id)
    {
        
        $select = $this->_db->select()->from($this->getTableName(REDBEES), array(
            'DbeeID'
        ))->where('ReDBUser =' . $id)->where('clientID = ?', clientID);
        
        $result = $this->_db->fetchAll($select);
        
        foreach ($result as $data):
            $useid[] = $data['DbeeID'];
        endforeach;
        
        return $useid;
        
    }
    
    
    
    public function getfeeoptiongroup($id)
    {
        $select = $this->_db->select()->from($this->getTableName(USERS))->where('UserID =' . $id)->where('clientID = ?', clientID);
        return $this->_db->fetchRow($select);
        
    }
    
    // use for home search
    
    public function getsearchdbee($text,$limit,$usertype='')
    {
    	$gethiddenDbs  =    $this->gethiddendbee($this->_userid);
        $text=str_replace('$', '\$',addslashes($text));
        $select = $this->_db->select()->distinct('c.DbeeID')->from(array(
            'c' => $this->_name,
            array(
                'c.DbeeID',
                'c.Type',
                'c.Text',
            	'c.surveyTitle',
            	'c.surveyPdf',
                'c.Link',
                'c.LinkTitle',
                'c.LinkDesc',
                'c.UserLinkDesc',
                'c.Pic',
                'c.PicDesc',
                'c.Vid',
                'c.VidDesc',
                'c.VidSite',
                'c.VidID',
                'c.PollText',
                'c.User',
                'c.PostDate',
                'c.RssFeed',
            	'c.dburl'
            )
        ))->joinLeft(array(
            'u' => $this->getTableName(USERS)
        ), 'u.UserID = c.User', array(
            'u.UserID',
            'u.Name',
        	'u.Username',
            'u.lname',
            'u.usertype',
            'u.full_name',
            'u.hideuser'
        ))->where('u.clientID = ?', clientID);       
        $select->where('Text LIKE "%'.$text.'%" OR FIND_IN_SET(("'.$text.'"),DbTag)');
        $select->where("c.Active= ?", '1')->where("c.Privategroup= ?", '0')->where("c.eventtype!= ?", '2');
        if($usertype!=100 && isADMIN!=1)
         {
           $select->where("u.usertype != ?", 100);
         } 
          
        if (count($gethiddenDbs) > 0) {
        	$select->where("c.DbeeID NOT IN(?)", $gethiddenDbs);
        }             
        $select->order(array(
            'LastActivity DESC'
        ))->limit($limit);
       //  echo $select->__toString(); die;
        return $this->_db->fetchAll($select);
        
        
    }

     public function getLatestAllHashTagUser($text,$skip='')
    {    
          $sql = "SELECT `u`.`RegistrationDate`,`u`.`Email`, `u`.`Name`, `u`.`Username`, `u`.`ProfilePic`, `u`.`UserID` FROM `tblDbees` AS `c` INNER JOIN `tblUsers` AS `u` ON u.UserID = c.User WHERE FIND_IN_SET('".$text."',c.DbTag) AND (c.Active = '1') AND (c.clientID= '".clientID."') AND c.Privategroup = 0 AND c.eventtype!= 2";

        return $this->_db->query($sql)->fetchAll();
    }


    public function getLatestAllCommentHashTagUser($text,$skip='')
    {    
          $sql = "SELECT `p`.`RegistrationDate`,`p`.`Email`, `p`.`Name`, `p`.`Username`, `p`.`ProfilePic`, `p`.`UserID` FROM `tblDbeeComments` AS `d` INNER JOIN `tblUsers` AS `p` ON p.UserID = d.UserID WHERE FIND_IN_SET('".$text."',d.DbTag) AND (d.Active = '1') AND (d.clientID= '".clientID."') GROUP BY `UserID` ";
          
        if($skip!='')
        {
            $skip = ($skip-1)*8;
            $sql.="LIMIT $skip , 8";
        }

        return $this->_db->query($sql)->fetchAll();
    }
    
     public function getsearchHashTagdbee($text,$limit)
    {
    	$gethiddenDbs  =    $this->gethiddendbee($this->_userid);
        $select = $this->_db->select()->distinct('c.DbeeID')->from(array(
            'c' => $this->_name,
            array(
                'c.DbeeID',
                'c.Type',
                'c.Text',
            	'c.surveyTitle',
            	'c.surveyPdf',
                'c.Link',
                'c.LinkTitle',
                'c.LinkDesc',
                'c.UserLinkDesc',
                'c.Pic',
                'c.PicDesc',
                'c.Vid',
                'c.VidDesc',
                'c.VidSite',
                'c.VidID',
                'c.PollText',
                'c.User',
                'c.PostDate',
                'c.RssFeed',
                'c.dburl'
            )
        ))->join(array(
            'u' => $this->getTableName(USERS)
        ), 'u.UserID = c.User', array(
            'u.UserID',
            'u.Name',
            'u.Username',
            'u.lname',
            'u.full_name',
            'u.usertype',
            'u.hideuser'
        ))->where('c.DbTag LIKE ?', "%$text%")->where("c.Privategroup= ?", '0')->where("c.Active= ?", '1')->where("c.eventtype!= ?", '2')->where('c.clientID = ?', clientID);
        if (count($gethiddenDbs) > 0) {
        	$select->where("c.DbeeID NOT IN(?)", $gethiddenDbs);
        } 
        if($usertype!=100)
         {
           $select->where("u.usertype != ?", 100);
         } 
                     
        $select->order(array(
            'LastActivity DESC'
        ))->limit($limit);
        return $this->_db->fetchAll($select);  
    }


    public function getCommentsearchHashTagdbee($text,$limit)
    {
    	$gethiddenDbs  =    $this->gethiddendbee($this->_userid);
        $select = $this->_db->select()->from(array(
            'c' => $this->_name,
            array(
                'c.DbeeID',
                'c.Type',
                'c.Text',
            	'c.surveyTitle',
            	'c.surveyPdf',
                'c.Link',
                'c.LinkTitle',
                'c.LinkDesc',
                'c.UserLinkDesc',
                'c.Pic',
                'c.PicDesc',
                'c.Vid',
                'c.VidDesc',
                'c.VidSite',
                'c.VidID',
                'c.PollText',
                'c.User',
                'c.PostDate',
                'c.RssFeed',
                'c.dburl'
            )
        ))->join(array(
            'u' => $this->getTableName(USERS)
        ), 'u.UserID = c.User', array(
            'u.UserID',
            'u.Name',
            'u.Username'
        ))->joinLeft(array(
            'M' => $this->getTableName(COMMENT)
        ), 'c.DbeeID = M.DbeeID', array(
            'M.CommentID','M.DbTag','M.Active','M.DbeeID'
        ))->where('M.DbTag LIKE ?', "%$text%")->where("c.Privategroup= ?", '0')->where("M.Active= ?", '1')->where("c.eventtype!= ?", '2')->where('c.clientID = ?', clientID);
        if (count($gethiddenDbs) > 0) {
        	$select->where("c.DbeeID NOT IN(?)", $gethiddenDbs);
        }
        if($usertype!=100)
         {
           $select->where("u.usertype != ?", 100);
         } 
                
        $select->order(array(
            'LastActivity DESC'
        ))->limit($limit);
        return $this->_db->fetchAll($select);  
    }
    public function getsearchdbeecnt($text,$usertype='')
    {
        
    	$gethiddenDbs  =    $this->gethiddendbee($this->_userid);
        $text=str_replace('$', '\$',addslashes($text));
        $select = $this->_db->select()->from($this->getTableName(DBEE), array(
            'Text',
            'DbeeID'
        ))->where('Text LIKE ?', "%$text%")->where("Privategroup= ?", '0')->where("eventtype!= ?", '2')->where('clientID = ?', clientID);
        if (count($gethiddenDbs) > 0) {
        	$select->where("DbeeID NOT IN(?)", $gethiddenDbs);
        }             
        $select->order(array(
            'DbeeID asc'
        ));
         return count($this->_db->fetchAll($select));
    }
    
    
    
    public function getsearchuser($text,$limit,$usertype='',$isadmin='')
    {
        $this->myclientdetails = new Application_Model_Clientdetails();
        $this->User_Model = new Application_Model_DbUser();
        $Result = $this->User_Model->globalSetting();
        $userconfirmed = $this->userconfirmed = $Result[0]['userconfirmed'];

         $text = $this->myclientdetails->customEncoding($text);
         $text=str_replace('$', '\$',addslashes($text));
        //die;
        $select = $this->_db->select()->from($this->getTableName(USERS), array(
            'UserID',
            'Name',
            'lname',
        	'Username',
            'usertype',
            'ProfilePic'
        ));
		
        $text=str_replace('mabirdnny#', '', $text);
		$pos = strpos($text,'@');

		if ($pos === false) {
			$select->where("REPLACE(Name, 'mabirdnny#', '') LIKE '%$text%' OR REPLACE(full_name, 'mabirdnny#', '') LIKE '%$text%' OR REPLACE(Username, 'mabirdnny#', '') LIKE '%$text%' OR REPLACE(full_name, 'mabirdnny#', '') REGEXP '^".$text.".*$'")->where('clientID = ?', clientID);
		} else {
			$text=substr($text,$pos+1);
			$select->where("REPLACE(Name, 'mabirdnny#', '') LIKE '%$text%' OR REPLACE(full_name, 'mabirdnny#', '') LIKE '%$text%' OR REPLACE(Username, 'mabirdnny#', '') LIKE '%$text%' OR REPLACE(full_name, 'mabirdnny#', '') REGEXP '^".$text.".*$'")->where('clientID = ?', clientID);
		}

         if($userconfirmed==0)
         {
           $select->where("Status= ?", '1');
         } 
         if($usertype!=100 && isADMIN!=1)
         {
           $select->where("usertype != ?", 100);
         }
        /* if($usertype==100 && isADMIN!=1)
         {
           $select->where("usertype = ?", 100);
         } */

         if(isADMIN!=1) 
         {
           $select->where("hideuser != ?", 1);
         }     

        if($isadmin==0)
        {
           $select->where('role != ?', 1)->where('usertype != ?', 10);                
        }
       // echo $select->__toString(); die;
        $select->order(array(
            'RegistrationDate DESC'
        ))->limit($limit);

        
       
        return $this->_db->fetchAll($select);
    }
    
    public function getsearchusercnt($text,$usertype='',$isadmin='')
    {
        $this->myclientdetails = new Application_Model_Clientdetails();
        $this->User_Model = new Application_Model_DbUser();
        $Result = $this->User_Model->globalSetting();
        $userconfirmed = $this->userconfirmed = $Result[0]['userconfirmed'];
        $text = $this->myclientdetails->customEncoding($text);
        $text=str_replace('$', '\$',addslashes($text));
       // $select = $this->_db->select()->from($this->getTableName(USERS), 'UserID'));
        $select = $this->_db->select()->from($this->getTableName(USERS), array('UserID'));

        $text=str_replace('mabirdnny#', '', $text);
        $pos = strpos($text,'@');

        if ($pos === false) {
            $select->where("REPLACE(Name, 'mabirdnny#', '') LIKE '%$text%' OR REPLACE(full_name, 'mabirdnny#', '') LIKE '%$text%' OR REPLACE(Username, 'mabirdnny#', '') LIKE '%$text%' OR REPLACE(full_name, 'mabirdnny#', '') REGEXP '^".$text.".*$'")->where('clientID = ?', clientID);
        } else {
            $text=substr($text,$pos+1);
            $select->where("REPLACE(Name, 'mabirdnny#', '') LIKE '%$text%' OR REPLACE(full_name, 'mabirdnny#', '') LIKE '%$text%' OR REPLACE(Username, 'mabirdnny#', '') LIKE '%$text%' OR REPLACE(full_name, 'mabirdnny#', '') REGEXP '^".$text.".*$'")->where('clientID = ?', clientID);
        }

        if($userconfirmed==0)
         {
           $select->where("Status= ?", '1');
         }
         if($usertype!=100 && isADMIN!=1)
         {
           $select->where("usertype != ?", 100);
         } 
         /*if($usertype==100 && isADMIN!=1)
         {
           $select->where("usertype = ?", 100);
         } */
         if(isADMIN!=1) 
         {
           $select->where("hideuser != ?", 1);
         } 
        if($isadmin==0)
        {
           $select->where('role != ?', 1)->where('usertype != ?', 10);                
        }
         //echo $select->__toString(); die;
         return count($this->_db->fetchAll($select));   
    }
    
    public function gettwitertag($dbee)
    {
        $select = $this->_db->select()->from($this->getTableName(DBEE), 'TwitterTag')->where('DbeeID = ?', $dbee)->where('clientID = ?', clientID);
        $result = $this->_db->fetchRow($select);
        return $result['TwitterTag'];
    }
    
    
    
    public function addactivities($data)
    {
        $this->_db->insert($this->getTableName(ACTIVITY), $data);
        return true;   
    }
    
    public function getusername($userid)
    {   
        $select = $this->_db->select()->from($this->getTableName(USERS))->where('UserID = ?', $userid)->where('clientID = ?', clientID);
        $result = $this->_db->fetchRow($select);
        return $result;
        
    }
    public function getusername2($userid)
    {
    
    	$select = $this->_db->select()->from($this->getTableName(USERS), 'Username')->where('UserID = ?', $userid)->where('clientID = ?', clientID);
    	$result = $this->_db->fetchRow($select);
    	return $result['Username'];
    
    }    
    
    public function getrowuser($userid)
    {
        
        $select = $this->_db->select()->from($this->getTableName(USERS))->where('UserID = ?', $userid)->where('clientID = ?', clientID);
        return $this->_db->fetchRow($select);
    }
    
    
    
    public function updatemydb($data, $dbeeid)
    {
        $this->update($data, array(
            "DbeeID='" . $dbeeid . "'", "clientID='".clientID."'"
        ));
        return true;
        
    }
    
    
    
    public function getdbeeleague($user)
    {      
        $select = $this->_db->select()->distinct('c.DbeeID')->from(array(
            'c' => $this->_name,
            array(
                'c.DbeeID',
                'c.Type',
                'c.Text',
            	'c.surveyTitle',
            	'c.surveyPdf',
                'c.Link',
                'c.LinkTitle',
                'c.LinkDesc',
                'c.UserLinkDesc',
                'c.Pic',
                'c.PicDesc',
                'c.Vid',
                'c.VidDesc',
                'c.VidSite',
                'c.VidID',
                'c.PollText',
                'c.User',
                'c.PostDate',
                'c.RssFeed'
            )
        ))->joinLeft(array(
            'u' => $this->getTableName(USERS)
        ), 'u.UserID = c.User', array(
            'u.UserID',
            'u.Name',
        	'u.Username'
        ))->where('user= ?', $user)->where("c.Privategroup= ?", '0')->where("c.Active= ?", '1')->where("c.eventtype!= ?", '2')->where('c.clientID = ?', clientID);
               
        $select->order(array(
            'PostDate DESC'
        ))->limit(1);
        return $this->_db->fetchAll($select);
        
    }
    
    
    public function inserblockuser($data)
    {
        if ($this->_db->insert($this->getTableName(BLOCKSUSER_DBEE), $data))
            return true;
        else
            return false;
    }
    
    public function insertuserrss($data)
    {
        $this->_db->insert($this->getTableName(USER_RSS), $data);
    }
    
    public function checkdbeeexist($id)
    {
        $select = $this->_db->select();
        $select->from('tblDbees');
        $select->where("DbeeID =?", $id)->where("clientID = ?", clientID);
        return $this->_db->fetchAll($select);
    }
    
    public function insertiwitter($data)
    {   
        if ($this->_db->insert($this->getTableName(TWITTER), $data))
            return true;
        else
            return false;
    }
    public function gettwitter($id)
    {
        $select = $this->_db->select()->from($this->getTableName(TWITTER))->where("DbeeID = ?", $id)->where("clientID = ?", clientID);
        return $this->_db->fetchAll($select);
        
    }
     public function gettwitterNew($id, $keyword)
    {
        $select = $this->_db->select()->from($this->getTableName(TWITTER))->where("clientID = ?", clientID)->where("DbeeID = ?", $id)->where('Keyword = ?', $keyword);
        //echo $select->__toString();
        return $this->_db->fetchAll($select);
        
    }
    public function updatetwtsreply($data, $id)
    {
        $this->update($data, array(
            "DbeeID='" . $id . "'", "clientID='".clientID."'"
        ));
    }
    
    public function getdbright($dbee)
    {
        $select = $this->_db->select()->from($this->getTableName(DBEE))->where('DbeeID = ?', $dbee)->where('clientID = ?', clientID);
        return $this->_db->fetchRow($select);
        
    }
    public function deletetwt($id,$keyword)
    {
        $this->_db->delete($this->getTableName(TWITTER), array('DbeeID= ?' => $id ,'Keyword= ?'=> $keyword, 'clientID = ?'=>clientID));
        return true;
    }

    public function deletetwtby($id,$userid)
    {
       //echo $id.' '.$userid.' '.$this->getTableName(TWITTER);die;
        $this->_db->delete($this->getTableName(TWITTER), array('ID= ?' => $id ,'clientID = ?'=>clientID,'UserID=?'=>$userid));
        return true;
    }
    
    
    public function chkrss($userid)
    {
        $select = $this->_db->select()->from($this->getTableName(USER_RSS))->where('User = ?', $userid)->where('clientID = ?', clientID);
        $result = $this->_db->fetchRow($select);
        return $result['User'];
    }
    
    public function twitterchk($dbeeid)
    {
        $select = $this->_db->select()->from($this->getTableName(TWITTER))->where('DbeeID = ?', $dbeeid)->where('clientID = ?', clientID);
        $result = $this->_db->fetchRow($select);
        if (count($result['DbeeID']) > 0)
            return 1;
        else
            return 0;
    }
    public function twitterchkNew($dbeeid, $keyword)
    {
        $select = $this->_db->select()->from($this->getTableName(TWITTER))->where('clientID = ?', clientID)->where('DbeeID = ?', $dbeeid)->where('Keyword = ?', $keyword);
        $result = $this->_db->fetchRow($select);
        if (count($result['DbeeID']) > 0)
            return 1;
        else
            return 0;
    }
    public function stopchk($userid)
    {
        $select = $this->_db->select()->from($this->getTableName(USERS))->where('UserID = ?', $userid)->where('clientID = ?', clientID);
        $result = $this->_db->fetchRow($select);
        if ($result['ShowPPBox'] == 1)
            return true;
        else
            return false;
    }
    
    public function updatestoppopup($data, $userid)
    {
        $this->_db->update($this->getTableName(USERS), $data, array(
            "UserID='" . $userid . "'", "clientID='".clientID."'"
        ));
        return true;
    }
    
    public function gettype($dbid)
    {
    	$select = $this->_db->select();
    	$select->from($this->getTableName(DBEE),'Type');
    	$select->where("DbeeID =?", $dbid)->where('clientID = ?', clientID);
    	$data = $this->_db->fetchRow($select);    
    	return $data['Type'];    
    }
    
    public function getdbeedetail($dbeeid)
    {
       	$select = $this->_db->select()->from(array(
    			'c' => $this->_name,
    			array(
    					'c.DbeeID',
    					'c.Type',
    					'c.Text',
    					'c.surveyTitle',
    					'c.surveyPdf',
    					'c.Link',
    					'c.LinkTitle',
    					'c.LinkDesc',
    					'c.UserLinkDesc',
    					'c.Pic',
    					'c.PicDesc',
    					'c.Vid',
    					'c.VidDesc',
    					'c.VidSite',
    					'c.VidID',
    					'c.PollText',
    					'c.User',
    					'c.PostDate',
    					'c.RssFeed',
                        'c.dburl',
                        'c.DbTag',
    					'c.StopTweetsReply',
                        'c.Attachment',
                        'c.events',
                        'c.GroupID'
    			)
    	))->join(array(
    			'u' => $this->getTableName(USERS)
    	), 'u.UserID = c.User AND u.clientID = c.clientID')->where('c.DbeeID = ?', $dbeeid)->where('c.clientID = ?', clientID);    
    	
    	$select->order(array(
    			'PostDate DESC'
    	));
    
    	return $this->_db->fetchRow($select);
    	
    }
    public function updatecoocke($data, $user)
    {
    	 $this->_db->update($this->getTableName(USERS), $data, array(
            "UserID='" . $userid . "'", "clientID='".clientID."'"
        ));
    	 
    	 return true;
    }
    public function getuserdetail($userid)
    {
    
    	$select = $this->_db->select();
	    	$select->from($this->getTableName(USERS));
	    		$select->where("UserID =?", $userid)->where('clientID = ?', clientID);
	    	$data = $this->_db->fetchRow($select);
    	return $data;
    
    }    
    
    public function getidbytitle($dburl)
    {
    	$select = $this->_db->select()
    		->from($this->getTableName(DBEE),array('DbeeID'))
    			->where("dburl = ?", $dburl)->where('clientID = ?', clientID);
    	//echo $select->__toString();
    //	exit;
    	$data = $this->_db->fetchRow($select);
    	return $data['DbeeID'];    
    }
    
    public function chkusername($username)
    {
        $this->myclientdetails = new Application_Model_Clientdetails();
    	$select = $this->_db->select()
	    	->from($this->getTableName(USERS),array('UserID'))
	    		->where("Username = ?", $username)->where('clientID = ?', clientID);    	
    	$data = $this->_db->fetchRow($select);
        if($data['UserID']>0){
        	$usernames = $this->myclientdetails->customDecoding($username).''.date('-s');
       		return str_replace('-', '', $usernames);
        }else 
        	return $this->myclientdetails->customDecoding($username);
    } 
    
    public function getdburltitle($dbeeid)
    {
    	$select = $this->_db->select()
    		->from($this->getTableName(DBEE),array('dburl','Type'))
    			->where("DbeeID = ?", $dbeeid)->where("clientID = ?", clientID);    	
    	$data = $this->_db->fetchRow($select);    	
    	return $data['dburl'];	    	    	
    }
    
    public function getdburltitle_update($dbeeid,$status=0)
    {
    	$commonfun  = new Application_Model_Commonfunctionality();
    	$select = $this->_db->select()
    	->from($this->getTableName(DBEE),array('dburl','Type','Text','UserLinkDesc','PicDesc','VidDesc','PollText','LinkDesc'))
    	->where("DbeeID = ?", $dbeeid)->where("clientID = ?", clientID);
    	$data = $this->_db->fetchRow($select);
        
        if($data['dburl']!='' && $status==1)
            return $data['dburl'];

        if($data['Type']==1)
            $dburl = $commonfun->makeSeo($data['Text'],'','save');
        elseif($data['Type']==2){
            if($data['UserLinkDesc']!='') $dburl = $commonfun->makeSeo($data['UserLinkDesc'],'','save'); 
            else $dburl = $commonfun->makeSeo($data['LinkTitle'],'','save');
        }
        elseif($data['Type']==3)
            $dburl = $commonfun->makeSeo($data['PicDesc'],'','save');
        elseif($data['Type']==4)
            $dburl = $commonfun->makeSeo($data['VidDesc'],'','save');
        elseif($data['Type']==5)
            $dburl = $commonfun->makeSeo($data['PollText'],'','save');
        if($dburl!='')
        {
            if($dburl=='') $dburl = $commonfun->makeSeo(time().rand(),'','save');
            $data2=array('dburl'=> $dburl); 
            $this->updatedbee($data2, $dbeeid);
        }
        return $dburl;
    	
    }
    public function getalldbee()
    {
    	$select = $this->_db->select()
    	->from($this->getTableName(DBEE));    	
    	$result = $this->_db->fetchAll($select);
    	return $result;
    }
    public function userdetailallbyname($username)
    {
    	$select = $this->_db->select()
    		->from($this->getTableName(USERS),array('UserID'))
    			->where("Username = ?", $username)->where('clientID = ?', clientID);   
    	$result = $this->_db->fetchRow($select);
    	return $result['UserID'];
    }
    public function getdburl($dbeeid)
    {
    	$select = $this->_db->select()
    	->from($this->getTableName(DBEE),array('dburl'))
    		->where("DbeeID = ?", $dbeeid)->where('clientID = ?', clientID);    	
    	$data = $this->_db->fetchRow($select);
    	return $data['dburl'];   
    }
    public function chkdbeetitle($title)
    {
    	$select = $this->_db->select()
    		->from($this->getTableName(DBEE),array('DbeeID'))
    			->where("dburl = ?", $title)->where('clientID = ?', clientID);
    	$data = $this->_db->fetchAll($select);
    	if(count($data)>0)
    		return false;
    	else 
    		return true;     	
    }
   
    public function getdbeealldbeeleague($userid)
    {
        $Offset           = (int) $ds;
        $select           = $this->_db->select()->from(array(
            'c' => $this->_name
        ), array(
            'c.DbeeID',
            'c.Type',
            'c.Text',
        	'c.surveyTitle',
        	'c.surveyPdf',
            'c.Link',
            'c.LinkTitle',
            'c.LinkDesc',
            'c.UserLinkDesc',
        	'c.LinkPic',
            'c.Pic',
            'c.PicDesc',
            'c.Vid',
            'c.VidDesc',
            'c.VidSite',
            'c.VidID',
            'c.PollText',
            'c.User',
            'c.PostDate',
            'LastActivity',
            'RssFeed',
            'u.UserID',
            'u.Name',
        	'u.Username',
            'u.Status',
            'u.ProfilePic',
            'c.TwitterTag',
            'c.GroupID',
        	'c.dburl'	
        ))->joinInner(array(
            'u' => $this->getTableName(USERS)
        ), 'u.UserID = c.User', array(
            'u.UserID',
            'u.Name'            
        ));
        
        $select->where("c.User = ?", $userid)->where('c.clientID = ?', clientID)->where("c.Active= ?", '1')->where("c.eventtype!= ?", '2')->order(array(
            'c' => 'LastActivity DESC'
        ))->limit(20, $Offset);   
         
        return $this->_db->fetchAll($select);
    }
    public function getdbeeidbyuser($users)    
    {
    	$curdate = $CurrDate=date('Y-m-d');    	
    	$select = $this->_db->select()
    		->from(array('db' => $this->getTableName(DBELEAGUE)),'db.DbeeID')
    			->distinct('db.DbeeID')    		
    			->joinInner(array('u' => $this->getTableName(LEAGUE)), 'db.LID = u.LID AND db.clientID = u.clientID','')
	    			->where("u.EndDate > ?",$curdate)
	    				->where("u.UserID =?",$users)->where("db.clientID =?",clientID);		
	    $result1 = $this->_db->fetchAll($select);  
	   
	    $dbid	=	array();
	    if(count($result1)>0){
	    	foreach ($result1 as $data):
	    		$dbid[] = $data['DbeeID'];
	    	endforeach;
	    	return $dbid;
	    	exit;
	    }
		return count($result1);
		exit;
    
    }
    
    public function chkdbeeexitleagueall($users,$calling='')    
    {
    	
    	if($calling=='hide') {
    		$dbeeid = $this->getdbeeidbyuser($users);
    		if($dbeeid>0)
    		{
    			$select = $this->_db->select()
	    			->from(array('c' => $this->getTableName(DBEE)));
	    			if($dbeeid>0) {
	    				$select->where('c.DbeeID IN (?)',$dbeeid);
	    			}
					    	$select->where("c.User =?",$users)
						    			->where("c.Active= ?", '1')->where("c.clientID= ?", clientID)
						                 	->order(array(
						                        'c' => 'LastActivity DESC'));    
		    			
    			$result = $this->_db->fetchAll($select);    		
    			return $result;
    		}
    		else { return 0; };
    	}
    	if($calling=='show') {
    		
    		$dbeeid = $this->getdbeeidbyuser($users);
    		$select = $this->_db->select();
	    	$select->from(array('c' => $this->getTableName(DBEE)));	
	    	
	    	if($dbeeid>0) {
	    			$select->where('c.DbeeID NOT IN (?)',$dbeeid);
	    	}
	    	$select->where("c.User =?",$users)
	    				->where("c.Active = ?", '1')->where("c.clientID= ?", clientID);
             $select->order(array(
                        'c' => 'LastActivity DESC'
                ));	    	
	    	$result = $this->_db->fetchAll($select);
	    
	   		return $result;
    	}
	   
    } 
  
    
    public function getdbeedetailin($dbeeid)
    {
    	$select = $this->_db->select()
    		->from($this->getTableName(DBEE))
    			->where("DbeeID IN(?)", $dbeeid);   
    	return $data = $this->_db->fetchAll($select)->where("clientID= ?", clientID);
    }
    public function getRelativePost($tag,$dbeeid)
    {
        $SQL = "SELECT * FROM ".$this->getTableName(DBEE)." AS db JOIN tblUsers AS U ON db.User = U.UserID WHERE eventtype!=2 AND ACTIVE = 1 AND DbeeID!='".$dbeeid."' AND U.clientID='".clientID."' AND FIND_IN_SET('$tag',DbTag) LIMIT 2";
        //echo $SQL;
        return $this->_db->query($SQL)->fetchAll();
    }
    
    public function getsearchHashTopdbee()
    {
        $select = $this->_db->select()->from(array(
            'c' => 'tblDbees',
            array(
                'c.DbTag'
            )
        ))->join(array(
            'u' => 'tblUsers'
        ), 'u.UserID = c.User', array(
            'u.UserID',
            'u.Name',
            'u.Username',
            'u.ProfilePic'
        ))->where('c.DbTag != ?', "")->where("c.Active= ?", '1')->where('c.clientID = ?', clientID)->limit('10');
        return $this->_db->fetchAll($select);
    }
     public function getsearchHashTopComment()
    {
        $select = $this->_db->select()->from(array(
            'c' => 'tblDbees',
            array(
                'c.DbeeID'
            )
        ))->join(array(
            'u' => 'tblUsers'
        ), 'u.UserID = c.User', array(
            'u.UserID'
        ))->join(array(
            'M' => 'tblDbeeComments'
        ), 'c.DbeeID = M.DbeeID', array(
            'M.DbTag','M.Active','M.DbeeID'
        ))->where('M.DbTag != ?', "")->where("M.Active= ?", '1')->where('M.clientID = ?', clientID)->limit('10');
        return $this->_db->fetchAll($select);
    }
    public function getadminemail()
    {
    	$select = $this->_db->select()
    		->from('tblUsers')
    			->where("role= ?", 1)->where('clientID = ?', clientID);
    	$data = $this->_db->fetchRow($select);
    	return $data['Email'];
    }
    
    public function getFolders($fId='',$folderorfile='') {    
    	$select = $this->_db->select()
    		->from('tblknowledge');
    	if($folderorfile=='foldernfiles' )
    		$select->where('kc_pid  =? ' , $fId)->where('status  =? ' , 0)->where('isdelete  =? ' , '0')->where('clientID = ?', clientID);
    	else if($folderorfile=='allfolder' )
    		$select->where('kc_pid  =? ' , 0)->where('status  =? ' , 0)->where('isdelete  =? ' , '0')->where('clientID = ?', clientID); 
        //echo $select->__toString();exit;
    		return $data = $this->_db->fetchAll($select);
    }
    
    public function getfolderscnt($fId='') {    
    	$select = $this->_db->select()
    	->from('tblknowledge','kc_pid')
    		->where('kc_pid  =? ' , $fId)->where('status  =? ' , 0)->where('isdelete  =? ' , '0')->where('clientID = ?', clientID);    	
    	return $data = count($this->_db->fetchAll($select));
    }
    
    public function getdir($filename) {
    	$select = $this->_db->select()
	    	->from('tblknowledge')
	    		->where('kc_file  =? ' , $filename)->where('isdelete  =?' , '0')->where('clientID = ?', clientID);
    	 $data = $this->_db->fetchRow($select);  
   
    	 return $this->getdir2($data['kc_pid']);
    }
     public function getdir2($pid) {
    	$select = $this->_db->select()
	    	->from('tblknowledge')
	    		->where('kc_id  =? ' , $pid)->where('isdelete  =?' , '0')->where('clientID = ?', clientID);
    	 $data = $this->_db->fetchRow($select);  
    	
    	 return $data['kc_cat_title'];
    }
    public function insertSocialFeed($data)
    {
        if ($this->_db->insert($this->getTableName(SocialFeed), $data))
            return true;
        else
            return false;
    }
    public function getSocialFeed($userid)
    {
        $select = $this->_db->select()
            ->from($this->getTableName(SocialFeed))
                ->where("user_id= ?", $userid)->where('clientID = ?', clientID);
        return $this->_db->fetchRow($select);
    }
    public function updateSocialFeed($data,$user_id)
    {
        if ($this->_db->update($this->getTableName(SocialFeed), $data ,array(
            "user_id='" . $user_id . "'","clientID='".clientID."'"
        )))
            return true;
        else
            return false;
    }
    public function surveyQuestions($dbeeid)
    {
        $select = $this->_db->select()
            ->from("tblSurveyquestion")
                ->where("Dbeeid= ?", $dbeeid)->where('parentID= ?',0)->where('clientID= ?',clientID);
                $select->order('id ASC');
        return $this->_db->fetchAll($select);
    }
    public function surveyAnswers($parentID)
    {
        $select = $this->_db->select()
            ->from("tblSurveyquestion")
            ->where('parentID= ?',$parentID)->where('clientID= ?',clientID);
            $select->order('id ASC');
        return $this->_db->fetchAll($select);
    }
    public function surveyCheckStatus($dbeeid,$userid)
    {
        $select = $this->_db->select()
            ->from("tblSurveyAnswer")
            ->where('dbeeid= ?',$dbeeid)
            ->where('clientID= ?',clientID)->where('clientID= ?',clientID)
            ->where('UserID= ?',$userid);
        return $this->_db->fetchRow($select);
    }
    
    public function insertServeyAnswer($data)
    {
        if ($this->_db->insert('tblSurveyAnswer', $data))
            return $this->_db->lastInsertId();
        else
            return false;
    }
    public function videoEventAttendies()
    {
        $select = $this->_db->select()->from(array(
            'p' => $this->getTableName(DbeeJoinedUser)
        ), array())->joinInner(array(
            'u' => $this->getTableName(USERS)
        ), 'u.UserID = p.userID', array(
            'u.UserID',
            'u.Name',
            'p.dbeeid'
        ))->joinInner(array(
            'c' =>  $this->_name
        ), 'c.DbeeID = p.dbeeid', array(
            'c.DbeeID',
            'c.Active',
            'c.Type',
            'c.Vid',
            'c.VidDesc',
            'u.UserID',
            'u.Name',
            'u.Email',
            'u.Username',
            'u.Status',
            'u.ProfilePic',
            'c.dburl',
        ));


        $add_time=strtotime(date('Y-m-d H:i:s')) + 60*60;
        $eventstart= date('Y-m-d H:i:s',$add_time);
        $lastTime = date('Y-m-d H:i:s');
        $select->where("c.Active= ?", '1')->where("c.notification= ?", 0)
                ->where ("c.eventstart between '".$lastTime."' and '".$eventstart."'")
                ->where("c.Type = ?", '6')->where('c.clientID = ?', clientID);

        return $this->_db->fetchAll($select);
    }
    public function updateVedioEvent($data,$dbeeid)
    {
        if ($this->_db->update($this->_name, $data ,array(
            "DbeeID='" . $dbeeid . "'","clientID='".clientID."'"
        )))
            return true;
        else
            return false;
    }
    public function broadcastevent($limit=1)
    {
        $select = $this->_db->select()->distinct('c.DbeeID')->from(array(
            'c' => $this->_name
        ), array(
            'c.VidDesc',
            'c.dburl',
        ));
        $eventstart = date('Y-m-d H:i:s');
        $select->where("c.Active= ?", '1')
                ->where("c.eventstart > ?", $eventstart)
                ->where("c.eventtype != ?", '2')->where("c.Type = ?", '6')->where('c.clientID = ?', clientID);  

        $select->order(array(
            'c' => 'eventstart ASC'
        ))->limit($limit);  
        return $this->_db->fetchAll($select);
    }
    public function insertUserFeedBack($data)
    {        
        $this->_db->insert($this->getTableName(feedback),$data);
        return $this->_db->lastInsertId();
    }
    public function getpdftitle($filename)
    {
    	$select = $this->_db->select()
    	->from("tblknowledge")
    		->where('kc_file= ?',$filename)->where('clientID = ?', clientID);    			
    	$row = $this->_db->fetchRow($select);       	
    	return $row['kc_cat_title'];
    	
    }
    public function getaadbtypebyuser($user)
    {
    	$select = $this->_db->select()->from(array(
            'c' => $this->_name
        ), array(           
            'c.Text',
        	'c.Link',
        	'c.Pic',
        	'c.Vid',
        	'c.PollText'        	
        	))
    	->where('c.User = ?',$user)->where('c.clientID = ?', clientID);
    	$row = $this->_db->fetchAll($select);    	
    	return $row;
    
    }
     public function checkdbtodashbord($dbid,$userid,$type)
    {
    	$select = $this->_db->select()
    	->from($this->getTableName(DASHBORDDB),array('ID'))
	    	->where("UserID = ?", $userid)
		    	->where("dbeeID = ?", $dbid)
			    	->where("actiontype = ?", $type)
			    		->where('clientID = ?', clientID);    	
    	$data = $this->_db->fetchAll($select);
    	if(count($data)>0)
    		return false;
    	else
    		return true;
    }
    
    public function deletedashdb($userid,$dbid,$type)
    {
    	$this->_db->delete($this->getTableName(DASHBORDDB), array('UserID= ?' => $userid ,'dbeeID= ?'=> $dbid, 'actiontype= ?'=>$type, 'clientID = ?'=>clientID));
    	return true;
    }
    public function getrestrictedurl($url)
    {
    	$urls = '';
    	$url =$this->__removehttp($url);
    	if(strpos($url, 'www.') === 0) {
    		 $url =  str_replace('www.', '', $url); 
    	}   		
    		 $pos = strpos($url, '/');
    		 if($pos!=''){
    				 $urls = substr($url,0,$pos);
    		 }
    	
    	if($urls=='')$urls=$url;	
    	$select = $this->_db->select()
	    	->from($this->getTableName(RESTRICTURL),array('id','linkurl'))
		    	->where('linkurl LIKE ?', "%".$urls."%")
		    		->where('clientID = ?', clientID);  
    	
    	$data = $this->_db->fetchAll($select);
    	if(count($data)>0)
    		return true;
    	else
    		return false;
    }

     public function getrestrictedurllist()
    {
        $select = $this->_db->select()
            ->from($this->getTableName(RESTRICTURL),array('linkurl'))
                    ->where('clientID = ?', clientID);     
        return $this->_db->fetchAll($select);
    }

	function __removehttp($url) {
	   $disallowed = array('http://', 'https://');
	   foreach($disallowed as $d) {
	      if(strpos($url, $d) === 0) {
	         return str_replace($d, '', $url);
	      }
	   }
	   return $url;
	}
    public function gethiddendbee($userid)    
    {    
    	$select = $this->_db->select()    
	    	->from($this->getTableName(DASHBORDDB),	array('dbeeID'))    
		    	->where("UserID = ?",$userid)
			    	->where("clientID = ?", clientID)
			    		->where("actiontype = ?", '2');    
    	$result = $this->_db->fetchAll($select);      	
    	return $result;    
    }
    
    public function getisdashboard($userid,$dbid)
    {
    	$select = $this->_db->select()
	    	->from($this->getTableName(DASHBORDDB),array('ID'))
		    	->where('UserID = ?', $userid)
			    	->where('dbeeID = ?', $dbid)
			    		->where('clientID = ?', clientID);    	
    	$data = $this->_db->fetchAll($select);
    	if(count($data)>0)
    		return 1;
    	else
    		return 0;
    }


    public function eventlist(){

        $select = $this->_db->select()      
        ->from('tblEvent',array("id","title"));
        $select->where('clientID = ?',clientID);    
        $select->where('end >= ?', new Zend_Db_Expr('NOW()'));
        $select->where('status = ?',1);         
        $select->order('id DESC');
        return $this->_db->fetchAll($select);
                
    }
    
    public function searchcomusers($param,$usertype='')
    {
    	$this->myclientdetails = new Application_Model_Clientdetails();
    	$defaultimagecheck = new Application_Model_Commonfunctionality();
    	
    	$param = $this->myclientdetails->customEncoding($param,$search="true");
    	$db = $this->_db;
    	$SQL = "SELECT * FROM tblUsers WHERE Name REGEXP '^$param' AND clientID='".clientID."'";
        if($usertype!=100)
         {           
           $SQL.=" AND usertype!='100'";
         } 
         if(isADMIN!=1) 
         {           
           $SQL.=" AND hideuser!='1'";
         } 
         
    	$query = $this->_db->fetchAll($SQL);
    	for ($x = 0, $numrows = count($query); $x < $numrows; $x++)
    	{
    	
    	$userpic = $defaultimagecheck->checkImgExist($query[$x]["ProfilePic"],'userpics','default-avatar.jpg');
    			$friends[$x] = array(
    			"id" => $query[$x]["UserID"],"name" => $this->myclientdetails->customDecoding($query[$x]["Name"]),
    			"email" => $this->myclientdetails->customDecoding($query[$x]["Email"]),
    			"url" =>  IMGPATH.'/users/small/'.$userpic.'"'
		  	);
    	}
    	$response = json_encode($friends);
    	return $response;
    }
    
    public function insertdatakc($data){    	
    	$allrec =	$this->_db->insert('tblknowledge',$data);
    	return $this->_db->lastInsertId();
    
    }
    
    public function insertfileuser($data){    	 
    	
    	$allrec =	$this->_db->insert('tblUserPdf',$data);
    	return $allrec;
    }
    public function getfollowUsersdet(){
    	$select = $this->_db->select()->distinct()
    	->from('tblFollows',array("User","FollowedBy"));
    	$select->where('clientID = ?',clientID);
    	//echo $select->__toString();exit;
    	//$select->order('id DESC');
    	return $this->_db->fetchAll($select);
    
    }
    public function updateshowtagmsg($data, $userid)
    { 
        	return $this->_db->update($this->getTableName(USERS), $data, array(
    			"UserID='" . $userid . "'","clientID='".clientID."'"
    	));
    }


    public function updatefiledel($data,$fileid)
    { 
         
       $this->_db->update('tblknowledge',$data, array("kc_id='" . $fileid . "'"));  

       return true;
    }

    public function polldata($start,$end,$userid)
    {
            //require_once 'includes/globalfile.php'; 
            $Offset = (int)$start;
            $select = $this->_db->select()->from(array('c' => 'viewposts' ))->joinLeft(array('p' => 'tblPollVotes'),
                                            'p.PollID = c.DbeeID', array('p.User','p.Vote','p.VoteDate'));
                                                if(!empty($blockuser))
                                                    $select->where("c.GroupID NOT IN ?", $sub_select);
                                                if(!empty($PrivateGroups))
                                                $select->where("c.User NOT IN ?", $sub_select);                 
                                                $select->where('p.User='.$userid);                                               
                                                $select->where("c.Active= ?",'1');
                                                $select->where("c.clientID = ?", clientID);
                                                        $select->group('c.DbeeID')
                                                                ->order('c.LastActivity DESC')
                                                                    ->limit(PAGE_NUM, $Offset);

                                                                    //echo $select->__toString();
            return $this->_db->fetchAll($select);
    }

    
    public function chkallrss()
    {

        $select = $this->_db->select()
            ->from('tblRssSites')
                ->where('clientID = ?',clientID);
       
        $rsslist = $this->_db->fetchAll($select);
        if(count($rsslist)>0)
            return 1;
        else
        return 0;
    }
    
    public function adminsetinggrpenable()
    {
    	$select = $this->_db->select()->from('tblAdminSettings', array('GroupEmail'))->where('clientID = ?', clientID);
    	$result = $this->_db->fetchRow($select);
    	return $result['GroupEmail'];
    }
    
   
      
}
