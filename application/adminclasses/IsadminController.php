<?php

/**
 * This is site's main class/controller.
 */
class IsadminController extends Zend_Controller_Action
{   
    public function init()
    {
        parent::init();
        $auth =  Zend_Auth::getInstance();
        $storage = new Zend_Auth_Storage_Session(); 
        if ($auth->hasIdentity())
        {
            $this->_helper->layout()->setLayout('layout');
            $datasocial = $storage->read();
            
            $this->twitter_connect_data = Zend_Json::decode($datasocial['twitter_connect_data']);
            $this->view->userid = $this->_userid = $this->adminUserID = $datasocial['UserID'];
            $this->view->session_data = $this->session_data = $datasocial;
            defined('adminID') or define('adminID', $datasocial['UserID']);
            $this->view->deshboard = $this->deshboard    = new Admin_Model_Deshboard();
            $this->view->socialInvite = $this->socialInvite = new Admin_Model_Social();
            $this->view->user_model = $this->user_model = new Admin_Model_User();
            $this->view->user_group = $this->user_group = new Admin_Model_Usergroup();
            $this->view->myclientdetails = $this->myclientdetails = new Admin_Model_Clientdetails();
            $this->view->advert = $this->advert = new Admin_Model_Advert(); // get advert model object
            $this->advertdata = new Admin_Model_Advertdata(); // get advert data model object
            $this->view->advertRelation = $this->advertRelation = new Admin_Model_Advertrelation(); // get advert relation model object
            $this->view->group = $this->group = new Admin_Model_Group();
            $this->session_name_space = new  Zend_Session_Namespace('User_Session');
            $this->view->common_model = $this->common_model = new Admin_Model_Common();
            $this->userRec = new Admin_Model_reporting();
            $configurations = $this->deshboard->getConfigurations();
            $this->view->getSocialShareMsg = $this->getSocialShareMsg = Zend_Json::decode($configurations['SocialShareMsg']);
            $this->view->expertText = $this->expertText = $configurations['expert'];
            $this->view->getSeoConfiguration = $this->getSeoConfiguration = json_decode($configurations['seoContent']);
            $namespace = new Zend_Session_Namespace('Zend_Auth');
            $namespace->setExpirationSeconds((1209600));

            $authNamespace = new Zend_Session_Namespace('identify');
            $authNamespace->setExpirationSeconds((1209600));
        }
    }

