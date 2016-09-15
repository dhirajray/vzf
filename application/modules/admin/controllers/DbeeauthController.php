<?php

class Admin_DbeeauthController extends IsadminController
{
    
    //private $options;
    public function init()
    {
        parent::init();  
        $this->infobj = new Admin_Model_Dbeeauth();   
    }
    
    public function indexAction()
    {
        $clientlist                  = $this->infobj->fetchallclient();        
        $this->view->clientlist      = $clientlist; 

    }    

    public function fetchdomainapiAction()
    {
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            $request                =   $this->getRequest()->getPost();   
            $clint_id               = $this->_request->getPost('clientid'); 
            $ApilData=$this->myclientdetails->passSQLquery("SELECT * FROM `domainAPI` WHERE clientID = '".$clint_id."'");

            //print_r($ApilData);

            $ApilData2=$this->myclientdetails->passSQLquery("SELECT tc.clientName,tc.clientID,da.id FROM `tblClient` as tc,`domainAPI` as da WHERE  tc.clientID=da.clientID");

            if(count($ApilData2) > 0)
            {
                $data['select']='<select id="SelectClient"><option valu="">Select Client </option>';
                foreach ($ApilData2 as $key => $value) {
                   $data['select'].='<option value="'.$value['clientID'].'">'.$value['clientName'].'</option>';
                }
                $data['select'].='</select>';
            }

             $data['status'] = 'success';
             $data['content'] = $ApilData;  

        }
        else 
        {
            $data['status']  = 'error';
            $data['content'] = 'something wrong';
        }
        return $response->setBody(Zend_Json::encode($data)); 

    }

    public function updateclientsocialapiAction()
    {
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            
            $request                =   $this->getRequest()->getPost();   
            $clint_id               = $this->_request->getPost('clint_id');        
            $client_type            = $this->_request->getPost('client_type');
            $smtp_key               = $this->_request->getPost('smtp_key');
            $short_url_api          = $this->_request->getPost('short_url_api');
            $facebook_app_id        = $this->_request->getPost('facebook_app_id');
            $facebook_secret        = $this->_request->getPost('facebook_secret');
            $facebook_domain        = $this->_request->getPost('facebook_domain');
            $linkedIn_app_id        = $this->_request->getPost('linkedIn_app_id');
            $linkedIn_secret        = $this->_request->getPost('linkedIn_secret');
            $twitter_app_id         = $this->_request->getPost('twitter_app_id');
            $twitter_secret         = $this->_request->getPost('twitter_secret');
            $twitter_access_token   = $this->_request->getPost('twitter_access_token');
            $twitter_access_secret  = $this->_request->getPost('twitter_access_secret');
            $gplus_app_id           = $this->_request->getPost('gplus_app_id');
            $gplus_secret           = $this->_request->getPost('gplus_secret');
            $gplus_redirect_url     = $this->_request->getPost('gplus_redirect_url');
            $sematria_key           = $this->_request->getPost('sematria_key');
            $sematria_secret        = $this->_request->getPost('sematria_secret');
            $google_email           = $this->_request->getPost('google_email');
            $google_password        = $this->_request->getPost('google_password');

            if($client_type=='')
            {
                $data['status'] = 'error';
                $data['message'] = 'Please select Client Type';
            }
            else
            {
                $wherearr['clientID'] = $clint_id;
                $ApilData=$this->myclientdetails->passSQLquery("SELECT `clientID` FROM `domainAPI` WHERE clientID = '".$clint_id."'");
                
                if(count($ApilData) < 1)
                {
                 $dataArray2 = array('clientID'=>$clint_id,'clientType'=>$client_type,'smtpkey'=>$smtp_key,'short_url_api'=>$short_url_api,'facebookAppid'=>$facebook_app_id,'facebookSecret'=>$facebook_secret,'facebookDomain'=>$facebook_domain,'linkedinAppid'=>$linkedIn_app_id,'linkedinSecret'=>$linkedIn_secret,'twitterAppid'=>$twitter_app_id,'twitterSecret'=>$twitter_secret,'twitterAccessToken'=>$twitter_access_token,'twitterAccessSecret'=>$twitter_access_secret,'gplusClientappid'=>$gplus_app_id,'gplusClientSecret'=>$gplus_secret,'gplusRedierctUrl'=>$gplus_redirect_url,'sematriaKey'=>$sematria_key,'semantriaSecret'=>$sematria_secret,'google_email'=>$google_email,'google_password'=>$google_password,'createdDate'=>date('Y-m-d H:i:s'));
                                 
                    $SocialApiInsertion = $this->infobj->insertdata_global('domainAPI',$dataArray2);
                }
                else
                {


                     $dataArray2 = array('clientType'=>$client_type,'smtpkey'=>$smtp_key,'short_url_api'=>$short_url_api,'facebookAppid'=>$facebook_app_id,'facebookSecret'=>$facebook_secret,'facebookDomain'=>$facebook_domain,'linkedinAppid'=>$linkedIn_app_id,'linkedinSecret'=>$linkedIn_secret,'twitterAppid'=>$twitter_app_id,'twitterSecret'=>$twitter_secret,'twitterAccessToken'=>$twitter_access_token,'twitterAccessSecret'=>$twitter_access_secret,'gplusClientappid'=>$gplus_app_id,'gplusClientSecret'=>$gplus_secret,'gplusRedierctUrl'=>$gplus_redirect_url,'sematriaKey'=>$sematria_key,'semantriaSecret'=>$sematria_secret,'google_email'=>$google_email,'google_password'=>$google_password);

                    $SocialApiUpdate = $this->infobj->updatedata_global('domainAPI',$dataArray2,'clientID',$clint_id);

                }

                $data['status'] = 'success';
                $data['message'] = 'Client domain API updated successfully';
               
             
            }
        }
        else 
        {
            $data['status']  = 'error';
            $data['message'] = 'something wrong';
        }
        return $response->setBody(Zend_Json::encode($data)); 

    }



    public function updatestatusAction()
    {
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            
            $request                =   $this->getRequest()->getPost();   
            $clint_id               = $this->_request->getPost('clientid');        
            $status                 = $this->_request->getPost('status');
            if($status==1)
            {
                $updstatus=0;
            }else
            {
                $updstatus=1;
            }

            $dataArraystatus = array('clientStatus'=>$updstatus);
            $SocialApiUpdate = $this->infobj->updatedata_global('tblClient',$dataArraystatus,'clientID',$clint_id);

            $clientData=$this->myclientdetails->passSQLquery("SELECT clientStatus FROM `tblClient` as tc WHERE clientID='".$clint_id."'");

            $statusvalue=$clientData[0]['clientStatus'];
            if($statusvalue==1)
             {
                 $clientStatustext='<span style="color:green">Active</span>';
              }
              else
              {
                 $clientStatustext='<span style="color:#ff0000">Deactive</span>';
              }
        
            $data['status']     = 'success';
            $data['statusvale'] = $statusvalue;
            $data['statustext'] = $clientStatustext;
            $data['id']         = $clint_id;
            $data['message']    = 'Status update successfully';             
           
        }
        else 
        {
            $data['status']  = 'error';
            $data['message'] = 'something wrong';
        }
        return $response->setBody(Zend_Json::encode($data)); 

    }

    

    public function insertAction()
    {
        
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            
            $request          =   $this->getRequest()->getPost();           
            $company_name     = $this->_request->getPost('company_name');
            $client_subdomain = $this->_request->getPost('client_subdomain');
            $client_email     = $this->_request->getPost('client_email');
            $client_type      = $this->_request->getPost('client_type');
            $cp_username      = $this->_request->getPost('cp_username');
            $cp_password      = $this->_request->getPost('cp_password');

            if($company_name=='')
            {
                $data['status'] = 'error';
                $data['message'] = 'Please enter company name';
            }
            else if($client_subdomain=='')
            {
                $data['status'] = 'error';
                $data['message'] = 'Please enter client subdomain';
            }
            else if($client_email=='')
            {
                $data['status'] = 'error';
                $data['message'] = 'Please enter client email';
            }
            else
            {

               if($cp_username=='superadmin' && $cp_password=='man1234'){

                    $testArray=$this->myclientdetails->passSQLquery("SELECT clientDomain FROM tblClient");            
                    $arr_domain=array();
                    foreach($testArray as $res)
                    {               
                        $arr_domain[]=$this->extract_subdomains(preg_replace('#^https?://#', '', $res['clientDomain']));
                    }
                    $domain = $client_subdomain;                
                    if(!in_array($domain,$arr_domain)) 
                    {                  
                        $slice=explode('.',$domain);
                        $client_subdomain  =$domain;
                        $clientadmindomain =$domain.'/admin';
                        $adminURL          ='https://'.$domain.'/';
                        $frontpublicpath   ='/home/db-csp.com/clientdev/production/site1/public/';
                        
                        $dataArray = array('clientType'=>$client_type,'clientDomain'=>$client_subdomain,'clientadmindomain'=>$clientadmindomain,'clientName'=>$company_name,'clientEmail'=>$client_email,'clientStatus'=>0,'createdDate'=>date('Y-m-d H:i:s'));
                            
                        $ClientId = $this->infobj->insertdata_global('tblClient',$dataArray);
                        $clientpath="/home/db-csp.com/clientdev/cimg/path_".$ClientId;

                        if (!file_exists($clientpath)) {
                                mkdir($clientpath, 0777, true);
                                mkdir($clientpath."/grouppics", 0777, true);
                                mkdir($clientpath."/grouppics/medium", 0777, true);
                               
                                mkdir($clientpath."/imageposts", 0777, true);
                                mkdir($clientpath."/imageposts/small", 0777, true);
                                mkdir($clientpath."/imageposts/medium", 0777, true);

                                mkdir($clientpath."/users", 0777, true);                               
                                mkdir($clientpath."/users/small", 0777, true);
                                mkdir($clientpath."/users/medium", 0777, true);
                        } 
                        
                        
                        $dataArrayrole1 = array('clientID'=>$ClientId,'role'=>'admin','role_id'=>1,'id_parent'=>0);
                        $roleentry1 = $this->infobj->insertdata_global('roles',$dataArrayrole1); 

                        $dataArrayrole2 = array('clientID'=>$ClientId,'role'=>'visitor','role_id'=>2,'id_parent'=>1);
                        $roleentry1 = $this->infobj->insertdata_global('roles',$dataArrayrole2);  

                        $dataArray2 = array('clientID'=>$ClientId,'adminURL'=>$adminURL,'siteName'=>$company_name,'companyName'=>$company_name,'postName'=>'post','fromMail'=>'admin@onserro.com','fromName'=>$company_name,'noreplyEmail'=>'noreply@onserro.com','companyFootertext'=>'The db-csp team','deactiveAlt'=>'profile deactive','pageNum'=>10,'pageNumLeague'=>10,'baseUrlImages'=>$adminURL,'frontpublicpath'=>$frontpublicpath,'frontUrl'=>$adminURL,'emailUrl'=>$adminURL,'createdDate'=>date('Y-m-d H:i:s'));

                        $data3 = array('clientID'=>$ClientId);

                        $domainvarInsertion = $this->infobj->insertdata_global('domainVariables',$dataArray2);

                        $adminemailtemplatesInsertion = $this->infobj->insertdata_global('adminemailtemplates',$data3);
                        
                        $dbeeCats = array('clientID'=>$ClientId,'CatName'=>'Miscellaneous','Priority'=>1);

                         $configuration = array('clientID'=>$ClientId,'allow_admin_post_live'=>0,
                                'content'=>'{"SigninText":"","SigninColor":"","backgroundColor":"#a6a2a6","SiteLogo":"logo.png","SigninImage":"dblogo.png","loginbackgroundimage":""}',
                                'seoContent'=>'{"SiteTitle":"db csp","SiteDescription":"db csp"}',
                                'LeagueNames'=>'{"score1":"Loved","score2":"Rogues","score3":"Philosophers"}', 'ScoreNames'=>'{"1":{"ScoreName1":"Love","ScoreIcon1":"132"},"2":{"ScoreName2":"Like","ScoreIcon2":"263"},"3":{"ScoreName3":"Dislike","ScoreIcon3":"262"},"4":{"ScoreName4":"Sad","ScoreIcon4":"119"}}','expert'=>'expert','SocialLogo'=>'dblogo.png');

                         






                        /*
                        $biofiledsql="INSERT INTO `tbl_biofields` (`clientID`, `name`, `field_code`, `published`, `required`, `priority`) VALUES
                                (".$ClientId.", 'About me', 'about_me', 1, 0, 1),
                                (".$ClientId.", 'Occupation', 'occupation', 1, 0, 2),
                                (".$ClientId.", 'Political views', 'political_views', 1, 0, 3),
                                (".$ClientId.", 'Religious views', 'religious_views', 1, 0, 4),
                                (".$ClientId.", 'Likes & dislikes', 'likes_&_dislikes', 1, 0, 6),
                                (".$ClientId.", 'Hobbies & interests', 'hobbies_&_interests', 1, 0, 5)"; 
                        */

                        $biofiledsql="INSERT INTO `tbl_biofields` (`clientID`, `name`, `field_code`, `published`, `required`, `priority`) VALUES
                                (".$ClientId.", 'About me', 'about_me', 1, 0, 1)"; 


  $emailtemplatesql="INSERT INTO `emailtemplates` (`clientID`,`case`, `keyword`, `title`, `subject`, `body`, `footertext`, `areatype`, `active`) VALUES
(".$ClientId.",1, 'New account activation', 'New account activation', 'Activate your [%%COMPANY_NAME%%] account now!', '&lt;table&gt;&lt;tbody&gt;&lt;tr&gt;&lt;td&gt;
Dear &lt;span contenteditable=&quot;false&quot;&gt;[%%fname%%]&lt;/span&gt;,&lt;br&gt;&lt;br&gt;A new account has been created for you at &lt;a href=&quot;[%%FRONT_URL%%]&quot; target=&quot;_blank&quot;&gt;&lt;span contenteditable=&quot;false&quot;&gt;[%%FRONT_URL%%]&lt;/span&gt;&lt;/a&gt;.&lt;br&gt;&lt;br&gt;Your log in email address is: &amp;nbsp;&lt;span contenteditable=&quot;false&quot;&gt;[%%loginid%%]&lt;/span&gt;&lt;br&gt;&amp;nbsp;&lt;br&gt;&lt;span&gt;&lt;span&gt;You will then be prompted to set up a password for your account.
&lt;/span&gt;&lt;/span&gt;&lt;br&gt;&lt;span&gt;&lt;span&gt;Please click on the link below to activate your account now... &lt;/span&gt;&lt;/span&gt;&lt;br&gt;  &lt;a href=&quot;[%%FRONT_URL%%]/index/activelink/link/activate/id/[%%Signuptoken%%]&quot; target=&quot;_blank&quot; style=&quot;color:#ff7709; font-size:24px; display:block; word-wrap:break-word;&quot;&gt; &lt;span contenteditable=&quot;false&quot;&gt;[%%FRONT_URL%%]&lt;/span&gt;/index/activelink/link/activate/id/&lt;span contenteditable=&quot;false&quot;&gt;[%%Signuptoken%%]&lt;/span&gt;&lt;/a&gt;&lt;br&gt;&lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_FOOTERTEXT%%]&lt;/span&gt;
&lt;/td&gt;&lt;/tr&gt;&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'admin', 1),
(".$ClientId.",2, 'Account has been activated / deleted', 'Account has been activated / deleted', 'Your account has been [%%activity%%]', '&lt;table&gt;
    &lt;tbody&gt;&lt;tr&gt;&lt;td&gt;
            Dear &lt;span contenteditable=&quot;false&quot;&gt;[%%fname%%]&lt;/span&gt;,&lt;br&gt;&lt;br&gt;
            &lt;span contenteditable=&quot;false&quot;&gt;[%%body_deleteactive%%]&lt;/span&gt;
    &lt;br&gt;&lt;br&gt;&lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_FOOTERTEXT%%]&lt;/span&gt;
    &lt;/td&gt;&lt;/tr&gt;
    &lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'admin', 1),
(".$ClientId.",3, 'You have been re-invited', 'You have been re-invited', 'You have been re-invited', '&lt;table&gt;
&lt;tbody&gt;&lt;tr&gt;
    &lt;td&gt;
    Dear &lt;span contenteditable=&quot;false&quot;&gt;[%%uName%%]&lt;/span&gt;,&lt;br&gt;&lt;br&gt;
    You have been re-invited to the&amp;nbsp;&lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_NAME%%]&lt;/span&gt;&amp;nbsp;platform. &amp;nbsp;&lt;br&gt;&lt;br&gt;Click on the link below to join in.&lt;br&gt;&lt;br&gt;
    &lt;a href=&quot;[%$FRONT_URL$%]&quot;&gt;&lt;span contenteditable=&quot;false&quot;&gt;[%%FRONT_URL%%]&lt;/span&gt;&lt;/a&gt;
&lt;br&gt;&lt;br&gt;&lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_FOOTERTEXT%%]&lt;/span&gt;
    &lt;/td&gt;
&lt;/tr&gt;
&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'admin', 1),
(".$ClientId.",4, 'Platform Administrator Messages', 'Platform Administrator Messages', '[%%subject%%].', '&lt;table&gt;
    &lt;tbody&gt;&lt;tr&gt;&lt;td&gt;
    Dear [%%FIRST_NAME%%], &lt;br&gt;&lt;br&gt;[%%BODY_MESSAGE%%]              
    &lt;br&gt;
    &lt;br&gt;[%%COMPANY_FOOTERTEXT%%]
    &lt;/td&gt;&lt;/tr&gt;
&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'admin', 1),
(".$ClientId.",5, 'Invitation to attend video event', 'Invitation to attend video event', 'Invitation to attend video event', '&lt;table&gt;
&lt;tbody&gt;&lt;tr&gt;&lt;td&gt;Dear&amp;nbsp;&lt;span contenteditable=&quot;false&quot;&gt;[%%dataName%%]&lt;/span&gt;,&lt;br&gt;&lt;br&gt; &lt;span&gt;&lt;span&gt;You have been invited to attend a video event on&lt;/span&gt;&lt;/span&gt;&lt;br&gt;&lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_NAME%%]&lt;/span&gt;.&lt;br&gt;&lt;br&gt;&lt;span contenteditable=&quot;false&quot;&gt;[%%db_url%%]&lt;/span&gt; to respond.&lt;br&gt;&lt;br&gt; 
&lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_FOOTERTEXT%%]&lt;/span&gt;
&lt;/td&gt;&lt;/tr&gt;
&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'admin', 1),
(".$ClientId.",6, 'Video event ''join request'' status', 'Video event ''join request'' status', 'Your request to join a video event has been accepted', '&lt;table&gt;
    &lt;tbody&gt;&lt;tr&gt;&lt;td&gt;Dear&amp;nbsp;&lt;span contenteditable=&quot;false&quot;&gt;[%%dataName%%]&lt;/span&gt;,&lt;br&gt; &lt;br&gt;
            Your request to join the video event [%%EVENTNAME%%]&amp;nbsp;has been accepted.&lt;br&gt;&lt;br&gt;Click on the link above to go to the event.&lt;br&gt;&lt;br&gt;
            &lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_FOOTERTEXT%%]&lt;/span&gt;
    &lt;/td&gt;&lt;/tr&gt;
