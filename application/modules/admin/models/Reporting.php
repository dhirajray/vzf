<?php

class Admin_Model_Reporting extends Zend_Db_Table_Abstract
{
     // Table name 
     protected $_name = 'tblUsers';
     // Schema name
     // protected $_use_adapter = "matjarna";
	 
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
            $this->setDbTable('Admin_Model_DbTable_User');
        }
        return $this->_dbTable;
    }
    public function init()
    {
        $this->myclientdetails = new Admin_Model_Clientdetails();
    }
	
	/* 
        * Start of the Reporting functions 
        * @Author Deepak  Nagar
        * Started on 3 June 2013
    */

    /* deshboard function */

    public function gettopDbeeusers($limit=2,$dbeeID='',$datefrom='', $dateto='') 
    {      
        $db = $this->getDbTable();
        $select = $db->select();
        /*$select->distinct('dbee.User')->from( array('dbee' => 'tblDbees'), array('DbeeID'=>'dbee.DbeeID',new Zend_Db_Expr("COUNT(dbee.DbeeID) AS total") ));
        $select->join( array('u' => 'tblUsers'), 'u.UserID=dbee.User', array( 'UserID' => 'u.UserID','username' => 'u.Name','lname' => 'u.lname','image' =>'u.ProfilePic','uname' =>'u.Username') );
        $select->setIntegrityCheck( false );     
        $select->where('u.Status = ?', '1');
        $select->where("dbee.clientID = ?", clientID);

        if(!empty($datefrom) && !empty($dateto)){
            $select->where("dbee.PostDate >= ?",  date('Y-m-d H:i:s',strtotime($datefrom)));
            $select->where("dbee.PostDate <= ?",  date('Y-m-d H:i:s',strtotime($dateto)));
        } 
        $select->group ( array ("dbee.User") );
        $select->order('total DESC');*/

        $select->distinct('act.act_userId')->from( array('act' => 'tblactivity'), array('DbeeID'=>'act.act_userId',new Zend_Db_Expr("COUNT(act.act_id) AS total"), new Zend_Db_Expr(" sum( case when act_type=1 then 1 else 0 end) as postCount" ), new Zend_Db_Expr(" sum( case when act_type=2 then 1 else 0 end) as commentCount" )));
        $select->join( array('u' => 'tblUsers'), 'u.UserID=act.act_userId', array( 'UserID' => 'u.UserID','username' => 'u.Name','lname' => 'u.lname','image' =>'u.ProfilePic','uname' =>'u.Username') );
        $select->setIntegrityCheck( false );     
        $select->where('u.Status = ?', '1');
        $select->where('u.Role != ?', '1');
        $select->where("act.clientID = ?", clientID);
        $select->where("act.act_type = 1 OR act.act_type = 2" );

        /*SELECT DISTINCT `act`.`act_userId` AS `DbeeID`, COUNT(act.act_id) AS total,sum(case when act_type=1 then 1 else 0 end) postCount,sum(case when act_type=2 then 1 else 0 end) commentCount ,`u`.`UserID`, `u`.`Name` AS `username`, `u`.`lname`, `u`.`ProfilePic` AS `image`, `u`.`Username` AS `uname` FROM `tblactivity` AS `act` INNER JOIN `tblUsers` AS `u` ON u.UserID=act.act_userId WHERE (u.Status = '1') AND (u.Role != '1') AND (act.clientID = '1') AND (act.act_type = 1 OR act.act_type = 2) GROUP BY `act`.`act_userId` ORDER BY `total` DESC*/

        if(!empty($datefrom) && !empty($dateto)){
            $select->where("act.act_date >= ?",  date('Y-m-d H:i:s',strtotime($datefrom)));
            $select->where("act.act_date <= ?",  date('Y-m-d H:i:s',strtotime($dateto)));
        } 
        $select->group ( array ("act.act_userId") );
        $select->order('total DESC');
        if($limit!='all')
        $select->LIMIT($limit, 0);
        //echo $select; exit;
        $result= $db->fetchAll($select);    
        return $result;     
    }

    public function gettopLiveGroup($limit=2) {             
        $db = $this->getDbTable();
        $select = $db->select();

        $select->setIntegrityCheck( false );
        $select->from( array('group' => 'tblGroups'),  array('ID'=>'group.ID', new Zend_Db_Expr("COUNT(group.ID) AS total")));
        $select->join( array('gt' => 'tblGroupTypes'), 'gt.TypeID = group.GroupType', array('TypeName' =>'gt.TypeName'));
        $select->join( array('u' => 'tblUsers'), 'u.UserID=group.User', array( 'UserID' => 'u.UserID','username' => 'u.Name','lname' => 'u.lname','image' =>'u.ProfilePic') );
        $select->where('u.Status = ?', '1');
        $select->where("u.clientID = ?", clientID);
        $select->group ( array ("u.UserID") );

        $select->group ( array ("group.User") );
        $select->order('total ASC');

        if($limit!='all')
        $select->LIMIT($limit, 0);      

        $result= $db->fetchAll($select);    

        return $result;     
    }

    public function getAllHashTagUser($text,$skip='')
    {    
        $sql = "SELECT `u`.`RegistrationDate`,`u`.`Email`, `u`.`Name`, `u`.`lname`, `u`.`Username`, `u`.`ProfilePic`, `u`.`UserID` FROM `tblDbees` AS `c` INNER JOIN `tblUsers` AS `u` ON u.UserID = c.User WHERE FIND_IN_SET('".$text."',c.DbTag) AND (c.Active = '1') AND (c.clientID= '".clientID."') UNION SELECT `p`.`RegistrationDate`, `p`.`Email`, `p`.`Name`,`p`.`lname`, `p`.`Username`, `p`.`ProfilePic`, `p`.`UserID` FROM `tblDbeeComments` AS `d` INNER JOIN `tblUsers` AS `p` ON p.UserID = d.UserID WHERE FIND_IN_SET('".$text."',d.DbTag) AND (d.Active = '1') AND (d.clientID= '".clientID."') GROUP BY `UserID` ";
          
        if($skip!='')
        {
            $skip = ($skip-1)*8;
            $sql.="LIMIT $skip , 8";
        }

        return $this->_db->query($sql)->fetchAll();
    }


    public function getAllCommentHashTagUser($text,$skip='')
    {    
          $sql = "SELECT `p`.`RegistrationDate`,`p`.`Email`, `p`.`Name`, `p`.`lname`, `p`.`Username`, `p`.`ProfilePic`, `p`.`UserID` FROM `tblDbeeComments` AS `d` INNER JOIN `tblUsers` AS `p` ON p.UserID = d.UserID WHERE FIND_IN_SET('".$text."',d.DbTag) AND (d.Active = '1') AND (d.clientID= '".clientID."') GROUP BY `UserID` ";
          
        if($skip!='')
        {
            $skip = ($skip-1)*8;
            $sql.="LIMIT $skip , 8";
        }

        return $this->_db->query($sql)->fetchAll();
    }


    public function getAllPostHashTagUser($text,$skip='')
    {    
          $sql = "SELECT `u`.`RegistrationDate`,`u`.`Email`, `u`.`Name`, `u`.`lname`, `u`.`Username`, `u`.`ProfilePic`, `u`.`UserID` FROM `tblDbees` AS `c` INNER JOIN `tblUsers` AS `u` ON u.UserID = c.User WHERE FIND_IN_SET('".$text."',c.DbTag) AND (c.Active = '1') AND (c.clientID= '".clientID."') GROUP BY `UserID` ";
          
        if($skip!='')
        {
            $skip = ($skip-1)*8;
            $sql.="LIMIT $skip , 8";
        }

        return $this->_db->query($sql)->fetchAll();
    }

     public function getsearchHashTopdbee()
    {
        $select = $this->_db->select()->from(array(
            'c' => 'tblDbees',
            array(
                'c.DbTag'
            )
        ))->joinLeft(array(
            'u' => 'tblUsers'
        ), 'u.UserID = c.User', array(
            'u.UserID',
            'u.Name',
            'u.lname',
            'u.Username',
            'u.ProfilePic'
        ))->where('c.DbTag != ?', "")->where("c.Active= ?", '1')->where("c.clientID= ?", clientID);
        
        return $this->_db->fetchAll($select);
    }

     public function getsearchHashTopComment()
    {
        $select = $this->_db->select()->from(array(
            'c' => 'tblDbees',
            array(
                'c.DbeeID'
            )
        ))->joinLeft(array(
            'u' => 'tblUsers'
        ), 'u.UserID = c.User', array(
            'u.UserID'
        ))->joinLeft(array(
            'M' => 'tblDbeeComments'
        ), 'c.DbeeID = M.DbeeID', array(
            'M.DbTag','M.Active','M.DbeeID'
        ))->where('M.DbTag != ?', "")->where("M.Active= ?", '1')->where("M.clientID= ?", clientID);

        return $this->_db->fetchAll($select);
    }

    public function gettopLatestComment($limit='2',$cmtType='') 
    {

        $db = $this->getDbTable();
        $selectB = $db->select()->setIntegrityCheck( false )->from(array('commt' => 'tblDbeeComments'),  array('DbeeID'=>'commt.DbeeID', new Zend_Db_Expr("COUNT(commt.DbeeID) AS total")));
        $selectB->join( array('u' => 'tblUsers'), 'u.UserID=commt.UserID', array( 'UserID' => 'u.UserID','username' => 'u.Name','lname' => 'u.lname','image' =>'u.ProfilePic') );
        $selectB->join( array('dbee' => 'tblDbees'), 'dbee.DbeeID = commt.DbeeID',  array());

        $selectB->where('u.Status = ?', '1');
        $selectB->where('u.clientID = ?', clientID);

        $selectB->order('total DESC');
       
        $selectB->group ( array ("commt.UserID") );
        if($limit!='all')
       $selectB->LIMIT($limit, 0); 
         try{
                return $result = $db->fetchAll($selectB);       
            }catch(Exception $exc){
              return 0;
            }   
    }

    public function gettopLiveScore($limit='',$dbeeID='',$UserId='',$type='') 
    {
        $db = $this->getDbTable();
        $selectB = $db->select()->setIntegrityCheck( false )->from(array('score' => 'tblScoring'), array('ID'=>'score.ID', new Zend_Db_Expr("COUNT(score.ID) AS total")));
        $selectB->joinleft( array('u' => 'tblUsers'), 'u.UserID=score.UserID', array( 'UserID' => 'u.UserID','username' => 'u.Name','lname' => 'u.lname','image' =>'u.ProfilePic') );
      
        $selectB->where('u.Status = ?', '1');
        $selectB->where('u.clientID = ?', clientID);
        $selectB->order('total DESC');
       
        $selectB->group ( array ("score.UserID") );

        if($limit!='all')
        $selectB->LIMIT($limit, 0);
      
        try{
            $result = $db->fetchAll($selectB);          
        }catch(Exception $exc){
             $result = '';
        }
        return $result; 
    }



    public function getallcategory() { //$limit=10,$offset=0
         $db = $this->getDbTable();
        //$db is an instance of Zend_Db_Adapter_Abstract
        $select = $db->select();
        $select->distinct('u.CatName')->from( array('u' => 'tblDbeeCats'), array( 'catid' => 'u.CatID','catname' => 'u.CatName',) );
        $select->where('u.clientID = ?', clientID);
        $select->setIntegrityCheck( false );
        $select->order('u.Priority ASC');

        //$select->limit($limit,$offset);
        
        $result= $db->fetchAll($select);  
        return $result->toarray();   

    }

    public function getcategoryinterestusers($catName='',$calling='',$checkaction='',$limit='50',$offset='0') 
     {       
        $db = $this->getDbTable();
        //$db is an instance of Zend_Db_Adapter_Abstract
        $select = $db->select();
        $deshboard   = new Admin_Model_Deshboard();
       //echo $calling; 

        if(trim($calling)=="user comments within posts")
        {
           // $select->distinct('commt.UserID')->from( array('u' => 'tblUsers'), array( "totUser" => "count(u.UserID)") );
            $select->distinct('u.UserID')->from( array('commt' => 'tblDbeeComments'),   array( "Name" => "(u.Name)", "Email" => "(u.Email)"));
            $select->setIntegrityCheck( false );
            $select->joinInner( array('dbee' => 'tblDbees'), 'dbee.DbeeID = commt.DbeeID',  array('catID'=>'dbee.Cats',  ));
            $select->joinInner( array('u' => 'tblUsers'), 'u.UserID = commt.UserID', array( "Name" => "(u.Name)", "Email" => "(u.Email)","UserID" => "(u.UserID)"));
            $select->joinInner( array('cat' => 'tblDbeeCats'), 'cat.CatID = dbee.Cats', array('category' => new Zend_Db_Expr('(cat.CatName)')));
            
            //if($limit !='nolimit') { $select->limit($limit,$offset); }
           
        }
        else if(trim($calling)=="unique posts")
        {
            $select->distinct('dbee.User')->from( array('u' => 'tblUsers'), array( "Name" => "(u.Name)", "Email" => "(u.Email)","UserID" => "(u.UserID)") );
            $select->setIntegrityCheck( false );
            $select->joinInner( array('dbee' => 'tblDbees'), 'dbee.User = u.UserID',  array('catID'=>'dbee.Cats',  ));
            $select->joinInner( array('cat' => 'tblDbeeCats'), 'cat.CatID = dbee.Cats', array('category' => new Zend_Db_Expr('(cat.CatName)')));
            // if($limit !='nolimit') { $select->limit($limit,$offset); }
        }    
            $select->where('u.clientID = ?', clientID);
           $select->where('u.Status = ?', '1');
         //  $select->where(' cat.CatName LIKE "%'.$catName.'%"');
           // $select->where(' cat.CatName = ?', $catName);
           $caID = $this->myclientdetails->getfieldsfromtable('CatID','tblDbeeCats','CatName',$catName);
           $catID = $caID[0]['CatID'];
           $select->where('FIND_IN_SET(dbee.Cats,(?))', "$catID"); 
           if($limit !='nolimit') { $select->limit($limit,$offset); }
        //   exit;    
           
            $result= $db->fetchAll($select)->toarray();    
       
            return $result;     
    }

   

    public function getcategoryinterest($catId,$calling,$checkaction='') {       
        
        $db = $this->getDbTable();
        //$db is an instance of Zend_Db_Adapter_Abstract
        $select = $db->select();

        if($calling=="reportcomment")
        {
           // $select->distinct('commt.UserID')->from( array('u' => 'tblUsers'), array( "totUser" => "count(u.UserID)") );
            $select->distinct('u.UserID')->from( array('commt' => 'tblDbeeComments'),   array( "UserId" => "(u.UserID)"));
            $select->setIntegrityCheck( false );
            $select->joinInner( array('dbee' => 'tblDbees'), 'dbee.DbeeID = commt.DbeeID',  array('catID'=>'dbee.Cats',  ));
            $select->joinInner( array('u' => 'tblUsers'), 'u.UserID = commt.UserID',  array());
            $select->joinInner( array('cat' => 'tblDbeeCats'), 'cat.CatID = dbee.Cats', array('category' => new Zend_Db_Expr('(cat.CatName)')));
           
           
        }
        else if($calling=="reportdbee")
        {
            $select->distinct('dbee.User')->from( array('u' => 'tblUsers'), array( "totUser" => "(u.UserID)") );
            $select->setIntegrityCheck( false );
            $select->joinInner( array('dbee' => 'tblDbees'), 'dbee.User = u.UserID',  array('catID'=>'dbee.Cats',  ));
            $select->joinInner( array('cat' => 'tblDbeeCats'), 'cat.CatID = dbee.Cats', array('category' => new Zend_Db_Expr('(cat.CatName)')));

        }    
       $select->where('u.clientID = ?', clientID); 
       $select->where('u.Status = ?', '1');
       $select->where('FIND_IN_SET(Cats,(?))', "$catId"); 
       
        //echo $select->__toString();
        $result= $db->fetchAll($select)->toarray();    
   
        return $result;     
    }

    public function usersTracking($searchfield='',$calling='',$limit='20',$offset='0')
    {
        $select     =   $this->select();

        if($searchfield!='')
        {

            $select     =   $select->from(array('logins'=>'tbluserlogindetails'),array()); 
            $select->join( array('u' => 'tblUsers'), 'u.UserID=logins.userid', array( 'distinct(logins.userid)','u.UserID','u.ProfilePic','u.Name','u.lname','u.Email') );
            $select->setIntegrityCheck( false );
            $select->where("logins.clientID = ?", clientID);
            $ne  = explode("to", $searchfield);

            if($calling=='dateFilter')
            { 
                
                $condition = "DATE_FORMAT(`logindate`, '%Y-%m-%d') >= '".date('Y-m-d',strtotime($ne[0]))."' AND DATE_FORMAT(`logindate`, '%Y-%m-%d') <= '".date('Y-m-d',strtotime($ne[1]))."'" ;
                $select->where( $condition);
            }
            else if($calling=='fullMonth')
            { 
                    $select->where('DATE_FORMAT(logins.logindate, "%Y-%m") = ?',$searchfield);
            }
            else
            {
                if($ne[1]!=''){
                    $ne2  = explode("  ", trim($ne[0]));
                    
                     $to    = date('Y-m',strtotime($ne[1]));
                     //echo $to.'-'.$ne2[0];
                     $date  = date('Y-m-d',strtotime($to.'-'.$ne2[0]));

                     //$condition = "DATE_FORMAT(`logindate`, '%Y-%m-%d') >= '".$from."' AND DATE_FORMAT(`logindate`, '%Y-%m-%d') <= '".$to."'" ;
                     $select->where( 'DATE_FORMAT(logins.logindate, "%Y-%m-%d") = ?', $date);
                }
                else
                {
                    $searchfield  = date('Y-m-d',strtotime($searchfield));
                    $searchfmonth = date('m',strtotime($searchfield));
                    $select->where('DATE_FORMAT(logins.logindate, "%Y-%m-%d") = ?', $searchfield);
                }
            }
            
            
           // $select->group('DATE_FORMAT(logindate, "%Y-%m-%d")');
        } 
         
        //echo $select; exit;
        $result= $this->fetchAll($select)->toarray();
        return $result;
    }

     public function usersTrackingexport($searchfield='',$calling='',$limit='20',$offset='0')
    {
        $select     =   $this->select();

        if($searchfield!='')
        {

            $select     =   $select->from(array('logins'=>'tbluserlogindetails'),array()); 
            $select->join( array('u' => 'tblUsers'), 'u.UserID=logins.userid', array( 'distinct(logins.userid)','u.UserID','u.ProfilePic','u.Name','u.lname','u.Email') );
            $select->setIntegrityCheck( false );
            $select->where("logins.clientID = ?", clientID);
            $ne  = explode("to", $searchfield);


            if($calling=='fullMonth')
            { 
                    $select->where('DATE_FORMAT(logins.logindate, "%Y-%m") = ?',$searchfield);
            }
            else
            {
                

                if($ne[1]!=''){
                     $ne2  = explode("  ", trim($ne[0]));
                     if(strlen($ne2[0])!=10){

                         $to    = date('Y-m',strtotime($ne[1]));
                         //echo $to.'-'.$ne2[0];
                         $date  = date('Y-m-d',strtotime($to.'-'.$ne2[0]));

                         //$condition = "DATE_FORMAT(`logindate`, '%Y-%m-%d') >= '".$from."' AND DATE_FORMAT(`logindate`, '%Y-%m-%d') <= '".$to."'" ;
                         $select->where( 'DATE_FORMAT(logins.logindate, "%Y-%m-%d") = ?', $date);
                     }
                     else
                     {
                        //In case of date filters
                        $ne  = explode("to", $searchfield);
                        $condition = "DATE_FORMAT(`logindate`, '%Y-%m-%d') >= '".date('Y-m-d',strtotime($ne[0]))."' AND DATE_FORMAT(`logindate`, '%Y-%m-%d') <= '".date('Y-m-d',strtotime($ne[1]))."'" ;
                        $select->where( $condition);
                     }
                }
                else
                {
                    $ne2  = explode("  ", trim($searchfield));
                    
                    if($ne2[1]!='')  $select->where('DATE_FORMAT(logins.logindate, "%Y-%m-%d") = ?', date('Y-m-d',strtotime($searchfield))); 
                    else $select->where('DATE_FORMAT(logins.logindate, "%Y-%m") = ?',$searchfield);
                    
                }
            }
            
            
           // $select->group('DATE_FORMAT(logindate, "%Y-%m-%d")');
        } 
         
        //echo $select; exit;
        $result= $this->fetchAll($select)->toarray();
        return $result;
    }


    public function getcontinentusers($searchfield='',$calling='',$limit='20',$offset='0',$dateTill='') {    
        
        $searchstr  =   '';

        $select     =   $this->select();

        if($calling!='')
        {          
            $select     =   $select->from(array( 'tblUsers'),array('Email','Name','lname','UserID'))->where('Status = ?', '1')->where('UserID != ?', '-1'); 
            $select->where('country_name = ?', $searchfield); 
            $select->where("clientID = ?", clientID);
            if($limit !='nolimit') { $select->limit($limit,$offset); }
            //$select->group('countrycode');
        } 
        else
        {
            if($searchfield!='')
            {          
               $select     =   $select->from(array( 'tblUsers'),array('COUNT(Email) as totcountrycode','country_name as countrycode'))->where('Status = ?', '1')->where('UserID != ?', '-1'); 
                $select->where('continent_name = ?', $searchfield); 
                $select->where("clientID = ?", clientID);
                $select->group('country_name');
            } 
            else
            {
                $select     =   $select->from(array( 'tblUsers'),array('COUNT(Email) as totcontinentcode','continent_name as continentcode'))->where('Status = ?', '1')->where('UserID != ?', '-1'); 
                $select->where('continent_name != ?', '');
                $select->where('continent_name != ?', '0');
                $select->where("clientID = ?", clientID);
                if($dateTill !='') {
                    $select->where("DATE_FORMAT(`RegistrationDate`, '%Y-%m-%d') <= ?",$dateTill);
                }
                $select->group('continent_name');
            }
        }    
        //echo $searchfield.$select; exit;
        $result= $this->fetchAll($select)->toarray();
        return $result;     
    }

    public function getPostVisiters($searchfield='',$calling='',$limit='20',$offset='0') 
    {    
        $searchstr  =   '';

        $select     =   $this->select();
       
        if($searchfield!='')
        {          
            $select->from( array('stat' => 'tbldbstats'));
            $select->join( array('u' => 'tblUsers'), 'u.UserID=stat.stats_userid', array('u.ProfilePic','u.Name','u.lname','u.Email','u.UserID') );
            $select->join( array('dbee' => 'tblDbees'),'stat.stats_dbid=dbee.DbeeID',  array('DbeeID'=>'dbee.DbeeID','text' => 'dbee.text','PollText' => 'dbee.PollText','type' => 'dbee.type','dburl' => 'dbee.dburl' ));
            $select->setIntegrityCheck( false );
            $select->where("stat.stats_dbid = ?", $searchfield);
            $select->where("stat.clientID = ?", clientID);
           // $select->group('stats_dbid');
            if($limit !='nolimit') { $select->limit($limit,$offset); }
        } 
       
        //echo $select->__toString();exit;
        $result= $this->fetchAll($select)->toarray();
       // echo "<pre>"; print_r($result);exit;
        return $result; 

 
    }
    public function getTwitterComment($searchfield='',$calling='',$limit='20',$offset='0') 
    {    
        $searchstr  =   '';

        $select     =   $this->select();
        
        $select->from( array('stat' => 'tblDbeeComments'),array('COUNT(CommentID) as totCommnets', 'CommentID','TwitterComment'));
        $select->join( array('u' => 'tblUsers'), 'u.UserID=stat.UserID', array('u.ProfilePic','u.Name','u.lname','u.Email','u.UserID') );
        $select->join( array('dbee' => 'tblDbees'),'stat.DbeeID=dbee.DbeeID',  array('DbeeText'=>'dbee.Text','DbeeID'=>'dbee.DbeeID','dburl' => 'dbee.dburl' ));
        $select->setIntegrityCheck( false );
        $select->where("stat.TwitterComment != ?", '');
        $select->where("stat.clientID = ?", clientID);
        if($searchfield!='')
        { 
            $select->where("stat.DbeeID = ?", $searchfield);
        } 
        else
        {
            $select->order('totCommnets DESC');
            $select->group('stat.DbeeID');
        }
        if($limit !='nolimit') { $select->limit($limit,$offset); }
       
        //echo $select->__toString();exit;
        return $result= $this->fetchAll($select)->toarray();
    }

    public function getOsUsers($searchfield='',$calling='',$limit='20',$offset='0') {    
        
        $searchstr  =   '';
        

        $select     =   $this->select();
       
        if($searchfield!='')
        {          
            $select     =   $select->from(array( 'tblUsers'),array('Email','Name','lname','UserID'))->where('Status = ?', '1')->where('UserID != ?', '-1'); 
            $select->where('os = ?', $searchfield); 
            $select->where('clientID = ?', clientID);
            if($limit !='nolimit') { $select->limit($limit,$offset); }
        } 
        else
        {
            $select     =   $select->from(array( 'tblUsers'),array('COUNT(Email) as totOs','os'))->where('Status = ?', '1')->where('UserID != ?', '-1');
            $select->where('clientID = ?', clientID); 
            $select->where('os != ?', '');
            $select->where('os != ?', '0');
            $select->group('os');
        }
       // echo $select->__toString();
        $result= $this->fetchAll($select)->toarray();
        return $result;     
    }

    public function getdeviceUsers($searchfield='',$calling='',$limit='20',$offset='0') 
    {    
        
        $searchstr  =   '';
        

        $select     =   $this->select();
       
        if($searchfield!='')
        {          
            $select     =   $select->from(array( 'tblUsers'),array('Email','Name','lname','UserID'))->where('Status = ?', '1')->where('UserID != ?', '-1'); 
            $select->where('userdevice = ?', $searchfield); 
            $select->where('clientID = ?', clientID);
            if($limit !='nolimit') { $select->limit($limit,$offset); }
        } 
        else
        {
            $select     =   $select->from(array( 'tblUsers'),array('COUNT(Email) as totOs','userdevice'))->where('Status = ?', '1')->where('UserID != ?', '-1');
            $select->where('clientID = ?', clientID); 
            $select->where('userdevice != ?', '');
            $select->where('userdevice != ?', '0');
            $select->group('userdevice');
        }
       // echo $select->__toString();
        $result= $this->fetchAll($select)->toarray();
        return $result;     
    }

    public function getBrowserUsers($searchfield='',$calling='',$limit='20',$offset='0') {    
        
        $searchstr  =   '';

        $select     =   $this->select();
       
        if($searchfield!='')
        {          
            $select     =   $select->from(array( 'tblUsers'),array('Email','Name','lname','UserID'))->where('Status = ?', '1')->where('UserID != ?', '-1'); 
            $select->where('browser = ?', $searchfield); 
             $select->where('clientID = ?', clientID);
            if($limit !='nolimit') { $select->limit($limit,$offset); }
        } 
        else
        {
            $select     =   $select->from(array( 'tblUsers'),array('COUNT(Email) as totBrowser','browser'))->where('Status = ?', '1')->where('UserID != ?', '-1')->where('clientID = ?', clientID); 
            $select->where('browser != ?', ''); 
            $select->where('browser != ?', '0'); 
            $select->group('browser');

        }
       // echo $select->__toString();
        $result= $this->fetchAll($select)->toarray();
        return $result;     
    }

    public function getReportUsers($searchfield='',$providers='',$calling='',$limit='20',$offset='0') {       
      
        $searchstr  =   '';

        $select     =   $this->select();
        $select     =   $select->from(array( 'tblUsers'),array('Email','Name','lname','IP','UserID'))->where('Status = ?', '1')->where('UserID != ?', '-1');
        $select->where('clientID = ?', clientID);
        if($searchfield!='')
        {   
            if($searchfield!='others' )
            {
            	
                $select =  $select->where(' Email LIKE "%'.$this->myclientdetails->customEncoding($searchfield,'report').'%"');
                if(($calling=='records'))  {  if($limit !='nolimit') { $select->limit($limit,$offset); } }
            } else {
                
                if(is_array($providers))
                {
                    //echo "<pre> ye wala"; print_r($providers);

                    foreach ($providers as $key => $value) {
                       $searchstr .=   'Email NOT LIKE "%'.$this->myclientdetails->customEncoding($value,'report').'%" && ';
                    }
                    $othersearch    =   trim(trim($searchstr),'&&');

                    $select =  $select->where($othersearch);
                    if(($calling=='records'))  {  if($limit !='nolimit') { $select->limit($limit,$offset); } }


                } else {
                   echo $select =  $select->where(' Email NOT LIKE "%'.$this->myclientdetails->customEncoding('gmail').'%" &&  Email NOT LIKE "%'.$this->myclientdetails->customEncoding('yahoo').'%" &&  Email NOT LIKE "%'.$this->myclientdetails->customEncoding('live').'%" &&  Email NOT LIKE "%'.$this->myclientdetails->customEncoding('hotmail').'%" &&   Email NOT LIKE "%'.$this->myclientdetails->customEncoding('msn').'%"');
                     if(($calling=='records'))  {  if($limit !='nolimit') { $select->limit($limit,$offset); } }
                }
            }
        } 

        // echo $select->__toString();
        if(!$calling)
        {
             $result= $this->fetchAll($select)->count();
        } else {
            $result= $this->fetchAll($select)->toarray();
        }
       
             
     

        return $result;     
    }
    
    public function getSocialUsers($searchfield='',$calling='',$limit='20',$offset='0',$dateTill='') {    
        
        $searchstr  =   '';

        $select = $this->_db->select();

        if($calling=='pie')
        {  
            if($searchfield!='')
            {          
                $select     =   $select->from(array( 'tblSocialShare'),array('DISTINCT(user_id)','sharetype','dbeeid','count')); 
                 $select->join( array('dbee' => 'tblDbees'), 'dbee.DbeeID = tblSocialShare.dbeeid',array('dbee.Type','dbee.Text','dbee.LinkTitle','dbee.LinkDesc','dbee.UserLinkDesc','dbee.PicDesc','dbee.VidDesc','dbee.PollText','dbee.dburl'));
                $select->join( array('u' => 'tblUsers'), 'u.UserID = tblSocialShare.user_id',  array('u.ProfilePic','u.Name','u.lname','u.Email','u.UserID'));
                $select->where('sharetype = ?', $searchfield); 
                $select->where('dbee.clientID = ?', clientID);

                if($limit !='nolimit') { $select->limit($limit,$offset); }
            } 
            else
            {
                $select     =   $select->from(array( 'tblSocialShare'),array('SUM(count) as totShare','sharetype','dbeeid','user_id')); 
                $select->where('clientID = ?', clientID);
                if($dateTill !='') {
                    $select->where("DATE_FORMAT(`timestamp`, '%Y-%m-%d') <= ?",$dateTill);
                }

               $select->group('sharetype');
            }
        } 
        else 
        {
            if($searchfield!='' && $searchfield!='sharedpost')
            {          
                $select     =   $select->from(array( 'tblSocialShare'),array('sharetype','dbeeid','user_id')); 
                $select->join( array('dbee' => 'tblDbees'), 'dbee.DbeeID = tblSocialShare.dbeeid',array('dbee.Type','dbee.Text','dbee.LinkTitle','dbee.LinkDesc','dbee.UserLinkDesc','dbee.PicDesc','dbee.VidDesc','dbee.PollText','dbee.dburl'));
                $select->join( array('u' => 'tblUsers'), 'u.UserID = tblSocialShare.user_id',  array('u.ProfilePic','u.Name','u.lname','u.Email','u.UserID'));
                
                $select->where('tblSocialShare.dbeeid = ?', $searchfield); 
                $select->where('tblSocialShare.clientID = ?', clientID);
                if($limit !='nolimit') { $select->limit($limit,$offset); }
            } 
            else if($searchfield=='sharedpost')
            {
                $select     =   $select->from(array( 'tblSocialShare'),array('sharetype','dbeeid','user_id')); 
                $select->join( array('dbee' => 'tblDbees'), 'dbee.DbeeID = tblSocialShare.dbeeid',array('dbee.DbeeID','dbee.Type','dbee.Text','dbee.LinkTitle','dbee.LinkDesc','dbee.UserLinkDesc','dbee.PicDesc','dbee.VidDesc','dbee.PollText','dbee.dburl'));
                $select->join( array('u' => 'tblUsers'), 'u.UserID = tblSocialShare.user_id',  array('u.ProfilePic','u.Name','u.lname','u.Email','u.UserID'));
                
                $select->where('tblSocialShare.clientID = ?', clientID);

                if($limit !='nolimit') { $select->limit($limit,$offset); }

                //echo $select;
            }
            else 
            {
                $select     =   $select->from(array( 'tblSocialShare'),array('COUNT(tblSocialShare.dbeeid) as totdbs','sharetype','dbeeid','user_id')); 
                $select->join( array('dbee' => 'tblDbees'), 'dbee.DbeeID = tblSocialShare.dbeeid',array('dbee.Type','dbee.Text','dbee.LinkTitle','dbee.LinkDesc','dbee.UserLinkDesc','dbee.PicDesc','dbee.VidDesc','dbee.PollText','dbee.dburl'));
                $select->join( array('u' => 'tblUsers'), 'u.UserID = dbee.User',  array('u.ProfilePic','u.Name','u.lname','u.Email','u.UserID'));
                $select->where('tblSocialShare.clientID = ?', clientID);

                if($dateTill !='') {
                    $select->where("DATE_FORMAT(`timestamp`, '%Y-%m-%d') <= ?",$dateTill);
                }

                $select->group('tblSocialShare.dbeeid');
                $dateTill .$select->order('totdbs DESC')->limit(5);
            }
        }
        //echo $select->__toString();//exit;

        $result = $this->_db->fetchAll($select);

        return $result;     
    }

    
    public function getcountrycode() {
    	$db = $this->getDbTable();
    	//$db is an instance of Zend_Db_Adapter_Abstract
    	$select = $db->select();
    	$select->distinct('u.CatName')->from( array('u' => 'tblDbeeCats'), array( 'catid' => 'u.CatID','catname' => 'u.CatName',) );
    	$select->setIntegrityCheck( false );
        $select->where('u.clientID = ?', clientID);
    	$select->order('u.Priority ASC');
    	$result= $db->fetchAll($select);
    	return $result->toarray();
    
    }

    public function getRolesDetails_del($role_id)
    {
        $db = $this->getDbTable();
        $select = $db->select();
        $select->setIntegrityCheck( false );
        $select->from( array('p'=>'permissions'),array('id_role'=>'p.id_role','id_resource'=>'p.id_resource','subresource'=>'p.subresource','permission'=>'p.permission'));
        $select->join( array('r' =>'resources'),'r.res_id = p.id_resource', array('module'=>'r.module','controller'=>'r.controller','action'=>'r.action','res_id'=>'r.res_id'));
        $select->where('p.id_role = ?',$role_id);
        $select->where("r.clientID = ?", clientID);      
        //echo $select->__toString();die;
        $result= $db->fetchAll($select);
        return $result->toarray();
    }

    public function getRolesDetails($role_id)
    {
        $db = $this->getDbTable();
        $select = $db->select();
        $select->setIntegrityCheck( false );
        $select->from( array('p'=>'permissions'),array('id_role'=>'p.id_role','id_resource'=>'p.id_resource','subresource'=>'p.subresource','permission'=>'p.permission'));
        $select->join( array('r' =>'resources'),'r.res_id = p.id_resource', array('module'=>'r.module','controller'=>'r.controller','action'=>'r.action','res_id'=>'r.res_id'));
        if($role_id!=''){$select->where('p.id_role = ?',$role_id);}     
        $select->where("r.clientID = ?", clientID); 
        //echo $select->__toString();//die;     
        return $result= $db->fetchAll($select)->toarray();

    }

    public function insertresource($table,$dataval)
    {
        foreach($dataval as $dataval) {
            if ($this->_db->insert($table,$dataval))
              $lastinsid =  $this->_db->lastInsertId();
        };
        return $lastinsid;
    }

    public function getadminDetails()
    {
        $select = $this->_db->select();
        $select->from(array(
            'res' => 'resources'
        ));
        $select/*->where("g.ID =?", $id)*/->where('res.clientID = ?', clientID);
        return $this->_db->fetchAll($select);

    }

}


