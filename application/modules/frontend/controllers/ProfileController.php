<?php
class ProfileController extends IsController
{
		public function init()
		{
			parent::init();
            $cat  = new Application_Model_Category();
            $this->view->cat = $cat->getallcategory();
			$storage 	= new Zend_Auth_Storage_Session();
			$auth        =  Zend_Auth::getInstance();
			if($auth->hasIdentity())
			{
				$data	  	= $storage->read();
				$this->_userid = $data['UserID'];
			}
			else
				$this->_helper->redirector->gotosimple('index','index',true);

		}
	    public function changeusernameAction()
	    {
	    	if ($this->getRequest()->isXmlHttpRequest()) 
			{
		    	$this->_helper->layout()->disableLayout();
				$this->_helper->viewRenderer->setNoRender(true);
		    	$request 	= $this->getRequest()->getParams();
		    	$response 	= $this->getResponse();
				$response->setHeader('Content-type', 'application/json', true);
				$userid 	= $this->_userid;
				$uname 		= $this->myclientdetails->customEncoding(strtolower($request['uname']));
		    	$returnArr	=	array();		    	
		    	$suggestion =	array();
		    	$finalsuggestion =	array();
		    	$finalsuggestionret = '';
				
		    	if($request['uname']!='')
		    	{
		    		$chkunameexist	=	$this->myclientdetails->getfieldsfromtable(array('Name'),'tblUsers','Username',$uname);
		    		if(count($chkunameexist)>0)
		    		{
		    			$newNm 	=	explode(' ', $this->myclientdetails->customDecoding($this->session_data['Name']));
		    			for ($i=0; $i < 3 ; $i++) { 
		    				$suggesstion[]	=	$newNm[0].'.'.$newNm[1].(date('Y')+$i);	
		    			}
		    			for ($j=0; $j < 3 ; $j++) { 
		    				$suggesstion[]	=	$newNm[1].'.'.$newNm[0].(date('m')+$j);	
		    			}
		    			for ($k=0; $k < 3 ; $k++) { 
		    				$suggesstion[]	=	$newNm[0].'.'.$newNm[1].(rand(10,100)+$k);	
		    			}
		    			for ($h=0; $h < 3 ; $h++) { 
		    				$suggesstion[]	=	$newNm[1].'.'.$newNm[0].(date('Y')+$h);	
		    			}
		    		} 
		    		else 
		    		{
		    			$this->profile_obj->updateusrname(array('Username'=>$uname),$userid);
		    			$returnArr['success'] = 'true';
		    		}
		    		if(count($suggesstion)>0)
		    		{
			    		foreach ($suggesstion as $key => $value) 
			    		{
			    			$chkunameexist2	=	$this->myclientdetails->getfieldsfromtable(array('Name'),'tblUsers','Username',$value);
			    			if(count($chkunameexist2)==0)
			    			{
			    				$finalsuggestion[] = $value;
			    				$finalsuggestionret .= '<li>'.strtolower($value).'</li>';
			    				if(count($finalsuggestion)==4)
			    				{
			    					break;
			    				}	
			    			}	
			    		}
			    		$returnArr['formfields'] = '<div id="sugErrorMsg"> This username is already in use. Please enter a new one or pick one from the suggestions.</div>
			    		<div id="passform" class="postTypeContent">
					    	<div class="formRow">
		    					<div class="formField">
			    					 <input type="text" id="newusername" class="textfield"  placeholder="write your name here">  
			    					 <i class="optionalText">Choose Name</i>
		    					 </div>
	    					 </div>
							<div class="formRow">
								<div class="UsNmSuggestions">
									<h4>Our suggestions</h4>
									<ul>'.$finalsuggestionret.'</ul>
								</div>
						</div>';
		    		}
		    		
		    	}
				return $response->setBody(Zend_Json::encode($returnArr));
		    }	
	    	exit;
	    }
	   	       
	    public function updatefromexpertAction()
		{
			$data = array();
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(true);
			$response = $this->getResponse();
			$response->setHeader('Content-type', 'application/json', true);
			
			if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
			{	
				$storage  = new Zend_Auth_Storage_Session();        
				$auth        =  Zend_Auth::getInstance();   
				
				$session    = $storage->read(); 
				
				$valid = new Zend_Validate_NotEmpty();

				$filter = new Zend_Filter_StripTags();
				
				if (!$valid->isValid($this->_request->getPost('experttxtName')))
				{
					
					$data['status'] = 'error';
					$data['message']= 'Enter your Name.';
				} 
			    else{
				
					$user_personal_info['Name']     = $this->myclientdetails->customEncoding($filter->filter($this->_request->getPost('experttxtName')));

					$user_personal_info['UserID'] =  $this->_userid;
					$this->User_Model->updateinfouser($user_personal_info);
					
					
                    $result_array = $this->User_Model->userdetailall($this->_userid);

                    $this->myclientdetails->sessionWrite($result_array[0]);
		
					$data['status'] = 'success';
					$data['message']= 'Your name has been updated';
				}
			}
			return $response->setBody(Zend_Json::encode($data));
		}

		public function updatetitleexpertAction()
		{
			$data = array();
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(true);
			$response = $this->getResponse();
			$response->setHeader('Content-type', 'application/json', true);
			
			if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
			{	
				$storage  = new Zend_Auth_Storage_Session();        
				$auth        =  Zend_Auth::getInstance();   
				
				$session    = $storage->read(); 
				$user_model = new Application_Model_DbUser(); // get model object
				$valid = new Zend_Validate_NotEmpty();
				$storage = new Zend_Auth_Storage_Session();
				$filter = new Zend_Filter_StripTags();
				
				if (!$valid->isValid($this->_request->getPost('experttxtName')))
				{
					
					$data['status'] = 'error';
					$data['message']= 'Please enter your title.';
				} 
			    else{
				
					$user_personal_info['title']     =  $this->myclientdetails->customEncoding($filter->filter($this->_request->getPost('experttxtName')));

					$user_personal_info['UserID'] =  $this->_userid;
					$user_model->updateinfouser($user_personal_info);
					
					 $result_array = $this->User_Model->userdetailall($this->_userid);

                    $this->myclientdetails->sessionWrite($result_array[0]);
		
		
					$data['status'] = 'success';
					$data['message']= 'Your title updated successfully.';
				}
			}
			return $response->setBody(Zend_Json::encode($data));
		}

