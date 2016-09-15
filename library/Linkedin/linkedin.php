<?php
require_once("twitter/OAuth.php");
class LinkedIn 
{
	public $base_url = "https://api.linkedin.com";
	public $secure_base_url = "https://api.linkedin.com";
	public $oauth_callback = "oob";
	public $consumer;
	public $request_token;
	public $access_token;
	public $oauth_verifier;
	public $signature_method;
	public $request_token_path;
	public $access_token_path;
	public $authorize_path;

	function __construct($consumer_key, $consumer_secret, $oauth_callback = NULL)
	{

		if($oauth_callback) {
			$this->oauth_callback = $oauth_callback;
		}

		$this->consumer = new OAuthConsumer($consumer_key, $consumer_secret, $this->oauth_callback);
		$this->signature_method = new OAuthSignatureMethod_HMAC_SHA1();
		$this->request_token_path = $this->secure_base_url . "/uas/oauth/requestToken";
		$this->access_token_path = $this->secure_base_url . "/uas/oauth/accessToken";
		$this->authorize_path = $this->secure_base_url . "/uas/oauth/authorize";
	}

	function getRequestToken()
	{
		$consumer = $this->consumer;
		$request = OAuthRequest::from_consumer_and_token($consumer, NULL, "GET", $this->request_token_path);
		$request->set_parameter("oauth_callback", $this->oauth_callback);
		$request->sign_request($this->signature_method, $consumer, NULL);
		$headers = Array();
		$url = $request->to_url();
		$response = $this->httpRequest($url, $headers, "GET");
		parse_str($response, $response_params);
		$this->request_token = new OAuthConsumer($response_params['oauth_token'], $response_params['oauth_token_secret'], 1);
	}

	function generateAuthorizeUrl()
	{
		$consumer = $this->consumer;
		$request_token = $this->request_token;
		return $this->authorize_path . "?oauth_token=" . $request_token->key;
	}

	function getAccessToken($oauth_verifier)
	{
		$request = OAuthRequest::from_consumer_and_token($this->consumer, $this->request_token, "GET", $this->access_token_path);
		$request->set_parameter("oauth_verifier", $oauth_verifier);
		$request->sign_request($this->signature_method, $this->consumer, $this->request_token);
		$headers = Array();
		$url = $request->to_url();
		$response = $this->httpRequest($url, $headers, "GET");
		parse_str($response, $response_params);
		$this->access_token = new OAuthConsumer($response_params['oauth_token'], $response_params['oauth_token_secret'], 1);
	}

	function getProfilefeed($resource = "~")
	{
		$profile_url = $this->secure_base_url . "/v1/people/" . $resource;
		$request = OAuthRequest::from_consumer_and_token($this->consumer, $this->access_token, "GET", $profile_url);
		$request->sign_request($this->signature_method, $this->consumer, $this->access_token);
		$auth_header = $request->to_header("https://api.linkedin.com"); # this is the realm
		$response = $this->httpRequest($profile_url, $auth_header, "GET");
		return $response;
	}

	function getProfile($resource = "~")
	{
		$profile_url = $this->secure_base_url . "/v1/people/" . $resource;
		$request = OAuthRequest::from_consumer_and_token($this->consumer, $this->access_token, "GET", $profile_url);
		$request->sign_request($this->signature_method, $this->consumer, $this->access_token);
		$auth_header = $request->to_header("https://api.linkedin.com"); # this is the realm
		$response = $this->httpRequest($profile_url, $auth_header, "GET");
		return $response;
	}

	function setStatus($status)
	{
		$profile_url = $this->secure_base_url . "/v1/people/~";
		$status_url = $this->secure_base_url . "/v1/people/~/current-status";
		echo "Setting status...\n";
		$xml = "<current-status>" . htmlspecialchars($status, ENT_NOQUOTES, "UTF-8") . "</current-status>";
		echo $xml . "\n";
		$request = OAuthRequest::from_consumer_and_token($this->consumer, $this->access_token, "PUT", $status_url);
		$request->sign_request($this->signature_method, $this->consumer, $this->access_token);
		$auth_header = $request->to_header("https://api.linkedin.com");

		$response = $this->httpRequest($profile_url, $auth_header, "GET");
		return $response;
	}

	# Parameters should be a query string starting with "?"
	# Example search("?count=10&start=10&company=LinkedIn");
	function search($parameters)
	{
		$search_url = $this->secure_base_url . "/v1/people-search:(people:(id,first-name,last-name,picture-url,site-standard-profile-request,headline),num-results)" . $parameters;
		//$search_url = $this->secure_base_url . "/v1/people-search?keywords=facebook";
		echo "Performing search for: " . $parameters . "<br />";
		echo "Search URL: $search_url <br />";
		$request = OAuthRequest::from_consumer_and_token($this->consumer, $this->access_token, "GET", $search_url);
		$request->sign_request($this->signature_method, $this->consumer, $this->access_token);
		$auth_header = $request->to_header("https://api.linkedin.com");
		$response = $this->httpRequest($search_url, $auth_header, "GET");
		return $response;
	}

