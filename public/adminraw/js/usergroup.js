$(function(){
if(jsonuserdetails!=''){
	var userDtailsJson = JSON.parse(jsonuserdetails);
}else{
	var userDtailsJson = '';
}

var groupname = $( "#groupname" ),
	groupnameid = $( ".dgrpUrsName" ).attr('usergroupdetailid'),
	 // BASE_URL = $("#BASE_URL").val(),
     
      allFields = $( [] ).add( groupname ).add( groupnameid ),
      tips = $( ".validateTips" );

	$('body').on('click','#grpUser li',function(e){
		e.stopPropagation();
		 var groupId = $(this).attr('usergroupdetailid');
		 var groupName = $(this).text();
		 var url = BASE_URL+'/admin/usergroup/usergroupdetail';
		 var data1 = "total=true"+'&id='+groupId;
		 var thisEl = $(this);
		$.ajax({
			url: url,
			type: "POST",
			data:data1,
			success:function(response){				
				 //var param=data.split("~");          
				 $('#grpRightListinguL').html(response.content);
				thisEl.closest('ul').find('.active').removeClass('active');
				thisEl.addClass('active');
				$('#grpRightListing .cateName span').html(response.groudetailtotal);
				$('#grpRightListing .cateName span').prepend(groupName);
				$('#grpRightListing .totalrec').html(response.addGroupUsers);
			}
		});
		
		
	});
	$('#grpUser li:first').click();

 /*this script for sidebar actions*/

    $('body').on('click', '.kcRightPanel #grpRightListinguL li:not(.noCategory) a', function(e){
    	
    	var groupid = $(this).attr('gname');
    	var userid	 = 	$(this).attr('userid');
    	var atype	=	$(this).attr('atype');

        /*var fileopt = '<div class="kcRightAction">\
                                    <a href="javascript:void(0);" class="kcSprite kcViewPdf show_details_user" userid="'+userid+'"></a>\
                                    <a href="javascript:void(0);" class="kcSprite kcPdfDelete"></a>\
                                </div>';*/
        e.stopPropagation()
       // if(e.type=="mouseenter"){
            //$(this).find('.kcRightAction').remove();            
        	//$(this).append(fileopt);
            //$(this).find('.kcRightAction').animate({right:'10px'});  
           
           
         // $( "#grpRightListinguL .kcPdfDelete").bind('click', function(){  
         	if(atype=='delete'){
          		var userNaME = $(this).closest('li').find('.dgrpUrsName').text(); 
          		var thisrow =  $(this).closest('li');      
				var confirmD = '<div id="confirBox" title="Please confirm"><p>Are you sure you want to delete <strong>'+userNaME+'?</strong></p></div>';
				$('#confirBox').remove();
				$('body').append(confirmD);
				$('#confirBox').dialog({ 
					minWidth:300,
					buttons: {
				        "Yes": function() {				        	
							var thisEl = $(this);
							var userrow = $("userslist_"+ userid);
							
				           $.ajax({
			                    type : "POST",
			                    dataType : 'json',
			                    url : BASE_URL + '/admin/usergroup/deleteuser',
			                    data:{'groupid':groupid,'userid':userid},   
			                    success : function(response) {    
			                    	thisrow.fadeOut(function(){
			                    		$(this).remove(); 
			                    		var totalUser = $('.kcrightheaderTop strong b').attr('total');
			                    		if(totalUser==1){
			                    		var usrtext = ' user';
			                    	}
			                    		else{
			                    			var usrtext = ' users';
			                    		}
			                    		
			                    		 $('.kcRightPanel .kcrightheaderTop strong b').text(totalUser-1+ usrtext);
			                    	});
			                       $messageSuccess("user removed successfully");  
			                       thisEl.dialog( "close" );
			                    },
			                    error : function(error) {
			                    $messageError("Some problems have occured. Please try again later: "+ error);
			                       
			                    }
			                    
			                   });


				        }
				      }
				  });
			//});
		}
        //}
        /*else if(e.type=="mouseleave"){
                $(this).find('.kcRightAction').animate({right:'-200px'},{
                    duration:500,
                    complete:function(){
                     $(this).remove();
                    }
                });
        }*/
    });


  /*$('#container').on('mouseenter mouseleave','.kcSidebar li:not(.noCategory)', function(e){
	
    	var isthis = $(this);
        var folderOpt = '<div class="kcFolderOption">\
                                <a href="javascript:void(0);" class="editFolder kc_editgroup">\
                                    <i class="fa fa-pencil"> </i> \
                                </a>\
                                <a href="javascript:void(0);" class="removeFolder deleteList">\
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
            	var grpnameid = $(this).closest('li').attr('usergroupdetailid');
				fileID	 =	isthis.attr('usergroupdetailid');
				
				$("#digForm").show();
				$("#ks_groupname").val($.trim(grpname));
				$("#ks_check").val(grpnameid);				
				$( "#usergroupDialog" ).dialog("open");			
				
				
			});
            $(".deleteList").click(function(e){            	
            	e.stopPropagation();
            	fileID	 =	isthis.attr('usergroupdetailid');
				//$( "#dialog_confirm_group_delete" ).dialog( "open" );
				var userNaME = $(this).closest('li.dgrpUrsName').text();
				var confirmD = '<div id="confirBox" title="Please confirm"><p>Are you sure you want to delete <strong>'+userNaME+'</strong> user set?</p></div>';
				$('#confirBox').remove();
				$('body').append(confirmD);
				$('#confirBox').dialog({ 
					minWidth:300,
					buttons: {
				        "Yes": function() {				        	
							var thisEl =$(this);						
							var grouprow = $("#grpid_"+ fileID);							
				           $.ajax({
			                    type : "POST",
			                    dataType : 'json',
			                    url : BASE_URL + '/admin/usergroup/deletegroup',
			                    data:{'groupid':fileID},   
			                    success : function(response) {   			                    	
			                    	grouprow.remove(); 		
			                    	$('#grpUser li:first').click();	                    	
			                       $messageSuccess("Set deleted successfully");  
			                       thisEl.dialog( "close" );
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
    });*/

 $("body").on('click','.kc_editgroup', function(e){            	
            	e.stopPropagation(); 
            	var isthis = $(this).closest('li');
            	var grpname = isthis.text();           
            	var grpnameid = isthis.attr('usergroupdetailid');
				fileID	 =	isthis.attr('usergroupdetailid');
				
				$("#digForm").show();
				$("#ks_groupname").val($.trim(grpname));
				$("#ks_check").val(grpnameid);				
				$( "#usergroupDialog" ).dialog("open");		
			});
  $("body").on('click','.deleteList', function(e){   
            	e.stopPropagation();
            	
            	var isthis = $(this).closest('li');
            	fileID	 =	isthis.attr('usergroupdetailid');
				//$( "#dialog_confirm_group_delete" ).dialog( "open" );
				var userNaME = $(this).closest('li.dgrpUrsName').text();
				var confirmD = '<div id="confirBox" title="Please confirm"><p>Are you sure you want to delete <strong>'+userNaME+'</strong> user set?</p></div>';
				$('#confirBox').remove();
				$('body').append(confirmD);
				$('#confirBox').dialog({ 
					minWidth:300,
					buttons: {
				        "Yes": function() {				        	
							var thisEl =$(this);						
							var grouprow = $("#grpid_"+ fileID);							
				           $.ajax({
			                    type : "POST",
			                    dataType : 'json',
			                    url : BASE_URL + '/admin/usergroup/deletegroup',
			                    data:{'groupid':fileID},   
			                    success : function(response) {   			                    	
			                    	grouprow.remove(); 		
			                    	$('#grpUser li:first').click();	                    	
			                       $messageSuccess("Set deleted successfully");  
			                       thisEl.dialog( "close" );
			                    },
			                    error : function(error) {
			                    $messageError("Some problems have occured. Please try again later: "+ error);                      
			                    }			                    
			                   });
				        }
				      }
				  });
			});

  $('body').on('click','#viewmoregroupuser',function()
		  {			
		      var thisEl  = $(this);		  
		      data    =   "id="+ $(this).attr('groupid' )+"&offset="+ $(this).attr('offset');		    
		      var action = $(this).attr('action');		              
		      url     =   BASE_URL+"/admin/"+action;		      
		      $.ajax({                                      
		        url: url,                  //the script to call to get data          
		        data: data,                        //you can insert url argumnets here to pass to api.php
		        type:'post',		    
		       beforeSend:function(){     	       
		           $loader();
		       },   //data format    
		       success: function(data)  //on recieve of reply
		       {   
		          $removeLoader();
		         
		          thisEl.closest('a').parent('div').remove();
		          $('#grpRightListinguL').append(data);	
		       }
		      });
		
		});
  
	$( "#usergroupDialog" ).dialog({
	    autoOpen: false,	    
	    height: 230,
	    width: 300,
	    modal: true,
	     buttons: {

   		"Go": function() {
			if (/[^a-zA-Z 0-9]+/.test(groupname.val())){
			   updateTips('Please use valid key words');
			   return false;
			}
			var grpname = $.trim($('#ks_groupname').val());
			var grpnameid = $('#ks_check').val();
			if(grpname==''){
				$messageError('please enter a set name');
				return false;		
			}
		
			secchk=  $("#ks_check").val();		
          	
	            data	=	"groupname="+ grpname +"&groupid="+grpnameid;	          
				url		=	BASE_URL+"/admin/usergroup/updategroups";
				$.ajax({                                      
				  url: url,                  //the script to call to get data          
				  data: data,                        //you can insert url argumnets here to pass to api.php
				  type: 'POST',
				 // dataType: 'json',                //data format  
				  beforeSend: function(){
				   $("#beforecall").show();
				   $("#digForm").hide();
				   //$("#beforecall").html('<span class="invitemsgbox"><img src="'+BASE_URL+'/images/loader2.gif" ></span>');
				  // removejscssfile(BASE_URL+'js/userfunctions.js', "js")
			 	 },  
				 success: function(data)          //on recieve of reply
				  {
					
					setTimeout(function(){ 
						 $messageSuccess("set updated successfully");  
						 $("#beforecall").hide()
		 	 			$("#usergroupDialog" ).dialog( "close" );	 }, 1000);
						$('#grpid_'+grpnameid).html(grpname+'<div class="kcFolderOption">\
                                <a href="javascript:void(0);" class="editFolder kc_editgroup">\
                                    <i class="fa fa-pencil"> </i> \
                                </a>\
                                <a href="javascript:void(0);" class="removeFolder deleteList">\
                                    <i class="fa fa-times"> </i>\
                                </a>\
                            </div>');
					
					//$('.kcrightheaderTop .cateName span').text(grpname);

					
					}
				});
      	
   		 },
   		 "Cancel": function() {
   		 	 allFields.val( "" ).removeClass( "error" );
   		 	 $( this ).dialog( "close" );	
   		 }
   		},
		close: function() { 
			 allFields.val( "" ).removeClass( "error" );
			$( this ).dialog( "close" );
		}
});
	
	
	$('body').on('click','.kcSidebar h2 a',function(e){   
		e.preventDefault();
		e.stopPropagation();
		
		var DbeeID = $(this).attr('dbid');
		var type = $(this).attr('type');

		$('#groupaddnew').remove();
		//var dbid = $('#dbid').val();
		 	var htmlLightbox = '<div id="groupaddnew"  title="Add new user set">\
                                <div id="datacollect" style="float:none"></div>\
                                <div id="groupaddnew"></div>';                           

		 	var content = '<div class="formRow">\
		 		<label class="label"> Name</label>\
		 		<input type="text"  name="filterName" id="gname"  class="fluid"   value="">\
			</div><input type="hidden"  name="ugcat" id="ugcat" value="">\
			<div class="formRow">\
				<label class="label">Description</label>\
				 <textarea id="grpDescription"  class="fluid" value=""></textarea>\
			</div>\
		 		';
	        $('body').append(htmlLightbox);				             
						$( "#groupaddnew" ).dialog({
					                dialogClass:'groupaddnew',             
					                width:300,
					                height:300,             
					                open:function(event, ui){  					                	
					                	$('html').attr('dbid', $(document).scrollTop()).css('overflow', 'hidden');
					                    $(this).dialog('option','position',{ my: 'center', at: 'center', of: window });			                	  
					                    $fluidDialog();
					                     $("#datacollect").html('');      
					                     $('.ui-dialog-content').append('<div class="loaderOverlay2"></div>'); 
					                     $('#groupaddnew').html(content);
					                     $('.loaderOverlay2').remove();
					                    
					                },
					                buttons: {							       
					                	 'Create set' : function() {
					                		 var ugcat = $('#ugcat').val();
					                		 var agroup = $( this );
					                		 var groupname = $('#gname').val();
					                	        var groupdiscription = $('#grpDescription').val();					                	      				                	         
					                	            if(groupname=='') {
					                	                 $messageError("please enter a set name");
					                	                 return false;
					                	            }            
					                	             if(groupdiscription==''){
					                	                 $messageError("please enter set description");
					                	                     return false;
					                	            }    
					                	             var snload = $('.kcSidebar li:not(.noCategory)');
					                	                $.ajax({
					                	                    type : "POST",
					                	                    dataType : 'json',
					                	                    url : BASE_URL+'/admin/usergroup/addgroup',
					                	                    data:{groupname:groupname,discription:groupdiscription,ugcat:ugcat},   
					                	                    success : function(response) {                                      
					                	                       $messageSuccess("Set added successfully"); 	
					                	                       $('.groupbox li').removeClass('active');
					                	                        $('.groupbox').prepend('<li id="grpid_'+response.content+'" class="dgrpUrsName active" usergroupdetailid="'+response.content+'">'+groupname+'</li>');
					                	                        $('.kcrightheaderTop .cateName span').text(groupname);
 
					                	                        agroup.dialog( "close" );
					                	                       location.reload(true);
					                	                    }
					                	                    });					                	            
					                	                
						 			        },
						 			        'Cancel': function() {
						 			          $( this ).dialog( "close" );
						 			        }
								      },
								      close : function(event, ui) { 
								          var scrollTop = $('html').css('overflow', 'auto').attr('data-scrollTop') || 0;
								          if( scrollTop ) $('html').scrollTop( scrollTop ).attr('data-scrollTop','');
								      }
				        		});
				
			    
	});
	
	
	
	$('body').on('click','.linkuseraddgrp a', function(){
		
		var DbeeID = '';
		var gaobj = $(this);
		$('#detailsDialog').remove();
	 	var htmlLightbox = '<div id="detailsDialog" class="srhUserbox"  title="Platform users">\
	 							<div class="srcUsrWrapper srhUserInpfld">\
	 								<div class="sprite searchIcon2"></div>\
										<input type="text" id="userlistgp" class="userFatchList"  >\
									<div class="srcUsrtotal" style="display:none;">40 <i>total</i></div>\
								</div>\
								<span class="orSpan">Or</span>\
								<div class="srhUserInpfld"><input type="hidden" id="selectCompany"></div>\
								<span class="orSpan">Or</span>\
                                <div class="srhUserInpfld"><input type="hidden" id="selecttitle"></div>\
                                <div id="datacollect" style="float:none"></div>\
                                <div id="userInfoContainer"><div class="dashBlockEmpty" style="width:95%;"></div>\
							</div>';

        $('body').append(htmlLightbox);
       
       		$("#selectCompany").select2({
                width: '100%',
                padding: '10px',
                placeholder: "Search Company",
                allowClear: true,
                data: userDtailsJson.company
            }).change(function(){ 
            	$("#selecttitle").select2("val", "");
            	$usersetadd($("#selectCompany").val(),'company');
            	$('#detailsDialog').scrollTop($('#detailsDialog').scrollTop()-200);          	
            });
            $("#selecttitle").select2({
                width: '100%',
                placeholder: "Search job title",
                allowClear: true,
                data: userDtailsJson.title
            }).change(function(){ 
            	$("#selectCompany").select2("val", "");
            	$usersetadd($("#selecttitle").val(),'title');
            	$('#detailsDialog').scrollTop($('#detailsDialog').scrollTop()-200);     
            });
            
        $( "#detailsDialog" ).dialog({
            dialogClass:'detailsDialogBox',             
            width:700,
            height:400,  
            title:'Search user ',
            open:function(){                
                $fluidDialog();
                 $("#datacollect").html('');      
              
            },
            buttons: {
		        "Save": function() {
					thisEl = $(this);
					var groupid = gaobj.attr('groupid');
					
					var userInfo = [];
					if($('input:checkbox[name=groupuser]').is(':checked')==false){
						$messageError('Please select a user');
						return false;
					}
					$('input:checkbox[name=groupuser]').each(function() 
					{    
						if($(this).is(':checked'))
						userInfo.push($(this).val());
					});
					
					var stringuserInfo = userInfo.join();

					$.ajax({
						type : "POST",
						dataType : 'json',
						data:{'groupid':groupid,'userid':stringuserInfo},
						url : BASE_URL + '/admin/usergroup/addgroupusergrp',
						timeout : 3000,
						beforeSend : function() {
						},
						complete : function() {
						},
						cache : false,
						success : function(response) 
						{ 	 
							$('#detailsDialog').remove();
							$('#grpUser li.active').trigger('click');
							$messageSuccess('Added successfully');
						},
						error : function(error) {

						}
					});
		        }
		      }
		});

		 

		
	});
	$('body').on('click','.selectallset input:checkbox',function(){		        
	    
	        var elto = $('input:checkbox[name=groupuser]');

				if($(this).is(':checked')==true){
					elto.attr('checked', true);										
				}else{
					elto.attr('checked', false);
				}
					
	}); 
	
	$usersetadd = function(keyword,typen){    
	        		
	  var datacc ={'keyword':keyword,'type':typen};	            
	       $.ajax({
	          url:BASE_URL+"/admin/usergroup/dbeeuser",
	          data:datacc,
	          dataType : 'json',			
	          type :"POST",
	          beforeSend:function(){
	      		$('#userInfoContainer').append('<div class="loaderOverlay2"></div>');	         
	          },
	          success:function(responce){
	          	$('#userlistgp').val('');
	        	 $('.loaderOverlay2').remove();	           
	             $('#userInfoContainer').html(responce.content);
	             var myval = '';
                 $('#userInfoContainer .inviteuser-search').each(function(){
                    myval = $(this).attr('checkvalue'); 
                    if(myval==1){
                       $('input[checkvalue="1"]').attr('type','hidden').closest('.labelCheckbox').css({cursor:"default"}).attr('title','invitation already send');
                     }                    
                 });             
	             $('#userInfoContainer').flexslider({
	                      animation: "slide",
	                      animationLoop: false,
	                      itemWidth:150,
	                      itemMargin: 5
	                  });
	             
	           
	          }
	       });

		
	       $(this).removeClass('error');
	       return false;  	      
	    
   }
		
	$('body').on('keypress','#userlistgp',function(e){   
	$("#selectCompany").select2("val", "");	
	$("#selecttitle").select2("val", "");
	  keyword = $.trim($(this).val());

	    if (e.which==13){
	       if(keyword.length>=2){	    		
	         data ={'keyword':keyword,'type':'admingroup'};	            
	       $.ajax({
	          url:BASE_URL+"/admin/usergroup/dbeeuser",
	          data:data,
	          dataType : 'json',			
	          type :"POST",
	          beforeSend:function(){
	      		$('.ui-dialog-content').append('<div class="loaderOverlay2"></div>');	         
	          },
	          success:function(responce){
	          	
	          	//return false;
	        	 $('.loaderOverlay2').remove();	           
	             $('#userInfoContainer').html(responce.content);
	             var myval = '';
                 $('#userInfoContainer .inviteuser-search').each(function(){
                    myval = $(this).attr('checkvalue'); 
                    if(myval==1){
                       $('input[checkvalue="1"]').attr('type','hidden').closest('.labelCheckbox').css({cursor:"default"}).attr('title','invitation already send');
                     }                    
                 });             
	             $('#userInfoContainer').flexslider({
	                      animation: "slide",
	                      animationLoop: false,
	                      itemWidth:150,
	                      itemMargin: 5
	                  });
	             
	           
	          }
	       });	    

	       $(this).removeClass('error');
	       return false;   

	       }else{
	          $messageError('Please type minimum 2 characters')
	        }
	    }
	  
	  
	 });


	
	$('body').on('click', '.removeUser', function(event) {
        event.preventDefault();
        $('#dialogConfirmSetting').remove();
        var UserID = $(this).attr('data-id');
        var type = $(this).attr('data-type');
        var thisEl = $(this);
        
        if(type=='twitter')
        	var msg = 'Are you sure you want to stop this user from posting with Twitter #tags?';
        else if(type=='promoted')
        	var msg = 'Are you sure you want to stop this user from promoted expert?';

        $('body').append('<div id="dialogConfirmSetting">' + msg + '</div>');
        $("#dialogConfirmSetting").dialog({
            resizable: false,
            title: 'Please confirm',
            width: 400,
            modal: true,
            buttons: {
                "Yes": function() {
                    $(this).dialog("close");
                    $.ajax({
                        type: "POST",
                        dataType: 'json',
                        data: {
                            'stringuserInfo': UserID,'fortype':type,'status':0
                        },
                        url: BASE_URL + '/admin/myaccount/connectplateformuser',
                        success: function(response) 
                        {
                            $messageSuccess(response.message);
                            userlist(type);
                            thisEl.dialog("close");
                        }
                    });
                }
            }
        });
    })

	$('body').on('keypress','#userlisttwittertag',function(e){   	
	  	keyword = $.trim($(this).val());
	  	type = $.trim($(this).attr('data-type'));

	    if (e.which==13)
	    {
	       if(keyword.length>=2)
	       {	    		
	           data ={'keyword':keyword,'type':type};	            
		       $.ajax({
		          url:BASE_URL+"/admin/usergroup/dbeeuser",
		          data:data,
		          dataType : 'json',			
		          type :"POST",
		          beforeSend:function(){
		      		$('.ui-dialog-content').append('<div class="loaderOverlay2"></div>');   
		          },
		          success:function(responce)
		          {
		        	 $('.loaderOverlay2').remove();	           
		             $('#userInfoContainer').html(responce.content);
		             $('#userInfoContainer').append('<input type="hidden" name="fortype" value="'+type+'" id= "fortype" />');

		             var myval = '';
	                 $('#userInfoContainer .inviteuser-search').each(function(){
	                    myval = $(this).attr('checkvalue'); 
	                    if(myval==1){
	                       $('input[checkvalue="1"]').attr('type','hidden').closest('.labelCheckbox').css({cursor:"default"}).attr('title','invitation already send');
	                     }                    
	                 });             
					$('#userInfoContainer').flexslider({
					  animation: "slide",
					  animationLoop: false,
					  itemWidth:150,
					  itemMargin: 5
					});
					userlist(type);
		          }
		       });	    
		       $(this).removeClass('error');
		       return false;   
	       }else{
	          $messageError('Please type minimum 2 characters')
	        }
	    }
	  
	 });

});


function filtergroupuser()
{
    var sorting = $("input[socialFriendlist='true']").val();
	var id = $("input[socialFriendlist='true']").attr('id');
	$('input.checkAllUser').attr('checked', false);
	var count = 0;
	$("."+id).each(function(index) {
			
		if($(this).attr('title'))
		{
			 if($(this).attr('title').match(new RegExp(sorting, "i"))){
				$(this).show("slow"); 
				count++;
			 }
			 else{
					$(this).hide("fast");
				}
		}
	});
	$('.srcUsrtotal').show();
	$(".srcUsrtotal").html(count+' <i>total</i>');
 }


 function userlist(type)
 {
 	data = {'type':type}
 	$.ajax({
      url:BASE_URL+"/admin/user/userlist",
      dataType : 'json',
      data:data,			
      type :"POST",
      success:function(response)
      {
    	$('.userlist').html(response.html);
      }
   });
 }
