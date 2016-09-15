<?php

class Application_Model_Expert extends Application_Model_DbTable_Master
{
    
    protected $_name = null;
    protected function _setupTableName()
    {
        parent::_setupTableName();   
        $this->_name = $this->getTableName(QNA);
    }
    public function insertdbeeqna($data)
    {
       $this->_db->insert($this->_name, $data);
       return $this->_db->lastInsertId();   
    }
    public function updatejoinrequser($data,$userid,$dbeeID)
    {

        $this->_db->update('tblDbeeJoinedUser', $data ,array(
            "userID='" . $userid . "'","dbeeID='" . $dbeeID . "'","clientID='" . clientID . "'"));
    }
    public function updateAnswerStatus($id,$user_id)
    {
        $where = array(
                'id = ?' => $id,
                'expert_id=?'=>$user_id,
                'clientID' => clientID,
                'timestamp'=>date('Y-m-d H:i:s')
        );
        $data = array(
                'reply' => 1
        );
        if($this->_db->update($this->_name, $data, $where))
            return true;
        else
            return false;
    }
    public function updateQaStatus($id,$type)
    {
        switch ($type) 
        {
            case 'verified':
               $status = 5;
                break;
            case 'removed':
               $status = 4;
                break;
            case 'other':
               $status = 3;
                break;
            case 'answeredbyexpert':
               $status = 6;
                break;
        }
        $where = array(
                'id = ?' => $id,
                'clientID' => clientID
        );
        $data = array(
                'status' => $status, // verified
                'timestamp'=>date('Y-m-d H:i:s')
        );
        if($this->_db->update($this->_name, $data, $where))
            return true;
        else
            return false;
    }
    
     public function updateQuestionsStatus($id,$user_id)
    {

        $where = array(
                'parentid = ?' => $id,
                'expert_id=?'=>$user_id,
                'clientID' => clientID
        );
        $data = array(
                'reply' => 1
        );
        if($this->_db->update($this->_name, $data, $where))
            return true;
        else
            return false;
    }

    public function moveToLiveFeed($id)
    {

        $where = array(
                'parentid = ?' => $id,
                'clientID' => clientID
        );
        $data = array(
                'makelive' => 1,'movetolive'=>1
        );
        $this->_db->update($this->_name, $data, $where);
        $this->moveToLiveFeed2($id);
    }

    public function moveToLiveFeed2($id)
    {

        $where = array(
                'id = ?' => $id,
                'clientID' => clientID
        );
        $data = array(
                'makelive' => 1
        );
        $this->_db->update($this->_name, $data, $where);
    }

    public function UndoQaStatus($id,$user_id)
    {
        $where = array(
                'id = ?' => $id,
                'expert_id=?'=>$user_id,
                'clientID' => clientID
        );
        $data = array(
            'reply' => 0
        );
        if($this->_db->update($this->_name, $data, $where))
            return true;
        else
            return false;
    } 
    public function checkAnswer($parentid)
    {
        $select = $this->_db->select()
            ->from(array('c' => $this->_name),array('c.*'))
                ->join(array('d' => $this->getTableName(USERS)),'c.user_id = d.UserID', array('Username','UserID','Name','lname','ProfilePic','Status'))
                    ->where("c.parentid = ?",$parentid)->where("c.clientID = ?",clientID);
        return $this->_db->fetchRow($select);
    }

    public function QuestionDeleted($commentid)
    {
       if($this->_db->delete($this->_name, array(
        'id = ?' => $commentid,
        'clientID = ?' => clientID
        )))
            return true;
        else
            return false;
    } 

    public function deleteAnswer($userid,$answer_id)
    {
        if($this->_db->delete($this->_name, array(
        'user_id = ?' => $userid,
        'id = ?' => $answer_id,
        'clientID = ?' => clientID
        )))
            return true;
        else
            return false;
    }
    // remove question after expert removed
    public function deleteQuestion($userid,$dbeeid)
    {
        if($this->_db->delete($this->_name, array(
            'expert_id = ?' => $userid,
            'reply = ?' => 0,
            'dbeeid' => $dbeeid,
            'clientID = ?' => clientID
        )))
            return true;
        else
            return false;
    }

