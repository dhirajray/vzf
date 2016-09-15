<?php

class Admin_InfluenceController extends IsadminController
{
    
    private $options;
    public function init()
    {
    	parent::init();
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
       
        $this->infobj = new Admin_Model_Influence();


    }

    public function indexAction()
    {

        $namespace                  = new Zend_Session_Namespace();  

        $request                    =  $this->getRequest()->getPost();
            
        $paginator                   = $this->infobj->InfluenceUser(); 
      
        $this->view->paginator       = $paginator;

        $paginatorcount              = count($this->infobj->InfluenceUser('','','count')); 


        $this->view->paginatorcount  = $paginatorcount;


        $paginator2                  = $this->infobj->InfluenceUserPost(); 
       
        $this->view->paginator2      = $paginator2; 

        $paginatorcount2             = count($this->infobj->InfluenceUserPost('','','count')); 


        $this->view->paginatorcount2 = $paginatorcount2;         


        $this->view->totalpostinf    =count($this->infobj->FilterResultPost());
        $this->view->totalgrpinf     =count($this->infobj->FilterResultGroup());
        $this->view->totaleventinf   =count($this->infobj->FilterResultEvent());
        
    }

    public function inflistAction()
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
            $userData           = $this->infobj->InfluenceUser($influencecomment,$type); 


            $content='';
            $total=count($userData);
           
            if ($total > 0)
            { 
                  $content='<input type="hidden" name="influencecomment" id="influencecomment" value="'.$influencecomment.'"/>';
                 foreach($userData as $user)
                 {
                     $rowchange    =   0;                    

                     if($rowchange%2==1) {$color = '#CCC';} else {$color ='#FFF';}

                     $content.='<tr id="'.$user['total'].'">
                                <td>
                                    <label>
                                        <input class="goupuser" type="checkbox" value="'.$user['UserID'].'" name="goupuserid" id="goupuserid'.$user['UserID'].'">
                                        <label for="goupuserid'.$user['UserID'].'"></label>
                                    </label>
                                </td>
                                <td>
                                     <img class="imgStyle" src="'.IMGPATH.'/users/small/'.$user['ProfilePic'].'" width="40" height="40">
                                    '.$this->myclientdetails->customDecoding($user['Name']).' '.$this->myclientdetails->customDecoding($user['lname']).'
                                  
                                </td>
                                <td>'.$user['total'].'</td>
                                <td>
                                    <div class="ViewAllClickedUsers">
                                       <input type="hidden" name="page" id="page" value="2">
                                        <a class="btn btn-mini btn-yellow" href="#" data-articleid="'.$user['ArticleId'].'" data-articletype="1">
                                        <i class="fa fa-user"> </i> 
                                       Users
                                        </a>
                                    </div>
                              </td>
                                
                            </tr>';
                 }
            }
            else
            {
             $content.='<tr><td colspan="4" align="center"><div class="notfound">No results <input type="hidden" id="usernotfound" value="0"></div></td></tr>';  
            }

            $content2='';
            $userData2           = $this->infobj->InfluenceUserPost($influencecomment,$type); 
            $total2              = count($userData2);

            //print_r($userData2);

            if ($total2 > 0)
            { 
                  $content2='<input type="hidden" name="influencecomment" id="influencecomment" value="'.$influencecomment.'"/>';
                 foreach($userData2 as $user)
                 {
                     $rowchange    =   0;                    

                     if($rowchange%2==1) {$color = '#CCC';} else {$color ='#FFF';}

                     $content2.='<tr id="'.$user['total'].'">
                                <td>
                                    <label>
                                        <input class="goupuser" type="checkbox" value="'.$user['UserID'].'" name="goupuserid" id="goupuserid'.$user['UserID'].'">
                                        <label for="goupuserid'.$user['UserID'].'"></label>
                                    </label>
                                </td>
                                <td>                                   
                                <img class="imgStyle" src="'.IMGPATH.'/users/small/'.$user['ProfilePic'].'" width="40" height="40">
                                <span> '.$this->myclientdetails->customDecoding($user['Name']).' '.$this->myclientdetails->customDecoding($user['lname']).'</span>
                                  
                                </td>   
                                <td> '.$user['text'].'</td>
                                <td>'.$user['total'].'</td>
                                <td>
                                    <div class="ViewAllClickedUsers">
                                       <input type="hidden" name="page" id="page" value="2">
                                        <a class="btn btn-mini btn-yellow" href="#" data-articleid="'.$user['ArticleId'].'" data-articletype="0">
                                        <i class="fa fa-user"> </i> 
                                       Users
                                        </a>
                                    </div>
                              </td>
                                
                            </tr>';
                 }
            }
            else
            {
             $content2.='<tr><td colspan="5" align="center"><div class="notfound">No results <input type="hidden" id="postnotfound" value="0"></div></td></tr>';  
            }
           
