<div style="clear:both"></div>



<div id="footerSlideContainer">

	<div id="footerSlideButton"></div>

	<div id="footerSlideContent">

		<div id="footerSlideText" style="display:none">

			<div style="float:left"><a href="http://www.myplanetlife.com" target="_blank"><div class="footerLogo"></div></a></div>

			<div align="right" style="float:right; margin-right:20px;">

				<a href="http://www.facebook.com/dbee.me" target="_blank"><div class="footer-fb" style="margin-top:10px;"></div></a>

				<a href="https://twitter.com/dbee_me" target="_blank"><div class="footer-twitter" style="margin-top:10px; margin-left:10px;"></div></a>

				<div style="margin-top:20px; margin-left:20px; float:left"><a href="javascript:void(0);" onclick="javascript:opentermsmyhome();"><font color="#FFFFFF">Terms & Conditions</font></a> <font color="#FFFFFF">|</font> <a href="javascript:void(0);" onclick="javascript:openprivacymyhome();"><font color="#FFFFFF">Privacy Policy</font></a> <font color="#FFFFFF">|</font> <a href="javascript:void(0);" onclick="javascript:openaboutmyhome();"><font color="#FFFFFF">About</font></a> <font color="#FFFFFF">|</font> <a href="javascript:void(0);" onclick="javascript:openfeedbackmyhome();"><font color="#FFFFFF">Feedback</font></a></div>

			</div>

		</div>

	</div>

</div>	



<div id="postdbee-popup" class="popup_block">Dbee posted to your profile.</div>

<!-- <div id="favourite-popup" class="popup_block">DB added to your favourites.</div> -->

<div id="archivemsg-popup" class="popup_block">Message deleted from your list.</div>

<div id="deletefavourite-popup" class="popup_block">Do you really want to remove this db from your favourites?<br /><br /><div id="deletefavourite-controls"></div></div>

<div id="joingroup-popup" class="popup_block">Request sent to group owner.</div>

<div id="hideuserdb-popup" class="popup_block"></div>



<div class="sticky-left-wrapper">

	<a href="http://www.facebook.com/dbee.me" target="_blank"><div class="sticky-left-fb sticky-left"></div></a>

	<a href="https://twitter.com/dbee_me" target="_blank"><div class="sticky-left-twitter sticky-left"></div></a>

	<a href="javascript:void(0);"><div class="sticky-left-about sticky-left" onclick="javascript:openaboutmyhome();"></div></a>

</div>

<!-- QTIP FUNCTION -->

<script type="text/javascript" src="<?php echo BASE_URL;?>/js/qtip/jquery.qtip-1.0.0-rc3.min.js"></script>

<!-- QTIP FUNCTION -->



<? if(isset($_COOKIE['newcommcookie'])) $disp='block'; else $disp='none'; $disp='none';?>

<div id="sticky-left-newcomm" style="display:<?=$disp;?>" onclick="javascript:opennewcomm()">

	<div class="sticky-left-newcomm"><div id="sticky-left-newcomm-num"><?=$_COOKIE['newcommcookie']?></div></div>

</div>

<a href="/dbleagues"><div class="sticky-left-leagues"></div></a>

<!--<div class="sticky-rss" onclick="javascript:openrssselector()"></div>-->

<div id="ghstpopup"><div id="ghstpopup-text">Adam Jones added a new db.</div></div>



<a name="slidetotop"><div id="backtotop" class="sticky-backtotop"></div></a>

