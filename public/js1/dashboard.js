// FUNCTION TO USER DETAILS ON PROFILE

function customDashboardDisplay()
{
	$('#customFieldsDown').hide();
	$.ajax({
		type : "POST",
		dataType : 'json',
		url : BASE_URL + '/dashboarduser/mycustomdashboard',
		//data : {'keyword':keyword,'type': calling},
		cache : false,			
		success : function(response) {	
			$('.customDashboard').html(response.returnDash);
			if(response.showfilters==1)
			{
				$('#postMenu').after(' <div id="customFieldsDown" ><div style="float:left;padding: 11px;font-size: 14px;font-weight: bold;color: #666;"  id="selectedType" > </div><div  style=" display: inline-block; margin-top: 2px;float:right "><div class="btn btn-mini btn-yellow dropDown pull-right" >Sort by type <i class="fa fa-angle-down"></i><ul class="postListingdashboard dropDownList right" id="customFields"></ul></div></div>	 <div class="clear"></div></div>');
			}	
		}		
	
	});
}

function adminliveDisplay(contener)
{
	$.ajax({
		type : "POST",
		dataType : 'json',
		url : BASE_URL + '/dashboarduser/adminlive',
		//data : {'keyword':keyword,'type': calling},
		cache : false,			
		success : function(response) {	
				$(contener).html(response.msg);	
		}		
	
	});
}

function MyPendingQuestion(contener)
{
	$.ajax({
		type : "POST",
		dataType : 'json',
		url : BASE_URL + '/dashboarduser/myquestion',
		data : {'type': 'limit'},
		cache : false,			
		success : function(response) {	
				
				if(response.count == 0)
				{
				  	$('#mypendingbox').remove();
				}
				else
				{
					$('#mypendingbox').show();
					$('#mypendingbox').addClass('whiteBox');
					var appDiv = '<div class="rtListOver"><h2 class="mypendingquestion" title="Click to open or Drag to re-arrange"><i class="fa fa-question-circle"></i>My questions <span class="navAllLink"><a id="myquestionviewall" rel="MyQuestion">view all</a></span> </h2>	</div>	<div class="rboxContainer" id="mypendingquestion">'+response.ownercontent+'</div>';
				  		
				  	$('#mypendingbox').html(appDiv);
				}	
		}		
	
	});
}

function PendingQuestion(contener)
{  
	//$('#leftListing .tabLinks').remove();
//	alert('PendingQuestion')
	$.ajax({
		type : "POST",
		dataType : 'json',
		url : BASE_URL + '/dashboarduser/pendingquestion',
		data : {'type': 'limit'},
		cache : false,			
		success : function(response) {			
				if(response.count > 0)
				{
				  $('#pendingbox').show();
				  $('#pendingbox').addClass('whiteBox');
				  newdiv = '<div class="rtListOver"><h2 class="pendingquestion" title="Click to open or Drag to re-arrange"><i class="fa fa-question-circle"></i>Pending questions <span class="navAllLink"><a id="pendingviewall" rel="PendingQuestion">view all</a></span> </h2></div><div class="rboxContainer" id="pendingquestion">'+response.content+' </div>';
				  $('#pendingbox').html(newdiv);
				  
				}
				
				if(response.count==0)
				{
					$('#pendingbox').remove();
				}

				
				
		}		
	
	});
}

function loaduserpdf(contener)
{
	$.ajax({
		type : "POST",
		dataType : 'json',
		url : BASE_URL + '/dashboarduser/loaduserpdf',
		//data : {'keyword':keyword,'type': calling},
		cache : false,			
		success : function(response) {

			if(response.success==1){				
				$('#pdfrtbox').show();
				$(contener).html(response.content);
				$('#myuploads').html(response.myshare);
				if(response.myshare!=''){
					$('#myuploadsbox').show();
				}
			}						
		}		
	
	});
}



