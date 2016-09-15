<?php

class Admin_MyaccountController extends IsadminController
{

	private $options;

    public function init()
    {
    	parent::init();
	    $this->deshboard   = new Admin_Model_Deshboard();
    }

        /**
     * Index Controller
     */
    
    public function indexAction()
    {
       $request = 	$this->getRequest()->getParams();
       //tblAdminSettings
       $selctGlobalSetting = $this->myclientdetails->getRowMasterfromtable('tblAdminSettings',array('social_account','plateform_scoring','google_spider','semantria_seen'));
       $this->view->globalSettingVal = $selctGlobalSetting['social_account'];
       $this->view->plateform_scoring = $selctGlobalSetting['plateform_scoring'];
       $this->view->google_spider = $selctGlobalSetting['google_spider'];
       $this->view->semantria_seen = $selctGlobalSetting['semantria_seen'];
       $this->view->adminpostscore = $selctGlobalSetting['adminpostscore'];
       $accSet	=	new Admin_Model_accountsetting();
  	   $profileRec	=	$accSet->getProfile($this->session_data['UserID']);
  	   $this->view->profileRec 	=	$profileRec[0];	   
         
	}

	public function configurationAction()
    {
       $request = $this->getRequest()->getParams();
       $roles = new Admin_Model_User();
       $selctAllRols = $roles->getRols();
       //echo'<pre>';print_r($selctAllRols);die;
       $this->view->roles = $selctAllRols;
       $selctGlobalSetting = $this->myclientdetails->getRowMasterfromtable('tblAdminSettings',array('*'));
       $this->view->globalSettingVal = $selctGlobalSetting['social_account'];
       $this->view->event = $selctGlobalSetting['event'];
       $this->view->creategrp = $selctGlobalSetting['creategrp'];
       $this->view->groupbg = $selctGlobalSetting['groupbg'];
       //
       $this->view->plateform_scoring = $selctGlobalSetting['plateform_scoring'];
       $this->view->google_spider = $selctGlobalSetting['google_spider'];
       $this->view->semantria_seen = $selctGlobalSetting['semantria_seen'];
       $this->view->IsLeagueOn = $selctGlobalSetting['IsLeagueOn'];
       $this->view->userconfirmed = $selctGlobalSetting['userconfirmed'];
       $this->view->allowexperts = $selctGlobalSetting['allowexperts'];
       $this->view->allow_user_create_polls = $selctGlobalSetting['allow_user_create_polls'];
       $this->view->addtocontact = $selctGlobalSetting['addtocontact'];
       $this->view->adminpostscore = $selctGlobalSetting['adminpostscore'];
       $this->view->Is_PollComments_On = $selctGlobalSetting['Is_PollComments_On'];
       $this->view->PollComments_On_Option = $selctGlobalSetting['PollComments_On_Option'];
       $this->view->IsSurveysOn = $selctGlobalSetting['IsSurveysOn'];
       $this->view->admin_searchable_frontend = $selctGlobalSetting['admin_searchable_frontend'];
       $this->view->allowedPostWithTwitter = $selctGlobalSetting['allowedPostWithTwitter'];
       $this->view->AllowLoginTerms 	   = $selctGlobalSetting['AllowLoginTerms'];
       $this->view->Profanityfilter 	   = $selctGlobalSetting['Profanityfilter'];
       //

       $query="SELECT count(*) as total FROM tblloginsocialresource WHERE clientID='".clientID."'";
       $fetchdata=$this->myclientdetails->passSQLquery($query);      
       if($fetchdata[0]['total'] < 1)
       {
       	  $dataArray = array('allSocialstatus'=> 0,'Facebookstatus'=> 0,'Twitterstatus'=> 0,'Gplusstatus'=> 0,'Linkedinstatus'=> 0,'clientID'=> clientID);
       	  $this->myclientdetails->insertdata_global('tblloginsocialresource',$dataArray);
       }

       $selctsocialSetting = $this->myclientdetails->getRowMasterfromtable('tblloginsocialresource',array('*'));
       $this->view->selctsocialSetting = $selctsocialSetting;
       $accSet	=	new Admin_Model_accountsetting();
         
	   $profileRec	=	$accSet->getProfile($this->_userid);
	   $this->view->profileRec 	=	$profileRec[0];

	   $profilefromUserTable	=	$accSet->getProfilesec($this->_userid);
	   $this->view->profilefromUserTable 	=	$profilefromUserTable[0];
	   //echo'<pre>';print_r($this->view->profileRec);die;
	   $this->view->adminid 	= $this->_userid;
	   //echo'<pre>';print_r($this->_userid);die;
	   $result = $this->myclientdetails->getRowMasterfromtable('tblConfiguration',array('*'));
	   $this->view->getSeoConfiguration = Zend_Json::decode($result['seoContent']);
	   $this->view->score = Zend_Json::decode($result['LeagueNames']);
	   $this->view->post_score_setting = 
	   $this->view->scoreset = $result['scoreset'];Zend_Json::decode($result['ScoreNames']);
	   $this->view->allow_admin_post_live = $result['allow_admin_post_live'];
	   $this->view->informviaemail = $result['informviaemail'];
	   $this->view->SocialLogo = $result['SocialLogo'];
	   $this->view->expert = $result['expert'];
	   $this->view->login_terms = $result['login_terms'];
	   $this->view->getSocialShareMsg  = Zend_Json::decode($result['SocialShareMsg']); 


	   $this->view->facebookPage = $result['facebook_page_data'];

	   $this->view->getStaticPage = $this->myclientdetails->getRowMasterfromtable('tblStaticPages',array('*'));
       
       $select = $this->myclientdetails->getAllMasterfromtable('tblAdminSettings',array('*'));
       $this->view->expireTime 	=	$select[0]['expireTime'];
       
       $this->view->facebookconnect = Zend_Json::decode($this->session_data['facebook_connect_data']);
       $this->view->twitterconnect = $twitter_connect_data = Zend_Json::decode($this->session_data['twitter_connect_data']);  
       $this->view->linkedinconnect = $twitter_connect_data = Zend_Json::decode($this->session_data['linkedin_connect_data']);

       $this->view->adminIconData = $adminIconData =$this->myclientdetails->getawvsomeicon();

 	   $this->view->siterss = $accSet->siterss();
 	   $this->view->country = $accSet->getcounty();  
       // for admin social connect 
	}



