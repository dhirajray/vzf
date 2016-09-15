var typedbee;
$(function() {
    
    $('body').on('click', '#spacialdb_section', function() 
    {
        busy = false;
        page = 1;
        typedbee = 6;
       var datafrom= $(this).attr('data-from');
        var pr = $(this).closest('.tabLinks');
         /*if(pr.length==1){
           $('#leftListing .tabLinks').remove();
         }*/

        if(datafrom=='main'){
        $('#leftListing').html('<ul id="dbee-feeds" class="postListing"><li> </li></ul>');
        }
        $('#dbee-feeds').html('<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>');
        dbeeData = {'typedbee':typedbee,'page':page,'isscroll':false};
        $dbeelist(dbeeData);
    });

    $('body').on('click', '.joinRequest', function() 
    {
        This = $(this);
        dbeeid = This.attr('data-id');
        list = This.attr('data-list');
        type = This.attr('data-type');
        This.append(' <i class="fa fa-spinner fa-spin"> </i>').css('cursor', 'default').removeClass("joinRequest");
         $.ajax({
            type: "POST",
            dataType: 'json',
            data: {
                'dbeeID': dbeeid
            },
            url: BASE_URL + '/dbeedetail/specialdbee',
            success: function(response) 
            {
                if(response.status=='success' && list=='true')
                {
                    if(type==0)
                    {
                        This.html('<i class="fa fa-check"></i>Joined');
                        This.removeClass('joinRequest').addClass('videoJoined');
                    }
                    else if(type==1){
                        This.html('<i class="fa fa-check"></i>Request sent');
                        This.removeClass('joinRequest').addClass('videoJoined');
                    }
                    if(response.result_dbee_attendies==1)
                        $('.attendeesCount').html(' '+response.result_dbee_attendies+' attendee ');
                    else if(response.result_dbee_attendies!='')
                        $('.attendeesCount').html(' '+response.result_dbee_attendies+' attendees ');
                }
                else if(response.status=='success')
                {
                    $('#warningMsg').hide();
                    $('.attendeesWrapper').html(response.contents);
                    $dbTip();
                    callsocket();
                    window.location.reload(true);
                }
            }
        });
    });
    
    $(window).scroll(function()
    {
        if ($(window).scrollTop() == $(document).height() - $(window).height()) 
        {
            if(busy == true && typedbee==6)
            {
                page = $('#page').val();
                isscroll = true;
                dbeetype = $('#dbeetype').val();
                 if ($('#more-feeds-loader')) 
                 {
                    $('#more-feeds-loader').html('');
                    $dbLoader('#more-feeds-loader', 'progress');
                    $('#more-feeds-loader').css({
                        marginBottom: 50
                    });
                }
                dbeeData = {'typedbee':typedbee,'page':page,'isscroll':true};
                $dbeelist(dbeeData);
            }
        }
    });
   
});

$dbeelist = function(dbeeData)
{
    busy = false;
    $.ajax({
        type: "POST",
        dataType: 'json',
        data: {
            'type': dbeeData.typedbee,'page':dbeeData.page
        },
        url: BASE_URL + '/post/getdata',
        success: function(response) 
        {
             var tabBro = $('.tabLinks.videoBroTab').length;
             console.log(tabBro);
            $('#page').val(parseInt(dbeeData.page)+1);
            $('#dbeetype').val(dbeeData.typedbee);
            $('#liveBroadcast').removeClass('active');
            var liveBroadcastvar ='';
            if(clientID==100)
            {
             var liveBroadcastvar='<li><a href="javascript:void(0);" id="liveBroadcast">Live Broadcasts</a></li>';
            }
            if(response.status=='success' && response.html!='' && dbeeData.isscroll==false){
                $('.userVisibilityHide').remove();
             $('#leftListing').removeClass('groupViewListing');
             if(tabBro==0){
            var topcontent='<div class="tabLinks videoBroTab" style="margin-top:0px; border-bottom:0">\
                             <ul><li>\
           <a title="Video Events" href="javascript:void(0);" data-from="child" id="spacialdb_section" class="active">\
           Scheduled Broadcasts</a>\
          </li>' + liveBroadcastvar + '</ul></div>';
           $('#leftListing:not(.myDashboard) .tabLinks').remove();
          }else{
            $('.videoBroTab #spacialdb_section').addClass('active');
          }
         
               
                $("#dbee-feeds").html(response.html).before(topcontent);
                 busy = true;
            }
            else if(response.status=='success' && response.html!='' && dbeeData.isscroll==true){
                $('.userVisibilityHide').remove();
                $("#dbee-feeds").append(response.html);
                 busy = true;
            }
            else if(response.status=='success' && response.html=='' && dbeeData.isscroll==false){
                $('.userVisibilityHide').remove();
                $('#leftListing').removeClass('groupViewListing');
                  if(tabBro==0){
                 var topcontent='<div class="tabLinks videoBroTab" style="margin-top:0px; border-bottom:0">\
                             <ul><li>\
           <a title="Video Events" href="javascript:void(0);" data-from="child" id="spacialdb_section" class="active">\
           Scheduled Broadcasts</a>\
          </li>' + liveBroadcastvar + '</ul></div>';
            $('#leftListing:not(.myDashboard) .tabLinks').remove();
      }else{
            $('.videoBroTab #spacialdb_section').addClass('active');
          }
        
                $("#dbee-feeds").html('<div class="userVisibilityHide" style="background-color:#FFFFFF;"><span class="fa-stack fa-2x"><i class="fa fa-television fa-lg"></i></span><br> No Scheduled Video Broadcasts found.</div>').before(topcontent);
                 busy = true;
            }

        }
    });
}

