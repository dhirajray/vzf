<?php
class Application_Model_DbUser
{
    protected $_dbTable;
    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Application_Model_DbTable_DbUser');
        }
        return $this->_dbTable;
    }
    
    public function userallval()
    {
        $db  = Zend_Db_Table_Abstract::getDefaultAdapter();
        $SQL = "select * from tblUsers where clientID='".clientID."' limit 0,11 ";
        return $db->fetchAll($SQL);
    }
    public function globalSetting()
    {
        $db  = Zend_Db_Table_Abstract::getDefaultAdapter();
        $SQL = "select * from tblAdminSettings where clientID='".clientID."'";
        return $db->fetchAll($SQL);
    }
    //**************** Profile Users related functions *****************************        
    public function adduser($userPersonalInfo)
    {
        return $this->getDbTable()->insert($userPersonalInfo);
    }
    //Adding users to data base at the time of registration
    public function userdetailall($userid)
    {
        $db = $this->getDbTable();
        $select = $db->select()->where('UserID = ?', $userid)->where('clientID = ?', clientID);
        $result = $db->fetchAll($select);
        return $result->toArray();
    }

 public function userDirectory($keyword,$text,$usertype,$userconfirmed='',$filterkeyword='',$orderby='',$companykeyword='',$isadmin='')
    {       

        $db = $this->getDbTable();
        $limit='40';         
        $select = $db->select()->from('tblUsers',array('UserID','Name','lname','ProfilePic','Username','usertype','title','company','Email','Status','typename','Emailmakeprivate'));
        $select->where('clientID = ?', clientID);

            if($text!="")
            {
             $select->where("full_name LIKE '%$text%' OR Name LIKE '%$text%' OR Username LIKE '%$text%'");
            }

            if($keyword!="")
            {
              $select->where('Name LIKE ?', ''.$keyword.'%');
            }

            if($companykeyword!="")
            {              
              $select->where('company LIKE ?', ''.$companykeyword.'%');
            }

            if(isADMIN!=1) 
            {
              $select->where("hideuser != ?", 1);
            } 

            if($filterkeyword!="")
            {
              
              if(count($filterkeyword) > 0)
              {
                    $company=array();
                    $title=array();
                    $usertypefilter=array();

                foreach ($filterkeyword as $key => $value) {
                    
                    $slice=explode('@@@', $value);                   

                    if($slice[1]=='company')
                    {
                        $company[]=$slice[0];
                    }
                    else if($slice[1]=='title')
                    {
                        $title[]=$slice[0];
                    }
                    else if($slice[1]=='usertype')
                    {
                         $usertypefilter[]=$slice[0];
                    }

                }

                if(count($company) > 0)
                {
                 $select->where('company IN(?) ', $company);
                }

                if(count($title) > 0)
                {
                 $select->where('title IN(?) ', $title);
                }

                if(count($usertypefilter) > 0)
                {
                 $select->where('typename IN(?) ', $usertypefilter);
                }

                
              }
            }

            if($userconfirmed==0)
             {
              $select->where('Status = ?', 1); 
             }

             if($isadmin==0)
             {
                $select->where('role != ?', 1)->where('usertype != ?', 10);                
             }

             if($usertype==0)
             {
              $select->where("usertype =  0 OR usertype =  6"); 
              
             }             
          
             if($orderby=="") 
             { 
           
              $select->order('UserID DESC')->limit($limit);
             }
             else
             {
               $select->order('UserID DESC')->limit($limit);  
             }
        // echo $select->__toString();
         //die;      
        return $db->fetchAll($select)->toArray();
    }

    public function userDirectoryalphachar($keyword,$text,$usertype,$userconfirmed='',$filterkeyword='',$orderby='',$companykeyword='',$isadmin='')
    {
        $db = $this->getDbTable();
        $select = $db->select()->from('tblUsers',array('UserID','Name','lname','ProfilePic','Username','usertype','title','company','Email','Status','typename','Emailmakeprivate'));
        $select->where('clientID = ?', clientID);
            if($text!="")
            {
             $select->where("full_name LIKE '%$text%' OR Name LIKE '%$text%' OR Username LIKE '%$text%'");
            }
            if($keyword!="")
            {
              //$select->where('Name REGEXP ?', '^'.$keyword);
              $select->where('Name LIKE ?', ''.$keyword.'%');
            }
            if($companykeyword!="")
            {
             // $select->where('company REGEXP ?', '^'.$companykeyword);
                $select->where('company LIKE ?', ''.$companykeyword.'%');
            }

            if(isADMIN!=1) 
            {
              $select->where("hideuser != ?", 1);
            } 

            if($filterkeyword!="")
            {
              
              if(count($filterkeyword) > 0)
              {
                    $company=array();
                    $title=array();
                    $usertypefilter=array();

                foreach ($filterkeyword as $key => $value) {
                    
                    $slice=explode('@@@', $value);                   

                    if($slice[1]=='company')
                    {
                        $company[]=$slice[0];
                    }
                    else if($slice[1]=='title')
                    {
                        $title[]=$slice[0];
                    }
                    else if($slice[1]=='usertype')
                    {
                         $usertypefilter[]=$slice[0];
                    }

                }

                if(count($company) > 0)
                {
                 $select->where('company IN(?) ', $company);
                }

                if(count($title) > 0)
                {
                 $select->where('title IN(?) ', $title);
                }

                if(count($usertypefilter) > 0)
                {
                 $select->where('typename IN(?) ', $usertypefilter);
                }

                
              }
            }

            if($userconfirmed==0)
             {
              $select->where('Status = ?', 1); 
             }
             if($isadmin==0)
             {
               $select->where('role != ?', 1)->where('usertype != ?', 10);                
             } 

             if($usertype==0)
             {
              $select->where("usertype =  0 OR usertype =  6"); 
             }
          
             if($orderby=="") 
             { 
           
              $select->order('UserID DESC');
             }
             else
             {
               $select->order('UserID DESC');  
             }
        //echo $select->__toString();
         
        return $db->fetchAll($select)->toArray();
    }

     public function userDirectoryCount($keyword,$text,$usertype,$userconfirmed='',$filterkeyword='',$orderby='',$companykeyword='',$isadmin='')
    {
        $db = $this->getDbTable();        
        $select = $db->select()->from('tblUsers',array('count(*) AS total'));
        $select->where('clientID = ?', clientID);
            if($text!="")
            {
             $select->where("full_name LIKE '%$text%' OR Name LIKE '%$text%' OR Username LIKE '%$text%'");
            }

            if($keyword!="")
            {
              //$select->where('Name REGEXP ?', '^'.$keyword);
              $select->where('Name LIKE ?', ''.$keyword.'%');
            }

            if($companykeyword!="")
            {
              //$select->where('company REGEXP ?', '^'.$companykeyword);
                $select->where('company LIKE ?', ''.$companykeyword.'%');
            }            
            if(isADMIN!=1) 
            {
              $select->where("hideuser != ?", 1);
            } 
            if($filterkeyword!="")
            {
              
              if(count($filterkeyword) > 0)
              {
                    $company=array();
                    $title=array();
                    $usertypefilter=array();

                foreach ($filterkeyword as $key => $value) {
                    
                    $slice=explode('@@@', $value);                   

                    if($slice[1]=='company')
                    {
                        $company[]=$slice[0];
                    }
                    else if($slice[1]=='title')
                    {
                        $title[]=$slice[0];
                    }
                    else if($slice[1]=='usertype')
                    {
                         $usertypefilter[]=$slice[0];
                    }

                }

                if(count($company) > 0)
                {
                 $select->where('company IN(?) ', $company);
                }

                if(count($title) > 0)
                {
                 $select->where('title IN(?) ', $title);
                }

                if(count($usertypefilter) > 0)
                {
                 $select->where('typename IN(?) ', $usertypefilter);
                }

                
              }
            }

            if($userconfirmed==0)
             {
              $select->where('Status = ?', 1); 
             } 

             if($isadmin==0)
             {
                $select->where('role != ?', 1)->where('usertype != ?', 10);                    
             }

             if($usertype==0)
             {
              $select->where("usertype =  0 OR usertype =  6");  
             }
          
             if($orderby=="") 
             { 
           
              $select->order('UserID DESC');
             }
             else
             {
               $select->order('UserID DESC');  
             }    
            //echo $select->__toString();   
        return $db->fetchAll($select)->toArray();
    }

 public function SearchMember($keyword,$orderby,$usertype,$userconfirmed='',$isadmin='')
    {
        $db = $this->getDbTable(); 
        $limit='40';                

        if($orderby=="") 
        {                   
            $select = $db->select()->from('tblUsers',array('UserID','Name','lname','ProfilePic','Username','usertype','title','company','Email','Status','typename','Emailmakeprivate'))->where("Name LIKE '%$keyword%' OR Username LIKE '%$keyword%'")->where('clientID = ?', clientID);


       
            if($usertype==0)
             {
              $select->where("usertype =  0 OR usertype =  6");  
             } 
             if($userconfirmed==0)
             {
              $select->where('Status = ?', 1); 
             }             
            if(isADMIN!=1) 
            {
              $select->where("hideuser != ?", 1);
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
               $select = $db->select()->from('tblUsers',array('UserID','Name','lname','ProfilePic','Username','usertype','title','company','Email','Status','typename','Emailmakeprivate'))->where("Name LIKE '%$keyword%' OR Username LIKE '%$keyword%'")->where('clientID = ?', clientID);
             if($usertype==0)
             {
              $select->where("usertype =  0 OR usertype =  6"); 
             } 
             if($userconfirmed==0)
             {
              $select->where('Status = ?', 1); 
             }             
             if(isADMIN!=1) 
             {
              $select->where("hideuser != ?", 1);
             } 
             if($isadmin==0)
             {
                $select->where('role != ?', 1)->where('usertype != ?', 10);              
             } 
             $select->order('UserID DESC')->limit($limit); 
         } 
        
               

        return $db->fetchAll($select);
    }


    public function SearchMemberPaging($Id,$SearchText,$SortBy,$usertype,$userconfirmed='',$filterkeyword='',$companykeyword='',$isadmin='')
    {
        $db = $this->getDbTable();
        $limit='40';
        $skip = ($Id-1)*$limit;     
        
        
       $select = $db->select()->from('tblUsers',array('UserID','Name','lname','ProfilePic','Username','usertype','title','company','Email','Status','typename','Emailmakeprivate'))->where('clientID = ?', clientID);

        if($SearchText!="")
            $select->where("full_name LIKE '%$text%' OR Name LIKE '%$SearchText%' OR Username LIKE '%$SearchText%'");
            //$query.=" AND (Name Like '%".$SearchText."%' OR Username LIKE '%".$SearchText."%') ";
        
        if($SortBy!="")
          
            $select->where('Name REGEXP ?', '^'.$SortBy);
            //$select->where('Name LIKE ?', ''.$keyword.'%');

         if($companykeyword!="")
          {
              $select->where('company REGEXP ?', '^'.$companykeyword);
         }         
        if(isADMIN!=1) 
        {
          $select->where("hideuser != ?", 1);
        } 
        if($usertype==0)
        {
          $select->where("usertype =  0 OR usertype =  6");  
        }

           if($filterkeyword!="")
            {
              
              if(count($filterkeyword) > 0)
              {
                    $company=array();
                    $title=array();
                    $usertypefilter=array();
                foreach ($filterkeyword as $key => $value) {
                    
                    $slice=explode('@@@', $value);

                   

                    if($slice[1]=='company')
                    {
                        $company[]=$slice[0];
                    }
                    else if($slice[1]=='title')
                    {
                        $title[]=$slice[0];
                    }
                    else if($slice[1]=='usertype')
                    {
                         $usertypefilter[]=$slice[0];
                    }

                }

                if(count($company) > 0)
                {
                   $select->where('company IN(?) ', $company);                 
                }

                if(count($title) > 0)
                {
                  $select->where('title IN(?) ', $title);                  
                }

                if(count($usertypefilter) > 0)
                {
                  $select->where('typename IN(?) ', $usertypefilter);                  
                }


                
                
              }
            }

        if($userconfirmed==0)
        {
           $select->where('Status = ?', 1);
        }
        if($isadmin==0)
        {
           $select->where('role != ?', 1)->where('usertype != ?', 10);                   
        } 
        
        $select->order('UserID DESC')->limit($limit,$skip);  

      //echo $select->__toString();exit;

        return $db->fetchAll($select)->toArray();
    }

    public function filtermember($usertype)
    {
      $query="SELECT UserID,usertype,title,company,typename FROM tblUsers WHERE clientID='".clientID."'";
      
      if($usertype==0) 
      {
       $query.=" AND (usertype='0' OR usertype='6')";
      }

     if(isADMIN!=1) 
     {
       $query.=" AND hideuser != 1"; 
     } 

      $query.="order by company asc";
      //echo $query;
        $db  = Zend_Db_Table_Abstract::getDefaultAdapter();
        return $db->fetchAll($query);
    }
    public function auserdetail($email)
    {
        $db = $this->getDbTable();
        $select = $db->select()->where('Email = ?', $email)->where('clientID = ?', clientID);
        $result = $db->fetchAll($select);
        return $result->toArray();
    }
    // edit by desh
    public function getUserDetail($UserID)
    {
        $db = $this->getDbTable();
        $select = $db->select()->where('UserID = ?', $UserID)->where('clientID = ?', clientID);
        return $db->fetchRow($select);
    }
    public function getUserDetailByUserType($usertype)
    {
        $db = $this->getDbTable();
        $select = $db->select()->where('usertype = ?', $usertype)->where('clientID = ?', clientID);
        return $db->fetchRow($select);
    }
    public function ausersocialdetail($UserID)
    {
        $db = $this->getDbTable();
        $select = $db->select()->where('UserID = ?', $UserID)->where('clientID = ?', clientID);
        $result = $db->fetchAll($select);
        return $result->toArray();
    }
    public function editausersocialdetail($data)
    {
        $db = $this->getDbTable();
        $UserID = $data['UserID'];
        $where[] = $this->getDbTable()->getAdapter()->quoteInto('UserID=?', $UserID);
        $where[] = $this->getDbTable()->getAdapter()->quoteInto('clientID=?', clientID);
        $this->getDbTable()->update($data, $where);
    }
    public function auseremailcheck($Email)
    {
        $db = $this->getDbTable();
        $select = $db->select()->where('Email = ?', $Email)->where('clientID = ?', clientID);
        return $db->fetchAll($select)->count();
    }
    
    public function getUserDataByUsername($username)
    {
        $db = $this->getDbTable();
        $select = $db->select()->where('Username = ?', $username)->where('clientID = ?', clientID);
        return $db->fetchAll($select);
    }
    
    public function chkusername($username)
    {
        $select = $this->_db->select()
            ->from($this->getTableName(USERS),array('UserID'))
                ->where("Username = ?", $username)
                ->where("clientID = ?", clientID);     
        $data = $this->_db->fetchRow($select);
    if($data['UserID']>0){
        $usernames = $username.''.date('-s');
        return str_replace('-', '', $usernames);
    }else 
        return $username;
    } 

    public function getSocialDetail($socialID)
    {
        $db = $this->getDbTable();
        $select = $db->select()->where('clientID = ?', clientID)
        ->where('Socialid = ?', $socialID)->limit(1);
        $result = $db->fetchAll($select);
        return $result->toArray();
    }
    public function checkSocialEmail($social_id, $email)
    {
        $db = $this->getDbTable();
        $select = $db->select()->where('Email = ?', $email)->where('clientID = ?', clientID)->where('Socialid != ?', $social_id);
        return $db->fetchAll($select)->count();
    }
    // *****************************
    // @ User Login Check 
    // *****************************
    public function chkLogin($email, $pass)
    {
        $db = $this->getDbTable();
        $select = $db->select()->where('Email = ?', $email)->where('password = ?', $pass)->where('Status = ?', '1')->where('clientID = ?', clientID);
        $result = $db->fetchAll($select)->count();
        if ($result == 1)
            return $result = $db->fetchAll($select);
        else if ($result == 0) {
            $select1 = $db->select()->where('email = ?', $email)->where('password = ?', $pass)->where('Status = ?', '1')->where('clientID = ?', clientID);
            $result1 = $db->fetchAll($select1)->count();
            if ($result1 == 1)
                return 'Deactivate';
            else {
                $select2 = $db->select()->where('email = ?', $email)->where('password = ?', $pass)->where('clientID = ?', clientID);
                $result2 = $db->fetchAll($select2)->count();
                if ($result2 == 1)
                    return 'EmailActivation';
                else
                    return 'Password Id not matched';
            }
        }
    }
    public function chkAval_user($data)
    {
        $db     = $this->getDbTable();
        $select = $db->select()->where('Email = ?', $data)->where('clientID = ?', clientID);
        $result = $db->fetchAll($select);
        return $result->toArray();
    }
    public function checkresetcode($user, $resetcode)
    {
        $db = $this->getDbTable();
        $select = $db->select()->where('UserID = ?', $user)
        ->where('ResetPassCode = ?', $resetcode)->where('clientID = ?', clientID);
        return $db->fetchAll($select)->count();
    }
    public function ausersocialdetailforget($email)
    {
        $db = $this->getDbTable();
        $select = $db->select()->where('Email = ?', $email)->where('clientID = ?', clientID);
        return $db->fetchRow($select);
    }
    public function chkAcountval_user($data)
    {
        $db = $this->getDbTable();
        $select = $db->select()->where('Signuptoken = ?', $data)->where('clientID = ?', clientID);
        $result = $db->fetchAll($select);
        return $result->toArray();
    }
    public function chkAcountval2_user($data)
    {
        $db = $this->getDbTable();
        $select = $db->select()->where('Emailtoken = ?', $data)->where('clientID = ?', clientID);
        $result = $db->fetchAll($select);
        return $result->toArray();
    }
    // *****************************
    // @ active users profile
    // *****************************
    public function activate_user($email)
    {
        $db = $this->getDbTable();
        $data = array(
            'Status' => '1',
            'Signuptoken' => '0'
        );
        $where[] = $db->getAdapter()->quoteInto('Email = ?', $email);
        $where[] = $db->getAdapter()->quoteInto('clientID = ?', clientID);
        return $this->getDbTable()->update($data, $where);
    }
    public function activate_email($email)
    {
        $db = $this->getDbTable();
        $data = array(
            'Emailtoken' => '0'
        );
        $where[] = $db->getAdapter()->quoteInto('Email = ?', $email);
        $where[] = $db->getAdapter()->quoteInto('clientID = ?', clientID);
        return $this->getDbTable()->update($data, $where);
    }
    public function resetpasscode($UserID, $rand)
    {
        $db = $this->getDbTable();
        $data = array(
            'ResetPassCode' => $rand
        );
        $where[] = $db->getAdapter()->quoteInto('UserID = ?', $UserID);
        $where[] = $db->getAdapter()->quoteInto('clientID = ?', clientID);
        return $this->getDbTable()->update($data, $where);
    }
    public function resetpass($user, $hashpass)
    {
        $db = $this->getDbTable();
        $data = array(
            'Pass' => $hashpass
        );
        $where[] = $db->getAdapter()->quoteInto('UserID = ?', $user);
        $where[] = $db->getAdapter()->quoteInto('clientID = ?', clientID);
        return $this->getDbTable()->update($data, $where);
    }
   
    /**********for settingcontroller ***********/
    public function updateinfouser($data)
    {
        $db = $this->getDbTable();
        $where[] = $this->getDbTable()->getAdapter()->quoteInto('UserID=?', $data['UserID']);
        $where[] = $db->getAdapter()->quoteInto('clientID = ?', clientID);
        $this->getDbTable()->update($data, $where);
    }
    
    public function deactiveuser($UserID)
    {
        $db = $this->getDbTable();
        $data = array(
            'Status' => '0'
        );
        $where[] = $this->getDbTable()->getAdapter()->quoteInto('UserID=?', $UserID);
        $where[] = $db->getAdapter()->quoteInto('clientID = ?', clientID);
        $this->getDbTable()->update($data, $where);
    }
    public function updateusergeoinfo($data, $UserID)
    {
        $db    = $this->getDbTable();
        $where[] = $db->getAdapter()->quoteInto('UserID = ?', $UserID);
        $where[] = $db->getAdapter()->quoteInto('clientID = ?', clientID);
        return $this->getDbTable()->update($data, $where);
    }
    public function updateSocialLogin($data, $socialID)
    {
        $where = array();
        $db    = $this->getDbTable();
        $where[] = $db->getAdapter()->quoteInto('Socialid = ?', $socialID);
        $where[] = $db->getAdapter()->quoteInto('clientID = ?', clientID);
        return $this->getDbTable()->update($data,$where);
    }
    public function updateRedirection($data, $UserID)
    {
        $db    = $this->getDbTable();
        $where[] = $db->getAdapter()->quoteInto('UserID = ?', $UserID);
        $where[] = $db->getAdapter()->quoteInto('clientID = ?', clientID);
        return $this->getDbTable()->update($data, $where);
    }

    
}