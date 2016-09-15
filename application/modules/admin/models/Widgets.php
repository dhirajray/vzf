<?php

class Admin_Model_Widgets  extends Zend_Db_Table_Abstract
{
     // Table name 
     protected $_name = 'tblGroups';
     
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
	/* Insert special dbs */


	public function insertdata_global($table,$data){
		
		$insertdb = $this->_db->insert($table, $data);
        if($insertdb) return $this->_db->lastInsertId();
      	else return false; 
	}

    public function grouplist($keyword="",$lastids="")
    {
            
        $db = $this->getDbTable();
        $select = $db->select()->setIntegrityCheck( false );
        $select->from(array('a'=>'tblGroups'),array('a.ID','a.clientID','a.GroupName','a.GroupPic','a.GroupDesc'));       
        $select->where('a.clientID= ?',clientID);
        if($keyword!="")
        {
          $select->where('GroupName LIKE ?', ''.$keyword.'%');
        }
        if($lastids!="")
        {
         $select->where('a.ID < ?',$lastids);
        }        
        $select->order('a.ID DESC')->limit(10);
        //echo $select->__toString();
        //die; 
        return $this->getDefaultAdapter()->query($select)->fetchAll();
    }
    public function grouplistCount($keyword="",$lastids="")
    {
            
        $db = $this->getDbTable();
        $select = $db->select()->setIntegrityCheck( false );
        $select->from(array('a'=>'tblGroups'),array('total'=>'count(*)',));                
        $select->where('a.clientID= ?',clientID);
        if($keyword!="")
        {
          $select->where('GroupName LIKE ?', ''.$keyword.'%');
        }
        if($lastids!="")
        {
         $select->where('a.ID < ?',$lastids);
        }        
        return $this->getDefaultAdapter()->query($select)->fetchAll();
    }
    public function postlistCount($keyword="",$lastids="")
    {
            
        $db = $this->getDbTable();
        $select = $db->select()->setIntegrityCheck( false );
        $select->from(array('a'=>'tblDbees'),array('total'=>'count(*)'));       
        $select->where('a.clientID= ?',clientID); 
        if($keyword!="")
        {
         $select->where("a.Text LIKE '%$keyword%' OR a.PollText LIKE '%$keyword%' OR a.surveyTitle LIKE '%$keyword%'");
        } 
        if($lastids!="")
        {
         $select->where('a.DbeeID < ?',$lastids);
        }           
        return $this->getDefaultAdapter()->query($select)->fetchAll();
    }

    public function postlist($keyword="",$lastids="")
    {            
        $db = $this->getDbTable();
        $select = $db->select()->setIntegrityCheck( false );
        $select->from(array('a'=>'tblDbees'),array('a.DbeeID','a.Text','a.Type','a.surveyTitle','a.Pic','a.PollText','a.dburl'));        
        $select->where('a.clientID= ?',clientID);  
        if($keyword!="")
        {
         //$select->where('a.Text LIKE "%'.$keyword.'%"' OR 'a.PollText LIKE "%'.$keyword.'%"' OR 'a.surveyTitle LIKE "%'.$keyword.'%"');
         $select->where("a.Text LIKE '%$keyword%' OR a.PollText LIKE '%$keyword%' OR a.surveyTitle LIKE '%$keyword%'");
        } 
        if($lastids!="")
        {
         $select->where('a.DbeeID < ?',$lastids);
        }       
        $select->order('a.DbeeID DESC')->limit(10);
        /*echo $keyword.':'. $select->__toString();
        die; */
        return $this->getDefaultAdapter()->query($select)->fetchAll();
    }

     public function GetWidgetname($type='')
    {
            
        $db = $this->getDbTable();
        $select = $db->select()->setIntegrityCheck( false );
        $select->from(array('a'=>'tbl_widgets'),array('a.id','a.name','a.type'));       
        $select->where('a.clientID= ?',clientID); 
        if($type!="")
        {
        $select->where('a.type= ?',$type);
        }             
        $select->order('a.id DESC');
        //echo $select->__toString();
        //die; 
        return $this->getDefaultAdapter()->query($select)->fetchAll();
    }

    public function GetWidget($widgetID)
    {
            
        $db = $this->getDbTable();
        $select = $db->select()->setIntegrityCheck( false );
        $select->from(array('a'=>'tbl_widgets'),array('a.*'));       
        $select->where('a.clientID= ?',clientID); 
        $select->where('a.id= ?',$widgetID);              
       
        //echo $select->__toString();
        //die; 
        return $db->fetchRow($select);;
    }

    public function getresult($type,$keyid)
      {
            $db = $this->getDbTable();
            if($type==0)
            {
                $selectgrp = $db->select()->setIntegrityCheck( false );
                $selectgrp->from(array('a'=>'tblGroups'),array('a.ID','a.clientID','a.GroupName','a.GroupPic','a.GroupDesc'));       
                $selectgrp->where('a.clientID= ?',clientID);
                $selectgrp->where('FIND_IN_SET(ID,(?))', $keyid);
                //$selectgrp->where("FIND_IN_SET('".$keyid."',a.ID)");
                //echo $selectgrp->__toString();
                //die; 
                return $db->fetchAll($selectgrp);
            }
            if($type==1)
            {
                $selectpost = $db->select()->setIntegrityCheck( false );
                $selectpost->from(array('a'=>'tblDbees'),array('a.DbeeID','a.Text','a.Type','a.surveyTitle','a.Pic','a.PollText','a.dburl'));        
                $selectpost->where('a.clientID= ?',clientID);
                $selectpost->where('FIND_IN_SET(DbeeID,(?))', $keyid);
                return $db->fetchAll($selectpost);
            }
           


      }
	
    
}