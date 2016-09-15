<?php

class Custom_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
	public function preDispatch (Zend_Controller_Request_Abstract $request)
	{
		$controller = $request->controller;
	    $action = $request->action;
	    $module = $request->module;
	    
	    if ($module == 'admin')
	    {
	    		
	    	if($this->_request->getParam('id')!="" && $controller=='dashboard' && $action=='post')
	        {
	        	
	         $searchparam = new Zend_Session_Namespace();
	         $searchparam->searchdb = $this->_request->getParam('id');
	        }

			$authNamespace = new Zend_Session_NameSpace('identify');
			$authNamespace->setExpirationSeconds((1209600));

			$role_id = $authNamespace->role; 
			$authNamespace->module = $module;
			$Roledetails = new Admin_Model_User();
			$AllRoledetails = $Roledetails->Rolesforacl($role_id);

            switch($AllRoledetails[0]['role_id']):
				case $AllRoledetails[0]['role_id']:
					$role = $AllRoledetails[0]['role'];
				break;
			endswitch;
			$acl = Zend_Registry::get('aclTawarlina');
          

			if (!$acl->isAllowed($role, $controller, $action)) 
		    {
		    	
				$request->setModuleName('adminlogin');
				$request->setControllerName('index');
				$request->setActionName('login');
		    }
		    else if (!$acl->isAllowed($role, $controller, $action)  && $role_id=='') 
		    {
		    	
				$request->setModuleName('adminlogin');
				$request->setControllerName('index');
				$request->setActionName('login');
		    }
		    else if($role_id==''){

		    	$request->setModuleName('adminlogin');
				$request->setControllerName('index');
				$request->setActionName('login');
		    }
	    }
	    if ($module =='frontend')
	    {
            $authNamespace = new Zend_Session_NameSpace('identify');
            $authNamespace->setExpirationSeconds((1209600));
			$role_id = $authNamespace->role;
			$authNamespace->module = $module;
			$Roledetails = new Admin_Model_User();
			$AllRoledetails = $Roledetails->Rolesforacl($role_id);
	    }	
	    
	}
	
}