function showmyDashboard(userid, from, activetab,controller, action,calling,cus_ids,thisBtn) 
{
	calling = typeof(calling)=='undefined' ? '' :calling;
	window.busy = false;
	cus_ids = typeof(cus_ids)=='undefined' ? '' :cus_ids;
	$('#selectedType').html('All');
	if(thisBtn!='')
	{
		var thisEl = $(thisBtn);
		selectedFilter = thisEl.attr('selFilt');
		if(selectedFilter!='') $('#selectedType').html(selectedFilter);
		else $('#selectedType').html('');
	}	
	
	var fields = new Array();
	var fieldsName 		= $('#fieldsName').val();
	var masterfieldids 	= $('#masterfieldids').val();
	var masteruserpics 	= $('#masteruserpics').val();
	var groupcats 		= $('#groupcats').val();

	if(cus_ids!='')
	{
		var fields 		= cus_ids.split(",");

		var fieldsName = typeof(fieldsName)=='undefined' ? '' :fieldsName.split(",");
		//var fieldsName 	= fieldsName.split(",");
		var userName 	= typeof(masterfieldids)=='undefined' ? '' :masterfieldids.split(",");
		var userPic 	= typeof(masteruserpics)=='undefined' ? '' :masteruserpics.split(",");
		var groupcats   = typeof(groupcats)=='undefined' ? '' :groupcats.split(",");
		var checkedValue = new Array();	
	}
	//alert(calling); return false;
	var customFields = '';
	$('#my-dbees').html('');	
	$('#customFieldsDown').hide();


	var cat = cus_ids;
    $('#filtercat').val( cat);
    if (calling=='dbcat') {
    	var data = "cat=" + cat;
    	if(userid!='notincall'){
    		customFields += '<li><a selFilt="All" onclick=javascript:showmyDashboard("userid",3,"my-dbees","myhome","catetorylist","dbcat","'+cat+'",this); href="javascript:void(0)"  >All </a>	</li>';
	    	$.each(fields, function (key, value) {	
	    		if(value!=''){				
					customFields += '<li><a selFilt="'+fieldsName[key]+'" onclick=javascript:showmyDashboard("notincall",3,"my-dbees","myhome","catetorylist","dbcat","'+value+'",this); href="javascript:void(0)" class="feed-link"  >'+fieldsName[key]+' </a><input type="hidden" id="" value=""	</li>';
				}
			}); 
			
			$('#customFields').html(customFields);
		}
		$('#customFieldsDown').show();	
    }
    if (calling=='masterUsers') {
    	var data = "user=" + cat;
    	if(userid!='notincall'){
    		
    		customFields += '<li><a selFilt="All" onclick=javascript:showmyDashboard("userid",3,"my-dbees","Following","customdashboarduser","masterUsers","'+cat+'",this); href="javascript:void(0)" class="feed-link" id="my-comments-home2" >All </a>	</li>';
	    	$.each(fields, function (key, value) {	
	    		if(value!=''){		
	    			if(userName[key]!=''){		
						customFields += '<li><a selFilt="'+userName[key]+'" onclick=javascript:showmyDashboard("notincall",3,"my-dbees","Following","customdashboarduser","masterUsers","'+value+'",this); href="javascript:void(0)" class="feed-link"  ><img src="'+IMGPATH+'/users/small/'+userPic[key]+'" width="32" height="32"> '+userName[key]+' </a>	</li>';
					}
				}
			}); 
			
			$('#customFields').html(customFields);	
		}
		$('#customFieldsDown').show();
    }
    if (calling=='groupcat') 
    {
    	var data = "cat=" + cat;
    	if(userid!='notincall'){
    		customFields += '<li><a selFilt="All" onclick=javascript:showmyDashboard("userid",3,"my-dbees","group","customdashboardgroups","groupcat","'+cat+'",this); href="javascript:void(0)" class="feed-link" id="my-comments-home2" >All </a>	</li>';
	    	$.each(fields, function (key, value) {	
	    		if(value!=''){				
					customFields += '<li><a selFilt="'+groupcats[key]+'" onclick=javascript:showmyDashboard("notincall",3,"my-dbees","group","customdashboardgroups","groupcat","'+value+'",this); href="javascript:void(0)" class="feed-link"  >'+groupcats[key]+' </a>	</li>';
				}
			}); 
			
			$('#customFields').html(customFields);
		}
		$('#customFieldsDown').show();
	}
	if(calling=='movedposts')  { $('#customFields').html(''); var data = "movedposts=" + cat;}

    //if(activetab!='') $('#'+activetab).addClass('active');
    $dbLoader('#'+activetab);
   // alert('#'+activetab)

    var url = BASE_URL+"/"+controller+"/"+action;
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        dataType: 'html',
        success: function (data) {  
            var resultArr = data.split('~#~');
            $('#my-dbees').removeClass('myWhiteBg'); 
            $('#masterUsers').val(cat);	
            if (calling=='dbcat') 
            {
                $('#feedtype').val('cat');
                $('#'+activetab).html( resultArr[0] );
                $("#callcatfirsttime").val(1);
                $('#startnewcat').val( PAGE_NUM);
            }
            else if (calling=='masterUsers') 
            {

                $('#feedtype').val( 'following');
                $('#startnewfollowing').val(PAGE_NUM);
                $('#my-dbees').html(resultArr[0]);
            }
            else if (calling=='groupcat') 
            {
                $('#feedtype').val( 'groupcatpost');
                $('#startnewdrouppost').val(PAGE_NUM);
                $('#my-dbees').html(resultArr[0]);
            }
            else if (calling=='movedposts') 
            {
                $('#feedtype').val('moveddbposts');
                $('#startnewmovedpost').val(PAGE_NUM);
                $('#my-dbees').html(resultArr[0]);
            }
            //console.log(resultArr);
            getMentionUser($.trim(resultArr[2]));

            
        }
    });

}


