<?php

/**
 * This is site's main class/controller.  added by deshbandhu
 */
class IsloginController extends Zend_Controller_Action
{
    
    public $_config;
    
    public function init()
    {
        // Call method from mother class
        parent::init();
        $data = array();        
        // pass config details and vars to views
        $this->_config = Zend_Registry::get("config");
        $this->view->config = $this->_config;
         // get model object
        $this->user_session = new  Zend_Session_Namespace('User_Session');
        
        $this->group_session = new Zend_Session_Namespace('Group_Session');
        $this->view->myclientdetails = $this->myclientdetails = new Application_Model_Clientdetails();
        $this->notification = new Application_Model_Notification();
        $this->notificationsetting = new Application_Model_DbNotificationsettings();
        $this->commonmodel_obj = new Application_Model_Commonfunctionality();
        $this->Myhome_Model = new Application_Model_Myhome();
        $this->groupModel = new Application_Model_Groups();
        $this->user_model = new Application_Model_DbUser();
        $userresult = $this->myclientdetails->getfieldsfromtable('*','tblUsers','role',1,'clientID',clientID);
        defined('adminID') or define('adminID', $userresult[0]['UserID']);
        $this->view->Adminresult = $this->Adminresult = $userresult[0];    
        $configurations = $this->commonmodel_obj->getConfiguration();
        if(isset($configurations['expert']))
            $this->view->expertText = $this->expertText = $configurations['expert'];
        $this->view->getSeoConfiguration = json_decode($configurations['seoContent']);
        if(isset($_COOKIE['themepreview']) && $_COOKIE['themepreview']=='show')
            $this->view->configuration = $this->configuration = json_decode($configurations['tempContent']);
        else
            $this->view->configuration = $this->configuration = json_decode($configurations['content']);
        $this->view->SocialLogo = $this->SocialLogo = $configurations['SocialLogo'];

    }
    function getDomainFromEmail($email)
    {
        // Get the data after the @ sign
        return substr(strrchr($email, "@"), 1);
    }

    
    public function makegroupmember($groupid,$social,$loginid)
    {
        $grupdetais = $this->groupModel->groupuserdetail($groupid);
        $groupinfo = array(
                'GroupID' => $groupid,
                'Owner' => $grupdetais[0]['UserID'],
                'User' => $loginid,
                'JoinDate' => date('Y-m-d H:i:s'),
                'SentBy' => 'Owner',
                'Status' => '1',
                'clientID' => clientID
        );
        $this->groupModel->insertgroup($groupinfo);
        $this->groupModel->dellockgroupsocial($social,$groupid);
    }

    public function sendForLocalSmtpMail($to,$setSubject, $from ='contact@dbee.me', $setBodyText)
    {
        if($this->getDomainFromEmail($to)=='dbee.me')
            return true;
        
        $config = array('auth' => 'login',
                        'username' => $this->_config->smtp->username,
                        'password' => smtpkey,
                        'port' => $this->_config->smtp->port);
        print_r($config); 
        $transport = new Zend_Mail_Transport_Smtp($this->_config->smtp->smtp_server, $config);
         
        $mail = new Zend_Mail('UTF-8');
        $mail->setBodyHtml($setBodyText);
        $mail->setFrom($from, FromName);
        $mail->addTo($to, $tofrom);
        $mail->setSubject($setSubject);
        try {
         
            $mail->send($transport);
        } catch (Zend_Mail_Transport_Exception $e) {
            print_r($e);
        }
    }
    public function sendMailToFaceBookUser($data)
    {
        $type == '';    
        if($data['Socialtype'] == 1)
            $type = 'Facebook';
        else if($data['Socialtype'] == 3)
            $type = 'LinkedIn';
        else if($data['Socialtype'] == 4)
            $type = 'Google';
        
        $EmailTemplateArray = array('Email' => $data['Email'],
                                       'Name'=> $data['Name'],
                                       'lname' => $data['lname'],
                                       'type'=> $type,
                                       'case'=> 20);
         $bodyContentmsgall = $this->dbeeComparetemplateOne($EmailTemplateArray);
    }
    public function sendWithoutSmtpMail($to, $setSubject, $from ='no-reply@db-csp.com', $setBodyText)
    {
        if ($this->_config->smtp->status == 1) 
        {
            $this->swiftMail($to,$setSubject,$from,$setBodyText);
        }else
        {
            $mail = new Zend_Mail();
            $rawat = new Zend_Mail_Transport_Sendmail('-fnoreply@db-csp.com');
            Zend_Mail::setDefaultTransport($rawat);
            $mail->setBodyHtml(html_entity_decode($setBodyText));
            $mail->setFrom($from, FromName);
            $mail->addTo($to);
            $mail->setSubject($setSubject);
            if($to!='twitteruser@db-csp.com'){
               return $mail->send();
            }
        }
    }

