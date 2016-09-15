<?php

class Admin_ReportingController extends IsadminController
{

	private $options;
	public $defaultimagecheck;
	
    public function init()
    {
	    $this->emailProvider	=	array('gmail','yahoo','live','msn','hotmail');
	    $this->defaultimagecheck = new Admin_Model_Common();
	    $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
	    $this->limit = 20;
	    parent::init();
    }

  

    /**
     * Index Controller
     */
    public function indexAction()
    {
		$userRec	=	new Admin_Model_reporting();
		$deshboard	=	new Admin_Model_Deshboard();

		//echo phpinfo();
	
		/********* SOCIAL PROVDERS ACTION ************/
			$socialArr			=	array();
			$Socialproviders	=	array();
			$Socialproviderdbs	=	array();

			$totalsocialRec	=	$userRec->getSocialUsers('','pie');   // Total Email ids
		
			foreach ($totalsocialRec as $key => $value)
				$Socialproviders[]	=	array('name'=> $value['sharetype'],'y'=> (int)$value['totShare'],'sliced'=> false, 'selected'=> false);
			
			$this->view->SocialProvidersdata 	=	json_encode($Socialproviders);

			//graph 
			$totalBrowserRec	=	$userRec->getSocialUsers('','',5);   // Total Email ids
			$totalsharedata	=	array();
			
			$dbdescription	=	array();
			$userUnameArr	=	array();
			$userPictureArr	=	array();


				
			foreach ($totalBrowserRec as $key => $value) 
			{
				$totalsharedata[]	=	array('y'=> (int)$value['totdbs']);

				if($value['Type']==1)
		            $dbdescription[] = htmlentities(substr($value['Text'],0,20));
		        else if($value['Type']==2) { 
		            $dbUserLinkDesc 	=   !empty($value['UserLinkDesc']) ? $value['UserLinkDesc'] : $value['LinkTitle'];
		            $dbdescription[]    =   htmlentities(substr($dbUserLinkDesc,0,20));
		        }
		        else if($value['Type']==3) 
		        	$dbdescription[]  =   htmlentities(substr($value['PicDesc'],0,'20'));
		        else if($value['Type']==4) { 
		            $dbVidDesc      =   $value['VidDesc'];
		            $dbdescription[]     =   htmlentities(substr($value['VidDesc'],0,'20'));
		        }
		        else if($value['Type']==5)            
		            $dbdescription[] =htmlentities(substr($value['PollText'],0,'20'));
		        

				$dburlArr[]			=	array($value['dbeeid']);
				$dbuserUnameArr[]	=	array($this->myclientdetails->customDecoding($value['Name']));
				$dbuserImageArr[]	=	array($value['ProfilePic']);
			}

			$totsocialproviders 	=	json_encode($totalsharedata);
			$uNameProvidersdata 	=	json_encode($dbuserUnameArr);
			
			$this->view->dburlProvidersdata 	=	json_encode($dburlArr);
			$this->view->userImageProvidersdata  =	json_encode($dbuserImageArr);
			$this->view->dbDescrProvidersdata   =	json_encode($dbdescription);		
			
			$this->view->socialdbProvidersdata 	=	($totsocialproviders);	
			$this->view->socialproviderscategory =	($uNameProvidersdata);




		/********* EMAIL PROVDERS ACTION ************/
			$fieldArr		=	array();
			$Emailproviders	=	array();
			$others			=	0;

			$totalRec	=	$userRec->getReportUsers();   // Total Email ids

			foreach ($this->emailProvider as $key => $value) {
				$fieldArr[$value] =	$userRec->getReportUsers($value);
				$others	 += $userRec->getReportUsers($value);
			}

			foreach ($fieldArr as $key => $value)
				$Emailproviders[]	=	array('name'=> $key,'y'=> (int)$value,'sliced'=> false, 'selected'=> false);
			
				$Emailproviders[]	=	array('name'=> 'others','y'=> (int)($totalRec-$others),'sliced'=> false, 'selected'=> false);	

			$this->view->emailProvidersdata 	=	json_encode($Emailproviders);

		/********* POST VISITERS PROVDERS ACTION ************/

			$browserArr	=	array();
			$browserProvidersdata	=	array();
		
			$totalBrowserRec	=	$userRec->getBrowserUsers();   // Total Email ids

			foreach ($totalBrowserRec as $key => $value) 
			{
				$browserProvidersdata[]	=	array('y'=> (int)$value['totBrowser']);
				$browserArr[]		=	array($value['browser']);
			}
		
			$this->view->browserproviderscategory 	=	json_encode($browserArr);
			$this->view->browserProvidersdata 		=	json_encode($browserProvidersdata);

		/********* BROWSER PROVDERS ACTION ************/

			$browserArr	=	array();
			$browserProvidersdata	=	array();
		
			$totalBrowserRec	=	$userRec->getBrowserUsers();   // Total Email ids

			foreach ($totalBrowserRec as $key => $value) 
			{
				$browserProvidersdata[]	=	array('y'=> (int)$value['totBrowser']);
				$browserArr[]		=	array($value['browser']);
			}
		
			$this->view->browserproviderscategory 	=	json_encode($browserArr);
			$this->view->browserProvidersdata 		=	json_encode($browserProvidersdata);

		/********* OS PROVDERS ACTION ************/

			$osArr	=	array();
			$osProvidersdata	=	array();
			$totalosRec	=	$userRec->getOsUsers();   // Total Email ids
			foreach ($totalosRec as $key => $value) 
			{
				$osProvidersdata[]	=	array('y'=> (int)$value['totOs']);
				$osArr[]		=	array($value['os']);
			}
			$this->view->osArrcategory 		=	json_encode($osArr);
			$this->view->osProvidersdata 	=	json_encode($osProvidersdata);


			$deviceArr	=	array();
			$deviceProvidersdata	=	array();
			$totaldeviceRec	=	$userRec->getdeviceUsers();   // Total Email ids
			foreach ($totaldeviceRec as $key => $value) 
			{
				$deviceProvidersdata[]	=	array('y'=> (int)$value['totOs']);
				$deviceArr[]		=	array($value['userdevice']);
			}
			$this->view->deviceArrcategory 		=	json_encode($deviceArr);
			$this->view->deviceProvidersdata 	=	json_encode($deviceProvidersdata);

		/********* similar interest based ACTION ************/

			$catArrcategory		=	array();
			$catArrPoints		=	array();
			$catProvidersdata	= 	array();

			$catcmntArrcategory		=	array();
			$a = array();	

			$categories		=	$userRec->getallcategory();
			$countcat = 1;
			foreach ($categories as $keycat => $valuecat) 
			{
				
				$catArrcategory[]	=  '<span class="categorychart" style="color:#'.rand(1000000,6).';font-weight:bold;" cateData="'.$valuecat['catname'].'" > '.$countcat++.'</span>'; //$setCat;//substr($catgry[0],0,10);
				$catArrPoints[]		=	$valuecat['catname'];
				$dbeecatdetailsRec	=	$userRec->getcategoryinterest($valuecat['catid'],'reportdbee');
				$dbeesres[]			=	array((int)count($dbeecatdetailsRec));
				$cmntcatdetailsRec	=	$userRec->getcategoryinterest($valuecat['catid'],'reportcomment');

				$commentsres[]		=	array((int)count($cmntcatdetailsRec));
			}
			$catProvidersdata[]	=	array('name'=>'unique posts', 'data' => $dbeesres,'stack'=>$valuecat['catid'],'stack'=>$valuecat['catid']);
			$catProvidersdata[]	=	array('name'=> 'user comments within posts', 'data' => $commentsres,'stack'=>$valuecat['catid'],'stack'=>$valuecat['catid']);
		
		   	// Total Email ids
			$this->view->catArrPoints 		=	json_encode($catArrPoints);
			$this->view->catArrcategory 	=	json_encode($catArrcategory);
			$this->view->catProvidersdata 	=	json_encode($catProvidersdata);
	}


