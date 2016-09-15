<?php

class Admin_WidgetsController extends IsadminController
{
    
    private $options;
    public function init()
    {
    	parent::init();
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();

    }

    public function indexAction()
    {
        $namespace                  = new Zend_Session_Namespace(); 
        $request                    =  $this->getRequest()->getPost(); 
    }

    public function feedAction()
    {        
        $objwid = new Admin_Model_Widgets();
        $request = $this->getRequest()->getParams();
        $serachtext = $this->_request->getPost('serachtext');
        //group
        $grouplist=$objwid->grouplist();
        $grouplistCount=$objwid->grouplistCount();
        $totalgrp = $grouplistCount[0]['total']; 

        
        $WidgetnameArray=$objwid->GetWidgetname(0);
        $this->view->WidgetnameArray = $WidgetnameArray;

        
        //post
        $postlist=$objwid->postlist();
        $postlistCount=$objwid->postlistCount();
        $totalpost = $postlistCount[0]['total']; 
        

        $this->view->totalgrp = $totalgrp;        
        $this->view->totalpost = $totalpost;
        $this->view->grouplist = $grouplist;
        $this->view->postlist = $postlist;
        

    }

    public function postsearchAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);

        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        {

            $objwid = new Admin_Model_Widgets();
            $postsearchvalue = $this->_request->getPost('postsearchvalue');                  
            $content=''; 
            $postlist      = $objwid->postlist($postsearchvalue);
            $postlistCount = $objwid->postlistCount($postsearchvalue);
            $totalpost     = $postlistCount[0]['total'];  

               if (count($postlist))
                { 
                 foreach($postlist as $value) : 
                 
                if($value['type']==5)
                {
                    $text=$value['PollText'];
                }
                elseif ($value['type']==7) {

                   $text=$value['surveyTitle'];
                }else
                {
                    $text=$value['Text'];
                }
                 $content.='<li data-id="'.$value['DbeeID'].'"><input id="pst_'.$value['DbeeID'].'"  type="checkbox" value="'.$value['DbeeID'].'" name="postid"><label for="pst_'.$value['DbeeID'].'">' .$text.'</label></li>';
                 endforeach;
               }              
                        
            
            
          }

        $data['content']=$content;


        return $response->setBody(Zend_Json::encode($data)); 

    }

    public function groupsearchAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);

        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        {

            $objwid = new Admin_Model_Widgets();
            $groupsearchvalue = $this->_request->getPost('groupsearchvalue');                  
            $content=''; 
            $grouplist=$objwid->grouplist($groupsearchvalue);
            $grouplistCount=$objwid->grouplistCount($groupsearchvalue);
            $totalgrp = $grouplistCount[0]['total'];                
                        
            if (count($grouplist))
            { 
             foreach($grouplist as $value) :                          

             $content.='<li data-id="'.$value['ID'].'"><input id="grp_'.$value['ID'].'"  type="checkbox" value="'.$value['ID'].'" name="goupid"><label for="grp_'.$value['ID'].'">' .$value['GroupName'].'</label></li>';
             endforeach;
             if($totalgrp > count($grouplist)){ 
                    $content.='<div class="show_more_main" id="show_more_main'.$value['ID'].'" style="cursor:pointer;">
                        <span id="'.$value['ID'].'" data-type="group" class="show_more" title="Load more groups">Show more</span>
                        <span class="loding" style="display: none;"><span class="loding_txt">Loading….</span></span></div>';                        
                }
            }
            
          }

        $data['content']=$content;


        return $response->setBody(Zend_Json::encode($data)); 

    }

    public function feedmoreAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);

        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
        {

            $objwid = new Admin_Model_Widgets();
            $optiontype = $this->_request->getPost('optiontype');            
            $lastid     = $this->_request->getPost('lastId');      
            $content='';
            if($optiontype=='group'){
                $grouplist           = $objwid->grouplist('',$lastid); 
                $grouplistCount      = $objwid->grouplistCount('',$lastid);
                $totalgrp            = $grouplistCount[0]['total'];                
                if (count($grouplist))
                { 
                 foreach($grouplist as $value) :                          

                 $content.='<li data-id="'.$value['ID'].'"><input id="grp_'.$value['ID'].'"  type="checkbox" value="'.$value['ID'].'" name="goupid"><label for="grp_'.$value['ID'].'">' .$value['GroupName'].'</label></li>';
                 endforeach;
                 if($totalgrp > count($grouplist)){ 
                        $content.='<div class="show_more_main" id="show_more_main'.$value['ID'].'" style="cursor:pointer;">
                            <span id="'.$value['ID'].'" data-type="group" class="show_more" title="Load more groups">Show more</span>
                            <span class="loding" style="display: none;"><span class="loding_txt">Loading….</span></span></div>';                        
                    }
                }
            }else
            {
               $postlist      = $objwid->postlist('',$lastid);
               $postlistCount = $objwid->postlistCount('',$lastid);
               $totalpost     = $postlistCount[0]['total'];  
               if (count($postlist))
                { 
                 foreach($postlist as $value) : 

                if($value['type']==5)
                {
                    $text=$value['PollText'];
                }
                elseif ($value['type']==7) {

                   $text=$value['surveyTitle'];
                }else
                {
                    $text=$value['Text'];
                }                         

                 $content.='<li data-id="'.$value['DbeeID'].'"><input id="pst_'.$value['DbeeID'].'"  type="checkbox" value="'.$value['DbeeID'].'" name="postid"><label for="pst_'.$value['DbeeID'].'">' .$text.'</label></li>';
                 endforeach;
                  if($totalpost > count($postlist)){ 
                        $content.='<div class="show_more_main" id="show_more_main'.$value['DbeeID'].'" style="cursor:pointer;">
                            <span id="'.$value['DbeeID'].'" data-type="post" class="show_more" title="Load more groups">Show more</span>
                            <span class="loding" style="display: none;"><span class="loding_txt">Loading….</span></span></div>';                        
                    }
                 }
                        
            }

          }

            $data['content']=$content;


        return $response->setBody(Zend_Json::encode($data)); 

    }

    function genRandomPassword($length = 8)
    {
        $salt = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $len = strlen($salt);
        $makepass = '';

        $stat = @stat(__FILE__);
        if(empty($stat) || !is_array($stat)) $stat = array(php_uname());

        mt_srand(crc32(microtime() . implode('|', $stat)));

        for ($i = 0; $i < $length; $i ++) {
            $makepass .= $salt[mt_rand(0, $len -1)];
        }

        return $makepass;

        //$code = md5($this->genRandomPassword(32));
    }

    public function insertcodeAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $filter = new Zend_Filter_StripTags();
        if ($this->getRequest()->isXmlHttpRequest())
        {
            $request    =  $this->getRequest()->getPost();            
           
            $rand_code  = md5($this->genRandomPassword(32));            
            $widgetjson         = $this->_request->getPost('widgetjson');

            $jsondata = json_decode($widgetjson, TRUE);
            
            $keyidArray=$jsondata['id'];
            $key_id= implode(",",$keyidArray);
           
            if($jsondata['type']=='Group')
            {
                $type=0;
            }else
            {
                $type=1;
            }
            $mode='update';
            $ID                 = $filter->filter($jsondata['widgetId']);
            $data1['clientID']  = $filter->filter(clientID); 
            if($ID==""){  
            $data1['rand_code'] = $filter->filter($rand_code); 
            $mode='insert';
            }           
            $data1['type']      = $type;
            $data1['key_id']    = $filter->filter($key_id);
            $data1['name']      = $filter->filter($jsondata['name']);
            $data1['title']     = $filter->filter($jsondata['title']);
            $data1['widgetjson']= $filter->filter($widgetjson);
            $data1['status']    =1;
          
    
            if(!empty($ID))
            $insertcode = $this->myclientdetails->updatedata_global('tbl_widgets',$data1, 'id', $ID);
            else
            $insertcode = $this->myclientdetails->insertdata_global('tbl_widgets',$data1);
    
            $content = 'widget code '.$mode.' successfully!';
            if($ID=="")
            {
                $ID=$insertcode;
            }
            $data['content'] = $content;
            $data['widgetID'] = $ID;
            $data['rand_code'] = $rand_code;
           
        }
        else
        {
            $data['status']  = 'error';
            $data['message'] = 'Configuration has not been Set successfully';
        }
        return $response->setBody(Zend_Json::encode($data));
    }

    public function GetWidgetListAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        
        if ($this->getRequest()->isXmlHttpRequest())
        {
             $objwid    = new Admin_Model_Widgets();            
             $datawidget = $objwid->GetWidgetname();
           
             $data['widgetName'] = $datawidget['name'];
             $data['widgetID']   = $datawidget['id'];
             $data['jsonData']   = $datawidget['widgetjson'];
             $data['code']   = $datawidget['rand_code'];
    
        }
        else
        {
            $data['status']  = 'error';
            $data['message'] = 'error';
        }
        return $response->setBody(Zend_Json::encode($data));
    }

    public function widgetdetailAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        
        if ($this->getRequest()->isXmlHttpRequest())
        {
             $request    =  $this->getRequest()->getPost();                   
             $ID         = $this->_request->getPost('widgetID');
             $objwid    = new Admin_Model_Widgets();            
             $datawidget = $objwid->GetWidget($ID);
           
             $data['widgetName'] = $datawidget['name'];
             $data['widgetID']   = $datawidget['id'];
             $data['type']       = $datawidget['type'];
             $data['jsonData']   = $datawidget['widgetjson'];
             $data['code']       = $datawidget['rand_code'];
             $data['key_id']     = $datawidget['key_id'];
             $data['title']      = $datawidget['title'];

             $resrows=$objwid->getresult($datawidget['type'],$datawidget['key_id']);
             $content="";
             if(count($resrows) > 0)
            {
                if($datawidget['type']==0)
                { 
                    foreach ($resrows as $key => $value) {
                     $content.='<li data-id="'.$value['ID'].'" class="activeList"><a href="javascript:void(0)" class="closeBtn">✖</a><span>'.$value['GroupName'].'</span></li>';
                    }

                }else
                {
                    foreach ($resrows as $key => $value) {
                        if($value['Type']==5)
                        {
                            $text=$value['PollText'];
                        }
                        elseif ($value['Type']==7) {

                           $text=$value['surveyTitle'];
                        }else
                        {
                            $text=$value['Text'];
                        }
                    $content.='<li data-id="'.$value['DbeeID'].'" class="activeList"><a href="javascript:void(0)" class="closeBtn">✖</a><span>'.$text.'</span></li>';
                    }

                }
                
               $data['content'] = $content;

            }
        }
        else
        {
            $data['status']  = 'error';
            $data['message'] = 'error';
        }
        return $response->setBody(Zend_Json::encode($data));
    }


    public function savelistAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        
        if ($this->getRequest()->isXmlHttpRequest())
        {
             $request      =  $this->getRequest()->getPost();                   
             $type         = $this->_request->getPost('type');
             $objwid       = new Admin_Model_Widgets();            
             $datawidget   = $objwid->GetWidgetname($type);             
            
           
           if($type==1)
           { $content      ="";
             if(count($datawidget) >0) {
              foreach ($datawidget as $key => $value) {
               $content.='<option value="'.$value['id'].'" data-widgettype="'.$value['type'].'">'.$value['name'].'</option>';
              }
            }
          }else
          {  
              $content      ="";
             if(count($datawidget) >0) {
              foreach ($datawidget as $key => $value) {
               $content.='<option value="'.$value['id'].'" data-widgettype="'.$value['type'].'">'.$value['name'].'</option>';
              }
           }
          }

        $data['content'] = $content;
    
        }
        else
        {
            $data['status']  = 'error';
            $data['message'] = 'error';
        }
        return $response->setBody(Zend_Json::encode($data));
    }

    public function deletewidgetAction()
    {
      $data = array();
      $this->_helper->layout()->disableLayout();
      $this->_helper->viewRenderer->setNoRender(true);
      $response = $this->getResponse();
      $response->setHeader('Content-type', 'application/json', true);
      
      if ($this->getRequest()->isXmlHttpRequest())
      {
        $widgetID = (int) $this->_request->getPost('widgetID');             
        $this->myclientdetails->deletedata_global('tbl_widgets','id',$widgetID);            

      }
        return $response->setBody(Zend_Json::encode($_POST));
    }


}

