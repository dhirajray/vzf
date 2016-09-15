<?php

class Admin_Model_User extends Zend_Db_Table_Abstract
{
     // Table name 
     protected $_name = 'tblUsers';
   protected $_dbTable;
      
  public function setDbTable($dbTable)
    {
    if (is_string($dbTable))
      $dbTable = new $dbTable();
        if (!$dbTable instanceof Zend_Db_Table_Abstract)
            throw new Exception('Invalid table data gateway provided');
        $this->_dbTable = $dbTable;   
        return $this;
    } 
    public function getDbTable()
    {
    if (null === $this->_dbTable) 
            $this->setDbTable('Admin_Model_DbTable_User');
        return $this->_dbTable;
    }
    public function updateInuser($data,$userlist)
    {
        $db = $this->getDbTable();
        $where = array(
            'UserID IN (?)' => $userlist,
            'clientID = ?' => clientID
        );
        $this->getDbTable()->update($data, $where);
    }
    public function getUser($username) 
    {
      $select= $this->select()->where('Status = ?', 1)
      ->where("Username = ?", $username)->where("clientID = ?", clientID);
      return $this->fetchAll($select);
    }

    public function getUserName($userID) 
    {
        $db = $this->getDbTable();
        $select = $db->select()->from('tblUsers',array('Name'));
        $select->where('clientID = ?', clientID);
        $select->where('UserID = ?', $userID);        
        $result= $db->fetchRow($select);
        return $result['Name'];     
    }

    public function getRols() 
    {
        $select = $this->_db->select()->from('roles')->where("clientID = ?", clientID);                            
        return $result = $this->_db->fetchAll($select);     
    }
   
    public function getRolsdatails($roleidno) 
    {
        $select = $this->_db->select()->from('roles')->where("role_id = ?", $roleidno)->where("clientID = ?", clientID);                            
        return $result = $this->_db->fetchAll($select);     
    }

    public function getPermissions($roleid,$resmangid) 
    {
        $select = $this->_db->select()->from('permissions')->where("id_role = ?",$roleid)->where("id_resource = ?",$resmangid)->where("clientID = ?", clientID);                            
        return $result = $this->_db->fetchAll($select);     
    }

    public function getselRols() 
    {
        $roleidno = array(1001,1,2);
        $select = $this->_db->select()->from('roles')->where("role_id NOT IN(?)", $roleidno)->where("clientID = ?", clientID)->order('role_id DESC');                            
        return $result = $this->_db->fetchAll($select);     
    }

    public function getselRolswithoutAdmin() 
    {
        $roleidno = array(1001,1,2);
        $select = $this->_db->select()->from('roles')->where("role_id NOT IN(?)", $roleidno)->where("clientID = ?", clientID);                            
        return $result = $this->_db->fetchAll($select);     
    }

    public function getselResource() 
    {
        $select = $this->_db->select()->from('tblResourcesManage')->where("status = ?",1)->where("parent_id = ?",0)->where("clientID = ?", clientID);                            
        return $result = $this->_db->fetchAll($select);     
    }

    public function getResourcecat($roleid,$resourceid,$parentid)
    {    
          $select = $this->_db->select()          
           ->from(array('A'=>'tblResourcesManage'),array('A.id as resmangid','A.clientID as resmangclientID','A.resource as resmangresource','A.parent_id as resmangparent_id'))->where("A.status = ?",1)->where("A.parent_id = ?",$resourceid)->where("A.clientID = ?", clientID);        
        //echo $select->__toString();die;     
        return  $this->_db->fetchAll($select);    
    }

    public function getuserwithRols($role) 
    {
        //echo $rol;die; ->where("c.act_typeId NOT IN(?)", $gethiddenDbs)
        $select = $this->_db->select()->from('tblUsers')->where('role = ?', $role)->where("clientID = ?", clientID);                            
        return $result = $this->_db->fetchAll($select);     
    }

