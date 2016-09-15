<?php
class Admin_Model_Restrictedurl  extends Zend_Db_Table_Abstract
{     
	protected $_name = 'tblRestrictLink';	 
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
            $this->setDbTable('Admin_Model_Restrictedurl');
        }
        return $this->_dbTable;
    }
	
    public function getlinkurl() 
    {
        $db = $this->getDbTable();
        $select = $db->select();
        $select->setIntegrityCheck( false );
        $select->from( array('tblRestrictLink'),array('ID','linkurl'));
        $select->where('clientID = ?',clientID);          
        return $db->fetchAll($select);
    }
    public function gettotal() 
    {
        $db = $this->getDbTable();
        $select = $db->select();
        $select->setIntegrityCheck( false );
        $select->from( array('tblRestrictLink'),array('ID'));
        $select->where('clientID = ?',clientID);          
        return count($db->fetchAll($select));
    }
    public function addurl($data)
    {
    	 if ($this->_db->insert('tblRestrictLink', $data))
            return $this->_db->lastInsertId();
        else
            return false;
    }
	public function dellinkurl($linkid){
    	
    	$del =  $this->_db->delete('tblRestrictLink', array('ID = '.$linkid));
    	return;
    
    }
    public function getrestrictedurl($url)
    {
    	$url =$this->__removehttp($url);
        if(strpos($url, 'www.') === 0) {
             $url =  str_replace('www.', '', $url);         
             $pos = strpos($url, '/');
             if($pos!=''){
                     $urls = substr($url,0,$pos);
             }else{$urls = $url; }
        }else{$urls = $url; }
    	$select = $this->_db->select()
    	->from('tblRestrictLink',array('ID','linkurl'))
    	->where('linkurl LIKE ?', "%$urls%")->where('clientID = ?', clientID);
    	$data = $this->_db->fetchAll($select);
    	if(count($data)>0)
    		return $data;
    	else
    		return false;
    }
    function __removehttp($url) {
    	$disallowed = array('http://', 'https://');
    	foreach($disallowed as $d) {
    		if(strpos($url, $d) === 0) {
    			return str_replace($d, '', $url);
    		}
    	}
    	return $url;
    }
  
}