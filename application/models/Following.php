<?php
class Application_Model_Following extends Application_Model_DbTable_Master
{
    
    protected $_name = null;
    
    protected function _setupTableName()
    {
        
        parent::_setupTableName();
        
        $this->_name = $this->getTableName(FOLLOWS);
        
    }
    
    public function index($start, $end, $userid)
    {
        require_once 'includes/globalfile.php';   
       // $privategroup_obj = new Application_Model_Privateuser();
        //$PrivateGroups = $privategroup_obj->getprivategroup2($userid); 
       // $PrivateGroups    = $privategroup_obj->getprivategroup();
        $myhome = new Application_Model_Myhome();
        $gethiddenDbs  =    $myhome->gethiddendbee($userid);
        $users            = $this->getfolloweruserbyid($userid);
        if (!empty($users))
            $user = array_unique($users);
        if ($user != '') {
            $Offset = (int) $start;
            $select = $this->_db->select()->from(array(
                'c' => $this->getTableName(DBEE)
            ), $dbtablefields)->joinLeft(array(
                'u' => $this->getTableName(USERS)
            ), 'u.UserID = c.User AND u.clientID = c.clientID');
            /* if(!empty($PrivateGroups)){
            $select->where("c.GroupID NOT IN(?)", $PrivateGroups);
            } */
           if (count($gethiddenDbs) > 0) {
             $select->where("c.DbeeID NOT IN(?)", $gethiddenDbs);
            }
            $select->where("c.clientID = ?", clientID);
            $select->where('c.User IN(?)', $users)->where("c.Active= ?", '1')->where("c.Privategroup= ?", '0')->where("c.eventtype!= ?", '2')->order('c.LastActivity DESC')->limit(PAGE_NUM, $Offset);
            $result = $this->_db->fetchAll($select);
            return $result;
        }
    }

    public function customDashboardUsers($start, $end, $userid,$userINids)
    {
        //require_once 'includes/globalfile.php';   
    
        $myhome = new Application_Model_Myhome();
        $gethiddenDbs  =    $myhome->gethiddendbee($userid);
       
        if ($userINids != '') {
            $Offset = (int) $start;
            $select           = $this->_db->select()->from(array('c' => 'viewposts' ));
            $select->where("c.clientID = ?", clientID);
            /* if(!empty($PrivateGroups)){
            $select->where("c.GroupID NOT IN(?)", $PrivateGroups);
            } */
           if (count($gethiddenDbs) > 0) {
             $select->where("c.DbeeID NOT IN(?)", $gethiddenDbs);
            }
            $select->where("c.clientID = ?", clientID);
            $select->where('c.User IN(?)', $userINids)->where("c.Active= ?", '1')->where("c.UserID!= ?", '')->where("c.Privategroup= ?", '0')->where("c.eventtype!= ?", '2')->order('c.LastActivity DESC')->limit(PAGE_NUM, $Offset);
            $result = $this->_db->fetchAll($select);
            return $result;
        }
    }
    
    public function getfolloweruser($userid)
    {
        
        $select = $this->_db->select();
        
        $select->from(array(
            'c' => $this->_name
        ))->join(array(
            'u' => $this->getTableName(USERS)
        ), 'u.UserID = c.User');
        
        $select->where('c.User =.' . $userid);
        $select->where("c.clientID = ?", clientID);
        $select->order(array(
            'c.User asc'
        ));
        
        $result = $this->_db->fetchAll($select);
        
        return $result;
        
        
        
        
        
    }
    
    public function getfollinguserserch($id)
    {
        
        $select = $this->_db->select();
        
        $select->from($this->_name, array(
            'User'
        ));
        
        $select->where('FollowedBy =' . $id);
        $select->where("clientID = ?", clientID);
        $result = $this->_db->fetchAll($select);
        
        foreach ($result as $data):
            $useid[] = $data['User'];
        endforeach;
        
        return $useid;
        
    }    
    
    
    public function getfolloweruserserch($id)
    {
        
        $select = $this->_db->select();
        
        $select->distinct();
        
        $select->from($this->_name, array(
            'User'
        ))->join(array(
            'u' => $this->getTableName(USERS)
        ), 'u.UserID = User')->where('FollowedBy = ?', $id);
        $select->where("u.clientID = ?", clientID);
        $result = $this->_db->fetchAll($select);
        
        return $result;
        
    }
    
    
    
