<?php
class Application_Model_League extends Application_Model_DbTable_Master
{
    protected $_name = null; 
	// protected $_dependentTables = array('Application_Model_Technicianjobtype');
	protected function _setupTableName()
    {
		parent::_setupTableName();		
		$this->_name = $this->getTableName(LEAGUE);     
    }
    public function index($start,$end,$userid)
    {
    	$select = $this->_db->select();    	
    	$select->from(array('c' => $this->_name))
    		->where('c.User IN(?)', $users)
    			->where("c.Active= ?",'1')    			
    					->order('c.LastActivity DESC')
    						->limit(PAGE_NUM, $Offset);
    		$result = $this->_db->fetchAll($select);
    		return $result;
    	}
   	public function getfolloweruser($userid)
    {
    	$select = $this->_db->select();
    		$select->from(array('c' => $this->_name))
    			->join(array('u' => $this->getTableName(USERS)), 'u.UserID = c.User');
    				$select->where('c.User =.'.$userid);
    					$select->order(array('c.User asc'));    						
    							$result = $this->_db->fetchAll($select);
    								return $result;
    }
    
    public function leagueinvolvedusers($dbs)
    {
        foreach ($dbs as $key => $value) {
            $cmntid    .=  $value['DbeeID'].',';
        }

        $select = $this->_db->select()
        ->from(array('cmnt' => 'tblDbeeComments'),'')
                ->join(array('u' => $this->getTableName(USERS)), 'u.UserID = cmnt.UserID AND u.clientID = cmnt.clientID',array('DISTINCT(u.UserID)','ProfilePic','Name','Username')) 
        			->where('cmnt.DbeeID IN ('.trim($cmntid,',').')')->where("cmnt.clientID = ?",clientID); 
        $result = $this->_db->fetchAll($select);        
        return $result;
    }
    public function memberleagues($callid)  //tblDbeeComments
    {
    	$select =   $this->_db->select();

        $sql    =   $select->from(array('cmnt' => 'tblDbeeComments'),'cmnt.DbeeID')->distinct('cmnt.DbeeID')
                   ->join(array('db'=>'tblDbees'),'db.DbeeID=cmnt.DbeeID AND db.clientID=cmnt.clientID','')                  
                   ->where('cmnt.UserID ='. $callid )->where("cmnt.clientID = ?",clientID);   
        //echo $sql->__toString();die;          
        $mydbs  =   $this->_db->fetchAll( $sql);
        
        $cudate = date('Y-m-d H:i:s');
        $memRet =   '';
       
        if(count($mydbs)>0)
        {
            foreach ($mydbs as $key => $value) {
                 $cmntid    .=  $value['DbeeID'].',';
            }
            $cmntids =   trim($cmntid,',');
            $onCondi = 'db.DbeeID  IN (' .$cmntids.' )' ;

            $memRet  .=   '<div id="leaguePositionRight" class="memberleaguesWrp">';
            $lesql  =    "select distinct(lg.LID), lg.*, u.Name,u.username,u.UserID,u.ProfilePic from tblUserLeague lg inner join tblLeagueDbee db on db.LID  = lg.LID inner join tblUsers u on u.UserID=lg.UserID where ".$onCondi." and lg.EndDate >= '".$cudate."' order by lg.EndDate DESC " ;  

            $lesql2  =    "select distinct(lg.LID), lg.*, u.Name,u.username,u.UserID,u.ProfilePic from tblUserLeague lg inner join tblLeagueDbee db on db.LID  = lg.LID inner join tblUsers u on u.UserID=lg.UserID where ".$onCondi." and lg.EndDate <= '".$cudate."' order by lg.EndDate DESC " ;   
            $leg1   =   $this->_db->query($lesql)->fetchAll();
            $leg2   =   $this->_db->query($lesql2)->fetchAll();
            $memRet .='<ul class="tabLinks tabHeader">
                <li><a href="#" rel="lploved" class="active">Live Leagues</a></li>
                <li><a href="#"  rel="lpRogues">Closed Leagues</a></li>
                </ul>
                <div class="tabcontainer tabContainerWrapper">';
            if(count($leg1)>0)
            {
            	
                $memRet .='<div class="tabcontent" id="lploved" style="display:block"><ul id="myleaguesPwrapper">';
                foreach ($leg1 as $ke => $val)
                {
                    if($val!='')
                    {
                    
                          $memRet .=$this->diplayleagueresult($val['LID'], $val['Title'], $val['Discription'], $val['EndDate'], $val['Name'], $val['UserID'],$val['ProfilePic'],$val['username']);
                    }
                }
                 $memRet .='</ul></div>';
            } else {
                $memRet .=   '<div class="tabcontent" id="lploved" style="display:block"><ul class="lplisting"><li style="text-align:center">  <div class="noFound" style="margin-top:50px; margin-bottom:50px;">You are not a member of any league.</div></li></ul></div>';
            }
            if(count($leg2)>0)
            {
                $memRet .='<div class="tabcontent" id="lpRogues" ><ul id="myleaguesPwrapper">';
                foreach ($leg2 as $ke => $val)
                {
                    if($val!='')
                    {
                
                        $memRet .=$this->diplayleagueresult($val['LID'], $val['Title'], $val['Discription'], $val['EndDate'], $val['Name'], $val['UserID'],$val['ProfilePic'],$val['username']);
                    
                    }
                }
                $memRet .='</ul></div>';
            } else {
                $memRet .=   '<div class="tabcontent" id="lpRogues" ><ul class="lplisting"><li style="text-align:center"><span style="text-align:center">  <div class="noFound" style="margin-top:50px; margin-bottom:50px;">All leagues are in running position.</div> </li></ul></div>';
            }
        }
        else
        {
            $memRet =   '<div class="noFound"  style="margin-top:50px; margin-bottom:50px;">You are not yet a member of any leagues.</div>';
        }

        return $memRet;
    }