    public function chkRolesExist($role) 
    {
        $select = $this->_db->select()->from('roles')->where('role = ?', $role)->where("clientID = ?", clientID);                            
        //echo $select->__toString();die;
        $result = $this->_db->fetchAll($select);
        return count($result);       
    }

    public function Rolesforacl($role) 
    {
        //echo $role;//die('----');
        if($role){
          $select = $this->_db->select()->from('roles')->where('role_id = ?', $role)->where("clientID = ?", clientID);
        }else{
          $select = $this->_db->select()->from('roles')->where("clientID = ?", clientID);
        }                                   
        //echo $select->__toString();//die;
        return $result = $this->_db->fetchAll($select);              
    }

    public function insertRoles($role){ 
       $select = $this->_db->select()->from("roles", array(new Zend_Db_Expr("MAX(role_id) AS maxrolesID")));
       $fetchlastroleid=$this->_db->fetchOne($select);
       $data=array('role'=>$role,'role_id'=>$fetchlastroleid+1,'clientID'=>clientID);
       return $this->_db->insert('roles', $data);  
    }

    public function deletroleuserall($role){
          //echo $role;die('-----');
          $where[] = $this->getAdapter()->quoteInto('clientID'.'= ?', clientID);
          $where[] = $this->getAdapter()->quoteInto('role_id'.'= ?', $role);
          $deletepreVal = $this->_db->delete('roles',$where);
          return 'deleterole';
 
    }

    public function delUserRoletable($role){
          //echo $role;die('-----');
          $where[] = $this->getAdapter()->quoteInto('clientID'.'= ?', clientID);
          $where[] = $this->getAdapter()->quoteInto('id_role'.'= ?', $role);
          $deletepreVal = $this->_db->delete('permissions',$where);
    }


