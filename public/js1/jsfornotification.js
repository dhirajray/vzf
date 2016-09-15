$('document').ready(function(){

    $('body').on('click','#changeusername' , function(){
        var curUname = $(this).attr('username');
        var profileTemplate = '<h2>Change username</h2>\
                               <div id="profileuname"  class="leaguesPostPopUp" >\
                                  <div id="passform" class="postTypeContent">\
                                    <div class="formRow singleRow">\
                                       <div class="formField">\
                                            <input type="text" id="newusername" class="textfield" value="'+curUname+'" >\
                                            <i class="optionalText">Choose Name </i>\
                                        </div>\
                                    </div>\
                                  </div>\
                              </div>';

        
       $.dbeePopup(profileTemplate,{ 
            overlayClick:false,
            otherBtn:'<a href="javascript:void(0)" class="btn btn-yellow pull-right" id="changeusernamestep2" >Done</a>'});

        $('#changeusernamestep2').click(function(){  
            $('#changeusernamestep2').append(' <i class="fa fa-spinner fa-spin"> </i>').css('cursor', 'default'); 
            var uname       =   $.trim($('#newusername').val());

            chkspace        =   uname.indexOf(' ') === -1;

            if( !/^[a-zA-Z0-9.]+$/.test( uname ) ) {
               //$messageError('user name can contain alpha numeric values only including dot !');
               $dbConfirm({content:'Only alphanumeric characters allowed', yes:false,error:true});
               setTimeout(function(){
                        $('#changeusernamestep2 .fa-spin').remove();
                        $('#changeusernamestep2').css('cursor', 'pointer');
                        }, 500); 
               return false;
            }
         
            if(curUname == uname){
                //$messageError("We don't see a change in the username! Please enter a new one.");
                $dbConfirm({content:"We don't see a change in the username! Please enter a new one.", yes:false,error:true});
                setTimeout(function(){
                        $('#changeusernamestep2 .fa-spin').remove();
                        $('#changeusernamestep2').css('cursor', 'pointer');
                        }, 500); 
                return false;
            }


            $('#profileuname').html('<div id="content_dat" ><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div><div class="clearfix"></div>');
            url         =   BASE_URL+"/profile/changeusername";            
            var data        =   'uname='+uname; 

            $.ajax({
                url : url,
                data : data,
                type : "POST",
                dataType : 'json',
                beforeSend : function() {
                  $('#changeusernamestep2 .fa-spin').remove();           
                },                  
                success : function(response) { 
                    if(response.success=='true'){                        
                        setTimeout(function(){
                        $('#changeusernamestep2').css('cursor', 'pointer');
                        }, 200); 
                        $.dbeePopup('close');
                       
                        //$dbConfirm({content:"username changed successfully", yes:false});
                        $('#changeusername').attr('username', uname).prev('span').html('@'+uname);
						var newLink = BASE_URL+"/user/"+uname;
						$("#ProfileLink").attr('href', newLink );
						window.history.pushState("", "", newLink);
						$('#leftsideMenu li:nth-child(2)').children("a").attr('href', newLink );					
                    } else {
                        setTimeout(function(){
                        $('#changeusernamestep2 .fa-spin').remove();
                        $('#changeusernamestep2').css('cursor', 'pointer');
                        }, 1000);
                        $('#profileuname').html(response.formfields);
                        $('.UsNmSuggestions li').click(function(){
                                var thisVal = $(this).text();                               
                                if($(this).hasClass('active')){
                                    $(this).removeClass('active');
                                    $('#newusername').val('');
                                }else{
                                     $(this).parents('ul').find('.active').removeClass('active');
                                    $(this).addClass('active');
                                    $('#newusername').val(thisVal);
                                    $('#sugErrorMsg').slideUp();

                                }                                
                        });

                         $.dbeePopup('resize');

                    }
                },
            });
            return false;            
        });

 /* $('body').on('click','.btnsharereject' , function(){

        e.stopPropagation();
        e.preventDefault();
        var El = $(this);
        var fileid = El.attr('fileid');
        var act = El.attr('act');
        alert(fileid);

  });*/

        $('body').on('keyup', '#newusername', function(event) {
                var thisVal = $.trim($(this).val());
                $('.UsNmSuggestions .active').removeClass('active');
                
                $('.UsNmSuggestions li').each(function(){
                    var sugValue = $(this).text();
                    if(thisVal==sugValue){
                        $('.UsNmSuggestions li:contains("'+thisVal+'")').addClass('active');
                    }//else if(thisVal!=sugValue){ $('.UsNmSuggestions li').removeClass('active');}
                   
                  
                });
             
                

               // if(thisVal==sugValue) $('.UsNmSuggestions li:contains("'+thisVal+'")').addClass('active');
               // else  $('.UsNmSuggestions li').removeClass('active');

                if(thisVal=='')$('.UsNmSuggestions .active').removeClass('active');
                if(event.which==13)$('#changeusernamestep2').click();
        });




        
    });


  
});  