&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'admin', 1),
(".$ClientId.",7, 'New account activation', 'New account activation', 'Activate your [%%COMPANY_NAME%%] account now!', '&lt;table&gt;&lt;tbody&gt;&lt;tr&gt;
        &lt;td&gt;
Dear &lt;span contenteditable=&quot;false&quot;&gt;[%%request_Name%%]&lt;/span&gt;,&lt;br&gt;&lt;br&gt;
Thank you for registering at&amp;nbsp;&lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_NAME%%]&lt;/span&gt; &lt;a href=&quot;[%%site_url%%]&quot; target=&quot;_blank&quot;&gt;&lt;span contenteditable=&quot;false&quot;&gt;[%%site_url%%]&lt;/span&gt;&lt;/a&gt;.&lt;br&gt;&lt;br&gt;
Please click on the link below to confirm your e-mail address and activate your &lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_NAME%%]&lt;/span&gt; account.&lt;br&gt; &lt;br&gt;
&lt;a href=&quot;[%%site_url%%]/index/activelink/link/activate/id/[%%request_Signuptoken%%]&quot; target=&quot;_blank&quot; style=&quot;color:#ff7709; font-size:24px; display:block;  word-wrap:break-word;&quot;&gt;&lt;span contenteditable=&quot;false&quot;&gt;[%%site_url%%]&lt;/span&gt;/index/activelink/
 link/activate/id/&lt;span contenteditable=&quot;false&quot;&gt;[%%request_Signuptoken%%]&lt;/span&gt;&lt;/a&gt;&lt;br&gt;&lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_FOOTERTEXT%%]&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'front', 1),
