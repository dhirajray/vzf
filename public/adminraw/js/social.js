var ProfileDetailsHttp; 
var currentGroupId = '';
var currentGroupName ='';
var shareType = '';
var uniqueIDSocial = '';
var callingfor = '';

$(function() {

    $('body').on('click', '.socialfriends', function() 
    {

        
        callingfor = $(this).attr('data-for');
        uniqueIDSocial = $(this).attr('data-uniqueValue');
        shareType = $(this).attr('data-type');
        title = $(this).attr('data-title');

      

        if(shareType=='linkedin')
        {
            searchForMessage = 'Search LinkedIn connections';
            placeholder = 'type a connections name and hit enter';
        }else if(shareType=='twitter'){
            searchForMessage = 'Search Twitter followers';
            placeholder = 'type the exact twitter username of the user you want to find';
        }else if(shareType=='facebook'){
            searchForMessage = 'Search Facebook friends';
            placeholder = 'type a friends name and hit enter';
        }
        else if(shareType=='InviteExpert'){
             searchForMessage = 'Search users to invite';
            placeholder = 'type a name and hit enter';
        }
        else{
            searchForMessage = 'Search users';
            placeholder = 'type a name and hit enter';
        }
        $('#detailsDialog').remove();
        var htmlLightbox = '<div id="detailsDialog" title="'+searchForMessage+'">\
          <div class="srcUsrWrapper">\
          <div class="sprite searchIcon2"></div>\
          <input type="text" placeholder="'+placeholder+'" shareType = "'+shareType+'" class="findsocialfriend" socialFriendlist="true"  >\
          </div><div id="datacollect" style="float:none"></div>\
          <div id="userInfoContainer"></div></div>';
        $('body').append(htmlLightbox);
        $('#detailsDialog').hide();
        $("#detailsDialog").dialog({
            dialogClass: 'detailsDialogBox',
            width: 800,
            height: 500,
            title: searchForMessage + ' <span style="color:#FF8C03;">' + title + '</span>',
            open: function() 
            {
                $fluidDialog();
                $('.ui-dialog-buttonset').hide();
                $('#detailsDialog').show();
            },
            buttons: {
                "Invite": function() {
                    
                    $(this).attr('disabled',true);
                    var userInfo = [];
                    Tthis = $(this);
                    flag = 0;
                    $('input:checkbox[name=socialUser]').each(function() 
                    {    
                        if($(this).is(':checked')){
                            userInfo.push($(this).val());
                            flag = 1;
                        }
                    });
                    if (flag == 0) 
                    {
                        $messageError('Please select a user');
                        return false;
                    }
                    var socialUser = userInfo.join();

                    $.ajax({
                        type: "POST",
                        dataType: 'json',
                        data: {"shareType": shareType,'uniqueIDSocial':uniqueIDSocial,'callingfor':callingfor,'socialUser':socialUser},
                        url: BASE_URL + '/admin/social/socialinvite',
                        beforeSend:function(){                       
                        //$('.ui-dialog-buttonset').html('<i class="fa fa-spinner fa-spin"></i>');  
                        $( ".ui-button-text-only" ).after( '<i class="fa fa-spinner fa-spin"></i>'); 
                        $( ".ui-button-text-only" ).attr('disabled',true);         
                        },
                        success: function(response) 
                        {
                            if(response.status=='success')
                            {
                                $messageSuccess("Invitation sent successfully");


                                 if(localTick == false)
                                 {                                  
                                    socket.emit('chkactivitynotification', 1,clientID);
                                 }
                                 
                                 if(shareType=='InviteExpert'){
                                 
                                    $('#startbtnhangout'+uniqueIDSocial+' a').addClass('disabled');
                                    $('#startbtnhangout'+uniqueIDSocial+' a').removeClass('StartBroadcast');
                                    $('#inviteexpertbtn'+uniqueIDSocial).html('');
                                    $('#inviteexpertbtn'+uniqueIDSocial).html('<span style="color:#ff0000">Invitation sent </span>&nbsp; '+response.content+'&nbsp;&nbsp; <a href="javascript:void(0);" data-id="'+uniqueIDSocial+'" id="CancelInvite'+uniqueIDSocial+'" data-title="'+title+'" class="btn btn-mini btn-yellow CancelInvite" style="display:none;">Cancel Invite</a>');
                                 }

                                Tthis.dialog("close");
                                if(callingfor=='expert')
                                    $('#spcPeopleIcon'+uniqueIDSocial).html('<div class="inPeople"><a href="javascript:void(0);" data-title="'+title+'" data-id="'+uniqueIDSocial+'" class="viewexistExpert">View existing invite</a></div>');
                            }
                         $( ".ui-button-text-only" ).attr('disabled',false);   
                        }
                    });

                }
            }
        });

        console.log('shareType='+shareType+' uniqueIDSocial= '+uniqueIDSocial+' '+callingfor);
    });
    

    $('body').on('keyup', '.findsocialfriend', function(e) 
    {
        if (e.keyCode == 13) 
        {
            var type = $(this).attr('shareType');
            keywords = $('.findsocialfriend').val();
            if (keywords.length==0) 
                return false;
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {"type": type,'keywords':keywords,'callingfor':callingfor},
                url: BASE_URL + '/admin/social/socialsearch',
                beforeSend:function(){
                //$('#userInfoContainer').html('<div class="loader"></div>');
                $('#userInfoContainer').html('<div class="loaderOverlay2"></div>');            
                },
                success: function(response) 
                {
                    $('.userFatchList').remove();
                    $('.notfoundSocial').remove();
                    $('#userInfoContainer').html(response.UserData);
                    if(response.userCount==true)
                    {
                        $('.ui-dialog-buttonset').show();
                    }
                    else{
                        $('.ui-dialog-buttonset').hide();
                    }
                    if(callingfor =='expert')
                    {
                        $('#userInfoContainer').addClass('singleselectuser');
                    }
                    if(callingfor =='ForExpert')
                    {
                        $('#userInfoContainer').addClass('singleselectuser');
                    }
                }
            });
        }
    });


 $('body').on('click', '.CancelInvite', function(e)
    {       
           
            var uniqueIDSocial = $(this).attr('data-id');
            var title          = $(this).attr('data-title');

            msg ='Are you sure you want to cancel this invite?';
            $('#dialogConfirmSetting').remove();
            $('body').append('<div id="dialogConfirmSetting">'+msg+'</div>');

            $( "#dialogConfirmSetting" ).dialog({
            resizable: false,
            title:'Please confirm',
            modal: true,            
           buttons: {
           "Yes": function() {
           $( this ).dialog( "close" );

            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {"dbid": uniqueIDSocial},
                url: BASE_URL + '/admin/social/cnacelinvite',
                beforeSend:function(){
                            
                },
                success: function(response) 
                {
                  
                  
                    if(response.content==0)
                    {

                    if(localTick == false)
                    {                                  
                     //socket.emit('chkactivitynotification', 1,clientID);
                     socket.emit('enablesocial', 'CancelInvite',clientID);
                    }

                     $('#startbtnhangout'+uniqueIDSocial+' a').removeClass('disabled');
                     $('#startbtnhangout'+uniqueIDSocial+' a').attr('disabled', false);
                     $('#startbtnhangout'+uniqueIDSocial+' a').addClass('StartBroadcast');                     
                     $('#inviteexpertbtn'+uniqueIDSocial).html(' or <a href="javascript:void(0);"  data-uniqueValue ="'+uniqueIDSocial+'" class="btn btn-mini btn-yellow socialfriends" data-for ="ForExpert" data-type="InviteExpert" data-title="'+title+'" >Invite someone else</a>');
                     }else
                     {
                        $('#startbtnhangout'+uniqueIDSocial+' a').addClass('disabled');
                        $('#startbtnhangout'+uniqueIDSocial+' a').attr('disabled', true);
                        $('#startbtnhangout'+uniqueIDSocial+' a').removeClass('StartBroadcast');
                        $('#inviteexpertbtn'+uniqueIDSocial).html('');
                     }

                   
                }
            });
        }
         }
    });
       
    });