	/*public function interestcategoryAction()
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$visitingTotal	 	= 	$deshboard->getVisitersPosts('all','',$datefrom, $dateto);
		$visitingChartData 	= 	$deshboard->getVisitersPosts($request['ranglimit'],$request['offset'],$datefrom, $dateto); // Visiters ON PLATEFORM
		
		$totalcommentdata	=	array();
		$dbdescription	=	array();
		$userUnameArr	=	array();
		$userPictureArr	=	array();
		$dburlArr	=	array();
		$pagignation	=	'';

		//	echo "<pre>";print_r($liveDbeeData->toarray());exit;
		foreach ($visitingChartData as $key => $value) 
		{
			if($value['type']!=5)
			{
				$totalcommentdata[]	=	array('y'=> (int)$value['totvisiters']);
				if($value['text']!='') $dbdescription[] = htmlentities(substr($value['text'],0,40));
		    	else $dbdescription[] = htmlentities(substr($value['dburl'],0,40));
		        if($value['type']==5) {             
		            $dbdescription[] =htmlentities(substr($value['PollText'],0,'40'));
		        }
				$dburlArr[]			=	array($value['DbeeID']);
				$dbuserUnameArr[]	=	array($this->myclientdetails->customDecoding($value['username']));
				$userImage 			=	$cmnObj->checkImgExist($value['image'],'userpics','default-avatar.jpg');
				$dbuserImageArr[]	=	array($userImage);
			}
		}
		if(count($visitingTotal)>$request['offset']) 
		{
			$nLimit = 0;
			$pLimit = 0;

			$tRange = $request['ranglimit']+$request['offset'];
			if($tRange<count($visitingTotal))
				$nLimit = $request['ranglimit']+$request['offset'];

			$nextClick = "callglobalajax('visitingcontainer','index','callingajaxcontainers', 'postvisiting','topdebateusers','','',".$nLimit.",".$request['offset'].")"; 
			
			
			
			$pLimit = ($request['ranglimit']-$request['offset']);
			$prevClick = "callglobalajax('visitingcontainer','index','callingajaxcontainers', 'postvisiting','topdebateusers','','',".$pLimit.",".$request['offset'].")"; 
			
			if($nLimit==0) $dNone = "display:none";
			
			if($pLimit<0) $pNone = "display:none";
			

			$pagignation ='<div id="visitingcontainerPaging"><span class="next btn btn-yellow pull-right" style="'.$dNone.'" onclick="'.$nextClick.'">Next <i class="fa fa-chevron-circle-right fa-lg"></i></span>	<span class="Prev btn btn-yellow pull-left" style="'.$pNone.'" onclick="'.$prevClick.'"><i class="fa fa-chevron-circle-left fa-lg"></i> Prev</span></div>';
		}

		if(count($visitingChartData)>0) 
			echo json_encode($totalcommentdata).'~'.json_encode($dbuserUnameArr).'~'.json_encode($dburlArr).'~'.json_encode($dbuserImageArr).'~'.json_encode($dbdescription).'~'.json_encode($pagignation);
		else echo "no";
	}
	*/
	public function worldmapAction()
	{
		
	}
	public function trackingvisitsfilterAction()
    {
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);

		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$request    =   $this->getRequest()->getParams();
	        $userRec	=	new Admin_Model_reporting();
	        $common		=	new Admin_Model_Common();
	        $condition  = 	'';
	        
	        /********* OS PROVDERS ACTION ************/
			$osArr	=	array();
			$osProvidersdata	=	array();
			//$totalosRec	=	$userRec->usertracking('a');   // Total 

			if($request['month']!='')
			{
				$condition = "DATE_FORMAT(`logindate`, '%Y-%m')='".date('Y-m', strtotime($request['month']))."'";
			}
			else
			{
				$condition = "DATE_FORMAT(`logindate`, '%Y-%m-%d') >= '".date('Y-m-d', strtotime($request['fromdate']))."' AND DATE_FORMAT(`logindate`, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($request['todate']))."'" ;
			}

			//$myQue = "select count(id) as totLogins, DATE_FORMAT(`logindate`, '%d-%b') as theDate,count(distinct(userid)) as usersCount from tbluserlogindetails where ".$condition." group by theDate"; 
			$myQue = "select count(id) as totLogins, DATE_FORMAT(`logindate`, '%d') as theDate,count(distinct(log.userid)) as usersCount ,u.UserID,u.ProfilePic,u.Name,u.lname,u.Email from tbluserlogindetails as log INNER JOIN tblUsers AS u ON u.UserID=log.userid  where log.clientID =".clientID." AND ".$condition." group by theDate"; 
			
			$totalosRec	=	$this->myclientdetails->passSQLquery ($myQue);   // Total 

			//print_r($totalosRec); exit;
			foreach ($totalosRec as $key => $value) 
			{
				$loginProvidersdata[]	=	array('y'=> (int)$value['totLogins']);
				$loginArr[]				=	array($value['theDate']);
				$loginUsersArr[]		=	array($value['usersCount']);
			}
			$data['loginArrcategory'] 		=	json_encode($loginArr);
			$data['loginProvidersdata'] 	=	json_encode($loginProvidersdata);
			$data['loginUsersArr'] 			=	json_encode($loginUsersArr);

			

