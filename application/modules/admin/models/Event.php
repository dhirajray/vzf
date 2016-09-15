<?php
class Admin_Model_Event extends Zend_Db_Table_Abstract
{
    protected $_name = 'tblEvent';
    public function getEvent($id)
    {
        $select = $this->_db->select()          
           ->from(array('tblEvent'))
           ->where("id = ?", $id)->where("clientID = ?", clientID)
           ->order('id  DESC');
        return  $this->_db->fetchRow($select);
    }
    
    public function getAllPublishEvent()
    {
        $created = date('Y-m-d H:i:s');
        $select = $this->_db->select()          
           ->from(array('tblEvent'))
           ->where("clientID = ?", clientID)
           ->where("end >=? ", $created)
           ->order('id  DESC');

        return  $this->_db->fetchAll($select);
    }
    public function getAllEvent()
    {
        $select = $this->_db->select()          
           ->from(array('tblEvent'))
           ->where("clientID = ?", clientID)
           ->order('id  DESC');
        return  $this->_db->fetchAll($select);
    }

     public function getAllUnPublishEvent()
    {
        $created = date('Y-m-d H:i:s');
        $select = $this->_db->select()          
           ->from(array('tblEvent'))
           ->where("clientID = ?", clientID)
           ->where("end <= ? ", $created)
           ->order('id  DESC');
        return  $this->_db->fetchAll($select);
    }

    public function getEventRecord($id)
    {        
      $select = $this->_db->select()
      ->from(array('tblEvent'))       
      ->where("id =?", $id)
        ->where("clientID = ?", clientID);                
      return $this->_db->fetchRow($select);              
    }

    public function getAllLikeEvent($query)
    {
        $select = $this->_db->select()          
           ->from(array('tblEvent'))
           ->where("clientID = ?", clientID)
            ->where('title LIKE "'.$query.'%"')
           ->order('id  DESC');
        return  $this->_db->fetchAll($select);
    }
    public function eventInsert($data)
    {
        if ($this->_db->insert($this->_name,$data))
            return $this->_db->lastInsertId();
        else
            return false;
    }
    public function eventmetaInsert($data)
    {
        if ($this->_db->insert('tblEventMeta',$data))
            return $this->_db->lastInsertId();
        else
            return false;
    }
    public function eventmetaDelete($id)
    {
      $this->_db->delete('tblEventMeta', array(
          "event_id='" . $id . "'","clientID='" . clientID. "'"
      ));
      return true;
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

    
     public function checkEventMember2($event_id)
    {
        $select = $this->_db->select()          
           ->from(array('tblEventmember'))
           ->where("clientID = ?", clientID)
           ->where("event_id = ?", $event_id)
           ->order('id  DESC');
        return  $this->_db->fetchAll($select);
    }
    public function getEventdbee($event_id) 
    {
       $select = $this->_db->select()->from(array('g' => 'viewposts' ));
        $select->where("g.clientID = ?", clientID)->where("g.events =?", $event_id)
                             ->where("g.Active =?", '1')        
                                ->order('PostDate DESC');
      $result = $this->_db->fetchAll($select);
      return $result;
    }  
}