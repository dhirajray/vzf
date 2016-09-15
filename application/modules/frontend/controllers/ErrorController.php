<?php
class ErrorController extends IsController
{

    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
       // print_r($errors); die;
        $params = $this->getRequest()->getParams();
       
        //$this->_helper->layout()->disableLayout();
        //$this->_helper->viewRenderer->setNoRender(true);
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
                default:
                    $this->getResponse()->setHttpResponseCode(500);
                    $this->view->message = 'Application error';
                    break;
            }
        }
        // Log exception, if logger available
        /*if ($log = $this->getLog()) {
            $log->crit($this->view->message, $errors->exception);
        }*/

        

        // conditionally display exceptions

        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }

          $this->view->exception = $errors->exception;
        /****for email ****/ 
        //mann.delus@gmail.com,anildbee@gmail.com,porwal.deshbandhu@gmail.com       
        /*$EmailTemplateArray = array('Email' => $this->myclientdetails->customEncoding('deshbandhu.dbee@gmail.com'),
                                    'errormessage' =>    $this->view->message,
                                    'controller' => $params['controller'],
                                    'action' => $params['action'],
                                    'url' => $_SERVER['REQUEST_URI'],
                                    'case'=>10);*/        
        
      
        $message = $errors->exception->getMessage();
        //exit; 
        //$this->dbeeComparetemplateOne($EmailTemplateArray);
        /****for email ****/
       
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



