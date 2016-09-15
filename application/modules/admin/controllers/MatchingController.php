<?php

class Admin_MatchingController extends IsadminController
{

  private $options; 
  
  /**
   * Init
   * 
   * @see Zend_Controller_Action::init()
   */

    public function init()
    { 
      $deshboard   = new Admin_Model_Deshboard();    
      $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
      parent::init();
    }    

    
    public function indexAction()
    {  
      $usertype   = new Admin_Model_User();
      $request = $this->getRequest()->getParams();
      $callfun = 'getmatching'.$request['type'];
      $data = $usertype->$callfun();
   
      $paginator = Zend_Paginator::factory($data);      
      $page = $this->_getParam('page',1);
      $paginator->setItemCountPerPage(20);      
      $paginator->setCurrentPageNumber($page);     
      
      $this->view->type = $request['type'];
      $this->view->paginator = $paginator;
      $this->view->typedata = $data;
      $this->view->pageCount = count($paginator);
      $this->view->totallink = count($data); 
    }

   
    public function deleteusertypeAction()
    {  
    	$response = $this->getResponse();
    	$this->_helper->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	$response->setHeader('Content-type', 'application/json', true);
    	if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
    	{
	    	$filter = new Zend_Filter_StripTags();
	    	$usertypeid = (int)$this->_request->getPost('ID');
	    	$usertypeobj   = new Admin_Model_Usertype();
	    	$del = $usertypeobj->dellinkurl($usertypeid); 
    	}  
    	$data = 'URL deleted successfully';
    	return $response->setBody(Zend_Json::encode($data));
    }
    

}    

