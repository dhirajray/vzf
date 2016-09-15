<?php
class FollowingController extends IsController
{
	 public function init()
    {
        parent::init();
        $this->following  = new Application_Model_Following();
    }
   public function indexAction()
   {
    	$userid = $this->_userid;
    	$request = $this->getRequest();
		$start = $this->getRequest()->getPost('start')?$this->getRequest()->getPost('start'):0;
		$end = $this->getRequest()->getPost('end')?$this->getRequest()->getPost('end'):5;		
    	$myFollowing= new Application_Model_Following();	
    	$CurrDate=date('Y-m-d H:i:s');
    	setcookie('currloginlastseen', $CurrDate, 0, '/');
    	setcookie('lastloginseen', $CurrDate, 0, '/');	    	
    	$this->view->followingdbees = $myFollowing->index($start,$end,$userid);	    			
    	$this->view->start = $start;
		$this->view->startnew = $start+PAGE_NUM;
		$this->view->end = $start+PAGE_NUM;
        $this->view->dbeenotavailmsg = $request->getPost('dbeenotavailmsg');
    	$response = $this->_helper->layout->disableLayout();
		return $response;
    }
    public function customdashboarduserAction()
    {
    	$userid = $this->_userid;
    	$request = $this->getRequest();
		$start = $this->getRequest()->getPost('start')?$this->getRequest()->getPost('start'):0;
		$end = $this->getRequest()->getPost('end')?$this->getRequest()->getPost('end'):5;	

		$userids = $this->getRequest()->getPost('user');
		$userids = trim($request->getPost('user'),', '); 
		$userINids 	=	explode(',', $userids);

    	$myFollowing= new Application_Model_Following();	
    	$CurrDate=date('Y-m-d H:i:s');
    	setcookie('currloginlastseen', $CurrDate, 0, '/');
    	setcookie('lastloginseen', $CurrDate, 0, '/');	    	
    	$this->view->followingdbees = $myFollowing->customDashboardUsers($start,$end,$userid,$userINids);	

    	$this->view->start = $start;
		$this->view->startnew = $start+PAGE_NUM;
		$this->view->end = $start+PAGE_NUM;
        $this->view->dbeenotavailmsg = $request->getPost('dbeenotavailmsg');
    	$response = $this->_helper->layout->disableLayout();
		return $response;
    }   
   
