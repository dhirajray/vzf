$(function() {	
	
	var comarray = JSON.parse(jsonCompany);	
	var myArray = JSON.parse(jsonData);

	
	
	var messageSearchWidget = '<h2>\
								<i class="fa fa-search"></i><span class="labelSidebar"> Message search<div  class="btn btn-yellow btn-rss-edit pull-right msgdRefreshIcon" style="padding-bottom:2px;">Reset </div></span>\
							</h2>\
							<form id="messagesearchform" action="'+BASE_URL+'message/search" method="post" name="messagesearchform">\
								<input type="hidden" id="sertextmsghidden" class="messagesearchinput" name="sertextmsghidden">\
								<input type="hidden" id="sertextmsg-compny" class="messagesearchcom" name="sertextmsg">\
								<div style="margin-top:10px; " ></div>\
								<div class="calMsgWrp">\
								<div class="calMsg"><input id="sertextmsgfrom" type="text"  value="" placeholder="Date from" name="filterName" maxlength="15"></div>\
								<div class="calMsg "><input id="sertextmsgto" type="text"  value="" placeholder="Date to" name="filterName" maxlength="15"></div>\
								</div>\
								<div style="clear:both"></div>\
								<div class="btn btn-yellow btn-rss-edit pull-right" style="margin-top:10px;padding:5px 10px;" id="btnsubmsgsearch" >Search</div>\
							</form>';
	
function formatArrayUser(myArray) {
    return '<img src="'+IMGPATH+'/users/small/'+myArray.profilepic+'" width="20" height="20" />' + myArray.text;
}

$('#messageSearchCont').html(messageSearchWidget);
var loadselect = function( event ) {
	$("#sertextmsghidden").select2({
		width:'100%',
		placeholder: "Search name",
		allowClear: true,
		formatResult: formatArrayUser,
		formatSelection: formatArrayUser,
		data:myArray
	}).change(function(){
		//if($(this).val()!='') $('.msgdRefreshIcon').show();
		 if($("#sertextmsg-compny").val()=='' && $("#sertextmsgfrom").val()=='' && $("#sertextmsgto").val()=='') $('.msgdRefreshIcon').hide();

	}); 

	var chkreset = function(){ alert('chkreset');
		if($("#sertextmsgto" ).val()=='' || $("#sertextmsghidden").val=='' || $("#sertextmsg-compny")==''){
			$('.msgdRefreshIcon').hide();
		}
	}

	$("#sertextmsg-compny").select2({
		width:'100%',
		placeholder: "Search company",
		allowClear: true,
		data:comarray
	}).change(function(){
		//if($(this).val()!='') $('.msgdRefreshIcon').show();
		 if($("#sertextmsghidden").val()=='' && $("#sertextmsgfrom" ).val()=='' && $("#sertextmsgto").val()=='') $('.msgdRefreshIcon').hide();
		//chkreset();

	}); 
	$( "#sertextmsgto" ).datetimepicker({
  		dateFormat:'dd-mm-yy',
  		showTimepicker: false,
  		showButtonPanel: false,
  		changeMonth: true,
  		changeYear: true,
  		maxDate:0,
  		 onSelect: function(dateText) {
  			$sD = new Date(dateText);
  			$(this).datepicker('hide');
  			$("input#DateTo").datepicker('option', 'maxDate', dateText);
  			//$('.msgdRefreshIcon').show();	  			
  		}, 
   		onClose: function( selectedDate ) {
	        $( "#sertextmsgfrom" ).datepicker( "option", "maxDate", selectedDate );
	       // if($(this).val()!='') $('.msgdRefreshIcon').show();
			 if($("#sertextmsghidden").val()=='' && $("#sertextmsg-compny").val()=='' && $("#sertextmsgto").val()=='') $('.msgdRefreshIcon').hide();
	      }
  	});
  		$("#sertextmsgfrom" ).datetimepicker({
  		dateFormat:'dd-mm-yy',
  		showTimepicker: false,
  		showButtonPanel: false,
  		changeMonth: true,
  		changeYear: true,
  		maxDate:0,
  		 onSelect: function(dateText) {
    		$sD = new Date(dateText);
  			$("input#DateTo").datepicker('option', 'maxDate', dateText);
  			$(this).datepicker('hide');
  			//$('.msgdRefreshIcon').show();
  			//chkreset();
  		},
  		onClose: function( selectedDate ) {
	        $( "#sertextmsgto" ).datepicker( "option", "minDate", selectedDate );
	        //if($(this).val()!='') $('.msgdRefreshIcon').show();
			 if($("#sertextmsghidden").val()=='' && $("#sertextmsg-compny").val()=='' && $("#sertextmsgto").val()=='') $('.msgdRefreshIcon').hide();
	      }
  	});


};
loadselect();	
	
	  	
	$('body').on('click','#btnsubmsgsearch',function(){

	  		var userID= $.trim($('#sertextmsghidden').val());
	  		$('#startnewmsg').val('10');
	  		var searchudate = '0';
	  		var searchfordate = '0';
	  		userID = (typeof userID=='undefined')? '':userID;
	  		var companyName= $.trim($('#sertextmsg-compny').val());
	  		companyName = (typeof companyName=='undefined')? '':companyName;
	  		var dateFrom= $('#sertextmsgfrom').val();
	  		dateFrom = (typeof dateFrom=='undefined')? '':dateFrom;
	  		var dateTo= $('#sertextmsgto').val();
	  		dateTo = (typeof dateTo=='undefined')? '':dateTo;
	  		var search=1;
	  		// seting hidden value

	  		$('#msgsearch_name').val(userID);
	  		$('#msgsearch_company').val(companyName);
	  		$('#msgsearch_datefrom').val(dateFrom);
	  		$('#msgsearch_dateto').val(dateTo);issearch
	  		$('#issearch').val('1');
	  		
	  		if(dateFrom!='' && dateTo==''){
	  			$dbConfirm({content:"Plese select to date", yes:false,error:true});
	  			return false;
	  		}
	  	
	  		if(dateFrom=='' && dateTo=='' && userID=='' && companyName==''){
	  			$dbConfirm({content:"Plese select search option", yes:false,error:true});
	  			return false;
	  		}
	  		if(userID=='' && dateFrom!=''){
	  			searchudate = 1;
	  		}	  		
	  		

	  		if(companyName!='' || userID =='' ){
	  			$('.msgdRefreshIcon').show();	 
	  			newmessagefeed(0,userID,companyName,dateFrom,dateTo,search);
	  		}else{
	  			 var myjson = {'uid':userID, 'datefrom':dateFrom, 'dateto':dateTo, 'search':search}
        		messagefeed(myjson);
	  			
	  		}
	  		loadselect();
	  	});	  
	  
	  	$('body').on('click','.msgdRefreshIcon',function(){	  		
	  		$('#messageSearchCont').html(messageSearchWidget);	 
	  		loadselect();
	  		newmessagefeed('0');
	  		
	  	});	

	  	$('#messagesearchform').closest('#messageSearchCont').addClass('noCollapse active');


	 $('body').on('click', '#messagelist', function(e) {
          e.stopPropagation();
	      e.preventDefault();    
	      $('#dbees-feeds-wrapper').remove();
	      $('#leftListing').html('<ul id="dbee-feeds" class="postListing"></ul>');
	      newmessagefeed('0');
	      $('#issearch').val('0');
	      
	    });

	  
});