    public function diplayleagueresult($lid, $title, $description, $enddate, $username='', $uid='', $upic='', $profname='')
    {
        $commonfunctionality = new Application_Model_Commonfunctionality();
        $this->myclientdetails = new Application_Model_Clientdetails();
        $upic = $commonfunctionality->checkImgExist($upic,'userpics','default-avatar.jpg');
        $ret = '';
        $ret .='<li>
                    <div class="leugUnWrapper"> 
                        <img src="'.IMGPATH.'/users/small/'.$upic.'" /> 
                    </div>
                    <div class="myleaguesUDetails">
                         <h4>
                            <a href="'.BASE_URL.'/league/index/id/'.$lid.'">'.$title.'</a> by <a href="'.BASE_URL.'/user/'.$this->myclientdetails->customDecoding($profname).'" class="leugUn">'.$this->myclientdetails->customDecoding($username).'</a>
                            <a href="'.BASE_URL.'/league/index/id/'.$lid.'" class="arrowBtnIcon"><span>see results</span></a>
                        </h4>
                        <div class="myleaguesDates">
                            <div> Closing On '.date("jS F Y H:i",strtotime($enddate)).' </div>
                        </div>
                    </div>
                 
                    <div class="myleagueDes">
                        '.$description.' 
                    </div>
                  </li>';

                  return   $ret;
    }

    public function getallleaguedbs($lid)  //tblDbeeComments
    {
       // $sql = "select DbeeID,Enddate from tblLeagueDbee  where LID=".$callid ;
       // return $results = $this->_db->query($sql)->fetchAll();
    	$select = $this->_db->select()
	    	->from(array('l'=>$this->getTableName(DBELEAGUE)),array('l.DbeeID','l.Enddate'))
	    	->joinInner(array('c' => $this->getTableName(DBEE)), 'c.DbeeID = l.DbeeID AND c.clientID = l.clientID', array('c.DbeeID'))
		    	->where("l.LID= ?", $lid)
		    		->where("c.Active= ?", '1')->where("l.clientID = ?",clientID);
    	
    	$result = $this->_db->fetchAll($select);
    	return $result;
    }