			if($request['fromdate']!='' && $request['todate']!='')
			{
				$dateString = $request['fromdate'].' to '.$request['todate'];
				$chartDataResult 	=	$userRec->usersTracking($dateString,'dateFilter',$this->limit,$request['offset']);

				$chartDataResulttotal	=	count($userRec->usersTracking($dateString,'dateFilter',$this->limit,$request['offset']));

				if($chartDataResulttotal>0)
				{
					$data['monthRecord'] = $common->reportusers_CM($chartDataResult,'<br><span>'.$dateString.'</span>',$dateString,$request['offset'],$request['action'],$newfilename='',$cattype='',$csvdatda,$chartDataResulttotal);
				}
			}
			
		
			if($request['month']!='')
			{
				
				$chartDataResult 	=	$userRec->usersTracking(date('Y-m', strtotime($request['month'])),'fullMonth',$this->limit,$request['offset']);
				$chartDataResulttotal	=	count($userRec->usersTracking(date('Y-m', strtotime($request['month'])),'fullMonth',$this->limit,$request['offset']));
				if($chartDataResulttotal>0)
				{
					$data['monthRecord'] = $common->reportusers_CM($chartDataResult,'<br><span>'.date('F, Y', strtotime($request['month'])).'</span>',date('Y-m', strtotime($request['month'])),$request['offset'],$request['action'],$newfilename='',$cattype='',$csvdatda,$chartDataResulttotal);
				}
			}
		}

		return $response->setBody(Zend_Json::encode($data));
	}

	public function trackingvisitsAction()
    {
    	
        $request    =   $this->getRequest()->getPost();
        $userRec	=	new Admin_Model_reporting();
        
        /********* OS PROVDERS ACTION ************/
		$osArr	=	array();
		$osProvidersdata	=	array();
		//$totalosRec	=	$userRec->usertracking('a');   // Total 

		$myQue = "select count(id) as totLogins, DATE_FORMAT(`logindate`, '%d') as theDate,count(distinct(log.userid)) as usersCount from tbluserlogindetails as log INNER JOIN tblUsers AS u ON u.UserID=log.userid   where log.clientID =".clientID." AND DATE_FORMAT(`logindate`, '%m')='".DATE('m')."' group by theDate"; 
		$totalosRec	=	$this->myclientdetails->passSQLquery ($myQue);   // Total 

		//print_r($totalosRec); exit;
		foreach ($totalosRec as $key => $value) 
		{
			$loginProvidersdata[]	=	array('y'=> (int)$value['totLogins']);
			$loginArr[]				=	array($value['theDate']);
			$loginUsersArr[]		=	array($value['usersCount']);
		}
		$this->view->loginArrcategory 		=	json_encode($loginArr);
		$this->view->loginProvidersdata 	=	json_encode($loginProvidersdata);
		$this->view->loginUsersArr 			=	json_encode($loginUsersArr);
    }

    public function eachdayloginsAction()
	{
		$request 	= 	$this->getRequest()->getParams();
	    $userRec	=	new Admin_Model_reporting();
	    $common		=	new Admin_Model_Common();
	  	$retResult	=	''; 
	  	// For CSV Results
	  	
	  	$csvdatda 	=	'';
	    $chartDataResult 	=	$userRec->usersTracking($request['provider'],'logins',$this->limit,$request['offset']);

	    $chartDataResulttotal	=	count($userRec->usersTracking($request['provider'],'logins','nolimit',$request['offset']));
	    $ne  = explode("to", trim($request['provider']));
	   	
	   	if(count($ne)==2){
	   		$ne2  = explode("  ", trim($ne[0]));
	   		$to    = date('F, Y',strtotime($ne[1]));
	   		$date = $ne2[0].' '.$to;
	   	} else
	   	{
	   		$date = $ne[0]  ;
	   	}

	 
	    echo $common->reportusers_CM($chartDataResult,'<br><span>'.$date.'</span>',$request['provider'],$request['offset'],$request['action'],$newfilename='',$cattype='',$csvdatda,$chartDataResulttotal);
	    exit;
	}

	public function locationsAction()
    {
    	
        $request    =   $this->getRequest()->getPost();
        $userRec	=	new Admin_Model_reporting();
        
        /********* OS PROVDERS ACTION ************/
		$osArr	=	array();
		$osProvidersdata	=	array();
		$totalosRec	=	$userRec->getcontinentusers();   // Total 
		foreach ($totalosRec as $key => $value) 
		{
			$osProvidersdata[]	=	array('y'=> (int)$value['totcontinentcode']);
			$osArr[]		=	array($this->myclientdetails->customDecoding($value['continentcode']));
		}
		$this->view->osArrcategory 		=	json_encode($osArr);
		$this->view->osProvidersdata 	=	json_encode($osProvidersdata);
    }
    public function twittercommentsAction()
    {
    	$request = $this->getRequest()->getParams();
    	$latestCommentData = $this->userRec->getTwitterComment();
    	$page = $this->_getParam('page',1);
		/*
		 * Object of Zend_Paginator
		*/
		$paginator = Zend_Paginator::factory($latestCommentData);
		/*
		 * Set the number of counts in a page
		*/
		$paginator->setItemCountPerPage(10);
		/*
		 * Set the current page number
		*/
		$paginator->setCurrentPageNumber($page);
		
		$this->view->paginator = $paginator;
		$this->view->total = $paginator->getTotalItemCount();
		$this->view->page = $page;
		$this->view->totalpage = $paginator->count();
		$this->view->rectodis = $page*20;
		$this->view->lastpage = $paginator->count();
    	
    }

    public function hashtagAction()
    {
    	$tagvalue = $this->_request->getParam('tag');
    	$this->view->tagvalue = $tagvalue;
    	$countTopDBeeTag = $this->userRec->getsearchHashTopdbee();
    	$countTopCommentTag = $this->userRec->getsearchHashTopComment();
    	$this->view->result = $this->taglogichere($countTopDBeeTag,$countTopCommentTag);
    	if($this->getRequest()->getMethod() == 'POST' || $tagvalue!='')
		{
			$hashtag = trim($this->_request->getPost('hashtag'));
			if($hashtag!='')
				$this->_redirect(BASE_URL.'/admin/reporting/hashtag/tag/'.$hashtag);
    	}
    }

    public function taglogichere($countTopDBeeTag,$countTopCommentTag)
    {
    	if(count($countTopDBeeTag)>0)
		{
			$data = array();
			$result = array();
			foreach ($countTopDBeeTag as  $value) 
			{
				$keywords = preg_split("/[\s,]+/", $value['DbTag']);
				foreach ($keywords as $keywordsvalue)
					$result[] = $keywordsvalue;
			}
			$vals = array_count_values($result);
		} 

		if(count($countTopCommentTag)>0)
		{
			$data2 = array();
			$result2 = array();
			foreach ($countTopCommentTag as  $value2) 
			{
				$keywords2 = preg_split("/[\s,]+/", $value2['DbTag']);
				foreach ($keywords2 as $keywordsvalue2)
					$result2[] = $keywordsvalue2;
			}
			$vals2 = array_count_values($result2);
		}

		if(!empty($vals2) && !empty($vals))
			$vals3 = array_merge($vals2, $vals);
		else if(!empty($vals2))
			$vals3 = $vals2;
		else if(!empty($vals))
			$vals3 = $vals;
		else if(empty($vals) && empty($vals2))
			$vals3 = array();
		arsort($vals3);
		return $vals3;
    }

    public function ajaxhashtagAction()
    {
    	$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);

		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$tagvalue = $this->_request->getPost('tag');
			$num = (int) $this->_request->getPost('paging');
    		$TotalDBee = $this->userRec->getAllHashTagUser($tagvalue,1);
    		$TotalDBeeUser = $this->userRec->getAllHashTagUser($tagvalue);
    		$count = count($TotalDBee);
    		
    		if($count!=0)
    		{
    			if($count>1)
    				$text =' users';
    			else
    				$text = ' user';

    			$content.='<input type="hidden" name="PostPage" value="2" id="PostPage"  /><input type="hidden" name="action2" value="post" id="action2"  />
    			<input type="hidden" name="AllCount" value="'.$count.'" id="AllCount"  />
    			<input type="hidden" name="cattype" value="AllUserTag" id=cattype"  />
    			<h3 class="allHasTagUser"> #'.$tagvalue.' has been used by <span class="userPostcounter">'.$count.$text.'</span></h3> 
    			<ul class="tagTab"> 
    				<li><a href="javascript:void(0);" rel="allTag" class="active">All users</a> </li>
    				<li><a href="javascript:void(0);" rel="PostTag">Users who have posted</a> </li>
    				<li><a href="javascript:void(0);" rel="CommentTag">Users who have commented</a> </li>
    			</ul>
      			';
      			$content.='<div id="searchresulthide" class="listing accountContainer"  style="display:block; margin-left:0px;overflow: inherit;">';
      			$content.='<div  class="hasTagSlider postTagList"><ul  class="scoredList slides postTagListUL" >';
           		foreach ($TotalDBee as  $liveDbee) 
           		{	
           			$liveDbeepic = $this->common_model->checkImgExist($liveDbee['ProfilePic'],'userpics','default-avatar.jpg');
           			$name = (empty($liveDbee['Name']))?$liveDbee['Username']:$liveDbee['Name'];
					$content.='<li class="listTagProfile">
					<div class="listUserPhoto">
						<img src="'.IMGPATH.'/users/medium/'.$liveDbeepic.'" width="90" height="90"  border="0" class="recievedUsePic"/>
					</div>
					<div class="ursNm oneline">'.$this->myclientdetails->customDecoding($name).'</div>
					<div>
					<a userid="'.$liveDbee['UserID'].'" class="show_details_user" href="javascript:void(0)">'.$this->myclientdetails->customDecoding($liveDbee['Username']).'</a>
					<div class="saveTolistCheckbox">
					<label class="saveToList">						
						<input class="goupuser" name="goupuserid" value="'.$liveDbee['UserID'].'" type="checkbox" id="save_'.$liveDbee['UserID'].'" value="'.$liveDbee['User'].'" name="goupuserid">
						<label for="save_'.$liveDbee['UserID'].'"></label>	Save to list
					</label>
					</div>
					</li>';
          		} 
         		$content.='</ul></div>';
         		$content.='<div class="userCheckAllHashUser">'.$this->chkboxhtml($Totalcount,'postallhash').'</div>';
         		$content.='<div class="rpGraphTop clearfix expUsrExl">
	    		<form method="post"  action="/admin/reporting/getcsv">
					<a style="margin-top:0px" href="javascript:void(0)" class="rpCsvExport ">
						Export all users CSV <span class="kcSprite CsvIcon"></span> 
					</a>
					<input type="hidden" value="'.$tagvalue.'" name="csvtag">
					<input type="hidden" value="" name="seachfieldChk">
					<input type="hidden" value="" name="orderfieldChk">
					<input type="hidden" value="" name="csvtagtype">
					<input type="hidden" value="users list" name="filename">
					<input type="hidden" value="  " name="records">
				</form></div>
         		'.$this->common_model->addtogroupbutton(1).'
         		<div class="clearfix"></div>';
				if($count>6){
					$content.='<a href="javascript:void(0);" class="viewMoreList PostMoreList" >view more</a>';
				}
      			$content.='</div>';         
      			$data['success']='success';
      			
    		}else{
    			$content.='<div style="padding:10px"><h2>No records found</h2><div>';
    			$content.='';    			
    		}
    		
    		$data['content']=$content;
		}
		return $response->setBody(Zend_Json::encode($data));
    }

    public function ajaxposthashtagAction()
    {
    	$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);

		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$tagvalue = $this->_request->getPost('tag');
			$num = (int) $this->_request->getPost('paging');
    		$TotalDBee = $this->userRec->getAllPostHashTagUser($tagvalue,1);
    		$TotalDBeeUser = $this->userRec->getAllPostHashTagUser($tagvalue);
    		$count = count($TotalDBeeUser);
    		
    		if($count!=0)
    		{
    			$content.='<input type="hidden" name="PostPage" value="2" id="PostPage"  />
    			<input type="hidden" name="action2" value="post" id="action2"  />
    			<input type="hidden" name="postCount" value="'.$count.'" id="postCount"  />
    			<input type="hidden" name="cattype" value="post" id=cattype"  />';
      			$content.='<div id="searchresulthide" class="listing accountContainer"  style="display:none; margin-left:0px;">';
      			$content.='<div  class="hasTagSlider postTagList"><ul  class="scoredList slides postTagListUL" >';
           		foreach ($TotalDBee as  $liveDbee) 
           		{	
           			$liveDbeepic = $this->common_model->checkImgExist($liveDbee['ProfilePic'],'userpics','default-avatar.jpg');
           			$name = (empty($liveDbee['Name']))?$liveDbee['Username']:$liveDbee['Name'];
					$content.='<li class="listTagProfile">
					<div class="listUserPhoto">
						<img src="'.IMGPATH.'/users/medium/'.$liveDbeepic.'" width="90" height="90" border="0" class="recievedUsePic"/>
					</div>
					<div class="ursNm oneline">'.$this->myclientdetails->customDecoding($name).'</div>
					<div><a userid="'.$liveDbee['UserID'].'" class="show_details_user" href="javascript:void(0)">'.$this->myclientdetails->customDecoding($liveDbee['Username']).'</a></div>
					<div class="saveTolistCheckbox">
					<label class="saveToList">						
						<input class="goupuser" name="goupuserid" value="'.$liveDbee['UserID'].'" type="checkbox" id="savepost_'.$liveDbee['UserID'].'" value="'.$liveDbee['User'].'" name="goupuserid">
						<label for="savepost_'.$liveDbee['UserID'].'"></label>	Save to list
					</label>
					</div>
					</li>';
          		} 
         		$content.='</ul></div>';
         		$content.='<div class="userCheckAllHashUser">'.$this->chkboxhtml($Totalcount,'posthash').'</div>';
         		$content.='<div class="rpGraphTop clearfix expUsrExl">
	    		<form method="post"  action="/admin/reporting/getcsv">
					<a style="margin-top:0px" href="javascript:void(0)" class="rpCsvExport ">
						Export all users CSV <span class="kcSprite CsvIcon"></span> 
					</a>
					<input type="hidden" value="'.$tagvalue.'" name="csvtag">
					<input type="hidden" value="" name="seachfieldChk">
					<input type="hidden" value="" name="orderfieldChk">
					<input type="hidden" value="post" name="csvtagtype">
					<input type="hidden" value="users list" name="filename">
					<input type="hidden" value="  " name="records">
				</form></div>
         		'.$this->common_model->addtogroupbutton(1).'
         		<div class="clearfix"></div>';
				if($count>6){
					$content.='<a href="javascript:void(0);" class="viewMoreList PostMoreList" >view more</a>';
				}
      			$content.='</div>';         
      			$data['success']='success';
      			
    		}else{
    			$content.='<div style="padding:10px"><h2>No records found</h2><div>';
    			$content.='';
    			
    		}
    		
    		$data['content']=$content;
		}
		return $response->setBody(Zend_Json::encode($data));
    }


    public function ajaxcommenthashtagAction()
    {
    	$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);

		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$tagvalue = $this->_request->getPost('tag');
			$num = (int) $this->_request->getPost('paging');
    		$TotalDBee = $this->userRec->getAllCommentHashTagUser($tagvalue,1);
    		$TotalDBeeUser = $this->userRec->getAllCommentHashTagUser($tagvalue);
    		$count = count($TotalDBeeUser);
    		
    		if($count!=0)
    		{
    			$content.='<input type="hidden" name="PostPage" value="2" id="PostPage"  />
    			<input type="hidden" name="action2" value="post" id="action2"  />
    			<input type="hidden" name="commentCount" value="'.$count.'" id="commentCount"  />
    			<input type="hidden" name="cattype" value="comment" id=cattype"  /></h2>';
      			$content.='<div id="searchresulthide" class="listing accountContainer"  style="display:none; margin-left:0px;">';
      			$content.='<div  class="hasTagSlider postTagList"><ul  class="scoredList slides postTagListUL" >';
           		foreach ($TotalDBee as  $liveDbee) 
           		{	
           			$liveDbeepic = $this->common_model->checkImgExist($liveDbee['ProfilePic'],'userpics','default-avatar.jpg');
           			$name = (empty($liveDbee['Name']))?$liveDbee['Username']:$liveDbee['Name'];
					$content.='<li class="listTagProfile">
					<div class="listUserPhoto">
						<img src="'.IMGPATH.'/users/medium/'.$liveDbeepic.'" width="90" height="90" border="0" class="recievedUsePic"/>
					</div>
					<div class="ursNm">'.$this->myclientdetails->customDecoding($name).'</div>
					<div><a userid="'.$liveDbee['UserID'].'" class="show_details_user" href="javascript:void(0)">'.$this->myclientdetails->customDecoding($liveDbee['Username']).'</a></div>
					<div class="saveTolistCheckbox">
					<label class="saveToList">						
						<input class="goupuser" name="goupuserid" value="'.$liveDbee['UserID'].'" type="checkbox" id="savecomment_'.$liveDbee['UserID'].'" value="'.$liveDbee['User'].'" name="goupuserid">
						<label for="savecomment_'.$liveDbee['UserID'].'"></label>	Save to list
					</label>
					</div>
					</li>';
          		} 
         		$content.='</ul></div>';
         		$content.='<div class="userCheckAllHashUser">'.$this->chkboxhtml($Totalcount,'commenthash').'</div>';
         		$content.='<div class="rpGraphTop clearfix expUsrExl">
	    		<form method="post"  action="/admin/reporting/getcsv">
					<a style="margin-top:0px" href="javascript:void(0)" class="rpCsvExport ">
						Export all users CSV <span class="kcSprite CsvIcon"></span> 
					</a>
					<input type="hidden" value="'.$tagvalue.'" name="csvtag">
					<input type="hidden" value="" name="seachfieldChk">
					<input type="hidden" value="" name="orderfieldChk">
					<input type="hidden" value="comments" name="csvtagtype">
					<input type="hidden" value="users list" name="filename">
					<input type="hidden" value="  " name="records">
				</form></div>
         		'.$this->common_model->addtogroupbutton(1).'
         		<div class="clearfix"></div>';
				if($count>6){
					$content.='<a href="javascript:void(0);" class="viewMoreList PostMoreList" >view more</a>';
				}
      			$content.='</div>';         
      			$data['success']='success';
      			
    		}else{
    			$content.='<div style="padding:10px"><h2>No records found</h2><div>';
    			$content.='';
    			
    		}
    		
    		$data['content']=$content;
		}
		return $response->setBody(Zend_Json::encode($data));
    }

    public function countrycontainerAction()
    {
    	
        $request    =   $this->getRequest()->getPost();
        $userRec	=	new Admin_Model_reporting();
        $common	 	=	new Admin_Model_Common();

		$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->layout()->disableLayout();

        if($request['calling']=='countryusers')
        {
        	$retResult	=	'';
		  	$catgry 			=	str_replace('<br>',' ',$request['category']); 
		  	$arr = explode(' ',trim($catgry));

		  	// For CSV Results
  			$csvdatda 	=	'';
			
  			$continentcode = $this->myclientdetails->customEncoding($request['continentcode']); 
		    
		    $chartDataResult 	=	$userRec->getcontinentusers($continentcode,'countryusers',$this->limit,$request['offset']); 
		  
		    $chartDataResulttotal 	=	count($userRec->getcontinentusers($continentcode,'countryusers','nolimit',''));
		  
		    echo $common->reportusers_CM($chartDataResult,'Country<br><span>'.$request['continentcode'].'</span>',$request['continentcode'], $request['offset'],$request['action'],str_replace(" ", "_", $request['continentcode']),'',$csvdatda,$chartDataResulttotal);
		    exit;
		    
        }
        else 
        {
	        /********* Country PROVDERS ACTION ************/
			$osArr	=	array();
			$osProvidersdata	=	array();
			$provider = $this->myclientdetails->customEncoding($request['provider']);
			$totalosRec	=	$userRec->getcontinentusers($provider);   // Total Email ids
			foreach ($totalosRec as $key => $value) 
			{
				$osProvidersdata[]	=	array('y'=> (int)$value['totcountrycode']);
				$osArr[]		=	array($this->myclientdetails->customDecoding( $value['countrycode']));
			}
			$osArrcategory 		=	json_encode($osArr);
			$osProvidersdata 	=	json_encode($osProvidersdata);

			echo $osArrcategory.'~'.$osProvidersdata.'~'.$request['provider'];
			exit;
			
		}
    }

	/**
     * Index Controller// use it when improve to scrolling display results
     */
	public function topuserssearchAction() 
    {
		$userRec	=	new Admin_Model_User();
		$request = $this->getRequest()->getParams();
		if(isset($request['searchfield']) && $request['searchfield']!='')
			$seachfield = $request['searchfield'];
		else
			$seachfield = '';
		
		$orderfieldChk = '';

		$this->view->search = $seachfield;
		$userData = $userRec->getUsers($this->myclientdetails->customEncoding($seachfield),'',$orderfieldChk);
		

		$this->view->totUsers	=	 count($userData);

		$this->view->paginator = $userData;

		$retResult 	=	'';

		//$retResult .= "<br>Total : ".(count($userData)).'<br><br><br>';
		if ((count($userData))>0){ 
		$retResult .= '<div id="liveDbee" class="topContent">
			<ul>';
			foreach($userData as $liveDbee) :
				$checkupic = $this->defaultimagecheck->checkImgExist($liveDbee->ProfilePic,'userpics','default-avatar.jpg');
		$retResult .= '<li>
			<div class="compairBtn"><a href="#" class="btn show_details_user" userid="'.($liveDbee->UserID) .'">View history</a><br /><a href="#" class="btn btn-green">+ Compaire user</a> </div>
			<div class="userPic">
				<img src="'.IMGPATH.'/users/medium/'.$checkupic.'" width="70" height="70" border="0" />
			</div>
			<div class="userDetails">
				<div class="usertitle">
					<span class="username">'. ($liveDbee->Name).'</span><br />
					<h3></h3>
				</div>
				
			</div>

		</li>';
		 endforeach; 
		$retResult .= '</ul>

		</div>';

	
		} else { 
			$retResult .= '<div class="dashBlockEmpty">Users Not Found!</div>';
		} 
		echo $retResult.'~'.count($userData);
		
	}

	public function compareusersAction()
    {
    	
        $request    =   $this->getRequest()->getPost();

        if(isset($request['searchfield']) && $request['searchfield']!='')
		$seachfield = $request['searchfield'];
		else
		$seachfield = '';
	
		$this->view->search = $seachfield;
        
        $this->view->userslist	=	$request['comparedid'];

    }
	public function topusersAction()
    {
		$userRec	=	new Admin_Model_User();

		$reporting	=	new Admin_Model_reporting();

		$request = $this->getRequest()->getParams();
		if(isset($request['searchfield']) && $request['searchfield']!='')
		$seachfield = $request['searchfield'];
		else
		$seachfield = '';
		$orderfieldChk = '';
		//echo '=>'.$seachfield;
		if(empty($seachfield))
		{		
		
			$liveDbeeData = $reporting->gettopDbeeusers(5);
			$this->view->liveDbeeData = $liveDbeeData;
			
			$liveGroupData = $reporting->gettopLiveGroup(5);
			$this->view->liveGroupData = $liveGroupData;
			
			$latestCommentData = $reporting->gettopLatestComment(5);
			$this->view->latestCommentData = $latestCommentData;
			
			$liveScoreData = $reporting->gettopLiveScore(5);
			$this->view->liveScoreData = $liveScoreData;
		} 
		else
		{	
			$this->view->search = $seachfield;
			$userData = $userRec->getUsers('','haveUid','',$this->myclientdetails->customEncoding($seachfield,'topuser'));
			$this->view->totUsers	=	 count($userData);
			$this->view->paginator = $userData;
		}
		

	}

	public function activeusersrecordAction()
    {
		$userRec	=	new Admin_Model_User();
		$reporting	=	new Admin_Model_reporting();
		$request = $this->getRequest()->getParams();
		if(isset($request['searchfield']) && $request['searchfield']!='')
		$seachfield = $request['searchfield'];
		else
		$seachfield = '';
		$orderfieldChk = '';
		if(empty($seachfield))
		{			
			$liveDbeeData = $reporting->gettopDbeeusers('all');
			$this->view->liveDbeeData = $liveDbeeData;
			$this->view->totUsers	=	 count($liveDbeeData);
		} 
		else
		{	
			$this->view->search = $seachfield;
			$userData = $userRec->getUsers('','haveUid','',$this->myclientdetails->customEncoding($seachfield,'topuser'));
			$this->view->totUsers	=	 count($userData);
			$this->view->paginator = $userData;
		}
	}

	public function getcsvAction()
	{
		$request 	= 	$this->getRequest()->getParams();
		$userRec	=	new Admin_Model_reporting();
	    $common		=	new Admin_Model_Common();
	    $u 			= new Admin_Model_User();

		if(!empty($request['calling']) && ($request['calling']!='usersList'))
		{

		    if($request['action2']=='emailproviders')
		    	$d = $userRec->getReportUsers( $request['provider'],$this->emailProvider,'records','nolimit');
		    else if($request['action2']=='browers')
		    	$d = $userRec->getBrowserUsers( $request['provider'],'browser','nolimit');
		    else if($request['action2']=='osusers')
		    	$d = $userRec->getOsUsers( $request['provider'],'os','nolimit');
		    else if($request['action2']=='categoryusers')
		    	$d = $userRec->getcategoryinterestusers( $request['category'],$request['cattype'],'chkusers','nolimit');
		    else if($request['action2']=='postvisiters')
		    	$d = $userRec->getPostVisiters( $request['provider'],'','nolimit','');
		    else if($request['action2']=='totalsocialusers')
		    	$d 	=	$userRec->getSocialUsers($request['provider'],'pie','nolimit','');
		    else if($request['action2']=='socialusers')
		    	$d 	=	$userRec->getSocialUsers($request['provider'],'','nolimit','');
		    else if($request['action2']=='eachdaylogins' || $request['action2']=='trackingvisitsfilter')
		    	$d 	=	$userRec->usersTrackingexport($request['provider'],'','nolimit','');
		    else
		    	$d = $userRec->getcontinentusers($this->myclientdetails->customEncoding( $request['continentcode']),'countryusers','nolimit');
		    $common->getusersoncsv_CM($d,$request['filename'],$request['action2'],$request['provider']);
		}
		else if($request['calling']=='usersList' && empty($request['calling2']))
		{
			$userData = $u->getUsers($request['seachfieldChk'],'',$request['orderfieldChk']);
			$d = $userData->toarray();
			$common->getusersoncsv_CM($d,$request['filename'],'usersection');
		}
		else if($request['csvtag']!='' && $request['csvtagtype']=='comments')
		{
			$userData = $userRec->getAllCommentHashTagUser($request['csvtag']);
			$common->getusersoncsv_CM($userData,$request['filename'],'usersection');
		}
		else if($request['csvtag']!='' && $request['csvtagtype']=='')
		{		
			$userData = $userRec->getAllHashTagUser($request['csvtag']);
			$common->getusersoncsv_CM($userData,$request['filename'],'usersection');
		}
		else if($request['csvtag']!='' && $request['csvtagtype']=='post')
		{
			$userData = $userRec->getAllPostHashTagUser($request['csvtag']);
			$common->getusersoncsv_CM($userData,$request['filename'],'usersection');
		}
		else if($request['calling']=='usersList' && $request['calling2']=='usersListvip' )
		{
			$userData = $u->getUsers($request['seachfieldChk'],'vipuser',$request['orderfieldChk']);
			$d = $userData->toarray();
			$common->getusersoncsv_CM($d,$request['filename'],'usersection');
		}
	    else 
	    {
	    	$userData = $u->getUsers($request['seachfieldChk'],'',$request['orderfieldChk']);
			$d = $userData->toarray();
			$common->getusersoncsv_CM($d,$request['filename'],'usersection');
	    }
	}	

	public function categoryusersAction()
	{
		$request 	= 	$this->getRequest()->getParams();
	    $userRec	=	new Admin_Model_reporting();
	    $common		=	new Admin_Model_Common();
	    $catparam 	=	 $request['category'];
	  	$retResult	=	'';
	  	// For CSV Results
	  	$csvdatda 	=	'';
	  	// For users data
	    $chartDataResult 	=	$userRec->getcategoryinterestusers( $request['category'],$request['cattype'],'chkusers',$this->limit,$request['offset']);
	    $chartDataResulttotal 	=	count($userRec->getcategoryinterestusers( $request['category'],$request['cattype'],'chkusers','nolimit',$request['offset']));
	   	$filename = str_replace(" ","_",($request['cattype'].'_'.$request['category']));
	  	$retResult	= $common->reportusers_CM($chartDataResult,ucfirst($request['cattype']).'<br><span>Category: '.$request['category'],$filename, $request['offset'],$request['action'],$catparam,$request['cattype'],$csvdatda,$chartDataResulttotal);
		echo $retResult;
	   // echo $retResult.'~'. $request['category'].'~'.$request['cattype'].'~chkusers';
	    exit;
	}	
	public function postvisitersAction()
	{
		$request 	= 	$this->getRequest()->getParams();
		
	    $userRec	=	new Admin_Model_reporting();
	    $common		=	new Admin_Model_Common();
	  
	  	$retResult	=	'';
	    // For CSV Results
			$csvdatda 	=	'';
	
	    $visDataResult 	=	$userRec->getPostVisiters($request['provider'],'dbstats',$this->limit,$request['offset']);
	    $visDataResultcount 	=	count($userRec->getPostVisiters($request['provider'],'dbstats','nolimit',$request['offset']));

			
        $dbdescription = htmlentities(substr($visDataResult[0]['text'],0,100));
        if($visDataResult[0]['type']==5) {             
            $dbdescription =htmlentities(substr($visDataResult[0]['PollText'],0,'100'));
        }
        if($dbdescription=='')
        {
        	$dbdescription =htmlentities(substr($visDataResult[0]['dburl'],0,'100'));
        }
	

	    echo $common->reportusers_CM($visDataResult,'Post<br><span> '.$dbdescription.'</span>',$request['provider'],$request['offset'],'postvisiters',$newfilename='',$request['provider'],$csvdatda,$visDataResultcount);
	    exit;
	}

	public function osusersAction()
	{
		$request 	= 	$this->getRequest()->getParams();
	    $userRec	=	new Admin_Model_reporting();
	    $common		=	new Admin_Model_Common();
	  	$retResult	=	''; 
	  	// For CSV Results
	  	$csvdatda 	=	'';
	    $chartDataResult 	=	$userRec->getOsUsers($request['provider'],'os',$this->limit,$request['offset']);
	    $chartDataResulttotal	=	count($userRec->getOsUsers($request['provider'],'os','nolimit',$request['offset']));    
	    echo $common->reportusers_CM($chartDataResult,'Provider<br><span>OS: '.$request['provider'].'</span>',$request['provider'],$request['offset'],$request['action'],$newfilename='',$cattype='',$csvdatda,$chartDataResulttotal);
	    exit;
	}

	public function deviceusersAction()
	{
		$request 	= 	$this->getRequest()->getParams();
	    $userRec	=	new Admin_Model_reporting();
	    $common		=	new Admin_Model_Common();
	  	$retResult	=	''; 
	  	// For CSV Results
	  	$csvdatda 	=	'';
	    $chartDataResult 	=	$userRec->getdeviceUsers($request['provider'],'device',$this->limit,$request['offset']);
	    $chartDataResulttotal	=	count($userRec->getdeviceUsers($request['provider'],'device','nolimit',$request['offset']));    
	    echo $common->reportusers_CM($chartDataResult,'Provider<br><span>OS: '.$request['provider'].'</span>',$request['provider'],$request['offset'],$request['action'],$newfilename='',$cattype='',$csvdatda,$chartDataResulttotal);
	    exit;
	}

	public function socialsharedpostsAction()
	{
		$request 	= 	$this->getRequest()->getParams();
		
	    $reportin	=	new Admin_Model_reporting();
	    $common		=	new Admin_Model_Common();
	  
	  	$retResult	=	''; 
	  	$csvdatda 	=	'';

	    $chartDataResult 	=	$reportin->getSocialUsers('sharedpost','',$this->limit,$request['offset']);
		

		$retResult	.='
			<div class="responsiveTable"><table class="reportingTable table-border table table-hover table-stripe">
			<thead>
				<tr>
					<td class="rpfirstTd">	Name </td>';
			$retResult	.='<td>Email</td>';
			$retResult	.='<td>Post</td>';
			$retResult	.='<td>Share on</td>';
			$retResult	.='<td>Post Report</td>';
			
			$retResult	.='</tr></thead>';
		

		foreach ($chartDataResult as $key => $value) {
			$dbdescription 		=	htmlentities(substr($value['Text'],0,'20'));
			$retResult	.='<tr><td>'. $this->myclientdetails->customDecoding(htmlentities($value['Name'])).'</td>';
			$retResult	.='<td>'. $this->myclientdetails->customDecoding(htmlentities($value['Email'])).'</td>';
			$retResult	.='<td>'. $dbdescription.'</td>';
			$retResult	.='<td>'. htmlentities($value['sharetype']).'</td>';
			$retResult	.='<td><a href="'.BASE_URL.'/admin/dashboard/postreport/m_-_xxp=t/'. base64_encode($value['DbeeID']).'">view report</a></td>';
			$retResult	.='</tr>';
		}
		$retResult	.='</table></div>';
	    echo $retResult;
	   exit; 	
	}

	public function socialusersAction()
	{
		$request 	= 	$this->getRequest()->getParams();
		
	    $userRec	=	new Admin_Model_reporting();
	    $common		=	new Admin_Model_Common();
	  
	  	$retResult	=	''; 
	  	// For CSV Results
	  	$csvdatda 	=	'';

	    $chartDataResult 	=	$userRec->getSocialUsers($request['provider'],'',$this->limit,$request['offset']);

	    $chartDataResulttotal	=	count($userRec->getSocialUsers($request['provider'],'','nolimit',$request['offset']));

	    if($chartDataResult[0]['Type']==1)
	        $dbdescription = htmlentities(substr($chartDataResult[0]['Text'],0,20));
	    else if($chartDataResult[0]['Type']==2) { 
	        $dbUserLinkDesc 	=   !empty($chartDataResult[0]['UserLinkDesc']) ? $chartDataResult[0]['UserLinkDesc'] : $chartDataResult[0]['LinkTitle'];
	        $dbdescription    =   htmlentities(substr($dbUserLinkDesc,0,20));
	    }
	    else if($chartDataResult[0]['Type']==3)
	    	$dbdescription[]  =   htmlentities(substr($chartDataResult[0]['PicDesc'],0,'20'));               
	    
	    else if($chartDataResult[0]['Type']==4) { 
	        $dbVidDesc      =   $chartDataResult[0]['VidDesc'];
	        $dbdescription     =   htmlentities(substr($chartDataResult[0]['VidDesc'],0,'20'));
	    }
	    else if($chartDataResult[0]['Type']==5)          
	        $dbdescription =htmlentities(substr($chartDataResult[0]['PollText'],0,'20'));
	    
	    echo $common->reportusers_CM($chartDataResult,'Users who shared this post<br><div class="shareUserPostWrp"><strong>'.$dbdescription.'</strong> <a href="'.BASE_URL.'/dbee/'.$chartDataResult[0]['dburl'].'" class="bx bx-yellow"> see post</a></div>',$request['provider'],$request['offset'],$request['action'],$newfilename='',$cattype='',$csvdatda,$chartDataResulttotal);
	   exit; 	
	}

	public function totalsocialusersAction()
	{
		$request 	= 	$this->getRequest()->getParams();
	    $userRec	=	new Admin_Model_reporting();
	    $common		=	new Admin_Model_Common();
	  	$retResult	=	''; 
	  	// For CSV Results
	  	$csvdatda 	=	'';
	    $chartDataResult 	=	$userRec->getSocialUsers($request['provider'],'pie',$this->limit,$request['offset']);
	    $chartDataResulttotal	=	count($userRec->getSocialUsers($request['provider'],'pie','nolimit',$request['offset']));
	    echo $common->reportusers_CM($chartDataResult,'Shared on   <br><span>'.$chartDataResult[0]['sharetype'].'</span>',$request['provider'],$request['offset'],$request['action'],$newfilename='',$cattype='',$csvdatda,$chartDataResulttotal,'pie');
	    exit;
	}

	// Checking Browser domain type i.e. safari,Opera

	public function browsertypesAction()
	{
		$request 	= 	$this->getRequest()->getParams();
		$userRec	=	new Admin_Model_reporting();
		$fieldArr	=	array();
		$providers	=	array();
		$others		=	0;
		$totalRec	=	$userRec->getBrowserUsers();   // Total Email ids
		foreach ($totalRec as $key => $value) 
		{
			$providers[]	=	array('y'=> (int)$value['totBrowser']);
			$fieldArr[]		=	array($value['browser']);
		}
		$this->view->providerscategory 	=	json_encode($fieldArr);
		$this->view->providersdata 		=	json_encode($providers);

	}

	public function browersAction()
	{
		$request 	= 	$this->getRequest()->getParams();
	    $userRec	=	new Admin_Model_reporting();
	    $common		=	new Admin_Model_Common();
	  	$retResult	=	'';
	  	// For CSV Results
		$csvdatda 	=	'';
	    $chartDataResult 	=	$userRec->getBrowserUsers($request['provider'],'browser',$this->limit,$request['offset']);
	    $chartDataResultcount 	=	count($userRec->getBrowserUsers($request['provider'],'browser','nolimit',$request['offset']));
	    echo $common->reportusers_CM($chartDataResult,'Provider<br><span> '.$request['provider'].'</span>',$request['provider'],$request['offset'],$request['action'],$newfilename='',$cattype='',$csvdatda,$chartDataResultcount);
	    exit;
		
	}


	// Checking Email domain type i.e. gmail, yahoo

	public function emailtypesAction()
	{
		$request 	= 	$this->getRequest()->getParams();
		$userRec	=	new Admin_Model_reporting();
		
		$fieldArr	=	array();
		$providers	=	array();
		$others		=	0;

		$totalRec	=	$userRec->getReportUsers();   // Total Email ids

		foreach ($this->emailProvider as $key => $value) {
			$fieldArr[$value] =	$userRec->getReportUsers($value);
			$others	 += $userRec->getReportUsers($value);
		}

		foreach ($fieldArr as $key => $value) 
			$providers[]	=	array('name'=> $key,'y'=> (int)$value,'sliced'=> false, 'selected'=> false);
		
		$providers[]	=	array('name'=> 'Others','y'=> (int)($totalRec-$others),'sliced'=> false, 'selected'=> false);	

		$this->view->providersdata 	=	json_encode($providers);

	}
	public function emailprovidersAction()
	{
		$request 	= 	$this->getRequest()->getParams();
		
	    $userRec	=	new Admin_Model_reporting();
	    $common		=	new Admin_Model_Common();
	  
	  	$retResult	=	'';
	    // For CSV Results
			$csvdatda 	=	'';
	    $chartDataResult 	=	$userRec->getReportUsers($request['provider'],$this->emailProvider,'records',$this->limit,$request['offset']);

	    $chartDataResult1 =	count($userRec->getReportUsers($request['provider'],$this->emailProvider,'records','nolimit'));
	    echo $common->reportusers_CM($chartDataResult,'Provider<br><span> '.$request['provider'].'</span>',$request['provider'],$request['offset'],$request['action'],$newfilename='',$cattype='',$csvdatda,$chartDataResult1);
	    exit;
	}

