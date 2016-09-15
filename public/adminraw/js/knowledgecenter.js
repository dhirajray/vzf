var myDropzoneshareFile;
var iconexe = {'docx':'word','xlsx':'excel','xls':'excel','doc':'word','ppt':'powerpoint','txt':'text','jpeg':'image','jpg':'image','gif':'image','png':'image','WAVE':'audio','mp3':'audio','Ogg':'audio','mp4':'video','WebM':'video','wmv':'video','ogv':'video','pdf':'pdf'} ;  
var kclist = function(){

	$.ajax({
             type : "POST",
             dataType : 'json',
             data:{'bannerid':'id'},
             url : BASE_URL+'/admin/knowledgecenter/catlist',  
             beforeSend: function(){                        
                 /* var htmlLoader = '<div class="loaderBoxOverlay"></div>';
                  var parentEl =  $('#showdbloding'); 
                  $('.ksfolderlist ul').html(htmlLoader);*/
                },  
             success : function(response) {               
            var dnc = response.content;          
			var content1 ='';
			var classf = "";
			var title = "";		
			var fcnt = "";
			var reg = '';
			if (dnc.length != 0) {				
	$.each(dnc, function(i, value){	
		fcnt = 0;
		reg = /_/g
		title = value.kc_cat_title.replace(reg, ' ');	
		fcnt = title+'<span class="txthint"> ('+value.cnt+')</span>';
		content1 += '<li class="expandFiles'+classf+'" title="'+title+'" kclistRec="kclistRec_'+value.kc_id+'" id="'+value.kc_id+'" pretitle="'+title+'"><span>'+title+fcnt+'</span><div class="kcFolderOption">\
	                                <a href="javascript:void(0);" class="btn-mini editFolder kc_addcategory">\
	                                     <i class="fa fa-pencil"> </i> \
	                                </a>\
	                                <a href="javascript:void(0);" class="btn-mini removeFolder deleteList">\
	                                     <i class="fa fa-times"> </i>\
	                                </a>\
	                            </div></li>	';
	});
			}else{
				content1 = '<li class="noCategory">No category added</li>';
			}
	$('.ksfolderlist ul').html(content1);
	$('.ksfolderlist li').eq(0).addClass('active');
	$('.ksfolderlist li').eq(0).trigger('click');

	 
	 $(".kc_addcategory").click(function(e){
	 	var isthis = $(this).closest('li'); 
            	e.stopPropagation();
				fileID	 =	isthis.attr('id');
				fileTitle=	isthis.attr('pretitle');
				prevtitle=  isthis.attr('pretitle'); 
				var popV = $("#knowledgecentarDialog").closest('.ui-dialog'); 
				$("#digForm").show();
				$("#ks_catname").val(fileTitle);
				$("#ks_check").val('');
				$("#knowledgecentarDialog").dialog( "open" );
				$('.ui-dialog-title', popV).text('Edit category');
				$('.ui-dialog-buttonset .ui-button-text', popV).text('Edit');
			});
		  $(".deleteList").click(function(e){  		  	
		  		var el = $(this).closest('li');    
            	e.stopPropagation();
				fileID	 =	el.attr('id');
				$( "#dialog_confirm" ).dialog( "open" );
				$('.ui-widget button').css('display','block'); 
			});
		}
	});

}