    public function chkdbinleague($dbid)  //tblDbeeComments
    {
        $CurrDate=date('Y-m-d H:i:s');
        $onCondi = 'DbeeID  = ' .$dbid.' and lg.Enddate >= "'. $CurrDate.'"' ;
        
        $sql = "select DISTINCT(lg.LID),lu.Title from tblUserLeague lu,tblLeagueDbee lg where lu.LID=lg.LID and lu.clientID = ".clientID." AND ".$onCondi ; 
        $results = $this->_db->query($sql)->fetchAll();
        return $results;
    }
    
    public function getleaguedbdetails($callid)  //tblDbeeComments
    {
    	foreach ($callid as $key => $value) {
    		$cmntid    .=  $value['DbeeID'].',';
    	}
    	$cmntids    =   trim($cmntid,',');
    	//$onCondi = 'AND DbeeID  IN (' .$cmntids.' )' ;    	
    	//$sql = "select * from tblDbees  where ".$onCondi ;
    	//return $results = $this->_db->query($sql)->fetchAll();
    	$select = $this->_db->select()
	    	->from(array('c'=>$this->getTableName(DBEE)))		    	
			    	->where("c.DbeeID IN(?)", $cmntids)
			    		->where("c.Active= ?", '1')->where("c.clientID = ?",clientID);    	
    	$result = $this->_db->fetchAll($select);
    	return $result;
    	
    	
    }
    
    public function getCommentIdsofDbs($callid,$calling)  //tblDbeeComments
    {
    	if($calling=='group')       $onCondi = 'db.GroupID  =' .$callid;
    	else if($calling=='dbee')   $onCondi = 'db.DbeeID  =' .$callid;
    	else if($calling=='league')
    	{
    		
    		foreach ($callid as $key => $value) {
    			 $cmntid    .=  $value['DbeeID'].',';
    		}
    		$cmntids    =   trim($cmntid,',');
    		$onCondi = 'db.DbeeID  IN (' .$cmntids.' )' ;
    	}
    	
      	$sql = "select cmnt.CommentID from tblDbees db inner join tblDbeeComments cmnt on db.DbeeID  = cmnt.DbeeID  AND db.clientID  = cmnt.clientID where cmnt.clientID = ".clientID." AND ".$onCondi ;
    	return $results = $this->_db->query($sql)->fetchAll();
    	exit;
    }
    
