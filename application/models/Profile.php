<?php
class Application_Model_Profile extends Application_Model_DbTable_Master
{
    protected $_name = null;
    protected function _setupTableName()
    {
        parent::_setupTableName();
        $this->_name = $this->getTableName(USERS);
    }

    public function updateusrname($data, $id)
    {
    	//print_r($data); echo $id;exit;
    	return $this->update( $data, "UserID=" . $id );
    	//$this->update($data, 'DbeeID =' . $id);
    }
    public function activeusersondb($dbeerid)
    {
        $dbeerid = (int) $dbeerid;
        $select  = $this->_db->select()->from(array('c' => $this->getTableName(COMMENT)),'')
        ->join(array('u' => $this->getTableName(USERS)), 'u.UserID = c.UserID', array('id'=>'UserID','name'=> 'Name','full_name'=> 'full_name','avatar' =>'ProfilePic','type' => 'Status'))
        ->where("c.DbeeID = ?", $dbeerid)->group("c.UserID");      
        return   $result  = $this->_db->fetchAll($select);
    }

    public function activeuser($dbeerid)
    {
        $dbeerid = (int) $dbeerid;
        $select  = $this->_db->select()->from(array('c' => 'tblDbees'),'')
        ->join(array('u' => $this->getTableName(USERS)), 'u.UserID = c.User', array('id'=>'UserID','name'=> 'Name','full_name'=> 'full_name','avatar' =>'ProfilePic','type' => 'Status'))
        ->where("c.DbeeID = ?", $dbeerid)->group("c.User");         
        return   $result  = $this->_db->fetchAll($select);
    }

    public function getTotalUsers($userid,$dbeeid='',$usertype='')
    {
        $date = date('Y-m-d H:i:s');
        $follwoing =  new Application_Model_Following();
        $data = array();
        $data_final = array();        
        $myclientdetails = new Application_Model_Clientdetails();
        $dbeerid = (int) $dbeeid;
         $extrasql="";
         if($usertype!=100)
          {
             $extrasql=" AND u.usertype!=100 ";
          }
          $sql = "SELECT `u`.`UserID` AS `id`, `u`.`Name` AS `name`, `u`.`full_name`, `u`.`ProfilePic` AS `avatar`, `u`.`Status` AS `type` FROM `tblDbeeComments` AS `c` INNER JOIN `tblUsers` AS `u` ON u.UserID = c.UserID WHERE (u.clientID = '".clientID."') ".$extrasql." AND (c.DbeeID = '".$dbeerid."') GROUP BY `c`.`UserID`
            UNION
            SELECT `u`.`UserID` AS `id`, `u`.`Name` AS `name`, `u`.`full_name`, `u`.`ProfilePic` AS `avatar
            `, `u`.`Status` AS `type` FROM `tblDbees` AS `c` INNER JOIN `tblUsers` AS `u` ON u.UserID = c.User WHERE (u.clientID = '".clientID."') ".$extrasql." AND (c.DbeeID = '".$dbeerid."') GROUP BY `c`.`User`";
        $results=$this->_db->query($sql)->fetchAll();      
        $userarray=array();
        foreach ($results as $value) 
        { 
            if(!in_array($value['id'],$userarray))
            {
                $data['name'] = $myclientdetails->customDecoding(str_replace("'", "", $value['full_name']));
                $data['id'] = $value['id'];
                $data['type'] = $value['type'];
                $data['avatar'] = $value['avatar'];
                $userarray[] = $value['id'];
                $data_final[$value['id']] =  $data;
            }

        }
        return json_encode($data_final);
    }