		public function updatecompanyexpertAction()
		{
			$data = array();
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(true);
			$response = $this->getResponse();
			$response->setHeader('Content-type', 'application/json', true);
			
			if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
			{	
				$storage  = new Zend_Auth_Storage_Session();        
				$auth        =  Zend_Auth::getInstance();   
				
				$session    = $storage->read(); 
				$user_model = new Application_Model_DbUser(); // get model object
				$valid = new Zend_Validate_NotEmpty();
				$storage = new Zend_Auth_Storage_Session();
				$filter = new Zend_Filter_StripTags();
				
				if (!$valid->isValid($this->_request->getPost('experttxtName')))
				{
					
					$data['status'] = 'error';
					$data['message']= 'Please enter your company name.';
				} 
			    else{
				
					$user_personal_info['company']     =  $this->myclientdetails->customEncoding($filter->filter($this->_request->getPost('experttxtName')));

					$user_personal_info['UserID'] =  $this->_userid;
					$user_model->updateinfouser($user_personal_info);
					
					 $result_array = $this->User_Model->userdetailall($this->_userid);

                    $this->myclientdetails->sessionWrite($result_array[0]);
		
		
					$data['status'] = 'success';
					$data['message']= 'Your company updated successfully.';
				}
			}
			return $response->setBody(Zend_Json::encode($data));
		}


		public function updatebioexpertAction()
		{
			$data = array();
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(true);
			$response = $this->getResponse();
			$response->setHeader('Content-type', 'application/json', true);
			
			if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
			{	
				$valid = new Zend_Validate_NotEmpty();
				$filter = new Zend_Filter_StripTags();
				
				if (!$valid->isValid($this->_request->getPost('experttxtName')))
				{
					$data['status'] = 'error';
					$data['message']= 'Please enter your biography.';
				} 
			    else{
					$userbiography = new Application_Model_DbUserbiography();
					$field_value     = $filter->filter($this->_request->getPost('experttxtName'));
					$userbiography->updateExpertBio($this->_userid,'3',$field_value);
					$data['status'] = 'success';
					$data['message']= 'Your biography updated successfully.';
				}
			}
			return $response->setBody(Zend_Json::encode($data));
		}

		public function updatenotificationexpertAction()
		{
			$data = array();
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(true);
			$response = $this->getResponse();
			$response->setHeader('Content-type', 'application/json', true);
			
			if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
			{	
		
				$user_model = new Application_Model_DbUser(); // get model object			
				$user_personal_info['Expert_Mail_Status']     = (int)$this->_request->getPost('emailSetting');
				$user_personal_info['UserID']     = (int)$this->_userid;
				$user_model->updateinfouser($user_personal_info);

				$data['status'] = 'success';
				if($user_personal_info['Expert_Mail_Status']==1)
					$data['message']= "Notification setting updated. You will now receive email notifications for user questions.";
				else
					$data['message']= "Notification setting updated. You will no longer receive email notifications for user questions.";

			}
			return $response->setBody(Zend_Json::encode($data));
		}

		public function updateemailAction()
		{
			$data = array();
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(true);
			$response = $this->getResponse();
			$response->setHeader('Content-type', 'application/json', true);
			
			if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
			{	
				$storage  = new Zend_Auth_Storage_Session();        
				$auth        =  Zend_Auth::getInstance();				
				$session    = $storage->read(); 
				$user_model = new Application_Model_DbUser(); // get model object
				$valid = new Zend_Validate_NotEmpty();
				$validator_email = new Zend_Validate_EmailAddress();
				$email = $this->myclientdetails->customEncoding($this->_request->getPost('email'));
				if (!$valid->isValid($email))
				{
					$data['status'] = 'error';
					$data['message']= 'Enter your email address.';
				} 
				else if (!$validator_email->isValid($this->_request->getPost('email'))) {
					
					$data['status'] = 'error';
					$data['message']= 'Please enter your valid email address.';
				} 
				else if ($user_model->auseremailcheck($email) && ($email != $session['Email'])){
					
					$data['status'] = 'error';
					$data['message']= 'Someone already has that email.';
					
				}else{
				
					$user_personal_info['Email']     = $email;
					$user_personal_info['EmailBox'] = 1;
					$user_personal_info['Emailtoken'] = md5(time());

					if($this->_request->getPost('filename'))
					{
						$user_personal_info['ProfilePic'] = $this->_request->getPost('filename');
						$user_personal_info['ShowPPBox'] = 0;
					}
					$user_personal_info['UserID'] =  $this->_userid;
					$user_model->updateinfouser($user_personal_info);
					
					$result_array = $user_model->ausersocialdetail($this->_userid);
					$result = $result_array['0'];
					$user_personal_info['Name'] = $result_array['0']['Name'];
					 $result_array = $this->User_Model->userdetailall($this->_userid);

                    $this->myclientdetails->sessionWrite($result_array[0]);
		
					$this->signupmail($user_personal_info);
					$data['message'] = 'An email has been sent to '.$this->_request->getPost('email').'. Please click on the verification link in the email to verify your email address and start receiving platform notifications.';
					$data['status'] = 'success';
					$data['notify']= 1;

				}
			}
			return $response->setBody(Zend_Json::encode($data));
		}
		
		public function signupmail($request)
	    {
	        if (isset($request)) {
	            $siteurl = BASE_URL;    
			    $EmailTemplateArray = array('Email' => $request['Email'],
		                                    'siteUrl' => $siteurl,
		                                    'Name' => $request['Name'],
		                                    'Emailtoken' => $request['Emailtoken'],
		                                    'case'=>23);
		        $bodyContentmsgall = $this->dbeeComparetemplateOne($EmailTemplateArray);
	        }
	    }
    
	
		
