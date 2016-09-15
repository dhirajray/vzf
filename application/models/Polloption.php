<?php
class Application_Model_Polloption extends Application_Model_DbTable_Master
{
	
	protected $_name = null;
	
	protected function _setupTableName()
	{
		parent::_setupTableName();
		$this->_name = $this->getTableName(POLL_OPTION);
	}
	
	public function getpollvote($dbeeid) {	
		$select = $this->_db->select()
			->from($this->getTableName(POLL_VOTES),array("cnt"=>"count('ID')"))
					->where('PollID = ?',$dbeeid)->where('clientID = ?',clientID);			
		return $this->_db->fetchAll($select);
		
	}
	public function getoptionbyid($vote)
	{
		$select = $this->_db->select()
		->from($this->getTableName(POLL_OPTION),
				array("OptionText"))
				->where('ID = ?',$vote);
		return $this->_db->fetchRow($select);
		
	}
	public function getvotename($user,$db)
	{
		$select = $this->_db->select()
		->from(array('o' =>$this->getTableName(POLL_OPTION)),array("OptionText"))
			->joinLeft(array('v' => $this->getTableName(POLL_VOTES)),'v.Vote = o.ID')
				->where('v.User = ?',$user)->where('v.clientID = ?',clientID)
					->where('v.PollID = ?',$db);
		return $this->_db->fetchRow($select);
	}
	
	public function getpolloption($dbeeid) 
	{	
		$select = $this->_db->select()
		->from($this->getTableName(POLL_OPTION),
				array("OptionText","ID"))
				->where('PollID = ?',$dbeeid)->where('clientID = ?',clientID);			
		return $this->_db->fetchAll($select);
		
	}
	
	public function getopname($id)
	{
		$select = $this->_db->select()
		->from($this->getTableName(POLL_OPTION),
				array("OptionText"))
				->where('ID = ?',$id)->where('clientID = ?',clientID);		
		return $this->_db->fetchAll($select);
		
	}
	
	public function totalpoll($poll)
	{
		$select = $this->_db->select()
			->from($this->getTableName(POLL_VOTES))
					->where('PollID = ?',$poll)->where('clientID = ?',clientID);		
		return count($this->_db->fetchAll($select));
		
	}
	
	public function insertpoll($data)
	{
		 $this->_db->insert($this->getTableName(POLL_VOTES), $data);
		 return 1;
	}
	public function getinsertid()
	{
		$id = $this->_db->lastInsertId($this->getTableName(POLL_VOTES));
		return $id;
	}
	
	public function insertpollcomment($data)
	{
		$this->_db->insert($this->getTableName(COMMENT), $data);
		return $this->_db->lastInsertId();
	}
	
	public function getpolloptionvote($dbeeid,$opid) 
	{	
		$select = $this->_db->select()
			->from($this->getTableName(POLL_VOTES),array("cnt"=>"count('ID')"));					
					$select->where('Vote ='.$opid.' AND PollID = '.$dbeeid.'')->where('clientID = ?',clientID);						
		return $this->_db->fetchAll($select);
		
	}
	
	public function getpartcpres($dbeeid) 
	{
		$select = $this->_db->select()
			->from(array('p' => $this->getTableName(POLL_VOTES)))
				->joinLeft(array('u' => $this->getTableName(USERS)),'p.User = u.UserID')
							->where('p.PollID = ?',$dbeeid)->where('p.clientID = ?',clientID);		
		return $this->_db->fetchAll($select);
		
	}
	public function getpartcpreslimit($dbeeid) 
	{
		$select = $this->_db->select()
		->from(array('p' => $this->getTableName(POLL_VOTES)))
			->joinLeft(array('u' => $this->getTableName(USERS)),'p.User = u.UserID')
				->where('p.PollID = ?',$dbeeid)->where('p.clientID = ?',clientID)
				->group('u.UserID')
					->limit(19);		
		return $this->_db->fetchAll($select);
		
	}
	
	public function getpartstatusrow($userid) 
	{
		$select = $this->_db->select()
		->from( $this->getTableName(USERS),array('Status'))		
		->where('Status = ?',$userid)->where('clientID = ?',clientID);
		$result = $this->_db->fetchAll($select);
		return $result[0]['Status'];
	}
	public function getmyvoteres($dbeeid,$userid) 
	{
		$select = $this->_db->select()
			->from( array('v' =>$this->getTableName(POLL_VOTES)))
				->joinLeft(array('o' => $this->getTableName(POLL_OPTION)),'o.ID = v.Vote')
					->where('v.User = ?',$userid)->where('v.clientID = ?',clientID)
						->where('v.PollID = ?',$dbeeid);					
		return $this->_db->fetchAll($select);
		
	}
	public function getmyvoteresdbee($dbeeid,$vote,$userid)
	{
		$select = $this->_db->select()
		->from( $this->getTableName(POLL_VOTES))
		->where('PollID ='.$dbeeid.' AND Vote ='.$vote)->where('clientID = ?',clientID);			
		return $this->_db->fetchAll($select);
		
	}	
	public function getvsql($voteid) 
	{
		$select = $this->_db->select()
			->from( $this->getTableName(POLL_OPTION),array('OptionText'))
				->where('ID ='.$voteid)->where('clientID = ?',clientID);		
		return $this->_db->fetchAll($select);
	}
	
	public function getpores($poll)
	{
		$select = $this->_db->select()
			->from( $this->getTableName(POLL_OPTION))
				->where('PollID = ?',$poll)->where('clientID = ?',clientID);		
		return $this->_db->fetchAll($select);
	}
	
	public function getoptionbyidrow($dbeeid,$userid)
	{
		$select = $this->_db->select()
		->from( array('v' =>$this->getTableName(POLL_VOTES)))
		->joinLeft(array('o' => $this->getTableName(POLL_OPTION)),'o.ID = v.Vote')
		->where('v.User = ?',$userid)->where('v.clientID = ?',clientID)
		->where('v.PollID = ?',$dbeeid);
		return $this->_db->fetchRow($select);
	}
}
