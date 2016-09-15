<?php
class CommentController extends IsController
{
    public function init()
    {
        parent::init();
        $storage = new Zend_Auth_Storage_Session();
        $auth    = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $data          = $storage->read();
            $this->_userid = $data['UserID'];
        } else {
            $action = $this->getRequest()->getActionName();
            if ($action != 'commentreload')
                $this->_helper->redirector->gotosimple('index', 'index', true);
        }
        
        $this->notification = new Application_Model_Notification();
        $this->myhome_obj   = new Application_Model_Myhome();
        
    }
    public function indexAction()
    {
        $CurrDate = date('Y-m-d H:i:s');
        $request  = $this->getRequest();
        $userid   = $request->getpost('user', $this->_userid);        
        $OtherUser   = $request->getpost('OtherUser');
        if (!$userid) {
            $userid = $this->_userid;
        }
        
        
        $start = $request->getpost('start', 0);
        $end   = $request->getpost('end', PAGE_NUM);
        setcookie('currloginlastseencomments', $CurrDate, 0, '/');
        $mycomment                   = new Application_Model_Comment();
        $this->view->dbeecomment     = $mycomment->index($start, $end, $userid);
        //print_r($this->view->dbeecomment); 
       // count($this->view->dbeecomment); die;
        $this->view->startnew        = $start + PAGE_NUM;
        $this->view->end             = $start + PAGE_NUM;
        $this->view->OtherUser       = $OtherUser;
        $this->view->dbeenotavailmsg = $request->getPost('dbeenotavailmsg');
        $this->view->seemore         = $request->getPost('seemore');
        
        $response                    = $this->_helper->layout->disableLayout();
        return $response;
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
    
    
    public function removeHashTags($comment)
    {
        $hasTags = $this->getHashTags($comment);
        
        $explodeHash = explode(',', $hasTags);
        
        foreach ($explodeHash as $value) {
            $comment = str_replace("#" . $value, "", $comment);
        }
        return $comment;
    }
    public function scoredbeeAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        
        
        
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            
        	$checkImage        = new Application_Model_Commonfunctionality();
            $validate = $this->commonmodel_obj->authorizeToken($this->_request->getPost('SessUser__'), $this->_request->getPost('SessId__'), $this->_request->getPost('SessName__'), $this->_request->getPost('Token__'), $this->_request->getPost('Key__'));
            
            $db         = (int) $this->_request->getPost('db');
            $commentid  = (int) $this->_request->getPost('comment');
            $score      = $this->_request->getPost('score');
            $type       = $this->_request->getPost('type');
            $ParentId   = $this->_request->getPost('ParentId');
            $ParentType = $this->_request->getPost('ParentType');
            
            $data['type']        = $type;
            $data['mylastscore'] = 0;
            $data['SubmitMsg']   = 1;
            $data['deleted']     = 0;
            $user                = $this->_userid;
            
            if ($type == '1') {
                $id = $db;
                unset($where);
                $where      = array(
                    'DbeeID' => $id
                );
                $dbeedetail = $this->myclientdetails->getRowMasterfromtable('tblDbees', array(
                    'Text','User'
                ), $where);
                
                
                $Owner  = $dbeedetail['User'];
                $MainDB = $db;
                $label  = 'post';
                $id     = $db;
            } else {
                unset($where);
                $where       = array(
                    'CommentID' => $commentid
                );
                $commentRow  = $this->myclientdetails->getRowMasterfromtable('tblDbeeComments', array(
                    'DbeeID',
                    'UserID',
                    'Type',
                    'Comment',
                    'LinkTitle',
                    'PicDesc',
                    'VidDesc'
                ), $where);
                $Owner       = $commentRow['UserID'];
                $MainDB      = $commentRow['DbeeID'];
                $CommentText = $commentRow['Comment'];
                $label       = 'comment';
                $id          = $commentid;
            }
            
            unset($where);
            $where     = array(
                'ID' => $id,
                'type' => $type,
                'UserID' => $user,
                'ParentId' => $ParentId,
                'ParentType' => $ParentType
            );
            $scoredata = $this->myclientdetails->getRowMasterfromtable('tblScoring', array(
                '*'
            ), $where);
            $scoreid   = $scoredata['ScoreID'];
            
            if (count($scoredata) == 0 || $scoredata == false) {
                $dataArray = array(
                    'ID' => $id,
                    'Owner' => $Owner,
                    'Type' => $type,
                    'UserID' => $user,
                    'Score' => $score,
                    'MainDB' => $MainDB,
                    'ParentId' => $ParentId,
                    'ParentType' => $ParentType,
                    'clientID' => clientID,
                    'ScoreDate' => date('Y-m-d H:i:s')
                );
                if ($this->myclientdetails->insertdata_global('tblScoring', $dataArray)) {
                    $data['scoreAdded'] = true;
                    $this->notification->commomInsert('6', '6', $db, $user, $Owner, $score, $commentid); // Insert for score activity
                }
            } else {
                if ($scoredata['Score'] == 1)
                    $mylastscore = 'love';
                elseif ($scoredata['Score'] == 2)
                    $mylastscore = 'like';
                //elseif($scoredata['Score']==3) $mylastscore='philosopher';
                elseif ($scoredata['Score'] == 4)
                    $mylastscore = 'dislike';
                elseif ($scoredata['Score'] == 5)
                    $mylastscore = 'hate';
                
                $data['mylastscore'] = $mylastscore;
                if ($scoredata['Score'] == $score) {
                    unset($where);
                    $where = array(
                        'ScoreID' => $scoreid
                    );
                    $this->myclientdetails->deleteMaster('tblScoring', $where);
                    $data['deleted'] = 1;
                    //$this->notification->commomInsert('6',$activityaMsg[11],$db,$user,$Owner); // delete for score activity
                } else {
                    unset($where);
                    $where      = array(
                        'ScoreID' => $scoreid
                    );
                    $dataUpdate = array(
                        'score' => $score
                    );
                    $success    = $this->myclientdetails->updateMaster('tblScoring', $dataUpdate, $where);
                    if ($success) {
                        $data['scoreAdded'] = true;
                        $data['id']         = $id;
                        $data['user']       = $user;
                        $data['SubmitMsg']  = 1;
                        
                        unset($where);
                        $where   = array(
                            'UserID' => $user
                        );
                        $UserRow = $this->myclientdetails->getRowMasterfromtable('tblUsers', array(
                            '*'
                        ), $where);
                        
                        if ($UserRow['Status'] != '0') {
                            if ($score == '1')
                                $scorelabel = 'love';
                            elseif ($score == '2')
                                $scorelabel = 'like';
                            elseif ($score == '3')
                                $scorelabel = 'food for thought';
                            elseif ($score == '4')
                                $scorelabel = 'dislike';
                            elseif ($score == '5')
                                $scorelabel = 'hate';
                            $data['scorelabel'] = $scorelabel;
                            if ($type == '1')
                                $Text = $dbeedetail['Text'];
                            else
                                $Text = $CommentText;
                            
                            $Textval = htmlentities($Text, ENT_QUOTES, "UTF-8");
                            
                            unset($where);
                            $where          = array(
                                'DbeeID' => $db,
                                'UserID' => $Owner
                            );
                            $getCommentinfo = $this->myclientdetails->getRowMasterfromtable('tblDbeeComments', array(
                                'NotifyEmail'
                            ), $where);
                            if ($getCommentinfo['NotifyEmail'] == 1) {                                    
                                $userprofile       = $checkImage->checkImgExist($UserRow['ProfilePic'], 'userpics', 'default-avatar.jpg');
                                $UserRowProfilePic = '<span><img src="'.IMGPATH.'/users/small/' . $userprofile . '" width="60" height="60" border="0" /><span>';
                                unset($where);
                                $where              = array(
                                    'UserID' => $Owner
                                );
                                $OwnerRow           = $this->myclientdetails->getRowMasterfromtable('tblUsers', array(
                                    '*'
                                ), $where);
                                $EmailTemplateArray = array(
                                    'Email' => $OwnerRow['Email'],
                                    'Username' => $UserRow['Username'],
                                    'UserRowProfilePic' => $UserRowProfilePic,
                                    'label' => $label,
                                    'Name' => $UserRow['Name'],
                                    'lname'=> $UserRow['lname'],
                                    'scorelabel' => $scorelabel,
                                    'MainDB' => $MainDB,
                                    'Textval' => $Textval,
                                    'clientID' => clientID,
                                    'case' => 24
                                );
                                $bodyContentmsgall  = $this->dbeeComparetemplateOne($EmailTemplateArray);
                            }
                        }
                    }
                    
                    $this->notification->commomInsert('6', '6', $db, $user, $Owner, $score, $commentid); // update for score activity   
                    
                }
            }

            $scoreother = $this->myclientdetails->passSQLquery("select s.UserID,u.full_name from tblScoring as s,tblUsers as u WHERE s.clientID='" . clientID . "' and  s.ID='" . $db . "' and  s.Score='" . $score . "' and s.UserID=u.UserID and s.UserID!='" . $user . "'");
            $countpeoplescore=count($scoreother);

                if($score==4)
                {
                    $iconval=3;
                }
                elseif ($score==5) {
                    $iconval=4;
                }
                else
                {
                     $iconval=$score;
                }
                $configuration_xx   = $checkImage->getConfiguration();
                $post_score_setting = json_decode($configuration_xx['ScoreNames'], true);
                $scoreicon=$this->myclientdetails->ShowScoreIcon($post_score_setting[$iconval]['ScoreIcon'.$iconval], "");

                $textforscore='';
                $textforscore.=$scoreicon .' You ';
                $classxx='single';
                if($countpeoplescore > 0)
                {  
                  $classxx='';
                  $others    = ($countpeoplescore == 1) ? 'other' : 'others';
                  $textforscore.=' and '.$countpeoplescore.' '.$others;
                  $titleusername='';
                  $i=0;
                  foreach ($scoreother as $key => $value) {
                     $titleusername.=$this->myclientdetails->customDecoding($value['full_name']).'<br>';
                     if($i==20)
                     {
                        $titleusername.='<a href="javascript:void(0); class="moreuserlike">more..</a>';
                     }

                     $i++;
                  }
                }

                $scorelink='<a rel="dbTip" href="javascript:void(0);" title="'.$titleusername.'" class="otheruserlike '.$classxx.' pull-right" data-dbeeid="'.$db.'" data-score="'.$iconval.'">'.$textforscore.'</a>';
                if($data['deleted']==1)
                {
                    $scorelink='';
                }
               $data['status']  = 'success';
               $data['scorelink'] = $scorelink;
               $data['scoreicon'] = $scoreicon;
            
        } else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
        
    }
    public function insertdataAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);

               
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $request  = $this->getRequest();
                $db     = intval($request->getpost('db'));
                $userid = $this->_userid;
                
                $SubmitMsg      = 0;
                $parentid       = intval($request->getpost('parentid'));
                $twittercomment = $request->getpost('twittercomment');
                $mentionsIds    = $request->getpost('mentionsIds');



                $comment        = stripslashes(strip_tags($request->getpost('comment', ''), '<br><a>'));
               
                $hashTag        = $this->getHashTags($comment);
                $link           = stripslashes(strip_tags($request->getpost('url', '')));
                if (strpos($link, "http://") !== false || strpos($link, "https://") !== false)
                    $link = $link;
                else if($link!='')
                    $link = "http://" . $link;
                $linktitle = stripslashes(strip_tags($request->getpost('linktitle', '')));
                $LinkPic   = stripslashes(strip_tags($request->getpost('LinkPic', '')));
                $linkdesc  = stripslashes(strip_tags($request->getpost('linkdesc', '')));
                $pic       = stripslashes(strip_tags($request->getpost('pic', '')));
                $vid       = stripslashes(strip_tags($request->getpost('vid', '')));
                $viddesc   = stripslashes(strip_tags($request->getpost('viddesc', ''), '<a>'));
                $VidTitle  = stripslashes(strip_tags($request->getpost('VidTitle', ''), '<a>'));
                $videosite = stripslashes(strip_tags($request->getpost('videosite', '')));
                $videoid   = stripslashes(strip_tags($request->getpost('videoid', '')));
                $audio     = stripslashes($request->getpost('audio', ''));
                
                $twittercomment = str_replace("<a href='javascript:void(0);' style='font-size:11px' onclick='javascript:removetweetfromnewcomment();'>remove</a>", "", $twittercomment);
                $twittercomment = stripslashes($twittercomment);
                $twittercomment = str_replace('&', '%26', $twittercomment);
                
                
                unset($where);
                $where     = array(
                    'DbeeID' => $db
                );
                $dbeeRow   = $this->myclientdetails->getRowMasterfromtable('tblDbees', array('User','Comments','Text','Type','GroupID','notification'), $where);
                $dbeeOwner = $dbeeRow['User'];
                
                $Type = 1;
                
                unset($where);
                $where          = array(
                    'DbeeID' => $db,
                    'UserID' => $this->_userid
                );
                $getCommentinfo = $this->myclientdetails->getRowMasterfromtable('tblDbeeComments', array(
                    'UserID'
                ), $where);
                
                if (!$getCommentinfo['UserID'])
                    $NotifyEmail = 1;
                else
                    $NotifyEmail = 0;
                
                // make array for insert
                $CommentDate = date('Y-m-d H:i:s');
                $dataArray   = array(
                    'DbeeID' => $db,
                    'parentid' => $parentid,
                    'DbeeOwner' => $dbeeOwner,
                    'UserID' => $userid,
                    'Type' => $Type,
                    'Comment' => nl2br($comment),
                    'Link' => $link,
                    'LinkTitle' => $linktitle,
                    'LinkPic' => $LinkPic,
                    'LinkDesc' => $linkdesc,
                    'Pic' => $pic,
                    'Vid' => $vid,
                    'VidDesc' => $viddesc,
                    'VidSite' => $videosite,
                    'VidTitle' => $VidTitle,
                    'VidID' => $videoid,
                    'Audio' => $audio,
                    'TwitterComment' => $twittercomment,
                    'CommentDate' => $CommentDate,
                    'NotifyEmail' => $NotifyEmail,
                    'DbTag' => $hashTag,
                    'clientID' => clientID
                );
                
                $insertId = $this->myclientdetails->insertdata_global('tblDbeeComments', $dataArray);
                
                // make array for insert
                
                
                if ($insertId) 
                {
                    $this->notification->commomInsert('2', '2', $db, $userid, $dbeeOwner, '', $insertId); // Insert for involve activity
                    $cmntownerId = intval($request->getpost('cmntownerId'));
                    $mentionsArr = explode(',', $mentionsIds);
                    if (count($mentionsArr) > 0) {
                        foreach ($mentionsArr as $key => $value) {
                            $dataM = array(
                                'clientID' => clientID,
                                'UserMentioned' => $value,
                                'MentionedBy' => $this->_userid,
                                'DbeeID' => $db,
                                'commentId' => $insertId,
                                'MentionDate' => $CommentDate
                            );
                            if ($value != '')
                                $this->myclientdetails->insertdata_global('tblMentions', $dataM);
                            if ($value != '')
                                $this->notification->commomInsert('3', '3', $db, $userid, $value, '', $insertId); // Mention activity
                        }
                    }
                    if (!empty($parentid))
                        $this->notification->commomInsert('3', '3', $db, $userid, $cmntownerId, '', $insertId); // Mention activity
                    
                    // set cookies value
                    $expire = time() + 60 * 60 * 24 * 10;
                    setcookie("activityseenghost", date('Y-m-d H:i:s'), 0, '/');
                    setcookie('currloginlastseencomments', $CommentDate, $expire, '/');
                    $seenact = $_COOKIE['setforonestochkcmnt'];
                    if ($seenact == 1) {
                        setcookie("activityseencomments", $CommentDate, 0, '/');
                        setcookie("setforonestochkcmnt", '2', 0, '/');
                    }
                    
                    $SubmitMsg = 1;
                    
                    // UPDATE LAST ACTIVITY TIME AND TOTAL COMMENTS FOR THE DBEE
                    $newcomment = ($dbeeRow['Comments'] + 1);

                     $wherexx          = array(
                        'DbeeID' => $db
                    );
                     
                    $allusers = $this->myclientdetails->getAllMasterfromtable('tblDbeeComments', array(
                        'DISTINCT(UserID)'
                    ), $wherexx);

                    $count_user=count($allusers);

                    $data2      = array(
                        'Comments' => $newcomment,
                        'LastCommentUser' => $userid,
                        'LastActivity' => $CommentDate,
                        'no_of_commented_users'=> $count_user
                    );
                    
                    $this->myclientdetails->updatedata_global('tblDbees', $data2, 'DbeeID', $db);
                    

                    // SEND MAIL TO DB OWNER INFORMING OF A NEW COMMENT
                    
                    $dbeeTextval       = strip_tags(htmlentities($dbeeRow['Text'], ENT_QUOTES, "UTF-8"));
                    $commTextval       = htmlentities($comment, ENT_QUOTES, "UTF-8");
                    $checkImage        = new Application_Model_Commonfunctionality();
                    $userprofilepic    = $checkImage->checkImgExist($this->session_data['ProfilePic'], 'userpics', 'default-avatar.jpg');
                    $UserRowProfilePic = '<span><img src="'.IMGPATH.'/users/small/' . $userprofilepic . '" width="60" height="60" border="0" /></span>';
                    
                    // send mail to all commented users on this dbee
                    $commentuser = $this->dbeeCommentobj->getcommentuser($db, $this->_userid);
                    
                    if (!empty($commentuser)) 
                    {
                        foreach ($commentuser as $CommentUsersRow):
                            if (trim($CommentUsersRow['Email']) != "" && $CommentUsersRow['Email'] != 'twitteruser@onserro.com' && $CommentUsersRow['Email'] != 'gplususer@onserro.com') 
                            {    
                                unset($where);
                                $where = array(
                                'User' => $CommentUsersRow['UserID']
                                );
                                $ChkOwnerCommentNotification = $this->myclientdetails->getRowMasterfromtable('tblNotificationSettings', array(
                                'Comments'
                                ), $where);
                                if ($CommentUsersRow['NotifyEmail'] == 1 && $ChkOwnerCommentNotification['Comments'] == '1') 
                                {
                                    $EmailTemplateArray = array(
                                        'Email' => $CommentUsersRow['Email'],
                                        'Name' => $this->session_data['Name'],
                                        'lname' => $this->session_data['lname'],
                                        'db' => $db,
                                        'dbeeTextval' => $dbeeTextval,
                                        'commTextval' => $commTextval,
                                        'UserRowProfilePic' => $UserRowProfilePic,
                                        'case' => 11
                                    );
                                    
                                    $bodyContentmsgall  = $this->dbeeComparetemplateOne($EmailTemplateArray);
                                }
                            }
                        endforeach;
                    }
                    // send mail to all commented users on this dbee
                    
                    
                    // SEND MAIL TO DB OWNER
                    if ($dbeeOwner != $this->_userid && $dbeeRow['notification']==1) 
                    {
                        unset($where);
                        $where = array(
                            'User' => $dbeeOwner
                        );
                        $ChkOwnerCommentNotification = $this->myclientdetails->getRowMasterfromtable('tblNotificationSettings', array(
                            'Comments'
                        ), $where);
                        if ($ChkOwnerCommentNotification['Comments'] == '1') 
                        {
                            unset($where);
                            $where    = array(
                                'UserID' => $dbeeOwner
                            );
                            $OwnerRow = $this->myclientdetails->getRowMasterfromtable('tblUsers', array(
                                'Email'
                            ), $where);
                            
                            $EmailTemplateArray = array(
                                'Email' => $OwnerRow['Email'],
                                'Name' => $this->session_data['Name'],
                                'lname' => $this->session_data['lname'],
                                'db' => $db,
                                'dbeeTextval' => $dbeeTextval,
                                'commTextval' => $commTextval,
                                'UserRowProfilePic' => $UserRowProfilePic,
                                'case' => 11
                            );
                            $bodyContentmsgall  = $this->dbeeComparetemplateOne($EmailTemplateArray);
                        }
                    }
                    // SEND MAIL TO DB OWNER
                }
                // SELECT COMMENTS
                
                $excerpt  = substr($commText, 0, 50) . '...';
                $leaguedb = stripcslashes($request->getpost('leaguedb'));
                $lgpart   = '';
                
                    $whereuser          = array(
                        'DbeeID' => $db
                    );
                    $allactiveusers = $this->myclientdetails->getAllMasterfromtable('tblDbeeComments', array(
                        'DISTINCT(UserID)'
                    ), $whereuser);

                if ($leaguedb > 0) {
                    unset($where);
                   
                    foreach ($allactiveusers as $key => $value) {
                        if ($this->_userid == $value['UserID']) {
                            $lgpart = "true";
                            break;
                        }
                    }
                } else
                    $lgpart = "league over";
                
                // End of db part

                 
                if($dbeeRow['Type']==5 && ($this->PollComments_On_Option==2 || $this->PollComments_On_Option==4 || $this->Is_PollComments_On==0))
                {
                  $newcomment='';
                }

                $data['status']     = 'success';
                $data['db']         = $db;
                $data['newcomment'] = $newcomment;
                $data['excerpt']    = $excerpt;
                $data['lgpart']     = $lgpart;
                $data['insertId']   = $insertId;
                $data['countusers']      = count($allactiveusers);
            
        }        
       
        
        
      
       if($dbeeRow['GroupID']!=''){
        $groupid = $dbeeRow['GroupID'];
       $groupobj  = new Application_Model_Groups();
       $groupUsers2 = $groupobj->calgroup($groupid);
       $groupUsersd = $groupobj->selectgroupmem($groupid,$this->_userid);
      
       $CurrDate=date('Y-m-d H:i:s');
       if(count($groupUsersd[0]['Owner'])==0){
      
       	$grdata = array(
       			'GroupID' => $groupid,
       			'Owner' => 66,
       			'User' => $this->_userid,
       			'JoinDate' => $CurrDate,
       			'SentBy' => 'Owner',
       			'clientID' => clientID,
       			'Status' => '1'
       	);
       //	print_r($grdata);
       
       	$groupobj->insertingroupmem($grdata);
      	}
        }
        
        return $response->setBody(Zend_Json::encode($data));
    }
        
    
    
    public function Optiontxthelper($user, $db)
    {
        $poll      = new Application_Model_Polloption();
        $optiontxt = $poll->getvotename($user, $db);
        return $optiontxt['OptionText'];
    }

    
   
    public function removecommentsAction()
    {
        $request = $this->getRequest();
        $response = $this->_helper->layout->disableLayout();
        $validate = $this->commonmodel_obj->authorizeToken($this->_request->getPost('SessUser__'), $this->_request->getPost('SessId__'), $this->_request->getPost('SessName__'), $this->_request->getPost('Token__'), $this->_request->getPost('Key__'));
        if ($validate == false)
            die;
        $comment            = (int) $request->getpost('comment');
        $islatestComment    = (int) $request->getpost('islatestComment');

        if($comment!=0)
        {
            $resultComment = $this->dbeeCommentobj->getcommentbyid($comment);
            $dbee_data = $this->Myhome_Model->getDbeeDetails($resultComment['DbeeID']);
        }
        if((adminID == $this->_userid) || ($this->_userid == $resultComment['UserID']) || ($this->_userid == $dbee_data['User']))
        {
            if(adminID != $this->_userid)
            {
                $where = array(
                    'CommentID' => $comment,
                    'UserID' => $this->_userid
                );
            }else{
                $where = array(
                    'CommentID' => $comment
                );
            }
            $this->myclientdetails->deleteMaster('tblactivity', array(
                "act_cmnt_id" => $comment
            ));
            
            $deleteStatus = $this->myclientdetails->deleteMaster('tblDbeeComments', $where);
          
            unset($where);
            
            $where  = array(
                'DbeeID' => $resultComment['DbeeID']
            );

            $result['total'] =  0;
            $resultAll = $this->myclientdetails->getAllMasterfromtable('tblDbeeComments', array(
                'CommentDate'
            ), $where,array('CommentDate'=>'DESC'));

            $result['total'] = count($resultAll);
            if(count($resultAll)==0)                
                $latestCdate     = $dbee_data['PostDate'];    // this will update inside lastactivity in tblDDbees 
            else
                $latestCdate     = $resultAll[0]['CommentDate'];    // this will update inside lastactivity in tblDDbees 

            // get comment count 
            // update comment count
            if ($deleteStatus) 
            {
                unset($where);
                $where = array(
                    'DbeeID' => $resultComment['DbeeID']
                );
                
                $allusers = $this->myclientdetails->getAllMasterfromtable('tblDbeeComments', array(
                    'DISTINCT(UserID)'
                ), $wherexx);
                
                $count_user = count($allusers);

                /*if($islatestComment==1)  // updateing lastActivity if top commnet removing
                {
                    $data = array('Comments' => $result['total'], 'no_of_commented_users' => $count_user, 'LastActivity' => $latestCdate );
                }
                else
                {
                    $data = array('Comments' => $result['total'], 'no_of_commented_users' => $count_user );
                }*/
                $data = array('Comments' => $result['total'], 'no_of_commented_users' => $count_user, 'LastActivity' => $latestCdate );
                $this->myclientdetails->updateMaster('tblDbees', $data, $where);

                if($resultComment['qid']!='' && $resultComment['qid']!=0)
                {
                    $this->myclientdetails->deleteMaster('tblDbeeComments', array(
                        "qid" => $resultComment['qid']
                    ));
                    unset($data);
                    unset($where);
                    $where = array(
                        'id' => $resultComment['qid']
                    );
                    $data = array(
                        'movetocomment' => 0
                    );
                    $this->myclientdetails->updateMaster('tblDbeeQna', $data, $where);

                    unset($data);
                    unset($where);
                    $where = array(
                        'parentid' => $resultComment['qid']
                    );
                    $data = array(
                        'movetocomment' => 0
                    );
                    $this->myclientdetails->updateMaster('tblDbeeQna', $data, $where);
                }
                echo $comment . '~' . $result['total']. '~' .$resultComment['DbeeID'];
            }
            else
                echo '0';
        }
        return $response;
    }
    public function scoreAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            $validate = $this->commonmodel_obj->authorizeToken($this->_request->getPost('SessUser__'), $this->_request->getPost('SessId__'), $this->_request->getPost('SessName__'), $this->_request->getPost('Token__'), $this->_request->getPost('Key__'));
            if ($validate == false) {
                $data['status']  = 'error';
                $data['content'] = 'Something went wrong please try again';
            } else {
                $userid = $this->_userid;
                if ($this->_request->getPost('db')) {
                    $id    = $this->_request->getPost('db');
                    $type  = 1;
                    $label = 'db';
                }
                if ($id == '') {
                    $id    = $this->_request->getPost('comment');
                    $type  = 2;
                    $label = 'comment';
                }
                
                $content .= '<h2 style="margin-bottom:20px;">Score this ' . $label . '</h2>
							<ul class="cmntScorePopup">
								<li onClick="javascript:scoredbee(1,\'love\',' . $type . ',' . $id . ');">
									<span class="scoreSprite scoreLoveY"></span>
									love  ' . $label . '
								</li>
								<li onClick="javascript:scoredbee(2,\'like\',' . $type . ',' . $id . ');">
									<span class="scoreSprite scoreLikeY"></span>
									like  ' . $label . '
								</li>
								<li onClick="javascript:scoredbee(3,\'philosopher\',' . $type . ',' . $id . ');">
									<span class="scoreSprite  scorePhilosopherY"></span>
									food for thought  ' . $label . '
								</li>
								<li onClick="javascript:scoredbee(4,\'dislike\',' . $type . ',' . $id . ');">
									<span class="scoreSprite  scoreDislikeY"></span>
									dislike  ' . $label . '
								</li>
								<li onClick="javascript:scoredbee(5,\'hate\',' . $type . ',' . $id . ');">
									<span class="scoreSprite scoreHateY"></span>
									hate  ' . $label . '
								</li>
							</ul>';
                
                $data['status']  = 'success';
                $data['content'] = $content;
            }
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    public function usersearchAction()
    {
        //$q = strtolower($this->getRequest()->getParam('q'));
        $request = $this->getRequest();
        $q       = 'a';
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
        $commenuserch    = new Application_Model_Comment();
        $CommentUsersres = $commenuserch->getalluser($dbeeid);
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
        $userlist = $commenuserch->getcommentusersearch($search, $UserList);
        if (count($userlist) > 0) {
            foreach ($userlist as $rs):
                if ($counter % 2 == 0)
                    $class = 'searchres-row-even';
                else
                    $class = 'searchres-row-odd';
                $checkImage  = new Application_Model_Commonfunctionality();
                $userpicsPic = $checkImage->checkImgExist($rs['ProfilePic'], 'userpics', 'default-avatar.jpg');
                $users .= '<div class="' . $class . '" style="padding:5px;"><div style="float:left; padding-bottom:5px;"><img src="'.IMGPATH.'/users/small/' . $userpicsPic . '" border="0" /></div><div style="float:left; margin-left:10px;">' . $rs['Name'] . '</div></div>|' . $rs['Name'] . '|' . $rs['UserID'] . '<div style="clear:both"></div>';
                $counter++;
            endforeach;
            if ($TotalUsers > 5)
                $users .= '<div unselectable="on" class="unselectable" style=" padding:10px 5px; text-align:center;"><a href="javascript:void(0);" onClick="javascript:seemoresearch(2);" style="color:#FFF">see more</a></div><div style="clear:both"></div>';
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
    public function viewslideshareAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            
            $slideid = $this->_request->getPost('slideid');
            $content .= '<iframe width="425" scrolling="no" id="slideShareIframe" height="355" frameborder="0" allowtransparency="true" style="margin-bottom:15px" marginheight="0" marginwidth="0" src="//www.slideshare.net/slideshow/embed_code/' . $slideid . '?rel=0"></iframe>';
            $data['status']  = 'success';
            $data['content'] = $content;
        } else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
        
    }
    public function viewvideoAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            
            $video             = $this->_request->getPost('video');
            $this->view->video = $video;
            $content .= '<iframe title="YouTube video player" width="100%" height="390" src="https://www.youtube.com/embed/' . $video . '" frameborder="0" allowfullscreen></iframe>';
            $data['status']  = 'success';
            $data['content'] = $content;
        } else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
        
    }
    public function viewaudioAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
            
            $audio               = (int) $this->_request->getPost('audio');
            $comaudio            = new Application_Model_Comment();
            $this->view->scaudio = $comaudio->getaudio($audio);
            $content .= '<div>' . str_replace('&show_artwork=true', "", $scaudio) . '</div>';
            $data['status']  = 'success';
            $data['content'] = $content;
            
        } else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    public function validatevideo($value)
    {
        $validate = new Zend_Validate_Abstract();
        if ($validate->isValid($value))
            return 1;
        else
            return 0;
    }
    public function commentnotifyAction()
    {
        $SubmitMsg = 0;
        $request   = $this->getRequest();
       
        $db = (int) $request->getpost('db');
        $type = (int) $request->getpost('type');
        unset($where);
        $where   = array(
            'DbeeID' => $db,
            'UserID' => $this->_userid
        );
        $noteRow = $this->myclientdetails->getRowMasterfromtable('tblDbeeComments', array(
            '*'
        ), $where);
        if ($type == '1') 
        {
            if ($noteRow['NotifyEmail'] == 1)
                $notify = 0;
            elseif ($noteRow['NotifyEmail'] == 0)
                $notify = 1;
            unset($where);
            $where = array(
                'DbeeID' => $db,
                'UserID' => $this->_userid
            );
            $data  = array(
                'NotifyEmail' => $notify
            );
            $this->myclientdetails->updateMaster('tblDbeeComments', $data, $where);
        }
        $noteRow = $this->myclientdetails->getRowMasterfromtable('tblDbeeComments', array(
            '*'
        ), $where);
        echo $notify . '~' . $type . '~' . $db;
        $response = $this->_helper->layout->disableLayout();
        return $response;
    }
    public function gotodbAction()
    {
        $seencomms           = '';
        $request             = $this->getRequest();
        $db                  = (int) $request->getpost('db');
        $comm                = (int) $request->getpost('comm');
        $cookieuser          = $this->_userid;
        $firstcommdate       = $request->getCookie('firstcommdate');
        $newcommentcountghst = $request->getCookie('newcommentcount-ghst');
        $newcommcookie       = $request->getCookie('newcommcookie');
        $CheckDateComments   = $request->getCookie('currloginlastseencomments');
        $seencomms           = $request->getCookie('seencomms');
        // COLLECT COMMENT IDS OF ALL COMMENTS FOR THIS DBEE
        $comms               = '';
        $counter             = 0;
        // SELECT COMMENT NOTIFY DETAILS
        $commentnotifyobj    = new Application_Model_Comment();
        $TCRes               = $commentnotifyobj->getcommentiddbee($CheckDateComments, $db, $cookieuser, $seencomms);
        foreach ($TCRes as $TCRow):
            $comms .= $TCRow['CommentID'] . ',';
            $counter++;
        endforeach;
        if ($comms != '')
            $comms = substr($comms, 0, -1);
        // COLLECT COMMENT IDS OF ALL COMMENTS FOR THIS DBEE
        // SET SEEN COMMENTS COOKIE TO EXCLUDE FROM POPUP
        if ($seencomms != '' && $comms != '') {
            $newseencomms = $seencomms . ',' . $comms;
        } else {
            $newseencomms = $comms;
        }
        // SET SEEN COMMENTS COOKIE TO EXCLUDE FROM POPUP
        $newcommcookie       = $newcommcookie - $counter;
        $newcommentcountghst = $newcommentcountghst - $counter;
        //set_cookie('currloginlastseencomments', $firstcommdate);
        $expire              = time() + 60 * 60 * 24;
        setcookie('seencomms', $newseencomms, $expire, '/');
        if ($newcommcookie > 0)
            setcookie('newcommcookie', $newcommcookie, $expire, '/');
        else
            setcookie('newcommcookie', '', time() - 3600);
        //setcookie('newcommentcount-ghst', $newcommentcountghst,$expire,'/');
        echo '1~' . $db;
        $response = $this->_helper->layout->disableLayout();
        return $response;
    }
    public function chknewcommentsAction()
    {
        $request = $this->getRequest();
        $db      = $request->getpost('db');
        $db      = 3898;
        $check   = $request->getpost('check');
        if ($check == '')
            $check = $this->getParam('check');
        $return              = '';
        $LastCommentSeenDate = $request->getCookie('LastCommentSeenDate');

        if ($LastCommentSeenDate != '') 
        {
            $comnew     = new Application_Model_Comment();
            $commentnum = $comnew->getcommentlastseen($LastCommentSeenDate, $db);
            $total      = count($commentnum);
            if ($total > 0) {
                setcookie("LastCommentSeenDate", date('Y-m-d H:i:s'), time() + 3600);
                // SELECT COMMENTS
                $TotalComments = count($comnew->totalcommentbydb($db));
                $return        = '1~#~' . $db . "~#~" . $TotalComments;
            } else
                $return = '0~#~0~#~0';
        } else
            $return = '0~#~0~#~0';
        echo $return;
        $response = $this->_helper->layout->disableLayout();
        return $response;
    }
    
    
    public function topcommentAction()
    {
        $request                = $this->getRequest();
        $db                     = $request->getpost('dbeeid');
        $comaudio               = new Application_Model_Comment();
        $this->view->commentrow = $comaudio->topcomment($db);
        
        //$dburl	=	$this->myclientdetails->getfieldsfromtable(array('dburl'),'tblDbees','DbeeID',$db);
        $this->view->dbid = $db;
        //$this->view->dburl 	= $dburl[0]['dburl'];
        $response         = $this->_helper->layout->disableLayout();
        return $response;
    }
}
