<?php

class Admin_Model_Layouts extends Zend_Db_Table_Abstract
{
     // Table name 
     protected $_name = 'tblUsers';
     // Schema name
     // protected $_use_adapter = "matjarna";
	 
	 protected $_dbTable;

    public function init()
    {
      // $this->_var = $var;
        $this->defaultimagecheck = new Admin_Model_Common();
        $this->myclientdetails = new Admin_Model_Clientdetails();
    } 
			
	public function setDbTable($dbTable)
    {
		if (is_string($dbTable)) {
			$dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;		
        return $this;
    } 
    public function getDbTable()
    {
		if (null === $this->_dbTable) {
            $this->setDbTable('Admin_Model_DbTable_User');
        }
        return $this->_dbTable;
    }
	
	/* 
        * Start of the Layouts creation for global search functions 
       
        * Started on 3 June 2013
    */

    public function scoringFromDb()
    {
        $resultArray = $this->myclientdetails->getRowMasterfromtable('tblConfiguration',array('ScoreNames'));
        $scorSet = Zend_Json::decode($resultArray['ScoreNames']);
        $scorCont = array();
        foreach ($scorSet as $key => $value) {
            if($key==1)  $scorCont['Love'] = $value['ScoreName'.$key];
            if($key==2)  $scorCont['Like'] = $value['ScoreName'.$key];
            if($key==3)  $scorCont['Dis Like'] = $value['ScoreName'.$key];
            //if($key==4)  $scorCont['F O T'] = $value['ScoreName'.$key];
            if($key==4)  $scorCont['Hate'] = $value['ScoreName'.$key];
        } 
        return $scorCont ;//= Zend_Json::encode($scorCont);

    }

    public function dbeeresultstemplate($liveDbeeData,$caller='')
    {
        $load = 0;
        $ret  = '';    
        $common = new Admin_Model_Common();
        $deshObj = new Admin_Model_Deshboard();           
         /*ini_set('display_errors',1);
error_reporting(E_ALL|E_STRICT);
ini_set('error_log','script_errors.log');
ini_set('log_errors','On');*/
        foreach($liveDbeeData as $liveDbee) :
            $ret .='<li>';
      
            if($liveDbee['Active']==0) { 
              $ret .='<div class="activateId bx bx-green pull-right" > 
                 <i class="fa fa-exclamation-circle fa-lg"></i> Click Activate to make this post live
              </div>
              <div class="clearfix"></div>';
            } else if($liveDbee['Active']==3) { 
              $ret .='<div class="activateId bx bx-green pull-right" > 
                 <i class="fa fa-exclamation-circle fa-lg"></i> Click Approve to make this post live
              </div>
              <div class="clearfix"></div>';
            } 
                       
         $descDisplay   ='';
         $dbtypebutton  =   '<a class="btn btn-full btn-mini btn-yellow" href="'.BASE_URL.'/admin/dashboard/postreport/m_-_xxp=t/'. base64_encode($liveDbee['DbeeID']).'"> Post report</a>';
        
         if($liveDbee['type']==1) { 
                if($liveDbee['LinkTitle']!='') {$dbtype = 'posted a link';}
                elseif($liveDbee['Pic']!='') {$dbtype = 'created an image post';}
                elseif($liveDbee['VidID']!='') {$dbtype = 'created a vedio post';}
                else{$dbtype  = 'created a text post';}     
         
         
             $dbtypeIcon        =   '<div class="pstype typeText"></div>';
             $descDisplay   =   '<div class="dbLinkDesc" style="float:left;">'.$this->myclientdetails->escape($liveDbee['description']).'</div>';//substr($liveDbee->Text,0,100).'</div>';
         }
         if( $liveDbee['Link']!='') { 
         
         $dbtype            =   'posted a link';
         $dbtypeIcon        =   '<div class="pstype typeLink"></div>';
         $dbLink            =   $liveDbee['Link'];
         $dbLinkTitle       =   $liveDbee['LinkTitle'];
         $dbLinkDesc        =   $liveDbee['LinkDesc'];
         $dbUserLinkDesc    =   !empty($liveDbee['UserLinkDesc']) ? $liveDbee['UserLinkDesc'] : $liveDbee['LinkTitle'];
         
         $descDisplay   =   '<div class="dbLinkDesc" style="float:left;">'.$dbUserLinkDesc.' - 
         <a target="_blank" href="'.$dbLink.'">'.$dbLink.'</a></div>';
         }
         if( $liveDbee['Pic']!='') {
         $dbtype    =   'created an image post';
         $dbtypeIcon    =   '<div class="pstype typePix"></div>';
         $dbPic     =   $liveDbee['Pic'];
         $dbPicDesc =   $this->myclientdetails->escape($liveDbee['description']);         
         if($dbPic!='')
         {
            $checkdbpic = $this->defaultimagecheck->checkImgExist($dbPic,'imageposts','default-avatar.jpg');
         }else
            $noPic = 'noPix';
          $descDisplay   .=  '<div class="dbPicDescs '.$noPic.'" style="float:left;">'.$dbPicDesc.'</div>';
         }
         if( $liveDbee['VidID']!='')
         {  
             $dbtype    =   'created a video post';
             $dbtypeIcon    =   '<div class="pstype typeVideo"></div>';
             $dbVid         =   $liveDbee['VidID'];
             $dbVidDesc     =   $this->myclientdetails->escape($liveDbee['description']);
             $dbUserLinkDesc    =   !empty($liveDbee['UserLinkDesc']) ? $liveDbee['UserLinkDesc'] : $liveDbee['LinkTitle'];
             $descDisplay   .=  '<div class="dbPicText" style="float:left;">';
        
             $descDisplay   .=  '<div class="dbPicDescs style="float:left;"'.$noPic.'">'.$dbVidDesc.'</div></div>';         
         }
         if($liveDbee['type']==5) { 
            $dbtype =   'created a voting poll';            
            $dbtypeIcon ='<div class="pstype typePoll"></div>';             
            $descDisplay    ='<div class="dbTextPoll" style="float:left;">'.$liveDbee['PollText'].'</div>';//substr($dbPollText,0,100).'</div>';
         } else
         {
            $descDisplay   =   '<div class="dbLinkDesc" style="float:left;">'.$this->myclientdetails->escape($liveDbee['description']).'</div>';
         }
        
        $liveDbeepic = $this->defaultimagecheck->checkImgExist($liveDbee['image'],'userpics','default-avatar.jpg');
        
        $schedulePost = '';
        if( $liveDbee['schedulepost']==1) $schedulePost = '<span  class="ScheduledPost" title="Scheduled post"  >'. $common->gettimeinterval($liveDbee['PostDate'],'<i class="fa fa-clock-o fa-lg"></i>').' </span>'; 
        else $schedulePost = '<span > '. date('d M Y',strtotime($liveDbee['PostDate'])).'</span>'; 

     
    
      $ret .='<div class="listUserPhoto">
         <img src="'.IMGPATH.'/users/medium/'.$liveDbeepic.'" width="70" height="70" border="0" class="recievedUsePic"/>
      </div>
      <div class="dataListWrapper">
         <div class="dataListbox">
            <div class="dataListCol1">
               <div class="scoredListTitle" style="font-size:14px;">
                   <a class="show_details_user" userid="'.$liveDbee['UserID'].'" href="javascript:void(0)">'. $this->myclientdetails->customDecoding($liveDbee['username']).' '.$this->myclientdetails->customDecoding($liveDbee['lname']).'</a>
                  </span>'. $dbtype.' - '. $schedulePost.'
               </div>
               <div class="dbPicDescs">'.$descDisplay.'
               </div>
                  <div class="dbPost" style="margin-top:10px;">
                     <div class="dbPicText">';
                     if($liveDbee['type']!=5) 
                     {
                        $ret .='<div class="dbPic">';
                           $Pic =$this->defaultimagecheck->checkImgExist($liveDbee['Pic'],'imageposts','linkimage.png');
                              if($Pic!='linkimage.png') {
                                $style='style="left:24%"';
                                $margin='355px';
                             
                          $ret .=' <img border="0" src="'.IMGPATH.'/imageposts/small/'. $Pic.'" width="100" >';
                        } else { 
                              $style='style="left:10%"';
                              $margin='205px';
                              }
                       
                        $ret .= ' </div>';
                     }
                       if($liveDbee['type']==5) { 
                           $ret .='<div class="groupeTextWrapper">';
                           $obj_home= new Admin_Model_Deshboard();
                            $ret .= $obj_home->Pollhelper($liveDbee['DbeeID']);
                            $ret .='</div>';
                            } 
                        if($liveDbee['Link']!="") {
                        $linkimage =$this->defaultimagecheck->checkImgExist($liveDbee['LinkPic'],'results','linkimage.png');

                         $ret .='<div class="makelinkWrp ">
                           <div class="makelinkDes otherlinkdis" style="margin:0px;">
                              <h2>'.$liveDbee['LinkTitle'].'</h2>
                              <div class="desc">'.$liveDbee['LinkDesc'].'</div>
                              <div class="makelinkshw">
                                 <a href="'.$liveDbee['Link'].'" target="_blank">'. $liveDbee['Link'].'</a>
                              </div>
                           </div>
                        </div>';
                        } 
                       if($liveDbee['VidSite']=="youtube") 
                       {
                         $ret .='<div  class="youTubeVideoPostWrp" style="width:100%; postion:relative;">
                           <div class="youTubeVideoPost" style="float:left;">
                              <a class="yPlayBtn"  href="#"><i class="fa fa-play-circle-o fa-5x"></i></a>
                              <a href="#">
                              <img border="0" video-id="'. $liveDbee['VidID'].'" src="https://i.ytimg.com/vi/'. $liveDbee['VidID'].'/0.jpg" width="186">
                              </a>
                           </div>
                           <div class="ytDesCnt" style="margin-left:'.$margin.'">
                              <h2>'. $liveDbee['VidTitle'].'</h2>
                              <p>'. $this->myclientdetails->dbSubstring($this->myclientdetails->escape($liveDbee['VidDesc']),'250','...').'</p>
                           </div>
                        </div>';
                       }
                       if($liveDbee['VidSite']=="dbcsp") 
                       { 
                         $ret .='<div class="youTubeVideoPostWrp">
                           <div class="youVideoPost">
                              <a class="yPlayBtn"  href="#"><i class="fa fa-play-circle-o fa-5x"></i></a>
                              <a href="javascript:void(0);" >
                               <div class="wistia_embed wistia_async_'.$liveDbee['Vid'].'" style="width:200px;height:130px;">&nbsp;</div>
                              </a>
                           </div>
                        </div>';
                     } 
                     if($liveDbee['twitter']) {
                         $ret .='<div class="twitterbird-dbee-feed dbPost">
                           <span class="twitterbird scoreSprite "></span>   
                           <span class="commonhashtag">'. htmlentities(substr($liveDbee['twitter'],0,100)).'</span>
                             
                        </div>';
                      } 
                     
                    if($liveDbee['DbTag']) {
                           $slicetag=explode(',',$liveDbee['DbTag']);
                           
                           if(count($slicetag) > 0) {
                    
                         $ret .='<div class="twitterbird-dbee-feed dbPost"><span class="dbSign"> </span>';
                         
                              foreach ($slicetag as $value) {
                              
                                 $ret .='<span class="commonhashtag"># '. $value.'</span>';
                              }
                            
                         $ret .='</div>';
                      } } 
                       if($caller=='post')
                      {
                         if($liveDbee['GroupID']!=0 && $liveDbee['GroupID']!='') {
                            $ret .=' <div class="twitterbird-dbee-feed dbPost" style="margin-top:7px;">
                               <span>Group: </span> 
                               <span <a href="javascript:void(0)">'. $liveDbee['GroupName'].'</a></span>
                            </div>';
                        }
                      }
                     $ret .='<div class="usrcomment twitterbird-dbee-feed dbPost" style="display: inline-block;">';
                              $delBTN = '';
                              $totcmnt  =   $deshObj->getTotalComments($liveDbee['DbeeID']);
                              if($totcmnt==0)
                              {
                                 $ret .= "<strong>".$totcmnt ." comments </strong>";

                                 $delBTN = '<a class="btn btn-full btn-mini btn-danger deleteDbee" href="javascript:void(0)" dbeeID="'.$liveDbee['DbeeID'].'"> Delete </a>';
                              }
                              elseif($totcmnt>0 && $totcmnt==1)
                              {
                                 $ret .= "<strong>".$totcmnt ." comment </strong>";
                              }
                              elseif($totcmnt>0 && $totcmnt>1)
                              {
                                 $ret .= "<strong>".$totcmnt ." comments </strong>";
                              }
          
                         $ret .='</div>
                     </div>
                  </div>
            </div>
            <div class="dataListCol2">
               <div  class="helponoff userActiveInactive updateDbeeUserStatus approveBtn">';
               $lableBtn ='<label for="switcherTarget_'. $liveDbee['DbeeID'].'">
                     <div class="onHelp" on="Activate" off="Inactive"></div>
                     <div class="onHelptext">
                        <span>Activate</span>
                        <span>Deactivate</span>
                     </div>
                  </label>';

                if($liveDbee['Active']==3) {
                    $ret .=' <input type="checkbox" id="switcherTarget_'.$liveDbee['DbeeID'].'" user_id="'. $liveDbee['DbeeID'].'"  status="2">';
                    $lableBtn = '<a class="btn btn-full btn-mini btn-green  updateDbeeUserStatusApproved"  href="javascript:void(0)" status="2" user_id="'. $liveDbee['DbeeID'].'"> Approve  </a>';
                }
                else  if($liveDbee['Active']==1) { 
                    $ret .=' <input type="checkbox" id="switcherTarget_'.$liveDbee['DbeeID'].'" user_id="'. $liveDbee['DbeeID'].'"  status="0">';
                }else{ 
                  $ret .='<input type="checkbox" id="switcherTarget_'. $liveDbee['DbeeID'].'" checked="checked" user_id="'.$liveDbee['DbeeID'].'"  status="1"> ';
                } 
               
                $ret .=$lableBtn.'</div>';
                
                if($liveDbee['type']==20 && $liveDbee['QA']==0){
                  $ret .='<a class="btn btn-full btn-mini btn-green promotedPost" data-status ="'.$liveDbee['QA'].'" data-id="'.$liveDbee['DbeeID'].'" href="javascript:void(0);" target="_blank"> Promote </a>';
                }else if($liveDbee['type']==20 && $liveDbee['QA']==1){
                  $ret .='<a class="btn btn-full btn-mini btn-danger promotedPost" data-status ="'.$liveDbee['QA'].'" data-id="'.$liveDbee['DbeeID'].'" href="javascript:void(0);" target="_blank"> Unpromote </a>';
                }
                $ret .=$dbtypebutton;
               $ret .='<a class="btn btn-full btn-mini btn-yellow" href="'.BASE_URL.'/dbee/'.$liveDbee['dburl'] .'" target="_blank"> View </a>';
               
               
              $ret .=$delBTN.'
            </div>
         </div>
      </div>
     </li> ';   
     endforeach;         
       
        return $ret;
    }

    public function onlydbeetemplate($liveDbeeData,$caller='')
    {
        $load = 0;
        $ret  = '';    
        $common = new Admin_Model_Common();
        $deshObj = new Admin_Model_Deshboard();           
        
        foreach($liveDbeeData as $liveDbee) :
          
         if($liveDbee['type']==1) { 
                if($liveDbee['LinkTitle']!='') {$dbtype = 'posted a link';}
                elseif($liveDbee['Pic']!='') {$dbtype = 'created an image post';}
                elseif($liveDbee['VidID']!='') {$dbtype = 'created a vedio post';}
                else{$dbtype  = 'created a text post';}     
         
         
               $dbtypeIcon        =   '<div class="pstype typeText"></div>';
               $descDisplay   =   '<div class="dbLinkDesc" style="float:left;">'.$this->myclientdetails->escape($liveDbee['description']).'</div>';//substr($liveDbee->Text,0,100).'</div>';
         }
         if( $liveDbee['Link']!='') { 
         
           $dbtype            =   'posted a link';
           $dbtypeIcon        =   '<div class="pstype typeLink"></div>';
           $dbLink            =   $liveDbee['Link'];
           $dbLinkTitle       =   $liveDbee['LinkTitle'];
           $dbLinkDesc        =   $liveDbee['LinkDesc'];
           $dbUserLinkDesc    =   !empty($liveDbee['UserLinkDesc']) ? $liveDbee['UserLinkDesc'] : $liveDbee['LinkTitle'];
           
           $descDisplay   =   '<div class="dbLinkDesc" style="float:left;">'.$dbUserLinkDesc.' - 
           <a target="_blank" href="'.$dbLink.'">'.$dbLink.'</a></div>';
         }
         if( $liveDbee['Pic']!='') {
           $dbtype    =   'created an image post';
           $dbtypeIcon    =   '<div class="pstype typePix"></div>';
           $dbPic     =   $liveDbee['Pic'];
           $dbPicDesc =   $this->myclientdetails->escape($liveDbee['description']);         
           if($dbPic!='')
           {
              $checkdbpic = $this->defaultimagecheck->checkImgExist($dbPic,'imageposts','default-avatar.jpg');
           }else
              $noPic = 'noPix';
           
           $descDisplay   .=  '<div class="dbPicDescs '.$noPic.'" style="float:left;">'.$dbPicDesc.'</div>';//substr($dbPicDesc,0,100).'</div>';
         }
         if( $liveDbee['VidID']!='')
         {  
             $dbtype    =   'created a video post';
             $dbtypeIcon    =   '<div class="pstype typeVideo"></div>';
             $dbVid         =   $liveDbee['VidID'];
             $dbVidDesc     =   $this->myclientdetails->escape($liveDbee['description']);
             $dbUserLinkDesc    =   !empty($liveDbee['UserLinkDesc']) ? $liveDbee['UserLinkDesc'] : $liveDbee['LinkTitle'];
             $descDisplay   .=  '<div class="dbPicText" style="float:left;">';
        
             $descDisplay   .=  '<div class="dbPicDescs style="float:left;"'.$noPic.'">'.$dbVidDesc.'</div></div>';         
         }
         $description = $this->myclientdetails->escape($liveDbee['description']);
         if($liveDbee['type']==5) { 
              $description =$liveDbee['PollText'];
         }
        
       
     
    
        $ret .='<div class="dataListbox">
                <div class="dataListCol1">
              
                  <div style="color:#0B0E0E">'.$description.' </div>
                  <div class="dbPost viewApprove">
                     <div class="dbPicText">
                        <div class="dbPic">';
                            $Pic =$this->defaultimagecheck->checkImgExist($liveDbee['Pic'],'imageposts','linkimage.png');
                            if($Pic!='linkimage.png') {
                              $style='style="left:24%"';
                              $margin='355px';

                              $ret .=' <img border="0" src="'.IMGPATH.'/imageposts/small/'. $Pic.'" width="130" >';
                            } else { 
                              $style='style="left:10%"';
                              $margin='205px';
                            }
                       
                        $ret .= ' </div>';
                        if($liveDbee['type']==5) { 
                          $ret .='<div class="groupeTextWrapper">';
                          $obj_home= new Admin_Model_Deshboard();
                          $ret .= $obj_home->Pollhelper($liveDbee['DbeeID']);
                          $ret .='</div>';
                        } 
                        if($liveDbee['Link']!="") {
                          $linkimage =$this->defaultimagecheck->checkImgExist($liveDbee['LinkPic'],'results','linkimage.png');

                          $ret .='<div class="makelinkWrp ">
                           <img src="'.BASE_URL.'/timthumb.php?src=/results/'.$linkimage.'&q=100&w=130" border="0">
                           <div class="makelinkDes">
                              <h2>'.$liveDbee['LinkTitle'].'</h2>
                              <div class="desc">'.$liveDbee['LinkDesc'].'</div>
                              <div class="makelinkshw">
                                 <a href="'.$liveDbee['Link'].'" target="_blank">'. $liveDbee['Link'].'</a>
                              </div>
                           </div>
                          </div>';
                        } 
                        if($liveDbee['Vid']!="") {
                         $ret .='<div  class="youTubeVideoPostWrp">
                             <div class="youTubeVideoPost" style="float:left; width:30%; margin-right:10px;">
                                <a class="yPlayBtn"  href="#"><i class="fa fa-play-circle-o fa-5x"></i></a>
                                <a href="#">
                                <img border="0" video-id="'. $liveDbee['VidID'].'" src="https://i.ytimg.com/vi/'. $liveDbee['VidID'].'/0.jpg" width="186">
                                </a>
                             </div>
                             <div class="ytvdTxt">
                                <h2>'. $liveDbee['VidTitle'].'</h2>
                                <p>'. $this->myclientdetails->dbSubstring($this->myclientdetails->escape($liveDbee['VidDesc']),'250','...').'</p>
                             </div>
                          </div>';
                        } 
                        if($liveDbee['GroupID']!=0 && $liveDbee['GroupID']!='') {
                          $ret .=' <div class="twitterbird-dbee-feed dbPost" style="margin-top:7px;">
                             <span>Group: </span> 
                             <span <a href="javascript:void(0)">'. $liveDbee['GroupName'].'</a></span>
                          </div>';
                        }
                     
                    $ret .='</div></div></div>
                 
      </div> ';   
     endforeach;         
       
        return $ret;
    }

    public function userresultstemplate($liveDbeeData , $calling='')
    {
        $load = 0;
        $ret  = '';    
        //echo "<pre>"; print_r($liveDbeeData);      
        foreach($liveDbeeData as $liveDbee) :
            $load++;
            $liveDbePic = $this->defaultimagecheck->checkImgExist($liveDbee['image'],'userpics','default-avatar.jpg');
            $ret .= '<li class="" userid="'.$liveDbee['UserID'].'">'; 
            if($liveDbee['totLogins']>0) $loginSession = 'Total logins for '.date('m/d/Y', strtotime($liveDbee['logindate'])).': '.$liveDbee['totLogins'];

            /*if($liveDbee['logindate']!='') {
              $loginSession = 'Login period - '.$liveDbee['logindate'].'  ';
              $logeddOut    = ($liveDbee['logoutdate']=='0000-00-00 00:00:00')? '' : ' to '. $liveDbee['logoutdate']; 
              $loginSession = $loginSession.$logeddOut;
            } */

            $descDisplay    ='';
            if($calling==''){
            $ret .='<div class="listUserPhoto">
                    <img src="'.IMGPATH.'/users/small/'.$liveDbePic.'" width="70" height="70" border="0" class="show_details_user recievedUsePic"  userid="'.$liveDbee['UserID'].'"/>
                </div>
                <div class="dataListWrapper">
                    <div class="dataListbox">
                        <div class="scoredListTitle">
                            <a class="show_details_user" userid="'.$liveDbee['UserID'].'" href="javascript:void(0)">'.htmlentities($this->myclientdetails->customDecoding($liveDbee['Name'])).' '.htmlentities($this->myclientdetails->customDecoding($liveDbee['lname'])). '</a>                           
                        </div>
                        <div class="scoredData">
                            <div class="dbPost">'.htmlentities($this->myclientdetails->customDecoding($liveDbee['Email'])).'&nbsp;</div><div class="dbPost">'.$this->myclientdetails->customDecoding($liveDbee['country_name']).'&nbsp;</div>';
                             
                        $ret .=$loginSession.'</div>
                    </div>
                    <div class="scoredPostDate">Registered on  - '.date('d M Y',strtotime($liveDbee['RegistrationDate'])).'</div>
                </div> </li>'; 
                } else{  
                 $ret .='<div class="listUserPhoto ">
                             <img src="'.IMGPATH.'/users/medium/'.$liveDbePic.'" width="70" height="70" border="0" class="show_details_user recievedUsePic"  userid="'.$liveDbee['UserID'].'"/>
                         </div>
                          <div class="ursNm">'.htmlentities($this->myclientdetails->customDecoding($liveDbee['Name'])).' '.htmlentities($this->myclientdetails->customDecoding($liveDbee['lname'])).'<br><div class="oneline " style="color:#fc9908">@'.$this->myclientdetails->customDecoding($liveDbee['Username']).'</div></div>
                        </li>'; 
            }

        $rowchange++; endforeach;        
        return $ret;
    }

    public function groupresultstemplate($liveDbeeData)
    {
        $commonobj = new Admin_Model_Common();
        $load = 0;
        $liveGroupPic = $this->defaultimagecheck->checkImgExist($liveGroup['GroupPic'],'userpics','default-avatar.jpg');
        $ret  = '';                    
        foreach($liveDbeeData as $liveGroup) : 
            $ret .='<li>
                    <div class="listUserPhoto">
                            <img src="'.IMGPATH.'/users/medium/'.$liveGroupPic.'" width="70" height="70" border="0"  class="recievedUsePic"/>
                        </div>
                        <div class="dataListWrapper">
                            <div class="dataListbox">
                                <div class="scoredListTitle">
                                    <a class="show_details_user" userid="'.$liveGroup['UserID'].'" href="javascript:void(0)" style="font-weight:normal">'.htmlentities($liveGroup['GroupName']).'</a> 
                                    created by&nbsp;<a class="show_details_user" userid="'.$liveGroup['UserID'].'" href="javascript:void(0)">'.htmlentities($this->myclientdetails->customDecoding($liveGroup['username'])).'</a> <span class="titleListDate">On '.date('d M Y',strtotime($liveGroup['GroupDate'])).'</span>                               
                                </div>';
                                
                                if(htmlentities($liveGroup['GroupDesc'])) {
                                    $ret .='<div class="scoredData">'.htmlentities(substr($liveGroup['GroupDesc'],0,100)).'</div>';
                                }
                                $ret .='</div>
                            
                            <div class="scoredPostDate">'.$commonobj->getspecialdbType_CM($liveGroup['GroupPrivacy']).'</div>
                        </div>
                    <div style="clear:both"></div>    
                </li>';   
        $rowchange++; endforeach;

        return $ret;
    }

    public function commentresultstemplate($liveDbeeData)
    {
        $commonobj = new Admin_Model_Common();
        $load = 0;
        $ret  = '';
        foreach($liveDbeeData as $liveDbee) :
            $load++;
            $ret .= '<li>';
                    
            $descDisplay    ='';

            if($liveDbee['Type']==1) { 
                $dbtype         =   'text db';
                $dbtypeIcon =   '<div class="pstype typeText"></div>';
                $descDisplay    =   '<div class="dbLinkDesc">'.htmlentities($liveDbee['Comment']).'</div>';//substr($liveDbee['Text'],0,100).'</div>';
            }
            if($liveDbee['Type']==2) { 
                $dbtype         =   'link db';
                $dbtypeIcon =   '<div class="pstype typeLink"></div>';
                $dbLink         =   $liveDbee['Link'];
                $dbLinkTitle    =   $liveDbee['LinkTitle'];
                $dbLinkDesc     =   $liveDbee['LinkDesc'];
                $dbUserLinkDesc =   !empty($liveDbee['UserLinkDesc']) ? $liveDbee['UserLinkDesc'] : $liveDbee['LinkTitle'];

                $descDisplay    =   '<div class="dbLinkDesc">'.htmlentities($dbUserLinkDesc).' - 
                <a target="_blank" href="'.$dbLink.'">'.$dbLink.'</a>
                </div>';
                }
            if($liveDbee['Type']==3) { 
            $dbtype =   'pix db';
            $dbtypeIcon =   '<div class="pstype typePix"></div>';
            $dbPic      =   $liveDbee['Pic'];
            $dbPicDesc  =   $liveDbee['PicDesc']; 
            $checkdbeepic = $this->defaultimagecheck->checkImgExist($dbPic,'imageposts','default-avatar.jpg');              
            if($dbPic!='')
            {
                $descDisplay    .=  '<div class="dbPic"><a href="#"><img src="'.IMGPATH.'/imageposts/small/'.$checkdbeepic.'" width="90" border="0" /></a></div>';
            }
            else{

                $noPic = 'noPix';
            }                   
            $descDisplay    .=  '<div class="dbPicDesc '.$noPic.'">'.htmlentities($dbPicDesc).'</div>';//substr($dbPicDesc,0,100).'</div>';
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
                $descDisplay    .=  '<div class="dbPicDesc '.$noPic.'">'.htmlentities($dbVidDesc).'</div></div>';
            
            
            }
            $checkliveDbpic = $this->defaultimagecheck->checkImgExist($liveDbee['ProfilePic'],'userpics','default-avatar.jpg'); 
            $ret .='<div class="listUserPhoto">
                    <img src="'.IMGPATH.'/users/medium/'.$checkliveDbpic.'" width="70" height="70" border="0" class="recievedUsePic"/>
                </div>
                <div class="dataListWrapper">
                    <div class="dataListbox">
                        <div class="scoredListTitle">
                            '. $dbtypeIcon.' <a class="show_details_user" userid="'.$liveDbee['UserID'].'" href="javascript:void(0)">'.htmlentities($this->myclientdetails->customDecoding($liveDbee['Name'])).'</a>
                            has commented on  </span><a class="show_details_user" userid="'.$liveDbee['ouid'].'" href="javascript:void(0)">'.$this->myclientdetails->customDecoding($liveDbee['oname']).'</a>  '.$dbtype.'<a class=""  href="'.BASE_URL.'/dbee/'.$liveDbee['dburl'].'" target="_blank"> see db</a>                           
                        </div>
                        <div class="scoredData">
                            <div class="dbPost">'.htmlentities($descDisplay).'&nbsp;</div>';
                             if($liveDbee['twitter']) {
                            $ret .='<div class="twitterbird-dbee-feed dbPost">
                                <span class="twitterbird scoreSprite "></span>'.htmlentities(substr($liveDbee->twitter,0,100)).'
                            </div>  ';
                             } 
                        $ret .='</div>
                    </div>
                    <div class="scoredPostDate">Commented on  - '.date('d M Y',strtotime($latestComment['CommentDate'])).'</div>
                </div> </li>';

        
        $rowchange++; endforeach; 
       return $ret;
    }
    public function scorenuserresultstemplate($liveDbeeData)
    {
        $commonobj = new Admin_Model_Common();
        $deshObj   = new Admin_Model_Deshboard();
        $load = 0;
        $ret  = '';                    
        foreach($liveDbeeData as $liveScore) : 
                    
            $dbtype ='';
            $dbtype1='';
            $descDisplay    ='';
            
            if($liveScore['stype']==1)
            {
                $dbdetails  =   $deshObj->getScoredbinfo($liveScore['stype'],$liveScore['ScoreID']);
                $dbtype1    =   'db';
                $dbdesc     =   $this->myclientdetails->escape($dbdetails[0]['description']);                           
            } else {
                $dbdetails  =   $deshObj->getScoredbinfo($liveScore['stype'],$liveScore['ScoreID']);
                $dbtype1    =   ' comment ';
                $dbdesc     =   $dbdetails[0]['Comment'];
            }
                        
        
            if($dbdetails[0]['type']==1) { 
                $descDisplay    =   '<div class="dbTextPoll">'.$dbdesc.'</div>';
            }
            if($dbdetails[0]['type']==2) { 
                $descDisplay    ='';
                $dbLink         =   $dbdetails[0]['Link'];
                $dbLinkTitle    =   $dbdetails[0]['LinkTitle'];
                $dbLinkDesc     =   $dbdetails[0]['LinkDesc'];
                $dbUserLinkDesc =   !empty($dbdetails[0]['UserLinkDesc']) ? $dbdetails[0]['UserLinkDesc'] : $dbdetails[0]['LinkTitle'];

                $descDisplay    =   '<div class="dbLinkDesc">'.$dbUserLinkDesc.' - 
                    <a target="_blank" href="'.$dbLink.'">'.$dbLink.'</a>                           
                </div>';
            }
            
            if($dbdetails[0]['type']==3) { 
             $descDisplay   ='';

            $dbPic      =   $dbdetails[0]['Pic'];
            $dbPicDesc  =   $dbdetails[0]['PicDesc'];
            
            $descDisplay    .=  '<div class="dbPicText">';
            if($dbPic!='')
            {
                $chDbpic = $this->defaultimagecheck->checkImgExist($dbPic,'imageposts','default-avatar.jpg');
                $descDisplay    .=  '<div class="dbPic" ><a href="#"><img src="'.IMGPATH.'/imageposts/small/'.$chDbpic.'" width="90" border="0"/></a></div>';
            }
            else{
                $noPic = 'noPix';
            }
            
            $descDisplay    .=  '<div class="dbPicDesc '.$noPix.'">'.$dbPicDesc.'</div></div>';
            
            }
            
            if($dbdetails[0]['type']==4) { 
            $descDisplay    ='';
            $dbVid          =   $dbdetails[0]['VidID'];
            $dbVidDesc      =   $dbdetails[0]['VidDesc'];
            $dbLinkDesc     =   $dbdetails[0]['LinkDesc'];
            $dbUserLinkDesc =   !empty($dbdetails[0]['UserLinkDesc']) ? $dbdetails[0]['UserLinkDesc'] : $dbdetails[0]['LinkTitle'];

            $descDisplay    .=  '<div class="dbPicText">';
            if($dbVid!='')
            {
                $descDisplay    .=  '<div class="dbPic" ><a href="javascript:void(0)"><img width="90" height="60" border="0" src="https://i.ytimg.com/vi/'.$dbVid.'/0.jpg"></a></div>';
            }
            else{
                $noPic = 'noPix';
            }
            $descDisplay    .=  '<div class="dbPicDesc '.$noPix.'">'.htmlentities($dbVidDesc).'</div></div>';
            }
            if($dbdetails[0]['type']==5) {  
            $dbPollText         =   $dbdetails[0]['PollText'];
            $descDisplay    =   '<div class="dbTextPoll">'.htmlentities($dbPollText).'</div>';
            }
            
        
        
        if($liveScore['Score']==1){
            $scorediv   =   '<span class="scoreSprite scoreLove"></span>';
        } else if($liveScore['Score']==2){
            $scorediv   =   '<span class="scoreSprite scoreLike"></span>';
        } else if($liveScore['Score']==3){
            $scorediv   =   '<span class="scoreSprite scoreFft"></span>';
        } else if($liveScore['Score']==4){
            $scorediv   =   '<span class="scoreSprite scoreUnLike"></span>';
        } else if($liveScore['Score']==5){
            $scorediv   =   '<span class="scoreSprite scoreHate"></span>';
        }
        $propic = $this->defaultimagecheck->checkImgExist($liveScore['ProfilePic'],'userpics','default-avatar.jpg');
        $propico = $this->defaultimagecheck->checkImgExist($liveScore['opic'],'userpics','default-avatar.jpg');
        $ret .= '<li>
            <div class="listUserPhoto">
                <div class="scoredUserPic">
                    
                    <img src="'.IMGPATH.'/users/medium/'.$propic.'" width="70" height="70" border="0" />
                    <span class="arrowLinkTo"></span>
                </div>
                
         <img src="'.IMGPATH.'/users/medium/'.$propico.'" width="90" height="90" border="0" class="recievedUsePic"/>
                    <span class="arrowLinkTo"></span>
            </div>
            <div class="dataListWrapper">
                <div class="dataListbox">
                    <div class="scoredListTitle">'.$scorediv.'
                        <a href="#">'.htmlentities($this->myclientdetails->customDecoding($liveScore['Name'])).'</a> scored <a href="#">'.htmlentities($this->myclientdetails->customDecoding($liveScore['oname'])).'&rsquo;s</a>  '.$dbtype.' '. $dbtype1.'  
                    </div>
                    <div class="scoredData">'.$descDisplay.'</div>
                </div>
                <div class="scoredPostDate">'.date('d M Y',strtotime($liveScore['ScoreDate'])).'</div>
            </div>
        </li><div style="clear:both"></div>';

        $rowchange++; endforeach;

        return $ret;
    }

    public function mentionnuserresultstemplate($liveDbeeData)
    {
        $commonobj = new Admin_Model_Common();
        $deshObj   = new Admin_Model_Deshboard();
        $load = 0;
        $ret  = '';                    
        foreach($liveDbeeData as $liveScore) : 
                    
            $dbtype ='';
            $dbtype1='';
            $descDisplay    ='';
            
            if($liveScore['stype']==2)
            {
               // echo $liveScore['act_cmnt_id'].'#';
                $dbdetails  =   $deshObj->getMentioninfo($liveScore['stype'],$liveScore['act_cmnt_id']);
                $dbtype1    =   ' comment ';
                $dbdesc     =   $dbdetails[0]['Comment'];
                //print_r($dbdetails);
            }
                        
        
            if($dbdetails[0]['type']==1) { 
                //$dbtype           =   'text db<div class="icon-db-text"></div>';
                $descDisplay    =   '<div class="dbTextPoll">'.$dbdesc.'</div>';
            }
            if($dbdetails[0]['type']==2) { 
                //$dbtype           =   'link db<div class="icon-db-link"></div>';
                $descDisplay    ='';
                $dbLink         =   $dbdetails[0]['Link'];
                $dbLinkTitle    =   $dbdetails[0]['LinkTitle'];
                $dbLinkDesc     =   $dbdetails[0]['LinkDesc'];
                $dbUserLinkDesc =   !empty($dbdetails[0]['UserLinkDesc']) ? $dbdetails[0]['UserLinkDesc'] : $dbdetails[0]['LinkTitle'];

                $descDisplay    =   '<div class="dbLinkDesc">'.$dbUserLinkDesc.' - 
                    <a target="_blank" href="'.$dbLink.'">'.$dbLink.'</a>                           
                </div>';
            }
            
            if($dbdetails[0]['type']==3) { 
             //$dbtype          =   'link db<div class="icon-db-link"></div>';
             $descDisplay   ='';

            $dbPic      =   $dbdetails[0]['Pic'];
            $dbPicDesc  =   $dbdetails[0]['PicDesc'];
            
            $descDisplay    .=  '<div class="dbPicText">';
            if($dbPic!='')
            {
                $dbeepic = $this->defaultimagecheck->checkImgExist($dbPic,'userpics','default-avatar.jpg');
                $descDisplay    .=  '<div class="dbPic" ><a href="#"><img src="'.IMGPATH.'/imageposts/small/'.$dbeepic.'" width="90" border="0" /></a></div>';
            }
            else{
                $noPic = 'noPix';
            }
            
            $descDisplay    .=  '<div class="dbPicDesc '.$noPix.'">'.$dbPicDesc.'</div></div>';
            
            }
            
            if($dbdetails[0]['type']==4) { 
                //$dbtype   =   'media db<div class="icon-db-vidz"></div>';
                $descDisplay    ='';
                $dbVid          =   $dbdetails[0]['VidID'];
                $dbVidDesc      =   $dbdetails[0]['VidDesc'];
                $dbLinkDesc     =   $dbdetails[0]['LinkDesc'];
                $dbUserLinkDesc =   !empty($dbdetails[0]['UserLinkDesc']) ? $dbdetails[0]['UserLinkDesc'] : $dbdetails[0]['LinkTitle'];

                $descDisplay    .=  '<div class="dbPicText">';
                if($dbVid!='')
                {
                    $descDisplay    .=  '<div class="dbPic" ><a href="javascript:void(0)"><img width="90" height="60" border="0" src="https://i.ytimg.com/vi/'.$dbVid.'/0.jpg"></a></div>';
                }
                else{
                    $noPic = 'noPix';
                }
            $descDisplay    .=  '<div class="dbPicDesc '.$noPix.'">'.htmlentities($dbVidDesc).'</div></div>';
            }
            if($dbdetails[0]['type']==5) {  
            $dbPollText         =   $dbdetails[0]['PollText'];
            //$dbtype   =   'polls <div class="icon-db-poll"></div>';
            $descDisplay    =   '<div class="dbTextPoll">'.htmlentities($dbPollText).'</div>';
            }
            
        $Profileepic = $this->defaultimagecheck->checkImgExist($liveScore['ProfilePic'],'userpics','default-avatar.jpg');
        $opicpic = $this->defaultimagecheck->checkImgExist($liveScore['opic'],'userpics','default-avatar.jpg');
        $ret .= '<li>
            <div class="listUserPhoto">
                <div class="scoredUserPic">
                   
                    <img src="'.IMGPATH.'/users/medium/'.$Profileepic.'" width="70" height="70" border="0" />
                    <span class="arrowLinkTo"></span>
                </div>
                
                <img src="'.IMGPATH.'/users/medium/'.$opicpic.'" width="90" height="90" border="0" />
            </div>
            <div class="dataListWrapper">
                <div class="dataListbox">
                    <div class="scoredListTitle">
                        <a href="#">'.htmlentities($this->myclientdetails->customDecoding($liveScore['Name'])).'</a> mentioned <a href="#">'.htmlentities($this->myclientdetails->customDecoding($liveScore['oname'])).'&rsquo;s</a>  '.$dbtype.' '. $dbtype1.'  
                    </div>
                    <div class="scoredData">'.$descDisplay.'</div>
                </div>
                <div class="scoredPostDate">'.date('d M Y',strtotime($liveScore['act_date'])).'</div>
            </div>
        </li><div style="clear:both"></div>';

        $rowchange++; endforeach;

        return $ret;
    }


}


