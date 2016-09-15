$(function(){


$('.socialActionsignin input').click(function(event) {
      event.preventDefault();
      var globelSettingVal = 1;
      var calling = $(this).attr('caller');
      var thisEl  =$(this);
      var msg = '';

      if(calling=='fbsociallogin')
      {
        if(thisEl.is(':checked')==false) {
            msg = 'Are you sure you want to allow users to login their Facebook accounts?';
            globelSettingVal=0;
        }else{
             msg ='Are you sure you want to stop users from login their Facebook accounts?';
        }
      }
      if(calling=='twsociallogin')
      {
        if(thisEl.is(':checked')==false) {
            msg = 'Are you sure you want to allow users to login their Twitter accounts?';
            globelSettingVal=0;
        }else{
             msg ='Are you sure you want to stop users from login their Twitter accounts?';
        }
      }
      if(calling=='gpsociallogin')
      {
        if(thisEl.is(':checked')==false) {
            msg = 'Are you sure you want to allow users to login their Google+ accounts?';
            globelSettingVal=0;
        }else{
             msg ='Are you sure you want to stop users from login their Google+ accounts?';
        }
      }
      if(calling=='ldsociallogin')
      {
        if(thisEl.is(':checked')==false) {
            msg = 'Are you sure you want to allow users to login their LinkedIn accounts?';
            globelSettingVal=0;
        }else{
             msg ='Are you sure you want to stop users from login their LinkedIn accounts?';
        }
      }

      if(calling=='allsocialsignin')
      {
        if(thisEl.is(':checked')==false) {
            msg = "Switching this master setting to 'ON' will allow all users to link their social accounts.<br><br>Do you wish to continue?";
            globelSettingVal=0;
        }else{
             msg ="Switching this master setting to 'OFF' will prevent all users from being able to link their social accounts, however they will still be able to share posts to their other social accounts.<br><br>Do you wish to continue?";
        }
      }
      if(calling=='fbsocialsignin')
      {
        if(thisEl.is(':checked')==false) {
            msg = 'Are you sure you want to allow users to link their Facebook accounts?';
            globelSettingVal=0;
        }else{
             msg ='Are you sure you want to stop users from linking their Facebook accounts?';
        }
      }
      if(calling=='twsocialsignin')
      {
        if(thisEl.is(':checked')==false) {
            msg = 'Are you sure you want to allow users to link their Twitter accounts?';
            globelSettingVal=0;
        }else{
             msg ='Are you sure you want to stop users from linking their Twitter accounts?';
        }
      }
      if(calling=='gpsocialsignin')
      {
        if(thisEl.is(':checked')==false) {
            msg = 'Are you sure you want to allow users to link their Google+ accounts?';
            globelSettingVal=0;
        }else{
             msg ='Are you sure you want to stop users from linking their Google+ accounts?';
        }
      }
      if(calling=='ldsocialsignin')
      {
        if(thisEl.is(':checked')==false) {
            msg = 'Are you sure you want to allow users to link their LinkedIn accounts?';
            globelSettingVal=0;
        }else{
             msg ='Are you sure you want to stop users from linking their LinkedIn accounts?';
        }
      }

      if(calling=='allsocialsignininvite')
      {
        if(thisEl.is(':checked')==false) {
            msg = "Switching this master setting to 'ON' will allow all users to invite their social connections.<br><br>Do you wish to continue?";
            globelSettingVal=0;
        }else{
             msg ="Switching this master setting to 'OFF' will prevent all users from being able to invite their social connections, however they will still be able to share posts to their social accounts.<br><br>Do you wish to continue?";
        }
      }
      if(calling=='fbsocialsignininvite')
      {
        if(thisEl.is(':checked')==false) {
            msg = 'Are you sure you want to allow users to invite their Facebook friends?';
            globelSettingVal=0;
        }else{
             msg ='Are you sure you want to stop users from inviting their Facebook friends?';
        }
      }
      if(calling=='twsocialsignininvite')
      {
        if(thisEl.is(':checked')==false) {
            msg = 'Are you sure you want to allow users to invite their followers?';
            globelSettingVal=0;
        }else{
             msg ='Are you sure you want to stop users from inviting their followers?';
        }
      }
      if(calling=='gpsocialsignininvite')
      {
        if(thisEl.is(':checked')==false) {
            msg = 'Are you sure you want to allow users to invite their Google+ friends?';
            globelSettingVal=0;
        }else{
             msg ='Are you sure you want to stop users from inviting their Google+ friends?';
        }
      }
      if(calling=='ldsocialsignininvite')
      {
        if(thisEl.is(':checked')==false) {
            msg = 'Are you sure you want to allow users to invite their LinkedIn connections?';
            globelSettingVal=0;
        }else{
             msg ='Are you sure you want to stop users from inviting their LinkedIn connections?';
        }
      }

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
                  url:  BASE_URL+"/admin/globalsetting/managersociallogin",
                  data:{ "globelSettingVal": globelSettingVal,'calling':calling,},
                   success:function(result){            
                        $messageSuccess("Social sign in settings updated successfully"); 
                        var signPr =  thisEl.closest('.socialLinkingInvite');
                        var signPr2 =  thisEl.closest('.makefbsignability');                       

                          if(thisEl.is(':checked')==false) {                              
                               thisEl.attr('checked', true);
                               if(calling=='allsocialsignin'){
                                 $('.makefbsignability input[type="checkbox"]', signPr).attr('disabled', 'disabled');
                                 $('.makefbsignability input', signPr).attr('checked', true);
                                 signPr.addClass('disabledAll');
                                if(localTick==false)
                                  socket.emit('disablesocial', calling,clientID);
                               }
                               else if(calling=='fbsocialsignin' || calling=='twsocialsignin' || calling=='gpsocialsignin' || calling=='ldsocialsignin'){             
                                 signPr2.addClass('disabled');
                                 if(localTick==false)
                                    socket.emit('disablesocial', calling,clientID);
                               }

                               if(calling=='allsocialsignininvite'){
                                 $('.makefbsignability input[type="checkbox"]', signPr).attr('disabled', 'disabled');
                                 $('.makefbsignability input', signPr).attr('checked', true);
                               
                                 signPr.addClass('disabledAll');
                                if(localTick==false)
                                  socket.emit('disablesocial', calling,clientID);
                               }
                               else if(calling=='fbsocialsignininvite' || calling=='twsocialsignininvite' || calling=='gpsocialsignininvite' || calling=='ldsocialsignininvite'){             
                                 signPr2.addClass('disabled');
                                 if(localTick==false)
                                    socket.emit('disablesocial', calling,clientID);
                               }
                               

                            }else
                            {
                                 if(localTick==false)
                                    socket.emit('enablesocial', calling,clientID);
                                 thisEl.attr('checked', false);
                                 if(calling=='allsocialsignin')
                                 {
                                  $('.makefbsignability input[type="checkbox"]', signPr).removeAttr('disabled');
                                  signPr.removeClass('disabledAll');                                  
                                 }
                                 else
                                 {
                                  signPr2.removeClass('disabled');
                                 }

                                 if(calling=='allsocialsignininvite')
                                 {
                                  $('.makefbsignability input[type="checkbox"]', signPr).removeAttr('disabled');
                                   signPr.removeClass('disabledAll');                                  
                                 }
                                 else
                                 {
                                   signPr2.removeClass('disabled');
                                 }
                          }
                            
                            if(result==2)
                            {
                              
                              $('#socialTargetall').attr('checked', true);
                              $('.makefbsignability input[type="checkbox"]', signPr).attr('disabled', 'disabled');
                                 signPr.addClass('disabledAll');
                            }

                            if(result==4)
                            {
                              
                              $('#socialTargetallinvite').attr('checked', true);
                              $('.makefbsignability2 input[type="checkbox"]', signPr).attr('disabled', 'disabled');
                                 signPr.addClass('disabledAll');
                            }
                       
                       } 
                  });
            }
          }
        });
});

})


