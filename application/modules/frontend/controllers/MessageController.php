<?php
class MessageController extends IsController {
	public function init() {
		parent::init ();
		$storage = new Zend_Auth_Storage_Session ();
		$auth = Zend_Auth::getInstance ();
		if ($auth->hasIdentity ()) {
			$data = $storage->read ();
			$this->_userid = $data ['UserID'];
			$this->session_data = $data;
		} else {
			$this->_helper->redirector->gotosimple ( 'index', 'index', true );
		}
		$this->notification = new Application_Model_Notification ();
		$this->myclientdetails = new Application_Model_Clientdetails ();
		$this->commonfun = new Application_Model_Commonfunctionality ();
	}
	public function indexAction() {
		$cat = new Application_Model_Category ();
		$this->view->cat = $cat->getallcategory ();
		$CurrDate = date ( 'Y-m-d H:i:s' );
		$expire = time () + 60 * 60 * 24;
		$myMessage = new Application_Model_Message ();
		setcookie ( 'currloginlastseenmsg', $CurrDate );
		$this->view->messages = $myMessage->fetchAll ();
		$this->view->user = $this->_userid;
		$this->view->msguser = json_decode ( $myMessage->getallmsguser ( $this->_userid, 10 ) );
		$this->view->dbeeuser = $this->_userid;
		$Activities = new Application_Model_Activities ();
		$others = $Activities->getNotification ( $this->_userid, 10, 'seen' ); // for type others
		if (count ( $others ) > 0) {
			foreach ( $others as $key1 => $value1 ) {
				$Activities->updateactivity ( array (
						'act_status' => 1 
				), $value1 ['act_id'] );
			}
		}
	}
	