    public function MyQuestion($myid,$limittype)
    {
         $select  = $this->_db->select()->from(array(
            'E' => $this->_name
        ), array(
            'E.*'
        ))->joinInner(array(
            'd' => 'tblDbees'
        ), 'd.DbeeID = E.dbeeid', array(
            'd.dburl'
        ));
        $select->where('E.clientID = ?', clientID);
        $select->where('E.user_id = ?', $myid);
        $select->where('E.parentid = ?', 0)->order(array(
            'E' => 'timestamp DESC'
        ));
        if($limittype=="limit")
        {
         $select->limit(5); 
        }
        // /echo $select->__toString();    die;      
        return $this->_db->fetchAll($select);
    }

    public function PendingQue($myid,$limittype,$owner=0,$expert=0)
    {
         $select  = $this->_db->select()->from(array(
            'E' => $this->_name
        ), array(
            'E.*'
        ))->joinInner(array(
            'd' => 'tblDbees'
        ), 'd.DbeeID = E.dbeeid', array(
            'd.dburl'
        ));
        $select->where('E.clientID = ?', clientID);
       
        if($owner > 0 && $expert == 0) 
        {
          $select->where('E.dbowner = ?', $myid); 
          $select->where('E.status=2 OR E.status=9');
          $select->where('E.removed=0');
        }
        else if ($expert > 0 && $owner == 0) {
           $select->where('E.expert_id = ?', $myid); 
           $select->where('E.status=3 OR E.status=5 OR E.status=7'); 
        }  
        else if ($expert > 0 && $owner > 0) {
           $select->where('E.expert_id='.$myid .' OR E.dbowner='.$myid);
           $select->where('E.status=2 OR E.status=9 OR E.status=3 OR E.status=5 OR E.status=7'); 
        } 
        $select->where('E.reply = ?', 0);
        $select->where('E.parentid = ?', 0)->order(array(
            'E' => 'timestamp DESC'
        ));

        if($limittype=="limit")
        {
         $select->limit(5); 
        } 
        //echo $select->__toString();    die;      
        return $this->_db->fetchAll($select);
    }

     public function getAllExpertQuestion($dbeeid, $page,$expert='')
    {
        $skip = ($page-1)*PAGE_NUM;
        $select  = $this->_db->select()->from(array(
            'E' => $this->_name
        ), array(
            'E.*'
        ))->joinInner(array(
            'u' => $this->getTableName(USERS)
        ), 'u.UserID = E.user_id', array(
            'u.UserID',
            'u.Username',
            'u.Name',
            'u.lname',
            'u.ProfilePic',
            'u.Status'
        ));
        $select->where("E.dbeeid= ?", $dbeeid);
        $select->where("u.Status= ?", 1);
        $select->where("u.clientID= ?", clientID);
        $select->where("E.reply= ?", 1);
        $select->where("E.parentid!= ?", '');
        if($expert!='')
            $select->where("E.expert_id= ?", $expert);
        $select->where("E.status= ?", 1)->order(array(
            'E' => 'timestamp DESC'
        ))->limit(PAGE_NUM, $skip);
        //echo $select->__toString();
        return $this->_db->fetchAll($select);  
    }


    public function getLiveExpertQuestion($dbeeid, $page)
    {
        $skip = ($page-1)*PAGE_NUM;
        $select  = $this->_db->select()->from(array(
            'E' => $this->_name
        ), array(
            'E.*'
        ))->joinInner(array(
            'u' => $this->getTableName(USERS)
        ), 'u.UserID = E.user_id', array(
            'u.UserID',
            'u.Username',
            'u.Name',
            'u.lname',
            'u.ProfilePic',
            'u.Status'
        ));
        $select->where("E.dbeeid= ?", $dbeeid);
        $select->where("u.Status= ?", 1);
        $select->where("u.clientID= ?", clientID);
        $select->where("E.makelive= ?", 1);
        $select->where("E.parentid!= ?", '');
        $select->where("E.status= ?", 1)->order(array(
            'E' => 'timestamp DESC'
        ))->limit(PAGE_NUM, $skip);
        return $this->_db->fetchAll($select);  
    }

    public function getPushQuestion($dbeeid, $qaid)
    {
        $select  = $this->_db->select()->from(array(
            'E' => $this->_name
        ), array(
            'E.*'
        ))->joinInner(array(
            'u' => $this->getTableName(USERS)
        ), 'u.UserID = E.user_id', array(
            'u.UserID',
            'u.Username',
            'u.Name',
            'u.lname',
            'u.ProfilePic',
            'u.Status'
        ));
        $select->where("E.dbeeid= ?", $dbeeid);
        $select->where("u.Status= ?", 1);
        $select->where("u.clientID= ?", clientID);
        $select->where("E.reply= ?", 1);
        $select->where("E.parentid= ?", $qaid);
        $select->where("E.status= ?", 1)->order(array(
            'E' => 'timestamp DESC'
        ))->limit(1);
        return $this->_db->fetchAll($select);  
    }