(".$ClientId.",8, 'Welcome email', 'Welcome email', 'Welcome to [%%COMPANY_NAME%%]', '&lt;table&gt;
&lt;tbody&gt;&lt;tr&gt;&lt;td&gt;
    Dear &lt;span contenteditable=&quot;false&quot;&gt;[%%chkUser_Name%%]&lt;/span&gt;,&lt;br&gt; &lt;br&gt;

    Your &lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_NAME%%]&lt;/span&gt; account is now active under the registered email address:&amp;nbsp;&amp;nbsp;&lt;span contenteditable=&quot;false&quot;&gt;[%%chkUser_Email%%]&lt;/span&gt;&lt;br&gt;&lt;br&gt;
    We look forward to seeing you on &lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_NAME%%]&lt;/span&gt;&lt;br&gt;&lt;br&gt; 
    &lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_FOOTERTEXT%%]&lt;/span&gt;
&lt;/td&gt;&lt;/tr&gt;
&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'front', 1),
(".$ClientId.",9, 'Password reset link', 'Password reset link', 'Reset your password', '&lt;table&gt;
&lt;tbody&gt;&lt;tr&gt;
&lt;td&gt;
Dear &lt;span contenteditable=&quot;false&quot;&gt;[%%Res_Name%%]&lt;/span&gt;,&lt;br&gt;&lt;br&gt;
You have requested to reset your &lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_NAME%%]&lt;/span&gt; password. Please click on the link below and enter 
the following password code.
&lt;br&gt;&lt;br&gt;&lt;a href=&quot;[%%BASE_URL%%]/index/recover/id/[%%Res_UserID%%]&quot;&gt;&lt;span contenteditable=&quot;false&quot;&gt;[%%BASE_URL%%]&lt;/span&gt;/index/recover/id/&lt;span contenteditable=&quot;false&quot;&gt;[%%Res_UserID%%]&lt;/span&gt;&lt;/a&gt;
&lt;br&gt;&lt;br&gt;Password reset code:&amp;nbsp;&amp;nbsp;&lt;span contenteditable=&quot;false&quot;&gt;[%%rand%%]&lt;/span&gt;
&lt;br&gt;&lt;br&gt;Please note that this is a one time code and will expire after you have successfully reset your password.
&lt;br&gt;&lt;br&gt;&lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_FOOTERTEXT%%]&lt;/span&gt;
&lt;/td&gt;
&lt;/tr&gt;
&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'front', 0),
(".$ClientId.",11, 'Db Error message', 'Db Error message', 'Db Error -[%%message%%]', '&lt;table&gt;
&lt;tbody&gt;&lt;tr&gt;
&lt;td&gt;
New feedback received below.&lt;br&gt; &lt;br&gt;
&lt;font&gt;
[%%feedbacktext%%]&lt;/font&gt; 
&lt;br&gt;[%%COMPANY_FOOTERTEXT%%]
&lt;/td&gt;
&lt;/tr&gt;
&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'front', 0),
(".$ClientId.",12, 'New comment', 'New comment', 'New comment', '&lt;table&gt;
&lt;tbody&gt;&lt;tr&gt;
&lt;td&gt;
&lt;div style=&quot;padding-bottom:5px; border-bottom:1px solid #CCC;&quot;&gt;&lt;/div&gt;&lt;div style=&quot;clear:both&quot;&gt;&lt;/div&gt;
&lt;div style=&quot;margin-top:10px; margin-bottom:10px;&quot;&gt;&lt;div style=&quot;float:left; width:80px;&quot;&gt;
[%%UserPic%%]&lt;/div&gt;
&lt;div style=&quot;float:left; &quot;&gt;&lt;font face=&quot;Arial, Helvetica, sans-serif&quot; &gt;[%%UserRow_Name%%] has  commented on
 [%%POST_NAME%%] &lt;a href=&quot;[%%BASE_URL%%]/dbee/[%%db%%]&quot;&gt;
&lt;i&gt;&lt;font color=&quot;#999999&quot;&gt;[%%dbeeText%%]&lt;/font&gt;&lt;/i&gt;&lt;/a&gt;&lt;br&gt;&lt;br&gt;[%%UserRow_Name%%] wrote:&lt;br&gt;&lt;font color=&quot;#666666&quot;&gt;
[%%commTextval%%]&lt;/font&gt;&lt;br&gt;&lt;/font&gt;&lt;/div&gt;&lt;/div&gt;&lt;font face=&quot;Arial, Helvetica, sans-serif&quot; &gt;&lt;div style=&quot;clear:both&quot;&gt;&lt;/div&gt; 
&lt;br&gt;[%%COMPANY_FOOTERTEXT%%]
&lt;/font&gt;&lt;/td&gt;
&lt;/tr&gt;
&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'front', 0),
(".$ClientId.",13, 'Expert status', 'Expert status', 'Expert status', '&lt;table&gt;
&lt;tbody&gt;&lt;tr&gt;
&lt;td&gt;
Dear &lt;span contenteditable=&quot;false&quot;&gt;[%%data_Name%%]&lt;/span&gt;,&lt;br&gt; &lt;br&gt;
&lt;span&gt;Your Expert status has now been turned off on&lt;/span&gt; &lt;span contenteditable=&quot;false&quot;&gt;[%%POSTLINK%%]&lt;/span&gt;.&lt;br&gt;&lt;br&gt; 
&lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_FOOTERTEXT%%]&lt;/span&gt;
&lt;/td&gt;
&lt;/tr&gt;
&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'front', 1),
(".$ClientId.",14, 'Abuse report received', 'Abuse report received', 'Abuse report received', '&lt;table&gt;
&lt;tbody&gt;&lt;tr&gt;
&lt;td&gt;
&lt;font face=&quot;Arial, Helvetica, sans-serif&quot; &gt;Following [%%label%%] has been reported as abuse by
&lt;a href=&quot;[%%BASE_URL%%]/profile/index/id/[%%$cookieuser%%]&quot;&gt;[%%ReportingUser%%]&lt;/a&gt;.&lt;/font&gt;&lt;br&gt;&lt;br&gt;
&lt;b&gt;[%%Text%%]&lt;/b&gt;&lt;br&gt;&lt;br&gt;
&lt;a href=&quot;[%%BASE_URL%%]/dbeedetail/home/id/[%%dbee%%]&quot;&gt;see [%%POST_NAME%%]&lt;/a&gt;
&lt;br&gt;&lt;br&gt;[%%COMPANY_FOOTERTEXT%%]
&lt;/td&gt;
&lt;/tr&gt;
&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'front', 0),
(".$ClientId.",15, 'Bug report received', 'Bug report received', 'Bug report received', '&lt;table&gt;
&lt;tbody&gt;&lt;tr&gt;
&lt;td&gt;
&lt;p&gt;&lt;font&gt;Bug report submitted by 
&lt;a href=&quot;[%%BASE_URL%%]/profile.php?user/[%%Row_UserID%%]&quot;&gt;[%%Row_Name%%]&lt;/a&gt;&lt;br&gt;&lt;br&gt;
&lt;b&gt;Bug noticed:&lt;/b&gt; &lt;font color=&quot;#666&quot;&gt;[%%bug%%]&lt;/font&gt;&lt;br&gt;&lt;b&gt;Browser:&lt;/b&gt; &lt;font color=&quot;#666&quot;&gt;[%%userbrowser%%]&lt;/font&gt;&lt;/font&gt;&lt;/p&gt;&lt;font&gt;
[%%COMPANY_FOOTERTEXT%%]
&lt;/font&gt;&lt;/td&gt;
&lt;/tr&gt;
&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'front', 0),
(".$ClientId.",16, 'Invited to a league', 'Invited to a league', 'You have been invited to a league on [%%POST_NAME%%]', '&lt;table&gt;
&lt;tbody&gt;&lt;tr&gt;
&lt;td&gt;
&lt;p&gt;&lt;font&gt;
Dear [%%row_Name%%],&lt;br&gt;&lt;br&gt;You have been invited to a league by 
&lt;a href=&quot;[%%BASE_URL%%]/user/[%%userleagueacce%%]&quot; target=&quot;_blank&quot;&gt;
[%%userleaguesend%%]&lt;/a&gt;&lt;br&gt;&lt;br&gt;&lt;/font&gt;
&lt;a href=&quot;[%%BASE_URL%%]/league/index/id/[%%leagueid%%]&quot; target=&quot;_blank&quot;&gt;Click here to see league details&lt;/a&gt;&lt;/p&gt;

[%%COMPANY_FOOTERTEXT%%]
&lt;/td&gt;
&lt;/tr&gt;
&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'front', 0),
(".$ClientId.",17, 'Email verified', 'Email verified', 'Your email has been verified', '&lt;table&gt;
&lt;tbody&gt;&lt;tr&gt;
&lt;td&gt;Dear&amp;nbsp;&lt;span contenteditable=&quot;false&quot;&gt;[%%chkUser_Name%%]&lt;/span&gt;,&lt;br&gt; &lt;br&gt;
Thank you for verifying your email address.&lt;br&gt; &lt;br&gt;
You will now receive updates from &lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_NAME%%]&lt;/span&gt;&lt;br&gt;&lt;br&gt; 
&lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_FOOTERTEXT%%]&lt;/span&gt;
&lt;/td&gt;
&lt;/tr&gt;
&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'front', 1),
(".$ClientId.",18, 'Group invitation', 'Group invitation', 'You have been invited to a Group', '&lt;table&gt;
&lt;tbody&gt;&lt;tr&gt;
&lt;td&gt;
&lt;p&gt;&lt;span &gt;Dear &lt;span contenteditable=&quot;false&quot;&gt;[%%USERNAME%%]&lt;/span&gt;,&lt;/span&gt;&lt;/p&gt;&lt;p&gt;&lt;span &gt;You have been invited to join&amp;nbsp;&lt;/span&gt;&lt;b &gt;&lt;span contenteditable=&quot;false&quot;&gt;[%%GroupName%%]&lt;/span&gt;&lt;/b&gt;&lt;font &gt;.&lt;/font&gt;&lt;/p&gt;&lt;p&gt;&lt;font  face=&quot;Arial, Helvetica, sans-serif&quot;&gt;Please respond to the invitation in your Notifications.&lt;/font&gt;&lt;/p&gt;&lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_FOOTERTEXT%%]&lt;/span&gt;
&lt;/td&gt;
&lt;/tr&gt;
&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'front', 1),
(".$ClientId.",19, 'Group join request', 'Group join request', 'Group join request', '&lt;table&gt;
&lt;tbody&gt;&lt;tr&gt;
&lt;td &gt;
&lt;font&gt;Dear &lt;span contenteditable=&quot;false&quot;&gt;[%%ownerName%%]&lt;/span&gt;,&lt;br&gt;&lt;br&gt;&lt;span contenteditable=&quot;false&quot;&gt;[%%userName%%]&lt;/span&gt; has requested to join&amp;nbsp;&lt;b&gt;
&lt;span contenteditable=&quot;false&quot;&gt;[%%GroupName%%]&lt;/span&gt;&lt;/b&gt;. &lt;br&gt;&lt;br&gt;Please go to your Notifications to&amp;nbsp;respond&amp;nbsp;to this.&lt;/font&gt;&lt;br&gt;
&lt;br&gt;&lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_FOOTERTEXT%%]&lt;/span&gt;
&lt;/td&gt;
&lt;/tr&gt;
&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'front', 1),
(".$ClientId.",20, 'Group Joining Request2', 'Group Joining Request2', 'Group Joining Request2', '&lt;table&gt;
&lt;tbody&gt;&lt;tr&gt;
&lt;td&gt;&lt;p style=&quot;padding-bottom:5px; border-bottom:1px solid #CCC;&quot;&gt;
&lt;/p&gt;&lt;p&gt;&lt;font&gt;Dear [%%ownerName%%],&lt;br&gt;
&lt;br&gt;[%%userName%%] has expressed interest in joining &lt;b&gt;[%%GroupName%%]&lt;/b&gt;
. Please login into your db account to respond under your groups notifications.&lt;/font&gt;&lt;br&gt;&lt;br&gt;[%%COMPANY_FOOTERTEXT%%]&lt;/p&gt;
&lt;/td&gt;
&lt;/tr&gt;
&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'front', 0),
(".$ClientId.",21, 'Activation email for social log in', 'Activation email for social log in', 'Welcome to [%%COMPANY_NAME%%]', '&lt;table&gt;
&lt;tbody&gt;&lt;tr&gt;
&lt;td&gt;
Dear &lt;span contenteditable=&quot;false&quot;&gt;[%%data_Name%%]&lt;/span&gt;,&lt;br&gt; &lt;br&gt;

Your &lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_NAME%%]&lt;/span&gt; account is now active and you can log in using your &lt;br&gt;&lt;span contenteditable=&quot;false&quot;&gt;[%%type%%]&lt;/span&gt; account details.&lt;br&gt; &lt;br&gt; 
&lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_FOOTERTEXT%%]&lt;/span&gt;
&lt;/td&gt;
&lt;/tr&gt;
&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'front', 1),
(".$ClientId.",22, 'Activation email for LinkedIn log in', 'Activation email for LinkedIn log in', 'Welcome to [%%COMPANY_NAME%%]', '&lt;table&gt;
&lt;tbody&gt;&lt;tr&gt;&lt;td &gt;
    Dear [%%chkUser_Name%%],&lt;br&gt;
    Your [%%COMPANY_NAME%%] account is now active and your login details are as follows:&lt;br&gt; &lt;br&gt;
    Account Details:&lt;br&gt;&lt;br&gt;
    Email:&amp;nbsp;[%%chkUser_Email%%]&lt;br&gt;
    Password: what you set on sign up&lt;br&gt;&lt;br&gt;
    We look forward to seeing you on [%%COMPANY_NAME%%]!&lt;br&gt;&lt;br&gt;
    Best wishes&lt;br&gt;&lt;br&gt; 
    [%%COMPANY_FOOTERTEXT%%]
&lt;/td&gt;&lt;/tr&gt;
&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'front', 0),
(".$ClientId.",23, 'New Follower', 'New follower', 'You have a new Follower', '&lt;table&gt;
&lt;tbody&gt;&lt;tr&gt;
&lt;td&gt;
&lt;div&gt;&lt;a target=&quot;_blank&quot; href=&quot;[%%BASE_URL%%]/user/id/[%%FollowerUserID%%]&quot;&gt;&lt;b&gt;&lt;span contenteditable=&quot;false&quot;&gt;[%%FollowerName%%]&lt;/span&gt;&lt;/b&gt;&lt;/a&gt;&amp;nbsp;is now following you on&amp;nbsp;&lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_NAME%%]&lt;/span&gt;.
&lt;br&gt;&lt;div style=&quot;&quot;&gt;&lt;font&gt;&lt;br&gt;&lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_FOOTERTEXT%%]&lt;/span&gt;&lt;/font&gt;&lt;/div&gt;&lt;/div&gt;&lt;font&gt;&lt;div style=&quot;clear:both&quot;&gt;&lt;/div&gt;                
&lt;/font&gt;&lt;/td&gt;
&lt;/tr&gt;
&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'front', 1),
(".$ClientId.",24, 'Twitter users email verification', 'Twitter users email verification', 'Please verify your email address', '&lt;table&gt;
&lt;tbody&gt;&lt;tr&gt;&lt;td&gt;
Dear &lt;span contenteditable=&quot;false&quot;&gt;[%%request_Name%%]&lt;/span&gt;,&lt;br&gt; &lt;br&gt;
Please click on the link below to verify your email address and activate your site notifications. &lt;br&gt; &lt;br&gt;
&lt;a href=&quot;[%%site_url%%]/email/activeemail/email/activate/id/[%%request_Emailtoken%%]&quot; target=&quot;_blank&quot; style=&quot;color:#ff7709; font-size:24px; display:block; width:550px; word-wrap:break-word;&quot;&gt;
 &lt;span contenteditable=&quot;false&quot;&gt;[%%site_url%%]&lt;/span&gt;/email/activeemail/link/activate/id/&lt;span contenteditable=&quot;false&quot;&gt;[%%request_Emailtoken%%]&lt;/span&gt;&lt;/a&gt; 
&lt;br&gt;&lt;br&gt;&lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_FOOTERTEXT%%]&lt;/span&gt;
&lt;/td&gt;
&lt;/tr&gt;
&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'front', 1),
(".$ClientId.",25, 'verify your score', 'verify your score', 'You have been scored', '&lt;table&gt;&lt;tbody&gt;&lt;tr&gt;&lt;td&gt;
&lt;div style=&quot;margin-top:10px; margin-bottom:10px; margin-left: -5px;&quot;&gt;&lt;div style=&quot;float:left; width:80px;&quot;&gt;
[%%UserPic%%]
&lt;/div&gt;
&lt;div style=&quot;float:left; width:470px;&quot;&gt;&lt;font face=&quot;Arial, Helvetica, sans-serif&quot; &gt;
&lt;a href=&quot;[%%BASE_URL%%]/user/[%%UserRow_Username%%]&quot;&gt;[%%UserRow_Name%%]&lt;/a&gt; scored your [%%label%%] - &lt;b&gt;[%%scorelabel%%]&lt;/b&gt;
&lt;br&gt;&lt;br&gt;&lt;a href=&quot;[%%BASE_URL%%]/dbeedetail/home/id/[%%MainDB%%]&quot;&gt;
&lt;i&gt;&lt;font color=&quot;#999999&quot;&gt;[%%Textval%%]&lt;/font&gt;&lt;/i&gt;&lt;/a&gt;&lt;br&gt;&lt;br&gt;[%%COMPANY_FOOTERTEXT%%]&lt;/font&gt;&lt;/div&gt;&lt;/div&gt;&lt;font&gt;
&lt;div style=&quot;clear:both&quot;&gt;&lt;/div&gt;
&lt;div&gt;&lt;font color=&quot;#999&quot; size=&quot;1&quot;&gt;This is an automated email from your [%%COMPANY_NAME%%] account.
 If you wish to turn off future notifications please edit your settings in your [%%COMPANY_NAME%%] profile.&lt;/font&gt;&lt;/div&gt;
&lt;/font&gt;&lt;/td&gt;
&lt;/tr&gt;
&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'front', 0),
(".$ClientId.",26, ' New comment', 'New comment', 'New comment', '&lt;table&gt;
&lt;tbody&gt;&lt;tr&gt;
&lt;td&gt;
&lt;div style=&quot;padding-bottom:5px; border-bottom:1px solid #CCC;&quot;&gt;&lt;/div&gt;&lt;div style=&quot;clear:both&quot;&gt;&lt;/div&gt;
&lt;div style=&quot;margin-top:10px; margin-bottom:10px;&quot;&gt;&lt;div style=&quot;float:left; width:80px;&quot;&gt;
[%%UserPic%%]&lt;/div&gt;
&lt;div style=&quot;float:left; &quot;&gt;&lt;font face=&quot;Arial, Helvetica, sans-serif&quot; &gt;[%%UserRow_Name%%] has  commented on
 [%%POST_NAME%%] &lt;a href=&quot;[%%BASE_URL%%]/profile/index/?db=[%%db%%]&quot;&gt;
&lt;i&gt;&lt;font color=&quot;#999999&quot;&gt;[%%dbeeText%%]&lt;/font&gt;&lt;/i&gt;&lt;/a&gt;&lt;br&gt;&lt;br&gt;[%%UserRow_Name%%] wrote:&lt;br&gt;&lt;font color=&quot;#666666&quot;&gt;
[%%commTextval%%]&lt;/font&gt;&lt;br&gt;&lt;/font&gt;&lt;/div&gt;&lt;/div&gt;&lt;font face=&quot;Arial, Helvetica, sans-serif&quot; &gt;&lt;div style=&quot;clear:both&quot;&gt;&lt;/div&gt; 
&lt;br&gt;[%%COMPANY_FOOTERTEXT%%]
&lt;/font&gt;&lt;/td&gt;
&lt;/tr&gt;
&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'front', 0),
(".$ClientId.",27, 'Group join request', 'Group join request', 'Group join request', '&lt;table&gt;
&lt;tbody&gt;&lt;tr&gt;
&lt;td&gt;&lt;p style=&quot;padding-bottom:5px; border-bottom:1px solid #CCC;&quot;&gt;&lt;/p&gt;&lt;p&gt;&lt;font&gt;Dear [%%ownerName%%],&lt;br&gt;
&lt;br&gt;[%%userName%%] has expressed interest in joining &lt;b&gt;[%%GroupName%%]&lt;/b&gt;. Please log in to your account to respond under your notifications.&lt;/font&gt;&lt;br&gt;&lt;br&gt;[%%COMPANY_FOOTERTEXT%%]&lt;/p&gt;
&lt;/td&gt;
&lt;/tr&gt;
&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'front', 0),
(".$ClientId.",28, 'Group join request2', 'Group join request2', 'Group join request2', '&lt;table&gt;&lt;tr&gt;&lt;td&gt;&lt;p&gt;&lt;br&gt;
&lt;font&gt;Dear [%%ownerName%%],&lt;br&gt;&lt;br&gt;[%%userName%%] has expressed interest in joining &lt;b&gt;
[%%GroupName%%]&lt;/b&gt;. Please login into your db account to respond under your group notifications.&lt;/font&gt;&lt;br&gt;
&lt;br&gt;[%%COMPANY_FOOTERTEXT%%]&lt;/p&gt;
&lt;/td&gt;
&lt;/tr&gt;
&lt;/table&gt;', 'powered by db corporate social platforms', 'front', 0),
(".$ClientId.",29, 'Expert invitation', 'Expert invitation', 'Invitation to become an Expert', '&lt;table&gt;
&lt;tbody&gt;&lt;tr&gt;
&lt;td&gt;
Dear &lt;span contenteditable=&quot;false&quot;&gt;[%%data_Name%%]&lt;/span&gt;,&lt;br&gt; &lt;br&gt;
&lt;span contenteditable=&quot;false&quot;&gt;[%%loginusername%%]&lt;/span&gt; has invited you to become an expert on&amp;nbsp;&lt;span contenteditable=&quot;false&quot;&gt;[%%url%%]&lt;/span&gt;&lt;br&gt;&lt;br&gt;Please go to your Notifications to respond to this invitation.&lt;br&gt;&lt;br&gt; 
&lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_FOOTERTEXT%%]&lt;/span&gt;
&lt;/td&gt;
&lt;/tr&gt;
&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'front', 1),
(".$ClientId.",30, 'New question', 'New question', 'New ''Ask The Expert'' question', '&lt;table&gt;
&lt;tbody&gt;&lt;tr&gt;
&lt;td style=&quot; &quot;&gt;
Dear &lt;span contenteditable=&quot;false&quot;&gt;[%%data_Name%%]&lt;/span&gt;,&lt;br&gt; &lt;br&gt;
&lt;span contenteditable=&quot;false&quot;&gt;[%%Name%%]&lt;/span&gt;&amp;nbsp;&lt;span style=&quot;white-space: pre-wrap; line-height: 1.38; font-size: 13px; font-family: Arial;&quot;&gt;has asked you a question on [%%POSTLINK%%].

Please click the link above to reply.&lt;br&gt;&lt;/span&gt;&lt;br&gt; 
&lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_FOOTERTEXT%%]&lt;/span&gt;
&lt;/td&gt;
&lt;/tr&gt;
&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'front', 1),
(".$ClientId.",31, 'Video event attendee request', 'Video event attendee request', 'Video event attendee request', '&lt;table&gt;
&lt;tbody&gt;&lt;tr&gt;
 &lt;td&gt;&lt;br&gt;
    &lt;span contenteditable=&quot;false&quot;&gt;[%%data_Name%%]&lt;/span&gt; is now on the attendee list for the following video event:&amp;nbsp;&amp;nbsp;&lt;span contenteditable=&quot;false&quot;&gt;[%%dbee_array_VidDesc%%]&lt;/span&gt;&lt;br&gt;&lt;br&gt; 
  &lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_FOOTERTEXT%%]&lt;/span&gt;