function seenotification(startnew, type, datedd) 
{   
    $('#noti-type').val( type);
    $('.userproLinks a').removeClass('active');
    $('#notes-tab-' + type).addClass( 'active');
    if(startnew==0){startnew='';}
   // $dbLoader('#notification-feed' +startnew,0);
    $('#notification-feed'+startnew).html('<div id="content_dat" ><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div><div class="clearfix"></div>'); 
    var url = BASE_URL+"/notification/fetchnotification2";
    var data = "type=" + type +"&start=" +startnew+"&seedate=" + datedd;

    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        dataType: 'html',
        success: function (data) 
        {               
            $('#notification-feed' + startnew).html(data);      
            $("html").getNiceScroll().resize();
        }
    });
}
function seenotification2(startnew, type, datedd) 
{	
    $('#noti-type').val( type);
   // $('.userproLinks a').removeClass('active');
   // $('#notes-tab-' + type).addClass( 'active');
    if(startnew==0){startnew='';}
   // $dbLoader('#notification-feed' +startnew,0);
    $('#notification-feed'+startnew).html('<div id="content_dat" ><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div><div class="clearfix"></div>'); 
    var url = BASE_URL+"/notification/fetchnotification";
    var data = "type=" + type +"&start=" +startnew+"&seedate=" + datedd;

    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        dataType: 'html',
        success: function (data) 
        {             	
            $('#notification-feed' + startnew).html(data);      
            $("html").getNiceScroll().resize();
        }
    });
}

function seenotificationreload(date1, type,date2) 
{
    $('#noti-type').val( type);
    $('.userproLinks a').removeClass('active');
    $('#notes-tab-' + type).addClass( 'active');  
    var url = BASE_URL+"/notification/fetchnotification";    
    var data = "type=" + type + "&date1=" + date1+ "&date2=" + date2 ;

    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        dataType: 'html',
        success: function (data) 
        {  
            var resultArr = data.split('~');
            $('#notification-feed').append(resultArr[0]);
            $('.loader' + date1).hide();
            $("html").getNiceScroll().resize();
        }
    });
}

function callsocket()
{
     if(localTick == false){
        socket.emit('chkactivitynotification', true,clientID,SESS_USER_ID);
    }
}

