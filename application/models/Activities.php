<?php

class Application_Model_Activities extends Application_Model_DbTable_Master
{
 	protected $_name = null; 
 
    protected function _setupTableName()
    {
              parent::_setupTableName();		
		      $this->_name = $this->getTableName(ACTIVITY);     
    }
    
    public function saveseenactivity($data, $table)
   {
      if ($this->_db->insert($table, $data)) return true;
      else return false;        
   }
    public function updateactivity($data, $act_id)
    {
        return $this->_db->update('tblactivity', $data, array( "act_id='" . $act_id . "'" ));
    }
    public function updateactivityofuser($data, $act_id)
    {
        return $this->_db->update('tblactivity', $data, array( "act_ownerid='" . $act_id . "'" ));
    }

    public function getactivitynotify($offset,$user,$type,$dbs='')   
    {          	
     $select = $this->_db->select()
      ->from(array('c' => 'tblactivity'))
	      ->join(array('u' => 'tblUsers'), 'c.act_userId  = u.UserID AND c.clientID  = u.clientID',array('u.UserID','u.Name','u.lname','u.Username','u.usertype','u.ProfilePic'))
    			 ->join(array('d' => 'tblDbeeComments'), 'c.act_cmnt_id  = d.CommentID',array('d.Type','d.VidID','d.Comment','d.Pic','d.PicDesc','d.VidDesc','d.LinkDesc','d.LinkTitle','d.UserLinkDesc'));  
		      if($dbs=='') $dbs=0;
     				$select->where('c.act_typeId IN(?)',$dbs);
		        $select->where('c.act_type = ?',$type)
		       	->where('d.UserID != ?',$user)->where('c.clientID = ?', clientID)
		      		->order('c.act_date Desc')
			      		->limit(PAGE_NUM_LEAGE,$offset);	

			$results = $this->_db->fetchAll($select);
      if(count($results)>0)
      {
          $alldbs   = array();
          $counter  = 0;
           
          foreach ($results as $key => $value) 
          {
              $chkread  = $this->chkdborcomment($value['act_id'],$user); 
              if($chkread==0)
              {
                 
                     $act_date=  date('Y-m-d H:i:s'); 
                      $data    =  array(
                     'act_id'     => $value['act_id'],
                     'chk_user'   => $user,
                     'chk_db'     => $value['act_typeId'],
                     'chk_date'   => $act_date, 
                     'clientID'   => clientID,  
                    );
                    $this->saveseenactivity($data,'tblactchecked');
                 
              } 
              $counter++;
          }
      }      
      return $results;       
    }

    public function getactivitynotifyMention($offset,$user,$type,$dbs='')   
    {           
     $select = $this->_db->select()
      ->from(array('c' => 'tblactivity'))
        ->join(array('u' => 'tblUsers'), 'c.act_userId  = u.UserID AND c.clientID  = u.clientID',array('u.UserID','u.Name','u.lname','u.Username','u.usertype','u.ProfilePic'))
           ->join(array('d' => 'tblDbeeComments'), 'c.act_cmnt_id  = d.CommentID',array('d.Type','d.VidID','d.Comment','d.Pic','d.PicDesc','d.VidDesc','d.LinkDesc','d.LinkTitle','d.UserLinkDesc'));  
         
            $select->where('c.act_type = ?',$type)->where('c.act_ownerid = ?',$user)
            ->where('d.UserID != ?',$user)->where('c.clientID = ?', clientID)
              ->order('c.act_date Desc')
                ->limit(PAGE_NUM_LEAGE,$offset);  

      $result = $this->_db->fetchAll($select);      
      return $result;       
    }

    public function getactivitynotifyMentionondbee($offset,$user,$type,$dbs='')   
    {           
     $select = $this->_db->select()
      ->from(array('c' => 'tblactivity'))
        ->join(array('u' => 'tblUsers'), 'c.act_userId  = u.UserID AND c.clientID  = u.clientID',array('u.UserID','u.Name','u.lname','u.Username','u.usertype','u.ProfilePic'))
           ->join(array('d' => 'tblDbees'), 'c.act_typeId  = d.DbeeID',array('d.Type','d.VidID','d.Text','d.Pic','d.LinkPic','d.PicDesc','d.VidDesc','d.PollText','d.events','d.LinkTitle','d.UserLinkDesc'));  
            $select->where('c.act_type = ?',$type)->where('c.act_ownerid = ?',$user)
            ->where('d.User != ?',$user)->where('c.clientID = ?', clientID)
              ->order('c.act_date Desc')
                ->limit(PAGE_NUM_LEAGE,$offset);
      $result = $this->_db->fetchAll($select);      
      return $result;       
    }
    public function getactivitynotifyscore($offset,$user,$type,$dbs='')
    {
    
    	$select = $this->_db->select()
	    	->from(array('c' => 'tblactivity'))
		    	->join(array('u' => 'tblUsers'), 'c.act_userId  = u.UserID AND c.clientID  = u.clientID',array('u.UserID','u.Name','u.lname','u.Username','u.usertype','u.ProfilePic'));
			    	//->join(array('d' => 'tblDbeeComments'), 'c.act_cmnt_id  = d.CommentID',array('d.Type','d.VidID','d.Comment','d.Pic','d.PicDesc','d.VidDesc','d.LinkDesc','d.LinkTitle','d.UserLinkDesc'));	
				      $select->where('c.act_type = ?',$type)
					     ->where('c.act_ownerid = ?',$user)->where('c.clientID = ?', clientID)
						     ->order('c.act_date Desc')
						    	 ->limit(PAGE_NUM_LEAGE,$offset);
    	
    	$result = $this->_db->fetchAll($select);
    	return $result;
    }   
    public function getactivitynotifyscoretotal($offset,$user,$type,$dbs='')
    {
    
    	$select = $this->_db->select()
	    	->from(array('c' => 'tblactivity'))
		    	->join(array('u' => 'tblUsers'), 'c.act_userId  = u.UserID AND c.clientID  = u.clientID',array('u.UserID','u.Name','u.lname','u.Username','u.ProfilePic'))
			    	->join(array('d' => 'tblDbeeComments'), 'c.act_cmnt_id  = d.CommentID',array('d.Type','d.VidID','d.Comment','d.Pic','d.PicDesc','d.VidDesc','d.LinkDesc','d.LinkTitle','d.UserLinkDesc'));	
					      $select->where('c.act_type = ?',$type)
							->where('c.act_ownerid = ?',$user)->where('c.clientID = ?', clientID)
								->order('c.act_date Desc');	     
    	
    	$result = $this->_db->fetchAll($select);
    	return $result;
    }

