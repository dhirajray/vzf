/*
this file contain all type of search represantations, being used on channels for dbee
@ authour Deepak Nagar
@ Lincesed to Dbee.me
@ Start date 9 Apr 2013
*/

$(document).ready(function () 
{
    var ev	= 1;
	var ev2	= 1;
	$("#open_user").show();	

	$('#openSearchBlock').click(function(e){
			e.preventDefault();
			$(this).toggleClass('openBtn');
			$('#searchWrapper').toggleClass('openSearch');
			$('#searchContainer').slideToggle();
			$('#select_type').show();
		});

	$('.openSearchBlock').click(function(e){
			e.preventDefault();
			$('#select_type').show();
			$(this).toggleClass('openBtn');
			$('#searchWrapper').toggleClass('openSearch');
			$('#searchContainer').slideToggle();
			if($('#addevent').is(':visible')==true){
				
				$('#addevent')[0].reset();
				$('#logoImage').html('');
				$('#backgroundImage').html('');
			}
			$('#specialdbsubmit').val('Save');
		});
		if(urlHash == '#createEvent'){ 
			$('#searchContainer').slideToggle();
			$('#searchWrapper').toggleClass('openSearch');
		}
		$("#uploadpdf").show();
        $(".orSpan").show();
        $("#addpdflink").show();

	$('#add_condition').on('click',function(){
			//$("#open_user").show();								
			ev2++;							 
			if(ev2%2==0)
			{
		  		$('#exp_condition').show('slow');
			} else {
				$('#exp_condition').hide('slow');
			}
    });
	
	$('#search_fields').on('change',function(){
			var id	=	$(this).val();
			$(".attributs").hide();	
			$("#open_"+id).show();	
	});
	
	function getdbeeTyp(type)
	{
		switch(type)
		{
			case '1' :
				 return  ' Text Posts';
				break;
			case '2' :
				 return  ' Link Posts';
				break;
			case '3' :
				 return  ' Pic Posts';
				break;
			case '4' :
				 return  ' Media Posts';
				break;
			case '5' :
				 return  ' Polls Posts';
				break;
			default :
				 return  ' All Posts';
				break;	
		}
		//return ret;
	}


	var i	=	0;
	$('#stepcondition').on('click',function(e){
			e.preventDefault();
			w=	$(".addedcondition").children(".conditiontext");
			if(w.length>0) { i = w.length; };
			var search_fields	=	$('#search_fields').val();
			
			var dbeetype		=	$('#dbeetype').val();
			if(dbeetype!=''){ dbval	= dbeetype} 			
			var showtype		=	getdbeeTyp(dbval);
			var haselemchk		=	'';
			
			var BASE_URL 	= $("#BASE_URL").val();	
			
			switch(search_fields)
			{
				case 'user' :
						i++;
						var chkbox	=	$("input:radio[name=chktype]:checked");
						var chkdval	=	 chkbox.val()  ;
						var chkdid	=	chkbox.attr('id')  ;
						var condi	=	 $('#usercondition').val();
						if(condi=='eq') { dcondi = 'is'; } else { dcondi = condi; }
						var textval	=	 $("#user_text").val()  ;
						if(textval.length<2) {  $messageError('Keywords should be atleast 2 charactors long'); return false;}
						if((chkdid!='' && chkbox.length>0 ) && textval!='' )
						{
							$('.addedcondition').show();
							$('.addedcondition').append('<div class="whiteBox conditiontext chk_'+i+'" > search  '+ showtype +' Where '+chkdval+' '+dcondi+ ' <strong>' + textval +'</strong><a href="#"  id="'+i+'" class="closeCondition"></a></div>');
			
							$('.addedcondition').append('<span class="conditiontext txt_'+i+'" style="display:none" ><input type="hidden" name="tblUsers[]" id="search_on_'+i+'" value="'+dbeetype+'"><input type="hidden" name="tblUsers[]" id="search_for_'+i+'" value="'+chkdid+'"><input type="hidden" name="tblUsers[]" id="search_condition_'+i+'" value="'+condi+'"><input type="hidden" name="tblUsers[]" id="search_value_'+i+'" value="'+textval+'"></span>');
							
							$("#dbeetype").closest('.select').hide() ;
							$("#dbeetypechk").show();
							$("#dbeetypechk").html(" : "+showtype)  ;
							
							/*$('.closeCondition').on('click', function(){
									aa	=	$(this).attr('id');			
									$('.chk_'+aa).remove();
									$('.txt_'+aa).remove();
									haselemchk			=	$(".addedcondition").children(".conditiontext");
									if(haselemchk.length<1){ 
										$("#dbeetype").closest('.select').show();
										$("#dbeetypechk, .addedcondition, .searchbutton, .savefilter").hide(); 
									}	
							});	*/
						} else
						{
							$messageError('Please choose your preference first');
							return false;
						}
						
						break;	
				case 'title' :	
						i++;
						var chkbox	=	 $("input:radio[name=chkdbee]:checked")  ;
						var chkdval	=	 chkbox.val()  ;
						var chkdid	=	chkbox.attr('id')  ;
						var condi	=	 $('#dbeecondition').val();
						if(condi=='eq') { dcondi = 'is'; } else { dcondi = condi; }
						var textval	=	 $("#dbee_text").val()  ;

						if(textval.length<2) { $messageError('Keywords should be 2 charactor long'); return false;}


						if((chkdid!='' && chkbox.length>0 ) && textval!='' )
						{
							$('.addedcondition').show();
							$('.addedcondition').append('<div class="whiteBox conditiontext chk_'+i+'" > search  '+ showtype +' Where '+chkdval+' '+dcondi+ ' <strong>' + textval +'</strong><a href="#"  id="'+i+'" class="closeCondition"></a></div>');
							
							$('.addedcondition').append('<span class="conditiontext txt_'+i+'" style="display:none "><input type="hidden" name="tblDbees[]" id="search_on_'+i+'" value="'+dbeetype+'"><input type="hidden" name="tblDbees[]" id="search_for_'+i+'" value="'+chkdid+'"><input type="hidden" name="tblDbees[]" id="search_condition_'+i+'" value="'+condi+'"><input type="hidden" name="tblDbees[]" id="search_value_'+i+'" value="'+textval+'"></span>');
							
							$("#dbeetype").closest('.select').hide()  ;
							$("#dbeetypechk").show();
							$("#dbeetypechk").html(" : "+showtype)  ;
							/*
							$('.closeCondition').on('click', function(){
									aa	=	$(this).attr('id');			
									$('.chk_'+aa).remove();
									$('.txt_'+aa).remove();
									haselemchk			=	$(".addedcondition").children(".conditiontext");
									if(haselemchk.length<1){ 
										$("#dbeetype").closest('.select').show();
										$("#dbeetypechk, .addedcondition, .searchbutton, .savefilter").hide(); 		
									}								
							});	*/
						
						} else
						{
							$messageError('Please choose your preference first');
							return false;
						}
						break;	
				case 'date' :

						i++;
						var chkbox	=	 $("input:radio[name=chkdate]:checked")  ;
						var chkdval	=	 chkbox.val()  ;
						var textval	=	 $("#date_text").val()  ;
						var chkdid	=	 $("#datefield").val()  ;
						var condi	=	 $("input:radio[name=chkdate]:checked").attr('id')  ;
		
						if((chkdid!='' && chkbox.length>0 )   && textval!='' )
						{
							$('.addedcondition').show();
							$('.addedcondition').append('<div class="whiteBox conditiontext chk_'+i+'" > search  '+ showtype +' Where '+chkdval+' <strong>'+ textval +'</strong><a href="#" id="'+i+'" class="closeCondition"></a></div>');
							
							$('.addedcondition').append('<span class="conditiontext txt_'+i+'" style="display:none" ><input type="hidden" name="datearr[]" id="search_on_'+i+'" value="'+dbeetype+'"><input type="hidden" name="datearr[]" id="search_for_'+i+'" value="'+chkdid+'"><input type="hidden" name="datearr[]" id="search_condition_'+i+'" value="'+condi+'"><input type="hidden" name="datearr[]" id="search_value_'+i+'" value="'+textval+'"></span>');
							
							$("#dbeetype").closest('.select').hide()  ;
							$("#dbeetypechk").show();
							$("#dbeetypechk").html(" : "+showtype)  ;

							/*
							$('.closeCondition').on('click',function(){
									aa	=	$(this).attr('id');			
									$('.chk_'+aa).remove();
									$('.txt_'+aa).remove();
									haselemchk			=	$(".addedcondition").children(".conditiontext");
									if(haselemchk.length<1){ 
										$("#dbeetype").closest('.select').show();
										$("#dbeetypechk, .addedcondition, .searchbutton, .savefilter").hide();
									 } 
							});	*/
						} else
						{
							$messageError('Please choose your search criteria ');
							return false;
						}
						break;		
				default :
						$messageError('plz select condition');
						break;	
							
			}
			
			//**** Adding search button ****
			haselemchk			=	$(".addedcondition").children(".conditiontext");
			
			if(haselemchk.length>0){ $('.searchbutton').show(); $('.savefilter').show();	} 
	});


$('body').on('click','.closeCondition',function(){
		aa	=	$(this).attr('id');			
		$('.chk_'+aa).remove();
		$('.txt_'+aa).remove();
		haselemchk = $(".addedcondition").children(".conditiontext");
		if(haselemchk.length<1){ 
			$("#dbeetype").closest('.select').show();
			$("#dbeetypechk, .addedcondition, .searchbutton, .savefilter").hide();
		 }	
		  var totalRow = $('.popupContent .conditionRow').size();
		  var loadFilter = $('#loadSelectedFilter').html();
		  if(totalRow==1 && loadFilter==''){
		  		   $('.popupContent h2.notfound').fadeIn('fast');
                    $('#saveSearchCondition, #clearSearchFilters').fadeOut('fast');           
		  }

});	
	
	$('#gosearch').on('click',function(){
										  
			
			//var fields = $(":input").serializeArray();+
			 $('html, body').animate({ scrollTop: $("#box_body").offset().top }, 500);
			var BASE_URL 	= $("#BASE_URL").val();	
			var fields 		= $("#search_form").serialize();

			
			//console.log(fields);
			
			data	=	fields ;
			url		=	BASE_URL+"/admin/dashboard/generatesearch";
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
					   removejscssfile(BASE_URL+'js/userfunctions.js', "js")
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
					 loadHeadScript(BASE_URL+'js/userfunctions.js');
					  
				  }
			});	
			function loadHeadScript(url) {
				//alert(url);
			    var script = document.createElement("script");
		        script.setAttribute("type", "text/javascript");
		        script.setAttribute("src", url);
		        var head = document.getElementsByTagName("head")[0];
		        head.appendChild(script);
		    }
		    function removejscssfile(filename, filetype){
			 	var targetelement=(filetype=="js")? "script" : (filetype=="css")? "link" : "none" //determine element type to create nodelist from
			 	var targetattr=(filetype=="js")? "src" : (filetype=="css")? "href" : "none" //determine corresponding attribute to test for
			 	var allsuspects=document.getElementsByTagName(targetelement)
			 	for (var i=allsuspects.length; i>=0; i--)
			 	{ //search backwards within nodelist for matching elements to remove
			 	 	if (allsuspects[i] && allsuspects[i].getAttribute(targetattr)!=null && allsuspects[i].getAttribute(targetattr).indexOf(filename)!=-1)
			  		 allsuspects[i].parentNode.removeChild(allsuspects[i]) //remove element by calling parentNode.removeChild()
			 	}
			}  						
	});
	
	// Added functionality for save filters functionality
	// Auth @ Deepak Nagar 
	// Added @ 18 Apr 2013
	$('#gosave').on('click',function(){
	
		$("#save_filterMsg").hide();
		$("#filtname").val('');
		$('#save_filterName').toggle();
		$('#saveFtWrapper').toggleClass('openWaveFilter');

		
	});
	
	$('#gosavefilter').on('click',function(){
			var filtName 	= $("#filtname").val();	
			$(this).closest('#saveFtWrapper').removeClass('openWaveFilter');
			if(filtName=='')
			{
				$("#save_filterName").effect( 'bounce', '', 500 );
				return false;
      		}
			var BASE_URL 	= $("#BASE_URL").val();	
			var fields 		= $("#search_form").serialize();
			
			data	=	fields ;
			
			url		=	BASE_URL+"/admin/dashboard/savefilter";
			$.ajax({                                      
				  url: url,                  //the script to call to get data          
				  data: data,                        //you can insert url argumnets here to pass to api.php
				  method: 'POST',
				  //dataType: 'json',                //data format    
				  beforeSend: function(){
					  $("#save_filterName").hide();
					   $("#save_filterMsg").show();
					  $('#save_filterMsg').html('<img src="'+BASE_URL+'img/loader3.gif" >');
				  },
				  success: function(data)          //on recieve of reply 
				  {
     				if(data=='1')
					{
						$('#save_filterMsg').html('Please use correct combination');
					}
					else
					{
						$('#save_filterMsg').addClass('message success').html('Filter saved successfully');
						$('#loadFilter').html(data);
						$select('#loadFilter');

					}
				  }
			});							
	});
	
	// Show Next Records functionality  functionality
	// Auth @ Deepak Nagar 
	// Added @ 16 Apr 2013
	$('#showmore').on('click',function(){					   
			var last 	= 	$('#listingResults').children('li:visible:last');
   			var chk		=	last.nextAll(':lt(5)').show();
			var allvis	= 	$('#listingResults').children('li:visible');
			var tot 	=	$("#listingResults li").length;
			if(tot	==	allvis.length)
			{
				$("#showmore").hide();
			}
	});


	$('#loadFilter').on('change',function(){
		

		var filter_id	=	$(this).val();		
		var BASE_URL 	= $("#BASE_URL").val();			
			data	=	'filter_id= '+filter_id;	
			url		=	BASE_URL+"/admin/dashboard/loadfilterattr";

		$.ajax({                                      
				url: url,                  //the script to call to get data          
				data: data,                        //you can insert url argumnets here to pass to api.php
				method: 'POST',
				//dataType: 'json',                //data format    
				beforeSend: function(){
				   $("#save_filterName").hide();
				   $("#save_filterMsg").show();
				   $('#save_filterMsg').removeClass('message success');
				   $('#save_filterMsg').html('<img src="'+BASE_URL+'img/loader3.gif" >');
				},
				success: function(data)          //on recieve of reply 
				{
					$('.addedcondition').show();
					$('.addedcondition').html(data);
					$('.expenddcondition').hide('slow');
					haselemchk			=	$(".addedcondition").children(".conditiontext");
					$("#save_filterMsg").hide();
					$('.savefilter').hide(); 
					
					if(haselemchk.length>0)
					{ 
						$('.searchbutton').show();
						$('.nocondition').hide();					
						$('.addedcondition').html(data);	
						
					} 
					else  
					{ 
						$('.searchbutton').hide(); 
						//$('.addedcondition').html('<p class="nocondition"> Filter is empty, Please add conditions</p>');


					}
					
					

				}
			});
	});
});
