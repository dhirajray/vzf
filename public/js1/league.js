$(function(){	

	$('.leagueMembers').click(function(){

		var CreategroupTemplate = '<div id="content_data" ><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>\
		 <div class="clearfix"></div>';
		
		$('.leaguescreatemain ul a').removeClass('active');
		$(this).addClass('active');

		$('#lg_content_data').html(CreategroupTemplate);
		$('#lg_content_button').hide();	
		
		url 		=	BASE_URL+"/League/getmembersleadge";
		
		var LeaguememTemplate = '<div id="leage_members" class="leaguesPostPopUp" ><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>';
		//$.dbeePopup(LeaguememTemplate);
		
		$.ajax({

			url: url,
			data: '',
			dataType: 'json',
			type: 'post',			
			beforeSend : function(){
			},
			success : function(responce)
			{
				//$('#leage_members').html(responce);
				$('#lg_content_data').html(responce);
				//$.dbeePopup('resize');
				
			},
		});
		return false;
	});

	$('body').on('click','.onpage-createleague',function(){
		var CreategroupTemplate = '<div id="content_data" ><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>\
		 <div class="clearfix"></div>';
		$('#lg_content_button').show();	
		$('.leaguescreatemain ul a').removeClass('active');
		$(this).addClass('active');

		$('#lg_content_data').html(CreategroupTemplate);	
	
		otherBtn = '<a href="javascript:void(0)" class="btn btn-yellow pull-right createGroupBtn" id="createleaguestep2">Next</a> \
		<div class="createGroupBtn2 createGroupBtnsteptwo" style="display:none">\
				<a href="javascript:void(0)" class="btn btn-yellow pull-right " id="createleaguestep3">Next</a> \
		</div>\
		<div class="backGroupBtn" style="display:none">\
			<a href="javascript:void(0)" class="btn btn-yellow pull-right" onclick="javascript:backtocreategroup();">back</a>\
		</div>\
		<div class="backGroupBtn" style="display:none">\
			<a href="javascript:void(0)" class="btn btn-yellow pull-right" onclick="javascript:processleague();">back</a>\
		</div>\
		<div class="processGroupBtnOk" style="display:none">\
			<a href="javascript:void(0)" class="btn btn-yellow pull-right" onclick="javascript:leagueusrinvite();">Next</a>\
		</div>\
		<div class="processGroupBtnSendMessageFacebook" style="display:none">\
			<a href="javascript:void(0)" class="btn btn-yellow pull-right" onclick="javascript:selectuserstoFacebook();">Ok</a>\
		</div>\
		<div class="processGroupBtnCreate" style="display:none">\
			<a href="javascript:void(0)" class="btn btn-yellow pull-right" onclick="javascript:createleague(0);">Create</a>\
		</div>\
		<div class="processGroupBtnCreatechk" style="display:none">\
			<a href="javascript:void(0)" class="btn btn-yellow pull-right" onclick="javascript:createleague(1);">Create</a>\
		</div>';
		$('#createleaguestep2').hide();
		$('.closePostPop').hide();
		$.ajax({
			type : "POST",
			dataType : 'html',
			url : BASE_URL + '/dbleagues/createleague',
			cache : false,		
			success : function(data) {	
				var resultArr = data.split('~#~');
				var alreadyAdd=$('#alreadyAddedcount').val();	

				$('#lg_content_data').html(resultArr[0]+'<div class="btnLeagueWrp">'+otherBtn+'</div><div class="clear"> </div>');	
				loadfollowingforleague('following');				
				loadfollowersforleague('followers');	

				$( "#enddate" ).datetimepicker({dateFormat:'dd-mm-yy', minDate:0});

				$('#allDbAlAdd').click(function(){
					var text = $(this).text();
					var alreadyAdd=$('#alreadyAddedcount').val();
					if(alreadyAdd >0){
						if($('.dblistLp .formField').is(':visible')==true){		
							$(this).text("Show available posts");
							$('.dblistLp .formField').hide();
							$('.dblistLp .formField_hide').show();
						}else{
							$(this).text("Show unavailable posts");
							$('.dblistLp .formField').show();
							$('.dblistLp .formField_hide').hide();
						}
						$('.alreadyAddedClass').toggle();
					}
				});	

				if(resultArr[2]==0){
					$('.dblistLp li').css({
					'border-bottom': '0px'
				});
				$('#createleaguestep3').css({
					'display': 'none'
				});											
				}
			$('.closePostPop').show();
			$('#createleaguestep2').show();	

			}

		});

	});



$('body').on('click','.league-myleague',function(){
	var CreategroupTemplate = '<div id="content_data" ><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>\
		 <div class="clearfix"></div>';
		
	$('.leaguescreatemain ul a').removeClass('active');
	$(this).addClass('active');

	$('#lg_content_data').html(CreategroupTemplate);
	$('#lg_content_button').hide();		 
		
	$.ajax({
		type : "POST",
		dataType : 'json',
		data : {'type':'list'},
		url : BASE_URL + '/dbleagues/listleague',
		cache : false,			
		success : function(response) {			
			$("#lg_content_data").html(response.content);
			
		}
		
		});
	
	});

$('body').on('click','#createleaguestep2',function(){ 
	var leaguname = $('#league-name');
	var endsdate = $('#enddate');
	var leaguedesc = $('#league-desc');
	
	if(leaguname.val()==''){
		leaguname.focus();
		//$messageError("Please enter a name for league");
		$dbConfirm({content:"Please enter a name for league", yes:false,error:true});
	}
	else if(endsdate.val()==''){
		endsdate.focus();
		//$messageError("Please select league closing date");
		$dbConfirm({content:"Please select league closing date", yes:false,error:true});
	}
	else if(leaguedesc.val()==''){
		leaguedesc.focus();
		//$messageError("Please write something about this league");
		$dbConfirm({content:"Please write something about this league", yes:false,error:true});
	}else{
		$('#createleague-step2').css({
			display: 'block'
		});
		$('.createGroupBtn').hide();
		$('.createGroupBtnsteptwo').show();
		$('#create-league-wrapper').css({
			display: 'none'
		});
	
	
	}	

	});



$('body').on('click','#createleaguestep3',function(){
	var leaguname = $('#league-name');
	var dbs ='';
	 $('#createleague-step2 input:checked').each(function() {
	    	 dbs	 += $(this).val()+', ' ;
	    });
	 
	 var count=($('#createleague-step2 input:checked').length);
	  
	if (count >= 2) { 
		$('#createleague-step3').css({
			display: 'block'
		});
		$('#createleague-step2').css({
			display: 'none'
		});
		$('.createGroupBtnsteptwo').hide();
		$('.processGroupBtnOk').show();	
		
	
	}else{
		err = true;
	    //$messageError("Please select at least 2 db's");
	    $dbConfirm({content:"Please select at least 2 db's", yes:false,error:true});

	}
	});


});

