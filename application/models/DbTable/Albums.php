<?php

class Application_Model_DbTable_Albums extends Zend_Db_Table_Abstract
{
	protected $_name = 'albums';

	public function save(	$title,
						  	$artist,
						  	$genre
						  	  ) {
		
    	$n = $this->createRow();
		$n->title = $title;
		$n->artist = $artist;
		$n->genre= $genre;
		$n->save();

    }

 	public function change( $id,
				 			$title,
				 			$artist,
				 			$genre
 						 ) {  
 							  	
        $data = array(
        	'title' 	=> $title,
        	'artist' 	=> $artist,
			'genre' 	=> $genre
			);  

        $this->update($data, 'id = ' . (int) $id);  

    }

	public function rowset($where = null, $order = null )
    {  
        return $this->fetchAll($where, $order);
    } 
	
	public function search($word = null, 
							$limit = null)
	{
		$db = Zend_Registry::get('db');
		$sql = new Zend_Db_Select( $db );
		$select = $sql->from('albums');

		if(!is_null($word)) {			  
			$select = $sql->where($db->quoteInto('title LIKE ? OR artist LIKE ?',"%".$word."%"));
		}
		if($limit > 0){
			$select = $sql->limit($limit);
		}
		
		$select = $sql->order('artist DESC');

		$results = $db->query( $select );
		$rows = $results->fetchAll();
	
		return $rows;	
		
	}

	public function remove($id)  
    {  
        $id = (int) $id;
    	$row = $this->fetchRow('id = ' . $id); 
        $this->delete('id =' . (int) $id);  
        
    } 

    public function get($id)  
    {  
        $id = (int) $id;  
  
        $row = $this->fetchRow('id = ' . $id);  
        if (!$row) {   
            return false;
        } else {	
        	return $row->toArray();
		}
		
    }  

    
}