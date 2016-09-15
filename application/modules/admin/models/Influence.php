<?php

class Admin_Model_Influence  extends Zend_Db_Table_Abstract
{
     // Table name 
     protected $_name = 'tblInfluence';
     
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

	public function MostInfluenceUser($dbeeid)
	{
			
		$db = $this->getDbTable();
		$select = $db->select()->setIntegrityCheck( false );
		$select->from(array('a'=>'tblInfluence'),array('total'=>'count(*)','a.UserId','u.Name','u.lname','u.ProfilePic'))
		->joInInner(array('u'=>'tblUsers'),'u.UserID=a.UserId')
		->where('a.clientID= ?',clientID)
		->where('a.ArticleId= ?',$dbeeid)
		->where('a.ArticleType= ?',1)
		->group('a.UserId')
		->order('total DESC')->limit(1);
		//echo $select->__toString();
		//die; 
		return $this->getDefaultAdapter()->query($select)->fetchAll();
	}

	public function InfluenceUser($postid='',$type='',$count='')
	{

		$db = $this->getDbTable();
		$select = $db->select()->setIntegrityCheck( false );
		$select->from(array('a'=>'tblInfluence'),array('total'=>'count(*)','a.UserId','u.Name','u.lname','u.ProfilePic','a.ArticleId'));
		$select->joInInner(array('u'=>'tblUsers'),'u.UserID=a.UserId');
		$select->where('a.clientID= ?',clientID);

		if($postid!="")
		{
			if($type==0)
			{
				$select->where('a.ArticleId= ?',$postid);
		    }
		    if($type==1)
			{
				$select->where('a.ParrentId= ?',$postid);
		    }
		    if($type==2)
			{
				$select->where('a.ParrentId= ?',$postid);
		    }
		}

		if($type!="")
		{
		  $select->where('a.ParrentType= ?',$type);
		}

		$select->where('a.ArticleType= ?',1);
		$select->group('a.ArticleId');
		$select->order('total DESC');
		if($count=="")
		{
		 $select->limit('20');
	    }
		// echo $select->__toString();	

		return $this->getDefaultAdapter()->query($select)->fetchAll();
	}

	public function InfluenceUserPost($postid='',$type='',$count='')
	{

		$db = $this->getDbTable();
		$select = $db->select()->setIntegrityCheck( false );
		$select->from(array('a'=>'tblInfluence'),array('total'=>'count(*)','a.UserId','u.Name','u.lname','u.ProfilePic','text'=>'c.Text','dbid'=>'c.DbeeID','a.ArticleId'));
		$select->joInInner(array('u'=>'tblUsers'),'u.UserID=a.UserId');
		$select->joInInner(array('c'=>'tblDbees'),'c.DbeeID =a.ArticleId ');
		$select->where('a.clientID= ?',clientID);

		if($postid!="")
		{
			if($type==0)
			{
				$select->where('a.ArticleId= ?',$postid);
		    }
		    if($type==1)
			{
				$select->where('a.ParrentId= ?',$postid);
		    }
		    if($type==2)
			{
				$select->where('a.ParrentId= ?',$postid);
		    }
		}
		
		if($type!="")
		{
		  $select->where('a.ParrentType= ?',$type);
		}
		
		$select->where('a.ArticleType= ?',0);
		$select->group('c.Text');
		$select->order('total DESC');
		if($count=="")
		{
		 $select->limit('20');
	    }
		// echo $select->__toString();	

		return $this->getDefaultAdapter()->query($select)->fetchAll();
	}

	public function InfluencePaging($postid='',$total='',$Id)
	{
		$limit=20;
		$skip = ($Id-1)*$limit;
		$db = $this->getDbTable();
		$select = $db->select()->setIntegrityCheck( false );
		$select->from(array('a'=>'tblInfluence'),array('total'=>'count(*)','a.UserId','u.Name','u.lname','u.ProfilePic'));
		$select->joInInner(array('u'=>'tblUsers'),'u.UserID=a.UserId');
		$select->where('a.clientID= ?',clientID);
		if($postid!="")
		{
			if($type==0)
			{
				$select->where('a.ArticleId= ?',$postid);
		    }
		    if($type==1)
			{
				$select->where('a.ParrentId= ?',$postid);
		    }
		    if($type==2)
			{
				$select->where('a.ParrentId= ?',$postid);
		    }
		}
		$select->where('a.ArticleType= ?',1);
		if($total!="")
		{
		 $select->having('count(*) < ?',$total);
	    }
		$select->group('a.ArticleId');
		$select->order('total DESC');
		$select->limit($limit,$skip);
		//echo $select->__toString();	
		//die;			
		return $this->getDefaultAdapter()->query($select)->fetchAll();
	}

