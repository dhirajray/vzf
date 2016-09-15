<?php

class Admin_LivebroadcastsController extends IsadminController
{
    
    private $options;
    public function init()
    {
        parent::init();
        $this->_options = $this->getInvokeArg('bootstrap')->getOptions();
        
        $this->infobj = new Admin_Model_Influence();
        
        
    }
    
    public function indexAction()
    {
        
        $namespace          =   new Zend_Session_Namespace();
        $deshboard          =   new Admin_Model_Deshboard();
      
        $GlobalSetting      = $this->myclientdetails->getRowMasterfromtable('tblConfiguration', array(
            'google_client_id',
            'google_client_secret',
            'redirect_uri'
        ));


        $facebook_connect_data = Zend_Json::decode($this->session_data['facebook_connect_data']);
        $twitter_connect_data = Zend_Json::decode($this->session_data['twitter_connect_data']);
        $linkedin_connect_data = Zend_Json::decode($this->session_data['linkedin_connect_data']);
        
       
        $facebook = $facebook_connect_data['access_token'];
        $twitter = $twitter_connect_data['twitter_access_token'];
        $linkedin = $linkedin_connect_data['oauth_verifier'];

        if($linkedin) // check linkedin logined or not
            $this->view->linkedinLogined = true;
        else
            $this->view->linkedinLogined = false;

        if($twitter)  // check twitter logined or not
            $this->view->twitterLogined = true;
        else
            $this->view->twitterLogined = false;

        if($facebook)  // check twitter logined or not
            $this->view->facebookLogined = true;
        else
            $this->view->facebookLogined = false;
                 
        $this->view->google_client_id     = $GlobalSetting['google_client_id'];
        $this->view->google_client_secret = $GlobalSetting['google_client_secret'];
        $this->view->redirect_uri         = $GlobalSetting['redirect_uri'];
        //$this->view->facebookurl          = $this->facebookconnect();

        $this->view->eventlist            = $deshboard->eventlist(); 
        
       
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
                    $user_personal_info['facebook_connect_data'] = Zend_Json::encode($dataArray);
                    $this->myclientdetails->updatedata_global('users',$user_personal_info,'id',$this->session['userid']);
                    $this->rewritesession();
                    $this->_redirect(BASE_URL.'/admin/user/invitesocial?invite=facebook&type=socialinvite');                   

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


    
    public function insertAction()
    {
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $namespace            = new Zend_Session_Namespace();
            $common               = new Admin_Model_Common();
            $GlobalSetting        = $this->myclientdetails->getRowMasterfromtable('tblConfiguration', array(
                'google_client_id',
                'google_client_secret',
                'redirect_uri'
            ));
            $google_client_id     = $GlobalSetting['google_client_id'];
            $google_client_secret = $GlobalSetting['google_client_secret'];
            $redirect_uri         = $GlobalSetting['redirect_uri'];
            require_once 'Google/Google/autoload.php';
            require_once 'Google/Google/Client.php';
            require_once 'Google/Google/Service/YouTube.php';
            $htmlBody             = '';
            $OAUTH2_CLIENT_ID     = $google_client_id;
            $OAUTH2_CLIENT_SECRET = $google_client_secret;
            
            $client = new Google_Client();
            $client->setClientId($OAUTH2_CLIENT_ID);
            $client->setClientSecret($OAUTH2_CLIENT_SECRET);
            $client->setScopes('https://www.googleapis.com/auth/youtube.force-ssl');
            $client->setRedirectUri($redirect_uri);
            
            // Define an object that will be used to make all API requests.
            $youtube = new Google_Service_YouTube($client);
            
            if (isset($_GET['code'])) {
                if (strval($_SESSION['state']) !== strval($_GET['state'])) {
                    die('The session state did not match.');
                }
                
                $client->authenticate($_GET['code']);
                $_SESSION['token'] = $client->getAccessToken();
                header('Location: ' . BASE_URL . '/admin/livebroadcasts/');
            }
            
            if (isset($_SESSION['token'])) {
                $client->setAccessToken($_SESSION['token']);
            }
            
            
            
            $request = $this->getRequest()->getPost();
            
            $eventzone = $request['timezone'];
            
            $newdatestart = $common->getdbtime($request['startdate'], $eventzone, 'Y-m-d H:i:s');
            
            $newdateend = $common->getdbtime($request['enddate'], $eventzone, 'Y-m-d H:i:s');
            
            /*if($eventzone=='0.0')
            {
                $newdatestart = strtotime($newdatestart) + 3600; // Add 1 hour
                $newdatestart = date('Y-m-d H:i:s', $newdatestart); // Back to string

                $newdateend = strtotime($newdateend) + 3600; // Add 1 hour
                $newdateend = date('Y-m-d H:i:s', $newdateend); // Back to string
               
            }
            else if($eventzone=='5.5')
            {
                $newdatestart = $request['startdate']; 
                $newdateend   =   $request['enddate']; 
            }*/
           
            $startdateslice = explode('+', date('c', strtotime($newdatestart)));
            // $stdate=$startdateslice[0].'T';
            // $sttime=$startdateslice[1];
            
            $enddateslice = explode('+', date('c', strtotime($newdateend)));
            //$endate=$enddateslice[0].'T';
            //$entime=$enddateslice[1];
            //echo"<pre>"; print_r($request); echo"</pre>";
            
            //die;
            // Check to ensure that the access token was successfully acquired.
            if ($client->getAccessToken()) {
                try {
                    // Create an object for the liveBroadcast resource's snippet. Specify values
                    // for the snippet's title, scheduled start time, and scheduled end time.
                    
                    $errorcode='';
                    
                    $broadcastSnippet = new Google_Service_YouTube_LiveBroadcastSnippet();
                    $broadcastSnippet->setTitle($request['title']);
                    $broadcastSnippet->setDescription($request['description']);
                    //$broadcastSnippet->setCategoryId($request['category']);
                    $broadcastSnippet->setScheduledStartTime(date("Y-m-d\TH:i:s.000\Z", strtotime($newdatestart)));
                    $broadcastSnippet->setScheduledEndTime(date("Y-m-d\TH:i:s.000\Z", strtotime($newdateend)));
                    
                    //categoryId
                    // Create an object for the liveBroadcast resource's status, and set the
                    // broadcast's status to "private".
                    $status = new Google_Service_YouTube_LiveBroadcastStatus();
                    $status->setPrivacyStatus('public');
                    
                    
                    
                    //$broadcastCat->categoryId = new Google_Service_YouTube_VideoCategories_Resource();
                    // Create the API request that inserts the liveBroadcast resource.
                    $broadcastInsert = new Google_Service_YouTube_LiveBroadcast();
                    $broadcastInsert->setSnippet($broadcastSnippet);
                    $broadcastInsert->setStatus($status);
                    $broadcastInsert->setKind('youtube#liveBroadcast');
                    
                    
                    
                    // Execute the request and return an object that contains information
                    // about the new broadcast.
                    $broadcastsResponse = $youtube->liveBroadcasts->insert('snippet,status', $broadcastInsert, array());
                    
                    // Create an object for the liveStream resource's snippet. Specify a value
                    // for the snippet's title.
                    $streamSnippet = new Google_Service_YouTube_LiveStreamSnippet();
                    $streamSnippet->setTitle('New Stream');
                    
                    // Create an object for content distribution network details for the live
                    
                    // stream and specify the stream's format and ingestion type.
                    $cdn = new Google_Service_YouTube_CdnSettings();
                    $cdn->setFormat("240p");
                    $cdn->setIngestionType('rtmp');
                    
                    // Create the API request that inserts the liveStream resource.
                    $streamInsert = new Google_Service_YouTube_LiveStream();
                    $streamInsert->setSnippet($streamSnippet);
                    $streamInsert->setCdn($cdn);
                    $streamInsert->setKind('youtube#liveStream');
                    
                    
                    // Execute the request and return an object that contains information
                    // about the new stream.
                    $streamsResponse = $youtube->liveStreams->insert('snippet,cdn', $streamInsert, array());
                    
                    // Bind the broadcast to the live stream.
                    $bindBroadcastResponse = $youtube->liveBroadcasts->bind($broadcastsResponse['id'], 'id,contentDetails', array(
                        'streamId' => $streamsResponse['id']
                    ));
                    
                    if ($broadcastsResponse['id']) {
                        $deshboard  = new Admin_Model_Deshboard();
                        $dburl      = $this->makeSeo($request['title'], '', 'save');
                        if($request['selectEventList']!="")
                        {
                           $selectEventList = $request['selectEventList'];
                        }
                        else
                        {
                            $selectEventList = 0;
                        }

                        $yt_url     = 'https://www.youtube.com/watch?v=' . $broadcastsResponse['id'];
                        $data       = array(
                            'liveeventend' => $newdateend,
                            'Type' => '15',
                            'User' => $this->adminUserID,
                            'Vid' => $yt_url,
                            'Text' => $request['title'],
                            'VidDesc' => $request['description'],
                            'VidSite' => 'youtube',
                            'VidID' => $broadcastsResponse['id'],
                            'eventtype'=>$request['eventtype'],
                            'eventstart' => $newdatestart,
                            'PostDate' => date('Y-m-d H:i:s'),
                            'LastActivity' => date('Y-m-d H:i:s'),
                            'dburl' => $dburl,
                            'slideshare' => '',
                            'eventzone' => $eventzone,
                            'clientID' => clientID,
                            'events' => $selectEventList,
                            'Active' => 1
                        );
                        $insspecial = $deshboard->insertdata_global('tblDbees', $data);

                        if($request['eventtype']==2)
                        {   

                             $data2       = array(
                            'dbeeID' => $insspecial,                            
                            'userID' => $this->adminUserID,                           
                            'clientID' => clientID,
                            'status' => 1,
                            'timestamp' => date('Y-m-d H:i:s')
                        );

                        $tblDbeeJoined = $deshboard->insertdata_global('tblDbeeJoinedUser', $data2);

                        }
                        
                    }
                    
                    //echo"<pre>"; print_r($streamsResponse); echo"</pre>"; die; 
                    // /echo $broadcastsResponse['id']; die;
                }
                catch (Google_ServiceException $e) {
                    $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>', htmlspecialchars($e->getMessage()));
                }
                catch (Google_Exception $e) {
                    $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>', htmlspecialchars($e->getMessage()));
                
                   echo $errorcode=htmlspecialchars($e->getCode());

                }
                
                $_SESSION['token'] = $client->getAccessToken();
            } else {
                // If the user hasn't authorized the app, initiate the OAuth flow
                
                $state = mt_rand();
                $client->setState($state);
                $_SESSION['state'] = $state;
                
                $authUrl  = $client->createAuthUrl();
                $htmlBody = <<<END
  <h3>Authorization Required</h3>
  <p>You need to <a href="$authUrl">authorize access</a> before proceeding.<p>
END;
            }
            //
            //echo $htmlBody;
        }
        
    }
    
    
    
    
    public function deleteAction()
    {
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $namespace            = new Zend_Session_Namespace();
            $GlobalSetting        = $this->myclientdetails->getRowMasterfromtable('tblConfiguration', array(
                'google_client_id',
                'google_client_secret',
                'redirect_uri'
            ));
            $google_client_id     = $GlobalSetting['google_client_id'];
            $google_client_secret = $GlobalSetting['google_client_secret'];
            $redirect_uri         = $GlobalSetting['redirect_uri'];
            require_once 'Google/Google/autoload.php';
            require_once 'Google/Google/Client.php';
            require_once 'Google/Google/Service/YouTube.php';
            $htmlBody             = '';
            $OAUTH2_CLIENT_ID     = $google_client_id;
            $OAUTH2_CLIENT_SECRET = $google_client_secret;
            
            $client = new Google_Client();
            $client->setClientId($OAUTH2_CLIENT_ID);
            $client->setClientSecret($OAUTH2_CLIENT_SECRET);
            $client->setScopes('https://www.googleapis.com/auth/youtube');
            $client->setRedirectUri($redirect_uri);
            // Define an object that will be used to make all API requests.
            $youtube = new Google_Service_YouTube($client);
            
            if (isset($_GET['code'])) {
                if (strval($_SESSION['state']) !== strval($_GET['state'])) {
                    die('The session state did not match.');
                }
                
                $client->authenticate($_GET['code']);
                $_SESSION['token'] = $client->getAccessToken();
                header('Location: ' . BASE_URL . '/admin/livebroadcasts/');
            }
            
            if (isset($_SESSION['token'])) {
                
                $client->setAccessToken($_SESSION['token']);
            }
            
            
            
            $request = $this->getRequest()->getPost();
            $VidId   = $request['VidId'];
            // Check to ensure that the access token was successfully acquired.
            if ($client->getAccessToken()) {
                try {
                    
                    //$this->deleteCaption($youtube, $VidId, $htmlBody);
                   
                    if($this->myclientdetails->deletedata_global('tblDbees','VidID',$VidId))
                    {
                      $youtube->liveBroadcasts->delete($VidId);
                    }
                    
                }
                catch (Google_ServiceException $e) {
                    $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>', htmlspecialchars($e->getMessage()));
                }
                catch (Google_Exception $e) {
                    $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>', htmlspecialchars($e->getMessage()));
                }
                
                $_SESSION['token'] = $client->getAccessToken();
            } else {
                // If the user hasn't authorized the app, initiate the OAuth flow
                
                $state = mt_rand();
                $client->setState($state);
                $_SESSION['state'] = $state;
                
                $authUrl  = $client->createAuthUrl();
                $htmlBody = <<<END
  <h3>Authorization Required</h3>
  <p>You need to <a href="$authUrl">authorize access</a> before proceeding.<p>
END;
            }
            //
            //echo $htmlBody;
        }
        
    }


    public function startbroadcastAction()
    {
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            
            $deshboard  = new Admin_Model_Deshboard();          
            $request    = $this->getRequest()->getPost();
            $VidId      = $request['VidId'];
            $DbeeID     = $request['dbeeID'];
            $dburl      = $request['dburl'];
            
            $userinfo = array(
                            'userid' => $this->_userid,
                            'dbid' => $DbeeID,
                            'status' => '1',
                            'currentdate' => date('Y-m-d H:i:s'),
                            'clientID' => clientID
                         );

             $datadbee['expertuser'] = $this->_userid;
             $this->myclientdetails->updatedata_global('tblDbees',$datadbee,'DbeeID',$DbeeID);
             $insspecial = $deshboard->insertdata_global('tblexpert', $userinfo);
             $data['status']       = 'success';
            
        }
        else
        {
            $data['status']  = 'error';
            $data['message'] = 'some thing was wrong';
        }

        return $response->setBody(Zend_Json::encode($data));
        
    }
    
    
    public function deleteCaption(Google_Service_YouTube $youtube, $captionId, &$htmlBody)
    {
        // Call the YouTube Data API's captions.delete method to delete a caption.
        $youtube->captions->delete($captionId);
        
        $htmlBody .= "<h2>Deleted caption track</h2><ul>";
        $htmlBody .= sprintf('<li>%s</li>', $captionId);
        $htmlBody .= '</ul>';
    }
}
