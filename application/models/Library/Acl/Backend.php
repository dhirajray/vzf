<?php

class Application_Model_Library_Acl_Backend extends Zend_View_Helper_Abstract
{
	public $acl;
	
	public function __construct()
	{
		$this->acl = new Zend_Acl();
	}
	
	public function setRoles()
	{
		$Roledetails = new Admin_Model_User();
		$AllRoleslist = $Roledetails->getRols();
		foreach($AllRoleslist as $Rolevalue){
           $this->acl->addRole(new Zend_Acl_Role($Rolevalue['role']));          
		}
	}
	
	/* new Zend_Acl_Resource(module or controller) */
	public function setResources()
	{ 
		// module resource
		$this->acl->add(new Zend_Acl_Resource('admin'));
		$this->acl->add(new Zend_Acl_Resource('visitor'));
        $this->acl->add(new Zend_Acl_Resource('adminlogin'));
        $this->acl->add(new Zend_Acl_Resource('frontend'));
        $this->acl->add(new Zend_Acl_Resource('aglogin'));
        //$this->acl->add(new Zend_Acl_Resource('api'));
   
		// controller resource
		$this->acl->add(new Zend_Acl_Resource('index'),'admin');
		$this->acl->add(new Zend_Acl_Resource('dashboard'),'admin');
		$this->acl->add(new Zend_Acl_Resource('survey'),'admin');
		$this->acl->add(new Zend_Acl_Resource('user'),'admin');
		$this->acl->add(new Zend_Acl_Resource('myaccount'),'admin');
		$this->acl->add(new Zend_Acl_Resource('Restrictedurl'),'admin');
		$this->acl->add(new Zend_Acl_Resource('advertisement'),'admin');
		$this->acl->add(new Zend_Acl_Resource('message'),'admin');
		$this->acl->add(new Zend_Acl_Resource('reporting'),'admin');
		$this->acl->add(new Zend_Acl_Resource('vipuser'),'admin');
		$this->acl->add(new Zend_Acl_Resource('event'),'admin');
		$this->acl->add(new Zend_Acl_Resource('usergroup'),'admin');
		$this->acl->add(new Zend_Acl_Resource('savedcharts'),'admin');
		$this->acl->add(new Zend_Acl_Resource('usertype'),'admin');
		$this->acl->add(new Zend_Acl_Resource('social'),'admin');/******/
		$this->acl->add(new Zend_Acl_Resource('knowledgecenter'),'admin');
		$this->acl->add(new Zend_Acl_Resource('leaguescore'),'admin');
		$this->acl->add(new Zend_Acl_Resource('influence'),'admin');
		$this->acl->add(new Zend_Acl_Resource('widgets'),'admin');
		$this->acl->add(new Zend_Acl_Resource('import'),'admin');/*********/
		$this->acl->add(new Zend_Acl_Resource('globalsetting'),'admin');/*******/
		$this->acl->add(new Zend_Acl_Resource('global'),'admin');/*****/
		$this->acl->add(new Zend_Acl_Resource('getjsonresult'),'admin');/******/
		$this->acl->add(new Zend_Acl_Resource('department'),'admin');/*******/
		$this->acl->add(new Zend_Acl_Resource('emailtempsetting'),'admin');
		$this->acl->add(new Zend_Acl_Resource('manageroles'),'admin');/**********/
		$this->acl->add(new Zend_Acl_Resource('matching'),'admin');/**********/
		$this->acl->add(new Zend_Acl_Resource('platformsettings'),'admin');
		$this->acl->add(new Zend_Acl_Resource('livebroadcasts'),'admin');
		$this->acl->add(new Zend_Acl_Resource('test'),'admin');/***********/
		$this->acl->add(new Zend_Acl_Resource('dbeeauth'),'admin');
		$this->acl->add(new Zend_Acl_Resource('dbeedetail'),'frontend');
	}
	
