<?
function is_ie() {
	return(eregi("MSIE", $_SERVER['HTTP_USER_AGENT']));
}

function is_chrome() {
	return(eregi("chrome", $_SERVER['HTTP_USER_AGENT']));
}

function is_safari() {
	return(eregi("safari", $_SERVER['HTTP_USER_AGENT']));
}

function is_macsafari() {
	return(eregi("Mac OS", $_SERVER['HTTP_USER_AGENT']));
}

function cleanText($str)
{
	$str=str_replace("&","&amp;",$str);
	return $str;
}

function _date_range_limit($start, $end, $adj, $a, $b, $result)
{
    if ($result[$a] < $start) {
        $result[$b] -= intval(($start - $result[$a] - 1) / $adj) + 1;
        $result[$a] += $adj * intval(($start - $result[$a] - 1) / $adj + 1);
    }

    if ($result[$a] >= $end) {
        $result[$b] += intval($result[$a] / $adj);
        $result[$a] -= $adj * intval($result[$a] / $adj);
    }

    return $result;
}

function _date_range_limit_days($base, $result)
{
    $days_in_month_leap = array(31, 31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    $days_in_month = array(31, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

    _date_range_limit(1, 13, 12, "m", "y", $base);

    $year = $base["y"];
    $month = $base["m"];

    if (!$result["invert"]) {
        while ($result["d"] < 0) {
            $month--;
            if ($month < 1) {
                $month += 12;
                $year--;
            }

            $leapyear = $year % 400 == 0 || ($year % 100 != 0 && $year % 4 == 0);
            $days = $leapyear ? $days_in_month_leap[$month] : $days_in_month[$month];

            $result["d"] += $days;
            $result["m"]--;
        }
    } else {
        while ($result["d"] < 0) {
            $leapyear = $year % 400 == 0 || ($year % 100 != 0 && $year % 4 == 0);
            $days = $leapyear ? $days_in_month_leap[$month] : $days_in_month[$month];

            $result["d"] += $days;
            $result["m"]--;

            $month++;
            if ($month > 12) {
                $month -= 12;
                $year++;
            }
        }
    }

    return $result;
}

function _date_normalize($base, $result)
{
    $result = _date_range_limit(0, 60, 60, "s", "i", $result);
    $result = _date_range_limit(0, 60, 60, "i", "h", $result);
    $result = _date_range_limit(0, 24, 24, "h", "d", $result);
    $result = _date_range_limit(0, 12, 12, "m", "y", $result);

    $result = _date_range_limit_days($base, $result);

    $result = _date_range_limit(0, 12, 12, "m", "y", $result);

    return $result;
}

/**
 * Accepts two unix timestamps.
 */
function _date_diff($one, $two)
{
    $invert = false;
    if ($one > $two) {
        list($one, $two) = array($two, $one);
        $invert = true;
    }

    $key = array("y", "m", "d", "h", "i", "s");
    $a = array_combine($key, array_map("intval", explode(" ", date("Y m d H i s", $one))));
    $b = array_combine($key, array_map("intval", explode(" ", date("Y m d H i s", $two))));

    $result = array();
    $result["y"] = $b["y"] - $a["y"];
    $result["m"] = $b["m"] - $a["m"];
    $result["d"] = $b["d"] - $a["d"];
    $result["h"] = $b["h"] - $a["h"];
    $result["i"] = $b["i"] - $a["i"];
    $result["s"] = $b["s"] - $a["s"];
    $result["invert"] = $invert ? 1 : 0;
    $result["days"] = intval(abs(($one - $two)/86400));

    if ($invert) {
        _date_normalize($a, $result);
    } else {
        _date_normalize($b, $result);
    }

    return $result;
}

// FUNCTION TO SORT AN ARRAY
function sortmddata($array, $by, $order, $type)
{
	//$array: the array you want to sort
	//$by: the associative array name that is one level deep
	////example: name
	//$order: ASC or DESC
	//$type: num or str
		   
	$sortby = "sort$by"; //This sets up what you are sorting by
	$firstval = current($array); //Pulls over the first array
	$vals = array_keys($firstval); //Grabs the associate Arrays
	
	foreach ($vals as $init)
	{
		$keyname = "sort$init";
		$$keyname = array();
	}
	
	//This was strange because I had problems adding
	//Multiple arrays into a variable variable
	//I got it to work by initializing the variable variables as arrays
	//Before I went any further
	
	foreach ($array as $key => $row)
	{   
		foreach ($vals as $names)
		{
			$keyname = "sort$names";
			$test = array();
			$test[$key] = $row[$names];
			$$keyname = array_merge($$keyname,$test);   
		}
	}
	
	//This will create dynamic mini arrays so that I can perform
	//the array multisort with no problem
	//Notice the temp array... I had to do that because I
	//cannot assign additional array elements to a
	//varaiable variable           
	
	if($order == "DESC")
	{   
		if ($type == "num")	array_multisort($$sortby,SORT_DESC, SORT_NUMERIC,$array);
		else array_multisort($$sortby,SORT_DESC, SORT_STRING,$array);
	} 
	else
	{
		if ($type == "num") array_multisort($$sortby, SORT_ASC, SORT_NUMERIC, $array);
		else array_multisort($$sortby, SORT_ASC, SORT_STRING, $array);
	}
	
	//This just goed through and asks the additional arguments
	//What they are doing and are doing variations of
	//the multisort
	
	return $array;
}


// CALCULATE AND RETURN MAX HEIGHT OF IMAGE AFTER RESIZE
function image_dimentions($filename,$img_loc,$thumbs_width,$thumbs_height)
{
	if(!is_file($img_loc.'/'.$filename)) {
		$filename = 'default-avatar.jpg';	
	}
		
	$tn_file = $filename;
	$file = $img_loc.'/'.$filename;

	$Max_Width=$FixedWidth=$thumbs_width;
	$Max_Height=$FixedHeight=$thumbs_height;

	if (list($img_width, $img_height, $img_type) = @getimagesize($file))
	{
		if ($img_width>$img_height)
		{
			if ($img_width > $FixedWidth)
			{
				$percent_change=$FixedWidth/$img_width;
				$Max_Height=ceil($img_height*$percent_change);
				$Max_Width=$FixedWidth;
			}
		}
		if ($img_width<$img_height)
		{
			if ($img_height > $FixedHeight)
			{
				$percent_change=$FixedHeight/$img_height;
				$Max_Width=ceil($img_width*$percent_change);
				$Max_Height=$FixedHeight;
			}
		}
		
		if ($Max_Height > $FixedHeight) $Max_Height=$FixedHeight;
		if ($Max_Width > $FixedWidth) $Max_Width=$FixedWidth;

		if ($img_width < $Max_Width) $Max_Width=$img_width;
		if ($img_height < $Max_Height) $Max_Height=$img_height;
	}
	
	return $Max_Width.'~'.$Max_Height;
}

// FUNCTION TO APPEND ST,ND,RD,TH AFTER NUMBER
function ordinal($cdnl){
	$test_c = abs($cdnl) % 10;
	$ext = ((abs($cdnl) %100 < 21 && abs($cdnl) %100 > 4) ? 'th'
			: (($test_c < 4) ? ($test_c < 3) ? ($test_c < 2) ? ($test_c < 1)
			? 'th' : 'st' : 'nd' : 'rd' : 'th'));
	return $cdnl.$ext;
}


// FUNCTION TO MAKE LINKS AS CLICKABLE HYPERLINKS IN TEXT
function makeClickableLinks($text)
{
	$text = eregi_replace('(((f|ht){1}tp://)[-a-zA-Z0-9@:%_\+.~#?&//=]+)','<a href="\\1" target="_blank">\\1</a>', $text);
	$text = eregi_replace('([[:space:]()[{}])(www.[-a-zA-Z0-9@:%_\+.~#?&//=]+)','\\1<a href="http://\\2" target="_blank">\\2</a>', $text);
	$text = eregi_replace('([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3})','<a href="mailto:\\1" target="_blank">\\1</a>', $text);	
	return $text;
}


// FUNCTION TO CALCULATE MUTUAL LOVE/HATE SCORE
function checkMutualScore($score,$user,$cookieuser) {
	global $conn;
	$SQL="SELECT COUNT(ScoreID) AS TotalGivenScore FROM `tblScoring` WHERE Score=".$score." AND Owner=".$user." AND UserID=".$cookieuser." GROUP BY Score";
	$Res=mysql_query($SQL,$conn);
	if(mysql_num_rows($Res)>0) {
		$Row=mysql_fetch_object($Res);
		$TotalGivenScore=$Row->TotalGivenScore;
	}
	else $TotalGivenScore=0;
	
	$SQL="SELECT COUNT(ScoreID) AS TotalRcvdScore FROM `tblScoring` WHERE Score=".$score." AND Owner=".$cookieuser." AND UserID=".$user." GROUP BY Score";
	$Res=mysql_query($SQL,$conn);
	if(mysql_num_rows($Res)>0) {
		$Row=mysql_fetch_object($Res);
		$TotalRcvdScore=$Row->TotalRcvdScore;
	}
	else $TotalRcvdScore=0;

	return $TotalRcvdScore.'~'.$TotalGivenScore;
}

// FUNCTION TO CALCULATE USER LOVE/HATE SCORE
function checkScore($score,$user) {
	global $conn;
	$SQL="SELECT COUNT(ScoreID) AS TotalScore FROM `tblScoring` WHERE Score=".$score." AND Owner=".$user." AND UserID!=".$user." GROUP BY Score";
	$Res=mysql_query($SQL,$conn);
	if(mysql_num_rows($Res)>0) {
		$Row=mysql_fetch_object($Res);
		$TotalScore=$Row->TotalScore;
	}
	else $TotalScore=0;
	
	$Position=1;
	$SQL="SELECT Owner,Score,COUNT(ScoreID) AS TotalScores FROM `tblScoring` WHERE Score=".$score." AND UserID!=Owner GROUP BY Owner ORDER BY TotalScores DESC";
	$Res=mysql_query($SQL,$conn);
	if(mysql_num_rows($Res)>0) {
		$userFound=false;
		while($Row=mysql_fetch_object($Res)) {
			if($Row->Owner==$user) { $userFound=true; break; }
			else $Position++;
		}
	}
	else $Position='--';
	
	if(!$userFound) $Position='--';
	
	if($Position==1) $Position.='st';
	elseif($Position==2) $Position.='nd';
	elseif($Position==3) $Position.='rd';
	else { if($Position!='--') $Position.='th'; }


	return $TotalScore.'~'.$Position;
}

// FUNCTION TO CALCULATE USER LEAGUE RATING TO SHOW ON PROFILE RIGHT SIDE
function checkLeague($score1,$score2,$user,$league) {
	global $conn;

	//-------------------------------------------
	// CALCULATE LEAGUE POINTS
	$LeagueScore=0;
	$SQL="SELECT COUNT(ScoreID) AS TotalScore FROM `tblScoring` WHERE Score=".$score1." AND Owner=".$user." AND UserID!=".$user." GROUP BY Score";
	$Res=mysql_query($SQL,$conn);
	if(mysql_num_rows($Res)>0) {
		$Row=mysql_fetch_object($Res);
		$TotalScore1=$Row->TotalScore;
	}
	else $TotalScore1=0;
	
	if($score2!=0) {
		$SQL2="SELECT COUNT(ScoreID) AS TotalScore FROM `tblScoring` WHERE Score=".$score2." AND Owner=".$user." AND UserID!=".$user." GROUP BY Score";
		$Res2=mysql_query($SQL2,$conn);
		if(mysql_num_rows($Res2)>0) {
			$Row2=mysql_fetch_object($Res2);
			$TotalScore2=$Row2->TotalScore;
		}
		else $TotalScore2=0;
		
		$LeagueScore=$TotalScore1+($TotalScore2*5);
	}
	else
		$LeagueScore=$TotalScore1;
	// CALCULATE LEAGUE POINTS
	//-------------------------------------------
	
	//-------------------------------------------
	// CALCULATE LEAGUE POSITION
	$LeagueArr=leagueTable($league,$user);
	for($k=0;$k<count($LeagueArr);$k++) {
		if($k!=count($LeagueArr)-1) {
			if($LeagueArr[$k]['ID']==$user) $Position=$k+1;
		}
	}

	if($Position!='') {
		$Position=ordinal($Position);
/*		if($Position==1) $Position.='st';
		elseif($Position==2) $Position.='nd';
		elseif($Position==3) $Position.='rd';
		else $Position.='th';
*/	} else $Position='--';
	// CALCULATE LEAGUE POSITION
	//-------------------------------------------


	return $LeagueScore.'~'.$Position;
}

function leagueMostfollowed($user) {
	global $conn;
	
	$found=false;
	$counter=1;
	$SQL="SELECT User,COUNT(ID) AS Total FROM tblFollows,tblUsers WHERE tblFollows.User=tblUsers.UserID AND tblUsers.Status=1 GROUP BY User ORDER BY Total DESC,User";
	$Res=mysql_query($SQL,$conn);
	while($Row=mysql_fetch_object($Res)) {
		if($Row->User==$user) { $found=true; $Position=$counter; $Followers=$Row->Total; break; }
		$counter++;
	}
	
	if(!$found) { $Position='--'; $Followers='0'; }
	
	if($Position!='--')
		return ordinal($Position).'~'.$Followers;
	else
		return $Position.'~'.$Followers;
}


function leagueTable($league,$user=0,$start=0,$commusers=0,$db=0) {
	global $conn;
	
	$Arr1=array();
	$Arr2=array();
	$Arr3=array();

	//-------------------------------------------
	// CALCULATE LEAGUE POINTS AND ARRANGE USERS
	if($league=='love') { $score1='1'; $score2='2'; }
	elseif($league=='rogue') { $score1='5'; $score2='4'; }
	elseif($league=='philosopher') { $score1='0'; $score2='3'; }
	
	$SQL="SELECT COUNT(ScoreID)*5 AS TotalScore,Owner,Name,ProfilePic from tblScoring,tblUsers WHERE tblScoring.Owner=tblUsers.UserID AND tblUsers.ScoringStatus=1 AND tblUsers.Status=1 AND Score=".$score1;
	if($commusers!=0)
		$SQL.=" AND tblScoring.MainDB=".$db." AND tblUsers.UserID IN (".$commusers.")";
	$SQL.=" GROUP BY Owner ORDER BY TotalScore DESC";
	$Res=mysql_query($SQL,$conn);
	if(mysql_num_rows($Res)>0) {
		$totallike=0;
		while($Row=mysql_fetch_object($Res)) {
			$Arr1[$Row->Owner]['ID']=$Row->Owner;
			$Arr1[$Row->Owner]['Name']=$Row->Name;
			$Arr1[$Row->Owner]['ProfilePic']=$Row->ProfilePic;
			$Arr1[$Row->Owner]['Score']=$Row->TotalScore;
			$IDArr1[$totallike]=$Row->Owner;
			$totallike++;
		}
	}
	
	$SQL="SELECT COUNT(ScoreID) AS TotalScore,Owner,Name,ProfilePic from tblScoring,tblUsers WHERE tblScoring.Owner=tblUsers.UserID AND tblUsers.ScoringStatus=1 AND tblUsers.Status=1 AND Score=".$score2;
	if($commusers!=0)
		$SQL.=" AND tblScoring.MainDB=".$db." AND tblUsers.UserID IN (".$commusers.")";
	$SQL.=" GROUP BY Owner ORDER BY TotalScore DESC";
	$Res=mysql_query($SQL,$conn);
	if(mysql_num_rows($Res)>0) {
		$totallove=0;
		while($Row=mysql_fetch_object($Res)) {
			$Arr2[$Row->Owner]['ID']=$Row->Owner;
			$Arr2[$Row->Owner]['Name']=$Row->Name;
			$Arr2[$Row->Owner]['ProfilePic']=$Row->ProfilePic;
			$Arr2[$Row->Owner]['Score']=$Row->TotalScore;
			$IDArr2[$totallove]=$Row->Owner;
			$totallove++;
		}
	}
	
	if($totallike>$totallove) {
		$formax=$totallike;
		$useName=$Arr1;
		$useID=$IDArr1;
	}
	else {
		$formax=$totallove;
		$useName=$Arr2;
		$useID=$IDArr2;
	}
	
	for($i=0;$i<$formax;$i++) {
		$Arr3[$i]['ID']=$useID[$i];
		$Arr3[$i]['Name']=$useName[$useID[$i]]['Name'];
		$Arr3[$i]['ProfilePic']=$useName[$useID[$i]]['ProfilePic'];
		$Arr3[$i]['Score']=$Arr1[$useID[$i]]['Score']+$Arr2[$useID[$i]]['Score'];
	}
	
	if($Arr3[0]['ID']!='')
		$Arr3=sortmddata($Arr3,'Score','DESC','num');
	// CALCULATE LEAGUE POINTS AND ARRANGE USERS
	//-------------------------------------------
	
	$pos=0;
	if($user!='0') {
		for($k=0;$k<count($Arr3);$k++) { if($Arr3[$k]['ID']==$user) { $pos=$k+1; break; } }
	}
	
	$i++;
	$Arr3[$i]['UserPos']=$pos;
	
	return $Arr3;
}

?>