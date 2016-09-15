<?php
class MyhomeController extends IsController
{
	protected $_userid = null;
	public function init()
	{
		//echo date_default_timezone_get(); die;
		parent::init();
		if(!$this->_userid)
		{
			$this->_helper->redirector->gotosimple('index','index',true);
		}
	}
	public function logmaintainAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$status = $this->_request->getPost('status');
			$responseText = $this->_request->getPost('responseText');
			if($status==200)
			{
				$this->sendWithoutSmtpMail('deshbandhu.dbee@gmail.com','Js error on '.$_SERVER['HTTP_HOST'],'no-reply@db-csp.com',$responseText);
				$log_f = $_SERVER['DOCUMENT_ROOT'].'/log/error_call.log';
		        $params_raw = $status;
		        $params_req = $responseText;
		        $this->generateLog($log_f,$params_raw,$params_req,null);
			}
		}
		else
        {
            $data['status'] = 'error';
            $data['message'] = 'Some thing went wrong here please try again';
        }
        return $response->setBody(Zend_Json::encode($data));
	}
	public function clickcommentAction()
	{
		
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$request = $this->_request->getParams();

			 $data = $this->commonmodel_obj->topdbeecomments($request['dbId'],$request['dbOwner'],$this->plateform_scoring,$this->adminpostscore,$request['groupId'],$request['eventId'],$request['userId']);
			
		}
		else
        {
            $data = 'Some thing went wrong here please try again';
        }
        echo $data;
	}

	public function searchtaguserAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		//if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'GET')
		//{
			$content='';
			$isvip = 0;
			
			$myhomesearch = new Application_Model_Myhome();
			$q = (String)strtolower($this->_getParam('q'));
			$user_type=$this->session_data['usertype'];	
			if($user_type==0 || $user_type==6){
				$isvip = 1;
			}
			$q = $this->myclientdetails->customEncoding($q);		
			
			$query = $myhomesearch->getfollowTagUser($q,$this->_userid,$isvip);

		    $checkImage = new Application_Model_Commonfunctionality();
		    
		   
		   
		   $numrows = count($query);
		   if($numrows > 0)
		   { 
			   for ($x = 0;  $x < $numrows; $x++) 
			   {	   
			   		$name = ($query[$x]["full_name"]!="") ? $this->myclientdetails->customDecoding($query[$x]["full_name"]) : $this->myclientdetails->customDecoding($query[$x]["Name"]);
				$picprofile1 = $checkImage->checkImgExist($query[$x]['ProfilePic'],'userpics','default-avatar.jpg');
						  
			   $friends[$x] = array(
			  "id" => $query[$x]["UserID"],"text" =>$name ,
			  "username" => ' @'.$this->myclientdetails->customDecoding($query[$x]["Username"]),
	          "url" =>  IMGPATH.'/users/small/'.$picprofile1.'"'
			  	);	

			   }
		  }
		  else
		  {
		  	$friends[] =''; 
		  } 
		// }				
		
        return $response->setBody(Zend_Json::encode($friends));
	}

	protected function generateLog($filename,$params_raw,$params_req,$er_chk)
    {
        if(file_exists($filename))
            $handle = fopen($filename, "a");
        else
            $handle = fopen($filename, "w");
        if(is_null($er_chk)){
            $str .= "\n\n================================================\n";
            $str = "[".date("Y-m-d h:i:s", mktime()) ."] \n\tRaw body - ".$params_raw."\n\tReponse - ".json_encode($params_req)."\n\t";
        }
        else{
            $str = "MetaData - ".json_encode($er_chk);
            $str .= "\n\n================================================\n";
        }
        fwrite($handle,$str);
        fclose($handle);
    } 
	public function createrandontokenAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
			if($_SERVER['REMOTE_ADDR']== REMOTE_IP){
				session_start();
		 		$request 		=	$this->getRequest()->getPost();
		 		$User_tokens 	= 	new Zend_Session_Namespace('User_tokens');
		 		$respo 			=	array();

		 		if (empty($request['captchakey'] )) {
	            	$respo['errormsg']= "Please use valid source in order to post data!";
	        	} else {
	        		
	        		$respo['SessUser__']= trim(md5(mt_rand(9, 400)));
	        		$respo['SessId__']	= trim(md5(time()));
	        		$respo['SessName__']= trim(md5(mt_rand(9, 400)));
	        		$respo['Token__']	= trim(md5(time()));
	        		$respo['Key__']		= trim(md5(time()));

	        		$respo['errormsg']= "404";

	        		$User_tokens->startupkey  =  $respo['SessUser__'].md5('adam').$respo['SessId__'].md5('guy').$respo['SessName__'];
	        	}
		 	} else {
		 		$respo['errormsg']= "Please use valid source in order to post data!";
		 	}

	 		//print_r($User_tokens->startupkey); exit;
	 		return $response->setBody(Zend_Json::encode($respo));
			
		}
		exit;
	}

	public function imgunlinkAction()
	{                        
		$request = $this->getRequest()->getParams();
		//unlink('./imageposts/'.trim($request['serverFileName_']));
		unlink(ABSIMGPATH."/imageposts/".trim($request['serverFileName_']));
		unlink(ABSIMGPATH."/imageposts/medium/".trim($request['serverFileName_']));
		unlink(ABSIMGPATH."/imageposts/small/".trim($request['serverFileName_']));
		exit;
	}
	public function imguplodAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
	 		$request 		=	$this->getRequest()->getPost();
	 		
			$storeFolder 	= ABSIMGPATH."/imageposts/";   
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
				$filename 		= ABSIMGPATH."/imageposts/medium/".$picture;
				$filename1 		= ABSIMGPATH."/imageposts/small/".$picture;
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
			   		list($width,$height)=getimagesize(IMGPATH."/imageposts/".$picture);
			   	if($width < 484)
		   		{		   			
		   			$medium=copy($storeFolder.$picture, $filename);

		   			if($width < 200)
		   			{		   			  
		   			  $medium=copy($storeFolder.$picture, $filename1);	
		   			}
		   			else
		   			{
		   				$newwidth1=200;
						$newheight1=($height/$width)*$newwidth1;
						$tmp1=imagecreatetruecolor($newwidth1,$newheight1);
						$red = imagecolorallocate($tmp1, 255, 0, 0);
						$black = imagecolorallocate($tmp1, 0, 0, 0);
						if($extension=="png"){
						imagecolortransparent($tmp1, $black);
					     }
						
						imagecopyresampled($tmp1,$src,0,0,0,0,$newwidth1,$newheight1,$width,$height);

						if($extension=="png"){						
						imagesavealpha($src, true);									
						imagepng ($src,$filename1,9, PNG_ALL_FILTERS);						
						}else
						{
						 imagejpeg($tmp1,$filename1,100);						 
						}
						imagedestroy($src);					
						imagedestroy($tmp1);
		   			}

		   		}
		   		else
		   		{

					$newwidth=484;
					$newheight=($height/$width)*$newwidth;
					$tmp=imagecreatetruecolor($newwidth,$newheight);

					$newwidth1=200;
					$newheight1=($height/$width)*$newwidth1;
					$tmp1=imagecreatetruecolor($newwidth1,$newheight1);
					$red = imagecolorallocate($tmp1, 255, 0, 0);
					$black = imagecolorallocate($tmp1, 0, 0, 0);
					if($extension=="png"){
					imagecolortransparent($tmp1, $black);
					imagecolortransparent($tmp, $black);
				     }

					imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
					imagecopyresampled($tmp1,$src,0,0,0,0,$newwidth1,$newheight1,$width,$height);
					
					if($extension=="png"){					
					imagesavealpha($src, true);					
				
					imagepng ($src,$filename,9, PNG_ALL_FILTERS);
					imagepng ($src,$filename1,9, PNG_ALL_FILTERS);
					}else
					{
					 imagejpeg($tmp,$filename,100);
					 imagejpeg($tmp1,$filename1,100);
					}
					imagedestroy($src);
					imagedestroy($tmp);
					imagedestroy($tmp1);
				}
			  }
				//code end


		   		if($copydone)
		   		{
		   			chmod($storeFolder , 0755);	
		   			echo $picture;
		   		}
		   		exit;
			}
		}

		exit;

	}

	public function scoreuserlistAction()
	{
		$data = array ();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		$followigslague = new Application_Model_Following();	
	    $userprofile    = new Application_Model_Profile();
		
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
			
				
		 		$request 		=	$this->getRequest()->getPost();
		 		
		 		$dbeeid 		= $request['dbeeid'];
		 		$score 			= $request['score'];
		 		$userid			= $this->_userid;

		 		//dbeeid   score
		 		if($dbeeid!="" && $score!="")
		 		{
		 			 $quesry="select s.UserID,u.username,u.full_name,u.ProfilePic from tblScoring as s,tblUsers as u WHERE s.clientID='" . clientID . "' and  s.ID='" . $dbeeid . "' and  s.Score='" . $score . "' and s.UserID=u.UserID and s.UserID!='" . $userid . "'"; 
		 			$scoreother = $this->myclientdetails->passSQLquery($quesry);
		 			if(count($scoreother) > 0)
		 			{
		 				$return.='<div class="forloader"><ul class="styleListing">';
		 				foreach($scoreother as $Row):
		 					$fcnt =$followigslague->chkfollowingcnt($Row['UserID'],$_SESSION['Zend_Auth']['storage']['UserID']);
							$fellowtxt = ($fcnt['ID']>0)?'Unfollow':'Follow';

							$checkImage = new Application_Model_Commonfunctionality();
							$userprofile1 = $checkImage->checkImgExist($Row['ProfilePic'],'userpics','default-avatar.jpg');
							$return.='<li class="usrList" id="'.$Row['UserID'].'">';
							
							$return.='<a href="'.BASE_URL.'/user/'.$this->myclientdetails->customDecoding($Row['username']).'" class="userSrcProfilePic" style="background:url('.IMGPATH.'/users/small/'.$userprofile1.') no-repeat; background-size:cover;"></a>';
							$return.='<div class="userSrcProfileContainer">';
							$return.='<span class="oneline">'.utf8_encode($this->myclientdetails->customDecoding($Row['full_name'])).'';

							if($this->session_data['UserID']!=$Row['UserID'] && $Row['UserID']!=adminID){							
							$return.='
								<a class="btn btn-yellow fallowina btn-mini pull-right" onclick="javascript:followme('.$Row['UserID'].',this);" href="javascript:void(0);">
								<div id="followme-label" style="cursor: pointer;">'.$fellowtxt.'</div>
								</a>';

							} else {
							 $return.=' ';
							}

						$return.='</span></div></li>';
		 				endforeach;
		 				$return.='</ul></div>';
		 		    }

		 		   $data['status'] = 'success';
				   $data['content']= $return;
				   $encoded_rows = array_map('utf8_encode', $data);

		 		}else
		 		{

		 		   $data['status'] = 'error';
		 		} 		
		 	

	 		
	 		return $response->setBody(Zend_Json::encode($encoded_rows));			
		    exit;

	   }
    }


	public function get_data($url) 
	{
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}

	public function imagefromurlAction()
	{
		$Browser = new COM('InternetExplorer.Application');
		$Browserhandle = $Browser->HWND;
		$Browser->Visible = true;
		$Browser->Fullscreen = true;
		$Browser->Navigate('http://www.stackoverflow.com');

		while($Browser->Busy){
		  com_message_pump(4000);
		}

		$img = imagegrabwindow($Browserhandle, 0);
		$Browser->Quit();
		imagepng($img, 'screenshot.png');
	
		exit;
	}

	public function imagefromlinAction()
	{
		 $url = "http://www.yahoo.com";
 
		// Name of your output image
		$name = "example2.jpg";
		 
		// Command to execute
		$command = "/usr/bin/wkhtmltoimage-amd64 --load-error-handling ignore";
		 
		// Directory for the image to be saved
		$image_dir = "/home/dbee/public_html/new/branches/public/imageposts/";
		 
		// Putting together the command for `shell_exec()`
		$ex = "$command $url " . $image_dir . $name;
		 
		// The full command is: "/usr/bin/wkhtmltoimage-amd64 --load-error-handling ignore http://www.google.com/ /var/www/images/example.jpg"
		// If we were to run this command via SSH, it would take a picture of google.com, and save it to /vaw/www/images/example.jpg
		 
		// Generate the image
		// NOTE: Don't forget to `escapeshellarg()` any user input!
		$output = shell_exec($ex);

		exit;
	}

	public function indexAction()
	{

		$CurrDate=date('Y-m-d H:i:s');
		$this->view->userid = $user = $this->_userid;

		$request = $this->getRequest()->getParams();

		$this->view->ismypost = $request['mypost'];
		$cat  = new Application_Model_Category();
		$usersite  = new Application_Model_Usersite();
		$myhome_obj  = new Application_Model_Myhome();
		$this->session_name_space->redirection = '';
		$expire = time()+60*60*24;
		setcookie('user', $user, $expire);
		$request = $this->getRequest();
		$siteid = (int)$request->getpost('id');
		$row = $myhome_obj->getrowuser($user);
		$activity=$request->getCookie('currloginlastseen');
		if($activity=='') 
		{
			setcookie('currloginlastseen', $CurrDate);
			setcookie('currloginlastseenmsg', $CurrDate);
			setcookie('currloginlastseengroup', $CurrDate);
			setcookie('currloginlastseencomments', $CurrDate);
			setcookie('currloginlastseenmention', $CurrDate);
			setcookie('currloginlastseenredbee', $CurrDate);
			setcookie('currloginlastfollowtime', $CurrDate);
			setcookie('currloginlastscoretime', $CurrDate);				
			//setcookie('CookieLastActivity', $CurrDate);
			setcookie('dbeecheck', $CurrDate);
			setcookie('seencomms', $CurrDate);
			$n=0;
		}
		else $n=1;

		// ********** user login status as per days **********
		$chkLoginque = 'select * from tbluserlogindetails where userid = '.$user.' AND DATE_FORMAT(logindate, "%Y-%m-%d") ="'.date('Y-m-d').'"';
		$chkLogin = $this->myclientdetails->passSQLquery($chkLoginque);
		if(count($chkLogin)<1 || $chkLogin[count($chkLogin)-1]['logoutdate']!="0000-00-00 00:00:00" )
		{
			 $dataval  = array('clientID'=> clientID,'userid'=>$user,'logindate'=> date("Y-m-d H:i:s")	);
 		 	 $this->myclientdetails->insertdata_global('tbluserlogindetails',$dataval);
 		 	 $this->myclientdetails->updatedata_global ( 'tblUsers', array('LastLoginDate'=>date( "Y-m-d H:i:s" )),'UserID',$this->_userid );
		}
		//echo "<pre>"; print_r($chkLogin); exit;
		
		// ********** user login status as per days **********
		
		$user_model = new Application_Model_DbUser(); // get model object
		$usersite  = new Application_Model_Usersite();
		$this->view->cat = $cat->getallcategory();
		$result = $user_model->ausersocialdetail($this->_userid);
		$this->view->userResult = $result[0];
		
		$this->view->dbeeuser = $user;
		$this->view->n = $n;
		$this->view->ProfilePic = $row['ProfilePic'];
		$this->view->Name = $row['Name'];
		$this->view->lname = $row['lname'];
		$this->view->ShowPPBox = $row['ShowPPBox'];
		
	}
	public function redirectAction()
	{
		$this->_helper->layout->disableLayout();
		$redirection_name_space = new Zend_Session_Namespace('User_Session'); // redirection code 
		if($redirection_name_space->redirection=='')
			$this->_redirect('/');	
		$this->view->redirect = $redirection_name_space->redirection;
		$redirection_name_space->redirection = '';
	}
	public function fetchdbeeAction()
	{	
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$request = $this->getRequest();
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if($this->getRequest()->getMethod() == 'POST' && $this->getRequest()->isXmlHttpRequest())
		{			
			$type = $this->_request->getPost('type');
			
			$dbeeall  = new Application_Model_Myhome();
			$start = $request->getPost('start',0);
			$end = $start;
			$CurrDate=date('Y-m-d H:i:s');
			setcookie("CookieLastActivity", $CurrDate, 0, '/');
			$data['postcount']  = 1;
			if($this->session_data['groupid']==0){				
				
					$dbeealldbees = $dbeeall->getdbeealldbee($this->_userid,$start,$end,$this->ShowVideoEvent,$this->ShowLiveVideoEvent,$this->session_data['usertype']);
								
			}else
				$dbeealldbees = $dbeeall->getGroupdbee($this->_userid,$this->session_data['groupid']);
			$startnew = $start+PAGE_NUM;
			$end = $start+5 ;
			$end = $end;
			//$start = $start;
			$this->session_name_space->redirection = '';
			
			$commonbynew 	=	new Application_Model_Commonfunctionality();
			//$startnew=$start;
			//$startnew =$startnew;
			
			$followinguser= $this->profile_obj->getTotalUsers2($this->session_data['UserID']);
			$dbeeList = array();
			if(count($dbeealldbees) > 0)
			{
				$k=1;
				foreach ($dbeealldbees as $row) :
				if($k=='1'){setcookie("CookiePostDate", $row['PostDate'], time()+3600,'/'); setcookie("CookieLastActivity", $row['LastActivity'], time()+3600, '/');}
			
			
				$slice = array();
				if($row['TaggedUsers']!="")
				{
					$slice=explode(',', $row['TaggedUsers']);
				}
			
				if(count($slice) > 0 && $this->session_data['UserID']!=$row['User'] && $row['PrivatePost']==1)
				{
					if(in_array($this->session_data['UserID'], $slice))
					{
						
							$catchrowdata =	$commonbynew->displayLayoutDbs($row,$this->Social_Content_Block,'all',$this->plateform_scoring,$this->adminpostscore);
							
						
						
						$return	.=	$catchrowdata;
						
						//$return	.=	$commonbynew->displayLayoutDbs($row,$this->Social_Content_Block,'all',$this->plateform_scoring,$this->adminpostscore);
					}
				}
				else
				{
					
						$catchrowdata =	$commonbynew->displayLayoutDbs($row,$this->Social_Content_Block,'all',$this->plateform_scoring,$this->adminpostscore);
						
					
					$return	.=	$catchrowdata;
					//$return	.=	$commonbynew->displayLayoutDbs($row,$this->Social_Content_Block,'all',$this->plateform_scoring,$this->adminpostscore);
				}
			
				$k++;
				$dbid       = $row['DbeeID'];
				$dbeeList[] = $row['DbeeID'];
				
				$activeuser =  $this->profile_obj->getTotalUsers($this->session_data['UserID'], $row['DbeeID']);
				$data[$dbid] =json_encode(array_merge(json_decode($followinguser, true),json_decode($activeuser, true)));

				unset($slice);
				endforeach;
			}
			else {
				$startnew=$start;
				$return.='<div><div class="userVisibilityHide"><span class="fa-stack fa-2x"><i class="fa fa-pencil fa-lg"></i></span><br>No more posts found.</div></div>';
				$data['postcount']  = 0;
			}
			
			
			$return.='<div id="see-more-feeds'.$startnew.'"><div id="more-feeds-loader" style="cursor:pointer; color:#333333; text-align:center;" onclick="javascript:seemorefeeds('.$startnew.',15);">'.$seemorelabel.'</div></div>';
			
			
			
			
			$data['content']  = $return;
			$data['end']  = $end;
			$data['cache']  = $cache_debug;
			$data['startnew']  = $startnew;
			$data['dblist']  = implode(',', $dbeeList);
			
			
		}else{		
			$data['status']  = 'error';
			$data['content'] = "No posts found";		}				
		
		return $response->setBody(Zend_Json::encode($data));
		
		
	}
	
	public function categorysortAction()
	{
		$cat  = new Application_Model_Category();
		$this->view->cat = $cat->getsortcategory();
		$response = $this->_helper->layout->disableLayout();
		return $response;
	}
	public function displaybyAction()
	{
		$response = $this->_helper->layout->disableLayout();
		return $response;
	}
	public function dbeereloadAction()
	{
		$request = $this->getRequest();
		$start = (int)$request->getPost('start',0);
		$end = $request->getPost('end');
		$initial = $request->getpost('initial');
		$expire = time()+60*60*24;
		$CurrDate=date('Y-m-d H:i:s');
		$user = $this->_userid;
		$group = (int)$this->_getParam('group');

		$dbeereload  = new Application_Model_Myhome();
		$this->view->dbeealldbees = $dbeereload->getdbeereload($start,$end,$user,$group);
		$this->view->end = $start+$end;
		$this->view->start = $start;
		$this->view->startnew = $start+5;
		$this->view->end = $initial;
		$this->view->user = $user;
		$response = $this->_helper->layout->disableLayout();
		return $response;
	}

	public function dbeereload2Action()
	{
		
		
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$request = $this->getRequest();
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if($this->getRequest()->getMethod() == 'POST' && $this->getRequest()->isXmlHttpRequest())
		{			
			$start = (int)$request->getPost('start',0);
			$end = $request->getPost('end');
			$user = $this->_userid;
			$initial = $request->getpost('initial');
			$dbeereload  = new Application_Model_Myhome();
			$dbeealldbees = $dbeereload->getdbeereload($start,$end,$user);
			$end = $start+$end;
			//$start = $start;
			$startnew = $start+PAGE_NUM;
			$user = $this->_userid;
			$end = $initial;
			
			$commonbynew 	=	new Application_Model_Commonfunctionality();
			
			$IDs = '';
			//$startnew =$this->startnew;
			$dbeeList = array();
			if(count($dbeealldbees) > 0) {
				$k=1;
				if(strlen($row->Text)>250) $dots='...'; else $dots='';
				if($k=='1' && $initial=='1') setcookie("CookiePostDate", $row->PostDate, time()+3600);
			
				foreach ($dbeealldbees as $row) :
				$IDs .= $row['DbeeID'].',';
				$slice = array();
				if($row['TaggedUsers']!="")
				{
					$slice=explode(',', $row['TaggedUsers']);
				}
			
				if(count($slice) > 0 && $this->session_data['UserID']!=$row['User'] && $row['PrivatePost']==1)
				{
					if(in_array($this->session_data['UserID'], $slice))
					{
						$return.=$commonbynew->displayLayoutDbs($row,$this->Social_Content_Block,'all',$this->plateform_scoring,$this->adminpostscore);
					}
				}
				else
				{
					$return.=$commonbynew->displayLayoutDbs($row,$this->Social_Content_Block,'all',$this->plateform_scoring,$this->adminpostscore);
				}
				$k++;
				$dbeeList[] = $row['DbeeID'];
				endforeach;
			
			}
			else {
				//$seemorelabel='- no more '.POST_NAME.'s available to show -';
			}
			$IDs=substr($IDs,0,-1);
			
			$return.='<div id="see-more-feeds'.$startnew.'"><div id="more-feeds-loader" style="cursor:pointer; color:#333333; text-align:center;" onclick="javascript:seemorefeeds('.$startnew.',15);">'.$seemorelabel.'</div></div>';
			
			//echo $return.'~#~'.$end.'~#~'.$IDs.'~#~'.$startnew.'~#~'.implode(',', $dbeeList);
			
			$data['content']  = $return;
			$data['end']  = $end;
			$data['id']  = $IDs;
			$data['startnew']  = $startnew;
			$data['dbeeList']  = implode(',', $dbeeList);
				
			
		}else{
			$data['status']  = 'error';
			$data['content'] = "No posts found";		}
		
			return $response->setBody(Zend_Json::encode($data));
		
		
	}
	public function myexpertdbeeAction()
	{
		$request = $this->getRequest();
		$user = $request->getPost('user',$this->_userid);
		$userinfo = new Application_Model_DbUser();
		$userinfodetails = $userinfo->userdetailall($user);
		$userexiest = count($userinfodetails);
		$this->view->userexiest = $userexiest;
		if($user==-1){
			$user = $this->_userid;			
		}
		$start = (int)$request->getPost('start')?(int)$request->getPost('start'):0;
		$end = $request->getPost('end')?$request->getPost('end'):PAGE_NUM;
		$dbeereload  = new Application_Model_Myhome();
		$dbeereloadrs = $dbeereload->getmydbee($start,$end,$user,'expert');
		$this->view->dbeealldbees = $dbeereloadrs;
		$this->view->start = $start;
		$this->view->startnew = $start+PAGE_NUM;
		$this->view->end = $start+PAGE_NUM;
		$this->view->user = $this->_userid;
		$this->view->dbeenotavailmsg = $request->getPost('dbeenotavailmsg');
		$response = $this->_helper->layout->disableLayout();
		return $response;
	}
	public function mydbeeAction()
	{
		$request = $this->getRequest();
		$user = $request->getPost('user');
		$type = $request->getPost('type');
		if($user==-1 || $user=='')
			$user = $this->_userid;

		$userinfo = new Application_Model_DbUser();
		$userinfodetails = $userinfo->userdetailall($user);
		$userexiest = count($userinfodetails);
		$this->view->userexiest = $userexiest;

		$start = (int)$request->getPost('start')?(int)$request->getPost('start'):0;
		$end = $request->getPost('end')?$request->getPost('end'):PAGE_NUM;
		$dbeereload  = new Application_Model_Myhome();
		if($type!='group')
			$dbeereloadrs = $dbeereload->getmydbee($start,$end,$user,'',$this->ShowVideoEvent,$this->ShowLiveVideoEvent);
		else
			$dbeereloadrs = $dbeereload->groupdbee($start,$end,$user,$this->session_data['groupid']);

		$this->view->dbeealldbees = $dbeereloadrs;
		$this->view->start = $start;
		$this->view->startnew = $start+PAGE_NUM;
		$this->view->end = $start+PAGE_NUM;
		$this->view->user = $this->_userid;
		$this->view->dbeenotavailmsg = $request->getPost('dbeenotavailmsg');
		$response = $this->_helper->layout->disableLayout();
		return $response;
	}
	public function mytaggeddbeeAction()
	{
		$request = $this->getRequest();
		$user = $request->getPost('user');
		$type = $request->getPost('type');
		if($user==-1 || $user=='')
			$user = $this->_userid;

		$userinfo = new Application_Model_DbUser();
		$userinfodetails = $userinfo->userdetailall($user);
		$userexiest = count($userinfodetails);
		$this->view->userexiest = $userexiest;

		$start = (int)$request->getPost('start')?(int)$request->getPost('start'):0;
		$end = $request->getPost('end')?$request->getPost('end'):PAGE_NUM;
		$dbeereload  = new Application_Model_Myhome();
		if($type!='group')
			$dbeereloadrs = $dbeereload->gettaggedmydbee($start,$end,$user);
		else
			$dbeereloadrs = $dbeereload->gettaggedmydbee($start,$end,$user,$this->session_data['groupid']);

		$this->view->dbeealldbees = $dbeereloadrs;
		$this->view->start = $start;
		$this->view->startnew = $start+PAGE_NUM;
		$this->view->end = $start+PAGE_NUM;
		$this->view->user = $this->_userid;
		$this->view->dbeenotavailmsg = $request->getPost('dbeenotavailmsg');
		$response = $this->_helper->layout->disableLayout();
		return $response;
	}
	public function catetorylistAction()
	{
		$dbeecat  = new Application_Model_Myhome();
		$requestval = $this->getRequest()->getParams();
		//echo'<pre>';print_r($requestval);die;
		$request = $this->getRequest();
		$start = $request->getPost('start',0);
		$end = $start+5 ;
		$category = trim($request->getPost('cat'),', ');
		
		$precal = $request->getPost('precal'); 
		/*$category = trim($request->getPost('datachk'),', ');*/
		if($precal==''){
			$catdbid 	=	explode(',', $category);
		}else{
			$catdbid[] 	=  $precal;
			$catdbidpre =	explode(',', $category);
		}
		
		$nameofCat 	=	array();
		if($precal==''){
		  foreach($catdbid as $value){
		    $getcatsname =  $this->myclientdetails->getfieldsfromtableusingin(array('CatName','CatID'),'tblDbeeCats','CatID',trim($value) );
		    $nameofCat[]=$getcatsname;
		   }
		}else{
			foreach($catdbid as $value){
				$getcatsname =  $this->myclientdetails->getfieldsfromtableusingin(array('CatName','CatID'),'tblDbeeCats','CatID',trim($value) );
				$nameofCat[]=$getcatsname;
			}

			foreach($catdbidpre as $value){
				$getcatsname =  $this->myclientdetails->getfieldsfromtableusingin(array('CatName','CatID'),'tblDbeeCats','CatID',trim($value) );
				$nameofCatpre[]=$getcatsname;
			}
		}			
		$dbeeincat 	=	array();
		/*foreach($catdbid as $key => $val)
		{*/
			/*if($precal==''){
			 $dbeeincat	= $dbeecat->getdbeesearchid($catdbid[0]);
		    }else{
		      $dbeeincat	= $dbeecat->getdbeesearchid($catdbid[0]);	
		    }
			//echo $val;echo'<pre>';print_r($dbeeincat);
			if(count($dbeeincat)>0)
			{
				foreach ($dbeeincat as $key => $value) {
					$dbeeincatret[] = $value['DbeeID'];
				}
			}*/
		/*}*/

		foreach($catdbid as $key => $val)
		{
			
			$dbeeincat	= $dbeecat->getdbeesearchid($val);
			if(count($dbeeincat)>0)
			{
				foreach ($dbeeincat as $key => $value) {
					$dbeeincatret[] = $value['DbeeID'];
				}
				
			}
		}
		$dbeeincatret = array_unique($dbeeincatret);		
		$this->view->dbeealldbees = $dbeecat->mydbeecat($start,$end,$dbeeincatret); 
		$this->view->dbeenotavailmsg = $request->getPost('dbeenotavailmsg');
		if($start=="" || $end==""){
			if($precal==''){
				$this->view->nameofCat = $nameofCat;
			}else{
				$this->view->nameofCat = $nameofCatpre;
			}	
		}			
		$this->view->end = $end;
		$this->view->start = $start;
		$this->view->cat = $catdbid[0];
		$this->view->startnew = $start+PAGE_NUM;
		$response = $this->_helper->layout->disableLayout();
		//echo'<pre>';print_r($response);die;
		return $response;
	}
	public function mostcommentedAction()
	{
		$request = $this->getRequest();
		$start = $request->getPost('start',0);
		$end = $start+5 ;
		$initial = $request->getpost('initial');
		$dbeemostcommenet  = new Application_Model_Myhome();
		$this->view->dbeealldbees = $dbeemostcommenet->mydbeesortby($start,$end);
		$this->view->end = $end;
		$this->view->start = $start;
		$this->view->startnew = $start+PAGE_NUM;
		$response = $this->_helper->layout->disableLayout();
		return $response;
	}
	public function filtertypeAction()
	{
		$request = $this->getRequest();
		$start = $request->getPost('start',0);		
		$initial = $request->getpost('initial');
		$type = $request->getpost('type');
		$type = trim($type, ' OR'); 
		$dbeesortbydata  = new Application_Model_Myhome();
		$this->view->dbeealldbees = $dbeesortbydata->mydbeesortbydata($start,$end,$type,$this->_userid);

		$this->view->start = $start;
		$this->view->startnew = $start+PAGE_NUM;
		$this->view->end = $start+PAGE_NUM; 
		$response = $this->_helper->layout->disableLayout();
		return $response;
	}

	public function mysurveyfiltertypeAction()
	{
		$request = $this->getRequest();
		$start = $request->getPost('start',0);		
		$initial = $request->getpost('initial');
		$type = $request->getpost('type');
		$type = trim($type, ' OR'); 
		$dbeesortbydata  = new Application_Model_Myhome();
		$UserID = $_SESSION['Zend_Auth']['storage']['UserID'];		
		
		$this->view->dbeealldbees = $dbeesortbydata->mydbeesortbydata3($start,$end,$type,$UserID);
		$this->view->start = $start;
		$this->view->startnew = $start+PAGE_NUM;
		$this->view->end = $start+PAGE_NUM; 
		$response = $this->_helper->layout->disableLayout();
		return $response;
	}

	public function myfiltertypeAction()
	{
		$request = $this->getRequest();
		$start = $request->getPost('start',0);		
		$initial = $request->getpost('initial');
		$type = $request->getpost('type');
		$type = trim($type, ' OR'); 
		$dbeesortbydata  = new Application_Model_Myhome();
		$UserID = $_SESSION['Zend_Auth']['storage']['UserID'];

		$removeattendee 			= $request->getpost('removeattendee');
		//$this->view->removeattendee = $removeattendee;
		$UserID = $_SESSION['Zend_Auth']['storage']['UserID'];
		if($removeattendee!="")
		{
		$this->myclientdetails->deleteMaster('tblDbeeJoinedUser',array('userID'=>$UserID,'dbeeID'=>$removeattendee));	
		}	
		
		$this->view->dbeealldbees = $dbeesortbydata->mydbeesortbydata2($start,$end,$type,$UserID);
		$this->view->start = $start;
		$this->view->startnew = $start+PAGE_NUM;
		$this->view->end = $start+PAGE_NUM; 
		$response = $this->_helper->layout->disableLayout();
		return $response;
	}
	public function mylivevideofilterAction()
	{
		$request = $this->getRequest();
		$start = $request->getPost('start',0);		
		$initial = $request->getpost('initial');
		$type = $request->getpost('type');
		$type = trim($type, ' OR'); 
		$dbeesortbydata  = new Application_Model_Myhome();
		$UserID = $_SESSION['Zend_Auth']['storage']['UserID'];

		$removeattendee 			= $request->getpost('removeattendee');
		//$this->view->removeattendee = $removeattendee;
		$UserID = $_SESSION['Zend_Auth']['storage']['UserID'];
		if($removeattendee!="")
		{
		$this->myclientdetails->deleteMaster('tblDbeeJoinedUser',array('userID'=>$UserID,'dbeeID'=>$removeattendee));	
		}	
		
		$this->view->dbeealldbees = $dbeesortbydata->mydbeesortbydata2($start,$end,$type,$UserID);
		$this->view->start = $start;
		$this->view->startnew = $start+PAGE_NUM;
		$this->view->end = $start+PAGE_NUM; 
		$response = $this->_helper->layout->disableLayout();
		return $response;
	}
	public function eventlistAction()
	{
		$request = $this->getRequest();
		$response = $this->_helper->layout->disableLayout();
		return $response;
	}
	public function myeventlistAction()
	{
		$request = $this->getRequest();
		$removeattendee = $request->getpost('removeattendee');
		if($removeattendee!="")
		{			
			$fetchdbeearray = $this->myclientdetails->getAllMasterfromtable('tblDbees',array('DbeeID'),array('events'=>$removeattendee),array('DbeeID'=>'ASC'));
			
			if(count($fetchdbeearray) > 0)
			{
				foreach ($fetchdbeearray as $key => $value) {					
				  $this->myclientdetails->deleteMaster('tblexpert',array('userid'=>$this->_userid,'dbid'=>$value['DbeeID']));
				}
			}

			$this->myclientdetails->deleteMaster('tblEventmember',array('member_id'=>$this->_userid,'event_id'=>$removeattendee));	
		}

		$response = $this->_helper->layout->disableLayout();
		return $response;
	}
	public function dbfeedfilterAction()
	{
		$request = $this->getRequest();
		$start = $request->getPost('start',0);
		$end = $start+5 ;
		$score = $request->getpost('score');
		$score = trim($score, ' OR'); 
		$dbeesortbydata  = new Application_Model_Myhome();
		$this->view->dbeealldbees = $dbeesortbydata->dbfeedfilter($start,$end,$score);
		$this->view->end = $end;
		$this->view->start = $start;
		$this->view->startnew = $start+PAGE_NUM;
		$response = $this->_helper->layout->disableLayout();
		return $response;
	}
	public function insertredbeeAction()
	{
		//require_once 'includes/globalfile.php'; //file for global variables
		$request = $this->getRequest();
		$dbee = (int)$request->getpost('db');
		$user = (int)$request->getpost('dbOwner');
		$data2 = array('clientID'=>clientID,'DbeeID'=>$dbee,'DbeeOwner'=>$user,'ReDBUser'=>$this->_userid,'ReDBDate' => date('Y-m-d H:i:s'));
		
		$redbeeinsert  = new Application_Model_Myhome();
		if($redbeeinsert->chkredbee($dbee,$user,$this->_userid)){
			
			if($redbeeinsert->updateredbee($data2,$dbee))
				$SubmitMsg=1;
		}else{
			if($redbeeinsert->addredbee($data2))
				$SubmitMsg=1;
		}

		$this->notification->commomInsert('7','7',$dbee,$this->_userid,$user); // Insert for redb activity
		
		$data = array('ReDBUsers'=>$this->_userid,'LastActivity' => date('Y-m-d H:i:s'));
		$this->myclientdetails->updatedata_global('tblDbees',$data,'DbeeID',$dbee);
		setcookie("CookieLastActivity", $CurrDate, 0, '/');
		echo $SubmitMsg.'~'.'0';
		
		$response = $this->_helper->layout->disableLayout();
		return $response;
	}
	
	
	public function dbeedetailAction()
	{
		$id = (int)$this->_getParam('id');
		$uid = $this->_userid;
		$dbeetwiter  = new Application_Model_Myhome();
		$this->view->userid = $this->_userid;
		$this->view->dbeeid = $id;
		$this->view->Twitertag = $dbeetwiter->gettwitertag($id);
		return $response;
	}

	public function checksociallinkingAction()
	{
		$data = array();
	    $this->_helper->layout()->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(true);
	    $response = $this->getResponse();
	    $response->setHeader('Content-type', 'application/json', true);
		if($this->getRequest()->getMethod() == 'POST' && $this->getRequest()->isXmlHttpRequest())
		{	
				$where = array('1'=>1);
                $socialloginabilitydetail = $this->myclientdetails->getRowMasterfromtable('tblloginsocialresource',array('*'),$where);
				
				$data['allSocialstatus'] = $socialloginabilitydetail['allSocialstatus'];
				$data['Facebookstatus']  = $socialloginabilitydetail['Facebookstatus'];
				$data['Twitterstatus']   = $socialloginabilitydetail['Twitterstatus'];
				$data['Linkedinstatus']  = $socialloginabilitydetail['Linkedinstatus'];
	 	}

	    return $response->setBody(Zend_Json::encode($data));

	}

	public function removeinviteAction()
	{
		$data = array();
	    $this->_helper->layout()->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(true);
	    $response = $this->getResponse();
	    $response->setHeader('Content-type', 'application/json', true);
		if($this->getRequest()->getMethod() == 'POST' && $this->getRequest()->isXmlHttpRequest())
		{	
				
			$content = '';
			$DbeeID = $this->_request->getPost('dbid');
			

			 if ($DbeeID!="")   
			 {
			        $where = array('act_typeId'=>$DbeeID,'act_type'=>39,'act_message'=>50);
                	$this->myclientdetails->deleteMaster('tblactivity',$where);
			 }			

			 $data['status'] = 'success';
			 
	 	}

	    return $response->setBody(Zend_Json::encode($data));

	}

	public function dbeechkAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$request = $this->getRequest();
	 	$activity=$request->getCookie('CookieLastActivity');
	 	
	 	if($activity=='')
	 	{
	 		$factivity = $CurrDate=date('Y-m-d H:i:s');
			setcookie("CookieLastActivity", $CurrDate, 0, '/');
	 	}
	 	else
	    {
	 	 $factivity = $activity;
	    }
	    $this->ShowVideoEvent;
		$cookieuser = $this->_userid;
		$dbeeall  = new Application_Model_Myhome();
		if($this->session_data['groupid']==0)
			$data = $dbeeall->dbeechkdata($factivity,$cookieuser,$this->ShowVideoEvent);
		else
			$data = $dbeeall->dbeechkgroupdata($factivity,$cookieuser,$this->session_data['groupid']);
		echo $data.'~#~'.'0';
	}
	public function tweetCheck($q)
	{
		
		$hash=false;
		
		if(substr($q,0,1)=='#') 
		{
			$hash=true;
			$q=substr($q,1);
			$q1='#'.$q;
		}else{
			$q1=$q;
		}

		$q=str_replace(' ','',$q);
		 
		$twitteroauth = new TwitterOAuth(twitterAppid, twitterSecret, 
		    $this->twitter_connect_data['twitter_access_token'], 
		    $this->twitter_connect_data['twitter_token_secret']);

		$results  = $twitteroauth->get('search/tweets', array(
		'q' => $q,
		'result_type' => 'mixed',
		'count' => 4
		));		

		$results = json_decode(json_encode($results), true);
		
		if(is_array($results['statuses'])) 
		{
			foreach( $results['statuses'] as $result) 
			{
				$screen_name=$results['statuses'][$cnt]['user']['screen_name'];
				$profile_image=$results['statuses'][$cnt]['user']['profile_image_url'];
				$tweet=$results['statuses'][$cnt]['text'];				
				$data = array('Keyword'=>$q1,'ScreenName'=>$screen_name,'ProfilePic' => $profile_image,'Tweet'=>$tweet);
				
			} 
		}
		return count($data);
	}

	public function testlistAction()
	{		
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
		$twitteroauth = new TwitterOAuth(twitterAppid, twitterSecret, 
	    $this->twitter_connect_data['twitter_access_token'], 
	    $this->twitter_connect_data['twitter_token_secret']);

        $rateLimit  = $twitteroauth->get('application/rate_limit_status', array());
        echo  '<pre>';
        $rateLimit = json_decode(json_encode($rateLimit), true);
        print_r($rateLimit['resources']['friendships']);/*
		$results  = $twitteroauth->get('search/tweets', array(
		'q' => 'cricket',
		'result_type' => 'mixed',
		'count' => 4
		));	
		$results = json_decode(json_encode($results), true);
*/

	}

	

	////////////////// Insert Start ////////////////
	public function dbinsertdataAction()
	{
		//require_once 'includes/globalfile.php'; //file for global variables
		
		$request = $this->getRequest();
		$filter = new Zend_Filter_StripTags();
		$response = $this->_helper->layout->disableLayout();	
		$commonfun  = new Application_Model_Commonfunctionality();

        $admindata = $this->myclientdetails->getfieldsfromtable(array('Email'),'tblUsers','role',1);

        $loginuserdata = $this->myclientdetails->getfieldsfromtable(array('hideuser','usertype'),'tblUsers','UserID',$this->_userid);

		$validate = $commonfun->authorizeToken($request->getPost('SessUser__'),$request->getPost('SessId__'),$request->getPost('SessName__'),$request->getPost('Token__'),$request->getPost('Key__')); 

		if($validate=='false') {
			echo $validate; // go to success event of ajax and redirect to myhome/logout  action
			exit;
		}

		// End of token and user session checking

		if (!$request->isPost()) {
			echo false; // go to success event of ajax and redirect to myhome/logout  action
			exit;
		}
		$data2 = array();
		$dbeetype = $request->getPost('dbeetype');

		$dbee_tag = $request->getPost('dbeeTag');

		if(clientID==19 && $dbee_tag!='')
		{
			$tagData = $this->myhome_obj->getTagdbee($dbee_tag);
			if(!empty($tagData)){
				echo 'errors'.'~'.$dbee_tag.' tag already exist';
				exit;
			}
		}
		
		// for special db : 6 and surveys 7 and polls 5 and remaining 1
		if($dbeetype=='Polls'){ 
			$Type=5;  $dbtitle = $this->makeSeo($request->getPost('polltext'),'','save'); }
		else { $Type=1; $dbtitle = $this->makeSeo($request->getPost('text'),'','save');}
		
		if($cat=='' || $cat=='undefined') $cat=10;
		
		$postdate=date('Y-m-d H:i:s');
		$twittertag = $filter->filter((stripslashes($request->getPost('twittertag'))));

		if($twittertag=='undefined')
			$twittertag = '';		
		
		$groupid 	   = $filter->filter(stripslashes($request->getPost('group')));
		$events 	   = $filter->filter(stripslashes($request->getPost('events')));

		$groupname     = $filter->filter($this->_request->getPost('groupname'));
		$activity 	   = date('Y-m-d H:i:s');
		$dbtag 		   = $request->getPost('DbTag');
		$QASTATUS 		   = $request->getPost('QASTATUS');
		$qatime 		   = $request->getPost('qatime');
		$qaendschedule 		   = $request->getPost('qaendtime');
		$timezone 		   = $request->getPost('timezone');
		if($QASTATUS==1)
		{
			$qatime = $commonfun->convert_time_zone($qatime,$timezone ,'Europe/London');
			$qaendschedule = $commonfun->convert_time_zone($qaendschedule,$timezone ,'Europe/London');
		}
		if(count($dbtag)> 0)
		{
		$dbtag         = implode(",",$dbtag);
		$data['DbTag'] = str_replace("#","",$dbtag);
	    }
	    else
	    {
	    $data['DbTag'] = '';
	    }
	    $chkval = $filter->filter((stripslashes($request->getPost('chkval'))));
		if($chkval==1){
			$this->updateshowtagmsg(0);
		}
		

		$rssFinaladded 		= '';

		if($request->getPost('rssfeed')!='')
		{
			$feedrepaceLink		= '<a href="'.$request->getPost('rssLink').'" target="_blank">more</a>';
			$rssadded			= str_replace('more', $feedrepaceLink, $filter->filter( $request->getPost('rssfeed')));
			$feedrepaceTitle	= '<div id="dbRssTitle">'.$request->getPost('rssTitle').'</div>';
			$rssFinaladded 		= str_replace($request->getPost('rssTitle'), $feedrepaceTitle, $rssadded);	
		}	
		//echo $rssFinaladded; exit;

		$dbinleague 		= $request->getPost('adddbtoleague');
		$leagueenddate		= $request->getPost('lgenddate');
		$data['clientID'] 	= clientID;
		$data['LastCommentUser'] = (int)$this->_userid;
		//$data['Type'] 		= $Type;
		$TaggedUsers         = stripslashes($filter->filter($request->getPost('TaggedUsers')));
		$AdminUserSet        = '';
		$AdminUserSetVar 	     = $filter->filter(stripslashes($request->getPost('AdminUserSet')));
		$userset =array();
		if($AdminUserSetVar!="")
		{
			$usersetarray 	= 	$this->myclientdetails->getAllMasterfromtable('usersgroupid',array('userid'),array('ugid'=>$AdminUserSetVar),array('id'=>'ASC'));
			$userset =array();
			foreach ($usersetarray as $key => $value) {
				$userset[] = $value['userid'];
			}
			$AdminUserSet = implode(", ",$userset);
		}

		if($Type!='5')
		{
			if($TaggedUsers=="")
			{
			 $data['Text'] 	= $request->getPost('text');
			}
			else
			{
			  $data['Text'] = $request->getPost('text');
			}
		}

		
		
		$data['RssFeed'] 	= $rssFinaladded;//stripslashes(str_replace($feedrepace, '',$request->getPost('rssfeed')));
		$data['Cats'] 		= stripslashes($filter->filter($request->getPost('cat')));
		
		$sliceallpic = array();
		if($request->getPost('pic')!="")
		{
			$sliceallpic=explode(',', $request->getPost('pic'));
			
		}
		if($sliceallpic[0]!="")
		{
		$data['Pic'] 		= $sliceallpic[0];
	    }
		
		if($sliceallpic[0]!=''){
			$imageSize = @getimagesize($_SERVER['DOCUMENT_ROOT'].'/imageposts/'.$sliceallpic[0]);
			$data['PicSize'] 		= $imageSize[0];
		}else
			$data['PicSize'] = '';

		$data['PrivatePost'] 		= stripslashes($filter->filter($request->getPost('PrivatePost')));

		$data['Link'] 		= stripslashes($filter->filter($request->getPost('url')));
		$data['LinkTitle'] 	= stripslashes($filter->filter($request->getPost('linktitle')));
		$data['LinkDesc'] 	= stripslashes($filter->filter($request->getPost('linkdesc')));
		$data['LinkPic'] 	= stripslashes($filter->filter($request->getPost('LinkPic')));
		$data['Vid'] 		= stripslashes($filter->filter($request->getPost('vid')));
		$data['VidDesc'] 	= stripslashes($filter->filter($request->getPost('viddesc')));
		$data['VidSite'] 	= strtolower(stripslashes($filter->filter($request->getPost('videosite'))));
		
		$data['QA'] 	= 0;
		$data['qaschedule'] 	= date('Y-m-d H:i:s',strtotime($qatime));
		$data['qaendschedule'] 	= date('Y-m-d H:i:s',strtotime($qaendschedule));
 		
 		
		$data['VidID'] 		= stripslashes($filter->filter($request->getPost('videoid')));
		$data['VidTitle'] 	= stripslashes($filter->filter($request->getPost('VidTitle')));
		$data['User'] 		= (int)$this->_userid;
		$data['GroupID'] 	= stripslashes($filter->filter($request->getPost('group')));
		$data['Privategroup'] 	= stripslashes($filter->filter($request->getPost('groupty')));
		$data['TwitterTag'] = trim($twittertag);
	    $data['events'] = $events;
	    $data['dbee_tag'] = $dbee_tag;
	    if($events){
	    	$event_result = $this->eventModel->getEventRecord($events);
	    	if(!empty($event_result))
	    		$data['event_type'] = $event_result['type'];
	    }
		
		$data['Attachment'] = stripslashes($filter->filter($request->getPost('attachment')));
		$keyword 			=  explode(",",trim($data['TwitterTag']));
		$keyword1 = $data['TwitterTag'];
		$count =  count($keyword);	
		

		if($count<=4) 
		{
			if($count>0 && $twittertag!='')	
			{
				$keyword_count = array();
				$keyword_error ='';
	
				for ($i=0; $i < $count; $i++) 
				{ 
				   $keyword_count = $this->tweetCheck(trim($keyword[$i]));
				   if($keyword_count==0)
						$keyword_error .= $keyword[$i].' ';
				}
	
				if(strlen($keyword_error)!=0)
				{
					echo 'errors'.'~'.'No tweets found for '.$keyword_error.'. Please change or remove in order to continue with the post. ';
					exit;
				}
			}
		} else {
			echo 'errors'.'~'.'Please enter maximum 4 #hashtags/keywords.';
			exit;
		}		
		
		$data['PostDate'] = $postdate;
		$data['LastActivity'] = $activity;
		$data['polltext'] = stripslashes($filter->filter($request->getPost('polltext')));
		
		if($this->allow_admin_post_live==1 && $this->_userid!=adminID)
		{
		   $data['Active'] = 3;
		   $Adminapproved=$this->allow_admin_post_live;
		}
		else
		{
           $data['Active'] = 1;
           $Adminapproved=$this->allow_admin_post_live;
		}
		if($this->_userid==adminID)
		{
			$Adminapproved=0;
		}

		$data['dburl'] = $dbtitle;		

		if($Type=='5')
		{
			$data2['polloption1'] = stripslashes($filter->filter($request->getPost('polloption1')));
			$data2['polloption2'] = stripslashes($filter->filter($request->getPost('polloption2')));
			$data2['polloption3'] = stripslashes($filter->filter($request->getPost('polloption3')));
			$data2['polloption4'] = stripslashes($filter->filter($request->getPost('polloption4')));
			
			$data['DbTag']='';
			$groupty = $filter->filter($request->getPost('groupty'));
			if($groupty!='')
				$data['Privategroup'] 	= 1;
			else
				$data['Privategroup'] 	= 0;

			if($this->_userid==adminID || $this->allow_user_create_polls==1)
			{			
				$pollst=true;
			}
			else
			{
				echo 'errors'.'~'.'Oops! Posting a poll has just been disabled by the platform admin!';
			    exit;
			}
		}

		if($events!=""){
			
			$Type = 9;
		}
		else if($QASTATUS==1){
			$Type = 20;
		}
		else if($dbeetype=='Polls'){ 

			$Type = 5;
		}
		else if($data['Text']!="" && $data['VidID']=="" && $data['Pic']=="" && $data['Link']=="")
		{
			$Type="1";

		}
		else if($data['Text']!="" && $data['VidID']=="" && $data['Pic']=="" && $data['Link']!="")
		{
			$Type="2";
		}
		else if($data['Text']!="" && $data['VidID']=="" && $data['Pic']!="" && $data['Link']=="")
		{
			$Type="3";
		}
		else if($data['Text']!="" && $data['VidID']!="" && $data['Pic']=="" && $data['Link']=="")
		{
			$Type="4";
		}
		else if($data['Text']!="" && $data['VidID']=="" && $data['Pic']!="" && $data['Link']!="")
		{
			$Type="10";
		}
		else if($data['Text']!="" && $data['VidID']!="" && $data['Pic']!="" && $data['Link']=="")
		{
			$Type="11";
		}

		$data['Type'] 		= $Type;
       
		$TaggedUsersanduserset='';
		
        if($AdminUserSet!="" && $TaggedUsers!="")
        {
        	$TaggedUsersanduserset = $TaggedUsers.','.$AdminUserSet;
        }
        else if($AdminUserSet=="" && $TaggedUsers!="")
        {
        	$TaggedUsersanduserset = $TaggedUsers;
        }
        else if($AdminUserSet!="" && $TaggedUsers=="")
        {
        	$TaggedUsersanduserset = $AdminUserSet;
        }

        
        $TaggedUsersanduserset = implode(', ', array_unique(explode(', ', $TaggedUsersanduserset)));

        $data['TaggedUsers'] = $TaggedUsersanduserset; 

		
		$insertdbeeobj  = new Application_Model_Myhome();

		$success1 = $insertdbeeobj->insertmydb($data);	
		
		//dbee pic insert
		if(count($sliceallpic) > 1) {

			 $p=0;	
			 foreach ($sliceallpic as $key => $value) {
			 	if($p==0)
			 	{
			 		$isDbeeDefaultPic=1;
			 	}
			 	else{

			 		$isDbeeDefaultPic=0;
			 	}

				$allpicData  = array(
		                    'clientID' => clientID,
		                    'type' => 0,
		                    "reff_key_id" => $success1,
		                    "picName" => $value,
		                    "isDbeeDefaultPic" => $isDbeeDefaultPic
		                );
		        $insertDbeePics = $this->myclientdetails->insertdata_global('tblDbeePics',$allpicData);

		        $p++;
	         }
	    }

    	//dbee pic insert


		if($this->allow_admin_post_live==1 && $success1!="" && $this->informviaemail==1)
		{
		   
		    $mail = new Zend_Mail();
			$setSubject="New post awaiting approval";

			$body="";

			$body.='<body style="background:#E8E9EB;"><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td style="padding-top:100px; padding-bottom:100px; "><table width="623" style="background:#ffffff; font-family:Arial, Helvetica, sans-serif; font-size:13px;" align="center" border="0" cellspacing="0" cellpadding="30"><tr><td><a href="'.BASE_URL.'"  title="'.BASE_URL.'" target="_blank"><img src="'.BASE_URL.'/img/'.$this->configuration->SiteLogo.'" border="0"  style="display:inline-block" alt="dbee logo" /></a></td></tr>';

			$body.= '<tr><td style="padding:0px 30px 30px 30px; ">
			<strong>Dear Admin</strong>,<br /> <br /><br />

			'.$this->myclientdetails->customDecoding($this->session_data['Name']).' submitted a new post for your approval.<br><br/><b>'.$filter->filter(stripslashes($request->getPost('text'))).'</b><br/><br/>

			<a href="'.adminURL.'admin/dashboard/post/id/'.$success1.'" target="blank">Click here to go to post</a> .
			<br>
			</td>
			</tr>';

			$body.= '<tr><td style="background:#d3d3d3;"><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td><div align="right"><a href="http://www.db-csp.com" target="_blank" style="text-decoration:none"><font color="#666">powered by db corporate social platforms</font></a></div></td></tr></table></td></tr></table></td></tr></table></body>';

            $to=$this->myclientdetails->customDecoding(trim($admindata[0]['Email']));
           
            $this->sendWithoutSmtpMail($to,$setSubject, $from ='no-reply@db-csp.com',$body);
		}

		if($dbtitle==''){
			$datadburl = array('dburl'=> $success1);
			$insertdbeeobj->updatedbee($datadburl, $success1);
		}
		
		if($success1 !='' && $dbinleague!='') // insert db into league
		{
			$leagueData  = array(
                    'clientID' => clientID,
                    'LID' => $dbinleague,
                    "DbeeID" => $success1,
                    "Enddate" => $leagueenddate,
                    "Status" => '1'
                );
            $insertleague = $this->myclientdetails->insertdata_global('tblLeagueDbee',$leagueData);
		}		
		
		// post on facebook wall
		$dburl = BASE_URL."/dbee/".$dbtitle;

		$Facebook = $filter->filter($this->_request->getPost('Facebook'));
		$Twitter = $filter->filter($this->_request->getPost('Twitter'));
		
		$groupname = $filter->filter($this->_request->getPost('groupname'));
		
		if($Facebook==11)
			$this->facebookShareOnWall($data, $dburl);
		if($Twitter==12)
			$this->twitterShareOnWall($data, $dburl);
		
		// end code  on facebook wall
		 $last_insertedId=$success1;
		

		 if($success1)
		 {
			 $mentionuserdata=stripslashes($filter->filter($request->getPost('dbmentiononPost')));
			 if($mentionuserdata!="")
			 	$this->twittermentionShareOnWall($data, $dburl, $mentionuserdata);
		 }

		 

		if($Type=='5')
		{
			 $success = $insertdbeeobj->insertpol($data2,$last_insertedId,$ChosenCount=0);	
		}
		
		if($success1)
		{
			$success=1;
			$CurrDate=date('Y-m-d H:i:s');
			setcookie("CookieLastActivity", $CurrDate, 0, '/');
		}
		
		$expire = time()+60*60*24;
		$CurrDate=date('Y-m-d H:i:s');
		$isgroupdb = $filter->filter($request->getPost('groupty'));
		if($this->allow_admin_post_live==1 && $success1!="" && $request->getPost('PrivatePost')==0)
		{

			$this->notification->commomInsert('30','49',$last_insertedId,$this->_userid,adminID); // Insert for involve activity 
		
		} 
		elseif($isgroupdb!=1)
		{
		  	if($request->getPost('PrivatePost')==0)	// if there is no private post
		  	{
		  		if($events)
		  		{
					$this->notification->commomInsert('1','1',$last_insertedId,$this->_userid,$this->_userid,'','',1); // Insert for involve activity
			    }
			    else
			    {
			    	$this->notification->commomInsert('1','1',$last_insertedId,$this->_userid,$this->_userid); // Insert for involve activity
			    }

			}
			elseif(count($TaggedUsersanduserset) > 0)
			{
				$sliceTaggedUsersanduserset = explode(',', $TaggedUsersanduserset);
				$sliceTaggedUsersanduserset = array_unique($sliceTaggedUsersanduserset);
				$temp = array();
				foreach ($sliceTaggedUsersanduserset as $key => $value) {
					 if (!in_array($value, $temp)) {
					   $this->notification->commomInsert('3','3',$last_insertedId,$this->_userid,trim($value));
					   $temp[] = $value;
					}
				}
		   	 }
		}

  		$groupobj  = new Application_Model_Groups();

  		if($events){
	    	$event_member = $this->eventModel->getAllEventMember($events);

	    	if(count($event_member)>0)
			{
				
				foreach ($event_member as $value) {
					if($this->_userid!=$value['member_id']){		  
				  $this->notification->commomInsert('47','57',$last_insertedId,$value['member_id'],$this->_userid); // Insert for tagged user activity 
				 }
				}
				
				
			}	
	    	
	    }
  		
	  	if($isgroupdb!=1 && !empty($groupid))
	  	{
			$groupUsers2 = $groupobj->calgroup($groupid);
			$groupUsersd = $groupobj->selectgroupmem($groupid,$this->_userid);

			if(count($groupUsersd[0]['Owner'])==0 && $groupUsers2[0]['User']!=$this->_userid)
			{
				$grdata = array(
						'GroupID' => $groupid,
						'Owner' => $groupUsers2[0]['User'],
						'User' => $this->_userid,
						'JoinDate' => $CurrDate,
						'SentBy' => 'Owner',
						'clientID' => clientID,
						'Status' => '1'
				);
				$groupobj->insertingroupmem($grdata); 
			}
		}
		

	if(!empty($groupid)){
		$groupdetail = $groupobj->grouprow($groupid);				
		$groupUsers = $groupobj->selectallgroupmem($groupid,$this->_userid);
		$groupUsers2 = $groupobj->selectallgroupmememail($groupid,$this->_userid);
				//if($groupdetail['GroupPrivacy']==2)
				//{
		if(count($groupUsers)>0)
			{
				foreach ($groupUsers as $value) {			  
					$this->notification->commomInsert('35','38',$last_insertedId,$value['User'],$this->_userid); // Insert for tagged user activity 
				}
						
				if($groupUsers[0]['Owner']!=$this->_userid)	$this->notification->commomInsert('35','38',$last_insertedId,$groupUsers[0]['Owner'],$this->_userid); // Insert for tagged user activity
			
				// Mail to all group member

				//if($insertdbeeobj->adminsetinggrpenable()==1){
					$ntest = $this->senmailgroupmem($groupUsers,$this->_userid,$dburl,$groupid,$groupname,$dbtitle);
				//}	

			}
		}	
		echo $success.'~'.'0'.'~'.$groupid.'~'.$last_insertedId.'~'.$keyword1.'~'.$insertleague.'~'.$groupname.'~'.$Adminapproved.'~'.$isgroupdb.'~'.count($groupUsersd[0]['Owner']).'~'.$dburl.'~'.$ntest.'~'.$insertdbeeobj->adminsetinggrpenable();
	}
	
	public  function facebookfriendsAction()
	{

		$data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
		if($this->session_array['Socialid'] && $this->session_array['Socialtype'] == 1 && $this->getRequest()->isXmlHttpRequest())
		{ 
				
			// facebook share code
			$config = Zend_Registry::get('config');
			$params = array(
				'appId' => $config->facebook->appid,
				'secret' => $config->facebook->secret,
				'domain' => $config->facebook->domain
			);
			
			$facebook = new Facebook($params);
			

			echo $accessToken = $facebook->getAccessToken();
			//$session = $facebook->getSession();
			echo $user_id = $facebook->getUser();

			try { 
						$friends = $facebook->api('/me/friends?access_token='.$accessToken);

						 foreach ($friends["data"] as $value) 
						 {
			
							$content .= "<div class='facebookfriendlist boxFlowers'>
							<label class='labelCheckbox'><input type='checkbox' value='".$value["id"]."' class='inviteuser-search' name='facebookuser'><div class='follower-box'>
							
							<img  class=img border height=30 align='left' src='https://graph.facebook.com/" . $value["id"] . "/picture'>
							<br>". $value["name"]."</div></label>
							</div></div>";  
						 }

						$data['status']  = 'success';
	           		    $data['content'] = $content;
					
					

			} catch(FacebookApiException $e) {
			
				echo $e->getType(); 
				echo $e->getMessage();
				exit;
			}   
	 	}else{

	 		$data['status']  = 'error';
            $data['content'] = "Some thing went wrong here please try again";
	 	} 

        return $response->setBody(Zend_Json::encode($data));
   }

	public  function answerpostingAction()
	{

		$data = array();
	    $this->_helper->layout()->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(true);
	    $response = $this->getResponse();
	    $response->setHeader('Content-type', 'application/json', true);
		if($this->getRequest()->getMethod() == 'POST' && $this->getRequest()->isXmlHttpRequest() && $this->_request->getPost('replytype') =='answer')
		{
			$filter = new Zend_Filter_StripTags();
			$data_array = array();
			$data_array['dbeeid'] = (int) $this->_request->getPost('dbid');
			$data_array['qna'] = $filter->filter($this->_request->getPost('answer'));
			$data_array['user_id'] = $this->_userid;
			$data_array['expert_id']  = (int) $this->_request->getPost('expert_id');
			$data_array['timestamp'] = date('Y-m-d H:i:s');
			$data_array['parentid'] = (int)$this->_request->getPost('parentid');
			$dbowners = (int)$this->_request->getPost('dbowners');
			$data_array['status'] = 1;
			$data_array['clientID'] = clientID;
			$expertArray = $this->myhome_obj->showDBeeExpert($data_array['dbeeid']);

			  if($expertArray['userid']!='')
			  {
				$commentOwerId = (int)$this->_request->getPost('cmntownerId');
				$dburl = $this->Myhome_Model->getdburltitle($data_array['dbeeid']); // dbee url from id
				$quesid = $this->Expert_Model->insertdbeeqna($data_array);  // insert value in expert q&a
				$this->notification->commomInsert(11,12,$data_array['dbeeid'],$this->_userid,$commentOwerId,'',$quesid);

				if($commentOwerId!=$dbowners)
					$this->notification->commomInsert(11,21,$data_array['dbeeid'],$this->_userid,$dbowners,'',$quesid);
				
				$user_result = $this->User_Model->getUserDetail($commentOwerId); // get user details
				$this->Expert_Model->updateAnswerStatus($data_array['parentid'],$this->_userid);
				$this->Expert_Model->updateQuestionsStatus($data_array['parentid'],$this->_userid);	
				$this->sendMailToUserByExpert($user_result,$this->session_array,$dburl); // send mail
				$data['status']  = 'success';
				$data['content'] = "your reply successfully posted";
			}else
			{
				$data['status']  = 'error';
				$data['content'] = "Sorry your question was not submitted as this expert has been removed.";
			}
	 	}else{
	 		$data['status']  = 'error';
	        $data['content'] = "Some thing went wrong here please try again";
	 	} 

	    return $response->setBody(Zend_Json::encode($data));
	}

	public function removeanswerAction()
	{
		$data = array();
	    $this->_helper->layout()->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(true);
	    $response = $this->getResponse();
	    $response->setHeader('Content-type', 'application/json', true);
		if($this->getRequest()->getMethod() == 'POST' && $this->getRequest()->isXmlHttpRequest())
		{
			$filter = new Zend_Filter_StripTags();

			$parentid = (int)$this->_request->getPost('parentid');
			$answer_ids = (int)$this->_request->getPost('answer_ids');
			
			$Expert    = new Application_Model_Expert();
			
			$Expert->deleteAnswer($this->_userid,$answer_ids);

			$Expert->UndoupdateAnswerStatus($parentid,$this->_userid);

			$data['status']  = 'success';
			$data['message'] = "Your answer has been deleted";

			
	 	}else{

	 		$data['status']  = 'error';
	        $data['content'] = "Some thing went wrong here please try again";
	 	} 

	    return $response->setBody(Zend_Json::encode($data));
	}

    public function linkedinPostToWall($data, $dburl)
    {
		
		$title = $data['Text'];
		if ($data['LinkPic'] != ''){
			$checkImage = new Application_Model_Commonfunctionality();
			$picprofile1 = $checkImage->checkImgExist($data['LinkPic'],'imageposts','default-avatar.jpg');
			$image = IMGPATH.'/imageposts/'.$picprofile1.'';
		}
		else if ($data['Pic'] != '')
		{
			$checkImage = new Application_Model_Commonfunctionality();
			$picprofile1 = $checkImage->checkImgExist($data['Pic'],'imageposts','default-avatar.jpg');
			$image = IMGPATH.'/imageposts/'.$picprofile1.'';
		}
		else if ($data['VidID'] != '')
			$image = 'https://i.ytimg.com/vi/'.$data['VidID'].'/0.jpg';
		
		$comment = '';

		$image = (empty($image)) ? BASE_URL.'/img/brandlogoFb.png' : $image;

		if($this->SocialLogo!='')
                $image = URL.'/img/'.$this->SocialLogo;

		$config['base_url']        = BASE_URL;
		$config['callback_url']    = BASE_URL . '/social/callback';
		$config['linkedin_access'] = linkedinAppid;
		$config['linkedin_secret'] = linkedinSecret;

	    # First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
        $linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], '');
        //$linkedin->debug = true;
        
        $linkedin_connect_data = Zend_Json::decode($this->session_data['linkedin_connect_data']);
        
        $linkedin->request_token             = unserialize($linkedin_connect_data['requestToken']);
        $linkedin->oauth_verifier            = $linkedin_connect_data['oauth_verifier'];
        $linkedin->access_token              = unserialize($linkedin_connect_data['oauth_access_token']);

		$apiCallStatus    =   $linkedin->share($comment, $title, $dburl, $image);
		simplexml_load_string($apiCallStatus);

    }

	public function facebookShareOnWall($data, $url)
	{
		$title = $data['Text'];
		$checkImage = new Application_Model_Commonfunctionality();
		if ($data['LinkPic'] != ''){
			$picprofile1 = $checkImage->checkImgExist($data['LinkPic'],'imageposts','default-avatar.jpg');
			$image = IMGPATH.'/imageposts/medium/'.$picprofile1.'';
		}
		else if ($data['Pic'] != '')
		{
			$picprofile1 = $checkImage->checkImgExist($data['Pic'],'imageposts','default-avatar.jpg');
			$image = IMGPATH.'/imageposts/medium/'.$picprofile1.'';
		}
		else if ($data['VidID'] != '')
			$image = 'https://i.ytimg.com/vi/'.$data['VidID'].'/0.jpg';
		
		$comment = 'I just started a debate on '.BASE_URL.'. Check it out by clicking below.';

		$image = (empty($image)) ? BASE_URL.'/img/brandlogoFb.png' : $image;

		if($this->SocialLogo!='')
                $image = URL.'/img/'.$this->SocialLogo;
				
		// facebook share code
		$params = array(
                        'appId' => facebookAppid,
                        'secret' => facebookSecret,
                        'domain' => facebookDomain
                    );
		
		$facebook = new Facebook($params);
		
		try { 
				$id = $this->facebook_connect_data['facebookid'];
				$ret_obj = $facebook->api('/'.$id.'/feed', 'POST',
					array(
					'link' => $url,
					'picture' => $image,
					'message' => $title,
					'description' => "".POST_NAME." a real-time 'debate & rate' social network enabling youto share views on any subject, in open community or private groups.",
					'access_token' => $getAccessToken
				));
		
		} catch(FacebookApiException $e) {
		
			echo $e->getType(); 
			echo $e->getMessage();
			exit;
		}   
	
	}

	
	
	public function twitterShareOnWall($data, $url)
	{
		$url = $this->shortUrl($url);
		$title = "I've posted on ".COMPANY_NAME.". Check it out.";
		$twitteroauth = new TwitterOAuth(twitterAppid, twitterSecret, $this->twitter_connect_data['twitter_access_token'], $this->twitter_connect_data['twitter_token_secret']);		
	    $twitteroauth->post('statuses/update', array('status' => $title.' '.$url));
	}

	public function twittermentionShareOnWall($data, $url, $mentionuser='')
	{
		
		$userTxt = '';
		if($mentionuser!="")
		{
		 $keyVal = $mentionuser;
		 $keyVal = explode(',', $keyVal);
		 
		 foreach ($keyVal as $key => $value) {
		 	 $value=str_replace('@', '', $value);
		 	 if(trim($value)!="")
		 	 {
		 	 	$userTxt.='@'.trim($value).' ';
		 	 }
		 }
	    }

	    $text         = str_replace('&nbsp;', ' ', $data['Text']);
	    $title 		  = substr($text,0,116);
	    $textLength	  = strlen($title);
	    $userTxtLength=0;

	    if($userTxt!="")
	    {
	    	$userTxtLength=strlen($userTxt);
	    	if($textLength > 116)
	    	$title = substr($title, 0, -$userTxtLength);
	    }
	    
        //118
	    //21

	    $title=strip_tags($userTxt)." ".strip_tags($title);

		
		$url         = BASE_URL .'/dbee/'.$data['dburl'].'?from=twitter';
        $url = $this->shortUrl($url);

		//$url = $this->shortUrl($url);
		//echo $title.' '.$url;
		//die;
		//$title = $userTxt." I've posted on ".COMPANY_NAME.". Check it out.";
		$twitteroauth = new TwitterOAuth(twitterAppid, twitterSecret, $this->twitter_connect_data['twitter_access_token'], $this->twitter_connect_data['twitter_token_secret']);		
	    $twitteroauth->post('statuses/update', array('status' => $title.' '.$url));
	}

	public function sendtweetnoteAction()
	{
		$request = $this->getRequest();
		$name = $request->getPost('name');
		$db = $request->getPost('db');
		$data['excerpt'] = $request->getPost('excerpt');
		$myhome_obj = new Application_Model_Myhome();
		$config = array(
			'consumerKey' => 'SxcXsv2jho6cQeoedVlNWg',
			'consumerSecret' => 'zinaqvA0GwvxQgeU8k0ooEE9HVstKCAcV5KWxFnE',
			'oauth_token' => '605315971-8DmQpvj0awi6UNzwj3DAkWLfpwTZfvMP0Ka2DDmy',
			'oauth_token_secret' => 'AjGh8yja8LLDM8KGNhe32ilAUpTzh3Dqn5dKdvvM'
		);
		$dbee_urltitles = $myhome_obj->getdburltitle($db);
		
		$twitteroauth = new TwitterOAuth($config['consumerKey'],
									 $config['consumerSecret'],
									 $config['oauth_token'], 
									 $config['oauth_token_secret']);
		
		$twitteroauth->post('statuses/update', array('status' => '@'.$name.' ALERT tweet referred at '.BASE_URL.'/dbee/'.$dbee_urltitles));
	}

	public function dbuploadAction()
	{
		$form=new Application_Form_Dbupload();
		$request = $this->getRequest();
		$realpath = $request->getPost('relPath');
		$formData = $request->getPost();
		$adapter = new Zend_File_Transfer_Adapter_Http();			
		$filecheck=$_FILES['filename']['name'];
		$ext = substr($filecheck, strrpos($filecheck, '.') + 1);
		
		//echo $ext;exit;
		if ($ext == "png") $picerror=true; else $picerror=false;			
		if(!$picerror) {
			
			$adapter->setDestination($_SERVER['DOCUMENT_ROOT'].'/'.$realpath);
			if ($adapter->receive($_FILES['filename']['name'])) {
				$this->view->upload_image=$_FILES['filename']['name'];
				$this->view->relPath=$realpath;
				
				$this->view->picerr=$error;
			}
		}
		else{
			$error = true;
			$this->view->picerr=$error;
		}
		
		$response = $this->_helper->layout->disableLayout();
		return $response;
	}
	public function file_get_contents_curl($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	public function check_matches($data, $arrayofneedles) {
		$arrayofneedles = array('vimeo','dailymotion','metacafe');
		foreach ($arrayofneedles as $needle) {
			if (stripos($data, $needle)!==FALSE) {
				return true;
			}
		}
		return false;
	}
	//Add http in link
	public function check_website($site_url){
		$SiteURL=$site_url;
		$sub_site_url=substr(trim($SiteURL), 0, 4);
		if($sub_site_url!='http') $SiteURL='http://'.$SiteURL;
		return $SiteURL;
	}
	public function linkdetailAction(){
		
		$myhome = new Application_Model_Myhome();
		$request = $this->getRequest();
		$url= $request->getpost('dbeeurl');
		$urltt = $url;

		if($myhome->getrestrictedurl($url)){
			$return=-2; 
		}else{			
			if ($this->check_matches($url, $array_of_needles)) 
				$isvideo=true;
			else 
				$isvideo=false;

			if(!$isvideo) {
				$html = $this->file_get_contents_curl($url);
				$doc = new DOMDocument();
				@$doc->loadHTML($html);
				$nodes = $doc->getElementsByTagName('title');
				//get and display what you need:
				$title = $nodes->item(0)->nodeValue;
				$metas = $doc->getElementsByTagName('meta');
				for ($i = 0; $i < $metas->length; $i++)
				{
					$meta = $metas->item($i);
					if($meta->getAttribute('name') == 'description' || $meta->getAttribute('name') == 'Description')
						$description = $meta->getAttribute('content');
					if($meta->getAttribute('name') == 'keywords')
						$keywords = $meta->getAttribute('content');
				}
				
				$LinkPic =  '';
	            //$picprofile5 = $this->commonmodel_obj->checkImgExist($LinkPic,'results','linkimage.png');
				if(!$err) {
					$return='<div class="makelinkWrp">
					<div class="removeCircle" id="closeLinkUrl">
						<span class="fa-stack">
						  <i class="fa fa-circle fa-stack-2x"></i>
						  <i class="fa fa-times fa-stack-1x fa-inverse"></i>
						</span>
					</div>
					<div class="makelinkDes otherlinkdis" style="margin-left:0px;"><h2>'.$title.'</h2>';
					if($description!='')
						$return.='<div class="desc">'.$description.'</div>';
					$return.='<div class="makelinkshw"><a href="'.$url.'" target="_blank">'.substr($url ,0,50).'</a></div>';
					$return.='<input type="hidden" id="LinkPic" value="'.$LinkPic.'"><input type="hidden" id="LinkTitle" value="'.$title.'"><input type="hidden" id="LinkDesc" value="'.$description.'"></div></div>';
				}
				else $return=-1;
			}
			
			else $return=-1;
		}
		echo $return;
		$response = $this->_helper->layout->disableLayout();
		return $response;
	}
	
	
	public function snapshotAction(){
		
	
	require_once 'snapshot/GrabzItClient.class.php';
	
	$url = 'http://google.com';
	
	$appkey = "ZDY2YmY1MGFlMzFiNDVhMmIxYTIxZTc5NjNhNmI0MmE";
	$appsecret = "YHY/bz8/GD9rPyE/Mz8/NT9OXz9ZPzQOPz8/Pz9NPy0";
	
	$grabzIt = new GrabzItClient($appkey, $appsecret);
	
	$id = $grabzIt->SetImageOptions($url);
	
	$result = $grabzIt->GetPicture($id);
	
	$filepath = "/images/teeest.jpg";
	$ff = $grabzIt->SaveTo($filepath);
	
	$grabzIt->GetStatus($id);
	
	//wait a certain amount of time
	
	//$result = $grabzIt->GetPicture($id);
	
	
	Zend_Debug::dump($ff);
	
	$response = $this->_helper->layout->disableLayout();
	
	return $response;
	
	}
	
	
	public function convertrss2Action()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$request = $this->getRequest();
		$response->setHeader('Content-type', 'application/json', true);
	
		if ($this->getRequest()->getMethod() == 'POST')
		{
	
			$usersiteobj  = new Application_Model_Usersite();
			$usersite = $usersiteobj->getsite($this->_userid);
			//print_r($usersite);
			$siteid = (int)$request->getpost('id');
			if(empty($siteid)){
				$siteid = $usersite[0]['ID'];
			}
			//echo '<br><br>siteid: '.$siteid.'<br><br>site: ';

			$site_obj  = new Application_Model_Rss();
			if(!empty($siteid)){
			$site = $site_obj->getsitename($siteid);
			//print_r($site); exit();
			$sitename =$site['Name'];
			$siteurl =$site['URL'];

			try {			
				$feedLinks = Zend_Feed_Reader::import($siteurl);
			} catch (Exception $e) {
				// oops
				$e->getMessage();
			}
			
			$count=1;
			$i=0;
			if(is_object($feedLinks)){
				foreach ($feedLinks as $item) {
					$datav[$i]['date'] =''.$item->getDateModified().'';
					$datav[$i]['title'] = $item->getTitle();
					$datav[$i]['dis'] = strip_tags($item->getDescription());
					$datav[$i]['postname'] = ucfirst(POST_NAME);
					$datav[$i]['hlink'] = $item->getLink();
					$count++;
					$i++;
				}
				$data['status'] = 'success';
			}else{
					$datav['nofeed'] =1;
					$datav['text'] ='Unable to load feed';
			}
			$usersiteobj  = new Application_Model_Usersite();
			$usersite = $usersiteobj->getsite($this->_userid);
			
			$totalrsssite = $usersiteobj->getsitetotal();
			if($totalrsssite==0||$totalrsssite==''){
				$nofeed = 1;
			}

			$data['totalrss'] = $usersite;
			$data['totalsitecnt'] = $totalrsssite;
			
			$data['content'] = $datav;	
			$data['nofeed'] = $nofeed;
		}else{
			$data['nofeed'] = 1;
			$data['totalrss'] = $usersite;
			$data['totalsitecnt'] = $totalrsssite;
			
			$data['content'] = $datav;	
		}
		}
		else{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
			$data['nofeed'] = false;
		}
		
		return $response->setBody(Zend_Json::encode($data));
		exit;
	}
	
	
	public function dbeerssAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
			$site_obj  = new Application_Model_Rss();
			$userrss = $site_obj->gettotaluserrss($this->_userid);
			if($userrss=='')
				$userrss=$site_obj->getactive();
			$siterss = $site_obj->getsite();
			
	$content .= '<div class="user-name" style="margin-bottom:10px; font-size:18px;">Customise your RSS feeds below<br /><span style="font-size:12px; font-weight:normal; color:#999999"></span></div>
	<div class="next-line"></div>
	<div class="itemGuideMark"><span class="rssdeactiveMark"></span>RSS inactive <a href="#" rel="dbTip" Title="Feeds disabled by the platform admin. Once deselected, these will not be available to select again."><i class="fa fa-question-circle"></i></a></div>
	<div id="msg" class="rssErrormsg" style="display:none">You can select a maximum of 4</div>
	<div class="next-line"></div>
	<div>';	
	$counter=1;
	$labelcounter=1;
	$dnt = array();	
	foreach($siterss as $typeRow){
		$act = $typeRow['Active'];
		$classdeactive = "";
		$chkdesable = "";
		$crt = '';
		$classhide = "";
	if(in_array($typeRow['ID'],$userrss)) { 
		if(trim($act==1)){
		$checked='checked="checked"';
		 $color="color:#CC0000";		
		  $chkdesable = '';
		  $crt = '';
		 $rsslabel='<span class="rssfeedspopIcon"><i class="fa fa-rss fa-lg"></i> '.$labelcounter.'</span>'; $labelcounter++;
		}else{
		 $checked='checked="checked"';
		 $color="";	
		 $color="color:#CC0000";		
		 $rsslabel='<span class="rssfeedspopIcon"><i class="fa fa-rss fa-lg"></i> '.$labelcounter.'</span>'; $labelcounter++;
		 $crt = '<i class="fa fa-ban textchk-danger metroIcon"></i>';
		
		 $classdeactive = "rssdeactive";
		}
	 } else { 
	 	if($act==0){	 	
		 $color="";		
		 $checked='';
		 $chkdesable = "disabled=disabled";		
		 $classhide = "chkfeedhide";
		 $rsslabel = '';
		
		 $crt = '<i class="fa fa-ban textchk-danger"></i>';		 
		 $classdeactive = "rssdeactive";		 
	 	}else{
	 		$chkdesable = "";
	 		$checked=''; $color=''; $rsslabel=''; 
	 	}
	 }
	
	if(!in_array($typeRow['cname'],$dnt) && ($typeRow['Active']=='1' || $typeRow['isdefault']=='1' || in_array($typeRow['ID'],$userrss))){
	array_push($dnt,$typeRow['cname']);
	
	$content .= "<div class='next-line'></div></div><div class='rssfeedLft'>
		<div class='heading-bold'></div>";				
	}
	$content .='<span></span>
<label class="labelCheckbox '.$classdeactive.' '.$classhide.'" for="rssIn_'.$typeRow['ID'].'">'.$crt.'<input type="checkbox" '.$chkdesable.' class="check" value="'.$typeRow['ID'].'"'.$checked.'   id="rssIn_'.$typeRow['ID'].'"><label class="checkbox"></label>'.$typeRow['Name'].'</label><span id="label'.$typeRow['ID'].'" style="'.$color.'">'.$rsslabel.'</span><div style="height:1px"></div>';
	
	if($counter=='4')
    {				
        $content .='<div class="next-line"></div>';
        $counter=1;
	} else $counter++;	
	
	}
	$content .='<input type="hidden" id="rss-sites" value="'.implode($userrss,',').'"><input type="hidden" id="rss-sites1" value="'.implode($userrss,',').'"></div>';
	
			$data['status'] = 'success';
			$data['content'] = $content;
	}
		else{
				$data['status'] = 'error';
				$data['message'] = 'Some thing went wrong here please try again';	
		}
		return $response->setBody(Zend_Json::encode($data));
       	
	}
	
	public function savesiteAction()
	{
	  	$mydata = array();
	  	
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$requestval = $this->getRequest()->getParams();
			$request = $this->getRequest();
			$siteextract = $request->getpost('sites');
			$siteextract = $request->getpost('sites');		
			$userid = (int)$this->_userid;
			$site_obj  = new Application_Model_Rss;
			$success = $site_obj->deleterss($userid);
			$siteextract = explode(',',$siteextract);

			foreach($siteextract as $chk):			
				$data = '';
				$data['Site'] = $chk;
				$data['User'] = $userid;
				$data['clientID'] = clientID;
				$site_obj  = new Application_Model_Rss;
				$success = $site_obj->saveusersite($data);			
			endforeach;
			
			$datareload = $site_obj->getusersite($userid);	
			
			$mydata['id'] = $datareload[0]['ID'];
			$mydata['site'] = $datareload[0]['URL'];
			$mydata['name'] = $datareload[0]['Name'];
			$mydata['logo'] = $datareload[0]['Logo'];
			$SubmitMsg = 1;
		}else{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}		
		
		return $response->setBody(Zend_Json::encode($mydata));		
		
	}
	public function fetchiconAction()
	{
		$user = $this->_userid;
		$usersite  = new Application_Model_Usersite();
		$data = $usersite->getsiteRss($user);
		$this->view->rssurl = $data;
		$response = $this->_helper->layout->disableLayout();
		return $response;
	}
	public function redbeeAction()
	{
		//$userid = $this->_userid;
		$request = $this->getRequest();
		$userid = $request->getPost('user',$this->_userid);
		$start = (int)$request->getPost('start',0);
		$end = (int)$request->getPost('end',5);
		$redbee_obj  = new Application_Model_Myhome();
		$this->view->tblredbdbees = $redbee_obj->gettblredbdbee($start,$end,$userid);
		$this->view->startnew = $start+PAGE_NUM;
		$this->view->dbeenotavailmsg = $request->getPost('dbeenotavailmsg');		
		$response = $this->_helper->layout->disableLayout();
		return $response;
	}
	public function feeoptiongroupAction()
	{
		$request = $this->getRequest();
		$user = (int)$request->getPost('user');
		$grpmyhome = new Application_Model_Myhome();
		$data = $grpmyhome->getfeeoptiongroup($user);
		$this->view->grpOwnerRow = $data;
		$this->view->cookieuser=$this->_userid;
		$this->view->user=$this->_getParam('id');
		$response = $this->_helper->layout->disableLayout();
		return $response;
	}
	 

	public function logoutAction()
	{
		
		//die('sjhadhjahjsdjh');
		$request = $this->getRequest()->getParams();
		
		if($request['user']!='' && $request['clearcache']==1)
		{
		 	$this->getResponse()->setHeader('Cache-Control','no-store, no-cache, must-revalidate, post-check=0, pre-check=0',1);
	        $this->getResponse()->setHeader('Expires','Thu, 19 Nov 1981 08:52:00 GMT',1);
	        $this->getResponse()->setHeader('Pragma','no-cache',1);

	        $myhome_obj  = new Application_Model_Myhome();
	        $data = array('newfeatures'=>0);
            $sucess = $this->myclientdetails->updatedata_global('tblUsers',$data,'UserID',$this->_userid);
			
		}

		$this->myclientdetails->updatedata_global('tblUsers',array('chatstatus'=>0,'isonline'=>0),'UserID',$this->_userid);

		$this->myclientdetails->updatedata_global('tbluserlogindetails',array('logoutdate'=>date("Y-m-d H:i:s")),'userid',$this->_userid);
		
		$redirection_groupname_space = new Zend_Session_Namespace('Group_Session');
		
		if (isset($_SERVER['HTTP_COOKIE']))
		{
			$cookies = explode(';', $_SERVER['HTTP_COOKIE']);
			foreach($cookies as $cookie) 
			{
				$parts = explode('=', $cookie);
				$name = trim($parts[0]);
				setcookie($name, '', time()-1000, '/','onserro.com');
			}
		}

		if(isSet($_COOKIE['RememberEmail']) && $_COOKIE['RememberEmail']!="" && isSet($_COOKIE['Rememberpass']) && $_COOKIE['Rememberpass']!="")
		{
		    setcookie("RememberEmail","", time()- 3600, '/');
		    setcookie("Rememberpass","", time()- 3600, '/');
		}

		Zend_Auth::getInstance()->clearIdentity();
		if(isset($this->session_name_space->redirectToExpertPage) && $this->session_name_space->redirectToExpertPage!='')
			$this->_redirect($this->session_name_space->redirectToExpertPage);
		
		if(isset($this->session_name_space->redirectToExpertPage) && $this->session_name_space->redirectToExpertPage!='')
			$this->_redirect($this->session_name_space->redirectToExpertPage);
		
		if(isset($this->session_name_space->redirection) && $this->session_name_space->redirection!='')
			$this->_redirect($this->session_name_space->redirection);

		Zend_Session::namespaceUnset('User_AllowTC');
		unset($user_allowTC_session->allowTC);
		
		$this->session_name_space->unsetAll();
		$this->session_name_space->access_token = '';
		Zend_Session::namespaceUnset('application');
		$authNamespace = new Zend_Session_NameSpace('identify');

		$authNamespace->unsetAll();
		
		session_destroy();
		session_unset();
		//die('sd');
		if(clientID==19)
		{
			$this->_redirect('https://dev04-web-onlinegolf.demandware.net/s/AmericanGolf-GB/logout?url=https://dev04-web-onlinegolf.demandware.net/s/AmericanGolf-GB');
			
		} else $this->_redirect('/');

		echo 1; exit;
		/********************facebook logout *********************/
		 
	}
	public function searchmainAction()
	{
		$myhomesearch = new Application_Model_Myhome();
		$q = (String)strtolower($this->_getParam('q'));
		$q = str_replace("[","",$q);
		$q = str_replace("]","",$q);
		$user_type=$this->session_data['usertype'];
		$this->view->dbeedata = $myhomesearch->getsearchdbee($q,5,$user_type);
		$this->view->userdata = $myhomesearch->getsearchuser($q,5,$user_type,$this->admin_searchable_frontend);
		$this->view->TotalDbees = $myhomesearch->getsearchdbeecnt($q,$user_type);
		$this->view->TotalUser =  $myhomesearch->getsearchusercnt($q,$user_type,$this->admin_searchable_frontend);
		$this->view->q = $q;
		$this->_helper->layout->disableLayout();
	
	}
	public function searchAction()
	{
		$request = $this->getRequest()->getParams();
		if($request['searchword']==''){
            $url='/myhome';
			$this->_redirect($url);
		}
		$request = $this->getRequest();
		$searchtype = $this->getRequest()->getPost('searchtype');
	    $searchid = $this->getRequest()->getPost('searchid');		
		$searchword = $request->getpost('searchword');			
		if($searchid!='') {
			$searchArr=explode('~',$searchid);
			if($searchArr[0]=='1') {
				$this->_redirect('dbee/'.$searchArr[1]);
			}
			elseif($searchArr[0]=='2') {
				$this->_redirect('/user/'.$searchArr[1]);
			} 
		}
		else {
			$searchword=$_POST['searchword'];
			$url='/myhome/serchresult/q/'.$searchword;
			$this->_redirect($url);
		}
		$response = $this->_helper->layout->disableLayout();
		return $response;
	}
	
	public function serchresultAction()
	{
		$request = $this->getRequest()->getParams();
		$user_type=$this->session_data['usertype'];

		$myhomesearch = new Application_Model_Myhome();
		$limit = 40;
		$AlphaChar='';
		if($this->_request->getPost('AlphaChar'))
		{
			$AlphaChar=$this->_request->getPost('AlphaChar');
			echo $this->view->$AlphaChar = $AlphaChar;
			die;
		}
		$q = (String)strtolower($this->_getParam('q'));
		$q = str_replace("[","",$q);
		$q = str_replace("]","",$q);
		$this->view->dbeedata = $myhomesearch->getsearchdbee($q,$limit);
		$this->view->userdata = $myhomesearch->getsearchuser($q,$limit,$user_type);
		$this->view->q = $q;
		$user = $this->_userid;		
		$cat  = new Application_Model_Category();
		$usersite  = new Application_Model_Usersite();
		$myhome_obj  = new Application_Model_Myhome();
		$row = $myhome_obj->getrowuser($user);
		$user_model = new Application_Model_DbUser(); // get model object
		$usersite  = new Application_Model_Usersite();
		$this->view->cat = $cat->getallcategory();
		$result = $user_model->ausersocialdetail($this->_userid);
		$this->view->userResult = $result[0];
		$this->view->dbeeuser = $user;
		$this->view->n = $n;
		$this->view->ProfilePic = $row['ProfilePic'];
		$this->view->Name = $row['Name'];
		$this->view->ShowPPBox = $row['ShowPPBox'];
		$this->view->userid=$this->session_data['UserID'];
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
                $sortby 	  = $this->_request->getPost('sortby');
                $searchMember = $this->_request->getPost('searchMember');
                $UserCount = $this->_request->getPost('UserCount');
                
                $lastId = $this->_request->getPost('ID');
                $lastId = ($lastId=='')? '' : $lastId;
				$letters = range('a', 'z');
				$alphabets=array();
				foreach($letters as $letter)
				{
					$encodeletter = $this->myclientdetails->customEncoding($letter,'allusersAlphabat'); 
					$alphaRow=$this->myclientdetails->getRowHavingMasterfromtable('tblUsers',array('DISTINCT SUBSTRING(Name, 1, 1) AS firstletter'),array('firstletter'=>$encodeletter),array('Name'=>'ASC'));
					$alphabets[]=$alphaRow;
				}
                $alphabets = array_filter($alphabets);

                $searchMember = $this->myclientdetails->customEncoding($searchMember,'allusersAlphabat');

                $filter = new Zend_Filter_StripTags();
			    $searchMember =$filter->filter(addslashes($searchMember));

				$sort=array('UserID'=>'DESC');
				$memberSortAlpha=0;
				$totalSearchHtml ='';
				$page=2;

				$sortby = $this->myclientdetails->customEncoding($sortby,'allusersAlphabat');
				$return.='<div id="middleWrpBox">';
				
				if($lastId!="")
				{
					$IsSearchText = $this->_request->getPost('IsSearchText');
					$IsSearchText = $this->myclientdetails->customEncoding($IsSearchText,'allusersAlphabat');
					$IsSortBy = $this->_request->getPost('IsSortBy');
					$IsSortBy = $this->myclientdetails->customEncoding($IsSortBy,'allusersAlphabat');	
					$users = $this->User_Model->SearchMemberPaging($lastId,$IsSearchText,$IsSortBy,$user_type);
				}
				else if($searchMember!="")
				{
					
					$activeclass='';
				    $activeclass_latest='active';
					$memberSortAlpha=0;
				    $users = $this->User_Model->SearchMember($searchMember,'',$user_type);
					$totalSearchHtml = '<div usercountfilter="true" class="Usercountfilter srcUsrtotal" id="Usercountfilter" style="display:block">'.sizeof($users).'<i>
										total <br>user</i>
										</div>';
					$return.='<input type="hidden" name="SearchUser" class="SearchUser" value="'.$searchMember.'">';
					$return.='<input type="hidden" name="SearchMemberTextScroll" id="SearchMemberTextScroll" value="1">';
				}
				else if($sortby!="")
				{
					
                    $activeclass='active';
				    $activeclass_latest='';
					$SearchUserTextXX = $this->_request->getPost('SearchUserTextXX');
					$SearchUserTextXX = $this->myclientdetails->customEncoding($SearchUserTextXX,'allusersAlphabat');
					$users = $this->User_Model->userDirectory($sortby,$SearchUserTextXX,$user_type);
					$return.='<input type="hidden" name="SearchUser" class="SearchUser" value="'.$SearchUserTextXX.'">';
					$return.='<input type="hidden" name="SortByScroll" id="SortByScroll" value="1">';
					$display='block';
					$memberSortAlpha=1;
					
				}else
				{
					$activeclass='';
					$activeclass_latest='active';
					$orderby = $this->_request->getPost('orderby');
					$SearchUserTextXX = $this->_request->getPost('SearchUserTextXX');
					$SearchUserTextXX = $this->myclientdetails->customEncoding($SearchUserTextXX,'allusersAlphabat');
					
					$return.='<input type="hidden" name="SearchUser" class="SearchUser" value="'.$searchMember.'">';
                 	
                 	if($orderby=="")
                 	{
                 		if($user_type==0)
                 		{
                 		
                 		 $users=$this->myclientdetails->getAllMasterfromtable('tblUsers',array('UserID','Name','ProfilePic','Username','usertype'),array('Status'=>1,'usertype'=>$user_type),$sort,$limit);           	
                 		}
                 		else
                 		{
                 		 $users=$this->myclientdetails->getAllMasterfromtable('tblUsers',array('UserID','Name','ProfilePic','Username','usertype'),array('Status'=>1),$sort,$limit);           	
                 		}
                 	}
                 	else
                 	{
                 	  $users = $this->User_Model->SearchMember($SearchUserTextXX,$orderby,$user_type);
                 	  $display='none';
                 	}
				}


				if($searchMember!="" || $SearchUserTextXX!="")
				{
					unset($alphabets);

					$SearchUserTextXX = $this->_request->getPost('SearchUserTextXX');
					$SearchUserTextXX = $this->myclientdetails->customEncoding($SearchUserTextXX,'allusersAlphabat');
										
					if($searchMember!="")
						$users_xx = $this->User_Model->SearchMember($searchMember,'',$user_type);
				    
				    if($SearchUserTextXX!="")
						$users_xx = $this->User_Model->SearchMember($SearchUserTextXX,'',$user_type);
				    
					foreach($users_xx as $usr):
					$substr=substr($usr['Name'],0,1);							
					$alphaRow=$this->myclientdetails->getRowHavingMasterfromtable('tblUsers',array('DISTINCT SUBSTRING(Name, 1, 1) AS firstletter'),array('firstletter'=>$substr),array('Name'=>'ASC'));
                	$alphabets[]=$alphaRow;
                	endforeach;
                	if(sizeof($alphabets)>0)
                		$alphabets = array_map("unserialize", array_unique(array_map("serialize", $alphabets)));
                    
                	
				}

				$res = null;
				if(count($alphabets)>0)
				{	
					foreach ($alphabets as $v) {
					    if ($v !== null) {
					        $res = $v;
					        break;
					    }
					}
				}
				$alphabet=$alphabets;

				$first=$this->myclientdetails->customDecoding($res[firstletter]);
				
				if($IsSearchText!="1" || $sortby=="" || $searchMember=="")
				{
					$return.='<input type="hidden" name="SearchMemberScroll" id="SearchMemberScroll" value="1">';
					$return.='<h3 class="pageTitle">Platform Members</h3>';
					$return.='<div class="searchHeaderAllUser">';
					$return.='<div class="srcUsrWrapper">
								<div class="fa fa-search fa-lg searchIcon2"></div>
								<input type="hidden" name="page" id="page" value="2">
								<input type="text" name="searchMember" id="searchMember" placeholder="search users" value="">
								'.$totalSearchHtml.'
							</div>';
						
					$return.='<ul id="searchUserAllMenu">
								  <li><a href="javascript:void(0);" class="'.$activeclass_latest.'" id="allLatestUser" data-xx="noload">Latest registed</a></li>

								  <li><a href="javascript:void(0);" class="SortAlphabet '.$activeclass.'" data-xx="alphabetically" data-char="'.$first.'">Sort alphabetically</a></li>
								  <li><a href="javascript:void(0);"  data-xx="viewAll">View All</a></li>
							  </ul>';

					$style='';

					if(count($alphabet)>0)
					 { 
					 	if($memberSortAlpha==1)
					 	{
						$return.='<span id="MemberSortAlphbet" data-char="'.$first.'">';
						 	$return.='<ul>';

						 	foreach($alphabet as $char):
							 	 if($this->_request->getPost('sortby')==$this->myclientdetails->customDecoding($char['firstletter']))
							 	  		$style='active';
							      else
							       	$style='';
							     $return.='&nbsp;&nbsp;<li><a href="javascript:void(0);" class="SortAlphabet '.$style.'" data-char="'.$this->myclientdetails->customDecoding($char['firstletter']).'"  data-xx="alphabetically">'.ucfirst($this->myclientdetails->customDecoding($char['firstletter'])).'</a></li>';
						 	endforeach;
						 	$return.='</ul>';
						 	$return.='</span>';
					 	}
					 }
				
				}    
				$return.='</div>';
				$return.='<ul class="searchMemberList">';
				
				if($lastId!="")
					$return="";
				
				if(count($users)>0)
					$data['page'] = $lastId+1;
				else
					$data['page'] = $lastId;	
				
				if(count($users)>0) 
				{

					
					$counter=1;
					foreach($users as $Row):
					    $checkImage = new Application_Model_Commonfunctionality();
						$type ='';
						if((int)$Row['usertype']!=0)
						 	$type = $checkImage->checkuserTypetooltip($Row['usertype']); 
						 	$userprofile1 = $checkImage->checkImgExist($Row['ProfilePic'],'userpics','default-avatar.jpg');	
						$return.='<li class="usrList" id="'.$Row['UserID'].'"><a href="'.BASE_URL.'/user/'.$this->myclientdetails->customDecoding($Row['Username']).'" rel="dbTip" title="'.$type.'">						
									<img src="'.IMGPATH.'/users/medium/'.$userprofile1.'" width="90" height="90" border="0" />
									<div class="membersName  oneline">'.$this->myclientdetails->customDecoding($Row['Name']).'</div>
								  </a></li>';
						if($counter%4==0) $return.='';
						$counter++;
					endforeach;
					if($lastId=="")
						$return.='</ul>';
				  $return.='<input type="hidden" name="TextUserCount" id="UserCount" value="'.count($users).'">';
					if($UserCount >= $limit && $lastId==2)
					{
				   		 $return.='<div id="last_msg_loader" class="clear"></div>';
				    }else{
				    	$return.='<div class="clear"></div>';
				    }
				    
				}
				else if($lastId=="")	
					 $return.='<div class="noFound" style="margin-top:50px;">No user found !.</div>';
				
				if($lastId=="")
					$return.='</div></div>';

				$data['status'] = 'success';
				$data['content']= $return;
			}
    		return $response->setBody(Zend_Json::encode($data));
    	}

	

	public function hashtagAction()
	{
		$myhomesearch = new Application_Model_Myhome();
		$limit = 500;
		$userid=$this->_userid;
		$q = (String)strtolower($this->_getParam('tag'));
		$this->view->dbeedata = $myhomesearch->getsearchHashTagdbee($q,$limit);
		$this->view->commentDbeeData = $myhomesearch->getCommentsearchHashTagdbee($q,$limit);
		$this->view->q = $q;
		$this->view->userid = $userid;
	}
	
	public function searchtwitterAction()
	{
		$request = $this->getRequest();
		$page = 1;
		$searchtype = $this->getRequest()->getPost('q');
		$stoptweetreply = (int)($this->getRequest()->getPost('stoptweetreply'));
		$tweetnum = (int)($this->getRequest()->getPost('tweetnum'));
		$profileholder=(int)($this->getRequest()->getPost('profileholder'));
		$return='';
		$addtoreply=true;
		$divtitle='';
		if($stoptweetreply=='1') {
			if(!$profileholder) {
				$addtoreply=false;
				$divtitle="title='Administrator Restricted'";
			}
		}
		$twitter_search = new Zend_Service_Twitter_Search('json');
		$search_results = $twitter_search->search('love', array('lang' => 'en','rpp' => '5','page' => $page,'show_user' => 'true'));
		$this->view->search_results =$search_results;
		$this->view->divtitle = $divtitle;
		$response = $this->_helper->layout->disableLayout();
		return $response;
	}
	public function testactivityAction()
	{
		$myhomesearch = new Application_Model_Myhome();
		$myhomesearch->addactivities(1, 23, 4, date('Y-m-d H:i:s'));
		return $response;
	}
	public function Usrall($dbeeid)
	{
			$myhome_obj = new Application_Model_Myhome();
			return  $myhome_obj->getrowuser($dbeeid);			
			
	}
	public function newcommpopAction()
	{
		
		$data = array();
		$content ='';
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
		$cookieuser=$this->_userid;
		$request = $this->getRequest();
		$CheckDateComments = $request->getCookie('currloginlastseencomments');
		$seencomms=$request->getCookie('seencomms');
		$dbs='';
		$myhomecommpop = new Application_Model_Myhome();
		$NotificationRowdata = new Application_Model_Notification();
		$dbRes=$myhomecommpop->dbeeusernotifi($cookieuser);
		if(count($dbRes)>0)
		{
			$comments = $NotificationRowdata->chknewcomment($seencomms,$CheckDateComments,$dbRes,$this->_userid);
			setcookie('currloginlastseencomments', date('Y-m-d H:i:s'), 0, '/');
		}
		
		foreach($comments as $TCRow)
		{
		$dbeeRes=$myhomecommpop->getfieldsfromtable(array('Type','Text','LinkTitle','PicDesc','VidDesc'),'tblDbees','DbeeID',$TCRow['DbeeID']);
		if($dbeeRes[0]['Type']=='1')     $dbFormate=substr($dbeeRes[0]['Text'],0,100);
		elseif($dbeeRes[0]['Type']=='2') $dbFormate=substr($dbeeRes[0]['LinkTitle'],0,100);
		elseif($dbeeRes[0]['Type']=='3') $dbFormate=substr($dbeeRes[0]['PicDesc'],0,100);
		elseif($dbeeRes[0]['Type']=='4') $dbFormate=substr($dbeeRes[0]['VidDesc'],0,100);
		
		if($TCRow['Type']=='1') 	$dbText=substr($TCRow['Comment'],0,100);
		elseif($TCRow['Type']=='2') $dbText=substr($TCRow['LinkTitle'],0,100);
		elseif($TCRow['Type']=='3') $dbText=substr($TCRow['PicDesc'],0,100);
		elseif($TCRow['Type']=='4') $dbText=substr($TCRow['VidDesc'],0,100);
		
		$content.='<div class="cmntnote-'.$TCRow['DbeeID'].'">
				<div class="newcommentnote" onClick="javascript:gotodb('.$TCRow['DbeeID'].','.$TCRow['CommentID'].')" title="go to db">
					<div style="padding:5px;">';
						$ownerdetails=$this->usrall($TCRow['DbeeOwner']);
		                $checkImage = new Application_Model_Commonfunctionality();
                        $picprofile6 = $checkImage->checkImgExist($ownerdetails['ProfilePic'],'userpics','default-avatar.jpg');
						$content.='<div style="float:left; width:70px;"><img src="'.IMGPATH.'/users/small/'.$picprofile6.'" width="50" height="50" border="0" /></div>
						<div style="float:left;">
							<div style="font-weight:bold">db creator: '.$ownerdetails['Name'].'</div>
                            <div style="margin-top:5px">'.$dbFormate.'</div>
						</div>
					</div>
					<div class="next-line"></div>
                    <br>
					<div style="padding:5px; margin-top:10px;">';
                       
						$userdetails=$this->usrall($TCRow['UserID']);
						$checkImage = new Application_Model_Commonfunctionality();
                        $picprofile7 = $checkImage->checkImgExist($userdetails['ProfilePic'],'userpics','default-avatar.jpg');
						$content.='<div style="float:left; width:70px;"><img src="'.IMGPATH.'/users/small/'.$picprofile7.'" width="50" height="50"  border="0" /></div>
						<div style="float:left; width:450px;">
							<div style="font-weight:bold">NEW COMMENT</div>
							<div style="margin-top:5px">'.$dbText.'</div>
						</div>
						<br style="clear:both">
					</div>
				</div>
				<div class="next-line"></div>';
				
				$content.='<div align="right" style="margin-top:10px; cursor:pointer;" onClick="javascript:commentnotify('.$TCRow['DbeeID'].',2)">[x stop notifications on this '.POST_NAME.']</div>';
				$content.='<div class="next-line"></div>
				<div style="width:auto; height:1px; background-color:#CCC; margin:10px 0 10px 0"></div>
			</div>';
		}
		
	
				$data['status'] = 'success';
				$data['content'] = $content;	
			}
		return $response->setBody(Zend_Json::encode($data));
	}
	public function uploadprofilepicAction()
	{
		if($this->_getParam('currpic')== 1)
			$pic = 'default-avatar.jpg';
		$myhome_obj = new Application_Model_Myhome();
		$this->view->currpic = $pic;
		$this->view->username = $myhome_obj->getusername($this->_userid);
		$this->view->user = $this->_userid;
		$response = $this->_helper->layout->disableLayout();
		return $response;
	}
	
	public function groupnotificationAction()
	{
		$request = $this->getRequest();
		$data = array();
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		$data['TotalGroups']=0;
		$cookieuser = $this->_userid;
	
		$request = $this->getRequest();
		$CheckDateGroups=$request->getCookie('currloginlastseengroup', 'default');
		$NotificationRowdata = new Application_Model_Notification();
		$FollowingTable = new Application_Model_Following();
		$NotificationRow = $NotificationRowdata->getnotificationuser($this->_userid);		
		$users = $FollowingTable->getfolloweruserbyid($this->_userid);	
		// CALCUATE TOTAL GROUP INVITATIONS SINCE LAST LOGIN/LAST SEEN
		if($NotificationRow['Groups']=='1') {
			if($CheckDateGroups!='') {
				$TGIRow= $NotificationRowdata->getgroupmember($cookieuser,$CheckDateGroups);
				
				$data['TGIRow'] = $TGIRow['Name'].' '.$TGIRow['lname'];
				$data['TotalGroupInvite']=$NotificationRowdata->getgroupmembercnt($cookieuser,$CheckDateGroups);
			}
			else $data['TotalGroupInvite']=0;
			// CALCUATE TOTAL GROUP JOIN REQUESTS SINCE LAST LOGIN/LAST SEEN
			if($CheckDateGroups!='') {
				$TGRRow=$NotificationRowdata->getjoingroup($cookieuser,$CheckDateGroups);
				$data['TGRRow'] = $TGRRow['Name'].' '.$TGRRow['lname'];
				$data['TotalGroupRequests']=$NotificationRowdata->getjoingroupcnt($cookieuser,$CheckDateGroups);
			}
			else $data['TotalGroupRequests']=0;
			$data['TotalGroups']=$data['TotalGroupInvite']+$data['TotalGroupRequests'];
		}
		$TotalNotifications=$data['TotalGroups'];
		setcookie('newnotificationcount', $TotalNotifications, 0, '/');
		setcookie('newgrpcount-ghst', $data['TotalGroups'], 0, '/');
		setcookie('newgrpinvitecount-ghst', $data['TotalGroupInvite'], 0, '/');
		setcookie('newgrpreqcount-ghst', $data['TotalGroupRequests'], 0, '/');
		$data['status'] = 'success';
		$data['totalnotify'] = $TotalNotifications;
		$this->_helper->layout->disableLayout();
		$response = $this->getResponse();
		return $response->setBody(Zend_Json::encode($data));
	}

	public function hideuserdbAction()
	{
		$request = $this->getRequest();
		$userid = $request->getpost('user');
		if($userid){
			$cookieuser= $this->_userid;
			$blockeddate=date('Y-m-d H:i:s');
			$dbeereload  = new Application_Model_Myhome();
			$data = array("User"=>$cookieuser,"BlockedUser"=>$userid,"BlockedDate"=>$blockeddate);
			$dbeereload->inserblockuser($data);
			if($dbeereload)
				echo "1~0";
			else echo "0~0";
		}
		$response = $this->_helper->layout->disableLayout();
		return $response;
	}
	public function termsAction()
	{
		$request	=	$this->getRequest()->getParams();
		$this->_helper->layout->disableLayout();
	}
	public function aboutAction()
	{
		$request =	$this->getRequest()->getParams();
		$this->_helper->layout->disableLayout();
	}
	public function privacyAction()
	{
		$request =	$this->getRequest()->getParams();
		$this->_helper->layout->disableLayout();
	}
	public function feedbackAction()
	{
		$request =	$this->getRequest()->getParams();
		$this->_helper->layout->disableLayout();
	}
	public function sendfeedbackAction()
	{
		$request =	$this->getRequest()->getParams();
		$this->view->feedbacktext =$request['feedbacktext'];
		$storage 	= new Zend_Auth_Storage_Session();
		$data	  	= $storage->read();
		$this->_userEmail = $data['Email'];
		$this->view->userEmail = $this->_userEmail;
		$this->_userName = $data['Name'];
		$this->view->userName = $this->_userName;
	}
	public function deletedbAction()
	{
		$request = $this->getRequest();
		$db = $request->getPost('db');
		$type = $request->getpost('type');
		$user = $this->_userid;
		if($type=='main'){
			$totalCom = $this->myclientdetails->getfieldsfromtable(array('DbeeID'),'tblDbeeComments','DbeeID',$db);
			//$data 	= array('Active' => '0');	
			//$delete = $this->myclientdetails->updatedata_global('tblDbees',$data,'DbeeID',$db);
			if(count($totalCom)<1)
			{
				$this->myclientdetails->deleteMaster('tblactivity',array("act_typeId"=> $db ));
				$delete = $this->myclientdetails->deleteMaster('tblDbees',array("DbeeID"=> $db ));
				$deleteinfluence = $this->myclientdetails->deleteMaster('tblInfluence',array("ArticleId"=> $db ));
			}
			else
			{
				echo "999";exit;
			}
		}elseif($type=='redb') {
			$delete = $this->myclientdetails->deleteMaster('tblReDbees',array("ID"=> $db ));
		}elseif($type=='favourite') {
			$delete = $this->myclientdetails->deleteMaster('tblFavourites',array("DbeeID"=> $db , "User"=> $user
        ));
			$totalFav = $this->myclientdetails->getfieldsfromtable(array('DbeeID'),'tblFavourites','User',$user);
    
			if($totalFav==0)
				$return='<div class="noFound" style="margin-top:110px;">You have not added any '.POST_NAME.' to your favourites yet.</div>';
		}
		echo '1~'.$db.'~'.$type.'~'.$return.'~'.$totalFav;
		$this->_helper->layout->disableLayout();
		return $response;
	}
	public function twtsreplyAction(){
		 
		$request = $this->getRequest();
		 
		$dbee = $request->getpost('db');
		 
		$dbeetweet  = new Application_Model_Myhome();
		 
		$getreply = $dbeetweet->getdbright($dbee);
		
		$reply = ($getreply['StopTweetsReply']==1)?'0':'1';
		 
		$data =array('StopTweetsReply'=> $reply);

		$dbeetweet->updatetwtsreply($data, $dbee);
		 
		$response = $this->_helper->layout->disableLayout();
		 
		return $response;
		 
		 
	}

	public function attendiesreplyAction()
	{	 
		$this->_helper->viewRenderer->setNoRender(true);
		$request = $this->getRequest();	 
		$dbee = $request->getpost('db');
		$dbeetweet  = new Application_Model_Myhome();
		$getreply = $dbeetweet->getdbright($dbee);
		$reply = ($getreply['attendiesList']==1)?'0':'1';
		$data =array('attendiesList'=> $reply);
		$dbeetweet->updatetwtsreply($data, $dbee);
		$response = $this->_helper->layout->disableLayout();
		return $response;	 
	}

	public function changetwnumAction(){
		$request = $this->getRequest();
		$dbee = $request->getpost('db');
		 
		$num = $request->getpost('tweetnum');
		$dbeetweet  = new Application_Model_Myhome();
		$data =array('ShowTweetsNum'=> $num);
		$dbeetweet->updatetwtnum($data, $dbee);
		$response = $this->_helper->layout->disableLayout();
		return $response;
	}
	
	public function stopppboxAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
			$Myhome_model  = new Application_Model_Myhome();
			
			if($Myhome_model->stopchk($this->_userid))
				$data1 =array('ShowPPBox'=> 0);
			else
				$data1 =array('ShowPPBox'=> 1);
				
			$Myhome_model->updatestoppopup($data1, $this->_userid);	
			$data['status'] = 'success';
		}else{
				$data['status'] = 'error';
				$data['message'] = 'Some thing went wrong here please try again';	
			}
		
		return $response->setBody(Zend_Json::encode($data));
	}
	
	public function stopemailboxAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
			$Myhome_model  = new Application_Model_Myhome();
			
			$data1 =array('EmailBox'=> 1);
			
			$Myhome_model->updatestoppopup($data1, $this->_userid);	
			$data['status'] = 'success';
		}else{
				$data['status'] = 'error';
				$data['message'] = 'Some thing went wrong here please try again';	
			}
		
		return $response->setBody(Zend_Json::encode($data));
	}
	
	
	
	// Seo friendly url create function 
	function makeSeo($title, $raw_title = '', $context = 'display'){
		$myhome_obj  = new Application_Model_Myhome();
		$dburl = $this->getdbeeurl($title, '', $context);
		if($myhome_obj->chkdbeetitle($dburl)){
			return $dburl;			
		}else{		
			$words = explode(' ', $title);
			$title2 = implode(' ', array_slice($words, 0, 14)).'-'.date('i:s'); 
			return $dburl = $this->getdbeeurl($title2, '', $context);
		}	
	}
	
	function getdbeeurl($title, $raw_title = '', $context = 'display')
	{
	
		$title = $this->string_limit_words($title, 15);
	
		$title = strip_tags($title);
	
		// Preserve escaped octets.
	
		$title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
	
		// Remove percent signs that are not part of an octet.
	
		$title = str_replace('%', '', $title);
	
		// Restore octets.
	
		$title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);
	
	
	
		if ($this->seems_utf8($title)) {
	
			if (function_exists('mb_strtolower')) {
	
				$title = mb_strtolower($title, 'UTF-8');
	
			}
	
			$title = $this->utf8_uri_encode($title, 200);
	
		}
	
	
	
		$title = strtolower($title);
	
		$title = preg_replace('/&.+?;/', '', $title); // kill entities
	
		$title = str_replace('.', '-', $title);
	
	
	
		if ('save' == $context) {
	
			// Convert nbsp, ndash and mdash to hyphens
	
			$title = str_replace(array(
	
					'%c2%a0',
	
					'%e2%80%93',
	
					'%e2%80%94'
	
			), '-', $title);
	
	
	
			// Strip these characters entirely
	
			$title = str_replace(array(
	
			// iexcl and iquest
	
					'%c2%a1',
	
					'%c2%bf',
	
					// angle question
	
					'%c2%ab',
	
					'%c2%bb',
	
					'%e2%80%b9',
	
					'%e2%80%ba',
	
					// curly question
	
					'%e2%80%98',
	
					'%e2%80%99',
	
					'%e2%80%9c',
	
					'%e2%80%9d',
	
					'%e2%80%9a',
	
					'%e2%80%9b',
	
					'%e2%80%9e',
	
					'%e2%80%9f',
	
					// copy, reg, deg, hellip and trade
	
					'%c2%a9',
	
					'%c2%ae',
	
					'%c2%b0',
	
					'%e2%80%a6',
	
					'%e2%84%a2'
	
			), '', $title);
	
	
	
			// Convert times to x
	
			$title = str_replace('%c3%97', 'x', $title);
	
		}
	
	
	
		$title = preg_replace('/[^%a-z0-9 _-]/', '', $title);
	
		$title = preg_replace('/\s+/', '-', $title);
	
		$title = preg_replace('|-+|', '-', $title);
	
		$title = trim($title, '-');
		
		//$title = str_replace('%', '', strtolower(trim($title)));
		
		//$title = str_replace(' ', '-', strtolower(trim($title)));
	
		return $title;
	
	}
	
	
	function seems_utf8($str)
	{
	
		$length = strlen($str);
	
		for ($i = 0; $i < $length; $i++) {
	
			$c = ord($str[$i]);
	
			if ($c < 0x80)
				$n = 0; // 0bbbbbbb
	
			elseif (($c & 0xE0) == 0xC0)
			$n = 1; // 110bbbbb
			elseif (($c & 0xF0) == 0xE0)
			$n = 2; // 1110bbbb
			elseif (($c & 0xF8) == 0xF0)
			$n = 3; // 11110bbb
			elseif (($c & 0xFC) == 0xF8)
			$n = 4; // 111110bb
			elseif (($c & 0xFE) == 0xFC)
			$n = 5; // 1111110b
	
			else
				return false; // Does not match any model
	
			for ($j = 0; $j < $n; $j++) { // n bytes matching 10bbbbbb follow ?
	
				if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
					return false;
	
			}
	
		}
	
		return true;
	
	}
	
	function utf8_uri_encode($utf8_string, $length = 0)
	{
	
		$unicode = '';
	
		$values = array();
	
		$num_octets = 1;
	
		$unicode_length = 0;
	
	
	
		$string_length = strlen($utf8_string);
	
		for ($i = 0; $i < $string_length; $i++) {
	
	
	
			$value = ord($utf8_string[$i]);
	
	
	
			if ($value < 128) {
	
				if ($length && ($unicode_length >= $length))
					break;
	
				$unicode .= chr($value);
	
				$unicode_length++;
	
			} else {
	
				if (count($values) == 0)
					$num_octets = ($value < 224) ? 2 : 3;
	
	
	
				$values[] = $value;
	
	
	
				if ($length && ($unicode_length + ($num_octets * 3)) > $length)
					break;
	
				if (count($values) == $num_octets) {
	
					if ($num_octets == 3) {
	
						$unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]) . '%' . dechex($values[2]);
	
						$unicode_length += 9;
	
					} else {
	
						$unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]);
	
						$unicode_length += 6;
	
					}
	
	
	
					$values = array();
	
					$num_octets = 1;
	
				}
	
			}
	
		}
	
		return $unicode;
	
	}
	
	function string_limit_words($string, $word_limit)
	{
	
		$words = explode(' ', $string);
	
		return implode(' ', array_slice($words, 0, $word_limit));
	
	}
	public function updateuserpopupAction()
	{ 			
		$data = array();
		$response = $this->getResponse();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		
		if ($this->getRequest()->isXmlHttpRequest()  && $this->getRequest()->getMethod() == 'POST' ) {
			$request   = $this->getRequest();
			$filter = new Zend_Filter_StripTags();
			$user      = $filter->filter($request->getpost('userid'));
			$password  = $filter->filter($request->getpost('password'));
			$birthdate = $filter->filter($request->getpost('birthdate'));
			$gender    = $filter->filter($request->getpost('gender'));
			$pass = $this->_generateHash($password);	
			$profilepic = 'default-avatar.jpg';
			if($gender=='female') $profilepic = 'default-avatar-female.jpg';			
			$dataupdate = array('Pass'=>$pass,'Birthdate'=>$birthdate,'Gender'=>$this->myclientdetails->customEncoding($gender),'ProfilePic'=>$profilepic);				
			if(!empty($user)){
				
				$store = $this->myclientdetails->updatedata_global('tblUsers',$dataupdate,'UserID',$user);
				$data['msg'] = 'records updated successfully';	
				$data['content'] = 1;
			}else{
				$data['msg'] = 'records not updated successfully';
				$data['content'] = 0; 
			}		
			return $response->setBody(Zend_Json::encode($data));
		}
	}
	
	public function _generateHash($plainText, $salt = null)
	{ 
		define('SALT_LENGTH', 9);
		if ($salt === null) {
			$salt = substr(md5(uniqid(rand(), true)), 0, SALT_LENGTH);
		} else {
			$salt = substr($salt, 0, SALT_LENGTH);
		}
	
		return $salt . sha1($salt . $plainText);
	}

	
	
	
	public function getdefaultlistsAction()
	{
		//$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response 	= 	$this->getResponse();
		$catlist 	=	'';
		$grplist 	=	'';
		$leglist 	=	'';
		$eventhtml  =   '';
		$grpPrivcy 	=	array('1'=>'open','2'=>'Private','3'=>'Requet to join');

		$response->setHeader('Content-type', 'application/json', true);
		if($this->getRequest()->getMethod() == 'POST' && $this->getRequest()->isXmlHttpRequest())
		{
			
			$userid 	= 	$_SESSION['Zend_Auth']['storage']['UserID']; 
			$catlist 	= 	$this->myclientdetails->getAllMasterfromtable('tblDbeeCats',array('CatID','CatName'),'',array('Priority'=>'Desc'));
			
			$grouplist 	= 	$this->myclientdetails->getfieldsfromtable(array('ID','GroupName','GroupPrivacy'),'tblGroups','User',$userid);

			$groupcatlist 	= 	$this->myclientdetails->getfieldsfromtable(array('TypeID','TypeName'),'tblGroupTypes');

			$usersetarray 	= 	$this->myclientdetails->getAllMasterfromtable('usersgroup',array('ugid','ugname'),'',array('ugname'=>'ASC'));
			
			
			$CurrDate	=	date('Y-m-d H:i:s');
        	$onCondi 	= 	'lu.UserID  = ' .$userid.' and lu.Enddate >= "'. $CurrDate.'"' ;
        	$leaguesql 	= 	"select DISTINCT(lu.LID),lu.Title,lu.EndDate from tblUserLeague lu where lu.clientID = ".clientID." AND ".$onCondi ; 
        	$leaguelist = $this->myclientdetails->passSQLquery($leaguesql);

            $myhome_obj = new Application_Model_Myhome();

        	$eventlist  = $myhome_obj->eventlist();



			if(count($catlist)>0)
			{
				$content = '<div class="formRow postCatWrapper"  id="postincategory">
				<a href="javascript:void(0)" class="categoriesBtn"><i class="postpopupIcon fa fa-folder fa-lg"></i> Categorise post <span class="ar"></span> <i class="optionalText">Optional</i></a>
			
				<div class="categoryList" >';
				foreach($catlist as $key =>$value):
				$checked = ($value['CatName']=="Miscellaneous")?checked:'';
				
				$content .='<label class="labelCheckbox"><input type="checkbox" '.$checked.' value="'.$value['CatID'].'" cat-name="'.$value['CatName'].'"><label class="checkbox"></label>'.$value['CatName'].'</label>';
				endforeach;
				$content .='</div>
				</div>';
			}

			if(count($catlist)>0)
			{
				$categorysort='<div id="dbfeedfilterbycatWrp"><ul class="dbfeedfilterbycat">';

				$counter=1;

				foreach($catlist as $dbcatRow):

					if($counter%2==0){
						$categorysort.='<li><label class="labelCheckbox" for="dbfeedfilterbycat'.$counter.'"><input type="checkbox" id="dbfeedfilterbycat'.$counter.'" name="dbfeedfilterbycat[]" value="'.$dbcatRow['CatID'].'"><label class="checkbox" for="dbfeedfilterbycat'.$counter.'"></label>'.$dbcatRow['CatName'].'</label></li>';
					//$return.='<div class="" ><a href="javascript:dbfeedfilterbycat('.$dbcatRow['CatID'].')">'.$dbcatRow['CatName'].'</a></div>';		

					}else{
						$categorysort.='<li><label class="labelCheckbox" for="dbfeedfilterbycat'.$counter.'"><input type="checkbox" id="dbfeedfilterbycat'.$counter.'" name="dbfeedfilterbycat[]" value="'.$dbcatRow['CatID'].'"><label class="checkbox" for="dbfeedfilterbycat'.$counter.'"></label>'.$dbcatRow['CatName'].'</label></li>';

					//$return.='<div class="" ><a href="javascript:void(0)" onclick="javascript:dbfeedfilterbycat('.$dbcatRow['CatID'].');">'.$dbcatRow['CatName'].'</a></div>';

					}

				$counter++;

				endforeach;

				$categorysort.='</ul></div><a id="postdbee-main" chkfilter="trueval" href="javascript:void(0)" onclick=javascript:seeglobaldbeelist("nouserid",3,"","myhome","catetorylist","dbcat");return false; class="">search</a></div>';
			}

			if(count($groupcatlist)>0)
			{
				$grpcatlist = '<div class="formRow postCatWrapper"  id="postincategory">
				<a href="javascript:void(0)" class="grpcategoriesBtn">group categories  <span class="ar"></span> <i class="optionalText">Optional</i></a>

				<div class="grpcategoryList" >';
				foreach($groupcatlist as $key =>$value):
				$grpcatlist .='<label class="labelCheckbox"><input type="checkbox" value="'.$value['TypeID'].'"><label class="checkbox"></label>'.$value['TypeName'].'</label>';
				endforeach;
				$grpcatlist .='</div>
				</div>';
			}
			if(count($grouplist)>0)
			{
				$grplist = '<div class="formRow postGroupWrapper" id="postingrouup">
				<div class="spoLabel"><i class="fa fa-users"></i> Select Group</div>
				<div class="groupLeftPop">
					<select name="selectGroupList" id="selectGroupList" class="selectDrp">';
				foreach($grouplist as $key =>$value):
				$grplist .='<option value="'.$value['ID'].'" groupname="'.$value['GroupName'].'" grouptype="'.$value['GroupPrivacy'].'">'.$value['GroupName'].' - '.$grpPrivcy[$value['GroupPrivacy']].'</option>';
				endforeach;
				$grplist .='</select>
						<a href="javascript:void(0);" class="closeRowField">
							<i class="fa fa-times-circle fa-lg"></i>
						</a>
					</div>
				</div>';
			}
			else
			{
				$grplist = '<div class="formRow postGroupWrapper" id="postingrouup">
				<div class="spoLabel"><i class="fa fa-users"></i> Select group</div>
				<div class="groupLeftPop">				
					<div class="notPopupCreate" >You have not created a group yet.</div>
						<a href="javascript:void(0);" class="closeRowField">
							<i class="fa fa-times-circle fa-lg"></i>
						</a>
					</div>
				</div>';
			}
			
			if(count($leaguelist)>0)
			{
				$leglist = '<div class="formRow postLeagueWrapper" id="postinleague">
				<div class="spoLabel"><i class="fa fa-shield"></i> Select league</div>
				<div class="groupLeftPop">
					<select name="leagueId" id="leagueId" class="selectDrp">';
					foreach($leaguelist as $key =>$value):
					$leglist .='<option value="'.$value['LID'].'">'.$value['Title'].'</option><input type="hidden" lgenddate="'.$value['EndDate'].'">';
					endforeach;
					$leglist .='</select>
						<a href="javascript:void(0);" class="closeRowField">
							<i class="fa fa-times-circle fa-lg"></i>
						</a>
					</div>
				</div>';
			} 
			else
			{
				$leglist = '<div class="formRow postLeagueWrapper" id="postinleague">
				<div class="spoLabel"><i class="fa fa-shield"></i> Select league</div>
				<div class="groupLeftPop">
					<div class="notPopupCreate" >You have not started a league yet!</div>
						<a href="javascript:void(0);" class="closeRowField">
							<i class="fa fa-times-circle fa-lg"></i>
						</a>
					</div>
				</div>';
			}
			//userset start
			if(count($usersetarray)>0)
			{
				$userset = '<div class="formRow postUserSetWrapper" id="postinuserset">
				<div class="spoLabel"  style="width:132px;"><i class="fa fa-users"></i> Select a user set </div>
				<div class="groupLeftPop">
					<select name="userset" id="userset" class="selectDrp">';
					$userset .='<option value="">Select a user set</option>';
					foreach($usersetarray as $key =>$value):
					$userset .='<option value="'.$value['ugid'].'">'.$value['ugname'].'</option>';
					endforeach;
					$userset .='</select>						
					</div>
				</div>';
			} 
			else
			{
				$userset = '';
			}
			// userset end
			if(count($eventlist)>0)
			{
				$eventhtml = '<div class="formRow postGroupWrapper" id="postinevent">
				<div class="spoLabel"><i class="fa fa-calendar"></i> &nbsp;Select event</div>
				<div class="groupLeftPop">
					<select name="eventid" id="eventid" class="selectEvent selectDrp">';
					foreach($eventlist as $key =>$value):
					$eventhtml .='<option value="'.$value['id'].'">'.$value['title'].'</option>';
					endforeach;
					$eventhtml .='</select>
						<a href="javascript:void(0);" class="closeRowField">
							<i class="fa fa-times-circle fa-lg"></i>
						</a>
					</div>
				</div>';
			}
			else
			{
				$eventhtml = '<div class="formRow postEventWrapper" id="postinevent">
				<div class="spoLabel"><i class="fa fa-calendar"></i> &nbsp;Select event</div>
				<div class="groupLeftPop">				
					<div class="notPopupCreate" >You have not created an event yet.</div>
						<a href="javascript:void(0);" class="closeRowField">
							<i class="fa fa-times-circle fa-lg"></i>
						</a>
					</div>
				</div>';
			}

			if($this->IsLeagueOn==1)
			{
				$leglist='';
			}
		
	
		$data['cat'] = $content;
		$data['group'] = $grplist;
		$data['leage'] = $leglist;
		$data['userset'] = $userset;
		$data['groupcat'] = $grpcatlist;
		$data['events'] = $eventhtml;
		$data['catsearch'] = $categorysort;

		
		
	}
			
		return $response->setBody(Zend_Json::encode($data));
		//echo $content.'~'.$grplist.'~'.$leglist.'~'.$grpcatlist.'~'.$eventhtml.'~'.$categorysort;
		exit;
	}
	
	public function getusrgrouplistAction()
	{
		//$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response 	= 	$this->getResponse();		
		$grplist 	=	'';		
		$grpPrivcy 	=	array('1'=>'open','2'=>'Private','3'=>'Requet to join');
	
		$response->setHeader('Content-type', 'application/json', true);
		if($this->getRequest()->getMethod() == 'POST' && $this->getRequest()->isXmlHttpRequest())
		{
			$userid 	= 	$_SESSION['Zend_Auth']['storage']['UserID'];
			
				$userid 	= 	$_SESSION['Zend_Auth']['storage']['UserID']; 
				$grouplist 	= 	$this->myclientdetails->getfieldsfromtable(array('ID','GroupName','GroupPrivacy'),'tblGroups','User',$userid);
	
				if(count($grouplist)>0)
				{
					$grplist = '<div class="formRow postGroupWrapper" id="postingrouup">
				<div class="spoLabel"><i class="fa fa-users"></i> Select Group</div>
				<div class="groupLeftPop">
					<select name="selectGroupList" id="selectGroupList" class="selectDrp">';
						foreach($grouplist as $key =>$value):
						$grplist .='<option value="'.$value['ID'].'" groupname="'.$value['GroupName'].'" grouptype="'.$value['GroupPrivacy'].'">'.$value['GroupName'].' - '.$grpPrivcy[$value['GroupPrivacy']].'</option>';
						endforeach;
						$grplist .='</select>
								<a href="javascript:void(0);" class="closeRowField">
									<i class="fa fa-times-circle fa-lg"></i>
								</a>
					</div>
				</div>';
				}
				else
				{
					$grplist = '<div class="formRow postGroupWrapper" id="postingrouup">
				<div class="spoLabel"><i class="fa fa-users"></i> Select Group</div>
				<div class="groupLeftPop">
					<div class="notPopupCreate" >You have not created a group yet.</div>
						<a href="javascript:void(0);" class="closeRowField">
							<i class="fa fa-times-circle fa-lg"></i>
						</a>
					</div>
				</div>';
				}	
	
			
				$data['group'] = $grplist;
				
				
		}
			
		return $response->setBody(Zend_Json::encode($data));
		
		exit;
	}
	
	public function categorylistsAction()
	{
		//$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response 	= 	$this->getResponse();
		$catlist 	=	'';
		$groupcatlist 	=	'';
		//$grpPrivcy 	=	array('1'=>'open','3'=>'Requet to join');

		$userid 	= 	$_SESSION['Zend_Auth']['storage']['UserID']; 
		$catlist 	= 	$this->myclientdetails->getAllMasterfromtable('tblDbeeCats',array('CatID','CatName'),'',array('Priority'=>'Desc'));
		
		$groupcatlist 	= 	$this->myclientdetails->getfieldsfromtable(array('TypeID','TypeName'),'tblGroupTypes');

		$addedPostcatlist 	= 	$this->myclientdetails->getAllMasterfromtable('tblCustomDashboard',array('cus_ids'),array('cus_userid'=>$userid,'cus_filtertype'=>'postCategory'));
		if(count($addedPostcatlist)>0)
		{
			$PostcatArray =  explode(',',$addedPostcatlist[0]['cus_ids']);
		}

		$addedGroupcatlist 	= 	$this->myclientdetails->getAllMasterfromtable('tblCustomDashboard',array('cus_ids','cus_subtype'),array('cus_userid'=>$userid,'cus_filtertype'=>'groupCategory'));
		if(count($addedGroupcatlist)>0)
		{
			$PostgrpArray =  explode(',',$addedGroupcatlist[0]['cus_ids']);
			/*if($addedGroupcatlist[0]['cus_subtype']!='')
			$PostgrptypeArray =  explode(',',$addedGroupcatlist[0]['cus_subtype']);*/
		}

		if(count($catlist)>0)
		{
			$content = '<div class="postCatWrapper"  id="postincategory">
			<h2><span>Categories<i>Show me posts from these categories</i></span><a id="postCategory" class="pull-right  btn btn-yellow  btn-mini filterdashboard" href="javascript:void(0)">Save</a></h2>

			<div class="categoryList categoryListMyDash" >';
			foreach($catlist as $key =>$value):
				$checked = '';
				//$this->myclientdetails->searchvalue_MultiArray($value['CatID'], $test,$searchfieldname='');
				if(count($addedPostcatlist)>0)
				{
					if (in_array($value['CatID'], $PostcatArray)) $checked = "checked=checked";
				}

				$content .='<label class="labelCheckbox"><input type="checkbox" value="'.$value['CatID'].'" '.$checked.'><label class="checkbox"></label>'.$value['CatName'].'</label>';
			endforeach;
			$content .='</div>
			</div>';
		}
		if(count($groupcatlist)>0)
		{
			$grpcatlist = '<div class="postCatWrapper"  id="postincategory">
			<h2><span>Groups<i>Show me posts from these Groups</i></span><a id="groupCategory" class="pull-right btn btn-yellow  btn-mini  filterdashboard" href="javascript:void(0)">Save</a></h2>

			<div class="grpcategoryList grpcategoryListMyDash" >';
			foreach($groupcatlist as $key =>$value):
				$checked = '';
				if(count($PostgrpArray)>0)
				{
					if (in_array($value['TypeID'], $PostgrpArray)) $checked = "checked=checked";
				}

				$grpcatlist .='<label class="labelCheckbox"><input type="checkbox" value="'.$value['TypeID'].'" '.$checked.'><label class="checkbox"></label>'.$value['TypeName'].'</label>';
			endforeach;
			$grpcatlist .='</div>
			</div><div class="defaultGrpType" data-type="groupType" >';
			/*foreach($grpPrivcy as $key =>$value):
				$checked = '';
				if(count($PostgrptypeArray))
				{
					if (in_array($key, $PostgrptypeArray)) $checked = "checked=checked";
				}

				$grpcatlist .='<label class="labelCheckbox"><input type="checkbox" value="'.$key.'" '.$checked.'><label class="checkbox"></label>'.$value.'</label>';
			endforeach;*/
					
			$grpcatlist .='</div>';
		}
		
		echo $content.'~'.$grpcatlist;
		exit;
	}
	public function getfolderslistAction() {
		
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		$data = array();
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
			
		$myhome_obj  = new Application_Model_Myhome();
		$content = '<div class="attachrow"><h2 class="titlePop">Attach</h2><ul class="mainFilesList">';
		$allfolders = $myhome_obj->getFolders('','allfolder');
		
		if(count($allfolders)>0)
		{
			$iconexe = array(
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
			foreach ($allfolders as $value):
			
			$allfiles = $myhome_obj->getFolders($value['kc_id'],'foldernfiles');
			if(count($allfiles)>0)
			{
				$fcnt = $myhome_obj->getfolderscnt($value['kc_id']);
			$filestitle = ($fcnt > 1)?' files':' file';
			$content .= '<li  title="'. $value['kc_cat_title'].'"  folder="'. $value['kc_id'] .'" id="'. $value['kc_id'] .'" pretitle="'. str_replace('_',' ',$value['kc_cat_title']).'">';
			$content .= '<a href="javascript:void(0)"><i class="fa fa-folder "></i> '.str_replace('_',' ',$value['kc_cat_title']).' ('.$fcnt.$filestitle.')</a>';
			
			$content .= '<div  class="postfilelist" style="display: none;"><ul>';
					foreach ($allfiles as $data):	
					$exticon = '';	
					$ext = '';
					$ext = pathinfo($data['kc_file'], PATHINFO_EXTENSION); 
					$exticon = $iconexe[$ext];
					$exticon = ($exticon!='')?$exticon:'text';						
						$content .= '<li class="folderlist"  title="'. $data['kc_cat_title'].'"   pretitle="'. str_replace('_',' ',$data['kc_cat_title']).'"><label for="'.$data['kc_id'].'"><input type="checkbox" id="'.$data['kc_id'].'" value="'.$data['kc_file'].'" attname="'.$data['kc_cat_title'].'"><label for="'.$data['kc_id'].'" class="checkbox"></label>';
						$content .= '<i class="fa fa-file-'.$exticon.'-o" style="margin-right:5px"></i>';
						$content .=str_replace('_',' ',$data['kc_cat_title']);
						$content .= '</label></li>';
					endforeach;
					$content .= '</ul></div>';
				}  
		  	endforeach;
		  	
		  	$content .= '</li>';
			  	
			 } else { 
			
				$content .= '<li class="noCategory">No category added </li>';
			 }		
			$content .= '</ul><div class="clear"> </div></div>';
			
			$data['content'] = $content;
			
			return $response->setBody(Zend_Json::encode($data));
		exit;
		}
		
	}

	public function adddashboardAction() {
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if($this->getRequest()->getMethod() == 'POST' && $this->getRequest()->isXmlHttpRequest())
		{
			$data_array = array();	
			$type = (int) $this->_request->getPost('action');
			$dbid = (int) $this->_request->getPost('dbeeID');
			$data_array['dbeeID'] = $dbid;	
			$data_array['UserID'] = (int) $this->_userid;
			$data_array['actiontype'] = $type;
			$data_array['clientID'] = clientID;
			$dbuser = $this->_request->getPost('dbuser');
			$notindashboard = $this->myhome_obj->checkdbtodashbord($data_array['dbeeID'],$data_array['UserID'],$type);		
			if($notindashboard)
			{
				$insertleague = $this->myclientdetails->insertdata_global('tblHiddenPosts',$data_array);
				$this->notification->commomInsert('31','31',$dbid,$this->_userid,$dbuser);
				$data['status']  = 'success';
				$data['content'] = "question sent!";
				$data['type'] = $type;
				$data['dbid'] = $dbid;
				if($type==1)				
				$data['message'] = 'post successfully add to dashboard';
				
		
			}else
			{	
				if($type==1)			
				$data['message'] = 'Allready add to dashboard';
				$data['dbid'] = $dbid;
				$data['status']  = 'error';
				$data['content'] = $content;
				$data['type'] = $type;
				
			}
		}else{
		
			$data['status']  = 'error';
			$data['message'] = 'error try later';
		}
		
		
		
		return $response->setBody(Zend_Json::encode($data));
	}
	
	public function removefromdashboardAction() {
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if($this->getRequest()->getMethod() == 'POST' && $this->getRequest()->isXmlHttpRequest())
		{
			$data_array = array();			
			$dbid = (int) $this->_request->getPost('dbeeID');
			$data_array['dbeeID'] = $dbid;
			$userid = (int) $this->_userid;			
			$notindashboard = $this->myhome_obj->deletedashdb($userid,$dbid,2);			
		}else{
	
			$data['status']  = 'error';
			$data['message'] = 'error try later';
		}
	
		return $response->setBody(Zend_Json::encode($data));
	}
	

	public function insertfollowAction() {
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if($this->getRequest()->getMethod() == 'POST' && $this->getRequest()->isXmlHttpRequest())
		{
			$data_array = array();
			$userID = (int) $this->_request->getPost('dbusr');	
			$following = new Application_Model_Following();
			//$chkfollowing = $following->chkfollowing($userID,$this->_userid);
			$finser['User']= $userID;
			$finser['FollowedBy']= $this->_userid;
			$finser['clientID']= clientID;
			$insertfollowing = $following->insertfollowing($finser);			
			$this->notification->commomInsert('4','4',$this->_userid,$this->_userid,$userID);
			if($insertfollowing)
			{
				$data['success']  = 'success';
			}
		}else{
	
			$data['status']  = 'error';
			$data['message'] = 'error try later';			
		}
	
		return $response->setBody(Zend_Json::encode($data));
	}	
	public function chkfollowingAction() {
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if($this->getRequest()->getMethod() == 'POST' && $this->getRequest()->isXmlHttpRequest())
		{
			$data_array = array();
			$userID = (int) $this->_request->getPost('dbusr');
			$following = new Application_Model_Following();
			$chkfollowing = $following->chkfollowing($userID,$this->_userid);
				
			if(!empty($chkfollowing))
			{
				$data['success']  = '1';
			}
		}else{
	
			$data['status']  = 'error';
			$data['message'] = 'error try later';
			$data['success']  = '0';
		}
	
		return $response->setBody(Zend_Json::encode($data));
	}
	
	public function cleardbcacheAction() {
		$folderID = $_SESSION['Zend_Auth']['storage']['UserID'];
		$dir = APPLICATION_PATH.'/cache/';
		array_map('unlink', glob($dir."/*"));
		echo "done";
		exit;
	}
	public function searchusersAction(){
		$request = $this->getRequest()->getParams();
		$myhomesearch = new Application_Model_Myhome();
		$this->_helper->viewRenderer->setNoRender(true);
		$param = $request["q"];
		$searchuser = $myhomesearch->searchcomusers($param,$this->session_data['usertype']);
		echo $searchuser;die;
	}
	
	public function sharefileAction() 
	{
		
		
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		if ($this->getRequest()->isXmlHttpRequest() ) 
		{
			
			$ds = DIRECTORY_SEPARATOR;
	
			$storeFolder 	= './userpdf';  

			//$image_info 	= getimagesize($_FILES["file"]["tmp_name"]);

			/*list($width, $height, $type, $attr) = getimagesize($_FILES["file"]["tmp_name"]);
			$limt_widthimg =300;
            if($width<$limt_widthimg){
                echo "imagewidtherror";
				exit;
            }*/

			/*if($image_info[0] < 1 && $image_info[1] < 1)
			{
				echo "Please use valid image to upload";
				exit;
			}*/
				
			if (!empty($_FILES))
			{
				$fileCount = count($_FILES["file"]["name"]);
				 $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION); 
					
				 $picture	=	rand(0,5000).strtolower(time().'.'.$ext);
			
				if(copy($_FILES['file']['tmp_name'], './userpdf/' .$picture))
				{
					echo $picture.'~'.$_FILES["file"]["name"];
				}
				exit;
			}
		}

		exit;
	}
	

	
	public function sharefileinsertAction()
	{ 
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$followuserobj = new Application_Model_Following();
			$myhomeObj = new Application_Model_Myhome();
			
			$type = $this->_request->getPost('typehh');
			
			$fileName = $this->_request->getPost('sfile');
			
			$realname = $this->_request->getPost('realname');
			
			if(count($fileName)>0){
				foreach($fileName as $value):
			
				$sp = spliti ("~", $value);
				
		
				$data	 =	array('clientID'=>clientID,'kc_pid'=>'front','kc_cat_title'=>$sp[1],'kc_file'=>$sp[0],'kc_adddate'=>date("Y-m-d H:i:s"), 'added_by'=>$this->_userid, 'is_front'=>'1');
				$fileExs =	$myhomeObj->insertdatakc($data);
				
				if($type=='follow'){
						
					$followuser = $followuserobj->getfollingautoid($this->_userid);
					
					 if(count($followuser)>0){
						
						foreach ($followuser as $value):
						$data2	 =	array('clientID'=>clientID,'userid'=>$value,'fileid'=>$fileExs); 
						$fid = $myhomeObj->insertfileuser($data2);
						$activity = array('clientID'=>clientID,'act_type'=>'34','act_message'=>'37','act_typeId'=>$fileExs,'act_userId'=>$this->_userid, 'act_ownerid'=>$value,'act_date'=>date("Y-m-d H:i:s") );
						$this->myclientdetails->insertdata_global('tblactivity',$activity);					
				
						endforeach;
					}  
				
					}
					elseif($type=='contact'){
						
						$contactuser = $followuserobj->getcontact($this->_userid);						
						foreach ($contactuser as $value):
						$data2	 =	array('clientID'=>clientID,'userid'=>$value,'fileid'=>$fileExs);
						$fid = $myhomeObj->insertfileuser($data2);
						$activity = array('clientID'=>clientID,'act_type'=>'34','act_message'=>'37','act_typeId'=>$fileExs,'act_userId'=>$this->_userid, 'act_ownerid'=>$value,'act_date'=>date("Y-m-d H:i:s") );
						$this->myclientdetails->insertdata_global('tblactivity',$activity);
						
						endforeach;
							
							
					}
					elseif($type=='user'){
						
						$userids = $this->_request->getPost('userid');
						$userid = explode(',', $userids);
						if(count($userid)>0){
							foreach ($userid as $value):
							$data2	 =	array('clientID'=>clientID,'userid'=>$value,'fileid'=>$fileExs);
							$fid = $myhomeObj->insertfileuser($data2);
							$activity = array('clientID'=>clientID,'act_type'=>'34','act_message'=>'37','act_typeId'=>$fileExs,'act_userId'=>$this->_userid, 'act_ownerid'=>$value,'act_date'=>date("Y-m-d H:i:s") );
							$this->myclientdetails->insertdata_global('tblactivity',$activity);
							
							endforeach;		
						}				
							
					}
				
				
				
				endforeach;
			
			}		
			
			
		}
		else
		{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));
	}
	
	public function sharefileupdateAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$followuserobj = new Application_Model_Following();
			$myhomeObj = new Application_Model_Myhome();
				
			$type = $this->_request->getPost('type');
				
			$fileExs = $this->_request->getPost('fileid');			
		
	
				if($type=='following'){
	
					$followuser = $followuserobj->getfollingautoid($this->_userid);
						
					if(count($followuser)>0){
	
						foreach ($followuser as $value):
						$data2	 =	array('clientID'=>clientID,'userid'=>$value,'fileid'=>$fileExs);
						$fid = $myhomeObj->insertfileuser($data2);
						$activity = array('clientID'=>clientID,'act_type'=>'34','act_message'=>'37','act_typeId'=>$fileExs,'act_userId'=>$this->_userid, 'act_ownerid'=>$value,'act_date'=>date("Y-m-d H:i:s") );
						$this->myclientdetails->insertdata_global('tblactivity',$activity);
	
						endforeach;
					}
	
				}
				elseif($type=='contact'){
	
					$contactuser = $followuserobj->getcontact($this->_userid);
					foreach ($contactuser as $value):
					$data2	 =	array('clientID'=>clientID,'userid'=>$value,'fileid'=>$fileExs);
					$fid = $myhomeObj->insertfileuser($data2);
					$activity = array('clientID'=>clientID,'act_type'=>'34','act_message'=>'37','act_typeId'=>$fileExs,'act_userId'=>$this->_userid, 'act_ownerid'=>$value,'act_date'=>date("Y-m-d H:i:s") );
					$this->myclientdetails->insertdata_global('tblactivity',$activity);
	
					endforeach;
				}
				elseif($type=='user'){
	
					$userids = $this->_request->getPost('userid');
					$userid = explode(',', $userids);
					if(count($userid)>0){
						foreach ($userid as $value):
						$data2	 =	array('clientID'=>clientID,'userid'=>$value,'fileid'=>$fileExs);
						$fid = $myhomeObj->insertfileuser($data2);
						$activity = array('clientID'=>clientID,'act_type'=>'34','act_message'=>'37','act_typeId'=>$fileExs,'act_userId'=>$this->_userid, 'act_ownerid'=>$value,'act_date'=>date("Y-m-d H:i:s") );
						$this->myclientdetails->insertdata_global('tblactivity',$activity);
							
						endforeach;
					}
						
				}
				$data['success'] = 'Data inserted successfully';
				
				
		}
		else
		{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));
	}
	
	
	public function deletesfileAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$myhomeObj = new Application_Model_Myhome();
			
			/*$fileid = $this->_request->getPost('fileid');
			$fileinfo = $admindata = $this->myclientdetails->getfieldsfromtable(array('kc_id','kc_file','is_front','added_by'),'tblknowledge','kc_id',$fileid);
			
			$data['file'] = $target = $_SERVER['DOCUMENT_ROOT'].'/userpdf/'.$fileinfo['kc_file'];
		if (file_exists($target)){
			  @unlink($target);
		} else {
			    $data['message'] = "file does not exist"; 
		}		
			
			$this->myclientdetails->deleteMaster('tblknowledge',array('kc_id'=>$fileid,'clientID'=>clientID));
			$this->myclientdetails->deleteMaster('tblUserPdf',array('fileid'=>$fileid,'clientID'=>clientID));
			$data['success'] = 'file deleted successfully';*/
			 $fileid = $this->_request->getPost('fileid');
			 $myupload = $this->_request->getPost('myupload');
			
			$userid = $this->_userid;

			if($myupload==1){	
			$data = array('isdelete'=>1);						
				$myhomeObj->updatefiledel($data,$fileid);
			}else{
				$this->myclientdetails->deleteMaster('tblUserPdf',array('fileid'=>$fileid,'clientID'=>clientID));			
			}

			$data['message'] = 'Data update successfully';
		}
		else
		{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));
	}
	public function updateshowtagmsg($chk)
	{ 
		
		$myhomeObj = new Application_Model_Myhome();
		$data = array('showtagmsg'=>0);
		$myhomeObj->updateshowtagmsg($data, $this->_userid);
		$options['UserID'] = $this->_userid;
        $userresult = $this->myclientdetails->getAllMasterfromtable('tblUsers','*',$options);
        $authNamespace = new Zend_Session_Namespace('identify');
        $authNamespace->setExpirationSeconds((1209600));
        $authNamespace->role = $userresult['0']['role'];
        $authNamespace->id = $userresult[0]['UserID'];
        $authNamespace->user = $userresult[0]['Username'];
        $this->myclientdetails->sessionWrite($userresult[0]);
    	
	}

	public function polllistAction()
    { 
        $request = $this->getRequest();
       // $start = $this->getRequest()->getPost('start')?$this->getRequest()->getPost('start'):0;
       // $end = $this->getRequest()->getPost('end')?$this->getRequest()->getPost('end'):PAGE_NUM;  
        $myhomeObj = new Application_Model_Myhome();
        $start = $request->getpost('start', 0);
        $end   = $request->getpost('end', PAGE_NUM);    
        $this->view->favouritesdbees = $myhomeObj->polldata($start,$end,$this->_userid);                   
        $this->view->start = $start;
        $this->view->startnew = $start+PAGE_NUM;
        $this->view->end = $start+PAGE_NUM; 
        $this->view->dbeenotavailmsg = $this->getRequest()->getPost('dbeenotavailmsg'); 
        $this->view->seemore = $this->getRequest()->getPost('seemore');
        $response = $this->_helper->layout->disableLayout();
        return $response;
    }
	
	 public function dbupdatetextAction()
	{ 			
		$data = array();
		$response = $this->getResponse();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		
		if ($this->getRequest()->isXmlHttpRequest()  && $this->getRequest()->getMethod() == 'POST' ) {
			
			$request   = $this->getRequest();
			$filter = new Zend_Filter_StripTags();

			$dbeeid      = $filter->filter($request->getpost('dbeeid'));
			$text  = stripcslashes($filter->filter($request->getpost('text')));
			
						
			$dataupdate = array('Text'=>$text);				
			if(!empty($dbeeid)){
				
				$db = $this->myclientdetails->updatedata_global('tblDbees',$dataupdate,'DbeeID',$dbeeid);
				$data['msg'] = 'records updated successfully';	
				$data['content'] = 1;
			}else{
				$data['msg'] = 'records not updated successfully';
				$data['content'] = 0; 
			}		
			return $response->setBody(Zend_Json::encode($data));
		}
	}
	
 public function senmailgroupmem($groupUsers,$userid,$dburl,$groupid,$groupname,$dbtitle)
    {
    	//$myname='',$dearname='' ,$emailid='',$posttxt='',$dburl=''
    	//$this->senmailgroupmem($groupUsers,$this->_userid,$dburl,$groupid,$groupname,$dbtitle);
        $dbeeEmailtemplate = new RawEmailtemplate();
        $emailTemplatemain = $dbeeEmailtemplate->dbeeEmailtemplate();       
        $deshboard   = new Admin_Model_Deshboard();
        $bodyContent = $deshboard->getGroupemailtemplate();
       
        $body = '';
        $myhomeObj = new Application_Model_Myhome();
        $postuserobj = $myhomeObj->getuserdetail($userid);
        
        $myname = $this->myclientdetails->customDecoding($postuserobj['Name']);
        
        $MailSubject = "New post in group '".$groupname."'";
       
        $tesxtappent = $myname." started a new post in group '".$groupname."'"; 
       
        $footerContentmsg = $bodyContent[0]['footertext'];
       
      
        $emailTemplatejson = $this->myclientdetails->getfieldsfromtable(array('id','emailtemplatejson','htmllayout'),'adminemailtemplates');

        $bodyContentjson = $emailTemplatejson[0]['emailtemplatejson'];
        $bodyContentjsonval = json_decode($bodyContentjson, true);
        $data1 = array('[%bodycontentbacgroColor%]',
                       '[%bodycontenttxture%]',
                       '[%headerbacgroColor%]',
                       '[%headertxture%]',
                       '[%bannerfreshimg%]',
                       '[%contentbodyfontColor%]',
                       '[%contentbodybacgroColor%]',
                       '[%contentbodytxture%]',
                       '[%%body%%]',
                       '[%footerfontColor%]',
                       '[%footerbacgroColor%]',
                       '[%footertxture%]',
                       '[%footerfontColor%]',
                       '[%%footertext%%]'
                       );
       
        foreach($groupUsers as $value):
        
        $userdetail = $myhomeObj->getuserdetail($value['User']);
        
        $dearname = $this->myclientdetails->customDecoding($userdetail['Name']);
        $emailid = $this->myclientdetails->customDecoding($userdetail['Email']);
        $bodyContentmsg = 'Dear '.$dearname. ',<br><br>'.$tesxtappent.'<br><br> <span style="color:#999;font-size:16px;line-height:20px">'.$dbtitle.'</span><br><br><a href="'.BASE_URL.'/dbee/'.$dburl.'">Go to post</a>';
        
        $data2 = array($bodyContentjsonval['bodycontentbacgroColor'],
                       $bodyContentjsonval['bodycontenttxture'],
                       $bodyContentjsonval['headerbacgroColor'],
                       $bodyContentjsonval['headertxture'],
                       $bodyContentjsonval['bannerfreshimg'],
                       $bodyContentjsonval['contentbodyfontColor'],
                       $bodyContentjsonval['contentbodybacgroColor'],
                       $bodyContentjsonval['contentbodytxture'],
                       $bodyContentmsg,
                       $bodyContentjsonval['footerfontColor'],
                       $bodyContentjsonval['footerbacgroColor'],
                       $bodyContentjsonval['footertxture'],
                       $bodyContentjsonval['footerfontColor'],
                       $footerContentmsg);
        
        $messagemail = str_replace($data1,$data2,$emailTemplatemain);
        $MailFrom=SITE_NAME; //Give the Mail From Address Here
        $MailReplyTo=NOREPLY_MAIL;
        $MailTo     =   $emailid;      
        $MailBody = html_entity_decode(str_replace("'","\'",$messagemail));
        // echo $MailBody;
        // echo $MailSubject;
        // echo $MailFrom;
        // exit;
     
        $this->sendWithoutSmtpMail($MailTo,$MailSubject,$MailFrom,$MailBody);
        
        endforeach;
        
        return true;          

    }  

	
	
}
