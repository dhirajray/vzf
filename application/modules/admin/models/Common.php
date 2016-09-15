<?php

class Admin_Model_Common  extends Zend_Db_Table_Abstract
{
    protected $_dbTable;
	public $myclientdetails;
	
			
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
    public function filterSaveChartHtml($datefilter='',$calling='')
    {
          $divid= "addgroupBtn";
          if($calling=='tracklogins')
          {
            $divid= "";
          }

          $allchartgroups = $this->myclientdetails->getfieldsfromtable(array('id','groupname'),'adminchartgroup','1','1');

          $filterSaveChartHtml = '<div class="dropDown pull-right">
                   <a href="javascript:void(0)" class="filterSave dropDownTarget pull-right btn btn-mini btn-green" style="font-size:12px" >Save report</a>
                   <div class="dropDownList groupinsertWrapper  right">
                   <div class="grpWrapperBox">
            <div style="display:block;" class="createdGrpDrp">
                <div class="creatGWrap">              
              <div class="saveFilterWrapper">
                <div class="subPopupContainer">
                  <h3>Save report</h3>
                  <div class="formRow">
                    <input type="text" name="groupvalue" placeholder="Add report name" class="pull-right" value="">
                  </div>
                  <div class="formRow">
                    <input type="hidden" name="chartvalue2" placeholder="Add report name" class="pull-right" value="">
                  </div>      
                </div>
                <div class="popupFooterWrapper">
                  <button type="button" class="btn btn-green" id="saveChartGrpName" data-group="true">Save report</button>
                  <button type="button" class="btn cancelSaveFilter">Cancel</button>
                </div>
              </div>
            </div>
              <div style="margin-bottom:15px;"><a id="usergrouplinkforadd" class="btn fluidBtn" href="javascript:void(0)"> Create new report</a></div>
              <div class="clearfix"></div>';  
              if(count($allchartgroups)>0)
                      {

                $filterSaveChartHtml .='<div class="formRow"><select name="groupvalueid" id="groupvalueid"><option value="">Select existing report</option>';
                          foreach ($allchartgroups as $key => $value) {
                            $filterSaveChartHtml .= '<option value="'.$value['id'].'">'.$value['groupname'].'</option>';
                          }         
              $filterSaveChartHtml .= '</select>
              </div>';

              $addnewchart ='<button style="display:block;"  id="'.$divid.'" class="btn btn-green filterSaveBtn fluidBtn removeOr" type="button">  Save to existing report </button> ';
              } 
              $filterSaveChartHtml .= '<div class="formRow">  
                <input type="hidden" name="chartvalue" placeholder="chart title" class="pull-right fluidBtn" value="">
              </div>          
            </div>'.$addnewchart.'
            </div>
                  <div class="formRow">';

                      //calling all chart groups  <i class="fa fa-filter"></i>
          $filterSaveChartHtml .= '</div>
                </div>';
          if($datefilter!='')     
          {
            $filterSaveChartHtml .= '<a href="javascript:void(0);" class="filterChart pull-right btn btn-mini btn-green" title="Filter Chart">Date filter</a>';
          }  
          return $filterSaveChartHtml;

    }
	


    //*************** Start For Global Search *************

    public function createGlobalConditions_CM($field,$operator,$keyword,$condi)
	{
	 
	  switch($field)
      {
      	case 'User' :
        	$skeyword = $this->myclientdetails->customEncoding($keyword);
        	if($operator=='Is') return  'u.clientID = '.clientID .' AND u.full_name '. $this->getSyntax_CM( $operator ) .' "'. $skeyword.'"';
        	else  
        	{
        		$keyword = $this->myclientdetails->customEncoding($keyword,'fortopsearch');
        		return  'u.clientID = '.clientID .' AND u.full_name '. $this->getSyntax_CM( $operator ) .' "%'.$keyword.'%"';
        	}
          break;
        case 'User name' :
        	$skeyword = $this->myclientdetails->customEncoding($keyword);
        	if($operator=='Is') return  'u.clientID = '.clientID .' AND u.full_name '. $this->getSyntax_CM( $operator ) .' "'. $skeyword.'"';
        	else  
        	{
        		$keyword = $this->myclientdetails->customEncoding($keyword,'fortopsearch');
        		return  'u.clientID = '.clientID .' AND u.full_name '. $this->getSyntax_CM( $operator ) .' "%'.$keyword.'%"';
        	}
          break;
        case 'Email address' :
        	$ekeyword = $this->myclientdetails->customEncoding($keyword,'globalsearch');
        	if($operator=='Is') return  'u.clientID = '.clientID .' AND u.Email '. $this->getSyntax_CM( $operator ) .' "'. $ekeyword.'"';
        	else
        	{
        		$keyword = $this->myclientdetails->customEncoding($keyword,'fortopsearch');
        	  	return  'u.clientID = '.clientID .' AND u.Email '. $this->getSyntax_CM( $operator ) .' "%'.$keyword.'%"';
        	}
          break;
        case 'Register date' :
        	if($operator!='On') return  'u.clientID = '.clientID .' AND u.RegistrationDate '. $this->getSyntax_CM( $operator ) .' "'.date('Y-m-d H:i:s',strtotime($keyword)).'"';
        	else  return  'u.clientID = '.clientID .' AND u.RegistrationDate '. $this->getSyntax_CM( $operator ) .' "'.date('Y-m-d H:i:s',strtotime($keyword)).'"';
            break;
        case 'Login date' :
        	if($operator!='On') return  'u.clientID = '.clientID .' AND DATE_FORMAT(tbluserlogindetails.logindate,"%Y-%m-%d" )'. $this->getSyntax_CM( $operator ) .' "'.date('Y-m-d',strtotime($keyword)).'"';
        	else  return  'u.clientID = '.clientID .' AND DATE_FORMAT(tbluserlogindetails.logindate,"%Y-%m-%d" ) '. $this->getSyntax_CM( $operator ) .' "'.date('Y-m-d',strtotime($keyword)).'" ';
            break;    
        case 'Posts' :           
           if($operator=='Exactly') 
           	{
           		return  'tblDbees.clientID = '.clientID .' AND  tblDbees.Text '. $this->getSyntax_CM( $operator ) .' "'.$keyword.'"  OR tblDbees.PollText '. $this->getSyntax_CM( $operator ) .' "'.$keyword.'"';
           	}
        	else  return  'tblDbees.clientID = '.clientID .' AND tblDbees.Text like "%'.$keyword.'%"  OR tblDbees.PollText like "%'.$keyword.'%"';
          break;
        case 'Post Type' :
            return  'tblDbees.clientID = '.clientID .' AND '. $this->dbtypelinked_CM($keyword,'tblDbees');
        	break;
        case 'Comments' :
        	$cmntkeyword = $this->myclientdetails->customEncoding($keyword,'commentbyname');
           	/*if($operator!='By') return  'tblDbeeComments.clientID = '.clientID .' AND tblDbeeComments.DbeeOwner '. $this->getSyntax_CM( $operator ) .' "'. $keyword.'"';
        	else  return  'tblDbeeComments.clientID = '.clientID .' AND tblDbeeComments.UserID'. $this->getSyntax_CM( $operator ) .' "'.$keyword.'"';*/
        	if($operator!='By') return  'tblDbeeComments.clientID = '.clientID .' AND owner.Name like "%'. $cmntkeyword.'%"';
        	else  return  'tblDbeeComments.clientID = '.clientID .' AND u.Name like "%'. $cmntkeyword.'%"';
          break;
        case 'Comment Type' :
            return  'tblDbeeComments.clientID = '.clientID .' AND '.$this->dbtypelinked_CM($keyword,'tblDbeeComments');
          break;
        case 'Scored' :
           if($operator!='By') return  'tblScoring.clientID = '.clientID .' AND tblScoring.Owner '. $this->getSyntax_CM( $operator ) .' "'. $keyword.'"';
        	else  return  'tblScoring.clientID = '.clientID .' AND tblScoring.UserID'. $this->getSyntax_CM( $operator ) .' "'.$keyword.'"';
          break; 
        case 'Score Type' :
            return  'tblScoring.clientID = '.clientID .' AND tblScoring.Score '. $this->getSyntax_CM( $operator ) .' "'. $this->getscoreidfromType_CM($keyword).'"';
          break;
        case 'Group name' :
           if($operator!='Containing') return  'tblGroups.clientID = '.clientID .' AND tblGroups.GroupName '. $this->getSyntax_CM( $operator ) .' "'. $keyword.'"';
        	else  return  'tblGroups.clientID = '.clientID .' AND tblGroups.GroupName '. $this->getSyntax_CM( $operator ) .' "%'.$keyword.'%"';
          break;
        case 'Groups type' :
            return  'tblGroups.clientID = '.clientID .' AND tblGroups.GroupPrivacy '. $this->getSyntax_CM( $operator ) .' "'. $this->getgroupidfromType_CM($keyword).'"';
          break;
        case 'Group category' :
            return  'tblGroups.clientID = '.clientID .' AND tblGroups.GroupType '. $this->getSyntax_CM( $operator ) .' "'. ($keyword).'"';
          break;
        case '@User references' :
           /*if($operator!='By') return  'tblactivity.clientID = '.clientID .' AND (tblactivity.act_ownerid '. $this->getSyntax_CM( $operator ) .' "'. $keyword.'" AND ( tblactivity.act_type =2 ))';
        	else  return  'tblactivity.clientID = '.clientID .' AND (tblactivity.act_userId'. $this->getSyntax_CM( $operator ) .' "'.$keyword.'" AND ( tblactivity.act_type =2 ))';*/
        	$ntkeyword = $this->myclientdetails->customEncoding($keyword,'commentbyname');
        	if($operator!='By') return  'tblactivity.clientID = '.clientID .' AND ( owner.Name like "%'. $ntkeyword.'%" AND ( tblactivity.act_type =2 ))';
        	else  return  'tblactivity.clientID = '.clientID .' AND (u.Name like "%'. $ntkeyword.'%" AND ( tblactivity.act_type =2 ))';
          break;        
        
        default :
           return  ' ';
          break;           
      }
	}

	public function addhttp($url) {
        if (!preg_match("@^https?://@i", $url) && !preg_match("@^ftps?://@i", $url)) {
            $url = "http://" . $url;
        }
        return $url;
    }

	public function addLinks($text) {
    return preg_replace('@(http)?(s)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@', '&lt;a target="ref" href="http$2://$4"&gt;$1$2$3$4&lt;/a&gt;', $text);
	}