            $data['content']=$content;
            $data['total']=$total;
            $data['content2']=$content2;
            $data['total2']=$total2;

        }


        return $response->setBody(Zend_Json::encode($data)); 

    }


    public function inflistpagingAction()
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
           // $totalpages         = $this->deshboard->InfluencePaging($influencecomment);
            $lastPageId         = $this->_request->getPost('ID');
            $lastPageId         = ($lastPageId=='')? '' : $lastPageId; 
            $lastid             = $request['lastId'];      
            $userData           = $this->infobj->InfluencePaging($influencecomment,$lastid,$lastPageId); 

            if($lastPageId!="")
             $return="";
                
            if(count($userData)>0)
                $data['page'] = $lastPageId+1;
            else
                $data['page'] = $lastPageId;

            $content='';
            
            if (count($userData))
            { 
             foreach($userData as $user) :
             $rowchange    =   0; 

             if($rowchange%2==1) {$color = '#CCC';} else {$color ='#FFF';}

             $content.='<tr id="'.$user['total'].'">
                        <td>
                            <label>
                                <input class="goupuser" type="checkbox" value="'.$user['UserID'].'" name="goupuserid" id="goupuserid'.$user['UserID'].'">
                                <label for="goupuserid'.$user['UserID'].'"></label>
                            </label>
                        </td>
                        <td>                            
                            <img class="imgStyle" src="'.IMGPATH.'/users/small/'.$user['ProfilePic'].'" width="40" height="40">

                            '.$this->myclientdetails->customDecoding($user['Name']).' '.$this->myclientdetails->customDecoding($user['lname']).'
                          
                        </td>   
                        <td>'.$user['total'].'</td>
                        <td>
                                    <div class="ViewAllClickedUsers">
                                       
                                        <a class="btn btn-mini btn-yellow" href="#" data-articleid="'.$user['ArticleId'].'" data-articletype="1">
                                        <i class="fa fa-user"> </i> 
                                       Users
                                        </a>
                                    </div>
                              </td>
                        
                    </tr>';
             endforeach;
            }
            else
            {
              $content.='<tr id="'.$lastid.'"></tr>';  
            }

            $data['content']=$content;
            $data['total']=$total;

        }


        return $response->setBody(Zend_Json::encode($data)); 

    }

    public function inflistpostpagingAction()
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
           // $totalpages         = $this->deshboard->InfluencePaging($influencecomment);
            $lastid             = $request['lastId'];   
            $lastPageId2        = $this->_request->getPost('ID');
            $lastPageId2        = ($lastPageId2=='')? '' : $lastPageId2;   
            $userData           = $this->infobj->InfluencePostPaging($influencecomment,$lastid,$lastPageId2); 
           

            if($lastPageId2!="")
             $return="";
                
            if(count($userData)>0)
                $data['page2'] = $lastPageId2+1;
            else
                $data['page2'] = $lastPageId2;

            $content='';
            
            if (count($userData))
            { 
             foreach($userData as $user) :
             $rowchange    =   0; 

             if($rowchange%2==1) {$color = '#CCC';} else {$color ='#FFF';}

             $content.='<tr id="'.$user['total'].'">
                        <td>
                            <label>
                                <input class="goupuser" type="checkbox" value="'.$user['UserID'].'" name="goupuserid" id="goupuserid'.$user['UserID'].'">
                                <label for="goupuserid'.$user['UserID'].'"></label>
                            </label>
                        </td>
                        <td>
                           
                             <img class="imgStyle" src="'.IMGPATH.'/users/small/'.$user['ProfilePic'].'" width="40" height="40">

                            '.$this->myclientdetails->customDecoding($user['Name']).' '.$this->myclientdetails->customDecoding($user['lname']).'
                         
                        </td>  
                         <td> '.$user['text'].'</td> 
                        <td>'.$user['total'].'</td>
                        <td>
                            <div class="ViewAllClickedUsers">
                             
                                <a class="btn btn-mini btn-yellow" href="#" data-articleid="'.$user['ArticleId'].'" data-articletype="0">
                                <i class="fa fa-user"> </i> 
                               Users
                                </a>
                            </div>
                        </td>
                        
                    </tr>';
             endforeach;
            }
            else
            {
              $content.='<tr id="'.$lastid.'"></tr>';  
            }

            $data['content']=$content;
            $data['total']=$total;

        }


        return $response->setBody(Zend_Json::encode($data)); 

    }




    public function influncetypefilterAction()
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
                $result      =  $this->infobj->FilterResultGroup($type); 
            }
            else  if ($type==2) 
            {
                $result      =  $this->infobj->FilterResultEvent($type);
            }
            else
            {
                $result      =  $this->infobj->FilterResultPost($type);
            }
            
             
            
            
            $content="";
            if($type!="")
            {
                if(count($result) >0)
                {   
                    $content.='<input type="hidden" name="type" id="type" value="'.$type.'">';
                    $content.='<select name="influencecomment" id="influencecomment" class="gh-tb">
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
            $type='';
            $articleid = $this->_request->getPost('articleid');
           // $type = $this->_request->getPost('type');
            $articletype = $this->_request->getPost('articletype');         
            $datatypexx  = $this->_request->getPost('datatypexx');
                $u= new Admin_Model_Usergroup();
                $common = new Admin_Model_Common();
                
                if($articletype==0)
                {
                  $result = $this->infobj->Userlist($articleid,$type,$articletype);
                }
                else
                {
                  $result = $this->infobj->Userlist($articleid,$type,$articletype);
                }


                $grouprs = $u->list_groupall();                
                $grouplist='';
                if(count($result)>0){
                    foreach ($result as $value)
                    {
                        $countLabel = ($value['totalclick']=='1') ? 'time' : 'times';
						$valuepic = $common->checkImgExist($value['ProfilePic'],'userpics','default-avatar.jpg');
                        $content .= "<div class='userFatchList boxFlowers' title='".$this->myclientdetails->customDecoding($value['Name'])."' socialFriendlist='true'>
                        <label class='labelCheckbox'>
                        <input type='checkbox' value='".$value['UserID']."' checkvalue='".$checkvalue."' class='inviteuser-search' name='goupuserid'>
                        <div class='follower-box'>
                        <div class='usImg'><img class='img border' align='left' src='".IMGPATH."/users/small/".$valuepic."' border='0' /></div>
                        ".$this->myclientdetails->customDecoding($value['Name'])." ".$this->myclientdetails->customDecoding($value['lname'])."
                        <br>";
                        if($datatypexx=='user')
                        {
                          $content .= "Clicked ".$value['totalclick']." ".$countLabel;
                        }
                        $content .= "</div>
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