function loadfollowersforleague(type){
	
	$.ajax({
				type : "POST",
				dataType : 'json',
				url : BASE_URL + '/dbleagues/followingleague',
				data : {'type':type},
				cache : false,			
				success : function(response) {					
				
						$("#followers-list").html(response.userlist);	
						$("#total-followers").val(response.totaluser);
						$.dbeePopup('resize');
				}
				
				
			
			});


}

function loadfollowingforleague(type){

	$.ajax({
				type : "POST",
				dataType : 'json',
				url : BASE_URL + '/dbleagues/followingleague',
				data : {'type':type},
				cache : false,			
				success : function(response) {					
						$("#following-list").html(response.userlist);	
						$("#total-following").val(response.totaluser);	
						$.dbeePopup('resize');
				}		
			
			});

}


function searchleague() {
	
	var keyword=$('#keyword').val();	
	
	$.ajax({
		type : "POST",
		dataType : 'json',
		url : BASE_URL + '/dbleagues/followingleague',
		data : {'keyword':keyword,'type':'searchuser'},
		cache : false,			
		success : function(response) {	
			$('#search-list .formRow').removeClass('singleRow');
			$("#search-invite-list").show().find('.formField').html(response.userlist);	
			$("#total-search").val(response.totaluser);		
			$.dbeePopup('resize');
		}		
	
	});

}
	

//FUNCTION TO SHOW SELECTED USERS FOR INVITE
function leagueusrinvite() {

    var err = false;
    
	$("#invitetoleague-header").show();
	$("#invite-selected").show();
	$("#league-users-label").html('Users selected by you to invite to league.');
	var totalfollowers = $('#total-followers').val();
	var totalfollowing = $('#total-following').val();
	var totalsearch = $('#total-search').val();
    var users = '';

    $('.tabcontainer input:checked').each(function() {
    	users	 += $(this).val()+', ' ;
    });
   
    if (users == "")    
    err = true;
    
    $('#total-users-toinvite').val(users);
    
    if (!err) {
    	 users = trim_last(users);
    	
					 $.ajax({
							type: "POST",
							url:BASE_URL+"/dbleagues/selecttoinvite",
							data:{ "users": users},
							success:function(response){
							$('.processGroupBtnOk').hide();
							$('.processGroupBtnCreate').show();
							$('.processGroupBtnSendInvite').show();
							$("#invite-message").show();
							//var resultArr = result.split('~#~');
							$('#createleague-step3').hide();
							$('#league-selected-div').show();
						
							$('#league-invite-selected').html(response.content);
								$.dbeePopup('resize');

							},
							error : function(error) {
							}	 
						});
    } else { 
    		//$messageError("Please select users to invite");
    		$dbConfirm({content:"Please select users to invite", yes:false,error:true});
			$("#invite-message").show();
			$('#invite-message').html('<font color="red" >Please select users to invite</font>'); 
			setTimeout(function(){
			$("#invite-message").fadeOut("slow", function () {
			$("#invite-message").html('');
			
			});
			}, 2000);
	}
}

