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

				 if(param[2]=='yes') { $('.totalrec').hide();} 
				 else  { $('.totalrec').show();}      
				 
				 $('#grpRightListinguL').html(param[0]);
					thisEl.closest('ul').find('.active').removeClass('active');
					thisEl.addClass('active');
				
				//$('#grpRightListing .cateName span').html(groupname+param[1]);
				$('#grpRightListing .cateName span').html(groupname);
				
			
			}
		});
	});

$('.groupbox li:first').trigger('click');

$('body').on('click','.deletepostreport',function(e){	
            	e.stopPropagation();
            	chariId	 =	$(this).attr('id');

				var confirmD = '<div id="confirBox" title="Please confirm"><p>Are you sure you want to delete this report?<strong></strong></p></div>';
				$('#confirBox').remove();
				$('body').append(confirmD);
				$('#confirBox').dialog({ 
					minWidth:300,
					buttons: {
				        "Yes": function() {				        	
							var thisEl =$(this);						
							var grouprow = $("#single_"+chariId);							
				           $.ajax({
			                    type : "POST",
			                    dataType : 'json',
			                    url : BASE_URL + '/admin/savedcharts/deletesavedcharts',
			                    data:{'groupid':chariId,'calling':'savedreport'},   
			                    success : function(response) { 
			                    	 if(response==1)
			                    	 {
			                    	 	grouprow.remove(); 			                    	
				                        $messageSuccess("Report deleted successfully");  
				                        thisEl.dialog( "close" );
				                       // $('.groupbox li:first').trigger('click');
				                    }
				                    else $messageError('something went wrong, please try again');
			                    },
			                    error : function(error) {
			                    $messageError("Some problems have occured. Please try again later: "+ error);                      
			                    }			                    
			                   });
				        }
				      }
				  });
			});

