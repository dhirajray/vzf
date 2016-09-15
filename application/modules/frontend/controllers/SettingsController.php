<?php
class SettingsController extends IsController
{
    
    public function init()
    {
        parent::init();
        $storage = new Zend_Auth_Storage_Session();
        $this->myclientdetails = new Application_Model_Clientdetails();
        $auth    = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $data          = $storage->read();
            $this->data = $data;
            $this->_userid = $data['UserID'];
            $this->user_session = $this->session_name_space;
        } else {
            $this->_helper->redirector->gotosimple('index', 'index', true);
        }
    }
    
    public function indexAction()
    {
         $request = $this->view->request = $this->getRequest()->getParams();
    }
    public function desconnectfromtwitterAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $filter = new Zend_Filter_StripTags();
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $storage = new Zend_Auth_Storage_Session();
        if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        {
                $user_personal_info['twitter_connect_data'] = '';
                
                $changeuserinfo = $this->User_Model->updateusergeoinfo($user_personal_info,$this->_userid);

                $result_array = $this->User_Model->userdetailall($this->_userid);

                    $this->myclientdetails->sessionWrite($result_array[0]);
        }
        else
        {
            $data['status'] = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));

    }
    public function desconnectfromfacebookAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $filter = new Zend_Filter_StripTags();
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $storage = new Zend_Auth_Storage_Session();
        if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        {
            $user_personal_info['facebook_connect_data'] = '';
            $changeuserinfo = $this->User_Model->updateusergeoinfo($user_personal_info,$this->_userid);
             $result_array = $this->User_Model->userdetailall($this->_userid);

                    $this->myclientdetails->sessionWrite($result_array[0]);
        }
        else
        {
            $data['status'] = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }
     public function desconnectfromlinkedinAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $filter = new Zend_Filter_StripTags();
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $storage = new Zend_Auth_Storage_Session();
        if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        {
                $user_personal_info['linkedin_connect_data'] = '';
                $changeuserinfo = $this->User_Model->updateusergeoinfo($user_personal_info,$this->_userid);
                $result_array = $this->User_Model->userdetailall($this->_userid);

                $this->myclientdetails->sessionWrite($result_array[0]);
        }
        else
        {
            $data['status'] = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));

    }
    public function facebooklogin()
    {

        $params = array(
                    'appId' => facebookAppid,
                    'secret' => facebookSecret,
                    'domain' => facebookDomain
                );
        $facebook = new Facebook($params);
        if(isset($_GET['code']))
        {
            $user = $facebook->getUser();
            if ($user)
            {
                $storage = new Zend_Auth_Storage_Session();
                try
                {
                    $userdata = $facebook->api('/me');
                    $access_token_title = 'fb_'.facebookAppid.'_access_token';
                    $access_token = $_SESSION[$access_token_title];
                 
                    $dataArray['access_token'] = $access_token;
                    $dataArray['facebookid'] = $userdata['id'];
                    $dataArray['facebookname'] = $userdata['name'];
                    $graph_url_pages = "https://graph.facebook.com/me/accounts?access_token=".$access_token;
                    $dataArray['facebookPage'] = file_get_contents($graph_url_pages);

                    $user_personal_info['facebook_connect_data'] = Zend_Json::encode($dataArray);
                    $user_personal_info['UserID'] = $this->_userid;
                    $changeuserinfo = $this->User_Model->updateinfouser($user_personal_info);
                    
                    $result_array = $this->User_Model->userdetailall($this->_userid);

                    $this->myclientdetails->sessionWrite($result_array[0]);

                    $this->_redirect('/user/'.$this->myclientdetails->customDecoding($result_array[0]['Username']).'?qt=4');                   

                }
                catch (FacebookApiException $e) {
                    error_log($e);
                    $user = null;
                }
            }
        }
        return $facebook->getLoginUrl(array(
         'scope' => 'user_posts,user_friends,user_birthday,publish_pages,publish_actions,public_profile,email,manage_pages,user_tagged_places'
        ));
    }
    public function socialconnectAction()
    {
        $data = array();
        $Text = '';
        $url = $this->facebooklogin();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $filter = new Zend_Filter_StripTags();
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $redirection_name_space = new Zend_Session_Namespace('User_Session');
        $redirection_name_space->redirectToProfile = 'profile';
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        {
            
         if($this->socialloginabilitydetail['Facebookstatus']==0){

                if(empty($this->facebook_connect_data))
                {
                    $Text .='<a  href="'.$url.'" class="shareAllSocials fbAllShare" >
                      <div class="signwithSprite signWithSpriteFb">
                        <i class="fa dbFacebookIcon fa-5x"></i>
                        <span>Facebook</span>
                      </div>
                      <div class="socialCntSts cntSts"><i class="signCntSprite signCntIcon"></i> Connect</div>
                    </a>';
                }else
                {
                    $Text .='<div class="disconnectFromFacebook shareAllSocials fbAllShare" data-url="'.$url.'"><i class="signCntSprite signCntIcon signCntSmallIcon"></i>
                      <div class="signwithSprite signWithSpriteFb">
                        <i class="fa dbFacebookIcon fa-5x"></i>
                        <span>Facebook</span>
                      </div>
                     <div class="socialCntSts"><i class="signCntSprite signDisCntIcon"></i> Disconnect</div>
                    </div>';
                }
         }

         if($this->socialloginabilitydetail['Twitterstatus']==0){

            if(!empty($this->twitter_connect_data))
            {   
                $Text .='<div class="disconnectFromTwitter shareAllSocials twAllShare"><i class="signCntSprite signCntIcon signCntSmallIcon"></i>
                              <div class="signwithSprite signWithSpriteTwitter">
                                <i class="fa dbTwitterIcon fa-5x"></i>
                                <span>Twitter</span>
                              </div>
                               <div class="socialCntSts"><i class="signCntSprite signDisCntIcon"></i> Disconnect</div>
                            </div>';

            }else
            {
                  $Text .='<a href="'.BASE_URL.'/twitter"  class="shareAllSocials twAllShare">
                              <div class="signwithSprite signWithSpriteTwitter">
                                <i class="fa dbTwitterIcon fa-5x"></i>
                                <span>Twitter</span>
                              </div>
                               <div class="socialCntSts cntSts"><i class="signCntSprite signCntIcon"></i> Connect</div>
                            </a>';
            }
        }
             
    
           
            $data['content'] = $Text;
            $data['status'] = 'success';
                       
        }else
        {
            $data['status'] = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }


     public function socialconnect2Action()
    {
        $data = array();
        $Text = '';
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $filter = new Zend_Filter_StripTags();
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $redirection_name_space = new Zend_Session_Namespace('User_Session');
        
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        {   
            $dbeeid = $this->_request->getPost('id');
            $fromplace = $this->_request->getPost('fromplace');
            $checkdbeeexist = $this->myhome_obj->checkdbeeexist($dbeeid);
            $this->session_name_space->callBackUrl = '/dbee/' . $checkdbeeexist[0]['dburl'].'?from='.$fromplace.'&tye=';
            $Text .='<a href="'.BASE_URL.'/twitter"  class="shareAllSocials twAllShare">
                      <div class="signwithSprite signWithSpriteTwitter">
                        <i class="fa dbTwitterIcon fa-5x"></i>
                        <span>Twitter</span>
                      </div>
                       <div class="socialCntSts cntSts"><i class="signCntSprite signCntIcon"></i> Connect</div>
                    </a>';
            $data['content'] = $Text;
            $data['status'] = 'success';
                       
        }else
        {
            $data['status'] = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    
     public function socialconnectfacebookAction()
    {
        $data = array();
        $Text = '';
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $url = $this->facebooklogin();
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        {   
            
             $Text .='<a  href="'.$url.'" class="shareAllSocials fbAllShare" >
                  <div class="signwithSprite signWithSpriteFb">
                    <i class="fa dbFacebookIcon fa-5x"></i>
                    <span>Facebook</span>
                  </div>
                  <div class="socialCntSts cntSts"><i class="signCntSprite signCntIcon"></i> Connect</div>
                </a>';
            $data['content'] = $Text;
            $data['status'] = 'success';
                       
        }else
        {
            $data['status'] = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }

    public function notificationsettingsAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $filter = new Zend_Filter_StripTags();
        
            $usersnoti = new Application_Model_DbNotificationsettings();
            $getusersnotiinfo = $this->myclientdetails->getfieldsfromtable('*','tblNotificationSettings','User',$this->_userid);
            if (isset($getusersnotiinfo) && !empty($getusersnotiinfo)) 
            {
                
                $this->user_session->notificationsToken = md5(time());
                
                // CHECK CURRENT STATUS OF DB NOTIFICATIONS
                 

                $radioDB = $usersnoti->settingnotificationtemplate($getusersnotiinfo[0]['Dbees'],'1');

                $radioComments = $usersnoti->settingnotificationtemplate($getusersnotiinfo[0]['Comments'],'5');

                $radioGroup    = $usersnoti->settingnotificationtemplate($getusersnotiinfo[0]['Groups'],'2');

                $radioGroupDontFollow = $usersnoti->settingnotificationtemplate($getusersnotiinfo[0]['GroupsDontFollow'],'4');

                $radioMessage = $usersnoti->settingnotificationtemplate($getusersnotiinfo[0]['Messages'],'3');

                $radioFollowers = $usersnoti->settingnotificationtemplate($getusersnotiinfo[0]['Followers'],'6');
                
                $return .= '<input type="hidden" id="notificationsToken" name="notificationsToken" value ="'.$this->user_session->notificationsToken.'" >
                <h2> My email notifications</h2><div id="passform" class="postTypeContent myEmailNotification">
                            <div class="formRow">
                                <div id="scoring-radio-1" class="fieldClass mynotesonoff">' . $radioDB . '</div>
                                <div class="formField">'.POST_NAME.'s <span>Notifies you when someone you follow starts a new post.</span></div>                          
                            </div>
							<div class="formRow">                               
                                <div id="scoring-radio-5" class="fieldClass mynotesonoff">' . $radioComments . '</div> 
                                 <div class="formField">comments <span>Notifies you when there’s a new comment on a post you are a part of.</span></div>                                                               
                            </div>
							<div class="formRow">                                
                                <div id="scoring-radio-2" class="fieldClass">' . $radioGroup . '</div>
                                <div class="formField">groups <span>Notifies you when someone starts a new post in a group you are a member of.</span></div>
                            </div>
							<div class="formRow">                                
                                <div id="scoring-radio-3" class="fieldClass mynotesonoff">' . $radioMessage . '</div>
                                <div class="formField">messages <span>Notifies you when someone sends you a message.</span></div>
                            </div>
							<di class="formRow">                                
                                <div id="scoring-radio-6" class="fieldClass mynotesonoff">' . $radioFollowers . '</div>
                                <div class="formField">followers <span>Notifies you when someone follows you.</span></div>
                            </div>

							</div>';
            }else{
            	$return = 'Please update your email address to receive platform email notifications';
            }
            $data['status']  = 'success';
            $data['content'] = $return;

        return $response->setBody(Zend_Json::encode($data));
        
    }
    
    public function detailAction()
    {
        $request = $this->getRequest()->getParams();
        $this->_helper->layout->disableLayout();
        
    }
    
    public function updatenotificationAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
        	$usersnoti        = new Application_Model_DbNotificationsettings();
	        $id = (int)$this->_request->getPost('id');
	        if($this->user_session->notificationsToken == $this->_request->getPost('notificationsToken') && $id != '')
	        {
		        if ($id == '1')
		            $field = 'Dbees';
		        elseif ($id == '2')
		            $field = 'Groups';
		        elseif ($id == '3')
		            $field = 'Messages';
		        elseif ($id == '4')
		            $field = 'GroupsDontFollow';
		        elseif ($id == '5')
		            $field = 'Comments';
		        elseif ($id == '6')
		            $field = 'Followers';
		      	        
		        $getusersnotiinfo = $usersnoti->ausernotidetail($this->_userid);

		        if ($getusersnotiinfo[0][$field] == 0) 
		        {
		            $dataval             = array(
		                'User' => $this->_userid,
		                $field => '1'
		            );
		            $status           = '1';
		        }
		        else 
		        {
		            $dataval             = array(
		                'User' => $this->_userid,
		                $field => '0'
		            );

		            $status           = '0';
		        }
		        //$updateusersnotiinfo = $usersnoti->editausernotidetail($dataval);
                $updateusersnotiinfo  = $this->myclientdetails->updatedata_global('tblNotificationSettings',$dataval,'User',$this->_userid);
		        $data['status']  = $status;
		        $data['id']  = $id;
	            $data['message'] = 'Your notification settings updated.';
	            
	    	}
	       	$this->user_session->notificationsToken = md5(time());
	    	$data['notificationsToken'] = $this->user_session->notificationsToken;
	     }  
       	
        return $response->setBody(Zend_Json::encode($data));
    }
   
    public function updateprivacyAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $filter = new Zend_Filter_StripTags();
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
        
            $validate = $this->commonmodel_obj->authorizeToken($this->_request->getPost('SessUser__'), $this->_request->getPost('SessId__'), $this->_request->getPost('SessName__'), $this->_request->getPost('Token__'), $this->_request->getPost('Key__'));
            if ($validate == false)
            {
                $data['status'] = 'error';
                $data['content'] = 'Something went wrong please try again';
            }else
            {
                $dataArray['Numbermakeprivate'] = $this->_request->getPost('makenumberprivate');
                $dataArray['DOBmakeprivate'] = $this->_request->getPost('makedobprivate');
                $dataArray['Emailmakeprivate'] = $this->_request->getPost('makeemailprivate');

                $changeuserinfo = $this->myclientdetails->updatedata_global('tblUsers',$dataArray,'UserID',$this->_userid);
                $data['status'] = 'success';
                $data['content'] = 'Your privacy has been updated';
                $data['user'] = $this->_userid;
                $result_array = $this->User_Model->userdetailall($this->_userid);

                $this->myclientdetails->sessionWrite($result_array[0]);
            }
        }
        
        return $response->setBody(Zend_Json::encode($data));
    }

    public function makeprivateAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $filter = new Zend_Filter_StripTags();
        if ($this->getRequest()->isXmlHttpRequest()) 
        {
            $checked1 = $checked2 = $checked3 = '';
            $dataall = $this->myclientdetails->getfieldsfromtable('*','tblUsers','UserID',$this->_userid,'clientID',clientID);
            
            if($dataall[0]['Emailmakeprivate']==1)
                $checked1 = 'checked';
            if($dataall[0]['Numbermakeprivate']==1)
                $checked2 = 'checked';
            if($dataall[0]['DOBmakeprivate']==1)
                $checked3 = 'checked';

            $return ='<div id="passform" class="makePrivatePopup postTypeContent">
            <div class="formRow">
                <div class="formField">
                <label class="labelCheckbox" for="makeemailprivate"><input '.$checked1.'  type="checkbox" name ="makeemailprivate" id="makeemailprivate"  /><label class="checkbox" for="makeemailprivate"></label>Yes</label>
                <i class="optionalText" >Make email private</i>
                </div>
            </div>
            <div class="formRow">
                <div class="formField">
                <label class="labelCheckbox" for="makenumberprivate"><input '.$checked2.'  type="checkbox" name ="makenumberprivate" id="makenumberprivate"  /><label class="checkbox" for="makenumberprivate"></label>Yes</label>
                <i class="optionalText">Make contact number private</i>
                </div>
            </div>
            <div class="formRow">
                <div class="formField">
                <label class="labelCheckbox" for="makedobprivate"><input '.$checked3.'  type="checkbox" name ="makedobprivate" id="makedobprivate"  /><label class="checkbox" for="makedobprivate"></label>Yes</label>
                <i class="optionalText">Make date of birth private</i>
                </div>
            </div>
            </div>'; 
            $data['status'] = 'success';
            $data['content'] = $return;
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    public function accounteditAction()
    {
        $request = $this->getRequest()->getParams();
        $this->_helper->layout->disableLayout();
        $namespace = new Zend_Session_Namespace();
        $storage   = new Zend_Auth_Storage_Session();
        $data  = $storage->read();
        $this->view->dataall = $this->myclientdetails->getfieldsfromtable('*','tblUsers','UserID',$data['UserID']);
        
    }
    
    public function updateAction()
    {
        $data      = array();
        $container = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
		$request = $this->getRequest();	
		$commonfun  = new Application_Model_Commonfunctionality();

		$validate = $commonfun->authorizeToken($request->getPost('SessUser__'),$request->getPost('SessId__'),$request->getPost('SessName__'),$request->getPost('Token__'),$request->getPost('Key__')); 

		if($validate=='false') {
			echo $validate; // go to success event of ajax and redirect to myhome/logout  action
			exit;
		}

		// End of token and user session checking        
        $response->setHeader('Content-type', 'application/json', true);       
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            $dataall = $this->myclientdetails->getfieldsfromtable('*','tblUsers','UserID',$this->_userid,'clientID',clientID);            
            
            $storage     = new Zend_Auth_Storage_Session();
            $auth        = Zend_Auth::getInstance();                      
            $filter = new Zend_Filter_StripTags();

            $postEmail = trim($this->_request->getPost('email'));
            if($postEmail=='')
            {
                $container['message'] = 'User email can not be blank';
                $container['status']  = 'error'; 
                return $response->setBody(Zend_Json::encode($container));
                exit; 
            }

            $query = "select Email from tblUsers where Email='".$this->myclientdetails->customEncoding($postEmail)."' AND UserID !='".$dataall[0]['UserID']."' AND clientID=".clientID;
            
            $useremailcheck = $this->myclientdetails->passSQLquery($query);
         
            if ( count($useremailcheck)>0) {             
                $container['message'] = 'Someone already has that email';
                $container['status']  = 'error'; 
                return $response->setBody(Zend_Json::encode($container));
                exit;            
            }
 
            if (($dataall[0]['Socialtype'] == '2' && $this->myclientdetails->customDecoding($dataall[0]['Email']) == 'twitteruser@db-csp.com') || ($dataall[0]['Socialtype'] == '4' && $this->myclientdetails->customDecoding($dataall[0]['Email']) == 'gplususer@db-csp.com')) 
            {
                $data['Name']   = $this->myclientdetails->customEncoding($filter->filter($this->_request->getPost('fullname')));
                $data['lname']   = $this->myclientdetails->customEncoding($filter->filter($this->_request->getPost('lastname')));
                $data['Gender']         = $this->myclientdetails->customEncoding($filter->filter($this->_request->getPost('gender')));
                if($dataall[0]['ProfilePic']=='' || $dataall[0]['ProfilePic']=='default-avatar-female.jpg' || $dataall[0]['ProfilePic']=='default-avatar.jpg' || $dataall[0]['ProfilePic']=='default-profilepic-std.png'){
                        if($this->_request->getPost('gender')=='Female')
                                {$data['ProfilePic'] = 'default-avatar-female.jpg';}
                        elseif($this->_request->getPost('gender')=='Male')
                                {$data['ProfilePic'] = 'default-avatar.jpg';}
                        else{$data['ProfilePic'] = 'default-profilepic-std.png';}
                }
                $data['title']         = $this->myclientdetails->customEncoding($filter->filter($this->_request->getPost('title')));
                $data['company']         = $this->myclientdetails->customEncoding($filter->filter($this->_request->getPost('company')));
                $data['Birthdate'] = $filter->filter($this->_request->getPost('birthyear') . '-' . $this->_request->getPost('birthmonth') . '-' . $this->_request->getPost('birthday'));
                $data['full_name']   = $this->myclientdetails->customEncoding($filter->filter($this->_request->getPost('fullname')).' '.$filter->filter($this->_request->getPost('lastname')));
                $data['Email']      = $this->myclientdetails->customEncoding($postEmail);
                $data['Emailtoken'] = md5(time());
                $this->signupmail($data);
                
                $container['message'] = 'An email has been sent to ' . $postEmail . '. Please click on the verification link in the email to verify your email address and start receiving platform notifications.';
                $container['status']  = 'success';
                $container['notify']  = 1; 
                
            } else 
            {
                
                $data['Name']       = $this->myclientdetails->customEncoding($filter->filter($this->_request->getPost('fullname'))); 
                $data['lname']      = $this->myclientdetails->customEncoding($filter->filter($this->_request->getPost('lastname')));
                $data['full_name']   = $this->myclientdetails->customEncoding($filter->filter($this->_request->getPost('fullname')).' '.$filter->filter($this->_request->getPost('lastname')));
                $data['Gender']         = $this->myclientdetails->customEncoding($filter->filter($this->_request->getPost('gender')));
                if($dataall[0]['ProfilePic']=='' || $dataall[0]['ProfilePic']=='default-avatar-female.jpg' || $dataall[0]['ProfilePic']=='default-avatar.jpg' || $dataall[0]['ProfilePic']=='default-profilepic-std.png'){
                        if($this->_request->getPost('gender')=='Female')
                                {$data['ProfilePic'] = 'default-avatar-female.jpg';}
                        elseif($this->_request->getPost('gender')=='Male')
                                {$data['ProfilePic'] = 'default-avatar.jpg';}
                        else{$data['ProfilePic'] = 'default-profilepic-std.png';}
                }
                $data['title']         = $this->myclientdetails->customEncoding($filter->filter(trim($this->_request->getPost('title'))));
                $data['company']         = $this->myclientdetails->customEncoding($filter->filter(trim($this->_request->getPost('company'))));
                $data['Contacts']		= $this->myclientdetails->customEncoding($filter->filter(trim($this->_request->getPost('mobile'))));
                $data['Birthdate']      = $filter->filter($this->_request->getPost('birthyear') . '-' . $this->_request->getPost('birthmonth') . '-' . $this->_request->getPost('birthday'));
                if($postEmail != $this->myclientdetails->customDecoding($dataall[0]['Email']))
                {
                    $data['Email']      = $this->myclientdetails->customEncoding($postEmail);
                    $this->signupmail($data);
                }
                $container['message']   = 'Your profile has been updated';
                $container['status']    = 'success';        
            }
          
            $changeuserinfo = $this->myclientdetails->updatedata_global('tblUsers',$data,'UserID',$this->_userid);            
            $result_array = $this->User_Model->userdetailall($this->_userid);

             $this->myclientdetails->sessionWrite($result_array[0]);
            $container['user'] = $this->_userid;
            
        } else {
            $container['status']  = 'error';
            $container['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($container));
    }
    

    public function signupmail($request)
    { 
        $EmailTemplateArray = array('Email' => $request['Email'],
                                    'siteUrl' => BASE_URL,
                                    'Emailtoken' => $request['Emailtoken'],
                                    'Name' => $request['Name'],
                                    'case'=>23);
        $this->dbeeComparetemplateOne($EmailTemplateArray);
    }
    
    public function closeaccountAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $filter = new Zend_Filter_StripTags();
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            if (isset($this->_userid) && !empty($this->_userid)) {
                $data = array('Status' => '0');
                $changeuserinfo = $this->myclientdetails->updatedata_global('tblUsers',$data,'UserID',$this->_userid);
                $data['status']  = 'success';
                $data['message'] = 'Your account has been deactivated. You can re-activate your account any time by sending us a request using the link in footer.';
            }
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    
    
    public function biographyAction()
    {
        $request = $this->getRequest()->getParams();
        $this->_helper->layout->disableLayout();
        $biographyfield = $this->myclientdetails->getfieldsfromtable('*','tbl_biofields','clientID',clientID,'','','priority','DESC');        
        $userbiography = $this->myclientdetails->getfieldsfromtable('*','tblUserBiography','UserID',$this->_userid);        
        $this->view->userbiography  = $userbiography;
        $this->view->biographyfield = $biographyfield;
        //$this->view->$UserId        = $this->_userid;
       
    }

  

    public function locationAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $dataall = $this->myclientdetails->getfieldsfromtable('*','tblUsers','UserID',$this->_userid);

        $location = $this->view->partial('partials/location.phtml', array('dataall'=>$dataall,
            'myclientdetails'=>$this->myclientdetails));
        $locationData = $this->Myhome_Model->getCountry();

        $country = $this->myclientdetails->customDecoding($dataall[0]['reg_country_name']);
        $city = $this->myclientdetails->customDecoding($dataall[0]['City']);
       
        $countryData = $this->Myhome_Model->getNameCountry($country);
        $cityData = $this->Myhome_Model->getNameCity($city);

        if(empty($countryData)){
            $countryis='';
        }else{
            $countryis ='ds';
        }

        $html = array("locationDiv"=>$location,'locationData'=>$locationData,
            'country'=>$countryData[0],'city'=>$cityData[0],'countryis'=>$countryis);       
        $this->jsonResponse('success', 200, $html);
    }

    public function getaddressAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $code = $this->_request->getParam('id');
        $q = $this->_request->getParam('q');
        $locationData = $this->Myhome_Model->getCountryAddress($code,$q);      
        return $response->setBody(Zend_Json::encode($locationData)); 
    }

    public function editlocationAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $storage = new Zend_Auth_Storage_Session();
        $auth = Zend_Auth::getInstance();
        $city = $this->_request->getPost('city');
        $country = $this->_request->getPost('country');
        $cityData = $this->Myhome_Model->getCity($city);
        $countyData = $this->Myhome_Model->getidCountry($country);
        $data['reg_country_name'] = $this->myclientdetails->customEncoding($countyData[0]['name']);
        $data['City'] = $this->myclientdetails->customEncoding($cityData[0]['name']);
        $data['reg_currency_code'] = $this->myclientdetails->customEncoding($countyData[0]['code']);
        $this->myclientdetails->updatedata_global('tblUsers',$data,'UserID',$this->_userid);
        
        $result_array = $this->User_Model->userdetailall($this->_userid);

        $this->myclientdetails->sessionWrite($result_array[0]);

        $responsedata['status'] = 'success';      
        $responsedata['message'] = 'successfully updated';      
        return $response->setBody(Zend_Json::encode($responsedata)); 
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

    public function editbioAction()
    {
        
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $request = $this->getRequest()->getParams();
        $content='';

        $commonfun  = new Application_Model_Commonfunctionality();
        $userbiography = new Application_Model_DbUserbiography();

        if($request['update']==1)
        {

		
        $this->_helper->layout->disableLayout();
        $biographyfield = $this->myclientdetails->getfieldsfromtable('*','tbl_biofields','clientID',clientID); 

        if(count($biographyfield) > 0) {
            foreach ($biographyfield as $key => $value) {
              $fieldid=$value['id'];
              $fieldval=stripslashes(strip_tags($request[$fieldid]));

              $where  = array(
                            'UserID' => $this->_userid,
                            'field_id' =>$fieldid
                        );

              $biographidata = $this->myclientdetails->getRowMasterfromtable('tblUserBiography',array(
                        'field_id'),$where);

              if($biographidata['field_id'] > 0)
              {

                $data = array(           
                            'clientID' => clientID,
                            'field_id' => $value['id'],
                            'field_value' => $fieldval,                            
                            'UserID' => $this->_userid,
                            'LastUpdateDate' => date('Y-m-d H:i:s')
                        );
                     $edituserbio   = $this->myclientdetails->updateMaster('tblUserBiography', $data,$where);
                     //$data['update']=1;
                    // $data['uid']=$this->_userid;


              }
              else
              {
                  if($fieldval!="")
                  {
                    $data = array(           
                            'clientID' => clientID,
                            'field_id' => $value['id'],
                            'field_value' => $fieldval,                            
                            'UserID' => $this->_userid,
                            'LastUpdateDate' => date('Y-m-d H:i:s')
                        );
                    // $edituserbio   = $userbiography->editauserbiodetail($data);
                     $insertId = $this->myclientdetails->insertdata_global('tblUserBiography', $data);
                    //$data['insert']=1;
                  }
              }//else
            }// foreach end
        }       

        }
        
        
        if($request['UserID']!="")
        {
           $biographi = $this->myclientdetails->getfieldsfromtable('*','tblUserBiography','UserID',$request['UserID']);
        }
        else
        {
           $biographi = $this->myclientdetails->getfieldsfromtable('*','tblUserBiography','UserID',$this->_userid);  
        }

        
       
       if(count($biographi) > 0)
       {
        foreach ($biographi as $key => $value) {
            $id  = $value['field_id'];
            $field_value = $value['field_value'];

            if($field_value!="")
            {
            $content.='<div class="formField" id="'.str_replace(" ","_", $userbiography->getbiofieldsname($id)).'"><p class="prstTitle">'.$userbiography->getbiofieldsname($id).' </p><p class="bioListVal">'.$field_value.'</p></div>';
            }
        }        
       }


         $data['content']=$content;
         $data['resval']=1;

         return $response->setBody(Zend_Json::encode($data)); 

    }
    
    public function socialAction()
    {
        $data2=array();
        $data=array();
        $this->_helper->layout()->disableLayout();
        //
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);       
        $request = $this->getRequest()->getParams();
        //$this->_helper->viewRenderer->setNoRender(true);	
		$commonfun  = new Application_Model_Commonfunctionality();
        $findMeOn='';
       
        if (isset($request['cheditsoci']) && $request['cheditsoci'] == 'checkeditsoci') {
        $validate = $this->commonmodel_obj->authorizeToken($this->_request->getPost('SessUser__'),$this->_request->getPost('SessId__'),$this->_request->getPost('SessName__'),$this->_request->getPost('Token__'),$this->_request->getPost('Key__'));
		
                if($validate==false)
				{
					$data['status'] = 'error';
					$data['message'] = 'Some thing went wrong here please try again';
					echo $validate;					
				}		
            $data2                = array(
                'SocialFB' => stripslashes($request['socialfb']),
                'SocialTwitter' => stripslashes($request['socialtwitter']),
                'SocialLinkedin' => stripslashes($request['sociallinkedin']),
                'UserID' => $this->_userid,
                'LastUpdateDate' => date('Y-m-d H:i:s')
            );
            $updateusersocilinfo = $this->myclientdetails->updatedata_global('tblUsers',$data2,'UserID',$this->_userid);
            
            /*$where  = array(
                            'UserID' => $this->_userid
                        );
            $socialdata = $this->myclientdetails->getRowMasterfromtable('tblUsers',array(
                        'socialfb','socialtwitter','sociallinkedin'),$where);
           
            if($socialdata['socialfb']!="" || $socialdata['SocialTwitter']!="" || $socialdata['SocialLinkedin']!="")
             {
                 $findMeOn.='Find me on';

                 if($socialdata['SocialLinkedin']!="")
                 {
                    $findMeOn.='<a href="'.$socialdata['SocialLinkedin'].'"><i class="fa  dbLinkedInIcon fa-lg socialIcon"></i></a>';
                 }

                 if($socialdata['SocialTwitter']!="")
                 {
                     $findMeOn.='<a href="'.$socialdata['SocialTwitter'].'"><i class="fa dbTwitterIcon fa-lg socialIcon"></i></a>';
                 }

                 if($socialdata['SocialFB']!="")
                 {
                    $findMeOn.='<a href="'.$socialdata['SocialFB'].'"><i class="fa dbFacebookIcon fa-lg socialIcon"></i></a>';
                 }
             }
            

             $data['content']=$findMeOn;
             $data['seval']=1;
             return $response->setBody(Zend_Json::encode($data));*/

            exit('1');
        }
        
    }
    
    public function scoreAction()
    {          
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);       
        if ($this->getRequest()->isXmlHttpRequest())
        {   
        	$usersocial          = new Application_Model_DbUser();
	        if ($this->_request->getPost('update_toscoring') == 'updatetoscoring' && $this->getRequest()->getMethod() == 'POST') 
	        {	          	           
        		
                $getuserscoreinfo = $this->myclientdetails->getfieldsfromtable('*','tblUsers','UserID',$this->_userid);

	            if ($getuserscoreinfo[0]['ScoringStatus'] == '1') 
	            {
	                $store   = array(
	                    'ScoringStatus' => '0',
	                    'UserID' => $getuserscoreinfo[0]['UserID'],
	                    'LastUpdateDate' => date('Y-m-d H:i:s')
	                );
	               
	                $status = 0;
	                
	            }
	            else if ($getuserscoreinfo[0]['ScoringStatus'] == '0') 
	            {
	                $store = array(
	                    'ScoringStatus' => '1',
	                    'UserID' => $getuserscoreinfo[0]['UserID'],
	                    'LastUpdateDate' => date('Y-m-d H:i:s')
	                );
	               
	                $status = 1;
	            }
	            
                $updateusersocilinfo = $this->myclientdetails->updatedata_global('tblUsers',$store,'UserID',$store['UserID']);
	        }
    	
    		
            $getuserscoreinfo = $this->myclientdetails->getfieldsfromtable('*','tblUsers','UserID',$this->_userid,'clientID',clientID);
			
			if($getuserscoreinfo[0]['ScoringStatus']=='1')

         
			$radio ='<label class="switcher notiSettingBtn"  onclick="updatescoring(event);"><input type="checkbox"><div></div>
                    <span class="switchOnOff">
                        <span class="switchOn"></span>
                        <span class="switchOff"></span>
                    </span>
                </label>';
			else
			$radio='<label class="switcher notiSettingBtn"  onclick="updatescoring(event);"><input type="checkbox" checked="checked"><div></div>
                    <span class="switchOnOff">
                        <span class="switchOn"></span>
                        <span class="switchOff"></span>
                    </span>
                </label>';


			$return.='<div class="formLabel" style="width:100px;">scoring status</div><div id="scoring-radio" class="fieldClass">'.$radio.'</div>

			<div style="float:left; margin-left:10px; font-size:10px; margin-top:28px;">This hides your scores from the public and league tables, it doesn\'t however remove your followers position.</div>

			<br style="clear:both; font-size:1">';

			$data['status']  = 'success';
            $data['content'] = $return;
            $data['scoredNot']  = $getuserscoreinfo[0]['ScoringStatus'];


		}else {
            $data['status']  = 'error';
            $data['message'] = 'you are doing bad request';
        }
      
        return $response->setBody(Zend_Json::encode($data));
 
    }
    
    public function changepasswordAction()
    {
        $request = $this->getRequest()->getParams();
        $this->_helper->layout->disableLayout();
    }
    
    public function sendpasswordAction()
    {
        
        
        $usersdetail = new Application_Model_DbUser();
        $request     = $this->getRequest();
        $email       = $request->getpost('email');
        $birthday    = $request->getpost('birthday');
        $birthmonth  = $request->getpost('birthmonth');
        $birthyear   = $request->getpost('birthyear');
        $fromlogin   = $request->getpost('fromlogin');
        
        
        
        if (!empty($fromlogin)) {
            
            $fromlogin = 1;
            $Birthdate = $birthyear . '-' . $birthmonth . '-' . $birthday;
            $Res       = $usersdetail->ausersocialdetailforget($email, $Birthdate);
        } else {
            $fromlogin = 0;
            $user      = $request->getpost('user');
            $Res       = $usersdetail->ausersocialdetail($user);
        }
        
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        
        
        
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            
            
            if (count($Res) > 0) {
                $randkey  = rand(45982, 99999);
                $data = array('ResetPassCode' => $randkey);                         
                $store = $this->myclientdetails->updatedata_global('tblUsers',$data,'UserID',$Res[0]['UserID']);
                /****for email ****/ 
                $EmailTemplateArray = array('Email' => $Res[0]['Email'],
                                            'Name' => $Res[0]['Name'],
                                            'lname' => $Res[0]['lname'],
                                            'UserID' => $Res[0]['UserID'],
                                            'randkey'=> $randkey,
                                            'case'=>8);
                $bodyContentmsgall = $this->dbeeComparetemplateOne($EmailTemplateArray);
                /****for email ****/                         

                if ($bodyContentmsgall) {
                    
                    $SubmitMessage = 0;
                } else {
                    
                    $SubmitMessage   = 1;
                    $data['message'] = 'Detail don\'t match';
                }
                $data['content']       = 2;
                $data['SubmitMessage'] = $SubmitMessage;
                $data['fromlogin']     = $fromlogin;
                
            } else {
                $data['content']       = 1;
                $data['message']       = 'Detail don\'t match';
                $data['SubmitMessage'] = $SubmitMessage;
            }
            
            
        } else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        
        return $response->setBody(Zend_Json::encode($data));
        exit;
        
    }
    
    public function recoverAction()
    {
        $request        = $this->getRequest()->getParams();
        $user           = $this->_userid;
        $this->view->id = $request['id'];
        if (!empty($user)) {
            $this->redirect('settings/recover2/id/' . $request['id']);
        }
    }
    
    public function recover2Action()
    {
        $request        = $this->getRequest()->getParams();
        $this->view->id = $request['id'];
        
    }
    
    public function checkresetcodeAction()
    {
        $request = $this->getRequest()->getParams();
        $this->_helper->layout->disableLayout();
        $this->view->resetcode = $request['resetcode'];
        $this->view->user      = $request['user'];
    }
    
    public function resetpassAction()
    {
        $request = $this->getRequest()->getParams();
        $this->_helper->layout->disableLayout();
        $this->view->newpass = $request['newpass'];
        $this->view->user    = $request['user'];
    }
    
}