	public function dbtypelinked_CM($type,$table)
	{
		switch($type)
		{
			case 'Text' :
				 return  $table.'Text != "" ';
				break;
			case 'Link' :
				 return  $table.'.LinkTitle != "" ';
				break;
			case 'Pix' :
				 return  $table.'.Pic != "" ';
				break;
			case 'Media' :
				 return  $table.'.Vid != "" ';
				break;
			case 'Polls' :
				 return  $table.'.PollText != "" ';
				break;
			default :
				 return  ' 1= 1 ';
				break;	
		}
	}
	public function joiningGlobalConditions_CM($field,$operator,$keyword,$condi)
	{
	  switch($field)
      {
        case 'User name' : return 'tblUsers'; break;
        case 'Email address' : return  'tblUsers'; break;
        case 'Login date' : return  'tbluserlogindetails'; break;
        case 'Register date' : return  'tblUsers'; break;
        case 'Posts' :  return  'tblDbees'; break;
        case 'Post Type' :return  'tblDbees'; break;
        case 'Comments' : return  'tblDbeeComments'; break;
        case 'Comment Type' : return  'tblDbeeComments'; break;
        case 'Scored' : return 'tblScoring'; break; 
        case 'Score Type' : return  'tblScoring'; break;
        case 'Group name' : return  'tblGroups';   break;
        case 'Groups type' :return  'tblGroups'; break;
        case 'Group category' :return  'tblGroups'; break;        
        case '@User references' : return  'tblactivity'; break;        
        default : return  ''; break;           
      }
	}
	public function getjoinsfortable($tablerd)
	{
		//print_r($tablerd);
		/*if(count($tablerd)==1){
			if($tablerd[0]=='tblUsers') return ' 1=1 # tblUsers #distinct(tblUsers.UserID),tblUsers.Name,tblUsers.ProfilePic';
			
			if($tablerd[0]=='tblDbees') return ' tblDbees.User=tblUsers.UserID # tblUsers,tblDbees #distinct(tblDbees.DbeeID),tblDbees.Type,tblDbees.Text,tblDbees.Link,tblDbees.LinkTitle,tblDbees.LinkDesc,tblDbees.UserLinkDesc ,tblDbees.LinkPic,tblDbees.Pic,tblDbees.PicDesc,tblDbees.Vid,tblDbees.VidDesc ,tblDbees.VidID,tblDbees.PollText ,tblDbees.Cats,tblDbees.TwitterTag,tblDbees.LastActivity,tblUsers.UserID ,tblUsers.Name,tblUsers.ProfilePic ';
			
			if($tablerd[0]=='tblGroups') return ' tblGroups.User=tblUsers.UserID # tblUsers,tblGroups #distinct(tblGroups.ID),tblGroups.GroupName,tblGroups.GroupPic,tblGroups.GroupDesc,tblGroups.GroupPrivacy,tblGroups.GroupPrivacy ,tblGroups.GroupDate,tblUsers.UserID ,tblUsers.Name,tblUsers.ProfilePic ';
			
			if($tablerd[0]=='tblDbeeComments') return ' tblDbeeComments.UserID = tblUsers.UserID AND tblDbeeComments.DbeeID = tblDbees.DbeeID # tblUsers,tblDbees,tblDbeeComments #distinct(tblUsers.UserID),tblUsers.Name,tblUsers.ProfilePic';
			
			if($tablerd[0]=='tblScoring') return ' (tblScoring.ID = tblDbeeComments.CommentID OR tblScoring.ID = tblDbees.DbeeID) AND (tblScoring.Owner = tblUsers.UserID OR tblScoring.UserID = tblUsers.UserID) #tblUsers,tblDbees,tblDbeeComments,tblScoring # tblUsers.UserID,tblUsers.Name,tblUsers.ProfilePic';

			exit;
		}*/
		//if(count($tablerd)==2){
		//echo '#'.
		$query = '';
	
		//print_r($tablerd);
		 $chkforuser		=	array_search("tblUsers",$tablerd);
		 $chkfordbee		=	array_search("tblDbees",$tablerd);
		 $chkforgrup		=	array_search("tblGroups",$tablerd);
		 $chkforcmnt		=	array_search("tblDbeeComments",$tablerd);
		 $chkforscor		=	array_search("tblScoring",$tablerd);
		 $chkformentn		=	array_search("tblactivity",$tablerd);
		 $chkforlogins		=	array_search("tbluserlogindetails",$tablerd);

		 $chkforuser		=	isset($chkforuser) ? $chkforuser : '';
		 $chkfordbee		=	isset($chkfordbee) ? $chkfordbee : '';
		 $chkforgrup		=	isset($chkforgrup) ? $chkforgrup : '';
		 $chkforcmnt		=	isset($chkforcmnt) ? $chkforcmnt : '';
		 $chkforscor		=	isset($chkforscor) ? $chkforscor : '';
		 $chkformentn		=	isset($chkformentn) ? $chkformentn : '';
		 $chkforlogins		=	isset($chkforlogins) ? $chkforlogins : '';

		//echo $chkforscor.'#'.$tablerd[$chkforgrup].' = '.$tablerd[$chkfordbee].' = '.$tablerd[$chkforcmnt].' ** ';
		 if($tablerd[$chkformentn]=='tblactivity' && (  empty($chkforscor) && empty($chkforcmnt) && empty($chkforgrup)))
			$query = 'tblactivity.act_typeId = tblDbees.DbeeID AND tblactivity.act_cmnt_id = tblDbeeComments.CommentID AND u.UserID=tblactivity.act_userId AND owner.UserID=tblactivity.act_ownerid #tblDbeeComments,tblactivity,tblDbees ,tblUsers as u, tblUsers as owner # distinct(act_id),tblDbees.DbeeID,tblDbeeComments.CommentID,tblactivity.act_type as stype, act_typeId, act_cmnt_id, act_date, `u`.`UserID` as userid, `u`.`Name` , `u`.`ProfilePic` , `owner`.`UserID` as ownerid , `owner`.`Name` as oname , `owner`.`ProfilePic` as opic #mentionnusertemplate';

		if($tablerd[$chkforscor]=='tblScoring' && (empty($chkfordbee) && empty($chkforcmnt) && empty($chkforgrup) && empty($chkformentn) ))
			$query = ' u.UserID=tblScoring.UserID AND owner.UserID=tblScoring.Owner #tblScoring ,tblUsers as u, tblUsers as owner # tblScoring.Type as stype, Score, ScoreID, ID,ScoreDate,MainDB, `u`.`UserID` as userid, `u`.`Name` , `u`.`ProfilePic` , `owner`.`UserID` as ownerid , `owner`.`Name` as oname , `owner`.`ProfilePic` as opic #scorenusertemplate';

		else if($tablerd[$chkforscor]=='tblScoring' && (empty($chkfordbee) && !empty($chkforcmnt) && empty($chkforgrup) && empty($chkformentn) ))
			$query = ' u.UserID=tblScoring.UserID AND owner.UserID=tblScoring.Owner AND tblScoring.ID=tblDbeeComments.CommentID #tblScoring ,tblUsers as u, tblUsers as owner, tblDbeeComments # DbeeID,CommentID,tblDbeeComments.Type, Score, ScoreID, ID,ScoreDate, `u`.`UserID` as userid, `u`.`Name` , `u`.`ProfilePic` , `owner`.`UserID` as ownerid , `owner`.`Name` , `owner`.`ProfilePic` #scorencommenttemplate ';

		else if($tablerd[$chkforscor]=='tblScoring' && (!empty($chkfordbee) && empty($chkforcmnt) && empty($chkforgrup) && empty($chkformentn) ))
			$query = ' u.UserID=tblScoring.UserID AND owner.UserID=tblScoring.Owner AND tblScoring.ID=tblDbees.DbeeID #tblScoring ,tblUsers as u, tblUsers as owner, tblDbees # tblScoring.Type as stype, Score, ScoreID, ID,ScoreDate,MainDB, `u`.`UserID` as userid, `u`.`Name` , `u`.`ProfilePic` , `owner`.`UserID` as ownerid , `owner`.`Name` as oname , `owner`.`ProfilePic` as opic #scorendbeetemplate';

		else if($tablerd[$chkforscor]=='tblScoring' && (!empty($chkfordbee) && !empty($chkforcmnt) && empty($chkforgrup) && empty($chkformentn) ))
			$query = ' u.UserID=tblScoring.UserID AND owner.UserID=tblScoring.Owner AND tblScoring.ID=tblDbees.DbeeID AND tblDbees.DbeeID =tblDbeeComments.DbeeID # tblScoring ,tblUsers as u, tblUsers as owner, tblDbees, tblDbeeComments # distinct(ScoreID),tblScoring.Type as stype, Score, ID,ScoreDate,MainDB, `u`.`UserID` as userid, `u`.`Name` , `u`.`ProfilePic` , `owner`.`UserID` as ownerid , `owner`.`Name` as oname , `owner`.`ProfilePic` as opic #multiscoretemplate';

		else if($tablerd[$chkforuser]=='tblUsers' && ($chkfordbee=='' && $chkforcmnt=='' && $chkforscor=='' && $chkforgrup=='' && $chkformentn=='' && empty($chkforlogins) ))
			$query = ' 1=1 # tblUsers  as u #distinct(u.UserID),Email,u.Name,u.lname,u.ProfilePic as image,u.Username,RegistrationDate,City #usertemplate';

		else if($tablerd[$chkforgrup]=='tblGroups' && (empty($chkfordbee) && empty($chkforcmnt) && empty($chkforscor) && empty($chkformentn) ))
			$query =  ' tblGroups.User=u.UserID # tblUsers  as u,tblGroups #distinct(tblGroups.ID),tblGroups.GroupName,tblGroups.GroupPic,tblGroups.GroupDesc,tblGroups.GroupPrivacy,tblGroups.GroupDate,u.UserID ,u.Name,u.ProfilePic #grouptemplate';
		
		else if($tablerd[$chkfordbee]=='tblDbees' &&  ($chkforscor=='' && $chkforgrup=='' && $chkforcmnt=='' && $chkformentn=='' ) )
			$query =  ' tblDbees.User=u.UserID # tblUsers as u,tblDbees #distinct(tblDbees.DbeeID),tblDbees.Type as type,tblDbees.Text as description,tblDbees.Link,tblDbees.LinkTitle,tblDbees.LinkDesc,tblDbees.UserLinkDesc ,tblDbees.LinkPic,tblDbees.Pic,tblDbees.PicDesc,tblDbees.VidTitle,tblDbees.Vid,tblDbees.VidDesc ,tblDbees.VidID,tblDbees.DbTag,tblDbees.PollText ,tblDbees.Cats,tblDbees.TwitterTag,tblDbees.LastActivity,tblDbees.dburl,tblDbees.PostDate,tblDbees.GroupID,tblDbees.Active,u.UserID ,u.Name as username,u.lname,u.ProfilePic as image #dbeetemplate';

		else if($tablerd[$chkforcmnt]=='tblDbeeComments' && empty($chkforscor) && empty($chkforgrup) && empty($chkformentn) )
			$query =  'tblDbeeComments.DbeeOwner = owner.UserID AND tblDbeeComments.UserID = u.UserID AND tblDbeeComments.DbeeID = tblDbees.DbeeID # tblUsers as owner,tblUsers as u,tblDbees,tblDbeeComments #distinct(tblDbeeComments.CommentID),tblDbeeComments.DbeeID,tblDbeeComments.Type,tblDbeeComments.Comment,tblDbeeComments.Link,tblDbeeComments.LinkTitle,tblDbeeComments.LinkDesc,tblDbeeComments.UserLinkDesc ,tblDbeeComments.LinkPic,tblDbeeComments.Pic,tblDbeeComments.PicDesc,tblDbeeComments.Vid,tblDbeeComments.VidDesc ,tblDbeeComments.VidID,tblDbees.LastActivity,tblDbees.dburl,u.UserID ,u.Name,u.ProfilePic,owner.UserID as ouid ,owner.Name as oname #commenttemplate';	
		else if($tablerd[$chkforlogins]=='tbluserlogindetails' && (empty($chkforgrup) && empty($chkfordbee) && empty($chkforcmnt) && empty($chkforscor) && empty($chkformentn) ))
			$query =  ' tbluserlogindetails.userid 	=u.UserID # tblUsers  as u,tbluserlogindetails #count(tbluserlogindetails.userid) as totLogins,tbluserlogindetails.logindate,tbluserlogindetails.logoutdate,u.UserID ,u.full_name as Name,u.ProfilePic,u.Email,u.RegistrationDate #usertemplate';
		return $query;		

		//}
	}


	public function getSyntax_CM($type)
	{
		switch($type)
		{ 
			case 'Is':
	          return  '=';
	          break;
			case 'Exactly':
	          return  '=';
	          break;
	        case 'Is Not':
	          return   '!=';
	          break;
	        case 'Similar to':
	          return  'like';
	          break; 
	        case 'Containing':
	          return  'like';
	          break; 
	        case 'sameas':
	          return   '=';
	          break;  
	        case 'Before':
	          return   '<';
	          break;  
	        case 'After':
	          return   '>';
	          break;
	        case 'On':
	          return   '=';
	          break;
	        case 'By':
	          return  '=';
	          break; 
	         default :
	         return  '=';
	         break; 
	          

	    }
	}

	public function getExpertInfo_CM($type)
	{
		switch($type)
		{
			case '0' :
				 return  '  Posts when user designated as expert';
				break;
			case '1' :
				 return  '  Posts when user designated self as expert';
				break;
			case '2' :
				 return  ' Posts when user designated an other as expert';
				break;
			default :
				 return  ' ';
				break;	
		}
		//return ret;
	}
	public function getSocialType_CM($type)
	{
		switch($type)
		{
			case '0' :
				 return  ' <a class="socialSpecialIcons ssdbIcon" href="javascript:void(0);"></a>';
				break;
			case '1' :
				 return  ' <a class="socialSpecialIcons ssfbIcon" href="javascript:void(0);"></a>';
				break;
			case '2' :
				 return  ' <a class="socialSpecialIcons sstwIcon" href="javascript:void(0);"></a>';
				break;
			case '3' :
				 return  ' <a class="socialSpecialIcons sslnIcon" href="javascript:void(0);"></a>';
				break;
			case '4' :
				 return  ' <a class="socialSpecialIcons ssdbIcon" href="javascript:void(0);"></a>';
				break;		
			default :
				 return  ' <a class="socialSpecialIcons ssdbIcon" href="javascript:void(0);"></a>';
				break;	
		}
		//return ret;
	}
    public function getgroupidfromType_CM($type)
	{
		switch($type)
		{
			case 'Open' :
				 return  '1';
				break;
			case 'Request' :
				 return  '3';
				break;
			case 'Closed' :
				 return  '2';
				break;
			default :
				 return  '1';
				break;	
		}
		//return ret;
	}
	public function getgroupType_CM($type)
	{
		switch($type)
		{
			case '1' :
				 return  'Open';
				break;
			case '2' :
				 return  'Private';
				break;
			case '3' :
				 return  'Request';
				break;
			case '4' :
				 return  'VIP group';
				break;	
			default :
				 return  ' Group ';
				break;	
		}
		//return ret;
	}

