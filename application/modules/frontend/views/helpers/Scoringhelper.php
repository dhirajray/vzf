<?php
class Zend_View_Helper_Scoringhelper{
	public function Scoringhelper($commentid,$userid)
	{
		$comment = new Application_Model_Comment();	
		$score = $comment->getscore($userid,$commentid);	
		return $score;	
	}
}

?>