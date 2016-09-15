var windW = screen.width;

var Notification;
if(isMobile==null){
var window_focus = true;
var Notification = window.Notification || window.mozNotification || window.webkitNotification;
if(typeof Notification !='undefined'){
    Notification.requestPermission(function (permission) {});
    }
}

var allUserSelectJson =  [];
var myChatGroups =[];

var fileUploadingJson = [];
var allUserTitle = ['<li class="itsMyGroup itsSeprator"><span>Created Groups</span></li>', '<li class="itsOtherGroup itsSeprator"><span>My Groups</span></li>', '<li class="itsAllUsers itsSeprator"><span>All Users</span></li>'];
var groupLeaveMsg = 'Are you sure you want<br>to leave this Group?';
var leaveGroupCloseIcon  = '<a href="javascript:void(0);" class="deleteMyGroupChat circleBtn" title="Leave Group"><i class="fa fa-times"></i></a>';

    var totalChatWindow = 0;
    var pRight =0;
    var lastChatWindowPosition =0;
     var apptr   = '';

var arrayEmotions= [
                    {value:':)', replaceValue:"smile", title:"Smile"},
                    {value:':(', replaceValue:"sad", title:"sad"},
                    {value:':\'(', replaceValue:"crying", title:"Crying"},
                    {value:':-O', replaceValue:"surprise", title:"Surprised"},
                    {value:';)', replaceValue:"winking", title:"Winking"},
                    {value:';)', replaceValue:"winking", title:"Winking"},
                    {value:':p', replaceValue:"tongueOut", title:"Tongue out"},
                    {value:':-S', replaceValue:"confused", title:"Confused"},
                    {value:':@', replaceValue:"angry", title:"Angry"},
                    {value:':-#', replaceValue:"dontTellAnyone", title:"Don't Tell Anyone"},
                    {value:'l-)', replaceValue:"sleepy", title:"Sleepy"},
                    ];
function convertTolink (text){
    var myRegExp = /(https?:\/\/[^\s]+)/g;    
    //var myRegExp = /(https?:\/\/[^\s]+)/g;    
    var mailReg = /\S\w+@+\S\w+\.\w+\S/g;
     var httpCheck = /:\/\/.\w\S+/g;
     var wwwURL = /www.\w\S+/g;
     var  text = text;
    if(myRegExp.test(text)){
         text =   text.replace(myRegExp, function(url) {
        return '<a href="' + url + '" target="_blank">' + url + '</a>';
        });
     }
    
    if(httpCheck.test(text)==false){
         text =   text.replace(wwwURL, function(url) {
        return '<a href="http://' + url + '" target="_blank">' + url + '</a>';
        });
    }

     if(mailReg.test(text)){
         text =   text.replace(mailReg, function(url) {
        return '<a href="mailto:' + url + '" title="mailto:'+url+'" target="_blank">' + url + '</a>';
        });
     }



     return text;
}
function emotions(text){    

    $.each(arrayEmotions, function (index, value){
      text = text.replace(value.value, '<span class="chatEmotions emo_'+value.replaceValue+'" title="'+value.title+'">'+value.value+'</span>');
      
    }); 
    text =convertTolink(text);
     return text;
    //return text;  
}

function addUsersInChatGroups(action, groupId, groupBame){
    if(action=='add'){
     myChatGroups.push({groupId:groupId, groupName:groupBame});
    }else{
       $.each(myChatGroups, function (i, value){            
                if(groupId==value.groupId){
                    myChatGroups.splice(i, 0);
                }
           
       });
    }
     //console.log(myChatGroups);
   
     

}


function senderLayout(thisEl, text, json){    
   today = new Date();
   var lastModified='';
   var fileName='';
   if(thisEl.hasClass('childChatWindow')==false){
     var chatWindow = thisEl.closest('.childChatWindow');
  }else{
   chatWindow = thisEl;
  }
  if(typeof json !='undefined'){
    if(typeof json.lastModified !='undefined'){
         lastModified = 'last-modified="'+json.lastModified+'"';
    }
     if(typeof json.fileName !='undefined'){
       fileName = 'file-name="'+json.fileName+'"';
    }
  }
    var chatTime = today.format("HH:MM");
    if(fileName==''){
        var text =  emotions(text);
    }
    //var myRegExp = /(http(s)?:\\)?([\w-]+\.)+[\w-]+[.com|.in|.org]+(\[\?%&=]*)?/g;
   


      var msgTemplate = '<li class="sender" '+lastModified+' '+fileName+'>\
            <div class="textMsg">\
                '+text+'\
                <div class="msgTime">'+chatTime+'</div>\
            </div>\
            <img src="'+BASE_URL+userImagePath+userpic+'" class="userImg">\
        </li>';

        $('.chatMessageContainer ul' , chatWindow).append(msgTemplate);
        var sh = $('.chatMessageContainer' , chatWindow).prop('scrollHeight');
        $('.chatMessageContainer' , chatWindow).scrollTop(sh);
        if(thisEl.hasClass('childChatWindow')==false){
          thisEl.val('').removeAttr('style');
        }
}

function getdecodename(enName)
{
    if(enName=='') return false;

    expl = enName.split("nqebnzobg");
    
    originalString = '';
    
    for(var item in expl) {
      originalString += expl[item]+' ';
    }
    //console.log(originalString)
    //foreach ($expl as $key => $value) originalString .= $value.' '; 
    
    retuOrg  =  originalString.split("mabirdnny");
    
    return (retuOrg[0] + '')
    .replace(/[a-z]/gi, function(s) {
      return String.fromCharCode(s.charCodeAt(0) + (s.toLowerCase() < 'n' ? 13 : -13));
    });

    return enName;
}

function changeTitle() {
    var title = $(document).prop('title'); 
    if (title.indexOf(">>>") == -1) {
        setTimeout(changeTitle, 3000);  
        $(document).prop('title', '>'+title);
    }
}



