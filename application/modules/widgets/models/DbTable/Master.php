<?php
/*****Defining all DBEE tables*****/
define("BLOCKUSER", "BlockedUsers");
define("BLOCKUSER_MSG", "BlockedUsersMsgs");
define("CAT", "DbeeCats");
define("COMMENT", "DbeeComments");
define("DBEE", "Dbees");
define("FAVOURITES", "Favourites");
define("FOLLOWS", "Follows");
define("GROUP_MEMBER", "GroupMembers");
define("GROUP", "Groups");
define("GROUP_TYPE", "GroupTypes");
define("MENTIONS", "Mentions");
define("MESSAGES", "Messages");
define("NOTIFICATION_SETTING", "NotificationSettings");
define("POLL_OPTION", "PollOptions");
define("POLL_VOTES", "PollVotes");
define("REDBEES", "ReDbees");
define("RSS_COUNTRIES", "RssCountries");
define("RSS_SITES", "RssSites");
define("SCORING", "Scoring");
define("SENDMAIL_IP", "SendmailP");
define("USER_BIOGRAPHY", "UserBiography");
define("USER_RSS", "UserRss");
define("USERS", "Users");
define("BLOCKSUSER_DBEE", "BlockedUsersDbs");
define("FACEBOOK", "FacebookFriends");

class Application_Model_DbTable_Master extends Zend_Db_Table_Abstract
{
    protected $_prefix = null;
	protected $_db;			
  	public function __construct()
	{
	       $this->_db = Zend_Db_Table_Abstract::getDefaultAdapter();		   
	       $this->_prefix = 'tbl';
	       parent::__construct();
           
    }			
	public function getTableName($table)
	{	        
	       return $this->_prefix.$table;
	}	

}
