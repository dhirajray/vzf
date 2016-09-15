<?php



class Application_Model_Notification extends Application_Model_DbTable_Master

{

    protected $_name = null;



	protected function _setupTableName()
    {

              parent::_setupTableName();		

		      $this->_name = $this->getTableName(CAT);     

    }

   public function commomInsert($act_type, $act_message, $act_typeId, $act_userId, $act_ownerid,$scoretype='',$act_cmnt_id='',$status='0')
   {
      $act_date =  date('Y-m-d H:i:s'); 
      $data = array(
                     'act_type'     => $act_type,
                     'act_message'  => $act_message,
                     'act_typeId'   => $act_typeId,
                     'act_cmnt_id'   => $act_cmnt_id,
                     'act_userId'   => $act_userId,
                     'act_ownerid'  => $act_ownerid,
                     'act_score_type' => $scoretype,
                     'act_date'     => $act_date,
                     'clientID'     => clientID,
                     'act_status'   => $status
                  );
   
     
      if ($this->_db->insert('tblactivity', $data))
         return true;
      else
         return false;
        
   }
    
   public function updateactivity($data, $act_id)
   {
     
     return $this->_db->update('tblactivity', $data, array(
         "act_id='" . $act_id . "'"
     ));
   }

	public function getnotificationuser($userid)

    {

	   	$select = $this->_db->select()

	   		->from($this->getTableName(NOTIFICATION_SETTING))  

	   			->where('User = ?',$userid)->where('clientID = ?', clientID);   

	   	$result = $this->_db->fetchRow($select);	 

	   	return $result;	   	   	 

   }

   

   public function getnotificationuser1($userid)

   {

    $select = $this->_db->select()

  	 	->from($this->getTableName(FOLLOWS),array('User'))

   			->where('FollowedBy = ?',$id)->where('clientID = ?', clientID);

   		$result = $this->_db->fetchRow($select);

	   	return $result;	

   }

   public function getscorenotification($userid,$activity)

   {

 	$select = $this->_db->select()

   		->from(array('s' => $this->getTableName(SCORING)),array('ScoreDate','Score','Type','ID'))

   			->join(array('u' => $this->getTableName(USERS)), 's.UserID = u.UserID','Name')

   				->where('s.Owner= ?',$userid)

   					->where('s.ScoreDate >= ?',$activity)->where('s.clientID = ?', clientID)

   						->order(array('s.ScoreDate Desc')); 	

    $result = $this->_db->fetchAll($select);	

   	return count($result);

   }

   public function getscorenotificationall($userid,$activity)

   {

   	$select = $this->_db->select()

	   	->from(array('s' => $this->getTableName(SCORING)),array('ScoreDate','Score','Type','ID'))

		   	->join(array('u' => $this->getTableName(USERS)), 's.UserID = u.UserID')

			   	->where('s.Owner= ?',$userid)

				   	->where('s.ScoreDate >= ?',$activity)->where('s.clientID = ?', clientID)

				   		->order(array('s.ScoreDate Desc'));
   	
                      //echo $select->__toString();

   	$result = $this->_db->fetchAll($select);

   	return $result;

   }

   public function gecomm($Scoreid)

   {

   	$select = $this->_db->select()

   		->from($this->getTableName(COMMENT),array('Type','Comment','LinkTitle','PicDesc','VidDesc'))

   			->where('CommentID = ?',$Scoreid)->where('clientID = ?', clientID);   	

  			 	$result = $this->_db->fetchRow($select);    			 	

   	return $result;

   }  

   public function getdb($Scoreid)

   {

   	$select = $this->_db->select()

   		->from($this->getTableName(COMMENT),array('Type','Text','LinkTitle','PicDesc','VidDesc','PollText'))

   			->where('CommentID= ?',$ScoreRow)->where('clientID = ?', clientID);

   	$result = $this->_db->fetchAll($select);

   	return $result;

   }


   public function chknewcomments($seencomms,$CheckDateComments,$dbs,$cookieuser)

