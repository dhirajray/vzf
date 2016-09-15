<?php
class LeagueController extends IsController
{
    public function init()
    {
        parent::init();
      
        if($this->plateform_scoring==1 && $this->IsLeagueOn==1){ 
            $this->_redirect('myhome');
        }
        $this->preCall();
    }
    public function preCall()
    {
        if (!$this->_userid) {
            $this->_redirect('myhome/logout');
        }
    }

    public function mainAction()
    {
        $loggedin = true;
        if (!$this->_userid)
            $loggedin = false;
        $request = $this->getRequest();
        if ($request->getpost('league'))
            $league = $request->getpost('league');
        else
            $league = 'mostfollowed';
        $this->view->user   = $this->_userid;
        $this->view->league = $league;
    }
    public function indexAction()
    {
        $request = $this->getRequest()->getParams();
        
        $lid = (int) $request['id'];
        
        $myleaguedbs = $this->leagueObj->getallleaguedbs($lid);
        
        
        //************ start of league score section ****************
        if (count($myleaguedbs) > 0) {
            $groupleage = $this->leagueObj->getCommentIdsofDbs($myleaguedbs, 'league');
            
            $mostlove = $this->leagueObj->getLeagereport($groupleage, 'love', '', $myleaguedbs[0]['Enddate']); // Love leage toppers
            $mostfot  = $this->leagueObj->getLeagereport($groupleage, 'fot', '', $myleaguedbs[0]['Enddate']); // Love leage toppers
            $mosthate = $this->leagueObj->getLeagereport($groupleage, 'hate', '', $myleaguedbs[0]['Enddate']); // Love leage toppers	
            
            $lovearr = explode('~', $mostlove);
            $fotarr  = explode('~', $mostfot);
            $hatearr = explode('~', $mosthate);
            
            //group arr
            $this->view->mostlove = $lovearr[0];
            $this->view->mostfot  = $fotarr[0];
            $this->view->mosthate = $hatearr[0];
            
            //my position
            $this->view->positionlove = $lovearr[1];
            $this->view->positionfot  = $fotarr[1];
            $this->view->positionhate = $hatearr[1];
            
            $this->view->leaguedbee = $lid;
            $this->view->leageinfo  = $this->leagueObj->getleaguedetail($lid);
            
            //************ End of league score section ****************
            
            $dbs     = $this->leagueObj->getleaguedbdetails($myleaguedbs);
            $lgusers = $this->leagueObj->leagueinvolvedusers($myleaguedbs);
            
            $this->view->dbsinlg = count($myleaguedbs);
            
            $this->view->dbs     = $dbs;
            $this->view->lgusers = $lgusers;
        }
    }
    
    
    public function leagueinitialdbeeAction()
    {
        $CurrDate                = date('Y-m-d H:i:s');
        $request                 = $this->getRequest();
        $lid                     = $request->getpost('lid');
        $this->view->leaguedbees = $this->leagueObj->getdbeeintialleague($lid, $this->_userid);
        $this->view->user        = $this->_userid;
        $response                = $this->_helper->layout->disableLayout();
        return $response;
    }
    public function getmembersleadgeAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->getMethod() == 'POST'){
            $response = $this->getResponse();
            $response->setHeader('Content-type', 'application/json', true);
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $result = array();
            $userId = $this->_userid;
            
            $myleagues = $this->leagueObj->memberleagues($userId);
            
            return $response->setBody(Zend_Json::encode($myleagues));
            exit;
        }
    }
    
    public function getleadgepositionsAction()
    {
        $response = $this->getResponse();
        $response->setHeader('Content-type', 'application/json', true);
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $request = $this->getRequest()->getParams();
        
        $result = array();
        $userId = $this->_userid;
        
        $groupleage = $this->leagueObj->getCommentIdsofDbs($request['dbid'], 'dbee');
        
        $mostlove = $this->leagueObj->getLeagereport($groupleage, 'love'); // Love leage toppers
        $mostfot  = $this->leagueObj->getLeagereport($groupleage, 'fot'); // Love leage toppers
        $mosthate = $this->leagueObj->getLeagereport($groupleage, 'hate'); // Love leage toppers
        
        $lovearr = explode('~', $mostlove);
        $fotarr  = explode('~', $mostfot);
        $hatearr = explode('~', $mosthate);
        
     /*   $result['ledge'] = '<div id="leaguePositionRight">
					<h2>League positions</h2>
					<ul class="tabLinks tabHeader">
						<li><a href="#" rel="lploved" class="active">Loved</a></li>
						<li><a href="#"  rel="lpRogues">Rogues</a></li>
						<li><a href="#" rel="lpPhilosophers">Philosophers</a></li>
					</ul>
					<div class="tabcontainer tabContainerWrapper">
						<div class="tabcontent" id="lploved" style="display:block">
							<ul class="lplisting">' . $lovearr[0] . '</ul>
						</div>
						<div class="tabcontent" id="lpRogues">
							<ul class="lplisting">' . $hatearr[0] . '</ul>
						</div>	
						<div class="tabcontent" id="lpPhilosophers">
							<ul class="lplisting">' . $fotarr[0] . '</ul>
						</div>	
					</div>
				</div>
				<div class="clearfix"></div>';*/

              $result['ledge'] = '   <div class="whiteBox leagueRSPosition">
        <h2>League Positions <a href="#" class="clseSideBr"></a></h2>
          <ul class="dbAccordion">
            <li class="active">
              <h3>'.$this->score->score1.'</h3>
              <ul class="dbAccordionData">
               ' . $lovearr[0] . '
              </ul>
            </li>
            <li>
              <h3>'.$this->score->score2.'</h3>
               <ul class="dbAccordionData">
                ' . $hatearr[0] . '
              </ul>
            </li>
            <li>
              <h3>'.$this->score->score3.'</h3>
               <ul class="dbAccordionData">
                ' . $fotarr[0] . '
              </ul>
            </li>
          </ul>
      </div>';
        
        return $response->setBody(Zend_Json::encode($result));
    }
}


