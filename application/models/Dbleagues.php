<?php



class Application_Model_Dbleagues extends Application_Model_DbTable_Master

{

    protected $_name = null; 
    protected function _setupTableName()
    {
              parent::_setupTableName();		
		      $this->_name = $this->getTableName(GROUP);
              $storage    = new Zend_Auth_Storage_Session(); 
            $data      = $storage->read(); 
            $this->session_data = $data;     
    }
    public function privateuserleague()
    {
    	$select = $this->_db->select()
    		->from($this->getTableName(GROUP),'ID')
    			->where('GroupPrivacy = ?','2');
    	$result = $this->_db->fetchAll($select);
    	foreach($result as $data):
    		$useid[] = $data['ID'];
    	endforeach;
    	return $useid;
    } 

	public function getleauebyscore($league,$user=0,$start=0,$commusers=0,$db=0,$score)
    {

    	$select = $this->_db->select()

    	->distinct()

    		->from(array('s' => $this->getTableName(SCORING)),array('Score','Owner','MainDB','ID','Type','TotalScore'=>"count(*)"))

		    	->join(array('u' => $this->getTableName(USERS)), 'u.UserID = s.Owner')
                    ->where('u.clientID = ?',clientID)
			    	->where('u.ScoringStatus = ?','1')
				    	->where('u.Status = ?','1')
				    		->where('s.Score = ?',$score);    	

    	if($commusers!=0){

    		$select->where('s.MainDB = ?',$db);

    		$select->where('u.UserID IN(?)',$commusers);

    	}

    	$select->group('s.Owner');

       	//$select->order(array('Scorecnt Desc'));

    	$result = $this->_db->fetchAll($select);

		return $result;

    }

    public function getleauebyscore2($league,$user=0,$start=0,$commusers=0,$db=0,$score)

    {

    	$select = $this->_db->select()

    	->distinct()

    	->from(array('s' => $this->getTableName(SCORING)),array('Score','Owner','MainDB','ID','Type','TotalScore'=>"count(*)*5"))

    	->join(array('u' => $this->getTableName(USERS)), 'u.UserID = s.Owner')
        ->where('u.clientID = ?',clientID)
    	->where('u.ScoringStatus = ?','1')

    	->where('u.Status = ?','1')

    	->where('s.Score = ?',$score);

    	if($commusers!=0){

    		$select->where('s.MainDB = ?',$db);

    		$select->where('u.UserID IN(?)',$commusers);

    	}

    	$select->group('s.Owner');

    	//$select->order(array('Scorecnt Desc'));

    	$result = $this->_db->fetchAll($select);

    	return $result;

    }

    public function topthreedata($dbeeid,$privategroup)

    {    	

    	$select = $this->_db->select()

    		->from(array($this->getTableName(DBEE)))

    			->where('User = ?',$dbeeid)
                 ->where('clientID = ?',clientID);
    				if(count($PrivateGroups)>0){

			    	$select->where($GroupID.' NOT IN(?)', $PrivateGroups);}

			    		$select->order('PostDate Desc')

			    			->limit(1);					    	

    	$result = $this->_db->fetchRow($select);

		return $result;

    }

    

    public function mycustomdata($limit,$start)
    {
    	$Offset = (int)$start;
    	$limit1 = (int)$limit;    	
    	$select = $this->_db->select()    		
	    	->from(array('c' => $this->getTableName(DBEE)))
		    	->join(array('u' => $this->getTableName(USERS)), 'u.UserID = c.User AND u.clientID = c.clientID',array('Name','Username','Status','usertype','ProfilePic'))
			    	->join(array('d' => $this->getTableName(COMMENT)), 'd.DbeeID = c.DbeeID AND d.clientID = c.clientID',array('CommentDate','cnt'=> 'count(*)'))
                        ->where('u.clientID = ?',clientID)
				    	->group('c.DbeeID')
					    	->order('cnt Desc')						    	
						    		->limit($limit1,$Offset);       	
    	$result = $this->_db->fetchAll($select);
        //echo $select->__toString();die;
		return $result;

    }    

    public function  getfollowdata($limit,$start)