	public function InfluencePostPaging($postid='',$total='',$Id2)
	{
		$limit=20;
		$skip = ($Id2-1)*$limit;
		$db = $this->getDbTable();
		$select = $db->select()->setIntegrityCheck( false );
		$select->from(array('a'=>'tblInfluence'),array('total'=>'count(*)','a.UserId','u.Name','u.lname','u.ProfilePic','text'=>'c.Text','dbid'=>'c.DbeeID'));
		$select->joInInner(array('u'=>'tblUsers'),'u.UserID=a.UserId');
		$select->joInInner(array('c'=>'tblDbees'),'c.DbeeID =a.ArticleId ');
		$select->where('a.clientID= ?',clientID);
		if($postid!="")
		{
			if($type==0)
			{
				$select->where('a.ArticleId= ?',$postid);
		    }
		    if($type==1)
			{
				$select->where('a.ParrentId= ?',$postid);
		    }
		    if($type==2)
			{
				$select->where('a.ParrentId= ?',$postid);
		    }
		}
		$select->where('a.ArticleType= ?',0);
		if($total!="")
		{
		 //$select->having('count(*) < ?',$total);
	    }
		$select->group('c.Text');
		$select->order('total DESC');
		$select->limit($limit,$skip);
		//echo $select->__toString();	
		//die;			
		return $this->getDefaultAdapter()->query($select)->fetchAll();
	}



	
	public function FilterResultPost($type='')
	{
	
		$db = $this->getDbTable();
		$select = $db->select()->setIntegrityCheck( false );
		$select->from(array('a'=>'tblInfluence'),array('total'=>'count(*)','text'=>'c.Text','id'=>'c.DbeeID'));
		$select->joInInner(array('u'=>'tblUsers'),'u.UserID=a.UserId');
		$select->joInInner(array('c'=>'tblDbees'),'c.DbeeID =a.ArticleId ');
		$select->where('a.clientID= ?',clientID);
		
		$select->where('a.ParrentType= ?',0);
		
		//$select->where('a.ArticleType= ?',1);
		$select->group('c.Text');
		$select->order('total DESC');		
		return $this->getDefaultAdapter()->query($select)->fetchAll();
	}
	public function FilterResultGroup($type='')
	{
	
		$db = $this->getDbTable();
		$select = $db->select()->setIntegrityCheck( false );
		$select->from(array('a'=>'tblInfluence'),array('total'=>'count(*)','text'=>'c.GroupName','id'=>'c.ID'));
		$select->joInInner(array('u'=>'tblUsers'),'u.UserID=a.UserId');
		$select->joInInner(array('c'=>'tblGroups'),'c.ID=a.ParrentId');
		$select->where('a.clientID= ?',clientID);		
		$select->where('a.ParrentType= ?',1);		
		//$select->where('a.ArticleType= ?',1);
		$select->group('c.GroupName');
		$select->order('total DESC');		
		return $this->getDefaultAdapter()->query($select)->fetchAll();
	}

	public function FilterResultEvent($type='')
	{
	
		$db = $this->getDbTable();
		$select = $db->select()->setIntegrityCheck( false );
		$select->from(array('a'=>'tblInfluence'),array('total'=>'count(*)','text'=>'c.title','id'=>'a.id'));
		$select->joInInner(array('u'=>'tblUsers'),'u.UserID=a.UserId');
		$select->joInInner(array('c'=>'tblEvent'),'c.id=a.ParrentId');
		$select->where('a.clientID= ?',clientID);
		$select->where('a.ParrentType= ?',2);		
		//$select->where('a.ArticleType= ?',1);
		$select->group('c.title');
		$select->order('total DESC');		
		return $this->getDefaultAdapter()->query($select)->fetchAll();
	}

	public function Userlist($postid='',$type='',$ArticleType='')
	{

		$db = $this->getDbTable();
		$select = $db->select()->setIntegrityCheck( false );
		$select->from(array('a'=>'tblInfluence'),array('totalclick'=>'count(*)','u.Name','u.lname','u.ProfilePic'));
		$select->joinLeft(array('u'=>'tblUsers'),'u.UserID=a.influence_by');		
		$select->where('a.clientID= ?',clientID);
		$select->group('u.UserID');
		
		if($postid!="")
		{
			if($type==0)
			{
				$select->where('a.ArticleId= ?',$postid);
		    }
		    if($type==1)
			{
				$select->where('a.ParrentId= ?',$postid);
		    }
		    if($type==2)
			{
				$select->where('a.ParrentId= ?',$postid);
		    }
		}
		
		if($type!="")
		{
		  $select->where('a.ParrentType= ?',$type);
		}
		$select->where('a.ArticleType= ?',$ArticleType);
		//echo $select->__toString();	

		return $this->getDefaultAdapter()->query($select)->fetchAll();
	}

	public function influenceUserlist($userid='',$caller='')
	{

		$db = $this->getDbTable();
		$select = $db->select()->setIntegrityCheck( false );
		$select->from(array('a'=>'tblInfluence'),array('infTot' =>  new Zend_Db_Expr('count(a.id)')));
		if($caller=='byme')
		{
			$select->joinLeft(array('u'=>'tblUsers'),'u.UserID=a.influence_by');
			$select->where('a.influence_by= ?',$userid);
			$select->where('a.clientID= ?',clientID);
			$select->group('a.influence_by');
		}
		if($caller=='onme')
		{
			$select->joinLeft(array('u'=>'tblUsers'),'u.UserID=a.UserId');
			$select->where('a.UserId= ?',$userid);
			$select->where('a.clientID= ?',clientID);
			$select->group('a.UserId');
		}	
		
		return $this->getDefaultAdapter()->query($select)->fetchAll();
	}
	

	
    
}