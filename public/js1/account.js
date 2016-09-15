
function checkLogin() {
	var btn = $('.signinBtnGroup button');
	if(btn.hasClass('disable')==true) return false;

	var elm = $('#signInBlock .required');
        $checkError(elm);
       if($checkError(elm)==false){
        return false;
       }

    /*if (document.getElementById("loginemail").value == '' || document.getElementById("loginemail").value == 'registered email') {
        document.getElementById("loginemail").focus();
		$dbConfirm({content:'Please enter your email address.', yes:false,error:true});
		$('.fa-spin', btn).remove();
		btn.css('cursor', 'pointer');
        return false;
        
    } 
    if (document.getElementById("loginpass").value == '') {
        document.getElementById("loginpass").focus();
		$dbConfirm({content:'Please enter your password.', yes:false,error:true});
		$('.fa-spin', btn).remove();
		btn.css('cursor', 'pointer');
        return false;
    }*/ 

    btn.append(' <i class="fa fa-spinner fa-spin"> </i>').css('cursor', 'default').addClass('disable');
    $(this).closest('form').find('#login-form').removeAttr("onsubmit");
    return true;


}

var userdetailsLogon='' ; 

function createrandontokenLogin(callback)
{
    capcha = new Date();
    data = 'captchakey='+capcha;
    $.ajax(
    {
        type : "POST",
        dataType : 'json',
        data: data,
        url : BASE_URL + '/index/createrandontoken',  
        async : false,   // Dont remove this neccessary for returning value of respnce
        success : function(response) 
        {     
            if(response.errormsg != 404)
            {
                //$messageError(response.errormsg);
                $dbConfirm({content:response.errormsg, yes:false,error:true});
                return false;
            }
            else
            {
                userdetailsLogon = "SessUser__=" + response.SessUser__ + "&SessId__=" + response.SessId__ + "&SessName__=" + response.SessName__ + "&Token__=" + response.Token__+"&Key__=" + response.Key__; 
            }
           
        },
    })
}    


$(function(){
				$('[data-id]').click(function(e){
					e.preventDefault();
					var dataID  = $(this).attr('data-id');
					$('.frntSignIn').hide();
					$('#'+dataID).show();
					$('html, body').animate({scrollTop:$('#'+dataID).offset().top-70});
					if(dataID=='creatAccountBlock'){
						$('body').addClass('activeCreateAccount');
						
					}else{
						$('body').removeClass('activeCreateAccount');
					}

				});
			});

// SEND FORGOTTEN PASSWORD
var SendPassHttp;

function sendpassFromHome() {
	
    var email = $('#forgotemail').val(); 
    //var birthday = $('#birthday').val();
    //var birmons = $("#birmon option:selected").val();        
    //var birthyear = $('#birthyear').val();
    //var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    
    var elm = $('#passform .required');
        $checkError(elm);
       if($checkError(elm)==false){
        return false;
      }

$('#sendlink').append(' <i class="fa fa-spinner fa-spin"> </i>').css('cursor', 'default').removeAttr("onclick");

    /*if(email=='')
    {
    	$dbConfirm({content:'Please enter your email address', yes:false,error:true});
		$('#sendlink').attr("onclick","javascript:sendpassFromHome();");
		$('#sendlink .fa-spin').remove();
		$('#sendlink').css('cursor', 'pointer');
    	return false;
    }
    else if (!filter.test(email)) {
    	$('#sendlink').attr("onclick","javascript:sendpassFromHome();");
		$('#sendlink .fa-spin').remove();
		$('#sendlink').css('cursor', 'pointer');
		$dbConfirm({content:'Please enter your valid email address.', yes:false,error:true});
	    return false;
    }
    else if(birthday==0 && birmons ==0 && birthyear ==0)
    {
    	$('#sendlink').attr("onclick","javascript:sendpassFromHome();");
		$('#sendlink .fa-spin').remove();
		$('#sendlink').css('cursor', 'pointer');    	
    	$dbConfirm({content:'Please select your date of birth', yes:false,error:true});
    	return false;
    }*/


    	createrandontokenLogin();
    	//data = 'email='+email+'&birthday='+birthday+'&birthmonth='+birmons+'&birthyear='+birthyear+'&'+userdetailsLogon; 
    	data = 'email='+email+'&'+userdetailsLogon;
	    $.ajax({
	    	type: "POST",
	    	dataType: 'json',
	    	data: data,
	    	url:  BASE_URL+'/index/sendpassword',
	    	success: function(response){
                //alert(response);return false;
	    		if(response.SubmitMessage==0){
	    			$("#forgotpassm").hide();
	    			$("#sendlink").hide();
	    			$('#content_data').html('<div style="text-align:center;color:#999; font-size:18px;">Password reset link has been sent to your registered email address.</div>');
	    			setTimeout(function(){
						$.dbeePopup('resize');
					}, 200);
					setTimeout(function(){
						$.dbeePopup('close');
					}, 3000);

	    		}else{
	    			$dbConfirm({content:response.message, yes:false,error:true});
	    			$('#sendlink').attr("onclick","javascript:sendpassFromHome();");
					$('#sendlink .fa-spin').remove();
					$('#sendlink').css('cursor', 'pointer');
	    		}	    		
	    	}    	
	    });
   
}
function openforgotpass() {
	var ForgotTemplate = '<div id="content_data" ></div>\
				 <div class="clearfix"></div>';
        
		$.dbeePopup(ForgotTemplate, {
            title:'forgotten password?',
            otherBtn:'<span id="forgotpassm" ></span><a href="#" id="sendlink" class="pull-right btn btn-yellow" onclick="javascript:sendpassFromHome()" >Send reset link</a>'
            });
		
			$.ajax({
			
			type : "POST",
			dataType : 'json',
			
			url : BASE_URL + '/index/forgotpass',
			
			async : false,			
			beforeSend : function() {
			
			},
			
			complete : function() {
				
			},
			
			cache : false,
			
			success : function(response) {
				if(response.status=='success')
					$("#content_data").html(response.content);
				else
					$('#sendlink').remove();
			}			
			});
			
}