    public function swiftMail($to, $setSubject, $from ='no-reply@db-csp.com', $setBodyText)
    {
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


    public function dbeeEmailtemplate(){
    $myImgurl = FRONT_URL.'/adminraw/img/bgs/';
    $logoImgurl = FRONT_URL.'/adminraw/img/emailbgimage/'; 
    return '<table width="100%"><tbody><tr>
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
                                                    <div align="right"><a href="http://www.db-csp.com" style="text-decoration:none; color:#[%footerfontColor%]" target="_blank"  editType="footercontent" id="footerMsg">
                                                        [%%footertext%%]
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
    }
    public function sendMailToVideoEvent($data)
    {
       $eventName = '<a href="'.BASE_URL.'/dbee/'.$data['dburl'].'">'.$data['Text'].'</a>';
       $EmailTemplateArray = array('Email' => $this->myclientdetails->customEncoding('porwal.deshbandhu@gmail.com'),
                                   'Name' => $data['Name'],
                                   'lname' => $data['lname'],
                                   'EVENTNAME' => $eventName,
                                   'case'=>37);
      $this->dbeeComparetemplateOne($EmailTemplateArray);
        
    }


     public function dbeeComparetemplateOne($globalarr)
     {

            extract($globalarr); 
            $deshboard   = new Application_Model_Commonfunctionality();
            $casetype  = (int)$case;
            $Email = $this->myclientdetails->customDecoding($Email); 
            $fulname = $this->myclientdetails->customDecoding($Name).' '.$this->myclientdetails->customDecoding($lname);
            $Name = $this->myclientdetails->customDecoding($Name).' '.$this->myclientdetails->customDecoding($lname);
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
            
            if(clientID!='')
            {
                $case = '';
                $case = $casetype+1; 
                $bodyContent = array();
                $bodyContent = $deshboard->getGroupemailtemplate($case,'front');
              
            }
            else
            {
                $bodyContent = $deshboard->getGroupemailtemplate();
            }
            
          switch ($casetype){
                
            case '6': /* New account activation email section */
                /*if(clientID!='')
                    {
                         echo $bodyContent['subject'];
                         echo "<pre>"; print_r($bodyContent);
                         exit;
                    }*/
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
                $data2 = array($Name,COMPANY_NAME,BASE_URL,$UserID,BASE_URL,$UserID,
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
                $data2 = array(BASE_URL,$errormessage,$controller,$action,$url,COMPANY_FOOTERTEXT);
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
                            '[%%BASE_URL%%]','[%%db%%]','[%%dbeeTextval%%]','[%%UserRow_Name%%]',
                            '[%%commTextval%%]','[%%COMPANY_FOOTERTEXT%%]','[%%COMPANY_NAME%%]',
                            '[%%COMPANY_NAME%%]');
                $data2 = array(BASE_URL,$UserRowProfilePic,$Name,BASE_URL,$db,$dbeeTextval,
                                 $Name,$commTextval,COMPANY_FOOTERTEXT,COMPANY_NAME,COMPANY_NAME);
                //$subjectMsg = $bodyContent[$case]['subject'];
                if(clientID!='') $subjectMsg = $bodyContent['subject'];
                 else $subjectMsg = $bodyContent[$case]['subject'];
            }
            break; 

            case '12': /* Expert removal section */
            if(empty($Name) || empty($title)){
                $checkerror ='';
                if(empty($Name)){$checkerror .='$Name'.'#';}
                if(empty($title)){$checkerror .='$title'.'#';}
                $caseerror ='case = 12';        
            }else{
                $data1 = array('[%%data_Name%%]','[%%title%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($Name,$title,COMPANY_FOOTERTEXT);
                //$subjectMsg = $bodyContent[$case]['subject'];
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
                $data1 = array('[%%label%%]','[%%BASE_URL%%]','[%%cookieuser%%]',
                     '[%%ReportingUser%%]','[%%Text%%]','[%%BASE_URL%%]','[%%dbee%%]',
                     '[%%POST_NAME%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($label,BASE_URL,$cookieuser,$ReportingUser,$Text,BASE_URL,
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
                $data1 = array('[%%label%%]','[%%BASE_URL%%]','[%%cookieuser%%]',
                     '[%%ReportingUser%%]','[%%Text%%]','[%%BASE_URL%%]','[%%dbee%%]',
                     '[%%POST_NAME%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($label,BASE_URL,$cookieuser,$ReportingUser,$Text,BASE_URL,
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
                $data1 = array('[%%BASE_URL%%]','[%%Row_UserID%%]','[%%Row_Name%%]','[%%bug%%]',
                               '[%%userbrowser%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array(BASE_URL,$UserID,$Name,$bug,$userbrowser,COMPANY_FOOTERTEXT);
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
                $data1 = array('[%%BASE_URL%%]','[%%Row_UserID%%]','[%%Row_Name%%]','[%%bug%%]',
                               '[%%userbrowser%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array(BASE_URL,$UserID,$Name,$bug,$userbrowser,COMPANY_FOOTERTEXT);
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
                $data2 = array($Name,BASE_URL,$userleaguesend,$userleagueacce,BASE_URL,$leagueid,
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
                $data1 = array('[%%GroupName%%]','[%%GroupAdmin%%]','[%%COMPANY_NAME%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($GroupName,$GroupAdmin,COMPANY_NAME,COMPANY_FOOTERTEXT);
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
                $data1 = array('[%%userName%%]','[%%GroupName%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($fulname,'ppppppp',COMPANY_FOOTERTEXT);
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
                $data2 = array(BASE_URL,$FollowerUserID,BASE_URL,$FollowerProfilePicval,BASE_URL,
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
                $data2 = array(BASE_URL,$UserRowProfilePic,BASE_URL,$Username,$Name,$label,
                         $scorelabel,BASE_URL,$MainDB,$Textval,COMPANY_FOOTERTEXT,COMPANY_NAME,COMPANY_NAME);
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
                $data2 = array(BASE_URL,$UserRowProfilePic,$Name,POST_NAME,BASE_URL,$db,$dbeeText,
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
            if(empty($Name) || empty($sessionName) || empty($url)){
                $checkerror ='';
                if(empty($Name)){$checkerror .='$Name'.'#';}
                if(empty($sessionName)){$checkerror .='$sessionName'.'#';}
                if(empty($url)){$checkerror .='$url'.'#';}
                $caseerror ='case = 29';        
            }else{
                $data1 = array('[%%data_Name%%]','[%%Name%%]','[%%BASE_URL%%]','[%%url%%]',
                               '[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($Name,$sessionName,BASE_URL,$url,COMPANY_FOOTERTEXT);
                //$subjectMsg = $bodyContent[$case]['subject'];
                if(clientID!='') $subjectMsg = $bodyContent['subject'];
                else $subjectMsg = $bodyContent[$case]['subject'];
            }    
            break;    
                
            case '30': /* New attendee for video event section*/
            if(empty($Name) || empty($VidDesc) || empty($dburl)){
                $checkerror ='';
                if(empty($Name)){$checkerror .='$Name'.'#';}
                if(empty($VidDesc)){$checkerror .='$VidDesc'.'#';}
                if(empty($dburl)){$checkerror .='$dburl'.'#';}
                $caseerror ='case = 30';        
            }else{
                $data1 = array('[%%data_Name%%]','[%%dbee_array_VidDesc%%]','[%%BASE_URL%%]',
                               '[%%dbee_array_dburl%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($Name,$VidDesc,BASE_URL,$dburl,COMPANY_FOOTERTEXT);
                //$subjectMsg = $bodyContent[$case]['subject'];
                if(clientID!='') $subjectMsg = $bodyContent['subject'];
                else $subjectMsg = $bodyContent[$case]['subject'];
            }    
            break;

            case '31': /* New attendee for video event section*/
            if(empty($Name) || empty($dburl)){
                $checkerror ='';
                if(empty($Name)){$checkerror .='$Name'.'#';}
                if(empty($dburl)){$checkerror .='$dburl'.'#';}
                $caseerror ='case = 31';        
            }else{
                $data1 = array('[%%data_Name%%]','[%%COMPANY_NAME%%]','[%%BASE_URL%%]',
                               '[%%dbee_array_dburl%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($Name,COMPANY_NAME,BASE_URL,$dburl,COMPANY_FOOTERTEXT);
                //$subjectMsg = $bodyContent[$case]['subject'];
                if(clientID!='') $subjectMsg = $bodyContent['subject'];
                else $subjectMsg = $bodyContent[$case]['subject'];
            }    
            break;

            case '32': /* User request to join video event section*/
            if(empty($Name) || empty($VidDesc) || empty($dburl)){
                $checkerror ='';
                if(empty($Name)){$checkerror .='$Name'.'#';}
                if(empty($VidDesc)){$checkerror .='$VidDesc'.'#';}
                if(empty($dburl)){$checkerror .='$dburl'.'#';}
                $caseerror ='case = 32';        
            }else{
                $data1 = array('[%%name%%]','[%%dbee_array_VidDesc%%]','[%%BASE_URL%%]',
                               '[%%dbee_array_dburl%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($Name,$VidDesc,BASE_URL,$dburl,COMPANY_FOOTERTEXT);
                //$subjectMsg = $bodyContent[$case]['subject'];
                if(clientID!='') $subjectMsg = $bodyContent['subject'];
                else $subjectMsg = $bodyContent[$case]['subject'];
            }    
            break;

            case '33': /* Video event participation request section*/
            if(empty($Name) || empty($VidDesc) || empty($dburl)){
                $checkerror ='';
                if(empty($Name)){$checkerror .='$Name'.'#';}
                if(empty($VidDesc)){$checkerror .='$VidDesc'.'#';}
                if(empty($dburl)){$checkerror .='$dburl'.'#';}
                $caseerror ='case = 33';        
            }else{
                $data1 = array('[%%data_Name%%]','[%%dbee_array_VidDesc%%]','[%%BASE_URL%%]',
                               '[%%dbee_array_dburl%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($Name,$VidDesc,BASE_URL,$dburl,COMPANY_FOOTERTEXT);
                //$subjectMsg = $bodyContent[$case]['subject'];
                if(clientID!='') $subjectMsg = $bodyContent['subject'];
                else $subjectMsg = $bodyContent[$case]['subject'];
            }    
            break;

            case '34': /* Your question answered section*/
            if(empty($Name) || empty($sessionName) || empty($url)){
                $checkerror ='';
                if(empty($Name)){$checkerror .='$Name'.'#';}
                if(empty($sessionName)){$checkerror .='$sessionName'.'#';}
                if(empty($url)){$checkerror .='$url'.'#';}
                $caseerror ='case = 34';        
            }else{
                $data1 = array('[%%data_Name%%]','[%%Name%%]','[%%BASE_URL%%]','[%%url%%]',
                               '[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($Name,$sessionName,BASE_URL,$url,COMPANY_FOOTERTEXT);
                //$subjectMsg = $bodyContent[$case]['subject'];
                if(clientID!='') $subjectMsg = $bodyContent['subject'];
                else $subjectMsg = $bodyContent[$case]['subject'];
            }    
            break;

            case '37': /* for cron job video attendies users */
            if(empty($Name) || empty($eventName)){
                $checkerror ='';
                if(empty($Name)){$checkerror .='$Name'.'#';}
                if(empty($eventName)){$checkerror .='$eventName'.'#';}
                $caseerror ='case = 37';        
            }else
            { 
                $data1 = array('[%%data_Name%%]','[%%Name%%]','[%%eventName%%]',
                               '[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($Name,$sessionName,$eventName,COMPANY_FOOTERTEXT);
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
                $data2 = array($RName,$SName,$Message,BASE_URL,COMPANY_FOOTERTEXT);
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
           
            return $this->sendWithoutSmtpMail($Email,$subjectMsg,FROM_MAIL,$messagemail); 
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
        return $this->sendWithoutSmtpMail($Email,$subjectMsg,FROM_MAIL,$messagemail); 
        
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
        '[%footertxture%]','[%footerfontColor%]','[%%footertext%%]');
        $data2 = array($bodyContentjsonval['bodycontentbacgroColor'],$bodyContentjsonval['bodycontenttxture'],
        $bodyContentjsonval['headerbacgroColor'],$bodyContentjsonval['headertxture'],
        $bannerfreshimgdef,$bodyContentjsonval['contentbodyfontColor'],
        $bodyContentjsonval['contentbodybacgroColor'],$bodyContentjsonval['contentbodytxture'],
        $bodyContentmsg,$bodyContentjsonval['footerfontColor'],$bodyContentjsonval['footerbacgroColor'],
        $bodyContentjsonval['footertxture'],$bodyContentjsonval['footerfontColor'],$footerContentmsg);

        return str_replace($data1,$data2,$emailTemplatemain);
    }  
    
    public function globalSettings()
    {
        $Result = $this->user_model->globalSetting();  
        if($Result[0]['social_account']==0)
            $this->user_session->Social_Content_Block = 'unblock';
        else
            $this->user_session->Social_Content_Block = 'block';
    }
    public function getGlobalSettings()
    {
        $Result = $this->user_model->globalSetting();   
        if($Result[0]['social_account']==0)
            return 'unblock';
        else
            return 'block';
    }
    public function redirection($username)
    {
        
        if($this->user_session->redirection!='')
            $this->_redirect($this->user_session->redirection);
        else
            $this->_redirect(BASE_URL."/user/".$username);
        /*if($this->user_session->redirection!='')
            $this->_redirect($this->user_session->redirection);
        else
            $this->_redirect(BASE_URL."/myhome/");*/
    }
}

/*end of class */
