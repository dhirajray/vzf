<?php

class UpdateController extends IsloginController
{  

	public function init()
	{
         parent::init();
         $this->myclientdetails = $this->myclientdetails = new Application_Model_Clientdetails();
	}
    
	public function indexAction() 
    {
       
    }

    public function postreportAction()
    {
        $request = $this->getRequest()->getParams();
        $this->_helper->_layout->disableLayout('layout') ;

        $postid = base64_decode($request['m_-_xxp=t']);
        
        $this->view->postidEncode =$request['m_-_xxp=t'];
        $this->view->postid =$postid;

        $objinf =new Admin_Model_Influence();
        $this->view->mostinfuser=$objinf->MostInfluenceUser($postid);
        //exit;

    }

    public function getpdfAction()
    {
        require_once 'includes/pdfcrowd.php';

        //$this->_helper->_layout->setLayout('admin/layout.phtml') ;//other-layout.phtml

        try
        {  
            $request    =   $this->getRequest()->getParams();
            $convURL    =   BASE_URL."/update/postreport/m_-_xxp=t/".$request['postid'];  
            // create an API client instance
            $client = new Pdfcrowd("Ragini", "a6df1ab7187bddaf5fecd575b1a5a19b");

            // convert a web page and store the generated PDF into a $pdf variable
            $pdf = $client->convertURI($convURL);

            // set HTTP response headers
            header("Content-Type: application/pdf");
            header("Cache-Control: max-age=0");
            header("Accept-Ranges: none");
            header("Content-Disposition: attachment; filename=\"google_com.pdf\"");

            // send the generated PDF 
            echo $pdf;
        }
        catch(PdfcrowdException $why)
        {
            echo "Pdfcrowd Error: " . $why;
        }
        exit;
    }


