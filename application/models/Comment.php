<?php
class Application_Model_Comment extends Application_Model_DbTable_Master
{
    protected $_name = null;
    protected function _setupTableName()
    {
        parent::_setupTableName();
        $this->_name = $this->getTableName(COMMENT);
    }
    public function insertmention($tableName, $data) // Table will add all kind of data in given table.
    {
        if ($this->_db->insert($tableName, $data))
            return true;
        else
            return false;
    }
    // Activity for table stats
    public function updatedbstats($tableName, $data, $dbid, $userid, $type)
    {
        $this->_db->update($tableName, $data, array(
            "stats_dbid='" . $dbid . "'",
            "stats_userid='" . $userid . "'",
            "stats_type='" . $type . "'"
        ));
        return 1;
    }
    public function chkstatsexist($dbid, $userid, $type)
    {
        $select = $this->_db->select()->from(array(
            'tbldbstats'
        ), array(
            'tot' => new Zend_Db_Expr('count(stats_dbid)')
        ))->where('stats_dbid = ?', $dbid)->where('stats_userid  = ?', $userid)->where('clientID = ?', clientID)->where('stats_type = ?', $type);
        $result = $this->_db->fetchAll($select);
        return $result[0]['tot'];
    }
    public function getfieldsfromstats($dbid, $type,$Social_Content_Block)
    {
        $select     = $this->_db->select()->from(array(
            'tbldbstats'
        ), array(
            'stats_type',
            'tot' => new Zend_Db_Expr('count(stats_dbid)')
        ))->where('stats_dbid = ?', $dbid)->where('clientID = ?', clientID)->group('stats_type');
        $result     = $this->_db->fetchAll($select);
        $statsusers = '';
        foreach ($result as $key => $value) 
        {
            $stats_type = $this->getstatustype($value['stats_type']);
            if($stats_type!='Normal') 
              $statsusers .= '<span class="cmntuserLink ' . $stats_type . 'Visitor"><i class="fa db' . $stats_type . 'Icon"></i>' . $value['tot'] . '</span> ';
            else 
              $statsusers .= '<span class="cmntuserLink ' . $stats_type . 'Visitor"><i class="visitorSprite"></i>' . $value['tot'] . '</span> ';
          
            $totalvisits += $value['tot'];
        }
        $dbvisits = '<span class="dbvisits totalVisitor"><i class="fa fa-exclamation-circle fa-lg"  rel="dbTip" title="This displays the tally of unique user visits to this page from internal and external sources"></i> Total visits : ' . $totalvisits . '</span>';
        if($Social_Content_Block!='block')
            $dbvisits.='<div class="socialVisitorStatus">' . $statsusers . '</div>';

        return $dbvisits;
    }
    public function getstatustype($type)
    {
        switch ($type) {
            case '1':
                return 'Facebook';
                break;
            case '2':
                return 'Twitter';
                break;
            case '3':
                return 'LinkedIn';
                break;
            case '4':
                return 'Google';
                break;
            default:
                return 'Normal';
                break;
        }
    }
    // End of activity for tbldbstats
    public function getallComment()
    {
        $select = $this->_db->select()->from(array(
            $this->_name
        ), array(
            'CatID',
            'CatName'
        ));
        $select->where('clientID = ?', clientID);
        $result = $this->_db->fetchAll($select);
        return $result;
    }
    public function index($start, $end, $userid)
    {
        //require_once 'includes/globalfile.php'; 
        $blocluser_obj    = new Application_Model_Blockuser($userid);
        $blockuser        = $blocluser_obj->getblockuser($userid);
        $myhome = new Application_Model_Myhome();
        $gethiddenDbs  =    $myhome->gethiddendbee($userid);
        $Offset = (int) $start;
        
        $select = $this->_db->select()->from(array('c' => 'viewposts' ))->joinInner(array(
            'd' => $this->getTableName(COMMENT)
        ), 'd.DbeeID = c.DbeeID AND d.clientID = c.clientID', array(
            'd.CommentDate',
            "cnt" => "COUNT(DISTINCT(d.CommentID))"
        ));
        if (!empty($blockuser)) {
            $select->where("c.GroupID NOT IN(?)", $blockuser);
        }
        if (count($gethiddenDbs) > 0) {
        	$select->where("c.DbeeID NOT IN(?)", $gethiddenDbs);
        }             
        $select->where('d.UserID = ?', $userid)->where("c.eventtype!= ?", '2')->where("c.Privategroup= ?", '0')->where('d.clientID = ?', clientID); 
        $select->group('c.DbeeID')->order('c.LastActivity DESC')->limit(PAGE_NUM, $Offset);
        $result = $this->_db->fetchAll($select);
        return $result;
    }
    public function insertcomment($data)
    {
        if ($this->_db->insert($this->_name, $data))
            return $this->_db->lastInsertId();
        else
            return false;
    }
    public function totalsentiments($dbeerid, $calle) //case id = 4707680724
    {
        $select = $this->_db->select();
        if ($calle == 'user') {
            $select->from(array(
                'cmnt' => $this->getTableName(COMMENT)
            ), array(
                'tot' => new Zend_Db_Expr('count(cmnt.sentiment_polarity)')
            ))->where("cmnt.sentiment_polarity != ?", '')->where('cmnt.clientID = ?', clientID)->where("cmnt.DbeeID = ?", $dbeerid);
        } else {
            $select->from(array(
                'cmnt' => $this->getTableName(COMMENT)
            ), array(
                'sentiment_polarity',
                'tot' => new Zend_Db_Expr('count(cmnt.sentiment_polarity)')
            ))->where("cmnt.sentiment_polarity != ?", '')->where('cmnt.clientID = ?', clientID)->where("cmnt.DbeeID = ?", $dbeerid)->group("cmnt.sentiment_polarity")->order('tot DESC')->limit(1);
        }
        $result = $this->_db->fetchAll($select);
        return $result;
    }
    public function sentimentscomment($dbeerid)
    {
        $select = $this->_db->select()->from(array(
            'c' => $this->getTableName(COMMENT)
        ))->where("c.DbeeID = ?", $dbeerid)->where('c.clientID = ?', clientID)->where("c.sentiment_polarity = ?", '')->order('c.CommentDate asc')->limit(20);
        $result = $this->_db->fetchAll($select);
        return $result;
    }

