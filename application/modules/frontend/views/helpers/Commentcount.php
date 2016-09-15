<?php
class Zend_View_Helper_Commentcount{
	
	protected $request;
	
	/* public function __construct($request)
	{
		$this->request = $request;
	} */
	public function Commentcount($dbeeid)
	{
			$comment_obj = new Application_Model_Comment();
			$count = $comment_obj->totacomment($dbeeid);			
			return $count;		
	}
	
	
	}


?>