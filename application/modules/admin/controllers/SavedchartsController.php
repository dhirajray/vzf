<?php

class Admin_SavedchartsController extends IsadminController
{

	private $options;
	
	/**
	 * Init
	 * 
	 * @see Zend_Controller_Action::init()
	 */
    public function init()
    {
	//print_r( $this->getInvokeArg('bootstrap')->getOptions());
	//die;
        $this->_options= $this->getInvokeArg('bootstrap')->getOptions();
        parent::init();
    }

    public function capturedivAction()
    {
        $u= new Admin_Model_Usergroup();     
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $request        =   $this->getRequest()->getParams();   
        if($request['caller']=='')
        {
            $convURL        =   BASE_URL."/admin/dashboard/postreport/m_-_xxp=t/".$request['encodeid']; 
        }
        else
        {
            $convURL        =   BASE_URL."/admin/savedcharts/listing"; 
        }
        

        $filteredData   =   substr($request['img_val'], strpos($request['img_val'], ",")+1);

        //Decode the string
        $unencodedData  =   base64_decode($filteredData);

        $dir            =   "postreports"; // Full Path
        $dir2 = time();
        $name = time().'.png';

        is_dir($dir) || @mkdir($dir) || die("Can't Create folder");

        
        //copy($unencodedData, $dir . DIRECTORY_SEPARATOR . $name);

        $stored = file_put_contents($dir . DIRECTORY_SEPARATOR . $name, $unencodedData);
        if($stored!='')
        {
          
            
            $dataval = array('clientID'=>clientID,'postid'=>$request['postid'],'userid'=>$this->_userid,'title'=>$request['cattitle'],'reportimage'=>$name, 'reporttype'=>2,'date'=>date('Y-m-d H:i:s'));
            $id = $this->myclientdetails->insertdata_global('tblpostreports',$dataval); 


            if($request['caller']=='')
            {
                if($id) $this->_redirect(BASE_URL."/admin/dashboard/postreport/m_-_xxp=t/".$request['encodeid'].'/scshot/1');  
                else $this->_redirect(BASE_URL."/admin/dashboard/postreport/m_-_xxp=t/".$request['encodeid'].'/scshot/0'); 
            }
            else{
                if($id) $this->_redirect(BASE_URL.'/admin/savedcharts/listing/scshot/1');  
                else $this->_redirect(BASE_URL.'admin/savedcharts/listing/scshot/0');
            }   
        }



        //echo '<img src="'.$request['img_val'].'" />';
        exit;
    }

    public function viewscreenshotsAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $request    = $this->getRequest()->getParams();   

        $retresult  =   ''; 

        //$postRec   =   $this->myclientdetails->passsqlquery('update tblpostreports set clientID='.clientID );

        if($request['reporttype']==2)
        {
            $postRec   =   $this->myclientdetails->passsqlquery('select * from tblpostreports where reporttype =2 AND userid='.$request['userid'].' order by id desc' );
        }
        else{
            $postRec   =   $this->myclientdetails->passsqlquery('select * from tblpostreports where reporttype =1 AND userid='.$request['userid'] .' AND postid = '.$request['dbid']);
        }       
        
        
        
       // $retresult = ' Total screen shot taken <strong>(<b>'.count($postRec).'</b>)</strong>';
        
        if(count($postRec)>0)
        {

            $retresult .='<thead><tr><th class="active" colspan="2"> Total saved reports <strong>(<b>'.count($postRec).'</b>)</strong>  </th></tr></thead><tbody>';
            foreach ($postRec as $key => $value) {

                if($value['title']=='') $title='Saved on ';
                else $title= '"'. $value['title'].'"  saved on ';

                $retresult .='<tr id="single_'.$value['id'].'" >    
                                    <td > '.$title.date('d-M-Y',strtotime($value['date'])).' at '.date('H:i',strtotime($value['date'])).'</td>

                                    <td  style="width:210px;text-align:center">
                                        <a class=" btn btn-mini  " target="_blank"   href="'.BASE_URL.'/postreports/'.$value['reportimage'].'" >
                                        <i class="fa fa-eye"> </i> View </a> 
                                        <a class=" btn btn-mini   " href="'.BASE_URL.'/admin/dashboard/downloadpdf/filepdf/'.$value['reportimage'].'/pathname/postreports" >
                                        <i class="fa fa-download"> </i> Download  </a>

                                        <a class=" btn btn-mini deletepostreport" id="'.$value['id'].'" href="javascript:void(0)" >Delete  </a>
                                    </td>
                                
                                </tr>';
            }
             $retresult .='</tbody>';
            
        }
        else{
            $retresult .='<div class="dashBlockEmpty"> reports not available.</div>';
        }
        