    public function totalcomment($start, $end, $userid)
    {
        $Offset = (int) $start;
        $sql    = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(array(
            'c' => $this->getTableName(DBEE)
        ), '*');
        $select->join(array(
            'u' => $this->getTableName(USERS)
        ), 'u.UserID = c.User AND u.clientID = c.clientID');
        $select->join(array(
            'd' => $this->getTableName(COMMENT)
        ), 'd.DbeeID = c.DbeeID', array(
            'CommentDate',
            'cnt' => count('*')
        ), 'left');
        $select->where('d.UserID= ?', $userid)->where('d.clientID = ?', clientID);
        $select->group('c.DbeeID');
        $select->order('d.CommentDate Desc');
        $select->offset($Offset);
        $select->limit(5);
        $result = $this->_db->fetchAll($select);
        return count($result);
    }
    public function getoners($dbid)
    {
        $sql    = new Sql($this->adapter);
        $select = $sql->select()->from($this->getTableName(DBEE), array(
            'DbeeID',
            'User'
        ))->where('DbeeID = ?', $dbid)->where('clientID = ?', clientID);
        $result = $this->_db->fetchAll($select);
        return $result[0]['User'];
    }
    public function getdbeecomment($id)
    {
        $select = $this->_db->select()->from(array(
            'c' => $this->getTableName(DBEE)
        ))->joinLeft(array(
            'd' => $this->getTableName(USERS)
        ), 'c.User = d.UserID')->where('c.clientID = ?', clientID)->where("c.DbeeID = ?", $id)->order('c.DbeeID asc');
        $result = $this->_db->fetchAll($select);
        return $result[0];
    }
    public function getcommentuser($id, $userid)
    {
        $select = $this->_db->select()->from(array(
            'c' => $this->_name
        ))->joinLeft(array(
            'd' => $this->getTableName(USERS)
        ), 'c.UserID = d.UserID')->where('d.clientID = ?', clientID)->where("c.DbeeID = ?", $id)->where('c.UserID NOT IN(?)', $userid)->group('c.UserID')->order('c.DbeeID asc');
        $result = $this->_db->fetchAll($select);
        return $result;
    }
    public function totacomment($dbeerid)
    {
        $dbeerid = (int) $dbeerid;
        $select  = $this->_db->select()->from(array(
            'c' => $this->getTableName(COMMENT)
        ))->where("c.DbeeID = ?", $dbeerid)->where("clientID = ?",clientID);
        $result  = $this->_db->fetchAll($select);
        return count($result);
    }
    public function totacommentgroup($dbeerid)
    {
        $dbeerid = (int) $dbeerid;
        $select  = $this->_db->select()->from(array(
            'c' => $this->getTableName(COMMENT)
        ))->where("c.DbeeID = ?", $dbeerid)->where("c.clientID = ?",clientID)->group("c.UserID");
        $result  = $this->_db->fetchAll($select);
        return count($result);
    }
    public function totalgpc($dbeerid, $twitergp)
    {
        $dbeerid = (int) $dbeerid;
        $select  = $this->_db->select()->from(array(
            'c' => $this->getTableName(COMMENT)
        ))->where("c.DbeeID = ?", $dbeerid)->where("c.TwitterGPName =" . $dbeerid . "")->where("c.clientID = ?",clientID);
        $result  = $this->_db->fetchAll($select);
        return count($result);
    }

