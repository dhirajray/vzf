<?php 
class Zend_View_Helper_Polloptionlhelper{	
	
	public function Polloptionlhelper($dbeeid,$polid,$userid)
	{	
		$poloption = new Application_Model_Polloption();
		$result = $poloption->getmyvoteresdbee($dbeeid,$polid,$userid);
		return count($result);
	}
	

}


?>

