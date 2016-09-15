<?php



class CustomsearchController extends IsController

{

	public function init()

	{

		$namespace = new Zend_Session_Namespace();

	

		/* if($namespace->userid=='')

		 {

		$this->_helper->redirector->gotosimple('index','index',true);

		} */

	}



public function indexAction()

	{

		//require_once('googlecustomsearch/src/Google/CustomSearch.php');

		$request = $this->getRequest();

		

		$searchterm= $request->getpost('searchterm');

		$startindex= $request->getpost('startindex');

		

		$return='';

		

		$search = new Google_CustomSearch($searchterm);

		//$search->setApiKey('AIzaSyBNL7GgY6dl_KkZJMK5Nj5ZydbqvB85sWg');

		//$search->setCustomSearchEngineId('007037843371790043183:fjabb9wnvra');

		

		$search->setApiKey('AIzaSyD0Z2GTnJBorv79P-BFTvj_iszAuW2eRVk');

		$search->setCustomSearchEngineId('002883075482518213413:-y2ynbajzlk');

		

		$search->setStartIndex($startindex);

		$response = $search->getResponse();

		$pages = $response->getPages();

		

		$return.='<div style="text-align:right">';

		foreach($pages as $key => $page) {

			$startIndex=$page['startIndex'];

			if($startindex==$startIndex) { $bold='<b>'; $boldend='</b>'; } else { $bold=''; $boldend=''; }

			$return.='<a href="javascript:void(0)" onclick="javascript:searchgooglelinkdb('.$startIndex.')">'.$bold.$page['label'].$boldend.'</a>&nbsp;&nbsp;';

		}

		$return.='</div>';

		

		if ($response->hasResults()) {

		    foreach($response->getResults() as $result) {

		        $return.='<a href="'.$result->getLink().'" target="_blank"><div class="googleresult-title">'.$result->getTitle() . '</div></a><div class="googleresult-link">' . $result->getLink() . '</div><div><div style="float:left;  width: 85%;">' . $result->getSnippet() . '</div><div class="button-yellow" style="margin:0 0 0 5px; float:left;" onclick="javascript:startdbfromlink(\''.$result->getLink().'\')">add link</div></div><div class="next-line"></div><div style="width:10px; height:20px;"></div>';

		    }

		}

		//$queries = $response->getQueries();

		//foreach($queries as $query) {

		//	$return.=$query['request'];

		//}

		

		//$return.=print_r($queries);

		//$return .= '<br><br>'.print_r($response->getQueries());

		echo $return;

		$response = $this->_helper->layout->disableLayout();

		return $response;

	}    

	



	

}