&lt;/td&gt;
&lt;/tr&gt;
&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'front', 1),
(".$ClientId.",32, 'Video event acceptance', 'Video event acceptance', 'Your video event request has been accepted', '&lt;table&gt;
&lt;tbody&gt;&lt;tr&gt;
&lt;td&gt;
Dear&amp;nbsp;&lt;span contenteditable=&quot;false&quot;&gt;[%%data_Name%%]&lt;/span&gt;,&lt;br&gt;&lt;br&gt;Your request to join the&amp;nbsp;&lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_NAME%%]&lt;/span&gt;&amp;nbsp;video event has been accepted.&lt;br&gt;&lt;br&gt;Click on the link above to go to the event.
&lt;br&gt;&lt;br&gt;
&lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_FOOTERTEXT%%]&lt;/span&gt;
&lt;/td&gt;
&lt;/tr&gt;
&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'front', 1),
(".$ClientId.",33, 'User request to join video event', 'User request to join video event', 'User request to join video event', '&lt;table&gt;
&lt;tbody&gt;&lt;tr&gt;
&lt;td&gt;
Dear admin,
&lt;br&gt;[%%name%%] has requested to join the video event  [%%dbee_array_VidDesc%%]&lt;br&gt;
&lt;a href=&quot;[%%BASE_URL%%]/dbee/[%%dbee_array_dburl%%]&quot;&gt;Click here to go to event&lt;/a&gt; 
&lt;br&gt;&lt;br&gt; Best regards&lt;br&gt;&lt;br&gt; 
  [%%COMPANY_FOOTERTEXT%%]
&lt;/td&gt;
&lt;/tr&gt;
&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'front', 0),
(".$ClientId.",35, 'User''s question answered', 'User''s question answered', 'Your question has been answered', '&lt;table&gt;
&lt;tbody&gt;&lt;tr&gt;
&lt;td&gt;
Dear &lt;span contenteditable=&quot;false&quot;&gt;[%%data_Name%%]&lt;/span&gt;,&lt;br&gt; &lt;br&gt;
&lt;span contenteditable=&quot;false&quot;&gt;[%%Name%%]&lt;/span&gt; has answered your question on &lt;span contenteditable=&quot;false&quot;&gt;[%%POSTLINK%%]&lt;/span&gt;.&lt;br&gt;&lt;br&gt;Please click the link above to view.&lt;br&gt;&lt;br&gt; 
&lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_FOOTERTEXT%%]&lt;/span&gt;
&lt;/td&gt;
&lt;/tr&gt;
&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'front', 1),
(".$ClientId.",36, 'VIP Group invitation', 'VIP Group invite', 'You have been invited to join a VIP Group', '&lt;table&gt;&lt;tbody&gt;&lt;tr&gt;&lt;td&gt;
Dear &lt;span contenteditable=&quot;false&quot;&gt;[%%dataName%%]&lt;/span&gt;,&lt;br&gt;&lt;br&gt;
You have been invited to join&amp;nbsp;&lt;span contenteditable=&quot;false&quot;&gt;[%%request_grp_name%%]&lt;/span&gt;.&lt;br&gt;&lt;br&gt;
Please respond to the invitation in your Notifications.&lt;br&gt;&lt;br&gt;
&lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_FOOTERTEXT%%]&lt;/span&gt;&lt;/td&gt;&lt;/tr&gt;
&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'admin', 1),
(".$ClientId.",38, 'New account / VIP Group', 'New account creation / VIP Group invite', 'Your [%%COMPANY_NAME%%] account needs activation.', '&lt;table&gt;&lt;tbody&gt;&lt;tr&gt;&lt;td&gt;
Dear [%%dataName%%],&lt;br&gt;&lt;br&gt;&lt;br&gt;
A new account has been created for you at &lt;a href=&quot;[%%FRONT_URL%%]&quot; target=&quot;_blank&quot;&gt;[%%FRONT_URL%%]&lt;/a&gt;.&lt;br&gt;&lt;br&gt;
Your log in email address is as below:
&lt;br&gt;&lt;br&gt;
log in email : [%%sendEmailvalue_uemail%%]&lt;br&gt;
password : whatever you set when logging in
&lt;br&gt;&lt;br&gt;You have been invited to a VIP group [%%request_grp_name%%] by the platform administrator. 
Upon activation of your newly created account you will be redirected to a page where you can accept/reject the request.&lt;br&gt;&lt;br&gt;
Please click on the link below to confirm your email address and activate your [%%COMPANY_NAME%%] account. &lt;br&gt; &lt;br&gt;
&lt;a href=&quot;[%%FRONT_URL%%]/index/activelink/link/activate/id/[%%sendEmailvalue_token%%]/notify/on&quot; target=&quot;_blank&quot; style=&quot;color:#ff7709; font-size:24px; display:block; width:550px; word-wrap:break-word;&quot;&gt;
[%%FRONT_URL%%]/index/activelink/link/activate/id/[%%sendEmailvalue_token%%]&lt;/a&gt;
&lt;br&gt;&lt;br&gt;  Please change your password under account settings immediately after log in.&lt;br&gt;&lt;br&gt;
[%%COMPANY_FOOTERTEXT%%]&lt;/td&gt;&lt;/tr&gt;
&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'admin', 1),
(".$ClientId.",37, 'Video event participation reminder', 'Video event participation reminder', 'Video event participation reminder', '&lt;table&gt;
&lt;tbody&gt;&lt;tr&gt;
 &lt;td&gt;
  Dear admin,&lt;br&gt; &lt;br&gt;
    &lt;span contenteditable=&quot;false&quot;&gt;[%%data_Name%%]&lt;/span&gt; has requested to become an attendee on the event &lt;span contenteditable=&quot;false&quot;&gt;[%%VidDesc%%]&lt;/span&gt;.&lt;br&gt;&lt;br&gt;
    &lt;a href=&quot;[%%BASE_URL%%]/dbee/[%%dburl%%]&quot;&gt;Click here&lt;/a&gt; to go to the event.
  &lt;br&gt;&lt;br&gt; Best regards&lt;br&gt; 
  &lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_FOOTERTEXT%%]&lt;/span&gt;