    public function getTotalUsers2($userid,$usertype='')
    {
        $date = date('Y-m-d H:i:s');
        $follwoing =  new Application_Model_Following();
        $data = array();
        $data_final = array();                
        $myclientdetails = new Application_Model_Clientdetails();
         $dbeerid = (int) $dbeeid;
         $extrasql="";
         if($usertype!=100)
          {
             $extrasql=" AND u.usertype!=100 ";
          }
         $sql = "SELECT DISTINCT `u`.`UserID` AS `id`,`u`.`Name` AS `name`, `u`.`full_name`,`u`.`ProfilePic` AS 
            `avatar`, `u`.`Status` AS `type` FROM `tblFollows` INNER JOIN `tblUsers` AS `u` ON u.UserID = User WHERE (FollowedBy = '".$userid."') ".$extrasql." AND (u.clientID = '".clientID."')
            UNION
             SELECT `u`.`UserID` AS `id`,`u`.`Name` AS `name`, `u`.`full_name`,`u`.`ProfilePic` AS `avatar`, `u`.`Status` AS `type` FROM `tblFollows` AS `f` INNER JOIN `tblUsers` AS `u` ON u.UserID = f.FollowedBy WHERE (f.User = '".$userid."') ".$extrasql." AND (f.clientID = '".clientID."')";
        $results=$this->_db->query($sql)->fetchAll();
        $userarray=array();
        foreach ($results as $value) 
        { 
            if(!in_array($value['id'],$userarray))
            {
                $data['name'] = $myclientdetails->customDecoding(str_replace("'", "", $value['full_name']));
                $data['id'] = $value['id'];
                $data['type'] = $value['type'];
                $data['avatar'] = $value['avatar'];
                $userarray[] = $value['id'];
                $data_final[$value['id']] =  $data;
            }

        }
        return json_encode($data_final);
    }

     public function getTotalUsersPost($param1,$userid,$usertype='')
    {
        $date = date('Y-m-d H:i:s');
        $follwoing =  new Application_Model_Following(); 
        $data = array();
        $data_final = array();
        $extrasql="";
        if($usertype!=100)
         {
            $extrasql=" AND u.usertype!=100 ";
         }
        if($param1==1)
        {
            $follower = $follwoing->getfolloweruserprofile($userid); 
            $folloing =  $follwoing->getfollowing($userid);
            
            $results = array_merge($follower,$folloing);
            $myclientdetails = new Application_Model_Clientdetails();
            foreach ($results as $value) 
            {
                    $data['name'] = $myclientdetails->customDecoding(str_replace("'", "", $value['full_name']));
                    $data['id'] = $value['id'];
                    $data['type'] = '8';
                    $data['avatar'] = $value['avatar'];
                    $data_final[$value['id']] =  $data; 
            }
        }
        else if($param1==2)
        {

            $eventModel = new Application_Model_Event();
            $results = $eventModel->getAllEventMember($userid);
            //print($results);
            $myclientdetails = new Application_Model_Clientdetails();
            foreach ($results as $value) 
            {
                    $data['name'] = $myclientdetails->customDecoding(str_replace("'", "", $value['full_name']));
                    $data['id'] = $value['id'];
                    $data['type'] = '8';
                    $data['avatar'] = $value['ProfilePic'];
                    $data_final[$value['id']] =  $data; 
            } 

        }
        else if($param1==3)
        {

            $grpModel = new Application_Model_Groups();
            $results = $grpModel->getAllGroupMember($userid);
           
            $myclientdetails = new Application_Model_Clientdetails();
            foreach ($results as $value) 
            {
                    $data['name'] = $myclientdetails->customDecoding(str_replace("'", "", $value['full_name']));
                    $data['id'] = $value['id'];
                    $data['type'] = '8';
                    $data['avatar'] = $value['ProfilePic'];
                    $data_final[$value['id']] =  $data; 
            } 

        }
        return json_encode($data_final);
    }


    public function totalikesdbee($userid, $score, $dbeeid)
    {
        $select = $this->_db->select()->from($this->getTableName(SCORING))
        //->where("Owner = ?",$userid)
            ->where("Score = ?", $score)
            ->where("Type = ?",1)
        	->where("clientID = ?",clientID)
            ->where("MainDB = ?", $dbeeid);
        return count($this->_db->fetchAll($select));
    }
    