    public function getfolloweruserprofile($id,$limit='',$offset=0,$sort='',$keyword='',$usertype='')
    {

        $select = $this->_db->select();
        $select->distinct();
        $select->from($this->_name, array(
            'User'
        ))->join(array(
            'u' => $this->getTableName(USERS)
        ), 'u.UserID = User', array(
            'id' => 'UserID',
            'username' => 'Username',
            'name' => 'Name',
            'lname' => 'lname',
            'full_name'=> 'full_name',
            'Email' => 'Email',
            'title' => 'title',
            'company' => 'company',
            'avatar' => 'ProfilePic',
            'type' => 'Status',
            'typename'=>'typename'
        ))->where('FollowedBy = ?', $id);
        $select->where("u.clientID = ?", clientID);
        if($keyword!="")
        {
          $select->where('Name REGEXP ?', '^'.$keyword);
        }
        if($usertype!=100)
        {
          $select->where('usertype != ?', 100); 
        }
        if(isADMIN!=1 && isADMIN!=1) 
        {
          $select->where("hideuser != ?", 1);
        } 
        

        if($sort!="")
        {              
               $slice=explode('-',$sort);
              if($slice[0]=='RegistrationDate')
              {

               $select->order('u.'.$slice[0].' '.$slice[1].'');
              }
        } 

        if($limit) $select->limit($limit,$offset);
        //echo $select->__toString();    die; 
        $result = $this->_db->fetchAll($select);        
        return $result;        
    }

    public function GetContactUserList($uid,$limit='',$offset=0,$sort='',$keyword='')
    {
        $select = $this->_db->select();
        $select->distinct(); 
        $select->from(array(
            'c' => 'tblUserContact'
        ), array(
            'UserId'
        ))->join(array(
            'u' => $this->getTableName(USERS)
        ), 'u.UserID = c.UserId', array(
            'id' => 'UserID',
            'username' => 'Username',
            'name' => 'Name',
            'lname' => 'lname',
            'title' => 'title',
            'company' => 'company',
            'Email' => 'Email',
            'typename' => 'typename',
            'Emailmakeprivate' => 'Emailmakeprivate',
            'avatar' => 'ProfilePic',
            'Status' => 'Status'
        ))->where('added_by = ?', $uid);
        $select->where("u.clientID = ?", clientID);
        if($keyword!="")
        {
          $select->where('Name REGEXP ?', '^'.$keyword);
        }
        if($sort!="")
        {              
               $slice=explode('-',$sort);
              if($slice[0]=='RegistrationDate')
              {

               $select->order('u.'.$slice[0].' '.$slice[1].'');
              }
        } 
        if($limit) $select->limit($limit,$offset);
       //echo $select->__toString();    die; 
        $result = $this->_db->fetchAll($select);        
        return $result;
        
    }
    
    public function getfolloweruserprofilelimit($id)
    {
        
        $select = $this->_db->select();
        
        $select->distinct();
        
        $select->from($this->_name, array(
            'User'
        ))->join(array(
            'u' => $this->getTableName(USERS)
        ), 'u.UserID = User')->where('FollowedBy = ?', $id);
        $select->where("u.clientID = ?", clientID);
        $select->limit(19);
        
        $result = $this->_db->fetchAll($select);
        
        return $result;
        
    }
    
    public function getallfollowing()
    {
        
        $select = $this->_db->select()->from(array(
            $this->_name
        ));
        $select->where("clientID = ?", clientID);
        $result = $this->_db->fetchAll($select);
        
        return $result;
        
    }
    
   public function getfollowing($userid,$limit='',$offset=0,$sort='',$keyword='',$usertype='')
    {
        $select = $this->_db->select();
         $select->from(array(
            'f' => $this->_name
        ))->join(array(
            'u' => $this->getTableName(USERS)
        ), 'u.UserID = f.FollowedBy', array(
            'id' => 'UserID',
            'username' => 'Username',
            'name' => 'Name',
            'lname' => 'lname',
            'full_name'=> 'full_name',
            'Email' => 'Email',
            'title' => 'title',
            'company' => 'company',
            'avatar' => 'ProfilePic',
            'type' => 'Status',
            'typename'=>'typename'
        ))->where("f.User = ?", $userid)->where("f.clientID = ?", clientID);
        if($keyword!="")
        {
          $select->where('Name REGEXP ?', '^'.$keyword);
        }
        if($usertype!=100 && isADMIN!=1)
        {
          $select->where('usertype != ?', 100); 
        }
        if(isADMIN!=1) 
        {
          $select->where("hideuser != ?", 1);
        } 
        if($sort!="")
        {              
               $slice=explode('-',$sort);
              if($slice[0]=='RegistrationDate')
              {

               $select->order('u.'.$slice[0].' '.$slice[1].'');
              }
        } 
        if($limit) $select->limit($limit,$offset);
       //echo $select->__toString();
       //die;
        $result = $this->_db->fetchAll($select);
        
        return $result;
        
    }
    
