<?php

class Admin_IndexController extends IsadminController
{
    
    private $options;
    public $defaultimagecheck;
    public function init()
    { 
        parent::init();
    }
    
    public function preDispatch()
    {
        $auth = Zend_Auth::getInstance();   
        if ($auth->hasIdentity()) 
        {
            $data = $auth->getStorage()->read();
        }else
            $this->_redirect('/admin/login');
    }
    //**************************** ACTION Start FOR ADVANCE SEARCH AUTOSUGGEST @19 July 2013 *******************************
    public function autoselectAction()
    {
        $request   = $this->getRequest()->getParams();
        $deshboard = new Admin_Model_Deshboard();
        
        
        if ($request['lookon'] == "Email") {
            $dbfield = "Email";
        } else //  if($request['lookon']=="User")
            {
            $dbfield = "Name";
        }
        $term    = $this->myclientdetails->customEncoding($request['term'], 'autoselect');
        $query   = "SELECT $dbfield as user,ProfilePic as pic, UserID FROM tblUsers where clientID=" . clientID . " AND $dbfield like '%" . $term . "%' AND Status = 1  ";
        $datares = $deshboard->SearchDbees($query);
        
        //  echo json_encode( $datares );
        
        $result  = array();
        $pic     = array();
        $UserDtl = array();
        
        foreach ($datares as $key => $value) {
            $keypic           = $this->common_model->checkImgExist($value['pic'], 'imageposts', 'default-avatar.jpg');
            $result['user']   = $this->myclientdetails->customDecoding($value['user']);
            $result['pic']    = $keypic;
            $result['UserID'] = $value['UserID'];
            $UserDtl[]        = $result;
        }
        echo json_encode($UserDtl);
        //echo $ret;
        exit;
    }
    
    public function deleteglobalfiletrsAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $request = $this->getRequest()->getPost();
        
        
        $filterid = $request['filterid'];
        
        $filtdel    = $this->myclientdetails->deletedata_global('tblSearchFilter', 'filter_id', $filterid);
        $filtatrdel = $this->myclientdetails->deletedata_global('tblSearchAttr', 'filter_id', $filterid);
        
        if ($filtdel) {
            echo true;
        } else {
            echo false;
        }
        