	public function getspecialdbType_CM($type)
	{
		switch($type)
		{
			case '1' :
				 return  'Open';
				break;
			case '3' :
				 return  'Request';
				break;
			case '2' :
				 return  'Closed';
				break;
			default :
				 return  ' Open ';
				break;	
		}
		//return ret;
	}
	
	public function getdbeeType_CM($type)
	{
		switch($type)
		{
			case '1' :
				 return  'Text';
				break;
			case '2' :
				 return  'URL link';
				break;
			case '3' :
				 return  'Image';
				break;
			case '4' :
				 return  'Video';
				break;
			case '5' :
				 return  'Polls';
			case '6' :
				 return  'Video';	 
				break;
			case '7' :
				 return  'Survey';	 
				break;
			case '8' :
				 return  'Posts';	 
				break;	
			case '9' :
				 return  'Event posts';	 
				break;		
			default :
				 return  '  ';
				break;	
		}
		//return ret;
	}

	public function getscoreType_CM($type)
	{
		$layoutsobj = new Admin_Model_Layouts();
		$scoreset =  $layoutsobj->scoringFromDb();
		switch($type)
		{
			case '1' :
				 return  $scoreset['Love'];
				break;
			case '2' :
				 return  $scoreset['Like'];
				break;
			case '4' :
				 return  $scoreset['Dis Like'];
				break;
			case '5' :
				 return  $scoreset['Hate'];
				break;
			default :
				 return  ' Score ';
				break;	
		}
		//return ret;
	}
	
	public function getdbeeidfromType_CM($type)
	{
		switch($type)
		{
			case 'Text' :
				 return  '1';
				break;
			case 'Link' :
				 return  '2';
				break;
			case 'Pix' :
				 return  '3';
				break;
			case 'Media' :
				 return  '4';
				break;
			case 'Polls' :
				 return  '5';
				break;
			default :
				 return  '1';
				break;	
		}
		//return ret;
	}

	public function getscoreidfromType_CM($type)
	{
		switch($type)
		{
			case 'Love' :
				 return  '1';
				break;
			case 'Like' :
				 return  '2';
				break;
			case 'F O T' :
				 return  '3';
				break;
			case 'Dis Like' :
				 return  '4';
				break;
			case 'Hate' :
				 return  '5';
				break;
			default :
				 return  '2';
				break;	
		}
		//return ret;
	}



    //*************** For Global search *******************
	/*
		@ Function is responsible for :
		@ Taking All records according to searching query etc....
		
		@ Date 30 -Apr 2013
	*/ 

	public function getvaluetodrawchart_CM($dbee,$cmnt,$score,$grp)
	{
		
		if($dbee>0)
		{
			return 1;
		}
		else if ($cmnt>0) {
			return 1;
		}
		else if ($score>0) {
			return 1;
		}
		else if ($grp>0) {
			return 1;
		}
		else {
			return 0;
		}
	}	

	/*public function addusertogroup_cm()
	{
		$common	 	=	new Admin_Model_Common();
		$Groupdropdown = $common->Groupdropdown(); 
			if(empty($Groupdropdown)){
				$notfound = '<div class="noFound">No groups found</div>';
					$groupFound='none';
			}
				
		$content ='<div class="dropDown" style="float:right;"><a href="javascript:void(0);" class="btn dropDownTarget "><i class="fa fa-plus fa-lg"></i> &nbsp; Add user to group</a>
		<div class="dropDownList groupinsertWrapper right">
		<div id="groupuserinsert">
		<div class="grpWrapperBox">
		<div class="createdGrpDrp" style="display:'.$groupFound.';"> '.$Groupdropdown.'</div>
		<div class="creatGWrap">
		<div class="saveFilterWrapper">
		<div class="subPopupContainer">
		<h2>Create group</h2>
		<div class="formRow">
		<input type="text" maxlength="20" name="filterName" id="gname" placeholder="enter group name" value="">
		</div>
		<div class="formRow">
		<textarea id="grpDescription" placeholder="enter group Discription" value=""></textarea>
		</div>
		</div>
		<div class="popupFooterWrapper">
		<button id="saveGrpName" class="btn btn-green" type="button"><i class="fa fa-group"> </i>Save group</button>
		<button class="btn cancelSaveFilter" type="button"><i class="fa fa-times-sign"> </i>Cancel</button>
		</div>
		</div>
		</div>'.$notfound.'';
		$content .='<button type="button" class="btn btn-green fluidBtn" id="addgroupBtn" dataAfter="or" style="display:'.$groupFound.'">';
		$content .='<i class="fa fa-plus fa-lg"></i>&nbsp; Save to existing group </button>';
		$content .='<a href="javascript:void(0)" class="btn fluidBtn" id="usergrouplinkforadd"><i class="fa fa-group fa-lg"></i> &nbsp; + Add new group and auto save</a>
		</div>
		</div></div></div>';
		return $content;
			
	}*/
	

	public function reportusers_CM($chartDataResult,$category='',$filename='',$count='',$action='',$newfilename='',$cattype='',$csvarray='',$total='',$extracondition='')
	{	
		if($action=='') { $action='countrycontainer'; }	
	
		$groupFound='block';

		
		if($count<20)  // checking for pagignation
		{
			$retResult .='<div class="rpGraphTop clearfix">
						<h2 class="pull-left">'.$category.'</h2>
						
						
						<form action="'. BASE_URL.'/admin/Reporting/getcsv" method="post">
							<a class="rpCsvExport pull-right " href="javascript:void(0)" >
								Export all users CSV <span class="kcSprite CsvIcon"></span> 
							</a>
							<input type="hidden" name="provider" value="'.htmlentities($filename).'">
							<input type="hidden" name="continentcode" value="'.htmlentities($filename).'">
							<input type="hidden" name="category" value="'.htmlentities($newfilename).'">
							<input type="hidden" name="cattype" value="'.$cattype.'">
							<input type="hidden" name="action2" value="'.$action.'">';

							$retResult .='<input type="hidden" name="calling" value="countryusers">
              <input type="hidden" name="filename" value="'.$filename.'">
							<input type="hidden" name="exportfilename" value="'.$newfilename.'">
							<input type="hidden" name="records" value="'.htmlentities(json_encode($csvarray)).'">

						</form>
					</div>'; 


			$retResult .= '<div class="grpTopTabel"> 
			
				<label  style="margin-left:20px; display:none"><input type="checkbox" name="goupusermain" class="goupusermain" id="goupusermain" value="allOnPage" /><label for="goupusermain"></label>Select all in view</label>
			<div id="selmsgrep" class="selmsgrep">Total: '.$total.'</div>
			<div id="grouplist"></div>';
			
			$retResult .= '<div class="reportAddToGroupWrp">'.$this->addtogroupbutton().'</div>'; // pass parameters to change bt
			if($total>20)
			$totalrec = $count+20;
			else 
				$totalrec = $total;
			$retResult	.='
			<div class="responsiveTable"><table class="reportingTable table-border table table-hover table-stripe">
			<thead>
				<tr>
					<td><label title="Select all"><input type="checkbox" name="goupusermain" id="tlallresult" class="goupusermain" value="allInResults" /><label for="tlallresult"></label></label></td>
					<td class="rpfirstTd">
						<div class="searchInRpTable">
							<input type="text" class="searchByName" placeholder="type name to filter list"  onkeyup="javascript:filterReportUser(this)" value=""/> 
							Name <div class="sprite searchIcon2"></div>
						</div>
					</td>';
			$retResult	.='<td>Email</td>';
			
			if($extracondition == 'pie' )
				$retResult	.='<td>Share count</td>';
			else if($chartDataResult[0]['sharetype']!='' )
				$retResult	.='<td>Shared on</td>';
			else if($action=='eachdaylogins' || $action=='trackingvisitsfilter' )
				$retResult	.='<td>Total logins</td>';
			else{
				$retResult.='';
			}
			$retResult	.='</tr></thead>';
		}	
		
		foreach ($chartDataResult as $key => $value) {

			
			$retResult	.='<tr>
			<td width="20">
				<label>
					<input type="checkbox" name="goupuserid" class="goupuser" value="'.$value['UserID'].'" id="goupuser'.$value['UserID'].'" />
					<label></label>
				</label>
			</td>
			<td>
			'. $this->myclientdetails->customDecoding(htmlentities($value['Name'])).' '.$this->myclientdetails->customDecoding(htmlentities($value['lname'])).'
			</td>';
			$retResult	.='<td>'. $this->myclientdetails->customDecoding(htmlentities($value['Email'])).'</td>';

			if($extracondition == 'pie' )
			{
				$format = '';
				//if($value['count']>1) $format = 'times'; else $format = 'time';
				$retResult	.='<td>'.$value['count'].' '.$format.'</td>';
			}
			else if($chartDataResult[0]['sharetype']!='' )
				$retResult	.='<td>'. htmlentities($value['sharetype']).'</td>';
			else if($chartDataResult[0]['sharetype']!='' )
				$retResult	.='<td>'. htmlentities($value['sharetype']).'</td>';
			else if($action=='eachdaylogins'  ){

        $ne  = explode("to", $filename);
        if($ne[1]!=''){
           $ne2  = explode("  ", trim($ne[0]));
           $to    = date('Y-m',strtotime($ne[1]));
           //echo $to.'-'.$ne2[0];
           $date  = date('Y-m-d',strtotime($to.'-'.$ne2[0]));
           $condition = "DATE_FORMAT(`logindate`, '%Y-%m-%d') = '".date('Y-m-d',strtotime($date))."'" ;
        }
        else
        {
            $condition = "DATE_FORMAT(`logindate`, '%Y-%m-%d') = '".date('Y-m-d',strtotime($filename))."'" ;
        }

			   $myQue = "select count(id) as totLoginsTimes from tbluserlogindetails where userid=".$value['UserID']." AND clientID=".clientID." AND ".$condition; 

				$totalosRec	=	$this->myclientdetails->passSQLquery ($myQue);
				$times = ($totalosRec[0]['totLoginsTimes']>1)?' times':' time'; 
				$retResult	.='<td>'.$totalosRec[0]['totLoginsTimes'].' </td>';
			}
      else if($action=='trackingvisitsfilter'  ){
        $expFile   = explode("to", $filename);
        
        if($expFile[1]=='') $condition = "DATE_FORMAT(`logindate`, '%Y-%m') = '".$filename."'" ;
        else $condition = "DATE_FORMAT(`logindate`, '%Y-%m-%d') >= '".date('Y-m-d',strtotime($expFile[0]))."' AND DATE_FORMAT(`logindate`, '%Y-%m-%d') <= '".date('Y-m-d',strtotime($expFile[1]))."'" ;
        
        $myQue = "select count(id) as totLoginsTimes from tbluserlogindetails where userid=".$value['UserID']." AND clientID=".clientID." AND ".$condition; 

        $totalosRec = $this->myclientdetails->passSQLquery ($myQue);
        $times = ($totalosRec[0]['totLoginsTimes']>1)?' times':' time'; 
        $retResult  .='<td>'.$totalosRec[0]['totLoginsTimes'].' </td>';
      }
			$retResult	.='</tr>';
		}
		$paginbttonmdiv = '';
		if( count($chartDataResult) > 19){
		$paginbttonmdiv = '<a id="viewmorereport" limit="20" offset="'.($count+20).'" provider="'.htmlentities($filename).'" action="'.$action.'" category="'.htmlentities($newfilename).'" cattype="'.$cattype.'" >View More </a>';
		
		}
		$colspan= ($action=='socialusers' || $action=='totalsocialusers' || $action=='eachdaylogins' || $action=='trackingvisitsfilter') ? 4 : 3;
		
			$retResult	.='<tr><td colspan="'.$colspan.'" style="text-align:center;font-weight:bold;"> '.$paginbttonmdiv.'
			<div style="float:right">Showing '.($count + count($chartDataResult)).' of '.$total.'</div></td></tr>';
		
	
		
		$retResult	.='<input type="hidden" name="provider" id="provider" value="'.htmlentities($filename).'">
							<input type="hidden" name="continentcode" id="continentcode" value="'.htmlentities($filename).'">
							<input type="hidden" name="category"  id="category" value="'.htmlentities($newfilename).'">
							<input type="hidden" name="cattype" id="cattype" value="'.$cattype.'">
							<input type="hidden" name="action2" id="action2" value="'.$action.'">
							<input type="hidden" name="countreport" id="countreport" value="'.$total.'">
							<input type="hidden" name="calling" value="countryusers"></table></div>';
		
		return $retResult;
	}
	public function getusersoncsv_CM($proparray,$filename='',$calling='',$sqlParam='')
	{
		$separator = ",";


    if($calling=='eachdaylogins' || $calling=='trackingvisitsfilter')
    {
      $ne  = explode("to", $sqlParam);
      $filter   = 0;
      if($ne[1]!=''){ $ne2  = explode(" ", $ne[0]); }
        if(strlen($ne2[0])!=10){
          $str = $ne[0];
        
          if($ne2[2]!=''){
            $str = $ne2[0].'-'.substr($ne2[2], 3);
          }
          $replca = str_replace(' ', '_', $str);
          $replca = str_replace('-', '_', $replca);
          $savename =  "LOGINS_".str_replace(',', '_', $replca);
        }
        else{
          $savename =  "LOGINS_".str_replace(' ', '_', $sqlParam);
          $filter   = 1;
        }

        
    } else $savename = $filename;

  
		header("Content-type: text/x-csv");
		header("Content-Disposition: attachment; filename=".$savename.".csv");
		header("Pragma: no-cache");
		header("Expires: 0");

		//	print "ID $separator";
		print "NAME $separator";
		print "EMAIL ";
		if($calling=='usersection') { print "$separator REGISTRATION "; } 

    if($calling=='eachdaylogins' || $calling=='trackingvisitsfilter')
    {
      print "$separator TOTAL LOGINS ";
    }

		print "\n";

		$line = '';
	    

	    foreach($proparray as $value )
	    {                                            
	        //	 $id = htmlentities($value['UserID']) ;
	        $Name = htmlentities($value['Name']);
	        $Email =  htmlentities($value['Email']);
	        if($calling=='usersection') { $Reg =  date('d/n/Y',strtotime($value['RegistrationDate'])) ; }
	         print $this->myclientdetails->customDecoding($Name) . $separator;
			     print $this->myclientdetails->customDecoding($Email) ;
    			if($calling=='usersection') { print $separator. $Reg ; } 
          
          if($calling=='eachdaylogins' )
          {
              $ne  = explode("to", $filename);
              if($ne[1]!=''){
                 $ne2  = explode("  ", trim($ne[0]));
                 $to    = date('Y-m',strtotime($ne[1]));
                 //echo $to.'-'.$ne2[0];
                 $date  = date('Y-m-d',strtotime($to.'-'.$ne2[0]));
                 $condition = "DATE_FORMAT(`logindate`, '%Y-%m-%d') = '".date('Y-m-d',strtotime($date))."'" ;
              }
              else
              {
                  $condition = "DATE_FORMAT(`logindate`, '%Y-%m-%d') = '".date('Y-m-d',strtotime($filename))."'" ;
              }

              $myQue = "select count(id) as totLoginsTimes from tbluserlogindetails where userid=".$value['UserID']." AND clientID=".clientID." AND ".$condition; 

              $totalosRec = $this->myclientdetails->passSQLquery($myQue);

              print $separator. $totalosRec[0]['totLoginsTimes'] ;
          }
          if($calling=='trackingvisitsfilter')
          {
              
              if($filter  == 1)
              {
                $ne  = explode("to", $sqlParam);
                $condition = "DATE_FORMAT(`logindate`, '%Y-%m-%d') >= '".date('Y-m-d',strtotime($ne[0]))."' AND DATE_FORMAT(`logindate`, '%Y-%m-%d') <= '".date('Y-m-d',strtotime($ne[1]))."'" ;
              }
              else
              {
                $ne  = explode(" ", $sqlParam);
                if($ne[1]==''){
                   $condition = "DATE_FORMAT(`logindate`, '%Y-%m') = '".date('Y-m',strtotime($sqlParam))."'" ;
                }
                else
                {
                    $condition = "DATE_FORMAT(`logindate`, '%Y-%m-%d') = '".date('Y-m-d',strtotime($sqlParam))."'" ;
                }
              }
                

              $myQue = "select count(id) as totLoginsTimes from tbluserlogindetails where userid=".$value['UserID']." AND clientID=".clientID." AND ".$condition; 

              $totalosRec = $this->myclientdetails->passSQLquery($myQue);

              print $separator. $totalosRec[0]['totLoginsTimes'] ;
          }
          
          print "\n" ;
    	}
	    exit;
	   
	}

