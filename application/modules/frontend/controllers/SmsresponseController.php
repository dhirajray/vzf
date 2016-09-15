<?php
class SmsresponseController extends IsController
{  

	public function init()
	{

	}
	
	public function indexAction() 
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $rest_json = file_get_contents("php://input");
        $rest_json = rawurldecode(str_replace("+",' ',rtrim($rest_json,'"')));
        $rest_json =str_replace("XMLDATA=",'',$rest_json);
        $dataxml = json_decode(json_encode(simplexml_load_string($rest_json)), true);
        if(isset($dataxml['NotificationList']['Notification']['@attributes']['BatchID']))
        {
           $msg_id = $dataxml['NotificationList']['Notification']['@attributes']['BatchID'];
           $status = $dataxml['NotificationList']['Notification']['Subscriber']['Reason'];
           $smsno = $dataxml['NotificationList']['Notification']['Subscriber']['SubscriberNumber'];
        }
 
        $subj = "mblox response";
        $message = "return data by mblox msg_id =".$msg_id. '== status ='.$status.' === smsno= '.$smsno;
        $to = 'porwal.deshbandhu@gmail.com';
        $headers = 'From: <porwal.deshbandhu@gmail.com>' . "\r\n";
        mail($to, $subj, $message, $headers);
    }

    protected function generateLog($filename,$params_raw,$params_req,$er_chk)
    {
        if(file_exists($filename))
            $handle = fopen($filename, "a");
        else
            $handle = fopen($filename, "w");
        if(is_null($er_chk)){
            $str .= "\n\n================================================\n";
            $str = "[".date("Y-m-d h:i:s", mktime()) ."] \n\tRaw body - ".$params_raw."\n\tReponse - ".json_encode($params_req)."\n\t";
        }
        else{
            $str = "MetaData - ".json_encode($er_chk);
            $str .= "\n\n================================================\n";
        }
        fwrite($handle,$str);
        fclose($handle);
    } 
     public function callbackAction()
    {
        //$this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
 
        $sms_log_f = 'public/theme/version3/generated/sms_call.log';
        $params_raw = $this->getRequest()->getRawBody();
        $params_req = $this->getRequest()->getParams();
        $this->generateLog($sms_log_f,$params_raw,$params_req,null);
        $aff_fields = $er_check = array();
        $er_check['is post'] = $this->getRequest()->isPost();
        if($this->getRequest()->isPost()){   
            $smsResp = file_get_contents('php://input');
            $xmlobj = simplexml_load_string($smsResp);
             
            $insertData = 0;
            if(isset($xmlobj->MSG->REFERENCE) && !empty($xmlobj->MSG->REFERENCE)){
            $er_check['reference'] = (string) $xmlobj->MSG->REFERENCE;
            $decrypt = explode("|",$this->decrypt_sms((string) $xmlobj->MSG->REFERENCE));  // userid, messageId, serverId, Msg_no
            $er_check['decrypt'] = json_encode($decrypt);
            if((int) $decrypt[3] == 1)
            {     // check if first msg, skipping others for now
                $status = (int) $xmlobj->MSG->STATUS->CODE;
                $er_check['status'] = $status;
                //0=accepted by the operator, 1=rejected by CM or operator,2=delivered, 3=failed
                if($status == 2){
                    $insertData = 1;

                    $aff_fields = array(
                    'userid' => $decrypt[0],
                    'messageId' => $decrypt[1],
                    'serverId' => $decrypt[2],
                    'Msg_no' => $decrypt[3],
                    );
                    $er_check['aff_fields'] = $aff_fields;
                }
            }
            }

            if($insertData){
            $dbdetail = $this->getdbdetails($decrypt[2]);
            $er_check['dbdetail'] = json_encode($dbdetail);

            $date_rec = $this->setDate((string) $xmlobj->MSG->attributes()->RECEIVED);             
            $smtp_array = array(
            '$push' =>array('smtp_delivered'=> array(
            'mid' => (int) $decrypt[1],
            'smtp' => "cm.nfl",
            'date' => $date_rec
            )));

            // connect with userdb
            $db_repl = isset($dbdetail['db_repl']) ? ','.$dbdetail['db_repl'] : '';
            $connStr =  'mongodb://'.$dbdetail['db_host'].$db_repl.'/'.$dbdetail['db_name'];
            $aff_fields['db_name'] = $dbdetail['db_name'];
            $er_check['connStr'] = $connStr;

            $connection = new Shanty_Mongo_Connection($connStr);
            Shanty_Mongo::addMaster($connection,1,'users');

            $baseModel = new baseModel();
            $baseModel->_db($dbdetail['db_name']);
            Zend_Loader::loadClass('Users');
            $users = new Users();
            $users->updateUserdata(array('id'=>(int) $decrypt[0]),$smtp_array);
            $er_check['after update'] = 'success';

            }

            }
            $this->generateLog($sms_log_f,$params_raw,$params_req,$er_check);
            echo 'Welcome to SMS callback page';
    }
  
}