    public function updateinsertSubresource($id_role,$id_resource){
      if(empty($id_resource)){
          $where[] = $this->getAdapter()->quoteInto('clientID'.'= ?', clientID);
          $where[] = $this->getAdapter()->quoteInto('id_role'.'= ?', $id_role);
          $deletepreVal = $this->_db->delete('permissions',$where);
        return 'You do not select any permissions this role.';
      }else{

      $datadel0 = array();
      $datadel2 = array();
      foreach($id_resource as $idresource){
          $masVal = explode("~asr~",$idresource);
          $datadel0[]=$masVal[0];
          $datadel1[]=$masVal[1];
      }
      $subresourceval0 = array_unique($datadel0);
      $subresourceval1 = array_unique($datadel1);
      /**********which not avaliable in subresource *******/
       $selsubresource = $this->_db->select()->from('permissions')->where('id_role = ?', $id_role)->where("clientID = ?", clientID);
       //echo $select->__toString();die;
       $selsubresval = $this->_db->fetchAll($selsubresource);
       foreach($selsubresval as $myvalue){
         $subresval[]=$myvalue['subresource'];
       }
       if(count($subresval)){
       $subresunival = array_unique($subresval);
       $arr3 = array_diff($subresunival, $subresourceval1);
       }
       if(count($arr3)>0){
       foreach($arr3 as $arr3val){
          $where[] = $this->getAdapter()->quoteInto('clientID'.'= ?',clientID);
          $where[] = $this->getAdapter()->quoteInto('id_role'.'= ?',$id_role);
          $where[] = $this->getAdapter()->quoteInto('subresource'.'= ?',$arr3val);
          $where[] = $this->getAdapter()->quoteInto('permission' .'= ?','allow');
          $deletepreVal = $this->_db->delete('permissions',$where);
          unset($where);
       }}
      /*****************/
      //echo'<pre>';print_r($subresourceval1);die('empty');
      if(count($subresourceval1)){
      foreach($subresourceval1 as $subresourcelval){
          $where[] = $this->getAdapter()->quoteInto('clientID'.'= ?', clientID);
          $where[] = $this->getAdapter()->quoteInto('id_role'.'= ?', $id_role);
          $where[] = $this->getAdapter()->quoteInto('subresource'.'= ?', $subresourcelval);
          $where[] = $this->getAdapter()->quoteInto('permission'.'= ?','allow');
          $deletepreVal = $this->_db->delete('permissions',$where);
          unset($where);
       }} 
        foreach($id_resource as $idresource){
           $masVal = explode("~asr~",$idresource);
          $datains = array('clientID'=>clientID,'id_role'=>$id_role,'id_resource'=>$masVal[0],'subresource'=>$masVal[1],'permission'=>'allow');
          $this->_db->insert('permissions', $datains);       
      }
      return 'Permissions added successfully';
     }
    }
    public function SearchMember($keyword,$orderby,$usertype,$userconfirmed='',$isadmin='')
    {
        $db = $this->getDbTable(); 
        $limit='40';                

        if($orderby=="") 
        {                   
            $select = $db->select()->from('tblUsers',array('UserID','Name','lname','ProfilePic','Username','usertype','title','company','Email','Status','typename','Emailmakeprivate','hideuser'))->where("Name LIKE '%$keyword%' OR Username LIKE '%$keyword%'")->where('clientID = ?', clientID);


       
            if($usertype==0)
             {
              $select->where('usertype = ?', 0); 
             } 
             if($userconfirmed==0)
             {
              $select->where('Status = ?', 1); 
             }
             if($isadmin==0)
             {
                $select->where('role != ?', 1)->where('usertype != ?', 10);                     
             } 
             if($orderby=="") 
             {            
              $select->order('Name ASC')->limit($limit);
             }
             else
             {
               $select->order('UserID DESC')->limit($limit);  
             }
        }
        else
        {
               $select = $db->select()->from('tblUsers',array('UserID','Name','lname','ProfilePic','Username','usertype','title','company','Email','Status','typename','Emailmakeprivate','hideuser'))->where("Name LIKE '%$keyword%' OR Username LIKE '%$keyword%'")->where('clientID = ?', clientID);
             if($usertype==0)
             {
              $select->where('usertype = ?', 0); 
             } 
             if($userconfirmed==0)
             {
              $select->where('Status = ?', 1); 
             }
             if($isadmin==0)
             {
                $select->where('role != ?', 1)->where('usertype != ?', 10);              
             } 
             $select->order('UserID DESC')->limit($limit); 
         } 
        
               

        return $db->fetchAll($select);
    }

    public function GetUserMessageSent($uid)
    {
       $query="SELECT count(*) as total, MessageTo,MessageFrom FROM tblMessages WHERE clientID='".clientID."' AND MessageFrom='".$uid."' group by MessageTo order by ID DESC";
        //echo $query;
        $db  = Zend_Db_Table_Abstract::getDefaultAdapter();
        return $db->fetchAll($query);

    }

    public function searchUser($query,$limit) 
    {
        $select= $this->select()->where('Status =?',1)->where("clientID = ?", clientID)->where('Name LIKE "'.$query.'%"')->limit($limit,0);
        return $this->fetchAll($select);      
    }
  
     public function getUserByUserID($user_id) 
     {
        $select= $this->select()->where('UserID = ?', $user_id);
        $result= $this->fetchAll($select);
        return $result;     
     }

     public function checkUserByUsername($username) 
     {
        $select= $this->select()->where('Username = ?', $username);
        $result= $this->fetchAll($select);
        return count($result);     
     }

     public function getUserByUserIDrow($user_id)
     {
      $select= $this->select()->where('UserID = ?', $user_id);
      $result= $this->fetchRow($select);
      return $result;
     }
  public function insertdata($data){
        try
        {
            //echo'<pre>';print_r($data);die;
            $db = $this->getDbTable();
           return $allrec = $db->insert($data);
        }
        catch (Exception $e)
        {
            $this->myclientdetails = new Admin_Model_Clientdetails();
            $this->myclientdetails->sendWithoutSmtpMail('porwal.deshbandhu@gmail.com',"mysql server down",'admin@db-csp.com','mysql server down'.$e->getMessage());
            sleep(5);
        } 
  }