$('body').on('click', '.ViewInvite', function(e)
    {       
         var thisEl = $(this);
         var p = thisEl.closest('li');
            var uniqueIDSocial = $(this).attr('data-id');
            var name           = $(this).attr('data-name');
            var picval         = $(this).attr('data-pic');

            var pic            = IMGPATH+'/users/small/'+picval;

            msg ="<div class='follower-box'><div class='usImg'><strong id='headerImg'><img class='img border' height='30' align='left' src='"+pic+"' border='0' /></strong></div>&nbsp;&nbsp;<span class='oneline'> "+name+" </span></div> <br>";
            $('#dialogConfirmSetting').remove();
            $('body').append('<div id="dialogConfirmSetting">'+msg+'</div>');

            $( "#dialogConfirmSetting" ).dialog({
            resizable: false,
            title:'Invited user',
            width:400,
            modal: true,            
           buttons: {
           "Cancel Invite": function() {
           $( this ).dialog( "close" );

            $('.CancelInvite', p).click();
        }
         }
    });
       
    });
    
    $('body').on('click', '.viewexistExpert', function(e) 
    {
        var id = $(this).attr('data-id');
        var title = $(this).attr('data-title');
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {"dbid": id},
            url: BASE_URL + '/admin/social/checkexistexpert',
            success: function(response) 
            {
                 if (response.status == 'success') 
                { 
                    console.log(response);
                    var SocialTypeUserClass='';
                    var bgSocial='';
                    var parentClass='';
                    if(response.socialType=='linkedin'){
                        bgSocial = '#007ab9';
                        photo = response.userPhoto;
                        types = 'LinkedIn';
                    }
                    else if(response.socialType=='twitter'){
                        bgSocial = '#20b8ff';
                        photo = response.userPhoto;
                        types = 'Twitter';
                    }
                    else if(response.socialType=='facebook'){
                        bgSocial = '#3a589b';
                        photo = "https://graph.facebook.com/"+response.socialid +"/picture";
                        types = 'Facebook';
                    }else{
                        photo = response.userPhoto;
                        parentClass  = ' dbeeuser ';
                        types = 'Platform';
                    }
                    var resultData = '<div class="signWithSMContainer '+parentClass+' videoB"><h3>Invited from '+types+'</h3>\
                    <div class="signwithSprite"></div>\
                    <div class="signTittleSocialM" id="socialCntTxt">\
                        <div class="cellRow">\
                            <div class="cellTd cellIMg"><img src="'+photo+'"></div>\
                            <div class="cellTd cellUserName">'+response.userName+'</div>\
                        </div>\
                    </div>\
                    </div>';
                    $('#detailsDialog').remove();
                    var htmlLightbox = '<div id="detailsDialog">\
                      <div id="userInfoContainer"></div></div>';
                    $('body').append(htmlLightbox);
                    $('#detailsDialog').hide();
                    $("#detailsDialog").dialog({
                        dialogClass: 'detailsDialogBox',
                        width: 300,
                        title: ' <span style="color:#FF8C03;">' + title + '</span>',
                        open: function() 
                        {
                            $fluidDialog();
                            $('#userInfoContainer').html(resultData);
                            $('#detailsDialog').show();
                        },
                        buttons: {
                            "Cancel Invite": function() 
                            {
                                $.ajax({
                                    type: "POST",
                                    dataType: 'json',
                                    data: {"dbid": id},
                                    url: BASE_URL + '/admin/social/cancelpending',
                                    success: function(response) 
                                    {
                                        window.location.reload();
                                    }
                                });
                            }
                        }
                    });
                }
            }
        });
    });

    $('body').on('click', '.singleselectuser .labelCheckbox input', function() 
    {
         $(this).closest('.singleselectuser').find('.labelCheckbox input').attr('checked', false);
         $(this).attr('checked', true);
    });

    

});