	public function grouptypes_CM()
	{
		$db = $this->getDbTable();
		//$db is an instance of Zend_Db_Adapter_Abstract
		$select = $db->select();		
		$select->setIntegrityCheck( false );
		$select->from( array('gt' => 'tblGroupTypes'),  array('ID'=>'gt.TypeID','TypeName' =>'gt.TypeName'));
		return $result = $db->fetchAll($select)->toarray();	
	}
	
	public function chkemail($email)
	{
		$db = $this->getDbTable();
		$select = $db->select();
		$select->setIntegrityCheck( false );
		$select->from( array('tblUsers'),  array('Email','Name'));
		$select->where('Email=?',$email)->where('Status=?','1');
		//echo $select->__toString();
		
		$result = $db->fetchRow($select);
		if($result['Email']!=''){
			return true;
		}else{
			
			return false;
		}
	}
	
	public function uploadFilterWordcsvOpration($file)
	{
		set_time_limit(0);
		$myclientdetails = new Admin_Model_Clientdetails();
		$deshboard   = new Admin_Model_Deshboard();
		$filename	=	$file['tmp_name'];
		$linecount  = count(file($filename));
	 	
	 	if($linecount>1002)
	 	{
	 		return "404";
	 		exit();
	 	}

		$filehandle = 	fopen($filename, "r");
		
		$datas 		=	array();
		$failedrec 	= 	array();
		$cnt		=	0;
		$errorchk	=	0; 
		while (($data1 = fgetcsv($filehandle, 1000, ",")) !== FALSE) 
		{
			//print_r($data1);
			$cnt++;	 // Counter for total records
			if($cnt>0 )  // Condition to avoid first 2 lines of csv fine
			{
				$ab	=	implode(',', $data1);
				$data	=	explode(',',$ab);
				
				$Word 	= (trim($data[0])!=' ')? (trim($data[0],'"')):'';
				if($Word=='FILTER WORDS')  $failedrec[] = '';
				else if($Word!='')
				{
					$dataval = array('name'=> trim($Word),'status'=> 1,'clientID'=>clientID);
				
					$myclientdetails->insertdata_global('tblProfanityFilter',$dataval);
				}
				else
					$failedrec[]	= array('error'=>'true', 'msg'=>'<tr class="errorRow"><td>'.$uName.'</td><td>'.$uEmail.'</td><td>filter word should not be empty</td></tr>');
			}
		}
		return $failedrec;
		
	}	

