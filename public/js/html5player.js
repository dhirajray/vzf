
var _video;


$(function() {

  window._wq = window._wq || [];
  _wq.push({ "_all": function(video) {
  console.log("This will run for every video on the page. Right now I'm on this one:", video);
  _video = video;
   
    
    video.ready(function() {
      // run this function when/if the video is ready.
      // this will usually fire shortly after `embedded(callbackFn)`.
      init();
    });
    

    _video.bind("pause", pauseEvent);
    _video.bind("play", play_clicked);

    video.bind("end", function(t) {
      console.log("the video ended");
      endEvent();
    });

  }});
});
function init() 
{
 
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
              <a href="javascript:void(0);" class=" btn btn-yellow joinRequest" data-id="'+ dbeeid +'"  >Request to join</a>\
              <a href="javascript:void(0);" class=" btn" id="RejectDbeeRequest" >Cancel</a>\
              </div>';
              $messageWarning(SpAcceptPopup);

          }else if(data.userAttendStatus==0 && data.invitationType=='public' && data.eventJoinLinkHide==false && data.isexpert==false && $("#dbeeuser").val())
          {
              var SpAcceptPopup = '<div style="margin-top:5px; text-align:center" >Click the button below to join the event and become an attendee.</div>\
              <div style="margin-top:5px; text-align:center" >\
              <a href="javascript:void(0);" class=" btn btn-yellow joinRequest" data-id="'+ dbeeid +'" >Join</a>\
              </div>';
              $messageWarning(SpAcceptPopup);
          }
          if(data.videoStartStatus==true)
          {
              playVideo();
              seekTime(data.lapsTime);
              $('.videoOverlay').remove();
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

function pauseEvent()
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

function endEvent(e) 
{
    commentsStatus = true;
    $.ajax({
    url: BASE_URL+"/dbeedetail/youtubevideoend",
    type: "POST",
    dataType:"json",
    data: {'dbeeid':dbeeid,commentsStatus:commentsStatus},
    success:function(data)
    {
      if(data.status==true)
      {
        $('#dbee-post-comment').show();
        $('.miniPostWraper').attr('userlogin', '1');
        $('#warningMsg').hide();
      }else{
        seekTime(data.differenceInSeconds);
      }
    }
  });
}

function play_clicked() 
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
            $('.videoOverlay').html('<div class="youtubeWarning" ><h1>This video event has now finished. <a href="/myhome#videobroadcast">Click here</a> to view other forthcoming video events.</h1></div>');
            $('#warningMsg').css({width:500, marginLeft:'-250px'});
            setTimeout(function() {
               $('#warningMsg').css({width:500, marginLeft:'-250px'});
            }, 500);
        }
        else if(data.videoStartStatus==true && data.userAttendStatus==1){ 
          $('.videoOverlay').remove();
          playVideo();
        }
        if(data.videoStartStatus==false){
          stopVideo();
        }
      }
    });
}

function playVideo() { 
   _video.play();
}

function stopVideo() { 
   _video.pause();
}

function pauseVideo() {
    _video.pause();
}

function seekTime(time)
{
  if(time)
    _video.time(time);
    playVideo();
}

function checkvideopost(DbeeID)
{
  console.log(DbeeID);
  socket.emit('videoeventstart', DbeeID,clientID);
}