    function convert_clickable_links($message)
    {
        return preg_replace(array('/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?Â«Â»â€œâ€�â€˜â€™]))/',  '/(^|[^a-z0-9_-])#([a-z0-9_-]+)/i'), array('$1', '$1<a href=/myhome/hashtag/tag/$2>#$2</a>'), $message);
      
    }
    
    function convert_clickable_links_Second($message)
    {
        return preg_replace(array('/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?Â«Â»â€œâ€�â€˜â€™]))/', '/(^|[^a-z0-9_-])#([a-z0-9_-]+)/i'), array('<a href=$1 class=hashTags tags tag-small target=_blank>$1</a>', '$1<a class=tags tag-small href=/myhome/hashtag/tag/$2>#$2</a>'), $message);
       
    }
    public function getallcommentsfromcommentid($parentid, $commentid,$QNATYPE)
    {
        $dbeerid      = (int) $dbeerid;
        $select       = $this->_db->select()->from(array(
            'c' => $this->getTableName(COMMENT)
        ), array(
            'parentid',
            'QNATYPE',
            'DbeeID',
            'UserID',
            'Type',
            'sentiment_score',
            'sentiment_polarity',
            'Comment',
            'Link',
            'LinkTitle',
            'LinkDesc',
            'UserLinkDesc',
            'Pic',
            'PicDesc',
            'Vid',
            'VidDesc',
            'VidSite',
            'VidID',
            'Audio'
        ))->join(array(
            'd' => $this->getTableName(USERS)
        ), 'c.UserID = d.UserID', array(
            'UserID',
            'Name',
            'Username',
            'ProfilePic',
            'Status'
        ))->where("c.CommentID = ?", $parentid)->where('c.clientID = ?', clientID);
        $row_comments = $this->_db->fetchAll($select);
        $this->myclientdetails = new Application_Model_Clientdetails();
        $checkImage = new Application_Model_Commonfunctionality();
        $pic1 = $checkImage->checkImgExist($row_comments[0]['ProfilePic'],'userpics','default-avatar.jpg');
        if ($QNATYPE == 1) {
            $qantypeText = 'Question:';
        } else {
            $qantypeText = 'In reply to:';
        }
        $return = '';
  
        if ($row_comments[0]['Status'] == 1)
            $cmntprofileLinkStart = '<a href="'.BASE_URL.'/user/' . $this->myclientdetails->customDecoding($row_comments[0]['Username']).'" class="cmntuserLink" >';
        else
            $cmntprofileLinkStart = '<a href="javascript:void(0)" class="profile-deactivated" title="' . DEACTIVE_ALT . '" onclick="return false;">';

        $return .= '<div id="" class="" style="background:#fff">
            <div id="dbcomment-speechwrapper" class="dbcomment-speechwrapper">
                <div class="dbcmntspeech">                   
                        <div class="inReplyWrapper">
                        <div class="inreplyPhoto">' . $cmntprofileLinkStart . '<img src="'.IMGPATH.'/users/small/'.$pic1.'" width="30" height="30" border="0" /></a></div>
                         <div class="inReplyContent">
                        <div class="rptoTitle">' . $qantypeText . '</div>' . $cmntprofileLinkStart . '<span >' . $this->myclientdetails->customDecoding($row_comments[0]['Name']) . '</span></a>';
        if ($row_comments[0]['Type'] == '1') {
            $return .= '&nbsp;<span >' . $this->convert_clickable_links(substr($row_comments[0]['Comment'], 0, 400));
            if (strlen($row_comments[0]['Comment']) > 400) {
                $return .= '<strong class="expandparentcomment"  cid="' . $commentid . '" id="expandparentcomment' . $commentid . '" >[+]</strong>  <span style="display:none" id="parentexpende_' . $commentid . '">' . substr($row_comments[0]['Comment'], 400) . '</span> <strong class="collespparentcomment" id="collespparentcomment' . $commentid . '" cid="' . $commentid . '" >[-]</strong> ';
            }
            $return .= '</span>';
        } elseif ($row_comments[0]['Type'] == '2') {
            $return .= '&nbsp;' . $row_comments[0]['UserLinkDesc'] . '<div style="float:left; margin:0 10px 0 0;"><div style="width:530px; padding:5px; margin-top:5px; margin-bottom:5px; background-color:#DAD9D9;"><div>' . $row_comments[0]['LinkTitle'] . ' - <a href="' . $row_comments[0]['Link'] . '" target="_blank">' . $row_comments[0]['Link'] . '</a></div><div style="margin-top:10px;">' . substr($row_comments[0]['LinkDesc'], 0, 400);
            if (strlen($row_comments[0]['LinkDesc']) > 400) {
                $return .= '<strong class="expandparentcomment"  cid="' . $commentid . '" id="expandparentcomment' . $commentid . '" >[+]</strong>  <span style="display:none" id="parentexpende_' . $commentid . '">' . substr($row_comments[0]['LinkDesc'], 400) . '</span> <strong class="collespparentcomment" id="collespparentcomment' . $commentid . '" cid="' . $commentid . '">[-]</strong> ';
            }
            $return .= '</span>';
            $return .= '</div></div></div>';
        } elseif ($row_comments[0]['Type'] == '3') {
            $return .= '&nbsp;' . substr($row_comments[0]['PicDesc'], 0, 400);
            if (strlen($row_comments[0]['PicDesc']) > 400) {
                $return .= '<strong class="expandparentcomment"  cid="' . $commentid . '" id="expandparentcomment' . $commentid . '" >[+]</strong>  <span style="display:none" id="parentexpende_' . $commentid . '">' . substr($row_comments[0]['PicDesc'], 400) . '</span> <strong class="collespparentcomment" id="collespparentcomment' . $commentid . '" cid="' . $commentid . '">[-]</strong> ';
            }
            $return .= '</span>';
            $checkImage = new Application_Model_Commonfunctionality();
            $pic2 = $checkImage->checkImgExist($row_comments[0]['Pic'],'imageposts','default-avatar.jpg');
            $return .= '<div style="float:left;  margin:0 10px 0 0;"><div style="float:left; margin-top:5px; width:auto; margin-bottom:5px; padding:3px; border:1px solid #CCCCCC;"><a href="'.IMGPATH.'/imageposts/' . $row_comments[0]['Pic'] . '" rel="popupbox"><img src="'.IMGPATH.'/imageposts/small/' .$pic2. '0" border="0" /></a></div></div>';
        } elseif ($row_comments[0]['Type'] == '4') {
            if ($row_comments[0]['Vid'] != '') {
                $atag      = '<a href="javascript:seevideo(\'' . $row_comments[0]['VidID'] . '\');">';
                $mediaicon = '<div class="icon-youtube" style="margin:-2px 20px 0 -30px;; height:30px;"></div>';
                if ($row_comments[0]['VidSite'] == 'Youtube')
                    $VideoThumbnail = '<img src="http://i.ytimg.com/vi/' . $row_comments[0]['VidID'] . '/0.jpg" width="120" height="100" border="0" />';
                elseif ($row_comments[0]['VidSite'] == 'Vimeo') {
                    $url            = 'http://vimeo.com/api/v2/video/' . $row_comments[0]['VidID'] . '.php';
                    $contents       = @file_get_contents($url);
                    $thumb          = @unserialize(trim($contents));
                    $VideoThumbnail = "<img src=" . $thumb[0][thumbnail_small] . ">";
                } elseif ($row_comments[0]['VidSite'] == 'Dailymotion')
                    $VideoThumbnail = '<img src="http://www.dailymotion.com/thumbnail/video/' . $row_comments[0]['VidID'] . '" width="120" height="100" border="0" />';
            } elseif ($row_comments[0]['Audio'] != '') {
                $atag           = '<a href="javascript:seeaudio(\'' . $row_comments[0]['CommentID'] . '\');">';
                $VideoThumbnail = '<img src="'.BASE_URL.'/images/soundcloud.png">';
                $mediaicon      = '<div class="icon-soundcloud" style="margin:3px 20px 0 -30px; height:30px;"></div>';
            }
            $return .= '&nbsp;' . substr($row_comments[0]['VidDesc'], 0, 400);
            if (strlen($row_comments[0]['PicDesc']) > 400) {
                $return .= '<strong class="expandparentcomment"  cid="' . $commentid . '" id="expandparentcomment' . $commentid . '">[+]</strong>  <span style="display:none" id="parentexpende_' . $commentid . '">' . substr($row_comments[0]['PicDesc'], 400) . '</span> <strong class="collespparentcomment" id="collespparentcomment' . $commentid . '" cid="' . $commentid . '">[-]</strong> ';
            }
            $return .= '</span>';
            $return .= '<div style="float:left; margin:0 10px 0 0;"><div style="float:left; margin-right:10px; width:120px; margin-bottom:5px; padding:3px; border:1px solid #CCCCCC;">' . $atag . $VideoThumbnail . '</a></div></div> ';
        }
        $return .= '  </div></div>
                    </div>
                </div>           
        </div>';
        return $return;
    }
    public function getcomment($dbeerid)
    {
        $dbeerid = (int) $dbeerid;
        $select  = $this->_db->select()->from(array(
            'c' => $this->getTableName(COMMENT)
        ))->join(array(
            'd' => $this->getTableName(USERS)
        ), 'c.UserID = d.UserID')->where("c.QNA != ?", 1)->where('c.clientID = ?', clientID)->where("c.DbeeID = ?", $dbeerid)->order('c.CommentDate Desc')->limit(20);
        $result  = $this->_db->fetchAll($select);
        return $result;
    }
    public function getcommentreload($dbeerid, $start, $order)
    {
        $Offset  = (int) $start;
        $dbeerid = (int) $dbeerid;
        $select  = $this->_db->select()->from(array(
            'c' => $this->getTableName(COMMENT)
        ))->join(array(
            'd' => $this->getTableName(USERS)
        ), 'c.UserID = d.UserID')
        ->where("c.clientID = ?", clientID)
        ->where("c.DbeeID = ?", $dbeerid)->where("c.QNA != ?", 1)
        ->order('c.CommentDate ' . $order)->limit(20, $Offset);
        $result  = $this->_db->fetchAll($select);
        return $result;
    }

