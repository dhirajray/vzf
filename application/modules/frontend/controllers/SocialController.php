<?php
class SocialController extends IsController
{
    public function init()
    {
        parent::init();
        if($this->_userid==''){
            $this->_redirect('/');
        }
    }
    public function redirectToHome()
    {
        $storage    = new Zend_Auth_Storage_Session();
        $auth        =  Zend_Auth::getInstance();
        if(!$auth->hasIdentity()){
           $this->_redirect('/');
        }
    }

     public function getTwitterTimeLine($twitter_connect_data,$userID)
    {
            $twitteroauth = new TwitterOAuth(twitterAppid, twitterSecret, 
                $twitter_connect_data['twitter_access_token'], 
            $twitter_connect_data['twitter_token_secret']);
            
            $TwitterResult = $this->myhome_obj->getSocialFeed($userID);
            $screen_name = $twitter_connect_data['screen_name'];
            
            if($userID==adminID)
                $timeLine = 'user_timeline';
            else
                $timeLine = 'home_timeline';

            if(!empty($TwitterResult))
            {
                $dbStoreTime = strtotime($TwitterResult['twitterUpdatedTime']);
                $currentTime = strtotime(date('Y-m-d H:i:s'));
                $difference = $currentTime-$dbStoreTime;
                if($difference > 900)
                {
                    $feedResult = $twitteroauth->get('statuses/'.$timeLine, array(
                                'include_entities' => true,
                                'include_rts' => true,
                                'screen_name' => $screen_name,
                                'page' => 1
                     ));
                    $twitterData['requestCount'] = 1;
                    $twitterData['twitterFeed'] = serialize($feedResult);
                    $twitterData['status'] = 1;
                    $twitterData['twitterUpdatedTime'] = date('Y-m-d H:i:s');
                    $twitterData['user_id'] = $userID;
                    $twitterData['timestamp'] = date('Y-m-d H:i:s');
                    $data['database'] = false;
                    $this->myhome_obj->updateSocialFeed($twitterData,$userID);
                }
                else if($TwitterResult['requestCount']<15)
                {
                    
                    $feedResult = $twitteroauth->get('statuses/'.$timeLine, array(
                                'include_entities' => true,
                                'include_rts' => true,
                                'screen_name' => $screen_name,
                                'page' => 1
                     ));
        
                    $twitterData['requestCount'] = ($TwitterResult['requestCount']+1);
                    $twitterData['twitterFeed'] = serialize($feedResult);
                    $twitterData['status'] = 1;
                    $twitterData['twitterUpdatedTime'] = date('Y-m-d H:i:s');
                    $twitterData['user_id'] = $userID;
                    $twitterData['timestamp'] = date('Y-m-d H:i:s');
                    $data['database'] = false;
                    $this->myhome_obj->updateSocialFeed($twitterData,$userID);
                }
                else
                { 
                    $feedResult = unserialize($TwitterResult['twitterFeed']);
                    $data['database'] = true;
                }
            }else
            { 
                $feedResult = $twitteroauth->get('statuses/'.$timeLine, array(
                        'include_entities' => true,
                        'include_rts' => true,
                        'screen_name' => $screen_name,
                        'page' => 1
                ));

                $twitterData['requestCount'] = 1;
                $twitterData['twitterFeed'] = serialize($feedResult);
                $twitterData['status'] = 1;
                $twitterData['twitterUpdatedTime'] = date('Y-m-d H:i:s');
                $twitterData['user_id'] = $userID;
                $twitterData['timestamp'] = date('Y-m-d H:i:s');
                $data['database'] = false;
                $this->myhome_obj->insertSocialFeed($twitterData);
            }
            $data['feedResult'] = $feedResult;   
            $data['timeLine'] = $timeLine;   
        return $data;
    }

