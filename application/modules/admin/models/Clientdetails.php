<?php



class Admin_Model_Clientdetails extends Zend_Db_Table_Abstract
{

    protected $_name = null;

    public function init()
    {
      $this->mail = new PHPMailer;
    }
    public function sendWithoutSmtpMail($to, $setSubject, $from ='no-reply@dbee.me', $setBodyText)
    {

        $from='"'.FromName.'" <'.NOREPLY_MAIL.'>'; //Give the Mail From Address Here
        if(SMTPSTATUS==0)
        { 
            $replyto = NOREPLY_MAIL;                                  
            $MailCharset = "iso-8859-1";
            $MailEncoding = "8bit";                         
            $headers  = "From: $from\n";
            $headers .= "Reply-To: $replyto\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=$MailCharset\n";
            $headers .= "Content-Transfer-Encoding: $MailEncoding\n";
            $headers .= "X-Mailer: PHP/".phpversion();
            if(!mail($to,$setSubject,$setBodyText,$headers, '-f'.NOREPLY_MAIL)) {
               return false;
            } else {
               return true;
            }
        }else
        {
            $this->swiftMail($to, $setSubject, $from, $setBodyText);
        }
    }
    public function swiftMail($to, $setSubject, $from ='no-reply@db-csp.com', $setBodyText)
    {
        if($to=='twitteruser@db-csp.com')
          return true;
        $from = 'no-reply@db-csp.com';
        require_once 'lib/swift_required.php';
        $transport = Swift_SmtpTransport::newInstance('smtp.mandrillapp.com', 587);
        $transport->setUsername('adam@db-csp.com');
        $transport->setPassword(smtpkey);
        
        // Create the message
        $message = Swift_Message::newInstance();
        $message->setTo(array($to));
        //$message->setCc(array("a.derosa@audero.it" => "Aurelio De Rosa"));
        /*$message->setBcc(array(
            "porwal.deshbandhu@gmail.com" => "Bank Boss"
        ));*/
 
        $message->setSubject($setSubject);
        $message->setBody($setBodyText, 'text/html');
        $message->setFrom($from, SITE_NAME);
        //$message->attach(Swift_Attachment::fromPath("path/to/file/file.zip"));
        
        // Send the email
        $mailer = Swift_Mailer::newInstance($transport);
        return $mailer->send($message, $failedRecipients);
    }
    public function dbSubstring($string,$break='100',$appended='...'){
        $newParag   =   substr($string, 0,$break);
        $expl       =   explode(" ", trim($newParag));
        $trimchars  =   strlen($expl[count($expl) - 1]);
        $dots       =   (strlen($string) > $break) ? $appended : '';

        return  ($dots=='') ? $string :substr($newParag,0,-($trimchars+1)).' '.$dots;
    }

     public function customEncoding($cryptokey,$usingCall=''){
     	if($cryptokey=='') return false;
     	
        if($usingCall=='')
        {
          $length       = rand(4,8);
          $randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
          $key          = strtolower($randomString);
          $mykey        = str_replace(" ",'adroambot',trim($cryptokey));
          $lastkey      = str_rot13(strtolower('mabirdnny'));
          $final        = ($mykey.$lastkey.'#');
        }
        else
        {
          $mykey    = str_replace(" ",'adroambot',trim($cryptokey));
          $final    = $mykey;
        }
      return str_rot13($final);  
    }
    public function customDecoding($cryptokey){
    	if($cryptokey=='') return false;
        $expl = explode("nqebnzobg", trim($cryptokey));
        $originalString = '';
        foreach ($expl as $key => $value) $originalString .= $value.' '; 
        $retuOrg  =  explode("mabirdnny", trim($originalString));
        return str_rot13($retuOrg[0]);
    }

    public function insertdata_global($table,$data){
      $insertdb = $this->_db->insert($table, $data);
      
        if($insertdb) return $this->_db->lastInsertId();
         else return false; 
    }
    // pass single where conditions in form of fields and id
    public function updatedata_global($table,$data,$uniqId,$id)
    {
      $where[] = $this->getAdapter()->quoteInto('clientID = ?', clientID);
      $where[] = $this->getAdapter()->quoteInto($uniqId .'= ?', $id);      
        if ($this->_db->update($table, $data ,$where))
            return true;
        else
            return false;
    }
    // pass multiple where conditions in form of Array
    public function updateMaster($table,$data,$whereData)
    {
       $where[] = $this->getAdapter()->quoteInto('clientID = ?', clientID);
       foreach ($whereData as $key => $value)
          $where[] = $this->getAdapter()->quoteInto($key .'= ?', $value);
        if ($this->_db->update($table, $data ,$where))
            return true;
        else
            return false;
    }

    // pass single where conditions in form of fields and id to delete
    public function deletedata_global($table,$wherefield,$whereid) 
    {
      $where[] = $this->getAdapter()->quoteInto('clientID = ?', clientID);
      $where[] = $this->getAdapter()->quoteInto($wherefield .'= ?', $whereid);
      $del =  $this->_db->delete($table,$where);
      return $del;
    }

