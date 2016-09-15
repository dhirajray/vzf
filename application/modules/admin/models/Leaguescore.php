<?php

class Admin_Model_Leaguescore  extends Zend_Db_Table_Abstract
{
     // Table name 
     protected $_name = 'tblScoring';
     
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

    public function init()
    {
    	$this->myclientdetails = new Admin_Model_Clientdetails();
	}
	/* Insert special dbs */


	public function insertdata_global($table,$data){
		
		$insertdb = $this->_db->insert($table, $data);
        if($insertdb) return $this->_db->lastInsertId();
      	else return false; 
	}

	public function lovelikePaging($postid='',$total='')
	{
		$db = $this->getDbTable();
		$select = $db->select()->setIntegrityCheck( false );
		$select->from(array('a'=>'tblScoring'),array('likes'=>'SUM(IF(a.Score= 2,1,0))','love'=>'SUM(IF(a.Score= 1,3,0))','total'=>'(SUM(IF(a.Score= 1,3,0))+SUM(IF(a.Score= 2,1,0)))','a.ParentType','a.Owner','a.UserID','u.Name','u.lname','u.ProfilePic'));
		$select->joInInner(array('u'=>'tblUsers'),'u.UserID=a.Owner');
		$select->joInInner(array('c'=>'tblDbees'),'c.DbeeID =a.MainDB ');
		$select->where('a.clientID= ?',clientID);
		if($postid!="")
		{
			if($type==0)
			{
				$select->where('a.MainDB= ?',$postid);
		    }
		    if($type==1)
			{
				$select->where('a.ParentId= ?',$postid);
		    }
		    if($type==2)
			{
				$select->where('a.ParentId= ?',$postid);
		    }
		}
		
		if($total!="")
		{

		 $select->having('(SUM(IF(a.Score= 1,3,0))+SUM(IF(a.Score= 2,1,0))) < ?',$total);
	    }
	    $select->having('(SUM(IF(a.Score= 1,3,0))+SUM(IF(a.Score= 2,1,0))) != ?', '0');

		$select->group('a.Owner');
		$select->order('total DESC');
		$select->limit('10');
		//echo $select->__toString();	
		//die;			
		return $this->getDefaultAdapter()->query($select)->fetchAll();
	}

	public function hatedislikePaging($postid='',$total='')
	{
		$db = $this->getDbTable();
		$select = $db->select()->setIntegrityCheck( false );
		$select->from(array('a'=>'tblScoring'),array('hate'=>'SUM(IF(a.Score= 5,3,0))','dislike'=>'SUM(IF(a.Score= 4,1,0))','total'=>'(SUM(IF(a.Score= 5,3,0))+SUM(IF(a.Score= 4,1,0)))','a.ParentType','a.Owner','a.UserID','u.Name','u.lname','u.ProfilePic'));
		$select->joInInner(array('u'=>'tblUsers'),'u.UserID=a.Owner');
		$select->joInInner(array('c'=>'tblDbees'),'c.DbeeID =a.MainDB ');
		$select->where('a.clientID= ?',clientID);
		if($postid!="")
		{
			if($type==0)
			{
				$select->where('a.MainDB= ?',$postid);
		    }
		    if($type==1)
			{
				$select->where('a.ParentId= ?',$postid);
		    }
		    if($type==2)
			{
				$select->where('a.ParentId= ?',$postid);
		    }
		}

		if($total!="")
		{

		 $select->having('(SUM(IF(a.Score= 5,3,0))+SUM(IF(a.Score= 4,1,0))) < ?',$total);
	    }
	    $select->having('(SUM(IF(a.Score= 5,3,0))+SUM(IF(a.Score= 4,1,0))) != ?', '0');
		$select->group('a.Owner');
		$select->order('total DESC');
		$select->limit('10');
		//$select->limit('2');
		//echo $select->__toString();	
		//die;			
		return $this->getDefaultAdapter()->query($select)->fetchAll();
	}

