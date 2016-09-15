<?php
class PlateformcssController extends IsController
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
       if($this->configuration!='')
       {
        $json = $this->configuration;
      $hightLightColor =  $json->highlightsColorPlatform;
      $fontFamily =  $json->fontFamily;
      $hColorFPl =  $json->highForegroundPlatform;
      $p='';
      if($fontFamily!=''){
        $p.='
         body{font-family:'.$fontFamily.';}';
      }
      
      $p.='
     
      .seeMoreFulltext, .seeMoreFulltextgrp,
       .dbRelbox.active .fa-lightbulb-o, 
       .comment-list.active .comntBulb, 
       .dbeePopContent .itemGuideMark,
       .srcEmailId,
       .postListing li.listingTypeMedia.active span.influence a .fa, 
       .eventVenue i , 
       #dbRssWrapper #dbRssDesc a, .dbRssWrapper a,
       .dbRssWrapperFeed .fa , body .cmntScoreState span:hover , .cmntScoreState span.active,
       .listingTypeMedia.pendingClass .pendingTxt,
       .dbDetailsCntFooter .scroreDivOnTop ul li a:hover,
       .dbtphdrLinks a:hover,
       .cmntScoreState span.influence a:hover,
       .ftrCmntRight a.active
        {
          color:'. $hightLightColor.' !important;
        }
      .comment-list .comntRelBox .comntInflTxt, 
      .youtubeWarning, 
      .dbeePopContent .itemGuideMark .rssdeactiveMark,
       #sideBarFeeds .totalCountWrp, 
      .sclIconsSide.rssVisitor.active,
      .questionAnsWrp li input[type="radio"]:checked + label,
      .questionAnsWrp li label:hover,
      .switchOn:before,
      #searchUserAllMenu .active,
      #CompanySortAlphbet li a.active,
      .loadpreviousChat,
      .heightLightChat .chatTittleBar, 
      .heightLightChat .userPicTitle, 
      .allUserChat .chatContainer li.heightLightChat, 
      .allUserChat .chatContainer li:hover.heightLightChat, 
      #MemberSortAlphbet li a.active,
      .backToUserList.heightLightChat,
      .dbRelbox.active .inflSpan,
      .askExpertBtn, .allQaBtn, .saveModiBtn,
      #HangoutButton, .enterWrp .btn,
      .pstBriefFt a:hover,
      .expertQAArea .pstBriefFt,
      #dbee-visited-wrapper ul.tabLinks li span.CountQA,
      #mySideMenu .menuLinksTablink ul > li:hover, #mySideMenu .menuLinksTablink li.active
      {
           background: '. $hightLightColor.';
      }
      .heightLightChat{    outline: 1px solid '. $hightLightColor.';}
      .allUserChat .chatContainer li.heightLightChat, 
      .allUserChat .chatContainer li:hover.heightLightChat{border-bottom-color:'. $hightLightColor.';}
      .comment-list .comntRelBox .comntInflTxt:before,
      .dbRelbox.active .inflSpan:before{
            border-top-color:'. $hightLightColor.';
      }
      
      .dbRelbox.active,
      .comment-list.active,
      .postListing li.listingTypeMedia.active,
      .listingTypeMedia.pendingClass
        {
            box-shadow: 0 0 0px 1px '. $hightLightColor.';
        }
      

     .switchOn:before,
      .videoOverlay,
      .videoOverlay a,
      .dbRelbox.active .inflSpan,
      .askExpertBtn, .allQaBtn, .saveModiBtn,
      #HangoutButton #countdown div,
      #MemberSortAlphbet li a.active,
#searchUserAllMenu .active , .enterWrp .btn,
.pstBriefFt a:hover ,.expertQAArea .pstBriefFt, #CompanySortAlphbet li a.active, .goToAdmin,
#mySideMenu .menuLinksTablink ul > li:hover a, #mySideMenu .menuLinksTablink li.active a
      {
        color:'.$hColorFPl.';
      }

      ';

      
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