   {

   	$select = $this->_db->select()

   		->from(array('c' => $this->getTableName(COMMENT)))

		   	->join(array('u' => $this->getTableName(USERS)), 'c.UserID = u.UserID','Name')

			   	->where('c.UserID != ?',$cookieuser)

				   	->where('c.CommentDate >= ?',$CheckDateComments)

				   		->where('c.DbeeID IN(?)',$dbs)->where('c.clientID = ?', clientID);

   	if($seencomms){

   	$select->where('c.CommentID != ?',$seencomms);

   	}  

   	$select->order(array('c.CommentDate Desc'));

   //	echo $select->__toString();

   	$result = $this->_db->fetchAll($select);

   	return $result;

   }

   public function chknewmention($CheckDateMention,$cookieuser)
   
   {
   
   	$select = $this->_db->select()
   
   	->from(array('c' => $this->getTableName(MENTIONS)))
   
   	->join(array('u' => $this->getTableName(USERS)), 'c.MentionedBy = u.UserID','Name')
   
   	->where('c.UserMentioned = ?',$cookieuser)
   
   	->where('c.MentionDate >= ?',$CheckDateMention)->where('c.clientID = ?', clientID);
   

   	$select->order(array('c.MentionDate Desc'));
   
 	//echo $select->__toString();
   
   	$result = $this->_db->fetchAll($select);
   
   	return $result;
   
   }
   

   //use by myhome with dbee listing

   public function chknewcomment($seencomms,$CheckDateComments,$dbs,$cookieuser)

   {

   	$select = $this->_db->select()

   	->from(array('c' => $this->getTableName(COMMENT)))

	   	->join(array('u' => $this->getTableName(USERS)), 'c.UserID = u.UserID','Name')

		   	->join(array('d' => $this->getTableName(DBEE)), 'c.DbeeID = d.DbeeID')

			   	->where('c.UserID != ?',$cookieuser)

				   	->where('c.CommentDate > ?',$CheckDateComments)

				   		->where('c.DbeeID IN(?)',$dbs)->where('c.clientID = ?', clientID);

   	if($seencomms){

   		$select->where('c.CommentID != ?',$seencomms);

   	}

   	$select->order(array('c.CommentDate Desc'));

   	

   	$result = $this->_db->fetchAll($select);

   	return $result;

   }



   public function getNotifyPop($dbeeid,$cookieuser)

   {

   	$select = $this->_db->select()

   		->from($this->getTableName(COMMENT),array('NotifyPop'))

		   	->where('DbeeID='.$dbeeid)

		   		->where('UserID= ?',$cookieuser)->where('clientID = ?', clientID);

   	//echo $select->__toString();

   	$result = $this->_db->fetchRow($select);

   	return $result;

   }

   public function getmessagenotify($cookieuser,$CheckDateMsgs)

   {

    $select = $this->_db->select()

	   	->from(array('m' => $this->getTableName(MESSAGES)))

		   	->join(array('u' => $this->getTableName(USERS)), 'm.MessageFrom = u.UserID',array('u.Name'))

			   	->where('m.MessageTo ='.$cookieuser)

			   		->where('m.MessageDate >= ?',$CheckDateMsgs)->where('m.clientID = ?', clientID) 

    					->order('m.MessageDate DESC')

    						->limit(1);    

   	$result = $this->_db->fetchRow($select);

   	return $result;

   }

   public function getmessagenotifycnt($cookieuser,$CheckDateMsgs)

   {

   	$select = $this->_db->select()

	   	->from(array('m' => $this->getTableName(MESSAGES)))

		   	->join(array('u' => $this->getTableName(USERS)), 'm.MessageFrom = u.UserID')

			   	->where('m.MessageTo='.$cookieuser)

			  	 	->where('m.MessageDate >= ?',$CheckDateMsgs)->where('m.clientID = ?', clientID);

   	 	$result = $this->_db->fetchAll($select);

   		return count($result);

   }    

   public function getgroupmember($cookieuser,$CheckDateGroups)

