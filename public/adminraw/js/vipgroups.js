function checkLength( o, n, min, max ) {
  //if ( o.val().length > max || o.val().length < min ) {
  	if (  o.val().length < min ) {
    
    $messageError( "The " + n + " must be at least " + min + " characters long" );
    return false;
  } else {
    return true;
  }
}

function checkRegexp( o, regexp, n ) {
  if ( !( regexp.test($.trim( o) ) ) ) {
    
    $messageError( n );
    return false;
  } else {
    return true;
  }
}

function checkBothLength( Lname, Lemail, n ) {
  if ( Lname.length != Lemail.length ) {
    
    $messageError( n );
    return false;
  } else {
    return true;
  }
}
$(function(){
	
	$('.groupbox li').click(function(){
		
		 var groupId 	= $(this).attr('chartgroupid');
		 var groupname 	= $(this).attr('groupname');
		
		 var url 	= BASE_URL+'/admin/savedcharts/chartsdetail';
		 var data 	= 'groupId='+groupId+'&groupname='+groupname;
		 var thisEl = $(this);
			$.ajax({
				url: url,
				type: "POST",
				data:data,
				beforeSend: function(){                        
	                    var htmlLoader = $(document.createElement('div')).addClass('loaderBoxOverlay');
	                    //var parentEl =  thisEl.closest('.dashBlock');
	                    $('#grpRightListinguL').append(htmlLoader);
	                  },
				success:function(data){				
					 var param=data.split("~");          
					 
					 $('#grpRightListinguL').html(param[0]);
						thisEl.closest('ul').find('.active').removeClass('active');
						thisEl.addClass('active');
					
					$('#grpRightListing .cateName span').html(groupname+param[1]);
					
				
				}
			});
	});

	

	$('.updatevipgroup').click(function()
    {
    	
    	$("#searchContainer").show();
    	$("#vipgroupsubmit").hide();
    	$("#vipgroupedit").show();
    	
    	$('html, body').animate({
	        scrollTop: $("body").offset().top
	    }, 800);
    	var closeser	= $(this).closest('li');

    	var grp_id	 	= $(this).attr('gid');
        var grp_name 	= closeser.find('#up_grp_name').val();
		var discription = closeser.find('#up_grp_des').val();
		var picture		= closeser.find('#up_grouppicture').val();
		var memid 		= closeser.find('#up_memid').val();
		var restrict 	= closeser.find('#up_restrict').val();
		var expert 		= closeser.find('#up_expert').val();

		//alert(grp_name+' # '+picture+' # '+restrict+' # '+expert+' # ');
		$('#grp_id').val(grp_id);
		$('#grp_name').val(grp_name);
		$('#grp_des').val(discription);	
		$('#roupdategrouppicture').val(picture);	
		$('#membersofgroup').val(memid);	

		if(restrict==1) 
		{
			$("#restrict").attr( "checked", true );
		}

		if(expert==1) 	 $("#expert").attr( "checked", true );
		
    });
	
  $('#vipgroup').submit(function()
    {
       // formdata = $('form#vipgroup').serialize();
       //	$("#vipgroupsubmit").show();
    	//$("#vipgroupedit").hide();

    	var groupeditcase = $('#grp_id').val();


       var bValid = true;
       var grp_name = $('#grp_name').val();
	    if (grp_name == '')
	    { 
	      $messageError('Please add a Group name');
	      return false;
	    }
		
		var discription = $('#grp_des').val();
	    if (discription == '')
	    { 
	      $messageError('Please add a Group description');
	      return false;
	    }

	    var picture		= $.trim($('form#vipgroup input[name=grouppicture]').val()); 
	    if(picture!='')
		{
			if(!/(\.jpg)|(\.jpeg)|(\.png)|(\.png)$/i.test(picture)) {
			$messageError( "Only well formated images are allowed. " );  
			return false;   
			}  
	    }

	    chkedradio = $.trim($('[name="addinvite"]:checked').attr('id'));

	    if(groupeditcase=='')
	    {
		    if(chkedradio=='siteusers')
		    {
		    	var invietvipuser	= $('#invietvipuser').val();
			    if(invietvipuser!="")
			    {
			    	selecteduser = $('[name="groupuser[]"]:checked').length;
			    	if(selecteduser<1) $messageError( "Please choose group members. " );  
			    }
			    else
			    {
			    	$messageError( "Please invite users to the Group" ); return false; 
			    }	
		    }
		    else
		    {
		    	var uname = $('#vipusersname');
				var vipusersemail = $('#vipusersemail');

			    var fileName	= $.trim($('form#vipgroup input[name=uploadvipcsv]').val());
			    if(fileName!='')
			    {
				    if(!/(\.csv)$/i.test(fileName)) {
				        $messageError("We accept only well formated CSV files.");      
				        return false;   
				    }  
				}
				else if(uname.val()!="" && vipusersemail.val()!="")
				{
					var separatedName  = uname.val().split(",");
					var separatedEmail = vipusersemail.val().split(",");

					bValid =  bValid && checkBothLength(separatedName,separatedEmail,'Oops, please check the details and try again');
						
					$.each(separatedEmail, function(index, chunk) {
					    bValid = bValid && checkLength( uname, "vipusersname", 3, 16 );
					   // bValid = bValid && checkRegexp( name, /^[a-z]([0-9a-z_])+$/i, "Username may consist of a-z, 0-9, underscores, begin with a letter." );
					});

					$.each(separatedEmail, function(index, chunk) {
					    bValid = bValid && checkLength( vipusersemail, "vipusersemail", 6, 80 );
					    bValid =  bValid && checkRegexp( chunk, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "email should be in valid format eg. admin@dbee.com" );	
					});

					if(!bValid) return false;
				}
				else{
					$messageError( "Please add and invite users to group. " );return false; 
				}
			}
		}
	   
    });

	
});
