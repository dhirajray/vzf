<?php
class Application_Model_DbNotificationsettings
{
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
			$this->setDbTable('Application_Model_DbTable_DbNotificationsettings');
		}
		return $this->_dbTable;
	}
	
	public function ausernotidetail($UserID)
	{   
	    $User = $UserID;
		$db = $this->getDbTable();
		
		$select = $db->select()->where('User = ?', $User);
		$result = $db->fetchAll($select);
		return $result->toArray();
	}
	public function settingnotificationtemplate($checkVal,$dataval)
	{	
		$append ='';
		if($checkVal==1)  $checked = ' ';
		 else  $checked = 'checked="checked"';
/*    if ($dataval == '2') $append = '<div class="peoFollow">From people I follow</div>';
    if ($dataval == '4') $append = '<div class="peoFollow">From people I don\'t follow</div>';
    */
         $settingTemplate ='<label class="switcher notiSettingBtn" data-type="'.$dataval.'"><input type="checkbox" name=" "  '.$checked.'>
                                    <span class="switchOnOff">
                                        <span class="switchOn"></span>
                                    </span>
                                </label>'.$append;
        return $settingTemplate; 

    }
	
	
	public function getNotiDetail($UserID)
	{   
	    $User = $UserID;
		$db = $this->getDbTable();
		
		$select = $db->select()->where('User = ?', $User);
		$result = $db->fetchAll($select);
		return $result->toArray();
	}
	public function editausernotidetail($data)
	{
	   $db = $this->getDbTable();
	   $User =  $data['User'];
	   $where	=	$this->getDbTable()->getAdapter()->quoteInto('User = ?', $User); 
	   $allrec	=	$this->getDbTable()->update($data,$where);
	}
	
	public function addusernoti($data)
	{  
		return $allrec = $this->getDbTable()->insert($data);
	}
	
}
