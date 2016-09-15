<?php

class Zend_View_Helper_Chkisadded{
	protected $request;
	public function Chkisadded($email,$isadded,$id)  
	{
		$id = (int)$id;	
		if($isadded==0){		
			$common_obj = new Admin_Model_Common();		
			if($common_obj->chkemail($email)){						
				$invite_obj = new Admin_Model_Invite();
				$data = array('isadded'=>'1');
				$invite_obj->update($data,$id);	
				return "Account created";
			}else{				
				return "Pending";
			}			
		}else{			
			return "Account created";			
		}
	}

}


?>