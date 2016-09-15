<?php
class LinkedinController extends IsloginController
{
	public function init()
	{
		parent::init();
		$request = $this->getRequest()->getParams();
		$this->_helper->layout->setLayout('index');
		$storage 	= new Zend_Auth_Storage_Session();
		$auth        =  Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$data	  	= $storage->read();
			$this->session_data = $data;
			if ($data['UserID'] != '' && $data['Email'] != '')
				$this->_helper->redirector->gotosimple('index', 'myhome', true);

		}
	}
	
	public function loginAction()
	{

		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		if($this->getGlobalSettings()=='block')
		{
			$this->_helper->flashMessenger->addMessage('block');
			$this->_redirect("/");
		}
		
		$session_name_space = new Zend_Session_Namespace('User_Session');
		$config['base_url']        = FRONT_URL;
        $config['callback_url']    = FRONT_URL . '/linkedin/callback';
        $config['linkedin_access'] = linkedinAppid;
        $config['linkedin_secret'] = linkedinSecret;
        $config['oauth_callback'] = FRONT_URL;
		
		# First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
		$linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], $config['callback_url'] );
		//$linkedin->debug = true;

		# Now we retrieve a request token. It will be set as $linkedin->request_token
		$linkedin->getRequestToken();

		$session_name_space->requestToken = serialize($linkedin->request_token);

		# With a request token in hand, we can generate an authorization URL, which we'll direct the user to
		//echo "Authorization URL: " . $linkedin->generateAuthorizeUrl() . "\n\n";
		header("Location: " . $linkedin->generateAuthorizeUrl());

	}



	public function callbackAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$config['base_url']        = FRONT_URL;
        $config['callback_url']    = FRONT_URL . '/linkedin/callback';
        $config['linkedin_access'] = linkedinAppid;
        $config['linkedin_secret'] = linkedinSecret;
        $config['oauth_callback'] = FRONT_URL;
	    # First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
	    $linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], '');
	    $linkedin->debug = false;
	   	$security = unserialize($this->user_session->requestToken);
	   	if($_REQUEST['oauth_problem']=='user_refused'){
	   		$this->_redirect('/');
	   	}
	   if(is_object($security) && !empty($security->key))
	   {
	
		   if (isset($_REQUEST['oauth_verifier']))
		   {
		        $this->user_session->oauth_verifier   = $_REQUEST['oauth_verifier'];
		        $linkedin->request_token    =   unserialize($this->user_session->requestToken);
		        $linkedin->oauth_verifier   =   $this->user_session->oauth_verifier;
		        $linkedin->getAccessToken($_REQUEST['oauth_verifier']);
		        $this->user_session->oauth_access_token = serialize($linkedin->access_token);
		        header("Location: " . $config['callback_url']);
		        exit;
		   }
		   else
		   {
		        $linkedin->request_token    =   unserialize($this->user_session->requestToken);
		        $linkedin->oauth_verifier   =   $this->user_session->oauth_verifier;
		        $linkedin->access_token     =   unserialize($this->user_session->oauth_access_token);
		   }
	   		$this->user_session->LinkedInLogined = 6;
			
			$filter = new Zend_Filter_StripTags();
			$filter_alnum = new Zend_Filter_Alnum();
			
	   		$resultJson = $linkedin->getProfile("~:(id,email-address,first-name,last-name,picture-url;secure=true,date-of-birth)?format=json");
	   		$jsonData = json_decode($resultJson);
	   		
			$email = $jsonData->emailAddress;
	   		$firstName = $jsonData->firstName;
	   		$lastName = $jsonData->lastName;
	   		$id = $jsonData->id;
	   		
	   		if(isset($jsonData->pictureUrl))
	   			$picUrl = $jsonData->pictureUrl;
	   		else
	   			$picUrl= '';

	   		$dateOfBirth = $jsonData->dateOfBirth;
	   		
	   		if(isset($dateOfBirth->day))
	   			$day = $dateOfBirth->day;
	   		else
	   			$day = '00';

	   		if(isset($dateOfBirth->month))
	   			$month = $dateOfBirth->month;
	   		else
	   			$month = '00';

	   		if(isset($dateOfBirth->year))
	   			$year = $dateOfBirth->year;
	   		else
	   			$year = '0000';

	   		$dob = $day.'-'.$month.'-'.$year;
	   		
	   		$result_array = $this->user_model->getSocialDetail($id);

	   		if($result_array[0]['Status']==0 && !empty($result_array))
		    {
				$this->_helper->flashMessenger->addMessage('Oops, your account seems to be deactivated.');
				$this->_helper->redirector->gotosimple('index','index',true); exit();
		    }
	   		else if(!$this->user_model->checkSocialEmail($id, $this->myclientdetails->customEncoding($email)))
			{	
				$geoplugin = new geoPlugin();
				$geoplugin->locate($this->_request->getServer('REMOTE_ADDR'));

				if($this->user_model->getSocialDetail($id) && $id!='')
				{
					$user_personal_info['longitude']       = $this->myclientdetails->customEncoding($geoplugin->longitude);
                	$user_personal_info['latitude']        = $this->myclientdetails->customEncoding($geoplugin->latitude);
					$user_personal_info['LastLoginIP']  = $this->_request->getServer('REMOTE_ADDR');
					$user_personal_info['LastLoginDate'] = date('Y-m-d H:i:s');
					$user_personal_info['userdevice']      = $this->commonmodel_obj->getdevice();
					$this->user_model->updateSocialLogin($user_personal_info, $id);
					$result_array = $this->user_model->getSocialDetail($id);
					$result = $result_array['0'];
					
        			$this->myclientdetails->sessionWrite($userresult[0]);
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
                	$user_personal_info['reg_country_code']    = $this->myclientdetails->customEncoding($geoplugin->countryCode);
                	$user_personal_info['reg_country_name']    = $this->myclientdetails->customEncoding($geoplugin->countryName);
                	$user_personal_info['reg_continent_name']  = $this->myclientdetails->customEncoding($this->commonmodel_obj->getcontinent($geoplugin->continentCode));
                	$user_personal_info['longitude']       = $this->myclientdetails->customEncoding($geoplugin->longitude);
                	$user_personal_info['latitude']        = $this->myclientdetails->customEncoding($geoplugin->latitude);
                	$user_personal_info['reg_currency_code']   = $this->myclientdetails->customEncoding($geoplugin->currencyCode);
                	$user_personal_info['reg_currency_symbol'] = $this->myclientdetails->customEncoding($geoplugin->currencySymbol);
                	$user_personal_info['activeFirstTime'] = 1;
					$user_personal_info['Status'] = 1;
					$user_personal_info['Name'] = $this->myclientdetails->customEncoding($firstName);
					$user_personal_info['lname'] = $this->myclientdetails->customEncoding($lastName);
					$user_personal_info['full_name'] = $this->myclientdetails->customEncoding($firstName);
					$username = $this->myclientdetails->customEncoding(strtolower($firstName));
					$user_personal_info['Username'] = $this->myclientdetails->customEncoding($this->Myhome_Model->chkusername($username));
					$user_personal_info['Email'] = $this->myclientdetails->customEncoding($filter->filter($email));
					$user_personal_info['Pass'] = $this->_generateHash(time());
					$user_personal_info['Birthdate'] = date("Y-m-d", strtotime($dob));
					$user_personal_info['LastLoginDate'] = date('Y-m-d H:i:s');
					$user_personal_info['DOBmakeprivate'] = 1;
					$user_personal_info['Gender'] = '';
					$user_personal_info['Socialid'] = $id;
					$user_personal_info['Socialtype'] = 3;
					$user_personal_info['clientID'] = clientID;
					$dataArray['request_token'] = $this->user_session->requestToken;
					$dataArray['oauth_verifier'] = $this->user_session->oauth_verifier;
					$dataArray['oauth_access_token'] = $this->user_session->oauth_access_token;
					$dataArray['firstName'] = $firstName;
					$dataArray['id'] = $id;
					$dataArray['connnected'] = 'connnected';
					$user_personal_info['linkedin_connect_data'] = Zend_Json::encode($dataArray);

					// get image code here
					if($picUrl)
					{
						$filename = time().'.jpg';
							
						$dir = ABSIMGPATH."/users/".$filename;
							
						$url = $picUrl;

						define('BUFSIZ', 40950);
							
						$rfile = @fopen($url, 'r');
							
						$lfile = @fopen($dir, 'w');
							
						while (!feof($rfile))
						@fwrite($lfile, fread($rfile, BUFSIZ), BUFSIZ);
							
						fclose($rfile);
							
						fclose($lfile);

						$user_personal_info['ProfilePic'] = $filename;
					}else
						$user_personal_info['ProfilePic'] = 'default-avatar.jpg';
				   // get end image code here
					$user_personal_info['RegistrationDate'] = date('Y-m-d H:i:s');
					$user_personal_info['role'] = 3;
					
					if($this->group_session->groupid!='')
					{
						$user_personal_info['role'] = 100;
						$user_personal_info['groupid'] = $this->group_session->groupid;
						$this->user_session->redirection = BASE_URL.'/group/groupdetails/group/'.$this->group_session->groupid;
					}
					$lastInsertId = $this->user_model->adduser($user_personal_info);

					/************activated user follow to admin******************/
					$this->following  = new Application_Model_Following();
					if(adminID!=''){
						$data = array('clientID'=>clientID,'User'=>adminID,
						'FollowedBy'=>$lastInsertId,'StartDate'=>date('Y-m-d H:i:s'));
						$this->following->insertfollowing($data);
					}
					/************activated user follow to admin******************/

					
					if($this->group_session->groupid!='')
						$this->makegroupmember($this->group_session->groupid,$user_personal_info['Socialid'],$lastInsertId);
					

					$this->notification->commomInsert(8,8,'',$lastInsertId,$lastInsertId,'','');
					// insert notificaton setting
					$notiArray['User'] = $lastInsertId;
					$notiArray['clientID'] = clientID;

					$this->notificationsetting->addusernoti($notiArray);
					// end notification code

					 $rss   = new Application_Model_Rss();
	                 $rss->insertactivefeed($lastInsertId);
					// set session
					$user_personal_info['UserID'] = $lastInsertId;
					$user_personal_info['usertype'] = 0;
					$user_personal_info['showtagmsg'] = 1;
					$this->myclientdetails->sessionWrite($user_personal_info);

					$this->globalSettings(); // for social	
					$this->sendMailToFaceBookUser($user_personal_info);
					$username = $this->myclientdetails->customDecoding($user_personal_info['Username']);
					$this->user_session->loggedIn = true;
					$this->redirection($username);
			
				}
			}
			else
			{
				$this->_helper->flashMessenger->addMessage('Sorry! You have tried to login with an email address already registered with us');
				$this->_redirect("/");
			}
		}
		else
		{
			$this->_helper->flashMessenger->addMessage('Sorry! You have tried to login with an email address already registered with us');
			$this->_redirect("/");
		}
   		

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
