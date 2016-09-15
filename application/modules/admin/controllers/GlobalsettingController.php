<?php
    /**
	 * 
	 * @ admin global setting
	 */
class Admin_GlobalsettingController extends IsadminController
{
	/**
	 * Init
	 * 
	 * @see Zend_Controller_Action::init()
	 */
    public function init()
    {
      parent::init();
    }

    public function indexAction()
    {
       $selctGlobalSetting = $this->myclientdetails->getRowMasterfromtable('tblAdminSettings',array('*'));
       $this->view->globalSettingVal = $selctGlobalSetting['social_account'];
    }

    public function insertgsettingAction()
    {
       $request = $this->getRequest()->getParams();
       $globe_val = $this->_request->getPost('globelSettingVal');
      
       $calling   = $this->_request->getPost('calling');
       if($calling=="social") $data = array('social_account'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID); 
       else if($calling=="event") $data = array('event'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
       else if($calling=="groupbg") $data = array('groupbg'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
       else if($calling=="creategrp") $data = array('creategrp'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
       else if($calling=="scoringoff") $data = array('plateform_scoring'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
       else if($calling=="gspider") $data = array('google_spider'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
       else if($calling=="semantria_seen") $data = array('semantria_seen'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
       else if($calling=="IsLeagueOn") $data = array('IsLeagueOn'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
       else if($calling=="userconfirmed") $data = array('userconfirmed'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
       else if($calling=="allow_user_create_polls") $data = array('allow_user_create_polls'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
       else if($calling=="addtocontact") $data = array('addtocontact'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
       else if($calling=="adminpostscore") $data = array('adminpostscore'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
       else if($calling=="allowexperts") $data = array('allowexperts'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
       else if($calling=="IsSurveysOn") $data = array('IsSurveysOn'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
       else if($calling=="Profanityfilter") $data = array('Profanityfilter'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
       else if($calling=="admin_searchable_frontend") $data = array('admin_searchable_frontend'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
       else if($calling=="allowedPostWithTwitter") $data = array('allowedPostWithTwitter'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
       else if($calling=="AllowLoginTerms") $data = array('AllowLoginTerms'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
       else if($calling=="ShowVideoEvent") $data = array('ShowVideoEvent'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
       else if($calling=="ShowLiveVideoEvent") $data = array('ShowLiveVideoEvent'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
       else if($calling=="ShowRSS") $data = array('ShowRSS'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
       else if($calling=="showAllUsers") $data = array('showAllUsers'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
       else if($calling=="allowmultipleexperts") $data = array('allowmultipleexperts'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
       else if($calling=="groupemail") $data = array('GroupEmail'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
       
       else  { echo 'false'; exit; }

       $insertglobal_setingval = $this->deshboard->updateadminglobalsetting($data);

       $fetchdata = $this->myclientdetails->getRowMasterfromtable('tblAdminSettings',array('plateform_scoring','adminpostscore','showAllUsers'));
      
       if($fetchdata['plateform_scoring']==1 && $fetchdata['adminpostscore']==1)
       {
            $data2 = array('adminpostscore'=>0,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
           $insertglobal_setingval2 = $this->deshboard->updateadminglobalsetting($data2);
       }
       if($fetchdata['showAllUsers']==0)
       {
            $data3 = array('userconfirmed'=>0,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
            $insertglobal_setingval3 = $this->deshboard->updateadminglobalsetting($data3);
       }

       

       echo $insertglobal_setingval;exit; 
    }

    public function managersocialloginAction()
    {
        $request = $this->getRequest()->getParams();
        $globe_val = $this->_request->getPost('globelSettingVal');
        $calling   = $this->_request->getPost('calling');
       
        if($calling=="fbsociallogin") $data = array('Facebooklogin'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
        if($calling=="twsociallogin") $data = array('Twitterlogin'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
        if($calling=="gpsociallogin") $data = array('Gpluslogin'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
        if($calling=="ldsociallogin") $data = array('Linkedinlogin'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);

        if($calling=="allsocialsignin") $data = array('allSocialstatus'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
        if($calling=="fbsocialsignin") $data = array('Facebookstatus'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
        if($calling=="twsocialsignin") $data = array('Twitterstatus'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
        if($calling=="gpsocialsignin") $data = array('Gplusstatus'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
        if($calling=="ldsocialsignin") $data = array('Linkedinstatus'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);

        if($calling=="allsocialsignininvite") $data = array('allSocialinvite'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
        if($calling=="fbsocialsignininvite") $data = array('Facebookinvite'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
        if($calling=="twsocialsignininvite") $data = array('Twitterinvite'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
        if($calling=="gpsocialsignininvite") $data = array('Gplusinvite'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
        if($calling=="ldsocialsignininvite") $data = array('Linkedininvite'=>$globe_val,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
        $insertsociallogin_setingval = $this->deshboard->updatesocialloginsetting($data);
         
         if($calling=="fbsociallogin" || $calling=="twsociallogin" || $calling=="gpsociallogin" || $calling=="ldsociallogin")
         {
            $insertsociallogin_setingval=1;
         }
       

        $result = $this->myclientdetails->getRowMasterfromtable('tblloginsocialresource',array('*'));

        if($result['allsocialsignin']==1)
        {
          $rajxx=1;
          $data = array('Facebookstatus'=>$rajxx,'Twitterstatus'=>$rajxx,'Gplusstatus'=>$rajxx,'Linkedinstatus'=>$rajxx,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
          $insertsociallogin_setingval = $this->deshboard->updatesocialloginsetting($data);
          $insertsociallogin_setingval=2;
        }

        if($result['allSocialinvite']==1)
        {
          $rajxxx=1;
          $data3 = array('Facebookinvite'=>$rajxxx,'Twitterinvite'=>$rajxxx,'Gplusinvite'=>$rajxxx,'Linkedininvite'=>$rajxxx,'timestamp'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
          $insertsociallogin_setingval = $this->deshboard->updatesocialloginsetting($data3);
          $insertsociallogin_setingval=3;
        }


        echo $insertsociallogin_setingval;exit;
    }
	
   
}
