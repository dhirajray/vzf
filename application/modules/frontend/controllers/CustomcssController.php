<?php
class CustomcssController extends IsController
{  
	public function init()
    {
        parent::init();
    }
	 public function indexAction() 
    {


       $this->_helper->layout()->disableLayout();
       $this->_helper->viewRenderer->setNoRender(true);
       $response = $this->getResponse();
       $response->setHeader('Content-type', 'text/css', true);
       if($this->plateformInterface!=''){
      $json = json_decode($this->plateformInterface);
      //echo '<pre>';
     // print_r($json); exit;
      $p='';
       if(isset($json->body) && $json->body!=''){
          $body =  $json->body;
         $p .= "
         body:not([id=\"frontPage2\"]), .loadingPage #fullScreenBg{
          background: -webkit-linear-gradient(0deg, ".$body->fill->startColor.", ".$body->fill->stopColor.");
          background: -o-linear-gradient(0deg, ".$body->fill->startColor.", ".$body->fill->stopColor.");
          background: -moz-linear-gradient(0deg, ".$body->fill->startColor.", ".$body->fill->stopColor.");
          background: linear-gradient(0deg, ".$body->fill->startColor.", ".$body->fill->stopColor.");
       }";

       if($body->backgroundTexture!=''){
          $bgpos = 'inherit';
          if($body->backgroundImage=="true") $bgpos =  'cover';
            $p .= " #fullScreenBg, .loadingPage #fullScreenBg{ background:url(".$body->backgroundTexture."); background-size:".$bgpos."; }";
       }
         $p .= "";

       } 
        if(isset($json->page) && $json->page!=''){
       $page =  $json->page;
       $p .="#pageCenterCont{
                    background: -webkit-linear-gradient(0deg, ".$page->fill->startColor.", ".$page->fill->stopColor.");
                    background: -o-linear-gradient(0deg, ".$page->fill->startColor.", ".$page->fill->stopColor.");
                    background: -moz-linear-gradient(0deg, ".$page->fill->startColor.", ".$page->fill->stopColor.");
                    background: linear-gradient(0deg, ".$page->fill->startColor.", ".$page->fill->stopColor.");
                  }
                    #postHeader{background:transparent}
                    
                  ";
        }
        if(isset($json->pageInner) && $json->pageInner!=''){
        $pageInner =  $json->pageInner;
         $p .=  " #pageContent{
                    background: -webkit-linear-gradient(0deg, ".$pageInner->fill->startColor.", ".$pageInner->fill->stopColor.");
                    background: -o-linear-gradient(0deg, ".$pageInner->fill->startColor.", ".$pageInner->fill->stopColor.");
                    background: -moz-linear-gradient(0deg, ".$pageInner->fill->startColor.", ".$pageInner->fill->stopColor.");
                    background: linear-gradient(0deg, ".$pageInner->fill->startColor.", ".$pageInner->fill->stopColor.");
                  }

                
                ";

        }
        if(isset($json->leftSideContent) && $json->leftSideContent!=''){
       
                 $leftContent =  $json->leftSideContent;

     $p .= " 
     #leftsideMenu ul, #leftsideMenu ul ul, .openMysideBar #leftsideMenu, .closeNoti , #leftsideMenu .navMinicon {
           background: -webkit-linear-gradient(0deg, ".$leftContent->fill->startColor.", ".$leftContent->fill->stopColor.");
            background: -o-linear-gradient(0deg, ".$leftContent->fill->startColor.", ".$leftContent->fill->stopColor.");
            background: -moz-linear-gradient(0deg, ".$leftContent->fill->startColor.", ".$leftContent->fill->stopColor.");
            background: linear-gradient(0deg, ".$leftContent->fill->startColor.", ".$leftContent->fill->stopColor.");
            color:".$leftContent->color." !important; 
      }
      #leftsideMenu .fa{color:".$leftContent->color." !important; }
      #leftsideMenu li a span, #leftsideMenu li > ul li a, #leftsideMenu li:hover > ul li a{color:rgba(".$this->hex2rgb($leftContent->color).", 0.7) ;}
      #leftsideMenu li.active:before, #leftsideMenu li:hover:before, #rightListing .mobSortlist .rboxContainer:not(#InTMemberlist) a:hover:before{content:''; border-color: ".$leftContent->hoverFontColor.";}
      #pageContent.active .navMinicon:before{color: ".$leftContent->hoverFontColor.";}
     

      #leftsideMenu ul:not(.slides) li.active:after{border-left-color:".$leftContent->color.";}
      #leftsideMenu li:hover > ul:after{border-right-color:".$leftContent->fill->stopColor."}
       #leftsideMenu li:hover a , #leftsideMenu li:hover  a .fa,  #leftsideMenu li:hover span, #leftsideMenu li ul li:hover > a{  color:".$leftContent->hoverFontColor."; }

      

      ";



       }