	    public function detailAction()
	    {
	    	$request = $this->getRequest();
			$requestasr = $this->getRequest()->getParams();	    	
	    	$userid=$request->getPost('user'); 	
	    	$cookieuser = $this->_userid;  	    		
	    	$age='';
	    	$profile = new Application_Model_Profile();
	    	$follwoing =  new Application_Model_Following();
			$userbio   =  new Application_Model_DbUserbiography();
			$userdetails = new Application_Model_DbUser();
			$myclientdetails = new Application_Model_Clientdetails();
	    	$fcnt =$follwoing->chkfollowingcnt($userid,$cookieuser);
	    	
			$this->view->following = $follwoing->getfolloweruser($userid);
			$this->view->myclientdetails = $myclientdetails;
			$this->view->follower = $follwoing->getallfollowing($userid);
			$this->view->row = $profile->getuserbyprofileid($userid);
			$message = new Application_Model_Message();
			$this->view->blockuser = $message->chkblockuser($userid,$this->_userid );
			$this->view->bio = $userbio->auserbiodetail($userid);
			$this->view->userdetails = $userdetails->ausersocialdetail($userid);
			//$this->view->row = $profile->getsocial($userid,'1',$id);	    			
			$this->view->totalLike = $profile->totalikesprofile($userid,'2');
			$this->view->totalLove = $profile->totalikesprofile($userid,'1');
			$this->view->totalPhil = $profile->totalikesprofile($userid,'3');
			$this->view->totalDislike = $profile->totalikesprofile($userid,'4');
			$this->view->totalHate = $profile->totalikesprofile($userid,'5');	
			$this->view->TotalFollowingRes = $follwoing->getfolloweruserprofile($userid);
			$this->view->FollowingRow = $follwoing->getfolloweruserprofilelimit($userid);
			$this->view->changelink = $request->getPost('sid');
			$this->view->groupvalcheck = $request->getPost('groupvalcheck');
			$this->view->TotalFollowersRes = $follwoing->getfollowing($userid);
			$this->view->FollowersRes = $follwoing->getfollowinglimit($userid);
			
			$this->view->cookieuser = $cookieuser;
			$this->view->userid = $userid;
			$this->view->follow = $fcnt;
			/****************social friends***************/
			$userinfo = new Application_Model_DbUser();
			$userinfodetails = $userinfo->userdetailall($this->_userid);
			$this->view->Socialtype   = $userinfodetails[0]['Socialtype'];
			$this->view->Socialid	  = $userinfodetails[0]['Socialid'];
			$this->_helper->layout->disableLayout();
	    			
	    }

