<?php
class CronController extends IsloginController
{
	public function videoeventAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$result = $this->Myhome_Model->videoEventAttendies();
		if(!empty($result))
		{
			foreach ($result as $value)
			{
				$this->sendMailToVideoEvent($value);
				$this->Myhome_Model->updateVedioEvent(array('notification'=>1),$value['DbeeID']);
			}
			echo 'sent';
		}else
			echo 'No result';
	}
}