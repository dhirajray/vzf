<?php
/**
 * This is site's main class/controller.  added by deshbandhu
 */
class IsController extends Zend_Controller_Action
{
    
    public $_site;
    public $_controller;
    public $_action;
    public $_siteUrl;
    public $_config;
    public $Social_Content_Block;
    public $session_data;
    public $session;
    
    public function init()
    {
        
        // Call method from mother class
        
        parent::init();
        $data = array();        
        // pass config details and vars to views
        $this->_config = Zend_Registry::get("config");
        $auth =  Zend_Auth::getInstance();
        
        $this->view->config = $this->_config;
        $this->view->myclientdetails = $this->myclientdetails = new Application_Model_Clientdetails();
        $this->eventModel = new Application_Model_Event();
        $this->view->globalsecuretoken = '';
        $this->Myhome_Model = new Application_Model_Myhome();
        $this->User_Model = new Application_Model_DbUser();
        $usersite  = new Application_Model_Usersite();
        $Result = $this->User_Model->globalSetting();
        
        $this->expert_model = new Application_Model_Expert();
        $this->special_dbee_session = new Zend_Session_Namespace('special_dbee_session');
        $this->session_name_space   = new Zend_Session_Namespace('User_Session'); 
        $this->dbeecontrollers = $this->view->dbeecontrollers = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
        $actionName  = Zend_Controller_Front::getInstance()->getRequest()->getActionName();

        $where = array('1'=>1);
        $checkclient = $this->myclientdetails->getRowMasterfromtable('tblClient',array('*'),$where);
        
        if($checkclient['clientStatus']==0)
        {   echo'<div style="font-family:Arial; padding:20px; text-align:center; border-radius:10px;">';
            echo"<h1> Site not found</h1>";
            echo"<p> You might have followed an incorrect link.</p><br>";
            echo'</div>';
            die;
        }

        if ($auth->hasIdentity())
        {
            $storage = new Zend_Auth_Storage_Session();  
            $datasocial = $storage->read();
            $this->session_data = $datasocial;
            $this->session = $datasocial;
            $this->_userid = $datasocial['UserID'];
            $this->view->session_data = $datasocial;
            //print_r($datasocial); die;
            if(!is_array($datasocial) && !$this->getRequest()->isXmlHttpRequest()){
                $this->_redirect('/myhome/logout');
            }
            $userresult = $this->myclientdetails->getfieldsfromtable('*','tblUsers','role',1,'clientID',clientID);
            defined('adminID') or define('adminID', $userresult[0]['UserID']);
            $this->view->Adminresult = $this->Adminresult = $userresult[0];          
            $this->view->eventglobal = $Result[0]['event'];
            $this->view->creategrpglobal = $Result[0]['creategrp'];
             if($this->_userid==adminID)
                $this->view->groupbg = $this->groupbg = 1;
            else
               $this->view->groupbg = $this->groupbg = $Result[0]['groupbg'];

            $this->twitter_connect_data = Zend_Json::decode($datasocial['twitter_connect_data']);
            $this->facebook_connect_data = Zend_Json::decode($datasocial['facebook_connect_data']);
            $this->linkedin_connect_data = Zend_Json::decode($datasocial['linkedin_connect_data']);
            $this->view->linkedin_token_secret = $this->linkedin_connect_data['connnected'];
            $this->twitter_token_secret = $this->view->twitter_token_secret = $this->twitter_connect_data['twitter_token_secret'];
            $this->view->facebookid = $this->facebook_connect_data['facebookid'];
            $this->view->twitteridxx = $this->twitter_connect_data['screen_id'];
            $this->view->tsrss = $this->Myhome_Model->chkallrss();

        }
        $this->Expert_Model = new Application_Model_Expert();

        $this->view->session_name_space = $this->session_name_space;
        $redirection_name_space->redirection1 = $this->curPageURL();
        
        $where = array('1'=>1);
        $socialloginabilitydetail = $this->myclientdetails->getRowMasterfromtable('tblloginsocialresource',array('*'),$where);
        $this->view->socialloginabilitydetail = $this->socialloginabilitydetail = $socialloginabilitydetail;
        
        $this->g_index = $Result[0]['google_spider'];
        $this->view->g_index = $Result[0]['google_spider'];
        $this->view->plateform_scoring = $this->plateform_scoring = $Result[0]['plateform_scoring'];
        $this->view->adminpostscore = $this->adminpostscore = $Result[0]['adminpostscore'];
        $this->semantria_seen = $Result[0]['semantria_seen'];
        $this->view->semantria_seen = $Result[0]['semantria_seen'];
        $this->view->IsLeagueOn = $this->IsLeagueOn = $Result[0]['IsLeagueOn'];
        $this->view->userconfirmed = $this->userconfirmed = $Result[0]['userconfirmed'];
        $this->view->allow_user_create_polls = $this->allow_user_create_polls = $Result[0]['allow_user_create_polls'];
        $this->view->addtocontact = $this->addtocontact = $Result[0]['addtocontact'];
        $this->view->allowexperts = $this->allowexperts = $Result[0]['allowexperts'];
        $this->view->allowexperts = $this->allowmultipleexperts = $Result[0]['allowmultipleexperts'];
        defined('allowmultipleexperts') or define('allowmultipleexperts', $Result[0]['allowmultipleexperts']);
        $this->view->IsSurveysOn = $this->IsSurveysOn = $Result[0]['IsSurveysOn'];
        $this->view->Social_Block = $this->Social_Block = $Result[0]['social_account'];
        $this->view->admin_searchable_frontend = $this->admin_searchable_frontend = $Result[0]['admin_searchable_frontend'];
        $this->view->allowedPostWithTwitter = $this->allowedPostWithTwitter = $Result[0]['allowedPostWithTwitter'];
        $this->view->AllowLoginTerms = $this->AllowLoginTerms = $Result[0]['AllowLoginTerms'];

        $this->view->Is_PollComments_On     = $this->Is_PollComments_On     = $Result[0]['Is_PollComments_On'];
        $this->view->PollComments_On_Option = $this->PollComments_On_Option = $Result[0]['PollComments_On_Option'];
        
        $this->view->ShowVideoEvent     = $this->ShowVideoEvent     = $Result[0]['ShowVideoEvent'];
        $this->view->ShowLiveVideoEvent = $this->ShowLiveVideoEvent = $Result[0]['ShowLiveVideoEvent'];
        $this->view->ShowRSS            = $this->ShowRSS            = $Result[0]['ShowRSS'];
        $this->view->showAllUsers       = $this->showAllUsers            = $Result[0]['showAllUsers'];

        $this->notification = new Application_Model_Notification();
        if($this->Social_Block==1)
            $this->Social_Content_Block = 'block'; 
        else
            $this->Social_Content_Block = 'unblock';

        $this->view->Social_Content_Block = $this->Social_Content_Block;
        $this->myhome_obj           = $this->Myhome_Model;
        $this->dbeeCommentobj       = new Application_Model_Comment();
        $this->commonmodel_obj      = new Application_Model_Commonfunctionality();
        $this->profile_obj          = new Application_Model_Profile();
        $this->activity             = new Application_Model_Activities();
        $this->leagueObj            = new Application_Model_League();
        $this->groupModel           = new Application_Model_Groups();
        $this->dbleaguesTable = new Application_Model_Dbleagues();
        $this->usersite  = new Application_Model_Usersite();
        $this->Advert = new Application_Model_Advert();
        $this->Biography = new Application_Model_DbUserbiography();
        $configurations = $this->commonmodel_obj->getConfiguration();       
        $this->view->getSeoConfiguration = $this->getSeoConfiguration = json_decode($configurations['seoContent']);
        $this->view->expertText = $this->expertText = $configurations['expert'];
        defined('expertText') or define('expertText',$this->expertText);
        $this->facebook_page_data = $configurations['facebook_page_data'];
        $this->linkedin_page_id = $configurations['linkedin_page_id'];
        $this->plateformInterface = $configurations['plateformInterface'];
        $this->view->SocialLogo = $this->SocialLogo = $configurations['SocialLogo'];
        $this->view->login_terms = $this->login_terms = $configurations['login_terms']; 
        $this->view->getSocialShareMsg = $this->getSocialShareMsg = Zend_Json::decode($configurations['SocialShareMsg']);
        
        $this->view->clientType = $this->clientType = clientType;
        $this->view->allowmultipleexperts = $this->allowmultipleexperts = allowmultipleexperts;
        
        if($_COOKIE['themepreview']=='show')
            $this->view->configuration = $this->configuration = json_decode($configurations['tempContent']);
        else
            $this->view->configuration = $this->configuration = json_decode($configurations['content']);

        $this->view->score = $this->score = json_decode($configurations['LeagueNames']);

        $this->view->post_score_setting = $this->post_score_setting = json_decode($configurations['ScoreNames'],true);

        $this->view->allow_admin_post_live = $this->allow_admin_post_live = $configurations['allow_admin_post_live'];
        
        $this->view->informviaemail = $this->informviaemail = $configurations['informviaemail'];

        $namespace = new Zend_Session_Namespace('Zend_Auth');
        $namespace->setExpirationSeconds((1209600));

        $authNamespace = new Zend_Session_Namespace('identify');
        $authNamespace->setExpirationSeconds((1209600));
        //echo $this->session_name_space->redirection;
    }
    function httpsPost($longUrl)
    {
        $postData = array('longUrl' => $longUrl, 'key' => 'AIzaSyDXdTKWaNO-2wcXWL8N3Jy-Q4XI5qXyazg');
        $curlObj = curl_init();
         
        $jsonData = json_encode($postData);
         
        curl_setopt($curlObj, CURLOPT_URL, 'https://www.googleapis.com/urlshortener/v1/url');
        curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curlObj, CURLOPT_HEADER, 0);
        curl_setopt($curlObj, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
        curl_setopt($curlObj, CURLOPT_POST, 1);
        curl_setopt($curlObj, CURLOPT_POSTFIELDS, $jsonData);
         
        $response = curl_exec($curlObj);
         
        //change the response json string to object
        $json = json_decode($response);
        curl_close($curlObj);
         
        return $json;
    }
    function shortUrl($longUrl)
    {
        // set HTTP header
        $headers = array(
            'Content-Type: application/json',
        );
        $postData = array('format'=>'json','longUrl' => $longUrl, 'access_token' => "adf0ecca6d54059cafc4be154d2bd200d545ddcf");
        $url = 'https://api-ssl.bitly.com/v3/shorten?' . http_build_query($postData);
        //echo $url; exit;
        // Open connection
        $ch = curl_init();

        // Set the url, number of GET vars, GET data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPGET, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // Execute request
        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // Close connection
        curl_close($ch);
        if ($status == 200) 
        {
            $resultdata = json_decode($result, true);
            return $resultdata['data']['url'];
        }
         else{
            return $longUrl;
        }
    }
    public function preCall()
    {
        $auth =  Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $data = $auth->getStorage()->read();
            $this->_userid = $data['UserID'];
            if(!$this->_userid)
                $this->_helper->redirector->gotosimple('index','index',true);
        }
    }

    public function preReturnCall()
    {
        $auth =  Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $data = $auth->getStorage()->read();
            $this->_userid = $data['UserID'];
            if(!$this->_userid)
                return false;
            else
                return true;
        }
        return true;
    }
    function rewritesession()
    {
       if($this->_userid)
       {
            $options['UserID'] = $this->_userid;
            $userresult = $this->myclientdetails->getAllMasterfromtable('tblUsers','*',$options);
            $authNamespace = new Zend_Session_Namespace('identify');
            $authNamespace->setExpirationSeconds((1209600));
            $authNamespace->role = $userresult['0']['role'];
            $authNamespace->id = $userresult[0]['UserID'];
            $authNamespace->user = $userresult[0]['Username'];
            $auth= Zend_Auth::getInstance();
            $auth->getStorage()->write($userresult[0]);
       }
    }

    public function islogin()
    {
        $auth =  Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $data = $auth->getStorage()->read();
            if($data['UserID'])
                return true;
            else
                return false;
        }
        return false;
    }
    public function generateUnique($plainText, $salt = null)
    {
        define('SALT_LENGTH', 9);
        if ($salt === null)
            $salt = substr(md5(uniqid(rand(), true)), 0, SALT_LENGTH);
        else 
            $salt = substr($salt, 0, SALT_LENGTH);
        return $salt . sha1($salt . $plainText);
    }
    public function destroySession()
    {
        if (isset($_SERVER['HTTP_COOKIE']))
        {
            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach($cookies as $cookie) 
            {
                $parts = explode('=', $cookie);
                $name = trim($parts[0]);
                setcookie($name, '', time()-1000, '/','db-csp.com');
            }
        }
        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::namespaceUnset('application');
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        session_destroy();
        session_unset();
    }
   
    function curPageURL() {
        $pageURL = 'http';
        if ($_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }
   
    function getDomainFromEmail($email)
    {
        // Get the data after the @ sign
        $domain = substr(strrchr($email, "@"), 1);
        return $domain;
    }
 
    public function sendWithoutSmtpMail($to, $setSubject, $from ='no-reply@db-csp.com', $setBodyText)
    {      
        if ($this->_config->smtp->status == 1) 
            $this->swiftMail($to,$setSubject, $from, $setBodyText);
        else
        {
            return false;
            $mail = new Zend_Mail('UTF-8');
            $rawat = new Zend_Mail_Transport_Sendmail('-fadmin@db-csp.com');
            Zend_Mail::setDefaultTransport($rawat);
            $mail->setBodyHtml(html_entity_decode($setBodyText));
            $mail->setFrom($from, FromName);
            $mail->addTo($to);
            $mail->setSubject($setSubject);
            if($to!='twitteruser@db-csp.com')
               return $mail->send();
        }
    }

     public function swiftMail($to, $setSubject, $from ='no-reply@db-csp.com', $setBodyText)
    {
         if($to=='twitteruser@db-csp.com')
          return true;
        require_once 'lib/swift_required.php';
        // Create the SMTP configuration
        $transport = Swift_SmtpTransport::newInstance($this->_config->smtp->smtp_server, 587);
        $transport->setUsername($this->_config->smtp->username);
        $transport->setPassword(smtpkey);
        
        // Create the message
        $message = Swift_Message::newInstance();
        $message->setTo(array($to));
        //$message->setCc(array("a.derosa@audero.it" => "Aurelio De Rosa"));
       /* $message->setBcc(array(
            "porwal.deshbandhu@gmail.com" => "Bank Boss"
        ));*/

        $message->setSubject($setSubject);
        $message->setBody($setBodyText, 'text/html');
        $message->setFrom($from, SITE_NAME);
        //$message->attach(Swift_Attachment::fromPath("path/to/file/file.zip"));
        
        // Send the email
        $mailer = Swift_Mailer::newInstance($transport);
        $mailer->send($message, $failedRecipients);
    }

   public function globalSettings()
    {
        $Result = $this->User_Model->globalSetting();  
        if($Result[0]['social_account']==0)
            $this->session_name_space->Social_Content_Block = 'unblock';
        else
            $this->session_name_space->Social_Content_Block = 'block';
    }
    public function getGlobalSettings()
    {
        $Result = $this->User_Model->globalSetting();   
        if($Result[0]['social_account']==0)
            return 'unblock';
        else
            return 'block';
    }
   
     public function sendMailToAssignExpertUser($data,$loginUserData,$url)
    {
        $loginusername =  ucfirst($loginUserData["Name"]);
      
        $EmailTemplateArray = array('Email' => $data->Email,
                                    'Name' => $data['Name'],
                                    'lname' => $data['lname'],
                                    'url' => $url,
                                    'userName'=> $loginusername,
                                    'expertText'=> $this->expertText,
                                    'case'=>28);
         $this->dbeeComparetemplateOne($EmailTemplateArray);
        /****for email ****/
            
    }
    public function sendMailToExpertUser($data,$loginUserData,$url)
    {      
     
        $EmailTemplateArray = array('Email' => $data->Email,
                                    'Name' => $data->Name,
                                    'lname' => $data->lname,
                                    'sessionName' => $this->session_array["Name"],
                                    'sessionlname' => $this->session_array["lname"],
                                    'url' => $url,
                                    'expertText'=> $this->expertText,
                                    'case'=>29);
        $this->dbeeComparetemplateOne($EmailTemplateArray);
        /****for email ****/       
    }
   
    public function sendMailToAdminForSpecialDbUseraccept($email,$data,$eventName)
    {
        $EmailTemplateArray = array('Email' => $email,
                                    'Name' => $data['Name'],
                                    'lname' => $data['lname'],
                                    'eventName' => $eventName,
                                    'case'=>30);
         $this->dbeeComparetemplateOne($EmailTemplateArray);
    }
    public function senmailtoadmin($email,$data,$eventName)
    {
        $EmailTemplateArray = array('Email' => $email,
                                    'Name' => $data['Name'],
                                    'lname' => $data['lname'],
                                    'eventName' => $eventName,
                                    'case'=>32);
         $this->dbeeComparetemplateOne($EmailTemplateArray);
        
    }
    
   
    public function sendMailToUserByExpert($data,$loginUserData,$url)
    {
        $EmailTemplateArray = array('Email' => $data->Email,
                                    'Name' => $data->Name,
                                    'lname' => $data->lname,
                                    'sessionName' => $this->session_array["Name"],
                                    'sessionlname' => $this->session_array["lname"],
                                    'dburl' => $dbee_array['dburl'],
                                    'expertText'=> $this->expertText,
                                    'case'=>33);
         $this->dbeeComparetemplateOne($EmailTemplateArray);
    }

    public function sendMailToAdminForNewPostNotification($data,$dbee_array)
    {
        $EmailTemplateArray = array('Email' => $this->myclientdetails->customEncoding('rajyadavmca@gmail.com'),
                                    'Name' => $data['Name'],
                                    'lname' => $data['lname'],
                                    'VidDesc' => $dbee_array['VidDesc'],
                                    'dburl' => $dbee_array['dburl'],
                                    'case'=>39);
         $this->dbeeComparetemplateOne($EmailTemplateArray);
        
    }

     public function curl($url,$data)
     {
        $ch = curl_init();
        // prepate data for curl post
        $ndata = '';
        if (is_array($data))
            foreach ($data as $key => $value)
                $ndata .= $key.'='.urlencode($value).'&';
        else
            $ndata = $data;
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Expect:'
            )
        );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $ndata);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Set curl to return the
        // data instead of
        // printing it to
        // the browser.
        curl_setopt($ch, CURLOPT_URL, $url);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
     }
         public function dbeeEmailtemplate(){
                $myImgurl = FRONT_URL.'/adminraw/img/bgs/';
                $logoImgurl = FRONT_URL.'/adminraw/img/emailbgimage/';
                $emailTemplate ='<table width="100%"><tbody><tr>
                     <td style="padding-top:100px;padding-bottom:100px;background:#[%bodycontentbacgroColor%] url('.$myImgurl.'[%bodycontenttxture%]) repeat;" class="editingBlck" titleval="Body" editType="bodycontent">
                     <form id="mainForm">
                       <table width="623" style="background:#ffffff;font-family:Arial,Helvetica,sans-serif;font-size:13px" align="center" border="0" cellspacing="0" cellpadding="30">
                        <tbody>
                            <tr>
                                <td class="editingBlck" titleval="Header" editType="header" style="background:#[%headerbacgroColor%] url('.$myImgurl.'[%headertxture%]) repeat; padding-top:20px; padding-bottom:20px;"><a href="#"  ><img src="'.$logoImgurl.'[%bannerfreshimg%]" id="bannerHolder" border="0" style="display:inline-block" alt="dbee logo"></a>
                                </td>
                            </tr>
                            <tr>
                                <td style="color:#[%contentbodyfontColor%]; padding:20px 30px 30px 30px; background:#[%contentbodybacgroColor%] url('.$myImgurl.'[%contentbodytxture%]) repeat;"  class="editingBlck bodymsg" titleval="Body container" editType="contentbody" id="bodymsg" >
                                   <div id="bodyEmailMsg" style="padding-top:10px">[%%body%%]</div>
                                 </td>
                            </tr>
                            <tr>
                                <td style="color:#[%footerfontColor%]; background:#[%footerbacgroColor%] url('.$myImgurl.'[%footertxture%]) repeat;" class="editingBlck" titleval="Footer" editType="footer">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div align="right"><a href="'.FRONT_URL.'" style="text-decoration:none; color:#[%footerfontColor%]" target="_blank"  editType="footercontent" id="footerMsg">
                                                        [%%footertext%%]
                                                     </a>
                                                    </div>
                                                </td>

                                            </tr>
                                            <tr>
                                                <td>
                                                    <div align="right"><a href="'.FRONT_URL.'" style="text-decoration:none; color:#[%footerfontColor%]" target="_blank"  editType="footercontent" id="footerMsg">
                                                        Click here to go to [%%COMPANY_NAME%%]
                                                     </a>
                                                    </div>
                                                </td>
                                                
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>

                        </tbody>
                       </table>
                     </form>    
                     </td></tr>
                     </tbody></table>';
        return $emailTemplate;
    }

    public function dbeeComparetemplateOne($globalarr)
    {
    	
        extract($globalarr); 
        $deshboard   = new Application_Model_Commonfunctionality();
        $casetype  = (int)$case;
        $Email = $this->myclientdetails->customDecoding($Email);
        $Name = $this->myclientdetails->customDecoding($Name).' '.$this->myclientdetails->customDecoding($lname);
        $sessionName = $this->myclientdetails->customDecoding($sessionName);
        $userName = $this->myclientdetails->customDecoding($userName).' '.$this->myclientdetails->customDecoding($lname);
        $Username = $this->myclientdetails->customDecoding($Username).' '.$this->myclientdetails->customDecoding($lname);
        $cookieuser = $this->myclientdetails->customDecoding($cookieuser).' '.$this->myclientdetails->customDecoding($lname);
        $signupName = $this->myclientdetails->customDecoding($signupName).' '.$this->myclientdetails->customDecoding($lname);
        $FollowerName = $this->myclientdetails->customDecoding($FollowerName).' '.$this->myclientdetails->customDecoding($lname);
        $GroupAdmin = $this->myclientdetails->customDecoding($GroupAdmin).' '.$this->myclientdetails->customDecoding($lname);
        $ownerName = $this->myclientdetails->customDecoding($ownerName).' '.$this->myclientdetails->customDecoding($ownerlname);
        $ReportingUser = $this->myclientdetails->customDecoding($ReportingUser).' '.$this->myclientdetails->customDecoding($lname);
        $SName = $this->myclientdetails->customDecoding($SName).' '.$this->myclientdetails->customDecoding($Slname);
        $RName = $this->myclientdetails->customDecoding($RName).' '.$this->myclientdetails->customDecoding($Rlname);
        $fullname = $this->myclientdetails->customDecoding($fullname);
        if(clientID!='')
        {
            $case = '';
            $case = $casetype+1; 
            $bodyContent = array();
            $bodyContent = $deshboard->getGroupemailtemplate($case,'front');
        }
        else
            $bodyContent = $deshboard->getGroupemailtemplate();
            
         switch ($casetype){
                
            case '6': /* New account activation email section */
              
                if(empty($signupName) || empty($siteUrl) || empty($Signuptoken)){
                    $checkerror ='';
                    if(empty($signupName)){$checkerror .='$signupName'.'#';}
                    if(empty($siteUrl)){$checkerror .='$siteUrl'.'#';}
                    if(empty($Signuptoken)){$checkerror .='$Signuptoken'.'#';}
                    $caseerror ='case = 6';        
                }else{
                    $data1 = array('[%%request_Name%%]','[%%COMPANY_NAME%%]','[%%site_url%%]',
                                   '[%%site_url%%]','[%%COMPANY_NAME%%]','[%%site_url%%]',
                                   '[%%request_Signuptoken%%]','[%%site_url%%]',
                                   '[%%request_Signuptoken%%]','[%%COMPANY_FOOTERTEXT%%]');
                    $data2 = array($signupName,COMPANY_NAME,$siteUrl,$siteUrl,COMPANY_NAME,
                            $siteUrl,$Signuptoken,$siteUrl,$Signuptoken,COMPANY_FOOTERTEXT); 
                    $datasub1 = array('[%%COMPANY_NAME%%]');
                    $datasub2 = array(COMPANY_NAME);
                    
                    //$subjectMsg = str_replace($datasub1,$datasub2,$bodyContent[$case]['subject']);
                    if(clientID!='') $subjectMsg = str_replace($datasub1,$datasub2,$bodyContent['subject']);
                    else $subjectMsg = str_replace($datasub1,$datasub2,$bodyContent[$case]['subject']);
                }
            break;

            case '7': /* Welcome Email secton*/

                if(empty($Name) || empty($Email)){
                    $checkerror ='';
                    if(empty($Name)){$checkerror .='$Name'.'#';}
                    if(empty($Email)){$checkerror .='$Email'.'#';}
                    $caseerror ='case = 7';        
                }else{
                    $data1 = array('[%%chkUser_Name%%]','[%%COMPANY_NAME%%]','[%%chkUser_Email%%]',
                                   '[%%COMPANY_NAME%%]','[%%COMPANY_FOOTERTEXT%%]');
                    $data2 = array($Name,COMPANY_NAME,$Email,COMPANY_NAME,COMPANY_FOOTERTEXT);
                    $datasub1 = array('[%%COMPANY_NAME%%]');
                    $datasub2 = array(COMPANY_NAME);

                    if(clientID!='') $subjectMsg = str_replace($datasub1,$datasub2,$bodyContent['subject']);
                    else $subjectMsg = str_replace($datasub1,$datasub2,$bodyContent[$case]['subject']);

                    //$subjectMsg = str_replace($datasub1,$datasub2,$bodyContent[$case]['subject']);
                }
            
            break;

            case '8': /* Password reset link section*/
            if(empty($Name) || empty($UserID) || empty($randkey)){
                $checkerror ='';
                if(empty($Name)){$checkerror .='$Name'.'#';}
                if(empty($UserID)){$checkerror .='$UserID'.'#';}
                if(empty($randkey)){$checkerror .='$randkey'.'#';}
                $caseerror ='case = 8';        
            }else{
                $data1 = array('[%%Res_Name%%]','[%%COMPANY_NAME%%]','[%%BASE_URL%%]',
                               '[%%Res_UserID%%]','[%%BASE_URL%%]','[%%Res_UserID%%]',
                               '[%%rand%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($Name,COMPANY_NAME,FRONT_URL,$UserID,FRONT_URL,$UserID,
                                $randkey,COMPANY_FOOTERTEXT);
                //$subjectMsg = $bodyContent[$case]['subject'];
                 if(clientID!='') $subjectMsg = $bodyContent['subject'];
                else $subjectMsg = $bodyContent[$case]['subject'];
            }
            break;

            case '9': /* Feedback section */
            if(empty($feedbacktext)){
                $checkerror ='';
                if(empty($feedbacktext)){$checkerror .='$feedbacktext'.'#';}
                $caseerror ='case = 9';        
            }else{ 
                $data1 = array('[%%feedbacktext%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($feedbacktext,COMPANY_FOOTERTEXT);
                //$subjectMsg = $bodyContent[$case]['subject'];
                if(clientID!='') $subjectMsg = $bodyContent['subject'];
                else $subjectMsg = $bodyContent[$case]['subject'];
            }
            break;

            case '10': /* Db Error message section */
            if(empty($errormessage) || empty($controller) || empty($action) || empty($url)){
                $checkerror ='';
                if(empty($errormessage)){$checkerror .='$errormessage'.'#';}
                if(empty($controller)){$checkerror .='$controller'.'#';}
                if(empty($action)){$checkerror .='$action'.'#';}
                if(empty($url)){$checkerror .='$url'.'#';}
                $caseerror ='case = 10';        
            }else{ 
                $data1 = array('[%%BASE_URL%%]','[%%message%%]','[%%params_controller%%]',
                    '[%%params_action%%]','[%%SERVER_REQUEST_URI%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array(FRONT_URL,$errormessage,$controller,$action,$url,COMPANY_FOOTERTEXT);
                $datasub1 = array('[%%message%%]');
                $datasub2 = array($errormessage);
                //$subjectMsg = str_replace($datasub1,$datasub2,$bodyContent[$case]['subject']);
                if(clientID!='') $subjectMsg = str_replace($datasub1,$datasub2,$bodyContent['subject']);
                else $subjectMsg = str_replace($datasub1,$datasub2,$bodyContent[$case]['subject']);
            }
            break;

            case '11':  /* New comment posted section */ 
            if(empty($Name) || empty($db) || empty($dbeeTextval) || empty($commTextval)){
                $checkerror ='';
                if(empty($label)){$checkerror .='$label'.'#';}
                if(empty($cookieuser)){$checkerror .='$cookieuser'.'#';}
                if(empty($ReportingUser)){$checkerror .='$ReportingUser'.'#';}
                if(empty($Text)){$checkerror .='$Text'.'#';}
                $caseerror ='case = 11';        
            }else{                        
                $data1 = array('[%%BASE_URL%%]','[%%UserPic%%]','[%%UserRow_Name%%]',
                            '[%%BASE_URL%%]','[%%POST_NAME%%]','[%%dbeeText%%]','[%%UserRow_Name%%]',
                            '[%%commTextval%%]','[%%COMPANY_FOOTERTEXT%%]','[%%COMPANY_NAME%%]',
                            '[%%COMPANY_NAME%%]');
                $data2 = array(FRONT_URL,$UserRowProfilePic,$Name,FRONT_URL,$db,$dbeeTextval,
                                 $Name,$commTextval,COMPANY_FOOTERTEXT,COMPANY_NAME,COMPANY_NAME);
                //$subjectMsg = $bodyContent[$case]['subject'];
                if(clientID!='') $subjectMsg = $bodyContent['subject'];
                 else $subjectMsg = $bodyContent[$case]['subject'];
            }
            break; 

            case '12': /* Expert removal section */
            if(empty($Name) || empty($dburl))
            {
                $checkerror ='';
                if(empty($Name)){$checkerror .='$Name'.'#';}
                if(empty($dburl)){$checkerror .='$dburl'.'#';}
                $caseerror ='case = 12';        
            }else
            {
                $data1 = array('[%%data_Name%%]','[%%POSTLINK%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($Name,$dburl,COMPANY_FOOTERTEXT);
                if(clientID!='') $subjectMsg = $bodyContent['subject'];
                else $subjectMsg = $bodyContent[$case]['subject'];
            }
            break;

            case '13': /* Abuse report sent section */
            if(empty($label) || empty($cookieuser) || empty($ReportingUser) || empty($Text) || empty($dbee)){
                $checkerror ='';
                if(empty($label)){$checkerror .='$label'.'#';}
                if(empty($cookieuser)){$checkerror .='$cookieuser'.'#';}
                if(empty($ReportingUser)){$checkerror .='$ReportingUser'.'#';}
                if(empty($Text)){$checkerror .='$Text'.'#';}
                if(empty($dbee)){$checkerror .='$dbee'.'#';}
                $caseerror ='case = 13';        
            }else{
                $data1 = array('[%%USERNAME%%]','[%%label%%]','[%%BASE_URL%%]','[%%cookieuser%%]',
                     '[%%ReportingUser%%]','[%%Text%%]','[%%BASE_URL%%]','[%%dbee%%]',
                     '[%%POST_NAME%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($userName,$label,FRONT_URL,$cookieuser,$ReportingUser,$Text,FRONT_URL,
                      $dbee,POST_NAME,COMPANY_FOOTERTEXT);
                //$subjectMsg = $bodyContent[$case]['subject'];
                 if(clientID!='') $subjectMsg = $bodyContent['subject'];
                else $subjectMsg = $bodyContent[$case]['subject'];
            }
            break;
            case '39': /* Abuse report sent section */
            if(empty($label) || empty($cookieuser) || empty($ReportingUser) || empty($Text) || empty($dbee)){
                $checkerror ='';
                if(empty($label)){$checkerror .='$label'.'#';}
                if(empty($cookieuser)){$checkerror .='$cookieuser'.'#';}
                if(empty($ReportingUser)){$checkerror .='$ReportingUser'.'#';}
                if(empty($Text)){$checkerror .='$Text'.'#';}
                if(empty($dbee)){$checkerror .='$dbee'.'#';}
                $caseerror ='case = 39';        
            }else{
                $data1 = array('[%%USERNAME%%]','[%%label%%]','[%%BASE_URL%%]','[%%cookieuser%%]',
                     '[%%ReportingUser%%]','[%%Text%%]','[%%BASE_URL%%]','[%%dbee%%]',
                     '[%%POST_NAME%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($userName,$label,FRONT_URL,$cookieuser,$ReportingUser,$Text,FRONT_URL,
                      $dbee,POST_NAME,COMPANY_FOOTERTEXT);
                //$subjectMsg = $bodyContent[$case]['subject'];
                 if(clientID!='') $subjectMsg = $bodyContent['subject'];
                else $subjectMsg = $bodyContent[$case]['subject'];
            }
            break;
            case '14': /* Bug report sent section */
            if(empty($UserID) || empty($Name) || empty($bug) || empty($userbrowser)){
                $checkerror ='';
                if(empty($UserID)){$checkerror .='$UserID'.'#';}
                if(empty($Name)){$checkerror .='$Name'.'#';}
                if(empty($bug)){$checkerror .='$bug'.'#';}
                if(empty($userbrowser)){$checkerror .='$userbrowser'.'#';}
                $caseerror ='case = 14';        
            }else{
                $data1 = array('[%%USERNAME%%]','[%%BASE_URL%%]','[%%Row_UserID%%]','[%%Row_Name%%]','[%%bug%%]',
                               '[%%userbrowser%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($userName,FRONT_URL,$UserID,$Name,$bug,$userbrowser,COMPANY_FOOTERTEXT);
                //$subjectMsg = $bodyContent[$case]['subject'];
                 if(clientID!='') $subjectMsg = $bodyContent['subject'];
                else $subjectMsg = $bodyContent[$case]['subject'];
            }
            break;
            case '40': /* Bug report sent section */
            if(empty($UserID) || empty($Name) || empty($bug) || empty($userbrowser)){
                $checkerror ='';
              
                if(empty($UserID)){$checkerror .='$UserID'.'#';}
                if(empty($Name)){$checkerror .='$Name'.'#';}
                if(empty($bug)){$checkerror .='$bug'.'#';}
                if(empty($userbrowser)){$checkerror .='$userbrowser'.'#';}
                $caseerror ='case = 40';        
            }else{
                $data1 = array('[%%USERNAME%%]','[%%BASE_URL%%]','[%%Row_UserID%%]','[%%Row_Name%%]','[%%bug%%]',
                               '[%%userbrowser%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($userName,FRONT_URL,$UserID,$Name,$bug,$userbrowser,COMPANY_FOOTERTEXT);
                //$subjectMsg = $bodyContent[$case]['subject'];
                 if(clientID!='') $subjectMsg = $bodyContent['subject'];
                else $subjectMsg = $bodyContent[$case]['subject'];
            }
            break;
            case '15': /* Invited to a league section */
            if(empty($Name) || empty($userleaguesend) || empty($userleagueacce) || empty($leagueid)){
                $checkerror ='';
                if(empty($Name)){$checkerror .='$Name'.'#';}
                if(empty($userleaguesend)){$checkerror .='$userleaguesend'.'#';}
                if(empty($userleagueacce)){$checkerror .='$userleagueacce'.'#';}
                if(empty($leagueid)){$checkerror .='$leagueid'.'#';}
                $caseerror ='case = 15';        
            }else{        
                $data1 = array('[%%row_Name%%]','[%%BASE_URL%%]','[%%userleaguesend%%]','[%%userleagueacce%%]',
                         '[%%BASE_URL%%]','[%%leagueid%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($Name,FRONT_URL,$userleaguesend,$userleagueacce,FRONT_URL,$leagueid,
                         COMPANY_FOOTERTEXT);
                $datasub1 = array('[%%POST_NAME%%]');
                $datasub2 = array(POST_NAME);
                //$subjectMsg = str_replace($datasub1,$datasub2,$bodyContent[$case]['subject']);
                if(clientID!='') $subjectMsg = str_replace($datasub1,$datasub2,$bodyContent['subject']);
                else $subjectMsg = str_replace($datasub1,$datasub2,$bodyContent[$case]['subject']);
            }
            break;
                
            case '16': /* Email verified section */
            if(empty($Name)){
                $checkerror ='';
                if(empty($Name)){$checkerror .='$Name'.'#';}
                $caseerror ='case = 16';        
            }else{
                $data1 = array('[%%chkUser_Name%%]','[%%COMPANY_NAME%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($Name,COMPANY_NAME,COMPANY_FOOTERTEXT);
                //$subjectMsg = $bodyContent[$case]['subject'];
                 if(clientID!='') $subjectMsg = $bodyContent['subject'];
                else $subjectMsg = $bodyContent[$case]['subject'];
            }
            break; 

            case '17': /* Invited to a group section */
            if(empty($GroupName) || empty($GroupAdmin)){
                $checkerror ='';
                if(empty($GroupName)){$checkerror .='$GroupName'.'#';}
                if(empty($GroupAdmin)){$checkerror .='$GroupAdmin'.'#';}
                $caseerror ='case = 17';        
            }else{
                $data1 = array('[%%USERNAME%%]','[%%GroupName%%]','[%%GroupAdmin%%]','[%%COMPANY_NAME%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($fullname,$GroupName,$GroupAdmin,COMPANY_NAME,COMPANY_FOOTERTEXT);
                //$subjectMsg = $bodyContent[$case]['subject'];
                 if(clientID!='') $subjectMsg = $bodyContent['subject'];
                else $subjectMsg = $bodyContent[$case]['subject'];
            }
            break; 

            case '18': /* Group Joining Request section */
            if(empty($ownerName) || empty($userName)|| empty($GroupName)){
                $checkerror ='';
                if(empty($ownerName)){$checkerror .='$ownerName'.'#';}
                if(empty($userName)){$checkerror .='$userName'.'#';}
                if(empty($GroupName)){$checkerror .='$GroupName'.'#';}
                $caseerror ='case = 18';        
            }else{
                $data1 = array('[%%ownerName%%]','[%%userName%%]','[%%GroupName%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($ownerName,$userName,$GroupName,COMPANY_FOOTERTEXT);
                //$subjectMsg = $bodyContent[$case]['subject'];
                 if(clientID!='') $subjectMsg = $bodyContent['subject'];
                 else $subjectMsg = $bodyContent[$case]['subject'];
            }
            break;     

            case '19': /* Group Joining Request2 section */
            if(empty($Name) || empty($type)){
                $checkerror ='';
                if(empty($Name)){$checkerror .='$Name'.'#';}
                if(empty($type)){$checkerror .='$type'.'#';}
                $caseerror ='case = 19';        
            }else{
                $data1 = array('[%%data_Name%%]','[%%COMPANY_NAME%%]','[%%type%%]','[%%COMPANY_NAME%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($Name,COMPANY_NAME,$type,COMPANY_NAME,COMPANY_FOOTERTEXT);
                $datasub1 = array('[%%COMPANY_NAME%%]');
                $datasub2 = array(COMPANY_NAME);
                //$subjectMsg = str_replace($datasub1,$datasub2,$bodyContent[$case]['subject']);
                if(clientID!='') $subjectMsg = str_replace($datasub1,$datasub2,$bodyContent['subject']);
                else $subjectMsg = str_replace($datasub1,$datasub2,$bodyContent[$case]['subject']);
            }
            break;

            case '20': /* Activation email for facebook user section */
            if(empty($Name) || empty($type)){
                $checkerror ='';
                if(empty($Name)){$checkerror .='$Name'.'#';}
                if(empty($type)){$checkerror .='$type'.'#';}
                $caseerror ='case = 20';        
            }else{
                $data1 = array('[%%data_Name%%]','[%%COMPANY_NAME%%]','[%%type%%]',
                               '[%%COMPANY_NAME%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($Name,COMPANY_NAME,$type,COMPANY_NAME,COMPANY_FOOTERTEXT);
                $datasub1 = array('[%%COMPANY_NAME%%]');
                $datasub2 = array(COMPANY_NAME);
                //$subjectMsg = str_replace($datasub1,$datasub2,$bodyContent[$case]['subject']);
                if(clientID!='') $subjectMsg = str_replace($datasub1,$datasub2,$bodyContent['subject']);
                else $subjectMsg = str_replace($datasub1,$datasub2,$bodyContent[$case]['subject']);
            }
            break; 

            case '21': /* Activation email for Linkedin user section */
            if(empty($Name) || empty($type)){
                $checkerror ='';
                if(empty($Name)){$checkerror .='$Name'.'#';}
                if(empty($type)){$checkerror .='$type'.'#';}
                $caseerror ='case = 21';        
            }else{
                $data1 = array('[%%data_Name%%]','[%%COMPANY_NAME%%]','[%%type%%]',
                               '[%%COMPANY_NAME%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($Name,COMPANY_NAME,$type,COMPANY_NAME,COMPANY_FOOTERTEXT);
                $datasub1 = array('[%%COMPANY_NAME%%]');
                $datasub2 = array(COMPANY_NAME);
                    //$subjectMsg = str_replace($datasub1,$datasub2,$bodyContent[$case]['subject']);
                if(clientID!='') $subjectMsg = str_replace($datasub1,$datasub2,$bodyContent['subject']);
                else $subjectMsg = str_replace($datasub1,$datasub2,$bodyContent[$case]['subject']);
            }
            break;

            case '22': /* follower email section */
            if(empty($FollowerUserID) || empty($FollowerName)){
                $checkerror ='';
                if(empty($FollowerUserID)){$checkerror .='$FollowerUserID'.'#';}
                if(empty($FollowerName)){$checkerror .='$FollowerName'.'#';}
                $caseerror ='case = 22';        
            }else{
                $data1 = array('[%%BASE_URL%%]','[%%FollowerUserID%%]','[%%BASE_URL%%]',
                    '[%%FollPic%%]','[%%BASE_URL%%]','[%%FollowerUserID%%]','[%%FollowerName%%]',
                    '[%%COMPANY_NAME%%]','[%%COMPANY_FOOTERTEXT%%]','[%%COMPANY_NAME%%]',
                    '[%%COMPANY_NAME%%]');
                $data2 = array(FRONT_URL,$FollowerUserID,FRONT_URL,$FollowerProfilePicval,FRONT_URL,
                     $FollowerUserID,$FollowerName,COMPANY_NAME,COMPANY_FOOTERTEXT,COMPANY_NAME,COMPANY_NAME);
                //$subjectMsg = $bodyContent[$case]['subject'];
                if(clientID!='') $subjectMsg = $bodyContent['subject'];
                else $subjectMsg = $bodyContent[$case]['subject'];
            }
            break;

            case '23': /* verify your email address section */
            if(empty($Name) || empty($siteUrl) || empty($Emailtoken)){
                $checkerror ='';
                if(empty($Name)){$checkerror .='$Name'.'#';}
                if(empty($siteUrl)){$checkerror .='$siteUrl'.'#';}
                if(empty($Emailtoken)){$checkerror .='$Emailtoken'.'#';}
                $caseerror ='case = 23';        
            }else{
                $data1 = array('[%%request_Name%%]','[%%site_url%%]','[%%request_Emailtoken%%]',
                        '[%%site_url%%]','[%%request_Emailtoken%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($Name,$siteUrl,$Emailtoken,$siteUrl,$Emailtoken,COMPANY_FOOTERTEXT);
                //$subjectMsg = $bodyContent[$case]['subject'];
                if(clientID!='') $subjectMsg = $bodyContent['subject'];
                else $subjectMsg = $bodyContent[$case]['subject'];
            }        
            break;

            case '24': /* verify your score section */
             //if($this->_userid=='15835'){}
            if(empty($Username) || empty($Name) || empty($label) 
                || empty($scorelabel) || empty($MainDB) || empty($Textval)){
                $checkerror ='';
                if(empty($Username)){$checkerror .='$Username'.'#';}
                if(empty($Name)){$checkerror .='$Name'.'#';}
                if(empty($label)){$checkerror .='$label'.'#';}
                if(empty($scorelabel)){$checkerror .='$scorelabel'.'#';}
                if(empty($MainDB)){$checkerror .='$MainDB'.'#';}
                if(empty($Textval)){$checkerror .='$Textval';}
                $caseerror ='case = 24';        
            }else{
                $data1 = array('[%%BASE_URL%%]','[%%UserPic%%]','[%%BASE_URL%%]',
                  '[%%UserRow_Username%%]','[%%UserRow_Name%%]','[%%label%%]','[%%scorelabel%%]',
                  '[%%BASE_URL%%]','[%%MainDB%%]','[%%Textval%%]','[%%COMPANY_FOOTERTEXT%%]',
                  '[%%COMPANY_NAME%%]','[%%COMPANY_NAME%%]');
                $data2 = array(FRONT_URL,$UserRowProfilePic,FRONT_URL,$Username,$Name,$label,
                         $scorelabel,FRONT_URL,$MainDB,$Textval,COMPANY_FOOTERTEXT,COMPANY_NAME,COMPANY_NAME);
                //$subjectMsg = $bodyContent[$case]['subject'];
                if(clientID!='') $subjectMsg = $bodyContent['subject'];
                else $subjectMsg = $bodyContent[$case]['subject'];
            }
            break;

            case '25': /* Commented on db section*/
            if(empty($Name) || empty($db) || empty($dbeeText) || empty($commTextval1)){
                $checkerror ='';
                if(empty($Name)){$checkerror .='$Name'.'#';}
                if(empty($db)){$checkerror .='$db'.'#';}
                if(empty($dbeeText)){$checkerror .='$dbeeText'.'#';}
                if(empty($commTextval1)){$checkerror .='$commTextval1'.'#';}
                $caseerror ='case = 25';        
            }else{
                $data1 = array('[%%BASE_URL%%]','[%%UserPic%%]','[%%UserRow_Name%%]',
                  '[%%POST_NAME%%]','[%%BASE_URL%%]','[%%db%%][%%dbeeText%%]','[%%UserRow_Name%%]',
                  '[%%commTextval%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array(FRONT_URL,$UserRowProfilePic,$Name,POST_NAME,FRONT_URL,$db,$dbeeText,
                    $Name,$commTextval1,COMPANY_FOOTERTEXT);
                //$subjectMsg = $bodyContent[$case]['subject'];
                if(clientID!='') $subjectMsg = $bodyContent['subject'];
                else $subjectMsg = $bodyContent[$case]['subject'];
            }
            break;

            case '26': /* Group Joining Request section*/
            if(empty($ownerName) || empty($userName) || empty($GroupName)){
                $checkerror ='';
                if(empty($ownerName)){$checkerror .='$ownerName'.'#';}
                if(empty($userName)){$checkerror .='$userName'.'#';}
                if(empty($GroupName)){$checkerror .='$GroupName'.'#';}
                $caseerror ='case = 26';        
            }else{
                $data1 = array('[%%ownerName%%]','[%%userName%%]','[%%GroupName%%]',
                               '[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($ownerName,$userName,$GroupName,COMPANY_FOOTERTEXT);
                //$subjectMsg = $bodyContent[$case]['subject'];
                if(clientID!='') $subjectMsg = $bodyContent['subject'];
                else $subjectMsg = $bodyContent[$case]['subject'];
            }
            break; 

            case '27': /* Group Joining Request2 section*/
            if(empty($ownerName) || empty($userName) || empty($GroupName)){
                $checkerror ='';
                if(empty($ownerName)){$checkerror .='$ownerName'.'#';}
                if(empty($userName)){$checkerror .='$userName'.'#';}
                if(empty($GroupName)){$checkerror .='$GroupName'.'#';}
                $caseerror ='case = 27';        
            }else{
                $data1 = array('[%%ownerName%%]','[%%userName%%]','[%%GroupName%%]',
                               '[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($ownerName,$userName,$GroupName,COMPANY_FOOTERTEXT);
                //$subjectMsg = $bodyContent[$case]['subject'];
                if(clientID!='') $subjectMsg = $bodyContent['subject'];
                else $subjectMsg = $bodyContent[$case]['subject'];
            }
            break;

            case '28': /* Invitation to join as expert section*/
            if(empty($Name) || empty($userName) || empty($url)){
                $checkerror ='';
                if(empty($Name)){$checkerror .='$Name'.'#';}
                if(empty($userName)){$checkerror .='$userName'.'#';}
                if(empty($url)){$checkerror .='$url'.'#';}
                $caseerror ='case = 28';        
            }else{
                $data1 = array('[%%data_Name%%]','[%%loginusername%%]','[%%url%%]',
                               '[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($Name,$userName,$url,COMPANY_FOOTERTEXT);
                //$subjectMsg = $bodyContent[$case]['subject'];
                if(clientID!='') $subjectMsg = $bodyContent['subject'];
                else $subjectMsg = $bodyContent[$case]['subject'];
            }
            break; 

            case '29': /* New question asked section*/
            if(empty($Name) || empty($sessionName) || empty($dburl)){
                $checkerror ='';
                if(empty($Name)){$checkerror .='$Name'.'#';}
                if(empty($sessionName)){$checkerror .='$sessionName'.'#';}
                if(empty($dburl)){$checkerror .='$dburl'.'#';}
                $caseerror ='case = 29';        
            }else{
                $data1 = array('[%%data_Name%%]','[%%Name%%]','[%%BASE_URL%%]','[%%POSTLINK%%]',
                               '[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($Name,$sessionName,FRONT_URL,$dburl,COMPANY_FOOTERTEXT);
                //$subjectMsg = $bodyContent[$case]['subject'];
                if(clientID!='') $subjectMsg = $bodyContent['subject'];
                else $subjectMsg = $bodyContent[$case]['subject'];
            }    
            break;    
                
            case '30': /* New attendee for video event section*/
            if(empty($Name) || empty($eventName)){
                $checkerror ='';
                if(empty($Name)){$checkerror .='$Name'.'#';}
                if(empty($eventName)){$checkerror .='$eventName'.'#';}
                $caseerror ='case = 31';        
            }else{
                
                $data1 = array('[%%data_Name%%]','[%%EVENTNAME%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($Name,$eventName,COMPANY_FOOTERTEXT);
                //$subjectMsg = $bodyContent[$case]['subject'];
                if(clientID!='') $subjectMsg = $bodyContent['subject'];
                else $subjectMsg = $bodyContent[$case]['subject'];
            }    
            break;


            case '31': /* New attendee for video event section*/
            if(empty($Name) || empty($eventName)){
                $checkerror ='';
                if(empty($Name)){$checkerror .='$Name'.'#';}
                if(empty($eventName)){$checkerror .='$eventName'.'#';}
                $caseerror ='case = 31';        
            }else{
                
                $data1 = array('[%%data_Name%%]','[%%EVENTNAME%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($Name,$eventName,COMPANY_FOOTERTEXT);
                //$subjectMsg = $bodyContent[$case]['subject'];
                if(clientID!='') $subjectMsg = $bodyContent['subject'];
                else $subjectMsg = $bodyContent[$case]['subject'];
            }    
            break;

            case '32': /* User request to join video event section*/
            if(empty($Name) || empty($eventName)){
                $checkerror ='';
                if(empty($Name)){$checkerror .='$Name'.'#';}
                if(empty($eventName)){$checkerror .='$eventName'.'#';}
                $caseerror ='case = 32';        
            }else{
                $data1 = array('[%%name%%]','[%%title%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($Name,$eventName,COMPANY_FOOTERTEXT);
                //$subjectMsg = $bodyContent[$case]['subject'];
                if(clientID!='') $subjectMsg = $bodyContent['subject'];
                else $subjectMsg = $bodyContent[$case]['subject'];
            }    
            break;

            case '33': /* Video event participation request section*/
            if(empty($Name) || empty($eventName)){
                $checkerror ='';
                if(empty($Name)){$checkerror .='$Name'.'#';}
                if(empty($eventName)){$checkerror .='$eventName'.'#';}
                $caseerror ='case = 33';        
            }else{
                $data1 = array('[%%data_Name%%]','[%%EVENTNAME%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($Name,$eventName,COMPANY_FOOTERTEXT);
                //$subjectMsg = $bodyContent[$case]['subject'];
                if(clientID!='') $subjectMsg = $bodyContent['subject'];
                else $subjectMsg = $bodyContent[$case]['subject'];
            }    
            break;

            case '34': /* Your question answered section*/
            if(empty($Name) || empty($sessionName) || empty($dburl)){
                $checkerror ='';
                if(empty($Name)){$checkerror .='$Name'.'#';}
                if(empty($sessionName)){$checkerror .='$sessionName'.'#';}
                if(empty($dburl)){$checkerror .='$dburl'.'#';}
                $caseerror ='case = 34';        
            }else{
                $data1 = array('[%%data_Name%%]','[%%Name%%]','[%%POSTLINK%%]',
                               '[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($Name,$sessionName,$dburl,COMPANY_FOOTERTEXT);
                //$subjectMsg = $bodyContent[$case]['subject'];
                if(clientID!='') $subjectMsg = $bodyContent['subject'];
                else $subjectMsg = $bodyContent[$case]['subject'];
            }    
            break;

            case '37': /* for cron job video attendies users */
            if(empty($Name) || empty($sessionName) || empty($dburl)){
                $checkerror ='';
                if(empty($Name)){$checkerror .='$Name'.'#';}
                if(empty($sessionName)){$checkerror .='$sessionName'.'#';}
                if(empty($dburl)){$checkerror .='$dburl'.'#';}
                $caseerror ='case = 37';        
            }else
            { 
                $data1 = array('[%%data_Name%%]','[%%Name%%]','[%%BASE_URL%%]','[%%dburl%%]','[%%VidDesc%%]',
                               '[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($Name,$sessionName,FRONT_URL,$dburl,$VidDesc,COMPANY_FOOTERTEXT);
                //$subjectMsg = $bodyContent[$case]['subject']; 

                if(clientID!='') $subjectMsg = $bodyContent['subject'];
                else $subjectMsg = $bodyContent[$case]['subject'];
            }    
            break;

            case '38': /* message send to users */
                if(empty($SName) || empty($RName)){
                    $checkerror ='';
                    if(empty($SName)){$checkerror .='$SName'.'#';}
                    if(empty($RName)){$checkerror .='$RName'.'#';}
                    $caseerror ='case = 38';        
                }
                else
                { 
                    $data1 = array('[%%RName%%]','[%%SName%%]','[%%Message%%]','[%%BASE_URL%%]','[%%COMPANY_FOOTERTEXT%%]');
                    $data2 = array($RName,$SName,$Message,FRONT_URL,COMPANY_FOOTERTEXT);
                    $datasub1 = array('[%%SName%%]');
                    $datasub2 = array($SName);
                    //$subjectMsg = str_replace($datasub1,$datasub2,$bodyContent[$case='37']['subject']);
                    if(clientID!='') $subjectMsg = str_replace($datasub1,$datasub2,$bodyContent['subject']);
                    else $subjectMsg = str_replace($datasub1,$datasub2,$bodyContent[$case]['subject']);
                    
                }    
            break;
                       
            } 



            if(isset($checkerror)){
             $errormessagemail = $this->dbeeErrotemplate($checkerror,$caseerror,$case);
            }else{  
                if(clientID!='')
                {
                    $bodyContentmsg = str_replace($data1,$data2,$bodyContent['body']); 
                  
                    $bodyContentmsg = html_entity_decode($bodyContentmsg);     
                    $messagemail = $this->dbeeComparetemplate($bodyContentmsg,$bodyContent['footertext']);
                }else
                {
                    $bodyContentmsg = str_replace($data1,$data2,$bodyContent[$case]['body']); 
                    $bodyContentmsg = html_entity_decode($bodyContentmsg);     
                    $messagemail = $this->dbeeComparetemplate($bodyContentmsg,$bodyContent[$case]['footertext']);
                }
            }      
            
            //$subjectMsg = str_replace("&amp;", "&", $subjectMsg);
            //echo $messagemail; exit;
            return $this->sendWithoutSmtpMail($Email,htmlspecialchars_decode($subjectMsg),FROM_MAIL,$messagemail);
    }
    
    
    public function dbeeErrotemplate($checkerror,$caseerror,$case){
        $deshboard   = new Application_Model_Commonfunctionality();
        $bodyContent = $deshboard->getGroupemailtemplate();
        $subjectMsg ='Some variables value are missing';
        $Email ='anildbee@gmail.com';
        $bodyContentmsg .="<table><tr>
                           <td style='padding:0px 30px 30px 30px;'>
                           <strong>These variables are not find</strong>,<br /> 
                           <br /><br />";
        $errorValues    =   explode('#', $checkerror);
        foreach($errorValues as $value){
          $bodyContentmsg .="<font color='#666' style='color:#ff7709;font-size:24px;
                               display:block; width:550px; word-wrap:break-word;'>
                                 ".$value."
                                </font> 
                                <br /><br />";
        }
        $bodyContentmsg .="<p>and error find in ".$caseerror."</p><br/>
                           ".COMPANY_FOOTERTEXT."</td></tr></table>";
        $messagemail = $this->dbeeComparetemplate($bodyContentmsg,$bodyContent[$case]['footertext']);
        $chk = $this->sendWithoutSmtpMail($Email,$subjectMsg,FROM_MAIL,$messagemail); 
        return $chk;
    }
     public function dbeeComparetemplate($bodyContentmsg,$footerContentmsg){
        $emailTemplatemain = $this->dbeeEmailtemplate();
        $emailTemplatejson = $this->myclientdetails->getfieldsfromtable('*','adminemailtemplates');
        $bodyContentjson = $emailTemplatejson[0]['emailtemplatejson'];
        //echo'<pre>';print_r($bodyContentjson);die;
        if($bodyContentjson==''){
                    $bodyContentjsonval = Array(
                                                'fontColor' => 'e4e4f0',
                                                'background' => 'e8e9eb', 
                                                'bodycontentfontColor' =>'e4e4f0',
                                                'bodycontentbacgroColor' =>'e8e9eb',
                                                'bodycontenttxture' =>'',
                                                'headerfontColor' =>'333',
                                                'headerbacgroColor' =>'fff',
                                                'headertxture' =>'',
                                                'contentbodyfontColor' => '333',
                                                'contentbodybacgroColor' => 'fff',
                                                'contentbodytxture' =>'',
                                                'bannerfreshimg' =>'dblogo-black.png',
                                                'footerfontColor' => '333',
                                                'footerbacgroColor' => 'd3d3d3',
                                                'footertxture' => '',
                                                'footerMsgval' => 'powered by db corporate social platforms'
                                               );
                    $bannerfreshimgdef ='dblogo-black.png';
        }
        else
        {
            $bodyContentjsonval = json_decode($bodyContentjson, true);
            //$bannerfreshimgdef ='http://www.db-csp.com/img/dblogo-black.png';
            $bannerfreshimgdef = $bodyContentjsonval['bannerfreshimg'];

        }
        
        $data1 = array('[%bodycontentbacgroColor%]','[%bodycontenttxture%]','[%headerbacgroColor%]',
        '[%headertxture%]','[%bannerfreshimg%]','[%contentbodyfontColor%]','[%contentbodybacgroColor%]',
        '[%contentbodytxture%]','[%%body%%]','[%footerfontColor%]','[%footerbacgroColor%]',
        '[%footertxture%]','[%footerfontColor%]','[%%footertext%%]','[%%COMPANY_NAME%%]');
        $data2 = array($bodyContentjsonval['bodycontentbacgroColor'],$bodyContentjsonval['bodycontenttxture'],
        $bodyContentjsonval['headerbacgroColor'],$bodyContentjsonval['headertxture'],
        $bannerfreshimgdef,$bodyContentjsonval['contentbodyfontColor'],
        $bodyContentjsonval['contentbodybacgroColor'],$bodyContentjsonval['contentbodytxture'],
        $bodyContentmsg,$bodyContentjsonval['footerfontColor'],$bodyContentjsonval['footerbacgroColor'],
        $bodyContentjsonval['footertxture'],$bodyContentjsonval['footerfontColor'],$footerContentmsg,COMPANY_NAME);
        return $messagemail = str_replace($data1,$data2,$emailTemplatemain);
    }
    
}
/*end of class */

// For sematra
class SessionCallbackHandler extends \Semantria\CallbackHandler
{
    function onRequest($sender, $args)
    {
        //$s = json_encode($args);
        //echo "REQUEST: ", htmlspecialchars($s), "\r\n";
    }

    function onResponse($sender, $args)
    {
        //$s = json_encode($args);
        //echo "RESPONSE: ", htmlspecialchars($s), "\r\n";
    }

    function onError($sender, $args)
    {
        $s = json_encode($args);
        echo "ERROR: ", htmlspecialchars($s), "\r\n";
    }

    function onDocsAutoResponse($sender, $args)
    {
        //$s = json_encode($args);
        //echo "DOCS AUTORESPONSE: ", htmlspecialchars($s), "\r\n";
    }

    function onCollsAutoResponse($sender, $args)
    {
        //$s = json_encode($args);
        //echo "COLLS AUTORESPONSE: ", htmlspecialchars($s), "\r\n";
    }
}

// old code 
/*class SessionCallbackHandler extends \Semantria\CallbackHandler
{
    function onRequest($sender, $args)
    {
        //$s = json_encode($args);
        //echo "REQUEST: ", htmlspecialchars($s), "\r\n";
    }

    function onResponse($sender, $args)
    {
        //$s = json_encode($args);
        //echo "RESPONSE: ", htmlspecialchars($s), "\r\n";
    }

    function onError($sender, $args)
    {
        $s = json_encode($args);
        echo "ERROR: ", htmlspecialchars($s), "\r\n";
    }

    function onDocsAutoResponse($sender, $args)
    {
        //$s = json_encode($args);
        //echo "DOCS AUTORESPONSE: ", htmlspecialchars($s), "\r\n";
    }

    function onCollsAutoResponse($sender, $args)
    {
        //$s = json_encode($args);
        //echo "COLLS AUTORESPONSE: ", htmlspecialchars($s), "\r\n";
    }

    
}*/