	public function insertdataAction() 
	{
		$SubmitMsg=0;
		$userid = $this->_userid;
		$request = $this->getRequest();		
		$db=intval($request->getpost('db'));
		$replytype=stripcslashes($request->getpost('replytype'));
		$comment=stripslashes(strip_tags($request->getpost('comment','')));
		$link=stripslashes(strip_tags($request->getpost('url','')));
		$linktitle=stripslashes(strip_tags($request->getpost('linktitle','')));
		$linkdesc=stripslashes(strip_tags($request->getpost('linkdesc','')));
		$userlinkdesc=stripslashes(strip_tags($request->getpost('userlinkdesc','')));
		$pic=stripslashes(strip_tags($request->getpost('pic','')));
		$picdesc=stripslashes(strip_tags($request->getpost('picdesc','')));
		$vid=stripslashes(strip_tags($request->getpost('vid','')));
		$viddesc=stripslashes(strip_tags($request->getpost('viddesc','')));
		$videosite=stripslashes(strip_tags($request->getpost('videosite','')));
		$videoid=stripslashes(strip_tags($request->getpost('videoid','')));
		$audio=stripslashes($request->getpost('audio',''));
		$twittercomment=stripslashes($request->getpost('twittercomment',''));
		$twittercomment=str_replace('&','%26',$twittercomment);			
		
		$twittercomment=str_replace("<div style='float:right;'><a href='javascript:void(0);' onclick='javascript:removetweetfromnewcomment();'>remove</a></div>","",$twittercomment);
		
		if($userlinkdesc=='write something about this link...') $userlinkdesc='';
		if($picdesc=='write something about this picture...') $picdesc='';
		if($viddesc=='write something about this media...') $viddesc='';
		if($vid=='paste YouTube link here') $vid='';
		if($audio=='paste SoundCloud embed code here') $audio='';
		
		$myhometable= new Application_Model_Myhome();
		$dbeeRow=$myhometable->getDbeeDetails($db);
		$dbeeOwner=$dbeeRow['User'];
		$link='';
		$linktitle='';
				
		if($replytype=='text') $Type=1;
		if($replytype=='link') $Type=2;
		if($replytype=='pix') $Type=3;
		if($replytype=='vidz') $Type=4;
		
		if(isset($_COOKIE['user'])) $user=$_COOKIE['user'];
		else $user='-1';		
		if(isset($_COOKIE['gptn'])) $gptn=$_COOKIE['gptn'];
		else $gptn='';		
			$ProfileDate=date('Y-m-d H:i:s');
			$commenttable = new Application_Model_Profile();
			$NotifyEmail = $commenttable->getCommentInfo($db,$userid);
			if(!$NotifyEmail)			
				$NotifyEmail=1;
			$data = array(
				    'DbeeID'      => $db,
				    'DbeeOwner' => $dbeeOwner,
				    'UserID'      => $user,
					'Type'      => $Type,
					'Profile' => $comment,
					'Link'      => $link,
					'LinkTitle' => $linktitle,
					'LinkDesc'      => $linkdesc,
					'UserLinkDesc' => $userlinkdesc,
					'Pic'      => $pic,
					'PicDesc' => $picdesc,
					'Vid'      => $vid,
					'VidDesc' => $viddesc,
					'VidSite'      => $videosite,
					'VidID' => $videoid,
					'Audio'      => $audio,
					'TwitterProfile' => $twittercomment,
					'TwitterGPName' => $gptn,
					'ProfileDate'      => $ProfileDate,
					'NotifyEmail' => $NotifyEmail					
				);				
				$expire = time()+60*60*24*10;
			if($commenttable->insertcomment($data)) {
				setcookie('currloginlastseencomments', $ProfileDate,$expire);
				$SubmitMsg=1;
				// UPDATE LAST ACTIVITY TIME AND TOTAL COMMENTS FOR THE DBEE
				$newcomment = $myhometable->getcomment + 1;
				$data = array(
						'Profiles'      => $newcomment,
						'LastActivity' => $ProfileDate,
						'UserID'      => $user,
				);			
				
				// UPDATE LAST ACTIVITY TIME AND TOTAL COMMENTS FOR THE DBEE
		
				// SEND MAIL TO DB OWNER INFORMING OF A NEW COMMENT
				$OwnerRow = $commenttable->getuserdata($dbeeOwner);
				$UserRow = $commenttable->getuserdata($user);
				
			
				/*$MailBody='<div style="padding-bottom:5px; border-bottom:1px solid #CCC;"><a href="" target="_blank"><img src="/images/logo-emails.png" border="0"></a></div><div style="clear:both"></div><div style="margin-top:10px; margin-bottom:10px;"><div style="float:left; width:80px;"><img src="/show_thumbnails.php?ImgName='.$UserRow->ProfilePic.'&ImgLoc=userpics&Width=60&Height=60" border="0" /></div><div style="float:left; width:600px;"><font size="2" face="Arial, Helvetica, sans-serif">'.$UserRow->Name.' has commented on db <a href="/profile.php?db='.$db.'">';*/
		
				// BUILD DB TEXT
				if($dbeeRow['Type']=='1') $dbeeText=$dbeeRow['Text'];
				elseif($dbeeRow->Type=='2') $dbeeText=($dbeeRow->LinkDesc!='') ? $dbeeRow->LinkDesc : $dbeeRow->LinkTitle;
				elseif($dbeeRow->Type=='3') $dbeeText=($dbeeRow->PicDesc!='') ? $dbeeRow->PicDesc : '<img src="/show_thumbnails.php?ImgName='.$dbeeRow->Pic.'&ImgLoc=imageposts&Width=30&Height=30" border="0" />';
				elseif($dbeeRow->Type=='4') $dbeeText=$dbeeRow->VidDesc;
		
				// BUILD COMMENT TEXT
				if($Type=='1') $commText=$comment;
				elseif($Type=='2') $commText=($linkdesc!='') ? $linkdesc : $linktitle;
				elseif($Type=='3') $commText=($picdesc!='') ? $picdesc : '<img src="/show_thumbnails.php?ImgName='.$pic.'&ImgLoc=imageposts&Width=50&Height=50" border="0" />';
				elseif($Type=='4') $commText=$viddesc;
				$commTextval = str_replace("\\","",$commText);
	            $checkImage = new Application_Model_Commonfunctionality();
                $pics1 = $checkImage->checkImgExist($UserRow->ProfilePic,'userpics','default-avatar.jpg');		
                $UserRowProfilePic = '<span><img src="'.IMGPATH.'/users/small/'.$pics1.'" width="60" height="60" border="0" /><span>';
 	

				$commentuser = $commenttable->getcommentuser($db);		
				foreach($commentuser as $ProfileUsersRow):				
						if(trim($ProfileUsersRow['Email'])!=""){
 		/****for email ****/ 


		$EmailTemplateArray = array('Email' => $ProfileUsersRow->Email,
                                    'Name' => $UserRow->Name,
                                    'lname' => $UserRow->lname,
                                    'UserRowProfilePic' => $UserRowProfilePic,
                                    'db'=> $db,
                                    'dbeeText'=>$dbeeText,
                                    'commTextval1'=> $commTextval1,
                                    'case'=>25);
        $bodyContentmsgall = $this->dbeeComparetemplateOne($EmailTemplateArray);
		/****for email ****/
		/*$this->sendWithoutSmtpMail($ProfileUsersRow->Email, $subjectMsg, NOREPLY_MAIL,$messagemail);*/ 															
				}
					endforeach;
		
					// SEND MAIL TO DB OWNER
					if($OwnerRow['UserID']!=$user) {
						$notificationuser = new Application_Model_Notification();
						$ChkOwnerProfileNotification = $notificationuser->getnotificationuser($OwnerRow['UserID']);
						    if($notificationuser['Profiles']=='1') {
		 /****for email ****/ 


		$EmailTemplateArray = array('Email' => $OwnerRow->Email,
                                    'Name' => $UserRow->Name,
                                    'lname' => $UserRow->lname,
                                    'UserRowProfilePic' => $UserRowProfilePic,
                                    'db'=> $db,
                                    'dbeeText'=>$dbeeText,
                                    'commTextval1'=> $commTextval1,
                                    'case'=>25);
        $bodyContentmsgall = $this->dbeeComparetemplateOne($EmailTemplateArray);
		/****for email ****/		
							//$this->sendWithoutSmtpMail($OwnerRow->Email, $subjectMsg, NOREPLY_MAIL, $messagemail); 
						}
					}
					// SEND MAIL TO DB OWNER
				}		
		
			// SELECT COMMENTS
			$TotalProfiles=$commenttable->totacomment($db);// CALCUATE TOTAL DBEES IN DATABASE
		
			//
			$excerpt=substr($comment,0,50).'...';
		
			// SEND BACK TOTAL COMMENTS IF GUEST USER FROM TWITTER
			if(isset($_COOKIE['gptn'])) {		
			$totalgpc=	$commenttable ->totalgpc($db,$_COOKIE['gptn']);
			set_cookie('gptc', $totalgpc);
		} else $totalgpc='-1';
		
		echo $SubmitMsg.'~'.$db.'~'.$TotalProfiles.'~'.$excerpt.'~'.$totalgpc.'~'.$cookieUserBlockedInt;
		
		$response = $this->_helper->layout->disableLayout();
		return $response;
		
	}
	
	public function mutualscoresAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
			$user = (int)$this->_request->getPost('userID');
			$cookieuser = $this->_userid;
			$getuser = new Application_Model_Myhome();
			$proscore = new Application_Model_Profile();
			$uName = $getuser->getfeeoptiongroup($user);

			
			$loveScore=explode('~',$proscore->checkMutualScore('1',$user,$cookieuser));
			$likeScore=explode('~',$proscore->checkMutualScore('2',$user,$cookieuser));
			$philosopherScore=explode('~',$proscore->checkMutualScore('3',$user,$cookieuser));
			$dislikeScore=explode('~',$proscore->checkMutualScore('4',$user,$cookieuser));
			$hateScore=explode('~',$proscore->checkMutualScore('5',$user,$cookieuser));

			$scoretemp1=$this->myclientdetails->scoringTemplate(1,$this->post_score_setting,'fa-2x');

			$scoretemp2=$this->myclientdetails->scoringTemplate(2,$this->post_score_setting,'fa-2x');

