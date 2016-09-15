<?php

class Zend_View_Helper_Dbeeicon{



	protected $request;



	/* public function __construct($request)

	 {

	$this->request = $request;

	} */

	public function Dbeeicon($CommentsNum=0,$Type)

	{

		

		if($Type!='5') {

			// SELECT NUMBER OF COMMENTS FOR THIS DBEE			

			if($CommentsNum>=0 && $CommentsNum<=5) {

				$dbstate='icon-db-cool'; $dbstatetitle='cool '.POST_NAME.'';

			}

			elseif($CommentsNum>=6 && $CommentsNum<=10) {

				$dbstate='icon-db-warm'; $dbstatetitle='warm '.POST_NAME.'';

			}

			elseif($CommentsNum>=11 && $CommentsNum<=20) {

				$dbstate='icon-db-hot'; $dbstatetitle='hot '.POST_NAME.'';

			}

			elseif($CommentsNum>20) {

				$dbstate='icon-db-burning'; $dbstatetitle='burning '.POST_NAME.'';

			}

			

		} else {

			//----- TOTAL VOTES ---------//		

			if($CommentsNum>=0 && $CommentsNum<=5) {

				$dbstate='icon-db-cool'; $dbstatetitle='cool '.POST_NAME.'';

			}

			elseif($CommentsNum>=6 && $CommentsNum<=10) {

				$dbstate='icon-db-warm'; $dbstatetitle='warm '.POST_NAME.'';

			}

			elseif($CommentsNum>=11 && $CommentsNum<=20) {

				$dbstate='icon-db-hot'; $dbstatetitle='hot '.POST_NAME.'';

			}

			elseif($CommentsNum>20) {

				$dbstate='icon-db-burning'; $dbstatetitle='burning '.POST_NAME.'';

			}

		}

		

		$iconarr = array($dbstate,$dbstatetitle);

		

	return $iconarr;



	}

}