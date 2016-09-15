<?php
class TwitterController extends IsController
{
	 public function init()
    {
        parent::init();
    } 
	public function indexAction()
	{
		$this->redirectToHome();
		$this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
		
		
		$TwitterOAuth = new TwitterOAuth(twitterAppid, twitterSecret);
		$request_token = $TwitterOAuth->getRequestToken(FRONT_URL.'/twitter/callback');

		$this->session_name_space->oauth_token = $request_token['oauth_token'];
		$this->session_name_space->oauth_token_secret = $request_token['oauth_token_secret'];

		if ($TwitterOAuth->http_code == 200) {
			// Let's generate the URL and redirect
			$url = $TwitterOAuth->getAuthorizeURL($request_token['oauth_token']);
			$this->_redirect($url);
				
		} else 
			$this->_helper->redirector->gotosimple('index','index',true);
		
	}
	
	public function redirectToHome()
	{
		$storage    = new Zend_Auth_Storage_Session();
        $auth        =  Zend_Auth::getInstance();
        if(!$auth->hasIdentity()){
           $this->_redirect('/');
        }
	}

	public function callbackAction()
	{
		$this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
		$oauth_verifier = $this->_request->getParam('oauth_verifier');
		$this->redirectToHome();
		if (!empty($oauth_verifier) && !empty($this->session_name_space->oauth_token) && !empty($this->session_name_space->oauth_token_secret)) 
		{
			$twitteroauth = new TwitterOAuth(twitterAppid,
					twitterSecret,
					$this->session_name_space->oauth_token,
					$this->session_name_space->oauth_token_secret);
						
			$access_token = $twitteroauth->getAccessToken($oauth_verifier);
			// Save it in a session var
			$this->session_name_space->access_token = $access_token;
			
			// Let's get the user's info
			$user_info = $twitteroauth->get('account/verify_credentials');

			$this->session_name_space->screen_name = $user_info->screen_name;
			$this->session_name_space->screen_id = $user_info->id_str;
			$result_array = $this->User_Model->getSocialDetail($user_info->id_str);
			
		    if($result_array[0]['Status']==0 && !empty($result_array))
		    {
		    	unset($this->session_name_space->access_token);
				$this->_helper->flashMessenger->addMessage('Sorry your account has been made dormant by the administrator.');
				$this->_helper->redirector->gotosimple('index','index',true);

		    }else
		    {
		    	$dataArray['twitter_access_token'] = $this->session_name_space->access_token['oauth_token'];
			    $dataArray['twitter_token_secret'] = $this->session_name_space->access_token['oauth_token_secret'];
			    $dataArray['screen_name'] = $user_info->screen_name;
			    $dataArray['screen_id'] = $user_info->id_str;
			    if($user_info->profile_image_url_https)
				{
					$url = str_replace("_normal","",$user_info->profile_image_url_https);
					$filename = time().'.jpg';
					$dir = './userpics/'.$filename;
					define('BUFSIZ', 40950);
					$rfile = @fopen($url, 'r');
					$lfile = @fopen($dir, 'w');
					while (!feof($rfile))
					@fwrite($lfile, fread($rfile, BUFSIZ), BUFSIZ);	
					fclose($rfile);
					fclose($lfile);
					$dataArray['ProfilePic'] = $filename;
				}else
					$dataArray['ProfilePic'] = 'default-avatar.jpg';
					
		    	$user_personal_info['twitter_connect_data'] = Zend_Json::encode($dataArray);
		    	$user_personal_info['UserID'] = $this->_userid;
			    $changeuserinfo = $this->User_Model->updateinfouser($user_personal_info);
			    $result_array = $this->User_Model->userdetailall($this->_userid);
			    
        		$this->myclientdetails->sessionWrite($result_array[0]);

			    if(isset($this->session_name_space->callBackUrl) && $this->session_name_space->callBackUrl!='')
			    	$this->_redirect($this->session_name_space->callBackUrl."twitter&qt=5");
			    else
			    	$this->_redirect('/user/'.$this->myclientdetails->customDecoding($result_array[0]['Username']).'?qt=5');                    

		    }	
		}else
			$this->_helper->redirector->gotosimple('index','index',true);
	}

	
}
