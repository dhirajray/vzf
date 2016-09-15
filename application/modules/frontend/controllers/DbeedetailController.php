<?php
class DbeedetailController extends IsController
{
    protected $profileTable;
    protected $_userid = null;
    protected $dbeeid;
    protected $myhome_obj;
    protected $dbeeCommentobj;
    protected $commonmodel_obj;
    protected $profile_obj;
    
    public function init()
    {
        parent::init();
        $this->dbeeid = (int) $this->_getParam('id');
        
        $auth    = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) 
        {
            $storage = new Zend_Auth_Storage_Session();
            $data               = $storage->read();
            $this->_userid      = $data['UserID'];
            $this->session_data = $data;
        }
        $this->expert = new Application_Model_Expert();
        $this->KeepMeLoggedIn();
        
    }
    
    public function KeepMeLoggedIn()
    {
        // auto login
        if (isSet($_COOKIE['RememberEmail']) && $_COOKIE['RememberEmail'] != "" && isSet($_COOKIE['Rememberpass']) && $_COOKIE['Rememberpass'] != "") {
            
            $error_message = 'Incorrect log in details. Please try again.';
            
            $request['loginpass']  = $this->myclientdetails->customDecoding($_COOKIE['Rememberpass']);
            $request['loginemail'] = $_COOKIE['RememberEmail'];
            
            
            $auth     = Zend_Auth::getInstance();
            $registry = Zend_Registry::getInstance();
            if (empty($request['act_email'])) {
                $userresult = $this->myclientdetails->getfieldsfromtable('*', 'tblUsers', 'Email', $request['loginemail'], 'clientID', clientID);
                
                if ((isset($request['loginemail']) && !empty($request['loginemail'])) && (isset($request['loginpass']) && !empty($request['loginpass'])) && (isset($userresult[0]['Status']) && !empty($userresult[0]['Status']))) {
                    
                    $filter  = new Zend_Filter_Decrypt();
                    $config  = Zend_Registry::get('config');
                    $db      = Zend_Db::factory('Pdo_Mysql', $config->resources->db->params);
                    $adapter = new Zend_Auth_Adapter_DbTable($db);
                    $adapter->setTableName('tblUsers');
                    $adapter->setIdentityColumn('Email');
                    $adapter->setCredentialColumn('Pass');
                    $adapter->setIdentity($request['loginemail']);
                    $chkPass = $adapter->setCredential($adapter->setCredentialdbee($request['loginpass'], $userresult[0]['Pass']));
                    
                    $select = $adapter->getDbSelect();
                    $select->where('clientID = ?', clientID);
                    
                    $result = $auth->authenticate($adapter);
                    
                    if ($result->isValid()) {
                        
                        $userinfodata = array(
                            'UserID' => $adapter->getResultRowObject()->UserID,
                            'role' => $adapter->getResultRowObject()->role,
                            'clientID' => $adapter->getResultRowObject()->clientID,
                            'Title' => $adapter->getResultRowObject()->title,
                            'company' => $adapter->getResultRowObject()->company,
                            'Email' => $adapter->getResultRowObject()->Email,
                            'Name' => $adapter->getResultRowObject()->Name,
                            'lname' => $adapter->getResultRowObject()->lname,
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
                        
                        $addStatus = $this->myclientdetails->updatedata_global('tblUsers', $users, 'UserID', $UserID);
                        
                        $this->myclientdetails->sessionWrite($userinfodata);
                        
                        $username = $this->myclientdetails->customDecoding($userinfodata['Username']);
                        $this->special_dbee_session->redirectToSpecialDbeePage = $this->curPageURL();

                    } else {
                        $this->_helper->flashMessenger->addMessage($error_message);
                        $this->user_session->form_status                       = 0;
                        $this->special_dbee_session->redirectToSpecialDbeePage = BASE_URL . '/';
                    }
                } else {
                    $this->_helper->flashMessenger->addMessage($error_message);
                    $this->user_session->form_status                       = 0;
                    $this->special_dbee_session->redirectToSpecialDbeePage = BASE_URL . '/';
                }
            }
            
        }
        
    }

    
    public function socialblockAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->_userid == '') 
        {
            $data['message'] = base64_encode('Your session timed out!<br>Please login again.');
            $data['ForcedLogout'] = "logout";
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    
    public function expiresocialAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $this->_helper->flashMessenger->clearCurrentMessages();
        $this->_helper->flashMessenger->addMessage('unblock');
        if ($this->Social_Content_Block == 'unblock') {
            $this->session_name_space->Social_Content_Block = 'unblock';
            
        } else if ($this->Social_Content_Block == 'block') {
            $this->session_name_space->Social_Content_Block = 'block';
        }
        $this->session_name_space->Social_block_redirect = 'yes';
        return $response->setBody(Zend_Json::encode($data));
        
    }
    
    public function profanityfilterAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $content = '';
        if ($this->getRequest()->isXmlHttpRequest()) 
        {
            if ($this->_userid == '') 
                return $response->setBody(Zend_Json::encode($data)); // return without login
            
            $rowData = $this->User_Model->getUserDetail(adminID); // get admin user social connect info
            
            $twitter_connect_data = Zend_Json::decode($rowData['twitter_connect_data']);
            
            if (!empty($twitter_connect_data) && $this->socialloginabilitydetail['Twitterstatus']==0)
                $data['admin_twitter_data'] = true;
            else
                $data['admin_twitter_data'] = false;
            
            if ($this->facebook_page_data != '' && $this->socialloginabilitydetail['Facebookstatus']==0) 
            {
                $page = split("-", $this->facebook_page_data);
                $data['page_token'] = $page[0];
                $data['page_id']    = $page[1];
            } 
            else 
            {
                $data['page_token'] = '';
                $data['page_id']    = '';
            }
            
            $result = $this->myhome_obj->fetchprofanityfilter();
            $data['status']       = 'success';
            $data['result']       = $result;
            $data['ShowRSS']       = $this->ShowRSS;
            $data['Facebookstatus']       = $this->socialloginabilitydetail['Facebookstatus'];
            $data['Twitterstatus']       = $this->socialloginabilitydetail['Twitterstatus'];
            $data['getrestrictedurl'] = $this->myhome_obj->getrestrictedurllist();
            $data['fetchTwitter'] = $this->session_data['twitterTag'];
            $data['clientType'] = $this->clientType;
            $data['allowmultipleexperts'] = $this->allowmultipleexperts;
            
        } else {
            $data['status']  = 'error';
            $data['message'] = 'some thing was wrong';
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    
    public function checkUsereligibleInAds($type, $relationID)
    {
        //echo $relationID.' == '.$type. ' '.$this->_userid; exit;
        if ($type == 4 && $relationID == $this->_userid)
            return true;
        else if ($type == 2) {
            $result = $this->myhome_obj->searchUser_group($relationID, $this->_userid);
            if (!empty($result))
                return true;
        } else if ($type == 3) {
            $result = $this->myhome_obj->searchGroup($relationID, $this->_userid);
            if (!empty($result))
                return true;
        }
        return false;
    }
    
    
    function curPageURL()
    {
        $pageURL = 'http';
        if ($_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }
    
    // this function is responsible for adding join users for special dbee
    
    public function specialdbeeAction()
    {
        
        $data      = array();
        $dataArray = array();
        
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            $dataArray['userID']    = (int) $this->_userid;
            $dataArray['dbeeID']    = (int) $this->_request->getPost('dbeeID');
            $dataArray['status']    = 1;
            $dataArray['timestamp'] = date('Y-m-d H:i:s');
            $dataArray['clientID']  = clientID;

            $row = $this->myhome_obj->getdbeedetail($dataArray['dbeeID']);

            switch ($row['eventtype']) 
            {
                case '0':
                   $checkCurrentUserJoinedOrNot = $this->myhome_obj->checkUserOrNotJoined($this->_userid, $dataArray['dbeeID']);
                    
                    if (empty($checkCurrentUserJoinedOrNot)) 
                    {
                        $this->myclientdetails->insertdata_global('tblDbeeJoinedUser', $dataArray);
                        $email  = $this->myhome_obj->getadminemail();
                        $eventName = '<a href="'.BASE_URL.'/dbee/'.$row['dburl'].'">'.$row['Text'].'</a>';
                        $this->sendMailToAdminForSpecialDbUseraccept($email,$this->session_data, $eventName);
                        $this->notification->commomInsert(16, 25, $dataArray['dbeeID'], $this->_userid, adminID);
                        $data['status']   = 'success';
                        $data['contents'] = $this->fetchLatestAttendies($dataArray['dbeeID']);
                        $data['message']  = 'You have successfully joined the event and are now on the attendee list';

                        $data['result_dbee_attendies'] = count($this->Myhome_Model->attendies($dataArray['dbeeID']));
                    } else {
                        $data['status']  = 'error';
                        $data['message'] = 'You are already on the attendee list';
                    }
                    break;
                case '1':

                    $checkCurrentUserJoinedOrNot = $this->myhome_obj->checkUserOrNotJoined($this->_userid, $dataArray['dbeeID']);

                    if (empty($checkCurrentUserJoinedOrNot)) 
                    {
                        unset($dataArray);
                        $dataArray['userID']    = (int) $this->_userid;
                        $dataArray['dbeeID']    = (int) $this->_request->getPost('dbeeID');
                        $dataArray['status']    = 1;
                        $dataArray['timestamp'] = date('Y-m-d H:i:s');
                        $dataArray['clientID']  = clientID;
                        $this->myclientdetails->insertdata_global('tblDbeeJoinedUser', $dataArray);
                        $row    = $this->myhome_obj->getdbeedetail($dataArray['dbeeID']);
                        $email  = $this->myhome_obj->getadminemail();
                        $result = $this->myhome_obj->getusername($this->_userid);
                        $eventName = '<a href="'.BASE_URL.'/dbee/'.$row['dburl'].'">'.$row['Text'].'</a>';
                        $this->senmailtoadmin($email, $this->session_data, $eventName);
                        $this->notification->commomInsert(17, 26, $dataArray['dbeeID'], $this->_userid, adminID);
                        $data['status']  = 'success';
                        $data['message'] = 'Request sent to event admin';
                        $data['result_dbee_attendies'] = '';
                    } else {
                        $data['status']  = 'error';
                        $data['message'] = 'you have already joined successfully';
                    }

                    break;
                    case '2':
                    
                    $checkCurrentUserJoinedOrNot = $this->myhome_obj->checkUserOrNotJoined($this->_userid, $dataArray['dbeeID']);
            
                    if (empty($checkCurrentUserJoinedOrNot)) 
                    {
                        $this->myclientdetails->insertdata_global('tblDbeeJoinedUser', $dataArray);
                        $updateData['token'] = '';
                        if ($InsertStatus == true) // delete invation record
                            $this->myhome_obj->deleteSpecialDbeeInvitation($updateData, $this->special_dbee_session->Special_Token);
                        
                        $dbee_urltitles = $this->myhome_obj->getdburltitle($dataArray['dbeeID']);
                        $email  = $this->myhome_obj->getadminemail();
                        
                        $eventName = '<a href="'.BASE_URL.'/dbee/'.$row['dburl'].'">'.$row['Text'].'</a>';
                        $this->sendMailToAdminForSpecialDbUseraccept($email,$this->session_data, $eventName);
                        
                        $this->notification->commomInsert(18, 27, $dataArray['dbeeID'], $this->_userid, adminID);
                        
                        $data['contents'] = $this->fetchLatestAttendies($dataArray['dbeeID']);
                        $data['status']   = 'success';
                        $data['message']  = 'You have successfully joined the event and are on the attendee list now';
                        $data['redirect'] = BASE_URL . '/dbee/' . $dbee_urltitles;
                        $data['result_dbee_attendies'] = '';
                    } else {
                        $data['status']  = 'error';
                        $data['message'] = 'You are already on the attendee list';
                    }
                    break;
            }
            
        } else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
        
    }

    
    public function Attendies($dbeeid)
    {
        return $this->myhome_obj->attendies($dbeeid);
    }
    
  
    public function specialDbFeature($type, $token, $dbeeid, $authID,$dbdata)
    {
        
        $resultFromToken = $this->myhome_obj->checkSpecialDBeeInvitationToken($token, $authID, $dbeeid);
        
        $checkType = $this->userSocialType($type);
         //print_r($resultFromToken); 
        $dbee_urltitles = $dbdata['dburl'];
        
        $dbeeType = 'private';
        
        // check current user already joined or not 
        $redirection_name_space                                = new Zend_Session_Namespace('User_Session');
        if (empty($resultFromToken) && $this->_userid != '') 
        {
            $this->_redirect('dbee/' . $dbee_urltitles . '?expirelink=1');
            $this->special_dbee_session->showAcceptPopup = "reject";
        }
        if ($authID == $this->_userid && $this->_userid != '') 
        {
            $this->special_dbee_session->showAcceptPopup = "accept";
        } else if ($this->session_data['Socialtype'] == $checkType && $this->_userid != '' && ($authID == $this->session_data['Socialid'])) {
            $this->special_dbee_session->showAcceptPopup = "accept";
        }
        else if ($this->_userid != '' && $this->session_data['Socialtype'] == $checkType && ($authID != $this->session_data['Socialid'])) 
        { 
            $this->special_dbee_session->typeExpert            = $type;
            $this->special_dbee_session->showAcceptPopup       = "reject";
            $this->special_dbee_session->showAcceptPopupLogout = "logout";
            $this->special_dbee_session->invitationType        = $dbeeType;
            if ($type != '')
                $redirection_name_space->redirection = BASE_URL . '/dbee/' . $dbee_urltitles . '?sptype=' . $type . '&sptoken=' . $token . '&spauthid=' . $authID; // redirect after logout thats why assign in session       
            else
                $redirection_name_space->redirection = BASE_URL . '/dbee/' . $dbee_urltitles . '&sptoken=' . $token . '&spauthid=' . $authID; // redirect after logout thats why assign in session        
            
        }
        else if ($this->_userid != '' && $this->session_data['Socialtype'] != $checkType) 
        { 
            $this->special_dbee_session->typeExpert            = $type;
            $this->special_dbee_session->showAcceptPopup       = "reject";
            $this->special_dbee_session->showAcceptPopupLogout = "logout";
            $this->special_dbee_session->invitationType        = $dbeeType;
            if ($type != '')
                $redirection_name_space->redirection = BASE_URL . '/dbee/' . $dbee_urltitles . '?sptype=' . $type . '&sptoken=' . $token . '&spauthid=' . $authID; // redirect after logout thats why assign in session       
            else
                $redirection_name_space->redirection = BASE_URL . '/dbee/' . $dbee_urltitles . '&sptoken=' . $token . '&spauthid=' . $authID; // redirect after logout thats why assign in session        
            
        }
        else if ($this->_userid != '') 
        {
            $this->special_dbee_session->Special_Redirection = '';
            $this->special_dbee_session->showAcceptPopup     = "reject";
            $this->_redirect(BASE_URL . '/dbee/' . $dbee_urltitles . '?wrong=1');
        }
        
        if ($this->special_dbee_session->showAcceptPopup == "accept") 
        {
            $this->special_dbee_session->Special_Token       = $token;
            $this->special_dbee_session->Special_AuthID      = $authID;
            $this->special_dbee_session->Special_Redirection = '';
            $this->special_dbee_session->Special_DbeeID      = $dbeeid;
            $this->special_dbee_session->Special_Type        = $type;
            $this->special_dbee_session->invitationType      = $dbeeType;
        } 
        else 
        {
            $this->special_dbee_session->Special_Type   = $type;
            $this->special_dbee_session->invitationType = $dbeeType;
        }
        if ($this->_userid == '') 
        {
            $this->special_dbee_session->Special_Token             = $token;
            $this->special_dbee_session->Special_AuthID            = $authID;
            $this->special_dbee_session->Special_Redirection       = '';
            $this->special_dbee_session->Special_DbeeID            = $dbeeid;
            $this->special_dbee_session->Special_Type              = $type;
            $this->special_dbee_session->invitationType            = $dbeeType;
            $redirection_name_space->redirection                   = $this->curPageURL();
        }
    }
    
    public function userSocialType($type)
    {
        if ($type == 'facebook')
            $checkType = 1;
        else if ($type == 'twitter')
            $checkType = 2;
        else if ($type == 'linkedin')
            $checkType = 3;
        else if ($type == 'dbee')
            $checkType = 0;
        return $checkType;
    }
    
    public function fetchLatestAttendies($dbeeid)
    {
        $content               = '';
        $result_dbee_attendies = $this->Attendies($dbeeid);
        if (count($result_dbee_attendies)) {
            $k = 1;
            $content .= '<h2>Attendees</h2><div class="attendeesBox"><ul class="slides">';
            $content .= '<li>';
            foreach ($result_dbee_attendies as $res_value) {
                $checkImage  = new Application_Model_Commonfunctionality();
                $userprofile = $checkImage->checkImgExist($res_value['ProfilePic'], 'userpics', 'default-avatar.jpg');
                
                $content .= '<a href="/user/' . $this->myclientdetails->customDecoding($res_value['Username']) . '" title="' . $this->myclientdetails->customDecoding($res_value['Name']) . '" rel="dbTip">
                <img src="'.IMGPATH.'/users/small/' . $userprofile . '" width="60" height="60">
              </a>';
                if ($k % 20 == 0) {
                    $content .= '</li><li>';
                }
                $k++;
            }
            return $content .= '</li></ul></div>';
        }
    }

    public function youtubevideoendAction()
    {
        $data      = array();
        $dataArray = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            $dbeeid = (int) $this->_request->getPost('dbeeid');
            
            $row = $this->myhome_obj->getdbeedetail($dbeeid);

            $currentTime = strtotime(date('Y-m-d H:i:s'));
            
            $videoEndTime = (strtotime($row['eventstart'])+$row['eventend']);

            if($currentTime > $videoEndTime)
            {
                $dataArray['videoWatched'] = 1;
                $this->myhome_obj->updatedbee($dataArray, $dbeeid);
                $data['status']  = true;
            }else{
                $data['differenceInSeconds']  = $videoEndTime - $currentTime;
                $data['status']  = false;
            }

        } else 
        {
            $data['status']  = 'error';
            $data['message'] = "Some thing went wrong here please try again";
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    
    public function youtubevideocontrolsAction()
    {
        $data      = array();
        $dataArray = array();
        
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            
            $dbeeid                 = (int) $this->_request->getPost('dbeeid');
            $row                    = $this->myhome_obj->getdbeedetail($dbeeid);
            $currentTime            = date('Y-m-d H:i:s');
            $data['videoDuration']  = $row['eventend'];
            $data['eventStartTime'] = $row['eventstart'];
            if ($row['eventstart'] <= $currentTime) 
            {
                $data['eventStartTimeInSecond'] = strtotime($row['eventstart']);
                $data['serverTimeInSecond']     = strtotime($currentTime);
                $data['differenceInSeconds']    = $data['serverTimeInSecond'] - $data['eventStartTimeInSecond'];
                if ($data['videoDuration'] <= $data['differenceInSeconds']) 
                    $data['controls'] = true;
                else
                    $data['controls'] = false;
            } else 
                $data['controls'] = false;
            
        } else {
            $data['status']  = 'error';
            $data['message'] = "Some thing went wrong here please try again";
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    
    public function shouldbeplayAction()
    {
        $data      = array();
        $dataArray = array();
        
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            $dbeeid = (int) $this->_request->getPost('dbeeid');

            $row = $this->myhome_obj->getdbeedetail($dbeeid);

            $currentTime = strtotime(date('Y-m-d H:i:s'));
            
            $startTime = strtotime($row['eventstart']);

            $diff = $startTime - $currentTime;

            if ($this->_userid == '') 
            {
                $data['LoginStatus']       = false;
                $data['videoStartStatus']  = false;
                $data['commentOpenStatus'] = false;
            } 
            else if ($this->_userid != '') 
            {
                $userAttendStatus = $this->myhome_obj->Userattendies($dbeeid, $this->_userid);
                $data['LoginStatus']      = true;
                $data['userAttendStatus'] = $userAttendStatus;
                switch ($userAttendStatus) 
                {
                    case '0':
                        
                        $data['videoStartStatus']  = false;
                        $data['commentOpenStatus'] = false;

                        if ($row['eventtype'] == 0) 
                        {
                            if ($diff < 0) 
                            {
                                $data['videoStartStatus']  = true;
                                $data['commentOpenStatus'] = true;
                            }
                            $data['invitationType']    = 'public';
                            $data['eventJoinLinkHide'] = false;
                        } 
                        else if ($row['eventtype'] == 1) 
                        {
                            $data['invitationType']    = 'protected';
                            $data['eventJoinLinkHide'] = false;
                            $data['videoStartStatus']  = false;
                        }
                        $data['case'] = 0;
                        break;
                    
                    default:
                        
                        $commentOpenStatus = ($row['commentduring'] == 1) ? true : false;
                        
                        $data['commentOpenStatus'] = $commentOpenStatus;
                        
                        // vedio play condition
                        if ($diff < 0) 
                        {
                            $data['videoStartStatus'] = true;
                        } 
                        $data['case'] = 1;

                        break;
                }
            }
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    
    public function shouldbepauseAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {    
            $dbeeid = (int) $this->_request->getPost('dbeeid');
            $row    = $this->myhome_obj->getdbeedetail($dbeeid);
           
            $currentTime = strtotime(date('Y-m-d H:i:s'));
            
            $videoEndTime = $row['eventend'];

            $startTime = strtotime($row['eventstart']);

            $diff = abs($startTime - $currentTime);
            $data['diff'] = $diff;
            $data['videoEndTime'] = $videoEndTime;

            if($diff > $videoEndTime)
                $data['pause'] = true;
            else
                $data['pause'] = false;
            
        }
        return $response->setBody(Zend_Json::encode($data));
    }

    
    public function attendiesContent($dbeeid)
    {
        $Atendiescontent       = '';
        $result_dbee_attendies = $this->Attendies($dbeeid);
        $data['attendies']     = count($result_dbee_attendies);
        if (count($result_dbee_attendies)) 
        {
            $Atendiescontent .= '<h2>Attendees<a class="clseSideBr" href="#"><i id="grpclosert" class="fa fa-times-circle"></i></a></h2>';
            $Atendiescontent .= '<div class="rboxContainer attendeesBox ps-container"><ul class="slides">';
            $k = 1;
            $Atendiescontent .= '<li>';
            foreach ($result_dbee_attendies as $res_value) {
                $checkImage   = new Application_Model_Commonfunctionality();
                $userprofile1 = $checkImage->checkImgExist($res_value['ProfilePic'], 'userpics', 'default-avatar.jpg');
                $Atendiescontent .= '<a href="/user/' . $this->myclientdetails->customDecoding($res_value['Username']) . '" title="' . $this->myclientdetails->customDecoding($res_value['Name']) . '" rel="dbTip">
                <img src="'.IMGPATH.'/users/small/' . $userprofile1 . '" width="60" height="60">
                </a>';
                if ($k % 20 == 0)
                    $Atendiescontent .= '</li><li>';
                
                $k++;
            }
            $Atendiescontent .= '</li>';
            $Atendiescontent .= '</ul></div>';
        }
        return $Atendiescontent;
    }
    
    public function isexpert($row)
    {
        if ($this->session_name_space->showTwitterAcceptPopup == true)
            return true;
        if ($this->_userid == $row['User'])
            return false;
        else
            return false;
    }
    
    public function videoduration($row)
    {
        $currentTime            = date('Y-m-d H:i:s');
        $data['eventStartTime'] = $row['eventstart'];
        $data['serverTime']     = $currentTime;
        
        $data['videoDuration'] = $row['eventend'];
        $data['videoID']       = $row['VidID'];
        
        $data['eventStartTimeInSecond'] = strtotime($row['eventstart']);
        $data['serverTimeInSecond']     = strtotime($currentTime);
        
        if ($data['serverTimeInSecond'] <= $data['eventStartTimeInSecond']) {
            $data['differenceInSeconds'] = $data['eventStartTimeInSecond'] - $data['serverTimeInSecond'];
            $data['expireInSeconds']     = 0;
        } else {
            $data['expireInSeconds'] = $data['serverTimeInSecond'] - $data['eventStartTimeInSecond'];
            if ($data['videoDuration'] < $data['expireInSeconds']) {
                $data['timeexpired'] = true;
            } else {
                $data['timeexpired'] = false;
            }
        }
        return $data;
    }
    
    public function youtubevideoplayAction()
    {
        $data      = array();
        $dataArray = array();
        
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            
            $dbeeid = (int) $this->_request->getPost('dbeeid');
            $row  = $this->myhome_obj->getdbeedetail($dbeeid);
            
            $result_dbee_attendies = $this->myhome_obj->attendies($dbeeid);
            $count = count($result_dbee_attendies);
            
            $attendiesListStop = ($row['attendiesList'] == '1') ? 'checked' : '';
            
            if ($count != 0)
                $data['Atendiescontent'] = $this->view->partial('partials/attendies.phtml', array(
                    'resultData' => $result_dbee_attendies,
                    'count' => $count,
                    'myclientdetails' => $this->myclientdetails,
                    'dbeeid' => $dbeeid,

                    'attendiesListStop' => $attendiesListStop,
                    'userid' => $this->_userid,
                    'adminID' => adminID
                ));
            else
                $data['Atendiescontent'] = '';
            
            $data['isexpert'] = $this->isexpert($row);
            $data['videoID']  = $row['VidID'];
            
            $getArray = $this->videoduration($row);
            
            if ($this->_userid == '') 
            {
                $data['LoginStatus']       = false;
                $data['videoStartStatus']  = false;
                $data['videoLoadStatus']   = true;
                $data['commentOpenStatus'] = false;
                $data['userAttendStatus']  = 0;
                if ($getArray['timeexpired'] == false) 
                {
                    $data['expireInSeconds']  = $getArray['differenceInSeconds'];
                    $data['removeVideoLayer'] = false;
                    $data['timeexpired']      = false;
                } else {
                    
                    $data['removeVideoLayer'] = true;
                    $data['timeexpired']      = true;
                }
            } else if ($this->_userid != '') {
                $userAttendStatus         = $this->myhome_obj->Userattendies($dbeeid, $this->_userid);
                $userAttendStatus2        = $this->myhome_obj->Userattendies2($dbeeid, $this->_userid);
                $data['LoginStatus']      = true;
                $data['userAttendStatus'] = $userAttendStatus;
                $data['videoLoadStatus']  = true; // video load condition
                if ($row['attendiesList'] == 0) 
                {
                    $data['Atendiescontent']  = '';
                }
                
                if ($userAttendStatus == 0 && $row['eventtype'] == 0 && $getArray['expireInSeconds'] == 0) {
                    $data['videoStartStatus']  = false;
                    $data['commentOpenStatus'] = false;
                    $data['invitationType']    = 'public';
                    $data['eventJoinLinkHide'] = false;
                    $data['removeVideoLayer']  = false;
                    $data['expireInSeconds']   = $getArray['differenceInSeconds'];
                    $data['timeexpired']       = false;
                } else if ($userAttendStatus == 0 && $row['eventtype'] == 0 && $getArray['timeexpired'] == false) {
                    $data['videoStartStatus']  = false;
                    $data['commentOpenStatus'] = false;
                    $data['invitationType']    = 'public';
                    $data['eventJoinLinkHide'] = false;
                    $data['removeVideoLayer']  = false;
                    $data['expireInSeconds']   = 0;
                    $data['timeexpired']       = false;
                } else if ($userAttendStatus == 0 && $row['eventtype'] == 0 && $getArray['timeexpired'] == true) {
                    $data['videoStartStatus']  = false;
                    $data['commentOpenStatus'] = true;
                    $data['invitationType']    = 'public';
                    $data['eventJoinLinkHide'] = true;
                    $data['expireInSeconds']   = $getArray['expireInSeconds'];
                    $data['removeVideoLayer']  = true;
                    $data['timeexpired']       = true;
                } else if ($userAttendStatus == 0 && $row['eventtype'] == 1 && $getArray['expireInSeconds'] == 0) {
                    $data['videoStartStatus']  = false;
                    $data['commentOpenStatus'] = false;
                    $data['invitationType']    = 'protected';
                    $data['removeVideoLayer']  = false;
                    if ($userAttendStatus2 == 1)
                        $data['eventJoinLinkHide'] = true;
                    else
                        $data['eventJoinLinkHide'] = false;
                    
                    $data['expireInSeconds'] = $getArray['differenceInSeconds'];
                    $data['timeexpired']     = false;
                } else if ($userAttendStatus == 0 && $row['eventtype'] == 1 && $getArray['timeexpired'] == false) {
                    $data['videoStartStatus']  = false;
                    $data['commentOpenStatus'] = false;
                    $data['invitationType']    = 'protected';
                    if ($userAttendStatus2 == 1)
                        $data['eventJoinLinkHide'] = true;
                    else
                        $data['eventJoinLinkHide'] = false;
                    $data['removeVideoLayer'] = false;
                    $data['expireInSeconds']  = 0;
                    $data['timeexpired']      = false;
                } else if ($userAttendStatus == 0 && $row['eventtype'] == 1 && $getArray['timeexpired'] == true) {
                    $data['videoStartStatus']  = false;
                    $data['commentOpenStatus'] = true;
                    $data['invitationType']    = 'protected';
                    $data['eventJoinLinkHide'] = true;
                    $data['expireInSeconds']   = $getArray['expireInSeconds'];
                    $data['removeVideoLayer']  = true;
                    $data['timeexpired']       = true;
                } else if ($getArray['expireInSeconds'] == 0) {
                    $data['eventJoinLinkHide'] = true;
                    $data['videoStartStatus']  = false;
                    $commentOpenStatus         = ($row['commentduring'] == 1) ? true : false;
                    $data['commentOpenStatus'] = $commentOpenStatus;
                    $data['lapsTime']          = 0;
                    $data['removeVideoLayer']  = false;
                    $data['expireInSeconds']   = $getArray['differenceInSeconds'];
                    $data['timeexpired']       = false;
                } else if ($getArray['timeexpired'] == true) {
                    $data['eventJoinLinkHide'] = true;
                    $data['videoStartStatus']  = false;
                    $data['commentOpenStatus'] = true;
                    $data['lapsTime']          = 0;
                    $data['removeVideoLayer']  = true;
                    $data['timeexpired']       = true;
                } else if ($getArray['timeexpired'] == false) {
                    $data['eventJoinLinkHide'] = true;
                    $data['videoStartStatus']  = true;
                    $commentOpenStatus         = ($row['commentduring'] == 1) ? true : false;
                    $data['commentOpenStatus'] = $commentOpenStatus;
                    $data['lapsTime']          = $getArray['expireInSeconds'];
                    $data['removeVideoLayer']  = true;
                    $data['timeexpired']       = false;
                }
            }
        }
        return $response->setBody(Zend_Json::encode($data));
    }

    
    
    public function surveyfinishAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $stringSurvey = $this->_request->getPost('stringSurvey');
            $dbeeid       = $this->_request->getPost('dbeeid');
            $explode      = explode(',', $stringSurvey);
            foreach ($explode as $value) {
                
                $explodeArray = explode('_', $value);
                $where        = array(
                    'id' => $explodeArray[2]
                );
                $ansdeatil    = $this->myclientdetails->getRowMasterfromtable('tblSurveyquestion', array(
                    'correct_answer'
                ), $where);
                
                $IsAnswerCorrect = $ansdeatil['correct_answer'];
                
                
                $dataResponse['dbeeid']          = $explodeArray[0];
                $dataResponse['question_id']     = $explodeArray[1];
                $dataResponse['answer_id']       = $explodeArray[2];
                $dataResponse['IsAnswerCorrect'] = $IsAnswerCorrect;
                $dataResponse['UserID']          = $this->_userid;
                $dataResponse['surveyTime']      = date('Y-m-d H:i:s');
                $dataResponse['clientID']        = clientID;
                $id                              = $this->myhome_obj->insertServeyAnswer($dataResponse);
            }
            $this->notification->commomInsert(19, 28, $dbeeid, $this->_userid, adminID);
            
            $row = $this->myhome_obj->getdbeedetail($dbeeid);
            if ($row['surveyPdf'] != '') {
                $stringSurvey = '<div class="surveyComplete">
                                    <a href="' . BASE_URL . '/dbeedetail/downloadpdf/filepdf/' . $row['surveyPdf'] . '">
                                        <img src="' . BASE_URL . '/img/pdficon.png" class="filePdfDwn" />
                                        <div class="pdfDwnCnt">
                                            Survey successfully completed!
                                            <i>Please download your PDF here</i>
                                        </div>
                                    </a>
                                </div>';
            } else if ($row['surveyLink'] != '') {
                $stringSurvey = '<div class="surveyComplete">
                    <a href="' . $this->commonmodel_obj->addhttp($row['surveyLink']) . '" target="_blank">                    
                    <span class="fa-stack fa-2x pull-left">
                      <strong class="fa fa-circle-thin fa-stack-2x"></strong>
                      <strong  class="fa fa-check fa-stack-1x "></strong>
                    </span>
                    <div class="pdfDwnCnt">
                    Survey successfully completed!<br/>
                    ' . $row['surveyLink'] . '
                    </div>
                    </a>
                    </div>';
            } else {
                $stringSurvey = '<div class="surveyComplete">
                                              <span class="pull-left"> 
                                                <span class="fa-stack fa-2x">
                                                  <strong class="fa fa-circle-thin fa-stack-2x"></strong>
                                                  <strong  class="fa fa-check fa-stack-1x "></strong>
                                                </span>
                                               Survey successfully completed!

                                               </span>
                                </div>';
            }
            $data['status']  = 'success';
            $data['content'] = $stringSurvey;
        }
        return $response->setBody(Zend_Json::encode($data));
        
    }
    
    public function downloadpdfAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $pdfName = $this->_request->getParam('filepdf');
        $file    = $this->config->file->frontpublicpath . "surveydoc/" . $pdfName;
        if (file_exists($file) && $pdfName != '' && filesize($file) != 0) {
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file) . '"');
            header('Content-Length: ' . filesize($file));
            readfile($file);
        } else {
            $this->_redirect('/');
        }
    }
    
    public function checkEventRedirecttion($id)
    {
        $eventModel  = new Application_Model_Event();
        $eventResult = $eventModel->getEvent($id);
        if($this->_userid)
        $memberRow   = $eventModel->checkEventMember($this->_userid, $id);
        if ((empty($memberRow) && $this->_userid!='' && $eventResult[0]['type'] == 3) || ($this->_userid=='' && $eventResult[0]['type'] == 3))
            $this->_redirect('/');
    }

    public function checkGroupPostRedirecttion($data)
    {
       $grouptypes     = new Application_Model_Groups();
       $groupdetails   = $grouptypes->selectgroup($data['GroupID']);
       if($groupdetails[0]['User']!=$this->_userid && $this->_userid!='')
       {
            $dataM = $grouptypes->checkgroupmem($data['GroupID'],$this->_userid);
            if(empty($dataM))
                 $this->_redirect('/');
       }else if($this->_userid=='')
            $this->_redirect('/');
    }

    public function getSocialInvite($type,$dbeeid)
    {
        switch ($type) {
            case 'facebook':
                $fbjoinwherearr=array('dbeeid' => $dbeeid,'type' =>'facebook');
                $fbjoineddata=$this->myclientdetails->getAllMasterfromtable('tblspecialdbinvite','socialid',$fbjoinwherearr);
                $fbjoineduser=array();          
                if(count($fbjoineddata) > 0)
                {            
                 foreach ($fbjoineddata as $key => $value) {
                     $fbjoineduser[] = $value['socialid'];
                 }             
                }
                return $fbjoineduser;
                break;
            case 'twitter':
                $twjoinwherearr=array('dbeeid' => $dbeeid,'type' =>'twitter');
                  $twjoineddata=$this->myclientdetails->getAllMasterfromtable('tblspecialdbinvite','socialid',$twjoinwherearr);
                
                  $twjoineduser=array();          
                  if(count($twjoineddata) > 0)
                  {            
                     foreach ($twjoineddata as $key => $value) {
                         $twjoineduser[] = $value['socialid'];
                     }             
                  }
                  return $twjoineduser;
                break;
            default:
              $joinwherearr=array('dbeeID' => $dbeeid);
              $joineddata=$this->myclientdetails->getAllMasterfromtable('tblDbeeJoinedUser','userID',$joinwherearr);
              $joineduser=array();          
              if(count($joineddata) > 0)
              {            
                 foreach ($joineddata as $key => $value) {
                     $joineduser[] = $value['userID'];
                 }             
              }
              return $joineduser;
             break;
        }
    }

    public function homeAction()
    {
       
        if ($this->Social_Content_Block == 'block' && $this->_userid == "")
        {
            $this->_redirect('/');            
        }

        $dbeeid                             = '';
        $redirection_name_space             = new Zend_Session_Namespace('User_Session');
        $this->view->redirection_name_space = $redirection_name_space;
        $storage                            = new Zend_Auth_Storage_Session();
        $sessiondata                        = $storage->read();
        $filter                             = new Zend_Filter_StripTags();
        $request                            = $this->getRequest();
        $this->view->facebookloginUrl       = BASE_URL . '/index/facebook';
        $seo_title                          = trim($this->_request->getParam('title'));
        $this->view->seo_title              = $seo_title;
        $Expert                             = new Application_Model_Expert(); 
        
        
        if (empty($seo_title) && $SessionSocialUrl->redUrl!="") 
        {
            if ($this->_request->getParam('id') != '') 
            {
                $dbee_urltitles = $this->myhome_obj->getdburltitle_update($this->_request->getParam('id'), 1);
                if (!empty($dbee_urltitles))
                    $this->_redirect('/dbee/' . $dbee_urltitles);
            } else
                $this->_helper->redirector->gotosimple('myhome', 'index', true);
        }

        if ($this->_userid)
        {          
            $redirection_name_space->redirection = '';
            $SessionSocialUrl->redUrl='';
            unset($SessionSocialUrl->redUrl);
            Zend_Session::namespaceUnset('SocialUrl');           
        }

        if ($this->_userid == '')
            $redirection_name_space->redirection = BASE_URL . '/dbee/' . $seo_title;
        
        $dbeeid = (int) $this->_getIdbytitle($this->commonmodel_obj->makeSeo($seo_title));
        
        $checkdbeeexist = $this->myhome_obj->checkdbeeexist($dbeeid);

        if ($dbeeid) 
        {
              // updated by desh
              $this->view->twjoineduser = $this->getSocialInvite('twitter',$dbeeid);
              $this->view->fbjoineduser = $this->getSocialInvite('facebook',$dbeeid);
              $this->view->joineduser   = $this->getSocialInvite('',$dbeeid);
                /*  vip post */
    
            /* special db code here for attendies*/
            $sptype   = $filter->filter($this->_request->getParam('sptype'));
            $sptoken  = $filter->filter($this->_request->getParam('sptoken'));
            $spauthID = $filter->filter($this->_request->getParam('spauthid'));
            
            if($this->_userid && $checkdbeeexist[0]['eventtype'] == 2 && $checkdbeeexist[0]['Type'] == 6)
                $checkCurrentUserJoinedOrNot = $this->myhome_obj->checkUserOrNotJoined($this->_userid, $dbeeid);
            else
                $checkCurrentUserJoinedOrNot = array();

            if($sptoken != '' && $spauthID != '' && $checkdbeeexist[0]['eventtype'] == 2 && $checkdbeeexist[0]['Type'] == 6)
            { 
                $this->specialDbFeature($sptype, $sptoken, $dbeeid, $spauthID,$checkdbeeexist[0]); // internal process for special dbeee
                $this->view->special_dbee_session = $this->special_dbee_session;
            }else if (empty($checkCurrentUserJoinedOrNot) && $checkdbeeexist[0]['eventtype'] == 2 && $checkdbeeexist[0]['Type'] == 6)
                    $this->_redirect('/');
            else if($this->_userid == '' && $checkdbeeexist[0]['eventtype'] == 2 && $checkdbeeexist[0]['Type'] == 6)
                $this->_redirect('/');
            /* special db code here for attendies*/
           
          
            
            $type = $filter->filter($this->_request->getParam('type'));
            $invite = $filter->filter($this->_request->getParam('invite'));
            $token = $filter->filter($this->_request->getParam('token'));
            $oathtoken = $filter->filter($this->_request->getParam('oathtoken'));
            
            $redirection_name_space->oathtoken = $oathtoken;

            $this->view->showTwitterAcceptPopupLogout = false;
            $this->view->showTwitterAcceptPopup       = false;
            
            //  invite expert code start
            if ((isset($type) && !empty($type)) && (isset($token) && !empty($token)) && (isset($oathtoken) && !empty($oathtoken))) 
            {
                $checkType = $this->userSocialType($type); // get user social type
                $this->expertInviteCode($dbeeid, $token, $checkType, $checkdbeeexist[0]['expertuser'], $seo_title, $oathtoken, $type, $redirection_name_space);
            }else if ($checkdbeeexist[0]['Type'] == 9)
                $this->checkEventRedirecttion($checkdbeeexist[0]['events']); // for private event restriction
            else if($checkdbeeexist[0]['GroupID']!='' && $checkdbeeexist[0]['Privategroup']==1)
                $this->checkGroupPostRedirecttion($checkdbeeexist[0]); // for private group restriction
             //  invite expert code start
        }
            
        /**** check db existance ****/
        
        $this->view->checkdbeeexist = count($checkdbeeexist);
        $isdbactive                 = $checkdbeeexist[0]['Active'];
        $this->view->isdbactive     = $isdbactive;
        $this->view->isdbType       = $checkdbeeexist[0]['Type'];
        
        if ($isdbactive == 1) {
            
            if ($sessiondata['UserID']) {
                // Insert user in dbstats table for checking his social activity.
                $CurrDatestats = date('Y-m-d H:i:s');
                
                $socialType = ($sessiondata['Socialtype'] != '') ? $sessiondata['Socialtype'] : 0;
                
                $chkstatsexist = $this->dbeeCommentobj->chkstatsexist($dbeeid, $sessiondata['UserID'], $socialType);
                
                if ($chkstatsexist < 1) {
                    $dbstatsData = array(
                        'stats_dbid' => $dbeeid,
                        'stats_userid' => $sessiondata['UserID'],
                        'stats_type' => $socialType,
                        'stats_date' => $CurrDatestats,
                        'clientID' => clientID
                    );
                    $this->dbeeCommentobj->insertmention('tbldbstats', $dbstatsData);
                } else {
                    $dbstatsData = array(
                        'stats_date' => $CurrDatestats,
                        'clientID' => clientID
                    );
                    $this->dbeeCommentobj->updatedbstats('tbldbstats', $dbstatsData, $dbeeid, $sessiondata['UserID'], $socialType);
                }
                
                // Get all stats of dbee
                
                if ($sessiondata['UserID'] == $checkdbeeexist[0]['User']) {
                    $allstats             = $this->dbeeCommentobj->getfieldsfromstats($dbeeid, $socialType, $this->Social_Content_Block);
                    $this->view->dbvisits = $allstats;
                    
                    $this->view->imdbowner = $sessiondata['UserID'];
                }
                //End of dbstats functionality
            }
            
            
            /**** check db existance ****/
            $profileholder = $request->getpost('profileholder');
            $uid           = $this->_userid;
            $dbright       = $this->myhome_obj->getdbright($dbeeid);
            $editdbee      = $this->myhome_obj->gettype($dbeeid);
            
            /**** check my comments on this db ****/
            if ($this->_userid != '') {
                $cmntArr = $this->myclientdetails->getfieldsfromtable(array(
                    'CommentID'
                ), 'tblDbeeComments', 'UserID', $uid);
                $cmnt    = $cmntArr[0]['CommentID'];
            } else
                $cmnt = '';
            
            if ($cmnt != '' && $cmnt != '0')
                $mycomment = 1;
            else
                $mycomment = 0;
            
            $this->view->profileholder = ($dbright['User'] == $this->_userid) ? true : false;
            $this->view->Active        = $dbright['Active'];
            $this->view->GroupID       = $requestval['GroupID'];
            $this->view->dbeeid        = $dbeeid;
            $this->view->userid        = $uid;
            $this->view->mycomment     = $mycomment;
            $this->view->dbedit_type   = $editdbee;
            $this->view->dbright       = $dbright;
            
            $this->view->UserID = $this->_userid;
            
            // new update for profile index date 3-10-2013
            
            $loginid  = $this->_userid;
            $loggedin = true;
            if (!$this->_userid)
                $loggedin = false;
            if ($loggedin)
                $userloggedin = '1';
            else
                $userloggedin = '0';
            $polloption        = new Application_Model_Polloption();
            $this->view->poRes = $poRes = $polloption->getpolloption($dbeeid);
            
            
            $totalvotesexist = $polloption->totalpoll($dbeeid);
            if ($this->_userid)
                $dbeepollvote_all = $polloption->getmyvoteres($dbeeid, $this->_userid);
            
            $userid = $this->_userid;
            
            $row = $this->myhome_obj->getdbeedetail($dbeeid);
            
            $this->view->dbeedetail_row = $row;
            
            if ($loggedin) {
                $userdet    = new Application_Model_DbUser();
                $rowuserall = $userdet->userdetailall($userid);
            }
            $dbeeUser = $dbright['User'];
            
            // for expert remove button  added by desh 
            $this->view->dbeeOwner   = $dbeeUser;
            $this->view->LoginUserid = $this->_userid;
            // end expert remove button code
            if ($row['Attachment'] != '') {
                
                // $Attachlinkarray = explode(',', substr(trim($row['Attachment']), 0, -1));
                $Attachlinkarray = json_decode($row['Attachment'], true);
                //print_r($Attachlinkarray); 
                $Attachlink      = '<div class="pdfDetailsWrp"><div  class="paperClipICon"></div>';
                $iconexe = array (
                                    'xlsx'=>'excel',
                                    'xls'=>'excel',
                                    'doc'=>'word',
                                    'ppt'=>'powerpoint',
                                    'txt'=>'text',
                                    'jpeg'=>'image',
                                    'jpg'=>'image',
                                    'gif'=>'image',
                                    'png'=>'image',                             
                                    'WAVE'=>'audio',
                                    'mp3'=>'audio',
                                    'Ogg'=>'audio',
                                    'mp4'=>'video',
                                    'WebM'=>'video',
                                    'wmv'=>'video',
                                    'ogv'=>'video',
                                    'pdf'=>'pdf'
                            );
                foreach ($Attachlinkarray as $key => $Attachrow):
                    $exticon = '';
                    $ext = '';
                    $ext = pathinfo ( $Attachrow, PATHINFO_EXTENSION );
                    $exticon = $iconexe [$ext];
                    $exticon = ($exticon != '') ? $exticon : 'text';
                //$Attachlink .= '<div fid="' . $Attachrow . '" id="openpdflink"><a href="http://devadmin.db-csp.com/public/knowledgecenter/new 1/'. $Attachrow . '" fid="' . $Attachrow . '" ><i class="fa fa-file-pdf-o"></i> ' .$key. '</a></div>';
                    $Attachlink .= '<div fid="' . $Attachrow . '" id="openpdflink1"><a href="'.BASE_URL.'/dashboarduser/downloadpdfuser/pdf/' . $Attachrow . '"><i
                    class="fa fa-file-'. $exticon.'-o kcIconPdfList"></i> ' . $key . '</a></div>';
                endforeach;
                $Attachlink .= '<div class="clearfix"></div></div>';
            }
            
            $noTwitterFeed = '';
            
            if ($row['TwitterTag'] != '' && $this->Social_Content_Block != 'block') {
                $TwiTagval = '<span class="twitterlistingHas"><i class="fa dbTwitterIcon fa-lg"></i> ' . htmlentities($dbright['TwitterTag']) . '</span>';
            }
            
            $userStatus = $dbright['Status'];
            $imgH       = '130px';
            setcookie('dbforusrsrch', $dbeeid, time() + 3600);
            if ($dbeeid == '')
                $dbeeid = 0;
            $user   = $row['UserID'];
            $return = '';
            $video  = false;
            $audio  = false;
            $isPoll = 0;
            if ($userid == $dbeeUser) {
                $ProfileHolder = true;
            } else {
                $ProfileHolder = false;
            }
            if (!$ProfileHolder && $loggedin) {
                if ($this->followedby) {
                    $follow       = 1;
                    $followstring = "Unfollow";
                } else {
                    $follow       = 0;
                    $followstring = "Follow";
                }
            } else
                $follow = -1;
            if (strlen($ProfileUser) > 15)
                $smallfont = ' font-size:14px;';
            else
                $smallfont = '';
            
            // INITIALIZE SCORE DBEE DIV
            if ($this->_userid == $dbeeUser && $this->_userid)
                $this->view->invite = $invite;
            else
                $this->view->invite = '';
            
            
            $this->view->TotalComments = $TotalComments = $this->dbeeCommentobj->totacomment($dbeeid);
            
            // edit by desh make funtional code 
            $reportabuse = $this->showAbuseLink($dbeeid);
            
            $showLinks = false;
            if ($this->_userid)
                $showLinks = true;
            
            $userStatus = $row['Active'];
            
            if ($row['Active'] == 1 && $showLinks)
                $profileLinkStart = '<a href="/user/' . $this->myclientdetails->customDecoding($row['Username']) . '">';
            else if ($showLinks == false)
                $profileLinkStart = '<a href="javascript:void(0)" class="profile-deactivated" title="' . DEACTIVE_ALT . '" onclick="return false;">';
            
            if ($row['Clientdiscription'] != '')
                $readmore = '<div onClick="openpdflinkdetail(' . $row['DbeeID'] . ')" class="readmore">readmore</div>';
            
            $dbee_score_array = $this->getscore($userid, $dbeeid, $dbeeUser, $followstring, $TotalComments, $row);
            $scoreDiv         = $dbee_score_array[0];
            $scoreSmallIcon   = $dbee_score_array[2];
            $getSentiments    = $dbee_score_array[3];
            
            $totsenti        = $this->dbeeCommentobj->totalsentiments($dbeeid, 'common');
            $sentiRes        = $totsenti[0]['sentiment_polarity']; //exit;
            $overallpostmood = '';
            if ($sentiRes == 'positive') {
                $overallpostmood .= '<div class="overallPostMod"><a href="javascript:void(0);" rel="dbTip1" title="" class="dbee-feed-titlebar-smallFont" style="font-weight:normal">
                <i style="background: url(../images/positive.png) no-repeat; display:inline-block; width:30px; height:30px;vertical-align: middle;"></i>
                 </a> Positive</div>';
            } else if ($sentiRes == 'negative') {
                $overallpostmood .= '<div class="overallPostMod"><a href="javascript:void(0);"  rel="dbTip1" title="" class="dbee-feed-titlebar-smallFont" style="font-weight:normal" > 
                <i style="background: url(../images/negative.png) no-repeat; display:inline-block; width:30px; height:30px;   vertical-align: middle;"></i></a> Negative</div>';
            } else if ($sentiRes == 'neutral') {
                $overallpostmood .= '<div class="overallPostMod"><a href="javascript:void(0);"  rel="dbTip1" title=""  class="dbee-feed-titlebar-smallFont" style="font-weight:normal" >
                <i style="background: url(../images/neutral.png) no-repeat; display:inline-block; width:30px; height:30px;   vertical-align: middle;"></i></a> Neutral</div>';
            }
            
            if ($this->semantria_seen == 0 && $this->_userid != adminID) {
                $overallpostmood = '';
            }
            
            if ($row['Type'] == '5') {
                $totalvotesexist = $this->totalvotesexist;
                if ($this->_userid) {
                    $dbeepollvote_all = $polloption->getmyvoteres($dbeeid, $this->_userid);
                    
                }
                $myvoterow                           = $dbeepollvote_all[0];
                $globalArray['profileLinkStart']     = $profileLinkStart;
                $globalArray['myvoterow']            = $myvoterow;
                $globalArray['poRes']                = $poRes;
                $globalArray['userid']               = $this->_userid;
                $globalArray['id']                   = $dbeeid;
                $globalArray['row']                  = $row;
                $globalArray['loggedin']             = $loggedin;
                $globalArray['followstring']         = $followstring;
                $globalArray['groupname']            = $groupname;
                $globalArray['grplinkstart']         = $grplinkstart;
                $globalArray['grplinkend']           = $grplinkend;
                $globalArray['scoreDiv']             = $scoreDiv;
                $globalArray['dbee_content']         = $dbee_content;
                $globalArray['TotalComments']        = $TotalComments;
                $globalArray['reportabuse']          = $reportabuse;
                $globalArray['TwiTagval']            = $TwiTagval;
                $globalArray['scoreSmallIcon']       = $scoreSmallIcon;
                $globalArray['Social_Content_Block'] = $this->Social_Content_Block;
                $dbee_highlighted                    = $this->commonmodel_obj->dbeeprofiledetail2($globalArray);
            } else {
                $dbee_content       = '';
                $dbee_content_array = $this->getcontent($row['Type'], $row, $showLinks, $readmore, $TwiTagval, $Attachlink);
                $dbee_content       = $dbee_content_array[0];
            }
            
            if ($row['Type'] == '6') 
            {
                $twitter_connect_data                = Zend_Json::decode($this->session_data['twitter_connect_data']);
                $InvolveTwitte                       ='';
                $twitter                             = $twitter_connect_data['twitter_access_token'];
                $globalArray['profileLinkStart']     = $profileLinkStart;
                $globalArray['myvoterow']            = $myvoterow;
                $globalArray['poRes']                = $poRes;
                $globalArray['userid']               = $userid;
                $globalArray['surveyresult']         = $surveyresult;
                $globalArray['row']                  = $row;
                $globalArray['dbee_content']         = $dbee_content;
                $globalArray['loggedin']             = $loggedin;
                $globalArray['groupname']            = $groupname;
                $globalArray['grplinkstart']         = $grplinkstart;
                $globalArray['scoreDiv']             = $scoreDiv;
                $globalArray['getSentiments']        = $getSentiments;
                $globalArray['overallpostmood']      = $overallpostmood;
                $globalArray['TotalComments']        = $TotalComments;
                $globalArray['reportabuse']          = $reportabuse;
                $globalArray['Social_Content_Block'] = $this->Social_Content_Block;
                $globalArray['scoreSmallIcon']       = $scoreSmallIcon;
                $globalArray['TwiTagval']            = $TwiTagval;
                
                /*if(clientID!=55) { // HIDE INVOLVE TWITTER USERS BUTTON ON AMERICAN GOLF
                    if ($this->socialloginabilitydetail['allSocialstatus'] == 0 && $this->socialloginabilitydetail['Twitterstatus'] == 0) {
                        if ($this->_userid) 
                        {
                            if (!empty($twitter)) // check twitter logined or not
                            {
                                $InvolveTwitte  = '<a href="javascript:void(0);" class="btn btn-twitter btn-mini" id="twittersphere">Involve Twitter users</a><div class = "asktoquestion"></div>';
                            } else {
                                //   $dbee_content.=' <a href="javascript:void(0);" class="btn btn-twitter btn-mini" id="twittersphere">Twittersphere</a> ';
                             $InvolveTwitte           = '<a class="btn btn-twitter btn-mini connectToTwitter" data-fromplace="sphere" data-id="' . $row['DbeeID'] . '" href="javascript:void(0);">Involve Twitter users</a> ';
                            }
                        }
                    }
                }*/
                $dbee_highlighted = $this->commonmodel_obj->dbeeprofiledetailSpecial($globalArray,$InvolveTwitte);
            } else if ($row['Type'] == '7') {
                if ($this->_userid)
                    $surveyresult = $this->myhome_obj->surveyCheckStatus($dbeeid, $this->_userid);
                else
                    $surveyresult = array();
                $globalArray['profileLinkStart'] = $profileLinkStart;
                $globalArray['surveyresult']     = $surveyresult;
                $globalArray['rowData']          = $row;
                $globalArray['dbee_content']     = $dbee_content;
                $globalArray['loggedin']         = $loggedin;
                $dbee_highlighted                = $this->commonmodel_obj->dbeeprofiledetailType7($globalArray, $dbeeid, $this->_userid);
                
            } else if ($row['Type'] != '5') {
                $globalArray['profileLinkStart']     = $profileLinkStart;
                $globalArray['surveyresult']         = $surveyresult;
                $globalArray['row']                  = $row;
                $globalArray['dbee_content']         = $dbee_content;
                $globalArray['loggedin']             = $loggedin;
                $globalArray['groupname']            = $groupname;
                $globalArray['grplinkstart']         = $grplinkstart;
                $globalArray['scoreDiv']             = $scoreDiv;
                $globalArray['getSentiments']        = $getSentiments;
                $globalArray['overallpostmood']      = $overallpostmood;
                $globalArray['dbee_content']         = $dbee_content;
                $globalArray['TotalComments']        = $TotalComments;
                $globalArray['reportabuse']          = $reportabuse;
                $globalArray['Social_Content_Block'] = $this->Social_Content_Block;
                $globalArray['scoreSmallIcon']       = $scoreSmallIcon;
                $globalArray['TwiTagval']            = $TwiTagval;
                $globalArray['adminpostscore']       = $this->adminpostscore;
                $globalArray['socialloginabilitydetail']       = $this->socialloginabilitydetail;
                
                $dbee_highlighted                    = $this->commonmodel_obj->dbeeprofiledetail($globalArray);
            }
        }
        $this->view->dbee_highlighted = $dbee_highlighted;
    }
    /* Expert code here
     * @row which has dbee result
     */
    public function expertInviteCode($dbeeid, $token, $checkType, $expertArray, $seo_title, $oathtoken, $type, $redirection_name_space)
    {
        $checkinviteexport = $this->myhome_obj->checkinviteexpert($dbeeid, $token);
        $expertlist = explode(',', $expertArray);

        if ($checkinviteexport == 0) 
        {
            if (!in_array($this->_userid,$expertlist)) 
            {
                $redirection_name_space->unsetAll();
                $this->_redirect('dbee/' . $seo_title . '?used=1');
                $redirection_name_space->showTwitterAcceptPopup = false;
            } else 
            {
                if ($this->session_data['Socialtype'] != $checkType && !in_array($this->_userid,$expertlist)) 
                {       
                    $this->_redirect('dbee/' . $seo_title . '?used=1');
                    $redirection_name_space->unsetAll();
                    $redirection_name_space->showTwitterAcceptPopup = false;
                }
                $this->_redirect('dbee/' . $seo_title.'?used=1');
                $redirection_name_space->showTwitterAcceptPopup = false;
            }
        } 
        else 
        {
            if ($redirection_name_space->oathtoken == $this->_userid && $this->_userid != '') 
            {
                $this->view->showTwitterAcceptPopup             = true;
                $redirection_name_space->token                  = $token;
                $redirection_name_space->oathtoken              = $oathtoken;
                $redirection_name_space->showTwitterAcceptPopup = true;
                $redirection_name_space->redirection            = '';
                $redirection_name_space->dbeeid                 = $dbeeid;
                $redirection_name_space->type                   = $type;
            } 
            else if ($this->session_data['Socialtype'] == $checkType && $this->_userid != '' && ($redirection_name_space->oathtoken == $this->session_data['Socialid'])) 
            {
                $this->view->showTwitterAcceptPopup             = true;
                $redirection_name_space->token                  = $token;
                $redirection_name_space->oathtoken              = $oathtoken;
                $redirection_name_space->showTwitterAcceptPopup = true;
                $redirection_name_space->redirection            = '';
                $redirection_name_space->dbeeid                 = $dbeeid;
                $redirection_name_space->type                   = $type;
                
            } 
            else if ($this->_userid != '' && $this->session_data['Socialtype'] == $checkType && $redirection_name_space->oathtoken != $this->session_data['Socialid']) 
            {
                $this->view->showTwitterAcceptPopupLogout       = true;
                $redirection_name_space->showTwitterAcceptPopup = false;
                $redirection_name_space->redirectToExpertPage   = BASE_URL . '/dbee/' . $seo_title . '?type=' . $type . '&token=' . $token . '&oathtoken=' . $oathtoken;
                $this->view->typeExpert                         = $type;
                
            }
            else if ($this->_userid != '' && $this->session_data['Socialtype'] != $checkType) 
            {
                $this->view->showTwitterAcceptPopupLogout       = true;
                $redirection_name_space->showTwitterAcceptPopup = false;
                $redirection_name_space->redirectToExpertPage   = BASE_URL . '/dbee/' . $seo_title . '?type=' . $type . '&token=' . $token . '&oathtoken=' . $oathtoken;
                $this->view->typeExpert                         = $type;
                
            } else if ($this->_userid != '') 
            {
                $redirection_name_space->redirection            = '';
                $redirection_name_space->showTwitterAcceptPopup = false;
                $redirection_name_space->unsetAll();
                $this->_redirect(BASE_URL . '/dbee/' . $seo_title . '?wrong=1');
            }
            if ($this->_userid == '') 
            {
                $redirection_name_space->dbeeid                  = $dbeeid;
                $redirection_name_space->type                    = $type;
                $redirection_name_space->token                   = $token;
                $redirection_name_space->oathtoken               = $oathtoken;
                $this->view->redirection_name_space_inviteexpert = $redirection_name_space;
                $redirection_name_space->redirection             = BASE_URL . '/dbee/' . $seo_title . '?type=' . $type . '&token=' . $token . '&oathtoken=' . $oathtoken;
            }
            
        }
    }
    
    /* show share icon content
     * @row which has dbee result
     */
    
    public function showShareLinkContent($row)
    {
        $social = '';
        if ($row['SocialFB'] != '' || $row['SocialTwitter'] != '' || $row['SocialLinkedin'] != '')
            $social .= '<div align="center" style="text-align:center; margin:10px 10px 0 0;"><div style="margin-bottom:5px; font-size:12px">My other social links</div><div class="next-line"></div>';
        if ($row['SocialFB'] != '')
            $social .= '<a href="' . $row['SocialFB'] . '" target="_blank"><div class="profilesocial-fb" style="margin-right:5px"></div></a>';
        if ($row['SocialTwitter'] != '')
            $social .= '<a href="http://twitter.com/' . $row['SocialTwitter'] . '" target="_blank"><div class="profilesocial-twitter" style="margin-right:5px"></div></a>';
        if ($row['SocialLinkedin'] != '')
            $social .= '<a href="' . $row['SocialFB'] . '" target="_blank"><div class="profilesocial-linkedin"></div></a>';
        if ($row['SocialFB'] != '' || $row['SocialTwitter'] != '' || $row['SocialLinkedin'] != '')
            $social .= '</div>';
        return $social;
        
    }
    /* get expert design div for type six template 
     * @param dbeeid
     * @ expertArray this is expert data array
     */
    public function getexpertspecialdbeeAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $expert_div  = '';
            $dbeeid      = (int) $this->_request->getPost('dbeeid');
            $expertArray = $this->myhome_obj->showDBeeExpert($dbeeid);
            if (!empty($expertArray)) {
                if ($this->_userid && $this->_userid != $expertArray['UserID'])
                    $expert_ask_question = '<a href="javascript:void(0)" class="askExpertBtn AskAnExpert" dbeeid ="' . $dbeeid . '" expertid ="' . $expertArray['UserID'] . '">Ask a question</a>';
                else
                    $expert_ask_question = '';
                $checkImage   = new Application_Model_Commonfunctionality();
                $userprofile1 = $checkImage->checkImgExist($expertArray['ProfilePic'], 'userpics', 'default-avatar.jpg');
                $expert_div .= '<div class="whiteBox todayGuestSpeaker">
                    <h2>Todays ' . $this->expertText . '</h2>
                    <div class="rboxContainer">
                        <h3 class="oneline">' . $this->myclientdetails->customDecoding($expertArray['Username']) . '</h3>';
                if ($expertArray['title'] != '')
                    $expert_div .= '<h4 class="oneline">' . $this->myclientdetails->customDecoding($expertArray['title']) . '</h4>';
                
                if ($expertArray['company'] != '')
                    $expert_div .= '<h4 class="oneline">' . $this->myclientdetails->customDecoding($expertArray['company']) . '</h4>';
                
                $expert_div .= '<div class="picGuestSpeaker">
                           <a href="/user/' . $this->myclientdetails->customDecoding($expertArray['Username']) . '">
                            <img border="0" src="'.IMGPATH.'/users/' . $userprofile1 . '" width="176" height="176">
                            </a>
                        </div>
                        <div class="btnAskQuestion">
                          ' . $expert_ask_question . '
                        </div>
                    </div>
                  </div>';
                $data['expert'] = true;
            } else
                $data['expert'] = false;
            
            $data['status']     = 'success';
            $data['expert_div'] = $expert_div;
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    
    public function showAbuseLink($dbeeid)
    {
        return '<div class="dropDown invtExpert repDeBottomLine" dbid="'.$dbeeid.'" style="display:inline-block;"></div><div class="dropDown repDeBottomLine" style="display:inline-block;">
        <a href="javascript:void(0)">Report</a>
        <ul class="dropDownList right" style="width:140px;">
        <li><a href="javascript:void(0);" class="report-abusetwo " dbid="' . $dbeeid . '" type="1"  style="font-weight:normal" >Report abuse</a></li>
        <li><a href="javascript:void(0);" id="report-abuse" dbid="' . $dbeeid . '"  style="font-weight:normal" >Report a bug</a></li>
        </ul>
        </div>';
    }

 
    public function UserDbeeGroupDbeeInviteRestriced($result)
    {
        
        if ($result['GroupID'] != '') {
            $groupResult = $this->groupModel->groupuserdetail($result['GroupID']);
            if ($groupResult[0]['Invitetoexpert'] == 1)
                return $groupResult[0]['UserID'];
            else
                return false;
        } else
            return false;
    }
    public function expertquestionAction()
    {
        $response = $this->_helper->layout->disableLayout();
        if ($this->getRequest()->getMethod() == 'POST' && $this->getRequest()->isXmlHttpRequest()) {
            $dbeidfromexpert    = (int) $this->_request->getPost('dbid');
            $expertfromexpert   = (int) $this->_request->getPost('expertid');
            $Expert             = new Application_Model_Expert();
            $this->view->result = $Expert->getAllExpertQuestion($expertfromexpert, $dbeidfromexpert);
        }
    }
    public function indexAction()
    {
        /*ini_set('display_errors',1);
error_reporting(E_ALL|E_STRICT);
ini_set('error_log','script_errors.log');
ini_set('log_errors','On');*/
        $request  = $this->getRequest();
        $CurrDate = date('Y-m-d H:i:s');
        $groupid = $request->getPost('GroupID');
        $id      = $request->getPost('db');
        
        $loginid     = $this->_userid;
        $groupidArr  = $this->myclientdetails->getfieldsfromtable(array(
            'GroupID',
            'events'
        ), 'tblDbees', 'DbeeID', $id);
        $groupid     = $groupidArr[0]['GroupID'];
        $eventid     = $groupidArr[0]['events'];
        /**** check db existance ****/
        $checkdbeeexist = $this->myhome_obj->checkdbeeexist($id);
        /**** check db existance ****/
        if (count($checkdbeeexist) > 0) {
            $this->view->checkdbeeexist = count($checkdbeeexist);
            /****  check db existance  ****/
            $this->view->Active         = $checkdbeeexist[0]['Active'];
            $userid                     = $this->myhome_obj->getuserid($id);
            $polloption                 = new Application_Model_Polloption();
            $editdbee                   = $this->myhome_obj->gettype($id);
            if ($this->_userid)
                $dbeepollvote_all = $polloption->getmyvoteres($id, $loginid);
            $result          = $this->dbeeCommentobj->getdbeecomment($id);
            $followinfinsert = new Application_Model_Following();
            
            if ($loginid) {
                $scoringRow   = $this->profile_obj->getmydbscore($id, $loginid, 'topscore');
                $chkfollowing = (int) $followinfinsert->chkfollowing($userid, $loginid);
                $nottifyemail = $this->dbeeCommentobj->notifyemail($id, $loginid);
                $blockuser    = $this->dbeeCommentobj->blockuser($loginid, $id);
            }
            if (count($scoringRow) > 0) {
                if ($scoringRow['Score'] == '1')
                    $myDBScore = 'love-dbee';
                if ($scoringRow['Score'] == '2')
                    $myDBScore = 'like-dbee';
                if ($scoringRow['Score'] == '3')
                    $myDBScore = 'philosopher-dbee';
                if ($scoringRow['Score'] == '4')
                    $myDBScore = 'dislike-dbee';
                if ($scoringRow['Score'] == '5')
                    $myDBScore = 'hate-dbee';
            }
            $this->view->row           = $result;
            $this->view->TotalComments = $this->dbeeCommentobj->totacomment($id);
            
            $getComment                   = $this->dbeeCommentobj->getcomment($id);
            $this->view->row_comment      = $getComment;
            $this->view->NewTotalComments = count($getComment);
            $this->view->poRes            = $polloption->getpolloption($id);
            $this->view->NotifyEmail      = $nottifyemail[0]['NotifyEmail'];
            $this->view->totalvotesexist  = $polloption->totalpoll($id);
            $this->view->myvoterow        = $dbeepollvote_all[0];
            $this->view->userid           = $userid;
            $this->view->db               = $id;
            $this->view->EventID          = $eventid;
            $this->view->GroupID          = $groupid;
            $this->view->loginid          = $loginid;
            $this->view->followedby       = $chkfollowing;
            $this->view->myDBScore        = $myDBScore;
            $this->view->blockusercount   = $blockuser;
            $this->view->dbedit_type      = $editdbee;
            $response                     = $this->_helper->layout->disableLayout();
            return $response;
        } else {
            $this->view->checkdbeeexist = count($checkdbeeexist);
        }
    }
    function getHashTags($text)
    {
        //Match the hashtags
        preg_match_all('/(^|[^a-z0-9_])#([a-z0-9_]+)/i', $text, $matchedHashtags);
        $hashtag = '';
        // For each hashtag, strip all characters but alpha numeric
        if (!empty($matchedHashtags[0])) {
            foreach ($matchedHashtags[0] as $match) {
                $hashtag .= preg_replace("/[^a-z0-9]+/i", "", $match) . ",";
            }
        }
        //to remove last comma in a string
        return rtrim($hashtag, ',');
    }
    public function castvoteAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->_helper->layout->disableLayout();
        $request  = $this->getRequest();
        $vote     = $request->getPost('vote');
        $dbid     = $request->getPost('dbid');
        if ($vote == '')
            echo die('0');
        
        $polloption = new Application_Model_Polloption();
        
        $poll        = $request->getPost('poll');
        $pollcomment = $request->getPost('pollcomment');
        
        $pollResult = $polloption->getmyvoteres($poll, $this->_userid);
        if (empty($pollResult)) 
        {
            $hashTag  = $this->getHashTags($pollcomment);
            $loginid  = $this->_userid;
            $Type     = 1;
            $result   = $this->dbeeCommentobj->getdbeecomment($poll);
            $getdbee  = $this->myhome_obj->getDbeeDetails($poll);
            $VoteDate = date('Y-m-d H:i:s');
            $data     = array(
                'PollID' => $poll,
                'User' => $loginid,
                'Vote' => $vote,
                'VoteDate' => $VoteDate,
                'clientID' => clientID
            );
            $success  = $polloption->insertpoll($data);
           
            if($getdbee['User']==adminID)
            {
              $this->notification->commomInsert('44', '54', $dbid, $this->_userid, adminID);
            }else
            {
              $this->notification->commomInsert('1', '33', $dbid, $this->_userid, $getdbee['User'], '', $success);   
            }

            // Insert for involve activity
            $id = $polloption->getinsertid();
            if ($pollcomment != '') 
            {
                $data    = array(
                    'DbeeID' => $poll,
                    'DbeeOwner' => $getdbee['User'],
                    'UserID' => $loginid,
                    'Type' => $Type,
                    'VoteID' => $vote,
                    'Comment' => $pollcomment,
                    'CommentDate' => $VoteDate,
                    'clientID' => clientID,
                    'DbTag' => $hashTag
                );
                $success = $polloption->insertpollcomment($data);
            }
            $votenaobj = $polloption->getopname($vote);
            $votename  = $votenaobj[0]['OptionText'];
            if ($success) 
            {
                $SubmitMsg = 1;
                $data2     = array(
                    'LastActivity' => $VoteDate
                );
                $this->myhome_obj->updatedbee($data2, $poll);
                $totalvotesexist = $polloption->totalpoll($poll);
                $count           = 1;
                $pres            = $polloption->getpores($poll);
                foreach ($pres as $prow):
                    $total                        = $polloption->getpolloptionvote($poll, $prow['ID']);
                    ${'polloptid' . $count}       = $prow['ID'];
                    ${'pollopt' . $count}         = $prow['OptionText'];
                    ${'pollopt' . $count . 'num'} = $total[0]['cnt'];
                    $count++;
                endforeach;
                $return = '<div align="center"><div class="medium-font-bold-grey">you voted</div><div class="large-font-orange">' . $votename . '</div></div>';
            }
        } else {
            echo die('3');
        }
        $SubmitMsg = 1;
        echo $SubmitMsg . '~#~' . $return . '~#~' . $votename . '~#~' . $pollopt1 . '~#~' . $pollopt2 . '~#~' . $pollopt3 . '~#~' . $pollopt4 . '~#~' . $pollopt1num . '~#~' . $pollopt2num . '~#~' . $pollopt3num . '~#~' . $pollopt4num . '~#~' . $totalvotesexist . '~#~' . $vote . '~#~' . $polloptid1 . '~#~' . $polloptid2 . '~#~' . $polloptid3 . '~#~' . $polloptid4.'~#~'.$success;
        
    }
    
    
    
    
    public function invitefollowingAction()
    {
        $request = $this->getRequest()->getParams();
        //echo'<pre>';print_r($request);die;
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $group_model = new Application_Model_Groups();
        
        if ($this->getRequest()->getMethod() == 'POST') {
            $filter = new Zend_Filter_StripTags();
            
            $usertype = $filter->filter($this->_request->getPost('usertype'));
            $isvip = 0;
                if($this->session_data['usertype'] != 0){
                    $isvip = 1;
                }
            
            $return = '';
            
            if ($usertype == 'followers') {
                $allsqlquery = $group_model->getfollowersgrouptypes($this->_userid,'','',$isvip);
                
            } elseif ($usertype == 'following') {
                $allsqlquery = $group_model->getfollowinggrouptypes($this->_userid,'','',$isvip);
            }
            
            $TotalUsers = count($allsqlquery);
            if ($TotalUsers > 0) {
                $counter = 1;
                foreach ($allsqlquery as $key => $Row) {
                    $checkImage   = new Application_Model_Commonfunctionality();
                    $userprofile2 = $checkImage->checkImgExist($Row['ProfilePic'], 'userpics', 'default-avatar.jpg');
                    $return .= '<div class="boxFlowers" title="'.$this->myclientdetails->customDecoding($Row['Name']).'" ><label class="labelCheckbox"><input type="checkbox" name="inviteuser-' . $usertype . '" value="' . $Row['UserID'] . '" >
                    <div class="follower-box" >                  
                    <img src="'.IMGPATH.'/users/small/' . $userprofile2 . '"  width="50" height="50" border="0" /><br />
                    <div class="oneline">' . $this->myclientdetails->customDecoding($Row['Name']) . '</div></div>' . $inviteLabel . '</label></div>';
                    if ($counter % 7 == 0)
                        $return .= '<div class="next-line"></div>';
                    $counter++;
                }
                
            } else {
                $return .= '- No Users -';
                
            }
            $total = $counter - 1;
            echo $return . '~#~' . $total;
        }
    }
    
    public function invitesearchuserAction()
    {
        $request = $this->getRequest()->getParams();
        //echo'<pre>';print_r($request);die;
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $group_model = new Application_Model_Groups();
        if ($this->getRequest()->getMethod() == 'POST') {
            $filter      = new Zend_Filter_StripTags();
            $keyword     = $filter->filter($this->_request->getPost('keyword'));
            $return      = '';
            $vipuser     = $this->session_data['usertype'];
            $allsqlquery = $group_model->getusers($keyword,'','',$vipuser);
            $usertype    = 'search';
            $TotalUsers  = count($allsqlquery);
            if ($TotalUsers > 0) {
                $counter = 1;
                foreach ($allsqlquery as $key => $Row) {
                    $checkImage   = new Application_Model_Commonfunctionality();
                    $userprofile3 = $checkImage->checkImgExist($Row['ProfilePic'], 'userpics', 'default-avatar.jpg');
                    $return .= '<div class="boxFlowers" title="'.$this->myclientdetails->customDecoding($Row['Name']).'" ><label class="labelCheckbox">  <input type="checkbox" name="inviteuser-' . $usertype . '" value="' . $Row['UserID'] . '" ><div class="follower-box" >
                    <img src="'.IMGPATH.'/users/small/' . $userprofile3 . '" width="50" height="50" border="0" /><br /><div class="oneline">' . $this->myclientdetails->customDecoding($Row['Name']) . '</div></div>' . $inviteLabel . '</label></div>';
                    if ($counter % 7 == 0)
                        $return .= '<div class="next-line"></div>';
                    $counter++;
                }
            } else
                $return .= '- No Users -';
            
            $total = $counter - 1;
            echo $return . '~#~' . $total;
        }
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
    // Seo friendly url create function 
    function makeSeo($title, $raw_title = '', $context = 'display')
    {
        $myhome_obj = new Application_Model_Myhome();
        $dburl      = $this->getdbeeurl($title, '', $context);
        if ($myhome_obj->chkdbeetitle($dburl)) {
            return $dburl;
        } else {
            $words  = explode(' ', $title);
            $title2 = implode(' ', array_slice($words, 0, 14)) . '-' . date('i:s');
            
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
    
    
    private function _generateHash($plainText, $salt = null)
    {
        define('SALT_LENGTH', 9);
        if ($salt === null) {
            $salt = substr(md5(uniqid(rand(), true)), 0, SALT_LENGTH);
        } else {
            $salt = substr($salt, 0, SALT_LENGTH);
        }
        
        return $salt . sha1($salt . $plainText);
    }
    
    public function editdbeeAction()
    {
        $request              = $this->getRequest();
        $requestasr           = $this->getRequest()->getParams();
        $text                 = stripslashes($request->getPost('text'));
        $userlinkdesc         = stripslashes($request->getPost('userlinkdesc'));
        $picdesc              = stripslashes($request->getPost('picdesc'));
        $viddesc              = stripslashes($request->getPost('viddesc'));
        $dbeetype             = stripslashes($request->getPost('dbeetype'));
        $dbeeid               = (int) ($request->getPost('id'));
        $data['Text']         = stripslashes($request->getPost('text'));
        $data['UserLinkDesc'] = stripslashes($request->getPost('userlinkdesc'));
        $data['PicDesc']      = stripslashes($request->getPost('picdesc'));
        $data['VidDesc']      = stripslashes($request->getPost('viddesc'));
        $data['LastActivity'] = date('Y-m-d H:i:s');
        $data['LastEditDate'] = date('Y-m-d H:i:s');
        $success              = $this->myhome_obj->updatemydb($data, $dbeeid);
        if ($success)
            $SubmitMsg = 1;
        if ($dbeetype == '1')
            echo $SubmitMsg . '~#~' . str_replace("\'", "'", $text);
        elseif ($dbeetype == '2')
            echo $SubmitMsg . '~#~' . str_replace("\'", "'", $userlinkdesc);
        elseif ($dbeetype == '3')
            echo $SubmitMsg . '~#~' . str_replace("\'", "'", $picdesc);
        elseif ($dbeetype == '4')
            echo $SubmitMsg . '~#~' . str_replace("\'", "'", $viddesc);
        $response = $this->_helper->layout->disableLayout();
        return $response;
    }
    
    public function usersearchmentionAction()
    {
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $Profile = new Application_Model_Profile();
        $users   = $Profile->getTotalUsers($dbeeId);
        return $response->setBody(Zend_Json::encode($users));
    }
    
    public function usersearchAction()
    {
        $q       = strtolower($this->getRequest()->getParam('q'));
        $request = $this->getRequest();
        if (!$q)
            return;
        $return  = '';
        $users   = '';
        $counter = 1;
        $pos     = strpos($q, '@');
        if ($pos === false) {
            $search = $q;
        } else {
            $keyword = substr($q, $pos + 1);
            if (strlen($keyword) > 1) {
                $keywordArr = explode(',', $keyword);
                $index      = count($keywordArr) - 1;
                $search     = $keywordArr[$index];
            } else
                $search = -1;
        }
        $CommentUsers    = 0;
        $request         = $this->getRequest();
        $dbeeid          = $request->getCookie('dbforusrsrch');
        $CommentUsersres = $this->dbeeCommentobj->getalluser($dbeeid);
        if (count($CommentUsersres) > 0) {
            foreach ($CommentUsersres as $CommentUsersRow):
                $CommentUsers .= $CommentUsersRow['UserID'] . ',';
            endforeach;
        }
        if ($CommentUsers != 0)
            $CommentUsers = substr($CommentUsers, 0, -1);
        $Followers     = 0;
        $followuserobj = new Application_Model_Following();
        $followuser    = $followuserobj->getfollinguserserch($this->_userid);
        if (count($followuser) > 0) {
            foreach ($followuser as $Row) {
                $Followers .= $Row['FollowedBy'] . ',';
            }
        }
        if ($Followers != 0)
            $Followers = substr($Followers, 0, -1);
        $Following = 0;
        $foling    = $followuserobj->getfolloweruserserch($this->_userid);
        if (count($foling) > 0) {
            foreach ($foling as $Row):
                $Following .= $Row['User'] . ',';
            endforeach;
        }
        if ($Following != 0)
            $Following = substr($Following, 0, -1);
        
        $UserList = $CommentUsers . ',' . $Followers . ',' . $Following;
        $UserList = array(
            $this->unique($UserList)
        );
        $userlist = $this->dbeeCommentobj->getcommentusersearch($search, $UserList);
        if (count($userlist) > 0) {
            foreach ($userlist as $rs):
                if ($counter % 2 == 0)
                    $class = 'searchres-row-even';
                else
                    $class = 'searchres-row-odd';
                $checkImage   = new Application_Model_Commonfunctionality();
                $userprofile4 = $checkImage->checkImgExist($rs['ProfilePic'], 'userpics', 'default-avatar.jpg');
                $users .= '<div class="' . $class . '" style="padding:5px;"><div style="float:left; padding-bottom:5px;"><img src="'.IMGPATH.'/users/small/' . $userprofile4 . '" border="0" /></div><div style="float:left; margin-left:10px;">' . $rs['Name'] . '</div></div>|' . $rs['Name'] . '|' . $rs['UserID'] . '<div style="clear:both"></div>';
                $counter++;
            endforeach;
            if ($TotalUsers > 5)
                $users .= '<div unselectable="on" class="unselectable" style=" padding:10px 5px; text-align:center;"><a href="javascript:void(0);" onClick="javascript:seemoresearch(2);" style="color:#333">see more</a></div><div style="clear:both"></div>';
        }
        
        $return = $users;
        
        echo $return;
        $response = $this->_helper->layout->disableLayout();
        return $response;
    }
    function unique($list)
    {
        return implode(',', array_keys(array_flip(explode(',', $list))));
    }
    

    
    public function chknewcommentsAction()
    {
        
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->getMethod() == 'POST' && $this->getRequest()->isXmlHttpRequest()) {
            $Expert              = new Application_Model_Expert();
            $request             = $this->getRequest();
            $db                  = (int) $this->_request->getpost('db');
            $dbeeOwner           = (int) $this->_request->getpost('dbeeOwner');
            $dbid                = $db;
            /*  video code start */
            $row      = $this->myhome_obj->getdbeedetail($dbid);
            $getArray = $this->videoduration($row);
            if ($row['Type'] == 6 && $this->_userid) 
            {
                $userAttendStatus         = $this->myhome_obj->Userattendies($dbid, $this->_userid);
                $userAttendStatus2        = $this->myhome_obj->Userattendies2($dbid, $this->_userid);
                $data['LoginStatus']      = true;
                $data['userAttendStatus'] = $userAttendStatus;
                $data['videoLoadStatus']  = true; // video load condition
                
                if ($userAttendStatus == 0 && $row['eventtype'] == 0 && $getArray['expireInSeconds'] == 0) {
                    $data['videoStartStatus']  = false;
                    $data['commentOpenStatus'] = false;
                    $data['invitationType']    = 'public';
                    $data['eventJoinLinkHide'] = false;
                    $data['removeVideoLayer']  = false;
                    $data['expireInSeconds']   = $getArray['differenceInSeconds'];
                    $data['timeexpired']       = false;
                } else if ($userAttendStatus == 0 && $row['eventtype'] == 0 && $getArray['timeexpired'] == false) {
                    $data['videoStartStatus']  = false;
                    $data['commentOpenStatus'] = false;
                    $data['invitationType']    = 'public';
                    $data['eventJoinLinkHide'] = false;
                    $data['expireInSeconds']   = $getArray['expireInSeconds'];
                    $data['timeexpired']       = false;
                } else if ($userAttendStatus == 0 && $row['eventtype'] == 0 && $getArray['timeexpired'] == true) {
                    $data['videoStartStatus']  = false;
                    $data['commentOpenStatus'] = true;
                    $data['invitationType']    = 'public';
                    $data['eventJoinLinkHide'] = true;
                    $data['expireInSeconds']   = $getArray['expireInSeconds'];
                    $data['removeVideoLayer']  = true;
                    $data['timeexpired']       = true;
                } else if ($userAttendStatus == 0 && $row['eventtype'] == 1 && $getArray['expireInSeconds'] == 0) {
                    $data['videoStartStatus']  = false;
                    $data['commentOpenStatus'] = false;
                    $data['invitationType']    = 'protected';
                    $data['removeVideoLayer']  = false;
                    if ($userAttendStatus2 == 1)
                        $data['eventJoinLinkHide'] = true;
                    else
                        $data['eventJoinLinkHide'] = false;
                    $data['expireInSeconds'] = $getArray['differenceInSeconds'];
                    $data['timeexpired']     = false;
                } else if ($userAttendStatus == 0 && $row['eventtype'] == 1 && $getArray['timeexpired'] == false) {
                    $data['videoStartStatus']  = false;
                    $data['commentOpenStatus'] = false;
                    $data['invitationType']    = 'protected';
                    if ($userAttendStatus2 == 1)
                        $data['eventJoinLinkHide'] = true;
                    else
                        $data['eventJoinLinkHide'] = false;
                    $data['expireInSeconds'] = $getArray['expireInSeconds'];
                    $data['timeexpired']     = false;
                } else if ($userAttendStatus == 0 && $row['eventtype'] == 1 && $getArray['timeexpired'] == true) {
                    $data['videoStartStatus']  = false;
                    $data['commentOpenStatus'] = true;
                    $data['invitationType']    = 'protected';
                    $data['eventJoinLinkHide'] = true;
                    $data['expireInSeconds']   = $getArray['expireInSeconds'];
                    $data['removeVideoLayer']  = true;
                    $data['timeexpired']       = true;
                } else if ($getArray['expireInSeconds'] == 0) {
                    $data['eventJoinLinkHide'] = true;
                    $data['videoStartStatus']  = false;
                    $commentOpenStatus         = ($row['commentduring'] == 1) ? true : false;
                    $data['commentOpenStatus'] = $commentOpenStatus;
                    $data['lapsTime']          = 0;
                    $data['removeVideoLayer']  = false;
                    $data['expireInSeconds']   = $getArray['differenceInSeconds'];
                    $data['timeexpired']       = false;
                } else if ($getArray['timeexpired'] == true) {
                    $data['eventJoinLinkHide'] = true;
                    $data['videoStartStatus']  = false;
                    $data['commentOpenStatus'] = true;
                    $data['lapsTime']          = 0;
                    $data['removeVideoLayer']  = true;
                    $data['timeexpired']       = true;
                } else if ($getArray['timeexpired'] == false) {
                    $data['eventJoinLinkHide'] = true;
                    $data['videoStartStatus']  = true;
                    $commentOpenStatus         = ($row['commentduring'] == 1) ? true : false;
                    $data['commentOpenStatus'] = $commentOpenStatus;
                    $data['lapsTime']          = $getArray['expireInSeconds'];
                    $data['removeVideoLayer']  = true;
                    $data['timeexpired']       = false;
                }
            }
            /*  video code end */
            if ($row['Type'] == 5 && ($this->PollComments_On_Option == 2 || $this->PollComments_On_Option == 4 || $this->Is_PollComments_On == 0))
                $data['count'] = 0;
            
        }
        return $response->setBody(Zend_Json::encode($data));
        
    }
    public function sentimentsAction()
    {
        
        $request      = $this->getRequest()->getParams();
        $getcomments  = $this->dbeeCommentobj->sentimentscomment($request['dbid']);
        $initialTexts = array();
        $CommentID    = array();
        
        foreach ($getcomments as $key => $value) {
            if ($value['Type'] == 1) {
                if (empty($value['Comment']))
                    $initialTexts[] = 'DBEE';
                else
                    $initialTexts[] = $value['Comment'];
                
                $commentid[] = $value['CommentID'];
            } else if ($value['Type'] == 2) {
                if (empty($value['UserLinkDesc']))
                    $initialTexts[] = 'DBEE';
                else
                    $initialTexts[] = $value['UserLinkDesc'];
                $commentid[] = $value['CommentID'];
            } else if ($value['Type'] == 3) {
                if (empty($value['PicDesc']))
                    $initialTexts[] = 'DBEE';
                else
                    $initialTexts[] = $value['PicDesc'];
                
                $commentid[] = $value['CommentID'];
            } else if ($value['Type'] == 4) {
                if (empty($value['VidDesc']))
                    $initialTexts[] = 'DBEE';
                else
                    $initialTexts[] = $value['VidDesc'];
                
                $commentid[] = $value['CommentID'];
            } else if ($value['Type'] == 5) {
                if (empty($value['Comment']))
                    $initialTexts[] = 'DBEE';
                else
                    $initialTexts[] = $value['Comment'];
                $commentid[] = $value['CommentID'];
            }
        }
        
        $session = new \Semantria\Session(CONSUMER_KEY, CONSUMER_SECRET, NULL, NULL, TRUE);
        
        $callback = new SessionCallbackHandler();
        $session->setCallbackHandler($callback);
        
        foreach ($initialTexts as $text) {
            $doc    = array(
                "id" => uniqid(''),
                "text" => $text
            );
            $status = $session->queueDocument($doc);
            if ($status == 202) {
                
            }
        }
        // Count of the sample documents which need to be processed on Semantria
        $length  = count($initialTexts);
        $results = array();
        
        while (count($results) < $length) {
            sleep(10);
            $status = $session->getProcessedDocuments();
            if (is_array($status)) {
                $results = array_merge($results, $status);
            }
            
        }
        $counter   = 0;
        $chkupdate = '';
        
        $updArr = array();
        
        foreach ($results as $data) {
            $updArr = array(
                'sentiment_score' => $data["sentiment_score"],
                'sentiment_polarity' => $data["sentiment_polarity"]
            );
            
            $chkupdate = $this->myclientdetails->updatedata_global('tblDbeeComments', $updArr, 'CommentID', $commentid[$counter]);
            $counter++;
        }
        if ($chkupdate) {
            echo $_SERVER['HTTP_REFERER'];
        } else {
            echo '404';
        }
        exit;
    } // End of sentiment function
    
    protected function getcontent($type, $row, $showLinks, $readmore, $TwiTagval, $Attachlink)
    {
        
        $dbee_content = '';
        $checkImage   = new Application_Model_Commonfunctionality();
        
        if ($row['Type'] != 6)
            $dbee_content = '<div id="non-editable" class="moreViewTitle listTxtNew">' . nl2br($row['Text']) . $Attachlink . '</div>' . $rssfeed ;

    if ($row['Type'] == 6)
            $dbee_content = '<div id="non-editable" class="moreViewTitle listTxtNew">' . nl2br($row['Text']) .'</div>';

    $style='';
        
        if ($row['Vid'] != '' && $row['Pic'] != '')
            $popup = 'true';
        else
            $popup = 'false';
        
        if ($row['Vid'] != '' && $row['Type'] != 6) 
        {

            if($row['expertuser']!=$this->_userid)
            {
                if($row['Type']==15)
                {
                    $imglvb=BASE_URL.'/images/live_vid_image.png';

                    $dbee_content .= '<span id="RefreshYoutubespan">If the video hasn’t started for you, try clicking the ‘Refresh’ button</span> <a '.$style.' class="btn btn-mini" id="RefreshYoutube" href="javascript:void(0);">Refresh</a> ';
                }
                else if($row['VidSite']=='youtube')
                {
                     $imglvb='https://i.ytimg.com/vi/' . $row['VidID'] . '/0.jpg';
                }


         
                $dbeeVideoHTML   = '<div popup="' . $popup . '" class="youTubeVideoPostWrp">
                              <div class="youTubeVideoPost">
                              <a class="yPlayBtn" href="#"><i class="fa fa-play-circle-o fa-5x"></i></a>
                                  <a href="#">
                                      <img border="0" video-id="' . $row['VidID'] . '" src="'.$imglvb.'">
                                  </a>
                              </div>
                              <div class="ytDesCnt">
                                  <h2>' . $row['VidTitle'] . '</h2>
                                  <p>' . $this->myclientdetails->escape($row['VidDesc']) . '</p>
                              </div>
                          </div>';
            }
            if($row['Type']==15)
            {

                $dbeeVideoHTML   = '<p>' . $this->myclientdetails->escape($row['VidDesc']) . '</p>';
                $style='style="margin-top:20px;"';
            }
            if($row['VidSite']=='dbcsp')
            {
                $imglvb = BASE_URL . '/adminraw/knowledge_center/video_' . clientID . '/' . $row['Vid'] . '.png';
                $dbeeVideoHTML   = '<div  class="youTubeVideoPostWrp">
                              <div class="youVideoPost">
                              <a class="yPlayBtn" href="#"><i class="fa fa-play-circle-o fa-5x"></i></a>
                                  <div class="wistia_embed wistia_async_'.$row['VidID'].'" style="height:460px;">&nbsp;</div>
                              </div>
                              <div class="ytDesCnt">
                                  <h2>' . $row['VidTitle'] . '</h2>
                              </div>
                          </div>';
            }
            $dbeeVideoAndPic = $dbeeVideoHTML;
        }
        
        if ($row['Pic'] != '') {
            $OnlyPicClass = '';
            $popup        = 'true';
            if ($row['Vid'] == '' && $row['Link'] == '') {
                $OnlyPicClass = 'OnlyPicClass';
                $popup        = 'false';
            }
            
            if ($row['Pic'] != '' && $row['Link'] != '') {
                $OnlyPicClass = 'OnlyPicClass';
                $popup        = 'false';
            }

            
            $picName = $checkImage->checkImgExist($row['Pic'], 'imageposts', 'default-avatar.jpg');

            if ($row['PicSize'] > 800 || $row['Vid'] != '')
                $imgsrc = '/imageposts/medium/' . $picName;
            else
                $imgsrc = '/imageposts/medium/' . $picName;

            $picdata = $this->myclientdetails->passSQLquery("SELECT picName FROM tblDbeePics WHERE clientID='" . clientID . "' AND isDbeeDefaultPic='0'  AND     reff_key_id =" . $row['DbeeID']);
             $morepic="";
                $cnt = count($picdata);
                $picTemplate = '';
                $picTemplateClass = 'postImgThumb';
                               
                if($cnt == 0){
                    $morepic.= '<img popup="true" popup-image="' . htmlentities($picName) . '" src="' . IMGPATH . '/imageposts/'. $picName.'" />';
                }else{
                     $morepic.= '<div style="background:url(' . IMGPATH . '/' . $imgsrc . ') no-repeat; background-size: cover;" class="postImgThumb" popup="true" popup-image="' . htmlentities($picName) . '"></div>';
                }
                 
                if($cnt>0){
                     foreach ($picdata as $key => $value) {
                        $morepic.='<div popup="true" popup-image="' . htmlentities($value['picName']) . '"  style="background:url(' . IMGPATH . '/imageposts/medium/'.htmlentities($value['picName']).') no-repeat; background-size: cover;" class="'.$picTemplateClass.'"> </div>';
                    }
                    $picTemplate ='<div class="multImgWrp imglayout_'.($cnt+1).'">'.$morepic.'</div>';
                }else{
                    $picTemplate = $morepic;
                }

            $dbeePicHTML     = '<div class="pixPostWrp ' . $OnlyPicClass . '">'.$picTemplate.'</div>';
            $dbeeVideoAndPic = $dbeePicHTML;
        }
        
        if ($row['Vid'] != '' && $row['Pic'] != '')
            $dbeeVideoAndPic = '<div class="pix picMediaComboWrp">
                          ' . $dbeeVideoHTML . $dbeePicHTML . '
                        </div>';
        
        $dbee_content .= $dbeeVideoAndPic;
        
        if ($row['Link'] != '') {
            
           
            $dbee_content .= '<div class="makelinkWrp ">
                                <div class="makelinkDes">
                                  <h2>' . $row['LinkTitle'] . '</h2>
                                  <div class="desc">' . $row['LinkDesc'] . '</div>
                                  <div class="makelinkshw">
                                    <a href="' . $row['Link'] . '" target="_blank">
                                    ' . $row['Link'] . '</a>
                                  </div>
                                </div>
                            </div>';
        }
        
        if ($row['RssFeed'] != '')
            $dbee_content .= '<div class="dbRssWrapper dbRssWrapperMain" style=" display:block; margin:10px 0 10px 0">' . $row['RssFeed'] . '</div>';
        
        
        if ($type == 7)
            $dbee_content = '<div id="non-editable" style="margin-bottom:15px">' . nl2br(strip_tags($row['surveyTitle'], '<a>')) . '</div>';
        
        $rajxx = strip_tags($dbee_content);
        
        if ($row['DbTag'] != '') {
            $tagArray = explode(',', $row['DbTag']);
            foreach ($tagArray as $value)
                $tag_content .= '#' . $value . ' ';
        }
        
        $dbee_content         = $dbee_content . $this->dbeeCommentobj->convert_clickable_links_Second($tag_content);
        $twitter_connect_data = Zend_Json::decode($this->session_data['twitter_connect_data']);
        $twitter              = $twitter_connect_data['twitter_access_token'];
        
        
        $expertuser = $row['expertuser'];
        $VidID      = $row['VidID'];
        

            if($row['Type']==15)
            {
                $style='style="margin-top:20px;"';
                $pos = strpos($row['eventzone'], '-');
                if ($pos === false) {
                   $opra='+';
                } else {
                    $opra='-';
                }
                if($row['eventzone']='4.4')
                {
                    $timezone='3.4';
                }
                else
                {
                    $timezone=$row['eventzone'];
                }
                  
                $timezone=str_replace('-', '', $timezone);
                $timezoneslice=explode('.', $timezone);
                $minutes=$timezoneslice[1]*6;
                $eventstart=date('Y-m-d H:i',strtotime($opra.$timezoneslice[0].' hour '.$opra.$minutes.' minutes',strtotime($row['eventstart'])));
                
                $datetimeslice=explode(' ', $eventstart);
                $date=$datetimeslice[0];
                $dateslice=explode('-', $date);
                $time=$datetimeslice[1];
                $timeslice=explode(':', $time);
                $buttons = '<button data-token="'.$row["Text"].'" style="display:none;" disabled="disabled" data-video-id="'.$row["VidID"].'"  type="button" id="HangoutButton2" class="yt-uix-button yt-uix-button-size-small yt-uix-button-default start-hangouts-on-air vm-video-info-button-control btn btn-yellow btn-mini"><span class="yt-uix-button-content">Initiate Broadcasting</span></button>';
                $msgPopup = '<div class="BroadCastingMsg">Please login to Google using the following details: <br>Email: <strong>'.GoogleEmail.'</strong><br>Password: <strong>'.GooglePassword.'</strong><br><br><i>Please note you will need to logout of any current Google session.</i></div>';

                $msgPopup1 = '<div class="BroadCastingMsg">Please click the button below to start broadcasting.</div>';

                $msgPopup2 = '<div class="BroadCastingMsg">Please login to Google using the following details: <br>Email: <strong>'.GoogleEmail.'</strong><br>Password: <strong>'.GooglePassword.'</strong><br><br><i>Please note you will need to logout of any current Google session.</i><br><a href="https://accounts.google.com" target="_blank" ><img src="../img/go-googgle.jpg" /></a> </div>';

                ?>
                <script type="text/javascript">
                var current='';    //-->enter what you want the script to display when the target date and time are reached, limit to 20 characters
                var year=2010;    //-->Enter the count down target date YEAR
                var month=12;      //-->Enter the count down target date MONTH
                var day=21;       //-->Enter the count down target date DAY
                var hour=18;      //-->Enter the count down target date HOUR (24 hour clock)
                var minute=38;    //-->Enter the count down target date MINUTE
                var tz=<?=$row['eventzone']?>;
                $(document).ready(function(){
                    countdown(<?=$dateslice[0]?>,<?=$dateslice[1]?>,<?=$dateslice[2]?>,<?=$timeslice[0]?>,<?=$timeslice[1]?>,'<?=$buttons?>', '<?=$msgPopup?>','<?=$expertuser?>','<?=$VidID?>' );
                 });
                </script>
                <?php
                //$GoogelAuthNamespace = new Zend_Session_Namespace('Google_Auth_Email');

            if (isset($_GET['authparam']))
            {
                
                 $authparam=$_GET['authparam'];
                if($authparam==md5(GoogleEmail))
                {
                    ?>
                        <script type="text/javascript">
                         $(document).ready(function(){
                        var pathname = window.location.pathname;
                        //window.location.assign(BASE_URL+pathname) ;
                        window.history.pushState("string", "Title", BASE_URL+pathname);
                        $('#HangoutButton').html('Broadcast initiated.');
                        $('#HangoutButton').removeClass('nowStartBroadcasting');
                        $('#HangoutButton').addClass('nowStartBroadcasting2');
                          $dbConfirm({
                                    content:'<?=$msgPopup1?>', 
                                    yes: true,
                                    no: false,
                                    yesLabel: 'Start broadcast',                                    
                                    yesClick:function (e){
                                        $('#HangoutButton2').removeAttr('disabled');
                                        $('#HangoutButton2').click();
                                    }
                                });
                           
                                  $dbTip();
                         });
                        </script>
                    <?php
                }
                else
                {
                    ?>
                        <script type="text/javascript">
                         $(document).ready(function(){  
                         var pathname = window.location.pathname;
                         window.history.pushState("string", "Title", BASE_URL+pathname);                          
                          $dbConfirm({
                                    content:'<?=$msgPopup?>', 
                                    yes: true,
                                    no: true,
                                    yesLabel: 'Authenticate yourself',
                                    noLabel: 'Cancel',
                                    yesClick:function (e){
                                        var gLink = $('#googleauthlink').attr('href');
                                        window.location = gLink;
                                    }
                                   
                                });
                                  $dbTip();
                         });
                        </script>
                    <?php
                }
            }
           
           
          if($row['liveeventend'] < date('Y-m-d H:i'))
            {
                ?>
                <script type="text/javascript">
                $(document).ready(function(){  
                $('#HangoutButton').html('Broadcast finished.');
                $('#HangoutButton').removeClass('nowStartBroadcasting');
                $('#HangoutButton').addClass('nowStartBroadcasting2');
                $('#HangoutButton').addClass('disabled');
                var hangcontent='<div id="HangoutButton" style="margin-top:20px;" class="nowStartBroadcasting2 disabled">Broadcast finished.</div>';
                $('.youTubeVideoPostWrp').after(hangcontent);
                $('#ytplayer').remove();
                $('#RefreshYoutube').remove();
                $('#RefreshYoutubespan').remove();
                });
                </script>
                <?php
            }

               $dbee_content .='<div id="HangoutButton" style="margin-top:20px;"><div id="countdown" style="display:none"><table cellspacing="4" cellpadding="0" border="0" align="center">
                    <tr>
                        <td><div class="numbers" id="count2" style="margin-right:10px;">Time to go live:</div></td>
                        <td align="right"><div class="numbers" id="dday"></div></td>        
                        <td align="left"><div class="title" id="days">Days</div></td>
                        <td align="right"><div class="numbers" id="dhour"></div></td>
                        <td align="left"><div class="title" id="hours">Hours</div></td>
                        <td align="right"><div class="numbers" id="dmin"></div></td>
                        <td align="left"><div class="title" id="minutes">Minutes</div></td>
                        <td align="right"><div class="numbers" id="dsec"></div></td>
                        <td align="left"><div class="title" id="seconds">Seconds</div></td>
                    </tr>
                </table>
                </div></div>';
            }



           /* if ($this->socialloginabilitydetail['allSocialstatus'] == 0 && $this->socialloginabilitydetail['Twitterstatus'] == 0) {
            if ($this->_userid  && $row['Type'] != 6 && clientID!=55) {
                if (!empty($twitter)) // check twitter logined or not
                    {
                    $dbee_content .= '<a '.$style.' href="javascript:void(0);" class="btn btn-twitter btn-mini" id="twittersphere">Involve Twitter users</a><div class = "asktoquestion"></div>';
                } else {
                    
                    $dbee_content .= ' <a '.$style.' class="btn btn-twitter btn-mini connectToTwitter" data-fromplace="sphere" data-id="' . $row['DbeeID'] . '" href="javascript:void(0);">Involve Twitter users</a> ';
                }
            }
            
        }*/
        
        return $dbee_content_array = array(
            $dbee_content
        );
        
    }
    
    public function searchtwitteruserAction()
    {
        
        $data = array();
        $content='';
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        //$response = $this->getResponse();
        //$response->setHeader('Content-type', 'application/json', true);
        $filter = new Zend_Filter_StripTags();
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $allgrouptypes = $this->groupModel->getgrouptypes();
            
            $keyword = $filter->filter($this->_request->getPost('twitteruserkeyword'));
            $type    = $filter->filter($this->_request->getPost('type'));
            $dbeeid  = (int) $this->_request->getPost('dbeeid');
            
            $dbee_result = $this->myhome_obj->checkdbeeexist($dbeeid);
            $url         = BASE_URL . '/dbee/' . $dbee_result['0']['dburl'];
            $url         = $this->makeshortlink($dbeeid);
            
            $title = "";
            
            $title = strip_tags(str_replace('@', '', $dbee_result['0']['Text']));
            
            if ($dbee_result['0']['Type'] == '5')
                $title = $dbee_result['0']['polltext'];
            if (strlen($title) > 100)
                $title = substr($title, 0, 114) . '...';
            
            $content.='<h2 id="searchgroups">Involve Twitter</h2>
            <div class="postTypeContent" id="passform">
                <div class="formRow">
                    <div class="formField">
                        <input type="text" id="twitteruserkeyword" class="textfield" placeholder= "Please enter username separted with commas" value="">
                    </div>
                </div>
                <div class="previewTwitterWrp">
                    <h2>Preview</h2>
                    <textarea  name="titlefortweet" style="display:none;"  id="titleForTweet">' . $title . '</textarea>
                    <textarea  name="preview" style="display:none;"  id="preview_twitterSphere">' . $title . '</textarea>
                    <div id="preview_twitterSphereHtml" g-url="' . $url. '"></div>
                </div>
            </div>
            <div id="search-results"></div>';
            echo $content;
            //$data['content'] = $content;
           // $data['status']  = 'success';
            //$encoded_rows = array_map('utf8_encode', $data);            
            
        }
        //return $response->setBody(Zend_Json::encode($data));
        
    }
    
    public function makeshortlink($dbeeid)
    {
        $checkdbeeexist = $this->myhome_obj->checkdbeeexist($dbeeid);
        return ' ' . $this->shortUrl(BASE_URL . '/dbee/' . $checkdbeeexist[0]['dburl']) . ' ';
    }
    
    
    
    public function getscore($userid, $id, $dbeeUser, $followstring, $TotalComments = '', $row)
    {
        
        $ParrentID   = '0';
        $ParrentType = '0';
        if ($row['GroupID'] != 0 && $row['GroupID'] != '') {
            $ParrentID   = $row['GroupID'];
            $ParrentType = '1';
        }
        
        if ($row['events'] != 0 && $row['events'] != '') {
            $ParrentID   = $row['events'];
            $ParrentType = '2';
        }
        
        $objuser        = new Application_Model_DbUser();
        $sentimentDiv   = '';
        $totalLike      = $this->profile_obj->totalikesdbee($userid, '2', $id);
        $totalLove      = $this->profile_obj->totalikesdbee($userid, '1', $id);
        $totalPhil      = $this->profile_obj->totalikesdbee($userid, '3', $id);
        $totalDislike   = $this->profile_obj->totalikesdbee($userid, '4', $id);
        $totalHate      = $this->profile_obj->totalikesdbee($userid, '5', $id);
        $scoreDiv       = '';
        $scoreSmallIcon = '<span id="loveTotalDB"><i class="scoreSprite scrollSmallIcons scoreLove"></i><strong>' . $totalLove . '</strong></span>
                      <span id="likeTotalDB"><i class="scoreSprite scrollSmallIcons scoreLike"></i><strong>' . $totalLike . '</strong></span>
                      <span id="philosopherTotalDB"><i class="scoreSprite scrollSmallIcons scorePhilosopher"></i><strong>' . $totalPhil . '</strong></span>
                      <span id="dislikeTotalDB"><i class="scoreSprite scrollSmallIcons scoreDislike"></i><strong>' . $totalDislike . '</strong></span>
                      <span id="hateTotalDB"><i class="scoreSprite scrollSmallIcons scoreHate"></i><strong>' . $totalHate . '</strong></span>';
        
        
        if ($userid == adminID) {
            $totsentiofuser = $this->dbeeCommentobj->totalsentiments($id, 'user');
            if (($TotalComments > $totsentiofuser[0]['tot']) && ($TotalComments > 1)) {
                $sentimentDiv = '<input type="hidden" name="dbeetype_edit" value="' . $this->dbedit_type . '"  > <div class="fetchsentimentsBtn"><a href="javascript:void(0);" id="fetchsentiments" dbid="' . $id . '" class="btn btn-green btn-mini" >Sentiment Analysis</a></div><div id="opensentiment" class="pull-left"></div>';
            }
        }
        if ($row['Type'] != 5)
            $commentBtn = '';
        
        
        if ($this->IsLeagueOn == 0)
            $postLeagueBtn = '<a href="javascript:void(0);" id="singledbleages" style="display:none" class="btn btn-yellow pull-right">post leagues</a> ';
        
        if ($userid == $dbeeUser) {
            $scoreDiv .= $postLeagueBtn;
            if ($this->plateform_scoring == 0) 
            {
                $scoreDiv .= '<div class="scroreDivOnTop disabledScore pull-left"><ul>';                
                $ScoreName2 = $this->post_score_setting[2]['ScoreName2'];
                $ScoreIcon2 = $this->myclientdetails->ShowScoreIcon($this->post_score_setting[2]['ScoreIcon2']);
                $ScoreName1 = $this->post_score_setting[1]['ScoreName1'];
                $ScoreIcon1 = $this->myclientdetails->ShowScoreIcon($this->post_score_setting[1]['ScoreIcon1']);
                $scoreDiv .= '<li><a href="javascript:void(0)" class="disabled">' . $ScoreIcon2 . ' </a> </li>';
                $scoreDiv .= '<li><a href="javascript:void(0)" class="disabled">' . $ScoreIcon1 . ' </a> </li>';
                
                $ScoreName3 = $this->post_score_setting[3]['ScoreName3'];
                $ScoreIcon3 = $this->myclientdetails->ShowScoreIcon($this->post_score_setting[3]['ScoreIcon3']);
                $scoreDiv .= '<li><a href="javascript:void(0)" class="disabled">' . $ScoreIcon3 . ' </a> </li>';
                $ScoreName4 = $this->post_score_setting[4]['ScoreName4'];
                $ScoreIcon4 = $this->myclientdetails->ShowScoreIcon($this->post_score_setting[4]['ScoreIcon4']);
                $scoreDiv .= '<li><a href="javascript:void(0)" class="disabled">' . $ScoreIcon4 . ' </a></li>';
                $scoreDiv .= ' </ul></div>';
            }
            $scoreDiv .= $commentBtn;
        } else if ($this->_userid == '')
            $scoreDiv .= '';
        else {
            
            if ($dbeeUser == adminID && $this->adminpostscore == 0) {
                $style         = 'style="cursor:text"';
                $data          = 'data-none';
                $styleforscore = 'style="display:none"';
            } else {
                $style         = '';
                $data          = 'data';
                $styleforscore = 'style="display:block"';
            }
            $scoreDiv .= $postLeagueBtn;
            
            $tip = 'rel="dbTip"';

            if (($this->plateform_scoring == 0 && $dbeeUser != adminID) || ($this->adminpostscore == 1 && $dbeeUser == adminID)) {
                if(ismobile)$tip = '';

                $scoreDiv .= '<div class="scroreDivOnTop postDetailsScore pull-left" ' . $styleforscore . '><div class="userScore"><a href="#" class="scoreTxt"><i class="fa fa-check"></i> Score</a><ul class="scoreList">';
                $ParrentId  = $row['events'];
                $ScoreName1 = $this->post_score_setting[1]['ScoreName1'];
                $ScoreIcon1 = $this->myclientdetails->ShowScoreIcon($this->post_score_setting[1]['ScoreIcon1'], 'fa-lg');
                
                
                
                $ScoreName2 = $this->post_score_setting[2]['ScoreName2'];
                $ScoreIcon2 = $this->myclientdetails->ShowScoreIcon($this->post_score_setting[2]['ScoreIcon2']);
                
                $scoreDiv .= '<li><a title="' . $ScoreName2 . '" '.$tip.' href="javascript:void(0)" ' . $style . ' ' . $data . '="2,like,1,' . $id . ',' . $ParrentID . ',' . $ParrentType . '" id="like-dbee">' . $ScoreIcon2 . ' <span class="scoreSpantxt">Agree</span></a> </li>';
                
                $scoreDiv .= '<li><a title="' . $ScoreName1 . '" '.$tip.' href="javascript:void(0);" ' . $style . ' ' . $data . '="1,love,1,' . $id . ',' . $ParrentID . ',' . $ParrentType . '" id="love-dbee">' . $ScoreIcon1 . ' <span class="scoreSpantxt">Strongly Agree</span></a> </li>';
                
                $ScoreName3 = $this->post_score_setting[3]['ScoreName3'];
                $ScoreIcon3 = $this->myclientdetails->ShowScoreIcon($this->post_score_setting[3]['ScoreIcon3']);
                
                $scoreDiv .= '<li> <a title="' . $ScoreName3 . '" '.$tip.' href="javascript:void(0)" ' . $style . ' ' . $data . '="4,dislike,1,' . $id . ',' . $ParrentID . ',' . $ParrentType . '" id="dislike-dbee">' . $ScoreIcon3 . ' <span class="scoreSpantxt">Disagree</span></a> </li>';
                $ScoreName4 = $this->post_score_setting[4]['ScoreName4'];
                $ScoreIcon4 = $this->myclientdetails->ShowScoreIcon($this->post_score_setting[4]['ScoreIcon4'], 'fa-lg');
                
                $scoreDiv .= '<li> <a title="' . $ScoreName4 . '" '.$tip.' href="javascript:void(0)" data="5,hate,1,' . $id . ',' . $ParrentID . ',' . $ParrentType . '" id="hate-dbee">' . $ScoreIcon4 . ' <span class="scoreSpantxt">Strongly Disagree</span></a></li>';
                $scoreDiv .= '</ul></div></div>';
            }
            $scoreDiv .= $commentBtn;
        }
        if ($row['Type'] == 5)
            $scoreDiv = '';

        if ($dbeeUser == adminID)
            $scoreDiv = '';
        
        return $score_array = array(
            $scoreDiv,
            $scoreSmallIcon,
            $sentimentDiv
        );
        
    }
    
    function _getIdbytitle($title)
    {
        
        return $dbeeid = $this->myhome_obj->getidbytitle($title);
        
    }
    public function updatedbeeurlAction()
    {
        
        foreach ($this->myhome_obj->getalldbee() as $row):
            $dbee_urltitles = $this->myhome_obj->getdburltitle_update($row['DbeeID']);
            echo $row['DbeeID'];
            echo "</br >";
        endforeach;
        $this->_helper->layout()->disableLayout();
        return;
    }
    
    public function showpdfAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $filter = new Zend_Filter_StripTags();
            $id     = $filter->filter($this->_request->getPost('id'));
            
            $data['dir'] = $this->myhome_obj->getdir($id);
        }
        return $response->setBody(Zend_Json::encode($data));
        
    }
    
    public function influenceAction()
    {
        $data = array();
        
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            
            $filter        = new Zend_Filter_StripTags();
            $storage       = new Zend_Auth_Storage_Session();
            $sessiondata   = $storage->read();
            $sessionUserid = $sessiondata['UserID'];
            
            $dbeeobj = new Application_Model_Dbeedetail();
            
            $UserId      = $filter->filter($this->_request->getPost('UserId'));
            $ParrentId   = $filter->filter($this->_request->getPost('ParrentId'));
            $ParrentType = $filter->filter($this->_request->getPost('ParrentType'));
            $ArticleId   = $filter->filter($this->_request->getPost('ArticleId'));
            $ArticleType = $filter->filter($this->_request->getPost('ArticleType'));
            $CommentId   = $filter->filter($this->_request->getPost('CommentId'));
            
            $recordfound = $dbeeobj->CheckInfluence($UserId, $ParrentId, $ParrentType, $ArticleId, $ArticleType, $sessionUserid, $CommentId);
            
            $iflid = $recordfound[0]['id'];
            
            if (count($recordfound) < 1) {
                $insertdata = array(
                    'clientID' => clientID,
                    'UserId' => $UserId,
                    'ParrentId' => $ParrentId,
                    'ParrentType' => $ParrentType,
                    'influence_by' => $sessionUserid,
                    'ArticleId' => $ArticleId,
                    'ArticleType' => $ArticleType,
                    'CommentId' => $CommentId
                );
                
                
                $insertin = $dbeeobj->AddInfluence($insertdata);
                if ($CommentId == 0 || $CommentId == "") {
                    $this->notification->commomInsert('33', '35', $ArticleId, $sessionUserid, $UserId);
                } else {
                    $this->notification->commomInsert('33', '36', $ArticleId, $sessionUserid, $UserId, '', $CommentId);
                }
                
                $data['success'] = 'insert';
                
            } else {
                $delinf          = $dbeeobj->RemoveInfluence($iflid);
                $data['success'] = 'remove';
                
            }
           
           
        }
        return $response->setBody(Zend_Json::encode($data));
        
    }
    
}
