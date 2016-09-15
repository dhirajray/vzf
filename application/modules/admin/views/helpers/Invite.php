<?php

class Zend_View_Helper_Invite{
	
	protected $request;
	
	
	public function Invite($dbeeid)
	{
		$dash_obj = new Admin_Model_Deshboard();
		$ddd = $dash_obj->inviteuser($dbeeid);
		
		if($ddd>0)
			return $ddd;
		else
			return;
	}	
	

}


?>