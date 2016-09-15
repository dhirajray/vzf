<?php

class Admin_Model_Social extends Zend_Db_Table_Abstract
{
     // Table name 
     protected $_name = 'tblspecialdbinvite';
     // Schema name
     // protected $_use_adapter = "matjarna";
	 
	 protected $_dbTable;
			
	public function setDbTable($dbTable)
    {
		if (is_string($dbTable)) {
			$dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;		
        return $this;
    } 
    public function getDbTable()
    {
		if (null === $this->_dbTable) {
            $this->setDbTable('Admin_Model_Social');
        }
        return $this->_dbTable;
    }

    public function insertSocialInvitation($data)
    {
        if ($this->_db->insert('tblspecialdbinvite', $data))
            return true;
        else
            return false;
    }
    
    public function insertEventInvitation($data)
    {
        if ($this->_db->insert('tblEventInvitation', $data))
            return true;
        else
            return false;
    }
    public function insertininviteexport($data)
    {
        /*******Insert inviteexport *********/
        if ($this->_db->insert('tblinvitexport', $data))
            return true;
        else
            return false;
    }
    
    public function insertMessage($data)
    {        
        $this->_db->insert('tblMessages',$data);
        $id = $this->_db->lastInsertId();
        $data2 = array('ChainParent'=> $id);
        $this->_db->update('tblMessages', $data2 ,array("ID='" . $id . "'"));       
        return $id;       
    }

    public function commomInsert($act_type, $act_message, $act_typeId, $act_userId, $act_ownerid,$scoretype='',$act_cmnt_id='')
   {
      $act_date=  date('Y-m-d H:i:s'); 
      $data    =  array(
					 'clientID'     => clientID,	
                     'act_type'     => $act_type,
                     'act_message'  => $act_message,
                     'act_typeId'   => $act_typeId,
                     'act_cmnt_id'   => $act_cmnt_id,
                     'act_userId'   => $act_userId,
                     'act_ownerid'  => $act_ownerid,
                     'act_score_type'     => $scoretype,
                     'act_date'     => $act_date,
                     'act_status' => 0
                  );

      if ($this->_db->insert('tblactivity', $data))
         return true;
      else
         return false;
        
   }   
    public function getdburltitle($dbeeid)
    {
         $select = $this->_db->select()->from(array(
            'A' => 'tblDbees'
        ), array(
            'A.*'
        ))->where("A.DbeeID = ?", $dbeeid)->where("A.clientID = ?", clientID);
        return $this->_db->fetchRow($select);

    }
    public function checkpreInviteSocial($type,$socialid)
    {
         $select = $this->_db->select()->from(array(
            'A' => 'tblSocialinvitation'
        ), array(
            'A.*'
        ))->where("A.type = ?", $type)->where("A.socialid = ?", $socialid)->where("A.clientID = ?", clientID);
        return $this->_db->fetchRow($select);
    }
    public function checkchainparent($userid,$user)
    {
        $select = $this->_db->select()
        ->from(array('t' => 'tblMessages'),  array('ID','MsgDate'=>'MessageDate'))       
            ->where('t.MessageFrom ='.$userid.' AND t.MessageTo = '.$user.'')
                ->orWhere('(t.MessageFrom ='.$user.' AND t.MessageTo = '.$userid.')')->where("t.clientID = ?", clientID)
                    ->order('t.MessageDate asc')
                        ->limit(1);
        $result = $this->_db->fetchRow($select);
        return $result;
    }
}