    public function totalikesdbeecmm($ID, $score, $dbeeid)
    {
        $select = $this->_db->select()->from($this->getTableName(SCORING))->where("ID = ?", $ID)->where("Score = ?", $score)->where("Type = ?", 2)->where("MainDB = ?", $dbeeid)->where("clientID = ?", clientID);
        //echo $select->__toString();die('in');
        return count($this->_db->fetchAll($select));
      
    }
    
    public function totalikesprofile($userid, $score)
    {
        $select = $this->_db->select()->from($this->getTableName(SCORING))->where("Owner = ?", $userid)->where("Score = ?", $score)->where("clientID = ?",clientID);
        //->where("Type = ?",1);
        return count($this->_db->fetchAll($select));
      
    }
    
    public function getbiographi($userid)
    {
        $select = $this->_db->select();
        $select->from($this->getTableName(USER_BIOGRAPHY));
        $select->where("UserID =?", $userid)->where("clientID = ?",clientID);
        return $this->_db->fetchRow($select);
      
    }
    
    public function getuserbyprofileid($userid)
    {
        $select = $this->_db->select()->from($this->_name, array(
            'UserID',
            'Name',
            'lname',
			'Username',
            'usertype',
            'ProfilePic',
            'Status',
            'Birthdate'
        ))->where("UserID =?", $userid)->where("clientID = ?",clientID);
        return $this->_db->fetchRow($select);
       
    }
    public function getfollow($userid)
    {
        $select = $this->_db->select()->from($this->_name, array(
            'UserID',
            'Name',
            'ProfilePic',
            'Status'
        ))->where("UserID =?", $userid)->where("clientID = ?",clientID);
        return $this->_db->fetchRow($select);
      
    }
    
    public function getfollowing($userid)
    {
        $select = $this->_db->select()->from($this->_name, array(
            'UserID',
            'Name',
            'ProfilePic',
            'Status'
        ))->where("User =?", $userid)->where("clientID = ?",clientID);
        return $this->_db->fetchRow($select);
        
    }
    public function getfollowinglimit($userid)
    {
        $select = $this->_db->select()->from($this->_name, array(
            'UserID',
            'Name',
            'ProfilePic',
            'Status'
        ))->where("User =?", $userid)->where("clientID = ?",clientID)->limit(19);
        return $this->_db->fetchRow($select);
       
    }
    
    function checkMutualScore($score, $user, $cookieuser)
    {
        
        $select = $this->_db->select()->from(array(
            'c' => $this->getTableName(SCORING)
        ))->where("Score = ?", $score)->where("Owner = ?", $user)->where("UserID = ?", $cookieuser)->where("clientID = ?",clientID);
        $result = $this->_db->fetchAll($select);
        if (count($result) > 0) {
            $TotalGivenScore = count($result);
        } else
            $TotalGivenScore = 0;
        
        
        $select2 = $this->_db->select()->from(array(
            'c' => $this->getTableName(SCORING)
        ))->where("Score =?", $score)->where("Owner =?", $cookieuser)->where("UserID =?", $user);
        $result2 = $this->_db->fetchAll($select2);
        if (count($result2) > 0) {
            $TotalRcvdScore = count($result2);
        } else
            $TotalRcvdScore = 0;
        return $TotalRcvdScore . '~' . $TotalGivenScore;
    }
    
    function totalikes($user, $score)
    {
        
        $sql    = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(array(
            't' => $this->getTableName(USER_BIOGRAPHY)
        ));
        $select->join(array(
            'c' => $this->getTableName(SCORING)
        ), 't.UserID = c.UserID');
        $select->where(array(
            'c.Score = ' . $score
        ));
        $statement = $sql->prepareStatementForSqlObject($select);
        $results   = $statement->execute();
        $resultSet = $this->resultSetPrototype->initialize($results); // here you put Sql Object into ResultSet Object
        return count($resultSet);
        
        $select = $this->_db->select()->from(array(
            't' => $this->getTableName(USER_BIOGRAPHY)
        ))->joinLeft(array(
            'c' => $this->getTableName(SCORING)
        ), 't.UserID = c.UserID')->where("Score = ?", $score)->where("c.clientID = ?",clientID);
        $result = $this->_db->fetchAll($select);
        if (count($result) > 0) {
            $TotalGivenScore = count($result);
        } else
            $TotalGivenScore = 0;
        
    }
    