        exit;
    }
    public function loadglobalfiletrsAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $request = $this->getRequest()->getPost();
        $filter  = new Admin_Model_Searchfilter();
        $userMod = new Admin_Model_User();
        $user_id = $_SESSION['Zend_Auth']['storage']['userid'];
        
        $filterid = $request['filterid'];
        $isglobal = $request['global'];
        
        $filtAttr = $filter->loadFilterAttr($filterid);
        
        $ret = '';
        $i   = 0;
        foreach ($filtAttr as $key => $value) {
            # code...
            $uid = $value['filt_attr_value'];
            
            if (is_numeric($uid)) {
                $unam = $userMod->getUsers($uid, 'userdetails');
                if (count($unam) > 0)
                    $uname = $unam[0]['Name'];
            } else {
                $uname = $value['filt_attr_value'];
            }
            
            
            $i++;
            if ($uname != '') {
                $ret .= '<div class="whiteBox conditiontext chk_' . $i . '" > ' . $value['filt_attr_on'] . ' ' . $value['filt_attr_condition'] . '<strong> ' . $uname . '</strong><a href="javascript:void(0)" class="closeCondition removeCondition" id="' . $i . '"></a></div>';
                
                $ret .= '<span class="conditiontext txt_' . $i . '" style="display:none" >
				<input type="hidden" name="operator[]" id="search_on_' . $i . '" value="' . $value['filt_attr_on'] . '">
				<input type="hidden" name="conditions[]" id="search_condition_' . $i . '" value="' . $value['filt_attr_condition'] . '">
				<input type="hidden" name="selectOptions[]" id="search_value_' . $i . '" value="' . $value['filt_attr_value'] . '"></span>';
            }
            
        }
        echo $ret;
        
        exit;
    }
    public function saveglobalfiletrsAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $request = $this->getRequest()->getPost();
        $filter  = new Admin_Model_Searchfilter();
        $deshObj = new Admin_Model_Deshboard();
        
        $user_id = $_SESSION['Zend_Auth']['storage']['UserID'];
        
        
        
        $allarray = explode(',', $request['allinone_']);
        if (trim($request['filtername']) != '') {
            $data          = array(
                'clientID' => clientID,
                'filter_name' => $request['filtername'],
                'searchtag' => '1',
                'filter_created_by' => $user_id
            );
            $chkfoldername = $this->myclientdetails->getfieldsfromtable(array(
                'filter_name'
            ), 'tblSearchFilter', 'filter_name', $request['filtername']);
            if (count($chkfoldername) < 1)
                $filter_id = $filter->insertfilter($data);
            else {
                echo "403";
                exit;
            }
        } else {
            echo '404';
            exit;
        }
        
        foreach ($allarray as $key => $value) {
            $parameters = explode('~', $value);
            if ($parameters[0] != '') {
                $tblUsersArr = array(
                    'clientID' => clientID,
                    'filter_id' => $filter_id,
                    'filt_attr_on' => $parameters[0],
                    'filt_attr_condition' => $parameters[1],
                    'filt_attr_value' => $parameters[2]
                );
                $filter->insertfilterattr($tblUsersArr);
                $coun++;
            }
        }
        $getFilters = $filter->loadFilters($user_id, '1');
        $ret        = '';
        //$ret .= '<select name="loadFilter"><option value="">SELECT</option>';
        foreach ($getFilters as $key => $value) {
            
            $ret .= '<div class="whiteBox conditiontext conditionRow filterRows" data-value="' . $value['filter_id'] . '"> 
				<span>' . $value['filter_name'] . '</span>
				<button type="button" class="removeFilter btn-mini btn pull-right"> Remove</button> 
				<button type="button" class="loadFilter btn btn-mini pull-right"> Load</button> 
			</div>';
            //$ret .=  "<option value='".$value['filter_id']."'>".$value['filter_name']."</option>";
        }
        
        echo $ret;
        
        exit;
    }
    public function commonsearchAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $request = $this->getRequest()->getPost();
        $cmnObj  = new Admin_Model_Common();
        
        
        $getTables = array();
        $andWhwer  = '';
        
        $cmntret     = '';
        $cmntPrev    = '';
        $coun        = 1;
        $chkforuser  = '';
        $chkfordbee  = '';
        $chkforgroup = '';
        
        $jointables = array();
        $finalTable = array();
        $finaljoin  = array();
        
        $userTables[] = 'tblUsers';
        $dbeeTables[] = 'tblDbees';
        $reqKeyword   = $request['keyword']; //$this->myclientdetails->customEncoding($request['keyword'],'topsearch');
        
        $userWhwer = 'AND (' . $cmnObj->createGlobalConditions_CM(ucfirst('User name'), 'Similar to', $reqKeyword, 'OR') . ')  ';
        $dbeeWhwer = 'AND (' . $cmnObj->createGlobalConditions_CM(ucfirst('Posts'), 'Similar to', $request['keyword'], 'OR') . ')  ';
        
        
        $userTab = $cmnObj->getjoinsfortable($userTables);
        $dbeeTab = $cmnObj->getjoinsfortable($dbeeTables);
        
        $adisnalTables = explode('#', $userTab);
        $adidbeeTables = explode('#', $dbeeTab);
        $returnUsers   = '';
        $returnDbees   = '';
        $userwidth     = '';
        $dbwidth       = '';
        
        if ($adisnalTables[2] != '' && $adisnalTables[1] != '' && $adisnalTables[0] != '') {
            
            $query        = "select " . $adisnalTables[2] . " from " . $adisnalTables[1] . " where  " . $adisnalTables[0] . " " . $userWhwer;
            $deshboard    = new Admin_Model_Deshboard();
            $layouts      = new Admin_Model_Layouts();
            $userwidth    = 'width:500px;';
            $liveuserData = $deshboard->SearchDbees($query);
            $returnUsers .= '<div class="userThumberSilider flexslider"><ul class="slides" id="listingResults">';
            if (count($liveuserData) > 0) {
                if ($adisnalTables[3] == 'usertemplate')
                    $returnUsers .= $layouts->userresultstemplate($liveuserData, 'common');
                else if ($adisnalTables[3] == 'dbeetemplate')
                    $returnUsers .= $layouts->dbeeresultstemplate($liveuserData);
            } else {
                $returnUsers .= '<h2 class="notfound message error">No results found!</h2>';
                
            }
            $returnUsers .= '</ul></div>';
        } else {
            $returnUsers = '<h2 class="notfound message error">No results found!</h2>';
            $dbwidth     = '';
        }
        
        if ($adidbeeTables[2] != '' && $adidbeeTables[1] != '' && $adidbeeTables[0] != '') {
            
            $query = "select " . $adidbeeTables[2] . " from " . $adidbeeTables[1] . " where  tblDbees.clientID = " . clientID . " AND  " . $adidbeeTables[0] . " " . $dbeeWhwer;
            
            $deshboard    = new Admin_Model_Deshboard();
            $layouts      = new Admin_Model_Layouts();
            $dbwidth      = 'width:600px;';
            $liveDbeeData = $deshboard->SearchDbees($query);
            $returnDbees .= '<ul class="listing scoredList colStyle" id="listingResults">';
            if (count($liveDbeeData) > 0) {
                if ($adidbeeTables[3] == 'usertemplate')
                    $returnDbees .= $layouts->userresultstemplate($liveDbeeData);
                else if ($adidbeeTables[3] == 'dbeetemplate')
                    $returnDbees .= $layouts->dbeeresultstemplate($liveDbeeData);
            } else {
                $returnDbees .= '<h2 class="notfound message error">No results found!</h2>';
                $userwidth = '100%';
            }
            $returnDbees .= '</ul>';
        } else {
            $returnDbees = '<h2 class="notfound message error">No results found!</h2>';
            
        }
        
        $total        = (count($liveuserData) + count($liveDbeeData));
        $commonresult = '<div>';
        if ($total > 0) {
            if (count($liveuserData) > 0)
                $commonresult .= '<div id="userTotalWrp" class="dashBlock_full"><h2 >Total Users: ' . count($liveuserData) . '</h2>' . $returnUsers . '</div>';
            else
                $dbwidth = '100%';
            
            if (count($liveDbeeData) > 0)
                $commonresult .= '<div id="postTotalWrp" class="colStyle"><h2 class="totalPostSrcTitle">Total Posts: ' . count($liveDbeeData) . '</h2>' . $returnDbees . '</div>';
            
            $commonresult .= '</div>';
        } else {
            $commonresult = '<h2 class="notfound message error">No results found!</h2>';
            
        }
        echo $commonresult . '~' . $total; //json_encode($returnArr) ;
        
    }
    
    public function globalsearchAction()
    {
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $request = $this->getRequest()->getPost();
        $cmnObj  = new Admin_Model_Common();
        
        /*$a = json_decode('{"1":{"ScoreName1":"love","ScoreIcon1":"131"},"2":{"ScoreName2":"like","ScoreIcon2":"23"},"3":{"ScoreName3":"dislike","ScoreIcon3":"69"},"4":{"ScoreName4":"food for thought","ScoreIcon4":"2"},"5":{"ScoreName5":"hate","ScoreIcon5":"25"}}');
        echo "<pre>";
        print_r($a);exit;*/
        
        $allarray = explode(',', $request['allinone_']);
        //print_r($allarray);
        
        $getTables = array();
        $andWhwer  = '';
        
        $cmntret     = '';
        $cmntPrev    = '';
        $coun        = 1;
        $chkforuser  = '';
        $chkfordbee  = '';
        $chkforgroup = '';
        
        $jointables = array();
        $finalTable = array();
        $finaljoin  = array();
        //echo "<pre>";
        foreach ($allarray as $key => $value) {
            
            $parameters = explode('~', $value);
            //print_r($parameters);
            if ($parameters[0] != '') {
                $getTables[$coun] = $cmnObj->joiningGlobalConditions_CM($parameters[0], $parameters[1], $parameters[2], '');
                
                
                if ($cmntret == $parameters[0] && $cmntPrev == $parameters[1]) {
                    $andWhwer .= 'OR (' . $cmnObj->createGlobalConditions_CM(ucfirst($parameters[0]), $parameters[1], $parameters[2], 'OR') . ')  ';
                    //$andWhwer .= '(' .$andWhwer .')';
                } else {
                    $andWhwer .= 'AND (' . $cmnObj->createGlobalConditions_CM(ucfirst($parameters[0]), $parameters[1], $parameters[2], 'AND') . ')  ';
                }
                
                $cmntret  = $parameters[0];
                $cmntPrev = $parameters[1];
                
                //$andWhwer .=	'('.$cmnObj->createGlobalConditions_CM($parameters[0], $parameters[1], $parameters[2]).') AND ';
                $coun++;
            }
        }
        //echo $andWhwer;exit;
        $conWhwer = explode('AND', $andWhwer);
        
        foreach ($conWhwer as $key => $value) {
            if ($value != '') {
                $where .= ' AND (' . $value . ')  ';
            }
        }
        if($parameters[0]=='Login date')
        {
            $where .= ' group by tbluserlogindetails.userid order by totLogins desc';
        }
        //$where = trim($where,' AND ');
        //echo $where;exit;
        
        //print_r($getTables);
        $availTables = array_unique($getTables);
        if (count($availTables) > 3) {
            //echo "No result found";exit; // if tables are greater then 3 then direct return no need to get that more complex
        }
        
        $tblandjoins = $cmnObj->getjoinsfortable($availTables);
        
        $adisnalTables = explode('#', $tblandjoins); // In case of comment and score
        $adisnalJoin   = explode(',', $tblandjoins[0]);
        //print_r($adisnalTables); exit;
        $returnArr     = '';
        if ($adisnalTables[2] != '' && $adisnalTables[1] != '' && $adisnalTables[0] != '') {
            
            //echo $query 	=	"select ".$adisnalTables[2]." from ".$adisnalTables[1]." where ".$adisnalTables[0] ." ".$where; exit;
            $query = "select " . $adisnalTables[2] . " from " . $adisnalTables[1] . " where " . $adisnalTables[0] . " " . $where;
            
            $deshboard = new Admin_Model_Deshboard();
            $layouts   = new Admin_Model_Layouts();
            
            $icount = 0;
            
            $liveDbeeData = $deshboard->SearchDbees($query);
            
            $returnArr .= '<ul class="listing scoredList colStyle" id="listingResults">';
            //echo "string".count($liveDbeeData);
            if (count($liveDbeeData) > 0) {
                
                if ($adisnalTables[3] == 'usertemplate')
                    $returnArr .= $layouts->userresultstemplate($liveDbeeData);
                
                else if ($adisnalTables[3] == 'dbeetemplate')
                    $returnArr .= $layouts->dbeeresultstemplate($liveDbeeData);
                
                else if ($adisnalTables[3] == 'commenttemplate')
                    $returnArr .= $layouts->commentresultstemplate($liveDbeeData);
                
                else if ($adisnalTables[3] == 'grouptemplate')
                    $returnArr .= $layouts->groupresultstemplate($liveDbeeData);
                
                else if ($adisnalTables[3] == 'scorenusertemplate')
                    $returnArr .= $layouts->scorenuserresultstemplate($liveDbeeData);
                
                else if ($adisnalTables[3] == 'scorencommenttemplate')
                    $returnArr .= $layouts->scorencommentresultstemplate($liveDbeeData);
                
                else if ($adisnalTables[3] == 'scorendbeetemplate')
                    $returnArr .= $layouts->scorenuserresultstemplate($liveDbeeData);
                
                else if ($adisnalTables[3] == 'multiscoretemplate')
                    $returnArr .= $layouts->scorenuserresultstemplate($liveDbeeData);
                
                else if ($adisnalTables[3] == 'mentionnusertemplate')
                    $returnArr .= $layouts->mentionnuserresultstemplate($liveDbeeData);
                
            } else {
                $returnArr .= '<h2 class="notfound message error">No search results found.</h2>';
            }
            
            $returnArr .= '</ul>';
            
            
        } else {
            $returnArr = '<h2 class="notfound message error">Search does not formed well, Please change your search criteria and try again </h2>';
        }
        echo $returnArr . '~' . count($liveDbeeData); //json_encode($returnArr) ;
        //print_r($liveDbeeData);
        
    }
    
    //**************************** ACTION END FOR ADVANCE SEARCH AUTOSUGGEST *******************************
    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    
    public function callingajaxcontainersAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $request   = $this->getRequest()->getPost();
        $cmnObj    = new Admin_Model_Common();
        $reporting = new Admin_Model_reporting();
        $deshboard = new Admin_Model_Deshboard();
        
        $datefrom  = empty($request['datefrom']) ? '' : $request['datefrom'];
        $dateto    = empty($request['dateto']) ? '' : $request['dateto'];
        $ranglimit = empty($request['ranglimit']) ? '5' : $request['ranglimit'];

        if ($request['calling'] == 'SematriaCategories') {
            $semBox = '';

            $semQue = $this->myclientdetails->passSQLquery("select count(*) as total, entity_type,entity_title from tblsematira where clientID=".clientID." AND entity_type!='Regex' AND entity_type!='Quote'  group by entity_title");
            if (count($semQue) > 0){
                $semBox = '<div id="word-cloud" class="wordcloud"> ';

                $style="";

                $fontColor  =   array();
                $topLen     =   array();
                $counter =0;
                foreach ($semQue as $key => $value) {
                //for($i=0;$i<80;$i++)  {
               
                    $style="";
                    $showval = $value['entity_title'];
                    //$showval = $this->generateRandomString(6);
                   
                    if($value['entity_type']=="Regex") $showval ="Web-URL";
                    
                    $semBox .=  "<a href='".BASE_URL.'/admin/dashboard/userstalkingon/entity/'. $value['entity_title']."'  data-weight=".($value['total'])." >".$showval."</a> ";

                    $counter++;


                }
                $semBox .= '</div>'; 
                echo $semBox;
            }
            else
                echo "no";
            
        }
        
        if ($request['calling'] == 'topdebating') {
            $usertotProvidersdata = array();
            $userNameArr          = array();
            $userUnameArr         = array();
            $userPictureArr       = array();
            
            $liveDbeeData = $reporting->gettopDbeeusers($ranglimit, '', $datefrom, $dateto);
            
            foreach ($liveDbeeData as $key => $value) {
                $usertotProvidersdata[] = array(
                    'y' => (int) $value['total']
                );
                $userNameArr[]          = array(
                    $this->myclientdetails->customDecoding($value['username'])
                );
                $userUnameArr[]         = array(
                    $this->myclientdetails->customDecoding($value['uname'])
                );
                $userImage              = $cmnObj->checkImgExist($value['image'], 'userpics', 'default-avatar.jpg');
                $userPictureArr[]       = array(
                    $userImage
                );
            }
            if (count($liveDbeeData) > 0)
                echo json_encode($usertotProvidersdata) . '~' . json_encode($userNameArr) . '~' . json_encode($userUnameArr) . '~' . json_encode($userPictureArr);
            else
                echo "no";
            
        } else if ($request['calling'] == 'postvisitinguser') {
            $visitingTotal = $deshboard->getmyVisitingPosts($request['uid']);
            
            $totalcommentdata = array();
            $dbdescription    = array();
            $userUnameArr     = array();
            $userPictureArr   = array();
            $pagignation      = '';
            
            //	echo "<pre>";print_r($liveDbeeData->toarray());exit;
            if (count($visitingTotal) > 0) {
                $visits = '';
                $visits .= '<ul>';
                foreach ($visitingTotal as $key => $value) {
                    $desc = ($value['type'] == 5) ? $this->myclientdetails->dbSubstring($value['PollText'], '100', '...') : $this->myclientdetails->dbSubstring($value['Text'], '100', '...');

                    $desc = ($value['type'] == 7) ? '<span style="color:red">SURVEY - </span>'.$this->myclientdetails->dbSubstring($value['surveyTitle'], '100', '...') : $desc;
                    $visits .= '<li><div class="description" style="padding:10px">
					<a href="' . BASE_URL . '/dbee/' . $value['dburl'] . '" target="_blank">' . $desc . '</a></div><span class="dateTimeNoti">Visit on : ' . date("d M, Y", strtotime($value['stats_date'])) . '</span>
					</li>';
                }
                echo $visits .= '</ul>';
            } else
                echo '<div class="dashBlockEmpty">user not visited to any post</div>';
            
        } else if ($request['calling'] == 'postvisiting') {
            $visitingTotal     = $deshboard->getVisitersPosts('all', '', $datefrom, $dateto);
            $visitingChartData = $deshboard->getVisitersPosts($request['ranglimit'], $request['offset'], $datefrom, $dateto); // Visiters ON PLATEFORM
            
            $totalcommentdata = array();
            $dbdescription    = array();
            $userUnameArr     = array();
            $userPictureArr   = array();
            $dburlArr         = array();
            $pagignation      = '';
            
            //	echo "<pre>";print_r($liveDbeeData->toarray());exit;
            foreach ($visitingChartData as $key => $value) {
                if ($value['type'] != 5) {
                    $totalcommentdata[] = array(
                        'y' => (int) $value['totvisiters']
                    );
                    if ($value['text'] != '')
                        $dbdescription[] = htmlentities(substr($value['text'], 0, 40));
                    else
                        $dbdescription[] = htmlentities(substr($value['dburl'], 0, 40));
                    if ($value['type'] == 5) {
                        $dbdescription[] = htmlentities(substr($value['PollText'], 0, '40'));
                    }
                    $dburlArr[]       = array(
                        $value['DbeeID']
                    );
                    $dbuserUnameArr[] = array(
                        $this->myclientdetails->customDecoding($value['username'])
                    );
                    $userImage        = $cmnObj->checkImgExist($value['image'], 'userpics', 'default-avatar.jpg');
                    $dbuserImageArr[] = array(
                        $userImage
                    );
                }
            }

            if (count($visitingTotal) > $request['offset']) {
                $nLimit = 0;
                $pLimit = 0;
                
                $tRange = $request['ranglimit'] + $request['offset'];
                if ($tRange < count($visitingTotal))
                    $nLimit = $request['ranglimit'] + $request['offset'];
                
                $nextClick = "callglobalajax('visitingcontainer','index','callingajaxcontainers', 'postvisiting','topdebateusers','" . $datefrom . "','" . $dateto . "'," . $nLimit . "," . $request['offset'] . ",this)";
                
                
                
                $pLimit    = ($request['ranglimit'] - $request['offset']);
                $prevClick = "callglobalajax('visitingcontainer','index','callingajaxcontainers', 'postvisiting','topdebateusers','" . $datefrom . "','" . $dateto . "'," . $pLimit . "," . $request['offset'] . ",this)";
                
                if ($nLimit == 0)
                    $dNone = "display:none";
                
                if ($pLimit < 0)
                    $pNone = "display:none";
                
                
                $pagignation = '<div id="visitingcontainerPaging" class="visitingcontainerPaging"><span class="next btn btn-yellow pull-right" style="' . $dNone . '" onclick="' . $nextClick . '">Next <i class="fa fa-chevron-circle-right fa-lg"></i></span>	<span class="Prev btn btn-yellow pull-left" style="' . $pNone . '" onclick="' . $prevClick . '"><i class="fa fa-chevron-circle-left fa-lg"></i> Prev</span></div>';
            }
            
            if (count($visitingChartData) > 0)
                echo json_encode($totalcommentdata) . '~' . json_encode($dbuserUnameArr) . '~' . json_encode($dburlArr) . '~' . json_encode($dbuserImageArr) . '~' . json_encode($dbdescription) . '~' . json_encode($pagignation);
            else
                echo "no";
            
        } else if ($request['calling'] == 'usersignupfromplateform') {
            
            $signedChartData = $deshboard->signedupuserData('', $datefrom, $dateto); // TOTAL USERS FORM SOURCES
            
            if (count($signedChartData) > 0)
                echo $signedChartDatajson = json_encode($signedChartData);
            else
                echo "no";
        } else if ($request['calling'] == 'mosetscore') {
            /********* SCORE  PROVDERS ACTION ************/
            $scoretotProvidersdata = array();
            $scuserNameArr         = array();
            $scuserUnameArr        = array();
            $scuserPictureArr      = array();
            
            $livescoreData = $deshboard->gettopScoreusers($ranglimit, 'owner', 1, $datefrom, $dateto);
            
            foreach ($livescoreData as $key => $value) {
                $scoretotProvidersdata[] = array(
                    'y' => (int) $value['totscore']
                );
                $scuserNameArr[]         = array(
                    $this->myclientdetails->customDecoding($value['username'])
                );
                $scuserUnameArr[]        = array(
                    $this->myclientdetails->customDecoding($value['uname'])
                );
                $userImage               = $cmnObj->checkImgExist($value['image'], 'userpics', 'default-avatar.jpg');
                $scuserPictureArr[]      = array(
                    $userImage
                );
            }
            
            if (count($livescoreData) > 0)
                echo $scoretotproviderscategory = json_encode($scoretotProvidersdata) . '~' . json_encode($scuserNameArr) . '~' . json_encode($scuserUnameArr) . '~' . json_encode($scuserPictureArr);
            else
                echo "no";
            
            
            
            /********* SCORE DEBATE PROVDERS ACTION ************/
        } else if ($request['calling'] == 'populardebate') {
            $popularChartData = $deshboard->getPopularDbee($ranglimit, '', '', $datefrom, $dateto); // POPULAR DETATES ON PLATEFORM
            $totalcommentdata = array();
            $dbdescription    = array();
            $userUnameArr     = array();
            $userPictureArr   = array();
            
            //	echo "<pre>";print_r($liveDbeeData->toarray());exit;
            foreach ($popularChartData as $key => $value) {
                $totalcommentdata[] = array(
                    'y' => (int) $value['totcomment']
                );
                
                if ($value['type'] == 1) {
                    $dbdescription[] = htmlentities(substr($value['text'], 0, 40));
                }
                if ($value['type'] == 2) {
                    $dbUserLinkDesc  = !empty($value['UserLinkDesc']) ? $value['UserLinkDesc'] : $value['LinkTitle'];
                    $dbdescription[] = htmlentities(substr($dbUserLinkDesc, 0, 40));
                }
                if ($value['type'] == 3) {
                    $dbdescription[] = htmlentities(substr($value['PicDesc'], 0, '40'));
                }
                if ($value['type'] == 4) {
                    $dbVidDesc       = $value['VidDesc'];
                    $dbdescription[] = htmlentities(substr($value['VidDesc'], 0, '40'));
                }
                if ($value['type'] == 5) {
                    $dbdescription[] = htmlentities(substr($value['PollText'], 0, '40'));
                }
                
                $dburlArr[]       = array(
                    $value['dburl']
                );
                $dbuserUnameArr[] = array(
                    $this->myclientdetails->customDecoding($value['username'])
                );
                $userImage        = $cmnObj->checkImgExist($value['image'], 'userpics', 'default-avatar.jpg');
                $dbuserImageArr[] = array(
                    $userImage
                );
            }
            
            if (count($popularChartData) > 0)
                echo json_encode($totalcommentdata) . '~' . json_encode($dbuserUnameArr) . '~' . json_encode($dburlArr) . '~' . json_encode($dbuserImageArr) . '~' . json_encode($dbdescription);
            else
                echo "no";
            
        }
        exit;
    }
    
    
    /**  * Index Controller    */
    public function indexAction()
    {     
        $request   = $this->getRequest()->getParams();
        $deshboard = new Admin_Model_Deshboard();
        $common    = new Admin_Model_Common();
        
        $this->view->charttype = $request['type'];
        
        $result = $this->myclientdetails->getRowMasterfromtable('tblConfiguration', array(
            'allow_admin_post_live'
        ));
        $this->view->allow_admin_post_live = $result['allow_admin_post_live'];
        
        $request = $this->getRequest()->getParams();
        if (isset($request['searchfield']) && $request['searchfield'] != '')
            $seachfield = $request['searchfield'];
        else
            $seachfield = '';
        
        
        $facebookconnect = Zend_Json::decode($this->session_data['facebook_connect_data']);
        $twitterconnect  = Zend_Json::decode($this->session_data['twitter_connect_data']);
        $linkedinconnect = Zend_Json::decode($this->session_data['linkedin_connect_data']);
        if (empty($facebookconnect) && empty($twitterconnect) && empty($linkedinconnect))
            $this->view->social_connect = false;
        else
            $this->view->social_connect = true;
        
        $liveDbeeData             = $deshboard->getLiveDbee(4);
        $this->view->liveDbeeData = $liveDbeeData;
        
        $liveGroupData                    = $deshboard->getLiveGroup(4);
        $this->view->liveGroupData        = $liveGroupData;
        $topGroupActivityList             = $deshboard->getTopactivegroup(0, 4);
        
        $this->view->topGroupActivityList = $topGroupActivityList;
        $latestCommentData                = $deshboard->getLatestComment(4);
        $this->view->latestCommentData    = $latestCommentData;
        $this->view->Groupdropdown        = $common->Groupdropdown();
        
        $liveScoreData             = $deshboard->getLiveScore(7);
        $this->view->getLiveGrouptotal = $deshboard->getLiveGroup('all');
        $this->view->liveScoreData = $liveScoreData;
        
        $this->view->category = $deshboard->getallcategory();

        // after logged out activities 

       
        
    }
    
    public function callstockchartsAction()
    {
        $request    = $this->getRequest()->getParams();
        $deshboard  = new Admin_Model_Deshboard();
        $layoutsobj = new Admin_Model_Layouts();
        $scoreset   = $layoutsobj->scoringFromDb(); // taking value from database
        if ($request['filename'] == 'users') {
            
            $retvar           = '';
            $registeredUsers2 = "SELECT DATE(`RegistrationDate`) AS ForDate,COUNT(*) AS NumPosts FROM   tblUsers where clientID = " . clientID . " GROUP BY DATE(`RegistrationDate`) ORDER BY ForDate ASC";
            
            $userRegis1 = $deshboard->SearchDbees($registeredUsers2);
            //print_r($userRegis1);exit; 
            foreach ($userRegis1 as $item) {
                $retvar .= '';
                $tmp = preg_split('/(-| |\:)/', $item['ForDate']);
                $retvar .= implode(', ', $tmp);
                $retvar .= ', ' . $item['NumPosts'] . '~';
            }
            
            echo json_encode($retvar);
            
        }
        if ($request['filename'] == 'activities') {
            $registeredUsers2 = "SELECT DATE(`act_date`) AS ForDate,COUNT(*) AS NumPosts FROM   tblactivity where clientID = " . clientID . " GROUP BY DATE(`act_date`) ORDER BY ForDate ASC";
            $userRegis1       = $deshboard->SearchDbees($registeredUsers2);
            $retvar           = '';
            
            foreach ($userRegis1 as $item) {
                $retvar .= '';
                $tmp = preg_split('/(-| |\:)/', $item['ForDate']);
                $retvar .= implode(', ', $tmp);
                $retvar .= ', ' . $item['NumPosts'] . '~';
                
            }
            echo json_encode($retvar);
        }
        
        if ($request['filename'] == 'comments') {
            $registeredUsers2 = "SELECT DATE(`CommentDate`) AS ForDate,COUNT(*) AS NumPosts FROM   tblDbeeComments where `DbeeID` = '" . $request['dbId'] . "' GROUP BY DATE(`CommentDate`)  ORDER BY ForDate ASC";
            $userRegis1       = $deshboard->SearchDbees($registeredUsers2);
            $retvar           = '';
            
            foreach ($userRegis1 as $item) {
                $retvar .= '';
                $tmp = preg_split('/(-| |\:)/', $item['ForDate']);
                $retvar .= implode(', ', $tmp);
                $retvar .= ', ' . $item['NumPosts'] . '~';
                
            }
            echo json_encode($retvar);
        }
        
        if ($request['filename'] == $scoreset['Love']) {
            $registeredUsers2 = "SELECT DATE(`ScoreDate`) AS ForDate,COUNT(*) AS NumPosts FROM tblScoring where `MainDB` = '" . $request['dbId'] . "' AND Score=1 GROUP BY DATE(`ScoreDate`)  ORDER BY ForDate ASC";
            $userRegis1       = $deshboard->SearchDbees($registeredUsers2);
            $retvar           = '';
            
            foreach ($userRegis1 as $item) {
                $retvar .= '';
                $tmp = preg_split('/(-| |\:)/', $item['ForDate']);
                $retvar .= implode(', ', $tmp);
                $retvar .= ', ' . $item['NumPosts'] . '~';
                
            }
            echo json_encode($retvar);
        }
        if ($request['filename'] == $scoreset['Like']) {
            $registeredUsers2 = "SELECT DATE(`ScoreDate`) AS ForDate,COUNT(*) AS NumPosts FROM tblScoring where `MainDB` = '" . $request['dbId'] . "' AND Score=2 GROUP BY DATE(`ScoreDate`)  ORDER BY ForDate ASC";
            $userRegis1       = $deshboard->SearchDbees($registeredUsers2);
            $retvar           = '';
            
            foreach ($userRegis1 as $item) {
                $retvar .= '';
                $tmp = preg_split('/(-| |\:)/', $item['ForDate']);
                $retvar .= implode(', ', $tmp);
                $retvar .= ', ' . $item['NumPosts'] . '~';
                
            }
            echo json_encode($retvar);
        }
        if ($request['filename'] == $scoreset['F O T']) {
            $registeredUsers2 = "SELECT DATE(`ScoreDate`) AS ForDate,COUNT(*) AS NumPosts FROM tblScoring where `MainDB` = '" . $request['dbId'] . "' AND Score=3 GROUP BY DATE(`ScoreDate`)  ORDER BY ForDate ASC";
            $userRegis1       = $deshboard->SearchDbees($registeredUsers2);
            $retvar           = '';
            
            foreach ($userRegis1 as $item) {
                $retvar .= '';
                $tmp = preg_split('/(-| |\:)/', $item['ForDate']);
                $retvar .= implode(', ', $tmp);
                $retvar .= ', ' . $item['NumPosts'] . '~';
                
            }
            echo json_encode($retvar);
        }
        if ($request['filename'] == $scoreset['Dis Like']) {
            $registeredUsers2 = "SELECT DATE(`ScoreDate`) AS ForDate,COUNT(*) AS NumPosts FROM tblScoring where `MainDB` = '" . $request['dbId'] . "' AND Score=4 GROUP BY DATE(`ScoreDate`)  ORDER BY ForDate ASC";
            $userRegis1       = $deshboard->SearchDbees($registeredUsers2);
            $retvar           = '';
            
            foreach ($userRegis1 as $item) {
                $retvar .= '';
                $tmp = preg_split('/(-| |\:)/', $item['ForDate']);
                $retvar .= implode(', ', $tmp);
                $retvar .= ', ' . $item['NumPosts'] . '~';
                
            }
            echo json_encode($retvar);
        }
        if ($request['filename'] == $scoreset['Hate']) {
            $registeredUsers2 = "SELECT DATE(`ScoreDate`) AS ForDate,COUNT(*) AS NumPosts FROM tblScoring where `MainDB` = '" . $request['dbId'] . "' AND Score=5 GROUP BY DATE(`ScoreDate`)  ORDER BY ForDate ASC";
            $userRegis1       = $deshboard->SearchDbees($registeredUsers2);
            $retvar           = '';
            
            foreach ($userRegis1 as $item) {
                $retvar .= '';
                $tmp = preg_split('/(-| |\:)/', $item['ForDate']);
                $retvar .= implode(', ', $tmp);
                $retvar .= ', ' . $item['NumPosts'] . '~';
                
            }
            echo json_encode($retvar);
        }
        
        exit;
    }
    public function chkGraphData($month = '', $gVal = '')
    {
        //echo $gVal .' '.$month; 
        $gVal = ($gVal != '' ? $gVal : 0);
        switch ($month) {
            case '0':
                return $retMon = $gVal;
                break;
            case '1':
                return $retMon = $gVal;
                break;
            case '2':
                return $retMon = $gVal;
                break;
            case '3':
                return $retMon = $gVal;
                break;
            case '4':
                return $retMon = $gVal;
                break;
            case '5':
                return $retMon = $gVal;
                break;
            case '6':
                return $retMon = $gVal;
                break;
            case '7':
                return $retMon = $gVal;
                break;
            case '8':
                return $retMon = $gVal;
                break;
            case '9':
                return $retMon = $gVal;
                break;
            case '10':
                return $retMon = $gVal;
                break;
            case '11':
                return $retMon = $gVal;
                break;
            
            default:
                # code...
                break;
        }
    }
    
    public function chkMonts($month = '', $year, $dataAvl = '')
    {
        
        switch ($month) {
            case '1':
                return $retMon = 'Jan-' . $year;
                break;
            case '2':
                return $retMon = 'Feb-' . $year;
                break;
            case '3':
                return $retMon = 'Mar-' . $year;
                break;
            case '4':
                return $retMon = 'Apr-' . $year;
                break;
            case '5':
                return $retMon = 'May-' . $year;
                break;
            case '6':
                return $retMon = 'Jun-' . $year;
                break;
            case '7':
                return $retMon = 'Jul-' . $year;
                break;
            case '8':
                return $retMon = 'Aug-' . $year;
                break;
            case '9':
                return $retMon = 'Sep-' . $year;
                break;
            case '10':
                return $retMon = 'Oct-' . $year;
                break;
            case '11':
                return $retMon = 'Nov-' . $year;
                break;
            case '12':
                return $retMon = 'Dec-' . $year;
                break;
            
            default:
                # code...
                break;
        }
    }
    
    
    public function activityGraphAction()
    {
        $request   = $this->getRequest()->getParams();
        $deshboard = new Admin_Model_Deshboard();
    }
    
    public function savedchartsAction()
    {
        $request   = $this->getRequest()->getParams();
        $deshboard = new Admin_Model_Deshboard();
        
        //echo "<pre>"; print_r($request);
        
        $datefrom = date('Y-m-d H:i:s', strtotime($request['datefrom']));

        if((date('Y-m', strtotime($request['datefrom']))==date('Y-m')) && $request['type']=='postvisiting'){
            $datefrom = date('Y-m-d H:i:s');
        }
        
        if (!empty($request['groupid'])) {

            

            $data = array(
                'clientID' => clientID,
                'groupid' => $request['groupid'],
                'chartname' => $request['chartename'],
                'charttype' => $request['type'],
                'limit' => $request['ranglimit'],
                'datefrom' => $datefrom,
                'dateto' => date('Y-m-d H:i:s', strtotime($request['dateto'])),
                'curdate' => date('Y-m-d H:i:s')
            );
            
            $savechart = $this->myclientdetails->insertdata_global('adminsavedcharts', $data);
            if ($savechart)
                echo 1;
            else
                echo 0;
        } else {
            $chkgrpname = $this->myclientdetails->getfieldsfromtable(array(
                'groupname'
            ), 'adminchartgroup', 'groupname', $request['groupname']);
            if (count($chkgrpname) < 1) {
                
                $data = array(
                    'clientID' => clientID,
                    'groupname' => $request['groupname'],
                    'createddate' => date('Y-m-d H:i:s')
                );
                
                $groupid = $this->myclientdetails->insertdata_global('adminchartgroup', $data);
                
                
                $data = array(
                    'clientID' => clientID,
                    'groupid' => $groupid,
                    'chartname' => $request['chartename'],
                    'charttype' => $request['type'],
                    'limit' => $request['ranglimit'],
                    'datefrom' => $datefrom,
                    'dateto' => date('Y-m-d H:i:s', strtotime($request['dateto'])),
                    'curdate' => date('Y-m-d H:i:s')
                );
                
                $savechart = $this->myclientdetails->insertdata_global('adminsavedcharts', $data);
                if ($savechart) {
                    $grpname = $this->myclientdetails->getfieldsfromtable(array(
                        'id',
                        'groupname'
                    ), 'adminchartgroup', '1', '1');
                    foreach ($grpname as $key => $value) {
                        $filterSaveChartHtml .= '<option value="' . $value['id'] . '">' . $value['groupname'] . '</option>';
                    }
                    echo '1' . '~' . $filterSaveChartHtml;
                } else
                    echo 0;
                
            } else {
                echo "403~wrong entry";
                exit;
            }
        }
        
        
        exit();
    }
    
    public function calllinechartsAction()
    {
        $request   = $this->getRequest()->getParams();
        $deshboard = new Admin_Model_Deshboard();
        
        if (!empty($request['type'])) {
            // $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $this->view->charttype = $request['type'];
            
            $datefrom = $request['dateto'];
            $dateto   = $request['datefrom'];
            
            
            
            switch ($request['type']) {
                case 'monthly':
                    
                    $registeredUsers = "SELECT count(`UserID`) as Total, DATE_FORMAT(RegistrationDate,'%b-%y') as inMonth, DATE_FORMAT(RegistrationDate,'%c') as numMonth FROM tblMonths as M  LEFT OUTER JOIN tblUsers as DT ON  M.Nr = Month(DT.`RegistrationDate`) AND DT.`RegistrationDate`>= DATE_SUB( CURDATE( ) , INTERVAL 1 YEAR ) GROUP BY M.Nr, M.Name ORDER BY M.Nr ASC";
                    
                    //echo	$registeredUsers	=	"SELECT DATE(RegistrationDate) AS sales_date,count(`UserID`) as Total,COUNT(*) AS transactions							  FROM tblUsers GROUP BY DATE(RegistrationDate) ORDER BY DATE(RegistrationDate)";
                    
                    
                    // exit;
                    // For Active user
                    
                    $activeUsers = "SELECT  COUNT(DT.`act_id`) as Total ,DATE_FORMAT(act_date,'%b-%y') as inMonth ,DATE_FORMAT(act_date,'%y') as inYear , DATE_FORMAT(act_date,'%c') as numMonth FROM tblMonths as M  LEFT OUTER JOIN tblactivity as DT ON  M.Nr = Month(DT.`act_date`) AND  `act_date`>= DATE_SUB( CURDATE( ) , INTERVAL 1 YEAR ) GROUP BY M.Nr, M.Name  ORDER BY M.Nr ASC";
                    
                    $userRegis = $deshboard->SearchDbees($registeredUsers);
                    
                    $userActive = $deshboard->SearchDbees($activeUsers);
                    
                    $getMonth = array();
                    $getUser  = array();
                    
                    $getActvM    = array();
                    $chkGraphReg = array();
                    
                    $chknewa       = array();
                    $existRecinMon = array();
                    $chkGraphAct   = array();
                    
                    
                    $lastA = date(n); // A numeric representation of a month, without leading zeros (1 to 12)
                    $lastY = date(y); // A two digit representation of a year
                    
                    
                    $c = $a = 1;
                    $d = 12;
                    
                    for ($i = 0; $i < 12; $i++) {
                        $stPoint = $lastA - $i;
                        
                        if ($stPoint == 0) {
                            $c = 0;
                            
                        }
                        //echo ','.($stPoint-1);
                        if ($c != 0) {
                            
                            $chknewa[] = $this->chkMonts($stPoint, $lastY);
                            
                            if (!empty($userActive[$stPoint]['numMonth'])) {
                                $chkGraphAct[] = (int) $this->chkGraphData($stPoint - 1, $userActive[$stPoint - 1]['Total']);
                                $chkGraphReg[] = (int) $this->chkGraphData($stPoint - 1, $userRegis[$stPoint - 1]['Total']);
                            } else {
                                $chkGraphAct[] = (int) $this->chkGraphData($stPoint - 1, $userActive[$stPoint - 1]['Total']);
                                $chkGraphReg[] = (int) $this->chkGraphData($stPoint - 1, $userRegis[$stPoint - 1]['Total']);
                            }
                        }
                        
                        if ($c == 0) {
                            $chknewa[] = $this->chkMonts($d, ($lastY - 1));
                            
                            if (!empty($userActive[$stPoint]['numMonth'])) {
                                $chkGraphAct[] = (int) $this->chkGraphData($d - 1, $userActive[$d]['Total']);
                                $chkGraphReg[] = (int) $this->chkGraphData($d - 1, $userRegis[$d]['Total']);
                            } else {
                                $chkGraphAct[] = (int) $this->chkGraphData($d - 1, $userActive[$d]['Total']);
                                $chkGraphReg[] = (int) $this->chkGraphData($d - 1, $userRegis[$d]['Total']);
                            }
                            $d--;
                        }
                    }
                    /*echo'<pre>';print_r(array_reverse($chknewa));
                    echo'<pre>';print_r(array_reverse($chkGraphReg));//die;
                    echo'<pre>';print_r(array_reverse($chkGraphAct));die;*/
                    
                    $rMonthJson = json_encode(array_reverse($chknewa));
                    $rDataJson  = json_encode(array_reverse($chkGraphReg));
                    $aDataJson  = json_encode(array_reverse($chkGraphAct));
                    
                    $totdata = (int) $imparr[1] + (int) $imparr[2] + (int) $imparr[3] + (int) $imparr[4];
                    echo $rMonthJson . '~' . $rDataJson . '~' . $aDataJson . '~monthly';
                    break;
                
                case 'dbeetypes':
                    
                    $DbeeChartData = $deshboard->DbeeChartData($datefrom, $dateto);
                    
                    $imparr    = explode('=', $DbeeChartData);
                    $cdata     = array(
                        array(
                            'name' => 'Text',
                            'y' => (int) $imparr[0],
                            'sliced' => false,
                            'selected' => false
                        ),
                        array(
                            'name' => 'Link',
                            'y' => (int) $imparr[1],
                            'sliced' => false,
                            'selected' => false
                        ),
                        array(
                            'name' => 'Pix',
                            'y' => (int) $imparr[2],
                            'sliced' => false,
                            'selected' => false
                        ),
                        array(
                            'name' => 'Media',
                            'y' => (int) $imparr[3],
                            'sliced' => false,
                            'selected' => false
                        ),
                        array(
                            'name' => 'Polls',
                            'y' => (int) $imparr[4],
                            'sliced' => false,
                            'selected' => false
                        )
                    );
                    $totdata   = (int) $imparr[0] + (int) $imparr[1] + (int) $imparr[2] + (int) $imparr[3] + (int) $imparr[4];
                    $cdatajson = json_encode($cdata);
                    echo $cdatajson . '~dbeetypes~' . $totdata;
                    break;
                
                case 'score':
                    
                    $ScoringChartData = $deshboard->ScoringChartData($datefrom, $dateto);
                    
                    $imparr    = explode('=', $ScoringChartData);
                    $cdata     = array(
                        array(
                            'name' => 'Like',
                            'y' => (int) $imparr[0],
                            'sliced' => false,
                            'selected' => false
                        ),
                        array(
                            'name' => 'Love',
                            'y' => (int) $imparr[1],
                            'sliced' => false,
                            'selected' => false
                        ),
                        array(
                            'name' => 'Food For Thought',
                            'y' => (int) $imparr[2],
                            'sliced' => false,
                            'selected' => false
                        ),
                        array(
                            'name' => 'Dislike',
                            'y' => (int) $imparr[3],
                            'sliced' => false,
                            'selected' => false
                        ),
                        array(
                            'name' => 'Hate',
                            'y' => (int) $imparr[4],
                            'sliced' => false,
                            'selected' => false,
                            'color' => '#23uida'
                        )
                    );
                    $totdata   = (int) $imparr[0] + (int) $imparr[1] + (int) $imparr[2] + (int) $imparr[3] + (int) $imparr[4];
                    $cdatajson = json_encode($cdata);
                    echo $cdatajson . '~score~' . $totdata;
                    break;
                
                case 'group':
                    $GroupChartData = $deshboard->GroupChartData($datefrom, $dateto);
                    
                    $imparr    = explode('=', $GroupChartData);
                    $cdata     = array(
                        array(
                            'name' => 'Open',
                            'y' => (int) $imparr[0],
                            'sliced' => false,
                            'selected' => false
                        ),
                        array(
                            'name' => 'Private',
                            'y' => (int) $imparr[1],
                            'sliced' => false,
                            'selected' => false
                        ),
                        array(
                            'name' => 'Request',
                            'y' => (int) $imparr[2],
                            'sliced' => false,
                            'selected' => false
                        )
                    );
                    $totdata   = (int) $imparr[0] + (int) $imparr[1] + (int) $imparr[2] + (int) $imparr[3] + (int) $imparr[4];
                    $cdatajson = json_encode($cdata);
                    echo $cdatajson . '~group~' . $totdata;
                    
                    break;
                
                default:
                    echo 'monthly';
            }
            exit;
        }
    }
    public function callchartsAction()
    {
        require_once 'includes/globalfileadmin.php';
        $request   = $this->getRequest()->getParams();
        $deshboard = new Admin_Model_Deshboard();
        
        if (!empty($request['type'])) {
            // $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $this->view->charttype = $request['type'];
            
            $datefrom = $request['dateto'];
            $dateto   = $request['datefrom'];
            
            
            
            switch ($request['type']) {
                case 'alladmin':
                    $TotalChartData = $result = $this->deshboard->AllactivityAdmin($this->adminUserID, $dateto ,  $datefrom);
                    
                    $imparr = explode('=', $TotalChartData);
                    $cdata  = array(
                        array(
                            'name' => '‘Admin created’ user activity',
                            'y' => (int) $imparr[1],
                            'sliced' => false,
                            'selected' => false
                        ),
                        array(
                            'name' => '‘User created’ user activity',
                            'y' => (int) $imparr[2],
                            'sliced' => false,
                            'selected' => false
                        )
                    );
                    
                    $cdatajson = json_encode($cdata);
                    
                    $totdata = (int) $imparr[1] + (int) $imparr[2] + (int) $imparr[3] + (int) $imparr[4];
                    echo $cdatajson . '~alladmin~' . $totdata;
                    break;
                case 'adminbreck':
                    $TotalChartData = $result = $this->deshboard->breakActivityAdmin($this->adminUserID, $datefrom, $dateto);
                    $totdata        = '';
                    foreach ($TotalChartData as $key => $value) {
                        $breArr[] = array(
                            'name' => $adminChartMsg[$value['act_message']],
                            'y' => (int) $value['actTot'],
                            'sliced' => false,
                            'selected' => false
                        );
                        $totdata += (int) $value['actTot'];
                    }
                    $bdata = ($breArr);
                    
                    
                    $cdatajson = json_encode($bdata);
                    
                    
                    echo $cdatajson . '~adminbreck~' . $totdata;
                    break;
                case 'influence':
                    $TotalChartData = $result = $this->deshboard->breakInfluence($this->adminUserID, $datefrom, $dateto);
                    $totdata        = '';
                    foreach ($TotalChartData as $key => $value) {
                        if ($value['ArticleType'] == 0)
                            $slice = 'Post influence';
                        else
                            $slice = 'Comment influence';
                        $breArr[] = array(
                            'name' => $slice,
                            'y' => (int) $value['actTot'],
                            'sliced' => false,
                            'selected' => false
                        );
                        $totdata += (int) $value['actTot'];
                    }
                    $bdata = ($breArr);
                    
                    
                    $cdatajson = json_encode($bdata);
                    
                    
                    echo $cdatajson . '~influence~' . $totdata;
                    break;
                case 'platformbreck':
                    $TotalChartData = $result = $this->deshboard->breakActivityPlatform($this->adminUserID, $datefrom, $dateto);
                    $totdata        = '';
                    foreach ($TotalChartData as $key => $value) {
                        $breArr[] = array(
                            'name' => $adminChartMsg[$value['act_message']],
                            'y' => (int) $value['actTot'],
                            'sliced' => false,
                            'selected' => false
                        );
                        $totdata += (int) $value['actTot'];
                    }
                    
                    $cdatajson = json_encode($breArr);
                    
                    echo $cdatajson . '~platformbreck~' . $totdata;
                    break;
                case 'dbee':
                    $TotalChartData = $deshboard->TotalChartData('', $datefrom, $dateto);
                    
                    $imparr = explode('=', $TotalChartData);
                    $cdata  = array(
                        array(
                            'name' => 'Posts',
                            'y' => (int) $imparr[1],
                            'sliced' => false,
                            'selected' => false
                        ),
                        array(
                            'name' => 'Comments',
                            'y' => (int) $imparr[2],
                            'sliced' => false,
                            'selected' => false
                        ),
                        array(
                            'name' => 'Scores',
                            'y' => (int) $imparr[3],
                            'sliced' => false,
                            'selected' => false
                        ),
                        array(
                            'name' => 'Groups',
                            'y' => (int) $imparr[4],
                            'sliced' => false,
                            'selected' => false
                        )
                    );
                    
                    $cdatajson = json_encode($cdata);
                    
                    $totdata = (int) $imparr[1] + (int) $imparr[2] + (int) $imparr[3] + (int) $imparr[4];
                    echo $cdatajson . '~dbee~' . $totdata;
                    break;
                
                case 'dbeetypes':
                    
                    $DbeeChartData = $deshboard->PostBreakdown($datefrom, $dateto, $this->adminUserID);
                    
                    $imparr    = explode('=', $DbeeChartData);
                    $cdata     = array(
                        array(
                            'name' => 'Text',
                            'y' => (int) $imparr[0],
                            'sliced' => false,
                            'selected' => false
                        ),
                        array(
                            'name' => 'Link',
                            'y' => (int) $imparr[1],
                            'sliced' => false,
                            'selected' => false
                        ),
                        array(
                            'name' => 'Picture',
                            'y' => (int) $imparr[2],
                            'sliced' => false,
                            'selected' => false
                        ),
                        array(
                            'name' => 'Video',
                            'y' => (int) $imparr[3],
                            'sliced' => false,
                            'selected' => false
                        ),
                        array(
                            'name' => 'Polls',
                            'y' => (int) $imparr[4],
                            'sliced' => false,
                            'selected' => false
                        )
                    );
                    $totdata   = (int) $imparr[0] + (int) $imparr[1] + (int) $imparr[2] + (int) $imparr[3] + (int) $imparr[4];
                    $cdatajson = json_encode($cdata);
                    echo $cdatajson . '~dbeetypes~' . $totdata;
                    break;
                
                case 'score':
                    $layoutsobj       = new Admin_Model_Layouts();
                    $scoreset         = $layoutsobj->scoringFromDb();
                    $ScoringChartData = $deshboard->ScoringChartData($datefrom, $dateto);
                    
                    $imparr    = explode('=', $ScoringChartData);
                    $cdata     = array(
                        array(
                            'name' => $scoreset['Like'],
                            'y' => (int) $imparr[0],
                            'sliced' => false,
                            'selected' => false
                        ),
                        array(
                            'name' => $scoreset['Love'],
                            'y' => (int) $imparr[1],
                            'sliced' => false,
                            'selected' => false
                        ),
                        array(
                            'name' => $scoreset['Dis Like'],
                            'y' => (int) $imparr[3],
                            'sliced' => false,
                            'selected' => false
                        ),
                        array(
                            'name' => $scoreset['Hate'],
                            'y' => (int) $imparr[4],
                            'sliced' => false,
                            'selected' => false,
                            'color' => '#23uida'
                        )
                    );
                    $totdata   = (int) $imparr[0] + (int) $imparr[1] + (int) $imparr[2] + (int) $imparr[3] + (int) $imparr[4];
                    $cdatajson = json_encode($cdata);
                    echo $cdatajson . '~score~' . $totdata;
                    break;
                
                case 'group':
                    $GroupChartData = $deshboard->GroupChartData($datefrom, $dateto);
                    
                    $imparr    = explode('=', $GroupChartData);
                    $cdata     = array(
                        array(
                            'name' => 'Open',
                            'y' => (int) $imparr[0],
                            'sliced' => false,
                            'selected' => false
                        ),
                        array(
                            'name' => 'Closed',
                            'y' => (int) $imparr[1],
                            'sliced' => false,
                            'selected' => false
                        ),
                        array(
                            'name' => 'Request',
                            'y' => (int) $imparr[2],
                            'sliced' => false,
                            'selected' => false
                        )
                    );
                    $totdata   = (int) $imparr[0] + (int) $imparr[1] + (int) $imparr[2] + (int) $imparr[3] + (int) $imparr[4];
                    $cdatajson = json_encode($cdata);
                    echo $cdatajson . '~group~' . $totdata;
                    
                    break;
                
                default:
                    echo 'dbee';
            }
            exit;
        }
    }
    
    public function callscorechartsAction()
    {
        $request           = $this->getRequest()->getParams();
        $deshboard         = new Admin_Model_Deshboard();
        $defaultimagecheck = new Admin_Model_Common();
        $layoutsobj        = new Admin_Model_Layouts();
        // 	echo "string";exit();
        if (!empty($request['type'])) {
            $scoreset = $layoutsobj->scoringFromDb(); // taking value from database
            // $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $this->view->charttype = $request['type'];
            
            $datefrom  = empty($request['datefrom']) ? '' : $request['datefrom'];
            $dateto    = empty($request['dateto']) ? '' : $request['dateto'];
            $ranglimit = empty($request['ranglimit']) ? '5' : $request['ranglimit'];
            
            $scoretotProvidersdata = array();
            $scuserNameArr         = array();
            $scuserUnameArr        = array();
            $scuserPictureArr      = array();
            
            switch ($request['type']) {
                case $scoreset['Love']:
                    $livescoreData = $deshboard->gettopScoreusers($ranglimit, 'owner', 1, $datefrom, $dateto);
                    $heading       = 'Most ' . strtolower($scoreset['Love']) . 'd';
                    $formatter     = 'Total ' . strtolower($scoreset['Love']) . 'd count';
                    break;
                
                case $scoreset['Like']:
                    $livescoreData = $deshboard->gettopScoreusers($ranglimit, 'owner', 2, $datefrom, $dateto);
                    $heading       = 'Most ' . strtolower($scoreset['Like']) . 'd';
                    $formatter     = 'Total ' . strtolower($scoreset['Like']) . 'd count';
                    break;
                
                case $scoreset['Dis Like']:
                    $livescoreData = $deshboard->gettopScoreusers($ranglimit, 'owner', 4, $datefrom, $dateto);
                    $heading       = 'Most ' . strtolower($scoreset['Dis Like']) . 'd';
                    $formatter     = 'Total ' . strtolower($scoreset['Dis Like']) . 'd count';
                    break;
                
                case $scoreset['F O T']:
                    $livescoreData = $deshboard->gettopScoreusers($ranglimit, 'owner', 3, $datefrom, $dateto);
                    $heading       = 'Biggest ' . strtolower($scoreset['F O T']) . 'd';
                    $formatter     = 'Total ' . strtolower($scoreset['F O T']) . 'd count';
                    break;
                
                case $scoreset['Hate']:
                    $livescoreData = $deshboard->gettopScoreusers($ranglimit, 'owner', 5, $datefrom, $dateto);
                    $heading       = 'Most ' . strtolower($scoreset['Hate']) . 'd';
                    $formatter     = 'Total ' . strtolower($scoreset['Hate']) . 'd count';
                    break;
                
                default:
                    $livescoreData = $deshboard->gettopScoreusers(5, 'owner', 1);
                    
            }
            
            foreach ($livescoreData as $key => $value) {
                $scoretotProvidersdata[] = array(
                    'y' => (int) $value['totscore']
                );
                $scuserNameArr[]         = array(
                    $this->myclientdetails->customDecoding($value['username'])
                );
                $scuserUnameArr[]        = array(
                    $this->myclientdetails->customDecoding($value['uname'])
                );
                $checkdbpic              = $defaultimagecheck->checkImgExist($value['image'], 'userpics', 'default-avatar.jpg');
                $scuserPictureArr[]      = array(
                    $checkdbpic
                );
            }
            
            $scoretotproviderscategory = json_encode($scoretotProvidersdata);
            $scuNameProvidersdata      = json_encode($scuserNameArr);
            $scuserNameProvidersdata   = json_encode($scuserUnameArr);
            $scuserImageProvidersdata  = json_encode($scuserPictureArr);
            
            echo $scoretotproviderscategory . "~" . $scuNameProvidersdata . "~" . $scuserNameProvidersdata . "~" . $scuserImageProvidersdata . "~" . $formatter . "~" . $heading . "~" . count($livescoreData);
            
            exit;
        }
    }
    /**
     * Menu
     */
    public function menuAction()
    {
        // @todo Add the menu page action
    }
    
    public function logoutAction()
    {
        $uid= $_SESSION['Zend_Auth']['storage']['UserID'];
        $this->myclientdetails->updatedata_global('tblUsers',array('LastLogoutDate'=>date('Y-m-d H:i:s')),'UserID',$uid);
       
        if (isset($_SERVER['HTTP_COOKIE']))
        {
            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach($cookies as $cookie) 
            {
                $parts = explode('=', $cookie);
                $name = trim($parts[0]);
                setcookie($name, '', time()-1000, '/','db-csp.com');
            }
        }
        $this->session_name_space->unsetAll();
        $authNamespace = new Zend_Session_NameSpace('identify');
        
        $authNamespace->unsetAll();
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        session_destroy();
        session_unset();
        $this->_redirect('/admin/login');
    }
    
    public function notificationAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest()) {
            $data['status'] = 'success';
            $result = $this->deshboard->getCountForNewNotification($this->_userid, '', 1, '', '');
            $data['count']  = count($result);
            $data['result'] = $result;
        } else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    
    public function ajaxnotificationAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest()) {
            require_once 'includes/globalfileadmin.php';
            $content        = '<ul>';
            $data['status'] = 'success';
            $result         = $this->deshboard->getListNotification($this->adminUserID, '', '', '', '');
            if (count($result) > 0) {
                foreach ($result as $key => $value) {
                    if ($value['act_type'] == 2) {
                        //$db_url = $this->socialInvite->getdburltitle($value['act_typeId']);
                        $hashTag = '#comment';
                        //$postTitle = $db_url['VidDesc'];
                    }
                    $postTitle = '';
                    
                    if ($value['act_type'] == 15) {
                        $hashTag   = '#feedback';
                        $feedArray = $this->deshboard->getFeedBack($value['act_typeId']);
                        $name      = '<a userid="' . $value['UserID'] . '" class="show_details_user" href="javascript:void(0)"> ' . $feedArray['name'] . '</a>';
                    } else
                        $name = '';
                    
                     if ($value['act_type'] == 30)
                        $hashTag = '#post';
                    else if ($value['act_type'] == 16)
                        $hashTag = '#feedback';
                    else if ($value['act_type'] == 16)
                        $hashTag = '#publicvideoevent';
                    else if ($value['act_type'] == 17)
                        $hashTag = '#protectedvideoevent';
                    else if ($value['act_type'] == 18)
                        $hashTag = '#privatevideoevent';
                    else if ($value['act_type'] == 19)
                        $hashTag = '#survey';
                    else if ($value['act_type'] == 12)
                        $hashTag = '#group';
                    else if ($value['act_type'] == 44)
                        $hashTag = '#privatepost';
                    
                    $checkdbpic = $this->common_model->checkImgExist($value['ProfilePic'], 'userpics', 'default-avatar.jpg');
                    
                    $content .= '<li>
								<div class="userPic"><img src="'.IMGPATH.'/users/small/' . $checkdbpic . '" width="40" height="40"/></div>
								<div class="userDetails"><div class="description ">
									<a userid="' . $value['UserID'] . '" class="show_details_user " href="javascript:void(0)">' . $this->myclientdetails->customDecoding($value['Name']) . ' ' . $this->myclientdetails->customDecoding($value['lname']) . '&nbsp;&nbsp;</a><a href = "' . BASE_URL . '/admin/dashboard/notification' . $hashTag . '" class="dateTimeNoti2" > ' . ' ' . $activityaMsg[$value['act_message']] . '&nbsp;&nbsp;</a>' . $name . '</div><span class="dateTimeNoti">' . date('d M, Y', strtotime($value['act_date'])) . '</span></div></li>';
                }
                $data['content'] = $content . '</ul>';
                $data['content'] .= '<div class="bfooter"><a style="float:right;font-weight:normal;" class="btn" href="/admin/dashboard/notification">View all</a>
									</div>';
                $data['count'] = count($result);
            } else
                $data['content'] = '<div class="dashBlockEmpty">notification not found</div>';
        } else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    
    public function updatenotifyAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest()) {
            $type = $this->_request->getPost('Type');
            if ($type == 109) {
                $dataArray['act_status'] = 1;
                $this->deshboard->updateNotify($dataArray, $this->adminUserID, $type);
            } else {
                $dataArray['act_status'] = 1;
                $this->deshboard->updateNotify($dataArray, $this->adminUserID, $type);
            }
        } else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    
    
    public function searchusrAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest()) {
            $dashboard = new Admin_Model_Deshboard();
            $common    = new Admin_Model_Common();
            $gender    = $this->_request->getPost('gender');
            $age1      = $this->_request->getPost('age1');
            $age2      = $this->_request->getPost('age2');
            $cat       = $this->_request->getPost('cat');
            $offset    = $this->_request->getPost('start') ? $this->_request->getPost('start') : '0';
            
            $users     = $this->deshboard->searchusr($cat, $gender, $age1, $age2, '20', $offset);
            $userscnt  = count($this->deshboard->searchusr($cat, $gender, $age1, $age2, '21', $offset));
            $userscnt2 = count($this->deshboard->searchusr($cat, $gender, $age1, $age2, '20', $offset, '0'));
            if (count($users) > 0) {
                //$content = "<div><label class='quickallchkall'><input id='quickchkall' class='quickuser' type='checkbox' name='quickuser' value='1'>
                //<label></label>check all</label></div>";
                foreach ($users as $value) {
                    
                    //$content .= "<div>".$dashboard->customDecoding($value['Name'])."</div>";
                    $valuepic = $common->checkImgExist($value['ProfilePic'], 'userpics', 'default-avatar.jpg');
                    
                    if ($value['country_name'])
                        $city = "<div class='small oneline'>" . "from " . $this->myclientdetails->customDecoding($value['country_name']) . "</div>";
                    if ($value['company'])
                        $company = "<div class='small'>" . " woring with <i>" . $this->myclientdetails->customDecoding($value['company']) . "</i></div>";
                    $content .= "<li title='" . $this->myclientdetails->customDecoding($value['Name']) . "' socialFriendlist='true'>
					<label class='labelCheckbox'>
					<input type='checkbox' value='" . $value['UserID'] . "' checkvalue='" . $checkvalue . "' class='inviteuser-search' name='goupuserid'>
					<div class='follower-box'>
					<div class='usImg'><img class='img border'  align='left' src='".IMGPATH."/users/small/" . $valuepic . "' border='0' /></div>
					<div class='udetailtop'>
					<div class='oneline'><span>" . $this->myclientdetails->customDecoding($value['Name']) . "</span></div>" . $city . " <div class='udetail'><a href='#' class='show_details_user bx bx-yellow' userid='" . $value['UserID'] . "'>Full details</a></div>
					</div>
					</label>
					</div></li>";
                }
                $data['offset'] = $offset + 20;
                $data['count']  = $userscnt;
                $data['count2'] = $userscnt2;
                if ($userscnt > 20) {
                    $content .= '<div class="quickseemore"><a href="#">see more</a></div>';
                }
                
            } else {
                $content .= '<div class="textcolor" >No results</div>';
                $data['notfound'] = 1;
            }
            
            $data['user']    = $content;
            $data['success'] = '1';
        } else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    
    public function matchtaglistAction()
    {
    	$data = array();
    	$content = array();
    	$this->_helper->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	$response = $this->getResponse();
    	$response->setHeader('Content-type', 'application/json', true);
    	//if ($this->getRequest()->isXmlHttpRequest()) {
    		$userobj = new Admin_Model_User();
    		$common    = new Admin_Model_Common();    		
    
    		$data     = $userobj->getuserbytitle();
    		if(count($data)>0){
    		$tagstr .= '<div id="tagwrapper">';
    		if(count($data['tag'])!=''){
                $i=100;
    			$tagstr .= '<div class="tagcnt"><ul class="box-brdertag" id="mactag"><li class="heading">#tags</li>';
    			
                foreach($data['tag'] as $value){
                     $tagstrcnt = '';
                     $cntu = 'user';
                    if(strlen($value['tag'])>15) $tagstrcnt = "..";
                    if($value['cnt']>1) $cntu = 'users';
    				$tagstr .= '<li><label><input id="tlallresult'.$i.'" class="taginput" type="checkbox" value="' .$value['DbTag'].'" name="tag"><label for="tlallresult'.$i.'"></label></label><a  title="'.$value['DbTag'].'"  href="javascript:void(0)" >#' .substr($value['DbTag'],0,15).$tagstrcnt.'</a> '.$value['cnt'].' '.$cntu.' match <a class="btn btn-green btn-mini pull-right tagusrsetd" cnt="'.$value['cnt'].'" tag="' .$value['DbTag'].'">
                                View</a></li>';
                    $i++;
    			}
                if(count($data['tagcnt'])>10){    
                $tagstr .= '<li><div class="bfooter">
                            <span class="bfTotal"><strong>'.count($data['tagcnt']).'</strong> total </span>
                            <a class="btn matchingbtnc" href="'.BASE_URL.'/admin/matching/index/type/tag">View all</a></div></li>';
                  }
    		
    				$tagstr .= '</ul></div>';
                    
    		
    				
    		}
    		
    		if(count($data['title'])!=''){
                $i=200;
    			$tagstr .= '<div class="tagcnt"><ul class="box-brdertag" id="mactitle"><li class="heading">Job title</li>';
    			foreach($data['title'] as $value){
                     $titlestr = '';
                     $cntu = 'user';
                    if(strlen($this->myclientdetails->customDecoding($value['title']))>15) $titlestr = "..";
                    if($value['cnt']>1) $cntu = 'users';
    				$tagstr .= '<li><label><input id="tlallresult'.$i.'" class="taginput" type="checkbox" value="' .$value['title'].'" name="title"><label for="tlallresult'.$i.'"></label></label><a  href="javascript:void(0)" class="tagtile" title="'.$this->myclientdetails->customDecoding($value['title']).'">' .substr($this->myclientdetails->customDecoding($value['title']),0,15).$titlestr.'</a> '.$value['cnt'].' '.$cntu.' match <a class="btn btn-green btn-mini pull-right tagusrsetd" cnt="'.$value['cnt'].'" title="'.$value['title'].'">
                                View</a></li>';
    			    $i++;
                }

                if(count($data['titlecnt'])>10){    
                $tagstr .= '<li><div class="bfooter">
                            <span class="bfTotal"><strong>'.count($data['titlecnt']).'</strong> total  </span>
                            <a class="btn matchingbtnc" href="'.BASE_URL.'/admin/matching/index/type/tilte">View all</a></div></li>';
                  }
    		
    			$tagstr .= '</ul></div>';
    		
    		
    		}
    		
    		if(count($data['company'])!=''){
                $i=1;
    			$tagstr .= '<div class="tagcnt"><ul class="box-brdertag" id="maccompany"><li class="heading">Company</li>';
    			foreach($data['company'] as $value){
                    $companystr = '';
                    $cntu = 'user';
                    if($this->myclientdetails->customDecoding($value['company'])>15) $companystr = "..";
                    if($value['cnt']>1) $cntu = 'users';
    				$tagstr .= '<li><label><input id="tlallresult'.$i.'" class="taginput" type="checkbox" value="' .$value['company'].'" name="company"><label for="tlallresult'.$i.'"></label></label><a href="javascript:void(0)" class="tagcompany" title="'.$this->myclientdetails->customDecoding($value['company']).'">' .substr($this->myclientdetails->customDecoding($value['company']),0,15).$companystr.'</a> '.$value['cnt'].' '.$cntu.' match <a class="btn btn-green btn-mini pull-right tagusrsetd" cnt="'.$value['cnt'].'" company="' .$value['company'].'">
                                View</a></li>';
    			     $i++;
                }

                if(count($data['companycnt'])>10){    
                $tagstr .= '<li><div class="bfooter">
                            <span class="bfTotal"><strong>'.count($data['companycnt']).'</strong> total  </span>
                            <a class="btn matchingbtnc" href="'.BASE_URL.'/admin/matching/index/type/company">View all</a></div></li>';
                  }
    		
    			$tagstr .= '</ul></div></div>';
    		
    		
    		}
    	}else{
    		
    		$tagstr .= '<div id="tagwrapper" style="margin-left:45%;"><div class="heading">No record found</div></div>';
    	}	
    	
    		
   
    		
    
    		$data1['content']    = $tagstr;
    		
    		$data1['success'] = '1';
    	//}
    	return $response->setBody(Zend_Json::encode($data1));
    }
    
}