    public function MoveToCommentStatus($id)
    {

        $where = array(
                'parentid = ?' => $id,
                'clientID = ?' => clientID
        );
        $data = array(
                'movetocomment' => 1
        );
        if($this->_db->update($this->_name, $data, $where))
            return true;
        else
            return false;
    }

    public function getQuestionDetails($parentid)
    {
        $select  = $this->_db->select()->from(array(
            'E' => $this->_name
        ), array(
            'E.*'
        ));
        $select->where("E.id= ?", $parentid);
        $select->where("E.clientID= ?", clientID);
        return $this->_db->fetchRow($select);
    }

    public function getAnswerDetails($answerid)
    {        
        $select  = $this->_db->select()->from(array(
            'E' => $this->_name
        ), array(
            'E.*'
        ));
        $select->where("E.parentid= ?", $answerid);
        $select->where("E.status= ?", '1');
        $select->where("E.clientID= ?", clientID);
        return $this->_db->fetchRow($select);
    }

    public function getMyQuestion($dbeeid, $user_id, $page)
    {
        $skip = ($page-1)*PAGE_NUM;
        $select  = $this->_db->select()->from(array(
            'E' => $this->_name
        ), array(
            'E.*'
        ))->joinInner(array(
            'u' => $this->getTableName(USERS)
        ), 'u.UserID = E.user_id', array(
            'u.UserID',
            'u.Username',
            'u.Name',
            'u.lname',
            'u.ProfilePic',
            'u.Status'
        ));
        
        if($user_id!=0)
        {
            $select->where("E.user_id= ?", $user_id);
            $select->where("E.parentid= ?", '0');
        }

        if($dbeeid)
            $select->where("E.dbeeid= ?", $dbeeid);
        
        $select->where("u.Status= ?", 1);
        $select->where("u.clientID= ?", clientID)->order(array(
            'E' => 'timestamp DESC'
        ))->limit(PAGE_NUM, $skip);
        //echo $select->__toString();
        return $this->_db->fetchAll($select);   
    }

    public function getPendingQuestion($dbeeid, $user_id, $page,$type='')
    {
        $skip = ($page-1)*PAGE_NUM;
        $select  = $this->_db->select()->from(array(
            'E' => $this->_name
        ), array(
            'E.*'
        ))->joinInner(array(
            'u' => $this->getTableName(USERS)
        ), 'u.UserID = E.user_id', array(
            'u.UserID',
            'u.Username',
            'u.Name',
            'u.lname',
            'u.ProfilePic',
            'u.Status'
        ));
        if($user_id!=0)
            $select->where("E.expert_id = $user_id");
        
        $select->where("E.removed= ?", 0);

        if($dbeeid)
            $select->where("E.dbeeid= ?", $dbeeid);
        else
            $select->where("E.dbeeid= ?", 0);

        $select->where("u.Status= ?", 1);
        $select->where("u.clientID= ?", clientID);
        $select->where("E.reply= ?", '0');
        $select->where("E.parentid= ?", '0');

        if($type=='ownerISexpert')
            $select->where("E.status= 2 OR E.status= 9");
        else if($type=='expertISNOTowner')
            $select->where("E.status= 5 OR E.status= 3 OR E.status= 7");
        else 
            $select->where("E.status!= 4 AND E.status!= 7 AND E.status!= 9");

        $select->order(array(
            'E' => 'timestamp DESC'
        ))->limit(PAGE_NUM, $skip);
        //echo $select->__toString();
        return $this->_db->fetchAll($select);
    }

    public function getPomotedQuestion($user_id,$page,$id='')
    {
        $skip = ($page-1)*PAGE_NUM;
        $select  = $this->_db->select()->from(array(
            'E' => $this->_name
        ), array(
            'E.*'
        ))->joinInner(array(
            'u' => $this->getTableName(USERS)
        ), 'u.UserID = E.user_id', array(
            'u.UserID',
            'u.Username',
            'u.Name',
            'u.lname',
            'u.ProfilePic',
            'u.Status'
        ));
        
        $select->where("E.dbeeid= ?", 0);
        
        if($user_id!=0)
            $select->where("(E.expert_id = $user_id OR E.user_id = $user_id)");
        
        $select->where("u.Status= ?", 1);
        $select->where("u.clientID= ?", clientID);
        $select->where("E.parentid= ?", '0');
        $select->where("E.status= 11");

        if($page!='')
        {
            $select->order(array(
                'E' => 'timestamp DESC'
            ))->limit(PAGE_NUM, $skip);
        }else{
            $select->where("E.id= ?", $id)->limit(1);;
        }
        //echo $select->__toString();
        return $this->_db->fetchAll($select);
    }

