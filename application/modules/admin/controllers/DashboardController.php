<?php

class Admin_DashboardController extends IsadminController
{

	private $options;

	/**
	 * Init
	 *
	 * @see Zend_Controller_Action::init()
	 */

	protected function jsonResponse($status, $code, $html, $message = null)
    {
        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            -> setHttpResponseCode($code)
            ->setBody(Zend_Json::encode(array("status"=>$status, "html"=>$html, "message"=>$message)))
            ->sendResponse();
            exit;
    }

	public function init()
	{		
		
		parent::init();
		$this->_options= $this->getInvokeArg('bootstrap')->getOptions();
		$this->deshboard = new Admin_Model_Deshboard();
		$this->defaultimagecheck = new Admin_Model_Common();
		$this->dash_obj = new Admin_Model_Deshboard();
		//echo $system_timezone = date_default_timezone_get();
		
	}
	
	public function notificationAction()
	{
		require_once 'includes/globalfileadmin.php';
		$this->view->activityaMsg = $activityaMsg;
		$this->view->activityArr = $activityArr;
	}

	public function getpdfAction()
	{
		require_once 'includes/pdfcrowd.php';

		try
		{  
			$request 	=	$this->getRequest()->getParams();
			$convURL 	= 	BASE_URL."/admin/dashboard/postreport/m_-_xxp=t/".$request['postid'];  
		    // create an API client instance
		    $client = new Pdfcrowd("Ragini", "a6df1ab7187bddaf5fecd575b1a5a19b");

		    // convert a web page and store the generated PDF into a $pdf variable
		    $pdf = $client->convertURI($convURL);

		    // set HTTP response headers
		    header("Content-Type: application/pdf");
		    header("Cache-Control: max-age=0");
		    header("Accept-Ranges: none");
		    header("Content-Disposition: attachment; filename=\"google_com.pdf\"");

		    // send the generated PDF 
		    echo $pdf;
		}
		catch(PdfcrowdException $why)
		{
		    echo "Pdfcrowd Error: " . $why;
		}
		exit;
	}

	public function imguplodAction()
	{
		
	
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);	
        $commonfun  = new Admin_Model_Common();	
		
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
	 		$request 		=	$this->getRequest()->getPost();
 		
			$storeFolder 	= ABSIMGPATH."/imageposts/";
			
