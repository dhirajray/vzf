<?php
/**
 * Login_Model_Users
 *  
 * @author Enrico Zimuel (enrico@zimuel.it)
 */
class Adminlogin_Model_Users extends Zend_Db_Table_Abstract
{
    protected $_name = 'users';
    
    public function isLdapUser($username) {
    	if (empty($username)) {
    		return false;
    	}
    	$select= $this->select()->where('username = ?', $username)
        ->where("clientID = ?", clientID);
    	$result= $this->fetchRow($select);						
		return !empty($result);		
    }
    
	public function getUserId($username) {
    	if (empty($username)) {
    		return false;
    	}
    	$result= $this->find($username);
    	if (!empty($result)) {
    		return $result[0]['id'];
    	}
    	return false;
    }

    public function getUserEmail($useremail) 
    {
        $this->myclientdetails = new Application_Model_Clientdetails();
        if (empty($useremail)) 
        {
            return false;
        }
        $useremail = $this->myclientdetails->customEncoding($useremail);

        $select= $this->select()->where('email = ?', $useremail)->where("clientID = ?", clientID); 
        return    $result= $this->fetchAll($select)->toarray();   

        //$select = $this->_db->select()->from('tblUsers')->where('email = ?', $useremail)->where("clientID = ?", clientID);                            
       // return  $this->_db->fetchAll($select);        
    }
   
    public function getUserbyId($id) 
    {
        if (empty($id)) {
            return false;
        }
        $select= $this->select()->where('id = ?', $id)->where("clientID = ?", clientID); 
        return    $result= $this->fetchAll($select)->toarray();          
    }


    public function checkresetcode($user, $resetcode)
    {
        $select = $this->select()->where('id = ?', $user)->where('password = ?', $resetcode)->where("clientID = ?", clientID);
        return $this->fetchAll($select)->count(); 
    }
    public function resetpasscode($UserID, $rand,$salt)
    {
        $password   = sha1($salt.trim($rand));   
        $data = array( 'password' => $password );        
        $where[] = $this->getAdapter()->quoteInto('clientID = ?', clientID);
        $where[] = $this->getAdapter()->quoteInto('id = ?', $UserID);
        return $this->update($data, $where);
        
    }

    public function chkforregistration()
    {
        $select = $this->select()->where('username != ?', '')->where("clientID = ?", clientID);        
        return $this->fetchAll($select)->count();        
    }

    public function chkforregistration_tblUsers() 
    {
        $select = $this->_db->select()->from('tblUsers')->where('username != ?', '')->where("clientID = ?", clientID);                            
        return  $this->_db->fetchAll($select);     
    }

    public function insertdata_global($table,$data){
        $insertdb = $this->_db->insert($table, $data);
        if($insertdb) return $this->_db->lastInsertId();
        else return false; 
    }
}