function createleague(skip) {
	
	var leaguedbee = '';
	var users= '';
    err = false;
    if (document.getElementById('league-name').value == '') {
        document.getElementById("league-name").focus();
        err = true;
    } 
    var enddate = $("#enddate").val();
    if (enddate == '') {
    	$("#enddate").focus();
        err = true;
    } 
   
	var leaguename = $("#league-name").val();
	var leaguedisc = $("#league-desc").val();
	//var leaguedb =  $('#createleague-step2 input[name=league-db]:checked').val();
	$('#createleague-step2 input[name=league-db]:checked').each(function() {
		leaguedbee	 += $(this).val()+', ' ;
    });	
	$('.tabcontainer input:checked').each(function() {
		users	 += $(this).val()+', ' ;
    });	
	if(skip==0){
		if(users==''){
			//$messageError("Please select users to invite");
			$dbConfirm({content:"Please select users to invite", yes:false,error:true});
			 err = true;
		}
	}
	//alert(leaguename);alert(leaguedisc);alert(leaguedbee);alert(users);alert(enddate);
    if (!err) {
    	
    	 $.ajax({
				type: "POST",
				url:BASE_URL +"/dbleagues/insertdata",
				data:{ "Title": leaguename,"Discription": leaguedisc,"leaguedbee": leaguedbee,"Title": leaguename,"EndDate":enddate,"users":users},
				beforeSend : function() {
			          $('.processGroupBtnCreate a').append(' <i class="fa fa-spinner fa-spin"> </i>').css('cursor', 'default').removeAttr("onclick");
			          $('.processGroupBtnCreatechk a').append(' <i class="fa fa-spinner fa-spin"> </i>').css('cursor', 'default').removeAttr("onclick");
			    },
			    complete : function() {
								$('.processGroupBtnCreate a').attr("onclick","javascript:createleague(0);");
								$('.processGroupBtnCreate .fa-spin').remove();
								$('.processGroupBtnCreate a').css('cursor', 'pointer');

								$('.processGroupBtnCreatechk a').attr("onclick","javascript:createleague(1);");
								$('.processGroupBtnCreatechk .fa-spin').remove();
								$('.processGroupBtnCreatechk a').css('cursor', 'pointer');
					  		
			    },
				success:function(response){	
				    				
					$('#league_idhidden').val(response.insertleague)	
				     if (response.users != '0') {					    	
				    	 sendleagueinvite(response.users);			               												
			            } else {
			            	 $dbConfirm({content:"League created successfully."});
			            	 $('#invite-message').show();
			                 $('.leaguescreatemain li:first a').trigger('click');	          			                
			            }
				} 
			});
       
		//$('.processGroupBtnCreate').hide();
    }
}
function sendleagueinvite(user) {
	var leaguedb = $('#league_idhidden').val();
	

	$.ajax({
		type : "POST",
		dataType : 'json',
		url : BASE_URL + '/dbleagues/sendleagueinvite',
		data : {'users':user,'leaguedb':leaguedb},
		cache : false,			
		success : function(response) {	
			
			$('#league-selected-div').hide();
           $('#invite-message').show();
            $('.leaguescreatemain li:first a').trigger('click');
            $dbConfirm({content:'League created and invitation sent to all selected users', yes:false});
           
           		
		}	
	});

}

function fetchdbeeleague(lid) {
	$('#dbee-feeds').html('<div id="lg_content_data"><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>');
	
	$.ajax({
		type : "POST",
		dataType : 'html',
		url : BASE_URL + '/league/leagueinitialdbee',
		data : {'lid':lid},
		cache : false,			
		success : function(data) {	
			var resultArr = data.split('~#~');
			 $('#dbee-feeds_league').html(resultArr[0]);					
		}		
	
	});

}
function backtoselectionleague()
{
	$('#league-selected-div').hide();
	$('#createleague-step3').show();
	$('.processGroupBtnCreatechk').hide();
	$('.processGroupBtnCreate').show();
}

function removeuserfroleague(user) {
    var users = $('#total-users-toinvite').val();   
    users = users.replace(user + ',', "");
	$('#total-users-toinvite').val(users);
    $('#select-invite-' + user).hide();
    
}

function skipinviteleague() 
{
	
	$('.processGroupBtnOk').hide();
	$('.processGroupBtnCreate').hide();
	$('.processGroupBtnCreatechk').show();
	$('#createleague-step3').hide();
	$('#invite-message').hide();
	$('#total-users-toinvite').val('');
	$('#invite-selected').hide();
	$('#league-selected-div').show();
	$('#league-users-label').hide();
	$('#invitetoleague-header').hide();
	$('#league-invite-selected').html('Click OK to create your league or click \'back to selection\' to select users to invite.');
}

