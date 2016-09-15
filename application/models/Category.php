<?php



class Application_Model_Category extends Application_Model_DbTable_Master

{

    protected $_name = null; 

	  

   // protected $_dependentTables = array('Application_Model_Technicianjobtype');



	protected function _setupTableName()

    {

              parent::_setupTableName();		

		      $this->_name = $this->getTableName(CAT);     

    }

	

    public function getallcategory()

    {

    	$select = $this->_db->select()->from(array($this->_name),	array('CatID','CatName'))->where("clientID = ?", clientID);	

		$result = $this->_db->fetchAll($select);

		return $result;

    }

    

    public function getsortcategory()

    {

    	$select = $this->_db->select()

    	->from(array($this->_name),	array('CatID','CatName'))->where("clientID = ?", clientID)->order("Priority DESC");

    	$result = $this->_db->fetchAll($select);

    	return $result;

    }

    

	

}	