    public function getalldbeeCommentUser($DbeeID)   
    {      
      
     $select = $this->_db->select()
	     ->distinct()
	        ->from(array('d' => 'tblDbeeComments'),array('d.UserID'));         
	          $select->where('d.DbeeID = ?',$DbeeID);
	          $select->where('d.Active = ?',1)->where('clientID = ?', clientID);	
	          $result = $this->_db->fetchAll($select);
      
      return $result;
    }   

    public function getactivitynotifytotal($offset,$user,$type,$dbs='')
    {    
    	 $select = $this->_db->select()
      ->from(array('c' => 'tblactivity'))
	      ->join(array('u' => 'tblUsers'), 'c.act_userId  = u.UserID AND c.clientID  = u.clientID',array('u.UserID','u.Name','u.lname','u.Username','u.ProfilePic'))
    			 ->join(array('d' => 'tblDbeeComments'), 'c.act_cmnt_id  = d.CommentID',array('d.Type','d.VidID','d.Comment','d.Pic','d.PicDesc','d.VidDesc','d.LinkDesc','d.LinkTitle','d.UserLinkDesc'));	  
		      if($dbs=='') $dbs=0;
     				$select->where('c.act_typeId IN(?)',$dbs);
		      $select->where('c.act_type = ?',$type)
		       	->where('d.UserID != ?',$user)->where('c.clientID = ?', clientID)
		      		->order('c.act_date Desc');		      			     
			  $result = $this->_db->fetchAll($select);      
      return $result;       
    }
    
