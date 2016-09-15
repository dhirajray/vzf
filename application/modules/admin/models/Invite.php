<?php

class Admin_Model_Invite extends Zend_Db_Table_Abstract
{
     // Table name 
     protected $_name = 'tblinvite';
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
            $this->setDbTable('Admin_Model_DbTable_Invite');
        }
        return $this->_dbTable;
    }
    public function init()
    {
        $this->myclientdetails = new Admin_Model_Clientdetails();
    }
	
	
	public function insertdata($data){
	   //$params = array('host' => 'localhost','username' =>'root','password' =>'','dbname' =>'db_dbee');
       //$DB = new Zend_Db_Adapter_Pdo_Mysql($params);
        $db = $this->getDbTable();
	   return $allrec =	$db->insert($data);
	
	}
    public function getSpecificRecord($field,$inv_id)
    {
        $select= $this->select()->where('inv_id = ?', $inv_id)->where('clientID = ?', clientID);
        $result= $this->fetchAll($select)->toarray();     
        return $result[0][$field]; 
    }

    public function reinviteUser($inv_id)
    {
        $db = $this->getDbTable();
        $reinvite   =   $this->getSpecificRecord('reinvite',$inv_id);
        $data       =   array('reinvite'=>($reinvite+1));
        $allrec = $this->myclientdetails->updatedata_global('tblinvite',$data,'inv_id',$inv_id);//$db->update($data,'inv_id ='.$inv_id);
        if($allrec) { return ($reinvite+1); } else { return $reinvite;}
    }
    

    public function update($data,$inv_id)
    {
    	$db = $this->getDbTable();
    	$reinvite   =   $this->getSpecificRecord('reinvite',$inv_id);
    	$data       =   array('isadded'=>1);
    	$allrec = $db->update($data,'inv_id ='.$inv_id);
    	if($allrec) {
    		return ($reinvite+1);
    	} else { return $reinvite;
    	}
    }
    public function deleteinviteUser($inv_id,$calle='')
    {
        $db = $this->getDbTable();
       // $where[] = $this->getAdapter()->quoteInto('clientID = ?', clientID);
        //$where[] = $this->getAdapter()->quoteInto($wherefield .'= ?', $whereid);
      //$del =  $this->_db->delete($table,$where);
        if($calle=='') return $this->myclientdetails->deletedata_global('tblinvite','inv_id',(int)$inv_id); //$db->delete('inv_id =' . (int)$inv_id);
        else
        {
            $this->myclientdetails->deletedata_global('tblUserMeta','UserId',(int)$inv_id);
            return $this->myclientdetails->deletedata_global('tblUsers','UserID',(int)$inv_id);//$this->_db->delete('tblUsers',"UserID='". $inv_id."'");
        }
    }

    public function chkInviteExist($userEmail,$calle='') 
    {
        $select= $this->select()->where('isadded != ?', '1');
        if($userEmail!='')
        {
            $select->where('inv_email = ?', $userEmail);
        } 
        return $result= $this->fetchAll($select)->count();      
    }
	
	 public function getUsers($searchfield,$calle='') {

		$select= $this->select();
		if($searchfield!='')
		{
            $select->where('inv_name LIKE "%'.$searchfield.'%" OR inv_email  LIKE "%'.$searchfield.'%"  ');
		} 
		$result= $this->fetchAll($select);		
		return $result;		
    }
	
	//public function getcontacts($login,$password)
   
	public function getcontacts( $email,$password )
    {
	    //print_r($email);print_r($password);
	    $this->gmail_client_auth = "https://www.google.com/accounts/ClientLogin";
        $this->number_of_results = '1000'; // Number of contacts to retrieve from user account
        $this->start_index = '1';
        // Parameteres for curl request Email ID and Password
        $params = "accountType=GOOGLE&Email=".$email."&Passwd=".$password."&service=cp&source=moneymagpie";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$this->gmail_client_auth);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

		$response = curl_exec($ch);
	     	$html = split(chr(10), $response );

        for ($i=0; $i<count( $html ); $i++)
	    {
            // Find position of first "=" character
            $splitAt = strpos( $html[$i], "=");
            // Create an associative (hash) array with key/value pairs ('trim' strips excess whitespace)
            $output[trim(substr($html[$i], 0, $splitAt))] = trim(substr($html[$i], ($splitAt+1)));
        }
        //print_r($params);

        $email = urlencode($email);
        // Contacts Feed request url
        $action = "http://www.google.com/m8/feeds/contacts/".$email."/full?start-index=".$this->start_index."&max-results=".$this->number_of_results;
        //print_r($action);
        /*if( !isset( $output['Auth'] ))
        {
            return array();
        }*/
        //print_r($action);
        // Header should be supplied with the authorization key
        $header = "Authorization: GoogleLogin auth=".$output['Auth'];
        $header .= ",GData-Version: 3.0";
        //print_r($header);die('hi asr');
        curl_setopt($ch, CURLOPT_URL,$action);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        curl_setopt ($ch, CURLOPT_HTTPHEADER, array($header) );
		
        //print_r($action);die('hi asr');
		
        $contacts = curl_exec($ch);
        $email_array = array();

        // Converts xml to array 
        $array_feed = $this->xmlToarray( $contacts );
        //echo'<pre>';print_r($array_feed);echo'===';

        // Gets the parent node 
        if( !isset( $array_feed['feed']['entry'] ))
        {
            return array();
        }

       // Iterates each node.
        foreach( $array_feed['feed']['entry']   as $contact )
        {
           if( isset( $contact['gd:email_attr']['address']) && isset( $contact['title'] )  )
           {
             $email_array[] = array ( 'email' =>  $contact['gd:email_attr']['address'],
                                      'name' =>  is_string( $contact['title'])?$contact['title']:''
                                    );
           }
        }


      // Deletes the duplicate entries.
      $emails = array();
      $exclude = array();
      for ($i = 0; $i<=count($email_array)-1; $i++)
      {
         if (!in_array(trim($email_array[$i]["email"]) ,$exclude))
         {
             $emails[] = $email_array[$i];
             $exclude[] = trim($email_array[$i]["email"]);

         }
      }

     
     // Sorts the array and returns 
     return $this->sort_array_by_field( $emails, 'email' );
        
        
    }

    /**
     * xml2array() will convert the given XML text to an array in the XML structure.
     * Link: http://www.bin-co.com/php/scripts/xml2array/
     *
     * @param : $contents - The XML text
     *                $get_attributes - 1 or 0. If this is 1 the function will get the attributes as well as the tag values - this results in a different array structure in the return value.
     *                $priority - Can be 'tag' or 'attribute'. This will change the way the resulting array sturcture. For 'tag', the tags are given more importance.
     * @return: The parsed XML in an array form. Use print_r() to see the resulting array structure.
     * @example: $array =  xml2array(file_get_contents('feed.xml'));
     *           $array =  xml2array(file_get_contents('feed.xml', 1, 'attribute'));
     */
     
    function xmlToarray($contents, $get_attributes=1, $priority = 'tag')
    {

        if(!$contents) return array();

        if(!function_exists('xml_parser_create')) {
            //print "'xml_parser_create()' function not found!";
            return array();
        }

        //Get the XML parser of PHP - PHP must have this module for the parser to work
        $parser = xml_parser_create('');
        xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, trim($contents), $xml_values);
        xml_parser_free($parser);

        if(!$xml_values) return;//Hmm...

        //Initializations
        $xml_array = array();
        $parents = array();
        $opened_tags = array();
        $arr = array();

        $current = &$xml_array; //Refference

        //Go through the tags.
        $repeated_tag_index = array();//Multiple tags with same name will be turned into an array
        foreach($xml_values as $data) {
            unset($attributes,$value);//Remove existing values, or there will be trouble

            //This command will extract these variables into the foreach scope
            // tag(string), type(string), level(int), attributes(array).
            extract($data);//We could use the array by itself, but this cooler.

            $result = array();
            $attributes_data = array();

            if(isset($value)) {
                if($priority == 'tag') $result = $value;
                else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
            }

            //Set the attributes too.
            if(isset($attributes) and $get_attributes) {
                foreach($attributes as $attr => $val) {
                    if($priority == 'tag') $attributes_data[$attr] = $val;
                    else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
                }
            }

            //See tag status and do the needed.
            if($type == "open") {//The starting of the tag '<tag>'
                $parent[$level-1] = &$current;
                if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
                    $current[$tag] = $result;
                    if($attributes_data) $current[$tag. '_attr'] = $attributes_data;
                    $repeated_tag_index[$tag.'_'.$level] = 1;

                    $current = &$current[$tag];

                } else { //There was another element with the same tag name

                    if(isset($current[$tag][0])) {//If there is a 0th element it is already an array
                        $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
                        $repeated_tag_index[$tag.'_'.$level]++;
                    } else {//This section will make the value an array if multiple tags with the same name appear together
                        $current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
                        $repeated_tag_index[$tag.'_'.$level] = 2;

                        if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
                            $current[$tag]['0_attr'] = $current[$tag.'_attr'];
                            unset($current[$tag.'_attr']);
                        }

                    }
                    $last_item_index = $repeated_tag_index[$tag.'_'.$level]-1;
                    $current = &$current[$tag][$last_item_index];
                }

            } elseif($type == "complete") { //Tags that ends in 1 line '<tag />'
                //See if the key is already taken.
                if(!isset($current[$tag])) { //New Key
                    $current[$tag] = $result;
                    $repeated_tag_index[$tag.'_'.$level] = 1;
                    if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data;

                } else { //If taken, put all things inside a list(array)
                    if(isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array...

                        // ...push the new element into that array.
                        $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;

                        if($priority == 'tag' and $get_attributes and $attributes_data) {
                            $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                        }
                        $repeated_tag_index[$tag.'_'.$level]++;

                    } else { //If it is not an array...
                        $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
                        $repeated_tag_index[$tag.'_'.$level] = 1;
                        if($priority == 'tag' and $get_attributes) {
                            if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well

                                $current[$tag]['0_attr'] = $current[$tag.'_attr'];
                                unset($current[$tag.'_attr']);
                            }

                            if($attributes_data) {
                                $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                            }
                        }
                        $repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken
                    }
                }

            } elseif($type == 'close') { //End of tag '</tag>'
                $current = &$parent[$level-1];
            }
        }

        return($xml_array);
    }

    /**
     * Function to sort array of array with an index.
     * @author: Sarath DR
     * 
     */
     
    function sort_array_by_field( &$data, $field)
    {
        $code = " if( \$a['$field'] == \$b['$field']  )return 0;  return ( strcmp( \$a['$field'] , \$b['$field']) > 0 ) ? 1 : -1 ;";
        usort($data, create_function('$a,$b', $code));
        return $data;
    }  
	
	
	
	
	
}