    public function cookiesAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $filter = new Zend_Filter_StripTags();
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        {
             $dataval     = $filter->filter(stripslashes($this->_request->getPost('subject')));
           
            $cookiesdata = $this->myclientdetails->getRowMasterfromtable('tblStaticPages',array('CookiePolicy'),'clientID ',clientID);
           
            if($dataval="cookies"){
                       $content ='<div><h2><strong>Cookie Policy</strong></h2>';
                       $content.=nl2br($cookiesdata['CookiePolicy']);
                       $content.='</div>';
            }           
            $data['status'] = 'success';
            echo $content; die;
        }
        //return $response->setBody(Zend_Json::encode($data));
    }

     public function termsofuseAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        //$response = $this->getResponse();
        //$response->setHeader('Content-type', 'application/json', true);
        $filter = new Zend_Filter_StripTags();
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        {
             $dataval     = $filter->filter(stripslashes($this->_request->getPost('subject')));
           
            $cookiesdata = $this->myclientdetails->getRowMasterfromtable('tblStaticPages',array('terms_of_use'),'clientID ',clientID);
           
            //if($dataval="cookies"){
                       $content.='<div><h2><strong>Terms Of Use</strong></h2>';
                       $content.=nl2br($cookiesdata['terms_of_use']);
                       $content.='</div>';
           // }           
            $data['status'] = 'success';
           echo $content; die;

           // $encoded_rows = array_map('utf8_encode', $data);
        }
       // return $response->setBody($data);
    }

    public function termsAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $filter = new Zend_Filter_StripTags();
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        {
            $dataval = $filter->filter(stripslashes($this->_request->getPost('subject')));
            $result = $this->commonmodel_obj->getStaticPage();
            $content.='<div><h2><strong>Terms & Conditions</strong></h2>';
            $content.=nl2br($result['terms']);
            $content.='</div>';
            $data['status'] = 'success';            
            echo $content; die;
        }
        //return $response->setBody(Zend_Json::encode($data));
    }
    public function coockieupdateAction()
    {
        $request = $this->getRequest()->getParams();    
        $data = array();
        $response = $this->getResponse();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest()  && $this->getRequest()->getMethod() == 'POST' ) {      
            $request   = $this->getRequest();
            $user      = $request->getpost('user');


            if(trim($user)!='')
            {
                $data = array('CookieAccept'=>1);
                $myhome_obj  = new Application_Model_Myhome();
                $sucess = $this->myclientdetails->updatedata_global('tblUsers',$data,'UserID',$user);
                if($sucess){
                    $data['user'] = $user;
                }
            }
            else
            {
                //setcookie("CookieAcceptghost", date('Y-m-d'), time()+3600, '/');                
                $_SESSION['CookieAcceptghost'] =   date('Y-m-d');              
            }
            $data['status'] = $_SESSION['CookieAcceptghost'];
         }
         else
         {
            $data['status'] = 'error';
            $data['message'] = 'Some thing went wrong here please try again';   
         }
         $data['user'] = $user;
        return $response->setBody(Zend_Json::encode($data));
    }

    public function aboutAction()
    {

        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        {
            $result = $this->commonmodel_obj->getStaticPage();  
            $data['status'] = 'success';
            $data['content']= $result['aboutus'];
        }
        return $response->setBody(Zend_Json::encode($data));
    }

    public function privacyAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        {
            $result = $this->commonmodel_obj->getStaticPage();
            $data['status'] = 'success';
            $content.='<div><h2><strong>Privacy Policy</strong></h2>';
            $content.=nl2br($result['privacy']);
            $content.='</div>';

            echo $content; die;
        }
        //return $response->setBody(Zend_Json::encode($data));
    }

    public function feedbackAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        {
            $this->user_session->feedbacktoken = md5(time());
            $content = '<div id="signuplastWrapper" class="feedbackFormFront">
                          <h2>Feedback</h2>
                        <div style="margin-bottom:10px;">We value all feedback that helps us to improve user experience.</div>
                        <div id="passform" class="postTypeContent">
                        <div class="formRow"><div class="formField"><input type="text" name = "feedbackname" id="feedbackname" value="" class="textfield" placeholder="Name" /></div></div>
                        <div class="formRow"><div class="formField"><input type="text" name = "feedbackemail" id="feedbackemail" value="" class="textfield" placeholder="Email"/></div></div>
                        <div class="formRow"><div class="formField"><textarea id="feedbacktext" name="feedbacktext" class="textareafield"  placeholder="Write your feedback here"></textarea></div></div>
                        </div>
                        <div id="feedback-message" style="padding:10px; margin-top:5px; font-size:14px; font-weight:bold; color:#999999;"></div>
                      </div>';
            
            $data['status'] = 'success';
            $data['content']= $content;

        }else{
             $data['status'] = 'error';
             $data['message'] = 'Some thing went wrong here please try again';
        }   
        return $response->setBody(Zend_Json::encode($data));
    }

    public function sendfeedbackAction()
    {
            
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        {
            
            $feedbacktext = $this->_request->getPost('feedbacktext');
            $feedbackname = $this->_request->getPost('feedbackname');
            $feedbackemail = $this->_request->getPost('feedbackemail');

            $validate = $this->commonmodel_obj->authorizeToken($this->_request->getPost('SessUser__'),$this->_request->getPost('SessId__'),$this->_request->getPost('SessName__'),$this->_request->getPost('Token__'),$this->_request->getPost('Key__')); 

            if ($validate==false) 
            {
                $data['status'] = 'error';
                $data['message'] = 'Some thing went wrong here please try again';
            }else if($feedbackname=='')
            {
                $data['status'] = 'error';
                $data['message'] = 'Please enter your name';
            }
            else if($feedbackemail=='')
            {
                $data['status'] = 'error';
                $data['message'] = 'Please enter your email';
            }
            else if($feedbacktext=='')
            {
                $data['status'] = 'error';
                $data['message'] = 'Please enter your feed back message';
            }
            else
            {
                $userData['name'] = $feedbackname;
                $userData['email'] = $feedbackemail;
                $userData['text'] = $feedbacktext;
                $userData['clientID'] = clientID;
                $userData['DATETIME'] = date('Y-m-d H:i:s');



                $id = $this->Myhome_Model->insertUserFeedBack($userData);
              
                //$admindata = $this->myclientdetails->getfieldsfromtable(array('email'),'users','front_userid ',adminID);
                $EmailTemplateArray = array('Email' => $this->Adminresult['Email'],
                                            'feedbacktext'  => $feedbacktext,
                                            'case'=> 9);
             

                $this->dbeeComparetemplateOne($EmailTemplateArray); // send mail to admin
            
                $this->notification->commomInsert(15,24,$id,$id,adminID); // Insert for involve activity 
                //print_r( $EmailTemplateArray); exit;
                $data['status'] = 'success';
                $data['message']= 'Thank you for your feedback, a member of our team will be in touch with you very shortly.';
            }
        }
        else
        {
                $data['status'] = 'error';
                $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }

    public function verifyemailAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $request = $this->getRequest()->getParams();
        $uid = base64_decode($request['xggTyhr']);
        $sesUserid= $_SESSION['Zend_Auth']['storage']['UserID'];

        if($uid!='')
        {
            $dataall = $this->myclientdetails->getfieldsfromtable('*','tblUsers','UserID',$uid,'clientID',clientID);  

            $query = "select Email from tblUsers where Email='".$this->myclientdetails->customEncoding($dataall[0]['emailChange'])."' AND UserID !='".$dataall[0]['UserID']."' AND clientID=".clientID;
            $useremailcheck = $this->myclientdetails->passSQLquery($query);
        
            if ( count($useremailcheck)>0) 
            { 
                if($sesUserid!='')
                    $this->_redirect(BASE_URL.'/settings/index/emailverification/false');
                else $this->_redirect(BASE_URL.'/index/index/emailverification/false');
            }
            else
            {
                if($dataall[0]['emailChange']!='')
                {
                    $data = array('Email'=>$this->myclientdetails->customEncoding($dataall[0]['emailChange']), 'emailChange'=>'');
                    $this->myclientdetails->updatedata_global('tblUsers',$data,'UserID',$dataall[0]['UserID']);

                    if($sesUserid!='')
                        $this->_redirect(BASE_URL.'/settings/index/emailverification/true');
                    else $this->_redirect(BASE_URL.'/index/index/emailverification/true');  
                }
                else
                {
                    if($sesUserid!='')
                        $this->_redirect(BASE_URL.'/settings/index/emailverification/invalid');
                    else $this->_redirect(BASE_URL.'/index/index/emailverification/invalid');
                }
            }
           
        }
        else
        {
            if($sesUserid!='')
                $this->_redirect(BASE_URL.'/settings/index/emailverification/xfalse');
            else $this->_redirect(BASE_URL.'/index/index/emailverification/xfalse');  
        }
        exit;
    }

}