<?php

class Admin_UsertypeController extends IsadminController
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

    /**  * Message Controller    */
    public function indexAction()
    {  
      $usertype   = new Admin_Model_Usertype();
      $data = $usertype->getusertype();
      $paginator = Zend_Paginator::factory($data);      
      $page = $this->_getParam('page',1);
      $paginator->setItemCountPerPage(20);      
      $paginator->setCurrentPageNumber($page);  
      $this->view->usertype = $paginator;
      $this->view->typedata = $data;
      $this->view->pageCount = count($data);
      $this->view->totallink = count($data);
      $this->view->pageCount = count($data);
     return;
    }

    public function check_website($site_url){
    	$SiteURL=$site_url;
    	$sub_site_url=substr(trim($SiteURL), 0, 4);
    	if($sub_site_url!='http') $SiteURL='http://'.$SiteURL;
    	return $SiteURL;
    }
    
    public function addusertypeAction()
    {
    	$data = array();
    	$data2 = array();
       
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $filter = new Zend_Filter_StripTags();
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {     	
	    	$usertype = $filter->filter($this->_request->getPost('usertype'));
	    	
	    	$typeref = 'vip';
	    	   	
	    	$usertypeobj   = new Admin_Model_Usertype();	    	
	    	$data = array('TypeName'=>$usertype,'clientID'=>clientID,'Typeref'=>$typeref);
	    	$chk = $usertypeobj->chkusertype($usertype,$typeid);
	    	//echo count($chk);
	    	if(count($chk)==0){
	    	$insertid = $usertypeobj->addusrtype($data);	
	    	
	    	if($insertid==0 || $insertid==6 || $insertid==10){
	    		$usertypeobj   = new Admin_Model_Usertype();
	    		$del = $usertypeobj->dellinkurl($insertid);
	    		$insertid = $usertypeobj->addusrtype($data);	
	    	}
	    	
	    $data2['content']='<li id="urlid_'. $insertid.'" data-id="'.$insertid.'"><div class="utype" data-type="'. $usertype.'" data-id="'. $insertid.'" >
	    		<i class="fa fa-user fa-lg" style="width: 20px; height: 20px;"></i>											
		'.$usertype.'<div class="Rtlist"><a class="btn btn-danger btn-mini" href="javascript:void(0);" userid="'.$usertype.'" id="utypedelete" gname="146" type="delete">Delete</a>
		<span class="sprt"> </span>	<a class="btn btn-green btn-mini "  href="javascript:void(0);" id="utypeedit" type="edit">&nbsp;&nbsp;Edit &nbsp;</a>		</div>
		</div></div></li>';
	    	}else{
	    		$data2['error']=true;
	    	}    		
	    	 	
	    	return $response->setBody(Zend_Json::encode($data2));
        }
    }
    
    public function updateusertypeAction()
    {
    	$data = array();
    	$data2 = array();
    
    	$this->_helper->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	$filter = new Zend_Filter_StripTags();
    	$response = $this->getResponse();
    	$response->setHeader('Content-type', 'application/json', true);
    	if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
    	{
    		$usertype = $filter->filter($this->_request->getPost('usertype'));
    		$typeid = $filter->filter($this->_request->getPost('id'));    		
    		
    		$usertypeobj   = new Admin_Model_Usertype();
    		$chk = $usertypeobj->chkusertype($usertype);
    		
    		if(count($chk)==0){
			
    		$data = array('TypeName'=>$usertype);
    		$insertid = $usertypeobj->updateusrtype($data,$typeid);    
    		$data2['content']='<li id="urlid_'.$insertid.'" data-id="'.$insertid.'">
    		<i class="fa fa-user fa-lg" style="width: 20px; height: 20px;"></i>	'.$usertype.' </div>
    		<div class="pull-right">
    		<a   href="#" class="linurldelicon deletelinkurl" title="delete"> </a>
    		</div>
    		</li>';
			}else{
				$data2['error'] = "User type exit";
			}
    
    		return $response->setBody(Zend_Json::encode($data2));
    	}
    }
    
    public function searchurlAction()
    {
    	$data = array();
    	$data2 = array();
    	$content ='';
    	$this->_helper->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	$filter = new Zend_Filter_StripTags();
    	$response = $this->getResponse();
    	$response->setHeader('Content-type', 'application/json', true);
    	if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
    	{
    		$url1 = $filter->filter($this->_request->getPost('usertype'));
    		
    		$usertypeobj   = new Admin_Model_Usertype();
    		
    		$rs = $usertypeobj->getusertypesearch($url1);
    		
    	foreach($rs as $value):
    		$content.='<li id="urlid_'.$value['ID'].'" data-id="'.$value['ID'].'">
    		<i class="fa fa-user fa-lg" style="width: 20px; height: 20px;"></i>	'.$value['TypeName'].' </div>
    		<div class="pull-right">
    		<a   href="#" class="linurldelicon deletelinkurl" title="delete"> </a>
    		</div>
    		</li>';
    	endforeach;
			$data2['total'] = count($rs);
    		$data2['content'] = $content;
    
    		return $response->setBody(Zend_Json::encode($data2));
    	}
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

