<?php

class Application_Model_Message extends Application_Model_DbTable_Master
{
	protected $_name = null;

	protected function _setupTableName()
	{
		parent::_setupTableName();
		$this->_name = $this->getTableName(MESSAGES);
	}

	public function fetchAll()
	{
		$select = $this->_db->select()
		->from(array($this->_name),	array('MessageTo','MessageFrom','Message','MessageDate'))->where('clientID = ?', clientID);
		/*echo $select->__toString();
	exit;*/

		$result = $this->_db->fetchAll($select);
		return $result;
	}

	public function dbeemassagesdate($userid,$start)
	{
		$sql = "SELECT MessageDate AS MsgDate FROM tblMessages WHERE clientID=".clientID." AND
		(MessageTo=".$userid." OR MessageFrom=".$userid.")
		AND NOT FIND_IN_SET(".$userid.", ArchivedBy) order by MsgDate Desc";
		$results = $this->_db->query($sql)->fetchAll();
		return $results;
	}
	public function dbeemassagesdatesearch($userid,$start)
	{
		$sql = "SELECT MAX(MessageDate) AS MsgDate FROM tblMessages WHERE clientID=".clientID." AND
		(MessageTo=".$userid." OR MessageFrom=".$userid.")
		AND NOT FIND_IN_SET(".$userid.", ArchivedBy) order by MsgDate DESC";
		$results = $this->_db->query($sql)->fetchAll();
		return $results;
	}
	public function dbeemassagesdate2($userid,$start,$shortid)
	{
		$sql = "SELECT MAX(MessageDate) AS MsgDate FROM tblMessages
		WHERE clientID=".clientID." AND ((MessageTo=".$userid."
		AND MessageFrom=".$shortid.") OR (MessageTo=".$shortid."
		AND MessageFrom=".$userid.")) AND NOT FIND_IN_SET(".$userid.", ArchivedBy) GROUP BY ChainParent";
		 
		$results = $this->_db->query($sql)->fetchAll();
		return $results;
	}
	public function getallmsguser($userid,$start)
	{
		$myclientdetails = new Application_Model_Clientdetails();
		$common_obj =  new Application_Model_Commonfunctionality();
		$sql ="SELECT MessageFrom AS USERID FROM `tblMessages` 
		 where MessageTo = $userid AND clientID=".clientID." GROUP BY MessageFrom
		UNION SELECT MessageTo AS USERID FROM `tblMessages` where MessageFrom = $userid AND clientID=".clientID." GROUP BY MessageTo"; 
		$results = $this->_db->query($sql)->fetchAll();
		$defaultimagecheck = new Application_Model_Commonfunctionality();
	
		$userList = array();
		$userCom = array();
		$userSub =array();
		$userli = '';
		$userli .='<li><a onclick="javascript:newmessagefeed(0);" href="javascript:void(0);">All</a></li>';
		$i=1;
		foreach($results as $user):
		//print_r($user);
			
			$ChkUser=$user['USERID'];
			$ChkUsername = $this->getuser($user['USERID']);
			
			$userli .='<li><a onclick="javascript:newmessagefeed(0,'.$ChkUser.');" href="javascript:void(0);">'.
			$myclientdetails->customDecoding($ChkUsername['Name']).'</a></li>';
			$userList['text'] = str_replace("'", '', $myclientdetails->customDecoding($ChkUsername['Name']));
			if($userList['text']!=false){
			$userList['id'] = $user['USERID'];
			$userList['profilepic'] =  $defaultimagecheck->checkImgExist($ChkUsername['ProfilePic'],'userpics','default-avatar.jpg');
			$userSub[] = $userList;
			}
			
			if(!empty($ChkUsername['company'])){
			
				$id = 'id';
				$text = 'text';
				$usercompany['id'] = $myclientdetails->customDecoding($ChkUsername['company']);	
				$usercompany['text'] = $myclientdetails->customDecoding($ChkUsername['company']);
				$userCom[] = $usercompany;				
			}
			$i++;
		endforeach;
		
		$userData['content']= $userli;
		$userData['list']= json_encode($userSub);
		$userData['company']= json_encode(array_filter($userCom));
		
		
		//exit;
		return json_encode($userData);
	}
	public function getallmsguser1($userid,$start,$user1='')
	{
		
		$myclientdetails = new Application_Model_Clientdetails();
		$common_obj =  new Application_Model_Commonfunctionality();
		/* $sql = "SELECT * FROM tblMessages WHERE clientID=".clientID." AND (MessageTo=".$userid." OR MessageFrom=".$userid.") AND NOT FIND_IN_SET(".$userid.", ArchivedBy) GROUP BY ChainParent"; */
		$sql ="SELECT MessageFrom AS USERID FROM `tblMessages` where MessageTo = $userid AND clientID=".clientID." GROUP BY MessageFrom
		UNION SELECT MessageTo AS USERID FROM `tblMessages` where MessageFrom = $userid AND clientID=".clientID." GROUP BY MessageTo"; 
		$results1 = $this->_db->query($sql)->fetchAll();
	
		$userid = array();
		foreach($results1 as $user):
			$userid[]=$user['USERID'];
		endforeach;
		
		$userli = '';
		
		$select = $this->_db->select()
		->from($this->getTableName(USERS))
		->where('UserID IN(?)',$userid);
		$select->where("Name LIKE '%$user1%' OR Username LIKE '%$user1%'");
		$select->where('UserID IN(?)',$userid);
		$select->group('UserID');		
		$result = $this->_db->fetchAll($select);
		if(count($result)>0){
		foreach($result as $user):			
			$userli .='<li>'.$myclientdetails->customDecoding($user['Name']).'</li>';
		
		//}
		endforeach;
		}
		return $userli;
	}
	public function dbeemassages($msgdate,$userid,$start,$adminid='',$company='')
	{
		
		/*if($msgdate){
			$Offset = (int)$start;
			$select = $this->_db->select()
			->from(array($this->_name),array('ID','MessageTo','MessageFrom','Message','Unread','MsgDate'=>'MessageDate','ChainParent','Fromadmin'))
			->join(array('u' => $this->getTableName(USERS)), 'u.UserID = MessageFrom',array('Name','lname','UserID','Status','usertype','ProfilePic','company'));
			//$select->where('MessageFrom = '.$userid.' OR MessageTo = '.$userid)
			$select->where('NOT FIND_IN_SET(ArchivedBy,(?))', $userid)
			->where('MessageDate IN(?)', $msgdate)->where('u.clientID = ?', clientID);
			if($company !=''){
			$select->where('u.company = ?',$company);
			}
			$select->group('ChainParent')
			->order('MessageDate DESC')
			->limit('10', $Offset); 
			//echo $select->__toString();
			$result =$this->_db->fetchAll($select);
			return $result;
		}*/
		$companyCond = '';
		if($company !=''){
			$companyCond =  " AND `u.company` = '".$company."'";
		}		
		$Offset = " OFFSET ".(int)$start;
		$chainGroup = "select m.ID,m.MessageTo,m.MessageFrom,m.Message,m.Unread,m.ChainParent,m.Fromadmin,m.MessageDate as MsgDate, u.Name, u.lname, u.UserID, u.Status, u.usertype, u.ProfilePic, u.company from 
				(select * from tblMessages ORDER BY MessageDate DESC) AS m INNER JOIN tblUsers AS u ON u.UserID = MessageFrom  
		WHERE (MessageFrom = ".$userid." OR MessageTo = ".$userid.") 
		AND (NOT FIND_IN_SET('".$userid."',ArchivedBy)) 
		AND (u.clientID = '".clientID."') ".$companyCond."
		GROUP BY ChainParent 
		ORDER BY `MessageDate` DESC LIMIT 10 ".$Offset; 

		$results = $this->_db->query($chainGroup)->fetchAll();
		return $results;
		
	}
	public function dbeemassagesnotify($msgdate,$userid,$start,$adminid='',$company='')
	{

	   $sql = "select c.act_id,u.UserID,u.Name,u.lname,u.Username,u.usertype,u.ProfilePic,m.ID as msgid,m.MessageTo,m.MessageFrom,m.Message,m.MessageDate,m.ChainParent,m.Fromadmin from tblactivity c left join tblUsers u on c.act_userId  = u.UserID  INNER JOIN tblMessages m on c.act_typeId  = m.ID WHERE c.act_ownerid  =" .$userid ." AND c.act_userId  !=" .$userid ." order by c.act_date Desc LIMIT 10";

	/* and c.act_ownerid  =" .$user ." and c.act_userId  !=" .$user ."

	 select m.ID,m.MessageTo,m.MessageFrom,m.Message,m.Unread,m.ChainParent,m.Fromadmin,m.MessageDate as MsgDate, u.Name, u.lname, u.UserID, u.Status, u.usertype, u.ProfilePic, u.company from 
				(select * from tblMessages ORDER BY MessageDate DESC) AS m INNER JOIN tblUsers AS u ON u.UserID = MessageTo  LEFT JOIN 
		WHERE MessageTo = ".$userid." AND u.clientID = '".clientID."'	ORDER BY `MessageDate` DESC LIMIT 10 ";
*/		
		
		$results = $this->_db->query($sql)->fetchAll();
		return $results;

	}
	