	public function ScoreUser($score,$whois,$text="",$limit='',$postid="",$type="")
	{
		
		if($whois==0){
			$whoistext='UserID';
		}
		else
		{
		   $whoistext='Owner';
		}
		if($type==5)
		{
		  $type="";
		}

		$db = $this->getDbTable();
		$select = $db->select()->setIntegrityCheck( false );
		
		$select->from(array('a'=>'tblScoring'),array('total'=>'COUNT(a.ScoreID)','dbuser'=>'c.User','a.ParentType','a.'.$whoistext.'','u.UserID','u.Name','u.lname','u.ProfilePic'));
		$select->joInInner(array('u'=>'tblUsers'),'u.UserID=a.'.$whoistext.'');
		$select->joInInner(array('c'=>'tblDbees'),'c.DbeeID =a.MainDB ');
		$select->where('a.clientID= ?',clientID);
		$select->where('a.Score= ?',$score);
		if($text!="")
        {
         $select->where("u.full_name LIKE '%$text%' OR u.Name LIKE '%$text%' OR u.Username LIKE '%$text%'");
        }
        if($postid!="")
		{
			if($type==0)
			{
				$select->where('a.MainDB= ?',$postid);
		    }
		    if($type==1)
			{
				$select->where('a.ParentId= ?',$postid);
		    }
		    if($type==2)
			{
				$select->where('a.ParentId= ?',$postid);
		    }
		}	

		if($type!="")
		{
		  $select->where('a.ParentType= ?',$type);
		}
        $select->group('a.'.$whoistext.'');
        if($limit!="")	
		{
			$select->having('COUNT(a.ScoreID) < ?',$limit);
		}		
		
		$select->order('total DESC');
		$select->limit('20');
		//$select->limit('2');		
		//echo $select->__toString(); die;
		return $this->getDefaultAdapter()->query($select)->fetchAll();
	}

	public function ScoreUserCount($score,$whois,$text="",$limit='',$postid="",$type="")
	{
		
		if($whois==0){
			$whoistext='UserID';
		}
		else
		{
		   $whoistext='Owner';
		}

		if($type==5)
		{
		  $type="";
		}

		$db = $this->getDbTable();
		$select = $db->select()->setIntegrityCheck( false );
		
		$select->from(array('a'=>'tblScoring'),array('total'=>'COUNT(a.ScoreID)'));
		$select->joInInner(array('u'=>'tblUsers'),'u.UserID=a.'.$whoistext.'');
		$select->joInInner(array('c'=>'tblDbees'),'c.DbeeID =a.MainDB ');
		$select->where('a.clientID= ?',clientID);
		$select->where('a.Score= ?',$score);
		if($text!="")
        {
         $select->where("u.full_name LIKE '%$text%' OR u.Name LIKE '%$text%' OR u.Username LIKE '%$text%'");
        }
        if($postid!="")
		{
			if($type==0)
			{
				$select->where('a.MainDB= ?',$postid);
		    }
		    if($type==1)
			{
				$select->where('a.ParentId= ?',$postid);
		    }
		    if($type==2)
			{
				$select->where('a.ParentId= ?',$postid);
		    }
		}	

		if($type!="")
		{
		  $select->where('a.ParentType= ?',$type);
		}
				
		
		$select->group('a.'.$whoistext.'');
		$select->order('total DESC');
		
		//$select->limit('2');		
		//echo $select->__toString(); die;
		return $this->getDefaultAdapter()->query($select)->fetchAll();
	}


	public function lovelikeScoreUser($postid='',$type='')
	{
		$db = $this->getDbTable();
		$select = $db->select()->setIntegrityCheck( false );
		$select->from(array('a'=>'tblScoring'),array('likes'=>'SUM(IF(a.Score= 1,3,0))','love'=>'SUM(IF(a.Score= 2,1,0))','total'=>'(SUM(IF(a.Score= 1,3,0))+SUM(IF(a.Score= 2,1,0)))','dbuser'=>'c.User','a.ParentType','a.Owner','a.UserID','u.Name','u.lname','u.ProfilePic'));
		$select->joInInner(array('u'=>'tblUsers'),'u.UserID=a.Owner');
		$select->joInInner(array('c'=>'tblDbees'),'c.DbeeID =a.MainDB ');
		$select->where('a.clientID= ?',clientID);
		
		if($postid!="")
		{
			if($type==0)
			{
				$select->where('a.MainDB= ?',$postid);
		    }
		    if($type==1)
			{
				$select->where('a.ParentId= ?',$postid);
		    }
		    if($type==2)
			{
				$select->where('a.ParentId= ?',$postid);
		    }
		}	

		if($type!="")
		{
		  $select->where('a.ParentType= ?',$type);
		}

		$select->having('(SUM(IF(a.Score= 1,3,0))+SUM(IF(a.Score= 2,1,0))) != ?', '0');
		
		$select->group('a.Owner');
		$select->order('total DESC');
		$select->limit('20');
		//$select->limit('2');		
		//echo $select->__toString();
		return $this->getDefaultAdapter()->query($select)->fetchAll();
	}