        /*if(isset($json->leftSideActive) && $json->leftSideActive!=''){
          $leftActiveMenu = $json->leftSideActive;
          $p .= "  
           #pageContent.active .navMinicon:before{color: ".$leftActiveMenu->fill->startColor.";}
           #leftsideMenu li.active > a{
              background: -webkit-linear-gradient(0deg, ".$leftActiveMenu->fill->startColor.", ".$leftActiveMenu->fill->stopColor.");
              background: -o-linear-gradient(0deg, ".$leftActiveMenu->fill->startColor.", ".$leftActiveMenu->fill->stopColor.");
              background: -moz-linear-gradient(0deg, ".$leftActiveMenu->fill->startColor.", ".$leftActiveMenu->fill->stopColor.");
              background: linear-gradient(0deg, ".$leftActiveMenu->fill->startColor.", ".$leftActiveMenu->fill->stopColor.");
              color:".$leftActiveMenu->color." !important; 
          }
          #leftsideMenu li.active + li {border-top:1px solid ".$leftActiveMenu->fill->startColor.";}

          #leftsideMenu li.active > a span, #leftsideMenu li.active  a .fa{
            color:".$leftActiveMenu->color." !important; 
          }
          ";
        }*/

    

        if(isset($json->header) && $json->header!=''){
        $header =  $json->header;
       $p .=" 

    #topMenu, #sideBarFeeds .feddmtitle, #header{
                    background: -webkit-linear-gradient(0deg, ".$header->fill->startColor.", ".$header->fill->stopColor.");
                    background: -o-linear-gradient(0deg, ".$header->fill->startColor.", ".$header->fill->stopColor.");
                    background: -moz-linear-gradient(0deg, ".$header->fill->startColor.", ".$header->fill->stopColor.");
                    background: linear-gradient(0deg, ".$header->fill->startColor.", ".$header->fill->stopColor.");
                    color:".$header->color." !important; 
                  }
      #sideBarFeeds .feddmtitle a{color:".$header->color." !important; }
      #sideBarFeeds .totalCountWrp{background:".$header->fill->stopColor."}
      #sideBarFeeds .totalCountWrp a{color:".$header->color." !important; }
      .headerMenu .fa, .mobileDevicesHeader .fa,  #mySideMenu a, #notiListing .dbLoader{ color:".$header->color." !important; }
      
      .openTopMenu .headerMenu{background:rgba(".$this->hex2rgb($header->fill->startColor).", 1); }
      /*.ac_results.mainHeaderSearch:after{border-bottom-color:rgba(".$this->hex2rgb($header->fill->startColor).", 1); }
      .ac_results.mainHeaderSearch:before{border-bottom-color:".$this->colourCreator($header->fill->startColor,-20)."; }*/

      #mySideMenu, .startTakeATour #mySideMenu ul > li > a:not([data-id='myRighSideBtn']){background:rgba(".$this->hex2rgb($header->fill->startColor).", 0.8); }
      .startTakeATour  #mySideMenu ul > li > a:not([data-id='myRighSideBtn']){background:rgba(".$this->hex2rgb($header->fill->startColor).", 0.8); }
      
      
      .goTobackAdminMobile a:hover, .mobileDevicesHeader .socialFrTop a:hover, .headerMenu a:hover .fa, .headerMenu li.activeNotification i{color:".$header->hoverFontColor." !important;}
      .goToAdmin, #notifications-top-wrapper-dbee, .searchMemberList li:hover, 
      .popOverContainer,#dbRssWrapper, 
      .dbRssWrapper,  #ghstpopup,  #topMenu a.socialfriends, .goTobackAdminMobile a, 
      .btnSurveyWrp, html:not(.softByPic) .searchMemberList li:hover.usrList:nth-child(odd){
        background:".$header->fill->startColor." ;
      }



