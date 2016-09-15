<?php
class NotificationController extends IsController
{
    public function init()
    {
        parent::init();
        if (!$this->_userid) {
            $this->_helper->redirector->gotosimple('index', 'index', true);
        }
    }
    
    
    public function indexAction()
    {
        $myNotification = new Application_Model_Notification();
        
        $latseen = date('Y-m-d H:i:s');
        setcookie("activityseencomments", $latseen, 0, '/');
        
        //setcookie('currloginlastseen', $CurrDate, 0, '/');
        
        $this->view->notification = $myNotification->fetchAll();
        
        $this->view->user = $this->_userid;
        
        $type = $this->_getParam('type');
        
        $this->view->notifyvar = $this->_getParam('notify');
        
        if ($this->_getParam('type') == '')
            $type = 2;
        
        $this->view->type = $type;
        
        return $response;
        
    }
    
    public function chkactivitynotificationAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $response = $this->getResponse();
            $response->setHeader('Content-type', 'application/json', true);
            
            $request   = $this->getRequest();
            $lastseen  = $request->getCookie('activityseencomments');
            $ghostseen = $request->getCookie('activityseenghost');
  
            $ondbdetail = $request->getPost('cmntondb');
            
            if ($lastseen == '') {
                $lastseen = date('Y-m-d H:i:s');
                setcookie("activityseencomments", $lastseen, 0, '/');
                setcookie("setforonestochkcmnt", '1', 0, '/');
            }
            
            if ($ghostseen == '') {
                setcookie("activityseenghost", date('Y-m-d H:i:s'), 0, '/');
            }
            
            
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            
            $FollowingTable = new Application_Model_Following();
            $Activities     = new Application_Model_Activities();
            
            $request = $this->getRequest();
            $result  = array();
            $userId  = $this->_userid;
            

            $others             = $Activities->getNotification($userId, 3, 'seen'); // for type others
            $result['otherTot'] = count($others);


            
            $msgcount         = $Activities->getNotification($userId, 10, 'seen'); // for messages
            $result['msgTot'] = count($msgcount);
            
            $privatepostgrouptot        = $Activities->privatepostgrouptot($userId, 35, $lastseen); // for messages
            

            $result['privatepostgroup'] = count($privatepostgrouptot);

            $eventposttot               = $Activities->privatepostgrouptot($userId, 47, $lastseen); // for 
            $result['eventposttot']     = count($eventposttot);
            
            if(count($eventposttot) ==0 && count($privatepostgrouptot) ==0)
            {
                $follusers     = $FollowingTable->getfolloweruserbyid($userId,$this->session_data['usertype']);
                $result['dbs'] = 0;
                if (count($follusers) > 0) {
                    $dbs = $Activities->getdbeeNotification($follusers, $userId, 1, $lastseen); // for dbs others
                    if (count($dbs) > 0) {
                        $dbcount = 1;
                        foreach ($dbs as $key2 => $value2) {
                            $getFollowing = $this->myclientdetails->passSQLquery("select * from tblFollows where User=" . $value2['UserID'] . " AND FollowedBy = " . $this->_userid);
                            //echo $getFollowing[0]['StartDate'].' < '. $value2['act_date'];
                            if ($getFollowing[0]['StartDate'] < $value2['act_date']) {
                                $result['dbs'] = $dbcount++;
                            }
                        }
                    }
                }
            }
            else
            {
              $result['dbs'] = 0;
            }
            
            
            //$adminapprove	 	=	$Activities->GetAdminApproveNotification($userId,30, $lastseen);  //for admin approve
            
            //$result['adminapprove'] 	=	count($adminapprove);
            
            $commentId = $Activities->getcommenteddbs($userId, 2);
            if (count($commentId) > 0)
                $comments = $Activities->getdbeeNotification($commentId, $userId, 2, $lastseen, '', $ondbdetail); // for dbs others
            $result['comments'] = count($comments);
            
            $groupmdatan = $Activities->groupaccept($start, $this->_userid, 20);
            
            $grcount            = $Activities->getgroupnotigymsgtot($userId, 12, $lastseen); // for group
            $result['groupTot'] = count($grcount) + count($groupmdatan);
            
