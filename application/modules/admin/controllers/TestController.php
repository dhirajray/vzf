<?php

class Admin_TestController extends IsadminController
{
    public function init()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        parent::init();
        //error_reporting(1);
    }

    public function indexAction()
    {
        require_once 'bulkmail.php';
    }
    
    public function autoAction()
    {
          echo'plz comment it';die;
          $clientID =21;
          $data1 = array(
                   array('clientID' => $clientID,'resource'=>'Dashboard','status'=>1,'parent_id'=>0),
          ); 
          $returnid1 = $this->userRec->insertresource('tblResourcesManage',$data1);
      /***************%*******************/
          $dataval1 = array(    
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'dashboard','action'=>'post','res_id'=>$returnid1+1),
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'dashboard','action'=>'comments','res_id'=>$returnid1+2),
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'dashboard','action'=>'groups','res_id'=>$returnid1+3),
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'dashboard','action'=>'notification','res_id'=>$returnid1+4),
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'dashboard','action'=>'scores','res_id'=>$returnid1+5),
            );
          $returnidval1 = $this->userRec->insertresource('resources',$dataval1);
      /***************%%******************/
          $data2 = array(    
                   array('clientID' =>$clientID,'resource'=>'New Posts','status'=>1,'parent_id'=>$returnid1),
                   array('clientID' =>$clientID,'resource'=>'New Comments','status'=>1,'parent_id'=>$returnid1),
                   array('clientID' =>$clientID,'resource'=>'User Created Groups','status'=>1,'parent_id'=>$returnid1),
                   array('clientID' =>$clientID,'resource'=>'Notifications','status'=>1,'parent_id'=>$returnid1),
                   array('clientID' =>$clientID,'resource'=>'Live Scoring','status'=>1,'parent_id'=>$returnid1),
            );
          $returnid2 = $this->userRec->insertresource('tblResourcesManage',$data2)+1;

      /*****************%*****************/
          $dataval2 = array(    
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'dashboard','action'=>'post','res_id'=>$returnid2+1),
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'dashboard','action'=>'specialdbs','res_id'=>$returnid2+2),
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'livebroadcasts','action'=>'index','res_id'=>$returnid2+3),
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'survey','action'=>'index','res_id'=>$returnid2+4),
            );
          $returnidval2 = $this->userRec->insertresource('resources',$dataval2);
      /*****************%%****************/


          $data3 =  array(
                    array('clientID' =>$clientID,'resource'=>'Posts','status'=>1,'parent_id'=>0),
                    array('clientID' =>$clientID,'resource'=>'Create Post','status'=>1,'parent_id'=>$returnid2),
                    array('clientID' =>$clientID,'resource'=>'Video Broadcasts','status'=>1,'parent_id'=>$returnid2),
                    array('clientID' =>$clientID,'resource'=>'Live Youtube Video broadcasts','status'=>1,'parent_id'=>$returnid2),
                    array('clientID' =>$clientID,'resource'=>'Surveys','status'=>1,'parent_id'=>$returnid2),
          );
          $returnid3 = $this->userRec->insertresource('tblResourcesManage',$data3)+1;

      /***************%*******************/
          $dataval3 = array(    
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'event','action'=>'index','res_id'=>$returnid3+1),
            );
          $returnidval3 = $this->userRec->insertresource('resources',$dataval3);
      /*****************%%****************/

          $data4 = array(
                   array('clientID' =>$clientID,'resource'=>'Events','status'=>1,'parent_id'=>0),
                   array('clientID' =>$clientID,'resource'=>'Events','status'=>1,'parent_id'=>$returnid3),
          );
          $returnid4 = $this->userRec->insertresource('tblResourcesManage',$data4)+1;
      /***************%*******************/
          $dataval4 = array(    
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'user','action'=>'index','res_id'=>$returnid4+1),
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'user','action'=>'invitesocial','res_id'=>$returnid4+2),
            );
          $returnidval4 = $this->userRec->insertresource('resources',$dataval4);
      /*****************%%****************/
          $data5 = array(
                    array('clientID' =>$clientID,'resource'=>'Users','status'=>1,'parent_id'=>0),
                    array('clientID' =>$clientID,'resource'=>'User Accounts','status'=>1,'parent_id'=>$returnid4),
                    array('clientID' =>$clientID,'resource'=>'Invite Social Connections','status'=>1,'parent_id'=>$returnid4),
          );
          $returnid5 = $this->userRec->insertresource('tblResourcesManage',$data5)+1;
      /***************%*******************/
          $dataval5 = array(    
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'vipuser','action'=>'index','res_id'=>$returnid5+1),
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'usertype','action'=>'index','res_id'=>$returnid5+2),
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'vipuser','action'=>'vipgroups','res_id'=>$returnid5+3),
            );
          $returnidval5 = $this->userRec->insertresource('resources',$dataval5);
      /*****************%%****************/
          $data6 = array(
                    array('clientID' =>$clientID,'resource'=>'VIP Users','status'=>1,'parent_id'=>0),
                    array('clientID' =>$clientID,'resource'=>'VIP User Accounts','status'=>1,'parent_id'=>$returnid5),
                    array('clientID' =>$clientID,'resource'=>'VIP User Types','status'=>1,'parent_id'=>$returnid5),
                    array('clientID' =>$clientID,'resource'=>'VIP User Groups','status'=>1,'parent_id'=>$returnid5),
          );
          $returnid6 = $this->userRec->insertresource('tblResourcesManage',$data6)+1;
      /***************%*******************/
          $dataval6 = array(    
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'usergroup','action'=>'listing','res_id'=>$returnid6+1),
            );
          $returnidval6 = $this->userRec->insertresource('resources',$dataval6);
      /*****************%%****************/
          $data7 = array(
                    array('clientID' =>$clientID,'resource'=>'Admin User Sets','status'=>1,'parent_id'=>0),
                    array('clientID' =>$clientID,'resource'=>'Admin User Sets','status'=>1,'parent_id'=>$returnid6),
          );         
          $returnid7 = $this->userRec->insertresource('tblResourcesManage',$data7)+1;
      /***************%*******************/
          $dataval7 = array(    
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'reporting','action'=>'index','res_id'=>$returnid7+1),
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'leaguescore','action'=>'index','res_id'=>$returnid7+2),
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'influence','action'=>'index','res_id'=>$returnid7+3),
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'reporting','action'=>'index','res_id'=>$returnid7+4),
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'reporting','action'=>'index','res_id'=>$returnid7+5),
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'reporting','action'=>'hashtag','res_id'=>$returnid7+6),
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'reporting','action'=>'index','res_id'=>$returnid7+7),
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'reporting','action'=>'twittercomments','res_id'=>$returnid7+8),
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'reporting','action'=>'topusers','res_id'=>$returnid7+9),
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'reporting','action'=>'locations','res_id'=>$returnid7+10),
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'reporting','action'=>'index','res_id'=>$returnid7+11),
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'reporting','action'=>'index','res_id'=>$returnid7+12),
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'reporting','action'=>'index','res_id'=>$returnid7+13),
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'savedcharts','action'=>'listing','res_id'=>$returnid7+14),
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'message','action'=>'report','res_id'=>$returnid7+15),

            );
          $returnidval7 = $this->userRec->insertresource('resources',$dataval7);
      /*****************%%****************/
          $data8 = array(
                    array('clientID' =>$clientID,'resource'=>'Reports','status'=>1,'parent_id'=>0),
                    array('clientID' =>$clientID,'resource'=>'Reports','status'=>0,'parent_id'=>$returnid7),
                    array('clientID' =>$clientID,'resource'=>'Scoring & Leagues','status'=>0,'parent_id'=>$returnid7),
                    array('clientID' =>$clientID,'resource'=>'Influencers','status'=>0,'parent_id'=>$returnid7),
                    array('clientID' =>$clientID,'resource'=>'Similar Interest Based','status'=>0,'parent_id'=>$returnid7),
                    array('clientID' =>$clientID,'resource'=>'Posts Visited','status'=>0,'parent_id'=>$returnid7),
                    array('clientID' =>$clientID,'resource'=>'Matching #Tags','status'=>0,'parent_id'=>$returnid7),
                    array('clientID' =>$clientID,'resource'=>'Social Sharing','status'=>0,'parent_id'=>$returnid7),
                    array('clientID' =>$clientID,'resource'=>'Twitter Usage','status'=>0,'parent_id'=>$returnid7),
                    array('clientID' =>$clientID,'resource'=>'User Cross Referencing','status'=>0,'parent_id'=>$returnid7),
                    array('clientID' =>$clientID,'resource'=>'User Location','status'=>0,'parent_id'=>$returnid7),
                    array('clientID' =>$clientID,'resource'=>'Email Providers','status'=>0,'parent_id'=>$returnid7),
                    array('clientID' =>$clientID,'resource'=>'Browser Sources','status'=>0,'parent_id'=>$returnid7),
                    array('clientID' =>$clientID,'resource'=>'Users OS','status'=>0,'parent_id'=>$returnid7),
                    array('clientID' =>$clientID,'resource'=>'Saved Dashboard Charts','status'=>0,'parent_id'=>$returnid7),
                    array('clientID' =>$clientID,'resource'=>'Email Stats','status'=>0,'parent_id'=>$returnid7),
          );
          $returnid8 = $this->userRec->insertresource('tblResourcesManage',$data8)+1;
      /***************%*******************/
          $dataval8 = array(    
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'message','action'=>'index','res_id'=>$returnid8+1),
            );
          $returnidval8 = $this->userRec->insertresource('resources',$dataval8);
      /*****************%%****************/
          $data9 = array(
                    array('clientID' =>$clientID,'resource'=>'Messaging','status'=>1,'parent_id'=>0),
                    array('clientID' =>$clientID,'resource'=>'Create & Send','status'=>1,'parent_id'=>$returnid8),
          );
          $returnid9 = $this->userRec->insertresource('tblResourcesManage',$data9)+1;
      /***************%*******************/
          $dataval9 = array(    
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'knowledgecenter','action'=>'index','res_id'=>$returnid9+1),
            );
          $returnidval9 = $this->userRec->insertresource('resources',$dataval9);
      /*****************%%****************/
          $data10 = array(
                    array('clientID' =>$clientID,'resource'=>'Document Library','status'=>1,'parent_id'=>0),
                    array('clientID' =>$clientID,'resource'=>'Document Library','status'=>1,'parent_id'=>$returnid9),
          );
          $returnid10 = $this->userRec->insertresource('tblResourcesManage',$data10)+1;
      /***************%*******************/
          $dataval10 = array(    
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'advertisement','action'=>'index','res_id'=>$returnid10+1),
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'advertisement','action'=>'trackreport','res_id'=>$returnid10+2),
            );
          $returnidval10 = $this->userRec->insertresource('resources',$dataval10);
      /*****************%%****************/
          $data11 = array(
                    array('clientID' =>$clientID,'resource'=>'Advertising','status'=>1,'parent_id'=>0),
                    array('clientID' =>$clientID,'resource'=>'Manage Ads','status'=>1,'parent_id'=>$returnid10),
                    array('clientID' =>$clientID,'resource'=>'Ad Tracking','status'=>1,'parent_id'=>$returnid10),
          );
          $returnid11 = $this->userRec->insertresource('tblResourcesManage',$data11)+1;
      /***************%*******************/
          $dataval11 = array(    
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'platformsettings','action'=>'themesetting','res_id'=>$returnid11+1),
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'emailtempsetting','action'=>'index','res_id'=>$returnid11+2),
            );
          $returnidval11 = $this->userRec->insertresource('resources',$dataval11);
      /*****************%%****************/
          $data12 = array(
                    array('clientID' =>$clientID,'resource'=>'Platform Settings','status'=>1,'parent_id'=>0),
                    array('clientID' =>$clientID,'resource'=>'Theme Configuration','status'=>1,'parent_id'=>$returnid11),
                    array('clientID' =>$clientID,'resource'=>'Email Templates','status'=>1,'parent_id'=>$returnid11),
          );
          $returnid12 = $this->userRec->insertresource('tblResourcesManage',$data12)+1;
      /***************%*******************/
          $dataval12 = array(    
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'Restrictedurl','action'=>'profanityfilter','res_id'=>$returnid12+1),
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'Restrictedurl','action'=>'index','res_id'=>$returnid12+2),
            );
          $returnidval12 = $this->userRec->insertresource('resources',$dataval12);
      /*****************%%****************/
          $data13 = array(
                    array('clientID' =>$clientID,'resource'=>'Content Restrictions','status'=>1,'parent_id'=>0),
                    array('clientID' =>$clientID,'resource'=>'Profanity / keyword Filter','status'=>1,'parent_id'=>$returnid12),
                    array('clientID' =>$clientID,'resource'=>'Restricted URLs','status'=>1,'parent_id'=>$returnid12),
          );
          $returnid13 = $this->userRec->insertresource('tblResourcesManage',$data13)+1;
      /***************%*******************/
          $dataval13 = array(    
                      array('clientID' =>$clientID,'module'=>'admin','controller'=>'myaccount','action'=>'configuration','res_id'=>$returnid13+1),
            );
          $returnidval13 = $this->userRec->insertresource('resources',$dataval13);
      /*****************%%****************/
          $data14 = array(
                    array('clientID' =>$clientID,'resource'=>'General Settings','status'=>1,'parent_id'=>0),
                    array('clientID' =>$clientID,'resource'=>'General Settings','status'=>1,'parent_id'=>$returnid13),
          );
          $returnid14 = $this->userRec->insertresource('tblResourcesManage',$data14)+1;

          //echo'<pre>';var_dump($cars);die;

          echo '<h1>all define controller and action are set according to clientid</h1>';die;
    }
}