	function connections($parameters)
	{
		$search_url = $this->secure_base_url . "/v1/people/~/connections:(id,first-name,last-name,picture-url;secure=true,site-standard-profile-request,headline)?modified=new" . $parameters;
		$request = OAuthRequest::from_consumer_and_token($this->consumer, $this->access_token, "GET", $search_url);
		$request->sign_request($this->signature_method, $this->consumer, $this->access_token);
		$auth_header = $request->to_header("https://api.linkedin.com");
		$response = $this->httpRequest($search_url, $auth_header, "GET");
		return $response;
	}
	

	function sendMessageById($id, $ccUser=FALSE, $subject='', $message='') {
      $messageUrl   =   $this->secure_base_url . "/v1/people/~/mailbox";
 
      $subject      =   htmlspecialchars($subject, ENT_NOQUOTES, "UTF-8") ;
      $message      =   htmlspecialchars($message, ENT_NOQUOTES, "UTF-8") ;
 
      if ($ccUser){
          $CCToUser   =   "<recipient>
                             <person path='/people/~'/>
                           </recipient>";
      }
      else{
          $CCToUser   =   '';
      }
 
      $xml = "<mailbox-item>
                <recipients>
                    $CCToUser
                    <recipient>
                        <person path='/people/$id' />
                    </recipient>
                </recipients>
                <subject>$subject</subject>
                <body>$message</body>
              </mailbox-item>";
 
      //echo $xml . "\n";
      $request = OAuthRequest::from_consumer_and_token($this->consumer, $this->access_token, "POST", $messageUrl);
      $request->sign_request($this->signature_method, $this->consumer, $this->access_token);
      $auth_header = $request->to_header("https://api.linkedin.com");
      if ($debug) {
          echo $auth_header . "\n";
      }
      $response = $this->httpRequest($messageUrl, $auth_header, "POST", $xml);
      return $response;
  }

  function postlike($resource = "~")
  {
		$likeURl = $this->secure_base_url . "/v1/posts/" . $resource;
		$xml = "<is-liked>true</is-liked>";
		$request = OAuthRequest::from_consumer_and_token($this->consumer, $this->access_token, "PUT", $likeURl);
		$request->sign_request($this->signature_method, $this->consumer, $this->access_token);
		$auth_header = $request->to_header("https://api.linkedin.com");
		if ($debug) {
			echo $xml . "\n";
			echo $auth_header . "\n";
		}
		$response = $this->httpRequest($likeURl, $auth_header, "PUT", $xml);
		return $response;
  }
  function postdislike($resource = "~")
  {
		$unlikeURl = $this->secure_base_url . "/v1/posts/" . $resource;
		$xml = "<is-liked>false</is-liked>";
		$request = OAuthRequest::from_consumer_and_token($this->consumer, $this->access_token, "PUT", $unlikeURl);
		$request->sign_request($this->signature_method, $this->consumer, $this->access_token);
		$auth_header = $request->to_header("https://api.linkedin.com");
		if ($debug) {
			echo $xml . "\n";
			echo $auth_header . "\n";
		}
		$response = $this->httpRequest($unlikeURl, $auth_header, "PUT", $xml);
		return $response;
  }

   function postComment($resource = "~",$comment)
  {
		$postComment = $this->secure_base_url . "/v1/posts/" . $resource;
		$xml = "<comment>
				    <text>".$comment."</text>
				</comment>";
		$request = OAuthRequest::from_consumer_and_token($this->consumer, $this->access_token, "POST", $postComment);
		$request->sign_request($this->signature_method, $this->consumer, $this->access_token);
		$auth_header = $request->to_header("https://api.linkedin.com");
		if ($debug) {
			echo $xml . "\n";
			echo $auth_header . "\n";
		}
		$response = $this->httpRequest($postComment, $auth_header, "POST", $xml);
		return $response;
  }
  function group($resource = "~")
  {
		$group_url = $this->secure_base_url . "/v1/people/" . $resource;
		$request = OAuthRequest::from_consumer_and_token($this->consumer, $this->access_token, "GET", $group_url);
		$request->sign_request($this->signature_method, $this->consumer, $this->access_token);
		$auth_header = $request->to_header("https://api.linkedin.com"); # this is the realm
		$response = $this->httpRequest($group_url, $auth_header, "GET");
		return $response;
  }

  function groupdetails($resource = "~")
  {
		$group_url = $this->secure_base_url . "/v1/groups/" . $resource;
		$request = OAuthRequest::from_consumer_and_token($this->consumer, $this->access_token, "GET", $group_url);
		$request->sign_request($this->signature_method, $this->consumer, $this->access_token);
		$auth_header = $request->to_header("https://api.linkedin.com"); # this is the realm
		$response = $this->httpRequest($group_url, $auth_header, "GET");
		return $response;
  }

