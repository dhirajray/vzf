<?php

class Home_SurveyController extends IsController
{

	private $options;

	/**
	 * Init
	 *
	 * @see Zend_Controller_Action::init()
	 */
	public function init()
	{		
		
		parent::init();
		$this->_options= $this->getInvokeArg('bootstrap')->getOptions();
		$this->deshboard = new Home_Model_Deshboard();
		$this->defaultimagecheck = new Home_Model_Common();
		$this->dash_obj = new Home_Model_Deshboard();
		if($this->IsSurveysOn==0){ 
            $this->_redirect('myhome');
        }
		
	}
	
	public function indexAction()
	{
		define(PAGE_NUM,30);
		$request = $this->getRequest()->getParams();		
		$start = $request['start']?$request['start']:'0';
		$spdbs = $this->deshboard->surveydbs($start,PAGE_NUM);
		$spdbstotal = $this->deshboard->surveydbstotal($start,PAGE_NUM);
		$this->view->spdbs = $spdbs;
		$this->view->spdbstotal = $spdbstotal;
		$this->view->start = $start;
		$this->view->deshboard = $this->deshboard;
	}

	public function surveydetailAction()
	{
		$this->view->dbeeid = $id = (int)$this->_request->getParam('id');
		$this->view->surveyDetails = $this->deshboard->surveydetails($id);
		$this->view->deshboard = $this->deshboard;
	}

