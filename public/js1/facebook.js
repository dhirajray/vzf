function inviteFriends()
{
   FB.ui(
   {
     method: 'feed',
     name: 'Dbee.me',
     link: BASE_URL,
     picture: BASE_URL+'/img/logo.png',
     description: "post is a real-time 'debate & rate' social network enabling youto share views on any subject, in open community or private groups.",
     message: 'I just started a debate on dbee.me. Check it out by clicking below.'
   },
   function(response) {
     if (response && response.post_id) {
       
       $dbConfirm({content:'Post was published.', yes:false});
     }
   }
 );

return false;

	FB.ui({method: 'apprequests',
      title: 'Friend Dbee.me',
      message: 'Join Dbee.me',
    }, fbCallback2);
 } 


function sendrequest(id)
{
		FB.ui({
		method: 'friends',
		id: id
		}, function(response){ 
			console.log(response);
		});
}
function fbCallback(response) 
{

	console.log(response.to[0]);
	$.each(response.to, function(index, fbid){
		FB.ui({
		method: 'feed',
		to:fbid,
		link: 'https://developers.facebook.com/docs/dialogs/',
		caption: 'An example caption',
		}, function(response){});
	});
	
}
function fbCallback2(response) 
{
	return false;

	console.log(response.to[0]);
		FB.ui({
		method: 'feed',
		to:response.to[0],
		link: 'https://developers.facebook.com/docs/dialogs/',
		caption: 'An example caption',
		}, function(response){});

}
function fbcallback3(response)
{
	if (!response || !response.to) { /* user canceled */ return; }
	var req_ids = response.to;
	var _batch = [];
	for(var i=0; i<req_ids.length; i++ ){
		_batch.push({"to":req_ids[i],
			link:'https://developers.facebook.com/docs/dialogs/',caption: 'An example caption'});
	}
	if(_batch.length>0){
	FB.ui({method: 'feed',batch:_batch},function(res){
	  console.log(res);
	});
	}
	return false;
}

function FacebooksentMessage()
{
   $.ajax({
			
			type : "POST",
			dataType : 'json',
			data:{'userID':'groupid'},
			url : BASE_URL + '/myhome/facebookfriends',
			
			async : true,			
			beforeSend : function() {
			
			},
			
			complete : function() {
				
			},
			
			cache : false,
			
			success : function(response) {
				$("#socalnetworking-list").html(response.content);
				$(".facebookfriendlist").equalizeHeights();
				
			},
			
			error : function(error) {
			
			$("#socalnetworking-list").html("Some problems have occured. Please try again later: "
			+ error);
			
			}
			
			});
}




function FacebookInviteFriendsOnProfilePage(r) 
{
    $(document).ready(function () {
  
        FB.login(function (response) {

            var access_token = FB.getAuthResponse()['accessToken'];

            if (access_token) {

                   FB.api('/me', function (response) {

                    var query = FB.Data.query('select first_name , last_name, email, name, birthday, hometown_location, current_location ,political, religion, sex, pic_big  from user where uid={0}', response.id);

                    query.wait(function (rows) {
                        
                    $.ajax({
                        type: "POST",
                        dataType : 'json',
                        url:"/dbeedetail/facebooktoken",
                        data:{"access_token": access_token},
                        success:function(result){
                               
                                showFacebookFriendsOnFacebook();
                             }   
                        });

                    });
                });
                 
            } 
            
        }, {
            scope: 'read_stream,publish_stream,offline_access,email,user_birthday,publish_actions'
        });
    });
}

function showFacebookFriendsOnFacebook()
{   
   $.dbeePopup('close');

   $("#content_data_button").hide();

   var FacebookTemplate = '<h2>Connect with facebook</h2>\
   <div class="srcUsrWrapper">\
	   <div class="fa fa-search fa-lg searchIcon2"></div>\
	   <input type="text" id="facebookfriendlist" class="findsocialfriend" value=""  onkeyup="javascript:filtersocailuser()" socialFriendlist="true">\
	   <div id="Usercountfilter" class="Usercountfilter srcUsrtotal" Usercountfilter="true"></div>\
   </div>\
   <div id="socalnetworking-facebooklist">\
   <div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>';
	setTimeout(function(){
	$.dbeePopup(FacebookTemplate, {overlayClick:false, otherBtn:'<a href="javascript:void(0);" class="pull-right btn btn-yellow"  id="SendPostToFacebook" >Invite</a><a href="javascript:void(0);" class="pull-right btn btn-yellow"  id="SendPostToFacebookafter" style="display:none;">Invite</a>'});
						


	var content = '';
    FB.api('/me/friends', function(response) {
        content +="<input type='hidden' name='logined' value='logined' id='logined'>";
        for(var i = 0; i < response.data.length; i++) 
        {
			
             content += "<div class='facebookfriendlist boxFlowers' title='"+response.data[i].name+"' socialFrienduser='true'>\
			 <input type='hidden' name='userid' value='"+response.data[i].user+"' id='userid'>\
            <label class='labelCheckbox'><input type='checkbox' value='"+response.data[i].id+"_"+response.data[i].name+"' class='inviteuser-search' name='FacebookInvite'>\
<div class='follower-box'>\
            <img  class=img border height=30 align='left' src='https://graph.facebook.com/" +response.data[i].id+"/picture'>\
            <br>"+response.data[i].name+"</div></label>\
            </div></div>";
        }

        $("#socalnetworking-facebooklist").html(content);
		$("#allrev").show();
        $(".facebookfriendlist").equalizeHeights();
        setTimeout(function(){
            $.dbeePopup('resize');
        }, 500);

    });
    },500);

}

$(document).ready(function(){

	$('body').on('click','#SendPostToFacebook',function(){
		$('#SendPostToFacebook').hide();
		$('#SendPostToFacebookafter').show().addClass('processBtnLoader');
		var userInfo = [];
		$('input:checkbox[name=FacebookInvite]').each(function() 
		{    
			if($(this).is(':checked'))
			userInfo.push($(this).val());
		});
        var stringuserInfo = userInfo.join();

			$.ajax({
				type: "POST",
				dataType : 'json',
				url:"/profile/sendmessagetofacebook",
				data:{"userid": stringuserInfo},
				success:function(result){

						$('.closePostPop').trigger('click');
				    	
				    	$dbConfirm({content:'Invitation sent', yes:false});
				    	$.dbeePopup('close');

				     }   
			});
	});

	

	

});



function shareOnwallFacebookMedium()
{
	$.dbeePopup('close');
	setTimeout(function(){
		var favTemplate = '<div style="padding:20px;">\
		<div class="formRow" id="content_data" >Do you want to share this post on facebook?</div>\
		<div class="clearfix"></div>\
		</div>';
		$.dbeePopup(favTemplate,{width:350,otherBtn:'<a href="javascript:void(0);" class="btn btn-yellow pull-right" onclick="javascript:shareOnwallFacebook()">Yes</a>\
		'});
	 },500);

}

function shareOnwallFacebook(thisEl)
{
	var dbid = $('#dbid').val();
	var thisEl = $(thisEl);
	
	
	$.ajax({
		beforeSend:function(){
			$dbLoader(thisEl,'center');
		},
		type: "POST",
		data :{'dbeeid':dbid},
		dataType : 'json',
		url:"/social/shareonfacebookwall",
		success:function(response)
		{
			 $('.closePostPop').trigger('click');
	
			  $dbConfirm({content:response.message, yes:false});
		}   
	});
}