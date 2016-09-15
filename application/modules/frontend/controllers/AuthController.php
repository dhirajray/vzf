<?php
class AuthController extends IsloginController
{
	public function init()
	{
		parent::init();
		$request = $this->getRequest()->getParams();
		$this->_helper->layout->setLayout('index');
	}
	 
	public function twitterAction()
	{
		if($this->getGlobalSettings()=='block')
		{
			$this->_helper->flashMessenger->addMessage('block');
			$this->_redirect("/");
		}

		$TwitterOAuth = new TwitterOAuth(twitterAppid, twitterSecret);
		$request_token = $TwitterOAuth->getRequestToken(FRONT_URL.'/auth/callback');
		$this->user_session->oauth_token = $request_token['oauth_token'];
		$this->user_session->oauth_token_secret = $request_token['oauth_token_secret'];
		if ($TwitterOAuth->http_code == 200) {
			$url = $TwitterOAuth->getAuthorizeURL($request_token['oauth_token']);
			$this->_redirect($url);	
		} else
			$this->_helper->redirector->gotosimple('index','index',true);
	}


	public function callbackAction()
	{
		$oauth_verifier = $this->_request->getParam('oauth_verifier');
		$filter_alnum = new Zend_Filter_Alnum();
		if($_REQUEST['denied']!=''){
			$this->_redirect('/');
		}
		if (!empty($oauth_verifier) && !empty($this->user_session->oauth_token) && !empty($this->user_session->oauth_token_secret)) {
			
			$twitteroauth = new TwitterOAuth(twitterAppid,
					twitterSecret,
					$this->user_session->oauth_token,
					$this->user_session->oauth_token_secret);				
			$access_token = $twitteroauth->getAccessToken($oauth_verifier);
			// Save it in a session var
			$this->user_session->access_token = $access_token;
			// Let's get the user's info
			$user_info = $twitteroauth->get('account/verify_credentials');
			//print_r($user_info); exit;
			$this->user_session->screen_name = $user_info->screen_name;
			$this->user_session->screen_id = $user_info->id_str;
			$result_array = $this->user_model->getSocialDetail($user_info->id_str);
			$geoplugin = new geoPlugin();
			$geoplugin->locate($this->_request->getServer('REMOTE_ADDR'));

		    if($result_array[0]['Status']==0 && !empty($result_array))
		    {
		    	
		    	unset($this->user_session->access_token);
				$this->_helper->flashMessenger->addMessage('Oops, your account seems to be deactivated.');
				$this->_helper->redirector->gotosimple('index','index',true); exit();
		    }
			else if(isset($this->session_data['UserID']) && $this->session_data['UserID']!='')
			{
				
				if($this->user_session->redirectToProfile)
					$this->_redirect($this->user_session->redirectToExperturl."?invite=twitter");
				else if($this->user_session->redirectToShaurl)
					$this->_redirect($this->user_session->redirectToShaurl."?share=twitter");
				else
					$this->_redirect($this->user_session->redirectToExperturl."?invite=twitter");
			}
			else if(!empty($result_array[0]) && $user_info->id_str!='')
			{
				
				$user_personal_info['LastLoginIP']     = $this->_request->getServer('REMOTE_ADDR');
				$user_personal_info['browser']         = $this->commonmodel_obj->getbrowser();
				$user_personal_info['os']              = $this->commonmodel_obj->getos();
				$user_personal_info['userdevice']      = $this->commonmodel_obj->getdevice();
				$user_personal_info['city']            = $this->myclientdetails->customEncoding($geoplugin->city);
				$user_personal_info['region']          = $this->myclientdetails->customEncoding($geoplugin->region);
				$user_personal_info['area_code']       = $this->myclientdetails->customEncoding($geoplugin->areaCode);
				$user_personal_info['dma']             = $this->myclientdetails->customEncoding($geoplugin->dmaCode);
				$user_personal_info['country_code']    = $this->myclientdetails->customEncoding($geoplugin->countryCode);
				$user_personal_info['country_name']    = $this->myclientdetails->customEncoding($geoplugin->countryName);
				$user_personal_info['continent_name']  = $this->myclientdetails->customEncoding($this->commonmodel_obj->getcontinent($geoplugin->continentCode));
				$user_personal_info['longitude']       = $this->myclientdetails->customEncoding($geoplugin->longitude);
				$user_personal_info['latitude']        = $this->myclientdetails->customEncoding($geoplugin->latitude);
				$user_personal_info['currency_code']   = $this->myclientdetails->customEncoding($geoplugin->currencyCode);
				$user_personal_info['currency_symbol'] = $this->myclientdetails->customEncoding($geoplugin->currencySymbol);
				$user_personal_info['chatstatus'] = 1;
				$user_personal_info['isonline'] = 1;
				$dataArray['twitter_access_token'] = $this->user_session->access_token['oauth_token'];
			    $dataArray['twitter_token_secret'] = $this->user_session->access_token['oauth_token_secret'];
			    $dataArray['screen_name'] = $user_info->screen_name;
			    $dataArray['screen_id'] = $user_info->id_str;
		    	$user_personal_info['twitter_connect_data'] = Zend_Json::encode($dataArray);
				$this->user_model->updateSocialLogin($user_personal_info, $user_info->id_str);
				$result = $result_array['0'];
				$this->myclientdetails->sessionWrite($result);
				$this->globalSettings(); // for social	
				$username = $this->myclientdetails->customDecoding($result['Username']);
				$this->notification->commomInsert(9,9,'',$result['UserID'],$result['UserID'],'','');
				
				$authNamespace = new Zend_Session_Namespace('identify');
				$authNamespace->setExpirationSeconds((1209600));
                $authNamespace->role = $result_array['0']['role'];
				$authNamespace->id = $result_array[0]['UserID'];
				$authNamespace->user = $result_array[0]['Username'];
				$this->user_session->loggedIn = true;
				// Check if user is locked to a group
				$groupid = $result_array[0]['groupid'];
				$SessionSocialUrl = new Zend_Session_Namespace('SocialUrl');             
                
				if($SessionSocialUrl->redUrl!="")
					 $this->_redirect($SessionSocialUrl->redUrl); 
				
				if($groupid!='0') // If user is locked to a group, redirect him to group page on log in
					$this->_redirect('/group/groupdetails/group/'.$groupid);
				else
					$this->redirection($username);
			}else
			{

				$user_personal_info['LastLoginIP']     = $this->_request->getServer('REMOTE_ADDR');
				$user_personal_info['browser']         = $this->commonmodel_obj->getbrowser();
				$user_personal_info['os']              = $this->commonmodel_obj->getos();
				$user_personal_info['City']            = $this->myclientdetails->customEncoding($geoplugin->city);
				$user_personal_info['reg_region']          = $this->myclientdetails->customEncoding($geoplugin->region);
            	$user_personal_info['reg_area_code']       = $this->myclientdetails->customEncoding($geoplugin->areaCode);
            	$user_personal_info['reg_dma']             = $this->myclientdetails->customEncoding($geoplugin->dmaCode);
            	$user_personal_info['country_code']    = $this->myclientdetails->customEncoding($geoplugin->countryCode);
            	$user_personal_info['country_name']    = $this->myclientdetails->customEncoding($geoplugin->countryName);
            	$user_personal_info['continent_name']  = $this->myclientdetails->customEncoding($this->commonmodel_obj->getcontinent($geoplugin->continentCode));
            	$user_personal_info['longitude']       = $this->myclientdetails->customEncoding($geoplugin->longitude);
            	$user_personal_info['latitude']        = $this->myclientdetails->customEncoding($geoplugin->latitude);
            	$user_personal_info['currency_code']   = $this->myclientdetails->customEncoding($geoplugin->currencyCode);
            	$user_personal_info['currency_symbol'] = $this->myclientdetails->customEncoding($geoplugin->currencySymbol);
            	$user_personal_info['activeFirstTime'] = 1;
				$user_personal_info['Status'] = 1;
				$user_personal_info['Name'] = $this->myclientdetails->customEncoding($user_info->name);
				$user_personal_info['full_name'] = $this->myclientdetails->customEncoding($user_info->name);
				$username = $this->myclientdetails->customEncoding(strtolower($filter_alnum->filter($user_info->name)));
				$user_personal_info['Username'] = $this->myclientdetails->customEncoding($this->Myhome_Model->chkusername($username));
				$user_personal_info['Email'] = $this->myclientdetails->customEncoding('twitteruser@db-csp.com');
				$user_personal_info['Pass'] = $this->_generateHash(time());
				$user_personal_info['Birthdate'] = '0000-00-00';
				$user_personal_info['LastLoginDate'] = date('Y-m-d H:i:s');
				$user_personal_info['DOBmakeprivate'] = 1;
				$user_personal_info['Gender'] = '';
				$user_personal_info['Socialid'] = $user_info->id_str;
				$user_personal_info['Socialtype'] = 2;
				$user_personal_info['clientID'] = clientID;
				$dataArray['twitter_access_token'] = $this->user_session->access_token['oauth_token'];
			    $dataArray['twitter_token_secret'] = $this->user_session->access_token['oauth_token_secret'];
			    $dataArray['screen_name'] = $user_info->screen_name;
			    $dataArray['screen_id'] = $user_info->id_str;

		    	$user_personal_info['twitter_connect_data'] = Zend_Json::encode($dataArray);
		    	// get image code here
				if($user_info->profile_image_url_https)
				{
					$url = str_replace("_normal","",$user_info->profile_image_url_https);
					$sizes = array(100 => 100, 50 => 50);
					$filename = time().'.jpg';
					$dir = ABSIMGPATH."/users/".$filename;
					define('BUFSIZ', 40950);
					$rfile = @fopen($url, 'r');
					$lfile = @fopen($dir, 'w');
					while (!feof($rfile))
					@fwrite($lfile, fread($rfile, BUFSIZ), BUFSIZ);	
					fclose($rfile);
					fclose($lfile);
					$commonobj = new Application_Model_Commonfunctionality();
				
					foreach ($sizes as $w => $h) {
					 $files[] = $commonobj->resize($w, $h,$filename,$filename);
					}
					
					$user_personal_info['ProfilePic'] = $filename;
				}else
					$user_personal_info['ProfilePic'] = 'default-avatar.jpg';
				//end get image code here
				$user_personal_info['RegistrationDate'] = date('Y-m-d H:i:s');
				$user_personal_info['role'] = 3;
				
				if($this->group_session->groupid!='')
				{
					$user_personal_info['role'] = 100;
					$user_personal_info['groupid'] = $this->group_session->groupid;
					$this->user_session->redirection = BASE_URL.'/group/groupdetails/group/'.$this->group_session->groupid;
				}

				$lastInsertId = $this->user_model->adduser($user_personal_info);

				if($this->group_session->groupid!='')
					$this->makegroupmember($this->group_session->groupid,$user_personal_info['Socialid'],$lastInsertId);
				
				/************activated user follow to admin******************/
				$this->following  = new Application_Model_Following();
                if(adminID!=''){
					$data = array('clientID'=>clientID,'User'=>adminID,
						'FollowedBy'=>$lastInsertId,'StartDate'=>date('Y-m-d H:i:s'));
					$this->following->insertfollowing($data);

					$chatData = array('clientID'=> clientID,'sendto'=> adminID,'sendby'=> $lastInsertId,'status'=> 0,'dateofchat'=>date('Y-m-d H:i:s'));
           			$this->myclientdetails->insertdata_global('tblchatusers', $chatData);
			    }
				/************activated user follow to admin******************/
					
				// insert notificaton setting
				$notiArray['User'] = $lastInsertId;
				$notiArray['clientID'] = clientID;
				$this->notificationsetting->addusernoti($notiArray);
				$this->notification->commomInsert(8,8,'',$lastInsertId,$lastInsertId,'','');
				// end notification code

				// set session
				 $rss   = new Application_Model_Rss();
                 $rss->insertactivefeed($lastInsertId);

				$user_personal_info['UserID'] = $lastInsertId;
				$user_personal_info['usertype'] = 0;
				$user_personal_info['showtagmsg'] = 1;

				$this->user_session->loggedIn = true;
				$this->myclientdetails->sessionWrite($user_personal_info);
				$this->globalSettings(); // for social
				$username = $this->myclientdetails->customDecoding($user_personal_info['Username']);
				$this->redirection($username);
			}	
		}else
			$this->_helper->redirector->gotosimple('index','index',true);
	}

	private function _generateHash($plainText, $salt = null)
	{
		define('SALT_LENGTH', 9);
		if ($salt === null)
			$salt = substr(md5(uniqid(rand(), true)), 0, SALT_LENGTH);
		else 
			$salt = substr($salt, 0, SALT_LENGTH);
		return $salt . sha1($salt . $plainText);
	}

	

}
