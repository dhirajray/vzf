/*
this file contain all type of search represantations, being used on channels for dbee
@ authour Deepak Nagar
@ Lincesed to Dbee.me
@ Start date 9 Apr 2013


*/

$(document).ready(function () 
{
    

    $('#score_search').on('click',function(){
										  
			//alert(''); return false;
		
			 $('html, body').animate({ scrollTop: $("#box_body").offset().top }, 500);
			var BASE_URL 	= $("#BASE_URL").val();	
			var fields 		= $("#search_form_score").serialize();

			var usrcount	= 0;
			if ($( "#score_owner" ).val().length>2) { usrcount = 1; };
			if ($( "#score_by" ).val().length>2) { usrcount++; };

			if (usrcount<1) {
				alert('Please select either owner or user of score and must be 3 characters long!');
				return false;
			};


			var dtcount		= 0;
			if ($( "#created_from" ).val()!='') { dtcount = 1; };
			if ($( "#created_to" ).val()!='') { dtcount++; };

			if (dtcount==1) {
				alert('Please select valid date range!');
				return false;
			};
			
			//console.log(fields);
			
			data	=	fields ;
			url		=	BASE_URL+"/admin/dashboard/scoresearch";
			$.ajax({                                      
				  url: url,                  //the script to call to get data          
				  data: data,                        //you can insert url argumnets here to pass to api.php
				  method: 'POST',
				  //dataType: 'json',                //data format    
				  beforeSend: function(){
					   $("#searchresultvis").hide();
					   $("#searchresulthide, #paginnationWrapper").hide();
					   $("#pagigZend").hide();
					   $("#showmore").hide(); 
					   $("#beforecall").show();
					   $("#beforecall").html('<span class="notfound"><img src="'+BASE_URL+'img/loader3.gif" ></span>');
					 
				  },
				  success: function(data)          //on recieve of reply 
				  {
					 $("#searchresulthide, #paginnationWrapper").hide();
					 $("#beforecall").hide();
					 $("#pagigZend").hide();
					 $("#searchresultvis").show();
					 
					 $("#searchresultvis").html(data);
					 var tot 	=$("#listingResults li").length;
					
					 $('#listingTotalResults').html("Total Results : <strong>"+tot+"</strong>");
					 if(tot >5 )
					 {
						$("#showmore").show(); 
					 }
					 
					  $('#listingResults li:gt(4)').hide();
					 // load();	
					
					  
				  }
			});	
						
	});
	
	$('#cmntsearch').on('click',function(){
										  
			//alert(''); return false;
		
			 $('html, body').animate({ scrollTop: $("#box_body").offset().top }, 500);
			var BASE_URL 	= $("#BASE_URL").val();	
			var fields 		= $("#search_form_cmnt").serialize();

	
			var usrcount		= 0;

			if ($( "#cmnt_owner" ).val().length>2) { usrcount = 1; };
			if ($( "#cmnt_by" ).val().length>2) { usrcount++; };

			if (usrcount<1) {
				alert('Please select either owner or user of comments and must be 3 characters long!');
				return false;
			};
			
			

			var dtcount		= 0;
			if ($( "#created_from" ).val()!='') { dtcount = 1; };
			if ($( "#created_to" ).val()!='') { dtcount++; };

			if (dtcount==1) {
				alert('Please select valid date range!');
				return false;
			};
			
			//console.log(fields);
			
			data	=	fields ;
			url		=	BASE_URL+"/admin/dashboard/commentssearch";
			$.ajax({                                      
				  url: url,                  //the script to call to get data          
				  data: data,                        //you can insert url argumnets here to pass to api.php
				  method: 'POST',
				  //dataType: 'json',                //data format    
				  beforeSend: function(){
					   $("#searchresultvis").hide();
					   $("#searchresulthide, #paginnationWrapper").hide();
					   $("#pagigZend").hide();
					   $("#showmore").hide(); 
					   $("#beforecall").show();
					   $("#beforecall").html('<span class="notfound"><img src="'+BASE_URL+'img/loader3.gif" ></span>');
					 
				  },
				  success: function(data)          //on recieve of reply 
				  {
					 $("#searchresulthide, #paginnationWrapper").hide();
					 $("#beforecall").hide();
					 $("#pagigZend").hide();
					 $("#searchresultvis").show();
					 
					 $("#searchresultvis").html(data);
					 var tot 	=$("#listingResults li").length;
					
					 $('#listingTotalResults').html("Total Results : <strong>"+tot+"</strong>");
					 if(tot >5 )
					 {
						$("#showmore").show(); 
					 }
					 
					  $('#listingResults li:gt(4)').hide();
					 // load();	
					
					  
				  }
			});	
						
	});
	
	$('#groupsearch').on('click',function(){
										  
			//alert(''); return false;
		
			 $('html, body').animate({ scrollTop: $("#box_body").offset().top }, 500);
			var BASE_URL 	= $("#BASE_URL").val();	
			var fields 		= $("#search_form_group").serialize();
			var dtcount		= 0;
			if ($( "#created_from" ).val()!='') { dtcount = 1; };
			if ($( "#created_to" ).val()!='') { dtcount++; };

			if (dtcount==1) {
				alert('Please select valid date range!');
				return false;
			};
			
			//console.log(fields);
			
			data	=	fields ;
			url		=	BASE_URL+"/admin/dashboard/groupsearch";
			$.ajax({                                      
				  url: url,                  //the script to call to get data          
				  data: data,                        //you can insert url argumnets here to pass to api.php
				  method: 'POST',
				  //dataType: 'json',                //data format    
				  beforeSend: function(){
					   $("#searchresultvis").hide();
					   $("#searchresulthide, #paginnationWrapper").hide();
					   $("#pagigZend").hide();
					   $("#showmore").hide(); 
					   $("#beforecall").show();
					   $("#beforecall").html('<span class="notfound"><img src="'+BASE_URL+'img/loader3.gif" ></span>');
					 
				  },
				  success: function(data)          //on recieve of reply 
				  {
					 $("#searchresulthide, #paginnationWrapper").hide();
					 $("#beforecall").hide();
					 $("#pagigZend").hide();
					 $("#searchresultvis").show();
					 
					 $("#searchresultvis").html(data);
					 var tot 	=$("#listingResults li").length;
					
					 $('#listingTotalResults').html("Total Results : <strong>"+tot+"</strong>");
					 if(tot >5 )
					 {
						$("#showmore").show(); 
					 }
					 
					  $('#listingResults li:gt(4)').hide();
					 // load();	
					
					  
				  }
			});	
						
	});

	$( "#created_from" ).datepicker({
	  defaultDate: "+1w",
	  changeMonth: true,
	  numberOfMonths: 2,
	  dateFormat:"yy-mm-dd",
	  onClose: function( selectedDate ) {
	    $( "#created_to" ).datepicker( "option", "minDate", selectedDate );
	  }
	});
	$( "#created_to" ).datepicker({
	  defaultDate: "+1w",
	  changeMonth: true,
	  numberOfMonths: 2,
	  dateFormat:"yy-mm-dd",
	  onClose: function( selectedDate ) {
	    $( "#created_from" ).datepicker( "option", "maxDate", selectedDate );
	  }
	});
	
	
		
});