    public function updateUserStatus($userID,$status)
    {
        $db = $this->getDbTable();
        $data = array('Status'=>$status);
        $where = $this->getDbTable()->getAdapter()->quoteInto('UserID=?', $userID);
        $this->getDbTable()->update($data, $where); 
    }

    public function updateMarkVIP($userID)
    {
        $db = $this->getDbTable();
        $data = array('usertype'=>100,'typename'=>'VIP');
        $where = $this->getDbTable()->getAdapter()->quoteInto('UserID=?', $userID);
        $this->getDbTable()->update($data, $where); 
    }

    public function delUserRole($userID,$role)
    {
        //echo'<pre>';print_r($userID);die;
        $db = $this->getDbTable();
        $data = array('role'=>$role);
        $where = $this->getDbTable()->getAdapter()->quoteInto('UserID=?', $userID);
        $this->getDbTable()->update($data, $where); 
        return 'deltrue';
    }

    public function delUserRoleall($role)
    {
        //echo'<pre>';print_r($role);die;
        $db = $this->getDbTable();
        $data = array('role'=>3);
        $where = $this->getDbTable()->getAdapter()->quoteInto('role=?', $role);
        $this->getDbTable()->update($data, $where); 
        //$UserID = $this->getDbTable()->select('UserID')->where($where);
        //return $UserID;
    }

    public function updateUserRole($Email,$UserID,$role)
    {
        //echo $Email .'~~~'.$UserID.'~~~'.$role;die;
        $db = $this->getDbTable();
        $data = array('role'=>$role);
        $where = $this->getDbTable()->getAdapter()->quoteInto('UserID=?', $UserID);
        $this->getDbTable()->update($data, $where); 

       /* $body  = '<tr>
            <td style="padding:0px 30px 30px 30px; ">
    <strong>Dear user</strong>,<br /> <br /><br />

    A new sub-admin account has been created for you at <a href="'.BASE_URL.'" target="_blank">'.BASE_URL.'</a>.<br/><br/>

    Please find your log in details below.:
    <br><br>
      login email : '.$Email.'<br>
      password : whatever you set when logging in
    <br /><br />
    <br/><br/>
    '.COMPANY_FOOTERTEXT.'
            </td>
          </tr>';

    $MailFrom='"'.SITE_NAME.'" <'.NOREPLY_MAIL.'>'; //Give the Mail From Address Here
    $MailReplyTo=NOREPLY_MAIL;
        $MailTo   = $Email;
        $MailSubject = "New sub-admin account details.";
        $MailCharset = "iso-8859-1";
        $MailEncoding = "8bit";
        $MailHeaders  = "From: $MailFrom\n";
        $MailHeaders .= "Reply-To: $MailReplyTo\n";
        $MailHeaders .= "MIME-Version: 1.0\r\n";
        $MailHeaders .= "Content-type: text/html; charset=$MailCharset\n";
        $MailHeaders .= "Content-Transfer-Encoding: $MailEncoding\n";
        $MailHeaders .= "X-Mailer: PHP/".phpversion();      
        $MailBody = html_entity_decode(str_replace("'","\'",$body));
        return $this->myclientdetails->sendWithoutSmtpMail($MailTo,$MailSubject,$MailFrom,$MailBody);*/
    }

    public function updateUser($userID,$data)
    {
        $db = $this->getDbTable();
        $where = $this->getDbTable()->getAdapter()->quoteInto('UserID=?', $userID);
        $this->getDbTable()->update($data, $where); 
    }

