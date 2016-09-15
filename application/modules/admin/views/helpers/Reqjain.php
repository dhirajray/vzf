<?php

class Zend_View_Helper_Reqjain{

	protected $request;
	
	
	
	/* public function __construct($request)
	
	{
	
	$this->request = $request;
	
	} */
public function Attendies($dbeeid,$type)
	{
		$dash_obj = new Admin_Model_Deshboard();
		$ddd = $dash_obj->inviteuser($dbid);
		return $ddd;
	}
	
	
	

}


?>