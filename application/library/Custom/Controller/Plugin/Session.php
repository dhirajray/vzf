<?php

class Custom_Controller_Plugin_Session extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch (Zend_Controller_Request_Abstract $request)
    { 	
    	//echo $request->module; die;
    	if($request->module == 'admin')
    	{ 
			$authNamespace = new Zend_Session_Namespace('identify');
			$authNamespace->setExpirationSeconds((1209600));
		    $exceptions = array('login');
		    if(empty($authNamespace->id) || $authNamespace->id == NULL) 
		    { 
		    	if(!in_array($request->controller,$exceptions)) 
		    	{ 
					$request->setModuleName('frontend');
					$request->setControllerName('myhome');
					$request->setActionName('index');
					//header('location: '.BASE_URL);
		    	}
		    } 
		    //return true;
    	}
    	$response = $this->getResponse();
        $body = $response->getBody();
        $body = preg_replace('|\s+|', '', $body);
        $response->setBody($body);
    	
    }
}