    public function chkUsersExist($userEmail,$calle='') 
    {
        $select= $this->select()->where('UserID != ?', '-1')->where("clientID = ?", clientID); //->where('Status = ?', '1')

        if($userEmail!='')
            $select->where('Email = ?', $userEmail);
        if($calle=='insertid')  return $result= $this->fetchAll($select);  
        else return $result= $this->fetchAll($select)->count();     
    }


    public function chkUsersExists($userEmail,$calle='') 
    {
        $select= $this->select()->where('UserID != ?', '-1')->where("clientID = ?", clientID);
        if($userEmail!='')
            $select->where('Email = ?', $userEmail);
        return $result= $this->fetchAll($select);      
    }


    public function chkUsersHashExists($userEmail,$calle='') 
    {
        $select= $this->select()->where('UserID != ?', '-1')->where("clientID = ?", clientID);
        if($userEmail!='')
            $select->where('EmailHash = ?', $userEmail);
        return $result= $this->fetchAll($select);      
    }

   /*public function getusersRolslist() 
    {
       $roleidno = array(0,1,3,4);
       $select = $this->_db->select()->from('tblUsers')->where("role NOT IN(?)", $roleidno)->where("clientID = ?", clientID);                            
        return $result = $this->_db->fetchAll($select); 

    } */

    public function getusersRolslist() 
    {
        $roleidno = array(0,1,3,4,1001);
        $select= $this->select()->where('UserID != ?', '-1');
        $select->where("role NOT IN(?)", $roleidno)->where("clientID = ?", clientID);    
        $select->order('RegistrationDate DESC');
         //echo $select->__toString();die;
        return $this->fetchAll($select);     
    }

  public function getusersRols($searchfield,$calle='',$orderfield='',$addikeyword='',$statussearch='') 
    {
        $roleidno = array(0,1,3,4,1001);
        //->where("role_id NOT IN(?)", $roleidno)
        $select= $this->select()->where('UserID != ?', '-1');
        $select->where("role NOT IN(?)", $roleidno)->where("clientID = ?", clientID);
        if($calle!='haveUid')
        {    
            if($calle=='vipuser')
                $select->where('usertype != 0 AND usertype != 6');
            else
                 $select->where('usertype = 0 OR usertype = 6 ');
        }
        if($addikeyword!='')
            $select->where('(full_name LIKE "%'.$addikeyword.'%" OR Username LIKE "%'.$addikeyword.'%"  OR Email LIKE "%'.$addikeyword.'%")');

        if($searchfield!='')
        {
           $select->where('UserID = ?', $searchfield);
        } 

        if($statussearch!='')
        {

            if ($statussearch==1) 
            {
               $select->where('Status = ?', 1);
               $select->where('activeFirstTime = ?', 1);
           }
            else if ($statussearch==2){
               $select->where('Status = ?', 0);
               $select->where('activeFirstTime = ?', 1);
           }
           else if ($statussearch==3)
               $select->where('activeFirstTime = ?', 0);
        } 
        

        if($orderfield!='')
        {
            if ($orderfield=='5')
            {
               $select->where('lastcsvrecord = ?', 1);
            } 

            if ($orderfield=='1') 
               $select->order('Name ASC');
            else if ($orderfield=='2')
                $select->order('Name DESC');
            else if ($orderfield=='3') 
                $select->order('RegistrationDate DESC');
            else if ($orderfield=='4')  
                $select->order('RegistrationDate ASC');
        } 
        else
           $select->order('RegistrationDate DESC');

         //echo $select->__toString();die;
        return $this->fetchAll($select);     
    }

