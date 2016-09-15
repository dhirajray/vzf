
$(function(){

	$('body').on('click','.expandparentcomment',function()
	{
		$(this).hide();

		var cId = $(this).attr('cid');

		$('#parentexpende_'+cId).css({display:'inline'});
		$('#collespparentcomment'+cId).css({display:'inline'});
	});

	$('.postDetailsScore a:not(.otheruserlike)').bind('click',function(){
			
			var data = $(this).attr('data');
			data = data.split(',');	
			scoredbee(data[0],data[1],data[2],data[3],data[4],data[5]);
			
		});

	$('body').on('click','.btnCommentFocus',function(e){
		e.preventDefault();
		var offset = $('#dbee_comment').offset();
		$('body,html').animate({
			scrollTop: offset.top-90
		});
		$('#dbee_comment').focus();
	});

	


	$('body').on('click','#fetchsentiments',function(){
		var dbId 		=	$('#dbid').val();
		//alert(dbId);
		url 		=	BASE_URL+"/dbeedetail/sentiments";

		$dbMessageProgressBar({fetchText:'Fetching sentiments...',time:12000});
	

		$.ajax({
			url: url,
			data: 'dbid='+dbId,
			type:'post',
			success : function(result){
				if(result!='404')
				{	
					$dbMessageProgressBar({complete:true});

					setTimeout(function(){
						window.location.href=result;
					}, 1000);
					//return false;
				}
			},
			error:function(a, b,c) {
				$dbConfirm({content:c, yes:false})
				$dbMessageProgressBar({close:true});
			}
		});
		return false;

	});


// this is for video post description
var txtCnt = $.trim($('.dbBroadcardDesc:not(#twittersphere)').text());
var charLenth = txtCnt.length;
var charVar = 250;
var moreLink = '';
if($('.expertDivBlankClass').is(':visible')==true){
	charVar = 600;
	if(charLenth>600){
		moreLink= '<a href="#" id="moreCnt">[+ more] </a>';
	}
}else{
	if(charLenth>600){
		moreLink= '<a href="#" id="moreCnt">[+ more] </a>';
	}
}
var txtCnt2 = txtCnt.substr(0, charVar)+moreLink;
$('.dbBroadcardDesc .trickerContent').html(txtCnt2);

$('body').on('click','#moreCnt', function(event) {
	event.preventDefault();
	$('.dbBroadcardDesc .trickerContent').html(txtCnt.substr(0, charLenth)+'<a href="#" id="lessCnt">[- less] </a>').addClass('on');
});
$('body').on('click','#lessCnt',function(event) {
	event.preventDefault(); 
	$('.dbBroadcardDesc .trickerContent').html(txtCnt2).removeClass('on');
});
// end  this is for video post description



	$('body').on('click','#singledbleages',function(){
		dbId 		=	$('#dbid').val();
		//alert(dbId);
		url 		=	BASE_URL+"/League/getleadgepositions";
		var LeagueposTemplate = '<div id="leage_result" class="leaguesPostPopUp"><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>';
		$.dbeePopup(LeagueposTemplate);

		$.ajax({
			
			url: url,
			data: 'dbid='+dbId,
			dataType:'json',
			type:'post',
			beforeSend  : function(){
				//$dbLoader('#replymebox'+parentId, 1);
			},
			success : function(result){


				$('#leage_result').html(result.ledge);
				$.dbeePopup('resize');

			},
		});
		return false;

	});


	$('body').on('click','.collespparentcomment',function()
	{
		$(this).hide();
		var cId = $(this).attr('cid');
		$('#parentexpende_'+cId).hide();
		$('#expandparentcomment'+cId).css({display:'inline'});
	});

	$('body').on('click','.replytocomment',function()
	{
		cmntId 		=	$(this).attr('commentId');
		dbId 		=	$(this).attr('dbid');
		cmntowner 	=	$(this).attr('cmntowner');
		clearInt();

		//$('.replymebox').hide('slow');
		$('.replymebox').html('');
		var cmntbox = '<div style=" display:block" >\
			<div class="">\
				<div id="dbreply-text>\
					<div class="formRow">\
						<textarea class="dbreplyTextarea"   placeholder="Reply to user" id="reply_comment_now" ></textarea>\
					</div>\
					<input type="hidden" id="reply_parentid" value="'+cmntId+'">\
					<input type="hidden" id="reply_dbid" value="'+dbId+'">\
					<input type="hidden" id="reply_cmntownerid" value="'+cmntowner+'">\
					<div class="formSubtitle">\
						<span class="pull-right limitLength"></span>\
					</div>\
					<a class="pull-right btn btn-yellow" style="font-size:12px" href="javascript:void(0)" id="reply_to_comment">Reply</a>\
					<a class="pull-right" style="font-size:12px; margin:7px 10px 0 10px" href="javascript:void(0)" id="reply_to_hide">Close</a>\
				</div>\
			</div>\
		</div>';
		
		//$('#comment-block-'+cmntId).a
		$('.replymebox').show();
		$('#replymebox'+cmntId).html(cmntbox);
		$('.dbreplyTextarea').elastic();
		
	});

	$('body').on('click','.replytoquestion',function()
	{
		cmntId 		=	$(this).attr('commentId');
		dbId 		=	$(this).attr('dbid');
		cmntowner 	=	$(this).attr('cmntowner');
		expert_id 	=	$(this).attr('expert_id');
		clearInt();

		$('.replymebox').html('');
		var cmntbox = '<div style=" display:block" >\
			<div class="">\
				<div id="dbreply-text" class="formField">\
					<textarea class="dbreplyTextarea"  placeholder="Add your reply..." id="reply_question_now" ></textarea>\
					<input type="hidden" id="reply_parentid" value="'+cmntId+'">\
					<input type="hidden" id="reply_expert_id" value="'+expert_id+'">\
					<input type="hidden" id="reply_dbid" value="'+dbId+'">\
					<input type="hidden" id="reply_questionownerid" value="'+cmntowner+'">\
					<div class="formSubtitle">\
						<span class="pull-right limitLength"></span>\
					</div>\
					<a  class="btn btn-mini" href="javascript:void(0)" id="reply_to_hide_question">Close</a>\
					<a  class="pull-right btn btn-yellow btn-mini reply_to_question" href="javascript:void(0)">Reply</a>\
				</div>\
			</div>\
		</div>';
		$('.replymebox').show();
		$('#replymebox'+cmntId).html(cmntbox);

		
	});

	$('body').on('click','#reply_to_hide_question', function()
	{
		$('.replymebox').html('');
		$('.replymebox').hide();
		parentdbId	=	$('.replytoquestion').attr('dbid');
		startInt(parentdbId);
	});


	
	
	

	$('body').on('click','#reply_to_hide', function()
	{
		
		$('.replymebox').html('');
		$('.replymebox').hide();
		parentdbId	=	$('.replytocomment').attr('dbid');
		startInt(parentdbId);
	});

	$('body').on('click','#reply_to_comment', function()
	{
		$('#reply_to_comment').append(' <i class="fa fa-spinner fa-spin"> </i>').css('cursor', 'default');
		cmntval		=	$('#reply_comment_now').val();
		parentId	=	$('#reply_parentid').val();
		parentdbId	=	$('#reply_dbid').val();
		cmntownerId	=	$('#reply_cmntownerid').val();
		startInt(parentdbId);
		if(cmntval=='')
		{
			$dbConfirm({content:'Please give some message to reply user...', yes:false,error:true});
			return false;
		}
		$('#startnew').val('20');
		url = BASE_URL+"/comment/insertdata";
		data = 'db='+parentdbId+'&parentid='+parentId+'&comment='+cmntval+'&cmntownerId='+cmntownerId+'&replytype=text';
		$('.replymebox').html('');
		$('.replymebox').hide();
		$('#reply_to_comment').hide();
		$.ajax({
			url	: url,
			data: data,
			type: 'post',
			dataType:'json',
			beforeSend  : function(){
				$dbLoader('#replymebox'+parentId, 1);
			},
			complete : function() {
				setTimeout(function(){
						$('#reply_to_comment .fa-spin').remove();
						$('#reply_to_comment a').css('cursor', 'pointer');
					}, 3000);
			},
			success : function(result){ 
				if(localTick == false){
					socket.emit('loadComment', result.insertId,parentdbId,clientID);
                    socket.emit('checkdbee', true,clientID,SESS_USER_ID);
                    callsocket();
            	}
				$('.replymebox').html('');
				$('.replymebox').hide();
				$('#startnew').val('20');			 
			}
		});
		return false;
	});		
	
$(window).load(function (){
	$('.questionAnsMainWrp').flexslider({
	    animation: "slide",
	    controlNav:false,
	    smoothHeight:true,
	    slideshow:false,
	    slideshowSpeed:500,
	    start:function (slider){
	    	slider.removeClass('questionLoader');
	    },
	    end:function(){
	    	questionEnd=true;
	    }
	  });
});

var questionEnd = false;
var currentQuestions = 1;
var surveyArray = [];
var TotalQuestions = $('.totalQuestions i').text();

	$('body').on('click','#surveyNext' , function(e){
		var parentDiv = $(this).closest('#dbtphdrSurveyID');
		if($('.flex-active-slide input', parentDiv).is(':checked')==false){
			//$messageError('please select any answer');
			$dbConfirm({content:'please select any answer', yes:false,error:true});
			return false; 
		}
		currentQuestions +=1;
		var dbeeid = $('#dbid').val();
		surveyArray.push($('.flex-active-slide input:checked', parentDiv).val());
		console.log(surveyArray)
		var stringSurvey = surveyArray.join(',');

		if(currentQuestions <= TotalQuestions){
			$('.flex-next:visible', parentDiv).trigger('click');				
			$('.totalQuestions strong').text(currentQuestions);
			if(currentQuestions == TotalQuestions){
				$(this).text('Finish');	
			}

		}else{
			

			$(this).hide();
				$.ajax({
		            type: "POST",
		            dataType:"json",
		            url:BASE_URL+"/dbeedetail/surveyfinish",
		            data:{"stringSurvey": stringSurvey,'dbeeid':dbeeid},
		            success:function(response){

		            	//response.content
		            	$('.questionAnsMainWrp').html(response.content);
				        surveyArray=[];
				        $('.StartSurvey, .btnSurveyWrp').hide();
				         if(localTick == false){
			                    callsocket();
			                }

		           }
		        });


		}
	});




}); // end jquery load function

