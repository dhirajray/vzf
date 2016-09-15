<?php

class Zend_View_Helper_Invitecnt{

	

	protected $request;

	

	/* public function __construct($request)

	{

		$this->request = $request;

	} */
	public function Invitecnt($dbeeid)
	{
		$dash_obj = new Admin_Model_Deshboard();
		$ddd = $dash_obj->inviteusergroupby($dbeeid);
		$classname = array('facebook'=>'socialSpecialIcons ssfbIcon','twitter'=>'socialSpecialIcons sstwIcon','linkedin'=>'socialSpecialIcons sslnIcon','dbee'=>'socialSpecialIcons ssdbIcon');
		$bg = array('dbee'=>'#FFCB0A','facebook'=>'#3A589B','linkedin'=>'#007AB9','twitter'=>'#20B8FF');
		$fntColor = array('dbee'=>'#000','facebook'=>'#fff','linkedin'=>'#fff','twitter'=>'#fff');
		//$dname = array('facebook','twitter','linkedin','db');
		$res ="<span class='countSocialSpecial'>";
		$i=0;
		foreach($ddd as $data):
			$res.="<span  rel='dbTip' title='".$data['dbeeidcnt']." Users Invited from ".$data['type']."' style='background:".$bg[$data['type']]."; color:".$fntColor[$data['type']]."'><strong class='".$classname[$data['type']]."'></strong><b> ".$data['dbeeidcnt']."</b></span>";
			$i++;
		endforeach;
		$res .="</span>";
		return $res;
}


}