    function checkscore($score, $user)
    {
        
        $select = $this->_db->select()->from(array(
            'c' => $this->getTableName(SCORING)
        ))->where("Score = ?", $score)->where("Owner = ?", $user)->where("clientID = ?",clientID);
        //->where("Type = ?",1);
        //->where("UserID = ?",$user)
        $result = $this->_db->fetchAll($select);
        if (count($result) > 0) {
            $TotalScore = count($result);
        } else
            $TotalScore = 0;
        
        $Position = 1;
        
        $select2 = $this->_db->select()->from(array(
            'c' => $this->getTableName(SCORING)
        ), array(
            'Owner',
            'Score',
            "cnt" => "count(*)"
        ))->where("Score =?", $score)->where("UserID !=?", 'Owner')->order('cnt');
        
        $result2 = $this->_db->fetchAll($select2);
        if (count($result2) > 0) {
            $userFound = false;
            foreach ($result2 as $Row):
                if ($Row['Owner'] == $user) {
                    $userFound = true;
                    break;
                } else
                    $Position++;
            endforeach;
        }
        
        else
            $Position = '--';
        if (!$userFound)
            $Position = '--';
        
        if ($Position == 1)
            $Position .= 'st';
        elseif ($Position == 2)
            $Position .= 'nd';
        elseif ($Position == 3)
            $Position .= 'rd';
        else {
            if ($Position != '--')
                $Position .= 'th';
        }
        
        
        return $TotalScore . '~' . $Position;
    }
    
    function checkLeague($score1, $score2, $user, $league)
    {
        $LeagueScore = 0;
        $select      = $this->_db->select()->from(array(
            'c' => $this->getTableName(SCORING)
        ))->where("Score = ?", $score1)->where("Owner = ?", $user)->where("UserID = ?", $user)->group('Score')->where("clientID = ?",clientID);
        $result      = $this->_db->fetchAll($select);
        
        if (count($result) > 0) {
            $TotalScore1 = count($result);
        } else
            $TotalScore1 = 0;
        
        if ($score2 != 0) {
            
            $select2 = $this->_db->select()->from(array(
                'c' => $this->getTableName(SCORING)
            ))->where("Score =?", $score2)->where("Owner =?", $user)->where("UserID =?", $user)->group('Score')->where("clientID = ?",clientID);
            $result2 = $this->_db->fetchAll($select2);
            if (count($result2) > 0) {
                $TotalScore2 = count($result2);
            } else
                $TotalScore2 = 0;
            
            
            $LeagueScore = $TotalScore1 + ($TotalScore2 * 5);
        } else
            $LeagueScore = $TotalScore1;
        
        $LeagueArr = $this->leagueTable($league, $user);
        for ($k = 0; $k < count($LeagueArr); $k++) {
            if ($k != count($LeagueArr) - 1) {
                if ($LeagueArr[$k]['ID'] == $user)
                    $Position = $k + 1;
            }
        }
        
        if ($Position != '') {
            $Position = $this->ordinal($Position);
            
        } else
            $Position = '--';
        
        return $LeagueScore . '~' . $Position;
    }
    
    
    
