<?php
/* new api for sending sms
 doc : http://docs.cm.nl/http_MT.pdf
 Nitin Nayal - 16-04-13
*/
class Custom_Smsapi_Easysmsv2
{
		protected $gateway;
		protected $mode;
		protected $customerId;
		protected $login;
		protected $password;

		function __construct()
		{
			//load config
			$cnf = Zend_Registry::get("config");
			$this->gateway = $cnf->sms->http_gateway;
			$this->mode        = $cnf->sms->mode;
			$this->customerId = $cnf->sms->customer_id;
			$this->login = $cnf->sms->username;
			$this->password = $cnf->sms->password;
			$this->smsLength = 160;
			$this->chLength = 7;
		}
		
		public function sendsms($from, $to, $message, $smsCount, $reference = null, $mode = 'prod')
		{
			$this->mode = $mode; // mode should be prod/sandbox, default should be prod
			return $this->sms_mblox($from,$to,$message,$smsCount,$reference);
		}
	
	    public function sms_mblox($from,$to,$message,$smsCount,$reference = null)
	    {
	    	// For testing need not to make a request for sms
	        if ($this->mode == 'sandbox') {
	        	$resp = array('code'=>'0','reason'=>'sandbox accepted responce','ticket'=>uniqid(rand(99999,9999999)));
	            return $resp;
	        }

			$to = str_replace('+', '', $to);
		   	$rand_no = mt_rand(100000, 99999999);
		   	if ($smsCount == 1) { //not using UDP
			$xmlRequest = '<?xml version="1.0" encoding="UTF-8"?>
				<NotificationRequest Version="3.5">
					<NotificationHeader> 
						<PartnerName>'.$this->login.'</PartnerName> 
						<PartnerPassword>'.$this->password.'</PartnerPassword>
					</NotificationHeader>
					<NotificationList BatchID="'.$rand_no.'"> 
						<Notification SequenceNumber="1" MessageType="SMS" Format="UTF8"> 
							<Message><![CDATA['.$message.' ]]></Message> 
							<Profile>'.$this->customerId.'</Profile> 
							<SenderID Type="Alpha">'.$from.'</SenderID> 
							<Subscriber>
								<SubscriberNumber>'.$to.'</SubscriberNumber>
							</Subscriber>
						</Notification> 
					</NotificationList>
				</NotificationRequest>';
			} else {
				
				$xmlRequest = '<?xml version="1.0" encoding="UTF-8"?>
	 
				<NotificationRequest Version="3.5">
					<NotificationHeader> 
						<PartnerName>'.$this->login.'</PartnerName> 
						<PartnerPassword>'.$this->password.'</PartnerPassword>
					</NotificationHeader>
					<NotificationList BatchID="'.$rand_no.'">';
					 for ($i=1; $i<=$smsCount; $i++) {
						 if ($i==1) {
							 $intial_length = 0;
						 } else {
							$intial_length = ($i-1)*153; 
						 }
						if ($i<10) {
							$seq_no = '0'.$i;
							$sms_count =  '0'.$smsCount;
						} else {
							$seq_no = $i;
							$sms_count = $smsCount;
						}
						$msg = substr($message, $intial_length, 153);
						$xmlRequest .= '<Notification SequenceNumber="'.$seq_no.'" MessageType="SMS" Format="UTF8"> 
								<Message><![CDATA['.$msg.']]></Message> 
								<Profile>'.$this->customerId.'</Profile> 
								<Udh>:05:00:03:5F:'.$sms_count.':'.$seq_no.'</Udh>
								<SenderID Type="Alpha">'.$from.'</SenderID> 
								<Subscriber>
									<SubscriberNumber>'.$to.'</SubscriberNumber>
								</Subscriber>
							</Notification>';
					 }
					$xmlRequest .= '</NotificationList>
				</NotificationRequest>';
			}
			//setting the curl parameters.
			$headers = array(
			"Content-type: text/xml;",
			"Accept: text/xml",
			"Cache-Control: no-cache",
			"Pragma: no-cache",
			"SOAPAction: \"run\""
			);

	        try{

	            $ch = curl_init();
	            curl_setopt($ch, CURLOPT_URL, $this->gateway);
	            curl_setopt($ch, CURLOPT_POST, 1);

	            // send xml request to a server

	            curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
	            curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);

	            curl_setopt($ch, CURLOPT_POSTFIELDS,  $xmlRequest);
	            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	            curl_setopt($ch, CURLOPT_VERBOSE, 0);
	            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	            $data = curl_exec($ch);
	            //convert the XML result into array
	            if($data === false){
	                $error = curl_error($ch);
	                $resp = array('code'=>-1, 'reason'=>"no_connection", 'ticket'=>$rand_no);
	                return $resp;
	            }else{

	                $data = json_decode(json_encode(simplexml_load_string($data)), true);  
	            }
	            curl_close($ch);
	           
	        }catch(Exception  $e){
	        	$resp = array('code'=>-1, 'reason'=>"no_connection", 'ticket'=>$rand_no);
                return $resp;
			}
			$json_data = json_encode($data);
			if ($smsCount == 1) {
				if($data['NotificationResultList']['NotificationResult']['NotificationResultCode'] == 0){
					$SubscriberResult = $data['NotificationResultList']['NotificationResult']['SubscriberResult'];
				} else {
					$SubscriberResult = $data['NotificationResultList']['NotificationResult'];
				}
			} else {
				if($data['NotificationResultList']['NotificationResult'][0]['NotificationResultCode'] == 0) {
					$SubscriberResult = $data['NotificationResultList']['NotificationResult'][0]['SubscriberResult'];
				} else {
					$SubscriberResult = $data['NotificationResultList']['NotificationResult'][0];
				}
			}
			
