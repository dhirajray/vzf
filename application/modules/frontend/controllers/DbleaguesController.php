<?php
class DbleaguesController extends IsController
{
    public function init()
    {
        parent::init();
        $this->preCall();

    }
    public function preCall()
    {
        if (!$this->_userid) {
            $this->_redirect('myhome/logout');
        }
    }
    public function indexAction()
    {
        $cat  = new Application_Model_Category();
        $this->view->cat = $cat->getallcategory();
        $loggedin = true;
        if (!$this->_userid)
            $loggedin = false;
        $request = $this->getRequest();
        if ($request->getpost('league'))
            $league = $request->getpost('league');
        else
            $league = 'mostfollowed';
        $this->view->user   = $this->_userid;
        $this->view->dbeeuser = $this->_userid;
        $this->view->league = $league;
    }
    public function userleagueAction()
    {
        $pricetype      = array(
            'gold',
            'silver',
            'bronze'
        );
        $pricePostion      = array(
            '1',
            '2',
            '3'
        );
        $request        = $this->getRequest();
        $league         = $request->getpost('league');
        $showtabs       = $request->getpost('showtabs');
        $user           = $this->_userid;
        $dbleague       = $request->getpost('dbleague') ? 'seedbleague' : 'seeuserleague';
        $DbleaguesTable = $this->dbleaguesTable;
        $myhome_obj     = $this->myhome_obj;
        $comm_obj       = $this->commonmodel_obj;
        $private        = $DbleaguesTable->privateuserleague();
        // leage table start
        $topthree       = '';
        $afterthree     = '';
        $counter        = 1;;
        if ($league != 'mostfollowed') {
            if ($league != 'mostcomm') {
                $LeagueArr = $this->getleauebyscore($league, $user);
                if(count($LeagueArr)>1)
                {    
                    $topthree .= '<ul class="leaguesLists" id="league-table-topthree">';
                    for ($k = 0; $k < 3; $k++) {
                        if ($k != count($LeagueArr) - 1) {
                            if ($LeagueArr[$k]['ID'] != '') {
                                $exist = true;
                                $rowdb = $DbleaguesTable->topthreedata($LeagueArr[$k]['ID'], $private);
                                $checkImage = new Application_Model_Commonfunctionality();
                                $userprofile1 = $checkImage->checkImgExist($rowdb['Pic'],'imageposts','default-avatar.jpg');
                                if ($rowdb['Type'] == '1')
                                    $dbeeText = '<p><a href="/dbee/' . $rowdb['dburl'] . '">' . $rowdb['Dbeeid'] . substr($rowdb['Text'], 0, 300) . '</a></p>';
                                elseif ($rowdb['Type'] == '2')
                                    $dbeeText = '<p><a href="/dbee/' . $rowdb['dburl'] . '">' . substr($rowdb['LinkDesc'], 0, 300) . '</a></p>';
                                elseif ($rowdb['Type'] == '3')
                                    $dbeeText = '<p>' . ($rowdb['PicDesc'] != '' ? '<a href="/dbee/' . $rowdb['dburl'] . '">' . substr($rowdb['PicDesc'], 0, 300) . '</a></p>' : '<img src="'.IMGPATH.'/imageposts/small/'.$userprofile1.'" width="80" border="0" />');
                                elseif ($rowdb['Type'] == '4')
                                    $dbeeText = '<p><a href="/dbee/' . $rowdb['dburl'] . '">' . substr($rowdb['VidDesc'], 0, 300) . '</a></p>';
                                if ($LeagueArr[$k]['ID'] == $user)
                                    $bg = ' background-color:#FFFA7F;';
                                else
                                    $bg = '';
                                 $commonbynew    = new Application_Model_Commonfunctionality();
                                 $userTypenal = $commonbynew->checkuserTypetooltip($LeagueArr[$k]['usertype']);
                                 $checkImage = new Application_Model_Commonfunctionality();
                                 $userprofile2 = $checkImage->checkImgExist($LeagueArr[$k]['ProfilePic'],'userpics','default-avatar.jpg');
                                $topthree .= '<li>
                                                <div class="prizeType ' . $pricetype[$k] . 'Cup cupSprite">
                                                    <i class="fa fa-trophy"></i> '.$pricePostion[$k].'
                                                </div>
                								<div class="leaguesUserPic" style="background:url('.IMGPATH.'/users/small/' . $userprofile2 . ') top center no-repeat; background-size: contain;">
                                                     <a href="/user/' . $this->myclientdetails->customDecoding($LeagueArr[$k]['Username']) . '"></a>
                                                </div>
                                                <div class="leagusListDetails">
                                                    <div class="leaguesTopContent">
                                                        <div class="leaguesUserName">
                                                            <a href="'.BASE_URL.'/user/' .$this->myclientdetails->customDecoding( $LeagueArr[$k]['Username']) . '" rel="dbTip" title="'.$userTypenal.'">' . $this->myclientdetails->customDecoding($LeagueArr[$k]['Name']) . '</a>
                                                            <span class="leaguePts">' . $LeagueArr[$k]['Score'] . ' pts</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="lgprofileCell"> 
                                                     <a href="'.BASE_URL.'/user/' .$this->myclientdetails->customDecoding( $LeagueArr[$k]['Username']) . '" class="btn btn-mini">Profile</a>
                                                </div>
                                            </li>';
                                $counter++;
                            }
                        }
                    }
                    $topthree .= '</ul>';
                    $afterthree .= '<ul class="leaguesLists" id="league-table-afterthree">';
                    for ($k = 3; $k < 15; $k++) {
                        if ($k != count($LeagueArr) - 1) {
                            if ($LeagueArr[$k]['ID'] != '') {
                                $exist    = true;
                                $resdb    = $DbleaguesTable->topthreedata($LeagueArr[$k]['ID'], $private);
                                $dbeeurdd = $rowdb['dbeeurl'];
                                if ($resdb) {
                                    $checkImage = new Application_Model_Commonfunctionality();
                                    $userprofile3 = $checkImage->checkImgExist($rowdb['Pic'],'imageposts','default-avatar.jpg');
                                    if ($rowdb['Type'] == '1')
                                        $dbeeText = '<br /><div style="font-size:12px;"><a href="/dbee/' . $comm_obj->generateUrl($dbeeurdd) . '">' . substr($rowdb['Text'], 0, 300) . '</a></div>';
                                    elseif ($rowdb['Type'] == '2')
                                        $dbeeText = '<br /><div style="font-size:12px;"><a href="/dbee/' . $comm_obj->generateUrl($dbeeurdd) . '">' . substr($rowdb['LinkDesc'], 0, 300) . '</a></div>';
                                    elseif ($rowdb['Type'] == '3')
                                        $dbeeText = '<br />' . ($rowdb['PicDesc'] != '' ? '<div style="font-size:12px;"><a href="/dbee/' . $comm_obj->generateUrl($dbeeurdd) . '">' . substr($rowdb['PicDesc'], 0, 300) . '</a></div>' : '<img src="'.IMGPATH.'/imageposts/small/' . $userprofile3 . '" width="80" border="0" />');
                                    elseif ($rowdb['Type'] == '4')
                                        $dbeeText = '<br /><div style="font-size:12px;"><a href="/dbee/' . $comm_obj->generateUrl($dbeeurdd) . '">' . substr($rowdb['VidDesc'], 0, 300) . '</a></div>';
                                    elseif ($rowdb['Type'] == '1')
                                        $dbeeText = '<br /><div style="font-size:12px;"><a href="/dbee/' . ($dbeeurdd) . '">' . substr($rowdb['Text'], 0, 300) . '</a></div>';
                                    elseif ($rowdb['Type'] == '2')
                                        $dbeeText = '<br /><div style="font-size:12px;"><a href="/dbee/' . ($dbeeurdd) . '">' . substr($rowdb['LinkDesc'], 0, 300) . '</a></div>';
                                    elseif ($rowdb['Type'] == '3')
                                        $dbeeText = '<br />' . ($rowdb['PicDesc'] != '' ? '<div style="font-size:12px;"><a href="/dbee/' . ($dbeeurdd) . '">' . substr($rowdb['PicDesc'], 0, 300) . '</a></div>' : '<img src="'.IMGPATH.'/imageposts/small/' . $userprofile3 . '" width="80" border="0" />');
                                    elseif ($rowdb['Type'] == '4')
                                        $dbeeText = '<br /><div style="font-size:12px;"><a href="/dbee/' . ($dbeeurdd) . '">' . substr($rowdb['VidDesc'], 0, 300) . '</a></div>';
                                } else
                                    $dbeeText = '';
                                if ($LeagueArr[$k]['ID'] == $user)
                                    $bg = ' background-color:#FFFA7F;';
                                else
                                    $bg = '';
                                $commonbynew    = new Application_Model_Commonfunctionality();
                                $userTypenal = $commonbynew->checkuserTypetooltip($LeagueArr[$k]['usertype']);
                                $checkImage = new Application_Model_Commonfunctionality();
                                $userprofile4 = $checkImage->checkImgExist($LeagueArr[$k]['ProfilePic'],'userpics','default-avatar.jpg');
                                $afterthree .= '<li>
                                                    <div class="prizeType">' . $counter . '</div>
                    								<div class="leaguesUserPic" style="background:url('.IMGPATH.'/users/small/' .$userprofile4. ') top center no-repeat; background-size: contain;"><a href="/user/' . $LeagueArr[$k]['Username'] . '"></a></div>
                                                    <div class="leagusListDetails">
                                                        <div class="leaguesTopContent">
                                                            <div class="leaguesUserName">
                                                                <a href="'.BASE_URL.'/user/' . $this->myclientdetails->customDecoding($LeagueArr[$k]['Username']) . '" rel="dbTip" title="'.$userTypenal.'">' . $this->myclientdetails->customDecoding($LeagueArr[$k]['Name']) . '</a>
                                                                <span class="leaguePts">' . $LeagueArr[$k]['Score'] . ' pts</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="lgprofileCell"> 
                                                         <a href="'.BASE_URL.'/user/' .$this->myclientdetails->customDecoding( $LeagueArr[$k]['Username']) . '" class="btn btn-mini">Profile</a>
                                                    </div>
                                                </li>';
                                $counter++;
                            }
                        }
                    }
                    $afterthree .= '</ul>';
                    // Need to update
                }
                else
                {
                    $noresult = "no results";
                }
                $seemoreval = count($LeagueArr)-1;
                if($seemoreval>15){
                $afterthree .= '<div id="see-more-leagues15"><div id="more-leagues-loader" style="cursor:pointer; color:#333333; text-align:center; margin-top:20px;" onClick="javascript:seemoreleagues(15,15,\'' . $league . '\',' . $counter . ');"><div class="nomoremessagedbee"><strong>see more</strong></div></div></div>';
              }
        } 
        elseif ($league == 'mostcomm') {
                
                $limit = '';
                for ($i = 1; $i <= 2; $i++) {
                    if ($i == 1) {
                        $limit        = '3';
                        $start        = 0;
                        $var          = 'topthree';
                        $counterclass = 'league-counter-big';
                        $commnumbox   = 'league-mostcomment-num-big';
                        $commlabel    = 'medium-grey-font-bold';
                        $textw        = '850';
                        $tww          = '95';
                        $tw           = '80';
                        $th           = '80';
                        $cupClass     = '' . $pricetype[$counter] . 'Cup cupSprite';
                        $cupcount     = 1;
                        $scoreCount   = 3;
                    } else {
                        $limit        = '12';
                        $start        = 3;
                        $var          = 'afterthree';
                        $counterclass = 'league-counter-small';
                        $commnumbox   = 'league-mostcomment-num-small';
                        $commlabel    = 'small-grey-font-bold';
                        $textw        = '880';
                        $tww          = '60';
                        $tw           = '45';
                        $th           = '45';
                        $cupClass     = '';
                    }
                    $mycustomdata = $DbleaguesTable->mycustomdata($limit, $start);
                    $myalldata = $DbleaguesTable->mycustomdata('0','0');
                    $seemoreval = count($myalldata);
                    

                    $$var .= '<ul class="leaguesLists" id="league-table-' . $i . '">';
                    foreach ($mycustomdata as $rowdb):
                     
                        $pricetype    = array(
                            'gold',
                            'silver',
                            'bronze'
                        );
                        $dbeeurlellow = $myhome_obj->getdburl($rowdb['DbeeID']);
                        $exist        = true;
                        $checkImage = new Application_Model_Commonfunctionality();
                        $userprofile5 = $checkImage->checkImgExist($rowdb['Pic'],'imageposts','default-avatar.jpg');
                        if ($rowdb['Type'] == '1')
                            $dbeeText = '<p><a href="/dbee/' . $comm_obj->generateUrl($dbeeurlellow) . '">' . substr($rowdb['Text'], 0, 300) . '</a></p>';
                        elseif ($rowdb['Type'] == '2')
                            $dbeeText = '<p><a href="/dbee/' . $comm_obj->generateUrl($dbeeurlellow) . '">' . substr($rowdb['LinkDesc'], 0, 300) . '</a></p>';
                        elseif ($rowdb['Type'] == '3')
                            $dbeeText = '<p>' . ($rowdb['PicDesc'] != '' ? '<a href="/dbee/' . $comm_obj->generateUrl($dbeeurlellow) . '">' . substr($rowdb['PicDesc'], 0, 300) . '</a></p>' : '<img src="'.IMGPATH.'/imageposts/small/' . $userprofile5 . '" width="80" border="0" />');
                        elseif ($rowdb['Type'] == '4')
                            $dbeeText = '<p><a href="/dbee/' . $comm_obj->generateUrl($dbeeurlellow) . '">' . substr($rowdb['VidDesc'], 0, 300) . '</a></p>';
                        if ($rowdb['User'] == $user)
                            $bg = ' background-color:#FFFA7F;';
                        else
                            $bg = '';
                        //	$$var.='<li><div class="prizeType '.$cupClass.'">'.$counter.'</div><div style="float:left; width:'.$textw.'px; font-size:14px;"><div class="'.$commnumbox.'">'.$rowdb['cnt'].'<br /><span class="'.$commlabel.'">comments</span></div><div style="float:left; margin-left:20px; width:700px;">'.$dbeeText.'<span style="font-size:12px">by</span> <a href="user/'.$rowdb['User'].'" class="small-link">'.$rowdb['Name'].'</a></div></div><br style="clear:both" /></li>';
                        if ($cupcount == 1) {
                            $mycupClass = 'cupSprite goldCup';
                        } else if ($cupcount == 2) {
                            $mycupClass = 'cupSprite silverCup';
                        } else if ($cupcount == 3) {
                            $mycupClass = 'cupSprite bronzeCup';
                        } else {
                            $mycupClass = '';
                        }
                        if ($cupcount > 3) {
                            $scoreCount++;
                            $showscore = $scoreCount;
                        } else {
                            $showscore = '';
                        }
                        if($rowdb['cnt']==1){
                         $Commentstring ='Comment';
                        }else{
                            $Commentstring ='Comments';
                        }
                        $commonbynew    = new Application_Model_Commonfunctionality();
                        $userTypenal = $commonbynew->checkuserTypetooltip($rowdb['usertype']);
                        $checkImage = new Application_Model_Commonfunctionality();
                        $userprofile6 = $checkImage->checkImgExist($rowdb['ProfilePic'],'userpics','default-avatar.jpg');
                        $$var .= '
							<li>
								<div class="prizeType  ' . $mycupClass . ' ">' . $showscore . '</div>
								<div class="leaguesUserPic" style="background:url('.IMGPATH.'/users/medium/'.$userprofile6.') top center no-repeat; background-size: contain;"></div>
								<div class="leagusListDetails" > 
									<div class="leaguesTopContent">
										<span class="leaguesUserName"><a href="/user/' . $this->myclientdetails->customDecoding($rowdb['Username']) . '" rel="dbTip" title="'.$userTypenal.'" > by ' . $this->myclientdetails->customDecoding($rowdb['Name']) . '</a></span>
										<span class="weekWinner"></span>
										<span class="dailyWinner"></span>
									</div>
									' . $dbeeText . '
								</div>
								<div class="leaguesPointsWrapper" >' . $rowdb['cnt'] . '<br><span style="font-size:14px;">'.$Commentstring.'</span></div>
							</li>';
                        $counter++;
                        $cupcount++;
                    endforeach;
                    $$var .= '</ul>';
                    $totlimit = $start+$limit;
                    if ($i == 2)
                        if($seemoreval>$totlimit){
                        $$var .= '<div id="see-more-leagues15"><div id="more-leagues-loader" style="cursor:pointer; color:#333333; text-align:center; margin-top:20px;" onClick="javascript:seemoreleagues(15,15,\'' . $league . '\',' . $counter . ');"><div class="nomoremessagedbee"><strong>see more</strong></div></div></div>';                       
                        }
                }
            }
        } else {
            	
            // MOST FOLLOWED LEAGUE STARTS
            $limit = '';
            for ($i = 1; $i <= 2; $i++) {
                if ($i == 1) {
                    	
                    $limit        = '3';
                    $start        = 0;
                    $var          = 'topthree';
                    $counterclass = 'league-counter-big';
                    $textw        = '750';
                    $tww          = '95';
                    $tw           = '80';
                    $th           = '80';
                    $cupClass     = '' . $pricetype[$counter] . 'Cup cupSprite';
                    $cupcount     = 1;
                    $scoreCount   = 3;
                } else {
                    $limit        = '12';
                    $start        = 3;
                    $var          = 'afterthree';
                    $counterclass = 'league-counter-small';
                    $textw        = '780';
                    $tww          = '60';
                    $tw           = '45';
                    $th           = '45';
                    $cupClass     = '';
                }
                
                if ($showtabs == '1') {
                    $return .= '<div><div class="user-name" style="margin-bottom:15px">db leagues</div><div class="next-line"></div><div class="maindb-wrapper-border" style="padding:10px">';
                    $return .= '<div style="width:auto; text-align:center;"><div id="leagues-tab-love" style="width:140px;" class="leagues-tab-active" onClick="' . $function . '(\'love\',' . $user . ')">love list</div><div id="leagues-tab-rogue" style="width:155px;" class="leagues-tab" onClick="' . $function . '(\'rogue\',' . $user . ')">rogues gallery</div><div id="leagues-tab-mostfollowed" style="width:150px;" class="leagues-tab" onClick="' . $function . '(\'mostfollowed\',' . $user . ')">most followed</div><div id="leagues-tab-philosopher" style="width:180px;" class="leagues-tab" onClick="' . $function . '(\'philosopher\',' . $user . ')">philosopher\'s corner</div></div><div class="next-line"></div>';
                }
                $mycustomdata = $DbleaguesTable->getfollowdata($limit, $start);
                $myalldata = $DbleaguesTable->getfollowdata('0','0');
                $$var .= '<ul class="leaguesLists" id="league-table-' . $i . '">';
                foreach ($mycustomdata as $Row):
                    $exist        = true;
                    $FollowerText = ($Row['Total'] > 1) ? $Row['Total'] . ' <br><span style="font-size:14px;">followers</span>' : $Row['Total'] . ' <br><span style="font-size:14px;">follower </span>';
                    $rowdb        = $DbleaguesTable->getdbeedata($this->myclientdetails->customDecoding($Row['User']), $PrivateGroups);
                    if (count($rowdb)) {
                        $checkImage = new Application_Model_Commonfunctionality();
                        $userprofile7 = $checkImage->checkImgExist($rowdb['Pic'],'imageposts','default-avatar.jpg');
                        if ($rowdb['Type'] == '1')
                            $dbeeText = '<p><a href="/dbee/' . $comm_obj->generateUrl($rowdb['dburl']) . '">' . substr($rowdb['Text'], 0, 300) . '</a></p>';
                        elseif ($rowdb['Type'] == '2')
                            $dbeeText = '<p><a href="/dbee/' . $comm_obj->generateUrl($rowdb['dburl']) . '">' . substr($rowdb['LinkDesc'], 0, 300) . '</a></p>';
                        elseif ($rowdb['Type'] == '3')
                            $dbeeText = '<p>' . ($rowdb['PicDesc'] != '' ? '<a href="/dbee/' . $comm_obj->generateUrl($rowdb['dburl']) . '">' . substr($rowdb['PicDesc'], 0, 300) . '</a></p>' : '<img src="'.IMGPATH.'/imageposts/small/'.$userprofile7.'" width="80" border="0" />');
                        elseif ($rowdb['Type'] == '4')
                            $dbeeText = '<p><a href="/dbee/' . $comm_obj->generateUrl($rowdb['dburl']) . '">' . substr($rowdb['VidDesc'], 0, 300) . '</a></p>';
                    } else
                        $dbeeText = '';
                    if ($Row['User'] == $user)
                        $bg = ' background-color:#FFFA7F;';
                    else
                        $bg = '';
                    if ($cupcount == 1) {
                        $mycupClass = 'cupSprite goldCup';
                    } else if ($cupcount == 2) {
                        $mycupClass = 'cupSprite silverCup';
                    } else if ($cupcount == 3) {
                        $mycupClass = 'cupSprite bronzeCup';
                    } else {
                        $mycupClass = '';
                    }
                    if ($cupcount > 3) {
                        $scoreCount++;
                        $showscore = $scoreCount;
                    } else {
                        $showscore = '';
                    }
                    $commonbynew    = new Application_Model_Commonfunctionality();
                    $userTypenal = $commonbynew->checkuserTypetooltip($Row['usertype']);
                    $checkImage = new Application_Model_Commonfunctionality();
                    $userprofile8 = $checkImage->checkImgExist($Row['ProfilePic'],'userpics','default-avatar.jpg');         
                    $$var .= '
							<li>
								<div class="prizeType  ' . $mycupClass . ' ">' . $showscore . '</div>
								<div class="leaguesUserPic" style="background:url('.IMGPATH.'/users/medium/'.$userprofile8.') top center no-repeat; background-size: contain;"><a href="/user/' . $Row['Username'] . '"></a></div>
								<div class="leagusListDetails" > 
									<div class="leaguesTopContent">
										<span class="leaguesUserName"><a href="/user/' . $this->myclientdetails->customDecoding($Row['Username']) . '" rel="dbTip" title="'.$userTypenal.'"> ' . $this->myclientdetails->customDecoding($Row['Name']) . '</a></span>
										<span class="weekWinner"></span>
										<span class="dailyWinner"></span>
									</div>
									' . $dbeeText . '
								</div>
								<div class="leaguesPointsWrapper" >' . $FollowerText . '</div>
							</li>';
                    $counter++;
                    $cupcount++;
                endforeach;
                $$var .= '</ul>';
                //need to update========funton prams and id=================================
                $totlimit = $start+$limit;
                
                if ($i == 2)
                    if($seemoreval>$totlimit){
                    $$var .= '<div id="see-more-leagues15"><div id="more-leagues-loader" style="cursor:pointer; color:#333333; text-align:center; margin-top:20px;" onClick="javascript:seemoreleagues(15,15,\'' . $league . '\',' . $counter . ');"><div class="nomoremessagedbee"><strong>see more</strong></div></div></div>';
                   }
            }
        }
        if (!$exist) {
            $result = '<div style="color:#999; margin-left:20px;"></div>';
        }
        $this->view->user       = $this->_userid;
        $this->view->league     = $league;
        $this->view->showtabs   = $showtabs;
        $this->view->topthree   = $topthree;
        $this->view->afterthree = $afterthree;
        $this->view->exist      = $exist;
        $response               = $this->_helper->layout->disableLayout();
        //return $response;
    }
    public function commentleagueAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $request    = $this->getRequest()->getParams();
        $result     = array();
        $userId     = $this->_userid;
        $groupleage = $this->leagueObj->getCommentIdsofDbs($request['dbid'], 'dbee');
        $mostlove   = $this->leagueObj->getLeagereport($groupleage, 'love', 'single'); // Love leage toppers
        $mostfot    = $this->leagueObj->getLeagereport($groupleage, 'fot', 'single'); // Love leage toppers
        $mosthate   = $this->leagueObj->getLeagereport($groupleage, 'hate', 'single'); // Love leage toppers
        $lovearr    = explode('~', $mostlove);
        $fotarr     = explode('~', $mostfot);
        $hatearr    = explode('~', $mosthate);
        $count      = $lovearr[0] . '' . $fotarr[0] . '' . $hatearr[0];
        $result     = '<div id="comment-league2" class="commentodd">
      	<div style="float:left;text-align:center;margin-left:7px;">
      		<div class="minileague-user">' . $lovearr[0] . '</div>
      	</div>
      	<div style="margin-left:7px;float:left; text-align:center">
      		<div class="minileague-user">' . $fotarr[0] . '</div>
      	</div>
      	<div style="margin-left:7px;float:left;text-align:center">
      		<div class="minileague-user">' . $hatearr[0] . '</div>
      	</div>
      	<div id="singledbleages" class="btn btn-yellow"><span style="vertical-align: middle;">db leagues </span></div><br style="clear:both"></div>';
        echo $result . '~' . $count;
        exit;
    }
    public function getleauebyscore($league, $user)
    {
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
    public function fetchdbleaguesAction()
    {
        if ($this->$this->userid) {
            $id = $this->userid;
        }
        $request    = $this->getRequest();
        $type       = $request->getpost('type');
        $cookieuser = $this->userid;
        $CurrDate   = date('Y-m-d H:i:s');
        $expire     = time() + 60 * 60 * 24 * 10;
        setcookie('currloginlastseen', $CurrDate, $expire);
        $followingtbl      = new Application_Model_Following();
        $NotificationTable = new Application_Model_Following();
        if ($type == 1) {
            $following = $followingtbl->getfollingauto($id);
            if (!count($following) == '')
                $mdata = $NotificationTable->getdbeesuser1($following);
            else
                $mdata = 0;
        } elseif ($type == 2) {
            $dbeeid = $NotificationTable->redbeeid($id);
            if (!count($dbeeid) == '')
                $mdata = $NotificationTable->getdbeenotify($dbeeid);
            else
                $mdata = 0;
        }
        $this->view->row2 = $mdata;
        $response         = $this->_helper->layout->disableLayout();
        return $response;
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
    public function userleaguemoreAction()
    {
        $request        = $this->getRequest();
        $league         = $request->getpost('league');
        $start          = $request->getpost('start');
        $end            = $request->getpost('end');
        $counter        = $request->getpost('counter');
        $user           = $this->_userid;
        $DbleaguesTable = new Application_Model_Dbleagues();
        $comm_obj       = new Application_Model_Commonfunctionality();
        $private        = $DbleaguesTable->privateuserleague();
        // leage table start
        $result         = '';
        if ($league != 'mostfollowed') {
            if ($league != 'mostcomm') {
                $LeagueArr = $this->getleauebyscore($league, $user);
                
                if (count($LeagueArr) >= $start + $end) {
                    $result .= '<ul class="leaguesLists" id="league-table-afterthree">';
                    $end = $start + 15;
                    for ($k = $start; $k < $end; $k++) {
                        $exist = true;
                        $rowdb = $DbleaguesTable->topthreedata($LeagueArr[$k]['ID'], $private);
                        
                        if ($rowdb) {
                            $checkImage = new Application_Model_Commonfunctionality();
                            $userprofilepico1 = $checkImage->checkImgExist($rowdb['Pic'],'imageposts','default-avatar.jpg');
                            if ($rowdb['Type'] == '1')
                                $dbeeText = '<br /><div style="font-size:12px;"><a href="/dbee/' . $comm_obj->generateUrl($dbeeurlellow) . '">' . substr($rowdb['Text'], 0, 300) . '</a></div>';
                            elseif ($rowdb['Type'] == '2')
                                $dbeeText = '<br /><div style="font-size:12px;"><a href="/dbee/' . $comm_obj->generateUrl($dbeeurlellow) . '">' . substr($rowdb['LinkDesc'], 0, 300) . '</a></div>';
                            elseif ($rowdb['Type'] == '3')
                                $dbeeText = '<br />' . ($rowdb['PicDesc'] != '' ? '<div style="font-size:12px;"><a href="/dbee/' . $comm_obj->generateUrl($dbeeurlellow) . '">' . substr($rowdb['PicDesc'], 0, 300) . '</a></div>' : '<img src="'.IMGPATH.'/imageposts/small/' . $userprofilepico1 . '" width="80" border="0" />');
                            elseif ($rowdb['Type'] == '4')
                                $dbeeText = '<br /><div style="font-size:12px;"><a href="/dbee/' . $comm_obj->generateUrl($dbeeurlellow) . '">' . substr($rowdb['VidDesc'], 0, 300) . '</a></div>';
                        } else
                            $dbeeText = '';
                        if ($LeagueArr[$k]['ID'] == $user)
                            $bg = ' background-color:#FFFA7F;';
                        else
                            $bg = '';
                        $commonbynew    = new Application_Model_Commonfunctionality();
                        $userTypenal = $commonbynew->checkuserTypetooltip($LeagueArr[$k]['usertype']);
                        $checkImage = new Application_Model_Commonfunctionality();
                        $userprofilepico2 = $checkImage->checkImgExist($LeagueArr[$k]['ProfilePic'],'userpics','default-avatar.jpg');

                        $result .= '<li><div class="prizeType">' . $counter . '</div>
								<div class="leaguesUserPic" style="background:url('.IMGPATH.'/users/medium/'.$userprofilepico2.') top center no-repeat; background-size: contain;"><a href="/user/' . $LeagueArr[$k]['Username'] . '"></a></div><div class="leagusListDetails"><div class="leaguesTopContent">
                                <div class="leaguesUserName"><a href="/user/' . $LeagueArr[$k]['Username'] . '" rel="dbTip" title="'.$userTypenal.'">' . $LeagueArr[$k]['Name'] . '</a></div></div>' . $dbeeText . '</div><div class="leaguesPointsWrapper">' . $LeagueArr[$k]['Score'] . ' pts</div></li>';
                        $counter++;
                    }
                    $result .= '</ul>';
                } else {
                    $feedend = true;
                }
                // Need to update
                if (!$feedend) {
                    $startnew = $start + 15;
                    if($rowdb==15){
                    $result .= '<div id="see-more-leagues' . $startnew . '"><div id="more-leagues-loader" style="cursor:pointer; color:#333333; text-align:center; margin-top:20px;" onClick="javascript:seemoreleagues(' . $startnew . ',15,\'' . $league . '\',' . $counter . ');"><div class="nomoremessagedbee"><strong>see more</strong></div></div></div>';
                   }
                } else {
                    $result .= '<div><div id="more-leagues-loader" style="color:#333333; text-align:center; margin-top:20px;"><div class="nomoremessagedbee"><strong>no more records to show</strong></div></div></div>';
                }
            } elseif ($league == 'mostcomm') {
                $counterclass = 'league-counter-small';
                $textw        = '780';
                $tww          = '60';
                $tw           = '45';
                $th           = '45';
                
                $mycustomdata = $DbleaguesTable->mycustomdata(15, $start);
                
                if (count($mycustomdata) > 0) {
                    $result .= '<ul class="leaguesLists" id="league-table-' . $i . '">';
                    foreach ($mycustomdata as $rowdb):
                    
                        $checkImage = new Application_Model_Commonfunctionality();
                        $userprofilepico3 = $checkImage->checkImgExist($rowdb['Pic'],'imageposts','default-avatar.jpg');
                        $exist = true;
                        if ($rowdb['Type'] == '1')
                            $dbeeText = '<br /><div style="font-size:12px;"><a href="/dbee/' . $comm_obj->generateUrl($dbeeurlellow) . '">' . substr($rowdb['Text'], 0, 300) . '</a></div>';
                        elseif ($rowdb['Type'] == '2')
                            $dbeeText = '<br /><div style="font-size:12px;"><a href="/dbee/' . $comm_obj->generateUrl($dbeeurlellow) . '">' . substr($rowdb['LinkDesc'], 0, 300) . '</a></div>';
                        elseif ($rowdb['Type'] == '3')
                            $dbeeText = '<br />' . ($rowdb['PicDesc'] != '' ? '<div style="font-size:12px;"><a href="/dbee/' . $comm_obj->generateUrl($dbeeurlellow) . '">' . substr($rowdb['PicDesc'], 0, 300) . '</a></div>' : '<img src="'.IMGPATH.'/imageposts/small/' .$userprofilepico3. '" width="80" border="0" />');
                        elseif ($rowdb['Type'] == '4')
                            $dbeeText = '<br /><div style="font-size:12px;"><a href="/dbee/' . $comm_obj->generateUrl($dbeeurlellow) . '">' . substr($rowdb['VidDesc'], 0, 300) . '</a></div>';
                        if ($rowdb['User'] == $user)
                            $bg = ' background-color:#FFFA7F;';
                        else
                            $bg = '';
                        if($rowdb['cnt']==1){
                         $Commentstring ='Comment';
                        }else{
                            $Commentstring ='Comments';
                        }
                        $checkImage = new Application_Model_Commonfunctionality();
                        $userprofilepico4 = $checkImage->checkImgExist($rowdb['ProfilePic'],'userpics','default-avatar.jpg');
                        $result .= '
							<li>
								<div class="prizeType  ' . $mycupClass . ' ">' . $counter . '</div>
								<div class="leaguesUserPic" style="background:url('.IMGPATH.'/users/medium/' . $userprofilepico4 . ') top center no-repeat; background-size: contain;"></div>
								<div class="leagusListDetails" > 
									<div class="leaguesTopContent">
										<span class="leaguesUserName"><a href="/user/' . $this->myclientdetails->customDecoding($rowdb['Username']) . '" > by ' . $this->myclientdetails->customDecoding($rowdb['Name']) . '</a></span>
										<span class="weekWinner"></span>
										<span class="dailyWinner"></span>
									</div>
									' . $dbeeText . '
								</div>
								<div class="leaguesPointsWrapper" >' . $rowdb['cnt'] . '<br><span style="font-size:14px;">'.$Commentstring.'</span></div>
							</li>';
                        $counter++;
                        $cupcount++;
                    endforeach;
                    $result .= '</ul>';
                } else {
                    $feedend = true;
                }
                if (!$feedend) {
                    // Should update ------------------------------
                    $startnew = $start + 15;
                    if(count($mycustomdata)==15){
                    $result .= '<div id="see-more-leagues' . $startnew . '"><div id="more-leagues-loader" style="cursor:pointer; color:#333333; text-align:center; margin-top:20px;" onClick="javascript:seemoreleagues(' . $startnew . ',15,\'' . $league . '\',' . $counter . ');"><div class="nomoremessagedbee" id="'.count($mycustomdata).'"><strong >see more</strong></div></div></div>';
                    }
                } else {
                    $result .= '<div><div id="more-leagues-loader" style="color:#333333; text-align:center; margin-top:20px;"><div class="nomoremessagedbee"><strong>no more records to show</strong></div></div></div>';
                }
            }
        } else {
            $counterclass = 'league-counter-small';
            $textw        = '780';
            $tww          = '60';
            $tw           = '45';
            $th           = '45';
            
            $mycustomdata = $DbleaguesTable->getfollowdata(15, $counter);
            
            if (count($mycustomdata) > 0) {
                $result .= '<ul class="leaguesLists" id="league-table-' . $i . '">';
                foreach ($mycustomdata as $Row):
                    $exist        = true;
                    $FollowerText = ($Row['Total'] > 1) ? $Row['Total'] . ' <br><span style="font-size:14px;">followers</span>' : $Row['Total'] . ' <br><span style="font-size:14px;">follower </span>';
                    $rowdb        = $DbleaguesTable->getdbeedata($Row['User'], $PrivateGroups);
                    if (count($rowdb)) {
                        $checkImage = new Application_Model_Commonfunctionality();
                        $userprofilepico5 = $checkImage->checkImgExist($rowdb['Pic'],'imageposts','default-avatar.jpg');

                        if ($rowdb['Type'] == '1')
                            $dbeeText = '<p><a href="/dbee/' . $comm_obj->generateUrl($dbeeurlellow) . '">' . substr($rowdb['Text'], 0, 300) . '</a></p>';
                        elseif ($rowdb['Type'] == '2')
                            $dbeeText = '<p><a href="/dbee/' . $comm_obj->generateUrl($dbeeurlellow) . '">' . substr($rowdb['LinkDesc'], 0, 300) . '</a></p>';
                        elseif ($rowdb['Type'] == '3')
                            $dbeeText = '<p>' . ($rowdb['PicDesc'] != '' ? '<a href="/dbee/' . $comm_obj->generateUrl($dbeeurlellow) . '">' . substr($rowdb['PicDesc'], 0, 300) . '</a></p>' : '<img src="'.IMGPATH.'/imageposts/small/' . $userprofilepico5 . '" width="80" border="0" />');
                        elseif ($rowdb['Type'] == '4')
                            $dbeeText = '<p><a href="/dbee/' . $comm_obj->generateUrl($dbeeurlellow) . '">' . substr($rowdb['VidDesc'], 0, 300) . '</a></p>';
                    } else
                        $dbeeText = '';
                    if ($Row['User'] == $user)
                        $bg = ' background-color:#FFFA7F;';
                    else
                        $bg = '';
                    $checkImage = new Application_Model_Commonfunctionality();
                    $userprofilepico6 = $checkImage->checkImgExist($Row['ProfilePic'],'userpics','default-avatar.jpg');
                    $result .= '<li>
							<div class="prizeType  ' . $mycupClass . ' ">' . $counter . '</div>
								<div class="leaguesUserPic" style="background:url('.IMGPATH.'/users/medium/'.$userprofilepico6.') top center no-repeat; background-size: contain;"><a href="/user/' . $Row['Username'] . '"></a></div>
								<div class="leagusListDetails" > 
									<div class="leaguesTopContent">
										<span class="leaguesUserName"><a href="/user/' . $this->myclientdetails->customDecoding($Row['Username']) . '" > ' . $this->myclientdetails->customDecoding($Row['Name']) . '</a></span>
										<span class="weekWinner"></span>
										<span class="dailyWinner"></span>
									</div>
									' . $dbeeText . '
								</div>
								<div class="leaguesPointsWrapper" >' . $FollowerText . '</div>
							</li>';
                    $counter++;
                endforeach;
                $result .= '</ul>';
                $startnew = $start + 15;
                if(count($mycustomdata)==15){
                $result .= '<div id="see-more-leagues' . $startnew . '"><div id="more-leagues-loader" style="cursor:pointer; color:#333333; text-align:center; margin-top:20px;" onClick="javascript:seemoreleagues(' . $startnew . ',15,\'' . $league . '\',' . $counter . ');"><div class="nomoremessagedbee"><strong>see more</strong></div></div></div>';
                }
            } else {
                $result .= '<div><div id="more-leagues-loader" style="color:#333333; text-align:center; margin-top:20px;">
				<div class="nomoremessagedbee"><strong>- no more records to show -</strong></div></div></div>';
            }
        }
        if ($showtabs == '1') {
            $result .= '<br style="clear:both; font-size:1px" /></div></div>';
        }
        $this->view->result   = $result;
        $this->view->startnew = $startnew;
        $this->view->league   = $league;
        $this->view->counter  = $counter;
        $response             = $this->_helper->layout->disableLayout();
        return $response;
    }
    function leagueTable($league, $user = 0, $start = 0, $commusers = 0, $db = 0)
    {
        $Arr1 = array();
        $Arr2 = array();
        $Arr3 = array();
        //-------------------------------------------
        // CALCULATE LEAGUE POINTS AND ARRANGE USERS
        if ($league == 'love') {
            $score1 = '1';
            $score2 = '2';
        } elseif ($league == 'rogue') {
            $score1 = '5';
            $score2 = '4';
        } elseif ($league == 'philosopher') {
            $score1 = '0';
            $score2 = '3';
        }
        $profilepicsave_obj = new Application_Model_Profile();
        $Res                = $profilepicsave_obj->getleagutablescore($score1, $db, $commusers);
        //print_r($Res);
        if (count($Res) > 0) {
            $totallike = 0;
            foreach ($Res as $Row) {
                $Arr1[$Row['Owner']]['ID']         = $Row['Owner'];
                $Arr1[$Row['Owner']]['Name']       = $Row['Name'];
                $Arr1[$Row['Owner']]['ProfilePic'] = $Row['ProfilePic'];
                $Arr1[$Row['Owner']]['Score']      = $Row['TotalScore'];
                $IDArr1[$totallike]                = $Row['Owner'];
                $totallike++;
            }
        }
        $Res = $profilepicsave_obj->getleagutablescore($score2, $db, $commusers);
        if (count($Res) > 0) {
            $totallove = 0;
            foreach ($Res as $Row) {
                $Arr2[$Row['Owner']]['ID']         = $Row['Owner'];
                $Arr2[$Row['Owner']]['Name']       = $Row['Name'];
                $Arr2[$Row['Owner']]['ProfilePic'] = $Row['ProfilePic'];
                $Arr2[$Row['Owner']]['Score']      = $Row['TotalScore'];
                $IDArr2[$totallove]                = $Row['Owner'];
                $totallove++;
            }
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
            $Arr3[$i]['ProfilePic'] = $useName[$useID[$i]]['ProfilePic'];
            $Arr3[$i]['Score']      = $Arr1[$useID[$i]]['Score'] + $Arr2[$useID[$i]]['Score'];
        }
        //if($Arr3[0]['ID']!='')
        //$Arr3=sortmddata($Arr3,'Score','DESC','num');
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
    public function createleagueAction()
    {
        $showhidetxt = '';
        //if ($this->getRequest()->isXmlHttpRequest()) {
        $myhome_obj  = new Application_Model_Myhome;
        $league_obj  = new Application_Model_Dbleagues();
        $hidedbs     = $myhome_obj->chkdbeeexitleagueall($this->_userid, 'hide');
        if (count($hidedbs) > 0 && $hidedbs != '') {
            $showhidetxt              = '<a id="allDbAlAdd" class="pull-right" href="javascript:void(0)">Show unavailable '.POST_NAME.'\'s</a>';
            $this->view->hidedbeelist = $hidedbs;
        } else {
            $showhidetxt              = '';
            $this->view->hidedbeelist = $hidedbs;
        }
        $this->view->sowhidelink = $showhidetxt;
        $this->view->dbeelist    = $myhome_obj->chkdbeeexitleagueall($this->_userid, 'show');
        $response                = $this->_helper->layout->disableLayout();
        return $response;
    }
    public function followingleagueAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest()) {
            $usertype   = '';
            $usertype   = $this->_request->getPost('type');
            $keyword    = $this->_request->getPost('keyword');
            $league_obj = new Application_Model_Dbleagues();
            //$league_obj->getfollowingleague($this->_userid);
            $keyword = $this->myclientdetails->customEncoding($keyword,'searchleague');
            $return     = '';
            if ($usertype == 'searchuser') {
                $row = $league_obj->getusers($keyword);
            } else {
                if ($usertype == 'following') {
                    $row = $league_obj->getfollowersleaguetypes($this->_userid);
                } elseif ($usertype == 'followers') {
                    $row = $league_obj->getfollowingleaguetypes($this->_userid);
                }
            }
            $TotalUsers = count($row);
            if ($TotalUsers > 0) {
                $counter = 1;
                foreach ($row as $value) {
                    $UserID = $value['UserID'];
                    $checkImage = new Application_Model_Commonfunctionality();
                    $userprofilepico7 = $checkImage->checkImgExist($value['ProfilePic'],'userpics','default-avatar.jpg');
                    // CHECK IF USER FOLLOWS PROFILE HOLDER, AND ALLOWS TO BE NOTIFIED
                    $return .= '<div class="boxFlowers" ' . $title . '><label class="labelCheckbox"><input type="checkbox" id="inviteuser-' . $usertype . $counter . '" value="' . $value['UserID'] . '" ' . $Disabled . '><div class="follower-box" ' . $BG . '><img src="'.IMGPATH.'/users/small/' . $userprofilepico7 . '" width="50" height="50" border="0" /><br /><div class="oneline">' . $this->myclientdetails->customDecoding($value['Name']) . '</div></div>' . $inviteLabel . '</label></div>';
                    if ($counter % 7 == 0)
                        //$return .= '<div class="next-line"></div>';
                    $counter++;
                }
                $return .= "<div class='next-line'></div><div style='margin-top:15px; overflow:hidden'><div style='float:left; margin-top:10px;'>";
                $return .= "<a href='javascript:void(0);' onclick='javascript:skipinviteleague();'>skip invite</a>";
                $return .= "</div>";
                $return .= "<div style='float:right; margin-top:9px; margin-right:20px;'> </div>";
                $return .= "</div><br style='clear:both;' />";
            } else {
                $return .= '<div class="noFound" style="margin-top:50px; margin-bottom:50px;"> No users found.</div>';
                $return .= "<div style='margin-top:105px;'><div style='float:left;'><a href='javascript:void(0);' onclick='javascript:skipinviteleague();'>skip invite</a></div></div>";
            }
            $data['totaluser'] = $TotalUsers;
            $data['userlist']  = $return;
            $data['type']      = $type;
            return $response->setBody(Zend_Json::encode($data));
        }
    }
    public function listleagueAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest()) {
            $leagetype  = $this->_request->getPost('type');
            $curdate    = date('Y-m-d');
            $row        = $this->dbleaguesTable->listleague($this->_userid, $curdate);
            if (count($row) > 0) {
                $counter = 1;
                $return .= "<h2>&nbsp;</h2><ul id='myleaguesPwrapper'>";
                foreach ($row as $value) {
                    $return .= "<li>
				<h4><a href='".BASE_URL."/league/index/id/" . $value['LID'] . "'>" . $value['Title'] . "</a></h4>
				<div class='myleaguesDates'>
					<div class='myleagueStartDate'>Start Date : " . date('d - F - Y - H:i', strtotime($value['StartDate'])) . "</div>
					<div class='myleagueEndDate'>End Date : " . date('d - F - Y - H:i', strtotime($value['EndDate'])) . "</div>
				</div>
				<div class='myleagueDes'> " . $value['Discription'] . "</div>";
                    $return .= "</li>";
                }
                $data['success'] = 'success';
            } else {
                $return .= "<ul id='myleaguesPwrapper'>";
                $return .= '<li class="noLeagues"> <div class="noFound" style="margin-top:50px; margin-bottom:50px;">You have not created a league yet.</div>  </li>';
            }
        }
        $return .= "</ul>";
        $data['content'] = $return;
        $data['type']    = $leagetype;
        return $response->setBody(Zend_Json::encode($data));
    }
    public function selecttoinviteAction()
    {
        $data  = array();
      
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);

        if ($this->getRequest()->isXmlHttpRequest()) 
        {
			$user  = $this->_request->getPost('users');
			$users = substr($user, 0, -1);
			$users = explode(',', $users);
			foreach ($users as $user):
				$newuser[] = $user;
			endforeach;

            if (count($newuser)) 
            {
                $allsqlquery = $this->dbleaguesTable->getallusers($newuser);
                $TotalUsers  = count($allsqlquery);
                if ($TotalUsers > 0) {
                    $counter = 1;
                    foreach ($allsqlquery as $key => $Row) {
                        $checkImage = new Application_Model_Commonfunctionality();
                        $userprofilepico8 = $checkImage->checkImgExist($Row['ProfilePic'],'userpics','default-avatar.jpg');
                        $return .= '<div id="select-invite-' . $Row['UserID'] . '" class="follower-box"><img src="'.IMGPATH.'/users/small/' . $userprofilepico8. '" width="50" height="50" border="0" /><br /><div class="oneline">' . $this->myclientdetails->customDecoding($Row['Name']) . '</div></div>';
                        if ($counter % 9 == 0)
                            $return .= '<div class="next-line"></div>';
                        $counter++;
                    }
                } else {
                    $return .= '- No users selected -';
                }
            }
            $data['totaluser'] = $TotalUsers;
            $data['content']   = $return;
            $data['users']     = $users;
            return $response->setBody(Zend_Json::encode($data));
        }
    }
    public function insertdataAction()
    {
        $request = $this->getRequest()->getParams();
        //echo'<pre>';print_r($request);
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->getMethod() == 'POST') {
            $filter       = new Zend_Filter_StripTags();
            $title        = $filter->filter(stripslashes($this->_request->getPost('Title')));
            $disc         = $filter->filter(stripslashes($this->_request->getPost('Discription')));
            $leaguedbee   = $filter->filter(stripslashes($this->_request->getPost('leaguedbee')));
            $enddate      = date('Y-m-d H:i:s', strtotime($this->_request->getPost('EndDate')));
            $users        = $this->_request->getPost('users');
            $startDate    = date('Y-m-d H:i:s');
            $usersdbee    = substr($users, 0, -1);
            $users        = explode(',', $usersdbee);
            $userid       = $this->_userid;
            $data         = array(
                'clientID' => clientID,
                'Title' => $title,
                "StartDate" => $startDate,
                "EndDate" => $enddate,
                "UserID" => $userid,
                "Discription" => $disc,
                "Status" => '1'
            );
            
            $insertleague = $this->myclientdetails->insertdata_global('tblUserLeague',$data);

            foreach ($users as $user):
                $newuser[] = $user;
            endforeach;
            //echo'<pre>';print_r($leaguedbee);
            $leaguedbee     = substr($leaguedbee, 0, -1);
            //echo'<pre>';print_r($leaguedbee);die('<----');
            $leaguedbeelist = explode(',', $leaguedbee);
            foreach ($leaguedbeelist as $dbees):
                $data2 = '';
                if ($dbees != 0) {
                    $data2         = array(
                        'clientID' => clientID,
                        'LID' => $insertleague,
                        "DbeeID" => $dbees,
                        "Enddate" => $enddate,
                        "Status" => '1'
                    );
                    $insertleague1 = $this->myclientdetails->insertdata_global('tblLeagueDbee',$data2);
                }
            endforeach;
            $data['dbee']         = $leaguedbeelist;
            $data['content']      = $return;
            $data['users']        = $usersdbee;
            $data['insertleague'] = $insertleague;
            return $response->setBody(Zend_Json::encode($data));
        }
    }
    public function sendleagueinviteAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);

        $myhome_obj = new Application_Model_Myhome();
        if ($this->getRequest()->getMethod() == 'POST') {
            $filter    = new Zend_Filter_StripTags();
            $users     = $this->_request->getPost('users');
            $leagueid  = $this->_request->getPost('leaguedb');
            $usersdbee = substr($users, 0, -1);
            $userss    = explode(',', $usersdbee);
            $JoinDate  = date('Y-m-d H:i:s');
            $return    = '';
            if ($users != '') {
                foreach ($userss as $user):
                    $newuser[] = $user;
                endforeach;
                $checkuser = $this->dbleaguesTable->getallusers($newuser);
                if (count($checkuser) > 0) {

                    foreach ($checkuser as $row) {
                       
                        if ($row['UserID'])
                            $this->notification->commomInsert('14', '22', $leagueid, $this->_userid, $row['UserID']); // Insert for redb activity
                            $userleaguesend = $this->myhome_obj->getusername2($this->_userid);
                            $userleagueacce = $this->myhome_obj->getusername($row['UserID']);
        /****for email ****/ 
        //mann.delus@gmail.com,anildbee@gmail.com       
        $EmailTemplateArray = array('Email' => $row['Email'],
                                     'Name' => $row['Name'],
                                     'lname'=> $row['lname'],
                                    'userleaguesend' => $this->myclientdetails->customDecoding($userleaguesend),
                                    'userleagueacce' => $this->myclientdetails->customDecoding($userleagueacce),
                                    'leagueid' => $leagueid,
                                    'case'=>15);
        $bodyContentmsgall = $this->dbeeComparetemplateOne($EmailTemplateArray);
        /****for email ****/                        
                    }
                }
            }
        }
        $data['content'] = 'success';
        return $response->setBody(Zend_Json::encode($data));
    }
}