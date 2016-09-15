<?php
class IndexController extends IsloginController
{  

	public function init()
	{
		parent::init();
		if(INVALID_DOMAIN =='WRONG_URL')
            $this->_helper->redirector->gotosimple('notfound','index', true);
		$request = $this->getRequest()->getParams();
		$this->_helper->layout->setLayout('index');
		$auth        =  Zend_Auth::getInstance();
		$this->Social_Content_Block = $this->getGlobalSettings();
		$this->myclientdetails = new Application_Model_Clientdetails();
		//echo $this->user_session->redirection;
		$where = array('1'=>1);
        $checkclient = $this->myclientdetails->getRowMasterfromtable('tblClient',array('*'),$where);
		
		if($checkclient['clientStatus']==0)
        {   echo'<div style="font-family:Arial; padding:20px; text-align:center; border-radius:10px;">';
            echo"<h1> Site not found</h1>";
            echo"<p> You might have followed an incorrect link.</p><br>";
            echo'</div>';
            die;
        }
		if($auth->hasIdentity())
		{
			$storage 	= new Zend_Auth_Storage_Session();
			$data	  	= $storage->read();
			if ($data['UserID'] != '' && $data['Email'] != '')
				$this->_helper->redirector->gotosimple('index', 'myhome', true);
		}

		if(isSet($_COOKIE['RememberEmail']) && $_COOKIE['RememberEmail']!="" && isSet($_COOKIE['Rememberpass']) && $_COOKIE['Rememberpass']!="")
		   $this->KeepMeLoggedIn();	

	}	


	public function KeepMeLoggedIn()
	{
		// auto login
		if(isSet($_COOKIE['RememberEmail']) && $_COOKIE['RememberEmail']!="" && isSet($_COOKIE['Rememberpass']) && $_COOKIE['Rememberpass']!="")
		 {
		   
		 	$error_message = 'Incorrect log in details. Please try again.';
			
			$request['loginpass'] =  $this->myclientdetails->customDecoding($_COOKIE['Rememberpass']);
			$request['loginemail'] = $_COOKIE['RememberEmail'];
			

			$auth = Zend_Auth::getInstance();
			$registry = Zend_Registry::getInstance();
			if (empty($request['act_email'])) 
			{
				$userresult = $this->myclientdetails->getfieldsfromtable('*','tblUsers','Email',$request['loginemail'],'clientID',clientID);
		
				if ((isset($request['loginemail']) && !empty($request['loginemail'])) && (isset($request['loginpass']) && !empty($request['loginpass'])) && (isset($userresult[0]['Status']) && !empty($userresult[0]['Status']))) 
				{	
					$filter = new Zend_Filter_Decrypt();
					$config = Zend_Registry::get('config');
		            $db = Zend_Db::factory('Pdo_Mysql', $config->resources->db->params);
					$adapter = new Zend_Auth_Adapter_DbTable($db);
					$adapter->setTableName('tblUsers');
					$adapter->setIdentityColumn('Email');
					$adapter->setCredentialColumn('Pass');
					$adapter->setIdentity($request['loginemail']);
					$chkPass = $adapter->setCredential($adapter->setCredentialdbee($request['loginpass'], $userresult[0]['Pass']));
					
					$select = $adapter->getDbSelect();
	                $select->where('clientID = ?',clientID);

					$result = $auth->authenticate($adapter);
									
					if ($result->isValid())
					{
					
						$userinfodata = array(
	                        'UserID' => $adapter->getResultRowObject()->UserID,
	                        'role' => $adapter->getResultRowObject()->role,
	                        'clientID' => $adapter->getResultRowObject()->clientID ,
	                        'Title' => $adapter->getResultRowObject()->title,
	                        'company' => $adapter->getResultRowObject()->company,
	                        'Email' => $adapter->getResultRowObject()->Email,
	                        'Name' => $adapter->getResultRowObject()->Name,
	                        'lname' => $adapter->getResultRowObject()->lname,
	                        'full_name' => $adapter->getResultRowObject()->full_name,
	                        'Username' => $adapter->getResultRowObject()->Username,
							'usertype' => $adapter->getResultRowObject()->usertype,
	                        'Fbname' => $adapter->getResultRowObject()->Fbname,
	                        'Address' => $adapter->getResultRowObject()->Address,
	                        'City' => $adapter->getResultRowObject()->City,
	                        'Gender' => $adapter->getResultRowObject()->Gender,
	                        'Birthdate' => $adapter->getResultRowObject()->Birthdate,
	                        'ProfilePic' => $adapter->getResultRowObject()->ProfilePic,
	                        'ActCode' => $adapter->getResultRowObject()->ActCode,
	                        'ShowPPBox' => $adapter->getResultRowObject()->ShowPPBox,
	                        'SocialFB' => $adapter->getResultRowObject()->SocialFB,
	                        'Socialtype' => $adapter->getResultRowObject()->Socialtype,
	                        'SocialTwitter' => $adapter->getResultRowObject()->SocialTwitter,
	                        'SocialLinkedin' => $adapter->getResultRowObject()->SocialLinkedin,
	                        'ResetPassCode' => $adapter->getResultRowObject()->ResetPassCode,
	                        'CookieAccept' => $adapter->getResultRowObject()->CookieAccept,
	                        'ScoringStatus' => $adapter->getResultRowObject()->ScoringStatus,
	                        'RegistrationDate' => $adapter->getResultRowObject()->RegistrationDate,
	                        'LastUpdateDate' => $adapter->getResultRowObject()->LastUpdateDate,
	                        'LastLoginDate' => $adapter->getResultRowObject()->LastLoginDate,
	                        'LastLoginSeenDate' => $adapter->getResultRowObject()->LastLoginSeenDate,
	                        'IP' => $adapter->getResultRowObject()->IP,
	                        'LastLoginIP' => $adapter->getResultRowObject()->LastLoginIP,
	                        'Status' => $adapter->getResultRowObject()->Status,
	                        'state' => $adapter->getResultRowObject()->state,
	                        'twitter_connect_data' => $adapter->getResultRowObject()->twitter_connect_data,
	                        'facebook_connect_data' => $adapter->getResultRowObject()->facebook_connect_data,
	                        'linkedin_connect_data' => $adapter->getResultRowObject()->linkedin_connect_data,
	                        'usertype' => $adapter->getResultRowObject()->usertype,
	                        'showtagmsg' => $adapter->getResultRowObject()->showtagmsg
						);

	                    
						$UserID    = $adapter->getResultRowObject()->UserID;
						$geoplugin = new geoPlugin();
						if ($_SERVER['SERVER_NAME'] == 'localhost')
	                	$geoplugin->locate('182.64.210.137'); // for local
	                	else
	                	$geoplugin->locate($this->_request->getServer('REMOTE_ADDR'));		

						$users['LastLoginIP']     = $this->_request->getServer('REMOTE_ADDR');
	                	$users['browser']         = $this->commonmodel_obj->getbrowser();
	                	$users['os']              = $this->commonmodel_obj->getos();
	                	$users['userdevice']      = $this->commonmodel_obj->getdevice();
						$users['city']            = $this->myclientdetails->customEncoding($geoplugin->city);
						$users['region']          = $this->myclientdetails->customEncoding($geoplugin->region);
						$users['area_code']       = $this->myclientdetails->customEncoding($geoplugin->areaCode);
						$users['dma']             = $this->myclientdetails->customEncoding($geoplugin->dmaCode);
						$users['country_code']    = $this->myclientdetails->customEncoding($geoplugin->countryCode);
						$users['country_name']    = $this->myclientdetails->customEncoding($geoplugin->countryName);
						$users['continent_name']  = $this->myclientdetails->customEncoding($this->commonmodel_obj->getcontinent($geoplugin->continentCode));
						$users['longitude']       = $this->myclientdetails->customEncoding($geoplugin->longitude);
						$users['latitude']        = $this->myclientdetails->customEncoding($geoplugin->latitude);
						$users['currency_code']   = $this->myclientdetails->customEncoding($geoplugin->currencyCode);
						$users['currency_symbol'] = $this->myclientdetails->customEncoding($geoplugin->currencySymbol);

	                    $addStatus  = $this->myclientdetails->updatedata_global('tblUsers',$users,'UserID',$UserID);
						
        				$this->myclientdetails->sessionWrite($userinfodata);
						$authNamespace = new Zend_Session_Namespace('identify');
						$authNamespace->setExpirationSeconds((1209600));
						$authNamespace->role = $userinfodata['role'];
						$authNamespace->id = $userinfodata['UserID'];
						$authNamespace->user = $userinfodata['Username'];
						
						$this->globalSettings(); // for social
						if($userinfodata['UserID'] == adminID)
							$this->Myhome_Model->updateGlobalLogin();	

						$username = $this->myclientdetails->customDecoding($userinfodata['Username']);
						$this->redirection($username);
					}
					else 
					{
						$this->_helper->flashMessenger->addMessage($error_message);
						$this->user_session->form_status = 0;
						$this->_redirect('/');
					}
				} else {
					$this->_helper->flashMessenger->addMessage($error_message);
					$this->user_session->form_status = 0;
					$this->_redirect('/');
				}
			} else {
				$this->_redirect('/?invalid path');			
			}

		 }

	}

