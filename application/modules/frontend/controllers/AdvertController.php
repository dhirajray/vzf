<?php
class AdvertController extends IsController
{    
    public function init()
    {
        parent::init();
        $this->dbeeid = (int) $this->_getParam('id');
    }
    
    public function fetchAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $content = '';
        if ($this->getRequest()->isXmlHttpRequest()) 
        {
            if ((int)$_COOKIE['advertID'] != 0) 
            {
                $full == false;
                $advertID = (int)$_COOKIE['advertID'];
                $adshide = $_COOKIE['adshide'];
                $headerFlag = 0;
                $slidershow = 0;
                $getAdvert = $this->Advert->getAdvertisement($advertID);
                $headerslide = '<div class="flideslide"><ul class="slides">';
                $header = '';
                $right = '';
                if(!empty($getAdvert))
                {
                    foreach ($getAdvert as $value) 
                    {
                        if($value['position']=='header')
                        {
                            $headerFlag = 1;
                            if($value['layout']=='full')
                            {
                                $full = true;
                                if($value['link']!='')
                                    $header .= '<li><a target="_blank" href="'.$value['link'].'"><img src="/ads/' . $value['image'] . '" class="headerBannerfull" data-adid="'. $value['advertID'].'" data-bannerid="'. $value['bannerid'].'"  /></a></li>';
                                else
                                    $header .= '<li><img src="'.BASE_URL.'/ads/' . $value['image'] . '"  data-adid="'. $value['advertID'].'" data-bannerid="'. $value['bannerid'].'"  /></li>';
                            }
                            else
                            {
                                if($value['link']!='')
                                    $header .= '<li><a target="_blank" href="'.$value['link'].'"><img src="/ads/' . $value['image'] . '" class="headerBanner headerBannerfull" data-adid="'. $value['advertID'].'" data-bannerid="'. $value['bannerid'].'" /></a></li>';
                                else
                                    $header .= '<li><img src="'.BASE_URL.'/ads/' . $value['image'] . '" class="headerBanner" data-adid="'. $value['advertID'].'" data-bannerid="'. $value['bannerid'].'" /></li>';
                            }
                            $data['headerbannereffect'] = $value['effect'];
                            $data['headerbannerspeed'] = $value['speed'];
                            $data['headerposition'] = $value['layout'];
                        }

                        if($value['position']=='right')
                        {
                            $rightFlag = 1;
                            $rightslide = '<div class="rightbannerAdvertisement" data-adid="'. $value['advertID'].'" data-bannerid="'. $value['bannerid'].'"><ul class="slides">';
                            if($value['slidershow']==1){
                                $slidershow = 1;
                                if($value['link']!='')
                                    $right .='<li><a target="_blank" href="'.$value['link'].'"><img src="/ads/' . $value['image'] . '" class="headerBannerfull" data-adid="'. $value['advertID'].'" data-bannerid="'. $value['bannerid'].'" /></a></li>';
                                else
                                    $right .= '<li><img src="'.BASE_URL.'/ads/' . $value['image'] . '"  data-adid="'. $value['advertID'].'"  data-bannerid="'. $value['bannerid'].'" /></li>';
                            }
                            else
                            {
                                if($value['link']!='')
                                    $right .='<li><a target="_blank" href="'.$value['link'].'">

                                    <img src="/ads/' . $value['image'] . '" class="headerBannerfull" data-adid="'. $value['advertID'].'" data-bannerid="'. $value['bannerid'].'"/></a></li>';
                                else
                                    $right .= '<li><img src="'.BASE_URL.'/ads/' . $value['image'] . '"  data-adid="'. $value['advertID'].'" data-bannerid="'. $value['bannerid'].'"/></li>';
                            }
                            $data['rightbannereffect'] = $value['effect'];
                            $data['rightbannerspeed'] = $value['speed'];
                            $data['slidershow'] = $value['slidershow'];
                        }
                    }
                    if($slidershow==1 && $rightFlag ==1){
                        $rightslide .= $right;
                        $rightslide .= '</ul></div>';
                        $data['rightbanner'] = $rightslide;
                    }
                    else if($rightFlag ==1){
                        $rightslide .= $right;
                        $data['rightbanner'] = $rightslide;
                    }else
                        $data['rightbanner'] = '';

                    if($headerFlag ==1){
                        $headerslide .= $header;
                        $headerslide .= '</ul></div>';
                        $data['headerbanner'] = $headerslide;
                    }else
                        $data['headerbanner'] = '';
                }else{
                        $data['rightbanner'] = '';
                        $data['headerbanner'] = '';
                }
            }
            else
            {
                $headerFlag = 0;
                $slidershow = 0;
                $adshide = $_COOKIE['adshide'];
                $full == false;
                $getAdvert = $this->Advert->getGlobalAdvertisement();
                //print_r($getAdvert);
                $headerslide = '<div class="flideslide"><ul class="slides">';
                $header = '';
                $right = '';
                if(!empty($getAdvert))
                {
                    foreach ($getAdvert as $value) 
                    {
                        if($value['position']=='header')
                        {
                            $headerFlag = 1;
                            if($value['layout']=='full'){
                                $full = true;
                                if($value['link']!='')
                                    $header .= '<li><a target="_blank" href="'.$value['link'].'"><img src="/ads/' . $value['image'] . '" class="headerBannerfull" data-adid="'. $value['advertID'].'" data-bannerid="'. $value['bannerid'].'" /></a></li>';
                                else
                                    $header .= '<li><img src="'.BASE_URL.'/ads/' . $value['image'] . '"  data-adid="'. $value['advertID'].'" data-bannerid="'. $value['bannerid'].'" /></li>';
                            }
                            else
                            {
                                if($value['link']!='')
                                    $header .= '<li><a target="_blank" href="'.$value['link'].'"><img src="/ads/' . $value['image'] . '" class="headerBannerfull" data-adid="'. $value['advertID'].'" data-bannerid="'. $value['bannerid'].'"/></a></li>';
                                else
                                    $header .= '<li><img src="'.BASE_URL.'/ads/' . $value['image'] . '"  data-adid="'. $value['advertID'].'" data-bannerid="'. $value['bannerid'].'"/></li>';
                            }
                            $data['headerbannereffect'] = $value['effect'];
                            $data['headerbannerspeed'] = $value['speed'];
                            $data['headerposition'] = $value['layout'];
                        }

                        if($value['position']=='right')
                        {
                            $rightFlag = 1;
                            $rightslide = '<div class="rightbannerAdvertisement" data-adid="' . $value['advertID'] . '" data-bannerid="'. $value['bannerid'].'" slidetype="'.$value['slidershow'].'"><ul class="slides">';
                            if($value['slidershow']==1){
                                $slidershow = 1;
                                if($value['link']!='')
                                    $right .='<li><a target="_blank" href="'.$value['link'].'"><img src="/ads/' . $value['image'] . '" class="headerBannerfull" data-adid="'. $value['advertID'].'" data-bannerid="'. $value['bannerid'].'" /></a></li>';
                                else
                                    $right .= '<li><img src="'.BASE_URL.'/ads/' . $value['image'] . '"  data-adid="'. $value['advertID'].'"  data-bannerid="'. $value['bannerid'].'"/></li>';
                            }
                            else
                            {
                                if($value['link']!='')
                                    $right .='<li><a target="_blank" href="'.$value['link'].'"><img src="/ads/' . $value['image'] . '" class="headerBannerfull" data-adid="'. $value['advertID'].'"  data-bannerid="'. $value['bannerid'].'"/></a></li>';
                                else
                                    $right .= '<li><img src="'.BASE_URL.'/ads/' . $value['image'] . '"  data-adid="'. $value['advertID'].'"  data-bannerid="'. $value['bannerid'].'"/></li>';
                            }
                            $data['rightbannereffect'] = $value['effect'];
                            $data['rightbannerspeed'] = $value['speed'];
                            $data['slidershow'] = $value['slidershow'];
                        }
                    }
                    if($slidershow==1 && $rightFlag ==1){
                        $rightslide .= $right;
                        $rightslide .= '</ul></div>';
                        $data['rightbanner'] = $rightslide;
                    }
                    else if($rightFlag ==1){
                        $rightslide .= $right;
                        $data['rightbanner'] = $rightslide;
                    }else
                        $data['rightbanner'] = '';
                    
                    if($headerFlag ==1){
                        $headerslide .= $header;
                        $headerslide .= '</ul></div>';
                        $data['headerbanner'] = $headerslide;
                    }else
                        $data['headerbanner'] = '';
                }else{
                        $data['rightbanner'] = '';
                        $data['headerbanner'] = '';
                }
            }
            if($adshide=='yes' && $full==true){
                $data['headerbanner'] = '';
            }

            $logoAttr = getimagesize(BASE_URL.'/img/'.$this->configuration->SiteLogo);
           
            if($logoAttr['width']>230) $LogoImage =  '<img src="'.BASE_URL.'/timthumb.php?src=/img/'.$this->configuration->SiteLogo.'&q=100&w=230" />';
            else  $LogoImage = '<img src="'.BASE_URL.'/img/'.$this->configuration->SiteLogo.'" />';

            $data['logo'] = '<a href="'.BASE_URL.'/myhome" class="takeusername" username="'.$this->myclientdetails->customDecoding($this->session_data['Name']).'" profilepicforappend="'.$this->session_data['ProfilePic'].'" >'.$LogoImage.'</a>';
        }else {
            $data['status']  = 'error';
            $data['message'] = 'some thing was wrong';
        }

