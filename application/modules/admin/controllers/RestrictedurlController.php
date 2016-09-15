<?php

class Admin_RestrictedurlController extends IsadminController
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
      $linkurl   = new Admin_Model_Restrictedurl();
      $data = $linkurl->getlinkurl();
      $paginator = Zend_Paginator::factory($data);      
      $page = $this->_getParam('page',1);
      $paginator->setItemCountPerPage(20);      
      $paginator->setCurrentPageNumber($page);     
      $this->view->paginator = $paginator;
      $this->view->linkurl = $data;
      $this->view->pageCount = count($data);
      $this->view->totallink = count($data);
    }

    public function check_website($site_url){
    	$SiteURL=$site_url;
    	$sub_site_url=substr(trim($SiteURL), 0, 4);
    	if($sub_site_url!='http') $SiteURL='http://'.$SiteURL;
    	return $SiteURL;
    }
    
    public function addurlAction()
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
	    	$url1 = $filter->filter($this->_request->getPost('linkurl'));
	    	$url = $this->check_website($url1);	    	
	    	$linkurl   = new Admin_Model_Restrictedurl();	    	
	    	$data = array('linkurl'=>$url,'clientID'=>clientID);	    	
	    	$insertid = $linkurl->addurl($data);	
            $data2['total']= $linkurl->gettotal();
	    	
	    		$data2['content']='<li id="urlid_'.$insertid.'" data-id="'.$insertid.'">
				<i class="fa fa-link"></i>	'.$url.' </div>
				</li>';
	    		
	    	 	
	    	return $response->setBody(Zend_Json::encode($data2));
        }
    }
    
    public function profanityfilterAction()
    {
    	$request = $this->getRequest()->getParams();
    	if(isset($request['searchfield']) && $request['searchfield']!='')
    	{
    		$seachfieldChk = $request['searchfield'];
    		$namespace->searchfield 	= 	 $seachfieldChk;
    		$this->view->seachfield	=	 $seachfieldChk;
    	}
    	else
    		$seachfield = '';
    
    	if(isset($request['orderfield']) && $request['orderfield']!='')
    	{
    		$orderfieldChk = $request['orderfield'];
    		$namespace->orderfield 	= 	$orderfieldChk;
    		$this->view->orderfield	=	$orderfieldChk;
    	}
    	else
    		$orderfield = '';
    
    	$this->view->data = $this->deshboard->profanityfilter($seachfieldChk,$orderfieldChk);
    	$this->view->totWords = 0;
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
    		$url1 = $filter->filter($this->_request->getPost('linkurl'));
    		$url = $this->check_website($url1);
    		$linkurl   = new Admin_Model_Restrictedurl();
    		
    		$data = $linkurl->getrestrictedurl($url1);
    	foreach($data as $value):
    		$content.='<li id="urlid_'.$value['ID'].'" data-id="'.$value['ID'].'">
    		<i class="fa fa-link"></i>	'.$value['linkurl'].' </div>
    	
    		</li>';
    	endforeach;
    		$data2['content'] = $content;
    
    		return $response->setBody(Zend_Json::encode($data2));
    	}
    }
 public function deleteurlAction()
    {  
        $response = $this->getResponse();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        {
            $filter = new Zend_Filter_StripTags();
            $linkid = (int)$this->_request->getPost('linkurl');
            $linkurl   = new Admin_Model_Restrictedurl();
            $del = $linkurl->dellinkurl($linkid); 
        }  
        $data = 'URL deleted successfully';
        return $response->setBody(Zend_Json::encode($data));
    }

}    

