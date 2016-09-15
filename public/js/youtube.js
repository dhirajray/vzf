var player;
var videoStartStatus = false;
var videoFinished = false;
 function onYouTubeIframeAPIReady() {
           var dbid = $('#dbid').val();
           var controls ;
           $.ajax({
              url: BASE_URL+"/dbeedetail/youtubevideocontrols",
              type: "POST",
              dataType:"json",
              data: {'dbeeid':dbid},
              success:function(data)
              {
                 if(data.controls==true){
                  controls = 1;
                 }else if(data.controls==false){
                  controls = 0;
                 }
                player = new YT.Player('playerss', {
                    height: '390',
                    width: '640',
                    playerVars : {'rel':0,'autohide':1,'disablekb':1,'enablejsapi':1,'controls':controls,"wmode":"transparent","modestbranding":1},
                    events: {
                      'onReady': onPlayerReady,
                      'onStateChange': onPlayerStateChange
                    }
               });
              }
        });
      }
      function onPlayerReady(event) {
         
          videoID = $('#videoID').val();
          dbeeid = $('#dbid').val();
          $.ajax({
            url: BASE_URL+"/dbeedetail/youtubevideoplay",
            type: "POST",
            dataType:"json",
            data: {'dbeeid':dbeeid},
            success:function(data)
            {  
              if($('#login').val())
                setInterval("checkvideopost(" + dbeeid + ")", 1000);
              
              $('.AskAnExpert').hide();
              if(isMobile==false){
                if(!FlashDetect.installed || !FlashDetect.versionAtLeast(9)){
                  $('.videoOverlay').hide();
                  $('.youtubeWarning').remove();
                }
              }

               if(data.timeexpired==false && data.removeVideoLayer==false) {
                $('.clock').FlipClock(data.expireInSeconds, {
                  clockFace: 'DailyCounter',
                  countdown: true
                });
                $('.youtubeWarning').show();
              }
                
                if(data.userAttendStatus==0 && data.invitationType=='protected' && data.eventJoinLinkHide==false && data.isexpert==false && $("#dbeeuser").val())
                {
                    var SpAcceptPopup = '<div style="margin-top:5px; text-align:center" >Click the button below to send a joining request to event admin.</div>\
                    <div style="margin-top:5px; text-align:center" >\
                    <a href="javascript:void(0);" class=" btn btn-yellow joinRequest" data-id="'+ dbeeid +'" >Request to join</a>\
                    <a href="javascript:void(0);" class=" btn" id="RejectDbeeRequest" >Cancel</a>\
                    </div>';
                    $messageWarning(SpAcceptPopup);

                }else if(data.userAttendStatus==0 && data.invitationType=='public' && data.eventJoinLinkHide==false && data.isexpert==false && $("#dbeeuser").val())
                {
                    var SpAcceptPopup = '<div style="margin-top:5px; text-align:center" >Click the button below to join the event and become an attendee.</div>\
                    <div style="margin-top:5px; text-align:center" >\
                    <a href="javascript:void(0);" class=" btn btn-yellow joinRequest" data-id="'+ dbeeid +'"  >Join</a>\
                    </div>';
                    $messageWarning(SpAcceptPopup);
                }
                if(data.videoLoadStatus== true)
                {
                  player.cueVideoById({'videoId': videoID,'startSeconds':data.lapsTime});
                  $('.miniPostWraper').attr('userlogin', '0');
                  commentBlockStatus = true;
                  $('#dbee-post-comment').hide();
                }
                if(data.videoStartStatus==true && videoStartStatus == false && videoFinished==false)
                {
                    playVideo();
                    videoStartStatus = true;
                    $('.AskAnExpert').show();
                }
                if(data.removeVideoLayer==true)
                {
                    $('.videoOverlay').hide();
                    $('.youtubeWarning').remove();
                    $('.AskAnExpert').show();
                }
                if(data.commentOpenStatus==true)
                {
                    $('.miniPostWraper').attr('userlogin', '1');
                    commentBlockStatus = false;
                    $('#dbee-post-comment').show();
                    $('.commentWillStart').hide();
                }else
                {
                    $('.miniPostWraper').attr('userlogin', '0');
                    commentBlockStatus = true;
                    $('#dbee-post-comment').hide();
                }
                if(data.userAttendStatus==0 && data.timeexpired==true)
                {
                    $('.miniPostWraper').attr('userlogin', '0');
                    commentBlockStatus = true;
                    $('#dbee-post-comment').hide();
                    $('.videoOverlay').show();
                    $('.videoOverlay').html('<div class="youtubeWarning missevent" ><h1>This video event has now finished. <a href="'+BASE_URL+'/myhome#videobroadcast">Click here</a> to view other forthcoming video events.</h1></div>');
                }
            }
          });
      }
      function onPlayerStateChange(event) 
      {
        if(event.data==YT.PlayerState.ENDED)
        { 
            commentsStatus = true;
            $.ajax({
            url: BASE_URL+"/dbeedetail/youtubevideoend",
            type: "POST",
            dataType:"json",
            data: {'dbeeid':dbeeid,commentsStatus:commentsStatus},
            success:function(data)
            {
              videoFinished = true;
              videoStartStatus = true;
              if(data.status==true)
              {
                commentsStatus = true; 
                commentBlockStatus = false; 
                $('.commentWillStart').hide();
                $('#dbee-post-comment').show();
                $('.miniPostWraper').attr('userlogin', '1');
                if(data.videoStartStatus==false){
                 stopVideo();
                }
                $('#warningMsg').hide();
              }
            }
          });
        }

        if(event.data==YT.PlayerState.PLAYING)
        {
            $.ajax({
            url: BASE_URL+"/dbeedetail/shouldbeplay",
            type: "POST",
            dataType:"json",
            data: {'dbeeid':dbeeid},
            success:function(data)
            { 
              if(data.case==0 && data.videoStartStatus==true)
              {
                  var casePopup = '<div style="margin-top:5px; text-align:center" >This video event has now finished. <a href="'+BASE_URL+'/myhome#videobroadcast">Click here</a> to view other forthcoming video events.</div>\
                    <div style="margin-top:5px; text-align:center" >\
                    <a href="javascript:void(0);" class="btn btn-yellow closePopup" >OK</a>\
                    </div>';
                  $messageWarning(casePopup);
                  
                  stopVideo();
                  $('.videoOverlay').show();
                  $('.videoOverlay').html('<div class="youtubeWarning" ><h1>This video event has now finished. <a href="'+BASE_URL+'/myhome#videobroadcast">Click here</a> to view other forthcoming video events.</h1></div>');
                  $('#warningMsg').css({width:500, marginLeft:'-250px'});
                  setTimeout(function() {
                     $('#warningMsg').css({width:500, marginLeft:'-250px'});
                  }, 500);
              }
              else if(data.videoStartStatus==true && data.userAttendStatus==1){
                $('.videoOverlay').remove();
              }
              if(data.videoStartStatus==false){
                stopVideo();
              }
              
            }
          });
            //alert(getCurrentTime());
        }

        if(event.data==YT.PlayerState.PAUSED)
        {
           $.ajax({
            url: BASE_URL+"/dbeedetail/shouldbepause",
            type: "POST",
            dataType:"json",
            data: {'dbeeid':dbeeid},
            success:function(data)
            {
              if(data.pause==false)
               playVideo();
            }
          });
          
        }

      }

      function setSize(width,height)
      {
         if (player) { 
           player.setSize(width,height);
          }
      }

    
      function stopVideo() {
      
       if (player) { 
           player.stopVideo();
        }
      }

      function getCurrentTime()
      {
        return player.getDuration();
      }

      function playVideo() {
        if (player) 
        {
          player.playVideo();
        }
      }
      function play_clicked()
      {
        if (player) 
        {
          player.playVideo();
        }
      }
      function pauseVideo() {
        if (player) {
          player.pauseVideo();
        }
      }

      function seekTime(time)
      {
         if (player) 
         {
            player.seekTo(time);
         }
      }

      function muteVideo() {
        if(player) {
          player.mute();
        }
      }

      function unMuteVideo() {
        if(player) {
          player.unMute();
        }
      }
      function checkvideopost(DbeeID)
      {
        console.log(DbeeID);
        socket.emit('videoeventstart', DbeeID,clientID);
      }
      