	public function dbeemassages2($msgdate,$userid,$start,$adminid='',$company='',$datefrom='',$dateto='')
	{
		
		if($msgdate){
			$Offset = (int)$start;
			$select = $this->_db->select()
			->from(array($this->_name),array('ID','MessageTo','MessageFrom','Message','Unread','MsgDate'=>'MessageDate','ChainParent','Fromadmin'))
			->join(array('u' => $this->getTableName(USERS)), 'u.UserID = MessageFrom',array('Name','lname','UserID','Status','usertype','ProfilePic','company'));
			if($company !=''){				
				$select->join(array('us' => $this->getTableName(USERS)), 'us.UserID = MessageTo',array('UserID as uid2','company as com2'));
				$select->where('u.company = "'.$company.'" OR us.company = "'.$company.'"');
			}
			$select->where('MessageFrom = '.$userid.' OR MessageTo = '.$userid);
			//$select->where('NOT FIND_IN_SET(ArchivedBy,(?))', $userid);
			//->where('MessageDate IN(?)', $msgdate)->where('u.clientID = ?', clientID);
			
			if($datefrom !=''){  
				//$select->where("DATE_FORMAT(MessageDate,'%d-%m-%Y') between '".$datefrom."' AND '".$dateto."'");
				$start_date_formatted = date('Y-m-d', strtotime($datefrom)).' 00:01:01';
				$end_date_formatted = date('Y-m-d', strtotime($dateto)).' 00:01:01';
				$select->where("MessageDate >= ?",  $start_date_formatted)
   				->where("MessageDate <= ?",  $end_date_formatted);
			}
			$select->group('ChainParent')
			->order('MessageDate DESC')
			->limit('10', $Offset);
			//echo $select->__toString();exit;
			$result =$this->_db->fetchAll($select);
			return $result;
		}
	}
	 
