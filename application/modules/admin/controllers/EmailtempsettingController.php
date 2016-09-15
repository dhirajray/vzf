<?php

class Admin_EmailtempsettingController extends IsadminController
{
	private $options;
	
	/**
	 * Init
	 * 
	 * @see Zend_Controller_Action::init()
	 */

    public function init()
    {   	
	    $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
        parent::init();
    }

     /**
     * Index Controller
     */
    public function indexAction()
    {
    	$request = $this->getRequest()->getParams();
    	$deshboard   = new Admin_Model_Deshboard();
      $emailTemplate = $deshboard->getGroupemailtemplate('admin');
      $this->view->emailTemplate = $emailTemplate;
      if($request['areatype']){
        $this->_helper->layout()->disableLayout();
        $emailTemplate = $deshboard->getGroupemailtemplate($request['areatype']);
        $value='';
        foreach($emailTemplate as $key => $etempval){
        $value .='<option value="'.$etempval['id'].'" id="'.$etempval['id'].'" caseid="'.$etempval['case'].'">
        '.$etempval['keyword'].'</option>';
        }
        echo $value;exit();
      }                        
        $emailTemplatejson = $deshboard->getfieldsfromtable(array('id','emailtemplatejson','htmllayout'),'adminemailtemplates',1,1);
        $this->view->emailTemplatejson = json_decode($emailTemplatejson[0]['emailtemplatejson'], true);       
    }
   
    public function emailtempletechangeAction()
    {
        $this->_helper->layout()->disableLayout();
        $request = $this->getRequest()->getParams();
        $emailid = $this->_request->getPost('etemp_id');
        $deshboard   = new Admin_Model_Deshboard();
        if($emailid){
              $emailTemplate = $deshboard->getGroupemailtemplateone($emailid);
              $emailTemplate_body = html_entity_decode($emailTemplate[0]['body']);
              echo $emailTemplate_body.'~#~'.$emailTemplate[0]['title'].'~#~'.$emailTemplate[0]['footertext'].'~#~'.$emailTemplate[0]['subject'];exit();
            } 
    }
    public function emailtempleteresetAction()
    {
        $this->_helper->layout()->disableLayout();
        $request = $this->getRequest()->getParams();
        $emailid = $this->_request->getPost('etemp_id');
        $deshboard   = new Admin_Model_Deshboard();
        if($emailid){
              $emailTemplate = $deshboard->getGroupemailtemplateone($emailid);
              $emailTemplate_body = html_entity_decode($emailTemplate[0]['defaultbody']);
              $deshboard->updatedata_global('emailtemplates',array('body'=> $emailTemplate[0]['defaultbody']),'id',$emailid);
             
              echo $emailTemplate_body.'~#~'.$emailTemplate[0]['title'].'~#~'.$emailTemplate[0]['footertext'].'~#~'.$emailTemplate[0]['subject'];exit();
            } 
    }
    public function emailtempleteAction()
    {
        $request = $this->getRequest()->getParams();
        $this->_helper->layout()->disableLayout(); 
        $deshboard   = new Admin_Model_Deshboard(); 
        $emailTemplate = $deshboard->getGroupemailtemplateone();
        $this->view->emailTemplate   = $emailTemplate;

        $deshboard   = new Admin_Model_Deshboard();
        $emailTemplatejson = $deshboard->getfieldsfromtable(array('id','emailtemplatejson','htmllayout'),'adminemailtemplates','clientID',clientID);

        $this->view->emailTemplatejson = json_decode($emailTemplatejson[0]['emailtemplatejson'], true); 
        
    }
    public function emailbannerAction()
    {
        $this->_helper->layout->disableLayout();
        $request = $this->getRequest()->getPost();
        
        $image_info = getimagesize($_FILES["file"]["tmp_name"]);
        if ($image_info[0] < 1 && $image_info[1] < 1) {
            echo "Please use valid image to upload";
            exit;
        } else if (!empty($_FILES)) {
            
            $ext     = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $picture = strtolower(time() . '.' . $ext);
            
            if (copy($_FILES['file']['tmp_name'], './adminraw/img/emailbgimage/' . $picture)) {
                echo $picture;
            }
            exit;
        }
          
    }
     public function emailbannersuccesAction()
    {
      $this->_helper->layout->disableLayout();
      $this->_helper->viewRenderer->setNoRender(true);
      $request = $this->getRequest()->getPost();
      $deshboard   = new Admin_Model_Deshboard();  
      $firstformval = $this->_request->getPost('firstformval'); 
      $emailtempid = $this->_request->getPost('emailtempid');
      $iframcontentval = stripcslashes($this->_request->getPost('iframcontentval'));
      $iframcontentval = htmlentities($iframcontentval);
      $subjectMsgval = stripcslashes($this->_request->getPost('subjectMsgval'));
      $subjectMsgval = htmlentities($subjectMsgval);
      $footerMsgval = $this->_request->getPost('footerMsgval');
      $templatetype = $this->_request->getPost('templatetype');
      $fromname = $this->_request->getPost('fromname');
      $fromnamedata  = array('fromName'=> $fromname);
      $Insertformnamedata =  $deshboard->updatedata_global('domainVariables',$fromnamedata,'clientID',clientID);
      $firstformval['footerMsgval'] = $footerMsgval;
      $datavalbody  = array('body'=> $iframcontentval,'subject'=> $subjectMsgval);
      $datavalfooter  = array('footertext'=> $footerMsgval);
      if($templatetype!='nosel'){
         $Insertdatabody =  $deshboard->updatedata_global('emailtemplates',$datavalbody,'id',$emailtempid); 
      } 
      $Insertdatafooter =  $deshboard->updatedata_global('emailtemplates',$datavalfooter,'clientID',clientID);     
      $form_template = json_encode($firstformval);
      $jsondataval  = array('emailtemplatejson'=> $form_template,);

      $Insertjsondata =  $deshboard->updatedata_global('adminemailtemplates',$jsondataval,'clientID',clientID);
      echo $Insertjsondata;exit();
    }

}

