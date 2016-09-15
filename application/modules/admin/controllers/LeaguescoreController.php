<?php

class Admin_LeaguescoreController extends IsadminController
{
    
    private $options;
    public function init()
    {
    	parent::init();
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
        $this->objscoore = new Admin_Model_Leaguescore();
       
    }

    public function indexAction()
    {

        $namespace = new Zend_Session_Namespace();  


        $request     =  $this->getRequest()->getPost();



        $type = $this->getRequest()->getParam('type');
        if($type!="")
        {
          if($type==1)
            {
                $result      =  $this->objscoore->FilterResultScoreGroup($type); 
            }
            else  if ($type==2) 
            {
                $result      =  $this->objscoore->FilterResultScoreEvent($type);
            }
            else if ($type==0)
            {
                $result      =  $this->objscoore->FilterResultScorePost($type);
            }

            $this->view->result   =    $result;
            $this->view->type     =    $type;
        }else
        {
          $this->view->type     =    5;
          $this->view->result   =    '';
        }

        $influencecomment='';
        $searchuser        =  $request['searchuser'];
        $posttype          =  $request['type'];
        $influencecomment   =  $request['influencecomment'];
        //print_r($request); die;
        if($posttype=="" && $type!="")
        {
           $posttype = $type;
        }
        if($influencecomment!="")
        {
           $this->view->influencecomment     =    $influencecomment;
         }else
         {
           $this->view->influencecomment     =    '';
         }

      // print_r($request); die;

        $SearchUser        = $this->myclientdetails->customEncoding($searchuser,'allusersAlphabat');
        $userData = $this->objscoore->ScoreUser(2,1,$SearchUser,'',$influencecomment,$posttype); 
         $this->view->countrow= $this->objscoore->ScoreUserCount(2,1,$SearchUser,'',$influencecomment,$posttype);       
        
         $this->view->totUsers   =    count($userData);

        $this->view->paginator = $userData;
        $this->view->searchuser = $searchuser;
      //hate and dislike 

        $userData2 = $this->objscoore->ScoreUser(1,1,$SearchUser,'',$influencecomment,$posttype);
        $this->view->countrow2= $this->objscoore->ScoreUserCount(1,1,$SearchUser,'',$influencecomment,$posttype);  
        
        $this->view->paginator2 = $userData2;

        $userData3 = $this->objscoore->ScoreUser(4,1,$SearchUser,'',$influencecomment,$posttype); 
        $this->view->countrow3= $this->objscoore->ScoreUserCount(4,1,$SearchUser,'',$influencecomment,$posttype);   
        
        $this->view->paginator3 = $userData3;
        $this->view->countrow4= $this->objscoore->ScoreUserCount(5,1,$SearchUser,'',$influencecomment,$posttype);  

        $userData4 = $this->objscoore->ScoreUser(5,1,$SearchUser,'',$influencecomment,$posttype);  
        
        $this->view->paginator4 = $userData4;

        $this->view->totalpostinf   =count($this->objscoore->FilterResultScorePost());
        $this->view->totalgrpinf    =count($this->objscoore->FilterResultScoreGroup());
        $this->view->totaleventinf  =count($this->objscoore->FilterResultScoreEvent());

    }