    public function getactivitynotifydbee($offset,$user,$type,$following='')
    {

      if(count($following )>0){
        $select = $this->_db->select()->from(array('c' => 'tblactivity'))
        ->join(array('u' => 'tblUsers'), 'c.act_userId  = u.UserID AND c.clientID  = u.clientID',array('u.UserID','u.Name','u.lname','u.Username','u.usertype','u.ProfilePic'))
        ->join(array('d' => 'tblDbees'), 'c.act_typeId  = d.DbeeID',array('d.Type','d.VidID','d.Text','d.Pic','d.LinkPic','d.PicDesc','d.VidDesc','d.PollText','d.events','d.LinkTitle','d.UserLinkDesc'));

        $select->join(array('folo' => 'tblFollows'), 'folo.User = c.act_userId',array('folo.User','folo.FollowedBy','folo.StartDate'))->where('folo.StartDate <  c.act_date')->where('folo.FollowedBy  =?',$user);
          $select->where('c.act_ownerid IN(?)',$following)
          ->where('c.act_type = ?',$type)->where('c.act_type = ?',$type)->where('c.act_type != ?',47)->where('c.act_status =?', 0);     
      $select->where('c.act_userId != ?',$user)
      ->order('c.act_date Desc')
      ->limit(PAGE_NUM_LEAGE,$offset);  
      //  echo $select->__toString();  exit;

      $results = $this->_db->fetchAll($select);

      if(count($results)>0)
      {
          $alldbs   = array();
          $counter  = 0;
           
          foreach ($results as $key => $value) 
          {
              $chkread  = $this->chkdborcomment($value['act_id'],$user); 
              if($chkread==0)
              {

                     $act_date=  date('Y-m-d H:i:s'); 
                      $data    =  array(
                     'act_id'     => $value['act_id'],
                     'chk_user'   => $user,
                     'chk_db'     => $value['act_typeId'],
                     'chk_date'   => $act_date, 
                     'clientID'   => clientID,  
                    );
                    $this->saveseenactivity($data,'tblactchecked');
              } 
              $counter++;
          }
      } 
      //return $result;
      }
      return $results;



    	/*
          if($following !=''){
        	$select = $this->_db->select()
        	->from(array('c' => 'tblactivity'))
        	 ->join(array('u' => 'tblUsers'), 'c.act_userId  = u.UserID AND c.clientID  = u.clientID',array('u.UserID','u.Name','u.lname','u.Username','u.usertype','u.ProfilePic'))
        			->join(array('d' => 'tblDbees'), 'c.act_typeId  = d.DbeeID',array('d.Type','d.VidID','d.Text','d.Pic','d.LinkPic','d.PicDesc','d.VidDesc','d.PollText','d.LinkTitle','d.UserLinkDesc'));
            //$select->join(array('folo' => 'tblFollows'), 'folo.FollowedBy = c.act_userId',array('folo.User','folo.FollowedBy','folo.StartDate'))->where('folo.StartDate <  c.act_date')->where('folo.FollowedBy  =?',$user);
        			$select->where('c.act_ownerid IN(?)',$following)
        			->where('c.act_type = ?',$type)->where('c.act_type = ?',$type);    	
          	$select->where('c.act_userId != ?',$user)
          	->order('c.act_date Desc')
          	->limit(PAGE_NUM_LEAGE,$offset);  
           //  echo $select->__toString();  exit;
        	 $result = $this->_db->fetchAll($select);
        	 //return $result;
        	 }
        	 return $result;
      */


    }

/*
     public function GetActivityNotifyAdminApprove($offset,$user,$type)
    {
   
      $select = $this->_db->select()
      ->from(array('c' => 'tblactivity'))
       ->join(array('u' => 'tblUsers'), 'c.act_userId  = u.UserID AND c.clientID  = u.clientID',array('u.UserID','u.Name','u.Username','u.usertype','u.ProfilePic'))
          ->join(array('d' => 'tblDbees'), 'c.act_typeId  = d.DbeeID',array('d.Type','d.VidID','d.Text','d.Pic','d.LinkPic','d.PicDesc','d.VidDesc','d.PollText','d.LinkTitle','d.UserLinkDesc'));
          $select->where('c.act_ownerid = ?',adminID);
          $select->where('c.act_type = ?',$type);    
        $select->where('c.act_userId = ?',$user)->where('c.clientID = ?', clientID)
      ->order('c.act_date Desc')
      ->limit(PAGE_NUM_LEAGE,$offset); 
           
      $result = $this->_db->fetchAll($select);
      //return $result;
     
      return $result;
    }
    */
    public function getactivitynotifydbeetotal($offset,$user,$type,$following='')
    {
    	if($following !=''){
    		$select = $this->_db->select()
	    		->from(array('c' => 'tblactivity'))
	    			->join(array('u' => 'tblUsers'), 'c.act_userId  = u.UserID AND c.clientID  = u.clientID',array('u.UserID','u.Name','u.lname','u.Username','u.ProfilePic'));
	    
    		$select->where('c.act_ownerid IN(?)',$following)
    			->where('c.act_type = ?',$type);
    
    		$select->where('c.act_userId != ?',$user)->where('c.clientID = ?', clientID)
    			->order('c.act_date Desc')
    				->limit(PAGE_NUM_LEAGE+1,$offset);
    
    		$result = $this->_db->fetchAll($select);
    		//return $result;
    	}
    	return $result;
    }
    public function getactivitynotifymessage($offset,$user,$type)
    {    
      $select = $this->_db->select();
      $select->from(array('c' => 'tblactivity'))->join(array('u' => 'tblUsers'), 'c.act_userId  = u.UserID AND c.clientID  = u.clientID',array('u.UserID','u.Name','u.lname','u.Username','u.usertype','u.ProfilePic'));  
      $select->join(array('m' => 'tblMessages'), 'c.act_typeId  = m.ID',array('m.Message'));
      $select->where('c.act_ownerid  = ?',$user)->where('c.act_type = ?','10')->where('c.act_userId  !=?', $user ) ;
      $select->where('c.clientID = ?', clientID)->order('c.act_date Desc')->limit(PAGE_NUM_LEAGE,$offset);
      //echo $select->__toString();
    //exit;
      return $this->_db->fetchAll($select);     
    }
    public function getactivitynotifyall($offset,$user,$type,$following='')
    {    
      $select = $this->_db->select();
      $select->from(array('c' => 'tblactivity'))
          ->join(array('u' => 'tblUsers'), 'c.act_userId  = u.UserID AND c.clientID  = u.clientID',array('u.UserID','u.Name','u.lname','u.Username','u.usertype','u.ProfilePic')) ;
      if($type!=4) $select->join(array('d' => 'tblDbees'), 'c.act_typeId  = d.DbeeID',array('d.Type','d.VidID','d.Text','d.Pic','d.LinkPic','d.PicDesc','d.VidDesc','d.PollText','d.events','d.LinkTitle','d.UserLinkDesc'));
              if($type!=5) $select->where('c.act_userId != ?',$user);  
              $select->where('c.act_message != ?','18')->where('c.act_message != ?','2')->where('c.act_message != ?','6');
                     $select->where('c.clientID = ?', clientID)->order('c.act_date Desc')
                          ->limit(PAGE_NUM_LEAGE,$offset);
                       // echo $select->__toString();  
                return $this->_db->fetchAll($select);     
    }
    public function getactivitynotify1totalall($offset,$user,$type,$following='')
    {    
      $select = $this->_db->select();
      $select->from(array('c' => 'tblactivity'))
          ->join(array('u' => 'tblUsers'), 'c.act_userId  = u.UserID AND c.clientID  = u.clientID',array('u.UserID','u.Name','u.lname','u.Username','u.ProfilePic'))  ;
         if($type!=4) $select->join(array('d' => 'tblDbees'), 'c.act_typeId  = d.DbeeID',array('d.Type','d.VidID','d.Text','d.Pic','d.LinkPic','d.PicDesc','d.VidDesc','d.PollText','d.events','d.LinkTitle','d.UserLinkDesc'));
            $select->where('c.act_ownerid  = ?',$user)->where('c.act_message != ?','13')->where('c.act_userId  !=?', $user ) ;
              if($type!=5) $select->where('c.act_userId != ?',$user);               
                     $select->where('c.clientID = ?', clientID)->order('c.act_date Desc');
               return $this->_db->fetchAll($select);
              
    }
    public function getactivitynotify1($offset,$user,$type,$following='')
    {    
    	$select = $this->_db->select();
    	$select->from(array('c' => 'tblactivity'))
    			->join(array('u' => 'tblUsers'), 'c.act_userId  = u.UserID AND c.clientID  = u.clientID',array('u.UserID','u.Name','u.lname','u.Username','u.usertype','u.ProfilePic'))	;
    	if($type!=4) $select->join(array('d' => 'tblDbees'), 'c.act_typeId  = d.DbeeID',array('d.Type','d.VidID','d.Text','d.Pic','d.LinkPic','d.PicDesc','d.VidDesc','d.PollText','d.events','d.LinkTitle','d.UserLinkDesc'));
      if($type!=4) $select->join(array('cmnt' => 'tblDbeeComments'), 'c.act_cmnt_id  = cmnt.CommentID',array(  'cmnt.Comment'));  
				    $select->where('c.act_ownerid  = ?',$user)
				    			->where('c.act_type = ?',$type)->where('c.act_message != ?','13')->where('c.act_userId  !=?', $user )	;
    	 				if($type!=5) $select->where('c.act_userId != ?',$user);    						
	    							 $select->where('c.clientID = ?', clientID)->order('c.act_date Desc')
	    										->limit(PAGE_NUM_LEAGE,$offset);
				       	return $this->_db->fetchAll($select);		    
    }
    
