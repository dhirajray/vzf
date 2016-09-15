<?php



class Application_Model_Privateuser extends Application_Model_DbTable_Master

{

    protected $_name = null; 


	protected function _setupTableName()

    {

              parent::_setupTableName();		

		      $this->_name = $this->getTableName(GROUP);     

    }

	

    public function getprivategroup()

    {
    	$select = $this->_db->select()
			->from(array($this->_name),	array('ID'))
    			->Where('GroupPrivacy= ?','2')->Where('clientID= ?',clientID)  
    				->orWhere('GroupPrivacy= ?','4');
    	//echo $select->__toString();
		$result = $this->_db->fetchAll($select);
		return $result;
    }
    
    public function getprivategroup2($userid)
    
    {    
    	$memberof = $this->getmemberof($userid);    	
    	$select = $this->_db->select()    
	    	->from(array('g' => $this->_name),	array('ID'))		    	
			    	->Where('g.GroupPrivacy= ?','2')  
    					->orWhere('g.GroupPrivacy= ?','4');
			    	if(count($memberof)>0){
    					$select->where("g.ID NOT IN(?)", $memberof);	
    				}    	
    	$result = $this->_db->fetchAll($select);
    	if(count($result)>0){
    	foreach($result as $data):
    	
    	$mydata[] = $data['ID'];
    	
    	endforeach;
    	return $mydata;
    	}
    }
    public function getmemberof($userid)    
    {
    
    	$select = $this->_db->select()    
	    	->from(array('gm' => $this->getTableName(GROUP_MEMBER)),array('GroupID'))
		    	->joinleft(array('g' => $this->getTableName(GROUP)), 'g.ID = gm.GroupID')
		    		->where("gm.User = ?", $userid)
		    			->where("gm.Status= 1")
	    					->Where('g.GroupPrivacy= ?','2')  
    							->orWhere('g.GroupPrivacy= ?','4');
    	    	
    	$result = $this->_db->fetchAll($select);
	    if(count($result)>0){
	    	foreach($result as $data):	    
	    	$useid[] = $data['GroupID'];
	    
	    	endforeach;
	    
	    	return $useid;
	    }
		
    }
    
  
}	