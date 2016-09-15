<?php
class AjaxController extends IsController
{
    protected $_userid = null;
    public function init()
    {
        parent::init();
        // Disable the main layout renderer
        $this->_helper->layout->disableLayout();
        // Do not even attempt to render a view
        $this->_helper->viewRenderer->setNoRender(true);
        $storage    = new Zend_Auth_Storage_Session();          
        $auth        =  Zend_Auth::getInstance();      
        if($auth->hasIdentity()) 
        {               
             $data      = $storage->read(); 
             $this->_userid = $data['UserID'];
             $this->session_data = $data;
             $this->expert = new Application_Model_Expert();   
        }
    }

    protected function jsonResponse($status, $code, $html, $message = null)
    {
        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            -> setHttpResponseCode($code)
            ->setBody(Zend_Json::encode(array("status"=>$status, "html"=>$html, "message"=>$message)))
            ->sendResponse();
            exit;
    }
    
    public function soapAction()
    {
        $carrID = $this->_request->getPost('carrID');
        $pageID = $this->_request->getPost('page');
        $page = 20*($pageID-1);
        if($carrID!='')
        {
            if($page==0)
            {
                $WSCarerBookingData = $this->getBookingList($carrID);
                $count = count($WSCarerBookingData);
                $count = $count-1;
                $this->session_name_space->responseData = $WSCarerBookingData;
            }else{
                $WSCarerBookingData = $this->session_name_space->responseData;
                $count = count($WSCarerBookingData);
                $count = $count-1;
            }

            $total = ($count-$page);

            for ($i = $total; $i> $total-20; $i--) { 
                if(!empty($WSCarerBookingData[$i]) && is_array($WSCarerBookingData[$i]))
                $responseData[] = $WSCarerBookingData[$i];
            }
            if(count($responseData)!=0)
                $this->jsonResponse('success', 200, $responseData);
            else
                $this->jsonResponse('success', 200, array());
        }else{
            $this->jsonResponse('success', 200, array());
        }
    }


    public function getBookingList($carrID)
    {
        try {
            $carrID = $this->_request->getPost('carrID');
            $wsdl = "http://webservicetest.consultuscare.com:9595/ConsultusWebService.asmx?wsdl";
            $passphrase = 'livephrase';
            $soapClient = new SoapClient($wsdl);
            $theResponse = $soapClient->test();
            
            $params = array(
              "Connection" => 'CarerLive',
              "PassPhrase" => $passphrase
            );
            
            $response1 = $soapClient->__soapCall("GetToken", array($params));

            $params = array(
              "Connection" => 'CarerLive',
              "CarerId" => $carrID,
              "Token" => $response1->GetTokenResult->Token
            );
            
            $response = $soapClient->__soapCall("GetCarerBookingList", array($params));
            $WSCarerBookingData = array();
            $WSCarerBooking = $response->GetCarerBookingListResult->BookingList->WSCarerBooking;
            $i = 0;
            foreach ($WSCarerBooking as $key => $value) 
            {
              
               $WSCarerBookingData[$i]['ClientAccountId'] = ($value->ClientAccountId!=null)?$value->ClientAccountId:'';
               $WSCarerBookingData[$i]['ClientId'] = ($value->ClientId!=null)?$value->ClientId:'';
               $WSCarerBookingData[$i]['AccountName'] = ($value->AccountName!=null)?$value->AccountName:'';
               $WSCarerBookingData[$i]['StartDate'] = $value->StartDate;
               $WSCarerBookingData[$i]['EndDate'] = $value->EndDate;
               $WSCarerBookingData[$i]['ClientTitle'] = ($value->ClientTitle!=null)?$value->ClientTitle:'';
               $WSCarerBookingData[$i]['ClientSurname'] = ($value->ClientSurname!=null)?$value->ClientSurname:'';
               $WSCarerBookingData[$i]['ClientFirstname'] = ($value->ClientFirstname!=null)?$value->ClientFirstname:'';
               $WSCarerBookingData[$i]['ClientProfile'] = ($value->ClientProfile!=null)?$value->ClientProfile:'';
               $WSCarerBookingData[$i]['ClientAddressLine1'] = ($value->ClientAddressLine1!=null)?$value->ClientAddressLine1:'';
               $WSCarerBookingData[$i]['ClientAddressLine2'] = ($value->ClientAddressLine2!=null)?$value->ClientAddressLine2:'';
               $WSCarerBookingData[$i]['ClientAddressLine3'] = ($value->ClientAddressLine3!=null)?$value->ClientAddressLine3:'';
               $WSCarerBookingData[$i]['ClientAddressLine4'] = ($value->ClientAddressLine4!=null)?$value->ClientAddressLine4:'';
               $WSCarerBookingData[$i]['ClientPostCode'] = ($value->ClientPostCode!=null)?$value->ClientPostCode:'';
               $WSCarerBookingData[$i]['ClientHomePhone'] = ($value->ClientHomePhone!=null)?$value->ClientHomePhone:'';
               $WSCarerBookingData[$i]['ClientDOB'] = ($value->ClientDOB!=null)?date('d/m/Y', strtotime($value->ClientDOB)):'';
               $i++;
            }
            return $WSCarerBookingData;

        } catch (SoapFault $fault) {
            return false;
        }
    }

