<?php 

class Zend_View_Helper_Polldetailhelper{	

	

	public function Polldetailhelper($row,$scoreDiv,$followstring,$followDiv,$ago,$myvoterow)

	{

		$poloption = new Application_Model_Polloption();

			$dbeeType=5;

			$loggedin=true;

			$imgH = '130';

			if(!$_COOKIE['user']) $loggedin=false;

			if($loggedin) $userloggedin='1'; else $userloggedin='0';

			$userid = $_COOKIE['user'];

			$db = $row['DbeeID'];		

			$isPoll=1;			

			$pres=$poloption->getpollvote($db);

			$totalvotesexist=count($pres);

			if($totalvotesexist==0) $showChart=false; else $showChart=true;			

			// GATHER ALL POLL VOTES TO SEND TO CHART

			if($showChart) {

				$count=1;			

				$pres = $poloption->getpolloption($db);

				foreach($pres as $prow):					

					$total=$poloption->getpolloptionvote($db,$prow['ID']);

					${'pollopt'.$count} = $prow['OptionText'];

					${'pollopt'.$count.'num'} = $total;

					$count++;

				endforeach;

			}	

			$participants='';		

			$TotalPartcpRes = count($poloption->getpartcpres($db));
			

			//----- TOTAL PARTICIPATING USERS ---------//			

			$PartcpRes = $poloption->getpartcpreslimit($db);			

			if(count($PartcpRes)>0) {

				$participants.='<div style="margin:20px 0 10px 0;"><div class="medium-font-bold" style="float:left; margin-right:20px;"><b>Members who have taken part so far</b></div>';

				if($TotalPartcpRes>19)

					$participants.='<div style="float:left; margin-right:20px;">|</div><div style="float:left"><a href="javascript:void(0);" onclick="javascript:OpenShadowbox(\'pollparticipants.php?poll='.$db.'\',\'\',\'325\',\'630\');">see all</a></div>';

				$participants.='</div><div class="next-line"></div>';

				$counter=1;

				foreach($PartcpRes as $PartcpRow):

					//$partStatusRow=mysql_fetch_object(mysql_query("select Status from tblUsers WHERE UserID=".$row->User,$conn));

				$partStatusRow = $poloption->getpartstatusrow($db);	

					$partStatus=$PartcpRow['Status'];

					if($partStatus==1) $partLinkStart='<a href="/user/'.$PartcpRow['Username'].'">';

					else $partLinkStart='<a href="javascript:void(0)" class="profile-deactivated" title="'.DEACTIVE_ALT.'" onclick="return false;">';

					$participants.=$partLinkStart.'<div class="follower-box-profile"><img src="'.BASE_URL_IMAGES.'/show_thumbnails.php?ImgName='.$PartcpRow['ProfilePic'].'&ImgLoc=userpics&Width=35&Height=35" border="0" /></div></a>';

					if($counter%19==0) $participants.='<div class="next-line"></div>';

					$counter++;

				endforeach;

				$participants.='<div class="next-line"></div>';

			}			

			if(!$loggedin) $user=0; // IF NOT LOGGED IN  				

			$myvoteres = count($myvoterow);

			if($myvoterow['Vote']) {				

				$voteopdisplay='style="display:block"'; $myvotedisplay='style="display:block"';				

				$vorteid = $myvoterow['Vote'];

				$vsql = $poloption->getvsql($vorteid);

				$votename = $vsql[0]['OptionText'];

				$myvotetext='<div align="center"><div class="medium-font-bold-grey">you voted</div><div class="large-font-orange">'.$votename.'</div></div>';
			}

			else { $voteopdisplay='style="display:block"'; $myvotedisplay='style="display:none"'; }

			// GATHER INFO RE YOUR VOTE FOR THIS POLL			

			$dbee_highlighted='<div class="maindb-user-infobox"><div class="small-font-bold">'.$row['Name'].'</div>'.$profileLinkStart.'<div style="margin:10px 0px 10px 0px; width:130px; height:'.$imgH.'px; background-image:url('.BASE_URL_IMAGES.'/show_thumbnails.php?ImgName='.$row['ProfilePic'].'&ImgLoc=userpics&Width=130&Height=130); background-repeat:no-repeat; background-position:center"></div></a>';

			if($showLinks)

				$dbee_highlighted.='<div><a href="#" class="small-link">history</a> | <a href="#" class="small-link">bio</a> | <a href="#" class="small-link">rankings</a></div>';

			$dbee_highlighted.=$social.'</div><div class="maindb-db-infobox"><div class="small-font-bold" style="margin:0 0 10px 20px;">'.$ago.$reportabuse.'</div><div style="margin-left:10px"><div class="maindb-text-wrapper" style="width:380px; min-height:220px; position:relative"><div class="maindb-type-poll"></div><div class="maindb-text" style="float:left; width:300px; margin-left:10px;"><div style="margin-bottom:15px">'.$row['PollText'].'</div><div><div style="position:absolute; bottom: 0; left: 50;"><div id="voteoptions" '.$voteopdisplay.'><div class="medium-font-bold" style="margin-bottom:10px; float:left;">Cast your vote</div><div id="voteerr">choose your vote first</div><div class="next-line"></div><div style="float:left;"><form>';

			$poRes = $poloption->getpores($db);			

			foreach($poRes as $poRow):	

			if($myvoterow['Vote']) { if($poRow['ID']==$myvoterow['Vote']) { $autoselect='checked="checked"'; $color=''; } else { $autoselect='disabled="disabled"'; $color='style="color:#666"'; } }

				$dbee_highlighted.='<div id="pollopt'.$poRow['ID'].'" '.$color.'><label><input type="radio" id="pollradio'.$poRow['ID'].'" name="pollradio" value="'.$poRow['ID'].'" '.$autoselect.' onclick="javascript:document.getElementById(\'poll-comment\').style.display=\'block\'">'.$poRow['OptionText'].'</label></div><div style="clear:both"></div>';

			endforeach;

			$dbee_highlighted.='</form><div class="maindb-addtofav" style="margin:20px 0 10px 0;"><a href="javascript:void(0)" rel="favourite-popup~300" class="poplight small-link addtofav-dbmain" onclick="javascript:addtofavourite()" alt="add to favourites" title="add to favourites">add to favourites</a></div></div>';

			if($loggedin) { // SHOW VOTE BUTTON ONLY IF LOGGED IN

				$dbee_highlighted.='<div id="button-vote" class="button-vote" style="float:left; position:absolute; bottom: 0; left: 175px; margin-bottom:50px;" onclick="javascript:castvote('.$db.');">vote</div>';

				$dbee_highlighted.='<div id="vote-submitted" class="vote-submitted" style="float:left; position:absolute; bottom: 0; left: 165px; margin-bottom:50px;">your vote submitted</div>';

			} else {

				$dbee_highlighted.='<div id="vote-submitted" class="vote-submitted" style="float:left; position:absolute; bottom: 0; left: 165px; margin-bottom:50px; display:block;">login to vote</div>';

			}

			$dbee_highlighted.='</div></div></div><div style="float:left; margin-left:25px;"><div class="medium-font-bold">Current Statistics</div><div id="PollPieChart" style="width:330px; height:230px;">';

			if(!$showChart) $dbee_highlighted.='No stats currently available';

			$dbee_highlighted.='</div></div></div>'.$followDiv.'<div id="poll-comment" style="float:left; margin-left:150px; display:none;"><textarea id="PollComment" class="roundedge-textbox fieldtext" style="width:280px; height:30px; margin-top:5px;" onKeyDown="limitText(\'PollComment\',500,\'pollcommcountdown\');" onKeyUp="limitText(\'PollComment\',500,\'pollcommcountdown\');" onfocus="if(this.value == \'write something to support your vote\') { this.value = \'\'; }" onblur="if(this.value == \'\') { this.value = \'write something to support your vote\'; }">write something to support your vote</textarea><br /><div id="pollcommcountdown" class="dbtextcountdown" style="margin:5px; color:#F9C10C; font-size:10px">500 limit</div></div><div id="maindb-scorewrapper" style="float:right; margin:5px 15px 0 0;"><div class="maindb-score-speecharrow"></div><div class="maindb-score">'.$scoreDiv.'<input type="hidden" id="hiddendb" value="'.$db.'"></div></div><div style="margin-left:160px">'.$participants.'</div></div><br style="clear:both; font-size:1px;">';

			return $dbee_highlighted;	

	

	}

	



}





?>