			$scoretemp3=$this->myclientdetails->scoringTemplate(3,$this->post_score_setting,'fa-2x');
			$scoretemp4=$this->myclientdetails->scoringTemplate(4,$this->post_score_setting,'fa-2x');
			//$scoretemp5=$this->myclientdetails->scoringTemplate(5,$this->post_score_setting,'fa-2x');
			// $uName['ProfilePic']

			$checkImage = new Application_Model_Commonfunctionality();
            $pics1 = $checkImage->checkImgExist($uName['ProfilePic'],'userpics','default-avatar.jpg');		
            $UserProfilePic = '<img src="'.IMGPATH.'/users/medium/'.$pics1.'" width="50" height="50" border="0" />';

            $pics2 = $checkImage->checkImgExist($_SESSION['Zend_Auth']['storage']['ProfilePic'],'userpics','default-avatar.jpg');		
            $UserProfilePic2 = '<img src="'.IMGPATH.'/users/medium/'.$pics2.'" width="50" height="50" border="0" />';

			$content .= '
	<div class="mUscoredRow">
			'.$UserProfilePic2.'
		<ul class="lgScorePopup">
			
			<li>				
				'.$scoretemp2[1].'
				<span class="scrBallon">'.$likeScore[1].'</span>
			</li>
			<li>
				
				'.$scoretemp1[1].'
				<span class="scrBallon">'. $loveScore[1].'</span>
			</li>
				
			<li>				
				'.$scoretemp3[1].'
				<span class="scrBallon">'.$philosopherScore[1].'</span>
			</li>
			<li>				
				'.$scoretemp4[1].'
				<span class="scrBallon">'.$dislikeScore[1].'</span>
			</li>
		</ul>
		<div class="mUscoredNote">You scored '.$this->myclientdetails->customDecoding( $uName['full_name']).' </div>
	</div>

	
	
	
	
	<div class="mUscoredRow">
		'.$UserProfilePic.'
		<ul class="lgScorePopup">
			