	public function uploadcsvOpration($file,$user_id,$calle,$usertype="")
	{
		//print_r($file); exit();
		set_time_limit(0);
		$db = $this->getDefaultAdapter();
		$inviteModal=	new Admin_Model_Invite();
		$userModal	=	new Admin_Model_User();
		$common		=   new Admin_Model_Common();
		$deshboard   = new Admin_Model_Deshboard();
		$filename	=	$file['tmp_name'];
		$linecount  = count(file($filename));
		$importValues = '';

		$curDate = date('Y-m-d');
		$csvlimit = "SELECT COUNT(*) AS TotalSentToday FROM `tblUsers` WHERE clientID=".clientID." AND DATE_FORMAT(`RegistrationDate`,'%Y-%m-%d')='".$curDate."'  AND fromcsv=1";
		$todaysLim = $this->myclientdetails->passSQLquery($csvlimit);
		if($todaysLim[0]['TotalSentToday']<1001)
		{
			$chkLimit = $todaysLim[0]['TotalSentToday']+$linecount;
			if($chkLimit>1001)
			{
				return 'limit exhausted';
			}
		}
		else return 'limit exhausted';
	 	
	 	if($linecount>502)
	 	{
	 		return "404";
	 		exit();
	 	}

		$filehandle = 	fopen($filename, "r");
		
		$datas 		=	array();
		$failedrec 	= 	array();
    $chkDuplicate = array();
		$cnt		=	0;
		$errorchk	=	0; 
		$bunchcount =   0;
		//echo "<pre>";
		while (($data1 = fgetcsv($filehandle, 1000, ",")) !== FALSE) 
		{
			$cnt++;	 // Counter for total records
			if($cnt>0 )  // Condition to avoid first 2 lines of csv fine
			{
				$data = $data1;
				//$ab	=	implode(',', $data1);
				//$data	=	explode(',',$ab);
				if($calle=='ADD VIP' || $calle=='VIP GROUP')
				{
					$uName 		= (trim($data[0])!=' ')? (trim($data[0],'"')):'';
					$Lname		= (trim($data[1])!=' ')? (trim($data[1],'"')):'';
					$Company	= (trim($data[2])!=' ')? (trim($data[2],'"')):'';
					$jobtitle	= (trim($data[3])!=' ')? (trim($data[3],'"')):'';
					$uEmail		= (trim($data[4])!=' ')? (trim($data[4],'"')):'';
					$Status		= (trim($data[5])!=' ')? (trim($data[5],'"')):'';
				}	else if($calle=='ADD USERS CARERID'){
					$uName 		= (trim($data[0])!=' ')? (trim($data[0],'"')):'';
          $uEmail   = (trim($data[1])!=' ')? (trim($data[1],'"')):'';
					$carerid	= (trim($data[2])!=' ')? (trim($data[2],'"')):'';
         
				}
        else {
          $uName    = (trim($data[0])!=' ')? (trim($data[0],'"')):'';
          $uEmail   = (trim($data[1])!=' ')? (trim($data[1],'"')):'';
        }
				
				//if($uName=='NOM' || $uName=='Fname' || $uEmail=='EMAIL' || $uEmail=='Email ')  $failedrec[] = '';  // avoid header line
				if($cnt==1) $failedrec[] = '';
				else if(!empty($uEmail) && (bool)preg_match("`^[a-z0-9!#$%&'*+\/=?^_\`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_\`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$`i", trim($uEmail)) )
				{
					$encodedEmail = $this->myclientdetails->customEncoding($uEmail);
					$encodedName  = $this->myclientdetails->customEncoding($uName);				
					if($calle=='Invite')
					{
						$availUsers	  =	 $userModal->chkUsersExist($encodedEmail);
				  	 	$availInvited =	 $inviteModal->chkInviteExist($encodedEmail);

				   	 	if($availUsers<1 && $availInvited<1)
				   	 	{
			   	 			$dataval  = array(
			   	 						'clientID' => clientID,
				   	 					'inv_name'=> $encodedName,
				   	 					'inv_email'=>$encodedEmain,
				   	 					'inv_by'=> $user_id,
				   	 					'inv_added'=> date("Y-m-d H:i:s"),
				   	 					);
			   	 			
			   	 			$Insertdataadmin	=	$inviteModal->insertdata($dataval);

			   	 			$this->getdeleteemailtempate($uName,$uEmail,'inviteuser');

			   	 			//$this->send_email('info@dbee.me', $uEmail, 'You have been invited', 'Body Mail');
			   	 		}
			   	 		else {
			   	 			$failedrec[]	= '<tr><td>'.$uName.'</td><td>'.$uEmail.'</td><td><span class="greenTxt">already invited</span></td></tr>';	
			   	 		}
			   	 	}
			   	else if($calle=='ADD USERS')
					{	
						$password = getrandmax(); 
				  	 	$avail	  =	 $userModal->chkUsersExist($encodedEmail);

				   	 	if($avail<1)
				   	 	{
				   	 		$token 		=  '';
				   	 		//$password = rand(100000,999999);
				   	 		$spuname  	=  split('@', $uEmail);
					  	 	$spname 	= explode(' ', $uName);
					  	 	$usname 	=	$spuname[0].rand(1000,9999);
					  	 	$token 		=  	md5(round(microtime(true) * 1000).$uEmail.clientID.$this->_generateHash($uEmail));

				   	 		/*$dataval  = array(
				   	 					'clientID' => clientID,
				   	 					'Name'=> $encodedName,
				   	 					'Email'=>$encodedEmail,
				   	 					'Pass'=> '',//$this->_generateHash($password),
				   	 					'Username'=> $this->myclientdetails->customEncoding($usname),
				   	 					'Signuptoken'=>$token,
				   	 					'RegistrationDate'=> date("Y-m-d H:i:s"),
				   	 					'Status' => 0,
				   	 					'emailsent'=>0
				   	 					);*/
				   	 		$importValues .= '(';
							$importValues .= clientID.','.$db->quote($encodedName).','.$db->quote($encodedName).','.$db->quote($encodedEmail).',"",'.$db->quote($this->myclientdetails->customEncoding($usname)).',"'.$token.'","'.date("Y-m-d H:i:s").'",0,0,1,1,6';
							$importValues .= '),';
								
							$chkDuplicate[$uEmail] = array(clientID,$db->quote($encodedName),$db->quote($encodedName),$db->quote($encodedEmail),'',$db->quote($this->myclientdetails->customEncoding($usname)),$token);
			   	 			
			   	 			//$Insertdataadmin	=	$userModal->insertdata($dataval);

			   	 			//$MailBody = $this->getemailtempate($uName,$token,$uEmail,$password);
			   	 			//sleep(2);
			   	 			//$return_noti = $deshboard->insertNotification($Insertdataadmin);
			   	 		}
			   	 		else {
			   	 				$failedrec[]	= '<tr><td>'.$uName.'</td><td>'.$uEmail.'</td><td><span class="greenTxt">already invited</span></td></tr>';	
			   	 		}
					}
          else if($calle=='ADD USERS CARERID')
          { 

            $password = getrandmax(); 
              $avail    =  $userModal->chkUsersExist($encodedEmail);

              if($avail<1)
              {
                $token    =  '';
                //$password = rand(100000,999999);
                $spuname    =  split('@', $uEmail);
                $spname   = explode(' ', $uName);
                $usname   = $spuname[0].rand(1000,9999);
                $token    =   md5(round(microtime(true) * 1000).$uEmail.clientID.$this->_generateHash($uEmail));

                $dataval  = array(
                      'clientID' => clientID,
                      'Name'=> $encodedName,
                      'Email'=>$encodedEmail,
                      'Pass'=> '',//$this->_generateHash($password),
                      'Username'=> $this->myclientdetails->customEncoding($usname),
                      'Signuptoken'=>$token,
                      'RegistrationDate'=> date("Y-m-d H:i:s"),
                      'Status' => 0,
                      'emailsent'=>0,
                      'fromcsv'=>1,
                      'lastcsvrecord'=>1,
                      'usertype'=>6,
                      );
                $importValues = '';
                
                $chkDuplicate[$uEmail] = array(clientID,$db->quote($encodedName),$db->quote($encodedName),$db->quote($encodedEmail),'',$db->quote($this->myclientdetails->customEncoding($usname)),$token);


                 $Insertdataadmin  = $userModal->insertdata($dataval);

                if($carerid)
                {
                   $this->myclientdetails->insertdata_global('tblUserMeta',array('clientID'=>clientID,'UserId'=>$Insertdataadmin,'carerid'=>$carerid,'date_added'=>date("Y-m-d H:i:s")));
                }
                
                //$Insertdataadmin  = $userModal->insertdata($dataval);

                //$MailBody = $this->getemailtempate($uName,$token,$uEmail,$password);
                //sleep(2);
                //$return_noti = $deshboard->insertNotification($Insertdataadmin);
              }
              else {
                  $failedrec[]  = '<tr><td>'.$uName.'</td><td>'.$uEmail.'</td><td><span class="greenTxt">already invited</span></td></tr>'; 
              }
          }
					else if($calle=='ADD VIP')
					{	
						//echo "string"; exit;
						$chkType = $this->myclientdetails->getAllMasterfromtable('tblUserType',array('TypeName','TypeID'),array('TypeName'=>$Status));

				  	 	$avail	  =	 $userModal->chkUsersExist($encodedEmail);

				   	 	if($avail<1 && count($chkType)>0 && ($chkType[0]['TypeID']!=10 && $chkType[0]['TypeID']!=0 && $chkType[0]['TypeID']!=6))
				   	 	{
				   	 		$token 		= '';
				   	 		//$randToken = rand(100000,999999);
				   	 		$spuname  	=  split('@', $uEmail);
					  	 	$spname 	= explode(' ', $uName);
					  	 	$uname 		=	$spuname[0].rand(1000,9999);
					  	 	$token 		=  	md5(round(microtime(true) * 1000).$uEmail.clientID.$this->_generateHash($uEmail));
			   	 			
			   	 			/*$dataval[]  = array(
			   	 						'clientID' => clientID,
				   	 					'Name'=> $encodedName,
				   	 					'lname'=>$this->myclientdetails->customEncoding($Lname),
				   	 					'title'=>$this->myclientdetails->customEncoding($jobtitle),
				   	 					'company'=>$this->myclientdetails->customEncoding($Company),
				   	 					'Email'=>$encodedEmail,
				   	 					'usertype'=>$chkType[0]['TypeID'],
				   	 					'typename'=>$chkType[0]['TypeName'],
				   	 					'Emailmakeprivate'=>1,
				   	 					'Pass'=> '',//$this->_generateHash($password),
				   	 					'Username'=> $this->myclientdetails->customEncoding($uname),
				   	 					'Signuptoken'=>$token,
				   	 					'RegistrationDate'=> date("Y-m-d H:i:s"),
				   	 					'Status' => 0,
				   	 					'emailsent'=>0
				   	 					);
				   	 		*/
							
							$fullName = $db->quote($this->myclientdetails->customEncoding($uName.' '. $Lname));
			   	 			$importValues .= '(';
							$importValues .= clientID.','. $db->quote($encodedName).','.$db->quote($this->myclientdetails->customEncoding($Lname)).','.$fullName.','.$db->quote($this->myclientdetails->customEncoding($jobtitle)).','.$db->quote($this->myclientdetails->customEncoding($Company)).','.$db->quote($encodedEmail).','.$chkType[0]['TypeID'].','.$db->quote($chkType[0]['TypeName']).',"1","",'.$db->quote($this->myclientdetails->customEncoding($uname)).',"'.$token.'","'.date("Y-m-d H:i:s").'",0,0,1,1';
							 $importValues .= '),';
							
			   	 			//$Insertdataadmin	=	$userModal->insertdata($dataval);
			   	 			//$MailBody = $this->getemailtempate($uName,$token,$uEmail,$password);
			   	 			//usleep(100);
			   	 			//$return_noti = $deshboard->insertNotification($Insertdataadmin);
			   	 			//$this->send_email('info@dbee.me', $uEmail, 'Your new dbee account', 'Body Mail');
			   	 		}
			   	 		else {
			   	 			$givemsg	= '';
			   	 			$givemsg	.= '<tr><td>'.$uName.'</td><td>'.$Lname.'</td><td>'.$Company.'</td><td>'.$jobtitle.'</td><td>'.$uEmail.'</td><td>'.$Status.'</td><td>';
			   	 		
			   	 			if($chkType[0]['TypeID']==10 || $chkType[0]['TypeID']==6)
			   	 				$givemsg	.= '<span class="redTxt">you can not use researve type</span>';
			   	 			else 		$givemsg	.= '<span style="color:red">Account already created OR Incorrect user type</span>';
			   	 			$givemsg	.= '</span></td></tr>';	
			   	 			$failedrec[] = $givemsg;
			   	 		}
					}
					else if($calle=='VIP GROUP')
					{	
						//echo "string"; exit;
						$chkType = $this->myclientdetails->getAllMasterfromtable('tblUserType',array('TypeName','TypeID'),array('TypeName'=>$Status));

				  	 	$avail	  =	 $this->myclientdetails->getfieldsfromtable(array('UserID','Name','Email','usertype'),'tblUsers','Email',$encodedEmail);
				  	 	
				   	 	if(count($avail)<1 && count($chkType)>0 && ($chkType[0]['TypeID']!=10 && $chkType[0]['TypeID']!=0 && $chkType[0]['TypeID']!=6))
				   	 	{
				   	 		
				   	 		//$password = rand(100000,999999);
				   	 		$spuname  	=  split('@', $uEmail);
					  	 	$spname 	= explode(' ', $uName);
					  	 	$uname 		=	$spuname[0].rand(1000,9999);
					  	 	$token 		= '';
					  	 	$token 		=  	md5(round(microtime(true) * 1000).$uEmail.clientID.$this->_generateHash($uEmail));
			   	 			
			   	 			/*$dataval  = array(
			   	 						'clientID' => clientID,
				   	 					'Name'=> $encodedName,
				   	 					'lname'=>$this->myclientdetails->customEncoding($Lname),
				   	 					'title'=>$this->myclientdetails->customEncoding($jobtitle),
				   	 					'company'=>$this->myclientdetails->customEncoding($Company),
				   	 					'Email'=>$encodedEmail,
				   	 					'usertype'=>$chkType[0]['TypeID'],
				   	 					'typename'=>$chkType[0]['TypeName'],
				   	 					'Emailmakeprivate'=>1,
				   	 					'Pass'=> '',//$this->_generateHash($password),
				   	 					'Username'=> $this->myclientdetails->customEncoding($uname),
				   	 					'Signuptoken'=>$token,
				   	 					'RegistrationDate'=> date("Y-m-d H:i:s"),
				   	 					'Status' => 0,
				   	 					'emailsent'=>0
				   	 					);
				   	 		*/
							
							$fullName = $db->quote($this->myclientdetails->customEncoding($uName.' '. $Lname));
			   	 			$importValues .= '(';
							$importValues .= clientID.','. $db->quote($encodedName).','.$db->quote($this->myclientdetails->customEncoding($Lname)).','.$fullName.','.$db->quote($this->myclientdetails->customEncoding($jobtitle)).','.$db->quote($this->myclientdetails->customEncoding($Company)).','.$db->quote($encodedEmail).','.$chkType[0]['TypeID'].','.$db->quote($chkType[0]['TypeName']).',"1","",'.$db->quote($this->myclientdetails->customEncoding($uname)).',"'.$token.'","'.date("Y-m-d H:i:s").'",0,0,1,1';
							 $importValues .= '),';

			   	 			//$Insertdataadmin	=	$userModal->insertdata($dataval);
			   	 			//sleep(2);
			   	 			//$MailBody = $this->getemailtempate($uName,$token,$uEmail,$password);
			   	 			//$return_noti = $deshboard->insertNotification($Insertdataadmin);
			   	 			//$this->send_email('info@dbee.me', $uEmail, 'Your new dbee account', 'Body Mail');
			   	 			//$failedrec[] = array('user_id'=>$Insertdataadmin,'uemail'=>$uEmail,'uname'=>$uName,'token'=>$token,'password'=>$password);
			   	 			$failedrec[] = array('user_id'=>$Insertdataadmin,'uemail'=>$uEmail,'token'=>$token,'password'=>$password,'uname'=>$uName,'lname'=>$Lname,'company'=>$Company,'jobtitle'=>$jobtitle,'usertype'=>$chkType[0]['TypeID'],'status'=>$Status,'position'=>'true');
			   	 		}
			   	 		else {
			   	 			//echo count($chkType).' # '.count($avail).' # '.$avail[0]['Email']. '<br>';
			   	 			$failedrec[] = array('user_id'=>$avail[0]['UserID'],'uemail'=>$uEmail,'uname'=>$uName,'lname'=>$Lname,'company'=>$Company,'jobtitle'=>$jobtitle,'usertype'=>$chkType[0]['TypeID'],'status'=>$Status,'position'=>'true');
			   	 				
			   	 		}
			   	 	}

				}
				else
				{
					if($calle=='ADD VIP' )
					{
						
						$failedrec[]	= '<tr class="errorRow"><td>'.$uName.'</td><td>'.$Lname.'</td><td>'.$Company.'</td><td>'.$jobtitle.'</td><td>'.$uEmail.'</td><td>'.$Status.'</td><td>Invalid email address</td></tr>';	
					}	
					else if($calle=='VIP GROUP')
					{
						$failedrec[] = array('user_id'=>'','uemail'=>$uEmail,'uname'=>$uName,'lname'=>$Lname,'company'=>$Company,'jobtitle'=>$jobtitle,'usertype'=>'','status'=>$Status,'position'=>'false');
					}
					else {
						$failedrec[]	= '<tr class="errorRow"><td>'.$uName.'</td><td>'.$uEmail.'</td><td>Invalid email address</td></tr>';	
					}
					//$failedrec[]	= $uName;
					
				}	
			}
		}
		//echo "<pre>";
		if($importValues!='')
		{
			//echo "<pre>"; print_r($chkDuplicate); 
			$values = '';
			$nCount = 0;
			foreach ($chkDuplicate as $key => $value) {
				$nCount++;
				if($nCount!='')
				{
					$values .= '(';
					$values .= $value[0].','.$value[1].','.$value[2].','.$value[3].',"'.$value[4].'",'.$value[5].',"'.$value[6].'","'.date("Y-m-d H:i:s").'",0,0,1,1,6';
					$values .= '),';	
				}

			}
			// echo $values1 = rtrim($importValues,","); //exit;
			// echo "<br><br>".$values = rtrim($values,","); exit;
			$values = rtrim($values,",");
			if($calle=='ADD USERS')
			{
				$updateCSV = "UPDATE tblUsers set `lastcsvrecord`=0 WHERE clientID=".clientID." AND  (`usertype`= 0 OR `usertype`=6 )";
				$import="INSERT into tblUsers(clientID,Name,full_name,Email,Pass,Username,Signuptoken,RegistrationDate,Status,emailsent,fromcsv,lastcsvrecord,usertype)values $values";
			}
      else if($calle=='ADD USERS CARERID')
      {
        // do not remove this block
      }
			else
			{
				$updateCSV = "UPDATE tblUsers set `lastcsvrecord`=0 WHERE clientID=".clientID." AND ( `usertype`!=0 AND `usertype`!=6 )";
				$import="INSERT into tblUsers(clientID,Name,lname,full_name,title,company,Email,usertype,typename,Emailmakeprivate,Pass,Username,Signuptoken,RegistrationDate,Status,emailsent,fromcsv,lastcsvrecord)values $values";
			}

			$this->getDefaultAdapter()->query($updateCSV);
			$this->getDefaultAdapter()->query($import);
		}
    $failedrec2['failedrec'] = $failedrec;
		$failedrec2['totalSuccess'] = count($chkDuplicate);
	
		//print_r($failedrec);
		return $failedrec2;
		
	}