    public function getSingleQuestion($qaid)
    {
        $select  = $this->_db->select()->from(array(
            'E' => $this->_name
        ), array(
            'E.*'
        ))->joinInner(array(
            'u' => $this->getTableName(USERS)
        ), 'u.UserID = E.user_id', array(
            'u.UserID',
            'u.Username',
            'u.Name',
            'u.lname',
            'u.ProfilePic',
            'u.Status'
        ));
        $select->where("u.clientID= ?", clientID)
        ->where("E.id= ?", $qaid)->limit(1);
        return $this->_db->fetchAll($select);
    }

  
     public function getQuestionUser($dbeeid)
    {
        $select  = $this->_db->select()
        ->distinct()
        ->from(array(
            'E' => $this->_name
        ), array(
            'E.user_id'
        ));
        if($dbeeid)
            $select->where("E.dbeeid= ?", $dbeeid);
        $select->where("E.status= ?", '1')->where("E.clientID= ?", clientID);
        return $this->_db->fetchAll($select);
    }
    
    public function insertUserJoinedForSpecialDbee($data)
    {
           if ($this->_db->insert($this->getTableName(DbeeJoinedUser), $data))
                return $this->_db->lastInsertId();
            else
                return false;
    }

    public function getallanswersfromcommentid($parentid)
    {
        $dbeerid  = (int) $dbeerid;
        $commentid = 5;
        $select = $this->_db->select()
            ->from(array('c' => $this->_name),array('c.*'))
                ->join(array('d' => $this->getTableName(USERS)),'c.user_id = d.UserID', array('Username','UserID','Name','lname','ProfilePic','Status'))
                    ->where("c.id = ?",$parentid)->where("c.clientID = ?",clientID);

        $row_comments = $this->_db->fetchAll($select);
        if(empty($row_comments[0]))
            return;
        $return = '';
        $this->myclientdetails = new Application_Model_Clientdetails();
        
        if($row_comments[0]['Status']==1)
            $cmntprofileLinkStart='<a href="/user/'.$this->myclientdetails->customDecoding($row_comments[0]['Username']).'" class="cmntuserLink" >';
        else $cmntprofileLinkStart='<a href="javascript:void(0)" class="profile-deactivated" title="'.DEACTIVE_ALT.'" onclick="return false;">';
        $this->dbeeCommentobj = new Application_Model_Comment();

        $return .='<div id="" class="" style="background:#fff">           
            <div id="dbcomment-speechwrapper" class="dbcomment-speechwrapper">
                <div class="dbcmntspeech">                 
                        <div class="inReplyWrapper" id="expertRepWrapper">
                            <div class="inreplyPhoto">'.$cmntprofileLinkStart.'<img src="'.IMGPATH.'/users/small/'.$row_comments[0]['ProfilePic'].'" width="30" height="30" border="0" /></a></div>
                            <div class="inReplyContent"><div class="rptoTitle"> Question:</div>'.$cmntprofileLinkStart.'<span>'.$this->myclientdetails->customDecoding($row_comments[0]['Name']).' '.$this->myclientdetails->customDecoding($row_comments[0]['lname']).'</span></a>';
                            $return .='&nbsp;<span >'.$this->dbeeCommentobj->convert_clickable_links($row_comments[0]['qna']);
                          return  $return .='</span></div></div></div></div></div>';
    }

