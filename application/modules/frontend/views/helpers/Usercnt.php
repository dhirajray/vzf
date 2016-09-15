<?php
class Zend_View_Helper_Usercnt{
	
	protected $request;
	
	/* public function __construct($request)
	{
		$this->request = $request;
	} */
	public function Usercnt($dbeeid)
	{
			$comment_obj = new Application_Model_Comment();
			$count = $comment_obj->totacommentgroup($dbeeid);			
			return $count;		
	}
	
	
	}


?>