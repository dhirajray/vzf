$(function (){
	var totalChatWindow = 0;
	 var pRight =0;
	 var lastChatWindowPosition =0;

 $(window).resize(function() {
        windH = $(window).height();
        windW = $(window).width();      
    }).resize();

$.chatWindows = function (options){
var defaults = {
				userType:'alluser',				
				content:'',
				userName:'<i class="fa fa-user"></i> Users',
				positions:'',
				highlight:'<a href="javascript:void(0);" class="highlightChatBox"><i class="fa fa-certificate"></i><i class="fa fa-square-o"></i></a>',
				search:'<a href="javascript:void(0);" class="searchInUserBox"><i class="fa fa-search"></i></a>',
				expand:'<a href="javascript:void(0);" class="expandCollapse"><i class="fa fa-angle-down"></i></a>',
				maximize:'<a href="javascript:void(0);" class="chatMinmax"><i class="fa fa-expand"></i></a>',
				close:'<a href="javascript:void(0);" class="chatCloseUser"><i class="fa fa-times"></i></a>',
				class:''
			 }

var settings = $.extend({}, defaults, options);
var userType = settings.userType;
var positions = settings.positions;
if(positions!=''){
	positions = 'right:'+positions;
}
 var openUserType = '<div class="openChatType">'+settings.userName+'</div>';
 var iconSetChat = '<div class="iconSetChat">'+settings.highlight+settings.search+settings.expand+settings.close+settings.maximize+'</div>';
var titleBar = '<div class="chatTittleBar">'+openUserType+iconSetChat+'</div>';
var allUsers = '<div class="chatContainer">'+settings.content+'</div>';
var chatInterface = '<div class="dbchatWrapper '+settings.class+'" style="'+positions+'"   chat-type="'+settings.userType+'">'+titleBar+allUsers+'</div>';
$('body').append(chatInterface);



	if(userType!='alluser'){
		$.cookie('openedChildWindow-'+userType, userType);
		$('.dbchatWrapper[chat-type="'+userType+'"] .chatMessageContainer').perfectScrollbar({ wheelSpeed: 100,includePadding:true });
		var sh = $('.dbchatWrapper[chat-type="'+userType+'"] .chatMessageContainer').prop('scrollHeight');
		$('.dbchatWrapper[chat-type="'+userType+'"] .chatMessageContainer').scrollTop(sh);
		if($.cookie('chatWindowOff-'+userType)=='true'){
			$('.dbchatWrapper[chat-type="'+userType+'"]:not(.offCh) .expandCollapse').trigger('click');
		}
		var isFullChat = $('body').hasClass('fullChatWindowSize');
		if(isFullChat==true){
					$('.onchatMaxWin .chatMinmax').trigger('click');
					$('.dbchatWrapper[chat-type="'+userType+'"] .chatMinmax').trigger('click');
				}

	}else{
		$('.dbchatWrapper[chat-type="'+userType+'"] .chatContainer').perfectScrollbar({ wheelSpeed: 100,includePadding:true });
		var sh = $('.dbchatWrapper[chat-type="'+userType+'"] .chatContainer').prop('scrollHeight');
		$('.dbchatWrapper[chat-type="'+userType+'"] .chatContainer').scrollTop(sh);
		if(windH<sh){
			$('.dbchatWrapper[chat-type="'+userType+'"] .chatMinmax').trigger('click');
		}

	}

	

}
$.allUserChat = function(options){
        
        var defaults = {
                append:false,
                prepand:false,
                userId:'',
                remove:false,
                allUser:true,
                content:'',             
             }

        var settings = $.extend({}, defaults, options);
        var ap = settings.append;
        var pre = settings.prepand;
        var cnt = settings.content;
        var remove = settings.remove;
        var id = settings.userId;

         if(settings.allUser==true){
            $('.allUserChat').remove();
            $.chatWindows({close:'',class:'allUserChat',content:cnt});
            

            $('.chathighlightOverlay').remove();
            $('body').prepend('<div class="chathighlightOverlay"></div>');

            if($.cookie('chatWindowOff-alluser')=='true'){                  
                $('.dbchatWrapper[chat-type="alluser"] .expandCollapse').trigger('click');
            }
            if($.cookie('hightLightChat')=='on'){
                $('.chathighlightOverlay').show();
            }
            if($.cookie('chatWindowType-alluser')=='max'){
                $('.dbchatWrapper[chat-type="alluser"]:not(.onchatMaxWin) .chatMinmax').trigger('click');
            }

            $('.dbchatWrapper[chat-type="alluser"] li').each(function (){
                var userId  = $(this).attr('data-id');
                if($.cookie('openedChildWindow-'+userId)==userId){              
                    $(this).trigger('click');
                }
            });

         }
         else if(ap==true){
            $('.allUserChat ul').append(cnt);
        }else if(pre==true){
            $('.allUserChat ul').prepend(cnt);
        }
        else if(remove==true){
            $('.allUserChat li[data-id="'+id+'"]').remove();
            $('.childChatWindow[chat-type="'+id+'"] .chatCloseUser').trigger('click');
        }
    };
	
$('body').on('click', '.highlightChatBox', function(event) {
				$('.chathighlightOverlay').fadeToggle(function(){
					if($('.chathighlightOverlay').is(':visible')==true){
							$.cookie('hightLightChat', 'on');
						}else{
							$.cookie('hightLightChat', '');
						}
				});
				
			});
$('body').on('click', '.chatMinmax', function(event) {
				var tsl = $(this);
				var p = tsl.closest('.dbchatWrapper');
				var addCompress = $('.fa-expand', tsl).removeClass('fa-expand');
				var addExpand = $('.fa-compress', tsl).removeClass('fa-compress');
				 
				var chatType = p.attr('chat-type');
				if(p.hasClass('childChatWindow')==true && $('body').hasClass('fullChatWindowSize') == false ){
					$('.childChatWindow').removeClass('onchatMaxWin');
					if(p.hasClass('offCh')==true){
						p.removeClass('offCh');
						$.cookie('chatWindowOff-'+chatType, '');
					}
					$('.allUserChat').removeAttr('style');
					p.removeAttr('style');
					$('body').addClass('fullChatWindowSize');
					p.addClass('onchatMaxWin');
					addCompress.addClass('fa-compress');
					$('.fa-angle-up', p).removeClass('fa-angle-up').addClass('fa-angle-down');
					
				}else{	
				if(p.hasClass('childChatWindow')==true){				
					addExpand.addClass('fa-expand');
					$('.fa-angle-down', p).removeClass('fa-angle-down').addClass('fa-angle-up');
					$('body').removeClass('fullChatWindowSize');
					$.reRarrangechatWindow();
					var sh = $('.chatMessageContainer' , chatInner).prop('scrollHeight');
						$('.chatMessageContainer' , chatInner).scrollTop(sh);
					p.css({height:354});
					p.removeClass('onchatMaxWin');

				}else{
					p.removeAttr('style');
					if(p.hasClass('onchatMaxWin')==false){
						p.css({top:0})
						p.addClass('onchatMaxWin');
						p.removeClass('offCh');
						addCompress.addClass('fa-compress');
						$('.fa-angle-up', p).removeClass('fa-angle-up').addClass('fa-angle-down');
						$.cookie('chatWindowType-'+chatType, 'max');
						$.cookie('chatWindowOff-'+chatType, '');


					}else{
						p.removeClass('onchatMaxWin');
						p.css({height:400});
						addExpand.addClass('fa-expand');
						$('.fa-angle-down', p).removeClass('fa-angle-down').addClass('fa-angle-up');
						$.cookie('chatWindowType-'+chatType, '');


					}
				}


					

				}

				if(p.hasClass('allUserChat')==true){	
					$('.chatContainer', p).perfectScrollbar('destroy');
					$('.chatContainer', p).perfectScrollbar({ wheelSpeed: 100,includePadding:true });
				}else{
					$('.chatMessageContainer', p).perfectScrollbar('destroy');
					$('.chatMessageContainer', p).perfectScrollbar({ wheelSpeed: 100,includePadding:true });
				}
				$('textarea', p).elastic();
				
				
			});
$('body').on('click', '.expandCollapse', function(event) {
	var tsl = $(this);
	var p = tsl.closest('.dbchatWrapper');
	var chatType = p.attr('chat-type');
	$('body').removeClass('fullChatWindowSize');
	p.toggleClass('offCh');
	if(p.hasClass('offCh')==true){
		p.css({top:'100%'});
		p.removeClass('onchatMaxWin');
		$.cookie('chatWindowOff-'+chatType, 'true');
		$.cookie('chatWindowType-'+chatType, '');
		$('.fa-angle-down', tsl).removeClass('fa-angle-down').addClass('fa-angle-up');
	}else{
		p.css({top:'inherit'});

		$('.fa-angle-up', tsl).removeClass('fa-angle-up').addClass('fa-angle-down');
		$.cookie('chatWindowOff-'+chatType, '');
	}
	
});



$('body').on('click', '.chatCloseUser',function (){
	var tsl = $(this);
	var p = tsl.closest('.dbchatWrapper');
	var pleft = p.css('right');		
	var chatType = p.attr('chat-type');
	var totalHideWindow = $('body .childChatWindow:hidden').size();
	$('body').removeClass('fullChatWindowSize');
	

	 $('body .childChatWindow:hidden:last').css({right:pleft, bottom:-400}).show().animate({bottom:0});
	 	$(this).closest('.dbchatWrapper').remove();
		$.cookie('openedChildWindow-'+chatType, '');
		$.cookie('chatWindowType-'+chatType, '');
		$.cookie('chatWindowOff-'+chatType, '');

	if(totalHideWindow<1){
		$.reRarrangechatWindow();
	}
});

$('body').on('click', '.searchInUserBox',function (){
	var tsl = $(this);
	var p = tsl.closest('.dbchatWrapper');
	$('.chatTittleBar',p).toggleClass('activeSearchBox');
	
});


$('body').on('click', '.dbchatWrapper[chat-type="alluser"] li',function (){
			var tsl = $(this);			
			var p = tsl.closest('.dbchatWrapper');
			var dataId = tsl.attr('data-id');
			var chatW = p.width();
			var uName = $('.Uname',tsl).text();
			var userPic =  $('.chatUserPic img',tsl).attr('src');
			var userHtml = '<div class="userPicTitle"><img src="'+userPic+'"></div> <div class="oneline unameTitle">'+uName+'</div>';
			var chatWindow = $('body .childChatWindow[chat-type="'+dataId+'"]');
			var isFullChat = $('body').hasClass('fullChatWindowSize');
			if(!chatWindow.length){			
			
				pRight =  parseInt($('body .dbchatWrapper:last').css('right').split('px')[0]);

				var lastWindowLft = pRight+chatW+10;				
				var checkSpacelastWindow = lastWindowLft+chatW;
				if(windW<checkSpacelastWindow){
					$('body .childChatWindow:visible:last').hide();
					lastWindowLft = lastChatWindowPosition;

				}else{
					lastChatWindowPosition = lastWindowLft;
				}
				var chatMessageContainer = '<div class="chatMessageContainer"><ul></ul></div>';
				var chatWindowInputField = chatMessageContainer+'<div class="chatInputAreaBox">\
												<a href="javscript:void(0);" class="fa fa-plus-circle addIntoChat"></a>\
												<textarea></textarea>\
											</div>';

				if( $('body').hasClass('fullChatWindowSize')==true ){
					lastWindowLft = '';
				}
				if(lastWindowLft!=''){	
					lastWindowLft	= lastWindowLft+'px';	
				}		
				$.chatWindows({class:'childChatWindow',userType:dataId,userName:userHtml,content:chatWindowInputField , positions:lastWindowLft, highlight:'', search:''});		
		}else{
			if(chatWindow.is(':visible')==false){
				$('body .childChatWindow:visible:last').hide();
				chatWindow.show();
			}else{
				if(chatWindow.hasClass('offCh')==true){
					$('.expandCollapse', chatWindow).trigger('click');	
				}
			}
			if(isFullChat==true){
				$('.onchatMaxWin .chatMinmax').trigger('click');
				$('.chatMinmax', chatWindow).trigger('click');
			}			
		}
	$('.chatInputAreaBox textarea').elastic();	
});

$('body').on('keyup','.chatInputAreaBox textarea', function (e){
	e.stopPropagation();
	var thisEl = $(this);
		chatTxt = thisEl.val();
		chatInner = thisEl.closest('.childChatWindow');
		var msgTemplate = '<li class="sender">\
				<div class="textMsg">\
					'+chatTxt+'\
					<div class="msgTime">12:00 pm</div>\
				</div>\
				<img src="https://development.db-csp.com//timthumb.php?src=/userpics/1425553462.jpg&amp;q=100&amp;w=32&amp;h=32" class="userImg">\
			</li>';
	 if(e.keyCode == 13 && !e.shiftKey) { 
  		$('.chatMessageContainer ul' , chatInner).append(msgTemplate);
  		var sh = $('.chatMessageContainer' , chatInner).prop('scrollHeight');
  		$('.chatMessageContainer' , chatInner).scrollTop(sh);
  		thisEl.val('').removeAttr('style');
	 }

});
$('body').on('keypress','.chatInputAreaBox textarea', function (e){
	if(e.keyCode == 13 && !e.shiftKey) { 
	return false;
	 }
});

	$.reRarrangechatWindow = function (){
		var lastWindowLft= 0;		
		$('body .childChatWindow:visible').each(function (index){
				var tsl = $(this);
				var dataId = tsl.attr('chat-type');
				var chatW = tsl.width();
				var allW = $('.dbchatWrapper.allUserChat').width();
				var allWRightSpace = parseInt($('.dbchatWrapper.allUserChat').css('right').split('px')[0]);
				 if(index>0){
				 	allWRightSpace=0;
				 }
				lastWindowLft += chatW+allWRightSpace+10;
				$(this).css({right:lastWindowLft});
		});

	};
	$.winResize = function (){
		var lastWindowLft = 0;
		
		$('.dbchatWrapper').each(function (index){
			var chatW = $(this).width();
			var allWRightSpace = 10;
			if(index<=0){
				 	allWRightSpace=0;
				}

			 lastWindowLft = (chatW*index)+chatW+allWRightSpace;				
			var checkSpacelastWindow = lastWindowLft;
			
			$(this).hide();
			if(windW>checkSpacelastWindow){
				$(this).show();
			}
		});
		
	}


	$(window).resize(function (){
		if($('body').hasClass('fullChatWindowSize')==false){
		  	$.winResize();
		  }
	});

	$.allUserChat({content:'<ul>\
				<li data-id="1">\
					<div class="chatUserPic">\
						<span class="chatNoti">43</span>\
						<span class="liveStatus"></span>\
						<img height="42" src="https://development.db-csp.com//timthumb.php?src=/userpics/1426938123.jpg&q=100&w=42&h=42"  />\
					</div>\
					<div class="chatUserDetails">\
						<span class="recentChatTime">2s</span>\
						<div class="oneline Uname">Deshbandhu Porwal</div>\
						<div class="oneline recentCht">Yes ! peter i am fine, and where are you dear?</div>\
					</div>\
				</li>\
				<li data-id="2">\
					<div class="chatUserPic">\
						<span class="chatNoti">43</span>\
						<span class="liveStatus"></span>\
						<img height="42" src="https://development.db-csp.com//timthumb.php?src=/userpics/1428559873.jpg&q=100&w=42&h=42"  />\
					</div>\
					<div class="chatUserDetails">\
						<span class="recentChatTime">2s</span>\
						<div class="oneline Uname">Vijay kumar</div>\
						<div class="oneline recentCht">Yes ! peter i am fine, and where are you dear?</div>\
					</div>\
				</li>\
				<li data-id="3">\
					<div class="chatUserPic">\
						<span class="liveStatus"></span>\
						<img height="42" src="https://development.db-csp.com//timthumb.php?src=/userpics/1429101991.jpg&q=100&w=42&h=42"  />\
					</div>\
					<div class="chatUserDetails">\
						<span class="recentChatTime">2s</span>\
						<div class="oneline Uname">Adam jones</div>\
						<div class="oneline recentCht">Yes ! peter i am fine, and where are you dear?</div>\
					</div>\
				</li>\
				<li data-id="4">\
					<div class="chatUserPic">\
						<span class="liveStatus offline"></span>\
						<img height="42" src="https://development.db-csp.com//timthumb.php?src=/userpics/1427112173.jpg&q=100&w=42&h=42"  />\
					</div>\
					<div class="chatUserDetails">\
						<span class="recentChatTime">sunday</span>\
						<div class="oneline Uname">Deepak kumar</div>\
						<div class="oneline recentCht">Yes ! peter i am fine, and where are you dear?</div>\
					</div>\
				</li>\
				<li data-id="5">\
					<div class="chatUserPic">\
						<span class="liveStatus offline"></span>\
						<img height="42" src="https://development.db-csp.com//timthumb.php?src=/userpics/1427112173.jpg&q=100&w=42&h=42"  />\
					</div>\
					<div class="chatUserDetails">\
						<span class="recentChatTime">sunday</span>\
						<div class="oneline Uname">Manmeet singh</div>\
						<div class="oneline recentCht">Yes ! peter i am fine, and where are you dear?</div>\
					</div>\
				</li>\
				<li data-id="6">\
					<div class="chatUserPic">\
						<span class="liveStatus offline"></span>\
						<img height="42" src="https://development.db-csp.com//timthumb.php?src=/userpics/1426162926.jpg&q=100&w=42&h=42"  />\
					</div>\
					<div class="chatUserDetails">\
						<span class="recentChatTime">monday</span>\
						<div class="oneline Uname">Dhiraj ray</div>\
						<div class="oneline recentCht">Yes ! peter i am fine, and where are you dear?</div>\
					</div>\
				</li>\
				<li data-id="7">\
					<div class="chatUserPic">\
						<span class="liveStatus offline"></span>\
						<img height="42" src="https://development.db-csp.com//timthumb.php?src=/userpics/1425553316.jpg&q=100&w=42&h=42"  />\
					</div>\
					<div class="chatUserDetails">\
						<span class="recentChatTime">monday</span>\
						<div class="oneline Uname">Kapil kumar</div>\
						<div class="oneline recentCht">Yes ! peter i am fine, and where are you dear?</div>\
					</div>\
				</li>\
				<li data-id="8">\
					<div class="chatUserPic">\
						<span class="liveStatus offline"></span>\
						<img height="42" src="https://development.db-csp.com//timthumb.php?src=/userpics/1425553462.jpg&q=100&w=42&h=42"  />\
					</div>\
					<div class="chatUserDetails">\
						<span class="recentChatTime">monday</span>\
						<div class="oneline Uname">Rajendra yadav</div>\
						<div class="oneline recentCht">Yes ! peter i am fine, and where are you dear?</div>\
					</div>\
				</li>\
			</ul>'});

	
});