    public function gettweetAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        {    
           $dbeeid = (int) $this->_request->getPost('db'); // dbeeid
           $twittertag = '';
           $resultData = $this->myhome_obj->getdbeedetail($dbeeid);
           $twittertag = $this->view->partial('partials/twittertag.phtml', array('myhome_obj'=>$this->myhome_obj,
                        'resultData'=>$resultData,
                        'commonmodel_obj'=>$this->commonmodel_obj,'dbeeCommentobj'=>$this->dbeeCommentobj,
                        'myclientdetails'=>$this->myclientdetails,
                        'userid'=>$this->_userid,'twitter_token_secret'=>$this->twitter_token_secret));
            $this->jsonResponse('success', 200, $twittertag);         
        }
        else
        {
            $errorMessage = $result->getMessages();
            $errorMessage['error'] = 'Somethings went wrong please try again later';
            $alert = $this->view->partial('partials/alert.phtml', array('alert'=>$errorMessage['error'], 'alertClass'=>'alert-error'));
            $html = array("alert"=>$alert);     
            $this->jsonResponse('error', 401, $html, $errorMessage['error']);
        }
    }
    public function loadcommentrowAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        {    
            $eventModel  = new Application_Model_Event();
            $dbeeid = (int) $this->_request->getPost('dbeeid');
            /*$getmention = $this->getmention2($dbeeid);
            print_r($getmention); exit;*/
            $type = $this->_request->getPost('type');
            $lastID = (int) $this->_request->getPost('lastID');
            $commentid = (int) $this->_request->getPost('commentid');
            $order = $this->_request->getPost('order');
            $order = ($order=='')?'DESC':$order;
            $dbeeData = $this->myhome_obj->getdbeedetail($dbeeid); 
            $currentdate = date('Y-m-d H:i:s', time());
            if($dbeeData['Type']==20 && strtotime($currentdate)<strtotime($dbeeData['qaschedule']))
            {
                $t = strtotime($dbeeData['qaschedule'])-strtotime($currentdate);
                $expireDiv= '<div align="center" class="noMoreComments" style="text-align:center"><div class="qnLiveIn">Q&A will be live in</div> <div style="display:inline-block"><div class="countDown"></div></div>'.$expire.'</div>';
                $this->jsonResponse('success', 200, array('commentlist'=>$expireDiv,'timeinsecond'=>$t,'Type'=>$dbeeData['Type']));
                die;
            }
            else if($dbeeData['Type']==20 && (strtotime($currentdate)<strtotime($dbeeData['qaendschedule'])) && strtotime($currentdate)>strtotime($dbeeData['qaschedule']))
            {
                $this->jsonResponse('success', 200, array('commentlist'=>'<div align="center" class="noMoreComments">Q&A is now live.</div>'));
                die;
            }
            else if($dbeeData['Type']==20 && strtotime($currentdate)>strtotime($dbeeData['qaendschedule']))
            {
                $this->jsonResponse('success', 200, array('commentlist'=>'<div align="center" class="noMoreComments">This Q&A is now closed.</div>'));
                die;
            }
            $grouptypes   = new Application_Model_Groups();
            $private = 0;
            switch ($type) 
            {
                case 'loadfirst':
                    $where = array('lastID'=>'','type'=>$type,'dbeeid'=>$dbeeid,'commentid'=>'','order'=>$order);
                    setcookie("activityseenghost", date('Y-m-d H:i:s'), 0, '/');
                    break;
                case 'push':
                    $where = array('lastID'=>'','type'=>$type,'dbeeid'=>$dbeeid,'commentid'=>$commentid,'order'=>$order);
                    setcookie("activityseenghost", date('Y-m-d H:i:s'), 0, '/');
                    break;
                case 'scroll':
                    $where = array('lastID'=>$lastID,'type'=>$type,'dbeeid'=>$dbeeid,'commentid'=>'','order'=>$order);
                    break;
            }

            $result = $this->dbeeCommentobj->getcommentData($where);
            $resultCount = $this->dbeeCommentobj->getcommentData($where,false);
            $sortAndTweeterfeedWrapper = true;

           if ($this->_userid == '' && $dbeeData['GroupID'] != '' && $dbeeData['GroupID']!=0) 
           {
                $groupRow = $grouptypes->selectgroupprivacy($dbeeData['GroupID']);
                if ($groupRow[0]['GroupPrivacy'] == 3) 
                {
                    $iconEx = '<span class="fa-stack fa-lg">
                                    <strong class="fa fa-circle-thin fa-stack-2x"></strong>
                                    <strong class="fa fa-exclamation fa-stack-1x "></strong></span>';
                     $commentlist = '<div id="sort-comments"></div><div id="request-group-message" class="dbee-reply-wrapper surveyComplete" style="width:100%; padding:20px 15px;">' . $iconEx . ' This post belongs to a group that requires you to join before you can post a comment.</div><div class="next-line"></div>';
                }else
                {
                    $commentlist = $this->view->partial('partials/commentrow.phtml', array('post_score_setting'=>$this->post_score_setting,'result'=>$result,'dbeeData'=>$dbeeData,'Is_PollComments_On'=>$this->Is_PollComments_On,'PollComments_On_Option'=>$this->PollComments_On_Option,'plateform_scoring'=>$this->plateform_scoring,'myclientdetails'=>$this->myclientdetails,'userid'=>$this->_userid,'counts'=>count($result)));
                }
                $sortAndTweeterfeedWrapper = false;
            }
            else if ($this->_userid != '' && $dbeeData['Type'] == 9 && $dbeeData['events']!= 0) 
            {
                $eventResult = $eventModel->getEvent($dbeeData['events']);
                $memberData  = $eventModel->checkEventMember($this->_userid, $dbeeData['events']);
                if (empty($memberData) && $dbeeData['event_type'] == 2)
                {
                    $commentlist = "<div class='dbee-reply-wrapper surveyComplete' style='width:100%;'><a href = 'javascript:void(0);' class='eventJoin' event-id='".$dbeeData['events']."' event-type='".$dbeeData['event_type']."'  >Click here to become an attendee</a></div><div class='next-line'></div>";
                    $sortAndTweeterfeedWrapper = false;
                }else
                {
                    $commentlist = $this->view->partial('partials/commentrow.phtml', array('post_score_setting'=>$this->post_score_setting,'result'=>$result,'dbeeData'=>$dbeeData,'Is_PollComments_On'=>$this->Is_PollComments_On,'PollComments_On_Option'=>$this->PollComments_On_Option,'plateform_scoring'=>$this->plateform_scoring,'myclientdetails'=>$this->myclientdetails,'userid'=>$this->_userid,'counts'=>count($result),'post_score_setting'=>$this->post_score_setting));
                }
            }
            else if ($this->_userid != '' && $dbeeData['GroupID'] != '' && $dbeeData['GroupID']!=0) 
            {
                $requestGroup = false;
                $requestStatus = 5;
                $groupdetails = $grouptypes->selectgroup($dbeeData['GroupID']);
                $owner    = $groupdetails[0]['User'];
                $thisgroupowner = false;
                if ($this->_userid == $owner)
                    $thisgroupowner = true;

                $groupRow = $grouptypes->selectgroupprivacy($dbeeData['GroupID']);
                if ($groupRow[0]['GroupPrivacy'] == '2' && $thisgroupowner==false) 
                {
                    $memberRes = $grouptypes->selectpvtgroupmem($dbeeData['GroupID'], $this->_userid);
                    if (count($memberRes) == 0) 
                    {
                        $requestGroup  = true;
                        $requestStatus = 3;
                    }
                } 
                elseif ($groupRow[0]['GroupPrivacy'] == '3' && $this->_userid && $thisgroupowner==false)
                { 
                    $memberRes = $grouptypes->selectgroupmem($dbeeData['GroupID'], $this->_userid);
                    if (count($memberRes) == 0) 
                    {
                        $requestGroup  = true;
                        $requestStatus = 0;
                    } elseif ($memberRes[0]['Status'] == 0 && $memberRes[0]['SentBy'] == 'Self') 
                    {
                        $requestGroup  = true;
                        $requestStatus = 1;
                    } elseif ($memberRes[0]['Status'] == 0 && $memberRes[0]['SentBy'] == 'Owner') 
                    {
                        $requestGroup  = true;
                        $requestStatus = 2;
                    }
                }

                $iconEx = '<span class="fa-stack fa-lg">
                                <strong class="fa fa-circle-thin fa-stack-2x"></strong>
                                <strong class="fa fa-exclamation fa-stack-1x "></strong></span>';

                switch ($requestStatus) 
                {
                    case 0:
                       $commentlist = '<div id="sort-comments"></div><div id="request-group-message" class="dbee-reply-wrapper surveyComplete" style="width:100%; padding:20px 15px;">' . $iconEx . ' This post belongs to a group that requires you to join before you can post a comment.<br><br><a href="javascript:void(0)" onclick="javascript:joingroupreq(' . $dbeeData['GroupID'] . ',' . $owner . ');">Click here to request</a>.</div><div class="next-line"></div>';
                       $sortAndTweeterfeedWrapper = false;
                        break;
                    case 1:
                       $commentlist = "<div class='dbee-reply-wrapper surveyComplete' style='width:100%;'>" . $iconEx . " Your request is pending approval by the group owner.</div><div class='next-line'></div>";
                       $sortAndTweeterfeedWrapper = false;
                        break;
                    case 2:
                       $commentlist = "<div class='dbee-reply-wrapper surveyComplete' style='width:100%;'>" . $iconEx . " You have been invited to this group.<br /><br />Respond via your group notifications.</div><div class='next-line'></div>";
                       $sortAndTweeterfeedWrapper = false;
                        break;
                    case 3:
                      $commentlist = "<div class='dbee-reply-wrapper surveyComplete' style='width:100%;'>" . $iconEx . " This post belongs to a private group.<br><br>you need to be a member to comment on it.</div><div class='next-line'></div>";
                       $sortAndTweeterfeedWrapper = false;
                       //$private = 1;
                        break;
                    case 5:
                      $commentlist = $this->view->partial('partials/commentrow.phtml', array('post_score_setting'=>$this->post_score_setting,'result'=>$result,'dbeeData'=>$dbeeData,'Is_PollComments_On'=>$this->Is_PollComments_On,'PollComments_On_Option'=>$this->PollComments_On_Option,'plateform_scoring'=>$this->plateform_scoring,'myclientdetails'=>$this->myclientdetails,'userid'=>$this->_userid,'counts'=>count($result),'post_score_setting'=>$this->post_score_setting));
                        break;
                } 
            }
            else
            {
                $commentlist = $this->view->partial('partials/commentrow.phtml', array('post_score_setting'=>$this->post_score_setting,'result'=>$result,'dbeeData'=>$dbeeData,'Is_PollComments_On'=>$this->Is_PollComments_On,'PollComments_On_Option'=>$this->PollComments_On_Option,'plateform_scoring'=>$this->plateform_scoring,'myclientdetails'=>$this->myclientdetails,'userid'=>$this->_userid,'counts'=>count($result),'post_score_setting'=>$this->post_score_setting));
            }
            $getmention = $this->getmention2($dbeeid);
            $html = array("commentlist"=>$commentlist,'counts'=>count($result),'sortAndTweeterfeedWrapper'=>$sortAndTweeterfeedWrapper,'TotalCount'=>count($resultCount),'getmention'=>json_encode($getmention));
            $this->jsonResponse('success', 200, $html);         
        }
        else
        {
            $errorMessage = $result->getMessages();
            $errorMessage['error'] = 'Somethings went wrong please try again later';
            $alert = $this->view->partial('partials/alert.phtml', array('alert'=>$errorMessage['error'], 'alertClass'=>'alert-error'));
            $html = array("alert"=>$alert);     
            $this->jsonResponse('error', 401, $html, $errorMessage['error']);
        }
    }

    public function getmention2($dbeeid)
    {
        $followinguser = $this->profile_obj->getTotalUsers2($this->_userid);
        $activeuser =  $this->profile_obj->getTotalUsers($this->_userid, $dbeeid);
        return array_merge(json_decode($followinguser, true),json_decode($activeuser, true));
    }

    public function postdetailsAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        {    
            $postlist = $this->view->partial('partials/postlist.phtml', array('result'=>$result,
                'counts'=>$counts));
            $html = array("postlist"=>$postlist,'page_number'=>$page_number);       
            $this->jsonResponse('success', 200, $html);         
        }
        else
        {
            $errorMessage = $result->getMessages();
            $errorMessage['error'] = 'Somethings went wrong please try again later';
            $alert = $this->view->partial('partials/alert.phtml', array('alert'=>$errorMessage['error'], 'alertClass'=>'alert-error'));
            $html = array("alert"=>$alert);     
            $this->jsonResponse('error', 401, $html, $errorMessage['error']);
        }
    }

    public function usermentionAction()
    {
        $followinguser = $this->profile_obj->getTotalUsers2($this->_userid,$this->session_data['usertype']);
        $dbString = $this->_request->getPost('DbArray');
        $DbArray = explode(',', $dbString);
        foreach ($DbArray as $value)
        {
          $activeuser =  $this->profile_obj->getTotalUsers($this->_userid, $value,$this->session_data['usertype']);
          $html[$value] =json_encode(array_merge(json_decode($followinguser, true),json_decode($activeuser, true)));
        }
        $this->jsonResponse('success', 200, $html);
    }

    public function usermentionpostAction()
    {
        $dbString = $this->_request->getPost('DbArray');
        if($dbString==1)
        {
            $html['userlist'] = $mentionusers = $this->profile_obj->getTotalUsersPost($dbString,$this->_userid,$this->session_data['usertype']);
        }
        else if($dbString==2)
        {
            $eventid = $this->_request->getPost('id'); 
            $html['userlist'] = $mentionusers = $this->profile_obj->getTotalUsersPost($dbString,$eventid,$this->session_data['usertype']);
        }
        else if($dbString==3)
        {
            $grpid = $this->_request->getPost('id'); 
            $html['userlist'] = $mentionusers = $this->profile_obj->getTotalUsersPost($dbString,$grpid,$this->session_data['usertype']);
        }

        $this->jsonResponse('success', 200, $html);
    }
    

    public function showExperLinkDisplayBlock($dbeeid,$type,$expertArray)
    {

        if($type==5 || $type==6)
            return false;
        
        if((count($expertArray)!=0 && $this->allowmultipleexperts == 1) || ($type==15 && count($expertArray)!=0))
            return false;

        else if(count($expertArray)>=4 && $this->allowmultipleexperts == 3)
            return false;
       
        $requestInvitation = $this->expert->checkinviteexpert($dbeeid);

        $requestDbeeInvitation = $this->expert->getInviteUser($dbeeid);

        if(!empty($requestInvitation) && $this->allowmultipleexperts ==1)
            return '<a href="javascript:void(0);" class="invtExpertExist" >View existing invite</a> <span style="margin-right:3px;">|</span>';
        else if(!empty($requestDbeeInvitation) && $this->allowmultipleexperts ==1)
            return '<a href="javascript:void(0);" class="invtExpertExist" >View existing invite</a> <span style="margin-right:3px;">|</span>';

        $expertLabel  = 'Invite ' . $this->expertText;
        $content = '<a href="javascript:void(0)">' . $expertLabel . '</a>
        <ul class="dropDownList right" style="width:170px;">';
        $content .= '<li><a href="javascript:void(0);" class="invitexport" dbid="' . $dbeeid . '"  type="2" style="font-weight:normal" >Invite from platform</a></li>';
       
        if((count($expertArray)==0))
            $content .= '<li><a href="javascript:void(0);" class="makeownexpert" dbid="' . $dbeeid . '" 
            type="1" style="font-weight:normal" >Make me ' . $this->expertText . '</a></li>';

        if($this->allowmultipleexperts == 3 && (!empty($requestInvitation) || !empty($requestDbeeInvitation)))
            $content .= '<li><a href="javascript:void(0);" class="invtExpertExist" dbid="'.$dbeeid.'" >View existing invite</a></li>';

        return $content.'</ul><span style="margin-right:3px;">|</span>';
    }


    public function rightpartAction()
    {
        $storage  = new Zend_Auth_Storage_Session();
        $session      = $storage->read();
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        {    
            if($this->_userid!='')
            {
                $dbeeid = (int) $this->_request->getPost('dbeeid'); // dbeeid
                $twittertag = '';
                $resultData = $this->myhome_obj->getdbeedetail($dbeeid); // get dbee result 
                if($resultData['Type']==6)
                {
                   
                    $result_dbee_attendies = $this->myhome_obj->attendies($dbeeid);
                    $count = count($result_dbee_attendies);
                    $attendiesListStop = ($resultData['attendiesList']==1)?'checked':'';
                    if($count!=0)
                        $attendies = $this->view->partial('partials/attendies.phtml', array('resultData'=>$result_dbee_attendies,
                        'count'=>$count,'myclientdetails'=>$this->myclientdetails,'dbeeid'=>$dbeeid,'attendiesListStop'=>$attendiesListStop,'userid'=>$this->_userid,'adminID'=>adminID,'attendiesList'=>$resultData['attendiesList'],'usertype'=>$this->session_data['usertype']));
                    else
                        $attendies = '';
                    
                }
                else if($resultData['Type']==15)
                {
                    $result_dbee_attendies = $this->myhome_obj->attendies($dbeeid);
                    $count = count($result_dbee_attendies);
                    $attendiesListStop = ($resultData['attendiesList']==1)?'checked':'';
                    if($count!=0)
                        $attendies = $this->view->partial('partials/attendies.phtml', array('resultData'=>$result_dbee_attendies,
                        'count'=>$count,'myclientdetails'=>$this->myclientdetails,'dbeeid'=>$dbeeid,'attendiesListStop'=>$attendiesListStop,'userid'=>$this->_userid,'adminID'=>adminID,'attendiesList'=>0,'usertype'=>$this->session_data['usertype']));
                    else
                        $attendies = '';
                }
                
                $twittertag = $this->view->partial('partials/twittertag.phtml', array('myhome_obj'=>$this->myhome_obj,
                        'result'=>$vals3,'resultData'=>$resultData,
                        'commonmodel_obj'=>$this->commonmodel_obj,'dbeeCommentobj'=>$this->dbeeCommentobj,
                        'myclientdetails'=>$this->myclientdetails,
                        'userid'=>$this->_userid,'twitter_token_secret'=>$this->twitter_token_secret));
                
                /* post tag and relative post div */

                $countTopDBeeTag    = $this->myhome_obj->getsearchHashTopComment();
                $countTopCommentTag = $this->myhome_obj->getsearchHashTopdbee();
                
                if (count($countTopDBeeTag) > 0 && $resultData['DbTag']!='')
                {
                    $data   = array();
                    $result = array();
                    foreach ($countTopDBeeTag as $value) 
                    {
                        $keywords = preg_split("/[\s,]+/", $value['DbTag']);
                        foreach ($keywords as $keywordsvalue)
                            $result[] = $keywordsvalue;
                    }
                    $vals = array_count_values($result);
                }
                
                if (count($countTopCommentTag) > 0) 
                {
                    $data2   = array();
                    $result2 = array();
                    foreach ($countTopCommentTag as $value2) 
                    {
                        $keywords2 = preg_split("/[\s,]+/", $value2['DbTag']);
                        foreach ($keywords2 as $keywordsvalue2)
                            $result2[] = $keywordsvalue2;
                    }
                    $vals2 = array_count_values($result2);
                }
                
                if (!empty($vals2) && !empty($vals))
                    $vals3 = array_merge($vals2, $vals);
                else if (!empty($vals2))
                    $vals3 = $vals2;
                else if (!empty($vals))
                    $vals3 = $vals;
                else if (empty($vals) && empty($vals2))
                    $vals3 = array();

                if (!empty($vals3) && $resultData['DbTag']!='') 
                {
                    arsort($vals3);
                    $output = array_slice($vals3, 0, 11);
                    $postTag = $this->view->partial('partials/posttag.phtml', array('myhome_obj'=>$this->myhome_obj,
                        'result'=>$output,'resultData'=>$resultData,
                        'commonmodel_obj'=>$this->commonmodel_obj,'dbeeCommentobj'=>$this->dbeeCommentobj,
                        'myclientdetails'=>$this->myclientdetails,'session'=>$session));
                }else
                    $postTag ='';
                /* post tag and relative post div */

                $askquestion = '';
                /* expert div */

                $expertArray = $this->myhome_obj->showDBeeExpert($dbeeid);
               
                if($resultData['User']==$this->_userid && $this->_userid==adminID){
                    $expertLink = $this->showExperLinkDisplayBlock($dbeeid,$resultData['Type'],$expertArray);
                }
                else
                    $expertLink = '';
                $currentdate = date('Y-m-d H:i:s', time());
                if($resultData['Type']==20 && (strtotime($currentdate) >= strtotime($resultData['qaschedule'])) && (strtotime($currentdate) < strtotime($resultData['qaendschedule'])))
                {
                    $diff = strtotime($resultData['qaendschedule']) - strtotime($currentdate);
                }else{
                    $diff = 0;
                }
                $expert = $this->view->partial('partials/expert.phtml', array('allowexperts'=>$this->allowexperts,myhome_obj=>$this->myhome_obj,'expertArray'=>$expertArray,'userid'=>$this->_userid,'resultData'=>$resultData,'myclientdetails'=>$this->myclientdetails,
                            'commonmodel_obj'=>$this->commonmodel_obj,'dbeeCommentobj'=>$this->dbeeCommentobj,'expertText'=>$this->expertText));
                

                $mentionusers = $this->profile_obj->getTotalUsers($this->_userid, $dbeeid);
                
                $html = array('attendies'=>$attendies,'postTag'=>$postTag,"twittertag"=>$twittertag,'expert'=>$expert,'mentionusers'=>$mentionusers,'askquestion'=>$askquestion,'Loginid'=>$this->_userid,'expertid'=>$expertArray['UserID'],'expertLink'=>$expertLink,'countExpert'=>count($expertArray),'Biography'=>$this->Biography,'countAten'=>$count,'resultData'=>$resultData,'diff'=>$diff);

            }else{
                $html = array('attendies'=>'','postTag'=>'',"twittertag"=>'','expert'=>'');
            }
                   
            $this->jsonResponse('success', 200, $html);         
        }
        else
        {
            $errorMessage = $result->getMessages();
            $errorMessage['error'] = 'Somethings went wrong please try again later';
            $alert = $this->view->partial('partials/alert.phtml', array('alert'=>$errorMessage['error'], 'alertClass'=>'alert-error'));
            $html = array("alert"=>$alert);     
            $this->jsonResponse('error', 401, $html, $errorMessage['error']);
        }
    }

    public function getpromotedexpertAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        {
            if($this->_userid!='')
            {
                $where = array('promoted'=>1);
                $userlist = $this->myclientdetails->getAllMasterfromtable('tblUsers',array('*'),$where);
                $promoted = $this->view->partial('partials/promoted.phtml', array('userid'=>$this->_userid,'userlist'=>$userlist,
                        'myclientdetails'=>$this->myclientdetails));
            }
            $this->jsonResponse('success', 200, $promoted); 
        }
    }

    public function fetchtwitdatarightAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        {
            if($this->_userid!='')
            {
                $dbeeid = (int) $this->_request->getPost('dbeeid'); // dbeeid
                $twittertag = '';
                $resultData = $this->myhome_obj->getdbeedetail($dbeeid); // get dbee result 
                $twittertag = $this->view->partial('partials/twittertag.phtml', array('myhome_obj'=>$this->myhome_obj,
                        'resultData'=>$resultData,
                        'commonmodel_obj'=>$this->commonmodel_obj,'dbeeCommentobj'=>$this->dbeeCommentobj,
                        'myclientdetails'=>$this->myclientdetails,
                        'userid'=>$this->_userid,'twitter_token_secret'=>$this->twitter_token_secret));
                
                $mentionusers = $this->profile_obj->getTotalUsers($this->_userid, $dbeeid);
                $html = array("twittertag"=>$twittertag);

            }else{
                $html = array("twittertag"=>'');
            }
                   
            $this->jsonResponse('success', 200, $html);         
        }
        else
        {
            $errorMessage = $result->getMessages();
            $errorMessage['error'] = 'Somethings went wrong please try again later';
            $alert = $this->view->partial('partials/alert.phtml', array('alert'=>$errorMessage['error'], 'alertClass'=>'alert-error'));
            $html = array("alert"=>$alert);     
            $this->jsonResponse('error', 401, $html, $errorMessage['error']);
        }
    }
    public function tweetdeleteAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        {
            $id = (int)$this->_request->getPost('id');
            $myhome_obj  = new Application_Model_Myhome();
            $myhome_obj->deletetwtby($id,$this->_userid);
            $this->jsonResponse('success', 200, $id); 
        }
    }
    public function postcreaterAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        {
            $following =  new Application_Model_Following();
            $dbeeid = (int) $this->_request->getPost('dbeeid'); // dbeeid
            $resultData = $this->myhome_obj->getdbeedetail($dbeeid);
            if($resultData['GroupID']!=0 && $resultData['GroupID']!='')
            {
                $grouptypes  = new Application_Model_Groups();  
                  $groupdetails = $grouptypes->selectgroup($resultData['GroupID']);
                  $thisgroupowner=false;
                  $requestStatus=false;
                  if($this->_userid==$groupdetails[0]['User']) $thisgroupowner=true;
                  if(!$thisgroupowner) {
                    if($groupdetails[0]['GroupPrivacy']=='2' && $this->_userid!='') { // IF PRIVATE GROUP
                      $memberRes = $grouptypes->selectpvtgroupmem($resultData['GroupID'],$this->_userid);
                      if(count($memberRes)==0) {
                        $requestGroup=true;
                        $requestStatus=3;
                      }
                    }
                  }
             }
             // get dbee result 
            $postcreater = $this->view->partial('partials/postcreater.phtml', 
                array('ResultData'=>$resultData,
                'Myhome_Model'=>$this->Myhome_Model,
                'following'=>$following,
                'myclientdetails'=>$this->myclientdetails,
                'userid'=>$this->_userid,'requestStatus'=>$requestStatus,'grprow'=>$resultData));
            $html = array("postcreater"=>$postcreater);       
            $this->jsonResponse('success', 200, $html);         
        }
    }

}