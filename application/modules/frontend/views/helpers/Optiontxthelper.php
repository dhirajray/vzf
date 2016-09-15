<?php
class Zend_View_Helper_Optiontxthelper{
	
	protected $request;
	
	/* public function __construct($request)
	{
		$this->request = $request;
	} */
	public function Optiontxthelper($user,$db)
	{
		$poll = new Application_Model_Polloption();
		$optiontxt = $poll->getvotename($user,$db);			
		return $optiontxt['OptionText'];	
	}
}


?>