    public function twitterfeedhometimelineAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && !empty($this->twitter_connect_data)) 
        {
            $result = $this->getTwitterTimeLine($this->twitter_connect_data,$this->_userid); 
            $data['database'] = $result['database'];
            $data['status']  = 'success';
            $data['content'] = $result['feedResult'];
            $data['timeLine'] = $result['timeLine'];
            if(isset($result['feedResult']["errors"])) {
                $data['status']  = 'error';
                $data['message'] = "Sorry, there seems to be an error. Please try again in few minutes.";
            }
            
        }else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }

    /* Twitter friend user_timeline and 
    *  user status from twitter like follow and unfollow
    *  it ll return json
    */

    public function twitteruserfeedhometimelineAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest()) 
        {
            $screen_name = $this->_request->getPost('screenname');
            $dbeeUsername = $this->_request->getPost('dbeeUsername');
            $result_array = $this->User_Model->getUserDataByUsername($dbeeUsername);
            $twitter_connect_data = Zend_Json::decode($result_array['0']['twitter_connect_data']);
            
            $result = $this->getTwitterTimeLine($twitter_connect_data,$result_array['0']['UserID']); 

            $twitteroauth = new TwitterOAuth(twitterAppid, twitterSecret, 
                $twitter_connect_data['twitter_access_token'], 
            $twitter_connect_data['twitter_token_secret']);
            
            
            $userResult = $twitteroauth->get('users/lookup', array(
                            'screen_name' => $twitter_connect_data['screen_name']
                        ));

            if($this->twitter_connect_data['screen_name']!='')
            {
                $params = array(
                    'source_screen_name' => $this->twitter_connect_data['screen_name'],
                    'target_screen_name'  => $twitter_connect_data['screen_name']
                );

                $follow = $twitteroauth->get('friendships/show',$params,true);
                $data['followstatus'] = $follow;
            }
            $data['status']  = 'success';
            $data['database'] = $result['database'];
            $data['status']  = 'success';
            $data['content'] = $result['feedResult'];
            $data['userResult'] = $userResult;
            $data['timeLine'] = $result['timeLine'];
            if(isset($result['feedResult']["errors"])) {
                $data['status']  = 'error';
                $data['message'] = "Sorry, there seems to be an error. Please try again in few minutes.";
            }
        
        }else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }

    

    public function twitteradminfeedhometimelineAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest()) 
        {
            $rowData = $this->User_Model->getUserDetail(adminID);
            if($rowData['twitter_connect_data']!='')
            {
                $twitter_connect_data = Zend_Json::decode($rowData['twitter_connect_data']);                
                $result = $this->getTwitterTimeLine($twitter_connect_data,adminID);    
                $data['status']  = 'success';
                $data['database'] = $result['database'];
                $data['status']  = 'success';
                $data['content'] = $result['feedResult'];
                $data['timeLine'] = $result['timeLine'];
            }    
            else
            {
                $data['status']  = 'error';
                $data['message'] = "Sorry, there seems to be an error. Please try again in few minutes.";
            }
            
        }else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
            
        return $response->setBody(Zend_Json::encode($data));
    }
    public function facebookfeedhometimelineAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && !empty($this->facebook_connect_data)) 
        {
            $params = array(
            'appId' => facebookAppid,
            'secret' => facebookSecret,
            'domain' => facebookDomain
            );
            $page = 25;
            $facebook       = new Facebook($params);
            $getAccessToken = $this->facebook_connect_data['access_token'];
            try 
            {
                $result = $facebook->api('/'.$this->facebook_connect_data['facebookid'].'/feed?limit='.$page,'GET',array("access_token"=>$getAccessToken));
                $data['status']  = 'success';
                $data['content'] = $result;
                $data['access_token'] = $getAccessToken;
                $data['facebook_id'] = $this->facebook_connect_data['facebookid'];
                $data['facebook_name'] = $this->facebook_connect_data['facebookname'];
                $data['status']  = 'success';
            }
            catch (FacebookApiException $e) 
            {
                $data['type'] =  $e->getType();
                $data['message'] =  $e->getMessage();
                $data['status']  = 'error';
            }
            
        }else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }
   
    public function facebookfriendfeedhometimelineAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest()) 
        {
                $id = $this->_request->getPost('facebookid');
                $dbeeUsername = $this->_request->getPost('dbeeUsername');
                $result_array = $this->User_Model->getUserDataByUsername($dbeeUsername);
                $facebook_connect_data = Zend_Json::decode($result_array['0']['facebook_connect_data']);
                
                $params = array(
                            'appId' => facebookAppid,
                            'secret' => facebookSecret,
                            'domain' => facebookDomain
                        );
                $page = 25;
                $facebook       = new Facebook($params);
                $getAccessToken = $facebook_connect_data['access_token'];
                 try {
                    
                    $result = $facebook->api('/'.$id.'/feed?limit='.$page,'GET',array("access_token"=>$getAccessToken));
                   if($this->facebook_connect_data['facebookid']!=''){
                        $friendStatus = $facebook->api('/'.$id.'/friends/'.$this->facebook_connect_data['facebookid'], 'GET', array(
                        "access_token" => $this->facebook_connect_data['access_token']));
                    }else{
                        $friendStatus = '';
                    }


                    $friendStatus = ($friendStatus==null)?'':$friendStatus;
                    $data['status']  = 'success';
                    $data['content'] = $result;
                    $data['friendStatus'] = $friendStatus;
                    $data['facebook_id'] = $facebook_connect_data['facebookid'];
                    $data['facebook_name'] = $facebook_connect_data['facebookname'];
                    
                }
                catch (FacebookApiException $e) 
                {
                    $data['type'] =  $e->getType();
                    $data['message'] =  $e->getMessage();
                    $data['status']  = 'error';
                }          
        }else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }

    public function makefollowuptwitterAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && !empty($this->twitter_connect_data)) 
        {
                $screen_name = $this->_request->getPost('screen_name');
                
                $twitteroauth = new TwitterOAuth(twitterAppid, twitterSecret, 
                    $this->twitter_connect_data['twitter_access_token'], 
                $this->twitter_connect_data['twitter_token_secret']);

                $feedResult = $twitteroauth->post('friendships/create', array(
                                'screen_name' => $screen_name
                            ));

                $data['status']  = 'success';
                $data['content'] = $feedResult;
                $data['message'] = "You are following now ".$screen_name.' at twitter';
            
        }else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    public function maketweetfavAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && !empty($this->twitter_connect_data)) 
        {
                $tweet_id = $this->_request->getPost('tweet_id');
                
                $twitteroauth = new TwitterOAuth(twitterAppid, twitterSecret, 
                    $this->twitter_connect_data['twitter_access_token'], 
                $this->twitter_connect_data['twitter_token_secret']);

                $feedResult = $twitteroauth->post('favorites/create', array(
                                'id' => $tweet_id
                            ));

                $data['status']  = 'success';
                $data['content'] = $feedResult;
            
        }else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    /* Twitter user status from twitter like follow and unfollow
    *  it ll return json
    */

    public function checkfollowstatusAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && !empty($this->twitter_connect_data)) 
        {
            $screen_name = $this->_request->getPost('screen_name');
            $dbeeid = $this->_request->getPost('dbeeid');
            $dbee_data = $this->Myhome_Model->getDbeeDetails($dbeeid);
            $params = array(
                'source_screen_name' => $this->twitter_connect_data['screen_name'],
                'target_screen_name'  => $screen_name
            );
            $twitteroauth = new TwitterOAuth(twitterAppid, twitterSecret, 
                    $this->twitter_connect_data['twitter_access_token'], 
                $this->twitter_connect_data['twitter_token_secret']);
            $follow = $twitteroauth->get('friendships/show',$params,true);
            $data['status'] = 'success';
            $data['result'] = $follow;
            $data['shortUrl'] = $this->shortUrl(BASE_URL.'/dbee/'.$dbee_data['dburl'].'?logintwitter=true');
        }
        else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }

    public function twitterstatuspostAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && !empty($this->twitter_connect_data)) 
        {
                $status = $this->_request->getPost('tweet');
                $tweet_id = $this->_request->getPost('tweet_id');
               
                $twitterscreenname = $this->_request->getPost('twitterscreenname');
                
                $embedlinkPost = $this->_request->getPost('embedlinkPost');
               
                $twitteroauth = new TwitterOAuth(twitterAppid, twitterSecret, 
                    $this->twitter_connect_data['twitter_access_token'], 
                $this->twitter_connect_data['twitter_token_secret']);

                $twitteroauth->post('statuses/update', array(
                                    'status' => '@'.$twitterscreenname.' '.$embedlinkPost.' '.$status,
                                    'in_reply_to_status_id' => $tweet_id
                                ));

                $data['status']  = 'success';
                $data['content'] = $feedResult;
            
        }else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }

    public function makeshortlink($dbeeid)
    {
        $checkdbeeexist = $this->myhome_obj->checkdbeeexist($dbeeid);
        return ' '.$this->shortUrl(BASE_URL.'/dbee/' . $checkdbeeexist[0]['dburl']).' ';
    }


    public function retweetAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && !empty($this->twitter_connect_data)) 
        {
                $tweet_id = $this->_request->getPost('tweet_id');

                $twitteroauth = new TwitterOAuth(twitterAppid, twitterSecret, 
                    $this->twitter_connect_data['twitter_access_token'], 
                $this->twitter_connect_data['twitter_token_secret']);

                $feedResult = $twitteroauth->post('statuses/retweet/'.$tweet_id);
                $data['tweet_id'] = $tweet_id;
                $data['status']  = 'success';
                $data['content'] = $feedResult;
            
        }else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    public function maketweetunfavAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && !empty($this->twitter_connect_data)) 
        {
                $tweet_id = $this->_request->getPost('tweet_id');
                
                $twitteroauth = new TwitterOAuth(twitterAppid, twitterSecret, 
                    $this->twitter_connect_data['twitter_access_token'], 
                $this->twitter_connect_data['twitter_token_secret']);

                $feedResult = $twitteroauth->post('favorites/destroy', array(
                                'id' => $tweet_id
                            ));

                $data['status']  = 'success';
                $data['content'] = $feedResult;
            
        }else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    public function makeunfollowuptwitterAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && !empty($this->twitter_connect_data)) 
        {
                $screen_name = $this->_request->getPost('screen_name');
                
                $twitteroauth = new TwitterOAuth(twitterAppid, twitterSecret, 
                    $this->twitter_connect_data['twitter_access_token'], 
                $this->twitter_connect_data['twitter_token_secret']);

                $feedResult = $twitteroauth->post('friendships/destroy', array(
                                'screen_name' => $screen_name
                            ));

                $data['status']  = 'success';
                $data['content'] = $feedResult;
            
        }else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
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
                    $graph_url_pages = "https://graph.facebook.com/me/accounts?access_token=".$access_token;
                    
                    $dataArray['facebookPage'] = file_get_contents($graph_url_pages);

                    $user_personal_info['facebook_connect_data'] = Zend_Json::encode($dataArray);

                    $user_personal_info['UserID'] = $this->_userid;
                    
                    $changeuserinfo = $this->User_Model->updateinfouser($user_personal_info);
                    
                    $result_array = $this->User_Model->userdetailall($this->_userid);

                    $this->myclientdetails->sessionWrite($result_array[0]);
                    
                    $this->_redirect('/user/'.$this->myclientdetails->customDecoding($result_array[0]['Username']).'?type='.$this->session_name_space->type);                    

                }
                catch (FacebookApiException $e) {
                    $data['type'] =  $e->getType();
                    $data['message'] =  $e->getMessage();
                    $data['status']  = 'error';
                }
            }
        }
        $this->_redirect($facebook->getLoginUrl(array(
         'scope' => 'user_posts,user_friends,user_birthday,publish_pages,publish_actions,public_profile,email,manage_pages,user_tagged_places'
        )));
    }
    public function facebookpageAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest()) 
        {
            $params = array(
                'appId' => facebookAppid,
                'secret' => facebookSecret,
                'domain' => facebookDomain
            );

            $result = $this->commonmodel_obj->getFacebookPageConfiguration();

            $page = split("-",$result['facebook_page_data']);

            $page_token = $page[0];
            $facebookpagename = $result['facebookpagename'];
            $page_id= $page[1];
            $facebook  = new Facebook($params);

            try {
                $result = $facebook->api('/'.$page_id.'/feed?limit=25','GET',array("access_token"=>$page_token));
                $data['status']  = 'success';
                $data['content'] = $result;
                $data['facebookpagename'] = $facebookpagename;
                $data['page_access_token'] = $page_token;
            }
            catch (FacebookApiException $e) {
                $data['type'] =  $e->getType();
                $data['message'] =  $e->getMessage();
                $data['status']  = 'error';
            }
        }else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));        
    }
   
     public function twitterinvitationAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $filter                 = new Zend_Filter_StripTags();
        $response               = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {   
            $screen_name = explode(',', $this->_request->getPost('contcat_name')); 
            $twitteroauth       = new TwitterOAuth(twitterAppid, twitterSecret, $this->twitter_connect_data['twitter_access_token'], 
            $this->twitter_connect_data['twitter_token_secret']);
            $dbid = (int) $this->_request->getPost('dbid');
            
            $dbee_urltitles = $this->myhome_obj->getdburltitle($dbid);
            
            $TokenArray = $this->myhome_obj->checkExistenceTokenexpert($dbid,'twitter');
            
            if ($TokenArray['token'] != '')
                $token = $TokenArray['token'];
            else
                $token = time();
            
            if (!empty($screen_name)) 
            {
                foreach ($screen_name as $value) 
                {
                    $twitterArray = explode('@', $value);        
                    $url = $this->shortUrl(BASE_URL . '/dbee/' . $dbee_urltitles . '?type=twitter&token=' . $token . '&oathtoken=' . $twitterArray[1]);
                   
                    $message=str_replace("[EXPERT_TEXT]", trim($this->expertText),$this->getSocialShareMsg['expert_share']);
                    

                    $twitteroauth->post('statuses/update', array(
                        'status' => '@' . $twitterArray[0] . $message . $url
                    ));

                    $type = 'twitter';
                    $data = array(
                        'dbeeid' => $dbid,
                        'token' => $token,
                        'socialid' => $twitterArray[1],
                        'type' => $type,
                        'name' => $twitterArray[0],
                        'currenttime' => date('Y-m-d H:i:s'),
                        'clientID' => clientID
                    );
                    $this->myhome_obj->insertininviteexport($data);
                }
            }
            $data['status']  = 'success';
        } else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
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
      public function socialtypeAction()
    {
        $data = array();
        $Text = '';
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $filter = new Zend_Filter_StripTags();
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        {   
            $forto = $this->_request->getPost('forto');
            $uniqueValue = (int)$this->_request->getPost('uniqueValue');
            
            if($forto=='shareOnPost')
            {
                $dbee_data = $this->myhome_obj->getDbeeDetails($uniqueValue);
                $data['heading'] = 'Share post on';
                $this->session_name_space->callBackUrl = BASE_URL.'/dbee/'.$dbee_data['dburl'];
            }
            else if($forto=='inviteToSite')
            {
                $data['heading'] = 'Invite social connections';
                $this->session_name_space->callBackUrl = BASE_URL.'/user/'.$this->myclientdetails->customDecoding($this->session['Username']);
            }
            else if($forto=='inviteToGroup')
            {
                $data['heading'] = 'Invite social connections';
                $this->session_name_space->callBackUrl = BASE_URL.'/Group';
            }

            if($this->socialloginabilitydetail['Facebookstatus']==0)
            {
                    if($this->facebook_connect_data['access_token']!='')    
                        $Text .='<div class="shareAllSocials fbAllShare" onClick="SocialUserSearch(\''.$forto.'\','.$uniqueValue.',\'facebook\')" >
                                  <div class="signwithSprite signWithSpriteFb">
                                    <i class="fa dbFacebookIcon fa-5x"></i>
                                    <span>Facebook</span>
                                  </div>
                                </div>';
                    else
                          $Text .='<a href="/social/facebook?type=facebook"  class="shareAllSocials fbAllShare">
                                  <div class="signwithSprite signWithSpriteFb">
                                    <i class="fa dbFacebookIcon fa-5x"></i>
                                    <span>Facebook</span>
                                  </div>
                                    </a>';
            }

            if($this->socialloginabilitydetail['Twitterstatus']==0)
            {

                    if($this->twitter_connect_data['twitter_access_token']!='' && $this->twitter_connect_data['twitter_token_secret']!='')  
                        $Text .='<div class="shareAllSocials twAllShare" onClick="SocialUserSearch(\''.$forto.'\','.$uniqueValue.',\'twitter\')" >
                                      <div class="signwithSprite signWithSpriteTwitter">
                                        <i class="fa dbTwitterIcon fa-5x"></i>
                                        <span>Twitter</span>
                                      </div>
                                    </div>';
                    else
                          $Text .='<a href="/twitter?type=twitter"  class="shareAllSocials twAllShare">
                                      <div class="signwithSprite signWithSpriteTwitter">
                                        <i class="fa dbTwitterIcon fa-5x"></i>
                                        <span>Twitter</span>
                                      </div>
                                    </a>';
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

     /**
     *  all type of social share ll be display
     */
    public function shareonwallAction()
    {
        $data = array();
        $Text = '';
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $filter   = new Zend_Filter_StripTags();
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $dbeeid = (int) $this->_request->getPost('dbeeID');
            $dbee_urltitles                                = $this->myhome_obj->getdburltitle($dbeeid); // get seo link for makeing dbee url
            $this->session_name_space->callBackUrl    = BASE_URL . '/dbee/' . $dbee_urltitles.'?share=';

            // start facebook icon code
            if($this->socialloginabilitydetail['Facebookstatus']==0)
            {
                if($this->facebook_connect_data['access_token']!='') 
                    $Text .= '<div class="shareAllSocials fbAllShare"  onClick="shareOnwallFacebook(this)">
                            <div class="signwithSprite signWithSpriteFb">
                                <i class="fa dbFacebookIcon fa-5x"></i>
                                <span>Facebook</span>
                            </div>
                        </div>';
                else
                    $Text .='<a href="/social/facebook?type=facebook"  class="shareAllSocials fbAllShare">
                                  <div class="signwithSprite signWithSpriteFb">
                                    <i class="fa dbFacebookIcon fa-5x"></i>
                                    <span>Facebook</span>
                                  </div>
                                    </a>';

            }
            // end facebook icon code

            // start twitter icon code
            if($this->socialloginabilitydetail['Twitterstatus']==0)
            {
                if($this->twitter_connect_data['twitter_access_token']!='' && $this->twitter_connect_data['twitter_token_secret']!='')
                 $Text .= '<div class="shareAllSocials twAllShare"  onClick="TwittershareOnWall(this)">
                            <div class="signwithSprite signWithSpriteTwitter">
                                <i class="fa dbTwitterIcon fa-5x"></i>
                                <span>Twitter</span>
                            </div>
                        </div>';
                else                 
                    $Text .= '<a href="/twitter" class="shareAllSocials twAllShare">
                            <div class="signwithSprite signWithSpriteTwitter">
                                <i class="fa dbTwitterIcon fa-5x"></i>
                                <span>Twitter</span>
                            </div>
                        </a>';
            }
            
            // end twitter icon code
            
            
            // end linkedin icon code
            $data['content'] = $Text;
            $data['status']  = 'success';
            
        } else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    public function linkedinAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->getGlobalSettings() == 'block') 
        {
            $this->_helper->flashMessenger->addMessage('All social sharing and connections have been disabled by the platform admin.');
            $this->_redirect("/");
        }
        $config['base_url']        = BASE_URL;
        $config['callback_url']    = BASE_URL . '/social/callback';
        $config['linkedin_access'] = linkedinAppid;
        $config['linkedin_secret'] = linkedinSecret;
        
        # First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
        $linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], $config['callback_url']);
        //$linkedin->debug = true;
        
        # Now we retrieve a request token. It will be set as $linkedin->request_token
        $linkedin->getRequestToken();
        
        $this->session_name_space->requestToken = serialize($linkedin->request_token);
        
        # With a request token in hand, we can generate an authorization URL, which we'll direct the user to
        //echo "Authorization URL: " . $linkedin->generateAuthorizeUrl() . "\n\n";
        header("Location: " . $linkedin->generateAuthorizeUrl());
        
    }
    
    public function callbackAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $config['base_url']        = BASE_URL;
        $config['callback_url']    = BASE_URL . '/social/callback';
        $config['linkedin_access'] = linkedinAppid;
        $config['linkedin_secret'] = linkedinSecret;
        $auth        = Zend_Auth::getInstance();  
        # First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
        $linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], '');
        //$linkedin->debug = true;
        if($_REQUEST['oauth_problem']=='user_refused'){
            $this->_redirect(BASE_URL);
        }
        if (isset($_REQUEST['oauth_verifier'])) 
        {
            $this->session_name_space->oauth_verifier = $_REQUEST['oauth_verifier'];
            $linkedin->request_token = unserialize($this->session_name_space->requestToken);
            $linkedin->oauth_verifier = $this->session_name_space->oauth_verifier;
            $linkedin->getAccessToken($_REQUEST['oauth_verifier']);
            $this->session_name_space->oauth_access_token = serialize($linkedin->access_token);
            
            header("Location: " . $config['callback_url']);
            exit;
        } else {
            $linkedin->request_token  = unserialize($this->session_name_space->requestToken);
            $linkedin->oauth_verifier = $this->session_name_space->oauth_verifier;
            $linkedin->access_token   = unserialize($this->session_name_space->oauth_access_token);
        }
        $resultJson = $linkedin->getProfile("~:(id,email-address,first-name,last-name,picture-url;secure=true,date-of-birth)?format=json");
        $jsonData = json_decode($resultJson);
        
        $email = $jsonData->emailAddress;
        $firstName = $jsonData->firstName;
        $lastName = $jsonData->lastName;
        $id = $jsonData->id;
        // set offline token in linkedin 
        $dataArray['request_token'] = $this->session_name_space->requestToken;
        $dataArray['oauth_verifier'] = $this->session_name_space->oauth_verifier;
        $dataArray['oauth_access_token'] = $this->session_name_space->oauth_access_token;
        $dataArray['connnected'] = 'connnected';
        $dataArray['firstName'] = $firstName;
        $dataArray['id'] = $id;
        $user_personal_info['linkedin_connect_data'] = Zend_Json::encode($dataArray);;
        $this->myclientdetails->updatedata_global('tblUsers',$user_personal_info,'UserID',$this->_userid);
        $dataall = $this->myclientdetails->getfieldsfromtable('*','tblUsers','UserID',$this->_userid,'clientID',clientID);

        $this->myclientdetails->sessionWrite($dataall[0]);
            
        // end offline token in linkedin
         if(isset($this->session_name_space->callBackUrl) && $this->session_name_space->callBackUrl!='')
            $this->_redirect($this->session_name_space->callBackUrl."linkedin");
        else
            $this->_redirect('/user/'.$this->myclientdetails->customDecoding($dataall[0]['Username']));                    

    }
    public function photouploadAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $postData          = $this->_request->getPost('postData');
            $isPostOnPlateForm = $this->_request->getPost('isPostOnPlateForm');
            $picName           = $this->_request->getPost('socialpich');
            $status            = $this->_request->getPost('PostText');
            
            if ($postData == 1 || $postData == 3) 
            {
                $id       = $this->facebook_connect_data['facebookid'];
                $params = array(
                    'appId' => facebookAppid,
                    'secret' => facebookSecret,
                    'domain' => facebookDomain
                );
                $facebook = new Facebook($params);
                
                try {
                    $result = $facebook->api('/me/photos', 'POST', array(
                        'url' => IMGPATH . "/imageposts/$picName",
                        "access_token" => $this->facebook_connect_data['access_token'],
                        "message" => $status
                    ));
                    $data['status']  = 'success';
                }
                catch (FacebookApiException $e) {
                    $data['type'] =  $e->getType();
                    $data['message'] =  $e->getMessage();
                    $data['status']  = 'error';
                }
            } 
            else if ($postData == 2 || $postData == 3) 
            {
                 $twitteroauth = new TwitterOAuth(twitterAppid, twitterSecret, 
                    $this->twitter_connect_data['twitter_access_token'], 
                $this->twitter_connect_data['twitter_token_secret']);
                $image        = file_get_contents(BASE_URL . '/imageposts/' . $picName);
                $twitteroauth->post('statuses/update_with_media', array(
                    'status' => $status,
                    'media[]' => $image
                ));
            }
            if ($isPostOnPlateForm == 1 && $data['status']!='error') 
            {
                $postdata['Type']         = 3;
                $activity                 = date('Y-m-d H:i:s');
                $postdate                 = date('Y-m-d H:i:s');
                $dbtitle                  = $this->makeSeo($status, '', 'save');
                $postdata['Pic']          = $picName;
                $postdata['Text']      = $status;
                $postdata['User']         = (int) $this->_userid;
                $postdata['dburl']        = $dbtitle;
                $postdata['PostDate']     = $postdate;
                $postdata['LastActivity'] = $activity;
                $postdata['clientID'] = clientID;
                $this->Myhome_Model->insertmydb($postdata);
                $data['status']  = 'success';
                $data['message'] = 'Status successfully shared';
            }
            
        } else {
            $data['status']  = 'error';
            $data['message'] = 'failed';
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    
  
    public function commentsfacebookAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id       = $this->facebook_connect_data['facebookid'];
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            $params = array(
                    'appId' => facebookAppid,
                    'secret' => facebookSecret,
                    'domain' => facebookDomain
                );
            
            $facebook       = new Facebook($params);
            $getAccessToken = $this->facebook_connect_data['access_token'];
            $post_id        = $this->_request->getPost('facebook_post_id');
            $comment        = $this->_request->getPost('facebook_post_comment');
            
            try {
                
                $result = $facebook->api('/' . $post_id . '/comments', 'POST', array(
                    'message' => $comment,
                    "access_token" => $getAccessToken
                ));
                $data['status']  = 'success';
            }
            catch (FacebookApiException $e) {
                
                $data['type'] =  $e->getType();
                $data['message'] =  $e->getMessage();
                $data['status']  = 'error';
            }
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    
    
    
    public function likefacebookAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            $id = $this->facebook_connect_data['facebookid'];
            
            $config = Zend_Registry::get('config');
            
            $params = array(
                    'appId' => facebookAppid,
                    'secret' => facebookSecret,
                    'domain' => facebookDomain
                );
            
            $facebook       = new Facebook($params);
            $getAccessToken = $this->facebook_connect_data['access_token'];
            $post_id        = $this->_request->getPost('facebook_post_id');
            
            try {
                
                $result = $facebook->api('/' . $post_id . '/likes', 'POST', array(
                    "access_token" => $getAccessToken
                ));
                $data['status']  = 'success';
            }
            catch (FacebookApiException $e) {
                
                $data['type'] =  $e->getType();
                $data['message'] =  $e->getMessage();
                $data['status']  = 'error';
            }
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    
    public function unlikefacebookAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $id = $this->facebook_connect_data['facebookid'];
            
            $config = Zend_Registry::get('config');
            
            $params = array(
                    'appId' => facebookAppid,
                    'secret' => facebookSecret,
                    'domain' => facebookDomain
                );
            
            $facebook       = new Facebook($params);
            $getAccessToken = $this->facebook_connect_data['access_token'];
            $post_id        = $this->_request->getPost('facebook_post_id');
            
            try {
                
                $result = $facebook->api('/' . $post_id . '/likes', 'DELETE', array(
                    "access_token" => $getAccessToken
                ));
                $data['status']  = 'success';
            }
            catch (FacebookApiException $e) {
                
                $data['type'] =  $e->getType();
                $data['message'] =  $e->getMessage();
                $data['status']  = 'error';
            }
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    public function socialsearchAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);    
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST' && $this->islogin()==true) 
        {
            $profHTML = '';
            $type = $this->_request->getPost('type');
            $checkedList = $this->_request->getPost('checkedList');
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
                            if($callingfor=='inviteToGroup' && $checkedList==1)
                            {
                                $resultSocial = $this->User_Model->getSocialDetail($value->id);
                                if(!empty($resultSocial[0])){
                                    $lockclass = 'grouplockclass';
                                    $disabled = 'disabled';
                                    $tip = 'rel="dbTip" title="You cannot invite an existing platform user to a locked group."';
                                }
                            }
                            $profHTML .= "<div class='userFatchList boxFlowers ".$lockclass."'  ".$tip.">
                                <label class='labelCheckbox'>
                                    <input type='checkbox' ".$disabled." value='" . $value->id . "@".$firstName."@".$picUrl."' class='inviteuser-search' name='socialUser'>
                                    <div class='follower-box'>
                                        <img  class=img border height=30 align='left' src='" . $picUrl . "'>
                                        <div class='oneline'>" . $firstName . " " . $lastName . "</div>
                                    </div>
                                </label>
                            </div>";
                            $lockclass = '';
                            $defaultMessage = '';
                            $disabled = '';
                            $tip = '';
                            $data['userCount']  = true;
                        }
                    }
                    $profHTML .=$defaultMessage;
                    $data['status']  = 'success';
                    
                    break;
                case 'twitter':

                    $defaultMessage = '<div class="notfoundSocial" >No followers found.</div>';
                    $twitteroauth = new TwitterOAuth(twitterAppid, twitterSecret, 
                    $this->twitter_connect_data['twitter_access_token'], 
                    $this->twitter_connect_data['twitter_token_secret']);
                    $rateLimit  = $twitteroauth->get('application/rate_limit_status', array());
                    $rateLimit = json_decode(json_encode($rateLimit), true);
                    $data['rateLimit'] = $rateLimit;
                    if($rateLimit['resources']['friendships']['/friendships/lookup']['remaining']==0)
                    {
                        $data['twitter']  = 'error';
                        $data['time']  = time();
                        $data['reset'] = $rateLimit['resources']['friendships']['/friendships/lookup']['reset'];
                        $data['diff']  = $data['reset']-$data['time'];
                        $data['minute'] = ceil($data['diff']/60);
                        return $response->setBody(Zend_Json::encode($data));
                    }

                    $result  = $twitteroauth->get('friendships/lookup', array(
                        'screen_name' => $this->_request->getPost('keywords')
                    ));

                    if(!empty($result[0]) && !empty($result) && in_array('followed_by',$result[0]->connections) && $result[0]->connections!=null)
                    {
                        $resultTwitter  = $twitteroauth->get('users/lookup', array(
                            'screen_name' => $result[0]->screen_name
                        ));
                        if($callingfor=='inviteToGroup' && $checkedList==1)
                        {
                            $resultSocial = $this->User_Model->getSocialDetail($resultTwitter[0]->id);
                            if(!empty($resultSocial[0])){
                                $lockclass = 'grouplockclass';
                                $disabled = 'disabled';
                                $tip = 'rel="dbTip" title="You cannot invite an existing platform user to a locked group."';
                            }
                        }
                        $profHTML .= "<div class='userFatchList boxFlowers ".$lockclass."'  ".$tip.">
                        <label class='labelCheckbox'><input type='checkbox' ".$disabled." value='" . $resultTwitter[0]->screen_name . "@" . $resultTwitter[0]->id . "@".$resultTwitter[0]->profile_image_url_https."' class='inviteuser-search' name='socialUser'>
                        <div class='follower-box'>
                        <img  class='img border' height='30' align='left' src='" . $resultTwitter[0]->profile_image_url_https . "' >
                        <span class='oneline'>" . $resultTwitter[0]->name . "</span></div></label>
                        </div></div>";
                        $defaultMessage = '';
                        $lockclass = '';
                        $disabled = '';
                        $tip= '';
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
                                if($callingfor=='inviteToGroup' && $checkedList==1)
                                {
                                    $resultSocial = $this->User_Model->getSocialDetail($value["id"]);
                                    if(!empty($resultSocial[0])){
                                        $lockclass = 'grouplockclass';
                                        $tip = 'rel="dbTip" title="You cannot invite an existing platform user to a locked group."';
                                        $disabled = 'disabled';
                                    }
                                }
                                $profHTML .= "<div class='userFatchList boxFlowers ".$lockclass."'  ".$tip.">
                                                <label class='labelCheckbox'>
                                                    <input type='checkbox' ".$disabled." value='" . $value["id"]."@".$value['name']. "' class='inviteuser-search' name='socialUser'>
                                                    <div class='follower-box'>
                                                        <img class=img border height='30' weight='30' align='left' src='https://graph.facebook.com/" . $value["id"] . "/picture'>
                                                        <br><span class ='oneline'>" . $value['name'] . "</span>
                                                    </div>
                                                </label>
                                            </div>";
                                $defaultMessage = '';
                                $lockclass = '';
                                $disabled = '';
                                $tip ='';
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
                    $variable = $this->user_model->SearchMember($query,10);
                    foreach ($variable as $key => $value) 
                    {   
                        $profHTML .= "<div class='userFatchList boxFlowers' >
                        <label class='labelCheckbox'><input type='checkbox' value='".$value['UserID']."' class='inviteuser-search' name='socialUser'>
                        <div class='follower-box'><img  width='48' height='48' src='".BASE_URL."/timthumb.php?src=/userpics/".$value['ProfilePic']."&q=100&w=160&h=160' border='0'>
                             <br><span class ='oneline'>". $this->myclientdetails->customDecoding($value['Name']).' '.$this->myclientdetails->customDecoding($value['lname'])."</span>
                        </div></label></div>";
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
    public function secondsToTime($seconds)
    {
        // extract hours
        $hours = floor($seconds / (60 * 60));
     
        // extract minutes
        $divisor_for_minutes = $seconds % (60 * 60);
        $minutes = floor($divisor_for_minutes / 60);
     
        // extract the remaining seconds
        $divisor_for_seconds = $divisor_for_minutes % 60;
        $seconds = ceil($divisor_for_seconds);
     
        // return the final array
        $obj = array(
            "h" => (int) $hours,
            "m" => (int) $minutes,
            "s" => (int) $seconds,
        );
        return $obj;
    }
    public function socialinviteAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);    
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST' && $this->islogin()==true) 
        {
            $profHTML = '';
            $type = $this->_request->getPost('shareType');
            $callingfor = $this->_request->getPost('callingfor');
            $socialUser = explode(',',$this->_request->getPost('socialUser'));
            $uniqueIDSocial = (int)$this->_request->getPost('uniqueIDSocial');
            $checkedList = (int)$this->_request->getPost('checkedList');
            $expertCount = $this->myhome_obj->checkeexpertStatus($uniqueIDSocial);
            if($callingfor=='inviteToExpert')
            {
                $data['social'] = $this->expert_model->checkinviteexpert($uniqueIDSocial);
                $data['plateform'] = $this->expert_model->getInviteUser($uniqueIDSocial);

                $totalCount =  (int)(count($data['social']) + count($data['plateform']));
                if($totalCount==1 && $this->allowmultipleexperts==1 && $expertCount==0)
                {
                    $data['status']  = 'error';
                    $data['allowmultipleexperts'] = $this->allowmultipleexperts;
                    $data['content'] = "Sorry, you can't invite more user(s) on this post.";
                    return $response->setBody(Zend_Json::encode($data));

                }else if($this->allowmultipleexperts==3 && $expertCount<=4)
                {
                    $userListCount = count($socialUser);
                    $totalP = $userListCount+$totalCount+$expertCount;
                    if($totalP>4)
                    {
                        $data['status']  = 'error';
                        $data['allowmultipleexperts'] = $this->allowmultipleexperts;
                        $data['content'] = "Sorry, you can only invite ".(4-$totalCount)." more user(s) on this post.";
                        return $response->setBody(Zend_Json::encode($data));
                    }
                    else if($totalCount==4 && $expertCount!=0)
                    {
                        $data['status']  = 'error';
                        $data['allowmultipleexperts'] = $this->allowmultipleexperts;
                        $data['content'] = "Sorry, you can only invite ".(4-$totalCount)." more user(s) on this post.";
                        return $response->setBody(Zend_Json::encode($data));
                    }
                }
            }

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
                    if($callingfor=='inviteToSite')
                      $respo = $this->linkedinInvitationToSite($linkedin,$socialUser);
                    else if($callingfor=='inviteToExpert')
                      $respo = $this->linkedinExpertInvitation($linkedin,$socialUser,$uniqueIDSocial);
                    else if($callingfor=='inviteToGroup')
                      $respo = $this->linkedinGroupInvitation($linkedin,$socialUser,$uniqueIDSocial,$checkedList);
                    break;
                case 'twitter':
                    $defaultMessage = '<div class="notfoundSocial" >No followers found.</div>';
                    $twitteroauth       = new TwitterOAuth(twitterAppid, twitterSecret, 
                    $this->twitter_connect_data['twitter_access_token'], 
                    $this->twitter_connect_data['twitter_token_secret']);
                    if($callingfor=='inviteToSite')
                      $respo = $this->twitterInvitationToSite($twitteroauth,$socialUser);
                    else if($callingfor=='inviteToExpert')
                      $respo = $this->twitterExpertInvitation($twitteroauth,$socialUser,$uniqueIDSocial);
                    else if($callingfor=='inviteToGroup')
                      $respo = $this->twitterGroupInvitation($twitteroauth,$socialUser,$uniqueIDSocial,$checkedList);

                    break;
                default:

                    $params = array('appId' => facebookAppid,'secret' => facebookSecret,
                    'domain' => facebookDomain);
                    $facebook = new Facebook($params);
                    
                    if($callingfor=='inviteToSite')
                      $respo = $this->facebookInvitationToSite($facebook,$socialUser);
                    else if($callingfor=='inviteToExpert')
                      $respo = $this->facebookExpertInvitation($facebook,$socialUser,$uniqueIDSocial);
                    else if($callingfor=='inviteToGroup')
                      $respo = $this->facebookGroupInvitation($facebook,$socialUser,$uniqueIDSocial,$checkedList);
                    break;
            }
           if($respo==false)
                $data['failed']  = true;
           else
            $data['status']  = 'success';
            $data['type']  = $type;
        }
        
        return $response->setBody(Zend_Json::encode($data));
    }

    public function facebookGroupInvitation($facebook,$socialUser,$groupid,$checkedList)
    {
        $grupdetais = $this->groupModel->groupuserdetail($groupid);
        $group_data = $grupdetais[0];
        if(!empty($group_data))
        {
            try 
            {
                 if(trim($group_data['GroupDesc']))
                    $description = trim($group_data['GroupDesc']);
                else
                    $description = $this->getSeoConfiguration->SiteDescription;                           
               
                
              
                foreach ($socialUser as $value) 
                {
                   $socialArray = explode('@', $value);
                   $privacy = array(
                    'value'   => 'CUSTOM',
                    'friends' => 'SOME_FRIENDS',
                    'allow'   => $socialArray[0]
                    );
                    $message="Hi " . $socialArray[1] . ", ".$this->getSocialShareMsg['group_share'];
                    $message=str_replace("[NAME]", $socialArray[1], $message);
                    $message=str_replace("[GROUP_NAME]", trim($grupdetais[0]['GroupName']),$message);
                    $message=str_replace("[PLATFORM_NAME]", SITE_NAME,$message);

                    
                    $token = $this->generateHash(time());
                    if($this->SocialLogo!='')
                        $image = BASE_URL.'/img/'.$this->SocialLogo;
                    else
                        $image = BASE_URL.'/img/logo.png';

                    if($checkedList==0)
                        $url = $this->shortUrl(BASE_URL . '/group/groupdetails/group/' . $groupid . '/type/facebook/token/' .$token . '/oathtoken/' . $socialArray[0].'/status/open');
                    else
                        $url = $this->shortUrl(BASE_URL . '/group/groupdetails/group/' . $groupid . '/type/facebook/locktoken/' .$token . '/token/'.time().'/oathtoken/' . $socialArray[0].'/status/lock');
                       
                    $result = $facebook->api('/'.$this->facebook_connect_data['facebookid'].'/feed', 'POST', array(
                      'from' => array(
                      'name' => $this->facebook_connect_data['facebookname'],
                      'id' => $this->facebook_connect_data['facebookid']
                    ),
                    'link'    => $url,
                    'picture' => $image,
                    'description' => $description,
                    'privacy' => json_encode($privacy),
                    'message' => $message,
                    'access_token' => $this->facebook_connect_data['access_token'],
                    'tags'=> $socialArray[0],
                    'place'=>'155021662189'
                    ));

                    $data_insert['socialid'] = $socialArray[0];
                    $data_insert['groupid']  = $groupid;
                    $data_insert['type']     = 'facebook';
                    $data_insert['date']     = date('Y-m-d H:i:s');
                    $data_insert['token']    = $token;
                    $data_insert['clientID']    = clientID;
                    if($checkedList==0)
                        $this->groupModel->insertgroupsocial($data_insert);
                    else
                        $this->groupModel->insertgrouplock($data_insert); 
                }
                return true; 
            } catch (Exception $e) {
                return false;
            }
        }
    }

    public function linkedinGroupInvitation($linkedin,$socialUser,$groupid,$checkedList)
    {
        $grupdetais = $this->groupModel->groupuserdetail($groupid);
        $group_data = $grupdetais[0];
        if(!empty($group_data))
        {
            foreach ($socialUser as $value) 
            {
                $token = $this->generateHash(time());
                $socialArray = explode('@', $value);
                if($checkedList==0)
                    $url = $this->shortUrl(BASE_URL . '/group/groupdetails/group/' . $groupid . '/type/linkedin/token/' .$token . '/oathtoken/' . $socialArray[0].'/status/open');
                else
                    $url = $this->shortUrl(BASE_URL . '/group/groupdetails/group/' . $groupid . '/type/linkedin/token/'.time().'/locktoken/' .$token . '/oathtoken/' . $socialArray[0].'/status/lock');
                $message ="Hi " . $socialArray[1] . ", ".$this->getSocialShareMsg['group_share'].' '.$url;
                $message=str_replace("[NAME]", $socialArray[1], $message);
                $message=str_replace("[GROUP_NAME]", trim($grupdetais[0]['GroupName']),$message);
                $message=str_replace("[PLATFORM_NAME]", SITE_NAME,$message);
                $subject = "Invitation to join group";
                $linkedin->sendMessageById($socialArray[0], FALSE, $subject, $message);
                $data_insert['socialid'] = $socialArray[0];
                $data_insert['groupid']  = $groupid;
                $data_insert['type']     = 'linkedin';
                $data_insert['date']     = date('Y-m-d H:i:s');
                $data_insert['token']    = $token;
                $data_insert['clientID']    = clientID;
                if($checkedList==0)
                    $this->groupModel->insertgroupsocial($data_insert);
                else
                    $this->groupModel->insertgrouplock($data_insert); 
            }
            return true; 
        }
    }

    public function twitterGroupInvitation($twitteroauth,$socialUser,$groupid,$checkedList)
    {        
        $grupdetais = $this->groupModel->groupuserdetail($groupid);
        $group_data = $grupdetais[0];
        if(!empty($group_data))
        {
            foreach ($socialUser as $value) 
            {
                $token = $this->generateHash(time());
                $socialArray = explode('@', $value);
                if($checkedList==0)
                    $url = $this->shortUrl(BASE_URL . '/group/groupdetails/group/' . $groupid . '/type/twitter/token/' .$token . '/oathtoken/' . $socialArray[1].'/status/open');
                else
                    $url = $this->shortUrl(BASE_URL . '/group/groupdetails/group/' . $groupid . '/type/twitter/token/'.time().'/locktoken/' .$token . '/oathtoken/' . $socialArray[1].'/status/lock');

                $message="Hi @" . $socialArray[0] . ", ".$this->getSocialShareMsg['group_share'].' '.$url;
                $message=str_replace("[NAME]", $socialArray[0], $message);
                $message=str_replace("[GROUP_NAME]", trim($group_data['GroupName']),$message);
                $message=str_replace("[SITE_NAME]", SITE_NAME,$message);
                $twitteroauth->post('statuses/update', array(
                            'status' => $message
                        ));
                $data_insert['socialid'] = $socialArray[1];
                $data_insert['groupid']  = $groupid;
                $data_insert['type']     = 'twitter';
                $data_insert['date']     = date('Y-m-d H:i:s');
                $data_insert['token']    = $token;
                $data_insert['clientID']    = clientID;
                $this->groupModel->insertgroupsocial($data_insert);
                if($checkedList==0)
                    $this->groupModel->insertgroupsocial($data_insert);
                else
                    $this->groupModel->insertgrouplock($data_insert);  
            }
            return true; 
        }
    }

    public function facebookExpertInvitation($facebook,$socialUser,$dbeeid)
    {
        $dbee_data = $this->myhome_obj->getDbeeDetails($dbeeid);
        $token = $this->generateHash(time());
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
                'description' => $dbee_data['Text'],
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
                $this->myhome_obj->insertininviteexport($data);
                return true; 
            } catch (Exception $e) {
                return false;
            }
        }
    }

    public function twitterExpertInvitation($twitteroauth,$socialUser,$dbeeid)
    {
        $dbee_data = $this->myhome_obj->getDbeeDetails($dbeeid);
        $token = $this->generateHash(time());
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
            $this->myhome_obj->insertininviteexport($data);
            return true; 
        }
    }

    public function linkedinExpertInvitation($linkedin,$socialUser,$dbeeid)
    {
        $dbee_data = $this->myhome_obj->getDbeeDetails($dbeeid);
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
            $this->myhome_obj->insertininviteexport($data);
            return true; 
        }
    }
    public function facebookInvitationToSite($facebook,$socialUser)
    {
        try 
        {
            foreach ($socialUser as $value) 
            {
               $facebookUser = explode('@', $value);
               $privacy = array(
                'value'   => 'CUSTOM',
                'friends' => 'SOME_FRIENDS',
                'allow'   => $facebookUser[0]
                );

                if($this->SocialLogo!='')
                    $image = BASE_URL.'/img/'.$this->SocialLogo;
                else
                    $image = BASE_URL.'/img/logo.png';

                $result = $facebook->api('/'.$this->facebook_connect_data['facebookid'].'/feed', 'POST', array(
                  'from' => array(
                  'name' => $this->facebook_connect_data['facebookname'],
                  'id' => $this->facebook_connect_data['facebookid']
                ),
                'link'    => BASE_URL,
                'picture' => $image,
                'description' => $this->getSeoConfiguration->SiteDescription,
                'privacy' => json_encode($privacy),
                'message' => "Hi " . $facebookUser[1] . ", ".$this->getSocialShareMsg['plateform_share'],
                'access_token' => $this->facebook_connect_data['access_token'],
                'tags'=> $facebookUser[0],
                'place'=>'155021662189'
                ));
            }
            return true; 
        } catch (Exception $e) {
            return false;
        }
    }

    public function twitterInvitationToSite($twitteroauth,$socialUser)
    {
        $twitterArray = explode('@', $socialUser[0]);
        $url = $this->shortUrl(BASE_URL);
        $twitteroauth->post('statuses/update', array('status' => '@'.$twitterArray[0]." ".$this->getSocialShareMsg['plateform_share']." ".$url));
        return true;
    }

    public function linkedinInvitationToSite($linkedin,$socialUser)
    {
        $count = count($socialUser);
        for($i = 0; $i < $count; $i++) 
        {
            $resultData = explode('@', $socialUser[$i]);
            $body = "Hey ".$resultData[1].", ".$this->getSocialShareMsg['plateform_share'].' '.$this->shortUrl(BASE_URL);
            $subject = "Invitation to join this cool website";
            $linkedin->sendMessageById($resultData[0], FALSE, $subject, $body);
            unset($resultData);
        }
        return true;
    }

  
  
     public function linkedinplateformfeedAction()
    {

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);    
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            $result = $this->myclientdetails->getRowMasterfromtable('tblConfiguration',array('*'),array());

            if($result['linkedin_page_id']!='')
            {
                $config['base_url']        = BASE_URL;
                $config['callback_url']    = BASE_URL . '/social/callback';
                $config['linkedin_access'] = linkedinAppid;
                $config['linkedin_secret'] = linkedinSecret;
                
                # First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
                $linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], '');
                $linkedin->debug = true;
                
                $linkedin_connect_data = Zend_Json::decode($this->session_data['linkedin_connect_data']);
                
                $linkedin->request_token = unserialize($linkedin_connect_data['requestToken']);
                $linkedin->oauth_verifier            = $linkedin_connect_data['oauth_verifier'];
                $linkedin->access_token              = unserialize($linkedin_connect_data['oauth_access_token']);

                $data['getnetwork'] = $linkedin->getgrouppost($result['linkedin_page_id']."/posts:(id,creation-timestamp,title,summary,creator:(first-name,last-name,picture-url;secure=true,headline),likes,comments,attachment:(image-url,content-domain,content-url,title,summary),relation-to-viewer)?category=discussion&order=recency&format=json&secure-urls=true");
            }else{
                $data['status'] = 'error';
                $data['message'] = 'Some thing went wrong here please try again';
            }
       }
        return $response->setBody(Zend_Json::encode($data));          
    }
    public function linkedinuserprofileAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);    
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            $config['base_url']        = BASE_URL;
            $config['callback_url']    = BASE_URL . '/social/callback';
            $config['linkedin_access'] = linkedinAppid;
            $config['linkedin_secret'] = linkedinSecret;

            $dbeeUsername = $this->_request->getPost('dbeeUsername');
            $result_array = $this->User_Model->getUserDataByUsername($dbeeUsername);
            $linkedin_connect_data = Zend_Json::decode($result_array['0']['linkedin_connect_data']);
                
            # First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
            $linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], '');
            $linkedin->debug = true;
 
            
            $linkedin->request_token             = unserialize($linkedin_connect_data['requestToken']);
            $linkedin->oauth_verifier            = $linkedin_connect_data['oauth_verifier'];
            $linkedin->access_token              = unserialize($linkedin_connect_data['oauth_access_token']);
           
            $data['getnetwork'] = $linkedin->getProfilefeed($linkedin_connect_data['id'].'/network/updates?format=json&secure-urls=true&count=25');
       }
        return $response->setBody(Zend_Json::encode($data));           
    }

    public function linkedinmyprofileAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);    
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest()) 
        { 
       
            $config['base_url']        = BASE_URL;
            $config['callback_url']    = BASE_URL . '/social/callback';
            $config['linkedin_access'] = linkedinAppid;
            $config['linkedin_secret'] = linkedinSecret;
            
            # First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
            $linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], '');
            $linkedin->debug = true;
            
            $linkedin_connect_data = Zend_Json::decode($this->session_data['linkedin_connect_data']);
            //print_r($linkedin_connect_data);
            $linkedin->request_token             = unserialize($linkedin_connect_data['requestToken']);
            $linkedin->oauth_verifier            = $linkedin_connect_data['oauth_verifier'];
            $linkedin->access_token              = unserialize($linkedin_connect_data['oauth_access_token']);
           
            $data['getnetwork'] = $linkedin->getProfilefeed('~/network/updates?format=json&secure-urls=true&count=25');
       }
        return $response->setBody(Zend_Json::encode($data));           
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
            $config['callback_url']    = BASE_URL . '/social/callback';
            $config['linkedin_access'] = linkedinAppid;
            $config['linkedin_secret'] = linkedinSecret;
            $page = $this->_request->getPost('page')*10;
            # First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
            $linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], '');
            $linkedin->debug = true;
            
            $linkedin_connect_data = Zend_Json::decode($this->session_data['linkedin_connect_data']);
            
            $linkedin->request_token             = unserialize($linkedin_connect_data['requestToken']);
            $linkedin->oauth_verifier            = $linkedin_connect_data['oauth_verifier'];
            $linkedin->access_token              = unserialize($linkedin_connect_data['oauth_access_token']);
            if($page==0)
                $data['getnetwork'] = $linkedin->group('~/group-memberships:(group:(id,name,short-description,description,site-group-url,small-logo-url,large-logo-url),membership-state,show-group-logo-in-profile,allow-messages-from-members,email-digest-frequency,email-announcements-from-managers,email-for-every-new-post)?format=json&secure-urls=true');
            else
                $data['getnetwork'] = $linkedin->group("~/group-memberships:(group:(id,name,short-description,description,site-group-url,small-logo-url,large-logo-url),membership-state,show-group-logo-in-profile,allow-messages-from-members,email-digest-frequency,email-announcements-from-managers,email-for-every-new-post)?format=json&start=$page&count=10&secure-urls=true");
                
       }
        return $response->setBody(Zend_Json::encode($data));           
    }

    public function linkedingroupdetailsAction()
    {
       $this->redirectToHome();
       if($this->Social_Content_Block=='block')
            $this->_redirect('/');
       $this->view->groupid = $this->_request->getParam('groupid');

    }


     public function linkedingroupajaxdetailsAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);    
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        { 
            $groupid = $this->_request->getPost('groupid');
            $config['base_url']        = BASE_URL;
            $config['callback_url']    = BASE_URL . '/social/callback';
            $config['linkedin_access'] = linkedinAppid;
            $config['linkedin_secret'] = linkedinSecret;

            # First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
            $linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], '');
            $linkedin->debug = true;

            $linkedin_connect_data = Zend_Json::decode($this->session_data['linkedin_connect_data']);

            $linkedin->request_token             = unserialize($linkedin_connect_data['requestToken']);
            $linkedin->oauth_verifier            = $linkedin_connect_data['oauth_verifier'];
            $linkedin->access_token              = unserialize($linkedin_connect_data['oauth_access_token']);
            $resultJson = $linkedin->getProfile("~:(id,email-address,first-name,last-name,picture-url;secure=true,date-of-birth)?format=json&secure-urls=true");
            $jsonData = json_decode($resultJson);
                        

            $data['firstName'] = $jsonData->firstName;
            $data['lastName'] = $jsonData->lastName;
            

            if(isset($jsonData->pictureUrl))
              $data['pictureUrl'] = $jsonData->pictureUrl;
            else
              $data['pictureUrl']= URL .'/timthumb.php?src=/userpics/default-avatar.jpg&q=100&w=40&h=40';

            $data['getnetwork'] = $linkedin->groupdetails($groupid.':(id,name,short-description,description,relation-to-viewer:(membership-state,available-actions),posts,counts-by-category,is-open-to-non-members,category,website-url,locale,location:(country,postal-code),allow-member-invites,site-group-url,small-logo-url,large-logo-url)?format=json&secure-urls=true');
        }
        return $response->setBody(Zend_Json::encode($data));
    }

  

     public function linkedingroupsuggestedAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);    
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest()) 
        { 
       
            $config['base_url']        = BASE_URL;
            $config['callback_url']    = BASE_URL . '/social/callback';
            $config['linkedin_access'] = linkedinAppid;
            $config['linkedin_secret'] = linkedinSecret;
            
            # First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
            $linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], '');
            $linkedin->debug = true;
            
            $linkedin_connect_data = Zend_Json::decode($this->session_data['linkedin_connect_data']);
            
            $linkedin->request_token             = unserialize($linkedin_connect_data['requestToken']);
            $linkedin->oauth_verifier            = $linkedin_connect_data['oauth_verifier'];
            $linkedin->access_token              = unserialize($linkedin_connect_data['oauth_access_token']);
           
            $data['getnetwork'] = $linkedin->groupsuggesred('~/suggestions/groups?format=json&secure-urls=true');
       }
        return $response->setBody(Zend_Json::encode($data));           
    }

    public function linkedingetgrouppostAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);    
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest()) 
        { 
       
            $config['base_url']        = BASE_URL;
            $config['callback_url']    = BASE_URL . '/social/callback';
            $config['linkedin_access'] = linkedinAppid;
            $config['linkedin_secret'] = linkedinSecret;
            
            # First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
            $linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], '');
            $linkedin->debug = true;
            
            $linkedin_connect_data = Zend_Json::decode($this->session_data['linkedin_connect_data']);
            
            $linkedin->request_token             = unserialize($linkedin_connect_data['requestToken']);
            $linkedin->oauth_verifier            = $linkedin_connect_data['oauth_verifier'];
            $linkedin->access_token              = unserialize($linkedin_connect_data['oauth_access_token']);
            $groupid = $this->_request->getPost('groupid');
            $page = $this->_request->getPost('page');
            //$groupid =120323;

            if($page==0)
            $data['getnetwork'] = $linkedin->getgrouppost($groupid.'/posts:(id,creation-timestamp,title,summary,creator:(first-name,last-name,picture-url;secure=true,headline),likes,comments,attachment:(image-url,content-domain,content-url,title,summary),relation-to-viewer)?category=discussion&order=recency&format=json&secure-urls=true');
            else
                $data['getnetwork'] = $linkedin->getgrouppost($groupid."/posts:(id,creation-timestamp,title,summary,creator:(first-name,last-name,picture-url;secure=true,headline),likes,comments,attachment:(image-url,content-domain,content-url,title,summary),relation-to-viewer)?category=discussion&order=recency&start=$page&count=10&format=json&secure-urls=true");
            $data['page'] = $page+10;
       }
        return $response->setBody(Zend_Json::encode($data));           
    }


     public function linkedinjoingroupAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);    
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest()) 
        { 
       
            $config['base_url']        = BASE_URL;
            $config['callback_url']    = BASE_URL . '/social/callback';
            $config['linkedin_access'] = linkedinAppid;
            $config['linkedin_secret'] = linkedinSecret;
            
            # First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
            $linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], '');
            $linkedin->debug = true;
            
            $linkedin_connect_data = Zend_Json::decode($this->session_data['linkedin_connect_data']);
            
            $linkedin->request_token             = unserialize($linkedin_connect_data['requestToken']);
            $linkedin->oauth_verifier            = $linkedin_connect_data['oauth_verifier'];
            $linkedin->access_token              = unserialize($linkedin_connect_data['oauth_access_token']);
            $groupid = $this->_request->getPost('groupid');
            //$groupid =120323;
            $data['getnetwork'] = $linkedin->joingroup('~/group-memberships?format=json&secure-urls=true',$groupid);
       }
        return $response->setBody(Zend_Json::encode($data));           
    }

     public function linkedinleavegroupAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);    
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest()) 
        { 
            $config['base_url']        = BASE_URL;
            $config['callback_url']    = BASE_URL . '/social/callback';
            $config['linkedin_access'] = linkedinAppid;
            $config['linkedin_secret'] = linkedinSecret;
            
            # First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
            $linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], '');
            $linkedin->debug = true;
            
            $linkedin_connect_data = Zend_Json::decode($this->session_data['linkedin_connect_data']);
            
            $linkedin->request_token             = unserialize($linkedin_connect_data['requestToken']);
            $linkedin->oauth_verifier            = $linkedin_connect_data['oauth_verifier'];
            $linkedin->access_token              = unserialize($linkedin_connect_data['oauth_access_token']);
            $groupid = $this->_request->getPost('groupid');
            //$groupid = 2272881;
            $data['getnetwork'] = $linkedin->leaveGroup('~/group-memberships/'.$groupid.'?format=json');
       }
       return $response->setBody(Zend_Json::encode($data));           
    }
    public function linkedingrouppostAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);    
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            $config['base_url']        = BASE_URL;
            $config['callback_url']    = BASE_URL . '/social/callback';
            $config['linkedin_access'] = linkedinAppid;
            $config['linkedin_secret'] = linkedinSecret;
            
            # First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
            $linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], '');
            $linkedin->debug = true;
            
            $linkedin_connect_data = Zend_Json::decode($this->session_data['linkedin_connect_data']);
            
            $linkedin->request_token             = unserialize($linkedin_connect_data['requestToken']);
            $linkedin->oauth_verifier            = $linkedin_connect_data['oauth_verifier'];
            $linkedin->access_token              = unserialize($linkedin_connect_data['oauth_access_token']);
            $title = $this->_request->getPost('title');
            $summary = $this->_request->getPost('description');
            $groupid = $this->_request->getPost('groupid');

            $data['getnetwork'] = $linkedin->groupPost($groupid.'/posts?format=json',$title,$summary);
       }
        return $response->setBody(Zend_Json::encode($data));           
    }

   
    public function linkedinpostdislikeAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);    
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest()) 
        { 
            $postID = $this->_request->getPost('id');
            $type = $this->_request->getPost('type');
            $config['base_url']        = BASE_URL;
            $config['callback_url']    = BASE_URL . '/social/callback';
            $config['linkedin_access'] = linkedinAppid;
            $config['linkedin_secret'] = linkedinSecret;
            
            # First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
            $linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], '');
            $linkedin->debug = true;
            
            $linkedin_connect_data = Zend_Json::decode($this->session_data['linkedin_connect_data']);
            
            $linkedin->request_token             = unserialize($linkedin_connect_data['requestToken']);
            $linkedin->oauth_verifier            = $linkedin_connect_data['oauth_verifier'];
            $linkedin->access_token              = unserialize($linkedin_connect_data['oauth_access_token']);
            if($type=='Like')
                $data['getnetwork'] = $linkedin->postlike($postID.'/relation-to-viewer/is-liked?format=json');
            else
            $data['getnetwork'] = $linkedin->postdislike($postID.'/relation-to-viewer/is-liked?format=json');
       }
        return $response->setBody(Zend_Json::encode($data));           
    }

    

    public function linkedinpostcommentAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);    
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest()) 
        { 
            $postID = $this->_request->getPost('id');
            $config['base_url']        = BASE_URL;
            $config['callback_url']    = BASE_URL . '/social/callback';
            $config['linkedin_access'] = linkedinAppid;
            $config['linkedin_secret'] = linkedinSecret;
            
            # First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
            $linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], '');
            $linkedin->debug = true;
            
            $linkedin_connect_data = Zend_Json::decode($this->session_data['linkedin_connect_data']);
            
            $linkedin->request_token             = unserialize($linkedin_connect_data['requestToken']);
            $linkedin->oauth_verifier            = $linkedin_connect_data['oauth_verifier'];
            $linkedin->access_token              = unserialize($linkedin_connect_data['oauth_access_token']);
            $comments = $this->_request->getPost('commentText');

            $data['getnetwork'] = $linkedin->postComment($postID.'/comments?format=json',$comments);
       }
        return $response->setBody(Zend_Json::encode($data));           
    }




    public function sendmessagelinkedinAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $session_name_space = new Zend_Session_Namespace('User_Session');
        $response->setHeader('Content-type', 'application/json', true);
    
        $config['base_url']        = BASE_URL;
        $config['callback_url']    = BASE_URL . '/social/callback';
        $config['linkedin_access'] = linkedinAppid;
        $config['linkedin_secret'] = linkedinSecret;
        # First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
        $linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], '');
        //$linkedin->debug = true;
        
        $linkedin_connect_data = Zend_Json::decode($this->session_data['linkedin_connect_data']);
        $linkedin->request_token  = unserialize($linkedin_connect_data['requestToken']);
        $linkedin->oauth_verifier = $linkedin_connect_data['oauth_verifier'];
        $linkedin->access_token = unserialize($linkedin_connect_data['oauth_access_token']);
        
        $stringuserInfo = $this->_request->getPost('userid');
        $UserConnect = explode(',', $stringuserInfo);
        for($i = 0; $i < count($UserConnect); $i++) 
        {

            $ResultData = explode('@', $UserConnect[$i]);
            $body = "Hey ".$ResultData[1].", check this cool website I came across for debating and sharing your views on any topic.".$this->shortUrl(BASE_URL);
            $subject = "Invitation to join this cool website";
            $linkedin->sendMessageById($ResultData[0], FALSE, $subject, $body);
            unset($ResultData);
        }
        $data['status'] = 'success';
        $data['message'] = 'Post shared';       
        return $response->setBody(Zend_Json::encode($data));

    }

    public function shareontwitterwallAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            $dbeeid = (int) $this->_request->getPost('dbeeid');
            $socialShareModel = new Application_Model_Socialshare();
            
            $config = array(
                'consumerKey' => $this->config->twitter->api->consumer,
                'consumerSecret' => $this->config->twitter->api->secret
            );
            
            $dbee_result = $this->myhome_obj->checkdbeeexist($dbeeid);
            $url         = BASE_URL . '/dbee/' . $dbee_result['0']['dburl'];
            $url = $this->makeshortlink($dbeeid);
            $title = "";
            $title = $dbee_result['0']['Text'];
            
            if ($dbee_result['0']['Type'] == '5')
                $title = $dbee_result['0']['polltext'];
            
            $twitteroauth = new TwitterOAuth(twitterAppid, twitterSecret, $this->twitter_connect_data['twitter_access_token'], $this->twitter_connect_data['twitter_token_secret']);
            
            $msg = str_replace('[PLATFORM_NAME]', SITE_NAME, $this->getSocialShareMsg['post_share']);
            
            /*
            if(strlen($title)>100)
                $title = substr($title,0,100).'..';
            */
            
            $title = $msg;

            $twitteroauth->post('statuses/update', array(
                'status' => $title . ' ' . $url
            ));
            
            $count_status = $socialShareModel->checkSocialShare($dbeeid, "twitter", $this->_userid);
            
            if ($count_status == 0) {
                
                $dataArray = array(
                    "sharetype" => "twitter",
                    "count" => 1,
                    "timestamp" => date('Y-m-d H:i:s'),
                    "dbeeid" => $dbeeid,
                    "user_id" => $this->_userid,
                    'clientID' => clientID
                );
                
                $socialShareModel->insertSocialShare($dataArray);
                
            } else 
            {
                $dataArray = array(
                    "sharetype" => "twitter",
                    "count" => $count_status['count'] + 1,
                    "timestamp" => date('Y-m-d H:i:s'),
                    "dbeeid" => $dbeeid,
                    "user_id" => $this->_userid
                );
                
                $socialShareModel->updateSocialShare($dataArray, $dbeeid, "twitter", $this->_userid);
            }
            
            
            $data['status']  = 'success';
            $data['message'] = 'Post shared';
            
        } else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }

    public function twittersphereshareAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $dbeeid = (int) $this->_request->getPost('dbeeid');

            $username = $this->_request->getPost('username');
            $sharecontent = $this->_request->getPost('sharecontent');
            
            $pos1 = strpos($username,',');

            if ($pos1 === false) {

                $username=$username;
                $pos = strpos($username,'@');
                if ($pos === false) {                    
                    $username='@'.trim($username);
                }
                else
                {
                     $username=trim($username);
                }
            }
            else
            {
                $slice=explode(',', trim($username));
                $username="";
                if(count($slice) > 0)
                {
                   foreach ($slice as $key => $value) {
                                         
                        $pos = strpos($value,'@');
                        if ($pos === false) {                    
                            $username.=' @'.trim($value);
                        }
                        else
                        {
                             $username.=trim($value);
                        }
                    }
                }
            }


               

            $socialShareModel = new Application_Model_Socialshare();
            
            $config = array(
                'consumerKey' => $this->config->twitter->api->consumer,
                'consumerSecret' => $this->config->twitter->api->secret
            );
            
            $dbee_result = $this->myhome_obj->checkdbeeexist($dbeeid);
            $url         = BASE_URL . '/dbee/' . $dbee_result['0']['dburl'].'?from=twitter';
            $url = $this->shortUrl($url);
            $title = "";
            

            $title = $dbee_result['0']['Text'];
            
            if ($dbee_result['0']['Type'] == '5')

                $title = $dbee_result['0']['polltext'];
            
            $twitteroauth = new TwitterOAuth(twitterAppid, twitterSecret, $this->twitter_connect_data['twitter_access_token'], $this->twitter_connect_data['twitter_token_secret']);
            
            if(strlen($title)>100)
                $title = substr(strip_tags($title),0,100).'..';
                $title = str_replace('&nbsp;', ' ', $title);
            
            $twitteroauth->post('statuses/update', array(
                'status' => $username.' '.$title . ' ' . $url
            ));
             
            /* $twitteroauth->post('statuses/update', array(
                'status' => $sharecontent
            ));*/
            
            $count_status = $socialShareModel->checkSocialShare($dbeeid, "twitter", $this->_userid);
            
            if ($count_status == 0) {
                
                $dataArray = array(
                    "sharetype" => "twitter",
                    "count" => 1,
                    "timestamp" => date('Y-m-d H:i:s'),
                    "dbeeid" => $dbeeid,
                    "user_id" => $this->_userid,
                    'clientID' => clientID
                );
                
                $socialShareModel->insertSocialShare($dataArray);
                
            } else {
                $dataArray = array(
                    "sharetype" => "twitter",
                    "count" => $count_status['count'] + 1,
                    "timestamp" => date('Y-m-d H:i:s'),
                    "dbeeid" => $dbeeid,
                    "user_id" => $this->_userid
                );
                
                $socialShareModel->updateSocialShare($dataArray, $dbeeid, "linkedin", $this->_userid);
            }
            
            
            $data['status']  = 'success';
            $data['message'] = 'Post shared';
            
        } else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    
    public function shareonfacebookwallAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $dbeeid = (int) $this->_request->getPost('dbeeid');
            $params = array(
                'appId' => facebookAppid,
                'secret' => facebookSecret,
                'domain' => facebookDomain
            );
            $socialShareModel = new Application_Model_Socialshare();
            $facebook = new Facebook($params);
            
            if ($this->facebook_connect_data['access_token'])
                $getAccessToken = $this->facebook_connect_data['access_token'];
            else
                $getAccessToken = $facebook->getAccessToken();
            
            $dbee_result = $this->myhome_obj->checkdbeeexist($dbeeid);
            $url         = BASE_URL . '/dbee/' . $dbee_result['0']['dburl'];
            
            
            $image = '';
            
            if ($dbee_result['0']['Type'] == '1')
                $title = $dbee_result['0']['Text'];
            else if ($dbee_result['0']['Type'] == '2') {
                $title = $dbee_result['0']['LinkTitle'];
                $checkImage = new Application_Model_Commonfunctionality();
                $pic1 = $checkImage->checkImgExist($dbee_result['0']['LinkPic'],'imageposts','default-avatar.jpg');
                $image = BASE_URL . '/timthumb.php?src=/imageposts/' .$pic1. '&q=100&w=200&h=150';
            } else if ($dbee_result['0']['Type'] == '3') {
                $title = $dbee_result['0']['PicDesc'];
                $checkImage = new Application_Model_Commonfunctionality();
                $pic2 = $checkImage->checkImgExist($dbee_result['0']['Pic'],'imageposts','default-avatar.jpg');
                $image = BASE_URL . '/timthumb.php?src=/imageposts/'.$pic2.'&q=100&w=200&h=150';
            } else if ($dbee_result['0']['Type'] == '4' || $dbee_result['0']['Type'] == '6') {
                $title = $dbee_result['0']['VidDesc'];
                $image = 'https://i.ytimg.com/vi/' . $dbee_result['0']['VidID'] . '/0.jpg';
            } else if ($dbee_result['0']['Type'] == '5')
                $title = $dbee_result['0']['polltext'];
            
            $comment = '';

            $msg = str_replace('[PLATFORM_NAME]', SITE_NAME, $this->getSocialShareMsg['post_share']);

            if($this->SocialLogo!='')
                $image = URL.'/img/'.$this->SocialLogo;
          
            try {
                
                $result = $facebook->api('/me/feed', 'POST', array(
                    'name' => $this->string_limit_words($title, 15),
                    'link' => $url,
                    'image' => $image,
                    'message' => $msg,
                    "access_token" => $getAccessToken
                ));
                
                $count_status = $socialShareModel->checkSocialShare($dbeeid, "facebook", $this->_userid);
                
                if ($count_status == 0) {
                    
                    $dataArray = array(
                        "sharetype" => "facebook",
                        "count" => 1,
                        "timestamp" => date('Y-m-d H:i:s'),
                        "dbeeid" => $dbeeid,
                        "user_id" => $this->_userid,
                        'clientID' => clientID
                    );
                    
                    $socialShareModel->insertSocialShare($dataArray);
                    
                } else {
                    $dataArray = array(
                        "sharetype" => "facebook",
                        "count" => $count_status['count'] + 1,
                        "timestamp" => date('Y-m-d H:i:s'),
                        "dbeeid" => $dbeeid,
                        "user_id" => $this->_userid
                    );
                    
                    $socialShareModel->updateSocialShare($dataArray, $dbeeid, "facebook", $this->_userid);
                }
                $data['status']  = 'success';
            }
            catch (FacebookApiException $e) {
                
                $data['type'] =  $e->getType();
                $data['message'] =  $e->getMessage();
                $data['status']  = 'error';
            }
            $data['status']  = 'success';
            $data['message'] = 'Post shared';
            
        } else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    // Seo friendly url create function 
    function makeSeo($title, $raw_title = '', $context = 'display'){
        $myhome_obj  = new Application_Model_Myhome();
        $dburl = $this->getdbeeurl($title, '', $context);
        if($myhome_obj->chkdbeetitle($dburl)){
            return $dburl;          
        }else{      
            $words = explode(' ', $title);
            $title2 = implode(' ', array_slice($words, 0, 14)).'-'.date('i:s'); 
            return $dburl = $this->getdbeeurl($title2, '', $context);
        }   
    }
    
    function getdbeeurl($title, $raw_title = '', $context = 'display')
    {
    
        $title = $this->string_limit_words($title, 15);
    
        $title = strip_tags($title);
    
        // Preserve escaped octets.
    
        $title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
    
        // Remove percent signs that are not part of an octet.
    
        $title = str_replace('%', '', $title);
    
        // Restore octets.
    
        $title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);
    
    
    
        if ($this->seems_utf8($title)) {
    
            if (function_exists('mb_strtolower')) {
    
                $title = mb_strtolower($title, 'UTF-8');
    
            }
    
            $title = $this->utf8_uri_encode($title, 200);
    
        }
    
    
    
        $title = strtolower($title);
    
        $title = preg_replace('/&.+?;/', '', $title); // kill entities
    
        $title = str_replace('.', '-', $title);
    
    
    
        if ('save' == $context) {
    
            // Convert nbsp, ndash and mdash to hyphens
    
            $title = str_replace(array(
    
                    '%c2%a0',
    
                    '%e2%80%93',
    
                    '%e2%80%94'
    
            ), '-', $title);
    
    
    
            // Strip these characters entirely
    
            $title = str_replace(array(
    
            // iexcl and iquest
    
                    '%c2%a1',
    
                    '%c2%bf',
    
                    // angle question
    
                    '%c2%ab',
    
                    '%c2%bb',
    
                    '%e2%80%b9',
    
                    '%e2%80%ba',
    
                    // curly question
    
                    '%e2%80%98',
    
                    '%e2%80%99',
    
                    '%e2%80%9c',
    
                    '%e2%80%9d',
    
                    '%e2%80%9a',
    
                    '%e2%80%9b',
    
                    '%e2%80%9e',
    
                    '%e2%80%9f',
    
                    // copy, reg, deg, hellip and trade
    
                    '%c2%a9',
    
                    '%c2%ae',
    
                    '%c2%b0',
    
                    '%e2%80%a6',
    
                    '%e2%84%a2'
    
            ), '', $title);
    
    
    
            // Convert times to x
    
            $title = str_replace('%c3%97', 'x', $title);
    
        }
    
    
    
        $title = preg_replace('/[^%a-z0-9 _-]/', '', $title);
    
        $title = preg_replace('/\s+/', '-', $title);
    
        $title = preg_replace('|-+|', '-', $title);
    
        $title = trim($title, '-');
        
        //$title = str_replace('%', '', strtolower(trim($title)));
        
        //$title = str_replace(' ', '-', strtolower(trim($title)));
    
        return $title;
    
    }
    
    
    function seems_utf8($str)
    {
    
        $length = strlen($str);
    
        for ($i = 0; $i < $length; $i++) {
    
            $c = ord($str[$i]);
    
            if ($c < 0x80)
                $n = 0; // 0bbbbbbb
    
            elseif (($c & 0xE0) == 0xC0)
            $n = 1; // 110bbbbb
            elseif (($c & 0xF0) == 0xE0)
            $n = 2; // 1110bbbb
            elseif (($c & 0xF8) == 0xF0)
            $n = 3; // 11110bbb
            elseif (($c & 0xFC) == 0xF8)
            $n = 4; // 111110bb
            elseif (($c & 0xFE) == 0xFC)
            $n = 5; // 1111110b
    
            else
                return false; // Does not match any model
    
            for ($j = 0; $j < $n; $j++) { // n bytes matching 10bbbbbb follow ?
    
                if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
                    return false;
    
            }
    
        }
    
        return true;
    
    }
    
    function utf8_uri_encode($utf8_string, $length = 0)
    {
    
        $unicode = '';
    
        $values = array();
    
        $num_octets = 1;
    
        $unicode_length = 0;
    
    
    
        $string_length = strlen($utf8_string);
    
        for ($i = 0; $i < $string_length; $i++) {
    
    
    
            $value = ord($utf8_string[$i]);
    
    
    
            if ($value < 128) {
    
                if ($length && ($unicode_length >= $length))
                    break;
    
                $unicode .= chr($value);
    
                $unicode_length++;
    
            } else {
    
                if (count($values) == 0)
                    $num_octets = ($value < 224) ? 2 : 3;
    
    
    
                $values[] = $value;
    
    
    
                if ($length && ($unicode_length + ($num_octets * 3)) > $length)
                    break;
    
                if (count($values) == $num_octets) {
    
                    if ($num_octets == 3) {
    
                        $unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]) . '%' . dechex($values[2]);
    
                        $unicode_length += 9;
    
                    } else {
    
                        $unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]);
    
                        $unicode_length += 6;
    
                    }
    
    
    
                    $values = array();
    
                    $num_octets = 1;
    
                }
    
            }
    
        }
    
        return $unicode;
    
    }
    
    function string_limit_words($string, $word_limit)
    {
    
        $words = explode(' ', $string);
    
        return implode(' ', array_slice($words, 0, $word_limit));
    
    }
     public function shareonlinkedinwallAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            $dbeeid = (int) $this->_request->getPost('dbeeid');
            $socialShareModel = new Application_Model_Socialshare();
            $config['base_url']        = BASE_URL;
            $config['callback_url']    = BASE_URL . '/social/callback';
            $config['linkedin_access'] = linkedinAppid;
            $config['linkedin_secret'] = linkedinSecret;
            
            # First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
            $linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], '');
            
            $linkedin_connect_data = Zend_Json::decode($this->session_data['linkedin_connect_data']);
            $linkedin->request_token             = unserialize($linkedin_connect_data['requestToken']);
            $linkedin->oauth_verifier            = $linkedin_connect_data['oauth_verifier'];
            $linkedin->access_token              = unserialize($linkedin_connect_data['oauth_access_token']);
            
            $dbee_result = $this->myhome_obj->checkdbeeexist($dbeeid);
            $url         = BASE_URL . '/dbee/' . $dbee_result['0']['dburl'];
            
            
            $image = '';
            
            $title = $dbee_result['0']['Text'];
            if ($dbee_result['0']['LinkPic'] != ''){
                $checkImage = new Application_Model_Commonfunctionality();
                $picprofile1 = $checkImage->checkImgExist($dbee_result['0']['LinkPic'],'imageposts','default-avatar.jpg');
                $image = BASE_URL.'/timthumb.php?src=/imageposts/'.$picprofile1.'&q=100&w=200&h=150';
            }
            else if ($dbee_result['0']['Pic'] != '')
            {
                $checkImage = new Application_Model_Commonfunctionality();
                $picprofile1 = $checkImage->checkImgExist($dbee_result['0']['Pic'],'imageposts','default-avatar.jpg');
                $image = BASE_URL.'/timthumb.php?src=/imageposts/'.$picprofile1.'&q=100&w=200&h=150';
            }
            else if ($dbee_result['0']['VidID'] != '')
                $image = 'https://i.ytimg.com/vi/'.$dbee_result['0']['VidID'].'/0.jpg';

            $comment = '';

            $image = (empty($image)) ? BASE_URL.'/img/brandlogoFb.png' : $image;

            if($this->SocialLogo!='')
                $image = URL.'/img/'.$this->SocialLogo;
            
            $linkedin->share($comment, $title, $this->shortUrl($url), $image);
            
            $count_status = $socialShareModel->checkSocialShare($dbeeid, "linkedin", $this->_userid);
            
            if ($count_status == 0) 
            {
                $dataArray = array(
                    "sharetype" => "linkedin",
                    "count" => 1,
                    "timestamp" => date('Y-m-d H:i:s'),
                    "dbeeid" => $dbeeid,
                    "user_id" => $this->_userid,
                    "clientID" => clientID
                );
                $socialShareModel->insertSocialShare($dataArray);
                
            } else {
                $dataArray = array(
                    "sharetype" => "linkedin",
                    "count" => $count_status['count'] + 1,
                    "timestamp" => date('Y-m-d H:i:s'),
                    "dbeeid" => $dbeeid,
                    "user_id" => $this->_userid
                );
                
                $socialShareModel->updateSocialShare($dataArray, $dbeeid, "linkedin", $this->_userid);
            }
            
            $data['status']  = 'success';
            $data['message'] = 'Post shared';
            
        } else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }

     public function updatelinkstatusfacebookAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $id = $this->facebook_connect_data['facebookid'];
            
            $params = array(
                'appId' => facebookAppid,
                'secret' => facebookSecret,
                'domain' => facebookDomain
            );
            $filter         = new Zend_Filter_StripTags();
            $facebook       = new Facebook($params);
            $getAccessToken = $this->facebook_connect_data['access_token'];
            $status         = $filter->filter($this->_request->getPost('facebook_status'));
            //$status ='Sorry facebook app testing';
            $link           = $this->_request->getPost('link');
            //$link =  'http://google.com';
            try {
                $result = $facebook->api('/' . $id . '/feed', 'POST', array(
                    "access_token" => $getAccessToken,
                    'message' => $status,
                    'type' => 'link',
                    'link' => $link
                ));
                $data['status']  = 'success';
            }
            catch (FacebookApiException $e) {
                
                $data['type'] =  $e->getType();
                $data['message'] =  $e->getMessage();
                $data['status']  = 'error';
            }
        }
        return $response->setBody(Zend_Json::encode($data));
    }

   public function updatestatusAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $id     = $this->facebook_connect_data['facebookid'];
            $filter = new Zend_Filter_StripTags();
            $status   = stripslashes($filter->filter($this->_request->getPost('PostText')));
            $shareUrl = $filter->filter($this->_request->getPost('shareUrl'));
            $postData = (int) $this->_request->getPost('postData');
            $shareOnEmbed = (int) $this->_request->getPost('shareOnEmbed');
            $isPostOnPlateForm = (int) $this->_request->getPost('isPostOnPlateForm');
           
            if ($status == '') 
            {
                $data['status']  = 'error';
                $data['message'] = 'Please enter your status';
                
            } else 
            {

                if ($isPostOnPlateForm == 1) 
                {
                    $activity = date('Y-m-d H:i:s');
                    $postdate = date('Y-m-d H:i:s');
                    $dbtitle  = $this->makeSeo($status, '', 'save');
                    $postdata['Type'] = 1;
                    $postdata['Text'] = $status;
                    $postdata['User']         = (int) $this->_userid;
                    $postdata['dburl']        = $dbtitle;
                    $postdata['PostDate']     = $postdate;
                    $postdata['LastActivity'] = $activity;
                    $postdata['clientID'] = clientID;
                    $dbeeid = $this->Myhome_Model->insertmydb($postdata);
                    $resultData = $this->myhome_obj->getdbeedetail($dbeeid);;
                   
                }
                if ($postData == 1 || $postData == 3) 
                {
                    if($shareOnEmbed == 1)
                        $status = $status.' '.$this->shortUrl(BASE_URL.'/dbee/'.$resultData['dburl'].'?loginfacebook=true');
                     $params = array(
                            'appId' => facebookAppid,
                            'secret' => facebookSecret,
                            'domain' => facebookDomain
                        );
                    
                    $facebook       = new Facebook($params);
                    $getAccessToken = $this->facebook_connect_data['access_token'];
                    try 
                    {
                        if(adminID==$this->_userid)
                        {
                            $page = split("-", $this->facebook_page_data);
                            $data['page_token'] = $page[0];
                            $id   = $page[1];
                            if ($shareUrl != '')
                                $result = $facebook->api('/' . $id . '/feed', 'POST', array(
                                    "access_token" => $data['page_token'] ,
                                    'message' => $status,
                                    'type' => 'link',
                                    'link' => $shareUrl
                                ));
                            else
                                $result = $facebook->api('/' . $id . '/feed', 'POST', array(
                                    "access_token" => $data['page_token'] ,
                                    'message' => $status,
                                    'type' => 'status'
                                ));
                        }else{
                             if ($shareUrl != '')
                                $result = $facebook->api('/' . $id . '/feed', 'POST', array(
                                    "access_token" => $getAccessToken,
                                    'message' => $status,
                                    'type' => 'link',
                                    'link' => $shareUrl
                                ));
                            else
                                $result = $facebook->api('/' . $id . '/feed', 'POST', array(
                                    "access_token" => $getAccessToken,
                                    'message' => $status,
                                    'type' => 'status'
                                ));
                        }
                        $data['status']  = 'success';
                    }
                    catch (FacebookApiException $e) {
                        $data['type'] =  $e->getType();
                        $data['message'] =  $e->getMessage();
                        $data['status']  = 'error';
                    }
                }
              
                if ($postData == 2 || $postData == 3) 
                {
                     if($shareOnEmbed == 1)
                        $status = $status.' '.$this->shortUrl(BASE_URL.'/dbee/'.$resultData['dburl'].'?logintwitter=true');
                    $twitteroauth = new TwitterOAuth(twitterAppid, twitterSecret, $this->twitter_connect_data['twitter_access_token'], $this->twitter_connect_data['twitter_token_secret']);
                    if ($shareUrl != '')
                        $twitteroauth->post('statuses/update', array(
                            'status' => $status . ' ' . $shareUrl
                        ));
                    else
                        $twitteroauth->post('statuses/update', array(
                            'status' => $status
                        ));
                }
            }
            
            $data['status']  = 'success';
            $data['message'] = 'successfully updated status';
            
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    public function file_get_contents_curl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    
}
