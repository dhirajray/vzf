<?php



class Application_Model_Blockuser extends Application_Model_DbTable_Master

{

    protected $_name = null; 

    

    protected $_userid = null;

	  

	protected function _setupTableName()

    {

              parent::_setupTableName();		

		      $this->_name = $this->getTableName(BLOCKSUSER_DBEE);     

    }   

	

   public function getblockuser($userid)

    { 

    	$select = $this->_db->select()

			->from(array($this->_name),	array('ID','BlockedUser'))

				->where("User = ?",$userid)->where("clientID = ?", clientID);

		$result = $this->_db->fetchAll($select);

		foreach($result as $data):

		$useid[] = $data['BlockedUser'];

		endforeach;

		return $useid;				

    }

    	

}	



