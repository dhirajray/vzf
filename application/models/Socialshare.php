<?php
class Application_Model_Socialshare extends Application_Model_DbTable_Master
{
    protected $_name = null;
    protected function _setupTableName()
    {
        parent::_setupTableName();
        
        $this->_name = $this->getTableName(DBEESOCIALSHARE);
        
    }
    /**
     *  insert social share data
     */
    public function insertSocialShare($data)
    {
        if ($this->_db->insert($this->_name, $data))
            return true;
        else
            return false;
    }
    /**
     *  check social share if exist then update counter
     */
    public function checkSocialShare($dbeeid, $sharetype, $user_id)
    {
        $select = $this->_db->select()->from($this->_name)->where("dbeeid = ?", $dbeeid)
        ->where("sharetype = ?", $sharetype)->where("clientID = ?", clientID)
        ->where("user_id = ?", $user_id);
        $result = $this->_db->fetchRow($select);
      
        if (!empty($result))
            return $result;
        else
            return '0';
    }
    /**
     *  update social share counter 
     */
    public function updateSocialShare($data, $dbeeid, $sharetype, $user_id)
    {
        return $this->_db->update($this->getTableName(DBEESOCIALSHARE), $data, array(
            "dbeeid='" . $dbeeid . "'","sharetype='" . $sharetype . "'","user_id='" . $user_id . "'","clientID='" . clientID . "'"
        ));
    }
}
