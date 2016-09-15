<?php
class Application_Model_Commonfunctionality extends Application_Model_DbTable_Master
{
    public function init()
    {
        $storage = new Zend_Auth_Storage_Session();
        $auth    = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $data          = $storage->read();
            $this->_userid = $data['UserID'];
        }
        $this->myclientdetails = new Application_Model_Clientdetails();
        $this->Myhome_Model    = new Application_Model_Myhome();
        $this->eventModel      = new Application_Model_Event();

        $this->dbeecontrollers = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
    }
    function hex2rgba($color, $opacity = false)
    {
        
        $default = 'rgb(0,0,0)';
        
        //Return default if no color provided
        if (empty($color))
            return $default;
        
        //Sanitize $color if "#" is provided 
        if ($color[0] == '#') {
            $color = substr($color, 1);
        }
        
        //Check if color has 6 or 3 characters and get values
        if (strlen($color) == 6) {
            $hex = array(
                $color[0] . $color[1],
                $color[2] . $color[3],
                $color[4] . $color[5]
            );
        } elseif (strlen($color) == 3) {
            $hex = array(
                $color[0] . $color[0],
                $color[1] . $color[1],
                $color[2] . $color[2]
            );
        } else {
            return $default;
        }
        
        //Convert hexadec to rgb
        $rgb = array_map('hexdec', $hex);
        
        //Check if opacity is set(rgba or rgb)
        if ($opacity) {
            //if(abs($opacity) > 1)
            $opacity = 0.2;
            $output  = 'rgba(' . implode(",", $rgb) . ',' . $opacity . ')';
        } else {
            $output = 'rgb(' . implode(",", $rgb) . ')';
        }
        
        //Return rgb(a) color string
        return $output;
    }
    function convert_time_zone($date_time, $from_tz, $to_tz)
    {
        $time_object = new DateTime($date_time, new DateTimeZone($from_tz));
        $time_object->setTimezone(new DateTimeZone($to_tz));
        return $time_object->format('c');
    }
    
    function addhttp($url)
    {
        if (!preg_match("@^https?://@i", $url) && !preg_match("@^ftps?://@i", $url)) {
            $url = "http://" . $url;
        }
        return $url;
    }
    
    function timezoneevent($zone)
    {
        switch ($zone) {
            case '-12.0':
                $zonetext = '(GMT -12:00) Eniwetok, Kwajalein';
                break;
            case '-11.0':
                $zonetext = '(GMT -11:00) Midway Island, Samoa';
                break;
            case '-10.0':
                $zonetext = '(GMT -10:00) Hawaii';
                break;
            case '-9.0':
                $zonetext = '(GMT -9:00) Alaska';
                break;
            case '-8.0':
                $zonetext = '(GMT -8:00) Pacific Time (US &amp; Canada)';
                break;
            case '-7.0':
                $zonetext = '(GMT -7:00) Mountain Time (US &amp; Canada)';
                break;
            case '-6.0':
                $zonetext = '(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima';
                break;
            case '-5.0':
                $zonetext = '(GMT -7:00) Mountain Time (US &amp; Canada)';
                break;
            case '-4.0':
                $zonetext = '(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz';
                break;
            case '-3.5':
                $zonetext = '(GMT -3:30) Newfoundland';
                break;
            case '-3.0':
                $zonetext = '(GMT -3:00) Brazil, Buenos Aires, Georgetown';
                break;
            case '-2.0':
                $zonetext = '(GMT -2:00) Mid-Atlantic';
                break;
            case '-1.0':
                $zonetext = '(GMT -1:00 hour) Azores, Cape Verde Islands';
                break;
            case '0.0':
                $zonetext = '(GMT) Western Europe Time, London, Lisbon, Casablanca';
                break;
            case '1.0':
                $zonetext = '(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris';
                break;
            case '2.0':
                $zonetext = '(GMT +2:00) Kaliningrad, South Africa';
                break;
            case '3.0':
                $zonetext = '(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg';
                break;
            case '3.5':
                $zonetext = '(GMT +3:30) Tehran';
                break;
            case '4.0':
                $zonetext = '(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi';
                break;
            case '5.0':
                $zonetext = '(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent';
                break;
            case '4.5':
                $zonetext = '(GMT +4:30) Bombay, Calcutta, Madras, New Delhi';
                break;
            case '5.75':
                $zonetext = '(GMT +5:45) Kathmandu';
                break;
            case '6.0':
                $zonetext = '(GMT +6:00) Almaty, Dhaka, Colombo';
                break;
            case '7.0':
                $zonetext = '(GMT +7:00) Bangkok, Hanoi, Jakarta';
                break;
            case '8.0':
                $zonetext = '(GMT +8:00) Beijing, Perth, Singapore, Hong Kong';
                break;
            case '9.0':
                $zonetext = '(GMT +9:00) Tokyo, Seoul, Osaka';
                break;
            case '9.5':
                $zonetext = '(GMT +9:30) Adelaide, Darwin';
                break;
            case '10.0':
                $zonetext = '(GMT +10:00) Eastern Australia, Guam, Vladivostok';
                break;
            case '11.0':
                $zonetext = 'GMT +11:00) Magadan, Solomon Islands, New Caledonia';
                break;
            case '12.0':
                $zonetext = '(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka';
                break;
            default:
                $zonetext = '';
                break;
        }
        return $zonetext;
    }
    
    public function authorizeToken($SessUser, $SessId, $SessName, $token = '', $key = '')
    {
        $User_tokens = new Zend_Session_Namespace('User_tokens');
        
        $tokenkey = $SessUser . md5('adam') . $SessId . md5('guy') . $SessName;
        
        if ($User_tokens->startupkey == $tokenkey) {
            return 'true';
        } else {
            return 'false';
        }
        exit;
    }
    public function snapshot($url)
    {
        $name      = time() . ".jpg";
        // Command to execute
        $command   = 'xvfb-run --server-args="-screen 0, 1024x768x24"  /home/dbee/bin/CutyCapt';
        // Directory for the image to be saved
        // $image_dir = "--out=".$_SERVER['DOCUMENT_ROOT']."/results/";
        $image_dir = "--out=" . Front_Public_Path . "/results/";
        //echo $image_dir = "--out=".Front_Public_Path."results/";
        // Putting together the command for `shell_exec()`
        $ex        = "$command --url=$url " . $image_dir . $name;
        shell_exec($ex);
        return $name;

    }
    
    public function createdebate()
    {
        $options = '';
        $options = '<ul class="postTypeIcons">
                          <li><a href="#" rel="dbtext" data-title="db link"><i class="postSprite postText"></i><span>text</span></a></li>
                          <li><a href="#"  rel="dblink" data-title="db link"><i class="postSprite postLink"></i><span>link</span></a></li>
                          <li><a href="#"  rel="dbpix" data-title="db pic"><i class="postSprite postPix"></i><span>pic</span></a></li>
                          <li><a href="#" rel="dbvideo" data-title="db video"><i class="postSprite postVideo"></i><span>media</span></a></li>
                          <li><a href="#"  rel="dbpolls" data-title="db polls"><i class="postSprite postPolls"></i><span>poll</span></a></li>
                      </ul>';
        return $options;
    }

    function checkImgExist($img, $folder, $default, $plateform = '')
    {
        if ($img == '')
            $ProfilePic = $default;
        else if ($folder == 'userpics') {
            $ProfilePic = $img;
        } else {
            if (empty($plateform))
                $url = IMGPATH . '/' . $folder . '/' . $img;
            else if ($plateform == 'admin')
                $url = adminURL . '/' . $folder . '/' . $img;
            else
                $url = IMGPATH . '/' . $folder . '/' . $img;
            $file = @getimagesize($url);

            if (empty($file)) {
                $ProfilePic = $default;
            } else
                $ProfilePic = $img;
        }

         
        return $ProfilePic;
    }
    
    
    public function Grouphelper($groupid)
    {
        $group_obj = new Application_Model_Groups();
        $GroupRow  = $group_obj->grouprow($groupid);
        $immember  = '';
        if ($GroupRow['GroupPrivacy'] == '1')
            $bgimage = 'icon_groupopen.png';
        elseif ($GroupRow['GroupPrivacy'] == '3') {
            $bgimage = 'icon_groupreq.png';
            if ($GroupRow['User'] == $this->_userid) {
                $immember = 'true';
            } else {
                if ($this->_userid != '') {
                    $chkusermembergroup = $this->myclientdetails->getAllMasterfromtable('tblGroupMembers', array(
                        'ID',
                        'Owner'
                    ), array(
                        'GroupID' => $groupid,
                        'User' => $this->_userid,
                        'Status' => 1
                    ));
                    if (count($chkusermembergroup) > 0)
                        $immember = 'true';
                }
            }
        } else
            $bgimage = 'icon_grouppriv.png';
        // GROUP TYPE NAME
        if ($GroupRow['GroupType'] != 0 && $GroupRow['GroupType'] != '-1') {
            $GroupTypeRow  = $group_obj->getgrouptype($GroupRow['GroupType']);
            $GroupTypeName = ', ' . $GroupTypeRow['TypeName'];
        } elseif ($GroupRow['GroupType'] == '-1')
            $GroupTypeName = ', ' . $GroupRow['GroupTypeOther'];
        // GROUP TYPE NAME
        return $GroupText = '<div class="db-group-label"><div style="float:left"> <a href="' . BASE_URL . '/group/groupdetails/group/' . $groupid . '">group - ' . $GroupRow['GroupName'] . '</a>' . $GroupTypeName . '</div><div style="float:left; margin-left:5px; margin-top:-5px; width:77px; height:28px; background-image:url(images/' . $bgimage . '); background-repeat:no-repeat;"></div></div>~' . $GroupRow['GroupPrivacy'] . '~' . $immember;
        
    }
    
    public function Grouphelper1($groupid)
    {
        $group_obj = new Application_Model_Groups();
        $GroupRow  = $group_obj->grouprow($groupid);
        $immember  = false;
        if ($GroupRow['GroupPrivacy'] == '1')
            $bgimage = 'icon_groupopen.png';
        elseif ($GroupRow['GroupPrivacy'] == '3') {
            $bgimage = 'icon_groupreq.png';
            if ($GroupRow['User'] == $this->_userid) {
                $immember = 'true';
            } else {
                if ($this->_userid != '') {
                    $chkusermembergroup = $this->myclientdetails->getAllMasterfromtable('tblGroupMembers', array(
                        'ID',
                        'Owner'
                    ), array(
                        'GroupID' => $groupid,
                        'User' => $this->_userid,
                        'Status' => 1
                    ));
                    if (count($chkusermembergroup) > 0)
                        $immember = 'true';
                }
            }
        } else
            $bgimage = 'icon_grouppriv.png';
        // GROUP TYPE NAME
        if ($GroupRow['GroupType'] != 0 && $GroupRow['GroupType'] != '-1') {
            $GroupTypeRow  = $group_obj->getgrouptype($GroupRow['GroupType']);
            $GroupTypeName = ', ' . $GroupTypeRow['TypeName'];
        } elseif ($GroupRow['GroupType'] == '-1')
            $GroupTypeName = ', ' . $GroupRow['GroupTypeOther'];
        // GROUP TYPE NAME
        return $GroupText = ' <a href="' . BASE_URL . '/group/groupdetails/group/' . $groupid . '" class="pstGroupName"> (\'' . $GroupRow['GroupName'] . '\' group)</a>~' . $immember . '~' . $GroupRow['GroupPrivacy'];
        
    }
    
    public function displaybroadcastdbs()
    {
        $broadcast = $this->Myhome_Model->broadcastevent();
        
        if (count($broadcast) > 0) {
            $event = '<div class="whiteBox noCollapse active"><h2>Video Broadcast <a class="btn btn-yellow btn-rss-edit" href="' . BASE_URL . '/dbee/' . $broadcast[0]['dburl'] . '">Join</a></h2><div class="rboxContainer"> ' . stripslashes($broadcast[0]['VidDesc']) . ' <br> <br><a class="ryratnow" href="' . BASE_URL . '/dbee/' . $broadcast[0]['dburl'] . '">Register your attendence NOW</a></div></div>';
        }
        return $event;
    }
    
    public function displayEvent()
    {
        $event1      = new Application_Model_Event();
        $eventResult = $event1->getTopEvent();
        
        $timezone = '';
        
        
        if (!empty($eventResult)) {
            $count     = count($eventResult);
            $eventText = ($count == 1) ? 'Latest event' : 'Events';
            
            $event .= '<div class="whiteBox upcommingevent letstEvnt active">
        <div class="rtListOver">
          <h2 class="eventItem mymenuItem" title=""><i class="fa fa-calendar"></i>' . $eventText . '
            <span class="navAllLink"><a class="eventmore" href="javascript:void(0);">view all</a></span>
          </h2>
        </div>';
            
            $event .= '<div class="eventListSlide flexslider" id="eventItem">
          <ul class="slides">';
            foreach ($eventResult as $value) {
                if ($value['bgimage'] == '' || $value['bgcolor'] == '')
                    $link = BASE_URL . '/event/eventdetails/id/' . $value['id'];
                else
                    $link = BASE_URL . '/event/splash/id/' . $value['id'];
                
                if ($value['timezoneevent'] != '')
                    $timezone = $this->timezoneevent($value['timezoneevent']);
                
                $event .= '<li>';
                $event .= '<div class="rboxContainer" id="eventItem">';
                if ($value['logo'] != '')
                    $event .= '<a href="' . $link . '"><img src="' . BASE_URL . '/event/' . $value['logo'] . '" ></a>';
                $event .= '<h3><a href="' . $link . '">' . stripslashes($value['title']) . '</a></h3>';
                $event .= '<span>Starts: ' . date('d M Y&\nb\sp;&\nb\sp;  h:i A', strtotime($value['start'])) . '</span>';
                $event .= '<br><span>' . $timezone . '</span>';
                $event .= '</div>';
                $event .= '</li>';
                
            }
            $event .= '</ul></div></div>';
        }
        return $event;
    }

    public function displayPromotedExpertPost()
    {
        $dbeeresult = $this->Myhome_Model->getpromoteddbee();
        
        if (!empty($dbeeresult)) 
        {
            $count     = count($dbeeresult);
            $eventText = 'Featured Q&A';
            
            $event .= '<div class="whiteBox upcommingevent letstEvnt active">
        <div class="rtListOver">
          <h2 class="eventItem mymenuItem" title=""><i class="fa fa-calendar"></i>' . $eventText . '
          </h2>
        </div>';
            
        $event .= '<div class="eventListSlide flexslider" id="eventItem">
          <ul class="slides">';
            foreach ($dbeeresult as $value) 
            {  
                $event .= '<li>';
                $event .= '<div class="rboxContainer" id="eventItem">';
                $event .= '<h3><a href="' .BASE_URL . '/dbee/'.$value['dburl'].'">' . stripslashes($value['Text']) . '</a></h3>';
                $event .= '<span>Starts: ' . date('d M Y&\nb\sp;&\nb\sp;  h:i A', strtotime($value['qaschedule'])) . '</span>';
                $event .= '</div>';
                $event .= '</li>';
                
            }
            $event .= '</ul></div></div>';
        }
        return $event;
    }
    function secondsToTime($seconds)
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
    public function displayLayoutDbs($row, $Social_Content_Block = '', $from = '', $plateform_scoring = '', $adminpostscore = 0, $MyVideoEvent = "", $IsEventPage = false)
    {
        $dbeecontrollersaction = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
       
        $zonedata    = $this->myclientdetails->getAllMasterfromtable('tblDbees', array(
            'eventzone'
        ), array(
            'DbeeID' => $row['DbeeID']
        ));
        $commonbynew = new Application_Model_Commonfunctionality();
        
        if ($row['event_type'] == "3" && $IsEventPage == false)
            return; // event type 3 posts will not display any where on site
        $checkCurrentAttendes = array();

        if ($row['Type'] == 6)
        {
            $curtime = date('Y-m-d H:i:s');

            $eventStartTimeInSecond = strtotime($row['eventstart']);
            $serverTimeInSecond = strtotime($curtime);
            $videoDuration = ($eventStartTimeInSecond + $row['eventend']);
            $checkCurrentAttendes = $this->Myhome_Model->checkUserOrNotJoined($this->_userid, $row['DbeeID']);
            /*if(clientID==7)
                print_r($checkCurrentAttendes);*/
        }
        $currentTime = date('Y-m-d H:i:s');
        
        if ($row['Type'] == 6 && $serverTimeInSecond > $eventStartTimeInSecond && empty($checkCurrentAttendes)){ 
            return; 
        }else if ($row['Type'] == 6 && $serverTimeInSecond > $eventStartTimeInSecond && !empty($checkCurrentAttendes) && $checkCurrentAttendes['status']==0){ 
            return; 
        }
        
        $pendingText  = '';
        $pendingClass = '';
       
        if($this->_userid){
            $storage      = new Zend_Auth_Storage_Session();
            $session      = $storage->read();
            
            $expertlist = explode(',', $row['expertuser']);
            if (in_array($session['UserID'], $expertlist) && $row['User'] != $session['UserID']) {
                $ExpertRecQuery = $this->myclientdetails->passSQLquery("SELECT count(*) AS totalexpert FROM tblDbeeQna WHERE clientID='" . clientID . "' AND expert_id='" . $row['expertuser'] . "' AND (status = 7 OR status = 5 OR status = 3) AND  parentid=0 AND reply=0 AND dbeeid =" . $row['DbeeID']);
                $ExpertRec      = $ExpertRecQuery[0]['totalexpert'];
                if ($ExpertRec == 1) {
                    $pendingText  = $ExpertRec . ' Pending Question';
                    $pendingClass = ' pendingClass ';
                } else if ($ExpertRec != 0) {
                    $pendingText  = $ExpertRec . ' Pending Questions';
                    $pendingClass = ' pendingClass ';
                } else {
                    $pendingText  = '';
                    $pendingClass = '';
                }
            } else if (in_array($session['UserID'], $expertlist) && $row['User'] == $session['UserID']) {
                $ExpertRecQuery = $this->myclientdetails->passSQLquery("SELECT count(*) AS totalexpert FROM tblDbeeQna WHERE clientID='" . clientID . "' AND expert_id='" . $row['expertuser'] . "' AND (status = 9 OR status = 2) AND  parentid=0 AND reply=0 AND dbeeid =" . $row['DbeeID']);
                $ExpertRec      = $ExpertRecQuery[0]['totalexpert'];
                if ($ExpertRec == 1) {
                    $pendingText  = $ExpertRec . ' Pending Question';
                    $pendingClass = ' pendingClass ';
                } else if ($ExpertRec != 0) {
                    $pendingText  = $ExpertRec . ' Pending Questions';
                    $pendingClass = ' pendingClass ';
                } else {
                    $pendingText  = '';
                    $pendingClass = '';
                }
            } else if (!in_array($session['UserID'], $expertlist) && $row['User'] == $session['UserID']) {
                $ExpertRecQuery = $this->myclientdetails->passSQLquery("SELECT count(*) AS totalexpert FROM tblDbeeQna WHERE clientID='" . clientID . "' AND expert_id='" . $row['expertuser'] . "' AND (status = 4 AND status = 7 AND status = 7) AND  parentid=0 AND reply=0 AND dbeeid =" . $row['DbeeID']);
                $ExpertRec      = $ExpertRecQuery[0]['totalexpert'];
                if ($ExpertRec == 1) {
                    $pendingText  = $ExpertRec . ' Pending Question';
                    $pendingClass = ' pendingClass ';
                } else if ($ExpertRec != 0) {
                    $pendingText  = $ExpertRec . ' Pending Questions';
                    $pendingClass = ' pendingClass ';
                } else {
                    $pendingText  = '';
                    $pendingClass = '';
                }
            }
        }
         
        $CommentsNum      = 0;
        $commentago       = '';
        $agodk            = '';
        $youtubevideo     = '';
        $postpicture      = '';
        $redbee           = '';
        $deletepost       = '';
        $HideUserLabel    = '';
        $deletefavorite   = '';
        $addtofavpost     = '';
        $linkedurl        = '';
        $movetodashboard  = '';
        $dontlike         = '';
        $iamGroupMem      = 'yes';
        $dbusername       = $this->myclientdetails->customDecoding($row['Name']);
        $lname            = $this->myclientdetails->customDecoding($row['lname']);
        $agodk            = $this->agohelper($this->myclientdetails->escape($row['PostDate']));
        $CommentsNum      = $row['Comments']; //Comments
        $ActiveUsersLabel = ($row['no_of_commented_users'] == 1) ? $row['no_of_commented_users'] . " user" : $row['no_of_commented_users'] . " users";


        $type             = $this->myclientdetails->escape($row['Type']);
        $postid           = $this->myclientdetails->escape($row['DbeeID']);
        $User             = $this->myclientdetails->escape($row['UserID']);
        $url              = $this->myclientdetails->escape($row['dburl']);
        $hasexpert        = ($row['Type'] == 20) ? '<div class="listExpertLabel"><i class="fa fa-comments"></i> Q&A <span class="qaSpantxt">Discussion</span></div>' : '';
        if (!empty($this->_userid))
            $isdashboarddb = $this->getisdashboard($this->_userid, $postid);
        $url  = (!empty($url)) ? BASE_URL . '/dbee/' . $this->generateUrl($url) : BASE_URL . '/dbeedetail/home/id/' . $postid;
        $link = $this->myclientdetails->escape($row['Link']);
        if ($CommentsNum > 0) {
            $IDs        = '';
            $commentago = $this->agohelper($this->myclientdetails->escape($row['LastActivity']));
        }
        
        $profileLinkStart = ($row['Status'] == 1) ? '<a class="psUserName"  href="' . BASE_URL . '/user/' . $this->myclientdetails->customDecoding($row['Username']) . '" >' : '<a href="javascript:void(0)" class="profile-deactivated " title="' . DEACTIVE_ALT . '" onclick="return false;">';
        
        $GroupDB   = false;
        $GroupText = '';
        $GroupText2 = '';
        $popup     = 'false';
        if ($row['GroupID'] != 0 && $row['GroupID'] != '') {
            $GroupDB    = true;
            $Grouparray = explode('~', $this->grouphelper1($row['GroupID']));
            
            $GroupText = $Grouparray[0]; //'#'.$Grouparray[1].'#'.$Grouparray[2];
            if ($Grouparray[1] == true) {
                if ($Grouparray[1] == true)
                    $iamGroupMem = 'yes';
                else
                    $iamGroupMem = 'no';
            }
            if ($Grouparray[1] == '' && $Grouparray[2] == 3) {
                $iamGroupMem = 'no';
            }

            $GroupText2=str_replace("('","",$GroupText);
            $GroupText2=str_replace("' group)","",$GroupText2);

            $GroupText2='<span class="eventCalrow"> <i class="fa fa-users"></i> <b>' . $GroupText2 . '</b></span>';

        } 
        // for open popup
        if ($row['Vid'] != '' && $row['Pic'] != '') {
            $popup = 'true';
        }
        // end
        $ProfilePic = $this->checkImgExist($row['ProfilePic'], 'userpics', 'default-avatar.jpg');
        
        if (!empty($row['title']) && !empty($row['company']))
            $cmpntitle = $this->myclientdetails->customDecoding($row['title']) . ', ' . $this->myclientdetails->customDecoding($row['company']);
        else if (isset($row['title']))
            $cmpntitle = $this->myclientdetails->customDecoding($row['title']);
        else if (isset($row['company']))
            $cmpntitle = $this->myclientdetails->customDecoding($row['company']);
        $vipusertypeArr[1]  = 'delegate';
        $vipusertypeArr[2]  = 'speaker';
        $vipusertypeArr[3]  = 'sponsor';
        $vipusertypeArr[10] = 'Administrater';
        
        if($row['typename']!=""){
         $vipusertype = ($row['usertype'] != '0' && $row['usertype'] != '6') ? ' <strong class="usernameSubtext">(' . $row['typename'] . ') </strong>' : '';
        } else{
         $vipusertype = "";
        }
        
        if ($row['Type'] != 6 && $row['VidSite'] != 'dbcsp') 
        {
            if ($row['TwitterTag'] != '' && $Social_Content_Block != 'block')
                $twittertag = '<span class="twitterlistingHas"><i class="fa dbTwitterIcon fa-lg"></i> ' . $this->myclientdetails->escape($row['TwitterTag']) . '</span>';
            else
                $twittertag = '';
            
            $rssfeed = ($row['RssFeed'] != '') ? '<div class="dbRssWrapper dbRssWrapperFeed dbRssWrapperMtop" style="display:block">
            <i class="fa fa-rss fa-2x pull-left"></i>
            <div class="rssBoxCnt"> ' . $row['RssFeed'] . '</div>
            </div>' : '';
            
            if ($row['Vid'] != '')  
            {
                $mediaicon = '<div class="icon-youtube" style="margin:-2px 20px 0 -30px;; height:30px;"></div>';
                if ($row['VidSite'] == 'youtube')
                    $VideoThumbnail = '<img data-name="raj" src="https://i.ytimg.com/vi/' . $this->myclientdetails->escape($row['VidID']) . '/0.jpg" video-id="' . $this->myclientdetails->escape($row['VidID']) . '"  border="0" />';
                $VidTitle = ($row['VidTitle'] != '') ? '<h2>' . $row['VidTitle'] . '</h2>' : '';
                $VidDesc  = ($row['VidDesc'] != '') ? '<p>' . $this->myclientdetails->dbSubstring($this->myclientdetails->escape($row['VidDesc']), '250', '...') . '</p>' : '';
                
                if ($row['Type'] == 15) 
                {
                     $videofinishedcontent='';
                     $spanstyle='';
                    if($row['liveeventend'] < date('Y-m-d H:i'))
                    {
                      $class="LiveVideoFinished";
                      $videofinishedcontent='Broadcast finished.';
                      $spanstyle='display:none;';
                    }
                    if($row['eventstart'] <= date('Y-m-d H:i') && $row['liveeventend'] >= date('Y-m-d H:i'))
                    {
                        $class="LiveVideoFinished textGreen";
                        $videofinishedcontent='Broadcasting live now.';
                        $spanstyle='display:none;';
                    }

                    $eventstart = date('d M Y&\nb\sp;\a\t h:i A', strtotime($row['eventstart']));
                    if ($zonedata[0]['eventzone'] != '')
                        $timezoneevent = $commonbynew->timezoneevent($zonedata[0]['eventzone']);
                    
                    $youtubevideo = '<a href="#" ><img data-name="raj" src="' . BASE_URL . '/images/live_vid_image.png" video-id="' . $this->myclientdetails->escape($row['VidID']) . '"  border="0" /></a>';
                    $youtubemedia = '<div class="briefVideoTime"><span style="'.$spanstyle.'"><strong>Video starts:</strong> ' . $eventstart . ' <br> <span style="font-size:12px; color:#999">' . $timezoneevent . '</span></span><div class="'.$class.'">'.$videofinishedcontent.'</div></div><div class="youTubeVideoPostWrp specialDbNotPlay"><div class="youTubeVideoPost"><img src="' . BASE_URL . '/images/live_vid_image.png" border="0"><div class="videoOverlay"><div class="youtubeWarning videoClickToseeOn">Live Video Broadcast <br><div class="videoClickToseeSp"><a href="' . BASE_URL . '/dbee/' . $row['dburl'] . '">Click to view</a></div></div></div></div><div class="ytDesCnt"><p>' . $VidTitle . $VidDesc . '</p></div></div>';
                } else if ($row['VidSite'] == 'youtube') {
                    $youtubevideo = '<a href="#" >' . str_replace("http://i.ytimg.com/", "https://i.ytimg.com/", $VideoThumbnail) . '</a>';
                    $youtubemedia = '<div class="youTubeVideoPostWrp" popup="' . $popup . '"><div class="youTubeVideoPost"><a href="#" class="yPlayBtn"><i class="fa fa-play-circle-o fa-5x"></i></a>' . $youtubevideo . '</div><div class="ytDesCnt">' . $VidTitle . $VidDesc . '</div></div>';
                }
            } else if ($row['Pic'] != '') {
                $popClick = 'false';
                $picdata = $this->myclientdetails->passSQLquery("SELECT picName FROM tblDbeePics WHERE clientID='" . clientID . "' AND isDbeeDefaultPic='0'  AND     reff_key_id =" . $row['DbeeID']);
                
                $Pic      = $this->checkImgExist($row['Pic'], 'imageposts', 'default-avatar.jpg');
                $newfile  = 'imageposts/' . $Pic;
                list($width, $height) = getimagesize($newfile);
                if ($width >= 480) {
                    $finderIcon = 'fa fa-search-plus';
                    $popClick   = 'true';
                    $imgsrc     = 'imageposts/medium/' . htmlentities($Pic) . '';
                } else {
                    $finderIcon = '';
                    $imgsrc     = 'imageposts/medium/' . htmlentities($Pic);
                }
                $morepic="";
                $cnt = count($picdata);
                $picTemplate = '';
                $picTemplateClass = 'postImgThumb';
                               
                if($cnt == 0){
                    $morepic.= '<img popup="true" popup-image="' . htmlentities($Pic) . '" src="' . IMGPATH . '/' . $imgsrc . '" />';
                }else{
                     $morepic.= '<div style="background:url(' . IMGPATH . '/' . $imgsrc . ') no-repeat; background-size: cover;" class="postImgThumb" popup="true" popup-image="' . htmlentities($Pic) . '"></div>';
                }
                 
                    if($cnt>0){
                         foreach ($picdata as $key => $value) {
                        $morepic.='<div popup="true" popup-image="' . htmlentities($value['picName']) . '"  style="background:url(' . IMGPATH . '/imageposts/medium/'.htmlentities($value['picName']).') no-repeat; background-size: cover;" class="'.$picTemplateClass.'"> </div>';
                        }
                        $picTemplate ='<div class="multImgWrp imglayout_'.($cnt+1).'">'.$morepic.'</div>';
                    }else{
                        $picTemplate = $morepic;
                    }
             
                $postpicture = '<div class="pixPostWrp">'.$picTemplate.'</div>';
                $picandmedia = '<div class="pix onlyPicturePost">' . $postpicture . '</div>';
            }
            
            if ($VideoThumbnail != '' && $row['Pic'] != '') {
                $postpicture = '<div class="pixPostWrp "><a href="javascript:void(0)" popup="' . $popClick . '" popup-image="' . htmlentities($Pic) . '"><i class="' . $finderIcon . '"></i><img src="'.IMGPATH.'/imageposts/medium/' . htmlentities($Pic) . '" width="480" border="0" /></a></div>';
                $picandmedia = '<div class="pix picMediaComboWrp">' . $youtubemedia . $postpicture . '</div>';
            } else if ($VideoThumbnail != '') {
                $picandmedia = $youtubemedia;
            }
            if ($row['LinkTitle'] != '') {
                $linkedurl = '<div class="makelinkWrp">
            <div class="makelinkDes otherlinkdis" style="margin-left:0px;"><h2>' . $row['LinkTitle'] . '</h2><div class="desc">' . $row['LinkDesc'] . '</div><div class="makelinkshw"><a target="_blank" href="' . $row['Link'] . '">' . $row['Link'] . '</a></div></div></div>';
            }
            
            
            if ($row['Cats'] != '' && $row['Cats'] != '0' && $row['Cats'] != 'undefined') {
                $showcategories        = '';
                $categoriestooltip     = '';
                $showcategoriestooltip = '';
                $slicecat              = explode('#', $row['catname']);
                // echo count($slicecat); die;
                if (count($slicecat) > 1) {
                    $i       = 1;
                    $catname = '';
                    $catId   = '';
                    foreach ($slicecat as $key => $value) {
                        $slicecat2 = explode(':', $value);
                        
                        if ($i == 1) {
                            
                            $catname = $slicecat2[0];
                            $catId   = $slicecat2[1];
                            
                        } else {
                            $showcategoriestooltip .= $slicecat2['0'] . ', ';
                        }
                        $i++;
                    }
                    
                    
                }
                
                
                $other = " others";
                
                if (count($slicecat) == 1) {
                    $slicecat2      = explode(':', $row['catname']);
                    $showcategories = 'in <span class="catfindval" value="' . $slicecat2['1'] . '" >' . $slicecat2['0'] . '</span>';
                    
                } else {
                    if (count($slicecat) > 2)
                        $other = " others";
                    else
                        $other = " other";
                    
                    $showcategories = 'in <span class="catfindval" value="' . $catId . '"  >' . $catname . '</span> and <strong rel="dbTip" title="' . trim($showcategoriestooltip, ', ') . '" style="color:#2190BB">' . (count($slicecat) - 1) . $other . '  </strong>';
                }
            }
            if ($row['DbTag'] != '') {
                $hashtag    = '';
                $hasExplode = explode(',', $row['DbTag']);
                foreach ($hasExplode as $key => $value) {
                    if (!empty($value))
                        $hashtag .= '<span class="commonhashtag"> <a href="' . BASE_URL . '/myhome/hashtag/tag/' . $value . '"> #' . $value . '</a> </span>';
                }
            }
            
            if (!empty($row['Attachment']))
                $attachments = '<div rel="dbTip" class="paperClipICon" title="File attached"></div>';
        
            $ismobile = preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
            $currentdate = date('Y-m-d H:i:s', time());

            if ($iamGroupMem == 'yes' && ismobile == '' && $row['Type']!=20) {
                $commentcontainer = '<div id="listingCommentLatest' . $row['DbeeID'] . '" class="listingCommentLatest">' . $this->topdbeecomments($row['DbeeID'], $User, $plateform_scoring, $adminpostscore, $row['GroupID'], $row['events'], $row['User']) . '</div>';
            }
            else if ($row['Type']==20 && strtotime($currentdate)<strtotime($row['qaschedule'])) 
            {
                $t = $this->secondsToTime((strtotime($row['qaschedule'])-strtotime($currentdate)));

                
                if($t['h']==1){
                    $expire .= $t['h']." hour, ";
                }
                else if($t['h']!=0){
                    $expire .= $t['h']." hours, ";
                }
                if($t['m']==1){
                    $expire .= $t['m']." minute, ";
                }
                else if($t['m']!=0){
                    $expire .= $t['m']." minutes, ";
                }
                if($t['s']==1){
                    $expire .= $t['s']." second ";
                }
                else if($t['s']!=0){
                    $expire .= $t['s']." seconds ";
                }
                
                $commentcontainer = '<div id="listingCommentLatest' . $row['DbeeID'] . '" class="listingCommentLatest" style="padding:5px">Q&A will be live in '.$expire.'</div>';
            }
            else if ($row['Type']==20 && strtotime($currentdate)>strtotime($row['qaendschedule'])) 
            {
               $commentcontainer = '<div id="listingCommentLatest' . $row['DbeeID'] . '" class="listingCommentLatest" style="padding:5px">This Q&A is now closed.</div>';
            }
            else if (ismobile == '' && $row['Type']!=20) 
            {
                $commentcontainer = '<div id="listingCommentLatest' . $row['DbeeID'] . '" class="listingCommentLatest" style="padding:5px">You are not currently a member of this group</div>';
            }
            $redbee = ($User == $this->_userid) ? '' : '<li><a href="javascript:void(0)"  onclick="javascript:redbee(' . $postid . ',' . htmlentities($User) . ')" ><i class="sprite psListRedbIcon"></i>Repost</a></li>';
            if ($row['User'] != $this->_userid && $row['usertype'] != 10 && $dbeecontrollersaction=='fetchdbee')
                $HideUserLabel = '<li><a href="javascript:void(0)"  onclick="javascript:fillhideuserdbpopup(' . $row['User'] . ')"><i class="sprite psListRmUserIcon"></i>Hide user</a></li>';
            
            if ($CommentsNum == 0 && $row['UserID'] == $this->_userid) {
                $deletepost = '<li><a href="javascript:void(0)"  class=" deletemydblink" onclick="javascript:filldeletedbcontrols(' . $row['DbeeID'] . ')"><i class="sprite psListRmUserIcon"></i>Delete post</a></li>';
            }

            if ($row['UserID'] == $this->_userid) {
                $editpost = '<li><a href="javascript:void(0)"  class="editdb" id="' . $row['DbeeID'] . '"><i class="sprite psListRmUserIcon"></i>Edit post</a></li>';
            }
            
            if ($isdashboarddb == false && $row['User'] != $this->_userid && $this->_userid != ''  )
                $movetodashboard = '<li><a href="javascript:void(0)" class="addtofav-dbmain movetodashboard" cat="' . $getcatsname[0]['CatName'] . '" dbusr="' . $row['User'] . '" dbusrname="' . $dbusername . ' ' . $lname . '" data-id="' . $row['DbeeID'] . '" type="1">Move to Dashboard</a></li>';
            
            if ($isdashboarddb == true && $row['User'] != $this->_userid && $this->_userid != '')
                $movetodashboard = '<li><a href="javascript:void(0)" class="disabled" >Moved to dashboard</a></li>';
            
            if ($row['User'] != $this->_userid && $dbeecontrollersaction=='fetchdbee')
                $dontlike = '<li><a href="javascript:void(0)" class="addtofav-dbmain movetodashboard" data-id="' . $row['DbeeID'] . '" type="2">I don\'t want to see this</a></li>';
            
            
            if ($from == 'group' && !empty($row['GroupID']) && $this->_userid == $User)
                $redbee = '';
        }
        else if ($row['VidSite'] == 'youtube') 
        {

             if ($serverTimeInSecond > $videoDuration)
            {
                $eventStartText = '<div class="youtubeWarning videoClickToseeOn">Available to rewatch<br /><div class="videoClickToseeSp"><a href="' . $url . '" >Click to view</a></div></div>';
            }
            else
            {
                $t = $this->secondsToTime(($videoDuration-$serverTimeInSecond));
                if($t['h']==1){
                    $expire .= $t['h']." hour, ";
                }
                else if($t['h']!=0){
                    $expire .= $t['h']." hours, ";
                }
                if($t['m']==1){
                    $expire .= $t['m']." minute, ";
                }
                else if($t['m']!=0){
                    $expire .= $t['m']." minutes, ";
                }
                if($t['s']==1){
                    $expire .= $t['s']." second ";
                }
                else if($t['s']!=0){
                    $expire .= $t['s']." seconds ";
                }
                $expire = " <br/><span class='VideoTimeCount'>".$expire."</span>";
                $eventStartText = '<div class="youtubeWarning">Video broadcast starting in '.$expire.'</div>';
            }
            
            $VideoThumbnail = '<img src="https://i.ytimg.com/vi/' .$row['Vid']. '/0.jpg"   border="0" />';
            
            $VidDesc = ($row['VidDesc'] != '') ? '<p>' . $this->myclientdetails->dbSubstring($this->myclientdetails->escape($row['VidDesc']), '250', '...') . '</p>' : '';
            
            $picandmedia = '<div class="youTubeVideoPostWrp specialDbNotPlay"><div class="youTubeVideoPost">' . $VideoThumbnail . '<div class="videoOverlay">' . $eventStartText . '</div></div></div>';
            unset($t);unset($expire);
        }
        else if ($row['VidSite'] == 'dbcsp' && $row['Type'] == 6) 
        {
           

            if ($serverTimeInSecond > $videoDuration)
            {
                $eventStartText = '<div class="youtubeWarning videoClickToseeOn">Available to rewatch<br /><div class="videoClickToseeSp"><a href="' . $url . '" >Click to view</a></div></div>';
            }
            else
            {
                $t = $this->secondsToTime(($videoDuration-$serverTimeInSecond));
                if($t['h']==1){
                    $expire .= $t['h']." hour, ";
                }
                else if($t['h']!=0){
                    $expire .= $t['h']." hours, ";
                }
                if($t['m']==1){
                    $expire .= $t['m']." minute, ";
                }
                else if($t['m']!=0){
                    $expire .= $t['m']." minutes, ";
                }
                if($t['s']==1){
                    $expire .= $t['s']." second ";
                }
                else if($t['s']!=0){
                    $expire .= $t['s']." seconds ";
                }
                $expire = " <br/><span class='VideoTimeCount'>".$expire."</span>";
                $eventStartText = '<div class="youtubeWarning">Video broadcast starting in '.$expire.'</div>';
            }
            $VideoThumbnail = '<div class="youVideoPost">
                              <a class="yPlayBtn"  href="#"><i class="fa fa-play-circle-o fa-5x"></i></a>
                              <a href="javascript:void(0);" ><img border="0"  video-id="' . $row['Vid'] . '" src="' . BASE_URL . '/timthumb.php?src=/adminraw/knowledge_center/video_' . clientID . '/' . $row['Vid'] . '.jpg&amp;q=100&amp;w=130" ></a></div>';
            $picandmedia = '<div class="youTubeVideoPostWrp specialDbNotPlay"><div class="youTubeVideoPost">' . $VideoThumbnail . '<div class="videoOverlay">' . $eventStartText . '</div></div></div>';
            unset($expire);unset($t);
        }
        else if ($row['VidSite'] == 'dbcsp' && $row['Type'] != 6) 
        {
            $VideoThumbnail = '<div class="youVideoPost">
                              <a class="yPlayBtn"  href="#"><i class="fa fa-play-circle-o fa-5x"></i></a>
                              <a href="javascript:void(0);" ><img border="0"  video-id="' . $row['Vid'] . '" src="' . BASE_URL . '/timthumb.php?src=/adminraw/knowledge_center/video_' . clientID . '/' . $row['Vid'] . '.jpg&amp;q=100&amp;w=130" ></a></div>';
            $picandmedia = '<div class="youTubeVideoPostWrp specialDbNotPlay"><div class="youTubeVideoPost">' . $VideoThumbnail . '</div></div>';
        }


        if ($row['Type'] == '5') 
        {
            $stats            = $this->Pollhelper($row['DbeeID']);
            $dotstext         = $this->myclientdetails->dbSubstring($this->myclientdetails->escape($row['PollText']), '250', '...');
            $commentcontainer = '';
        } else
            $dotstext = $this->myclientdetails->dbSubstring($this->myclientdetails->escape($row['Text']), '250', '...');
        
        if ($row['Type'] == 9 || $row['Type'] == 7 || $row['Type'] == 15) {
            $remaingcontentEvent='';
            $eventDtl  = $this->myclientdetails->getfieldsfromtable(array(
                'title'
            ), 'tblEvent', 'id', $row['events']);
            $EventText = '<a href="' . BASE_URL . '/event/eventdetails/id/' . $row['events'] . '">' . $eventDtl[0]['title'] . '</a>';

            if ($eventDtl[0]['title'] != '' )
                $remaingcontentEvent =  '<span class="eventCalrow"> <i class="fa fa-calendar " ></i> <b>' . $EventText . '</b></span>';
            
        }
        
        
        if ($row['Type'] == 7) 
        {
            $scoreonoff = $this->myclientdetails->getRowMasterfromtable('tblAdminSettings', array(
                'IsSurveysOn'
            ), array(
                'clientID' => clientID
            ));
            $dotstext   = $this->myclientdetails->dbSubstring($this->myclientdetails->escape($row['surveyTitle']), '250', '...');
            
            if ($scoreonoff['IsSurveysOn'] == 1) 
            {
                return $this->servey_displaydbs($row['ProfilePic'], $profileLinkStart, $this->myclientdetails->escape($dbusername), $agodk, $commentago, $CommentsNum, $row['surveyTitle'], $row['surveyPdf'], $dotstext, $row['DbeeID'], $twittertag, $ActiveUsersLabel, $row['User'], $HideUserLabel, '', '', $rssfeed, $dbstate, $dbstatetitle, $GroupText, $row['GroupID'], $row['ReDBUsers'], $row['dburl'], $row['Attachment'], $MyVideoEvent,$remaingcontentEvent);
            } else {
                return '';
            }
            exit;
        }
        
        
        $groupeTextWrapper = '';
        $remaingcontent    = '';
        if ($linkedurl != '' || $stats != '' || $hashtag != '' || $rssfeed != '')
            $groupeTextWrapper = '<div class="groupeTextWrapper">' . $linkedurl . $stats . $rssfeed . $hashtag . '</div>';
        

        if ($groupeTextWrapper != '' || $twittertag != '')
            $remaingcontent = '<div class="listText">' . $groupeTextWrapper . ' </div></div>';
        
        $useractions = $addtofavpost . $redbee . $deletepost . $editpost . $HideUserLabel . $movetodashboard . $dontlike;
        

		if ($useractions != '' && $this->_userid != '') {
            
            $useractionarrow = '<div class="userActionArrow">
              <span class="fa fa-angle-down fa-lg"></span>
              <ul>' . $useractions . '</ul>
            </div>';
        }
        $usrArr = array(
            'Username',
            'ProfilePic',
            'usertype',
            'Name',
            'lname',
            'Status'
        );
        
        $ParrentId   = 0;
        $ParrentType = 0;
        if ($row['GroupID'] != 0 && $row['GroupID'] != '') {
            $ParrentId   = $row['GroupID'];
            $ParrentType = '1';
        }
        if ($row['events'] != 0 && $row['events'] != '') {
            $ParrentId   = $row['events'];
            $ParrentType = '2';
        }
        
        $ArticleId   = $postid;
        $ArticleType = 0;
        
        $dbeeobj     = new Application_Model_Dbeedetail();
        $recordfound = $dbeeobj->CheckInfluence($row['User'], $ParrentId, $ParrentType, $ArticleId, $ArticleType, $this->_userid);
        $class       = '';
        if (count($recordfound) > 0) {
            $class = 'active';
        }
        
        $pic3         = $this->checkImgExist($ProfilePic, 'userpics', 'default-avatar.jpg');
        $removebutton = '';
        if($this->_userid){
            $MyLiveVideoEvent = $this->myclientdetails->getRowMasterfromtable('tblDbeeJoinedUser', array(
                    'dbeeID'
                ), array(
                    'clientID' => clientID,'dbeeID' => $postid,'userID' => $this->_userid
                ));

            if ($MyLiveVideoEvent['dbeeID'] == $postid) {
                
                if ($row['Type'] == 15) {
                    $removebutton = '<a class="btn btn-red btn-mini pull-left removeVideoAttendes" data-videoeventidxx="' . $postid . '" href="javascript:void(0);">Remove attendance</a>';
                } else if (adminID != $row['User']) {
                    $removebutton = '<a class="btn btn-red btn-mini pull-left removeVideoAttendee" data-videoeventidxx="' . $postid . '" href="javascript:void(0);">Remove attendance</a>';
                }
            }
        }
        $userarray = $this->myclientdetails->getAllMasterfromtable('tblUsers', array(
            'hideuser'
        ), array(
            'UserID' => $row['UserID']
        ));

        $redbeedetail = '';
        if ($row['ReDBUsers'] != '') {
            $redbeearray   = $this->myclientdetails->getAllMasterfromtable('tblUsers', array(
                'UserID',
                'Name',
                'lname',
                'Username',
                'ProfilePic',
                'usertype',
                'company',
                'title',
                'usertype',
                'hideuser'
            ), array(
                'UserID' => $row['ReDBUsers']
            ));
            $redbeedetail  = '';
            $redbeedate    = '';
            $redbeedetail2 = '  re' . POST_NAME . ' '; //.$Name.'db';
            $pic2          = $this->checkImgExist($ProfilePic, 'userpics', 'default-avatar.jpg');
            
            if ($row['hideuser'] == 1 && $session['role'] != 1 && $row['UserID'] != $session['UserID']) {
                
                $redbeedetail .= '<div class="next-line"></div><div id="redbee"><img src="'.IMGPATH.'/users/small/' . HIDEUSERPIC . '" width="21" height="21" border="0" /><span>' . HIDEUSER . ' ' . $vipusertype . '</span><div class="rePostComnt"><i class="titlencompany"> ' . $cmpntitle . ' </i><i class="misRepostDate">' . $agodk . ' <b> ' . $showcategories . ' </b></i></div></div>';
            } else {
                $redbeedetail .= '<div class="next-line"></div><div id="redbee" ' . $row['usertype'] . '>' . $profileLinkStart . '<img src="'.IMGPATH.'/users/small/' . $pic2 . '" style="width:21px" border="0" /><span>' . $dbusername . ' ' . $lname . '' . $vipusertype . '</span></a><div class="rePostComnt"><i class="titlencompany"> ' . $cmpntitle . ' </i><i class="misRepostDate">' . $agodk . ' <b> ' . $showcategories . ' </b></i></div></div>';
                
            }
            
            $redbDate = $this->myclientdetails->getAllMasterfromtable('tblReDbees', array(
                'ReDBDate'
            ), array(
                'DbeeID' => $postid,
                'DbeeOwner' => $User,
                'ReDBUser' => $row['ReDBUsers']
            ));
            
            $agodk          = $this->Agohelper($redbDate[0]['ReDBDate']);
            $ProfilePic     = $this->checkImgExist($redbeearray[0]['ProfilePic'], 'userpics', 'default-avatar.jpg');
            $showcategories = '';
            if ($ProfilePic == '')
                $ProfilePic = 'default-avatar.jpg';
            $Name             = $this->myclientdetails->customDecoding($redbeearray[0]['Name']);
            $lname            = $this->myclientdetails->customDecoding($redbeearray[0]['lname']);
            $profileLinkStart = '<a href="' . BASE_URL . '/user/' . $this->myclientdetails->customDecoding($redbeearray[0]['Username']) . '" >';
            $usrDetails       = htmlentities($Name) . ' ' . htmlentities($lname);
        } else {
            
            $usrDetails = htmlentities($dbusername) . ' ' . htmlentities($lname) . $vipusertype . '<span class="titlencompany"> ' . $cmpntitle . ' </span>';
        }
        
        
        $postreturn .= '
        <li class="listingTypeMedia ' . $class . ' ' . $pendingClass . ' " id="dbee-id-' . $postid . '">' . $attachments . $schedulePost . '
        <div class="postListContent">
        <div class="pstListTitle"><div class="psUserName userNameRow">';
        
        if ($redbeearray[0]['hideuser'] == 1 && $session['role'] != 1 && $row['ReDBUsers'] != "" && $row['UserID'] != $session['UserID']) {
            $postreturn .= '<div class="userPicLink">
          <img src="'.IMGPATH.'/users/small/' . htmlentities(HIDEUSERPIC) . '" border="0" /></div>
          <div class="psName">
               <span class="usrNameMn">' . HIDEUSER . ' ' . $vipusertype . ' ' . $redbeedetail2 . '</span>';
        } else if ($userarray[0]['hideuser'] == 1 && $session['role'] != 1 && $row['UserID'] != $session['UserID']) {
            
            $postreturn .= '<div class="userPicLink">
          <img src="'.IMGPATH.'/users/small/' . htmlentities(HIDEUSERPIC) . '" border="0" /></div>
          <div class="psName">
               <span class="usrNameMn">' . HIDEUSER . ' ' . $vipusertype . ' ' . $redbeedetail2 . '</span>';
            
        } else {
            $postreturn .= '<div class="userPicLink" data-rw="' . $rw['usertype'] . '" data-ses="' . $session['usertype'] . '">' . $profileLinkStart . '
          <img src="'.IMGPATH.'/users/small/' . htmlentities($pic3) . '" border="0" /></a></div>
          <div class="psName">
              ' . $profileLinkStart . ' <span class="usrNameMn">' . $usrDetails . '' . $redbeedetail2 . '</span></a>';
        }
        
        $postreturn .= '<div class="missPostComnt"><i>' . $agodk . ' <b> ' . $showcategories . ' </b> </i></div>
              </div>
              </div>
  
              <div class="pull-right postHist">
        ' . $useractionarrow . $hasexpert . '</div>
                    ' . $redbeedetail . '
                    </div>';
        
        if ($row['TaggedUsers'] == "") {
            $postreturn .= '<div class="listTxtNew">' . $this->addLinks($dotstext) . '</div>';
        } else {
            
            $postreturn .= '<div class="listTxtNew">' . $row['Text'] . '</div>';
            
            
        }
        $postreturn .= $picandmedia . '
        ' . $remaingcontent . '
        </div>';
        if ($pendingText != '') {
            $pendingTxt = '<span class="pendingTxt">' . $pendingText . '</span>';
        }
        $moreBtn = '<a class="btn btn-yellow btn-mini joinDiscusstion" href="' . $url . '">more</a>';
        
        $cmnt = '<a href="javascript:void(0);" class="cmntOpen"  dbid="' . $postid . '" eventid="' . $row['events'] . '" groupid="' . $row['GroupID'] . '" dbowner="' . $User . '"><i class="fa fa-comment"></i> Comment</a>';
        if ($row['Type'] == 5 || $row['Type'] == 6 || $row['Type'] == 15) {
            $cmnt = '';
        }
        if (ismobile != '') {
            $cmntBar = '<div class="cmntNewBarOndbs pstBriefFt">' . $cmnt . '
          <a class="joinDiscusstion" href="' . $url . '"><i class="fa fa-arrow-circle-right"></i> More</a>
        </div>';
        }
        if($row['Type']==6)
        {
            $result_dbee_attendies = $this->Myhome_Model->attendies($row['DbeeID']);
            
            $countAttendies = count($result_dbee_attendies);
            $ActiveUsersLabel = ($countAttendies == 1) ? $countAttendies . " attendee" : $countAttendies . " attendees";
            unset($result_dbee_attendies);
            //print_r($checkCurrentAttendes);
            if($row['eventtype']==0 && empty($checkCurrentAttendes)){
                $commentCount = '<a href="javascript:void(0);" data-list="true" data-type="'.$row['eventtype'].'" data-id="'.$row['DbeeID'].'" class="btn btn-yellow btn-mini joinRequest">Join</a>';
            }else if($row['eventtype']==0 && $checkCurrentAttendes['status']==1){
                $commentCount = '<a href="javascript:void(0);" class="btn btn-yellow btn-mini videoJoined"><i class="fa fa-check"></i>Joined</a>';
            }
            else if($row['eventtype']==1 && empty($checkCurrentAttendes)){
                $commentCount = '<a href="javascript:void(0);" data-id="'.$row['DbeeID'].'" class="btn btn-yellow btn-mini joinRequest" data-type="'.$row['eventtype'].'" data-list="true">Request to join</a>';
            }
            else if($row['eventtype']==1 && !empty($checkCurrentAttendes) && $checkCurrentAttendes['status']==0){
                $commentCount = '<a href="javascript:void(0);" class="btn btn-yellow btn-mini videoJoined" ><i class="fa fa-check"></i>Request sent</a>';
            }
            else if($row['eventtype']==1 && !empty($checkCurrentAttendes) && $checkCurrentAttendes['status']==1){
                $commentCount = '<a href="javascript:void(0);" class="btn btn-yellow btn-mini videoJoined" ><i class="fa fa-check"></i>Joined</a>';
            }
            $ActiveUsers .= '<i class="fa fa-user"></i><strong class="aucb attendeesCount"> ' . $ActiveUsersLabel . '</strong>  ';
        }else{
            $commentCount = '<span> <i class="fa fa-comment-o " ></i> <strong>' . $CommentsNum . '</strong></span> ';
            $ActiveUsers .= '<i class="fa fa-user"></i> <strong class="auc"> ' . $ActiveUsersLabel . '</strong>  ';
        }
        $postreturn .= '<div class="psListingFt">
          <span class="activeUserCount">' . $ActiveUsers .$commentCount. $remaingcontentEvent . ' ' . $GroupText2 . '</span>' . $pendingTxt . '
          ' . $twittertag . '
            <div class="pull-right">' . $removebutton . $moreBtn . $commentBtn . '
          </div>
        </div>' . $commentcontainer . '
        ' . $cmntBar . '
          <div class="clearfix"></div>
          </li>';
        return $postreturn;
    }
    
    
    
    public function servey_displaydbs($ProfilePic, $profileLinkStart, $Name, $agodk, $commentago, $CommentsNum, $surveyTitle, $surveyPdf, $dotstext, $favid, $twittertag, $ActiveUsersLabel, $User, $HideUserLabel = '', $deletepost = '', $from = '', $rssfeed = '', $dbstate, $dbstatetitle, $GroupText, $GroupId = '', $redbees = '', $url = '', $attachfile = '', $extraparam = "", $contentEvent="")
    {
        
        if (!empty($url))
            $url = BASE_URL . '/dbee/' . $this->generateUrl($url);
        else
            $url = BASE_URL . '/dbeedetail/home/id/' . $favid;
        
        $SurveyAnswer = $this->myclientdetails->getAllMasterfromtable('tblSurveyAnswer', array(
                'answer_id'
            ), array(
                'dbeeid' => $favid,
                'UserID' => $this->_userid
            ));

        $myhomeObj = new Application_Model_Myhome();
        if (!empty($attachfile) || !empty($surveyPdf)) {
            $attachments = '<div class="paperClipICon" rel="dbTip" title="File attached"></div>';
        }
        $return .= '<li id="dbee-id-' . $favid . '" class="SurveyDbeePostWrp">' . $attachments . '<div class="postListContent">
            <div class="pstListTitle">
              <div class="psName psUserName">
                <span>Survey</span>
                <i>' . $agodk . '</i>
              </div>
          </div>';
        $DisclaimerText='';
        $return .= '<div class="listTxtNew">' . strip_tags(htmlspecialchars_decode($surveyTitle)) . '';
        if (count($SurveyAnswer) > 0) {
            $return .= '</div><div class="psListingFt"><span class="completedSurvey"><i class="fa fa-check-circle-o fa-lg"></i> You have already completed this survey<span> <a class="btn btn-yellow btn-mini pull-right" href="' . $url . '">more</a></div>'.$DisclaimerText.'</div></div>';
        } else {
            $return .= '</div><div class="psListingFt"><span class="activeUserCount">' .$contentEvent . '</span>            
            <a class="btn btn-yellow btn-mini pull-right" href="' . $url . '">Take survey</a></div>'.$DisclaimerText.'</div></div>';
        }
        $return .= '
    <div class="clearfix"></div>
    </li>';
        return $return;
    }
    
    
    public function imagefromlink($url, $name)
    {
        // Command to execute
        $command   = "/usr/bin/wkhtmltoimage-amd64 --load-error-handling ignore";
        // Directory for the image to be saved
        $image_dir = "/home/dbee/public_html/new/branches/public/imageposts/";
        // Putting together the command for `shell_exec()`
        $ex        = "$command $url " . $image_dir . $name;
        // Generate the image
        // NOTE: Don't forget to `escapeshellarg()` any user input!
        $output    = shell_exec($ex);
        return '1';
    }
    
    /*testing*/
    public function Commentcount($dbeeid)
    {
        $comment_obj = new Application_Model_Comment();
        $count       = $comment_obj->totacomment($dbeeid);
        return $count;
    }
    public function Agohelper($date)
    {
        $currentdate = date('Y-m-d H:i:s', time());
        $start_date  = new DateTime($currentdate);
        
        $since_start = $start_date->diff(new DateTime($date));
        if ($since_start->y > 0) {
            $ago = ($since_start->y > 1) ? $since_start->y . ' years ago' : $since_start->y . ' year ago';
        } else if ($since_start->m > 0) {
            $ago = ($since_start->m > 1) ? $since_start->m . ' months ago' : $since_start->m . ' month ago';
        } else if ($since_start->d > 0) {
            $ago = ($since_start->d > 1) ? $since_start->d . ' days ago' : $since_start->d . ' day ago';
        } else if ($since_start->h > 0) {
            $ago = ($since_start->h > 1) ? $since_start->h . ' hours ago' : $since_start->h . ' hour ago';
        } else if ($since_start->i > 0) {
            $ago = ($since_start->i > 1) ? $since_start->i . ' mins ago' : $since_start->i . ' min ago';
        } else if ($since_start->s > 0) {
            $ago = ($since_start->s > 1) ? $since_start->s . ' secs ago' : $since_start->s . ' sec ago';
        }
        
        if (!empty($ago)) {
            return $ago;
        } else {
            
            $diff = ($this->_date_diff(time(), strtotime($date)));
            if ($diff[y] != 0)
                $ago = ($diff[y] > 1) ? $diff[y] . ' years ' : $diff[y] . ' year ';
            elseif ($diff[m] != 0)
                $ago = ($diff[m] > 1) ? $diff[m] . ' months ' : $diff[m] . ' month ';
            elseif ($diff[d] != 0)
                $ago = ($diff[d] > 1) ? $diff[d] . ' days ' : $diff[d] . ' day ';
            elseif ($diff[h] != 0)
                $ago = ($diff[h] > 1) ? $diff[h] . ' hours ' : $diff[h] . ' hour ';
            elseif ($diff[i] != 0)
                $ago = ($diff[i] > 1) ? $diff[i] . ' minutes ' : $diff[i] . ' minute ';
            elseif ($diff[s] != 0)
                $ago = ($diff[s] > 1) ? $diff[s] . ' seconds ' : $diff[s] . ' second ';
            $ago .= ' ago';
            return $ago;
        }
    }
    
    
    function _date_diff($one, $two)
    {
        $invert = false;
        if ($one > $two) {
            list($one, $two) = array(
                $two,
                $one
            );
            $invert = true;
        }
        
        $key = array(
            "y",
            "m",
            "d",
            "h",
            "i",
            "s"
        );
        $a   = array_combine($key, array_map("intval", explode(" ", date("Y m d H i s", $one))));
        $b   = array_combine($key, array_map("intval", explode(" ", date("Y m d H i s", $two))));
        
        $result           = array();
        $result["y"]      = $b["y"] - $a["y"];
        $result["m"]      = $b["m"] - $a["m"];
        $result["d"]      = $b["d"] - $a["d"];
        $result["h"]      = $b["h"] - $a["h"];
        $result["i"]      = $b["i"] - $a["i"];
        $result["s"]      = $b["s"] - $a["s"];
        $result["invert"] = $invert ? 1 : 0;
        $result["days"]   = intval(abs(($one - $two) / 86400));
        
        if ($invert) {
            $this->_date_normalize($a, $result);
        } else {
            $this->_date_normalize($b, $result);
        }
        
        return $result;
    }
    
    function _date_normalize($base, $result)
    {
        $result = $this->_date_range_limit(0, 60, 60, "s", "i", $result);
        $result = $this->_date_range_limit(0, 60, 60, "i", "h", $result);
        $result = $this->_date_range_limit(0, 24, 24, "h", "d", $result);
        $result = $this->_date_range_limit(0, 12, 12, "m", "y", $result);
        
        $result = $this->_date_range_limit_days($base, $result);
        
        $result = $this->_date_range_limit(0, 12, 12, "m", "y", $result);
        
        return $result;
    }
    
    function _date_range_limit($start, $end, $adj, $a, $b, $result)
    {
        if ($result[$a] < $start) {
            $result[$b] -= intval(($start - $result[$a] - 1) / $adj) + 1;
            $result[$a] += $adj * intval(($start - $result[$a] - 1) / $adj + 1);
        }
        
        if ($result[$a] >= $end) {
            $result[$b] += intval($result[$a] / $adj);
            $result[$a] -= $adj * intval($result[$a] / $adj);
        }
        
        return $result;
    }
    
    function _date_range_limit_days($base, $result)
    {
        $days_in_month_leap = array(
            31,
            31,
            29,
            31,
            30,
            31,
            30,
            31,
            31,
            30,
            31,
            30,
            31
        );
        $days_in_month      = array(
            31,
            31,
            28,
            31,
            30,
            31,
            30,
            31,
            31,
            30,
            31,
            30,
            31
        );
        
        $this->_date_range_limit(1, 13, 12, "m", "y", $base);
        
        $year  = $base["y"];
        $month = $base["m"];
        
        if (!$result["invert"]) {
            while ($result["d"] < 0) {
                $month--;
                if ($month < 1) {
                    $month += 12;
                    $year--;
                }
                
                $leapyear = $year % 400 == 0 || ($year % 100 != 0 && $year % 4 == 0);
                $days     = $leapyear ? $days_in_month_leap[$month] : $days_in_month[$month];
                
                $result["d"] += $days;
                $result["m"]--;
            }
        } else {
            while ($result["d"] < 0) {
                $leapyear = $year % 400 == 0 || ($year % 100 != 0 && $year % 4 == 0);
                $days     = $leapyear ? $days_in_month_leap[$month] : $days_in_month[$month];
                
                $result["d"] += $days;
                $result["m"]--;
                
                $month++;
                if ($month > 12) {
                    $month -= 12;
                    $year++;
                }
            }
        }
        
        return $result;
    }
    public function Usercnt($dbeeid)
    {
        $comment_obj = new Application_Model_Comment();
        $count       = $comment_obj->totacommentgroup($dbeeid);
        return $count;
    }
    public function Dbeeicon($CommentsNum = 0, $Type)
    {
        
        if ($Type != '5') {
            // SELECT NUMBER OF COMMENTS FOR THIS DBEE      
            if ($CommentsNum >= 0 && $CommentsNum <= 5) {
                $dbstate      = 'icon-db-cool';
                $dbstatetitle = 'cool ' . POST_NAME . '';
            } elseif ($CommentsNum >= 6 && $CommentsNum <= 10) {
                $dbstate      = 'icon-db-warm';
                $dbstatetitle = 'warm ' . POST_NAME . '';
            } elseif ($CommentsNum >= 11 && $CommentsNum <= 20) {
                $dbstate      = 'icon-db-hot';
                $dbstatetitle = 'hot ' . POST_NAME . '';
            } elseif ($CommentsNum > 20) {
                $dbstate      = 'icon-db-burning';
                $dbstatetitle = 'burning ' . POST_NAME . '';
            }
            
        } else {
            //----- TOTAL VOTES ---------//   
            if ($CommentsNum >= 0 && $CommentsNum <= 5) {
                $dbstate      = 'icon-db-cool';
                $dbstatetitle = 'cool ' . POST_NAME . '';
            } elseif ($CommentsNum >= 6 && $CommentsNum <= 10) {
                $dbstate      = 'icon-db-warm';
                $dbstatetitle = 'warm ' . POST_NAME . '';
            } elseif ($CommentsNum >= 11 && $CommentsNum <= 20) {
                $dbstate      = 'icon-db-hot';
                $dbstatetitle = 'hot ' . POST_NAME . '';
            } elseif ($CommentsNum > 20) {
                $dbstate      = 'icon-db-burning';
                $dbstatetitle = 'burning ' . POST_NAME . '';
            }
        }
        
        $iconarr = array(
            $dbstate,
            $dbstatetitle
        );
        
        return $iconarr;
    }
    public function Commentagohelper($date)
    {
        $ago  = '';
        $diff = ($this->_date_diff(time(), strtotime($date)));
        if ($diff[y] != 0)
            $ago = ($diff[y] > 1) ? $diff[y] . ' years ' : $diff[y] . ' year ';
        elseif ($diff[m] != 0)
            $ago = ($diff[m] > 1) ? $diff[m] . ' months ' : $diff[m] . ' month ';
        elseif ($diff[d] != 0)
            $ago = ($diff[d] > 1) ? $diff[d] . ' days ' : $diff[d] . ' day ';
        elseif ($diff[h] != 0)
            $ago = ($diff[h] > 1) ? $diff[h] . ' hrs ' : $diff[h] . ' hr ';
        elseif ($diff[i] != 0)
            $ago = ($diff[i] > 1) ? $diff[i] . ' mins ' : $diff[i] . ' min ';
        elseif ($diff[s] != 0)
            $ago = ($diff[s] > 1) ? $diff[s] . ' secs ' : $diff[s] . ' sec ';
        $ago .= ' ago';
        
        return $ago;
    }
    
    public function getgroupcomment($dbeeid)
    {
        $comaudio   = new Application_Model_Comment();
        $commentrow = $comaudio->topcomment($dbeeid);
        if (count($commentrow) > 0) {
            $startnew = $this->startnew;
            $counter  = 1;
            $return .= '<div class="comment-feed1">';
            $moreComment = '';
            if (count($commentrow) > 3) {
                $moreComment = '<div class="moreComment"><a href="/dbeedetail/home/id/' . $dbeeid . '"> more... </a></div>';
            }
            foreach ($commentrow as $row):
                if ($counter < 4) {
                    $myhome_obj       = new Application_Model_Myhome();
                    $userinfo         = $myhome_obj->getrowuser($row['UserID']);
                    $profilelinkstart = '<a class="psUserName" href="' . BASE_URL . '/user/' . $userinfo['Username'] . '">';
                    $profilelinkend   = '</a>';
                    $profile          = new Application_Model_Profile();
                    $ago              = $this->agohelper($row['CommentDate']);
                    if ($counter % 2 == 0)
                        $oddeven = 'commenteven';
                    else
                        $oddeven = 'commentodd1';
                    $qantype = '';
                    
                    
                    $checkImage = new Application_Model_Commonfunctionality();
                    $pic4       = $checkImage->checkImgExist($userinfo['ProfilePic'], 'userpics', 'default-avatar.jpg');
                    $return .= '<div id="comment-block-' . $row['CommentID'] . '" class="comment-list2 ' . $oddeven . '" ' . $no_bottom_border . '><div class="cmntPrpic">' . $profilelinkstart . '<img src="'.IMGPATH.'/users/small/' . $pic4 . '" border="0" width="32" height="32"/>' . $profilelinkend . '</div>';
                    $return .= '<div id="dbcomment-speechwrapper" class="dbcomment-speechwrapper"><div style="float:left; margin-bottom:5px;">' . $profilelinkstart . $row['Name'] . $profilelinkend . $TGPN;
                    if ($isPoll)
                        $return .= ' voted' . $uservote;
                    if ($row['Type'] == '1') {
                        $return .= '&nbsp;' . $row['Comment'] . '</div></div>';
                    } elseif ($row['Type'] == '2') {
                        $return .= '&nbsp;' . $row['UserLinkDesc'] . '</div><div style="float:left; width:560px; margin:0 10px 0 0;"><div style="width:530px; padding:5px; margin-top:5px; margin-bottom:5px; background-color:#DAD9D9;"><div>' . $row['LinkTitle'] . ' - <a href="' . $row['Link'] . '" target="_blank">' . $row['Link'] . '</a></div><div style="margin-top:10px;">' . $row['LinkDesc'] . '</div></div></div></div>';
                    } elseif ($row['Type'] == '3') {
                        $return .= '&nbsp;' . $row['PicDesc'] . '</div><div style="float:left; width:560px; margin:0 10px 0 0;"><div style="float:left; margin-top:5px; width:auto; margin-bottom:5px; padding:3px; border:1px solid #CCCCCC;"><a href="' . BASE_URL . '/imageposts/' . $row['Pic'] . '" rel="popupbox"><img src="' . BASE_URL_IMAGES . '/show_thumbnails.php?ImgName=' . $row['Pic'] . '&ImgLoc=imageposts&Width=120&Height=120" border="0" /></a></div></div></div>';
                    } elseif ($row['Type'] == '4') {
                        if ($row['Vid'] != '') {
                            $atag      = '<a href="javascript:seevideo(\'' . $row['VidID'] . '\');">';
                            $mediaicon = '<div class="icon-youtube" style="margin:-2px 20px 0 -30px;; height:30px;"></div>';
                            if ($row['VidSite'] == 'Youtube')
                                $VideoThumbnail = '<img src="https://i.ytimg.com/vi/' . $row['VidID'] . '/0.jpg" width="120" height="100" border="0" />';
                            elseif ($row['VidSite'] == 'Vimeo') {
                                $url            = 'https://vimeo.com/api/v2/video/' . $row['VidID'] . '.php';
                                $contents       = @file_get_contents($url);
                                $thumb          = @unserialize(trim($contents));
                                $VideoThumbnail = "<img src=" . $thumb[0][thumbnail_small] . ">";
                            } elseif ($row['VidSite'] == 'Dailymotion')
                                $VideoThumbnail = '<img src="https://www.dailymotion.com/thumbnail/video/' . $row['VidID'] . '" width="120" height="100" border="0" />';
                        } elseif ($row['Audio'] != '') {
                            $atag           = '<a href="javascript:seeaudio(\'' . $row['CommentID'] . '\');">';
                            $VideoThumbnail = '<img src="/images/soundcloud.png">';
                            $mediaicon      = '<div class="icon-soundcloud" style="margin:3px 20px 0 -30px; height:30px;"></div>';
                        }
                        $return .= '&nbsp;' . $row['VidDesc'] . '</div><div style="float:left; width:560px; margin:0 10px 0 0;"><div style="float:left; margin-right:10px; width:120px; margin-bottom:5px; padding:3px; border:1px solid #CCCCCC;">' . $atag . str_replace("http://i.ytimg.com/", "https://i.ytimg.com/", $VideoThumbnail) . '</a></div></div></div>';
                    }
                    $return .= '<div class="next-line"></div><div class="ftrCmnt"><div style="float:left;">' . $ago . '</div><div class="cmntScreFt">' . $commentScore . '</div>';
                    $return .= '<br style="clear:both; font-size:1px;" /></div>';
                    $return .= '</div></div><div class="next-line"></div></div>';
                    
                    $counter++;
                }
            endforeach;
            $dbee_comments .= '</div>';
        } 
        return $return . $moreComment;
    }
    
    
    
    public function getcontinent($agent)
    {
        if (stripos($agent, 'AS') !== false) {
            $agent = 'Asia';
        } elseif (stripos($agent, 'AF') !== false) {
            $agent = 'Africa';
        } elseif (stripos($agent, 'AN') !== false) {
            $agent = 'Antarctica';
        } elseif (stripos($agent, 'EU') !== false) {
            $agent = 'Europe';
        } elseif (stripos($agent, 'NA') !== false) {
            $agent = 'North america';
        } elseif (stripos($agent, 'OC') !== false) {
            $agent = 'Australia';
        } elseif (stripos($agent, 'SA') !== false) {
            $agent = 'South america';
        }
        return $agent;
    }
    
    public function getbrowser()
    {
        if (empty($agent)) {
            $agent = $_SERVER['HTTP_USER_AGENT'];
            if (stripos($agent, 'Firefox') !== false) {
                $agent = 'Firefox';
            } elseif (stripos($agent, 'MSIE') !== false) {
                $agent = 'IE';
            } elseif (stripos($agent, 'iPad') !== false) {
                $agent = 'Ipad';
            } elseif (stripos($agent, 'Android') !== false) {
                $agent = 'Android';
            } elseif (stripos($agent, 'Chrome') !== false) {
                $agent = 'Chrome';
            } elseif (stripos($agent, 'Safari') !== false) {
                $agent = 'Safari';
            } elseif (stripos($agent, 'AIR') !== false) {
                $agent = 'Air';
            } elseif (stripos($agent, 'Fluid') !== false) {
                $agent = 'Fluid';
            }
        }
        return $agent;
    }
    
   
    
    public function getdevice()
    {
        if (empty($agent)) {
            $agent   = $_SERVER['HTTP_USER_AGENT'];
            $osArray = array(
                '/Windows phone/i' => 'Windows Phone',
                '/iphone/i' => 'iPhone',
                '/ipod/i' => 'iPod',
                '/ipad/i' => 'iPad',
                '/android/i' => 'Android',
                '/blackberry/i' => 'BlackBerry',
                '/nokia/i' => 'Nokia',
                '/Microsoft/i' => 'Lumia',
                '/webos/i' => 'Mobile'
            );
            
            foreach ($osArray as $k => $v) {
                
                if (@preg_match(strtolower($k), strtolower($_SERVER['HTTP_USER_AGENT']))) {
                    break;
                } else {
                    $v = "PC";
                }
            }
        }
        return $v;
    }
    
  
    public function getos()
    {
        if (empty($agent)) {
            $agent   = $_SERVER['HTTP_USER_AGENT'];
            $osArray = array(
                'iPhone' => '(iPhone)',
                'Windows 98' => '(Win98)|(Windows 98)',
                'Windows 2000' => '(Windows 2000)|(Windows NT 5.0)',
                'Windows 2003' => '(Windows NT 5.2)',
                'Windows ME' => 'Windows ME',
                'Windows XP' => '(Windows XP)|(Windows NT 5.1)',
                'Windows Vista' => 'Windows NT 6.0',
                'Windows 7' => '(Windows NT 6.1)|(Windows NT 7.0)',
                'Windows NT 4.0' => '(WinNT)|(Windows NT 4.0)|(WinNT4.0)|(Windows NT)',
                'Linux' => '(X11)|(Linux)',
                'Mac OS' => '(Safari)',
                'Open BSD' => 'OpenBSD',
                'Sun OS' => 'SunOS',
                'Mac OS' => '(Mac_PowerPC)|(Macintosh)|(Mac OS)',
                'QNX' => 'QNX',
                'BeOS' => 'BeOS',
                'OS/2' => 'OS/2',
                'Android' => 'Android',
                'Search Bot' => '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp/cat)|(msnbot)|(ia_archiver)'
            );
            
            foreach ($osArray as $k => $v) {
                if (@preg_match("/$v/", $agent)) {
                    break;
                } else {
                    $k = "Unknown OS";
                }
            }
        }
        return $k;
    }
    /*testing*/
    public function checkuserType($keytype)
    {
        //echo $keytype;die;
        switch ($keytype) {
            case "1":
                $typeof = " delegate";
                break;
            case "2":
                $typeof = " speaker";
                break;
            case "3":
                $typeof = " sponsor";
                break;
            case "3":
                $typeof = " Administrator";
                break;
            case "0":
                $typeof = "";
                break;
        }
        return $typeof;
    }
    
    public function checkuserTypetooltip($keytype)
    {
        //echo $keytype;die;
        switch ($keytype) {
            case "1":
                $typeof = "delegate";
                break;
            case "2":
                $typeof = "speaker";
                break;
            case "3":
                $typeof = "sponsor";
                break;
            case "10":
                $typeof = "Administrator";
                break;
            case "0":
                $typeof = "";
                break;
        }
        return $typeof;
    }
    
    public function dbeeprofiledetailSpecial($data, $Twitte = '')
    {
        extract($data);
        $checkImage       = new Application_Model_Commonfunctionality();
        $sowlink          = true;
        $dbee_highlighted = '';
        
        if ($row['VidSite'] == 'youtube')
            $playerss = '<div id="playerss"></div>';
        else
            $playerss = '<div class="wistia_embed wistia_async_'.$row['Vid'].' " style="width:740px;height:460px;">&nbsp;</div>';
        
        $dbee_highlighted .= '<div class="dbDetailsContent">
              <div class="dbDetailsBox">
                <div class="brdcrsdtlsRight">
                    <div style="position:relative; z-index:9">
                      ' . $playerss . '
                   </div>
                    <div class="videoOverlay">
                      <div class="youtubeWarning" style="display:none;">
                        <h2>Video starts in</h2>
                        <div class="clock"></div>
                      </div>
                    </div>
                </div>';
        $dbee_highlighted .= '<div class="dbTopContentWrapper dbBroadcardDesc' . $expertDivBlankClass . '" style="padding-left:10px;">                   
                    <div class="trickerWrapper">
                          <div class="scrllContnt videoContent"> 
                            ' . $dbee_content . '
                        </div></div>';
        
        if ($loggedin)
            $dbee_highlighted .= '<div class="dbtpftr"><div class="pull-left postHist">
               </div>' . $TwiTagval . '</div>';
        
        $dbee_highlighted .= '</div>';
        
        if ($Twitte != '')
            $dbee_highlighted .= '<div class="vdBrodcastPad">' . $Twitte . '</div>';
        
        
        $dbee_highlighted .= '</div></div>';
        return $dbee_highlighted;
    }
    
    public function dbeeprofiledetail($data)
    {
        extract($data);
        $sowlink      = true;
        $follwoing    = new Application_Model_Following();
        $Myhome_Model = new Application_Model_Myhome();
        $twitter_connect_data = $_SESSION['Zend_Auth']['storage']['twitter_connect_data'];
        $twitter_connect_data = Zend_Json::decode($twitter_connect_data); 

        $twitter              = $twitter_connect_data['twitter_access_token'];
        
        $highlight['getSentiments'] = $getSentiments;
        
        $ParrentId   = 0;
        $ParrentType = 0;
        
        if ($row['GroupID'] != 0 && $row['GroupID'] != '') {
            $ParrentId   = $row['GroupID'];
            $ParrentType = '1';
            
        }
        if ($row['events'] != 0 && $row['events'] != '') {
            $ParrentId   = $row['events'];
            $ParrentType = '2';
        }
        
        $ArticleId   = $row['DbeeID'];
        $ArticleType = 0;
        $UserId      = $row['UserID'];
        
        $dbeeobj     = new Application_Model_Dbeedetail();
        $recordfound = $dbeeobj->CheckInfluence($UserId, $ParrentId, $ParrentType, $ArticleId, $ArticleType, $this->_userid);
        
        $tip = 'rel="dbTip"';
        if (ismobile)
            $tip = '';
        if (count($recordfound) < 1) {
            $bulb  = '<i class="fa fa-lightbulb-o" ' . $tip . ' title="Add to my Influence list"></i>';
            $class = "";
        } else {
            $bulb  = '<i class="fa fa-lightbulb-o" ' . $tip . ' title="Remove from my Influence list"></i>';
            $class = "active";
        }
        
        $test = "0";
        
        
        $dbee_footer .= '<div class="dbDetailsCntFooter">' . $TwiTagval . ' ' . $scoreDiv;
        if ($loggedin) {
            $dbee_footer .= '<div class="pull-right dbtphdrLinks">';
            if ($requestStatus != '3' && clientID!=55)
                $googletranslate = '<a href="javascript:void(0);"   class="dropDown gtranslator '.$twitter.'">Translate  <div class="dropDownList"></div></a> <span>|</span>';

                if(clientID!=55) { // HIDE INVOLVE TWITTER USERS BUTTON ON AMERICAN GOLF
                    if ($socialloginabilitydetail['allSocialstatus'] == 0 && $socialloginabilitydetail['Twitterstatus'] == 0) {
                        
                            if (!empty($twitter)) // check twitter logined or not
                            {
                                $InvolveTwitte  = ' | <a href="javascript:void(0);" id="twittersphere">Involve <i class="fa fa-twitter" aria-hidden="true"></i></a><div class = "asktoquestion"></div>';
                            } else {
                             $InvolveTwitte           = '| <a class="connectToTwitter" data-fromplace="sphere" data-id="' . $row['DbeeID'] . '" href="javascript:void(0);">Involve <i class="fa fa-twitter" aria-hidden="true"></i></a> ';
                            }
                        
                    }
                }
        
        }
        if ($loggedin && ($socialloginabilitydetail['Facebookstatus']==0 || $socialloginabilitydetail['Twitterstatus']==0))
            $share = '<a href="#" class="bookMarksShare">Share</a>';
        else
            $share = '';
        
        if ($loggedin) {
            if ($requestStatus != '3')
                $dbee_footer .= $googletranslate . ' ' . $reportabuse . ' | ' . $share . ' ' . $InvolveTwitte . ' </div>';
        }
        $dbee_footer .= '</div>';
        $highlight['translatefooter'] = $dbee_footer;
        //echo $dbee_content; exit;
        $dbee_contener .= '<div class="dbDetailsBox dbRelbox ' . $class . '">';
        
        $dbee_contener .= '<div class="dbTopContentWrapper">' . $dbee_content;
        
        if ($loggedin) {
            if ($requestStatus != '3')
                $dbee_contener .= '<div class="sentimentsGetWrp">' . $getSentiments . $overallpostmood . '</div>';
        }
        $dbee_contener .= '<div class="dbtpftr">';
        
        
        $dbee_contener .= '</div></div></div> ';
        
        $highlight['content'] = $dbee_contener;
        return $highlight;
    }
    
    public function dbeeprofiledetailType7($data, $dbeeid, $userid)
    {
        $myhome_obj = new Application_Model_Myhome();
        extract($data);
        $dbee_highlighted = '';
        $dbee_highlighted .= '<div class="dbDetailsBox" id="surverMainWrpDetails"> ';
        
        if ($loggedin) {
            $nextButton = '<a href="#" class="btn btn-yellow pull-right" id="surveyNext">Next</a>';
        } else {
            $nextButton = '<a href="#" class="pull-right" >signin</a>';
        }
        $dbee_highlighted .= '<div class="dbTopContentWrapper">' . $dbee_content . '</div>';
        
        if ($loggedin && empty($surveyresult))
            $surveyresult = $myhome_obj->surveyCheckStatus($dbeeid, $userid);
        if (empty($surveyresult)) {
            $row     = $myhome_obj->surveyQuestions($dbeeid);
            $content = ' <div id="StartSurvey" class="questionAnsMainWrp questionLoader"><ul class="slides">';
            $abcd    = array(
                'A',
                'B',
                'C',
                'D',
                'E',
                'F',
                'G',
                'H',
                'I',
                'J',
                'K',
                'L',
                'M',
                'N',
                'O',
                'P',
                'Q',
                'R',
                'S',
                'T',
                'U',
                'V',
                'W',
                'X',
                'Y',
                'Z'
            );
            foreach ($row as $value) {
                $an       = 0;
                $question = '<li class="questionAnsWrp"><ul><li class="questionRow"><span class="mark">question</span><strong>' . $value["content"] . '</strong></li>';
                $dataRow  = $myhome_obj->surveyAnswers($value['id']);
                foreach ($dataRow as $answervalue) {
                    $val = $dbeeid . '_' . $value['id'] . '_' . $answervalue['id'];
                    $question .= '<li><input type="radio" id="answer' . $answervalue['id'] . '" name="answer' . $value['id'] . '" class="surveyAnswer" value="' . $val . '" /><label for="answer' . $answervalue['id'] . '"><span class="mark mark-green" data-value="&#10004;">' . $abcd[$an] . '</span><strong>' . $answervalue['content'] . '</strong></label></li>';
                    $an++;
                }
                $an = 0;
                $content .= $question;
                $content .= '</ul></li>';
            }
            $content .= '</ul></div>';
            
            $dbee_highlighted .= '<div class="dbtphdrSurvey" id="dbtphdrSurveyID"><div class="clear"> </div>';
            $dbee_highlighted .= $content . '<div class="btnSurveyWrp"><div class="totalQuestions pull-left"><strong>1</strong> of <i>' . count($row) . '</i></div>' . $nextButton . '</div></div></div>';
            
        } else if ($rowData['surveyPdf'] != '') {
            $dbee_highlighted .= '<div class="dbtphdrSurvey surveyComplete">
                <a href="' . BASE_URL . '/dbeedetail/downloadpdf/filepdf/' . $rowData['surveyPdf'] . '">
                <img src="' . BASE_URL . '/img/pdficon.png" class="filePdfDwn" />
                <div class="pdfDwnCnt">
                You have already completed this survey<br />
                <span>Please download your PDF here</span>
                </div>
                </a></div>';
        } else if ($rowData['surveyLink'] != '') {
            $dbee_highlighted .= '<div class="dbtphdrSurvey surveyComplete">
                <a href="' . $this->addhttp($rowData['surveyLink']) . '" target="_blank">
                <span class="fa-stack fa-2x pull-left">
                      <strong class="fa fa-circle-thin fa-stack-2x"></strong>
                      <strong  class="fa fa-check fa-stack-1x "></strong>
                    </span>
                <div class="pdfDwnCnt">
                You have already completed this survey<br />
                <span>' . $rowData['surveyLink'] . '</span>
                </div>
                </a></div>';
        } else
            $dbee_highlighted .= '<div class="dbtphdrSurvey surveyComplete">You have already completed this survey</div>';
        return $dbee_highlighted;
    }
    
    public function dbeeprofiledetail2($data)
    {
        extract($data);
        
        if ($this->_userid == $row['User'])
            $scoreDiv = '';
        
        $dbee_footer .= '<div class="dbDetailsCntFooter">' . $TwiTagval . ' ' . $scoreDiv;
        if ($loggedin) {
            $dbee_footer .= '<div class="pull-right dbtphdrLinks">';
            if ($requestStatus != '3' && clientID!=55)
                $dbee_footer .= '<a href="javascript:void(0);"  class="dropDown gtranslator">Translate  <div class="dropDownList"></div></a> <span>|</span>
        ';
        }
        if ($loggedin)
            $share = '<a href="#" class="bookMarksShare">Share</a> | ';
        else
            $share = '';
        
        if ($loggedin) {
            if ($requestStatus != '3')
                $dbee_footer .= $share . $reportabuse . ' </div>';
        }
        $dbee_footer .= '</div>';
        $highlight['translatefooter'] = $dbee_footer;
        $dbee_contener .= '<div class="dbDetailsBox"><div class="dbTopContentWrapper">' . $this->Polldetailhelper($row, $scoreDiv, $followstring, $ago, $myvoterow, $userid) . ' 
      <div class="dbtpftr">';
        $dbee_contener .= '</div></div></div>';
        $highlight['content'] = $dbee_contener;
        return $highlight;
    }
    
    
    
    
    public function Pollhelper($dbeeid)
    {
        $poloption = new Application_Model_Polloption();
        
        $totalvotesobj = $poloption->getpollvote($dbeeid);
        $totalvotes    = $totalvotesobj[0]['cnt'];
        $pres          = $poloption->getpolloption($dbeeid);
        $colorRadio    = array(
            '#3366cc',
            '#dc3912',
            '#ff9900',
            '#109618'
        );
        $colorcount    = 0;
        foreach ($pres as $prow):
            if ($totalvotes >= 0) {
                $psrid    = $prow['ID'];
                $totalobj = $poloption->getpolloptionvote($dbeeid, $psrid);
                $total    = $totalobj[0]['cnt'];
           
                if ($totalvotes) {
                    $percent = ($total / $totalvotes) * 100;
                } else {
                    $percent = '';
                }
                $width = round($percent, 1);
                $stats .= '<div class="pollstatsbar-wrapper">';
                if (round($percent) > 0)
                    $stats .= '<span class="checkcolorSymbol" style="background:' . $colorRadio[$colorcount] . '"><span class="pollPercentValue">' . round($percent, 1) . '%</span></span>';
                else
                    $stats .= '<span class="checkcolorSymbol" style="background:' . $colorRadio[$colorcount] . '"><span class="pollPercentValue"></span></span>';
                $stats .= '<span class="pollLableTxt">' . $prow['OptionText'] . '</div></span>';
            }
            $colorcount++;
        endforeach;
        
        
        return $stats;
        
    }
    
    
    public function Polloptionlhelper($dbeeid, $polid, $userid)
    {
        $poloption = new Application_Model_Polloption();
        $result    = $poloption->getmyvoteresdbee($dbeeid, $polid, $userid);
        return count($result);
    }
    
    public function Polldetailhelper($row, $scoreDiv, $followstring, $ago, $myvoterow, $sessionID)
    {
        
        $poloption = new Application_Model_Polloption();
        
        $userdata = new Application_Model_Myhome();
        
        $userRec = $this->myclientdetails->getfieldsfromtable('ProfilePic', 'tblUsers', 'UserID', $sessionID);
        
        $uImage   = '<div class="pfpic"><img src="'.IMGPATH.'/users/small/' . $userRec[0]['ProfilePic'] . '" width="56" height="56"></div>';
        $dbeeType = 5;
        
        $loggedin = true;
        
        $imgH = '130';
        
        if (!$sessionID)
            $loggedin = false;
        
        if ($loggedin)
            $userloggedin = '1';
        else
            $userloggedin = '0';
        
        $userid = $sessionID;
        
        $db = $row['DbeeID'];
        
        $isPoll = 1;
        
        $pres = $poloption->getpollvote($db);
        
        $totalvotesexist = count($pres);
        
        if ($totalvotesexist == 0)
            $showChart = false;
        else
            $showChart = true;
        
        // GATHER ALL POLL VOTES TO SEND TO CHART
        
        if ($showChart) {
            $count = 1;
            $pres  = $poloption->getpolloption($db);
            foreach ($pres as $prow):
                $total                        = $poloption->getpolloptionvote($db, $prow['ID']);
                ${'pollopt' . $count}         = $prow['OptionText'];
                ${'pollopt' . $count . 'num'} = $total;
                $count++;
            endforeach;
        }
        
        if (!$loggedin)
            $user = 0; // IF NOT LOGGED IN          
        
        $myvoteres = count($myvoterow);
        
        if ($myvoterow['Vote']) {
            $voteopdisplay = 'style="display:block"';
            $myvotedisplay = 'style="display:block"';
            $vorteid       = $myvoterow['Vote'];
            $vsql          = $poloption->getvsql($vorteid);
            $votename      = $vsql[0]['OptionText'];
            $myvotetext    = '<div align="center"><div class="medium-font-bold-grey">you voted</div><div class="large-font-orange">' . $votename . '</div></div>';
        } else {
            $voteopdisplay = 'style="display:block"';
            $myvotedisplay = 'style="display:none"';
        }
        
        $dbee_highlighted .= '<div class="pollwrapper">
      <div class="pollTxtWrapper">' . $row['PollText'] . '</div>
      <div id="voteerr">choose your vote first</div>
      <div class="next-line"></div>
      <form>';
        
        $pollset = $this->myclientdetails->getRowMasterfromtable('tblAdminSettings', array(
            'Is_PollComments_On',
            'PollComments_On_Option'
        ), array(
            'clientID' => clientID
        ));
        
        $poRes      = $poloption->getpores($db);
        $colorRadio = array(
            '#3366cc',
            '#dc3912',
            '#ff9900',
            '#109618'
        );
        $colorcount = 0;
        $dbee_highlighted .= '<div class="pollOptionLeft">';
        foreach ($poRes as $poRow):
            if ($myvoterow['Vote']) {
                if ($poRow['ID'] == $myvoterow['Vote']) {
                    $autoselect = 'checked="checked"';
                    $voted      = 1;
                } else {
                    $disabled   = 'disabled="disabled"';
                    $autoselect = '';
                }
            }
            
            $dbee_highlighted .= '<div id="pollopt' . $poRow['ID'] . '" ' . $color . ' class="optiontextfloat"><span class="checkcolorSymbol" style="background:' . $colorRadio[$colorcount] . '"></span><label><input type="radio" id="pollradio' . $poRow['ID'] . '" name="pollradio" value="' . $poRow['ID'] . '" ' . $autoselect . ' ' . $disabled . '  class="radiodtn">' . $poRow['OptionText'] . '</label></div>';
            $colorcount++;
        endforeach;
        $dbee_highlighted .= '</div>';
        $dbeeVoteBtn = '';
        if ($voted == 1 && $loggedin) {
            $disabled2     = 'disabled="disabled"';
            $dbeeVoteBtn   = 'You have already submitted your vote';
            $alreadySubmit = 'alreadyVoteSubmit';
        } else if ($loggedin)
            $dbeeVoteBtn = '<a href="javascript:void(0);" onclick="javascript:castvote(' . $db . ');" class="btn btn-yellow pull-left">Vote</a>';
        else
            $dbeeVoteBtn = '<a href="javascript:void(0);" class="btn btn-yellow pull-left">login to vote</a>';
        
        $dbee_highlighted .= '<div id="PollPieChart" >';
        
        if (!$showChart)
            $dbee_highlighted .= '<div class="noFound">No stats currently available</div>';
        
        $dbee_highlighted .= '</div>
      <div style="clear:both"></div>
      <div id="poll-comment">';
        $pollCommentOff = '';
        if ($pollset['Is_PollComments_On'] != 1)
            $pollCommentOff = 'pollCommentOff';
        
        if ($voted != 1 && $loggedin) {
            $dbee_highlighted .= '<div  class="miniPostWraper ' . $pollCommentOff . '">';
            if ($pollset['Is_PollComments_On'] == 1) {
                $dbee_highlighted .= '<div class="minPostTopBar">
                  ' . $uImage . '
                    <div class="dbpostWrp">
                        <div class="cmntBntsWrapper clearfix">
                          <textarea id="PollComment" placeholder="Add a comment to support your vote"></textarea>
                        </div>
                    </div>
                </div>';
            }
            $dbee_highlighted .= '<div id="postCommentBtn" style="display: none;"> ' . $dbeeVoteBtn . '</div>
          </div>';
        } else if ($loggedin) {
            $dbee_highlighted .= ' <div class="pollOptnSubmit ' . $alreadySubmit . '" >' . $dbeeVoteBtn . ' </div>';
        }
        
        $dbee_highlighted .= '</div></form></div>
      <input type="hidden" id="hiddendb" value="' . $db . '">';
        
        return $dbee_highlighted;
    }
    
    public function generateUrl($name)
    {
        $url = html_entity_decode($this->makeSeo($name));
        return trim($url);
    }
    
    // Seo friendly url create function
    
    function makeSeo($title, $raw_title = '', $context = 'display')
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
    
    ///////////// End Seo friendly url create function ////////////
    
    public function dbeeleaguexit($dbeeid, $curdate)
    {
        $select = $this->_db->select()->from(array(
            'l' => $this->getTableName(LEAGUE)
        ))->join(array(
            'dl' => $this->getTableName(DBELEAGUE)
        ), 'l.LID = dl.LID AND l.clientID = dl.clientID')->where("dl.DbeeID =?", $dbeeid)->where("l.EndDate >= ?", $curdate)->where('l.clientID = ?', clientID);
        
        $result = $this->_db->fetchAll($select);
        return count($result);
    }
    
    public function getalluser()
    {
        $select = $this->_db->select()->from($this->getTableName(USERS), array(
            'UserID',
            'Username',
            'Name',
            'lname',
            'Email'
        ))->where('clientID = ?', clientID);
        $result = $this->_db->fetchAll($select);
        return $result;
    }
    
    public function updateusrname($data, $id)
    {
        //print_r($data);exit;
        return $this->_db->update($this->getTableName(USERS), $data, "UserID=" . $id);
        //$this->update($data, 'DbeeID =' . $id);
    }
    
    public function getConfiguration()
    {
        $select = $this->_db->select();
        $select->from($this->getTableName(Configuration))->where('clientID = ?', clientID);
        return $this->_db->fetchRow($select);
    }
    
    public function getFacebookPageConfiguration()
    {
        $select = $this->_db->select();
        $select->from($this->getTableName(Configuration))->where('clientID = ?', clientID);
        return $this->_db->fetchRow($select);
    }
    
    public function getStaticPage()
    {
        $select = $this->_db->select();
        $select->from($this->getTableName(tblStaticPages))->where('clientID = ?', clientID);
        return $this->_db->fetchRow($select);
    }
    
    public function getSeoConfiguration()
    {
        $select = $this->_db->select();
        $select->from($this->getTableName(Configuration))->where('clientID = ?', clientID);
        $data = $this->_db->fetchRow($select);
        return json_decode($data['seoContent']);
    }
    
    
    public function getTempConfiguration()
    {
        $select = $this->_db->select();
        $select->from($this->getTableName(Configuration))->where('clientID = ?', clientID);
        $data = $this->_db->fetchRow($select);
        return json_decode($data['tempContent']);
    }
    
    
    public function getallBrowserVersion()
    {
        $old = 0;
        $ua  = strtolower($_SERVER['HTTP_USER_AGENT']);
        
        
        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            // firefox <= 9
            if (preg_match('/(firefox)[ \/]([\w.]+)/', $ua)) {
                $browser = 'firefox';
                preg_match('/(' . $browser . ')[ \/]([\w]+)/', $ua, $version);
                if ($version[2] <= 20) {
                    $old = 1;
                }
            }
            // IE < 11
            if (preg_match('/(msie)[ \/]([\w.]+)/', $ua)) {
                $browser = 'msie';
                preg_match('/(' . $browser . ')[ \/]([\w]+)/', $ua, $version);
                if ($version[2] < 11) {
                    $old = 1;
                } else {
                    $old = 2;
                }
                $browser = 'Internet Explorer';
            }
            // IE >= 11
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/7.0; rv:11.0') !== false) {
                $old     = 2;
                $browser = 'Internet Explorer';
            }
            // Crome <= 15
            if (preg_match('/(chrome)[ \/]([\w.]+)/', $ua)) {
                $browser = 'chrome';
                preg_match('/(' . $browser . ')[ \/]([\w]+)/', $ua, $version);
                if ($version[2] <= 15) {
                    $old = 1;
                }
            }
            
        }
        return $old . '~' . $browser . '~' . $version[2];
    }
    

    public function getGroupemailtemplate($case = '', $area = '')
    {
        $condition = '';
        $db        = Zend_Db_Table_Abstract::getDefaultAdapter();
        
        if ($case != '')
            $condition = ' AND areatype = "' . $area . '" AND  `emailtemplates`.`case` = ' . $case;
        
        $SQL = "SELECT * FROM `emailtemplates` where `clientID`='" . clientID . "' " . $condition; // ORDER BY `emailtemplates`.`case`
        
        if ($case != '')
            return $db->fetchRow($SQL);
        else
            return $db->fetchAll($SQL);
    }
    
    public function getredbeedetail($userid)
    {
        $return     = '';
        $myhome_obj = new Application_Model_Myhome();
        $userdetail = $myhome_obj->getuserdetail($userid);
        return $userdetail;
    }
    
    
    public function getisdashboard($userid, $dbid)
    {
        $return     = '';
        $myhome_obj = new Application_Model_Myhome();
        $userdetail = $myhome_obj->getisdashboard($userid, $dbid);
        return $userdetail;
    }
    public function topdbeecomments($dbid, $dbowner, $plateform_scoring, $adminpostscore, $GroupID = '', $EventsId = '', $dbuser = '')
    {
        $commonbynew  = new Application_Model_Commonfunctionality();
        $sen_comments = new Application_Model_Comment();
        
        $objuser = new Application_Model_DbUser();
        
        $user = $_SESSION['Zend_Auth']['storage']['UserID'];
        
        $return       = '';
        $scoreLove    = '';
        $scoreLike    = '';
        $scoreDislike = '';
        $scoreFOT     = '';
        $scoreHate    = '';
        
        $i = 1;
        
        $ownerPic  = $_SESSION['Zend_Auth']['storage']['ProfilePic'];
        $ownerName = $_SESSION['Zend_Auth']['storage']['Name'];
        
        $ownerPic = $commonbynew->checkImgExist($ownerPic, 'userpics', 'default-avatar.jpg');
        
        $cmntArr = array(
            'CommentID',
            'Comment',
            'Pic',
            'LinkTitle',
            'LinkPic',
            'LinkDesc',
            'Link',
            'Vid',
            'VidSite',
            'VidID',
            'VidTitle',
            'VidDesc',
            'CommentDate',
            'TwitterComment'
        );
        
        $usrArr = array(
            'UserID',
            'Username',
            'ProfilePic',
            'usertype',
            'Name',
            'lname',
            'Status',
            'hideuser'
        );
        
        $commentrow = $this->myclientdetails->getfieldsfromjointable($cmntArr, 'tblDbeeComments', array(
            'DbeeID' => $dbid
        ), $usrArr, 'tblUsers', 'UserID', 'UserID', '', array(
            'CommentDate' => 'DESC'
        ), 3);

        $configuration_xx   = $this->getConfiguration();
        $post_score_setting = json_decode($configuration_xx['ScoreNames'], true);
        
        if ($this->_userid != '') {
            
            //$score = $this->myclientdetails->passSQLquery("SELECT IFNULL(SUM(IF(a.Score= 1,1,0)),0) as love, IFNULL(SUM(IF(a.Score= 2,1,0)),0) as likes,IFNULL(SUM(IF(a.Score= 4,1,0)),0) as dislike,IFNULL(SUM(IF(a.Score= 5,1,0)),0) as hate FROM tblScoring as a WHERE clientID='" . clientID . "' and  `MainDB`='" . $dbid . "'");
            
            $scoreActive = $this->myclientdetails->getAllMasterfromtable('tblScoring', array(
                'Score'
            ), array(
                'ID' => $dbid,
                'UserID' => $user
            ));
            
            $ParrentId   = 0;
            $ParrentType = 0;
            if ($EventsId != 0 && $EventsId != '') {
                $ParrentId   = $EventsId;
                $ParrentType = '2';
            }
            if ($GroupID != 0 && $GroupID != '') {
                $ParrentId   = $GroupID;
                $ParrentType = '1';
            }
            
            $UserID      = $dbuser;
            $ArticleId   = $dbid;
            $ArticleType = 0;
            
            
            $active        = $scoreActive[0]['Score'];
            $scoreLoveA    = ($active == 1) ? 'active' : '';
            $scoreLikeA    = ($active == 2) ? 'active' : '';
            $scoreFOTA     = ($active == 3) ? 'active' : '';
            $scoreDislikeA = ($active == 4) ? 'active' : '';
            $scoreHateA    = ($active == 5) ? 'active' : '';

            if($active!="")
            {
               /* $scoreother = $this->myclientdetails->getAllMasterfromtable('tblScoring', array(
                    'UserID'
                ), array(
                    'ID' => $dbid,
                    'Score' => $active
                ));*/

$scoreother = $this->myclientdetails->passSQLquery("select s.UserID,u.full_name from tblScoring as s,tblUsers as u WHERE s.clientID='" . clientID . "' and  s.ID='" . $dbid . "' and  s.Score='" . $active . "' and s.UserID=u.UserID and s.UserID!='" . $user . "'");

                $countpeoplescore=count($scoreother);

                if($active==4)
                {
                    $iconval=3;
                }
                elseif ($active==5) {
                    $iconval=4;
                }
                else
                {
                     $iconval=$active;
                }
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


            }
        }
        
        $moreComment      = '';
        $dbeeScoreCommnet = '
      <div class="cmntScoreState">';
        
        $disbleclass = "";
        if ($dbowner == $user) {
            $disbleclass = " disbledClick";
            $active      = '';
        }
        
        //if($dbowner == adminID){ $disbleclass =" disbledClick"; $active='';}
        
        
        
       
        
        $dbeeobj     = new Application_Model_Dbeedetail();
        $recordfound = $dbeeobj->CheckInfluence($UserID, $ParrentId, $ParrentType, $ArticleId, $ArticleType, $user);
        
        $tip = 'rel="dbTip"';
        if (ismobile)
            $tip = '';
        if (count($recordfound) < 1) {
            $bulb = '<i class="fa fa-lightbulb-o fa-lg" ' . $tip . ' title="Add to my Influence list"></i>';
        } else {
            $bulb = '<i class="fa fa-lightbulb-o fa-lg"  ' . $tip . ' title="Remove from my Influence list"></i>';
        }
        
        
        
        $test         = "0";
        $tip          = 'rel="dbTip"';
        $dropDown     = '';
        $dropDownList = '';
        
        if ($plateform_scoring == 0 && $this->_userid != '') {
            if (ismobile) {
                $tip          = '';
                $dropDown     = 'dropDown';
                $dropDownList = 'dropDownList';
            }
            $dbeeScoreCommnet .= '<div class="scoreComnt ' . $dropDown . '"><a href="#" class="scoreTxt"><i class="fa fa-check"></i> Score</a><div class="comntScorelist ' . $dropDownList . '"><span title="' . $post_score_setting[2]['ScoreName2'] . '" ' . $tip . ' data="2,like,1,' . $dbid . ',' . $ParrentId . ',' . $ParrentType . '" id="like-dbee" class="' . $scoreLikeA . $disbleclass . '">' . $this->myclientdetails->ShowScoreIcon($post_score_setting[2]['ScoreIcon2'], "") . '  <abbr class="scoreSpantxt">Agree</abbr></span><span title="' . $post_score_setting[1]['ScoreName1'] . '" ' . $tip . ' data="1,love,1,' . $dbid . ',' . $ParrentId . ',' . $ParrentType . '" id="love-dbee" class="' . $scoreLoveA . $disbleclass . '" >' . $this->myclientdetails->ShowScoreIcon($post_score_setting[1]['ScoreIcon1'], "") . '  <abbr class="scoreSpantxt">Strongly Agree</abbr></span>
        <span title="' . $post_score_setting[3]['ScoreName3'] . '" ' . $tip . ' data="4,dislike,1,' . $dbid . ',' . $ParrentId . ',' . $ParrentType . '" id="dislike-dbee" class="' . $scoreDislikeA . $disbleclass . '">' . $this->myclientdetails->ShowScoreIcon($post_score_setting[3]['ScoreIcon3'], "") . '  <abbr class="scoreSpantxt">Disagree</abbr></span>
        <span title="' . $post_score_setting[4]['ScoreName4'] . '" ' . $tip . ' data="5,hate,1,' . $dbid . ',' . $ParrentId . ',' . $ParrentType . '" id="hate-dbee" class="' . $scoreHateA . $disbleclass . '">' . $this->myclientdetails->ShowScoreIcon($post_score_setting[4]['ScoreIcon4'], "") . '  <abbr class="scoreSpantxt">Strongly Disagree</abbr></span></div>
        <span class="influence" style="display:none"><a style="margin-left:15px; margin-top:10px;" href="javascript:void(0);" onclick="influence(' . $UserID . ',' . $ParrentId . ',' . $ParrentType . ',' . $ArticleId . ',' . $ArticleType . ',' . $test . ',this);">' . $bulb . '</a></span>
        <a rel="dbTip" href="javascript:void(0);" title="'.$titleusername.'" class="otheruserlike '.$classxx.' pull-right" data-dbeeid="'.$dbid.'" data-score="'.$iconval.'">'.$textforscore.'</a></div>';
        } else {
            $dbeeScoreCommnet .= '';
        }
        
        $dbeeScoreCommnet .= '</div> ';
        if ($dbowner == $this->_userid) {
            $dbeeScoreCommnet = '';
        } else if ($dbowner == adminID && $adminpostscore == 0) {
            $dbeeScoreCommnet = '';
        }
        
        if ($this->_userid != '') {
            $postCommnetHtml = '<div class="minPostTopBar"><div class="pfpic">
                <img border="0" src="'.IMGPATH.'/users/small/' . $ownerPic . '" width="32" height="32"  profilename="' . $this->myclientdetails->customDecoding($ownerName) . '"> </div><div class="dbpostWrp">
                  <div class="cmntBntsWrapper clearfix">
                    <textarea data-id="' . $dbid . '"  id="myhomedbee_comment" name="myhomedbee_comment" placeholder="Write a comment..." value="" class="mentionto_' . $dbid . '"></textarea>
                    <a data-type="CommentPix" class="cmntBtnContent" href="javascript:void(0)"><i class="fa fa-lg fa-camera"></i>
                    <form id="uploadCoommentDropzone' . $dbid . '" action="/file-upload" class="dropzone">
                      <div class="fallback">
                        <input name="file" type="file" multiple />
                      </div>
                    </form></a>
                  </div>
                </div>
              </div><script type="text/javascript">
              $(function(){
                Dropzone.options.myAwesomeDropzone = false;
                Dropzone.autoDiscover = false;
              $commentPicUploader("#uploadCoommentDropzone' . $dbid . '"); 
              });</script>';
            
        }
        
        if (count($commentrow) > 0) {
            $counter = 1;
            $return .= '<div class="comment-feed1">' . $dbeeScoreCommnet;
            $commentreverce = array_reverse($commentrow);
            foreach ($commentreverce as $row):
                if ($counter < 4) {
                    $ago = $this->agohelper($row['CommentDate']);
                    
                    $userTypenal = $row['typename'];
                    if ($row['Status'] == 1 && $row['UserID'] != '-1')
                        $profilelinkstart = '<a href=' . BASE_URL . '/user/' . $this->myclientdetails->customDecoding($row['Username']) . ' class=cmntuserLink rel=dbTip title=' . $userTypenal . '>';
                    else
                        $profilelinkstart = '<a href="javascript:void(0)" class="profile-deactivated cmntuserLink" title="' . DEACTIVE_ALT . '" >';
                    
                    $profilelinkend = '</a>';
                    $ProfilePic     = $commonbynew->checkImgExist($row['ProfilePic'], 'userpics', 'default-avatar.jpg');
                    
                    
                    if ($row['usertype'] == 100 && $_SESSION['Zend_Auth']['storage']['usertype'] != 100 && $_SESSION['Zend_Auth']['storage']['role'] != 1) {
                        $return .= '<div id="comment-block-' . $row['CommentID'] . '" class="comment-list2 " ' . $no_bottom_border . '><div class="userPicLink"><img src="'.IMGPATH.'/users/small/' . VIPUSERPIC . '" width="32" height="32" border="0" /></div>';
                        $fulldata = VIPUSER . '&nbsp;' . $sen_comments->convert_clickable_links($row['Comment'], '') . '';
                        $return .= '<div id="dbcomment-speechwrapper" class="dbcomment-speechwrapper"><div class="dbcmntspeech"><div><div style="float:left; margin-bottom:5px; " class="dbsmore" fulldata="' . nl2br(strip_tags($row['Comment'])) . '"><div class="fulldataContainer">' . $fulldata . '</div><span class="cmntuserLink">' . VIPUSER . '</span>';
                    } else if ($row['hideuser'] == 1 && $_SESSION['Zend_Auth']['storage']['role'] != 1 && $row['UserID'] != $_SESSION['Zend_Auth']['storage']['UserID']) {
                        $return .= '<div id="comment-block-' . $row['CommentID'] . '" class="comment-list2 " ' . $no_bottom_border . '><div class="userPicLink"><img src="'.IMGPATH.'/users/small/' . HIDEUSERPIC . '" width="32" height="32" border="0" /></div>';
                        $fulldata = HIDEUSER . '&nbsp;' . $sen_comments->convert_clickable_links($row['Comment'], '') . '';
                        $return .= '<div id="dbcomment-speechwrapper" class="dbcomment-speechwrapper"><div class="dbcmntspeech"><div><div style="float:left; margin-bottom:5px; " class="dbsmore" fulldata="' . nl2br(strip_tags($row['Comment'])) . '"><div class="fulldataContainer">' . $fulldata . '</div><span class="cmntuserLink">' . HIDEUSER . '</span>';
                    } else {
                        
                        $return .= '<div id="comment-block-' . $row['CommentID'] . '" class="comment-list2 " ' . $no_bottom_border . '><div class="userPicLink">' . $profilelinkstart . '<img src="'.IMGPATH.'/users/small/' . $ProfilePic . '" width="32" height="32" border="0" />' . $profilelinkend . '</div>';
                        $fulldata = $profilelinkstart . $this->myclientdetails->customDecoding($row['Name']) . ' ' . $this->myclientdetails->customDecoding($row['lname']) . $profilelinkend . '&nbsp;' . $sen_comments->convert_clickable_links($row['Comment'], '') . '';
                        $return .= '<div id="dbcomment-speechwrapper" class="dbcomment-speechwrapper"><div class="dbcmntspeech"><div><div style="float:left; margin-bottom:5px; " class="dbsmore" fulldata="' . nl2br(strip_tags($row['Comment'])) . '"><div class="fulldataContainer">' . $fulldata . '</div>' . $profilelinkstart . $this->myclientdetails->customDecoding($row['Name']) . ' ' . $this->myclientdetails->customDecoding($row['lname']) . $profilelinkend;
                    }
                    
                    $return .= '&nbsp;' . $sen_comments->convert_clickable_links($this->myclientdetails->dbSubstring(nl2br(strip_tags($row['Comment'], '<br><a>')), 200, "  <a href='javascript:void(0);' class='seeMoreFulltextdb' ishow='1'><i class='fa fa-plus-circle'></i> more</a>")) . '</div></div>';
                    if ($row['Pic'] != '') {
                        $Pic = $commonbynew->checkImgExist($row['Pic'], 'imageposts', 'default-avatar.jpg');
                        $return .= '<div class="clear"> </div><div class="cmntPhotoWrp" popup="true"  popup-image="' . $Pic . '" style="position:relative;  "><img src="'.IMGPATH.'/imageposts/small/' . $Pic . '" /><i class="fa fa-search-plus"></i></div>';
                        
                    }
                    if ($row['LinkTitle'] != '') {
                        $return .= '<div class="clear"> </div><div class="makelinkWrp">
                        <div class="makelinkDes otherlinkdis" style="margin-left:0px;">
                          <h2>' . $row['LinkTitle'] . '</h2>
                          <div class="desc">' . $this->myclientdetails->dbSubstring($this->myclientdetails->escape(nl2br($row['LinkDesc'])), '100', '...') . '</div>
                          <div class="makelinkshw"><a href="' . $row['Link'] . '" target="_blank">' . $row['Link'] . '</a></div>
                        </div>
                      </div>';
                    }
                    if ($row['Vid'] != '') 
                    {
                        if ($row['VidSite'] == 'Youtube')
                            $VideoThumbnail = '
                <div class="youTubeVideoPost"  popup="true">
                  <a class="yPlayBtn" href="#">
                    <i class="fa fa-play-circle-o fa-5x"></i>
                  </a>
                  <a href="javascript:void(0);">
                    <img src="https://i.ytimg.com/vi/' . $row['Vid'] . '/1.jpg" video-id="' . $row['Vid'] . '" width="80" height="80" border="0" />
                  </a>
                </div>';
                        $return .= '<div class="clear"></div><div class="makelinkWrp">
                        ' . $VideoThumbnail . '
                        <div class="makelinkDes">
                          <h2 class="oneline">' . $row['VidTitle'] . '</h2>
                          <div class="desc">' . $this->myclientdetails->dbSubstring($this->myclientdetails->escape(nl2br($row['VidDesc'])), '100', '...') . '</div>
                        </div>
                      </div>';
                    }
                    if ($row['TwitterComment'] != '') {
                        $return .= "<div class='next-line'></div><div id='tweet-block-" . $row['CommentID'] . "' class='twitter-post-tag-comments'><div style='float:left; color:#29b2e4; margin-right:10px;'><i class='fa dbTwitterIcon fa-2x'></i></div>" . str_replace('%26', '&', $row['TwitterComment']) . "";
                        $return .= "<br style='clear:both'></div>";
                    }
                    
                    $return .= '<div class="next-line"></div><div style="color:#999; margin:0 10px 0 0; width:auto;"><div class="cmntDateTime">' . $ago . '</div>';
                    $return .= '<br style="clear:both; font-size:1px;" /></div>';
                    
                    $return .= '</div></div><div class="next-line"></div></div>';
                    $counter++;
                }
            endforeach;
            $return .= '</div>' . $moreComment . $postCommnetHtml;
            $dbee_comments .= '</div>'; // COMMENT-FEED DIV ENDS
            
        } else
            $return .= $dbeeScoreCommnet . $postCommnetHtml;
        return $return;
    }
    
    
    
    public function addLinks($text)
    {
        return $text;
    }
    
    
    public function isvip($usertype)
    {
        if ($usertype != 10 || $usertype != 6 || $usertype != 0)
            return true;
        else
            return false;
        
    }

    public function resize($width, $height,$imgname,$content){  
    $file = ABSIMGPATH."/users/".$imgname;
    list($w, $h, $type, $attr) = getimagesize($file);      
    $ratio = max($width/$w, $height/$h);
    $h = ceil($height / $ratio);
    $x = ($w - $width / $ratio) / 2;
    $w = ceil($width / $ratio);   
    if($width==50)
    {
     $path = ABSIMGPATH."/users/small/".$imgname;
    }
    else if($width==100)
    {
      $path = ABSIMGPATH."/users/medium/".$imgname;
    }
    $imgString = file_get_contents($file);
    $image = imagecreatefromstring($imgString);
    $tmp = imagecreatetruecolor($width, $height);
    imagecopyresampled($tmp, $image,
      0, 0,
      $x, 0,
      $width, $height,
      $w, $h);
    /* Save image */
    switch ($type) {
      case 'image/jpeg':
        imagejpeg($tmp, $path, 100);
        break;
      case '2':
        imagejpeg($tmp, $path, 100);
        break;
      case 'image/png':
        imagepng($tmp, $path, 0);
        break;
      case '3':
        imagepng($tmp, $path, 0);
        break;
      case 'image/gif':
        imagegif($tmp, $path);
        break;
      case '0':
        imagegif($tmp, $path);
        break;
      default:
        exit;
        break;
      }

       return $path;
      /* cleanup memory */
      imagedestroy($image);
      imagedestroy($tmp);
    }

    public function resizeGroup($width, $height,$imgname,$content){  
    $file = ABSIMGPATH."/grouppics/".$imgname;
    list($w, $h, $type, $attr) = getimagesize($file);      
    $ratio = max($width/$w, $height/$h);
    $h = ceil($height / $ratio);
    $x = ($w - $width / $ratio) / 2;
    $w = ceil($width / $ratio);   
    if($width==100)
    {
      $path = ABSIMGPATH."/grouppics/medium/".$imgname;
    }
    $imgString = file_get_contents($file);
    $image = imagecreatefromstring($imgString);
    $tmp = imagecreatetruecolor($width, $height);
    imagecopyresampled($tmp, $image,
      0, 0,
      $x, 0,
      $width, $height,
      $w, $h);
    /* Save image */
    switch ($type) {
      case 'image/jpeg':
        imagejpeg($tmp, $path, 100);
        break;
      case '2':
        imagejpeg($tmp, $path, 100);
        break;
      case 'image/png':
        imagepng($tmp, $path, 0);
        break;
      case '3':
        imagepng($tmp, $path, 0);
        break;
      case 'image/gif':
        imagegif($tmp, $path);
        break;
      case '0':
        imagegif($tmp, $path);
        break;
      default:
        exit;
        break;
      }

       return $path;
      /* cleanup memory */
      imagedestroy($image);
      imagedestroy($tmp);
    }

    public function getExtension($str) {
         $i = strrpos($str,".");
         if (!$i) { return ""; }
         $l = strlen($str) - $i;
         $ext = substr($str,$i+1,$l);
         return $ext;
    }

    public function TotalInfluence($UserID)
    {
        $influencearray = $this->myclientdetails->passSQLquery('SELECT * FROM `tblInfluence` WHERE influence_by='.$UserID.' and ArticleType=0 and clientID='.clientID .'');
       return $TotalInfluence = count($influencearray);
    }

    public function progressiveImg($imgpath)
    {
      $image = imagecreatefrompng($imgpath);
      imagefilter($image, IMG_FILTER_MEAN_REMOVAL);
      imageinterlace($image, 1);
      header("content-type: image/png");
      imagepng($image);
      imagedestroy($image);
    }
    
    
}