        return $response->setBody(Zend_Json::encode($data));
    }


    public function trackadvertAction()
    {
       
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $content = '';
        if ($this->getRequest()->isXmlHttpRequest()) 
        {
             $advertobj= new Application_Model_Advert();

             $adid = $this->_request->getPost('adid');
             $position = $this->_request->getPost('position');
             $bannerid = $this->_request->getPost('bannerid');

             $ipAddress = $_SERVER['REMOTE_ADDR'];
             $UserID=$this->session_data['UserID'];
             $geoplugin = new geoPlugin();
             $geoplugin->locate($this->_request->getServer('REMOTE_ADDR'));

                        



            $data2 = array('clientID'=>clientID,'UserId'=>$UserID,'AdvertId'=>$adid,'position'=>$position,'BannerId'=>$bannerid);
            $data2['ip']     = $this->_request->getServer('REMOTE_ADDR');
            $data2['browser']         = $this->commonmodel_obj->getbrowser();
           
            $data2['City']            = $this->myclientdetails->customEncoding($geoplugin->city);
            $data2['region']          = $this->myclientdetails->customEncoding($geoplugin->region);
            $data2['area_code']       = $this->myclientdetails->customEncoding($geoplugin->areaCode);
           
            $data2['country_code']    = $this->myclientdetails->customEncoding($geoplugin->countryCode);
            $data2['country_name']    = $this->myclientdetails->customEncoding($geoplugin->countryName);
                       
            $data2['longitude']       = $this->myclientdetails->customEncoding($geoplugin->longitude);
            $data2['latitude']        = $this->myclientdetails->customEncoding($geoplugin->latitude);
                       


             $advertobj->InsertClickAdvertTrackRecord($data2,$adid,$UserID,$bannerid);

        }else 
        {
            $data['status']  = 'error';
            $data['message'] = 'some thing was wrong';
        }

        return $response->setBody(Zend_Json::encode($data));
        
    }
    public function fetchrelationAction()
    {
        $data = array();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $content = '';
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST') 
        {
            $headerFlag = 0;
            $slidershow = 0;
            $relationID = $this->_request->getPost('relationID');
            $type = $this->_request->getPost('type');

            $getAdvert = $this->Advert->getSpecificAdvertisement($relationID,$type);

            if($type==2 && empty($getAdvert))
                $getAdvert = $this->Advert->getSpecificAdvertisement($relationID,6);

            if($type==4 && empty($getAdvert) && $relationID == $this->_userid)
            {
                $listGroup = $this->Advert->getRelationAdvert(3);
                foreach ($listGroup as $value) 
                {
                    if($this->Advert->checkUserInUsergroup($value['relationID'],$this->_userid))
                        $getAdvert = $this->Advert->getSpecificAdvertisement($value['relationID'],3);
                    if(!empty($getAdvert))
                    break;
                 }
            }else if($type==4 && !empty($getAdvert) && $relationID != $this->_userid)
            {
                $getAdvert = array();
            }


            $headerslide = '<div class="flideslide"><ul class="slides">';
           
            $header = '';
            $right = '';

            if(!empty($getAdvert))
            {
                foreach ($getAdvert as $value) 
                {
                    if($value['position']=='header')
                    {
                        $headerFlag = 1;
                        if($value['layout']=='full'){
                            if($value['link']!='')
                                $header .= '<li><a target="_blank" href="'.$value['link'].'"><img src="/ads/' . $value['image'] . '" class="headerBannerfull" data-adid="'. $value['advertID'].'" data-bannerid="'. $value['bannerid'].'"  /></a></li>';
                            else
                                $header .= '<li><img src="'.BASE_URL.'/ads/' . $value['image'] . '"  data-adid="'. $value['advertID'].'" data-bannerid="'. $value['bannerid'].'"  /></li>';
                        }
                        else
                        {
                            if($value['link']!='')
                                $header .= '<li><a target="_blank" href="'.$value['link'].'"><img src="/ads/' . $value['image'] . '" class="headerBannerfull" data-adid="'. $value['advertID'].'" data-bannerid="'. $value['bannerid'].'"  /></a></li>';
                            else
                                $header .= '<li><img src="'.BASE_URL.'/ads/' . $value['image'] . '"  data-adid="'. $value['advertID'].'" data-bannerid="'. $value['bannerid'].'"  /></li>';
                        }
                        $data['headerbannereffect'] = $value['effect'];
                        $data['headerbannerspeed'] = $value['speed'];
                        $data['headerposition'] = $value['layout'];
                    }

                    if($value['position']=='right')
                    {
                        $rightslide = '<div class="rightbannerAdvertisement" data-adid="' . $value['advertID'] . '" data-bannerid="'. $value['bannerid'].'" slidetype="'.$value['slidershow'].'"><ul class="slides">';
                        $rightFlag = 1;
                        if($value['slidershow']==1){
                            $slidershow = 1;
                            if($value['link']!='')
                                $right .='<li><a  target="_blank" href="'.$value['link'].'"><img src="/ads/' . $value['image'] . '" class="headerBannerfull" data-adid="'. $value['advertID'].'" data-bannerid="'. $value['bannerid'].'"  /></a></li>';
                            else
                                $right .= '<li><img src="'.BASE_URL.'/ads/' . $value['image'] . '"  data-adid="'. $value['advertID'].'" data-bannerid="'. $value['bannerid'].'"  /></li>';
                        }
                        else
                        {
                            if($value['link']!='')
                                $right .='<li><a target="_blank" href="'.$value['link'].'"><img src="/ads/' . $value['image'] . '" class="headerBannerfull"  data-adid="'. $value['advertID'].'"  data-bannerid="'. $value['bannerid'].'"/></a></li>';
                            else
                                $right .= '<li><img src="'.BASE_URL.'/ads/' . $value['image'] . '" data-adid="'. $value['advertID'].'"   data-bannerid="'. $value['bannerid'].'"/></li>';
                        }
                        $data['rightbannereffect'] = $value['effect'];
                        $data['rightbannerspeed'] = $value['speed'];
                        $data['slidershow'] = $value['slidershow'];
                    }
                }
                if($slidershow==1 && $rightFlag ==1){
                    $rightslide .= $right;
                    $rightslide .= '</ul></div>';
                    $data['rightbanner'] = $rightslide;
                }
                else if($rightFlag ==1){
                    $rightslide .= $right;
                    $data['rightbanner'] = $rightslide;
                }else
                    $data['rightbanner'] = '';
                
                if($headerFlag ==1){
                    $headerslide .= $header;
                    $headerslide .= '</ul></div>';
                    $data['headerbanner'] = $headerslide;
                }else
                    $data['headerbanner'] = '';
            }else
            {
                $headerFlag = 0;
                $slidershow = 0;
                $getAdvert = $this->Advert->getGlobalAdvertisement();
                $headerslide = '<div class="flideslide"><ul class="slides">';
              
                $header = '';
                $right = '';
                if(!empty($getAdvert))
                {
                    foreach ($getAdvert as $value) 
                    {


                        if($value['position']=='header')
                        {
                            $headerFlag = 1;
                            if($value['layout']=='full'){
                                if($value['link']!='')
                                    $header .= '<li><a  target="_blank" href="'.$value['link'].'"><img src="/ads/' . $value['image'] . '" class="headerBannerfull" data-adid="'. $value['advertID'].'" data-bannerid="'. $value['bannerid'].'"  /></a></li>';
                                else
                                    $header .= '<li><img src="'.BASE_URL.'/ads/' . $value['image'] . '" data-adid="'. $value['advertID'].'" data-bannerid="'. $value['bannerid'].'" /></li>';
                            }
                            else
                            {
                                if($value['link']!='')
                                    $header .= '<li><a target="_blank" href="'.$value['link'].'"><img src="/ads/' . $value['image'] . '" class="headerBanner"  class="headerBannerfull" data-adid="'. $value['advertID'].'" data-bannerid="'. $value['bannerid'].'"/></a></li>';
                                else
                                    $header .= '<li><img src="'.BASE_URL.'/ads/' . $value['image'] . '"  data-adid="'. $value['advertID'].'" data-bannerid="'. $value['bannerid'].'" /></li>';
                            }
                            $data['headerbannereffect'] = $value['effect'];
                            $data['headerbannerspeed'] = $value['speed'];
                            $data['headerposition'] = $value['layout'];
                        }

                        if($value['position']=='right')
                        {
                            $rightslide = '<div class="rightbannerAdvertisement" data-adid="' . $value['advertID'] . '" data-bannerid="'. $value['bannerid'].'" slidetype="'.$value['slidershow'].'"><ul class="slides">';
                            $rightFlag = 1;
                            if($value['slidershow']==1){
                                $slidershow = 1;
                                if($value['link']!='')
                                    $right .='<li><a target="_blank" href="'.$value['link'].'"><img src="/ads/' . $value['image'] . '" class="headerBannerfull" data-adid="'. $value['advertID'].'" data-bannerid="'. $value['bannerid'].'"  /></a></li>';
                                else
                                    $right .= '<li><img src="'.BASE_URL.'/ads/' . $value['image'] . '" data-adid="'. $value['advertID'].'" data-bannerid="'. $value['bannerid'].'" / > </li>';
                            }
                            else
                            {
                                if($value['link']!='')
                                    $right .='<li><a target="_blank" href="'.$value['link'].'"><img src="/ads/' . $value['image'] . '" class="headerBannerfull" data-adid="'. $value['advertID'].'" data-bannerid="'. $value['bannerid'].'" /></a></li>';
                                else
                                    $right .= '<li><img src="'.BASE_URL.'/ads/' . $value['image'] . '"  data-adid="'. $value['advertID'].'" data-bannerid="'. $value['bannerid'].'"/></li>';
                            }
                            $data['rightbannereffect'] = $value['effect'];
                            $data['rightbannerspeed'] = $value['speed'];
                            $data['slidershow'] = $value['slidershow'];
                        }
                    }
                    if($slidershow==1 && $rightFlag ==1){
                        $rightslide .= $right;
                        $rightslide .= '</ul></div>';
                        $data['rightbanner'] = $rightslide;
                    }
                    else if($rightFlag ==1){
                        $rightslide .= $right;
                        $data['rightbanner'] = $rightslide;
                    }else
                        $data['rightbanner'] = '';
                    
                    if($headerFlag ==1){
                        $headerslide .= $header;
                        $headerslide .= '</ul></div>';
                        $data['headerbanner'] = $headerslide;
                    }else
                        $data['headerbanner'] = '';
                }else
                {
                    $data['rightbanner'] = '';
                    $data['headerbanner'] = '';
                }
            }

            $logoAttr = getimagesize(BASE_URL.'/img/'.$this->configuration->SiteLogo);

            if($logoAttr['width']>230) $LogoImage =  '<img src="'.BASE_URL.'/timthumb.php?src=/img/'.$this->configuration->SiteLogo.'&q=100&w=230" />';
            else  $LogoImage = '<img src="'.BASE_URL.'/img/'.$this->configuration->SiteLogo.'" />';

            $data['logo'] = '<a href="'.BASE_URL.'/myhome" class="takeusername" username="'.$this->myclientdetails->customDecoding($this->session_data['Name']).'" profilepicforappend="'.$this->session_data['ProfilePic'].'" >'.$LogoImage.'</a>';
        }else {
            $data['status']  = 'error';
            $data['message'] = 'some thing was wrong';
        }

        return $response->setBody(Zend_Json::encode($data));
    }
    
}