    public function getactivitynotify1total($offset,$user,$type,$following='')
    {    
    	$select = $this->_db->select();
    	$select->from(array('c' => 'tblactivity'))
    			->join(array('u' => 'tblUsers'), 'c.act_userId  = u.UserID AND c.clientID  = u.clientID',array('u.UserID','u.Name','u.lname','u.Username','u.ProfilePic'))	;
    	   if($type!=4) $select->join(array('d' => 'tblDbees'), 'c.act_typeId  = d.DbeeID',array('d.Type','d.VidID','d.Text','d.Pic','d.LinkPic','d.PicDesc','d.VidDesc','d.PollText','d.events','d.LinkTitle','d.UserLinkDesc'));
				    $select->where('c.act_ownerid  = ?',$user)
				    			->where('c.act_type = ?',$type)->where('c.act_message != ?','13')->where('c.act_userId  !=?', $user )	;
    	 				if($type!=5) $select->where('c.act_userId != ?',$user);    						
	    							 $select->where('c.clientID = ?', clientID)->order('c.act_date Desc');
				       return $this->_db->fetchAll($select);
				    	
    }

    public function getNotification($user,$type,$calling='',$ghostseendate='',$allNotification='')   
    {    
      $condition = "" ;
      $ghostseen = "";
      $mentionchk = "";
      $expertChk = "";
      $scoreChk = '';
     
      if($allNotification=='')
      {
        $mentionchk = 'c.act_type =3 OR ';
        $scoreChk = 'c.act_type =6 OR ';
        $expertChk = 'c.act_type =11 OR ';
        if($calling=='seen') $condition = " AND c.act_status='0'";
        else $condition = "";

        if($ghostseendate!='') 
        {
           $ghostseen = " AND  c.act_date >'".$ghostseendate."'";
        }
        else $ghostseen = "";
      } 

      if($type==3)  
        $chkType = ' ( '.$mentionchk.$scoreChk.$expertChk.'  c.act_type =4 OR c.act_type =5  OR c.act_type =14  OR c.act_type =7   OR c.act_type =30 OR c.act_type =31 OR c.act_type =33 OR c.act_type =34 OR c.act_type =36 OR c.act_type =37 OR c.act_type =38 OR c.act_type =39   OR c.act_type =40 OR c.act_type =41 OR c.act_type =42  OR c.act_type =43 OR c.act_type =44 OR c.act_type =17  OR c.act_type =46 OR c.act_type =1 ) ';
          
      else if($type==10)   $chkType = 'c.act_type = 10 '; 

       $sql = "select c.*,u.UserID,u.Name,u.lname,u.Username,u.usertype,u.ProfilePic from tblactivity c left join tblUsers u on c.act_userId  = u.UserID AND c.clientID  = u.clientID where c.clientID=".clientID." AND ".$chkType."  ".$condition." ".$ghostseen." and c.act_ownerid  =" .$user ." and c.act_userId  !=" .$user ." and c.act_message !=13 order by c.act_date Desc";
      
      if($calling=='') $sql .= " LIMIT 10";
      
      return $this->_db->query($sql)->fetchAll();
      
    } 