/*	public function getgroupbutton()
	{
		$common	 	=	new Admin_Model_Common();
		$Groupdropdown = $common->Groupdropdown(); 
			if(empty($Groupdropdown)){
				$notfound = '<div class="noFound">No groups found</div>';
					$groupFound='none';
			}
				
 		$content ='<div class="dropDown" style="float:right;margin-top:5px"><a href="javascript:void(0);" class="btn dropDownTarget disabled"><i class="fa fa-plus fa-lg"></i> &nbsp; Add user to group</a>
		<div class="dropDownList groupinsertWrapper right">
		<div id="groupuserinsert">
		<div class="grpWrapperBox">
		<div class="createdGrpDrp" style="display:'.$groupFound.';"> '.$Groupdropdown.'</div>
		<div class="creatGWrap">
		<div class="saveFilterWrapper">
		<div class="subPopupContainer">
		<h2>Create group</h2>
		<div class="formRow">
		<input type="text" maxlength="20" name="filterName" id="gname" placeholder="enter group name" value="">
		</div>
		<div class="formRow">
		<textarea id="grpDescription" placeholder="enter group Discription" value=""></textarea>
		</div>
		</div>
		<div class="popupFooterWrapper">
		<button id="saveGrpName" class="btn btn-green" type="button"><i class="fa fa-group"> </i>Save group</button>
		<button class="btn cancelSaveFilter" type="button"><i class="fa fa-times-sign"> </i>Cancel</button>
		</div>
		</div>
		</div>'.$notfound.'';
		$content .='<button type="button" class="btn btn-green fluidBtn" id="addgroupBtn" dataAfter="or" style="display:'.$groupFound.'">';
		$content .='<i class="fa fa-plus fa-lg"></i>&nbsp; Save to existing group </button>';
		return $content .='<a href="javascript:void(0)" class="btn fluidBtn" id="usergrouplinkforadd"><i class="fa-group fa-lg"></i> &nbsp; + Add new group and auto save</a>
		</div>
		</div></div></div>';
		
			
	}*/
	public function chkboxhtml($Totalcount, $typeCheckbox)
	{
		return $content = '<label>
		<input id="tlallresult2_'.$typeCheckbox.'" class="goupusermain2" type="checkbox" value="allInResults" name="goupusermain2">
		<label for="tlallresult2_'.$typeCheckbox.'"></label>
		Select all '.$Totalcount.'
		</label>';
	}

}