	/*public function getemailtempate($fname,$Signuptoken,$loginid,$password)
    {
    	$body  = '<tr>
		        <td style="padding:0px 30px 30px 30px; ">
		<strong>Dear '.$fname.'</strong>,<br /> <br /><br />

		A new account has been created for you at <a href="'.BASE_URL.'" target="_blank">'.BASE_URL.'</a>.<br/><br/>

		Your login email address is as below:
		<br><br>
			login email : '.$loginid.'<br>
			password : whatever you set when logging in
		<br /><br />
		Please click on the link below to confirm your email address and activate your '.COMPANY_NAME.' account. <br /> <br />
		<a href="'.BASE_URL.'/index/activelink/link/activate/id/'.$Signuptoken.'"  target="_blank" style="color:#ff7709; font-size:24px; display:block; width:550px; word-wrap:break-word;"> '.BASE_URL.'/index/activelink/link/activate/id/'.$Signuptoken.'</a> 
		<br><br>  Please change your password under account settings immediately after login.<br/><br/>
		'.COMPANY_FOOTERTEXT.'
		        </td>
		      </tr>';

		$MailFrom='"'.SITE_NAME.'" <'.NOREPLY_MAIL.'>'; //Give the Mail From Address Here
		$MailReplyTo=NOREPLY_MAIL;
        $MailTo 	=	$loginid;
        $MailSubject = "Your ".COMPANY_NAME." account needs activation";
        $MailCharset = "iso-8859-1";
        $MailEncoding = "8bit";
        $MailHeaders  = "From: $MailFrom\n";
        $MailHeaders .= "Reply-To: $MailReplyTo\n";
        $MailHeaders .= "MIME-Version: 1.0\r\n";
        $MailHeaders .= "Content-type: text/html; charset=$MailCharset\n";
        $MailHeaders .= "Content-Transfer-Encoding: $MailEncoding\n";
        $MailHeaders .= "X-Mailer: PHP/".phpversion();
        
        $MailBody = html_entity_decode(str_replace("'","\'",$body));
        return $this->myclientdetails->sendWithoutSmtpMail($MailTo,$MailSubject,$MailFrom,$MailBody);
    }*/

	public function getemailtempate($fname,$Signuptoken,$loginid,$password,$reinvite='')
    {
    	
        if($reinvite=='reinvite'){
		   $loginid = $this->myclientdetails->customDecoding($loginid);
		   $fname = $this->myclientdetails->customDecoding($fname);
        }
    	$dbeeEmailtemplate = new RawEmailtemplate();
    	$emailTemplatemain = $dbeeEmailtemplate->dbeeEmailtemplatestatic();     	
    	$deshboard   = new Admin_Model_Deshboard();
    	$areatype ='admin';
    	$bodyContent = $deshboard->getGroupemailtemplate($areatype);
    	
        /*$databodymsg1 = array('[%%fname%%]','[%%BASE_URL%%]','[%%BASE_URL%%]','[%%loginid%%]',
        	'[%%COMPANY_NAME%%]','[%%BASE_URL%%]','[%%Signuptoken%%]','[%%COMPANY_FOOTERTEXT%%]');
        $databodymsg2 = array($fname,BASE_URL,BASE_URL,$loginid,COMPANY_NAME,BASE_URL,$Signuptoken,BASE_URL,COMPANY_FOOTERTEXT);  */

        $databodymsg1 = array('[%%fname%%]','[%%BASE_URL%%]','[%%FRONT_URL%%]','[%%loginid%%]',
        	'[%%COMPANY_NAME%%]','[%%FRONT_URL%%]','[%%Signuptoken%%]','[%%COMPANY_FOOTERTEXT%%]');
        $databodymsg2 = array($fname,FRONT_URL,FRONT_URL,$loginid,COMPANY_NAME,FRONT_URL,
        	            $Signuptoken,FRONT_URL,COMPANY_FOOTERTEXT); 

        $bodyContentmsg = str_replace($databodymsg1,$databodymsg2,$bodyContent[0]['body']);
		
		$datasub1 = array('[%%COMPANY_NAME%%]');
        $datasub2 = array(COMPANY_NAME);
        $subjectMsg = str_replace($datasub1,$datasub2,$bodyContent[0]['subject']);
        $footerContentmsg = $bodyContent[0]['footertext'];

        $data1 = array( '[%bannerfreshimg%]','[%%body%%]','[%%footertext%%]');

        $data2 = array( '',$bodyContentmsg,$footerContentmsg);
            $messagemail = str_replace($data1,$data2,$emailTemplatemain);
            $from='"'.SITE_NAME.'" <'.NOREPLY_MAIL.'>';
            $replyto = NOREPLY_MAIL;
            $to = $loginid; 
            $setSubject = $subjectMsg;
            $setBodyText = html_entity_decode($messagemail);                                 
          
            $this->myclientdetails->sendWithoutSmtpMail($to,$setSubject,$from,$setBodyText);

    }

    public function getemailtempateroleree($loginid,$Name,$Username)
    {
    	//echo $Name.'~'.$Username.'~'.$loginid;die;
    	$fname = $Name;
    	$username=$Username;
    	$password='whatever you set when logging in';
    	$dbeeEmailtemplate = new RawEmailtemplate();
    	$emailTemplatemain = $dbeeEmailtemplate->dbeeEmailtemplatestatic();     	
    	$deshboard   = new Admin_Model_Deshboard();
    	$areatype ='admin';
    	$bodyContent = $deshboard->getGroupemailtemplaterole($areatype);
        $databodymsg1 = array('[%%fname%%]','[%%BASE_URL%%]','[%%FRONT_URL%%]','[%%username%%]','[%%password%%]',
        	'[%%COMPANY_NAME%%]','[%%COMPANY_FOOTERTEXT%%]');
        $databodymsg2 = array($fname,FRONT_URL,FRONT_URL,$username,$password,COMPANY_NAME,COMPANY_FOOTERTEXT); 
        $bodyContentmsg = str_replace($databodymsg1,$databodymsg2,$bodyContent[0]['body']);	
		$datasub1 = array('[%%COMPANY_NAME%%]');
        $datasub2 = array(COMPANY_NAME);
        $subjectMsg = str_replace($datasub1,$datasub2,$bodyContent[0]['subject']);
        $footerContentmsg = $bodyContent[0]['footertext'];
        $data1 = array('[%%body%%]','[%%footertext%%]');
        $bodyContentmsg = html_entity_decode($bodyContentmsg);
        //$data2 = array($bodyContentmsg,$footerContentmsg);
        $data2 = $this->dbeeComparetemplate($bodyContentmsg,$footerContentmsg);
        $from='"'.SITE_NAME.'" <'.NOREPLY_MAIL.'>';
        $replyto = NOREPLY_MAIL;
        $to = $loginid; 
        $setSubject = $subjectMsg;
        $setBodyText = html_entity_decode($data2);
        $this->myclientdetails->sendWithoutSmtpMail($to,$setSubject,$from,$setBodyText);
    }

    public function getemailtempaterole($fname,$username,$Signuptoken,$loginid,$password,$reinvite='')
    {
    	/*echo $fname.'``'.$Signuptoken.'``'.$loginid.'``'.$password.'``'.$reinvite;die;*/
    	$dbeeEmailtemplate = new RawEmailtemplate();
    	$emailTemplatemain = $dbeeEmailtemplate->dbeeEmailtemplatestatic();     	
    	$deshboard   = new Admin_Model_Deshboard();
    	$areatype ='admin';
    	$bodyContent = $deshboard->getGroupemailtemplaterole($areatype);
        $databodymsg1 = array('[%%fname%%]','[%%BASE_URL%%]','[%%FRONT_URL%%]','[%%username%%]','[%%password%%]',
        	'[%%COMPANY_NAME%%]','[%%FRONT_URL%%]','[%%COMPANY_FOOTERTEXT%%]');
        $databodymsg2 = array($fname,FRONT_URL,FRONT_URL,$username,$password,COMPANY_NAME,COMPANY_FOOTERTEXT); 

        $bodyContentmsg = str_replace($databodymsg1,$databodymsg2,$bodyContent[0]['body']);
		
		$datasub1 = array('[%%COMPANY_NAME%%]');
        $datasub2 = array(COMPANY_NAME);
        $subjectMsg = str_replace($datasub1,$datasub2,$bodyContent[0]['subject']);

        $footerContentmsg = $bodyContent[0]['footertext'];
        $data1 = array('[%%body%%]','[%%footertext%%]');
        $data2 = array($bodyContentmsg,$footerContentmsg);
        $messagemail = str_replace($data1,$data2,$emailTemplatemain);
        /****************/
         /*$bodyContentmsg = html_entity_decode($bodyContentmsg);
         $messagemail = $this->dbeeComparetemplate($bodyContentmsg,$footerContentmsg);*/
        /****************/
            $from='"'.SITE_NAME.'" <'.NOREPLY_MAIL.'>';
            $replyto = NOREPLY_MAIL;
            $to = $loginid; 
            $setSubject = $subjectMsg;
            $setBodyText = html_entity_decode($messagemail);
            $this->myclientdetails->sendWithoutSmtpMail($to,$setSubject,$from,$setBodyText);

    }


    public function dbeeComparetemplate($bodyContentmsg,$footerContentmsg){
        //$deshboard   = new Admin_Model_Deshboard();
        $dbeeEmailtemplate = new RawEmailtemplate();
        $emailTemplatemain = $dbeeEmailtemplate->dbeeEmailtemplate();
        $emailTemplatejson = $this->myclientdetails->getfieldsfromtable(array('id','emailtemplatejson','htmllayout'),'adminemailtemplates','clientID',clientID);
        $bodyContentjson = $emailTemplatejson[0]['emailtemplatejson'];
        
        if($bodyContentjson==''){
                    $bodyContentjsonval = Array(
                                                'fontColor' => 'e4e4f0',
                                                'background' => 'e8e9eb', 
                                                'bodycontentfontColor' =>'e4e4f0',
                                                'bodycontentbacgroColor' =>'e8e9eb',
                                                'bodycontenttxture' =>'',
                                                'headerfontColor' =>'333',
                                                'headerbacgroColor' =>'fff',
                                                'headertxture' =>'',
                                                'contentbodyfontColor' => '333',
                                                'contentbodybacgroColor' => 'fff',
                                                'contentbodytxture' =>'',
                                                'bannerfreshimg' =>'dblogo-black.png',
                                                'footerfontColor' => '333',
                                                'footerbacgroColor' => 'd3d3d3',
                                                'footertxture' => '',
                                                'footerMsgval' => 'powered by db corporate social platforms'
                                               );
                    $bannerfreshimgdef ='dblogo-black.png';
        }
        else
        { 
            $bodyContentjsonval = json_decode($bodyContentjson, true);
            //$bannerfreshimgdef ='http://www.db-csp.com/img/dblogo-black.png';
            $bannerfreshimgdef = $bodyContentjsonval['bannerfreshimg'];
        }
        
        $data1 = array('[%bodycontentbacgroColor%]','[%bodycontenttxture%]','[%headerbacgroColor%]',
        '[%headertxture%]','[%bannerfreshimg%]','[%contentbodyfontColor%]','[%contentbodybacgroColor%]',
        '[%contentbodytxture%]','[%%body%%]','[%footerfontColor%]','[%footerbacgroColor%]',
        '[%footertxture%]','[%footerfontColor%]','[%%footertext%%]');
        $data2 = array($bodyContentjsonval['bodycontentbacgroColor'],$bodyContentjsonval['bodycontenttxture'],
        $bodyContentjsonval['headerbacgroColor'],$bodyContentjsonval['headertxture'],
        $bannerfreshimgdef,$bodyContentjsonval['contentbodyfontColor'],
        $bodyContentjsonval['contentbodybacgroColor'],$bodyContentjsonval['contentbodytxture'],
        $bodyContentmsg,$bodyContentjsonval['footerfontColor'],$bodyContentjsonval['footerbacgroColor'],
        $bodyContentjsonval['footertxture'],$bodyContentjsonval['footerfontColor'],$footerContentmsg);

        return $messagemail = str_replace($data1,$data2,$emailTemplatemain);
    } 