   {

   	$select = $this->_db->select()

	   	->from(array('g' => $this->getTableName(GROUP_MEMBER)))

		   	->join(array('u' => $this->getTableName(USERS)), 'g.Owner = u.UserID',array('u.Name','u.lname'))

			   	->where('g.User = ?',$cookieuser)

				   	->where('g.SentBy = ?','Owner')

					   	->where('g.Status = ?','0')

					   		->where('g.JoinDate >= ?',$CheckDateGroups)->where('g.clientID = ?', clientID)

					   			->order('g.JoinDate DESC')

   									->limit(1);   

   	$result = $this->_db->fetchRow($select);

   	return $result;

   

   }

   public function getgroupmembercnt($cookieuser,$CheckDateGroups)

   {

   	$select = $this->_db->select()

	   	->from(array('g' => $this->getTableName(GROUP_MEMBER)))

		   	->join(array('u' => $this->getTableName(USERS)), 'g.Owner = u.UserID','Name')

			   	->where('g.User='.$cookieuser)

				   	->where('g.SentBy= ?','Owner')

					   	->where('g.Status = ?', '0')->where('g.clientID = ?', clientID)

					   		->where('g.JoinDate >= ?',$CheckDateGroups);

   	$result = $this->_db->fetchAll($select);

   	return count($result);

   

   }

  

   public function getjoingroup($cookieuser,$CheckDateGroups)

   {

   	$select = $this->_db->select()

	   	->from(array('g' => $this->getTableName(GROUP_MEMBER)))

		   	->join(array('u' => $this->getTableName(USERS)), 'g.User = u.UserID',array('u.Name','u.lname'))

			   	->where('g.Owner= ?',$cookieuser)

				   	->where('g.SentBy= ?','Self')

				   		->where('g.Status= ?','0')

   							->where('g.JoinDate >= ?',$CheckDateGroups)->where('g.clientID = ?', clientID)

					   			->order('g.JoinDate DESC')

   									->limit(1);

   	$result = $this->_db->fetchRow($select);

   	return $result;

   }

   public function getjoingroupcnt($cookieuser,$CheckDateGroups)

   {

    $select = $this->_db->select()

	   	->from(array('g' => $this->getTableName(GROUP_MEMBER)))

		   	->join(array('u' => $this->getTableName(USERS)), 'g.User = u.UserID','Name')

			   	->where('g.Owner= ?',$cookieuser)

				   	 ->where('g.SentBy= ?','Self')

					   	->where('g.Status= ?','0')

					   		->where('g.JoinDate >= ?',$CheckDateGroups)->where('g.clientID = ?', clientID);

   	$result = $this->_db->fetchAll($select);

   	return count($result);

   }

    

   public function getdbeesuser1($user,$type,$date1)
   {
   	
   	// $privategroup_obj = new Application_Model_Privateuser();
   	// $PrivateGroups    = $privategroup_obj->getprivategroup();
   	// foreach($PrivateGroups as $data):   	
   	// $pgroup[] = $data['ID'];
   	// endforeach;   	
   	// if(!empty($PrivateGroups)){
   		// $privateGroups1 = implode($pgroup,',');
   	// }  
   	$user = implode($user,',');
   	$sql = "select * from tblDbees c left join tblUsers u on c.User = u.UserID where c.clientID=".clientID." AND c.Privategroup=0 AND u.UserID IN(".$user.") ";
   	// if(!empty($PrivateGroups)){
   		// $sql .= " AND c.GroupID NOT IN(".$privateGroups1.")";
   	// }   	
   	if($date1 > 0){
   		$sql .= "AND c.PostDate BETWEEN DATE( DATE_SUB( NOW() , INTERVAL ".$date1." DAY ) ) AND NOW() ";
   	}
   	$sql .= "order by c.Postdate Desc";
	
   	$results = $this->_db->query($sql)->fetchAll();
   	return $results;
   	
   }

   public function redbeeid($user)

   {

   	$select = $this->_db->select()

   		->from(array('c' => $this->getTableName(REDBEES)))

   			->where('DbeeOwner= ?',$user)->where('clientID = ?', clientID);   	

   	$result = $this->_db->fetchAll($select); 	
	if(count($result)>0){
	   	foreach($result as $data):
	
	   	$dbeeid[] = $data['DbeeID'];
	
	   	endforeach;
	 }
	   	return $dbeeid;
	
	   }
	 