		 	$codeArr = array(2,4,5,6,7,8,9,10);
			if ($SubscriberResult['SubscriberResultText'] == 'OK' || in_array($SubscriberResult['SubscriberResultCode'],$codeArr)) {
				$resp = array('code'=>$SubscriberResult['SubscriberResultCode'],'reason'=>$SubscriberResult['SubscriberResultText'],'ticket'=>$rand_no); 
			} else if ($SubscriberResult['SubscriberResultCode'] == 11 || $SubscriberResult['SubscriberResultCode'] == 100) {
				$resp = array('code'=>$SubscriberResult['SubscriberResultCode'],'reason'=>"no_connection",'ticket'=>$rand_no,'resulttext'=>$SubscriberResult['SubscriberResultText'] );
		    } else {
				$resp = array('code'=>$SubscriberResult['NotificationResultCode'],'reason'=>$SubscriberResult['NotificationResultText'],'ticket'=>$rand_no); 
				
				$subj = "mblox bug";
				$message = "return data by mblox ".$json_data." and ref id is ".$reference;
				$to = 'porwal.deshbandhu@gmail.com';
				$headers = 'From: <porwal.deshbandhu@gmail.com>' . "\r\n";
				mail($to, $subj, $message, $headers);
			}
			return $resp;
		}
		public function urlencode_array($array_args) { 
			if (! is_array($array_args) ) return false; 
			
			$pairs=array(); 
			foreach ($array_args as $k=>$v) { 
				$pairs[] = "$k=" . rawurlencode($v); 
			} 
			return implode($pairs, "&"); 
		} 
}


/**
* ErrorResponse holds all the Error response data
*
*/
class ErrorResponse {
	public $IsError;
	public $ErrorMessage;
	public $ErrorCode;

	public function __construct($IsError, $ErrorMessage, $ErrorCode) {
		$this->IsError = $IsError;
		$this->ErrorMessage = $ErrorMessage;
		$this->ErrorCode = $ErrorCode;
	}
}
