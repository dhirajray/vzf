<?php
class Admin_AdvertisementController extends IsadminController
{
	private $options;
    public function init()
    {
	   parent::init();
       //print_r($this->shortUrl(BASE_URL)); exit;
    }

    public function indexAction()
    {

	}
    /**
     *  edit advert
     */
	public function editAction()
    {
    	$this->view->id = $id = $this->_request->getParam('id');
        $this->view->type = $type = $this->_request->getParam('type');
	    $this->view->headerResult = $this->advert->getSingleHeaderAdvert($id,$type);
        $this->view->rightResult = $this->advert->getSingleRightAdvert($id,$type);
	}
    /**
     *  insert advert data and return json data
     */
    public function advertpushAction()
    {
        $data = array();
        $postData = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {   
            $advertTitle = $this->_request->getPost('adstitle');
            $edit = $this->_request->getPost('edit');
            $advertID = $this->_request->getPost('advertID');
            $vipgroup = $this->_request->getPost('vipgroup');
            $search = explode(',',$this->_request->getPost('search'));
            $rightSpeed = $this->_request->getPost('rightSpeed');
            $headerSpeed = $this->_request->getPost('headerSpeed');
            $type = $this->_request->getPost('type');
            if(!is_numeric($rightSpeed) && $rightSpeed!='')
            {
                $data['status'] = 'error';
                $data['message'] = 'Please enter a valid display time';
            }
            else if(!is_numeric($headerSpeed) && $headerSpeed!='')
            {
                $data['status'] = 'error';
                $data['message'] = 'Please enter a valid display time';
            }
            else if($advertTitle=='')
            {
                $data['status'] = 'error';
                $data['message'] = 'please add a title';
            }else
            {
                $dataArray = array('advertTitle'=>$advertTitle,
                                  'status'=>3,
                                  'type'=>$type,
                                  'clientID'=>clientID,
                                  'DATETIME'=>date("Y-m-d H:i:s"));
                $lastID = $this->advert->advertInsert($dataArray);
                // insert advert relation 
                if(count($search))
                {
                    foreach ($search as $value) 
                    {
                        $relationArray = array('relationID'=>$value,
                                                'clientID'=>clientID,
                                                'advertID' => $lastID,
                                                'DATETIME'=>date("Y-m-d H:i:s"));
                        $this->advertRelation->advertDataInsert($relationArray);
                    }
                }else
                {
                    $relationArray = array('relationID'=>$value,
                                            'clientID'=>clientID,
                                            'advertID' => $lastID,
                                            'DATETIME'=>date("Y-m-d H:i:s"));
                    $this->advertRelation->advertDataInsert($relationArray);
                }
                // insert advert relation

                if($lastID!=false)
                {
                    //include_once("resize_class.php");
                    $output_dir = Front_Public_Path.'ads/';
                    // right banner logic here
                    $rightImageArray = $this->_request->getPost('rightImageArray');
                    $slideShow = $this->_request->getPost('slideShow');
                    $countRightImage = count($rightImageArray); // get before loop bcz of optimization
                    $rightLinkArray = $this->_request->getPost('rightLinkArray');
                    $slideshow = $this->_request->getPost('slideshow');
                    $rightBanner['position'] = 'right';
                    $rightBanner['speed'] = $this->_request->getPost('rightSpeed')*1000; // make mili second
                    $rightBanner['effect'] = 'fade';
                    $rightBanner['advertID'] = $lastID;
                    $rightBanner['type'] = 'right';
                    $rightBanner['clientID'] = clientID;
                    
                    $rightBanner['slidershow'] = ($slideShow==1)? 1:0;

                    if($countRightImage>0)
                    {
                        for ($i=0; $i <$countRightImage; $i++) { 
                            $rightBanner['image'] = $rightImageArray[$i];
                            $rightBanner['link'] = $rightLinkArray[$i];
                            $rightBanner['clientID'] = clientID;
                            $this->advertdata->advertDataInsert($rightBanner);
                        }
                    }
                    // right banner logic here

                    // header banner logic here

                    $headerImageArray = $this->_request->getPost('headerImageArray');
                    $layout = $this->_request->getPost('layout');

                    $countHeaderImage = count($headerImageArray); // get before loop bcz of optimization
                    $headerLinkArray = $this->_request->getPost('headerLinkArray');
                    $headerBanner['position'] = 'header';
                    $headerBanner['layout'] = ($layout=='fullwidth')? 'full':'half';
                    $headerBanner['speed'] = $this->_request->getPost('headerSpeed')*1000; // make mili second
                    $headerBanner['effect'] = 'fade';
                    $headerBanner['advertID'] = $lastID;
                    $headerBanner['type'] = 'header';
                    $headerBanner['clientID'] = $this->clientID;

                    if($countHeaderImage>0)
                    {
                        for ($j=0; $j < $countHeaderImage; $j++) 
                        { 
                            $headerBanner['image'] = $headerImageArray[$j];
                            $headerBanner['link'] = $headerLinkArray[$j];
                            $headerBanner['clientID'] = clientID;
                            $this->advertdata->advertDataInsert($headerBanner);
                        }
                    }
                    // header banner logic here
                   $data['status']  = 'success';
                   $data['advertID']  = $lastID;
                   $data['message'] = 'advert has been saved successfully';
                }
            }
        } 
        return $response->setBody(Zend_Json::encode($data));
    }


