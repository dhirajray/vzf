<?php

class Admin_UserController extends IsadminController
{

	private $options;
	public $defaultimagecheck;
	/**
	 * Init
	 * 
	 * @see Zend_Controller_Action::init()
	 */
    public function init()
    {
	//print_r( $this->getInvokeArg('bootstrap')->getOptions());
	//die;
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
        $this->defaultimagecheck = new Admin_Model_Common();
        $this->userModal	=	new Admin_Model_User();
        parent::init();
    }
   
    public function callingcommonajaxAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $request    =   $this->getRequest()->getPost();

        $deshboard  =   new Admin_Model_Deshboard();
        $userModal	=	new Admin_Model_User();
        $common		=   new Admin_Model_Common();
       	

        $liveDbeeData = $deshboard->userdetailsDataChild($request['uid'],$request['calling'],$request['limit']);
		//$this->view->liveDbeeData = $liveDbeeData;
		if($request['calling']=='dbee')
		{
			
			$blocksdbee ='';
			$blocksdbee .= ''; 
			if (count($liveDbeeData)){ 
			$blocksdbee .='	<div id="liveDbee" class="dashContent">
						<ul>';
			foreach($liveDbeeData as $liveDbee) : 
			$blocksdbee .=' <li>';
										
						if(($liveDbee['type'] )==1) { 
							$dbtype			=	'text db';
							$descDisplay	=	substr($liveDbee->description,0,100);
						}
						if(($liveDbee['type'] )==2) { 
							$dbtype			=	'link db';
							$dbLink			=	$liveDbee->Link;
							$dbLinkTitle	=	$liveDbee->LinkTitle;
							$dbLinkDesc		=	$liveDbee->LinkDesc;
							$dbUserLinkDesc	=	!empty($liveDbee->UserLinkDesc) ? $liveDbee->UserLinkDesc : $liveDbee->LinkTitle;

							$descDisplay	=	'<div style=" background-color:#DAD9D9; padding:5px;">
							<div class="font12">'.$dbUserLinkDesc.' - 
							<a target="_blank" href="'.$dbLink.'">'.$dbLink.'</a>
							</div>
							</div>';
						}
						if(($liveDbee['type'] )==3) { 
							$dbtype	=	'pix db';

							$dbPic		=	$liveDbee->Pic;
							$dbPicDesc	=	$liveDbee->PicDesc;
                            $checkdbpic = $common->checkImgExist(($dbPic),'imageposts','default-avatar.jpg');
							$descDisplay	=	'<div style="float:left; width:360px; ">
							<div style="float:left;  width:auto; padding:3px; border:1px solid #CCCCCC;"><a href="#"><img src="'.IMGPATH.'/imageposts/small/'.$checkdbpic.'" width="40" border="0" /></a></div>
							<div class="font12" style="float:left; margin-left:10px; width:245px;">'.substr($dbPicDesc,0,100).'</div></div></p>';
						}
						if(($liveDbee['type'] )==4) { 
							$dbtype	=	'media db';

							$dbtype			=	'link db<div class="icon-db-link"></div>';
							$dbVid			=	$liveDbee->Vid;
							$dbVidDesc		=	$liveDbee->VidDesc;
							$dbLinkDesc		=	$liveDbee->LinkDesc;
							$dbUserLinkDesc	=	!empty($liveDbee->UserLinkDesc) ? $liveDbee->UserLinkDesc : $liveDbee->LinkTitle;
							$descDisplay	=	'<div style="float:left; width:360px; ">
							<div style="float:left;  width:auto; padding:3px; border:1px solid #CCCCCC;"><img border="0" src="https://i.ytimg.com/vi/'.$dbVid.'"></div>
							<div class="font12" style="float:left; width:245px;">'.substr($dbVidDesc,0,100).'</div></div></p>';
						}
						if(($liveDbee['type'] )==5) {  
							$dbPollText			=	$liveDbee->PollText;
							$dbtype	=	'polls';
							$descDisplay	=	substr($dbPollText,0,100);
						} 
                        $checkupic = $common->checkImgExist($liveDbee->image,'userpics','default-avatar.jpg');
				$blocksdbee .='<div class="userPic">
									<img src="'.IMGPATH.'/users/medium/'.$checkupic.'" width="80" height="80" border="0" />
								</div>
								<div class="userDetails">
									<div class="usertitle">
										<span class="username">'.$this->myclientdetails->customDecoding($liveDbee->username).'</span>
										<span class="userposted">has posted a  '. $dbtype.'</span> <br>
										<span class="userposted"> '. date('d M, Y',strtotime(($liveDbee->PostDate))).'</span>
										<span class="usrcomment">';
										
												$totcmnt 	=	$deshboard->getTotalComments($liveDbee->DbeeID);
												if($totcmnt>0)
												{
													$blocksdbee .= "<strong>".$totcmnt ." comments -</strong>";
												}
											
				$blocksdbee .='	<i> <a href="'. BASE_URL.'/dbeedetail/home/id/'.$liveDbee->DbeeID .'" target="_blank">see db </a> </i>

										</span>
									</div>
									<p>'. $descDisplay .'</p>
								</div>

							</li>';
						 endforeach; 
				$blocksdbee .='	</ul>
							<div class="bfooter">
								<span class="bfTotal">total posts - <strong>'.count($liveDbeeData).'</strong></span>';
								
				$blocksdbee .='</div>
					</div>';
					 } else { 
				$blocksdbee .='	<div class="dashBlockEmpty">post not found!</div>';
			 }	
			//$blocksdbee .='</div>';

			echo $blocksdbee;
		}

        

        
        exit;
	  
    }   

    public function userdetailsAction()
    {
        $request    =   $this->getRequest()->getParams();
        $deshboard  =   new Admin_Model_Deshboard();
        $userModal	=	new Admin_Model_User();

        $userid 	=	(int)$request['uid'];
        $type 	=	$request['type'];
        $common		=   new Admin_Model_Common();
        if($type=='vipuser')
        	$userRec	=	$userModal->getUsers($userid,'vipuser');
        else
          $userRec	=	$userModal->getUsers($userid,'userdetails');
      	

        $userArr	=	$userRec->toarray();
		$this->view->userid = $userid;
        $this->view->userArr = $userArr;
        
        $usertypeobj   = new Admin_Model_Deshboard();
        /****user type****/
        $typeid = array(o,6,10);
        $this->view->usertype = $usertypeobj->filterusertype($typeid);
        /****user type****/
        /****user details****/
        $this->view->userdetails = $usertypeobj->filteruserdetails();
        /****user details****/
	}  

	public function usermessagesAction()
    {
    	$request    =   $this->getRequest()->getPost();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$userModal	=	new Admin_Model_User();
		$common		=   new Admin_Model_Common();

		$messagesent=$userModal->GetUserMessageSent($request['uid']);
		$msgBlock ='';
		/*if ($this->getRequest()->isXmlHttpRequest())
		{*/

			$msgBlock .='
	   	  	<ul>';
		  	if(count($messagesent) >0 ) { 
			   	foreach ($messagesent as $key => $value) { 
				   	if($userModal->getUserName($value['MessageTo'])!="") 
				   	{
				   		/*echo $value['ProfilePic'].$ProfilePicpic = $common->checkImgExist($value['ProfilePic'],'userpics','default-avatar.jpg');
				   		<div class="userPic">
						   	<img src="'.BASE_URL.'/timthumb.php?src=/userpics/'.$ProfilePicpic.'&q=100&w=35&h=35" border="0"/>		   	 
						  </div>*/
						  $msgText = ($value['total']>1)?' messages':' message';
						$msgBlock .='<li>
							<div class="userDetails2">
								<span class="decsScoreCal">
								<a style="margin-left: 5px;" userid="'.$value['MessageFrom'].'>" class="show_details_user username" href="javascript:void(0)">'.$this->myclientdetails->customDecoding($userModal->getUserName($value['MessageFrom'])).'  </a>  &nbsp;&nbsp;sent '.$value['total'].$msgText.'  to 
								<a style="float:none;" userid="'.$value['MessageTo'].'" class="show_details_user username" href="javascript:void(0)"> '. $this->myclientdetails->customDecoding($userModal->getUserName($value['MessageTo'])).'</a> </span><br>
							</div>
						</li>';
				    } 
				   	$msgBlock .='</ul>';
			    }
		     } else {
		  		$msgBlock .='<div class="dashBlockEmpty">no messages found</div>';
		   }

			$msgBlock .='</ul> ';
	    //}
	    echo $msgBlock;
    }      

    public function updateuserstatusAction()
    {
    	$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		$userModal	=	new Admin_Model_User();
		

		if ($this->getRequest()->isXmlHttpRequest())
		{
			$userid = (int) $this->_request->getPost('userid');
			$status = (int) $this->_request->getPost('status');

			$this->userModal->updateUserStatus($userid,$status);
			
	    }
	    return $response->setBody(Zend_Json::encode($_POST));
    }

    public function markedvipAction()
    {
    	$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		$userModal	=	new Admin_Model_User();
		$socialModal	=	new Admin_Model_Social();
		if ($this->getRequest()->isXmlHttpRequest())
		{
			$userid = (int) $this->_request->getPost('userid');

			$this->userModal->updateMarkVIP($userid);
			$userdetailall = $userModal->getUserByUserID($userid);
			$Profile_URL= BASE_URL.'/user/'.$this->myclientdetails->customDecoding($userdetailall[0]['Username']);
			//$socialModal->commomInsert(46,56,'',$this->adminUserID,$userid);

			$EmailTemplateArray = array('uEmail'  => $userdetailall[0]['Email'],
                                    'uName'   => $userdetailall[0]['full_name'],
                                    'Profile_URL'  => $Profile_URL,
                                    'case'      => 8);
			$this->dbeeComparetemplateOne($EmailTemplateArray);
	    }
	    return $response->setBody(Zend_Json::encode($_POST));
    }
    
    public function livedbeeAction()
	{		    	
		$request    =   $this->getRequest()->getPost();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$userid = $request['uid'];
		$deshboard  =   new Admin_Model_Deshboard();
		$liveDbeeData = $deshboard->getuserdbee($userid);
	
		    $blocksdbee ='';
		
			
		if (count($liveDbeeData)){

			$blocksdbee .='	<ul>';
			foreach($liveDbeeData as $liveDbee) :
			$blocksdbee .=' <li>';
			$texttype = " created ";
			if($liveDbee['type']==1) {
				$dbtype			=	'a text post';
				$descDisplay	=	substr($liveDbee['description'],0,100);
			}
			if($liveDbee['Link']!='') {
				$dbtype			=	' a link';
				$dbLink			=	$liveDbee['Link'];
				$texttype = " posted  ";
				$dbLinkTitle	=	$liveDbee['LinkTitle'];
				$dbLinkDesc		=	$liveDbee['LinkDesc'];
				$dbUserLinkDesc	=	!empty($liveDbee['UserLinkDesc']) ? $liveDbee['UserLinkDesc'] : $liveDbee['LinkTitle'];

				$descDisplay	=	'<div style=" background-color:#DAD9D9; padding:5px;">
							<div class="font12">'.$dbUserLinkDesc.' - 
							<a target="_blank" href="'.$dbLink.'">'.$dbLink.'</a>
							</div>
							</div>';
			}
			if($liveDbee['PicDesc']!='') {
				$dbtype	=	' an image post';

				$dbPic		=	$liveDbee['Pic'];
				$dbPicDesc	=	$liveDbee['PicDesc'];
                $checkdbpic = $this->defaultimagecheck->checkImgExist($dbPic,'imageposts','default-avatar.jpg');
				$descDisplay	=	'<div style="float:left; width:360px; ">
							<div style="float:left;  width:auto; padding:3px; border:1px solid #CCCCCC;"><a href="#"><img src="'.IMGPATH.'/imageposts/small/'.$checkdbpic.'" width="40" border="0" /></a></div>
							<div class="font12" style="float:left; margin-left:10px; width:245px;">'.substr($dbPicDesc,0,100).'</div></div></p>';
			}
			if($liveDbee['Vid']!='') {
				$dbtype	=	'a video post';

				$dbVid			=	$liveDbee['VidID'];
				$dbVidDesc		=	$liveDbee['VidDesc'];
				$dbLinkDesc		=	$liveDbee['LinkDesc'];
				$dbUserLinkDesc	=	!empty($liveDbee['UserLinkDesc']) ? $liveDbee['UserLinkDesc'] : $liveDbee['LinkTitle'];
				$descDisplay	=	'<div style="float:left; width:360px; ">
							<div style="float:left;  width:auto; padding:3px; border:1px solid #CCCCCC;"><img border="0" src="https://i.ytimg.com/vi/'.$dbVid.'/0.jpg" width="40px" height="40px"></div>
							<div class="font12" style="float:left; width:245px;">'.substr($dbVidDesc,0,100).'</div></div></p>';
			}
			if($liveDbee['type']==5) {
				$dbPollText			=	$liveDbee['PollText'];
				$dbtype	=	' a voting poll';
				$descDisplay	=	substr($dbPollText,0,100);
			}

				
            $liveDbepic = $this->defaultimagecheck->checkImgExist($liveDbee['image'],'userpics','default-avatar.jpg');
			//$checkupic = $defaultimagecheck->checkImgExist($liveDbee->image,'userpics','default-avatar.jpg');
			$blocksdbee .='<div class="userPic">
									<img src="'.IMGPATH.'/users/medium/'.$liveDbepic.'" width="80" height="80" border="0" />
								</div>
								<div class="userDetails">
									<div class="usertitle">
										<span class="username"><a href="javascript:void(0)" class="show_details_user username" userid="'.$liveDbee->User.'">'.$this->myclientdetails->customDecoding($liveDbee->username).' '.$this->myclientdetails->customDecoding($liveDbee->lname).'</a></span>
										<span class="userposted">'.$texttype. $dbtype.'</span>
										<span class="userposted"> on '.date('d M, Y',strtotime($liveDbee['PostDate'])).'</span>
										<br><span class="userposted">';
										$totcmnt 	=	$deshboard->getTotalComments($liveDbee['DbeeID']);
										if($totcmnt>0)
										{
											if($totcmnt==1) $cmntCount = $totcmnt.' comment'; else $cmntCount = $totcmnt.' comments';
											$blocksdbee .= $cmntCount;
										}
										$blocksdbee .='</span>
										<span class="usrcomment">';
											$blocksdbee .='	<i> <a href="'.BASE_URL.'/admin/dashboard/post/id/'.$liveDbee['DbeeID'] .'" target="_blank">More</a> | <a href="'.BASE_URL.'admin/dashboard/postreport/m_-_xxp=t/'.base64_encode($liveDbee                 ['DbeeID']).'">Post report</a></i>
										</span>
									</div>
									<br><p>'. $descDisplay .'</p>
								</div>

							</li>';
						 endforeach; 
		$blocksdbee .='	</ul>
							<div class="bfooter"><a href="'.BASE_URL.'admin/dashboard/post"  class="btn">View all</a></div>
					</div>';
					 } else { 
		$blocksdbee .='	<div class="dashBlockEmpty">no posts found</div>';
			 }	
		$blocksdbee .='</div>';	
		echo $blocksdbee ;
	} 
    
	public function livecommentAction()
	{
		$request    =   $this->getRequest()->getPost();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$userid = $request['uid'];
		$deshboard  =   new Admin_Model_Deshboard();
		$latestCommentData = $deshboard->callingcommoncomment($userid);
		
		$ret	='';
		$load	=0;
		if (count($latestCommentData)){

			$ret	.= '<ul>';
			foreach($latestCommentData as $latestComment) :

			$dbtype	='';

			if($latestComment['type']==1) {
				//$dbtype			=	'text db';
				$descDisplay	=	substr($latestComment['Comment'],0,100);
			}
			if($latestComment['type']==2) {
				//$dbtype			=	'link db';
				$dbLink			=	$latestComment['Link'];
				$dbLinkTitle	=	$latestComment['LinkTitle'];
				$dbLinkDesc		=	$latestComment['LinkDesc'];
				$dbUserLinkDesc	=	!empty($latestComment['UserLinkDesc']) ? $latestComment['UserLinkDesc'] : $latestComment['LinkTitle'];

				$descDisplay	=	'<div style="padding:5px; margin-top:5px; margin-bottom:5px;">
				<div class="font12">'.$dbUserLinkDesc.' -
				<a target="_blank" href="'.$dbLink.'">'.$dbLink.'</a>
				</div>
				<div class="font12" style="margin-top:10px;"></div></div>';
			}
			if($latestComment['type']==3) {
				//	$dbtype	=	'pix db';

				$dbPic		=	$latestComment['Pic'];
				$dbPicDesc	=	$latestComment['PicDesc'];
                $checkdbpic = $this->defaultimagecheck->checkImgExist($dbPic,'imageposts','default-avatar.jpg');
				$descDisplay	=	'
				<div style="float:left;  width:auto; padding:3px; border:1px solid #CCCCCC;"><a href="#"><img src="'.IMGPATH.'/imageposts/small/'.$checkdbpic.'" width="50" border="0" /></a></div>
				<div class="font12" style="float:left; margin-left:10px; width:245px;">'.substr($dbPicDesc,0,100).'</div>';
			}
			if($latestComment->type==4) {
				//$dbtype	=	'media db';

				$dbtype			=	'link post<div class="icon-db-link"></div>';
				$dbVid			=	$latestComment['VidID'];
				$dbVidDesc		=	$latestComment['VidDesc'];
				$dbLinkDesc		=	$latestComment['LinkDesc'];
				$dbUserLinkDesc	=	!empty($latestComment['UserLinkDesc']) ? $latestComment['UserLinkDesc'] : $latestComment['LinkTitle'];

				$descDisplay	=	'<div style="float:left;  width:auto; padding:3px; border:1px solid #CCCCCC;"><img width="50" height="50" border="0" src="'.$dbVid.'"></div>
				<div class="font12" style="float:left; width:245px;">'.substr($dbUserLinkDesc,0,100).'</div>';
			}
			if($latestComment['type']==5) {
				$dbPollText			=	$latestComment['PollText'];
				//$dbtype	=	'polls';
				$descDisplay	=	substr($dbPollText,0,100);
			}



            $latestCommentapic =$this->defaultimagecheck->checkImgExist($latestComment['image'],'userpics','default-avatar.jpg');
			$ret	.= '<li>
			<div class="userPic"><img src="'.IMGPATH.'/users/medium/'.$latestCommentapic.'" width="80" height="80" border="0" /></div>
			<div class="userDetails">
			<div class="usertitle">
			<a href="#" class="username"><a userid="'.$latestComment['UserID'].'" class="show_details_user username" href="javascript:void(0)">'.$this->myclientdetails->customDecoding($latestComment['username']).' '.$this->myclientdetails->customDecoding($latestComment['lname']).'</a>
			<span class="userposted">has commented on <strong><a userid="'.$latestComment['OwnerID'].'" class="show_details_user username" href="javascript:void(0)">'.$this->myclientdetails->customDecoding($latestComment['Ownername']).' '.$this->myclientdetails->customDecoding($latestComment['Ownerlname']).'</a>&rsquo;s - post'. $dbtype.'</strong></span>	<span class="usrcomment">'. date('d M, Y',strtotime($latestComment['CommentDate'])).'<i> - <a href="'.BASE_URL.'/dbee/'.$latestComment['dburl'] .'" target="_blank">view </a> </i></span>
			</div> <br>
			<p>'. $descDisplay.'</p>
			</div>
			</li>';
			endforeach;
			$ret	.= '</ul>
			<div class="bfooter"><a href="'.BASE_URL.'/admin/dashboard/comments"  class="btn">View all</a></div>
			</div>';
		} else {
			$ret	.= '<div class="dashBlockEmpty">no comments found</div>';
		}
		echo $ret ;
		exit;
	}
	
	public function livegroupusrAction()
	{
			$request    =   $this->getRequest()->getPost();
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(true);
			$userid = $request['uid'];
			$deshboard  =   new Admin_Model_Deshboard();			
			$liveGroupData = $deshboard->getusergroup($userid);			
			$data = '';
				if (count($liveGroupData))
				{
						$data .='<ul>';
						foreach($liveGroupData as $liveGroup) :
								$livegrouppic = $this->defaultimagecheck->checkImgExist($liveGroup['image'],'userpics','default-avatar.jpg');
								
								$data .='<li>
								<div class="userPic"><img src="'.IMGPATH.'/users/medium/'. $livegrouppic .'" width="80" height="80" border="0" /></div>
								<div class="userDetails">
								<div class="usertitle">
								<a href="#" class="username">'. htmlentities($liveGroup['GroupName']).'</a>
								</div>
								<div class="subhUsertitle">
								<span class="gcreatedUser">
								Created by <a href="#">'. $this->myclientdetails->customDecoding($liveGroup['username']).' '. $this->myclientdetails->customDecoding($liveGroup['lname']).'</a>
								'.$liveGroup->TypeName.'
								</span>
								<span class="gCreatedDate1"> on '.date('d M, Y',strtotime($liveGroup['Gdate'])).'</span>
								</div>
								<p>';
								if($liveGroup['description'])
									$data .= htmlentities(substr($liveGroup['description'],0,100));
								 else
									$data .= '&nbsp';
								
								$data .='</p></div></li>';
						 endforeach;
						$data .='</ul>';
						
						 if(count($liveGroupData)>4){
						 	$data .='<div class="bfooter"><a href="'. BASE_URL.'/admin/dashboard/groups/uid/'.$liveGroup['UserID'].'" class="btn">View all</a></div>';
						  } 
						
				} else {
						$data .='<div class="dashBlockEmpty">no groups found</div>';
					   } 
		echo $data;
	}
	
	public function livescoreusrAction()
	{
			$request    =   $this->getRequest()->getPost();
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(true);
			$userid = $request['uid'];
			$deshboard  =   new Admin_Model_Deshboard();			
			$liveScoreData = $deshboard->callingcommonscore($userid);
			$configurations = $deshboard->getConfigurations();
			$post_score_setting = json_decode($configurations['ScoreNames'],true);
			$data = '';
			$data .= '<ul>';
			 if (count($liveScoreData)){
				foreach($liveScoreData as $liveScore) :
					if($liveScore['type']==1)  $dbtype =   'post'; else  $dbtype = 'comment';
				
					if($liveScore['Score']==1){ 
						//$scorediv   ='<span class="scoreSprite scoreLove"></span>' ; 
						$scorediv=$this->myclientdetails->ShowScoreIcon($post_score_setting[1]['ScoreIcon1']);
					} 
					else if ($liveScore['Score']==2){ 
						//$scorediv   = '<span class="scoreSprite scoreLike"></span>' ;  
						$scorediv=$this->myclientdetails->ShowScoreIcon($post_score_setting[2]['ScoreIcon2']);
					} 
					else if ($liveScore['Score']==3){ 
						//$scorediv   = '<span class="scoreSprite scoreFft"></span>' ;   
						//$scorediv=$this->myclientdetails->ShowScoreIcon($post_score_setting[1]['ScoreIcon1']);
					} 
					else if ($liveScore['Score']==4){ 
						//$scorediv   = '<span class="scoreSprite scoreUnLike"></span>'; 
						$scorediv=$this->myclientdetails->ShowScoreIcon($post_score_setting[3]['ScoreIcon3']);
					} 
					else if ($liveScore['Score']==5){ 
									//$scorediv   = '<span class="scoreSprite scoreHate"></span>' ;
									$scorediv=$this->myclientdetails->ShowScoreIcon($post_score_setting[4]['ScoreIcon4']); 

					}
			        $livescorepic = $this->defaultimagecheck->checkImgExist($liveScore['image'],'userpics','default-avatar.jpg');
					$ret	.= '<li>
									<div class="userPic"><img src="'.IMGPATH.'/users/medium/'.$livescorepic .'" width="80" height="80" border="0" /></div>
									<div class="userDetails">
										<span class="decsScoreCal"><a href="javascript:void(0)" class="show_details_user" userid="'. $liveScore['UserID'].'"><span class="username">'.$this->myclientdetails->customDecoding($liveScore['username']).' '.$this->myclientdetails->customDecoding($liveScore['lname']).'</span></a> scored <a href="javascript:void(0)" class="show_details_user" userid="'. $liveScore['OwnerID'].'"><span class="username">'. $this->myclientdetails->customDecoding($liveScore['Ownername']).' '.$this->myclientdetails->customDecoding($liveScore['Ownerlname']) .'&rsquo;s </span></a>
										'. $dbtype.'</span>'. $scorediv.'		

									</div>
								</li>';
					endforeach;
					$ret	.= '</ul>
					<div class="bfooter"><a href="'.BASE_URL.'/admin/dashboard/scores" class="btn">View all</a></div>';
				} else {
					$ret	.= '<div class="dashBlockEmpty">no scores found</div>';
				}
				echo $ret ;
				exit;
			
	}
	
	public function livescoremeusrAction()
	{
			$request    =   $this->getRequest()->getPost();
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(true);
			$userid = $request['uid'];
			$deshboard  =   new Admin_Model_Deshboard();
			$configurations = $deshboard->getConfigurations();
			$post_score_setting = json_decode($configurations['ScoreNames'],true);
			$liveScoreonmeData = $deshboard->callingcommononmescore($userid);
			
			$data = '';
			$data .= '<ul>';
			 if (count($liveScoreonmeData)){
				foreach($liveScoreonmeData as $liveScore) :
					if($liveScore['type']==1)  $dbtype =   'post'; else  $dbtype = 'comment';
				
					if($liveScore['Score']==1){ 
						//$scorediv   ='<span class="scoreSprite scoreLove"></span>' ; 
						$scorediv=$this->myclientdetails->ShowScoreIcon($post_score_setting[1]['ScoreIcon1']);
					} 
					else if ($liveScore['Score']==2){ 
						//$scorediv   = '<span class="scoreSprite scoreLike"></span>' ;  
						$scorediv=$this->myclientdetails->ShowScoreIcon($post_score_setting[2]['ScoreIcon2']);
					} 
					else if ($liveScore['Score']==3){ 
						//$scorediv   = '<span class="scoreSprite scoreFft"></span>' ;   
						//$scorediv=$this->myclientdetails->ShowScoreIcon($post_score_setting[1]['ScoreIcon1']);
					} 
					else if ($liveScore['Score']==4){ 
						//$scorediv   = '<span class="scoreSprite scoreUnLike"></span>'; 
						$scorediv=$this->myclientdetails->ShowScoreIcon($post_score_setting[3]['ScoreIcon3']);
					} 
					else if ($liveScore['Score']==5){ 
									//$scorediv   = '<span class="scoreSprite scoreHate"></span>' ;
									$scorediv=$this->myclientdetails->ShowScoreIcon($post_score_setting[4]['ScoreIcon4']); 

					}
			        $livescorepic = $this->defaultimagecheck->checkImgExist($liveScore['image'],'userpics','default-avatar.jpg');
					$ret	.= '<li>
									<div class="userPic"><img src="'.IMGPATH.'/users/small/'.$livescorepic .'" width="80" height="80" border="0" /></div>
									<div class="userDetails">
										<span class="decsScoreCal"><a href="javascript:void(0)" class="show_details_user" userid="'. $liveScore['UserID'].'"><span class="username">'.$this->myclientdetails->customDecoding($liveScore['username']).' '.$this->myclientdetails->customDecoding($liveScore['lname']).'</span></a> scored <a href="javascript:void(0)" class="show_details_user" userid="'. $liveScore['OwnerID'].'"><span class="username">'. $this->myclientdetails->customDecoding($liveScore['Ownername']).' '.$this->myclientdetails->customDecoding($liveScore['Ownerlname']) .'&rsquo;s </span></a>
										'. $dbtype.'</span>'. $scorediv.'		

									</div>
								</li>';
					endforeach;
					$ret	.= '</ul>
					<div class="bfooter"><a href="'.BASE_URL.'/admin/dashboard/scores" class="btn">View all</a></div>';
				} else {
					$ret	.= '<div class="dashBlockEmpty">no scores found</div>';
				}
				echo $ret ;
				exit;
			
	}
	
	
    public function reloadactivityAction()
    {
    	require_once 'includes/globalfileadmin.php';
    	$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
    	$request    =   $this->getRequest()->getPost();
    	$deshboard  =   new Admin_Model_Deshboard();
    	$userModal	=	new Admin_Model_User();
    	
    	$lastactivityData = $deshboard->lastactivityData($request['userid']);
    	$this->view->lastactivityData = $lastactivityData;
    	
    	$data = array();
    
    	if (count($lastactivityData)){ 
    	$data['content'] = '<ul>';
				
					foreach($lastactivityData as $lastactivity) :	

						$seedbeelink 	= '';

						/*if($lastactivity['act_message'] == '12') {
							$hashtag	= '#Q&A';
							$class	= 'class="notifyquestion"';
						}
						else if($lastactivity['act_message'] == '11') {
							$hashtag	= '#pendingquestion';
							$class	= 'class="notifyquestion"';
						}
						else if($lastactivity['act_message'] == '20') {
							$hashtag	= '#myquestion';
							$class	= 'class="notifyquestion"';
						}
						else if($lastactivity['act_message'] == '21') {
							$hashtag	= '#Q&A';
							$class	= 'class="notifyquestion"';
						}
						else if($lastactivity['act_message'] == '3') 
						{
							$hashtag	= '#comment-block-'.$lastactivity['act_cmnt_id'];
						}
						else if($lastactivity['act_message'] == '6') 
						{
							$hashtag	= '#comment-block-'.$lastactivity['act_cmnt_id'];
						}
						else 
						{
							$hashtag	= ''; 
							$class	= '';
						}
						

						if(($lastactivity['act_type']==12 || $lastactivity['act_type']==13 )  ){	
							$seedbeelink ='<span class="usrcomment"><i><a target="_blank" href="'.BASE_URL.'/group/groupdetails/group/'.$lastactivity['act_typeId'].'">view </a> </i></span>';
						}	
						else if($lastactivity['act_type']==14){
							$seedbeelink ='<span class="usrcomment"><i><a target="_blank" href="'.BASE_URL.'/league/index/id/'.$lastactivity['act_typeId'].'">view </a> </i></span>';

						}
						else if($lastactivity['act_type']==10){
							$seedbeelink ='<span class="usrcomment"><i><a target="_blank" href="'.BASE_URL.'/message">view </a> </i></span>';
						}
						else
						{
							$seedbeelink ='<span class="usrcomment"><i><a target="_blank" href="'.BASE_URL.'/dbee/dbeedetail/home/id/'.$lastactivity['act_typeId'].$hashtag.'"> view </a> </i></span>';
						}*/

						$xxx = '<a userid="'.$lastactivity['owUserID'].'" class="show_details_user username" href="javascript:void(0)">'.$this->myclientdetails->customDecoding($lastactivity['owusername']).' '.$this->myclientdetails->customDecoding($lastactivity['owuserlname']).'</a>';
						//$data['content'] .= $lastactivity['act_type'].' # '. $seedbeelink.'<br>';
						$notificationdate = '<br><span>'.date('d M, Y ', strtotime($lastactivity['act_date'])).'</span>';
						$data['content'] .= '<li>
							 <div class="userPic"><img src="'.IMGPATH.'/users/small/'.$lastactivity['image'] .'" width="35" height="35" border="0" /></div>
							<div class="userDetails">
								<span class="decsScoreCal"><a userid="'.$lastactivity['UserID'].'" class="show_details_user username" href="javascript:void(0)">'. $this->myclientdetails->customDecoding($lastactivity['username']).'  '. $this->myclientdetails->customDecoding($lastactivity['lname']).'</a> '.str_replace("XXX", $xxx, $activityuserMsg[$lastactivity['act_message']]) .$scoreType[$lastactivity['act_score_type']].'</span><br>
								 '.$seedbeelink.$notificationdate.' 						
							</div>
						</li>';
					 endforeach;
		
					
			$data['content'] .= '</ul>';
    	 } else {
    		$data['content'] .='<div class="dashBlockEmpty">no activity by this user</div>';
    	 }  
    	
    	return $response->setBody(Zend_Json::encode($data));
    	
    	//echo  $cdatajson;
    	exit;
    }

    
    public function getuserdetailsAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $request    =   $this->getRequest()->getPost();
        $deshboard  =   new Admin_Model_Deshboard();
        $userModal	=	new Admin_Model_User();

        $common		=   new Admin_Model_Common();


		$TotalChartData = 	$deshboard->TotalChartData($request['userid']);
		$imparr			=	explode('=',$TotalChartData );

		$getchartData 	= 	$deshboard->TotalChartDataChild($request['userid'],'dbee');

		$getcharttotal 	=	array();
		$TypeDBchart	=	array();

		foreach ($getchartData as $key => $value) 
		{
			 $getcharttotal[] = (int)$value['dbee'];
			 if($value['Type']==5) $ext = ''; else $ext = ' Posts';
			 $TypeDBchart[]	  =	''. $common->getdbeeType_CM($value['Type']).''.$ext;
		}
		//*********** End of Dbee Types and Start of comments section ***********//

		$getcommentData 	= 	$deshboard->TotalChartDataChild($request['userid'],'comment');
		$cmntImparr			=	explode('=',$getcommentData );

		//*********** End of comments Types and Start of Score section ***********//

		$getscoreData 	= 	$deshboard->TotalChartDataChild($request['userid'],'score');
		$scoreImparr	=	explode('=',$getscoreData );

		//*********** End of comments Types and Start of Score section ***********//

		$getgroupData 	= 	$deshboard->TotalChartDataChild($request['userid'],'group');
		$getgrouptotal 	=	array();
		$TypeDBgroup	=	array();

		foreach ($getgroupData as $key => $value) 
		{
			 $getgrouptotal[] = (int)$value['group'];
			 $TypeDBgroup[]	  =	$common->getgroupType_CM($value['type']) .' Groups ';
		}

		$chkdatatotal 	=	$common->getvaluetodrawchart_CM((int)$imparr[1],(int)$cmntImparr[0],(int)$scoreImparr[0], (int)$imparr[4]);
		
		if($chkdatatotal>0)
		{
			

			$legends ='<div class="totalStatusActivites">
					    <ul>
					     <li style="background:#2f7ed8">Posts</li>
					     <li style="background:#0d233a">Comments</li>';
					     if( $this->plateform_scoring==0)
					     $legends .='<li style="background:#8bbc21">Scores</li>';
					     $legends .='<li style="background:#910000">Group</li>
					    </ul>
					    <div>The chart below lists all the users activity. The inner circle shows consolidated data and the outer circles show the break-up.</div>
					   </div>';
			if( $this->plateform_scoring==0)
			{
				$colors			=	array('#2f7ed8','#0d233a','#8bbc21','#910000');
				$cdata		=	array( 
									array('y'=> (int)$imparr[1], 'color'=> $colors[0],'drilldown'=> array('name'=> 'DBEE', 'categories' => $TypeDBchart,'data' => $getcharttotal,'color' => 'colors[0]')),

									array('y'=> (int)$cmntImparr[0], 'color'=> $colors[1],'drilldown'=> array('name'=> 'Comments', 'categories' => array('Comments Received', 'Comment Made'),'data' => array( (int)$cmntImparr[1],(int)$cmntImparr[2]),'color' => 'colors[1]')),

									array('y'=> (int)$imparr[4], 'color'=> $colors[3],'drilldown'=> array('name'=> 'Group', 'categories' => $TypeDBgroup,'data' => $getgrouptotal,'color' => 'colors[3]')),

									array('y'=> (int)$scoreImparr[0], 'color'=> $colors[2],'drilldown'=> array('name'=> 'Scores', 'categories' => array('Scores Given', 'Scores Received'),'data' => array((int)$scoreImparr[1], (int)$scoreImparr[2]),'color' => 'colors[2]')),

									
									
								);	
			}
			else
			{
				$colors			=	array('#2f7ed8','#0d233a','#910000');
				$cdata		=	array( 
									array('y'=> (int)$imparr[1], 'color'=> $colors[0],'drilldown'=> array('name'=> 'DBEE', 'categories' => $TypeDBchart,'data' => $getcharttotal,'color' => 'colors[0]')),

									array('y'=> (int)$cmntImparr[0], 'color'=> $colors[1],'drilldown'=> array('name'=> 'Comments', 'categories' => array('Comments Received', 'Comment Made'),'data' => array( (int)$cmntImparr[1],(int)$cmntImparr[2]),'color' => 'colors[1]')),

									array('y'=> (int)$imparr[4], 'color'=> $colors[2],'drilldown'=> array('name'=> 'Group', 'categories' => $TypeDBgroup,'data' => $getgrouptotal,'color' => 'colors[2]')),
									
								);	
			}		   
			

			$cdatajson	=	json_encode($cdata);
		} else {
			$cdatajson	=	99;
		}

        $userRec	=	$userModal->getUsers($request['userid'],'haveUid');
        $userArr	=	$userRec->toarray();

        $logindate  = 	!(int)($userArr[0]['LastLoginDate']) ? 'Not Available' : date('d-M-Y h:i A',strtotime($userArr[0]['LastLoginDate']));
        $pupddate 	=	!(int)($userArr[0]['LastUpdateDate']) ? 'Not Available' : date('d-M-Y',strtotime($userArr[0]['LastUpdateDate']));
       	if($userArr[0]['usertype']==0 || $userArr[0]['usertype']==6 ) $useridtype = $userArr[0]['UserID'];
       	else  $useridtype = $userArr[0]['UserID'].'/type/vipuser';

        //print_r($userRec->toarray());
        $ProfilePicpic = $this->defaultimagecheck->checkImgExist($userArr[0]['ProfilePic'],'userpics','default-avatar.jpg');
        $birthDate = '';
        if($userArr[0]['Birthdate']!='0000-00-00'){
        	 $birthDate = date('d-M-Y',strtotime($userArr[0]['Birthdate']));
        }
        $ret 	=	'';
        if($userArr[0]['isonline']==1)
  			$userOnline = '<span class="onlineuser">Online</span>';
  		else
  			$userOnline = '<span class="offlineuser">Offline</span>';
        $ret 	.= '<div>
		  	<div class="popUserDetails">
		  		<div class="popUserPic">
		  			<div class="box_image">
						<img src="'.IMGPATH.'/users/medium/'.$ProfilePicpic.'" width="100" height="100" border="0"/>
						'.$userOnline.'
					</div>
		  		</div>
		  		<div class="popColUserDetailsWrapper">
			  		<div class="popColUserDetails">
			  			<div class="userdtlrow">
				  			<div class="userdtlrow105">Name</div>
				  			<div class="userdtlrow150 oneline">&nbsp;'.htmlentities( $this->myclientdetails->customDecoding($userArr[0]['Name'])) .' '.htmlentities( $this->myclientdetails->customDecoding($userArr[0]['lname'])) .'</div>
			  			</div>

			  			<div class="userdtlrow">
				  			<div class="userdtlrow105">User Name</div>
				  			<div class="userdtlrow150 oneline">&nbsp;'. htmlentities( $this->myclientdetails->customDecoding($userArr[0]['Username'])).'</div>
			  			</div>
			  			<div  class="userdtlrow">
				  			<div class="userdtlrow105">User Email</div>
				  			<div class="userdtlrow150 oneline" title="'.htmlentities( $this->myclientdetails->customDecoding($userArr[0]['Email'])).'">&nbsp;'. htmlentities( $this->myclientdetails->customDecoding($userArr[0]['Email'])).'</div>
			  			</div>
			  			<div  class="userdtlrow" >
				  			<div class="userdtlrow105">Gender</div>
				  			<div class="userdtlrow150 oneline">&nbsp;'.htmlentities(  $this->myclientdetails->customDecoding($userArr[0]['Gender'])).'</div>
				  		</div>';
				  		if($userArr[0]['Birthdate']!='0000-00-00'){
			  			$ret.='<div class="userdtlrow" >
				  			<div class="userdtlrow105">Birthdate</div>
				  			<div class="userdtlrow150 oneline">&nbsp;'. $birthDate .'</div>
				  		</div>';
				  		}	
				  	$ret.='</div>
				  	<div class="popColUserDetails">
				  		<div class="userdtlrow">
				  			<div class="userdtlrow105">Registration</div>
				  			<div class="userdtlrow150 oneline">&nbsp;'.  date('d-M-Y',strtotime($userArr[0]['RegistrationDate'])).'</div>
				  		</div>	
				  		<div class="userdtlrow">
				  			<div class="userdtlrow105">Last Login</div>
				  			<div class="userdtlrow150 oneline">&nbsp;'.  $logindate.'</div>
				  		</div>	
				  		<div class="userdtlrow adduserGroup"><a class="btn viewFull" href="'.BASE_URL.'/admin/user/userdetails/uid/'.$useridtype.'"><i class="fa-eye-open"></i> Full details</a> '.$common->addtogroupbutton(1).' </div>
			  		</div>
		  		</div>
		  	</div>
				  		
		  <div id="container" style="margin: 0 auto;"></div>
		 
	    </div><input type="hidden" id="userdetails" value="yes"><input type="hidden" id="singleaddgrp" value="'.$userArr[0]['UserID'].'" checked="checked" >';

	    echo $ret.'~'.$cdatajson.'~dbee~'.$legends;
        exit;
    } 

	public function facebookconnect()
    {

        $params = array(
            'appId' => facebookAppid,
            'secret' => facebookSecret,
            'domain' => facebookDomain
        );
        $facebook = new Facebook($params);
        if(isset($_GET['code']))
        {
            $user = $facebook->getUser();
            if ($user)
            {
                $logoutUrl = $facebook->getLogoutUrl();
                try
                {
                    $userdata = $facebook->api('/me');
                    $access_token_title = 'fb_'.facebookAppid.'_access_token';
                    $access_token = $_SESSION[$access_token_title];

					$dataArray['access_token'] = $access_token;
                    $dataArray['facebookid'] = $userdata['id'];
                    $dataArray['facebookname'] = $userdata['name'];
                    // get facebook page data
                    $user_personal_info['facebook_connect_data'] = Zend_Json::encode($dataArray);
					$this->myclientdetails->updatedata_global('users',$user_personal_info,'id',$this->session['userid']);
					$this->rewritesession();
					$this->_redirect(BASE_URL.'/admin/user/invitesocial?invite=facebook&type=socialinvite');                   

                }
                catch (FacebookApiException $e) {
                    error_log($e);
                    $user = null;
                }
            }
        }
        return $facebook->getLoginUrl(array(
         'scope' => 'user_friends,user_about_me,photo_upload,user_activities,user_birthday,user_likes,user_photos,user_status,user_videos,email,read_friendlists,read_insights,read_mailbox,read_requests,read_stream,offline_access,publish_checkins,publish_stream,manage_pages'
        ));
    }
    
	public function invitesocialAction()
	{

       	$facebook_connect_data = Zend_Json::decode($this->session_data['facebook_connect_data']);
       	$twitter_connect_data = Zend_Json::decode($this->session_data['twitter_connect_data']);
       	$linkedin_connect_data = Zend_Json::decode($this->session_data['linkedin_connect_data']);
		$facebook = $facebook_connect_data['access_token'];
		$twitter = $twitter_connect_data['twitter_access_token'];
		$linkedin = $linkedin_connect_data['oauth_verifier'];

		if($linkedin) // check linkedin logined or not
			$this->view->linkedinLogined = true;
		else
			$this->view->linkedinLogined = false;

		if($twitter)  // check twitter logined or not
			$this->view->twitterLogined = true;
		else
			$this->view->twitterLogined = false;

		if($facebook)  // check twitter logined or not
			$this->view->facebookLogined = true;
		else
			$this->view->facebookLogined = false;
		
		$this->view->invite = $this->_request->getParam('invite');
		$this->view->type = $this->_request->getParam('type');
		$this->view->facebookurl = $this->facebookconnect();
	}
	
	public function promotedexpertAction()
	{

	}

	public function userlistAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		if($this->getRequest()->isXmlHttpRequest())
		{
			$type = $this->_request->getPost('type');
			if($type=='promoted')
				$where = array('promoted'=>1);
			else
				$where = array('twitterTag'=>1);
			$userlist = $this->myclientdetails->getAllMasterfromtable('tblUsers',array('*'),$where);
			$list = $this->view->partial('partials/userlist.phtml', array('userlist'=>$userlist,'myclientdetails'=>$this->myclientdetails,'type'=>$type));
			$this->jsonResponse('success', 200, $list);  
		}
	}

	protected function jsonResponse($status, $code, $html, $message = null)
    {
        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            -> setHttpResponseCode($code)
            ->setBody(Zend_Json::encode(array("status"=>$status, "html"=>$html, "message"=>$message)))
            ->sendResponse();
            exit;
    }
    /**
     * Index
     */

    public function indexAction()
    {

		$namespace = new Zend_Session_Namespace();  
		
		unset( $_SESSION['Default']['searchfield']);
		$this->view->request = $request = $this->getRequest()->getParams();
		//print_r($request);
		//if( ( isset($request['searchfield']) && $request['searchfield']!='') || ( $_SESSION['Default']['searchfield']!='')) 
		if( ( isset($request['searchfield']) && $request['searchfield']!='') ) 
		{
			// $seachfieldChk = ($request['searchfield'] !='' ? $request['searchfield'] : $_SESSION['Default']['searchfield']);
			$seachfieldChk = ($request['searchfield'] !='' ? $request['searchfield'] : '');
			$namespace->searchfield 	= 	 $seachfieldChk;
			$this->view->seachfield	=	 $seachfieldChk; 

		}
		else { $seachfieldChk = '';  }

		
		// if( ( isset($request['statussearch']) && $request['statussearch']!='') || ( $_SESSION['Default']['statussearch']!='')) 
		if( ( isset($request['statussearch']) && $request['statussearch']!='') || ( $_SESSION['Default']['statussearch']!='')) 
		{
			// $statussearchChk = ($request['statussearch'] !='' ? $request['statussearch'] : $_SESSION['Default']['statussearch']);
			$statussearchChk = ($request['statussearch'] !='' ? $request['statussearch'] : '');
			$namespace->statussearch 	= 	 $statussearchChk;
			$this->view->statussearch	=	 $statussearchChk; 

		}
		else { $statussearchChk = '';  }
		
		//echo $request['searchfield'];exit;
		//if( (isset($request['orderfield']) && $request['orderfield']!='') || ( $_SESSION['Default']['orderfield']!='')) 
		if( (isset($request['orderfield']) && $request['orderfield']!='') ) 
		{
			//$orderfieldChk = ($request['orderfield'] !='' ? $request['orderfield'] : $_SESSION['Default']['orderfield']);
			$orderfieldChk = ($request['orderfield'] !='' ? $request['orderfield'] : '');
			$namespace->orderfield 	= 	$orderfieldChk;
			$this->view->orderfield	=	$orderfieldChk; 
		}
		else { $orderfield = ''; }
	
		
		$u= new Admin_Model_User();
		$seachkey = $this->myclientdetails->customEncoding($seachfieldChk,'usersearch');
		//$userData = $u->getUsers($seachkey,'',$orderfieldChk);
		$userData = $u->getUsers('','',$orderfieldChk,$seachkey,$statussearchChk);
		
		$this->view->totUsers	=	 count($userData);
		$this->view->forcsvrec	=	 $userData->toarray();


		$page = $this->_getParam('page',1);
		/*
		 * Object of Zend_Paginator
		 */
		$paginator = Zend_Paginator::factory($userData);
		/*
		 * Set the number of counts in a page
		 */
		$paginator->setItemCountPerPage(25);
		/*
		 * Set the current page number
		 */
		$paginator->setCurrentPageNumber($page);
		/*
		 * Assign to view
		 */
		$this->view->paginator = $paginator;
		
    }
	
	public function adduserAction()
    {
        // @todo Add the menu page action
		$request = $this->getRequest()->getParams();
		$u= new Admin_Model_User();
		if(isset($request['contactinfo']) && $request['contactinfo']='submit'){
		   $this->view->beforesubmit = $request['contactinfo'];
		 $password = getrandmax();  
		 $dataval = array('Name'=> $request['Name'],'Email'=> $request['Email'],
		                  'Pass'=> $password,'RegistrationDate'=> date("Y-m-d H:i:s"));
		 $Insertdataadmin	=	$u->insertdata($dataval);
		 if(isset($Insertdataadmin) && $Insertdataadmin='1' ){
				/*$to = $request['Email'];
				$subject = "Test mail";
				$body = "Hello! This is a simple email message.";
				$from = "someonelse@example.com";
				$this->sendWithoutSmtpMail($to,$subject,$from,$body);
				echo "User added successfully and a confermation mail sent.";*/

        $to = $request['Email'];
		$subject = "Test mail";
		$body = "Hello! This is a simple email message.";
		$from = "someonelse@example.com";
		$footerContentmsg = "powered by db corporate social platforms";			
		$dbeeEmailtemplate = new RawEmailtemplate();
        $emailTemplatemain = $dbeeEmailtemplate->dbeeEmailtemplate();     

        $emailTemplatejson = $deshboard->getfieldsfromtable(array('id','emailtemplatejson','htmllayout'),
        	                 'adminemailtemplates','id',1);
		$bodyContentjson = $emailTemplatejson[0]['emailtemplatejson'];
		$bodyContentjsonval = json_decode($bodyContentjson, true);
        $data1 = array('[%bodycontentbacgroColor%]','[%bodycontenttxture%]','[%headerbacgroColor%]',
             '[%headertxture%]','[%bannerfreshimg%]','[%contentbodyfontColor%]','[%contentbodybacgroColor%]',
             '[%contentbodytxture%]','[%%body%%]','[%footerfontColor%]','[%footerbacgroColor%]',
             '[%footertxture%]','[%footerfontColor%]','[%%footertext%%]'
             );
        $data2 = array($bodyContentjsonval['bodycontentbacgroColor'], $bodyContentjsonval['bodycontenttxture'],
             $bodyContentjsonval['headerbacgroColor'],$bodyContentjsonval['headertxture'],
             $bodyContentjsonval['bannerfreshimg'],$bodyContentjsonval['contentbodyfontColor'],
             $bodyContentjsonval['contentbodybacgroColor'],$bodyContentjsonval['contentbodytxture'],
             $body,$bodyContentjsonval['footerfontColor'],$bodyContentjsonval['footerbacgroColor'],
             $bodyContentjsonval['footertxture'],$bodyContentjsonval['footerfontColor'],$footerContentmsg);
        $messagemail = str_replace($data1,$data2,$emailTemplatemain);
        
		$this->myclientdetails->sendWithoutSmtpMailsendWithoutSmtpMail($to,$subject,$from,$messagemail);
		echo "User added successfully and a confermation mail sent.";	 
		 }
		}
    }
	/**
     * Menu
     */
    public function menuAction()
    {
        // @todo Add the menu page action
    }
	
	public function invitefriendAction()
    {
		$request = $this->getRequest()->getParams();
		$u= new Admin_Model_User();
		$login = $request['Email'];
        $password =  $request['Password'];
		$resultarray = $u->getcontacts($login,$password);

    }
	

	
	public function inviteAction()
    {
		
    }
    
    
    public function callingcommoncommentajaxAction()
    {
    	$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	
    	$request    =   $this->getRequest()->getParams();
    	$deshboard  =   new Admin_Model_Deshboard();
    	$userModal	=	new Admin_Model_User();
    
    	$common		=   new Admin_Model_Common();
    	$userRec	=	$userModal->getUsers($request['uid'],'userdetails');
    	$userArr	=	$userRec->toarray();
    
    	$latestCommentData = $deshboard->callingcommoncomment($request['uid'],'comment');

    	$result ="<ul class='listing'>";
    	 if (count($latestCommentData)){ 
    	 foreach($latestCommentData as $latestComment) : 
    	$result .="<li>";
    	
    	 if($liveDbee['type'] ==1) {
    	 	$dbtype			=	'text db';
    	 	$descDisplay	=	'<div class="font12" style="float:left; width:350px;">'.substr($liveDbee['description'],0,100).'</div>';
    	 }
    	 if($liveDbee['type'] ==2) {
    	 	$dbtype			=	'link db';
    	 	$dbLink			=	$liveDbee->Link;
    	 	$dbLinkTitle	=	$liveDbee->LinkTitle;
    	 	$dbLinkDesc		=	$liveDbee->LinkDesc;
    	 	$dbUserLinkDesc	=	!empty($liveDbee['UserLinkDesc']) ? $liveDbee['UserLinkDesc'] : $liveDbee['LinkTitle'];
    	 
    	 	$descDisplay	=	'<div>
    	 	<div class="font12">'.$dbUserLinkDesc.' -
    	 	<a target="_blank" href="'.$dbLink.'">'.$dbLink.'</a>
    	 	</div>
    	 	</div>';
    	 }
    	 if($liveDbee['type'] ==3) {
    	 	$dbtype	=	'pix db';
    	 
    	 	$dbPic		=	$liveDbee['Pic'];
    	 	$dbPicDesc	=	$liveDbee['PicDesc'];
    	 	$checkdbpic = $this->defaultimagecheck->checkImgExist($dbPic,'imageposts','default-avatar.jpg');
    	 	$descDisplay	=	'<div style="float:left; width:360px; ">
    	 	<div style="float:left;  width:auto; padding:3px; border:1px solid #CCCCCC;"><a href="#"><img src="'.IMGPATH.'/imageposts/small/'.$checkdbpic.'" width="90" border="0" /></a></div>
    	 	<div class="font12" style="float:left; margin-left:10px; width:245px;">'.substr($dbPicDesc,0,100).'</div></div>';
    	 }
    	 if($liveDbee['type'] ==4) {
    	 	$dbtype	=	'media db';
    	 
    	 	$dbtype			=	'link db<div class="icon-db-link"></div>';
    	 	$dbVid			=	$liveDbee['Vid'];
    	 	$dbVidDesc		=	$liveDbee['VidDesc'];
    	 	$dbLinkDesc		=	$liveDbee['LinkDesc'];
    	 	$dbUserLinkDesc	=	!empty($liveDbee['UserLinkDesc']) ? $liveDbee['UserLinkDesc'] : $liveDbee['LinkTitle'];
    	 
    	 	$descDisplay	=	'<div style="float:left; width:360px; ">
    	 	<div style="float:left;  width:auto; padding:3px; border:1px solid #CCCCCC;"><img width="90" height="90" border="0" src="'.$dbVid.'"></div>
    	 	<div class="font12" style="float:left; width:245px;">'.substr($dbVidDesc,0,100).'</div></div>';
    	 }
    	 if($liveDbee['type']  ==5) {
    	 	$dbPollText			=	$liveDbee->PollText;
    	 	$dbtype	=	'polls';
    	 	$descDisplay	=	'<div class="font12" style="float:left; width:350px;">'.substr($dbPollText,0,100).'</div>';
    	 }
    	 
    	
    	$descDisplay	=	'<div style="float:left; width:360px; ">
    	<div style="float:left;  width:auto; padding:3px; border:1px solid #CCCCCC;"><img width="90" height="90" border="0" src="'.$dbVid.'"></div>
    	<div class="font12" style="float:left; width:245px;">'.substr($latestComment['Comment'],0,100).'</div></div>';

    	
    	$latestCommentpic = $this->defaultimagecheck->checkImgExist($latestComment['image'],'userpics','default-avatar.jpg');
    	$date =date('d M y',strtotime($latestComment['CommentDate']));
    	
    	$result .='<div style="width:450px;float:left;"  >
    	<div style="width:70px;float:left;padding:2	px;" class="box_image">
    	<div style="float:left">
    	
    	<img src="'.IMGPATH.'/users/medium/'. $latestCommentpic.'" width="70" height="70" border="0" />
    	</div>
    	</div>
    	<div style="width:350px;float:left; margin-left:10px;">
    	<div class="" style="width:350px;font-weight:bold"><a href="#">'.$latestComment->username.'</a>
    	<span> has commented on  </span>'.$latestComment->Ownername.'&rsquo;s - db'. $dbtype .'
    	</div>
    	<div class="details-gift">';
    	
    	$result .='<div>'. $descDisplay.'
    	</div>
    	<div class="clear"></div>
    	<div> commented on '.$date.' </div>
    	</div>
    	</div>
    	</li>';
    	
    	endforeach;
    	} else { 
    	$result .='no posts found</p>';
    	 }
    	$result .='</ul>';
    
    
    	echo $result;
    	
    	return;
    }
    
        public function callingcommonscoreajaxAction()
	    {
	    	$this->_helper->layout->disableLayout();
	    	$this->_helper->viewRenderer->setNoRender(true);
	    	
	    	$request    =   $this->getRequest()->getParams();
	    	$deshboard  =   new Admin_Model_Deshboard();
	    	$userModal	=	new Admin_Model_User();
	    
	    	$common		=   new Admin_Model_Common();
	    	$userRec	=	$userModal->getUsers($request['uid'],'userdetails');
	    	$userArr	=	$userRec->toarray();
	    
	    	$latestscoreData = $deshboard->getLiveScore($limit='',$dbeeID='',$request['uid'],$type='');

	    	$result ='<ul class="listing">';
				    if(count($latestscoreData)){
					    foreach($latestscoreData as $liveScore) :
							    if($liveScore->type==1)
							    {
							   		 $dbtype	=	'db';
							    } else {
							   		 $dbtype	=	'comment';
							    }
							    if($liveScore->Score == 1){
							  		  $scorediv	=	'<div  style="float:right;margin-right: 30px; " id="love-dbee"></div>';
							    } else if($liveScore->Score == 2){
							    		$scorediv	=	'<div  style="float:right;margin-right: 30px; " id="like-dbee"></div>';
							    } else if($liveScore->Score == 3){
							    		$scorediv	=	'<div  style="float:right;margin-right: 30px; " id="philosopher-dbee"></div>';
							    } else if($liveScore->Score == 4){
							   			 $scorediv	=	'<div  style="float:right;margin-right: 30px; " id="dislike-dbee"></div>';
							    } else if($liveScore->Score == 5){
							    		$scorediv	=	'<div  style="float:right;margin-right: 30px;" id="hate-dbee"></div>';
							    }
							    $liveScoremdbpic = $defaultimagecheck->checkImgExist($liveScore->image,'userpics','default-avatar.jpg');
							   
							    $result .='<li>
							    <div style="width:450px;float:left;"  >
							    <div style="width:70px;float:left;padding:2	px;" class="box_image">
							    <div style="float:left">							    
							    <img src="'.IMGPATH.'/users/small/'. $liveScoremdbpic .'" width="70" height="70" border="0" />
							    </div>
							    </div>
							    <div style="width:370px;float:left; margin-left:10px;">
							    <div class="" style="font-weight:bold"><a href="#">'.$liveScore->username.'</a>
							    <span>scored on  </span>&nbsp;<a href="#">'.$liveScore->Ownername.'&rsquo;s</a> - '.$dbtype.'  '.$scorediv.'</div><div class="details-gift">
							    <div>
							    </div>
							    <div class="clear"></div>
							    <div><b></b></div>
							    </div>
							    </div>
							    
							    </li>';
					     endforeach; 
				 } else {  
			    $result .="<p>Score Not Found!</p>";
			    }  
			   $result .=' </ul>';
			   echo $result;
			   
			   return;
		}

}
