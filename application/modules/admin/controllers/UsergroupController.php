<?php

class Admin_UsergroupController extends IsadminController
{

	private $options;
	
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
        parent::init();
    }

	public function addgroupAction()
    {
        $u= new Admin_Model_Usergroup();		
	    $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
    	$data = array();
    	$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$request = $this->getRequest()->getParams();		
		$this->_helper->layout()->disableLayout();
		if ($this->getRequest()->getMethod() == 'POST') {
			$filter           = new Zend_Filter_StripTags();
			$data['clientID']= clientID;
			$data['ugname']   = $request['groupname'] ? $filter->filter(stripslashes($request['groupname'])) : '0';
			$data['ugdis']   = $request['discription'] ? $filter->filter(stripslashes($request['discription'])) : '0';
			$data['ugcat']   = $request['ugcat'] ? $filter->filter(stripslashes($request['ugcat'])) : '0';		
		
			$data['ugcreated']= date('Y-m-d H:i:s');
			$insertgrouptypes = $u->insertdata_group($data);
			
			$data1['content']=$insertgrouptypes;
			return $response->setBody(Zend_Json::encode($data1));
			
		}
    }
    public function usersgroupstoreAction()
    {   
    	 	
    	$u= new Admin_Model_Usergroup(); 
        $reporting = new Admin_Model_reporting();
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
    	$data = array();
    	$this->_helper->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	$request = $this->getRequest()->getParams();    	
    	$this->_helper->layout()->disableLayout();
    	$provider = $request['provider'];
        if ($this->getRequest()->getMethod() == 'POST') 
        {
	            $groupid      =   (int)$request['groupid'] ? $request['groupid'] : '0';          
                $category      =   $request['cat'] ? $request['cat'] : 'browser';               
                $userarray    =   $request['uid'] ? $request['uid'] : '0';
                if(trim($request['chktypew'])!='false'){	
                	$data1['debug']=$category;
                	
                    switch ($category) 
                    {
                      case 'browers':
                          $totdata  = $reporting->getBrowserUsers($provider,'usersgroup','nolimit','');  
                          break;
                      case 'countrycontainer':
                          $totdata  = $reporting->getcontinentusers($this->myclientdetails->customEncoding($provider),'usersgroup','nolimit',''); 
                          break;
                      case 'categoryusers':
                          $totdata  = $reporting->getcategoryinterestusers($request['category'],$request['cattype'],'nolimit','');  
                          break;
                      case 'emailproviders':
                          $totdata  = $reporting->getReportUsers($request['continentcode'],$request['continentcode'],'usersgroup','nolimit','');  
                          break;
                      case 'osusers':
                          $totdata  = $reporting->getOsUsers($request['continentcode'],'os','nolimit','');
                          break;
                      case 'post':
                          $totdata = $reporting->getAllPostHashTagUser($request['tag']);
                          break;
                      case 'AllUserTag':
                          $totdata = $reporting->getAllHashTagUser($request['tag']);
                          break;
                      case 'comment':
                          $totdata = $reporting->getAllCommentHashTagUser($request['tag']);
                          break;
                      case 'totalsocialusers':
                          $totdata = $reporting->getSocialUsers($request['continentcode'],'pie','nolimit');
                          break;
                      case 'postvisiters':
                          $totdata = $reporting->getPostVisiters( $request['provider'],'','nolimit','');
                          break;
                      case 'scoringandleagues':
                          $totdata = $reporting->getPostVisiters( $request['provider'],'','nolimit','');
                          break;
                      case 'eachdaylogins':
                          $totdata = $reporting->usersTracking( $request['provider'],'','nolimit','');
                          break;  
                    }
	    			foreach ($totdata as $row) { 	                	
	                	if($row['UserID']!='')
                        {                           
		                    $data['ugid']     = $groupid;
		                    $data['userid']   = $row['UserID']; 
		                    $data['clientID']= clientID;
		                    $u->insertdata_addusergroup($data);
	                	 }                     
	                } 
	            }
	            else 
	            {
	    	       $userarray    =   $request['uid'] ? $request['uid'] : '0'; 
	        		$cnt = count($userarray);
	        		
	        		for($i=0;$i<$cnt;$i++)
	                {	                	  	 
	        		      $data['ugid']     = $groupid;
	            		  $data['userid']   = $userarray[$i]; 
	            		  $data['clientID']= clientID;
	        		     $u->insertdata_addusergroup($data);     
	        		}
	        		
	        		$data1['content']="Group inserted successfully";
	            }
	    		
	            return $response->setBody(Zend_Json::encode($data1));
	    }
	    
    }
    
    public function usersgroupquickAction()
    {
    	$response = $this->getResponse();
    	$response->setHeader('Content-type', 'application/json', true);
    	$data = array();
    	$this->_helper->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	$request = $this->getRequest()->getParams();
    	$this->_helper->layout()->disableLayout();
    	
    	if ($this->getRequest()->getMethod() == 'POST')
    	{
    		$u= new Admin_Model_Usergroup();
    		$dashboard= new Admin_Model_Deshboard();
    		$reporting = new Admin_Model_reporting();
    		$groupid      =   (int)$request['groupid'] ? $request['groupid'] : '0';
    		
    		$chk      =   $request['chk'] ? $request['chk'] : '';
    		$userarray    =   (int)$request['uid'] ? $request['uid'] : '0';
    		
    		//echo $chk;

	    	if($chk=='true'){
	    			$cat      =   $request['cat'] ? $request['cat'] : '';
	    			$gender      =   $request['gender'] ? $request['gender'] : '';
	    			$age1      =   $request['age1'] ? $request['age1'] : '';
	    			$age2      =   $request['age2'] ? $request['age2'] : '';
	    			$start = '';
	    			$offset = '';
	    				$totdata = $dashboard->searchusr($cat,$gender,$age1,$age2,'',$offset,'0');
	    			
	    		
	    			foreach ($totdata as $row) {
	    				if($row['UserID']!=''){
	    					$data['ugid']     = $groupid;
	    					$data['userid']   = $row['UserID'];
	    					$data['clientID']= clientID;
	    					$u->insertdata_addusergroup($data);
	    				}
	    			}
	    
	    			$data1['success']=$chk;
	    	}else
    		{
    
    			$userarray    =   (int)$request['uid'] ? $request['uid'] : '0';
    
    			$cnt = count($userarray);
    
    			for($i=0;$i<$cnt;$i++)
    			{
    			$data['ugid']     = $groupid;
    			$data['userid']   = $userarray[$i];
    			$data['clientID']= clientID;
    			$u->insertdata_addusergroup($data);
    		}
    
    		$data1['content']="Group inserted successfully";
    	}
    
    			return $response->setBody(Zend_Json::encode($data1));
    
    
    }
    
    }
    
    public function listgroupAction()
    {    	
    	
    	$u= new Admin_Model_Usergroup();    	
    		$data = array();
		$content ='';
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true); 
    	
    	$usergrouplist = $u->list_group();
    	$content.= '<div class="group_option">Select Option</div><input type="radio" value="byquery" name="groupoption">Save all user<input type="radio" value="bychk" name="groupoption">Save check user';
    	
    	$content.= '<div class="group_txt">Select group from list</div>';
    	
    	foreach($usergrouplist as $row):
    		$content.='<label style="float:left"><input type="radio" name="grname" value="'.$row['ugid'].'"><div class="checkbox" /></div></label><div style="float:left">'.$row['ugname'].'</div>';
    	endforeach;
    	$content.= '<button type="button" class="btn btn-green" id="addgroupBtn"> <i class="fa fa-plus fa-lg"></i>  Save group </button>';
    	$data['content']=$content;
    	
    	return $response->setBody(Zend_Json::encode($data));
    	
    }
    
    public function listingAction()
    {    
    	$u= new Admin_Model_Usergroup();  
        $dashboard= new Admin_Model_Deshboard();   	
    	$response = $this->getResponse(); 	
    	$this->view->data	=	 $u->list_group();
        $this->view->userdetails   = $dashboard->filteruserdetails(); 
           
    	return $response;    
    }
    
    public function usergroupdetailAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $filter = new Zend_Filter_StripTags();
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        {
        $u= new Admin_Model_Usergroup();		
        $this->_helper->layout()->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(true);
		$request = $this->getRequest()->getParams();			
	    $filter = new Zend_Filter_StripTags();
	    $count=20;
		$groupid = $filter->filter(stripslashes($this->_request->getPost('id')));
		$offset = (int)$request['offset'];
		$offset = $offset?$offset:'0';
		$start = $count+$offset;
		$groudetail = $u->groupdetailbyid($groupid,$count,$offset);
		$groudetailtotaltotal = $u->groupdetailbyidtotla($groupid);
		
		$this->view->groupname = $groudetail[0]['ugname'];


        $usrtext = ($groudetailtotaltotal==1)?' user':' users';
		
		$groudetailtotal = ' <strong>(<b total="'.$groudetailtotaltotal.'">'.$groudetailtotaltotal.$usrtext.'</b>)</strong>';
		
		$addGroupUsers = '<div class="linkuseraddgrp" groupid="'.$groupid.'"><a href="#" groupid="'.$groupid.'" class="pull-right btn btn-green"><i class="fa fa-user"> </i> Add users to set</a></div>';
		
		$retresult	=	'';
        $retresult .= ($groudetail[0]['ugdis']!='') ? '<div class="userGrpDescription">'.$groudetail[0]['ugdis'].'</div>' : '<div class="userGrpDescriptionNoDes">no description</div>';
		$offset1 = $offset+20;
		if(count($groudetail)>0)
		{

			foreach ($groudetail as $key => $value) {
				if($value['Name']!=''){
				$retresult .='<li id="userslist_'.$value['userid'].'" userid="'.$value['userid'].'" gname="'.$value['ugid'].'">
							    <div class="dgrpUrsName"><i class="kcSprite grpuserIcon"></i>'.$this->myclientdetails->customDecoding($value['Name']).' '.$this->myclientdetails->customDecoding($value['lname']).'</div>
							    <div class="dgrpUrsEmail">'.$this->myclientdetails->customDecoding($value['Email']).'</div>';
					$retresult .='<div class="btnRtlist"><a class="btn btn-green btn-mini show_details_user" userid="'.$value['userid'].'" href="javascript:void(0);" atype="view">View</a><span class="sprt"> </span><a class="btn btn-danger btn-mini" href="javascript:void(0);" userid="'.$value['userid'].'" gname="'.$value['ugid'].'" atype="delete">Delete</a></div>';
                    $retresult .='</li>';
				}
			}
		
			if($groudetailtotaltotal > $offset1){
			     $retresult .='<div style="text-align:center;font-weight:bold;padding-top:10px;padding-bottom:10px;"><a id="viewmoregroupuser" offset="'.$offset1.'" groupid="'.$groupid.'" action="usergroup/usergroupdetail" >View More </a></div>';
			}
		}else{
				 $retresult .='<div class="dashBlockEmpty">no users in this set</div>';
			 }
			 

             if($request['total']==true){
					/*echo $retresult.'~'.$groudetailtotal.'~'.$addGroupUsers;*/
                $data['status'] = 'success';
                $data['content'] = $retresult;
                $data['groudetailtotal'] = $groudetailtotal;
                $data['addGroupUsers'] = $addGroupUsers;
			 }else{
			 	$data['status'] = 'success';
                $data['content'] = $retresult;
			 }
		   
        }
        else
        {
            $data['status'] = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    
    
    public function deleteuserAction()
    {
    	$this->_helper->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	$request = $this->getRequest()->getParams();
    	$filter = new Zend_Filter_StripTags();
    	$groupid = $filter->filter(stripslashes($request['groupid']));
    	$userid = $filter->filter(stripslashes($request['userid']));
    	
    	$u= new Admin_Model_Usergroup();
    	$response = $u->deleteusergroup($groupid,$userid);
    	 
    	return $response;
    	
    }

   

    public function deletegroupAction()
    {
    	$this->_helper->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	$request = $this->getRequest()->getParams();
    	$filter = new Zend_Filter_StripTags();
    	$groupid = $filter->filter(stripslashes($request['groupid']));    
    	$u= new Admin_Model_Usergroup();    	
    	$response = $u->deletegroup($groupid);    	
    	return $response;
    	exit;
    }
   
    
    public function dbeeuserAction()
    {
    	$data = array();
    	$this->_helper->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	$filter = new Zend_Filter_StripTags();
    	$response = $this->getResponse();
    	$response->setHeader('Content-type', 'application/json', true);
    	if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
    	{
    		$keyword = $this->_request->getPost('keyword');
            $calling = $this->_request->getPost('from');
            $type = $this->_request->getPost('type');
            $common = new Admin_Model_Common();
            if($calling =='Roleani')
                $type='Roleani';
            $keyword = $this->myclientdetails->customEncoding($keyword,'vipgroup');
            if($calling=='vipgroup')
            {
  
                $u= new Admin_Model_User();
                $result = $u->getUsers('','vipuser','',$keyword);

                if(count($result)>0){ 
                $content = '';  
                foreach ($result as $value)
                {
                    $extratext='' ;
                    if($value['usertype']==100)
                    {
                        $extratext='<span style="color:#bd362f; font-weight:bold; margin-top: 5px; display: inline-block;">(VIP)</span>' ;
                    }
                    if($value['usertype']==100 && $value['hideuser']==1)
                    {
                        $extratext='<span style="color:#bd362f; font-weight:bold; margin-top: 5px; display: inline-block;">(Anonymous VIP)</span>' ;
                    }
                    $valuepic = $common->checkImgExist($value['ProfilePic'],'userpics','default-avatar.jpg');
                    $content .= "<div class='userFatchList boxFlowers' title='".$this->myclientdetails->customDecoding($value['Name'])."' socialFriendlist='true'>
                    <label class='labelCheckbox'><input type='checkbox' value='".$value['UserID']."' class='inviteuser-search' name='groupuser[]'>
                    <div class='follower-box'>                    
                    <div class='usImg'><img class='img border' align='left' src='".IMGPATH."/users/small/".$valuepic."' border='0' /></div>
                    ".$this->myclientdetails->customDecoding($value['Name']).$this->myclientdetails->customDecoding($value['lname'])."<br><div class='oneline ' style='color:#fc9908'> @".$this->myclientdetails->customDecoding($value['Username'])."</div>".$extratext."
                    </div>
                    </label>
                    </div></div>";
                }
                }else{
                    $content .= "<div class='dashBlockEmpty' style='width:95%'>no user found</div>";
                }
            }
            else
            {
                $result =  $this->user_group->searchgroupuseruser($keyword,$type);

        		if(count($result)>0)
                {
                    $content .= '<div class="selectallset" name="allsetusr"><label title="Select all">
<input id="allsetusr" class="goupusermain" type="checkbox" value="allInResults" name="allsetusr">
<label for="allsetusr"></label> Select all </div><div class="totaltext">Total : '.count($result).'</div>';
            		foreach ($result as $value)
            		{
                        if(!empty($uservals)){
                        if (in_array($value['UserID'],$uservals) && $type=='twitter') 
                           {$checkvalue='1';}
                        else{$checkvalue='0';}
                        }else{
                            $checkvalue='0';
                        }

                        $extratext='' ;
                        if($value['usertype']==100)
                        {
                            $extratext='<span style="color:#bd362f; font-weight:bold; margin-top: 5px; display: inline-block;">(VIP)</span>' ;
                        }
                        if($value['usertype']==100 && $value['hideuser']==1)
                        {
                            $extratext='<span style="color:#bd362f; font-weight:bold; margin-top: 5px; display: inline-block;">(Anonymous VIP)</span>' ;
                        }

            			$valuepic = $common->checkImgExist($value['ProfilePic'],'userpics','default-avatar.jpg');
            			$content .= "<div class='userFatchList boxFlowers' title='".$this->myclientdetails->customDecoding($value['Name'])."' socialFriendlist='true'>
            			<label class='labelCheckbox'>
                        <input type='checkbox' value='".$value['UserID']."' checkvalue='".$checkvalue."' class='inviteuser-search' name='groupuser'>
            			<div class='follower-box'>                    
            			<div class='usImg'><img class='img border' align='left' src='".IMGPATH."/users/small/".$valuepic."' border='0' /></div>
            			".$this->myclientdetails->customDecoding($value['Name'])." ".$this->myclientdetails->customDecoding($value['lname'])."<br><div class='oneline ' style='color:#fc9908'> @".$this->myclientdetails->customDecoding($value['Username'])."</div>".$extratext."
            			</div>                
            			</label>
            			</div></div>";
            		}
        		}else{
        			$content .= "<div class='dashBlockEmpty' style='width:95%'>no user found</div>";
        		}
            }
    		$data['status'] = 'success';
    		$data['content'] = $content;
    		$data['post_title'] = $groupname;
    	}
    	else
    	{
    		$data['status'] = 'error';
    		$data['message'] = 'Some thing went wrong here please try again';
    	}
    	return $response->setBody(Zend_Json::encode($data));
    }

      public function updategroupsAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $request = $this->getRequest()->getParams();
        $filter = new Zend_Filter_StripTags();
        $groupid = $filter->filter(stripslashes($request['groupid']));
        $groupnm = $filter->filter(stripslashes($request['groupname']));
        $data = array('ugname'=>$groupnm);
        $u= new Admin_Model_Usergroup();
        $response = $u->updategroup($data,$groupid);        
        return $groupid;
        exit;
    }
    
    public function addgroupusergrpAction()
    {
    	$data = array();
    	$u= new Admin_Model_Usergroup();
    	$request = $this->getRequest()->getParams();
    	$this->_helper->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	$filter = new Zend_Filter_StripTags();
    	$response = $this->getResponse();
    	$response->setHeader('Content-type', 'application/json', true);

    	if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
    	{
			    $groupid    =   (int)$request['groupid'] ? $request['groupid'] : '0';
			    $userarray    =   (int)$request['userid'] ? $request['userid'] : '0';
			    
			    $userarray2 = explode(",", $userarray);
			    
			    $cnt = count($userarray2);
			    
			    for($i=0;$i<$cnt;$i++)
			    {
			    	//echo $userarray[$i];
			    	$data['ugid']     = $groupid;
			    	$data['userid']   = $userarray2[$i];
			    	$data['clientID']= clientID;
			    	$u->insertdata_addusergroup($data);
			    	//print_r($data);
			    }
			   
			    $data1['message']="Group user inserted successfully";
			    }
    
  		  return $response->setBody(Zend_Json::encode($data1));
    
    }
    
    public function addusersgrouptagAction()
    {
    	$data = array();
    	$usergroupobj= new Admin_Model_Usergroup();
    	$request = $this->getRequest()->getParams();
    	$this->_helper->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	$filter = new Zend_Filter_StripTags();
    	$response = $this->getResponse();
    	$response->setHeader('Content-type', 'application/json', true);
    	if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
    	{
    	    $groupid    =   (int)$this->_request->getPost('groupid');
    		$tag    =  $this->_request->getPost('tag');
    		$title    =  $this->_request->getPost('title');
    		$company    =  $this->_request->getPost('company');
    		
    		if(count($tag)>0){
    			foreach($tag as $value):    			
    			$this->addgrouptagus($usergroupobj->getuserbytag($value),$groupid);
    			endforeach;
    			
    		}
    		
    		if(count($title)>0){
    			foreach($title as $value):    			
    			$this->addgrouptagus($usergroupobj->getuserbytitle($value),$groupid);
    			endforeach;    			
    		}
    		
    		if(count($company)>0){
    			foreach($company as $value):
    			$this->addgrouptagus($usergroupobj->getuserbycompany($value),$groupid);
    			endforeach;    			
    		}
    
    		$data1['message']="Group user inserted successfully";
    	}
    
  		  return $response->setBody(Zend_Json::encode($data1));
    
    }
    
    public function addgrouptagus($userarray,$groupid)
    {
    	$cntu = count($userarray);    	
    	$usergroupobj= new Admin_Model_Usergroup();
    	for($i=0;$i<$cntu;$i++)
    	{    	
    		$data['ugid']     = $groupid;
    		$data['userid']   = $userarray[$i];
    		$data['clientID']= clientID;    		
    		$usergroupobj->insertdata_addusergroup($data);
    	
    	}
    	
    	return true;
    
    }
    
    public function getuserlistbytagAction()
    {
    	$data = array();
    	$usergroupobj= new Admin_Model_Usergroup();
    	$common = new Admin_Model_Common();
    	$request = $this->getRequest()->getParams();
    	$this->_helper->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	$filter = new Zend_Filter_StripTags();
    	$response = $this->getResponse();
    	$response->setHeader('Content-type', 'application/json', true);
    	if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
    	{
    		$groupid    =   (int)$this->_request->getPost('groupid');
    		$tag    =  $this->_request->getPost('tag');
    		$title    =  $this->_request->getPost('title');
    		$company    =  $this->_request->getPost('company');
    		
    		$content ="<ul>";
    		if(!empty($tag)){
    			$ulist = $usergroupobj->getuserbytaglist($tag);
    			
    			if(count($ulist)>0){
    				foreach($ulist as $row):
    				 $checkliveDbpic = $common->checkImgExist($row['ProfilePic'],'userpics','default-avatar.jpg'); 
    					$content.='<li class="listTagProfile">
							<div class="listUserPhoto show_details_user" userid="'.$row['UserID'].'">
								<a href="javascript:void(0)"><img src="'.IMGPATH.'/users/medium/'.$checkliveDbpic.'" width="90" height="90" border="0" class="recievedUsePic"/></a>
							</div>
							<div class="details_user">'.$this->myclientdetails->customDecoding($row['Name']).'</div>
							<div class="details_user"><a  href="javascript:void(0)" class="graycolor"> @'.$this->myclientdetails->customDecoding($row['Username']).'</a></div>
							
							</li>';
    				endforeach;
    			}
    		}
    
    		elseif(!empty($title)){
    			$title = str_replace("%23", '#', $title);
	    		$ulist = $usergroupobj->getuserbytitlelist($title);
	    		
	    			if(count($ulist)>0){
	    				foreach($ulist as $row):
	    					$checkliveDbpic = $common->checkImgExist($row['ProfilePic'],'userpics','default-avatar.jpg'); 
    					$content.='<li class="listTagProfile">
							<div class="listUserPhoto show_details_user" userid="'.$row['UserID'].'">
								<a  href="javascript:void(0)"><img src="'.IMGPATH.'/users/medium/'.$checkliveDbpic.'" width="90" height="90" border="0" class="recievedUsePic"/></a>
							</div>
							<div class="details_user">'.$this->myclientdetails->customDecoding($row['Name']).'</div>
							<div class="details_user"><a  href="javascript:void(0)" class="graycolor"> @'.$this->myclientdetails->customDecoding($row['Username']).'</a></div>
							
							</li>';
	    				endforeach;
	    			}
    		}
    
    		elseif(!empty($company)){
    			$company = str_replace("%23", '#', $company);
    			$ulist = $usergroupobj->getuserbycompanylist($company);
    			if(count($ulist)>0){
    				foreach($ulist as $row):
    					$checkliveDbpic = $common->checkImgExist($row['ProfilePic'],'userpics','default-avatar.jpg'); 
    					$content.='<li class="listTagProfile">
							<div class="listUserPhoto show_details_user" userid="'.$row['UserID'].'" >
								<a  href="javascript:void(0)"><img src="'.IMGPATH.'/users/medium/'.$checkliveDbpic.'" width="90" height="90" border="0" class="recievedUsePic"/></a>
							</div>
							<div class="details_user">'.$this->myclientdetails->customDecoding($row['Name']).'</div>
							<div class="details_user"><a href="javascript:void(0)" class="graycolor"> @'.$this->myclientdetails->customDecoding($row['Username']).'</a></div>
							
							</li>';
    				endforeach;
    			}
    		}
    		
    		$content.="</ul>";
    		$data['content']= $content;
    		$data['message']="Group user inserted successfully";
    	}
    
    	return $response->setBody(Zend_Json::encode($data));
    
    }

    public function getonlineuserlistAction()
    {
        $data = array();
        $usergroupobj= new Admin_Model_User();
        $common = new Admin_Model_Common();
        $request = $this->getRequest()->getParams();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $filter = new Zend_Filter_StripTags();
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        {            
            
            $content ="<ul>";            
                //$ulist = $usergroupobj->getUsers('','online','','',5);
                $ulist = $usergroupobj->getUsers('','','','',5);
                
                if(count($ulist)>0){
                    foreach($ulist as $row):
                        $checkliveDbpic = $common->checkImgExist($row['ProfilePic'],'userpics','default-avatar.jpg'); 
                    $content.='<li class="listTagProfile">
                        <div class="listUserPhoto show_details_user" userid="'.$row['UserID'].'">
                            <a  href="javascript:void(0)"><img src="'.IMGPATH.'/users/medium/'.$checkliveDbpic.'" width="90" height="90" border="0" class="recievedUsePic"/></a>
                        </div>
                        <div class="details_user">'.$this->myclientdetails->customDecoding($row['Name']).'</div>
                        <div class="details_user"><a  href="javascript:void(0)" class="graycolor"> @'.$this->myclientdetails->customDecoding($row['Username']).'</a></div>
                        
                        </li>';
                    endforeach;
                }
            
            
            $content.="</ul>";
            $data['content']= $content;
            $data['message']="fetced";
        }
    
        return $response->setBody(Zend_Json::encode($data));
    
    }
    
}

