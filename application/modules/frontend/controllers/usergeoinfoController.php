<?php

class usergeoinfoController extends IsController
{

    public function indexAction()
	{
	exit;
		$request = $this->getRequest()->getParams();
		$usersallinfo = new Application_Model_DbUser();
		$infoofuser = $usersallinfo->userallval();
		foreach($infoofuser as $key=>$value){
		       echo'<pre>';print_r($value);
		        $UserID = $value['UserID'];
		        $geoplugin1 = new geoPlugin();
				
				$geoplugin1->locate($value['LastLoginIP']);
				
				print_r($geoplugin1);
				exit;
				
				$users1['browser']        = $this->getbrowser();
				$users1['os']             = $this->getos();
				$users1['city'] 			 = $geoplugin->city;
				$users1['region'] 		 = $geoplugin->region;	
				$users1['area_code'] 	 = $geoplugin->areaCode;
				$users1['dma'] 			 = $geoplugin->dmaCode;
				$users1['country_code'] 	 = $geoplugin->countryCode;
				$users1['country_name'] 	 = $geoplugin->countryName;
				$users1['continent_name'] = $this->getcontinent($geoplugin->continentCode);
				$users1['longitude'] 	 = $geoplugin->longitude;
				$users1['latitude'] 		 = $geoplugin->latitude;
				$users1['currency_code']  = $geoplugin->currencyCode;
				$users1['currency_symbol'] = $geoplugin->currencySymbol;
				
				$users1['reg_city'] 			 = $geoplugin->city;
				$users1['reg_region'] 		 = $geoplugin->region;	
				$users1['reg_area_code'] 	 = $geoplugin->areaCode;
				$users1['reg_dma'] 			 = $geoplugin->dmaCode;
				$users1['reg_country_code'] 	 = $geoplugin->countryCode;
				$users1['reg_country_name'] 	 = $geoplugin->countryName;
				$users1['reg_continent_name'] = $this->getcontinent($geoplugin->continentCode);
				$users1['reg_longitude'] 	 = $geoplugin->longitude;
				$users1['reg_latitude'] 		 = $geoplugin->latitude;
				$users1['reg_currency_code']  = $geoplugin->currencyCode;
				$users1['reg_currency_symbol'] = $geoplugin->currencySymbol;				
				$addStatus1	= $usersallinfo->updateusergeoinfo($users1,$UserID);
				/**********reg***************/
		/**********login***************/
		/*$geoplugin->locate($value['LastLoginIP']);
		$users2['LastLoginIP']    = $value['LastLoginIP'];
		$users2['browser']        = $this->getbrowser();
		$users2['os']             = $this->getos();
		$users2['city'] 			 = $geoplugin->city;
		$users2['region'] 		 = $geoplugin->region;	
		$users2['area_code'] 	 = $geoplugin->areaCode;
		$users2['dma'] 			 = $geoplugin->dmaCode;
		$users2['country_code'] 	 = $geoplugin->countryCode;
		$users2['country_name'] 	 = $geoplugin->countryName;
		$users2['continent_name'] = $this->getcontinent($geoplugin->continentCode);
		$users2['longitude'] 	 = $geoplugin->longitude;
		$users2['latitude'] 		 = $geoplugin->latitude;
		$users2['currency_code']  = $geoplugin->currencyCode;
		$users2['currency_symbol'] = $geoplugin->currencySymbol;				
		$addStatus	= $usersallinfo->updateusergeoinfo($users2,$UserID);*/
		
		/**********login***************/
	    }
	}