	public function topmessageAction() {
		$data = array ();
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		$response = $this->getResponse ();
		$response->setHeader ( 'Content-type', 'application/json', true );
		//if ($this->getRequest ()->isXmlHttpRequest () && $this->getRequest ()->getMethod () == 'POST') {
			$request = $this->getRequest ()->getParams ();
			$user = $request ['userID'];
			$checking = $request ['call'];
			$editable = $request ['editable'];
			
			$msgArr = array (
					'ID',
					'MessageTo',
					'MessageFrom',
					'Message',
					'MessageDate' 
			);
			$usrArr = array (
					'Username',
					'ProfilePic',
					'usertype',
					'Name',
					'Status' 
			);
			if ($checking == 'false') {
				$msgempty = 'No messages exchanged';
				$msgdata .= '<h2 class="recMsg"><i class="fa fa-envelope-o"></i> Messages exchanged <span id="actnotifications-profile-top" ></span></h2>';
				$commentrow = $this->myclientdetails->getfieldsfromjointable ( $msgArr, 'tblMessages', array (
						'MessageTo' => $user,
						'MessageFrom' => $this->_userid,
						'tblUsers.Status' => 1 
				), $usrArr, 'tblUsers', 'MessageFrom', 'UserID', '', array (
						'MessageDate' => 'DESC' 
				), 3 );
			} else {
				$msgempty = 'No messages found';
				if ($user == '')
					$user = $this->_userid;
				if ($editable == 'yesedit') {
					$Activities = new Application_Model_Activities ();
					$others = $Activities->getNotification ( $user, 10, 'seen' ); // for type others
					if (count ( $others ) > 0) {

						foreach ( $others as $key1 => $value1 ) {
							$Activities->updateactivity ( array (
									'act_status' => 1 
							), $value1 ['act_id'] );
						}

					}
				}
			}	
			  
			  
			$msgdata .= '<div class="rtListOver">
							<h2 class="recMsg" title="Click to open or Drag to re-arrange"><i class="fa fa-envelope-o"></i>Recent messages <span id="actnotifications-profile-top" ></span>																		
							<span class="navAllLink"><a href="'.BASE_URL.'/message">view all</a></span>
							</h2>							
						</div>';

			if($user==$this->_userid)
			{

				$exSQL = "SELECT `tblMessages`.`ID`, `tblMessages`.`MessageTo`, `tblMessages`.`MessageFrom`, `tblMessages`.`Message`, `tblMessages`.`MessageDate`, `tblUsers`.`Username`, `tblUsers`.`ProfilePic`, `tblUsers`.`usertype`, `tblUsers`.`Name`, `tblUsers`.`Status` FROM `tblMessages`  INNER JOIN `tblUsers` ON tblMessages.MessageFrom=tblUsers.UserID WHERE (tblMessages.clientID = '".clientID."') AND (MessageTo = '".$user."' OR MessageFrom = '".$this->_userid."' ) AND (tblUsers.Status = 1) ORDER BY (MessageDate) DESC LIMIT 3";
			
			}			 
			else
			{

			 	$exSQL = "SELECT `tblMessages`.`ID`, `tblMessages`.`MessageTo`, `tblMessages`.`MessageFrom`, `tblMessages`.`Message`, `tblMessages`.`MessageDate`, `tblUsers`.`Username`, `tblUsers`.`ProfilePic`, `tblUsers`.`usertype`, `tblUsers`.`Name`, `tblUsers`.`Status` FROM `tblMessages`  INNER JOIN `tblUsers` ON tblMessages.MessageFrom=tblUsers.UserID WHERE (tblMessages.clientID = '".clientID."') AND (MessageTo = '".$user."') AND ( MessageFrom = '".$this->_userid."' ) AND (tblUsers.Status = 1) ORDER BY (MessageDate) DESC LIMIT 3";
			
			} 	
			$commentrow = $this->myclientdetails->passSQLquery($exSQL); 
			
			//$data [] = $msgdata;
		//}
			
      	$msgdata .= '<div class="rboxContainer" id="recMsg">';
      	if(count($commentrow)>0){
      		foreach ($commentrow as $key => $value) {

				$msguserId = ($value['MessageFrom']!=$this->_userid) ? $value['MessageFrom'] : $value['MessageTo'];
      			$profilePc = $this->commonfun->checkImgExist($value['ProfilePic'],'userpics','default-avatar.jpg');
      			$msgdata .= '<div id="message-'.$value['ID'].'" class="rbcRow newmsgrow" chkuser="'.$msguserId.'" fromadmin="'.$value['fromadmin'].'" read="0" search="" from="" to=""><a href="#"> <img border="0" profilename="Admin User" src="'.IMGPATH.'/users/small/'.$profilePc.'" width="32" height="32"> <span class="dashMsgLink"> '.$this->myclientdetails->dbSubstring($this->myclientdetails->escape($value['Message']),'200','...').'</span></a></div>';
      		}
      	}
      	else
      	{
      		$msgdata .= '<div class="rbcRow noFound"> '.$msgempty.' </div>';
      	}
      	$msgdata .= '</div>';

      	$data[] = $msgdata;
	exit;
	return $response->setBody(Zend_Json::encode($data));
}	 


public function getuserAction() {
		$data = array ();
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		$response = $this->getResponse ();
		$q = ( string ) strtolower ( $this->_getParam ( 'q' ) );
		// $q = 'ad';
		$text = $this->myclientdetails->customEncoding ( $q, $search = "true" );
		$myMessage = new Application_Model_Message ();
		$data = $myMessage->getallmsguser1 ( $this->_userid, '0', $text );
		
		echo $data;
		return;
		
	} 





public function msgnotificationAction() {	

	$data = array();
	$this->_helper->layout()->disableLayout();
	$this->_helper->viewRenderer->setNoRender(true);
	$response = $this->getResponse();
	$request  = $this->getRequest();
	$response->setHeader('Content-type', 'application/json', true);
	if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
	{
		
		$id = $this->_userid;
		$myMessage = new Application_Model_Message ();
		$myhome_obj = new Application_Model_Myhome();
		
		/*$datasobj = $myMessage->dbeemassagesdate ($this->_userid, $start);
		
		foreach ($datasobj as $data) {
			$mydate [] = $data ['MsgDate'];
		}	*/	
		
		$messages = $myMessage->dbeemassagesnotify ( $mydate, $this->_userid, $start, adminID );
		$userid = $id;
		$adminid = adminID;
		$start = $start;
		$startnew = $start + 10;
		$search = $search;
		$countRetain = 1;
		$myclientdetails = new Application_Model_Clientdetails();
		$common_obj =  new Application_Model_Commonfunctionality();
		$CurrDate=date('Y-m-d H:i:s');
		$i=1;
		$highlightCSS = '';
		$highlight = ($request->getpost('higlight') == '') ? 0 : $request->getpost('higlight');

		if(count($messages)>0) {
			
			foreach($messages as $rowMsg):
			$highlightCSS = '';
			$ago= date("M d,y", strtotime($rowMsg['MessageDate']));

		if($highlight >= $countRetain) $highlightCSS = 'highlightNotification';
		
			$userName=$rowMsg['Name'].' '.$rowMsg['lname'];;
			$userPic=$rowMsg['ProfilePic'];
			if($rowMsg['Fromadmin']=='1'){ 
				$msglink=false;
				$cursor='';
			 }else{ 
			 	$msglink=true;
			 	$cursor='style="cursor:pointer"';
			 }

			 
			$userTypenal = $common_obj->checkuserTypetooltip($rowMsg['usertype']);
			if($rowMsg['MessageTo']==$id) {
				$ChkUser=$rowMsg['MessageFrom'];
				$ChkUsername = $common_obj->getredbeedetail($rowMsg['MessageFrom']);
				$sendlabel='<font size="1" color="#999">from</font>&nbsp;';
			}
			else {
				$ChkUser=$rowMsg['MessageTo'];
				$ChkUsername = $common_obj->getredbeedetail($rowMsg['MessageTo']);
				$sendlabel='<font size="1" color="#999">sent to</font>&nbsp;';
			}
			if($rowMsg['Fromadmin']=='1') {
				$ChkUser=adminID;
				$ChkUsername = $common_obj->getredbeedetail(adminID);
				$sendlabel='<font size="1" color="#999">sent by admin</font>&nbsp;';
			}
			

			
			$Msguser = $myhome_obj->getrowuser($ChkUser);
			$userName=$Msguser['Name'].' '.$Msguser['lname'];
			$userPic=$Msguser['ProfilePic'];
		
			
				$profileLinkStart='<a href="'.BASE_URL.'/user/'.$myclientdetails->customDecoding($ChkUsername['Username']).'">';
			
				$ago = $this->agohelper($rowMsg['MsgDate']);		
				$pic1 = $common_obj->checkImgExist($userPic,'userpics','default-avatar.jpg');
			


			$return[$i]['rowid'] = $rowMsg['ID'];
			$return[$i]['msgid'] = $rowMsg['msgid'];
			$return[$i]['class'] = $highlightCSS.' '.$newmsgrow.' notiContainerList newmsgrow';
			$return[$i]['ChkUser'] = $ChkUser;
			$return[$i]['read'] = $rowMsg['Unread'];
			$return[$i]['Fromadmin'] = $rowMsg['Fromadmin'];
			$return[$i]['MessageFrom'] = $rowMsg['MessageFrom'];
			$return[$i]['search'] = $search;
			$return[$i]['from'] = $dateFrom;
			$return[$i]['to'] = $dateTo;
			
			$return[$i]['profilelink'] = $profileLinkStart;
			$return[$i]['pic'] = $pic1;
			$return[$i]['cursor'] = $cursor;
			$return[$i]['msglink'] = $msglink;
			$return[$i]['sendlabel'] = $sendlabel;
			$return[$i]['userTypenal'] = $userTypenal;
			
			$return[$i]['userName'] = $myclientdetails->customDecoding($userName);
			$return[$i]['archiveDiv'] = $archiveDiv;
			$return[$i]['blockDiv'] = $blockDiv;
			$htmlmsg = preg_replace('#(<br */?>\s*)+#i', '<br />', nl2br($rowMsg['Message']));
			$return[$i]['Message'] = $htmlmsg;
			$return[$i]['MsgDate'] = date('d - M - Y', strtotime($rowMsg['MessageDate'])).'</li>';
			
			$i++; 
			$countRetain++;
			//print_r($Msguser);
			$myMessage->updateactivity($rowMsg['act_id']);
			endforeach;

			

			$success=1;		
		}else {
			$startnew=$start;
			$seemorelabel='- no more messages -';
			$return='';
			$success = 0;
		}
		$data['content'] = $return;
		$data['success'] = $success;
		
		
	}
	else
	{
		$data['status'] = 'error';
		$data['message'] = 'Some thing went wrong here please try again';
	}
	return $response->setBody(Zend_Json::encode($data));
}


public function dbeemessageAction() {	

	$data = array();
	$this->_helper->layout()->disableLayout();
	$this->_helper->viewRenderer->setNoRender(true);
	$response = $this->getResponse();
	$response->setHeader('Content-type', 'application/json', true);
	if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
	{
		$id = $this->_userid;
		$search = 0;
		$filter = new Zend_Filter_StripTags ();
		$request = $this->getRequest ();
		$start = ( int ) $request->getPost ( 'start' );
		$searchid = ( int ) $request->getPost ( 'userid' );
		$company = $filter->filter($request->getPost ('company'))?$this->myclientdetails->customEncoding ($request->getPost ('company')):'';
		$dateFrom = $filter->filter ( $request->getPost ( 'dateFrom' ) );
		$search = $filter->filter ( $request->getPost ( 'search' ) );
		
		$dateTo = $filter->filter ( $request->getPost ( 'dateTo' ) );
		$dateTo = strtotime ( $dateTo );
		$dateTo = strtotime ( "+1 day", $dateTo );
		$dateTo = date ( 'd-m-Y', $dateTo );
		
		
		$end = $request->getPost ( 'end' );
		$sortuserid = ( int ) $request->getPost ( 'shortid' );
		$myMessage = new Application_Model_Message ();
		$myhome_obj = new Application_Model_Myhome();
		if (!empty($sortuserid)) {
			$datasobj = $myMessage->dbeemassagesdate2 ($this->_userid, $start, $sortuserid);
		} else {
			$datasobj = $myMessage->dbeemassagesdate ($this->_userid, $start);
		}
		foreach ($datasobj as $data) {
			$mydate [] = $data ['MsgDate'];
		}		
		if (($company != '') || ($dateFrom != '')) {
			$messages = $myMessage->dbeemassages2 ( $mydate, $this->_userid, $start, adminID, $company, $dateFrom, $dateTo );
			$dateFrom = $dateFrom;
			$dateTo = $dateTo;
			
		} else
		$messages = $myMessage->dbeemassages ( $mydate, $this->_userid, $start, adminID );
		$userid = $id;
		$adminid = adminID;
		$start = $start;
		$startnew = $start + 10;
		$search = $search;
		
		$myclientdetails = new Application_Model_Clientdetails();
		$common_obj =  new Application_Model_Commonfunctionality();
		$CurrDate=date('Y-m-d H:i:s');
		$i=1;
		
		if(count($messages)>0) {
			if(isset($_GET['reload'])) $startnew=$end;
			else $startnew=$start+10;
			if(count($messages)<10) {
				$startnew=$start;
				$seemorelabel='- no more message to show -';
			}
			else $seemorelabel='see more';
		
			foreach($messages as $rowMsg):
		
			$ago= date("M d,y", strtotime($rowMsg['MessageDate']));
		
			$userName=$rowMsg['Name'].' '.$rowMsg['lname'];;
			$userPic=$rowMsg['ProfilePic'];
			if($rowMsg['Fromadmin']=='1'){ 
				$msglink=false;
				$cursor='';
			 }else{ 
			 	$msglink=true;
			 	$cursor='style="cursor:pointer"';
			 }
			$userTypenal = $common_obj->checkuserTypetooltip($rowMsg['usertype']);
			if($rowMsg['MessageTo']==$id) {
				$ChkUser=$rowMsg['MessageFrom'];
				$ChkUsername = $common_obj->getredbeedetail($rowMsg['MessageFrom']);
				$sendlabel='<font size="1" color="#999">from</font>&nbsp;';
			}
			else {
				$ChkUser=$rowMsg['MessageTo'];
				$ChkUsername = $common_obj->getredbeedetail($rowMsg['MessageTo']);
				$sendlabel='<font size="1" color="#999">sent to</font>&nbsp;';
			}
			if($rowMsg['Fromadmin']=='1') {
				$ChkUser=adminID;
				$ChkUsername = $common_obj->getredbeedetail(adminID);
				$sendlabel='<font size="1" color="#999">sent by admin</font>&nbsp;';
			}
			
			$Msguser = $myhome_obj->getrowuser($ChkUser);
			$userName=$Msguser['Name'].' '.$Msguser['lname'];
			$userPic=$Msguser['ProfilePic'];
		
			if(!$msglink) {
				$archiveDiv='<div style="float:left; margin-left:10px; cursor:pointer; color:#666666;" onclick="javascript:archivemessage('.$ChkUser.','.$rowMsg['ID'].',1);" title="delete message">[delete message]</div>';
			}
			else {
				$archiveDiv='';
			}
			
			$archiveDiv='<div id="archivemessage" user="'.$ChkUser.'" rowid="'.$rowMsg['ID'].'" style="float:left; margin-left:10px; cursor:pointer; color:#666666;" title="delete message">[delete message]</div>';
			// CHECK IF THIS USER IS BLOCKED BY PROFILE OWNER
		
			if($rowMsg['Fromadmin']!=1) {
				if($myMessage->chkblockuser($this->_userid,$ChkUser)==0)					
					$blockDiv='<div style="float:left; margin-left:10px; cursor:pointer; color:#666666;" title="block '.$myclientdetails->customDecoding($userName).' from messaging you" '.$ChkUser.',\''.$myclientdetails->customDecoding($userName).'\','.$rowMsg['ID'].');" class="blockmessage" user="'.$ChkUser.'" rowid="'.$rowMsg['ID'].'" name="'.$myclientdetails->customDecoding($userName).'">[block '.$myclientdetails->customDecoding($userName).']</div>';
				else
					$blockDiv='<div style="float:left; margin-left:10px; color:#CC0000;" title="'.$myclientdetails->customDecoding($userName).' is blocked from messaging you">['.$myclientdetails->customDecoding($userName).' is blocked]</div>';
			} else $blockDiv='';
			$newmsgrow = 'newmsgrow';
			$morebtn = '<div style="margin-top: 10px;"><span class="btn btn-yellow btn-mini joinDiscusstion">more</span></div>';		
			if($rowMsg['Fromadmin']==1){				
					$newmsgrow = '';
					$morebtn = '';
			}
		
			if($rowMsg['Unread']=='1' && $rowMsg['MessageFrom']!=$this->_userid && $rowMsg['MessageFrom']!='100000') {				
				$highlightBox='msgRowUnread';
			} else {				
				$highlightBox='';
			};
		
			if($rowMsg['Status']=='1')
				$profileLinkStart='<a href="'.BASE_URL.'/user/'.$myclientdetails->customDecoding($ChkUsername['Username']).'">';
			else
				$profileLinkStart='<a href="javascript:void(0)" class="profile-deactivated" title="'.DEACTIVE_ALT.'" onclick="return false;">';
				
				$ago = $this->agohelper($rowMsg['MsgDate']);		
				$pic1 = $common_obj->checkImgExist($userPic,'userpics','default-avatar.jpg');
			
			
			$return[$i]['rowid'] = $rowMsg['ID'];
			$return[$i]['class'] = $highlightBox.' '.$newmsgrow.' msgblockndelete msgRow';
			$return[$i]['morebtn'] = $morebtn;
			$return[$i]['ChkUser'] = $ChkUser;
			$return[$i]['read'] = $rowMsg['Unread'];
			$return[$i]['fromadmin'] = $rowMsg['fromadmin'];
			$return[$i]['search'] = $search;
			$return[$i]['from'] = $dateFrom;
			$return[$i]['to'] = $dateTo;
			
			$return[$i]['profilelink'] = $profileLinkStart;
			$return[$i]['pic'] = $pic1;
			$return[$i]['cursor'] = $cursor;
			$return[$i]['msglink'] = $msglink;
			$return[$i]['sendlabel'] = $sendlabel;
			$return[$i]['userTypenal'] = $userTypenal;
			
			$return[$i]['userName'] = $myclientdetails->customDecoding($userName);
			$return[$i]['archiveDiv'] = $archiveDiv;
			$return[$i]['blockDiv'] = $blockDiv;
			
			$return[$i]['Message'] = nl2br($rowMsg['Message']);
			$return[$i]['MsgDate'] = date('M - d', strtotime($rowMsg['MsgDate'])).'</li>';
			
			$i++;
			endforeach;
			$success=1;		
		}else {
			$startnew=$start;
			$seemorelabel='- no more messages -';
			$return='';
			$success = 0;
		}
		$data['content'] = $return;
		$data['success'] = $success;
		
		
	}
	else
	{
		$data['status'] = 'error';
		$data['message'] = 'Some thing went wrong here please try again';
	}
	return $response->setBody(Zend_Json::encode($data));
}

public function messagefeedAction() {	
		
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$request = $this->getRequest ();
			$start = ( int ) $request->getPost ( 'start', 0 );
			$end = $request->getPost ('end');			
			$user = $request->getpost ('user');
			$myMessagedbee = new Application_Model_Message ();
			$chanpar = $request->getpost ('mid');
			$datefrom = $request->getpost ('datefrom');
			$dateto = $request->getpost ('dateto');
			$reload = $request->getpost ('reload');
			$dateTo = strtotime ( $dateto );
			$dateTo = strtotime ( "+1 day", $dateTo );
			$dateTo = date ( 'd-m-Y', $dateTo );
			$search = $request->getpost ( 'search' );			
			$mid = $myMessagedbee->getchaninparent ( $chanpar );			
			$userid = ( int ) $this->_userid;
			
				if ($search == 1) {
						
					$messages = $myMessagedbee->messagedbeessearch ( $user, $userid, $datefrom, $dateTo );
					$sendmessage = 1;
				} else {
					if ($myMessagedbee->chkblockuserfeed ( $this->_userid, $user ) > 0)
						$sendmessage = 0;
					else
						$sendmessage = 1;
					$myMessagedbee->chkblockuser ( $this->_userid, $user );
					$messages = $myMessagedbee->messagedbees ( $mid, $userid, $user, $start, $userid );
				}
			$read=$request->getpost ('read');		
			$CurrDate=date('Y-m-d');
			$return='';
			$messagefeed=array();
			$dates='';
			$myclientdetails = new Application_Model_Clientdetails();
			$i=0;
			if(count($messages)>0) {
				$myhome_obj = new Application_Model_Myhome();
				$checkImage = new Application_Model_Commonfunctionality();			
				foreach($messages as $rowMsg):				
					$dates.='<div style="float:left; width:90px; margin-bottom:5px; margin-right:25px;"><img src="'.IMGPATH.'/users/medium/'.$userPic.'" width="90" height="90" border="0" /></div><div align="center" style="width:90px; margin-bottom:15px;"><b>'.$myclientdetails->customDecoding($rowMsg['Name']).'</b></div></div>';				
					$ago=date('d M', strtotime($rowMsg['MsgDate']));
						if($userid==$rowMsg['MessageTo']) {
							$ChkUser=$rowMsg['MessageFrom'];
						}
						else {
							$ChkUser=$rowMsg['MessageFrom'];
						}
					$Msguser = $myhome_obj->getrowuser($ChkUser);					
					$userName=$myclientdetails->customDecoding($Msguser['Name']);
					$userPic=$Msguser['ProfilePic'];				
					$pic1 = $checkImage->checkImgExist($userPic,'userpics','default-avatar.jpg');					
					
				 	$messagefeed[$i]['dates'] = $dates;
					$messagefeed[$i]['ago'] = $ago;
					$messagefeed[$i]['userName'] = $userName;
					$messagefeed[$i]['userPic'] = $userPic;
					$messagefeed[$i]['Name'] = $myclientdetails->customDecoding($rowMsg['Name']);
					$messagefeed[$i]['Message'] =nl2br($rowMsg['Message']);
					$messagefeed[$i]['MessageFrom'] = $rowMsg['MessageFrom'];
					$messagefeed[$i]['userid'] = $userid;
					$messagefeed[$i]['pic'] = $pic1; 
					$i++;
				endforeach;
			}else{
				$data['status'] = 'error';
				$data['message'] = '<div align="center" class="noFound" style="margin-top:140px;">message feed empty</div>';
			}
			$data['reload'] = $reload;
			$data['sendmessage'] = $sendmessage;
			$data['user'] = $user;
			$data['mid'] = $mid;
			
			
			$data['content'] = $messagefeed;
			
		}
		else
		{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));				
		
		
	}





	public function messagefeed2Action() {	
		
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$request = $this->getRequest ();
			$start = ( int ) $request->getPost ( 'start', 0 );
			$end = $request->getPost ('end');
			
			$user = $request->getpost ('user');
			$myMessagedbee = new Application_Model_Message ();
			$chanpar = $request->getpost ('mid');
			$datefrom = $request->getpost ('datefrom');
			$dateto = $request->getpost ('dateto');
			$reload = $request->getpost ('reload');
			$dateTo = strtotime ( $dateto );
			$dateTo = strtotime ( "+1 day", $dateTo );
			$dateTo = date ( 'd-m-Y', $dateTo );
			$search = $request->getpost ( 'search' );
			if($chanpar!=''){
			$mid = $myMessagedbee->getchaninparent ( $chanpar );
			}
			
			$userid = ( int ) $this->_userid;
			
				if ($search == 1) {
						
					$messages = $myMessagedbee->messagedbeessearch ( $user, $userid, $datefrom, $dateTo );
					$sendmessage = 1;
				} else {
					if ($myMessagedbee->chkblockuserfeed ( $this->_userid, $user ) > 0)
						$sendmessage = 0;
					else
						$sendmessage = 1;
					$myMessagedbee->chkblockuser ( $this->_userid, $user );
					$messages = $myMessagedbee->messagedbees ( $mid, $userid, $user, $start, $userid );
				}
			$read=$request->getpost ('read');		
			$CurrDate=date('Y-m-d');
			$return='';
			$messagefeed=array();
			$dates='';
			$myclientdetails = new Application_Model_Clientdetails();
			$i=0;
			if(count($messages)>0) {
				$myhome_obj = new Application_Model_Myhome();
				$checkImage = new Application_Model_Commonfunctionality();			
				foreach($messages as $rowMsg):				
					$dates.='<div style="float:left; width:90px; margin-bottom:5px; margin-right:25px;"><img src="'.IMGPATH.'/users/medium/'.$userPic.'" width="90" height="90" border="0" /></div><div align="center" style="width:90px; margin-bottom:15px;"><b>'.$myclientdetails->customDecoding($rowMsg['Name']).'</b></div></div>';				
					$ago=date('d M', strtotime($rowMsg['MsgDate']));
						if($userid==$rowMsg['MessageTo']) {
							$ChkUser=$rowMsg['MessageFrom'];
						}
						else {
							$ChkUser=$rowMsg['MessageFrom'];
						}
					$Msguser = $myhome_obj->getrowuser($ChkUser);					
					$userName=$myclientdetails->customDecoding($Msguser['Name']);
					$userPic=$Msguser['ProfilePic'];				
					$pic1 = $checkImage->checkImgExist($userPic,'userpics','default-avatar.jpg');					
					
				 	$messagefeed[$i]['dates'] = $dates;
					$messagefeed[$i]['ago'] = $ago;
					$messagefeed[$i]['userName'] = $userName;
					$messagefeed[$i]['userPic'] = $userPic;
					$messagefeed[$i]['Name'] = $myclientdetails->customDecoding($rowMsg['Name']);
					$messagefeed[$i]['Message'] =nl2br($rowMsg['Message']);
					$messagefeed[$i]['MessageFrom'] = $rowMsg['MessageFrom'];
					$messagefeed[$i]['userid'] = $userid;
					$messagefeed[$i]['pic'] = $pic1; 
					$i++;
				endforeach;
			}else{
				$data['status'] = 'error';
				$data['message'] = '<div align="center" class="noFound" style="margin-top:140px;">message feed empty</div>';
			}
			$data['reload'] = $reload;
			$data['sendmessage'] = $sendmessage;
			$data['user'] = $user;
			$data['mid'] = $mid;
			
			
			$data['content'] = $messagefeed;
			
		}
		else
		{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));
				
		
		
	}


	public function messagedbeeAction() {
		$mid = $this->_getParam ( 'id' );
		$read = $this->_getParam ( 'read' );
		$user = $this->_getParam ( 'user' );
		$datedd = $this->_getParam ( 'from' );
		$dateTodd = $this->_getParam ( 'to' );
		$search = $this->_getParam ( 'search' );
		
		$userid = ( int ) $this->_userid;
		$myMessagedbee = new Application_Model_Message ();
		if ($read == 1) {
			$read = '1';
			$msgid = $_GET ['m'];
			$data = array (
					'Unread' => '0' 
			);
			$myMessagedbee->updatemunread ( $data, $mid );
		} else {
			$read = '0';
			$msgid = '0';
		}
		$this->view->msguser = json_decode ( $myMessagedbee->getallmsguser ( $this->_userid, 10 ) );
		$this->view->loginuser = $userid;
		$this->view->user = $user;
		$this->view->dateFrom = $datedd;
		$this->view->dateTo = $dateTodd;
		$this->view->search = $search;
		$this->view->userid = $userid;
		$this->view->msgid = $mid;
		
	}



	public function messagedetailAction() {

		$mid = $this->_getParam ( 'id' );
		$read = $this->_getParam ( 'read' );
		$user = $this->_getParam ( 'user' );
		$datedd = $this->_getParam ( 'from' );
		$dateTodd = $this->_getParam ( 'to' );
		$search = $this->_getParam ( 'search' );
		
		$userid = ( int ) $this->_userid;
		$myMessagedbee = new Application_Model_Message ();
		if ($read == 1) {
			$read = '1';
			$msgid = $_GET ['m'];
			$data = array (
					'Unread' => '0' 
			);
			$myMessagedbee->updatemunread ( $data, $mid );
		} else {
			$read = '0';
			$msgid = '0';
		}
		$this->view->msguser = json_decode ( $myMessagedbee->getallmsguser ( $this->_userid, 10 ) );
		$this->view->loginuser = $userid;
		$this->view->user = $user;
		$this->view->dateFrom = $datedd;
		$this->view->dateTo = $dateTodd;
		$this->view->search = $search;
		$this->view->userid = $userid;
		$this->view->msgid = $mid;
		

		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{

			$data['success'] = 'success';
			
		}
		else
		{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));

		
	}
	
	public function addAction() {
		$data = array ();
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		$filter = new Zend_Filter_StripTags ();
		$response = $this->getResponse ();
		$response->setHeader ( 'Content-type', 'application/json', true );
		$fromadmin = '0';
		
		if ($this->getRequest ()->isXmlHttpRequest () && $this->getRequest ()->getMethod () == 'POST') {
			$Startdate = date ( 'Y-m-d H:i:s' ). substr(microtime(), 1, 9);
			$SubmitMsg = 0;
			$userid = $this->_userid;
			$request = $this->getRequest ();
			
			$user1 = $request->getpost ( 'user', '' );
			
			$from = $request->getpost ( 'from', '0' );
			if($from==1){
				$user = $request->getpost ( 'user', '' );
			}else{
				$user[0] = $request->getpost ( 'user', '' );
			}
			
			$commonfun = new Application_Model_Commonfunctionality ();
			
			$message = $filter->filter ( strip_tags ( $request->getpost ( 'message', '' ) ) );
			
			$massageinsert = new Application_Model_Message ();
			$messagedbeeval = ($request->getpost ( 'messagedbeeval' ));
			// check user is VIP OR not
		
			foreach($user as $userarr):
			// get follower info
			$getUserInfo = $this->User_Model->getUserDetail ( $userarr );
			$vip = 0;			
			if ($getUserInfo ['usertype'] != 0 && $getUserInfo ['usertype'] != 6 && $getUserInfo ['usertype'] != 10) {
				$vip = 1;
				if ($this->session_data ['usertype'] != 0 && $this->session_data ['usertype'] != 6) {
					$vip = 0;
				}
			}
			
			if ($vip == 0) {
				$chaninparent_obj = $massageinsert->checkchainparent ( $userid, $userarr );
				if ($chaninparent_obj ['ID']) {
					$Chainparent = $chaninparent_obj ['ID'];
				} else
					$Chainparent = 0;
			if($userid==adminID){
				$fromadmin = '1';
			}
				
				//
			$data2 = array (
					'MessageTo' => $userarr,
					'clientID' => clientID,
					'MessageFrom' => $userid,
					'Message' => $message,
					'MessageDate' => date ( 'Y-m-d H:i:s' ). substr(microtime(), 1, 9),
					'Unread' => '1',
					'Archive' => '0',
					'ArchivedBy' => '',
					'ChainParent' => $Chainparent,
					'Fromadmin' => $fromadmin 
			);
				
				$success = $massageinsert->insertmessage ( $data2 );
				if ($success) {
					$SubmitMsg = 1;
					$useInfo = $this->User_Model->ausersocialdetail ( $userarr );
					/**
					 * ***for email ****
					 */

					
					$getusersnotiinfo = $this->myclientdetails->getfieldsfromtable(array('Messages'),'tblNotificationSettings','User',$userarr);
	                if ($getusersnotiinfo[0]['Messages'] == 1) 
	                {

						$EmailTemplateArray = array (
								'Email' => $useInfo [0] ['Email'],
								'SName' => $this->session_data ['Name'],
								'Slname' => $this->session_data ['lname'],
								'RName' => $useInfo [0] ['Name'],
								'Rlname' => $useInfo [0] ['lname'],
								'Message' => $message,
								'case' => 38 
						);
						$bodyContentmsgall = $this->dbeeComparetemplateOne ( $EmailTemplateArray );
					}
					 /**
					 * ***for email ****
					 */
					$this->notification->commomInsert ( '10', '10', $success, $userid, $userarr );
					// Insert for involve activity
				                                                                
					if ($messagedbeeval == 'messagedbeeval') {
						// echo $success;exit;
						$data ['success'] = $success;
					}
				}
			
				$data ['user'] = $user[0];
				$data ['rand'] = rand ();
				$data ['chainparent'] = $Chainparent;
				$data ['username'] = 'user';
				$data ['status'] = 'success';
				$userName = 'username';
			} else {
				
				$data ['status'] = 'error';
				$data ['vip'] = $vip;
				$data ['errormessage'] = 'Sorry you cannot message this user';
			}
			
			endforeach;
			
			return $response->setBody ( Zend_Json::encode ( $data ) );
		}
	}
	public function reloadmessagesAction() {
	
			$data = array();
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(true);
			$response = $this->getResponse();
			$response->setHeader('Content-type', 'application/json', true);
			if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
			{
				$Messagetable = new Application_Model_Message ();
				
				$user = $this->_request->getPost('user');
				$messagefeed = '';
				$reloadmsg = $Messagetable->messagereloads ( $user, $this->_userid );
				$checkImage = new Application_Model_Commonfunctionality();
				$myhome_obj = new Application_Model_Myhome();
				$myclientdetails = new Application_Model_Clientdetails();
				$i=1;
				
				if(count($reloadmsg)>0) {
					
					$myclientdetails= new Application_Model_Clientdetails();
					foreach($reloadmsg as $row) :
					if($this->_userid==$row['MessageTo']) {
						$ChkUser=$row['MessageFrom'];
					}
					else {
						$ChkUser=$row['MessageFrom'];
					}
					$Msguser = $myhome_obj->getrowuser($ChkUser);			
					$userName=$Msguser['Name'];
					$userPic=$Msguser['ProfilePic'];					
					$pic1 = $checkImage->checkImgExist($userPic,'userpics','default-avatar.jpg');
					$ago=date('d M', strtotime($row['MessageDate']));
					if($row['MessageFrom']==$this->_userid) // MESSAGE NOT SENT BY PROFILE HOLDER
						$messagefeed.='<div class="msgouter"><div class="imgfloatmsg" >
						<img src="'.IMGPATH.'/users/small/'.$pic1.'" border="0" /></div>
					<div class="default-speechwrapper-lightgrey speechWrpBox" >
					<div class="msgTxtboxFeed"><span class="msgdate">'.$ago.'</span>'.nl2br($row['Message']).'</div>
					</div></div>';
					else // MESSAGE SENT BY PROFILE HOLDER
				
						$messagefeed.='<div class="msgouter"><div class="imgfloatmsg-right">
					<img src="'.IMGPATH.'/users/small/'.$pic1.'" border="0" /></div>
					<div class="default-speechwrapper-darkgrey speechWrpBox">
					<div class="msgTxtbox"><span class="msgdate">'.$ago.'</span>
					'.nl2br($row['Message']).'</div>
					</div></div>';
					 $i++;
				
					endforeach;
				}
				
				$data ['content'] = $messagefeed;
				$data ['status'] = 'success';
				} else {
					$data ['status'] = 'error';
					$data ['message'] = 'Some thing went wrong here please try again';
				}			
			
			return $response->setBody(Zend_Json::encode($data));
		
		
	}
	public function sendmessageAction() {
		$data = array ();
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		$response = $this->getResponse ();
		$response->setHeader ( 'Content-type', 'application/json', true );
		
		if ($this->getRequest ()->isXmlHttpRequest () && $this->getRequest ()->getMethod () == 'POST') {
			
			$Messagetable = new Application_Model_Message ();
			
			$user = $this->_request->getPost ( 'user' );
			$name = $this->_request->getPost ( 'name' );
			if ($Messagetable->chkblockuser ( $this->_userid, $user ))
				$sendmessage = true;
			else
				$sendmessage = false;
			
			$userid = ( int ) $this->_userid;
			$sendmessage = $sendmessage;
			$user = $user;
			$Row = $Messagetable->getuser ( $user );
			
			$Name = $Row ['Name'];
			$Gender = $Row ['Gender'];
			$vip = false;
			if ($Gender == 'Male')
				$gendertag = 'him ';
			elseif ($Gender == 'Female')
				$gendertag = 'her ';
			else
				$gendertag = '';
			$storage = new Zend_Auth_Storage_Session ();
			$session = $storage->read ();
			$userinfo = new Application_Model_DbUser ();
			
			$userinfodetails = $userinfo->userdetailall ( $user );
			
			if ($getUserInfo ['usertype'] != 0 && $getUserInfo ['usertype'] != 6) {
				// echo $this->session_data['usertype'];
				$vip = true;
				if ($this->session ['usertype'] != 0 && $this->session ['usertype'] != 6) {
					$vip = false;
				}
			}
			if ($sendmessage == '') {
				$content .= '<div>
			<h2 class="titlePop">New Message</h2>
			<div class="postTypeContent" id="passform">
				<div class="formRow">
				 <div class="formField">
					 <input type="text"  readonly class="textfield"  value="' . $this->myclientdetails->customDecoding ( $Name ) . '">
					 <i class="optionalText">To</i>
				 </div>
				</div>
				
				<div class="formRow">
				 <div class="formField">
					<textarea class="textareafield" id="message-reply"></textarea>
					<input type="hidden" id="hiddenuser" value="' . $user . '">
					
					 <i class="optionalText">Message</i>
				 </div>
				</div>
			</div>
			
			<div align="center" id="sendmessage-msg" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:14px; color:#999999; margin-top:60px; display:none;">message sent to <b>' . $this->myclientdetails->customDecoding ( $Name ) . '</b></div>
			</div></div>';
			} else {
				if ($vip)
					$content .= '<div align="center" id="sendmessage-msg" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:18px; color:#999; margin-top:30px;">Sorry you cannot message this user</div>';
				else
					$content .= '<div align="center" id="sendmessage-msg" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:14px; padding:20px; color:#CC0000; margin-top:80px;">' . $this->myclientdetails->customDecoding ( $Name ) . ' has blocked you from sending ' . $gendertag . ' a message.</div>';
			}
			
			$data ['content'] = $content;
			$data ['status'] = 'success';
		} else {
			$data ['status'] = 'error';
			$data ['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody ( Zend_Json::encode ( $data ) );
	}
	public function sendmsgtopAction() {
		$data = array ();
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		$response = $this->getResponse ();
		$response->setHeader ( 'Content-type', 'application/json', true );
		$filter = new Zend_Filter_StripTags ();
		if ($this->getRequest ()->isXmlHttpRequest ()) {
			
			$return = '<div id="createmsgContainer" class="newtopmsg postTypeContent">
					<h2 class="titlePop">New message</h2>
					<div class="formRow" >
						<div class="formField">					
					<input type="hidden" id="_submit_tag_names" value="" name="_submit_tag_names">
						</div>
					</div> 
            <div class="formRow">              
				<div class="formField">
				<textarea id="message-reply-top" class="textareafield message-reply-top"></textarea>
				<input id="hiddenuser" type="hidden" value="'.$this->_userid.'">				
				<label class="optionalText" for="message-reply-top">Message</label>
				</div>
            </div>            
            </div>';
			$data ['status'] = 'success';
			$data ['content'] = $return;
		}
		return $response->setBody ( Zend_Json::encode ( $data ) );
	}
	public function getfollwingfolowerAction() {
		$dataArray = array ();
		$data = array ();
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		$response = $this->getResponse ();
		$response->setHeader ( 'Content-type', 'application/json', true );
		$filter = new Zend_Filter_StripTags ();
		$messageobj = new Application_Model_Message ();
		$Common = new Application_Model_Commonfunctionality ();
		$myclientdetails = new Application_Model_Clientdetails ();
		$request = $this->getRequest ()->getParams ();
		
		$this->_helper->viewRenderer->setNoRender ( true );
		$param = $request ["q"];
		$searchuser = $messageobj->searchcomusers ( $param,$this->_userid );
	
		for($x = 0, $numrows = count ( $searchuser ); $x < $numrows; $x ++) {
			
			$userpic = $Common->checkImgExist ( $searchuser [$x] ["ProfilePic"], 'userpics', 'default-avatar.jpg' );
		
			$data [$x] = array (
					"id" => $searchuser [$x] ["UserID"],
					"text" => $myclientdetails->customDecoding ( $searchuser [$x] ["Name"] ),
					"email" => $myclientdetails->customDecoding ( $searchuser [$x] ["Email"] ),
					"url" => BASE_URL . '/timthumb.php?src=/userpics/' . $userpic . '&q=100&w=18&h=18"' 
			);
		}
		
		
		return $response->setBody ( Zend_Json::encode ( $data ) );
	}
	public function MessagereloadAction() {
		$request = $this->getRequest ();
		$data = $request->getParams ();
		$user = $this->_userid;
		
		$Messagetable = new Application_Model_Message ();
		
		$dbeeid = $this->getRequest ()->getParam ( 'db' );
		$end = $this->getRequest ()->getParam ( 'end' );
		$start = $this->getRequest ()->getParam ( 'start' );
		$reload = $this->getRequest ()->getParam ( 'reload' );
		$order = $this->getRequest ()->getParam ( 'sortorder' );
		$this->view->row = $myhome->getuserdbee ( $dbeeid );
		$this->view->Messagerow = $Messagetable->getMessagereload ( $dbeeid, $start );
		$this->view->Messagetotal = $Messagetable->getMessagetotal ( $dbeeid, $start );
		$this->view->startnew = $start + 5;
		$this->view->end = $start + 5;
		
		$response = $this->_helper->layout->disableLayout ();
		return $response;
	}
	public function archivemessageAction() {
		
		if ($this->getRequest ()->getMethod () == 'POST') {
			$request = $this->getRequest ();
			$user = $request->getPost ( 'user' );
			$message = $request->getPost ( 'message' );
			$status = $request->getPost ( 'status' );
			
			$rowid = $request->getPost ( 'rowid' );
			
			$Messagearchiv = new Application_Model_Message ();
			
			$row = $Messagearchiv->messagearchivtable ( $message );

			$chainparent = $row ['ChainParent'];
			$archivedby = $row ['ArchivedBy'];
			$msgfrom = $row ['MessageFrom'];
			
			if ($archivedby == '0' || $archivedby == '')
				$setarchivedby = $this->_userid;
			else
				$setarchivedby = $archivedby . ',' . $this->_userid;
			
			if ($msgfrom == adminID) {
				$data1['ArchivedBy'] = $this->_userid;
				
				$Messagearchiv->updatearchive ( $data1, $message,$message,$this->_userid );

			} else {
				$data2['ArchivedBy'] = $setarchivedby;
				
				$Messagearchiv->updatearchive2 ( $data2, $chainparent,$message,$this->_userid );
				
			}
			echo $message;
			$response = $this->_helper->layout->disableLayout ();
			return $response;
		}
	}
	public function linkdetailAction() {
		$request = $this->getRequest ();
		$user = $request->getPost ( 'user' );
		
		$response = $this->_helper->layout->disableLayout ();
		return $response;
	}
	public function blockuserfrommsgAction() {
		$request = $this->getRequest ();
		$user = $request->getPost ( 'user' );
		$id = $request->getPost ( 'id' );
		$cookieuser = ( int ) $this->_userid;
		$blockdate = date ( 'Y-m-d H:i:s' );
		$this->myclientdetails = new Application_Model_Clientdetails ();
		$Messagearchiv = new Application_Model_Message ();
		$myhome_obj = new Application_Model_Myhome ();
		$notify = 0;
		$return = '';
		$data = array (
				'BlockedUser' => $user,
				'BlockedBy' => $cookieuser,
				'BlockedDate' => $blockdate 
		);
		$Messagearchiv->inserblockuser ( $data );
		
		// SEND BLOCKED NOTIFICATION
		if ($notify == '1') {
			
			$row = $Messagearchiv->getuser ( $user );
			
			if ($row ['Gender'] == 'Male')
				$GenderTag = 'his';
			elseif ($row ['Gender'] == 'Female')
				$GenderTag = 'her';
			$OwnerName = $row ['Name'];
			$MessageDate = date ( 'Y-m-d H:i:s' );
			
			$dbeelife = '100000';
			if (! $request->getPost ( 'unblock' ))
				$message = "<a href='profile/index/id/" . $cookieuser . "'><b>" . $OwnerName . "</b></a> has blocked you from " . $GenderTag . " " . POST_NAME . "."; /* <a href='message.php?user=".$owner."'>Message ".$OwnerName."</a> to start a private conversation." */
			else
				$message = "<a href='profile/index/id/" . $cookieuser . "'><b>" . $OwnerName . "</b></a> has un-blocked you from " . $GenderTag . " " . POST_NAME . "."; /* <a href='profile.php?db=".$db."'>Click here</a> to go to the main db and join back the conversation :)"; */
			
			$data = array (
					'MessageTo' => $user,
					'MessageFrom' => $dbeelife,
					'Message' => $message,
					'MessageDate' => $MessageDate 
			);
			$Messagearchiv->insertMessage ( $data );
		}
		// SEND BLOCKED NOTIFICATION
		$username = $myhome_obj->getusername ( $user );
		echo '1~' . $this->myclientdetails->customDecoding ( $username ) . '~' . $id;
		$response = $this->_helper->layout->disableLayout ();
		return $response;
	}
	
	
	public function Agohelper($date)
	
	{
		$currentdate = date('Y-m-d h:i:s', time());
		$start_date  = new DateTime($currentdate);
	
	
		$since_start = $start_date->diff(new DateTime($date));
	
		if($since_start->y >0)
		{
			$ago = ($since_start->y >1 ) ? $since_start->y.' years ' : $since_start->y.' year ';
		}
		else if($since_start->m > 0)
		{
			$ago = ($since_start->m>1) ? $since_start->m.' months ' : $since_start->m.' month ';
		}
		else if($since_start->d>0)
		{
			$ago = ($since_start->d>1) ? $since_start->d.' days ' :$since_start->d.' day ';
		}
		else if($since_start->h>0)
		{
			$ago = ($since_start->h>1) ? $since_start->h.' hours ' : $since_start->h.' hour ';
		}
		else if($since_start->i>0)
		{
			$ago = ($since_start->i) ? $since_start->i.' minutes ' : $since_start->i.' minute ';
		}
		else if($since_start->s>0)
		{
			$ago = ($since_start->s>1) ? $since_start->s.' seconds ' : $since_start->s.' second ';
		}
	
		if(!empty($ago))
		{
			return $ago;
		}
		else
		{
	
			$diff=($this->_date_diff(time(), strtotime($date)));
	
			if($diff[y]!=0) $ago=($diff[y]>1) ? $diff[y].' years ' : $diff[y].' year ';
	
			elseif($diff[m]!=0) $ago=($diff[m]>1) ? $diff[m].' months ' : $diff[m].' month ';
	
			elseif($diff[d]!=0) $ago=($diff[d]>1) ? $diff[d].' days ' : $diff[d].' day ';
	
			elseif($diff[h]!=0) $ago=($diff[h]>1) ? $diff[h].' hours ' : $diff[h].' hour ';
	
			elseif($diff[i]!=0) $ago=($diff[i]>1) ? $diff[i].' minutes ' : $diff[i].' minute ';
	
			elseif($diff[s]!=0) $ago=($diff[s]>1) ? $diff[s].' seconds ' : $diff[s].' second ';
			$ago.=' ago';
			return $ago;
		}
	
	
	
	}
	
	
	
	
	
	function _date_diff($one, $two)
	
	{
	
		$invert = false;
	
		if ($one > $two) {
	
			list($one, $two) = array($two, $one);
	
			$invert = true;
	
		}
	
	
	
		$key = array("y", "m", "d", "h", "i", "s");
	
		$a = array_combine($key, array_map("intval", explode(" ", date("Y m d H i s", $one))));
	
		$b = array_combine($key, array_map("intval", explode(" ", date("Y m d H i s", $two))));
	
	
	
		$result = array();
	
		$result["y"] = $b["y"] - $a["y"];
	
		$result["m"] = $b["m"] - $a["m"];
	
		$result["d"] = $b["d"] - $a["d"];
	
		$result["h"] = $b["h"] - $a["h"];
	
		$result["i"] = $b["i"] - $a["i"];
	
		$result["s"] = $b["s"] - $a["s"];
	
		$result["invert"] = $invert ? 1 : 0;
	
		$result["days"] = intval(abs(($one - $two)/86400));
	
	
	
		if ($invert) {
	
			$this-> _date_normalize($a, $result);
	
		} else {
	
			$this-> _date_normalize($b, $result);
	
		}
	
	
	
		return $result;
	
	}
	
	
	
	function _date_normalize($base, $result)
	
	{
	
		$result =$this->_date_range_limit(0, 60, 60, "s", "i", $result);
	
		$result = $this->_date_range_limit(0, 60, 60, "i", "h", $result);
	
		$result = $this->_date_range_limit(0, 24, 24, "h", "d", $result);
	
		$result = $this->_date_range_limit(0, 12, 12, "m", "y", $result);
	
	
	
		$result = $this->_date_range_limit_days($base, $result);
	
	
	
		$result =$this->_date_range_limit(0, 12, 12, "m", "y", $result);
	
	
	
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
	
		$days_in_month_leap = array(31, 31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	
		$days_in_month = array(31, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	
	
	
		$this->_date_range_limit(1, 13, 12, "m", "y", $base);
	
	
	
		$year = $base["y"];
	
		$month = $base["m"];
	
	
	
		if (!$result["invert"]) {
	
			while ($result["d"] < 0) {
	
				$month--;
	
				if ($month < 1) {
	
					$month += 12;
	
					$year--;
	
				}
	
	
	
				$leapyear = $year % 400 == 0 || ($year % 100 != 0 && $year % 4 == 0);
	
				$days = $leapyear ? $days_in_month_leap[$month] : $days_in_month[$month];
	
	
	
				$result["d"] += $days;
	
				$result["m"]--;
	
			}
	
		} else {
	
			while ($result["d"] < 0) {
	
				$leapyear = $year % 400 == 0 || ($year % 100 != 0 && $year % 4 == 0);
	
				$days = $leapyear ? $days_in_month_leap[$month] : $days_in_month[$month];
	
	
	
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
	
}

