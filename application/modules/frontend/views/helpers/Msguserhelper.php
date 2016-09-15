<?php
class Zend_View_Helper_Msguserhelper{
	
	protected $request;
	
	/* public function __construct($request)
	{
		$this->request = $request;
	} */
	public function Msguserhelper($userid)
	{
			$myhome_obj = new Application_Model_Myhome();
			$resultuser = $myhome_obj->getrowuser($userid);			
			return $resultuser;		
	}
}
?>