function followandsearchusers(calling,thisEl) {
	//alert(calling)
	//return false;
	calling = typeof(calling)=='undefined' ? 'searchuser' :calling;
	thisEl = typeof(thisEl)=='undefined' ? '' :$(this);
	if(thisEl!='')
	{
		if(calling=='following')
		{
			loading = $('.showMoreFollowing').attr('loadmore');
			offset = $('.showMoreFollowing').attr('currentOffset');
		}
		if(calling=='followers')
		{
			loading = $('.showMoreFollowers').attr('loadmore');
			offset = $('.showMoreFollowers').attr('currentOffset');
		}

	}

	loading = typeof(loading)=='undefined' ? '' :loading;
	offset = typeof(offset)=='undefined' ? '0' :offset;

	if(calling=='searchuser')
	{
		var keyword	=	$('#keyword').val();
		if(keyword.length<1){
			return false;
		}
		$('#searchleagueUsers .searchIcon2').removeClass('fa-search').addClass('fa-spin fa-spinner');	
	}		
	$.ajax({
		type : "POST",
		dataType : 'json',
		url : BASE_URL + '/dashboarduser/followandsearchusers',
		data : {'keyword':keyword,'type': calling,'offset':offset,'loading':loading},
		cache : false,			
		success : function(response) {	
			if(calling=='searchuser')
			{	
				$('#searchleagueUsers .searchIcon2').removeClass('fa-spin fa-spinner').addClass('fa-search');
				//followandsearchusers('following');	
				$('#search-list .formRow').removeClass('singleRow');
				$("#searchunfollowusers").show().find('.formField').html(response.userlist);
				$("#total-search").val(response.totaluser);	
			}
			if(calling=='followers')
			{
				
				loadbtn = '<div class="btn btn-green pull-right showMoreFollowers" style="display:none" loadmore="true" currentOffset="0" onclick=followandsearchusers("followers",this)>show more</div>';
				$(".showMoreFollowers").remove();
				if(loading=='')
				{
					$("#followers-list").html(response.userlist);
					if(response.totaluser>19)
	                 {
	                 	$("#followers-list").append(loadbtn);
	                 	$('.showMoreFollowers').show();
	                 	$('.showMoreFollowers').attr('currentOffset',(response.offset+1));
	                 }
	                 else
	                 {
	                 	$('.showMoreFollowers').hide();
	                 }
				}else
				{
					$("#followers-list").append(response.userlist);
					if(response.totaluser>19)
	                 {
	                 	$("#followers-list").append(loadbtn);
	                 	$('.showMoreFollowers').show();
	                 	$('.showMoreFollowers').attr('currentOffset',(response.offset+1));
	                 }
	                 else
	                 {
	                 	$('.showMoreFollowers').hide();
	                 }
				}	
				$("#total-followers").val(response.totaluser);
			}	
			if(calling=='following')
			{
				loadbtn = '<div class="btn btn-green pull-right showMoreFollowing" style="display:none" loadmore="true" currentOffset="0" onclick=followandsearchusers("following",this)>show more</div>';
				$(".showMoreFollowing").remove();
				if(loading=='')
				{
					$("#following-list").html(response.userlist);

					if(response.totaluser>19)
	                 {
	                 	$("#following-list").append(loadbtn);
	                 	$('.showMoreFollowing').show();
	                 	$('.showMoreFollowing').attr('currentOffset',(response.offsetFol+1));
	                 }
	                 else
	                 {
	                 	$('.showMoreFollowing').hide();
	                 }
				}else
				{

					$("#following-list").append(response.userlist);
					if(response.totaluser>19)
	                 {
	                 	$("#following-list").append(loadbtn);
	                 	$('.showMoreFollowing').show();
	                 	$('.showMoreFollowing').attr('currentOffset',(response.offsetFol+1));
	                 }
	                 else
	                 {
	                 	$('.showMoreFollowing').hide();
	                 }
				}	
				$("#total-following").val(response.totaluser);
			}		
		}		
	
	});

}
var PostCategoryList='';
var GroupCategoryList='';
function Categorylists()
{
	$dbLoader('.defaultCategories');
	//$dbLoader('.showPostTheseUsers');
	$.post( BASE_URL+'/myhome/Categorylists', function( data ) {
		defaultlists 	= 	data.split('~') 
		PostCategoryList	=	defaultlists[0];
		GroupCategoryList	=	defaultlists[1];
		$('.defaultCategories').html(PostCategoryList);
		$('.defaultCategories, .categoryListMyDash').show();
		$('.defaultGrpCategories').show().html(GroupCategoryList);
		$('.showPostTheseUsers').show();
	});
}