	public function messagedbees($massageid,$massageto,$massagefrom,$start,$userid)
	{
		$Offset = (int)$start;
		$select = $this->_db->select()
		->from(array('t' => $this->_name),	array('ID','MessageTo','ChainParent','ArchivedBy','MessageFrom','Message','MsgDate'=>'MessageDate'))
		->joinLeft(array('u' => $this->getTableName(USERS)), 't.MessageFrom = u.UserID AND t.clientID=u.clientID')
		->where('(' .'t.MessageFrom ='.$massagefrom.' AND t.MessageTo = '.$massageto.'')
		->orWhere('t.MessageFrom ='.$massageto.' AND t.MessageTo = '.$massagefrom.''.')' )
		->where('ChainParent = ?', $massageid)
		->where('NOT FIND_IN_SET(ArchivedBy,(?))', $userid)->where('t.clientID = ?', clientID)
		->order('t.MessageDate DESC')
			->limit(10,$Offset);
		//echo $select->__toString();
			
		// $sql = "SELECT * FROM tblMessages WHERE ChainParent=".$massageid." AND NOT FIND_IN_SET(".$userid.", ArchivedBy) ORDER BY `MessageDate` DESC";
		 
		$result =$this->_db->fetchAll($select);
		return $result;

	}
	public function messagedbeessearch($user,$userid,$datefrom,$dateto)
	{
		$Offset = (int)$start;
		$select = $this->_db->select()
		->from(array('t' => $this->_name),	array('ID','MessageTo','ChainParent','ArchivedBy','MessageFrom','Message','MsgDate'=>'MessageDate'))
		->joinLeft(array('u' => $this->getTableName(USERS)), 't.MessageFrom = u.UserID AND t.clientID=u.clientID');	
		if($user!=''){
			$select->where('(' .'t.MessageFrom ='.$user.' AND t.MessageTo = '.$userid.'');
			$select->orWhere('t.MessageFrom ='.$userid.' AND t.MessageTo = '.$user.''.')' );
		}else{
			$select->where('t.MessageFrom = '.$userid.' OR t.MessageTo = '.$userid);
		}
		if($datefrom !=''){
			//$select->where("DATE_FORMAT(t.MessageDate,'%Y-%m-%d') between '".$datefrom."' AND '".$dateto."'");
			$start_date_formatted = date('Y-m-d', strtotime($datefrom)).' 00:00:00';
			$end_date_formatted = date('Y-m-d', strtotime($dateto)).' 00:00:00';
			$select->where("t.MessageDate >= ?",  $start_date_formatted)
			->where("t.MessageDate <= ?",  $end_date_formatted);
		}
		$select->where('NOT FIND_IN_SET(ArchivedBy,(?))', $userid)->where('t.clientID = ?', clientID)
		->order('t.MessageDate DESC');
		// $sql = "SELECT * FROM tblMessages WHERE ChainParent=".$massageid." AND NOT FIND_IN_SET(".$userid.", ArchivedBy) ORDER BY `MessageDate` DESC";
	
		$result =$this->_db->fetchAll($select);
		return $result;
	
	}
	 