    public function getcommentreloadPoll($dbeerid, $start, $order,$pollOptionID='')
    {
        $Offset  = (int) $start;
        $dbeerid = (int) $dbeerid;
        $select  = $this->_db->select()->from(array(
            'c' => $this->getTableName(COMMENT)
        ))->join(array(
            'd' => $this->getTableName(USERS)
        ), 'c.UserID = d.UserID')
        ->where("c.clientID = ?", clientID);
        if($pollOptionID)
        $select->where("c.VoteID = ?", $pollOptionID);
        $select->where("c.DbeeID = ?", $dbeerid)->limit(20, $Offset);
        return $this->_db->fetchAll($select);
    }

    public function commentleague($dbeerid)
    {
        $Offset  = (int) $start;
        $dbeerid = (int) $dbeerid;
        $select  = $this->_db->select()->from(array(
            'c' => $this->getTableName(COMMENT)
        ))->join(array(
            'd' => $this->getTableName(USERS)
        ), 'c.UserID = d.UserID')->where("c.DbeeID = ?", $dbeerid)
        ->where("c.clientID = ?", clientID)
        ->order('c.CommentDate Desc')->limit(5, $Offset);
        $result  = $this->_db->fetchAll($select);
        return $result;
    }
    public function getcommenttotal($dbeerid, $start, $order)
    {
        $Offset  = (int) $start;
        $dbeerid = (int) $dbeerid;
        $select  = $this->_db->select()->from(array(
            'c' => $this->getTableName(COMMENT)
        ))->join(array(
            'd' => $this->getTableName(USERS)
        ), 'c.UserID = d.UserID')->where('c.clientID = ?', clientID)->where("c.DbeeID = ?", $dbeerid)->order(array(
            'c.CommentDate ' . $order
        ))->limit(20, $Offset);
        $result  = $this->_db->fetchAll($select);
        return count($result);
    }
    public function getcommentreloadNew($dbeerid, $order, $LastCommentSeenDate)
    {
        $select  = $this->_db->select()->from(array(
            'c' => $this->getTableName(COMMENT)
        ))->join(array(
            'd' => $this->getTableName(USERS)
        ), 'c.UserID = d.UserID')->where("c.DbeeID = ?", $dbeerid)->where("c.clientID = ?", clientID)->where("c.QNA != ?", 1)->where('c.CommentDate >= ?', $LastCommentSeenDate)->order('c.CommentDate ' . $order);
        
        return $this->_db->fetchAll($select);
    }
    public function getcommenttotalNew($dbeerid, $order, $LastCommentSeenDate)
    {
        $dbeerid = (int) $dbeerid;
        $select  = $this->_db->select()->from(array(
            'c' => $this->getTableName(COMMENT)
        ))->join(array(
            'd' => $this->getTableName(USERS)
        ), 'c.UserID = d.UserID')->where("c.DbeeID = ?", $dbeerid)->where("c.clientID = ?", clientID)->where('c.CommentDate >= ?', $LastCommentSeenDate)->order(array(
            'c.CommentDate ' . $order
        ));
        return count($this->_db->fetchAll($select));
    }

