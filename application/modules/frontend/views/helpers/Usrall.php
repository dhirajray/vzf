<?php
class Zend_View_Helper_Usrall{
	
	protected $request;
	
	/* public function __construct($request)
	{
		$this->request = $request;
	} */
	public function Usrall($dbeeid)
	{
			$myhome_obj = new Application_Model_Myhome();
			return  $myhome_obj->getrowuser($dbeeid);			
			
	}
	
	
	}


?>