    function leagueTable($league, $user = 0, $start = 0, $commusers = 0, $db = 0)
    {
        
        $Arr1 = array();
        $Arr2 = array();
        $Arr3 = array();
        
        //-------------------------------------------
        // CALCULATE LEAGUE POINTS AND ARRANGE USERS
        if ($league == 'love') {
            $score1 = '1';
            $score2 = '2';
        } elseif ($league == 'rogue') {
            $score1 = '5';
            $score2 = '4';
        } elseif ($league == 'philosopher') {
            $score1 = '0';
            $score2 = '3';
        }
        $Res = $this->getleagutablescore($score1, $db, $commusers);
        if (count($Res) > 0) {
            $totallike = 0;
            foreach ($Res as $Row) {
                $Arr1[$Row['Owner']]['ID']         = $Row['Owner'];
                $Arr1[$Row['Owner']]['Name']       = $Row['Name'];
                $Arr1[$Row['Owner']]['ProfilePic'] = $Row['ProfilePic'];
                $Arr1[$Row['Owner']]['Score']      = $Row['TotalScore'];
                $Arr1[$Row['Owner']]['Username']       = $Row['Username'];
                $IDArr1[$totallike]                = $Row['Owner'];
                $totallike++;
            }
        }
        
        $Res = $this->getleagutablescore($score2, $db, $commusers);
        
        if (count($Res) > 0) {
            $totallove = 0;
            foreach ($Res as $Row) {
                $Arr2[$Row['Owner']]['ID']         = $Row['Owner'];
                $Arr2[$Row['Owner']]['Name']       = $Row['Name'];
                $Arr2[$Row['Owner']]['ProfilePic'] = $Row['ProfilePic'];
                $Arr2[$Row['Owner']]['Score']      = $Row['TotalScore'];
                $Arr2[$Row['Owner']]['Username']   = $Row['Username'];
                $IDArr2[$totallove]                = $Row['Owner'];
                $totallove++;
            }
        }
        
        if ($totallike > $totallove) {
            $formax  = $totallike;
            $useName = $Arr1;
            $useID   = $IDArr1;
        } else {
            $formax  = $totallove;
            $useName = $Arr2;
            $useID   = $IDArr2;
        }
        
        for ($i = 0; $i < $formax; $i++) {
            $Arr3[$i]['ID']         = $useID[$i];
            $Arr3[$i]['Name']       = $useName[$useID[$i]]['Name'];
            $Arr3[$i]['Username']       = $useName[$useID[$i]]['Username'];
            $Arr3[$i]['ProfilePic'] = $useName[$useID[$i]]['ProfilePic'];
            $Arr3[$i]['Score']      = $Arr1[$useID[$i]]['Score'] + $Arr2[$useID[$i]]['Score'];
        }
        
        
        $pos = 0;
        if ($user != '0') {
            for ($k = 0; $k < count($Arr3); $k++) {
                if ($Arr3[$k]['ID'] == $user) {
                    $pos = $k + 1;
                    break;
                }
            }
        }
        
        $i++;
        return $Arr3[$i]['UserPos'] = $pos;
       
    }
    
    function ordinal($cdnl)
    {
        $test_c = abs($cdnl) % 10;
        $ext    = ((abs($cdnl) % 100 < 21 && abs($cdnl) % 100 > 4) ? 'th' : (($test_c < 4) ? ($test_c < 3) ? ($test_c < 2) ? ($test_c < 1) ? 'th' : 'st' : 'nd' : 'rd' : 'th'));
        return $cdnl . $ext;
    }
    
    function leagueMostfollowed($user)
    {
        $found   = false;
        $counter = 1;
        $select  = $this->_db->select()->from(array(
            'f' => $this->getTableName(FOLLOWS)
        ), array(
            'f.User',
            "Total" => "count(*)"
        ))->join(array(
            'u' => $this->getTableName(USERS)
        ), 'f.User=u.UserID', array(
            'u.ProfilePic',
            'u.Name'
        ))->where("Score =?", $score2)->where("u.clientID = ?",clientID);
        $select->order('cnt')->group('f.User')->order('Total DESC');
        $result = $this->_db->fetchAll($select);
        foreach ($result as $Row):
            if ($Row['User'] == $user) {
                $found     = true;
                $Position  = $counter;
                $Followers = $Row['Total'];
                break;
            }
            $counter++;
        endforeach;
        
        if (!$found) {
            $Position  = '--';
            $Followers = '0';
        }
        
        if ($Position != '--')
            return $this->ordinal($Position) . '~' . $Followers;
        else
            return $Position . '~' . $Followers;
    }
    