	public function globalsettingsAction()
    {
       $request = $this->getRequest()->getParams();
       $roles = new Admin_Model_User();
       $selctAllRols = $roles->getRols();
       //echo'<pre>';print_r($selctAllRols);die;
       $this->view->roles = $selctAllRols;
       $selctGlobalSetting = $this->myclientdetails->getRowMasterfromtable('tblAdminSettings',array('*'));
       $this->view->globalSettingVal = $selctGlobalSetting['social_account'];
       $this->view->event = $selctGlobalSetting['event'];
       $this->view->creategrp = $selctGlobalSetting['creategrp'];
       $this->view->groupbg = $selctGlobalSetting['groupbg'];
       //
       $this->view->plateform_scoring = $selctGlobalSetting['plateform_scoring'];
       $this->view->google_spider = $selctGlobalSetting['google_spider'];
       $this->view->semantria_seen = $selctGlobalSetting['semantria_seen'];
       $this->view->IsLeagueOn = $selctGlobalSetting['IsLeagueOn'];
       $this->view->userconfirmed = $selctGlobalSetting['userconfirmed'];
       $this->view->allowexperts = $selctGlobalSetting['allowexperts'];
       $this->view->allowmultipleexperts = $selctGlobalSetting['allowmultipleexperts'];
       $this->view->allow_user_create_polls = $selctGlobalSetting['allow_user_create_polls'];
       $this->view->addtocontact = $selctGlobalSetting['addtocontact'];
       $this->view->adminpostscore = $selctGlobalSetting['adminpostscore'];
       $this->view->Is_PollComments_On = $selctGlobalSetting['Is_PollComments_On'];
       $this->view->PollComments_On_Option = $selctGlobalSetting['PollComments_On_Option'];
       $this->view->IsSurveysOn = $selctGlobalSetting['IsSurveysOn'];
       $this->view->admin_searchable_frontend = $selctGlobalSetting['admin_searchable_frontend'];
       $this->view->allowedPostWithTwitter = $selctGlobalSetting['allowedPostWithTwitter'];
       $this->view->AllowLoginTerms 	   = $selctGlobalSetting['AllowLoginTerms'];
       $this->view->Profanityfilter 	   = $selctGlobalSetting['Profanityfilter'];
       $this->view->ShowVideoEvent          = $selctGlobalSetting['ShowVideoEvent'];
       $this->view->ShowLiveVideoEvent      = $selctGlobalSetting['ShowLiveVideoEvent'];
       $this->view->ShowRSS                 = $selctGlobalSetting['ShowRSS'];
       $this->view->showAllUsers            = $selctGlobalSetting['showAllUsers'];
       $this->view->groupemail                 = $selctGlobalSetting['GroupEmail'];
       //

       $query="SELECT count(*) as total FROM tblloginsocialresource WHERE clientID='".clientID."'";
       $fetchdata=$this->myclientdetails->passSQLquery($query);      
       if($fetchdata[0]['total'] < 1)
       {
       	  $dataArray = array('allSocialstatus'=> 0,'Facebookstatus'=> 0,'Twitterstatus'=> 0,'Gplusstatus'=> 0,'Linkedinstatus'=> 0,'clientID'=> clientID);
       	  $this->myclientdetails->insertdata_global('tblloginsocialresource',$dataArray);
       }

       $selctsocialSetting = $this->myclientdetails->getRowMasterfromtable('tblloginsocialresource',array('*'));
       $this->view->selctsocialSetting = $selctsocialSetting;
       $accSet	=	new Admin_Model_accountsetting();
       
       
	   $profileRec	=	$accSet->getProfile($this->_userid);
	   $this->view->profileRec 	=	$profileRec[0];

	   $profilefromUserTable	=	$accSet->getProfilesec($this->_userid);
	   $this->view->profilefromUserTable 	=	$profilefromUserTable[0];
	   //echo'<pre>';print_r($this->view->profileRec);die;
	   $this->view->adminid 	= $this->_userid;
	   //echo'<pre>';print_r($this->_userid);die;
	   $result = $this->myclientdetails->getRowMasterfromtable('tblConfiguration',array('*'));
	   $this->view->getSeoConfiguration = Zend_Json::decode($result['seoContent']);
	   $this->view->score = Zend_Json::decode($result['LeagueNames']);
	   $this->view->post_score_setting = 
	   $this->view->scoreset = $result['scoreset'];Zend_Json::decode($result['ScoreNames']);
	   $this->view->allow_admin_post_live = $result['allow_admin_post_live'];
	   $this->view->informviaemail = $result['informviaemail'];
	   $this->view->SocialLogo = $result['SocialLogo'];
	   $this->view->expert = $result['expert'];
	   $this->view->login_terms = $result['login_terms'];
	   $this->view->getSocialShareMsg  = Zend_Json::decode($result['SocialShareMsg']); 


	   $this->view->facebookPage = $result['facebook_page_data'];

	   $this->view->getStaticPage = $this->myclientdetails->getRowMasterfromtable('tblStaticPages',array('*'));
       
       $select = $this->myclientdetails->getAllMasterfromtable('tblAdminSettings',array('*'));
       $this->view->expireTime 	=	$select['expireTime'];
       
       $this->view->facebookconnect = Zend_Json::decode($this->session_data['facebook_connect_data']);
       $this->view->twitterconnect = $twitter_connect_data = Zend_Json::decode($this->session_data['twitter_connect_data']);  
       $this->view->linkedinconnect = $twitter_connect_data = Zend_Json::decode($this->session_data['linkedin_connect_data']);

       $this->view->adminIconData = $adminIconData =$this->myclientdetails->getawvsomeicon();

 	   $this->view->siterss = $accSet->siterss();
 	   $this->view->country = $accSet->getcounty();  
       // for admin social connect 
	}
	public function themesettingAction()
    {
	   $result = $this->myclientdetails->getRowMasterfromtable('tblConfiguration',array('*'));
	   $this->view->getThemeConfiguration = Zend_Json::decode($result['content']);
	}

	public function ajaxplateforminterfaceAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$filter = new Zend_Filter_StripTags();
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$plateformInterface = $this->_request->getPost('plateformInterface');
			$data2['plateformInterface'] = $plateformInterface;
			$this->myclientdetails->updatedata_global('tblConfiguration',$data2,clientID,clientID);			
			$data['status'] = 'success';
			$data['message'] = 'successfully disconnect';
		}
		else
		{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));
	}
        
    public function platforminterfaceAction()
    {
       $this->_helper->layout()->disableLayout();
       $result = $this->myclientdetails->getRowMasterfromtable('tblConfiguration',array('plateformInterface'));
	   $this->view->plateformInterface = $result['plateformInterface'];
	}    
   

	public function disconnectsocialAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$filter = new Zend_Filter_StripTags();
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$disconnect = $this->_request->getPost('disconnect');
			if($disconnect=='facebook')
			{
				$data2['facebook_connect_data'] = '';
				$data3['facebook_page_data'] = '';
				$this->myclientdetails->updatedata_global('tblConfiguration',$data3,clientID,clientID);
			}
			else if($disconnect=='twitter')
				$data2['twitter_connect_data'] = '';
			else if($disconnect=='linkedin')
				$data2['linkedin_connect_data'] = '';

			$this->myclientdetails->updatedata_global('tblUsers',$data2,'UserID',$this->_userid);
			
			$this->myclientdetails->updatedata_global('tblConfiguration',array('linkedin_page_id' => ''),'clientID',clientID);
			$this->rewritesession();
			$data['status'] = 'success';
			$data['message'] = 'successfully disconnect';
		}
		else
		{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));
	}


	public function facebookconnect()
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

					$this->user_model->updateUser($this->_userId,$user_personal_info);

					$this->_redirect(BASE_URL.'/admin/myaccount/configuration?invite=facebook');                   

                }
                catch (FacebookApiException $e) {
                    error_log($e);
                    $user = null;
                }
            }
        }
        return $facebook->getLoginUrl(array(
         'scope' => 'user_friends,user_about_me,photo_upload,user_activities,user_birthday,user_likes,user_photos,user_status,user_videos,email,read_friendlists,read_insights,read_mailbox,read_requests,read_stream,offline_access,publish_checkins,publish_stream,manage_pages'
        ));
    }
    public function updatefacebookpageinfoAction()
    {
    	$data = array();
		$postData = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);

		if ($this->getRequest()->isXmlHttpRequest()) 
		{
			$type = $this->_request->getPost('type');
			if($type=='facebook')
      {
        $facebookPage_token = $this->_request->getPost('pagetoken');
				$pagename = $this->_request->getPost('pagename');
				$data = array('facebook_page_data' => $facebookPage_token,'facebookpagename'=>$pagename);
			}else{
				$pageid = $this->_request->getPost('pageid');
				$data = array('linkedin_page_id' => $pageid);
			}
			$this->myclientdetails->updatedata_global('tblConfiguration',$data,'clientID',clientID);
			$this->rewritesession();
			$data['status']  = 'success';
      $data['message'] = 'facebook page Set successfully';
		}
		else 
		{
      $data['status']  = 'error';
      $data['message'] = 'facebook page has not been Set successfully';
    }
        return $response->setBody(Zend_Json::encode($data));
    }
	public function ajaxseoAction()
	{
		$data = array();
		$postData = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);

    $request1 = $this->_request->getParams();
  
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
			$postData['SiteTitle'] = $this->_request->getPost('SiteTitle');
			$postData['SiteDescription'] = str_replace("\\","", $this->_request->getPost('SiteDescription'));

			$data = array('seoContent' => Zend_Json::encode($postData));
			$this->myclientdetails->updatedata_global('tblConfiguration',$data,'clientID',clientID);
			$data['status']  = 'success';
            $data['message'] = 'updated successfully';
		}
		else 
		{
            $data['status']  = 'error';
            $data['message'] = 'Seo settings not updated';
        }
        return $response->setBody(Zend_Json::encode($data));
	}

	public function ajaxscoreAction()
	{
		$data = array();
		$postData = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);

		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
			$postData['score1'] = $this->_request->getPost('score1');
			$postData['score2'] = $this->_request->getPost('score2');
			$postData['score3'] = $this->_request->getPost('score3');

			$data = array('LeagueNames' => Zend_Json::encode($postData));
			$this->myclientdetails->updatedata_global('tblConfiguration',$data,'clientID',clientID);
			$data['status']  = 'success';
            $data['message'] = 'League settings has been set successfully';
		}
		else 
		{
            $data['status']  = 'error';
            $data['message'] = 'Seo settings not updated';
        }
        return $response->setBody(Zend_Json::encode($data));
	}

	public function ajaxpostscoreAction()
	{
		$data = array();
		$ScoretData = array();
		$postData1 = array();
		$postData2 = array();
		$postData3 = array();
		$postData4 = array();
		$postData5 = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);

		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
			
			

			$postData1['ScoreName1'] = $this->_request->getPost('ScoreName1');
			$postData1['ScoreIcon1'] = $this->_request->getPost('ScoreIcon1');
			//$postData1['ScoreStatus1'] = $this->_request->getPost('ScoreStatus1');


			$postData2['ScoreName2'] = $this->_request->getPost('ScoreName2');
			$postData2['ScoreIcon2'] = $this->_request->getPost('ScoreIcon2');
			//$postData2['ScoreStatus2'] = $this->_request->getPost('ScoreStatus2');

			$postData3['ScoreName3'] = $this->_request->getPost('ScoreName3');
			$postData3['ScoreIcon3'] = $this->_request->getPost('ScoreIcon3');
			//$postData3['ScoreStatus3'] = $this->_request->getPost('ScoreStatus3');
			

			$postData4['ScoreName4'] = $this->_request->getPost('ScoreName4');
			$postData4['ScoreIcon4'] = $this->_request->getPost('ScoreIcon4');
			//$postData4['ScoreStatus4'] = $this->_request->getPost('ScoreStatus4');

			//$postData5['ScoreName5'] = $this->_request->getPost('ScoreName5');
            //$postData5['ScoreIcon5'] = $this->_request->getPost('ScoreIcon5');
            //$postData5['ScoreStatus5'] = $this->_request->getPost('ScoreStatus5');

            $ScoretData[] = $postData1;
            $ScoretData[] = $postData2;
            $ScoretData[] = $postData3;
            $ScoretData[] = $postData4;
           // $ScoretData[] = $postData5;

            $ScoretData = array_filter(array_merge(array(0), $ScoretData));
			
			$data = array('ScoreNames' => Zend_Json::encode($ScoretData));
			$this->myclientdetails->updatedata_global('tblConfiguration',$data,'clientID',clientID);
			$data['status']  = 'success';
            $data['message'] = 'post score settings has been set successfully';
		}
		else 
		{
            $data['status']  = 'error';
            $data['message'] = 'post score settings has been set successfully';
        }
        return $response->setBody(Zend_Json::encode($data));
	}

	public function ajaxpostscoresetAction()
	{
		$data = array();		
		
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
;
		if ($this->getRequest()->isXmlHttpRequest())
		{
			$request = 	$this->getRequest()->getParams();

			if($request['scoreset']==1)
			{
               $ScoretData = '{"1":{"ScoreName1":"Strongly agree","ScoreIcon1":"189"},"2":{"ScoreName2":"Agree","ScoreIcon2":"189"},"3":{"ScoreName3":"Disagree","ScoreIcon3":"167"},"4":{"ScoreName4":"Strongly disagree","ScoreIcon4":"167"}}';
            }
            else
            {
              $ScoretData = '{"1":{"ScoreName1":"Strongly agree","ScoreIcon1":"227"},"2":{"ScoreName2":"Agree","ScoreIcon2":"263"},"3":{"ScoreName3":"Disagree","ScoreIcon3":"262"},"4":{"ScoreName4":"Strongly disagree","ScoreIcon4":"119"}}';
            }
			
			
			$dataarray = array('ScoreNames' => $ScoretData,'scoreset'=>$request['scoreset']);
			$this->myclientdetails->updatedata_global('tblConfiguration',$dataarray,'clientID',clientID);
			$data['status']  = 'success';
            $data['message'] = 'score set updated successfully';
		}
		else 
		{
            $data['status']  = 'error';
            $data['message'] = 'score set updated successfully';
        }
        return $response->setBody(Zend_Json::encode($data));
	}


	public function ajaxexpertAction()
	{
		$data = array();
		$postData = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);

		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
			$postData['expert'] = $this->_request->getPost('expert');

			$data = array('expert' => $postData['expert']);
			$this->myclientdetails->updatedata_global('tblConfiguration',$data,'clientID',clientID);
			$data['status']  = 'success';
            $data['message'] = 'expert settings has been set successfully';
		}
		else 
		{
            $data['status']  = 'error';
            $data['message'] = 'expert settings has not been set successfully';
        }
        return $response->setBody(Zend_Json::encode($data));
	}


	public function ajaxpollcomplianceAction()
	{
		$data = array();
		$postData = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);

		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
			
			
			$pollsettingval = $this->_request->getPost('pollsettingval');

			if($pollsettingval==1)
			{
				$Is_PollComments_On=1;
				$PollComments_On_Option=3;
			}
			else if($pollsettingval==2){

				$Is_PollComments_On=1;
				$PollComments_On_Option=2;
			}
			else if($pollsettingval==3){

				$Is_PollComments_On=1;
				$PollComments_On_Option=3;
			}	
			else if($pollsettingval==4){

				$Is_PollComments_On=1;
				$PollComments_On_Option=4;
			}	
			else if($pollsettingval==5){

				$Is_PollComments_On=1;
				$PollComments_On_Option=5;
			}
			else
			{
				$Is_PollComments_On=0;
				$PollComments_On_Option=0;
			}		

			$data = array('Is_PollComments_On' => $Is_PollComments_On,'PollComments_On_Option'=>$PollComments_On_Option);
			$this->myclientdetails->updatedata_global('tblAdminSettings',$data,'clientID',clientID);
			$data['status']  = 'success';
            $data['message'] = 'Poll compliance has been set successfully';
		}
		else 
		{
            $data['status']  = 'error';
            $data['message'] = 'Poll compliance has not been set successfully';
        }
        return $response->setBody(Zend_Json::encode($data));
	}


	public function ajaxpostliveconfAction()
	{
		$data = array();
		$postData = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$request = $this->getRequest()->getParams();

		if ($this->getRequest()->getMethod() == 'POST') 
		{
			
			$globe_val = $this->_request->getPost('globelSettingVal');      
       		$calling   = $this->_request->getPost('calling');

		    if($calling=="allow_admin_post_live") $data1 = array('allow_admin_post_live'=>$globe_val); 
       		else if($calling=="informviaemail") $data1 = array('informviaemail'=>$globe_val);			
			
			$this->myclientdetails->updatedata_global('tblConfiguration',$data1,'clientID',clientID);
			$data['status']  = 'success';
            $data['message'] = 'Allow Admin Post settings has been set successfully';
		}
		else 
		{
            $data['status']  = 'error';
            $data['message'] = 'Allow Admin Post settings has not been set successfully';
        }
        return $response->setBody(Zend_Json::encode($data));
	}

	public function ajaxstaticpagesAction()
	{
		$data = array();
		$postData = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
    $request1 = $this->_request->getParams();
    
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
			$postData['privacy'] = $this->_request->getPost('privacy');
			$postData['terms'] = $this->_request->getPost('terms');
			$postData['aboutus'] = $this->_request->getPost('aboutus');
      $postData['CookiePolicy'] = $this->_request->getPost('CookiePolicy');
			$this->myclientdetails->updatedata_global('tblStaticPages',$postData,'clientID',clientID);
			$data['status']  = 'success';
            $data['message'] = 'Static page has been updated';
		}
		else 
		{
            $data['status']  = 'error';
            $data['message'] = 'Static page has not been updated';
        }
        return $response->setBody(Zend_Json::encode($data));
	}


	public function ajaxafterlogintcAction()
	{
		$data = array();
		$postData = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);

		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
			$postData['login_terms'] = $this->_request->getPost('login_terms');
			
			$this->myclientdetails->updatedata_global('tblConfiguration',$postData,'clientID',clientID);
			$data['status']  = 'success';
            $data['message'] = 'Login terms and condition has been updated';
		}
		else 
		{
            $data['status']  = 'error';
            $data['message'] = 'Login terms and condition has not been updated';
        }
        return $response->setBody(Zend_Json::encode($data));
	}

	public function ajaxsocialshareAction()
	{
		$data = array();
		$postData = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);

		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
			$postData['group_share']     = $this->_request->getPost('group_share');
			$postData['event_share'] 	 = $this->_request->getPost('event_share');
			$postData['post_share'] 	 = $this->_request->getPost('post_share');
			$postData['expert_share'] 	 = $this->_request->getPost('expert_share');
			$postData['plateform_share'] = $this->_request->getPost('plateform_share');

			$myData['SocialShareMsg'] = Zend_Json::encode($postData);

			$this->myclientdetails->updatedata_global('tblConfiguration',$myData,'clientID',clientID);
			$data['status']  = 'success';
            $data['message'] = 'Configuration has been Set successfully';
		}
		else 
		{
            $data['status']  = 'error';
            $data['message'] = 'Configuration has not been Set successfully';
        }
        return $response->setBody(Zend_Json::encode($data));
	}
	
	public function ajaxconfigurationAction()
	{
		$data = array();
		$postData = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);

		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{

			$hiddenSigninImage = $this->_request->getPost('hiddenSigninImage');
			
			$hiddensitelogo = $this->_request->getPost('hiddensitelogo');
			$hiddenfaviconlogo = $this->_request->getPost('hiddenfaviconlogo');
			$hiddenbackgroundimage = $this->_request->getPost('hiddenbackgroundimage');
			$hiddenloginbackgroundimage = implode(',',$this->_request->getPost('hiddenloginbackgroundimage'));
			
			$hightlightlink = $this->_request->getPost('hightlightlink');

			$backgroundColor = $this->_request->getPost('backgroundColor');
			
			$postData['SigninText']  = $_POST['SigninText'];			
			$postData['SigninColor']  = $this->_request->getPost('backgroundsignColor');
			$postData['backgroundColor'] = $backgroundColor;

			if(isset($hightlightlink) && $hightlightlink==1)
				$postData['hightlightlink'] = $hightlightlink;
			else
				$postData['hightlightlink'] = 0;
			// *** Include the class
			include("resize_class.php");

			if($_FILES['sitelogo'])
			{
				$ext = pathinfo($_FILES['sitelogo']['name'], PATHINFO_EXTENSION); 
				$picture	=	strtolower(time().mt_rand(1,79632).'.'.$ext);
				@copy($_FILES['sitelogo']['tmp_name'], Front_Public_Path.'img/' .$picture);
				$postData['SiteLogo'] = $picture;
			}else if($hiddensitelogo!='')
				$postData['SiteLogo'] = $hiddensitelogo;

			if($_FILES['faviconlogo'])
			{
				$ext = pathinfo($_FILES['faviconlogo']['name'], PATHINFO_EXTENSION); 
				$picture	=	strtolower(time().mt_rand(1,79632).'.'.$ext);
				@copy($_FILES['faviconlogo']['tmp_name'], Front_Public_Path.'images/' .$picture);
				$postData['FaviconLogo'] = $picture;
				/*include_once("resize_class.php");
				$output_dir = Front_Public_Path.'images/';
				$resizeObj = new resize($output_dir.$picture);
				$resizeObj->resizeImage(20, 20, 'auto');
				unlink(Front_Public_Path.'/images/' .$picture);
                $resizeObj->saveImage($output_dir.$picture, 100);*/
			}else if($hiddenfaviconlogo!='')
				$postData['FaviconLogo'] = $hiddenfaviconlogo;	

			if($_FILES['signinlogo'])
			{
				$ext = pathinfo($_FILES['signinlogo']['name'], PATHINFO_EXTENSION); 
				$picture	=	strtolower(time().mt_rand(1,79632).'.'.$ext);
				@copy($_FILES['signinlogo']['tmp_name'], Front_Public_Path.'img/' .$picture);
				$postData['SigninImage'] = $picture;

			}else if($hiddenSigninImage!='')
				$postData['SigninImage'] = $hiddenSigninImage;
			
			if($_FILES['backgroundimage'])
			{
				$ext = pathinfo($_FILES['backgroundimage']['name'], PATHINFO_EXTENSION); 
				$picture	=	strtolower(time().mt_rand(1,79632).'.'.$ext);
			    @copy($_FILES['backgroundimage']['tmp_name'], Front_Public_Path.'img/' .$picture);
			    $postData['backgroundimage'] = $picture;
			}else if($hiddenbackgroundimage!='')
				$postData['backgroundimage'] = $hiddenbackgroundimage;
			
			// if($_FILES['loginbackgroundimage'])
			// {
			// 	$ext = pathinfo($_FILES['loginbackgroundimage']['name'], PATHINFO_EXTENSION); 
			// 	$picture	=	strtolower(time().mt_rand(1,79632).'.'.$ext);
			//     @copy($_FILES['loginbackgroundimage']['tmp_name'], Front_Public_Path.'img/' .$picture);
			//     $postData['loginbackgroundimage'] = $picture;
			// }else if($hiddenloginbackgroundimage!='')
			
			$postData['loginbackgroundimage'] = $hiddenloginbackgroundimage;

			$myData['tempContent'] = Zend_Json::encode($postData);
			$this->myclientdetails->updatedata_global('tblConfiguration',$myData,'clientID',clientID);
			$data['status']  = 'success';
            $data['message'] = 'Configuration has been Set successfully';
		}
		else 
		{
            $data['status']  = 'error';
            $data['message'] = 'Configuration has not been Set successfully';
        }
        return $response->setBody(Zend_Json::encode($data));
	}


	public function ajaxsociallogoAction()
	{
		$data = array();
		$postData = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);

		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{

			$hiddenSocialLogo = $this->_request->getPost('hiddenSocialLogo');
			
			if($_FILES['sociallogo'])
			{
				$ext = pathinfo($_FILES['sociallogo']['name'], PATHINFO_EXTENSION); 
				$SocialLogo	=	strtolower(time().mt_rand(1,79632).'.'.$ext);
				@copy($_FILES['sociallogo']['tmp_name'], Front_Public_Path.'img/' .$SocialLogo);
				$postData['SocialLogo'] = $SocialLogo;
			}else
				$postData['SocialLogo'] = $hiddenSocialLogo;


			$this->myclientdetails->updatedata_global('tblConfiguration',$postData,'clientID',clientID);
			$data['status']  = 'success';
            $data['message'] = 'Social logo has been Set successfully';
		}
		else 
		{
            $data['status']  = 'error';
            $data['message'] = 'Social logo has not been Set successfully';
        }
        return $response->setBody(Zend_Json::encode($data));
	}

	public function ajaxbackgroundAction()
	{
		$data = array();
		$postData = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);

		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
			if(!empty($_FILES['file']))
			{
				$ext = pathinfo($_FILES['file']['name'][0], PATHINFO_EXTENSION); 
				$filename	=	strtolower(time().mt_rand(1,79632).'.'.$ext);
				@copy($_FILES['file']['tmp_name'][0], Front_Public_Path.'img/' .$filename);
				$postData['filename'] = $filename;
			}
		}
        return $response->setBody(Zend_Json::encode($postData));
	}

	public function ajaxbgAction()
	{
		$data = array();
		$postData = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);

		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
			if(!empty($_FILES['file']))
			{
				$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION); 
				$filename	=	strtolower(time().mt_rand(1,79632).'.'.$ext);
				@copy($_FILES['file']['tmp_name'], Front_Public_Path.'img/' .$filename);
				$postData['filename'] = $filename;
			}
		}
        return $response->setBody(Zend_Json::encode($postData));
	}
	public function ajaxsaveconfigurationAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);

		if ($this->getRequest()->isXmlHttpRequest()) 
		{
			$this->deshboard->updateTempToPermConfiguration();
			$data['status']  = 'success';
            $data['message'] = 'Settings saved successfully';
		}
		else 
		{
            $data['status']  = 'error';
            $data['message'] = 'Configuration has not been Set successfully';
        }
        return $response->setBody(Zend_Json::encode($data));
	}


	public function ajaxremoveimageAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);

		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
			$type = $this->_request->getPost('type');
			$bgnameString = $this->_request->getPost('bgnameString');
			
			$resultArray = $this->myclientdetails->getRowMasterfromtable('tblConfiguration',array('tempContent'));
	   		$result = Zend_Json::decode($resultArray['tempContent']);
	   		
			if($type=='sitelogo')
			{
				$result['SiteLogo'] = '';
				$data['sitelogo']  = 'remove';
			}
			else if($type=='faviconlogo')
			{
				//unlink(Front_Public_Path.'/images/' .$result['FaviconLogo']);
				$result['FaviconLogo'] = '';
				$data['faviconlogo']  = 'remove';
			}
			else if($type=='headerBanner')
			{
				$result['HeaderBanner'] = '';
				$data['HeaderBanner']  = 'remove';
			}
			else if($type=='rightBanner')
			{
				$result['RightBanner'] = '';
				$data['rightBanner']  = 'remove';
			}
			else if($type=='backgroundImage')
			{
				$result['backgroundimage'] = '';
				$data['backgroundImage']  = 'remove';
			}
			else if($type=='loginbackgroundimage')
			{
				$result['loginbackgroundimage'] = $bgnameString;
				$data['loginbackgroundImage']  = 'remove';
			}
			else if($type=='loginlogoimage')
			{
				$result['SigninImage'] = '';
				$data['loginlogoimage']  = 'remove';
			}
			$myData['tempContent'] = Zend_Json::encode($result);
			$this->myclientdetails->updatedata_global('tblConfiguration',$myData,'clientID',clientID);
			$data['status']  = 'success';
            $data['message'] = 'Configuration has been Set successfully';
		}
		else 
		{
            $data['status']  = 'error';
            $data['message'] = 'Configuration has not been Set successfully';
        }
        return $response->setBody(Zend_Json::encode($data));
	}

	public function updateaccountAction()
	{
    $request =  $this->getRequest()->getPost();
		$request1 = 	$this->getRequest()->getParams();

		$accSet	=	new Admin_Model_accountsetting();
		$email		=	$this->myclientdetails->customEncoding($request['email']);
		$name		=	$this->myclientdetails->customEncoding($request['fname']);
		$lname		=	$this->myclientdetails->customEncoding($request['lname']);
    $sizes = array(100 => 100, 50 => 50);
	
    $userID		= $this->_userid;

    //echo "<pre>"; print_r($request1); exit;

   

		if($_FILES['profilepicture']['name']!='')
	  {
        $img = $request['profilePicData'];
        $picture = time() .'.jpeg';
        $img = str_replace('data:image/jpeg;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        
        $mobiledata = base64_decode($img);  


        $file = ABSIMGPATH."/users/".$picture; 

         @file_put_contents($file, $mobiledata);  

	   		//$ext      = pathinfo($_FILES['profilepicture']['name'], PATHINFO_EXTENSION);
        //$picture  = strtolower(time() . '.' . $ext);
        //$storeFolder    = ABSIMGPATH."/users/";
	   		//$copydone = copy($_FILES['profilepicture']['tmp_name'], $storeFolder.$picture);
  	   	  
           // $file     = ABSIMGPATH."/users/".$picture; 
            $commonobj = new Admin_Model_Common();
        
            foreach ($sizes as $w => $h) {
             $files[] = $commonobj->resize($w, $h,$picture);
              }
          
           $copydone = 2;
	  } else 
	  {
	   		$picture	=	$request['picture'];
	   		$copydone	=	2;	
	  }

//echo $copydone; die;
		
		if($copydone==2)
		{
			$query = "select Email from tblUsers where Email='".$email."' AND userid!='".$userID."' AND clientID=".clientID;
			$chkfuser = $this->myclientdetails->passSQLquery($query);
			//echo count($chkfuser);die;
			if(count($chkfuser)<1)
            {
				$data	 =	array('Name'=>$name,'lname'=>$lname,'Email'=>$email,'ProfilePic'=>$picture,'LastUpdateDate'=>date("Y-m-d H:i:s"));
				//print_r($data);exit;
				$fileExs =	$accSet->updateProfile($data,$userID);

      
        $this->myclientdetails->updatedata_global('users',array('email'=>$email), 'front_userid', $userID);
				//print_r($fileExs);exit;
				if(isset($fileExs) && $fileExs!='')
				{
					echo 'Account updated successfully~1~'.$picture;
					exit;
				}
				else
				{
					echo 'Error: Resources are busy. Please try again~4';
					exit;
				}
			}
			else
			{
				echo 'Error: This email address is registered with a platform user~4';
				exit;
			}	
		}
		else
		{
			echo 'Failed: Resources are busy. Please try again~4';
			exit;
		}
		exit;

	}
	private function _generateHash($plainText, $salt = null)
	{
		define('SALT_LENGTH',9);          
          if ($salt === null)
                $salt = substr(md5(uniqid(rand(), true)), 0, SALT_LENGTH);
          else 
                $salt = substr($salt, 0, SALT_LENGTH);
          return $salt . sha1($salt . $plainText);
	}

	public function updatesecqueansAction()
	{
		//echo "<pre>"; print_r($this->_options['auth']['salt']);
		$request = 	$this->getRequest()->getParams();
		$filter = new Zend_Filter_Alnum();
		//echo'<pre>';print_r($request);die;
		$accSet	=	new Admin_Model_accountsetting();
		$userID		= $this->_userid;
		if(!empty($request['secretquestion']) && !empty($request['answer']))
		{
			$password 	= $this->_generateHash(trim($request['curpwd']));
			$data	 =	array('secretquestion'=>$request['secretquestion'],'answer'=>$filter->filter($request['answer']),'clientID'=>clientID);
			echo $accSet =	$accSet->updatesecque($data,$userID);
			exit;
		}
		exit;
	}

	public function updateaccountpassAction()
	{
		//echo "<pre>"; print_r($this->_options['auth']['salt']);
		$this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
		$request = 	$this->getRequest()->getPost();
		$accSet	=	new Admin_Model_accountsetting();
		$options['UserID'] = $this->_userid;
        $userresult = $this->myclientdetails->getAllMasterfromtable('tblUsers','*',$options);
       
		$userID		=	$this->_userid;
		if($request['req']=='checkpwd')
		{
			$password 	= $this->_generateHash(trim($request['curpwd']),$userresult[0]['Pass']);	
			echo $accSet->chkPassword($userID,$password);
			//echo 1;
			exit;
		}
		if($request['req']=='changepwd')
		{
			$password 	= $this->_generateHash(trim($request['curpwd']));
			$data	 =	array('Pass'=>$password,'LastUpdateDate'=>date("Y-m-d H:i:s"),'clientID'=>clientID);
			echo $accSet =	$accSet->updateProfile($data,$userID);
			exit;
		}
		exit;
	}

	public function setglobaltimeAction(){
		$request 	= 	$this->getRequest()->getParams();
        $expireTime = $this->_request->getPost('expireTime');
        $accSet	=	new Admin_Model_accountsetting();
        if(isset($expireTime) || !empty($expireTime))
        {
        	echo $success = $accSet->update_globaltimsetting($expireTime);
        	exit;
        }
	} 
	
	public function ajaxcategorylistAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest())
		{		
			$orderbyArr = array('Priority'=>'DESC');
			$category = $this->myclientdetails->getAllMasterfromtable('tblDbeeCats',array('CatID','CatName','Priority'),'',$orderbyArr);			
				foreach($category as $data1):
					$totPost = ''; 
					$delBtn = '<a class="btn btn-danger btn-mini catdelete" catid ="'.$data1['CatID'].'" type="post" href="javascript:void(0);"><i class="fa fa-trash"></i> Delete</a>';
					$postincat = 	$this->myclientdetails->passSQLquery("SELECT count(DbeeID) as tot FROM tblDbees WHERE tblDbees.clientID=".clientID."  AND FIND_IN_SET(".$data1['CatID'].", Cats)");
					if($postincat[0]['tot']>0) {
						$totPost = ', posts : '.$postincat[0]['tot'];
						$delBtn = '';
					} 
				$editBtn = '<a class="btn btn-green btn-mini editcat" type="post" name="'.$data1['CatName'].'" Priority="'.$data1['Priority'].'" href="javascript:void(0)"><i class="fa fa-repeat"></i> Edit</a> ';
				if(htmlentities($data1['CatName'])=='Miscellaneous')$editBtn='&nbsp;';
					$content  .= '<li id="'.$data1['CatID'].'">'.htmlentities($data1['CatName']).'<div class="priority">Priority: '.$data1['Priority'].' '.$totPost.'</div><span>'.$editBtn.$delBtn.'</span></li>';
				endforeach;
			$data['content'] = $content;		
		}
		else
		{
			$data['status']  = 'error';
			$data['message'] = 'Configuration has not been Set successfully';
		}
		return $response->setBody(Zend_Json::encode($data)); 
	}

	public function ajaxbiofieldlistAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest())
		{		
			
			$orderbyArr = array('priority'=>'DESC');
			$biofields = $this->myclientdetails->getAllMasterfromtable('tbl_biofields',array('id','name','priority'),'',$orderbyArr);			
				foreach($biofields as $data1):
				
				$totPost = ''; 
				$fldvalres='';
				$delBtn='';
				$fldvalres=$this->myclientdetails->passSQLquery("SELECT field_id,field_value FROM tblUserBiography where clientID='".clientID."' and  field_id='".$data1['id']."' and field_value<>''");


				if(count($fldvalres) < 1)
				{
				 $delBtn = '<a class="btn btn-danger btn-mini biodelete" bioid ="'.$data1['id'].'" type="post" href="javascript:void(0);"><i class="fa fa-trash"></i> Delete</a>';
				}

				$editBtn = '<a class="btn btn-green btn-mini editbio" type="post" name="'.$data1['name'].'" Priority="'.$data1['priority'].'" href="javascript:void(0)"><i class="fa fa-repeat"></i> Edit</a> ';
				if(htmlentities($data1['CatName'])=='Miscellaneous')$editBtn='&nbsp;';
					$content  .= '<li id="'.$data1['id'].'">'.htmlentities($data1['name']).'<div class="priority">Priority: '.$data1['priority'].' </div><span>'.$editBtn.$delBtn.'</span></li>';
				endforeach;
			$data['content'] = $content;		
		}
		else
		{
			$data['status']  = 'error';
			$data['message'] = 'Configuration has not been Set successfully';
		}
		return $response->setBody(Zend_Json::encode($data)); 
	}
	
	public function groupcategorylistAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest())
		{
			$orderbyArr = array('Priority'=>'DESC');
			$groupcategory = $this->myclientdetails->getAllMasterfromtable('tblGroupTypes',array('TypeID','TypeName','Priority'),'',$orderbyArr);
			foreach($groupcategory as $data1):
			$content  .= '<li id="'.$data1['TypeID'].'">'.$data1['TypeName'].'<div class="priority">Priority: '.$data1['Priority'].'</div><span><a class="btn btn-green btn-mini editcat" name="'.$data1['TypeName'].'" type="group" Priority="'.$data1['Priority'].'" href="javascript:void(0)"><i class="fa fa-repeat"></i> Edit</a>
			<a class="btn btn-danger btn-mini catdelete" catid ="'.$data1['TypeID'].'" type="group" href="javascript:void(0);"><i class="fa fa-trash"></i> Delete</a><span></li>';
			endforeach;
			$data['content'] = $content;
		}
		else
		{
			$data['status']  = 'error';
			$data['message'] = 'Configuration has not been Set successfully';
		}
		return $response->setBody(Zend_Json::encode($data));
	}
	public function groupcatAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		$filter = new Zend_Filter_StripTags();
		if ($this->getRequest()->isXmlHttpRequest())
		{
			$request = 	$this->getRequest()->getPost();
			$catname = $this->_request->getPost('groupcatname');
			$priority = (int)$this->_request->getPost('grouppriority');
			$typeID = $this->_request->getPost('typeID');	
			$data1['TypeName']=$filter->filter($catname);
			$data1['clientID']= $filter->filter(clientID);
			if($priority!='')$data1['Priority']=(int)$priority;
	
			if(!empty($typeID))
				$category = $this->myclientdetails->updatedata_global('tblGroupTypes',$data1, 'TypeID', $typeID);
			else
				$category = $this->myclientdetails->insertdata_global('tblGroupTypes',$data1);
	
			$content = 'message inser successfully!';
			$data['content'] = $content;
	
		}
		else
		{
			$data['status']  = 'error';
			$data['message'] = 'Configuration has not been Set successfully';
		}
		
		return $response->setBody(Zend_Json::encode($data));
	}
	public function delgroupcateAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
	
		if ($this->getRequest()->isXmlHttpRequest())
		{
			$request = 	$this->getRequest()->getPost();
			$catid = (int)$this->_request->getPost('cat');
			$type = (int)$this->_request->getPost('type');
			if($type=='post')
			$category = $this->myclientdetails->deletedata_global('tblDbeeCats','CatID',$catid);
			else 
			$category = $this->myclientdetails->deletedata_global('tblGroupTypes','TypeID',$catid);
			$content = 'categoty deleted successfully!';
			$data['content'] = $content;
		}
		else
		{
			$data['status']  = 'error';
			$data['message'] = 'Configuration has not been Set successfully';
		}
		return $response->setBody(Zend_Json::encode($data));
	}
	
	
	
	public function addcatAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
	
		if ($this->getRequest()->isXmlHttpRequest())
		{
			$request = 	$this->getRequest()->getPost();
			$catname = $this->_request->getPost('catname');
			$priority = (int)$this->_request->getPost('priority');
			$catid = $this->_request->getPost('cat');
			$filter = new Zend_Filter_StripTags();
			
			$data1['catname']=$filter->filter(stripslashes($catname)); 
			$data1['clientID']= clientID;
			if($priority!='')$data1['Priority']=$priority;
			
			if(!empty($catid))
				$category = $this->myclientdetails->updatedata_global('tblDbeeCats',$data1, 'CatID', $catid);
			else
				$category = $this->myclientdetails->insertdata_global('tblDbeeCats',$data1);
			
			$content = 'message inser successfully!';
			$data['content'] = $content;
			
		}
		else
		{
			$data['status']  = 'error';
			$data['message'] = 'Configuration has not been Set successfully';
		}
		
		return $response->setBody(Zend_Json::encode($data));
	}

	public function biofieldAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
	
		if ($this->getRequest()->isXmlHttpRequest())
		{
			$request = 	$this->getRequest()->getPost();
			$biofield = $this->_request->getPost('biofield');
			$priority = (int)$this->_request->getPost('priority');
			$id = $this->_request->getPost('id');
			$filter = new Zend_Filter_StripTags();
			
			$data1['name']=$filter->filter(stripslashes($biofield)); 
			$data1['clientID']= clientID;
			$field_code=strtolower(str_replace(' ','_',$biofield));

			$data1['field_code']=$filter->filter(stripslashes($field_code)); 
			
			if($priority!='')$data1['Priority']=$priority;
			
			if(!empty($id))
				$bioupd = $this->myclientdetails->updatedata_global('tbl_biofields',$data1, 'id', $id);
			else
				$bioupd = $this->myclientdetails->insertdata_global('tbl_biofields',$data1);
			
			$content = 'insert successfully!';
			$data['content'] = $content;
			
		}
		else
		{
			$data['status']  = 'error';
			$data['message'] = 'Configuration has not been Set successfully';
		}
		return $response->setBody(Zend_Json::encode($data));
	}

	public function delcatAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);	
		if ($this->getRequest()->isXmlHttpRequest())
		{
			$request = 	$this->getRequest()->getPost();
			$catid = (int)$this->_request->getPost('cat');
			$type = $this->_request->getPost('type');
			if($type=='post')
			$category = $this->myclientdetails->deletedata_global('tblDbeeCats','CatID',$catid);
			else if($type=='group')
			$category = $this->myclientdetails->deletedata_global('tblGroupTypes','TypeID',$catid);
			$content = 'categoty deleted successfully!';
			$data['content'] = $content;
		}
		else
		{
			$data['status']  = 'error';
			$data['message'] = 'Configuration has not been Set successfully';
		}
		return $response->setBody(Zend_Json::encode($data));
	}

	public function delbiofieldAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);	
		if ($this->getRequest()->isXmlHttpRequest())
		{
			$request = 	$this->getRequest()->getPost();
			$bioid = (int)$this->_request->getPost('bioid');
			
			
			$category = $this->myclientdetails->deletedata_global('tbl_biofields','id',$bioid);
			$content = 'field deleted successfully!';
			$data['content'] = $content;
		}
		else
		{
			$data['status']  = 'error';
			$data['message'] = 'Configuration has not been Set successfully';
		}
		return $response->setBody(Zend_Json::encode($data));
	}
	

	public function connectplateformuserAction()
  {      
    $request = $this->getRequest()->getParams();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$filter = new Zend_Filter_StripTags();
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$userlist = $this->_request->getPost('stringuserInfo');
      $fortype = $this->_request->getPost('fortype');
      $status = $this->_request->getPost('status');
      if($fortype=='twitter')
      {    
        $data = array(
            'twitterTag' => $status       
        );
      }
      else if($fortype=='promoted')
      {
        $data = array(
            'promoted' => $status        
        );
      }
      $this->user_model->updateInuser($data,$userlist);
      $data = array();
			$data['status'] = 'success';
			$data['message'] = "updated successfully";
		}
		else
		{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));

	}

	public function deleteplateformuserAction()
  {
	  $request = $this->getRequest()->getParams();
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);

		if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
      $UserID = $filter->filter($this->_request->getPost('UserID'));
			$type = $this->_request->getPost('type');
      $deleteplateformuser = $getuserpl->deletedata_twittertaguser($UserID); 
			$data['status'] = 'success';
			$data['message'] = "updated successfully";
		}
		else
		{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));	

	}

	public function helpvideoAction(){
		$request = $this->getRequest()->getParams();
		//echo'<pre>';print_r($request);die;
		$this->view->statusShow = 'comming soon';
	}	
	
	public function addfeedlogoAction()
	{ 
		$this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {

          if($_FILES['file']['name'])
          {
            $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION); 
            $feedlogo  = strtolower(time().mt_rand(1,79632).'.'.$ext);
            @copy($_FILES['file']['tmp_name'], Front_Public_Path.'/images/rsslogos/' .$feedlogo);
            $dataArray['bgimage'] = $feedlogo;
            $dataArray['content'] = '<input type="hidden" name="feedimghidden" id="feedimghidden" value="'.$feedlogo.'" >';
          }
        } 
        return $response->setBody(Zend_Json::encode($dataArray));

	
		 
	}
	public function addfeedAction()
	{
		$data = array();
		$error = false;
		$postData = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
	
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$logo = $this->_request->getPost('filename');
			$title = $this->_request->getPost('feedtitle');
    
			$feedurldatalink = $this->_request->getPost('feedurl');
			$request = 	$this->getRequest()->getPost();
			$datainser['Country'] = (int)$this->_request->getPost('country');
			$datainser['Name'] = $title;
			$datainser['URL'] = $feedurldatalink;
			$datainser['clientID'] = clientID;
     
			//$datainser['Logo'] = $logo;	
			//$feedLinks = Zend_Feed_Reader::import($this->_request->getPost('feedurl'));	
			/*if ($logo=='') {
			   $data['status']  = 'noexitlogo';
			   $error = true;
			}*/

	        try {
	            //$links = Zend_Feed_Reader::findFeedLinks($feedurldatalink);
	           $feedLinks = Zend_Feed_Reader::import($this->_request->getPost('feedurl'));	
	           $feedtitlesn = $feedLinks->getTitle();
	        } catch(Exception $e) {
	            $e->getMessage();
	        }
	       
			if (empty($feedtitlesn)) {
			   $data['status']  = 'notexit';
			   $error = true;
			}
			
			if(!$error){
        $cntrsst = $this->myclientdetails->getAllMasterfromtable('tblRssSites',array('ID'),array('clientID'=>clientID),'','','','noclientID');
        if(count($cntrsst)<1){
           $datainser['isdefault'] = 1;
        }
				$this->myclientdetails->insertdata_global('tblRssSites',$datainser);

				$data['status']  = 'success';
				$data['message'] = 'rss feed has been insert successfully';
				$data['feedtitlesn'] = $feedtitlesn;
			}
		}
		else
		{
			$data['status']  = 'error';
			$data['message'] =  'rss feed has been not insert successfully';
		}
		return $response->setBody(Zend_Json::encode($data));
	}
	public function updatefeedAction()
	{
		$data = array();
		$postData = array();
		$accountobj = new Admin_Model_accountsetting();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
	
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$request = 	$this->getRequest()->getPost();
			$feed = $this->_request->getPost('feed');
			//$variable = explode(',', $feed);
			$variable = $feed;
			if(count($variable>0)){
				$row = array(
						'Active' => '0',
						'isdefault'		=>	'0'
				);
				
				$category = $this->myclientdetails->updatedata_global('tblRssSites',$row,'clientID',clientID);
				foreach ($variable as $value)
				{
					$row = array(						
							'Active' => '1'						
					);
						
					
				
					$category = $this->myclientdetails->updatedata_global('tblRssSites',$row, 'ID', $value,'clientID',clientID);
				}
			}
				$data['status']  = 'success';
				$data['message'] = 'rss feed has been insert successfully';
			
		}
		else
		{
			$data['status']  = 'error';
			$data['message'] =  'rss feed has been not insert successfully';
		}
		return $response->setBody(Zend_Json::encode($data));
	}

   public function updatefeedalldectiveAction()
  {
    $data = array();
    $postData = array();
    $accountobj = new Admin_Model_accountsetting();
    $this->_helper->layout()->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
    $response = $this->getResponse();
    $response->setHeader('Content-type', 'application/json', true);
  
    if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
    {
      $request =  $this->getRequest()->getPost();
      $action = $this->_request->getPost('action');
      //$variable = explode(',', $feed);
     if($action=='active'){
        $row = array(
              'Active' => '1'           
          );
        $data['message'] = 'active';
      }else{
        $row = array(
            'Active' => '0'           
        );
        $data['message'] = 'deactive';
      }
        
        
        $category = $this->myclientdetails->updatedata_global('tblRssSites',$row,'clientID',clientID);
       
    
        $data['status']  = 'success';
        
      
    }
    else
    {
      $data['status']  = 'error';
      $data['message'] =  'rss feed has been not insert successfully';
    }
    return $response->setBody(Zend_Json::encode($data));
  }
    
    public function rollistAction(){
      $request = $this->getRequest()->getParams();
      $this->_helper->layout()->disableLayout();
      $this->_helper->viewRenderer->setNoRender(true);
      $roles = new Admin_Model_User();
      $selctAllRols = $roles->getRols();
       $content.="<select name='rools' class='' id='userRols'>";
      foreach ($selctAllRols as $rolval):
         $content .= "<option value=".$rolval['role']." name='selectrols' selectrol=".$rolval['role_id'].">
                      ".$rolval['role']."
                    </option>";
      endforeach;
      $content.="</select>";
      print_r($content);exit();
    }

	public function usersrollistAction(){
      $request = $this->getRequest()->getParams();
      //echo'<pre>';print_r($request['selectrol']);die;
      //return $request['selectrol'];
      $filter = new Zend_Filter_StripTags();
      $this->_helper->layout()->disableLayout();
      $this->_helper->viewRenderer->setNoRender(true);
      $rol = $filter->filter($this->_request->getPost('selectrol'));
      $roles = new Admin_Model_User();
      //echo count($selctuserswithrol);die;
      $selctuserswithrol = $roles->getuserwithRols($rol);
      //echo count($selctuserswithrol);die;
      if(count($selctuserswithrol)>0){
       $content = '<div class="responsiveTable"><table class="table-border userListTable table table-hover table-stripe"><thead>      
                      <tr><td colspan="8"><div class="rpGraphTop clearfix">User list according rols</div></td></tr>
                      <tr>
                      <td style="width: 20%;">User name</td>
                      <td style="width: 32%;">User email</td>
                      <td style="width: 15%;">Role</td>
                      </tr>
                      <tbody>';
      }else{
      	$content ='<div class="responsiveTable"><table class="table-border userListTable table table-hover table-stripe">
                     <thead><tr><td colspan="5">No users found!</td></tr></thead>
                   </table></div>';
      }
      
     foreach ($selctuserswithrol as $Row):
         //echo'<pre>';print_r($Row);
         $content .= "<tr class=".msgalluserslist.">
	                       <td>".$this->myclientdetails->customDecoding($Row['Name'])."</td>
	                       <td>".$this->myclientdetails->customDecoding($Row['Email'])."</td>
	                       <td>".$Row['role']."</td>                          
	                   </tr>";
     endforeach;	
     $content .= '</tbody></table></div>';
     print_r($content);exit();                                 
	}

	
	 public function deletefeedAction()
    {
        $data = array();
    	$this->_helper->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	$response = $this->getResponse();
    	$response->setHeader('Content-type', 'application/json', true);
    	if($this->getRequest()->getMethod() == 'POST' && $this->getRequest()->isXmlHttpRequest())
    	{
    		
    		$feedid = (int) $this->_request->getPost('feedid');
    		$accountobj = new Admin_Model_accountsetting();
    		if($accountobj->deletefeed($feedid)){

				$data['content']='Feed deleted successfully!';
    		}

    	}else{

    		$data['status']='error';
    		$data['content']='not deleted';
    	}
    		return $response->setBody(Zend_Json::encode($data));

    }
    
    public function deleteallAction()
    {
    	$data = array();
    	$this->_helper->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	$response = $this->getResponse();
    	$response->setHeader('Content-type', 'application/json', true);
    	if($this->getRequest()->getMethod() == 'POST' && $this->getRequest()->isXmlHttpRequest())
    	{
    
    		$feedid = (int) $this->_request->getPost('feedid');
    		$accountobj = new Admin_Model_accountsetting();
    		if($accountobj->deleteall($feedid)){
    
    			$data['content']='Feed deleted successfully!';
    		}
    
    	}else{
    
    		$data['status']='error';
    		$data['content']='not deleted';
    	}
    	return $response->setBody(Zend_Json::encode($data));
    
    }
    
    public function activefeedAction()
    {
    	$data = array();
    	$this->_helper->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	$response = $this->getResponse();
    	$response->setHeader('Content-type', 'application/json', true);
    	if($this->getRequest()->getMethod() == 'POST' && $this->getRequest()->isXmlHttpRequest())
    	{
    
    		$feedid = (int) $this->_request->getPost('feedid');
    		$active = (int) $this->_request->getPost('active');
    		$activesn = (int) $this->_request->getPost('activesn');
    		$accountobj = new Admin_Model_accountsetting();
    		if($activesn==1)
				$data1 = array('Active'=> $active);
			else
			  $data1 = array('isdefault'=> $active,'Active'=> $active); 		
    		$rs = $accountobj->activefeed($data1,$feedid);
    
    			$data['content']='Feed activated successfully!';
    			$data['active']=$active;
    			$data['status']=$rs;
    		
    
    	}else{
    
    		$data['status']='error';
    		$data['content']='not deleted';

    	}
    	return $response->setBody(Zend_Json::encode($data));
    
    }
    
	  
}