      ";
      }
  if(isset($json->searchHeader) && $json->searchHeader!=''){
        $searchHeader =  $json->searchHeader;
       $p .=" 
      /*.headerSearch .fa-search{color:".$searchHeader->iconColor." !important;  }*/
            .headerSearch input, .headerSearch input:focus{
                background: -webkit-linear-gradient(0deg, ".$searchHeader->fill->startColor.", ".$searchHeader->fill->stopColor.");
                    background: -o-linear-gradient(0deg, ".$searchHeader->fill->startColor.", ".$searchHeader->fill->stopColor.");
                    background: -moz-linear-gradient(0deg, ".$searchHeader->fill->startColor.", ".$searchHeader->fill->stopColor.");
                    background: linear-gradient(0deg, ".$searchHeader->fill->startColor.", ".$searchHeader->fill->stopColor.");
                    color:".$searchHeader->color." !important; 
            }
       ";
     }
     if(isset($json->createPostBtn) && $json->createPostBtn!=''){
        $createPostBtn =  $json->createPostBtn;
       $p .=" 
       #startdbHeaderBtn .createPostIcon{color:".$createPostBtn->iconColor." !important;  }
           #startdbHeaderBtn{
                background: -webkit-linear-gradient(0deg, ".$createPostBtn->fill->startColor.", ".$createPostBtn->fill->stopColor.");
                    background: -o-linear-gradient(0deg, ".$createPostBtn->fill->startColor.", ".$createPostBtn->fill->stopColor.");
                    background: -moz-linear-gradient(0deg, ".$createPostBtn->fill->startColor.", ".$createPostBtn->fill->stopColor.");
                    background: linear-gradient(0deg, ".$createPostBtn->fill->startColor.", ".$createPostBtn->fill->stopColor.");
                    color:".$createPostBtn->color." !important; 
                    text-shadow:none;
            }
       ";
     }
       if(isset($json->buttons) && $json->buttons!=''){
          $btns =  $json->buttons;
          $p .="
          .btn, #dbeePopupWrapper .PrivatePost a{
            background: -webkit-linear-gradient(0deg, ".$btns[0]->fill->startColor.", ".$btns[0]->fill->stopColor.");
            background: -o-linear-gradient(0deg, ".$btns[0]->fill->startColor.", ".$btns[0]->fill->stopColor.");
            background: -moz-linear-gradient(0deg, ".$btns[0]->fill->startColor.", ".$btns[0]->fill->stopColor.");
            background: linear-gradient(0deg, ".$btns[0]->fill->startColor.", ".$btns[0]->fill->stopColor.");
            border-color:".$btns[0]->borderColor.";
            border-width:".$btns[0]->borderWidth."px;
            border-radius:".$btns[0]->borderRadius."px;
            color:".$btns[0]->color.";
          }
          

          .btn-yellow{
            background: -webkit-linear-gradient(0deg, ".$btns[1]->fill->startColor.", ".$btns[1]->fill->stopColor.");
            background: -o-linear-gradient(0deg, ".$btns[1]->fill->startColor.", ".$btns[1]->fill->stopColor.");
            background: -moz-linear-gradient(0deg, ".$btns[1]->fill->startColor.", ".$btns[1]->fill->stopColor.");
            background: linear-gradient(0deg, ".$btns[1]->fill->startColor.", ".$btns[1]->fill->stopColor.");
            border-color:".$btns[1]->borderColor.";
            border-width:".$btns[1]->borderWidth."px;
            border-radius:".$btns[1]->borderRadius."px;
            color:".$btns[1]->color." !important;
          }
         

          .btn-black{
            background: -webkit-linear-gradient(0deg, ".$btns[2]->fill->startColor.", ".$btns[2]->fill->stopColor.");
            background: -o-linear-gradient(0deg, ".$btns[2]->fill->startColor.", ".$btns[2]->fill->stopColor.");
            background: -moz-linear-gradient(0deg, ".$btns[2]->fill->startColor.", ".$btns[2]->fill->stopColor.");
            background: linear-gradient(0deg, ".$btns[2]->fill->startColor.", ".$btns[2]->fill->stopColor.");
            border-color:".$btns[2]->borderColor.";
            border-width:".$btns[2]->borderWidth."px;
            border-radius:".$btns[2]->borderRadius."px;
            color:".$btns[2]->color." !important;
          }
          .PrivateDropDownCls{background: ".$btns[0]->fill->startColor.";
            border-color:".$btns[0]->borderColor.";
            border-width:".$btns[0]->borderWidth."px;
            color:".$btns[0]->color.";
          }
          #dbeePopupWrapper .PrivatePost .select2-arrow b:after{ color:".$btns[0]->color.";}
          .PrivateDropDownCls .select2-highlighted{background:".$btns[0]->color."; color:".$btns[0]->fill->startColor."}




          ";
       } 
      
      if(isset($json->rightSideTitle) && $json->rightSideTitle!=''){
         $rightSideTitle =  $json->rightSideTitle;
       $p .= " 

       #rightListing .whiteBox h2, #rightListing #sortable .whiteBox h2{
                background: -webkit-linear-gradient(0deg, ".$rightSideTitle->fill->startColor.", ".$rightSideTitle->fill->stopColor.");
                background: -o-linear-gradient(0deg, ".$rightSideTitle->fill->startColor.", ".$rightSideTitle->fill->stopColor.");
                background: -moz-linear-gradient(0deg, ".$rightSideTitle->fill->startColor.", ".$rightSideTitle->fill->stopColor.");
                background: linear-gradient(0deg, ".$rightSideTitle->fill->startColor.", ".$rightSideTitle->fill->stopColor.");
                color:".$rightSideTitle->color." ;
              }
               #rightListing #sortable .navAllLink a, #rightListing .whiteBox h2 span a { color:".$rightSideTitle->color." ;  }
              .searchByThis h2 input[type='text']{background:rgba(".$this->hex2rgb($rightSideTitle->color).", 0.2)}
              .scoringdisplay li:nth-child(odd){background:rgba(".$this->hex2rgb($rightSideTitle->fill->startColor).", 0.2);}
              .scoringdisplay li:nth-child(even){background:none}


        ";
    }
    if(isset($json->rightSideTitleActive) && $json->rightSideTitleActive!=''){
         $rightSideTitleActive =  $json->rightSideTitleActive;
       $p .= " 

      #rightListing .whiteBox.upcommingevent.active h2, #rightListing #sortable .whiteBox.active .rtListOver h2, #rightListing .whiteBox.active h2, #rightListing .whiteBox h2:hover, #leftsideMenu .whiteBox.active h2, #rightListing #sortable .whiteBox h2:hover, #rightListing #sharefiles:hover h2, #rightListing .searchByThis h2{
       
                background: -webkit-linear-gradient(0deg, ".$rightSideTitleActive->fill->startColor.", ".$rightSideTitleActive->fill->stopColor.");
                background: -o-linear-gradient(0deg, ".$rightSideTitleActive->fill->startColor.", ".$rightSideTitleActive->fill->stopColor.");
                background: -moz-linear-gradient(0deg, ".$rightSideTitleActive->fill->startColor.", ".$rightSideTitleActive->fill->stopColor.");
                background: linear-gradient(0deg, ".$rightSideTitleActive->fill->startColor.", ".$rightSideTitleActive->fill->stopColor.");
                color:".$rightSideTitleActive->color." 
              }

            #rightListing #sortable .whiteBox.active .navAllLink a, #rightListing .whiteBox.active .navAllLink a, #rightListing #sortable .whiteBox:hover .navAllLink a, #rightListing .whiteBox:hover .navAllLink a, #rightListing .mobSortlist .whiteBox h2 a, #rightListing .whiteBox.active h2 a { color:".$rightSideTitleActive->color.";}
             


        ";
    }
      if(isset($json->rightSideContent) && $json->rightSideContent!=''){
         $rightSideContent =  $json->rightSideContent;
       $p .=  "       

       #rightListing #sortable .rboxContainer a:hover{background:rgba(".$this->hex2rgb($rightSideContent->color).", 0.2)}
          .whiteBox:not(.customizeDeshboard):not(.biogrophydisplay), #messageSearchCont form, #rightListing #sortable .rboxContainer, #rightListing .rboxContainer, .scoringdisplay li:nth-child(odd), .scoringdisplay li:nth-child(even){
            background: -webkit-linear-gradient(0deg, ".$rightSideContent->fill->startColor.", ".$rightSideContent->fill->stopColor.");
            background: -o-linear-gradient(0deg, ".$rightSideContent->fill->startColor.", ".$rightSideContent->fill->stopColor.");
            background: -moz-linear-gradient(0deg, ".$rightSideContent->fill->startColor.", ".$rightSideContent->fill->stopColor.");
            background: linear-gradient(0deg, ".$rightSideContent->fill->startColor.", ".$rightSideContent->fill->stopColor.");
            color:".$rightSideContent->color." !important;            
          }
          
          .rbcRow a, .rbcRow .fa, .scoringdisplay li, #sliderInfo1 .userInfoDetail dt,#eventlocation .rbcRow{color:".$rightSideContent->color." !important; }
          .rbcRow{border-bottom-color:rgba(".$this->hex2rgb($rightSideContent->color).", 0.2) }";
      }

      if(isset($json->pageMiddle) && $json->pageMiddle!=''){
          $pageMiddle =  $json->pageMiddle;
         $p .= " #middleWrpBox, .postListing li, .dashboarprofleWrp, .whiteBox.biogrophydisplay, .whiteBox.customizeDeshboard, .profileStats, .scrollOnUseAll{
            background: -webkit-linear-gradient(0deg, ".$pageMiddle->fill->startColor.", ".$pageMiddle->fill->stopColor.");
            background: -o-linear-gradient(0deg, ".$pageMiddle->fill->startColor.", ".$pageMiddle->fill->stopColor.");
            background: -moz-linear-gradient(0deg, ".$pageMiddle->fill->startColor.", ".$pageMiddle->fill->stopColor.");
            background: linear-gradient(0deg, ".$pageMiddle->fill->startColor.", ".$pageMiddle->fill->stopColor.");
             color:".$pageMiddle->color." !important; 
        }     
       .userActionArrow ul li a, .userActionArrow .dropDownList li a{background:none;  background:".$pageMiddle->fill->stopColor."}
       .userActionArrow ul li a:hover, .userActionArrow .dropDownList li a:hover{ background:".$this->colourCreator($pageMiddle->fill->stopColor,-20).";}

         .profileStats .fa:not(.socialIcon),#searchUserAllMenu li a, .activeUserCount, .pstListTitle a,
        .userActionArrow span,.cmntScoreState span,.cmntuserLink {color:".$pageMiddle->color." !important;}

        .bx-gray, .profileStats .prstTitle a, .psUserName i, .tabLinks li a, .bioInfoList .bioInfoHd, .bioInfoList .bioListTxt .optionalText
        {color:rgba(".$this->hex2rgb($pageMiddle->color).", 0.5) !important;}

       .psUserName i a, .vipusertype {color:rgba(".$this->hex2rgb($pageMiddle->color).", 0.8) !important;}


        .myWhiteBg, .customizeDeshboard h2, .profileStatsWrp .progressBar{background:rgba(".$this->hex2rgb($pageMiddle->color).", 0.2);}
        .customizeDeshboard h2{color:".$pageMiddle->color."}


        .listingCommentLatest{background:rgba(".$this->hex2rgb($pageMiddle->color).", 0.05);}
        .tabLinks li a{background: rgba(".$this->hex2rgb($pageMiddle->fill->stopColor).", 0.7);}
        .tabLinks li a.active{background: ".$pageMiddle->fill->stopColor."; color:rgba(".$this->hex2rgb($pageMiddle->color).", 1) !important;}
        #profilepic-edit .fa-camera {color:".$pageMiddle->fill->stopColor."}";

       }
      
          echo $p;
    }

  }

   public function color_inverse($color){
            $color = str_replace('#', '', $color);
            if (strlen($color) != 6){ return '000000'; }
            $rgb = '';
            for ($x=0;$x<3;$x++){
                $c = 255 - hexdec(substr($color,(2*$x),2));
                $c = ($c < 0) ? 0 : dechex($c);
                $rgb .= (strlen($c) < 2) ? '0'.$c : $c;
            }
            return '#'.$rgb;
        }

  public function hex2rgb($hex) {
         $hex = str_replace("#", "", $hex);

         if(strlen($hex) == 3) {
            $r = hexdec(substr($hex,0,1).substr($hex,0,1));
            $g = hexdec(substr($hex,1,1).substr($hex,1,1));
            $b = hexdec(substr($hex,2,1).substr($hex,2,1));
         } else {
            $r = hexdec(substr($hex,0,2));
            $g = hexdec(substr($hex,2,2));
            $b = hexdec(substr($hex,4,2));
         }
         $rgb = array($r, $g, $b);
         return implode(",", $rgb); // returns the rgb values separated by commas
        // return $rgb; // returns an array with the rgb values
    }

    public  function colourCreator($colour, $per) {  
    $colour = substr( $colour, 1 ); // Removes first character of hex string (#) 
    $rgb = ''; // Empty variable 
    $per = $per/100*255; // Creates a percentage to work with. Change the middle figure to control colour temperature
     
    if  ($per < 0 ) // Check to see if the percentage is a negative number 
    { 
        // DARKER 
        $per =  abs($per); // Turns Neg Number to Pos Number 
        for ($x=0;$x<3;$x++) 
        { 
            $c = hexdec(substr($colour,(2*$x),2)) - $per; 
            $c = ($c < 0) ? 0 : dechex($c); 
            $rgb .= (strlen($c) < 2) ? '0'.$c : $c; 
        }   
    }  
    else 
    { 
        // LIGHTER         
        for ($x=0;$x<3;$x++) 
        {             
            $c = hexdec(substr($colour,(2*$x),2)) + $per; 
            $c = ($c > 255) ? 'ff' : dechex($c); 
            $rgb .= (strlen($c) < 2) ? '0'.$c : $c; 
        }    
    } 
    return '#'.$rgb; 
} 



}