var kcfilelist = function(option,esl){

	$.ajax({
	          type : "POST",
	          dataType : 'json',
	          data:{'parentId':option.fileID,'folderName':option.fileit},
	          url : BASE_URL + '/admin/knowledgecenter/getpdffilelist',
	          timeout : 3000,
	          beforeSend: function(){                        
                  var htmlLoader = '<div class="loaderBoxOverlay"></div>';
                  var parentEl =  $('#showdbloding');                
                  parentEl.closest('.kcRightPanel').css({position:'relative'});
                  parentEl.after(htmlLoader);
                 
                },
	          success : function(response){
	          	$('.loaderBoxOverlay').remove();
	        	 $("#showdbloding").removeClass('loaderBoxOverlay2');	        	
	        	 $("#showdbloding").html(response.content);	
	        	 $('#folderPid').val(option.fileID); 
	        	 $('#folderdir').val(option.fileit);   
	        	
	        	 var newcatname = "<i class='kcSprite kcLargFolder'></i> "+option.fileit+" ";  
	        	 $(".kcRightPanel .cateName").html(newcatname);	
				esl.closest('ul').find('.active').removeClass('active');		      	
		        esl.addClass('active');
	          }
	        }); 

}
 $(function() {
   kclist();


	$(".kc_addcategory1").click(function(){
           
		fileID	 =	 $(this).attr('id');
		fileTitle=	 $(this).attr('title');
		prevtitle=   $(this).attr('title'); 		
		$("#digForm").show();
		$("#ks_catname").val(fileTitle);
		$("#ks_check").val('');
		var popV = $("#knowledgecentarDialog").closest('.ui-dialog'); 

		$( "#knowledgecentarDialog" ).dialog( "open" )
		$('.ui-dialog-title', popV).text('Add new category');;
		$('.ui-dialog-buttonset .ui-button-text', popV).text('Add');
		$('.ui-button').css('display','block');
	});

 	$('body').on('click', '.kcRightPanel  #showdbloding > ul > li a', function(e){
 		e.stopPropagation();
 		e.preventDefault(); 		
 		if($('.kcupdatebtn').is(':visible')==true){	
 			return false;
 		}
    	parentel = $(this).closest('li');

    	var filepath = parentel.attr('fileData');
    	var filename = parentel.attr('filename');
    	var delrowId = parentel.attr('id');
    	var delId	 = 	parentel.attr('fileID');
    	var fdelId	 = 	parentel.attr('filedeletepath');
    	var etype = $(this).attr('etype');    	
    	var isGallery = $(this).attr('is-gallery');    	
    	var vcodecs = ""; 	

         if(etype=='view' && isGallery==0)
         { 
         
         	$('#openfilepdf').html('<div class="pdftitle"></div>\
				<iframe id="pdfIframe" style="border: 0px;" src="" width="600"	height="500"></iframe>');
			$('#pdfIframe').attr('src',filepath);						
	 		$("#openfilepdf").dialog("open");
	 		$(".ui-dialog-titlebar span").html(filename);
			$('.ui-widget button').css('display','none');

		}
		else if(isGallery==1)
        { 
        	var playVideoHTml = '<video width="100%" height="100%" controls autoplay>\
                                  <source src="'+filepath+'" type="video/mp4">\
                                  Your browser does not support HTML5 video.\
                                </video>';
			$('#openfilepdf').html(playVideoHTml);					
	 		$("#openfilepdf").dialog("open");
			$('.ui-widget button').css('display','none');
			$("#openfilepdf video").on('canplay', function (){
	 			$(window).trigger('resize');
	 		});
		}
		else if(isGallery==2) 
        { 

        	var vtype = $(this).attr('data-videoID'); 
        	
        	var playVideoHTml = ''; 
        	if(vtype=='webm' || vtype=='wmv') {
        		 "vcodecs = 'codecs=vp8,vorbis'";
        	}else if(vtype=='ogv'){
        		 vcodecs = "codecs='theora,vorbis'";
        		 vtype = 'ogg'	
        	}
        	        	
        	 playVideoHTml = '<video width="100%" height="100%" controls autoplay>\
                                  <source src="'+filepath+'" >\
                                  Your browser does not support HTML5 video.\
                                </video>';
             if(vtype=='mp3' || vtype == 'wav' || vtype == 'Ogg') {
            	playVideoHTml = '<div align="center" style="padding:20px 0px"><audio controls><source src="'+filepath+'"></audio></div>';
        	}
			$('#openfilepdf').html(playVideoHTml);					
	 		$("#openfilepdf").dialog("open");
	 		$("#openfilepdf video").on('canplay', function (){
	 			$(window).trigger('resize');
	 		});
			$('.ui-widget button').css('display','none');
			
		}else if(etype=='delete'){           		
				fileDelId	=	delId;
				fileDelpath 	= 	fdelId;				
				$("#dialog_confirm_file_delete" ).dialog("open");
				$('.ui-widget button').css('display','block');
		}else if(etype=='share'){			 
			 	fileDelId	=	delId;
				fileDelpath 	= 	fdelId;
				var Elinner = $(this);	
				var closeli = Elinner.closest('li');
				if(closeli.hasClass('show')){
					$('#lnx').toggle();
					$('#lnx').remove();
					closeli.removeClass('show');
					return false;
				}
				$('#showdbloding li').removeClass('show');
				closeli.addClass('show');
				kcinvite({'parentEl':Elinner,'fileid':delId});				
				$select('select');			
			}else if(etype=='edit'){			 
			 	var fileid	=	delId;				
				var Elinner = $(this);	
				var closeli = Elinner.closest('li');
				var filename = closeli.find('.kcFileName').text();
				var hban = closeli.clone();				
				filename = $.trim(filename);
				var templupdate = '<input id="updateknfile" class="updateknfile"  type="text" value="'+filename+'">\
<span style="float:right;margin:5px"><a  class="btn btn-gray btn-mini kccancle"  data-id="'+delId+'" >\
Cancel</a></span><span style="float:right;margin:5px"><a  class="btn btn-green btn-mini kcupdatebtn" data-id="'+delId+'" >\
Update</a></span>&nbsp; ';

		closeli.html(templupdate);
		closeli.find('.updateknfile').focus();	
		$('.kcupdatebtn').click(function(e){
		
		var filename = $('#updateknfile').val();
		
	$.ajax({
	          type : "POST",
	          dataType : 'json',
	          data:{'fileid':fileid,'fileName':filename},
	          url : BASE_URL + '/admin/knowledgecenter/updatefiletitle',	                 
	          success : function(response){
	        	 $('.kcSidebar').find('.active').trigger('click');
	          }
	         }); 

	}); 
		$('.kccancle').click(function(e){	
			closeli.after(hban);
			closeli.remove();
		}); 	
	}
  });


var kcinvite = function(options){
$('#lnx').remove();

	var El = options.parentEl;
	var rdata ='';	
	var usergroupset = function(){	
		if(usergroupsetadmin.length>0){
		$.each(usergroupsetadmin,function(i,val){
		rdata += '<option value="'+val.ugid+'">'+val.ugname+'</option>';
		})

			return rdata;
		}

	}
	var htmldata ='<div class="formRow sharefiletitle">Share file with</div>\
									<div class="formRow" id="updatepdf">\
										<label class="label">User set</label>\
										<div class="field dropDwonRowSuggestion">\
										<select name="pdfuserupdate" id="pdfuserupdate" style="min-width:165px">\
										<option value="0"> Select a user set </option>'+usergroupset(usergroupsetadmin)+'\
										</select>\
										<span class="btn btn-black" style="margin-top:-3px" id="resetusersetr2">Reset</span>\
										</div><input class="filenameset" type="hidden" value="'+options.fileid+'" name="">\
									</div>\
									<span class="orSpan">Or</span>\
									<div class="formRow" id="pdfuserboxd2">\
										<span>\
										<div id="flnright">\
										<label class="label">Search user(s)</label>\
										<div class="field dropDwonRowSuggestion" >\
										<input id="submit_tag_names" type="hidden" value="" name="">\
										<ul class="fieldInput" id="myTags3"></ul>\
										</div></div></span></div><span class="orSpan">Or</span>\
										<div class="formRow" id="pdfuserboxdl">\
										<span><div id="flnright" style="display:block">\
										<label class="label" style="width: 135px">All users</label>\
										<div class="field checkboxshar"><label class="label" style="width:auto;">\
										<input id="inviteall" class="inviteall" type="checkbox" name="inviteall" value="1">\
										<label></label>Share with all platform users</label></div></span>\
										</div></div>\
										<div class="formRow btnrow">\
										<button type="submit"  class="btn btn-green" name="kc_submit" id="kc_submit_update" > Share</button>\
										<button class="btn btn-gray canclepdfupdate" name="kc_canclepdfupdate" class="canclepdfupdate"> Cancel</button>';

		
	var dms = El.closest('li');	
 	
	dms.after('<div id="lnx"> '+ htmldata + '</div> ');
	$('#lnx').slideToggle('slow');
	 $("#myTags3").tokenInput(BASE_URL+"/admin/message/searchusers/", {
       preventDuplicates: true,
       hintText: "type user name",
       theme: "facebook",
       resultsLimit:10,
       resultsFormatter: function(item){ return "<li>" + "<img src='" + item.url + "' title='" + item.name + "' height='25px' width='25px' />" + "<div style='display: inline-block; padding-left: 10px;'><div class='full_name'>" + item.name + "</div><div class='email'>" + item.email + "</div></div></li>" },
       tokenFormatter: function(item) { return "<li><p><img src='" + item.url + "' title='" + item.name + "' height='25px' width='25px' />" + item.name + "</p></li>" }
    });


		
$('#resetusersetr2').hide();
$('body').on('change','#pdfuserupdate', function(){
   		if($("#pdfuserupdate").val()=='0'){   			
   			 $('#resetusersetr2').trigger('click');
   			 return false;   			 
   		}else{
   			$('#resetusersetr2').show();
   			 $('#lnx #token-input-myTags3').attr('disabled','disabled');
		     $('#lnx #token-input-myTags3').css({background:'#eee'});
		     $('#lnx ul.token-input-list-facebook').css({background:'#eee'});     
	   		}   		        
	     
  });  
 $("body").on('click','#resetusersetr2', function(e){
 		e.preventDefault()
 		$("#pdfuserupdate").val(0);
   		$('#pdfuserupdate').removeAttr('disabled');
   		$select('select');
   		$('#lnx #token-input-myTags3').removeAttr('disabled');   		
   		$('#lnx #token-input-myTags3').css({background:'#fff'});
	    $('#lnx ul.token-input-list-facebook').css({background:'#fff'});	    
	    $('#pdfuserboxd2 li.token-input-token-facebook').remove();
	    $('#resetusersetr2').hide();
   	}); 
 $("body").on('click','.canclepdfupdate', function(e){
 		$('#lnx').remove();
 }); 
 
 $("body").on('change','#myTags3', function(){
     	//$('#resetusersetr').show();
     	if($('#myTags3').val()!=0){     	
	    	$('#lnx #pdfuserupdate').attr('disabled','disabled');
	    	$select('#lnx #pdfuserupdate');
	    	$('#lnx .styledSelect ').css({background:'#eee'});
    	}else{
	    	$("#pdfuserupdate").val(0);
	   		$('#pdfuserupdate').removeAttr('disabled');
	   		$select('select');
    	}
     })
}

$("body").on('click','#kc_submit_update', function(e){
 		var usrgroup = $('#pdfuserupdate').val();
 		var alluser = '0';
 		var myTags3  = $('#myTags3').val();
 		var filename = $('.filenameset').val();
 		var isChecked 	= $('.inviteall').is(":checked");
 		
	   if(isChecked==true) alluser = '1';
 		
 		if(usrgroup=='0' && myTags3== '' && alluser=='0'){
 				$messageError('Please select a user(s) or a user set');
 				return false;
 		}		
				$.ajax(
                        {
                            type : "POST",
                            dataType : 'json',
                            data:{'userset':usrgroup,'taguser':myTags3,'filename':filename,'alluser':alluser},
                            url : BASE_URL+'/admin/knowledgecenter/updateuserset',
                            success : function(response) 
                            {                                      
                                $messageSuccess('file share successful');
                                socket.emit('chkactivitynotification', 1,clientID);
                                $('#lnx').remove(); 
                            },
                            error : function(error) {}             
                        });
 }); 

	$( "#openfilepdf" ).dialog({
		autoOpen: false,
		modal: true,
		open:function (){
			$('#openfilepdf .ui-dialog-titlebar').remove();
			$fluidDialog();		
		},
		close:function (){
			$('#openfilepdf').html('');
		}
    });
	
	$( "#dialog_confirm_file_delete" ).dialog({
      resizable: false,
      autoOpen: false,
      height:180,
      modal: true,
      buttons: {
        "Delete File": function() {
          	data	=	"fileDelpath="+fileDelpath+"&fileId="+fileDelId+"&require=deletefile"  ;
          	
			url		=	BASE_URL+"/admin/knowledgecenter/createbase";

			$.ajax({                                      
			  url: url,                        
			  data: data,                        
			  type: 'POST',            
			 success: function(data)          
			  {
				data = data.split('~');
				$( "#deletemsg" ).html(data[0]);
				
				if(data[1]==3)
				{
					$('#kc_filelist_'+fileDelId).hide();
					
				}

			 	setTimeout(function(){ 
						$( "#dialog_confirm_file_delete" ).dialog( "close" ); 	 }, 500);
				}
			});
        }
      }
    });

	$("body").on('click','.expandFiles',function(){
		  $('.addPdfFileWrapper2').fadeOut('fast');
		
		var esl = $(this);

		var fileID	 	=	esl.attr('id');
		var filetit	 	=	esl.attr('pretitle');

		var myJosn = {'fileID':fileID,'fileit':filetit};
		kcfilelist(myJosn,esl);
		
		
		
	});

    $('#addNewFile,.cancelUploadFile').click(function(){ 

	    $('.uploaderror').remove();
	    $('#resetusersetr').hide();
	    var uploaderFrm = 'Browse <form id="sharedropzone" action="/admin/Knowledgecenter/fileupload" class="dropzone">\
						  <div class="fallback">\
						    <input name="file" type="file" multiple />\
						  </div>\
						</form>\
						<input name="file" type="hidden" name="pdffile" id="pdffile" />';
	    $('.browserFilesDrp').html(uploaderFrm);
	     $('.hbfile').html('');
	   $('#pdffile, .fileTitle').val('');
	   $('#inviteall').attr('checked', false); 
	   $('#inviteall').removeAttr('disabled');
	   var vns = $('.uploadType');
	   vns.val('');
	   $('#pdfuserboxdl .token-input-list-facebook').remove();
	   $('#addFilenew #pdfuserset').removeAttr('disabled','disabled');
	   $select('#addFilenew #pdfuserset');
   	   $('#addFilenew .styledSelect ').css({background:'#fff'});
       $('.addPdfFileWrapper2').slideToggle();        

    var userNameTag = [];
    var inputVAlue = '';
	
    $("#myTags").tokenInput(BASE_URL+"/admin/message/searchusers/", {
       preventDuplicates: true,
       hintText: "type user name",
       theme: "facebook",
       resultsLimit:10,
       resultsFormatter: function(item){ return "<li>" + "<img src='" + item.url + "' title='" + item.name + "' height='25px' width='25px' />" + "<div style='display: inline-block; padding-left: 10px;'><div class='full_name'>" + item.name + "</div><div class='email'>" + item.email + "</div></div></li>" },
       tokenFormatter: function(item) { return "<li><p><img src='" + item.url + "' title='" + item.name + "' height='25px' width='25px' />" + item.name + "</p></li>" }
    });

     $("body").on('click','#inviteall', function(){ 
     	
     	if($('#myTags').val()!=0){      	
	    	$('#addFilenew #pdfuserset').attr('disabled','disabled');
	    	$('#inviteall').attr('disabled','disabled');
	    	$select('#addFilenew #pdfuserset');
	    	$('#addFilenew .styledSelect ').css({background:'#eee'});
    	}else{
    		if($('#inviteall').prop('checked')==true){
	    		$('#addFilenew #pdfuserset').attr('disabled','disabled');
		    	$select('#addFilenew #pdfuserset');
		    	$('#addFilenew .styledSelect ').css({background:'#eee'});	
		    	$('#addFilenew #token-input-myTags').attr('disabled','disabled');
			    $('#addFilenew #token-input-myTags').css({background:'#eee'});
			    $('#addFilenew ul.token-input-list-facebook').css({background:'#eee'});   			
	   		}else{
		   		$('#addFilenew #pdfuserset').removeAttr('disabled','disabled');
		    	$select('#addFilenew #pdfuserset');
		    	$('#addFilenew .styledSelect ').css({background:'#fff'});
		    	$('#addFilenew #token-input-myTags').removeAttr('disabled','disabled');
			    $('#addFilenew #token-input-myTags').css({background:'#fff'});
			    $('#addFilenew ul.token-input-list-facebook').css({background:'#fff'});   
				
	   		}
    	}
     });
   
     $("body").on('change','#myTags', function(){     	
     	if($('#myTags').val()!=0){     	
	    	$('#addFilenew #pdfuserset').attr('disabled','disabled');
	    	$('#inviteall').attr('disabled','disabled');
	    	$select('#addFilenew #pdfuserset');
	    	$('#addFilenew .styledSelect ').css({background:'#eee'});
    	}else{
    		if($('#inviteall').prop('checked')==true){
		    	$("#pdfuserset").val(0);
				$('#addFilenew #pdfuserset').attr('disabled','disabled');
				$('#inviteall').removeAttr('disabled');
	   		}else{
				$("#pdfuserset").val(0);
				$('#pdfuserset').removeAttr('disabled');
				$('#inviteall').removeAttr('disabled');
				$select('select');
	   		}
    	}
     });


    
		var folderDirs	= $('#folderdir').val(); 
		folderDirs	= folderDirs.replace('+','_');
	    folderDirs	= folderDirs.replace(' ','_');

		Dropzone.autoDiscover = false;
      var  myDropzoneshare = new Dropzone("#sharedropzone",{
                  url: BASE_URL+'/admin/knowledgecenter/fileupload/',
                  maxFiles: 1,
                  addRemoveLinks: true,
                  uploadMultiple:true,
                  parallelUploads: 1,
                  autoProcessQueue:false,
                  maxFilesize: 50, // MB
                  params: {folderDir: folderDirs},
			      init: function() {},
			      maxfilesreached:function (){ this.removeAllFiles();},                  
                 uploadprogress: function(file, progress, bytesSent) {              
                  	myDropzoneshareFile = file;
                  	cancelFileDropzone = file;
                  	loaderHtml = '<span class="loaderImg"></span><br>Uploading... <a href="javascript:void(0);" class="uploadCancel">Cancel</a>';
                  	$dbLoader({process:true,totalUpload:Math.round(progress), loaderHtml:loaderHtml});
				  },
				  processing: function(file, serverFileName) {  
                },                 
               });		
			
			myDropzoneshare.on("addedfile", function(file) {				
			    $('.hbfile').html(file.name);
			    $('#pdffile').val(file.name);
			});	
			 myDropzoneshare.on("error", function (file, serverFileName) {
			 	console.log(file)
                  $messageError(serverFileName);
              });		
      		myDropzoneshare.on("success", function (file, serverFileName) {
      			this.removeAllFiles();
     			$('input[name=pdffile]').val(serverFileName.filename);
     			$('.overlayPopup').fadeOut();
     			$('#addFilenew').fadeOut();
     			$('#kc_submit i').remove();
       var fileName		= serverFileName.filename; 
	   var fileTitle 	= $('#filetitle').val();
	   var folderId 	= $('#folderPid').val(); 
	   var isChecked	= '0';
	   folderDir	= $('#folderdir').val();
	   folderDirs	= folderDir.replace('+','_');
	   folderDirs	= folderDir.replace(' ','_');
	   var pdfuser 		= $('#myTags').val(); 
	   var userset 		= $('#pdfuserset').val(); 
	   isChecked 	= $('#inviteall').prop('checked');
	   if(isChecked==true) isChecked = '1';
	   var datad = {'pdfuser':pdfuser,'fileName':fileName,'fileTitle':fileTitle,'folderId':folderId,'folderDir':folderDir,'isChecked':isChecked};
	 
	    var URL		=	BASE_URL+"/admin/knowledgecenter/createfilesbase";
		$.ajax({                                      
			url: URL,
			type : "POST",
			dataType:'json',
			data:datad,			 
			success: function(data)          
			{
				
				var extfile = '';
				var typeIcon = '';
				var docurl = '';
				var licontent = '';
				var isgallery = '0';
				var view = 'View';
				extfile = data.kc_file.split('.');
                extfile = extfile[1];
                var mxarray = ['mp3','mp4','ogg','WebM','WAVE'];
                if ($.inArray(extfile, mxarray)!='-1') {
                	isgallery = '2';
                }

                if(extfile == 'mp3' || extfile == 'ogg' || extfile == 'WAVE'){			
									view = "Listen";									
								}
				if(extfile == 'mp4' || extfile == 'WebM' || extfile == 'ogv' || extfile == 'wmv'){			
									view = "Watch";									
								}
                typeIcon = '<i class="fa fa-file-'+iconexe[extfile]+'-o kcIconPdfList" style="font-size: 20px"></i>';
                if(iconexe[extfile] == 'image' || iconexe[extfile] == 'audio' || iconexe[extfile] == 'video'){
									docurl = BASE_URL+'/adminraw/knowledge_center/client_'+clientID+'/'+data.folderdir+'/'+data.kc_file;
								}else{
									docurl = '//docs.google.com/gview?url='+BASE_URL+'/adminraw/knowledge_center/client_'+clientID+'/'+data.folderdir+'/'+data.kc_file+'&embedded=true';
								}
				licontent = '<li id="kc_filelist_'+data.fileid+'" data-videoID="'+extfile+'" filename="'+data.kc_cat_title+'" filedeletepath=" '+data.folderdir+'/'+data.kc_file+'" fileid="'+data.fileid+'" filedata="'+docurl+'">\
'+typeIcon+' <span id="kc_list_'+data.fileid+'" class="kcFileName"> '+data.kc_cat_title+' </span><div class="lnx"></div><div class="btnRtlist"><a class="btn btn-green btn-mini" href="javascript:void(0);" etype="view" data-videoID = "'+extfile+'" is-gallery="'+isgallery+'">'+view+'</a><span class="sprt"> </span><a class="btn btn-green btn-mini" href="javascript:void(0);" etype="edit">&nbsp;Edit&nbsp;</a>\
<span class="sprt"> </span><a class="btn btn-green btn-mini" href="#" etype="share">Share</a>\
<span class="sprt"> </span><a class="btn btn-danger btn-mini" href="javascript:void(0);" etype="delete">Delete</a></div></li>';


			$('#showdbloding').find('ul').prepend(licontent); 
				$("#beforecallfile").hide();
				$("#exp_condition").hide();
				$(".overlayPopup").hide();
				$(".noCategory").remove();									
				socket.emit('chkactivitynotification', true,clientID);
			}
			
	    });
     });

     $('body').on('click','.uploadCancel', function (e){
	    	 e.preventDefault();
  		   $('.cancelUploadFile').trigger('click');
  		   $('#mesageNotfiOverlay').remove();
     });
	$("body").on('click','#kc_submit', function(e){

	   var fileTitle 	= $('#filetitle').val();
	   var filenamev 	= $('.hbfile').text();	 
	   var pdfuser 		= $('#myTags').val(); 
	   var userset 		= $('#pdfuserset').val();   
	   var pdffile 		= $('#pdffile').val();
	   var isChecked 	= $('#inviteall').prop('checked');
 	
	   var error = false;	   
	    if(!fileTitle) {
	        $messageError("Please enter a title");      
	        error = true;
	    } 
	    /*
	    if (/[^a-zA-Z 0-9]+/.test(fileTitle) ) {
	        $messageError("Title should use valid charactors.");      
	        return false;   
	    } 
	    */ 
	    if(!filenamev) {
	        $messageError("Please select file to upload");      
	        error = true;
	    } 	   
	    if(error==false){
		e.preventDefault();
		e.stopPropagation();
		myDropzoneshare.processQueue();
		}
	});    

 	$("body").on('click','#resetusersetr', function(e){
 		e.preventDefault()
 		$("#pdfuserset").val(0);
   		$('#pdfuserset').removeAttr('disabled');
   		$select('select');
   		$('#inviteall').removeAttr('disabled','disabled');
   		$('#inviteall').css({background:'#fff'});
   		$('#addFilenew #token-input-myTags').removeAttr('disabled');   		
   		$('#addFilenew #token-input-myTags').css({background:'#fff'});
	    $('#addFilenew ul.token-input-list-facebook').css({background:'#fff'});	    
	    $('#pdfuserboxdl li.token-input-token-facebook').remove();
	    $('#resetusersetr').hide();
   	}); 

   	$('body').on('change','#addFilenew #pdfuserset', function(){
   		if($("#pdfuserset").val()=='0'){   			
   			 $('#resetusersetr').trigger('click');
   			 $('#inviteall').removeAttr('disabled','disabled');
   			 $('#inviteall').css({background:'#fff'});
   			 return false;   			 
   		}else{
   			$('#resetusersetr').show();
   			 $('#addFilenew #token-input-myTags').attr('disabled','disabled');
   			 $('#inviteall').attr('disabled','disabled');
   			 $('#inviteall').css({background:'#eee'});
		     $('#addFilenew #token-input-myTags').css({background:'#eee'});
		     $('#addFilenew ul.token-input-list-facebook').css({background:'#eee'});     
	   		}   		        
	    
	  });  

});  	
}); 