	/*private function _checkAndUpdateUser($fbUser,$facebook_imagename)
    {
			if(!$fbUser)
			{ return 1;}
            $fbEmail = $fbUser['email'];
            $gender = $fbUser['gender'];
           // Check for user existence
			$userstuff		= new Application_Model_DbUser();
			$status_userid = $userstuff->chkAval_user($fbEmail);
			$status = count($status_userid);
			if($status)
			{   
				$UserID = $status_userid[0]['UserID'];
			    $geoplugin = new geoPlugin(); 
				$geoplugin->locate($_SERVER['REMOTE_ADDR']);
				$users['LastLoginIP'] = $_SERVER['REMOTE_ADDR'];
				$users['browser'] = $this->getbrowser();
				$users['os'] = $this->getos();
				$users['city'] 			 = $geoplugin->city;
				$users['region'] 		 = $geoplugin->region;	
				$users['area_code'] 	 = $geoplugin->areaCode;
				$users['dma'] 			 = $geoplugin->dmaCode;
				$users['country_code'] 	 = $geoplugin->countryCode;
				$users['country_name'] 	 = $geoplugin->countryName;
				$users['continent_name'] = $this->getcontinent($geoplugin->continentCode);
				$users['longitude'] 	 = $geoplugin->longitude;
				$users['latitude'] 		 = $geoplugin->latitude;
				$users['currency_code']  = $geoplugin->currencyCode;
				$users['currency_symbol'] = $geoplugin->currencySymbol;				
				$addStatus	= $userstuff->updateusergeoinfo($users,$UserID);
				return 2;
			}
			else
			{
			    $passval = ucfirst($fbUser['first_name']);
				$hashpass = $this->_generateHash($passval);// secure password generated on _generateHash() function
				$users['Name'] = ucfirst($fbUser['first_name'].' '.$fbUser['last_name']);
				$users['Username'] = ucfirst($fbUser['first_name']);
                $users['Fbname'] = $passval;
				$users['Pass'] = $hashpass ;
				$users['Email'] = $fbUser['email'];
				$users['Gender'] = $fbUser['gender'];
				$users['Birthdate']= date("Y-m-d",strtotime($fbUser['birthday']));
				$users['ProfilePic'] = $facebook_imagename[4];
				$users['RegistrationDate'] = date('Y-m-d H:i:s');
				$users['Status'] ='1';
				
				$geoplugin = new geoPlugin(); 
				$geoplugin->locate($_SERVER['REMOTE_ADDR']);
				$users['IP'] = $_SERVER['REMOTE_ADDR'];
				$users['browser'] = $this->getbrowser();
				$users['os'] = $this->getos();
				$users['reg_city'] 			 = $geoplugin->city;
				$users['reg_region'] 		 = $geoplugin->region;	
				$users['reg_area_code'] 	 = $geoplugin->areaCode;
				$users['reg_dma'] 			 = $geoplugin->dmaCode;
				$users['reg_country_code'] 	 = $geoplugin->countryCode;
				$users['reg_country_name'] 	 = $geoplugin->countryName;
				$users['reg_continent_name'] = $this->getcontinent($geoplugin->continentCode);
				$users['reg_longitude'] 	 = $geoplugin->longitude;
				$users['reg_latitude'] 		 = $geoplugin->latitude;
				$users['reg_currency_code']  = $geoplugin->currencyCode;
				$users['reg_currency_symbol'] = $geoplugin->currencySymbol;				
				$addStatus	= $userstuff->adduser($users);
				
            }
	}
	*/

	
	public  function getcontinent($agent)
	{
		if ( stripos($agent, 'AS') !== false ) {
			$agent = 'Asia';
		} elseif ( stripos($agent, 'AF') !== false ) {
			$agent = 'Africa';
		} elseif ( stripos($agent, 'AN') !== false ) {
			$agent = 'Antarctica';
		} elseif ( stripos($agent, 'EU') !== false ) {
			$agent = 'Europe';
		} elseif ( stripos($agent, 'NA') !== false ) {
			$agent = 'North america';
		} elseif ( stripos($agent, 'OC') !== false ) {
			$agent = 'Australia';
		} elseif ( stripos($agent, 'SA') !== false ) {
			$agent = 'South america';
		} 
		return $agent;
	}

	public  function getbrowser()
	{
		if ( empty($agent) ) {
			$agent = $_SERVER['HTTP_USER_AGENT'];
			if ( stripos($agent, 'Firefox') !== false ) {
				$agent = 'Firefox';
			} elseif ( stripos($agent, 'MSIE') !== false ) {
				$agent = 'IE';
			} elseif ( stripos($agent, 'iPad') !== false ) {
				$agent = 'Ipad';
			} elseif ( stripos($agent, 'Android') !== false ) {
				$agent = 'Android';
			} elseif ( stripos($agent, 'Chrome') !== false ) {
				$agent = 'Chrome';
			} elseif ( stripos($agent, 'Safari') !== false ) {
				$agent = 'Safari';
			} elseif ( stripos($agent, 'AIR') !== false ) {
				$agent = 'Air';
			} elseif ( stripos($agent, 'Fluid') !== false ) {
				$agent = 'Fluid';
			}
	
		}
		return $agent;
	}
	
	public  function getos()
	{
		if ( empty($agent) ) {
			$agent = $_SERVER['HTTP_USER_AGENT'];
			$osArray = array(
					'iPhone' => '(iPhone)',
					'Windows 98' => '(Win98)|(Windows 98)',
					'Windows 2000' => '(Windows 2000)|(Windows NT 5.0)',
					'Windows 2003' => '(Windows NT 5.2)',
					'Windows ME' => 'Windows ME',
					'Windows XP' => '(Windows XP)|(Windows NT 5.1)',
					'Windows Vista' => 'Windows NT 6.0',
					'Windows 7' => '(Windows NT 6.1)|(Windows NT 7.0)',
					'Windows NT 4.0' => '(WinNT)|(Windows NT 4.0)|(WinNT4.0)|(Windows NT)',
					'Linux' => '(X11)|(Linux)',
					'Safari' => '(Safari)',
					'Open BSD'=>'OpenBSD',
					'Sun OS'=>'SunOS',
					'Mac OS' => '(Mac_PowerPC)|(Macintosh)|(Mac OS)',
					'QNX'=>'QNX',
					'BeOS'=>'BeOS',
					'OS/2'=>'OS/2',
					'Search Bot'=>'(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp/cat)|(msnbot)|(ia_archiver)'
			); 
			foreach ($osArray as $k => $v) 
			{
				if (preg_match("/$v/", $agent)) {
					 break;
				}	else {
				$k = "Unknown OS";
				}
			} 
		}
		return $k;
	}
	
 
	  

	 



}