    // pass multiple where conditions in form of Array to delete
    public function deleteMaster($table,$whereData) 
    {
      $where[] = $this->getAdapter()->quoteInto('clientID = ?', clientID);
       foreach ($whereData as $key => $value)
          $where[] = $this->getAdapter()->quoteInto($key .'= ?', $value);
      if ($this->_db->delete($table, $where))
            return true;
        else
            return false;
    }

    // Execute sql querys like pass - select * from tblname 
    public function passSQLquery($query)  
    {
      return $this->getDefaultAdapter()->query($query)->fetchAll();
      exit;
    }

    // Execute sql querys like pass - select * from tblname 
    public function passSQLqueryExecut($query)  
    {
      return $this->getDefaultAdapter()->query($query);
      exit;
    }


     public function getawvsomeicon($id='') 
    {
        $select = $this->_db->select();
        $select->from( array( 'tblFontIcons'));
        if($id!="")
        {
          $select->where("ID =?",$id);
        }
        return $this->_db->fetchAll($select);
    }
   
  
    // passing single where and order in form of ARRAY 
    // this will return all data using fetchAll 
    // this function is responsible for taking first execution will iniciate to BOOTING
    public function getfieldsfromtable($fields,$table,$wherefield='',$whereid='',$wherefield2='',$whereid2='',$orderbyfield='',$order='DESC',$limit='',$offset='0')
    {
        $query = $this->_db->select()->from($table,$fields);

        if($wherefield!='clientDomain' && $wherefield!='clientadmindomain')
        $query->where('clientID = ?', clientID);

        if($wherefield!='' && $whereid!='' ) 
          $query->where($wherefield.'= "'.$whereid.'"');  
            
        if($wherefield2!='' && $whereid2!='' ) 
          $query->where($wherefield2.'= "'.$whereid2.'"');  

        if($orderbyfield!=''  ) 
          $query->order("($orderbyfield) $order");  

        if($limit!='')
          $query->limit($limit,$offset);
        //echo $query.'<br>';
        return $this->_db->fetchAll($query);
    }
    // passing multiple where and order in form of ARRAY 
    // this will return all data using fetchAll 
    public function getAllMasterfromtable($tableName, $fieldsArr, $whereArr='', $orderbyArr='',$limit='',$offset='0')
    {
        $query = $this->_db->select()->from($tableName,$fieldsArr);

        // checking for specified Domain using client
        $query->where('clientID = ?', clientID); 
        
        // Taking all args as ARRAY where $key = database field name and $value = values need to be use
        if($whereArr!='')
        {
            foreach ($whereArr as $key => $value) 
              $query->where($key.' = ?', $value);
        }
        
        // Taking all args as ARRAY where $key = database field name and $value = values need to be use
        if($orderbyArr!='')
          foreach ($orderbyArr as $key => $value) $query->order("($key) $value");
        

        if($limit!='')
          $query->limit($limit,$offset);
       

        return $this->_db->fetchAll($query);
    }

    // passing multiple where and order in form of ARRAY 
    // this will return all rows using fetchROW
    public function getRowMasterfromtable($table,$fieldsArr,$whereArr='',$orderby='',$limit='',$offset='0')
    {
        $query = $this->_db->select()->from($table,$fieldsArr);
        if($whereArr!='')
        {
          foreach ($whereArr as $key => $value)
            $query->where($key.' = ?', $value);
        }
        $query->where('clientID = ?', clientID);
        if($orderby!='')
        {
          foreach ($orderby as $key => $value)
            $query->order("($key) $value");
        } 
        if($limit!='')
          $query->limit($limit,$offset);
        return $this->_db->fetchRow($query);
    }

    // this function is responsible for IN database query
    public function getfieldsfromtableusingin($fields,$table,$wherefield,$whereid)
    {
      $query = $this->_db->select()->from($table,$fields)->where($wherefield .' IN( '. $whereid  .')' ); 
      return $this->_db->fetchAll($query);
    }

    public function getfieldsfromgrouptable($fields,$table,$whereArr,$groupby='',$orderbyArr='')
    {

      //$this->_db->select();
      $select = $this->_db->select();
      $select->from(array($table),$fields);
      $select->where('clientID = ?', clientID);
      
      // Taking all args as ARRAY where $key = database field name and $value = values need to be use
      if($whereArr!='')
      {
          foreach ($whereArr as $key => $value) 
            $select->where($key.' = ?', $value);
      }
       if($groupby!='') $select->group("$table.$groupby");   
      // Taking all args as ARRAY where $key = database field name and $value = values need to be use
      if($orderbyArr!='')
        foreach ($orderbyArr as $key => $value) $select->order("($key) $value");           
     
      return $this->getDefaultAdapter()->query($select)->fetchAll(); 
    }
   

