<?php

class Admin_ErrorController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->headTitle("ERROR | Backend");
        $this->view->url = $this->_request->getBaseUrl();
    }

     public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        $params = $this->getRequest()->getParams();
        //print_r( $errors->type); exit;
        
        if (!$this->getRequest()->isXmlHttpRequest())
        {
            switch ($errors->type) 
            {
                case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
                    $this->getResponse()->setHttpResponseCode(404);
                    $this->view->message = 'Noroute';
                    $this->view->code = '404';
                    break;
                case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
                    $this->getResponse()->setHttpResponseCode(404);
                    $this->view->message = 'Nocontroller';
                    $this->view->code = '404';
                    break;
                case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                    $this->getResponse()->setHttpResponseCode(404);
                    $this->view->message = 'Noaction';
                    $this->view->code = '404';
                    break;
                case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER:
                   
                    $this->getResponse()->setHttpResponseCode(404);
                    $this->view->message = 'Noaction';
                    $this->view->code = '404';
                     
                    break;    
                default:
                    $this->getResponse()->setHttpResponseCode(500);
                    $this->view->message = 'Application error';
                    break;
            }
        }
        // Log exception, if logger available
        if ($log = $this->getLog()) {
            $log->crit($this->view->message, $errors->exception);
        }

        

        // conditionally display exceptions

        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }

         $this->view->exception = $errors->exception;
        
      
        $message = $errors->exception->getMessage();
        
       
    }



    public function getLog()

    {

        $bootstrap = $this->getInvokeArg('bootstrap');

        if (!$bootstrap->hasPluginResource('Log')) {

            return false;

        }

        $log = $bootstrap->getResource('Log'); 

        return $log;

    }


}