	public function chkblockuser($cookieuser,$user)
	{
		$select = $this->_db->select()
		->from($this->getTableName(BLOCKUSER_MSG))
		->where('BlockedUser ='.$user)
		->where('BlockedBy ='.$cookieuser)->where('clientID = ?', clientID);
			

		$result = $this->_db->fetchAll($select);
		return count($result);
	}

	public function chkblockuserfeed($cookieuser,$user)
	{
		$select = $this->_db->select()
		->from($this->getTableName(BLOCKUSER_MSG))
		->where('BlockedUser ='.$cookieuser)
		->where('BlockedBy ='.$user)->where('clientID = ?', clientID);
			
		//		echo $select->__toString();
			
		$result = $this->_db->fetchAll($select);
		return count($result);
	}

	public function updatemunread($data,$mid)
	{
		if($this->_db->update($this->_name,$data,'ID='.$mid))
			return 1;
		else
			return 0;

	}
	public function insertMessage($data)
	{
		 
		if($this->_db->insert($this->_name,$data)){
			$id = $this->_db->lastInsertId();
			 
			if($data['ChainParent']==0){
				 
				$data2 = array('ChainParent'=> $id);
				$this->update($data2,'ID = '.$id);
			}
			//$id = $this->_db->lastInsertId();
			return $id;
		}
		else
			return 0;

	}
	 
	public function messagereloads($massageto,$massagefrom)
	{
		$Offset = (int)$start;
		$sql = "SELECT * FROM tblMessages WHERE clientID=".clientID." AND ((MessageTo=".$massageto." AND MessageFrom=".$massagefrom.") OR (MessageTo=".$massagefrom." AND MessageFrom=".$massageto.")) AND Fromadmin='0' AND NOT FIND_IN_SET(".$massagefrom.", ArchivedBy) ORDER BY `MessageDate` DESC LIMIT 1";
		 
		$results = $this->_db->query($sql)->fetchAll();
		return $results;

	}
	public function getuser($id,$user='')
	{
		$select = $this->_db->select()
		->from($this->getTableName(USERS))
		->where('UserID ='.$id)->where('clientID = ?', clientID);		
		$result = $this->_db->fetchRow($select);
		return $result;
	}
	public function getuser1($id,$user='')
	{
		$select = $this->_db->select()
		->from($this->getTableName(USERS))
		->where('UserID ='.$id)->where('clientID = ?', clientID);		
			$select->where("Name LIKE '%$user%' OR Username LIKE '%$user%'");	
		//echo $select->__toString();
		$result = $this->_db->fetchRow($select);
		return $result;
	}

