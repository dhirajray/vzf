<?php
class Application_Model_Event extends Application_Model_DbTable_Master
{
    protected $_name = null;
    
    public function getEvent($id)
    {
        $select = $this->_db->select()          
           ->from(array('tblEvent'))
           ->where("id = ?", $id)->where("clientID = ?", clientID)->where("status = ?", 1)
           ->order('id  DESC');
        return  $this->_db->fetchAll($select);
    }
    public function checkEventSocial($eventid,$type,$token,$socialid)
    {
        $select = $this->_db->select()
          ->from('tblEventInvitation')
            ->where("eventID = ?", $eventid)
              ->where("type = ?", $type)
                ->where("token = ?", $token)
                ->where("socialid = ?", $socialid)
                  ->where("clientID = ?", clientID);
        $result = $this->_db->fetchRow($select);
        if ($result)
            return '1';
        else
            return '0';
    }
    public function deleteEventInvitation($token)
    {
        if ($this->_db->delete('tblEventInvitation',array(
            "token='" . $token . "'","clientID='" . clientID . "'"
        )))
            return true;
        else
            return false;
    }
    
    public function checkEventMember($id,$event_id)
    {
        $select = $this->_db->select()          
           ->from(array('tblEventmember'))
           ->where("clientID = ?", clientID)
           ->where("member_id = ?", $id)->where("event_id = ?", $event_id)
           ->order('id  DESC');
        return  $this->_db->fetchRow($select);
    }
     public function getAllMetaDataEvent($event_id)
    {
        $select = $this->_db->select()          
        ->from(array('tblEventMeta'))
        ->where("clientID = ?", clientID)
        ->where("event_id = ?", $event_id)
        ->order('id  ASC');
        return  $this->_db->fetchAll($select);
    }
    public function getAllEvent()
    {
        $select = $this->_db->select()          
        ->from(array('tblEvent'))
        ->where("clientID = ?", clientID)
        ->where("status = ?", 1);
        $select->where("type != ?", 3);
        $select->order('id  DESC');
        return  $this->_db->fetchAll($select);
    }
    public function getMyEvent($uid)
    {  
        $select = $this->_db->select()->distinct('m.event_id')          
           ->from(array('e' => 'tblEvent'))
           ->join(array('m' => 'tblEventmember'), 'm.event_id =e.id',  array('member_id'))
           ->where("e.clientID = ?", clientID)->where("e.status = ?", 1)->where("m.status = ?", 1)->where("m.member_id = ?", $uid)
           ->order('e.id  DESC');
        return  $this->_db->fetchAll($select);
    }

    public function getTopEvent()
    { 
        $select = $this->_db->select()          
           ->from(array('tblEvent'))
           ->where("clientID = ?", clientID)
           ->where("status = ?", 1)
           ->where("promoted = ?", 1)
           ->where("DATE_FORMAT(end, '%Y-%m-%d') >= ?", date('Y-m-d'))
           ->order('start  ASC');
        return  $this->_db->fetchAll($select);
    }
       
    public function caltotaldbee($id)
    {        
      $select = $this->_db->select()
      ->from($this->getTableName(DBEE))       
      ->where("events =?", $id)
        ->where("clientID = ?", clientID);                
      return $this->_db->fetchAll($select);              
    }

     public function getEventRecord($id)
    {        
      $select = $this->_db->select()
      ->from(array('tblEvent'))       
      ->where("id =?", $id)
        ->where("clientID = ?", clientID);                
      return $this->_db->fetchRow($select);              
    }

    public function caltotaldbresult($id,$lastID)
    {    
        $select = $this->_db->select()->from(array('g' => 'viewposts' ));
        $select = $select->where("g.events =?", $id)
             ->where("g.clientID = ?", clientID)        
               ->where("g.Active =?", '1');
               if($lastID)
                  $select->where("g.DbeeID < ?", $lastID);
                $select->order('PostDate DESC')->limit(PAGE_NUM); 
                //echo $select->__toString();  exit;  
       return $this->_db->fetchAll($select);
    }

     public function dbresult($id)
    {      
        $select = $this->_db->select()        
          ->from(array('g' => $this->getTableName(DBEE)))      
            ->join(array('u' => $this->getTableName(USERS)), 'g.User =u.UserID')
              ->where("g.DbeeID =?", $id)
             ->where("g.clientID = ?", clientID)        
               ->where("g.Active =?", '1');        
       return $this->_db->fetchRow($select);
    }

    public function getAllEventMember($id)
    {        
        $select = $this->_db->select()        
          ->from(array('g' => 'tblEventmember'))      
            ->join(array('u' => $this->getTableName(USERS)), 'g.member_id =u.UserID')
             ->where("g.clientID = ?", clientID)        
               ->where("g.status =?", '1')     
               ->where("g.member_id!=?", adminID)         
               ->where("g.event_id =?", $id)        
                ->order('g.timestamp DESC');   
                //echo $select->__toString();  exit;      
       return $this->_db->fetchAll($select);
    }


    
}