$('body').on('click','.deletesinglechart',function(e){	
            	e.stopPropagation();
            	chariId	 =	$(this).attr('chartid');

				var confirmD = '<div id="confirBox" title="Please confirm"><p>Are you sure you want to delete this chart?<strong></strong></p></div>';
				$('#confirBox').remove();
				$('body').append(confirmD);


				$('#confirBox').dialog({ 
					minWidth:300,
					buttons: {
				        "Yes": function() {				        	
							var thisEl =$(this);						
							var grouprow = $("#single_"+chariId);							
				           $.ajax({
			                    type : "POST",
			                    dataType : 'json',
			                    url : BASE_URL + '/admin/savedcharts/deletesavedcharts',
			                    data:{'groupid':chariId,'calling':'singlechart'},   
			                    success : function(response) { 
			                    	 if(response==1)
			                    	 {
			                    	 	grouprow.remove(); 			                    	
				                        $messageSuccess("Report deleted successfully");  
				                        thisEl.dialog( "close" );
				                        menulen = $('#grpRightListinguL li').length;
										if(menulen==0) $('.totalrec').hide();
				                       // $('.groupbox li:first').trigger('click');
				                    }
				                    else $messageError('something went wrong, please try again');
			                    },
			                    error : function(error) {
			                    $messageError("Some problems have occured. Please try again later: "+ error);                      
			                    }			                    
			                   });
				        }
				      }
				  });
			});
 
 $('#container').on('mouseenter mouseleave','.kcSidebar li:not(.noCategory)', function(e){
	
    	var isthis = $(this);
        var folderOpt = '<div class="kcFolderOption">\
                                <a href="javascript:void(0);" class="plBtn plBtnGreen btn-mini editFolder kc_editgroup">\
                                    <i class="fa fa-pencil"> </i> \
                                </a>\
                                <a href="javascript:void(0);" class="plBtn plBtnRed btn-mini removeFolder deleteList">\
                                    <i class="fa fa-times"> </i>\
                                </a>\
                            </div>';
       
        if(e.type=="mouseenter"){
            $(this).find('.kcFolderOption').remove();
            $(this).append(folderOpt);
            $(this).find('.kcFolderOption').animate({right:'10px'});

            $(".kc_editgroup").click(function(e){

            	e.stopPropagation();         
            	var grpname = $(this).closest('li').text();           
            	var grpnameid = $(this).closest('li').attr('chartgroupid');


				fileID	 =	isthis.attr('chartgroupid');
				
				$("#digForm").show();
				$("#ks_groupname").val($.trim(grpname));
				$("#ks_check").val(grpnameid);		
				$( "#chartgroupDialog").dialog("open");		
						
				
				
			});
            $(".deleteList").click(function(e){            	
            	e.stopPropagation();
            	fileID	 =	isthis.attr('chartgroupid');
				
				var chartNaME = $(this).closest('li').attr('groupname');

				var confirmD = '<div id="confirBox" title="Please confirm"><p>Are you sure you want to delete <strong>'+chartNaME+'</strong>? <strong></strong></p></div>';
				$('#confirBox').remove();
				$('body').append(confirmD);
				$('#confirBox').dialog({ 
					minWidth:300,
					buttons: {
				        "Yes": function() {				        	
							var thisEl =$(this);						
							var grouprow = $("#id_"+fileID);							
				           $.ajax({
			                    type : "POST",
			                    dataType : 'json',
			                    url : BASE_URL + '/admin/savedcharts/deletesavedcharts',
			                    data:{'groupid':fileID},   
			                    success : function(response) { 
			                    	 if(response==1)
			                    	 {
			                    	 	grouprow.remove(); 			                    	
				                        $messageSuccess("Deleted successfully");  
				                        thisEl.dialog( "close" );
				                        var remainChart = $('.groupbox li:first').size();
            							if(remainChart>0)
            							{
            								$('.groupbox li:first').trigger('click');	
            							}
            							else
            							{
            								$('.groupbox').append('<div class="notfound">no charts saved yet</div>');
            								$('#grpRightListinguL').html('');
            								$('.cateName').html('<i class="fa fa-folder-open fa-2x kcLargFolderGrp"></i>');
            								$('.totalrec').hide();
            							}
				                    }
				                    else $messageError('something went wrong, please try again');
			                    },
			                    error : function(error) {
			                    $messageError("Some problems have occured. Please try again later: "+ error);                      
			                    }			                    
			                   });
				        }
				      }
				  });
			});

			
           
        }
         else if(e.type=="mouseleave"){
                $(this).find('.kcFolderOption').animate({right:'-200px'},{
                    duration:500,
                    complete:function(){
                     $(this).remove();
                    }
                });
        }
    });

$( "#chartgroupDialog" ).dialog({
	    autoOpen: false,	    
	    height: 230,
	    width: 300,
	    modal: true,
	     buttons: {

   			"Update": function() 
   			{
   				var isthisPop = $(this);
   				var grpname = $.trim($('#ks_groupname').val());
				if (/[^a-zA-Z 0-9-]+/.test(grpname)){
				   $messageError('Group name can only contain alphabets, numbers and dash character');
				   return false;
				}
				var grpid = $('#ks_check').val();
				if(grpname==''){
					$messageError('please enter a group name');
					return false;		
				}
				secchk=  $("#ks_check").val();		
	            dataM	=	"groupname="+ grpname +"&groupid="+grpid;	          
				url		=	BASE_URL+"/admin/savedcharts/updatechartsgroup";
				$.ajax({                                      
					  url: url,                  //the script to call to get data          
					  data: dataM,                        //you can insert url argumnets here to pass to api.php
					  type: 'POST',
					 // dataType: 'json',                //data format  
					  beforeSend: function(){
						   $("#beforecall").show();
				 		},  
						success: function(data)          //on recieve of reply
					  	{
							$("#beforecall").hide();
							if(data==1){ $messageSuccess("Updated successfully"); isthisPop.dialog( "close" );} 
							//else if(data=='403') $messageError('this chart name is saved already, please try different');
							$('#id_'+grpid).text(grpname);
						}
				});
					
      	
   		 }
   		},
		close: function() { 
			$( this ).dialog( "close" );
		}
});
	


	
});