    public function followinguserAction()
    {
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
			$alphabets      = array();
			$followigslague = new Application_Model_Following();	
			$userprofile    = new Application_Model_Profile();
			$commonbynew 	= new Application_Model_Commonfunctionality();
			$userID 	    = (int)$this->_request->getPost('userID');
			$limit 		    = (int)$this->_request->getPost('limit');
			$sortval		= $this->_request->getPost('sortval');
			$offset	 	    = (int)$this->_request->getPost('offset');
			$caller	 	    = $this->_request->getPost('caller');
			$extraparam		= $this->_request->getPost('extraparam');
			$regsortval		= $this->_request->getPost('regsortval');
			$regsortvalcon	= $this->_request->getPost('regsortval');
			$OtherUser     = $this->_request->getPost('OtherUser');
			$letters 		= range('a', 'z');

			if($regsortval!="")
			{
				$regsortval=$this->myclientdetails->customDecoding($regsortval);
			}
			
			if($sortval!="")
			{
			 $limit 	        = ($limit=='')?9:$limit+1;
			 $regsortval        = "";
		    }
		    else
		    {
		      $limit 	        = '';
		    }
			$offset         = ($offset=='')?0:$offset;

			$user =$userprofile->getuserbyprofileid($userID);

			if($sortval!="")
			{
			 $followingdbeesforalpha = $followigslague->getfollowing($userID,$limit+1,$offset,'','',$this->session_data['usertype']);
		    }
		    else
		    {
		     $followingdbeesforalpha = $followigslague->getfollowing($userID,$limit,$offset,'','',$this->session_data['usertype']);
		    }

				foreach($followingdbeesforalpha as $usr):
				 $substr=substr($usr['name'],0,1);						
				 if(in_array($this->myclientdetails->customDecoding(strtolower($substr)), $letters))
				 {
	               $alphabets[]=$this->myclientdetails->customDecoding(strtolower($substr));
	             }	              
	        	endforeach; 

				if(sizeof($alphabets)>0)
                {
                	$alphabets = array_map("unserialize", array_unique(array_map("serialize", $alphabets)));
                	asort($alphabets);
                }

                $res = null;

				if(count($alphabets)>0)
				{	
					foreach ($alphabets as $v) {
						
					    if ($v != null) {
					        $res = $v;
					        break;
					    }
					}
				}

			  $first=$res['firstletter'];
							   
			  if(($extraparam=='MyProfile' || $extraparam=='OtherProfile') && ($regsortval==""))
			  {
			  	  $regsortval=$this->myclientdetails->customEncoding($first,$call="something");
			  	  $regsortvalcon=$first;
			  	 
			  }
			  if($regsortval!="")
			  {
			  	 $activeclass='active';
			  	 $activeclass_earlier='';
			  	 $activeclass_latest='';
			  	 $style='display:block;';
			  }
			  if($sortval=='RegistrationDate-DESC')
			  {
			  	 $regsortval='';
			  	 $activeclass='';
			  	 $activeclass_earlier='';
			  	 $activeclass_latest='active';
			  	 $style='display:none;';
			  }
			  if($sortval=='RegistrationDate-ASC')
			  {
			  	 $regsortval='';
			  	 $activeclass='';
			  	 $activeclass_earlier='active';
			  	 $activeclass_latest='';
			  	 $style='display:none;';
			  }
			
			if($sortval!="")
			{
			 $followingdbees = $followigslague->getfollowing($userID,$limit+1,$offset,$sortval,$regsortval,$this->session_data['usertype']);
		    }
		    else
		    {
		     $followingdbees = $followigslague->getfollowing($userID,$limit,$offset,$sortval,$regsortval,$this->session_data['usertype']);
		    }

			$offset = $offset+$limit;

			$return = '';
				
			$NameRow = $userprofile->getuserbyprofileid($userID);
			if($caller=='')
			{
				$UserName=ucwords($this->myclientdetails->customDecoding($NameRow['Name'])).' '.ucwords($this->myclientdetails->customDecoding($NameRow['lname'])).' is being followed by';
			}
			$userTypenalval = $commonbynew->checkuserTypetooltip($NameRow['usertype']);
			$UserNamemsg=ucwords($this->myclientdetails->customDecoding($NameRow['Name'])).' '.ucwords($this->myclientdetails->customDecoding($NameRow['lname']));
			if($caller=='')
			{
				$return.='<div id="middleWrpBox"><div class="user-name titleHdpad" style="margin-bottom:15px">'.$UserName.'</div><div class="next-line"></div><div>';     
				$return.='<div id="mydbcontrols"></div>';
			}
			if(count($followingdbees)>0) {
				$return.='<div class="searchHeaderAllUser sortUserList"><ul id="searchUserAllMenu">';
				    
					$return.='<li><span class="sortBytxt">Sort By:</span></li><li><a href="javascript:void(0);" class="SortAlphabetFollowers '.$activeclass.'" data-xxx="alphabetically" data-OtherUser="'.$OtherUser.'" data-char="'.$first.'" follow_user_uid="'.$userID.'">Name</a></li><li><a href="javascript:void(0);" data-OtherUser="'.$OtherUser.'" class="'.$activeclass_latest.'" id="FollowersLatestUser" data-sort="RegistrationDate-DESC" follow_user_uid="'.$userID.'">Latest</a></li>
					<li><a href="javascript:void(0);" data-OtherUser="'.$OtherUser.'" class="'.$activeclass_earlier.'" id="FollowersEarliestUser" data-sort="RegistrationDate-ASC" follow_user_uid="'.$userID.'">Earliest</a></li></ul>';					

					if(count($alphabets)>0)
					{ 					 	
						$return.='<span id="MemberSortAlphbet" data-char="'.$first.'" style="'.$style.'">';
						 	$return.='<ul class="alphaMenu">';

						 	foreach($alphabets as $char):
							 	if(strtolower($regsortvalcon)==strtolower($char['firstletter']))
							 	  	 $style2='active';
							      else
							       	$style2='noclass';
							     $return.='<li style="width:5%;" data-raj='.$regsortvalcon.' data-raj2='.$char['firstletter'].'><a href="javascript:void(0);" data-OtherUser="'.$OtherUser.'" class="SortAlphabetFollowers '.$style2.'" data-char="'.$char['firstletter'].'" follow_user_uid="'.$userID.'">'.ucfirst($char['firstletter']).'</a></li>';
						 	endforeach;
						 	$return.='</ul>';
						 	$return.='</span></div>';	
					}
			

			$counter=1;
			$return.='<div class="forloader"><ul class="searchMemberList sortUserbox">';

			foreach($followingdbees as $Row):
			$fcnt =$followigslague->chkfollowingcnt($Row['id'],$_SESSION['Zend_Auth']['storage']['UserID']);
			$fellowtxt = ($fcnt['ID']>0)?'Unfollow':'Follow';

			$checkImage = new Application_Model_Commonfunctionality();
			$userprofile1 = $checkImage->checkImgExist($Row['avatar'],'userpics','default-avatar.jpg');
			$return.='<li class="usrList" id="'.$Row['UserID'].'">';
				if((int)$Row['type']==1){
				$return.='<a href="'.BASE_URL.'/user/'.$this->myclientdetails->customDecoding($Row['username']).'" class="userSrcProfilePic">
				<img src="'.IMGPATH.'/users/medium/'.$userprofile1.'" border="0"  width="90" height="90"/>
				</a>';
				$return.='<div class="userSrcProfileContainer">';
				}
				else
				{
				$return.='<div class="userSrcProfileContainer" style="margin-left:0px;">';

				}
				$return.='<h2 class="oneline">'.utf8_encode($this->myclientdetails->customDecoding($Row['name'])).' '.utf8_encode($this->myclientdetails->customDecoding($Row['lname'])).' ';
				if($Row['typename']!="")
				{
				$return.= '('.$Row['typename'].')';
				}
				$return.= '</h2>';

				if($this->myclientdetails->customDecoding($Row['title'])!=''){
				$return.= '<h3 class="oneline">'.utf8_encode($this->myclientdetails->customDecoding($Row['title'])).'</h3>';

				}
				if($this->myclientdetails->customDecoding($Row['company'])!=''){
				$return.= '<h3 class="oneline">'.utf8_encode($this->myclientdetails->customDecoding($Row['company'])).'</h3>';
				}
				if($this->myclientdetails->customDecoding($Row['Email'])!='' && $Row['Emailmakeprivate']==0){
				$return.= '<div class="srcEmailId oneline">'.utf8_encode($this->myclientdetails->customDecoding($Row['Email'])).'</div>';
				}

				$return.= '<div class="profileDashBtn">';

				if($this->session_data['UserID']!=$Row['id'] && $Row['id']!=adminID){
				
				$return.= ' <a class="btn messageina btn-mini" onclick="javascript:opensendmessage('.$Row['id'].', \''.utf8_encode($this->myclientdetails->customDecoding($Row['name'])).'\',this);" href="javascript:void(0)"><i class="fa fa-envelope-o"></i></a>

				';
				} else { $return.=' ';
				}
			$return.='</div></div></li>';
            
			if($counter%5==0) $return.='';
			$counter++;
			endforeach;
			$return.='</ul></div>';
				if($sortval!="")
				{		
					$return.='<div class="show_more_main" id="show_more_main'.$Row["id"].'">
				                <div id="'.$Row["id"].'" class="show_more showMoreBtn" title="Load more posts" data-userID="'.$userID.'" data-caller="'.$caller.'" data-sortval="'.$sortval.'" data-regsortval="'.$regsortval.'" data-regsortvalcon="'.$regsortvalcon.'" data-OtherUserx="'.$OtherUser.'" data-list="Followers"  title="Load more posts">Show more</div>
				                <div class="loding_txt"><i class="fa fa-spinner fa-spin fa-lg"></i></div>
				            </div>';
                }

			}
			else {	
			$return.='<div class="noFound" >'.$UserNamemsg.' is currently not followed by anyone.</div>';
			}
			$return.='</div>';
				
			$data['status'] = 'success';
			$data['total'] = count($followingdbees);
			$data['offset'] = $offset;
			$data['content']= $return;
		}
		return $response->setBody(Zend_Json::encode($data));	


    }

    public function followinguserloadmoreAction()
    {
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
			$alphabets      = array();
			$followigslague = new Application_Model_Following();	
			$userprofile    = new Application_Model_Profile();
			$commonbynew 	= new Application_Model_Commonfunctionality();
			$userID 	    = (int)$this->_request->getPost('userID');
			$limit 		    = (int)$this->_request->getPost('limit');
			$sortval		= $this->_request->getPost('sortval');
			$offset	 	    = (int)$this->_request->getPost('offset');
			$caller	 	    = $this->_request->getPost('caller');
			$extraparam		= $this->_request->getPost('extraparam');
			$regsortval		= $this->_request->getPost('regsortval');
			$regsortvalcon	= $this->_request->getPost('regsortval');
			$OtherUser     = $this->_request->getPost('OtherUser');
			$letters 		= range('a', 'z');

			if($regsortval!="")
			{
				$regsortval=$this->myclientdetails->customDecoding($regsortval);
			}
						
			$limit 	        = ($limit=='')?9:$limit+1;		    
			$offset         = ($offset=='')?0:$offset;
			$user =$userprofile->getuserbyprofileid($userID);
			$first='';
							   
			  if(($extraparam=='MyProfile' || $extraparam=='OtherProfile') && ($regsortval==""))
			  {
			  	  $regsortval=$this->myclientdetails->customEncoding($first,$call="something");
			  	  $regsortvalcon=$first;
			  	 
			  }
			  if($regsortval!="")
			  {
			  	 $activeclass='active';
			  	 $activeclass_earlier='';
			  	 $activeclass_latest='';
			  	 $style='display:block;';
			  }
			  if($sortval=='RegistrationDate-DESC')
			  {
			  	 $regsortval='';
			  	 $activeclass='';
			  	 $activeclass_earlier='';
			  	 $activeclass_latest='active';
			  	 $style='display:none;';
			  }
			  if($sortval=='RegistrationDate-ASC')
			  {
			  	 $regsortval='';
			  	 $activeclass='';
			  	 $activeclass_earlier='active';
			  	 $activeclass_latest='';
			  	 $style='display:none;';
			  }
			
			$followingdbees = $followigslague->getfollowing($userID,$limit+1,$offset,$sortval,$regsortval,$this->session_data['usertype']);

			$offset = $offset+$limit;

			$return = '';
				
			$NameRow = $userprofile->getuserbyprofileid($userID);
			if($caller=='')
			{
				$UserName=ucwords($this->myclientdetails->customDecoding($NameRow['Name'])).' '.ucwords($this->myclientdetails->customDecoding($NameRow['lname'])).' is being followed by';
			}
			$userTypenalval = $commonbynew->checkuserTypetooltip($NameRow['usertype']);
			$UserNamemsg=ucwords($this->myclientdetails->customDecoding($NameRow['Name'])).' '.ucwords($this->myclientdetails->customDecoding($NameRow['lname']));
			
			if(count($followingdbees)>0) {

			$counter=1;

			foreach($followingdbees as $Row):
			$fcnt =$followigslague->chkfollowingcnt($Row['id'],$_SESSION['Zend_Auth']['storage']['UserID']);
			$fellowtxt = ($fcnt['ID']>0)?'Unfollow':'Follow';

			$checkImage = new Application_Model_Commonfunctionality();
			$userprofile1 = $checkImage->checkImgExist($Row['avatar'],'userpics','default-avatar.jpg');
			$return.='<li class="usrList" id="'.$Row['UserID'].'">';
				if((int)$Row['type']==1){
				$return.='<a href="'.BASE_URL.'/user/'.$this->myclientdetails->customDecoding($Row['username']).'" class="userSrcProfilePic">
				<img src="'.IMGPATH.'/users/medium/'.$userprofile1.'" border="0"  width="90" height="90"/>
				</a>';
				$return.='<div class="userSrcProfileContainer">';
				}
				else
				{
				$return.='<div class="userSrcProfileContainer" style="margin-left:0px;">';

				}
				$return.='<h2 class="oneline">'.utf8_encode($this->myclientdetails->customDecoding($Row['name'])).' '.utf8_encode($this->myclientdetails->customDecoding($Row['lname'])).' ';
				if($Row['typename']!="")
				{
				$return.= '('.$Row['typename'].')';
				}
				$return.= '</h2>';

				if($this->myclientdetails->customDecoding($Row['title'])!=''){
				$return.= '<h3 class="oneline">'.utf8_encode($this->myclientdetails->customDecoding($Row['title'])).'</h3>';

				}
				if($this->myclientdetails->customDecoding($Row['company'])!=''){
				$return.= '<h3 class="oneline">'.utf8_encode($this->myclientdetails->customDecoding($Row['company'])).'</h3>';
				}
				if($this->myclientdetails->customDecoding($Row['Email'])!='' && $Row['Emailmakeprivate']==0){
				$return.= '<div class="srcEmailId oneline">'.utf8_encode($this->myclientdetails->customDecoding($Row['Email'])).'</div>';
				}

				$return.= '<div class="profileDashBtn">';

				if($this->session_data['UserID']!=$Row['id'] && $Row['id']!=adminID){
				
				$return.= ' <a class="btn messageina btn-mini" onclick="javascript:opensendmessage('.$Row['id'].', \''.utf8_encode($this->myclientdetails->customDecoding($Row['name'])).'\',this);" href="javascript:void(0)"><i class="fa fa-envelope-o"></i></a>

				';
				} else { $return.=' ';
				}
			$return.='</div></div></li>';
            
			if($counter%5==0) $return.='';
			$counter++;
			endforeach;
			
				if($sortval!="")
				{		
					$return.='<div class="show_more_main" id="show_more_main'.$Row["id"].'">
				                <div id="'.$Row["id"].'" class="show_more showMoreBtn" title="Load more posts" data-userID="'.$userID.'" data-caller="'.$caller.'" data-sortval="'.$sortval.'" data-regsortval="'.$regsortval.'" data-regsortvalcon="'.$regsortvalcon.'" data-OtherUserx="'.$OtherUser.'" data-list="Followers"  title="Load more posts">Show more</div>
				                <div class="loding_txt"><i class="fa fa-spinner fa-spin fa-lg"></i></div>
				            </div>';
                }

			}
			else {	
			$return.='<div class="noFound" style="margin-top:2px;">No more result found!.</div>';			
				
			}
			$return.='</div>';
				
			$data['status'] = 'success';
			$data['total'] = count($followingdbees);
			$data['offset'] = $offset;
			$data['content']= $return;
		}
		return $response->setBody(Zend_Json::encode($data));	
    }



    	

    public function allusersAction()
    	{
    		
            $data = array(); 
            $user_type=$this->session_data['usertype'];
          	$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(true);
			$response = $this->getResponse();
			$response->setHeader('Content-type', 'application/json', true);			
			$activeclass_latest='';
			$activeclass='';
			
			if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
			{
				$limit='40';
                $sortby 	  	  = $this->_request->getPost('sortby');
                $sortbycompany 	  = $this->_request->getPost('sortbycompany');
                $filtertby 	      = $this->_request->getPost('filtertby');
                $searchMember 	  = $this->_request->getPost('searchMember');
                $UserCount 		  = $this->_request->getPost('UserCount');                
                $lastId 		  = $this->_request->getPost('ID');
                $lastId 		  = ($lastId=='')? '' : $lastId;
				$letters 		  = range('a', 'z');
				$alphabets=array();
				$companyalphabets=array();
				$nextcharfilter  = 0;
				//foreach($letters as $letter)
				//{
					//$encodeletter = $this->myclientdetails->customEncoding($letter,'allusersAlphabat'); 
					
					$alphaquery="SELECT  SUBSTRING(Name, 1, 1) AS `firstletter`, SUBSTRING(company, 1, 1) AS `firstlettercom` FROM `tblUsers` WHERE (clientID = '".clientID."')  ";
					if($this->userconfirmed==0)
			        {
			          $alphaquery.=" AND Status = 1";
			        }
			        if($this->admin_searchable_frontend==0)
			        {
			          $alphaquery.=" AND role != 1 AND usertype != 10";
			        }
			        if($user_type==0)
			        {
			          $alphaquery.=" AND usertype = 0"; 
			        }
			        if(isADMIN!=1) 
		            {
		              $alphaquery.=" AND hideuser != 1"; 
		            }			       
			       		            
					$alphaRow=$this->myclientdetails->passSQLquery($alphaquery);
					
					foreach($alphaRow as $letterrow)
					{
					  if($letterrow['firstletter'] != null) {
						 $alphabets[]=ucfirst($this->myclientdetails->customDecoding($letterrow['firstletter']));
						}
						if ($letterrow['firstlettercom'] != null) {
						 $companyalphabets[]=ucfirst($this->myclientdetails->customDecoding($letterrow['firstlettercom']));
						}
				    }
				//}

                 asort($alphabets);     
                 asort($companyalphabets);
                 $alphabets=array_unique($alphabets);
		         $companyalphabets=array_unique($companyalphabets);
                
				
				//foreach($letters as $letter)
				//{
					//$encodeletter = $this->myclientdetails->customEncoding($letter,'allusersAlphabat');
					
					/*$alphaquerycompany="SELECT DISTINCT SUBSTRING(company, 1, 1) AS `firstlettercom` FROM `tblUsers` WHERE (clientID = '".clientID."')  ";
					if($this->userconfirmed==0)
			        {
			          $alphaquerycompany.=" AND Status = 1";
			        }
			        if($this->admin_searchable_frontend==0)
			        {
			          $alphaquerycompany.=" AND role != 1 AND usertype != 10";
			        }
			        if($user_type==0)
			        {
			          $alphaquerycompany.=" AND usertype = 0"; 
			        }
			      
			        $alphaquerycompany.= " ORDER BY company ASC";
			        
					$alphaRowcompany=$this->myclientdetails->passSQLquery($alphaquerycompany);
					
					foreach($alphaRowcompany as $lettercompanyrow)
					{				
					 $companyalphabets[]=$lettercompanyrow['firstlettercom'];
				    }*/
				//}
			     
				
                $searchMember = $this->myclientdetails->customEncoding($searchMember,'allusersAlphabat');

                $filter = new Zend_Filter_StripTags();
			    $searchMember =$filter->filter(addslashes($searchMember));

				$sort=array('UserID'=>'DESC');
				$memberSortAlpha=0;
				$companySortAlpha=0;
				$totalSearchHtml ='';
				$page=2;

				$sortby 		= $this->myclientdetails->customEncoding($sortby,'allusersAlphabat');
				$sortbycompany  = $this->myclientdetails->customEncoding($sortbycompany,'allusersAlphabat');

				$return.='<div id="middleWrpBox">';				
				if($lastId!="")
				{	//echo"A";			die;
					$IsSearchText = $this->_request->getPost('IsSearchText');
					$IsSearchText = $this->myclientdetails->customEncoding($IsSearchText,'allusersAlphabat');
					$IsSortBy 	  = $this->_request->getPost('IsSortBy');
					$IsSortBy 	  = $this->myclientdetails->customEncoding($IsSortBy,'allusersAlphabat');
					$SearchUser   = $this->_request->getPost('SearchUser');
					$SearchUser   = $this->myclientdetails->customEncoding($SearchUser,'allusersAlphabat');

					$users        = $this->User_Model->SearchMemberPaging($lastId,$SearchUser,$IsSortBy,$user_type,$this->userconfirmed,$filtertby,$sortbycompany,$this->admin_searchable_frontend);

				}
				else if($searchMember!="")
				{					
					//echo"B";			die;
					$activeclass='';
				    $activeclass_latest='active';
				    $activeclasscompany='';
					$memberSortAlpha=0;	
					$companySortAlpha=0;				
				    $users = $this->User_Model->userDirectory($sortby,$searchMember,$user_type,$this->userconfirmed,$filtertby,'','',$this->admin_searchable_frontend);
				   

				    $userscounter = $this->User_Model->userDirectoryCount($sortby,$searchMember,$user_type,$this->userconfirmed,$filtertby,'','',$this->admin_searchable_frontend);				   
					
					$return.='<input type="hidden" name="SearchUser" class="SearchUser" value="'.$this->myclientdetails->customDecoding($searchMember).'">';
					$return.='<input type="hidden" name="SearchMemberTextScroll" id="SearchMemberTextScroll" value="1">';
					$return.='<input type="hidden" name="SortByScroll" id="SortByScroll" value="'.$this->_request->getPost('sortby').'">';
					$return.='<input type="hidden" name="SortByScrollCompany" id="SortByScrollCompany" value="'.$this->_request->getPost('sortbycompany').'">';
				}
				else if($sortby!="")
				{					
                    //echo"C";			die;
                    $activeclass='active';
				    $activeclass_latest='';
				    $activeclasscompany='';
					$SearchUserTextXX = $this->_request->getPost('SearchUserTextXX');
					$SearchUserTextXX = $this->myclientdetails->customEncoding($SearchUserTextXX,'allusersAlphabat');

					$users = $this->User_Model->userDirectory($sortby,$SearchUserTextXX,$user_type,$this->userconfirmed,$filtertby,'','',$this->admin_searchable_frontend);

					$userscounter = $this->User_Model->userDirectoryCount($sortby,$SearchUserTextXX,$user_type,$this->userconfirmed,$filtertby,'','',$this->admin_searchable_frontend);

					$return.='<input type="hidden" name="SearchUser" class="SearchUser" value="'.$this->myclientdetails->customDecoding($SearchUserTextXX).'">';
					$return.='<input type="hidden" name="SortByScroll" id="SortByScroll" value="'.$this->_request->getPost('sortby').'">';
					$return.='<input type="hidden" name="SortByScrollCompany" id="SortByScrollCompany" value="'.$this->_request->getPost('sortbycompany').'">';
					$display='block';
					$memberSortAlpha=1;
					$companySortAlpha=0;
					
				}
				else if($sortbycompany!="")
				{	
					//echo"D";			die;				
                    $activeclasscompany='active';
                    $activeclass       ='';
				    $activeclass_latest='';
					$SearchUserTextXX = $this->_request->getPost('SearchUserTextXX');
					$SearchUserTextXX = $this->myclientdetails->customEncoding($SearchUserTextXX,'allusersAlphabat');

					$users = $this->User_Model->userDirectory($sortby,$SearchUserTextXX,$user_type,$this->userconfirmed,$filtertby,'',$sortbycompany,$this->admin_searchable_frontend);

					$userscounter = $this->User_Model->userDirectoryCount($sortby,$SearchUserTextXX,$user_type,$this->userconfirmed,$filtertby,'',$sortbycompany,$this->admin_searchable_frontend);

					$return.='<input type="hidden" name="SearchUser" class="SearchUser" value="'.$this->myclientdetails->customDecoding($SearchUserTextXX).'">';
					$return.='<input type="hidden" name="SortByScroll" id="SortByScroll" value="'.$this->_request->getPost('sortby').'">';
					$return.='<input type="hidden" name="SortByScrollCompany" id="SortByScrollCompany" value="'.$this->_request->getPost('sortbycompany').'">';
					$display='block';
					$companySortAlpha=1;
					$memberSortAlpha=0;

					
				}
				else if($filtertby!="")
				{
					//echo"E";			die;
                    $activeclass='';   
				    $activeclass_latest='active';
					$SearchUserTextXX = $this->_request->getPost('SearchUserTextXX');
					$SearchUserTextXX = $this->myclientdetails->customEncoding($SearchUserTextXX,'allusersAlphabat');
					$users = $this->User_Model->userDirectory($sortby,$SearchUserTextXX,$user_type,$this->userconfirmed,$filtertby,'',$sortbycompany,$this->admin_searchable_frontend);

					$userscounter = $this->User_Model->userDirectoryCount($sortby,$SearchUserTextXX,$user_type,$this->userconfirmed,$filtertby,'',$sortbycompany,$this->admin_searchable_frontend);


					$return.='<input type="hidden" name="SearchUser" class="SearchUser" value="'.$this->myclientdetails->customDecoding($SearchUserTextXX).'">';
					$return.='<input type="hidden" name="SortByScroll" id="SortByScroll" value="'.$this->_request->getPost('sortby').'">';
					$return.='<input type="hidden" name="SortByScrollCompany" id="SortByScrollCompany" value="'.$this->_request->getPost('sortbycompany').'">';
				
					
				}
				else
				{
					//echo"F";			die;
					$activeclass='';
					$activeclass_latest='active';
					$orderby = $this->_request->getPost('orderby');
					$SearchUserTextXX = $this->_request->getPost('SearchUserTextXX');
					$SearchUserTextXX = $this->myclientdetails->customEncoding($SearchUserTextXX,'allusersAlphabat');
					
					$return.='<input type="hidden" name="SearchUser" class="SearchUser" value="'.$this->myclientdetails->customDecoding($SearchUserTextXX).'">';
                 	
                 	if($orderby=="")
                 	{
                 		if($user_type==0)
                 		{
                 			
                 		   $normalquery="SELECT UserID,Name,lname,ProfilePic,Username,usertype,title,company,Email,Status,typename,Emailmakeprivate FROM tblUsers WHERE (clientID = '".clientID."')  ";
                 		   if($this->userconfirmed==0)
						   {
							  $normalquery.=" AND Status=1 AND usertype=0";
						   }
							else
							{							   
							  $normalquery.=" AND usertype=0";
							}

							if($this->admin_searchable_frontend==0)
					        {
					          $normalquery.=" AND role != 1 AND usertype != 10";
					        }
					        if(isADMIN!=1) 
				            {
				              $normalquery.=" AND hideuser != 1"; 
				            }

					        $normalquery.= " ORDER BY UserID DESC LIMIT ".$limit;

                 		   $users=$this->myclientdetails->passSQLquery($normalquery);


                 		   $normalcountquery="SELECT count(*) AS total FROM tblUsers WHERE (clientID = '".clientID."')  ";
                 		   
                 		   if($this->userconfirmed==0)
							{
							  $normalcountquery.=" AND Status=1 AND usertype='".$user_type."'";
							}
							else
							{							   
							  $normalcountquery.=" AND usertype='".$user_type."'";
							}

							if($this->admin_searchable_frontend==0)
					        {
					          $normalcountquery.=" AND role != 1 AND usertype != 10";
					        }
					        if(isADMIN!=1) 
				            {
				              $normalcountquery.=" AND hideuser != 1"; 
				            }

                 		   $userscounter=$this->myclientdetails->passSQLquery($normalcountquery);

                 		}
                 		else
                 		{
                 		 	$vipquery="SELECT UserID,Name,lname,ProfilePic,Username,usertype,title,company,Email,Status,typename,Emailmakeprivate FROM tblUsers WHERE (clientID = '".clientID."')  ";
                 		   if($this->userconfirmed==0)
							{
							  $vipquery.=" AND Status=1 AND usertype='".$user_type."'";
							}
							/*else
							{							   
							  $vipquery.=" AND usertype='".$user_type."'";
							}*/

							if($this->admin_searchable_frontend==0)
					        {
					          $vipquery.=" AND role != 1 AND usertype != 10";
					        }
					        if(isADMIN!=1) 
				            {
				              $vipquery.=" AND hideuser != 1"; 
				            }

					        $vipquery.=" ORDER BY UserID DESC LIMIT ".$limit;

                 		   $users=$this->myclientdetails->passSQLquery($vipquery);

                 		   //below count query

                 		   $vipcountquery="SELECT count(*) AS total FROM tblUsers WHERE (clientID = '".clientID."')  ";
                 		   if($this->userconfirmed==0)
							{
							  $vipcountquery.=" AND Status=1 AND usertype='".$user_type."'";
							}
							else
							{							   
							  $vipcountquery.=" AND usertype='".$user_type."'";
							}

							if($this->admin_searchable_frontend==0)
					        {
					          $vipcountquery.=" AND role != 1 AND usertype != 10";
					        }

					        if(isADMIN!=1) 
				            {
				              $vipcountquery.=" AND hideuser != 1"; 
				            }

                 		   $userscounter=$this->myclientdetails->passSQLquery($vipcountquery);
                 		   // end below count query           	
                 		}
                 	}
                 	else
                 	{ 

                 		
                 	  $users = $this->User_Model->userDirectory($sortby,$SearchUserTextXX,$user_type,$this->userconfirmed,$filtertby,$orderby,'',$sortbycompany,$this->admin_searchable_frontend);

                 	 $userscounter = $this->User_Model->userDirectoryCount($sortby,$SearchUserTextXX,$user_type,$this->userconfirmed,$filtertby,$orderby,'',$sortbycompany,$this->admin_searchable_frontend);

                 	  $display='none';
                 	}
				}				

					$countquery="SELECT count(*) AS `total` FROM `tblUsers` WHERE clientID = ".clientID." AND usertype != '10'";
		
					if($this->userconfirmed==0)
					{
					   $countquery.=" AND Status=1";
					}
					 if($this->admin_searchable_frontend==0)
			        {
			          $countquery.=" AND role != 1 AND usertype != 10";
			        }
			        if($user_type==0)
			        {
			          $countquery.=" AND usertype = 0"; 
			        }

			        if(isADMIN!=1) 
		            {
		              $countquery.=" AND hideuser != 1"; 
		            }	

					$usercountarray=$this->myclientdetails->passSQLquery($countquery);

					$totalSearchHtml='';

					if($searchMember!="" || $SearchUserTextXX!="" || $filtertby!="" || $sortbycompany!="")
					{
							unset($alphabets);
							unset($companyalphabets);


							$SearchUserTextXX = $this->_request->getPost('SearchUserTextXX');
							$SearchUserTextXX = $this->myclientdetails->customEncoding($SearchUserTextXX,'allusersAlphabat');									
							

							if($searchMember!="" || $filtertby!="")
						    {
						    	
								$users_xx = $this->User_Model->userDirectoryalphachar('',$searchMember,$user_type,$this->userconfirmed,$filtertby,'','',$this->admin_searchable_frontend);
							}						    

						    
						    if($SearchUserTextXX!="" || $filtertby!="" || $sortbycompany!="")
						    {
						    	
								$users_xx = $this->User_Model->userDirectoryalphachar('',$SearchUserTextXX,$user_type,$this->userconfirmed,$filtertby,'','',$this->admin_searchable_frontend);
							}

							foreach($users_xx as $usr):
								$substr=substr($usr['Name'],0,1);	
								$substr2=substr($usr['company'],0,1);	
							 if(in_array($this->myclientdetails->customDecoding(strtolower($substr)), $letters)){
			                   $alphabets[]=$this->myclientdetails->customDecoding(strtolower($substr));
			                   }
			                  if(in_array($this->myclientdetails->customDecoding(strtolower($substr2)), $letters)) {	
			                	$companyalphabets[]=$this->myclientdetails->customDecoding(strtolower($substr2));
			                   }
		                	endforeach;              

		                	if(sizeof($alphabets)>0)
			                {
			                	$alphabets = array_map("unserialize", array_unique(array_map("serialize", $alphabets)));
			                	asort($alphabets);
			                }
		                	
		                	if(sizeof($companyalphabets)>0)
			                {
			                	$companyalphabets = array_map("unserialize", array_unique(array_map("serialize", $companyalphabets)));
			                	asort($companyalphabets);
			                }
		                                    	
						}

						$res = null;
						
						if(count($alphabets)>0)
						{	
							foreach ($alphabets as $v) {
								
							    if ($v != null) {
							        $res = $v;
							        break;
							    }
							}
						}
						
						$alphabet=$alphabets;


						if($filtertby!="" && $sortby!="" && $userscounter[0]['total']==0)
						{
							

							$activeclass='active';   
						    $activeclass_latest=''; 
							$SearchUserTextXX = $this->_request->getPost('SearchUserTextXX');
							$SearchUserTextXX = $this->myclientdetails->customEncoding($SearchUserTextXX,'allusersAlphabat');
							$users = $this->User_Model->userDirectory($this->myclientdetails->customDecoding($res),$SearchUserTextXX,$user_type,$this->userconfirmed,$filtertby,'',$sortbycompany,$this->admin_searchable_frontend);

							$userscounter = $this->User_Model->userDirectoryCount($this->myclientdetails->customDecoding($res),$SearchUserTextXX,$user_type,$this->userconfirmed,$filtertby,'',$sortbycompany,$this->admin_searchable_frontend);


							$return.='<input type="hidden" name="SearchUser" class="SearchUser" value="'.$this->myclientdetails->customDecoding($SearchUserTextXX).'">';
							$return.='<input type="hidden" name="SortByScroll" id="SortByScroll" value="'.$this->_request->getPost('sortby').'">';
							$return.='<input type="hidden" name="SortByScrollCompany" id="SortByScrollCompany" value="'.$this->_request->getPost('sortbycompany').'">';

							$nextcharfilter=1;
							

						}



						
						$rescomany = null;
					    if(count($companyalphabets)>0)
					    {
					     foreach ($companyalphabets as $v) {
					      if ($v != null) {
					       $rescomany = $v;
					       break;
					      }
					     }
					    }

					    $companyalphabet=$companyalphabets;
					    
					    $companyalphabet=@array_values($companyalphabet);
						//	print_r($companyalphabet); die;
					   $firstlettercompany=$rescomany;					

					if($filtertby!="")
		        	{
		              $filtertby = array_unique($filtertby);

		              if(count($filtertby) > 0)
		              {
		                    $company='';
		                    $title='';
		                    $typenamexx='';
		                foreach ($filtertby as $key => $value) 
		                {		                    
		                    $slice=explode('@@@', $value);		                   

		                    if($slice[1]=='company')
		                    {		                        
		                        $company.='<li class="tagit-choice ui-widget-content ui-state-default ui-corner-all tagit-choice-editable">
							<span class="tagit-label">'.utf8_encode(preg_replace('/[^&#.A-Za-z0-9\-]/', ' ',$this->myclientdetails->customDecoding($slice[0]))).'</span>
							<a class="tagit-close" id="filterTag" data-filterClose="'.utf8_encode(preg_replace('/[^&#.A-Za-z0-9\-]/', ' ',$slice[0])).'@@@company"> <span class="text-icon"><i class="fa fa-times" style="font-size:14px;"></i></span>
							<span class="ui-icon ui-icon-close"></span></a>
							</li>';
							                    }
							                    else if($slice[1]=='title')
							                    {
							                        
							                        $title.='<li class="tagit-choice ui-widget-content ui-state-default ui-corner-all tagit-choice-editable">
							<span class="tagit-label">'.utf8_encode(preg_replace('/[^&#.A-Za-z0-9\-]/', ' ',$this->myclientdetails->customDecoding($slice[0]))).'</span>
							<a class="tagit-close" id="filterTag" data-filterClose="'.utf8_encode(preg_replace('/[^&#.A-Za-z0-9\-]/', ' ',$slice[0])).'@@@title"> <span class="text-icon"><i class="fa fa-times" style="font-size:14px;"></i></span>
							<span class="ui-icon ui-icon-close"></span></a>
							</li>';
							                    }
							                    else if($slice[1]=='usertype')
							                    {
							                        
							                        $typenamexx.='<li class="tagit-choice ui-widget-content ui-state-default ui-corner-all tagit-choice-editable">
							<span class="tagit-label">'.utf8_encode(preg_replace('/[^&#.A-Za-z0-9\-]/', ' ',$slice[0])).'</span>
							<a class="tagit-close" id="filterTag" data-filterClose="'.utf8_encode(preg_replace('/[^&#.A-Za-z0-9\-]/', ' ',$slice[0])).'@@@usertype"> <span class="text-icon"><i class="fa fa-times fa-l" style="font-size:14px;"></i></span>
							<span class="ui-icon ui-icon-close"></span></a>
							</li>';
		                    }

		                }
		            }
		        }

				
				 $first=$res['firstletter'];
				
				if($IsSearchText!="1" || $sortby=="" || $searchMember=="")
				{
					$return.='<input type="hidden" name="SearchMemberScroll" id="SearchMemberScroll" value="1">';
					$return.='<h3 class="pageTitle">&nbsp;'.$usercountarray[0]['total'].' Users</h3>';
					$return.='<div class="searchHeaderAllUser">';
					$return.='<div class="srcUsrWrapper">
								<div class="fa fa-search fa-lg searchIcon2"></div>
								<input type="hidden" name="page" id="page" value="2">
								<input type="text" name="searchMember" id="searchMember" value="'.$this->myclientdetails->customDecoding($SearchUserTextXX).'">
								'.$totalSearchHtml.'
							</div>';
						
					
					    $return.='<ul id="searchUserAllMenu">';
					    if(count($users)>0 || $this->_request->getPost('sortby')!="" || $this->_request->getPost('sortbycompany')!="") 
						{
						$return.='<li><a href="javascript:void(0);" class="'.$activeclass_latest.'" id="allLatestUser" data-xx="noload">New users</a></li>

						 <li style="margin-left:8px; margin-top:5px; font-weight: bold;"> Sort alphabetically:<li>
						 <li><a href="javascript:void(0);" class="SortAlphabet '.$activeclass.'" data-xx="alphabetically" data-char="'.$res.'">By Name</a></li>

							<li><a href="javascript:void(0);" class="CompanySortAlphabet '.$activeclasscompany.'" data-xx="comapnyalphabetically" data-charcomapny="'.$firstlettercompany.'">By Company</a></li>';
						 }
						$return.='<li id="softByPic"><a href="javascript:void(0);" class="fa fa-th-large"></a></li><li id="ThirdOption"><a href="javascript:void(0);"  data-xx="viewAll">View all</a></li>
							  </ul>';
					

					$style='';

					if(count($alphabet)>0)
					 { 
					 	if($memberSortAlpha==1)
					 	{
						$return.='<span id="MemberSortAlphbet" data-char="'.$res.'">';
						 	$return.='<ul>';

						 	foreach($alphabet as $char):
							 	if(strtolower($this->_request->getPost('sortby'))==strtolower($char))
							 	  	 $style2='active';
							      else if($nextcharfilter==1 && $res==strtolower($char))
							      	$style2='active';
							      	else
							       	$style2='noclass';
							     $return.='<li data-raj='.$this->_request->getPost('sortby').' data-raj2='.$char.'><a href="javascript:void(0);" class="SortAlphabet '.$style2.'" data-char="'.$char.'"  data-xx="alphabetically">'.ucfirst($char).'</a></li>';
						 	endforeach;
						 	$return.='</ul>';
						 	$return.='</span>';
					 	}
					 }
					
					if(count($companyalphabet)>0)
					{
						if($companySortAlpha==1)
					 	{
							$return.='<span id="CompanySortAlphbet" data-charcomapny="'.$firstlettercompany.'">';
							$return.='<ul>';

							foreach($companyalphabet as $char):
							if($char!="")
							{
								if(strtolower($this->_request->getPost('sortbycompany'))==$char)
								{
									$style='active';
								}
								else
								{
									$style='';
								}
							$return.='<li><a href="javascript:void(0);" class="CompanySortAlphabet '.$style.'" data-charcomapny="'.$char.'"  data-xx="comapnyalphabetically">'.ucfirst($char).'</a></li>';
						    }
							endforeach;
							$return.='</ul>';
							$return.='</span>';
						}
						
					}
				
				}    
				$return.='</div>';

				if($company!="" || $title!="" || $typenamexx!=""){
				$return.='<ul id="filterTag" class="tagit ui-widget ui-widget-content ui-corner-all" >';
				
				if($typenamexx!="")
				{
					$return.='<li class="filterRowSrc">
					<ul > 
						<li><strong>User Type :</strong> </li>'.$typenamexx.'
					</ul> 
					</li>';
				}

				if($company!="")
				{
					$return.=' <li class="filterRowSrc"> 
									<ul> 
										<li><strong>Company :</strong> </li>'.$company.'
									</ul> 
								</li>';
				}
				if($title!="")
				{
					$return.=' <li class="filterRowSrc"> 
					<ul> 
						<li><strong>Job Title :</strong> </li>'.$title.'
					</ul> 
					</li>';
				}			
				$return.='</ul><br>';	
				}
								
				if($searchMember!="" || $SearchUserTextXX!="" || $filtertby!="" || $sortbycompany!="" || $sortby!="")
				{
				 
					if($userscounter[0]['total']==1)
					{
					 $return.='<div class="matchUserFound" data-UserCount="'.$userscounter[0]['total'].'">'.$userscounter[0]['total'].' matching user</div>';
					}
					else
					{
					 $return.='<div class="matchUserFound" data-UserCount="'.$userscounter[0]['total'].'">'.$userscounter[0]['total'].' matching users</div>';
					}
			    }

				$return.='<ul class="searchMemberList">';

				
								
				if($lastId!="")
					$return="";
				
				if(count($users)>0)
					$data['page'] = $lastId+1;
				else
					$data['page'] = $lastId;

				$follwoing =  new Application_Model_Following(); 	
				
				if(count($users)>0) 
				{

					
				    $checkImage = new Application_Model_Commonfunctionality();
					$follwoing =  new Application_Model_Following();
					$counter=1;
					foreach($users as $Row):
						$type ='';
						if((int)$Row['usertype']!=0)
						$type = $checkImage->checkuserTypetooltip($Row['usertype']); 

						$fcnt =$follwoing->chkfollowingcnt($Row['UserID'],$_SESSION['Zend_Auth']['storage']['UserID']);
						$fellowtxt = ($fcnt['ID']>0)?'Unfollow':'Follow'; 
						$conatctfcnt =$follwoing->CheckContactCount($_SESSION['Zend_Auth']['storage']['UserID'],$Row['UserID']);			

						$contacttext = ($conatctfcnt['id']>0)?'Remove from Contacts':'Add to Contacts';

						$userprofile1 = 'default-avatar.jpg';

						$userprofile1 = $checkImage->checkImgExist($Row['ProfilePic'],'userpics','default-avatar.jpg');	
						
						
						  $return.='<li class="usrList" id="'.$Row['UserID'].'">';
						  if((int)$Row['Status']==1){
						$return.='<a href="'.BASE_URL.'/user/'.$this->myclientdetails->customDecoding($Row['Username']).'" class="userSrcProfilePic">
								<img src="'.IMGPATH.'/users/medium/'.$userprofile1.'" border="0"  width="90" height="90"/>
							</a>';
							$return.='<div class="userSrcProfileContainer">';
						}
						else
						{
						 $return.='<div class="userSrcProfileContainer" style="margin-left:0px;">';
						  
						}
							$return.='<h2 class="oneline">'.utf8_encode($this->myclientdetails->customDecoding($Row['Name'])).' '.utf8_encode($this->myclientdetails->customDecoding($Row['lname'])).' ';
								if($Row['typename']!="")
									{
									 $return.= '('.$Row['typename'].')';
									}
									$return.= '</h2>';

								if($this->myclientdetails->customDecoding($Row['title'])!=''){
									$return.= '<h3 class="oneline">'.utf8_encode($this->myclientdetails->customDecoding($Row['title'])).'</h3>';
									
								}
								if($this->myclientdetails->customDecoding($Row['company'])!=''){
									$return.= '<h3 class="oneline">'.utf8_encode($this->myclientdetails->customDecoding($Row['company'])).'</h3>';
								}
								if($this->myclientdetails->customDecoding($Row['Email'])!='' && $Row['Emailmakeprivate']==0){
									$return.= '<div class="srcEmailId oneline">'.utf8_encode($this->myclientdetails->customDecoding($Row['Email'])).'</div>';
								}
								
							$return.= '<div class="profileDashBtn">';

							if($this->session_data['UserID']!=$Row['UserID'] && $Row['UserID']!=adminID){
								$return.='
								<a class="btn btn-yellow fallowina btn-mini" onclick="javascript:followme('.$Row['UserID'].',this);" href="javascript:void(0);">
								<div id="followme-label" style="cursor: pointer;">'.$fellowtxt.'</div>
								</a>';
					/*if($this->addtocontact==1 || ($this->addtocontact==0 && $contacttext=='Remove from Contacts')){
						$return.= ' <a class="btn btn-yellow fallowina btn-mini addtoconxx" onclick="javascript:addtocontact('.$Row['UserID'].',this,\''.$this->myclientdetails->customDecoding($Row['Name']).'\',\''.$fellowtxt.'\');" href="javascript:void(0);">
						 <div id="contact-label" style="cursor: pointer;">'.$contacttext.'</div>
						</a>';
			     	  } */

				/*$return.= ' <a class="btn messageina btn-mini" onclick="javascript:opensendmessage('.$Row['UserID'].', \''.utf8_encode($this->myclientdetails->customDecoding($Row['Name'])).'\',this);" href="javascript:void(0)"><i class="fa fa-envelope-o"></i></a>
									
								';*/
							} else { $return.=' ';
							}
							$return.='</div></div></li>';
									
								 
						if($counter%4==0) $return.='';
						$counter++;
					endforeach;
					if($lastId=="")
						$return.='</ul>';
				  $return.='<input type="hidden" name="TextUserCount" id="UserCount" value="'.count($users).'">';
				   $return.='<input type="hidden" name="ResulttedUserCount" id="ResulttedUserCount" value="'.$userscounter[0]['total'].'">';
					if($UserCount >= $limit && $lastId==2)
					{
				   		 $return.='<div id="last_msg_loader" class="clear"></div>';

				    }else{
				    	$return.='<div class="clear"></div>';
				    }
				    
				}
				else if($lastId=="")	
					 $return.='<div class="noFound" >No matching user found.</div>';
				
				if($lastId=="")
					$return.='</div></div>';
		
				
				$data['status'] = 'success';
				$data['content']= $return;
				$data['FChar']= $res;
				$data['FilterUserTotal']= $userscounter[0]['total'];
				
				
				$encoded_rows = array_map('utf8_encode', $data);
			}
    		return $response->setBody(Zend_Json::encode($encoded_rows));
    	}

	    public function followeruserAction()
	    {
		
			$data = array();
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(true);
			$response = $this->getResponse();
			$response->setHeader('Content-type', 'application/json', true);
			
			if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
			{
				$alphabets      =array();
				$followigslague = new Application_Model_Following();	
				$userprofile 	= new Application_Model_Profile();
				$userID 		= (int)$this->_request->getPost('userID');
				$limit 			= (int)$this->_request->getPost('limit');
				$offset	 		= (int)$this->_request->getPost('offset');
				$caller	 		= $this->_request->getPost('caller');
				$sortval		= $this->_request->getPost('sortval');
				$regsortval		= $this->_request->getPost('regsortval');
				$regsortvalcon	= $this->_request->getPost('regsortval');
				$letters 		= range('a', 'z');
				
				
				if($sortval!="")
				{
				 $limit 	        = ($limit=='')?9:$limit+1;
			    }
			    else
			    {
			      $limit 	        = '';
			    }
				$offset         = ($offset=='')?0:$offset;

				$user           = $userprofile->getuserbyprofileid($userID);

				$extraparam     = $this->_request->getPost('extraparam');
				$OtherUser     = $this->_request->getPost('OtherUser');

				
				if($regsortval!="")
				{
					$regsortval=$this->myclientdetails->customDecoding($regsortval);
				}
				
				if($sortval!="")
				{			
                $followingdbeesforalpha = $followigslague->getfolloweruserprofile($userID,$limit+1,$offset,'','',$this->session_data['usertype']);
                
                }
                else
                {
                 $followingdbeesforalpha = $followigslague->getfolloweruserprofile($userID,$limit,$offset,'','',$this->session_data['usertype']);	
                }

				foreach($followingdbeesforalpha as $usr):
					 $substr=substr($usr['name'],0,1);						
					 if(in_array($this->myclientdetails->customDecoding(strtolower($substr)), $letters))
					 {
		               $alphabets[]=$this->myclientdetails->customDecoding(strtolower($substr));
		             }	              
	        	endforeach; 

				if(sizeof($alphabets)>0)
                {
                	$alphabets = array_map("unserialize", array_unique(array_map("serialize", $alphabets)));
                	asort($alphabets);
                }

                $res = null;

				if(count($alphabets)>0)
				{	
					foreach ($alphabets as $v) {
						
					    if ($v != null) {
					        $res = $v;
					        break;
					    }
					}
				}

			  $first=$res['firstletter'];
			  if(($extraparam=='MyProfile' || $extraparam=='OtherProfile') && ($regsortval==""))
			  {
			  	  $regsortval=$this->myclientdetails->customEncoding($first,$call="something");
			  	  $regsortvalcon=$first;
			  	 
			  }
			  if($regsortval!="")
			  {
			  	 $activeclass='active';
			  	 $activeclass_earlier='';
			  	 $activeclass_latest='';
			  	 $style='display:block;';
			  }
			  if($sortval!="")
			  {
			  	 $regsortval="";
			  }

			  if($sortval=='RegistrationDate-DESC')
			  {
			  	 $activeclass='';
			  	 $activeclass_earlier='';
			  	 $activeclass_latest='active';
			  	 $style='display:none;';
			  }
			  if($sortval=='RegistrationDate-ASC')
			  {
			  	 $activeclass='';
			  	 $activeclass_earlier='active';
			  	 $activeclass_latest='';
			  	 $style='display:none;';
			  }

			 if($sortval!="")
			 {
			  $followingdbees = $followigslague->getfolloweruserprofile($userID,$limit+1,$offset,$sortval,$regsortval,$this->session_data['usertype']);
			 }
			 else
			 {
			   $followingdbees = $followigslague->getfolloweruserprofile($userID,$limit,$offset,$sortval,$regsortval,$this->session_data['usertype']);
			 }

                /*echo "<pre>";
				print_r($alphabets);
				echo "</pre>";
				die;*/

				$offset = $offset+$limit;
				$return = '';
					
				$NameRow = $userprofile->getuserbyprofileid($userID);
						
				$UserName=ucwords($this->myclientdetails->customDecoding($NameRow['Name'])).' '.ucwords($this->myclientdetails->customDecoding($NameRow['lname'])).' is following';
				$UserNamemsg=ucwords($this->myclientdetails->customDecoding($NameRow['Name'])).' '.ucwords($this->myclientdetails->customDecoding($NameRow['lname']));
				if($caller=='')
				{
					$return.= '<div id="middleWrpBox"><div class="user-name titleHdpad" style="margin-bottom:15px">'.$UserName.'</div><div>';     
					$return.= '<div id="mydbcontrols"></div>';
				}


			  

				if(count($followingdbees)>0) {
                
               // if($regsortval=="" || $sortval=="")
                //{

					 $return.='<div class="searchHeaderAllUser sortUserList"><ul id="searchUserAllMenu">';
				    
					$return.='<li><span class="sortBytxt">Sort By:</span></li><li><a href="javascript:void(0);" class="SortAlphabetFollwing '.$activeclass.'" data-xxx="alphabetically" data-OtherUser="'.$OtherUser.'" data-char="'.$first.'" following_user_uid="'.$userID.'">Name</a></li><li><a href="javascript:void(0);" data-OtherUser="'.$OtherUser.'" class="'.$activeclass_latest.'" id="FollowingLatestUser" data-sort="RegistrationDate-DESC" following_user_uid="'.$userID.'">Latest</a></li>
					<li><a href="javascript:void(0);" data-OtherUser="'.$OtherUser.'" class="'.$activeclass_earlier.'" id="FollowingEarliestUser" data-sort="RegistrationDate-ASC" following_user_uid="'.$userID.'">Earliest</a></li></ul>';				

					if(count($alphabets)>0)
					{ 					 	
						$return.='<span id="MemberSortAlphbet" data-char="'.$first.'" style="'.$style.'">';
						 	$return.='<ul class="alphaMenu">';

						 	foreach($alphabets as $char):
							 	if(strtolower($regsortvalcon)==strtolower($char['firstletter']))
							 	  	 $style2='active';
							      else
							       	$style2='noclass';
							     $return.='<li style="width:5%;" data-raj='.$regsortvalcon.' data-raj2='.$char['firstletter'].'><a href="javascript:void(0);" data-OtherUser="'.$OtherUser.'" class="SortAlphabetFollwing '.$style2.'" data-char="'.$char['firstletter'].'" following_user_uid="'.$userID.'">'.ucfirst($char['firstletter']).'</a></li>';
						 	endforeach;
						 	$return.='</ul>';
						 	$return.='</span></div>';					 	
					}
				//}

				$counter=1;
				$return.='<div class="forlader"><ul class="searchMemberList sortUserbox">';
				foreach($followingdbees as $Row):
				$checkImage = new Application_Model_Commonfunctionality();
                $userprofile2 = $checkImage->checkImgExist($Row['avatar'],'userpics','default-avatar.jpg');		

                if($extraparam=='OtherProfile')	
                {																						
                  $fcnt =$followigslague->chkfollowingcnt($Row['id'],$_SESSION['Zend_Auth']['storage']['UserID']);
				  $fellowtxt = ($fcnt['ID']>0)?'Unfollow':'Follow'; 	
				}
				else
				{
				  $fellowtxt = 'Unfollow';
				}
				$return.='<li class="usrList" id="'.$Row['UserID'].'">';
				if((int)$Row['type']==1){
				$return.='<a href="'.BASE_URL.'/user/'.$this->myclientdetails->customDecoding($Row['username']).'" class="userSrcProfilePic">
				<img src="'.IMGPATH.'/users/medium/'.$userprofile2.'" border="0"  width="90" height="90"/>
				</a>';
				$return.='<div class="userSrcProfileContainer">';
				}
				else
				{
				$return.='<div class="userSrcProfileContainer" style="margin-left:0px;">';

				}
				$return.='<h2 class="oneline">'.utf8_encode($this->myclientdetails->customDecoding($Row['name'])).' '.utf8_encode($this->myclientdetails->customDecoding($Row['lname'])).' ';
				if($Row['typename']!="")
				{
				$return.= '('.$Row['typename'].')';
				}
				$return.= '</h2>';

				if($this->myclientdetails->customDecoding($Row['title'])!=''){
				$return.= '<h3 class="oneline">'.utf8_encode($this->myclientdetails->customDecoding($Row['title'])).'</h3>';

				}
				if($this->myclientdetails->customDecoding($Row['company'])!=''){
				$return.= '<h3 class="oneline">'.utf8_encode($this->myclientdetails->customDecoding($Row['company'])).'</h3>';
				}
				if($this->myclientdetails->customDecoding($Row['Email'])!='' && $Row['Emailmakeprivate']==0){
				$return.= '<div class="srcEmailId oneline">'.utf8_encode($this->myclientdetails->customDecoding($Row['Email'])).'</div>';
				}

				$return.= '<div class="profileDashBtn">';

				if($this->session_data['UserID']!=$Row['id'] && $Row['id']!=adminID){
							

				$return.= ' <a class="btn messageina btn-mini" onclick="javascript:opensendmessage('.$Row['id'].', \''.utf8_encode($this->myclientdetails->customDecoding($Row['name'])).'\',this);" href="javascript:void(0)"><i class="fa fa-envelope-o"></i></a>

				';
				} else { $return.=' ';
				}
				$return.='</div></div></li>';																															
				
				if($counter%5==0) //$return.='<div class="next-line"></div>';
				$counter++;
				endforeach;
				$return.='</ul></div>';	
				if($sortval!="")
				{		
					$return.='<div class="show_more_main" id="show_more_main'.$Row["id"].'">
				                <div id="'.$Row["id"].'" class="show_more showMoreBtn" title="Load more posts" data-userID="'.$userID.'" data-caller="'.$caller.'" data-sortval="'.$sortval.'" data-regsortval="'.$regsortval.'" data-regsortvalcon="'.$regsortvalcon.'" data-OtherUserx="'.$OtherUser.'" data-list="Following"  title="Load more posts">Show more</div>
				                <div class="loding_txt"><i class="fa fa-spinner fa-spin fa-lg"></i></div>
				            </div>';
                }
				//$return.='<div class="noFound" style="margin-top:35px; display:none;">'.$UserNamemsg.' is currently not following anyone.</div>';
				}
				else 
					$return.='<div class="noFound" style="margin-top:35px;">'.$UserNamemsg.' is currently not following anyone.</div>';
				
				$return.='<br style="clear:both; font-zise:1px" /></div></div>';
			
				$data['status'] = 'success';
				$data['total'] = count($followingdbees);
				$data['offset'] = $offset;
				$data['content']= $return;
			}
			return $response->setBody(Zend_Json::encode($data));	
	    }

	     public function followeruserloadmoreAction()
	    {
			
			$data = array();
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(true);
			$response = $this->getResponse();
			$response->setHeader('Content-type', 'application/json', true);
			
			if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
			{
				$alphabets      =array();
				$followigslague = new Application_Model_Following();	
				$userprofile 	= new Application_Model_Profile();
				$userID 		= (int)$this->_request->getPost('userID');
				$limit 			= (int)$this->_request->getPost('limit');
				$offset	 		= (int)$this->_request->getPost('offset');
				$caller	 		= $this->_request->getPost('caller');
				$sortval		= $this->_request->getPost('sortval');
				$regsortval		= $this->_request->getPost('regsortval');
				$regsortvalcon	= $this->_request->getPost('regsortval');
				$letters 		= range('a', 'z');
				
				
				$limit 	        = ($limit=='')?9:$limit+1;
				$offset         = ($offset=='')?0:$offset;

				$user           = $userprofile->getuserbyprofileid($userID);

				$extraparam     = $this->_request->getPost('extraparam');
				$OtherUser     = $this->_request->getPost('OtherUser');

				
				if($regsortval!="")
				{
					$regsortval=$this->myclientdetails->customDecoding($regsortval);
				}
				
			  $followingdbees = $followigslague->getfolloweruserprofile($userID,$limit+1,$offset,$sortval,$regsortval,$this->session_data['usertype']);

				$offset = $offset+$limit;
				$return = '';
					
				$NameRow = $userprofile->getuserbyprofileid($userID);
						
				$UserName=ucwords($this->myclientdetails->customDecoding($NameRow['Name'])).' '.ucwords($this->myclientdetails->customDecoding($NameRow['lname'])).' is following';
				$UserNamemsg=ucwords($this->myclientdetails->customDecoding($NameRow['Name'])).' '.ucwords($this->myclientdetails->customDecoding($NameRow['lname']));

				if(count($followingdbees)>0) {
				$counter=1;				
				foreach($followingdbees as $Row):
				$checkImage = new Application_Model_Commonfunctionality();
                $userprofile2 = $checkImage->checkImgExist($Row['avatar'],'userpics','default-avatar.jpg');		

                if($extraparam=='OtherProfile')	
                {																						
                  $fcnt =$followigslague->chkfollowingcnt($Row['id'],$_SESSION['Zend_Auth']['storage']['UserID']);
				  $fellowtxt = ($fcnt['ID']>0)?'Unfollow':'Follow'; 	
				}
				else
				{
				  $fellowtxt = 'Unfollow';
				}
				$return.='<li class="usrList" id="'.$Row['UserID'].'">';
				if((int)$Row['type']==1){
				$return.='<a href="'.BASE_URL.'/user/'.$this->myclientdetails->customDecoding($Row['username']).'" class="userSrcProfilePic">
				<img src="'.IMGPATH.'/users/medium/'.$userprofile2.'" border="0"  width="90" height="90"/>
				</a>';
				$return.='<div class="userSrcProfileContainer">';
				}
				else
				{
				$return.='<div class="userSrcProfileContainer" style="margin-left:0px;">';

				}
				$return.='<h2 class="oneline">'.utf8_encode($this->myclientdetails->customDecoding($Row['name'])).' '.utf8_encode($this->myclientdetails->customDecoding($Row['lname'])).' ';
				if($Row['typename']!="")
				{
				$return.= '('.$Row['typename'].')';
				}
				$return.= '</h2>';

				if($this->myclientdetails->customDecoding($Row['title'])!=''){
				$return.= '<h3 class="oneline">'.utf8_encode($this->myclientdetails->customDecoding($Row['title'])).'</h3>';

				}
				if($this->myclientdetails->customDecoding($Row['company'])!=''){
				$return.= '<h3 class="oneline">'.utf8_encode($this->myclientdetails->customDecoding($Row['company'])).'</h3>';
				}
				if($this->myclientdetails->customDecoding($Row['Email'])!='' && $Row['Emailmakeprivate']==0){
				$return.= '<div class="srcEmailId oneline">'.utf8_encode($this->myclientdetails->customDecoding($Row['Email'])).'</div>';
				}

				$return.= '<div class="profileDashBtn">';

				if($this->session_data['UserID']!=$Row['id'] && $Row['id']!=adminID){
							

				$return.= ' <a class="btn messageina btn-mini" onclick="javascript:opensendmessage('.$Row['id'].', \''.utf8_encode($this->myclientdetails->customDecoding($Row['name'])).'\',this);" href="javascript:void(0)"><i class="fa fa-envelope-o"></i></a>

				';
				} else { $return.=' ';
				}
				$return.='</div></div></li>';																															
				
				if($counter%5==0) //$return.='<div class="next-line"></div>';
				$counter++;
				endforeach;
				
			$return.='<div class="show_more_main" id="show_more_main'.$Row["id"].'">
				                <div id="'.$Row["id"].'" class="show_more showMoreBtn" title="Load more posts" data-userID="'.$userID.'" data-caller="'.$caller.'" data-sortval="'.$sortval.'" data-regsortval="'.$regsortval.'" data-regsortvalcon="'.$regsortvalcon.'" data-OtherUserx="'.$OtherUser.'" data-list="Following"  title="Load more posts">Show more</div>
				                <div class="loding_txt"><i class="fa fa-spinner fa-spin fa-lg"></i></div>
				            </div>';
				//$return.='<div class="noFound" style="margin-top:35px; display:none;">'.$UserNamemsg.' is currently not following anyone.</div>';
				}
				else 
					$return.='<div class="noFound" style="margin-top:2px;">No more result found!.</div>';
				
				$return.='<br style="clear:both; font-zise:1px" /></div></div>';
			
				$data['status'] = 'success';
				$data['total'] = count($followingdbees);
				$data['offset'] = $offset;
				$data['content']= $return;
			}
			return $response->setBody(Zend_Json::encode($data));	
	    }




	public function contactlistAction()
	{
			
			$data = array();
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(true);
			$response = $this->getResponse();
			$response->setHeader('Content-type', 'application/json', true);
			
			if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
			{
				
				$alphabets      = array();
				$followigslague = new Application_Model_Following();	
				$userprofile    = new Application_Model_Profile();
				$userID         = (int)$this->_request->getPost('userID');
				$user           = $userprofile->getuserbyprofileid($userID);
				$extraparam     = $this->_request->getPost('extraparam');
				$sortval		= $this->_request->getPost('sortval');
				$regsortval		= $this->_request->getPost('regsortval');
				$regsortvalcon	= $this->_request->getPost('regsortval');
				$letters 		= range('a', 'z');			
	
				
				if($regsortval!="")
				{
					$regsortval=$this->myclientdetails->customDecoding($regsortval);
				}

				if($sortval!="")
				{
				 $limit 	        = ($limit=='')?9:$limit+1;
			    }
			    else
			    {
			      $limit 	        = '';
			    }

				$offset         = ($offset=='')?0:$offset;

				$followingdbeesforalpha = $followigslague->GetContactUserList($userID);

				foreach($followingdbeesforalpha as $usr):
					 $substr=substr($usr['name'],0,1);						
					 if(in_array($this->myclientdetails->customDecoding(strtolower($substr)), $letters))
					 {
		               $alphabets[]=$this->myclientdetails->customDecoding(strtolower($substr));
		             }	              
	        	endforeach; 

				if(sizeof($alphabets)>0)
                {
                	$alphabets = array_map("unserialize", array_unique(array_map("serialize", $alphabets)));
                	asort($alphabets);
                }

                $res = null;

				if(count($alphabets)>0)
				{	
					foreach ($alphabets as $v) {
						
					    if ($v != null) {
					        $res = $v;
					        break;
					    }
					}
				}

			  $first=$res['firstletter'];

			  if($extraparam=='MyProfile')
			  {
			  	  $regsortval=$this->myclientdetails->customEncoding($first,$call="something");
			  	  $regsortvalcon=$first;
			  	 
			  }
			  if($regsortval!="")
			  {
			  	 $activeclass='active';
			  	 $activeclass_earlier='';
			  	 $activeclass_latest='';
			  	 $style='display:block;';
			  }
			  if($sortval=='RegistrationDate-DESC')
			  {
			  	 $activeclass='';
			  	 $activeclass_earlier='';
			  	 $activeclass_latest='active';
			  	 $style='display:none;';
			  }
			  if($sortval=='RegistrationDate-ASC')
			  {
			  	 $activeclass='';
			  	 $activeclass_earlier='active';
			  	 $activeclass_latest='';
			  	 $style='display:none;';
			  }

    
				//	OtherProfile
				if($sortval!="")
				{
				 $followingdbees = $followigslague->GetContactUserList($userID,$limit+1,$offset,$sortval,$regsortval);
			    }
			    else
                {
                 $followingdbees = $followigslague->GetContactUserList($userID,$limit,$offset,$sortval,$regsortval);	
                }

				$NameRow = $userprofile->getuserbyprofileid($userID);
						
				$UserName='My Contacts List';
				$UserNamemsg=ucwords($this->myclientdetails->customDecoding($NameRow['Name'])).' '.ucwords($this->myclientdetails->customDecoding($NameRow['lname']));

				$return.= '<div id="middleWrpBox"><div class="user-name" style="margin-bottom:15px">'.$UserName.'</div><div class="next-line"><input type="hidden" id="CountContactList" value="'.count($followingdbeesforalpha).'"></div><div>';

				if($this->addtocontact==0){ 

					$return.= '<p style="margin-bottom:10px;">The platform admin has turned OFF the ability to add users to your Contacts list. </p>';
				}

				$return.= '<div id="mydbcontrols"></div>';
				if(count($followingdbees)>0) {

				$return.='<div class="searchHeaderAllUser sortUserList"><ul id="searchUserAllMenu">';
			    
				$return.='<li><span class="sortBytxt">Sort By:</span></li><li><a href="javascript:void(0);" class="SortAlphabetContacts '.$activeclass.'" data-xxx="alphabetically" data-char="'.$first.'" data_myid="'.$userID.'">Name</a></li><li><a href="javascript:void(0);" class="'.$activeclass_latest.'" id="ContactsLatestUser" data-sort="RegistrationDate-DESC" data_myid="'.$userID.'">Latest</a></li>
				<li><a href="javascript:void(0);" class="'.$activeclass_earlier.'" id="ContactsEarliestUser" data-sort="RegistrationDate-ASC" data_myid="'.$userID.'">Earliest</a></li></ul>';
				

				 if(count($alphabets)>0)
				{ 					 	
					$return.='<span id="MemberSortAlphbet" data-char="'.$first.'" style="'.$style.'">';
					 	$return.='<ul class="alphaMenu">';

					 	foreach($alphabets as $char):
						 	if(strtolower($regsortvalcon)==strtolower($char['firstletter']))
						 	  	 $style2='active';
						      else
						       	$style2='noclass';
						     $return.='<li style="width:5%;" data-raj='.$regsortvalcon.' data-raj2='.$char['firstletter'].'><a href="javascript:void(0);" class="SortAlphabetContacts '.$style2.'" data-char="'.$char['firstletter'].'" data_myid="'.$userID.'">'.ucfirst($char['firstletter']).'</a></li>';
					 	endforeach;
					 	$return.='</ul>';
					 	$return.='</span></div>';					 	
				}

				$counter=1;
				$return.='<div class="forloader"><ul class="searchMemberList sortUserbox">';
				foreach($followingdbees as $Row):
				

				$checkImage = new Application_Model_Commonfunctionality();
                $userprofile2 = $checkImage->checkImgExist($Row['avatar'],'userpics','default-avatar.jpg');		

                if($extraparam=='OtherProfile')	
                {																						
                  $fcnt =$followigslague->chkfollowingcnt($Row['id'],$_SESSION['Zend_Auth']['storage']['UserID']);
				  $fellowtxt = ($fcnt['ID']>0)?'Unfollow':'Follow'; 	
				}
				else
				{
				  $fellowtxt = 'Unfollow';
				}

				$conatctfcnt =$followigslague->CheckContactCount($_SESSION['Zend_Auth']['storage']['UserID'],$Row['id']);			

				$contacttext = ($conatctfcnt['id']>0)?'Remove from Contacts':'Add to Contacts';	

				if($this->addtocontact==0 && $contacttext=='Add to Contacts')
				{ 																												
					$contacttext='';
				}		
				
				$return.='<li class="usrList usrListforcount" style="text-align:left" id="contact_'.$Row['id'].'">';

				if((int)$Row['Status']==1){	
				$return.='<a href="'.BASE_URL.'/user/'.$this->myclientdetails->customDecoding($Row['username']).'" target="_top" class="userSrcProfilePic"><img src="'.IMGPATH.'/users/medium/'.$userprofile2.'" border="0" /></a>';
				$return.='<div class="userSrcProfileContainer">';
				}
		       if((int)$Row['Status']==0){
		      
		      	$return.='<div class="userSrcProfileContainer" style="margin-left:0px;">';
		      	 
		       }

				$return.='<h2 class="oneline">'.utf8_encode($this->myclientdetails->customDecoding($Row['name'])).' '.utf8_encode($this->myclientdetails->customDecoding($Row['lname'])).' ';
								if($Row['typename']!="")
									{
									 $return.= '('.$Row['typename'].')';
									}
									$return.= '</h2>';

								if($this->myclientdetails->customDecoding($Row['title'])!=''){
									$return.= '<h3 class="oneline">'.utf8_encode($this->myclientdetails->customDecoding($Row['title'])).'</h3>';
									
								}
								if($this->myclientdetails->customDecoding($Row['company'])!=''){
									$return.= '<h3 class="oneline">'.utf8_encode($this->myclientdetails->customDecoding($Row['company'])).'</h3>';
								}
								if($this->myclientdetails->customDecoding($Row['Email'])!='' && $Row['Emailmakeprivate']==0){
									$return.= '<div class="srcEmailId oneline">'.utf8_encode($this->myclientdetails->customDecoding($Row['Email'])).'</div>';
								}
								




			$return.='<div class="profileDashBtn">';

				if($Row['id']!=adminID)	
				$return.='<a class="btn btn-yellow fallowina btn-mini" onclick="javascript:followme('.$Row['id'].',this);" href="javascript:void(0);">
					<div id="followme-label" style="cursor: pointer;" data-user="following" data-id="'.$Row['id'].'">'.$fellowtxt.'</div>
				</a>';
			//if($this->addtocontact==1){
				$return.=' <a class="btn btn-yellow fallowina btn-mini addtoconxx" onclick="javascript:addtocontact('.$Row['id'].',this,\''.$this->myclientdetails->customDecoding($Row['name']).'\',\''.$fellowtxt.'\');" href="javascript:void(0);">
				 <div id="contact-label" style="cursor: pointer;">'.$contacttext.'</div>
				</a> ';
			     //}
				$return.=' <a class="btn messageina btn-mini" onclick="javascript:opensendmessage('.$Row['id'].', \''.$this->myclientdetails->customDecoding($Row['Name']).'\',this);" href="javascript:void(0)">
					<i class="fa fa-envelope-o"></i>
				</a>
				</div></div>
				</li>';
				if($counter%5==0) //$return.='<div class="next-line"></div>';
				$counter++;
				endforeach;
				$return.='</ul></div>';
				if($sortval!="")
				{		
					$return.='<div class="show_more_main" id="show_more_main'.$Row["id"].'">
				                <div id="'.$Row["id"].'" class="show_more showMoreBtn" title="Load more posts" data-userID="'.$userID.'" data-caller="'.$caller.'" data-sortval="'.$sortval.'" data-regsortval="'.$regsortval.'" data-regsortvalcon="'.$regsortvalcon.'" data-OtherUserx="'.$OtherUser.'" data-list="Contacts" title="Load more posts">Show more</div>
				                <div class="loding_txt"><i class="fa fa-spinner fa-spin fa-lg"></i></div>
				            </div>';
                }
								
				$return.='<br style="clear:both; font-zise:1px" /></div></div>';
			
				$data['status'] = 'success';
				$data['content']= $return;
			}
		}
			return $response->setBody(Zend_Json::encode($data));	
	    }

	public function contactlistloadmoreAction()
	{
			
			$data = array();
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(true);
			$response = $this->getResponse();
			$response->setHeader('Content-type', 'application/json', true);
			
			if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
			{
				
				$alphabets      = array();
				$followigslague = new Application_Model_Following();	
				$userprofile    = new Application_Model_Profile();
				$userID         = (int)$this->_request->getPost('userID');
				$user           = $userprofile->getuserbyprofileid($userID);
				$extraparam     = $this->_request->getPost('extraparam');
				$sortval		= $this->_request->getPost('sortval');
				$regsortval		= $this->_request->getPost('regsortval');
				$regsortvalcon	= $this->_request->getPost('regsortval');
				$offset	 		= (int)$this->_request->getPost('offset');
				$limit 			= (int)$this->_request->getPost('limit');
				$letters 		= range('a', 'z');			
	
				
				if($regsortval!="")
				{
					$regsortval=$this->myclientdetails->customDecoding($regsortval);
				}
				
				$limit 	        = ($limit=='')?9:$limit+1;			    

				$offset         = ($offset=='')?0:$offset;

				

			  if($extraparam=='MyProfile')
			  {
			  	  $regsortval=$this->myclientdetails->customEncoding($first,$call="something");
			  	  $regsortvalcon=$first;
			  	 
			  }
			  if($regsortval!="")
			  {
			  	 $activeclass='active';
			  	 $activeclass_earlier='';
			  	 $activeclass_latest='';
			  	 $style='display:block;';
			  }
			  if($sortval=='RegistrationDate-DESC')
			  {
			  	 $activeclass='';
			  	 $activeclass_earlier='';
			  	 $activeclass_latest='active';
			  	 $style='display:none;';
			  }
			  if($sortval=='RegistrationDate-ASC')
			  {
			  	 $activeclass='';
			  	 $activeclass_earlier='active';
			  	 $activeclass_latest='';
			  	 $style='display:none;';
			  }

    
				//	OtherProfile
				
				$followingdbees = $followigslague->GetContactUserList($userID,$limit+1,$offset,$sortval,$regsortval);

			    $offset = $offset+$limit;
				$return = '';

				$NameRow = $userprofile->getuserbyprofileid($userID);
						
				$UserName='My Contacts List';
				$UserNamemsg=ucwords($this->myclientdetails->customDecoding($NameRow['Name'])).' '.ucwords($this->myclientdetails->customDecoding($NameRow['lname']));		

				
				if(count($followingdbees)>0) {				

				 
				$counter=1;
				
				foreach($followingdbees as $Row):
				

				$checkImage = new Application_Model_Commonfunctionality();
                $userprofile2 = $checkImage->checkImgExist($Row['avatar'],'userpics','default-avatar.jpg');		

                if($extraparam=='OtherProfile')	
                {																						
                  $fcnt =$followigslague->chkfollowingcnt($Row['id'],$_SESSION['Zend_Auth']['storage']['UserID']);
				  $fellowtxt = ($fcnt['ID']>0)?'Unfollow':'Follow'; 	
				}
				else
				{
				  $fellowtxt = 'Unfollow';
				}

				$conatctfcnt =$followigslague->CheckContactCount($_SESSION['Zend_Auth']['storage']['UserID'],$Row['id']);			

				$contacttext = ($conatctfcnt['id']>0)?'Remove from Contacts':'Add to Contacts';	

				if($this->addtocontact==0 && $contacttext=='Add to Contacts')
				{ 																												
					$contacttext='';
				}		
				
				$return.='<li class="usrList usrListforcount" style="text-align:left" id="contact_'.$Row['id'].'">';

				if((int)$Row['Status']==1){	
				$return.='<a href="'.BASE_URL.'/user/'.$this->myclientdetails->customDecoding($Row['username']).'" target="_top" class="userSrcProfilePic"><img src="'.IMGPATH.'/users/medium/'.$userprofile2.'" border="0" /></a>';
				$return.='<div class="userSrcProfileContainer">';
				}
		       if((int)$Row['Status']==0){
		      
		      	$return.='<div class="userSrcProfileContainer" style="margin-left:0px;">';
		      	 
		       }

				$return.='<h2 class="oneline">'.utf8_encode($this->myclientdetails->customDecoding($Row['name'])).' '.utf8_encode($this->myclientdetails->customDecoding($Row['lname'])).' ';
								if($Row['typename']!="")
									{
									 $return.= '('.$Row['typename'].')';
									}
									$return.= '</h2>';

								if($this->myclientdetails->customDecoding($Row['title'])!=''){
									$return.= '<h3 class="oneline">'.utf8_encode($this->myclientdetails->customDecoding($Row['title'])).'</h3>';
									
								}
								if($this->myclientdetails->customDecoding($Row['company'])!=''){
									$return.= '<h3 class="oneline">'.utf8_encode($this->myclientdetails->customDecoding($Row['company'])).'</h3>';
								}
								if($this->myclientdetails->customDecoding($Row['Email'])!='' && $Row['Emailmakeprivate']==0){
									$return.= '<div class="srcEmailId oneline">'.utf8_encode($this->myclientdetails->customDecoding($Row['Email'])).'</div>';
								}
								




			$return.='<div class="profileDashBtn">';

				if($Row['id']!=adminID)	
				$return.='<a class="btn btn-yellow fallowina btn-mini" onclick="javascript:followme('.$Row['id'].',this);" href="javascript:void(0);">
					<div id="followme-label" style="cursor: pointer;" data-user="following" data-id="'.$Row['id'].'">'.$fellowtxt.'</div>
				</a>';
			//if($this->addtocontact==1){
				$return.=' <a class="btn btn-yellow fallowina btn-mini addtoconxx" onclick="javascript:addtocontact('.$Row['id'].',this,\''.$this->myclientdetails->customDecoding($Row['name']).'\',\''.$fellowtxt.'\');" href="javascript:void(0);">
				 <div id="contact-label" style="cursor: pointer;">'.$contacttext.'</div>
				</a> ';
			     //}
				$return.=' <a class="btn messageina btn-mini" onclick="javascript:opensendmessage('.$Row['id'].', \''.$this->myclientdetails->customDecoding($Row['Name']).'\',this);" href="javascript:void(0)">
					<i class="fa fa-envelope-o"></i>
				</a>
				</div></div>
				</li>';
				if($counter%5==0) //$return.='<div class="next-line"></div>';
				$counter++;
				endforeach;
				
				if($sortval!="")
				{		
					$return.='<div class="show_more_main" id="show_more_main'.$Row["id"].'">
				                <div id="'.$Row["id"].'" class="show_more showMoreBtn" title="Load more posts" data-userID="'.$userID.'" data-caller="'.$caller.'" data-sortval="'.$sortval.'" data-regsortval="'.$regsortval.'" data-regsortvalcon="'.$regsortvalcon.'" data-OtherUserx="'.$OtherUser.'" data-list="Contacts" title="Load more posts">Show more</div>
				                <div class="loding_txt"><i class="fa fa-spinner fa-spin fa-lg"></i></div>
				            </div>';
                }
								
				$return.='<br style="clear:both; font-zise:1px" /></div></div>';
			
				$data['status'] = 'success';
				$data['content']= $return;
			}
		}
			return $response->setBody(Zend_Json::encode($data));	
	    }

	public function checknewfollowingAction()
    {
    	$this->_helper->viewRenderer->setNoRender(true);
    	$data = array();
    	$response = $this->getResponse();
    	$response->setHeader('Content-type', 'application/json', true);	    	
    	$request = $this->getRequest();
    	$checkFlag= (int)$request->getPost('checkFlag');
    	$curCount = $request->getPost('currCount');	    	
    	$currCount=(isset($curCount))?$curCount:0;
    	$cookieuser=$this->_userid;
    	$users=array();
    	$followignotify = new Application_Model_Following();
    	$fuser = $followignotify->getfollowerusernotify($this->_userid);
    	foreach($fuser as $row):
    		$users[]=$row['User'];
    	endforeach;
    	if(count($users)>0)
    		$following=true;
    	else
    		$following=false;
    	
    	if($checkFlag == '') 
    		$checkFlag = 1;    	
    	if($following) 
    	{	
    		if($checkFlag=='0') // CHECK SINCE LAST LOGIN
    			$PostDate=$_COOKIE['lastloginseen'];
    		elseif($checkFlag=='1') // CHECK SINCE LAST SEEN
    		$PostDate=$_COOKIE['currloginlastseen'];
    		$TotalDbees= $followignotify->follingdbnotifys($users,$PostDate);
    		// CALCUATE TOTAL DBEES SINCE LAST LOGIN/LAST SEEN
			 	$TotalDbees=$TotalDbees+$currCount;	    		
    		setcookie('newnotificationcount', $TotalDbees, 0, '/');
    	}	    	
    	$data['status'] = 'success';
    	$data['TotalDbees']= $TotalDbees;
    	$this->_helper->layout->disableLayout();	    	
        return $response->setBody(Zend_Json::encode($data));	        
    }

	public function makefollowAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $filter   = new Zend_Filter_StripTags();
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            $userID = (int)$this->_request->getPost('user'); // type cast into interger
             // for token validation and cross side domain validation
            $validate = $this->commonmodel_obj->authorizeToken($this->_request->getPost('SessUser__'),$this->_request->getPost('SessId__'),$this->_request->getPost('SessName__'),$this->_request->getPost('Token__'),$this->_request->getPost('Key__'));
            if ($validate==false) 
            {
                $data['status']  = 'error';
                $data['message'] = 'Something went wrong please try again';
            }else if($userID!=0)
            {
                // get follower info
                $getUserInfo = $this->User_Model->getUserDetail($userID);               
              
                // check user is VIP OR not
                $vip = 0;
                
                if($getUserInfo['usertype']!=0 && $getUserInfo['usertype']!=6 )
                {
                	//echo $this->session_data['usertype'];
                	$vip=1;
                	if($this->session_data['usertype']!=0 && $this->session_data['usertype']!=6)
                	{
                		$vip = 0;
                	}
                }
                /*if(in_array($getUserInfo['usertype'],array('1','2','3','10')))
                {
                    if(in_array($this->session_data['usertype'],array('1','2','3','10')))
                            $vip=0;
                    else
                        $vip=1;
                }*/
                if($vip==0)
                {
                	//$latseen = date('Y-m-d H:i:s');
   					//setcookie("activityseencomments", $latseen, 0, '/');
                    $chkfollowing = $this->following->chkfollowing($userID,$this->_userid);
                    
                    //$chkChatUsers = $this->following->chkChatUsers($userID,$this->_userid);


                    if($chkfollowing['ID'])
                    {
                        if($this->following->deletefollowing($chkfollowing['ID']))
                        {
                        	//echo'----in here';die;
                        	$chkChatUsers = $this->following->chkChatUFollowUsers($userID,$this->_userid);
                            $data['message'] = 'You do not follow ' . $this->myclientdetails->customDecoding($getUserInfo['Name']) . ' any more';
                            $data['status'] = 'success';
                            $data['types'] = 'Follow';
                            $this->notification->commomInsert('4','13',$this->_userid,$this->_userid,$userID); // Insert for following activity
                        }else
                        {
                            $data['message'] = 'Something went wrong please try again';
                            $data['status'] = 'error';
                        }
                    }
                    else
                    {

                        $data = array('clientID'=>clientID,'User'=>$userID,'FollowedBy'=>$this->_userid,'StartDate'=>date('Y-m-d H:i:s'));
                        $success = $this->following->insertfollowing($data);
                        if($success!='')
                        {
                        	$data['chatList'] = $chkChatUsers = $this->following->chkChatUsers($userID,$this->_userid);
                            /**************mail section*****************/
                            $this->notification->commomInsert('4','4',$this->_userid,$this->_userid,$userID); // Insert for following activity
                            $UserEmail = $getUserInfo['Email'];
                            $UserName = $getUserInfo['Name'];
                            $FollowerUserID = $this->session_data['UserID'];
                            $FollowerName = $this->session_data['Name'];
                            $Followerlname = $this->session_data['lname'];
                            $FollowerEmail = $this->session_data['Email'];
                            $FollowerProfilePic = $this->session_data['ProfilePic'];
                            
                            $getusersnotiinfo = $this->myclientdetails->getfieldsfromtable(array('Followers'),'tblNotificationSettings','User',$userID);
			                if ($getusersnotiinfo[0]['Followers'] == 1) 
			                { 
	                            $EmailTemplateArray = array('Email' => $UserEmail,
	                                                'FollowerUserID' => $FollowerUserID,
	                                                'FollowerProfilePicval' => $FollowerProfilePicval,
	                                                'FollowerName'=> $FollowerName,
	                                                'lname'=>$Followerlname,
	                                                'case'=>22);
	                            $bodyContentmsgall = $this->dbeeComparetemplateOne($EmailTemplateArray);
	                        }
                            $data['status']  = 'success';
                            $data['message'] = 'You now follow '.$this->myclientdetails->customDecoding($getUserInfo['Name']);
                            $data['types'] = 'Unfollow';
                        }else
                        {
                            $data['status']  = 'error';
                            $data['message'] = 'Some thing went wrong here please try again';
                        }

                    }
                    
                }else
                {
                    //$data['message'] = $this->myclientdetails->customDecoding($getUserInfo['Name']).' is vip user you can not follow him';
                    $data['status'] = 'error';
                    $data['message'] = 'Sorry you cannot follow this user';
                }
            }
            
        } else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        $data['username'] = $getUserInfo['full_name']; 
        $data['ProfilePic'] = $getUserInfo['ProfilePic']; 
        //print_r($data); exit;
        return $response->setBody(Zend_Json::encode($data));
    }



    public function addtocontactAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $filter   = new Zend_Filter_StripTags();
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
           
            $userID = (int)$this->_request->getPost('user'); // type cast into interger
            $sessionuser = $_SESSION['Zend_Auth']['storage']['UserID']; // type cast into interger

            $followingoption = $this->_request->getPost('followingoption'); // type cast into interger

            $validate = $this->commonmodel_obj->authorizeToken($this->_request->getPost('SessUser__'),$this->_request->getPost('SessId__'),$this->_request->getPost('SessName__'),$this->_request->getPost('Token__'),$this->_request->getPost('Key__'));
            if ($validate==false) 
            {
                $data['status']  = 'error';
                $data['message'] = 'Something went wrong please try again';
            }
            else if($userID!=0)
            {
               $getUserInfo = $this->User_Model->getUserDetail($userID);
               $chkcontact = $this->following->chkcontact($sessionuser,$userID);

               //$chkChatUsers = $this->following->chkChatUsers($sessionuser,$userID);

              	  if($chkcontact['id'])
                    {
                        if($this->following->deleteFromContact($sessionuser,$userID))
                        {
                            $data['addlistcount'] = $this->following->CheckContactCount($sessionuser,$userID);
                            $data['status'] = 'success';
                            $data['types']  = 'Add to Contacts';
                            
                        }else
                        {
                            $data['message'] = 'Something went wrong please try again';
                            $data['status'] = 'error';
                        }
                    }
                    else
                    {
                   		$data = array('clientID'=>clientID,'added_by'=>$sessionuser,'UserId'=>$userID);
                         $success = $this->following->InsertInContactList($data);
                         
                        if($followingoption=='Follow')
                        {
	                         $data2 = array('clientID'=>clientID,'User'=>$userID,'FollowedBy'=>$sessionuser,'StartDate'=>date('Y-m-d H:i:s'));
	                         $success2 = $this->following->insertfollowing($data2);
	                         if($success2!='')
	                        {
	                        	$data['chatList'] = $chkChatUsers = $this->following->chkChatUsers($sessionuser,$userID);

	                            /**************mail section*****************/

	                            /*
	                            $this->notification->commomInsert('4','4',$sessionuser,$sessionuser,$userID); // Insert for following activity
	                            $UserEmail = $getUserInfo['Email'];
	                            $UserName = $getUserInfo['Name'];
	                            $FollowerUserID = $this->session_data['UserID'];
	                            $FollowerName = $this->session_data['Name'];
	                            $FollowerEmail = $this->session_data['Email'];
	                            $FollowerProfilePic = $this->
	                            ['ProfilePic'];                              
	                            $EmailTemplateArray = array('Email' => $UserEmail,
	                                                'FollowerUserID' => $FollowerUserID,
	                                                'FollowerProfilePicval' => $FollowerProfilePicval,
	                                                'FollowerName'=> $FollowerName,
	                                                'case'=>22);
	                            $bodyContentmsgall = $this->dbeeComparetemplateOne($EmailTemplateArray);
	                            */
	                             $data['types2'] = 'Unfollow';

	                        }else
	                        {
	                            $data['status']  = 'error';
	                            $data['message'] = 'Some thing went wrong here please try again';
	                        }
                      	}

                      	$data['status']  = 'success';                            
                        $data['types'] = 'Remove from Contacts';

                        
                    }
                
            }
            
        } else {
            $data['status']  = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }




    

}



