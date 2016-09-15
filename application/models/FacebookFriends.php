<?php

class Application_Model_FacebookFriends extends Application_Model_DbTable_Master
{
	protected $_name = null;

	protected function _setupTableName()
	{
		parent::_setupTableName();
		$this->_name = $this->getTableName(FACEBOOKFRIENDS);
	}

	public function myfbfriends($userfriends_details)
	{
	  $this->_name = $this->getTableName(FACEBOOKFRIENDS);
	  return $allrec = $this->_db->insert($this->_name,$userfriends_details);
	}
	
	public function chkfriend_user($uid)
    { 
		$select = $this->_db->select()
			    ->from($this->getTableName(FACEBOOKFRIENDS))
    			->where("fb_userid = ?",$uid);    
		$result = $this->_db->fetchRow($select);
		return $result;
    }
	
	
	public function updatefriendlist($data,$id)
    {
	    echo'<pre>';print_r($data);print_r($id);die;
    	if($this->_db->update($this->getTableName(FACEBOOKFRIENDS), $data,array("fb_userid='".$id."'")))
    		{return true;}
    	else
    		{return false;}
    }
	
	
	
}

?>