$(document).ready(function(){

	//alert('ppppppppppp');
	$('body').on('mouseover','#profileimage',function(){		
		//imgtag = $(this).attr("datapic-url");
	});
	
	$('body').on('click','.proinfo .fa-pencil',function(){
		$('#account_setting').trigger('click');
	});

	$('body').on('click','.proinfo .fa-pencil',function(){
		$('#account_setting').trigger('click');
	});

	$('body').on('click','.filterdashboard', function(){
		
		var filtertype = $(this).attr('id');
		//var filterLoader = $(this).attr('id');
		var filterids  = '';
		var subid	   = '';	
		var thisEl  = $(this);

		if(filtertype=='' ) 
		{
			$dbConfirm({content:'Please select correct dashboard filters', error:true, yes:false})
			return false;
		}
		$(this).append(' <i class="fa fa-spinner fa-spin"></i>');
		/*if(filtertype=='searchUsers')
			{
				var checkedValue = $('.BoxSearch input:checked').val();
				var checkedValue = new Array();
				$.each($('.BoxSearch input[type="checkbox"]:checked'), function (key, value) {					
					checkedValue.push($(this).val());
				}); 
				filterids = checkedValue.toString();
			}
			else if(filtertype=='Following')
			{
				var checkedValue = $('.boxFollowing input:checked').val();
				var checkedValue = new Array();
				$.each($('.boxFollowing input[type="checkbox"]:checked'), function (key, value) {					
					checkedValue.push($(this).val());
				}); 
				filterids = checkedValue.toString();
				
			}
			else if(filtertype=='Followers')
			{
				var checkedValue = $('.boxFollowers input:checked').val();
				var checkedValue = new Array();
				$.each($('.boxFollowers input[type="checkbox"]:checked'), function (key, value) {					
					checkedValue.push($(this).val());
				}); 
				filterids = checkedValue.toString();
				
			}
		*/	
		if(filtertype=='postCategory')
		{
			var checkedValue = $('.categoryList input:checked').val();
			var checkedValue = new Array();
			$.each($('.categoryList input[type="checkbox"]:checked'), function (key, value) {					
				checkedValue.push($(this).val());
			}); 
			filterids = checkedValue.toString();
			
		} else if(filtertype=='groupCategory')
		{
			var checkedValue = $('.grpcategoryList input:checked').val();
			var checkedValue = new Array();
			$.each($('.grpcategoryList input[type="checkbox"]:checked'), function (key, value) {					
				checkedValue.push($(this).val());
			}); 

			filterids 	=	checkedValue.toString();

			var checkedType = $('.defaultGrpType input:checked').val();
			var checkedType = new Array();
			$.each($('.defaultGrpType input[type="checkbox"]:checked'), function (key, value) {					
				checkedType.push($(this).val());
			}); 
			subid		=	checkedType.toString();
		} else
		{
			var checkedValue = $('.BoxSearch input:checked').val();
			var checkedValue = new Array();
			$.each($('.BoxSearch input[type="checkbox"]:checked'), function (key, value) {					
				checkedValue.push($(this).val());
			}); 
			filterids += checkedValue.toString();

			var checkedValue = $('.boxFollowing input:checked').val();
			var checkedValue = new Array();
			$.each($('.boxFollowing input[type="checkbox"]:checked'), function (key, value) {					
				checkedValue.push($(this).val());
			}); 
			filterids += ','+checkedValue.toString();

			var checkedValue = $('.boxFollowers input:checked').val();
			var checkedValue = new Array();
			$.each($('.boxFollowers input[type="checkbox"]:checked'), function (key, value) {					
				checkedValue.push($(this).val());
			}); 
			filterids += ','+checkedValue.toString();
			filtertype = 'masterfield';
		}
		$.ajax({
			type: "POST",
			dataType : 'json',
			url: BASE_URL+"/dashboarduser/customdashboard",
			data:{"filtertype": filtertype,'filterid':filterids,'subtype':subid},
			success:function(data)
			{  

				customDashboardDisplay(); // fetching customized posts
				if(filtertype=='searchUsers')
				{
					followandsearchusers('following');	
					followandsearchusers('followers');
				}
				if(filtertype=='Followers')
				{
					followandsearchusers('following');
				}	
				if(filtertype=='Following')
				{
					followandsearchusers('followers');
				}	 
				//$('#'+filtertype).html('post');
				$('.fa-spin',thisEl).remove();
			}   
		});

	});
	

	$('body').on('click', '.showPostTheseUsers .labelCheckbox input',function(){
		var checkedValue = $(this).val();
		var thisEl = $(this);
		var chkme = $(this).is(":checked");
		var checkboxPost = $('.showPostTheseUsers input[type="checkbox"]');
		if(chkme==true)
		{
			$.each(checkboxPost, function (key, value) {	
				if(checkedValue==$(this).val()) $(this).prop( "checked", true );			
			}); 
		} else if(chkme==false)
		{
			$.each(checkboxPost, function (key, value) {	
				if(checkedValue==$(this).val()) $(this).prop( "checked", false );			
			}); 
		}
	});

	$('body').on('click','.custmydashTitle', function(){
		
		
		
		if($('.customizeDeshboardform').is(':visible')==false){
			if($('.defaultCategories').is(':empty')==true){
				$('.showPostTheseUsers').hide();
				Categorylists();
				followandsearchusers('following');				
				followandsearchusers('followers');
			}
		}
		
	/*	$('.categoriesBtn').toggleClass('active');
		$('.categoryList').slideToggle('fast');
		$('.grpcategoriesBtn').toggleClass('active');*/
		$('.customizeDeshboardform').slideToggle(100);
		$('#idprstTitleDeshboard i').toggleClass('fa-minus');	

        $('#biogrophydisplayForm').slideUp(100);
        $('.userBtnOver span.biogrophydisplayHeading').removeClass('active'); 
        $(this).toggleClass('active');
        if($(this).hasClass('active')==true){
			$('html').addClass('activeDeshAndBio');
		}else{
			$('html').removeClass('activeDeshAndBio');
		}

	});

	$('body').on('click','.scoringdisplayHeading', function(){

		$('#scoringdisplayForm').slideToggle('slow');
		$('#idprstTitleLeage i').toggleClass('fa-minus');	

	});

	$('body').on('click','.biogrophydisplayHeading', function(){
		$('.customizeDeshboardform').slideUp(100);
        $('.userBtnOver span.custmydashTitle').removeClass('active');
        $(this).toggleClass('active');
		$('#biogrophydisplayForm').slideToggle(100);
		$('#idprstTitleBiogrophy i').toggleClass('fa-minus');
		if($(this).hasClass('active')==true){
			$('html').addClass('activeDeshAndBio');
		}else{
			$('html').removeClass('activeDeshAndBio');
		}
	});


	$('body').on('click','.prstTitle .LinkClose', function(){
		$('#rightListing .profileStatsWrp').fadeOut('fast');
	});


	$('body').on('click','.grpcategoriesBtn', function(e){	
		e.preventDefault();
		$(this).toggleClass('active');
		//$('.grpcategoryList input').attr('checked', false);
	});

	/*$('body').on('click','.categoriesBtn', function(e){			
		e.preventDefault();
		$(this).toggleClass('active');
		$('.categoryList').slideToggle('fast');
		
		//$('.categoryList input').attr('checked', false);
	});*/

$('body').on('click', '#pdfrtviewall', function(e) {
 		e.preventDefault();
 		$('#leftListing .tabLinks').remove();
        $('#dashboarduserDetails').remove();
        if ($('#dbee-feeds').is(':visible') == true) {	                
	               $('#dbee-feeds').html('<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>');
	            } else {
	               $('#my-dbees').html('<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>');
	                
	            }
        $('.searchByThis').remove(); 
        $('.customizeDeshboard').hide();
        $('.biogrophydisplay').fadeOut('fast');
        $('#customFieldsDown').hide(); // to hide customised dashborad filters   
        if ($('#dbee-feeds').is(':visible') == false) {
            $('#dashboarduserDetails').fadeOut('fast');
            $('#postMenu').fadeOut('fast');
            $('#Selectedposts').fadeOut('fast');
            $('#leftListing .profileStatsWrp').fadeOut('fast');
            $('#rightListing .proinfo').fadeOut('fast');
            $('#leftListing .group-highlighted').fadeOut('fast');
            $('#rightListing .contactInfo').fadeOut('fast');

        }

        $.ajax({
            type: "POST",
            data: {
                type: "pdf"
            },
            url: BASE_URL + '/dashboarduser/loaduserpdfdetail',
            beforeSend: function() {},
            success: function(response) {
            	if(response.success==1){
            	var typeIcon = '';
            	//var iconexe = {"docx": "word", "method": "newURI", "regex": "^http://.*"}
            	var iconexe = {
								'docx':'word',
								'xlsx':'excel',
								'xls':'excel',
								'doc':'word',
								'ppt':'powerpoint',
								'txt':'text',
								'jpeg':'image',
								'jpg':'image',
								'gif':'image',
								'png':'image',
								'mp3':'audio',
								'mp4':'audio',
								'pdf':'pdf'
								}	
            	var pdf ='';
            	var datacontent = '';
            	datacontent += '<div class="user-name titleHdpad">Downloads</div><li class="pdfListing">';
            	
            	var extfile='';
            	
	            $.each(response.content, function(i, value){
	            	  extfile = value.kc_file.split('.');
	            	  extfile = extfile[1];
	            typeIcon = '<i class="fa fa-file-'+iconexe[extfile]+'-o pdfPostIcon"></i>';
            	datacontent +='<div class="downloadPdflist" data="'+value.id+'" is_front="'+value.is_front+'"><div class="rtpdflinkd">'+typeIcon+' '+value.title+' <span class="subtitle">Shared by <a href="'+BASE_URL+'/user/'+value.full_name+'">'+value.full_name+'</a> on '+value.sdate+' at '+value.stime+'</span></div>\
            	  <div class="rtpdfIcons" title="delete"><span><a href="' + BASE_URL + '/dashboarduser/downloadpdfuser/pdf/' +value.kc_file+ '/isf/' +value.is_front+ '/id/'+value.id+'" rel="dbTip" title="Download" ><i class="fa fa-download"></i></a></span><span id="sfiledel"><a rel="dbTip" title="Delete"><i class="fa fa-times"></i></a></span>\
            	</div></div>'; 

				});
				datacontent += '</li>';
				if ($('#dbee-feeds').is(':visible') == true) {
	                $('#dbee-feeds').html(datacontent);
	            } else {
	                $('#my-dbees').html(datacontent);
	            }
                //$('#leftListing').html(datacontent);

            }else{
					 $('#leftListing').html( 'no results found' );
				}
                 
                 var pdflistView = $('.downloadPdflist').length;
	                if(pdflistView == 1){
	                	$('li.pdfListing').addClass('pdfListround');
	                }

			}
        });

        if ($(document).scrollTop() != 0) {
            $('html, body').animate({
                scrollTop: 0
            }, 'fast');
        }
    });






$('body').on('click', '#pendingviewall', function(e) {
 		e.preventDefault();
 		$('#leftListing .tabLinks').remove();
        $('#dashboarduserDetails').remove();
        if ($('#dbee-feeds').is(':visible') == true) {	                
	               $('#dbee-feeds').html('<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>');
	            } else {
	               $('#my-dbees').html('<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>');
	                
	            }
        $('.searchByThis').remove(); 
        $('.customizeDeshboard').hide();
        $('.biogrophydisplay').fadeOut('fast');
        $('#customFieldsDown').hide(); // to hide customised dashborad filters   
        if ($('#dbee-feeds').is(':visible') == false) {
            $('#dashboarduserDetails').fadeOut('fast');
            $('#postMenu').fadeOut('fast');
            $('#Selectedposts').fadeOut('fast');
            $('#leftListing .profileStatsWrp').fadeOut('fast');
            $('#rightListing .proinfo').fadeOut('fast');
            $('#leftListing .group-highlighted').fadeOut('fast');
            $('#rightListing .contactInfo').fadeOut('fast');

        }

        $.ajax({
            type: "POST",
            data: {
                type: "all"
            },
            url: BASE_URL + '/dashboarduser/pendingquestion',
            beforeSend: function() {},
            success: function(response) {
           
           if(response.count > 0){
            	 var contents='';            	
				 contents=response.content;
            	
            	var datacontent = '';           	

				datacontent += '<div class="user-name titleHdpad">Pending questions</div><li class="questionListMiddle suggestRow">';
				datacontent += contents+'</li>';

				if ($('#dbee-feeds').is(':visible') == true) {
	                $('#dbee-feeds').html(datacontent);
	            } else {
	               
	                $('#my-dbees').html(datacontent);
	            }                

            }

            if(response.count==0)
            {
					 $('#leftListing').html( 'no question found' );
			}



			}
        });

        		
				

        if ($(document).scrollTop() != 0) {
            $('html, body').animate({
                scrollTop: 0
            }, 'fast');
        }
    });



$('body').on('click', '#myquestionviewall', function(e) {
 		e.preventDefault();
 		
        $('#dashboarduserDetails').remove();
        if ($('#dbee-feeds').is(':visible') == true) {	                
	               $('#dbee-feeds').html('<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>');
	            } else {
	               $('#my-dbees').html('<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>');
	                
	            }
        $('.searchByThis').remove(); 
        $('.customizeDeshboard').hide();
        $('.biogrophydisplay').fadeOut('fast');
        $('#customFieldsDown').hide(); // to hide customised dashborad filters   
        if ($('#dbee-feeds').is(':visible') == false) {
            $('#dashboarduserDetails').fadeOut('fast');
            $('#postMenu').fadeOut('fast');
            $('#Selectedposts').fadeOut('fast');
            $('#leftListing .profileStatsWrp').fadeOut('fast');
            $('#rightListing .proinfo').fadeOut('fast');
            $('#leftListing .group-highlighted').fadeOut('fast');
            $('#rightListing .contactInfo').fadeOut('fast');

        }

        $.ajax({
            type: "POST",
            data: {
                type: "all"
            },
            url: BASE_URL + '/dashboarduser/myquestion',
            beforeSend: function() {},
            success: function(response) {
           
            	if(response.count > 0){
            	
            	var datacontent = '';           	

				datacontent += '<div class="user-name titleHdpad">My questions</div><li class="pdfListing questionListMiddle">';
				datacontent += response.ownercontent+'</li>';

				if ($('#dbee-feeds').is(':visible') == true) {
	                $('#dbee-feeds').html(datacontent);
	            } else {
	               
	                $('#my-dbees').html(datacontent);
	            }                

            }else{
					 $('#leftListing').html( 'no question found' );
				}



			}
        });

        if ($(document).scrollTop() != 0) {
            $('html, body').animate({
                scrollTop: 0
            }, 'fast');
        }
    });

$('body').on('click', '#proCollapseMain input[name=makefeedsprivate]', function() {
	
	var el = $(this);
	var ndata =0;
	if(el.is(':checked')==true){
		ndata = 1;
	}
	$.ajax({
		type : "POST",
		dataType : 'json',
		url : BASE_URL + '/dashboarduser/hidefeed',
		data : {'type': ndata},
		cache : false,			
		success : function(response) {	
			//$messageSuccess('updated successfully');
		}		
	
	});

});

$('body').on('click', '#proCollapseMain input[name=makeanonymous]', function() {
	
	var el = $(this);
	var ndata =0;
	if(el.is(':checked')==true){
		ndata = 1;
	}
	$.ajax({
		type : "POST",
		dataType : 'json',
		url : BASE_URL + '/dashboarduser/hideuser',
		data : {'type': ndata},
		cache : false,			
		success : function(response) {	
			if(response==0)
			{
			   //$messageSuccess('Your VIP account is no longer anonymous');
			   $dbConfirm({
                        content: 'Your VIP account is no longer anonymous',
                        yes: false,
                        error: true
                    });
		    }
		    if(response==1)
		    {
		       //$messageSuccess('Your VIP account is now anonymous');
		       $dbConfirm({
                        content: 'Your VIP account is now anonymous',
                        yes: false,
                        error: true
                    });
		    }
		}		
	
	});

});


});