    public function getcommentData($data,$limit=true)
    {
        $select  = $this->_db->select();
        $select->from(array(
            'c' => $this->getTableName(COMMENT)
        ))->join(array(
            'd' => $this->getTableName(USERS)
        ), 'c.UserID = d.UserID')->where("c.DbeeID = ?", $data['dbeeid'])->where("c.clientID = ?", clientID)->where("c.QNA != ?", 1);

        if($data['lastID']!='' && $data['lastID']!=0 && $data['order'] == 'DESC')
            $select->where("c.CommentID < ?", $data['lastID']);

        if($data['lastID']!='' && $data['lastID']!=0 && $data['order'] == 'ASC')
            $select->where("c.CommentID > ?", $data['lastID']);

        if($data['commentid']!='' && $data['commentid']!='')
            $select->where("c.CommentID = ?", $data['commentid']);

        $select->order('c.CommentDate ' . $data['order']);
        if($limit==true)
            $select->limit(10);
        return $this->_db->fetchAll($select);
    }

    public function getfollowedby($dbuid, $uid)
    {
        $dbeeiduser = $this->getuserbydbeeid($dbuid);
        $select     = $this->_db->select()->from($this->getTableName(FOLLOWS))->where('clientID = ?', clientID)->where("User = ?", $dbeeiduser)->where("FollowedBy = ?", $uid);
        $result     = $this->_db->fetchAll($select);
        return $result['ID'];
    }
    public function getuserbydbeeid($dbee)
    {
        $select = $this->_db->select()->from(array(
            'c' => $this->_name
        ), array(
            'c.DbeeID'
        ))->where("c.DbeeID = ?", $dbee)->where("c.clientID =?",clientID);
        $result = $this->_db->fetchAll($select);
        return $result[0]['DbeeID'];
    }
    public function getCommentInfo($dbee, $userid)
    {
        $select = $this->_db->select()->from(array(
            'c' => $this->_name
        ), array(
            'c.NotifyEmail'
        ))->where("c.DbeeID = ?", $dbee)->where("c.UserID = ?", $userid)->where("c.clientID =?",clientID);
        return $this->_db->fetchAll($select);
    }
    public function notifyemail($dbee, $userid)
    {
        $select = $this->_db->select()->from(array(
            'c' => $this->_name
        ), array(
            'c.NotifyEmail'
        ))->where("c.DbeeID = ?", $dbee)->where("c.UserID = ?", $userid)->where("c.clientID =?",clientID);
        return $result = $this->_db->fetchAll($select);
        return $result[0]['NotifyEmail'];
    }
    public function getcommentbyid($commentid)
    {
        $select = $this->_db->select()->from(array(
            'c' => $this->_name
        ), array(
            'DbeeID',
            'UserID',
            'Type',
            'Comment',
            'LinkTitle',
            'PicDesc',
            'VidDesc',
            'qid'
        ))->where('CommentID = ?', $commentid)->where('clientID = ?', clientID);
        $result = $this->_db->fetchRow($select);
        return $result;
    }
    public function getscorebyid($id, $type, $userid)
    {
        $select = $this->_db->select()->from($this->getTableName(SCORING))->where('clientID = ?', clientID)->where('ID = ?', $id)->where('Type = ?', $type)->where('UserID = ?', $userid);
        $result = $this->_db->fetchRow($select);
        return $result;
    }
    public function addscore($data)
    {
        if ($this->_db->insert($this->getTableName(SCORING), $data))
            return true;
        else
            return false;
    }
    public function deletescoring($id)
    {
        if ($this->_db->delete($this->getTableName(SCORING), array(
            "ScoreID='" . $id . "'"
        )))
            return true;
        else
            return false;
    }
    public function updatescoring($data, $id)
    {
        if ($this->_db->update($this->getTableName(SCORING), $data, array(
            "ScoreID='" . $id . "'"
        )))
            return true;
        else
            return false;
    }
    public function getuser($userid)
    {
        $select = $this->_db->select()->from($this->getTableName(USERS))->where('UserID = ?', $userid)->where('clientID = ?', clientID);
        $result = $this->_db->fetchRow($select);
        return $result;
    }
    public function getnotigyemail($userid, $score, $dbeeid)
    {
        $select = $this->_db->select()->from(array(
            't' => $this->getTableName(USER_BIOGRAPHY)
        ))->join(array(
            'c' => $this->getTableName(SCORING)
        ), 't.UserID = c.UserID')->where('c.clientID = ?', clientID)->where("c.Score = ?", $score)->where("c.MainDB = ?", $dbeeid);
        $result = $this->_db->fetchAll($select);
        return count($result);
    }
    public function getalluser($db)
    {
        $select = $this->_db->select()->distinct()->from($this->getTableName(COMMENT), 'UserID')->where('DbeeID = ?', $db)->where('clientID = ?', clientID);
        $result = $this->_db->fetchAll($select);
        return $result;
    }
    public function getcommentusersearch($search, $UserList)
    {
        $select = $this->_db->select()->from($this->getTableName(USERS))->where('Name LIKE ?', "%$search%")->where('clientID = ?', clientID)
            ->limit(10);
        $result = $this->_db->fetchAll($select);
        return $result;
    }
    public function removecomment($comment, $userid)
    {
        $this->_db->delete($this->_name, array(
            "CommentID='" . $comment . "'","clientID='" . clientID . "'"
        ));
        return true;
    }
    public function getscore($userid, $commentid)
    {
        $select = $this->_db->select()->from($this->getTableName(SCORING))->where('clientID = ?', clientID)->where('ID = ?', $commentid)->where('UserID = ?', $userid);
        $result = $this->_db->fetchRow($select);
        return $result;
    }
    public function countdbee($dbee)
    {
        $select = $this->_db->select()->from($this->_name, 'CommentID')->where('clientID = ?', clientID)->where('DbeeID = ?', $dbee);
        $result = $this->_db->fetchAll($select);
        return $result;
    }
    public function getleauebyscore2($league, $CommentUsers, $db)
    {
        $select = $this->_db->select()->from(array(
            'u' => $this->getTableName(USERS)
        ))->joinLeft(array(
            's' => $this->getTableName(SCORING)
        ), 'u.UserID = s.UserID', array(
            "ScoreID",
            "cnt" => "count(*)*5"
        ))->where('s.Type = ?', '2')->where('s.clientID = ?', clientID)->where('s.Score = ?', $league)->where('s.MainDB = ?', $db)->group('s.UserID')->order('cnt');
        $result = $this->_db->fetchAll($select);
        return $result;
    }
    public function getleauebyscorelike($league, $CommentUsers, $db)
    {
        $select = $this->_db->select()->from(array(
            'u' => $this->getTableName(USERS)
        ))->joinLeft(array(
            's' => $this->getTableName(SCORING)
        ), 'u.UserID = s.UserID', array(
            "ScoreID",
            "cnt" => "count(*)"
        ))->where('s.Type = ?', '2')->where('s.clientID = ?', clientID)->where('s.Score = ?', $league)->where('s.MainDB = ?', $db)->group('s.UserID')->order('cnt');
        $result = $this->_db->fetchAll($select);
        return $result;
    }
    public function getaudio($comid)
    {
        $select = $this->_db->select()->from($this->_name, 'Audio')->where('clientID = ?', clientID)->where('CommentID = ?', $comid);
        $result = $this->_db->fetchRow($select);
        return $result['Audio'];
    }
    public function getnotifycomment($db, $cookieuser)
    {
        $select = $this->_db->select()->from($this->getTableName(COMMENT))->where('clientID = ?', clientID)->where('DbeeID = ?', $db)->where('UserID = ?', $cookieuser);
        //echo $select->__toString();
        $result = $this->_db->fetchAll($select);
        return $result;
    }

