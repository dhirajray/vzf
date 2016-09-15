<?php
class Application_Plugin_Checkcotrolleraction extends Zend_Controller_Plugin_Abstract {
    public function preDispatch(Zend_Controller_Request_Abstract $request){
        $controllerasr = $this->getRequest()->getControllerName();
        $actionasr = $this->getRequest()->getActionName();         
        $front = Zend_Controller_Front::getInstance();
        $dispatcher = $front->getDispatcher();
        $test = new Zend_Controller_Request_Http();
        $test->setParams(array('action' => $actionasr,'controller' => $controllerasr,));
        if ($dispatcher->isDispatchable($test)) {
            $controllerClassName = $dispatcher->formatControllerName( $request->getControllerName() ); 
            $controllerClassFile = $controllerClassName . '.php'; 
            if ($request->getModuleName() != $dispatcher->getDefaultModule()) { 
                    $controllerClassName = ucfirst($request->getModuleName()) . '_' . $controllerClassName; 
            } 
            try { 
                    require_once 'Zend/Loader.php'; 
                    Zend_Loader::loadFile($controllerClassFile, $dispatcher->getControllerDirectory($request->getModuleName())); 
                    $actionMethodName = $dispatcher->formatActionName($request->getActionName()); 
                    if (@in_array($actionMethodName, get_class_methods($controllerClassName))) { 
                            return true; die;
                    }else{ 
                          $asr = new Zend_Controller_Action_Helper_Redirector;
                          $asr->gotoUrl('/Urlerror/unvalidateurl')->redirectAndExit();
                    } 
                     
            } catch(Exception $e) { 
                    return false; 
            } 
                  
        }
        else{
                    $asr = new Zend_Controller_Action_Helper_Redirector;
                    $asr->gotoUrl('/Urlerror/unvalidateurl')->redirectAndExit(); 
            } 
    }    
	
}