function dashboardprofile(id,sid, callfrom) 
{ 
	$.ajax({
	type: "POST",
	dataType : 'json',
	url: BASE_URL+"/dashboarduser/detail",
	data:{"userid": id,'sid':sid,'callfrom':callfrom},
	cache: true,
		success:function(data)
		{   //console.log(data)
			var userProfilePic = data.userProfilePic;
			$('#dashboarduserDetails').html(data.userinfo);
			
			
			
			if(data.profileholder==false){        		
				$('.profileStatsWrp').hide();	
			}
			if(data.contactinfo==''){        		
				$('.contactInfo').hide();
			}
			if(sid=='0'){
		    	$('#plink').css('display','block');        	
		    	//$('#dashboardcontactinfo').html();
		    	if(data.personalinfo=="" && data.contactinfo==""){ 
		    		$('.bioInfoSep').hide();
		    	}
		    	else
		    	{
		    		$('.bioInfoSep').show();
		    	}
		    	$('#dashboardpersonalinfo').html(data.personalinfo+data.contactinfo);
		    	
		    	$('.dbeetypeicon').html(data.dbicon); 
		    	$('#lkdac').html(data.profilelink2); 
		    	$('#plink span.progressComplete').css('width',data.profilelink2per+'%');
		    	$('#lkdac2').html(data.profilecomplete); 
		    	$('#plink2 span.progressComplete').css('width',data.profilecompleteper+'%');
		    	$('#plink2').css('display','block');
			}
			
		}   
	});
}