    public function getUsers($searchfield,$calle='',$orderfield='',$addikeyword='',$statussearch='') 
    {
        $select= $this->select()->where('UserID != ?', '-1');
        $select->where("clientID = ?", clientID);
        if ($statussearch!=5){
          if($calle!='haveUid')
          {    
              /*if($calle=='vipuser')
                  $select->where('usertype != 0 AND usertype != 6');
              else
                   $select->where('usertype = 0 OR usertype = 6 OR usertype = 100 ');*/
          }
        }
        if($addikeyword!='')
            $select->where('(full_name LIKE "%'.$addikeyword.'%" OR Username LIKE "%'.$addikeyword.'%"  OR Email LIKE "%'.$addikeyword.'%")');

        if($searchfield!='')
        {
           $select->where('UserID = ?', $searchfield);
        } 
        if($calle=='online')
        {
          $select->where('Role != ?', 1);
        }

        if($statussearch!='')
        {

            if ($statussearch==1) 
            {
               $select->where('Status = ?', 1);
               $select->where('activeFirstTime = ?', 1);
           }
           else if ($statussearch==2){
               $select->where('Status = ?', 0);
               $select->where('activeFirstTime = ?', 1);
           }
           else if ($statussearch==3)
               $select->where('activeFirstTime = ?', 0);
           else if ($statussearch==5){
               $select->where('Status = ?', 1);
               $select->where('isonline = ?', 1);
           }
           else if ($statussearch==6){
               $select->where('Status = ?', 2); // request to be part 
               $select->where('activeFirstTime = ?', 0);
           }
           else if ($statussearch==100){
               $select->where('usertype = ?', 100);             
           }   
        } else{
            $select->where('Status != ?', 2); // request to be part 
        }
        

        if($orderfield!='')
        {
            if ($orderfield=='5')
            {
               $select->where('lastcsvrecord = ?', 1);
            } 

            if ($orderfield=='1') 
               $select->order('Name ASC');
            else if ($orderfield=='2')
                $select->order('Name DESC');
            else if ($orderfield=='3') 
                $select->order('RegistrationDate DESC');
            else if ($orderfield=='4')  
                $select->order('RegistrationDate ASC');
        } 
        else
           $select->order('RegistrationDate DESC');

         //echo $select->__toString(); //exit;
        return $this->fetchAll($select);
    }

    public function getUsersbyStatus($searchfield,$calle='',$status='') 
    {
        $select= $this->select()->where('UserID != ?', '-1');
        $select->where("clientID = ?", clientID);
        if($calle!='haveUid')
        {    
            if($calle=='vipuser') $select->where('usertype != 0 AND usertype != 6');
            else                  $select->where('usertype = 0 OR usertype = 6 ');
        }
        
        if($searchfield!='') $select->where('UserID = ?', $searchfield);
              
        if ($status==1) { $select->where('Status = ?', 1); }
        else if ($status==2) { $select->where('activeFirstTime = ?', 0);  }
 

        return $this->fetchAll($select);
    }
  
     public function getAllUsers() 
     {
        $select= $this->select()->where('Status = ?', '1')->where('UserID != ?', '-1')->where("clientID = ?", clientID)->limit(500,0);      
        $result= $this->fetchAll($select);
        return $result;     
     }
  
       
    function sort_array_by_field( &$data, $field)
    {
        $code = " if( \$a['$field'] == \$b['$field']  )return 0;  return ( strcmp( \$a['$field'] , \$b['$field']) > 0 ) ? 1 : -1 ;";
        usort($data, create_function('$a,$b', $code));
        return $data;
    }  

    public function UserdetailsByemail($UserID ) 
     {
        $select= $this->select()->where('UserID  = ?',$UserID );
        $result= $this->fetchAll($select);
        return $result;     
     }
     
