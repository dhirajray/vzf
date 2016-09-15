<?php
class Admin_Model_Polloption extends Zend_Db_Table_Abstract
{ 
	
	protected $_name = null;
	
	protected function _setupTableName()
	{
		parent::_setupTableName();
		$this->_name = 'tblPollOptions';
	}
	



	public function getpolloptionvotePie($dbeeid) 
	{	
		$select = $this->_db->select();
		$select->from( array('opt' => 'tblPollOptions'),array('tvotes' =>  new Zend_Db_Expr('count(votes.ID)')));
		$select->join( array('votes' => 'tblPollVotes'), 'opt.ID=votes.Vote',array('opt.OptionText'));
		$select->where("opt.clientID = ?", clientID);		
		$select->where('opt.PollID = '.$dbeeid.'');
		$select->group('OptionText');
		
		return $this->_db->fetchAll($select);
		
	}
	public function getpolloptionvoteusersPie($dbeeid) 
	{	
		$select = $this->_db->select();
		$select->from( array('opt' => 'tblPollOptions'));
		$select->join( array('votes' => 'tblPollVotes'), 'opt.ID=votes.Vote',array('opt.OptionText'));
		$select->join( array('u' => 'tblUsers'), 'u.UserID=votes.User',array( 'UserID' => 'u.UserID','username' => 'u.Name','lname' => 'u.lname','image' =>'u.ProfilePic'));
		$select->join( array('c' => 'tblDbeeComments'), 'c.UserID=votes.User',array( 'CommentID' => 'c.CommentID','comment' => 'c.Comment','DbeeID' => 'c.DbeeID'));
		$select->where("opt.clientID = ?", clientID);		
		$select->where('opt.PollID = '.$dbeeid.'');
		$select->where('c.DbeeID = '.$dbeeid.'');
		$select->order('votes.VoteDate DESC');		
		return $this->_db->fetchAll($select);
		
	}

	public function getpores($poll)
	{
		$select = $this->_db->select()
			->from( 'tblPollOptions')
				->where('PollID = ?',$poll)->where('clientID = ?',clientID);		
		return $this->_db->fetchAll($select);
	}
	
	
	
	public function Polldetailhelper($row)
    {
		$row = $row[0];
		$db = $row['DbeeID'];       
		
		$dbee_highlighted.='<div class="pollTxtWrapper">'.$row['PollText'].'</div><div class="pollwrapper"><form>';

		$poRes = $this->getpores($db);
		$colorRadio =  array('#3366cc' ,'#dc3912',  '#ff9900', '#109618');
		$colorcount = 0;
		$dbee_highlighted.='<div class="pollOptionLeft">';
		
		foreach($poRes as $poRow):
			$dbee_highlighted.='<div id="pollopt'.$poRow['ID'].'" '.$color.' class="optiontextfloat"><span class="checkcolorSymbol" style="background:'.$colorRadio[$colorcount].'"></span><label><input type="radio" id="pollradio'.$poRow['ID'].'" name="pollradio" value="'.$poRow['ID'].'" '.$autoselect.' '.$disabled.'  class="radiodtn">'.$poRow['OptionText'].'</label></div>';
			$colorcount++;
		endforeach;
		$dbee_highlighted.='</div>';
		
	
		

		return $dbee_highlighted;   
	}
}
