<?php

class Admin_Model_Deshboard  extends Zend_Db_Table_Abstract
{
     // Table name 
     protected $_name = 'tblDbees';
     
	 protected $_dbTable;
			
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

    public function init()
    {
    	$this->myclientdetails = new Admin_Model_Clientdetails();
	}
	/* Insert special dbs */


	public function insertdata_global($table,$data){
		
		$insertdb = $this->_db->insert($table, $data);
        if($insertdb) return $this->_db->lastInsertId();
      	else return false; 
	}

	

	public function updatedata_global($table,$data,$uniqId,$id)
    {
    	$where = $this->getAdapter()->quoteInto($uniqId .'= ?', $id); //exit;
        if ($this->_db->update($table, $data ,$where))
            return true;
        else
            return false;
    }

    public function deletedata_global($table,$wherefield,$whereid) 
	{
		$where = $this->getAdapter()->quoteInto($wherefield .'= ?', $whereid); //exit;
		$del =  $this->_db->delete($table, array($where));
    	return $del;
	}
	

	public function getfieldsfromtable($fields,$table,$wherefield,$whereid)
    {

        $Qry = $this->_db->select()->from($table,$fields)->where('clientID = ?', clientID)->where($wherefield.'= "'.$whereid.'"');   
        $rs = $this->_db->fetchAll($Qry);
        return $rs;
    }

    public function getfieldsfromtableusingin($fields,$table,$wherefield,$whereid)
    {
    	//echo $wherefield.'~~~'.$whereid;die;
       $Qry = $this->_db->select()->from($table,$fields)->where('clientID = ?', clientID)->where($wherefield .' IN( '. $whereid  .')' ); 
        $rs = $this->_db->fetchAll($Qry);
        return $rs;
    }

    

    public function getfieldsfromgrouptable($fields,$table,$wherefield,$whereid,$groupby='',$orderfield='',$resultorder='DESC',$wherefield2='',$whereid2='')
    {
        $db = $this->getDbTable();
		$select = $db->select()->setIntegrityCheck( false );
		$select->from(array($table),$fields)->where($wherefield.'= "'.$whereid.'"');
		$select->where($table.'.clientID = ?', clientID);
		if($wherefield2!='') $select->where($wherefield2.'= "'.$whereid2.'"');					
		if($groupby!='') $select->group("$table.$groupby");	
		if($orderfield!='') $select->order("($orderfield) $resultorder");

		//echo '<br><br>'. $select; 
		return $this->getDefaultAdapter()->query($select)->fetchAll();	
    }
   

    public function getfieldsfromjointable($fields,$table,$wherefield,$whereid,$fields2,$table2,$joinfromfield,$jointofield,$groupby='',$orderfield='',$resultorder='DESC',$wherefield2='',$whereid2='',$wherefield3='',$whereid3='')
    {
        $db = $this->getDbTable();
		$select = $db->select()->setIntegrityCheck( false );
		$select->from(array($table),$fields)
					->join(array($table2), "$table.$joinfromfield=$table2.$jointofield",$fields2)
							->where($wherefield.'= "'.$whereid.'"');
							$select->where($table.'.clientID = ?', clientID);
		if($wherefield2!='') $select->where($wherefield2.'= "'.$whereid2.'"');	
		if($wherefield3!='') $select->where($wherefield3.'= "'.$whereid3.'"');					
		if($groupby!='') $select->group("$table.$groupby");	
		if($orderfield!='') $select->order("($orderfield) $resultorder");
		return $this->getDefaultAdapter()->query($select)->fetchAll();	
    }

