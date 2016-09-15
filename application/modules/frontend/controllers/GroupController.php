<?php
class GroupController extends IsController
{
	public function init()
	{
		parent::init();
		$action = $this->getRequest()->getActionName();
		if ($action != 'groupdetails' && $action != 'agroupdetails' && $action != 'groupdbees')
		{
			if ($this->session_name_space->redirection == '' && $this->_userid == '')
			{
				$this->session_name_space->redirection = $this->curPageURL();
				$this->_helper->redirector->gotosimple('index', 'index', true);
			}
		}
	}
	

	public function indexAction()
	{
		$request = $this->getRequest()->getParams();		
		$member = ($request['member'])?$request['member']:'0';
		$vip = ($request['vip'])?'1':'0';		
		$this->view->userid = (int)$this->_userid;
		$this->view->vip = (int)$vip;
		$this->view->dbeeuser = $this->_userid;
		$this->view->member =$member;
	}

	public function customdashboardgroupsAction()
	{
		$dbeecat  = new Application_Model_Myhome();
		$userid = $this->_userid;
		$request = $this->getRequest();
		$start = $this->getRequest()->getPost('start')?$this->getRequest()->getPost('start'):0;
		$end = $this->getRequest()->getPost('end')?$this->getRequest()->getPost('end'):5;

		$movedPosts = $this->getRequest()->getPost('movedposts');
		if($movedPosts=='')
		{
			$gCatids    = $this->getRequest()->getPost('cat');
			$gCatids    = trim($request->getPost('cat'),', ');
			$gCatINids  = explode(',', $gCatids);

			$groups = '';
			if(count($gCatINids)>0)
			{
				foreach ($gCatINids as $key => $value) {
					$groupsfind  = $this->myclientdetails->getfieldsfromtable(array('*'),'tblGroups','GroupType',$value);
					if($groupsfind[0]['ID']!='')
						$groups    .= $groupsfind[0]['ID'].',';
				}
			}

			if($groups!='')
			{
				$groups = trim($groups,',');
				$allgroups = $this->myclientdetails->getfieldsfromtableusingin(array('DbeeID'),'tblDbees','GroupID',$groups);
			}
			$this->view->dbeesTot = 0;
			if(count($allgroups)>0 )
			{
				$groupdbees = $dbeecat->mydbeecat($start,$end,$allgroups);

			}
			$this->view->groupdbees = $groupdbees;
			$this->view->dbeesTot = count($groupdbees);
			$this->view->start = $start;
			$this->view->startnew = $start+PAGE_NUM;
			$this->view->end = $start+PAGE_NUM;
			$this->view->dbeenotavailmsg = $request->getPost('dbeenotavailmsg');
			$response = $this->_helper->layout->disableLayout();
		}
		else
		{
			$mvpostarr  = explode(',', $movedPosts);			
			$groupdbees = $dbeecat->mydbeecat($start,$end,$mvpostarr);
			$this->view->groupdbees = $groupdbees;
			$this->view->dbeesTot = count($groupdbees);
			$this->view->start = $start;
			$this->view->startnew = $start+PAGE_NUM;
			$this->view->end = $start+PAGE_NUM;
			$this->view->dbeenotavailmsg = $request->getPost('dbeenotavailmsg');
			$response = $this->_helper->layout->disableLayout();
		}

		return $response;

	}

	public function searchgroupsAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		$filter = new Zend_Filter_StripTags();
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$allgrouptypes = $this->groupModel->getgrouptypes();
			$keyword = $filter->filter($this->_request->getPost('keyword'));
			$type    = $filter->filter($this->_request->getPost('type'));

			$content .= '<h2 id="searchgroups">Search Groups</h2><div class="clearfix"></div>			
			<div class="postTypeContent" id="passform"><div class="formRow"><div class="formField">
			<input type="text" id="groupkeyword" class="textfield" placeholder= "Keyword" value="">
			</div></div><div class="formRow last">
			<div class="formField"><select id="grouptype" class="selectbox200">
			<option value="0">All Group types</option>';

			foreach ($allgrouptypes as $Row):
			if (isset($type)) {
				if ($Row['TypeID'] == $type)
					$selected = "selected='selected'";
				else
					$selected = '';
			} else
				$selected = '';
			$content .= "<option value='" . $Row['TypeID'] . "' " . $selected . ">" . $Row['TypeName'] . "</option>";
			endforeach;