	public function getdeleteemailtempate($fname='',$loginid='',$from='')
    {
    	$dbeeEmailtemplate = new RawEmailtemplate();
    	$emailTemplatemain = $dbeeEmailtemplate->dbeeEmailtemplate();    	
    	$deshboard   = new Admin_Model_Deshboard();
    	$bodyContent = $deshboard->getGroupemailtemplate();
    	$fname=$this->myclientdetails->customDecoding($fname);
      $body = '';
    	if($from=='deleteuser'){
		 
			 $MailSubject = "Your account has been deleted ";
			 $activity = 'deleted';

         
       
        $bodyContentmsg = 'Dear '.$fname. '<br><br>This email has been sent to inform you that your account has been deleted. Please visit the platform again if you wish to create a new account.';

       
        $footerContentmsg = $bodyContent[0]['footertext'];
        /*********/
		} 
    else if($from='inviteuser')
		{
			 $body  = 'You have been invited to be a part of '.COMPANY_NAME.' social platform. Click on the link below to join in.<br><br>'. BASE_URL;
			 $MailSubject = "You have been invited";
			 $activity = 'invited';

        $databody1 = array('[%%fname%%]','[%%body_deleteactive%%]','[%%COMPANY_FOOTERTEXT%%]');
        $databody2 = array($fname,$body,COMPANY_FOOTERTEXT);       
       
        $bodyContentmsg = str_replace($databody1,$databody2,$bodyContent[1]['body']);

        /*********/
        $datasub1 = array('[%%activity%%]');
        $datasub2 = array($activity);
        $subjectMsg = str_replace($datasub1,$datasub2,$bodyContent[0]['subject']);
        $footerContentmsg = $bodyContent[0]['footertext'];
        /*********/
		}
      
        $emailTemplatejson = $this->myclientdetails->getfieldsfromtable(array('id','emailtemplatejson','htmllayout'),'adminemailtemplates');
        //echo'<pre>';print_r($emailTemplatejson);die;
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
        $MailTo 	=	$this->myclientdetails->customDecoding($loginid);      
        $MailBody = html_entity_decode(str_replace("'","\'",$messagemail));
        /*echo $MailBody;
        echo $MailSubject;
        echo $MailFrom; exit;*/
      
        $this->myclientdetails->sendWithoutSmtpMail($MailTo,$MailSubject,$MailFrom,$MailBody);
        return true;	      

    }  

	public function SearchDbees($query)  
	{
		$db = $this->getDbTable();
		return $this->getDefaultAdapter()->query($query)->fetchAll();
		exit;
	}
	/**
 * Convert number of seconds into hours, minutes and seconds
 * and return an array containing those values
 *
 * @param integer $seconds Number of seconds to parse
 * @return array
 */
	function secondsToTime($seconds)
	{
		// extract hours
		$hours = floor($seconds / (60 * 60));

		// extract minutes
		$divisor_for_minutes = $seconds % (60 * 60);
		$minutes = floor($divisor_for_minutes / 60);

		// extract the remaining seconds
		$divisor_for_seconds = $divisor_for_minutes % 60;
		$seconds = ceil($divisor_for_seconds);

		// return the final array
		$obj = array(
			"h" => (int) $hours,
			"m" => (int) $minutes,
			"s" => (int) $seconds,
		);
		return $obj;
	}
	public function Agohelper($date)
	{
		$currentdate = date('Y-m-d H:i:s', time()); 
        $start_date  = new DateTime($currentdate);
        
        $since_start = $start_date->diff(new DateTime($date));
        if($since_start->y >0)
        {
            $ago = ($since_start->y >1 ) ? $since_start->y.' years ago' : $since_start->y.' year ago';
        } 
        else if($since_start->m > 0)
        {
            $ago = ($since_start->m>1) ? $since_start->m.' months ago' : $since_start->m.' month ago';
        }
        else if($since_start->d>0)
        {
            $ago = ($since_start->d>1) ? $since_start->d.' days ago' :$since_start->d.' day ago';
        }
        else if($since_start->h>0)
        {
            $ago = ($since_start->h>1) ? $since_start->h.' hours ago' : $since_start->h.' hour ago';
        }
        else if($since_start->i>0)
        {
            $ago = ($since_start->i>0) ? $since_start->i.' mins ago' : $since_start->i.' min ago';
        }
        else if($since_start->s>0)
        {
            $ago = ($since_start->s>1) ? $since_start->s.' secs ago' : $since_start->s.' sec ago';
        }
        
        if(!empty($ago))
        {
            return $ago;
        }
        else
        {
        
            $diff=($this->_date_diff(time(), strtotime($date)));            
            if($diff[y]!=0) $ago=($diff[y]>1) ? $diff[y].' years ' : $diff[y].' year ';
            elseif($diff[m]!=0) $ago=($diff[m]>1) ? $diff[m].' months ' : $diff[m].' month ';
            elseif($diff[d]!=0) $ago=($diff[d]>1) ? $diff[d].' days ' : $diff[d].' day ';
            elseif($diff[h]!=0) $ago=($diff[h]>1) ? $diff[h].' hours ' : $diff[h].' hour ';
            elseif($diff[i]!=0) $ago=($diff[i]>1) ? $diff[i].' minutes ' : $diff[i].' minute ';
            elseif($diff[s]!=0) $ago=($diff[s]>1) ? $diff[s].' seconds ' : $diff[s].' second ';
            $ago.=' ago';
            return $ago;    
        }
	}
	
	public function send_email($from, $to, $subject, $message)
	{
		$headers = "From: ".$from."\r\n";
		$headers .= "Reply-To: ".$from."\r\n";
		$headers .= "Return-Path: ".$from."\r\n"; 
		//$headers .= "CC: sombodyelse@gmail.com\r\n";
		//$headers .= "BCC: hiddenemail@gmail.com\r\n";
		//set content type to HTML 
		$headers .= "Content-type: text/html\r\n"; 
		if ( mail($to,$subject,$message,$headers) ) 
			{return true;} 
		else 
			{return false;}
	}
	
	public function Groupdropdown(){
		
		$select = $this->_db->select();
		$select->from(array('g'=>'usersgroup'))->where('clientID = ?',clientID)->order('g.ugid Desc');
		$result = $this->_db->fetchAll($select);
		if(count($result)>0)
		{
			$box='<div class="clearfix"></div>
			<select id="grpselect">';
			$box.='<option value=""> - Select a set - </option>';
			
			foreach($result as $data):
			$box.='<option value="'.$data['ugid'].'">'.$data['ugname'].'</option>';
			endforeach;	
			$box.='</select><script>$(function(){$select("#grpselect");})</script> ';
		}
		return $box;
	
	
	}
	
	public function Groupdropdown2(){
	
		$select = $this->_db->select();
		$select->from(array('g'=>'usersgroup'))->where('clientID = ?',clientID)->order('g.ugid Desc');
		$result = $this->_db->fetchAll($select);
		if(count($result)>0)
		{
			$box='<div class="clearfix"></div>
			<select id="grpselect2">';
			$box.='<option value=""> - Select a set - </option>';
	
			foreach($result as $data):
			$box.='<option value="'.$data['ugid'].'">'.$data['ugname'].'</option>';
			endforeach;
			$box.='</select><script>$(function(){$select("#grpselect");})</script> ';
		}
		return $box;
	
	
	}
	function convert_time_zone($date_time, $from_tz, $to_tz)
	{
	
		$time_object = new DateTime($date_time, new DateTimeZone($from_tz));
		$time_object->setTimezone(new DateTimeZone($to_tz));
		return $time_object->format('Y-m-d H:i:s');
	
	}
   function timezoneevent($zone)
   {
    switch ($zone) 
    {
      case '-12.0':
        $zonetext = '(GMT -12:00) Eniwetok, Kwajalein';
        break;
      case '-11.0':
        $zonetext = '(GMT -11:00) Midway Island, Samoa';
      break;
      case '-10.0':
      $zonetext = '(GMT -10:00) Hawaii';
      break;
      case '-9.0':
      $zonetext = '(GMT -9:00) Alaska';
        break;
      case '-8.0':
      $zonetext = '(GMT -8:00) Pacific Time (US &amp; Canada)';
        break;
       case '-7.0':
      $zonetext = '(GMT -7:00) Mountain Time (US &amp; Canada)';
        break;
       case '-6.0':
      $zonetext = '(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima';
        break;
       case '-5.0':
      $zonetext = '(GMT -7:00) Mountain Time (US &amp; Canada)';
        break;
       case '-4.0':
      $zonetext = '(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz';
        break;
        case '-3.5':
      $zonetext = '(GMT -3:30) Newfoundland';
        break;
       case '-3.0':
      $zonetext = '(GMT -3:00) Brazil, Buenos Aires, Georgetown';
        break;
       case '-2.0':
      $zonetext = '(GMT -2:00) Mid-Atlantic';
        break;
      case '-1.0':
      $zonetext = '(GMT -1:00 hour) Azores, Cape Verde Islands';
        break;
     case '0.0':
      $zonetext = '(GMT) Western Europe Time, London, Lisbon, Casablanca';
        break;
     case '1.0':
        $zonetext = '(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris';
        break;
     case '2.0':
        $zonetext = '(GMT +2:00) Kaliningrad, South Africa';
      break;
      case '3.0':
      $zonetext = '(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg';
      break;
      case '3.5':
      $zonetext = '(GMT +3:30) Tehran';
        break;
      case '4.0':
      $zonetext = '(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi';
        break;
       
       case '5.0':
      $zonetext = '(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent';
        break;
       case '4.5':
      $zonetext = '(GMT +4:30) Bombay, Calcutta, Madras, New Delhi';
        break;
       case '5.75':
      $zonetext = '(GMT +5:45) Kathmandu';
        break;
        case '6.0':
      $zonetext = '(GMT +6:00) Almaty, Dhaka, Colombo';
        break;
       case '7.0':
      $zonetext = '(GMT +7:00) Bangkok, Hanoi, Jakarta';
        break;
       case '8.0':
      $zonetext = '(GMT +8:00) Beijing, Perth, Singapore, Hong Kong';
        break;
      case '9.0':
      $zonetext = '(GMT +9:00) Tokyo, Seoul, Osaka';
        break;
     case '9.5':
      $zonetext = '(GMT +9:30) Adelaide, Darwin';
        break;
      case '10.0':
      $zonetext = '(GMT +10:00) Eastern Australia, Guam, Vladivostok';
        break;
       case '11.0':
      $zonetext = 'GMT +11:00) Magadan, Solomon Islands, New Caledonia';
        break;
       case '12.0':
      $zonetext = '(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka';
        break;
      default:
         $zonetext = '';
        break;
    }
    return $zonetext;
  }
	public function getrestrictedurl($url)
    {
    	$select = $this->_db->select()
    	->from('tblRestrictLink',array('id','linkurl'))
    	->where('linkurl = ?', $url)->where('clientID = ?', clientID);
    	$data = $this->_db->fetchAll($select);
    	if(count($data)>0)
    		return true;
    	else
    		return false;
    }

	function checkImgExist($img, $folder, $default,$plateform='')
	{
		if($img=='') $ProfilePic = $default;
		else if($folder == 'userpics')
		{
			$ProfilePic = $img;
		}
		else 
		{
			if(empty($plateform)) $url = IMGPATH .'/'.$folder.'/'.$img; 
			else  $url = IMGPATH .'/'.$folder.'/'.$img; 

			$file = @getimagesize($url);         
			// $file = @fopen($url, "rt");

			if (empty($file)) {
			$ProfilePic = $default;
		} 
		else $ProfilePic = $img;

		}
		return $ProfilePic;
	}