    public function getdbeeNotification($follusers,$user,$type,$latseen='',$seen='',$ondbdetail='',$followdate='')   
    {     
        $select = $this->_db->select()->from(array(
            'c' => "tblactivity"
          ))->join(array(
              'u' => 'tblUsers'
          ), 'u.UserID = c.act_userId AND c.clientID  = u.clientID',array('u.UserID','u.Name','u.lname','u.Username','u.ProfilePic'));

          if($type == 1)  
          { 
            $select->where('c.act_userId IN(?)', $follusers);
            //$select->where('c.act_date >?', $latseen);
          }
          if($type == 2) 
          { 
              $select->where('c.act_typeId IN(?)', $follusers); // for comments
              //if($seen=='') { $select->where('c.act_date >?', $latseen); };
            //  if($ondbdetail !='') { $select->where('c.act_typeId !=?', $ondbdetail); };
          } 
          if($latseen!='all')
          $select->where('c.act_date >?', $latseen);
          $select->where('c.act_userId  != ?',$user)      
                ->where('c.act_type    = ?', $type )->where('c.clientID = ?', clientID)
                  ->order('c.act_date DESC');  
          if($latseen!='all')
          $select->limit(10 );  
          

          $results  = $this->_db->fetchAll($select);
         
        
          if(count($results)>0)
          {
              $alldbs   = array();
              $counter  = 0;
               
              foreach ($results as $key => $value) 
              {
                  $chkread  = $this->chkdborcomment($value['act_id'],$user); 
                  if($chkread==0)
                  {
                      if($value['act_typeId']!=$ondbdetail)
                      {
                        $alldbs[$counter]['act_id']     = $value['act_id'];
                        $alldbs[$counter]['UserID']     = $value['UserID'];
                        $alldbs[$counter]['Name']       = $value['Name']; 
                        $alldbs[$counter]['lname']      = $value['lname'];                     
                        $alldbs[$counter]['ProfilePic'] = $value['ProfilePic'];
                        $alldbs[$counter]['act_message']= $value['act_message'];
                        $alldbs[$counter]['act_typeId'] = $value['act_typeId'];
                        $alldbs[$counter]['act_date']   = $value['act_date'];
                        $alldbs[$counter]['Username']   = $value['Username'];
                      }
                      else
                      {
                        $alldbs[$counter]['act_id']     = $value['act_id'];
                        $alldbs[$counter]['UserID']     = $value['UserID'];
                        $alldbs[$counter]['Name']       = $value['Name']; 
                        $alldbs[$counter]['lname']      = $value['lname'];                     
                        $alldbs[$counter]['ProfilePic'] = $value['ProfilePic'];
                        $alldbs[$counter]['act_message']= $value['act_message'];
                        $alldbs[$counter]['act_typeId'] = $value['act_typeId'];
                        $alldbs[$counter]['act_date']   = $value['act_date'];
                        $alldbs[$counter]['Username']   = $value['Username'];
                      }
                      if($ondbdetail!=0 || $seen=='seen'){
                         $act_date=  date('Y-m-d H:i:s'); 
                          $data    =  array(
                         'act_id'     => $value['act_id'],
                         'chk_user'   => $user,
                         'chk_db'     => $value['act_typeId'],
                         'chk_date'   => $act_date, 
                         'clientID'   => clientID,  
                        );
                        //$this->saveseenactivity($data,'tblactchecked');
                      }
                     
                     
                  } 
                  $counter++;
              }
          } 
       /* print_r($alldbs);
        exit;*/
        if($seen == 'seen') 
        {
          return $alldbs;//results;   
        } else {
          return $alldbs; 
        }  
      
       exit;

    } 

/*
    public function GetAdminApproveNotification($user,$type,$latseen='',$seen='',$ondbdetail='',$followdate='')   
    {     
        $select = $this->_db->select()->from(array(
            'c' => "tblactivity"
          ))->join(array(
              'u' => 'tblUsers'
          ), 'u.UserID = c.act_userId AND c.clientID  = u.clientID',array('u.UserID','u.Name','u.Username','u.ProfilePic'));

         
          $select->where('c.act_userId  = ?',$user);
          
         // $select->where('c.act_date >?', $latseen);
          $select->where('c.act_ownerid  = ?',adminID)      
                ->where('c.act_type    = ?', $type )->where('c.clientID = ?', clientID)
                  ->order('c.act_date DESC');  
          $results  = $this->_db->fetchAll($select);
         
        
        if(count($results)>0)
        {
            $alldbs   = array();
            $counter  = 0;
             
            foreach ($results as $key => $value) 
            {
                $chkread  = $this->chkdborcomment($value['act_id'],$user); 
                if($chkread==0)
                {
                    if($value['act_typeId']!=$ondbdetail)
                    {
                      $alldbs[$counter]['act_id']     = $value['act_id'];
                      $alldbs[$counter]['UserID']     = $value['UserID'];
                      $alldbs[$counter]['Name']       = $value['Name'];                      
                      $alldbs[$counter]['ProfilePic'] = $value['ProfilePic'];
                      $alldbs[$counter]['act_message']= $value['act_message'];
                      $alldbs[$counter]['act_typeId'] = $value['act_typeId'];
                      $alldbs[$counter]['act_date']   = $value['act_date'];
                      $alldbs[$counter]['Username']       = $value['Username'];
                    }
                    if($ondbdetail!=0 || $seen=='seen'){
                       $act_date=  date('Y-m-d H:i:s'); 
                        $data    =  array(
                       'act_id'     => $value['act_id'],
                       'chk_user'   => $user,
                       'chk_db'     => $value['act_typeId'],
                       'chk_date'   => $act_date, 
                       'clientID'   => clientID,  
                      );
                      $this->saveseenactivity($data,'tblactchecked');
                    }
                   
                   
                } 
                $counter++;
            }
        } 
       
        if($seen == 'seen') 
        {
          return $alldbs;//results;   
        } else {
          return $alldbs; 
        }  
      
       exit;

    }   
  */
    public function getdbeeNotificationLoading($follusers,$user,$type,$latseen='',$seen='',$ondbdetail='')   
    {       
        $select = $this->_db->select()->from(array(
            'c' => "tblactivity"
          ))->join(array(
              'u' => 'tblUsers'
          ), 'u.UserID = c.act_userId AND c.clientID  = u.clientID',array('u.UserID','u.Name','u.lname','u.Username','u.ProfilePic'));

          if($type == 1)  
          { 
              $select->where('c.act_userId IN(?)', $follusers); 
              $select->where('c.act_date >?', $latseen);
          }
          if($type == 2) 
          { 
              $select->where('c.act_typeId IN(?)', $follusers); // for comments all dbs id
              if($seen=='') { $select->where('c.act_date >?', $latseen); };
            //  if($ondbdetail !='') { $select->where('c.act_typeId !=?', $ondbdetail); };
          } 
          
          $select->where('c.act_userId  != ?',$user)      
			          ->where('c.act_type    = ?', $type )->where('c.clientID = ?', clientID)
			         		->order('c.act_date DESC'); 
			         
       		$results  = $this->_db->fetchAll($select);
	       
        
        if(count($results)>0)
        {
            $alldbs   = array();
            $counter  = 0;
             
            foreach ($results as $key => $value) 
            {
                $chkread  = $this->chkdborcomment($value['act_id'],$user); 
                if($chkread==0)
                {
                    if($value['act_typeId']!=$ondbdetail)
                    {
                      $alldbs[$counter]['act_id']     = $value['act_id'];
                      $alldbs[$counter]['UserID']     = $value['UserID'];
                      $alldbs[$counter]['Name']       = $value['Name']; 
                      $alldbs[$counter]['lname']      = $value['lname'];                     
                      $alldbs[$counter]['ProfilePic'] = $value['ProfilePic'];
                      $alldbs[$counter]['act_message']= $value['act_message'];
                      $alldbs[$counter]['act_typeId'] = $value['act_typeId'];
                      $alldbs[$counter]['act_date']   = $value['act_date'];
                      $alldbs[$counter]['Username']       = $value['Username'];
                    }
                    
                    if($ondbdetail!='' || $seen=='seen'){
                       $act_date=  date('Y-m-d H:i:s'); 
                        $data    =  array(
                       'act_id'     => $value['act_id'],
                       'chk_user'   => $user,
                       'chk_db'     => $value['act_typeId'],
                       'chk_date'   => $act_date, 
                       'clientID'   => clientID,  
                      );
                      $this->saveseenactivity($data,'tblactchecked');
                    }
                   
                   
                } 
                $counter++;
            }
        } 
        
        if($seen == 'seen') 
        {
          return $alldbs;//results;   
        } else {
          return $alldbs; 
        }  
      
       exit;

    }