    {

    	$Offset = (int)$start;

    	$limit1 = (int)$limit;

    	$select = $this->_db->select()

	    	->from(array('f' => $this->getTableName(FOLLOWS)))

		    	->join(array('u' => $this->getTableName(USERS)), 'u.UserID = f.User AND u.clientID = f.clientID',array('Name','Username','usertype','ProfilePic','Total'=> 'count(*)'))
                    ->where('u.clientID = ?',clientID)
			    	->group(array('f.User'))

			    		->order('Total DESC')

				    		->limit($limit1,$Offset);      	

    	$result = $this->_db->fetchAll($select);
       
		return $result;

    }

    public function getdbeedata($user,$PrivateGroups)

    {

    	$select = $this->_db->select();

	    $select->from($this->getTableName(DBEE));

		    	if(count($PrivateGroups)>0){

		    		$select->where($GroupID.' NOT IN(?)', $PrivateGroups);

		    	}
		    	$select->where('User='.$user)->where('clientID='.clientID)

		    		->limit(10);

    	$result = $this->_db->fetchRow($select);

		return $result;

    }    

    public function getleague($league,$user)

    {

    	$id  = (int) $id;

    	$select = $this->_db->select()

    		->from(array('c' => $this->getTableName(DBEE)))

    			->join(array('d' => $this->getTableName(USERS)), 'c.User = d.UserID AND c.clientID = d.clientID','*','left')
                    ->where('d.clientID = ?',clientID)
			    	->where('d.UserID='.$user)

			    		->order(array('c.PostDate Desc'));

    	$result = $this->_db->fetchAll($select);

		return $result;

    }    

  

    

	public function getscoreleaguetable($league,$user=0,$start=0,$commusers=0,$db=0,$score)

    {				

    	$select = $this->_db->select()

    	->distinct()

    		->from(array('s' => $this->getTableName(SCORING)),array('Score','Owner','MainDB','ID','Type','TotalScore'=>"count(*)*5"))

		    	->join(array('u' => $this->getTableName(USERS)), 'u.UserID = s.Owner AND u.clientID = s.clientID')
                    ->where('u.clientID = ?',clientID)
			    	->where('u.ScoringStatus = ?','1')

				    	->where('u.Status = ?','1')

				    		->where('s.Score = ?',$score);    	

    	if($commusers!=0){

    		$select->where('s.MainDB = ?',$db);

    		$select->where('u.UserID IN(?)',$commusers);

    	}

    	$select->group('s.Owner');

       	$select->order('TotalScore Desc');

      // 	echo $select->__toString();

    	$result = $this->_db->fetchAll($select);

		return $result;

    }

    

    public function getscoreleaguetabletwo($league,$user=0,$start=0,$commusers=0,$db=0,$score)

