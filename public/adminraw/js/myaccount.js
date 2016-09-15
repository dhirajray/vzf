/*
this file contain all type of search represantations, being used on channels for dbee
@ authour Deepak Nagar
@ Lincesed to Dbee.me
@ Start date 28 May 2013


*/

 $(function() {

   

/* Creating myaccount
*  @author Deepak Nagar
*  Date 16 May 2013 */
// Taking reference of knowledge center file
	
tips = $( ".validateTips" );
	
function updateTips( t ) {
  tips
    .text( t )
    .addClass( "ui-state-highlight" );
  setTimeout(function() {
    tips.removeClass( "ui-state-highlight", 1500 );
  }, 2000 );
}

function checkLength( o, n, min, max ) {
  //if ( o.val().length > max || o.val().length < min ) {

  	if (  o.val().length < min ) {
    o.addClass( "error" );
    updateTips( "The " + n + " must be at least " + min + " characters long" );
    return false;
  } else {
    return true;
  }
}

function checkRegexp( o, regexp, n ) {
  if ( !( regexp.test($.trim( o.val()) ) ) ) {
    o.addClass( "error" );
    updateTips( n );
    return false;
  } else {
    return true;
  }
}
$('.managerol').click(function(){
   $.ajax({
            type: "POST",
	        url:  BASE_URL+"/admin/myaccount/rollist",
			success:function(result){ 	
             $('#selectuserrols').html(result);
             $select('#userRols');
				$('select[name="rools"]').change(function(event) { 
				var selectrol = $('select[name="rools"] option:selected').attr('selectrol');
				$userswithrols(selectrol);
				})
			}
  });		
 var selectrol = $('select[name="rools"] option:first').attr('selectrol');
 $userswithrols(selectrol=1);
})


$userswithrols = function(selectrol){
  $.ajax({
            type: "POST",
	        url:  BASE_URL+"/admin/myaccount/usersrollist",
	        data:{ "selectrol": selectrol},
			success:function(result){ 
             $('#userrollistshow').html(result);
			}
  });
}	

 
$('#checkpass').click(function(event) 
{
   
   $('#currentPwdchked').val('');

   	var currentPwd 	= $('#currentPwd').val();
   
   	if(currentPwd=='')
	{
		//$("#chkpwd").effect( 'bounce', '', 500 ); 
		$messageError('Please add your current password')
		return false;
	}
	
  

   	formData =  'curpwd= '+currentPwd+'&req=checkpwd';
  	url		=	BASE_URL+"/admin/myaccount/updateaccountpass";
	$.ajax({                                      
		url: url,                  //the script to call to get data          
		data: formData,                        //you can insert url argumnets here to pass to api.php
		type: 'POST',
		beforeSend: function()
		{
			$(".beforecall").show();
			$(".beforecall").html('<img src="'+BASE_URL+'images/loader2.gif" >');	 //return false;
		},  
		success: function(data)          //on recieve of reply
		{
			$(".beforecall").hide();
		
			if(data==1)
			{ 
				$("#currentPwdForm").hide('slow');			
				$("#changePwdForm").show('slow');
				$('#currentPwd').val('');
				$('#currentPwdchked').val('dbeEverfieD');
			}
			else
			{
				$messageError("Oops, this isn't your current password");
			}

		},
		
    });
	 return false;
});

$('#securityQuesetval').click(function(){
   	var userquestion =$('select[name="questionsecque"] option:selected').attr('value');
   	var useranswer 	=$.trim($('#answer').val());
   	if(userquestion==''){
     $messageError("please fill the security question");
     return false;
    }
    if(useranswer==''){
     $messageError("please fill the security answer");
     return false;
    } 
	formDataseqcheck ='secretquestion='+userquestion+'&answer='+useranswer;	
	//alert(formDataseqcheck);
	url		=	BASE_URL+"/admin/myaccount/updatesecqueans";
	$.ajax({                                      
			url: url,                
			data: formDataseqcheck,                     
			type: 'POST',
			success: function(data) {
				//alert(data)
			    if(data==1){
                       $messageSuccess('updated secret question and answer successfully.');
			    }					
				 
			},
			/*cache: false,
			contentType: false,
			processData: false*/
	    });
});

$('.clickpagestatus').on('click',function() 
{
	var pagetoken = $('#pagestatus option:selected').val();
	var pagename = $('#pagestatus option:selected').text();
	if(pagetoken==''){
		$messageError('Please select facebook page');
		return false;
	}
	//alert(pagetoken);
   	formData =  'pagename='+pagename+'&pagetoken='+pagetoken+'&type=facebook';
  	url		=	BASE_URL+"/admin/myaccount/updatefacebookpageinfo";
	$.ajax({                                      
		url: url,                  //the script to call to get data          
		data: formData,                        //you can insert url argumnets here to pass to api.php
		type: 'POST',  
		success: function(data)          //on recieve of reply
		{
			$messageSuccess('Facebook page feed updated successfully');
			$('.selectPagesFb').remove();
			$('.socialDroptxt').remove();
		}
    });
});



$('body').on('click', '.socialdisconnect',function(e) 
{	
	e.preventDefault();
	var type = $(this).attr('type');
	var thisBtn  = $(this);
	var dataUrl  = thisBtn.attr('dataurl');
	$(this).attr('href', dataUrl);
   	formData =  'disconnect='+type;
  	url		=	BASE_URL+"/admin/myaccount/disconnectsocial";
	$.ajax({                                      
		url: url,                  //the script to call to get data          
		data: formData,                        //you can insert url argumnets here to pass to api.php
		type: 'POST',  
		success: function(data)          //on recieve of reply
		{	thisBtn.attr('href', dataUrl).removeClass('socialdisconnect');			
			if(type=='facebook'){
				if(localTick==false)
					socket.emit('disconnectsocial', 'facebook',clientID);
				thisBtn.html('<i class="socialSpecialIcons ssfbIcon"> </i> Facebook connect');
				$('#fbContDet').fadeOut();
				$('.userName.facebook').html('');
			}else if(type=='twitter'){
				if(localTick==false)
					socket.emit('disconnectsocial', 'twitter',clientID);
				thisBtn.html('<i class="socialSpecialIcons sstwIcon"> </i> Twitter connect');
				$('.userName.twitter').html('');
			}
			else if(type=='linkedin'){
				if(localTick==false)
					socket.emit('disconnectsocial', 'linkedin',clientID);
				thisBtn.html('<i class="socialSpecialIcons ssinIcon"> </i> LinkedIn connect');
				$('.selectPagesFb').fadeOut();
			}
		}
    });
});


$('body').on('click', '.socialadmindisconnect',function(e) 
{	
	e.preventDefault();
	var type = $(this).attr('type');
	var thisBtn  = $(this);
	var dataUrl  = thisBtn.attr('dataurl');
	$(this).attr('href', dataUrl);
   	formData =  'disconnect='+type;
  	url		=	BASE_URL+"/admin/myaccount/disconnectadminsocial";
	$.ajax({                                      
		url: url,                  //the script to call to get data          
		data: formData,                        //you can insert url argumnets here to pass to api.php
		type: 'POST',  
		success: function(data)          //on recieve of reply
		{	thisBtn.attr('href', dataUrl).removeClass('socialdisconnect');			
			if(type=='facebook'){
				thisBtn.html('<i class="socialSpecialIcons ssfbIcon"> </i> Facebook connect');
				$('#fbContDet').fadeOut();
			}else if(type=='twitter'){
				thisBtn.html('<i class="socialSpecialIcons sstwIcon"> </i> Twitter connect');
			}
			else if(type=='linkedin'){
				thisBtn.html('<i class="socialSpecialIcons ssinIcon"> </i> Linkedin connect');
			}
		}
    });
});

// changing to password

$('button#changepassword').click(function() 
{
  // alert(''); return false;

   	var newPwd 		= $('#newPwd').val();
   	var confPwd 	= $('#confirmPwd').val();
   	var verified 	= $('#currentPwdchked').val();

   	if(verified!='dbeEverfieD')
	{
		//$("#changepass").effect( 'bounce', '', 500 );
		//$("#confirmpass").effect( 'bounce', '', 500 ); 
		$messageError("Please use secure way to change the password!"); 
		return false;
	}
   
   	if(newPwd=='')
	{
		//$("#changepass").effect( 'bounce', '', 500 ); 
		$messageError('Please insert new password');
		return false;
	}

	if(newPwd!=confPwd)
	{
		//$("#changepass").effect( 'bounce', '', 500 ); 
		//$("#confirmpass").effect( 'bounce', '', 500 ); 
		$messageError('Password don\'t match');
		return false;
	}


   	formData =  'curpwd= '+newPwd+'&req=changepwd';
  	url		=	BASE_URL+"/admin/myaccount/updateaccountpass";
	$.ajax({                                      
		url: url,                  //the script to call to get data          
		data: formData,                        //you can insert url argumnets here to pass to api.php
		type: 'POST',
		beforeSend: function()
		{
			$(".beforecall").show();
			$(".beforecall").html('<img src="'+BASE_URL+'images/loader2.gif" >');	 //return false;
		},  
		success: function(data)          //on recieve of reply
		{
			$(".beforecall").hide();
		
			if(data==1)
			{ 
				$messageSuccess('updated successfully');
				$("#currentPwdForm").show('slow');			
				$("#changePwdForm").hide('slow');
				$('#newPwd').val('');
   				$('#confirmPwd').val('');
   				$('#currentPwdchked').val('');
			}
			else
			{
				$messageError('Internal server error!');
			}

		},
		
    });


    return false;

});

// End of change password
// Changing account settings 

$('form#myAccount').submit(function(event) {


   	var picture		= $.trim($('form#myAccount input[type=file]').val()); 
   	var userFname 	= $('form#myAccount #fname');
   	var userEmail 	= $('form#myAccount #email'); 
   	var picUpdat 	= $('form#myAccount #picture').val(); 
   	var username 	= $.trim($('form#myAccount #fname').val());
   	var usermail 	= $.trim($('form#myAccount #email').val());
   	//var useranswer 	= $.trim($('form#myAccount #answer').val());

   	//alert(userFname) ; return false;
   	if(username==''){
     $messageError("please fill the name");
     return false;
    }
    if(usermail==''){
     $messageError("please fill the email");
     return false;
    }
    /*if(useranswer==''){
     $messageError("please fill the password");
     return false;
    }*/

   //	allFields = $( [] ).add( userFname ).add( userEmail ),
  
    bValid = true;
    
	bValid =  bValid && checkLength(userFname, "fname", 3, 80 );
	bValid =  bValid && checkLength(userEmail, "email", 6, 80 );
	bValid =  bValid && checkRegexp(userEmail, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "eg. admin@dbee.com" );	
	var formData = new FormData($(this)[0]);
	//	alert(formData);  return false;  

	if ( bValid )
	{


		if(picture!='')
		{

			if(!/(\.jpg)|(\.jpeg)|(\.png)|(\.png)$/i.test(picture)) {
			$messageError( "Only well formated images are allowed. " );  
			return false;   
			}  
	    }
	  		

	   // return false;  
		
		url		=	BASE_URL+"/admin/myaccount/updateaccount";
		$.ajax({                                      
			url: url,                
			data: formData,                     
			type: 'POST',
			success: function(data) {
			    //alert(data);die;						
				data = data.split('~');
				var timThumb = '/users/medium/'+data[2];
				$("#picture").val(data[2]);
				if(data[1]==1)
				{ 
					//alert('<img src="'+BASE_URL+timThumb+'"  class="imgStyle" width="93">');
					$messageSuccess(data[0] ); 
					$("#newpicconfig").html('<img src="'+IMGPATH+timThumb+'"  class="imgStyle" width="93">'); 					
					$("#newpic").html('<img src="'+IMGPATH+timThumb+'"  class="imgStyle" width="93">');

					$("#headerImg").html('<img src="'+IMGPATH+timThumb+'"  width="93">');
					$("#headerImg2").html('<img src="'+IMGPATH+timThumb+'" "  width="93" class="imgStyle">');
					$("#username").html(userFname.val());
					$(".accountName").html(userFname.val());					
				}
				else
				{
					$messageError( data[0] ); 
				}

			},
			cache: false,
			contentType: false,
			processData: false
	    });
	}

    return false;

	});
		
		$('body').on('click','.accountTabLink li a',function(e){	
			var body = $("html, body");
			var thisEl = $(this);
			var tabId = thisEl.attr('href');
			tabId = tabId.split("#")[1];
			body.animate({scrollTop:0}, '500', 'swing', function() { });
			var index = $(this).closest('.accountTabLink').find('li').index(this);
			$(this).closest('ul').find('.active').removeClass('active');
			$(this).closest('li').addClass('active');
			$(this).closest('.accountTabLink').next('.accountContainer').find('.accountContent').hide();
			//$(this).closest('.accountTabLink').next('.accountContainer').find('.accountContent:eq('+index+')').show();
			$('[data-id="'+tabId+'"]').show();		
		});
		
	

		/*global setting*/
	$('.socialAction2 input').click(function(event) {
		//alert('');return false;
	    event.preventDefault();
	  	var globelSettingVal = 1;
	    var calling = $(this).attr('caller');
	    var thisEl  =$(this);
	   
	   //alert(calling);
	    var msg = '';
	    if(calling=='social')
	    {
	      if(thisEl.is(':checked')==false) {
	          msg = 'Are you sure you want to make the platform public?';
	          globelSettingVal=0;
	      }else{
	           msg ='Are you sure you want to restrict platform access to only pre-approved or invited users? By doing this you are removing any standard user account creation and social API sign in functionality.';
	      }
	    }
	    else if(calling=='event')
	    {
	      if(thisEl.is(':checked')==false) {
	          msg = 'Are you sure you want to remove live events?';
	          globelSettingVal=0;
	      }else{
	           msg ='Are you sure you want to enable live events?';
	      }
	    }
	    else if(calling=='groupbg')
	    {
	      if(thisEl.is(':checked')==false) {
	          msg = 'Are you sure you want to remove the ability for users to add background images to their Groups?';
	          globelSettingVal=0;
	      }else{
	           msg ='Are you sure you want to allow the ability for users to add background images to their Groups?';
	      }
	    }
	    else if(calling=='creategrp')
	    {
	      if(thisEl.is(':checked')==false) {
	          msg = 'Are you sure you want to remove the ability for users to create Groups?';
	          globelSettingVal=0;
	      }else{
	           msg ='Are you sure you want to allow the ability for users to create Groups?';
	      }
	    }
	    else if(calling=='scoringoff')
	    {
	      if(thisEl.is(':checked')==true) {
	          msg = 'Are you sure you want to turn on platform scoring?';
	          globelSettingVal=0;
	      }else{
	           msg ='Are you sure you want to turn off platform scoring?';
	            globelSettingVal=1;
	      }
	    }
	    else if(calling=='gspider')
	    {
	      if(thisEl.is(':checked')==false) {
	          msg = 'Are you sure you want to remove any Google indexing?';
	          globelSettingVal=0;
	      }else{
	           msg ='Are you sure you want to allow Google indexing?';
	      }
	    }
	    else if(calling=='semantria_seen')
	    {
	      if(thisEl.is(':checked')==false) {
	          msg = 'Are you sure you want to hide sentiment analysis results from users?';
	          globelSettingVal=0;
	      }else{
	           msg ='Are you sure you want to show sentiment analysis results to users?';
	      }
	    }
	    else if(calling=='IsLeagueOn')
	    {
	      if(thisEl.is(':checked')==false) {
	          msg = 'By turning the Leagues on, all leagues related features will be activated on the platform. Do you wish to continue?';
	          globelSettingVal=0;
	      }else{
	           msg ='By turning the Leagues off, all leagues related features will be deactivated on the platform. Do you wish to continue?';
	           globelSettingVal=1;
	      }
	    }
	    else if(calling=='userconfirmed')
	    {
	      if(thisEl.is(':checked')==false) {
	      	  msg ='Are you sure you want to hide unconfirmed user accounts within front end user search results?';	          
	          globelSettingVal=0;
	      }else{
	           msg = 'Are you sure you want to display unconfirmed user accounts within front end user search results?';
	           globelSettingVal=1;
	      }
	    }
	    else if(calling=='allow_user_create_polls')
	    {
	      if(thisEl.is(':checked')==true) {
	          msg = 'Are you sure you want to allow the ability for users to create polls?';
	          globelSettingVal=1;
	      }else{
	           msg ='Are you sure you want to remove the ability for users to create polls?';
	           globelSettingVal=0;
	      }
	    }
	    else if(calling=='allowexperts')
	    {
	      if(thisEl.is(':checked')==true) {
	          msg = 'Are you sure you want to allow the ability for users to ask an expert a question?';
	          globelSettingVal=1;
	      }else{
	           msg ='Are you sure you want to remove the ability for users to ask an expert a question?';
	           globelSettingVal=0;
	      }
	    }
	    else if(calling=='allowmultipleexperts')
	    {
	      if(thisEl.is(':checked')==true) {
	          msg = 'Are you sure you want to allow multiple experts on a post?';
	          globelSettingVal=3;
	      }else{
	           msg ='Are you sure you want to have only one expert on a post?';
	           globelSettingVal=1;
	      }
	    }
	    else if(calling=='addtocontact')
	    {
	      if(thisEl.is(':checked')==true) {
	          msg = 'Are you sure you want to allow the ability for users to add contacts?';
	          globelSettingVal=1;
	      }else{
	           msg ='Are you sure you want to remove the ability for users to add contacts?';
	           globelSettingVal=0;
	      }
	    }
	    else if(calling=='adminpostscore')
	    {
	      if(thisEl.is(':checked')==true) {
	          msg = 'Are you sure you want to allow users the ability to score an admin post or comment?';
	          globelSettingVal=1;
	      }else{
	           msg ='Are you sure you want to remove the ability for users to score an admin post or comment?';
	           globelSettingVal=0;
	      }
	    }
	    else if(calling=='IsSurveysOn')
	    {
	      if(thisEl.is(':checked')==true) {
	          msg = 'Are you sure you want to enable surveys?';
	          globelSettingVal=1;
	      }else{
	           msg ='Are you sure you want to remove surveys?';
	           globelSettingVal=0;
	      }
	    }

	     else if(calling=='Profanityfilter')
	    {
	      if(thisEl.is(':checked')==true) {
	          msg = 'Are you sure you want to enable the profanity filter?';
	          globelSettingVal=1;
	      }else{
	           msg ='Are you sure you want to remove the profanity filter?';
	           globelSettingVal=0;
	      }
	    }
	     else if(calling=='admin_searchable_frontend')
	    {
	      if(thisEl.is(':checked')==true) {
	          msg = 'Are you sure you want to enable the admin appearing in front end user searches?';
	          globelSettingVal=1;
	      }else{
	           msg ='Are you sure you want to disable the admin appearing in front end user searches?';
	           globelSettingVal=0;
	      }
	    }
	    else if(calling=='allowedPostWithTwitter')
	    {
	      if(thisEl.is(':checked')==true) {	           
	           msg = 'Are you sure you want to allow only selected users to post with Twitter #tags?';
	           globelSettingVal=0;
	      }else{
	      	 	  msg ='Are you sure you want to allow all platform users to post with Twitter #tags?';	           
	              globelSettingVal=1;
	      }
	    }
	    else if(calling=='AllowLoginTerms')
	    {
	      if(thisEl.is(':checked')==false) {
	          msg = 'Are you sure you want to show a message to users when they log in?';
	          globelSettingVal=1;
	      }else{
	           msg ='Are you sure you do not want to show a message to users when they log in?';
	           globelSettingVal=0;
	      }
	    }
	    else if(calling=='ShowVideoEvent')
	    {
	      if(thisEl.is(':checked')==false) {
	          msg = 'Are you sure you want to remove video events?';
	          globelSettingVal=0;
	      }else{
	           msg ='Are you sure you want to enable video events?';
	           globelSettingVal=1;
	      }
	    }
	    else if(calling=='ShowLiveVideoEvent')
	    {
	      if(thisEl.is(':checked')==false) {
	          msg = 'Are you sure you want to remove live video broadcasts?';
	          globelSettingVal=0;
	      }else{
	           msg ='Are you sure you want to enable live video broadcasts?';
	           globelSettingVal=1;
	      }
	    }
	    else if(calling=='ShowRSS')
	    {
	      if(thisEl.is(':checked')==false) {
	          msg = 'Are you sure you want to remove RSS?';
	          globelSettingVal=0;
	      }else{
	           msg ='Are you sure you want to enable RSS?';
	           globelSettingVal=1;
	      }
	    }
	    else if(calling=='showAllUsers')
	    {
	      if(thisEl.is(':checked')==false) {
	          msg = 'Are you sure you want to hide ‘All users’ from main menu?';
	          globelSettingVal=0;
	      }else{
	           msg ='Are you sure you want to show ‘All users’ on main menu?';
	           globelSettingVal=1;
	      }
	    }

	     else if(calling=='groupemail')
	    {
	      if(thisEl.is(':checked')==false) {
	          msg = 'Are you sure you want to desable email to user on group Post?';
	          globelSettingVal=0;
	      }else{
	           msg ='Are you sure you want to enable email to user on group Post?';
	           globelSettingVal=1;
	      }
	    }

    
       $('#dialogConfirmSetting').remove();
       $('body').append('<div id="dialogConfirmSetting">'+msg+'</div>');

       $( "#dialogConfirmSetting" ).dialog({
          resizable: false,
          title:'Please confirm',
          modal: true,
          buttons: {
            "Yes": function() {
              $( this ).dialog("close");
                $.ajax({
                  type: "POST",
                  url:  BASE_URL+"/admin/globalsetting/insertgsetting",
                  data:{ "globelSettingVal": globelSettingVal,'calling':calling,},
                   success:function(result)
                   {  
                   	

						$messageSuccess("updated successfully");

						if(thisEl.is(':checked')==false) 
						{                              
							thisEl.attr('checked', true);
							if(calling=='creategrp')
							{
							  $('#groupbg').removeClass('disabled');
							  $('#groupbg').attr('disabled', false);
							  $('.GroupBackgroundRow').removeClass('disabled');
							}
							if(calling=='showAllUsers')
							{
							  
							  $('#socialTarget6').removeClass('disabled');
							  $('#socialTarget6').attr('disabled', false);
							  $('#showAllUsersBackground').removeClass('disabled');
							}
						
	    					

							if(calling=='scoringoff')
							{
							  $('#socialTarget7').removeClass('disabled');
							  $('#socialTarget7').attr('disabled', false);
							  $('#scoreset1').removeClass('disabled');
							  $('#scoreset1').attr('disabled', false);
							  $('#scoreset2').removeClass('disabled');
							  $('#scoreset2').attr('disabled', false);
							  $('.GroupBackgroundRow').removeClass('disabled');
							}
							
							if(calling!='userconfirmed' || calling!='adminpostscore')
							{
								if(calling!='social')
								socket.emit('enablesocial', calling,clientID);
								else
								socket.emit('disablesocial', calling,clientID);
					        }

						}else
						{
							thisEl.attr('checked', false);

							if(calling=='showAllUsers')
							{ 
							  $('#socialTarget6').addClass('disabled');
							  $('#socialTarget6').attr('disabled', 'disabled');
							  $('#showAllUsersBackground').addClass('disabled');
							}

							
							if(calling!='userconfirmed' || calling!='adminpostscore')
							{
								if(calling=='allowedPostWithTwitter' || calling=='social')
								{
								 socket.emit('enablesocial', calling,clientID);
								}else{
		    					  socket.emit('disablesocial', calling,clientID);
								}
						    }
							
							if(calling=='creategrp')
							{
								//$('#groupbg').attr('checked', true);
								$('#groupbg').addClass('disabled');
								$('#groupbg').attr('disabled', 'disabled');
								$('.GroupBackgroundRow').addClass('disabled');
							}
							
							if(calling=='scoringoff')
							{
							  $('#socialTarget7').addClass('disabled');
							  $('#socialTarget7').attr('disabled', 'disabled');
							  $('#socialTarget7').prop('checked',false);
							  $('#scoreset1').addClass('disabled');
							  $('#scoreset1').attr('disabled', 'disabled');
							  $('#scoreset2').addClass('disabled');
							  $('#scoreset2').attr('disabled', 'disabled');
							  $('.GroupBackgroundRow').addClass('disabled');						  
							}

						}

						if(calling=='allowedPostWithTwitter')
						{
							if(globelSettingVal==0)
							{
								//$('#RefPostTargetTwitterTable').css('opacity','1');
								$('#RefPostTargetTwitterTable, #RefPostTargetTwitterTable .btn-green').removeClass('disabled');

							}
							else
							{
								//$('#RefPostTargetTwitterTable').css('opacity','0.3');
								$('#RefPostTargetTwitterTable, #RefPostTargetTwitterTable .btn-green').addClass('disabled');
							}
							
						}

						if(calling=='AllowLoginTerms')
						{
							if(globelSettingVal==1)
							{
								$('#Reflogin_terms').css('opacity','1');
								$('#login_terms_btn').removeClass('disabled');
								$("#login_terms").prop("disabled", false);
								$("#login_terms_btn").prop("disabled", false);
							}
							else
							{
								$('#Reflogin_terms').css('opacity','0.4');
								$('#login_terms_btn').addClass('disabled');
								$("#login_terms").prop("disabled", true);
								$("#login_terms_btn").prop("disabled", true);
							}
							
						}
                   }  
                  });
            }
          }
        });

    
  });

  $('.gTimeSet').click(function(e){
			e.preventDefault();
			var globalsettingTime = $('select[name="globalTimeSetting"] option:selected').attr('value');
			$.ajax({
	        type: "POST",
	        url:  BASE_URL+"/admin/myaccount/setglobaltime",
	        data:{ "expireTime": globalsettingTime},
			success:function(result){
				     //console.log(result);return false;
			    	 $messageSuccess("updated successfully");	
	             },
		        error : function(error) {
			    	 alert("Retrieval Error" );
			     }	 
	        });

		});
/*end global setting*/
	
	




}); // End of main function