        echo $retresult;
        
        exit; 
    }
   
    public function listingAction()
    { 
        $this->view->data = $this->myclientdetails->getfieldsfromtable(array('groupname','id'),'adminchartgroup','1','1','','','id','DESC');
    }
    
    public function chartsdetailAction()
    {
        $norecord = '';
        $u= new Admin_Model_Usergroup();		
        $this->_helper->layout()->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(true);
		$request = $this->getRequest()->getParams();			
	    
        $chartRec   =   $this->myclientdetails->getfieldsfromtable(array('id','chartname','charttype','limit','chartname','datefrom','dateto','curdate'),'adminsavedcharts','groupid',$request['groupId']);
		
		$groudetailtotal = ' <strong>(<b>'.count($chartRec).'</b>)</strong>';

		
		$retresult	=	'';
		$offset1 = $offset+20;
		if(count($chartRec)>0)
		{
			foreach ($chartRec as $key => $value) {
				if($value['charttype']!='')
                {
                    if($value['datefrom']<'2000-01-01 05:30:00' && $value['dateto']<'2000-01-01 05:30:00')
                    {
                        $dateto   =   $value['curdate'];
                        $datefield ='<div class="dateShowOnHeader">up till: <strong>'.date('d-M-Y',strtotime($dateto)).'</strong></div>';
                    }
                    else
                    {
                        $datefrom =   $value['datefrom'];
                        $dateto   =   $value['dateto'];
                        $datefield ='<div class="dateShowOnHeader">From: <strong>'.date('d-M-Y',strtotime($datefrom)).'</strong> to: <strong>'.date('d-M-Y',strtotime($dateto)).'</strong></div>';
                    }

                    if($value['charttype']=='postvisiting'){
                        if(date('Y-m', strtotime($datefrom))==date('Y-m')){
                            $datefield ='<div class="dateShowOnHeader">up till: <strong>'.date('d-M-Y',strtotime($datefrom)).'</strong> </div>';
                        } else $datefield ='<div class="dateShowOnHeader"><strong>'.date('M-Y',strtotime($datefrom)).'</strong> </div>';
                    }
                    
                    if($value['charttype']=='socialreportchart' || $value['charttype']=='socialreportBARchart' || $value['charttype']=='continents' || $value['charttype']=='country' ){
                        $datefrom = $value['curdate'] ;
                    } 

                    if($value['charttype']=='userlogins' && $datefrom == $dateto){
                        if(date('Y-m', strtotime($datefrom))==date('Y-m')){
                            $datefield ='<div class="dateShowOnHeader">up till: <strong>'.date('d-M-Y',strtotime($datefrom)).'</strong> </div>';
                        } else $datefield ='<div class="dateShowOnHeader"><strong>'.date('M-Y',strtotime($datefrom)).'</strong> </div>';
                    }



                    $this->drawcharts($value['charttype'],'chart_'.$value['id'],$datefrom,$dateto,$value['limit']);
                    //echo $value['limit'];
                    $barchart ='';
                    if($value['limit']>9) $barchart = 'barchart-style="true"';
				    $retresult .='<li  id="single_'.$value['id'].'" '.$barchart.'>
                                        '.$datefield.'
                                   
                                    <a class="plBtn btn btn-mini plBtnRed  deletesinglechart" chartid="'.$value['id'].'" href="javascript:void(0);" >
                                    <i class="fa fa-times"> </i> </a>
                                    <div id="chart_'.$value['id'].'"></div>
    							</li>';
				}
			}
		}
        else{
    		$retresult .='<div class="dashBlockEmpty">no charts saved in this group</div>';
            $norecord  = 'yes';
    	}
		
		echo $retresult.'~'.$groudetailtotal.'~'.$norecord;
		
		exit;
    }
    
    public function drawcharts($type,$divid,$datefrom,$dateto,$ranglimit)
    {
        $layoutsobj = new Admin_Model_Layouts();
        $userRec    =   new Admin_Model_reporting();
        $deshboard  =   new Admin_Model_Deshboard();
        $scoreset =  $layoutsobj->scoringFromDb(); // taking value from database
        $adminChartMsg = array(

                   '1' =>  ' posts',
                   '2' =>  ' comments',
                   '3' =>  ' @user mentions',
                   '4' =>  ' following',
                   '5' =>  ' favourites',
                   '6' =>  ' scores',
                   '7' =>  " reposts",
                   '8' =>  ' new user registrations',
                   '9' =>  ' logged in',
                   '10' => ' messages',
                   '11' => ' questions',
                   '12' => ' answers',
                   '13' => ' following removed',
                   '14' => ' scores removed',
                   '15' => ' questions',
                   '16' => ' expert added',
                   '17' => ' expert removed',
                   '18' => ' group invitations',
                   '19' => ' group requests',
                   '20' => ' question removed',
                   '21' => ' answers',
                   '22' => ' league invitations',
                   '23' => ' total group',
                   '24' => ' feedbacks',
                   '25' => ' video broadcast attendances',
                   '26' => ' event attendances',
                   '27' => ' video broadcast attendances',
                   '28' => ' survey completions',
                   '29' => ' group requests',
                   '30' =>  ' post activated',
                   '31' =>  ' moved posts',
                   '32' =>  ' event attendances',
                   '33' =>  ' poll votes',
                   '34' =>  ' groups',
                   '35' =>  ' influenced by post',
                   '36' =>  ' influenced by comment'
                );
        $ranglimit = $ranglimit!='' ? $ranglimit : '';

        switch ($type) {

            case 'continents':
                $continentsArr  =   array();
                $continentsProvidersdata    =   array();
                $totalosRec =   $userRec->getcontinentusers('','','','',$datefrom);   // Total 
                foreach ($totalosRec as $key => $value) 
                {
                    $continentsProvidersdata[]  =   array('y'=> (int)$value['totcontinentcode']);
                    $continentsArr[]        =   array($this->myclientdetails->customDecoding($value['continentcode']));
                }
               
                      
                ?>
                <script type="text/javascript">
                    chartforbrowsersproviders(<?php echo json_encode($continentsProvidersdata) ?>,<?php echo json_encode($continentsArr)?>,'<?php echo $divid ?>',"Platform users by continent",'platform users across the world','No. of users ','','','users');
                </script>
                <?php
                break;

            case 'country':
                $Socialproviders    =   array();

                $totalsocialRec =   $userRec->getSocialUsers('','pie','','',$datefrom);   // Total Email ids
            
                foreach ($totalsocialRec as $key => $value)
                    $Socialproviders[]  =   array('name'=> $value['sharetype'],'y'=> (int)$value['totShare'],'sliced'=> false, 'selected'=> false);
                
                $SocialProvidersdata    =   json_encode($Socialproviders);
                      
                ?>
                <script type="text/javascript">
                    piechartproviders(<?php echo $SocialProvidersdata ?>,'<?php echo $divid ?>',"",' Social shares','totalsocialusers','socialusers','pie');
                </script>
                <?php
                break;

            case 'userlogins':
               // echo $datefrom.' == '.$dateto;
                
                if(date('Y-m', strtotime($datefrom))==date('Y-m')){
                    $condition = "DATE_FORMAT(`logindate`, '%Y-%m-%d') >= '".date('Y-m-01')."' AND DATE_FORMAT(`logindate`, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dateto))."'" ;
                }
                else if($datefrom == $dateto)
                {
                    $condition = "DATE_FORMAT(`logindate`, '%Y-%m')='".date('Y-m', strtotime($datefrom))."'";
                }
                else
                {
                    $condition = "DATE_FORMAT(`logindate`, '%Y-%m-%d') >= '".date('Y-m-d', strtotime($datefrom))."' AND DATE_FORMAT(`logindate`, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($dateto))."'" ;
                }

               $myQue = "select count(id) as totLogins, DATE_FORMAT(`logindate`, '%d') as theDate,count(distinct(log.userid)) as usersCount ,u.UserID,u.ProfilePic,u.Name,u.lname,u.Email from tbluserlogindetails as log INNER JOIN tblUsers AS u ON u.UserID=log.userid  where log.clientID =".clientID." AND ".$condition." group by theDate"; 
                
                $totalosRec =   $this->myclientdetails->passSQLquery ($myQue);   // Total 

                //print_r($totalosRec); exit;
                foreach ($totalosRec as $key => $value) 
                {
                    $loginProvidersdata[]   =   array('y'=> (int)$value['totLogins']);
                    $loginArr[]             =   array($value['theDate']);
                    $loginUsersArr[]        =   array($value['usersCount']);
                }
                $data['loginArrcategory']       =   json_encode($loginArr);
                $data['loginProvidersdata']     =   json_encode($loginProvidersdata);
                $data['loginUsersArr']          =   json_encode($loginUsersArr);

                ?>
                <script type="text/javascript">
                    chartforbrowsersproviders(<?php echo json_encode($loginProvidersdata) ?>,<?php echo json_encode($loginArr) ?>,'<?php echo $divid ?>', "",'platform users ','No. of users ','eachdaylogins','','','','',<?php echo json_encode($loginUsersArr) ?>,'','Date');
                </script>
                <?php

                break;
             case 'socialreportchart':
                $Socialproviders    =   array();

                $totalsocialRec =   $userRec->getSocialUsers('','pie','','',$datefrom);   // Total Email ids
            
                foreach ($totalsocialRec as $key => $value)
                    $Socialproviders[]  =   array('name'=> $value['sharetype'],'y'=> (int)$value['totShare'],'sliced'=> false, 'selected'=> false);
                
                $SocialProvidersdata    =   json_encode($Socialproviders);
                      
                ?>
                <script type="text/javascript">
                    piechartproviders(<?php echo $SocialProvidersdata ?>,'<?php echo $divid ?>',"",' Social shares','totalsocialusers','socialusers','pie');
                </script>
                <?php
                break;
            case 'socialreportBARchart':
                $totalBrowserRec    =   $userRec->getSocialUsers('','',5,'',$datefrom);   // Total Email ids
                $totalsharedata =   array();
                
                $dbdescription  =   array();
                $userUnameArr   =   array();
                $userPictureArr =   array();


                    
                foreach ($totalBrowserRec as $key => $value) 
                {
                    $totalsharedata[]   =   array('y'=> (int)$value['totdbs']);

                    if($value['Type']==1)
                        $dbdescription[] = htmlentities(substr($value['Text'],0,20));
                    else if($value['Type']==2) { 
                        $dbUserLinkDesc     =   !empty($value['UserLinkDesc']) ? $value['UserLinkDesc'] : $value['LinkTitle'];
                        $dbdescription[]    =   htmlentities(substr($dbUserLinkDesc,0,20));
                    }
                    else if($value['Type']==3) 
                        $dbdescription[]  =   htmlentities(substr($value['PicDesc'],0,'20'));
                    else if($value['Type']==4) { 
                        $dbVidDesc      =   $value['VidDesc'];
                        $dbdescription[]     =   htmlentities(substr($value['VidDesc'],0,'20'));
                    }
                    else if($value['Type']==5)            
                        $dbdescription[] =htmlentities(substr($value['PollText'],0,'20'));
                    

                    $dburlArr[]         =   array($value['dbeeid']);
                    $dbuserUnameArr[]   =   array($this->myclientdetails->customDecoding($value['Name']));
                    $dbuserImageArr[]   =   array($value['ProfilePic']);
                }

                $totsocialproviders     =   json_encode($totalsharedata);
                $uNameProvidersdata     =   json_encode($dbuserUnameArr);
                
                $dburlProvidersdata     =   json_encode($dburlArr);
                $userImageProvidersdata  =  json_encode($dbuserImageArr);
                $dbDescrProvidersdata   =   json_encode($dbdescription);        
                
                $socialdbProvidersdata  =   ($totsocialproviders);  
                $socialproviderscategory =  ($uNameProvidersdata);
                      
                ?>
                <script type="text/javascript">
                    chartforbrowsersproviders(<?php echo $socialdbProvidersdata ?>,<?php echo $socialproviderscategory ?>,'<?php echo $divid ?>',"",'Post author','Share count','socialusers','socialusers','  shares','',<?php echo $dburlProvidersdata ?>,<?php echo $userImageProvidersdata ?>,<?php echo $dbDescrProvidersdata ?>,'Post creator');
                </script>
                <?php
                break;
            case 'postvisiting':
                      
                ?>
                <script type="text/javascript">
                   callglobalajax('<?php echo $divid ?>','index','callingajaxcontainers', 'postvisiting','topdebateusers','month','<?php echo $datefrom?>',0,5); 
                </script>
                <?php
                break; 
            case 'similarinterest':
                    $catArrcategory     =   array();
                    $catArrPoints       =   array();
                    $catProvidersdata   =   array();

                    $catcmntArrcategory     =   array();
                    $a = array();   

                    $categories     =   $userRec->getallcategory();
                    $countcat = 1;
                    foreach ($categories as $keycat => $valuecat) 
                    {
                        
                        $catArrcategory[]   =  '<span class="categorychart" style="color:#'.rand(1000000,6).';font-weight:bold;" cateData="'.$valuecat['catname'].'" > '.$countcat++.'</span>'; //$setCat;//substr($catgry[0],0,10);
                        $catArrPoints[]     =   $valuecat['catname'];
                        $dbeecatdetailsRec  =   $userRec->getcategoryinterest($valuecat['catid'],'reportdbee');
                        $dbeesres[]         =   array((int)count($dbeecatdetailsRec));
                        $cmntcatdetailsRec  =   $userRec->getcategoryinterest($valuecat['catid'],'reportcomment');

                        $commentsres[]      =   array((int)count($cmntcatdetailsRec));
                    }
                    $catProvidersdata[] =   array('name'=>'unique posts', 'data' => $dbeesres,'stack'=>$valuecat['catid'],'stack'=>$valuecat['catid']);
                    $catProvidersdata[] =   array('name'=> 'user comments within posts', 'data' => $commentsres,'stack'=>$valuecat['catid'],'stack'=>$valuecat['catid']);
                
                    
                ?>
                <script type="text/javascript">
                   chartforcategories(<?php echo json_encode($catProvidersdata) ?>,<?php echo json_encode($catArrcategory)?>,'<?php echo $divid ?>',"User breakdown by category interest",'users interest on categories','No. of users','categoryusers','categoryusers','',<?php echo json_encode($catArrPoints) ?>); 
                </script>
                <?php
                break; 
            case 'dbee':
                    //echo $datefrom .','. $dateto.'<br>';
                    $TotalChartData = $this->deshboard->TotalChartData('', $dateto, $datefrom );
                    $imparr         =   explode('=',$TotalChartData );
                    $cdata          =   array( 
                                            array('name'=> 'Post','y'=> (int)$imparr[1],'sliced'=> false, 'selected'=> false),
                                            array('name'=> 'Comments','y'=>(int) $imparr[2],'sliced'=> false, 'selected'=> false),
                                            array('name'=> 'Scores','y'=> (int)$imparr[3],'sliced'=> false, 'selected'=> false),
                                            array('name'=> 'Groups','y'=> (int)$imparr[4],'sliced'=> false, 'selected'=> false)
                                        );
                    
                    $cdatajson  =   json_encode($cdata); 
                    //echo $divid;
                    ?>
                    <script type="text/javascript">
                        chartofdbees(<?php echo $cdatajson;?>,'<?php echo $divid ?>','Total platform activity ');
                    </script>
                    <?php
                break;
            case 'dbeetypes':
                
                    //$DbeeChartData  = $this->deshboard->DbeeChartData($dateto, $datefrom);    
                    $DbeeChartData  = $this->deshboard->PostBreakdown($dateto, $datefrom,$this->adminUserID);    
                    
                     $imparr        =   explode('=',$DbeeChartData );
                     $cdata         =   array( 
                                            array('name'=> 'Text','y'=> (int)$imparr[0],'sliced'=> false, 'selected'=> false),
                                            array('name'=> 'Link','y'=>(int) $imparr[1],'sliced'=> false, 'selected'=> false),
                                            array('name'=> 'Picture','y'=> (int)$imparr[2],'sliced'=> false, 'selected'=> false),
                                            array('name'=> 'Video','y'=> (int)$imparr[3],'sliced'=> false, 'selected'=> false),
                                            array('name'=> 'Polls','y'=> (int)$imparr[4],'sliced'=> false, 'selected'=> false)
                                        );
                    
                    $cdatajson  =   json_encode($cdata);
                     ?>
                    <script type="text/javascript">
                        chartofdbeetypes(<?php echo $cdatajson;?>,'<?php echo $divid ?>');
                    </script>
                    <?php

                   // echo $cdatajson.'~dbeetypes';       
                    break;    
            case 'score':
                    $ScoringChartData   = $this->deshboard->ScoringChartData($dateto, $datefrom); 
                    $layoutsobj = new Admin_Model_Layouts();
                    $scoreset =  $layoutsobj->scoringFromDb();
                    
                     $imparr        =   explode('=',$ScoringChartData );
                     $cdata         =   array( 
                                            array('name'=> $scoreset['Like'],'y'=> (int)$imparr[0],'sliced'=> false, 'selected'=> false),
                                            array('name'=> $scoreset['Love'],'y'=>(int) $imparr[1],'sliced'=> false, 'selected'=> false),
                                            array('name'=> $scoreset['Dis Like'],'y'=> (int)$imparr[3],'sliced'=> false, 'selected'=> false),
                                            array('name'=> $scoreset['Hate'],'y'=> (int)$imparr[4],'sliced'=> false, 'selected'=> false,'color'=>'#23uida')
                                        );
                    
                    $cdatajson  =   json_encode($cdata);
                    ?>
                    <script type="text/javascript">
                        chartofscores(<?php echo $cdatajson;?>,'<?php echo $divid ?>');
                    </script>
                    <?php  
                    break;
            case 'group':
                    $GroupChartData = $this->deshboard->GroupChartData($dateto, $datefrom);   
                    
                    $imparr         =   explode('=',$GroupChartData );
                    $cdata          =   array( 
                                            array('name'=> 'Open','y'=> (int)$imparr[0],'sliced'=> false, 'selected'=> false),
                                            array('name'=> 'Private','y'=>(int) $imparr[1],'sliced'=> false, 'selected'=> false),
                                            array('name'=> 'Request','y'=> (int)$imparr[2],'sliced'=> false, 'selected'=> false),
                                        );
                    
                    $cdatajson  =   json_encode($cdata);
                    ?>
                    <script type="text/javascript">
                        chartofgrops(<?php echo $cdatajson;?>,'<?php echo $divid ?>');
                    </script>
                    <?php     

                    break;        
            case 'signedcontainer':            
                ?>
                <script type="text/javascript">
                    callglobalajax('<?php echo $divid ?>','index','callingajaxcontainers', 'usersignupfromplateform','usersignup','<?php echo $datefrom;?>','<?php echo $dateto;?>','<?php echo $ranglimit;?>');  
                </script>
                <?php
                break;  
            case 'debatingcontainer':
                ?>
                <script type="text/javascript">
                    callglobalajax('<?php echo $divid ?>','index','callingajaxcontainers', 'topdebating','topdebateusers','<?php echo $datefrom;?>','<?php echo $dateto;?>','<?php echo $ranglimit;?>');  
                </script>
                <?php
                break; 
            case 'popularcontainer':
                ?>
                <script type="text/javascript">
                   callglobalajax('<?php echo $divid ?>','index','callingajaxcontainers', 'populardebate','populardebates','<?php echo $datefrom;?>','<?php echo $dateto;?>','<?php echo $ranglimit;?>');  
                </script>
                <?php
                break;             
            case $scoreset['Love']:
                    $livescoreData = $this->deshboard->gettopScoreusers($ranglimit,'owner',1,$datefrom,$dateto);
                    $heading = 'Most '.strtolower($scoreset['Love']);
                    $formatter = 'Total '.strtolower($scoreset['Love']).' count';

                    $scoreresult = $this->callscoringresultsforchart($livescoreData,$divid,$heading,$formatter);

                    $scorearr    = explode('~', $scoreresult);
                    //print_r($scorearr);

                     ?>
                    <script type="text/javascript">
                        chartforbrowsersproviders(<?php echo $scorearr[0];?>,<?php echo $scorearr[1];?>,'<?php echo $divid;?>','<?php echo $heading;?>','score on comments','Score couns','','','<?php echo $formatter;?>','user',<?php echo $scorearr[2];?>,<?php echo $scorearr[3];?>);
                    </script>
                    <?php
                    break;
            case $scoreset['Like']:
                    $livescoreData = $this->deshboard->gettopScoreusers($ranglimit,'owner',4,$datefrom,$dateto);
                    $heading = 'Most '.strtolower($scoreset['Like']);
                    $formatter = 'Total '.strtolower($scoreset['Like']).' count';

                    $scoreresult = $this->callscoringresultsforchart($livescoreData,$divid,$heading,$formatter);

                    $scorearr    = explode('~', $scoreresult);
                    //print_r($scorearr);

                     ?>
                    <script type="text/javascript">
                        chartforbrowsersproviders(<?php echo $scorearr[0];?>,<?php echo $scorearr[1];?>,'<?php echo $divid;?>','<?php echo $heading;?>','score on comments','Score count','','','<?php echo $formatter;?>','user',<?php echo $scorearr[2];?>,<?php echo $scorearr[3];?>);
                    </script>
                    <?php
                    break;        
            case $scoreset['Dis Like']:
                    $livescoreData = $this->deshboard->gettopScoreusers($ranglimit,'owner',4,$datefrom,$dateto);
                    $heading = 'Most '.strtolower($scoreset['Dis Like']);
                    $formatter = 'Total '.strtolower($scoreset['Dis Like']).' count';

                    $scoreresult = $this->callscoringresultsforchart($livescoreData,$divid,$heading,$formatter);

                    $scorearr    = explode('~', $scoreresult);
                    //print_r($scorearr);

                     ?>
                    <script type="text/javascript">
                        chartforbrowsersproviders(<?php echo $scorearr[0];?>,<?php echo $scorearr[1];?>,'<?php echo $divid;?>','<?php echo $heading;?>','score on comments','Score count','','','<?php echo $formatter;?>','user',<?php echo $scorearr[2];?>,<?php echo $scorearr[3];?>);
                    </script>
                    <?php
                    break;
            case $scoreset['F O T']:
                    $livescoreData = $this->deshboard->gettopScoreusers($ranglimit,'owner',3,$datefrom,$dateto);
                    $heading = 'biggest '.strtolower($scoreset['F O T']);
                    $formatter = 'Total '.strtolower($scoreset['F O T']).' count';

                    $scoreresult = $this->callscoringresultsforchart($livescoreData,$divid,$heading,$formatter);

                    $scorearr    = explode('~', $scoreresult);
                    //print_r($scorearr);

                     ?>
                    <script type="text/javascript">
                        chartforbrowsersproviders(<?php echo $scorearr[0];?>,<?php echo $scorearr[1];?>,'<?php echo $divid;?>','<?php echo $heading;?>','score on comments','Score count','','','<?php echo $formatter;?>','user',<?php echo $scorearr[2];?>,<?php echo $scorearr[3];?>);
                    </script>
                    <?php
                    break;
            case $scoreset['Hate']:
                    $livescoreData = $this->deshboard->gettopScoreusers($ranglimit,'owner',5,$datefrom,$dateto);
                    $heading = 'Most '.strtolower($scoreset['Hate']);
                    $formatter = 'Total '.strtolower($scoreset['Hate']).' count';

                    $scoreresult = $this->callscoringresultsforchart($livescoreData,$divid,$heading,$formatter);

                    $scorearr    = explode('~', $scoreresult);
                    //print_r($scorearr);

                     ?>
                    <script type="text/javascript">
                        chartforbrowsersproviders(<?php echo $scorearr[0];?>,<?php echo $scorearr[1];?>,'<?php echo $divid;?>','<?php echo $heading;?>','score on comments','Score count','','','<?php echo $formatter;?>','user',<?php echo $scorearr[2];?>,<?php echo $scorearr[3];?>);
                    </script>
                    <?php
                    break;
            case 'love':
                    $livescoreData = $this->deshboard->gettopScoreusers($ranglimit,'owner',1,$datefrom,$dateto);
                    $heading = 'Most loved';
                    $formatter = 'Total love count';

                    $scoreresult = $this->callscoringresultsforchart($livescoreData,$divid,$heading,$formatter);

                    $scorearr    = explode('~', $scoreresult);
                    //print_r($scorearr);

                     ?>
                    <script type="text/javascript">
                        chartforbrowsersproviders(<?php echo $scorearr[0];?>,<?php echo $scorearr[1];?>,'<?php echo $divid;?>','<?php echo $heading;?>','score on comments','Score count','','','<?php echo $formatter;?>','user',<?php echo $scorearr[2];?>,<?php echo $scorearr[3];?>);
                    </script>
                    <?php
                    break;   
            case 'alladmin':
                    $TotalChartData = $result = $this->deshboard->AllactivityAdmin($this->adminUserID,$datefrom, $dateto);

                    $imparr         =   explode('=',$TotalChartData );
                    $cdata          =   array( 
                                            array('name'=> 'Admin created user activity','y'=> (int)$imparr[1],'sliced'=> false, 'selected'=> false),
                                            array('name'=> 'User created user activity','y'=>(int) $imparr[2],'sliced'=> false, 'selected'=> false),
                                        );

                    $cdatajson  =   json_encode($cdata);

                    $totdata    =   (int)$imparr[1]+(int)$imparr[2]+(int)$imparr[3]+(int)$imparr[4]; 
                    //echo $cdatajson.'~alladmin~'.$totdata;
                     ?>
                     <script type="text/javascript">
                        chartofdbees(<?php echo $cdatajson;?>,'<?php echo $divid ?>',' Admin created user activity vs User created user activity');
                    </script>
                    <?php     
                     break; 
            case 'adminbreck':
                    $TotalChartData = $result = $this->deshboard->breakActivityAdmin($this->adminUserID,$dateto, $datefrom);
                    $totdata    =   ''; 
                    foreach ($TotalChartData as $key => $value) {
                        $breArr[]    = array('name'=>$adminChartMsg[$value['act_message']],'y'=> (int)$value['actTot'],'sliced'=> false, 'selected'=> false) ;
                        $totdata += (int)$value['actTot'];
                    }
                    $bdata = ($breArr);
                    
                    
                    $cdatajson  =   json_encode($bdata);

                    
                   ?>
                     <script type="text/javascript">
                        chartofdbees(<?php echo $cdatajson;?>,'<?php echo $divid ?>','Admin created user activity breakdown');
                    </script>
                    <?php     
                     break; 
            case 'platformbreck':
             
                    $TotalChartData = $result = $this->deshboard->breakActivityPlatform($this->adminUserID,$dateto, $datefrom);
                    $totdata    =   ''; 
                    foreach ($TotalChartData as $key => $value) {
                        $breArr[]    = array('name'=>$adminChartMsg[$value['act_message']],'y'=> (int)$value['actTot'],'sliced'=> false, 'selected'=> false) ;
                        $totdata += (int)$value['actTot'];
                    }

                    $cdatajson  =   json_encode($breArr);

                    
                   ?>
                     <script type="text/javascript">
                        chartofdbees(<?php echo $cdatajson;?>,'<?php echo $divid ?>','User created user activity breakdown');
                    </script>
                    <?php     
                     break;  
            case 'influence':
                    $TotalChartData = $result = $this->deshboard->breakInfluence($this->adminUserID,$dateto, $datefrom);
                    $totdata    =   ''; 
                    foreach ($TotalChartData as $key => $value) {
                        if($value['ArticleType']==0) $slice = 'Post influence';
                        else $slice = 'Comment influence';
                        $breArr[]    = array('name'=>$slice,'y'=> (int)$value['actTot'],'sliced'=> false, 'selected'=> false) ;
                        $totdata += (int)$value['actTot'];
                    }
                    $bdata = ($breArr);
                    
                    
                    $cdatajson  =   json_encode($bdata);

                    
                   ?>
                     <script type="text/javascript">
                        chartofdbees(<?php echo $cdatajson;?>,'<?php echo $divid ?>','Total platform influence');
                    </script>
                    <?php     
                     break;                              
            default:
                # code...
                break;
        }
    }
    public function callscoringresultsforchart($livescoreData,$divid)
    {
        foreach ($livescoreData as $key => $value) 
        {
            $scoretotProvidersdata[]    =   array('y'=> (int)$value['totscore']);
            $scuserNameArr[]        =   array($this->myclientdetails->customDecoding($value['username']));
            $scuserUnameArr[]       =   array($this->myclientdetails->customDecoding($value['uname']));
            $checkdbpic = $this->common_model->checkImgExist($value['image'],'userpics','default-avatar.jpg');
            $scuserPictureArr[] =   array($checkdbpic);
        }

        $scoretotproviderscategory  =   json_encode($scoretotProvidersdata);
        $scuNameProvidersdata   =   json_encode($scuserNameArr);
        $scuserNameProvidersdata    =   json_encode($scuserUnameArr);
        $scuserImageProvidersdata = json_encode($scuserPictureArr);
       
        return $scoretotproviderscategory."~".$scuNameProvidersdata."~".$scuserNameProvidersdata."~".$scuserImageProvidersdata;

        exit;
    }

    public function updatechartsgroupAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $request    =   $this->getRequest()->getPost();      
        $groupid    =   $request['groupid'];
        $groupname  =   $request['groupname'];
        $data = array('groupname'=>$groupname);
        $this->myclientdetails->updatedata_global('adminchartgroup',$data,'id',$groupid); 
        echo 1;
    }

    public function deletesavedchartsAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $request    =   $this->getRequest()->getPost();
        $groupid    =   $request['groupid'];
        if($request['calling']=='savedreport')
        {
            $filtdel = $this->myclientdetails->deletedata_global('tblpostreports','id',$groupid);
            if($filtdel)
            {
               echo true;
            } else
            {
                echo false;
            }
        }
        else if($request['calling']!='singlechart')
        {
            $filtdel    =   $this->myclientdetails->deletedata_global('adminchartgroup','id',$groupid);
            $filtatrdel =   $this->myclientdetails->deletedata_global('adminsavedcharts','groupid',$groupid);
            if($filtdel)
            {
               echo true;
            } else
            {
                echo false;
            }
        }
         
        else
        {
            $filtatrdel =   $this->myclientdetails->deletedata_global('adminsavedcharts','id',$groupid);
            if($filtatrdel)
            {
               echo true;
            } 
            else
            {
                echo false;
            }
        }    
        exit;
    }
   
   
    

}

