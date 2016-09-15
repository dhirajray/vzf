<?php

class Adminlogin_CronController extends IsloginController
{

    /**
     * Init
     * 
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
        parent::init();
        $this->myclientdetails = new Admin_Model_Clientdetails();
    }



     public function cronAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        require_once('parsecsv/parsecsv.lib.php');
        # create new parseCSV object.
        $csv = new parseCSV();


        # Parse '_books.csv' using automatic delimiter detection...
        $csv->auto('reevoo_agdata_28012016.csv');
        foreach ($csv->data as $key => $row): 

            $userID = $this->UserDataCheck($row['soon_to_be_encrypted_email_address'],$row['first_name']);
            $data['UserID'] =  $userID;
            $data['clientID'] =  clientID;
            $data['Comment'] =  nl2br($row['text_answer']);
            $data['CommentDate'] =  $row['submit_date'];
            if(trim($row['image_url_answer'])!=''){
                $data['Pic'] = $this->getRevooPic($row['image_url_answer']);
            }else{
                $data['Pic'] = '';
            }
            $data['Type'] = 1;
            $data['fingerprint'] =  $row['text_response_fingerprint'];
            $data['DbeeID'] =  $this->getPostIdByTag($row['tag2']);
            $this->postCommentClientData($data);
          
        endforeach;
    }

    public function UserDataCheck($uEmail,$first_name)
    {

      $password = getrandmax();
      $userModal    =   new Admin_Model_User();
      $encodedEmail = $this->myclientdetails->customEncoding($uEmail);
      $encodedName = $this->myclientdetails->customEncoding($first_name);
      $userData    =  $userModal->chkUsersExists($encodedEmail);
      if(count($userData)<1)
      {
        $spuname    =  explode('@', $uEmail);
        $usname   = $spuname[0].rand(1000,9999);
        $dataval  = array(
              'clientID' => clientID,
              'Name'=> $encodedName,
              'full_name'=> $encodedName,
              'Email'=>$encodedEmail,
              'Pass'=> '',//$this->_generateHash($password),
              'Username'=> $this->myclientdetails->customEncoding($usname),
              'Signuptoken'=>'',
              'RegistrationDate'=> date("Y-m-d H:i:s"),
              'Status' => 0,
              'emailsent'=>0,
              'fromcsv'=>0,
              'lastcsvrecord'=>0,
              'usertype'=>96,
              );
        return $userModal->insertdata($dataval);
      }
      else {
          return $userData[0]['UserID'];
      }
    }

    public function postCommentClientData($data)
    {
        $this->myclientdetails->insertdata_global('tblDbeeComments', $data);
        print_r($data);
    }


    public function getPostIdByTag()
    {
        return 2199;
    }


    public function getRevooPic($pics)
    {

        if($pics)
        {
            $picture = time().'.png';
            $dir = ABSIMGPATH."/imageposts/".$picture;

            $storeFolder    = ABSIMGPATH."/imageposts/";
            chmod($storeFolder , 0777);
            $iscopy = copy($pics, $dir);

            $filename       = ABSIMGPATH."/imageposts/medium/".$picture;
            $filename1      = ABSIMGPATH."/imageposts/small/".$picture;
            list($width,$height) = getimagesize(IMGPATH."/imageposts/".$picture);
            $src = imagecreatefrompng(IMGPATH."/imageposts/".$picture);
            if($iscopy)
            {
                if($width < 484)
                {                   
                    $medium=copy($storeFolder.$picture, $filename);

                    if($width < 200)
                    {                     
                      $medium=copy($storeFolder.$picture, $filename1);  
                    }
                    else
                    {
                        $newwidth1=200;
                        $newheight1=($height/$width)*$newwidth1;
                        $tmp1=imagecreatetruecolor($newwidth1,$newheight1);
                        $red = imagecolorallocate($tmp1, 255, 0, 0);
                        $black = imagecolorallocate($tmp1, 0, 0, 0);
                        imagecolortransparent($tmp1, $black);
                        imagecopyresampled($tmp1,$src,0,0,0,0,$newwidth1,$newheight1,$width,$height);
                        $quality = round(abs((70 - 100) / 11.111111));                      
                        imagepng ($tmp1,$filename1,$quality);   
                        imagedestroy($src);                 
                        imagedestroy($tmp1);
                    }

                }
                else
                {
                    $newwidth=484;
                    $newheight=($height/$width)*$newwidth;
                    $tmp=imagecreatetruecolor($newwidth,$newheight);

                    $newwidth1=200;
                    $newheight1=($height/$width)*$newwidth1;
                    $tmp1=imagecreatetruecolor($newwidth1,$newheight1);
                    $red = imagecolorallocate($tmp1, 255, 0, 0);
                    $black = imagecolorallocate($tmp1, 0, 0, 0);
           
                    imagecolortransparent($tmp1, $black);
                    imagecolortransparent($tmp, $black);
                    

                    imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
                    imagecopyresampled($tmp1,$src,0,0,0,0,$newwidth1,$newheight1,$width,$height);
                    
                    $quality = round(abs((70 - 100) / 11.111111));
                    imagepng ($tmp,$filename,$quality);
                    imagepng ($tmp1,$filename1,$quality);
                    imagedestroy($src);
                    imagedestroy($tmp);
                    imagedestroy($tmp1);
                }

                return $picture;
            }else{
                return '';
            }

        }
    }

}

