<?php



class Application_Model_Usersite extends Application_Model_DbTable_Master

{

	protected $_name = null;

	

	protected function _setupTableName()

	{

		parent::_setupTableName();

		$this->_name = $this->getTableName(USER_RSS);

	}
	public function getsitetotal()
	{
		$select = $this->_db->select()
		->from($this->getTableName(RSS_SITES),array('ID','Logo','Name','URL'))
		->where('clientID = ?', clientID);	
		$result = $this->_db->fetchAll($select);
		return count($result);	
	}	



	public function getsite()
	{
		 $select = $this->_db->select()		
			->from( $this->getTableName(RSS_SITES),array('ID','Logo','Name','URL'))						
				->where('clientID = ?', clientID)
					->order('Country')->limit(4);	
								//echo $select->__toString();					
			return $this->_db->fetchAll($select);		
	}

	public function getpdflist($user,$limit=true)	
	{	
		$select = $this->_db->select()	
		->from(array('p' => $this->getTableName(USERPDF)),array('id','userid','fileid','sdate'))	
		->joinInner(array('k' => 'tblknowledge'),'p.fileid = kc_id',array('kc_cat_title','kc_file','kc_adddate','is_front','kc_id'))
		->joinInner(array('u' => 'tblUsers'),'k.added_by = u.UserID',array('full_name','UserID'))
				->where('p.clientID = ?', clientID)
				->where('p.userid = ?',$user)->where('p.status = ?','1')->order('p.id Desc');
		if($limit) $select->limit('5');	
		//echo $select->__tostring();
		$result = $this->_db->fetchAll($select);		
		return $result;
	
	}
	public function getmyshare($user,$limit=true)
	{
		$select = $this->_db->select()
		->from(array('k' => 'tblknowledge'),array('kc_cat_title','kc_file','kc_adddate','is_front','kc_id'))
		->joinInner(array('u' => 'tblUsers'),'k.added_by = u.UserID',array('full_name','UserID'))
		->where('k.clientID = ?', clientID)
		->where('k.added_by='.$user)->where('k.added_by='.$user)->where('k.isdelete = ?',1)->order('kc_id Desc');		
		if($limit) $select->limit('5');		
		$result = $this->_db->fetchAll($select);
		return $result;
	
	}

}

