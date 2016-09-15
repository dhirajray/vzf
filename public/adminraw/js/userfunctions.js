/*
this file contain all type of search represantations, being used on channels for dbee
@ Lincesed to Dbee.me
@ Start date 30 Apr 2013
*/
 $(function() {
  var name  = $("#usernameInput"),
      email = $("#useremail"),
      lname = $("#userlnameInput"),
      jobname = $("#jobtitleInput"),
      companyname = $("#companyInput"),
      usernamer = $("#usernamerInput"),
      password = $("#passwordInput"),	
	  invname = $("#inv_username"),
      invemail = $("#inv_useremail"),
      folderName = $("#ks_catname"),
     
      allFields = $( [] ).add( name ).add( lname ).add( jobname ).add( companyname ).add( usernamer ).add( password ).add( email ).add( invname ).add( invemail ).add( folderName ),
      tips = $( ".validateTips" );


    function updateTips( t ) {
      tips
        .text( t )
        .addClass( "message warning" );
      setTimeout(function() {
        tips.removeClass( "message warning");
      }, 2000 );
    }
 
    function checkLength( o, n, min, max ) {
      //if ( o.val().length > max || o.val().length < min ) {
      	if (  o.val().length < min ) {
        o.addClass( "error" );
        $messageError( "The " + n + " must be at least " + min + " characters long" );
        return false;
      } else {
        return true;
      }
    }
 
    function checkRegexp( o, regexp, n ) {
      if ( !( regexp.test($.trim( o) ) ) ) {
        email.addClass( "error" );
        $messageError( n );
        return false;
      } else {
        return true;
      }
    }

    function checkBothLength( Lname, Lemail, n ) {
      if ( Lname.length != Lemail.length ) {
        email.addClass( "error" );
        name.addClass( "error" );
        $messageError( n );
        return false;
      } else {
        return true;
      }
    }

$('#addUsersroleBtn').click(function(e){
            e.preventDefault();
            var selectrol = $('select[name="rools"] option:selected').attr('selectrol');
			var calling 	= $('#calling').val();
            var fields = $('#subadminresults input:checked').serializeArray();
            if(fields==''){
                 $messageError("please select a user");
                 return false;
                }
		    $( "#results" ).empty();
		    jQuery.each( fields, function( i, field ) {
		      $("#results").append( field.value + "," );
		    });
		    /*var selectrol = $('select[name="rools"] option:selected').attr('selectrol');*/
            if(selectrol=='selectrole'){
                 $messageError("please select a role");
                 return false;
                }
            var selectusersid = $("#results").text();       	    
            data =	"selectusersid="+ selectusersid +"&roleid="+ selectrol +"&require="+calling;
			url	=	BASE_URL+"/admin/import/updateuserrole";
			$.ajax({                                      
			  url: url,              
			  type:'POST',     
			  data: data,                   
			 success: function(data)        
			  { 
			  	     //alert(data);exit;
			 		 $messageSuccess("updated successfully");
			 		 if(localTick == false){
                            socket.emit('chkactivitynotification', true,clientID);
                        }
			 		 window.location.reload();			 	
			  }
			});
});
/****************************************************/
$(function(){
	$("#usernamerInput").keyup(function() { 
	    var usr = $("#usernamerInput").val();
		if(usr.length >= 5)
		{
            $("#status").html('<img src="'+BASE_URL+'/adminraw/images/loaderani.gif" align="absmiddle">&nbsp;Checking availability...');
            $.ajax({  
					type: "POST",  
					url: BASE_URL+"/admin/import/checkuser",  
					data: "usernamerInput="+ usr,  
					success: function(msg){ 
					    $("#status").ajaxComplete(function(event, request, settings){ 
							if(msg == 'OK')
							{ 
							    $("#usernamerInput").removeClass('object_error');
								$("#usernamerInput").addClass("object_ok");
								$(this).html('&nbsp;<img src="'+BASE_URL+'/adminraw/images/tickani.gif" align="absmiddle">');
							}  
							else  
							{  
								$("#usernamerInput").removeClass('object_ok');
								$("#usernamerInput").addClass("object_error");
								$(this).html(msg);
							}  

					    });

					 } 
			});
		}
		else
		{
			$("#status").html('<font color="red">The username should have at least <strong>5</strong> characters.</font>');
			$("#usernamerInput").removeClass('object_ok');
			$("#usernamerInput").addClass("object_error");
		}	
	})
})


/****************************************************/


$('#inviteUsersBtn').click(function(){
var bValid = true;
            allFields.removeClass( "error" );
 			
			var separatedName  = invname.val().split(",");
			var separatedEmail = invemail.val().split(",");
			
			bValid =  bValid && checkBothLength(separatedName,separatedEmail,'Oops, please check the details and try again');
			
			$.each(separatedEmail, function(index, chunk) {
			    bValid = bValid && checkLength( invname, "username", 3, 16 );
			   // bValid = bValid && checkRegexp( name, /^[a-z]([0-9a-z_])+$/i, "Username may consist of a-z, 0-9, underscores, begin with a letter." );
			});

			$.each(separatedEmail, function(index, chunk) {
			    bValid = bValid && checkLength( invemail, "email", 6, 80 );
			    bValid =  bValid && checkRegexp( chunk, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "email should be in valid format eg. admin@dbee.com" );	
			});
			         
			if ( bValid ) {
          	
	            data	=	"username="+ invname.val() +"&email="+ invemail.val() +"&require=inviteadmin" ;
				url		=	BASE_URL+"/admin/import/adduser";
				$.ajax({                                      
				  url: url,                  //the script to call to get data          
				  data: data,                        //you can insert url argumnets here to pass to api.php
				 // dataType: 'json',                //data format    
				 success: function(data)          //on recieve of reply
				 {
				 	if(data!='')
				 	{
				 		$messageError("These users already registered or invited : "+data+" Remaining added successfully");
				 		setTimeout(function(){window.location.reload();}, 2000);
				 	}
				 	else
				 	{
				 		$messageSuccess(" Invitation sent successfully");
				 		window.location.reload();

				 	}
					
				 }
				});
          	}

});

/*$('.userListTable tr td:eq(1)').each(function(){
		var elt = $(this);
		var pTr = elt.closest('tr');
		var tottalChecked = $('td:eq(1) ul input:checked', pTr).size();	
		var totalList = $('td:eq(1) ul li', pTr).size();
		if(tottalChecked>1){
			$('td:eq(1) ul', pTr).show();
		}

	});*/

$('.rolecheckboxt').each(function(){
	var el = $(this);
	var pTr = el.closest('tr');
	var parentid = el.attr('parentid');
	var selectrole = el.attr('roleid');
	var resourceid = el.val();
	var data = {roleid:selectrole, resourceid:resourceid, parentid:parentid};
	var url  = BASE_URL+"/admin/manageroles/getrolesubresource";
	$.ajax({                                      
		url: url,              
		type:'POST',     
		data: data,                   
		success: function(data){
			$('td:eq(1)', pTr).append(data);
			var tottalChecked = $('td:eq(1) ul input:checked', pTr).size();	
			var totalList = $('td:eq(1) ul li', pTr).size();
				if(totalList==tottalChecked){
					$('.selectAllCheckBox input', pTr).attr('checked', true);
					$('.selectAllCheckBox span', pTr).text('Deselect all');
				}else{
					$('.selectAllCheckBox input', pTr).attr('checked', false);
					$('.selectAllCheckBox span', pTr).text('Select all');
				}

				if(tottalChecked>0){
					if(totalList>1){
						$('td:eq(1) ul', pTr).show(); 
						pTr.addClass('openRules');
					}
       				else {
       					$('td:eq(1) ul', pTr).hide(); 
       					pTr.removeClass('openRules');
       				}
       				 el.attr('checked', 'checked');
       			}else{
       				 $('td:eq(1) ul', pTr).hide();
       				 pTr.removeClass('openRules');
       				  el.attr('checked', false);
       			} 


		}
	});

});
$('body').on('click','.rolecheckboxt',function(){
 	var el = $(this);
	var pTr = el.closest('tr'); 
	var totalList = $('td:eq(1) ul li', pTr).size();
	if(el.is(':checked')==true){
		if(totalList>1){
			$('td:eq(1) ul', pTr).show();
			pTr.addClass('openRules');
		}else {
		 	$('td:eq(1) ul', pTr).hide();
		 	pTr.removeClass('openRules');
		 	$('td:eq(1) input', pTr).attr('checked', 'checked');
		}

	}else{
		 $('td:eq(1) ul', pTr).hide();
		 pTr.removeClass('openRules');
		// if(totalList==1) {
		 	$('td:eq(1) input', pTr).removeAttr('checked');
		// }
	}    				
});
$('body').on('click','.selectAllCheckBox input',function(e){
	var el = $(this);
	var pTr = el.closest('tr'); 
	var pTd = el.closest('td'); 
	var totalList = $('td:eq(1) ul li', pTr).size();
	var tottalChecked = $('td:eq(1) ul input:checked', pTr).size();	
	if(el.is(':checked')==true){
		$('ul input', pTd).attr('checked', 'checked');
		$('.selectAllCheckBox span', pTr).text('Deselect all');
	}else{
		$('ul input', pTd).removeAttr('checked');
		$('.selectAllCheckBox span', pTr).text('Select all');
	}

});
$('body').on('click','#userRolesTable input', function(e){
	$('.addroleresourceval').fadeIn();
});


$('body').on('click','.addroleresourceval',function(){
    var formrolerescheckval = $('#userform').serialize();
    //alert(formrolerescheckval);exit;	
    if(formrolerescheckval==''){
        $messageError('Please select a section');
        return false;
    }
    var roleid = $('.rolecheckboxt').attr('roleid');
	data = $('#userform').serialize()+"&id_role="+ roleid;
	url  = BASE_URL+"/admin/manageroles/addroleresource";
	$.ajax({                                      
	  url: url,              
	  type:'POST',     
	  data: data,                   
	 success: function(data){
        //alert(formrolerescheckval);//exit;
	 	$messageSuccess(data);
	  }
	});
})



$('body').on('click','.selectallval',function(){   	
   var elt = $(this);
   var parentTD = elt.closest('td');
   $('ul input[type="checkbox"]',parentTD).attr('checked',this.checked);
   
   if($(this).attr('checked')=='checked')
   {
    $(this).closest('label').find('a.btn-green').text('Deselect All');	
   }
   else
   {
      $(this).closest('label').find('a.btn-green').text('Select All');	   	
   }
        
});

$('.addRoleBtn').click(function(e){
   rolename = $("input[name=roleInput]").val();
   rolenameval = (typeof(rolename)=='undefined') ? '' : rolename;
   if(rolenameval.length==0){
     $messageError("Please add a name");
     return false;
   }
      data = "role="+ rolename;
	  url  = BASE_URL+"/admin/manageroles/addrole";
		$.ajax({                                      
		  url: url,              
		  type:'POST',     
		  data: data,                   
		 success: function(data)        
		  {
			if(data=='Role avaliable')
		 	{
		 		$messageError("A role with this name already exists");
		 		setTimeout(function(){window.location.reload();},2000);
		 	}
		 	else
		 	{
		 		$messageSuccess("role added successfully");
		 		setTimeout(function(){window.location.reload();},2000);
		 	}
		  }
		});
})	

$('.addUsersBtn').click(function(e){
            e.preventDefault();
        	var bValid = true;
            allFields.removeClass( "error" );

            title 	= $('#jobtitleInput');
            Company = $('#companyInput');
 			
			var separatedName  	= name.val().split(",");
			var separatedEmail 	= email.val().split(",");
			var separatedtitle  	= $('#jobtitleInput').val().split(",");
			var separatedCompany  = $('#companyInput').val().split(",");
            var selectrol = $('select[name="rools"] option:selected').attr('selectrol');
			var usertype 	= $('#usertype').val();
			var carerid 	= $('#carerid').val();

			var usertypename 	= $('#usertypename').val();

			var calling 	= $('#calling').val();
			
			usertype 	=	(typeof(usertype)=='undefined') ? '' : usertype;
			carerid 	=	(typeof(carerid)=='undefined') ? '' : carerid;
			
			bValid =  bValid && checkBothLength(separatedName,separatedEmail,'Oops, please check the details and try again');
			
			$.each(separatedEmail, function(index, chunk) {
			    bValid = bValid && checkLength( name, "username", 3, 16 );
			   // bValid = bValid && checkRegexp( name, /^[a-z]([0-9a-z_])+$/i, "Username may consist of a-z, 0-9, underscores, begin with a letter." );
			});

			$.each(separatedEmail, function(index, chunk) {
			    bValid = bValid && checkLength( email, "email", 6, 80 );
			    bValid =  bValid && checkRegexp( chunk, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "email should be in valid format eg. admin@dbee.com" );	
			});
			/*$.each(separatedEmail, function(index, chunk) {
			    bValid = bValid && checkLength( title, "job title", 3, 16 );
			});
			$.each(separatedEmail, function(index, chunk) {
			    bValid = bValid && checkLength( Company, "company ", 3, 16 );
			});*/
			         
			if ( bValid ) {

				
          	    
	            data	=	"username="+ name.val() +"&roleid="+ selectrol +"&email="+ email.val()+"&jobtitle="+ title.val()+"&company="+ Company.val() +"&require="+calling+"&usertype="+usertype+"&usertypename="+usertypename+"&carerid="+carerid ;
				url		=	BASE_URL+"/admin/import/adduser";
				$.ajax({                                      
				  url: url,              
				  type:'POST',     
				  data: data,                   
				 success: function(data)        
				  {
				  	//alert(data);exit;
					if(data!='')
				 	{
				 		$messageError("Oops, this email address is already in use: "+data+" ");
				 		setTimeout(function(){window.location.reload();}, 2000);
				 	}
				 	else
				 	{
				 		$messageSuccess("User added successfully");
				 		window.location.reload();
				 	}
				  }
				});
          	}
});
	

$('#addroleUsersBtn').click(function(e){
            e.preventDefault();
            var nameNotval = $('.nameNotval').val();
            if(nameNotval=='errornameNot'){
               $messageError("Sorry, this username is already in use");
               return false;
            }
        	var bValid = true;
            allFields.removeClass( "error" );
            title 	= $('#jobtitleInput');
            company = $('#companyInput');
            usernamerole = $('#usernamerInput');			
			var separatedName  	= name.val().split(",");
			var separatedEmail 	= email.val().split(",");
			var separatedlName  = lname.val().split(",");
			var separatedusernamer  = usernamer.val().split(",");
			var separatedpassword  = password.val().split(",");
            var selectrol = $('select[name="rools"] option:selected').attr('selectrol');         
			//var usertype 	= $('#usertype').val();
			//var usertypename 	= $('#usertypename').val();
			var calling 	= $('#calling').val();			
			//usertype 	=	(typeof(usertype)=='undefined') ? '' : usertype;
			bValid =  bValid && checkBothLength(separatedName,separatedEmail,'Oops, please check the details and try again');
			
			$.each(separatedName, function(index, chunk) {
			    bValid = bValid && checkLength( name, "user first name", 3, 16 );
			});

            $.each(separatedlName, function(index, chunk) {
			    bValid = bValid && checkLength( lname, "user last name", 3, 16 );
			});
			
			$.each(separatedEmail, function(index, chunk) {
			    bValid = bValid && checkLength( email, "email", 6, 80 );
			    bValid =  bValid && checkRegexp( chunk, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "email should be in valid format eg. admin@dbee.com" );	
			});

			/*$.each(separatedjobname, function(index, chunk) {
			    bValid = bValid && checkLength( jobname, "job name", 3, 16 );
			});
            
            $.each(separatedcompanyname, function(index, chunk) {
			   bValid = bValid && checkLength( companyname, "company name", 3, 16 );
			});
            */

			$.each(separatedusernamer, function(index, chunk) {
			    bValid = bValid && checkLength( usernamer, "username", 3, 16 );
			});

			$.each(separatedpassword, function(index, chunk) {
			    bValid = bValid && checkLength( password, "password", 3, 16 );
			});


         
			if ( bValid ) {         	    
	            data	="username="+ name.val() +"&lname="+ lname.val()+"&roleid="+ selectrol +"&email="+ email.val()+"&jobtitle="+ title.val()+"&company="+ company.val()+"&usernamerole="+ usernamerole.val()+"&passwordrole="+ password.val() +"&require="+calling;
				url		=	BASE_URL+"/admin/import/adduser";
				if(selectrol=='selectrole'){
                 $messageError("Please select a role");
                 return false;
                }
                //alert(data);
				$.ajax({                                      
				  url: url,              
				  type:'POST',     
				  data: data,                   
				 success: function(data)        
				  {
				  	//alert(data);
					if(data==1)
				 	{
				 	  $messageError("Oops, this email is already in use");
				 	  setTimeout(function(){window.location.reload();},2000);
				 	}
				 	if(data==0)
				 	{
				 	  $messageSuccess("User added successfully");
				 	  window.location.reload();
				 	}
				  }
				});
          	}
});

	function actionInviteDelete (action, userId, email,thisEl){

		var extraCaller = thisEl.attr('requestfrom');
		extraCaller = (typeof(extraCaller)=='undefined') ? '' : extraCaller;

		if(action=='deleteallvips')
		{

			data	=	$('#userform').serialize()+"&calle="+action+"&toemail="+email;
		}
		else if(action=='markedallvip')
		{
			action=='markedallvip';
			data	=	$('#userform').serialize()+"&calle="+action+"&toemail="+email;
		}
		else if(action=='addcarerid')
		{
			carerid = $("#addcarerid").val();
			data	=	"userid="+ userId +"&calle="+action+"&carerid="+carerid;
		}
		else 
		{
			data	=	"userid="+ userId +"&calle="+action+"&toemail="+email;
		}
		
		//alert(data); return false;
        
		url		=	BASE_URL+"/admin/import/invitationopration";
		$.ajax({                                      
			  url: url,                  //the script to call to get data          
			  data: data,                //you can insert url argumnets here to pass to api.php
			  method: 'POST',            //data format      
			  beforeSend: function(){
			  		
			  		if(action=='reinvitevips')
			  		{

			  			thisEl.find('.fa-repeat').addClass('fa-spin');
			  			thisEl.addClass('disabled');
			  		}
			  		else
			  		{
				  		$('#recounter_'+userId).show();
					    $('#recounter_'+userId).closest('a').find('.fa-repeat').addClass('fa-spin');//.html('<img src="'+BASE_URL+'images/loader2.gif" >');
					}   
					
			    },
			  success: function(result)          //on recieve of reply
			  { 
			  	if(action=='reinvitevips')
		  		{

		  			thisEl.find('.fa-repeat').removeClass('fa-spin');
		  			thisEl.removeClass('disabled');
		  		}
			  	     
			  	     result = result.split('~')
			  		 $('#recounter_'+userId).closest('a').find('.fa-repeat').removeClass('fa-spin');
			   		if(action=='re' )
			   		{
			   			$('#recounter_'+userId).html('('+result[0]+')');
			   		}	
			   		else if(action=='delete' || result[0]=='deletevips')
			   		{
			   			//console.log('ss');
			   			$('#deleteUserPopup').dialog('close');
			   			$('#recounter_'+userId).remove();
			   			$('#userslist_'+userId).remove();
			   			$messageSuccess(email+' has been deleted successfully');


			   			socket.emit('userActivaties', {userID:userId, uStatus:'DELETE'});

			   			if(extraCaller=='acceptuser'){
			   				setTimeout(function(){
			   					window.location.reload();
			   				},2000)

			   			}
			   		} 
			   		else if(result[0]=='reinvitevips')
			   		{
			   			if(extraCaller=='acceptuser'){
			   				$messageSuccess(' Activation link sent successfully');
			   				setTimeout(function(){
			   					window.location.reload();
			   				},2000)

			   			} else 	$messageSuccess(email+' invited again successfully');
			   		}
			   		else if(result[0]=='invideagainall')
			   		{
			   			 $dbLoader({element:'.pageTrue',close:true});
			   			 $dbLoader({
				 			page:true,
				 			height:false,
				 			loaderHtml:'<div class="loaderShow " style="text-align:center;font-weight:bold;font-size:18px;">\
					    					<br>\
					    					All pending users reinvited<br>\
					    				</div>'
				 		}); 
			   		}
			   		else if(result[0]=='deleteallvips')
			   		{	
			   			$('.deletecheck input:checked').each(function(){
			   				var userId = $(this).val();
			   				//console.log(userId);
							socket.emit('userActivaties', {userID:userId, uStatus:'DELETE'});
						});
			   			

			   			$('#deleteUserPopup').dialog('close');
			   			$dbLoader({element:'.pageTrue',close:true});
			   			$dbLoader({
				 			page:true,
				 			height:false,
				 			loaderHtml:'<div class="loaderShow " style="text-align:center;font-weight:bold;font-size:18px;">\
					    					<br>\
					    					All selected users deleted<br>\
					    				</div>'
				 		}); 

			   			setTimeout(function(){
			   				if(result[2]=='vipuser')
			   				{
				   				if(result[1]=='') window.location.href=BASE_URL+"/admin/vipuser";
				   				else window.location.href=BASE_URL+"/admin/vipuser/index/page/"+result[1];
				   			}
				   			else
				   			{
				   				if(result[1]=='') window.location.href=BASE_URL+"/admin/user";
			   					else window.location.href=BASE_URL+"/admin/user/index/page/"+result[1];
				   			}
			   			},2000);
			   		}
			   		else if(result[0]=='markedallvip')
			   		{	
			   			/*$('.deletecheck input:checked').each(function(){
			   				var userId = $(this).val();
			   				//console.log(userId);
							socket.emit('userActivaties', {userID:userId, uStatus:'DELETE'});
						});*/
			   			

			   			$('#deleteUserPopup').dialog('close');
			   			$dbLoader({element:'.pageTrue',close:true});
			   			$dbLoader({
				 			page:true,
				 			height:false,
				 			loaderHtml:'<div class="loaderShow " style="text-align:center;font-weight:bold;font-size:18px;">\
					    					<br>\
					    					All selected users marked VIP<br>\
					    				</div>'
				 		}); 

			   			setTimeout(function(){
			   				if(result[2]=='vipuser')
			   				{
				   				if(result[1]=='') window.location.href=BASE_URL+"/admin/vipuser";
				   				else window.location.href=BASE_URL+"/admin/vipuser/index/page/"+result[1];
				   			}
				   			else
				   			{
				   				if(result[1]=='') window.location.href=BASE_URL+"/admin/user";
			   					else window.location.href=BASE_URL+"/admin/user/index/page/"+result[1];
				   			}
			   			},2000);
			   		}
			   		else if(result[0]=='reinviteAll')
			   		{	
			   			$('#deleteUserPopup').dialog('close');
			   			$dbLoader({element:'.pageTrue',close:true});
			   			$dbLoader({
				 			page:true,
				 			height:false,
				 			loaderHtml:'<div class="loaderShow " style="text-align:center;font-weight:bold;font-size:18px;">\
					    					<br>\
					    					All pending users reinvited successfully.<br>\
					    				</div>'
				 		}); 

			   			setTimeout(function(){
			   				window.location.href=BASE_URL+"/admin/user/index/statussearch/3"
			   			},3000);
			   		}
			   		else if(result[0]=='deactiveAll')
			   		{	
			   			$('#deleteUserPopup').dialog('close');
			   			$dbLoader({element:'.pageTrue',close:true});
			   			$dbLoader({
				 			page:true,
				 			height:false,
				 			loaderHtml:'<div class="loaderShow " style="text-align:center;font-weight:bold;font-size:18px;">\
					    					<br>\
					    					All users deactivated successfully.<br>\
					    				</div>'
				 		}); 

			   			setTimeout(function(){
			   				window.location.href=BASE_URL+"/admin/user/index/statussearch/2"
			   			},3000);
			   		}
			   		else if(result[0]=='activateAll')
			   		{	
			   			$('#deleteUserPopup').dialog('close');
			   			$dbLoader({element:'.pageTrue',close:true});
			   			$dbLoader({
				 			page:true,
				 			height:false,
				 			loaderHtml:'<div class="loaderShow " style="text-align:center;font-weight:bold;font-size:18px;">\
					    					<br>\
					    					All users activated successfully.<br>\
					    				</div>'
				 		}); 

			   			setTimeout(function(){
			   				window.location.href=BASE_URL+"/admin/user/index/statussearch/1"
			   			},3000);
			   		}
			   		else if(result[0]=='careridsuccess')
			   		{	
			   			
			   			$('#'+userId).html(carerid);
			   			$messageSuccess('Added successfully!');
			   			$('#deleteUserPopup').dialog('close');
			   		}
			   		else
			   		{
			   			$messageError('Something went wrong!');
			   			$('#deleteUserPopup').dialog('close');
			   		}
			    }
		});

	}


   $('.roledelAction').click(function(event) {
                    var Email = $(this).attr('email');
                    var UserID = $(this).attr('userid');
                    var role = $(this).attr('role');
                    var Username = $(this).attr('username');
                    $deleterole(Email, UserID, Username,role);                 
    })

    $deleterole = function(Email, UserID, Username,role) {
    	var deluserval = 'userslist_'+UserID;
        var confirmDe = '<div id="confirBoxdel" title="Please confirm"><p>Are you sure you want to remove sub-admin access?</p></div>';
        $('body').append(confirmDe);
        $('#confirBoxdel').dialog({
            minWidth: 300,
            close:function(){
            	 $('#confirBoxdel').remove();
            },
            buttons: {
                "Yes": function() {
                    var thisPopup = $(this);
                    $.ajax({
                        type: "POST",
                        url: BASE_URL + "/admin/Manageroles/deletroleuser",
                        data: {
                            "UserID": UserID,"role": role
                        },
                        success: function(result) {
                            if (result == 'deltrue') {
                                $messageSuccess("deleted successfully");
                                thisPopup.dialog("close");
                                $('#confirBoxdel').remove();
                                $('#'+deluserval+'').hide();
                                if(localTick == false){
		                            socket.emit('chkactivitynotification', true,clientID);
		                        }
                                //window.location.reload();
                                return false;
                            }

                        }
                    });
                }
            }
        });
    }


$('.deleteroleres').click(function(event) {
        var roleid = $(this).attr('roleid');
        var rolevaldel = 'rolelist_'+roleid;
        //alert(rolevaldel);exit;
        var rolevalue = $(this).attr('rolevalue');
        var confirmDe = '<div id="confirBoxdel" title="Please confirm"><p>Are you sure you want to delete this role?</p></div>';
        $('body').append(confirmDe);
        $('#confirBoxdel').dialog({
            minWidth: 300,
            close:function(){
            	$('#confirBoxdel').remove();
            },
            buttons: {
                "Yes": function() {
                    var thisPopup = $(this);
                    $.ajax({
                        type: "POST",
                        url: BASE_URL + "/admin/Manageroles/deletroleuserall",
                        data: {
                            "roleid": roleid
                        },
                        success: function(result) {
                        	   //alert(result);exit;
                            if (result == 'deleterole') {
                            	$('#'+rolevaldel+'').hide();
                                $messageSuccess("deleted succesfully");
                                thisPopup.dialog("close");
                                $('#confirBoxdel').remove();
                                //window.location.reload();
                                return false;
                            }

                        }
                    });
                }
            }
        });
    })



	$('body').on('click','.inviteAction',function(){

		var thisEl = $(this);
		var calle	=	$(this).attr('action');
		var userId 	=	$(this).attr('userid');
		var calling	=	$(this).attr('calling');
		$('#deleteUserPopup').remove();
		calling 	=	(typeof(calling)=='undefined') ? '' : calling;
		
		var email 	=	$(this).attr('email');
		
		var markedvipvar  ='<div id="deleteUserPopup" title="Please confirm">\
							<p>Are you sure you would like to make the account(s) VIP?</p>\
							<div class="buttonWrapper"><a href="javascript:void(0);"  class="btn btn-green yesContinue">Yes</a></div>\
						</div>';
		
		var delteUser  ='<div id="deleteUserPopup" title="Please confirm">\
							<p>Are you sure you want to delete '+ email+'?</p>\
							<div class="buttonWrapper"><a href="javascript:void(0);"  class="btn btn-green yesContinue">Yes</a></div>\
						</div>';
		var inviteUser  ='<div id="deleteUserPopup" title="Please confirm">\
							<p>Are you sure you want to re invite all pending users?</p>\
							<div class="buttonWrapper"><a href="javascript:void(0);"  class="btn btn-green yesContinue">Yes</a></div>\
						</div>';
		var deactiveUser  ='<div id="deleteUserPopup" title="Please confirm">\
							<p>Are you sure you want to deactivate all active users?</p>\
							<div class="buttonWrapper"><a href="javascript:void(0);"  class="btn btn-green yesContinue">Yes</a></div>\
						</div>';
		var activeUser  ='<div id="deleteUserPopup" title="Please confirm">\
							<p>Are you sure you want to activate all users?</p>\
							<div class="buttonWrapper"><a href="javascript:void(0);"  class="btn btn-green yesContinue">Yes</a></div>\
						</div>';
		var carerUser  ='<div id="deleteUserPopup" title="Add carer id">\
							<p><input type="text" id="addcarerid" placeholder="Carer ID "></p>\
							<div class="buttonWrapper"><a href="javascript:void(0);"  class="btn btn-green yesContinue">Add</a></div>\
						</div>';


		if(calle=='openDialog'){
			$('#deleteUserPopup').remove();
			if(calling=='markedvip'){
			 	$('body').append(markedvipvar);
		    }
		    else if(calling=='addcarerid')
		    {
		    	$('body').append(carerUser);
		    }
		    else
		    {
		     	$('body').append(delteUser);
		    }
			$('#deleteUserPopup').dialog({minHeight:'auto'});
			
			$('.yesContinue').click(  function (){	
                
				if(calling=="deleteallvips")
				{
					$('#deleteUserPopup').dialog('close');
					  $dbLoader({
			 			page:true,
			 			height:false,
			 			loaderHtml:'<div class="loaderShow csvPageLoader" style="text-align:center">\
				    					<span class="fa fa-spinner fa-spin fa-3x" style="margin-bottom:10px;"></span><br>\
				    					Deleting all selected users. Please wait.<br>\
				    				</div>'
			 		});  
				}
				else if(calling=='markedvip'){
				
				  $('#deleteUserPopup').dialog('close');
				  $dbLoader({
		 			page:true,
		 			height:false,
		 			loaderHtml:'<div class="loaderShow csvPageLoader" style="text-align:center">\
			    					<span class="fa fa-spinner fa-spin fa-3x" style="margin-bottom:10px;"></span><br>\
			    					Please wait...<br>\
			    				</div>'
		 		}); 			

				}
				else
				{
					var	totalRow = $('.inviteDetailsTable  tbody tr').size();
					if(totalRow==1){
						$('.notfound').remove();
						$('.inviteDetailsTable').after('<h2 class="notfound">no posts found</h2>').hide();
						$('#paginnationWrapper').hide();
					}
				}
				if(calling=='deletevips') 
				{
					//console.log('s')
					actionInviteDelete('deletevips', ''+userId+'', ''+email+'',thisEl);
				}
				else if(calling=='deleteallvips') 
				{
				   actionInviteDelete('deleteallvips', ''+userId+'', ''+email+'',thisEl);
			    }
			    else if(calling=='markedvip') 
				{
				 	actionInviteDelete('markedallvip', ''+userId+'', ''+email+'',thisEl);
				}
				else if(calling=='addcarerid') 
				{
				 	actionInviteDelete('addcarerid', ''+userId+'', ''+email+'',thisEl);
				}
			    else
			    {
			     	actionInviteDelete('delete', ''+userId+'', ''+email+'',thisEl);
			    }
			});
			
		}		
		else if(calling=='invideagainall'){
			$('#deleteUserPopup').remove();
			$('body').append(inviteUser);
			$('#deleteUserPopup').dialog({minHeight:'auto'});
			
			$('.yesContinue').click( function (e){
				e.stopPropagation();
				$('#deleteUserPopup').dialog('close');
				  $dbLoader({
		 			page:true,
		 			height:false,
		 			loaderHtml:'<div class="loaderShow csvPageLoader" style="text-align:center">\
			    					<span class="fa fa-spinner fa-spin fa-3x" style="margin-bottom:10px;"></span><br>\
			    					Please wait while  invitation <br>sending to all pending  users<br>\
			    				</div>'
		 		});  
				if(calling=='invideagainall') actionInviteDelete('invideagainall', ''+userId+'', ''+email+'',thisEl);
			});
			
		}
		else{
			if(calling=='reinvitevips') actionInviteDelete('reinvitevips', ''+userId+'', ''+email+'',thisEl);
			if(calling=='reinviteAll' || calling=='deactiveAll' || calling=='activateAll' ) 
			{
					digMsg = (calling=='deactiveAll')?deactiveUser:inviteUser;

					digMsg = (calling=='activateAll')?activeUser:digMsg;

					$('#deleteUserPopup').remove();
					$('body').append(digMsg);
					$('#deleteUserPopup').dialog({minHeight:'auto'});
					
					$('.yesContinue').click(function (){
						$('#deleteUserPopup').dialog('close');
						  $dbLoader({
				 			page:true,
				 			height:false,
				 			loaderHtml:'<div class="loaderShow csvPageLoader" style="text-align:center">\
					    					<span class="fa fa-spinner fa-spin fa-3x" style="margin-bottom:10px;"></span><br>\
					    					Please wait...<br>\
					    				</div>'
				 		}); 	
								                
						actionInviteDelete(calling, ''+userId+'', ''+email+'',thisEl);
					});
				
			}
			else actionInviteDelete('re', ''+userId+'', ''+email+'');
			
		}


		
	});
	




	//Invite user via CSV File
	$("#im_invitecsv").click(function(){
		$("#inviteusersCSV").show('slow');
	});

	// Invitation successfully
	$( "#csvupload" ).dialog({
		    autoOpen: false,
		    height: 400,
		    width: 480,
		    modal: true,
		     buttons: {

       		 "OK": function() {
       		 	referer	=	$("#chkrefererPage").val();
		     	if(referer=="Invite")
		     	{
		     		window.location.href = BASE_URL+'/admin/import/invitedetails';
		     	}
		     	else
		     	{
		     		window.location.href = BASE_URL+'/admin/user';
		     	}
       		 }
       		},
			close: function() { 
				referer	=	$("#chkrefererPage").val();
		     	if(referer=="Invite")
		     	{
		     		window.location.href = BASE_URL+'/admin/import/invitedetails';
		     	}
		     	else
		     	{
		     		window.location.href = BASE_URL+'/admin/user';
		     	}
			}
	});

	$('#formid').submit(function(event) {
	   var file = $('input[type=file]').val();       
	   var frmidfilterprofinalty = $('#frmidfilterprofinalty').val(); 
	   if(!/(\.csv)$/i.test(file)) {
	     	$messageError("Oops, you haven't uploaded a CSV file")  ;
	        return false;   
	    } 
 
	   if(!/(\.csv)$/i.test(file)) {
	     	$messageError("Oops, you haven't uploaded a CSV file")  ;
	        return false;   
	    } 
	    var msg = "Please wait while all accounts are created";
	    if(frmidfilterprofinalty==1){
	    	 msg = "Uploading...";
	    }

	    $dbLoader({
 			page:true,
 			height:false,
 			loaderHtml:'<div class="loaderShow csvPageLoader" style="text-align:center">\
	    					<span class="fa fa-spinner fa-spin fa-3x" style="margin-bottom:10px;"></span><br>\
	    					'+msg+'<br>\
	    				</div>'
 		});  
	});

	// user order select data

	/* Knowledge Center Functionality add Folder/ Category section 
	*  @author Deepak Nagar
	*  Date 13 May 2013 */

	

	$( "#knowledgecentarDialog" ).dialog({
		    autoOpen: false,		    
		    height: 250,
		    width: 380,
		    modal: true,
		     buttons: {

       		"Add": function() {
       			
       			if((fileID) != 0) 	{   required=	'editfolder'; }  else { required=	'addfolder';}
       		 	var bValid = true;
	            allFields.removeClass( "error" );
				bValid = bValid && checkLength( folderName, "username", 3, 16 );
				
				if (/[^a-zA-Z 0-9]+/.test(folderName.val())){
				   $messageError('No special characters allowed in title');
				   return false;
				}

				secchk=  $("#ks_check").val();

				if ( bValid ) {
	          	
		            data	=	"folderName="+ folderName.val() +"&folderId="+fileID+"&securitycheck="+secchk+"&require="+required+"&prevtitle="+prevtitle  ;
					url		=	BASE_URL+"/admin/knowledgecenter/createbase";
					$.ajax({                                      
					  url: url,                  //the script to call to get data          
					  data: data,                        //you can insert url argumnets here to pass to api.php
					  type: 'POST',
					 // dataType: 'json',                //data format  
					  beforeSend: function(){

					   $("#beforecall").show();
					   $("#digForm").hide();
					   //$("#beforecall").html('<span class="invitemsgbox"><img src="'+BASE_URL+'/images/loader2.gif" ></span>');
					   removejscssfile(BASE_URL+'js/userfunctions.js', "js")
				 	 },  
					 success: function(data)          //on recieve of reply
					  {
						$("#beforecall").hide();
						data = data.split('~');
						//$messageSuccess(data[0]);
						if(data[1]==1)
						{
							kclist(0);
					    }
					    else if(data[1]==2)
						{
							//$('#kc_list_'+fileID).html(folderName.val());
							//$('#'+fileID).attr('title',folderName.val().replace("_", " "));	
							kclist(data[2]);							
					    }

					    $("#ks_check").val('1');
						setTimeout(function(){ 
						 	allFields.val( "" ).removeClass( "error" );
   		 	 				$( "#knowledgecentarDialog" ).dialog( "close" );	 }, 1000);
						}
					});
          	}
       		 }
       		},
			close: function() { 
				 allFields.val( "" ).removeClass( "error" );
				$( this ).dialog( "close" );
			}
	});



	
	
	$( "#dialog_confirm" ).dialog({
      resizable: false,
      autoOpen: false,
      height:180,
      modal: true,
      buttons: {
        "Remove Category": function() {
          	data	=	"folderId="+fileID+"&require=deletefolder"  ;
			url		=	BASE_URL+"/admin/knowledgecenter/createbase";
			$.ajax({                                      
			  url: url,                  //the script to call to get data          
			  data: data,                        //you can insert url argumnets here to pass to api.php
			  type: 'POST',
			 // dataType: 'json',                //data format    
			 success: function(data)          //on recieve of reply
			  {
				data = data.split('~');
				$( "#deletemsg" ).html(data[0]);
				
				if(data[1]==3)
				{
					//var tnc = '#kclistrec=kclistRec_'+fileID;
					$('li[kclistrec="kclistRec_'+fileID+'"]').remove();
					$('.ksfolderlist li:first').trigger('click');
					//$(tnc).remove();
					//window.location.href = BASE_URL+'/admin/knowledgecenter/';

				}

			    $("#ks_check").val('1');
			    $( "#deletemsg" ).html("This Category will be permanently deleted and cannot be recovered. Are you sure?");
				setTimeout(function(){ 
				allFields.val( "" ).removeClass( "error" );
	 	 				$( "#dialog_confirm" ).dialog( "close" ); 	 }, 500);
				}
			});
        }
      }
    });

	
// Taking reference of knowledge center file


$('form#formknowledgecenter').submit(function(e) {

		e.stopPropagation();
	 	e.preventDefault();

 	   var fileName		= $('input[type=file]').val(); 
	   var fileTitle 	= $('#filetitle').val();
	   var folderId 	= $('#folderPid').val(); 
	   var folderDir	= $('#folderdir').val(); 
	   var pdfuser 		= $('#myTags').val(); 
	   var userset 		= $('#pdfuserset').val(); 

	 var file = $('input[type=file]').val(); 

    var xhr = new XMLHttpRequest();
    
    var url		=	BASE_URL+"/admin/knowledgecenter/createfilesbase";
    xhr.open('post', url, true);
	
    var data = new FormData;
    data.append('file', fileName);
    data.append('fileName', fileName);
    data.append('fileTitle', fileTitle);
    data.append('folderId', folderId);
    data.append('folderDir', folderDir);
    data.append('userdata', '{"pdfuser":'+pdfuser+',"fileName":'+fileName+',"fileTitle":'+fileTitle+',"folderId":'+folderId+',"folderDir":'+folderDir+'}'); // for jsFiddle
    data.append('json', '{"foo":"bar"}');
    xhr.send(data);
    (xhr.upload || xhr).addEventListener('progress', function(e) {
        var done = e.position || e.loaded
        var total = e.totalSize || e.total;
        console.log('xhr progress: ' + Math.round(done/total*100) + '%');
    });
    xhr.addEventListener('load', function(e) {
        console.log('xhr upload complete', e, this.responseText);
        $("#beforecallfile").hide();
				$("#exp_condition").hide();
				
				data = data.split('~');

				if(data[1]==1)
				{
					$('body').append($('<form/>', {
	                    id: 'form',
	                    method: 'POST',
	                    action: '#'
			        }));

					$('#form').append($('<input/>', {
					    type: 'hidden',
					    name: 'parentId',
					    value: folderId
					}));

					$('#form').append($('<input/>', {
					    type: 'hidden',
					    name: 'folderName',
					    value: folderDir
					}));
					if(localTick==false)
					socket.emit('chkactivitynotification', 1,clientID);
					$('#form').submit();
				}
				
				socket.emit('chkactivitynotification', true,clientID);
    });
 });



/*$('form#formknowledgecenter').submit(function(event) {
	alert('ggggggg');
	   var fileName		= $('input[type=file]').val(); 
	   var fileTitle 	= $('#filetitle').val();
	   var folderId 	= $('#folderPid').val(); 
	   var folderDir	= $('#folderdir').val(); 
	   var pdfuser 		= $('#myTags').val(); 
	   var userset 		= $('#pdfuserset').val(); 

	   if(userset==0) {
	       userset='';  
	    }  

	  	var formData = new FormData($(this)[0]);
	  	//var formData = $('form#formknowledgecenter').serialize();
	  	formData.append("pdfuser", pdfuser);
	  	formData.append("userset", userset);
	  	//formData = {'pdfuser':pdfuser,'fileName':fileName,'fileTitle':fileTitle,'folderId':folderId,'folderDir':folderDir}
	  	//formData	=	"pdfuser="+ pdfuser +"&fileName="+fileName+"&fileTitle="+fileTitle+"&folderId="+folderId+"&folderDir="+folderDir;
	  	if ( !$('input#folderPid').is('[readonly]') ) {  
	  		//alert("Internal Error.Please use secure way to add a category.");  
	  		$messageError('Internal Error.Please use secure way to add a category');    
	        return false;  
	    }
	  	  	
	    if(!folderId.match('^(0|[1-9][0-9]*)$') ) {
	       $messageError("Internal Error. Please try again after some time.");      
	        return false;   
	    } 

	    if(!fileTitle) {
	        $messageError("Please enter a title");      
	        return false;   
	    }  
	    if($('.uploaderror').val()=='') {
	        $messageError("Please select a file to upload");      
	        return false;   
	    }  

	    if (/[^a-zA-Z 0-9]+/.test(fileTitle) ) {
	        $messageError("No special characters allowed in title.");      
	        return false;   
	    }   
	     
		/*if(!/(\.pdf)$/i.test(fileName)) {
	        $messageError("Oops, you haven't uploaded a PDF");      
	        return false;   
	    }  *
		if (!(/\.(xlsx|xls|docx|doc|txt|ppt|pdf|gif|jpg|jpeg|png|mp3|mp4|MP4)$/i).test(fileName)) {              
		    $messageError("Sorry you cannot upload this file type");      
		        return false;               
	    }
		var url		=	BASE_URL+"/admin/knowledgecenter/createfilesbase";


	//var formData = new FormData();
    if($('#myFile').val()=='') {
     alert("Please choose file!");
     return false;
    }
    $('#shviid').show();
    var file = fileName;
    formData.append('uploadfile', file);
   var xhr = new XMLHttpRequest(); 
    xhr.open('post', url, true);
    xhr.upload.onprogress = function(e) {
      if (e.lengthComputable) {
        var percentage = (e.loaded / e.total) * 100;
       // $('div.progress div').css('width', percentage.toFixed(0) + '%');
        // $('div.progress div').html(percentage.toFixed(0) + '%');

        $('#shviid').append('<div id="mesageNotfiOverlay" class="loaderOverlay3"> </div>')	;				        		
		$('#mesageNotfiOverlay').html('<div class="msgNoticontent">\
		<div class="loaderShow">\
		<span class="loaderImg"></span><br>\
		</div>\
		<br>\
		<div class="progressBarWrp" style="margin-top:20px;" data-loaded=""><div class="progressBar" ></div></div>\
		</div>');
		//$('#mesageNotfiOverlay .progressBar').animate({width: percentage.toFixed(0)}, 500,function(){
			$('#mesageNotfiOverlay .progressBarWrp').attr('data-loaded', percentage.toFixed(0) + '%');
			//});
		    
      }
    };
    xhr.onerror = function(e) {
      alert('An error occurred while submitting the form. Maybe your file is too big');
    };
   xhr.onload = function() {
      var file = xhr.responseText;
      $('#shviid').hide();
       $('div.progress div').css('width','0%');
       $('div.progress').hide();

      //showMsg("alert alert-success", "File uploaded successfully!"); 
      $('#myFile').val('');    
    };
	console.log(formData);
    xhr.send(formData);
   return false;
   
 });*/







	

	function loadHeadScript(url) 
	{
		//alert(url);
	    var script = document.createElement("script");
        script.setAttribute("type", "text/javascript");
        script.setAttribute("src", url);
        var head = document.getElementsByTagName("head")[0];
        head.appendChild(script);
    }
    function removejscssfile(filename, filetype)
    {
	 	var targetelement=(filetype=="js")? "script" : (filetype=="css")? "link" : "none" //determine element type to create nodelist from
	 	var targetattr=(filetype=="js")? "src" : (filetype=="css")? "href" : "none" //determine corresponding attribute to test for
	 	var allsuspects=document.getElementsByTagName(targetelement)
	 	for (var i=allsuspects.length; i>=0; i--)
	 	{ //search backwards within nodelist for matching elements to remove
	 	 	if (allsuspects[i] && allsuspects[i].getAttribute(targetattr)!=null && allsuspects[i].getAttribute(targetattr).indexOf(filename)!=-1)
	  		 allsuspects[i].parentNode.removeChild(allsuspects[i]) //remove element by calling parentNode.removeChild()
	 	}
	}
	

}); // End of main function

