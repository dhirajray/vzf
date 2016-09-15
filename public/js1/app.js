var socialfacebook = false;
var sociallinkedin = false;
var socialtwitter = false;
var counter       = 0;
var max_socket_reconnects = 6;
var controlname = '';
var QASTART = 0;
var QAEND = 0;
var QASTARTINT;
var QAENDINT;
$(function() {
    controlname = $('#startdbHeaderBtn').attr('controlname');
    socket = io.connect('https://secure.onserro.com:5000',{'forceNew':true,
        'max reconnection attempts' : max_socket_reconnects,
        secure: true,'reconnect': true});
    // on connection to server, ask for user's name with an anonymous callback
    socket.on('connect', function(){
        // call the server-side function 'adduser' and send one parameter (value of prompt)
        socket.emit('adduser', SESS_USER_ID,clientID);
    });
    socket.on('reconnect', function() {
     console.log('my connection has been restored!');
    } ); 
    // listener, whenever the server emits 'updatechat', this updates the chat body
    socket.on('initNodes', function (data) {
        $("#mainMsgInput").css("display" , "inline");
    });
    
    socket.on('updateNodes', function (data) {
        $("body").append(data);
    });
    socket.on('loadComment', function (commentID,dbeeid,domain_id) 
    {
        if((controlname=='dbeedetail' || controlname=='Dbeedetail') && PAGE_DBEE_ID == dbeeid)
          loadComment(commentID,'push',dbeeid);
        
    });
    
    socket.on('videoeventstart', function (data,domain_id) {
        dbid = $('#dbid').val();
        videoStartStatus = true;
        console.log(dbid+'=='+data);
        if(dbid==data){
            play_clicked();
        }
    });

    socket.on('QAstart', function (data,domain_id) 
    {
        dbid = $('#dbid').val();
        if(dbid==data && QASTART==0)
        {
            $('.noMoreComments').html('Q&A is now live.');
            getexpertAsk(dbid);
            clearInterval(QASTARTINT);
            QASTART =1;
            QAENDINT = setInterval("checkEndQA('"+dbid+"')", 1000);
        }
    });

     socket.on('QAend', function (data,domain_id) 
    {
        dbid = $('#dbid').val();
        if(dbid==data && QAEND ==0)
        {
            $('.noMoreComments').html('This Q&A is now closed.');
            getexpertAsk(dbid);
            QAEND = 1;
            clearInterval(QAENDINT);
        }
    });


      socket.on('makevip', function (data,domain_id) {
       
        if(data==SESS_USER_ID)
        {

            $dbConfirm({
                    content: "Your account has been given VIP status. Click 'OK' to log out and log in again to refresh your session.",
                    yesClick: function() {
                         gologout();
                    },
                    no: false,
                    
                    yesLabel: 'OK'
                
            });
            
        }
        
    });

    socket.on('testvideo', function (data,domain_id) {
        dbid = $('#dbid').val(); 
        if(adminID==SESS_USER_ID)
        {
            var tothiglight = 1;
            var data = "type=all&higlight="+tothiglight;
            $.ajax({
                type: "POST",
                dataType: 'html',
                data: data,
                url: BASE_URL + '/notification/displayactivitynotification',               
                beforeSend: function() {                  
                    $('#actNotification').html('');
                    $dbLoader('#actNotification', 3, '', 'center');
                },
                cache: false,
                success: function(response) 
                {
                    $('#actNotification').html(response);                   
                    storeNotifications = $('#actNotification').html();
                    storeNotifications = false;                   
                }
            });
        }

        if(dbid==data)
            playVideo();
    });

    
    socket.on('getexpert', function (db,domain_id,expert_id) { 
        getexpert(db,expert_id);
    });
    
    socket.on('refreshtweet', function (db) { 
        refreshtweet(db);
    });

    socket.on('promotedexpert', function (domain_id,expert_id,id) { 
        if(expert_id==SESS_USER_ID){
            getpromotedquestion(id,'socket');
        }
    });

    socket.on('removetweet', function (id) { 
        $('#tweet_'+id).hide();
    });
    socket.on('getexpertremove', function (db,domain_id,expert_id) {  
        getexpertremove(db,expert_id);
    });

    socket.on('askquestion', function(id, domain_id,action,dbeeid){
        dbid = $('#dbid').val();
        if(clientID==domain_id && dbid == dbeeid)
        { 
            $('.errorWarning').remove();
            if(action=='hide')
            { 
                $('#askquestion-'+id).addClass('disabled disabledQuestion');
                $('#askquestion-'+id).attr('title','Disabled');
            }
            else
            {
                $('#askquestion-'+id).attr('title','');
                $('#askquestion-'+id).removeClass('disabled disabledQuestion');
            }
        }
    });

    socket.on('askexpert', function (db,domain_id,QAID,expertID,refer) {
        dbid = $('#dbid').val();
        if(dbid==db)
        {
            if(!$('li a').hasClass('Pendingquestion'))
            { 
                postclick = 0;
                if(!$('ul').hasClass('tabLinks'))
                    postclick = 1;
                showQaTab('pending','none');
                setTimeout(function() 
                {
                    if(postclick==1)
                        $('.ShowMycomment').trigger('click');
                }, 3000);
            }
            else if($('.Pendingquestion').hasClass('active'))
                pushNewQuestion(db,QAID);
            else
            {
               preCount = $.trim($('#pendingQACount').html());
               if(preCount=='')
                    preCount = 0;
               count = parseInt(parseInt(preCount)+1);
               $('#pendingQACount').text(count).css({display:'inline-block'});
            }
        }
    });

    socket.on('livequestion', function (db,domain_id,cmntownerId,dbeeowner,QAID) {
        dbid = $('#dbid').val();
        profileuser = $('#profileuser').val();
        if(dbid==db && dbeeowner==profileuser)
        {
            if(!$('li a').hasClass('ShowMyquestion'))
                showQaTab('liveqa','none');  
            else if($('.ShowMyquestion').hasClass('active'))
                pushNewQA(db,QAID);
            else
            {
               preCount = $.trim($('#LiveQACount').html());
               if(preCount=='')
                    preCount = 0;
               count = parseInt(parseInt(preCount)+1);
               $('#LiveQACount').text(count).css({display:'inline-block'});
            }
        }
        else if(profileuser==cmntownerId && !$('.Myquestion').hasClass('active'))
        {
           preCount = $.trim($('#myQACount').html());
           if(preCount=='')
                preCount = 0;
           count = parseInt(parseInt(preCount)+1);
           $('#myQACount').text(count).css({display:'inline-block'});
        }
        else if(profileuser==cmntownerId && $('.Myquestion').hasClass('active')==true)
           $('.Myquestion').trigger('click');
        
    });
    

    socket.on('makelive', function (db,domain_id,QAID) 
    {
        dbid = $('#dbid').val();
        if(dbid==db)
        {
            if(!$('li a').hasClass('publicQa'))
                showQaTab('makelive','none');  
            if($('.publicQa').hasClass('active')){
                pushMakeLiveQA(db,QAID); 
            }
            else
            {
               preCount = $.trim($('#publicQaCount').html());
               if(preCount=='')
                    preCount = 0;
               count = parseInt(parseInt(preCount)+1);
               $('#publicQaCount').text(count).css({display:'inline-block'});
            }
        }
    });

    socket.on('removeexpert', function (data,domain_id,expert_id) { 
        db = $('#dbid').val();
        if(db==data)
            getexpert(dbid,expert_id);
    });
    
    socket.on('checkdbee', function (data,domain_id){ 
        if(controlname=='myhome' || controlname=='Myhome')
        {
            checkdbeestatus();
        }
    });

    socket.on('enablesocial', function (data,domain_id) 
    {

        $('.dbConfirmOverlay').fadeOut('slow', function() 
        {
            $(this).remove();
        }); 

        switch(data) 
        {
            case 'CancelInvite':

               var tothiglight = 1;
               var data = "type=all&higlight="+tothiglight;
                $.ajax({
                    type: "POST",
                    dataType: 'html',
                    data: data,
                    url: BASE_URL + '/notification/displayactivitynotification',               
                    beforeSend: function() {                  
                        $('#actNotification').html('');
                        $dbLoader('#actNotification', 3, '', 'center');

                    },
                    cache: false,
                    success: function(response) {
                        $('#actNotification').html(response);                   
                        storeNotifications = $('#actNotification').html();                   
                    }

                });
            break;
            case 'allowedPostWithTwitter':
                content = "Platform users can now post with Twitter #tags. Click 'OK' to reload this page.";
                break;
            case 'allow_admin_post_live':
                content = "Admin has turned on post verification. Click 'OK' to refresh your session.";
                break;
            case 'gpsocialsignin':
                content = "You can now connect your Google+ account in 'Link Social Accounts'.";
                break;
            case 'allsocialsignin':
                $("#socialConnect").css("display", "block");
            $(".socialfriends").css("display", "block");
            $("#linkedinGroup").css("display", "block");
            content = "All social linking and connections have been enabled by the platform admin. Click 'OK' to reload this page.";
                break;
            case 'twsocialsignin':
                CheckSocialLinkingStatus();
            content = "You can now connect your Twitter account in 'Link Social Accounts'.";
                break;
            case 'ldsocialsignin':
                
            CheckSocialLinkingStatus();
            content = "You can now connect your LinkedIn account in 'Link Social Accounts'.";
                break;
            case 'fbsocialsignin':
               
            CheckSocialLinkingStatus();      
             content = "You can now connect your Facebook account in 'Link Social Accounts'.";
                break;
            case 'Profanityfilter':
                content = "Admin has now turned on the profanity & content restriction filter. Click ‘OK’ to refresh your session";
                break;
            case 'social':
                
                content = "All social sharing and connections have been enabled by the platform admin.";
                break;
            case 'allow_user_create_polls':
                
            content = "Admin has enabled the ability for users to create polls. Click 'OK' to refresh your session.";
                break;
            case 'ShowLiveVideoEvent':
                
                content = "Admin has turned on Live video broadcasts. Click 'OK' to refresh your session.";
                break;
            case 'ShowVideoEvent':
                
               content = "Admin has turned on video events. Click 'OK' to refresh your session.";
                break;
            case 'ShowRSS':
                
               content = "Admin has turn on RSS. Click 'OK' to refresh your session.";
                break;
            case 'showAllUsers':
                
                 $('#alluserslist').show();
               content = "";
                break;
            case 'scoringoff':
                
                content = "Admin has turned on platform scoring. Click 'OK' to refresh your session.";
                break;
            case 'userconfirmed':
                
                 content = "Admin has turned on non-activated user search display. Click 'OK' to refresh your session.";
                break; 
            case 'event':
                
                content = "Admin has turned on live events. Click 'OK' to refresh your session.";
                break; 

            case 'allowmultipleexperts':

                content = "Admin has now turned on the ability to invite multiple experts to a post. Click ‘OK’ to refresh your session.";
                break; 
            case 'allowexperts':

                content = "Admin has now turned on the ability to ask questions to experts. Click ‘OK’ to refresh your session.";
                break; 
        }
        
        if(content!='' && data!='showAllUsers' && content!='[object Window]')
        {
             $dbConfirm({
                    content: content,
                    yesClick: function() {
                        window.location.reload();
                    },
                    no: false,
                    
                    yesLabel: 'OK'
                //noLabel: 'Close'
            });
        }
    });
    
   

    socket.on('disablesocial', function (data,domain_id) {
        //closeConfirmboxRaj();
        $('.dbConfirmOverlay').fadeOut('slow', function() {
                $(this).remove();
            });
        
        switch(data) 
        {
            case 'allowexperts':

                content = "Admin has now turned off the ability to ask questions to experts. Click ‘OK’ to refresh your session.";
                break; 

            case 'allowmultipleexperts':

            content = "Admin has now turned off the ability to invite multiple experts to a post. Click ‘OK’ to refresh your session.";
                break;

            case 'allow_admin_post_live':
                content = "Admin has turned off post verification. Click 'OK' to refresh your session.";
                break;
            case 'gpsocialsignin':
                content = "Google Plus linking and connection has been disabled by the platform admin. Click 'OK' to reload this page.";
                break;
            case 'allsocialsignin':
                $("#socialConnect").css("display", "none");
                $(".socialfriends").css("display", "none");
                $("#linkedinGroup").css("display", "none");
                content = "All social linking and connections have been disabled by the platform admin. Click 'OK' to reload this page.";
                break;
            case 'twsocialsignin':
                CheckSocialLinkingStatus();
            content = "Twitter linking and connection has been disabled by the platform admin. Click 'OK' to reload this page.";
                break;
            case 'ldsocialsignin':
                
            CheckSocialLinkingStatus();
            content = "LinkedIn linking and connection has been disabled by the platform admin. Click 'OK' to reload this page.";
                break;
            case 'fbsocialsignin':
               
            CheckSocialLinkingStatus();      
            content = "Facebook linking and connection has been disabled by the platform admin. Click 'OK' to reload this page.";
                break;
            case 'Profanityfilter':
                content = "Admin has now turned off the profanity & content restriction filter. Click ‘OK’ to refresh your session";
                break;
            case 'social':
                if(Socialtype==0){
                    content = "All social sharing and connections are now disabled.";
                }
                else
                { 
                    $dbConfirm({
                    content: "Social login has been disabled by the platform administrator. You will now be logged out.",
                    yesClick: function() {
                        window.location.href = BASE_URL+"/myhome/logout";
                    },
                    no: false,
                    yesLabel: 'OK'
                    });
                    return false;
                }
                break;
            case 'allow_user_create_polls':
                
                 
           content = "Admin has removed the ability for users to create polls. Click 'OK' to refresh your session.";
                break;
            case 'ShowLiveVideoEvent':
                
                content = "Admin has turned off Live video broadcasts. Click 'OK' to refresh your session.";
                break;
            case 'ShowVideoEvent':
                
               content = "Admin has turned off video events. Click 'OK' to refresh your session.";
                break;
            case 'ShowRSS':
                
                content = "Admin has turn off RSS. Click 'OK' to refresh your session.";
                break;
            case 'showAllUsers':
                
                $('#alluserslist').hide();
                content = "";
                break;
            case 'scoringoff':
                
                content = "Admin has turned off platform scoring. Click 'OK' to refresh your session.";
                break;
            case 'userconfirmed':
                
                content = "Admin has turned off non-activated user search display. Click 'OK' to refresh your session.";
                break; 
            case 'event':
                
                content = "Admin has turned off live events. Click 'OK' to refresh your session.";
                break;   
        }
         
        if(data!='CancelInvite' && content!='' && content!='[object Window]' && data!='showAllUsers') 
        {
             $dbConfirm({
                content: content,
                yesClick: function() {
                    window.location.reload();
                },
                no: false,
                
                yesLabel: 'OK'
                //noLabel: 'Close'
            });
       }

    });

     socket.on('disconnectsocial', function (data,domain_id) {
        if(data=='facebook')
            socialfacebook = true;
        if(data=='linkedin')
            sociallinkedin = true;
        if(data=='twitter')
            socialtwitter = true

    });

    socket.on('checkcomment', function (db,domain_id) 
    {
        if($('.ShowMycomment').hasClass('active'))
            checknewcomments(db);
        else if(!$('li a').hasClass('ShowMycomment'))
            checknewcomments(db);
        else
        {
           preCount = $.trim($('#LivePostCount').html());
           if(preCount=='')
                preCount = 0;
           count = parseInt(parseInt(preCount)+1);
           $('#LivePostCount').text(count).css({display:'inline-block'});
        }
    });

    socket.on('chkactivitynotification', function (db,domain_id) {
        //chkactivitynotification(1);
        if(adminStatus==true)
            ajaxnotification();
        else
            chkactivitynotification(1);
    });
    socket.emit('refresh');
    socket.on('makeprivatetask', function (data,domain_id) { //alert(4);
        location.reload();
    });
});
function callServer(){
    var all= '<br/>' + name + ' : ' + $('#msg').val(); 
    socket.emit('chat', all); $('#msg').val('');
    return false;
}