	public function messagearchivtable($message)
	{
		$select = $this->_db->select()
		->from($this->_name)
		->where('ID ='.$message)->where('clientID = ?', clientID);
		//echo $select->__toString();
		$result = $this->_db->fetchRow($select);
		return $result;
	}

	public function updatearchive($data,$mid,$rowid,$uid)
	{ 
		
		
		if($this->_db->update($this->_name,$data,'ID = '.$mid)){
			$this->_db->delete('tblactivity', array(
                "act_typeId='" . $rowid . "'","act_ownerid='" . $uid . "'"
            ));

			return 1;
		}else{
			return 0;
		}

	}
	public function updatearchive2($data,$mid,$rowid,$uid)
	{
		

		$this->_db->update($this->_name,$data,'ChainParent = '.$mid);
		$this->_db->delete('tblactivity', array(
                "act_typeId='" . $rowid . "'","act_ownerid='" . $uid . "'"
            ));
		
			return 1;
	}
	public function checkchainparent($userid,$user)
	{
		/* $select = $this->_db->select()
		->from(array('t' => $this->_name),	array('ID','MsgDate'=>'MessageDate'))
		->where('t.MessageFrom ='.$userid.' AND t.MessageTo = '.$user.'')
		->orWhere('t.MessageFrom ='.$user.' AND t.MessageTo = '.$userid.'')
		->where('t.clientID = ?', clientID)
		->where('t.Fromadmin = ?', '0')
		->order('t.MessageDate asc')
		->limit(1);*/

		 $select = "SELECT `ID`, `MessageDate` AS `MsgDate` FROM `tblMessages` As `t`  WHERE (MessageFrom =".$userid." AND MessageTo = ".$user.") AND (clientID = ".clientID.") AND (Fromadmin = '0') UNION SELECT `ID`, `MessageDate` AS `MsgDate` FROM `tblMessages`  WHERE  (MessageFrom =".$user." AND MessageTo = ".$userid.") AND (clientID = ".clientID.") AND (Fromadmin = '0') ORDER BY `MsgDate` asc LIMIT 1";
		
		$result = $this->_db->fetchRow($select);
		return $result;
	}
	public function inserblockuser($data)
	{
		if($this->_db->insert($this->getTableName(BLOCKUSER_MSG),$data)){
			return 1;
		}
		else
			return 0;

	}

	public function getchaninparent($mid)
	{
		$select = $this->_db->select()
		->from(array('t' => $this->_name),	array('ID','ChainParent'))
		->where('t.ID = ?',$mid)->where('t.clientID = ?', clientID)
		->limit(1);
		$result = $this->_db->fetchRow($select);
		return $result['ChainParent'];
	}
	public function searchcomusers($param,$user)
	{
		$myclientdetails = new Application_Model_Clientdetails();
		$param = $myclientdetails->customEncoding($param,$search="true");
		$select1 = $this->_db->select()
		->from(array('u'=>'tblUsers'),	array('UserID','Name','ProfilePic'))->joinLeft(array('f'=>$this->getTableName(FOLLOWS)), 'u.UserID=f.User')
		->where('Name REGEXP ?',"^$param")->where('u.clientID = ?', clientID)->where('f.User = ?', $user);
		$select2 = $this->_db->select()
		->from(array('u'=>'tblUsers'),	array('UserID','Name','ProfilePic'))->joinLeft(array('f'=>$this->getTableName(FOLLOWS)), 'u.UserID=f.User')
		->where('Name REGEXP ?',"^$param")->where('u.clientID = ?', clientID)->where('f.FollowedBy = ?', $user);		
		$select = $this->_db->select()
		->union(array($select1, $select2));
		$result = $this->_db->fetchAll($select);			
		return $result;
		
	}

	public function updateactivity($id)
	{
			if($id!=''){
				 
				$data = array('act_status'=> '1');
				$this->_db->update('tblactivity',$data,'act_id='.$id);
				
			}
	
	}
}