	public function ajaxsurveydetailsAction()
	{
		$data1 = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$filter = new Zend_Filter_StripTags();
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$userid = $this->_request->getPost('userid');
			$surveyid = $this->_request->getPost('dbeeid');
			$this->view->dbeeid=$surveyid;
			$where = array('Dbeeid'=>$surveyid,'parentID'=>0);
			$surveyDetails = $this->myclientdetails->getAllMasterfromtable('tblSurveyquestion',array('id','content'),$where);
			
			$surveyAnswer = $this->deshboard->checkSurveyAnswers($surveyid,$userid);
			foreach ($surveyAnswer as  $ansvalue)
				$answerS[]=$ansvalue['answer_id'];
			
			if(!empty($surveyDetails))
			{
				$abcd =array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

				foreach ($surveyDetails as $value) 
				{
					$an=0;
					$content .= '<div class="questionRow" >
					<div class="col2"><ul><li class="questionName" data-id="'.$value['id'].'"><span class="mark">Question</span> <strong>'.$value['content'].'</strong></li>';		
					$result = $this->deshboard->surveyquestion($value['id']);


					foreach ($result as $data) 
					{
						if(in_array($data['id'],$answerS))
						{
							$checked=1;
							$dataResult = $this->deshboard->checkSurveySimilarAnswers($data['id'],$userid);
							if(!empty($dataResult))
							$viewSimilar = "<a href='javascript:void(0);' class='similarAnswer btn btn-black btn-mini' data-id='".$data['id']."'>Users who answered same</a>";
							else
							$viewSimilar='';
						}else
						{
							$checked=0;
							$viewSimilar='';
						}
						$content .= '<li data-id="'.$data['id'].'" data-selected="'.$checked.'" ><span class="mark mark-green" data-value="&#10004;">'.$abcd[$an].'</span> <strong>'.$data['content'].'</strong>'.$viewSimilar;


						$content .='</li>';

						$an++;
					}
					$an=0;
					$content .='</ul>';




					$sureyChartData   = $this->deshboard->SurveyChartData($value['id']);

					

					  
				     $innerarray=array();
				     $newarray= array();
				     


				     $r=0;
				     foreach($sureyChartData as $datas)
					 { 
						$imparr        =   explode('==',$datas);
				        $innerarray[]  =array('name'=> $abcd[$r],'y'=> (int)$imparr[1],'sliced'=> false, 'selected'=> false);

				        $r++;
				     }
				    
				     $raj['chartjson']=json_encode($innerarray);

				     $raj['chartid']=$value['id'];
				     $data1['json'][]=$raj;

					 $content .='</div><div id="'.$value['id'].'" class="col2"></div></div>';
				}

			}

			$data1['total'] = count($surveyDetails);
			$data1['status'] = 'success';
			$data1['content'] = $content;

			
		}
		else
		{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data1));
	}
	public function surveyreportAction()
	{
		$this->view->dashboard = $this->deshboard;
		$this->view->dbeeid = $id = (int)$this->_request->getParam('id');
		$result =  $this->deshboard->surveydetails($id);
		$this->view->surveyDetails = $result['0'];


		
	}

	public function editsurveyAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$filter = new Zend_Filter_StripTags();
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$question_id = (int) $this->_request->getPost('id');
			$text = stripcslashes($this->_request->getPost('text'));
			$this->dash_obj->surveyquestionUpdate($question_id,$text);
			$data['status'] = 'success';
			$data['message'] = 'survey has been updated successfully';
		}
		else
		{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));
	}

	public function editsurveytitleAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$filter = new Zend_Filter_StripTags();
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$dbeeid = (int) $this->_request->getPost('dbeeid');
			$data['surveyTitle'] = stripcslashes($this->_request->getPost('surveytitle'));
			$data['dburl'] = $this->makeSeo($data['surveyTitle']);
			$this->dash_obj->updatedata_global('tblDbees',$data,'DbeeID',$dbeeid);
			
			$data['status'] = 'success';
			$data['message'] = 'survey has been updated successfully';
		}
		else
		{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));
	}


	public function ajaxsimilaruserAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$filter = new Zend_Filter_StripTags();
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$request = $this->getRequest()->getPost();	
			$result =	$this->dash_obj->answerSimilaruserlist($request['answerid'],$request['ownerdataid']);
			$content = '';
			$u = new Home_Model_Usergroup();
			$grouprs = $u->list_groupall();
			if(count($result)>0){
				foreach ($result as $value)
				{
					$ProfilePic = $this->defaultimagecheck->checkImgExist($value['ProfilePic'],'userpics','default-avatar.jpg');
					
					$content .= "<div class='userFatchList boxFlowers' title='".$this->myclientdetails->customDecoding($value['Name'])."' socialFriendlist='true'>
					<label class='labelCheckbox'>
					<input type='checkbox' value='".$value['UserID']."' checkvalue='".$checkvalue."' class='inviteuser-search' name='groupuser'>
					<div class='follower-box'>
					<div class='usImg'><img class='img border' height='30' align='left' src='".IMGPATH."/users/small/".$ProfilePic."' border='0' /></div>
					".$this->myclientdetails->customDecoding($value['Name'])."
					</div>
					</label>
					</div></div>";
				}
				if(count($grouprs)>0){
					$grouplist ='<div id="surveyDialog"  title="Platform users">
					Group : <div class="select">
					<select id="similarusrid" class="gh-tb s-hidden"  name="orderfield" value="" maxlength="200">
					<option value="0"> - Select Group - </option>';
					foreach ($grouprs as $value):
					$grouplist .='<option value="'.$value['ugid'].'">'.$value['ugname'].' </option>';
					endforeach;
					$grouplist .='</select>
					<div class="styledSelect"> - Sort Results - </div>
					<ul class="options">';
					foreach ($grouprs as $value):
					$grouplist .='<li rel="'.$value['ugid'].'"> '.$value['ugname'].' </li>';
					endforeach;
					$grouplist .='</ul>
					</div>';
				}
			}else{
					
				$content .= "No record found";
			}
			$data['status'] = 'success';
			$data['content'] = $content;
			$data['grouplist'] = $grouplist;
		}
		else
		{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));

	}

	public function deletesurveypostAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$filter = new Zend_Filter_StripTags();
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$id = (int) $this->_request->getPost('id');
			$type = (int) $this->_request->getPost('type');
			if($type=='question')
				$this->dash_obj->surveyQuestiondelete($id);
			else
				$this->dash_obj->surveyAnswerdelete($id);

			$data['status'] = 'success';
			$data['message'] = 'Survey deleted successfully';
		}
		else
		{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));
	}

	public function deletesurveyAction()
	{
		$data = array();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$filter = new Zend_Filter_StripTags();
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$id = (int) $this->_request->getPost('id');

			$this->dash_obj->survedelete($id);
			$this->dash_obj->surveyAnswerbydbeeiddelete($id);
			$this->dash_obj->surveyQuestionbydbeeiddelete($id);

			$data['status'] = 'success';
			$data['message'] = 'Survey deleted successfully';
		}
		else
		{
			$data['status'] = 'error';
			$data['message'] = 'Some thing went wrong here please try again';
		}
		return $response->setBody(Zend_Json::encode($data));
	}

	public function downloadpdfAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$pdfName = $this->_request->getParam('filepdf');
		$file = Front_Public_Path."surveydoc/".$pdfName;
		if(file_exists($file) && $pdfName!='' && filesize($file)!=0)
		{
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.basename($file).'"'); 
			header('Content-Length: ' . filesize($file));
			readfile($file);
		}else{
			$this->_redirect('/');
		}
	}

	public function surveyreloadAction()
	{
		define(PAGE_NUM,10);
		$request    =   $this->getRequest()->getPost();
		$startss = $request['cnt']?$request['cnt']:'0';
		$response = $this->getResponse();
		$this->_helper->layout()->disableLayout();		
		$deshboard 	= new Home_Model_Deshboard();
		$cmnObj= new Home_Model_Common();
		$spdbstotal = $deshboard->getTotalDbee();
		$spdbs = $deshboard->surveydbs($startss,PAGE_NUM);
		$result ='';
		if (count($spdbs))
		{
			 foreach($spdbs as $value) :		
			 	$where = array('Dbeeid'=>$value["DbeeID"],'parentID'=>0);
				$surveyDetails = $this->myclientdetails->getAllMasterfromtable('tblSurveyquestion',array('id','content'),$where);
				$result .=	'<li id="remove_'.$value["DbeeID"].'"><div class="dataListWrapper"> 
					<div class="dataListbox">
						<div class="scoredListTitle">
						<a href="'.FRONT_URL.'/dbee/'.$value['dburl'].'">view</a> 
						<div class="scoredPostDate">Posted on  - '.date('d M y',strtotime($value['PostDate'])).'</div>								
						</div>
						<div class="scoredData">
							<div class="dbPost">'.$value['surveyTitle'].'&nbsp;</div>
						</div>';
					if($value['Active']==0){
						 $result.='<span style="color:red;" id="'.$value['DbeeID'].'" >NOTE: This survey is not yet published.</span>'; 
					}else{
				 	 echo '<span style="color:red;display:none;" id="'.$value['DbeeID'].'" >NOTE: This survey is not yet published.</span>'; }
					$result.='</div>
					<div class="listBtnsWrp">';
						if($value["surveyPdf"]!='')
							$result.='<a href="'.FRONT_URL.'/surveydoc/'.$value['surveyPdf'].'" class="btn btn-black btn-mini">View PDF</a> ';
						else if($value["surveyLink"]!='')
							$result.='<a href="'.$value["surveyLink"].'" class="btn btn-black btn-mini">View PDF</a> ';
						$counter = $deshboard->checkUserCompleteSurvey($value["DbeeID"]);

						if($counter==0)
							$result.='<div class="addsurveyQuestion btn btn-green btn-mini" dbeeid="'.$value['DbeeID'].'" count-id="'.count($surveyDetails).'" data-title="'.$value['surveyTitle'].'" id="addsurveyQuestion'.$value['DbeeID'].'" status="'.$value['Active'].'" >Add question</div> ';
						else
							$result.='<div class="btn btn-green btn-mini disabled" rel="dbTip" title="You cannot add more questions to a survey once it has been completed by any user" dbeeid="'.$value['DbeeID'].'" >Add question</div> ';

						$result.='<a href="'.BASE_URL .'home/surveydetail/id/'.$value["DbeeID"].'/'.$value["dburl"].'" class="btn btn-yellow btn-mini">Survey details</a>
						<a href="'.BASE_URL .'home/surveyreport/id/'.$value["DbeeID"].'/'.$value["dburl"].'" class="btn btn-yellow btn-mini">Survey report</a>
						<a href="javascript:void(0);"  data-id="'.$value["DbeeID"].'" class="btn btn-danger btn-mini deletesurvey">Delete</a> ';
						if($value["Active"]==0){
							$result.='<a href="javascript:void(0);" status="'.$value["Active"].'"  data-id="'.$value["DbeeID"].'" id="publish'.$value["DbeeID"].'"  class="btn btn-green btn-mini publishSurvey">Publish</a> ';
						}else
						{ 
							$result.'=<a href="javascript:void(0);" status="'.$value["Active"].'" data-id="'.$value["DbeeID"].'" id="publish'.$value["DbeeID"].'"  class="btn btn-green btn-mini publishSurvey">UnPublish</a> ';
						}
						$result.'=<span id="QuestionsCount'.$value["DbeeID"].'">Questions: '.count($surveyDetails).'</span>'; 
					$result.='</div>
				</div>
			</li>';
			unset($where);
			endforeach;

			if($spdbstotal>10){
				$fft = (int)$startss+10;
				$result .='<div id="vewpagebottom" style="text-align:center;padding: 20px 2%;font-weight:bold;font-size:16px;clear:both;">
				<a id="viewmoresurvey" offset="'. $fft.'">View More </a></div>';
			}
		}else{
			$result .='<div style="padding: 20px 2%;color: #CCCCCC;font-size: 20px;margin-top:20px;clear:both;text-align: center; width: 100%;">No post found!</div>';
		}
		echo $result;			
		return;
	}

	public function createsurveyAction()
	{
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$request = $this->getRequest()->getParams();
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$deshboard 	= 	new Home_Model_Deshboard();
			$common	= 	new Home_Model_Common();
			$surveyTitle = stripcslashes($this->_request->getPost('surveyTitle'));
			$surveyLink = $this->_request->getPost('pdflink');
            $dburl = $this->makeSeo($surveyTitle,'','save');
            //print_r($_POST); exit;

            if($surveyTitle=='')
            {
            	$data['status'] = 'error';
            	$data['message'] = 'Please enter your survey title';
            }
            else
            {
	        	$output_dir = Front_Public_Path.'surveydoc/';
				if(isset($_FILES["file"]) && $_FILES["file"]["name"]!='' && $surveyLink=='')
				{
					if($_FILES["file"]["name"]!='') //single file
					{
						$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
				 	 	$fileName	=	strtolower(time().mt_rand(1,79632).'.'.$ext);
				 		move_uploaded_file($_FILES["file"]["tmp_name"],$output_dir.$fileName);
				    	$ret[]= $fileName;
					}
					$surveyLink = '';
				}else
				 	$fileName='';

				$pdftitle = $this->_request->getPost('pdftitle');
				
				$dataArray = array('Type'=>'7','User'=>$this->adminUserID,
								  'surveyTitle'=>$surveyTitle,'surveyPdf'=>$fileName,
								  'surveyLink'=>$surveyLink,'surveyPdfTitle'=>$pdftitle,
								  'PostDate'=>date('Y-m-d H:i:s'),'LastActivity'=>date('Y-m-d H:i:s'),
								  'dburl'=>$dburl,'clientID'=>clientID,'Active'=>0);
				
				$insspecial = $deshboard->insertdata_global('tblDbees',$dataArray);	
				
				$data['surveyid'] = $insspecial;
				$data['surveyTitle'] = $surveyTitle;

				$data['status'] = 'success';
				$data['message'] = 'successfully updated';
		    }

			return $response->setBody(Zend_Json::encode($data));
		}
	}

	public function createsurveyquestionAction()
	{
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$request = $this->getRequest()->getParams();
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$deshboard 	= 	new Home_Model_Deshboard();
			$common	= 	new Home_Model_Common();
			$question = $this->_request->getPost('question');
			$dbeeid = $this->_request->getPost('dbeeid');
			$answer = $this->_request->getPost('answer');
			if($question=='')
			{
				$data['status'] = 'error';
				$data['message'] = 'Please enter question';

			}else if($dbeeid==''){

				$data['status'] = 'error';
				$data['message'] = 'Please enter answer';

			}elseif (empty($answer)) {
				$data['status'] = 'error';
				$data['message'] = 'Please enter answer';
			}
            else if($question!='')
            {
				$questionArray =	array('userID'=>$this->adminUserID,
										  'Dbeeid'=>$dbeeid,
										  'content'=>$question,
										  'parentID'=>0,
										  'status'=>1,
										   'clientID'=>clientID,
										  'timestamp'=>date('Y-m-d H:i:s'));
				
				$questionid = $deshboard->insertdata_global('tblSurveyquestion',$questionArray);
				$abcd =array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
				$an = 0;	
				foreach ($answer as $value) 
				{
					if($value!='')
					{
						$dataArray =	array('userID'=>$this->adminUserID,
											'Dbeeid'=>$dbeeid,
											'content'=>$value,
											'parentID'=>$questionid,
											'status'=>1,
											 'clientID'=>clientID,
											'timestamp'=>date('Y-m-d H:i:s'));

						$insspecial = $deshboard->insertdata_global('tblSurveyquestion',$dataArray);
						$answerlist.='<li data-id="'.$insspecial.'"><span class="mark mark-green">'.$abcd[$an].'</span> <strong>'.$value.'</strong></li>';
					}
					$an++;
				}
				$content ='<div class="questionRow questionsWrp">
				<ul><li data-id="'.$questionid.'" class="questionName"><span class="mark">Question</span> <strong>'.$question.'</strong></li>'.$answerlist.'</ul>
				</div>';
				$data['status'] = 'success';
				$data['message'] = 'successfully updated';
				$data['content'] = $content;
				$where = array('Dbeeid'=>$dbeeid,'parentID'=>0);
				$surveyDetails = $this->myclientdetails->getAllMasterfromtable('tblSurveyquestion',array('id','content'),$where);
				$data['count'] = count($surveyDetails);
			}

			
			return $response->setBody(Zend_Json::encode($data));
		}
	}
	public function publishsurveyAction()
	{

		$response = $this->getResponse();
		$response->setHeader('Content-type', 'application/json', true);
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$request = $this->getRequest()->getParams();
		if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST')
		{
			$dbeeid = $this->_request->getPost('surveyid');
			$status = $this->_request->getPost('status');
			
			if($status==0)
				$statuss = 1;
			else
				$statuss = 0;

			$myData['Active'] =$statuss;
			$where['DbeeID'] = $dbeeid;
			$this->myclientdetails->updateMaster('tblDbees',$myData,$where);

			if($status==1)
				$message = 'survey unpublished successfully';
			else
				$message = 'survey published successfully';

			$data['status'] = 'success';
			$data['message'] = $message;
		}
		return $response->setBody(Zend_Json::encode($data));
	}
	
}