    public function trackreportAction()
    {
      
    }
    

    public function adverteditAction()
    {
        $data = array();
        $postData = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {   
            $advertTitle = $this->_request->getPost('adstitle');
            $edit = $this->_request->getPost('edit');
            $advertID = $this->_request->getPost('advertID');
            $vipgroup = $this->_request->getPost('vipgroup');
            $search = explode(',',$this->_request->getPost('search'));
            $type = $this->_request->getPost('type');
            $rightSpeed = $this->_request->getPost('rightSpeed');
            $headerSpeed = $this->_request->getPost('headerSpeed');
            if(!is_numeric($rightSpeed) && $rightSpeed!='')
            {
                $data['status'] = 'error';
                $data['message'] = 'Please enter a valid display time';
            }
            else if(!is_numeric($headerSpeed) && $headerSpeed!='')
            {
                $data['status'] = 'error';
                $data['message'] = 'Please enter a valid display time';
            }
            else if($advertTitle==''){
                $data['status'] = 'error';
                $data['message'] = 'please add a title';
            }else
            {
                $dataArray = array('advertTitle'=>$advertTitle,
                                  'status'=>3,
                                  'type'=>$type,
                                  'clientID'=>clientID,
                                  'DATETIME'=>date("Y-m-d H:i:s"));
                $lastID = $this->advert->advertInsert($dataArray);
                if(count($search))
                {
                    foreach ($search as $value) 
                    {
                        $relationArray = array('relationID'=>$value,
                                                'clientID'=>clientID,
                                                'advertID' => $lastID,
                                                'DATETIME'=>date("Y-m-d H:i:s"));
                        $this->advertRelation->advertDataInsert($relationArray);
                    }
                }else
                {
                    $relationArray = array('relationID'=>$value,
                                            'clientID'=>clientID,
                                            'advertID' => $lastID,
                                            'DATETIME'=>date("Y-m-d H:i:s"));
                    $this->advertRelation->advertDataInsert($relationArray);
                }
                // insert advert relation

                if($lastID!=false)
                {
                    include("resize_class.php");
                    $output_dir = Front_Public_Path.'ads/';
                    // right banner logic here
                    $rightImageArray = $this->_request->getPost('rightImageArray');
                    $slideShow = $this->_request->getPost('slideShow');
                    $countRightImage = count($rightImageArray); // get before loop bcz of optimization
                    $rightLinkArray = $this->_request->getPost('rightLinkArray');
                    $slideshow = $this->_request->getPost('slideshow');
                    $rightBanner['position'] = 'right';
                    $rightBanner['speed'] = $this->_request->getPost('rightSpeed')*1000; // make mili second
                    $rightBanner['effect'] = 'fade';
                    $rightBanner['advertID'] = $lastID;
                    $rightBanner['type'] = 'right';
                    $rightBanner['clientID'] = clientID;
                    $rightBanner['slidershow'] = ($slideShow==1)? 1:0;

                    if($countRightImage>0)
                    {
                        for ($i=0; $i <$countRightImage; $i++) { 
                            $rightBanner['image'] = $rightImageArray[$i];
                            $rightBanner['link'] = $rightLinkArray[$i];
                            $rightBanner['clientID'] = clientID;
                            $this->advertdata->advertDataInsert($rightBanner);
                        }
                    }
                    // right banner logic here

                    // header banner logic here

                    $headerImageArray = $this->_request->getPost('headerImageArray');
                    $layout = $this->_request->getPost('layout');

                    $countHeaderImage = count($headerImageArray); // get before loop bcz of optimization
                    $headerLinkArray = $this->_request->getPost('headerLinkArray');
                    $headerBanner['position'] = 'header';
                    $headerBanner['layout'] = ($layout=='fullwidth')? 'full':'half';
                    $headerBanner['speed'] = $this->_request->getPost('headerSpeed')*1000; // make mili second
                    $headerBanner['effect'] = 'fade';
                    $headerBanner['advertID'] = $lastID;
                    $headerBanner['type'] = 'header';
                    $headerBanner['clientID'] = clientID;

                    if($countHeaderImage>0)
                    {
                        for ($j=0; $j < $countHeaderImage; $j++) 
                        { 
                            $headerBanner['image'] = $headerImageArray[$j];/*
                            $resizeObj = new resize($output_dir.$headerImageArray[$j]);
                            if($headerBanner['layout']=='full')
                                $resizeObj->resizeImage(1100, 130, 'crop');
                            else
                                $resizeObj->resizeImage(500, 95, 'crop');
                            $resizeObj->saveImage($output_dir.$headerImageArray[$j], 100);*/
                            $headerBanner['link'] = $headerLinkArray[$j];
                            $headerBanner['clientID'] = clientID;
                            $this->advertdata->advertDataInsert($headerBanner);
                        }
                    }
                    // header banner logic here
                   $data['status']  = 'success';
                   $data['advertID']  = $lastID;
                   $data['message'] = 'advert has been saved successfully';
                  
                }
            }
        } 
        return $response->setBody(Zend_Json::encode($data));
    }
	/**
     * upload right bannner
     */
    public function rightbannerAction()
    {
        $data = array();
        $postData = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest()) 
        {   
            $output_dir = Front_Public_Path.'ads/';
            if(isset($_FILES["file"]))
            {
              $ret = array();
              $fileCount = count($_FILES["file"]["name"]);
              for($i=0; $i < $fileCount; $i++)
              {
                $ext = pathinfo($_FILES['file']['name'][$i], PATHINFO_EXTENSION);
                $time = time().mt_rand(1,79632);
                $fileName   =   strtolower($time.'.'.$ext);
                move_uploaded_file($_FILES["file"]["tmp_name"][$i],$output_dir.$fileName);
                
                $ret['fileLinkText'] = "<div class='formRow' data-image='".$fileName."' ><input type='hidden' class='rightImageArray' name='rightImageArray[]' value='".$fileName."' /><label class='label'>Image link</label>
                        <div class='field'>
                        <input type='text' name='rightLinkArray[]' value='' />
                        <img style='margin-left:5px; vertical-align: middle;' src='".BASE_URL."/ads/".$fileName."' width='29' height = '29' />
                         <div class='clearfix'></div>
                            <span class='noteText'> Please ensure you have added http:// to the link URL<span>
                        </div></div>";
                        
                $ret['filename'] = $fileName;
              }
            }
        }
        else 
        {
            $data['status']  = 'error';
            $data['message'] = 'Seo settings not updated';
        }
        return $response->setBody(Zend_Json::encode($ret));
    }
    /**
     *  upload header bannner 
     */
    public function headerbannerAction()
    {
        $data = array();
        $postData = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest()) 
        {   
            $output_dir = Front_Public_Path.'ads/';
            if(isset($_FILES["file"]))
            {
                  $ret = array();
                  $fileCount = count($_FILES["file"]["name"]);
                  for($i=0; $i < $fileCount; $i++)
                  {
                    $ext = pathinfo($_FILES['file']['name'][$i], PATHINFO_EXTENSION);
                    $time = time().mt_rand(1,79632);
                    $fileName   =   strtolower($time.'.'.$ext);
                    move_uploaded_file($_FILES["file"]["tmp_name"][$i],$output_dir.$fileName);
                    $ret['fileLinkText'] = "<div class='formRow' data-image='".$fileName."' >
                    <label class='label'>Image link</label>
                    <div class='field'>
                        <input type='text' name='headerLinkArray[]' value='' />
                        <img style='margin-left:5px; vertical-align: middle;' src='".BASE_URL."/ads/".$fileName."' width='29' height = '29' />
                        <input type='hidden' class='headerImageArray' name='headerImageArray[]' value='".$fileName."' />
                         <div class='clearfix'></div>
                            <span class='noteText'> Please ensure you have added http:// to the link URL<span>
                        </div>
                    </div>
                        ";
                    $ret['filename'] = $fileName;
                  }
             }
        }
        else 
        {
            $data['status']  = 'error';
            $data['message'] = 'Seo settings not updated';
        }
        return $response->setBody(Zend_Json::encode($ret));
    }
    /**
     *  publish advert
     */
    public function publishAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest()) 
        {
            $advertID = $this->_request->getPost('advertID');
            $status = $this->_request->getPost('status');
            $OldadvertID = $this->_request->getPost('OldadvertID');
            $type = $this->_request->getPost('type');

            if($status=='publish')
            {
                if($type==1)
                    $this->advert->unpublishAllAdvert(array('status'=>0));
                
                $dataArray = array('status'=>1);
            }
            else
                $dataArray = array('status'=>0);
            $this->advert->publishAdvert($advertID,$dataArray);

            if($OldadvertID!='')
            {
                $this->advert->deleteAdvert($OldadvertID);
                $this->advertdata->deleteAdvert($OldadvertID);
                $this->advertRelation->deleteAdvert($OldadvertID);
            }
            $data['status']  = 'success';
            $data['message'] = 'Advert updated successfully';
        }
        else
        {
            $data['status']  = 'error';
            $data['message'] = 'Configuration has not been Set successfully';
        }
        return $response->setBody(Zend_Json::encode($data));
    }
    /**
     *  publish advert
     */
    public function deleteAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest()) 
        {
            $advertID = $this->_request->getPost('advertID');
            $this->advert->deleteAdvert($advertID);
            $this->advertdata->deleteAdvert($advertID);
            $this->advertRelation->deleteAdvert($advertID);
            $data['status']  = 'success';
            $data['message'] = 'advert deleted successfully';
        }
        else
        {
            $data['status']  = 'error';
            $data['message'] = 'has not been deleted';
        }
        return $response->setBody(Zend_Json::encode($data));
    }


    public function showuserlistAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $filter = new Zend_Filter_StripTags();
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        {
            $bannerid = $this->_request->getPost('bannerid');         
    
                $u= new Admin_Model_Usergroup();
                $common = new Admin_Model_Common();
                $advert = new Admin_Model_Advert();
                $result = $advert->trackuserdetails($bannerid);
                $grouprs = $u->list_groupall();                
                $grouplist='';
                if(count($result)>0){
                    foreach ($result as $value)
                    {
                        $valuepic = $common->checkImgExist($value['ProfilePic'],'userpics','default-avatar.jpg');
                        $content .= "<div class='userFatchList boxFlowers' title='".$this->myclientdetails->customDecoding($value['Name'])."' socialFriendlist='true'>
                        <label class='labelCheckbox'>
                        <input type='checkbox' value='".$value['UserID']."' checkvalue='".$checkvalue."' class='inviteuser-search' name='goupuserid'>
                        <div class='follower-box'>
                        <div class='usImg'><img class='img border' height='30' align='left' src='".IMGPATH."/users/".$valuepic."' border='0' /></div>
                        ".$this->myclientdetails->customDecoding($value['Name'])." ".$this->myclientdetails->customDecoding($value['lname'])."
                        <br>
                        Clicked ".$value['totalclick']." time(s)
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

}