    {

    	$select = $this->_db->select()

	    	->distinct()

		    	->from(array('s' => $this->getTableName(SCORING)),array('Score','Owner','MainDB','ID','Type','TotalScore'=>"count(*)"))

			    	->join(array('u' => $this->getTableName(USERS)), 'u.UserID = s.Owner AND u.clientID = s.clientID')
                       ->where('u.clientID = ?',clientID)
				    	->where('u.ScoringStatus = ?','1')

					    	->where('u.Status = ?','1')

						    	->where('s.Score = ?',$score);

							    	if($commusers!=0){

							    		$select->where('s.MainDB = ?',$db);

							    		$select->where('u.UserID IN(?)',$commusers);

							    	}

							    	$select->group('s.Owner');

							    	$select->order('TotalScore Desc');

    	$result = $this->_db->fetchAll($select);

    	return $result;

    }  
    public function getfollowingandfollowers($cookieuser,$isadmin='')
    {
        $select = $this->_db->select();
    
        $select->from(array('f' => $this->getTableName(FOLLOWS)),array('FollowedBy','User'));
    
        $select->join(array('u' => $this->getTableName(USERS)), 'f.FollowedBy = u.UserID AND f.clientID = u.clientID','');
    
        $select->where("f.User =?", $cookieuser)->orwhere("f.FollowedBy =?", $cookieuser)->where('f.clientID = ?',clientID);
       
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

        $result = $this->_db->fetchAll($select);
    
        $allinone = array();
        foreach ($result as $key => $value) {
            $allinone[] = $value['FollowedBy'];
            $allinone[] = $value['User'];
        }
        return array_unique($allinone);
    }
    public function getusersnotinfollow($keyword,$allfollows='',$isadmin='')
    {     
        if(count($allfollows)>0)
        {
            $ids     = join(',',$allfollows);
            $incondi = 'UserID NOT IN( '. $ids  .')';
        } else  $incondi = '1=1';
      
      $select = $this->_db->select()        
            ->from('tblUsers',array('UserID','Name','lname','usertype','username','Email','ProfilePic')) 
                ->where('clientID = ?',clientID)
                ->where('Status = ?',1)                     
                ->where('Name LIKE  "%'.$keyword.'%" OR full_name LIKE "%'.$keyword.'%" OR Username LIKE  "%'.$keyword.'%"' )->where($incondi ) ;
                
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
                //->orWhere('full_name LIKE "%$keyword%" ')
               // ->orWhere('Username LIKE ?', "%$keyword%");
                //->where($incondi ) ;     
        $result = $this->_db->fetchAll($select);        
        return $result;             
    }
	public function getusers($keyword)
    {        
        $select = $this->_db->select()        
        	->from('tblUsers') 
                ->where('clientID = ?',clientID)       
       			 ->where('Name LIKE ?', "%$keyword%");        
        $result = $this->_db->fetchAll($select);        
        return $result;             
    }
    public function ChkFollowerNum($cookieuser, $UserID)
    {    
    	$select = $this->_db->select()    
	    	->from('tblFollows') 
            ->where('clientID = ?',clientID)   
	    		->where("User =?", $cookieuser)    
	    			->where("FollowedBy =?",$UserID);	    
    	$result = $this->_db->fetchAll($select);    
    	return $result;    
    }
    
    public function getfollowersleaguetypes($cookieuser,$limit='',$Offset='')
    {
    
    	$select = $this->_db->select();
    
    	$select->from(array(
    			'f' => $this->getTableName(FOLLOWS)
    	));
    
    	$select->join(array(
    			'u' => $this->getTableName(USERS)
    	), 'f.FollowedBy = u.UserID AND f.clientID = u.clientID');
    
    	$select->where("f.User =?", $cookieuser)->where('f.clientID = ?',clientID);

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
        //echo $select->__toString();
         if($limit!='') $select->limit($limit,$Offset);
   
    	$result = $this->_db->fetchAll($select);
    
    	return $result;
    }
    public function getfollowingleaguetypes($cookieuser,$limit='',$Offset='')
    {
    
    	$select = $this->_db->select();
    
    	$select->from(array(
    			'f' => $this->getTableName(FOLLOWS)
    	));
    
    	$select->join(array(
    			'u' => $this->getTableName(USERS)
    	), 'f.User = u.UserID AND f.clientID = u.clientID',array('u.Name','u.lname','u.Email','u.UserID','u.usertype','u.ProfilePic'));
    
    	$select->where("f.FollowedBy =?", $cookieuser)->where('f.clientID = ?',clientID);

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

        //echo $select->__toString();
    	
    	$result = $this->_db->fetchAll($select);
    
    	return $result;
    
    }
    public function getallusers($users)
    {   
    	$select = $this->_db->select()    
	    	->from($this->getTableName(USERS)) 	    	
	    	 		->where('clientID = ?',clientID)->where("UserID IN(?)",$users);
    	//echo $select->__toString();
    	$result = $this->_db->fetchAll($select);
    
    	return $result;
    
    }
    public function listleague($users,$curdate)
    
    {
    	$select = $this->_db->select()
    		->from($this->getTableName(LEAGUE))
     			->where("UserID =?",$users) 
    			   ->where("EndDate >= ?",$curdate)->where("clientID = ?",clientID)
  						->order("StartDate DESC");
    	//echo $select->__toString();
    	$result = $this->_db->fetchAll($select);
    
    	return $result;
    
    }
    
  
}	