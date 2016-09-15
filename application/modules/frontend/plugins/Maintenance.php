<?php
class Application_Plugin_Maintenance extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request) { 
        if ($_SERVER['REMOTE_ADDR']!= "182.68.194.242" && false) {
            $request->setControllerName('index')
                    ->setActionName('maintenance')
                    ->setDispatched(true);
        }
    }
}
