<?php

class Admin_SocialController extends IsadminController
{
	/**
	 * Init
	 * 
	 * @see Zend_Controller_Action::init()
	 */
    public function init()
    {
        parent::init();
        $this->twitter_connect_data = Zend_Json::decode($this->session_data['twitter_connect_data']);
        $this->facebook_connect_data = Zend_Json::decode($this->session_data['facebook_connect_data']);
        
	}
	
	public function sendMail($data,$db_url='')
    {   
		$EmailTemplateArray = array('uEmail'  => $data['Email'],
                                    'uName'   => $data['Name'],
                                    'lname'   => $data['lname'],
                                    'db_url' => $db_url,
                                    'case'     => 4);
        $bodyContentmsgall = $this->dbeeComparetemplateOne($EmailTemplateArray); 
		/****for email ****/
    }
	public function linkedinAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		

		$config['base_url']             =   BASE_URL;
		$config['callback_url']         =   BASE_URL."/admin/social/callback";
		$config['linkedin_access']      =   linkedinAppid;
		$config['linkedin_secret']      =   linkedinSecret;

		# First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
		$linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], $config['callback_url'] );
		//$linkedin->debug = true;

		# Now we retrieve a request token. It will be set as $linkedin->request_token
		$linkedin->getRequestToken();

		$this->session_name_space->postid = $_GET['postid'];
		$this->session_name_space->type = $_GET['type'];
		$this->session_name_space->socialinvite = $_GET['socialinvite'];
		$this->session_name_space->requestToken = serialize($linkedin->request_token);

		header("Location: " . $linkedin->generateAuthorizeUrl());

	}


	 public function facebookAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $params = array(
                    'appId' => facebookAppid,
                    'secret' => facebookSecret,
                    'domain' => facebookDomain
                );
        $facebook = new Facebook($params);
        $type = $this->_request->getParam('type');
        $this->session_name_space->type = $type; 
        if(isset($_GET['code']))
        {
            $user = $facebook->getUser();
            if ($user)
            {
                $storage = new Zend_Auth_Storage_Session();
                $logoutUrl = $facebook->getLogoutUrl();
                try
                {
                    $userdata = $facebook->api('/me'); 
                    $access_token_title = 'fb_'.facebookAppid.'_access_token';
                    $access_token = $_SESSION[$access_token_title];
					$dataArray['access_token'] = $access_token;
                    $dataArray['facebookid'] = $userdata['id'];
                    $dataArray['facebookname'] = $userdata['name'];
                    // get facebook page data
                    $graph_url_pages = "https://graph.facebook.com/me/accounts?access_token=".$access_token;
                    $dataArray['facebookPage'] = file_get_contents($graph_url_pages);
                    $user_personal_info['facebook_connect_data'] = Zend_Json::encode($dataArray);
                    $this->myclientdetails->updatedata_global('tblUsers',$user_personal_info,'UserID',$this->_userid);
                  	$this->rewritesession();

                  	if($this->session_name_space->type=='socialinvite')
						$this->_redirect(BASE_URL.'/admin/user/invitesocial?invite=twitter&type=socialinvite');
					else if($this->session_name_space->type=='expert')
			   			$this->_redirect(BASE_URL.'/admin/dashboard/specialdbs?invite=twitter&type=expert');
			   		else if($this->session_name_space->type=='config')
			   			$this->_redirect('/admin/myaccount/configuration#socialconnect');
			   		else if($this->session_name_space->type=='attendies')
			   			$this->_redirect(BASE_URL.'/admin/dashboard/specialdbs?invite=twitter&type=attendies');
			   		else if($this->session_name_space->type=='event')
			   			$this->_redirect(BASE_URL.'/admin/event?invite=facebook&type=event');
                }
                catch (FacebookApiException $e) {
                    error_log($e);
                    $user = null;
                }
            }
        }
        $this->_redirect($facebook->getLoginUrl(array(
         'scope' => 'user_posts,user_tagged_places,user_friends,user_birthday,publish_pages,publish_actions,public_profile,email,manage_pages'
        )));
    }

	public function twitterAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$config = array(
				'consumerKey' => twitterAppid,
				'consumerSecret' => twitterSecret
			);


		$TwitterOAuth = new TwitterOAuth($config['consumerKey'], $config['consumerSecret']);
		$request_token = $TwitterOAuth->getRequestToken(FRONT_URL.'/admin/social/twittercallback');

		$this->session_name_space->oauth_token = $request_token['oauth_token'];
		$this->session_name_space->oauth_token_secret = $request_token['oauth_token_secret'];

		// If everything goes well..
		$this->session_name_space->postid = $_GET['postid'];
		$this->session_name_space->type = $_GET['type'];
		
		if ($TwitterOAuth->http_code == 200) {
			// Let's generate the URL and redirect
			$url = $TwitterOAuth->getAuthorizeURL($request_token['oauth_token']);
			$this->_redirect($url);
				
		} else
			$this->_redirect(BASE_URL.'/admin/user/invitesocial');
	}

	public function twittercallbackAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$config = array(
				'consumerKey' => twitterAppid,
				'consumerSecret' => twitterSecret
			);

		$oauth_verifier = $this->_request->getParam('oauth_verifier');
		

		if (!empty($oauth_verifier) && !empty($this->session_name_space->oauth_token) && !empty($this->session_name_space->oauth_token_secret)) 
		{
				
			$twitteroauth = new TwitterOAuth($config['consumerKey'],
					$config['consumerSecret'],
					$this->session_name_space->oauth_token,
					$this->session_name_space->oauth_token_secret);
				
			$access_token = $twitteroauth->getAccessToken($oauth_verifier);
			// Save it in a session var
			$this->session_name_space->access_token = $access_token;
				
			// Let's get the user's info
			$user_info = $twitteroauth->get('account/verify_credentials');

			$dataArray['twitter_access_token'] = $access_token['oauth_token'];
		    $dataArray['twitter_token_secret'] = $access_token['oauth_token_secret'];
		    $dataArray['screen_name'] = $user_info->screen_name;
		    $dataArray['screen_id'] = $user_info->id_str;
		    
	    	$user_personal_info['twitter_connect_data'] = Zend_Json::encode($dataArray);
	    	$this->myclientdetails->updatedata_global('tblUsers',$user_personal_info,'UserID',$this->_userid);
	    	$this->rewritesession();

	   	    if($this->session_name_space->type=='socialinvite')
				$this->_redirect(BASE_URL.'/admin/user/invitesocial?invite=twitter&type=socialinvite');
			else if($this->session_name_space->type=='expert')
	   			$this->_redirect(BASE_URL.'/admin/dashboard/specialdbs?invite=twitter&type=expert');
	   		else if($this->session_name_space->type=='config')
	   			$this->_redirect('/admin/myaccount/configuration#socialconnect');
	   		else if($this->session_name_space->type=='attendies')
	   			$this->_redirect(BASE_URL.'/admin/dashboard/specialdbs?invite=twitter&type=attendies');
	   		else if($this->session_name_space->type=='event')
			   			$this->_redirect(BASE_URL.'/admin/event?invite=twitter&type=event');

		}else
			$this->_redirect(BASE_URL.'/admin/user/invitesocial');
		
	}
	public function callbackAction()
	{

		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$config['base_url']             =   BASE_URL;
		$config['callback_url']         =   BASE_URL."/admin/social/callback";
		$config['linkedin_access']      =   linkedinAppid;
		$config['linkedin_secret']      =   linkedinSecret;

	    # First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
	    $linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], '');
	    $linkedin->debug = false;

	   if (isset($_REQUEST['oauth_verifier']))
	   {
	        $this->session_name_space->oauth_verifier   = $_REQUEST['oauth_verifier'];

	        $linkedin->request_token    =   unserialize($this->session_name_space->requestToken);

	        $linkedin->oauth_verifier   =   $this->session_name_space->oauth_verifier;

	        $linkedin->getAccessToken($_REQUEST['oauth_verifier']);

	        $this->session_name_space->oauth_access_token = serialize($linkedin->access_token);

	        header("Location: " . $config['callback_url']);
	        exit;
	   }
	   else
	   {
	        $linkedin->request_token    =   unserialize($this->session_name_space->requestToken);
	        $linkedin->oauth_verifier   =   $this->session_name_space->oauth_verifier;
	        $linkedin->access_token     =   unserialize($this->session_name_space->oauth_access_token);

	        $resultJson = $linkedin->getProfile("~:(id,email-address,first-name,last-name,picture-url;secure=true,date-of-birth)?format=json");
	   		$jsonData = json_decode($resultJson);
	   		
			$email = $jsonData->emailAddress;
	   		$firstName = $jsonData->firstName;
	   		$lastName = $jsonData->lastName;
	   		$id = $jsonData->id;

	        $dataArray['request_token'] = $this->session_name_space->requestToken;
			$dataArray['oauth_verifier'] = $this->session_name_space->oauth_verifier;
			$dataArray['oauth_access_token'] = $this->session_name_space->oauth_access_token;
			$dataArray['firstName'] = $firstName;
			$dataArray['id'] = $id;
			$dataArray['connnected'] = 'connnected';
			$user_personal_info['linkedin_connect_data'] = Zend_Json::encode($dataArray);
			$this->myclientdetails->updatedata_global('tblUsers',$user_personal_info,'UserID',$this->_userid);
			$this->rewritesession();

			if($this->session_name_space->type=='socialinvite')
				$this->_redirect(BASE_URL.'home/user/invitesocial?invite=linkedin&type=socialinvite');
			else if($this->session_name_space->type=='expert')
	   			$this->_redirect(BASE_URL.'home/dashboard/specialdbs?invite=linkedin&type=expert');
	   		else if($this->session_name_space->type=='config')
	   			$this->_redirect('/admin/myaccount/configuration#socialconnect');
	   		else if($this->session_name_space->type=='attendies')
	   			$this->_redirect(BASE_URL.'home/dashboard/specialdbs?invite=linkedin&type=attendies');
	   		else if($this->session_name_space->type=='event')
			   			$this->_redirect(BASE_URL.'/admin/event?invite=linkedin&type=event'); 
			
	   }
	}
	 public function linkedingroupAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);    
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest()) 
        { 
       
            $config['base_url']        = BASE_URL;
            $config['callback_url']    = BASE_URL . '/admin/social/callback';
            $config['linkedin_access'] = linkedinAppid;
            $config['linkedin_secret'] = linkedinSecret;
            # First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
            $linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], '');
            $linkedin->debug = false;
            $result = $this->myclientdetails->getRowMasterfromtable('tblConfiguration',array('*'));
            $linkedin_connect_data = Zend_Json::decode($this->session_data['linkedin_connect_data']);
            
            $linkedin->request_token             = unserialize($linkedin_connect_data['requestToken']);
            $linkedin->oauth_verifier            = $linkedin_connect_data['oauth_verifier'];
            $linkedin->access_token              = unserialize($linkedin_connect_data['oauth_access_token']);
            $data['getnetwork'] = $linkedin->group('~/group-memberships:(group:(id,name,short-description,description,site-group-url,small-logo-url,large-logo-url),membership-state,show-group-logo-in-profile,allow-messages-from-members,email-digest-frequency,email-announcements-from-managers,email-for-every-new-post)?format=json&secure-urls=true');
            $data['linkedinGroupID'] = $result['linkedin_page_id'];
       }
       return $response->setBody(Zend_Json::encode($data));           
    }
	public function linkedinfriendsAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest())
		{
			$config['base_url']             =   BASE_URL;
			$config['callback_url']         =   BASE_URL."home/social/callback";
			$config['linkedin_access']      =   linkedinAppid;
			$config['linkedin_secret']      =   linkedinSecret;

		    # First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
		    $linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], '');
		    //$linkedin->debug = false;	
		    $linkedin_connect_data = Zend_Json::decode($this->session_data['linkedin_connect_data']);
	        $linkedin->request_token    =   unserialize($linkedin_connect_data['requestToken']);
	        $linkedin->oauth_verifier   =   $linkedin_connect_data['auth_verifier'];
	        $linkedin->access_token     =   unserialize($linkedin_connect_data['oauth_access_token']);
	 	
	        $type = $this->_request->getPost('type');
			$search_response = $linkedin->connections("?start=0&count=500&format=json");
			$result = json_decode($search_response);
			$post = array();
			$profHTML ='';
			if($type=='socialinvite')
			{
				foreach ($result->values as $key => $lvalue) 
	            {
					$where['type'] = 'linkedin';
					$socialData = $this->myclientdetails->getAllMasterfromtable('tblSocialinvitation',array('socialid'),$where,'');
					foreach($socialData as $value):
						$data['preinvitedUser'][] = $value['socialid'];
					endforeach;

					$whereU['Socialtype'] = 3;
					$socialData = $this->myclientdetails->getAllMasterfromtable('tblUsers',array('Socialid'),$whereU,'');
					foreach($socialData as $value):
						$data['preJoinedUser'][] = $value['Socialid'];
					endforeach;

					$alreadyJoined = 0;
					$alreadyInvitedHTml ='';
				 	if(!empty($data['preinvitedUser']) && in_array($lvalue->id, $data['preinvitedUser'])){
				 		$alreadyInvitedHTml = '<br><span class="bx bx-green">invited</span>';
				 	}
				 	
				    if(!in_array($lvalue->id, $data['preJoinedUser']))
				 	{	
		                $firstName = $lvalue->firstName;
		                $lastName  = $lvalue->lastName;
		                $picUrl    = $lvalue->pictureUrl;
				    	 
				    	 $profHTML .= "<div class='userFatchList boxFlowers' title='".$firstName . " " .$lastName."' socialFriendlist='true'>
			              <label class='labelCheckbox'><input type='checkbox' value='".$lvalue->id.'_'.$firstName.'@'.$picUrl.'@'.$firstName . " " .$lastName."' class='inviteuser-search' name='linkedin'>
							<div class='follower-box'>
			              <div class='usImg'><img  class=img border height=30 align='left' src='" .$picUrl . "'></div>
			              <div class='userDetailsBx'>
			              	". $firstName . " " .$lastName." ".$alreadyInvitedHTml."
			              </div>
			              </div>
			             </label>
			              </div>";
		            } 
			    }

			}
			else if($type=='preinvited')
			{
				$where['type'] = 'linkedin';
				$socialData = $this->myclientdetails->getAllMasterfromtable('tblSocialinvitation',array('DISTINCT (socialid)','name','pic'),$where,'');
				foreach ($socialData as $value) 
				{
		    		$profHTML .= '<li>
						<div class="userPic">
							<img border="0" src="'.BASE_URL.'/adminraw/socialimage/'.$value['pic'] .'" style="display: none;">
						</div>
						<div class="userDetails">
							'.$value['name'] . '
						</div>
					</li>';
				 }
			}else if($type=='event')
			{
				$DbeeID = $this->_request->getPost('DbeeID');
				$event = new Admin_Model_Event();
				$resultData = $event->getEvent($DbeeID);
				$data['post_title'] = $resultData['title'];
	            foreach ($result->values as $key => $value) 
	            {
	                $firstName = $value->firstName;
	                $lastName  = $value->lastName;
	                $picUrl    = $value->pictureUrl;			    	 
			    	$profHTML .= "<div class='userFatchList boxFlowers' title='".$firstName . " " .$lastName."' socialFriendlist='true'>
		              <label class='labelCheckbox'><input type='checkbox' value='".$value->id."' class='inviteuser-search' name='linkedin'>
					<div class='follower-box'>
		              <div class='usImg'><img  class=img border height=30 align='left' src='" .$picUrl . "'></div>
		             <div class='userDetailsBx'> ". $firstName . " " .$lastName."</div>
		             </div>
		             </label>
		              </div>"; 
			    }
			}
			else
			{
				$DbeeID = $this->_request->getPost('DbeeID');
				$db_url = $this->socialInvite->getdburltitle($DbeeID);
		    	$data['post_title'] = $db_url['VidDesc'];
	            foreach ($result->values as $key => $value) 
	            {
	                $firstName = $value->firstName;
	                $lastName  = $value->lastName;
	                $picUrl    = $value->pictureUrl;			    	 
			    	$profHTML .= "<div class='userFatchList boxFlowers' title='".$firstName . " " .$lastName."' socialFriendlist='true'>
		              <label class='labelCheckbox'><input type='checkbox' value='".$value->id."' class='inviteuser-search' name='linkedin'>
					<div class='follower-box'>
		              <div class='usImg'><img  class=img border height=30 align='left' src='" .$picUrl . "'></div>
		             <div class='userDetailsBx'> ". $firstName . " " .$lastName."</div>
		             </div>
		             </label>
		              </div>"; 
			    }
			}

		    $data['content'] = $profHTML;
		    $data['status'] = 'success';
	    }
	    return $response->setBody(Zend_Json::encode($data));
	}
	
	
	public function sendmessagetolinkedinAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$filter = new Zend_Filter_StripTags();
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		$config['base_url']             =   BASE_URL;
		$config['callback_url']         =   BASE_URL."home/social/callback";
		$config['linkedin_access']      =   linkedinAppid;
		$config['linkedin_secret']      =   linkedinSecret;
	    # First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
	    $linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], '');	
	    $linkedin_connect_data = Zend_Json::decode($this->session_data['linkedin_connect_data']);
        $linkedin->request_token    =   unserialize($linkedin_connect_data['requestToken']);
        $linkedin->oauth_verifier   =   $linkedin_connect_data['auth_verifier'];
        $linkedin->access_token     =   unserialize($linkedin_connect_data['oauth_access_token']);
	    $stringuserInfo = $this->_request->getPost('stringuserInfo');
	    $UserConnect = explode(',', $stringuserInfo);
	    $dbeeid = (int)$this->_request->getPost('dbeeID');
	    $type = $this->_request->getPost('type');
		$db_url = $this->socialInvite->getdburltitle($dbeeid);
		$result = $this->myclientdetails->getRowMasterfromtable('tblConfiguration',array('SocialShareMsg'));
		$getSocialShareMsg  = Zend_Json::decode($result['SocialShareMsg']); 
	   
	    $dburl = $db_url['dburl'];

	    $TokenArray = $this->deshboard->checkExistenceTokenexpert($dbeeid);
                
        if ($TokenArray['token'] != '')
            $token = $TokenArray['token'];
        else
            $token = time();

        $count = count($UserConnect);

		for($i = 0; $i < $count; $i++) 
		{
			if($type=='expert' && is_string($UserConnect[$i]))
			{
     			$url = $this->shortUrl(BASE_URL.'/dbee/'.$dburl.'?token='.$token.'&oathtoken='.$UserConnect[$i].'&type=linkedin');
				$body  = "I've invited you as an expert on this topic. Click the link below to join in. " . $url;
				$subject = "Invitation to join as expert";
				$apiCallStatus = $linkedin->sendMessageById($UserConnect[$i], FALSE, $subject, $body);
		
				if ($TokenArray['token'] == '') 
	            {
	                $data = array(
	                    'dbeeid' => $dbeeid,
	                    'token' => $token,
	                    'type' => 'twitter',
	                    'clientID'=>clientID,
	                    'currenttime' => date('Y-m-d H:i:s')
	                );
	                $this->deshboard->insertdata_global('tblinvitexport',$data);
	            }
			}
			elseif(is_string($UserConnect[$i]) && $db_url['eventtype']==2 && $type=='attendies')
			{
				$token = time();
				$url = $this->shortUrl(BASE_URL.'/dbee/'.$dburl.'?sptoken='.$token.'&spauthid='.$UserConnect[$i].'&sptype=linkedin');
				$body = "I've invited you to participate in a video event on ".COMPANY_NAME.". Click the link below to join in. ".$url;
				$subject = "Video event invitation";
				$linkedin->sendMessageById($UserConnect[$i], FALSE, $subject, $body);
				$dataArray['dbeeid'] = $dbeeid;
				$dataArray['socialid'] = $UserConnect[$i];
				$dataArray['type'] = 'linkedin';
				$dataArray['token'] = $token;
				$dataArray['clientID'] = clientID;
				$dataArray['status'] = 1;
				$dataArray['date'] = date('Y-m-d H:i:s');
				$this->socialInvite->insertSocialInvitation($dataArray);

			}else if($type == 'attendies' && is_string($UserConnect[$i]))
			{
				$dataArray['dbeeid'] = $dbeeid;
				$dataArray['socialid'] = $UserConnect[$i];
				$dataArray['type'] = 'linkedin';
				$dataArray['token'] = '';
				$dataArray['clientID'] = clientID;
				$dataArray['date'] = date('Y-m-d H:i:s');
				$this->socialInvite->insertSocialInvitation($dataArray);
				$url = $this->shortUrl(BASE_URL.'/dbee/'.$dburl);
				$body = "I've invited you to participate in a video event on ".SITE_NAME.". Click the link below to join in. ".$url;
				$subject = "Video event invitation";
				$linkedin->sendMessageById($UserConnect[$i], FALSE, $subject, $body);
			}
			else if($type=='event' && is_string($UserConnect[$i]))
			{
				$eventid = (int)$this->_request->getPost('dbeeID');
				$url = $this->shortUrl(BASE_URL."/event/invite/id/".$eventid.'/token/'.$token.'/authid/'.$UserConnect[$i].'/type/linkedin');
				$body = $getSocialShareMsg['event_share']." ".$url;
	            $body=str_replace("[PLATFORM_NAME]", SITE_NAME,$body);
				//$body = "I've invited you to participate in an event on ".SITE_NAME.". Click the link below to join in. ".$url;
				$subject = "Event invitation";
				$res = $linkedin->sendMessageById($UserConnect[$i], FALSE, $subject, $body);
                $dataArray['eventid'] = $eventid;
				$dataArray['socialid'] = $UserConnect[$i];
				$dataArray['type'] = 'linkedin';
				$dataArray['clientID'] = clientID;
				$dataArray['token'] = $token;
				$dataArray['currenttime'] = date('Y-m-d H:i:s');
				$this->socialInvite->insertEventInvitation($dataArray);
			}	
			else if($type=='socialinvite')
			{
				
				$linkedinA = explode('@', $UserConnect[$i]);
				$explode = explode("_", $linkedinA[0]);
				$socialV = $this->socialInvite->checkpreInviteSocial('linkedin',$explode[0]);
				if(empty($socialV))
				{
					$dataArray['socialid'] = $explode[0];
					$dataArray['type'] = 'linkedin';
					$dataArray['name'] = $linkedinA[2];
					$name = $this->image_save_from_url($linkedinA[1],$_SERVER["DOCUMENT_ROOT"].'/adminraw/socialimage/'); 
					$dataArray['pic'] = $name;
					$dataArray['date'] = date('Y-m-d H:i:s');
					$dataArray['clientID'] = clientID;
					$this->myclientdetails->insertdata_global('tblSocialinvitation',$dataArray);
				}
				$body = "Hey, check this cool website I came across for debating and sharing your views on any topic.".BASE_URL;
				
				$subject = "Invitation to join this cool website";
				$linkedin->sendMessageById($explode[0], FALSE, $subject, $body);
			}

			 $data['status'] = 'success';
			 $data['message'] = "Invitation sent successfully";
		}
	    $data['status'] = 'success';
		$data['content'] = "Invitation sent successfully";

		return $response->setBody(Zend_Json::encode($data));

	}
	
	public function twitterinvitationAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$filter = new Zend_Filter_StripTags();
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);

		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			
			$config = array(
				'consumerKey' => twitterAppid,
				'consumerSecret' => twitterSecret
			);
			
			$contcat_name = $this->_request->getPost('stringuserInfo');
			
			$screen_name = explode(',', $contcat_name);
			$type = $this->_request->getPost('type');

			$twitter_connect_data = Zend_Json::decode($this->session_data['twitter_connect_data']);

			$twitteroauth = new TwitterOAuth($config['consumerKey'],
											 $config['consumerSecret'],
											 $twitter_connect_data['twitter_access_token'], 
											 $twitter_connect_data['twitter_token_secret']);
			if($type!='socialinvite' && $type!='event')
			{
				$dbeeid = (int)$this->_request->getPost('dbeeID');
				$db_url = $this->socialInvite->getdburltitle($dbeeid);
				$dburl = $db_url['dburl'];
				$TokenArray = $this->deshboard->checkExistenceTokenexpert($dbeeid);
	                
	            if ($TokenArray['token'] != '')
	                $token = $TokenArray['token'];
	            else
	                $token = time();
            }

			if($type=='expert')
			{
				foreach ($screen_name as $value) 
				{
					$twitterArray = explode('_', $value);
	                $url = $this->shortUrl(BASE_URL.'/dbee/'.$dburl.'?token='.$token.'&oathtoken='. $twitterArray[1].'&type=twitter');
	                $twitteroauth->post('statuses/update', array(
	                    'status' => '@' . $twitterArray[0] . " I've invited you as an expert on this topic. Click the link below to join in. " . $url
	                ));
	                if ($TokenArray['token'] == '') 
		            {
		                $data = array(
		                    'dbeeid' => $dbeeid,
		                    'token' => $token,
		                    'type' => 'twitter',
		                    'clientID'=>clientID,
		                    'currenttime' => date('Y-m-d H:i:s')
		                );
		                $this->deshboard->insertdata_global('tblinvitexport',$data);
		            }
            	}
			}
			else if(!empty($screen_name) && $db_url['eventtype']==2 && $type=='attendies')
			{
				foreach ($screen_name as $value) 
				{
				    $twitterArray = explode('_', $value);
					$token = time();
                    $url = $this->shortUrl(BASE_URL.'/dbee/'.$dburl.'?sptoken='.$token.'&spauthid='.$twitterArray[1].'&sptype=twitter');
					$twitteroauth->post('statuses/update', array('status' => '@'.$twitterArray[0]." I've invited you to participate in a video event on ".SITE_NAME.". Click the link below to join in. ".$url)); 
					$dataArray['dbeeid'] = $dbeeid;
					$dataArray['socialid'] = $twitterArray[1];
					$dataArray['type'] = 'twitter';
					$dataArray['token'] = $token;
					$dataArray['clientID'] = clientID;
					$dataArray['date'] = date('Y-m-d H:i:s');
 					$this->socialInvite->insertSocialInvitation($dataArray);
				}

			}
			else if(!empty($screen_name) && $type=='event')
			{
				$eventid = $this->_request->getPost('dbeeID');
				$result = $this->myclientdetails->getRowMasterfromtable('tblConfiguration',array('SocialShareMsg'));
				$getSocialShareMsg  = Zend_Json::decode($result['SocialShareMsg']);

				

				foreach ($screen_name as $value) 
				{
				    $twitterArray = explode('_', $value);
					$token = time();
                    $url = $this->shortUrl(BASE_URL.'/event/invite/id/'.$eventid.'/token/'.$token.'/authid/'.$twitterArray[1].'/type/twitter');
                    $message = "@".$twitterArray[0] ." ". $getSocialShareMsg['event_share'];
	                $message=str_replace("[PLATFORM_NAME]", SITE_NAME,$message);
					$twitteroauth->post('statuses/update', array('status' => $message.$url)); 
					$dataArray['eventid'] = $eventid;
					$dataArray['socialid'] = $twitterArray[1];
					$dataArray['type'] = 'twitter';
					$dataArray['token'] = $token;
					$dataArray['clientID'] = clientID;
					$dataArray['currenttime'] = date('Y-m-d H:i:s');
	 				$this->socialInvite->insertEventInvitation($dataArray);
				}
			}
			else if($type == 'attendies')
			{
				foreach ($screen_name as $value) 
				{
					$twitterArray = explode('_', $value);
					$dataArray['dbeeid'] = $dbeeid;
					$dataArray['socialid'] = $twitterArray[1];
					$dataArray['type'] = 'twitter';
					$dataArray['token'] = '';
					$dataArray['clientID'] = clientID;
					$dataArray['date'] = date('Y-m-d H:i:s');
					$this->socialInvite->insertSocialInvitation($dataArray);
					$url = $this->shortUrl(BASE_URL.'/dbee/'.$dburl);
					$twitteroauth->post('statuses/update', array('status' => '@'.$twitterArray[0]." I've invited you to participate in a video event on db. Click the link below to join in. ".$url)); 
				}
			}
			else if($type=='socialinvite')
			{
				foreach ($screen_name as $value) 
				{
					
					$twitterA = explode('@', $value);
					$twitterArray = explode('_', $twitterA[0]);
					$socialV = $this->socialInvite->checkpreInviteSocial('twitter',$twitterArray[1]);
					if(empty($socialV))
					{
						$dataArray['socialid'] = $twitterArray[1];
						$dataArray['type'] = 'twitter';
						$dataArray['name'] = $twitterA[2];
						//echo $twitterA[1];
						$name = $this->image_save_from_url($twitterA[1],$_SERVER["DOCUMENT_ROOT"].'/adminraw/socialimage/'); 
						$dataArray['pic'] = $name;
						$dataArray['date'] = date('Y-m-d H:i:s');
						$dataArray['clientID'] = clientID;
						
						$this->myclientdetails->insertdata_global('tblSocialinvitation',$dataArray);
					}
			
					$twitteroauth->post('statuses/update', array('status' => '@'.$twitterArray[0]." Hey, check this cool website I came across for debating and sharing your views on any topic. ".$this->shortUrl(BASE_URL))); 
				}
			} 

			 $data['status'] = 'success';
			 $data['message'] = "Invitation sent successfully";
		}
		else
		{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));
	}


	public function twitteruserAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$filter = new Zend_Filter_StripTags();
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$config = array(
				'consumerKey' => twitterAppid,
				'consumerSecret' => twitterSecret
			);
			
			$twitter_connect_data = Zend_Json::decode($this->session_data['twitter_connect_data']);

			$type = $this->_request->getPost('type');
			$twitteroauth = new TwitterOAuth($config['consumerKey'],
											 $config['consumerSecret'],
											 $twitter_connect_data['twitter_access_token'], 
											 $twitter_connect_data['twitter_token_secret']);

			$content = '';
			$result = $twitteroauth->get('followers/list', array('screen_name' => $twitter_connect_data['screen_name'], 'cursor' => -1,'skip_status'=>true));

			if($type=='socialinvite')
			{
				$where['type'] = 'twitter';
				$socialData = $this->myclientdetails->getAllMasterfromtable('tblSocialinvitation',array('socialid'),$where,'');
				foreach($socialData as $value):
					$data['preinvitedUser'][] = $value['socialid'];
				endforeach;
				
				$whereU['Socialtype'] = 2;
				$socialData = $this->myclientdetails->getAllMasterfromtable('tblUsers',array('Socialid'),$whereU,'');
				foreach($socialData as $value):
					$data['preJoinedUser'][] = $value['Socialid'];
				endforeach;

				foreach ($result->users as $value) 
				{
				 	$alreadyJoined = 0;
				 	$alreadyInvitedHTml ='';
				 	if(!empty($data['preinvitedUser']) && in_array($value->id, $data['preinvitedUser'])){
				 		$alreadyInvitedHTml = '<br><span class="bx bx-green">invited</span>';
				 	}
				 	
				 	if(!in_array($value->id, $data['preJoinedUser']))
				 	{
						$content .= "<div class='userFatchList boxFlowers' title='".$value->name."' socialFriendlist='true'>
						<label class='labelCheckbox'><input type='checkbox' value='".$value->screen_name."_".$value->id.'@'.$value->profile_image_url_https.'@'.$value->name."' class='inviteuser-search' name='twitter'>
						<div class='follower-box'>
						<div class='usImg'><img  width='48' height='48'  src='" .$value->profile_image_url_https."?&size=bigger'> </div>
							<div class='userDetailsBx'>". $value->name."  ".$alreadyInvitedHTml."</div>
						</div>
						</label>
						</div></div>";
					}  
				 }
			}else if($type=='preinvited')
			{
				$where['type'] = 'twitter';
				$socialData = $this->myclientdetails->getAllMasterfromtable('tblSocialinvitation',array('DISTINCT (socialid) AS socialid','name','pic'),$where,'');
				foreach ($socialData as $value) 
				{
					$whereU['Socialid'] = $value["socialid"];
		 			$socialData = $this->myclientdetails->getAllMasterfromtable('tblUsers',array('Socialid'),$whereU,'');
					if(!empty($socialData)){
					$registeredText = '<a userid="'.$socialData['UserID'].'" style="right:0px;position:unset;" class="show_details_user" href="#">'.$value['name'].'</a><span class="registeredText Green"><i class="fa fa-check"></i>Registered</span>';
					$style ='';
					}else{
					$registeredText = $value['name'].'<span class="registeredText">Pending</span>';
					$style ='style="cursor:none;"';

					}
					$content .= '<li '.$style.'>
							<div class="userPic">
								<img border="0" src="'.BASE_URL.'/adminraw/socialimage/'.$value['pic'] .'" style="display: none;">
							</div>
							<div class="userDetails">
								'.$registeredText.'
							</div>
						</li>';
				 }
			}
			else if($type=='event')
			{

				$DbeeID = $this->_request->getPost('DbeeID');
				$event = new Admin_Model_Event();
				$resultData = $event->getEvent($DbeeID);
				$data['post_title'] = $resultData['title'];
				 foreach ($result->users as $value) 
				 {
					$content .= "<div class='userFatchList boxFlowers' title='".$value->name."' socialFriendlist='true'>
					<label class='labelCheckbox'><input type='checkbox' value='".$value->screen_name."_".$value->id."' class='inviteuser-search' name='twitter'>
					<div class='follower-box'>
					<div class='usImg'><img  width='48' height='48' src='" .$value->profile_image_url_https."'> </div>
						<div class='userDetailsBx'>". $value->name."</div>
					</div>
					</label>
					</div></div>";
				} 
			}	
			else
			{
				$DbeeID = $this->_request->getPost('DbeeID');
				$db_url = $this->socialInvite->getdburltitle($DbeeID);
				$data['post_title'] = $db_url['VidDesc'];
				 foreach ($result->users as $value) 
				 {
					$content .= "<div class='userFatchList boxFlowers' title='".$value->name."' socialFriendlist='true'>
					<label class='labelCheckbox'><input type='checkbox' value='".$value->screen_name."_".$value->id."' class='inviteuser-search' name='twitter'>
					<div class='follower-box'>
					<div class='usImg'><img  width='48' height='48' src='" .$value->profile_image_url_https."'> </div>
						<div class='userDetailsBx'>". $value->name."</div>
					</div>
					</label>
					</div></div>";  
				 }
			 }
			 $data['status'] = 'success';
			 
			 $data['content'] = $content;
		}
		else
		{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));
	}


	public  function facebookfriendsAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$type = $this->_request->getPost('type');
			$DbeeID = $this->_request->getPost('DbeeID');
			

			$params = array(
	            'appId' => facebookAppid,
	            'secret' => facebookSecret,
	            'domain' => facebookDomain
	        );
	            
	        $facebook = new Facebook($params);
	        $content ='';
	       	$facebookconnect = Zend_Json::decode($this->session_data['facebook_connect_data']);
	       
			$accessToken = $facebookconnect['access_token'];
			
			
			try { 
			
				if($type=='socialinvite')
				{
					$friends = $facebook->api('/'.$facebookconnect['facebookid'].'/friends?access_token='.$accessToken);

					$content.="<div class='friendslist'>";
					$where['type'] = 'facebook';
					$socialData = $this->myclientdetails->getAllMasterfromtable('tblSocialinvitation',array('socialid'),$where,'');
					
					foreach($socialData as $value):
						$data['preinvitedUser'][] = $value['socialid'];
					endforeach;
					unset($socialData);
					
					$whereU['Socialtype'] = 1;
					$socialData = $this->myclientdetails->getAllMasterfromtable('tblUsers',array('Socialid'),$whereU,'');
					foreach($socialData as $value):
						$data['preJoinedUser'][] = $value['Socialid'];
					endforeach;
					unset($socialData);

					 foreach ($friends["data"] as $value) 
					 {
					 	$alreadyJoined = 0;
					 	$alreadyInvited = 0;
					 	$alreadyInvitedHTml = '';
					 	if(!empty($data['preinvitedUser']) && in_array($value["id"], $data['preinvitedUser']))
					 	{
					 		$alreadyInvited = 1;
					 		$alreadyInvitedHTml = '<br><span class="bx bx-green">invited</span>';
					 	}

					 	if(in_array($value["id"], $data['preJoinedUser'])==false)
				 		{
				 			$stringv = $value["id"]."_".$value['name'];
							$content .= "<div class='userFatchList boxFlowers' title='".$value['name']."' socialFriendlist='true'>
							<label class='labelCheckbox'><input type='checkbox' value='".$stringv."' class='inviteuser-search' name='FacebookInvite'>
							<div class='follower-box'>
							<div class='usImg'><img class=img border  align='left' src='https://graph.facebook.com/" . $value["id"] . "/picture'> </div>
							<div class='userDetailsBx'>". $value['name']." ".$alreadyInvitedHTml."</div>
							</div>
							</label>
							</div></div>";
						}

					 }
					 $content.="</div>";
					}
					else if($type=='preinvited')
					{
						$where['type'] = 'facebook';
						$socialData = $this->myclientdetails->getAllMasterfromtable('tblSocialinvitation',array('DISTINCT (socialid)','name','pic'),$where,'');
						foreach ($socialData as $value) 
						{
							$whereU['Socialid'] = $value["socialid"];
				 			$socialData = $this->myclientdetails->getAllMasterfromtable('tblUsers',array('Socialid'),$whereU,'');
				 			if(!empty($socialData)){
				 				$registeredText = '<a userid="'.$socialData['UserID'].'"  style="right:0px;position:unset;"  class="show_details_user" href="#">'.$value['name'].'</a><span class="registeredText Green"><i class="fa fa-check"></i>Registered</span>';
				 				$style ='';
				 			}else{
				 				$registeredText = $value['name'].'<span class="registeredText">Pending</span>';
				 				$style ='style="cursor:none;"';
				 				
				 			}
					 		$content .= '<li '.$style.'>
							<div class="userPic">
								<img border="0" src="https://graph.facebook.com/' . $value["socialid"] .'/picture?width=48&height=48" style="display: none;">
							</div>
							<div class="userDetails">
								'.$registeredText.'
							</div>
							</li>';
							unset($socialData);
						 }
					}
					else if($type=='event')
					{
						$friends = $facebook->api('/'.$facebookconnect['facebookid'].'/friends?access_token='.$accessToken);
						$DbeeID = $this->_request->getPost('DbeeID');
						$event = new Admin_Model_Event();
						$resultData = $event->getEvent($DbeeID);
						$data['post_title'] = $resultData['title'];
						 foreach ($friends["data"] as $value) 
						 {
						 	$stringv = $value["id"]."_".$value['name'];
							$content .= "<div class='userFatchList boxFlowers' title='".$value['name']."' socialFriendlist='true'>
							<label class='labelCheckbox'><input type='checkbox' value='".$stringv."'  class='inviteuser-search' name='FacebookInvite'>
							<div class='follower-box'>
							<div class='usImg'><img class=img border  align='left' src='https://graph.facebook.com/" . $value["id"] . "/picture?width=70&height=70'> </div>
							<div class='userDetailsBx'>". $value['name']."</div>
							</div>
							</label>
							</div></div>";

						 }
					}
					else
					{
						$friends = $facebook->api('/'.$facebookconnect['facebookid'].'/friends?access_token='.$accessToken);
						$db_url = $this->socialInvite->getdburltitle($DbeeID);
						$data['post_title'] = $db_url['VidDesc'];
						 foreach ($friends["data"] as $value) 
						 {
						 	$stringv = $value["id"]."_".$value['name'];
							$content .= "<div class='userFatchList boxFlowers' title='".$value['name']."' socialFriendlist='true'>
							<label class='labelCheckbox'><input type='checkbox'  value='".$stringv."'  class='inviteuser-search' name='FacebookInvite'>
							<div class='follower-box'>
							<div class='usImg'><img class=img border  align='left' src='https://graph.facebook.com/" . $value["id"] . "/picture?width=70&height=70'> </div>
							<div class='userDetailsBx'>". $value['name']."</div>
							</div>
							</label>
							</div></div>";
						 }
					}				
				 $data['return'] = $content;
				 $data['status'] = 'success';
					
			} catch(FacebookApiException $e) {
				$data['status'] = 'error';
			}   
		}else{
			$data['status'] = 'error';
			$data['message'] = 'Facebook Token has not Set successfully';
		}
		return $response->setBody(Zend_Json::encode($data));
	}
	
	
	function image_save_from_url($my_img,$fullpath)
	{
		include_once 'class.get.image.php';
		$ext = strrchr($my_img, ".");
		$strlen = strlen($ext);
		$new_image_ext = 'jpg';
		$new_name = basename($my_img);
		// initialize the class
		$image = new GetImage;
		// just an image URL
		$image->source = $my_img;
		$image->save_to = $fullpath; // with trailing slash at the end
		$get = $image->download('curl'); // using GD
		if($get)
		{
			return $new_name;
		}else
			return 'default-avatar.jpg';
		
	}
	public function disconnectAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$filter = new Zend_Filter_StripTags();
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$type = $this->_request->getPost('type');
			if($type=='facebook')
				$UpdateDate['facebook_connect_data'] = '';
			else if($type=='twitter')
				$UpdateDate['twitter_connect_data'] = '';
			else if($type=='linkedin')
				$UpdateDate['linkedin_connect_data'] = '';
	    	$this->myclientdetails->updatedata_global('users',$UpdateDate,'id',$this->session_data['userid']);
	    	$this->rewritesession();

		}else{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));
	}

	public function dbeeuserAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$filter = new Zend_Filter_StripTags();
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			
			$result = $this->user_model->getAllUsers();
			$content = '';
			$DbeeID = $this->_request->getPost('DbeeID');
			$db_url = $this->socialInvite->getdburltitle($DbeeID);

			 foreach ($result as $value)   
			 {
			        $valuepic = $this->defaultimagecheck->checkImgExist($value['ProfilePic'],'userpics','default-avatar.jpg');
					$content .= "<div class='userFatchList boxFlowers' title='".$value['Name']."' socialFriendlist='true'>
					<label class='labelCheckbox'><input type='checkbox' value='".$value['UserID']."' class='inviteuser-search' name='dbeeUser'>
					<div class='follower-box'>
					<div class='usImg'><img class='img border' align='left' src='".IMGPATH."/users/small/".$valuepic."'  border='0' /></div>
						".$value['Name']."
					</div>
					</label>
					</div></div>"; 
			 }
			 $data['status'] = 'success';
			 $data['content'] = $content;
			 $data['post_title'] = $db_url['VidDesc'];
		}
		else
		{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));
	}

	public function invitedbeeuserAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$filter = new Zend_Filter_StripTags();
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$dbeeid = (int)$this->_request->getPost('dbeeID');
			$type = $this->_request->getPost('type');
			
			$db_url = $this->socialInvite->getdburltitle($dbeeid);
			
			$dburl = $db_url['dburl'];

			$Startdate=date('Y-m-d H:i:s');

			$dbeeUser = $filter->filter($this->_request->getPost('stringuserInfo'));

			$variable = explode(',', $dbeeUser);
			$fromUser = $this->_userid;

			foreach ($variable as $value) 
			{
				if($type=='expert')
				{
					$TokenArray = $this->deshboard->checkExistenceTokenexpert($dbeeid);
                
					if ($TokenArray['token'] != '')
						$token = $TokenArray['token'];
					else
						$token = time();

					$url = $this->shortUrl(BASE_URL.'/dbee/'.$dburl.'?token='.$token.'&oathtoken='. $value.'&type=dbee');

					
					$url = "<a href = '".$this->shortUrl(BASE_URL."/dbee/".$dburl."?sptoken=".$token."&spauthid=".$value)."' >click here</a>";

					$message = "I have invited you as an expert on my post Please ".$url." to join the conversation and respond to invitation.";

					$chaninparent_obj = $this->socialInvite->checkchainparent($fromUser,$value);
					if($chaninparent_obj['ID']) {			
						$Chainparent=$chaninparent_obj['ID'];
					} else $Chainparent=0;
					
					$data2 = array(
						'MessageTo'      => $value,
						'MessageFrom' => $fromUser,
						'Message'      => $message,
						'MessageDate'      => $Startdate,
						'Unread' => '1',
						'Archive'      => '0',
						'ArchivedBy'      => '',
						'ChainParent' => $Chainparent,						
						'clientID' => clientID						
					);
					
					$success = $this->socialInvite->insertMessage($data2);
				}
				else if($db_url['eventtype']==2 && $type=='attendies')
				{
					$token = time();
					$url = "<a href = '".$this->shortUrl(BASE_URL."/dbee/".$dburl."?sptoken=".$token."&spauthid=".$value)."' >click here</a>";
					$message = "I have invited you to participate in a video event on ".SITE_NAME.". Please ".$url." to respond.";
					$chaninparent_obj = $this->socialInvite->checkchainparent($fromUser,$value);
					if($chaninparent_obj['ID']) {			
						$Chainparent=$chaninparent_obj['ID'];
					} else $Chainparent=0;
					

					$data2 = array(
						'MessageTo'      => $value,
						'MessageFrom' => $fromUser,
						'Message'      => $message,
						'MessageDate'      => $Startdate,
						'Unread' => '1',
						'Archive'      => '0',
						'ArchivedBy'      => '',
						'ChainParent' => $Chainparent,						
						'clientID' => clientID						
					);
					
					$success = $this->socialInvite->insertMessage($data2);
					
					$dataArray['dbeeid'] = $dbeeid;
					$dataArray['socialid'] = $value;
					$dataArray['type'] = 'dbee';
					$dataArray['token'] = $token;
					$dataArray['date'] = date('Y-m-d H:i:s');
					$dataArray['clientID'] = clientID;

					$this->socialInvite->insertSocialInvitation($dataArray);

					$this->socialInvite->commomInsert('10','',$success,$fromUser,$value);
					
					$ResultUsers = $this->user_model->getUserByUserIDrow($value);				
					$db_url = $this->socialInvite->getdburltitle($dbeeid);
					$this->sendMail($ResultUsers,$url);
					
				}else if($type=='attendies')
				{

					$Chainparent=0;

					$url = "<a href = '".$this->shortUrl(BASE_URL."/dbee/".$dburl)."' >click here</a>";
					
					$message = "I have invited you to participate in a video event on ".SITE_NAME.". Please ".$url." to respond.";
					
					$data2 = array(
						'MessageTo'      => $value,
						'MessageFrom' => $fromUser,
						'Message'      => $message,
						'MessageDate'      => $Startdate,
						'Unread' => '1',
						'Archive'      => '0',
						'ArchivedBy'      => '',
						'ChainParent' => $Chainparent,
						'clientID' => clientID					
					);
					$success = $this->socialInvite->insertMessage($data2);

					$dataArray['dbeeid'] = $dbeeid;
					$dataArray['socialid'] = $value;
					$dataArray['type'] = 'dbee';
					$dataArray['token'] = '';
					$dataArray['date'] = date('Y-m-d H:i:s');
					$dataArray['clientID'] = clientID;
					$this->socialInvite->insertSocialInvitation($dataArray);
					$this->socialInvite->commomInsert('10','',$success,$fromUser,$value);
				}
				else if($type=='event')
				{
					$eventid = (int)$this->_request->getPost('dbeeID');
					$where = array('act_typeId'=>$eventid,'act_ownerid'=>$this->_userid,'act_type'=>36,'act_message'=>39);
					$result = $this->myclientdetails->getAllMasterfromtable('tblactivity',array('act_typeId'),$where);
					if(empty($result[0]))
						$this->socialInvite->commomInsert(36,39,$eventid,$fromUser,$value);
					else
					{	
						$where = array('act_typeId'=>$eventid,'act_ownerid'=>$this->_userid,'act_type'=>36,'act_message'=>39);
                		$this->myclientdetails->deleteMaster('tblactivity',$where);
						$this->socialInvite->commomInsert(36,39,$eventid,$fromUser,$value);
					}
				}
			}
			 $data['status'] = 'success';
			 $data['message'] = "Invitation sent successfully";
		}
		else
		{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));
	}

   public function cnacelinviteAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$filter = new Zend_Filter_StripTags();
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			
			
			$content = '';
			$DbeeID = $this->_request->getPost('dbid');
			

			 if ($DbeeID!="")   
			 {
			        $where = array('act_typeId'=>$DbeeID,'act_type'=>39,'act_message'=>50);
                	$this->myclientdetails->deleteMaster('tblactivity',$where);
			 }
			 $where2 = array('dbid'=>$DbeeID,'status'=>1);
             $result2 = $this->myclientdetails->getAllMasterfromtable('tblexpert',array('userid'),$where2);

			 $data['status'] = 'success';
			 $data['content'] = count($result2);
			 //$data['post_title'] = $db_url['VidDesc'];
		}
		else
		{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));
	}




	public function sendmailaccept($data,$dburls)
	{
		/****for email ****/      
		$EmailTemplateArray = array('uEmail'  => $data->Email,
                                    'uName'   => $data->Name,
                                    'lname'   => $data->lname,
                                    'db_url'  => $dburls,
                                    'case'      => 5);
        $this->dbeeComparetemplateOne($EmailTemplateArray); 
		/****for email ****/		
	}
	
	public function updatejoinrequserAction()
	{	 
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$ateeenuserstring = $this->_request->getPost('ateeenuserstring');
			$DbeeID = (int)$this->_request->getPost('dbeeID');
			$act_id = $this->_request->getPost('act_id');
			$data1 = array('status'=>1);
			$dashboard_obj = new Admin_Model_Deshboard();
			$db_url = $dashboard_obj->getdbeedetailbyid($DbeeID);
			$dburls =  '<a href="'.BASE_URL.'/dbee/'.$db_url['dburl'].'">'.$db_url['Text'].'</a>';
			if($act_id)
			{
				$where = array('act_id'=>$act_id);
                $this->myclientdetails->deleteMaster('tblactivity',$where);
			}
			if(is_array($ateeenuserstring))
			{	 
				foreach ($ateeenuserstring as $value)
				{ 
					$userdetail = $this->user_model->getUserByUserIDrow($value);	
					$dashboard_obj->updatejoinrequser($data1,$value,$DbeeID);
					$this->sendmailaccept($userdetail,$dburls);
					$where = array('act_type'=>17,'act_message'=>26,'act_userId'=>$value,'act_typeId'=>$DbeeID);
                	$this->myclientdetails->deleteMaster('tblactivity',$where);	
                	$this->socialInvite->commomInsert(40,46,$DbeeID,$this->_userid,$value);	
                	unset($where);
				}
			}
			else
			{
				$userdetail = $this->user_model->getUserByUserIDrow($ateeenuserstring);
				$dashboard_obj->updatejoinrequser($data1,$ateeenuserstring,$DbeeID);
				$this->sendmailaccept($userdetail,$dburls);
				$this->socialInvite->commomInsert(40,46,$DbeeID,$this->_userid,$ateeenuserstring);
				$where = array('act_type'=>17,'act_message'=>26,'act_userId'=>$ateeenuserstring,'act_typeId'=>$DbeeID);
                $this->myclientdetails->deleteMaster('tblactivity',$where);
                unset($where);		
			}
			
			$data['status'] = 'success';
			$data['message'] = 'Request accepted successfully';
		}else{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));
		 
	}

	public function rejectvideoeventAction()
	{	 
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$act_id = (int)$this->_request->getPost('act_id');
			$dbid = (int)$this->_request->getPost('dbid');
			$userid = (int)$this->_request->getPost('userid');

			$where = array('act_id'=>$act_id);
            $this->myclientdetails->deleteMaster('tblactivity',$where); 

            unset($where);

            $where = array('dbeeID'=>$dbid,'status'=>0,'userID'=>$userid);
            $this->myclientdetails->deleteMaster('tblDbeeJoinedUser',$where);

            $this->socialInvite->commomInsert(40,55,$dbid,$this->_userid,$userid);

			$data['status'] = 'success';
			$data['message'] = 'Request rejected successfully';
		}else{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));
		 
	}

	 public function socialsearchAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);    
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            $profHTML = '';
            $type = $this->_request->getPost('type');
            $callingfor = $this->_request->getPost('callingfor'); //inviteToGroup
            $target = strtolower($this->_request->getPost('keywords'));
            $lockclass = '';
            switch ($type) 
            {
                case 'linkedin':
                    $config['base_url']        = BASE_URL;
                    $config['callback_url']    = BASE_URL . '/social/callback';
                    $config['linkedin_access'] = linkedinAppid;
                    $config['linkedin_secret'] = linkedinSecret;
                    $config['oauth_callback'] = BASE_URL;
                    $linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], '');
                    $linkedin_connect_data = Zend_Json::decode($this->session_data['linkedin_connect_data']);
                    $linkedin->request_token             = unserialize($linkedin_connect_data['requestToken']);
                    $linkedin->oauth_verifier            = $linkedin_connect_data['oauth_verifier'];
                    $linkedin->access_token              = unserialize($linkedin_connect_data['oauth_access_token']);
                    $search_response = $linkedin->connections("?start=0&count=2000&format=json");
                    $result = json_decode($search_response);
                    $defaultMessage = '<div class="notfoundSocial" >No connections found.</div>';
                    foreach ($result->values as $key => $value) 
                    {
                        if (strpos(strtolower($value->firstName.' '.$value->lastName), $target) !== false) 
                        {
                            $firstName = $value->firstName;
                            $lastName  = $value->lastName;
                            $picUrl    = $value->pictureUrl;
                            if($picUrl=='') 
                                $picUrl = '/userpics/default-avatar.jpg';
                            	$tip = '';
	                            if($callingfor=='socialinvite')
	                            {
	                                $where['socialid'] = $value->id;
	                            	$resultSocial = $this->myclientdetails->getAllMasterfromtable('tblSocialinvitation',array('socialid'),$where,'');
	                                if(!empty($resultSocial[0]))
	                                    $tip = 'rel="dbTip" title="Invited"';
	                            }
	                            $profHTML .= "<div class='userFatchList boxFlowers' ".$tip.">
								<label class='labelCheckbox'><input type='checkbox' value='" . $value->id . "@".$firstName."@".$picUrl."' class='inviteuser-search' name='socialUser'>
								<div class='follower-box'>
								<div class='usImg'><img  width='48' height='48' src='" .$picUrl."'> </div>
									<div class='userDetailsBx'>". $firstName . " " . $lastName."</div>
								</div></label></div></div>";
                            $defaultMessage = '';
                            $tip = '';
                            $data['userCount']  = true;
                        }
                    }
                    $profHTML .=$defaultMessage;
                    $data['status']  = 'success';
                    
                    break;
                case 'twitter':
                    $defaultMessage = '<div class="notfoundSocial" >No followers found.</div>';
                    $twitteroauth       = new TwitterOAuth(twitterAppid, twitterSecret, 
                    $this->twitter_connect_data['twitter_access_token'], 
                    $this->twitter_connect_data['twitter_token_secret']);

                    $rateLimit  = $twitteroauth->get('application/rate_limit_status', array());
                    $rateLimit = json_decode(json_encode($rateLimit), true);
                    if($rateLimit['resources']['friendships']['/friendships/lookup']['remaining']==0)
                    {
                        $data['twitter']  = 'error';
                        $data['time']  = time();
                        $data['reset'] = $rateLimit['resources']['friendships']['/friendships/lookup']['reset'];
                        $data['diff']  = $data['reset']-$data['time'];
                        return $response->setBody(Zend_Json::encode($data));
                    }


                    $result  = $twitteroauth->get('friendships/lookup', array(
                        'screen_name' => $this->_request->getPost('keywords')
                    )); 

                   // print_r($result); die;
                    if(!empty($result[0]) && !empty($result) && in_array('followed_by',$result[0]->connections) && $result[0]->connections!=null)
                    {
                            $resultTwitter  = $twitteroauth->get('users/lookup', array(
                                'screen_name' => $result[0]->screen_name
                            ));
                           	$tip = '';
                            if($callingfor=='socialinvite')
                            {
                                $where['socialid'] = $resultTwitter[0]->id;
                            	$resultSocial = $this->myclientdetails->getAllMasterfromtable('tblSocialinvitation',array('socialid'),$where,'');
                                if(!empty($resultSocial[0]))
                                    $tip = 'rel="dbTip" title="Invited"';
                            }

                            $profHTML .= "<div class='userFatchList boxFlowers' ".$tip.">
							<label class='labelCheckbox'><input type='checkbox' value='" . $resultTwitter[0]->screen_name . "@" . $resultTwitter[0]->id . "@".$resultTwitter[0]->profile_image_url_https."' class='inviteuser-search' name='socialUser'>
							<div class='follower-box'>
							<div class='usImg'><img  width='48' height='48' src='" . $resultTwitter[0]->profile_image_url_https . "'> </div>
								<div class='userDetailsBx'>". $resultTwitter[0]->name."</div>
							</div></label></div></div>";
                            $defaultMessage = '';
                            $tip = '';
                            $data['userCount']  = true;
                    }
                    $profHTML .= $defaultMessage;
                    $data['status']  = 'success';

                    break;
                case 'facebook':
                    $defaultMessage = '<div class="notfoundSocial" >No friends found.</div>';
                    $params = array('appId' => facebookAppid,'secret' => facebookSecret,
                    'domain' => facebookDomain);
                    $facebook = new Facebook($params);
                    $getAccessToken = $this->facebook_connect_data['access_token'];
                    try 
                    { 
                        $friends = $facebook->api('/'.$this->facebook_connect_data['facebookid'].'/friends?access_token='.$getAccessToken);
                        foreach ($friends["data"] as $value) 
                        {
                            if (strpos(strtolower($value['name']), $target) !== false) 
                            {
	                            $tip = '';
	                            if($callingfor=='socialinvite')
	                            {
	                                $where['socialid'] = $value["id"];
	                            	$resultSocial = $this->myclientdetails->getAllMasterfromtable('tblSocialinvitation',array('socialid'),$where,'');
	                                if(!empty($resultSocial[0]))
	                                    $tip = 'rel="dbTip" title="Invited"';
	                            }
	                            $profHTML .= "<div class='userFatchList boxFlowers' ".$tip.">
								<label class='labelCheckbox'><input type='checkbox' value='" . $value["id"]."@".$value['name']. "' class='inviteuser-search' name='socialUser'>
								<div class='follower-box'>
								<div class='usImg'><img  width='48' height='48' src='https://graph.facebook.com/" . $value["id"] . "/picture'> </div>
									<div class='userDetailsBx'>". $value['name']."</div>
								</div></label></div></div>";
                                $defaultMessage = '';
                                $tip = '';
                                $data['userCount']  = true;
                            }
                        }
                        $profHTML .=$defaultMessage;
                        $data['UserData'] = $profHTML;
                        $data['status']  = 'success';
                    } 
                    catch(FacebookApiException $e) 
                    {
                        $data['status']  = 'error';
                        $data['message'] = 'Some thing went wrong here please try again';
                    }  
                    break;
                   case 'dbee':
                    $defaultMessage = '<div class="notfoundSocial" >No users found.</div>';
                    $query = $this->myclientdetails->customEncoding($target,$search="true");
                    $variable = $this->user_model->SearchMember($query,'',10);
                    foreach ($variable as $key => $value) 
                    { 
                        $extratext='' ;
                        if($value['usertype']==100)
                        {
                        	$extratext='<span style="color:#bd362f; font-weight:bold; margin-top: 5px; display: inline-block;">(VIP)</span>' ;
                        }
                        if($value['usertype']==100 && $value['hideuser']==1)
                        {
                        	$extratext='<span style="color:#bd362f; font-weight:bold; margin-top: 5px; display: inline-block;">(Anonymous VIP)</span>' ;
                        }


                         $profHTML .= "<div class='userFatchList boxFlowers' ".$tip.">
							<label class='labelCheckbox'><input type='checkbox' value='" . $value['UserID']. "' class='inviteuser-search' name='socialUser'>
							<div class='follower-box'>
							<div class='usImg'><img  width='48' height='48' src='".IMGPATH."/users/small/".$value['ProfilePic']."' border='0'> </div>
								<div class='userDetailsBx'>". $this->myclientdetails->customDecoding($value['Name']).' '.$this->myclientdetails->customDecoding($value['lname'])."<br><div class='oneline ' style='color:#fc9908'> @".$this->myclientdetails->customDecoding($value['Username'])."</div>".$extratext."</div>
							</div></label></div></div>";
                    }
                    $defaultMessage = '';
                    $data['userCount']  = true;
                    $profHTML .= $defaultMessage;
                    $data['status']  = 'success';
                    break;
                    case 'InviteExpert':
                    $defaultMessage = '<div class="notfoundSocial" >No users found.</div>';
                    $query = $this->myclientdetails->customEncoding($target,$search="true");
                    $variable = $this->user_model->SearchMember($query,'',10);
                    foreach ($variable as $key => $value) 
                    { 
                         $profHTML .= "<div class='userFatchList boxFlowers' ".$tip.">
							<label class='labelCheckbox'><input type='checkbox' value='" . $value['UserID']. "' class='inviteuser-search' name='socialUser'>
							<div class='follower-box'>
							<div class='usImg'><img  width='48' height='48' src='".IMGPATH."/users/small/".$value['ProfilePic']."' border='0'> </div>
								<div class='userDetailsBx'>". $this->myclientdetails->customDecoding($value['Name']).' '.$this->myclientdetails->customDecoding($value['lname'])."</div>
							</div></label></div></div>";
                    }
                    $defaultMessage = '';
                    $data['userCount']  = true;
                    $profHTML .= $defaultMessage;
                    $data['status']  = 'success';
                    break;
                default:
                    $data['status']  = 'error';
                    $data['message'] = 'Some thing went wrong here please try again';
                break;
            }
            
            $data['UserData'] = $profHTML;
            $data['type']  = $type;
        }
        
        return $response->setBody(Zend_Json::encode($data));
    }

    public function socialinviteAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);    
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            $profHTML = '';
            $type = $this->_request->getPost('shareType');
            $callingfor = $this->_request->getPost('callingfor');
            $socialUser = explode(',',$this->_request->getPost('socialUser'));
            $uniqueIDSocial = (int)$this->_request->getPost('uniqueIDSocial');
            switch ($type) 
            {
                case 'linkedin':
                    $config['base_url']        = BASE_URL;
                    $config['callback_url']    = BASE_URL . '/social/callback';
                    $config['linkedin_access'] = linkedinAppid;
                    $config['linkedin_secret'] = linkedinSecret;
                    $config['oauth_callback'] = BASE_URL;
                    $linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], '');
                    $linkedin_connect_data = Zend_Json::decode($this->session_data['linkedin_connect_data']);
                    $linkedin->request_token             = unserialize($linkedin_connect_data['requestToken']);
                    $linkedin->oauth_verifier            = $linkedin_connect_data['oauth_verifier'];
                    $linkedin->access_token              = unserialize($linkedin_connect_data['oauth_access_token']);
                    if($callingfor=='attendies')
                      $respo = $this->linkedinInvitationAttendies($linkedin,$socialUser,$uniqueIDSocial);
                    else if($callingfor=='expert')
                      $respo = $this->linkedinExpertInvitation($linkedin,$socialUser,$uniqueIDSocial);
                    else if($callingfor=='event')
                      $respo = $this->linkedinInvitationEvent($linkedin,$socialUser,$uniqueIDSocial);
                 	else if($callingfor=='socialinvite')
                      $respo = $this->linkedinInvitationSocialInvite($linkedin,$socialUser);
                    break;
                case 'twitter':
                    $twitteroauth       = new TwitterOAuth(twitterAppid, twitterSecret, 
                    $this->twitter_connect_data['twitter_access_token'], 
                    $this->twitter_connect_data['twitter_token_secret']);
                    if($callingfor=='attendies')
                      $respo = $this->twitterInvitationAttendies($twitteroauth,$socialUser,$uniqueIDSocial);
                    else if($callingfor=='expert')
                      $respo = $this->twitterExpertInvitation($twitteroauth,$socialUser,$uniqueIDSocial);
                    else if($callingfor=='event')
                      $respo = $this->twitterInvitationEvent($twitteroauth,$socialUser,$uniqueIDSocial);
                   else if($callingfor=='socialinvite')
                      $respo = $this->twitterInvitationSocialInvite($twitteroauth,$socialUser);
                    break;
                case 'facebook':
                    $params = array('appId' => facebookAppid,'secret' => facebookSecret,
                    'domain' => facebookDomain);
                    $facebook = new Facebook($params);
                    if($callingfor=='attendies')
                      $respo = $this->facebookInvitationAttendies($facebook,$socialUser,$uniqueIDSocial);
                    else if($callingfor=='expert')
                      $respo = $this->facebookExpertInvitation($facebook,$socialUser,$uniqueIDSocial);
                  else if($callingfor=='event')
                      $respo = $this->facebookInvitationEvent($facebook,$socialUser,$uniqueIDSocial);
                    else if($callingfor=='socialinvite')
                      $respo = $this->facebookInvitationSocialInvite($facebook,$socialUser);
                    break;
                 case 'dbee':
                 	$respo = $this->dbeeInvitation($callingfor,$socialUser,$uniqueIDSocial);
                    break;
                 case 'InviteExpert':
                 	$respo = $this->dbeeInvitationToExpert($callingfor,$socialUser,$uniqueIDSocial);
                 	$common = new Admin_Model_Common();
	                $where3 = array('UserID'=>$socialUser);
	                $result3 = $this->myclientdetails->getRowMasterfromtable('tblUsers',array('full_name','ProfilePic'),$where3); 
	                $full_name=$this->myclientdetails->customDecoding($result3['full_name']);
	                $valuepic = $common->checkImgExist($result3['ProfilePic'],'userpics','default-avatar.jpg');
                    break;
                  	
            }
	           if($respo==false)
	           {
	                $data['status']  = 'error';
	           }
	           else
	           {
	            $data['status']  = 'success';
	            $data['type']  = $type;
	            
	            if($type=='InviteExpert')
	            {
	            	$data['content']='<a href="javascript:void(0);" data-id="'.$uniqueIDSocial.'" data-name="'.$full_name.'" id="ViewInvite'.$liveDbee['DbeeID'].'" data-pic="'.$valuepic.'" class="btn btn-mini btn-yellow ViewInvite">View/Cancel Invite</a>';
	       		 }
               }
        }
        
        return $response->setBody(Zend_Json::encode($data));
    }

    public function dbeeInvitation($callingfor,$socialUser,$id)
    {
       foreach ($socialUser as $value) 
       {
           if($callingfor=='expert')
           {
	           $where = array('act_typeId'=>$id,'act_ownerid'=>$value,'act_type'=>39,'act_message'=>44);
	           $this->myclientdetails->deleteMaster('tblactivity',$where);
	           $this->socialInvite->commomInsert(39,44,$id,$this->_userid,$value,'','');
       	   }
       	   else if($callingfor=='attendies')
       	   {
       	   	   $where = array('act_typeId'=>$id,'act_ownerid'=>$value,'act_type'=>41,'act_message'=>47);
	           $this->myclientdetails->deleteMaster('tblactivity',$where);
	           $this->socialInvite->commomInsert(41,47,$id,$this->_userid,$value,'','');
       	   }
       	   else if($callingfor=='event')
       	   {
       	   	   $where = array('act_typeId'=>$id,'act_ownerid'=>$value,'act_type'=>36,'act_message'=>39);
	           $this->myclientdetails->deleteMaster('tblactivity',$where);
	           $this->socialInvite->commomInsert(36,39,$id,$this->_userid,$value,'','');
       	   }
       	   return true; 
       }
    }

    public function dbeeInvitationToExpert($callingfor,$socialUser,$id)
    {
       foreach ($socialUser as $value) 
       {
          
       	   if($callingfor=='ForExpert')
       	   {
       	   	    $where = array('act_typeId'=>$id,'act_ownerid'=>$value,'act_type'=>39,'act_message'=>50);
	            $this->myclientdetails->deleteMaster('tblactivity',$where);
	            $this->socialInvite->commomInsert(39,50,$id,$this->_userid,$value,'','');

	            /*$dataArray['userid']      = $value;
				$dataArray['dbid']        = $id;
				$dataArray['status'] 	  = 1;				
				$dataArray['currentdate'] = date('Y-m-d H:i:s');
				$dataArray['clientID']    = clientID;
				$this->myclientdetails->insertdata_global('tblexpert',$dataArray);*/
       	   }
       	   
       	   return true; 
       }
    }
    public function checkexistexpertAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            $dbeeid = (int) $this->_request->getPost('dbid');
            $requestInvitation = $this->deshboard->checkinviteexpert($dbeeid);
            $requestDbeeInvitation = $this->deshboard->checkdbeeinviteexpert($dbeeid);
            if(!empty($requestInvitation))
            {
                $data['userType'] = 'socialUser';
                $data['socialType'] = $requestInvitation['type'];
                $data['socialType'] = $requestInvitation['type'];
                $data['socialid'] = $requestInvitation['socialid'];
                $data['userName'] = $requestInvitation['name'];
                $data['userPhoto'] = $requestInvitation['photo'];
                $data['status'] = 'success';
            }
            else if(!empty($requestDbeeInvitation))
            {
                $userData = $this->user_model->getUserByUserID($requestDbeeInvitation['act_ownerid']);
                $data['socialType'] = 'Site User';
                $data['userName'] = $this->myclientdetails->customDecoding($userData[0]['full_name']);
                $data['userPhoto'] = IMGPATH.'/users/small/'.$userData[0]['ProfilePic'].'';
                $data['status'] = 'success';
            }
        }
        else 
        {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));

    }

    public function cancelpendingAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            $dbeeid = (int) $this->_request->getPost('dbid');
            $this->deshboard->deleteExpertInvitation($dbeeid);
            $where = array('act_typeId'=>$dbeeid,'act_type'=>39,'act_message'=>44);
            $this->myclientdetails->deleteMaster('tblactivity',$where);
        }
        else 
        {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));

    }

    public function facebookExpertInvitation($facebook,$socialUser,$dbeeid)
    {
        $dbee_data = $this->deshboard->getDbeeDetails($dbeeid);
        $token = time();
        if(!empty($dbee_data))
        {
            try 
            {
               $socialArray = explode('@', $socialUser[0]);
               $privacy = array(
                'value'   => 'CUSTOM',
                'friends' => 'SOME_FRIENDS',
                'allow'   => $socialArray[0]
                );

                if($this->SocialLogo!='')
                    $image = BASE_URL.'/img/'.$this->SocialLogo;
                else
                    $image = BASE_URL.'/img/logo.png';

                $url = BASE_URL . '/dbee/' . $dbee_data['dburl'] . '?type=facebook&token=' . $token . '&oathtoken=' . $socialArray[0];

                $result = $facebook->api('/'.$this->facebook_connect_data['facebookid'].'/feed', 'POST', array(
                  'from' => array(
                  'name' => $this->facebook_connect_data['facebookname'],
                  'id' => $this->facebook_connect_data['facebookid']
                ),
                'link'    => $url,
                'picture' => $image,
                'description' => $this->getSeoConfiguration->SiteDescription,
                'privacy' => json_encode($privacy),
                'message' => "Hi " . $socialArray[1] . ", ".str_replace('[EXPERT_TEXT]', $this->expertText, $this->getSocialShareMsg['expert_share']),
                'access_token' => $this->facebook_connect_data['access_token'],
                'tags'=> $socialArray[0],
                'place'=>'155021662189'
                ));


                $data = array(
                    'dbeeid' => $dbeeid,
                    'token' => $token,
                    'name' => $socialArray[1],
                    'socialid' => $socialArray[0],
                    'type' => 'facebook',
                    'currenttime' => date('Y-m-d H:i:s'),
                    'clientID' => clientID
                );
                $this->socialInvite->insertininviteexport($data);
                return true; 
            } catch (Exception $e) {
                return false;
            }
        }
    }

    public function twitterExpertInvitation($twitteroauth,$socialUser,$dbeeid)
    {
        $dbee_data = $this->deshboard->getDbeeDetails($dbeeid);
        $token = time();
        if(!empty($dbee_data))
        {
            $socialArray = explode('@', $socialUser[0]);
            $url = BASE_URL . '/dbee/' . $dbee_data['dburl'] . '?type=twitter&token=' . $token . '&oathtoken=' . $socialArray[1];
            $url = $this->shortUrl($url);
            $twitteroauth->post('statuses/update', array('status' => '@'.$socialArray[0]." ".str_replace('[EXPERT_TEXT]', $this->expertText, $this->getSocialShareMsg['expert_share'])." ".$url));
            $data = array
            (
                'dbeeid' => $dbeeid,
                'token' => $token,
                'name' => $socialArray[0],
                'socialid' => $socialArray[1],
                'photo' => $socialArray[2],
                'type' => 'twitter',
                'currenttime' => date('Y-m-d H:i:s'),
                'clientID' => clientID
            );
            $this->socialInvite->insertininviteexport($data);
            return true; 
        }
    }

    public function linkedinExpertInvitation($linkedin,$socialUser,$dbeeid)
    {
        $dbee_data = $this->deshboard->getDbeeDetails($dbeeid);
        $token = $this->generateHash(time());
        if(!empty($dbee_data))
        {
            $socialArray = explode('@', $socialUser[0]);
            $url = BASE_URL . '/dbee/' . $dbee_data['dburl'] . '?type=linkedin&token=' . $token . '&oathtoken=' . $socialArray[0];
            $url = $this->shortUrl($url);
            $body = "Hey ".$socialArray[1].' '.str_replace('[EXPERT_TEXT]', $this->expertText, $this->getSocialShareMsg['expert_share']).$url;
            $subject = "Invitation to join this cool website";
            $linkedin->sendMessageById($socialArray[0], FALSE, $subject, $body);
            $data = array(
                'dbeeid' => $dbeeid,
                'token' => $token,
                'name' => $socialArray[1],
                'socialid' => $socialArray[0],
                'photo' => $socialArray[2],
                'type' => 'linkedin',
                'currenttime' => date('Y-m-d H:i:s'),
                'clientID' => clientID
            );
            $this->socialInvite->insertininviteexport($data);
            return true; 
        }
    }
    public function facebookInvitationAttendies($facebook,$socialUser,$dbeeid)
    {
        $dbee_data = $this->deshboard->getDbeeDetails($dbeeid);

       // print_r($dbee_data); die;
        if(!empty($dbee_data))
        {
            try 
            {
               foreach ($socialUser as $value) 
               {
	               $socialArray = explode('@', $value);
	               $token = time();
	               $privacy = array(
	                'value'   => 'CUSTOM',
	                'friends' => 'SOME_FRIENDS',
	                'allow'   => $socialArray[0]
	                );
	                if($this->SocialLogo!='')
	                    $image = BASE_URL.'/img/'.$this->SocialLogo;
	                else
	                    $image = BASE_URL.'/img/logo.png';

	                if($dbee_data['eventtype']==2)
						$url = BASE_URL.'/dbee/'.$dbee_data['dburl'].'?sptype=facebook&spauthid='.$socialArray[0].'&sptoken='.$token;
					else
						$url = BASE_URL.'/dbee/'.$dbee_data['dburl'];

	                $result = $facebook->api('/'.$this->facebook_connect_data['facebookid'].'/feed', 'POST', array(
	                  'from' => array(
	                  'name' => $this->facebook_connect_data['facebookname'],
	                  'id' => $this->facebook_connect_data['facebookid']
	                ),
	                'link'    => $url,
	                'picture' => $image,
	                'description' => $this->getSeoConfiguration->SiteDescription,
	                'privacy' => json_encode($privacy),
	                'message' => "Hi ".$socialArray[1].", I've invited you to participate in a video event on ".SITE_NAME.". Click the link below to join in.",
	                'access_token' => $this->facebook_connect_data['access_token'],
	                'tags'=> $socialArray[0],
	                'place'=>'155021662189'
	                ));

	                if($dbee_data['eventtype']==2)
	                {		               
		                $dataArray['dbeeid'] = $dbeeid;
		                $dataArray['socialid'] = $socialArray[0];
		                $dataArray['type'] = 'facebook';
		                $dataArray['token'] = $token;
		                $dataArray['date'] = date('Y-m-d H:i:s');
		                $dataArray['clientID'] = clientID;
		                $this->socialInvite->insertSocialInvitation($dataArray);
	            	}
            	}
                return true; 
            } catch (Exception $e) {
                return false;
            }
        }
    }

    public function twitterInvitationAttendies($twitteroauth,$socialUser,$dbeeid)
    {
        $dbee_data = $this->deshboard->getDbeeDetails($dbeeid);
        if(!empty($dbee_data))
        {
            $socialArray = explode('@', $socialUser[0]);
            $token = time();
            
            if($dbee_data['eventtype']==2)
				$url = $this->shortUrl(BASE_URL.'/dbee/'.$dbee_data['dburl'].'?sptype=twitter&sptoken='.$token.'&spauthid='.$socialArray[1]);
			else
				$url = $this->shortUrl(BASE_URL.'/dbee/'.$dbee_data['dburl']);

            //$twitteroauth->post('statuses/update', array('status' => '@'.$socialArray[0]." ".str_replace('[EXPERT_TEXT]', $this->expertText, $this->getSocialShareMsg['expert_share'])." ".$url));
            $twitteroauth->post('statuses/update', array('status' => '@'.$socialArray[0]." I've invited you to participate in a video event on ".SITE_NAME.". Click the link below to join in. ".$url));
            
         	if($dbee_data['eventtype']==2)
            {
                $dataArray['dbeeid'] = $dbeeid;
                $dataArray['socialid'] = $socialArray[1];
                $dataArray['type'] = 'twitter';
                $dataArray['token'] = $token;
                $dataArray['date'] = date('Y-m-d H:i:s');
                $dataArray['clientID'] = clientID;
                $this->socialInvite->insertSocialInvitation($dataArray);
        	}
            return true; 
        }
    }

    public function linkedinInvitationAttendies($linkedin,$socialUser,$dbeeid)
    {
        $dbee_data = $this->deshboard->getDbeeDetails($dbeeid);
        if(!empty($dbee_data))
        {
        	foreach ($socialUser as $value) 
            {
	            $socialArray = explode('@', $value);
	            $token = $this->generateHash(time());
	            
	            if($dbee_data['eventtype']==2)
					$url = $this->shortUrl(BASE_URL.'/dbee/'.$dbee_data['dburl'].'?sptoken='.$token.'&spauthid='.$socialArray[0].'&sptype=twitter');
				else
					$url = $this->shortUrl(BASE_URL.'/dbee/'.$dbee_data['dburl']);

	            $body = "Hey ".$socialArray[1].' '.str_replace('[EXPERT_TEXT]', $this->expertText, $this->getSocialShareMsg['expert_share']).$url;

	            $subject = "Video event invitation";

	            $linkedin->sendMessageById($socialArray[0], FALSE, $subject, $body);

	            if($dbee_data['eventtype']==2)
                {
	                $dataArray['dbeeid'] = $dbeeid;
	                $dataArray['socialid'] = $socialArray[0];
	                $dataArray['type'] = 'linkedin';
	                $dataArray['token'] = $token;
	                $dataArray['date'] = date('Y-m-d H:i:s');
	                $dataArray['clientID'] = clientID;
	                $this->socialInvite->insertSocialInvitation($dataArray);
            	}
        	}
            return true; 
        }
    }

    public function facebookInvitationEvent($facebook,$socialUser,$id)
    {
        $event_model = new Admin_Model_Event();
		$event_result = $event_model->getEventRecord($id);
        if(!empty($event_result))
        {
            try 
            {
               foreach ($socialUser as $value) 
               {
	               $socialArray = explode('@', $value);
	               
	               $token = time();
	               
	               $privacy = array(
	                'value'   => 'CUSTOM',
	                'friends' => 'SOME_FRIENDS',
	                'allow'   => $socialArray[0]
	                );

	                if($this->SocialLogo!='')
	                    $image = BASE_URL.'/img/'.$this->SocialLogo;
	                else
	                    $image = BASE_URL.'/img/logo.png';

	                $url = $this->shortUrl(BASE_URL."/event/invite/id/".$id.'/token/'.$token.'/authid/'.$socialArray[0].'/type/facebook');

	                $message = "Hi ".$socialArray[1].", ".$this->getSocialShareMsg['event_share'];
                    $message = str_replace("[PLATFORM_NAME]", SITE_NAME,$message);

	                $result = $facebook->api('/'.$this->facebook_connect_data['facebookid'].'/feed', 'POST', array(
	                  'from' => array(
	                  'name' => $this->facebook_connect_data['facebookname'],
	                  'id' => $this->facebook_connect_data['facebookid']
	                ),
	                'link'    => $url,
	                'picture' => $image,
	                'description' => 'Event invitation',
	                'privacy' => json_encode($privacy),
	                'message' => $message,
	                'access_token' => $this->facebook_connect_data['access_token'],
	                'tags'=> $socialArray[0],
	                'place'=>'155021662189'
	                ));
	                $dataArray['eventid'] = $id;
					$dataArray['socialid'] = $socialArray[0];
					$dataArray['type'] = 'facebook';
					$dataArray['token'] = $token;
					$dataArray['clientID'] = clientID;
					$dataArray['currenttime'] = date('Y-m-d H:i:s');
					//print_r($dataArray); exit;
					$this->socialInvite->insertEventInvitation($dataArray);
            	}
                return true; 
            } catch (Exception $e) {
                return false;
            }
        }
    }

    public function twitterInvitationEvent($twitteroauth,$socialUser,$id)
    {
        $event_model = new Admin_Model_Event();
		$event_result = $event_model->getEventRecord($id);
        if(!empty($event_result))
        {
            $socialArray = explode('@', $socialUser[0]);
            $token = time();
            $url = $this->shortUrl(BASE_URL."/event/invite/id/".$id.'/token/'.$token.'/authid/'.$socialArray[1].'/type/twitter');
            $twitteroauth->post('statuses/update', array('status' => '@'.$socialArray[0]." ".str_replace('[PLATFORM_NAME]', SITE_NAME, $this->getSocialShareMsg['event_share'])." ".$url));
         	$dataArray['eventid'] = $id;
			$dataArray['socialid'] = $socialArray[1];
			$dataArray['type'] = 'twitter';
			$dataArray['token'] = $token;
			$dataArray['clientID'] = clientID;
			$dataArray['currenttime'] = date('Y-m-d H:i:s');
			$this->socialInvite->insertEventInvitation($dataArray);
            return true; 
        }
    }

    public function linkedinInvitationEvent($linkedin,$socialUser,$id)
    {
        $event_model = new Admin_Model_Event();
		$event_result = $event_model->getEventRecord($id);
        if(!empty($event_result))
        {
        	foreach ($socialUser as $value) 
            {
	            $socialArray = explode('@', $value);
	            $token = $this->generateHash(time());
	            $url = $this->shortUrl(BASE_URL."/event/invite/id/".$id.'/token/'.$token.'/authid/'.$socialArray[0].'/type/linkedin');

	            $body = "Hey ".$socialArray[1].' '.str_replace('[PLATFORM_NAME]', SITE_NAME, $this->getSocialShareMsg['event_share']).' '.$url;

	            $subject = "Video event invitation";
	            
	            $linkedin->sendMessageById($socialArray[0], FALSE, $subject, $body);

	            $dataArray['eventid'] = $id;
				$dataArray['socialid'] = $socialArray[0];
				$dataArray['type'] = 'linkedin';
				$dataArray['token'] = $token;
				$dataArray['clientID'] = clientID;
				$dataArray['currenttime'] = date('Y-m-d H:i:s');
				$this->socialInvite->insertEventInvitation($dataArray);
        	}
            return true; 
        }
    }

    public function facebookInvitationSocialInvite($facebook,$socialUser)
    {
        try 
        {
           foreach ($socialUser as $value) 
           {
               $socialArray = explode('@', $value);
               $socialV = $this->socialInvite->checkpreInviteSocial('facebook',$socialArray[0]);
               if(empty($socialV))
				{
                    $dataArray['socialid'] = $socialArray[0];
                    $dataArray['type'] = 'facebook';
                    $dataArray['name'] = $socialArray[1];
                    $dataArray['date'] = date('Y-m-d H:i:s');
                    $dataArray['clientID'] = clientID;
                    $this->myclientdetails->insertdata_global('tblSocialinvitation',$dataArray);
            	}
               	
               	$privacy = array(
                'value'   => 'CUSTOM',
                'friends' => 'SOME_FRIENDS',
                'allow'   => $socialArray[0]
                );

                if($this->SocialLogo!='')
                    $image = BASE_URL.'/img/'.$this->SocialLogo;
                else
                    $image = BASE_URL.'/img/logo.png';

                $url = $this->shortUrl(BASE_URL);

                $result = $facebook->api('/'.$this->facebook_connect_data['facebookid'].'/feed', 'POST', array(
                  'from' => array(
                  'name' => $this->facebook_connect_data['facebookname'],
                  'id' => $this->facebook_connect_data['facebookid']
                ),
                'link'    => BASE_URL,
                'picture' => $image,
                'description' => $this->getSeoConfiguration->SiteDescription,
                'privacy' => json_encode($privacy),
                'message' => "Hi " . $socialArray[1] . ", ".$this->getSocialShareMsg['plateform_share'],
                'access_token' => $this->facebook_connect_data['access_token'],
                'tags'=> $socialArray[0],
                'place'=>'155021662189'
                ));
        	}
            return true; 
        } catch (Exception $e) {
            return false;
        }
    }

    public function twitterInvitationSocialInvite($twitteroauth,$socialUser)
    {
        $socialArray = explode('@', $socialUser[0]);
        $socialV = $this->socialInvite->checkpreInviteSocial('twitter',$socialArray[1]);
        if(empty($socialV))
		{
			$dataArray['socialid'] = $socialArray[1];
			$dataArray['type'] = 'twitter';
			$dataArray['name'] = $socialArray[0];
			//echo $twitterA[1];
			$name = $this->image_save_from_url($socialArray[2],$_SERVER["DOCUMENT_ROOT"].'/adminraw/socialimage/'); 
			$dataArray['pic'] = $name;
			$dataArray['date'] = date('Y-m-d H:i:s');
			$dataArray['clientID'] = clientID;
			$this->myclientdetails->insertdata_global('tblSocialinvitation',$dataArray);
		}
        $url = $this->shortUrl(BASE_URL);
        $twitteroauth->post('statuses/update', array('status' => '@'.$socialArray[0]." ".str_replace('[PLATFORM_NAME]', SITE_NAME, $this->getSocialShareMsg['plateform_share'])." ".$url));
        return true; 
    }

    public function linkedinInvitationSocialInvite($linkedin,$socialUser)
    {
    	foreach ($socialUser as $value) 
        {
            $socialArray = explode('@', $value);
            $url = $this->shortUrl(BASE_URL);
            $body = "Hey ".$socialArray[1].' '.str_replace('[PLATFORM_NAME]', SITE_NAME, $this->getSocialShareMsg['plateform_share']).' '.$url;
            $subject = "Invitation to join this cool website";
            
			$socialV = $this->socialInvite->checkpreInviteSocial('linkedin',$socialArray[0]);
            if(empty($socialV))
			{
				$dataArray['socialid'] = $socialArray[0];
				$dataArray['type'] = 'linkedin';
				$dataArray['name'] = $socialArray[1];
				$name = $this->image_save_from_url($socialArray[2],$_SERVER["DOCUMENT_ROOT"].'/adminraw/socialimage/'); 
				$dataArray['pic'] = $name;
				$dataArray['date'] = date('Y-m-d H:i:s');
				$dataArray['clientID'] = clientID;
				$this->myclientdetails->insertdata_global('tblSocialinvitation',$dataArray);
            	$linkedin->sendMessageById($socialArray[0], FALSE, $subject, $body);
			}
    	}
        return true; 
    }
     public function generateHash($plainText, $salt = null)
    {
        define('SALT_LENGTH', 9);
        if ($salt === null)
            $salt = substr(md5(uniqid(rand(), true)), 0, SALT_LENGTH);
        else 
            $salt = substr($salt, 0, SALT_LENGTH);
        return $salt . sha1($salt . $plainText);
    }
}