    public function ghostdbeeNotification($follusers,$user,$type,$latseen='',$seen='',$ondbdetail='')   
    {       
        $select = $this->_db->select()->from(array(
            'c' => "tblactivity"
          ));
		       if($type == 12 || $type == 13 || $type == 47) {
			       	$select->join(array(
			       			'u' => 'tblUsers'
			       	), 'u.UserID = c.act_ownerid AND c.clientID  = u.clientID',array('u.UserID','u.Name','u.lname','u.Username','u.ProfilePic'));
			         
		        }else{
		        	$select->join(array(
		        			'u' => 'tblUsers'
		        	), 'u.UserID = c.act_userId AND c.clientID  = u.clientID',array('u.UserID','u.Name','u.lname','u.Username','u.ProfilePic'));		        		
		        }
          if($type == 1)  $select->where('c.act_userId IN(?)', $follusers);
          if($type == 2) 
          { 
              $select->where('c.act_typeId IN(?)', $follusers); // for comments
              if($ondbdetail !='') { $select->where('c.act_typeId !=?', $ondbdetail); };
              $select->where('c.act_userId  != ?',$user) ;
              
          } 
          if($type == 12 || $type == 13 || $type == 47) {
          $select->where('c.act_userId  = ?',$user) ;  
          }
         
          $select->where('c.act_date >?', $latseen); 
          $select->where('c.act_type    = ?', $type )->where('c.clientID = ?', clientID)
          ->order('c.act_date DESC'); 
         
       // echo  $select->__toString();
        $results  = $this->_db->fetchAll($select);
       
        //  exit;
        if(count($results)>0)
        {
            $alldbs   = array();
            $counter  = 0;
             
            foreach ($results as $key => $value) 
            {
                $chkread  = $this->chkdborcomment($value['act_id'],$user); 
                if($chkread==0)
                {
                    $alldbs[$counter]['act_date']       = $value['act_date'];
                    $alldbs[$counter]['userid']       = $value['act_userId'];
                    $alldbs[$counter]['Name']       = $value['Name'];
                    $alldbs[$counter]['lname']       = $value['lname'];
                    $alldbs[$counter]['act_message']= $value['act_message'];
                } 
                $counter++;
            }
        } 
        
        if($seen == 'ghost') 
        {
          return $alldbs;   
        } else {
          return $alldbs; 
        }  
      
       exit;

    }    

    public function getcommenteddbs($user,$type)   
    {      
    	$myhome = new Application_Model_Myhome();
    	//$gethiddenDbs  =    $myhome->gethiddendbee($user);
        $select = $this->_db->select()->from(array(
            'c' => "tblactivity"
        ),array('c.act_id','c.act_typeId'))
	        ->where('c.act_userId =?', $user)
		     //   ->where("c.act_typeId NOT IN(?)", $gethiddenDbs)
			        ->where('c.act_type    = ?', $type )->where('c.clientID = ?', clientID)
			       		->order('c.act_date DESC');      

        $results  = $this->_db->fetchAll($select);

        $act_id   = $results[0]['act_id']; 

        $alldbs   = array();
        $counter  = 0;

        foreach ($results as $key => $value) 
        {
             $alldbs[$counter][] = $value['act_typeId'];
             $counter++;
        }

        return $alldbs;   
        exit;

    }   

    public function chkdborcomment($act_id,$user)
    {
        $select = $this->_db->select()->from(array(
            'c' => "tblactchecked"
        ))
	        ->where('c.chk_user =?', $user)
	        	->where('c.act_id    = ?', $act_id )->where('c.clientID = ?', clientID); 

        $results = $this->_db->fetchAll($select);
        return count($results);
    }
    