&lt;/td&gt;
&lt;/tr&gt;
&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'front', 1),
(".$ClientId.",39, 'New message from user', 'New message from user', 'New message from [%%SName%%]', '&lt;table&gt;
&lt;tbody&gt;&lt;tr&gt;
 &lt;td&gt;
  Dear &lt;span contenteditable=&quot;false&quot;&gt;[%%RName%%]&lt;/span&gt;,&lt;br&gt; &lt;br&gt;
    &lt;span contenteditable=&quot;false&quot;&gt;[%%SName%%]&lt;/span&gt; has sent you a new message:&amp;nbsp;&lt;br&gt;&lt;br&gt;&lt;span contenteditable=&quot;false&quot;&gt;&lt;i&gt;[%%Message%%]&lt;/i&gt;&lt;/span&gt;&lt;br&gt;&lt;br&gt;
    &lt;a href=&quot;[%%BASE_URL%%]/message&quot;&gt;Click here&lt;/a&gt;&amp;nbsp;to go to your Messages.&lt;br&gt;&lt;br&gt; 
  &lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_FOOTERTEXT%%]&lt;/span&gt;
&lt;/td&gt;
&lt;/tr&gt;
&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'front', 1),
(".$ClientId.",40, 'Abuse report', 'Abuse report', 'You have submitted a report', '&lt;table&gt;&lt;tbody&gt;&lt;tr&gt;&lt;td&gt;&lt;font &gt;You reported the following as abuse:&lt;/font&gt;&lt;br&gt;&lt;br&gt;
&lt;b&gt;&lt;span contenteditable=&quot;false&quot;&gt;[%%Text%%]&lt;/span&gt;&lt;/b&gt;&lt;br&gt;&lt;br&gt;
&lt;a href=&quot;https://clientdevadmin.db-csp.com/home/emailtempsetting/[%%BASE_URL%%]/dbeedetail/home/id/[%%dbee%%]&quot;&gt;View &lt;span contenteditable=&quot;false&quot;&gt;[%%POST_NAME%%]&lt;/span&gt;&lt;/a&gt;
&lt;br&gt;&lt;br&gt;&lt;font&gt;The platform administrator has been notified.&lt;/font&gt;&lt;br&gt;&lt;br&gt;&lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_FOOTERTEXT%%]&lt;/span&gt;
&lt;/td&gt;&lt;/tr&gt;&lt;tr&gt;&lt;/tr&gt;&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'front', 1),
(".$ClientId.",41, 'Bug report', 'Bug report', 'You have reported a bug', '&lt;table&gt;&lt;tbody&gt;&lt;tr&gt;&lt;td&gt;&lt;p&gt;&lt;font&gt;Hi &lt;span contenteditable=&quot;false&quot;&gt;[%%USERNAME%%]&lt;/span&gt;,&lt;/font&gt;&lt;/p&gt;&lt;p&gt;&lt;font&gt;You submitted the following bug report:&lt;/font&gt;&lt;/p&gt;&lt;p&gt;&lt;font&gt;
&lt;b&gt;Bug:&lt;/b&gt; &lt;font color=&quot;#666&quot;&gt;&lt;span contenteditable=&quot;false&quot;&gt;[%%bug%%]&lt;/span&gt;&lt;/font&gt;&lt;br&gt;&lt;b&gt;Browser:&lt;/b&gt; &lt;font color=&quot;#666&quot;&gt;&lt;span contenteditable=&quot;false&quot;&gt;[%%userbrowser%%]&lt;/span&gt;&lt;/font&gt;&lt;br&gt;&lt;br&gt;The platform administrator has been notified.&lt;/font&gt;&lt;/p&gt;&lt;font&gt;
&lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_FOOTERTEXT%%]&lt;/span&gt;&lt;/font&gt;&lt;/td&gt;&lt;/tr&gt;&lt;/tbody&gt;&lt;/table&gt;&lt;br&gt;', 'powered by db corporate social platforms', 'front', 1),
(".$ClientId.",42, 'Activation email for Google+ log in', 'Activation email for Google+ log in', 'Welcome to [%%COMPANY_NAME%%]', '&lt;table&gt;&lt;tbody&gt;&lt;tr&gt;
&lt;td&gt;
&lt;strong&gt;Dear [%%data_Name%%]&lt;/strong&gt;,&lt;br&gt; &lt;br&gt;
Your [%%COMPANY_NAME%%] account is now active and you can log in using your [%%type%%] account details.&lt;br&gt; &lt;br&gt;
We look forward to seeing you on [%%COMPANY_NAME%%]!&lt;br&gt;&lt;br&gt;
Best wishes&lt;br&gt;&lt;br&gt;
[%%COMPANY_FOOTERTEXT%%]
&lt;/td&gt;
&lt;/tr&gt;&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'front', 0),
(".$ClientId.",45, 'New sub-admin account details', 'This email is sent to newly created platform sub-admin accounts.Please note this email has no HTML content.', 'Your [%%COMPANY_NAME%%] sub-admin account details', '&lt;table&gt;&lt;tbody&gt;&lt;tr&gt;&lt;td&gt;
Dear &lt;span contenteditable=&quot;false&quot;&gt;[%%fname%%]&lt;/span&gt;, &lt;br&gt;&lt;br&gt;You can now access the sub-administration panel at&amp;nbsp;&lt;a href=&quot;[%%FRONT_URL%%]/admin&quot; target=&quot;_blank&quot;&gt;&lt;span contenteditable=&quot;false&quot;&gt;[%%FRONT_URL%%]/admin&lt;/span&gt;&lt;/a&gt; using the following username:&amp;nbsp;&amp;nbsp;&lt;span contenteditable=&quot;false&quot;&gt;[%%username%%]&lt;/span&gt;&lt;br&gt;&lt;br&gt;Please use your existing platform password.&lt;br&gt;&lt;br&gt;             
&lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_FOOTERTEXT%%]&lt;/span&gt;                 
&lt;/td&gt;&lt;/tr&gt;&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'admin', 1),
(".$ClientId.",46, 'Sub-admin account access removal', 'This email is sent to deleted sub-admin accounts.', 'Your [%%COMPANY_NAME%%] sub-admin account access has been removed', '&lt;table&gt;&lt;tbody&gt;&lt;tr&gt;&lt;td style=&quot; &quot;&gt;                    
Dear &lt;span contenteditable=&quot;false&quot;&gt;[%%fname%%]&lt;/span&gt;, &lt;br&gt;&lt;br&gt;Your sub-admin account access has been removed.&lt;br&gt;&lt;br&gt;                    
&lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_FOOTERTEXT%%]&lt;/span&gt;                 
&lt;/td&gt;&lt;/tr&gt;&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'admin', 1),
(".$ClientId.",47, 'Your status changed to VIP','Your status changed to VIP','Your status changed to VIP','&lt;table&gt;
&lt;tbody&gt;&lt;tr&gt;
    &lt;td&gt;
    Dear &lt;span contenteditable=&quot;false&quot;&gt;[%%uName%%]&lt;/span&gt;,&lt;br&gt;&lt;br&gt;
    Your account has been given VIP status.&lt;br&gt;&lt;br&gt;
    &lt;a href=&quot;[%%Profile_URL%%]&quot;&gt;Click here to go to your profile&lt;/a&gt;
&lt;br&gt;&lt;br&gt;&lt;span contenteditable=&quot;false&quot;&gt;[%%COMPANY_FOOTERTEXT%%]&lt;/span&gt;
    &lt;/td&gt;
&lt;/tr&gt;
&lt;/tbody&gt;&lt;/table&gt;', 'powered by db corporate social platforms', 'admin', 1);";

$updateemailtemp="UPDATE `emailtemplates` SET defaultbody=body where clientID=".$ClientId."";

/*
$rsssql="INSERT INTO `tblRssSites` (`clientID`, `Name`, `URL`, `Logo`, `Parent`, `Country`, `DisplayOrder`, `Active`, `isdefault`) VALUES
(".$ClientId.", 'BBC', 'http://feeds.bbci.co.uk/news/rss.xml', 'bbc.png', 0, 1, 1, '0', '0'),
(".$ClientId.", 'The Economist', 'http://www.economist.com/feeds/print-sections/75/europe.xml', 'economist.png', 0, 1, 1, '1', '0'),
(".$ClientId.", 'Daily Telegraph', 'http://www.telegraph.co.uk/news/uknews/rss', 'telegraph.png', 0, 1, 1, '1', '0'),
(".$ClientId.", 'The Times', 'http://www.thetimes.co.uk/tto/news/uk/rss', 'thetimes.png', 0, 1, 1, '1', '0'),
(".$ClientId.", 'The Guardian', 'http://feeds.guardian.co.uk/theguardian/uk/rss', 'guardian.png', 0, 1, 1, '1', '0'),
(".$ClientId.", 'Reuters Europe', 'http://mf.feeds.reuters.com/reuters/UKTopNews', 'reuters.png', 0, 2, 1, '1', '1'),
(".$ClientId.", 'Associated Press', 'http://hosted2.ap.org/atom/APDEFAULT/3d281c11a96b4ad082fe88aa0db04305', 'associated-press.png', 0, 2, 1, '1', '1'),
(".$ClientId.", 'CNN', 'http://rss.cnn.com/rss/edition.rss', 'cnn.png', 0, 3, 1, '1', '1'),
(".$ClientId.", 'Washington Post', 'http://feeds.washingtonpost.com/rss/world', 'washington.png', 0, 3, 1, '1', '0'),
(".$ClientId.", 'New York Times', 'http://www.nytimes.com/services/xml/rss/nyt/HomePage.xml', 'newyorktimes.png', 0, 3, 1, '1', '1'),
(".$ClientId.", 'Chicago', 'http://feeds.chicagotribune.com/chicagotribune/news/', 'chicago.png', 0, 3, 1, '1', '0'),
(".$ClientId.", 'Boston Times', 'http://syndication.boston.com/topstories.xml', 'boston.png', 0, 3, 1, '1', '0'),
(".$ClientId.", 'Variety', 'http://feeds.feedburner.com/variety/headlines', 'variety.png', 0, 3, 1, '1', '0'),
(".$ClientId.", 'Al Jazeera', 'http://www.aljazeera.com/Services/Rss/?PostingId=2007731105943979989', 'aljazeera.png', 0, 4, 1, '1', '0'),
(".$ClientId.", 'Pravda', 'http://english.pravda.ru/world/export.xml', 'pravda.png', 0, 5, 1, '1', '0'),
(".$ClientId.", 'Time Of India', 'http://timesofindia.indiatimes.com/rssfeeds/1221656.cms', 'timesofindia.png', 0, 6, 1, '1', '0'),
(".$ClientId.", 'Indian Express', 'http://syndication.indianexpress.com/rss/latest-news.xml', 'indiaexpress.png', 0, 6, 1, '1', '0'),
(".$ClientId.", 'Herald Sun', 'http://feeds.news.com.au/heraldsun/rss/heraldsun_news_topstories_2803.xml', 'heraldsun.png', 0, 7, 1, '1', '0'),
(".$ClientId.", 'China Daily', 'http://www.chinadaily.com.cn/rss/china_rss.xml', 'chinadaily.png', 0, 8, 1, '1', '0'),
(".$ClientId.", 'Shanghai Daily', 'http://www.shanghaidaily.com/rss/latest/', 'shanghaidaily.png', 0, 8, 1, '1', '0'),
(".$ClientId.", 'Latin American Herald', 'http://www.laht.com/rss-feed.xml', 'latinamericanherald.png', 0, 9, 1, '1', '0'),
(".$ClientId.", 'The Brazil Times', 'http://www.thebraziltimes.com/feed/rss/all/week.rss', 'braziltimes.png', 0, 9, 1, '1', '0'),
(".$ClientId.", 'Private Eye', 'http://www.private-eye.co.uk/rss/rss.php', 'privateeye.png', 0, 10, 1, '0', '0'),
(".$ClientId.", 'Huffington Post', 'http://feeds.huffingtonpost.com/huffingtonpost/raw_feed', 'huffingtonpost.png', 0, 10, 1, '1', '0'),
(".$ClientId.", 'The Jakartapost', 'http://www.thejakartapost.com/breaking/feed', 'thejakartapost.png', 0, 7, 1, '1', '0')";

$profinyfiltersql="INSERT INTO `tblProfanityFilter` (`clientID`, `name`, `status`) VALUES
(".$ClientId.", 'fuck', 1),
(".$ClientId.", 'piss', 1),
(".$ClientId.", 'ass', 1),
(".$ClientId.", 'sex', 1)";

$GroupTypessql="INSERT INTO `tblGroupTypes` (`clientID`, `TypeName`, `Priority`) VALUES
(".$ClientId.", 'Politics', 50),
(".$ClientId.", 'Sports', 5),
(".$ClientId.", 'Films', 10);";
 */
 
 mkdir("adminraw/knowledge_center/client_".$ClientId, 0700);
 $datausertype = array('clientID'=>$ClientId,'TypeName'=>'VIP','Typeref'=>'vip');
 $dataloginsocialresource = array('clientID'=>$ClientId,'Facebookstatus'=>1,'Linkedinstatus'=>1);

                        $this->infobj->autofill($ClientId);
                        $this->myclientdetails->passSQLqueryExecut($biofiledsql);
                       
                        $this->myclientdetails->passSQLqueryExecut($emailtemplatesql);

                        $this->myclientdetails->passSQLqueryExecut($updateemailtemp);
                       
                      

                        //$this->myclientdetails->passSQLqueryExecut($rsssql);
                        //$this->myclientdetails->passSQLqueryExecut($profinyfiltersql);                        
                       //$this->myclientdetails->passSQLqueryExecut($GroupTypessql);
                        $this->infobj->insertdata_global('tblloginsocialresource',$dataloginsocialresource);
                        $this->infobj->insertdata_global('tblUserType',$datausertype);                        
                        $this->infobj->insertdata_global('tblAdminSettings',$data3);
                        $this->infobj->insertdata_global('tblConfiguration',$configuration);
                        $this->infobj->insertdata_global('tblStaticPages',$data3);
                        $this->infobj->insertdata_global('tblDbeeCats',$dbeeCats); 
                        

                        $data['status'] = 'success';
                        $data['message'] = 'Client added successfully';    

                     } 
               }
          }
        }
        else 
        {
            $data['status']  = 'error';
            $data['message'] = 'something wrong';
        }
        return $response->setBody(Zend_Json::encode($data)); 

    }


      public function create_subdomain($subDomain,$cPanelUser,$cPanelPass,$rootDomain) {    
     
        //  $buildRequest = "/frontend/x3/subdomain/doadddomain.html?rootdomain=" . $rootDomain . "&domain=" . $subDomain;
    
    $buildRequest = "/frontend/x3/subdomain/doadddomain.html?rootdomain=" . $rootDomain . "&domain=" . $subDomain . "&dir=public_html/clientdev/production/site2/public/" . $subDomain;
 
    $openSocket = fsockopen('localhost',2082);
    if(!$openSocket) {
        return "Socket error";
        exit();
    }
 
    $authString = $cPanelUser . ":" . $cPanelPass;
    $authPass = base64_encode($authString);
    $buildHeaders  = "GET " . $buildRequest ."\r\n";
    $buildHeaders .= "HTTP/1.0\r\n";
    $buildHeaders .= "Host:localhost\r\n";
    $buildHeaders .= "Authorization: Basic " . $authPass . "\r\n";
    $buildHeaders .= "\r\n";
 
    fputs($openSocket, $buildHeaders);
    while(!feof($openSocket)) {
    fgets($openSocket,128);
    }
    fclose($openSocket);
 
    $newDomain = "http://" . $subDomain . "." . $rootDomain . "/";
 
     
        return "ok";  
     
    }


    public function checkdomainAction()
    {
        
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            
            $request        =   $this->getRequest()->getPost();
            $orderbyArr = array('id'=>'DESC');
            $testArray=$this->myclientdetails-> passSQLquery("SELECT clientDomain FROM tblClient");
            
            $arr_domain=array();
            foreach($testArray as $res)
            {               
                //$arr_domain[]=$this->extract_subdomains(preg_replace('#^https?://#', '', $res['clientDomain']));
                $arr_domain[]=$res['clientDomain'];
            }
            
            //print_r($arr_domain);                 
            $domain = trim($this->_request->getPost('client_subdomain'));

            $content='';
            $domain = str_replace('www.','',$domain);
            if($domain!="")
            {
                
                if(preg_match('/([a-zA-Z0-9\-_]+\.)?[a-zA-Z0-9\-_]+\.[a-zA-Z]{2,5}/',$domain)){

                    if(in_array($domain,$arr_domain)) 
                    {
                        $data['status']  = 'error';
                        $content.='<span class="error"><i class="fa fa-times" style="color:red;"></i> This sub-domain name is not available.</span>';
                    }       
                    else if (preg_match('/([a-zA-Z0-9\-_]+\.)?[a-zA-Z0-9\-_]+\.[a-zA-Z]{2,5}/',$domain)) 
                    {
                        $data['status']  = 'success';
                        $content.='<span class="success"><img src="'.BASE_URL.'/adminraw/images/tickani.gif" /> Sub-domain name is available.</span>';
                    } 
                    else 
                    {
                         $data['status']  = 'error';
                        $content.='<span class="error">Use alphanumeric characters only.</span>';
                    }

                } 
                else 
                {

                    $content.='<span class="error"><i>'.$domain.'</i> is not a valid URL.</span>';
                    
                }
            }

            $data['content'] = $content;
        }
        else 
        {
            $data['status']  = 'error';
            $data['message'] = 'something wrong';
        }
        return $response->setBody(Zend_Json::encode($data)); 
            
    }

    public function extract_domain($domain)
    {
        if(preg_match("/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $domain, $matches))
        {
            return $matches['domain'];
        } else {
            return $domain;
        }
    }

    public function extract_subdomains($domain)
    {
        $subdomains = $domain;
        $domain = $this->extract_domain($subdomains);

        $subdomains = rtrim(strstr($subdomains, $domain, true), '.');

        return $subdomains;
    }

    public function task1Action()
    {
        /*$this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); */
            
    }

    public function task2Action()
    {
        /*$this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); */
            
    }

  
}
