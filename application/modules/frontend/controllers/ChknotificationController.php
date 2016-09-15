<?php



class ChknotificationController extends IsController

{

	protected $_userid = null;

	

	public function init()

	{

		$storage 	= new Zend_Auth_Storage_Session();

		$auth        =  Zend_Auth::getInstance();

		if($auth->hasIdentity())

		{

			$data	  	= $storage->read();

			$this->_userid = $data['UserID'];		

		}else{

			$this->_helper->redirector->gotosimple('index','index',true);

		}

	}

	

	  public function indexAction()

	    {	    	

	    	$request = $this->getRequest();    	  	

	    	$checkFlag = (int)$request->getpost('checkFlag','');

	    	$currCount = (int)$request->getpost('currCount','');

	    	

	    	$TotalDbees=0;

	    	$TotalMessages=0;

	    	$TotalGroups=0;

	    	$cookieuser = $this->_userid;

	    	$dbeecoockes = $request->getCookie();

	    	$request = $this->getRequest();	    	

	    	if($checkFlag=='0') { // CHECK SINCE LAST LOGIN

	    		$CheckDateDbees=$request->getCookie('currloginlastseen', 'default');

	    		$CheckDateMsgs=$request->getCookie('currloginlastseen', 'default');

	    		$CheckDateGroups=$request->getCookie('currloginlastseen', 'default');

	    		$CheckDateComments=$request->getCookie('currloginlastseen', 'default');

	    		$CheckDateScore=$request->getCookie('currloginlastseen', 'default');

	    	}

	    	elseif($checkFlag=='1') { // CHECK SINCE LAST SEEN IN CURRENT LOGIN

	    		$CheckDateDbees=$request->getCookie('currloginlastseen', 'default');

	    		$CheckDateMsgs=$request->getCookie('currloginlastseenmsg', 'default');

	    		$CheckDateGroups=$request->getCookie('currloginlastseengroup', 'default');

	    		$CheckDateComments=$request->getCookie('currloginlastseencomments', 'default');

	    		$CheckDateScore=$request->getCookie('currloginlastscoretime', 'default');

	    	}

	    	$NotificationRowdata = new Application_Model_Notification();

	    	$FollowingTable = new Application_Model_Following();

	    	$DbeeallTable = new Application_Model_Myhome();

	    	$NotificationRow = $NotificationRowdata->getnotificationuser($this->_userid);	    

	    	$users = $FollowingTable->getfolloweruserbyid($this->_userid);

	  

	    	if($NotificationRow['Dbees']=='1') {		    		

	    		if(count($users)>0 && $CheckDateDbees!='') {		    				    			

	    			$TDBRow= $DbeeallTable->getdbeenotification($users,$CheckDateDbees);	    			

	    			$TotalDbees=count($DbeeallTable->getdbeenotification($users,$CheckDateDbees));

	    			if($currCount!='') $TotalDbees=$TotalDbees+$currCount;

	    		} else $TotalDbees=0;

	    	}

	    

	    	// CHECK ANY NEW SCORE

	    	$LastScoreDate=0;

	    	if($CheckDateScore!='') {

	    		$count=1;

	    		$Scoredata = $NotificationRowdata->getscorenotification($this->_userid,$CheckDateDbees);

	    

	    		if($Scoredata){

	    			foreach($Scoredata as $ScoreRow):

	    			if($count==1) {

	    				$ScoreUser=$ScoreRow['Name'];

	    				$LastScoreDate=$ScoreRow['ScoreDate'];

	    				if($ScoreRow['Score']=='1') $ScoreText='loved'; elseif($ScoreRow['Score']=='2') $ScoreText='liked'; elseif($ScoreRow['Score']=='3') $ScoreText='food for thought'; elseif($ScoreRow['Score']=='4') $ScoreText='disliked'; elseif($ScoreRow['Score']=='5') $ScoreText='hated';

	    				if($ScoreRow['Type']=='1') {

	    					$ScoreType='dbee';

	    					$dbRow = $NotificationRowdata->getdb($ScoreRow['ID']);

	    					if($dbRow['Type']=='1') $excerpt=(strlen($dbRow['Text'])<=50 ? $dbRow['Text'] : substr($dbRow['Text'],0,50).'...');

	    					elseif($dbRow['Type']=='2') $excerpt=(strlen($dbRow['LinkTitle'])<=50 ? $dbRow['LinkTitle'] : substr($dbRow['LinkTitle'],0,50).'...');

	    					elseif($dbRow['Type']=='3') $excerpt=(strlen($dbRow['PicDesc'])<=50 ? $dbRow['PicDesc'] : substr($dbRow['PicDesc'],0,50).'...');

	    					elseif($dbRow['Type']=='4') $excerpt=(strlen($dbRow['VidDesc'])<=50 ? $dbRow['VidDesc'] : substr($dbRow['VidDesc'],0,50).'...');

	    					elseif($dbRow['Type']=='5') $excerpt=(strlen($dbRow['PollText'])<=50 ? $dbRow['PollText'] : substr($dbRow['PollText'],0,50).'...');

	    				} elseif($ScoreRow->Type=='2') {

	    					$ScoreType='comment';

	    					$commRow = $NotificationRowdata->gecomm($ScoreRow['ID']);

	    					if($commRow['Type']=='1') $excerpt=(strlen($commRow['Comment'])<=50 ? $commRow['Comment'] : substr($commRow['Comment'],0,50).'...');

	    					elseif($commRow['Type']=='2') $excerpt=(strlen($commRow['LinkTitle'])<=50 ? $commRow['LinkTitle'] : substr($commRow['LinkTitle'],0,50).'...');

	    					elseif($commRow['Type']=='3') $excerpt=(strlen($commRow['PicDesc'])<=50 ? $commRow['PicDesc'] : substr($commRow['PicDesc'],0,50).'...');

	    					elseif($commRow['Type']=='4') $excerpt=(strlen($commRow['VidDesc'])<=50 ? $commRow['VidDesc'] : substr($commRow['VidDesc'],0,50).'...');

	    				}

	    			}

	    			$count++;

	    			endforeach;

	    		}

	    		$TotalScore=count($Scoredata);

	    	}

	    	if($TotalScore>0) setcookie('currloginlastscoretime',$LastScoreDate);

	    

	    	// CALCULATE ANY NEW COMMENTS

	    	

	    	if($CheckDateComments!='') {

	    		$dbs='';

	    		$dbs= $DbeeallTable->dbeeusernotifi($this->_userid);

	    		//print_r($dbs);

	    

	    		if($dbs!='') {

	    			$seencomms=$request->getCookie('seencomms', '');

	    			$TCdata = $NotificationRowdata->chknewcomments($seencomms,$CheckDateComments,$dbs,$this->_userid);	    		

	    			$count=1;

	    			$countTC=0;

	    			foreach($TCdata as $TCRow):

	    			$AllowedRow=$NotificationRowdata->getNotifyPop($TCRow['DbeeID'],$this->_userid);	    			

	    			if($AllowedRow['NotifyPop']=='1') {

	    				

	    				if($count==1) {

	    					$CommentUser=$TCRow['CommentUser']; $CommentUserName=$TCRow['Name'];

	    				}

	    				$FirstCommDate=$TCRow['CommentDate'];

	    				$count++;

	    				$countTC++;

	    			}

	    			endforeach;

	    			if($CommentUser==$this->_userid) $mycomment=1; else $mycomment=0;	    			

	    			$TotalComments=$countTC;

	    		} else $TotalComments=0;

	    	}

	    	else $TotalComments=0;

	    //echo $TotalComments;

	    	if($TotalComments>0) {

	    		setcookie('firstcommdate', $FirstCommDate);

	    		setcookie('newcommcookie', $TotalComments);

	    	}

	    

	    	// CALCUATE TOTAL MESSAGES SINCE LAST LOGIN/LAST SEEN

	    	if($NotificationRow['Messages']=='1') {

	    		if($CheckDateMsgs!='') {

	    			$TMRow = $NotificationRowdata->getmessagenotify($cookieuser,$CheckDateMsgs);

	    			$TotalMessages=$NotificationRowdata->getmessagenotifycnt($cookieuser,$CheckDateMsgs);

	    		} else $TotalMessages=0;

	    	}

	    	// CALCUATE TOTAL GROUP INVITATIONS SINCE LAST LOGIN/LAST SEEN

	    	if($NotificationRow['Groups']=='1') {

	    		if($CheckDateGroups!='') {

	    			$TGIRow= $NotificationRowdata->getgroupmember($cookieuser,$CheckDateGroups);

	    			$TotalGroupInvite=$NotificationRowdata->getgroupmembercnt($cookieuser,$CheckDateGroups);

	    		}

	    		else $TotalGroupInvite=0;

	    

	    		// CALCUATE TOTAL GROUP JOIN REQUESTS SINCE LAST LOGIN/LAST SEEN

	    		if($CheckDateGroups!='') {

	    			$TGRRow=$NotificationRowdata->getjoingroup($cookieuser,$CheckDateMsgs);

	    			$TotalGroupRequests=$NotificationRowdata->getjoingroupcnt($cookieuser,$CheckDateMsgs);

	    		}

	    		else $TotalGroupRequests=0;

	    

	    		$TotalGroups=$TotalGroupInvite+$TotalGroupRequests;

	    	}

	    

	    	$TotalNotifications=$TotalDbees+$TotalMessages+$TotalGroups;

	    

	    	setcookie('newnotificationcount', $TotalNotifications, 0, '/');

	    	setcookie('newdbcount-ghst', $TotalDbees, 0, '/');

	    	setcookie('newdbcountscore-ghst', $TotalScore, 0, '/');

	    	//setcookie('newcommentcount-ghst', $TotalComments, 0, '/');

	    	setcookie('newmsgcount-ghst', $TotalMessages, 0, '/');

	    	setcookie('newgrpcount-ghst', $TotalGroups, 0, '/');

	    

	    	echo $TotalDbees.'~'.$TotalMessages.'~'.$TotalGroups.'~'.$TotalGroupInvite.'~'.$TotalGroupRequests.'~'.$TDBRow['Name'].'~'.$TMRow['Name'].'~'.$TGIRow['Name'].'~'.$TGRRow['Name'].'~'.$TotalComments.'~'.$CommentUserName.'~'.$mycomment.'~'.$LastScoreDate.'~'.$ScoreUser.'~'.$ScoreText.'~'.$ScoreType.'~'.$excerpt;

	    

	    	$response = $this->_helper->layout->disableLayout();

			return $response;

	    }

	  

}



