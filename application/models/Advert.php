<?php
class Application_Model_Advert extends Application_Model_DbTable_Master
{
    protected $_name = null;
    
    public function getAdvertisement($id)
    {
        $select = $this->_db->select()          
           ->from(array('A'=>'tblAdvertImage'),array('A.*','A.id as bannerid'))
           ->where("A.advertID = ?", $id)->where("A.clientID = ?", clientID)
           ->order('A.id  DESC');
          // echo $select->__toString(); exit;
        return  $this->_db->fetchAll($select);
    }
    public function getGlobalAdvertisement()
    {
        $select = $this->_db->select()          
           ->from(array('A'=>'tblAdvertImage'),array('A.*','A.id as bannerid'))
             ->join(array('B'=>'tblAdvert',array('B.advertTitle')), 'B.id = A.advertID'
                )->where("B.status = ?", 1)->where("A.clientID = ?", clientID)
             ->where("B.type = ?", 1)->order('B.id  DESC');
        return  $this->_db->fetchAll($select);
    }

    public function getSpecificAdvertisement($relationID,$type)
    {
        $select = $this->_db->select()          
           ->from(array('A'=>'tblAdvertImage'),array('A.*','A.id as bannerid'))
             ->join(array('B'=>'tblAdvert',array('B.advertTitle')), 'B.id = A.advertID'
                )->join(array('C'=>'tblAdvertRelation',array('C.relationID')), 'B.id = C.advertID'
                )->where("B.status = ?", 1)->where("A.clientID = ?", clientID)
                ->where("C.relationID = ?", $relationID)
             ->where("B.type = ?", $type)->order('B.id  DESC');
        return  $this->_db->fetchAll($select);
    }

    public function checkUserInUsergroup($id,$userid)
    {
        $select = $this->_db->select()          
           ->from(array('usersgroupid'))
           ->where("ugid = ?", $id)->where("clientID = ?", clientID)
           ->where("userid = ?", $userid)
           ->order('ugid DESC');
        return  $this->_db->fetchRow($select);
    }
    public function getRelationAdvert($type)
    {   
          $select = $this->_db->select()          
           ->from(array('A'=>'tblAdvert'),array('A.*'))
             ->join(array('C'=>'tblAdvertRelation',array('C.relationID')), 'A.id = C.advertID'
                )->where("A.status = ?", 1)->where("A.clientID = ?", clientID)
             ->where("A.type = ?", $type)->order('A.id  DESC');
        return  $this->_db->fetchAll($select);        
    }
    
    // insert track click record
    public function InsertClickAdvertTrackRecord($data,$adid,$userid,$BannerId)
    {   
         
         $select = $this->_db->select()          
           ->from(array('tbltrackadvert'))
           ->where("clientID = ?", clientID)->where("AdvertId = ?", $adid)->where("UserId = ?", $userid)->where("BannerId = ?", $BannerId);
           $fetchdata=$this->_db->fetchAll($select);

          if(count($fetchdata) < 1)
          {
            $this->_db->insert('tbltrackadvert', $data);
          }
          else
          {  
            $no_of_click=$fetchdata[0]['no_of_click']+1;
            $data['no_of_click']=$no_of_click;           
           
            $this->_db->update('tbltrackadvert',$data, array("AdvertId='" . $adid . "'","UserId='" . $userid . "'","BannerId='" . $BannerId . "'","clientID='".clientID."'"));
          
          }      
    }
    // insert track click record end 
}