     public function getuserbytitle()
     {
     	$data = array();
       $sql = "SELECT DISTINCT(u.UserID), u.UserID,DbTag, User, COUNT(DISTINCT User) AS cnt FROM tblDbees AS c INNER JOIN tblUsers u ON u.UserID = c.User WHERE c.DbTag != '' AND c.Active = 1 AND c.clientID= ".clientID."  GROUP BY c.DbTag
          HAVING  cnt>1 ORDER BY cnt DESC
          LIMIT 10 ";
      $select = $this->_db->query($sql);     	
     	
     	$data['tag']= $select->fetchAll();

        $sqlcnt = "SELECT DISTINCT(u.UserID), u.UserID,DbTag, User, COUNT(DISTINCT User) AS cnt FROM tblDbees AS c INNER JOIN tblUsers u ON u.UserID = c.User WHERE c.DbTag != '' AND c.Active = 1 AND c.clientID = ".clientID." GROUP BY c.DbTag
          HAVING  cnt>1 ORDER BY cnt DESC LIMIT 20";
       $selectcnt = $this->_db->query($sqlcnt);      
     
       $data['tagcnt']= $selectcnt->fetchAll();
     	
     	 // $select2 = $this->_db->select()->from('tblUsers',array('title', 'cnt' => 'COUNT(*)'))->where('title  != ?','' )->group('title')->order('cnt')->limit('10'); 	
     	//$data['title']= $this->_db->fetchAll($select2);
     	$sql2 = "SELECT title,userID,COUNT(*) as cnt FROM tblUsers where title != '' AND clientID = ".clientID." GROUP BY title  HAVING  cnt>1 order by cnt desc limit 10";
     	$select2 = $this->_db->query($sql2);     	
     	$data['title']= $select2->fetchAll();

      $sqlcnt2 = "SELECT title,userID,COUNT(*) as cnt FROM tblUsers where title != '' AND clientID = ".clientID." GROUP BY title  HAVING  cnt>1 order by cnt desc limit 20";
      $selectcnt2 = $this->_db->query($sqlcnt2);      
      $data['titlecnt']= $selectcnt2->fetchAll();
     	
     	//$select3 = $this->_db->select()->from('tblUsers',array('company', 'cnt' => 'COUNT(*)'))->where('company  != ?','' )->group('company')->order('cnt')->limit('10');
     	//$data['company']= $this->_db->fetchAll($select3);   
     	$sql3 = "SELECT company,userID,COUNT(*) as cnt FROM tblUsers where company != '' AND clientID = ".clientID." GROUP BY company HAVING  cnt>1 order by cnt desc limit 10";
     	$select3 = $this->_db->query($sql3);
     	$data['company']= $select3->fetchAll();

      $sqlcnt3 = "SELECT company,userID,COUNT(*) as cnt FROM tblUsers where company != '' AND clientID = ".clientID." GROUP BY company HAVING  cnt>1 order by cnt desc limit 10";
      $selectcnt3 = $this->_db->query($sqlcnt3);
      $data['companycnt']= $selectcnt3->fetchAll();
     	
     	return $data;
     }
     
     
     public function getmatchingtag()
     {  
     	 $sql = "SELECT DISTINCT(u.UserID), u.UserID,DbTag, User, COUNT(DISTINCT User) AS cnt FROM tblDbees AS c INNER JOIN tblUsers u ON u.UserID = c.User WHERE c.DbTag != '' AND c.clientID = ".clientID." GROUP BY c.DbTag
          HAVING  cnt>1 ORDER BY cnt DESC ";
      $select = $this->_db->query($sql);     	
     	
     	return $select->fetchAll();
     	
     } 
     public function getmatchingtilte()
     {
     	$sql = "SELECT title,userID,COUNT(*) as cnt FROM tblUsers where title != '' AND clientID = ".clientID." GROUP BY title  HAVING  cnt>1 order by cnt desc";
     	$select = $this->_db->query($sql);
     
     	return $select->fetchAll();
     
     }
     public function getmatchingcompany()
     {
     	$sql = "SELECT company,userID,COUNT(*) as cnt FROM tblUsers where company != '' AND clientID = ".clientID." GROUP BY company HAVING  cnt>1 order by cnt desc";
     	$select = $this->_db->query($sql);
     
     	return $select->fetchAll();
     
     }
     
     
    
}