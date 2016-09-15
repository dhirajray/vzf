<?php



class Application_Model_Dbeedetail extends Application_Model_DbTable_Master

{

    protected $_name = null; 

	  

   // protected $_dependentTables = array('Application_Model_Technicianjobtype');

    

	protected function _setupTableName()
    {

              parent::_setupTableName();		

		      $this->_name = $this->getTableName(CAT);     

    }

    public function CheckInfluence($UserId,$ParrentId,$ParrentType,$ArticleId,$ArticleType,$influence_by,$CommentId=0)
    {

    	$db  = Zend_Db_Table_Abstract::getDefaultAdapter();
         $SQL = "select id,UserId from tblInfluence where clientID='".clientID."' and UserId='".$UserId."' and ParrentId='".$ParrentId."' and ParrentType='".$ParrentType."' and ArticleId='".$ArticleId."' and ArticleType='".$ArticleType."' and influence_by='".$influence_by."' and CommentId='".$CommentId."'";
        return $db->fetchAll($SQL);
    }  


     public function AddInfluence($data)
    {

    	 if ($this->_db->insert('tblInfluence', $data))
            return true;
        else
            return false;
        
    }   

     public function RemoveInfluence($id)
    {

    	if ($this->_db->delete('tblInfluence', array(
            "id='" . $id . "'"
        )))
            return true;
        else
            return false;
        
    }                 

    
	

}	