    public function getLeagereport($commentIdArr,$type,$calling='',$lgEndDate='')  //tblDbeeComments
    {
    	// echo "<pre>";
        $commonfunctionality = new Application_Model_Commonfunctionality();
    	$cmntid     =   '';
    	$result2    =   array();
    	$result1    =   array();
    	
    	if(count($commentIdArr)>0) 
    	{//echo count($commentIdArr);exit;
	    	foreach ($commentIdArr as $key => $value) {
	    		$cmntid    .=  $value['CommentID'].',';
	    	}
	    	$cmntids    =   trim($cmntid,',');
	    	$posiMsg    =   '';

            $lgEpiryDate = '';
           
            if($lgEndDate!='') $lgEpiryDate = 'and score.ScoreDate <= "'.$lgEndDate.'"';
	       
	    	if($type=='love')
	    	{
	    		$condition1 =   'and ( score.Score = 1 )';
	    		$condition2 =   'and ( score.Score = 2 )';
	    		$posiMsg    =   'Sorry, you don\'t seem to be on the list!';
                $finalpos   =   ' most loved';
	    	}
	    	else if($type=='fot')
	    	{
	    		$condition2 =   'and ( score.Score = 3)';
	    		$posiMsg    =   'Sorry, you don\'t seem to be on the list!';
                $finalpos   =   ' deepest thinker';
	    	}
	    	else if($type=='hate')
	    	{
	    		$condition1 =   'and ( score.Score = 5 )';
	    		$condition2 =   'and ( score.Score = 4 )';
	    		$posiMsg    =   'You are not on the rouges list :)';
                $finalpos   =   ' biggest rogue';
	    	}
	    
	    	if($condition1)
	    	{
	    		$sql = "select u.ProfilePic,u.Name,u.UserID,u.Username, count(*)*5 as score from tblScoring score  left join tblUsers u on score.Owner = u.UserID AND score.clientID = u.clientID  where score.clientID = ".clientID." AND score.Type = 2 and u.Status=1 and score.ID in (".$cmntids.") ".$condition1." ".$lgEpiryDate." group by score.Owner " ;
	    		$result1 = $this->_db->query($sql)->fetchAll();
	    	}
	    	if($condition2)
	    	{
	    		$sql = "select u.ProfilePic,u.Name,u.UserID,u.Username, count(*) as score from tblScoring score  left join tblUsers u on score.Owner = u.UserID AND score.clientID = u.clientID  where score.clientID = ".clientID." AND score.Type = 2 and u.Status=1 and score.ID in (".$cmntids.") ".$condition2." ".$lgEpiryDate." group by score.Owner " ;
	    		$result2 = $this->_db->query($sql)->fetchAll();
	    	}
    	}
    	$toppers    =   array();
    	$pridArr    =   array();
    	$prId       =   '';
    	$prSc       =   '';
    	$keycou     =   0;
    	$top3       =   0;
    
    	$mergedRes  =   (array_merge($result1,$result2));
    
    	//print_r($mergedRes);
        $myclientdetails = new Application_Model_Clientdetails();
    	foreach ($mergedRes as $key => $value)
    	{
    		$prId       =   '';
    		$prSc       =   '';
    		$prId       =   $value['UserID'];
    
    		if(!in_array($prId, array_unique($pridArr)))
    		{
    			for ($i=0; $i < count($mergedRes); $i++)
    			{
	    			if($prId ==  $mergedRes[$i]['UserID'])
	    			{
		    			$toppers[$keycou]['avater']  = $mergedRes[$i]['ProfilePic'];
		    			$toppers[$keycou]['name']    = $myclientdetails->customDecoding($mergedRes[$i]['Name']);
		    			$toppers[$keycou]['id']      = $mergedRes[$i]['UserID'];
		    			$toppers[$keycou]['score']   = $prSc+$mergedRes[$i]['score'];
                        $toppers[$keycou]['Username']      = $myclientdetails->customDecoding($mergedRes[$i]['Username']);
		    			$prSc =  $toppers[$keycou]['score'];
			    	}
			    }
		    }
		    
		    $pridArr[]  =   $prId;
		    $keycou++;
		}
    
	    $legReturn  =    '';
	    $notin      =    99;
        $myscore    =    '';
        $myposcount =   1;       
	    
	    $toppers = array_reverse($this->aasort($toppers,"score"));
	    
	    $sessionuserid  =   $_SESSION['Zend_Auth']['storage']['UserID'];
	  //  print_r($toppers);
	    if(count($toppers)>0)
	    {
            if($calling=='')
            {
	    
    		    foreach ($toppers as $ke => $val)
    		    {
    		    	if($val!='')
    		    	{
    		    
    			    	$numbers = '';
                        $tropy = '<i class="fa fa-trophy"></i> ';
    			    	if( $ke == 0) $cupclass = 'prizeType  cupSprite goldCup';
    			    	if( $ke == 1) $cupclass = 'prizeType  cupSprite silverCup';
    			    	if( $ke == 2) $cupclass = 'prizeType  cupSprite bronzeCup';
    			    	if( $ke > 2)  {
    				    	$cupclass  = 'prizeType';  
                            $numbers = $ke+1;
                             $tropy = '';
    				    }else $numbers = $ke+1;
                       
                        
                        $upic = $commonfunctionality->checkImgExist($val['avater'],'userpics','default-avatar.jpg');
    				    $legReturn .='<li>
        				    <div class=" '.$cupclass.' ">'. $tropy.' '.$numbers.'</div>
        				    <div class="leaguesUserPic">
        				        <a href="javascript:void()"><img src="'.IMGPATH.'/users/small/'.$upic.'" /></a>
        				    </div>
        				    <div class="leagusListDetails">
            				    <span class="leaguesUserName oneline"><a href="'.BASE_URL.'/profile/index/id/'.$val['id'].'"> '.$val['name'].'</a></span> <br>
            				    <span class="rleaguesPoints">'.$val['score'].' pts</span>
        				    </div>
    				    </li>';
    				    if($val['id'] == $sessionuserid)  { 
                            if( $myposcount == 1) $mypos =  $myposcount.'st';
                            else if( $myposcount == 2) $mypos =  $myposcount.'nd';
                            else if( $myposcount == 3) $mypos =  $myposcount.'rd';
                            else $mypos = $myposcount.'th';
                            $notin =  1;
                            $myscore = '<span class="mypsn">'.$mypos.' </span>';
                        }

                        $myposcount++;
    			    }
    		    }
                $posiMsg = 'Nil';
    	    	if($notin==99)  $myscore = ' <span class="mypsn mypsnNil"> '.$posiMsg.' </span>';
            }
            else
            {
                $uexp   =   explode(' ', $toppers[0]['name']);
                $toppic = $commonfunctionality->checkImgExist($toppers[0]['avater'],'userpics','default-avatar.jpg');
                $legReturn ='<div style="float:left;margin-top:2px;"><img border="0" src="'.IMGPATH.'/users/small/'.$toppic.'" width="27" height="27"></div><div style="float:left; margin:8px 0 0 5px"><a href="'.BASE_URL.'/user/'.$toppers[0]['name'].'" class="cmntuserLink">'.$uexp[0].'</a></div><span class="comment-league-label">'.$finalpos.'</span>';
            }	
    	}
    	else
    	{
            if($calling=='')
    		$legReturn .='<li> <div class="noFound"> -- This league currently has no users -- </div></li>';
            $myscore = '<span class="mypsn mypsnNil">Nil</span>';
	    }
	    
	 	return  $legReturn.'~'.$myscore;
	    exit;
    }
    public function aasort (&$array, $key) 
    {
	    $sorter=array();
	    $ret=array();
	    reset($array);
        foreach ($array as $ii => $va)
            $sorter[$ii]=$va[$key];
        asort($sorter);
        foreach ($sorter as $ii => $va)
         $ret[$ii]=$array[$ii];
	    return $array=$ret;
    }
    
