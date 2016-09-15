<?php
class Zend_View_Helper_Pollhelper{	 
	
	public function Pollhelper($dbeeid)
	{
		$poloption = new Application_Model_Polloption();		
			
				$totalvotesobj = $poloption->getpollvote($dbeeid);						
				  $totalvotes = $totalvotesobj[0]['cnt'];			
				$pres = $poloption->getpolloption($dbeeid);	
				   $colorRadio =  array('#3366cc' ,'#dc3912',  '#ff9900', '#109618');
      				$colorcount = 0;		
				foreach($pres as $prow):
					if($totalvotes>=0) {					
					 $psrid = $prow['ID'];
					$totalobj = $poloption->getpolloptionvote($dbeeid,$psrid);
					 $total = $totalobj[0]['cnt'];
					 //$totalvotes;
					//$total =1;
					 if($totalvotes){
						$percent=($total/$totalvotes)*100;
					}else{
						$percent='';
					}
						$width=round($percent,1);
						$stats.='<div class="pollstatsbar-wrapper">';
						if(round($percent)>0) $stats.='<span class="checkcolorSymbol" style="background:'. $colorRadio[$colorcount].'"><span class="pollPercentValue">'.round($percent,1).'%</span></span>'; 
						else $stats.='<span class="checkcolorSymbol" style="background:'. $colorRadio[$colorcount].'"><span class="pollPercentValue"></span></span>';
						$stats.='<span class="pollLableTxt">'.$prow['OptionText'].'</div></span>';
					} /*else {
						$stats.='<div class="pollstatsbar-wrapper"></div><div>'.$prow['OptionText'].'</div>';
					}*/
					$colorcount++;
				endforeach;
				
			
			return $stats;	
	
	}
	

}
?>