	public function setPrivilages()
	{
		  //echo clientID;die;
		  //require_once('Zend/Session/Namespace.php');
	      $authNamespace = new Zend_Session_NameSpace('identify');
	      $authNamespace->setExpirationSeconds((1209600));
	      //echo'<pre>';print_r($authNamespace->role);die;
	      if($authNamespace->role==''){
               $role_id = 1;
	      }else{$role_id = $authNamespace->role;}
		  
		  $moduleName = $authNamespace->module;
		  $Roledetails = new Admin_Model_User();
		  $Roledetailsval = new Admin_Model_Reporting();
		  $AllRolesdetailslist = $Roledetailsval->getRolesDetails($role_id);
		  $Roledetails = new Admin_Model_User();
		  $AllRoledetails = $Roledetails->Rolesforacl($role_id);
          
		    foreach($AllRolesdetailslist as $AllRolevalue)
		    {     	
		      $this->acl->allow($AllRoledetails[0]['role'],$AllRolevalue['controller'],array($AllRolevalue['action']));
		      $myAsrval = ($AllRolevalue['controller']=='dashboard') ? "dashboard" : $AllRolevalue['controller'];

		      
		          //if($role_id==1002){echo $AllRolevalue['action'];}           
	              if($myAsrval=="dashboard"){
	                $this->acl->allow($AllRoledetails[0]['role'],'index',array('index','logout','callingajaxcontainers','notification'));
	                $this->acl->allow($AllRoledetails[0]['role'],'dashboard',array('postreport','linkdetail','postsubmit','updatedbeestatus'));
	              }else if($myAsrval=="message"){
	              	$this->acl->allow($AllRoledetails[0]['role'],'message',array('messagelist','report','ajaxreport','templatemail','messageemail','deletemessageemail','searchusers'));
	              }else if($myAsrval=="event"){
	              	$this->acl->allow($AllRoledetails[0]['role'],'event');
	              }else if($myAsrval=="survey"){
	              	$this->acl->allow($AllRoledetails[0]['role'],'survey');
	              }
	              else if($myAsrval=="user"){	              	
	              	$this->acl->allow($AllRoledetails[0]['role'],'social');
	              	$this->acl->allow($AllRoledetails[0]['role'],'import',array('invitationopration'));
	              	$this->acl->allow($AllRoledetails[0]['role'],'user',array('userdetails','updateuserstatus','getuserdetails','reloadactivity','livedbee','livecomment','livegroupusr','livescoreusr','livescoremeusr','usermessages'));
	              	//social/socialsearch//user/userdetails//usergroup/usersgroupstore
	                $this->acl->allow($AllRoledetails[0]['role'],'usergroup',array('usersgroupstore'));
	                $this->acl->allow($AllRoledetails[0]['role'],'index',array('callingajaxcontainers'));
	              }
	              else if($myAsrval=="usergroup"){
	                $this->acl->allow($AllRoledetails[0]['role'],'usergroup');	
	                $this->acl->allow($AllRoledetails[0]['role'],'user',array('getuserdetails','userdetails','updateuserstatus','reloadactivity','livedbee','livecomment','livegroupusr','livescoreusr','livescoremeusr','usermessages'));
	                $this->acl->allow($AllRoledetails[0]['role'],'index',array('callingajaxcontainers'));
	              }else if($myAsrval=="usertype"){
	              	   
	              	   $this->acl->allow($AllRoledetails[0]['role'],'usertype');
	              	   $this->acl->allow($AllRoledetails[0]['role'],'import',array('invitationopration'));
	              	   //$this->acl->allow($AllRoledetails[0]['role'],'vipuser');
	              	   $this->acl->allow($AllRoledetails[0]['role'],'user',array('getuserdetails','userdetails','updateuserstatus','reloadactivity','livedbee','livecomment','livegroupusr','livescoreusr','livescoremeusr','usermessages'));
	              }else if($myAsrval=="vipuser"){
	              	
	              	//echo $AllRolevalue['action'];echo'+++';usertype
	              	if($AllRolevalue['action']=='vipgroups'){
	                     $this->acl->allow($AllRoledetails[0]['role'],'vipuser',array('createvipgroup','vipgroupmembers'));
	              	}
	              	if($AllRolevalue['action']=='index'){
	                     $this->acl->allow($AllRoledetails[0]['role'],'vipuser');
	                     $this->acl->allow($AllRoledetails[0]['role'],'import');
	              	}	                
	                $this->acl->allow($AllRoledetails[0]['role'],'usergroup',array('dbeeuser','addgroup','usersgroupstore'));
	                //$this->acl->allow($AllRoledetails[0]['role'],'user',array('getuserdetails','userdetails','updateuserstatus'));
	                $this->acl->allow($AllRoledetails[0]['role'],'user',array('getuserdetails','userdetails','updateuserstatus','reloadactivity','livedbee','livecomment','livegroupusr','livescoreusr','livescoremeusr','usermessages'));
	                $this->acl->allow($AllRoledetails[0]['role'],'usertype',array('searchurl','addusertype','updateusertype'));

	              }else if($myAsrval=="leaguescore"){
	              	$this->acl->allow($AllRoledetails[0]['role'],'leaguescore');
	              }else if($myAsrval=="influence"){
	              	$this->acl->allow($AllRoledetails[0]['role'],'influence');
	              }else if($myAsrval=="widgets"){
	              	$this->acl->allow($AllRoledetails[0]['role'],'widgets');
	              }else if($myAsrval=="savedcharts"){
	              	$this->acl->allow($AllRoledetails[0]['role'],'savedcharts');
	              }else if($myAsrval=="dbeeauth"){
	              	$this->acl->allow($AllRoledetails[0]['role'],'dbeeauth',array('index'));
	              }else if($myAsrval=="reporting"){
	              	//$this->acl->allow($AllRoledetails[0]['role'],'reporting',array('hashtag','twittercomments','topusers','locations','postvisiters','ajaxposthashtag'));
	              	$this->acl->allow($AllRoledetails[0]['role'],'reporting');
	                $this->acl->allow($AllRoledetails[0]['role'],'leaguescore',array('index'));
	                $this->acl->allow($AllRoledetails[0]['role'],'influence');
	                $this->acl->allow($AllRoledetails[0]['role'],'usergroup',array('addgroup','usersgroupstore'));
	                $this->acl->allow($AllRoledetails[0]['role'],'user');
	                $this->acl->allow($AllRoledetails[0]['role'],'savedcharts');
	                //x/callingajaxcontainers
	                $this->acl->allow($AllRoledetails[0]['role'],'index',array('callingajaxcontainers'));
	              }else if($myAsrval=="platformsettings"){
	              	$this->acl->allow($AllRoledetails[0]['role'],'platformsettings',array('ajaxplateforminterface','platforminterface'));
	              	//$this->acl->allow($AllRoledetails[0]['role'],'myaccount',array('configuration'));
	              }else if($myAsrval=="emailtempsetting"){
	              	$this->acl->allow($AllRoledetails[0]['role'],'emailtempsetting');
	              }else if($myAsrval=="Restrictedurl"){
	              	$this->acl->allow($AllRoledetails[0]['role'],'Restrictedurl',array('addurl','searchurl','deleteurl'));
	              }
	              else if($myAsrval=="myaccount"){
	              	$this->acl->allow($AllRoledetails[0]['role'],'myaccount',array('themesetting','ajaxplateforminterface','platforminterface','disconnectsocial','updatefacebookpageinfo','ajaxseo','ajaxscore','ajaxpostscore','ajaxpostscoreset','ajaxexpert','ajaxpollcompliance','ajaxpostliveconf','ajaxstaticpages','ajaxafterlogintc','ajaxsocialshare','ajaxconfiguration','ajaxsociallogo','ajaxbackground','ajaxbg','ajaxsaveconfiguration','ajaxremoveimage','updateaccount','updatesecqueans','updateaccountpass','setglobaltime','ajaxcategorylist','ajaxbiofieldlist','groupcategorylist','groupcat','delgroupcate','addcat','biofield','delcat','delbiofield','connectplateformuser','deleteplateformuser','helpvideo','addfeedlogo','addfeed','updatefeed','rollist','usersrollist','deletefeed','deleteall','activefeed'));
	              	$this->acl->allow($AllRoledetails[0]['role'],'globalsetting',array('insertgsetting'));
	              	//$this->acl->allow($AllRoledetails[0]['role'],'index',array('notification'));
	              }
	              else if($myAsrval=="knowledgecenter"){
	              	$this->acl->allow($AllRoledetails[0]['role'],'knowledgecenter');
	              	$this->acl->allow($AllRoledetails[0]['role'],'message',array('searchusers'));
	              }	
	              else if($myAsrval=="usergroup"){
	              	$this->acl->allow($AllRoledetails[0]['role'],'usergroup');
	              } 
	              else if($myAsrval=="advertisement"){
	              	//dbeedetail/socialblock/advertisement///index/notification
	              	$this->acl->allow($AllRoledetails[0]['role'],'dbeedetail');
	              	$this->acl->allow($AllRoledetails[0]['role'],'getjsonresult');
	              	$this->acl->allow($AllRoledetails[0]['role'],'advertisement',array('edit','advertpush','advertedit','rightbanner','headerbanner','publish','delete','showuserlist'));
	              	$this->acl->allow($AllRoledetails[0]['role'],'index',array('notification'));
	              	$this->acl->allow($AllRoledetails[0]['role'],'index',array('notification'));
	              	//socialblock/
	              }    
			}
			//$this->acl->allow($AllRoledetails[0]['role'],'myaccount');
			$this->acl->allow($AllRoledetails[0]['role'],'index',array('logout'));

         
		$this->acl->deny('visitor','admin');
		
		if((clientID==1) || (clientID==7)){
		  $this->acl->deny('super admin','admin');
	    }
		// admin
		$this->acl->allow('admin','dashboard');
		//if(clientID!=19){
		$this->acl->allow('admin','platformsettings');
	    //}
		$this->acl->allow('admin','manageroles');
		$this->acl->allow('admin','matching');
		$this->acl->allow('admin','index');
		if(clientID!=19){
		$this->acl->allow('admin','survey');
	    }
		$this->acl->allow('admin','myaccount');
		$this->acl->allow('admin','Restrictedurl');
		if(clientID!=19){
		$this->acl->allow('admin','advertisement');
	    }
		$this->acl->allow('admin','message');
		if(clientID!=19){
		$this->acl->allow('admin','reporting');
	    }
		$this->acl->allow('admin','vipuser');
		if(clientID!=19){
		$this->acl->allow('admin','event');
	    }
		$this->acl->allow('admin','usergroup');
		$this->acl->allow('admin','user');
		$this->acl->allow('admin','savedcharts');
		$this->acl->allow('admin','usertype');
		$this->acl->allow('admin','social');
		if(clientID!=19){
		$this->acl->allow('admin','knowledgecenter');
	    }
		$this->acl->allow('admin','leaguescore');
		$this->acl->allow('admin','influence');
		if((clientID==19) || (clientID==55)){
		$this->acl->allow('admin','widgets');
	    }
		$this->acl->allow('admin','import');
		$this->acl->allow('admin','globalsetting');
		$this->acl->allow('admin','global');
		$this->acl->allow('admin','getjsonresult');
		$this->acl->allow('admin','department');
		$this->acl->allow('admin','emailtempsetting');
		if(clientID==100){
		$this->acl->allow('admin','livebroadcasts');
	    }
		$this->acl->allow('admin','test');
		if((clientID==1) || (clientID==7)){
           $this->acl->allow('super admin','dbeeauth');
		}	
	}
	
	public function setAcl()
	{
		Zend_Registry::set('aclTawarlina',$this->acl);
	}
	
}