			if (!empty($_FILES)) {

				chmod($storeFolder , 0777);

			   $fileCount = count($_FILES["file"]["name"]);
               for($i=0; $i < $fileCount; $i++)
               {
				$ext = pathinfo($_FILES['file']['name'][$i], PATHINFO_EXTENSION);
                $time = time().mt_rand(1,79632);
                $fileName   =   strtolower($time.'.'.$ext);
                $permission = 	substr(sprintf('%o', fileperms($storeFolder)), -4);
				
				//code 
				$filenames 		= ABSIMGPATH."/imageposts/medium/".$fileName;
				$filename1 		= ABSIMGPATH."/imageposts/small/".$fileName;
				$image 			=   $_FILES["file"]["name"][$i];
				$uploadedfile 	=   $_FILES['file']['tmp_name'][$i];
				$filenamex 		= stripslashes($_FILES['file']['name'][$i]); 	
  			    $extension 		= $commonfun->getExtension($filenamex);
 		        $extension 		= strtolower($extension);
 		        if($extension=="jpg" || $extension=="jpeg" )
				{
					$uploadedfile = $_FILES['file']['tmp_name'][$i];
					$src = imagecreatefromjpeg($uploadedfile);

				}
				else if($extension=="png")
				{
					$uploadedfile = $_FILES['file']['tmp_name'][$i];
					$src = imagecreatefrompng($uploadedfile);

				}
				else 
				{
					$src = imagecreatefromgif($uploadedfile);
				}
				//code end 			
				
                $move=move_uploaded_file($_FILES["file"]["tmp_name"][$i],$storeFolder.$fileName);

                //code 
                if($move==1){
		   		list($width,$height)=getimagesize(IMGPATH."/imageposts/".$fileName);
		   		
		   		if($width < 484)
		   		{		   			
		   			$medium=copy($storeFolder.$fileName, $filenames);

		   			if($width < 200)
		   			{		   			  
		   			  $medium=copy($storeFolder.$fileName, $filename1);	
		   			}
		   			else
		   			{
		   				$newwidth1=200;
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
					$newwidth=484;
					$newheight=($height/$width)*$newwidth;
					$tmp=imagecreatetruecolor($newwidth,$newheight);
					$newwidth1=200;
					$newheight1=($height/$width)*$newwidth1;
					$tmp1=imagecreatetruecolor($newwidth1,$newheight1);
					imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
					imagecopyresampled($tmp1,$src,0,0,0,0,$newwidth1,$newheight1,$width,$height);
					imagejpeg($tmp,$filenames,100);
					imagejpeg($tmp1,$filename1,100);
					imagedestroy($src);
					imagedestroy($tmp);
					imagedestroy($tmp1);
				 }
			   }
				//code end
                

                $ret['img']=$fileName;

               }
               chmod($storeFolder , 0755);
			}
		}
		else 
        {
            $data['status']  = 'error';
            $data['message'] = 'Seo settings not updated';
        }
        return $response->setBody(Zend_Json::encode($ret));	
		
	}


	public function getfolderslistAction() {
		
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		$data = array();
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
		{
			
		
		$content = '<div class="attachrow"><ul class="mainFilesList">';
		$allfolders = $this->deshboard->getFolders('','allfolder');
		
		if(count($allfolders)>0)
		{
			foreach ($allfolders as $value):
			$allfiles = $this->deshboard->getFolders($value['kc_id'],'foldernfiles');
			if(count($allfiles)>0)
			{

			$filexx='files';
			if($this->deshboard->getfolderscnt($value['kc_id']) < 2)
			{
				$filexx='file';
			}

			$content .= '<li  title="'. $value['kc_cat_title'].'"  folder="'. $value['kc_id'] .'" id="'. $value['kc_id'] .'" pretitle="'. str_replace('_',' ',$value['kc_cat_title']).'">';
			$content .= '<a href="javascript:void(0)">'.str_replace('_',' ',$value['kc_cat_title']).' ('.$this->deshboard->getfolderscnt($value['kc_id']).' '.$filexx.')</a>';
			
			$content .= '<div  class="postfilelist" style="display: none;"><ul>';
					foreach ($allfiles as $data):					
						$content .= '<li class="folderlist"  title="'. $data['kc_cat_title'].'"  fileid="'. $data['kc_id'] .'" id="'. $data['kc_id'] .'" pretitle="'. str_replace('_',' ',$data['kc_cat_title']).'"><label><input type="checkbox" name="attachment[]" id="attached-'.$data['kc_id'].'" value="'.$data['kc_cat_title'].'####xxxx'.$data['kc_file'].'" attname="'.$data['kc_cat_title'].'"><label for="attached-'.$data['kc_id'].'" class="checkbox"></label>';
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
  

	public function imgunlinkAction()
	{

		$request = $this->getRequest()->getParams();	
	
		unlink(ABSIMGPATH."/imageposts/".trim($request['serverFileName_']));
		unlink(ABSIMGPATH."/imageposts/medium/".trim($request['serverFileName_']));
		unlink(ABSIMGPATH."/imageposts/small/".trim($request['serverFileName_']));		
		exit;

	}

	


	public function linkdetailAction(){
		//$commonfun  = new Application_Model_Commonfunctionality();
		//$myhome = new Application_Model_Myhome();
		$request = $this->getRequest();
		$url= $request->getpost('dbeeurl');
		if($this->common_model->getrestrictedurl($url)){
			$return=-2; 
		}else{			
			if ($this->check_matches($url, $array_of_needles)) $isvideo=true;
			else $isvideo=false;
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
				
				$LinkPic ='';
	
				if(!$err) {
					$return='<div class="makelinkWrp">
					<div class="removeCircle" id="closeLinkUrl">
						<span class="fa-stack">
						  <i class="fa fa-circle fa-stack-2x"></i>
						  <i class="fa fa-times fa-stack-1x fa-inverse"></i>
						</span>
					</div>
					<div class="makelinkDes otherlinkdis" style="margin:0px;"><h2>'.$title.'</h2>';
					if($description!='')
						$return.='<div class="desc">'.$description.'</div>';
					$return.='<div class="makelinkshw"><a href="'.$url.'" target="_blank">'.substr($url ,0,50).'</a></div>';
					$return.='<input type="hidden" id="url" name="url" value="'.$url.'"><input type="hidden" id="LinkPic" name="LinkPic" value="'.$LinkPic.'"><input type="hidden" id="LinkTitle" name="LinkTitle" value="'.$title.'"><input type="hidden" id="LinkDesc" name="LinkDesc" value="'.$description.'"></div></div>';
				}
				else $return=-1;
			}
			
			else $return=-1;
		}
		echo $return;
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

	public function postreportAction()
	{
		$request = $this->getRequest()->getParams();

		$postid = base64_decode($request['m_-_xxp=t']);
		
		$this->view->postidEncode =$request['m_-_xxp=t'];
		$this->view->postid =$postid;

		$objinf =new Admin_Model_Influence();
		$this->view->mostinfuser = $objinf->MostInfluenceUser($postid);
		$this->view->PollOptions = $this->deshboard->getPollOption($postid);
		
		//exit;

	}
	public function qnadetailsAction()
	{
		$request = $this->getRequest()->getParams();
		$commonobj = new Admin_Model_Common();
        $load = 0;
        $ret  = '';

        if($request['caller']=='attendies') 
        {
        	$que ="SELECT DISTINCT  `tblDbeeQna`.`user_id`,`tblDbeeQna`.`qna`, `tblUsers`.`UserID`, `tblUsers`.`full_name`, `tblUsers`.`lname`, `tblUsers`.`ProfilePic` FROM `tblDbeeQna` INNER JOIN `tblUsers` ON tblDbeeQna.user_id=tblUsers.UserID WHERE (dbeeid= ".$request['dbid'].") AND (tblDbeeQna.clientID = ".clientID.") AND parentid=0";
        } else {

        	$que ="SELECT  `tblDbeeQna`.`user_id`,`tblDbeeQna`.`qna`,`timestamp`, `tblUsers`.`UserID`, `tblUsers`.`full_name`, `tblUsers`.`lname`, `tblUsers`.`ProfilePic` FROM `tblDbeeQna` INNER JOIN `tblUsers` ON tblDbeeQna.user_id=tblUsers.UserID WHERE (dbeeid= ".$request['dbid'].") AND (tblDbeeQna.clientID = ".clientID.") AND parentid=0";

        }
      	
        $qnaAttendies = $this->myclientdetails->passSQLquery($que);	
        
        foreach($qnaAttendies as $liveDbee) :
            $load++;
            $ret .= '<li>';
                    
            $qnaUser    ='<div class="oneline"><a class="show_details_user" userid="'.$liveDbee['UserID'].'" href="javascript:void(0)">'.htmlentities($this->myclientdetails->customDecoding($liveDbee['full_name'])).'</a></div>';
            $descDisplay    ='';
            
            $dbtypeIcon ='';
            $checkliveDbpic = $this->defaultimagecheck->checkImgExist($liveDbee['ProfilePic'],'userpics','default-avatar.jpg'); 
           
            if($request['caller']=='question') { 
            	$ret .='<div class="listUserPhoto">
                    <img src="'.IMGPATH.'/users/medium/'.$checkliveDbpic.'" width="50" height="50" border="0" class="recievedUsePic"/>
                </div>';   
                $ret .='<div class="dataListWrapper">
                    <div class="dataListbox">
                        <div class="scoredData">
                        	'.$qnaUser.'
                            <div class="dbPost">'.($liveDbee['qna']).'</div>';
                        $ret .='</div>
                         <div class="scoredPostDate">Posted on '.date('d F, Y',strtotime($liveDbee['timestamp'])).'</div>
                    </div>                   
                </div>'; 
            } else{
            	 $ret .='<div class="listUserPhoto">
                    <img src="'.IMGPATH.'/users/medium/'.$checkliveDbpic.'" width="50" height="50" border="0" class="recievedUsePic"/>
                    '.$qnaUser.'
                </div>';
            }
            $ret .='</li>';

        
        $rowchange++; 
        endforeach; 
        
        echo $ret;
        exit;
	}
	public function sentimentcommentsAction()
	{
		$request = $this->getRequest()->getParams();
		$commonobj = new Admin_Model_Common();
        $load = 0;
        $ret  = '';
        if($request['senti']!='')
        {
        	$liveDbeeData 	=	$this->dash_obj->getfieldsfromjointable(array('*'),'tblDbeeComments','DbeeID',$request['dbid'],array('UserID','Name','ProfilePic'),'tblUsers','UserID','UserID','','','','sentiment_polarity',$request['senti']);
        } else {
        	$liveDbeeData 	=	$this->dash_obj->getfieldsfromjointable(array('*'),'tblDbeeComments','DbeeID',$request['dbid'],array('UserID','Name','ProfilePic'),'tblUsers','UserID','UserID','','','','tblDbeeComments.UserID',$request['userid']);
        }	
        foreach($liveDbeeData as $liveDbee) :
            $load++;
            $ret .= '<li>';
                    
            $descDisplay    ='';

            if($liveDbee['Type']==1) { 
                $dbtype         =   'text db';
                $dbtypeIcon =   '<div class="pstype typeText"></div>';
                $descDisplay    =   '<div class="dbLinkDesc">'.($liveDbee['Comment']).'</div>';//substr($liveDbee['Text'],0,100).'</div>';
            }
            if($liveDbee['Type']==2) 
            { 
                $dbtype         =   'link db';
                $dbtypeIcon =   '<div class="pstype typeLink"></div>';
                $dbLink         =   $liveDbee['Link'];
                $dbLinkTitle    =   $liveDbee['LinkTitle'];
                $dbLinkDesc     =   $liveDbee['LinkDesc'];
                $dbUserLinkDesc =   !empty($liveDbee['UserLinkDesc']) ? $liveDbee['UserLinkDesc'] : $liveDbee['LinkTitle'];

                $descDisplay    =   '<div class="dbLinkDesc">'.htmlentities($dbUserLinkDesc, ENT_QUOTES, "UTF-8").' - 
                <a target="_blank" href="'.$dbLink.'">'.$dbLink.'</a>
                </div>';
            }
            if($liveDbee['Type']==3) 
            { 
            $dbtype =   'pix db';
            $dbtypeIcon =   '<div class="pstype typePix"></div>';
            $dbPic      =   $liveDbee['Pic'];
            $dbPicDesc  =   $liveDbee['PicDesc']; 
            $checkdbeepic = $this->defaultimagecheck->checkImgExist($dbPic,'imageposts','default-avatar.jpg');              
            if($dbPic!='')
            {
                $descDisplay    .=  '<div class="dbPic"><a href="#"><img src="'.IMGPATH.'/imageposts/small/'.$checkdbeepic.'" border="0" /></a></div>';
            }
            else{

                $noPic = 'noPix';
            }                   
            $descDisplay    .=  '<div class="dbPicDesc '.$noPic.'">'.htmlentities($dbPicDesc, ENT_QUOTES, "UTF-8").'</div>';//substr($dbPicDesc,0,100).'</div>';
            }
            if($liveDbee['Type']==4) { 
                $dbtype =   'media db';
                $dbtypeIcon =   '<div class="pstype typeVideo"></div>';
                $dbVid          =   $liveDbee['VidID'];
                $dbVidDesc      =   $liveDbee['VidDesc'];
                $dbLinkDesc     =   $liveDbee['LinkDesc'];
                $dbUserLinkDesc =   !empty($liveDbee['UserLinkDesc']) ? $liveDbee['UserLinkDesc'] : $liveDbee['LinkTitle'];

                $descDisplay    .=  '<div class="dbPicText">';
                if($dbVid!='')
                { 
                    $descDisplay    .=  '<div class="dbPic" ><a href="javascript:void(0)"><img width="90" height="60" border="0" src="https://i.ytimg.com/vi/'.$dbVid.'/0.jpg"></a></div>';
                }
                else{
                    $noPic = 'noPix';
                }
                $descDisplay    .=  '<div class="dbPicDesc '.$noPic.'">'.htmlentities($dbVidDesc, ENT_QUOTES, "UTF-8").'</div></div>';
            
            
            }
            $dbtypeIcon ='';
            $checkliveDbpic = $this->defaultimagecheck->checkImgExist($liveDbee['ProfilePic'],'userpics','default-avatar.jpg'); 
            $ret .='<div class="listUserPhoto">
                    <img src="'.IMGPATH.'/users/medium/'.$checkliveDbpic.'" width="90" height="90" border="0" class="recievedUsePic"/>
                </div>
                <div class="dataListWrapper">
                    <div class="dataListbox">
                        <div class="scoredListTitle">
                            '. $dbtypeIcon.' <a class="show_details_user" userid="'.$liveDbee['UserID'].'" href="javascript:void(0)">'.htmlentities($this->myclientdetails->customDecoding($liveDbee['Name'])).'</a>';  
                            if($liveDbee['sentiment_polarity'] == 'positive'){
								$ret .=' <a href="javascript:void(0);" rel="" title="Post has positive sentiment" class="dbee-feed-titlebar-smallFont" style="font-weight:normal;margin:10px;">
								<img class="" src="'.BASE_URL.'/timthumb.php?src=/img/positive.png&amp;q=100&amp;w=30&amp;h=30" style=" display:inline-block; width:30px; height:30px;   vertical-align: middle;"></i>positive
								 </a>';
							} else if($liveDbee['sentiment_polarity'] == 'negative'){
								$ret .=' <a href="javascript:void(0);"  rel="" title="Post has negative sentiment" class="dbee-feed-titlebar-smallFont" style="font-weight:normal;margin:10px;" > 
								<img class="" src="'.BASE_URL.'/timthumb.php?src=/img/negative.png&amp;q=100&amp;w=30&amp;h=30" style=" display:inline-block; width:30px; height:30px;  vertical-align: middle;"> negative</a>';
							} else if($liveDbee['sentiment_polarity'] == 'neutral'){
								$ret .=' <a href="javascript:void(0);"  rel="" title="Post has neutral sentiment"  class="dbee-feed-titlebar-smallFont" style="font-weight:normal;margin:10px;" >
								<img class="" src="'.BASE_URL.'/timthumb.php?src=/img/neutral.png&amp;q=100&amp;w=30&amp;h=30" style=" display:inline-block; width:30px; height:30px;  vertical-align: middle;"> neutral
								</a>';
							}                          
                        $ret .='</div>
                        <div class="scoredData">
                            <div class="dbPost">'.($descDisplay).'<a href=""></a>&nbsp;</div>';
                             if($liveDbee['twitter']) {
                            $ret .='<div class="twitterbird-dbee-feed dbPost">
                                <span class="twitterbird scoreSprite "></span>'.htmlentities(substr($liveDbee['twitter'],0,100)).'
                            </div>  ';
                             } 
                        $ret .='</div>
                         <div class="scoredPostDate">Posted on '.date('d F, Y',strtotime($liveDbee['CommentDate'])).'</div>
                    </div>
                   
                </div> </li>';

        
        $rowchange++; endforeach; 
        echo $ret;
        exit;
	}
	public function sentimentsAction()
    {
        function queuedComparator($value) {
		    return $value == TASK_STATUS_QUEUED;
		}
        $request      	= $this->getRequest()->getParams();
        $getcomments  	= $this->deshboard->sentimentscomment($request['dbid']);
        $initialTexts 	= array();
        $CommentID    	= array();
        $userID    		= array();
        foreach ($getcomments as $key => $value) {
            if ($value['Type'] == 1) {
                if (empty($value['Comment']))
                    $initialTexts[] = 'DBEE';
                else
                    $initialTexts[] = $value['Comment'];
                
                $commentid[] = $value['CommentID'];
            } else if ($value['Type'] == 2) {
                if (empty($value['UserLinkDesc']))
                    $initialTexts[] = 'DBEE';
                else
                    $initialTexts[] = $value['UserLinkDesc'];
                $commentid[] = $value['CommentID'];
            } else if ($value['Type'] == 3) {
                if (empty($value['PicDesc']))
                    $initialTexts[] = 'DBEE';
                else
                    $initialTexts[] = $value['PicDesc'];
                
                $commentid[] = $value['CommentID'];
            } else if ($value['Type'] == 4) {
                if (empty($value['VidDesc']))
                    $initialTexts[] = 'DBEE';
                else
                    $initialTexts[] = $value['VidDesc'];
                
                $commentid[] = $value['CommentID'];
            } else if ($value['Type'] == 5) {
                if (empty($value['Comment']))
                    $initialTexts[] = 'DBEE';
                else
                    $initialTexts[] = $value['Comment'];
                $commentid[] = $value['CommentID'];
            }
            $userID[] = $value['UserID'];
        }
    
        if( count($commentid)>0)
        {

        	//$initialTexts[] = 'A baby monster named Huba becomes the target of some renegade monsters, as they believe his presence can disrupt the balance of power between monsters and humans.';
	        $session = new \Semantria\Session(CONSUMER_KEY, CONSUMER_SECRET, NULL, NULL, TRUE); 
	        

	        $callback = new SessionCallbackHandler();
	        $session->setCallbackHandler($callback);

	        $subscription = $session->getSubscription();

			$tracker = array();
			$documets = array();

	        foreach ($initialTexts as $text) {
			    $doc_id = uniqid('', TRUE);

			    $documents[] = array('id' => $doc_id, 'text' => $text);
			    $tracker[$doc_id] = TASK_STATUS_QUEUED;
			    
			    if (count($documents) == $subscription['basic_settings']['batch_limit']) {
			        $docsCount = count($documents);
			        $res = $session->queueBatch($documents);
			        if ($res == 200 || $res == 202) {
			            print("${docsCount} documents queued successfully.\n");
			            $documents = array();
			        }
			    }
			}
	        $docsCount = count($documents);
			if ($docsCount) {
			    $res = $session->queueBatch($documents);
			    if ($res == 200 || $res == 202) {
			        //print("${docsCount} documents queued successfully.\n");
			    }
			    else {
			        die("Unexpected error.\n");
			    }
			}
	       
	        
	        $results = array();

			while (count(array_filter($tracker, 'queuedComparator'))) {
			    usleep(500000);

			    //print("Retrieving your processed results...\n");
			    $response = $session->getProcessedDocuments();

			    foreach ($response as $item) {
			        if (array_key_exists($item['id'], $tracker)) {
			            $tracker[$item['id']] = $item['status'];
			            $results[] = $item;
			        }
			    }
			}
	        $counter   = 0;
	        $chkupdate = '';
	        
	        $updArr = array();    
	        $semArr = array();     //echo "<pre>" ;print_r($results); exit;

	        foreach ($results as $data) {
	        	
	        	if (isset($data["entities"])) {
			        foreach ($data["entities"] as $entity) {
			           // echo "	", $entity["title"], " : ", $entity["entity_type"], " (sentiment: ", $entity["sentiment_score"], ")", "\r\n";
			            $semArr = array('clientID'=>clientID,'userid'=>$userID[$counter],'commnetid'=>$commentid[$counter],'postid'=>$request['dbid'],'entity_type'=>$entity["entity_type"],'entity_title'=>$entity["title"],'sentiment_score'=>$entity["sentiment_score"],'sentiment_polarity'=>$entity["sentiment_polarity"],'sentiment'=>json_encode( $data),'date'=>date('Y-m-d H:i:s'));
  						
  						$this->myclientdetails->insertdata_global('tblsematira',$semArr);
			        }
			    }

	        	

	        	$updArr    = array(
	                'sentiment_score' => $data["sentiment_score"],
	                'sentiment_polarity' => $data["sentiment_polarity"]
	            );
	            $chkupdate = $updatecomment = $this->myclientdetails->updatedata_global('tblDbeeComments',$updArr,'CommentID', $commentid[$counter]);
	            $counter++;
	        }
	        if ($chkupdate) {
	            echo $_SERVER['HTTP_REFERER'];
	        } else {
	            echo '404';
	        }
	    }
	    else echo "503";
	    exit;
	    
    } 

    function queuedComparator($value) {
	    return $value == TASK_STATUS_QUEUED;
	}
    // End of sentiment function
	/*public function sentimentsAction()
    {
        
        $request      = $this->getRequest()->getParams();
        $getcomments  = $this->deshboard->sentimentscomment($request['dbid']);
        $initialTexts = array();
        $CommentID    = array();
        foreach ($getcomments as $key => $value) {
            if ($value['Type'] == 1) {
                if (empty($value['Comment']))
                    $initialTexts[] = 'DBEE';
                else
                    $initialTexts[] = $value['Comment'];
                
                $commentid[] = $value['CommentID'];
            } else if ($value['Type'] == 2) {
                if (empty($value['UserLinkDesc']))
                    $initialTexts[] = 'DBEE';
                else
                    $initialTexts[] = $value['UserLinkDesc'];
                $commentid[] = $value['CommentID'];
            } else if ($value['Type'] == 3) {
                if (empty($value['PicDesc']))
                    $initialTexts[] = 'DBEE';
                else
                    $initialTexts[] = $value['PicDesc'];
                
                $commentid[] = $value['CommentID'];
            } else if ($value['Type'] == 4) {
                if (empty($value['VidDesc']))
                    $initialTexts[] = 'DBEE';
                else
                    $initialTexts[] = $value['VidDesc'];
                
                $commentid[] = $value['CommentID'];
            } else if ($value['Type'] == 5) {
                if (empty($value['Comment']))
                    $initialTexts[] = 'DBEE';
                else
                    $initialTexts[] = $value['Comment'];
                $commentid[] = $value['CommentID'];
            }
        }
       // echo count($commentid);
        if( count($commentid)>0)
        {
	        $session = new \Semantria\Session(CONSUMER_KEY, CONSUMER_SECRET, NULL, NULL, TRUE); 
	        
	        $callback = new SessionCallbackHandler();
	        $session->setCallbackHandler($callback);
	        
	        foreach ($initialTexts as $text) {
	            $doc    = array(
	                "id" => uniqid(''),
	                "text" => $text
	            );
	            $status = $session->queueDocument($doc);
	            if ($status == 202) {
	                
	            }
	        }
	        // Count of the sample documents which need to be processed on Semantria
	        $length  = count($initialTexts);
	        $results = array();
	        
	        while (count($results) < $length) {
	            sleep(10);
	            $status = $session->getProcessedDocuments();
	            if (is_array($status)) {
	                $results = array_merge($results, $status);
	            }
	            
	        }
	        $counter   = 0;
	        $chkupdate = '';
	        
	        $updArr = array();

	        foreach ($results as $data) {
	            $updArr    = array(
	                'sentiment_score' => $data["sentiment_score"],
	                'sentiment_polarity' => $data["sentiment_polarity"]
	            );
	            $chkupdate = $updatecomment = $this->myclientdetails->updatedata_global('tblDbeeComments',$updArr,'CommentID', $commentid[$counter]);
	            $counter++;
	        }
	        if ($chkupdate) {
	            echo $_SERVER['HTTP_REFERER'];
	        } else {
	            echo '404';
	        }
	    }
	    else echo "503";
	    exit;
	    
    } // End of sentiment function*/

    

	// E O Moved to vipuser controller 
	private function _generateHash($plainText, $salt = null)
	{
		define('SALT_LENGTH', 9);
		if ($salt === null) {
			$salt = substr(md5(uniqid(rand(), true)), 0, SALT_LENGTH);
		} else {
			$salt = substr($salt, 0, SALT_LENGTH);
		}

		return $salt . sha1($salt . $plainText);
	}

	
	
	public function addprofanityfilterAction()
	{
		/* $this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$name = $this->_request->getPost('ProfanityfilterInput');
		$filterName = explode(',', $name);
		if(count($filterName)==1){
			$dataval = array('name'=> trim($filterName[0]),'status'=> 1,'clientID'=> clientID);
			$this->myclientdetails->insertdata_global('tblProfanityFilter',$dataval);
		}
		else{
			foreach ($filterName as $value) {
				$dataval = array('name'=> trim($value),'status'=> 1,'clientID'=> clientID);
				$this->myclientdetails->insertdata_global('tblProfanityFilter',$dataval);
			}
		}
		$this->_redirect('/admin/Restrictedurl/profanityfilter'); */
		
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$filter = new Zend_Filter_StripTags();
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
		$filterName = $this->_request->getPost('word');
		//$filterName = explode(',', $name);
		$vdata = array();
		 	if(count($filterName)==1){
				$dataval = array('name'=> trim($filterName[0]),'status'=> 1,'clientID'=> clientID);
				$id = $this->myclientdetails->insertdata_global('tblProfanityFilter',$dataval);
				$vdata[0]['id']=$id;
				$vdata[0]['name']=$filterName[0];
			}
			else{
					$i=0;
				foreach ($filterName as $value) {

					$dataval = array('name'=> trim($value),'status'=> 1,'clientID'=> clientID);

					$id = $this->myclientdetails->insertdata_global('tblProfanityFilter',$dataval);
					$vdata[$i]['id']=$id;
					$vdata[$i]['name']=$value;
					$i++;
				}
			} 
			$data['total'] = count($filterName);
			$data['id'] = $id;
			$data['status'] = $filterName;
			$data['vdata'] = $vdata;
		} 
		else
		{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));
		
		
		
	}
	public function profanitydeleteAction()
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$id = (int) $this->_request->getPost('filterID');
		$this->myclientdetails->deletedata_global('tblProfanityFilter','id',$id);
		$rs = $this->deshboard->profanityfilter('','');
		echo count($rs);
		return;
	}
	public function profanityfilterAction()
	{
		/* $request = $this->getRequest()->getParams();
		if(isset($request['searchfield']) && $request['searchfield']!='') 
		{
			$seachfieldChk = $request['searchfield'];
			$namespace->searchfield 	= 	 $seachfieldChk;
			$this->view->seachfield	=	 $seachfieldChk; 
		}
		else
			$seachfield = '';
		
		if(isset($request['orderfield']) && $request['orderfield']!='') 
		{
			$orderfieldChk = $request['orderfield'];
			$namespace->orderfield 	= 	$orderfieldChk;
			$this->view->orderfield	=	$orderfieldChk; 
		}
		else
			$orderfield = '';

		$this->view->data = $this->deshboard->profanityfilter($seachfieldChk,$orderfieldChk);
		$this->view->totWords = 0; */
		
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$filter = new Zend_Filter_StripTags();
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$request = $this->getRequest()->getParams();
			if(isset($request['orderfield']) && $request['orderfield']!='')
			{
				$orderfieldChk = $request['orderfield'];
				$namespace->orderfield 	= 	$orderfieldChk;
				$orderfield	=	$orderfieldChk;
			}
			else
				$orderfield = '';
			
			$rs = $this->deshboard->profanityfilter($request['word'],$request['orderfield']);
		
			$result='';
			//$result .='<div class="proCloundlist">';
			if(count($rs)>0){
						foreach($rs as $row):
							$result .='<span filterId="'. $row['id'].'" class="dgrpUrsName" id="grpid_'.$row['id'].'">
								<i>'.$row['name'].'</i>
								<a class="removeCompare" href="javascript:void(0);">x</a>
							</span>';
						 endforeach;
							 }else{
							$result='<div class="dashBlockEmpty">No Records found</div>';
							}
					 
						// $result .='</div>';
			
			$data['content'] = $result;
			$data['total'] = count($rs);
			$data['status'] = $filterName;
		}
		else
		{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));		
		
		
	}

	public function loadfilterattrAction()
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$request 	= 	$this->getRequest()->getPost();
		$filter		=	new Admin_Model_Searchfilter();

		$filtAttr 	=	$filter->loadFilterAttr($request['filter_id']);
		//	print_r($filtAttr);
		$ret 	= '';
		$i 		=	0;
		foreach ($filtAttr as $key => $value) {
			# code...
			$i++;
		if($value['filt_attr_on']=='date')
		{
			$ret 	.= '<div class="whiteBox conditiontext chk_'.$i.'" > search  '. $this->getdbeeTyp($value['filt_attr_dbeetype']) .' Where '.$value['filt_attr_on'] .' '.$this->searchConditions($value['filt_attr_condition'],$chk='',$dbfield='',$dbValue='',$joined='').'<strong> '. $value['filt_attr_value'] .'</strong><a href="javascript:void()" class="closeCondition" id="'.$i.'"></a></div>';

			$ret 	.= '<span class="conditiontext txt_'.$i.'" style="display:none" ><input type="hidden" name="datearr[]" id="search_on_'.$i.'" value="'.$value['filt_attr_dbeetype'].'"><input type="hidden" name="datearr[]" id="search_for_'.$i.'" value="'.$value['filt_attr_on'].'"><input type="hidden" name="datearr[]" id="search_condition_'.$i.'" value="'.$value['filt_attr_condition'].'"><input type="hidden" name="datearr[]" id="search_value_'.$i.'" value="'.$value['filt_attr_value'].'"></span>';

			?>

		<?php
		}
		else if($value['filt_attr_on']=='description')
		{
			$ret 	.= '<div class="whiteBox conditiontext chk_'.$i.'" > search  '. $this->getdbeeTyp($value['filt_attr_dbeetype']) .' Where '.$value['filt_attr_on'] .' '.$this->searchConditions($value['filt_attr_condition'],$chk='',$dbfield='',$dbValue='',$joined='').'<strong> '. $value['filt_attr_value'] .'</strong><a href="#" class="closeCondition" id="'.$i.'"> </a></div>';

			$ret 	.= '<span class="conditiontext txt_'.$i.'" style="display:none" ><input type="hidden" name="tbldbees[]" id="search_on_'.$i.'" value="'.$value['filt_attr_dbeetype'].'"><input type="hidden" name="tbldbees[]" id="search_for_'.$i.'" value="'.$value['filt_attr_on'].'"><input type="hidden" name="tbldbees[]" id="search_condition_'.$i.'" value="'.$value['filt_attr_condition'].'"><input type="hidden" name="tbldbees[]" id="search_value_'.$i.'" value="'.$value['filt_attr_value'].'"></span>';

			?>

		<?php	

		}
		else
		{
			$ret 	.= '<div class="whiteBox conditiontext chk_'.$i.'" > search  '. $this->getdbeeTyp($value['filt_attr_dbeetype']) .' Where '.$value['filt_attr_on'] .' '.$this->searchConditions($value['filt_attr_condition'],$chk='',$dbfield='',$dbValue='',$joined='').' <strong>'. $value['filt_attr_value'] .'</strong><a href="#" class="closeCondition" id="'.$i.'"></a></div>';

			$ret 	.= '<span class="conditiontext txt_'.$i.'" style="display:none" ><input type="hidden" name="tblUsers[]" id="search_on_'.$i.'" value="'.$value['filt_attr_dbeetype'].'"><input type="hidden" name="tblUsers[]" id="search_for_'.$i.'" value="'.$value['filt_attr_on'].'"><input type="hidden" name="tblUsers[]" id="search_condition_'.$i.'" value="'.$value['filt_attr_condition'].'"><input type="hidden" name="tblUsers[]" id="search_value_'.$i.'" value="'.$value['filt_attr_value'].'"></span>';

			?>

		<?php	
		}
		}

		echo $ret;
		exit;

	}

	public function getfieldandcond($field,$cond)
	{

	}

	public function getdbeeTyp($type)
	{
		switch($type)
		{
			case '1' :
				return  ' all Text Post ';
				break;
			case '2' :
				return  ' all Link Post ';
				break;
			case '3' :
				return  ' all Pic Post ';
				break;
			case '4' :
				return  ' all Media Post ';
				break;
			case '5' :
				return  ' all Polls Post ';
				break;
			default :
				return  ' all  Post ';
			break;
		}
		//return ret;
	}

	/**
	 *  This function is responsible for Dynamic search produced on Dbee
	 *	Author @ Deepak Nagar
	 */
	public function savefilterAction()
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$request 	= 	$this->getRequest()->getPost();
		$filter		=	new Admin_Model_Searchfilter();

	
		$user_id	=	$_SESSION['Zend_Auth']['storage']['userid'];
		$icount		=	0;
		$str		=	'';
		$query		=	'';
		$searchOn	=	'';
		
		if(empty($request['filtname'][0]) || (isset($request['tblUsers']) && isset($request['tbldbees']) && isset($request['datearr'])) )
		{
			echo "1";
			exit;
		}
		else
		{
			$data		=	array('filter_name'=>$request['filtname'][0], 'filter_created_by'=>$user_id);
			$filter_id	= 	$filter->insertfilter($data);
		}

		foreach($request as $onTable => $val)
		{
			$icount++;
			$jcount		=	0;
			$totRows	=	count($val)/4;
			$k			=	0;
			$p			=	0;
			$q			=	0;
			$r			=	0;
			foreach($val as $data )
			{
				//echo $onTable.' ';
				if(!empty($onTable) && $onTable=='tblUsers')
				{
					//echo 'data = '.$jcount. ' , <br>';
					if($jcount==0 || $jcount==$k+4 )
					{
						$filt_attr_dbeetype		=	$data;
						$k= $jcount;
					}
						
					if($jcount==1 || $jcount==$p+4 )
					{
						$filt_attr_on		=	$data;

						$p= $jcount;
					}
						
					if($jcount==2 || $jcount==$q+4 )
					{
						$filt_attr_condition		=	$data;

						$q= $jcount;
					}
						
					if($jcount==3 || $jcount==$r+4 )
					{
						$filt_attr_value	=	$data;
						$r= $jcount;

						$tblUsersArr	=	array('filter_id'=>$filter_id,'filt_attr_dbeetype'=>$filt_attr_dbeetype, 'filt_attr_on'=>$filt_attr_on, 'filt_attr_condition'=>$filt_attr_condition,'filt_attr_value'=>$filt_attr_value );
							
						//print_r
						$filter->insertfilterattr($tblUsersArr);
							
					}
					$jcount++;
				}

				if(!empty($onTable) && $onTable=='tblDbees')
				{
					//echo 'data = '.$jcount. ' , <br>';
					if($jcount==0 || $jcount==$k+4 )
					{
						$filt_attr_dbeetype		=	$data;
						$k= $jcount;
					}
						
					if($jcount==1 || $jcount==$p+4 )
					{
						$filt_attr_on		=	$data;

						$p= $jcount;
					}
						
					if($jcount==2 || $jcount==$q+4 )
					{
						$filt_attr_condition		=	$data;

						$q= $jcount;
					}
						
					if($jcount==3 || $jcount==$r+4 )
					{
						$filt_attr_value	=	$data;
						$r= $jcount;

						$tblUsersArr	=	array('filter_id'=>$filter_id,'filt_attr_dbeetype'=>$filt_attr_dbeetype, 'filt_attr_on'=>$filt_attr_on, 'filt_attr_condition'=>$filt_attr_condition,'filt_attr_value'=>$filt_attr_value );
							
						//print_r
						$filter->insertfilterattr($tblUsersArr);
							
					}
					$jcount++;
				}

				if(!empty($onTable) && $onTable=='datearr')
				{
					//echo 'data = '.$jcount. ' , <br>';
					if($jcount==0 || $jcount==$k+4 )
					{
						$filt_attr_dbeetype		=	$data;
						$k= $jcount;
					}
						
					if($jcount==1 || $jcount==$p+4 )
					{
						$filt_attr_on		=	$data;

						$p= $jcount;
					}
						
					if($jcount==2 || $jcount==$q+4 )
					{
						$filt_attr_condition		=	$data;

						$q= $jcount;
					}
						
					if($jcount==3 || $jcount==$r+4 )
					{
						$filt_attr_value	=	$data;
						$r= $jcount;

						$tblUsersArr	=	array('filter_id'=>$filter_id,'filt_attr_dbeetype'=>$filt_attr_dbeetype, 'filt_attr_on'=>$filt_attr_on, 'filt_attr_condition'=>$filt_attr_condition,'filt_attr_value'=>$filt_attr_value );
							
						//print_r
						$filter->insertfilterattr($tblUsersArr);
							
					}
					$jcount++;
				}
			}
		}
		
		$getFilters	=	$filter->loadFilters($user_id);

		
		$ret = '';
		//$ret .= '<select name="loadFilter"><option value="">SELECT</option>';
		foreach ($getFilters as $key => $value)
		{
			$ret .=  "<option value='".$value['filter_id']."'>".$value['filter_name']."</option>";
		}

		echo  $ret;
		exit;
	}

	/**
	 *  This function is responsible for Dynamic search produced on Dbee
	 *	Author @ Deepak Nagar
	 */

	public function scoresearchAction()
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$request = $this->getRequest()->getPost();

		$deshboard = new Admin_Model_Deshboard();
		$deshObj	= new Admin_Model_Deshboard();

		if(!empty($request['score_owner']))
		{
			$ownername =	" && (owner.Name like '%".$request['score_owner']."%')";
		}

		if(!empty($request['score_by']))
		{
			$username =	" && (u.Name like '%".$request['score_by']."%')";
		}

		if(!empty($request['score_type']))
		{
			$type =	" && (score.Score = '".$request['score_type']."')";
		}



		if(!empty($request['created_from']) && !empty($request['created_to']))
		{
			//$date =	" && (group.GroupDate  >= '".$request['created_from']."'  || group.GroupDate  <= '".$request['created_to']."')";
			$date =	" && (score.ScoreDate  BETWEEN   '".$request['created_from']."' AND  '".$request['created_to']."')";
		}

		$query  = "SELECT `score`.`Type` AS `type`, `score`.`Score`, `score`.`ScoreID`, `score`.`ID`,`score`.`ScoreDate`, `u`.`UserID`, `u`.`Name` AS `username`, `u`.`ProfilePic` AS `image`, `owner`.`UserID` AS `OwnerID`, `owner`.`Name` AS `Ownername`, `owner`.`ProfilePic` AS `Ownerimage` FROM `tblScoring` AS `score` LEFT JOIN `tblUsers` AS `u` ON u.UserID=score.UserID INNER JOIN `tblUsers` AS `owner` ON owner.UserID=score.Owner WHERE (u.Status = '1') AND (clientID=".clientID.") ".$ownername." ".$username." ".$type." ".$date." order by  ScoreDate DESC";

		$livescoresData = $deshboard->SearchDbees($query);
		$ret	='';
		$load	=0;
		$ret .= '<ul class="listing scoredList"  id="listingResults">';
		if (count($livescoresData)){
			foreach($livescoresData as $liveScore) :
				
			$dbtype	='';
			$dbtype1='';
			$descDisplay	='';

			if($liveScore->type==1)
			{
				$dbdetails	=	$deshObj->getScoredbinfo($liveScore['type'],$liveScore['ScoreID']);
				$dbtype1	=	' post';
				$dbdesc		=	$dbdetails[0]['description'];
				$viewsco = '<a href="'.BASE_URL.'/dbee/'.$dbdetails[0]['dburl'].'"> - view </a>';
					
					
			} else {
				$dbdetails	=	$deshObj->getScoredbinfo($liveScore['type'],$liveScore['ScoreID']);
				$dbtype1	=	' comment ';
				$dbdesc		=	$dbdetails[0]['Comment'];
				$viewsco = '<a href="'.BASE_URL.'/dbeedetail/home/id/'.$dbdetails[0]['DbeeID'].'"> - view </a>';
			}
				
				
			if($dbdetails[0]['type']==1) {
				//$dbtype			=	'text db<div class="icon-db-text"></div>';
				$descDisplay	=	'<div class="dbTextPoll">'.substr($dbdesc,0,100).'</div>';
			}
			if($dbdetails[0]['type']==2) {
				//$dbtype			=	'link db<div class="icon-db-link"></div>';
				$descDisplay	='';
				$dbLink			=	$dbdetails[0]['Link'];
				$dbLinkTitle	=	$dbdetails[0]['LinkTitle'];
				$dbLinkDesc		=	$dbdetails[0]['LinkDesc'];
				$dbUserLinkDesc	=	!empty($dbdetails[0]['UserLinkDesc']) ? $dbdetails[0]['UserLinkDesc'] : $dbdetails[0]['LinkTitle'];

				$descDisplay	=	'<div class="dbLinkDesc">'.$dbUserLinkDesc.' -
				<a target="_blank" href="'.$dbLink.'">'.$dbLink.'</a>
				</div>';
			}

			if($dbdetails[0]['type']==3) {
				//$dbtype			=	'link db<div class="icon-db-link"></div>';
				$descDisplay	='';

				$dbPic		=	$dbdetails[0]['Pic'];
				$dbPicDesc	=	$dbdetails[0]['PicDesc'];

				$descDisplay	.=	'<div class="dbPicText">';
				if($dbPic!='')
				{
                    $checkdbpic = $this->defaultimagecheck->checkImgExist($dbPic,'imageposts','default-avatar.jpg');
                    $descDisplay	.=	'<div class="dbPic" ><a href="#"><img src="'.IMGPATH.'/imageposts/small/'.$checkdbpic.'" width="50" border="0" /></a></div>';
				}
				else{
					$noPic = 'noPix';
				}

				$descDisplay	.=	'<div class="dbPicDesc '.$noPix.'">'.substr($dbPicDesc,0,100).'</div></div>';

			}

			if($dbdetails[0]['type']==4) {
				//$dbtype	=	'media db<div class="icon-db-vidz"></div>';
				$descDisplay	='';
				$dbVid			=	$dbdetails[0]['VidID'];
				$dbVidDesc		=	$dbdetails[0]['VidDesc'];
				$dbLinkDesc		=	$dbdetails[0]['LinkDesc'];
				$dbUserLinkDesc	=	!empty($dbdetails[0]['UserLinkDesc']) ? $dbdetails[0]['UserLinkDesc'] : $dbdetails[0]['LinkTitle'];

				$descDisplay	.=	'<div class="dbPicText">';
				if($dbVid!='')
				{
					$descDisplay	.=	'<div class="dbPic" ><a href="javascript:void(0)"><img width="50" height="60" border="0" src="https://i.ytimg.com/vi/'.$dbVid.'/0.jpg"></a></div>';
				}
				else{
					$noPic = 'noPix';
				}
				$descDisplay	.=	'<div class="dbPicDesc '.$noPix.'">'.substr($dbVidDesc,0,100).'</div></div>';
			}
			if($dbdetails[0]['type']==5) {
				$dbPollText			=	$dbdetails[0]['PollText'];
				//$dbtype	=	'polls <div class="icon-db-poll"></div>';
				$descDisplay	=	'<div class="dbTextPoll">'.substr($dbPollText,0,100).'</div>';
			}

				
				
			if($liveScore['Score']==1){
				$scorediv	=	'<span class="scoreSprite scoreLove"></span>';
			} else if($liveScore['Score']==2){
				$scorediv	=	'<span class="scoreSprite scoreLike"></span>';
			} else if($liveScore['Score']==3){
				$scorediv	=	'<span class="scoreSprite scoreFft"></span>';
			} else if($liveScore['Score']==4){
				$scorediv	=	'<span class="scoreSprite scoreUnLike"></span>';
			} else if($liveScore['Score']==5){
				$scorediv	=	'<span class="scoreSprite scoreHate"></span>';
			}
				
			$checklivescorepic = $this->defaultimagecheck->checkImgExist($liveScore['image'],'userpics','default-avatar.jpg');
            $checkownerpic = $this->defaultimagecheck->checkImgExist($liveScore['Ownerimage'],'userpics','default-avatar.jpg');
			$ret .= '<li>
			<div class="listUserPhoto">
			<div class="scoredUserPic">
			<img src="'.IMGPATH.'/users/small/'.$checklivescorepic.'" width="40" height="40" border="0" />
			<span class="arrowLinkTo"></span>
			</div>
			
			<img src="'.IMGPATH.'/users/medium/'.$checkownerpic.'"  width="80" height="80" border="0" class="recievedUsePic"/>
			<!--End User profile pic  who scored someone-->

			</div>
			<div class="dataListWrapper">
			<div class="dataListbox">
			<div class="scoredListTitle">
			<a href="#">'.$liveScore['username'].'</a> scored
			<a href="#">'.$liveScore['Ownername'].'&rsquo;s</a>  '.$dbtype.' '. $dbtype1.' '.$view.'
			</div>
			<div class="scoredData">'. $descDisplay.'</div>
			</div>
			<div class="scoredPostDate"> '. date('d M y',strtotime($liveScore['ScoreDate'])).'</div>
			</div>


			</li>';
			$rowchange++; endforeach;
				
		} else {
			$ret .= '<h2 class="notfound message error">Matching records Not Found! <br> Please change your search criteria and try again</h2>';
		}
		$ret .= '</ul>';

		echo $ret ;
	}

	public function commentssearchAction()
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$request = $this->getRequest()->getPost();

		$deshboard= new Admin_Model_Deshboard();

		if(!empty($request['cmnt_owner']))
		{
			$ownername =	" && (owner.Name like '%".$request['cmnt_owner']."%')";
		}

		if(!empty($request['cmnt_by']))
		{
			$username =	" && (u.Name like '%".$request['cmnt_by']."%')";
		}

		if(!empty($request['cmnt_type']))
		{
			$type =	" && (commt.Type = '".$request['cmnt_type']."')";
		}



		if(!empty($request['created_from']) && !empty($request['created_to']))
		{
			//$date =	" && (group.GroupDate  >= '".$request['created_from']."'  || group.GroupDate  <= '".$request['created_to']."')";
			$date =	" && (commt.CommentDate  BETWEEN   '".$request['created_from']."' AND  '".$request['created_to']."')";
		}

		$query  = "SELECT `commt`.`DbeeID`, `commt`.`Type` AS `type`, `commt`.`Comment`, `commt`.`Link`, `commt`.`LinkTitle`, `commt`.`LinkDesc`, `commt`.`UserLinkDesc`, `commt`.`Pic`, `commt`.`PicDesc`, `commt`.`Vid`, `commt`.`VidDesc`, `commt`.`VidSite`, `commt`.`VidID`, `commt`.`Audio`, `commt`.`CommentDate`, `u`.`UserID`, `u`.`Name` AS `username`, `u`.`ProfilePic` AS `image`, `owner`.`UserID` AS `OwnerID`, `owner`.`Name` AS `Ownername`, `owner`.`ProfilePic` AS `Ownerimage` FROM `tblDbeeComments` AS `commt` INNER JOIN `tblUsers` AS `u` ON u.UserID=commt.UserID INNER JOIN `tblUsers` AS `owner` ON owner.UserID=commt.DbeeOwner INNER JOIN `tblDbees` AS `dbee` ON dbee.DbeeID = commt.DbeeID WHERE (u.Status = '1') AND (clientID=".clientID.")  ".$ownername." ".$username." ".$type." ".$date."   ORDER BY `commt`.`CommentDate` DESC ";
		$livecmntsData = $deshboard->SearchDbees($query);
		$ret	='';
		$load	=0;
		$ret .= '<ul class="listing scoredList "  id="listingResults">';
		if (count($livecmntsData)){
			foreach($livecmntsData as $latestComment) :
			$viewcmm = '<a href="'.BASE_URL.'/dbeedetail/home/id/'.$latestComment['DbeeID'].'"> - view </a>';	
			$ret .= '<li>';
			$dbtype	='';
			$descDisplay	='';
			if($latestComment['type']==1) {
				$dbtype			=	'text post<div class="icon-db-text"></div>';
				$descDisplay	=	'<div  class="dbTextPoll">'.$latestComment['Comment'].'</div>';
			}
			if($latestComment['type']==2) {
				$dbtype			=	'link post<div class="icon-db-link"></div>';
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
				$dbtype			=	'link post<div class="icon-db-link"></div>';

				$dbPic		=	$latestComment['Pic'];
				$dbPicDesc	=	$latestComment['PicDesc'];

				$descDisplay	.=	'<div class="dbPicText">';
				if($dbPic!='')
				{
					$checkdbpic = $this->defaultimagecheck->checkImgExist($dbPic,'imageposts','default-avatar.jpg');
					$descDisplay	.=	'<div class="dbPic" ><a href="#"><img src="'.IMGPATH.'/imageposts/small/'.$checkdbpic.'" width="50" border="0" /></a></div>';
					
				}
					
				$descDisplay	.=	'<div class="dbPicDesc">'.$dbPicDesc.'</div></div>';
			}
			if($latestComment['type']==4) {
				$dbtype	=	'media post<div class="icon-db-vidz"></div>';

				$dbtype			=	'link post<div class="icon-db-link"></div>';
				$dbVid			=	$latestComment['VidID'];
				$dbVidDesc		=	$latestComment['VidDesc'];
				$dbLinkDesc		=	$latestComment['LinkDesc'];
				$dbUserLinkDesc	=	!empty($latestComment['UserLinkDesc']) ? $latestComment['UserLinkDesc'] : $latestComment['LinkTitle'];

				$descDisplay	.=	'<div class="dbPicText">';
				if($dbVid!='')
				{
					$descDisplay	.=	'<div class="dbPic" ><a href="javascript:void(0)"><img width="50" height="60" border="0" src="https://i.ytimg.com/vi/'.$dbVid.'/0.jpg"></a></div>';
				}
					
				$descDisplay	.=	'<div class="dbPicDesc">'.$dbVidDesc.'</div></div>';
			}
			if($latestComment['type']==5) {
				$dbPollText			=	$latestComment->PollText;
				$dbtype	=	'polls <div class="icon-db-poll"></div>';
				$descDisplay	=	'<div  class="dbTextPoll">'.$dbPollText.'</div>';
			}
            $checklatestcommpic = $this->defaultimagecheck->checkImgExist($latestComment['image'],'userpics','default-avatar.jpg');
			$ret .= '<div class="listUserPhoto">
			<img src="'.IMGPATH.'/users/medium/'.$checklatestcommpic.'" width="70" height="70" border="0" class="recievedUsePic"/>	
				
			</div>
			<div class="dataListWrapper">
			<div class="dataListbox">
			<div class="scoredListTitle">
			<a href="javascript:void(0)" class="show_details_user" userid="'.$latestComment['UserID'].'">'.$this->myclientdetails->customDecoding($latestComment['username']).'</a>
			commented on  </span><a href="javascript:void(0)" class="show_details_user" userid="'.$latestComment['OwnerID']	.'">'.$this->myclientdetails->customDecoding($latestComment['Ownername']).'&rsquo;s </a>
			'.$viewcmm.'</div>
			<div class="scoredData">'.$descDisplay.'</div>
			</div>
			<div class="scoredPostDate">Commented on  - '. date('d M y',strtotime($latestComment['CommentDate'])).' </div>
			</div>
				
			</li>';
			$rowchange++; endforeach;
				
		} else {
			$ret .= '<h2 class="notfound message error">Matching records Not Found! <br> Please change your search criteria and try again</h2>';
		}
		$ret .= '</ul>';

		echo $ret ;
	}




	public function groupsearchAction()
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$request = $this->getRequest()->getPost();

		$deshboard= new Admin_Model_Deshboard();

		if(!empty($request['group_name']))
		{
			$name =	" && (group.GroupName like '%".$request['group_name']."%')";
		}

		if(!empty($request['group_user_name']))
		{
			$name =	" && (u.Name like '%".$request['group_user_name']."%')";
		}

		if(!empty($request['group_privacy']))
		{
			$type =	" && (group.GroupPrivacy = '".$request['group_privacy']."')";
		}

		if(!empty($request['group_type']))
		{
			$type =	" && (group.GroupType = '".$request['group_type']."')";
		}

		if(!empty($request['created_from']) && !empty($request['created_to']))
		{
			$date =	" && (group.GroupDate  BETWEEN   '".$request['created_from']."' AND  '".$request['created_to']."')";
		}

		$query	=	"select `group`.`ID`, `group`.`GroupName`, `group`.`GroupPic`, `group`.`GroupDesc` AS `description`, `group`.`GroupType` AS `type`, `group`.`GroupDate` AS `Gdate`, `group`.`GroupTypeOther`, `gt`.`TypeName`, `u`.`UserID`, `u`.`Name` AS `username`, `u`.`ProfilePic` AS `image`  from `tblGroups` AS `group` INNER JOIN `tblGroupTypes` AS `gt` ON gt.TypeID = group.GroupType INNER JOIN `tblUsers` AS `u` ON u.UserID=group.User where (u.Status = '1') AND (clientID = ".clientID." ) ".$name." ".$type." ".$date."  ORDER BY `group`.`GroupDate` DESC ";


		$liveDbeeData = $deshboard->SearchDbees($query);

		$ret	='';
		$load	=0;

		$ret .= '<ul class="listing listingGroup scoredList" id="listingResults">';
		if (count($liveDbeeData)){
			foreach($liveDbeeData as $liveGroup) :
			$liveGrouppic = $this->defaultimagecheck->checkImgExist($liveGroup['image'],'userpics','default-avatar.jpg');
			$ret .= '<li>
			<div class="listUserPhoto">
			<!--User profile pic  who scored someone-->
			<img src="'.IMGPATH.'/users/medium/'.$liveGrouppic.'" width="90" height="90" border="0" class="recievedUsePic" />
			
			<!--End User profile pic  who scored someone-->
			</div>
			<div class="dataListWrapper">
			<div class="dataListbox">
			<div class="scoredListTitle">
			<a class="show_details_user" userid="'. $liveGroup['UserID'].'" href="javascript:void(0)"  style="font-weight:normal">'.$liveGroup['GroupName'].'</a>
			created by&nbsp;<a class="show_details_user" userid="'.  $liveGroup['UserID'].'" href="javascript:void(0)" >'. $this->myclientdetails->customDecoding($liveGroup['username']).'</a> <span class="titleListDate">On '. date('d M y',strtotime($liveGroup['Gdate'])).'</span>
			<a href="'.BASE_URL.'/group/groupdetails/group/'.$liveGroup['ID'].'"> - view</a></div>';
				
			if($liveGroup['description']) {
				$ret .= '	<div class="scoredData">'.htmlentities(substr($liveGroup['description'],0,100)).'
				</div>';
			}
			$ret .= '</div>

			<div class="scoredPostDate">'.$liveGroup['TypeName'].'</div>
			</div>

			</li>';
			$rowchange++; endforeach;
				
		} else {
			$ret .= '<h2 class="notfound message error">Matching records Not Found! <br> Please change your search criteria and try again</h2>';
		}
		$ret .= '</ul>';
		$ret .= '</ul>';

		echo $ret ;

	}

	/**
	 *  This function is responsible for Dynamic search produced on Dbee
	 *	Author @ Deepak Nagar
	 */
	public function generatesearchAction()
	{

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$request = $this->getRequest()->getPost();

		$deshboard= new Admin_Model_Deshboard();

		$icount		=	0;
		$str		=	'';
		$query		=	'';
		$searchOn	=	'';
		foreach($request as $onTable => $val)
		{
			$icount++;
			$jcount		=	0;
			$totRows	=	count($val)/4;
			$k			=	0;
			$p			=	0;
			$q			=	0;
			$r			=	0;
				
			$pcount		=	0;
			$pk			=	0;
			$pp			=	0;
			$pq			=	0;
			$pr			=	0;
				
			$qcount		=	0;
			$qk			=	0;
			$qp			=	0;
			$qq			=	0;
			$qr			=	0;
				
			foreach($val as $data )
			{

				if(!empty($onTable) && $onTable=='tblUsers')
				{
					//echo 'data = '.$jcount. ' , <br>';
					if($jcount==0 || $jcount==$k+4 )
					{
						$dbType		=	$data;
						$fieldname	= 	'';
						$k= $jcount;
					}
						
					if($jcount==1 || $jcount==$p+4 )
					{
						$usrRec		=	$data;
						$dbfield	=	'';
						switch($usrRec)
						{
							case 'user_name':
								$dbfield	=	'Name';
								break;
							case 'user_email':
								$dbfield	=	'Email';
								break;
						}
						//echo $dbfield;
						$p= $jcount;
					}
						
					if($jcount==2 || $jcount==$q+4 )
					{
						$condi		=	$data;
						$dbcondi	=	'';
						$dbcondi	=	$this->searchConditions($condi,'conditiononly');
						//echo $dbcondi;
						$q= $jcount;
					}
						
					if($jcount==3 || $jcount==$r+4 )
					{
						$dbValue	=	'';
						$dbValue	=	$data;
						$r= $jcount;

						$dbsyntax	=	$this->searchConditions($condi,'syntax',$dbfield,$dbValue,'tblUsers');
							
						$userstr	.=	"($dbsyntax && tblDbees.Type = $dbType) &&";
							
					}
					$jcount++;
				}


				if(!empty($onTable) && $onTable=='tblDbees')
				{
					if($pcount==0 || $pcount==$pk+4 )
					{
						$dbType		=	$data;
						$fieldname	= 	'';
						$fieldname	=   $this->dbtypeFields($dbType);
						$pk= $pcount;
					}
						
					if($pcount==1 || $pcount==$pp+4 )
					{
						$usrRec		=	$data;

						$pp= $pcount;
					}
						
					if($pcount==2 || $pcount==$pq+4 )
					{
						$condi		=	$data;
						$dbcondi	=	$this->searchConditions($condi);

						//echo $dbcondi;
						$pq= $pcount;
					}
						
					if($pcount==3 || $pcount==$pr+4 )
					{
						$dbValue	=	'';
						$dbValue	=	$data;
						$pr= $pcount;

						$dbsyntax	=	$this->searchConditions($condi,'syntax',$fieldname,$dbValue,'tblDbees');
						$dbeestr	.=	"($dbsyntax && tblDbees.Type = $dbType) &&";
					}
					$pcount++;
				}


				if(!empty($onTable) && $onTable=='datearr')
				{
					if($qcount==0 || $qcount==$qk+4 )
					{
						$dbType		=	$data;
						$fieldname	= 	'';
						$fieldname	=   'PostDate';
						$qk= $qcount;
					}
						
					if($qcount==1 || $qcount==$qp+4 )
					{
						$usrRec		=	$data;

						$qp= $qcount;
					}
						
					if($qcount==2 || $qcount==$qq+4 )
					{
						$condi		=	$data;
						$dbcondi	=	$this->searchConditions($condi);

						//echo $dbcondi;
						$qq= $qcount;
					}
						
					if($qcount==3 || $qcount==$qr+4 )
					{
						$dbValue	=	'';
						$dbValue	=	$data;
						$qr= $qcount;
						$dbsyntax	=	$this->searchConditions($condi,'syntax',$fieldname,$dbValue,'tblDbees');
						$datestr	.=	"($dbsyntax && tblDbees.Type = $dbType) &&";
							
					}
					$qcount++;
				}

			}
				
			$str1 = $str2=$str3	= $table1 = $table2	= $joined= '';
				
			if(!empty($userstr))
			{
				$fuserstr	= trim($userstr,' &&');

				$str1		=  '('.$fuserstr.') &&';

				$table1		=	'tblUsers ,';
			}
			if(!empty($dbeestr))
			{
				$fuserstr	= trim($dbeestr,' &&');
				$str2		.=  '('.$fuserstr.') &&';

				$table2		=	'tblDbees';
			}
			if(!empty($datestr))
			{
				$fdatestr	= trim($datestr,' &&');
				$str3		.=  '('.$fdatestr.') ';

				$table2		=	'tblDbees';
			}
				
			if(empty($table2))
			{
				$table1	= trim($table1,' ,');
				$table1	= 'tblUsers,tblDbees';

				$joined	=	'(tblUsers.UserID=tblDbees.User) && ';
			}
				
			if(!empty($table2) && !empty($table1))
			{
				$joined	=	'(tblUsers.UserID=tblDbees.User) && ';
			}
				
			$str		=	$str1.' '.$str2 .' '.$str3;
			$fstr		= 	trim($str,' &&');
				
			$final		=	'('.$fstr.')';
				
			$tableName	=	$table1.''.$table2;
		}


		 $query	=	"select * from tblUsers,tblDbees where (tblUsers.UserID=tblDbees.User) && ".$final;
		//	exit;
		$liveDbeeData = $deshboard->SearchDbees($query);
		$this->view->paginator	=	$liveDbeeData;

		$ret	='';
		$load	=0;
		$ret .= '<ul class="listing scoredList" id="listingResults">';
			
		if (count($liveDbeeData)){
		 foreach($liveDbeeData as $liveDbee) :
			$load++;
			$ret .= '<li>';
				
			$descDisplay	='';

			if($liveDbee['Type']==1) {
				$dbtype			=	'text post';
				$dbtypeIcon	=	'<div class="pstype typeText"></div>';
				$descDisplay	=	'<div class="dbLinkDesc">'.$liveDbee['Text'].'</div>';//substr($liveDbee['Text'],0,100).'</div>';
			}
			if($liveDbee['Type']==2) {
				$dbtype			=	'link post';
				$dbtypeIcon	=	'<div class="pstype typeLink"></div>';
				$dbLink			=	$liveDbee['Link'];
				$dbLinkTitle	=	$liveDbee['LinkTitle'];
				$dbLinkDesc		=	$liveDbee['LinkDesc'];
				$dbUserLinkDesc	=	!empty($liveDbee['UserLinkDesc']) ? $liveDbee['UserLinkDesc'] : $liveDbee['LinkTitle'];

				$descDisplay	=	'<div class="dbLinkDesc">'.$dbUserLinkDesc.' -
				<a target="_blank" href="'.$dbLink.'">'.$dbLink.'</a>
				</div>';
			}
			if($liveDbee['Type']==3) {
				$dbtype	=	'pix post';
				$dbtypeIcon	=	'<div class="pstype typePix"></div>';
				$dbPic		=	$liveDbee['Pic'];
				$dbPicDesc	=	$liveDbee['PicDesc'];
				if($dbPic!='')
				{
					$checkdbpic = $this->defaultimagecheck->checkImgExist($dbPic,'imageposts','default-avatar.jpg');
					$descDisplay	.=	'<div class="dbPic"><a href="#"><img src="'.IMGPATH.'/imageposts/small/'.$checkdbpic.'" width="50" border="0" /></a></div>';
					
				}
				else{

					$noPic = 'noPix';
				}
				$descDisplay	.=	'<div class="dbPicDesc '.$noPic.'">'.$dbPicDesc.'</div>';//substr($dbPicDesc,0,100).'</div>';
			}
			if($liveDbee['Type']==4) {
				$dbtype	=	'media post';
				$dbtypeIcon	=	'<div class="pstype typeVideo"></div>';
				$dbVid			=	$liveDbee['VidID'];
				$dbVidDesc		=	$liveDbee['VidDesc'];
				$dbLinkDesc		=	$liveDbee['LinkDesc'];
				$dbUserLinkDesc	=	!empty($liveDbee['UserLinkDesc']) ? $liveDbee['UserLinkDesc'] : $liveDbee['LinkTitle'];

				$descDisplay	.=	'<div class="dbPicText">';
				if($dbVid!='')
				{
					$descDisplay	.=	'<div class="dbPic" ><a href="javascript:void(0)"><img width="50" height="50" border="0" src="https://i.ytimg.com/vi/'.$dbVid.'/0.jpg"></a></div>';
				}
				else{
					$noPic = 'noPix';
				}
				$descDisplay	.=	'<div class="dbPicDesc '.$noPic.'">'.$dbVidDesc.'</div></div>';
					
					
			}
			if($liveDbee['Type']==5) {
				$dbtype	=	'polls';
				$dbtypeIcon	='<div class="pstype typePoll"></div>';
				$descDisplay	=	'<div class="dbTextPoll">'.$liveDbee['PollText'].'</div>';
			}
			$liveDbeepic = $this->defaultimagecheck->checkImgExist($liveDbee['ProfilePic'],'userpics','default-avatar.jpg');	
			$ret .='<div class="listUserPhoto">
			<img src="'.IMGPATH.'/users/small/'.$liveDbeepic.'" width="50" height="50" border="0" />
			
			</div>
			<div class="dataListWrapper">
			<div class="dataListbox">
			<div class="scoredListTitle">
			'. $dbtypeIcon.' <a class="show_details_user" userid="'.$liveDbee['UserID'].'" href="javascript:void(0)">'.$liveDbee['Name'].'</a>
			has posted a [asr] </span>'.$dbtype.'
			</div>
			<div class="scoredData">
			<div class="dbPost">'.$descDisplay.'&nbsp;</div>';
			if($liveDbee['twitter']) {
				$ret .='<div class="twitterbird-dbee-feed dbPost">
				<span class="twitterbird scoreSprite "></span>'.htmlentities(substr($liveDbee->twitter,0,100)).'
				</div>	';
			}
			$ret .='</div>
			</div>
			<div class="scoredPostDate">Commented on  - '.date('d M y',strtotime($latestComment['CommentDate'])).'</div>
			</div> </li>';


			$rowchange++; endforeach;

		} else {
			$ret .= '<h2 class="notfound message error">Matching records Not Found! <br> Please change your search criteria and try again</h2>';
		}
		$ret .= '</ul>';
		echo $ret ;
		//print_r($liveDbeeData);

	}

	public function dbtypeFields($dbType)
	{
		switch($dbType)
		{
			case 1 :
				$fieldname	=	'Text';
				break;
			case 2 :
				$fieldname	=	'UserLinkDesc';
				break;
			case 3 :
				$fieldname	=	'PicDesc';
				break;
			case 4 :
				$fieldname	=	'VidDesc';
				break;
			case 5 :
				$fieldname	=	'PollText';
				break;
		}
		return $fieldname;
	}

	public function searchConditions($condition,$chk='',$dbfield='',$dbValue='',$joined='')
	{

		if($chk	== 'syntax')
		{
			switch($condition)
			{
				case 'eq':
					$dbcondi	=	$joined.'.'.$dbfield." = ".'"'.$dbValue.'"';
					break;
				case 'like':
					$dbcondi	=	$joined.'.'.$dbfield." like ".'"%'.$dbValue.'%"';
					break;
				case 'sameas':
					$dbcondi	=	$joined.'.'.$dbfield." = ".'"'.$dbValue.'"';
					break;
				case 'before':
					$dbcondi	=	$joined.'.'.$dbfield." <= ".'"'.$dbValue.'"';
					break;
				case 'after':
					$dbcondi	=	$joined.'.'.$dbfield." >= ".'"'.$dbValue.'"';
					break;
			}
		}
		else
		{
			switch($condition)
			{
				case 'eq':
					$dbcondi	=	'=';
					break;
				case 'like':
					$dbcondi	=	'like';
					break;
				case 'sameas':
					$dbcondi	=	'=';
					break;
				case 'before':
					$dbcondi	=	'<';
					break;
				case 'after':
					$dbcondi	=	'>';
					break;
			}
		}

		return $dbcondi;
	}



	public function dialogAction()
	{
		$adminObj	=	new Admin_Model_admin();
		$admres		=	$adminObj->getAdmin();

		$request = $this->getRequest()->getParams();

		print_r($request);
			
		$this->view->adminData = $admres;
	}
	public function addadminAction()
	{
		$adminObj	=	new Admin_Model_admin();

		$request 	= 	$this->getRequest()->getParams();

		$admres		=	$adminObj->addAdmin();



		print_r($request);
			
		$this->view->adminData = $admres;
		exit;
	}
	public function indexAction()
	{
		$this->_helper->layout()->setLayout('layoutnomenu');
		$request = $this->getRequest()->getParams();
		if(isset($request['searchfield']) && $request['searchfield']!='')
			$seachfield = $request['searchfield'];
		else
			$seachfield = '';

		$deshboard= new Admin_Model_Deshboard();
		$liveDbeeData = $deshboard->getLiveDbee(3);
		$this->view->liveDbeeData = $liveDbeeData;

		$liveGroupData = $deshboard->getLiveGroup(3);
		$this->view->liveGroupData = $liveGroupData;

		$latestCommentData = $deshboard->getLatestComment(3);
		$this->view->latestCommentData = $latestCommentData;

		$liveScoreData = $deshboard->getLiveScore(3);
		$this->view->liveScoreData = $liveScoreData;

	}
	/* list dbee's*/

	public function specialdbsAction()
	{
		define(PAGE_NUM,30);
		$request = $this->getRequest()->getParams();		
		$start = $request['start']?$request['start']:'0';
		$deshboard 	= 	new Admin_Model_Deshboard();
		$this->view->invite = $this->_request->getParam('invite');
		$this->view->type = $this->_request->getParam('type');
		$spdbs = $deshboard->specialdbs($start,500);
		
		$spdbstotal = $deshboard->specialdbstotal($start,500);
		
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

		$this->view->postID = $this->session_name_space->postid;
		$this->view->timezonelist = $timezonelist;
		$this->view->spdbs = $spdbs;
		$this->view->spdbstotal = $spdbstotal;
		$this->view->start = $start;
		

		require_once 'Google/Google/autoload.php';
		require_once 'Google/Google/Client.php';
		require_once 'Google/Google/Service/YouTube.php';

		session_start();
		$OAUTH2_CLIENT_ID = youtubeapi;
		$OAUTH2_CLIENT_SECRET = youtubescret;

		$client = new Google_Client();
		$client->setClientId($OAUTH2_CLIENT_ID);
		$client->setClientSecret($OAUTH2_CLIENT_SECRET);
		$client->setScopes('https://www.googleapis.com/auth/youtube');
		$client->setRedirectUri(BASE_URL.'/admin/dashboard/specialdbs');

		// Define an object that will be used to make all API requests.
		$youtube = new Google_Service_YouTube($client);

		if (isset($_GET['code'])) 
		{
		  if (strval($_SESSION['state']) !== strval($_GET['state'])) {
		    die('The session state did not match.');
		  }
		  $client->authenticate($_GET['code']);
		  $_SESSION['token'] = $client->getAccessToken();
		  $_SESSION['message'] = 1;
		  $redirect = BASE_URL.'/admin/dashboard/specialdbs/task/create';
		  header('Location: ' . $redirect);
		}

		if (isset($_SESSION['token'])) 
		{
		   $client->setAccessToken($_SESSION['token']);
	      	$this->view->messageYoutube = 'Your Google account has been authenticated successfully. You can now schedule Youtube videos for broadcasting.';
		}
		
		if ($client->getAccessToken()) 
		{
	    	$_SESSION['token'] = $client->getAccessToken();
	      	$this->view->youtubeLogin = 1;
	      	$this->view->messageYoutube = 'Your Google account has been authenticated successfully. You can now schedule Youtube videos for broadcasting.';
		} 
		else 
		{
	      $state = mt_rand();
	      $client->setState($state);
	      $_SESSION['state'] = $state;
	      $this->view->authUrl = $client->createAuthUrl();
	      $this->view->youtubeLogin = 0;
	    }
	    $this->view->youtubeStatus = 0;


	}

	public function youtubeApi($youtube,$videoId)
	{

		try{
		    // Call the API's videos.list method to retrieve the video resource.
		    //$listResponse = $youtube->videos->listVideos("snippet",
		    $listResponse = $youtube->videos->listVideos("contentDetails",
		        array('id' => $videoId));

		    // If $listResponse is empty, the specified video was not found.
		    if (empty($listResponse)) {
		      $htmlBody .= sprintf('<h3>Can\'t find a video with video id: %s</h3>', $videoId);
		    } else {
		      // Since the request specified a video ID, the response only
		      // contains one video resource.
		      $video = $listResponse[0];
		      $duration_string = $video['contentDetails']['duration'];
		      $pattern = '/PT(([0-9]+)M)?(([0-9]+)S)?/';
		      preg_match($pattern, $duration_string, $matches);
		      if(!empty($matches[2]))
		        $mins = $matches[2];
		      else
		        $mins = 0;
		      
		      if(!empty($matches[4]))
		        $secs = $matches[4];
		      else
		        $secs = 0;
		    return ($mins*60+$secs); 

		  }
		} catch (Google_ServiceException $e) {
			unset($_SESSION['token']);
			echo $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
		  	htmlspecialchars($e->getMessage()));
		} catch (Google_Exception $e) {
			unset($_SESSION['token']);
			echo $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
		  	htmlspecialchars($e->getMessage()));
		}
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
    
	public function castfilterAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$filter = new Zend_Filter_StripTags();
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$type = $this->_request->getPost('type');
			$result = $this->deshboard->getNotification($this->_userid,$type,'','','');
			require_once 'includes/globalfileadmin.php';
			$data['postlist'] = $this->view->partial('partials/ajaxnotify.phtml', array('result'=>$result,'socialInvite'=>$this->socialInvite,'myclientdetails'=>$this->myclientdetails,'activityaMsg'=>$activityaMsg,'common_model'=>$this->common_model,'type'=>$type,'deshboard'=>$this->deshboard));
		}
		else
		{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));
	}
	
	public function ajaxsimilaruserAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$filter = new Zend_Filter_StripTags();
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$request = $this->getRequest()->getPost();	
			$result =	$this->dash_obj->answerSimilaruserlist($request['answerid'],$request['ownerdataid']);
			$content = '';
			$u = new Admin_Model_Usergroup();
			$grouprs = $u->list_groupall();
			if(count($result)>0){
				foreach ($result as $value)
				{
					$ProfilePic = $this->defaultimagecheck->checkImgExist($value['ProfilePic'],'userpics','default-avatar.jpg');
					
					$content .= "<div class='userFatchList boxFlowers' title='".$this->myclientdetails->customDecoding($value['Name'])." ".$this->myclientdetails->customDecoding($value['lname'])."' socialFriendlist='true'>
					<label class='labelCheckbox'>
					<input type='checkbox' value='".$value['UserID']."' checkvalue='".$checkvalue."' class='inviteuser-search' name='groupuser'>
					<div class='follower-box'>
					<div class='usImg'><span class='img'  style='background:url(".IMGPATH."/users/small/".$ProfilePic.") no-repeat; background-size:cover'/></span></div>
					".$this->myclientdetails->customDecoding($value['Name'])."
					</div>
					</label>
					</div></div>";
				}
				if(count($grouprs)>0){
					$grouplist ='<div id="surveyDialog"  title="Platform users">
					Select user set <div class="select">
					<select id="similarusrid" class="gh-tb s-hidden"  name="orderfield" value="" maxlength="200" style="width:200px;">
					<option value="0"> - Select Set - </option>';
					foreach ($grouprs as $value):
					$grouplist .='<option value="'.$value['ugid'].'">'.$value['ugname'].' </option>';
					endforeach;
					$grouplist .='</select>
					<div class="styledSelect">Select a set</div>
					<ul class="options">';
					foreach ($grouprs as $value):
					$grouplist .='<li rel="'.$value['ugid'].'"> '.$value['ugname'].' </li>';
					endforeach;
					$grouplist .='</ul>
					</div>';
				}
			}else{
					
				$content .= "No record found";
			}
			$data['status'] = 'success';
			$data['content'] = $content;
			$data['grouplist'] = $grouplist;
		}
		else
		{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));

	}

	

	public function downloadpdfAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$pdfName = $this->_request->getParam('filepdf');
		$path    = $this->_request->getParam('pathname');

		$pathi   = ($path!='')?$path:'surveydoc';		

		//$file = Front_Public_Path."surveydoc/".$pdfName;

		$file = Front_Public_Path.$pathi."/".$pdfName;

		if($path=='postreports')
		{
			if(getimagesize($file))
			{
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="'.basename($file).'"'); 
				header('Content-Length: ' . filesize($file));
				readfile($file);
			}else{
				$this->_redirect('/');
			}
		}
		else
		{
			if(file_exists($file) && $pdfName!='' && filesize($file)!=0)
			{
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="'.basename($file).'"'); 
				header('Content-Length: ' . filesize($file));
				readfile($file);
			}else{
				$this->_redirect('/');
			}
		}

		
	}

	
	public function specialdbsreloadAction()
	{
		define(PAGE_NUM,10);
		//$request = $this->getRequest()->getParams();
		$request    =   $this->getRequest()->getPost();
		$startss = $request['cnt']?$request['cnt']:'0';
		$response = $this->getResponse();
		
		$this->_helper->layout()->disableLayout();		
		$deshboard 	= 	new Admin_Model_Deshboard();
		$cmnObj= new Admin_Model_Common();
		$spdbstotal 		=	count($deshboard->specialdbstotal($startss,PAGE_NUM));
		$this->view->invite = $this->_request->getParam('invite');
		//$spdbs 		=	$deshboard->SearchDbees('select DbeeID,Name,UserID,ProfilePic,Type,Vid,VidDesc,VidSite,VidID,eventtype,eventstart,eventzone,PostDate from tblDbees as db, tblUsers as u where db.User=u.UserID  and db.Type=6  order by PostDate  DESC');
		$spdbs 		=	$deshboard->specialdbs($startss,PAGE_NUM);
		$this->session_name_space = new  Zend_Session_Namespace('User_Session');
	
		$facebook_connect_data = Zend_Json::decode($this->session['facebook_connect_data']);
		$twitter_connect_data = Zend_Json::decode($this->session['twitter_connect_data']);
		$linkedin_connect_data = Zend_Json::decode($this->session['linkedin_connect_data']);
		

		$facebook = $facebook_connect_data['access_token'];
		$twitter = $twitter_connect_data['twitter_access_token'];
		$linkedin = $linkedin_connect_data['oauth_verifier'];

		if($linkedin) // check linkedin logined or not
			$this->linkedinLogined = true;
		else
			$this->linkedinLogined = false;

		if($twitter)  // check twitter logined or not
			$this->twitterLogined = true;
		else
			$this->twitterLogined = false;

		if($facebook)  // check twitter logined or not
			$this->facebookLogined = true;
		else
			$this->facebookLogined = false;
	
		$locale = new Zend_Locale();
		$timezonelist = $locale->getTranslationList("TimezoneToTerritory");
	
		$this->view->timezonelist = $timezonelist;
	
		$result ='';
		
		$offset = $this->start+10;
				if (count($spdbs)){ 
					
				 foreach($spdbs as $value) :
				
						 $result .= "<li>";
						
						$descDisplay = '';
									if($value['Type']==6) {
										
									$dbtype	=	$cmnObj->getspecialdbType_CM($value['eventtype']).' Event Type  ';
									$dbtypeIcon	=	'<div class="pstype typeVideo"></div>';
									$dbVid			=	$value['VidID'];
									$dbVidDesc		=	$value['VidDesc'];
									
									
									$descDisplay	.=	'<div class="dbPicText">';
									if($dbVid!='')
									{
										$descDisplay	.=	'<div class="dbPic" ><a href="#"><img width="90" height="60" border="0" src="https://i.ytimg.com/vi/'.$dbVid.'/0.jpg"></a></div>';
									}else{
										$noPic = 'noPix';
										$note = 'Unpublished - Add a youtube video link to make the broadcast live';
									}	
									
									$invitelink = '<span>No User Invited</span>&nbsp;';
									if($this->invite($value['DbeeID'])>0){
									
									$invitelink = '';
									}
									
									if($this->attendies($value['DbeeID'],1)>0){
										$attendies = '<a href="javascript:void(0);" class="attendiesuser spdbltbox" dbid="'.$value['DbeeID'].'" type="1"><strong><span>'.$this->attendies($value['DbeeID'],1).'</span> user attending this event</strong></a>';									}else{
										$attendies = '<div class="spdbltbox"><strong>No user attending this event</strong></div>';
									}
									if($this->attendies($value['DbeeID'],0)>0){
										$reqtojoin = '<a href="javascript:void(0);" class="reqtojoinuser spdbltbox" dbid="'.$value['DbeeID'].'" type="0"><strong><span>'.$this->attendies($value['DbeeID'],0).'</span> user requested to join</strong></a>';
									}else{
										$reqtojoin = '<div class="spdbltbox"><strong> No user requested to join</strong></div>';		
									}
									
									$descDisplay	.=	'<div class="dbPicDesc '.$noPic.'">
									<div class="specialdbListDtleft">'.$dbVidDesc.' </div>
									<div class="specialdbListDtl">
									<h2>Event start on - '. date('d M y - h:i',strtotime($value['eventstart'])) .'</h2>
									'.$attendies.'	
									<div class="spdbltbox">
									'.$invitelink.'
									<span>'.$this->invitecnt($value['DbeeID']).'</span>
									</div>
									'.$reqtojoin.'
									
									<div class="spdbltbox">
									<div class="inPeople"> Invite <br>people</div>
									';
									
									if($this->facebookLogined==false)
										$descDisplay .='<a href="'.$this->facebookurl.'" class="socialSpecialIcons ssfbIcon" ></a>';
									else
										$descDisplay .='<a href="javascript:void(0);" class="socialSpecialIcons ssfbIcon" onclick="facebookUser('.$value['DbeeID'].',\'attendies\')"></a>';

									if($this->twitterLogined==false)
										$descDisplay	.=	'<a href="'.BASE_URL.'/admin/social/twitter?postid='.$value['DbeeID'].'&type=attendies" class="socialSpecialIcons sstwIcon"></a>';
									else
									$descDisplay	.=	'<a href="javascript:void(0);" onclick="twitterUser('.$value['DbeeID'].',\'attendies\')" class="socialSpecialIcons sstwIcon"></a>';
									
									if($this->linkedinLogined==false)
										$descDisplay	.=	'<a href="'.BASE_URL.'/admin/social/linkedin?postid='.$value['DbeeID'].'&type=attendies" class="socialSpecialIcons sslnIcon"></a>';
									else
									$descDisplay	.=	'<a   href="javascript:void(0);" onclick="linkedinUser('.$value['DbeeID'].',\'attendies\')" class="socialSpecialIcons sslnIcon"></a>';
									
									$descDisplay	.=	'<a href="javascript:void(0);" onclick="dbeeUser('.$value['DbeeID'].',\''.$dbVidDesc.'\');" class="socialSpecialIcons ssdbIcon"></a>
									</div>

									<div class="spdbltbox">
									<div class="inPeople"> Invite <br>guest speekar</div>';

									if($this->facebookLogined==false)
										$descDisplay .='<a href="'.$this->facebookurl.'" class="socialSpecialIcons ssfbIcon" ></a>';
									else
										$descDisplay .='<a href="javascript:void(0);" class="socialSpecialIcons ssfbIcon" onclick="facebookUser('.$value['DbeeID'].',\'expert\')"></a>';

									if($this->twitterLogined==false)
										$descDisplay	.=	'<a href="'.BASE_URL.'/admin/social/twitter?postid='.$value['DbeeID'].'&type=expert" class="socialSpecialIcons sstwIcon"></a>';
									else
										$descDisplay	.=	'<a href="javascript:void(0);" onclick="twitterUser('.$value['DbeeID'].',\'expert\')" class="socialSpecialIcons sstwIcon"></a>';
									
									if($this->linkedinLogined==false)
										$descDisplay	.=	'<a href="'.BASE_URL.'/admin/social/linkedin?postid='.$value['DbeeID'].'&type=expert" class="socialSpecialIcons sslnIcon"></a>';
									else
									$descDisplay	.=	'<a   href="javascript:void(0);" onclick="linkedinUser('.$value['DbeeID'].',\'expert\')" class="socialSpecialIcons sslnIcon"></a>';
									
									$descDisplay	.=	'<a href="javascript:void(0);" onclick="dbeeUser('.$value['DbeeID'].',\''.$dbVidDesc.'\');" class="socialSpecialIcons ssdbIcon"></a>
									</div>
									</div>
									<div class="clearfix"></div>
									</div><span style="color:red;">'.$note.'</span>
									</div>';
							} 
					
						$result .='<div class="dataListWrapper">
						<div class="dataListbox">
						<div class="scoredListTitle">'.$dbtypeIcon.' <a class="show_details_user" userid="'. $value['UserID'].'" href="javascript:void(0)">
						'. $this->myclientdetails->customDecoding($value['Name']).'</a>
						has posted a '. $dbtype. ' <a href="'. BASE_URL.'/dbee/'. $value['dburl'].'">  - view </a> 
						</div>
						<div class="scoredData">
						<div class="dbPost">'.$descDisplay.'&nbsp;</div>
						</div></div>
						<div class="scoredPostDate">Posted on  - '.date('d M y',strtotime($value['PostDate'])).'</div>
						<div class="scoredPostDate" style="float:right;"><span class="updateSpecialDbee openSearchBlock btn btn-green btn-mini" videoid="'.$value['DbeeID'].'">Edit</span></div>	
						</div>		
						</li>';

				endforeach;
				if($spdbstotal>10){
				$fft = (int)$startss+10;
				$result .='<div id="vewpagebottom" style="text-align:center;padding: 20px 2%;font-weight:bold;font-size:16px;clear:both;">
				<a id="viewmorespecialdb" offset="'. $fft.'">View More </a></div>';
				}
				}else{
			$result .='<div style="padding: 20px 2%;color: #CCCCCC;font-size: 20px;margin-top:20px;clear:both;text-align: center; width: 100%;">No post found!</div>';
			
		}
		echo $result;			
		return;
	}
	
	public function Invite($dbeeid)
	{
		$dash_obj = new Admin_Model_Deshboard();
		$ddd = $dash_obj->inviteuser($dbeeid);
		return $ddd;
	}
	public function Invitecnt($dbeeid)
	{
		$dash_obj = new Admin_Model_Deshboard();
		$ddd = $dash_obj->inviteusergroupby($dbeeid);
		$classname = array('facebook'=>'socialSpecialIcons ssfbIcon','twitter'=>'socialSpecialIcons sstwIcon','linkedin'=>'socialSpecialIcons sslnIcon','dbee'=>'socialSpecialIcons ssdbIcon');
		$bg = array('dbee'=>'#FFCB0A','facebook'=>'#3A589B','linkedin'=>'#007AB9','twitter'=>'#20B8FF');
		$fntColor = array('dbee'=>'#000','facebook'=>'#fff','linkedin'=>'#fff','twitter'=>'#fff');
		//$dname = array('facebook','twitter','linkedin','db');
		$res ="<span class='countSocialSpecial'>";
		$i=0;
		foreach($ddd as $data):
		$res.="<span  rel='dbTip' title='".$data['dbeeidcnt']." Users Invited ".$data['type']."' style='background:".$bg[$data['type']]."; color:".$fntColor[$data['type']]."'><strong class='".$classname[$data['type']]."'></strong><b> ".$data['dbeeidcnt']."</b></span>";
		$i++;
		endforeach;
		$res .="</span>";
		return $res;
	}
	public function Attendies($dbeeid,$type)
	{
		$dash_obj = new Admin_Model_Deshboard();
		return count($dash_obj->attendiesuser($dbeeid,$type));
	}
	

	public function createdbAction()
	{

		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$deshboard 	= 	new Admin_Model_Deshboard();
			$common	= 	new Admin_Model_Common();

			$description = $this->_request->getPost('yt_des');
			$eventstart = $this->_request->getPost('eventstart');
			$eventtype = $this->_request->getPost('eventtype');
			$youtube = $this->_request->getPost('youtube');
			$DbeeID = $this->_request->getPost('DbeeID');
			$commentduring = $this->_request->getPost('commentduring');
			$eventzone = $this->_request->getPost('timezoneevent');
			
			$commentduring = ($commentduring=='')?0:1;

			$newdate = $this->convert_time_zone($eventstart,$eventzone ,'Europe/London');

			if($youtube!='false')
			{
				$youtubeUrl = $VID = $this->_request->getPost('yt_url');

				$match = array();
				preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $youtubeUrl, $match);
				require_once 'Google/Google/autoload.php';
				require_once 'Google/Google/Client.php';
				require_once 'Google/Google/Service/YouTube.php';

				$OAUTH2_CLIENT_ID = youtubeapi;
				$OAUTH2_CLIENT_SECRET = youtubescret;

				$client = new Google_Client();
				$client->setClientId($OAUTH2_CLIENT_ID);
				$client->setClientSecret($OAUTH2_CLIENT_SECRET);
				$client->setScopes('https://www.googleapis.com/auth/youtube');
				$client->setRedirectUri(BASE_URL.'/admin/dashboard/specialdbs');

				// Define an object that will be used to make all API requests.
				$youtube = new Google_Service_YouTube($client);
				if (isset($_SESSION['token'])) {
				  $client->setAccessToken($_SESSION['token']);
				}
				$client->getAccessToken();
				$vid = $match[1];
				$eventend = $this->youtubeApi($youtube,$vid);
				$VidSite = 'youtube';
			}
			else
			{
				$vid = $this->_request->getPost('filename');
				$VideoTitle = $this->_request->getPost('VideoTitle');
				$eventend = $this->_request->getPost('eventend');
				$VidSite = 'dbcsp';
			   /*  if($VideoTitle!='')
			    {
				    $kcObj	=	new Admin_Model_Knowldgecenter();
				    $kResult = $kcObj->getGallery(1);
				    $kdata	=	array('clientID'=>clientID,'kc_cat_title'=>$VideoTitle,'kc_adddate'=>date("Y-m-d H:i:s"), 'added_by'=>$this->_userid,'galleryType'=>1,'kc_pid'=>$kResult[0]['kc_id'],'kc_file'=>$vid,'video_duration'=>$eventend);
					$kcObj->insertdata($kdata);
				}*/
				$VID = '';
			}
	
			$dburl = $this->makeSeo($description,'','save');

			$data =	array('eventend'=>$eventend ,
						  'Type'=> 6,
						  'User'=>adminID,
						  'Vid'=>$vid,
						  'Text'=>$description,
						  'VidDesc'=>$description,
						  'VidSite'=>$VidSite,
						  'VidID'=>$VID,
						  'eventtype'=>$eventtype,
						  'eventstart'=>$newdate,
						  'PostDate'=>date('Y-m-d H:i:s'),
						  'LastActivity'=>date('Y-m-d H:i:s'),
						  'dburl'=>$dburl,
						  'eventzone'=>$eventzone,
						  'clientID'=>clientID,
						  'commentduring'=>$commentduring,
						  'Active'=>1);

			$insert = $deshboard->insertdata_global('tblDbees',$data);	
		
			$dataArray['userID']    = (int) $this->_userid;
            $dataArray['dbeeID']    = (int) $insert;
            $dataArray['status']    = 1;
            $dataArray['timestamp'] = date('Y-m-d H:i:s');
            $dataArray['clientID']  = clientID;
            $this->myclientdetails->insertdata_global('tblDbeeJoinedUser',$dataArray);

			return $response->setBody(Zend_Json::encode($data));
		}
	}

	public function specialdbseditAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$common	= 	new Admin_Model_Common();
			$id = $this->_request->getPost('id');
			$result = $this->socialInvite->getdburltitle($id);
			$newdate = $this->convert_time_zone($result['eventstart'],'Europe/London',$result['eventzone']);
			$data['result'] = $result;
			if($result['VidSite']=='dbcsp')
			{
				$resultVideo = $this->myclientdetails->getAllMasterfromtable('tblVideo',array('*'),array('clientID'=>clientID,'hashed_id'=>$result['Vid']));
				$data['fileInfo'] = $resultVideo[0];
			}
			$data['videoTime'] = $newdate;
			$data['status'] = 'success';
		}
		else
		{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));

	}

	public function editvideodbAction()
	{

		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$request = $this->getRequest()->getParams();
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$description = $this->_request->getPost('yt_des');
			$eventstart = $this->_request->getPost('eventstart');
			$eventtype = $this->_request->getPost('eventtype');
			$youtube = $this->_request->getPost('youtube');
			$DbeeID = $this->_request->getPost('DbeeID');
			$commentduring = $this->_request->getPost('commentduring');
			$eventzone = $this->_request->getPost('timezoneevent');
			
			$commentduring = ($commentduring=='')?0:1;

			$newdate = $this->convert_time_zone($eventstart,$eventzone ,'Europe/London');

			if($youtube!='false')
			{
				$youtubeUrl = $VID = $this->_request->getPost('yt_url');

				$match = array();
				preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $youtubeUrl, $match);
				require_once 'Google/Google/autoload.php';
				require_once 'Google/Google/Client.php';
				require_once 'Google/Google/Service/YouTube.php';

				$OAUTH2_CLIENT_ID = youtubeapi;
				$OAUTH2_CLIENT_SECRET = youtubescret;

				$client = new Google_Client();
				$client->setClientId($OAUTH2_CLIENT_ID);
				$client->setClientSecret($OAUTH2_CLIENT_SECRET);
				$client->setScopes('https://www.googleapis.com/auth/youtube');
				$client->setRedirectUri(BASE_URL.'/admin/dashboard/specialdbs');

				// Define an object that will be used to make all API requests.
				$youtube = new Google_Service_YouTube($client);
				if (isset($_SESSION['token'])) {
				  $client->setAccessToken($_SESSION['token']);
				}
				$client->getAccessToken();
				$vid = $match[1];
				$eventend = $this->youtubeApi($youtube,$vid);
				$VidSite = 'youtube';
			}
			else
			{
				$vid = $this->_request->getPost('filename');
				$eventend = $this->_request->getPost('eventend');
				$VideoTitle = $this->_request->getPost('VideoTitle');
				$VidSite = 'dbcsp';
				$VID = '';
				if($VideoTitle!='')
			    {
				    $kcObj	=	new Admin_Model_Knowldgecenter();
				    $kResult = $kcObj->getGallery(1);
				    $kdata	=	array('clientID'=>clientID,'kc_cat_title'=>$VideoTitle,'kc_adddate'=>date("Y-m-d H:i:s"), 'added_by'=>$this->_userid,'galleryType'=>1,'kc_pid'=>$kResult[0]['kc_id'],'kc_file'=>$vid,'video_duration'=>$eventend);
					$kcObj->insertdata($kdata);
				}
			}
			
			$data =	array('eventend'=>$eventend ,
					  'Vid'=>$vid,
					  'Text'=>$description,
					  'VidDesc'=>$description,
					  'VidSite'=>$VidSite,
					  'VidID'=>$VID,
					  'eventtype'=>$eventtype,
					  'eventstart'=>$newdate,
					  'PostDate'=>date('Y-m-d H:i:s'),
					  'LastActivity'=>date('Y-m-d H:i:s'),
					  'eventzone'=>$eventzone,
					  'commentduring'=>$commentduring);

			$DbeeID = $this->_request->getPost('DbeeID');
			$where['DbeeID'] = $DbeeID;
			$this->myclientdetails->updateMaster('tblDbees',$data,$where);
			return $response->setBody(Zend_Json::encode($data));
		}
	}
	
	function getDuration($video_id)
    {
        $data = @file_get_contents('https://gdata.youtube.com/feeds/api/videos/' . $video_id . '?v=2&alt=jsonc');
        if (false === $data)
            return false;
        $obj = json_decode($data);
        return $obj->data->duration;
    }
	public function convert_time_zone($date_time, $from_tz, $to_tz)
	{
		$time_object = new DateTime($date_time, new DateTimeZone($from_tz));
		$time_object->setTimezone(new DateTimeZone($to_tz));
		return $time_object->format('Y-m-d H:i:s');	
	}

	public function deletepostAction()
    {
    	$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		
		if ($this->getRequest()->isXmlHttpRequest())
		{
			$dbeeid = (int) $this->_request->getPost('dbeeID');

			$query="SELECT Pic FROM tblDbees WHERE clientID='".clientID."' and Pic!='' and DbeeID='".$dbeeid."'";
       		$fetchdata=$this->myclientdetails->passSQLquery($query); 

       		if($fetchdata[0]['Pic']!="")
       		{
       			$filename=IMGPATH."".$fetchdata[0]['Pic'];
       			if (file_exists($filename)) {
       				unlink(ABSIMGPATH."/imageposts/".trim($filename));
					unlink(ABSIMGPATH."/imageposts/medium/".trim($filename));
					unlink(ABSIMGPATH."/imageposts/small/".trim($filename));
       			}

       		}     
       		$this->myclientdetails->deletedata_global('tblPollOptions','PollID',$dbeeid);
			$this->myclientdetails->deletedata_global('tblDbees','DbeeID',$dbeeid);
			$this->myclientdetails->deletedata_global('tblactivity','act_typeId',$dbeeid);
			
       		

	    }
	    return $response->setBody(Zend_Json::encode($_POST));
    }
    public function updatedbeestatusAction()
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
			$dbeeid = (int) $this->_request->getPost('dbeeid');

			$statusGen = (int) $this->_request->getPost('status');

			if($statusGen==2){ 
				$status = 1;
				$updData =  array('Active'=>$status,'LastActivity'=>date('Y-m-d H:i:s'));
			}
			else
			{
				$status = $statusGen;
				$updData =  array('Active'=>$status);	
			}
			
			$where['DbeeID'] = $dbeeid;
			
			$this->myclientdetails->updatedata_global('tblDbees',$updData,'DbeeID', $dbeeid);
			
			if($statusGen==2)
			{
				$this->myclientdetails->deletedata_global('tblactivity','act_typeId',$dbeeid);

				$dbinfo=$this->myclientdetails->getRowMasterfromtable('tblDbees',array('User'),array('DbeeID'=>$dbeeid));

				$checkrow=$this->myclientdetails->getRowMasterfromtable('tblactivity',array('act_typeId'),array('act_typeId'=>$dbeeid,'act_type'=>3,'act_message'=>30,'act_userId'=>$this->adminUserID,'act_ownerid'=>$dbinfo['User']));
				if($checkrow < 1)
				{
					$socialModal->commomInsert(30,30,$dbeeid,$this->adminUserID,$dbinfo['User']);
					$socialModal->commomInsert('1','1',$dbeeid,$dbinfo['User'],$dbinfo['User']);
				}
		    	
		    }
	    }
	    return $response->setBody(Zend_Json::encode($_POST));
    }
	 
	/* list dbee's*/
	public function postAction()
	{
		$userpram='';
		$request = $this->getRequest()->getParams();
		$userpram = $request['uid'];
		$task = $request['task'];

		$searchdb = $request['id'];

		if($task=='create')
		{
		   $userpram = $this->_userid;
		}
		
		if(isset($request['searchfield']) && $request['searchfield']!='')
			$seachfield = $request['searchfield'];
		else
			$seachfield = '';
		
		$deshboard 	= 	new Admin_Model_Deshboard();
		
		$filter		=	new Admin_Model_Searchfilter();
		$user_id	=	$this->_userid;

		if(isset($searchdb) && $searchdb!='')
		{
			$liveDbeeData = $deshboard->getLiveDbee('all',$searchdb,'',$userpram);
		}
		else
		{
			if(!empty($userpram)){
				$liveDbeeData = $deshboard->getLiveDbee('all','','',$userpram);
			}else{
				$liveDbeeData = $deshboard->getLiveDbee('all');
			}
		}
		
		$page = $this->_getParam('page',1);

		$paginator = Zend_Paginator::factory($liveDbeeData);

		$paginator->setItemCountPerPage(20);

		$paginator->setCurrentPageNumber($page);

		$orderbyArr = array('Priority'=>'DESC');
		$orderbyGrpArr = array('ID'=>'DESC');
		$orderbyLegpArr = array('LID'=>'DESC');
		$orderbyEventpArr = array('id'=>'DESC');

		$this->view->category = $this->myclientdetails->getAllMasterfromtable('tblDbeeCats',array('CatID','CatName','Priority'),'',$orderbyArr);			
		
		$this->view->liveGroupData = $this->myclientdetails->getAllMasterfromtable('tblGroups',array('DISTINCT (GroupName) as groupname','ID','GroupType'),array('User'=>$this->_userid),$orderbyGrpArr);
		
		$this->view->liveLegData = $this->myclientdetails->getAllMasterfromtable('tblUserLeague',array('DISTINCT (Title) as title','LID','EndDate'),array('UserID'=>$this->_userid),$orderbyLegpArr);

		$this->view->eventlist = $deshboard->eventlist();
		$this->view->task      = $task;

		if( ismobile !='') $paginator->setPageRange(5);
		// $paginator->count();
		$this->view->paginator = $paginator;
		
		$this->view->total = $paginator->getTotalItemCount();
		$this->view->page = $page;
		$this->view->totalpage = $paginator->count();
		$this->view->rectodis = $page*20;
		$this->view->lastpage = $paginator->count();
		

		$getFilters	=	$filter->loadFilters($user_id);
		$this->view->filters 	=	$getFilters;

	}

	public function popularpostAction()
	{
		//echo'i am here';die;
		$userpram='';
		$request = $this->getRequest()->getParams();
		$userpram = $request['uid'];
		$task = $request['task'];

		$searchdb = $request['id'];

		if($task=='create')
		{
		   $userpram = $this->_userid;
		}
		
		if(isset($request['searchfield']) && $request['searchfield']!='')
			$seachfield = $request['searchfield'];
		else
			$seachfield = '';
		
		$deshboard 	= 	new Admin_Model_Deshboard();
		
		$filter		=	new Admin_Model_Searchfilter();
		$user_id	=	$this->_userid;

		if(isset($searchdb) && $searchdb!='')
		{
			$liveDbeeData = $deshboard->getLiveDbeepopularpost('all',$searchdb,'',$userpram);
		}
		else
		{
			if(!empty($userpram)){
				$liveDbeeData = $deshboard->getLiveDbeepopularpost('all','','',$userpram);
			}else{
				$liveDbeeData = $deshboard->getLiveDbeepopularpost('all');
			}
		}
		//echo'<pre>';print_r($liveDbeeData);die;
		$page = $this->_getParam('page',1);

		$paginator = Zend_Paginator::factory($liveDbeeData);

		$paginator->setItemCountPerPage(20);

		$paginator->setCurrentPageNumber($page);

		$orderbyArr = array('Priority'=>'DESC');
		$orderbyGrpArr = array('ID'=>'DESC');
		$orderbyLegpArr = array('LID'=>'DESC');
		$orderbyEventpArr = array('id'=>'DESC');

		$this->view->category = $this->myclientdetails->getAllMasterfromtable('tblDbeeCats',array('CatID','CatName','Priority'),'',$orderbyArr);			
		
		$this->view->liveGroupData = $this->myclientdetails->getAllMasterfromtable('tblGroups',array('DISTINCT (GroupName) as groupname','ID','GroupType'),array('User'=>$this->_userid),$orderbyGrpArr);
		
		$this->view->liveLegData = $this->myclientdetails->getAllMasterfromtable('tblUserLeague',array('DISTINCT (Title) as title','LID','EndDate'),array('UserID'=>$this->_userid),$orderbyLegpArr);

		$this->view->eventlist = $deshboard->eventlist();
		$this->view->task      = $task;


		// $paginator->count();
		$this->view->paginator = $paginator;
		$this->view->total = $paginator->getTotalItemCount();
		$this->view->page = $page;
		$this->view->totalpage = $paginator->count();
		$this->view->rectodis = $page*20;
		$this->view->lastpage = $paginator->count();
		

		$getFilters	=	$filter->loadFilters($user_id);
		$this->view->filters 	=	$getFilters;

	}

	public function singlepostAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
	
		$request = $this->getRequest()->getParams();
		$dbeeID  = $request['dbeeid'];
		$user_id = $this->_userid;
		
		$deshboard 	 = 	new Admin_Model_Deshboard();
		$layoutsObj  =  new Admin_Model_Layouts();
	
		$liveDbeeData = $deshboard->getLiveDbee('all',$dbeeID);
		
		if (count($liveDbeeData)){ 	echo $layoutsObj->onlydbeetemplate($liveDbeeData,'post');
      	} else { echo "something went wrong.. Please reload your page";}
		
		exit;	

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
		//print_r($this->twitter_connect_data);
		$results  = $twitteroauth->get('search/tweets', array(
		'q' => $q,
		'result_type' => 'mixed',
		'count' => 4
		));		

		$results = json_decode(json_encode($results), true);
		//print_r($results); exit;
		if(is_array($results['statuses'])) 
		{
			foreach( $results['statuses'] as $result) 
			{
				$screen_name=$results['statuses'][$cnt]['user']['screen_name'];
				$profile_image=$results['statuses'][$cnt]['user']['profile_image_url'];
				$tweet = $results['statuses'][$cnt]['text'];				
				$data = array('Keyword'=>$q1,'ScreenName'=>$screen_name,'ProfilePic' => $profile_image,'Tweet'=>$tweet);
				
			} 
		}
		//print_r($data); exit;
		return count($data);
	}
	////////////////// Insert Start ////////////////

	/* Post Submit */

    public function postsubmitAction()
    {
    	$datapost = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);;		
		$common = new Admin_Model_Common();
    	$request = $this->getRequest();
    	$filter = new Zend_Filter_StripTags();
    	$socialModal	=	new Admin_Model_Social();

		if (!$request->isPost()) {
			echo false; // go to success event of ajax and redirect to myhome/logout  action
			exit;
		}

		$data2 = array();

		$dbeetype = $request->getPost('dbeetype');
		
		if($dbeetype=='Polls'){ 
			$Type=5;  $dbtitle = $this->makeSeo($request->getPost('polltext'),'','save'); }
		else { $Type=1; $dbtitle = $this->makeSeo($request->getPost('PostText'),'','save');}
		
		if($cat=='' || $cat=='undefined') $cat=10;
		
		$postdate=date('Y-m-d H:i:s') ;	
		$activity 	= date('Y-m-d H:i:s');
		$activity2 	= date('Y-m-d H:i:s');
		$Activestatus = 1;
		$schedulePost = 0;
		$postlater = $request->getPost('postlater');

		if($postlater=='on')
		{	
			$posttimings  = $request->getPost('posttimings');
			$scheduledate = $request->getPost('scheduledate');
			if($posttimings!='0' && $posttimings!='99')
			{
				$startDate = time();
				$postdate = date('Y-m-d H:i:s', strtotime('+'.$posttimings.' hour', $startDate));
				$activity = date('Y-m-d H:i:s', strtotime('+'.$posttimings.' hour', $startDate));
				$Activestatus = 2;
				$schedulePost = 1; 
			}
			elseif($scheduledate!="")
			{
				$postdate = $common->getdbtime($scheduledate, '0.0', 'Y-m-d H:i:s');
				$activity = $common->getdbtime($scheduledate, '0.0', 'Y-m-d H:i:s');
				
				$time_zone = $this->getTimeZoneFromIpAddress();
				
				$UTCObj = DateTime::createFromFormat("Y-m-d H:i:s", $activity, new DateTimeZone($time_zone)); 
				$LocalObj = $UTCObj;
				 
				$LocalObj->setTimezone(new DateTimeZone("Europe/London"));
				$gmtTime =  $LocalObj->format("Y-m-d H:i:s")  ;
				 
				 $postdate = $gmtTime;	
				 $activity = $gmtTime;	
				 $Activestatus = 2;
				 $schedulePost = 1;
			}
			else
			{
				 $postdate=date('Y-m-d H:i:s');	
				 $activity 	= date('Y-m-d H:i:s');
			}
		}
		
		$twittertag = $filter->filter((stripslashes($request->getPost('twittertag'))));
		if($twittertag=='undefined')
			$twittertag = '';		
		
		$groupid 	= $filter->filter(stripslashes($request->getPost('group')));
		$groupname = $filter->filter($this->_request->getPost('groupname'));
		
		$dbtag 				 = stripslashes($filter->filter($request->getPost('tagitent')));
		$TaggedUsers         = stripslashes($filter->filter($request->getPost('TaggedUsers')));

		$rssFinaladded 		= '';

		if($request->getPost('rssfeed')!='')
		{
			$feedrepaceLink		= '<a href="'.$request->getPost('rssLink').'" target="_blank">read more</a>';
			$rssadded			= str_replace('read more', $feedrepaceLink, $filter->filter( $request->getPost('rssfeed')));
			$feedrepaceTitle	= '<div id="dbRssTitle">'.$request->getPost('rssTitle').'</div>';
			$rssFinaladded 		= str_replace($request->getPost('rssTitle'), $feedrepaceTitle, $rssadded);	
		}	

		$data['clientID'] 	= clientID;
		$data['LastCommentUser'] = (int)$this->adminUserID;

		$data['schedulepost'] = $schedulePost;
		
		$data['Text'] 				= $request->getPost('text');
		$data['RssFeed'] 			= $rssFinaladded;//stripslashes(str_replace($feedrepace, '',$request->getPost('rssfeed')));
		$data['Cats'] 				= stripslashes($filter->filter(implode(',',$request->getPost('cat'))));
		$data['Pic'] 				= stripslashes($filter->filter($request->getPost('pic')));		
		$AdminUserSetVar 			= stripslashes($filter->filter($request->getPost('userset')));
		$data['PrivatePost'] 		= stripslashes($filter->filter($request->getPost('PrivatePost')));
		$AdminUserSet        = '';
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


		
		if($data['Pic']!=''){
			$imageSize = @getimagesize($_SERVER['DOCUMENT_ROOT'].'/imageposts/'.$data['Pic']);
			$data['PicSize'] 		= $imageSize[0];
		}else
			$data['PicSize'] = '';

		$data['Link'] 		= stripslashes($filter->filter($request->getPost('url')));
		$data['LinkTitle'] 	= stripslashes($filter->filter($request->getPost('LinkTitle')));
		$data['LinkDesc'] 	= stripslashes($filter->filter($request->getPost('LinkDesc')));
		$data['LinkPic'] 	= stripslashes($filter->filter($request->getPost('LinkPic')));
		// youtube url
		$data['Vid'] 		= stripslashes($filter->filter($request->getPost('Vid')));
		$data['VidDesc'] 	= stripslashes($filter->filter($request->getPost('VidDesc')));
		$data['VidSite'] 	= stripslashes($filter->filter($request->getPost('VidSite'))); 		
		$data['VidID'] 		= stripslashes($filter->filter($request->getPost('VidID')));
		$data['VidTitle'] 	= stripslashes($filter->filter($request->getPost('VidTitle')));

		$data['events'] 	= stripslashes($filter->filter($request->getPost('selectEventList')));

		$data['User'] 		= (int)$this->adminUserID;
		if($request->getPost('InsertInGrp')==1)
			$data['GroupID'] 	= stripslashes($filter->filter($request->getPost('selectGroupList')));
	    else
	    	$data['GroupID'] 	= 0;
	    
	    $file = $this->_request->getPost('filename');

	    if($file)
	    {
	    	$VideoTitle = $this->_request->getPost('VideoTitle');
	    	
		    /*$kcObj	=	new Admin_Model_Knowldgecenter();
		    $video_duration = $this->_request->getPost('video_duration');
		    $kResult = $kcObj->getGallery(1);
	    	if($VideoTitle!='')
	    	{
			    $kdata	=	array('clientID'=>clientID,'kc_cat_title'=>$VideoTitle,'kc_adddate'=>date("Y-m-d H:i:s"), 'added_by'=>$this->_userid,'galleryType'=>1,'kc_pid'=>$kResult[0]['kc_id'],'kc_file'=>$file,'video_duration'=>$video_duration);
				$kcObj->insertdata($kdata);
			}else{
				$VideoTitle = $kResult[0]['kc_cat_title'];
			}*/
			$data['Vid'] 		= $file;
			$data['VidID'] 		= $file;
			$data['VidTitle'] 	= $VideoTitle;
			$data['VidSite'] 	= 'dbcsp'; 
		}
	
		if($request->getPost('groupty')=="")
			$data['Privategroup'] 	= 0;
     	else
     		$data['Privategroup'] 	= stripslashes($filter->filter($request->getPost('groupty')));	
     	

		$data['TwitterTag'] = trim($twittertag);

		$attaechdata=array();
		
		$count_attachment=count($request->getPost('attachment') );
		
		if($count_attachment > 0)
		{
			foreach ($request->getPost('attachment') as $key => $value) {

				$sliceatteched=explode('####xxxx',$value);
				$attechedfilename=$sliceatteched[0];
				
				$attaechdata[$attechedfilename] = $sliceatteched[1];
			}
			$data['Attachment']=Zend_Json::encode($attaechdata);
	    }

		$keyword =  explode(",",trim($twittertag));
		 
		$count =  count($keyword);	

		if($count<4) 
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
					$this->_redirect('/admin/dashboard/post/error/1');
					exit;
				}
			}
		} else 
		{
			$this->_redirect('/admin/dashboard/post/error/1');
			exit;
		}
		$dbtag = (string)str_replace('#', '', trim($dbtag));

		$data['PostDate'] = $postdate;
		$data['LastActivity'] = $activity;
		$data['polltext'] = stripslashes($filter->filter($request->getPost('polltext')));
		$data['Active'] = $Activestatus; 
		$data['dburl'] = $dbtitle;		
		$data['DbTag'] = str_replace(' ', ',', $dbtag);
		$data['DbTag']; 
		
		if($Type=='5')
		{
			$data2['polloption1'] = stripslashes($filter->filter($request->getPost('polloption1')));
			$data2['polloption2'] = stripslashes($filter->filter($request->getPost('polloption2')));
			$data2['polloption3'] = stripslashes($filter->filter($request->getPost('polloption3')));
			$data2['polloption4'] = stripslashes($filter->filter($request->getPost('polloption4')));
			$data['DbTag']='';
		}

		$data['Type'] = $this->getType($data);

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


		if($request->getPost('postPublishAction')==0 && $request->getPost('postPublishAction')!="")
		 	$data['Active'] = 0;
		else
		    $data['Active'] = $Activestatus;
		
		$deshboard  = new Admin_Model_Deshboard();		
		$success1 = $deshboard->insertdata_global('tblDbees',$data);

		if($dbtitle==''){
			$datadburl = array('dburl'=> $success1);
			$deshboard->updatedata_global('tblDbees',$datadburl,'DbeeID',$success1);
		}

		$leagueId=$filter->filter($request->getPost('leagueId'));

		if($request->getPost('InsertInLeg')==1)
		{
			$sliceLeagueId=explode('###',$leagueId);

			if($success1 !='' && $sliceLeagueId[0]!='') // insert db into league
			{
				$leagueData  = array(
	                    'clientID' => clientID,
	                    'LID' => $sliceLeagueId[0],
	                    "DbeeID" => $success1,
	                    "Enddate" => $sliceLeagueId[1],
	                    "Status" => '1'
	                );
	            $insertleague = $deshboard->insertdata_global('tblLeagueDbee',$leagueData);
			}	
		}
		// end code  on facebook wall
		 $last_insertedId=$success1;

		if($Type=='5')
		{
			$success = $this->insertpol($data2,$last_insertedId,$ChosenCount=0);	
		}

		if($twittertag)
		{
			$hash=false;
			$keyword =  explode(",",$twittertag);
			$count =  count($keyword); 
			$db = $last_insertedId;
			$return = ''; 
			for ($i=0; $i < $count; $i++) 
			{ 
				$q = trim($keyword[$i]);

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
	            $rateLimit  = $twitteroauth->get('application/rate_limit_status', array('resources'=>'search'));
	            $rateLimit = json_decode(json_encode($rateLimit), true);
	            $data['rateLimit'] = $rateLimit;
	           
	            if($rateLimit['resources']['search']['/search/tweets']['remaining']==0)
	            {
	                $data['twitter']  = 'error';
	                $data['time']  = time();
	                $data['reset'] = $rateLimit['resources']['search']['/search/tweets']['reset'];
	                $data['diff']  = $data['reset']-$data['time'];
	                echo '5~#~'.ceil($data['diff']/60); exit;
	            }
				$results  = $twitteroauth->get('search/tweets', array(
					'q' => $q,
					'result_type' => 'mixed',
					'count' => 4
				));	
				$cnt=0;
				$results = json_decode(json_encode($results), true);
				if(is_array($results['statuses'])) 
				{
					foreach( $results['statuses'] as $result) 
					{
						$screen_name=$results['statuses'][$cnt]['user']['screen_name'];
						$profile_image=$results['statuses'][$cnt]['user']['profile_image_url_https'];
						$tweet=$results['statuses'][$cnt]['text'];				
						$tdata = array('Keyword'=>$q1,'ScreenName'=>$screen_name,'ProfilePic' => $profile_image,'Tweet'=>$tweet,'DbeeID'=>$db,'UserID'=>$this->_userid,'LastUpdate'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
						
						$success = $this->common_model->insertiwitter($tdata);
						$cnt++;
					} 
					unset($results);
				}
			}

		}

		if($success1)
		{	
			if($request->getPost('PrivatePost')==0)	// if there is no private post
		  	{		
		    $socialModal->commomInsert('1','1',$last_insertedId,$this->adminUserID,$this->adminUserID);	
		    }
		    elseif(count($TaggedUsersanduserset) > 0)
			{
				$sliceTaggedUsersanduserset = explode(',', $TaggedUsersanduserset);
				$sliceTaggedUsersanduserset = array_unique($sliceTaggedUsersanduserset);
				$temp = array();
				foreach ($sliceTaggedUsersanduserset as $key => $value) {
					 if (!in_array($value, $temp)) {
					   $socialModal->commomInsert('3','3',$last_insertedId,$this->adminUserID,trim($value));
					   $temp[] = $value;
					}
				}
		   	}	
		   	
			$datapost['status']  = 'success';
	        $datapost['content'] = 1;
		}
		else
		{
			$datapost['status']  = 'error';
	        $datapost['content'] = 0;

		}

		return $response->setBody(Zend_Json::encode($datapost));


    }
    public function deletevideoAction()
    {
    	$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest())
		{ 
			$hashed_id = $this->_request->getPost('hashed_id');
			$this->myclientdetails->deletedata_global('tblVideo','hashed_id',$hashed_id);
			$target_url = 'https://api.wistia.com/v1/medias/'.$hashed_id.'.json';
			$this->curl_del($target_url,array('api_password'=>api_password));		
			$data['status'] = 'success';
			$data['message'] = 'successfully deleted';
		}
		return $response->setBody(Zend_Json::encode($data));
	}
    public function videouploadAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && !empty($_FILES))
		{ 
			if(preg_match('/video\/*/',$_FILES['file']['type'])==false) 
			{
				$data['message'] = 'Sorry please upload video file';
				$data['status'] = false;
			}
			else
			{
				
				$filePath = Front_Public_Path.'adminraw/knowledge_center/video_'.clientID;
				
				$kcObj	=	new Admin_Model_Knowldgecenter();
			    
			    if (!file_exists($filePath))
			    {
			        mkdir($filePath, 0777);
			    }	


				$resultVideo = $this->myclientdetails->getAllMasterfromtable('tblVideo',array('project_id'),array('clientID'=>clientID));

				if(count($resultVideo)==0)
				{
					$target_url = 'https://api.wistia.com/v1/projects.json/';
					$postData = array('name' => 'video_'.clientID,'api_password' => api_password);
					$res = $this->curlRequest($target_url,$postData);
					$project_id = $res->hashedId; 
				}else{
					$project_id = $resultVideo[0]['project_id'];
				}

				$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION); 

				$picture = strtolower(time().'.'.$ext);

				copy($_FILES['file']['tmp_name'], $filePath."/".$picture);
				
			    //This needs to be the full path to the file you want to send.
				$file_name_with_full_path = realpath($filePath."/".$picture);
			    $target_url = 'https://upload.wistia.com/';
				$post = array('api_password' => api_password,'file'=>'@'.$file_name_with_full_path,'project_id'=>$project_id);
			 
			    $result = $this->curlRequest($target_url,$post);
			    
			    copy($result->thumbnail->url.'&image_crop_resized=700x400', $filePath."/".$result->hashed_id.'.jpg');

		        
		        $kdata = array('clientID'=>clientID,'name'=> $result->name ,'project_id'=>$project_id, 'hashed_id'=> $result->hashed_id ,'duration'=>$result->duration,'thumbnail'=>$result->hashed_id.'.jpg');
				
				$this->myclientdetails->insertdata_global('tblVideo',$kdata);

				@unlink($filePath."/".$picture);

				$data['file'] = $result->hashed_id;

				$data['status'] = true;

				$data['eventend'] = $result->duration;

			}
		}
		return $response->setBody(Zend_Json::encode($data));
	}

	
    public function getType($data)
    {
    	if($data['events']!="" && $data['events']!=0)
			$Type = 9;
		else if($dbeetype=='Polls')
			$Type = 5;
		else if($data['Text']!="" && $data['VidID']=="" && $data['Pic']=="" && $data['Link']=="")
			$Type="1";
		else if($data['Text']!="" && $data['VidID']=="" && $data['Pic']=="" && $data['Link']!="")
			$Type="2";
		else if($data['Text']!="" && $data['VidID']=="" && $data['Pic']!="" && $data['Link']=="")
			$Type="3";
		else if($data['Text']!="" && $data['VidID']!="" && $data['Pic']=="" && $data['Link']=="")
			$Type="4";
		else if($data['Text']!="" && $data['VidID']=="" && $data['Pic']!="" && $data['Link']!="")
			$Type="10";
		else if($data['Text']!="" && $data['VidID']!="" && $data['Pic']!="" && $data['Link']=="")
			$Type="11";
		return $Type;
    }
    /* end Post Submit */


    public function insertpol($data2, $insertid, $ChosenCount)
    {
        $deshboard  = new Admin_Model_Deshboard();

        if ($data2['polloption3'] == '' && $data2['polloption4'] == ''){
            $j =2;   
        }elseif ($data2['polloption4'] == ''){
        	$j = 3;
        }else
            $j = 4;
        
        for ($i = 1; $i <= $j; $i++) {
            
            $dd = array(
				'clientID' =>clientID,
                'OptionText' => $data2['polloption' . $i],
                
                'PollID' => $insertid,
                
                'ChosenCount' => $ChosenCount
                
            );
            
            
            
            if ($deshboard->insertdata_global('tblPollOptions', $dd));
            
        }
        
        return true;
        
    }
    


	/* view all  groups */

	public function groupsAction()
	{	
		$request = $this->getRequest()->getParams();
		$userpram = $request['uid'];
		if(isset($request['searchfield']) && $request['searchfield']!='')
			$seachfield = $request['searchfield'];
		else
			$seachfield = '';

		$deshboard= new Admin_Model_Deshboard();
		$liveGroupData = $deshboard->getLiveGroup('',$userpram);


		$page = $this->_getParam('page',1);
		/*
		 * Object of Zend_Paginator
		*/
		$paginator = Zend_Paginator::factory($liveGroupData);
		/*
		 * Set the number of counts in a page
		*/
		$paginator->setItemCountPerPage(20);
		/*
		 * Set the current page number
		*/
		$paginator->setCurrentPageNumber($page);
		/*
		 * Assign to view
		*/
			
		//echo "<pre>";
		//print_r($paginator);
		//die;
	
		$this->view->paginator = $paginator;
		$this->view->total = $paginator->getTotalItemCount();
		$this->view->page = $page;
		$this->view->totalpage = $paginator->count();
		$this->view->rectodis = $page*20;
		$this->view->lastpage = $paginator->count();

	}

	public function totalactivegroupsAction()
	{	
		$request = $this->getRequest()->getParams();
		$userpram = $request['uid'];
		/*if(isset($request['searchfield']) && $request['searchfield']!='')
			$seachfield = $request['searchfield'];
		else
			$seachfield = '';*/

		$deshboard= new Admin_Model_Deshboard();
		//$liveGroupData = $deshboard->getLiveGroup('',$userpram);
        $activeGroupData = $deshboard->getTopactivegroup();

		$page = $this->_getParam('page',1);
		/*
		 * Object of Zend_Paginator
		*/
		$paginator = Zend_Paginator::factory($activeGroupData);
		/*
		 * Set the number of counts in a page
		*/
		$paginator->setItemCountPerPage(20);
		/*
		 * Set the current page number
		*/
		$paginator->setCurrentPageNumber($page);
		/*
		 * Assign to view
		*/
			
		//echo "<pre>";
		//print_r($paginator);
		//die;
	
		$this->view->paginator = $paginator;
		$this->view->total = $paginator->getTotalItemCount();
		$this->view->page = $page;
		$this->view->totalpage = $paginator->count();
		$this->view->rectodis = $page*20;
		$this->view->lastpage = $paginator->count();

	}

	public function postdeleteAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		$userModal	=	new Admin_Model_User();
		$dashboard	=	new Admin_Model_Deshboard();
		if ($this->getRequest()->isXmlHttpRequest())
		{
			$dbee_id = (int) $this->_request->getPost('dbee_id');
			$dashboard->deletePostDbee($dbee_id); // delete Post
			$dashboard->deleteCommentDbee($dbee_id);
			$dashboard->deleteFavDbee($dbee_id);
			$dashboard->deleteFavDbee($dbee_id);
		
			$dashboard->deleteDbeeLeague($dbee_id); // delete league
			$dashboard->deleteDbeeQNADbee($dbee_id); // delete question 
			$dashboard->deleteDbeeJoinUserDbee($dbee_id); // delere Joined user
			$dashboard->deleteDbeeExpert($dbee_id); // delete expert
			$dashboard->deleteDbeeScoring($dbee_id); // delete score
	    }
	    return $response->setBody(Zend_Json::encode($_POST));
	}

	/* view all  comments */

	public function commentsAction()
	{
		$request = $this->getRequest()->getParams();
		$userpram = $request['uid'];
		if(isset($request['searchfield']) && $request['searchfield']!='')
			$seachfield = $request['searchfield'];
		else
			$seachfield = '';

		$deshboard= new Admin_Model_Deshboard();
		$latestCommentData = $deshboard->getLatestComment('','',$userpram);
		//echo'<pre>';print_r($latestCommentData);die;
		$page = $this->_getParam('page',1);
		/*
		 * Object of Zend_Paginator
		*/
		$paginator = Zend_Paginator::factory($latestCommentData);
		/*
		 * Set the number of counts in a page
		*/
		$paginator->setItemCountPerPage(20);
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

	public function userstalkingonAction()
	{
		$request = $this->getRequest()->getParams();
		
		if(isset($request['entity']) && $request['entity']!='')
			$seachfield = $request['entity'];
		else
			$seachfield = '';

		if(isset($request['polarity']) && $request['polarity']!='')
			$typefield = $request['polarity'];
		else
			$typefield = '';

		$deshboard= new Admin_Model_Deshboard();
		$latestCommentData = $deshboard->getEntityComment('',$seachfield,$typefield);


		
		//echo'<pre>';print_r($latestCommentData);die;
		$page = $this->_getParam('page',1);
		/*
		 * Object of Zend_Paginator
		*/
		$paginator = Zend_Paginator::factory($latestCommentData);
		/*
		 * Set the number of counts in a page
		*/
		$paginator->setItemCountPerPage(20);
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
		$this->view->seachfield = $seachfield;
		$this->view->polarity = $typefield;
	}

	/* view all  Scores */

	public function scoresAction()
	{
	
		$request = $this->getRequest()->getParams();
		$userpram = $request['uid'];
		if(isset($request['searchfield']) && $request['searchfield']!='')
			$seachfield = $request['searchfield'];
		else
			$seachfield = '';

		$deshboard= new Admin_Model_Deshboard();
		$liveScoreData = $deshboard->getLiveScore('','',$userpram);

		



		$page = $this->_getParam('page',1);
		/*
		 * Object of Zend_Paginator
		*/
		$paginator = Zend_Paginator::factory($liveScoreData);
		/*
		 * Set the number of counts in a page
		*/
		$paginator->setItemCountPerPage(20);
		/*
		 * Set the current page number
		*/
		$paginator->setCurrentPageNumber($page);
		/*
		 * Assign to view
		*/
		
		$this->view->paginator = $paginator;
		$this->view->total = $paginator->getTotalItemCount();
		$this->view->page = $page;
		$this->view->totalpage = $paginator->count();
		$this->view->rectodis = $page*20;
		$this->view->lastpage = $paginator->count();

	}


	//**************** Started for reload functionality from dashboard click

	public function dbeereloadAction() 
	{
		$deshboard	= new Admin_Model_Deshboard();
		$request    =   $this->getRequest()->getPost();
		
		$texttype = " created ";
		$userid =$request['uid'];
		if($userid)$liveDbeeData = $deshboard->getuserdbee($userid);
		$liveDbeeData = $deshboard->getLiveDbee(4);

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
							<div class="font12" style="float:left; margin-left:10px; width:245px;">'.substr($dbVidDesc,0,100).'</div></div></p>';
			}
			if($liveDbee['type']==5) {
				$dbPollText			=	$liveDbee['PollText'];
				$dbtype	=	' a voting poll';
				$descDisplay	=	substr($dbPollText,0,100);
			}

				
            $liveDbepic = $this->defaultimagecheck->checkImgExist($liveDbee['image'],'userpics','default-avatar.jpg');
			//$checkupic = $defaultimagecheck->checkImgExist($liveDbee->image,'userpics','default-avatar.jpg');
			$blocksdbee .='<div class="userPic">
									<img src="'.IMGPATH.'/users/small/'.$liveDbepic.'" width="80" height="80" border="0" />
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
											$blocksdbee .='	<i> <a href="'.BASE_URL.'/admin/dashboard/post/id/'.$liveDbee['DbeeID'] .'">More</a> | <a href="'.BASE_URL.'/admin/dashboard/postreport/m_-_xxp=t/'.base64_encode($liveDbee->DbeeID).'">Post report</a></i>
										</span>
									</div>
									<br><p>'. $descDisplay .'</p>';
									if($liveDbee['Active']==0)
									{
										$blocksdbee .= '<p style="color:red">INACTIVE</p>';
									}

								$blocksdbee .='</div>

							</li>';
						 endforeach; 
		$blocksdbee .='	</ul>
							<div class="bfooter">
								<span class="bfTotal"> <strong>'.$deshboard->getTotalDbee().'</strong> total posts</span>';
								 if($deshboard->getTotalDbee()>4){
		$blocksdbee .='<a href="'.BASE_URL.'/admin/dashboard/post"  class="btn">View all</a>'; }
		$blocksdbee .='</div>
					</div>';
					 } else { 
		$blocksdbee .='	<div class="dashBlockEmpty">no posts found</div>';
			 }	
		$blocksdbee .='</div>';	
		echo $blocksdbee ;
		exit;
	}

	public function commentreloadAction()
	{
		$deshboard	= new Admin_Model_Deshboard();

		$latestCommentData = $deshboard->getLatestComment(4);

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
			<div class="userPic"><img src="'.IMGPATH.'/users/small/'.$latestCommentapic.'" width="80" height="80" border="0" /></div>
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


	public function groupreloadAction()
	{
		$deshboard	= new Admin_Model_Deshboard();

		$liveGroupData = $deshboard->getLiveGroup(4);
		//$topGroupActivityList = $deshboard->getTopactivegroup(0,4);
		$ret	='';

		if (count($liveGroupData)){
			$ret	.= '<div class="useractGroup" style="display:block"><ul>';
			foreach($liveGroupData as $liveGroup) :
			//echo'<pre>';print_r($liveGroup);die;	
            $liveGroupapic = $this->defaultimagecheck->checkImgExist($liveGroup['GroupPic'],'grouppics','default-avatar.jpg');
			
			$ret	.= '<li><div class="userPic"><img src="'.IMGPATH.'/grouppics/medium/'.$liveGroupapic.'" width="80" height="80" border="0" /></div>
						<div class="userDetails">
							<div class="usertitle">
								<a href="'.BASE_URL .'/group/groupdetails/group/'.$liveGroup['ID'].'" class="username" target="_blanck">'. htmlentities($liveGroup['GroupName'], ENT_QUOTES, "UTF-8").'</a>
							
						
								<span class="userposted">
									Created by <a href="javascript:void(0)" class="show_details_user" userid="'.$liveGroup->User.'">'. $this->myclientdetails->customDecoding($liveGroup['username']).' '.$this->myclientdetails->customDecoding($liveGroup['lname']).'</a>
								</span>
								<span class="userposted"> on '. date('d M, Y',strtotime($liveGroup['Gdate'])).'</span></br>
						<div class="nsinfo">';
						if($liveGroup->memcnt > 0){
						$mem = ($liveGroup['memcnt'] == 1)?' member':' members';
						$ret	.=  "<span class='userposted'><i>".$liveGroup['memcnt'].$mem." </i></span> ";
						if($liveGroup['dbcnt'] > 0)$postbe = "-";

					}
						if($liveGroup->dbcnt > 0) {
							$post = ($liveGroup['dbcnt'] == 1)?' post':' posts';
						$ret	.=  "<span class='userposted'><i>".$liveGroup['dbcnt'].$post." </i> </span>";
					}
				
					$ret	.= '</div>	
							</div>
							<p>';
						if($liveGroup['description']) {
							$ret	.= htmlentities(substr($liveGroup['description'],0,100), ENT_QUOTES, "UTF-8");
								} else {
							$ret	.=  '&nbsp';
								} 
					$ret	.= '</p>
						</div>';
			endforeach;
			$ret	.= '</ul>
			<div class="bfooter">
			<span class="bfTotal">total posts - <strong>'.$deshboard->getTotalGroup().'</strong></span>';
			if($deshboard->getTotalGroup()>4){
				$ret	.= '<a href="'.BASE_URL.'/admin/dashboard/groups" class="btn">View all</a>';
			}
			$ret	.= '</div></div>';
		} else {
			$ret	.= '<div class="dashBlockEmpty">no groups found</div></div>';
		}
        $topGroupActivityList = $deshboard->getTopactivegroup(0,4);
		//$ret	='';
        $totalActivegroup = $deshboard->getTopactivegroup();
		$nooftotalActivegroup = count($totalActivegroup);
		if (count($liveGroupData)){
			$ret	.= '<div class="mostserGroup" style="display:none"><ul>';
			foreach($topGroupActivityList as $topactiveGroup) :
			//echo'<pre>';print_r($topactiveGroup);die;	
            $liveGroupapic = $this->defaultimagecheck->checkImgExist($topactiveGroup['GroupPic'],'grouppics','default-avatar.jpg');
			
			$ret	.= '<li><div class="userPic"><img src="'.IMGPATH.'/grouppics/medium/'.$liveGroupapic.'" width="80" height="80" border="0" /></div>
						<div class="userDetails">
							<div class="usertitle">
								<a href="'.BASE_URL .'/group/groupdetails/group/'.$topactiveGroup['ID'].'" class="username" target="_blanck">'. htmlentities($topactiveGroup['GroupName'], ENT_QUOTES, "UTF-8").'</a>
							
						
								<span class="userposted">
									Created by <a href="javascript:void(0)" class="show_details_user" userid="'.$topactiveGroup['User'].'">'. $this->myclientdetails->customDecoding($topactiveGroup['Name']).' '.$this->myclientdetails->customDecoding($topactiveGroup['lname']).'</a>
								</span>
								<span class="userposted"> on '. date('d M, Y',strtotime($topactiveGroup['GroupDate'])).'</span></br>
						<div class="nsinfo">';
						/*if($liveGroup->memcnt > 0){
						$mem = ($liveGroup['memcnt'] == 1)?' member':' members';
						$ret	.=  "<span class='userposted'><i>".$liveGroup['memcnt'].$mem." </i></span> ";
						if($liveGroup['dbcnt'] > 0)$postbe = "-";

					}*/
						if($topactiveGroup['totalcount'] > 0) {
							$post = ($topactiveGroup['totalcount'] == 1)?'action':' actions';
						$ret	.=  "<span class='userposted'><i>".$topactiveGroup['totalcount'].$post." </i> </span>";
					}
				
					$ret	.= '</div>	
							</div>
							<p>';
						if($topactiveGroup['description']) {
							$ret	.= htmlentities(substr($topactiveGroup['description'],0,100), ENT_QUOTES, "UTF-8");
								} else {
							$ret	.=  '&nbsp';
								} 
					$ret	.= '</p>
						</div>';
			endforeach;
			$ret	.= '</ul>
			<div class="bfooter">
			<span class="bfTotal">total groups - <strong>'.$nooftotalActivegroup.'</strong></span>';
			if($nooftotalActivegroup>4){
				$ret	.= '<a href="'.BASE_URL.'/admin/dashboard/groups" class="btn">View all</a>';
			}
			$ret	.= '</div></div>';
		} else {
			$ret	.= '<div class="dashBlockEmpty">no groups found</div></div>';
		}
		echo $ret ;
		exit;
	}

	public function scorereloadAction()
	{
		$deshboard	= new Admin_Model_Deshboard();
		$configurations = $deshboard->getConfigurations();
		$post_score_setting = json_decode($configurations['ScoreNames'],true);

		$liveScoreData = $deshboard->getLiveScore(7);

		$ret	='';

		if (count($liveScoreData)){

			$ret	.= '<ul>';
			foreach($liveScoreData as $liveScore) :

			if($liveScore->type==1)  { $dbtype =   'post'; } else {   $dbtype = 'comment';  }
						
			if($liveScore->Score==1){ 
				//$scorediv   ='<span class="scoreSprite scoreLove"></span>' ; 
				$scorediv=$this->myclientdetails->ShowScoreIcon($post_score_setting[1]['ScoreIcon1']);
			} 
			else if ($liveScore->Score==2){ 
				//$scorediv   = '<span class="scoreSprite scoreLike"></span>' ;  
				$scorediv=$this->myclientdetails->ShowScoreIcon($post_score_setting[2]['ScoreIcon2']);
			} 
			else if ($liveScore->Score==3){ 
				//$scorediv   = '<span class="scoreSprite scoreFft"></span>' ;   
				//$scorediv=$this->myclientdetails->ShowScoreIcon($post_score_setting[1]['ScoreIcon1']);
			} 
			else if ($liveScore->Score==4){ 
				//$scorediv   = '<span class="scoreSprite scoreUnLike"></span>'; 
				$scorediv=$this->myclientdetails->ShowScoreIcon($post_score_setting[3]['ScoreIcon3']);
			} 
			else if ($liveScore->Score==5){ 
							//$scorediv   = '<span class="scoreSprite scoreHate"></span>' ;
							$scorediv=$this->myclientdetails->ShowScoreIcon($post_score_setting[4]['ScoreIcon4']); 

			}
            $livescorepic = $this->defaultimagecheck->checkImgExist($liveScore['image'],'userpics','default-avatar.jpg');
			$ret	.= '<li>
							<div class="userPic"><img src="'.IMGPATH.'/users/small/'.$livescorepic .'" width="80" height="80" border="0" /></div>
							<div class="userDetails">
								<span class="decsScoreCal"><a href="javascript:void(0)" class="show_details_user" userid="'. $liveScore->UserID.'"><span class="username">'.$this->myclientdetails->customDecoding($liveScore->username).' '.$this->myclientdetails->customDecoding($liveScore->lname).'</span></a> scored <a href="javascript:void(0)" class="show_details_user" userid="'. $liveScore->OwnerID.'"><span class="username">'. $this->myclientdetails->customDecoding($liveScore->Ownername).' '.$this->myclientdetails->customDecoding($liveScore->Ownerlname) .'&rsquo;s </span></a>
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
	//**************** End for reload functionality from dashboard click

	
	public function attendiesuserlist1Action()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$filter = new Zend_Filter_StripTags();
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		$dashbord		=	new Admin_Model_Deshboard();
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$request 	= 	$this->getRequest()->getPost();
	
			$result =	$dashbord->attendiesuserlist($request['id'],$request['type']);
			// print_r($result);
			$content = '';
			if(count($result)>0){
				foreach ($result as $value)
				{
					$ProfilePic = $this->defaultimagecheck->checkImgExist($value['ProfilePic'],'userpics','default-avatar.jpg');
					$content .= "<div class='userFatchList boxFlowers social".$value['Socialtype']."' title='".$this->myclientdetails->customDecoding($value['Name'])."' socialFriendlist='true'>
					<label class='labelCheckbox'><input type='checkbox' value='".$value['UserID']."' class='inviteuser-search' name='attendeduser'>
					<div class='follower-box'>
					<div class='usImg'><img src='".IMGPATH."/users/".$ProfilePic."'  border='0' /></div>"
					.$this->myclientdetails->customDecoding($value['Name'])."
					</div>
					</label>
					</div></div>";
				}
				$content .= "<input type='hidden' id='dbeeid' value='".$request['id']."' />";
			}else{
	
				$content .= "No record found";
			}
			$data['status'] = 'success';
			$data['content'] = $content;
		}
		else
		{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));
	
	}
	
	public function promotedpostAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$filter = new Zend_Filter_StripTags();
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		$dashbord		=	new Admin_Model_Deshboard();
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$request 	= 	$this->getRequest()->getPost();
			$data['QA'] = ($request['status']==1)?0:1;
			$result =	$this->myclientdetails->updatedata_global('tblDbees',$data,'DbeeID',$request['dbeeid']);
			$data['status'] = 'success';
		}
		else
		{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));
	
	}
	
	 
	public function attendiesuserlistAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$filter = new Zend_Filter_StripTags();
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		$dashbord		=	new Admin_Model_Deshboard();
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$request 	= 	$this->getRequest()->getPost();
			$result =	$dashbord->attendiesuserlist($request['id'],$request['type']);
			$content = '';
			if(count($result)>0)
			{
				foreach ($result as $value)
				{
					$ProfilePic = $this->defaultimagecheck->checkImgExist($value['ProfilePic'],'userpics','default-avatar.jpg');
					$content .= "<div class='userFatchList boxFlowers social".$value['Socialtype']."' title='".$this->myclientdetails->customDecoding($value['Name'])."' socialFriendlist='true'>
					<label class='labelCheckbox'>
					<div class='follower-box'><input type='checkbox' value='".$value['UserID']."' class='inviteuser-search' name='groupuser[]'>
					<div class='usImg'><img src='".IMGPATH."/users/small/".$ProfilePic."' border='0' /></div>"
					.$this->myclientdetails->customDecoding($value['Name'])."
					</div>
					</label>
					</div></div>";
					
				}
			}else
				$content .= "No record found";
				$data['status'] = 'success';
				$data['content'] = $content;
		
				$where= array('DbeeID'=>$request['id']);
				$result = $this->myclientdetails->getRowMasterfromtable('tblDbees',array('attendiesList'),$where);
				
				if($result['attendiesList']==0)
				{
					$checked ='';
				}else{
					$checked ='checked="checked"';
				}

				$content2 = '<div class="helponoff pull-right" value="'.$request['id'].'" style=" font-size: 12px; text-algin:center;">
					<input type="checkbox" '.$checked.'  id="attendisListTarget"><label for="attendisListTarget"><div off="Yes" on="No" class="onHelp"></div>
					<div class="onHelptext"><span>No</span><span>Yes</span></div></label>
					</div><span style=" font-size: 12px; float:right; display: inline-block; margin-top:6px; margin-right: 12px; font-weight: normal;">Hide attendee list in broadcast</span';
				$data['content2'] = $content2;
		}
		else
		{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));

	}

	public function hideattendieslistAction()
	{	 
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true); 
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{		 
			$DbeeID = (int)$this->_request->getPost('dbeeID');
			$status1 = $this->_request->getPost('status');
			
			if($status1 == 'true')
				$status = 1;
			else
				$status = 0;
			$dataArray = array('attendiesList'=>$status);
			$where = array('DbeeID'=>$DbeeID);
			$this->myclientdetails->updateMaster('tblDbees',$dataArray,$where);
			$data['status'] = 'success';
			$data['message'] = 'updated successfully';

		}else
		{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));
	}

	public function sendmailaccept($data,$dburls)
	{
		/****for email ****/
		//mann.delus@gmail.com,anildbee@gmail.com,porwal.deshbandhu@gmail.com       
		$EmailTemplateArray = array('uEmail'  => $data['Email'],
                                    'uName'   => $data['Name'],
                                    'lname'   => $data['lname'],
                                    'db_url'  => $dburls,
                                    'case'      => 5);
        $bodyContentmsgall = $this->dbeeComparetemplateOne($EmailTemplateArray); 
		/****for email ****/		
	}
	public function surveygroupuserlistAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$filter = new Zend_Filter_StripTags();
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$surveyid = $this->_request->getPost('DbeeID');
			$case = $this->_request->getPost('caseval');			
	
				$u= new Admin_Model_Usergroup();
				$common = new Admin_Model_Common();
				$dashboard = new Admin_Model_Deshboard();
				if($case==1)
				{
				 $result = $dashboard->surveyuserdetails($surveyid);
			    }
			    else if($case==2)
			    {
				 $result  = $dashboard->surveyAllCorrect($surveyid);
			    }
			    else
			    {
				 $result  = $dashboard->surveyAtLeastOneCorrect($surveyid);
			    }

				$grouprs = $u->list_groupall();
				if(count($result)>0){
					foreach ($result as $value)
					{
						$valuepic = $common->checkImgExist($value['ProfilePic'],'userpics','default-avatar.jpg');
						$content .= "<div class='userFatchList boxFlowers' title='".$this->myclientdetails->customDecoding($value['Name'])."' socialFriendlist='true'>
						<label class='labelCheckbox'>
						<input type='checkbox' value='".$value['UserID']."' checkvalue='".$checkvalue."' class='inviteuser-search goupuser' name='goupuserid'>
						<div class='follower-box'>
						<div class='usImg'><img class='img border'  align='left' src='".IMGPATH."/users/small/".$valuepic."' border='0' /></div>
						".$this->myclientdetails->customDecoding($value['Name'])."
						</div>
						</label>
						</div></div>";
					}
					if(count($grouprs)>0){			
						$grouplist = $common->addtogroupbutton();
					}
				}else{
					$content .= "<div class='dashBlockEmpty' style='width:95%'>no user found</div>";
				
			}
			$data['status'] = 'success';
			$data['content'] = $content;
			$data['grouplist'] = $grouplist;
			$data['post_title'] = $groupname;
		}
		else
		{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));
	}

	public function deletecommentAction(){
        $request = $this->getRequest()->getParams();
        $CommentID = $request['CommentID'];
        $deleteMsgemail = $this->deshboard->deletedata_global('tblDbeeComments','CommentID',$CommentID);
        echo'deltrue';exit;
    }

    function getTimeZoneFromIpAddress(){
	    $clientsIpAddress = $this->get_client_ip();

	    $geoplugin = new geoPlugin();

		if ($_SERVER['SERVER_NAME'] == 'localhost')
    		$clientInformation = $geoplugin->locate('182.64.210.137'); // for local
    	else
    		$geoplugin->locate($this->get_client_ip());

	    $clientsLatitude = $geoplugin->latitude;
	    $clientsLongitude = $geoplugin->geoplugin_longitude;
	    $clientsCountryCode = $geoplugin->countryCode;

	    $timeZone = $this->get_nearest_timezone($clientsLatitude, $clientsLongitude, $clientsCountryCode) ;

	    return $timeZone;

	}

	function get_client_ip() {
	    $ipaddress = '';
	    if (getenv('HTTP_CLIENT_IP'))
	        $ipaddress = getenv('HTTP_CLIENT_IP');
	    else if(getenv('HTTP_X_FORWARDED_FOR'))
	        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
	    else if(getenv('HTTP_X_FORWARDED'))
	        $ipaddress = getenv('HTTP_X_FORWARDED');
	    else if(getenv('HTTP_FORWARDED_FOR'))
	        $ipaddress = getenv('HTTP_FORWARDED_FOR');
	    else if(getenv('HTTP_FORWARDED'))
	        $ipaddress = getenv('HTTP_FORWARDED');
	    else if(getenv('REMOTE_ADDR'))
	        $ipaddress = getenv('REMOTE_ADDR');
	    else
	        $ipaddress = 'UNKNOWN';
	    return $ipaddress;
	}

	function get_nearest_timezone($cur_lat, $cur_long, $country_code = '') {
	    $timezone_ids = ($country_code) ? DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $country_code)
	        : DateTimeZone::listIdentifiers();

	    if($timezone_ids && is_array($timezone_ids) && isset($timezone_ids[0])) {

	        $time_zone = '';
	        $tz_distance = 0;

	        //only one identifier?
	        if (count($timezone_ids) == 1) {
	            $time_zone = $timezone_ids[0];
	        } else {

	            foreach($timezone_ids as $timezone_id) {
	                $timezone = new DateTimeZone($timezone_id);
	                $location = $timezone->getLocation();
	                $tz_lat   = $location['latitude'];
	                $tz_long  = $location['longitude'];

	                $theta    = $cur_long - $tz_long;
	                $distance = (sin(deg2rad($cur_lat)) * sin(deg2rad($tz_lat)))
	                    + (cos(deg2rad($cur_lat)) * cos(deg2rad($tz_lat)) * cos(deg2rad($theta)));
	                $distance = acos($distance);
	                $distance = abs(rad2deg($distance));
	                // echo '<br />'.$timezone_id.' '.$distance;

	                if (!$time_zone || $tz_distance > $distance) {
	                    $time_zone   = $timezone_id;
	                    $tz_distance = $distance;
	                }

	            }
	        }
	        return  $time_zone;
	    }
	    return 'unknown';
	}

	public function usermentionpostAction()
    {       
        /*$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);		
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);*/
        $dashboard = new Admin_Model_Deshboard();
        $html['userlist'] = $mentionusers = $dashboard->getTotalUsersPost();

       $this->jsonResponse('success', 200, $html);
       //return $response->setBody(Zend_Json::encode($data));
    }
	
	
	
}
