<?php

class Admin_KnowledgecenterController extends IsadminController
{

	private $options;
	
	
    public function init()
    {
       parent::init();
        $auth = Zend_Auth::getInstance();   
        if ($auth->hasIdentity()) 
        {
            $data = $auth->getStorage()->read();
         
            $this->_userid = $data['UserID'];
        }
    }
   
	public function indexAction()
    {
        $request =  $this->getRequest()->getParams();
        $kcObj	 =	new Admin_Model_Knowldgecenter();
        $usergrpObj	 =	new Admin_Model_Usergroup();

        $allfolders 	= $kcObj->getFolders(0,'allfolder');
        $this->view->allfolders = $allfolders;
        if(count($allfolders)>0)
        {
        	if(!empty($request['parentId']))
        	{
        		$parentId	=	$request['parentId'];
        		$this->view->parentId 	= $request['parentId'];
        		if(empty($request['addedit']))
        			$this->view->parentDir 	= $request['folderName'];
        		else
        			$this->view->parentDir 	= str_replace(' ','_',$request['folderName']);
        	}
        	else
        	{
        		$parentId	=	$allfolders[0]['kc_id'];
        		$this->view->parentId 	= $allfolders[0]['kc_id'];
        		$this->view->parentDir 	= $allfolders[0]['kc_cat_title'];
        	}
	        $allfilesoffolder 	= $kcObj->getFolders($parentId,'foldernfiles');
	        $userset 	= $usergrpObj->list_groupall();
	        $this->view->allfilesoffolder = $allfilesoffolder;
	         $this->view->userset = $userset;
	         $this->view->usersetdatajson = json_encode($userset);
	        $this->view->totalfilesoffolder = count($allfilesoffolder);

	    } else
	    	$this->view->totalfilesoffolder = '0';
	    	 
    }


    public function catlistAction()
    {
        $data = array();
        $postData = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest()) 
        {  
        	$catid = (int) $this->_request->getPost('catid');
	    	$kcObj	 =	new Admin_Model_Knowldgecenter();
       		$usergrpObj	 =	new Admin_Model_Usergroup();
       		if($catid==0)
	 		$allfolders 	= $kcObj->getFolders2(0,'allfolder');	 
	 		else
	 		$allfolders 	= $kcObj->getFolders($catid,'foldernfiles');	
	 		 $data['content']  = $allfolders;
    	
		}
        else 
        {
            $data['status']  = 'error';
            $data['message'] = 'Seo settings not updated';
        }
        