    public function searchinmltarray($needle, $haystack, $strict = false) 
    {
	    foreach ($haystack as $item) {
	        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && $this->searchinmltarray($needle, $item, $strict))) {
	            return $item;
	        }
	    }

	    return false;
	}

	 public function totalsentiments($dbeerid, $calle) //case id = 4707680724
    {
        $select = $this->_db->select();
        if ($calle == 'user') {
            $select->from(array(
                'cmnt' => 'tblDbeeComments'
            ), array(
                'tot' => new Zend_Db_Expr('count(cmnt.sentiment_polarity)')
            ))->where("cmnt.sentiment_polarity != ?", '')->where("cmnt.DbeeID = ?", $dbeerid)->where("cmnt.clientID = ?", clientID);
        } else {
            $select->from(array(
                'cmnt' => 'tblDbeeComments'
            ), array(
                'sentiment_polarity',
                'tot' => new Zend_Db_Expr('count(cmnt.sentiment_polarity)')
            ))->where("cmnt.sentiment_polarity != ?", '')->where("cmnt.DbeeID = ?", $dbeerid)->where("cmnt.clientID = ?", clientID)->group("cmnt.sentiment_polarity")->order('tot DESC');//->limit(1);
        }
        $result = $this->_db->fetchAll($select);
        return $result;
    }
    public function sentimentscomment($dbeerid)
    {
        $select = $this->_db->select()->from(array(
            'c' => 'tblDbeeComments'
        ))->where("c.DbeeID = ?", $dbeerid)->where("c.sentiment_polarity = ?", '')->where("c.clientID = ?", clientID)->order('c.CommentDate asc')->limit(50);
        $result = $this->_db->fetchAll($select);
        return $result;
    }

	public function commentsdisplayed($cmntId)
	{
		$defaultimagecheck 		= new Admin_Model_Common(); 
		$latestComment = $this->getfieldsfromtable(array('*'),'tblDbeeComments','CommentID',$cmntId);

		
		$viewcmm = '<a href="'.BASE_URL.'/dbeedetail/home/id/'.$latestComment[0]['DbeeID'].'"> - view </a>';	
		$dbtype	='';	
		$descDisplay	='';
		if(($latestComment[0]['Type'])==1) { 
			$dbtype			=	'text db<div class="icon-db-text"></div>';
			$descDisplay	=	'<div  class="dbTextPoll">'.$latestComment[0]['Comment'].'</div>';//substr($latestComment->Comment,0,100).'</div>';
		}
		if(($latestComment[0]['Type'])==2) { 
			$dbtype			=	'link db<div class="icon-db-link"></div>';
			$dbLink			=	$latestComment[0]['Link'];
			$dbLinkTitle	=	$latestComment[0]['LinkTitle'];
			$dbLinkDesc		=	$latestComment[0]['LinkDesc'];
			$dbUserLinkDesc	=	!empty($latestComment[0]['UserLinkDesc']) ? $latestComment[0]['UserLinkDesc'] : $latestComment[0]['LinkTitle'];

			$descDisplay	=	'<div style="padding:5px; margin-top:5px; margin-bottom:5px; background-color:#DAD9D9;">
			<div class="font12">'.$dbUserLinkDesc.' - 
			  <a target="_blank" href="'.$dbLink.'">'.$dbLink.'</a>
			</div>
			<div class="font12" style="margin-top:10px;"></div></div>';
			}
			if(($latestComment[0]['Type'])==3) { 
			 $dbtype			=	'link db<div class="icon-db-link"></div>';

			$dbPic		=	$latestComment[0]['Pic'];
			$dbPicDesc	=	$latestComment[0]['PicDesc'];

			$descDisplay	.=	'<div class="dbPicText">';
			$checkdbpic = $defaultimagecheck->checkImgExist($dbPic,'imageposts','default-avatar.jpg');
			if($dbPic!='')
			{
				$descDisplay	.=	'<div class="dbPic" ><a href="#"><img src="'.IMGPATH.'/imageposts/small/'.$checkdbpic.'" width="90" border="0" /></a></div>';
			}
			
			$descDisplay	.=	'<div class="dbPicDesc">'.$dbPicDesc.'</div></div>';
		}
		if(($latestComment[0]['Type'])==4) { 
			$dbtype	=	'media db<div class="icon-db-vidz"></div>';

			$dbtype			=	'link db<div class="icon-db-link"></div>';
			$dbVid			=	$latestComment[0]['VidID'];
			$dbVidDesc		=	$latestComment[0]['VidDesc'];
			$dbLinkDesc		=	$latestComment[0]['LinkDesc'];
			$dbUserLinkDesc	=	!empty($latestComment[0]['UserLinkDesc']) ? $latestComment[0]['UserLinkDesc'] : $latestComment[0]['LinkTitle'];

			$descDisplay	.=	'<div class="dbPicText">';
			if($dbVid!='')
			{ 
				$descDisplay	.=	'<div class="dbPic" ><a href="javascript:void(0)"><img width="90" height="60" border="0" src="https://i.ytimg.com/vi/'.$dbVid.'/0.jpg"></a></div>';
			}
			
			$descDisplay	.=	'<div class="dbPicDesc">'.$dbVidDesc.'</div></div>';
		}
		if(($latestComment[0]['Type'])==5) {  
		$dbPollText			=	$latestComment[0]['PollText'];
		$dbtype	=	'polls <div class="icon-db-poll"></div>';
		$descDisplay	=	'<div  class="dbTextPoll">'.$dbPollText.'</div>';
		}
		if($descDisplay=='') $descDisplay = '<div  class="dbTextPoll">comment feed missing</div>';
		return $descDisplay;//.$viewcmm;
	}

	public function displayscorecomments($cmntLvArr,$statictext,$spirit='')
	{
		$cmntloves = '';

		if(count($cmntLvArr)>0)
		{
			$cmntloves .= '<div class="commentexplore">';
			foreach ($cmntLvArr as $key => $value) 
			{

				$scorediv=$this->myclientdetails->ShowScoreIcon($spirit);
				//echo $value['ID'].' # '; 
				$txtmsg = '';
				$cmntdtils = $this->commentsdisplayed($value['ID']);
				
				if( $statictext=='FFT') $txtmsg = 'Comment with most food for thought';
				else $txtmsg = 'Most '.$statictext.' comment';
				
				if($key == 0)
				{
					$cmntloves .=  '<div class="explorecomments" >';
					$cmntloves .=  '<div class="scoreinlie2" > <span class="">'.$scorediv.'</span>  '.$txtmsg.'  </div>';

					$cmntloves .= '<img class="smallProfilePic" src="'.IMGPATH.'/users/small/'.$value['ProfilePic'].'" width="40" height="40">
					<div class="dbDetails"> <div class="mainPostValue"><strong><a href="javascript:void(0)" userid="'.$value['UserID'].'" class="show_details_user ">'.$this->myclientdetails->customDecoding($value['Name']).' '.$this->myclientdetails->customDecoding($value['lname']).'</a></div>'.$cmntdtils.'</div></div>';
					//$cmntloves .=  $cmntdtils;
				}
				else{	
				$cmntloveshidd .=  '<li>';
					$cmntloveshidd .= '<img class="smallProfilePic" src="'.IMGPATH.'/users/small/'.$value['ProfilePic'].'" width="40" height="40"><div class="dbDetails"><strong>
					
					'.$this->myclientdetails->customDecoding($value['Name']).' '.$this->myclientdetails->customDecoding($value['lname']).' - '.$value['commenttot'].' </strong> comment &nbsp;&nbsp;</span>';
					$cmntloveshidd .=  $cmntdtils;
					$cmntloveshidd .=  '</div></li>';
				}
			}
			if($key > 0)
			{
				$cmntloves .='<a herf="#" class="showlovecomments" style="display:none"><i class="fa fa-plus"></i></a> <div class="clearfix"> </div>';
				$cmntloves .= '<div class="hideRowsDetails"><ul>'.$cmntloveshidd.'</ul></div>';
			}
			$cmntloves .= '<div class="clearfix"></div></div>';
		}
		else
		{
			//$cmntloves .= '<div class="commentexplore"><div class="explorecomments" ><span class="notfound">  Post not awarded <strong>'.$statictext.' </strong> by any one</span></div></div>';
		}
		echo $cmntloves;
	}

	public function displaygroupscore($scorArr,$statictext,$savegroupcontent='')
	{
		$lovescored = '';

		if(count($scorArr)>1)
		{
			$scorediv=$this->myclientdetails->ShowScoreIcon($statictext);
			$lovescored .= '<li class="listScoreType dataWrpCheckbox">';
			$lovescored .= '<h3> <span class="">'.$scorediv.'</span></h3>';
			foreach ($scorArr as $key => $value) {
				$mnext = '';
				if($value['scoretot']>1) $mnext = ' was scored '.$value['scoretot'].' times';
				else $mnext = ' was scored once';
				$lovescored .=  '<a href="javascript:void(0);" rel="dbTip" title="'.$this->myclientdetails->customDecoding($value['Name']).' '.$this->myclientdetails->customDecoding($value['lname']). $mnext.'"><img src="'.IMGPATH.'/users/small/'.$value['ProfilePic'].'" width="40" height="40"></a><input type="checkbox" name="reportusers" value="'.$value['UserID'].'" checked="checked" class="groupcheck">';
			}
			if(count($scorArr)>0) $savetogroup = $savegroupcontent; else $savetogroup='';
			$lovescored .= $savetogroup.'</li>';
		}
		else
		{
			//$lovescored .= '<li class="listScoreType dataWrpCheckbox"><span class="notfound">  Post not awarded by <strong>'.$statictext.'</strong></span></li>';
		}
		
						
		echo $lovescored;
	}


	public function postdbform($eventid="",$uid)
	{

		
		$this->adminUserID = $this->adminUserID;
		$orderbyArr = array('Priority'=>'DESC');
		$orderbyGrpArr = array('ID'=>'DESC');
		$orderbyLegpArr = array('LID'=>'DESC');
		$orderbyEventpArr = array('id'=>'DESC');

		$this->category      = $this->myclientdetails->getAllMasterfromtable('tblDbeeCats',array('CatID','CatName','Priority'),'',$orderbyArr);

		$this->liveGroupData = $this->myclientdetails->getAllMasterfromtable('tblGroups',array('DISTINCT (GroupName) as groupname','ID','GroupType'),array('User'=>$uid),$orderbyGrpArr);
		
		$this->liveLegData   = $this->myclientdetails->getAllMasterfromtable('tblUserLeague',array('DISTINCT (Title) as title','LID','EndDate'),array('UserID'=>$uid),$orderbyLegpArr);
		$this->eventlist     = $this->eventlist($eventid);
		$usersetarray 	     = $this->myclientdetails->getAllMasterfromtable('usersgroup',array('ugid','ugname'),'',array('ugname'=>'ASC'));

		$layout='';
		
		$layout.='<div id="searchContainer" style="display:block;">
	      <div id="exp_condition" class="expenddcondition1" >
	         <div class="whiteBox addConditionWrapper">
	            <div class="row postBntsRow">
	               <a href="javascript:void(0);" rel="dbpolls" data-type="" class="pull-right pollBtnForPopup"><i class="fa fa-signal"></i></a>
	               <a href="javascript:void(0);" rel="dbTip" title="Insert picture in Post" class="picUplaoderPopup pull-right">
	                  <i class="fa fa-camera"></i>
	                  <div id="uploadDropzoneImg" class="dropzone">
	                     <div class="fallback">
	                        <input type="file" name="file"  />
	                     </div>
	                  </div>
	               </a>
	               <a class="addonpost pull-right" data-type="postvideo" rel="dbTip" href="javascript:void(0);" title="Post video"><i class="fa fa-youtube-play"></i></a>
	               <a  href="javascript:void(0);" rel="dbTip" title="Post in a group" data-type="postingrouup" class="addonpost pull-right "><i class="fa fa-users"></i></a>';
	          if(clientID!=19){
	         $layout.='<a  href="javascript:void(0);" rel="dbTip" title="Post in a event" data-type="postinevent" class="addonpost pull-right "><i class="fa fa-calendar"></i></a>';
	        }
	        $layout.='<a href="javascript:void(0)" class="backbutPop active pollBtnForPopup pull-right" style="display:none; text-decoration:none;"><i class="fa fa-arrow-circle-left "></i> Back</a>
	            </div>
	            <form name="postForm" id="postForm" >
	               <input name="pic" id="PostPix_" type="hidden">
	               <input id="dbeetype" name="dbeetype" value="Text" type="hidden">
	               <input id="InsertInLeg" name="InsertInLeg" type="hidden">
	               <input id="InsertInGrp" name="InsertInGrp" type="hidden">
	               <input id="tagitent" name="tagitent" type="hidden" value="">
	               <div class="searchField" id="addTextWrp">
	                  <label class="label">Comment</label>
	                  <div class="fieldInput">
	                     <textarea  name="text" id="PostText" style="height:145px;" class="mention"></textarea>
	                     <div id="DataInPoll" style="display:none;" class="clearfix">
	                        <div class="txtContainer pollsfields">Change poll options below or leave to use default</div>
	                        <div class="pollsfields clearfix">                 
	                           <input id="poll-option-1" name="polloption1" value="Yes" type="text">
	                           <input value="No" id="poll-option-2" name="polloption2" type="text">
	                           <input placeholder="Option 1" id="poll-option-3" name="polloption3" type="text">                                    
	                           <input id="poll-option-4" name="polloption4" placeholder="Option 2" type="text">                                            
	                        </div>
	                     </div>
	                  </div>
	               </div>
	               <span id="DataNotInPoll">
	               	 <div class="uploadVideo uploadVideoTemp"></div>';
	               	  
	               	if(clientID==19)
	               	{
	               	  $layout .='<div class="searchField">
	                     <label class="label">Post tag</label>
	                     <div class="fieldInput">
	                        <input id="post-tag-text" name="posttag" type="text">
	                        <i class="postpopupIcon pstTwrIcon"></i>
	                     </div>
	                  </div>';
	              	}
	                $layout .='<div class="searchField">
	                     <label class="label">URL</label>
	                     <div class="fieldInput">
	                        <div class="twitterHas"><input id="PostLink" type="text"><i class="postpopupIcon pstLinkIcon"></i>												</div>
	                     </div>
	                  </div>
	                  <div class="searchField">
	                     <label class="label">#tag post </label>
	                     <div class="fieldInput">
	                        <ul id="DbTag"></ul>
	                     </div>
	                  </div>
	                  <div class="searchField">
	                     <label class="label">Twitter #tag</label>
	                     <div class="fieldInput">
	                        <input id="twitter-tag-text" name="twittertag" type="hidden">
	                        <i class="postpopupIcon pstTwrIcon"></i>
	                     </div>
	                  </div>
	                  <div class="searchField">
	                     <label class="label">Post category</label>
	                     <div class="fieldInput">
	                        <div class="categoryList">';
	                           if(sizeof($this->category) >0) { 
	                            foreach ($this->category as $cat) { 
	                           $layout.='<label class="labelCheckbox" for="cat'.$cat['CatID'].'">
	                           <input name="cat[]" value="'.$cat['CatID'].'" id="cat'.$cat['CatID'].'" cat-name="'.$cat['CatName'].'" type="checkbox" checked="checked">
	                           <label class="checkbox" for="cat'.$cat['CatID'].'"> </label>'.$cat['CatName'].'
	                           </label>';
	                            } 	
	                          } else { 
	                           $layout.='<span> There is no category yet!</span>';
	                            } 		
	                         $layout.='</div>
	                     </div>
	                  </div>
	               </span>
	               <div class="searchField" id="leagfield" style="display:none;">
	                  <label class="label">select league </label>
	                  <div class="fieldInput">';
	                      if(sizeof($this->liveLegData) >0) { 

	                      $layout.='<select name="leagueId" id="leagueId" class="selectDrp">';
	                         foreach($this->liveLegData as $legdata){
	                        $layout.='<option value="'.$legdata['LID'].'###'.$legdata['EndDate'].'">'.$legdata['title'].'</option>';
	                         } 
	                     $layout.='</select>';
	                     } else { 
	                     $layout.='<span> There is no league yet!</span>';
	                      } 
	                     $layout.='<input lgenddate="2014-08-31 13:51:00" type="hidden">
	                     <a href="javascript:void(0);" class="fa-stack closeRowField" id="closeleg">        
	                     <i class="fa fa-circle fa-stack-2x"></i>                                            
	                     <i class="fa fa-times fa-stack-1x fa-inverse"></i>
	                     </a>
	                  </div>
	               </div>
	               <div class="searchField" id="eventfield" style="display:none;">
	                  <label class="label">Select Event </label>
	                  <div class="fieldInput">';
	                      if(count($this->eventlist) >0) { 
	                     $layout.='<select name="selectEventList" id="selectEventList" class="selectEvent">';

	                     	if($eventid==""){
	                          $layout.='<option value="0">Select Event</option>';
	                        }
	                         foreach($this->eventlist as $event){

	                        $selected='';
	                        if($event['id']==$eventid)
	                        {
	                        	 $selected='selected="selected"';
	                        }

	                        $layout.='<option value="'.$event['id'].'">'.$event['title'].'</option>';
	                         } 
	                     $layout.='</select>';
	                      } else { 
	                     $layout.='<span> No event found</span>';
	                      } 
	                     $layout.='<a href="javascript:void(0);" class="fa-stack closeRowField" id="closeevent">      
	                     <i class="fa fa-circle fa-stack-2x"></i>                                              
	                     <i class="fa fa-times fa-stack-1x fa-inverse"></i>    
	                     </a>
	                  </div>
	               </div>
	               <div class="searchField" id="groupfield" style="display:none;">
	                  <label class="label">Select Group </label>
	                  <div class="fieldInput">';
	                      if(sizeof($this->liveGroupData) >0) { 
	                     $layout.='<select name="selectGroupList" id="selectGroupList" class="selectDrp">';
	                         foreach($this->liveGroupData as $gdata){
	                        $layout.='<option value="'.$gdata['ID'].'" groupname="'.$gdata['groupname'].'" grouptype="'.$gdata['GroupType'].'">'.$gdata['groupname'].'</option>';
	                         } 
	                     $layout.='</select>
	                     <input type="hidden" name="groupty" id="groupty"/>';
	                      } else { 
	                     $layout.='<span> There is no group yet!</span>';
	                      } 
	                     $layout.='<a href="javascript:void(0);" class="fa-stack closeRowField" id="closegrp">      
	                     <i class="fa fa-circle fa-stack-2x"></i>                                              
	                     <i class="fa fa-times fa-stack-1x fa-inverse"></i>    
	                     </a>
	                  </div>
	               </div>
	               <div class="searchField">
	                  <label class="label">Document upload</label>
	                  <div class="fieldInput" style="padding-top:6px">
	                     <a href="javascript:void(0)" class="Attachfile">Attach file <span></span> </a> 
	                     <div id="allAttachedFiles" style="padding:10px;">
	                     </div>
	                  </div>
	               </div>';

	               if(count($usersetarray)>0)
				   {
						$layout.='<div class="searchField" id="postinuserset">
						<label class="label">Select a user set </label>
						<div class="fieldInput">
							<select name="userset" id="userset" class="selectDrp">';
							$layout .='<option value="">Select a user set</option>';
							foreach($usersetarray as $key =>$value):
							$layout .='<option value="'.$value['ugid'].'">'.$value['ugname'].'</option>';
							endforeach;
							$layout .='</select>						
							</div>
						</div>';
					} 
					else
					{
						$layout.='';
					}

	               $layout.='<div class="searchField schPostRow">
	                  <label class="label">Schedule post</label>
	                  <div class="fieldInput" style="padding-top:6px">
	                  		<label class="labelCheckbox">
	                           <input type="checkbox"  id="postlater" value="on" name="postlater">
	                           <label for="postlater" class="checkbox"></label>Yes
	                        </label>
	                  </div>
	               </div>
	               <div class="searchField postLaterclass " style="display:none">
	                  <label class="label">Select post time</label>
	                  <div class="fieldInput" style="padding-top:6px">
                           <div class="pull-left" style="display:none">
	                  		<select name="posttimings" id="posttimings" >
	                  			<option value="0"> Select a time </option>
	                  			<option value="1"> An hour from now </option>
	                  			<option value="2"> 2 hours from now  </option>
	                  			<option value="3"> 3 hours from now  </option>
	                  			<option value="99" selected> Custom  </option>
	                  		</select>
                           </div> 
	                  		<div class="postlaterCal pull-left" >
	                  			<input type="text" name="scheduledate" id="scheduledate" class="uploadType">
	                  		</div>
	                  </div>
	               </div>
	               <div class="searchField buttonsPostRow" style="margin-top:10px;">
	                  <label class="label">&nbsp;</label>
	                  <div class="fieldInput">
	                     <input id="editLivePost" name="editLivePost" type="hidden" value="0">
	                     <input id="DbeeID" name="DbeeID" type="hidden" value=""><select style="width:100px;" id="PrivatePost" name="PrivatePost"  class="pull-left selectDrp PrivatePost" tabindex="-1" ><option value="0">Public</option><option value="1">Private</option></select>
	                    
	                     <a href="javascript:void(0);"  class="LivePostSubmit addbutton btn btn-green btn-medium pull-left eventSubmitBtn" >Post</a>
	                     <a  href="javascript:void(0);" id="" class="btn btn-medium openSearchBlock resetform"  style=""> Close</a>
	                     <div class="helponoff  pull-right" >
	                        <input type="checkbox"  id="postPublishAction" name="postPublishAction" value="0">
	                        <label for="postPublishAction" style="width:150px">
	                           <div off="Inactive" on="Active" class="onHelp"></div>
	                           <div class="onHelptext">
	                              <span>Activate</span>
	                              <span>Deactivate</span>
	                           </div>
	                        </label>
	                     </div>
	                  </div>
	               </div>
	               <div class="clearfix"></div>
	         	</form>
	         </div>
	         <div class="clearfix"></div>
	         
	      </div>
	      <div class="clearfix"></div>
	   </div>';

	   return $layout;
	}


	public function whosharedon($shareArr,$statictext , $icons,$savegroupcontent='')
	{
		$sharefb = '';
		
		
		$sharefb .= '<div class="psReprtSocial dataWrpCheckbox" data-type="'.$statictext.'">';
		$sharefb .= '<h3><span class="socialSpecialIcons  '.$icons.' "> </span>Users who shared on '.$statictext.' </h3>';
		if(count($shareArr)>0)
		{
			foreach ($shareArr as $key => $value) {
				$sharefb .=  '<a href="javascript:void(0);" rel="dbTip" title="'.$this->myclientdetails->customDecoding($value['Name']).' '.$this->myclientdetails->customDecoding($value['lname']).' shared post on '.$statictext.' '.$value['sharetot'].' times"><img class="imgStyle" src="'.IMGPATH.'/users/small/'.$value['ProfilePic'].'" width="40" height="40"></a><input type="checkbox" name="reportusers" value="'.$value['UserID'].'" checked="checked" class="groupcheck">';
			}
		}
		else
		{
			$sharefb .= '<span class="notfound">No user shared this post on <strong>'.$statictext.'</strong></span>';
		}
		if(count($shareArr)>0) $savetogroup = $savegroupcontent; else $savetogroup='';
		echo $sharefb .= $savetogroup.'</div>';
	}
	public function getDbeeDetails($dbee)
    {
        $select = $this->_db->select()->from(array(
            'c' => $this->_name
        ))->where("c.DbeeID = ?",$dbee)->where("c.clientID =?",clientID);
        return $this->_db->fetchRow($select);
    }

    /*public function getPollOption($dbeeid)
    { 

    	die('dfdfddf');
     echo $sql="SELECT * FROM `tblPollOptions` WHERE (clientID= '".clientID."' AND PollID= '".$dbeeid."')";
      die;
      return $this->getDefaultAdapter()->query($sql)->fetchAll();
    }*/

	public function SearchDbees($query)  
	{
		$db = $this->getDbTable();			
		return $this->getDefaultAdapter()->query($query)->fetchAll();
		exit;
		
	}
	public function getDbee($query,$limit) 
    {
    	$db = $this->getDbTable();
        $select = $db->select();
        $select->setIntegrityCheck( false );
        $select->from( array('DB' => 'tblDbees'),array('id'=>'DB.DbeeID',
        	'surveyTitle'=>'DB.surveyTitle','Text'=>'DB.Text','LinkTitle'=>'DB.LinkTitle','VidDesc'=>'DB.VidDesc','PicDesc'=>'DB.PicDesc','PollText'=>'DB.PollText','Type'=>'DB.Type'));
        $select->where('(Active =?',1)->where('Text LIKE ?)','%'.$query.'%')
        		->orwhere('(surveyTitle LIKE ?','%'.$query.'%')
        		->orwhere('LinkTitle LIKE ?','%'.$query.'%')
        		->orwhere('VidDesc LIKE ?','%'.$query.'%')
        		->orwhere('PicDesc LIKE ?','%'.$query.'%')
        		->orwhere('PollText LIKE ?)','%'.$query.'%')->where('DB.clientID = ?', clientID);
        $select->limit($limit,0);
       
        return $db->fetchAll($select);
    }
	public function specialdbs($sft,$limit=10)
	{
		$db = $this->getDbTable();
		$sft =(int)$sft;
		$select = $db->select()->setIntegrityCheck( false );
		$select->distinct('c.DbeeID');
		$select->from(array('c'=>'tblDbees',array('DbeeID','Name','lname','UserID','ProfilePic','Type','Vid','VidDesc','VidSite','VidID','eventtype','eventstart','eventzone','PostDate','expertuser')))
					->joInInner(array('u'=>'tblUsers'),'u.UserID=c.User')
							->where('c.Type= ?','6')->where('c.clientID= ?',clientID)
								->order('c.PostDate  DESC')
									->limit($limit,$sft);			
		return $this->getDefaultAdapter()->query($select)->fetchAll();		
	}
	public function checkinviteexpert($dbeeid)
    {
        $select = $this->_db->select()->from('tblinvitexport')
                ->where("dbeeid = ?", $dbeeid)
                ->where("clientID = ?", clientID);
        return $this->_db->fetchRow($select);
    }
     public function deleteExpertInvitation($dbid)
    {
        if ($this->_db->delete('tblinvitexport', array(
            "dbeeid='" . $dbid . "'","clientID='" . clientID . "'"
        )))
            return true;
        else
            return false;
    }
    public function checkdbeeinviteexpert($dbeeid)
    {
        $select = $this->_db->select()->from('tblactivity')
                    ->where("act_typeId = ?", $dbeeid)
                    ->where("act_type = ?", 39)
                    ->where("clientID = ?", clientID)
                    ->where("act_message = ?", 44);
        return $this->_db->fetchRow($select);
    }
	public function specialdbstotal($Offset,$limit=10)
	{
		$db = $this->getDbTable();
		$Offset1 =(int)$Offset;
		$select = $db->select()->setIntegrityCheck( false );
		$select->distinct('c.DbeeID');
		$select->from(array('c'=>'tblDbees',array('DbeeID','Name','lname','UserID','ProfilePic','Type','Vid','VidDesc','VidSite','VidID','eventtype','eventstart','eventzone','PostDate')))
		->joInInner(array('u'=>'tblUsers'),'u.UserID=c.User')
		->where('c.Type= ?','6')->where('c.clientID= ?',clientID)
		->order('c.PostDate  DESC');
		return $this->getDefaultAdapter()->query($select)->fetchAll();
	}
	public function surveydbs($sft,$limit=10)
	{
		$db = $this->getDbTable();
		$sft =(int)$sft;
		$select = $db->select()->setIntegrityCheck( false );
		$select->distinct('c.DbeeID');
		$select->from(array('c'=>'tblDbees',array('surveyPdf','surveyTitle','DbeeID','Name','lname','UserID','ProfilePic','Type','PostDate')))
					->joInInner(array('u'=>'tblUsers'),'u.UserID=c.User')
							->where('c.Type= ?','7')->where('c.clientID= ?',clientID)
								->order('c.PostDate  DESC')
									->limit($limit,$sft);			
		return $this->getDefaultAdapter()->query($select)->fetchAll();		
	}
	public function surveydbstotal()
	{
		$db = $this->getDbTable();
		$select = $db->select()->setIntegrityCheck( false );
		$select->distinct('c.DbeeID');
		$select->from(array('c'=>'tblDbees',array('DbeeID','Name','lname','UserID','ProfilePic','Type')))
		->joInInner(array('u'=>'tblUsers'),'u.UserID=c.User')
		->where('c.Type= ?','7')->where('c.clientID= ?',clientID)
		->order('c.PostDate  DESC');
		return $this->getDefaultAdapter()->query($select)->fetchAll();
	}
	public function answerSimilaruserlist($id,$ownID)
	{
		$db = $this->getDbTable();
		$select = $db->select()->setIntegrityCheck( false );
		$select->from(array('c'=>'tblSurveyAnswer'))
		->joInInner(array('u'=>'tblUsers'),'u.UserID=c.UserID')
		->where('c.answer_id= ?',$id)->where('c.clientID= ?',clientID)
		->where('u.UserID!= ?',$ownID);
		return $this->getDefaultAdapter()->query($select)->fetchAll();
	}

	public function surveydetails($dbeeid)
	{
		$db = $this->getDbTable();
		$select = $db->select()->setIntegrityCheck(false);
		$select->distinct('c.DbeeID');
		$select->from(array('c'=>'tblDbees',
			array('DbeeID','Name','lname','UserID','ProfilePic','Type')))
		->where('c.DbeeID= ?',$dbeeid)->where('c.clientID= ?',clientID);
		return $this->getDefaultAdapter()->query($select)->fetchAll();
	}


	public function surveyCheckCorrectAns($dbeeid)
	{
		return $this->getDefaultAdapter()->query("SELECT COUNT(*) as totalCorrectAns  FROM  `tblSurveyquestion` AS `s` WHERE (s.clientID= '".clientID."' AND s.dbeeid= '".$dbeeid."' AND s.correct_answer='1')")->fetchAll();
	}

	

	public function surveyuserdetails($dbeeid)
	{
		return $this->getDefaultAdapter()->query("SELECT DISTINCT s.UserID, `c`.Name,`c`.lname,`c`.ProfilePic FROM `tblUsers` AS `c` INNER JOIN `tblSurveyAnswer` AS `s` ON s.UserID=c.UserID WHERE (s.dbeeid= '".$dbeeid."' AND s.clientID= '".clientID."')")->fetchAll();
	}

	public function surveyAllCorrect($dbeeid)
	{
		return $this->getDefaultAdapter()->query("SELECT DISTINCT s.UserID, `c`.Name,`c`.lname,`c`.ProfilePic,(select count(*) from tblSurveyquestion where parentID=0 AND Dbeeid='".$dbeeid."' AND clientID= '".clientID."') as totans FROM `tblUsers` AS `c` INNER JOIN `tblSurveyAnswer` AS `s` ON s.UserID=c.UserID  WHERE (s.dbeeid= '".$dbeeid."' AND s.clientID= '".clientID."' AND s.IsAnswerCorrect=1)  GROUP BY s.UserID HAVING totans=sum(s.IsAnswerCorrect)")->fetchAll();
	}

	public function surveyAtLeastOneCorrect($dbeeid)
	{
		return $this->getDefaultAdapter()->query("SELECT DISTINCT s.UserID, `c`.Name,`c`.lname,`c`.ProfilePic FROM `tblUsers` AS `c` INNER JOIN `tblSurveyAnswer` AS `s` ON s.UserID=c.UserID WHERE (s.dbeeid= '".$dbeeid."' AND s.clientID= '".clientID."' AND s.IsAnswerCorrect=1)")->fetchAll();
	}

		
	
	public function surveyquestion($id)
	{
		$db = $this->getDbTable();
		$Offset1 =(int)$Offset;
		$select = $db->select()->setIntegrityCheck( false );
		$select->distinct('c.DbeeID');
		$select->from(array('A'=>'tblSurveyquestion'))
		->where('A.parentID= ?',$id)->where('A.clientID= ?',clientID);
		$select->order('id ASC');
		return $this->getDefaultAdapter()->query($select)->fetchAll();
	}



	public function checkUserCompleteSurvey($id)
	{
		$db = $this->getDbTable();
		$Offset1 =(int)$Offset;
		$select = $db->select()->setIntegrityCheck( false );
		$select->from(array('A'=>'tblSurveyAnswer'))
		->where('A.dbeeid= ?',$id)->where('A.clientID= ?',clientID);
		return count($this->getDefaultAdapter()->query($select)->fetchAll());
	}
	
	public function checkSurveyAnswers($dbeeid,$userid)
	{
		return $this->getDefaultAdapter()->query("SELECT answer_id FROM tblSurveyAnswer WHERE  clientID='".clientID."' AND dbeeid='".$dbeeid."' AND UserID='".$userid."'")->fetchAll();
	}

	public function checkSurveySimilarAnswers($answer_id,$userid)
	{
		return $this->getDefaultAdapter()->query("SELECT answer_id FROM tblSurveyAnswer WHERE clientID='".clientID."' AND answer_id='".$answer_id."' AND UserID!='".$userid."'")->fetchAll();
	}

	public function surveyquestionUpdate($id,$content,$CorrectAnswer,$parrent)
	{
		if($CorrectAnswer==1 && $parrent!="")
		{
			$data1 = array('correct_answer' => 0);
	        $this->_db->update('tblSurveyquestion', $data1 ,array(
	            "parentID='" . $parrent . "'","clientID='" . clientID. "'"
	        ));
        }
        if($content!="")
        {
		  $data = array('content' => $content,'correct_answer' => $CorrectAnswer);
		}
		else
		{
		  $data = array('correct_answer' => $CorrectAnswer);	
		}

        $this->_db->update('tblSurveyquestion', $data ,array(
            "id='" . $id . "'","clientID='" . clientID. "'"
        ));
	}
	public function survedelete($id)
    {
    	$this->_db->delete('tblDbees', array(
    			"DbeeID='" . $id . "'","clientID='" . clientID. "'"
    	));
    	return true;
    }

    public function surveyAnswerbydbeeiddelete($id)
    {
    	$this->_db->delete('tblSurveyquestion', array(
    			"DbeeID='" . $id . "'","clientID='" . clientID. "'"
    	));
    	return true;
    }
	public function surveyquestionbydbeeiddelete($id)
    {
    	$this->_db->delete('tblSurveyAnswer', array(
    			"DbeeID='" . $id . "'","clientID='" . clientID. "'"
    	));
    	return true;
    }
    public function surveyQuestiondelete($id)
    {
    	$this->_db->delete('tblSurveyquestion', array(
    			"id='" . $id . "'","clientID='" . clientID. "'"
    	));
    	$this->_db->delete('tblSurveyquestion', array(
    			"parentID='" . $id . "'","clientID='" . clientID. "'"
    	));
    	return true;
    }
	public function GroupChartData($datefrom='', $dateto='') 
	{
		$db = $this->getDbTable();		
		$getchart 	= 	$db->select();
		$getchart->setIntegrityCheck( false );
		$getchart->from(array('group' => 'tblGroups'), array('tot' =>  new Zend_Db_Expr('count(group.GroupPrivacy 	)')));	
		$getchart->where("group.clientID = ?", clientID);
		if(!empty($datefrom) )
		    $getchart->where("DATE_FORMAT(GroupDate, '%Y-%m-%d') <= ?",  date('Y-m-d',strtotime($datefrom)));
		if(!empty($dateto))   
			$getchart->where("DATE_FORMAT(GroupDate, '%Y-%m-%d') >= ?",  date('Y-m-d',strtotime($dateto)));
		 
		$getchart->group ( array ("group.GroupPrivacy") );	
		$getchart	= $db->fetchAll($getchart)->toarray();		
		return $retarray	=	$getchart[0]['tot'].'='.$getchart[1]['tot'].'='.$getchart[2]['tot'];		
		exit;
		
	}
	
	
	public function ScoringChartData($datefrom='', $dateto='') 
	{
		$db = $this->getDbTable();
		
		$getchart 	= 	$db->select();
		$getchart->setIntegrityCheck( false );
		$getchart->from(array('score' => 'tblScoring'), array('tot' =>  new Zend_Db_Expr('count(score.Score)')));	
		$getchart->where("score.clientID = ?", clientID);
		if(!empty($datefrom) )
		    $getchart->where("DATE_FORMAT(ScoreDate, '%Y-%m-%d') <= ?",  date('Y-m-d ',strtotime($datefrom)));
		if(!empty($dateto)) 
			$getchart->where("DATE_FORMAT(ScoreDate, '%Y-%m-%d') >= ?",  date('Y-m-d ',strtotime($dateto)));
		
		$getchart->group ( array ("score.Score") );
	
	
	
		$getchart	= $db->fetchAll($getchart)->toarray();
		
		return $retarray	=	$getchart[0]['tot'].'='.$getchart[1]['tot'].'='.$getchart[2]['tot'].'='.$getchart[3]['tot'].'='.$getchart[4]['tot'];		
		exit;
		
	}
	
	
	
	
	public function DbeeChartData($datefrom='', $dateto='')  
	{
		$db = $this->getDbTable();
		
		$getchart 	= 	$db->select();
		$getchart->setIntegrityCheck( false );
		$getchart->from(array('dbee' => 'tblDbees'), array('type' => 'dbee.Type','tot' =>  new Zend_Db_Expr('count(dbee.Type)')));
		$getchart->where("dbee.Type!=0");
		$getchart->where("dbee.Type!=2");
		$getchart->where("dbee.Type!=3");
		$getchart->where("dbee.Type!=4");
		$getchart->where("dbee.clientID = ?", clientID);
		if(!empty($datefrom) )
		    $getchart->where("DATE_FORMAT(PostDate, '%Y-%m-%d') <= ?",  date('Y-m-d',strtotime($datefrom)));
		if(!empty($dateto))     
			$getchart->where("DATE_FORMAT(PostDate, '%Y-%m-%d') >= ?",  date('Y-m-d',strtotime($dateto)));
		

		$getchart->group ( array ("dbee.Type") ); 
		$getchart	= $db->fetchAll($getchart)->toarray();	
		//print_r($getchart);	
		return $retarray	=	$getchart[0]['tot'].'='.$getchart[1]['tot'].'='.$getchart[2]['tot'].'='.$getchart[3]['tot'].'='.$getchart[4]['tot'];		
				
	}


	public function SurveyChartData($ParentId)  
	{
		 $db  = Zend_Db_Table_Abstract::getDefaultAdapter();
		 $answers=$this->surveyquestion($ParentId);
		 $sureydata=array();
		 foreach($answers as $ans)
		 {
		 	
		 	$query="select count(*) as TotalAns from tblSurveyAnswer where answer_id='".$ans['id']."'";
		 	
            $data=$db->fetchRow($query);

            $sureydata[]=$ans['content'].'=='.$data['TotalAns'];
		 }

		 return $sureydata;
		 
		
				
	}
	
	public function signedupuserData($userid='',$datefrom='', $dateto='') 
	{
		$db = $this->getDbTable();		
		$getchart 	= 	$db->select();
		$getchart->setIntegrityCheck( false );
		$getchart->from(array('user' => 'tblUsers'), array('totuser' =>  new Zend_Db_Expr('count(user.UserID)'),'user.Socialtype'));
		if(!empty($userid))
		{
			$getchart->where('user.UserID ='.$userid);
		}
		if(!empty($datefrom) && !empty($dateto)){
		    $getchart->where("DATE_FORMAT(RegistrationDate, '%Y-%m-%d') >= ?",  date('Y-m-d',strtotime($datefrom)));
			$getchart->where("DATE_FORMAT(RegistrationDate, '%Y-%m-%d') <= ?",  date('Y-m-d',strtotime($dateto)));
		} 
		$getchart->where("user.clientID = ?", clientID);
		$getchart->group('user.Socialtype');
				
		//exit;
		$getchart	= $db->fetchAll($getchart)->toarray();

		$cdata = array();
		foreach ($getchart as $key => $value) {
			if($value['Socialtype']==0)
			{
				$cdata[] = array('name'=> 'Platform registration','y'=> (int)$value['totuser'],'sliced'=> false, 'selected'=> false);
			}
			if($value['Socialtype']==1)
			{
				$cdata[] = array('name'=> 'Facebook sign in','y'=> (int)$value['totuser'],'sliced'=> false, 'selected'=> false);
			}
			if($value['Socialtype']==2)
			{
				$cdata[] = array('name'=> 'Twitter sign in','y'=> (int)$value['totuser'],'sliced'=> false, 'selected'=> false);
			}
			if($value['Socialtype']==3)
			{
				$cdata[] = array('name'=> 'LinkedIn sign in','y'=> (int)$value['totuser'],'sliced'=> false, 'selected'=> false);
			}
			if($value['Socialtype']==4)
			{
				$cdata[] = array('name'=> 'Google+ sign in','y'=> (int)$value['totuser'],'sliced'=> false, 'selected'=> false);
			}
		}
		return $cdata;
	}

	public function AllactivityAdminbeforeLogin($userid='',$datefrom='', $dateto='') 
	{
		$userid =  $_SESSION['Zend_Auth']['storage']['UserID'];

		$usr = "SELECT `LastLoginDate`,`LastLogoutDate` FROM `tblUsers` WHERE UserID=".$userid;
		$userD = $this->_db->query($usr)->fetchAll();

		//echo "<pre>"; print_r($userD);

		$sql2 = "select COUNT(c.act_userId) as actTot,act_message from tblactivity c where c.act_userId !='' AND ((c.act_message = 1 ) || (c.act_message = 2 ) || (c.act_message = 3 ) || (c.act_message = 6 ) || (c.act_message = 8 ) || (c.act_message = 9 ) || (c.act_message = 10 ) ||  (c.act_message = 19 ) ||  (c.act_message = 27 ) || (c.act_message = 28 ) || (c.act_message = 33 ) || (c.act_message = 34 )) AND  c.clientID=".clientID ;

	    $sql2 .= " AND DATE_FORMAT(c.act_date, '%Y-%m-%d %H:%i:%s') >= '". date('Y-m-d H:i:s',strtotime($userD[0]['LastLogoutDate']))."'";

		$sql2 .= " AND DATE_FORMAT(c.act_date, '%Y-%m-%d %H:%i:%s') <= '".  date('Y-m-d H:i:s',strtotime($userD[0]['LastLoginDate']))."'";

		$sql2 .= " group by act_message";
	 
		return  $this->_db->query($sql2)->fetchAll();

	}	

	public function AllactivityAdmin($userid='',$datefrom='', $dateto='') 
	{

		$sql = "select COUNT(c.act_userId) as actTot from tblactivity c where c.act_ownerid  =" .$userid ." AND ((c.act_message = 2 ) || (c.act_message = 6 ) || (c.act_message = 19 ) || (c.act_message = 25 ) || (c.act_message = 26 ) || (c.act_message = 27 ) || (c.act_message = 28 ) || (c.act_message = 33 ) || (c.act_message = 34 )) AND c.act_userId !=" .$userid ." AND c.clientID=".clientID ;
		//$sql = "select COUNT(c.act_userId) as actTot from tblactivity c where c.act_ownerid  =" .$userid ." AND  c.act_userId !=" .$userid ." AND c.clientID=".clientID ;
		if(!empty($datefrom) )
		    $sql .= " AND DATE_FORMAT(c.act_date, '%Y-%m-%d') >= '". date('Y-m-d',strtotime($datefrom))."'";
		if( !empty($dateto)) 
			$sql .= " AND DATE_FORMAT(c.act_date, '%Y-%m-%d') <= '".  date('Y-m-d',strtotime($dateto))."'";

		if(!empty($datefrom) )
		$dbdate .= " AND DATE_FORMAT(PostDate, '%Y-%m-%d') >= '". date('Y-m-d',strtotime($datefrom))."'";
		if( !empty($dateto)) 
		$dbdate .= " AND DATE_FORMAT(PostDate, '%Y-%m-%d') <= '".  date('Y-m-d',strtotime($dateto))."'";


		$events = $this->myclientdetails->passSQLquery('select count(*) as totEve from tblDbees where clientID='.clientID.' AND  events!=0 '.$dbdate );

		$groups = $this->myclientdetails->passSQLquery('select count(*) as totGrp from tblDbees,tblGroups where tblDbees.GroupID = tblGroups.ID AND tblDbees.clientID='.clientID.' AND tblGroups.User= ' .$userid.$dbdate  );
	
	

		
		$sql2 = "select COUNT(c.act_userId) as actTot from tblactivity c where c.act_ownerid  !=" .$userid ." AND c.act_userId !=" .$userid ." AND c.clientID=".clientID ;
		if(!empty($datefrom) )
		    $sql2 .= " AND DATE_FORMAT(c.act_date, '%Y-%m-%d') >= '". date('Y-m-d',strtotime($datefrom))."'";
		if( !empty($dateto)) 
			$sql2 .= " AND DATE_FORMAT(c.act_date, '%Y-%m-%d') <= '".  date('Y-m-d',strtotime($dateto))."'";
	 	
		$res = $this->_db->query($sql)->fetchAll();

		$res2 = $this->_db->query($sql2)->fetchAll();

		$posts =  ($res[0]['actTot']+$groups[0]['totGrp']+$events[0]['totEve']);
		
		$total		= $res[0]['actTot']+$res2[0]['actTot'];	
	
		return $retarray	=	$total.'='.$posts.'='.$res2[0]['actTot'];	
	}
	public function breakActivityAdmin($userid='',$datefrom='', $dateto='') 
	{

		$db = $this->getDbTable();
		$date='';
		
		if(!empty($datefrom) )
		$date .= " AND DATE_FORMAT(act_date, '%Y-%m-%d') <= '". date('Y-m-d',strtotime($datefrom))."'";
		if( !empty($dateto)) 
		$date .= " AND DATE_FORMAT(act_date, '%Y-%m-%d') >= '".  date('Y-m-d',strtotime($dateto))."'";

		$sql = "select COUNT(c.act_userId) as actTot, c.act_message,c.act_type from tblactivity c where c.act_ownerid  =" .$userid ." AND ((c.act_message = 2 ) || (c.act_message = 6 ) || (c.act_message = 19 ) || (c.act_message = 25 ) || (c.act_message = 26 ) || (c.act_message = 27 ) || (c.act_message = 28 ) || (c.act_message = 33 ) || (c.act_message = 34 )) AND c.act_userId !=" .$userid ." AND c.clientID=".clientID." ".$date." GROUP BY (act_type) " ;

		if(!empty($datefrom) )
		$dbdate .= " AND DATE_FORMAT(PostDate, '%Y-%m-%d') >= '". date('Y-m-d',strtotime($datefrom))."'";
		if( !empty($dateto)) 
		$dbdate .= " AND DATE_FORMAT(PostDate, '%Y-%m-%d') <= '".  date('Y-m-d',strtotime($dateto))."'";
		$events = $this->myclientdetails->passSQLquery('select count(*) as totEve from tblDbees where clientID='.clientID.' AND  events!=0 '.$dbdate );

		$groups = $this->myclientdetails->passSQLquery('select count(*) as totGrp from tblDbees,tblGroups where tblDbees.GroupID = tblGroups.ID AND tblDbees.clientID='.clientID.' AND tblGroups.User= ' .$userid.$dbdate  );
	
		$posts = $groups[0]['totGrp']+$events[0]['totEve'];

		$postarr[] = array('actTot'=>$posts,'act_message'=>1,'act_type'=>1);

		$retData = $this->_db->query($sql)->fetchAll();
		$final = array_merge($postarr,$retData);
		
		return $final;
		
	}
	public function breakInfluence($userid='',$datefrom='', $dateto='') 
	{

		$db = $this->getDbTable();
		$date='';
		
		if(!empty($datefrom) )
		$date .= " AND DATE_FORMAT(date_added, '%Y-%m-%d') <= '". date('Y-m-d',strtotime($datefrom))."'";
		if( !empty($dateto)) 
		$date .= " AND DATE_FORMAT(date_added, '%Y-%m-%d') >= '".  date('Y-m-d',strtotime($dateto))."'";

		$sql = "select COUNT(c.id) as actTot, c.ArticleType from tblInfluence c where  c.clientID=".clientID." ".$date." GROUP BY (ArticleType) " ;
		return $retData = $this->_db->query($sql)->fetchAll();
		
		
	}
	public function PostBreakdown($datefrom='', $dateto='',$userid='')  
	{
		$db = $this->getDbTable();
		
		$date='';
		
		if(!empty($datefrom) )
		$dbdate .= " AND DATE_FORMAT(PostDate, '%Y-%m-%d') <= '". date('Y-m-d',strtotime($datefrom))."'";
		if( !empty($dateto)) 
		$dbdate .= " AND DATE_FORMAT(PostDate, '%Y-%m-%d') >= '".  date('Y-m-d',strtotime($dateto))."'";

		$text = $this->myclientdetails->passSQLquery('select count(*) as totEve from tblDbees where clientID='.clientID.' AND Text!="" AND Link="" AND Pic="" AND Vid=""  AND Type!=5 AND Type!=6 AND Type!=7 AND Type!=9  AND User!='.$userid.$dbdate );

		$link = $this->myclientdetails->passSQLquery('select count(*) as totEve from tblDbees where clientID='.clientID.' AND  Link!="" AND Type!=5 AND Type!=6 AND Type!=7 AND Type!=9  AND User!='.$userid.$dbdate );
		
		$Pics = $this->myclientdetails->passSQLquery('select count(*) as totEve from tblDbees where clientID='.clientID.' AND  Pic!="" AND Type!=5 AND Type!=6 AND Type!=7 AND Type!=9  AND User!='.$userid.$dbdate );
		
		$vidz = $this->myclientdetails->passSQLquery('select count(*) as totEve from tblDbees where clientID='.clientID.' AND  Vid!="" AND Type!=5 AND Type!=6 AND Type!=7  AND User!='.$userid.$dbdate );
		$poll = $this->myclientdetails->passSQLquery('select count(*) as totEve from tblDbees where clientID='.clientID.' AND Type=5  AND User!='.$userid.$dbdate );
		
		/*echo "<pre>";
		print_r($text);	exit;*/
		return $retarray	=	$text[0]['totEve'].'='.$link[0]['totEve'].'='.$Pics[0]['totEve'].'='.$vidz[0]['totEve'].'='.$poll[0]['totEve'];		
				
	}

	public function breakActivityPlatform($userid='',$datefrom='', $dateto='') 
	{
		$db = $this->getDbTable();
		$date='';
		
		if(!empty($datefrom) )
		$date .= " AND DATE_FORMAT(act_date, '%Y-%m-%d') <= '". date('Y-m-d',strtotime($datefrom))."'";
		if( !empty($dateto)) 
		$date .= " AND DATE_FORMAT(act_date, '%Y-%m-%d') >= '".  date('Y-m-d',strtotime($dateto))."'";

		//$sql = "select COUNT(c.act_userId) as actTot, c.act_message,c.act_type from tblactivity c where  ((c.act_message = 1 ) || (c.act_message = 2 ) || (c.act_message = 6 ) || (c.act_message = 34 ) || (c.act_type = 12 ) ) AND c.act_userId !=" .$userid ." AND c.clientID=".clientID." ".$date." GROUP BY (act_type) " ;
		$sql = "select COUNT(c.act_userId) as actTot, c.act_message,c.act_type from tblactivity c where c.act_message NOT IN (13,14,20,21,22,27,30,29,38,39,40,41,42,43,44,45,46,47,48) AND  c.act_ownerid  !=" .$userid ." AND c.act_userId !=" .$userid ." AND c.clientID=".clientID.$date." GROUP BY (act_message) " ;

		$retData = $this->_db->query($sql)->fetchAll();

		return $retData;
		
	}
	public function TotalChartData($userid='',$datefrom='', $dateto='') 
	{
		$db = $this->getDbTable();


		
		$getchart 	= 	$db->select();
		$getchart->setIntegrityCheck( false );
		$getchart->from(array('dbee' => 'tblDbees'), array('dbee' =>  new Zend_Db_Expr('count(dbee.DbeeID)')));
		$getchart->where("dbee.clientID = ?", clientID);
		if(!empty($userid))
		{
			$getchart->where('dbee.User ='.$userid);
		}
		if(!empty($datefrom) )
		    $getchart->where("DATE_FORMAT(PostDate, '%Y-%m-%d') <= ?",  date('Y-m-d H:i:s',strtotime($datefrom)));
		if( !empty($dateto))   
			$getchart->where("DATE_FORMAT(PostDate, '%Y-%m-%d') >= ?",  date('Y-m-d H:i:s',strtotime($dateto)));
		
		
		$getcmt 	= 	$db->select();
		$getcmt->setIntegrityCheck( false );
		$getcmt->from(array('comments' => 'tblDbeeComments'), array('comment' =>  new Zend_Db_Expr('count(comments.CommentID)')));
		$getcmt->where("comments.clientID = ?", clientID);
		if(!empty($userid))
		{
			$getcmt->where('comments.DbeeOwner ='.$userid);
		}
		if(!empty($datefrom)  )
		    $getcmt->where("DATE_FORMAT(CommentDate, '%Y-%m-%d') <= ?",  date('Y-m-d',strtotime($datefrom)));
		if(!empty($dateto))    
			$getcmt->where("DATE_FORMAT(CommentDate, '%Y-%m-%d') >= ?",  date('Y-m-d',strtotime($dateto)));
		 
		
		$getscor 	= 	$db->select();
		$getscor->setIntegrityCheck( false );
		$getscor->from(array('scor' => 'tblScoring'), array('score' =>  new Zend_Db_Expr('count(scor.ScoreID)')));

		$getscor->where("scor.clientID = ?", clientID);
		if(!empty($userid))
		{
			$getscor->where('scor.Owner ='.$userid);
		}
		if(!empty($datefrom) )
		    $getscor->where("DATE_FORMAT(ScoreDate, '%Y-%m-%d') <= ?",  date('Y-m-d',strtotime($datefrom)));
		if(!empty($dateto))  
			$getscor->where("DATE_FORMAT(ScoreDate, '%Y-%m-%d') >= ?",  date('Y-m-d',strtotime($dateto)));
		 

		$getgroup 	= 	$db->select();
		$getgroup->setIntegrityCheck( false );
		$getgroup->from(array('group' => 'tblGroups'), array('group' =>  new Zend_Db_Expr('count(group.ID)')));

		$getgroup->where("group.clientID = ?", clientID);
		if(!empty($userid))
		{
			$getgroup->where('group.User ='.$userid);
		}
		if(!empty($datefrom) )
		    $getgroup->where("DATE_FORMAT(GroupDate, '%Y-%m-%d') <= ?",  date('Y-m-d',strtotime($datefrom)));
		if( !empty($dateto)) 
			$getgroup->where("DATE_FORMAT(GroupDate, '%Y-%m-%d') >= ?",  date('Y-m-d',strtotime($dateto)));
	 	
		
		$getchart	= $db->fetchAll($getchart)->toarray();
		$getcmt		= $db->fetchAll($getcmt)->toarray();
		$getscore	= $db->fetchAll($getscor)->toarray();
		$getgroup	= $db->fetchAll($getgroup)->toarray();
		$total		= ($getchart[0]['dbee'] + $getcmt[0]['comment'] + $getgroup[0]['group'] + $getscore[0]['score']);	
		//return $retarray	=	'total='.$total.' dbee='.$getchart[0]['dbee'].' comments='.$getcmt[0]['comment'].' score='.$getscore[0]['score'].' group='.$getgroup[0]['group'];	
		
		return $retarray	=	$total.'='.$getchart[0]['dbee'].'='.$getcmt[0]['comment'].'='.$getscore[0]['score'].'='.$getgroup[0]['group'];	
	}

	public function userDetailData($userid='') 
	{
		$db = $this->getDbTable();
		
		$getchart 	= 	$db->select();
		$getchart->setIntegrityCheck( false );
		$getchart->from(array('dbee' => 'tblDbees'), array('dbee' =>  new Zend_Db_Expr('count(dbee.DbeeID)')));
		if(!empty($userid))
		{
			$getchart->where('dbee.User ='.$userid);
		}
		$getchart->where("dbee.clientID = ?", clientID);
		
		$getcmt 	= 	$db->select();
		$getcmt->setIntegrityCheck( false );
		$getcmt->from(array('comments' => 'tblDbeeComments'), array('comment' =>  new Zend_Db_Expr('count(comments.CommentID)')));
		if(!empty($userid))
		{
			$getcmt->where('comments.DbeeOwner ='.$userid);
		}
		$getcmt->where("comments.clientID = ?", clientID);
		
		$getscor 	= 	$db->select();
		$getscor->setIntegrityCheck( false );
		$getscor->from(array('scor' => 'tblScoring'), array('score' =>  new Zend_Db_Expr('count(scor.ScoreID)')));
		if(!empty($userid))
		{
			$getscor->where('scor.Owner ='.$userid);
		}
		$getscor->where("scor.clientID = ?", clientID);

		$getgroup 	= 	$db->select();
		$getgroup->setIntegrityCheck( false );
		$getgroup->from(array('group' => 'tblGroups'), array('group' =>  new Zend_Db_Expr('count(group.ID)')));
		if(!empty($userid))
		{
			$getgroup->where('group.User ='.$userid);
		}
		$getgroup->where("group.clientID = ?", clientID);
		
		$getchart	= $db->fetchAll($getchart)->toarray();
		$getcmt		= $db->fetchAll($getcmt)->toarray();
		$getscore	= $db->fetchAll($getscor)->toarray();
		$getgroup	= $db->fetchAll($getgroup)->toarray();
		$total		= ($getchart[0]['dbee'] + $getcmt[0]['comment'] + $getgroup[0]['group'] + $getscore[0]['score']);	
		//return $retarray	=	'total='.$total.' dbee='.$getchart[0]['dbee'].' comments='.$getcmt[0]['comment'].' score='.$getscore[0]['score'].' group='.$getgroup[0]['group'];	
		
		return $retarray	=	$total.'='.$getchart[0]['dbee'].'='.$getcmt[0]['comment'].'='.$getscore[0]['score'].'='.$getgroup[0]['group'];	
	}
	public function userdetailsDataChild($userid='',$request='',$limit='') 
	{
		$common		=   new Admin_Model_Common();
		$db 		= $this->getDbTable();
		if ($request=='dbee') {

			$select = $db->select();
			$select->setIntegrityCheck( false );
			$select->distinct('dbee.DbeeID')->from( array('dbee' => 'tblDbees'), array('DbeeID'=>'dbee.DbeeID','type' => 'dbee.Type','description' => 'dbee.Text', 'Link' => 'dbee.Link','LinkTitle' => 'dbee.LinkTitle','LinkDesc' => 'dbee.LinkDesc','UserLinkDesc' => 'dbee.UserLinkDesc','Pic' => 'dbee.Pic','PicDesc' => 'dbee.PicDesc','Vid' => 'dbee.Vid','VidDesc' => 'dbee.VidDesc','VidSite' => 'dbee.VidSite','VidID' => 'dbee.VidID','Audio' => 'dbee.Audio','PollText' => 'dbee.PollText','twitter' => 'dbee.TwitterTag','redbee'=>'dbee.ReDBUsers','PostDate'=>'dbee.PostDate' ));
			$select->join( array('u' => 'tblUsers'), 'u.UserID=dbee.User', array( 'UserID' => 'u.UserID','username' => 'u.Name','lname' => 'u.lname','image' =>'u.ProfilePic') );
			$select->setIntegrityCheck( false );	
			$select->where("dbee.clientID = ?", clientID);	
			if(!empty($userid)){ $select->where('dbee.User = '.$userid.''); } // for searching 		
			$select->order('dbee.LastActivity DESC');
			if($limit!='all')
			$select->LIMIT($limit, 0);			
			$result= $db->fetchAll($select);		
			return $result;			
		}

		if ($request=='comment') {
			$getchart->from(array('comments' => 'tblDbeeComments'), array('comments' =>  new Zend_Db_Expr('count(comments.CommentID)')));
			if(!empty($userid)) { $getchart->where('comments.DbeeOwner ='.$userid);	}
			$getchart	= $db->fetchAll($getchart)->toarray();

			$getcmt 	= 	$db->select();
			$getcmt->setIntegrityCheck( false );
			$getcmt->from(array('comments' => 'tblDbeeComments'), array('comment' =>  new Zend_Db_Expr('count(comments.CommentID)')));
			if(!empty($userid))	{	$getcmt->where('comments.UserID ='.$userid);	}
			$getcmt->where("comments.clientID = ?", clientID);
			$getcmt		= $db->fetchAll($getcmt)->toarray();
			
			$total  = $getchart[0]['comments']+$getcmt[0]['comment'];
			
			return $retarray	=	$total.'='.$getchart[0]['comments'].'='.$getcmt[0]['comment'];
		}

		if ($request=='score') 
		{
			$getchart->from(array('scor' => 'tblScoring'), array('score' =>  new Zend_Db_Expr('count(scor.ScoreID)')));		
			if(!empty($userid))
			{
				$getchart->where('scor.Owner ='.$userid);
			}
			$getchart->where("scor.clientID = ?", clientID);
			$getchart	= $db->fetchAll($getchart)->toarray();
			//***
			$getscor 	= 	$db->select();
			$getscor->setIntegrityCheck( false );
			$getscor->from(array('scor' => 'tblScoring'), array('score' =>  new Zend_Db_Expr('count(scor.ScoreID)')));
			if(!empty($userid))
			{
				$getscor->where('scor.UserID ='.$userid);
			}
			$getscor->where("scor.clientID = ?", clientID);
			$getscor		= $db->fetchAll($getscor)->toarray();
			$total  = $getchart[0]['score']+$getscor[0]['score'];
			
			return $retarray	=	$total.'='.$getchart[0]['score'].'='.$getscor[0]['score'];
		}
		if ($request=='group') 
		{
			$getgroup 	= 	$db->select();
			$getgroup->setIntegrityCheck( false );
			$getgroup->from(array('group' => 'tblGroups'), array('group' =>  new Zend_Db_Expr('count(group.ID)'),'type'=>'GroupPrivacy'));

			$getgroup->join( array('gt' => 'tblGroupTypes'), 'gt.TypeID = group.GroupType');
			$getgroup->join( array('u' => 'tblUsers'), 'u.UserID=group.User' );
		  	$getgroup->where('u.Status = ?', '1');
		  	$getgroup->where("gt.clientID = ?", clientID);
			
			if(!empty($userid))
			{
				$getgroup->where('group.User ='.$userid);
				$getgroup->where('group.GroupPrivacy != 0');
				$getgroup->group('group.GroupPrivacy');
			}
			
			return $getchart	= $db->fetchAll($getgroup)->toarray();	
		}


	
	}

	public function userasexpert($userid='',$request='') 
	{
		
		$totexpert 	= 	$this->_db->select();
		$selfexpert = 	$this->_db->select();
		$invitexpert = 	$this->_db->select();
		
		$totexpert->from(array('dbee' => 'tblDbees'), array('expert' =>  new Zend_Db_Expr('count(dbee.DbeeID)')));
		$totexpert->join(array('expert' => 'tblexpert'), 'expert.dbid = dbee.DbeeID','');
		$totexpert->where('expert.userid = '.$userid);
		$totexpert->where('expert.clientID = '.clientID);

		$invitexpert->from(array('dbee' => 'tblDbees'), array('invite' =>  new Zend_Db_Expr('count(dbee.DbeeID)')));
		$invitexpert->join(array('expert' => 'tblinvitexport'), 'expert.dbeeid = dbee.DbeeID','');
		$invitexpert->where('dbee.User = '.$userid);
		$invitexpert->where('dbee.clientID = '.clientID);
		
	
		$selfexpert->from(array('dbee' => 'tblDbees'), array('selfexpert' =>  new Zend_Db_Expr('count(dbee.DbeeID)')));
		$selfexpert->join(array('expert' => 'tblexpert'), 'expert.dbid = dbee.DbeeID','');
		$selfexpert->where('expert.userid = dbee.User');
		
		$selfexpert->where('expert.userid = '.$userid);
		$selfexpert->where('expert.clientID = '.clientID);
		

		$exp  = $this->_db->fetchAll($totexpert);
		$self = $this->_db->fetchAll($selfexpert);
		$invite = $this->_db->fetchAll($invitexpert);

		return $exp[0]['expert'].'='.$self[0]['selfexpert'].'='.$invite[0]['invite'];
	}

	public function PostBreakdownComparrsion($userid='')  
	{
		$db = $this->getDbTable();
		
		$retarray= array();
		
		

		$text = $this->myclientdetails->passSQLquery('select count(*) as totEve from tblDbees where clientID='.clientID.' AND Text!="" AND Link="" AND Pic="" AND Vid=""  AND Type!=5 AND Type!=6 AND Type!=7 AND Type!=9  AND User='.$userid );

		$link = $this->myclientdetails->passSQLquery('select count(*) as totEve from tblDbees where clientID='.clientID.' AND  Link!="" AND Type!=5 AND Type!=6 AND Type!=7  AND Type!=9 AND User='.$userid );
		
		$Pics = $this->myclientdetails->passSQLquery('select count(*) as totEve from tblDbees where clientID='.clientID.' AND  Pic!="" AND Type!=5 AND Type!=6 AND Type!=7  AND Type!=9  AND User='.$userid );
		
		$vidz = $this->myclientdetails->passSQLquery('select count(*) as totEve from tblDbees where clientID='.clientID.' AND  Vid!="" AND Type!=5 AND Type!=6 AND Type!=7  AND Type!=9 AND User='.$userid );
		
		$poll = $this->myclientdetails->passSQLquery('select count(*) as totEve from tblDbees where clientID='.clientID.' AND Type=5  AND User='.$userid );

		$event = $this->myclientdetails->passSQLquery('select count(*) as totEve from tblDbees where clientID='.clientID.' AND Type=9  AND User='.$userid );
		
		/*echo "<pre>";
		print_r($text);	exit;*/
		$retarray[] = array('dbee'=>$text[0]['totEve'],'Type'=>1);
		$retarray[] = array('dbee'=>$link[0]['totEve'],'Type'=>2);
		$retarray[] = array('dbee'=>$Pics[0]['totEve'],'Type'=>3);
		$retarray[] = array('dbee'=>$vidz[0]['totEve'],'Type'=>4);
		$retarray[] = array('dbee'=>$poll[0]['totEve'],'Type'=>5);
		$retarray[] = array('dbee'=>$event[0]['totEve'],'Type'=>9);
		return $retarray;
		

				
	}

	

	public function TotalChartDataChild($userid='',$request) 
	{
		$common		=   new Admin_Model_Common();
		$db 		= $this->getDbTable();
		$getchart 	= 	$db->select();
		$getchart->setIntegrityCheck( false );
		
		if ($request=='dbee') {
			$getchart->from(array('dbee' => 'tblDbees'), array('dbee' =>  new Zend_Db_Expr('count(dbee.DbeeID)'), 'Type' => 'dbee.Type'));
			$getchart->where("dbee.clientID = ?", clientID);
			if(!empty($userid))
			{
				$getchart->where('dbee.User ='.$userid);
				$getchart->group('dbee.Type');
			}

			return $getchart	= $db->fetchAll($getchart)->toarray();
		}

		if ($request=='comment') {
			$getchart->from(array('comments' => 'tblDbeeComments'), array('comments' =>  new Zend_Db_Expr('count(comments.CommentID)')));
			if(!empty($userid)) { $getchart->where('comments.DbeeOwner ='.$userid);	}
			$getchart	= $db->fetchAll($getchart)->toarray();

			$getcmt 	= 	$db->select();
			$getcmt->setIntegrityCheck( false );
			$getcmt->from(array('comments' => 'tblDbeeComments'), array('comment' =>  new Zend_Db_Expr('count(comments.CommentID)')));
			if(!empty($userid))	{	$getcmt->where('comments.UserID ='.$userid);	}

			$getcmt->where("comments.clientID = ?", clientID);
			$getcmt		= $db->fetchAll($getcmt)->toarray();
			
			$total  = $getchart[0]['comments']+$getcmt[0]['comment'];
			
			return $retarray	=	$total.'='.$getchart[0]['comments'].'='.$getcmt[0]['comment'];
		}

		if ($request=='score') 
		{
			$getchart->from(array('scor' => 'tblScoring'), array('score' =>  new Zend_Db_Expr('count(scor.ScoreID)')));		
			if(!empty($userid))
			{
				$getchart->where('scor.Owner ='.$userid);
			}
			$getchart	= $db->fetchAll($getchart)->toarray();
			//***
			$getscor 	= 	$db->select();
			$getscor->setIntegrityCheck( false );
			$getscor->from(array('scor' => 'tblScoring'), array('score' =>  new Zend_Db_Expr('count(scor.ScoreID)')));
			if(!empty($userid))
			{
				$getscor->where('scor.UserID ='.$userid);
			}
			$getscor->where("scor.clientID = ?", clientID);
			$getscor		= $db->fetchAll($getscor)->toarray();
			$total  = $getchart[0]['score']+$getscor[0]['score'];
			
			return $retarray	=	$total.'='.$getchart[0]['score'].'='.$getscor[0]['score'];
		}
		if ($request=='group') 
		{
			$getgroup 	= 	$db->select();
			$getgroup->setIntegrityCheck( false );
			$getgroup->from(array('group' => 'tblGroups'), array('group' =>  new Zend_Db_Expr('count(group.ID)'),'type'=>'GroupPrivacy'));

			$getgroup->join( array('gt' => 'tblGroupTypes'), 'gt.TypeID = group.GroupType');
			$getgroup->join( array('u' => 'tblUsers'), 'u.UserID=group.User' );
		  	$getgroup->where('u.Status = ?', '1');
			$getgroup->where("gt.clientID = ?", clientID);
			if(!empty($userid))
			{
				$getgroup->where('group.User ='.$userid);
				$getgroup->where('group.GroupPrivacy != 0');
				$getgroup->group('group.GroupPrivacy');
			}
			
			return $getchart	= $db->fetchAll($getgroup)->toarray();	
		}
		if ($request=='groupmember') 
		{
			$getmgroup 	= 	$db->select();
			$getmgroup->setIntegrityCheck( false );
			$getmgroup->from(array('gm' => 'tblGroupMembers'), array('gmTot' =>  new Zend_Db_Expr('count(gm.GroupID)'),''));
			$getmgroup->join( array('group' => 'tblGroups'), 'gm.GroupID = group.ID','GroupPrivacy');
			
			$getmgroup->where("gm.clientID = ?", clientID);
			if(!empty($userid))
			{
				$getmgroup->where('gm.User ='.$userid);
				$getmgroup->where('group.GroupPrivacy != 0');
				$getmgroup->where('gm.Status =1');
				$getmgroup->group('group.GroupPrivacy');
			}
			//echo $getmgroup;exit;
			return $getmchart	= $db->fetchAll($getmgroup)->toarray();	
		}


	
	}
	
	
	
	
	
	
	/* deshboard function */
	 public function getDeshboard($searchfield) {		
		
		$db = $this->getDbTable();
		
		$select = $db->select();
		$select->distinct('u.UserID')->from( array('u' => 'tblUsers'), array( 'UserID' => 'u.UserID','username' => 'u.Name','lname' => 'u.lname','image' =>'u.ProfilePic') );
		$select->setIntegrityCheck( false );
		$select->join( array('dbee' => 'tblDbees'), 'dbee.User = u.UserID',  array('DbeeID'=>'dbee.DbeeID','type' => 'dbee.Type', 'Link' => 'dbee.Link','description' => 'dbee.Text','twitter' => 'dbee.TwitterTag','redbee'=>'dbee.ReDBUsers' ));
		$select->join( array('cat' => 'tblDbeeCats'), 'cat.CatID = dbee.Cats', array('category' => new Zend_Db_Expr('GROUP_CONCAT(cat.CatName)')));
		$select->where('u.Status = ?', '1');
		$select->group ( array ("u.UserID") );		
		$select->order('u.RegistrationDate DESC')->order('u.LastUpdateDate DESC');
		$select->LIMIT(20, 0);		
		$result= $db->fetchAll($select);
		return $result;		
    }

    public function gettopScoreusers($limit=2,$for='owner',$sctype='1',$datefrom='', $dateto='') {		
		
		$endOfCycle=date('Y-m-d h:i:s', strtotime("-190 days"));
		$db = $this->getDbTable();	
		
		$select = $db->select()->setIntegrityCheck( false )->from(array('score' => 'tblScoring'), array('totscore' =>  new Zend_Db_Expr('count(score.Score)') ,'type' => 'score.Type','ScoreID' => 'score.ScoreID','ID'=>'score.ID','ScoreDate'=>'score.ScoreDate'));
		if($for=='user'){
			$select->joinleft( array('u' => 'tblUsers'), 'u.UserID=score.UserID', array( 'uname' => 'u.Username','username' => 'u.Name','lname' => 'u.lname','image' =>'u.ProfilePic') );
			$select->where('u.Status = ?', '1');
			$select->where("score.clientID = ?", clientID);
			$select->group('u.UserID');
		}
		if($for=='owner'){
			$select->join( array('owner' => 'tblUsers'), 'owner.UserID=score.Owner', array( 'uname' => 'owner.Username','username' => 'owner.Name','lname' => 'owner.lname','image' =>'owner.ProfilePic') );
			$select->where('owner.Status = ?', '1');
			$select->where("score.clientID = ?", clientID);
			$select->group('owner.UserID');
		}	
		
		$select->where('score.Score = ?', $sctype);	
		if(!empty($datefrom) ) $select->where("DATE_FORMAT(ScoreDate, '%Y-%m-%d') >= ?",  date('Y-m-d',strtotime($datefrom)));
		if(!empty($dateto))	   $select->where("DATE_FORMAT(ScoreDate, '%Y-%m-%d') <= ?",  date('Y-m-d',strtotime($dateto)));
			
		$select->order('totscore DESC');
		$select->LIMIT($limit, 0);

		$result= $db->fetchAll($select);	
	
		return $result;		
    }
    public function getVisitersPosts($limit=10,$offset=0,$datefrom='', $dateto='') {		
		
		$endOfCycle=date('Y-m-d h:i:s', strtotime("-30 days"));
		$db = $this->getDbTable();		
		$select = $db->select();

		$select->from( array('stat' => 'tbldbstats'), array('totvisiters' =>  new Zend_Db_Expr('count(stat.stats_id)')));

		$select->join( array('dbee' => 'tblDbees'),'stat.stats_dbid=dbee.DbeeID',  array('DbeeID'=>'dbee.DbeeID','type' => 'dbee.Type','text' => 'dbee.Text', 'PollText' => 'dbee.PollText','dburl' => 'dbee.dburl','PostDate'=>'dbee.PostDate' ));

		$select->join( array('u' => 'tblUsers'), 'u.UserID=dbee.User', array( 'UserID' => 'u.UserID','username' => 'u.Name','lname' => 'u.lname','image' =>'u.ProfilePic') );
		
		$select->setIntegrityCheck( false );

		
		
		
		$select->where("stat.clientID = ?", clientID);
		//$select->where("totvisiters > ?", '1');

		if($datefrom=='month'){
			if(date('Y-m', strtotime($dateto))==date('Y-m')){
				
				$datefrm = strtotime("-30 day", strtotime($dateto));				
				$select->where("DATE_FORMAT(PostDate, '%Y-%m-%d') >= ?",  date("Y-m-d", $datefrm));
				$select->where("DATE_FORMAT(PostDate, '%Y-%m-%d') <= ?",  date('Y-m-d',strtotime($dateto)));
			}
			else $select->where("DATE_FORMAT(`PostDate`, '%Y-%m')=?", date('Y-m', strtotime($dateto)));
			//$select->where("DATE_FORMAT(`PostDate`, '%Y-%m')=?", date('Y-m', strtotime($dateto)));
		}
		else
		{
			$select->where('dbee.PostDate > "'.$endOfCycle.'"');
			if(!empty($datefrom) ) $select->where("DATE_FORMAT(PostDate, '%Y-%m-%d') >= ?",  date('Y-m-d',strtotime($datefrom)));
			if(!empty($dateto))	   $select->where("DATE_FORMAT(PostDate, '%Y-%m-%d') <= ?",  date('Y-m-d',strtotime($dateto)));
		}
		
		$select->group('stat.stats_dbid');
		$select->order('totvisiters DESC');
	    
		if($limit!='all')
		$select->LIMIT($offset, $limit);
	    // echo $select; exit;
		$result= $db->fetchAll($select);	
		//echo "<pre>"; print_r($result);
	
		return $result;		
    }

    public function getmyVisitingPosts($userId) {		
		
		//$endOfCycle=date('Y-m-d h:i:s', strtotime("-30 days"));
		$db = $this->getDbTable();		
		$select = $db->select();

		$select->from( array('stat' => 'tbldbstats') );
		$select->setIntegrityCheck( false );
		$select->join( array('dbee' => 'tblDbees'),'stat.stats_dbid=dbee.DbeeID',  array('DbeeID'=>'dbee.DbeeID','type' => 'dbee.Type','Text' => 'dbee.Text', 'PollText' => 'dbee.PollText','surveyTitle' => 'dbee.surveyTitle','dburl' => 'dbee.dburl','PostDate'=>'dbee.PostDate' ));

		$select->join( array('u' => 'tblUsers'), 'u.UserID=dbee.User', array( 'UserID' => 'u.UserID','username' => 'u.Name','lname' => 'u.lname','image' =>'u.ProfilePic') );
		
		//$select->where('dbee.PostDate > "'.$endOfCycle.'"');
		$select->where("dbee.clientID = ?", clientID);
		$select->where("stat.stats_userid = ?", $userId);
		$select->order("stat.stats_date DESC");
		$select->LIMIT(10);
		$result= $db->fetchAll($select);	
		//echo "<pre>"; print_r($result);
	
		return $result;		
    }
	public function getPopularDbee($limit=2,$dbeeID='',$calling='',$datefrom='', $dateto='') {		
		
		$endOfCycle=date('Y-m-d h:i:s', strtotime("-30 days"));
		$db = $this->getDbTable();		
		$select = $db->select();
		$select->distinct('dbee.DbeeID')->from( array('dbee' => 'tblDbees'), array('DbeeID'=>'dbee.DbeeID','type' => 'dbee.Type','text' => 'dbee.Text', 'Link' => 'dbee.Link','LinkTitle' => 'dbee.LinkTitle','LinkDesc' => 'dbee.LinkDesc','UserLinkDesc' => 'dbee.UserLinkDesc','Pic' => 'dbee.Pic','PicDesc' => 'dbee.PicDesc','Vid' => 'dbee.Vid','VidDesc' => 'dbee.VidDesc','VidSite' => 'dbee.VidSite','VidID' => 'dbee.VidID','PollText' => 'dbee.PollText','dburl'=>'dbee.dburl','PostDate'=>'dbee.PostDate' ));
		$select->join( array('u' => 'tblUsers'), 'u.UserID=dbee.User', array( 'UserID' => 'u.UserID','username' => 'u.Name','lname' => 'u.lname','image' =>'u.ProfilePic') );
		$select->join( array('commt' => 'tblDbeeComments'), 'commt.DbeeID=dbee.DbeeID', array( 'totcomment' =>  new Zend_Db_Expr('count(commt.DbeeID)')) );
		$select->setIntegrityCheck( false );

		$select->where('dbee.PostDate > "'.$endOfCycle.'"');
		$select->where("dbee.clientID = ?", clientID);
		
		if(!empty($dbeeID)){ $select->where('dbee.Type = '.$dbeeID.''); } // for searching 

		if(!empty($datefrom) ) $select->where("DATE_FORMAT(PostDate, '%Y-%m-%d') >= ?",  date('Y-m-d',strtotime($datefrom)));
		if(!empty($dateto))	   $select->where("DATE_FORMAT(PostDate, '%Y-%m-%d') <= ?",  date('Y-m-d',strtotime($dateto)));
		
		
		$select->group('commt.DbeeID');
		$select->order('totcomment DESC');
		if($limit!='all')
		$select->LIMIT($limit, 0);
	
		$result= $db->fetchAll($select);	
	
		return $result;		
    }
    public function getuserdbee($userid,$limit=4) {
    	$select = $this->_db->select()
	    	->distinct('dbee.DbeeID')->from( array('dbee' => 'tblDbees'), array('DbeeID'=>'dbee.DbeeID','type' => 'dbee.Type','description' => 'dbee.Text', 'Link' => 'dbee.Link','LinkTitle' => 'dbee.LinkTitle','LinkDesc' => 'dbee.LinkDesc','UserLinkDesc' => 'dbee.UserLinkDesc','Pic' => 'dbee.Pic','PicDesc' => 'dbee.PicDesc','Vid' => 'dbee.Vid','VidDesc' => 'dbee.VidDesc','VidSite' => 'dbee.VidSite','VidID' => 'dbee.VidID','Audio' => 'dbee.Audio','PollText' => 'dbee.PollText','twitter' => 'dbee.TwitterTag','redbee'=>'dbee.ReDBUsers','PostDate'=>'dbee.PostDate' ))
		    	->join( array('u' => 'tblUsers'), 'u.UserID=dbee.User', array( 'UserID' => 'u.UserID','username' => 'u.Name','lname' => 'u.lname','image' =>'u.ProfilePic') )
			    	->where('dbee.User = ?', $userid)->where("dbee.clientID = ?", clientID)		    	
					    	->order('dbee.LastActivity DESC')
					    		->limit($limit, 0);    	
    	$result= $this->_db->fetchAll($select);
    	return $result;
    }

    public function getDbeeByID($DbeeID) {
    	$select = $this->_db->select()
	    	->distinct('dbee.DbeeID')->from( array('dbee' => 'tblDbees'), array('Text'=>'dbee.Text','DbeeID'=>'dbee.DbeeID','type' => 'dbee.Type','description' => 'dbee.Text', 'Link' => 'dbee.Link','LinkTitle' => 'dbee.LinkTitle','LinkDesc' => 'dbee.LinkDesc','UserLinkDesc' => 'dbee.UserLinkDesc','Pic' => 'dbee.Pic','PicDesc' => 'dbee.PicDesc','Vid' => 'dbee.Vid','VidDesc' => 'dbee.VidDesc','VidSite' => 'dbee.VidSite','VidID' => 'dbee.VidID','Audio' => 'dbee.Audio','PollText' => 'dbee.PollText','twitter' => 'dbee.TwitterTag','redbee'=>'dbee.ReDBUsers','PostDate'=>'dbee.PostDate' ))
		    	->join( array('u' => 'tblUsers'), 'u.UserID=dbee.User', array( 'UserID' => 'u.UserID','username' => 'u.Name','lname' => 'u.lname','image' =>'u.ProfilePic') )
			    	->where('dbee.DbeeID = ?', $DbeeID)->where("dbee.clientID = ?", clientID)	    	
					    	->order('dbee.LastActivity DESC');    	
    	return $this->_db->fetchRow($select);
    }
    
    public function getusergroup($userid,$limit=4) {
    	$select = $this->_db->select()	    
		    	->from( array('group' => 'tblGroups'),  array('ID'=>'group.ID','GroupName'=>'group.GroupName','GroupPic'=>'group.GroupPic','description' => 'group.GroupDesc','type' => 'group.GroupType', 'Gdate' => 'group.GroupDate','GroupTypeOther' => 'group.GroupTypeOther'))
			    	->joinLeft( array('gt' => 'tblGroupTypes'), 'gt.TypeID = group.GroupType', array('TypeName' =>'gt.TypeName'))
				    	->join( array('u' => 'tblUsers'), 'u.UserID=group.User', array( 'UserID' => 'u.UserID','username' => 'u.Name','lname' => 'u.lname','image' =>'u.ProfilePic') )
					    	->where('group.User = ?', $userid)
						    	->where('u.Status = ?', '1')->where("group.clientID = ?", clientID)
							    	->order('group.GroupDate DESC')
							    		->limit($limit, 0);
    	
    	$result= $this->_db->fetchAll($select);
    	return $result;
    }
    
    public function callingcommoncomment($userid){
    	$select = $this->_db->select()
	    	->from( array('c'=>'tblDbeeComments'),array('c.DbeeID','type'=>'c.Type','c.Comment', 'c.Link','c.LinkTitle','c.LinkDesc','c.UserLinkDesc','c.Pic','c.PicDesc','c.Vid','c.VidDesc','c.VidSite','c.VidID','c.Audio','c.CommentDate'))
		    	->joinInner(array('u'=>'tblUsers'), 'u.UserID=c.UserID',array( 'UserID' => 'u.UserID','username' => 'u.Name','lname' => 'u.lname','image' =>'u.ProfilePic'))		    	
			    	->join( array('owner' => 'tblUsers'), 'owner.UserID=c.DbeeOwner', array( 'OwnerID' => 'owner.UserID','Ownername' => 'owner.Name','Ownerlname' => 'owner.lname','Ownerimage' =>'owner.ProfilePic') )
				    	->join( array('dbee' => 'tblDbees'), 'dbee.DbeeID = c.DbeeID',  array('dbee.DbeeID','dbee.dburl'))
					    	->where('u.Status = ?', '1')
					    		->where('c.UserID = ?', $userid)->where("c.clientID = ?", clientID)
					    			->limit('4', 0);
    	
    	return $result = $this->_db->fetchAll($select);
    }
    
    public function callingcommonscore($UserId) {
    	$select = $this->_db->select()
	    	->from(array('score' => 'tblScoring'), array('type' => 'score.Type','Score' => 'score.Score','ScoreID' => 'score.ScoreID','ID'=>'score.ID','ScoreDate'=>'score.ScoreDate'))
		    	->joinleft( array('u' => 'tblUsers'), 'u.UserID=score.UserID', array( 'UserID' => 'u.UserID','username' => 'u.Name','lname' => 'u.lname','image' =>'u.ProfilePic') )
			    	->join( array('owner' => 'tblUsers'), 'owner.UserID=score.Owner', array( 'OwnerID' => 'owner.UserID','Ownername' => 'owner.Name','Ownerlname' => 'owner.lname','Ownerimage' =>'owner.ProfilePic') )
				    	->where('u.Status = ?', '1')
					    	->where('score.UserID = '.$UserId.'')->where("score.clientID = ?", clientID)
					    		->order('ScoreDate DESC ')
    								->limit('7', 0);
    	
    	return $result = $this->_db->fetchAll($select);
    }
    
    public function callingcommononmescore($UserId) {
    	$select = $this->_db->select()
    	->from(array('score' => 'tblScoring'), array('type' => 'score.Type','Score' => 'score.Score','ScoreID' => 'score.ScoreID','ID'=>'score.ID','ScoreDate'=>'score.ScoreDate'))
    	->joinleft( array('u' => 'tblUsers'), 'u.UserID=score.UserID', array( 'UserID' => 'u.UserID','username' => 'u.Name','lname' => 'u.lname','image' =>'u.ProfilePic') )
    	->join( array('owner' => 'tblUsers'), 'owner.UserID=score.Owner', array( 'OwnerID' => 'owner.UserID','Ownername' => 'owner.Name','Ownerlname' => 'owner.lname','Ownerimage' =>'owner.ProfilePic') )
    	->where('u.Status = ?', '1')
    	->where('score.owner = '.$UserId.'')->where("score.clientID = ?", clientID)
    	->order('ScoreDate DESC ')
    	->limit('7', 0);
    	
    	return $result = $this->_db->fetchAll($select);
    }
    
    public function lastactivityData($UserId) {
    	 $select = $this->_db->select()
	    	->from(array('a' => 'tblactivity'))
		    	->joinleft( array('u' => 'tblUsers'), 'u.UserID=a.act_userId', array( 'UserID' => 'u.UserID','username' => 'u.Name','lname' => 'u.lname','image' =>'u.ProfilePic') )
		    	->joinleft( array('ow' => 'tblUsers'), 'ow.UserID=a.act_ownerid', array( 'owUserID' => 'ow.UserID','owusername' => 'ow.Name','owuserlname' => 'ow.lname','owimage' =>'ow.ProfilePic') )
			    	->where('a.act_userId = '.$UserId.'')->where("a.clientID = ?", clientID)->order('act_date DESC')    	
			    		->limit('7', 0);    	
    	return $result = $this->_db->fetchAll($select);
    }
	
	public function updateDbeeStatus($DbeeID,$status)
    {
        $data = array('Active' => $status);
        $this->_db->update('tblDbees', $data ,array("DbeeID='" . $DbeeID . "'" ));
    }

	public function getLiveDbee($limit=2,$dbeeID='',$calling='',$user='') {		
		
		$db = $this->getDbTable();
		
		$select = $db->select();
		$select->distinct('dbee.DbeeID')->from( array('dbee' => 'tblDbees'), array('DbeeID'=>'dbee.DbeeID','QA'=>'dbee.QA','type' => 'dbee.Type','videofile' => 'dbee.videofile','schedulepost' => 'dbee.schedulepost','dburl' => 'dbee.dburl','description' => 'dbee.Text', 'Link' => 'dbee.Link','LinkTitle' => 'dbee.LinkTitle','LinkDesc' => 'dbee.LinkDesc','LinkPic'=>'dbee.LinkPic','UserLinkDesc' => 'dbee.UserLinkDesc','Pic' => 'dbee.Pic','PicDesc' => 'dbee.PicDesc','Vid' => 'dbee.Vid','VidDesc' => 'dbee.VidDesc','VidSite' => 'dbee.VidSite','VidID' => 'dbee.VidID','VidTitle' => 'dbee.VidTitle','Audio' => 'dbee.Audio','PollText' => 'dbee.PollText','twitter' => 'dbee.TwitterTag','DbTag' => 'dbee.DbTag','redbee'=>'dbee.ReDBUsers','PostDate'=>'dbee.PostDate' ,'Active'=>'dbee.Active','User'=>'dbee.User','GroupID'=>'dbee.GroupID'));
		$select->join( array('u' => 'tblUsers'), 'u.UserID=dbee.User', array( 'UserID' => 'u.UserID','username' => 'u.Name','lname' => 'u.lname','image' =>'u.ProfilePic') );
		$select->joinLeft( array('g' => 'tblGroups'), 'g.ID=dbee.GroupID', array('g.GroupName') );
		//$select->joinLeft( array('p' => 'tblPollOptions'), 'p.PollID=dbee.DbeeID', array('p.OptionText') );
		$select->setIntegrityCheck( false );		
		//if(!empty($dbeeID)){ $select->where('dbee.Type = '.$dbeeID.''); } // for searching 	
		if(!empty($user)){
			$select->where('dbee.User = '.$user.'');
		} // for searching
		$select->where("dbee.clientID = ?", clientID);
		$select->where('dbee.Type != 15');
		$select->where('dbee.Type != 6');
		
		if($dbeeID!="")
		{
		 $select->where('dbee.DbeeID = '.$dbeeID.'');
		}

		$select->where('dbee.Type != 6');
		$select->where('dbee.Type != 7');
		//$select->order('dbee.LastActivity DESC');
		$select->order('dbee.DbeeID DESC');
		

		if($limit!='all')
		$select->LIMIT($limit, 0);	
		//echo $select->__toString();	die;	
		$result= $db->fetchAll($select);		
		return $result;		
    }
	
	public function getLiveDbeepopularpost($limit=2,$dbeeID='',$calling='',$user='') {		
		$db = $this->getDbTable();
		
		$select = $db->select();
		$select->distinct('dbee.DbeeID')->from( array('dbee' => 'tblDbees'), array('DbeeID'=>'dbee.DbeeID','type' => 'dbee.Type','schedulepost' => 'dbee.schedulepost','dburl' => 'dbee.dburl','description' => 'dbee.Text', 'Link' => 'dbee.Link','LinkTitle' => 'dbee.LinkTitle','LinkDesc' => 'dbee.LinkDesc','LinkPic'=>'dbee.LinkPic','UserLinkDesc' => 'dbee.UserLinkDesc','Pic' => 'dbee.Pic','PicDesc' => 'dbee.PicDesc','Vid' => 'dbee.Vid','VidDesc' => 'dbee.VidDesc','VidSite' => 'dbee.VidSite','VidID' => 'dbee.VidID','VidTitle' => 'dbee.VidTitle','Audio' => 'dbee.Audio','PollText' => 'dbee.PollText','twitter' => 'dbee.TwitterTag','DbTag' => 'dbee.DbTag','redbee'=>'dbee.ReDBUsers','PostDate'=>'dbee.PostDate' ,'Active'=>'dbee.Active','User'=>'dbee.User','GroupID'=>'dbee.GroupID','Comments'=>'dbee.Comments'));
		$select->join( array('u' => 'tblUsers'), 'u.UserID=dbee.User', array( 'UserID' => 'u.UserID','username' => 'u.Name','lname' => 'u.lname','image' =>'u.ProfilePic') );
		$select->joinLeft( array('g' => 'tblGroups'), 'g.ID=dbee.GroupID', array('g.GroupName') );
		$select->joinLeft( array('cmm' => 'tblDbeeComments'), 'cmm.DbeeID=dbee.DbeeID', array('total' => new Zend_Db_Expr('count(cmm.DbeeID)')) );
		$select->setIntegrityCheck( false );		
		//if(!empty($dbeeID)){ $select->where('dbee.Type = '.$dbeeID.''); } // for searching 	
		if(!empty($user)){
			$select->where('dbee.User = '.$user.'');
		} // for searching
		$select->where("dbee.clientID = ?", clientID);
		$select->where('dbee.Type != 15');
		$select->where('dbee.Type != 6');
		
		if($dbeeID!="")
		{
		 $select->where('dbee.DbeeID = '.$dbeeID.'');
		}
		$select->where('dbee.Type != 6');
		$select->where('dbee.Type != 7');
		//$select->order('dbee.LastActivity DESC');
		$select->group('cmm.DbeeID');
		$select->order('total DESC');		
		if($limit!='all')
		$select->LIMIT($limit, 0);	
		//echo $select->__toString();die;	
		$result= $db->fetchAll($select);		
		return $result;				
    }

	public function getLiveGroup($limit=2,$user='',$calling='',$GroupPrivacy='') {		
		
		$db = $this->getDbTable();
		
		$select = $db->select();
		
		$select->setIntegrityCheck( false );
		
		$select->from( array('group' => 'tblGroups'),  array('ID'=>'group.ID','GroupName'=>'group.GroupName','GroupPic'=>'group.GroupPic','GroupPrivacy'=>'group.GroupPrivacy','description' => 'group.GroupDesc','type' => 'group.GroupType', 'Gdate' => 'group.GroupDate','GroupTypeOther' => 'group.GroupTypeOther','User' => 'group.User','GroupRes'=>'group.GroupRes','Invitetoexpert'=>'group.Invitetoexpert'));
		
		if($calling!="VIPGROUP")
		{
			$select->joinLeft( array('gt' => 'tblGroupTypes'), 'gt.TypeID = group.GroupType', array('TypeName' =>'gt.TypeName'));
		}
		$select->joinLeft( array('gm' => 'tblGroupMembers'), 'gm.GroupID=group.ID',array('memcnt' =>'COUNT(DISTINCT(gm.GroupID))'));
		$select->joinLeft( array('db' => 'tblDbees'), 'db.GroupID = group.ID', array( 'dbcnt' => 'COUNT(DISTINCT(db.DbeeID))'));
		$select->join( array('u' => 'tblUsers'), 'u.UserID=group.User', array( 'UserID' => 'u.UserID','username' => 'u.Name','lname' => 'u.lname','image' =>'u.ProfilePic') );
	  	$select->where('u.Status = ?', '1');
	  	$select->where("group.clientID = ?", clientID);	
	  	$select->where("u.clientID = ?", clientID);
	  	if(!empty($user)){
	  		$select->where('group.User = '.$user.'');
	  	}	
	  	if($calling=="VIPGROUP")
		{
			$select->where('group.GroupPrivacy = '.$GroupPrivacy.'');
		}
		$select->group('group.ID');
		$select->order('group.GroupDate DESC');
		if($limit!='all')
		$select->LIMIT($limit, 0);		
		//echo $select->__toString();
		$result= $db->fetchAll($select);	
		return $result;		
    }

    public function getTopactivegroup($inLimit='',$lastLimit=''){
         $db = $this->getDbTable();
         $sql .="SELECT T . * , sum( T.Cnt ) AS 'totalcount'
				FROM (
				SELECT tg.ID, tg.GroupName, tg.GroupPic, tg.GroupPrivacy,tg.GroupDesc, tg.GroupDate, tg.User, tu.Name, tu.lname, tu.ProfilePic, tb.GroupID, tb.DbeeID, count( * ) AS 'Cnt'
				FROM tblGroups AS tg
				JOIN tblUsers AS tu ON tg.User = tu.UserID
				JOIN tblDbees AS tb ON tg.ID = tb.GroupID
				WHERE tg.clientID = ".clientID."
				GROUP BY tb.GroupID
				UNION
				SELECT tg.ID, tg.GroupName, tg.GroupPic, tg.GroupPrivacy,tg.GroupDesc, tg.GroupDate, tg.User, tu.Name, tu.lname, tu.ProfilePic, tb.GroupID, tb.DbeeID, count( * ) AS 'Cnt'
				FROM tblGroups AS tg
				JOIN tblUsers AS tu ON tg.User = tu.UserID
				JOIN tblDbees AS tb ON tg.ID = tb.GroupID
				JOIN tblDbeeComments AS tc ON tc.DbeeID = tb.DbeeID
				WHERE tb.GroupID != '0' AND tg.clientID = ".clientID."
				GROUP BY tb.GroupID
				) AS T
				GROUP BY T.GroupID
				ORDER BY totalcount DESC";
				if($lastLimit){
				$sql .=	" LIMIT ".$inLimit." , ".$lastLimit."";
			   }
				
          $retData = $this->_db->query($sql)->fetchAll();
		  return $retData;
    }
	
	// get comment by user
	public function getComment($CommentID,$UserId='',$type='') 
	{
		$db = $this->getDbTable();
		$selectB = $db->select()->setIntegrityCheck( false )->from(array('commt' => 'tblDbeeComments'), 'commt.Comment');
	    $selectB->where('commt.CommentID = '.$CommentID.'');
	    if($UserId!='')
		$selectB->where('commt.DbeeOwner = '.$UserId.'');
		$selectB->where("commt.clientID = ?", clientID);
		$selectB->order('commt.CommentDate DESC');
		$selectB->LIMIT(1, 0);
		//echo $selectB->__toString(); die;
		 try{
            $result = $db->fetchAll($selectB);
			$comment = $result[0]['Comment'];
	        }catch(Exception $exc){
			   $comment = '';
	        }
			return $comment;	
	}
	
	
	// get latest comment on dbee
	public function getLatestComment($limit='2',$cmtType='',$user='') {	
		
		$db = $this->getDbTable();
		$selectB = $db->select()->setIntegrityCheck( false )->from(array('commt' => 'tblDbeeComments'), array('CommentID'=>'commt.CommentID','DbeeID'=>'commt.DbeeID','type' => 'commt.Type','Comment' => 'commt.Comment', 'Link' => 'commt.Link','LinkTitle' => 'commt.LinkTitle','LinkDesc' => 'commt.LinkDesc','UserLinkDesc' => 'commt.UserLinkDesc','Pic' => 'commt.Pic','PicDesc' => 'commt.PicDesc','Vid' => 'commt.Vid','VidDesc' => 'commt.VidDesc','VidSite' => 'commt.VidSite','VidID' => 'commt.VidID','Audio' => 'commt.Audio','CommentDate' => 'commt.CommentDate','CommentDate' => 'commt.CommentDate'));
		$selectB->join( array('u' => 'tblUsers'), 'u.UserID=commt.UserID', array( 'UserID' => 'u.UserID','username' => 'u.Name','lname' => 'u.lname','image' =>'u.ProfilePic') );
		$selectB->join( array('owner' => 'tblUsers'), 'owner.UserID=commt.DbeeOwner', array( 'OwnerID' => 'owner.UserID','Ownername' => 'owner.Name','Ownerlname' => 'owner.lname','Ownerimage' =>'owner.ProfilePic') );
		$selectB->join( array('dbee' => 'tblDbees'), 'dbee.DbeeID = commt.DbeeID',  array('DbeeID','dburl'));
	    $selectB->where('u.Status = ?', '1');	
	    if($user!='')
	    	$selectB->where('commt.UserID = '.$user.'');	   
		if($cmtType!='')
		$selectB->where('commt.Type = '.$cmtType.'');
		$selectB->where("commt.clientID = ?", clientID);
		$selectB->where("dbee.clientID = ?", clientID);	
		$selectB->order('commt.CommentDate DESC');
		if($limit!='all')
		$selectB->LIMIT($limit, 0);	

	//echo $selectB;
	
		 try{
            return $result = $db->fetchAll($selectB);		
        }catch(Exception $exc){
          return 0;
		} 	
	}

	// get latest comment on dbee
	public function getEntityComment($limit='2',$entity_type='',$typefield='') {	
		
		$db = $this->getDbTable();
		$selectB = $db->select()->setIntegrityCheck( false )->from(array('commt' => 'tblDbeeComments'), array('CommentID'=>'commt.CommentID','DbeeID'=>'commt.DbeeID','type' => 'commt.Type','Comment' => 'commt.Comment', 'Link' => 'commt.Link','LinkTitle' => 'commt.LinkTitle','LinkDesc' => 'commt.LinkDesc','UserLinkDesc' => 'commt.UserLinkDesc','Pic' => 'commt.Pic','PicDesc' => 'commt.PicDesc','Vid' => 'commt.Vid','VidDesc' => 'commt.VidDesc','VidSite' => 'commt.VidSite','VidID' => 'commt.VidID','Audio' => 'commt.Audio','CommentDate' => 'commt.CommentDate','CommentDate' => 'commt.CommentDate'));
		
		$selectB->join( array('u' => 'tblUsers'), 'u.UserID=commt.UserID', array( 'UserID' => 'u.UserID','username' => 'u.Name','lname' => 'u.lname','image' =>'u.ProfilePic') );
		
		$selectB->join( array('owner' => 'tblUsers'), 'owner.UserID=commt.DbeeOwner', array( 'OwnerID' => 'owner.UserID','Ownername' => 'owner.Name','Ownerlname' => 'owner.lname','Ownerimage' =>'owner.ProfilePic') );
		
		$selectB->join( array('dbee' => 'tblDbees'), 'dbee.DbeeID = commt.DbeeID',  array('DbeeID','dburl'));
		
		$selectB->join( array('sematira' => 'tblsematira'), 'sematira.commnetid = commt.CommentID',  array( 'entity_type','sentiment_score','sentiment_polarity'));
	    
	    $selectB->where("sematira.entity_title = ?", $entity_type);
	    if($typefield!='') $selectB->where("sematira.sentiment_polarity = ?", $typefield);

	    $selectB->where("sematira.entity_type != ?", 'Quote');
	    $selectB->where("sematira.entity_type != ?", 'Regex');

		$selectB->where("commt.clientID = ?", clientID);
		$selectB->order('commt.CommentDate DESC');
		if($limit!='all')
		$selectB->LIMIT($limit, 0);	
		
		//echo $selectB; exit;
		try{
            return $result = $db->fetchAll($selectB);		
        }catch(Exception $exc){
          return 0;
		} 	
	}

	public function getConfigurations()
    {
    $select = $this->_db->select();
    $select->from('tblConfiguration')->where('clientID = ?', clientID);
    return $this->_db->fetchRow($select);
    }
	
	
	// get live score
	public function getLiveScore($limit='',$dbeeID='',$UserId='',$type='') {
		$db = $this->getDbTable();
	
		$selectB = $db->select()->setIntegrityCheck( false )->from(array('score' => 'tblScoring'), array('type' => 'score.Type','Score' => 'score.Score','ScoreID' => 'score.ScoreID','ID'=>'score.ID','ScoreDate'=>'score.ScoreDate'));
		$selectB->joinleft( array('u' => 'tblUsers'), 'u.UserID=score.UserID', array( 'UserID' => 'u.UserID','username' => 'u.Name','lname' => 'u.lname','image' =>'u.ProfilePic') );
		$selectB->join( array('owner' => 'tblUsers'), 'owner.UserID=score.Owner', array( 'OwnerID' => 'owner.UserID','Ownername' => 'owner.Name','Ownerlname' => 'owner.lname','Ownerimage' =>'owner.ProfilePic') );
		$selectB->where('u.Status = ?', '1');
		$selectB->where("u.clientID = ?", clientID);
		$selectB->where("score.clientID = ?", clientID);		
		//  $selectB->where('owner.Status = ?', '1');
		if($dbeeID!='')
			$selectB->where('score.DbeeID = '.$dbeeID.'');
		if($UserId!='')
			$selectB->where('score.UserID = '.$UserId.'');
		
		$selectB->order('ScoreDate DESC ');
	
		if($limit!='all')
			$selectB->LIMIT($limit, 0);
		//echo $selectB->__toString();
		//die;
		try{
			$result = $db->fetchAll($selectB);
		}catch(Exception $exc){
			$result = '';
		}
		return $result;
	}

	public function getMentioninfo($type,$ScoreID)
	{

		$db = $this->getDbTable();
		$selectB = $db->select()->setIntegrityCheck( false )->from(array('mention' => 'tblactivity'), array('type' => 'mention.act_type','Score' => 'mention.act_type','ScoreID' => 'mention.act_id','Cmnt_ID'=>'mention.act_cmnt_id','db_ID'=>'mention.act_typeId'));
		
		if($type==2)
		{
			$selectB->joinleft( array('commt' => 'tblDbeeComments'), 'commt.CommentID=mention.act_cmnt_id', array('DbeeID'=>'commt.DbeeID','type' => 'commt.Type','Comment' => 'commt.Comment', 'Link' => 'commt.Link','LinkTitle' => 'commt.LinkTitle','LinkDesc' => 'commt.LinkDesc','UserLinkDesc' => 'commt.UserLinkDesc','Pic' => 'commt.Pic','PicDesc' => 'commt.PicDesc','Vid' => 'commt.Vid','VidDesc' => 'commt.VidDesc','VidSite' => 'commt.VidSite','VidID' => 'commt.VidID','Audio' => 'commt.Audio','CommentDate' => 'commt.CommentDate')); 
			$selectB->where('commt.CommentID = '.$ScoreID.'');
			$selectB->where("commt.clientID = ?", clientID);
		}
		else if($type==1)
		{
			$selectB->joinleft( array('dbee' => 'tblDbees'), 'dbee.DbeeID=mention.act_typeId', array('DbeeID'=>'dbee.DbeeID','type' => 'dbee.Type','description' => 'dbee.Text', 'Link' => 'dbee.Link','LinkTitle' => 'dbee.LinkTitle','LinkDesc' => 'dbee.LinkDesc','UserLinkDesc' => 'dbee.UserLinkDesc','Pic' => 'dbee.Pic','PicDesc' => 'dbee.PicDesc','Vid' => 'dbee.Vid','VidDesc' => 'dbee.VidDesc','VidSite' => 'dbee.VidSite','VidID' => 'dbee.VidID','Audio' => 'dbee.Audio','PollText' => 'dbee.PollText'));
			$selectB->where('dbee.dbeeID = '.$ScoreID.'');
			$selectB->where("dbee.clientID = ?", clientID);
		}
		$selectB->where("mention.clientID = ?", clientID);		
		 try{
            $result = $db->fetchAll($selectB);			
        }catch(Exception $exc){
             $result= '';
        }
		return $result->toarray();	
	}
	
	public function getScoredbinfo($type,$ScoreID)
	{

		$db = $this->getDbTable();
		$selectB = $db->select()->setIntegrityCheck( false )->from(array('score' => 'tblScoring'), array('type' => 'score.Type','Score' => 'score.Score','ScoreID' => 'score.ScoreID','ID'=>'score.ID'));
		
		if($type==2)
		{
			$selectB->joinleft( array('commt' => 'tblDbeeComments'), 'commt.CommentID=score.ID', array('DbeeID'=>'commt.DbeeID','type' => 'commt.Type','Comment' => 'commt.Comment', 'Link' => 'commt.Link','LinkTitle' => 'commt.LinkTitle','LinkDesc' => 'commt.LinkDesc','UserLinkDesc' => 'commt.UserLinkDesc','Pic' => 'commt.Pic','PicDesc' => 'commt.PicDesc','Vid' => 'commt.Vid','VidDesc' => 'commt.VidDesc','VidSite' => 'commt.VidSite','VidID' => 'commt.VidID','Audio' => 'commt.Audio','CommentDate' => 'commt.CommentDate'));
			$selectB->where("commt.clientID = ?", clientID);
		}
		else if($type==1)
		{
			$selectB->joinleft( array('dbee' => 'tblDbees'), 'dbee.DbeeID=score.ID', array('DbeeID'=>'dbee.DbeeID','type' => 'dbee.Type','dburl' => 'dbee.dburl','description' => 'dbee.Text', 'Link' => 'dbee.Link','LinkTitle' => 'dbee.LinkTitle','LinkDesc' => 'dbee.LinkDesc','UserLinkDesc' => 'dbee.UserLinkDesc','Pic' => 'dbee.Pic','PicDesc' => 'dbee.PicDesc','Vid' => 'dbee.Vid','VidDesc' => 'dbee.VidDesc','VidSite' => 'dbee.VidSite','VidID' => 'dbee.VidID','Audio' => 'dbee.Audio','PollText' => 'dbee.PollText'));
			$selectB->where("dbee.clientID = ?", clientID);
		}
	  $selectB->where('score.ScoreID = '.$ScoreID.'');
	  $selectB->where("score.clientID = ?", clientID);
		 try{
            $result = $db->fetchAll($selectB);			
        }catch(Exception $exc){
             $result= '';
        }
		return $result->toarray();	
	}
	
	// get total live dbee's

	public function getTotalComments($dbeeid) {
		$db = $this->getDbTable();
		$selectB = $db->select()->setIntegrityCheck( false )->from(array('commt' => 'tblDbeeComments'), array('total' =>  new Zend_Db_Expr('count(commt.CommentID)')));
		$selectB->where("commt.DbeeID = ?",$dbeeid);
		$selectB->where("commt.clientID = ?", clientID);
		
		 try{
	            $result = $db->fetchAll($selectB);
				$total= $result[0]['total'];
	        }catch(Exception $exc){
	         
			   $total = '';
	        }	
	
		return $total;	
	}


	public function insertNotification($returnres){
		$data = array('User' => $returnres );
		$insertdb = $this->_db->insert('tblNotificationSettings', $data);
        if($insertdb) return $this->_db->lastInsertId();
      	else return false; 
	}

	public function profanityfilter($searchfield,$orderfield) 
	{
		$db = $this->getDbTable();		
		$select = $db->select();
		$select->setIntegrityCheck( false );
		$select->from( array('at' => 'tblProfanityFilter'));
		$select->where('at.status = ?', 1);
		$select->where('at.clientID = ?', clientID);
		if($searchfield!='')
                $select->where('name LIKE "%'.$searchfield.'%"');
		
        if($orderfield!='')
        {
            if ($orderfield=='1') 
               $select->order('name ASC');
            else if ($orderfield=='2')
                $select->order('name DESC');
           
        } 
        //echo $select->__toString();
        
		return $db->fetchAll($select);
	}


	public function getTotalDbee() {
		$db = $this->getDbTable();
		$selectB = $db->select()->setIntegrityCheck( false )->from(array('dbee' => 'tblDbees'), array('total' =>  new Zend_Db_Expr('count(dbee.DbeeID)')));
		$selectB->where("dbee.type <> ''");
		$selectB->where("dbee.clientID = ?", clientID);
		
		 try{
	            $result = $db->fetchAll($selectB);
				$total= $result[0]['total'];
	        }catch(Exception $exc){	        
			   $total = '';
	        }	
	
		return $total;	
	}
	
	
	// get total live groups
	public function getTotalGroup() 
	{
		$db = $this->getDbTable();
		$selectB = $db->select()->setIntegrityCheck( false )->from(array('group' => 'tblGroups'), array('total' =>  new Zend_Db_Expr('count(group.ID)')));
		$selectB->join( array('gt' => 'tblGroupTypes'), 'gt.TypeID = group.GroupType');
		$selectB->join( array('u' => 'tblUsers'), 'u.UserID=group.User' );

		$selectB->where("group.User <> ''");
		$selectB->where("group.clientID = ?", clientID);
		
		 try{
	            $result = $db->fetchAll($selectB);
				$total= $result[0]['total'];
        }catch(Exception $exc){
        
		   $total = '';
        }	
	
	return $total;	
	}


	public function getusersscorewithtype($userid,$whr)
	{
		//echo $whr;
		$db  = $this->getDbTable();
		$sel = $db->select()->setIntegrityCheck( false )->from(array('scor' => 'tblScoring'), array('Type'=>'scor.Score','score' =>  new Zend_Db_Expr('count(scor.ScoreID)')));		
		
		if($whr == 'onme')
			$sel->where('scor.Owner ='.$userid);
		else if ($whr == 'byme')
			$sel->where('scor.UserID ='.$userid);
		$sel->where("scor.Score != ?", 3);
		$sel->where("scor.clientID = ?", clientID);	
		$sel->group('Score');
		return $getchart	= $db->fetchAll($sel)->toarray();
		
	}

  

	public function getGroupPrivacylistgroup($groupPrivacy) {			
		$db = $this->getDbTable();	
		$select = $db->select();		
		$select->setIntegrityCheck( false );
		$select->from( array('group' => 'tblGroups'),  array('ID'=>'group.ID','GroupName'=>'group.GroupName','GroupPic'=>'group.GroupPic','description' => 'group.GroupDesc','type' => 'group.GroupType', 'Gdate' => 'group.GroupDate',
           'GroupTypeOther' => 'group.GroupTypeOther','GroupPrivacy' => 'group.GroupPrivacy'));
		$select->join( array('gm' => 'tblGroupMembers'), 'gm.GroupID=group.ID',array( 'cnt' => 'COUNT(DISTINCT(gm.User))'));
		$select->join( array('u' => 'tblUsers'), 'u.UserID=group.User', array( 'UserID' => 'u.UserID','username' => 'u.Name','image' =>'u.ProfilePic'));
		$select->where("group.clientID = ?", clientID);
	  	$select->where('u.Status = ?', '1');
	  	$select->where('group.GroupPrivacy = ?', $groupPrivacy);
	  	$select->where('u.clientID = ?', clientID);
	  	$select->group('group.ID');

		$select->order('group.GroupDate DESC');
		//echo $select->__toString();exit;
		$result= $db->fetchAll($select);

		return $result->toarray();		
    } 

    public function  insertgroup_msgdata($datamsg){
    	
    	$table ='tblMessages';
		$insertdb = $this->_db->insert($table, $datamsg);
		$id = $this->_db->lastInsertId();		
		if($datamsg['Chainparent']==0){		
			$data2 = array('ChainParent'=> $id);
			$this->_db->update($table,$data2,'ID = '.$id);
		}
        return $id;
      	//else return false;	
	} 

	public function insertgroup_msgdetail($msgdetail){
		//echo'<pre>';print_r($msgdetail);die('---in model---');
	    $table ='adminmsgemaildetail';
		$insertdb = $this->_db->insert($table, $msgdetail);
        if($insertdb){return $this->_db->lastInsertId();}
      	else{return false;}	
	}

	public function countmsgemaildetail($id)
	{
		
		$db = $this->getDbTable();
		$Offset1 =(int)$Offset;
		$select = $db->select()->setIntegrityCheck( false );
		$select->from(array('A'=>'adminmsgemaildetail'))
		->where('A.msgemailtable_id= ?',$id)->where('A.clientID = ?', clientID);
		return count($this->getDefaultAdapter()->query($select)->fetchAll());
	}

	public function msgemailuseriddetail($msgemailtable_id)
	{
       	$db = $this->getDbTable();	
		$getuserdetail 	= 	$db->select();
		$getuserdetail->setIntegrityCheck( false );
		$getuserdetail->from(array('u' => 'tblUsers'),array('u.*'));
		$getuserdetail->join(array('A' => 'adminmsgemaildetail'),'A.MessageTo = u.UserID');
		$getuserdetail->where('A.msgemailtable_id= ?',$msgemailtable_id);
		$getuserdetail->where('u.status = ?', 1);
        $getuserdetail->where('u.clientID = ?', clientID);
		//echo $getuserdetail->__toString();exit;
		$getuserdetail	= $db->fetchAll($getuserdetail)->toarray();
        return $getuserdetail;


	}

	public function searchcomusers($param)
	{
		$this->myclientdetails = new Admin_Model_Clientdetails();
        $param = $this->myclientdetails->customEncoding($param,$search="true");
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();       
        //$SQL = "SELECT * FROM tblUsers WHERE Name REGEXP '^$param' OR lname REGEXP '^$param' AND  clientID='".clientID."'"; 
       $select = $this->_db->select()
		->from(array('u'=>'tblUsers'),	array('UserID','Name','ProfilePic','Email'))
		->where("Name LIKE '%$param%' OR lname LIKE '%$param%'")
		->where('u.clientID = ?', clientID);
		$query = $this->_db->fetchAll($select);

        //$query = $db->fetchAll($SQL);
        for ($x = 0, $numrows = count($query); $x < $numrows; $x++) 
        { 
          $defaultimagecheck = new Admin_Model_Common();
          $userpic = $defaultimagecheck->checkImgExist($query[$x]["ProfilePic"],'userpics','default-avatar.jpg');	
		  $friends[$x] = array(
		  "id" => $query[$x]["UserID"],"name" => $this->myclientdetails->customDecoding($query[$x]["Name"]),
		  "email" => $this->myclientdetails->customDecoding($query[$x]["Email"]),
          "url" =>  IMGPATH.'/users/small/'.$userpic
		  	);			
	    } 
	   $response = json_encode($friends);           
       return $response;
	}
    public function insertgroup_msg($data){    	
    	$table ='adminmessageemail';
		$insertdb = $this->_db->insert($table, $data);
        if($insertdb){return $this->_db->lastInsertId();}
      	else{return false;} 
	}

    public function getGroupemailtemplaterole($areatype='admin'){
		$db = $this->getDbTable();		
		$getemailtemplate 	= 	$db->select();
		$getemailtemplate->setIntegrityCheck( false );
		$getemailtemplate->from(array('emtemp' => 'emailtemplates'));
		$getemailtemplate->where('emtemp.case = ?','45');
	    $getemailtemplate->where("emtemp.areatype='$areatype'");
	    $getemailtemplate->where('emtemp.clientID = ?', clientID);
	    $getemailtemplate->where('emtemp.active = ?','1');
		$getemailtemplate	= $db->fetchAll($getemailtemplate)->toarray();
		return $getemailtemplate;		
	}

    public function getGroupemailtemplateroledel($areatype='admin'){
		$db = $this->getDbTable();		
		$getemailtemplate 	= 	$db->select();
		$getemailtemplate->setIntegrityCheck( false );
		$getemailtemplate->from(array('emtemp' => 'emailtemplates'));
		$getemailtemplate->where('emtemp.case = ?','46');
	    $getemailtemplate->where("emtemp.areatype='$areatype'");
	    $getemailtemplate->where('emtemp.clientID = ?', clientID);
	    $getemailtemplate->where('emtemp.active = ?','1');
		$getemailtemplate	= $db->fetchAll($getemailtemplate)->toarray();
		return $getemailtemplate;		
	}

	public function getGroupemailtemplate($areatype='admin')
	{
		$db = $this->getDbTable();		
		$getemailtemplate 	= 	$db->select();
		$getemailtemplate->setIntegrityCheck( false );
		$getemailtemplate->from(array('emtemp' => 'emailtemplates'));
	    $getemailtemplate->where("emtemp.areatype='$areatype'");
	    $getemailtemplate->where('emtemp.clientID = ?', clientID);	    
	    $getemailtemplate->where('emtemp.active = ?','1'); 
	    $getemailtemplate->order('case asc');
		$getemailtemplate	= $db->fetchAll($getemailtemplate)->toarray();
		return $getemailtemplate;		
	}

	public function getGroupemailtemplateone($keywordid='')
	{
		$db = $this->getDbTable();		
		$getemailtemplate 	= 	$db->select();
		$getemailtemplate->setIntegrityCheck( false );
		$getemailtemplate->from(array('emtemp' => 'emailtemplates'));
		if($keywordid)
			$getemailtemplate->where("emtemp.id='$keywordid'");
		$getemailtemplate->where('emtemp.clientID = ?', clientID);	    
		$getemailtemplate	= $db->fetchAll($getemailtemplate);
		return $getemailtemplate;		
	}

	public function getusersdetailData($sendmsgval='',$limit='',$offset='0',$usertype='',$company='',$title='')  
	{
		
		$myclientdetails = new Admin_Model_Clientdetails();
		$company = $myclientdetails->customEncoding($company);	
		$title = $myclientdetails->customEncoding($title);

		$db = $this->getDbTable();	
		$getuserdetail 	= 	$db->select();
		$getuserdetail->setIntegrityCheck( false );
		$getuserdetail->from(array('usersdetail' => 'tblUsers'),array('usersdetail.Name','usersdetail.lname','usersdetail.Email','usersdetail.UserID','usersdetail.Username'));
		$getuserdetail->where('usersdetail.Status = ?', '1');
		if($usertype!='')
		$getuserdetail->where('usersdetail.usertype IN(?)', $usertype);
		if($company!='')
		$getuserdetail->where('usersdetail.company LIKE "%'.$company.'%"');
		if($title!='')
		$getuserdetail->where('usersdetail.title LIKE "%'.$title.'%"');
        $getuserdetail->where('usersdetail.clientID = ?', clientID);
		if($sendmsgval==1) // case of email sending
		{
			if($limit!='' )
			$getuserdetail->limit($limit,$offset);	
		}
		//echo $getuserdetail->__toString();die;
		$getuserdetail	= $db->fetchAll($getuserdetail)->toarray();
        return $getuserdetail;
	}


	public function getadmingroupdetailData($typeid,$sendmsgval='',$limit='',$offset='0')  
	{
		$db = $this->getDbTable();	
		$getuserdetail 	= 	$db->select();
		$getuserdetail->setIntegrityCheck( false );

		$getuserdetail->from(array('u' => 'tblUsers'),array('u.Name','u.lname','u.Email','u.UserID','u.Username'));
		$getuserdetail->join(array('group' => 'usersgroupid'),'group.userid = u.UserID');
		$getuserdetail->where('u.Status = ?', '1');
		$getuserdetail->where('group.ugid = ?', $typeid);
        $getuserdetail->where('u.clientID = ?', clientID);
		if($sendmsgval==1) // case of email sending
		{
			if($limit!='' )
			$getuserdetail->limit($limit,$offset);	
		}
		//echo $getuserdetail->__toString();exit;
		$getuserdetail	= $db->fetchAll($getuserdetail)->toarray();
        return $getuserdetail;
	}

	public function getusersgroupdetailData($typeid,$sendmsgval='',$limit='',$offset='0')  
	{
		$db = $this->getDbTable();	
		$getuserdetail 	= 	$db->select();
		$getuserdetail->setIntegrityCheck( false );

		$getuserdetail->from(array('u' => 'tblUsers'),array('u.Name','u.lname','u.Email','u.UserID','u.Username'));
		$getuserdetail->join(array('group' => 'tblGroupMembers'),'group.User = u.UserID');
		$getuserdetail->where('u.Status = ?', '1');
		$getuserdetail->where('group.GroupID = ?', $typeid);
		$getuserdetail->where('u.clientID = ?', clientID);
		if($sendmsgval==1) // case of email sending
		{
			if($limit!='' )
			$getuserdetail->limit($limit,$offset);	
		}
		//echo $getuserdetail->__toString();exit;
		$getuserdetail	= $db->fetchAll($getuserdetail)->toarray();
        return $getuserdetail;
	}
	
	public function selectresend($msgemailtable_id){
		$db = $this->getDbTable();		
		$select = $db->select();
		$select->setIntegrityCheck( false );
		$select->from( array('amd' => 'adminmsgemaildetail'));		
		$select->where('amd.msgemailtable_id = ?', $msgemailtable_id);
		$select->where('amd.clientID = ?', clientID);
		$result= $db->fetchAll($select)->toarray();
		return $result;		
	}

	public function listsiteusergroup(){	
		$select = $this->_db->select();				
		$select->from(array('g'=>'usersgroup'));
		$select->joinLeft(array('gu' => 'usersgroupid'), 'g.ugid = gu.ugid', array('id',"cnt" => "COUNT(DISTINCT(gu.id))"));
		$select->where('gu.clientID = ?', clientID);
		$select->group('g.ugid');
		return $result = $this->_db->fetchAll($select);			
    }

    public function listsiteusergroupmsg(){	
		$select = $this->_db->select();				
		$select->from(array('g'=>'usersgroup'));
		$select->joinInner(array('gu' => 'usersgroupid'), 'g.ugid = gu.ugid', array('id',"cnt" => "COUNT(DISTINCT(gu.id))"));
		$select->where('gu.clientID = ?', clientID);
		$select->group('g.ugid');	
		return $result = $this->_db->fetchAll($select);			
    }
    
   
    public function alluserslist($msg_type, $emailtemplateid){
    	    if($msg_type==1){
	    		if($emailtemplateid!=0) 
	    		                      {$condi = "admsgem.emailtemplateid != 0";}	 
	    		else{$condi = "admsgem.emailtemplateid = 0";}
	           	$select = $this->_db->select();
	           	$select->from( array('admsgem'=>'adminmessageemail',array('msg_subject','msg_body','msg_postdate','msg_editdate','emailtemplateid')));
			    $select->where("admsgem.msg_type = ?", $msg_type)->where($condi)->where("admsgem.status = ?", '0')->where('admsgem.clientID = ?', clientID);	
			    $select->order('admsgem.msg_id DESC');
            }
            if($msg_type==2){
	            if($emailtemplateid!=0) 
	    		                      {$condi = "admsgem.emailtemplateid != 0";}	 
	    		else{$condi = "admsgem.emailtemplateid = 0";}
	           	$select = $this->_db->select();
	           	$select->from( array('admsgem'=>'adminmessageemail',array('msg_subject','msg_body','msg_postdate','msg_editdate','emailtemplateid','tug.ugname')));
	           	$select->join( array('tug' => 'usersgroup'),'tug.ugid=admsgem.msg_type_id');
			    $select->where("admsgem.msg_type = ?", $msg_type)->where($condi)->where("admsgem.status = ?", '0')->where('admsgem.clientID = ?', clientID);	
			    $select->order('admsgem.msg_id DESC');	
            }
            if($msg_type==3){
	            if($emailtemplateid!=0) 
	    		                      {$condi = "admsgem.emailtemplateid != 0";}	 
	    		else{$condi = "admsgem.emailtemplateid = 0";}
	           	$select = $this->_db->select();
	           	$select->from( array('admsgem'=>'adminmessageemail',array('msg_subject','msg_body','msg_postdate','msg_editdate','emailtemplateid','tg.GroupName')));
	           	$select->join( array('tg' => 'tblGroups'),'tg.ID=admsgem.msg_type_id');
			    $select->where("admsgem.msg_type = ?", $msg_type)->where($condi)->where("admsgem.status = ?", '0')->where('admsgem.clientID = ?', clientID);	
			    $select->order('admsgem.msg_id DESC');	
            }
            if($msg_type==4){
	    		if($emailtemplateid!=0) 
	    		                      {$condi = "admsgem.emailtemplateid != 0";}	 
	    		else{$condi = "admsgem.emailtemplateid = 0";}
	           	$select = $this->_db->select();
	           	$select->from( array('admsgem'=>'adminmessageemail',array('msg_subject','msg_body','msg_postdate','msg_editdate','emailtemplateid')));
			    $select->where("admsgem.msg_type = ?", $msg_type)->where($condi)->where("admsgem.status = ?", '0')->where('admsgem.clientID = ?', clientID);	
			    $select->order('admsgem.msg_id DESC');
            }
            if($msg_type==5){
	    		if($emailtemplateid!=0) 
	    		                      {$condi = "admsgem.emailtemplateid != 0";}	 
	    		else{$condi = "admsgem.emailtemplateid = 0";}
	           	$select = $this->_db->select();
	           	$select->from( array('admsgem'=>'adminmessageemail',array('msg_subject','msg_body','msg_postdate','msg_editdate','emailtemplateid')));
			    $select->where("admsgem.msg_type = ?", $msg_type)->where($condi)->where("admsgem.status = ?", '0')->where('admsgem.clientID = ?', clientID);	
			    $select->order('admsgem.msg_id DESC');
            }
		    //echo $select->__toString();die;           
    	    return $result = $this->_db->fetchAll($select);
    }

	

	public function inviteuser($dbeeid){
		$db = $this->getDbTable();		
		$select = $db->select();
		$select->setIntegrityCheck( false );
		$select->from( array('sp' => 'tblspecialdbinvite'));		
		$select->where('sp.dbeeid = ?', $dbeeid);
		$select->where('sp.clientID = ?',clientID );
		$result= $db->fetchAll($select);
		return count($result);		
	}
	
	public function attendiesuser($dbeeid,$type)
	{
		$db = $this->getDbTable();		
		$select = $db->select();
		$select->setIntegrityCheck( false );
		$select->from( array('at' => 'tblDbeeJoinedUser'));
		$select->where('at.dbeeid = ?', $dbeeid);
		$select->where('at.status = ?', $type);
		$select->where('at.clientID = ?', clientID);
		$select->where('at.dbeeID = ?', $dbeeid);
		return $db->fetchAll($select);
	}
	
	
	public function inviteuserlist($dbeeid){
		$db = $this->getDbTable();		
		$select = $db->select();
		$select->setIntegrityCheck( false );
		$select->from( array('sp' => 'tblspecialdbinvite'),array("dbeeidcnt" => "COUNT(DISTINCT(sp.dbeeid))"));
		$select->join( array('c' => 'tblDbees'),'c.DbeeID=sp.dbeeid');		
		$select->where('sp.dbeeid = ?', $dbeeid);		
		$select->where('sp.clientID = ?', clientID);		
		$result= $db->fetchAll($select);
		return $result['dbeeidcnt'];
	}
	
	public function inviteusergroupby($dbeeid){
		$db = $this->getDbTable();		
		$select = $db->select();
		$select->setIntegrityCheck( false );
		$select->from( array('sp' => 'tblspecialdbinvite'),array("dbeeidcnt" => "COUNT('sp.id')",'sp.type'));		
		$select->where('sp.dbeeid = ?', $dbeeid);
		$select->where('sp.clientID = ?', clientID);
		$select->group('sp.type');		
		$result= $db->fetchAll($select);
		return $result;
	}
	
	public function attendiesuserlist($dbeeid,$type)
	{
		$db = $this->getDbTable();
		$select = $db->select();
		$select->setIntegrityCheck( false );
		$select->from( array('at' => 'tblDbeeJoinedUser'));
		$select->join( array('c' => 'tblDbees'),'c.DbeeID=at.dbeeID');
		$select->join( array('u' => 'tblUsers'), 'u.UserID=at.userID');
		$select->where('at.status = ?', $type);
		$select->where('at.clientID = ?', clientID);
		$select->where('at.dbeeID = ?', $dbeeid);		
		$result= $db->fetchAll($select);
		return $result;
	}
	
	public function updatejoinrequser($data,$userid,$dbeeID)
	{
		$this->_db->update('tblDbeeJoinedUser', $data ,array(
			"userID='" . $userid . "'","dbeeID='" . $dbeeID . "'","clientID='" . clientID . "'"));
	}
	public function upadategroupval($data,$userid,$groupid)
	{

	$this->_db->update('tblGroupMembers', $data ,array(
			"User='" . $userid . "'","GroupID='" . $groupid . "'","clientID='" . clientID . "'"));
	}
	public function selectgroup($GroupID)
    {
        
        $select = $this->_db->select();
        
        $select->from(array(
            'g' => 'tblGroups'
        ));
        
        $select->where("g.ID =?", $GroupID);
        $select->where("g.clientID = ?", clientID);	
        $result = $this->_db->fetchRow($select);
        
        return $result;
        
    }
	public function deleteGroupMembers($user, $group)
    {
        $this->_db->delete('tblGroupMembers', array(
            'User= ?' => $group,
            'GroupID= ?' => $user,
            'clientID=?'=> clientID
        ));       
        return true;
    }
	public function chkdbeetitle($title)
	{
		$db = $this->getDbTable();
		$select = $db->select();
		$select->setIntegrityCheck( false );
		$select->from('tblDbees',array('DbeeID'));
		$select->where("dburl = ?", $title);
		$select->where("clientID = ?", clientID);		
		$data = $db->fetchAll($select);
	
		if(count($data)>0)
			return false;
		else
			return true;
	}

	public function updateadminglobalsetting($data)
	{	
		$where[] = $this->getAdapter()->quoteInto('clientID = ?', clientID);
	    $insertval = $this->_db->update('tblAdminSettings', $data,$where);
	    return true;
	}

	public function updatesocialloginsetting($data)
	{	
		$where[] = $this->getAdapter()->quoteInto('clientID = ?', clientID);
	    $insertval = $this->_db->update('tblloginsocialresource', $data,$where);
	    return true;
	}

	public function selecttwittertaguser()
	{	
	    $db = $this->getDbTable();
		$select = $db->select();
		$select->setIntegrityCheck( false );
		$select->from('tblTwitterTagUser');
		$select->where("clientID = ?", clientID);	
		$select->order('id ASC');		
		$result = $db->fetchAll($select);
	    return $result->toArray();
	}

	public function insertdata_twittertaguser($table,$data){
		$insertdb = $this->_db->insert($table, $data);
        if($insertdb) return $this->_db->lastInsertId();
      	else return false; 
	}
	 public function deletedata_twittertaguser($UserID)
    {
    	$this->_db->delete('tblTwitterTagUser', array(
            "userID='" . $UserID . "'","clientID='" . clientID . "'"
        ));
        return true;
    }

    public function twittertaguser_totaldetails()
	{
		$db = $this->getDbTable();
		$select = $db->select()->setIntegrityCheck(false);
		$select->distinct('c.DbeeID');
		$select->from(array('tu'=>'tblUsers',array('UserID','Name','lname','ProfilePic')))
		->joInInner(array('tt'=>'tblTwitterTagUser'),'tu.UserID=tt.userID')->where("tt.clientID = ?", clientID)
		->order('tu.Name  ASC');			
		return $this->getDefaultAdapter()->query($select)->fetchAll();
	}
	
	public function updateGlobalLogin()
    {
        $data = array('logout_token' => '');
        $this->_db->update('tblAdminSettings', $data ,array(
            "clientID='" . clientID . "'"
        ));
    }

    public function setGlobalLogin()
    {
    	$cookies = md5(date('Y-m-d H:i:s'));
        $data = array('logout_token' => $cookies);
        $this->_db->update('tblAdminSettings', $data ,array(
            "clientID='" . clientID . "'"
        ));
        setcookie('globalsecuretoken', $cookies, time() + 3600, '/');
    }

    public function deletePostDbee($dbeeid)
    {
    	$this->_db->delete('tblDbees', array(
            "DbeeID='" . $dbeeid . "'", "clientID='" . clientID . "'"
        ));
        return true;
    }

    public function deleteCommentDbee($dbeeid)
    {
    	$this->_db->delete('tblDbeeComments', array(
            "DbeeID='" . $dbeeid . "'", "clientID='" . clientID . "'"
        ));
        return true;
    }

    public function deleteFavDbee($dbeeid)
    {
    	$this->_db->delete('tblFavourites', array(
            "DbeeID='" . $dbeeid . "'", "clientID='" . clientID . "'"
        ));
        return true;
    }

    public function deleteDbeeJoinUserDbee($dbeeid)
    {
    	$this->_db->delete('tblDbeeJoinedUser', array(
            "dbeeID ='" . $dbeeid . "'", "clientID='" . clientID . "'"
        ));
        return true;
    }
	public function deleteDbeeQNADbee($dbeeid)
    {
    	$this->_db->delete('tblDbeeQna', array(
            "dbeeid='" . $dbeeid . "'", "clientID='" . clientID . "'"
        ));
        return true;
    }

    public function deleteDbeeExpert($dbeeid)
    {
    	$this->_db->delete('tblexpert', array(
            "dbid='" . $dbeeid . "'", "clientID='" . clientID . "'"
        ));
        return true;
    }
     public function deleteDbeeLeague($dbeeid)
    {
    	$this->_db->delete('tblLeagueDbee', array(
            "DbeeID='" . $dbeeid . "'", "clientID='" . clientID . "'"
        ));
        return true;
    }

     public function deleteDbeeScoring($dbeeid)
    {
    	$this->_db->delete('tblScoring', array(
            "MainDB='" . $dbeeid . "'", "clientID='" . clientID . "'"
        ));
        return true;
    }
  
    public function getusersdetailbyid($id)
    {
    	$db = $this->getDbTable();
    	$select = $db->select();
    	$select->setIntegrityCheck( false );
    	$select->from( array('j' => 'tblDbeeJoinedUser'))    
    		->joInInner(array('u'=>'tblUsers'),'u.UserID=j.UserID')
    		->where('j.UserID = ?', $id)->where('j.clientID = ?', clientID);    	
    	$result= $db->fetchRow($select);
    	return $result;
    }

    public function getdbeedetailbyid($dbeeid){
    	$db = $this->getDbTable();
    	$select = $db->select();
    	$select->setIntegrityCheck( false );
    	$select->from( array('c' => 'tblDbees'))    	
    	->where('c.DbeeID = ?', $dbeeid)->where('c.clientID = ?', clientID);
    	//echo $select->__toString();
    	$result= $db->fetchRow($select);
    	return $result;
    }


	public function getStaticPage()
	{
	    $db = $this->getDbTable();
		$select = $db->select();
		$select->setIntegrityCheck( false );
		$select->from( array('c' => 'tblStaticPages'))->where('c.clientID = ?', clientID);
		$result= $db->fetchRow($select);
		return $result;
	}

	public function getTempConfiguration()
	{
	    $db = $this->getDbTable();
		$select = $db->select();
		$select->setIntegrityCheck( false );
		$select->from( array('c' => 'tblConfiguration'))->where('clientID = ?',clientID);
		$result= $db->fetchRow($select);
		return json_decode($result['tempContent']);
	}

    public function updateTempToPermConfiguration($check)
    {
    	$db = $this->getDbTable();
		$select = $db->select();
		$select->setIntegrityCheck(false);
		$select->from( array('c' => 'tblConfiguration'))->where('clientID = ?',clientID);
		$result= $db->fetchRow($select);
		$content=$result['tempContent'];
		$content2 = json_decode($result['tempContent']);
		if($check!=''){
			$content='{"SigninText":null,"SigninColor":"","backgroundColor":"#ffffff","highlightsColor":"#ff6d02","highlightsIconColor":"#ffffff", "hideIcons":"0", "SigninImage":"marketingLogo.png","loginbackgroundimage":"marketingBg.jpg","SiteLogo":"'.$content2->SiteLogo.'","FaviconLogo":"'.$content2->FaviconLogo.'"}';
		}
        $data = array('content' => $content);
        $this->_db->update('tblConfiguration', $data ,array(
            "clientID='" . clientID . "'"
        ));
    }

    public function searchusersection($UserID)
	{
		$db = $this->getDbTable();
		$Offset1 =(int)$Offset;
		$select = $db->select()->setIntegrityCheck( false );
		$select->from(array('A'=>'tblUsers'))
		->where('A.UserID = ?',$UserID )->where('A.clientID = ?', clientID);
		return $this->getDefaultAdapter()->query($select)->fetchAll();
	}
  
    

    public function getFeedBack($id)
	{   
	    $select = $this->_db->select()          
	       ->from(array('tblfeedback'))->where("tblfeedback.id = ?", $id)->where('tblfeedback.clientID = ?', clientID);
        return  $this->_db->fetchRow($select);         
    }

    public function getNotification($user,$type,$status='',$ghostseen='')   
    {     
	
		if($type) 
			$chkType = 'c.act_type = '.$type.' AND'; 

		if($type==109) 
			$chkType = '(c.act_type = 16 || c.act_type = 17 || c.act_type = 18 || c.act_type = 45) AND'; 
	      
	      $sql = "select c.*,u.UserID,u.Name,u.lname,u.Username,u.ProfilePic from 
	      		  tblactivity c join tblUsers u on c.act_userId  = u.UserID 
	      		  where ".$chkType."  ".$ghostseen."
	      		   c.act_ownerid  =" .$user ." AND c.act_type != 12
				  and c.act_userId !=" .$user ." AND c.clientID=".clientID."  
	             order by c.act_date Desc";
	    /*  if($type==2){
	      echo $sql; die;
	  	}*/
	      return $this->_db->query($sql)->fetchAll();
    }

    public function getListNotification($user,$type,$status='',$ghostseen='')   
    {     
	
		if($type) 
			$chkType = 'c.act_type = '.$type.' AND'; 
	      
	      $sql = "select c.*,u.UserID,u.Name,u.lname,u.Username,u.ProfilePic from 
	      		  tblactivity c join tblUsers u on c.act_userId  = u.UserID 
	      		  where ".$chkType." ".$ghostseen."
	      		   c.act_ownerid  =" .$user ." AND (c.act_type = 15 || c.act_type = 16 || c.act_type = 17 || c.act_type = 18 || c.act_type = 19 || c.act_type = 20 || c.act_type = 2 || c.act_type = 30 || c.act_type = 44)
				  and c.act_userId !=" .$user ." AND c.clientID=".clientID." 
	             order by c.act_date Desc";
	      $sql .= " LIMIT 7";
	      return $this->_db->query($sql)->fetchAll();
    }

    public function getRequestToJoinNotification($user,$type,$status='')   
    {     
		if($type) 
			$chkType = 'c.act_type = '.$type.' AND'; 
	      
	      $sql = "select c.*,u.UserID,u.Name,u.lname,u.Username,u.ProfilePic from 
	      		  tblactivity c join tblDbeeJoinedUser as j ON j.dbeeID = c.act_typeId join tblUsers u on c.act_userId  = u.UserID  
	      		  where ".$chkType."  ".$condition."
	      		   c.act_ownerid  =" .$user ." AND c.act_type != 12
				  and c.act_userId !=" .$user ." AND j.status = 0 AND c.clientID=".clientID." 
	             order by c.act_date Desc";
	      $sql .= " LIMIT 10";
	      return $this->_db->query($sql)->fetchAll();
    }

    public function getRequestToJoinCountNotification($user,$type,$status='',$ghostseen='')   
    {   
    	$condition = '';  
		if($status==1)  
			$condition = " c.act_status='0' AND";

		if($type) 
			$chkType = 'c.act_type = '.$type.' AND'; 
	      
	      $sql = "select c.*,u.UserID,u.Name,u.lname,u.Username,u.ProfilePic from 
	      		  tblactivity c join tblDbeeJoinedUser as j ON j.dbeeID = c.act_typeId join tblUsers u on c.act_userId  = u.UserID  
	      		  where ".$chkType."  ".$condition."
	      		   c.act_ownerid  =" .$user ." AND c.act_type != 12
				  and c.act_userId !=" .$user ." AND j.status = 0 AND c.clientID=".clientID." 
	             order by c.act_date Desc";
	      $sql .= " LIMIT 10";
	      return $this->_db->query($sql)->fetchAll();
    }

    public function getVIPGroupCountNotification($user,$type,$status='',$ghostseen='')   
    {   
    	$condition = '';  
		if($status==1)  
			$condition = " c.act_status='0' AND";

		if($type) 
			$chkType = 'c.act_type = '.$type.' AND'; 
	      
	      $sql = "select c.*,u.UserID,u.Name,u.lname,u.Username,u.ProfilePic from 
	      		  tblactivity c join tblGroupMembers as j ON j.GroupID = c.act_typeId join tblUsers u on c.act_userId  = u.UserID  
	      		  where ".$chkType."  ".$condition."
	      		   c.act_ownerid  =" .$user ." AND c.act_type != 12
				  and c.act_userId !=" .$user ." AND j.Status = 0 AND c.clientID=".clientID." 
	             order by c.act_date Desc";
	      $sql .= " LIMIT 10";
	      return $this->_db->query($sql)->fetchAll();
    }

    public function getCountNotification($user,$type,$status='',$ghostseen='')   
    {     
		if($status==1)  
			$condition = " c.act_status='0' AND";

		if($type) 
			$chkType = 'c.act_type = '.$type.' AND'; 

		if($type==109) 
			$chkType = '(c.act_type = 16 || c.act_type = 17 || c.act_type = 18 || c.act_type = 19) AND'; 
	      
	      $sql = "select c.*,u.UserID,u.Name,u.lname,u.Username,u.ProfilePic from 
	      		  tblactivity c join tblUsers u on c.act_userId  = u.UserID 
	      		  where ".$chkType."  ".$condition." ".$ghostseen."
	      		   c.act_ownerid  =" .$user ." AND (c.act_type != 12)
				  and c.act_userId !=" .$user ." AND c.clientID=".clientID." 
	             order by c.act_date Desc";
	      $sql .= " LIMIT 10";
	      return $this->_db->query($sql)->fetchAll();
    }

    public function getCountForNewNotification($user)   
    {     
      $condition = " c.act_status='0' AND";
      $sql = "select c.*,u.UserID,u.Name,u.lname,u.Username,u.ProfilePic from 
      		  tblactivity c join tblUsers u on c.act_userId  = u.UserID 
      		  where c.act_status='0' AND 
      		   c.act_ownerid  =" .$user ." AND (c.act_type = 15 || c.act_type = 16 || c.act_type = 45 || c.act_type = 17 || c.act_type = 18 || c.act_type = 19 || c.act_type = 20 || c.act_type = 2 || c.act_type = 1  || c.act_type = 30 || c.act_type = 44)
			  and c.act_userId !=" .$user ."  AND c.clientID=".clientID." 
             order by c.act_date Desc";
      $sql .= " LIMIT 10";
      return $this->_db->query($sql)->fetchAll();
    }

    public function updateNotify($data, $adminID, $type)
    {
        return $this->_db->update('tblactivity', $data, array(
            "act_ownerid='" . $adminID . "'",("act_type='16'" || "act_type='17'" || "act_type='45'"  || "act_type='18'" || "act_type='30'"),"clientID='" . clientID . "'"
        ));
    }
    public function checkExistenceTokenexpert($dbeeid)
    {
        $select = $this->_db->select()->from('tblinvitexport')->where("dbeeid = ?", $dbeeid)->where("clientID = ?", clientID);
        return $this->_db->fetchRow($select);
    }
    public  function getallcategory(){
    	$db = $this->getDbTable();
    	$select = $db->select();
    	$select->setIntegrityCheck( false );
    	$select->from('tblDbeeCats');
    	$select->where("clientID = ?", clientID); 
    	$select->order('CatName ASC');
    	
    	$result = $db->fetchAll($select);    	
    	return $result;
    }
    public  function searchusr($cat,$gender,$age1,$age2,$start=20,$offset,$limit=1){
    	$db = $this->getDbTable();
    	$select = $db->select();
    	
    	$gender1 = $this->customEncoding($gender); 
    	
    	$select->setIntegrityCheck( false );
    	//$select->distinct('u.UserID');
    	$select->from(array('u'=>'tblUsers'), array('UserID','Name','lname','ProfilePic','City','company','country_name'));    	
    	$select->where("u.clientID = ?", clientID);
    	if(!empty($cat)){
    		$select->joinRight(array('c'=>'tblDbees'), 'u.UserID = c.User','DbeeID');
    		$select->where("c.Cats = ?", $cat);
    	}
    	if(!empty($gender))
    		$select->where("Gender = ?", $gender1);
    	if(!empty($age2))
    		$select->where("Birthdate >= ?", $age2);
    	if(!empty($age1))
    		$select->where("Birthdate <= ?", $age1);
    	$select->group('u.UserID');
    	//echo $select->__toString();
    	if($limit==1)
    	$select->limit($start,$offset);
    
    	$result = $db->fetchAll($select);
    	return $result;
    }
  
    public function customEncoding($cryptokey,$usingCall=''){
    	if($cryptokey=='') return false;
    	if($usingCall=='')
    	{
    		$length       = rand(4,8);
    		$randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    		$key          = strtolower($randomString);
    		$mykey        = str_replace(" ",'adroambot',trim($cryptokey));
    		$lastkey      = str_rot13(strtolower('mabirdnny'));
    		$final        = ($mykey.$lastkey.'#');
    	}
    	else
    	{
    		$mykey        = str_replace(" ",'adroambot',trim($cryptokey));
    		$final    = $mykey;
    	}
    	return str_rot13($final);
    }
    public function customDecoding($cryptokey){
    	if($cryptokey=='') return false;
    	$expl = explode("nqebnzobg", trim($cryptokey));
    	$originalString = '';
    	foreach ($expl as $key => $value) $originalString .= $value.' ';
    	$retuOrg  =  explode("mabirdnny", trim($originalString));
    	return str_rot13($retuOrg[0]);
    }


    public function getpollvote($dbeeid) {	
		$select = $this->_db->select()
			->from('tblPollVotes',array("cnt"=>"count('ID')"))
					->where('PollID = ?',$dbeeid)->where('clientID = ?',clientID);			
		return $this->_db->fetchAll($select);
		
	}