    public function getactivitygroupnotify($offset,$userid,$type)
    {    	
    	  $select = $this->_db->select();
	    	$select->from(array('c' => $this->_name))
	    		->join(array('u' => $this->getTableName(USERS)), 'c.act_ownerid  = u.UserID AND c.clientID  = u.clientID',array('u.UserID','u.Name','u.lname','u.Username','u.usertype','u.ProfilePic'))
	    			->join(array('g' => $this->getTableName(GROUP)), 'c.act_typeId  = g.ID',array('g.GroupName','g.GroupPic','g.GroupPrivacy'))
		    			->join(array('gm' => $this->getTableName(GROUP_MEMBER)), 'g.ID  = gm.GroupID',array('gm.GroupID','gm.ID'))->where('c.act_message !=?', 29)->where('c.act_type= ?',$type);
                       if($type==12){ $select = $select->where('gm.User =?', $userid);}
							    			 $select = $select->where('c.act_userId =?', $userid)->where('c.clientID = ?', clientID)
							    				 ->group('g.ID')
										    		 ->order('c.act_date Desc')
										    			->limit(PAGE_NUM_LEAGE,$offset);
							    			//echo  $select->__toString();
							    			// exit;
  		$result = $this->_db->fetchAll($select);
  		return $result;				     
    }
    public function getprivatedgroupdbee($offset,$userid,$type)
    {
    	$select = $this->_db->select();
    	$select->from(array('c' => $this->_name))
	    	->join(array('u' => $this->getTableName(USERS)), 'c.act_ownerid  = u.UserID AND c.clientID  = u.clientID',array('u.UserID','u.Name','u.lname','u.Username','u.usertype','u.ProfilePic'))
		    	->join(array('d' => 'tblDbees'), 'c.act_typeId  = d.DbeeID',array('d.Type','d.VidID','d.Text','d.Pic','d.LinkPic','d.PicDesc','d.VidDesc','d.PollText','d.events','d.LinkTitle','d.UserLinkDesc'))    	
			    	->join(array('g' => $this->getTableName(GROUP)), 'd.GroupID  = g.ID',array('g.GroupName','g.GroupPic','g.GroupPrivacy'))
              ->where('c.act_userId  =?', $userid)->where('c.act_type  =?', $type)
  				    	->order('c.act_date Desc')
  				    		->limit(PAGE_NUM_LEAGE,$offset);                
    	$result = $this->_db->fetchAll($select);
    	return $result;
    }

    public function geteventpostdbee($offset,$userid,$type)
    {
      $select = $this->_db->select();
      $select->from(array('c' => $this->_name))
        ->join(array('u' => $this->getTableName(USERS)), 'c.act_ownerid  = u.UserID AND c.clientID  = u.clientID',array('u.UserID','u.Name','u.lname','u.Username','u.usertype','u.ProfilePic'))
          ->join(array('d' => 'tblDbees'), 'c.act_typeId  = d.DbeeID',array('d.Type','d.VidID','d.Text','d.Pic','d.LinkPic','d.PicDesc','d.PollText','d.events','d.LinkTitle','d.UserLinkDesc'))            
              ->where('c.act_userId  =?', $userid)->where('c.act_type  =?', $type)
                ->order('c.act_date Desc')
                  ->limit(PAGE_NUM_LEAGE,$offset);      
      $result = $this->_db->fetchAll($select);
      return $result;
    }
 

    public function privatepostgrouptot($userid,$type,$lastseen)
    {
    	$select = $this->_db->select();
    	$select->from(array('c' => $this->_name))
	    	->join(array('u' => $this->getTableName(USERS)), 'c.act_ownerid  = u.UserID AND c.clientID  = u.clientID',array('u.UserID','u.Name','u.lname','u.Username','u.usertype','u.ProfilePic'))
		    	->join(array('d' => 'tblDbees'), 'c.act_typeId  = d.DbeeID',array('d.Type','d.VidID','d.Text','d.Pic','d.LinkPic','d.PicDesc','d.VidDesc','d.PollText','d.events','d.LinkTitle','d.UserLinkDesc'))    		 
		    		->where('c.act_userId  =?', $userid)->where('c.act_type  =?', $type)->where('c.act_date >?', $lastseen);      	 
    	$result = $this->_db->fetchAll($select);
    	return $result;
    }
    public function groupaccept($offset,$userid,$type)
    {
    	$select = $this->_db->select();
    	$select->from(array('c' => $this->_name))
    	->join(array('u' => $this->getTableName(USERS)), 'c.act_ownerid  = u.UserID AND c.clientID  = u.clientID',array('u.UserID','u.Name','u.lname','u.Username','u.usertype','u.ProfilePic'))
    	->join(array('g' => $this->getTableName(GROUP)), 'c.act_typeId  = g.ID',array('g.GroupName','g.GroupPic','g.GroupPrivacy'));
    	//->join(array('gm' => $this->getTableName(GROUP_MEMBER)), 'g.ID  = gm.GroupID',array('gm.GroupID','gm.ID'));
    	
    	$select = $select->where('c.act_userId  =?', $userid)->where('c.act_type  =?', $type)->where('c.act_status =?', 0)->where('c.clientID = ?', clientID)
    	->order('c.act_date Desc')
    	->limit(PAGE_NUM_LEAGE,$offset);
    	//echo  $select->__toString();
    	// exit;
    	$result = $this->_db->fetchAll($select);
    	return $result;
    }
  