            $grcount2            = $Activities->getgroupnotigymsgtot($userId, 13, $lastseen); // for group
            $result['groupTot2'] = count($grcount2);
            
            $privatepost           = $Activities->privatepostgrouptot($userId, 35, $lastseen); // for group
            //$privatepostdb         = $Activities->privatepostgrouptot($userId, 43, $lastseen); 
            //$result['privatepost'] = count($privatepostdb);
            $result['privatepost'] = count($privatepost);

            /*$adminrole           = $Activities->privatepostgrouptot($userId, 37, $lastseen); // for group
            $result['adminrole'] = count($adminrole);

            $adminroleuser           = $Activities->privatepostgrouptot($userId, 38, $lastseen); // for group
            $result['adminroleuser'] = count($adminroleuser);*/
            
            $league           = $Activities->getactivityleaguenotifytotal($userId, 14, $ghostseen); // for group
            $result['league'] = count($league);            
            
            return $response->setBody(Zend_Json::encode($result));
            exit;
        }
    }
    
    public function displayactivitynotificationAction()
    {
        $myfollowing                 = new Application_Model_Following();
        $myNotification              = new Application_Model_Activities();
        $DbeeallTable                = new Application_Model_Myhome();
        $this->view->myclientdetails = new Application_Model_Clientdetails();
        if ($this->_userid)
            $id = $this->view->userid = $this->_userid;
        
        $this->view->response = $this->getResponse();
        
        $CurrDate = date('Y-m-d H:i:s');
        $request  = $this->getRequest();
        
        /*$lastseen = $request->getCookie('activityseencomments');
        if($lastseen==''){
        $lastseen = date('Y-m-d H:i:s');
        setcookie("activityseencomments", $lastseen, 0, '/');
        }*/
        setcookie("activityseencomments", $CurrDate, 0, '/');
        setcookie('currloginlastseen', $CurrDate, 0, '/');
        
        $type  = $request->getpost('type');
        $start = $request->getpost('start');
        $mdata = array();
        
        $cookieuser = $this->_userid;

        $eventpostdata   = $myNotification->geteventpostdbee($start, $this->_userid, 47);        
       
        $following       = $myfollowing->getfolloweruserbyid($this->_userid,$this->session_data['usertype']);
       
        $dbmdata         = $myNotification->getactivitynotifydbee($start, $id, 1, $following);
        $dbs             = $DbeeallTable->dbeeusernotifi($this->_userid);
        $cmntmdata       = $myNotification->getactivitynotify($start, $id, 2, $dbs);
        $groupmdata      = $myNotification->getactivitygroupnotify($start, $this->_userid, 12);
        $groupmdata2     = $myNotification->getactivitygroupnotify($start, $this->_userid, 13);
        $groupmdatan     = $myNotification->groupaccept($start, $this->_userid, 20);
        $lgmdata         = $myNotification->getactivityleaguenotify($start, $this->_userid, 14);
        $expmdata        = $myNotification->getactivityexpert($start, $this->_userid, 11);
        $scoremdata      = $myNotification->getactivitynotifyscore($start, $id, 6, $dbs);
        $privatepostdata = $myNotification->getprivatedgroupdbee($start, $this->_userid, 35); 
        if(!empty($privatepostdata)){
            $dbmdata = array();
        }  

        //echo "<pre>"; print_r($privatepostdata)."</pre>";
        $following       = '';
        $msgmdata        = $myNotification->getactivitynotifymessage($start, $id, $type);
        //$othmdata = $myNotification->getactivitynotifyall($start,$id,$type,$following);
        $othmdata        = $myNotification->getNotification($this->_userid, 3, '', 'seen', 'all');
        
        $mention       = $myNotification->getactivitynotifyMention($start, $id, 3); // on commnet 
        $mentiononPost = $myNotification->getactivitynotifyMentionondbee($start, $id, 3); // on post
        $poolPost = $myNotification->getactivitynotify1($start, $id, 44); // on post
       
        //$expertPost  = $myNotification->getactivitynotifyMentionondbee($start, $id, 39);      
        $dbmdata       = (count($dbmdata) > 0) ? $dbmdata : array();
        $mdata         = array_merge($dbmdata, $cmntmdata, $groupmdata, $groupmdata2, $mention, $mentiononPost, $groupmdatan, $lgmdata, $expmdata, $scoremdata, $othmdata, $privatepostdata, $poolPost, $eventpostdata);
        usort($mdata, function($a1, $a2)
        {
            $t1 = strtotime($a1['act_date']);
            $t2 = strtotime($a2['act_date']);
            return $t1 - $t2;
        });
        $mdata = array_reverse($mdata);
        if (!$mdata == 0)
            $this->view->row2 = $mdata;
        $this->view->type     = $type;
        $this->view->start    = $start;
        $this->view->grpacept = $groupmdatan;
        $this->view->Total    = $Total;
        $this->view->seedate  = $request->getpost('seedate');
        
        $this->view->highlight = ($request->getpost('higlight') == '') ? 0 : $request->getpost('higlight');
        
        
        
        $response = $this->_helper->layout->disableLayout();
        
        return $response;
        
    }
    
    
    public function ghostnotificationAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            require_once 'includes/globalfile.php'; //file for global variables
            $response = $this->getResponse();
            $response->setHeader('Content-type', 'application/json', true);
            
            $curdate  = date('Y-m-d H:i:s');
            $request  = $this->getRequest();
            $lastseen = $request->getCookie('activityseenghost');
            
            
            $ondbdetail = $request->getPost('cmntondb');
            
            if ($lastseen == '') {
                $lastseen = date('Y-m-d H:i:s');
                setcookie("activityseenghost", $lastseen, 0, '/');
            }
            
            //echo $lastseen;
            
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            
            $NotificationRowdata = new Application_Model_Notification();
            $FollowingTable      = new Application_Model_Following();
            $DbeeallTable        = new Application_Model_Myhome();
            $Activities          = new Application_Model_Activities();
            
            
            $request  = $this->getRequest();
            $result   = array();
            $userId   = $this->_userid;
            $othRes   = '';
            $dbRes    = '';
            $cmntRes  = '';
            $countRec = 0;
            
            $msgcount = $Activities->getNotification($userId, 10, 'seen', $lastseen); // for messages			
            
            if (count($msgcount) > 0) {
                foreach ($msgcount as $key => $value) {
                    $dbRes .= '<div class="description"><strong>' . $this->myclientdetails->customDecoding($value['Name']) . ' ' . $this->myclientdetails->customDecoding($value['lname']) . '</strong> just messaged you.</div>';
                }
            } else {
                $countRec++;
            }
            
            $others = $Activities->getNotification($userId, 3, 'seen', $lastseen); // for type others
            if (count($others) > 0) {
                foreach ($others as $key1 => $value1) {
                    
                    if($value1['act_type']==37 || $value1['act_type']==38){
                    $dbRes .= '<div class="description">' . $activityaMsg[$value1['act_message']] . '</div>';
                
                    }else{
                        $appendEnd = '';
                        if($value1['act_type']==6)
                        $appendEnd = ($value1['act_typeId'] == $value1['act_cmnt_id']) ?'r post' : 'r comment';
                    $dbRes .= '<div class="description"><strong>' . $this->myclientdetails->customDecoding($value1['Name']) . ' ' . $this->myclientdetails->customDecoding($value1['lname']) . '</strong>' . $scoreType[$value1['act_score_type']] . ' ' . $activityaMsg[$value1['act_message']] .$appendEnd .'<strong></strong></div>';
                    }
                    if($value1['act_type']==34){
                   // $dbRes .= '<div class="description">' . $activityaMsg[$value1['act_message']] . '</div>';

                       // $Activities->delactivity($value1['act_id']);
                    }
                }
             
            } else {
                $countRec++;
            }
            
            $privategrpost = $Activities->privatepostgrouptot($userId, 35, $lastseen); // for type others
            
            if (count($privategrpost) > 0) {
                foreach ($privategrpost as $key1 => $value1) {
                    $dbRes .= '<div class="description"><strong>' . $this->myclientdetails->customDecoding($value1['Name']) . ' ' . $this->myclientdetails->customDecoding($value1['lname']) . '</strong>' . $scoreType[$value1['act_score_type']] . ' ' . $activityaMsg[$value1['act_message']] . ' in group<strong></strong></div>';
                }
            } else {
                $countRec++;
            }

            $follusers = $FollowingTable->getfolloweruserbyid($userId);

            $eventpostnotifydata = $Activities->ghostdbeeNotification('', $userId, 47, $lastseen, 'ghost', '');
            if (count($eventpostnotifydata) > 0) {                
                
                foreach ($eventpostnotifydata as $key5 => $value5) {
                    if($value5['act_type']==57){
                    $dbRes.= '<div class="description">' . $activityaMsg[$value5['act_message']] . '</div>';
                
                    }else{$dbRes .= '<div class="description"><strong>' . $this->myclientdetails->customDecoding($value5['Name']) . ' ' . $this->myclientdetails->customDecoding($value5['lname']) . '</strong>' . $activityaMsg[$value5['act_message']] . '</div>';
                    }
                }  
            } else {
                $countRec++;
            }
            
            
            
            
            if (count($follusers) > 0 && count($eventpostnotifydata)==0)
                $dbs = $Activities->ghostdbeeNotification($follusers, $userId, 1, $lastseen, 'ghost');
            if (!empty($privategrpost))
                $dbs = array(); 
            
            if (count($dbs) > 0 && count($eventpostnotifydata)==0) {
                foreach ($dbs as $key2 => $value2) {
                    //echo "select * from tblfollows where User=".$value2['userid']." AND FollowedBy = ".$this->_userid;
                    $getFollowing = $this->myclientdetails->passSQLquery("select * from tblFollows where User=" . $value2['userid'] . " AND FollowedBy = " . $this->_userid);
                    
                    if ($getFollowing[0]['StartDate'] < $value2['act_date']) {
                        
                        $dbRes .= '<div class="description"><strong>' . $this->myclientdetails->customDecoding($value2['Name']) . ' ' . $this->myclientdetails->customDecoding($value2['lname']) . '</strong>' . $activityaMsg[$value2['act_message']] . '</div>';
                    }
                }
                $lastseen = date('Y-m-d H:i:s');
                setcookie("activityseenghost", $lastseen, 0, '/');
                
            } else {
                $countRec++;
            }
            
            $commentId = $Activities->getcommenteddbs($userId, 2);
            if (count($commentId) > 0)
                $comments = $Activities->ghostdbeeNotification($commentId, $userId, 2, $lastseen, 'ghost', $ondbdetail); // for dbs others
            
            if (count($comments) > 0) {
                foreach ($comments as $key3 => $value3) {
                    $dbRes .= '<div class="description"><strong>' . $this->myclientdetails->customDecoding($value3['Name']) . ' ' . $this->myclientdetails->customDecoding($value3['lname']) . '</strong>' . $activityaMsg[$value3['act_message']] . '</div>';
                }
                
            } else {
                $countRec++;
            }
            
            //$groupid	= 	$Activities->getgroupnotify($userId,12);
            //if(count($groupid) >0)
            $groupnotifydata = $Activities->ghostdbeeNotification('', $userId, 12, $lastseen, 'ghost', '');
            
            if (count($groupnotifydata) > 0) {
                foreach ($groupnotifydata as $key4 => $value4) {
                    $dbRes .= '<div class="description"><strong>' . $this->myclientdetails->customDecoding($value4['Name']) . ' ' . $this->myclientdetails->customDecoding($value4['lname']) . '</strong>' . $activityaMsg[$value4['act_message']] . '</div>';
                }
                
            } else {
                $countRec++;
            }
            
            $groupnotifydata = $Activities->ghostdbeeNotification('', $userId, 13, $lastseen, 'ghost', '');
            
            if (count($groupnotifydata) > 0) {
                foreach ($groupnotifydata as $key4 => $value4) {
                	if($value4['act_type']==37 || $value4['act_type']==38){
                    $dbRes .= '<div class="description">' . $activityaMsg[$value4['act_message']] . '</div>';
                
                	}else{$dbRes .= '<div class="description"><strong>' . $this->myclientdetails->customDecoding($value4['Name']) . ' ' . $this->myclientdetails->customDecoding($value4['lname']) . '</strong>' . $activityaMsg[$value4['act_message']] . '</div>';
                	}
                }  
            } else {
                $countRec++;
            }

           
           
            if ($countRec >= 8)
                $result['ResultArr'] = '';
            else {
                $result['ResultArr'] = $dbRes;
                setcookie("activityseenghost", $curdate, 0, '/');
            } 
            
            return $response->setBody(Zend_Json::encode($result));
            exit;
        }
    }
    
    public function fetchnotification2Action()
    {
        
        $myfollowing                 = new Application_Model_Following();
        $myNotification              = new Application_Model_Activities();
        $DbeeallTable                = new Application_Model_Myhome();
        $this->view->myclientdetails = new Application_Model_Clientdetails();
        if ($this->_userid)
            $id = $this->view->userid = $this->_userid;
        
        $request = $this->getRequest();
        $type    = $request->getpost('type');
        $start   = $request->getpost('start');
        $mdata   = array();
        
        $cookieuser = $this->_userid;
        $CurrDate   = date('Y-m-d H:i:s');
        
        setcookie('currloginlastseen', $CurrDate, 0, '/');
        $following      = $myfollowing->getfolloweruserbyid($this->_userid);
        $dbmdata        = $myNotification->getactivitynotifydbee($start, $id, 1, $following);
        $dbs            = $DbeeallTable->dbeeusernotifi($this->_userid);
        $cmntmdata      = $myNotification->getactivitynotify($start, $id, 2, $dbs);
        $groupmdata     = $myNotification->getactivitygroupnotify($start, $this->_userid, 12);
        $groupmdata2    = $myNotification->getactivitygroupnotify($start, $this->_userid, 13);
        $groupmdatan    = $myNotification->groupaccept($start, $this->_userid, 20);
        $lgmdata        = $myNotification->getactivityleaguenotify($start, $this->_userid, 14);
        $expmdata       = $myNotification->getactivityexpert($start, $this->_userid, 11);
        $scoremdata     = $myNotification->getactivitynotifyscore($start, $id, 6, $dbs);
        //echo "<pre>"; print_r($scoremdata);
        $following      = '';
        $msgmdata       = $myNotification->getactivitynotifymessage($start, $id, $type);
        //$othmdata = $myNotification->getactivitynotifyall($start,$id,$type,$following);
        $othmdata       = $myNotification->getNotification($this->_userid, 3, '', 'seen', 'all');
        $privategroupdb = $myNotification->getprivatedgroupdbee($start, $this->_userid, $type);
        $mention        = $myNotification->getactivitynotifyMention($start, $id, 3);
        $mentiononPost  = $myNotification->getactivitynotifyMentionondbee($start, $id, 3);
        
        //$expertPost  = $myNotification->getactivitynotifyMentionondbee($start, $id, 39);
        //echo "<pre>"; print_r($othmdata);
        $dbmdata        = (count($dbmdata) > 0) ? $dbmdata : array();
        $mdata          = array_merge($dbmdata, $cmntmdata, $groupmdata, $groupmdata2, $mention, $mentiononPost, $groupmdatan, $lgmdata, $expmdata, $scoremdata, $othmdata, $msgmdata, $privategroupdb);
        usort($mdata, function($a1, $a2)
        {
            $t1 = strtotime($a1['act_date']);
            $t2 = strtotime($a2['act_date']);
            return $t1 - $t2;
        });
        $mdata = array_reverse($mdata);
        if (!$mdata == 0)
            $this->view->row2 = $mdata;
        $this->view->type    = $type;
        $this->view->start   = $start;
        $this->view->Total   = $Total;
        $this->view->seedate = $request->getpost('seedate');
        
        
        $response = $this->_helper->layout->disableLayout();
        
        return $response;
        
    }

    public function fetchnotificationAction()
    {
        
        $myfollowing                 = new Application_Model_Following();
        $myNotification              = new Application_Model_Activities();
        $DbeeallTable                = new Application_Model_Myhome();
        $this->view->myclientdetails = new Application_Model_Clientdetails();
        if ($this->_userid)
            $id = $this->view->userid = $this->_userid;
        
        $request = $this->getRequest();
        $type    = $request->getpost('type');
        $start   = $request->getpost('start');
        
        $cookieuser = $this->_userid;
        $CurrDate   = date('Y-m-d H:i:s');
        
        setcookie('currloginlastseen', $CurrDate, 0, '/');
        if ($type == 1) {
            $following = $myfollowing->getfolloweruserbyid($this->_userid);
            $mdata     = $myNotification->getactivitynotifydbee($start, $id, $type, $following);
            $Total     = $myNotification->getactivitynotifydbeetotal($start, $id, $type, $following);
        } elseif ($type == 2) {
            $dbs   = $DbeeallTable->dbeeusernotifi($this->_userid);
            $mdata = $myNotification->getactivitynotify($start, $id, $type, $dbs);
            $Total = $myNotification->getactivitynotifytotal($start, $id, $type, $dbs);
            
        } elseif ($type == 12 || $type == 13) {
            $mdata = $myNotification->getactivitygroupnotify($start, $this->_userid, $type);
        } elseif ($type == 14) {
            $mdata = $myNotification->getactivityleaguenotify($start, $this->_userid, $type);
            $Total = $myNotification->getactivityleaguenotifytotal($start, $this->_userid, $type);
        } elseif ($type == 11) {
            $mdata = $myNotification->getactivityexpert($start, $this->_userid, $type);
            
        } elseif ($type == 6) {
            
            $dbs   = $DbeeallTable->dbeeusernotifi($this->_userid);
            $mdata = $myNotification->getactivitynotifyscore($start, $id, $type, $dbs);
            $Total = $myNotification->getactivitynotifyscoretotal($start, $id, $type, $dbs);
            
        } elseif ($type == 10) {
            $mdata = $myNotification->getactivitynotifymessage($start, $id, $type);
            //$Total = $myNotification->getactivitynotifymessageTotal($start,$id,$type);
            
        } elseif ($type == 3) {
            $mdata = $myNotification->getactivitynotifyMention($start, $id, 3);
            
        } elseif ($type == 35) {
            $mdata = $myNotification->getprivatedgroupdbee($start, $this->_userid, $type);
        }
        
        else {
            $following = '';
            $mdata     = $myNotification->getactivitynotify1($start, $id, $type, $following);
            $Total     = $myNotification->getactivitynotify1total($start, $id, $type, $following);
        }
        
        if (!$mdata == 0)
            $this->view->row2 = $mdata;
        $this->view->type    = $type;
        $this->view->start   = $start;
        $this->view->Total   = $Total;
        $this->view->seedate = $request->getpost('seedate');
        
        
        $response = $this->_helper->layout->disableLayout();
        
        return $response;
        
    }
    
    public function NotificationreloadAction()
    {
        
        $request = $this->getRequest();
        
        $data = $request->getParams();
        
        $user = $this->_userid;
        
        $myhome = new Application_Model_Myhome();
        
        $Notificationtable = new Application_Model_Notification();
        
        $dbeeid = $this->getRequest()->getParam('db');
        
        $end = $this->getRequest()->getParam('end');
        
        $start = $this->getRequest()->getParam('start');
        
        $reload = $this->getRequest()->getParam('reload');
        
        $order = $this->getRequest()->getParam('sortorder');
        
        $this->view->row = $myhome->getuserdbee($dbeeid);
        
        $this->view->Notificationrow = $Notificationtable->getNotificationreload($dbeeid, $start);
        
        $this->view->Notificationtotal = $Notificationtable->getNotificationtotal($dbeeid, $start);
        
        $this->view->startnew = $start + 5;
        
        $this->view->end = $start + 5;
        
        
        
        $response = $this->_helper->layout->disableLayout();
        
        return $response;        
    }

    
}
