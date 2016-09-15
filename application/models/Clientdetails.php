<?php
class Application_Model_Clientdetails extends Application_Model_DbTable_Master
{

    protected $_name = null;

    function isRoleAdmin()
    {
        $storage   = new Zend_Auth_Storage_Session();
        $auth        =  Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
          $data     = $storage->read(); 
          $this->_userid = $data['UserID'];

          if($data['UserID']==adminID)
          {
            return 1;
          }
        } 
        
        return 0;
    }

    public function sessionWrite($result)
    {
      $auth= Zend_Auth::getInstance();
      if(is_array($result))
        $auth->getStorage()->write($result);
    }
        
    public function dbSubstring($string,$break='100',$appended='...')
    {
        if(strlen($string)>(int)$break)
        {
          $newParag   =   substr($string, 0,$break);
          $expl       =   explode(" ", trim($newParag));
          $trimchars  =   strlen($expl[count($expl) - 1]);
          $dots       =   (strlen($string) > $break) ? $appended : '';
          return  ($dots=='') ? $string :substr($newParag,0,-($trimchars+1)).' '.$dots;
        }else{
          return $string;
        }
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
          $mykey        = str_replace(" ",'adroambot',trim($cryptokey));
          $final    = $mykey;
        }
      return str_rot13($final);  
    }
    public function customDecoding($cryptokey)
    {
    	if($cryptokey=='') return false;
        $expl = explode("nqebnzobg", trim($cryptokey));
        $originalString = '';
        foreach ($expl as $key => $value) $originalString .= $value.' '; 
        $retuOrg  =  explode("mabirdnny", trim($originalString));
        return str_rot13($retuOrg[0]);
    }

    // created  for searching value in multi(2D) array, for reference search this function used in commonfunctionality
    public function searchvalue_MultiArray($id, $array,$searchfieldname='') {
       foreach ($array as $key => $val) {
           if ($val[$searchfieldname] === $id) {
               return $val[$searchfieldname];
           }
       }
       return null;
    }

    public function scoringTemplate($type,$jsonData,$class2="") {
      $scroeArray = $jsonData[$type];
      $retArray   = array();
      $xs = "ScoreIcon".($type);
      $xt = "ScoreName".($type);
      $select = $this->_db->select();
      $select->from( array( 'tblFontIcons'));
      $select->where("ID =?",$scroeArray[$xs]);      
      $getIcon=$this->_db->fetchRow($select);
      $retArray[] =  $scroeArray[$xt];
      $retArray[] =  '<i class="fa '.$getIcon['Class'].' '.$class2.'"></i>';
      return ($retArray);
    }

    public function ShowScoreIcon($IconId,$size='')
    {
        
        // $getIcon = $this->getfieldsfromtable(array('Class'),'tblFontIcons','clientDomain','ID',$IconId);
        $getIcon = $this->getAllMasterfromtable('tblFontIcons',array('Class'),array('ID'=>$IconId),'','','','noclientID');
         return '<i class="fa '.$getIcon[0]['Class'].' '.$size.'"></i>';
    }
 
    public function ShowScoreClass($IconId)
    {
      
        
         $getIcon = $this->getAllMasterfromtable('tblFontIcons',array('Class'),array('ID'=>$IconId),'','','','noclientID');
         return 'fa '.$getIcon[0]['Class'];
    }

    public function insertdata_global($table,$data){
      $insertdb = $this->_db->insert($table, $data);
        if($insertdb) return $this->_db->lastInsertId();
         else return false; 
    }
 
    public function updatedata_global($table,$data,$uniqId,$id)
    {
      $where[] = $this->getAdapter()->quoteInto('clientID = ?', clientID);
      $where[] = $this->getAdapter()->quoteInto($uniqId .'= ?', $id);      
        if ($this->_db->update($table, $data ,$where))
            return true;
        else
            return false;
    }
  
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


    public function deletedata_global($table,$wherefield,$whereid) 
    {
      $where[] = $this->getAdapter()->quoteInto('clientID = ?', clientID);
      $where[] = $this->getAdapter()->quoteInto($wherefield .'= ?', $whereid);
      $del =  $this->_db->delete($table,$where);
      return $del;
    }

 
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

    
    public function passSQLquery($query)  
    {
      return $this->getDefaultAdapter()->query($query)->fetchAll();
      exit;
    }

  
   
  
    
    public function getfieldsfromtable($fields,$table,$wherefield='',$whereid='',$wherefield2='',$whereid2='',$orderbyfield='',$order='DESC',$limit='',$offset='0')
    {
        $query = $this->_db->select()->from($table,$fields);

        if($wherefield!='clientDomain')
        $query->where('clientID = ?', clientID);

        if($wherefield!='' && $whereid!='' ) 
          $query->where($wherefield.'= "'.$whereid.'"');  
            
        if($wherefield2!='' && $whereid2!='' ) 
          $query->where($wherefield2.'= "'.$whereid2.'"');  

        if($orderbyfield!=''  ) 
          $query->order("($orderbyfield) $order");  

        if($limit!='')
        {
          $query->limit($limit,$offset);
        }
       
        $rs = $this->_db->fetchAll($query);
        return $rs;
    }

   
    // passing multiple where and order in form of ARRAY 
    // this will return all data using fetchAll 
    public function getAllMasterfromtable($tableName, $fieldsArr, $whereArr, $orderbyArr='',$limit='',$offset='0',$notClientID='')
    {
        $query = $this->_db->select()->from($tableName,$fieldsArr);

        // checking for specified Domain using client
        if($notClientID=='')
        $query->where('clientID = ?', clientID);
        
        // Taking all args as ARRAY where $key = database field name and $value = values need to be use
        if($whereArr!='')
        {
            foreach ($whereArr as $key => $value) $query->where($key.' = ?', $value);
        } 
        // Taking all args as ARRAY where $key = database field name and $value = values need to be use
        if($orderbyArr!='')
        {
          foreach ($orderbyArr as $key => $value) $query->order("($key) $value");
        } 

        if($limit!='')
          $query->limit($limit,$offset);
        //echo '<br><br>'. $query;
        return $this->_db->fetchAll($query);
    }

    // passing multiple where and order in form of ARRAY 
    // this will return all rows using fetchROW
    public function getRowMasterfromtable($table,$data,$where,$orderby='',$limit='',$offset='0')
    {
        $query = $this->_db->select()->from($table,$data);
        if(!empty($where)){
          foreach ($where as $key => $value)
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

    public function getRowHavingMasterfromtable($table,$data,$where,$orderby='',$usertype='',$limit='',$offset='0',$isconfirmed='')
    {
        $query = $this->_db->select()->from($table,$data);
        foreach ($where as $key => $value)
          $query->having($key.' = ?', $value);

        $query->where('clientID = ?', clientID);
        if($isconfirmed==0)
        {
          $query->where('Status = ?', 1);
        }

        if($usertype==0)
         {
          $query->where('usertype = ?', 0); 
         }
        if($orderby!='')
        {
          foreach ($orderby as $key => $value)
            $query->order("($key) $value");
        } 
        

        return $this->_db->fetchRow($query);
    }

    // this function is responsible for IN database query
    public function getfieldsfromtableusingin($fields,$table,$wherefield,$whereid)
    {
      $query = $this->_db->select()->from($table,$fields)->where($wherefield .' IN( '. $whereid  .')' ); 
      $rs = $this->_db->fetchAll($query);
      return $rs;
    }

    public function getfieldsfromgrouptable($fields,$table,$wherefield,$whereid,$groupby='',$orderfield='',$wherefield2='',$whereid2='')
    {

      //$this->_db->select();
      $select = $this->_db->select();
      $select->from(array($table),$fields)->where($wherefield.'= "'.$whereid.'"');
      $select->where('clientID = ?', clientID);
      if($wherefield2!='') $select->where($wherefield2.'= "'.$whereid2.'"');              
      if($groupby!='') $select->group("$table.$groupby");   
      if($orderfield!='') foreach ($orderfield as $key => $value) $select->order("($key) $value");

     // echo '<br><br>'. $select; exit;
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
   public function getfilename($kdid)
    {
    	$query = $this->_db->select()->from('tblknowledge')->where('kc_id= ?',$kdid);
    	$rs = $this->_db->fetchRow($query);
    	return $rs;
    }

    public function getcatwithoutMisclist() 
    {

        $select = $this->_db->select()
        ->from(array('c' => 'tblDbees'))
        ->join(array('cat' => 'tblDbeeCats'), 'c.Cats =cat.CatID',  array('cat.CatName',"cat.CatID"))
        ->where("cat.CatName  != ?",'Miscellaneous')->where("c.Cats != ?",'')->where("c.clientID = ?", clientID);
        //echo $select->__toString(); exit; 
        return $this->_db->fetchAll($select); 
    }

  public function delactivity($id,$uid)
    {
        $this->_db->delete('tblactivity', array(
            "act_typeId='" . $id . "'","act_ownerid='" . $uid . "'"
        ));
       return true; 
       
    }

     public function deluserpdf($id,$uid)
    {
        $this->_db->delete('tblUserPdf', array(
            "fileid='" . $id . "'","userid='" . $uid . "'"
        ));
       return true;
       
    }

    
    public function statupdatepdf($data, $fileid,$userid)
    {
        $this->_db->update('tblUserPdf', $data, array( "userid='" . $userid . "'","fileid='" . $fileid . "'" ));

        return true;
    }
 
}	