function selectallcheckbox(thisEl,containerEl)
{  
	var thisEl = $(thisEl);
	var attClass= thisEl.attr('class');
	var sasCl 	= $(containerEl).hasClass('checked');

	var len =$(containerEl).find(':checkbox').length;

	if(len>0)
	{
		if(sasCl==false) 
		{
			if(attClass=='deleteallvips') thisEl.closest('td').find('a').attr('action','openDialog');
			thisEl.closest('td').find('a').removeClass('disabled').addClass('inviteAction');
			
			
			$(containerEl).addClass('checked');
			$(containerEl).find(':checkbox').prop('checked', true);
		}
		else 
		{
			thisEl.closest('td').find('a').addClass('disabled').removeClass('inviteAction');
			if(attClass=='deleteallvips') thisEl.closest('td').find('a').attr('action','')
			$(containerEl).removeClass('checked');
			$(containerEl).find(':checkbox').prop('checked', false);
		}
	}
	else
	{
		 $messageError("Records not available to perform this action."); 
	}
}

function selectsinglecheckbox(thisEl,containerEl)
{  
	var thisEl = $(thisEl);
	var attClass= 'deleteallvips';
	var sasCl 	= $(containerEl).hasClass('checked');
	var p = thisEl.closest('tbody');
	var cked = $('.deletecheck input[type="checkbox"]:checked', p).size();
	
	//var len =thisEl.attr('checked');
	if(cked>0)
	{
		
		if(attClass=='deleteallvips') $('.deleteallvips').closest('td').find('a').attr('action','openDialog');
		$('.deleteallvips').closest('td').find('a').removeClass('disabled').addClass('inviteAction');
	
	}
	else 
	{
		$('.deleteallvips').closest('td').find('a').addClass('disabled').removeClass('inviteAction');
		if(attClass=='deleteallvips') $('.deleteallvips').closest('td').find('a').attr('action','')

	}
	
}

function selectpasscheckbox(thisEl){
	var thisEl = $(thisEl);
	var typeVal = $('#passwordInput').attr('type');
	if(typeVal=='password'){
          $('#passwordInput').attr('type','text');
	}
	if(typeVal=='text'){
          $('#passwordInput').attr('type','password');
	}
}



