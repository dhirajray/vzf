<?php
class EdfController extends IsController
{
	public function init()
    {
        $storage = new Zend_Auth_Storage_Session();
        $auth    = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $data          = $storage->read();
            $this->_userid = $data['UserID'];
            $this->twitter_connect_data = Zend_Json::decode($data['twitter_connect_data']);
        } 
    }
	public function indexAction()
	{	
		$request = $this->getRequest();  					
		$this->_helper->layout->disableLayout();
		
		$hash=false;
		$keyword =  explode(",",$request->getpost('keyword'));
		$count =  count($keyword); 
		$filter_alnum = new Zend_Filter_Alnum();
		$update = (int)$request->getpost('update');

		$db = (int)$request->getpost('db');

		$myhome_obj  = new Application_Model_Myhome();
		
		$return = ''; 

		for ($i=0; $i < $count; $i++) 
		{ 
			
			$q = trim($keyword[$i]);

			if(substr($q,0,1)=='#') 
			{
				$hash=true;
				$q=substr($q,1);
				$q1='#'.$q;
			}else{
				$q1=$q;
			}

			$q=str_replace(' ','',$q); 
			

			if($update=='1')
			{
				$myhome_obj->deletetwt($db,$q1);
				$data3 = array('TwitterTag'=>$request->getpost('keyword'));
				$myhome_obj->updatedbee($data3,$db);
			}

			$twitteroauth = new TwitterOAuth(twitterAppid, twitterSecret, 
            $this->twitter_connect_data['twitter_access_token'], 
            $this->twitter_connect_data['twitter_token_secret']);
            $rateLimit  = $twitteroauth->get('application/rate_limit_status', array('resources'=>'search'));
            $rateLimit = json_decode(json_encode($rateLimit), true);
            $data['rateLimit'] = $rateLimit;
           
            if($rateLimit['resources']['search']['/search/tweets']['remaining']==0)
            {
                $data['twitter']  = 'error';
                $data['time']  = time();
                $data['reset'] = $rateLimit['resources']['search']['/search/tweets']['reset'];
                $data['diff']  = $data['reset']-$data['time'];
                echo '5~#~'.ceil($data['diff']/60); exit;
            }
			$results  = $twitteroauth->get('search/tweets', array(
				'q' => $q,
				'result_type' => 'mixed',
				'count' => 4
			));	
			$cnt=0;
			$results = json_decode(json_encode($results), true);
			if(is_array($results['statuses'])) 
			{
				foreach( $results['statuses'] as $result) 
				{
					$screen_name=$results['statuses'][$cnt]['user']['screen_name'];
					$profile_image=$results['statuses'][$cnt]['user']['profile_image_url_https'];
					$tweet=$results['statuses'][$cnt]['text'];				
					$data = array('Keyword'=>$q1,'ScreenName'=>$screen_name,'ProfilePic' => $profile_image,'Tweet'=>$tweet,'DbeeID'=>$db,'UserID'=>$this->_userid,'LastUpdate'=>date('Y-m-d H:i:s'),'clientID'=>clientID);
					$success = $myhome_obj->insertiwitter($data);
					$cnt++;
				} 
				unset($results);
			}
		}

		if($success) echo $return='1~#~'.$db."~#~".$request->getpost('keyword');
		else echo '0';

	}    

	public function fetchtwitdatarightAction(){
		$request = $this->getRequest();
		$dbee = $request->getpost('db');
		$dbeetweet  = new Application_Model_Myhome();
		$this->view->twiterdata =  $dbeetweet->gettwitterNew($dbee);
		$response = $this->_helper->layout->disableLayout();
		return $response;
	}

}