    public function getfollowinglimit($userid)
    {
        
        $select = $this->_db->select()->from(array(
            'f' => $this->_name
        ))->join(array(
            'u' => $this->getTableName(USERS)
        ), 'u.UserID = f.FollowedBy')->where("f.clientID = ?", clientID)->where("f.User =?", $userid)->limit(19);
        
        $result = $this->_db->fetchAll($select);
        
        return $result;
        
    }
    
    public function chkfollowing($dbuser, $useid)
    {
        $select = $this->_db->select()->from($this->_name)->where("clientID = ?", clientID)->where("User = ?", $dbuser)->where("FollowedBy =?", $useid);
        return $this->_db->fetchRow($select);
    }

    public function chkChatUsers($sendto,$sendby)
    {
       $select = $this->_db->select()->from('tblchatusers')->where("clientID = ?", clientID)
       ->where("sendto=$sendto and sendby=$sendby")
       ->orWhere("sendby=$sendto and sendto=$sendby");
       //echo $select->__toString(); exit;
       $checkChatUser = $this->_db->fetchRow($select); 
       if(empty($checkChatUser)){
           $data = array('clientID'=> clientID,'sendto'=> $sendto,'sendby'=> $sendby,'status'=> 0,'dateofchat'=>date('Y-m-d H:i:s'));
           $this->_db->insert('tblchatusers', $data);
           return 0;
       }else{
           //echo'<pre>';print_r($checkChatUser);echo $sendto.'~~~'.$sendby;die;
           if($checkChatUser['sendby']==$sendto && $checkChatUser['sendto']==$sendby){
               $data = array('status'=> 1);
               $this->_db->update('tblchatusers', $data, array("sendby='".$sendto."'" , "sendto='".$sendby."'"));
               return 1;
           }
           if($checkChatUser['sendto']==$sendto && $checkChatUser['sendby']==$sendby){
               $data = array('status'=> 1);
               $this->_db->update('tblchatusers', $data, array("sendby='".$sendby."'" , "sendto='".$sendto."'"));
               return 1;
           }
       } 
    }

    public function chkChatUFollowUsers($to,$by)
    {
       $select = $this->_db->select()->from('tblchatusers')->where("clientID = ?", clientID)
       ->where("sendto=$by and sendby=$to")
       ->orWhere("sendby=$by and sendto=$to");
       //echo $select->__toString(); exit;
       $checkChatUser = $this->_db->fetchRow($select);
       if(($checkChatUser['status']==1 && $checkChatUser['sendto']==$by)){
             $data = array('status'=> 0);
             $this->_db->update('tblchatusers', $data, array("sendto='".$by."'" , "sendby='".$to."'"));

             return 0;
       }
       if(($checkChatUser['status']==1 && $checkChatUser['sendby']==$by)){
             $data = array('status'=> 0);
             $this->_db->update('tblchatusers', $data, array("sendto='".$to."'" , "sendby='".$by."'"));

             return 0;
       }
       if(($checkChatUser['status']==0)){
             $this->_db->delete('tblchatusers', array("sendby='".$checkChatUser['sendby']."'" , "sendto='".$checkChatUser['sendto']."'"));
       }
    }    

    public function chkcontact($sessionid, $useid)
    {
        $select = $this->_db->select()->from('tblUserContact')->where("clientID = ?", clientID)->where("added_by = ?", $sessionid)->where("UserId =?", $useid);
        return $this->_db->fetchRow($select);
    }
    
    
    
    public function chkfollowingcnt($dbuser, $useid)
    {
        
        $select = $this->_db->select()->from($this->_name)->where("clientID = ?", clientID)->where("User = ?", $dbuser)->where("FollowedBy =?", $useid);
        
        $result = $this->_db->fetchRow($select);
        
        return $result;
        
    }

    public function CheckContactCount($sessionid, $useid)
    {
        
        $select = $this->_db->select()->from('tblUserContact')->where("clientID = ?", clientID)->where("added_by = ?", $sessionid)->where("UserId =?", $useid);
        
        $result = $this->_db->fetchRow($select);
        
        return $result;
        
    }
    
    public function getcontact($sessionid)
    {    
    	$select = $this->_db->select()->from('tblUserContact')->where("clientID = ?", clientID)->where("added_by = ?", $sessionid);    
    	$result = $this->_db->fetchAll($select);    	
    	foreach ($result as $data):
    	$useid[] = $data['UserId'];
    	endforeach;
    	
    	return $useid;    
    }
    
    public function deletefollowing($id)
    {
        if ($this->_db->delete($this->_name, array(
            "ID='" . $id . "'",
            "clientID='" . clientID . "'"
        )))
            return true;
        else
            return false;
    }

