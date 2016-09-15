<?php
class Application_Model_Favourites extends Application_Model_DbTable_Master
{

    protected $_name = null; 
	protected function _setupTableName()
    {
              parent::_setupTableName();		
		      $this->_name = $this->getTableName('tblInfluence');     
    }

    public function init()
    {
        parent::init();
        
        
    }
    public function index($start,$end,$userid)
    {
            //require_once 'includes/globalfile.php'; 
    		$Offset = (int)$start;
    		$select = $this->_db->select()->from(array('c' => 'viewposts' ))->joinLeft(array('f' => 'tblInfluence'),
						    				'f.ArticleId = c.DbeeID', array('UserId','ArticleId','influence_by'));
									    		if(!empty($blockuser))
									    			$select->where("c.GroupID NOT IN ?", $sub_select);
									    		if(!empty($PrivateGroups))
									    		$select->where("c.User NOT IN ?", $sub_select);		    		
									    		$select->where('f.influence_by='.$userid);
                                                $select->where("f.ArticleType= ?",'0');
									    		$select->where("c.Active= ?",'1');
                                                $select->where("c.clientID = ?", clientID);
												    	$select->group('c.DbeeID')
												    			->order('c.LastActivity DESC')
												    				->limit(PAGE_NUM, $Offset);

                                                                    //echo $select->__toString();
    		return $this->_db->fetchAll($select);
    }
   public function getfolloweruser($userid)
   {
    	$select = $this->_db->select();
    		$select->from(array('c' => $this->_name))
    			->join(array('u' => $this->getTableName(USERS)), 'u.UserID = c.User');
    				$select->where('c.User =.'.$userid);
    					$select->order(array('c.User asc'));    						
    							return $this->_db->fetchAll($select);
    }

    public function getfolloweruserbyid($id)
    {
    	$select = $this->_db->select();
    	$select->distinct();
    	$select->from($this->_name,array('User'));
    	$select->where('FollowedBy ='.$id)->where('clientID = ?', clientID);    	
    	$result = $this->_db->fetchAll($select);
    	foreach($result as $data):
    	$useid[] = $data['User'];
    	endforeach;
    	return $useid;
    }

     public function addfav($data)
    {
        if ($this->_db->insert($this->getTableName(FAVOURITES), $data))
            return true;
        else
            return false;
    }

    public function getallfollowing()
    {
    	$select = $this->_db->select()
		->from(array($this->_name),	array('CatID','CatName'));	
		return $this->_db->fetchAll($select);
    }
    public function checkfavdbee($DbeeID,$UserID)
    {
        $select = $this->_db->select()
        ->from(array($this->_name))->where('clientID = ?', clientID)
                                    ->where('DbeeID = ?', $DbeeID)
                                    ->where('User = ?', $UserID);  
        return $this->_db->fetchRow($select);
    }
}	