$(function (){

    var chatLimit = 20;
    if(SESS_USER_ID=='') return false;


    
    if(SERVER_NAME=='localhost') var socket = io.connect('http://localhost:4000');
    else var socket = io.connect('https://securedev.db-csp.com:5000',{'forceNew':true,
        'max reconnection attempts' : max_socket_reconnects,
        secure: true,'reconnect': true});

    /*socket.on('connectedPorts',function(data){

        socket.emit('connectingPorts', SESS_USER_ID, clientID);
    })

    socket.on('connectedPorts',function(data){
        console.log(data.date)
    })*/

    socket.on('connectedPorts',function(data){
        
        socket.emit('connectingPorts', SESS_USER_ID, clientID);
    })

    socket.on('connect', function(){   
        socket.emit('showallusers', SESS_USER_ID, clientID, SESS_USER_NAME);
    })

    socket.on('initial notes', function(users,senderID){
        //console.log(users)
        if(SESS_USER_ID==senderID)
        {
            var totuser = Object.keys(users).length;

            //alert(totuser)
            apptr = '';
            apptrMyGroup = '';
            apptrOtherGroup = '';
            
            if(totuser>0)
            {
                //apptr += '<tr> <td> ID </td> <td> Name </td><td> Status </td><td> Action </td> </tr>'; 
                
                mylistusers =[];
                for(var i=0; i<totuser;i++)
                {
                    groupOwner = ''
                    isAccepted = ''
                    ownerName = ''
                    ownerPic  = ''                    
                    noUserFound  = ''  ;                 
                    userID = ''; userName = ''; Astatus = '';picture=''; 
                    chatSource    = 'user'
                    var userOneStatus    = ''
                    if( users[i].groupid == undefined)
                    {
                        if(users[i].sendto==SESS_USER_ID)
                        {
                            userID      = users[i].UserID;
                            userName    = getdecodename(users[i].full_name);
                            picture     = users[i].ProfilePic

                            if(users[i].chatstatus==1) {
                                if(users[i].isonline==1) { Astatus = 'online';} else {Astatus = 'offline';}
                            } else Astatus = 'offline';

                            mylistusers.push({'userID':userID , 'userName':userName, 'Astatus':Astatus})
                        }

                        if(users[i].sendby==SESS_USER_ID)
                        {
                            userID      = users[i].flwUserID;
                            userName    = getdecodename(users[i].uflwfull_name);
                            picture     = users[i].flwProfilePic

                            if(users[i].flwchatstatus==1) {
                                if(users[i].flwonline==1) Astatus = 'online'; else Astatus = 'offline';
                            } else Astatus = 'offline';
                            
                            mylistusers.push({'userID':userID , 'userName':userName, 'Astatus':Astatus})
                        }
                        

                    }
                    else
                    {
                        userID      = users[i].groupid;
                        userName    = users[i].groupname;
                        picture     = 'chat-group.png' 
                        chatSource    = 'group'

                        Astatus = 'online'
                        //groupOwner  =   (users[i].owner==SESS_USER_ID) ? users[i].owner : '0'
                        groupOwner  =    users[i].owner
                        isAccepted  =    users[i].isaccepted
                        ownerName   =    getdecodename(users[i].full_name);
                        ownerPic    =    users[i].ProfilePic

                        mylistusers.push({'userID':userID , 'userName':userName, 'Astatus':Astatus});
                    }

                    if(chatSource!='group'){    
                        allUserSelectJson.push({id:userID,text:userName,src:picture});
                       
                         apptr +='<li data-id="'+userID+'" is-accepted="'+isAccepted+'" group-owner="'+groupOwner+'"  chat-source="'+chatSource+'" data-pic="'+picture+'">\
                            <div class="chatUserPic">\
                                <span class="liveStatus '+Astatus+'"></span>\
                                <img height="32" src="'+BASE_URL+userImagePath+picture+'"  />\
                            </div>\
                            <div class="chatUserDetails">\
                                <div class="oneline Uname">'+ userName+'</div>\
                            </div>\
                        </li>';

                    }else{
                        if(groupOwner==SESS_USER_ID){

                            //addUsersInChatGroups('add', userID,userName);
                             apptrMyGroup +='<li data-id="'+userID+'" is-accepted="'+isAccepted+'" group-owner="'+groupOwner+'"  chat-source="'+chatSource+'" data-pic="'+picture+'">\
                                <div class="chatUserPic">\
                                    <img height="32" src="'+BASE_URL+userImagePath+picture+'"  />\
                                </div>\
                                <div class="chatUserDetails">\
                                    <div class="oneline Uname">'+ userName+'</div>\
                                </div>\
                               '+leaveGroupCloseIcon+'\
                            </li>';

                        }else{                            
                             apptrOtherGroup +='<li data-id="'+userID+'" group-owner-name="'+ownerName+'" group-owner-pic="'+ownerPic+'" is-accepted="'+isAccepted+'" group-owner="'+groupOwner+'"  chat-source="'+chatSource+'" data-pic="'+picture+'">\
                            <div class="chatUserPic">\
                                <img height="32" src="'+BASE_URL+userImagePath+picture+'"  />\
                            </div>\
                            <div class="chatUserDetails">\
                                <div class="oneline Uname">'+ userName+'</div>\
                            </div>\
                            '+leaveGroupCloseIcon+'\
                        </li>';
                        }

                    }

                   
                   
                }
              

            }
            else
            {

                apptr += '<div class="nouserchatlist"><span class="noUserIcon"><i class="fa fa-user"></i></span><div class="Cnt">You currently have no connections <br>Connections are defined by users who follow one another.</div></div>'; 
               noUserFound = 'noUserFound';
            }
            var myGroupList = '';
            var normalUserList = '';
             var otherGroupList = '';
            if(apptrMyGroup!=''){
                myGroupList =  allUserTitle[0] //'<li class="itsMyGroup itsSeprator"><span>Created Groups</span></li>';
            } 
            if(apptrOtherGroup!=''){
                otherGroupList =  allUserTitle[1] // '<li class="itsOtherGroup itsSeprator"><span>My Groups</span></li>';
            }
           
            if(apptr!=''){
                normalUserList = allUserTitle[2]//'<li class="itsAllUsers itsSeprator"><span>All Users</span></li>';
            }
            $.allUserChat({content:'<ul class="allUserList">'+myGroupList+apptrMyGroup+otherGroupList+apptrOtherGroup+normalUserList+apptr+'</ul>', class:noUserFound, allUser:true});
        }


    })

    socket.on('appendBothFollow', function(userInfo){
        
       // console.log(userInfo)
        if(SESS_USER_ID==userInfo.senderID)
        {
            console.log('userInfo')
            apptr = ''
            firstAppend = ''
            userID      = userInfo.recieverID;
            userName    = userInfo.full_name;
            picture     = userInfo.ProfilePic
            ownerName = ''
            ownerPic=''
            var btnCloseGrpIcon =''
            var userOneStatus = '';
            if(userInfo.chatsource=='group')
            {
                Astatus = '';
                chatsource  =   userInfo.chatsource
                firstAppend =   'newGroup' 
                //groupOwner  =   (userInfo.owner==SESS_USER_ID) ? userInfo.owner : '0'
                groupOwner  =   userInfo.owner
                isAccepted  =   (userInfo.owner==SESS_USER_ID) ? 1 : '0';
                btnCloseGrpIcon =leaveGroupCloseIcon;
                ownerName   =    'group-owner-name="'+userInfo.groupOwnerName+'"'; 
                ownerPic    =   'group-owner-pic="'+userInfo.groupOwnerPic+'"';
            }
            else
            {
                if(userInfo.olStatus==1)  Astatus     = 'online'; else Astatus     = 'offline';
                chatsource = 'user'
                groupOwner = ''
                isAccepted = ''
                userOneStatus = '<span class="liveStatus '+Astatus+'"></span>';
            }
            
            //console.log(mylistusers)


            apptr +='<li data-id="'+userID+'" is-accepted="'+isAccepted+'" '+ownerPic+' '+ownerName+' group-owner="'+groupOwner+'" chat-source="'+chatsource+'" data-pic="'+picture+'" >\
                <div class="chatUserPic">\
                   '+userOneStatus+'\
                    <img height="32" src="'+BASE_URL+userImagePath+picture+'"  />\
                </div>\
                <div class="chatUserDetails">\
                    <div class="oneline Uname">'+userName+'</div>\
                </div>'+btnCloseGrpIcon+'\
            </li>';
            //alert(userInfo.olStatus)
             if(userInfo.olStatus==1){
                $('.nouserchatlist').hide();
                $.allUserChat({userId:userID, remove:true});
                $.allUserChat({content:apptr, prepend:true, type:chatsource, groupOwnerId:groupOwner});
              
                $(window).trigger('resize');
                 if(userInfo.chatsource=='group'){
                    //addUsersInChatGroups('add', userID, userName);
                    $('.dbchatWrapper.allUserChat .chatCancelGroupBtn').trigger('click');
                    if(groupOwner!=SESS_USER_ID && isMobile==null){
                        $('.dbchatWrapper.allUserChat .allUserList li[chat-source="group"][data-id="'+userID+'"]').trigger('click');
                    }
                 }else{
                     allUserSelectJson.push({id:userID,text:userName,src:picture});
                 }

             } else{

                /*for (var key in allUserSelectJson) {
                    if (allUserSelectJson.hasOwnProperty(key)) {
                        delete allUserSelectJson[key]
                    }
                }*/
                $.allUserChat({userId:userID, remove:true});
             }

        }


    })

    

     // Dbee chating started

    socket.on('chatstart', function(chatval){
        
       //console.log(chatval.fileJson)
      
        if(chatval.recieverID==SESS_USER_ID || chatval.senderID==SESS_USER_ID)
        {
            chatResAppend = '';
            popupClass = 'popupwindow_'+clientID+'_'+chatval.recieverID+'_'+chatval.senderID;

            textAreaID = 'mychattext_'+clientID+'_'+chatval.senderID+'_'+chatval.recieverID;
            
            //alert(popupClass)
            alOpen = $(".dbchatWrapper[chat-type='"+chatval.senderID+"']").length;


           if(typeof chatval.noInsert!='undefined'){
                if(chatval.noInsert=='success'){
                     return false;
                }
           }
            //console.log(chatval)
            if(alOpen == 0 && isMobile==null){
                 $(".dbchatWrapper[chat-type='alluser'] li[data-id='"+chatval.senderID+"'][chat-source='user']").trigger('click');
            }
            var SenderType='';
            if(chatval.senderID==SESS_USER_ID){
                
                   SenderType = 'sender';
            }

            chatline = chatval.mychat;


            today = new Date();
            var fileJson='';
            var lastModified ='';
            var fileNameOnLi ='';
            var uploadProgress ='';
            var fileStatus ='';
             var img = $(".childChatWindow[chat-type='"+chatval.senderID+"']").attr('data-pic');
            var userName = $(".childChatWindow[chat-type='"+chatval.senderID+"'] .unameTitle strong").text();
            var userpicsUrl = BASE_URL+userImagePath+img;  
            var notiText  = '';  

            if(typeof chatval.fileJson !='undefined'){
               
               lastModified= 'last-modified="'+ chatval.fileJson.lastModified+'"';
              var lastModified2= chatval.fileJson.lastModified;
              var fileName= chatval.fileJson.fileName;
               fileNameOnLi= 'file-name="'+ fileName+'"';
              var fileSizeMB= chatval.fileJson.fileSizeMB;
               uploadProgress= Math.floor(chatval.fileJson.uploadProgress);
               fileStatus= chatval.fileJson.status;
               if(uploadProgress!=''){
                      $('.childChatWindow[chat-type="'+chatval.senderID+'"] [last-modified="'+lastModified2+'"] .chatDownIcon').attr('upload-progress', uploadProgress+'%');
               }

               if(fileStatus=='success')
               {
                  var filefromPHP = chatval.fileJson.fileNameResponse.split('~');   
                  var downLoadIcon = '<span class="chatDownIcon"><i class="fa fa-download"></i></span>';
                  $('[last-modified="'+lastModified2+'"] .chatUploadThumb .chatDownIcon').replaceWith(downLoadIcon);
                  $('[last-modified="'+lastModified2+'"] .chatUploadThumb .uploading').removeClass('uploading');
                  var copyContent=  $('[last-modified="'+lastModified2+'"] .chatUploadThumb').html();
                  var fileTypeThumb = 'fileTypeThumb';
                  if($('[last-modified="'+lastModified2+'"] .chatUploadThumb').hasClass('fileTypeThumb')==false){
                    fileTypeThumb='';
                    var notiMSG = 'file shared with you'+fileName;                   
                  }
                  
                  var cnt = '<a href="'+BASE_URL+'/dashboarduser/downloadpdfuser/pdf/'+filefromPHP[0]+'/orname/'+filefromPHP[1]+'/isf/im" class="itsFileLink '+fileTypeThumb+'">'+copyContent+'</a><span class="fileNameShow">'+fileName+' <br>'+fileSizeMB+'</span>';
                  $('[last-modified="'+lastModified2+'"] .chatUploadThumb').replaceWith(cnt);
                  if(chatval.recieverID==SESS_USER_ID && isMobile==null){
                    $.notification({
                        title:userName,
                        text:notiMSG,
                        icon:userpicsUrl
                    });
                }

                  if(chatval.senderID==SESS_USER_ID) {
                    socket.emit('my chat',  {myname:SESS_USER_NAME, mychat :cnt,orgfilename : filefromPHP[1],clientID : clientID, recieverID:chatval.recieverID,senderID:chatval.senderID, noInsert:'success', userPicture : userpic});
                    
                    

                  } 
                  
                }

                if(fileStatus=='cancelFile'){
                    $('[last-modified="'+lastModified2+'"] .textMsg').text('File transfer cancelled');
                   

                }

                chatline2 = chatline;


            }else{
                 chatline2 = emotions(chatline);

            }

            
            var chatTime = today.format("HH:MM");
           
            //'+chatval.chatid+'
             
              if(typeof img  !='undefined' && typeof(chatval.fileJson) =='undefined' && isMobile==null){                     
                   $.notification({
                        title:userName,
                        text:chatline,
                        icon:userpicsUrl
                    }); 
               }

            chatResAppend = '<li class="'+SenderType+'"  '+lastModified+' '+fileNameOnLi+' >\
                <div class="textMsg">\
                    '+chatline2+'\
                    <div class="msgTime">'+chatTime+'</div>\
                </div>\
                <img src="'+BASE_URL+userImagePath+img+'" class="userImg">\
            </li>';

            $(".dbchatWrapper[chat-type='alluser'] li[data-id='"+chatval.senderID+"']").removeClass('heightLightChat');
            $(".childChatWindow[chat-type='"+chatval.senderID+"']").removeClass('heightLightChat');

            if( $(".childChatWindow[chat-type='"+chatval.senderID+"']").hasClass('imOnIt')==false && fileStatus!='success'){
                if(chatval.recieverID==SESS_USER_ID){
                    //if(PageTitleNotification.Vars.Interval==null){
                        //PageTitleNotification.On("New chat message!");
                    //}
                 }

                
              
                $(".dbchatWrapper[chat-type='alluser'] li[data-id='"+chatval.senderID+"']").addClass('heightLightChat');
                $(".childChatWindow[chat-type='"+chatval.senderID+"'] ").addClass('heightLightChat');
                var totalPing =   $(".dbchatWrapper[chat-type='alluser'] li[data-id='"+chatval.senderID+"'].heightLightChat").size();
                if(totalPing!=''){
                     $(".childChatWindow .backToUserList, .chatStartNow").addClass('heightLightChat');
                }else{
                    $(".childChatWindow .backToUserList, .chatStartNow").removeClass('heightLightChat');
                }
               
                if($("#chatRing_"+chatval.recieverID).hasClass('chatRing')==true){
                    $("#chatRing_"+chatval.recieverID).get(0).play();
                }
               
            }


            if(chatline!=''){
             $(".childChatWindow[chat-type='"+chatval.senderID+"'] .chatMessageContainer ul").append(chatResAppend);
             
            }
           

             var sh =  $(".childChatWindow[chat-type='"+chatval.senderID+"'][chat-source='user'] .chatMessageContainer").prop('scrollHeight');
            $(".childChatWindow[chat-type='"+chatval.senderID+"'][chat-source='user'] .chatMessageContainer").closest('.chatMessageContainer').scrollTop(sh);

        }
       
    })
    socket.on('groupChatstart', function(chatval){

        //console.log(chatval)
         var winGroup = $(".allUserChat .chatContainer li[data-id='"+chatval.recieverID+"'][chat-source='group'][is-accepted='1']");
        // ds.css({background:'red'});
         //console.log(chatval);

        if(winGroup.length==1)
        {
            chatResAppend = '';
           
            alOpen = $(".dbchatWrapper[chat-type='"+chatval.recieverID+"'][chat-source='group']").length;


           if(typeof chatval.noInsert!='undefined'){
                if(chatval.noInsert=='success'){
                     return false;
                }
           }
            //console.log(chatval)
            if(alOpen == 0 && isMobile==null){
                 $(".dbchatWrapper[chat-type='alluser'] li[data-id='"+chatval.recieverID+"'][chat-source='group']").trigger('click');
            }
            var SenderType='';
            if(chatval.senderID==SESS_USER_ID){
                
                   SenderType = 'sender';
            }

            chatline = chatval.mychat;


            today = new Date();
            var fileJson='';
            var lastModified ='';
            var fileNameOnLi ='';
            var uploadProgress ='';
            var fileStatus ='';
             var img = chatval.userPicture;//$(".childChatWindow[chat-type='"+chatval.senderID+"']").attr('data-pic');
            var userName = $(".childChatWindow[chat-type='"+chatval.senderID+"'] .unameTitle strong").text();
            var userpicsUrl = BASE_URL+userImagePath+img;  
            var notiText  = '';  
            //console.log(chatval)
            
            if(typeof chatval.fileJson !='undefined')
            {
                lastModified= 'last-modified="'+ chatval.fileJson.lastModified+'"';
                var lastModified2= chatval.fileJson.lastModified;
                var fileName= chatval.fileJson.fileName;
                fileNameOnLi= 'file-name="'+ fileName+'"';
                var fileSizeMB= chatval.fileJson.fileSizeMB;
                uploadProgress= Math.floor(chatval.fileJson.uploadProgress);
                fileStatus= chatval.fileJson.status;
                if(uploadProgress!=''){
                  $('.childChatWindow[chat-type="'+chatval.senderID+'"] [last-modified="'+lastModified2+'"] .chatDownIcon').attr('upload-progress', uploadProgress+'%');
                }

                if(fileStatus=='success')
                {
                  var filefromPHP = chatval.fileJson.fileNameResponse.split('~');   
                  var downLoadIcon = '<span class="chatDownIcon"><i class="fa fa-download"></i></span>';
                  $('[last-modified="'+lastModified2+'"] .chatUploadThumb .chatDownIcon').replaceWith(downLoadIcon);
                  $('[last-modified="'+lastModified2+'"] .chatUploadThumb .uploading').removeClass('uploading');
                  var copyContent=  $('[last-modified="'+lastModified2+'"] .chatUploadThumb').html();
                  var fileTypeThumb = 'fileTypeThumb';
                  if($('[last-modified="'+lastModified2+'"] .chatUploadThumb').hasClass('fileTypeThumb')==false){
                    fileTypeThumb='';
                    var notiMSG = 'file shared with you'+fileName;                   
                  }
                  
                  var cnt = '<a href="'+BASE_URL+'/dashboarduser/downloadpdfuser/pdf/'+filefromPHP[0]+'/orname/'+filefromPHP[1]+'/isf/im" class="itsFileLink '+fileTypeThumb+'">'+copyContent+'</a><span class="fileNameShow">'+fileName+' <br>'+fileSizeMB+'</span>';
                  $('[last-modified="'+lastModified2+'"] .chatUploadThumb').replaceWith(cnt);
                  if(chatval.recieverID==SESS_USER_ID && isMobile==null){
                    $.notification({
                        title:userName,
                        text:notiMSG,
                        icon:userpicsUrl
                    });
                }

                if(chatval.senderID==SESS_USER_ID) {
                    //console.log(chatval.senderID+'=='+userpic)
                   // console.log(cnt)
                    socket.emit('my chat',  {myname:SESS_USER_NAME, mychat :cnt,orgfilename : filefromPHP[1],clientID : clientID, recieverID:chatval.recieverID,senderID:chatval.senderID, noInsert:'success', userPicture : userpic,chatSource:chatval.chatSource});
                } 
                  
                }

                if(fileStatus=='cancelFile'){
                    $('[last-modified="'+lastModified2+'"] .textMsg').text('File transfer cancelled');
                    

                }
                chatline2 = chatline;
            }else{
                chatline2 = emotions(chatline);
            }
            var chatTime = today.format("HH:MM");
           
            //'+chatval.chatid+'
              
              if(typeof img  !='undefined' && typeof(chatval.fileJson) =='undefined' && isMobile==null){                          
                   $.notification({
                        title:userName,
                        text:chatline,
                        icon:userpicsUrl
                    }); 
               }

            joinorleftstatus    =    chatval.joinorleftstatus;
                
            if(joinorleftstatus==1)
            {
                chatResAppend = '<li class="joinorleftstatus">\
                    <div class="msgAsNoti">\
                        <img src="'+BASE_URL+userImagePath+img+'" >\
                        <strong>'+chatval.myname+'</strong> '+chatline2+'\
                    </div>\
                </li>';
            }   
            else{
                chatResAppend = '<li class="'+SenderType+'"  '+lastModified+' '+fileNameOnLi+' >\
                    <div class="textMsg">\
                        '+chatline2+'\
                        <div class="msgTime">'+chatTime+'</div>\
                    </div>\
                    <img src="'+BASE_URL+userImagePath+img+'" class="userImg">\
                </li>';
            }

            $(".dbchatWrapper[chat-type='alluser'] li[data-id='"+chatval.recieverID+"']").removeClass('heightLightChat');
            $(".childChatWindow[chat-type='"+chatval.recieverID+"'] ").removeClass('heightLightChat');

            if( $(".childChatWindow[chat-type='"+chatval.recieverID+"'] ").hasClass('imOnIt')==false && fileStatus!='success'){
                if(chatval.recieverID==SESS_USER_ID){
                    //if(PageTitleNotification.Vars.Interval==null){
                        //PageTitleNotification.On("New chat message!");
                    //}
                 }
                $(".dbchatWrapper[chat-type='alluser'] li[data-id='"+chatval.recieverID+"']").addClass('heightLightChat');
                $(".childChatWindow[chat-type='"+chatval.recieverID+"'] ").addClass('heightLightChat');
               
                if($("#chatRing_"+chatval.recieverID).hasClass('chatRing')==true){
                    $("#chatRing_"+chatval.recieverID).get(0).play();
                }
               
            }
            if(chatline!='' && chatval.senderID!=SESS_USER_ID){
             $(".childChatWindow[chat-type='"+chatval.recieverID+"'] .chatMessageContainer ul").append(chatResAppend);
            }

           
             var sh =  $(".childChatWindow[chat-type='"+chatval.recieverID+"'] .chatMessageContainer").prop('scrollHeight');
            $(".childChatWindow[chat-type='"+chatval.recieverID+"'] .chatMessageContainer").closest('.chatMessageContainer').scrollTop(sh);

            

        }
       
    })

    socket.on('started typing', function(chatval){

        //console.log(chatval);
        if(chatval.chatSource=='group')
        {
            typer  =  chatval.myname.split(' ') ;
            stoptyping  = chatval.mychat.trim();
                
            if(SESS_USER_ID!=chatval.senderID)
            {
                $(".childChatWindow[chat-type='"+chatval.recieverID+"'] .startTyping").remove();
                $(".childChatWindow[chat-type='"+chatval.recieverID+"'] .unameTitle").after('<div class="oneline startTyping">'+typer[0]+' typing...</div>');
            }
            if(!stoptyping)
            {
                $(".childChatWindow[chat-type='"+chatval.recieverID+"'] .startTyping").remove();
            }
        }
        else
        {
            if(chatval.recieverID==SESS_USER_ID || chatval.senderID==SESS_USER_ID)
            {
                stoptyping  = chatval.mychat.trim();
                
                if(SESS_USER_ID!=chatval.senderID)
                {
                    $(".childChatWindow[chat-type='"+chatval.senderID+"'] .startTyping").remove();
                    $(".childChatWindow[chat-type='"+chatval.senderID+"'] .unameTitle").after('<div class="oneline startTyping"> typing...</div>');
                }
                if(!stoptyping)
                {
                    $(".childChatWindow[chat-type='"+chatval.senderID+"'] .startTyping").remove();
                }
            }
        }
    })

    $('body').on('click','.loadpreviousChat',function(){
        var thisEl  = $(this);
        recieverid  = thisEl.attr('reciever-id')
        senderid    = thisEl.attr('sender-id')
        offset      = parseInt(thisEl.attr('offset'))        
        limVal      = parseInt(offset+chatLimit)
        chatSource  = thisEl.attr('chat-source')
        socket.emit('mychathistory',  {clientID : clientID,recieverID:recieverid,senderID:senderid,offsetLimit:limVal,'chatLimit':chatLimit,callerArea:'loadmore',chatSource:chatSource});
    })

    socket.on('loadgroupchatings',function(previousChating,usersInfo){
    
       
        var winGroup = $(".allUserChat .chatContainer li[data-id='"+usersInfo.recieverID+"'][chat-source='group'][is-accepted='1']");
        // ds.css({background:'red'});
        // console.log(previousChating);

        if(winGroup.length==1)
        {
            mychats = '';
            var totuser = Object.keys(previousChating).length;
            //console.log(totuser);
            
            var recieverWindow = $(".childChatWindow[chat-type='"+usersInfo.recieverID+"'][chat-source='group']");
            
            if(usersInfo.callerArea!='loadmore')  $(".chatMessageContainer ul", recieverWindow).html(''); 
 
            //console.log(usersInfo.callerArea)
            if(totuser==chatLimit) {
                loadmoreMsg ='<div class="animated loadpreviousChat" chat-source="group" reciever-id="'+usersInfo.recieverID+'" sender-id="'+usersInfo.senderID+'" offset ="'+usersInfo.offsetLimit+'"  >Load more</div>';
                $(".chatMessageContainer .loadpreviousChat", recieverWindow).remove();
                $(".chatMessageContainer", recieverWindow).prepend(loadmoreMsg);
            }
            else {
                loadmoreMsg ='<div class="nomorechat"  >&nbsp;</div>';
                $(".chatMessageContainer .loadpreviousChat", recieverWindow).remove();
                $(".chatMessageContainer", recieverWindow).prepend(loadmoreMsg);
            }

            cdate = new Date();
            var cday    = cdate.getDate();
            var cmonth  = cdate.getMonth();
            var cyear   = cdate.getFullYear();

            var chatdate = cyear + '-' + cmonth + '-' + cday;

            var monthNames = [
                "Jan", "Feb", "Mar",
                "Apr", "May", "June", "July",
                "Aug", "Sep", "Oct",
                "Nov", "Dec"
            ];

            
            if(usersInfo.callerArea=='loadmore')
            {
                mychats = '';
                for(var i=0; i<totuser;i++)
                {
                    joinorleftstatus    =    previousChating[i].joinorleftstatus;
                    var senderUser =  $(".allUserChat .chatContainer li[data-id='"+previousChating[i].senderid+"'][chat-source='user']");
                    date = new Date(previousChating[i].senddate);
                    var day     = date.getDate().toString();
                    var month   =  date.getMonth() ;
                    var year    = date.getFullYear();
                    var hours   = date.getHours();

                    var showdate = day + '-' + month + '-' + year;

                    displayDate = date.format("ddd - dd mmm, yyyy");//day + '-' + monthNames[month] + '-' + year;;

                    var chatTime = date.format("HH:MM");

                    var isDateapend = $("."+showdate,recieverWindow).length;
                       

                    //console.log(isDateapend+' == '+chatdate+'!='+showdate)

                    if(chatdate!=showdate)
                    {
                        mychats +='<li class="chatdate '+showdate+'">'+displayDate+'</li>';
                        chatdate =  showdate;
                    }

                    userID = ''; userName = ''; Astatus = '';
                    
                    var SenderType='';
                    
                    if(previousChating[i].senderid==SESS_USER_ID){
                        SenderType = 'sender';
                    }
                    
                    if(previousChating[i].senderid==SESS_USER_ID)
                    {
                        userName = previousChating[i].myName;
                        var img  =  userpic;
                    }
                    else 
                    {
                        userName    = previousChating[i].recName;
                        var img     = previousChating[i].myPic; //senderUser.attr('data-pic');
                    }
                   
                   //chatline = emotions(previousChating[i].message); 
                   

                   if(previousChating[i].filename!='' && previousChating[i].filename!='undefined')
                    {
                        chatline = previousChating[i].message;
                    }
                    else
                    {
                        chatline = emotions(previousChating[i].message);
                    }   
                   if(joinorleftstatus==1)
                   {
                        mychats ='<li class="joinorleftstatus">\
                            <div class="msgAsNoti">\
                                 <img src="'+BASE_URL+userImagePath+img+'" height="32"  height="32" >\
                                <strong>'+ getdecodename(userName) +'</strong> '+chatline+'\
                            </div>\
                        </li>';
                        
                   } 
                   else
                   {
                        mychats ='<li class="'+SenderType+'">\
                            <div class="textMsg">\
                                '+chatline+'\
                                <div class="msgTime">'+chatTime+'</div>\
                            </div>\
                            <img src="'+BASE_URL+userImagePath+img+'" class="userImg">\
                        </li>';
                    }

                }
                
                $(".chatMessageContainer ul", recieverWindow).prepend(mychats);
            }
            else
            {

                for(var i=0; i<totuser;i++)
                {
                    joinorleftstatus    =    previousChating[i].joinorleftstatus;

                    var senderUser =  $(".allUserChat .chatContainer li[data-id='"+previousChating[i].senderid+"'][chat-source='user']");
                    date = new Date(previousChating[i].senddate);
                   
                    var day     = date.getDate().toString();
                    var month   =  date.getMonth() ;
                    var year    = date.getFullYear();
                    var hours   = date.getHours();

                    var showdate = day + '-' + month + '-' + year;

                    displayDate = date.format("ddd - dd mmm, yyyy");//day + '-' + monthNames[month] + '-' + year;;

                    var chatTime = date.format("HH:MM");
                    

                    if(chatdate!=showdate)
                    {
                        mychatsdate ='<li class="chatdate '+showdate+'">'+displayDate+'</li>';
                        $(".chatMessageContainer ul", recieverWindow).append(mychatsdate);
                        chatdate =  showdate;   
                    }


                    userID = ''; userName = ''; Astatus = '';
                    
                    var SenderType='';
                    
                    if(previousChating[i].senderid==SESS_USER_ID){
                        SenderType = 'sender';
                    }
                    
                    if(previousChating[i].senderid==SESS_USER_ID)
                    {
                        userName = getdecodename( previousChating[i].myName);
                        var img  =  userpic;
                    }
                    else
                    {
                        userName = getdecodename( previousChating[i].myName);
                        var img     = previousChating[i].myPic;
                    }
                   
                   if(previousChating[i].filename!='' && previousChating[i].filename!='undefined')
                    {
                        chatline = previousChating[i].message;
                    }
                    else
                    {
                        chatline = emotions(previousChating[i].message);
                    }  

                   //alert(joinorleftstatus)
                   if(joinorleftstatus==1)
                   {
                        mychats ='<li class="joinorleftstatus">\
                            <div class="msgAsNoti">\
                                <img src="'+BASE_URL+userImagePath+img+'"  height="32" >\
                                <strong>'+ userName +'</strong> '+chatline+'\
                            </div>\
                        </li>';
                   } 
                   else
                   {
                        mychats ='<li class="'+SenderType+'">\
                            <div class="textMsg">\
                                '+chatline+'\
                                <div class="msgTime">'+chatTime+'</div>\
                            </div>\
                            <img src="'+BASE_URL+userImagePath+img+'" class="userImg">\
                        </li>';
                    }

                    $(".chatMessageContainer ul", recieverWindow).append(mychats);
                    

                }
                var sh = $(".chatMessageContainer", recieverWindow).prop('scrollHeight');
                $(".chatMessageContainer", recieverWindow).scrollTop(sh);
            }    
        }
        
    })

    socket.on('loadchatings',function(previousChating,usersInfo){
       //alert('not');
       

        if(usersInfo.recieverID==SESS_USER_ID || usersInfo.senderID==SESS_USER_ID)
        {
            mychats = '';
            var totuser = Object.keys(previousChating).length;
            //console.log(totuser);
            var recieverWindow = $(".childChatWindow[chat-type='"+usersInfo.recieverID+"']");
            
            if(usersInfo.callerArea!='loadmore')  $(".chatMessageContainer ul", recieverWindow).html(''); 
 
            //console.log(usersInfo.callerArea)
            if(totuser==chatLimit)
            {
                loadmoreMsg ='<div class="animated loadpreviousChat" chat-source="user" reciever-id="'+usersInfo.recieverID+'" sender-id="'+usersInfo.senderID+'" offset ="'+usersInfo.offsetLimit+'"  >Load more</div>';
                $(".chatMessageContainer .loadpreviousChat", recieverWindow).remove();
                $(".chatMessageContainer", recieverWindow).prepend(loadmoreMsg);
            }
            else
            {
                loadmoreMsg ='<div class="nomorechat"  >&nbsp;</div>';
                $(".chatMessageContainer .loadpreviousChat", recieverWindow).remove();
                $(".chatMessageContainer", recieverWindow).prepend(loadmoreMsg);
            }

            cdate = new Date();
            var cday    = cdate.getDate();
            var cmonth  = cdate.getMonth();
            var cyear   = cdate.getFullYear();

            var chatdate = cyear + '-' + cmonth + '-' + cday;

            var monthNames = [
                "Jan", "Feb", "Mar",
                "Apr", "May", "June", "July",
                "Aug", "Sep", "Oct",
                "Nov", "Dec"
            ];

            
            if(usersInfo.callerArea=='loadmore')
            {
                mychats = '';
                for(var i=0; i<totuser;i++)
                {
                    
                    date = new Date(previousChating[i].senddate);
                    var day     = date.getDate().toString();
                    var month   =  date.getMonth();
                    var year    = date.getFullYear();
                    var hours   = date.getHours();

                    var showdate = day + '-' + month + '-' + year;

                    displayDate = date.format("ddd - dd mmm, yyyy");//day + '-' + monthNames[month] + '-' + year;;

                    var chatTime = date.format("HH:MM");

                    var isDateapend = $("."+showdate,recieverWindow).length;
                       

                    //console.log(isDateapend+' == '+chatdate+'!='+showdate)

                    if(chatdate!=showdate)
                    {
                        mychats +='<li class="chatdate '+showdate+'">'+displayDate+'</li>';
                        chatdate =  showdate;
                    }

                    userID = ''; userName = ''; Astatus = '';
                    
                    var SenderType='';
                    
                    if(previousChating[i].senderid==SESS_USER_ID){
                        SenderType = 'sender';
                    }
                    
                    if(previousChating[i].senderid==SESS_USER_ID)
                    {
                        userName = previousChating[i].myName;
                        var img  =  userpic;
                    }
                    else
                    {
                        userName    = previousChating[i].recName;
                        var img = recieverWindow.attr('data-pic');
                    }
                   
                   if(previousChating[i].filename!='' && previousChating[i].filename!='undefined')
                    {
                        chatline = previousChating[i].message;
                    }
                    else
                    {
                        chatline = emotions(previousChating[i].message);
                    }   
                   mychats +='<li class="'+SenderType+'">\
                        <div class="textMsg">\
                            '+chatline+'\
                            <div class="msgTime">'+chatTime+'</div>\
                        </div>\
                        <img src="'+BASE_URL+userImagePath+img+'" class="userImg" />\
                    </li>';

                }
                $(".chatMessageContainer ul", recieverWindow).prepend(mychats);
            }
            else
            {

                for(var i=0; i<totuser;i++)
                {
                    date = new Date(previousChating[i].senddate);
                   
                    var day     = date.getDate().toString();
                    var month   =  date.getMonth() ;
                    var year    = date.getFullYear();
                    var hours   = date.getHours();

                    var showdate = day + '-' + month + '-' + year;

                    displayDate = date.format("ddd - dd mmm, yyyy"); //day + '-' + monthNames[month] + '-' + year;;

                    var chatTime = date.format("HH:MM");

                    if(chatdate!=showdate)
                    {
                        mychatsdate ='<li class="chatdate '+showdate+'">'+displayDate+'</li>';
                        $(".chatMessageContainer ul", recieverWindow).append(mychatsdate);
                        chatdate =  showdate;   
                    }


                    userID = ''; userName = ''; Astatus = '';
                    
                    var SenderType='';
                    
                    if(previousChating[i].senderid==SESS_USER_ID){
                        SenderType = 'sender';
                    }
                    
                    if(previousChating[i].senderid==SESS_USER_ID)
                    {
                        userName = previousChating[i].myName;
                        var img  =  userpic;
                    }
                    else
                    {
                        userName    = previousChating[i].recName;
                        var img = recieverWindow.attr('data-pic');
                    }

                    if(previousChating[i].filename!='' && previousChating[i].filename!='undefined')
                    {
                        chatline = previousChating[i].message;
                    }
                    else
                    {
                        chatline = emotions(previousChating[i].message);
                    }
                     
                   
                   mychats ='<li class="'+SenderType+'">\
                        <div class="textMsg">\
                            '+chatline+'\
                            <div class="msgTime">'+chatTime+'</div>\
                        </div>\
                        <img src="'+BASE_URL+userImagePath+img+'" class="userImg">\
                    </li>';

                    $(".chatMessageContainer ul", recieverWindow).append(mychats);

                }
                var sh = $(".chatMessageContainer", recieverWindow).prop('scrollHeight');
                $(".chatMessageContainer", recieverWindow).scrollTop(sh);
            }    
        }
        
    })

    socket.on('online offline', function(chatval,calling){
        //console.log('chatval')

        if(chatval.clientID==clientID)
        {
            var isOpen = $(".allUserChat li[data-id='"+chatval.userID+"']").length;
            var clientWin = $(".childChatWindow[chat-type='"+chatval.userID+"'][chat-source='user']");
            
            if(calling=='chatstatus')
            {
                if(chatval.olStatus==1)
                {
                    if(isOpen>0)
                    {
                        $ghostMessage( '<img height="32" src="'+BASE_URL+userImagePath+chatval.ProfilePic+'" style="float:left; margin-right:10px;"><strong>'+ chatval.full_name+'</strong><br>is now online');
                    }

                     $(".allUserChat li[data-id='"+chatval.userID+"'] .liveStatus").removeClass('offline'); 
                     $('.showMYstatus .liveStatus', clientWin).removeClass('offline');
                     $('.showMYstatus b', clientWin).text('online');
                }
                else
                { 
                    if(isOpen>0)
                    {
                        $ghostMessage( '<img height="32" src="'+BASE_URL+userImagePath+chatval.ProfilePic+'" style="float:left; margin-right:10px;"> <strong>'+ chatval.full_name+'</strong><br>went offline');
                    }
                    $(".allUserChat li[data-id='"+chatval.userID+"'] .liveStatus").addClass('offline');  
                    $('.showMYstatus .liveStatus', clientWin).addClass('offline');     
                    $('.showMYstatus b', clientWin).text('offline');     
                }
                 
            }
            else
            {
                if(chatval.olStatus==1)
                {
                    if(isOpen>0)
                    {
                        $ghostMessage( '<img height="32" src="'+BASE_URL+userImagePath+chatval.ProfilePic+'" style="float:left; margin-right:10px;"><strong>'+ chatval.full_name+'</strong><br>is now online');
                    }

                    $(".allUserChat li[data-id='"+chatval.userID+"'] .liveStatus").removeClass('offline');
                    $('.showMYstatus .liveStatus', clientWin).removeClass('offline');
                    $('.showMYstatus b', clientWin).text('online');
                   

                }
                else
                { 
                    if(isOpen>0)
                    {
                        $ghostMessage( '<img height="32" src="'+BASE_URL+userImagePath+chatval.ProfilePic+'" style="float:left; margin-right:10px;"> <strong>'+ chatval.full_name+'</strong><br>went offline');
                    }
                    $(".allUserChat li[data-id='"+chatval.userID+"'] .liveStatus").addClass('offline');
                    $('.showMYstatus .liveStatus', clientWin).addClass('offline');   
                    $('.showMYstatus b', clientWin).text('offline');   

                }
            }
        }
    })

    socket.on('chkactivityforchat', function(){
        chkactivitynotification(1)
    })

    

    $('body').on('click','.dbchatWrapper.allUserChat .chatTittleBar li a',function(e){
        
        var thisEl  = $(this);
        var Stval =$.trim(thisEl.attr('data-value'));
       // if(CHAT_STATUS==Stval) return false;
        var p = thisEl.closest('.chatTittleBar');
        var p2 = thisEl.closest('.dbchatWrapper');
        if(Stval==0){
            $('.dropDown .myPicForChat', p).addClass('offline');
            $('.showMYstatus', p).html('offline');
            showOnlineBtn = '<li><a href="javascript:void(0);" data-value="1"><span class="liveStatus"></span>Show me online</a></li>';

        }else if(Stval==1){
             $('.dropDown .myPicForChat', p).removeClass('offline');
             $('.showMYstatus', p).html('online');
              showOnlineBtn = '<li><a href="javascript:void(0);" data-value="0"><span class="liveStatus offline"></span>Show me offline</a></li>';

        }
        if(Stval==0 || Stval==1){
            $('.liveStatus', p).closest('li').remove();
            $('.dropDownList', p).prepend(showOnlineBtn);
            CHAT_STATUS=Stval;
            socket.emit('change chat status',  {'olStatus':Stval,'clientID' : clientID,'userID':SESS_USER_ID,'full_name': SESS_USER_NAME,'ProfilePic':userpic});
        }
         if(Stval==3){
          var creatGroup = '<div class="creatGroupChat">\
                                <div class="chatRowInfo"><a href="javascript:void(0);" class="chatCreatGroupBtn"><i class="fa fa-check"></i> Done</a><a href="javascript:void(0);" class="chatCancelGroupBtn"><i class="fa fa-times"></i> Cancel</a></div>\
                                <div class="chatRowInfo"><input type="text" name="chatGrpName" placeholder="Group name" /></div>\
                                <div class="chatRowInfo"><input type="hidden" name="selectChatUsers" /></div>\
                            </div>';
         $('.creatGroupChat', p2).remove(); 
         $('.chatContainer', p2).prepend(creatGroup);
        function _chatSelectUserFormating(data) {
                
                     return '<img src="'+BASE_URL+userImagePath+data.src+'" />' + data.text;
                   
                }

                function _chatSelectUserFormatingRes(data) {
                 
                     return '<img src="'+BASE_URL+userImagePath+data.src+'" />' + data.text;
                   
                }
         $('input[name="selectChatUsers"]', p2).select2({
            data:allUserSelectJson, 
            placeholder:'Select users',
            tags: true,
            formatResult:_chatSelectUserFormatingRes,
            formatSelection:_chatSelectUserFormating
        });

         $('.chatCreatGroupBtn').click(function(){
            var p = $(this).closest('.creatGroupChat');
            var gNameEl = $('input[name="chatGrpName"]', p);
            var gUsrEl =  $('input[name="selectChatUsers"]', p);
            var chatGrpName = $.trim(gNameEl.val());
            var chatGrpUsers = $.trim(gUsrEl.val());  

            if(chatGrpName==''){
                gNameEl.focus();
            }else if(chatGrpUsers==''){
                $('.select2-container input', p).focus();
            }else{

                 chatGrpUsers = chatGrpUsers+','+SESS_USER_ID;
                 socket.emit('create group',{clientID : clientID,groupName:chatGrpName,groupOwner:SESS_USER_ID,groupMembers:chatGrpUsers,groupOwnerName:SESS_USER_NAME,groupOwnerPic:userpic});

               
            }
           
            //var userPicName = 'chat-group.png';
         });

         $('.chatCancelGroupBtn').click(function(){
                $('.creatGroupChat').animate({top:'-100%'},1000, function(){
                    $(this).remove();
                });
         });


      }


    
    })

    


    $(window).resize(function() {
        windH = $(window).height();
        windW = $(window).width();
    }).resize();
var uploadDrp ='';
$.chatWindows = function (options){

    var oflSel = 'online';
    var showOnlineBtn = '<li><a href="javascript:void(0);" data-value="0"><span class="liveStatus offline"></span>Show me offline</a></li>';
    if(CHAT_STATUS==0) {
      oflSel = "offline";
      showOnlineBtn = '<li><a href="javascript:void(0);" data-value="1"><span class="liveStatus"></span>Show me online</a></li>';
    }
   // else if(CHAT_STATUS==1) oflSel = "online";


    var defaults = {
        userType:'alluser',             
        chatSource:'user',             
        userPic:'',             
        content:'',
        userName:'<div class="dropDown">\
                    <div class="myPicForChat '+oflSel+'"><img src="'+BASE_URL+userImagePath+userpic+'"></div>\
                    <i class="fa fa-chevron-circle-down"></i>\
                    <ul class="dropDownList">\
                    '+showOnlineBtn+'\
                     <li><a href="javascript:void(0);" data-value="3"><i class="fa fa-comments"></i></span>Create Group</a></li>\
                    </ul>\
                </div><div class="connectionChatTL oneline">'+SESS_USER_NAME+' <span class="showMYstatus">'+oflSel+'</span></div>',
        positions:'',
        highlight:'',
        search:'<a href="javascript:void(0);" class="searchInUserBox"><i class="fa fa-search"></i></a>',
        expand:'<a href="javascript:void(0);" class="expandCollapse"><i class="fa fa-angle-down"></i></a>',
        maximize:'',
        close:'<a href="javascript:void(0);" class="chatCloseUser"><i class="fa fa-times"></i></a>',
        class:''
    }

    var settings = $.extend({}, defaults, options);
    var userType = settings.userType;
    var chatSource = settings.chatSource;
    var positions = settings.positions;
    var backtoMainFeed ='';
    var backToUserList ='<a href="javascript:void(0);" class="backToUserList"><i class="fa fa-user"></i></a>';
    var chatStartNow ='';
    if(positions!=''){
        positions = 'right:'+positions;
    }
    var openShareBox = '';
    if(userType=='alluser'){
        backToUserList = '';
          chatStartNow ='';
        openShareBox='<div class="chatAudioRing"><audio autostart="false" class="chatRing" id="chatRing_'+SESS_USER_ID+'" controls></div>\
                          <source src="'+BASE_URL+'/img/audiochat.wav" type="audio/wav">\
                    </audio><div class="searchBoxOnChat">\
                        <input type="text" value="" placeholder="search user"/>\
                        <a href="javascript:void(0);" class="closeUserSearchBar"><i class="fa fa-times-circle"></i></a>\
                         <i class="fa fa-search"></i>\
                    </div>';
          //backtoMainFeed = '<a href="javascript:void(0);" class="backToMainFeed"><i class="fa fa-arrow-circle-right"></i></a>';
    }
   
    var openUserType = '<div class="openChatType">'+settings.userName+'</div>';
    var iconSetChat = '<div class="iconSetChat">'+settings.highlight+settings.search+settings.expand+settings.close+settings.maximize+'</div>';
    var titleBar = '<div class="chatTittleBar">\
                    '+backToUserList+backtoMainFeed+openUserType+iconSetChat+openShareBox+'\
                    </div>';
    var allUsers = '<div class="chatContainer">'+settings.content+'</div>';
    var chatInterface = '<div class="dbchatWrapper '+settings.class+'" style="'+positions+'"  data-pic="'+settings.userPic+'"  chat-type="'+userType+'" chat-source="'+chatSource+'">'+chatStartNow+titleBar+allUsers+'</div>';
    $('body').append(chatInterface);
    if(userType!='alluser'){  
            
          
              

            socket.emit('mychathistory',  {clientID : clientID,recieverID:userType,senderID:SESS_USER_ID,offsetLimit:0,'chatLimit':chatLimit,chatSource:chatSource}); 
          var  chatWindow =  $('.dbchatWrapper[chat-type="'+userType+'"]');
          uploadDrp  =  $("#fileSharingChat_"+userType).dropzone({ 
                url:  BASE_URL + "/dbchat/exchangefiles",
                maxFilesize: 10,
                acceptedFiles: '.png, .jpg, .gif, .GIF, .jpeg, .JPG, .JPEG, .PNG, .txt,.TXT, .doc, .DOC, .docx, .DOCX, .pdf, .PDF, .xls, .XLS, .xlsx, .XLSX, .ppt,.PPT, .pptx, .PPTX, .mp3, .MP3, .mp4, .MP4',
                error: function(file, dataUrl) {
                    fileType =   file.type.split('/')[0].toLowerCase(); //fileType == "audio" ||

                    chatWidth = $('.chatMessageContainer').width();
                    var msg = '';
                    if ( fileType == "video" ) {
                        msg = 'Sorry you cannot upload this file type.';
                    }
                    else if(file.size>this.options.maxFilesize * 1024 * 1024) {
                         msg = 'Sorry you can only upload files up to 5MB.';   
                    }
                    else {
                         msg = 'Sorry you cannot upload this file type.';
                    }

                      $('.chatContainer', chatWindow).append('<div class="maxsizeError">'+msg+'</div>'); 
                        setTimeout(function(){
                            $('.chatContainer .maxsizeError', chatWindow).remove();
                        },3000);
                        return false;
                },
                
                thumbnail: function(file, dataUrl) {
                    //var img = '<li><div class="chatUploadThumb"><img src="'+dataUrl+'"  datafile="'+file.previewElement+'" class="uploading"/></div></li>';
                    /*var lm = file.xhr.response;
                    console.log(lm);*/
                    if(file.size>this.options.maxFilesize * 1024 * 1024) 
                    {
                        return false;
                    }
                   
                    //console.log(file);
                    //console.log(file.timeStamp);
                    var chatAppend = '<div class="chatUploadThumb"><img src="'+dataUrl+'" class="uploading"/>\
                                            <span class="chatDownIcon" upload-progress="0">\
                                                <i class="fa fa-close"></i>\
                                            </span>\
                                        </div>';

                    var chatAppend2 = '<div class="chatUploadThumb"><img src="'+dataUrl+'" class="uploading"/>\
                                            <span class="chatDownIcon" upload-progress="0">\
                                            </span>\
                                        </div>'; 
                        var fileJson = {lastModified:file.timeStamp, fileName:file.name, uploadProgress:0};
                        senderLayout(chatWindow, chatAppend, fileJson);                 
                   /* var chatContent = '<li class="sender" last-modified="'+file.lastModified+'" file-name="'+file.name+'">\
                                            <div class="textMsg">\
                                               '+chatAppend+'\
                                            </div>\
                                            <img src="'+BASE_URL+'/timthumb.php?src=/userpics/'+userpic+'&q=100&w=32&h=32" class="userImg" />\
                                        </li>';

                    $('.chatMessageContainer ul', chatWindow).append(chatContent); 

                    
                    var sh =  $('.chatMessageContainer', chatWindow).prop('scrollHeight');
                         $('.chatMessageContainer', chatWindow).scrollTop(sh);

                    var fileJson = {lastModified:file.lastModified, uploadProgress:0};*/
                   socket.emit('chatfile',  {myname:SESS_USER_NAME, mychat :chatAppend2, clientID : clientID, recieverID:userType,senderID:SESS_USER_ID, fileJson:fileJson,chatSource:chatSource, userPicture : userpic});
                    
                },
                  processing:function (file){
                    var now = new Date();
                    var timeStamp = Math.floor(Date.now()+now.getSeconds() / 1000);
                    file.timeStamp = timeStamp;

                    var getExtention = file.name.split('.').length;
                    getExtention =   file.name.split('.')[getExtention-1].toLowerCase();
                    fileUploadingJson.push({lastModified:file.timeStamp, file:file});

                    if(getExtention !='png' && getExtention !='jpg' && getExtention !='jpeg' && getExtention !='gif' && getExtention !='GIF'){
                        var iconsFiles = 'fa-file';
                        if(getExtention=='doc' || getExtention=='docx'){
                            iconsFiles = 'fa-file-word-o';
                        }                       
                        else if(getExtention=='xls' || getExtention=='xlsx'){
                            iconsFiles = 'fa-file-excel-o';
                        }
                        else if(getExtention=='ppt' || getExtention=='pptx'){
                            iconsFiles = 'fa-file-powerpoint-o';
                        }
                        else if(getExtention=='pdf'){
                            iconsFiles = 'fa-file-pdf-o';
                        }
                         else if(getExtention=='txt'){
                            iconsFiles = 'fa-file-text-o';
                        }
                        else if(getExtention=='mp3'){
                            iconsFiles = 'fa-file-audio-o';
                        }
                         else if(getExtention=='mp4'){
                            iconsFiles = 'fa-file-video-o';
                        }
                        

                        var chatAppend = '<div class="chatUploadThumb fileTypeThumb"><strong class="fa  '+iconsFiles+' fa-4x uploading fileChatIcon"></strong>\
                                            <span class="chatDownIcon" upload-progress="0">\
                                                <i class="fa fa-close"></i>\
                                            </span>\
                                        </div>';
                        var chatAppend2 = '<div class="chatUploadThumb fileTypeThumb"><strong class="fa  '+iconsFiles+' fa-4x uploading fileChatIcon"></strong>\
                                            <span class="chatDownIcon" upload-progress="0">\
                                            </span>\
                                        </div>';

                         var fileJson = {lastModified:file.timeStamp, fileName:file.name, uploadProgress:0};                 
                      senderLayout(chatWindow, chatAppend, fileJson); 
                        
                      /*  var chatContent = '<li class="sender" last-modified="'+file.lastModified+'" file-name="'+file.name+'">\
                                            <div class="textMsg">\
                                               '+chatAppend+'\
                                            </div>\
                                            <img src="'+BASE_URL+'/timthumb.php?src=/userpics/'+userpic+'&q=100&w=32&h=32" class="userImg" />\
                                        </li>';

                    $('.chatMessageContainer ul', chatWindow).append(chatContent); 
                       var fileJson = {lastModified:file.lastModified, uploadProgress:0};*/
                       socket.emit('chatfile',  {myname:SESS_USER_NAME, mychat :chatAppend2,clientID : clientID, recieverID:userType,senderID:SESS_USER_ID, fileJson:fileJson,chatSource:chatSource, userPicture : userpic});
                   }

                        var sh =  $('.chatMessageContainer', chatWindow).prop('scrollHeight');
                         $('.chatMessageContainer', chatWindow).scrollTop(sh);
                    
                  },
                  uploadprogress:function(file, percentage){
                   
                    var percentage =   Math.floor(percentage);

                    $('[last-modified="'+file.timeStamp+'"] .chatDownIcon', chatWindow).attr('upload-progress', percentage+'%');
                    
                   if(percentage == 0 || percentage == 10 || percentage == 30 || percentage == 50 ||  percentage == 70 ||  percentage==80 ||  percentage==100){
                        var fileJson = {lastModified:file.timeStamp, uploadProgress:percentage};
                        socket.emit('chatfile',  {myname:SESS_USER_NAME, mychat :'',clientID : clientID, recieverID:userType,senderID:SESS_USER_ID, fileJson:fileJson,chatSource:chatSource, userPicture : userpic});
                   }
                   
                  },
                  success:function(file, response){
                    if(response!='error'){  
                         var fileSizeMB = Dropzone.prototype.filesize(file.size);
                         var fileName  = file.name;
                        var fileJson = {lastModified:file.timeStamp, uploadProgress:100,fileSizeMB:fileSizeMB, fileName:fileName, fileNameResponse:response, status:'success'};
                        socket.emit('chatfile',  {myname:SESS_USER_NAME, mychat :'',clientID : clientID, recieverID:userType,senderID:SESS_USER_ID, fileJson:fileJson,chatSource:chatSource, userPicture : userpic});
    
                    }
                  }
            });

            // sender and receiver parameter are exchanged because for recever chat window ids will exchanged
            //$.cookie('openedChildWindow-'+userType+'-'+SESS_USER_ID, userType ,{path:'/'});
             if(isMobile==null){
                $('.dbchatWrapper[chat-type="'+userType+'"] textarea').focus();
                }
            var sh = $('.dbchatWrapper[chat-type="'+userType+'"] .chatMessageContainer').prop('scrollHeight');
            $('.dbchatWrapper[chat-type="'+userType+'"] .chatMessageContainer').scrollTop(sh);
             
           /* if($.cookie('chatWindowOff-'+userType+'-'+SESS_USER_ID)=='true'){
                $('.dbchatWrapper[chat-type="'+userType+'"]:not(.offCh) .expandCollapse').trigger('click');
            }*/
        
    }else{
      
            var sh = $('.dbchatWrapper[chat-type="'+userType+'"] .chatContainer').prop('scrollHeight');
            //$('.dbchatWrapper[chat-type="'+userType+'"] .chatContainer').scrollTop(sh);
        
      /*  if(windH<sh){
            if($.cookie('chatWindowOff-alluser-'+SESS_USER_ID)!='true'){
                 $('.dbchatWrapper[chat-type="'+userType+'"] .chatMinmax').trigger('click');
            }
        }
       if($.cookie('chatWindowOff-alluser-'+SESS_USER_ID)=='true'){
             $('.dbchatWrapper[chat-type="'+userType+'"] .expandCollapse').trigger('click');
        } */
       
    }


} 

$.checkGroups = function (allUsersElement){
    var totalGroups = $('li[chat-source="group"]',allUsersElement).length;
            var createdGroups = $('li[chat-source="group"][group-owner="'+SESS_USER_ID+'"]',allUsersElement).length;
            var myGroups = totalGroups-createdGroups;
                if(createdGroups==0){
                     $('.itsMyGroup', allUsersElement).remove();
                }
                 if(myGroups==0){
                    $('.itsOtherGroup', allUsersElement).remove();
                }
}

$.allUserChat = function(options){        
    var defaults = {
            append:false,
            prepand:false,
            userId:'',
            remove:false,
            allUser:false,
            type:'user',
            content:'',             
            groupOwnerId:'', 
            class:'',            
         }

    var settings = $.extend({}, defaults, options);
    var ap = settings.append;
    var pre = settings.prepend;
    var cnt = settings.content;
    var remove = settings.remove;
    var id = settings.userId;
    var type = settings.type;
    var groupOwnerId = settings.groupOwnerId;
    var allUserClass = '';
    if(settings.class!=''){
        allUserClass = settings.class;
    }
     if(settings.allUser==true){
        $('.allUserChat').remove();
        $.chatWindows({close:'',class:'allUserChat '+allUserClass,content:cnt});
        $('body').addClass('activeChatingSystem'); 
        chatStartNow ='';
          $('.chatStartNow').remove();
        $('body').append(chatStartNow);

        

        $('.chathighlightOverlay').remove();
        $('body').prepend('<div class="chathighlightOverlay"></div>');

     
       /* if($.cookie('hightLightChat-'+SESS_USER_ID)=='on'){
            $('.chathighlightOverlay').show();
        }
        if($.cookie('chatWindowType-alluser-'+SESS_USER_ID)=='max'){
            $('.dbchatWrapper[chat-type="alluser"]:not(.onchatMaxWin) .chatMinmax').trigger('click');
        }
*/
       /* $('.dbchatWrapper[chat-type="alluser"] .chatContainer li:not(.itsSeprator)').each(function (){
            var userId  = $(this).attr('data-id');
            if($.cookie('openedChildWindow-'+userId+'-'+SESS_USER_ID)==userId && isMobile==null){            
                $(this).trigger('click');
            }
        });*/

     }
     else if(ap==true){
        $('.allUserChat .chatContainer ul').append(cnt);
    }else if(pre==true){
        
        if(SESS_USER_ID==groupOwnerId && type=='group'){
            if($('.allUserChat .chatContainer ul .itsMyGroup').length>0){
                 $('.allUserChat .chatContainer ul .itsMyGroup').after(cnt);
            }else{
                 $('.allUserChat .chatContainer ul').prepend( allUserTitle[0]+cnt);
            }
           

        }else if(SESS_USER_ID!=groupOwnerId && type=='group'){
             if($('.allUserChat .chatContainer ul .itsOtherGroup').length>0){
                $('.allUserChat .chatContainer ul .itsOtherGroup').after(cnt); 
             }else{
                $('.allUserChat .chatContainer ul .itsAllUsers').before(allUserTitle[1]+cnt);    
             }
        }else{
            $('.allUserChat .chatContainer ul .itsAllUsers').after(cnt); 
        }
        //$('.allUserChat .chatContainer ul').prepend(cnt);
    }
    else if(remove==true){
        $('.allUserChat .chatContainer  li[data-id="'+id+'"]').remove();
        $('.childChatWindow[chat-type="'+id+'"] .chatCloseUser').trigger('click');
    }
};


$('body').on('click', '.chatDownIcon', function(event) {
        var p =  $(this).closest('.sender');
        var p2 =  $(this).closest('.childChatWindow');   
        var userType =  p2.attr('chat-type');        
        var lastModified =  p.attr('last-modified');
        var fileName =  p.attr('file-name');
        var chatSource =  p2.attr('chat-source');
        $.each(fileUploadingJson, function(index, value){
            if(value.lastModified==lastModified){
                 value.file.xhr.abort();
            }
        });
        $('.dz-filename span:contains("'+fileName+'")', p2).closest('.dz-preview').remove();

        var fileJson = {lastModified:lastModified, status:'cancelFile'}; 
        //console.log(fileJson);
        socket.emit('chatfile',  {myname:SESS_USER_NAME, mychat :'',clientID : clientID, recieverID:userType,senderID:SESS_USER_ID, fileJson:fileJson,chatSource:chatSource, userPicture : userpic});
        
          
});

/*$('body').on('click', '.highlightChatBox', function(event) {
    $('.chathighlightOverlay').fadeToggle(function(){
        if($('.chathighlightOverlay').is(':visible')==true){
                $.cookie('hightLightChat-'+SESS_USER_ID, 'on', {path:'/'});
            }else{
                $.removeCookie('hightLightChat-'+SESS_USER_ID, {path:'/'});
            }
    });
});*/

$('body').on('click', '.addSmilyIntoChat', function(event) {
    var thisEl = $(this);
    var p = $(this).closest('.chatInputAreaBox');
    p.toggleClass('activeEmotion');
});
$('body').on('click', '.emoList a', function(event) {
     var thisEl = $(this);
     var p = thisEl.closest('.chatInputAreaBox');
     var recieverID = thisEl.closest('.childChatWindow').attr('chat-type');
     var txt = thisEl.text();
     p.removeClass('activeEmotion');
     var val = $('textarea', p).val();
     $('textarea', p).val(val+' '+txt+' ');
     $('textarea', p).focus();
   // senderLayout(thisEl, txt); 
    //socket.emit('my chat',  {myname:SESS_USER_NAME, mychat : txt,clientID : clientID, recieverID:recieverID,senderID:SESS_USER_ID});
});


$('body').on('click', '.searchInUserBox, .closeUserSearchBar',function (){
    var tsl = $(this);
    var p = tsl.closest('.dbchatWrapper');
    if(p.hasClass('childChatWindow')==false){
        if(tsl.hasClass('closeUserSearchBar')==false){
            $('.chatTittleBar',p).addClass('activeSearchBox');        
            setTimeout(function (){
                 $('.chatTittleBar  input',p).focus(); 
             }, 500);       
        }else{
            $('.chatTittleBar  input',p).val('').trigger('keyup');
            $('.chatTittleBar',p).removeClass('activeSearchBox');
        }
    }else{
        $('.searchBoxOnChat  input',p).val('').trigger('keyup');
        $('.searchUserListingChildW',p).fadeOut(function(){
            p.removeClass('activeSearchBox'); 
        });
        
    }
});
$('body').on('click', '.searchUserListingChildW .adrUsBtn',function (){
    var tsl = $(this);
    var p2 = tsl.closest('.dbchatWrapper');
    var p = tsl.closest('li');
    var chatType = p2.attr('chat-type');
    var memberId = p.attr('data-id');
    var memberName = $('.userNameAdre', p).text();
    var groupName = $('.chatTittleBar .unameTitle ', p2).text();
    var addUserVal = 1;
    if(tsl.hasClass('addUserToThisGroup')==true){ 
        tsl.addClass('removeUserToThisGroup').removeClass('addUserToThisGroup').text('Remove');
       
    }else{
         tsl.addClass('addUserToThisGroup').removeClass('removeUserToThisGroup').text('Add'); 
         addUserVal = 0;
         socket.emit('my chat',  {myname:memberName, mychat : ' removed by group owner',clientID : clientID, recieverID:chatType,senderID:memberId, chatSource : 'group', userPicture : userpic,'joinorleftstatus':1, siteNotification:1,owner:SESS_USER_ID});
    }

    socket.emit('addremove members',  {groupName:groupName , clientID : clientID, groupID:chatType,memberId:memberId,owner:SESS_USER_ID,isaccept:addUserVal,groupOwnerName:SESS_USER_NAME,groupOwnerPic:userpic});

    
});


$('body').on('keyup', '.activeSearchBox input',function (){
    var tsl = $(this);
    var p = tsl.closest('.dbchatWrapper');
    var thisValue = tsl.val().toLowerCase();
    if(p.hasClass('childChatWindow')==true){
       var searchByEl = $('.childUList li .userNameAdre', p);
    }else{
      var searchByEl = $('.chatContainer li .Uname', p);
    }
    searchByEl.each(function(index, val) {
            var str = $(this).text().toLowerCase();
            var  n = str.search(thisValue);
            if(n>-1){
                 $(this).closest('li').show();
                
            }else{
                 $(this).closest('li').hide();
                
            }
        });
});

socket.on('acceptrejectRespond',function(userRespond){
    var p = $('.dbchatWrapper[chat-type="'+userRespond.groupID+'"][chat-source="group"]');
    var index =  p.length;
    if(userRespond.isaccept==1)
    {   
        $('.groupChatNoficationBox', p).fadeOut(function (){
                $(this).remove()
                $('.chatInputAreaBox, .openChatType .dropDown',p).show();
                $('.chatInputAreaBox textarea',p).removeAttr('disabled').focus();
                $('.dbchatWrapper.allUserChat li[data-id="'+userRespond.groupID+'"][chat-source="group"]').attr('is-accepted', 1);
                socket.emit('mychathistory',  {clientID : clientID,recieverID:userRespond.groupID,senderID:SESS_USER_ID,offsetLimit:0,'chatLimit':chatLimit,callerArea:'',chatSource:'group'});
        });  
    }
    else if(userRespond.isaccept==0){
        var p2 = $('.dbchatWrapper.allUserChat');
        $.checkGroups(p2);
        if(index>0){
            if($('.groupChatNoficationBox', p).length>0){
                $('.groupChatNoficationBox', p).fadeOut(function (){             
                    $('.dbchatWrapper.allUserChat li[data-id="'+userRespond.groupID+'"][chat-source="group"]').remove();
                    $('.chatCloseUser', p).trigger('click');
                    $(this).remove();
                }); 
            }else{
                $('.dbchatWrapper.allUserChat li[data-id="'+userRespond.groupID+'"][chat-source="group"]').remove();
                $('.chatCloseUser', p).trigger('click');
            }
        }else{
          $('.dbchatWrapper.allUserChat li[data-id="'+userRespond.groupID+'"][chat-source="group"]').remove();  
        }

    }
})

$('body').on('click', '.chatAccpetGroupBtn', function(event) {
    var tsl = $(this);
    var p = tsl.closest('.dbchatWrapper');
    var chatType = p.attr('chat-type');
    socket.emit('chatacceptreject',  {myname:SESS_USER_NAME, clientID : clientID, groupID:chatType,myID:SESS_USER_ID,isaccept:1});
    socket.emit('my chat',  {myname:SESS_USER_NAME, mychat : ' joined this Group',clientID : clientID, recieverID:chatType,senderID:SESS_USER_ID, chatSource : 'group', userPicture : userpic,'joinorleftstatus':1});
});


$('body').on('click', '.chatRejectGroupBtn', function(event) {
    var tsl = $(this);
    var p = tsl.closest('.dbchatWrapper');
    var chatType = p.attr('chat-type');
    
    socket.emit('chatacceptreject',  {myname:SESS_USER_NAME, clientID : clientID, groupID:chatType,myID:SESS_USER_ID,isaccept:0});
   
});

$('body').on('click', '.addUserToChatGroup', function(event) {
    event.stopPropagation();
    var tsl = $(this);
    var p = tsl.closest('li');
     var chatType = p.attr('chat-type');
   
});

$('body').on('click', '.deleteMyGroupChat', function(event) {
    event.stopPropagation();
    var tsl = $(this);
    var p = tsl.closest('li');
    var p2 = tsl.closest('.allUserChat');
     var chatType = p.attr('chat-type');
    var removeAttrHtml = '<div class="chatNotiRemoveGrpWrp">\
                            <span>'+groupLeaveMsg+'</span>\
                            <a href="javascript:void(0);" class="chatLeaveOkGroupBtn circleBtn" title="Leave Group"><i class="fa fa-check"></i></a>\
                            <a href="javascript:void(0);" class="chatLeaveCancelGroupBtn circleBtn" title="Cancel"><i class="fa fa-times"></i></a>\
                        </div>';
   p.wrapInner('<div class="movePlaceHolder"></div>'); 
   $('.chatNotiRemoveGrpWrp' , p).remove(); 
   p.prepend(removeAttrHtml);
   p.addClass('activeLeaveChatGroup');  
   $('.chatNotiRemoveGrpWrp' , p).animate({right:0}); 
   $('.movePlaceHolder', p).animate({right:'-100%'});


});

$('body').on('click', '.chatLeaveOkGroupBtn', function(event) {
    event.stopPropagation();
    var tsl = $(this);
   if(tsl.closest('.dbchatWrapper').hasClass('childChatWindow')==false) {
        var p = tsl.closest('li');
        var p2 = tsl.closest('.allUserChat');
        var groupID = p.attr('data-id');
        var groupName = $('.chatUserDetails .Uname',p).text();        
        $('.chatNotiRemoveGrpWrp', p).animate({right:'-100%'},function(){
              p.animate({height:0},'fast',function(){          
                $('.childChatWindow[chat-type="'+groupID+'"][chat-source="group"] .chatCloseUser').trigger('click');
                //addUsersInChatGroups('', groupID, groupName);
                $.checkGroups(p2);

                 socket.emit('chatacceptreject',  {myname:SESS_USER_NAME, clientID : clientID, groupID:groupID,myID:SESS_USER_ID,isaccept:0});

                 socket.emit('my chat',  {myname:SESS_USER_NAME, mychat : ' left this Group',clientID : clientID, recieverID:groupID,senderID:SESS_USER_ID, chatSource : 'group', userPicture : userpic,'joinorleftstatus':1});

                   p.remove();
              });         
        }); 
    }else{
        var p = tsl.closest('.childChatWindow');
        var groupID = p.attr('chat-type');
        var groupName = $('.unameTitle strong',p).text();
        var p2 = $('.dbchatWrapper.allUserChat');
       $('.chatNotiRemoveGrpWrp', p).animate({right:'100%'},function(){ 
                //addUsersInChatGroups('', groupID, groupName);
                 $.checkGroups(p2);
                socket.emit('chatacceptreject',  {myname:SESS_USER_NAME, clientID : clientID, groupID:groupID,myID:SESS_USER_ID,isaccept:0});

                $('.allUserChat .allUserList li[data-id="'+groupID+'"][chat-source="group"]').remove();
               $('.chatCloseUser', p).trigger('click');
               socket.emit('my chat',  {myname:SESS_USER_NAME, mychat : ' left this Group',clientID : clientID, recieverID:groupID,senderID:SESS_USER_ID, chatSource : 'group', userPicture : userpic,'joinorleftstatus':1});

        });
    }

});

$('body').on('click', '.chatLeaveCancelGroupBtn', function(event) {
    event.stopPropagation();
    var tsl = $(this);
    if(tsl.closest('.dbchatWrapper').hasClass('childChatWindow')!=true) {
        var p = tsl.closest('li');
        $('.movePlaceHolder', p).animate({right:0});
        $('.chatNotiRemoveGrpWrp', p).animate({right:'100%'},function (){
             p.removeClass('activeLeaveChatGroup');
             $('.chatUserDetails', p).unwrap();
             $(this).remove();
        }); 
    }else{
        var p = tsl.closest('.childChatWindow');
        $('.chatNotiRemoveGrpWrp', p).animate({right:'100%'},function(){ 
              $(this).remove();
        });
    }
});

$('body').on('click', '.chatMinmax', function(event) {
                var tsl = $(this);
                var p = tsl.closest('.dbchatWrapper');
               /* var addCompress = $('.fa-expand', tsl).removeClass('fa-expand');
                var addExpand = $('.fa-compress', tsl).removeClass('fa-compress');*/
                 
                var chatType = p.attr('chat-type');
                if(p.hasClass('childChatWindow')==true && $('body').hasClass('fullChatWindowSize') == false ){
                    $('.childChatWindow').removeClass('onchatMaxWin');
                    if(p.hasClass('offCh')==true){
                        p.removeClass('offCh');
                        //$.removeCookie('chatWindowOff-'+chatType+'-'+SESS_USER_ID,{path:'/'});
                    }
                    $('.allUserChat').removeAttr('style');
                    p.removeAttr('style');
                    $('body').addClass('fullChatWindowSize');
                    p.addClass('onchatMaxWin');
                    $('.fa-expand', tsl).addClass('fa-compress').removeClass('fa-expand');
                    $('.iconSetChat .fa-angle-up', p).removeClass('fa-angle-up').addClass('fa-angle-down');

                    
                }else{  
                if(p.hasClass('childChatWindow')==true){                
                    $('.fa-compress', tsl).addClass('fa-expand').removeClass('fa-compress');
                    $('body').removeClass('fullChatWindowSize');
                    $.reRarrangechatWindow();

                    p.css({height:354});
                    /*if($.cookie('chatWindowType-alluser-'+SESS_USER_ID)!='max'){
                        var allUser = $('.dbchatWrapper.allUserChat');
                         if(allUser.hasClass('offCh')==true){
                            allUser.css({top:'100%'});
                         }
                          if(allUser.hasClass('onchatMaxWin')==false){
                         allUser.css({height:400});
                         }

                       
                    }*/
                    p.removeClass('onchatMaxWin');
                }else{
                    p.removeAttr('style');
                    if(p.hasClass('onchatMaxWin')==false){
                        if($('body').hasClass('activeSocialfeedSidebar')==true){
                             p.css({top:'60%'});
                             alert('')
                        }else{
                             p.css({top:0});
                        }
                       
                        p.addClass('onchatMaxWin');
                        p.removeClass('offCh');
                        $('.fa-expand', tsl).addClass('fa-compress').removeClass('fa-expand');
                        $('.iconSetChat .fa-angle-up', p).addClass('fa-angle-down').removeClass('fa-angle-up');
                        //$.cookie('chatWindowType-'+chatType+'-'+SESS_USER_ID, 'max',{path:'/'});
                       // $.removeCookie('chatWindowOff-'+chatType+'-'+SESS_USER_ID,{path:'/'});

                    }else{
                        p.removeClass('onchatMaxWin');
                        p.css({height:400});
                        $('.fa-compress', tsl).addClass('fa-expand').removeClass('fa-compress');
                        //$('.iconSetChat .fa-angle-down', p).addClass('fa-angle-up').removeClass('fa-angle-down');
                        //$.removeCookie('chatWindowType-'+chatType+'-'+SESS_USER_ID,{path:'/'});
                    }
                }

                }

               
                $('textarea', p).focus().elastic(); 

                  var sh = $('.chatMessageContainer' , p).prop('scrollHeight');
                        $('.chatMessageContainer' , p).scrollTop(sh);
             
                
});

$('body').on('click', '.expandCollapse', function(event) {
    var tsl = $(this);
    var p = tsl.closest('.dbchatWrapper');
    var chatType = p.attr('chat-type');
    if($('body').hasClass('fullChatWindowSize')==true){
        $('body').removeClass('fullChatWindowSize');
        $.reRarrangechatWindow();
        var allUser = $('.dbchatWrapper.allUserChat');
         if(allUser.hasClass('offCh')==true){
            allUser.css({top:'100%'});
         }else if( allUser.hasClass('onchatMaxWin')==true){
            allUser.css({height:'auto'});
         }
        else{
             allUser.css({height:400});
         }
    }
   
    p.toggleClass('offCh');
    if(p.hasClass('offCh')==true){
        p.css({top:'100%'});
        p.removeClass('onchatMaxWin');
       // $.cookie('chatWindowOff-'+chatType+'-'+SESS_USER_ID, 'true', {path:'/'});
        //$.removeCookie('chatWindowType-'+chatType+'-'+SESS_USER_ID,{path:'/'});
        $('.fa-angle-down', tsl).removeClass('fa-angle-down').addClass('fa-angle-up');
         $('.fa-compress', p).addClass('fa-expand').removeClass('fa-compress');
    }else{
       if($('.chatContainer',p).prop('scrollHeight')>windH){
             p.css({top:0, height:'auto'});
             p.addClass('onchatMaxWin');
              $('.fa-expand', p).addClass('fa-compress').removeClass('fa-expand');
        }else{
             p.css({top:'initial'});
             
        }
       
        $('.fa-angle-up', tsl).removeClass('fa-angle-up').addClass('fa-angle-down');
        
        //$.removeCookie('chatWindowOff-'+chatType+'-'+SESS_USER_ID,{path:'/'});
        
    }
    
});



$('body').on('click', '.chatCloseUser',function (){
    var tsl = $(this);
    var p = tsl.closest('.dbchatWrapper');
    var pleft = p.css('right');     
    var chatType = p.attr('chat-type');
    var totalHideWindow = $('body .childChatWindow:hidden').size();  
    $('body').removeClass('fullChatWindowSize');
    var childCount = $('.childChatWindow').length;
    if(childCount==1){
        $('html').removeClass('openedChildWindowNotice');
    }
    

     $('body .childChatWindow:hidden:last').css({right:pleft, bottom:-400}).show().animate({bottom:0});
        $(this).closest('.dbchatWrapper').remove();
        //$.removeCookie('openedChildWindow-'+chatType+'-'+SESS_USER_ID,{path:'/'});
        //$.removeCookie('chatWindowType-'+chatType+'-'+SESS_USER_ID,{path:'/'});
        //$.removeCookie('chatWindowOff-'+chatType+'-'+SESS_USER_ID,{path:'/'});

    if(totalHideWindow<1){
        $.reRarrangechatWindow();
    }
});


$('body').on('click', '.dbchatWrapper[chat-type="alluser"] .chatContainer  li:not(.itsSeprator)',function (){
            var tsl = $(this);          
            var p = tsl.closest('.dbchatWrapper');
            var dataId = tsl.attr('data-id');
            var chatSource = tsl.attr('chat-source');
            var groupOwnerId = tsl.attr('group-owner');
            var isAccepted = tsl.attr('is-accepted');
            var chatW = p.width();
            var uName = $('.Uname',tsl).text();
            var userPicName =  tsl.attr('data-pic');
            var userPicSrc =  BASE_URL+userImagePath+userPicName //$('.chatUserPic img',tsl).attr('src');
            var onlineStatus = 'offline';
             var groupOption ='';

                 $('html').removeClass('openedChildWindowNotice');
                   setTimeout(function(){
                        $('html').addClass('openedChildWindowNotice');

                    }, 300);
              

             if($('.liveStatus',tsl).hasClass('online')==true){
                onlineStatus='online';
             }
             var isGroupOwnerOption ='';
             if(SESS_USER_ID==groupOwnerId){
               isGroupOwnerOption='<li><a href="javascript:void(0);" class="addRemoveUsersInGroup">Add/Remove Users</a></li>';
             }
             var onelineStatusHtml = '<span class="showMYstatus"><span class="liveStatus '+onlineStatus+'"></span><b>'+onlineStatus+'</b></span>';
            var isAcceptedDisplay = '';
            var isAcceptedDisabled = '';
            var defaultChatLoad='Loading history...';
             
            if(isAccepted==0 && isAccepted!='') {
                 isAcceptedDisplay = 'style="display:none;"';
                 isAcceptedDisabled = 'disabled';
                   defaultChatLoad = '';
            }
             if(chatSource=='group'){
                userPicSrc = BASE_URL+'/userpics/chat-group-white.png';
                onelineStatusHtml = '';
                groupOption = '<div class="dropDown" '+isAcceptedDisplay+'>\
                                    <i class="fa fa-chevron-circle-down"></i>\
                                    <ul class="dropDownList">\
                                        <li><a href="javascript:void(0);" class="leaveFromGroup">Leave Group</a></li>'+isGroupOwnerOption+'\
                                    </ul>\
                                </div>';
             }
             

            var userHtml = '<div class="userPicTitle">'+groupOption+'<img src="'+userPicSrc+'"></div> <div class="oneline unameTitle"><strong>'+uName+'</strong>'+onelineStatusHtml+'</div>';
            var chatWindow = $('body .childChatWindow[chat-type="'+dataId+'"]');
            var isFullChat = $('body').hasClass('fullChatWindowSize');
            var groupChatNoficationHtml = '';
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
                var fileSharingChat = '<form id="fileSharingChat_'+dataId+'" class="dropzone">\
                                          <div class="fallback">\
                                            <input name="file" type="file" multiple />\
                                          </div>\
                                        </form>';
                
                 if(isAccepted==0 && isAccepted!=''){
                    var grpOwnerName = tsl.attr('group-owner-name');
                    var grpOwnerPic = tsl.attr('group-owner-pic');
                    groupChatNoficationHtml = '<div class="groupChatNoficationBox">\
                                                    <a href="javascript:void(0);" class="chatAccpetGroupBtn"><i class="fa fa-check"></i> Accept</a>\
                                                    <a href="javascript:void(0);" class="chatRejectGroupBtn"><i class="fa fa-times"></i> Reject</a>\
                                                    <div class="groupNameDes"><div class="grpOwnerInfo"><img src="'+BASE_URL+userImagePath+grpOwnerPic+'"><br>'+grpOwnerName+'</div> <i>has created a chat Group</i></div>\
                                               </div>';
                }

                var chatMessageContainer = groupChatNoficationHtml+'<div class="chatMessageContainer"><ul><li style="text-align:center;">'+defaultChatLoad+'</li></ul></div>';
                var emoList='';
                var checkU='';
                 $.each(arrayEmotions, function (index, value){

                       if(checkU!=value.replaceValue){
                            emoList += '<li><a href="javascript:void(0);" class="chatEmotions small emo_'+value.replaceValue+'" title="'+value.title+' '+value.value+'">'+value.value+'</a></li>';
                        }
                         checkU = value.replaceValue;
                });
               
                 var chatEditor = '<div class="chatInputAreaBox" '+isAcceptedDisplay+'>\
                                                <div class="emoList"><ul>'+emoList+'</ul></div>\
                                                <a href="javascript:void(0);" class="fa fa-plus-circle addIntoChat">'+fileSharingChat+'</a>\
                                                <a href="javascript:void(0);" class="fa fa-smile-o addSmilyIntoChat"></a>\
                                                <textarea '+isAcceptedDisabled+'></textarea>\
                                            </div>';

                 
                var chatWindowInputField = chatMessageContainer+chatEditor;

                if( $('body').hasClass('fullChatWindowSize')==true ){
                    lastWindowLft = '';
                }
                if(lastWindowLft!=''){  
                    lastWindowLft   = lastWindowLft+'px';   
                }       
                $.chatWindows({class:'childChatWindow',userPic:userPicName, chatSource:chatSource, userType:dataId,userName:userHtml,content:chatWindowInputField , positions:lastWindowLft, highlight:'', search:''});
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
    $('.chatMessageContainer').scroll(function(event) {
            event.stopPropagation();
             var tsl = $(this);
           var scrTop = tsl.scrollTop();
           if(scrTop<20){
            $('.loadpreviousChat',tsl).addClass('bounceInDown');
           }else{
            $('.loadpreviousChat',tsl).removeClass('bounceInDown');
           }
            
        });
});
    


    // chating textarea starting..
var checkTypeCount = 0;
$('body').on('keyup','.chatInputAreaBox textarea', function (e){
    e.stopPropagation();
    var thisEl = $(this);
    chatTxt = $.trim(thisEl.val());
   
    chatInner = thisEl.closest('.childChatWindow');
    var recieverID = chatInner.attr('chat-type');

    var chatSource = chatInner.attr('chat-source');

    today = new Date();
    var chatTime = today.format("HH:MM");
    if(chatTxt!='' && checkTypeCount==0){
        socket.emit('started typing',  {myname:SESS_USER_NAME, mychat : chatTxt, clientID : clientID, recieverID:recieverID,senderID:SESS_USER_ID, chatSource : chatSource});
        checkTypeCount = 1;
    } else{
        socket.emit('started typing',  {myname:SESS_USER_NAME, mychat : '',clientID : clientID, recieverID:recieverID,senderID:SESS_USER_ID, chatSource : chatSource});
        checkTypeCount = 0;
    }
      
    if(e.keyCode == 13 && !e.shiftKey && chatTxt!='') { 
        //console.log('ddd');
        senderLayout(thisEl, chatTxt);
        socket.emit('my chat',  {myname:SESS_USER_NAME, mychat : chatTxt,clientID : clientID, recieverID:recieverID,senderID:SESS_USER_ID, chatSource : chatSource, userPicture : userpic});
        socket.emit('started typing',  {myname:SESS_USER_NAME, mychat : '',clientID : clientID, recieverID:recieverID,senderID:SESS_USER_ID, chatSource : chatSource});
        checkTypeCount = 0;
     }      

});


$('body').on('focusin','.chatInputAreaBox textarea', function (e){
    var thisEl = $(this);
    p = thisEl.closest('.childChatWindow');
    p.addClass('imOnIt');
     var popupID = p.attr('chat-type');
     
    $(".childChatWindow[chat-type='"+popupID+"'] ").removeClass('heightLightChat')
    $(".dbchatWrapper[chat-type='alluser'] li[data-id='"+popupID+"']").removeClass('heightLightChat');

       var totalPing =   $(".dbchatWrapper[chat-type='alluser'] li.heightLightChat").size();
                if(totalPing!=''){
                     $(".childChatWindow .backToUserList, .chatStartNow").addClass('heightLightChat');
                }else{
                    $(".childChatWindow .backToUserList, .chatStartNow").removeClass('heightLightChat');
                }
    
    //if(PageTitleNotification.Vars.Interval!=null) PageTitleNotification.Off();
    
});
$('body').on('focusout','.chatInputAreaBox textarea', function (e){
    var thisEl = $(this);
    thisEl.elastic(); 
    p = thisEl.closest('.childChatWindow');
    p.removeClass('imOnIt');
});

$('body').on('keypress','.chatInputAreaBox textarea', function (e){
    if(e.keyCode == 13 && !e.shiftKey) { 
    return false;
     }
});

socket.on('my group members',function(members,grpDtls){
    var chatUserList='';
    //members.memberid
    var p = $('.dbchatWrapper[chat-type="'+grpDtls.groupID+'"][chat-source="group"]');
    p.addClass('activeSearchBox');
    $.each(allUserSelectJson, function(i, value){
        // console.log(members.indexOf(value.id));
        var userAct = '<a href="javascript:void(0);" class="addUserToThisGroup adrUsBtn">Add</a>';

         $.each(members, function(index, val){
            if(value.id == val.memberid){
                userAct = '<a href="javascript:void(0);" class="removeUserToThisGroup adrUsBtn">remove</a>';
            }
         });
       
            chatUserList +='<li data-id="'+value.id+'"><img src="'+BASE_URL+userImagePath+value.src+'"><div class="adre_userList"><span class="userNameAdre">'+value.text+'</span>'+userAct+'</div></li>';
       
    });
    var addRemoveUserHTml ='<div class="searchUserListingChildW">\
                             <div class="searchBoxOnChat">\
                                <input type="text" value="" placeholder="search user">\
                                <a href="javascript:void(0);" class="closeUserSearchBar">\
                                    <i class="fa fa-times-circle"></i>\
                                </a>\
                                <i class="fa fa-search"></i>\
                            </div>\
                             <div  class="childUList">\
                                <ul>\
                                    '+chatUserList+'\
                                </ul>\
                            </div>\
                        </div>';
    $('.searchUserListingChildW', p).remove();
    $('.chatContainer', p).prev().after(addRemoveUserHTml);
    setTimeout(function(){
        $('.searchBoxOnChat input ',p).focus();
    }, 500);

})

$('body').on('click','.addRemoveUsersInGroup', function (e){
    var thisEl = $(this);
    var p = thisEl.closest('.childChatWindow');
    var grpID = p.attr('chat-type');
    socket.emit('group members',  {clientID : clientID, groupID:grpID, owner:SESS_USER_ID});
    
});

$('body').on('click','.leaveFromGroup', function (e){
    var thisEl = $(this);
    var p = thisEl.closest('.childChatWindow');
  var leaveGrpNotHtml= '<div class="chatNotiRemoveGrpWrp">\
       <span>'+groupLeaveMsg+'</span>\
       <a href="javascript:void(0);" class="chatLeaveOkGroupBtn circleBtn" title="Leave Group"><i class="fa fa-check"></i></a>\
       <a href="javascript:void(0);" class="chatLeaveCancelGroupBtn circleBtn" title="Cancel"><i class="fa fa-times"></i></a>\
   </div>';
   $('.chatNotiRemoveGrpWrp',p).remove();
   $('.chatContainer',p).append(leaveGrpNotHtml);
   $('.chatNotiRemoveGrpWrp',p).animate({right:0});
   $('.dropDown',p).addClass('off');
});

$('body').on('mouseleave','.childChatWindow .dropDown', function (e){
    $(this).removeClass('off');
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
    if(isMobile==null){
    var lastWindowLft = 0;
     $.reRarrangechatWindow();
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
}


$(window).resize(function (){
    if($('body').hasClass('fullChatWindowSize')==false){
        $.winResize();
      }
      var p =  $('.dbchatWrapper.allUserChat');
      var sh = p.prop('scrollHeight');
      if(windH<sh){
        if(p.hasClass('.onchatMaxWin')!=true){
            $('.dbchatWrapper.allUserChat .chatMinmax').trigger('click');
        }
      }

});
 $(window).focus(function() {
            window_focus = true;
        }).blur(function() {
            window_focus = false;
        });

    $.usergoesoffline = function (olStatus){
        socket.emit('usergoesoffline',{ 'olStatus':olStatus,'userID' : SESS_USER_ID,'clientID' :clientID,'full_name': SESS_USER_NAME, 'ProfilePic':userpic });

    };

    $.callallchatusers = function (olStatus,userID,uname,picture){
        //alert(userID)
        socket.emit('appendnewuserusers',{'olStatus':olStatus, 'senderID' : SESS_USER_ID,'recieverID':userID, 'clientID' :clientID,'full_name': getdecodename(uname), 'ProfilePic':picture });

        socket.emit('appendnewuserusers', {'olStatus':olStatus, 'senderID' : userID,'recieverID':SESS_USER_ID, 'clientID' :clientID, 'full_name': SESS_USER_NAME, 'ProfilePic':userpic });
    };

    var PageTitleNotification = {
        Vars:{
            OriginalTitle: document.title,
            Interval: null
        },    
        On: function(notification, intervalSpeed){
            var _this = this;
            _this.Vars.Interval = setInterval(function(){
                 document.title = (_this.Vars.OriginalTitle == document.title)
                                     ? notification
                                     : _this.Vars.OriginalTitle;
            }, (intervalSpeed) ? intervalSpeed : 1000);
        },
        Off: function(){
            clearInterval(this.Vars.Interval);
            document.title = this.Vars.OriginalTitle;   
        }
    }


$.notification = function (options){
        defaults = {
            text:'',
            title:'',
            icon:'',
            dir:'rtl',
            permission:Notification.permission,
            click:$.noop,
            close:$.noop,
            show:$.noop,
            error:$.noop
        }
     var settings = $.extend({}, defaults, options);
        var txt = 'screenLeft : '+window.screenLeft;
         txt += 'screenTop : '+window.screenTop;
       // txt += 'screen width : '+screen.width;

       $(window).focus(function() {
            window_focus = true;
        }).blur(function() {
            window_focus = false;
        });

     if(settings.permission==='granted' && window_focus==false && isMobile==null){
       
         var instance = new Notification(
                 settings.title,{
                    body:settings.text,
                    icon: settings.icon

                }
            );

            instance.onclick = function () {
                // var windOpened =  window.open(location.href,'_self',false);
                  if(window.location==location.href){
                      window.focus();
                     }
                    
                //settings.click.call();
            };
            instance.onerror = function () {
                settings.error.call();
            
            };
            instance.onshow = function () {
                settings.show.call();
            };
            instance.onclose = function () {
                settings.close.call();
            };

            setTimeout(instance.close.bind(instance), 4000);
        }


        return false;
}



$('body').on('click', '.chatStartNow',function(){
    $('html').addClass('startChatWindow').addClass('tempChatWindowClass');
});
$('body').on('click', '.backToMainFeed',function(){
    $('html').removeClass('startChatWindow');
    setTimeout(function (){
        $('html').removeClass('tempChatWindowClass');
    }, 500);
});
$('body').on('click', '.backToUserList',function(){
    $('html').removeClass('openedChildWindowNotice');
    
});



}); // End of main function
 

//************** Date format functions **************************//

 /*
     * Date Format 1.2.3
     * (c) 2007-2009 Steven Levithan <stevenlevithan.com>
     * MIT license
     *
     * Includes enhancements by Scott Trenda <scott.trenda.net>
     * and Kris Kowal <cixar.com/~kris.kowal/>
     *
     * Accepts a date, a mask, or a date and a mask.
     * Returns a formatted version of the given date.
     * The date defaults to the current date/time.
     * The mask defaults to dateFormat.masks.default.
     */

    var dateFormat = function () {
        var    token = /d{1,4}|m{1,4}|yy(?:yy)?|([HhMsTt])\1?|[LloSZ]|"[^"]*"|'[^']*'/g,
            timezone = /\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,
            timezoneClip = /[^-+\dA-Z]/g,
            pad = function (val, len) {
                val = String(val);
                len = len || 2;
                while (val.length < len) val = "0" + val;
                return val;
            };
    
        // Regexes and supporting functions are cached through closure
        return function (date, mask, utc) {
            var dF = dateFormat;
    
            // You can't provide utc if you skip other args (use the "UTC:" mask prefix)
            if (arguments.length == 1 && Object.prototype.toString.call(date) == "[object String]" && !/\d/.test(date)) {
                mask = date;
                date = undefined;
            }
    
            // Passing date through Date applies Date.parse, if necessary
            date = date ? new Date(date) : new Date;
            if (isNaN(date)) throw SyntaxError("invalid date");
    
            mask = String(dF.masks[mask] || mask || dF.masks["default"]);
    
            // Allow setting the utc argument via the mask
            if (mask.slice(0, 4) == "UTC:") {
                mask = mask.slice(4);
                utc = true;
            }
    
            var    _ = utc ? "getUTC" : "get",
                d = date[_ + "Date"](),
                D = date[_ + "Day"](),
                m = date[_ + "Month"](),
                y = date[_ + "FullYear"](),
                H = date[_ + "Hours"](),
                M = date[_ + "Minutes"](),
                s = date[_ + "Seconds"](),
                L = date[_ + "Milliseconds"](),
                o = utc ? 0 : date.getTimezoneOffset(),
                flags = {
                    d:    d,
                    dd:   pad(d),
                    ddd:  dF.i18n.dayNames[D],
                    dddd: dF.i18n.dayNames[D + 7],
                    m:    m + 1,
                    mm:   pad(m + 1),
                    mmm:  dF.i18n.monthNames[m],
                    mmmm: dF.i18n.monthNames[m + 12],
                    yy:   String(y).slice(2),
                    yyyy: y,
                    h:    H % 12 || 12,
                    hh:   pad(H % 12 || 12),
                    H:    H,
                    HH:   pad(H),
                    M:    M,
                    MM:   pad(M),
                    s:    s,
                    ss:   pad(s),
                    l:    pad(L, 3),
                    L:    pad(L > 99 ? Math.round(L / 10) : L),
                    t:    H < 12 ? "a"  : "p",
                    tt:   H < 12 ? "am" : "pm",
                    T:    H < 12 ? "A"  : "P",
                    TT:   H < 12 ? "AM" : "PM",
                    Z:    utc ? "UTC" : (String(date).match(timezone) || [""]).pop().replace(timezoneClip, ""),
                    o:    (o > 0 ? "-" : "+") + pad(Math.floor(Math.abs(o) / 60) * 100 + Math.abs(o) % 60, 4),
                    S:    ["th", "st", "nd", "rd"][d % 10 > 3 ? 0 : (d % 100 - d % 10 != 10) * d % 10]
                };
    
            return mask.replace(token, function ($0) {
                return $0 in flags ? flags[$0] : $0.slice(1, $0.length - 1);
            });
        };
    }();
    
    // Some common format strings
    dateFormat.masks = {
        "default":      "ddd mmm dd yyyy HH:MM:ss",
        shortDate:      "m/d/yy",
        mediumDate:     "mmm d, yyyy",
        longDate:       "mmmm d, yyyy",
        fullDate:       "dddd, mmmm d, yyyy",
        shortTime:      "h:MM TT",
        mediumTime:     "h:MM:ss TT",
        longTime:       "h:MM:ss TT Z",
        isoDate:        "yyyy-mm-dd",
        isoTime:        "HH:MM:ss",
        isoDateTime:    "yyyy-mm-dd'T'HH:MM:ss",
        isoUtcDateTime: "UTC:yyyy-mm-dd'T'HH:MM:ss'Z'"
    };
    
    // Internationalization strings
    dateFormat.i18n = {
        dayNames: [
            "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat",
            "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"
        ],
        monthNames: [
            "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
            "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
        ]
    };
    
    // For convenience...
    Date.prototype.format = function (mask, utc) {
        return dateFormat(this, mask, utc);
    };





// today = new Date();
// var dateString = today.format("ddd-mmm-yy");