	public function HateDislikeScoreUser($postid='',$type='')
	{

		$db = $this->getDbTable();
		$select = $db->select()->setIntegrityCheck( false );
		$select->from(array('a'=>'tblScoring'),array('hate'=>'SUM(IF(a.Score= 5,3,0))','dislike'=>'SUM(IF(a.Score= 4,1,0))','total'=>'(SUM(IF(a.Score= 5,3,0))+SUM(IF(a.Score= 4,1,0)))','dbuser'=>'c.User','a.ParentType','a.Owner','a.UserID','u.Name','u.lname','u.ProfilePic'));
		$select->joInInner(array('u'=>'tblUsers'),'u.UserID=a.Owner');
		$select->joInInner(array('c'=>'tblDbees'),'c.DbeeID =a.MainDB ');
		$select->where('a.clientID= ?',clientID);
		if($postid!="")
		{
			if($type==0)
			{
				$select->where('a.MainDB= ?',$postid);
		    }
		    if($type==1)
			{
				$select->where('a.ParentId= ?',$postid);
		    }
		    if($type==2)
			{
				$select->where('a.ParentId= ?',$postid);
		    }
		}

		if($type!="")
		{
		  $select->where('a.ParentType= ?',$type);
		}
		
		$select->having('(SUM(IF(a.Score= 5,3,0))+SUM(IF(a.Score= 4,1,0))) != ?', '0');

		$select->group('a.Owner');
		$select->order('total DESC');
		$select->limit('20');
		//$select->limit('2');		
		//echo $select->__toString();
		return $this->getDefaultAdapter()->query($select)->fetchAll();
	}
	

	//scoring section

	public function FilterResultScorePost($type='')
	{	
		$db = $this->getDbTable();
		$select = $db->select()->setIntegrityCheck( false );
		$select->from(array('a'=>'tblScoring'),array('total'=>'count(*)','text'=>'c.Text','id'=>'c.DbeeID'));
		$select->joInInner(array('u'=>'tblUsers'),'u.UserID=a.Owner');
		$select->joInInner(array('c'=>'tblDbees'),'c.DbeeID =a.MainDB ');
		$select->where('a.clientID= ?',clientID);		
		$select->where('a.ParentType= ?',0);		
		//$select->where('a.ArticleType= ?',1);
		$select->group('c.Text');
		$select->order('total DESC');		
		return $this->getDefaultAdapter()->query($select)->fetchAll();
	}
	public function FilterResultScoreGroup($type='')
	{	
		$db = $this->getDbTable();
		$select = $db->select()->setIntegrityCheck( false );
		$select->from(array('a'=>'tblScoring'),array('total'=>'count(*)','text'=>'c.GroupName','id'=>'c.ID'));
		$select->joInInner(array('u'=>'tblUsers'),'u.UserID=a.Owner');
		$select->joInInner(array('c'=>'tblGroups'),'c.ID=a.ParentId');
		$select->where('a.clientID= ?',clientID);		
		$select->where('a.ParentType= ?',1);		
		//$select->where('a.ArticleType= ?',1);
		$select->group('c.GroupName');
		$select->order('total DESC');		
		return $this->getDefaultAdapter()->query($select)->fetchAll();
	}

	public function FilterResultScoreEvent($type='')
	{	
		$db = $this->getDbTable();
		$select = $db->select()->setIntegrityCheck( false );
		$select->from(array('a'=>'tblScoring'),array('total'=>'count(*)','text'=>'c.title','id'=>'a.id'));
		$select->joInInner(array('u'=>'tblUsers'),'u.UserID=a.Owner');
		$select->joInInner(array('c'=>'tblEvent'),'c.id=a.ParentId');
		$select->where('a.clientID= ?',clientID);
		$select->where('a.ParentType= ?',2);		
		//$select->where('a.ArticleType= ?',1);
		$select->group('c.title');
		$select->order('total DESC');		
		return $this->getDefaultAdapter()->query($select)->fetchAll();
	}

	// scoring section
	
    
}