    public function getdbeeintialleague($lid,$userid)
    {  
        require_once 'includes/globalfile.php';   	
    	$select           = $this->_db->select()->from(array(
    			'l' => $this->getTableName(DBELEAGUE)
    	))->joinInner(array(
    			'c' => $this->getTableName(DBEE)
    	), 'l.DbeeID = c.DbeeID AND l.clientID = c.clientID' ,$dbtablefields)->joinInner(array(
    			'u' => $this->getTableName(USERS)
    	), 'u.UserID = c.User AND u.clientID = c.clientID');    
    	$select->where('l.LID = ?',$lid);
    	$select->where('c.Active = ?','1');
        $select->where('c.clientID = ?',clientID);
    	$select->order(array(
    			'c' => 'LastActivity DESC'
    	));//->limit(PAGE_NUM, 0);
    
    	return $this->_db->fetchAll($select);
    }
    
    public function getleaguedetail($lid)
    {
    	$lids = (int)$lid;
    	$select = $this->_db->select()
	    	->from(array('l' => $this->getTableName(LEAGUE)),array('LID','Title','StartDate','EndDate','UserID','Discription'))    	
		    	->joinInner(array('u' => $this->getTableName(USERS)), 'l.UserID = u.UserID AND l.clientID = u.clientID', array('u.UserID','u.Name','u.Username','u.ProfilePic'))   
			    	->where('l.LID =?',$lids)->where("l.clientID = ?",clientID)
			    		->order('l.EndDate  ASC');	    
	    $result = $this->_db->fetchRow($select);	    	
	    return $result;
    }
    

}	