	public function snapshot($url) 
	{
	        $name = time().".jpg";
	        // Command to execute
	        $command = 'xvfb-run --server-args="-screen 0, 1024x768x24"  /home/dbee/bin/CutyCapt';
	        // Directory for the image to be saved
	        $image_dir = "--out=".$_SERVER['DOCUMENT_ROOT']."/results/";
	        // Putting together the command for `shell_exec()`
	        $ex = "$command --url=$url " . $image_dir . $name;
	        shell_exec($ex);
	        return $name ;

	        
	}

	 public function insertiwitter($data)
    {   
         $myclientdetails = new Admin_Model_Clientdetails();
        if ($myclientdetails->insertdata_global('tblDbeeTweets', $data))
            return true;
        else
            return false;
    }

	public function deletetwt($id,$keyword)
    {
        $myclientdetails = new Admin_Model_Clientdetails();  
        $myclientdetails->deleteMaster('tblDbeeTweets', array('DbeeID= ?' => $id ,'Keyword= ?'=> $keyword));
        return true;
    }

     public function updatedbee($data, $id)
    {
         $myclientdetails = new Admin_Model_Clientdetails();       
         $myclientdetails->updateMaster('tblDbees',$data, array('DbeeID =' . $id));
        
    }



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

  public function resize($width, $height,$imgname){  
    $file = ABSIMGPATH."/users/".$imgname;
    list($w, $h, $type, $attr) = getimagesize($file);      
    $ratio = max($width/$w, $height/$h);
    $h = ceil($height / $ratio);
    $x = ($w - $width / $ratio) / 2;
    $w = ceil($width / $ratio);   
    if($width==50)
    {
     $path = ABSIMGPATH."/users/small/".$imgname;
    }
    else if($width==100)
    {
      $path = ABSIMGPATH."/users/medium/".$imgname;
    }
    $imgString = file_get_contents($file);
    $image = imagecreatefromstring($imgString);
    $tmp = imagecreatetruecolor($width, $height);
    imagecopyresampled($tmp, $image,
      0, 0,
      $x, 0,
      $width, $height,
      $w, $h);
    /* Save image */
    switch ($type) {
      case 'image/jpeg':
        imagejpeg($tmp, $path, 100);
        break;
      case '2':
        imagejpeg($tmp, $path, 100);
        break;
      case 'image/png':
        imagepng($tmp, $path, 0);
        break;
      case '3':
        imagepng($tmp, $path, 0);
        break;
      case 'image/gif':
        imagegif($tmp, $path);
        break;
      case '0':
        imagegif($tmp, $path);
        break;
      default:
        exit;
        break;
      }

       return $path;
      /* cleanup memory */
      imagedestroy($image);
      imagedestroy($tmp);
    }

	public function getExtension($str) {
         $i = strrpos($str,".");
         if (!$i) { return ""; }
         $l = strlen($str) - $i;
         $ext = substr($str,$i+1,$l);
         return $ext;
    }
	
    public function getdbtime($date, $uTZoffset, $format_str="Y.m.d H:i")
    {
    	$ts=strtotime($date);
    	$ts_offset=date('Z',$ts);
    	if ($uTZoffset) 
    		$add_offset=$ts_offset - date('Z'); //when not UTC
    	else $add_offset=0; //when UTC
    	return date($format_str,$ts-$uTZoffset*3600);
    }

    public function getdbtime2($date, $uTZoffset, $format_str="Y.m.d H:i")
    {
    	$ts=strtotime($date);
    	return date($format_str,$ts+$uTZoffset*3600);
    }
    public function gettimeinterval($livedate='', $icon='')
    {
    	$curDate	=	date('Y-m-d H:i:s');
    	$after 		= '';
    	//echo $livedate .' > '. $curDate.'<br>';
    	if($livedate > $curDate)
    	{
			$date1 		= new DateTime($livedate);
			$date2 		= new DateTime($curDate);
			$interval 	= $date1->diff($date2);

			$after .= '<span class="goinglive">'.$icon.' Post going live in ';

			if($interval->y !=0){ $year 	= ($interval->y ==1) ? ' year ':' years '; 		$after .= $interval->y.$year;		}
			if($interval->m !=0){ $months 	= ($interval->m ==1) ? ' month ':' months '; 	$after .= $interval->m.$months ; 	}
			if($interval->d !=0){ $days 	= ($interval->d ==1) ? ' day ':' days '; 		$after .= $interval->d.$days; 		}
			if($interval->h !=0){ $hours 	= ($interval->h ==1) ? ' hour ':' hours '; 		$after .= $interval->h.$hours ; 	}
			if($interval->i !=0){ $minutes  = ($interval->i ==1) ? ' minute ':' minutes '; 	$after .= $interval->i.$minutes ; 	}
			if($interval->s !=0){ $seconds  = ($interval->s ==1) ? ' second ':' seconds '; 	$after .= $interval->s.$seconds ; 	}
			$after .= '<span>';
		} 
		else
		{
			$after = date('d M Y',strtotime($livedate));
		}
		return $after;
		
    }
    
	
	function addtogroupbutton($enabled='0',$btnlabel='Add to user set',$btnexisting='Save to existing set',$btnaddnew='Add new set',$btnsavegrp='Save set', $disabled='disabled'){
		
		$chkusersgroup = $this->myclientdetails->getAllMasterfromtable('usersgroup',array('ugname'),array('ugstatus'=>1));

		$notfound='';
		if($this->Groupdropdown()>0){
			$notfound = '<div class="noFound">No user set found</div>';
			$groupFound='none';
		}
		$disabled='disabled';
		if($enabled=='1') $disabled='';
		$retResult .='
			<div class="dropDown pull-right addToGrpWrap">
			<a href="javascript:void(0)" class="btn dropDownTarget '.$disabled.'"><i class="fa fa-plus fa-lg"></i> &nbsp; '.$btnlabel.'</a>			
			<div class="dropDownList groupinsertWrapper right">
				<div id="groupuserinsert">
					<div class="grpWrapperBox">
						<div class="createdGrpDrp" style="display:'.$groupFound.';"> '.$this->Groupdropdown().'</div>
						<div class="creatGWrap"> 							
							<div class="saveFilterWrapper">
								<div class="subPopupContainer">
									<h2 class="titleCG">Create user set</h2>
									<div class="formRow">
										<input type="text" name="filterName" id="gname" placeholder="Name" value="">
									</div>
									<div class="formRow">
										<textarea id="grpDescription" placeholder="Description" value=""></textarea>
									</div>
								
								</div>
								<div class="popupFooterWrapper">
									<button id="saveGrpName" class="btn btn-green" type="button">'.$btnsavegrp.'</button>
									<button class="btn cancelSaveFilter" type="button"><i class="fa fa-times-sign"> </i>Cancel</button>
								</div>
							</div>
						</div>
						'.$notfound.'';	
						if(count($chkusersgroup) > 0)
						{					
						$retResult .='<button type="button" class="btn btn-green fluidBtn" id="addgroupBtn" dataAfter="or" style="display:'.$groupFound.';"> <i class="fa fa-plus fa-lg"></i>&nbsp; '.$btnexisting.' </button>';
					    }
						$retResult .='<a href="javascript:void(0)" class="btn fluidBtn" id="usergrouplinkforadd"><i class="fa fa-group fa-lg"></i> &nbsp; '.$btnaddnew.'</a>
					</div>	
				</div>
			</div>		
			</div> ';
			return  $retResult;
	}
	
	
	function addtogroupbutton2($enabled='0',$btnlabel='Add to user set',$btnexisting='Save to existing set',$btnaddnew='Add new set',$btnsavegrp='Save set', $disabled='disabled'){
	
		$notfound='';
		if($this->Groupdropdown2()>0){
			$notfound = '<div class="noFound">No user set found</div>';
			$groupFound='none';
		}
		$disabled='disabled';
		if($enabled=='1') $disabled='';
		$retResult .='
		<div class="dropDown pull-right addToGrpWrap">
		<a href="javascript:void(0)" class="btn dropDownTarget '.$disabled.'"><i class="fa fa-plus fa-lg"></i> &nbsp; '.$btnlabel.'</a>
		<div class="dropDownList groupinsertWrapper right">
		<div id="groupuserinsert">
		<div class="grpWrapperBox">
		<div class="createdGrpDrp" style="display:'.$groupFound.';"> '.$this->Groupdropdown2().'</div>
		<div class="creatGWrap">
		<div class="saveFilterWrapper">
		<div class="subPopupContainer">
		<h2 class="titleCG">Create user set</h2>
		<div class="formRow">
		<input type="text" name="filterName" id="gname" placeholder="Name" value="">
		</div>
		<div class="formRow">
		<textarea id="grpDescription" placeholder="Description" value=""></textarea>
		</div>
	
		</div>
		<div class="popupFooterWrapper">
		<button id="saveGrpName2" class="btn btn-green" type="button">'.$btnsavegrp.'</button>
		<button class="btn cancelSaveFilter" type="button"><i class="fa fa-times-sign"> </i>Cancel</button>
		</div>
		</div>
		</div>
		'.$notfound.'
		<button type="button" class="btn btn-green fluidBtn" id="addgroupBtn2" dataAfter="or" style="display:'.$groupFound.';"> <i class="fa fa-plus fa-lg"></i>&nbsp; '.$btnexisting.' </button>
		<a href="javascript:void(0)" class="btn fluidBtn" id="usergrouplinkforadd"><i class="fa fa-group fa-lg"></i> &nbsp; '.$btnaddnew.'</a>
		</div>
		</div>
		</div>
		</div> ';
		return  $retResult;
	}
	
	
	public function getvipuserdropdown($usertype,$callfrom){
		$opval = '';
		
		foreach($usertype as $data):
		$opval.='<option value="'.$data['TypeID'].'">'.$data['TypeName'].'</option>';
		$opval1.='<li rel="'.$data['TypeID'].'">'.$data['TypeName'].'</li>';
		endforeach;
		if($callfrom==2){
			$content = '<select name="usertype" id="usertype">
			'.$opval.'
			</select>';
		}
		if($callfrom==1){
		foreach($usertype as $data):
			$opval.='<option value="1">'.$data['TypeName'].'</option>';
			$opval1.='<li rel="1">'.$data['TypeName'].'</li>';
		endforeach;
		$content = '<div class="select">
		<select id="usertype" name="usertype" class="s-hidden">
		'.$opval.'
		</select>
		<div class="styledSelect">delegate</div>
		<ul class="options" style="display: none;">
		'.$opval1.'
		</ul>
		</div>';
		}
		return $content;
	}
    
	
}

class Admin_Model_Csvtodb extends Zend_Db_Table_Abstract
{
	public $log;
	public function __construct()
	{
		//$this->log = new Logging();
	}
	
	public function city_blocks_fr($file)
	{
		print_r($file);
		exit();
		$filename	=	"GeoLiteCity-Blocks.csv";
		$filehandle = 	fopen("./".$filename, "r");
		$datas 		=	array();
		$rec 		= 	array();
		$cnt		=	0;
		$errorchk	=	0;
		while (($data1 = fgetcsv($filehandle, 1000, ",")) !== FALSE) 
		{
			 $cnt++;	 // Counter for total records
			 if($cnt>2 )  // Condition to avoid first 2 lines of csv fine
			 {
					$ab	=	implode(',', $data1);
					$data	=	explode(',',$ab);
					
					$startIpNum 	= (trim($data[0])!=' ')? mysql_real_escape_string(trim($data[0],'"')):'';
					$endIpNum 		= (trim($data[1])!=' ')? mysql_real_escape_string(trim($data[1],'"')):'';
					$locId		 	= (trim($data[2])!=' ')? mysql_real_escape_string(trim($data[2],'"')):'';
									
					$sqlInsert		=	"INSERT INTO `city_blocks_test` ( `startIpNum`,`endIpNum`, `locId`) VALUES ('$startIpNum','$endIpNum','$locId')";
					try {  
						if (mysql_query($sqlInsert) == '') 
						{
							$errorchk	=	1;	
							throw new MyException(mysql_error());
						}
					 
					} catch (MyException $e) {
					 
						 $e->getErrorReport();
					 
					}
					
					
				}
		}
		if($errorchk==1)
		{
			$this->log->lfile('s_log.txt');
			$this->log->lwrite( "End of import ".$filename);
			$this->log->lclose();
		}
	}

}