     public function scoregivenAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);

        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        {

            
            $request           =  $this->getRequest()->getPost();            
            $score             =  $request['score'];
            $order             =  $request['order']; 
            $searchval         =  $request['searchval'];
            $lastid             = $request['lastId']; 
            if($score==""){ 
              $score             = 2;
            }
            $SearchUser        = $this->myclientdetails->customEncoding($searchval,'allusersAlphabat');

            if($order=='received')
            {
              $isowner='1';
              $type='owner';

            }else
            {
              $isowner='0';
                 $type='user';
            }

            $posttype        =  $request['type'];
            $influencecomment   =  $request['influencecomment'];
         
               
            $userData = $this->objscoore->ScoreUser($score,$isowner,$SearchUser,$lastid,$influencecomment,$posttype); 
          
            $content='';

            $total=count($userData);
            
            if ($total > 0)
            { 
             foreach($userData as $user) :
             $rowchange    =   0; 

             if($rowchange%2==1) {$color = '#CCC';} else {$color ='#FFF';}

              $where = array('DbeeID'=>$user['DbeeID']);

            $resultArray = $this->myclientdetails->getRowMasterfromtable('tblDbees',array('dbuser'=>'User'),$where);

            $content.='<tr id="'.$user['total'].'">
                        <td>
                          <label>
                            <input class="goupuser" type="checkbox" value="'.$user['UserID'].'" name="goupuserid" id="goupuserid'.$user['UserID'].'">
                            <label for="goupuserid'.$user['UserID'].'"></label>
                          </label>
                        </td>
                        <td>
                           <div class="usImg">
                             <img class="imgStyle" src="'.IMGPATH.'/users/small/'.$user['ProfilePic'].'" width="40" height="40"></div><div class="udetailtop">
          <div class="oneline"> 
                           '.$this->myclientdetails->customDecoding($user['Name']).' '.$this->myclientdetails->customDecoding($user['lname']).'</div><div class="small oneline">';

                           
                          
                         $content.='</div></div></td>                          
                        <td><strong>'. $user['total'].'</strong></td>
                        
                    </tr>';
                   
    
             endforeach;
            }
            else
            {
              $content.='<tr id="'.$lastid.'"><td colspan="5" align="center"><div class="notfound"> </div></td></tr>';  
            }

            $data['content']=$content;
            $data['total']=$total;
        }

        return $response->setBody(Zend_Json::encode($data)); 
    }


     public function scorelistAction()
    {
        $data = array();

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);

        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        {

            $request            =  $this->getRequest()->getPost();
            $influencecomment   ='';
            $influencecomment   =  $request['influencecomment'];
            $type               =  $request['type'];
            $userData           = $this->objscoore->ScoreUser($influencecomment,$type);
            $content='';
            $total=count($userData);


            //$influencecomment

             

             $where = array('DbeeID'=>$influencecomment);
             $resultArray = $this->myclientdetails->getRowMasterfromtable('tblDbees',array('dbuser'=>'User'),$where1);

           


            if ($total > 0)
            { 
                  $content='<input type="hidden" name="influencecomment" id="influencecomment" value="'.$influencecomment.'"/>';
                 foreach($userData as $user)
                 {
                     $rowchange    =   0;                    

                     if($rowchange%2==1) {$color = '#CCC';} else {$color ='#FFF';}
                     
                    
                     $content.='<tr id="userslist'.$user['UserID'].'">
                        <td>
                          <label>
                            <input class="goupuser" type="checkbox" value="'.$user['UserID'].'" name="goupuserid" id="goupuserid'.$user['UserID'].'">
                            <label for="goupuserid'.$user['UserID'].'"></label>
                          </label>
                        </td>
                        <td><div class="usImg">
                       
                             <img class="imgStyle" src="'.IMGPATH.'/users/small/'.$user['ProfilePic'].'" width="40" height="40"></div><div class="udetailtop">
          <div class="oneline">
                           '.$this->myclientdetails->customDecoding($user['Name']).' '.$this->myclientdetails->customDecoding($user['lname']).'</div><div class="small oneline">';

                           if(count($resultArray) >0)
                           {
                                if($user['ParentType']==0)
                                {
                                  if($resultArray1['dbuser']==$user['Owner'])
                                  {
                                     $content.='Post creator';
                                  }
                                }

                                if($user['ParentType']==1)
                                {
                                  if($resultArray1['dbuser']==$user['Owner'])
                                  {
                                    $content.='Group creator';
                                  }
                                }
                            }
                          
             $content.='</div></div></td>
                       
                        <td>'.$user['likes'].'</td>
                        <td>'. $user['love'].'</td>    
                        <td><strong>'. $user['total'].'</strong></td>
                        
                    </tr>';
                 }
            }
            else
            {
             $content.='<tr><td colspan="5" align="center"><div class="notfound">No results <input type="hidden" id="postnotfound" value="0"></div></td></tr>';  
            }



            $userData2 = $this->objscoore->HateDislikeScoreUser($influencecomment,$type);   

            $content2='';
            $total2=count($userData2);
            if ($total2 > 0)
            { 
                  $content2='<input type="hidden" name="influencecomment" id="influencecomment" value="'.$influencecomment.'"/>';
                 foreach($userData2 as $user)
                 {
                     $rowchange    =   0;                    

                     if($rowchange%2==1) {$color = '#CCC';} else {$color ='#FFF';}

                     //$where2 = array('DbeeID'=>$user['DbeeID']);
                     //$resultArray2 = $this->myclientdetails->getRowMasterfromtable('tblDbees',array('dbuser'=>'User'),$where2);


                   $content2.='<tr id="userslist'.$user['UserID'].'">
                        <td>
                         <label>
                            <input class="goupuser" type="checkbox" value="'.$user['UserID'].'" name="goupuserid" id="goupuserid'.$user['UserID'].'">
                             <label for="goupuserid'.$user['UserID'].'"></label>
                           </label>
                        </td>
                        <td><div class="usImg"> 
                        
                             <img class="imgStyle" src="'.IMGPATH.'/users/small/'.$user['ProfilePic'].'" width="40" height="40"></div><div class="udetailtop">
          <div class="oneline">
                           '.$this->myclientdetails->customDecoding($user['Name']).' '.$this->myclientdetails->customDecoding($user['lname']).'</div><div class="small oneline">';

                          if(count($resultArray) >0)
                          {
                              if($user['ParentType']==0)
                              {
                                if($resultArray2['dbuser']==$user['Owner'])
                                {
                                   $content2.='Post creator';
                                }
                              }

                              if($user['ParentType']==1)
                              {
                                if($resultArray2['dbuser']==$user['Owner'])
                                {
                                  $content2.='Group creator';
                                }
                              }
                          }
                           
                        $content2.='</div></div></td>
                       
                       
                        <td>'. $user['hate'].'</td>  
                        <td>'.$user['dislike'].'</td>  
                        <td><strong>'. $user['total'].'</strong></td>
                        
                    </tr>';
                 }
            }
            else
            {
             $content2.='<tr><td colspan="5" align="center"><div class="notfound">No results <input type="hidden" id="usernotfound" value="0"></div></td></tr>';  
            }

            $data['content2']=$content2;
            $data['total2']=$total2;
            $data['content']=$content;
            $data['total']=$total;
        }
        return $response->setBody(Zend_Json::encode($data)); 
    }

    public function scoretypefilterAction()
    {
        $data=array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        

        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            $request     =  $this->getRequest()->getPost();
            
            $type        =  $request['type'];

            if($type==1)
            {
                $result      =  $this->objscoore->FilterResultScoreGroup($type); 
            }
            else  if ($type==2) 
            {
                $result      =  $this->objscoore->FilterResultScoreEvent($type);
            }
            else
            {
                $result      =  $this->objscoore->FilterResultScorePost($type);
            }
            
            $content="";

            if($type!="")
            {

                if(count($result) >0)
                {   
                    $content.='<input type="hidden" name="type" id="type" value="'.$type.'">';               
                    $content.='<select name="influencecomment" id="influencecomment" class="gh-tb pull-left">
                    <option value="">All</option>';
                    foreach ($result as $key => $value) 
                    {
                     $content.='<option value="'.$value['id'].'">'.$value['text'].'</option>';                    
                    }
                    $content.='</select>';

                }
                else
                {
                   $content.='no matching record found';
                } 
            }         
                
                $data['content']  = $content;
          
        }

       
        return $response->setBody(Zend_Json::encode($data)); 

    }




     public function lovelikepagingAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);

        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        {

            $request            =  $this->getRequest()->getPost();
            $influencecomment   ='';
            $influencecomment   =  $request['influencecomment'];
           // $totalpages         = $this->objscoore->InfluencePaging($influencecomment);
            $lastid             = $request['lastId'];      
            $userData           = $this->objscoore->lovelikePaging($influencecomment,$lastid); 
          
            $content='';

            $total=count($userData);
            
            if ($total > 0)
            { 
             foreach($userData as $user) :
             $rowchange    =   0; 

             if($rowchange%2==1) {$color = '#CCC';} else {$color ='#FFF';}

              $where = array('DbeeID'=>$user['DbeeID']);

            $resultArray = $this->myclientdetails->getRowMasterfromtable('tblDbees',array('dbuser'=>'User'),$where);

            $content.='<tr id="'.$user['total'].'">
                        <td>
                          <label>
                            <input class="goupuser" type="checkbox" value="'.$user['UserID'].'" name="goupuserid" id="goupuserid'.$user['UserID'].'">
                            <label for="goupuserid'.$user['UserID'].'"></label>
                          </label>
                        </td>
                        <td>
                           <div class="usImg">
                             <img class="imgStyle" src="'.IMGPATH.'/users/small/'.$user['ProfilePic'].'" width="40" height="40"></div><div class="udetailtop">
          <div class="oneline">
                           '.$this->myclientdetails->customDecoding($user['Name']).' '.$this->myclientdetails->customDecoding($user['lname']).'</div><div class="small oneline">';

                           if($user['ParentType']==0)
                            {
                              if($resultArray['dbuser']==$user['Owner'])
                              {
                                 $content.='Post creator';
                              } 
                            }

                            if($user['ParentType']==1)
                            {
                              if($resultArray['dbuser']==$user['Owner'])
                              {
                                $content.='Group creator';
                              } 
                            }
                          
                         $content.='</div></div></td>
                        <td>'.$user['likes'].'</td>
                        <td>'. $user['love'].'</td>    
                        <td><strong>'. $user['total'].'</strong></td>
                        
                    </tr>';
             endforeach;
            }
            else
            {
              $content.='<tr id="'.$lastid.'"><td colspan="5" align="center"><div class="notfound"> </div></td></tr>';  
            }

            $data['content']=$content;
            $data['total']=$total;
        }

        return $response->setBody(Zend_Json::encode($data)); 
    }


     public function hatedislikepagingAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);

        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        {

            $request            =  $this->getRequest()->getPost();
            $influencecomment   ='';
            $influencecomment   =  $request['influencecomment'];
          
            $lastid             = $request['lastId'];      
            $userData           = $this->objscoore->hatedislikePaging($influencecomment,$lastid); 
            /*
            echo'<pre>';
            print_r($userData);
            echo'</pre>';
           */

            
            $content='';

            $total=count($userData);
            
            if ($total > 0)
            { 
             foreach($userData as $user) :
             $rowchange    =   0; 

             if($rowchange%2==1) {$color = '#CCC';} else {$color ='#FFF';}

             $where = array('DbeeID'=>$user['DbeeID']);

            $resultArray = $this->myclientdetails->getRowMasterfromtable('tblDbees',array('dbuser'=>'User'),$where);


            $content.='<tr id="'.$user['total'].'">
                        <td>
                          <label>
                            <input class="goupuser" type="checkbox" value="'.$user['UserID'].'" name="goupuserid" id="goupuserid'.$user['UserID'].'">
                            <label for="goupuserid'.$user['UserID'].'"></label>
                          </label>
                        </td>
                        <td>
                        <div class="usImg">

                             <img class="imgStyle" src="'.IMGPATH.'/users/small/'.$user['ProfilePic'].'" width="40" height="40" ></div><div class="udetailtop">
          <div class="oneline">
                           '.$this->myclientdetails->customDecoding($user['Name']).' '.$this->myclientdetails->customDecoding($user['lname']).'</div><div class="small oneline">';



                           if($user['ParentType']==0)
                            {
                              if($resultArray['dbuser']==$user['Owner'])
                              {
                                 $content.='Post Creator';
                              }
                            }

                            if($user['ParentType']==1)
                            {
                              if($resultArray['dbuser']==$user['Owner'])
                              {
                                $content.='Group Creator';
                              }
                            }
                          
                        $content.='</div></div></td>
                       
                        <td>'. $user['hate'].'</td>
                        <td>'.$user['dislike'].'</td>                            
                        <td><strong>'. $user['total'].'</strong></td>
                        
                    </tr>';
             endforeach;
            }
            else
            {
              $content.='<tr id="'.$lastid.'"><td colspan="5" align="center"></td></tr>';  
            }

            $data['content']=$content;
            $data['total']=$total;
        }

        return $response->setBody(Zend_Json::encode($data)); 
    }


}

