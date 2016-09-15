<?php
class Zend_View_Helper_Blockuserhelper{	 
	
	public function Blockuserhelper($cookieuser,$user)
	{
		$messageblock = new Application_Model_Message();		
		$user = $messageblock->chkblockuser($cookieuser,$user);
		return $user;	
	
	}
}
?>