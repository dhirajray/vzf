<?php
class DashboarduserController extends IsController {
	public function init() {
		parent::init ();
		$storage = new Zend_Auth_Storage_Session ();
		$auth = Zend_Auth::getInstance ();
		if ($auth->hasIdentity ()) {
			$data = $storage->read ();
			$this->_userid = $data ['UserID'];
		} else
			$this->_helper->redirector->gotosimple ( 'index', 'index', true );
		$this->commonfun = new Application_Model_Commonfunctionality ();
	}

	public function takeatourAction() {
		$data = array ();
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		$response = $this->getResponse ();
		$response->setHeader ( 'Content-type', 'application/json', true );
		if ($this->getRequest ()->isXmlHttpRequest () && $this->getRequest ()->getMethod () == 'POST') {
			$userid = $this->_userid;
			$update = $this->myclientdetails->updatedata_global ( 'tblUsers', array('takeatour'=>1), 'UserID', $userid );
			$data['success'] = $update;
			
		}
		return $response->setBody ( Zend_Json::encode ( $data ) );
	}
	public function indexAction() {
		$this->session_name_space->callBackUrl = '';
		$filter = new Zend_Filter_StripTags ();
		$this->view->susername = $susername = $filter->filter ( $this->_getParam ( 'username' ) );
		$usersite = new Application_Model_Usersite ();
		$profile = new Application_Model_Profile ();
		$cat = new Application_Model_Category ();
		
		// ********** user login status as per days **********
		$chkLoginque = 'select * from tbluserlogindetails where userid = ' . $this->_userid . ' AND DATE_FORMAT(logindate, "%Y-%m-%d") ="' . date ( 'Y-m-d' ) . '"';
		$chkLogin = $this->myclientdetails->passSQLquery ( $chkLoginque );
		if (count ( $chkLogin ) < 1 || $chkLogin [count ( $chkLogin ) - 1] ['logoutdate'] != "0000-00-00 00:00:00") {
			$dataval = array (
					'clientID' => clientID,
					'userid' => $this->_userid,
					'logindate' => date ( "Y-m-d H:i:s" ) 
			);
			$this->myclientdetails->insertdata_global ( 'tbluserlogindetails', $dataval );
			$this->myclientdetails->updatedata_global ( 'tblUsers', array('LastLoginDate'=>date( "Y-m-d H:i:s" )),'UserID',$this->_userid );
		}
		// echo "<pre>"; print_r($chkLogin); exit;
		
		// ********** user login status as per days **********
		
		if (! empty ( $susername ))
			$this->view->profileuserid = $userid = $this->myhome_obj->userdetailallbyname ( $this->myclientdetails->customEncoding ( $susername ) );
		
		if (! $userid) {
			$proid = ( int ) $this->_getParam ( 'id' );
			if ($proid != '' || $proid == 0) {
				$rowobj = $this->commonmodel_obj->getredbeedetail ( $proid );
				if (! empty ( $rowobj ['Username'] ))
					$this->_redirect ( '/user/' . $this->myclientdetails->customDecoding ( $rowobj ['Username'] ) );
			} else
				$userid = '';
		}
		if ($userid) {
			
			$redirection_name_space = new Zend_Session_Namespace ( 'User_Session' );
			// redirection code
			
			if ($redirection_name_space->redirection != '')
				$this->_redirect ( 'Myhome/redirect' );
			
			$profileholder = ($userid == $this->_userid) ? true : false;
			$this->view->invite = $_GET ['invite'];
			$this->view->profileholder = $profileholder;
			$this->view->userid = $userid;
			$this->view->dbeeuser = $this->_userid;
			$this->view->cat = $cat->getallcategory ();
			$userinfodetails = $this->User_Model->userdetailall ( $userid );
			
			$AllowTC = $this->myclientdetails->getAllMasterfromtable ( 'tblUserMeta', array (
					'*' 
			), array (
					'UserId' => $this->_userid,
					'never_show_after_login_tc' => 1
			) );
			$this->view->chkAllowTC = count ( $AllowTC );
			$this->view->carerid = $AllowTC[0]['carerid'];
			//var_dump($AllowTC[0]['carerid']); die;
			$FollowsUserdata = $this->myclientdetails->getAllMasterfromtable ( 'tblFollows', array (
					'User' 
			), array (
					'FollowedBy' => $this->_userid 
			) );
			$this->view->FollowsUserdata = $FollowsUserdata;

			/*if ($userid=='33413'){
				echo "<pre>"; print_r($userinfodetails); exit;
			}*/
			

			
			$this->view->usertype = $userinfodetails[0]['usertype'];
			$this->view->role = $userinfodetails[0]['role'];
			$this->view->hideuser = $userinfodetails[0]['hideuser'];
			$this->view->userpic = $userinfodetails[0]['ProfilePic'];
			$this->view->username = $userinfodetails[0]['Username'];
			$this->view->sidebartab = $userinfodetails[0]['sidebartab'];
			$this->view->takeatour = $userinfodetails[0]['takeatour'];
			$this->view->useractivestatus = $userinfodetails[0]['Status'];
			$userexiest = count ( $userinfodetails );
			$this->view->userexiest = $userexiest;
			$this->view->personaledit = ($this->_userid == $userid) ? true : false;
			$usersite = new Application_Model_Usersite ();
			$pdflist = $usersite->getpdflist ( $this->_userid );
			$myuploadlist = $usersite->getmyshare ( $this->_userid );
			$this->view->chkpdf = (count ( $pdflist ) > 0) ? 1 : 0;
			$this->view->chkmyupload = (count ( $myuploadlist ) > 0) ? 1 : 0;
		} else
			$this->_helper->redirector->gotosimple ( 'index', 'index', true );
		$biographi = $profile->userbiographi ( $userid );
		
		$this->view->biographi = $biographi;
		// user you may interserd now block
		
		if ($this->myclientdetails->customDecoding ( $userinfodetails [0] ['latitude'] ) != "" && $this->myclientdetails->customDecoding ( $userinfodetails [0] ['longitude'] )) {
			$Distancearray = $profile->userbasedondistance ( $this->myclientdetails->customDecoding ( $userinfodetails [0] ['latitude'] ), $this->myclientdetails->customDecoding ( $userinfodetails [0] ['longitude'] ), $userid );
			$occuerarray = array ();
			foreach ( $this->view->cat as $key => $value ) {
				$dbcatposted = $profile->dbcatposted ( $userid, $value ['CatID'] );
				if ($dbcatposted ['total'] > 0) {
					$catid = $value ['CatID'];
					$occuerarray [$catid] = $dbcatposted ['total'];
				}
			}
			
			$distancequery = "((ACOS(SIN(" . $this->myclientdetails->customDecoding ( $userinfodetails [0] ['latitude'] ) . " * PI() / 180) * SIN(REPLACE(latitude, 'mabirdnny#', '') * PI() / 180) + COS(" . $this->myclientdetails->customDecoding ( $userinfodetails [0] ['latitude'] ) . " * PI() / 180) * COS(REPLACE(latitude, 'mabirdnny#', '') * PI() / 180) * COS((" . $this->myclientdetails->customDecoding ( $userinfodetails [0] ['longitude'] ) . " - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515)*1.609344";
			
			if (count ( $occuerarray ) > 0) {
				$maxs = array_keys ( $occuerarray, max ( $occuerarray ) );
				$maxpostcat = $maxs [0];
			} else {
				$maxpostcat = "";
			}
			
			if ($maxpostcat > 0 && $maxpostcat != "") {
				$sql = "SELECT IFNULL(" . $distancequery . ",20000) as 'distance',COUNT( * ) AS total,u.Username,u.Name,u.full_name,u.title,u.company,u.ProfilePic,u.lname,d.User as userid from tblUsers as u,tblDbees as d WHERE u.clientID=" . clientID . " AND FIND_IN_SET( " . $maxpostcat . ", `Cats` ) >0";
				if ($userinfodetails [0] ['usertype'] != 100) {
					$sql .= " AND u.usertype!=100";
				}
				$sql .= " AND u.UserID=d.User AND u.role!=1 AND d.User!=" . $userid . "  GROUP BY u.UserID having total> 4 order by total desc limit 5";
				
				$usercatbased = $this->myclientdetails->passSQLquery ( $sql );
				$result = array_merge ( $Distancearray, $usercatbased );
			} else {
				$result = $Distancearray;
			}
			
			$this->view->DisInsUser = $result;
			$this->view->count = count ( $result );
		} else {
			$this->view->DisInsUser = 0;
			$this->view->count = 0;
		}
	}
	public function array_unique_multidimensional($input) {
		$serialized = array_map ( 'serialize', $input );
		$unique = array_unique ( $serialized );
		return array_intersect_key ( $input, $unique );
	}
	public function mycustomdashboardAction() {
		$data = array ();
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		$response = $this->getResponse ();
		$response->setHeader ( 'Content-type', 'application/json', true );
		if ($this->getRequest ()->isXmlHttpRequest () && $this->getRequest ()->getMethod () == 'POST') {
			$userid = $this->_userid;
			$myDashboard = $this->myclientdetails->getAllMasterfromtable ( 'tblCustomDashboard', array (
					'*' 
			), array (
					'cus_userid' => $userid 
			) );
			
			$mymovedpost = $this->myclientdetails->getAllMasterfromtable ( 'tblHiddenPosts', array (
					'*' 
			), array (
					'UserID' => $userid,
					'actiontype' => 1 
			) );
			
			$masterfieldids = '';
			
			$returnDash = '';
			$data ['showfilters'] = '';
			
			$returnDash .= '<li>
				<a  href="javascript:void(0)" id="my-dbees-profile" class="active" title="My posts" onclick=javascript:seeglobaldbeelist(' . $userid . ',4,"my-dbees","myhome","mydbee","mydb")>My posts</a>			
			</li>';
			if (count ( $myDashboard ) > 0) {
				foreach ( $myDashboard as $key => $value ) {
					if ($value ['cus_filtertype'] == "postCategory" && $value ['cus_ids'] != '') {
						$postedCats = $value ['cus_ids'];
						$catarrIds = explode ( ',', $value ['cus_ids'] );
						$myCatsName = '';
						foreach ( $catarrIds as $key => $value ) {
							$myCats = $this->myclientdetails->getAllMasterfromtable ( 'tblDbeeCats', array (
									'CatName' 
							), array (
									'CatID' => $value 
							) );
							$myCatsName .= $myCats [0] ['CatName'] . ',';
						}
						$returnDash .= "<input type='hidden' id='fieldsName' value='" . $myCatsName . "'>";
						$returnDash .= '<li><a onclick=javascript:showmyDashboard("nouserid",3,"my-dbees","myhome","catetorylist","dbcat","' . $postedCats . '"); href="javascript:void(0)" class="feed-link" id="my-comments-home2" >Categories</a>
							</li>';
						$data ['showfilters'] = 1;
					}
					if ($value ['cus_filtertype'] == "groupCategory" && $value ['cus_ids'] != '') {
						$gCatsName = $value ['cus_ids'];
						$gcatarrIds = explode ( ',', $value ['cus_ids'] );
						$mygCatsName = '';
						foreach ( $gcatarrIds as $key => $value ) {
							$mygCats = $this->myclientdetails->getAllMasterfromtable ( 'tblGroupTypes', array (
									'TypeName' 
							), array (
									'TypeID' => $value 
							) );
							$mygCatsName .= $mygCats [0] ['TypeName'] . ',';
						}
						$returnDash .= "<input type='hidden' id='groupcats' value='" . $mygCatsName . "'>";
						
						$returnDash .= '<li><a onclick=javascript:showmyDashboard("nouserid",3,"my-dbees","group","customdashboardgroups","groupcat","' . $gCatsName . '"); href="javascript:void(0)" class="feed-link" id="my-comments-home2" >Groups</a>
							</li>';
						$data ['showfilters'] = 1;
					}
					if ($value ['cus_filtertype'] == "masterfield" && $value ['cus_ids'] != '') {
						$masterfieldids = $value ['cus_ids'];
						$usrarrIds = explode ( ',', $value ['cus_ids'] );
						$myusersName = '';
						foreach ( $usrarrIds as $key => $value ) {
							$myusers = $this->myclientdetails->getAllMasterfromtable ( 'tblUsers', array (
									'Name',
									'ProfilePic',
									'Email',
									'UserID' 
							), array (
									'UserID' => $value 
							) );
							$myusersName .= $this->myclientdetails->customDecoding ( $myusers [0] ['Name'] ) . ',';
							$myusersImg .= $this->commonfun->checkImgExist ( $myusers [0] ['ProfilePic'], 'userpics', 'default-avatar.jpg' ) . ',';
						}
						$returnDash .= "<input type='hidden' id='masterfieldids' value='" . $myusersName . "'>";
						$returnDash .= "<input type='hidden' id='masteruserpics' value='" . $myusersImg . "'>";
						$returnDash .= '<li><a onclick=javascript:showmyDashboard("nouserid",3,"my-dbees","Following","customdashboarduser","masterUsers","' . $masterfieldids . '"); href="javascript:void(0)" class="feed-link" id="my-comments-home2" >Users </a>
							</li>';
						$data ['showfilters'] = 1;
					}
				}
			}
			if (count ( $mymovedpost ) > 0) {
				$movedpost = '';
				foreach ( $mymovedpost as $key => $value ) {
					$movedpost .= $value ['dbeeID'] . ',';
				}
				$returnDash .= '<li><a onclick=javascript:showmyDashboard("nouserid",3,"my-dbees","group","customdashboardgroups","movedposts","' . $movedpost . '"); href="javascript:void(0)" class="feed-link" id="my-comments-home2" >Live stream  </a>	</li>';
			}
			if ($this->session_data ['promoted'] == 1 || count ( $this->expert_model->getPomotedQuestion ( $this->_userid, 1 ) ) > 0)
				$returnDash .= '<li>
				<a  href="javascript:void(0)" id="my-dbees-qa" class="feed-link" title="My QA" >My QA<span class="CountQA" id="pendingQACount"></span></a></li>';
			if ($returnDash == '')
				$data ['returnDash'] = '';
			else {
				// $returnDash .= '<div id="customFieldsDown" class="btn btn-yellow dropDown pull-right" style="display:none">SORT BY TYPE <i class="fa fa-angle-down"></i> <ul class="postListingdashboard dropDownList right" id="customFields"></ul> </div>';
				$data ['returnDash'] = $returnDash;
			}
		}
		return $response->setBody ( Zend_Json::encode ( $data ) );
	}
	public function adminliveAction() {
		$data = array ();
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		$response = $this->getResponse ();
		$response->setHeader ( 'Content-type', 'application/json', true );
		if ($this->getRequest ()->isXmlHttpRequest () && $this->getRequest ()->getMethod () == 'POST') {
			$userid = $this->_userid;
			$CurrDate = date ( 'Y-m-d H:i:s' );
			$datamsg = '';
			
			// $adminpostlist = $this->myclientdetails->getfieldsfromtableusingin(array('*'),'tblDbees','DbeeID','6,7');
			$adminpostlist = $this->myclientdetails->getAllMasterfromtable ( 'tblDbees', array (
					'DbeeID',
					'Text',
					'surveyTitle',
					'Type',
					'dburl',
					'VidDesc' 
			), array (
					'User' => adminID,
					'Active' => '1' 
			), array (
					'LastActivity' => 'DESC' 
			), 3 );
			foreach ( $adminpostlist as $key => $value ) {
				$typeIcon = '<i class="fa fa-user"></i>';
				$title = $value ['Text'];
				if ($value ['Type'] == 1)
					$typeIcon = '<i class="fa fa-user"></i>';
				if ($value ['Type'] == 6) {
					$typeIcon = '<i class="fa fa-film"></i>';
					$title = $value ['VidDesc'];
				}
				if ($value ['Type'] == '7') {
					$typeIcon = '<i class="fa fa-check-square-o"></i>';
					$title = $value ['surveyTitle']; // echo $value['Type'];exit;
				}
				if ($value ['Type'] == 9)
					$typeIcon = '<i class="fa fa-calendar"></i>';
				
				if ($title != '') {
					$datamsg .= '<div class="rbcRow">
					     	<a href="' . BASE_URL . '/dbee/' . $value ['dburl'] . '">' . $typeIcon . '						 	
							 	<span class="labelSidebar Dt1" id="labelSidebarMyComment">' . $this->myclientdetails->dbSubstring ( strip_tags ( $title ), '150', '...' ) . '</span>
							</a>
						</div>';
				}
			}
			$data ['msg'] = $datamsg;
			if ($data ['msg'] == '')
				$data ['msg'] = '<div class="rbcRow noFound">no results found</div>';
		}
		
		return $response->setBody ( Zend_Json::encode ( $data ) );
	}
	public function myquestionAction() {
		$data = array ();
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		$response = $this->getResponse ();
		$response->setHeader ( 'Content-type', 'application/json', true );
		if ($this->getRequest ()->isXmlHttpRequest () && $this->getRequest ()->getMethod () == 'POST') {
			$type = '';
			$expert = new Application_Model_Expert ();
			$type = $this->_request->getPost ( 'type' );
			$allmyquestion = $expert->MyQuestion ( $this->_userid, $type );
			
			$ownercontent = '';
			$expertcontent = '';
			if (count ( $allmyquestion ) > 0) {
				$data ['success'] = '1';
				foreach ( $allmyquestion as $value ) {
					$typeIcon = '';
					if (strlen ( $value ['qna'] ) > 30 && $type == 'limit') {
						$title = $value ['qna']; // substr($value['qna'], 0,30).'..';
						$oneline = 'oneline';
					} else {
						$title = $value ['qna'];
						$oneline = '';
					}
					
					if ($value ['reply'] == 0) {
						$typeIcon = '<i class="fa fa-clock-o" style="color:#666666;"></i>';
						$tip = 'Pending';
					} else {
						$typeIcon = '<i class="fa fa-check-circle" style="color:green;"></i>';
						$tip = 'Answered';
					}
					
					if (! empty ( $title )) {
						
						$ownercontent .= '<div class="rbcRow" class="left">   							
									<a href="' . BASE_URL . '/dbee/' . $value ['dburl'] . '#myqa">
										<span title="' . $tip . '" rel="dbTip"  class="iconsQA">' . $typeIcon . '</span>
										<div class="qaTitleCol"><div class="' . $oneline . '">' . $title . '</div></div>
											
									</a>	
								</div>';
					}
				}
			} else {
				$data ['success'] = '0';
			}
		}
		
		$data ['count'] = count ( $allmyquestion );
		$data ['ownercontent'] = $ownercontent;
		
		return $response->setBody ( Zend_Json::encode ( $data ) );
	}
	public function pendingquestionAction() {
		$data = array ();
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		$response = $this->getResponse ();
		$response->setHeader ( 'Content-type', 'application/json', true );
		if ($this->getRequest ()->isXmlHttpRequest () && $this->getRequest ()->getMethod () == 'POST') {
			
			$expert = new Application_Model_Expert ();
			$type = $this->_request->getPost ( 'type' );
			
			$OwnrRec = 0;
			$ExpertRec = 0;
			
			$OwnerRecQuery = $this->myclientdetails->passSQLquery ( "SELECT count(*) AS totalowner FROM tblDbeeQna WHERE clientID='" . clientID . "' AND dbowner='" . $this->_userid . "' AND parentid=0 AND reply=0" );
			
			$ExpertRecQuery = $this->myclientdetails->passSQLquery ( "SELECT count(*) AS totalexpert FROM tblDbeeQna WHERE clientID='" . clientID . "' AND expert_id='" . $this->_userid . "' AND parentid=0 AND reply=0" );
			
			$OwnrRec = $OwnerRecQuery [0] ['totalowner'];
			$ExpertRec = $ExpertRecQuery [0] ['totalexpert'];
			
			if ($OwnrRec != 0 || $ExpertRec != 0) {
				$allpendingquestion = $expert->PendingQue ( $this->_userid, $type, $OwnrRec, $ExpertRec );
				
				$content = '';
				if (count ( $allpendingquestion ) > 0) {
					$data ['success'] = '1';
					foreach ( $allpendingquestion as $value ) {
						$typeIcon = '';
						if (strlen ( $value ['qna'] ) > 30 && $type == 'limit') {
							$title = substr ( $value ['qna'], 0, 30 ) . '..';
							
							$btn = '';
						} else {
							$title = $value ['qna'];
						}
						
						if ($type == 'all') {
							if ($value ['dbowner'] == $this->_userid) {
								if ($value ['status'] == '2') {
									$btn = '<a class="btn btn-mini btn-green pull-right" href="' . BASE_URL . '/dbee/' . $value ['dburl'] . '#pqa">Click here to verify</a>';
								}
								if ($value ['status'] == '9') {
									$btn = '<a class="btn btn-mini btn-green pull-right" href="' . BASE_URL . '/dbee/' . $value ['dburl'] . '#pqa">Click here to answer</a>';
								}
							}
							if ($value ['expert_id'] == $this->_userid) {
								$btn = '<a class="btn btn-mini btn-green pull-right" href="' . BASE_URL . '/dbee/' . $value ['dburl'] . '#pqa">Click here to answer</a>';
							}
						}
						
						if (! empty ( $title )) {
							
							$content .= '<div class="rbcRow">		   							
							     	<a href="' . BASE_URL . '/dbee/' . $value ['dburl'] . '#pqa">
									 	<div class="rtpdflinkd">' . $title . ' ' . $btn . '</div></a>	
								</div>';
						}
					}
				} else {
					$data ['success'] = '0';
				}
			}
			$data ['count'] = count ( $allpendingquestion );
		} else {
			$data ['count'] = '0';
		}
		
		$data ['content'] = $content;
		
		return $response->setBody ( Zend_Json::encode ( $data ) );
	}
	public function loaduserpdfAction() {
		$data = array ();
		$iconexe = array (
				'docx'=>'word',
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
				'pdf'=>'pdf');
	
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		$response = $this->getResponse ();
		$response->setHeader ( 'Content-type', 'application/json', true );
		if ($this->getRequest ()->isXmlHttpRequest () && $this->getRequest ()->getMethod () == 'POST') {
			
			$usersite = new Application_Model_Usersite ();
			$pdflist = $usersite->getpdflist ( $this->_userid );
			$myshare = $usersite->getmyshare ( $this->_userid );
			// print_R($pdflist);
			if (count ( $pdflist ) > 0) {
				$data ['success'] = '1';
				foreach ( $pdflist as $value ) {
					
					$title = $value ['kc_cat_title'];
					$exticon = '';
					$typeIcon = '';
					$ext = '';
					$ext = pathinfo ( $value ['kc_file'], PATHINFO_EXTENSION );
					$exticon = $iconexe [$ext];
					$exticon = ($exticon != '') ? $exticon : 'text';
					$typeIcon = '<i class="fa fa-file-' . $exticon . '-o pdfPostIcon"></i>';
					if (! empty ( $title )) {
						$datacontent .= '<div class="rbcRow">
							     	<a href="' . BASE_URL . '/dashboarduser/downloadpdfuser/pdf/' . $value ['kc_file'] . '/isf/' . $value ['is_front'] . '/id/' . $value ['kc_id'] . '">
									 	<div class="rtpdflinkd">' . $typeIcon . ' ' . $title . ' </div>								 	
									 	<div class="rtpdfdownload" ><i class="fa fa-download" rel="dbTip" title="Download"></i></div></a>
									
								</div>';
					}
				}
			}
			
			if (count ( $myshare ) > 0) {
				$data ['success'] = '1';
				
				foreach ( $myshare as $value ) {
					
					$typeIcon = '<i class="fa fa-file-pdf-o pdfPostIcon"></i>';
					$title = $value ['kc_cat_title'];
					$exticon = '';
					$typeIcon = '';
					$ext = '';
					$ext = pathinfo ( $value ['kc_file'], PATHINFO_EXTENSION );
					$exticon = $iconexe [$ext];
					$exticon = ($exticon != '') ? $exticon : 'text';
					$typeIcon = '<i class="fa fa-file-' . $exticon . '-o pdfPostIcon"></i>';
					
					if (! empty ( $title )) {
						$datashare .= '<div class="rbcRow">
							     	<a href="javascript(void)">
									 	<div class="rtpdflinkd">' . $typeIcon . ' ' . $title . ' </div>
									 	<div class="rtpdfdownload shareArrow"  title="Share"><i class="fa fa-share-alt"></i></span>
											<ul>
												<li fileid="' . $value ['kc_id'] . '" type="following">Share with Followers</li>		
										 		
										 		<li fileid="' . $value ['kc_id'] . '" type="user">Select users</li>										 		
									 		</ul>
									 	</div></a>
									 	
					
								</div>';
					}
				}
			}
		}
		$data ['content'] = $datacontent;
		$data ['myshare'] = $datashare;
		if ($data ['content'] == '')
			$data ['msg'] = 'no results found';
		return $response->setBody ( Zend_Json::encode ( $data ) );
	}
	public function loaduserpdfdetailAction() {
		$data = array ();
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		$response = $this->getResponse ();
		$response->setHeader ( 'Content-type', 'application/json', true );
		if ($this->getRequest ()->isXmlHttpRequest () && $this->getRequest ()->getMethod () == 'POST') {
			$usersite = new Application_Model_Usersite ();
			$filter = $this->_request->getPost ( 'type' );
			if ($filter == 'share') {
				$pdflist = $usersite->getmyshare ( $this->_userid, false );
			} else {
				$pdflist = $usersite->getpdflist ( $this->_userid, false );
			}
			
			if (count ( $pdflist ) > 0) {
				$data ['success'] = '1';
				$i = 0;
				foreach ( $pdflist as $value ) :
					$title = $value ['kc_cat_title'];
					if (! empty ( $title )) {
						$data ['content'] [$i] ['title'] = $title;
						$data ['content'] [$i] ['kc_file'] = $value ['kc_file'];
						$data ['content'] [$i] ['adddate'] = date ( 'd M Y', strtotime ( $value ['kc_adddate'] ) );
						$data ['content'] [$i] ['full_name'] = $this->myclientdetails->customDecoding ( $value ['full_name'] );
						$data ['content'] [$i] ['is_front'] = $value ['is_front'];
						$data ['content'] [$i] ['id'] = $value ['kc_id'];
						$data ['content'] [$i] ['sdate'] = date ( 'd M Y', strtotime ( $value ['kc_adddate'] ) );
						$data ['content'] [$i] ['stime'] = date ( "H:i", strtotime ( $value ['kc_adddate'] ) );
					}
					$i ++;
				endforeach
				;
			} else {
				$data ['success'] = '0';
			}
		}
		if ($data ['content'] == '')
			$data ['msg'] = 'no results found';
		return $response->setBody ( Zend_Json::encode ( $data ) );
	}
	public function customdashboardAction() {
		$data = array ();
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		$response = $this->getResponse ();
		$response->setHeader ( 'Content-type', 'application/json', true );
		if ($this->getRequest ()->isXmlHttpRequest () && $this->getRequest ()->getMethod () == 'POST') {
			$userid = $this->_userid;
			$filtertype = $this->_request->getPost ( 'filtertype' );
			$filterid = $this->_request->getPost ( 'filterid' );
			$subtype = $this->_request->getPost ( 'subtype' ); // in case of
			$CurrDate = date ( 'Y-m-d H:i:s' );
			
			$filterid = trim ( $filterid, ',' );
			if ($filtertype == 'masterfield') {
				$masArr = explode ( ',', $filterid );
				$masunq = array_unique ( $masArr );
				$filterid = implode ( $masunq, ',' );
			}
			
			$groupcatlist = $this->myclientdetails->getAllMasterfromtable ( 'tblCustomDashboard', array (
					'cus_id' 
			), array (
					'cus_userid' => $userid,
					'cus_filtertype' => $filtertype 
			) );
			
			$filterData = array (
					'clientID' => clientID,
					'cus_userid' => $userid,
					'cus_filtertype' => $filtertype,
					'cus_subtype' => $subtype,
					'cus_ids' => $filterid,
					'cus_date' => $CurrDate 
			);
			
			if ($groupcatlist [0] ['cus_id'] == '') {
				$add = $this->myclientdetails->insertdata_global ( 'tblCustomDashboard', $filterData );
				
				if ($add)
					$data ['msg'] = 'Dashboard filter created';
			} else {
				$update = $this->myclientdetails->updatedata_global ( 'tblCustomDashboard', $filterData, 'cus_id', $groupcatlist [0] ['cus_id'] );
				if ($update)
					$data ['msg'] = 'Dashboard filter updated';
			}
			
			/*
			 * $masterfieldlist 	= 	$this->myclientdetails->getAllMasterfromtable('tblCustomDashboard',array('cus_id'),array('cus_userid'=>$userid,'cus_filtertype'=>'masterfield'));
			 * if($masterfieldlist[0]['cus_id']=='')
			 * {
			 * $masterfilterData 	= array('clientID'=>clientID,'cus_userid'=>$userid,'cus_filtertype'=>'masterfield','cus_subtype'=>$subtype,'cus_ids'=>$filterid,'cus_date'=>$CurrDate);
			 *
			 * $master = $this->myclientdetails->insertdata_global('tblCustomDashboard',$masterfilterData);
			 * }
			 * else {
			 * $this->myclientdetails->deleteMaster('tblCustomDashboard',array('cus_userid'=>$userid,'cus_filtertype'=>'masterfield'));
			 * $addedPostcatlist 	= 	$this->myclientdetails->getAllMasterfromtable('tblCustomDashboard',array('cus_ids'),array('cus_userid'=>$this->_userid));
			 *
			 * if(count($addedPostcatlist)>0)
			 * {
			 * $alladdedids = '';
			 * foreach ($addedPostcatlist as $key => $value) {
			 * if($value['cus_ids']!='') $alladdedids .= $value['cus_ids'].',';
			 * }
			 * $masterfilterData 	= array('clientID'=>clientID,'cus_userid'=>$userid,'cus_filtertype'=>'masterfield','cus_subtype'=>$subtype,'cus_ids'=>$alladdedids,'cus_date'=>$CurrDate);
			 * $master = $this->myclientdetails->insertdata_global('tblCustomDashboard',$masterfilterData);
			 * }
			 *
			 * }
			 */
			
			if ($data ['msg'] == '')
				$data ['msg'] = 'Something went wrong, Please change filters and try again';
		}
		return $response->setBody ( Zend_Json::encode ( $data ) );
	}
	public function followandsearchusersAction() {
		$data = array ();
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		$response = $this->getResponse ();
		$response->setHeader ( 'Content-type', 'application/json', true );
		if ($this->getRequest ()->isXmlHttpRequest ()) { // $this->getRequest()->isXmlHttpRequest()
			$searchfor = '';
			$loadmore = '';
			$searchfor = $this->_request->getPost ( 'type' );
			$keyword = $this->_request->getPost ( 'keyword' );
			$league_obj = new Application_Model_Dbleagues ();
			$keyword = $this->myclientdetails->customEncoding ( $keyword, 'searchleague' );
			$return = '';
			
			$addedPostcatlist = $this->myclientdetails->getAllMasterfromtable ( 'tblCustomDashboard', array (
					'cus_ids' 
			), array (
					'cus_userid' => $this->_userid,
					'cus_filtertype' => 'masterfield' 
			) ); // 'cus_filtertype'=>$searchfor
			
			if (count ( $addedPostcatlist ) > 0) {
				$alladdedids = '';
				foreach ( $addedPostcatlist as $key => $value ) {
					$alladdedids .= $value ['cus_ids'] . ',';
				}
				$postFollowingArray = explode ( ',', $alladdedids );
				$postFollowingArray = array_unique ( $postFollowingArray );
			}
			if ($searchfor == 'searchuser') {
				$allfollows = $league_obj->getfollowingandfollowers ( $this->_userid );
				
				$row = $league_obj->getusersnotinfollow ( $keyword, $allfollows );
				$class = 'BoxSearch';
				$lableClass = 'notCheckbox';
				// $subBtn = '<div class="pull-right "><a id="searchUsers" class="pull-left btn btn-black filterdashboard" href="javascript:void(0)">Post</a></div>';
			} else {
				if ($searchfor == 'following') {
					$class = 'boxFollowing';
					$lableClass = 'labelCheckbox';
					
					$limit = ( int ) $this->_request->getPost ( 'limit' );
					$offset = ( int ) $this->_request->getPost ( 'offset' );
					$loading = $this->_request->getPost ( 'loading' );
					
					$limit = ($limit == '') ? 19 : $limit + 1;
					$offset = ($offset == '') ? 0 : $offset;
					
					$row = $league_obj->getfollowingleaguetypes ( $this->_userid, $limit + 1, $offset );
					
					$offset = $offset + $limit;
					$data ['totaluser'] = count ( $row );
					$data ['offsetFol'] = $offset;
					
					// code for separate rows
					/*
					 * $allinsearchlist 	= 	$this->myclientdetails->getAllMasterfromtable('tblCustomDashboard',array('cus_ids'),array('cus_userid'=>$this->_userid,'cus_filtertype'=>'searchUsers'));
					 *
					 * if($allinsearchlist['0']['cus_ids'])
					 * {
					 * $poArray = explode(',',$allinsearchlist['0']['cus_ids']);
					 * $poArray = array_unique($poArray);
					 * }
					 *
					 * if(count($row)>0)
					 * {
					 * $allnewids = array();
					 * foreach ($row as $key => $value) {
					 * if(count($poArray)>0)
					 * {
					 * if (in_array($value['UserID'], $poArray)) $allnewids[] = $value['UserID'];
					 * }
					 * }
					 *
					 * }
					 * //echo "<pre>";
					 *
					 * $allinflwlist 	= 	$this->myclientdetails->getAllMasterfromtable('tblCustomDashboard',array('cus_ids'),array('cus_userid'=>$this->_userid,'cus_filtertype'=>'Following'));
					 *
					 * if($allinsearchlist['0']['cus_ids'])
					 * {
					 * $searchCusID = array_unique( array_diff($poArray ,$allnewids) );
					 *
					 *
					 * $searId = implode(',', $searchCusID);
					 * $updateSearch	= array('cus_ids'=>$searId);
					 * $update = $this->myclientdetails->updateMaster('tblCustomDashboard',$updateSearch,array('cus_filtertype'=>'searchUsers','cus_userid'=>$this->_userid));
					 *
					 * }
					 * if($allinflwlist['0']['cus_ids'])
					 * {
					 *
					 * $FoArray = explode(',',$allinflwlist['0']['cus_ids']);
					 * $FoArray = array_unique($FoArray);
					 * $followCusID = array_unique( array_merge($FoArray, $allnewids));
					 * //print_r($followCusID);print_r($allnewids); print_r($followCusID);
					 * if(count($followCusID)>0)
					 * {
					 * $searId = implode(',', $followCusID);
					 * $updateSearch	= array('cus_ids'=>$searId);
					 * $update = $this->myclientdetails->updateMaster('tblCustomDashboard',$updateSearch,array('cus_filtertype'=>'Following','cus_userid'=>$this->_userid));
					 * }
					 * }
					 */
					// exit();
					// $subBtn = '<div class="pull-right "><a id="Following" class="pull-left btn btn-black filterdashboard" href="javascript:void(0)">Post</a></div>';
				} elseif ($searchfor == 'followers') {
					$class = 'boxFollowers';
					$lableClass = 'labelCheckbox';
					
					$disFol = $league_obj->getfollowingleaguetypes ( $this->_userid );
					
					if (count ( $disFol ) > 0) {
						$allfollowids = array ();
						foreach ( $disFol as $key => $value ) {
							$allfollowids [] = $value ['UserID'];
						}
					}
					$limit = ( int ) $this->_request->getPost ( 'limit' );
					$offset = ( int ) $this->_request->getPost ( 'offset' );
					$loading = $this->_request->getPost ( 'loading' );
					$limit = ($limit == '') ? 19 : $limit + 1;
					$offset = ($offset == '') ? 0 : $offset;
					
					$row = $league_obj->getfollowersleaguetypes ( $this->_userid, $limit + 1, $offset );
					
					$offset = $offset + $limit;
					$data ['totaluser'] = count ( $row );
					$data ['offset'] = $offset;
					// $subBtn = '<div class="pull-right "><a id="Followers" class="pull-left btn btn-black filterdashboard" href="javascript:void(0)">Post</a></div>';
				}
			}
			$TotalUsers = count ( $row );
			if ($TotalUsers > 0) {
				$counter = 1;
				foreach ( $row as $value ) {
					$checked = '';
					$disabled = '';
					$infolowlist = '';
					if (count ( $addedPostcatlist ) > 0) {
						if (in_array ( $value ['UserID'], $postFollowingArray ))
							$checked = "checked=checked";
					}
					/*
					 * if(count($allfollowids)>0)
					 * {
					 * if (in_array($value['UserID'], $allfollowids)) { $disabled = "disabled=disabled"; $infolowlist ="follow"; }
					 * }
					 */
					$UserID = $value ['UserID'];
					$checkImage = new Application_Model_Commonfunctionality ();
					$userprofilepico7 = $checkImage->checkImgExist ( $value ['ProfilePic'], 'userpics', 'default-avatar.jpg' );
					// CHECK IF USER FOLLOWS PROFILE HOLDER, AND ALLOWS TO BE NOTIFIED
					$return .= '<div class="' . $class . '" ' . $title . '><label class="' . $lableClass . '"><input type="checkbox"  value="' . $value ['UserID'] . '" ' . $checked . ' ' . $disabled . '><div class="follower-box" style="width:75px" title="' . $this->myclientdetails->customDecoding ( $value ['Name'] ) . ' ' . $this->myclientdetails->customDecoding ( $value ['lname'] ) . '"><img src="'.IMGPATH.'/users/small/' . $userprofilepico7 . '" width="50" height="50" border="0" /><br /><div class="oneline" >' . $this->myclientdetails->customDecoding ( $value ['Name'] ) . ' ' . $this->myclientdetails->customDecoding ( $value ['lname'] ) . ' <br> ' . $infolowlist . '</div>';
					if ($searchfor == 'searchuser') {
						if ($value ['UserID'] != adminID)
							$return .= '<a onclick="javascript:followme(' . $value ['UserID'] . ',this);" class="btn btn-mini btn-yellow fallowina" href="javascript:void(0);"><div id="followme-label">Follow</div></a>';
					}
					
					$return .= '</div></label></div>';
					if ($counter % 7 == 0)
						
						// $return .= '<div class="next-line"></div>';
						$counter ++;
				}
				/*
				 * if($loading=='')
				 * {
				 * $return .= "<div class='next-line'></div>";
				 * $return .= "<div class='next-line'></div><div style='margin-top:15px; overflow:hidden'>";
				 * $return .= "<div style='float:right; margin-top:9px; margin-right:20px;'>".$subBtn."</div>";
				 * $return .= "</div><br style='clear:both;' />";
				 * }
				 */
			} else {
				$return .= '<div class="noFound" style="margin-top:20px; margin-bottom:20px;"> No user found</div>';
			}
			
			$data ['userlist'] = $return;
			$data ['type'] = $type;
			return $response->setBody ( Zend_Json::encode ( $data ) );
		}
	}
	public function viewallAction() {
		$data = array ();
		$user_type = $this->session_data ['usertype'];
		$response = $this->getResponse ();
		
		$activeclass_latest = '';
		$activeclass = '';
		
		$sortby = $this->_request->getPost ( 'sortby' );
		$searchMember = $this->_request->getPost ( 'searchMember' );
		$UserCount = $this->_request->getPost ( 'UserCount' );
		
		$lastId = $this->_request->getPost ( 'ID' );
		$lastId = ($lastId == '') ? '' : $lastId;
		
		$this->view->sortby = $sortby;
		$this->view->searchMember = $searchMember;
		$this->view->UserCount = $UserCount;
		$this->view->lastId = $lastId;
		$this->view->orderby = $this->_request->getPost ( 'orderby' );
		$this->view->SearchUserTextXX = $this->_request->getPost ( 'SearchUserTextXX' );
		$this->view->SearchUser = $this->_request->getPost ( 'SearchUser' );
		$this->view->IsSearchText = $this->_request->getPost ( 'IsSearchText' );
		$this->view->filtertby = $this->_request->getPost ( 'filtertby' );
		$this->view->sortbycompany = $this->_request->getPost ( 'sortbycompany' );
		
		// return $response->setBody(Zend_Json::encode($data));
	}
	public function facebooklogin() {
		$params = array (
				'appId' => facebookAppid,
				'secret' => facebookSecret,
				'domain' => facebookDomain 
		);
		$facebook = new Facebook ( $params );
		if (isset ( $_GET ['code'] )) {
			$user = $facebook->getUser ();
			if ($user) {
				$logoutUrl = $facebook->getLogoutUrl ();
				try {
					$userdata = $facebook->api ( '/me' );
					$access_token_title = 'fb_' . facebookAppid . '_access_token';
					$access_token = $_SESSION [$access_token_title];
					
					$dataArray ['access_token'] = $access_token;
					$dataArray ['facebookid'] = $userdata ['id'];
					$dataArray ['facebookname'] = $userdata ['name'];
					
					$user_personal_info ['facebook_connect_data'] = Zend_Json::encode ( $dataArray );
					$user_personal_info ['UserID'] = $this->_userid;
					$changeuserinfo = $this->User_Model->updateinfouser ( $user_personal_info );
					
					$result_array = $this->User_Model->userdetailall ( $this->_userid );
					
					$this->myclientdetails->sessionWrite($result_array[0]);

					$this->_redirect ( '/user/' . $this->myclientdetails->customDecoding ( $result_array [0] ['username'] ) );
				} catch ( FacebookApiException $e ) {
					error_log ( $e );
					$user = null;
				}
			}
		}
		return $facebook->getLoginUrl ( array (
				'scope' => 'user_posts,user_friends,user_birthday,publish_pages,publish_actions,public_profile,email,manage_pages,user_tagged_places' 
		) );
	}
	public function detailAction() {
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		$response = $this->getResponse ();
		$response->setHeader ( 'Content-type', 'application/json', true );
		if ($this->getRequest ()->isXmlHttpRequest () && $this->getRequest ()->getMethod () == 'POST') {
			$userid = ( int ) $this->_request->getPost ( 'userid' );
			$owner = ($userid == $this->_userid) ? '_owner_' : '';
			
			$request = $this->getRequest ();
			$requestasr = $this->getRequest ()->getParams ();
			// $userid= (int) $request->getPost('userid');
			$sid = $request->getPost ( 'sid' );
			$callfrom = $request->getPost ( 'callfrom' );
			$cookieuser = ( int ) $this->_userid;
			$profile = new Application_Model_Profile ();
			$follwoing = new Application_Model_Following ();
			$userdetails = $this->User_Model->ausersocialdetail ( $userid );
			
			$Totaldb = $this->Myhome_Model->usersdbeecount ( $userid );
			$dbtype = $this->Myhome_Model->getaadbtypebyuser ( $userid );
			$fcnt = $follwoing->chkfollowingcnt ( $userid, $cookieuser );
			$biographi = $profile->userbiographi ( $userid );
			
			$fellowtxt = ($fcnt ['ID'] > 0) ? 'Unfollow' : 'Follow';
			$ProfileHolder = ($cookieuser == $userid) ? true : false;
			
			$editprofilelink = ($cookieuser == $userid && $sid == 0) ? '<span username="' . $this->myclientdetails->customDecoding ( $userdetails [0] ['Username'] ) . '" id="changeusername"><i class="fa fa-pencil"></i></span>' : '';
			$contactbut = '';
			$islogin = (! empty ( $this->_userid )) ? true : false;
			$following = $follwoing->getfolloweruser ( $userid );
			$follower = $follwoing->getallfollowing ( $userid );
			$checkImage = new Application_Model_Commonfunctionality ();
			$follwoing = new Application_Model_Following ();
			$conatctfcnt = $follwoing->CheckContactCount ( $_SESSION ['Zend_Auth'] ['storage'] ['UserID'], $userdetails [0] ['UserID'] );
			
			$contacttext = ($conatctfcnt ['id'] > 0) ? 'Remove from Contacts' : 'Add to Contacts';
			
			if ($this->addtocontact == 1 && $userid != adminID && $userid != $_SESSION ['Zend_Auth'] ['storage'] ['UserID'] && false) {
				$contactbut .= '<a class="btn btn-yellow fallowina btn-mini addtoconxx" onclick="javascript:addtocontact(' . $userdetails [0] ['UserID'] . ',this,\'' . $this->myclientdetails->customDecoding ( $userdetails [0] ['Name'] ) . '\',\'' . $fellowtxt . '\');" href="javascript:void(0);">
					 <div id="contact-label" style="cursor: pointer;">' . $contacttext . '</div>
					</a>';
			}
			if ($userid == adminID) {
				if ($this->admin_searchable_frontend == 1) {
					$contactbut .= '';
				}
			}

	   		if($userdetails[0]['hidefeed']==1){
	   		$chekd = 'checked=checked';
	   		}else{$chekd ='';}
	        $userprofile = $checkImage->checkImgExist($userdetails[0]['ProfilePic'],'userpics','default-avatar.jpg');   		
	   		$data = array(); 
	   		$userimage.='<div class="bgProfile"><img src="'.IMGPATH.'/users/medium/'.$userprofile.'" width="100" height="100"/></div><div id="profileimage" class="profileImgSml" datapic-url="'.IMGPATH.'/users/medium/'.$userprofile.'" style="background-position: left top; background-image:url('.IMGPATH.'/users/'.$userprofile.'); background-repeat:no-repeat; background-size:contain;" >';

   			$utitle = $this->myclientdetails->customDecoding($userdetails[0]['title']);
   			$ucompany = $this->myclientdetails->customDecoding($userdetails[0]['company']);
			$utitlesclass = (strlen($utitle.$ucompany)>35)?'postionPostsmall':'postionPostsmall';
	   		if(!empty($userdetails[0]['title'])||!empty($userdetails[0]['company'])){	   		
	   		$title = $utitle.' - '.$ucompany;
	   		}else
	   			$title = '';

	   		$hyphen='';
	   		if($utitle!=""){
	   			$hyphen=' - ';
	   		}
	   		
	   		$jobtitle = (!empty($userdetails[0]['title'])||!empty($userdetails[0]['company']))?"<div class='bioInfoList'><div class='bioInfoHd'>Company</div><div class='bioListTxt'>Works at ".$this->myclientdetails->customDecoding($userdetails[0]["company"]).$hyphen.$this->myclientdetails->customDecoding($userdetails[0]['title'])."</div></div>":"";

	   		
			
			$facebook_connect_data = Zend_Json::decode ( $userdetails [0] ['facebook_connect_data'] );
			$twitter_connect_data = Zend_Json::decode ( $userdetails [0] ['twitter_connect_data'] );
			$linkedin_connect_data = Zend_Json::decode ( $userdetails [0] ['linkedin_connect_data'] );
			
			$socialTwitter = '';
			$socialFacebook = '';
			
			if ($userid != adminID) {
				
				$hide = 0;
				if ($userdetails [0] ['hidefeed'] == 0 || $userdetails [0] ['hidefeed'] == '') {
					$hide = 1;
				}
				
				if ((! empty ( $twitter_connect_data ) && $hide == 1 && $ProfileHolder == 0) || (! empty ( $facebook_connect_data ) && $hide == 1 && $ProfileHolder == 0) || (! empty ( $linkedin_connect_data ) && $hide == 1 && $ProfileHolder == 0)) {
					$button .= '<div class="seeMyFeedWrpPr dropDown "><a class="btn btn-mini btn-green"><i class="fa fa-share-alt"></i></a><ul class="dropDownList right">';
					$button .= '<h2 class="seeMySocialFeeds">My social feeds</h2>';
				}
				if (! empty ( $facebook_connect_data ) && $ProfileHolder == 0 && $hide == 1 && $this->socialloginabilitydetail ['Facebookstatus'] == 0) {
					$button .= '<li><a href="javascript:void(0);" class="facebookconnected mySocialIcons" username="' .  $this->myclientdetails->customDecoding($userdetails [0] ['Username']) . '" id="' . $facebook_connect_data ['facebookid'] . '" ><i class="fa dbFacebookIcon fa-lg socialIcon "></i> Facebook</a></li>';
				}
				if (! empty ( $twitter_connect_data ) && $ProfileHolder == 0 && $hide == 1 && $this->socialloginabilitydetail ['Twitterstatus'] == 0) {
					$button .= '<li><a href="javascript:void(0);" class="twitterconnected mySocialIcons" username="' . $this->myclientdetails->customDecoding($userdetails [0] ['Username']) . '" id="' . $twitter_connect_data ['screen_name'] . '" ><i class="fa dbTwitterIcon fa-lg socialIcon "></i> Twitter</a></li>';
				}
				$button .= '</ul></div>';
				// }
				// }
			}
			
			$picUploader = '';
			$button .= '</div>';
			
			if ($userdetails [0] ['SocialFB'] != "" || $userdetails [0] ['SocialTwitter'] != "" || $userdetails [0] ['SocialLinkedin'] != "") {
				if ($userid != adminID && $userid != $cookieuser) {
					$findMeOn = '<div class="findMeOn">';
					
					if ($userdetails [0] ['SocialFB'] != "") {
						$findMeOn .= '<a target="blank" href="' . $userdetails [0] ['SocialFB'] . '"><i class="fa dbFacebookIcon fa-lg socialIcon"></i></a>';
					}
					
					if ($userdetails [0] ['SocialTwitter'] != "") {
						$findMeOn .= '<a target="blank" href="' . $userdetails [0] ['SocialTwitter'] . '"><i class="fa dbTwitterIcon fa-lg socialIcon"></i></a>';
					}
					
					$findMeOn .= '<b class="">Connect <br> with me on</b></div>';
				}
			}
			
			// print_r($userdetails[0]);
			if ($userdetails [0] ['hidefeed'] == 1) {
				$hidechk = 'checked="checked"';
			} else {
				$hidechk2 = 'checked="checked"';
			}
			
			if ($userdetails [0] ['hideuser'] == 1) {
				$hideuserchk = 'checked="checked"';
			} else {
				//$hideuserchk2 = 'checked="checked"';
				$hideuserchk = '';
			}
			
			
			
			if ($userdetails [0] ['role'] != 1) {
				if ($ProfileHolder){
					$hidetextchk .= '<div class="userBtnOver"><span class="prstTitle biogrophydisplayHeading"><i class="fa fa-file-text"></i><b class="pLableHvr">Biography</b></span>';
				}else{
					$hidetextchk .= '<div class="userBtnOver"><span class="prstTitle biogrophydisplayHeading"><i class="fa fa-file-text"></i><b>Biography</b></span>';
				}
				
			}

			if ($ProfileHolder && $userdetails [0] ['role'] != 1) {
				$hidetextchk .= '<span class="prstTitle custmydashTitle"><i class="fa fa-cog fa-1x"></i> <b class="pLableHvr">Customise</b></span>';
			}
			if ($ProfileHolder && $userdetails [0] ['role'] != 1 && $this->socialloginabilitydetail ['allSocialstatus'] == 0) {
				if ($this->socialloginabilitydetail ['Facebookstatus'] == 0 || $this->socialloginabilitydetail ['Twitterstatus'] == 0 || $this->socialloginabilitydetail ['Linkedinstatus'] == 0 || $this->socialloginabilitydetail ['Gplusstatus'] == 0) {
					$hidetextchk .= '<div class="makefeedsRadio">
								<label for="makefeeds">
									<input type="checkbox" name="makefeedsprivate" id="makefeeds" ' . $hidechk . '>
									<span class="markSign"  data-positive="ON" data-negative="OFF"></span>
								</label>
									<span class="markLb ">Social feeds privacy</span>
								
							</div>';
					/*$hidetextchk .= '<div class="dashSocFeed" style="display: inline-block; vertical-align: middle; margin-top:0px;"><div class="makePrivateRadio"><div class="pull-left">
	      					
							<div class="fieldClass">
							<div class="makefeedsRadio">
								<label for="makefeeds">
									<input type="checkbox" name="makefeedsprivate" id="makefeeds" ' . $hidechk . '>
									<span class="markSign"></span>
									<span class="markLb">Make my social <br> feeds private</span>
								</label>
							</div>
							<label class="switcher notiSettingBtn hiddenfeeds" style="display:none;">
							<input type="checkbox" id="hiddenfeed" data-type="' . $this->session_data ['UserID'] . '" ' . $chekd . '>
							<div></div>
							<span class="switchOnOff">
							<span class="switchOn"></span>
							<span class="switchOff"></span>
							</span>
							</label>							
							</div></div>';*/
			}
		}
			
		if ($ProfileHolder && $userdetails [0] ['role'] != 1 && $userdetails [0] ['usertype'] == 100) {
							$hidetextchk .= '<div class="makefeedsRadio">
								<label for="makeanonymous">
									<input type="checkbox" name="makeanonymous" id="makeanonymous" ' . $hideuserchk . '>
									<span class="markSign " data-positive="On" data-negative="Off"></span>
								</label>
									<span class="markLb ">VIP <br> Anonymity <a id="aboutVIPAnonymity" href="javascript:void(0);"><i class="fa fa-question-circle"> </i></a></span>
								
							</div>';
			/*	$hidetextchk .= '<div class="dashSocFeed"><div class="makePrivateRadio"><div class="pull-left">
	      					<div class="bx bx-gray">VIP Anonymity <a id="aboutVIPAnonymity" href="javascript:void(0);">
<i class="fa fa-question-circle"> </i>
</a></div></div>
							<div class="fieldClass">
							<div class="makefeedsRadio">
                              <label><input type="radio" name="makeanonymous" id="makeanonymousYes" data="1" ' . $hideuserchk . '>On</label>
                               <label><input type="radio" name="makeanonymous" id="makeanonymousNo" data="0" ' . $hideuserchk2 . '>Off</label>
                            </div>
							<label class="switcher notiSettingBtn hiddenfeeds" style="display:none;">
							<input type="checkbox" id="hiddenuser" data-type="' . $this->session_data ['UserID'] . '" checked=checked>
							<div></div>
							<span class="switchOnOff">
							<span class="switchOn"></span>
							<span class="switchOff"></span>
							</span>
							</label>							
							</div></div>';*/
			}
			if (! $ProfileHolder) {
				$hidetextchk .= '<span class="prstTitle soclUsrIcon">' . $findMeOn . '</span>';
			}
			if ($userdetails [0] ['role'] != 1) {
				$hidetextchk .= '</div>';
			}
			
			if ($callfrom == '')
				$hidetextchk = '';
			
			if ($ProfileHolder && $sid == 0)
				$picUploader = '<div id="profilepic-edit" class="profilepic-edit pic-edit-action">
	                        <span class="picSpanIcon">
	                         <input type="file" accept="image/jpeg,image/gif,image/png"/>
	                        	<span class="fa-stack fa-lg">
	                          <i class="fa fa-circle fa-stack-2x"></i>
	                          <i class="fa fa-camera fa-stack-1x fa-inverse"></i>	                         
	                        </span>
	                        </span>
	                        <strong>Edit</strong>
	                    </div>';
			
			$usertypename = ($userdetails [0] ['typename'] != '') ? ' - ' . $userdetails [0] ['typename'] : '';
			
			/*
			 * if($userdetails[0]['SocialFB']!="" || $userdetails[0]['SocialTwitter']!="" || $userdetails[0]['SocialLinkedin']!="")
			 * {
			 * if($userid!=adminID && $userid!=$cookieuser){
			 * $findMeOn ='<div class="findMeOn">Connect with me on';
			 *
			 * if($userdetails[0]['SocialFB']!="")
			 * {
			 * $findMeOn.='<a target="blank" href="'.$userdetails[0]['SocialFB'].'"><i class="fa dbFacebookIcon fa-lg socialIcon"></i></a>';
			 * }
			 *
			 * if($userdetails[0]['SocialTwitter']!="")
			 * {
			 * $findMeOn.='<a target="blank" href="'.$userdetails[0]['SocialTwitter'].'"><i class="fa dbTwitterIcon fa-lg socialIcon"></i></a>';
			 * }
			 *
			 *
			 *
			 * if($userdetails[0]['SocialLinkedin']!="")
			 * {
			 * $findMeOn.='<a target="blank" href="'.$userdetails[0]['SocialLinkedin'].'"><i class="fa dbLinkedInIcon fa-lg socialIcon"></i></a>';
			 * }
			 *
			 * $findMeOn.='</div>';
			 * }
			 * }
			 */
			
			$userInform = $userimage . $picUploader . '

	   		</div>

	   		<div class="profileDes dbDetailsBox profileSmlImgDesc">	   		
	   		  <span class="fa fa-angle-up fa-lg profileDownAro"></span>

	   		<div class="userPrDetailsWrapper">
	   		<h2>' . $this->myclientdetails->customDecoding ( $userdetails [0] ['Name'] ) . ' ' . $this->myclientdetails->customDecoding ( $userdetails [0] ['lname'] ) . $usertypename . '   <span class="userIdOrgTxt">@' . $this->myclientdetails->customDecoding ( $userdetails [0] ['Username'] ) . '</span> </h2>
	   		<div class="' . $utitlesclass . '">' . $title . '</div>
	   		</div>  

           <div id="proCollapseMain">
	   		 <h3><span>@' . $this->myclientdetails->customDecoding ( $userdetails [0] ['Username'] ) . '</span>' . $editprofilelink . '</h3>

              

	   		<div class="dbDetailsTopBar">
	   		<div class="crSt">
	   		<i class="fa fa-pencil-square-o fa-lg"></i>
	   		<span>' . $Totaldb . '</span>';
			
			if ($Totaldb > 1)
				$postTxt = 'Posts';
			else
				$postTxt = 'Post';
			
			$userInform .=  '<b>'.$postTxt . '</b> 
	   		</div>
	   		<div class="crSt">
	   		<i class="fa fa-male fa-lg"></i>
	   		<span>' . count ( $follwoing->getfolloweruserprofile ( $userid ) ) . '</span>
	   		 <b>following</b>
	   		</div>
	   		<div class="crSt">
	   		<i class="fa fa-child fa-lg"></i> 
	   		<span>' . count ( $follwoing->getfollowing ( $userid ) ) . '</span>';
			
			if (count ( $follwoing->getfollowing ( $userid ) ) > 1)
				$folTxt = 'followers';
			else
				$folTxt = 'Follower';
			
			$userInform .=  '<b>'.$folTxt . '</b>
	   		</div>	   		  		   		
	   		</div>';
			if (! $ProfileHolder) {
				$userInform .= $button . $hidetextchk;
			}
			if ($ProfileHolder) {
				
				$userInform .= $hidetextchk . $button;
			}
			
			$userInform .= '</div></div>';
			
			$data ['userinfo'] = $userInform;
			
			$email = $this->myclientdetails->customDecoding ( $userdetails [0] ['Email'] );
			
			$Contacts = $this->myclientdetails->customDecoding ( $userdetails [0] ['Contacts'] );
			
			if ($userdetails [0] ['Emailmakeprivate'] == 0) {
				$Emailhtml = '<div class="bioInfoList">
						
						<div class="bioInfoHd">Email</div> 
						<div class="bioListTxt">' . $email . '</div>
					</div>';
			} elseif ($this->_userid == $userdetails [0] ['UserID'] && $userdetails [0] ['Emailmakeprivate'] == 1) {
				$emailP = '<span class="optionalText"><i class="fa fa-lock"></i> Private</span>';
				$Emailhtml = '<div class="bioInfoList" style="padding-bottom:0;">
				         <div class="bioInfoHd">Email '.$emailP .'</div> 						
						<div class="bioListTxt">' . $email . '</div>
					</div>';
			} else {
				$Emailhtml = '';
			}
			$data ['contactinfo'] = $contactinfo . $Emailhtml;
			
			if ($userdetails [0] ['Numbermakeprivate'] == 0) {
				if ($Contacts != "") {
					$numberhtml = '<div class="bioInfoList">
						
						<div class="bioInfoHd" style="margin-top:12px;">Mobile</div> 
						<div class="bioListTxt">' . $Contacts . '</div>
					</div>';
				}
			} elseif ($this->_userid == $userdetails [0] ['UserID'] && $userdetails [0] ['Numbermakeprivate'] == 1) {
				$numberP = '<span class="optionalText"><i class="fa fa-lock"></i> Private</span>';
				if ($Contacts != "") {
					$numberhtml = '<div class="bioInfoList" style="padding-bottom:0;">
					         <div class="bioInfoHd" >Mobile '. $numberP .'</div> 						
							<div class="bioListTxt">' . $Contacts . '</div>
						</div>';
				}
			} else {
				$contactinfo = '';
			}
			
			$data ['contactinfo'] .= $contactinfo . $numberhtml;
			
			if ($userdetails [0] ['Emailmakeprivate'] == 1 && $userdetails [0] ['Emailmakeprivate'] == 1 && $userdetails [0] ['Numbermakeprivate'] == 1 && $this->_userid != $userdetails [0] ['UserID'])
				$data ['contactinfo'] = '';
			
			if ($userdetails [0] ['DOBmakeprivate'] == 0 && $userdetails [0] ['Birthdate']!='0000-00-00') {
				$dob = 'Born on ' . date ( 'jS F Y', strtotime ( $userdetails [0] ['Birthdate'] ) );
				$dobHtml = '<div class="bioInfoList">	
	   						<div class="bioInfoHd">Date of Birth</div>					
						<div class="bioListTxt">' . $dob . '</div>
					</div>';
			} elseif ($this->_userid == $userdetails [0] ['UserID'] && $userdetails [0] ['DOBmakeprivate'] == 1 && $userdetails [0] ['Birthdate']!='0000-00-00') {
				$dobP = '<span class="optionalText"><i class="fa fa-lock"></i> Private</span>';
				$dob = 'Born on ' . date ( 'jS F Y', strtotime ( $userdetails [0] ['Birthdate'] ) );
				$dobHtml = '<div class="bioInfoList">
	   			        <div class="bioInfoHd">Date of Birth '.$dobP .'</div>						
						<div class="bioListTxt">' . $dob .  '</div>
					</div>';
			} else {
				$dobHtml = '';
			}
			
			if ($userdetails [0] ['City'] != '' && $userdetails [0] ['country_name'] !== '') {
				$data ['personalinfo'] = '<div class="bioInfoList">	
	   		            <div class="bioInfoHd">Location</div>					
						<div class="bioListTxt">Lives in ' . $this->myclientdetails->customDecoding ( $userdetails [0] ['country_name'] ) . '</div>
					</div>';
			}
			$data ['personalinfo'] .= $dobHtml . $jobtitle;
			
			if ($ProfileHolder) {
				
				if (count ( $dbtype ) > 0) {
					foreach ( $dbtype as $value ) :
						if ($value ['Text'])
							$dbtype_text = true;
						if ($value ['Link'])
							$dbtype_link = true;
						if ($value ['Pic'])
							$dbtype_pic = true;
						if ($value ['Vid'])
							$dbtype_vid = true;
						if ($value ['PollText'])
							$dbtype_pollText = true;
					endforeach
					;
					if ($dbtype_text == 1)
						$dbsiocn .= '<a href="#" class="posticons pstTxt"></a>';
					if ($dbtype_link == 1)
						$dbsiocn .= '<a href="#" class="posticons pstLnk"></a>';
					if ($dbtype_pic == 1)
						$dbsiocn .= '<a href="#" class="posticons pstPic"></a>';
					if ($dbtype_vid == 1)
						$dbsiocn .= '<a href="#" class="posticons pstVd"></a>';
					if ($dbtype_pollText == 1)
						$dbsiocn .= '<a href="#" class="posticons pstPll"></a>';
				} else
					$dbsiocn .= 'No post found';
				$social = 0;
				
				$socialcounter = 0;
				
				if ($this->socialloginabilitydetail ['Facebookstatus'] == 0) {
					$socialcounter = $socialcounter + 1;
				}
				if ($this->socialloginabilitydetail ['Twitterstatus'] == 0) {
					$socialcounter = $socialcounter + 1;
				}
				if ($this->socialloginabilitydetail ['Linkedinstatus'] == 0) {
					$socialcounter = $socialcounter + 1;
				}
				
				if ($socialcounter != 0) {
					$percent = 100 / $socialcounter;
				} else {
					$percent = 0;
				}
				
				$socialCount = 0;
				
				if ($this->socialloginabilitydetail ['Linkedinstatus'] == 0) {
					
					if (! empty ( $userdetails [0] ['linkedin_connect_data'] )) {
						$socialCount = $socialCount + $percent;
						$prosolink .= '<span username="' . $this->myclientdetails->customDecoding ( $userdetails [0] ['Username'] ) . '" href="javascript:void(0);"><i class="fa  dbLinkedInIcon fa-lg socialIcon"></i></span>';
					}
					if (empty ( $userdetails [0] ['linkedin_connect_data'] )) {
						$prosolink .= '<a href="/social/linkedin"><i class="fa  dbLinkedInIcon fa-lg socialIcon disabled"></i></a>';
					}
				}
				
				if ($this->socialloginabilitydetail ['Twitterstatus'] == 0) {
					
					if (! empty ( $userdetails [0] ['twitter_connect_data'] )) {
						$socialCount = $socialCount + $percent;
						$prosolink .= '<span  username="' . $this->myclientdetails->customDecoding ( $userdetails [0] ['Username'] ) . '" href="javascript:void(0);" ><i class="fa dbTwitterIcon fa-lg socialIcon"></i></span>';
					}
					if (empty ( $userdetails [0] ['twitter_connect_data'] )) {
						$prosolink .= '<a href="/twitter"><i class="fa dbTwitterIcon fa-lg socialIcon disabled"></i></a>';
					}
				}
				
				if ($this->socialloginabilitydetail ['Facebookstatus'] == 0) {
					
					if (! empty ( $userdetails [0] ['facebook_connect_data'] )) {
						$socialCount = $socialCount + $percent;
						$prosolink .= '<span id="" username="' . $this->myclientdetails->customDecoding ( $userdetails [0] ['Username'] ) . '" href="javascript:void(0);" ><i class="fa dbFacebookIcon fa-lg socialIcon "></i></span>';
					}
					if (empty ( $userdetails [0] ['facebook_connect_data'] )) {
						
						$prosolink .= '<a href="/social/facebook"><i class="fa dbFacebookIcon fa-lg socialIcon disabled"></i></a>';
					}
				}
				$socialCount = round ( $socialCount );
				$profilepersent = $this->getprofilepersent ( $userid );
				
				$data ['profilelink2'] = '<span>+' . $socialCount . '%</span>' . $prosolink;
				$data ['profilecomplete'] = '<span>+' . $profilepersent . '%</span>';
				$data ['profilelink2per'] = $socialCount;
				$data ['profilecompleteper'] = $profilepersent;
				$data ['dbicon'] = $dbsiocn;
			}
			
			$data ['profileholder'] = $ProfileHolder;
			$data ['userProfilePic'] = $userprofile;
			
			return $response->setBody ( Zend_Json::encode ( $data ) );
		} else {
		}
	}
	public function profilepersentAction() {
		$data = array ();
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		$response = $this->getResponse ();
		$response->setHeader ( 'Content-type', 'application/json', true );
		if ($this->getRequest ()->isXmlHttpRequest () && $this->getRequest ()->getMethod () == 'POST') {
			$userid = ( int ) $this->_request->getPost ( 'userid' );
			// $ProfileHolder = $this->getprofilepersent($userid);
			$data ['profilepersent'] = $this->getprofilepersent ( $userid );
		}
		return $response->setBody ( Zend_Json::encode ( $data ) );
	}
	public function getprofilepersent($userid) {
		$userdetails = new Application_Model_DbUser ();
		$profile = new Application_Model_Profile ();
		$userdetails = $userdetails->ausersocialdetail ( $userid );
		// print_r($userdetails);
		$biographi = $profile->userbiographi ( $userid );
		$profilepersent = '20';
		
		if (! empty ( $userdetails [0] ['title'] ))
			$profilepersent = $profilepersent + 10;
		if (! empty ( $userdetails [0] ['company'] ))
			$profilepersent = $profilepersent + 10;
		if (! empty ( $userdetails [0] ['Contacts'] ))
			$profilepersent = $profilepersent + 5;
		
		if ($userdetails [0] ['ProfilePic'] != 'default-avatar.jpg' && $userdetails [0] ['ProfilePic'] != '') {
			$profilepersent = $profilepersent + 10;
		}
		
		if (! empty ( $userdetails [0] ['SocialFB'] ))
			$profilepersent = $profilepersent + 5;
		if (! empty ( $userdetails [0] ['SocialTwitter'] ))
			$profilepersent = $profilepersent + 5;
		if (! empty ( $userdetails [0] ['SocialLinkedin'] ))
			$profilepersent = $profilepersent + 5;
		
		$biographyfield = $this->myclientdetails->getfieldsfromtable ( '*', 'tbl_biofields', 'clientID', clientID, '', '', 'priority', 'DESC' );
		$userbiography = $this->myclientdetails->getfieldsfromtable ( '*', 'tblUserBiography', 'UserID', $this->_userid );
		
		$profilepersentcnt = count ( $biographyfield );
		
		$profilepersentperf = ( int ) 30 / $profilepersentcnt;
		
		if ($profilepersentcnt > 0) {
			for($i = 0; $i < $profilepersentcnt; $i ++) {
				if (! empty ( $userbiography [$i] ['field_value'] )) {
					$profilepersent = $profilepersent + $profilepersentperf;
				}
			}
		}
		
		return ( int ) $profilepersent;
	}
	public function followinguserAction() {
		$data = array ();
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		$response = $this->getResponse ();
		$response->setHeader ( 'Content-type', 'application/json', true );
		if ($this->getRequest ()->isXmlHttpRequest () && $this->getRequest ()->getMethod () == 'POST') {
			$followigslague = new Application_Model_Following ();
			$userprofile = new Application_Model_Profile ();
			$commonbynew = new Application_Model_Commonfunctionality ();
			$userID = ( int ) $this->_request->getPost ( 'userID' );
			// $user =$userprofile->getuserbyprofileid($userID);
			
			$followingdbees = $followigslague->getfollowing ( $userID, '', '', '', '', $this->session_data ['usertype'] );
			
			$NameRow = $userprofile->getuserbyprofileid ( $userID );
			$UserName = ucwords ( $NameRow ['Name'] ) . ' is being followed by';
			$userTypenalval = $commonbynew->checkuserTypetooltip ( $NameRow ['usertype'] );
			$UserNamemsg = ucwords ( $NameRow ['Name'] );
			$return .= '<div id="my-dbees" style="padding: 10px 10px 10px 10px;"><div class="user-name titleHdpad" style="margin-bottom:15px">' . $UserName . '</div><div class="next-line"></div><div>';
			$return .= '<div id="mydbcontrols"></div>';
			if (count ( $followingdbees ) > 0) {
				$counter = 1;
				foreach ( $followingdbees as $Row ) :
					$checkImage = new Application_Model_Commonfunctionality ();
					$avatar = $checkImage->checkImgExist ( $Row ['avatar'], 'userpics', 'default-avatar.jpg' );
					$return .= '<a href="/user/' . $Row ['username'] . '" target="_top"><div class="follower-box-auto"><img src="'.IMGPATH.'/users/medium/' . $avatar . '" width="90" height="90" border="0" /><br /><div style="width:120px" rel="dbTip" title="' . $userTypenalval . '">' . $Row ['name'] . '</div></div></a>';
					if ($counter % 5 == 0)
						$return .= '<div class="next-line"></div>';
					$counter ++;
				endforeach
				;
			} else {
				$return .= '<div class="noFound" align="center" style="margin-top:50px;">' . $UserNamemsg . ' is currently not followed by anyone.</div>';
			}
			$return .= '<br style="clear:both; font-zise:1px" /></div></div>';
			
			$data ['status'] = 'success';
			$data ['content'] = $return;
		}
		return $response->setBody ( Zend_Json::encode ( $data ) );
	}
	public function followeruserAction() {
		$data = array ();
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		$response = $this->getResponse ();
		$response->setHeader ( 'Content-type', 'application/json', true );
		
		if ($this->getRequest ()->isXmlHttpRequest () && $this->getRequest ()->getMethod () == 'POST') {
			$followigslague = new Application_Model_Following ();
			$userprofile = new Application_Model_Profile ();
			$userID = ( int ) $this->_request->getPost ( 'userID' );
			// $user =$userprofile->getuserbyprofileid($userID);
			
			$followingdbees = $followigslague->getfolloweruserprofile ( $userID, '', '', '', '', $this->session_data ['usertype'] );
			
			$NameRow = $userprofile->getuserbyprofileid ( $userID );
			
			$UserName = ucwords ( $NameRow ['Name'] ) . ' is following';
			$UserNamemsg = ucwords ( $NameRow ['Name'] );
			$return .= '<div id="my-dbees" style="padding: 10px 10px 10px 10px;"><div class="user-name titleHdpad" style="margin-bottom:15px">' . $UserName . '</div><div class="next-line"></div><div>';
			$return .= '<div id="mydbcontrols"></div>';
			if (count ( $followingdbees ) > 0) {
				$counter = 1;
				foreach ( $followingdbees as $Row ) :
					$checkImage = new Application_Model_Commonfunctionality ();
					$avatar1 = $checkImage->checkImgExist ( $Row ['avatar'], 'userpics', 'default-avatar.jpg' );
					$return .= '<a href="/user/' . $Row ['username'] . '" target="_top"><div class="follower-box-auto"><img src="'.IMGPATH.'/users/medium/' . $avatar1 . '" width="95" height="95" border="0" /><br /><div style="width:95px">' . $Row ['name'] . '</div></div></a>';
					if ($counter % 5 == 0)
						$return .= '<div class="next-line"></div>';
					$counter ++;
				endforeach
				;
			} else
				$return .= '<div class="noFound" align="center" style="margin-top:50px;">' . $UserNamemsg . ' is currently not following anyone.</div>';
			
			$return .= '<br style="clear:both; font-zise:1px" /></div></div>';
			
			$data ['status'] = 'success';
			$data ['content'] = $return;
		}
		return $response->setBody ( Zend_Json::encode ( $data ) );
	}
	public function checknewfollowingAction() {
		$data = array ();
		$response = $this->getResponse ();
		$response->setHeader ( 'Content-type', 'application/json', true );
		$request = $this->getRequest ();
		$checkFlag = $request->getPost ( 'checkFlag' );
		$currCount = $request->getPost ( 'currCount' );
		$cookieuser = $this->_userid;
		$users = '';
		$followignotify = new Application_Model_Following ();
		$fuser = $followignotify->getfollowerusernotify ( $cookieuser );
		foreach ( $fuser as $row ) :
			$users [] = $row ['User'];
		endforeach
		;
		if (count ( $users ) > 0) {
			$following = true;
		} else {
			$following = false;
		}
		if ($checkFlag == '')
			$checkFlag = 1;
			// $PrivateGroups='';
			// $privategroup_obj = new Application_Model_Privateuser();
			// $PrivateGroups = $privategroup_obj->getprivategroup2($this->_userid);
		if ($following) {
			if ($checkFlag == '0') // CHECK SINCE LAST LOGIN
				$PostDate = $_COOKIE ['lastloginseen'];
			elseif ($checkFlag == '1') // CHECK SINCE LAST SEEN
				$PostDate = $_COOKIE ['currloginlastseen'];
			$TotalDbees = $followignotify->follingdbnotifys ( $users, $PostDate );
			// CALCUATE TOTAL DBEES SINCE LAST LOGIN/LAST SEEN
			$TotalDbees = $TotalDbees + $currCount;
			setcookie ( 'newnotificationcount', $TotalDbees, 0, '/' );
		}
		$data ['status'] = 'success';
		$data ['TotalDbees'] = $TotalDbees;
		$this->_helper->layout->disableLayout ();
		return $response->setBody ( Zend_Json::encode ( $data ) );
	}
	public function makefollowAction() {
		$data = array ();
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		$filter = new Zend_Filter_StripTags ();
		$response = $this->getResponse ();
		$response->setHeader ( 'Content-type', 'application/json', true );
		if ($this->getRequest ()->isXmlHttpRequest () && $this->getRequest ()->getMethod () == 'POST') {
			$userID = ( int ) $this->_request->getPost ( 'user' ); // type cast into interger
			                                                 // for token validation and cross side domain validation
			$validate = $this->commonmodel_obj->authorizeToken ( $this->_request->getPost ( 'SessUser__' ), $this->_request->getPost ( 'SessId__' ), $this->_request->getPost ( 'SessName__' ), $this->_request->getPost ( 'Token__' ), $this->_request->getPost ( 'Key__' ) );
			if ($validate == false) {
				$data ['status'] = 'error';
				$data ['message'] = 'Something went wrong please try again';
			} else if ($userID != 0) {
				// get follower info
				$getUserInfo = $this->User_Model->getUserDetail ( $userID );
				// check user is VIP OR not
				/*
				 * $vip = 0;
				 * if(in_array($getUserInfo['usertype'],array('1','2','3')))
				 * {
				 * if(in_array($this->session_data['usertype'],array('1','2','3')))
				 * $vip=0;
				 * else
				 * $vip=1;
				 * }
				 */
				if ($vip == 0) {
					$chkfollowing = $this->following->chkfollowing ( $userID, $this->_userid );
					if ($chkfollowing ['ID']) {
						if ($this->following->deletefollowing ( $chkfollowing ['ID'] )) {
							$data ['message'] = 'You do not follow ' . $this->myclientdetails->customDecoding ( $getUserInfo ['Username'] ) . ' any more';
							$data ['status'] = 'success';
							$data ['types'] = 'Follow';
							$this->notification->commomInsert ( '4', '13', $this->_userid, $this->_userid, $userID ); // Insert for following activity
						} else {
							$data ['message'] = 'Something went wrong please try again';
							$data ['status'] = 'error';
						}
					} else {
						
						$data = array (
								'clientID' => clientID,
								'User' => $userID,
								'FollowedBy' => $this->_userid,
								'StartDate' => date ( 'Y-m-d H:i:s' ) 
						);
						$success = $this->following->insertfollowing ( $data );
						if ($success != '') {
							/**
							 * ************mail section****************
							 */
							$this->notification->commomInsert ( '4', '4', $this->_userid, $this->_userid, $userID ); // Insert for following activity
							$UserEmail = $getUserInfo ['Email'];
							$UserName = $getUserInfo ['Name'];
							$FollowerUserID = $this->session_data ['UserID'];
							$FollowerName = $this->session_data ['Name'];
							$Followerlname = $this->session_data ['lname'];
							$FollowerEmail = $this->session_data ['Email'];
							$FollowerProfilePic = $this->session_data ['ProfilePic'];
							
							$EmailTemplateArray = array (
									'Email' => $UserEmail,
									'FollowerUserID' => $FollowerUserID,
									'FollowerProfilePicval' => $FollowerProfilePicval,
									'FollowerName' => $FollowerName,
									'lname' => $Followerlname,
									'case' => 22 
							);
							$bodyContentmsgall = $this->dbeeComparetemplateOne ( $EmailTemplateArray );
							$data ['status'] = 'success';
							$data ['message'] = 'You now follow ' . $this->myclientdetails->customDecoding ( $getUserInfo ['Username'] );
							$data ['types'] = 'Unfollow';
						} else {
							$data ['status'] = 'error';
							$data ['message'] = 'Some thing went wrong here please try again';
						}
					}
				} else {
					$data ['message'] = $this->myclientdetails->customDecoding ( $getUserInfo ['Username'] ) . ' is vip user you can not follow him';
					$data ['status'] = 'error';
				}
			}
		} else {
			$data ['status'] = 'error';
			$data ['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody ( Zend_Json::encode ( $data ) );
	}
	public function updatehidefeedAction() {
		$data = array ();
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		$filter = new Zend_Filter_StripTags ();
		$response = $this->getResponse ();
		$response->setHeader ( 'Content-type', 'application/json', true );
		if ($this->getRequest ()->isXmlHttpRequest () && $this->getRequest ()->getMethod () == 'POST') {
			$uid = ( int ) $this->_request->getpost ( 'uid' );
			$feed = ( int ) $this->_request->getpost ( 'feed' );
			$data ['hidefeed'] = $feed;
			
			$update = $this->myclientdetails->updatedata_global ( 'tblUsers', $data, 'UserID', $uid );
			if ($update && $feed == 1)
				$data1 ['msg'] = '';
			else
				$data1 ['msg'] = '';
		} else {
			$data1 ['status'] = 'error';
			$data1 ['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody ( Zend_Json::encode ( $data1 ) );
	}
	
	public function downloadpdfuserAction() {
		/*error_reporting(E_ALL);
		ini_set('display_errors', '1');*/
			
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		$pdfName = $this->_request->getParam ( 'pdf' );
		$isfront = $this->_request->getParam ( 'isf' );
		$orgname = $this->_request->getParam ( 'orname' );
		$isid = ( int ) $this->_request->getParam ( 'id' );
		
		if ($isid) {
			$this->myclientdetails->delactivity ( $isid, $this->_userid );
			$data = array (
					'status' => '1' 
			);
			$this->myclientdetails->statupdatepdf ( $data, $isid, $this->_userid );
		}

		
		 $path = $_SERVER ['DOCUMENT_ROOT'];

		// $path=str_replace('frontend','admin',$path);
		if ($isfront == 1) {
			$filepath = $path . "/userpdf/" . $pdfName;
		} else if ($isfront == 'im') {
			$filepath = $path . "/images/dbsecurechat/" . $pdfName; // to download chat files
			$pdfName = ($orgname != '') ? $orgname : $pdfName;
		} else {
			//
			defined ( 'CLIENTFOLDER' ) or define ( 'CLIENTFOLDER', 'adminraw/knowledge_center/client_' . clientID . '/' );
			
			$dir = $this->myhome_obj->getdir($pdfName);
			
			$filepath = $path . '/' . CLIENTFOLDER . $dir . '/' . $pdfName;



		}
		
		if ($pdfName != '') {
			header ( 'Content-Type: application/octet-stream' );
			header ( 'Content-Disposition: attachment; filename="' . $pdfName . '"' );
			header ( 'Content-Length: ' . filesize ( $filepath ) );
			@readfile ( $filepath );
		} else {
			// $this->_redirect('/');
		}
		exit ();
	}
	public function sideboxpositionAction() {
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		$sidebartab = $this->_request->getParam ( 'sideBoxPosition' );
		
		$this->myclientdetails->updatedata_global ( 'tblUsers', array (
				'sidebartab' => json_encode ( $sidebartab ) 
		), 'UserID', $this->_userid );
		
		echo json_encode ( $sidebartab );
	}
	public function hidefeedAction() {
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		
		$this->myclientdetails->updatedata_global ( 'tblUsers', array (
				'hidefeed' => $this->_request->getParam ( 'type' ) 
		), 'UserID', $this->_userid );
		
		echo 1;
		exit ();
	}
	public function hideuserAction() {
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		
		$this->myclientdetails->updatedata_global ( 'tblUsers', array (
				'hideuser' => $this->_request->getParam ( 'type' ) 
		), 'UserID', $this->_userid );
		
		echo $this->_request->getParam ( 'type' );
		exit ();
	}
	public function afterlogintcAction() {
		$data = array ();
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		$filter = new Zend_Filter_StripTags ();
		$response = $this->getResponse ();
		$response->setHeader ( 'Content-type', 'application/json', true );
		if ($this->getRequest ()->isXmlHttpRequest () && $this->getRequest ()->getMethod () == 'POST') {
			$dontShowmeAgainOracle = ( int ) $this->_request->getpost ( 'dontShowmeAgainOracle' );
			if ($dontShowmeAgainOracle == 1) {
				$data ['clientID'] = clientID;
				$data ['UserId'] = $this->_userid;
				$data ['never_show_after_login_tc'] = $dontShowmeAgainOracle;
				
				$insert = $this->myclientdetails->insertdata_global ( 'tblUserMeta', $data );
			}
			
			$user_allowTC_session = new Zend_Session_Namespace ( 'User_AllowTC' );
			$user_allowTC_session->allowTC = 1;
			
			$data1 ['msg'] = '';
		} else {
			$data1 ['status'] = 'error';
			$data1 ['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody ( Zend_Json::encode ( $data1 ) );
	}
	public function removenotishareAction() {
		$data = array ();
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		$filter = new Zend_Filter_StripTags ();
		$response = $this->getResponse ();
		$response->setHeader ( 'Content-type', 'application/json', true );
		if ($this->getRequest ()->isXmlHttpRequest () && $this->getRequest ()->getMethod () == 'POST') {
			$fileid = ( int ) $this->_request->getpost ( 'fileid' );
			$actid = ( int ) $this->_request->getpost ( 'act' );
			
			$this->myclientdetails->delactivity ( $fileid, $this->_userid );
			$this->myclientdetails->deluserpdf ( $fileid, $this->_userid );
			$data1 ['status'] = 'sucess';
		} else {
			$data1 ['status'] = 'error';
			$data1 ['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody ( Zend_Json::encode ( $data1 ) );
	}

	public function updategalleryAction() {
		$data = array ();
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		$filter = new Zend_Filter_StripTags ();
		$response = $this->getResponse ();
		$response->setHeader ( 'Content-type', 'application/json', true );
		if ($this->getRequest ()->isXmlHttpRequest () && $this->getRequest ()->getMethod () == 'POST') {

			  $imgid = ( int ) $this->_request->getpost ( 'imgid' );
			  $imgtitle =  $this->_request->getpost ( 'imgtitle' );
			  $update = $this->myclientdetails->updatedata_global ( 'tblDbeePics', array('title'=>$imgtitle), 'id', $imgid );		
			
			$data1 ['status'] = 'sucess';
		} else {
			$data1 ['status'] = 'error';
			$data1 ['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody ( Zend_Json::encode ( $data1 ) );
	}

	public function imgunlinkAction()
	{                        
		$request = $this->getRequest()->getParams();
		//unlink('./imageposts/'.trim($request['serverFileName_']));
		$storeFolder 	= ABSIMGPATH."/usergallery/".$this->_userid."/"; 	
		unlink($storeFolder."/".trim($request['serverFileName_']));
		unlink($storeFolder."/medium/".trim($request['serverFileName_']));
		unlink($storeFolder."/small/".trim($request['serverFileName_']));
		$this->myclientdetails->deletedata_global('tblDbeePics','id', $request['imgid']);
		exit;
	}

	public function gallerylistAction()
	{		
		$data = array ();
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		$filter = new Zend_Filter_StripTags ();

		$response = $this->getResponse ();
		$owner = $this->_request->getPost ( 'owner' );
		$response->setHeader ( 'Content-type', 'application/json', true );
		$content="";

		$user=Base64_decode($owner); 
		$userprofile = new Application_Model_Profile();
		$users =$userprofile->getuserbyprofileid($user);
		
		
		if($user!="")
		{
			$picdata = $this->myclientdetails->passSQLquery("SELECT id, picName, title FROM tblDbeePics WHERE clientID='" . clientID . "' AND type='1'  AND     reff_key_id =" . $user." order by id desc");
			if(count($picdata) > 0)	
			{	
				foreach ($picdata as $key => $value) {
					$content.='<li style="background:url(' . IMGPATH . '/usergallery/' . $user . '/small/'.$value['picName'].')" data-name="'.$value['picName'].'" data-id="'.$value['id'].'"><div class="gaPicTitle oneline">'.$value['title'].'</div></li>';
				}
			}

			$data1 ['content'] = $content;
			$data1 ['users'] = $users;
			$data1 ['status'] = 'sucess';
		}
		else
		{
			$data1 ['content'] = '';
			$data1 ['status'] = 'Something wrong';
		}
		
		return $response->setBody ( Zend_Json::encode ( $data1 ) );
	}


	public function imguplodAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
	 		$request 		=	$this->getRequest()->getPost();
	 		
			$storeFolder 	= ABSIMGPATH."/usergallery/".$this->_userid."/";  
			if (!file_exists(ABSIMGPATH."/usergallery/".$this->_userid)) {
				    mkdir(ABSIMGPATH."/usergallery/".$this->_userid, 0777, true);
				    mkdir(ABSIMGPATH."/usergallery/".$this->_userid."/small", 0777, true);
				    mkdir(ABSIMGPATH."/usergallery/".$this->_userid."/medium", 0777, true);
				   
			} 

			
			$image_info 	= getimagesize($_FILES["file"]["tmp_name"]);
			if($image_info[0] < 1 && $image_info[1] < 1)
			{
				echo "Please use valid image to upload";
				exit;
			}
			if (!empty($_FILES)) {

				chmod($storeFolder , 0777);					
			     
				$ext 			= 	pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION); 
				$microtime 		= 	preg_replace('/(0)\.(\d+) (\d+)/', '$3$1$2', microtime());

				$picture		=	strtolower($microtime.'.'.$ext);
				

				$permission 	= 	substr(sprintf('%o', fileperms($storeFolder)), -4);

				//code 
				
				$filename 		= $storeFolder."medium/".$picture;
				$filename1 		= $storeFolder."small/".$picture;
				$image 			=   $_FILES["file"]["name"];
				$uploadedfile 	=   $_FILES['file']['tmp_name'];
				$filenamex 		= stripslashes($_FILES['file']['name']); 	
  			    $extension 		= $this->commonmodel_obj->getExtension($filenamex);
 		        $extension 		= strtolower($extension);
 		        if($extension=="jpg" || $extension=="jpeg" )
				{
					$uploadedfile = $_FILES['file']['tmp_name'];
					$src = imagecreatefromjpeg($uploadedfile);

				}
				else if($extension=="png")
				{
					$uploadedfile = $_FILES['file']['tmp_name'];
					$src = imagecreatefrompng($uploadedfile);

				}
				else 
				{
					$src = imagecreatefromgif($uploadedfile);
				}
				//code end 		



				if($permission=='0777')
		   		$copydone =	copy($_FILES['file']['tmp_name'], $storeFolder.$picture);

		   		
				//code
				if($copydone) {
			   		list($width,$height)=getimagesize($storeFolder.$picture);
			   	if($width < 400)
		   		{		   			
		   			$medium=copy($storeFolder.$picture, $filename);

		   			if($width < 100)
		   			{		   			  
		   			  $medium=copy($storeFolder.$picture, $filename1);	
		   			}
		   			else
		   			{
		   				$newwidth1=100;
						$newheight1=($height/$width)*$newwidth1;
						$tmp1=imagecreatetruecolor($newwidth1,$newheight1);
						imagecopyresampled($tmp1,$src,0,0,0,0,$newwidth1,$newheight1,$width,$height);
						imagejpeg($tmp1,$filename1,100);
						imagedestroy($src);					
						imagedestroy($tmp1);
		   			}

		   		}
		   		else
		   		{

					$newwidth=400;
					$newheight=($height/$width)*$newwidth;
					$tmp=imagecreatetruecolor($newwidth,$newheight);

					$newwidth1=100;
					$newheight1=($height/$width)*$newwidth1;
					$tmp1=imagecreatetruecolor($newwidth1,$newheight1);

					imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
					imagecopyresampled($tmp1,$src,0,0,0,0,$newwidth1,$newheight1,$width,$height);
					
					imagejpeg($tmp,$filename,100);
					imagejpeg($tmp1,$filename1,100);
					imagedestroy($src);
					imagedestroy($tmp);
					imagedestroy($tmp1);
				}
			  }
				//code end


		   		if($copydone)
		   		{
		   			$allpicData  = array(
	                    'clientID' => clientID,
	                    'type' => 1,
	                    "reff_key_id" => $this->_userid,
	                    "picName" => $picture,
	                    "isDbeeDefaultPic" => 0,
	                    "title"  => "",
	                    "description"  => ""
	                );
	        		$insertUserPics = $this->myclientdetails->insertdata_global('tblDbeePics',$allpicData);
		   			chmod($storeFolder , 0755);	
		   			chmod(ABSIMGPATH."/usergallery/".$this->_userid, 0755);
				    chmod(ABSIMGPATH."/usergallery/".$this->_userid."/small", 0755);
				    chmod(ABSIMGPATH."/usergallery/".$this->_userid."/medium", 0755);
		   			echo $picture.','.$insertUserPics;
		   		}
		   		exit;
			}
		}

		exit;

	}
}



