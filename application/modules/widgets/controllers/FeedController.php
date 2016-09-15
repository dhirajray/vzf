<?php

class Widgets_FeedController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        parent::init();
    }

    public function indexAction()
    {
        // action body
        $objwidgetsmod = new Widgets_Model_Widgets();
        $code = $this->getRequest()->getParam('code');
      
        if($code!="")
        { 
            $checkcode=$objwidgetsmod->checkcode($code);
          

            
            if($checkcode['id'] < 1)
            {
                echo"wrong code!";
                exit;
            }

            $resrows=$objwidgetsmod->getresult($code);
         
            $content = '';
            if(count($resrows) > 0)
            {
                if($checkcode['type']==0)
                {   
                    foreach ($resrows as $key => $value) {
                       $doted='';
                       $grpuptext=strip_tags($value['GroupName']);
                       if(strlen($grpuptext) > 300)
                       {
                         $doted=' ..';
                       }

                       $grpuptext=substr($grpuptext,0,300).$doted;

                       $content.='<li><a href="'.BASE_URL.'/group/groupdetails/group/'.$value['ID'].'" target="_blank">'.$grpuptext.'</a></li>';
                    }
                }


                if($checkcode['type']==1)
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
                       $doted='';
                       $posttext=strip_tags($text);
                       if(strlen($posttext) > 300)
                       {
                         $doted=' ..';
                       }
                       
                       $posttext=substr($posttext,0,300).$doted;
                       $content.= '<li><a href="'.BASE_URL.'/dbee/'.$value['dburl'].'" target="_blank">' .$posttext.'</a></li>';
                    }
                }
                $jsondata = json_decode($checkcode['widgetjson'], TRUE);
                $saveMyList = $jsondata['theme'];
                $style = '<style type="text/css">
                        body{font-family: arial; margin: 0px; padding: 0px;  background: transparent;  position: relative; }
                        h1{font-size: 24px; color: #333; padding: 10px; margin: 0px;box-shadow: 0 2px 2px rgba(0,0,0,0.2); position: absolute; top: 0px; right: 0px; left: 0px; border-radius: 5px 5px 0px 0px; z-index: 1}
                        ul{padding: 0px; list-style: none; top: 48px; position: absolute; left: 0px ;right: 0px; overflow: auto; bottom: 0px; margin: 0px;} 
                        ul:empty, h1:empty{display: none;}
                        h1:empty + ul{top: 0px}
                        li{padding: 10px;border-bottom:1px solid #ccc; animation:selectedOptionA 0.5s ease forwards;  position: relative;}
                        li span{display: block; margin-right: 40px;}
                        ul li:last-child{border-bottom: 0px;}
                        .activeList, .noListInWidget{animation:selectedOption 0.5s ease forwards;}
                        .mainCnt{border: 1px solid #ccc; border-radius: 5px; position: fixed; top: 0px; bottom: 0px;right: 0px; left: 0px; background: #fff; overflow: hidden;}
                        h1{background:'.$saveMyList['titlebackgroundColor'].'; color:'.$saveMyList['titlecolor'].'; border-radius:'.$saveMyList['boxBorderRadius'].'px '.$saveMyList['boxBorderRadius'].'px 0px 0px;}
                          .mainCnt{background:'.$saveMyList['backgroundColor'].'; border-color:'.$saveMyList['boxborderColor'].';  border-width:'.$saveMyList['boxBorderWidth'].'px; border-radius:'.$saveMyList['boxBorderRadius'].'px}
                          .noListInWidget{color:'.$saveMyList['color'].'}
                        li, li a{text-decoration:none;
                            color:'.$saveMyList['color'].';
                            border-color:'.$saveMyList['borderColor'].'
                        }
                    </style>';
                     echo  $style.'<div class="mainCnt">
                            <h1>'.$jsondata['title'].'</h1>
                            <ul>'.$content.'</ul>   
                        </div>';

                exit;
            }
        }


        
        

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
    }

   


}