		return $response->setBody(Zend_Json::encode($data));
    }
    
    
    public function getpdffilelistAction()
    {
    	$data = array();
    	$this->_helper->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	$response = $this->getResponse();
    	$response->setHeader('Content-type', 'application/json', true);
    	if($this->getRequest()->getMethod() == 'POST' && $this->getRequest()->isXmlHttpRequest())
    	{ 
    		
    		$parentId = (int) $this->_request->getPost('parentId');
    	    $parentname	=	str_replace(' ','_',$this->_request->getPost('folderName'));
	    	$kcObj	 =	new Admin_Model_Knowldgecenter();
	    	$allfilesoffolder 	= $kcObj->getFolders($parentId,'foldernfiles');
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
	    	$content = '<ul>';
	    	if(count($allfilesoffolder)>0)
	    	{
				foreach ($allfilesoffolder as $key => $value):		
					$exticon = '';	
					$ext = '';
					$ext = pathinfo($value['kc_file'], PATHINFO_EXTENSION); 
					$exticon = $iconexe[$ext];
					$exticon = ($exticon!='')?$exticon:'text';	
					$docurl = '';
					$view = "View";
					$isgallery = $value['galleryType'];
					
					if($exticon == 'image' || $exticon == 'audio' || $exticon == 'video'){
						$docurl = BASE_URL.'/'.CLIENTFOLDER.$parentname.'/'.$value['kc_file'];									
					}else if($value['galleryType']!=1){
						$docurl = '//docs.google.com/gview?url='.BASE_URL.'/'.CLIENTFOLDER.$parentname.'/'.$value['kc_file'].'&embedded=true';
					}else if($value['galleryType']==1){
						$docurl = BASE_URL.'/adminraw/knowledge_center/video_'.clientID.'/'.$value['kc_file'].'.mp4';
					}
					if($exticon == 'audio'){			
						$view = "Listen";
						$isgallery = "2";
					}
					if($exticon == 'video'){			
						$view = "Watch";
						$isgallery = "2";
					}
					$content .='<li  id="kc_filelist_'. $value['kc_id'].'" fileData="'.$docurl.'" fileID="'.$value['kc_id'].'" filedeletepath="'. $parentId.'/'.$value['kc_file'].'" >
					<i class="fa fa-file-'.$exticon.'-o kcIconPdfList" style="font-size:20px"></i>	
					<span id="kc_list_'. $value['kc_id'].'" class="kcFileName">
					'. str_replace('_',' ',$value['kc_cat_title']).'</span>';									
					$content .='<div style="float:right"><a class="btn btn-green btn-mini" etype="view" href="javascript:void(0);" data-videoID = "'.$ext.'" is-gallery = "'.$isgallery.'" ><i class="fa fa-eye-open"></i> '.$view.' </a><span class="sprt"> </span><a class="btn btn-green btn-mini" etype="edit"  href="javascript:void(0);">  Edit  </a><span class="sprt"> </span><a class="btn btn-green btn-mini" etype="share" href="#"><i class="fa fa-eye-open "></i>Share</a><span class="sprt"> </span><a class="btn btn-danger btn-mini" etype="delete"  href="javascript:void(0);">Delete</a></div>';									
									
					$content .='</li>';	
				endforeach; 
			 } else { 
					$content .='<li class="noCategory">
					No files in this category. 
					</li>';
				 }							
			 $content .='</ul>';
			$data['content']=$content;
			return $response->setBody(Zend_Json::encode($data));
    	}
    
    }

    public function videolistAction()
    {
    	$data = array();
    	$this->_helper->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	$response = $this->getResponse();
    	$response->setHeader('Content-type', 'application/json', true);
    	if($this->getRequest()->isXmlHttpRequest())
    	{
    		$resultVideo['listData'] = $this->myclientdetails->getAllMasterfromtable('tblVideo',array('*'),array('clientID'=>clientID));
			return $response->setBody(Zend_Json::encode($resultVideo));
    	}
    
    }


    public function expandfilesbaseAction()
    {
       
       	$request = 	$this->getRequest()->getPost();
		$kcObj	=	new Admin_Model_Knowldgecenter();
		$retStream =	'';
       	$folderId	=	$request['folderId'];
		$allfilesoffolder 	= $kcObj->getFolders($folderId,'foldernfiles');
		if(count($allfilesoffolder)>0)
		{
			foreach ($allfilesoffolder as $key => $value) 
			{
				$retStream .='<div id="kc_filelist_'.$value[kc_id] .'">
					<span id="kc_list_'. $value['kc_id'].'" style="width:440px;float:left;border-bottom:1px solid gray; padding:5px; ">'.str_replace('_',' ',$value['kc_cat_title']).' </span> <span class="deletefileList" id="'. $value['kc_id'].'" style="width:10px;padding:5px;border-bottom:1px solid gray; float:left; cursor:pointer "> X </span>
				</div>	';
		 	} 
		} else { 
			$retStream .='<div id="">
				<span  style="width:400px;float:left;border-bottom:1px solid gray; padding:25px; ">No files in this category. </span>

			</div>	';
		 }

		echo $retStream.'~1';	
       	exit;
    }
    public function updateusersetAction()
    {
    	$data = array();
    	$this->_helper->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	$response = $this->getResponse();
    	$response->setHeader('Content-type', 'application/json', true);
    	if($this->getRequest()->getMethod() == 'POST' && $this->getRequest()->isXmlHttpRequest())
    	{
		    	$request = 	$this->getRequest()->getPost();
		    	$kcObj	=	new Admin_Model_Knowldgecenter();
		    	$usergrpObj	=	new Admin_Model_Usergroup();
		    	$myclientdetails =new Admin_Model_Clientdetails();
		    	
		    	$userset			=	$request['userset'];
		    	$fileExs =	$request['filename'];
		    	$userID = $this->_userid;
		    
		    	$taguser		=	$request['taguser'];

		    	$isChecked = $request['alluser'];

		    	if($fileExs)
		    	{
		    		
		    		$adminFrontID =  $_SESSION['Zend_Auth']['storage']['UserID'];
		    			
		    		if(!empty($taguser)){
		    			$userarray = explode(',',$taguser);
		    			foreach($userarray as $value):
		    			$data2	 =	array('clientID'=>clientID,'userid'=>$value,'fileid'=>$fileExs,'sdate'=>date("Y-m-d H:i:s"));
		    			$chedata = $kcObj->chkfileuser($data2);
		    			//if($kcObj->chkfileuser($data2)){
		    			$fid = $kcObj->insertfileuser($data2);
		    			$activity = array('clientID'=>clientID,'act_type'=>'34','act_message'=>'37','act_typeId'=>$fileExs,'act_userId'=>$adminFrontID, 'act_ownerid'=>$value,'act_date'=>date("Y-m-d H:i:s") );
		    			$myclientdetails->insertdata_global('tblactivity',$activity);
		    			
		    			
		    			endforeach;
		    		}
		    		elseif(!empty($userset)){
		    			$userarray = $usergrpObj->groupuserbygroup($userset);
		    				
		    			foreach($userarray as $value):
		    			$data2	 =	array('clientID'=>clientID,'userid'=>$value['userid'],'fileid'=>$fileExs,'sdate'=>date("Y-m-d H:i:s"));
		    			$chedata = $kcObj->chkfileuser($data2);
		    			//if($kcObj->chkfileuser($data2)){
		    			$fid = $kcObj->insertfileuser($data2);
		    			$activity = array('clientID'=>clientID,'act_type'=>'34','act_message'=>'37','act_typeId'=>$fileExs,'act_userId'=>$adminFrontID, 'act_ownerid'=>$value['userid'],'act_date'=>date("Y-m-d H:i:s") );
		    
		    			$myclientdetails->insertdata_global('tblactivity',$activity);
		    			//}
		    			endforeach;
		    		}
		    		elseif($isChecked==1){
						$userarrayall = $kcObj->getalluser();
						foreach($userarrayall as $value):
						$data2	 =	array('clientID'=>clientID,'userid'=>$value['UserID'],'fileid'=>$fileExs,'sdate'=>date("Y-m-d H:i:s"));
						$fid = $kcObj->insertfileuser($data2);
						
						$activity = array('clientID'=>clientID,'act_type'=>'34','act_message'=>'37','act_typeId'=>$fileExs,'act_userId'=>$adminFrontID, 'act_ownerid'=>$value['UserID'],'act_date'=>date("Y-m-d H:i:s") );
						$myclientdetails->insertdata_global('tblactivity',$activity);
						endforeach; 
					}	
		    			
		    		if(isset($fileExs) && $fileExs!='')
		    			$content = 'Category added successfully~1~'.$chedata;
		    		else
		    			$content = 'Failed : Resources are busy, Please try again~4';
		    	}
		    	else
		    		$content = 'Failed : Resources are busy, Please try again~4';
	    	
	    	$data['content']=$content;
	    	return $response->setBody(Zend_Json::encode($data));
	    	}    
    	
    }
    public function createfilesbaseAction()
    {

		$data = array();
    	$this->_helper->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	$response = $this->getResponse();
    	$response->setHeader('Content-type', 'application/json', true);
    	if($this->getRequest()->getMethod() == 'POST' && $this->getRequest()->isXmlHttpRequest())
    	{
	       $request = 	$this->getRequest()->getPost();
	       $kcObj	=	new Admin_Model_Knowldgecenter();
	       $usergrpObj	=	new Admin_Model_Usergroup();
	       $myclientdetails =new Admin_Model_Clientdetails();
	       $folderNameRep	=	$this->_request->getPost('folderId');
	       $folderdir		=	$this->_request->getPost('folderDir');
	       $userset			=	$this->_request->getPost('userset');
	       $fileName		=	$this->_request->getPost('fileName');
	       $filetitle		=	$this->_request->getPost('fileTitle');
	       $isChecked		=	$this->_request->getPost('isChecked');
	     
	       $userID = $this->_userid;
		   $pdfuser		=	$request['pdfuser'];
			
				$data	 =	array('clientID'=>clientID,'kc_pid'=>$folderNameRep,'kc_cat_title'=>$filetitle,'kc_file'=>$fileName,'kc_adddate'=>date("Y-m-d H:i:s"), 'added_by'=>$userID);
				
				$fileExs =	$kcObj->insertdata($data);

				$adminFrontID =  $_SESSION['Zend_Auth']['storage']['UserID'];

				if($isChecked==1){
					$userarrayall = $kcObj->getalluser();
					foreach($userarrayall as $value):
					$data2	 =	array('clientID'=>clientID,'userid'=>$value['UserID'],'fileid'=>$fileExs,'sdate'=>date("Y-m-d H:i:s"));
					$fid = $kcObj->insertfileuser($data2);
					
					$activity = array('clientID'=>clientID,'act_type'=>'34','act_message'=>'37','act_typeId'=>$fileExs,'act_userId'=>$adminFrontID, 'act_ownerid'=>$value['UserID'],'act_date'=>date("Y-m-d H:i:s") );
					$myclientdetails->insertdata_global('tblactivity',$activity);
					endforeach; 
				}				
				elseif(!empty($pdfuser)){
					$userarray = explode(',',$pdfuser);				
					foreach($userarray as $value):
					$data2	 =	array('clientID'=>clientID,'userid'=>$value,'fileid'=>$fileExs,'sdate'=>date("Y-m-d H:i:s"));
					$fid = $kcObj->insertfileuser($data2);
					$activity = array('clientID'=>clientID,'act_type'=>'34','act_message'=>'37','act_typeId'=>$fileExs,'act_userId'=>$adminFrontID, 'act_ownerid'=>$value,'act_date'=>date("Y-m-d H:i:s") );
					$myclientdetails->insertdata_global('tblactivity',$activity);				
					endforeach; 
				}
				elseif(!empty($userset)){				
					$userarray = $usergrpObj->groupuserbygroup($userset);
						
					foreach($userarray as $value):
					$data2	 =	array('clientID'=>clientID,'userid'=>$value['userid'],'fileid'=>$fileExs,'sdate'=>date("Y-m-d H:i:s"));
					$fid = $kcObj->insertfileuser($data2);
					$activity = array('clientID'=>clientID,'act_type'=>'34','act_message'=>'37','act_typeId'=>$fileExs,'act_userId'=>$adminFrontID, 'act_ownerid'=>$value['userid'],'act_date'=>date("Y-m-d H:i:s") );
					
					$myclientdetails->insertdata_global('tblactivity',$activity);
					endforeach; 
				}

			}	
			
			else
				$data['Failed']=true;				
				$data['folderdir']= str_replace(' ','_',$folderdir);
				$data['fileid']= $fileExs;
				$data['userset']= $userset;
				$data['success']= 'success';
				$data['content']= $content;
	    	return $response->setBody(Zend_Json::encode($data));
      
   }

   public function sowpdfAction()
    {
    	$this->_helper->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	$request = $this->getRequest()->getParams();    	
    	 $dir = $request['d'];
    	 $file = $request['f'];    	    	
   		echo '<iframe src="'.BASE_URL.'/'.CLIENTFOLDER.$dir.'/'.$file.'" width=600,height=600></iframe>';
    	exit;
    }

   public function createbaseAction()
    {
       $request = 	$this->getRequest()->getPost();

       $kcObj	=	new Admin_Model_Knowldgecenter();
       $folderNameRep	=	str_replace(' ','_',$request['folderName']);
       $userID = $this->_userid;
       if ($request['require']=='deletefile') 
	   {
	   		unlink(CLIENTFOLDER.trim($request[fileDelpath]));
	   		$fileExs =	$kcObj->deletefile($request['fileId']);
			if(isset($fileExs) && $fileExs!='')
				echo 'File Deleted successfully~3';
			else
				echo 'Failed : Resources are busy, Please try again~4';
	   }

	   if ($request['require']=='deletefolder') 
	   {
	   		$data	 =	array('status'=>1);
			$fileExs =	$kcObj->updateFolder($data,$request['folderId']);
			if(isset($fileExs) && $fileExs!='')
				echo 'Category Deleted successfully~3';
			else
				echo 'Failed : Resources are busy, Please try again~4';
	   }

       if(isset($request['securitycheck']) && $request['securitycheck']==1)
			echo 'Please Use secure way tp perform action~4';
		
      	$fileExs =	$kcObj->chkFileExist($folderNameRep,$request['folderId']);

     	if($fileExs==0)
      	{
			if ($request['require']=='addfolder') 
			{
				if (isset($folderNameRep)) 
				{   
					$dir = $folderNameRep;
				    if (!file_exists(CLIENTFOLDER.$dir))
				    mkdir(CLIENTFOLDER.$dir, 0777);

			    	$data	 =	array('clientID'=>clientID,'kc_cat_title'=>$folderNameRep,'kc_adddate'=>date("Y-m-d H:i:s"), 'added_by'=>$userID);
					$fileExs =	$kcObj->insertdata($data);
					if(isset($fileExs) && $fileExs!='')
						echo 'Category added successfully~1~'.$fileExs;
					else
						echo 'Failed : Resources are busy, Please try again~4';
				}
			}
			else if ($request['require']=='editfolder') 
			{
				if (isset($folderNameRep)) 
				{   
					$dir 		= 	$folderNameRep;
					$prevtitle	=	 CLIENTFOLDER.str_replace(' ','_',$request['prevtitle']);
				    $newfolder  =  CLIENTFOLDER. $dir;
			    	@rename($prevtitle, $newfolder);
			    	$data	 =	array('clientID'=>clientID,'kc_cat_title'=>$folderNameRep,'kc_editdate'=>date("Y-m-d H:i:s"), 'updated_by'=>$userID);
					$fileExs =	$kcObj->updateFolder($data,$request['folderId']);
					if(isset($fileExs) && $fileExs!='')
						echo 'Category updated successfully~2~'.$request['folderId'];
					else
						echo 'Failed : Resources are busy, Please try again~4';
				}
			}
		}
		else
			echo "Category already available, Please create another one~4";
       exit;
    }

	public function updatefiletitleAction()
    {       
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest()) 
        {   
        	$kcObj	=	new Admin_Model_Knowldgecenter();
        	$fileid		=	$this->_request->getPost('fileid');
        	$filename 	=	$this->_request->getPost('fileName');
        	$data1 = array('kc_cat_title'=>$filename);
        	$fileExs =	$kcObj->updateFolder($data1,$fileid);
        	$data['content']= 'fffffffff';
         }else 
        {
            $data['status']  = 'error';
            $data['message'] = 'Seo settings not updated';
        }
        return $response->setBody(Zend_Json::encode($data));
    }

   public function fileuploadAction()
    {       
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest()) 
        {   
        	$folderdir	=	str_replace(' ','_', $this->_request->getPost('folderDir'));
            
            if(isset($_FILES["file"]))
            {
            	//$sdir = Front_Public_Path.'/adminraw/knowledge_center/'.$folderdir;
            	$sdir = $_SERVER["DOCUMENT_ROOT"].'/'.CLIENTFOLDER.$folderdir;
                 $shareimgre = array();
                 $fileCount = count($_FILES["file"]["name"]);
                for($i=0; $i < $fileCount; $i++)
              	{ 
              		$fileNamedd = '';
                    $ext = pathinfo($_FILES['file']['name'][$i], PATHINFO_EXTENSION);
                    $time = time().mt_rand(1,79632);
                    $fileNamedd   =   strtolower($time.'.'.$ext);
                    move_uploaded_file($_FILES["file"]["tmp_name"][$i],$sdir.'/'.$fileNamedd);
                    $filetype = $_FILES['file']['type'];
                    // convert video
	              	if(preg_match('/video\/*/',$filetype[0])!=false) 
					{
						$djjd = 'dddd';
						$duration = shell_exec("/usr/bin/ffmpeg -i ".$sdir.'/'.$fileNamedd." -s 500x400 -vcodec libx264 -strict -2 ".$sdir."/".strtolower($time).".mp4 2>&1;");
						$fileNamedd   =   strtolower($time).'.mp4';
					}else{
						
					}
                    
                    //$duration = shell_exec("/usr/bin/ffmpeg -i ".$filePath."/".$picture." -s 500x400 -vcodec libx264 -strict -2 ".$filePath."/".$file.".mp4 2>&1;");
                    $shareimgre['fileLinkText'] = "
                    <div class='field'>                        
                        <img style='margin-left:5px; vertical-align: middle;' src='".BASE_URL.'/'.CLIENTFOLDER.$dir."' width='29' height = '29' />
                        <input type='hidden' class='shareimg' name='pdffile' value='".$fileNamedd."' />
                        
                    </div>";                     
                    $shareimgre['filename'] =$fileNamedd;     
                       $shareimgre['dir'] = $sdir.$fileNamedd;
				}             
 			}        
 		}         
 	else {
		$shareimgre['status']  = 'error';            
		$shareimgre['message'] = 'Seo
		settings not updated';        
 	}        
  	return $response->setBody(Zend_Json::encode($shareimgre));    
 	}
}