    public function getcommentiddbee($CheckDateComments, $db, $cookieuser, $seencomms)
    {
        $select = $this->_db->select()->from($this->getTableName(COMMENT))->where('clientID = ?', clientID)->where('CommentDate > ?', $CheckDateComments)->where('DbeeID = ?', $db)->where('UserID != ?', $cookieuser);
        if ($seencomms != '') {
            $select->where('CommentID NOT IN(?)', $seencomms);
        }
        //echo $select->__toString();
        $result = $this->_db->fetchAll($select);
        return $result;
    }
    public function blockuser($userid, $dbeeid)
    {
        $select = $this->_db->select()->from($this->getTableName(BLOCKUSER))->where('clientID = ?', clientID)->where('DbeeID = ?', $dbeeid)->where('User = ?', $userid);
        $result = $this->_db->fetchAll($select);
        return count($result);
    }
    public function getcommentlastseen($LastCommentSeenDate, $db)
    {
        $db1    = (int) $db;
        $select = $this->_db->select()->from($this->getTableName(COMMENT))->where('clientID = ?', clientID)->where('DbeeID = ?', $db1)->where('CommentDate >= ?', $LastCommentSeenDate);
        //echo $select->__toString();
        $result = $this->_db->fetchAll($select);
        return $result;
    }
    public function totalcommentbydb($db)
    {
        $select = $this->_db->select()->from($this->getTableName(COMMENT))
        //->where('DbeeID = ?',$db)
            ->where('CommentDate >= ?', $LastCommentSeenDate)->where('clientID = ?', clientID);
        //echo $select->__toString();
        $result = $this->_db->fetchAll($select);
        return $result;
    }
    
    public function chknotificationstatus($dbeeid, $userid)
    {
        $select = $this->_db->select()->from($this->getTableName(COMMENT))->where('clientID = ?', clientID)->where('DbeeID = ?', $dbeeid)->where('UserID = ?', $userid)->order('CommentID Desc')->limit(1);
        $result = $this->_db->fetchRow($select);
        return $result;
    }
}