    public function changepics($userid, $pics)
    {
        $data = array(
            'ProfilePic' => $pics
        );
        $this->update($data, array('UserID = ' . $userid,'clientID = ' . clientID));
        return true;
        
    }
    public function getmydbscore($dbeeid, $loginid, $topscore='')
    {
       
        $select = $this->_db->select()->from($this->getTableName(SCORING))->where("UserID = ?", $loginid)->where("MainDB = ?", $dbeeid); 
        if($topscore!= '') $select = $select->where("Type = ?", '1')->where("clientID = ?",clientID);

        return $this->_db->fetchRow($select);
  
    }
    
    public function getleagutablescore($score1, $db, $commusers)
    {
        if($score1=='1' || $score1=='5') {
			$SQL = "SELECT COUNT(ScoreID)*5 AS TotalScore,Owner,Name,Username,ProfilePic from tblScoring,tblUsers WHERE tblScoring.Owner=tblUsers.UserID AND tblUsers.ScoringStatus=1 AND tblUsers.Status=1 AND Score=" . $score1;
		}
        else {
			$SQL = "SELECT COUNT(ScoreID) AS TotalScore,Owner,Name,Username,ProfilePic from tblScoring,tblUsers WHERE tblScoring.Owner=tblUsers.UserID AND tblUsers.ScoringStatus=1 AND tblUsers.Status=1 AND Score=" . $score1;
		}
        if ($commusers != 0)
            $SQL .= " AND tblScoring.MainDB=" . $db . " AND tblUsers.UserID IN (" . $commusers . ")";
        $SQL .= " GROUP BY Owner ORDER BY TotalScore DESC";
    
        return $this->_db->query($SQL)->fetchAll();
       
    }

     public function userbasedondistance($lat='28.4667',$long='77.033302',$uid)
    {       
       $distancequery = "((ACOS(SIN(".$lat." * PI() / 180) * SIN(REPLACE(latitude, 'mabirdnny#', '') * PI() / 180) + COS(".$lat." * PI() / 180) * COS(REPLACE(latitude, 'mabirdnny#', '') * PI() / 180) * COS((".$long." - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515)*1.609344";

       $SQL = "SELECT IFNULL(".$distancequery.",20000) as 'distance',usertype as total,Username,Name,full_name,title,company,ProfilePic,lname,UserID as userid FROM `tblUsers` WHERE clientID=".clientID." AND `UserID` !=".$uid." HAVING distance<='10' ORDER BY distance ASC limit 5";

       return $this->_db->query($SQL)->fetchAll();       
    }

   

    public function dbcatposted($uid,$catid)
    {
        $select = $this->_db->select()->from('tblDbees', array(
                'COUNT( * ) AS total',
                'FIND_IN_SET( '.$catid.', `Cats` ) as isoccur'               
        ))->where("clientID = ?",clientID)->where("User =?", $uid)->where("FIND_IN_SET( ".$catid." , `Cats` ) >?", 0);        
        return $this->_db->fetchRow($select);    
    }
    
    public function chkvip($userid,$cockie)
    {
    	/* $select = $this->_db->select()->from($this->_name, array(
    			'UserID',
    			'Name',
    			'Username',
    			'ProfilePic',
    			'Status',
    			'Birthdate'
    	))->where("UserID =?", $userid);
    	return $this->_db->fetchRow($select); */
    	return 1;
    
    }
    public function userbiographi($userid)
    {
    	$select = $this->_db->select()->from('tblUserBiography', array(
    			'field_id',
    			'field_value'   			
    	))->where("UserID =?", $userid)->where("clientID = ?",clientID);
    	return $this->_db->fetchRow($select);
    
    }
    
}