public function getpolloption($dbeeid) 
	{	
		$select = $this->_db->select()
		->from('tblPollOptions',
				array("OptionText","ID","PollID"))
				->where('PollID = ?',$dbeeid)->where('clientID = ?',clientID);			
		return $this->_db->fetchAll($select);
		
	}




    public function getpolloptionvote($dbeeid,$opid) 
	{	
		$select = $this->_db->select()
			->from('tblPollVotes',array("cnt"=>"count('ID')"));					
					$select->where('Vote ='.$opid.' AND PollID = '.$dbeeid.'')->where('clientID = ?',clientID);						
		return $this->_db->fetchAll($select);
		
	}


	public function getFolders($fId='',$folderorfile='') {    
    	$select = $this->_db->select()
    		->from('tblknowledge');
    	if($folderorfile=='foldernfiles' )
    		$select->where('clientID = ?', clientID)->where('kc_pid  =? ' , $fId)->where('status  =? ' , 0);
    	else if($folderorfile=='allfolder' )
    		$select->where('clientID = ?', clientID)->where('kc_pid  =? ' , 0)->where('status  =? ' , 0); 
    		return $data = $this->_db->fetchAll($select);
    }


    public function getfolderscnt($fId='') {    
    	$select = $this->_db->select()
    	->from('tblknowledge','kc_pid')
    		->where('clientID = ?', clientID)->where('kc_pid  =? ' , $fId)->where('status  =? ' , 0);    	
    	return $data = count($this->_db->fetchAll($select));
    }



	public function Pollhelper($dbeeid)
	{
			
			
				$totalvotesobj = $this->getpollvote($dbeeid);						
				  $totalvotes = $totalvotesobj[0]['cnt'];			
				$pres = $this->getpolloption($dbeeid);	
				   $colorRadio =  array('#3366cc' ,'#dc3912',  '#ff9900', '#109618');
      				$colorcount = 0;		
				foreach($pres as $prow):
					if($totalvotes>=0) {					
						$psrid = $prow['ID'];
						$totalobj = $this->getpolloptionvote($dbeeid,$psrid);
						 $total = $totalobj[0]['cnt'];
						 //$totalvotes;
						//$total =1;
						if($totalvotes){
							$percent=($total/$totalvotes)*100;
						}else{
							$percent='';
						}
						$width=round($percent,1);
						$stats.='<div class="pollstatsbar-wrapper">';
						if(round($percent)>0) $stats.='<span class="checkcolorSymbol" style="background:'. $colorRadio[$colorcount].'"><span class="pollPercentValue">'.round($percent,1).'%</span></span>'; 
						else $stats.='<span class="checkcolorSymbol" style="background:'. $colorRadio[$colorcount].'"><span class="pollPercentValue"></span></span>';
						$stats.='<span class="pollLableTxt">'.$prow['OptionText'].'</div></span>';
					} /*else {
						$stats.='<div class="pollstatsbar-wrapper"></div><div>'.$prow['OptionText'].'</div>';
					}*/
					$colorcount++;
				endforeach;
				
			
			return $stats;	
	
	}

	public function FetchAdminSettings()
	{
		$select = $this->_db->select()
			->from('tblAdminSettings',array("plateform_scoring","IsLeagueOn"));					
					$select->where('clientID = ?',clientID);						
		return $this->_db->fetchAll($select);
	}


	public function eventlist($evid=''){

		$select = $this->_db->select()		
		->from('tblEvent',array("id","title"));
		$select->where('clientID = ?',clientID);	
		$select->where('end >= ?', new Zend_Db_Expr('NOW()'));
		$select->where('status = ?',1);	
		if($evid!='')
		{
		$select->where('id = ?',$evid);	
		}		
		$select->order('id DESC');
		return $this->_db->fetchAll($select);
				
	}

	public function eventlistall(){

		$select = $this->_db->select()		
		->from('tblEvent',array("*"));
		$select->where('clientID = ?',clientID);	
		$select->where('status = ?',1);			
		$select->order('id DESC');
		return $this->_db->fetchAll($select);				
	}

	public function getEventdetails($eventid){
		$select = $this->_db->select()		
		->from('tblEvent',array("*"));
		$select->where('clientID = ?',clientID);	
		$select->where('id = ?',$eventid);			
		return $this->_db->fetchAll($select);	
	}

	public function getuserseventData($eventid,$sendmsgval='',$limit='',$offset='0')  
	{
		$db = $this->getDbTable();	
		$getuserdetail 	= 	$db->select();
		$getuserdetail->setIntegrityCheck( false );

		$getuserdetail->from(array('u' => 'tblUsers'),array('u.Name','u.lname','u.Email','u.UserID','u.Username'));
		$getuserdetail->join(array('e' => 'tblEventmember'),'e.member_id = u.UserID');
		$getuserdetail->where('u.Status = ?', '1');
		$getuserdetail->where('e.event_id = ?', $eventid);
        $getuserdetail->where('u.clientID = ?', clientID);
		if($sendmsgval==1) // case of email sending
		{
			if($limit!='' )
			$getuserdetail->limit($limit,$offset);	
		}
		//echo $getuserdetail->__toString();exit;
		$getuserdetail	= $db->fetchAll($getuserdetail)->toarray();
        return $getuserdetail;
	}

	public function filterusertype($typeid)
    {
            $select = $this->_db->select();
            $select->from('tblUserType',array('TypeID','TypeName'));
            $select->where('TypeID NOT IN(?)',$typeid);
            $select->where('clientID = ?', clientID);
            $data = $this->_db->fetchAll($select); 
            //echo'<pre>';print_r($data);die;
            $userList = array();
            foreach($data as $user){
                //echo'<pre>';print_r($user);die;
                $userList['id']=$user['TypeID'];
                $userList['text']=$user['TypeName'];
                $userSub[] = $userList;
            }
            
            return json_encode($userSub);      
    }

    public function filteruserdetails()
    {
    	    $myclientdetails = new Admin_Model_Clientdetails();
      
           $sql = 'SELECT DISTINCT title,company,UserID,Name,
            lname,Username,Email FROM tblUsers WHERE clientID = "'.clientID.'"  AND title !="NULL" AND company != "NULL" 
GROUP BY title,company'; 
            $data = $this->_db->query($sql)->fetchAll($select);



            $usertitle = array();
            $usercompany = array();
            $userTit = array();
            $userCom = array();
            $title = array();
            $company = array();
            //$i=1;
            //$j=1;
            foreach($data as $userdetails){
				if(!empty($userdetails['title']))
				{	
					if(!in_array($myclientdetails->customDecoding($userdetails['title']), $title))
					{
						$usertitle['id'] = $myclientdetails->customDecoding($userdetails['title']);
						$usertitle['text'] = trim($myclientdetails->customDecoding($userdetails['title']));
						$title[] = trim($usertitle['text']);
						$userTit[] = $usertitle;
						unset($usertitle);
					}

				}
				//$i++;
				if(!empty($userdetails['company'])){

					if(!in_array($myclientdetails->customDecoding($userdetails['company']), $company))
					{
						$usercompany['id'] = $myclientdetails->customDecoding($userdetails['company']);	
						$usercompany['text'] = trim($myclientdetails->customDecoding($userdetails['company']));
						$company[] =  trim($usercompany['text']);
						$userCom[] = $usercompany;
						unset($usercompany);
					}

				}
				//$j++;
            }
            $userData['title']= $userTit;
		    $userData['company']= $userCom;
	
            return json_encode($userData);      
    }

    public function getTotalUsersPost($dbeeid)
	{
		$data = array();
        $data_final = array(); 
		$usesrdataarray = $this->getDefaultAdapter()->query("SELECT UserID as id, Name,lname,full_name,ProfilePic as avatar FROM `tblUsers` WHERE (clientID= '".clientID."')")->fetchAll();			
			
			$myclientdetails = new Admin_Model_Clientdetails();
            foreach ($usesrdataarray as $value) 
            {
                    $data['name'] = $myclientdetails->customDecoding(str_replace("'", "", $value['full_name']));
                    $data['id'] = $value['id'];
                    $data['type'] = '8';
                    $data['avatar'] = $value['avatar'];
                    $data_final[$value['id']] =  $data; 
            }
           return json_encode($data_final);
	}
    
}