<?php
class PostController extends IsController
{
    public function init()
    {
        parent::init();
        $storage = new Zend_Auth_Storage_Session ();
        $auth = Zend_Auth::getInstance ();
        if ($this->getRequest()->isXmlHttpRequest() && $auth->hasIdentity ()) 
        {
            // Disable the main layout renderer
            $this->_helper->layout->disableLayout();
            // Do not even attempt to render a view
            $this->_helper->viewRenderer->setNoRender(true);
        }else{
            $errorMessage['error'] = 'Somethings went wrong please try again later';
            $this->jsonResponse('error', 200, $errorMessage); 
        }   
    }
    protected function jsonResponse($status, $code, $html, $message = null)
    {
        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            -> setHttpResponseCode($code)
            ->setBody(Zend_Json::encode(array("status"=>$status, "html"=>$html, "message"=>$message)))
            ->sendResponse();
            exit;
    }

    public function getdataAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        {    
           $page = $this->_request->getPost('page');
           $type = $this->_request->getPost('type');
           $resultData = $this->myhome_obj->getTypeWiseDbee($page,$type,$this->_userid);
           $dbeelist = $this->view->partial('partials/dbeerow.phtml', array('Social_Content_Block'=>$this->Social_Content_Block,'myhome_obj'=>$this->myhome_obj,
                        'resultData'=>$resultData,
                        'commonmodel_obj'=>$this->commonmodel_obj,'dbeeCommentobj'=>$this->dbeeCommentobj,
                        'myclientdetails'=>$this->myclientdetails,
                        'userid'=>$this->_userid,'plateform_scoring'=>$this->plateform_scoring,'adminpostscore'=>$this->adminpostscore));
            $this->jsonResponse('success', 200, $dbeelist);         
        }
        else
        {
            $errorMessage = $result->getMessages();
            $errorMessage['error'] = 'Somethings went wrong please try again later';
            $alert = $this->view->partial('partials/alert.phtml', array('alert'=>$errorMessage['error'], 'alertClass'=>'alert-error'));
            $html = array("alert"=>$alert);     
            $this->jsonResponse('error', 401, $html, $errorMessage['error']);
        }
    }
}