    public function getallanswersfromcommentidQA($parentid,$expertText)
    {
        $dbeerid  = (int) $dbeerid;
        $commentid = 5;
        $select = $this->_db->select()
            ->from(array('c' => $this->_name),array('c.*'))
                ->join(array('d' => $this->getTableName(USERS)),'c.user_id = d.UserID', array('Username','UserID','Name','lname','ProfilePic','Status'))
                    ->where("c.parentid = ?",$parentid)->where("c.clientID = ?",clientID);
        $row_comments = $this->_db->fetchAll($select);
        if(empty($row_comments[0]))
            return;
        $return = '';
        $this->myclientdetails = new Application_Model_Clientdetails();
        $this->dbeeCommentobj = new Application_Model_Comment();
        if($row_comments[0]['Status']==1)
            $cmntprofileLinkStart='<a href="/user/'.$this->myclientdetails->customDecoding($row_comments[0]['Username']).'" class="cmntuserLink" >';
        else $cmntprofileLinkStart='<a href="javascript:void(0)" class="profile-deactivated" title="'.DEACTIVE_ALT.'" onclick="return false;">';
        $return .='<div id="" class="" style="background:#fff">           
            <div id="dbcomment-speechwrapper" class="dbcomment-speechwrapper">
                <div class="dbcmntspeech">                 
                        <div class="inReplyWrapper" id="expertRepWrapper">
                            <div class="rptoTitle">Answer:</div>
                            <div class="inreplyPhoto">'.$cmntprofileLinkStart.'<img src="'.IMGPATH.'/users/small/'.$row_comments[0]['ProfilePic'].'" width="30" height="30" border="0" /></a></div>
                            <div class="inReplyContent">'.$cmntprofileLinkStart.'<span>'.$this->myclientdetails->customDecoding($row_comments[0]['Name']).' '.$this->myclientdetails->customDecoding($row_comments[0]['lname']).'</span></a>';
                            $return .='&nbsp;<span >'.$this->dbeeCommentobj->convert_clickable_links($row_comments[0]['qna']);
                          return  $return .='</span></div></div></div></div></div>';
    }

    public function checkExpertStatus($dbeeid)
    {
        $select = $this->_db->select()->from($this->getTableName(EXPERT))->where("dbid = ?", $dbeeid)->where("clientID = ?", clientID);
        $result = $this->_db->fetchRow($select);
        if (!empty($result))
            return '1';
        else
            return '0';
    }
     public function delinviteexpert($data,$dbeeid)
    {
        if ($this->_db->update($this->getTableName(INVITEXPORT), $data ,array(
            "dbeeid='" . $dbeeid . "'","clientID='" . clientID . "'"
        )))
            return true;
        else
            return false;   
    }
    public function insertexpert($data)
    {
        if ($this->_db->insert($this->getTableName(EXPERT), $data))
            return true;
        else
            return false;
    }
    public function delexpertUsingOwnerOrExpert($dbid)
    {
        if ($this->_db->delete($this->getTableName(EXPERT), array(
            "dbid='" . $dbid . "'","clientID='" . clientID . "'"
        )))
            return true;
        else
            return false;
    }
     public function deleteExpertInvitation($dbid)
    {
        if ($this->_db->delete($this->getTableName(INVITEXPORT), array(
            "dbeeid='" . $dbid . "'","clientID='" . clientID . "'"
        )))
            return true;
        else
            return false;
    }
    public function checkinviteexpert($dbeeid)
    {
        $select = $this->_db->select()->from($this->getTableName(INVITEXPORT))
                                            ->where("dbeeid = ?", $dbeeid)
                                            ->where("clientID = ?", clientID);
        return $this->_db->fetchAll($select);
    }
    public function checkdbeeinviteexpert($dbeeid)
    {
        $select = $this->_db->select()->from('tblactivity')
                    ->where("act_typeId = ?", $dbeeid)
                    ->where("act_type = ?", 39)
                    ->where("clientID = ?", clientID)
                    ->where("act_message = ?", 44);
        return $this->_db->fetchAll($select);
    }
    public function deleteInviteRecord($ids,$from,$dbid)
    {
        if ($from=='dbee'){
            $this->_db->delete('tblactivity', array("act_ownerid in (" . $ids . ")",
            "act_typeId='" . $dbid . "'","act_message='44'", "clientID='" . clientID . "'"
            ));
        }else if ($from=='social'){
             $this->_db->delete($this->getTableName(INVITEXPORT), array("socialid in (" . $ids . ")",
            "dbeeid='" . $dbid . "'", "clientID='" . clientID . "'"
            ));
        }
        return true;
    }

    public function getInviteUser($dbeeid)
    {    
         $select = $this->_db->select()
        ->from(array('c' => 'tblactivity'))
          ->join(array('u' => 'tblUsers'), 'c.act_ownerid  = u.UserID AND c.clientID  = u.clientID',array('u.UserID','u.full_name','u.Name','u.lname','u.Username','u.ProfilePic'));   
                    $select->where('c.act_typeId = ?',$dbeeid)->where('c.act_type = ?',39)->where('c.act_message = ?',44)->where('c.clientID = ?', clientID)
                    ->order('c.act_date Desc');                          
            return $this->_db->fetchAll($select);       
    }
}