	public function maintenanceAction()
    {
        $this->getResponse()->setHttpResponseCode(503);
    }

	public function facebookAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_redirect($this->facebooklogin());
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
				$filter = new Zend_Filter_StripTags();
				$filter_alnum = new Zend_Filter_Alnum();
				
				try
				{
					$userdata = $facebook->api('/me');

					$geoplugin = new geoPlugin();
					$geoplugin->locate($this->_request->getServer('REMOTE_ADDR'));

					$access_token_title = 'fb_'.facebookAppid.'_access_token';
					$access_token = $_SESSION[$access_token_title];

					$userDetailsfb =  $this->user_model->getSocialDetail($userdata['id']);
					
					//echo "<pre>"; print_r($conLoginfb);exit;

					if($this->user_model->checkSocialEmail($userdata['id'], $this->myclientdetails->customEncoding($userdata['email'])))
					{
						$this->_helper->flashMessenger->addMessage('Sorry! You have tried to login with an email address already registered with us');
						$this->_redirect("/");
					}
					else if(!empty($userDetailsfb[0]) && $userdata['id']!='')
					{
						//cheking inactive or active user

						$profileActive = $userDetailsfb[0]['Status'];
						if($profileActive==0)
						{
							$this->_helper->flashMessenger->addMessage('Oops, your account seems to be deactivated.');
							$this->_redirect("/");
						}

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
						$user_personal_info['chatstatus'] = 1;
						$user_personal_info['isonline'] = 1;
						$this->user_model->updateSocialLogin($user_personal_info, $userdata['id']);
						$result_array = $this->user_model->getSocialDetail($userdata['id']);
						
						$this->myclientdetails->sessionWrite($result_array[0]);
						$this->globalSettings(); // for social
						$username = $this->myclientdetails->customDecoding($result_array['0']['Username']);
						$this->notification->commomInsert(9,9,'',$result_array[0]['UserID'],$result_array[0]['UserID'],'','');
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
						{
							 $this->_redirect($SessionSocialUrl->redUrl); 
						}

						if($groupid!='0') // If user is locked to a group, redirect him to group page on log in
							$this->_redirect('/group/groupdetails/group/'.$groupid);
						else
							$this->redirection($username); // Else to his profile					

					}else
					{
						

						// make user data array 
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
						$user_personal_info['Name'] = $this->myclientdetails->customEncoding($userdata['name']);
						$user_personal_info['full_name'] = $this->myclientdetails->customEncoding($userdata['name']);
						$username = $this->myclientdetails->customEncoding(strtolower($filter_alnum->filter($userdata['name'])));
						$user_personal_info['Username'] = $this->myclientdetails->customEncoding($this->Myhome_Model->chkusername($username));
						$user_personal_info['Email'] = $this->myclientdetails->customEncoding($filter->filter($userdata['email']));
						$user_personal_info['Pass'] = $this->_generateHash(time());
						$user_personal_info['Birthdate'] = date("Y-m-d", strtotime($userdata['birthday']));
						$user_personal_info['LastLoginDate'] = date('Y-m-d H:i:s');
						$user_personal_info['activeFirstTime'] = 1;

						$user_personal_info['Gender'] = $this->myclientdetails->customEncoding(ucfirst($userdata['gender']));
						$user_personal_info['Socialid'] = $userdata['id'];
						$user_personal_info['Socialtype'] = 1;
						$user_personal_info['clientID'] = clientID;
						$user_personal_info['showtagmsg'] = 1;
						// for offline token facebook its not part of dbee user name
						$dataArray['access_token'] = $access_token;
						$dataArray['facebookid'] = $userdata['id'];
						$dataArray['facebookname'] = $userdata['name'];
						$user_personal_info['facebook_connect_data'] = Zend_Json::encode($dataArray);
						// for offline token facebook its not part of dbee user name
						$user_personal_info['RegistrationDate'] = date('Y-m-d H:i:s');
						
						// get facebook image code here
						
						$file = @file_get_contents('http://graph.facebook.com/'.$userdata['id'].'/picture?type=large');
						if($file)
						{
							$sizes = array(100 => 100, 50 => 50);

							$filename = time().'.jpg';	
							$dir = ABSIMGPATH."/users/".$filename;
							$fp = fopen($dir, "w");
							fwrite($fp, $file);
							fclose($fp);
							// upload pic
								
							$commonobj = new Application_Model_Commonfunctionality();
				
							foreach ($sizes as $w => $h) {
							 $files[] = $commonobj->resize($w, $h,$filename,$filename);
						    }
						    
						    // upload
							$user_personal_info['ProfilePic'] = $filename;
						}else
							$user_personal_info['ProfilePic'] = 'default-avatar.jpg';

						$user_personal_info['role'] = 3;
					    // end get facebok image code here & and end userdata array code				

						if($this->group_session->groupid!='')
						{
							$user_personal_info['role'] = 100;
							$user_personal_info['groupid'] = $this->group_session->groupid;
							$this->user_session->redirection = BASE_URL.'/group/groupdetails/group/'.$this->group_session->groupid;
						}
						
						$lastInsertId = $this->user_model->adduser($user_personal_info);

						if($this->group_session->groupid!='')
							$this->makegroupmember($this->group_session->groupid,$user_personal_info['Socialid'],$lastInsertId);
		
						// insert notificaton setting
						$notiArray['User'] = $lastInsertId;
						$notiArray['clientID'] = clientID;
						$this->notificationsetting->addusernoti($notiArray);
						// end notification code
						$this->notification->commomInsert(8,8,'',$lastInsertId,$lastInsertId,'','');

						/************activated user follow to admin******************/
						$this->following  = new Application_Model_Following();
	                    if(adminID!=''){
							$data = array('clientID'=>clientID,'User'=>adminID,'FollowedBy'=>$lastInsertId,'StartDate'=>date('Y-m-d H:i:s'));
							$this->following->insertfollowing($data);
							$chatData = array('clientID'=> clientID,'sendto'=> adminID,'sendby'=> $lastInsertId,'status'=> 0,'dateofchat'=>date('Y-m-d H:i:s'));
           					$this->myclientdetails->insertdata_global('tblchatusers', $chatData);
					    }
					/************activated user follow to admin******************/


						// set session
						$rss   = new Application_Model_Rss();
            			$rss->insertactivefeed($lastInsertId);
						$user_personal_info['UserID'] = $lastInsertId;
						$user_personal_info['usertype'] = 0;
						$this->user_session->loggedIn = true;
						// end session set code 
						$this->myclientdetails->sessionWrite($user_personal_info);
				

						// send welcome mail to facebook user 
						$this->sendMailToFaceBookUser($user_personal_info);
						// redirect to logined user
						$this->globalSettings(); // for social	
						$username = $this->myclientdetails->customDecoding($user_personal_info['Username']);
						$this->redirection($username);
					}
				}
				catch (FacebookApiException $e) {
					error_log($e);
					$user = null;
				}
			}
		}
		return $facebook->getLoginUrl(array(
         'scope' => 'user_posts,user_tagged_places,user_friends,user_birthday,publish_pages,publish_actions,public_profile,email,manage_pages'
        ));
	}
	

    // send reset link with reset password
	public function sendpasswordAction()
	{
		$request = $this->getRequest()->getParams();
		//echo'<pre>';print_r($request);die;
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
				$request = $this->getRequest();

				$filter = new Zend_Filter_StripTags();
				$email = $filter->filter($request->getpost('email'));
				//$birthday = $filter->filter($request->getpost('birthday'));
				//$birthmonth = $filter->filter($request->getpost('birthmonth'));
				//$birthyear = $filter->filter($request->getpost('birthyear'));
				// for validate token 
				$validate = $this->commonmodel_obj->authorizeToken($this->_request->getPost('SessUser__'),$this->_request->getPost('SessId__'),$this->_request->getPost('SessName__'),$this->_request->getPost('Token__'),$this->_request->getPost('Key__'));
				if($validate==false)
				{
					$data['status'] = 'error';
					$data['message'] = 'Some thing went wrong here please try again';
					
				}else
				{
					//$Birthdate=$birthyear.'-'.$birthmonth.'-'.$birthday;
					$Res = $this->user_model->ausersocialdetailforget($this->myclientdetails->customEncoding($email));
                    //echo'<pre>';print_r($Res);die;
					if(count($Res)>0)
					{	
					   					 
						// error message based on error code									
						switch ($Res['Socialtype'])
						{
						  case 0:
						    $randkey=rand(45982, 99999);
							$data = array('ResetPassCode' => $randkey);
							// update token in user table and send to mail 						
				            $store = $this->myclientdetails->updatedata_global('tblUsers',$data,'UserID',$Res['UserID']);
						    
						    /*****for email *****/
			                $EmailTemplateArray = array('Email' => $Res['Email'],
			                                             'Name' => $Res['Name'],
			                                             'lname' => $Res['lname'],
			                                             'UserID'=>$Res['UserID'],
			                                             'randkey'=>$randkey,
			                                             'case'=>8);
	                        $bodyContentmsgall = $this->dbeeComparetemplateOne($EmailTemplateArray);
	                        //echo'<pre>';print_r($bodyContentmsgall);die;
	                        $SubmitMessage=0;		
	                        /*****for email *****/
							/*if($bodyContentmsgall)
                               $SubmitMessage=0;
							else
							{								
								$SubmitMessage=1;
								$data['message'] = 'Detail don\'t match' ;
							}*/
							$data['content']=2;
							$data['SubmitMessage'] = $SubmitMessage;
							$data['fromlogin'] = $fromlogin;
						    break;
						  case 1:
						    $data['status'] = 'error';
							$data['message'] = 'Error: Password reset not allowed. This email address is linked to a facebook account';
						    break;
						  case 2:
						    $data['status'] = 'error';
							$data['message'] = 'Error: Password reset not allowed. This email address is linked to a twitter account';
						    break;
						  case 3:
							$data['status'] = 'error';
							$data['message'] = 'Error: Password reset not allowed. This email address is linked to a linkedin account';
							break;
						  case 3:
							$data['status'] = 'error';
							$data['message'] = 'Error: Password reset not allowed. This email address is linked to a Gplus account';
							break;
						} 
					}
					else
					{
						$data['content'] = 1;
						$data['message'] = 'Detail don\'t match';
					}
				}
		}else
		{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data)); 
	}
	
	public function recoverAction()
	{	
		$request = $this->getRequest()->getParams();
		$user = $request->_userid;
		$this->view->id = $request['id'];
	}
	public function checkresetcodeAction()
	{
		$request = $this->getRequest()->getParams();
		$this->_helper->layout->disableLayout();
		$this->view->resetcode = $request['resetcode'];
		$this->view->user = $request['user'];
	}

	public function resetpassAction()
	{
		$request = $this->getRequest()->getParams();
		$this->_helper->layout->disableLayout();
		$this->view->newpass = $request['newpass'];
		$this->view->user = $request['user'];
	}
	public function floginAction()
	{
		$this->getResponse()->setHeader('Cache-Control','no-store, no-cache, must-revalidate, post-check=0, pre-check=0',1);
	        $this->getResponse()->setHeader('Expires','Thu, 19 Nov 1981 08:52:00 GMT',1);
	        $this->getResponse()->setHeader('Pragma','no-cache',1);
		$this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
		$this->_redirect($this->facebooklogin());
	}
	public function indexAction()
	{	
		$this->_helper->layout->setLayout('layout');
		//echo $_SERVER['HTTP_USER_AGENT']. ' **' . $this->commonmodel_obj->getdevice();exit;
		
		



		$request = $this->getRequest()->getParams();
		if($request['sessionkey']!="") $this->view->sessionExpire = base64_decode($request['sessionkey']);
		$where = array('1'=>1);
        $socialloginabilitydetail = $this->myclientdetails->getRowMasterfromtable('tblloginsocialresource',array('*'),$where);
       
        $this->view->socialloginabilitydetail = $socialloginabilitydetail;
		$this->view->Social_Content_Block = $this->Social_Content_Block;
		
		if($_REQUEST['error']=='access_denied'){
			$this->_redirect('/');
		}
		$request = $this->getRequest()->getParams();
		if($request['sessionkey']!="") $this->view->sessionExpire = base64_decode($request['sessionkey']);
		$where = array('1'=>1);
        $socialloginabilitydetail = $this->myclientdetails->getRowMasterfromtable('tblloginsocialresource',array('*'),$where);
        $this->view->socialloginabilitydetail = $socialloginabilitydetail;
        
		$client = new apiClient();
		$client->setApplicationName("Dbee Login Application");
		$client->setScopes(array('https://www.googleapis.com/auth/userinfo.email','https://www.googleapis.com/auth/plus.me'));
		$plus = new apiPlusService($client);
		$plus2 = new apiOauth2Service($client);
		$filter_alnum = new Zend_Filter_Alnum();
		if (isset($_GET['code']))
		{
			$client->authenticate();
			$this->user_session->access_token_gplus = $client->getAccessToken();
			$this->_redirect("/");
		}

		if (isset($this->user_session->access_token_gplus) && $this->user_session->access_token_gplus!=''){
			$client->setAccessToken($this->user_session->access_token_gplus);
		}

		if ($client->getAccessToken())
		{
			
			
			if($this->getGlobalSettings()=='block')
			{
				$this->_helper->flashMessenger->addMessage('block'); 
				$this->_redirect("/");
			}
			 $me = $plus->people->get('me');
			 $email = $plus2->userinfo->get();
			 
             // print_r($me);die;

			 if($email['email']=='')
			 	$email['email'] = 'gplususer@db-csp.com';
			$geoplugin = new geoPlugin();
			$geoplugin->locate($this->_request->getServer('REMOTE_ADDR'));

			$result_array = $this->user_model->getSocialDetail($me['id']);
	   		if($result_array[0]['Status']==0 && !empty($result_array))
		    {
		    	unset($this->user_session->access_token_gplus);
				$this->_helper->flashMessenger->addMessage('Oops, your account seems to be deactivated.');
				$this->_helper->redirector->gotosimple('index','index',true); exit();
		    }
			else if($this->user_model->checkSocialEmail($me['id'], $this->myclientdetails->customEncoding($email['email'])))
			{
				unset($this->user_session->access_token_gplus);
				$this->_helper->flashMessenger->addMessage('Sorry! You have tried to login with an email address already registered with us');
				$this->_redirect("/");
			}
			else if(!empty($result_array) && $me['id']!='')
			{
				$user_personal_info['LastLoginIP']     = $this->_request->getServer('REMOTE_ADDR');
				$user_personal_info['LastLoginDate'] = date('Y-m-d H:i:s');
				$user_personal_info['Email'] = $email['email'];
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
				$user_personal_info['chatstatus'] = 1;
				$user_personal_info['isonline'] = 1;
				$this->user_model->updateSocialLogin($user_personal_info, $me['id']);
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
				$this->redirection($username);
			}else
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
				$user_personal_info['Name'] = $this->myclientdetails->customEncoding($me['displayName']);
				$user_personal_info['full_name'] = $this->myclientdetails->customEncoding($me['displayName']);
				$username = $this->myclientdetails->customEncoding(strtolower($filter_alnum->filter($me['name']['givenName'])));
				$user_personal_info['Username'] = $this->myclientdetails->customEncoding($this->Myhome_Model->chkusername($username));
				$user_personal_info['Email'] = $this->myclientdetails->customEncoding($email['email']);
				$user_personal_info['Pass'] = $this->_generateHash(time());
				$user_personal_info['Birthdate'] = '0000-00-00';
				$user_personal_info['LastLoginDate'] = date('Y-m-d H:i:s');
				$user_personal_info['DOBmakeprivate'] = 1;
				$user_personal_info['Gender'] = $this->myclientdetails->customEncoding(ucfirst($me['gender']));
				$user_personal_info['Socialid'] = $me['id'];
				$user_personal_info['Socialtype'] = 4;
				$user_personal_info['clientID'] = clientID;
				$user_personal_info['usertype'] = 0;
				$user_personal_info['showtagmsg'] = 1;
				if($me['image']['url'])
				{
					$filename = time().'.jpg';
					$dir = './userpics/'.$filename;
					$url = $me['image']['url'].'&sz=250';
					define('BUFSIZ', 40950);
					$rfile = @fopen($url, 'r');
					$lfile = @fopen($dir, 'w');
					while (!feof($rfile))
					@fwrite($lfile, fread($rfile, BUFSIZ), BUFSIZ);
					fclose($rfile);
					fclose($lfile);
					$user_personal_info['ProfilePic'] = $filename;

				}else
				{
					if($user_personal_info['Gender']=='Female')
                		{$user_personal_info['ProfilePic'] = 'default-avatar-female.jpg';}
                	elseif($user_personal_info['Gender']=='Male')
            	        {$user_personal_info['ProfilePic'] = 'default-avatar.jpg';}
                	else{$user_personal_info['ProfilePic'] = 'default-profilepic-std.png';}                     
				}
				
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
                if(adminID!=''){
					$data = array('clientID'=>clientID,'User'=>adminID,'FollowedBy'=>$lastInsertId,'StartDate'=>date('Y-m-d H:i:s'));
					$this->following->insertfollowing($data);
					
					$chatData = array('clientID'=> clientID,'sendto'=> adminID,'sendby'=> $lastInsertId,'status'=> 0,'dateofchat'=>date('Y-m-d H:i:s'));
           			$this->myclientdetails->insertdata_global('tblchatusers', $chatData);
			    }
				/************activated user follow to admin******************/
					
				// send welcome mail to facebook user 
				$this->sendMailToFaceBookUser($user_personal_info);
				// redirect to logined user
				$this->globalSettings(); // for social
				$this->user_session->loggedIn = true;
				$this->user_session->access_token_gplus = $client->getAccessToken();
				$username = $this->myclientdetails->customDecoding($user_personal_info['Username']);
				$this->redirection($username);
			}

		} else
			$this->view->Googleplus = $client->createAuthUrl();

		$this->user_session->token = '';
		$this->user_session->token = trim(md5(mt_rand(9, 400)));
		$this->view->token   = $this->user_session->token; // secure token code
		$flash  = $this->_helper->getHelper('flashMessenger');
		$this->view->form_status   = $this->user_session->form_status;
		if ($flash->hasMessages())
			$this->view->message = $flash->getMessages(); // set flash error message
		
		if($this->user_session->afterSignup)
		{
			$this->view->afterSignup = $this->user_session->afterSignup;
			$this->user_session->afterSignup = '';
		}
	}

	

	public function loginAction()
	{
		if ($this->getRequest()->getMethod() == 'POST')
		{
			$error_message = 'Incorrect log in details. Please try again.';
			
			$request['loginpass'] =  $this->_request->getPost('loginpass');
			$request['loginemail'] = $this->myclientdetails->customEncoding($this->_request->getPost('loginemail'));
			$request['remember_me'] = $this->_request->getPost('remember_me');

			$namespacexx = new Zend_Session_Namespace('zend_token'); 
			$salt = 'b79jsMaEzXMvCO2iWtzU2gT7rBoRmQzlvj5yNVgP4aGOrZ524pT5KoTDJ7vNiIN';
			$token = sha1($salt.$this->_request->getPost('tm'));			
			$request['posttoken'] =  $this->_request->getPost('token');


			$auth = Zend_Auth::getInstance();
			$registry = Zend_Registry::getInstance();
			if (empty($request['act_email']) && $token == $request['posttoken']) 
			{
				$userresult = $this->myclientdetails->getfieldsfromtable('*','tblUsers','Email',$request['loginemail'],'clientID',clientID);
		
				if ((isset($request['loginemail']) && !empty($request['loginemail'])) && (isset($request['loginpass']) && !empty($request['loginpass'])) && (isset($userresult[0]['Status']) && !empty($userresult[0]['Status']))) {
					
					$filter = new Zend_Filter_Decrypt();
					$config = Zend_Registry::get('config');
		            $db = Zend_Db::factory('Pdo_Mysql', $config->resources->db->params);
					$adapter = new Zend_Auth_Adapter_DbTable($db);
					$adapter->setTableName('tblUsers');
					$adapter->setIdentityColumn('Email');
					$adapter->setCredentialColumn('Pass');
					$adapter->setIdentity($request['loginemail']);
					$chkPass = $adapter->setCredential($adapter->setCredentialdbee($request['loginpass'], $userresult[0]['Pass']));
					
					$select = $adapter->getDbSelect();
	                $select->where('clientID = ?',clientID);

					$result = $auth->authenticate($adapter);
									
					if ($result->isValid())
					{
					
						$userinfodata = array(
	                        'UserID' => $adapter->getResultRowObject()->UserID,
	                        'clientID' => $adapter->getResultRowObject()->clientID ,
	                        'Title' => $adapter->getResultRowObject()->title,
	                        'company' => $adapter->getResultRowObject()->company,
	                        'Email' => $adapter->getResultRowObject()->Email,
	                        'Name' => $adapter->getResultRowObject()->Name,
	                        'lname' => $adapter->getResultRowObject()->lname,
	                        'full_name' => $adapter->getResultRowObject()->full_name,
	                        'Username' => $adapter->getResultRowObject()->Username,
							'usertype' => $adapter->getResultRowObject()->usertype,
	                        'Fbname' => $adapter->getResultRowObject()->Fbname,
	                        'Address' => $adapter->getResultRowObject()->Address,
	                        'City' => $adapter->getResultRowObject()->City,
	                        'Gender' => $adapter->getResultRowObject()->Gender,
	                        'Birthdate' => $adapter->getResultRowObject()->Birthdate,
	                        'ProfilePic' => $adapter->getResultRowObject()->ProfilePic,
	                        'ActCode' => $adapter->getResultRowObject()->ActCode,
	                        'ShowPPBox' => $adapter->getResultRowObject()->ShowPPBox,
	                        'SocialFB' => $adapter->getResultRowObject()->SocialFB,
	                        'Socialtype' => $adapter->getResultRowObject()->Socialtype,
	                        'SocialTwitter' => $adapter->getResultRowObject()->SocialTwitter,
	                        'SocialLinkedin' => $adapter->getResultRowObject()->SocialLinkedin,
	                        'ResetPassCode' => $adapter->getResultRowObject()->ResetPassCode,
	                        'CookieAccept' => $adapter->getResultRowObject()->CookieAccept,
	                        'ScoringStatus' => $adapter->getResultRowObject()->ScoringStatus,
	                        'RegistrationDate' => $adapter->getResultRowObject()->RegistrationDate,
	                        'LastUpdateDate' => $adapter->getResultRowObject()->LastUpdateDate,
	                        'LastLoginDate' => $adapter->getResultRowObject()->LastLoginDate,
	                        'LastLoginSeenDate' => $adapter->getResultRowObject()->LastLoginSeenDate,
	                        'IP' => $adapter->getResultRowObject()->IP,
	                        'LastLoginIP' => $adapter->getResultRowObject()->LastLoginIP,
	                        'Status' => $adapter->getResultRowObject()->Status,
	                        'state' => $adapter->getResultRowObject()->state,
	                        'twitter_connect_data' => $adapter->getResultRowObject()->twitter_connect_data,
	                        'facebook_connect_data' => $adapter->getResultRowObject()->facebook_connect_data,
	                        'linkedin_connect_data' => $adapter->getResultRowObject()->linkedin_connect_data,
	                        'usertype' => $adapter->getResultRowObject()->usertype,
	                        'role' => $adapter->getResultRowObject()->role,
	                        'groupid' => $adapter->getResultRowObject()->groupid,
	                        'showtagmsg' => $adapter->getResultRowObject()->showtagmsg
						);

	                    
						$UserID    = $adapter->getResultRowObject()->UserID;
						$geoplugin = new geoPlugin();
						if ($_SERVER['SERVER_NAME'] == 'localhost')
	                	$geoplugin->locate('182.64.210.137'); // for local
	                	else
	                	$geoplugin->locate($this->_request->getServer('REMOTE_ADDR'));		

						$users['LastLoginIP']     = $this->_request->getServer('REMOTE_ADDR');
	                	$users['browser']         = $this->commonmodel_obj->getbrowser();
	                	$users['os']              = $this->commonmodel_obj->getos();
	                	$users['userdevice']      = $this->commonmodel_obj->getdevice();
						$users['city']            = $this->myclientdetails->customEncoding($geoplugin->city);
						$users['region']          = $this->myclientdetails->customEncoding($geoplugin->region);
						$users['area_code']       = $this->myclientdetails->customEncoding($geoplugin->areaCode);
						$users['dma']             = $this->myclientdetails->customEncoding($geoplugin->dmaCode);
						$users['country_code']    = $this->myclientdetails->customEncoding($geoplugin->countryCode);
						$users['country_name']    = $this->myclientdetails->customEncoding($geoplugin->countryName);
						$users['continent_name']  = $this->myclientdetails->customEncoding($this->commonmodel_obj->getcontinent($geoplugin->continentCode));
						$users['longitude']       = $this->myclientdetails->customEncoding($geoplugin->longitude);
						$users['latitude']        = $this->myclientdetails->customEncoding($geoplugin->latitude);
						$users['currency_code']   = $this->myclientdetails->customEncoding($geoplugin->currencyCode);
						$users['currency_symbol'] = $this->myclientdetails->customEncoding($geoplugin->currencySymbol);

						$users['chatstatus'] = 1;
						$users['isonline'] = 1;

	                    $addStatus  = $this->myclientdetails->updatedata_global('tblUsers',$users,'UserID',$UserID);

						$this->myclientdetails->sessionWrite($userinfodata);
				
						$this->user_session->loggedIn = true;
						$authNamespace = new Zend_Session_Namespace('identify');
						$authNamespace->setExpirationSeconds((1209600));
						$authNamespace->role = $userinfodata['role'];
						$authNamespace->id = $userinfodata['UserID'];
						$authNamespace->user = $userinfodata['Username'];
						$authNamespace->lockedtogroup = $userinfodata['groupid'];

						//$chkrss = $this->myclientdetails->getfieldsfromtable('ID','tblUserRss','User',$userinfodata['UserID'],'clientID',clientID);
	                    
	                    $this->notification->commomInsert('9','9','',$userinfodata['UserID'],$userinfodata['UserID'],'',''); // activity table insert
	
	                    $rss   = new Application_Model_Rss();
                 		$rss->insertactivefeed($userinfodata['UserID']);
	                   // set cookie
	                   if($request['remember_me']==1)
	                   {
                       	setcookie('RememberEmail', $this->myclientdetails->customEncoding($this->_request->getPost('loginemail')), time() + 2592000, '/');
                       	setcookie('Rememberpass', $this->myclientdetails->customEncoding($request['loginpass']), time() + 2592000, '/');
                       }
                       else
                       {
                       	setcookie('RememberEmail', "", time() + 2592000, '/');
                       	setcookie('Rememberpass', "", time() + 2592000, '/');
                       }

	                   //set cookie
	                   
						$this->globalSettings(); // for social
						if($userinfodata['UserID'] == adminID)
							$this->Myhome_Model->updateGlobalLogin();	

						$username = $this->myclientdetails->customDecoding($userinfodata['Username']);
						//echo $this->user_session->redirection; exit;
						$this->redirection($username);
					}
					else 
					{
						$this->_helper->flashMessenger->addMessage($error_message);
						$this->user_session->form_status = 0;
						$this->_redirect('/');
					}
				} else {
					$this->_helper->flashMessenger->addMessage($error_message);
					$this->user_session->form_status = 0;
					$this->_redirect('/');
				}
			} else {
				$this->_redirect('/?invalid path');			
			}
		}else {
				$this->_redirect('/?invalid path');			
			}
	}

	public function requestsignupAction()
	{
	    $requestani = $this->getRequest()->getParams();
	    //echo'<pre>';print_r($requestani);die;
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);		
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$flag = 0;
			$error_message = '';
			$this->myclientdetails = new Application_Model_Clientdetails();
			$common_model = new Application_Model_Commonfunctionality();			
			$MyhomeModel = new Application_Model_Myhome(); 						
			$valid = new Zend_Validate_NotEmpty();
			$filter = new Zend_Filter_StripTags();
			$validator_username = new Zend_Validate_Alnum();
			$validator_email = new Zend_Validate_EmailAddress();
			$filter_alnum = new Zend_Filter_Alnum();
			$validator_password = new Zend_Validate();
			$alpha_validator = new Zend_Validate_Alpha();
			$filter = new Zend_Filter_StripTags();
			
			
			$validator_date = new Zend_Validate_Date(array(
            'format' => 'YY|MM|DD',
            'locale' => 'de'
            ));
             
            $accept_terms_condition = $this->_request->getPost('accept_terms_condition');	            
            $cookiescheck = $filter->filter($this->_request->getPost('cookiescheck'));
            $validate = $common_model->authorizeToken($this->_request->getPost('SessUser__'),$this->_request->getPost('SessId__'),$this->_request->getPost('SessName__'),$this->_request->getPost('Token__'),$this->_request->getPost('Key__'));

            $checkuser = $this->myclientdetails->getfieldsfromtable('*','tblUsers','Email',$this->myclientdetails->customEncoding($filter->filter($this->_request->getPost('email'))),'clientID',clientID);
            $cheUseno = count($checkuser);
            
            if (!$valid->isValid($this->_request->getPost('firstname'))) {
            	$error_message.= "<p>Enter your first name.</p>";
            	$flag = 1;
            }
            else if (!$valid->isValid($this->_request->getPost('lastname'))) {
            	$error_message.= "<p>Enter your last name.</p>";
            	$flag = 1;
            }
            else if (!$valid->isValid($this->_request->getPost('email'))) {
            	$error_message.= "<p>Enter your email address.</p>";
            	$flag = 1;
            }
            else if ($cheUseno>0){
            	$error_message.= "<p>Someone already has that email.</p>";
            	$flag = 1;
            }
            else if($flag == 0)
            {
                
            	$user_personal_info = array();
            	$username = $filter->filter(strtolower($filter_alnum->filter($this->_request->getPost('firstname')))).$filter->filter(strtolower($filter_alnum->filter($this->_request->getPost('lastname'))));
            	
            	$user_personal_info['Status'] = 2;
            	$user_personal_info['Name'] = $this->myclientdetails->customEncoding($filter->filter($this->_request->getPost('firstname')));
            	$user_personal_info['lname'] = $this->myclientdetails->customEncoding($filter->filter($this->_request->getPost('lastname')));

            	$user_personal_info['full_name'] = $this->myclientdetails->customEncoding($filter->filter($this->_request->getPost('firstname')).' '.$filter->filter($this->_request->getPost('lastname')));

            	$user_personal_info['Username'] = $this->myclientdetails->customEncoding($MyhomeModel->chkusername($this->myclientdetails->customEncoding($username)));
            	$user_personal_info['clientID'] = clientID; 
            	
            	$user_personal_info['Email'] = $this->myclientdetails->customEncoding($filter->filter($this->_request->getPost('email')));
            	
            	
            	$user_personal_info['RegistrationDate'] = date('Y-m-d H:i:s');                	
            	/* entry by user */
            	/*  basic location  need  based on ip */
            	$geoplugin = new geoPlugin();
	           
	            if ($_SERVER['SERVER_NAME'] == 'localhost')
	            	$geoplugin->locate('182.64.210.137'); // for local
	            else
	            	$geoplugin->locate($this->_request->getServer('REMOTE_ADDR'));
               
            	$user_personal_info['LastLoginIP']     = $this->_request->getServer('REMOTE_ADDR');
            	$user_personal_info['browser']         = $common_model->getbrowser();
            	$user_personal_info['os']              = $common_model->getos();
            	$user_personal_info['reg_City']            = $geoplugin->city;
            	$user_personal_info['reg_region']          = $this->myclientdetails->customEncoding($geoplugin->region);
            	$user_personal_info['reg_area_code']       = $this->myclientdetails->customEncoding($geoplugin->areaCode);
            	$user_personal_info['reg_dma']             = $this->myclientdetails->customEncoding($geoplugin->dmaCode);
            	$user_personal_info['reg_country_code']    = $this->myclientdetails->customEncoding($geoplugin->countryCode);
            	$user_personal_info['reg_country_name']    = $this->myclientdetails->customEncoding($geoplugin->countryName);
            	$user_personal_info['reg_continent_name']  = $this->myclientdetails->customEncoding($common_model->getcontinent($geoplugin->continentCode));
            	$user_personal_info['longitude']       = $this->myclientdetails->customEncoding($geoplugin->longitude);
            	$user_personal_info['latitude']        = $this->myclientdetails->customEncoding($geoplugin->latitude);
            	$user_personal_info['reg_currency_code']   = $this->myclientdetails->customEncoding($geoplugin->currencyCode);
            	$user_personal_info['reg_currency_symbol'] = $this->myclientdetails->customEncoding($geoplugin->currencySymbol);
            	$user_personal_info['Socialtype'] = 0;
            	$user_personal_info['Signuptoken'] = md5(time());
            	$user_personal_info['usertype'] = 6;
            	/* basic location  need  based on ip */

            	
            	
            	$returnres =$this->myclientdetails->insertdata_global('tblUsers',$user_personal_info);
            	$carerid = $this->_request->getPost('carrerid');
            	
            	if($carerid>0)
	            {
	              $this->myclientdetails->insertdata_global('tblUserMeta',array('clientID'=>clientID,'UserId'=>$returnres,'carerid'=>$carerid,'date_added'=>date("Y-m-d H:i:s")));
	            }
            	
            	$notify = array('User' => $returnres,'clientID'=>clientID);
            	$this->myclientdetails->insertdata_global('tblNotificationSettings',$notify);

            	$rss   = new Application_Model_Rss();
            	$rss->insertactivefeed($returnres);
            	
            	
            }
		}
		$data['flag'] = $flag;
		$data['error_message'] = $error_message;
		$data['username'] = $this->myclientdetails->customDecoding($user_personal_info['Username']);
		$data['Name'] = $this->myclientdetails->customDecoding($user_personal_info['Name']);
		$data['lname'] = $this->myclientdetails->customDecoding($user_personal_info['lname']);
		return $response->setBody(Zend_Json::encode($data));
	}

	public function signupAction()
	{
	    $requestani = $this->getRequest()->getParams();
	    //echo'<pre>';print_r($requestani);die;
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);		
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$flag = 0;
			$error_message = '';
			$this->myclientdetails = new Application_Model_Clientdetails();
			$common_model = new Application_Model_Commonfunctionality();			
			$MyhomeModel = new Application_Model_Myhome(); 						
			$valid = new Zend_Validate_NotEmpty();
			$filter = new Zend_Filter_StripTags();
			$validator_username = new Zend_Validate_Alnum();
			$validator_email = new Zend_Validate_EmailAddress();
			$filter_alnum = new Zend_Filter_Alnum();
			$validator_password = new Zend_Validate();
			$alpha_validator = new Zend_Validate_Alpha();
			$filter = new Zend_Filter_StripTags();
			
			$validator_password->addValidator(new Zend_Validate_StringLength(array(
                'min' => 8,
                'max' => 16
			)));  // for future use if we need validate char limit validation

			$date_of_birth = (int)$this->_request->getPost('birthdayyear') . '-' . (int)$this->_request->getPost('birthmonth') . '-' . (int)$this->_request->getPost('birthdayday');
			$validator_date = new Zend_Validate_Date(array(
            'format' => 'YY|MM|DD',
            'locale' => 'de'
            ));
             
            $accept_terms_condition = $this->_request->getPost('accept_terms_condition');	            
            $cookiescheck = $filter->filter($this->_request->getPost('cookiescheck'));
            $validate = $common_model->authorizeToken($this->_request->getPost('SessUser__'),$this->_request->getPost('SessId__'),$this->_request->getPost('SessName__'),$this->_request->getPost('Token__'),$this->_request->getPost('Key__'));

            $checkuser = $this->myclientdetails->getfieldsfromtable('*','tblUsers','Email',$this->myclientdetails->customEncoding($filter->filter($this->_request->getPost('email'))),'clientID',clientID);
            $cheUseno = count($checkuser);
            if($this->Social_Content_Block=='block')
        	{
        		$error_message.= "<p>All social sharing and connections have been disabled by the platform admin.</p>";
            	$flag = 1;
        	}
            if ($validate==false) {
            	$error_message.= "<p>Some thing went wrong here please try again</p>";
            	$flag = 1;
            }
            else if (!$valid->isValid($this->_request->getPost('firstname'))) {
            	$error_message.= "<p>Enter your first name.</p>";
            	$flag = 1;
            }
            else if (!$valid->isValid($this->_request->getPost('lastname'))) {
            	$error_message.= "<p>Enter your last name.</p>";
            	$flag = 1;
            }
            else if (!$valid->isValid($this->_request->getPost('email'))) {
            	$error_message.= "<p>Enter your email address.</p>";
            	$flag = 1;
            }
            else if (!$validator_email->isValid($this->_request->getPost('email'))) {
            	$error_message.= "<p>Please enter your valid email address.</p>";
            	$flag = 1;
            }
            else if ($cheUseno>0){
            	$error_message.= "<p>Someone already has that email.</p>";
            	$flag = 1;
            }
            else if (!$valid->isValid($this->_request->getPost('passworddbee'))) {
            	$error_message.= "<p>Please provide a password.</p>";
            	$flag = 1;
            }
            else if (!$valid->isValid($this->_request->getPost('gender'))) {
            	$error_message.= "<p>Please select your gender</p>";
             $flag = 1;
            }
            else if (!$valid->isValid($this->_request->getPost('gender'))) {
            	$error_message.= "<p>Please select your gender</p>";
            	$flag = 1;
            }
            /*else if (!$alpha_validator->isValid($this->_request->getPost('gender'))) {
            	$error_message.= "<p>Please select your gender</p>";
            	$flag = 1;
            }
            else if (!$validator_date->isValid($date_of_birth)) {
            	$error_message.= "<p>Please select your date of birth</p>";
             $flag = 1;
            }*/
            else if (!isset($accept_terms_condition)) {
            	$error_message.= "<p>Please accept terms and conditions</p>";
            	$flag = 1;
            }
           /* else if (!isset($cookiescheck)) {
            	$error_message.= "<p>Please accept the use of cookies on this site</p>";
            	$flag = 1;
            }*/
            else if($flag == 0)
            {
                
            	$user_personal_info = array();
            	$username = $filter->filter(strtolower($filter_alnum->filter($this->_request->getPost('firstname')))).$filter->filter(strtolower($filter_alnum->filter($this->_request->getPost('lastname'))));
            	
            	$user_personal_info['Status'] = 0;
            	$user_personal_info['Name'] = $this->myclientdetails->customEncoding($filter->filter($this->_request->getPost('firstname')));
            	$user_personal_info['lname'] = $this->myclientdetails->customEncoding($filter->filter($this->_request->getPost('lastname')));

            	$user_personal_info['full_name'] = $this->myclientdetails->customEncoding($filter->filter($this->_request->getPost('firstname')).' '.$filter->filter($this->_request->getPost('lastname')));

            	$user_personal_info['Username'] = $this->myclientdetails->customEncoding($MyhomeModel->chkusername($this->myclientdetails->customEncoding($username)));
            	$user_personal_info['clientID'] = clientID; 
            	$user_personal_info['Pass'] = $this->_generateHash($this->_request->getPost('passworddbee'));// secure password generated on _generateHash() function
            	$user_personal_info['Email'] = $this->myclientdetails->customEncoding($filter->filter($this->_request->getPost('email')));
            	$user_personal_info['Gender'] = $this->myclientdetails->customEncoding($this->_request->getPost('gender'));
            	$user_personal_info['Birthdate'] = $date_of_birth;

            if($user_personal_info['Gender']=='Female')
            	{$user_personal_info['ProfilePic'] = 'default-avatar-female.jpg';}
            elseif($user_personal_info['Gender']=='Male')
            	{$user_personal_info['ProfilePic'] = 'default-avatar.jpg';}
            else{$user_personal_info['ProfilePic'] = 'default-profilepic-std.png';}
            	
            	$user_personal_info['RegistrationDate'] = date('Y-m-d H:i:s');                	
            	/* entry by user */
            	/*  basic location  need  based on ip */
            $geoplugin = new geoPlugin();
            if ($_SERVER['SERVER_NAME'] == 'localhost')
            	$geoplugin->locate('182.64.210.137'); // for local
            else
            	$geoplugin->locate($this->_request->getServer('REMOTE_ADDR'));
               
            	$user_personal_info['LastLoginIP']     = $this->_request->getServer('REMOTE_ADDR');
            	$user_personal_info['browser']         = $common_model->getbrowser();
            	$user_personal_info['os']              = $common_model->getos();
            	$user_personal_info['reg_City']            = $geoplugin->city;
            	$user_personal_info['reg_region']          = $this->myclientdetails->customEncoding($geoplugin->region);
            	$user_personal_info['reg_area_code']       = $this->myclientdetails->customEncoding($geoplugin->areaCode);
            	$user_personal_info['reg_dma']             = $this->myclientdetails->customEncoding($geoplugin->dmaCode);
            	$user_personal_info['reg_country_code']    = $this->myclientdetails->customEncoding($geoplugin->countryCode);
            	$user_personal_info['reg_country_name']    = $this->myclientdetails->customEncoding($geoplugin->countryName);
            	$user_personal_info['reg_continent_name']  = $this->myclientdetails->customEncoding($common_model->getcontinent($geoplugin->continentCode));
            	$user_personal_info['longitude']       = $this->myclientdetails->customEncoding($geoplugin->longitude);
            	$user_personal_info['latitude']        = $this->myclientdetails->customEncoding($geoplugin->latitude);
            	$user_personal_info['reg_currency_code']   = $this->myclientdetails->customEncoding($geoplugin->currencyCode);
            	$user_personal_info['reg_currency_symbol'] = $this->myclientdetails->customEncoding($geoplugin->currencySymbol);
            	$user_personal_info['Socialtype'] = 0;
            	$user_personal_info['Signuptoken'] = md5(time());
            	/* basic location  need  based on ip */

            	if($this->user_session->redirection)
            	{
            		$redirect = $this->user_session->redirection;
            		$this->user_session->redirection = '';
            		$user_personal_info['redirect'] = $redirect;
            	}
            	
            	$returnres =$this->myclientdetails->insertdata_global('tblUsers',$user_personal_info);
            	
            	//$notify = array('User' => $returnres,'clientID'=>clientID);
            	//$this->myclientdetails->insertdata_global('tblNotificationSettings',$notify);

            	$rss   = new Application_Model_Rss();
            	$rss->insertactivefeed($returnres);
            	$this->signupmail($user_personal_info);
            	$this->user_session->afterSignup = $user_personal_info['Email'];
            }
		}
		$data['flag'] = $flag;
		$data['error_message'] = $error_message;
		$data['username'] = $this->myclientdetails->customDecoding($user_personal_info['Username']);
		$data['Name'] = $this->myclientdetails->customDecoding($user_personal_info['Name']);
		$data['lname'] = $this->myclientdetails->customDecoding($user_personal_info['lname']);
		return $response->setBody(Zend_Json::encode($data));
	}

	
	public function createrandontokenAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type','application/json',true);
		
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
			if($_SERVER['REMOTE_ADDR']== REMOTE_IP){
				session_start();
		 		$request 		=	$this->getRequest()->getPost();
		 		$User_tokens 	= 	new Zend_Session_Namespace('User_tokens');
		 		$respo 			=	array();
		 		
		 		if (empty($request['captchakey'] )) {		 
	            	$respo['errormsg']= "Please use valid source in order to post data!";
	        	} else {                   
	        		$respo['SessUser__']= trim(md5(mt_rand(9, 400)));
	        		$respo['SessId__']	= trim(md5(time()));
	        		$respo['SessName__']= trim(md5(mt_rand(9, 400)));
	        		$respo['Token__']	= trim(md5(time()));
	        		$respo['Key__']		= trim(md5(time()));

	        		$respo['errormsg']= "404";

	        		$User_tokens->startupkey  =  $respo['SessUser__'].md5('adam').$respo['SessId__'].md5('guy').$respo['SessName__'];
	        	}
		 	} else {
		 		$respo['errormsg']= "Please use valid source in order to post data!";
		 	}
	 		
	 		return $response->setBody(Zend_Json::encode($respo));
			
		}
		exit;
	}

	/*for createanet*/

	private function _generateHash($plainText, $salt = null)
	{
		define('SALT_LENGTH', 9);
		if ($salt === null)
			$salt = substr(md5(uniqid(rand(), true)), 0, SALT_LENGTH);
		else 
			$salt = substr($salt, 0, SALT_LENGTH);
		return $salt . sha1($salt . $plainText);
	}

    public function signupmail($requestval)
	{	
	 if (isset($requestval)) {
	 /****** for email *******/	
	 $EmailTemplateArray = array('Email' => $requestval['Email'],
		                         'signupName'=>$requestval['Name'],
		                         'lname' => $requestval['lname'],
		                         'siteUrl'=>FRONT_URL,
		                         'Signuptoken'=>$requestval['Signuptoken'],
		                         'case'=>6);
     $this->dbeeComparetemplateOne($EmailTemplateArray);	
	 /****** for email *******/		
	 }
	}

	// @
	// ** Activate Email function ** //
	// @@

	public function activelinkAction()
	{
		$this->_helper->layout()->disableLayout();
		$request  = $this->getRequest()->getParams();
        
		$common_model = new Application_Model_Commonfunctionality();
		$filter = new Zend_Filter_StripTags();
		$chkUser = $this->myclientdetails->getfieldsfromtable('*','tblUsers','Signuptoken',$filter->filter($request['id']),'clientID',clientID);
		
		$this->view->AlreadyActivated = $chkUser[0]['Status'];
		$this->view->notificationpage = $request['notify'];
		if (($this->view->AlreadyActivated==0) && !empty($chkUser))
		{
		    $this->view->AlreadyActivated = 0;
			$this->view->username         = $this->myclientdetails->customDecoding($chkUser[0]['Name']);
		    $this->view->Email            = $this->myclientdetails->customDecoding($chkUser[0]['Email']);
		    /************* check user email exist in  tblusers table ****** */
			if (isset($chkUser) && !empty($chkUser))
			{
              
				/******* activated user account ********** */
			  $data_array = array('Status' => '1','Signuptoken' => '0','activeFirstTime' => '1','LastLoginDate'=>date('Y-m-d H:i:s'));
              $activated = $this->myclientdetails->updatedata_global('tblUsers',$data_array,'Email',$chkUser[0]['Email']); 
			  if ($activated=='1') {
                   	/************activated user follow to admin******************/
					$this->following  = new Application_Model_Following();
                    if(adminID!=''){
						$data = array('clientID'=>clientID,'User'=>adminID,'FollowedBy'=>$chkUser[0]['UserID'],'StartDate'=>date('Y-m-d H:i:s'));
						$this->following->insertfollowing($data);

						$chatData = array('clientID'=> clientID,'sendto'=> adminID,'sendby'=> $chkUser[0]['UserID'],'status'=> 0,'dateofchat'=>date('Y-m-d H:i:s'));
           				$this->myclientdetails->insertdata_global('tblchatusers', $chatData);
				    }


					/************fill notification settings table******************/
					$notify = array('User' => $chkUser[0]['UserID'],'clientID'=>clientID);
					$this->myclientdetails->insertdata_global('tblNotificationSettings',$notify);
					/************fill notification settings table******************/


					/************activated user follow to admin******************/


                   /*$urss = $this->myclientdetails->getfieldsfromtable('ID','tblUserRss','User',$chkUser[0]['UserID'],'clientID',clientID.'Active','1');                   
                   if(count($urss)<1){                   	 
                    for ($s = 1; $s < 5; $s++)
					{

						$datarss = array(
							    'clientID' => clientID,
								'Site' => $s,
								'User' => $chkUser[0]['UserID']
						);
						
						$insertrss = $this->myclientdetails->insertdata_global('tblUserRss',$datarss);
					}
                   }*/
	                 $rss   = new Application_Model_Rss();
	                 $rss->insertactivefeed($chkUser[0]['UserID']);
			        /****** for email *******/
			        $EmailTemplateArray = array('Email' => $chkUser[0]['Email'],
			                                    'Name'=>$chkUser[0]['Name'],
			                                    'lname' => $chkUser[0]['lname'],
			                                    'case'=>7);
	                $bodyContentmsgall = $this->dbeeComparetemplateOne($EmailTemplateArray);
	                /****** for email *******/                
		        	$auth = Zend_Auth::getInstance();
					$registry = Zend_Registry::getInstance();
                    $userresult = $this->myclientdetails->getfieldsfromtable('*','tblUsers','Email',$chkUser[0]['Email'],'clientID',clientID);
					
					if (isset($userresult[0]['Email']) ) {
						$filter = new Zend_Filter_Decrypt();
						$config = Zend_Registry::get('config');
			            $db = Zend_Db::factory('Pdo_Mysql', $config->resources->db->params);			            			            
						$adapter = new Zend_Auth_Adapter_DbTable($db);
						$adapter->setTableName('tblUsers');
						$adapter->setIdentityColumn('Email');
						$adapter->setCredentialColumn('Pass');
						$adapter->setIdentity($userresult[0]['Email']);
						$adapter->setCredential($userresult[0]['Pass'], $userresult[0]['Pass']);
						$select = $adapter->getDbSelect();
                        $select->where('clientID = ?',clientID);
						$result = $auth->authenticate($adapter);											
						if ($result->isValid()) 
						{						
							$userinfodata = array(
		                        'UserID' => $adapter->getResultRowObject()->UserID,
		                        'role' => $adapter->getResultRowObject()->role,
		                        'clientID' => $adapter->getResultRowObject()->clientID,
		                        'Email' => $adapter->getResultRowObject()->Email,
		                        'Name' => $adapter->getResultRowObject()->Name,
		                        'lname' => $adapter->getResultRowObject()->lname,
		                        'usertype' => $adapter->getResultRowObject()->usertype,
		                        'Username' => $adapter->getResultRowObject()->Username,
		                        'Fbname' => $adapter->getResultRowObject()->Fbname,
		                        'Address' => $adapter->getResultRowObject()->Address,
		                        'City' => $adapter->getResultRowObject()->City,
		                        'Gender' => $adapter->getResultRowObject()->Gender,
		                        'Birthdate' => $adapter->getResultRowObject()->Birthdate,
		                        'ProfilePic' => $adapter->getResultRowObject()->ProfilePic,
		                        'ActCode' => $adapter->getResultRowObject()->ActCode,
		                        'ShowPPBox' => $adapter->getResultRowObject()->ShowPPBox,
		                        'SocialFB' => $adapter->getResultRowObject()->SocialFB,
		                        'SocialTwitter' => $adapter->getResultRowObject()->SocialTwitter,
		                        'SocialLinkedin' => $adapter->getResultRowObject()->SocialLinkedin,
		                        'ResetPassCode' => $adapter->getResultRowObject()->ResetPassCode,
		                        'CookieAccept' => $adapter->getResultRowObject()->CookieAccept,
		                        'ScoringStatus' => $adapter->getResultRowObject()->ScoringStatus,
		                        'RegistrationDate' => $adapter->getResultRowObject()->RegistrationDate,
		                        'LastUpdateDate' => $adapter->getResultRowObject()->LastUpdateDate,
		                        'LastLoginDate' => $adapter->getResultRowObject()->LastLoginDate,
		                        'LastLoginSeenDate' => $adapter->getResultRowObject()->LastLoginSeenDate,
		                        'IP' => $adapter->getResultRowObject()->IP,
		                        'LastLoginIP' => $adapter->getResultRowObject()->LastLoginIP,
		                        'Status' => $adapter->getResultRowObject()->Status,
		                        'state' => $adapter->getResultRowObject()->state,
		                        'twitter_connect_data' => $adapter->getResultRowObject()->twitter_connect_data,
		                        'facebook_connect_data' => $adapter->getResultRowObject()->facebook_connect_data,
		                        'showtagmsg' => $adapter->getResultRowObject()->showtagmsg
							);
							
                            
							$UserID    = $adapter->getResultRowObject()->UserID;
							$geoplugin = new geoPlugin();
							if ($_SERVER['SERVER_NAME'] == 'localhost')
		                	$geoplugin->locate('182.64.210.137'); // for local
		                	else
                            
 
		                	$geoplugin->locate($this->_request->getServer('REMOTE_ADDR'));					
							$users['LastLoginIP']     = $this->_request->getServer('REMOTE_ADDR');
		                	$users['browser']         = $common_model->getbrowser();
		                	$users['os']              = $common_model->getos();
		                	$users['userdevice']      = $common_model->getdevice();
							$users['city']            = $this->myclientdetails->customEncoding($geoplugin->city);
							$users['region']          = $this->myclientdetails->customEncoding($geoplugin->region);
							$users['area_code']       = $this->myclientdetails->customEncoding($geoplugin->areaCode);
							$users['dma']             = $this->myclientdetails->customEncoding($geoplugin->dmaCode);
							$users['country_code']    = $this->myclientdetails->customEncoding($geoplugin->countryCode);
							$users['country_name']    = $this->myclientdetails->customEncoding($geoplugin->countryName);
							$users['continent_name']  = $this->myclientdetails->customEncoding($common_model->getcontinent($geoplugin->continentCode));
							$users['longitude']       = $this->myclientdetails->customEncoding($geoplugin->longitude);
							$users['latitude']        = $this->myclientdetails->customEncoding($geoplugin->latitude);
							$users['currency_code']   = $this->myclientdetails->customEncoding($geoplugin->currencyCode);
							$users['currency_symbol'] = $this->myclientdetails->customEncoding($geoplugin->currencySymbol);

							$users['chatstatus'] = 1;
							$users['isonline'] = 1;
		                    
							$addStatus  = $this->myclientdetails->updatedata_global('tblUsers',$users,'UserID',$UserID);


							$this->notification->commomInsert('8','8','',$UserID,$UserID,'','');

							
							$this->myclientdetails->sessionWrite($userinfodata);
				

							$userinfodata['redirect'] = '';
                            
							if($userinfodata['usertype']==10){$inserhome->updateGlobalLogin();}

							$this->view->username = $this->myclientdetails->customDecoding($userinfodata['Username']);
							$this->view->fullname = $this->myclientdetails->customDecoding($userinfodata['Name']);
							// $this->redirection($username);
						}
					}
				}

			}
		
		}
		else
		{
			 $this->view->AlreadyActivated = 1;
		}
	}



	
	public function forgotpassAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{

				 $content  ='<h2>Forgotten Password?<h2><div id="passform" class="postTypeContent forgotPass">
	                        <div class="pasDroprow"><input type="text" class="textfield required" id="forgotemail" placeholder="Please enter your email address"></div>
				            <div id="forgotpass-message"></div></div>';
			
			$data['status'] = 'success';
			$data['content']= $content;
		}
		return $response->setBody(Zend_Json::encode($data));

	}
}