    public function deleteFromContact($sessionid,$userid)
    {
        if ($this->_db->delete('tblUserContact', array(
            "added_by='" . $sessionid . "'",
            "UserId='" . $userid . "'",
            "clientID='" . clientID . "'"
        )))
            return true;
        else
            return false;
    }


     public function InsertInContactList($data)
    {        
        if ($this->_db->insert('tblUserContact', $data)) {           
            
            
            return 1;
            
        } else { 
            return 0;
            
        } 
    
    }



    
    public function insertfollowing($data)
    {        
        if ($this->_db->insert($this->_name, $data)) {           
            
            
            return 1;
            
        } else { 
            return 0;
            
        } 
    
    }

    
    public function getfollowerusernotify($userid)
    {
        
        $select = $this->_db->select()->from($this->getTableName(FOLLOWS))->where("clientID = ?", clientID)->where('FollowedBy =' . $userid);
        
        
        $result = $this->_db->fetchAll($select);
        
        return $result;
        
    }
    
    public function getGroupsnotify($userid)
    {
        
        $select = $this->_db->select()->from($this->getTableName(GROUP))->where("clientID = ?", clientID)->where('GroupPrivacy =2');
        
        $result = $this->_db->fetchAll($select);
        
        return $result;
        
    }
    
    
    
    
    
    public function getfollingauto($user)
    {
        
        $select = $this->_db->select();
        
        $select->from($this->_name);
        
        $select->where('User = ?', $user)->where("clientID = ?", clientID);
        
        $result = $this->_db->fetchAll($select);
        
        return $result;
        
    }
    
    public function getfollingautoid($user)
    {
    
    	$select = $this->_db->select();
    	 
        $select->from($this->_name, array(
            'FollowedBy','User'
        ));    
    	$select->where('User = ?', $user)->where("clientID = ?", clientID);    	

    	$result = $this->_db->fetchAll($select);    
    	
    	foreach ($result as $data):
            $useid[] = $data['FollowedBy'];
        endforeach;        
        
        return $useid;
    
    }
    
    
    
    public function getfolloweruserbyid($id,$usertype='')
    {
        
        $select = $this->_db->select();
        
        $select->distinct();
        
        $select->from($this->_name, array(
            'User'
        ))->join(array(
            'u' => $this->getTableName(USERS)
        ), 'u.UserID = User', array(
            'id' => 'UserID'
        ))->where('FollowedBy = ?', $id)->where('u.clientID = ?', clientID);
        
       // $select->where('FollowedBy =' . $id)->where('clientID = ?', clientID);

        if($usertype!=100 && isADMIN!=1)
        {
          $select->where('u.usertype != ?', 100); 
        }
        if(isADMIN!=1) 
        {
          $select->where("u.hideuser != ?", 1);
        } 
         //echo $select->__toString(); exit;
        $result = $this->_db->fetchAll($select);
        
        foreach ($result as $data):
            $useid[] = $data['User'];
        endforeach;
        
        return $useid;
        
    }
    
    
    
    public function getfollowinguserleageu()
    {
        
        $sql = "SELECT User,Username,COUNT(ID) AS Total FROM tblFollows,tblUsers WHERE tblFollows.User=tblUsers.UserID GROUP BY User ORDER BY Total DESC,User";
        
        $result = $this->_db->query($sql)->fetchAll();
        
        return $result;
        
        
        
    }
    
    
    
    public function getfollowinguserleageulimit($limitstart)
    {
        $sql = "SELECT User,ProfilePic,Name,Username,COUNT(ID) AS Total FROM tblFollows,tblUsers WHERE tblFollows.User=tblUsers.UserID GROUP BY User ORDER BY Total DESC,User limit " . $limitstart . ",5";
        
        $result = $this->_db->query($sql)->fetchAll();
        
        return $result;
    }
    
    public function getrowuserinfo($userid)
    {
        
        $select = $this->_db->select()->from($this->getTableName(USERS))->where('UserID = ?', $userid);
        $result = $this->_db->fetchRow($select);
        
        return $result;
        
    }
    
    public function follingdbnotifys($users, $PostDate)
    {
        
        $select = $this->_db->select()->from(array(
            'c' => $this->getTableName(DBEE)
        ), array(
            'c.DbeeID',
            'c.Type'
        ));
        if (!empty($users)) {
            $select->where('c.User IN(?)', $users);
        }
        $select->where("c.Privategroup= ?", '0')->where("c.LastActivity > ?", $PostDate)->where("c.Active= ?", '1');
        return count($this->_db->fetchAll($select));     
        
    }
    
    
}