    public function getactivityexpert($offset,$userid,$type)
    {
    	$select = $this->_db->select()
    	->from(array('c' => $this->_name))
    		->join(array('u' => $this->getTableName(USERS)), 'c.act_userId  = u.UserID AND c.clientID  = u.clientID',array('u.UserID','u.Name','u.lname','u.Username','u.usertype','u.ProfilePic'))
    			->join(array('q' => $this->getTableName(QNA)), 'c.act_cmnt_id  = q.id')
    					->where("c.act_type=11")
    						->where('c.act_ownerid =?', $userid)->where('c.clientID = ?', clientID)
    									->order('c.act_date Desc')
    											->limit(PAGE_NUM_LEAGE,$offset);   
    	return $this->_db->fetchAll($select);
    }
    
    
    public function getactivitygroupnotifytotal($offset,$userid,$type)
    {
    	$select = $this->_db->select()
	    	->from(array('c' => $this->_name))
	    		->join(array('u' => $this->getTableName(USERS)), 'c.act_ownerid  = u.UserID AND c.clientID  = u.clientID',array('u.UserID','u.Name','u.lname','u.Username','u.ProfilePic'))
	    			->join(array('g' => $this->getTableName(GROUP)), 'c.act_typeId  = g.ID',array('g.GroupName','g.GroupPic'))
		    			->join(array('gm' => $this->getTableName(GROUP_MEMBER)), 'g.ID  = gm.GroupID',array('gm.GroupID','gm.ID'))					    		
						    		->where("c.act_type=12" OR "c.act_type=13" OR "c.act_type=20")
						    			 ->where('c.act_userId =?', $userid)->where('c.clientID = ?', clientID)
						    				 ->group('g.ID')
									    		 ->order('c.act_date Desc')
									    			->limit(PAGE_NUM_LEAGE,$offset);
    	
    
		$result = $this->_db->fetchAll($select);
    	return count($results);
    }
    
    
    public function getgroupnotify($userid,$type)
    {
    	$select = $this->_db->select()
	    	->from(array('c' => $this->_name))
	    		->join(array('u' => $this->getTableName(USERS)), 'c.act_userId  = u.UserID AND c.clientID  = u.clientID')
	    			->join(array('g' => $this->getTableName(GROUP)), 'c.act_typeId  = g.ID',array('g.GroupName','g.GroupPic','g.ID'))
				    	->where('c.act_userId =?', $userid)->where('c.clientID = ?', clientID)
				    		->where('c.act_type = ?','12');
				    			
    	$result = $this->_db->fetchAll($select);
    	return $result;
    }    
	
    function getgroupnotigymsg($userid,$type,$lastseen)
    {    	
    
    	$select = $this->_db->select()
	    	->from(array('c' => $this->_name))
		    	->join(array('u' => $this->getTableName(USERS)), 'c.act_ownerid  = u.UserID AND c.clientID  = u.clientID',array('u.UserID','u.Name','u.lname','u.Username','u.ProfilePic'))
			    	->join(array('g' => $this->getTableName(GROUP)), 'c.act_typeId  = g.ID',array('g.GroupName','g.GroupPic'))
				    	->join(array('gm' => $this->getTableName(GROUP_MEMBER)), 'g.ID  = gm.GroupID',array('gm.GroupID','gm.ID'))
					    	//->where("c.act_type=?",$type)
              ->where("c.act_type=12" OR "c.act_type=13")
						    	->where('c.act_date >?', $lastseen)
							    	->where('c.act_userId =?', $userid)->where('c.clientID = ?', clientID)		
								    	->group('g.ID')
									    	->order('c.act_date Desc');
    	
    	$result = $this->_db->fetchAll($select);
    	return $result;
    }
    
    function getgroupnotigymsgtot($userid,$type,$lastseen)
    {
    	$select = $this->_db->select()
	    	->from(array('c' => $this->_name))
		    	->join(array('u' => $this->getTableName(USERS)), 'c.act_ownerid  = u.UserID AND c.clientID  = u.clientID',array('u.UserID','u.Name','u.lname','u.Username','u.ProfilePic'))
			    	->join(array('g' => $this->getTableName(GROUP)), 'c.act_typeId  = g.ID',array('g.GroupName','g.GroupPic'))
				    	->join(array('gm' => $this->getTableName(GROUP_MEMBER)), 'g.ID  = gm.GroupID',array('gm.GroupID','gm.ID'))
					    	->where("c.act_type=?",$type)
						    	->where('c.act_date >?', $lastseen)
							    	->where('c.act_userId =?', $userid)->where('c.clientID = ?', clientID)
								    	->group('g.ID')
								    		->order('c.act_date Desc');
    
    	$result = $this->_db->fetchAll($select);
    	return $result;
    }
    
    public function getactivityleaguenotify($offset,$userid,$lastseen='')
    {
    	$select = $this->_db->select()
	    	->from(array('c' => $this->_name))
	    	->distinct('c.act_id')
		   		->joinInner(array('u' => $this->getTableName(USERS)), 'c.act_userId  = u.UserID AND c.clientID  = u.clientID',array('u.UserID','u.Name','u.lname','u.Username','u.usertype','u.ProfilePic'))
				   	->joinInner(array('l' => $this->getTableName(LEAGUE)), 'c.act_typeId  = l.LID')     	
					    	->where("c.act_type=14")    	
						    	->where('c.act_ownerid =?', $userid)					    	
							    	->where('c.act_date >?', $lastseen)->where('c.clientID = ?', clientID)   						
								    	->order('c.act_date Desc')
								    		->limit(PAGE_NUM_LEAGE,$offset);
							    	
    	$result = $this->_db->fetchAll($select);
    	return $result;
    }
    
    public function getactivityleaguenotifytotal($offset,$userid,$lastseen)
    {
    	$select = $this->_db->select()
	    	->from(array('c' => $this->_name))
	    	->distinct('c.act_id')
		   		->joinInner(array('u' => $this->getTableName(USERS)), 'c.act_userId  = u.UserID AND c.clientID  = u.clientID',array('u.UserID','u.Name','u.lname','u.Username','u.ProfilePic'))
			   	->joinInner(array('l' => $this->getTableName(LEAGUE)), 'c.act_type  = l.LID')    	
				    	->where("c.act_type=14")    	
					    	->where('c.act_ownerid =?', $userid);					    	
						    	$select->where('c.act_date >?', $lastseen)->where('c.clientID = ?', clientID);    						
							    	$select->order('c.act_date Desc');							    
							    	
    	$result = $this->_db->fetchAll($select);
    	return $result;
    }

    
   /* public function delactivity($id,$uid)
    {
        $this->_db->delete($this->_name, array(
            "act_typeId='" . $id . "'","act_ownerid='" . $id . "'"
        ));
       return true;
       
    }*/
    
}