			<li>				
				'.$scoretemp2[1].'
				<span class="scrBallon">'.$likeScore[0].'</span>
			</li>
			<li>
				'.$scoretemp1[1].'
				<span class="scrBallon">'.$loveScore[0].'</span>
			</li>	
			<li>				
				'.$scoretemp3[1].'
				<span class="scrBallon">'.$philosopherScore[0].'</span>
			</li>
			<li>				
				'.$scoretemp4[1].'
				<span class="scrBallon">'.$dislikeScore[0].'</span>
			</li>
		</ul>
		<div class="mUscoredNote">'.$this->myclientdetails->customDecoding( $uName['full_name']).' scored you</div>
	</div>';
			$data['status'] = 'success';
			$data['content']= $content;
					
		}
		return $response->setBody(Zend_Json::encode($data));
			
	}
	public function leagueAction()
	{	
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		$filter = new Zend_Filter_StripTags();
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
			$this->session_data['ProfilePic'];
			$userprofile = new Application_Model_Profile();
			$user = (int)$this->_request->getPost('userID');
			$users =$userprofile->getuserbyprofileid($user);		
			$loveScore = explode('~',$userprofile->checkScore('1',$user));;
			$likeScore = explode('~',$userprofile->checkScore('2',$user));
			$philosopherScore = explode('~',$userprofile->checkScore('3',$user));
			$dislikeScore = explode('~',$userprofile->checkScore('4',$user));
			$hateScore = explode('~',$userprofile->checkScore('5',$user));
			
			$loveLeagueScore = explode('~',$userprofile->checkLeague('2','1',$user,'love'));
			$hateLeagueScore = explode('~',$userprofile->checkLeague('4','5',$user,'rogue'));
			$philLeagueScore = explode('~',$userprofile->checkLeague('3','0',$user,'philosopher'));
			$ProfilePic =$this->commonmodel_obj->checkImgExist($this->session_data['ProfilePic'],'userpics','default-avatar.jpg'); 
			$content.='
			<div id="dashboarduserDetails" class="dashboarprofleWrp scoreProfileWrp">
				<div style="background-position: left top; background-image:url('.IMGPATH.'/users/'.$ProfilePic.'); background-repeat:no-repeat; background-size:contain" id="profileimage"></div>
				<div class="profileDes dbDetailsBox">
					<h2 class="oneline"> '.$this->myclientdetails->customDecoding($users['Name']).' '.$this->myclientdetails->customDecoding($users['lname']).'</h2>
					<h3>Hi '.$this->myclientdetails->customDecoding($users['Name']).' '.$this->myclientdetails->customDecoding($users['lname']).', your score tally is as follows...</h3>
					<div class="cmntScoreState">
				      	<span class="disbledClick" id="love-dbee">'.$this->myclientdetails->ShowScoreIcon($this->post_score_setting[1]['ScoreIcon1']).' <strong>'.$loveScore[0].'</strong></span>
				        <span class="disbledClick" id="like-dbee">'.$this->myclientdetails->ShowScoreIcon($this->post_score_setting[2]['ScoreIcon2']).' <strong>'.$likeScore[0].'</strong></span>
				        
				        <span class="disbledClick" id="dislike-dbee">'.$this->myclientdetails->ShowScoreIcon($this->post_score_setting[3]['ScoreIcon3']).' <strong>'.$dislikeScore[0].'</strong></span>
				        <span class="disbledClick" id="hate-dbee">'.$this->myclientdetails->ShowScoreIcon($this->post_score_setting[4]['ScoreIcon4']).' <strong>'.$hateScore[0].'</strong></span>
			        </div>	
				</div>
			</div>';
			
			$data['status'] = 'success';
		    $data['content']= $content;
		}
		return $response->setBody(Zend_Json::encode($data));
		
	}
	public function uploadpicAction()
	{
		
		$profilepic_obj = new Application_Model_Profile();			
		$profilepic = $profilepic_obj->getuserbyprofileid($this->_userid);		
		$this->view->currpic = $profilepic['ProfilePic'];
		$this->view->user = $this->_userid;
		// $this->_helper->layout->disableLayout();
		return $response;
	}
	
	public function dbuploadAction()
	{
		$form=new Application_Form_Dbupload();		
		$request = $this->getRequest();
		$realpath = $request->getPost('relPath');
			$formData = $request->getPost();
			$adapter = new Zend_File_Transfer_Adapter_Http();	
			//$adapter->addValidator('Extension', false, array('jpg','png','jpeg','gif','bmp'));
			 $adapter->setDestination($_SERVER['DOCUMENT_ROOT'].'/userpics');		
				if ($adapter->receive($_FILES['filename']['name'])) {					
					$this->view->upload_image=$_FILES['filename']['name'];
					$this->view->relPath=$realpath;
					$this->view->picerr=$error;
				}	
			//$response = $this->_helper->layout->disableLayout();
			return $response;
		
	}
	public function imguplodAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
			
			$ds = DIRECTORY_SEPARATOR;
	
			$storeFolder 	= './userpics';   //2.

			$image_info 	= getimagesize($_FILES["file"]["tmp_name"]);

			if($image_info[0] < 1 && $image_info[1] < 1)
			{
				echo "Please use valid image to upload";
				exit;
			}	
			if (!empty($_FILES))
			{
				$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION); 
				$picture	=	strtolower(time().'.'.$ext);
				
				if(copy($_FILES['file']['tmp_name'], './userpics/' .$picture))
				{
					echo $picture;
				}
				exit;
			}
		}

		exit;
	}
	public function imgunlinkAction()
	{
		$request =	$this->getRequest()->getParams();	
	
		echo "=>".unlink('./userpics/'.trim($request['serverFileName_']));
		exit;
	}
	public function changepicAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		$sizes = array(100 => 100, 50 => 50);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
			if(!$this->getRequest()->getPost('picture'))
			{
				$data['status'] = 'error';
				$data['message'] = 'Some thing went wrong here please try again';
			}else
			{
				
				$img = $this->getRequest()->getPost('picture');
				$picture_name = time() .'.jpeg';
				$img = str_replace('data:image/jpeg;base64,', '', $img);
				$img = str_replace(' ', '+', $img);
				
				$mobiledata = base64_decode($img); 				

				//$file = '../../../cimg/client_'.clientID.'/users/'.$picture_name;
				$file = ABSIMGPATH."/users/".$picture_name;				

				@file_put_contents($file, $mobiledata);

				$commonobj = new Application_Model_Commonfunctionality();
				
				foreach ($sizes as $w => $h) {
				 $files[] = $commonobj->resize($w, $h,$picture_name,$this->getRequest()->getPost('picture'));
			    }

				$data1 = array('ProfilePic' => $picture_name);

				$this->myclientdetails->updatedata_global('tblUsers',$data1,'UserID',$this->_userid);
				
				 $result_array = $this->User_Model->userdetailall($this->_userid);

                    $this->myclientdetails->sessionWrite($result_array[0]);
				$data['status'] = 'success';

				$data['picture'] = $picture_name;

				$data['message'] = 'profile picture has been successfully updated';
			}
		
		}
		return $response->setBody(Zend_Json::encode($data));
		
	}
	
	
	public function userleaguepopupAction()
	{
		$request = $this->getRequest();
		$user = $request ->getPost('user');
		$league = $request ->getPost('league');		
		$dbleaguedatas = $request ->getPost('dbleague');
		$callforprofile = $request ->getPost('callforprofile');
		$profilepicsave_obj = new Application_Model_Profile();	
		$myhome_obj = new Application_Model_Myhome();
		$return='';		
		if($dbleaguedatas=='') $function='seeuserleague';
		else $function='seedbleague';
		// $privategroup_obj = new Application_Model_Privateuser();
		// $PrivateGroups = $privategroup_obj->getprivategroup();
		// COLLECT ALL PRIVATE GROUP IDS		
		$exist=false;
		$topthree='';
		$afterthree='';
		$counter=1;
		$rank=1;
		$topthreeClass = array('goldCup', 'silverCup', 'bronzeCup'); 
		if($league!='mostfollowed') {
			if($league!='mostcomm') {
				$userfound=false;
				$LeagueArr=$this->leagueTable($league,$user);
			
				for($k=0;$k<count($LeagueArr);$k++) {
					if($LeagueArr[$k]['ID']==$user) {
						$userfound=true; break;
					}
					$rank++;
				}
		
				if($userfound) {
					$loopstart=$rank-1;
					for($k=$loopstart;$k<$loopstart+5;$k++) {
						if($k!=count($LeagueArr)-1) {
							$exist=true;
							if($LeagueArr[$k]['ID']!='') {
								$rowdb = $myhome_obj->getdbeeleague($LeagueArr[$k]['ID']);								
								if(count($rowdb)>0) {
									
									if($rowdb['Type']=='1') $dbeeText='<br /><div style="font-size:12px;"><a href="/dbeedetail/home/id/'.$rowdb['DbeeID'].'" target="_top">'.substr($rowdb['Text'],0,300).'</a></div>';
									elseif($rowdb['Type']=='2') $dbeeText='<br /><div style="font-size:12px;"><a href="/dbeedetail/home/id/'.$rowdb['DbeeID'].'" target="_top">'.substr($rowdb['LinkDesc'],0,300).'</a></div>';
									elseif($rowdb['Type']=='3') $dbeeText='<br />'.($rowdb['PicDesc']!='' ? '<div style="font-size:12px;"><a href="/dbeedetail/home/id/'.$rowdb['DbeeID'].'" target="_top">'.substr($rowdb['PicDesc'],0,300).'</a></div>' : '<img src="/show_thumbnails.php?ImgName='.$rowdb['Pic'].'&ImgLoc=imageposts&Width=30&Height=30" border="0" />');
									elseif($rowdb['Type']=='4') $dbeeText='<br /><div style="font-size:12px;"><a href="/dbeedetail/home/id/'.$rowdb['DbeeID'].'" target="_top">'.substr($rowdb['VidDesc'],0,300).'</a></div>';
								} else $dbeeText='';
		
								if($LeagueArr[$k]['ID']==$user) $bg=' background-color:#F9C10C; '; else $bg='';
		                        $checkImage = new Application_Model_Commonfunctionality();
                                $pics2 = $checkImage->checkImgExist($LeagueArr[$k]['ProfilePic'],'userpics','default-avatar.jpg');
								$return.='<li><div class="league-counter-small prizeType"><strong>'.$rank.'</strong></div><div class="leaguesUserPic leaguePxUser"><a href="'.BASE_URL.'/user/' .$this->myclientdetails->customDecoding( $LeagueArr[$k]['Username']) . '" target="_top"><img src="'.IMGPATH.'/users/small/'.$pics2.'" width="45" height="45" border="0" /></a></div><div class="leagusListDetails leagueUN"><a href="'.BASE_URL.'/user/'.$this->myclientdetails->customDecoding($LeagueArr[$k]['Username']).'" target="_top"><strong>'.$this->myclientdetails->customDecoding($LeagueArr[$k]['Name']).'</strong></a><span class="leaguePts">'.$LeagueArr[$k]['Score'].' pts</span>'.$dbeeText.'</div><div class="lgprofileCell"> 
                                                     <a class="btn btn-mini" href="'.BASE_URL.'/user/'.$this->myclientdetails->customDecoding( $LeagueArr[$k]['Username']).'">Profile</a>
                                                </div></li>';
								if($counter==1 && $callforprofile!=''){
									if($callforprofile=='lovelikepostion') $posiText = 'Love pos - ';
									else if($callforprofile=='fotpostion') $posiText = 'Philospher pos - ';
									else if($callforprofile=='hatepostion') $posiText = 'Rouge pos - ';
									//$tropheeHtml = ' ';
									if($rank<4){
										$tropheeHtml = '<i class="fa fa-trophy fa-lg"></i>' ;
									}
									$callonrequerst ='<div class="leagueLstRow">
														<div class="leagueCell '.$topthreeClass[$k].'">'.$tropheeHtml.'</div>
														<div class="leagueCell">'.$league.' - pos '.$rank.'</div>
													</div>';
								}
								$counter++;
								$rank++;
							}
						}
					}
				}
			}
		}
		else { // MOST FOLLOWED LEAGUE STARTS
			$counterclass='league-counter-small'; $textw='500'; $tww='60'; $tw='45'; $th='45';					
			//$SQL="SELECT User,COUNT(ID) AS Total FROM tblFollows,tblUsers WHERE tblFollows.User=tblUsers.UserID GROUP BY User ORDER BY Total DESC,User";
			$Following_obj = new Application_Model_Following();
			$dataRow = $Following_obj->getfollowinguserleageu();
			foreach($dataRow as $Row){
				if($Row['User']==$user) {
					break;
				}
				$rank++;
			}
		
			$limitstart=$rank-1;
			
			$Rowdata = $Following_obj->getfollowinguserleageulimit($limitstart);
			//$SQL="SELECT User,ProfilePic,Name,COUNT(ID) AS Total FROM tblFollows,tblUsers WHERE tblFollows.User=tblUsers.UserID GROUP BY User ORDER BY Total DESC,User limit ".$limitstart.",5";
			
			foreach($Rowdata as $Row) {
				$exist=true;
		
				$FollowerText=($Row['Total']>1) ? $Row['Total'].' followers' : $Row['Total'].' follower';
		
				/* $dbees="select * from tblDbees where User=".$Row['User'];
				if($PrivateGroups!='')
					$dbees.=" AND GroupID NOT IN (".$PrivateGroups.")";
				$dbees.=" ORDER BY PostDate DESC limit 1"; */
				$resdb = $myhome_obj->getdbeeleague($Row['User']);
			
				if(count($resdb)>0) {					
					if($rowdb['Type']=='1') $dbeeText='<br /><div style="font-size:12px;"><a href="/dbeedetail/home/id/'.$rowdb['DbeeID'].'">'.substr($rowdb['Text'],0,300).'</a></div>';
					elseif($rowdb['Type']=='2') $dbeeText='<br /><div style="font-size:12px;"><a href="/dbeedetail/home/id/'.$rowdb['DbeeID'].'">'.substr($rowdb['LinkDesc'],0,300).'</a></div>';
					elseif($rowdb['Type']=='3') $dbeeText='<br /><div style="font-size:12px;"><a href="/dbeedetail/home/id/'.$rowdb['DbeeID'].'">'.substr($rowdb['PicDesc'],0,300).'</a></div>';
					elseif($rowdb['Type']=='4') $dbeeText='<br /><div style="font-size:12px;"><a href="/dbeedetail/home/id/'.$rowdb['DbeeID'].'">'.substr($rowdb['VidDesc'],0,300).'</a></div>';
				} else $dbeeText='';
		
				if($Row['User']==$user) $bg=' background-color:#F9C10C;'; else $bg='';
		        $checkImage = new Application_Model_Commonfunctionality();
                $pics3 = $checkImage->checkImgExist($Row['ProfilePic'],'userpics','default-avatar.jpg');
				$return.='<div style="'.$bg.'" class="leagueLstRow"><div class="leagueCell '.$counterclass.'"><strong>'.$rank.'</strong></div><div  class="leagueCell leaguePxUser">
				<a href="/user/'.$this->myclientdetails->customDecoding($Row['Username']).'" target="_top"><img src="'.IMGPATH.'/users/small/'.$pics3.'" border="0" /></a></div><div class="leagueCell leagueUN" ><a href="'.BASE_URL.'/user/'.$this->myclientdetails->customDecoding($Row['Username']).'" target="_top"><strong>'.$this->myclientdetails->customDecoding($Row['Name']).'</strong></a>&nbsp;&nbsp;&nbsp;<span style="color:#999">'.$FollowerText.'</span>'.$dbeeText.'</div></div>';
				$counter++;
				$rank++;
			}
		}
		if(!$exist){
			if($callforprofile!='') 
				$callonrequerst='<li><div class="noFound">'.$this->commonleaguemsg($league).'</div></li>';
			else $return='<li><div class="noFound">'.$this->commonleaguemsg($league).'</div></li>';	
		}
		
		if($callforprofile!='') echo $callonrequerst;
		else echo $return.'~#~'.$showtabs.'~#~'.$league;
		$response = $this->_helper->layout->disableLayout();
		return $response;
	
	}
	function commonleaguemsg($type)
	{
		switch ($type) {
			case 'mostfollowed':
				return ' You don\'t currently have any followers';
				break;
			case 'love':
				return ' You don\'t currently have any love points';
				break;
			case 'rogue':
				return ' You don\'t currently have any rogue points';
				break;
			case 'philosopher':
				return ' You are not yet listed as a philosopher';
				break;			
			
			default:
				return 'Not on list.';
				break;
		}
	}
	
	function leagueTable($league,$user=0,$start=0,$commusers=0,$db=0) {
	
		$Arr1 = array();
        $Arr2 = array();
        $Arr3 = array();
        //-------------------------------------------
        // CALCULATE LEAGUE POINTS AND ARRANGE USERS
        if ($league == 'love') {
            $score1 = '2';
            $score2 = '1';
        } elseif ($league == 'rogue') {
            $score1 = '4';
            $score2 = '5';
        } elseif ($league == 'philosopher') {
            $score1 = '3';
            $score2 = '0';
        } 
        $DbleaguesTable = new Application_Model_Dbleagues();
        $Leaguers       = $DbleaguesTable->getleauebyscore($league, $user, $start = 0, $commusers = 0, $db = 0, $score1);
      
        if (count($Leaguers) > 0) {
            $totallike = 0;
            foreach ($Leaguers as $Row):
                $Arr1[$Row['Owner']]['ID']         = $Row['Owner'];
                $Arr1[$Row['Owner']]['Name']       = $Row['Name'];
                $Arr1[$Row['Owner']]['usertype']   = $Row['usertype'];
                $Arr1[$Row['Owner']]['ProfilePic'] = $Row['ProfilePic'];
                $Arr1[$Row['Owner']]['Score']      = $Row['TotalScore'];
                $Arr1[$Row['Owner']]['Username']   = $Row['Username'];
                $IDArr1[$totallike]                = $Row['Owner'];
                $totallike++;
            endforeach;
        }
        $Leaguers2 = $DbleaguesTable->getleauebyscore2($league, $user, $start = 0, $commusers = 0, $db = 0, $score2);
        if (count($Leaguers2) > 0) {
            $totallove = 0;
            foreach ($Leaguers2 as $Row):
                $Arr2[$Row['Owner']]['ID']         = $Row['Owner'];
                $Arr2[$Row['Owner']]['Name']       = $Row['Name'];
                $Arr2[$Row['Owner']]['usertype']  = $Row['usertype'];
                $Arr2[$Row['Owner']]['ProfilePic'] = $Row['ProfilePic'];
                $Arr2[$Row['Owner']]['Score']      = $Row['TotalScore'];
                $Arr2[$Row['Owner']]['Username']   = $Row['Username'];
                $IDArr2[$totallove]                = $Row['Owner'];
                $totallove++;
            endforeach;
        }

      
        if ($totallike > $totallove) {
            $formax  = $totallike;
            $useName = $Arr1;
            $useID   = $IDArr1;
        } else {
            $formax  = $totallove;
            $useName = $Arr2;
            $useID   = $IDArr2;
        }

        for ($i = 0; $i < $formax; $i++) {
            $Arr3[$i]['ID']         = $useID[$i];
            $Arr3[$i]['Name']       = $useName[$useID[$i]]['Name'];
            $Arr3[$i]['usertype']   = $useName[$useID[$i]]['usertype'];
            $Arr3[$i]['Username']   = $useName[$useID[$i]]['Username'];
            $Arr3[$i]['ProfilePic'] = $useName[$useID[$i]]['ProfilePic'];
            $Arr3[$i]['Score']      = $Arr1[$useID[$i]]['Score'] + $Arr2[$useID[$i]]['Score'];
        }
        
        if ($Arr3[0]['ID'] != '')
            $Arr3 = $this->sortmddata($Arr3, 'Score', 'DESC', 'num');
        // CALCULATE LEAGUE POINTS AND ARRANGE USERS
        //-------------------------------------------
        $pos = 0;

        if ($user != '0') {
            for ($k = 0; $k < count($Arr3); $k++) {
                if ($Arr3[$k]['ID'] == $user) {
                    $pos = $k + 1;
                    break;
                }
            }
        }
        $i++;
        $Arr3[$i]['UserPos'] = $pos;
        return $Arr3;
	}
	 public function sortmddata($array, $by, $order, $type)
    {
        $sortby   = "sort$by";
        $firstval = current($array);
        $vals     = array_keys($firstval);
        foreach ($vals as $init) {
            $keyname  = "sort$init";
            $$keyname = array();
        }
        foreach ($array as $key => $row) {
            foreach ($vals as $names) {
                $keyname    = "sort$names";
                $test       = array();
                $test[$key] = $row[$names];
                $$keyname   = array_merge($$keyname, $test);
            }
        }
        if ($order == "DESC") {
            if ($type == "num")
                array_multisort($$sortby, SORT_DESC, SORT_NUMERIC, $array);
            else
                array_multisort($$sortby, SORT_DESC, SORT_STRING, $array);
        } else {
            if ($type == "num")
                array_multisort($$sortby, SORT_ASC, SORT_NUMERIC, $array);
            else
                array_multisort($$sortby, SORT_ASC, SORT_STRING, $array);
        }
        return $array;
    }
	public function updateusernameroutAction(){
		
		$comm_model = new Application_Model_Commonfunctionality();
		$myhome_obj = new Application_Model_Myhome();
		$username = $comm_model->getalluser();
		foreach($username as $data):
		if(empty($data['Username'])){
			$email = $data['Email'];
			$parts = explode("@",$email);
			$username_email = $parts[0];
			$parts2 = explode(" ",$data['Name']);
			$userlast = $parts2[1];
			if($userlast!='') $username = $username_email.'.'.$userlast;
			else $username = $username_email;
			
			$chkexist	=	$myhome_obj->chkusername($username);			
			$data1 = array('Username'=>strtolower($chkexist));			
					
			$userid = $comm_model->updateusrname($data1,$data['UserID']);
			
		}			
		 
		endforeach;
	}
	
	public function sharefileAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
	
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
				
			$ds = DIRECTORY_SEPARATOR;
	
			$storeFolder 	= './userpics';   //2.
	
			$image_info 	= getimagesize($_FILES["file"]["tmp_name"]);
	
			/*list($width, $height, $type, $attr) = getimagesize($_FILES["file"]["tmp_name"]);
				$limt_widthimg =300;
				if($width<$limt_widthimg){
				echo "imagewidtherror";
				exit;
			}*/
	
			if($image_info[0] < 1 && $image_info[1] < 1)
			{
				echo "Please use valid image to upload";
				exit;
			}
	
			if (!empty($_FILES))
			{
				$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
				$picture	=	strtolower(time().'.'.$ext);
	
				if(copy($_FILES['file']['tmp_name'], './userpics/' .$picture))
				{
					echo $picture;
				}
				exit;
			}
		}
	
		exit;
	}	
	
}
