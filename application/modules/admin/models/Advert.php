<?php
class Admin_Model_Advert extends Zend_Db_Table_Abstract
{
    protected $_name = 'tblAdvert';
	protected $_dbTable;		
	public function setDbTable($dbTable)
    {
		if (is_string($dbTable))
			$dbTable = new $dbTable();
        if (!$dbTable instanceof Zend_Db_Table_Abstract)
            throw new Exception('Invalid table data gateway provided');
        $this->_dbTable = $dbTable;		
        return $this;
    } 
    public function getDbTable()
    {
		if (null === $this->_dbTable)
            $this->setDbTable('Admin_Model_Advert');
        return $this->_dbTable;
    }
    /**
     * insert advert title
     * @param data = array();
     * return last insert id or false
     */
    public function advertInsert($data)
    {
        if ($this->_db->insert($this->_name,$data))
            return $this->_db->lastInsertId();
        else
            return false;
    }
    public function getAdvert($type)
    {   
        $select = $this->_db->select()          
           ->from(array($this->_name))
           ->where("type = ?", $type)
           ->where("clientID = ?", clientID)
           ->where("status != ?", 3)
           ->order('id  DESC');
        return  $this->_db->fetchAll($select);         
    }


    public function getclickAdvertRecord()
    {  
        $select = $this->_db->select()         
           ->from(array('t'=>'tbltrackadvert'),array('t.AdvertId','t.position','t.BannerId','SUM( t.no_of_click ) AS totalclick','COUNT(*) AS totalclickuser'))
             ->join(array('ad'=>'tblAdvert',array('ad.advertTitle')), 't.AdvertId = ad.id'
                )->join(array('i'=>'tblAdvertImage',array('i.image')), 't.BannerId = i.id'
                )->where("t.clientID = ?", clientID)->where("ad.clientID = ?", clientID)->group('BannerId');
             
        

        return  $this->_db->fetchAll($select);        
    }

    public function publishAdvert($id,$data)
    {   
        return $this->_db->update($this->_name, $data, array(
            "clientID='" . clientID . "'","id='" . $id . "'"
        ));        
    }

    public function unpublishAllAdvert($data)
    {   
        return $this->_db->update($this->_name, $data, array(
            "clientID='" . clientID . "'","status!=3","type = 1"
        ));        
    }
    
    public function deleteAdvert($id)
    {   
        return $this->_db->delete($this->_name, array(
            "clientID='" . clientID . "'","id='" . $id . "'"
        ));        
    }
    public function getSingleHeaderAdvert($id,$type)
    {
        $select = $this->_db->select()         
           ->from(array('A'=>'tblAdvertImage'),array('A.image','A.link','A.speed','A.effect','A.layout','A.position','A.slidershow'))
             ->join(array('B'=>'tblAdvert',array('B.advertTitle','B.id')), 'B.id = A.advertID'
                )->join(array('C'=>'tblAdvertRelation',array('C.relationID','C.status')), 'B.id = C.advertID'
                )->where("B.id = ?", $id)->where("A.position = ?", 'header')
             ->where("B.type = ?", $type)->where("B.clientID = ?", clientID)->order('B.id  DESC');
             $select->group('A.id');
        return  $this->_db->fetchAll($select);
    }
    public function getSingleRightAdvert($id,$type)
    {
        $select = $this->_db->select()          
           ->from(array('A'=>'tblAdvertImage'),array('A.image','A.link','A.speed','A.effect','A.layout','A.position','A.slidershow'))
             ->join(array('B'=>'tblAdvert',array('B.advertTitle','B.id')), 'B.id = A.advertID'
                )->join(array('C'=>'tblAdvertRelation',array('C.relationID','C.status')), 'B.id = C.advertID'
                )->where("B.id = ?", $id)->where("A.position = ?", 'right')
             ->where("B.type = ?", $type)->where("B.clientID = ?", clientID)->order('B.id  DESC');
             $select->group('A.id');
        return  $this->_db->fetchAll($select);
    }

    public function trackuserdetails($BannerId)
    {
       return $this->getDefaultAdapter()->query("SELECT SUM(tr.no_of_click) as totalclick, `c`.* FROM `tblUsers` AS `c` INNER JOIN `tbltrackadvert` AS `tr` ON tr.UserID=c.UserID WHERE (tr.BannerId= '".$BannerId."' AND tr.clientID= '".clientID."') GROUP BY tr.UserID")->fetchAll();
    }
    
}


