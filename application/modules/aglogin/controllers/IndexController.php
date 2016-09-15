<?php
class Aglogin_IndexController extends IsloginController
{  

	public function init()
	{
		parent::init(); 
		if(INVALID_DOMAIN =='WRONG_URL')
            $this->_helper->redirector->gotosimple('notfound','index', true);
		$storage 	= new Zend_Auth_Storage_Session();
		$data	  	= $storage->read();
		if ($data['UserID'] != '' && $data['Email'] != '')
			$this->_helper->redirector->gotosimple('index', 'myhome', true);
	}	

	public function maintenanceAction()
    {
        $this->getResponse()->setHttpResponseCode(503);
    }


	public function indexAction()
	{	
		$this->_helper->layout->setLayout('layout');
	}
	public function redirection($username)
    {
        
        if($this->user_session->redirection!='')
            $this->_redirect($this->user_session->redirection);
        else
            $this->_redirect(BASE_URL."/user/".$username);
    }
	public function callbackAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$filter = new Zend_Filter_Decrypt();
		$filter_alnum = new Zend_Filter_Alnum();
		if(isset($_GET['token']))
		{
			$result = json_decode($this->httpGet('https://dev04-web-onlinegolf.demandware.net/on/demandware.store/Sites-AmericanGolf-GB-Site/en_GB/TokenLogin-AuthenticateWithToken?token='.$_GET['token']),true);
			//print_r($result); die;
			$checkuser = $this->myclientdetails->getfieldsfromtable('*','tblUsers','Email',$this->myclientdetails->customEncoding($result['EmailHash']),'clientID',clientID);
            $cheUseno = count($checkuser);

			if(!empty($result) && $result['EmailHash']!='' && $cheUseno!=0)
			{
				$user_personal_info['LastLoginIP']     = $this->_request->getServer('REMOTE_ADDR');
				$user_personal_info['LastLoginDate'] = date('Y-m-d H:i:s');
				$user_personal_info['Email'] = $result['email'];
				$user_personal_info['LastLoginIP']     = $this->_request->getServer('REMOTE_ADDR');
				$user_personal_info['browser']         = $this->commonmodel_obj->getbrowser();
				$user_personal_info['os']              = $this->commonmodel_obj->getos();
				$user_personal_info['userdevice']      = $this->commonmodel_obj->getdevice();
				$users['city']            = $this->myclientdetails->customEncoding($geoplugin->city);
				$user_personal_info['region']          = $this->myclientdetails->customEncoding($geoplugin->region);
				$user_personal_info['area_code']       = $this->myclientdetails->customEncoding($geoplugin->areaCode);
				$user_personal_info['dma']             = $this->myclientdetails->customEncoding($geoplugin->dmaCode);
				$user_personal_info['country_code']    = $this->myclientdetails->customEncoding($geoplugin->countryCode);
				$user_personal_info['country_name']    = $this->myclientdetails->customEncoding($geoplugin->countryName);
				$users['continent_name']  = $this->myclientdetails->customEncoding($this->commonmodel_obj->getcontinent($geoplugin->continentCode));
				$user_personal_info['longitude']       = $this->myclientdetails->customEncoding($geoplugin->longitude);
				$user_personal_info['latitude']        = $this->myclientdetails->customEncoding($geoplugin->latitude);
				$user_personal_info['currency_code']   = $this->myclientdetails->customEncoding($geoplugin->currencyCode);
				$user_personal_info['currency_symbol'] = $this->myclientdetails->customEncoding($geoplugin->currencySymbol);

				
				$checkuser = $this->myclientdetails->getfieldsfromtable('*','tblUsers','Email',$this->myclientdetails->customEncoding($result['EmailHash']),'clientID',clientID);
				
				$users['chatstatus'] = 1;
				$users['isonline'] = 1;

                $addStatus  = $this->myclientdetails->updatedata_global('tblUsers',$users,'UserID',$checkuser[0]['UserID']);

				$this->myclientdetails->sessionWrite($checkuser[0]);

				$username = $this->myclientdetails->customDecoding($checkuser[0]['Username']);
				$this->notification->commomInsert(9,9,'',$checkuser[0]['UserID'],$checkuser[0]['UserID'],'','');
				$authNamespace = new Zend_Session_Namespace('identify');
				$authNamespace->setExpirationSeconds((1209600));
				$authNamespace->role = $checkuser['0']['role'];

				$authNamespace->id = $checkuser[0]['UserID'];
				$authNamespace->user = $checkuser[0]['Username'];
				
				$this->user_session->loggedIn = true;

				$this->redirection($username);

			}
			else
			{
				$user_personal_info['LastLoginIP']     = $this->_request->getServer('REMOTE_ADDR');
				$user_personal_info['browser']         = $this->commonmodel_obj->getbrowser();
				$user_personal_info['os']              = $this->commonmodel_obj->getos();
				$user_personal_info['City']            = $this->myclientdetails->customEncoding($geoplugin->city);
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
				$user_personal_info['Status'] = 1;
				$user_personal_info['activeFirstTime'] = 1;
				$user_personal_info['Name'] = $this->myclientdetails->customEncoding($result['Firstname']);
				$user_personal_info['full_name'] = $this->myclientdetails->customEncoding($resilt['Firstname']);
				$username = $this->myclientdetails->customEncoding(strtolower($filter_alnum->filter($result['Firstname'])));
				$user_personal_info['Username'] = $this->myclientdetails->customEncoding($this->Myhome_Model->chkusername($username));
				$user_personal_info['Email'] = $this->myclientdetails->customEncoding($result['EmailHash']);
				$user_personal_info['Pass'] = $this->_generateHash(time());
				$user_personal_info['Birthdate'] = '0000-00-00';
				$user_personal_info['LastLoginDate'] = date('Y-m-d H:i:s');
				$user_personal_info['DOBmakeprivate'] = 1;
				$user_personal_info['Gender'] = '';
				$user_personal_info['Socialid'] = '';
				$user_personal_info['Socialtype'] = 4;
				$user_personal_info['clientID'] = clientID;
				$user_personal_info['usertype'] = 0;
				$user_personal_info['showtagmsg'] = 1;
				
				if($user_personal_info['Gender']=='Female')
            		{$user_personal_info['ProfilePic'] = 'default-avatar-female.jpg';}
            	elseif($user_personal_info['Gender']=='Male')
        	        {$user_personal_info['ProfilePic'] = 'default-avatar.jpg';}
            	else{$user_personal_info['ProfilePic'] = 'default-profilepic-std.png';}                     
			
				
				$user_personal_info['RegistrationDate'] = date('Y-m-d H:i:s');
				$user_personal_info['role'] = 3;
				$lastInsertId = $this->user_model->adduser($user_personal_info);
				// insert notificaton setting
				$notiArray['User'] = $lastInsertId;
				$notiArray['clientID'] = clientID;
				$this->notificationsetting->addusernoti($notiArray);
				// end notification code
				$this->notification->commomInsert(9,9,'',$lastInsertId,$lastInsertId,'','');
		
				 $rss   = new Application_Model_Rss();
                 $rss->insertactivefeed($lastInsertId);
				// set session
				$user_personal_info['UserID'] = $lastInsertId;
				
				$this->myclientdetails->sessionWrite($user_personal_info);
				// end session set code 
				/************activated user follow to admin******************/
				$this->following  = new Application_Model_Following();
                if(adminID!='')
                {
					$data = array('clientID'=>clientID,'User'=>adminID,'FollowedBy'=>$lastInsertId,'StartDate'=>date('Y-m-d H:i:s'));
					$this->following->insertfollowing($data);
					
					$chatData = array('clientID'=> clientID,'sendto'=> adminID,'sendby'=> $lastInsertId,'status'=> 0,'dateofchat'=>date('Y-m-d H:i:s'));
           			$this->myclientdetails->insertdata_global('tblchatusers', $chatData);
			    }
				/************activated user follow to admin******************/
					
				// send welcome mail to facebook user 
				//$this->sendMailToFaceBookUser($user_personal_info);
				// redirect to logined user
				$this->globalSettings(); // for social
				$this->user_session->loggedIn = true;
				$username = $this->myclientdetails->customDecoding($user_personal_info['Username']);
				$this->redirection($username);
			}
		}else{
			$this->_redirect('/');
		}
	}

	public function httpGet($url)
	{
	    $ch = curl_init();  
	 
	    curl_setopt($ch,CURLOPT_URL,$url);
	    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	//  curl_setopt($ch,CURLOPT_HEADER, false); 
	 
	    $output=curl_exec($ch);
	 
	    curl_close($ch);
	    return $output;
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