			$content .= '</select></div></div></div>
			<div id="search-results"></div>';
			$data['status']  = 'success';
			$data['content'] = $content;

		}
		return $response->setBody(Zend_Json::encode($data)); 

	}

	public function Agohelper($date)
	{
		$currentdate = date('Y-m-d h:i:s', time());
		$start_date  = new DateTime($currentdate);

		$since_start = $start_date->diff(new DateTime($date));
		if ($since_start->y > 0) {
			$ago = ($since_start->y > 1) ? $since_start->y . ' years ' : $since_start->y . ' year ';
		} else if ($since_start->m > 0) {
			$ago = ($since_start->m > 1) ? $since_start->m . ' months ' : $since_start->m . ' month ';
		} else if ($since_start->d > 0) {
			$ago = ($since_start->d > 1) ? $since_start->d . ' days ' : $since_start->d . ' day ';
		} else if ($since_start->h > 0) {
			$ago = ($since_start->h > 1) ? $since_start->h . ' hours ' : $since_start->h . ' hour ';
		} else if ($since_start->i > 0) {
			$ago = ($since_start->i) ? $since_start->i . ' minutes ' : $since_start->i . ' minute ';
		} else if ($since_start->s > 0) {
			$ago = ($since_start->s > 1) ? $since_start->s . ' seconds ' : $since_start->s . ' second ';
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
	public function searchinggroupsAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();

		$auth = new Zend_Auth_Storage_Session();
		$authdata = $auth->read();

		$filter = new Zend_Filter_StripTags();
		if ($this->_request->getPost('serchinggroup') == 'serchinggroup') {

			if ($this->_request->getPost('keyword') == 'keyword') {
				$keyword = '';
			} else {
				$keyword = $this->_request->getPost('keyword');
				$keyword=str_replace('$', '\$',addslashes($keyword));
			}

			$type = $filter->filter($this->_request->getPost('type'));

			$allgrouptypes = $this->groupModel->searchgrouptypes($keyword, $type,$authdata['usertype']);

			$return .= '<div class="maindb-wrapper-border">';
			$TotalGroups = count($allgrouptypes);
			$return .= '<div class="grptotal">Total : '.$TotalGroups.'</div>';	
			
			if ($TotalGroups > 0) {
				$counter = 1;
				foreach ($allgrouptypes as $key => $Row) {
					if($Row['GroupPrivacy']=='1')  $grpPrivacy='Open Group';
					elseif($Row['GroupPrivacy']=='2') $grpPrivacy='Private Group';
					elseif($Row['GroupPrivacy']=='3') $grpPrivacy='Request to join Group';
					elseif($Row['GroupPrivacy']=='4') $grpPrivacy='VIP Group';

					$ago         = '';
					$currenttime = date("Y-m-d H:i:s");
					$GroupDate   = $this->Agohelper($Row['GroupDate']);
					$return .= '<div class="grpsearchbg"><a href="' . BASE_URL . '/group/groupdetails/group/' . $Row['ID'] . '" target="_top" class="grpsearchbga">' . $Row['GroupName'] . '</a> <div class="grpprivacydiv"> -  ' . $grpPrivacy . '</div><br /><div style="font-size:10px; color:#999;">' . $GroupDate . '&nbsp;&nbsp;by ';
					// condition put by desh
				if ($Row['usertype']==100 && $this->session_data['usertype']!=100 && isADMIN!=1) {

					$return .= '<a href="javascript:void(0)" class="profile-deactivated" onclick="return false;">' . VIPUSER . '</a>';
				} 
				else if ($Row['hideuser']==1 && isADMIN!=1 && $Row['UserID']!=$this->session_data['UserID']) {

					$return .= '<a href="javascript:void(0)" class="profile-deactivated" onclick="return false;">' . HIDEUSER . '</a>';
				}else {

					if ($Row['Status'] == 1) {
						$return .= '<a href="/user/' . $this->myclientdetails->customDecoding($Row['Username']) . '">' . $this->myclientdetails->customDecoding($Row['Name']) . ' ' . $this->myclientdetails->customDecoding($Row['lname']) . '</a>';					
					
					} else {

						$return .= '<a href="javascript:void(0)" class="profile-deactivated" title="VIP" onclick="return false;">' . $Row['Name'] . '</a>';
					}
				}
					$return .= '</div></div>';

					if ($counter != $TotalGroups)
						//$return .= '<div style="padding:10px; padding-left:0; padding-right:0;"><div style="background-color:#E2E2E2; width:auto; height:1px;"></div></div>';
					$counter++;
				}
			} else
				$return .= '<div style="font-size:14px; color:#999">No Groups found</div>';

			$return .= '</div>';

		}
		echo $return;

	}
	public function creategroupAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->_userid)
		{			
			$socialnetworkusers = '';
			$allgrouptypes = $this->groupModel->getgrouptypes();
			$usergroup = $this->groupModel->usergroup();


			$data['allgrouptypes'] = $allgrouptypes;			
			$data['usergroup'] = $usergroup;
			$data['groupbg'] = $this->groupbg;
			$data['userid'] = $this->_userid;
			$data['adminID'] = adminID;
			$data['socialnetworkusers'] = $socialnetworkusers;
		}
		return $response->setBody(Zend_Json::encode($data));
	}

	public function invitebylinkedinAction()
	{
		
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);

		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
			$social_id   = $this->_request->getPost('social_id');
			$groupid     = $this->_request->getPost('groupid');
			$token       = $this->_request->getPost('token');
			$timestamp   = time();
			$data_insert = array();
			$data_insert['socialid'] = $social_id;
			$data_insert['groupid']  = $groupid;
			$data_insert['token']    = $token;
			$data_insert['type']     = 'linkedin';
			$data_insert['date']     = $timestamp;
			$data_insert['clientID']     = clientID;
			$this->groupModel->insertgroupsocial($data_insert);
			$data['status'] = 'success';
		}
		return $response->setBody(Zend_Json::encode($_POST));

	}
	public function getusersetAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->_userid)
		{			
			$userserid   = $this->_request->getPost('setid');

			$usergroup = $this->groupModel->allsetuser($userserid);
			$data['usergroupset'] = $usergroup;
		}
		return $response->setBody(Zend_Json::encode($data));
	}
	
	public function scriptsAction()
	{
		$request = $this->getRequest()->getParams();
		$this->_helper->layout->disableLayout();
	}

	
	public function invitefollowingAction()
	{
		$request = $this->getRequest()->getParams();
		$isvip = 0;
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$this->views->grouptypes = $this->groupModel;
		if ($this->getRequest()->getMethod() == 'POST') {
			 $storage    = new Zend_Auth_Storage_Session();  
			  $data      = $storage->read();
			if($data['usertype'] != 0){
				$isvip = 1;
			}
			
			$filter   = new Zend_Filter_StripTags();
			$from     = $filter->filter($this->_request->getPost('from'));
			$group    = $filter->filter($this->_request->getPost('group'));
			$usertype = $filter->filter($this->_request->getPost('usertype'));

			if ($this->_request->getPost('searchval') == 'searchval') {

				$searchuser = $filter->filter($this->_request->getPost('searchuser'));
				$keyword    = $filter->filter($this->_request->getPost('keyword'));
				$from       = $filter->filter($this->_request->getPost('from'));
				$group      = $filter->filter($this->_request->getPost('group'));
			}

			$return = '';
			if ($searchuser) {				
				//$keyworden = $this->myclientdetails->customEncoding($keyword);
				$allsqlquery = $this->groupModel->getusers($keyword,$this->_userid,$this->admin_searchable_frontend,$data['usertype']);
				$usertype    = 'search';
			} else {
				if ($usertype == 'followers') {
					$limit 		= (int)$this->_request->getPost('limit');
					$offset	 	= (int)$this->_request->getPost('offset');
					$loading	= $this->_request->getPost('loading');
					
					$limit 	= ($limit=='')?24:$limit;
					$offset = ($offset=='')?0:$offset;
					$allsqlquery = $this->groupModel->getfollowersgrouptypes($this->_userid,$limit,$offset,$isvip);
					$allsqlquery2 = $this->groupModel->getfollowersgrouptypes2($this->_userid,$limit,$offset,$isvip);
					$offset = $offset+$limit;
		            $loadtot = count($allsqlquery2);
					
				} elseif ($usertype == 'following') {
					$limit 		= (int)$this->_request->getPost('limit');
					$offset	 	= (int)$this->_request->getPost('offset');
					$loading	 	= $this->_request->getPost('loading');
					
					$limit 	= ($limit=='')?24:$limit;
					$offset = ($offset=='')?0:$offset;
					$allsqlquery = $this->groupModel->getfollowinggrouptypes($this->_userid,$limit,$offset,$isvip);
					$allsqlquery2 = $this->groupModel->getfollowinggrouptypes2($this->_userid,$limit,$offset,$isvip);
					$offset = $offset+$limit;
		            $loadtot = count($allsqlquery2);
				}
			}
			$TotalUsers = count($allsqlquery);
			if ($TotalUsers > 0) {
				$counter = 1;
				foreach ($allsqlquery as $key => $Row) {
					$UserID         = $Row['UserID'];
					$title = '';
					// CHECK IF USER FOLLOWS PROFILE HOLDER, AND ALLOWS TO BE NOTIFIED
					$ChkFollower    = $this->groupModel->ChkFollowerNum($this->_userid, $UserID);
					$ChkFollowerNum = count($ChkFollower);
					if ($ChkFollowerNum > 0) {
						$NotificationRow = $this->groupModel->Notification($UserID);
						$NotifyMe        = $NotificationRow['Groups'];
					} else {
						// CHECK IF USER HAS ALLOWED GROUP NOTIFICATIONS FROM PEOPLE HE DONT FOLLOW
						$NotificationRow = $this->groupModel->Notificationdont($UserID);
						$NotifyMe        = $NotificationRow['GroupsDontFollow'];
					}
					if ($from == 'directinvite') {
						$memberRes = $this->groupModel->checkgroupmem($group, $UserID);
						if (count($memberRes) > 0) {
							$inviteStatus = $memberRes[0]['Status'];
						} else
							$inviteStatus = '-1';
					}
					$inviteLabel = '';
					$BG          = '';
					$Disabled    = '';
					if ($inviteStatus == '0') {
						$inviteLabel = '<div align="center" style="color:#FF0000; margin-top:5px;">invited</div>';
						$BG          = 'style="background-color:#EAE7E7"';
						$Disabled    = 'disabled="disabled"';
						$title       = 'title="'.$this->myclientdetails->customDecoding($Row['Name']). ' ' . $this->myclientdetails->customDecoding($Row['lname']).'"';
					} elseif ($inviteStatus == '1') {
						$inviteLabel = '<div align="center" style="color:#0000FF; margin-top:5px;">member</div>';
						$BG          = 'style="background-color:#EAE7E7"';
						$Disabled    = 'disabled="disabled"';
						$title       = 'title="'.$this->myclientdetails->customDecoding($Row['Name']). ' ' . $this->myclientdetails->customDecoding($Row['lname']).'"';
					} elseif ($NotifyMe == '0') {
						$inviteLabel = '<div align="center"><div class="icon-blocked-small" style="margin-top:5px;"></div></div>';
						$BG          = 'style="background-color:#EAE7E7"';
						$Disabled    = 'disabled="disabled"';
						$title       = 'title="This user has chosen not to receive groups invitations"';
					} elseif ($NotifyMe == '1') {						
						$title       = 'title="'.$this->myclientdetails->customDecoding($Row['Name']). ' ' . $this->myclientdetails->customDecoding($Row['lname']).'"';
					}
					$disableed = '';
					if($Row['Status']==0) $disableed = 'disabled';
						
					$checkImage = new Application_Model_Commonfunctionality();
					$userprofile1 = $checkImage->checkImgExist($Row['ProfilePic'],'userpics','default-avatar.jpg');
					$title1 = 'title="'.$this->myclientdetails->customDecoding($Row['Name']). ' ' . $this->myclientdetails->customDecoding($Row['lname']).'"';
					$return .= '<div class="boxFlowers  '.$disableed.'" ' . $title1 . '><label class="labelCheckbox"><input type="checkbox" '.$disableed.'  id="inviteuser-'.$usertype.$counter . '" value="' . $Row['UserID'] . '" ' . $Disabled . '><div class="follower-box" ' . $BG . '><img src="'.IMGPATH.'/users/small/' .$userprofile1. '" width="50" height="50" border="0" /><div class="oneline">' . $this->myclientdetails->customDecoding($Row['Name']). ' ' . $this->myclientdetails->customDecoding($Row['lname']). '</div></div>' . $inviteLabel . '</label></div>';
					/*if ($counter % 7 == 0)
						$return .= '<div class="next-line"></div>';*/
					$counter++;
				}
				/*if($loading=='')
				{
					$return .= "<div class='next-line'></div><div style='margin-top:15px; overflow:hidden' id='backone'><div style='float:left; margin-top:10px;'>";
					if ($from != 'directinvite')
						$return .= "<!--<a href='javascript:void(0);' onclick='javascript:skipinvitetogroup();'>skip invite</a>-->";
					$return .= "</div>";
					
				}*/
				$return .= "</div><br style='clear:both;' />";
			} else {
				if($usertype=='search'){
						$return .= '<div align="center" style="margin-top:5px;" class="noFound"> No matching users found </div>';
				}else{
				$return .= '<div align="center" style="margin-top:30px;" class="noFound"> No '.$usertype.' found </div>';
				}
				//if ($from != 'directinvite') {
					//$return .= "<div style='margin-top:105px;' id='backtwo';><div style='float:left;'><!--<a href='javascript:void(0);' onclick='javascript:skipinvitetogroup();'>skip invite</a>--></div><div style='float:right'><a href='javascript:void(0);' onclick='javascript:backtocreategroup();'>back</a></div></div>";
				//	$return .= '<div align="center" style="margin-top:10px;" class="noFound"> No '.$usertype.' found </div>';
				//}
			}

			$total = $counter - 1;
			echo $return . '~#~' . $total. '~#~' .$loadtot. '~#~' .$offset;
		}
	}

	public function selecttoinviteAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		if ($this->getRequest()->getMethod() == 'POST') {
			if ($this->_request->getPost('users')) {
				$filter      = new Zend_Filter_StripTags();
				$users       = $this->_request->getPost('users');
				if(is_array($users)){
					$allsqlquery = $this->groupModel->getallusers($users);
					$TotalUsers  = count($allsqlquery);
					if ($TotalUsers > 0) {
						$counter = 1;
						foreach ($allsqlquery as $key => $Row) {
							$checkImage = new Application_Model_Commonfunctionality();
							$userprofile2 = $checkImage->checkImgExist($Row['ProfilePic'],'userpics','default-avatar.jpg');
							$return .= '<div id="select-invite-' . $Row['UserID'] . '" class="follower-box" title="' . $this->myclientdetails->customDecoding($Row['Name']) . ' ' . $this->myclientdetails->customDecoding($Row['lname']) . '"><img src="'.IMGPATH.'/users/small/' . $userprofile2 . '" width="50" height="50" border="0" /><div class="oneline">' . $this->myclientdetails->customDecoding($Row['Name']) . ' ' . $this->myclientdetails->customDecoding($Row['lname']) . '</div><a href="javascript:void(0);" onClick="javascript:removeuserfrominvite(' . $Row['UserID'] . ')">remove</a></div>';
							/*if ($counter % 9 == 0)
								$return .= '<div class="next-line"></div>';*/
							$counter++;
						}
					}
				} else {
					$return .= '- No users selected -';
				}
				echo $return . '~#~' . $TotalUsers;
			}
		}
	}

	public function insertdataAction()
	{
		$request = $this->getRequest()->getParams();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		if ($this->getRequest()->getMethod() == 'POST')
		{
			$validate = $this->commonmodel_obj->authorizeToken($this->_request->getPost('SessUser__'),$this->_request->getPost('SessId__'),$this->_request->getPost('SessName__'),$this->_request->getPost('Token__'),$this->_request->getPost('Key__'));
			if($validate=='false') {
				echo $validate; // go to success event of ajax and redirect to myhome/logout  action
				exit;
			}
			$filter           = new Zend_Filter_StripTags();
			$groupname        = $request['groupname'] ? $filter->filter(stripslashes($request['groupname'])) : '0';
			$grouptype        = $request['grouptype'] ? $filter->filter(stripslashes($request['grouptype'])) : '0';
			$restgroupdb      = $request['restgroupdb'] ? $filter->filter(stripslashes($request['restgroupdb'])) : '0';
			$invitetoexpert   = $request['invitetoexpert'] ? $filter->filter(stripslashes($request['invitetoexpert'])) : '0';
			$grouptypeother   = $request['grouptypeother'] ? $filter->filter(stripslashes($request['grouptypeother'])) : '0';
			$groupprivacy     = $request['groupprivacy'] ? $filter->filter(stripslashes($request['groupprivacy'])) : '0';
			$grouppic         = $request['grouppic'] ? $filter->filter(stripslashes($request['grouppic'])) : '0';
			$groupdesc        = $request['groupdesc'] ? $filter->filter(stripslashes($request['groupdesc'])) : '';
			$groupbgimage        = $request['groupbgimage'] ? $filter->filter(stripslashes($request['groupbgimage'])) : '';
			$GroupDate        = date('Y-m-d H:i:s');
			 
			$data = array(
					'GroupName' => $groupname,
					'GroupPic' => $grouppic,
					'GroupBgPic' => $groupbgimage,
					'GroupDesc' => $groupdesc,
					'GroupType' => $grouptype,
					'GroupRes' => $restgroupdb,
					'Invitetoexpert' => $invitetoexpert,
					'GroupTypeOther' => $grouptypeother,
					'GroupPrivacy' => $groupprivacy,
					'GroupDate' => $GroupDate,
					'User' => $this->_userid,
					'clientID' => clientID
			);
			$insertgrouptypes = $this->groupModel->insertgroupdetail($data);
			$this->notification->commomInsert(12,34,$insertgrouptypes,$groupprivacy,$this->_userid);
			
			
			
			exit($insertgrouptypes);
			//return  $insertgrouptypes;
		}
		
	}

	public function sendgroupinviteAction()
	{
		$notification = new Application_Model_Notification();
		$data         = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		if ($this->getRequest()->getMethod() == 'POST') {
			$filter = new Zend_Filter_StripTags();
			$group  = $filter->filter($this->_request->getPost('group'));
			$users  = $filter->filter($this->_request->getPost('users'));
			$from   = $filter->filter($this->_request->getPost('from'));

			$JoinDate = date('Y-m-d H:i:s'); 
			// FETCH GROUP AND USER NAME

			$groupuserslist = $this->groupModel->groupuserdetail($group);
			 
			$GroupName  = $groupuserslist[0]['GroupName'];
			$GroupAdmin = $groupuserslist[0]['Name'];
			$GroupAdminlname = $groupuserslist[0]['lname'];
			$userarray = explode(',',$users);
				
			$return     = '';
			if (count($userarray)>0) {
				$checkuser = $this->groupModel->userdetail($userarray);
				$Res       = count($checkuser);
				if ($Res > 0) {
					foreach ($checkuser as $key => $Row) {
						// INSERT RECORD IN DATABASE
						$data     = array(
								'GroupID' => $group,
								'Owner' => $this->_userid,
								'User' => $Row['UserID'],
								'JoinDate' => $JoinDate,
								'SentBy' => 'Owner',
								'clientID' => clientID
						);
						 
						$success  = $this->groupModel->insertingroupmem($data);
						$success2 = $notification->commomInsert('12', '18', $group, $Row['UserID'], $this->_userid);
						/****for email ****/
						$EmailTemplateArray = array('fullname' => $Row['full_name'],                                
								'Email' => $Row['Email'],
								'GroupName' => $GroupName,
								'GroupAdmin' => $GroupAdmin,								
								'case'=>17);
						$bodyContentmsgall = $this->dbeeComparetemplateOne($EmailTemplateArray);
						/****for email ****/
					}
				}
			}
			if ($from == 'directinvite') {
				echo '<div style="font-size:12px; margin-top:10px; background-color:#F5F5F5; padding:10px; text-align:center;"><div>Invitation sent to all selected users.</div></div>';
			} else {
				echo '<div style="font-size:12px; margin-top:10px; background-color:#F5F5F5; padding:10px; text-align:center;"><div>Group created and invitation sent to all selected users (if any).</div></div>';
			}
		}

	}
	public function ajaxnotificationsAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		$filter = new Zend_Filter_StripTags();
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
			$CurrDate = date('Y-m-d H:i:s');
			setcookie('currloginlastseengroup', $CurrDate, 0, '/');
			$content .= '<h2 id="groupnotifications" class="titlePop">Group Notifications</h2><div id="group-notification-feed"></div>';
			$data['status']  = 'success';
			$data['content'] = $content;
		}
		return $response->setBody(Zend_Json::encode($data));
	}
	public function notificationsAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		$filter = new Zend_Filter_StripTags();
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
			$CurrDate = date('Y-m-d H:i:s');
			setcookie('currloginlastseengroup', $CurrDate);
			if (isset($_GET['initial']))
				$initial = $_GET['initial'];
			elseif (isset($_POST['initial']))
			$initial = $_POST['initial'];
			if ($initial == '')
				$initial = 0;
			if (isset($_GET['check']))
				$check = $_GET['check'];
			elseif (isset($_POST['check']))
			$check = $_POST['check'];
			if (isset($_GET['seemore']))
				$seemore = $_GET['seemore'];
			elseif (isset($_POST['seemore']))
			$seemore = $_POST['seemore'];
			if ($seemore == '')
				$seemore = 0;
			if (isset($_GET['start']))
				$start = $_GET['start'];
			elseif (isset($_POST['start']))
			$start = $_POST['start'];
			if ($start == '')
				$start = 0;
			if (isset($_GET['end']))
				$end = $_GET['end'];
			elseif (isset($_POST['end']))
			$end = $_POST['end'];
			if ($end == '' || $end == 'NaN')
				$end = $start + 5;
			if (isset($_GET['group']))
				$group = $_GET['group'];
			elseif (isset($_POST['group']))
			$group = $_POST['group'];
			if ($seemore == '1')
				setcookie("CookieFeedEndOnReload", $startnew, time() + 3600);
			$return = '';

			$cookieuser       = $this->_userid;
			$groupmodelaccess = new Application_Model_Groups();

			$TotalallNotifications = $groupmodelaccess->calalltotalgroupnatification($cookieuser);

			$result = $TotalallNotifications;
			$i      = 1;
			if (count($result) > 0) {
				if (isset($_GET['reload']))
					$startnew = $end;
				else
					$startnew = $start + 5;
				if (count($result) < 5) {
					$startnew     = $start;
					$seemorelabel = '- no more '.POST_NAME.'s available to show -';
				} else
					$seemorelabel = 'see more';

				foreach ($result as $key => $row) {
					$userRow = $groupmodelaccess->caltotaluser($row['User']);

					$userName = $this->myclientdetails->customDecoding($userRow[0]['Name']);
					$userPic  = $userRow[0]['ProfilePic'];
					$ownerRow = $groupmodelaccess->caltotaluser($row['Owner']);

					$ownerName = $this->myclientdetails->customDecoding($ownerRow[0]['Name']);
					$ownerPic  = $ownerRow[0]['ProfilePic'];

					$groupRow  = $groupmodelaccess->calgroup($row['GroupID']);
					$GroupName = $groupRow[0]['GroupName'];
					// FETCH GROUP NAME
					if ($row['SentBy'] == 'Owner') {
						$checkImage = new Application_Model_Commonfunctionality();
						$userprofile3 = $checkImage->checkImgExist($ownerPic,'userpics','default-avatar.jpg');
						$return .= '<div id="grpnote-' . $row['ID'] . '" class="grp-notification-feed"><div style="float:left; width:80px; height:80px;">
						<img src="'.IMGPATH.'/users/small/' . $userprofile3 . '" border="0" /></div><div style="float:left; width:425px;"><div style="font-size:12px;"><a href="profile/index/id/' . $row['Owner'] . '" target="_top"><b>' . $ownerName . '</b></a> has invited you to his groups <b>' . $GroupName . '</b></div><div style="font-size:12px; color:#666; margin-top:10px;"><a href="javascript:void(0);" onClick="javascript:respondgroupinvite(1,' . $row['ID'] . ')">Accept</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onClick="javascript:respondgroupinvite(0,' . $row['ID'] . ')">Reject</a></div></div><br style="clear:both; font-size:1px;" /></div>';
					} else {
						$checkImage = new Application_Model_Commonfunctionality();
						$userprofile4 = $checkImage->checkImgExist($userPic,'userpics','default-avatar.jpg');
						$return .= '<div id="grpnote-' . $row['ID'] . '" class="grp-notification-feed"><div style="float:left; width:80px; height:80px;"><img src="' . $userprofile4 . '" width="60" height="60" border="0" /></div><div style="float:left; width:425px;"><div style="font-size:12px;"><a href="profile/index/id/' . $row['User'] . '" target="_top"><b>' . $userName . '</b></a> wants to join your groups<b>' . $GroupName . '</b></div><div style="font-size:12px; color:#666; margin-top:10px;"><a href="javascript:void(0);" onClick="javascript:respondgroupinvite(1,' . $row['ID'] . ')">Accept</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onClick="javascript:respondgroupinvite(0,' . $row['ID'] . ')">Reject</a></div></div><br style="clear:both; font-size:1px;" /></div>';
					}
					$i++;
				}

			} else {
				$startnew     = $start;
				$return       = '<div align="center" class="noFound" style="margin-top:50px;">No pending notifications.</div>';
				$seemorelabel = '- no more '.POST_NAME.'s available to show -';
			}
			if (isset($_GET['reload'])) {
				$return;
			} else {

				$return;
			}
			$data['status']  = 'success';
			$data['content'] = $return;
		}
		return $response->setBody(Zend_Json::encode($data));
	}
	public function showgroupnotificationsAction()
	{
		$request = $this->getRequest()->getParams();
		setcookie("currloginlastseengroup", date('Y-m-d H:i:s'), time() + 3600);
		$this->_helper->layout->disableLayout();
		$this->view->groupmodelaccess = new Application_Model_Groups();
		$userid                       = $this->_userid;
		$this->view->userid           = $userid;
	}
	public function respondinviteAction()
	{
		$request = $this->getRequest()->getParams();
		$this->_helper->layout->disableLayout();
		$filter   = new Zend_Filter_StripTags();
		$Activity = new Application_Model_Activities();
		$sn       = 0;

		if ($request['id']) {
			 
			$valaction = $filter->filter($request['valaction']);
			$id        = $filter->filter($request['id']);
			$gid       = $filter->filter($request['groupid']);
			$gownerid  = $filter->filter($request['groupownerid']);
			$userid  = $filter->filter($request['userid']);
			$type  = $filter->filter($request['type']);
			 
			if ($valaction == '1') 
			{
				if($type==13){
					$update = $this->groupModel->upadategroupvalreq($gid,$userid,$gownerid);
				}else{
					$update = $this->groupModel->upadategroupval($id);
				}
				$delete = $this->groupModel->deletactivity($gid, $this->_userid);
				$gowner = ($gownerid!='')?$gownerid:adminID;
				$this->notification->commomInsert(20, 29, $gid, $gowner, $this->_userid);

			} elseif ($valaction == '0') 
			{
				$delete  = $this->groupModel->deletgroupval($id);
				$delete2 = $this->groupModel->deletactivity($gid, $this->_userid);
			}


			$TotalNotifications = $this->groupModel->calalltotalgroupnatification($this->_userid);

			$TotalNotifications1 = $Activity->getactivitygroupnotify(0, $this->_userid, 12);
			$sn = count($TotalNotifications1);

			if (count($TotalNotifications) > 0) {
				$more = 1;
				echo $id . '~yes' . '~' . $sn . '~'.$gid;
			} else {
				$more = 0;
				echo $id . '~no' . '~' . $sn . '~'. $gid;
			}
		}
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

	public function allgroupsAction()
	{		
		//$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{	
			$lastId='';
								
			$user       = (int)$this->_request->getpost('user');
			$loginid    = $this->_userid;
			$type    = (int)$this->_request->getpost('type');
			$memberof   = $this->_request->getpost('memberof');
			$lastId   = (int)$this->_request->getpost('lastId'); 
			
			$myclientdetails = new Application_Model_Clientdetails();
			$groupModel = new Application_Model_Groups();
			$checkImage = new Application_Model_Commonfunctionality();
			// CHECK IF PROFILE HOLDER
			if ($loginid == $user) {
				$ProfileHolder     = true;
				$ProfileHolderSend = 1;
			} else {
				$ProfileHolder     = false;
				$ProfileHolderSend = 0;
			}			
			$MemberOf = false;
			$isgroup = false;			
			if($memberof == '1'){
				$MemberOf  = true;
				$isgroup = true;
			}
			if($type == '1'){
				$isgroup = true;
			}
		
			if (isset($_GET['reload']))
				$startnew = $end;
			else
				$startnew = $start + 5;
			if (count($result) < 5) {
				$startnew     = $start;
				//$seemorelabel = '- no more groups to show -';
			} else {
				$seemorelabel = '<img src="/images/seemore.png">';
			}
						
			$result = $groupModel->allgroups($lastId);
			if($lastId=="")
			$return .= '<div id="middleWrpBox" style="margin-bottom:10px !important;"><div class="user-name titleHdpad">All Groups</div></div>';
			
			if (count($result) > 0) {
				$groupexist = false;
				foreach ($result as $row) {						
					$chk = true;
					if ($row['GroupPrivacy'] == 2 || $row['GroupPrivacy'] == 4)
						$chk = false;
					/* if ($row['GroupPrivacy'] == 4)
						$chk = false; */
					if ($row['User'] == $loginid || $memberof == 1 || $type == 1)
						$chk = true;
					/* if ($memberof == 1)
						$chk = true;
					if ($type == 1)
						$chk = true; */						
					if ($chk) {			
						$groupexist = true;
						$grouplink  = BASE_URL.'/group/groupdetails/group/' . $row['ID'];			
						// CHECK IF LOGGED IN USER IS A MEMBER OF THIS GROUP, IF REQUEST TO JOIN OR PRIVATE
						if (!$ProfileHolder) {
							if ($row['GroupPrivacy'] == '3') {
								$checkMemberre = $groupModel->checkuser($row['ID'], $loginid);
								$checkMember   = count($checkMemberre);
								if ($checkMember == 0)
									$grouplink = BASE_URL."/group/groupdetails/group/" . $row['ID'] . "";
							}			
							elseif ($row['GroupPrivacy'] == '2') {
								if ($userNum == 0)
									$grouplink = "";
							}
						}
						// CHECK IF LOGGED IN USER IS A MEMBER OF THIS GROUP, IF REQUEST TO JOIN OR PRIVATE
						// SELECT GROUP OWNER DETAILS
						$grpOwnerRow = $groupModel->caltotaluser($row['User']);
						// SELECT GROUP OWNER DETAILS
						// GROUP PRIVACY
						$group_url_image = BASE_URL_IMAGES.'/';
						if ($row['GroupPrivacy'] == '1') {
							$grpPrivacy = 'Open';
						} elseif ($row['GroupPrivacy'] == '2') {
							$grpPrivacy = 'Private';
						} elseif ($row['GroupPrivacy'] == '3') {
							$grpPrivacy = 'Request';
						} elseif ($row['GroupPrivacy'] == '4') {
							$grpPrivacy = 'VIP';
							$group_url_image = adminURL;								
						}
						// GROUP PRIVACY
						// SELECT GROUP MEMBERS
			
						$members      = '';
						$totalmembers = '';
						$Res          = $groupModel->calgroupmembers($row['ID']);			
						$TotalMembers = count($Res);			
						if ($TotalMembers > 0) {
							$totalmembers = '<div class="group-members-title"><span class="small-font">' . $TotalMembers . ($TotalMembers>1 ? ' members' : ' member') . '</span>';
							if ($TotalMembers > 12)
								$totalmembers .= ' - <span class="small-font"><a href="#">see all</a></span>';
							$totalmembers .= '</div>';
							$counter = 1;	
							
							foreach ($Res as $key => $Row) :
								if($Row['usertype']==100 && $this->session_data['usertype']!=100 && isADMIN!=1)
								{
											$members .= '<div class="follower-box-profile" title="VIP User"><a href="javascript:void(0);">
								<img src="'.IMGPATH.'/users/small/' . VIPUSERPIC . '"  width="35" height="35" border="0" /></a></div>';
								}
								else if($Row['hideuser']==1 && isADMIN!=1 && $Row['UserID']!=$this->session_data['UserID'])
								{
											$members .= '<div class="follower-box-profile" title="'.HIDEUSER.'"><a href="javascript:void(0);">
								<img src="'.IMGPATH.'/users/small/' . HIDEUSERPIC . '" width="35" height="35" border="0" /></a></div>';
								}else
								{
										  $members .= '<div class="follower-box-profile" title="' . $myclientdetails->customDecoding($Row['Name']) . '"><a href="'.BASE_URL.'/user/' . $myclientdetails->customDecoding($Row['Username']) . '">
							<img src="'.IMGPATH.'/users/small/' . $Row['ProfilePic'] . '" width="35" height="35" border="0" /></a></div>';
								}

								if ($counter % 12 == 0)
									$return .= '<div class="next-line"></div>';
								$counter++;
							endforeach;
						}			
						// SELECT GROUP MEMBERS
						$Name = $myclientdetails->customDecoding($row['Name']);
						if (!$ProfileHolder)
							$redb = '&nbsp;&nbsp;&nbsp;re'.POST_NAME.'';
						else
							$redb = '';
						$ago = '';			
						$ago .= ' ago';
						$GID .= $row['ID'] . ',';			
						$return .= '<li id="mygroup-row-' . $row['ID'] . '" class="group-feed" data-url="' . $grouplink . '">
									<div class="group-feed-left">';						
						
						if($row['GroupPrivacy']==4){
							$image_url = adminURL;								
							//$groupimg = $checkImage->checkImgExist($row['GroupPic'],'grouppics','default-avatar.jpg','admin');
							$groupimg = $row['GroupPic'];
							
						}else{							
							//$groupimg = $checkImage->checkImgExist($row['GroupPic'],'grouppics','default-avatar.jpg','');

							$groupimg = $row['GroupPic'];
							$image_url = BASE_URL_IMAGES;
						}


						$return .= '<div style="width:80px; height:80px;  background-position: center top; background-image:url('.IMGPATH.'/grouppics/medium/'.$groupimg.'); background-repeat:no-repeat; background-size:contain"></div>';			
						$return .= '</div><div class="groupFeedRight"><div id="label-group-name' . $row['ID'] . '" style="float:left;" class="medium-font-bold">' . $row['GroupName'] . '</div>';
			
						$groupcats = $row['TypeName'];
						if($groupcats!='0' && $groupcats != ''){
							if ($groupcats != -1 ) {
								$return .= '<br><div style="font-size:10px;"><b>Category:</b> ' . $groupcats . '</div>';
							}else{
								$return .= '<div style="margin-bottom:30px;"><b>Category:</b> ' . $row['GroupTypeOther'] . '</div>';
							}
						}
			
						$return .='<div style="font-size:10px; clear:both">'.$grpPrivacy.'</div>';
			
						$return .= '<div class="GroupDesc" fulldata="'.nl2br($row['GroupDesc']).'">'.$this->myclientdetails->dbSubstring(nl2br($row['GroupDesc']), 270, '<a class="seeMoreFulltextgrp lessClassww" href="javascript:void(0);"><i class="fa fa-plus-circle"></i> more</a>').'</div>';			
						$return .= '</div>';
						if ($ProfileHolder && $MemberOf) 
						{
									$return .= '<div class="gcby" style="clear:both">
							<b>Created by:</b> <a href="/profile/index/id/' . $row['User'] . '">' .$myclientdetails->customDecoding($grpOwnerRow[0]['Name']) . '</a>
							</div><div class="clearfix"></div>';
						}
						$return .= '<div class="group-members-wrapper">' . $totalmembers . $members . '</div></div></div>
            				<div class="pstBriefFt">' . $groupsettings . '</div>
							</li>';
					}			
				}

				$totalresult = $groupModel->allgroupscount($lastId);			

				 if($totalresult > count($result)){ 
                    $return.='<span class="show_more_main" id="show_more_main'.$row['ID'].'" style="cursor:pointer;">
                        <div id="'.$row['ID'].'" data-type="group" class="show_more showMoreBtn" title="Load more groups">Show more</div>
                        <span class="loding showMoreBtn" style="display: none;"><span ><i class="fa fa-spinner fa-spin fa-lg"></i></span></span></span>';                        
                }				
							
				} 			
				$GID = substr($GID, 0, -1);
				$data['content'] = $return;
				$data['end'] = $end;
				$data['name'] = $Name;
				$data['gid'] = $GID;
				
				
			
		}else{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));
	}

	
	public function groupdetailAction()
	{		
		//$data = array();
		
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{	
			
								
			$user       = (int)$this->_request->getpost('user');
			$loginid    = $this->_userid;
			$type    = (int)$this->_request->getpost('type');
			$memberof   = $this->_request->getpost('memberof');
			
			$myclientdetails = new Application_Model_Clientdetails();
			$groupModel = new Application_Model_Groups();
			$checkImage = new Application_Model_Commonfunctionality();
			// CHECK IF PROFILE HOLDER
			if ($loginid == $user) {
				$ProfileHolder     = true;
				$ProfileHolderSend = 1;
			} else {
				$ProfileHolder     = false;
				$ProfileHolderSend = 0;
			}			
			$MemberOf = false;
			$isgroup = false;			
			if($memberof == '1'){
				$MemberOf  = true;
				$isgroup = true;
			}
			if($type == '1'){
				$isgroup = true;
			}
			if ($isgroup) {
				// COLLECT ALL GROUP IDS THIS USER IS MEMBER OF
				$Groups  = array();
				$allsql    = $groupModel->collectallgroups($user, $ProfileHolderSend);			
				if(count($allsql)>0){
					foreach ($allsql as $Row) {
						$Groups[] = $Row['GroupID'];
					}
				}
				// COLLECT ALL GROUP IDS THIS USER IS MEMBER OF
			}
			if (isset($_GET['reload']))
				$startnew = $end;
			else
				$startnew = $start + 5;
			if (count($result) < 5) {
				$startnew     = $start;
				//$seemorelabel = '- no more groups to show -';
			} else {
				$seemorelabel = '<img src="/images/seemore.png">';
			}
			if($type=='1' && count($Groups)>0){			
				$result = $groupModel->collectallgroupbygrouptype($Groups);
			}
			if($MemberOf=='1' && count($Groups)>0) {
				$result = $groupModel->collectallgroupbygroupdes($Groups);
			}
			if($MemberOf=='0' && $type=='0') {
				$result = $groupModel->collectallgroupbyuserdes($user);
			}
			if (count($result) > 0) {
				$groupexist = false;
				foreach ($result as $row) {						
					$chk = true;
					if ($row['GroupPrivacy'] == 2 || $row['GroupPrivacy'] == 4)
						$chk = false;
					/* if ($row['GroupPrivacy'] == 4)
						$chk = false; */
					if ($row['User'] == $loginid || $memberof == 1 || $type == 1)
						$chk = true;
					/* if ($memberof == 1)
						$chk = true;
					if ($type == 1)
						$chk = true; */						
					if ($chk) {			
						$groupexist = true;
						$grouplink  = BASE_URL.'/group/groupdetails/group/' . $row['ID'];			
						// CHECK IF LOGGED IN USER IS A MEMBER OF THIS GROUP, IF REQUEST TO JOIN OR PRIVATE
						if (!$ProfileHolder) {
							if ($row['GroupPrivacy'] == '3') {
								$checkMemberre = $groupModel->checkuser($row['ID'], $loginid);
								$checkMember   = count($checkMemberre);
								if ($checkMember == 0)
									$grouplink = BASE_URL."/group/groupdetails/group/" . $row['ID'] . "";
							}			
							elseif ($row['GroupPrivacy'] == '2') {
								if ($userNum == 0)
									$grouplink = "";
							}
						}
						// CHECK IF LOGGED IN USER IS A MEMBER OF THIS GROUP, IF REQUEST TO JOIN OR PRIVATE
						// SELECT GROUP OWNER DETAILS
						$grpOwnerRow = $groupModel->caltotaluser($row['User']);
						// SELECT GROUP OWNER DETAILS
						// GROUP PRIVACY
						$group_url_image = BASE_URL_IMAGES.'/';
						if ($row['GroupPrivacy'] == '1') {
							$grpPrivacy = 'Open';
						} elseif ($row['GroupPrivacy'] == '2') {
							$grpPrivacy = 'Private';
						} elseif ($row['GroupPrivacy'] == '3') {
							$grpPrivacy = 'Request';
						} elseif ($row['GroupPrivacy'] == '4') {
							$grpPrivacy = 'VIP';
							$group_url_image = adminURL;								
						}
						// GROUP PRIVACY
						// SELECT GROUP MEMBERS
			
						$members      = '';
						$totalmembers = '';
						$Res          = $groupModel->calgroupmembers($row['ID']);			
						$TotalMembers = count($Res);			
						if ($TotalMembers > 0) {
							$totalmembers = '<div class="group-members-title"><span class="small-font">' . $TotalMembers . ($TotalMembers>1 ? ' members' : ' member') . '</span>';
							if ($TotalMembers > 12)
								$totalmembers .= ' - <span class="small-font"><a href="#">see all</a></span>';
							$totalmembers .= '</div>';
							$counter = 1;	
							
							foreach ($Res as $key => $Row) :
								if($Row['usertype']==100 && $this->session_data['usertype']!=100 && isADMIN!=1)
								{
											$members .= '<div class="follower-box-profile" title="VIP User"><a href="javascript:void(0);">
								<img src="'.IMGPATH.'/users/small/' . VIPUSERPIC . '"  width="35" height="35" border="0" /></a></div>';
								}
								else if($Row['hideuser']==1 && isADMIN!=1 && $Row['UserID']!=$this->session_data['UserID'])
								{
											$members .= '<div class="follower-box-profile" title="'.HIDEUSER.'"><a href="javascript:void(0);">
								<img src="'.IMGPATH.'/users/small/' . HIDEUSERPIC . '" width="35" height="35" border="0" /></a></div>';
								}else
								{
										  $members .= '<div class="follower-box-profile" title="' . $myclientdetails->customDecoding($Row['Name']) . '"><a href="'.BASE_URL.'/user/' . $myclientdetails->customDecoding($Row['Username']) . '">
							<img src="'.IMGPATH.'/users/small/' . $Row['ProfilePic'] . '" width="35" height="35" border="0" /></a></div>';
								}

								if ($counter % 12 == 0)
									$return .= '<div class="next-line"></div>';
								$counter++;
							endforeach;
						}			
						// SELECT GROUP MEMBERS
						$Name = $myclientdetails->customDecoding($row['Name']);
						if (!$ProfileHolder)
							$redb = '&nbsp;&nbsp;&nbsp;re'.POST_NAME.'';
						else
							$redb = '';
						$ago = '';			
						$ago .= ' ago';
						$GID .= $row['ID'] . ',';			
						if ($ProfileHolder && !$MemberOf)
						{		
							$groupsettings = '<a href="javascript:void(0);" class="groupmembers" group_id = "' . $row['ID'] . '"><i class="fa fa-users"></i> <span>Members</span></a>';
							$groupsettings .='<a href="javascript:void(0);" group_id = "' . $row['ID'] . '" class="editgroup" ><i class="fa fa-pencil-square-o"></i> <span>Edit</span></a>
								<a href="javascript:void(0);"  group_id = "' . $row['ID'] . '" groupname = "' . $row['GroupName'] . '" class="deletegroup" ><i class="fa fa-minus-circle"></i> <span>Delete</span></a>';
							if($this->Social_Content_Block!='block' && $this->socialloginabilitydetail['allSocialinvite']==0)
							{
								if($this->socialloginabilitydetail['Facebookinvite']==1 && $this->socialloginabilitydetail['Twitterinvite']==1 && $this->socialloginabilitydetail['Gplusinvite']==1 && $this->socialloginabilitydetail['Linkedininvite']==1){
									//echo'do nothing';
								} else{
								       	$groupsettings .= '<a href="javascript:void(0);" data-uniquevalue = "' . $row['ID'] . '" data-for="inviteToGroup" class="socialfriends"><i class="fa fa-share-alt"></i> <span>Invite social</span></a>';
								       }
						    }
						}						
						else
							$groupsettings = '<a class="deletefromgrpmem" group_id = "' . $row['ID'] . '" user = "' .$this->_userid. '" href="javascript:void(0);"><i class="fa fa-minus-circle"></i><span> Remove me from this Group</span></a>';
						
						if ($row['GroupPrivacy'] == '4') {
							$groupsettings = '<a class="deletefromgrpmem" group_id = "' . $row['ID'] . '" user = "' .$this->_userid. '" href="javascript:void(0);"> <i class="fa fa-minus-circle"></i> <span> Remove me from this Group</span></a>';
									}
			
						$return .= '<li id="mygroup-row-' . $row['ID'] . '" class="group-feed" data-url="' . $grouplink . '">
									<div class="group-feed-left">';						
						
						if($row['GroupPrivacy']==4){
							$image_url = adminURL;								
							//$groupimg = $checkImage->checkImgExist($row['GroupPic'],'grouppics','default-avatar.jpg','admin');
							$groupimg = $row['GroupPic'];
							
						}else{							
							//$groupimg = $checkImage->checkImgExist($row['GroupPic'],'grouppics','default-avatar.jpg','');

							$groupimg = $row['GroupPic'];
							$image_url = BASE_URL_IMAGES;
						}


						$return .= '<div style="width:80px; height:80px;  background-position: center top; background-image:url('.IMGPATH.'/grouppics/medium/'.$groupimg.'); background-repeat:no-repeat; background-size:contain"></div>';			
						$return .= '</div><div class="groupFeedRight"><div id="label-group-name' . $row['ID'] . '" style="float:left;" class="medium-font-bold">' . $row['GroupName'] . '</div>';
			
						$groupcats = $row['TypeName'];
						if($groupcats!='0' && $groupcats != ''){
							if ($groupcats != -1 ) {
								$return .= '<br><div style="font-size:10px;"><b>Category:</b> ' . $groupcats . '</div>';
							}else{
								$return .= '<div style="margin-bottom:30px;"><b>Category:</b> ' . $row['GroupTypeOther'] . '</div>';
							}
						}
			
						$return .='<div style="font-size:10px; clear:both">'.$grpPrivacy.'</div>';
			
						$return .= '<div class="GroupDesc" fulldata="'.nl2br($row['GroupDesc']).'">'.$this->myclientdetails->dbSubstring(nl2br($row['GroupDesc']), 270, '<a class="seeMoreFulltextgrp lessClassww" href="javascript:void(0);"><i class="fa fa-plus-circle"></i> more</a>').'</div>';			
						$return .= '</div>';
						if ($ProfileHolder && $MemberOf) 
						{
									$return .= '<div class="gcby" style="clear:both">
							<b>Created by:</b> <a href="/profile/index/id/' . $row['User'] . '">' .$myclientdetails->customDecoding($grpOwnerRow[0]['Name']) . '</a>
							</div><div class="clearfix"></div>';
						}
						$return .= '<div class="group-members-wrapper">' . $totalmembers . $members . '</div></div></div>
            				<div class="pstBriefFt">' . $groupsettings . '</div>
							</li>';
					}			
				}				
				if (!$groupexist) 
					$return = '<div align="center" class="noFound">You haven\'t created a Group yet!</div>';				
				} else {
					if (!$MemberOf) {
						if($type==1){
							$return = '<div align="center" class="noFound">You are not a member of any VIP Groups</div>';
						}else{
							if ($ProfileHolder)
								$return = '<div align="center" class="noFound">You haven\'t created a Group yet!</div>';
							else
								$return = '<div align="center" class="noFound">You haven\'t created a Group yet!</div>';
						}
				
					} else {
						if($this->type==1)
							$return = '<div align="center" class="noFound">You are not a member of any VIP Groups</div>';
						else if ($ProfileHolder)
							$return = '<div align="center" class="noFound">You are not a member of any Groups</div>';
						else
							$return = '<div align="center" class="noFound">Not a member of any Groups</div>';
					}
				}				
				$GID = substr($GID, 0, -1);
				$data['content'] = $return;
				$data['end'] = $end;
				$data['name'] = $Name;
				$data['gid'] = $GID;
				
				
			
		}else{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));
	}
	public function groupdetailsAction()
	{ 
		$redirection_groupname_space = new Zend_Session_Namespace('Group_Session');
		$redirection_session = new Zend_Session_Namespace('User_Session');
		$redirection_session->redirection = $this->curPageURL();
		$this->view->redirection_groupname_space = $redirection_groupname_space;
		$request = $this->getRequest()->getParams();
		$filter = new Zend_Filter_StripTags();
		$grouptypes  = new Application_Model_Groups();
		$leageObj = new Application_Model_League;
		$auth = new Zend_Auth_Storage_Session();
		$authdata = $auth->read();

		$this->view->loginuser = $authdata['UserID'];
		// ************* Leage Functionality starts *******
		if ($request['group'] != '' && $this->_userid != '') 
		{
			$groupleage = $leageObj->getCommentIdsofDbs($request['group'], 'group');

			$mostlove = $leageObj->getLeagereport($groupleage, 'love'); // Love leage toppers
			$mostfot  = $leageObj->getLeagereport($groupleage, 'fot'); // Love leage toppers
			$mosthate = $leageObj->getLeagereport($groupleage, 'hate'); // Love leage toppers

			$lovearr = explode('~', $mostlove);
			$fotarr  = explode('~', $mostfot);
			$hatearr = explode('~', $mosthate);

			$this->view->mostlove = $lovearr[0];
			$this->view->mostfot  = $fotarr[0];
			$this->view->mosthate = $hatearr[0];
		}
		//************* Leage ends
		// get value from url 

		$type     = $filter->filter($this->_request->getParam('type'));
		$token    = $filter->filter($this->_request->getParam('token'));
		$groupid  = (int) $filter->filter($this->_request->getParam('group'));
		$socialid = $filter->filter($this->_request->getParam('oathtoken'));
		$locktoken = $filter->filter($this->_request->getParam('locktoken'));
		$status = $filter->filter($this->_request->getParam('status'));
		$row = $grouptypes->allgroupdetailsuser($groupid);
		$grupdetais = $grouptypes->groupuserdetail($groupid);
		if ((isset($type) && !empty($type)) && (isset($token) && !empty($token)) && (isset($groupid) && !empty($groupid))) 
		{
			$redirection_groupname_space->type = $type;
			$redirection_groupname_space->token = $token;
			$redirection_groupname_space->groupid = $groupid;
			$redirection_groupname_space->socialid = $socialid;
			$redirection_groupname_space->locktoken = $locktoken;
			$redirection_groupname_space->status = $status;
			$this->view->sociallogin =true;
		}
		else if($row[0]['GroupPrivacy'] == 2 && $this->_userid ==''){
			$this->_redirect(BASE_URL);
		}else{
			$redirection_session->redirection = '';
		}

		// taking out normal user from vip group details
		if(!isADMIN)
		{
			
		 if($row[0]['GroupPrivacy'] == 4 && ($authdata['usertype'] ==0 ||  $authdata['usertype'] ==6))
		 {
		 	$this->_redirect(BASE_URL);
		 	exit;
		 }
		}
		// End of currunt section

		

		if(empty($row))
			$this->_redirect('/Myhome/redirect');

		if ($this->_userid!='')
			$this->view->memberchk = $grouptypes->allgroupdetailsuserre($groupid,$this->_userid); 
		else
			$this->view->memberchk ='';
		
		$this->view->row = $row;
		$this->view->group = $groupid;
		$this->view->loginuser = $this->_userid;
		$this->view->grouptypes  = $grupdetais;
		$this->view->user = $grupdetais[0]['UserID'];
		$this->view->dbeeuser = $this->_userid;
	}

	public function socialurllogicAction()
	{
		$data = array();
		$grouptypes = new Application_Model_Groups();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		$filter = new Zend_Filter_StripTags();
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
			$redirection_groupname_space = new Zend_Session_Namespace('Group_Session');
			//  group by socialnetwork code start
			$type = $redirection_groupname_space->type; 
			$token = $redirection_groupname_space->token;
			$groupid = $redirection_groupname_space->groupid;
			$socialid = $redirection_groupname_space->socialid;
			$status = $redirection_groupname_space->status;

			if ((isset($type) && !empty($type)) && (isset($token) && !empty($token)) && (isset($groupid) && !empty($groupid))) 
			{
				if ($this->_userid == '')
					$data['login'] = false;
				else
					$data['login'] = true;

				if ($type == 'facebook')
					$checkType = 1;
				else if ($type == 'twitter')
					$checkType = 2;
				else if ($type == 'linkedin')
					$checkType = 3;
				
				$checkgroupsocial = $grouptypes->checkgroupsocial($groupid, $type, $token, $socialid);

				if ($checkgroupsocial==0  && $locktoken=='') 
					$data['wrong'] = 1;
				else if ($checkgroupLock==0 && $locktoken!='') 
					$data['wrong'] = 1;
				else if ($this->_userid != '' && $this->session_data['Socialtype'] != $checkType) 
				{
					$data['logout'] = true;
					$data['typelogin'] = $type;
				}
				else if ($this->_userid != '' && $this->session_data['Socialtype'] == $checkType && $socialid != $this->session_data['Socialid']) 
				{
					$data['logout'] = true;
					$data['typelogin'] = $type;
				}  
				else if ($this->session_data['Socialtype'] == $checkType && $this->_userid != '' && ($socialid == $this->session_data['Socialid']) && $locktoken=='')
					$data['accept']    = true;
				else if ($this->_userid == '')
				{ 
					$data['typelogin'] = $type;
					$data['login'] = false;
				}
			}
			//  group by socia lnetwork code end
		}
		else {
			$data['status']  = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));
	}

	public function acceptgroupsocialrequestAction()
	{
		$data       = array();
		$grouptypes = new Application_Model_Groups();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$filter   = new Zend_Filter_StripTags();
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		$redirection_groupname_space = new Zend_Session_Namespace('Group_Session');
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
			$checkgrouptoken = $grouptypes->checkgroupsocialtoken($redirection_groupname_space->token);
			if ($checkgrouptoken) 
			{
				if (isset($redirection_groupname_space->token) && isset($redirection_groupname_space->groupid)) 
				{
					$grupdetais = $grouptypes->groupuserdetail($redirection_groupname_space->groupid);
					$groupinfo = array(
							'GroupID' => $redirection_groupname_space->groupid,
							'Owner' => $grupdetais[0]['UserID'],
							'User' => $this->_userid,
							'JoinDate' => date('Y-m-d H:i:s'),
							'SentBy' => 'Owner',
							'Status' => '1',
							'clientID' => clientID
					);
					$checkgroupinter = $grouptypes->insertgroup($groupinfo);
					$delgroup                              = $grouptypes->delinvitegroupsocial($redirection_groupname_space->token);
					$groupid                               = $redirection_groupname_space->groupid;
					$redirection_groupname_space->groupid  = '';
					$redirection_groupname_space->type     = '';
					$redirection_groupname_space->token    = '';
					$redirection_groupname_space->socialid = '';
					$data['redirect']                      = BASE_URL . '/group/groupdetails/group/' . $groupid;
					$data['status']                        = 'success';
				}

			} else {
				$data['status'] = 'error';
				$data['used']   = 'true';
			}
		} else {
			$data['status']  = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));
	}

	public function rejectgroupsocialrequestAction()
	{
		$data       = array();
		$grouptypes = new Application_Model_Groups();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$filter   = new Zend_Filter_StripTags();
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		$redirection_groupname_space = new Zend_Session_Namespace('Group_Session');
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
			if (isset($redirection_groupname_space->token) && isset($redirection_groupname_space->groupid))
				$delgroup = $grouptypes->delinvitegroupsocial($redirection_groupname_space->token);
			$groupid = $redirection_groupname_space->groupid;
			$redirection_groupname_space->groupid  = '';
			$redirection_groupname_space->type     = '';
			$redirection_groupname_space->token    = '';
			$redirection_groupname_space->socialid = '';
			$data['redirect'] = BASE_URL . '/group/groupdetails/group/' . $groupid;
			$data['status'] = 'success';

		} else {
			$data['status']  = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));
	}

	public function agroupdetailsAction()
	{
		$data = array();
		$request = $this->getRequest();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$filter   = new Zend_Filter_StripTags();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
			$owner = (int) $request->getPost('owner');
			$group     = (int) $request->getPost('group');
			$joinlink        = $filter->filter($request->getPost('joinlink'));
			$token           = $filter->filter($request->getPost('token'));
			 
			$follwoing                   = new Application_Model_Following();
			$this->view->myclientdetails = new Application_Model_Clientdetails();
			 
			$redirection_groupname_space = new Zend_Session_Namespace('Group_Session');
			if (isset($redirection_groupname_space->token) && !empty($redirection_groupname_space->token))
				$grupdetais       = $this->groupModel->groupuserdetail($group);
			 

			// $row               = $this->profile_obj->getuserbyprofileid($owner);
			//$bio               = $userbio->auserbiodetail($owner);
			$userdetails       = $this->User_Model->ausersocialdetail($owner);

			 
			$loggedin=true;
			if(!isset($this->_userid)){
				$loggedin=false;
			}
			$dbpage=$this->dbpage;
			if($this->_userid==$owner){
				$ProfileHolder=true;
			}else{$ProfileHolder=false;
			}

			$row = $this->groupModel->selectgroupdetails($group);

			$SentBy='Self';
			$sel_group_sta = $this->groupModel->groupstatus($group,$owner,$SentBy);
			$selgroupsta_count=count($sel_group_sta);
			 
			$requestGroup=false;
			if($this->_userid!='') {
				$groupRow = $this->groupModel->selectgroupprivacy($group);
				if($groupRow[0]['GroupPrivacy']=='3') {
					$memberRes = $this->groupModel->selectgroupmem($group,$this->_userid);
					if(count($memberRes)==0) {
						$requestGroup=true;
						$requestStatus=0;
					} else $requestStatus=1;
				}
			}

			if(!$ProfileHolder && $requestStatus==0) {
				if($selgroupsta_count==0){
					$linkstart="<a href='javascript:void(0)'  onclick='javascript:joingroupreq(".$row[0]['ID'].",".$row[0]['User'].");'>";
					$linkend="</a>";
				}
				else{
					$linkstart='';
					$linkend='';
				}
			} else {
				$linkstart='';
				$linkend='';
			}

			if($joinlink=='1') $reg="  <span id='joinbtn'><a href='javascript:void(0)' onclick='javascript:joingroupreq(".$row[0]['ID'].",".$row[0]['User'].");'>Join Group</a></span>";


			if($row[0]['GroupPrivacy']=='1')  $grpPrivacy='Open Group';
			elseif($row[0]['GroupPrivacy']=='2') $grpPrivacy='<i class="fa fa-lock"></i> Private Group';
			elseif($row[0]['GroupPrivacy']=='3') $grpPrivacy=$linkstart.'Request to join Group'.$linkend;
			elseif($row[0]['GroupPrivacy']=='4') $grpPrivacy=$linkstart.'VIP Group'.$linkend;

			$totalmembers='';
			$Res = $this->groupModel->selectgroupmembers($group);
			$TotalMembers=count($Res);
			$data['totalmem'] = $TotalMembers;
			if($TotalMembers>0) {
				$data['member']='<ul>';
				foreach($Res as $key=>$Row):
				$checkImage = new Application_Model_Commonfunctionality();
				$userprofile5 = $checkImage->checkImgExist($Row['ProfilePic'],'userpics','default-avatar.jpg');
				if($Row['usertype']==100 && $this->session_data['usertype']!=100 && isADMIN!=1){

					$data['member'].='<li><a href="javascript:void(0);">
				<img src="'.IMGPATH.'/users/small/'.VIPUSERPIC.'" width="35" height="35" border="0" />
				</a></li>';
			}else if($Row['hideuser']==1 && isADMIN!=1 && $Row['UserID']!=$this->session_data['UserID']){

					$data['member'].='<li><a href="javascript:void(0);">
				<img src="'.IMGPATH.'/users/small/'.HIDEUSERPIC.'" width="35" height="35" border="0" />
				</a></li>';
			}
			else
			{
				$prfilLink = ($this->_userid != '') ? BASE_URL.'/user/'.$this->myclientdetails->customDecoding($Row['Username']) : 'javascript:void(0)'; 
				$class = ($this->_userid != '') ? '' : 'cursor-default'; 
			 	 $data['member'].='<li><a href="'.$prfilLink.'" class="'.$class.'">
				<img src="'.IMGPATH.'/users/small/'.$userprofile5.'" width="35" height="35" border="0" />
				</a></li>';
			}
				endforeach;
				$data['member'].='</ul>';
			}else{
				 
				$data['member'].='<div class="noFound">This group currently has no members</div>';
			}



			if($row[0]['Status']==1)
			{
				if(!$ProfileHolder)
				{
					$profile_message.='<a style="cursor:pointer" onclick="javascript:opensendmessage('.$row[0]['UserID'].',\''.$fName[0].'\',this);" class="btn">message </a>';
				}
			}

			if($dbpage=='1')
			{
				$grplinkstart='<a href="group/?group/'.$group.'">'; $grplinkend='</a>';
				$maingrplink='<div class="next-line"></div><div style="margin-left:28px">'.$grplinkstart.'Back to main Group'.$grplinkend.'';
			}
			else
			{
				$grplinkstart=''; $grplinkend=''; $maingrplink='';
			}

			/*
			$ParrentId		=$row[0]['ID'];
		    $ParrentType	='1';
		    $ArticleId		=0;
		    $UserID   	  	=$row[0]['User'];
		    $ArticleType	=0;		


			$dbeeobj   = new Application_Model_Dbeedetail();
	        $recordfound=$dbeeobj->CheckInfluence($UserID,$ParrentId,$ParrentType,$ArticleId,$ArticleType,$userid);
	            
	             
	       if(count($recordfound) < 1)
	       {
	        $bulb='<i class="fa fa-lightbulb-o"></i>';
	       }
	       else
	       {
	        $bulb='<i class="fa fa-lightbulb-o" style="background-color:yellow;"></i>';
	       }
	       */

			 


			$profileofuser='<a style="cursor:pointer" href="'.BASE_URL.'/user/'.$this->myclientdetails->customDecoding($row[0]['Username']).'" class="btn btn-mini">Profile</a>';
			 
			$data['content']='<div class="dbUserPic">';
				$checkImage = new Application_Model_Commonfunctionality();
				//$userprofile6 = $checkImage->checkImgExist($row[0]['GroupPic'],'grouppics','default-avatar.jpg');
			$userprofile6 = $row[0]['GroupPic'];

			if($row[0]['GroupPic']!='default-avatar.jpg'){
			
				if($row[0]['GroupPrivacy']=='4')

					$data['content'].='<div style="width:100%; min-height:100px; background-position: center top; background-image:url('.IMGPATH.'/grouppics/medium/'.$userprofile6.'); background-repeat:no-repeat;"></div>';
				else
					$data['content'].='<div style="width:100%; min-height:100px; background-position: center top; background-image:url('.IMGPATH.'/grouppics/medium/'.$userprofile6.'); background-repeat:no-repeat;"></div>';
			}else{
				$data['content'].='<div class="grpDefaultImg" style=" background-position: center top; background-image:url('.IMGPATH.'/grouppics/medium/'.$userprofile6.'); background-repeat:no-repeat; background-size: cover;"></div>';
			}
			if($this->_userid != '')
			$data['content'].='<a href="'.BASE_URL.'/Group" class="backlink" >Back to Groups</a>';

			if($row[0]['TypeName']!='' && $row[0]['GroupType']!='-1'){ 
				$grouptype1 = ' posted in <b>'.$row[0]['TypeName'].'</b>';
			}else{
				if($row[0]['GroupTypeOther']!=0)
					$grouptype1 = ' posted in <b>'.$row[0]['GroupTypeOther'].'</b>';
				else $grouptype1 = '';
			}
			 if($loggedin)
			 	$postgroupbtn = '<div data-groupid = "'.$group.'" data-GroupType="'.$row[0]['GroupPrivacy'].'" style="font-size:14px;" class="pull-right btn btn-yellow postInGroup" is-group="isgroup">Create post in Group</div>';

			
			$data['content'].='</div>';

			$data['content'].='<div class="dbDetailsBox">
			 
			<!-- db content bar-->
			<div class="userPrDetailsWrapper">
			<h2>'.$grplinkstart.$row[0]['GroupName'].$grplinkend.$postgroupbtn.'</h2>
			<span class="grpPrivacy">'.$grpPrivacy.$grouptype1.'</span>
			<div>';
			if(strlen($row[0]['GroupDesc'])>250){
				$lesslinkghp = "<i class='fa fa-minus-circle' isshow='0'></i> less</a>";
			}else{
				$lesslinkghp = '';
			}
			if($row[0]['GroupDesc']!='') $data['content'].='<div class="GroupDesc" fulldata="'.$this->myclientdetails->dbSubstring(htmlentities($row[0]['GroupDesc']),5000,' ').'">'.$row[0]['GroupDesc'].'  <a href="javascript:void(0);" class="seeMoreFulltextgrp" isshow="1">'.$lesslinkghp.'</div>';

			if($groupcats!='')	$data['content'].='<div class="medium-font-bold">Categories: <span class="medium-font">'.$groupcats.'</span></div>';

			$data['content'].='</div></div></div></div>';


			$data['content'].='</div>';


			if(!empty($userdetails[0]['title']) && !empty($userdetails[0]['company']))
				$companydetail = '<span class="oneline jobspostDetails">'.$this->myclientdetails->customDecoding($userdetails[0]['title']).' - '.$this->myclientdetails->customDecoding($userdetails[0]['company']).'</span>';

			if(!$ProfileHolder && $this->_userid!='')
			{
				$fcnt =$follwoing->chkfollowingcnt($owner,$this->_userid);
				$fellowtxt = ($fcnt['ID']>0)?'Unfollow':'Follow';
				if($userdetails[0]['UserID']!=adminID)
				$btnfollow = '<a  class="btn btn-yellow btn-mini fallowina" followby="group" onclick="javascript:followme(\''. $userdetails[0]['UserID'].'\',this)"><div id="followme-label">'.$fellowtxt.'</div></a>
				';
			}
			$checkImage = new Application_Model_Commonfunctionality();
			$userprofile7 = $checkImage->checkImgExist($userdetails[0]['ProfilePic'],'userpics','default-avatar.jpg');
			
 			$following =  new Application_Model_Following();
			$data['detail'] = $this->view->partial('partials/postcreater.phtml', 
                array('ResultData'=>$userdetails[0],
                'Myhome_Model'=>$this->Myhome_Model,
                'following'=>$following,
                'myclientdetails'=>$this->myclientdetails,
                'userid'=>$this->_userid,'requestStatus'=>$requestStatus,'group'=>'1','grprow'=>$row[0]));

		//}



			return $response->setBody(Zend_Json::encode($data));

		}

	}

	public function groupmembersAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$this->_helper->layout->disableLayout();
			$validate = $this->commonmodel_obj->authorizeToken($this->_request->getPost('SessUser__'),$this->_request->getPost('SessId__'),$this->_request->getPost('SessName__'),$this->_request->getPost('Token__'),$this->_request->getPost('Key__'));
			if($validate=='false') {
				echo $validate; // go to success event of ajax and redirect to myhome/logout  action
				exit;
			}
			$grouptypes = $this->groupModel;
			$user       = $this->_userid;
			$group      = (int)$this->_request->getPost('group');
			$data['content'] = '<div style="padding:10px"><div class="user-name" >Group members<div style="font-size:14px;float:right"><a href="javascript:void(0);" class="pull-right btn-mini btn-yellow"  onclick="javascript:refreshInvitetogroup('.$group.');">Invite</a></div></div><div class="next-line"></div>';

			$Res = $this->groupModel->selectgrouptotaluser($group);
			$TotalUsers=count($Res);
			// $data['content'].='</div>';
			if($TotalUsers>0) {
				$counter=1;
				foreach($Res as $key => $Row):
				$checkImage = new Application_Model_Commonfunctionality();
				$userprofile8 = $checkImage->checkImgExist($Row['ProfilePic'],'userpics','default-avatar.jpg');
				$data['content'].='<div class="group_mem_box" style="border-bottom:1px dashed #CCC; padding:5px 0 5px 0;"><div style="float:left">
				<a href="'.BASE_URL.'/user/'.$this->myclientdetails->customDecoding($Row['Name']).'" target="_parent">
				<img src="'.IMGPATH.'/users/small/'.$userprofile8.'" width="50" height="50" border="0" /></a></div><div style="float:left; margin-left:20px; font-size:12px; font-weight:bold;"><a href="'.BASE_URL.'/user/'.$this->myclientdetails->customDecoding($Row['Name']).'" target="_parent">'.$this->myclientdetails->customDecoding($Row['Name']).' '.$this->myclientdetails->customDecoding($Row['lname']).'</a><br /><span style="font-size:10px; color:#999; font-weight:normal;">member since '.date('jS F Y',strtotime($Row['GroupJoinDate'])).'</span></div>
				<div class="btn btn-mini delmem" group_id="'.$group.'" name="'.$this->myclientdetails->customDecoding($Row['Name']).' '.$this->myclientdetails->customDecoding($Row['lname']).'" userid="'.$Row['UserID'].'"  style="float:right;margin-top:15px;" title="Remove this member from group">Remove from Group</div><br style="clear:both" /></div>';
				$data['content'].='<div class="next-line"></div>';
				endforeach;
				$data['content'].='<br style="clear:both" /></div>';
			}
			else $data['content'].='<div class="noFound" style="margin-top:50px; margin-bottom:50px;">This group has no members.</div>';
			$data['content'].='</div><div id="removemember-popup" class="popup_block"></div>';
			 
		}
		return $response->setBody(Zend_Json::encode($data));
	}

	public function inviteuserstogroupAction()
	{
		$this->_helper->layout->disableLayout();
		$validate = $this->commonmodel_obj->authorizeToken($this->_request->getPost('SessUser__'),$this->_request->getPost('SessId__'),$this->_request->getPost('SessName__'),$this->_request->getPost('Token__'),$this->_request->getPost('Key__'));
		if($validate=='false') {
			echo $validate; // go to success event of ajax and redirect to myhome/logout  action
			exit;
		}else
		{
			$this->view->grouptypes = $this->groupModel;
			$this->view->user       = $this->_userid;
			$this->view->group      = $this->_request->getPost('group');
		}
	}

	public function editgroupAction()
	{
		$request = $this->getRequest()->getParams();
		$this->_helper->layout->disableLayout();
		$filter                 = new Zend_Filter_StripTags();
		 
		if ($this->getRequest()->getMethod() == 'POST') {
			$grouptype = $this->groupModel->getgrouptypes();
			 
			$this->view->groupmodel = $this->groupModel;
			$this->view->user  = $this->_userid;
			$this->view->grouptype  = $grouptype;
			$this->view->group = (int) $filter->filter($request['group']);
		}
	}

	public function removegroupmemberAction()
	{		
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
			$filter = new Zend_Filter_StripTags();
				$user   = (int)$this->_request->getPost('user');
				$group  = (int)$this->_request->getPost('group');
				$this->groupModel->deletememgroup($group, $user);
			$data['status']  = 'success';
			$data['content'] = $this->_userid;
		
		}
		return $response->setBody(Zend_Json::encode($data));
		

	}
	public function deletegroupAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);

		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
			$this->view->user = $this->_userid;
			$group            = (int) $this->_request->getPost('group');
			$Row              = $this->groupModel->selectgroupprivacy($group);
			$GroupName        = $Row[0]['GroupName'];
			$content .= 'Are you sure you want to delete<br /><span style="color:#faa80b">'.$Row[0]['GroupName'].'</span> group?
			<br />This will delete all posts within it.';
			$data['status']  = 'success';
			$data['content'] = $content;

		}
		return $response->setBody(Zend_Json::encode($data));
	}


	public function groupdeleteAction() //not in use
	{
		$request = $this->getRequest()->getParams();
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
			$group = (int) $this->_request->getPost('group');
			if ($group) {
				//$this->groupModel->deleteallgrouprecord($group,$this->_userid); //** DELETE GROUP all recorde
				$this->groupModel->deletegroup($group, $this->_userid); //** DELETE GROUP
				$this->groupModel->deletedbeesgroup($group, $this->_userid); //** DELETE ALL DBEES FROM THIS GROUP
				$this->groupModel->deleteallmemgroup($group, $this->_userid); //** DELETE ALL MEMBERS FROM THIS GROUP
				echo '1~#~' . $group;
			}
		}
	}

	public function editgroupreAction()
	{
		if ($this->getRequest()->getMethod() == 'POST') {
			$request            = $this->getRequest()->getParams();
			$groupname          = stripslashes($request['groupname']);
			$grouptype          = stripslashes($request['grouptype']);
			$grouptypeother     = stripslashes($request['grouptypeother']);
			$groupprivacy       = stripslashes($request['groupprivacy']);
			$grouppic           = stripslashes($request['grouppic']);
			$grouppicbg           = stripslashes($request['grouppicbg']);
			$groupdesc          = stripslashes($request['groupdesc']);
			$group              = stripslashes($request['group']);
			$GroupRes           = stripslashes($request['GroupRes']);
			$Invitetoexpert     = stripslashes($request['invitetoexpert']);
			$cookieuser         = intval($userid);
			$GroupDate          = date('Y-m-d H:i:s');
			$this->view->userid = $this->_userid;
			 
			$data               = array(
					'GroupName' => $groupname,
					'GroupPic' => $grouppic,
					'GroupBgPic' => $grouppicbg,
					'GroupDesc' => $groupdesc,
					'GroupType' => $grouptype,
					'GroupTypeOther' => $grouptypeother,
					'GroupPrivacy' => $groupprivacy,
					'GroupDate' => $GroupDate,
					'User' => $this->_userid,
					'GroupRes' => $GroupRes,
					'Invitetoexpert' => $Invitetoexpert,
					'ID' => $group
			);
			$this->groupModel->updategroupdetail($data);
		}
	}
	public function groupdbeesAction()
	{
		$CurrDate=date('Y-m-d H:i:s');
		setcookie("CookieLastActivity", $CurrDate, 0, '/');
		
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$user = $this->_userid;
			$initial = $this->_request->getPost('initial','0');
			$check = $this->_request->getPost('check');
			$seemore = $this->_request->getPost('seemore','0');
			$start = $this->_request->getPost('start',0);
			$group = $this->_request->getPost('group');
			if($seemore=='1') setcookie("CookieFeedEndOnReload", $startnew, time()+3600);
			$return='';
			
			$end = $this->_request->getPost('end');
			if($end=='' || $end=='NaN') $end=$start+5;
			
			if($user) $cookieuser=$this->_userid; else $cookieuser=0;
			
			$commonbynew 	=	new Application_Model_Commonfunctionality();
			
			$privateGrp=false;
			$groupmodel  = new Application_Model_Groups();
			$grouptyperow = $groupmodel->allgroupdetailsuser($group);
			
			// CHECK IF PRIVATE GROUP
			if($grouptyperow[0]['GroupPrivacy']=='2') {
				// CHECK IF USER IS GROUP OWNER
				$GroupOwnerID=$grouptyperow[0]['User'];
				if($GroupOwnerID==$cookieuser){$GroupOwner=true;}else{$GroupOwner=false;}
			
				if(!$GroupOwner) {
					$MemberChk = $groupmodel->allgroupdetailsuserre($group,$cookieuser);
					$MemberNum=count($MemberChk);
					if($MemberNum==0) {
						$privateGrp=true;
					}
				}
			}
			
			if(!$privateGrp) {
				$TotalDbeesval = $groupmodel->caltotaldbee($group); // CALCUATE TOTAL DBEES IN DATABASE
				$TotalDbees = count($TotalDbeesval);
				$result = $groupmodel->caltotaldbresult($group,$start);
				$resultcnt = $groupmodel->caltotaldbresult($group,$start);
				$i=1;			
				if(count($result)>0){
					if(count($resultcnt>PAGE_NUM)){			
					$startnew=$start+PAGE_NUM;	
					}		
					$IDs='';
					foreach($result as $key => $row) {
						$return.=$commonbynew->displayLayoutDbs($row,$this->Social_Content_Block,'all',$this->plateform_scoring,$this->adminpostscore);
						$i++;
						$dbeeList[] = $row['DbeeID'];
					}			
					$dblistarry = implode(',', $dbeeList);
				}else{			
					$startnew=$start;
					$seemorelabel='';
					//$data['load']='0';
					if($start==0)$return='<li><div class="noFound">There are no '.ucwords(POST_NAME).'s in this Group.<br><br>Start a '.ucwords(POST_NAME).' now by using \'Post in this Group\' button above.</div></li>';
				}
			} // END if(!$privateGrp)
			else {
				//$seemorelabel='- no more '.POST_NAME.' to show -';
				$return='<li><div class="noFound"> <div class="surveyComplete">
            <div  align="center" id="requesttojoin" >
              <span class="fa-stack fa-lg">
                <strong class="fa fa-circle-thin fa-stack-2x"></strong>
                <strong  class="fa fa-exclamation fa-stack-1x "></strong>
              </span>
            This is a private group.<br/> Only members have access to '.POST_NAME.'\'s in this Group.</div></div><li>';
				$data['load']='0';
			}
			if(isset($_GET['reload']))
				echo $return;
			else
			{
				$return.='<div id="see-more-feeds'.$startnew.'"><div id="more-feeds-loader" style="cursor:pointer; color:#333333; text-align:center;" onclick="javascript:seemorefeeds('.$startnew.',15);">'.$seemorelabel.'</div></div>';
				//echo $return.'~#~'.$end.'~#~'.$startnew.'~#~'.$dblistarry;
				$data['content']=$return;
				$data['end']=$end;
				$data['start']=$start;
				$data['startnew']=$startnew;
				$data['dblistarry']=$dblistarry;
				//$data[total] = $TotalDbees;
			}
			
		}
		else
		{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));

		
	}
	public function insertdbAction()
	{
		$request             = $this->getRequest()->getParams();
		$this->view->group   = $request['groupval'];
		$this->view->frompop = '1';
		return;
	}
	public function feeoptiongroupAction()
	{
		$request = $this->getRequest();
		$this->_helper->layout->disableLayout();
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
			$user = (int) $request->getPost('user');
			if ($user) {
				$grpmyhome               = new Application_Model_Myhome();
				$data                    = $grpmyhome->getfeeoptiongroup($user);
				$this->view->grpOwnerRow = $data;
				$this->view->cookieuser  = $this->_userid;
				$this->view->user        = $user;
			}
		}
	}
	public function joingroupAction()
	{
		$request = $this->getRequest();
		$data    = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
			$notification = new Application_Model_Notification();
			$filter = new Zend_Filter_StripTags();
			$group  = (int) $filter->filter($request->getPost('group'));
			$owner  = (int) $filter->filter($request->getPost('owner'));
			$remind = $filter->filter($request->getPost('remind'));

			$JoinDate = date('Y-m-d H:i:s');
			$return   = '';

			$selectgroup = $this->groupModel->selectsearchgroup($group, $owner, $this->_userid);

			$userNum = count($selectgroup);

			if ($userNum == 0)
				$AddRecord = true;
			else
				$AddRecord = false;

			$groupname = $this->groupModel->grouprow($group);
			$GroupName = $groupname['GroupName'];

			$userval = new Application_Model_DbUser();

			$userdetails = $userval->userdetailall($this->_userid);

			$userName = $userdetails[0]['Name'];
            $userlname = $userdetails[0]['lname'];

			$ownedetails = $userval->userdetailall($owner);

			$ownerName = $ownedetails[0]['Name'];
            $ownerlname = $ownedetails[0]['lname'];

			$ownerEmail = $ownedetails[0]['Email'];

			if ($AddRecord || $remind == '0') {
				// INSERT RECORD IN DATABASE
				$data         = array(
						'GroupID' => $group,
						'Owner' => $owner,
						'User' => $this->_userid,
						'JoinDate' => $JoinDate,
						'SentBy' => 'Self',
						'clientID' => clientID
				);
				$success      = $this->groupModel->insertingroupmem($data);
				// INSERT RECORD IN DATABASE
				
				$notification->commomInsert('13', '19', $group, $owner, $this->_userid);

				// SEND EMAIL TO GROUP OWNER NOTIFYING OF NEW INTEREST
				/****for email ****/
				//mann.delus@gmail.com,anildbee@gmail.com,porwal.deshbandhu@gmail.com
				$EmailTemplateArray = array('Email' => $ownerEmail,
						'GroupName' => $GroupName,
						'ownerName' => $ownerName,
						'ownerlname' => $ownerlname,
						'userName' => $userName,
						'lname' => $userlname,
						'case'=>18);
				$bodyContentmsgall = $this->dbeeComparetemplateOne($EmailTemplateArray);
				/****for email ****/
			} else {

				$success = $this->groupModel->upadategroupval($JoinDate, $group, $owner, $this->_userid);
				// UPDATE RECORD IN DATABASE
				$notification->commomInsert('13', '19', $group, $owner, $this->_userid);
				// SEND REMINDER EMAIL TO GROUP OWNER
				if ($remind == '1') {
					/****for email ****/
					$EmailTemplateArray = array('Email' => $ownerEmail,
							'GroupName' => $GroupName,
							'ownerName' => $ownerName,
							'ownerlname' => $ownerlname,
							'userName' => $userName,
							'lname' => $userlname,
							'case'=>19);
					$bodyContentmsgall = $this->dbeeComparetemplateOne($EmailTemplateArray);
					/****for email ****/
				}
				// SEND REMINDER EMAIL TO GROUP OWNER
			}
			if ($success)
				$data1['success'] = '1';
			else
				$data1['success'] = '0';
		}
		return $response->setBody(Zend_Json::encode($data1));
	}



	public function socialfriendsgroupAction()
	{

		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$filter   = new Zend_Filter_StripTags();
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		$redirection_name_space                      = new Zend_Session_Namespace('User_Session');
		$redirection_name_space->callBackUrl = '/Group?invite=';
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') {
			$Text                              = '';
			$groupid                           = (int) $this->_request->getPost('groupid');
			$groupname = $this->groupModel->grouprow($groupid);
			$GroupName = $groupname['GroupName'];
			$redirection_name_space->mygroupid = $groupid;

			if($this->socialloginabilitydetail['Facebookstatus']==0)
			{
				if($this->facebook_connect_data['access_token']!='')	
					$Text .='<div class="shareAllSocials fbAllShare invitefacebookfri" groupid="' . $groupid . '" groupname="' . $GroupName . '" >
				              <div class="signwithSprite signWithSpriteFb">
				              	<i class="fa dbFacebookIcon fa-5x"></i>
				                <span>Facebook</span>
				              </div>
				            </div>';
				else
					  $Text .='<a href="/social/facebook?type=facebook&redirect=group"  class="shareAllSocials fbAllShare">
				              <div class="signwithSprite signWithSpriteFb">
				              	<i class="fa dbFacebookIcon fa-5x"></i>
				                <span>Facebook</span>
				              </div>
					            </a>';
			}

			if($this->socialloginabilitydetail['Twitterstatus']==0)
		    {
				if($this->twitter_connect_data['twitter_access_token']!='' || $this->twitter_connect_data['twitter_token_secret']!='')
					$Text .= '<div class="shareAllSocials twAllShare invitetwwifrigroup"  groupid="' . $groupid . '" groupname="' . $GroupName . '">
					<div class="signwithSprite signWithSpriteTwitter">
					<i class="fa dbTwitterIcon fa-5x"></i>
					<span>Twitter</span>
					</div>
					</div>';
				else
					$Text .= '<a href="/twitter" groupid="' . $groupid . '"  class="shareAllSocials twAllShare">
					<div class="signwithSprite signWithSpriteTwitter">
					<i class="fa dbTwitterIcon fa-5x"></i>
					<span>Twitter</span>
					</div>
					</a>';
			}
			if($this->socialloginabilitydetail['Linkedinstatus']==0)
		    {
				if(empty($this->session_data['linkedin_connect_data']))
					$Text .= '<a href="/social/linkedin" groupid="' . $groupid . '"  groupname="' . $GroupName . '" class="shareAllSocials lnAllShare">
					<div class="signwithSprite signWithSpriteLinkden">
					<i class="fa dbLinkedInIcon fa-5x"></i>
					<span>LinkedIn</span>
					</div>
					</a>';
				else
					$Text .= '<a ref="javascript:void(0)" class="shareAllSocials lnAllShare" groupid="' . $groupid . '" data-groupname="'.$GroupName.'" onClick="linkedinUserProfilegroup(this,' . $groupid . ')">
					<div class="signwithSprite signWithSpriteLinkden">
					<i class="fa dbLinkedInIcon fa-5x"></i>
					<span>LinkedIn</span>
					</div>
					</a>';
			}
		


			$data['content'] = $Text;
			$data['status']  = 'success';
		} else {
			$data['status']  = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));
	}

	 




	public function getdiscriptionAction(){

		return "The request for the meeting was put in after MLA from Shalimar Bagh, Bandana Kumari, was given a death threat by a BJP worker.
		We have been demanding fresh elections in Delhi since resignation from government in February 2014. In fact, we approached the Supreme Court against the lieutenant governor's decision to keep the";

	}

}