function chkactivitynotification(n) 
{
    dbid   =   $('#dbid').val();
    if ( dbid != '' && typeof (dbid) !== 'undefined')   
        data = 'cmntondb='+dbid;  
    else 
        data = '';
 siteHeadTitle =    $('#siteHeadTitle').html();
 if(typeof siteHeadTitle==='undefined') siteHeadTitle ='DB-csp';
    $.ajax(
    {
        type : "POST",
        dataType : 'json',
        data: data,
        url : BASE_URL + '/notification/chkactivitynotification',    
        cache : false,
        success : function(response) 
        {               
            // NEW DB NOTIFICATION
            tot =   (response.otherTot + response.dbs + response.comments+response.groupTot+response.groupTot2+response.league+response.privatepost+response.eventposttot);           
           

            if ( tot != '' && typeof (tot) != 'undefined') {           
                $('[id="actnotifications-top-wrapper"]').show();
                $('li[id="notificationsTop"]').addClass('activeNotification');
                $('[id="actnotifications-top"]').html( tot);
               // $('#notificationsTop a').on('click');
               $('title').html('('+tot+') '+siteHeadTitle);

            } else {
                $('li[id="notificationsTop"]').removeClass('activeNotification');
                $('[id="actnotifications-top-wrapper"]').hide();
                $('[id="actnotifications-top"]').html('');
                $('title').html(siteHeadTitle);
            }

            if ( response.msgTot != '' && typeof (response.msgTot) != 'undefined') {  
               
              $('[id="actnotifications-msg-top-wrapper"]').show();
                $('[id="topmessageMenu"]').addClass('activeNotification');
                $('[id="actnotifications-msg-top"]').html(response.msgTot); 
                    storeNotificationsmsg = false;
               /* $('#actnotifications-msg-top-wrapper').css('display','block');
                $('#actnotifications-msg-top').html( response.msgTot);
                $('#actnotifications-profile-top').html( ' - '+response.msgTot);
                msg = (response.msgTot==1)? 'You have a new message' : 'You have '+response.msgTot+' new messages';
                $('.showmsgarrive').html(msg);
                $('.showmsghighlight .prstTitle a').text('click here');
                $('.showmsghighlight').addClass('msgHighlight');*/
            } else {
                $('li[id="topmessageMenu"]').removeClass('activeNotification');
               $('[id="actnotifications-msg-top-wrapper"]').hide();
               $('[id="actnotifications-msg-top"]').html('');
                /*$('#actnotifications-profile-top').html( ' ');
                $('.showmsgarrive').html('No new messages');
                $('.showmsghighlight .prstTitle a').text('see previous');
                $('.showmsghighlight').removeClass('msgHighlight');*/
            }
            
			/*if ( response.groupTot != '' && typeof (response.groupTot) != 'undefined') {           
                $('#notifications-group-top-wrapper').css('display','block');
                $('#notifications-group-top').html( response.groupTot);
            } else {
                $('#notifications-group-top-wrapper').hide();
                $('#notifications-group-top').html('');
            } 
                 
		   if ( response.groupTot2 != '' && typeof (response.groupTot2) != 'undefined') {           
                $('#notifications-group-top-wrapper').css('display','block');
                $('#notifications-group-top').html( response.groupTot2);
            } else {
               // $('#notifications-group-top-wrapper').hide();
               // $('#notifications-group-top').html('');
            }*/
            // *****************************************
            // NEW COMMENT NOTIFICATION       
            ghostnotification();                
          
        },

        error : function(error) {
            
        }

    });
}

function ghostnotification() 
{
    dbid    =   $('#dbid').val();
    if ( dbid != '' && typeof (dbid) !== 'undefined')   data = 'cmntondb='+dbid;  else data = '';  
    $.ajax(
    {
        type : "POST",
        dataType : 'json',
        data:data,
        url : BASE_URL + '/notification/ghostnotification',
        cache : false,
        success : function(response) 
        {               
            // NEW DB NOTIFICATION

           var notedbhidden = $('#notifications-top-hidden').val();
           if(response.ResultArr!='' && typeof (response.ResultArr) !== 'undefined')
           {    
                $ghostMessage(response.ResultArr);
                $('#notifications-top-hidden').val(response.ResultArr);
            }
                 
            // *****************************************
            // NEW COMMENT NOTIFICATION                 
                                    
           
        },

        error : function(error) {
           
        }

    });
}