   public function getdbeenotify($dbeeid,$type,$date1,$date2)

   {
   
      	
   	$dbeeid = implode($dbeeid,',');
   	$sql = "select * from tblReDbees c left join tblUsers u on c.ReDBUser = u.UserID where c.clientID=".clientID." AND c.DbeeID IN(".$dbeeid.") "; 	
   	
   	
   	if($date1 > 0){
   		$sql .= "AND c.ReDBDate BETWEEN DATE( DATE_SUB( NOW() , INTERVAL ".$date1." DAY ) ) AND NOW() ";
   	}
   	$sql .= "order by c.ReDBDate Desc";

   	$results = $this->_db->query($sql)->fetchAll();
   	return $results;   	
   	
   }
   
   	public function getdbtext($DbeeID)

    {
	   	$select = $this->_db->select()
	   		->from($this->getTableName(DBEE))  
	   			->where('DbeeID = ?',$DbeeID)->where('clientID = ?', clientID);   
	   	$result = $this->_db->fetchRow($select);	 
	   	return $result;
		
		/*$select = $this->_db->select()
            ->from(array('r' => $this->getTableName(REDBEES)))
   			->join(array('d' => $this->getTableName(DBEE)), 'r.DbeeID = d.DbeeID')
   				->where('d.DbeeID IN(?)',$DbeeID)   
   					->order(array('r.ReDBDate Desc'));
   	    $result = $this->_db->fetchAll($select);
        //echo $select->__toString();die;
   	    return $result;	*/   	   	 

   }

   public function mendbeeid($user)

   {

   	$select = $this->_db->select()

   	->from(array('c' => $this->getTableName(MENTION)))

   	->where('UserMentioned= ?',$user)->where('clientID = ?', clientID);

   	$result = $this->_db->fetchAll($select);

   	foreach($result as $data):

   	$dbeeid[] = $data['DbeeID'];

   	endforeach;

   	return $dbeeid;

   }
   public function getmentionnotify($dbeeid,$type,$date1,$date2,$user)
   
   {
   
   
   	$dbeeid = implode($dbeeid,',');
   //	$sql = "select * from tblMentions c left join tblUsers u on c.MentionedBy  = u.UserID where c.DbeeID IN(".$dbeeid.") ";   
   
   	$sql = "select * from tblMentions c left join tblUsers u on c.MentionedBy  = u.UserID where  c.clientID=".clientID." AND c.UserMentioned =" .$user ;
   
   	if($date1 > 0){
   		$sql .= " AND c.MentionDate BETWEEN DATE( DATE_SUB( NOW() , INTERVAL ".$date1." DAY ) ) AND NOW() ";
   	}
   	$sql .= " order by c.MentionDate Desc"; 
   	
   	$results = $this->_db->query($sql)->fetchAll();
   	return $results;
   
   }   
   
   public function chkredbee($CheckDateDbees,$cookieuser)
   
   {     
   	$select = $this->_db->select()   
	   ->from(array('c' => $this->getTableName(REDBEES)))	   
		   	->join(array('u' => $this->getTableName(USERS)), 'c.ReDBUser = u.UserID','Name')	   
			   	->where('c.DbeeOwner  = ?',$cookieuser)	   
			   		->where('c.ReDBDate >= ?',$CheckDateDbees)->where('c.clientID = ?', clientID);		 
  // echo	$select->__toString();  
   	$result = $this->_db->fetchAll($select);
   
   	return $result;

   
   }
   
   public function chkfollowing($CheckDateDbees,$cookieuser)   
   {
   	$select = $this->_db->select()
	   	->from(array('c' => $this->getTableName(FOLLOWS)))
		   	->join(array('u' => $this->getTableName(USERS)), 'c.FollowedBy = u.UserID','Name')
			   	->where('c.User  = ?',$cookieuser)
			  	 	->where('c.StartDate >= ?',$CheckDateDbees)->where('clientID = ?', clientID)
			  	 		->order('c.StartDate Desc');
   // echo	$select->__toString();
   	$result = $this->_db->fetchAll($select);
   
   	return $result;
   
   }
   

}	