    public function getfieldsfromjointable($fields,$table,$whereArray,$fields2,$table2,$joinfromfield,$jointofield,$groupby='',$orderfield='',$limit='')
    {
      $select = $this->_db->select();
      //$select = $db->setIntegrityCheck( false );
      $select->from(array($table),$fields)
               ->join(array($table2), "$table.$joinfromfield=$table2.$jointofield",$fields2);
      $select->where("$table.clientID = ?", clientID);         
      if($whereArray!='') {      
          foreach ($whereArray as $key => $value)  $select->where($key.' = ?', $value);         
      }         
      if($groupby!='') $select->group("$table.$groupby");   
      if($orderfield!='') foreach ($orderfield as $key => $value) $select->order("($key) $value");
      if($limit!='') $select->LIMIT($limit);
      //echo '<br><br>'. $select; exit;
      return $this->getDefaultAdapter()->query($select)->fetchAll(); 
    }


    

    public function searchinmltarray($needle, $haystack, $strict = false) 
    {
       foreach ($haystack as $item) {
           if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && $this->searchinmltarray($needle, $item, $strict))) {
               return $item;
           }
       }

       return false;
    }


    public function ShowScoreIcon($IconId)
    {
        
        // $getIcon = $this->getfieldsfromtable(array('Class'),'tblFontIcons','clientDomain','ID',$IconId);


        $getIcon = $this->passSQLquery("select Class from tblFontIcons WHERE ID='".$IconId."'");
         return '<i class="fa '.$getIcon[0]['Class'].' fa-lg"></i>';
    }

    public static function escape($string, $strip_slashes = TRUE, $strip_tags = TRUE)
    {
        // safely handle arrays
        if (is_array($string)) {
            foreach ($string as $k => $v) {
                $string[$k] = Application_Model_Clientdetails::escape($v, $strip_slashes, $strip_tags);
            }
            return $string;
        }
 
        try {
            $string = mb_convert_encoding($string, 'UTF-8', mb_detect_encoding($string));
            if ($strip_tags === TRUE) {
                $string = strip_tags($string);
            }
            if ($strip_slashes === TRUE) {
                $string = stripslashes($string);
            }
            return htmlspecialchars($string, ENT_QUOTES, 'UTF-8', false);
        } catch (Exception $e) {
            return '';
        }
    }
  function timezoneevent($zone)
  {
    switch ($zone) 
    {
      case '-12.0':
        $zonetext = '(GMT -12:00) Eniwetok, Kwajalein';
        break;
      case '-11.0':
        $zonetext = '(GMT -11:00) Midway Island, Samoa';
      break;
      case '-10.0':
      $zonetext = '(GMT -10:00) Hawaii';
      break;
      case '-9.0':
      $zonetext = '(GMT -9:00) Alaska';
        break;
      case '-8.0':
      $zonetext = '(GMT -8:00) Pacific Time (US &amp; Canada)';
        break;
       case '-7.0':
      $zonetext = '(GMT -7:00) Mountain Time (US &amp; Canada)';
        break;
       case '-6.0':
      $zonetext = '(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima';
        break;
       case '-5.0':
      $zonetext = '(GMT -7:00) Mountain Time (US &amp; Canada)';
        break;
       case '-4.0':
      $zonetext = '(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz';
        break;
        case '-3.5':
      $zonetext = '(GMT -3:30) Newfoundland';
        break;
       case '-3.0':
      $zonetext = '(GMT -3:00) Brazil, Buenos Aires, Georgetown';
        break;
       case '-2.0':
      $zonetext = '(GMT -2:00) Mid-Atlantic';
        break;
      case '-1.0':
      $zonetext = '(GMT -1:00 hour) Azores, Cape Verde Islands';
        break;
     case '0.0':
      $zonetext = '(GMT) Western Europe Time, London, Lisbon, Casablanca';
        break;
     case '1.0':
        $zonetext = '(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris';
        break;
     case '2.0':
        $zonetext = '(GMT +2:00) Kaliningrad, South Africa';
      break;
      case '3.0':
      $zonetext = '(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg';
      break;
      case '3.5':
      $zonetext = '(GMT +3:30) Tehran';
        break;
      case '4.0':
      $zonetext = '(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi';
        break;
       case '4.5':
      $zonetext = '(GMT +4:30) Kabul';
        break;
       case '5.0':
      $zonetext = '(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent';
        break;
       case '5.5':
      $zonetext = '(GMT +5:30) Bombay, Calcutta, Madras, New Delhi';
        break;
       case '5.75':
      $zonetext = '(GMT +5:45) Kathmandu';
        break;
        case '6.0':
      $zonetext = '(GMT +6:00) Almaty, Dhaka, Colombo';
        break;
       case '7.0':
      $zonetext = '(GMT +7:00) Bangkok, Hanoi, Jakarta';
        break;
       case '8.0':
      $zonetext = '(GMT +8:00) Beijing, Perth, Singapore, Hong Kong';
        break;
      case '9.0':
      $zonetext = '(GMT +9:00) Tokyo, Seoul, Osaka';
        break;
     case '9.5':
      $zonetext = '(GMT +9:30) Adelaide, Darwin';
        break;
      case '10.0':
      $zonetext = '(GMT +10:00) Eastern Australia, Guam, Vladivostok';
        break;
       case '11.0':
      $zonetext = 'GMT +11:00) Magadan, Solomon Islands, New Caledonia';
        break;
       case '12.0':
      $zonetext = '(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka';
        break;
      default:
         $zonetext = '';
        break;
    }
    return $zonetext;
  }
}	