  function groupsuggesred($resource = "~")
  {
		$group_url = $this->secure_base_url . "/v1/people/" . $resource;
		$request = OAuthRequest::from_consumer_and_token($this->consumer, $this->access_token, "GET", $group_url);
		$request->sign_request($this->signature_method, $this->consumer, $this->access_token);
		$auth_header = $request->to_header("https://api.linkedin.com"); # this is the realm
		$response = $this->httpRequest($group_url, $auth_header, "GET");
		return $response;
  }

  function getgrouppost($resource = "~")
  {
		$group_url = $this->secure_base_url . "/v1/groups/" . $resource;
		$request = OAuthRequest::from_consumer_and_token($this->consumer, $this->access_token, "GET", $group_url);
		$request->sign_request($this->signature_method, $this->consumer, $this->access_token);
		$auth_header = $request->to_header("https://api.linkedin.com"); # this is the realm
		$response = $this->httpRequest($group_url, $auth_header, "GET");
		return $response;
  }

  function joingroup($resource = "~", $groupid) {
      $joingroup     =   $this->secure_base_url . "/v1/people/".$resource;
      $xml = "<group-membership>
			    <group>
			        <id>".$groupid."</id>
			    </group>
			    <show-group-logo-in-profile>true</show-group-logo-in-profile>
			    <email-digest-frequency>
			        <code>daily</code>
			    </email-digest-frequency>
			    <email-announcements-from-managers>true</email-announcements-from-managers>
			    <allow-messages-from-members>true</allow-messages-from-members>
			    <email-for-every-new-post>false</email-for-every-new-post>
			    <membership-state>
			        <code>member</code>
			    </membership-state>
			</group-membership>";
 
      $request = OAuthRequest::from_consumer_and_token($this->consumer, $this->access_token, "POST", $joingroup);
      $request->sign_request($this->signature_method, $this->consumer, $this->access_token);
      $auth_header = $request->to_header("https://api.linkedin.com");
      if ($debug) {
          echo $xml . "\n";
          echo $auth_header . "\n";
      }
      $response = $this->httpRequest($joingroup, $auth_header, "POST", $xml);
      return $response;
  }

  function leaveGroup($resource = "~") 
  {
		$leavegroup     =   $this->secure_base_url . "/v1/people/".$resource;
		$request = OAuthRequest::from_consumer_and_token($this->consumer, $this->access_token, "DELETE", $leavegroup);
		$request->sign_request($this->signature_method, $this->consumer, $this->access_token);
		$auth_header = $request->to_header("https://api.linkedin.com");
		$response = $this->httpRequest($leavegroup, $auth_header, "DELETE");
		return $response;
  }

  function groupPost($resource = "~",$title, $summary) 
  {
      $groupPost     =   $this->secure_base_url . "/v1/groups/".$resource;
      $xml = "<post>
			    <title>".$title."</title>
			    <summary>".$summary."</summary>
			  </post>";
 
      $request = OAuthRequest::from_consumer_and_token($this->consumer, $this->access_token, "POST", $groupPost);
      $request->sign_request($this->signature_method, $this->consumer, $this->access_token);
      $auth_header = $request->to_header("https://api.linkedin.com");
      if ($debug) {
          echo $xml . "\n";
          echo $auth_header . "\n";
      }
      $response = $this->httpRequest($groupPost, $auth_header, "POST", $xml);
      return $response;
  }

  function share($comment, $title, $url, $imageUrl) {
      $shareUrl     =   $this->secure_base_url . "/v1/people/~/shares";
 
      $xml = "<share>
              <comment>$comment</comment>
              <content>
                 <title>$title</title>
                 <submitted-url>$url</submitted-url>
                 <submitted-image-url>$imageUrl</submitted-image-url>
              </content>
              <visibility>
                 <code>anyone</code>
              </visibility>
            </share>";
 
      $request = OAuthRequest::from_consumer_and_token($this->consumer, $this->access_token, "POST", $shareUrl);
      $request->sign_request($this->signature_method, $this->consumer, $this->access_token);
      $auth_header = $request->to_header("https://api.linkedin.com");
      if ($debug) {
          echo $xml . "\n";
          echo $auth_header . "\n";
      }
      $response = $this->httpRequest($shareUrl, $auth_header, "POST", $xml);
      return $response;
  }

	function httpRequest($url, $auth_header, $method, $body = NULL)
	{
		if (!$method) {
			$method = "GET";
		};

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array($auth_header)); // Set the headers.

		if ($body) {
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array($auth_header, "Content-Type: text/xml;charset=utf-8"));
		}

		$data = curl_exec($curl);
		curl_close($curl);
		return $data;
	}

}