    public function preDispatch()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) 
        {
            $this->_redirect('/admin/login');
        }
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

    function makeSeo($title, $raw_title = '', $context = 'display'){

         
        $dashboard_obj  = new Admin_Model_Deshboard();
        $dburl = $this->getdbeeurl($title, '', $context);
         
        if($dashboard_obj->chkdbeetitle($dburl)){
            return $dburl;
        }else{
            $words = explode(' ', $title);
            $title2 = implode(' ', array_slice($words, 0, 14)).'-'.date('i:s');
             
            return $dburl = $this->getdbeeurl($title2, '', $context);
        }
    }
    // Seo friendly url create function
    

    function getdbeeurl($title, $raw_title = '', $context = 'display')
    {

        $title = $this->string_limit_words($title, 15);

        $title = strip_tags($title);

        // Preserve escaped octets.

        $title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);

        // Remove percent signs that are not part of an octet.

        $title = str_replace('%', '', $title);

        // Restore octets.

        $title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);



        if ($this->seems_utf8($title)) {

            if (function_exists('mb_strtolower')) {

                $title = mb_strtolower($title, 'UTF-8');

            }

            $title = $this->utf8_uri_encode($title, 200);

        }



        $title = strtolower($title);

        $title = preg_replace('/&.+?;/', '', $title); // kill entities

        $title = str_replace('.', '-', $title);



        if ('save' == $context) {

            // Convert nbsp, ndash and mdash to hyphens

            $title = str_replace(array(

                    '%c2%a0',

                    '%e2%80%93',

                    '%e2%80%94'

            ), '-', $title);



            // Strip these characters entirely

            $title = str_replace(array(

                    // iexcl and iquest

                    '%c2%a1',

                    '%c2%bf',

                    // angle question

                    '%c2%ab',

                    '%c2%bb',

                    '%e2%80%b9',

                    '%e2%80%ba',

                    // curly question

                    '%e2%80%98',

                    '%e2%80%99',

                    '%e2%80%9c',

                    '%e2%80%9d',

                    '%e2%80%9a',

                    '%e2%80%9b',

                    '%e2%80%9e',

                    '%e2%80%9f',

                    // copy, reg, deg, hellip and trade

                    '%c2%a9',

                    '%c2%ae',

                    '%c2%b0',

                    '%e2%80%a6',

                    '%e2%84%a2'

            ), '', $title);



            // Convert times to x

            $title = str_replace('%c3%97', 'x', $title);

        }



        $title = preg_replace('/[^%a-z0-9 _-]/', '', $title);

        $title = preg_replace('/\s+/', '-', $title);

        $title = preg_replace('|-+|', '-', $title);

        $title = trim($title, '-');

        //$title = str_replace('%', '', strtolower(trim($title)));

        //$title = str_replace(' ', '-', strtolower(trim($title)));

        return $title;

    }

    function string_limit_words($string, $word_limit)
    {

        $words = explode(' ', $string);

        return implode(' ', array_slice($words, 0, $word_limit));

    }
    function seems_utf8($str)
    {

        $length = strlen($str);

        for ($i = 0; $i < $length; $i++) {

            $c = ord($str[$i]);

            if ($c < 0x80)
                $n = 0; // 0bbbbbbb

            elseif (($c & 0xE0) == 0xC0)
            $n = 1; // 110bbbbb
            elseif (($c & 0xF0) == 0xE0)
            $n = 2; // 1110bbbb
            elseif (($c & 0xF8) == 0xF0)
            $n = 3; // 11110bbb
            elseif (($c & 0xFC) == 0xF8)
            $n = 4; // 111110bb
            elseif (($c & 0xFE) == 0xFC)
            $n = 5; // 1111110b

            else
                return false; // Does not match any model

            for ($j = 0; $j < $n; $j++) { // n bytes matching 10bbbbbb follow ?

                if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
                    return false;

            }

        }

        return true;

    }

    function utf8_uri_encode($utf8_string, $length = 0)
    {

        $unicode = '';

        $values = array();

        $num_octets = 1;

        $unicode_length = 0;



        $string_length = strlen($utf8_string);

        for ($i = 0; $i < $string_length; $i++) {



            $value = ord($utf8_string[$i]);



            if ($value < 128) {

                if ($length && ($unicode_length >= $length))
                    break;

                $unicode .= chr($value);

                $unicode_length++;

            } else {

                if (count($values) == 0)
                    $num_octets = ($value < 224) ? 2 : 3;



                $values[] = $value;



                if ($length && ($unicode_length + ($num_octets * 3)) > $length)
                    break;

                if (count($values) == $num_octets) {

                    if ($num_octets == 3) {

                        $unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]) . '%' . dechex($values[2]);

                        $unicode_length += 9;

                    } else {

                        $unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]);

                        $unicode_length += 6;

                    }



                    $values = array();

                    $num_octets = 1;

                }

            }

        }

        return $unicode;

    }
    function rewritesession()
    {
        $options['UserID'] = $this->_userid;
        $userresult = $this->myclientdetails->getAllMasterfromtable('tblUsers','*',$options);
        $auth= Zend_Auth::getInstance();
        $auth->getStorage()->write($userresult[0]);
    }
    
    
    public function destroySession()
    {
        if (isset($_SERVER['HTTP_COOKIE'])) {
          $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
          foreach($cookies as $cookie) {
            $parts = explode('=', $cookie);
            $name = trim($parts[0]);
            setcookie($name, '', time()-1000, '/',".db-csp.com");
          }
        }
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        session_destroy();
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

    public function curl_del($path, $json='')
    {
        $url = $path;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $result = json_decode($result);
        curl_close($ch);
        return $result;
    }
    
    public function curlRequest($target_url,$post)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$target_url);
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        $result=json_decode(curl_exec ($ch));
        curl_close ($ch);
        return $result;
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
    return $emailTemplate;
    }
 
    public function dbeeComparetemplateOne($globalarr){
       
        extract($globalarr) ;
        $casetype  = (int)$case;
        $deshboard   = new Admin_Model_Deshboard();
        //$bodyContent = $deshboard->getGroupemailtemplate();
        $comonfun = new Application_Model_Commonfunctionality();
        
        if(clientID!='')
        {
            $case = '';
            $case = $casetype+1; 
            $bodyContent = array();
            $bodyContent = $comonfun->getGroupemailtemplate($case,'admin');
            $newchk = 1;
        }
        else{
            $bodyContent = $deshboard->getGroupemailtemplate();
            $newchk = 2;
        }

        /**** variable ******/
        $uEmail = $this->myclientdetails->customDecoding($uEmail);
        $uName = $this->myclientdetails->customDecoding($uName).' '.$this->myclientdetails->customDecoding($lname);
        $sendEmailvalue_uemail = $this->myclientdetails->customDecoding($sendEmailvalue_uemail);
        
        //  echo $Email.' # '.$uName.' # '.$Profile_URL.' :'.$case;die('====');
       
        switch ($casetype) {
            /****start for admin side ****/
            case '0':
                $data1 = array('[%%uName%%]','[%%COMPANY_NAME%%]','[%%FRONT_URL%%]','[%%FRONT_URL%%]',
                               '[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($uName,COMPANY_NAME,FRONT_URL,FRONT_URL,COMPANY_FOOTERTEXT); 
                $subjectMsg = $bodyContent[$case]['subject'];
            break;
            case '1':
                $data1 = array('[%%uName%%]','[%%COMPANY_NAME%%]','[%%FRONT_URL%%]','[%%FRONT_URL%%]',
                               '[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($uName,COMPANY_NAME,FRONT_URL,FRONT_URL,COMPANY_FOOTERTEXT); 
                $subjectMsg = $bodyContent[$case]['subject'];
            break;

            case '2':
            if(empty($uName)){
                $checkerror ='';
                if(empty($uName)){$checkerror .='$uName'.'#';}
                $caseerror ='case = 2';        
            }else{
                $data1 = array('[%%uName%%]','[%%COMPANY_NAME%%]','[%%FRONT_URL%%]',
                               '[%%FRONT_URL%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($uName,COMPANY_NAME,FRONT_URL,FRONT_URL,COMPANY_FOOTERTEXT); 
                $subjectMsg = $bodyContent[$case]['subject'];
            }
            break;

            case '3':
            if(empty($uName) || empty($message)){
                $checkerror ='';
                if(empty($uName)){$checkerror .='$uName'.'#';}
                if(empty($message)){$checkerror .='$message'.'#';}
                $caseerror ='case = 3';        
            }else{
                $data1 = array('[%%FIRST_NAME%%]','[%%BODY_MESSAGE%%]',
                               '[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($uName,$message,COMPANY_FOOTERTEXT); 
                //$subjectMsg = $bodyContent[$id]['subject'];
                $datasub1 = array('[%%subject%%]');
                $datasub2 = array($subject);
                $subjectMsg = str_replace($datasub1,$datasub2,$bodyContent[$case]['subject']);
            }
            break;
            case '4':
            if(empty($uName) || empty($db_url)){
                $checkerror ='';
                if(empty($uName)){$checkerror .='$uName'.'#';}
                if(empty($db_url)){$checkerror .='$db_url'.'#';}
                $caseerror ='case = 4';        
            }else{
                $data1 = array('[%%dataName%%]','[%%COMPANY_NAME%%]','[%%db_url%%]',
                               '[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($uName,COMPANY_NAME,$db_url,COMPANY_FOOTERTEXT); 
                $subjectMsg = $bodyContent[$case]['subject'];
            }
            break;
            case '5':
            if(empty($uName) || empty($db_url)){
                $checkerror ='';
                if(empty($uName)){$checkerror .='$uName'.'#';}
                if(empty($db_url)){$checkerror .='$db_url'.'#';}
                $caseerror ='case = 5';        
            }else{
                $data1 = array('[%%dataName%%]','[%%COMPANY_NAME%%]','[%%EVENTNAME%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($uName,COMPANY_NAME,$db_url,COMPANY_FOOTERTEXT); 
                $subjectMsg = $bodyContent[$case]['subject'];
            }
            break;

            case '6':
            if(empty($uName) || empty($db_url)){
                $checkerror ='';
                if(empty($uName)){$checkerror .='$uName'.'#';}
                if(empty($db_url)){$checkerror .='$db_url'.'#';}
                $caseerror ='case = 6';        
            }else{
                $data1 = array('[%%dataName%%]','[%%COMPANY_NAME%%]','[%%EVENTNAME%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($uName,COMPANY_NAME,$db_url,COMPANY_FOOTERTEXT);
                $subjectMsg = $bodyContent[$case]['subject'];             
            }
            break;
         
            case '7':
            if(empty($uName) || empty($request_grp_name)){
                $checkerror ='';
                if(empty($uName)){$checkerror .='$uName'.'#';}
                if(empty($request_grp_name)){$checkerror .='$request_grp_name'.'#';}
                $caseerror ='case = 7';        
            }else{
                $data1 = array('[%%dataName%%]','[%%FRONT_URL%%]','[%%FRONT_URL%%]','[%%sendEmailvalue_uemail%%]','[%%request_grp_name%%]','[%%COMPANY_NAME%%]','[%%FRONT_URL%%]','[%%sendEmailvalue_token%%]','[%%FRONT_URL%%]','[%%sendEmailvalue_token%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($uName,FRONT_URL,FRONT_URL,$sendEmailvalue_uemail,$request_grp_name,COMPANY_NAME,FRONT_URL,$sendEmailvalue_token,FRONT_URL,$sendEmailvalue_token,COMPANY_FOOTERTEXT);
                $datasub1 = array('[%%COMPANY_NAME%%]');
                $datasub2 = array(COMPANY_NAME);
                $subjectMsg = str_replace($datasub1,$datasub2,$bodyContent[$case]['subject']);             
            }
            break;

            case '36':
            if(empty($uName) || empty($request_grp_name)){ 
                $checkerror ='';
                if(empty($uName)){$checkerror .='$uName'.'#';}
                if(empty($request_grp_name)){$checkerror .='$request_grp_name'.'#';}
                $caseerror ='case = 36';        
            }else{
                $data1 = array('[%%dataName%%]','[%%FRONT_URL%%]','[%%FRONT_URL%%]','[%%sendEmailvalue_uemail%%]','[%%request_grp_name%%]','[%%COMPANY_NAME%%]','[%%FRONT_URL%%]','[%%sendEmailvalue_token%%]','[%%FRONT_URL%%]','[%%sendEmailvalue_token%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($uName,FRONT_URL,FRONT_URL,$sendEmailvalue_uemail,$request_grp_name,COMPANY_NAME,FRONT_URL,$sendEmailvalue_token,FRONT_URL,$sendEmailvalue_token,COMPANY_FOOTERTEXT);
                $datasub1 = array('[%%COMPANY_NAME%%]');
                $datasub2 = array(COMPANY_NAME);
                $case = 36;
                $subjectMsg = str_replace($datasub1,$datasub2,$bodyContent[$case]['subject']); 
             
            }
            break;

            case '47':
              if(clientID==21){$case=46;}
              else if(clientID==22){$case=46;}  
            if(empty($uName) || empty($Profile_URL)){ 
                $checkerror ='';
                if(empty($uName)){$checkerror .='$uName'.'#';}
                if(empty($Profile_URL)){$checkerror .='$Profile_URL'.'#';}
                $caseerror ='case = 47';        
            }else{
                $data1 = array('[%%uName%%]','[%%Profile_URL%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($uName,$Profile_URL,COMPANY_FOOTERTEXT);
                $subjectMsg = $bodyContent[$case]['subject'];            
            }
            break;

            case '8':
              if(clientID==21){$case=6;}
              else if(clientID==22){$case=6;}  
            if(empty($uName) || empty($Profile_URL)){ 
                $checkerror ='';
                if(empty($uName)){$checkerror .='$uName'.'#';}
                if(empty($Profile_URL)){$checkerror .='$Profile_URL'.'#';}
                $caseerror ='case = 8';        
            }else{
                $data1 = array('[%%uName%%]','[%%Profile_URL%%]','[%%COMPANY_FOOTERTEXT%%]');
                $data2 = array($uName,$Profile_URL,COMPANY_FOOTERTEXT);
                $subjectMsg = $bodyContent[$case]['subject'];            
            }
            break;
         }

        if(isset($checkerror)){
            $errormessagemail = $this->dbeeErrotemplate($checkerror,$caseerror,$case);
        }else{   
            /*$bodyContentmsg = str_replace($data1,$data2,$bodyContent[$case]['body']);
            $bodyContentmsg = html_entity_decode($bodyContentmsg);
            $messagemail = $this->dbeeComparetemplate($bodyContentmsg,$bodyContent[$case]['footertext']);*/

            if($newchk==1){
                $bodyContentmsg = str_replace($data1,$data2,$bodyContent['body']);
                $bodyContentmsg = html_entity_decode($bodyContentmsg);
                $messagemail = $this->dbeeComparetemplate($bodyContentmsg,$bodyContent['footertext']);
                $subjectMsg = $bodyContent['subject'];
            }
            elseif($newchk==2){
                $bodyContentmsg = str_replace($data1,$data2,$bodyContent[$case]['body']);
                $bodyContentmsg = html_entity_decode($bodyContentmsg);
                $messagemail = $this->dbeeComparetemplate($bodyContentmsg,$bodyContent[$case]['footertext']);
                $subjectMsg = $bodyContent[$case]['subject'];
            }
        }
       // echo $uEmail.' '.$messagemail.' '.$subjectMsg; exit;  
        $chk = $this->myclientdetails->sendWithoutSmtpMail($uEmail,htmlspecialchars_decode($subjectMsg),FROM_MAIL,$messagemail); 
        return $chk;
    }

    public function dbeeErrotemplate($checkerror,$caseerror,$id){
        $deshboard   = new Admin_Model_Deshboard();
        $bodyContent = $deshboard->getGroupemailtemplate();
        $subjectMsg ='Some variables value are missing';
        //mann.delus@gmail.com,anildbee@gmail.com,porwal.deshbandhu@gmail.com 
        $uEmail ='anildbee@gmail.com';
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
        $messagemail = $this->dbeeComparetemplate($bodyContentmsg,$bodyContent[$id]['footertext']);
        
        return $this->myclientdetails->sendWithoutSmtpMail($uEmail,$subjectMsg,FROM_MAIL,$messagemail); 
        
    }

    public function dbeeComparetemplate($bodyContentmsg,$footerContentmsg){
        $deshboard   = new Admin_Model_Deshboard();
        $emailTemplatemain = $this->dbeeEmailtemplate();
        $emailTemplatejson = $deshboard->getfieldsfromtable(array('id','emailtemplatejson','htmllayout'),
                             'adminemailtemplates','clientID',clientID);
        $bodyContentjson = $emailTemplatejson[0]['emailtemplatejson'];
        
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

        return $messagemail = str_replace($data1,$data2,$emailTemplatemain);
    } 

    // SlideShare
    public function isValidSlideShareURL( $url ) {

        $parse = parse_url($url);
        $host  = strtolower( $parse['host'] );
        if ( !in_array( $host, array( 'www.slideshare.net', 'slideshare.net', 'es.slideshare.net', 'fr.slideshare.net', 'pt.slideshare.net','de.slideshare.net'  ) ) ) {
            return false;
        }
    
        $ch = curl_init();
        $oembedURL = 'www.slideshare.net/api/oembed/2?url=' . urlencode( $url ).'&format=json';
        curl_setopt( $ch, CURLOPT_URL, $oembedURL );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        
        
        $output = curl_exec( $ch );
        //unset( $output );
    
        $info = curl_getinfo( $ch );
        curl_close( $ch );
    
        if ( $info['http_code'] !== 404 ) {
            return json_decode( $output );
        } else {
            return false;
        }
    }

    public function file_get_contents_curl($url) {
        $ch = curl_init();
        //Mozilla/32.0.3 (X11; Linux x86_64) AppleWebKit/537.16 (KHTML, like Gecko) \ Chrome/38.0.2125.104 Safari/537.16
        $ua = 'Mozilla/32.0.3 (X11; Linux x86_64) AppleWebKit/537.16 (KHTML, like Gecko) \ 
        Chrome/38.0.2125.104 Safari/537.16';
        curl_setopt($ch, CURLOPT_USERAGENT, $ua);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 5 );
        curl_setopt( $ch, CURLOPT_TIMEOUT, 5 );
        curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
        $data = curl_exec($ch);
        curl_close($ch);
        
        return $data;
    }

    public function getYoutubeId( $url ) {
     $pattern = 
         '%^# Match any youtube URL
        (?:https?://)? 
        (?:www\.)?     
        (?:             
          youtu\.be/    
        | youtube\.com  
          (?:           
            /embed/    
          | /v/         
          | .*v=        
          )            
        )              
        ([\w-]{10,12})  
        ($|&).*         
        $%x'
        ;
            ;
        $result = preg_match( $pattern, $url, $matches );
        if ( false !== $result ) {
            return $matches[1];
        }
        return false;
    }

    public function isValidYoutubeURL( $url ) {

        $parse = parse_url($url);
        $host  = strtolower( $parse['host'] );
        if ( !in_array( $host, array( 'youtube.com', 'www.youtube.com', 'youtu.be', 'www.youtu.be' ) ) ) {
            return false;
        }
    
        $ch = curl_init();
        $oembedURL = 'www.youtube.com/oembed?url=' . urlencode($url).'&format=json';
        curl_setopt( $ch, CURLOPT_URL, $oembedURL );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        
        
        $output = curl_exec( $ch );
        //unset( $output );
    
        $info = curl_getinfo( $ch );
        curl_close( $ch );
    
        if ( $info['http_code'] !== 404 ) {
            return json_decode( $output );
        }  else {
            return false;
        }
    }//<-------- FUNCTION END
      
}


