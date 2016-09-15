var catFilterval = '0';
$(function() {
    $('.userMenuRight').click(function(event) {
        $('#Selectedposts').remove();
        $('.biogrophydisplay').remove();
        $('html').removeClass('activeDeshAndBio');
    });

    $('body').on('click', '.catfindval', function(event) {
        catFilterval = $(this).attr('value');
        //alert(catFilterval);
        seeglobaldbeelist("nouserid", 3, "", "myhome", "catetorylist", "dbcat")
    });

    $('.Catdropdwnall').click(function(event) {
        //$('.catlistval').remove();
        $('#filtercat').html('');
    });

});

function seeglobaldbeelist(id, from, activetab, controller, action, calling ,catiddata,only,EThis) {
    typedbee = '';
    //alert(id+' '+from+' '+activetab+' '+controller+' '+action+' '+calling+' '+catiddata+' '+only)
    $('.scoringdisplay').hide();
    //$('.customizeDeshboard').hide();

    var OtherUser = 0;
    var cat = '';
    from = typeof(from) != 'undefined' ? from : '0';

    catFilterval = typeof(catFilterval) != 'undefined' ? catFilterval : '0';

    if (calling == 'fav')
    {
        $('.customizeDeshboard').remove();
         $('#leftListing .tabLinks').remove();
        if($('#dbeecontrollers').val()!='dashboarduser')
        {             
            var href = $('#ProfileLink').attr('href');            
            window.location.href = href+'#my-influence';
            return false;
         }
    }
    if (calling == 'mypoll')
    {
        $('.customizeDeshboard').remove();
         $('#leftListing .tabLinks').remove();
        if($('#dbeecontrollers').val()!='dashboarduser')
        {             
            var href = $('#ProfileLink').attr('href');            
            window.location.href = href+'#my-poll';
            return false;
         }
    }

    if (from == '0' || from == '2') {
        var data = "user=" + id;
        // $('#dbee-feeds').html('<div style="margin:20px 0 0 20px;">Loading...</div>'); 
        $('#dbee-feeds').html('');
        $dbLoader('#dbee-feeds');
    } else if (from == '1') {
        var data = "user=" + id;
        $('#dashboarduserDetails, #postMenu, #leftListing .profileStatsWrp, #leftListing .group-highlighted').remove();
        $('#my-dbees').html('<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>');
        $('#leftListing').show();
    } 
    else if (from == '3') // for category case
    {
        if(calling == 'dbcat'){
                $('.formcat').hide();
                chklen = $('.dbfeedfilterbycat  input:checked').length;
                if (chklen == 0 && catFilterval == 0) {
                    //$messageError('Please choose your creteria first to find the results...');
                    $dbConfirm({
                        content: 'Please select your catagories',
                        yes: false,
                        error: true
                    });
                    return false;
                }               
                $('.dbfeedfilterbycat  input:checked').each(function() {
                    cat += $(this).val() + ', ';
                });
                if (catFilterval != 0) {
                    $('#filtercat').val(catFilterval);
                    var data = "cat=" + catFilterval;
                } else {
                    $('#filtercat').val(cat);
                    var data = "cat=" + cat;
                }
            }else if(calling == 'dbcatDropdwn'){
                $('#filtercat').val(id);
                var data = "cat=" + id+ ', ';
            }

    } else if (from == '4') {
        var data = "user=" + id;
        OtherUser = 1;
        if(calling=='mydb' || calling=='comment')
        {
            $('#leftListing:not(.myDashboard) .tabLinks').remove(); 
            $('#my-dbees').html('').addClass('myWhiteBg'); 
            $dbLoader('#my-dbees'); 
        }
        else if(calling=='livemydb')
        {
             $('#leftListing:not(.myDashboard) .tabLinks').remove();
            $('#dbee-feeds').html('<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>'); 
            //$dbLoader('#dbee-feeds'); 
        }
    }else if (from == '6') {
            
            $('.dbfeedfilterbycat  input:checked').each(function() {
              cat += $(this).val() + ', ';
            });
            if(cat==''){
               cat =  catiddata;
               $('.formcat').show();
            }else{
                $('.formcat').hide();
            }
            var dataval = "cat=" + cat;          
            $('#filtercat').val(catiddata);
            //var data = "cat=" + catiddata;
            data ='precal='+catiddata+'&'+dataval;

    } 
    /*else if (from == '5') {
         $('.formcat').show();
         $('#filtercat').val(catiddata);
         var data = "cat=" + catiddata;
    }*/    

    //$('#my-dbees-home, #my-comments-home,#all-dbees-home, #dbee-feed-favourite, #dbee-feed-following, #my-comments-home, #dbee-feed-category, #dbee-feed-displayby ').removeClass('active');

    $('#customFieldsDown').hide(); // to hide customised dashborad filters   
    $('.loadmore').hide(); // to hide customised dashborad filters   

    if (from != '6'){ 
      if (activetab != '') $('#' + activetab).addClass('active'); 
    }

    var url = BASE_URL + "/" + controller + "/" + action;
    //alert(url);
    //alert(data);

    catFilterval = '0';
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        dataType: 'html',
        beforeSend: function() {
            if (calling == 'dbcat' || calling == 'dbcatDropdwn') {
                $('#filtercat').show();
                if( calling == 'dbcatDropdwn'){
                     $('#dbee-feeds').html('<div style="clear:both"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>');
                }else{
                    $('#leftListing').html('');
                    $('#leftListing').append('<div><div id="feedtype" value="cat"></div><ul id="dbee-feeds" class="postListing"><li></li></ul></div>');
                     $('#dbee-feeds').html('<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>');
                }
                //$('#leftListing').prepend('<div id="filtercat" value="" style="float: left;margin-left: 21px;padding-top: 0;width: 49.5%;"></div>');
                
                //$dbLoader('#leftListing li');
               
            }
        },
        success: function(data) {
            //alert(data);exit;
            window.busy = false;
            var resultArr = data.split('~#~');
             $('.openMyRightSideBar [data-id="myRighSideBtn"]').trigger('click');
            //alert(resultArr[4]);exit;
            $('#my-dbees').removeClass('myWhiteBg');
            $('#dbee-feeds, #my-dbees').html(resultArr[0]);

            if (calling == 'mydb') {
                $('#leftListing:not(.myDashboard) .tabLinks').remove();
                $('#feedtype').val('mydbs');
               // $('#my-dbees').html(resultArr[0]);
                $('#startnewmydb').val(PAGE_NUM);
            } 
            else if(calling=='livemydb'){ 
                $('#leftListing:not(.myDashboard) .tabLinks').remove();      
                $('#feedtype').val('mydbs');
                //$('#dbee-feeds').html(resultArr[0]);
                $('#startnewmydb').val( PAGE_NUM);
            }
            else if(calling=='mypoll'){ 
                       
                $('#feedtype').val('mypolls');
                //$('#dbee-feeds').html(resultArr[0]);
                $('#startnewmydb').val( PAGE_NUM);
            }
            else if (calling == 'comment') {                
                
                 if(OtherUser==1){
                    $('#middleWrpBox').remove();
                  }
                  else
                  {
                    if ($(document).scrollTop() != 0) {
                        $('html, body').animate({
                            scrollTop: 0
                        }, 'fast');
                     }

                  }
                  /*if(resultArr[2]==''){
                    var notF = $('#middleWrpBox .noFound').clone();
                    $('#middleWrpBox').html('');
                    notF.appendTo('#middleWrpBox');
                  }*/
                 
                // $('#middleWrpBox .noFound').unwrap();

                $('#feedtype').val('mycomments');
               /* if ($('#dbee-feeds').is(':visible') == true) {
                    $('#dbee-feeds').html(resultArr[0]);
                } else {

                    $('#my-dbees').html(resultArr[0]);
                }*/
                $('#startnewmycomments').val(PAGE_NUM);
            } else if (calling == 'follow') {
                 $('#leftListing .tabLinks').remove();
                $('#notifications-top-hidden').val(0);
                $('#feedtype').val('following');
                $('#newdbcount-wrapper').hide();
                $('#startnewfollowing').val(PAGE_NUM);
               /* if ($('#dbee-feeds').is(':visible') == true) {
                    $('#dbee-feeds').html(resultArr[0]);
                } else {
                    $('#my-dbees').html(resultArr[0]);
                }*/
            } else if (calling == 'fav') {

                if ($(document).scrollTop() != 0) {
                    $('html, body').animate({
                        scrollTop: 0
                    }, 'fast');
                }
                
                $('.customizeDeshboard').remove();
                $('.biogrophydisplay').remove();
                $('#feedtype').val('favourite');
                $('#newdbcount').hide();

             /*   if ($('#dbee-feeds').is(':visible') == true) {
                    $('#dbee-feeds').html(resultArr[0]);
                } else {

                    $('#my-dbees').html(resultArr[0]);
                }*/
                $('#feedtype').val('favourite');
                $('#startnewfav').val(PAGE_NUM);
            } else if (calling == 'dbcat' || calling == 'dbcatDropdwn') {
                 //alert(resultArr[5]);
                $('#feedtype').val('cat');
                $('#filtercat').html(resultArr[5]);
                if(typeof catiddata!='undefined'){
                     var removecat = $('#filtercat a').removeClass('btn-yellow').attr('catid');
                     $('#filtercat a[catid='+removecat+']').closest('a').find('.removecate').html('<i class="cancelbtn fa fa-times-circle fa-1x" catid="'+removecat+'" style="float: right;padding-left:5px;"></i>');
                     $('#filtercat a[catid='+catiddata+']').closest('a').find('.removecate').html('');
                     $('#filtercat a[catid='+catiddata+']').addClass('btn-yellow');
                 }
                $("#callcatfirsttime").val(1);
                $('#startnewcat').val(PAGE_NUM);
                if(calling == 'dbcatDropdwn'){
                   /*$(".catlistval").removeAttr("href").css("cursor","pointer");
                   $(".removecate").removeAttr("href").css("cursor","pointer");*/
                   $(".catlistval").removeAttr("href").css("cursor","inherit");
                   $('#filtercat a').removeClass('catlistval'); 
                }                        
            } else if (calling == 'mostcommented') {
                $('#feedtype').val('mostcommented');
                $('#newdbcount').hide();
                $('#startnewmc').val(PAGE_NUM);
            }
            //console.log(resultArr[4]);
            //console.log(resultArr[2]);
            if(action=='catetorylist')
                getMentionUser($.trim(resultArr[2]));
            else
                getMentionUser($.trim(resultArr[4]));
            $dbTip();

        }
    });
}

function seedbeelistresult() {
        if (SeeMyDbeesHttp.readyState == 4) {
            if (SeeMyDbeesHttp.status == 200 || SeeMyDbeesHttp.status == 0) {
                var result = SeeMyDbeesHttp.responseText;
                var resultArr = result.split('~#~');
                document.getElementById('my-dbees').innerHTML = resultArr[0];
                document.getElementById('totaldbees').value = resultArr[5];
                document.getElementById('feedtype').value = 'mydbs';
                document.getElementById('startnewmydb').value = PAGE_NUM;
            } 
        }
    } // holding for profile


function seemoreglobaldbeelist(start, end, from, controller, action, calling, extraparam) {
    var totDbsLi = $('.postListing > li').length;
    if ( totDbsLi < 5 ){ return false; }
    //alert(calling)
    from = typeof(from) != 'undefined' ? from : adminID;
    if ($('#more-feeds-loader')) {
        $('#more-feeds-loader').html('');
        $dbLoader('#more-feeds-loader', 'progress');
        $('#more-feeds-loader').css({
            marginBottom: 50
        });
    }


    var url = BASE_URL + "/" + controller + "/" + action;
    if (calling == 'dbcat')
        var data = "seemore=1&cat=" + extraparam + "&start=" + start + '&end=' + end + '&dbeenotavailmsg=1';
    else if (calling == 'mycomments') {
        start = start.replace(/(^[\s]+|[\s]+$)/g, '');
        id = typeof(extraparam) != 'undefined' ? extraparam : '0';
        var data = "seemore=1&user=" + id + "&start=" + start + '&end=' + end + '&dbeenotavailmsg=1';
    } else if (calling == 'filtertype'){
        var data = "seemore=1&type=" + extraparam + "&start=" + start + '&end=' + end + '&dbeenotavailmsg=1';
    } else if (calling == 'liveBroadcast')

        var data = "seemore=1&type=" + extraparam + "&start=" + start + '&end=' + end + '&dbeenotavailmsg=1';   
    else if (calling == 'group')
        var data = "seemore=1&type=" + calling + "&start=" + start + '&end=' + end + '&dbeenotavailmsg=1';
    else if (calling == 'filterscore')
        var data = "seemore=1&score=" + extraparam + "&start=" + start + '&end=' + end;
    else
        var data = "check=0&seemore=1&start=" + start + '&end=' + end + '&user=' + from + '&dbeenotavailmsg=1';
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        async : false,
        dataType: 'html',
        success: function(data) {

            window.busy = false;
            
            var resultArr = data.split('~#~');
            if (calling == 'mydb') {
                $('#startnewmydb').val(resultArr[1]);

                if ($('#see-more-feeds' + start)) $('#see-more-feeds' + start).html(resultArr[0]);
                if(resultArr[5]==0)
                {
                    window.busy = true;
                }
            } else if (calling == 'mycomments') {

                $('#reloadend').val(start);
                if ($('#see-more-feeds' + start)) $('#see-more-feeds' + start).html(resultArr[0]);
                $('#startnewmycomments').val(resultArr[3]);
            } else if (calling == 'group') {
                $('#reloadend').val(start);
                if ($('#see-more-feeds' + start)) 
                    $('#see-more-feeds' + start).html(resultArr[0]);
                $('#startnewmycomments').val(resultArr[3]);
            }
            else if (calling == 'follow') {
                $('#reloadend').val(start);
                if ($('#see-more-feeds' + start)) $('#see-more-feeds' + start).html(resultArr[0]);
                $('#startnewfollowing').val(resultArr[3]);

                $('#masterUsers').val(from);
                if(resultArr[2]=='')
                {
                    window.busy = true;
                }

            } else if (calling == 'fav') {
                $('#reloadend').val(start);
                if ($('#see-more-feeds' + start)) $('#see-more-feeds' + start).html(resultArr[0]);
                $('#startnewfav').val(resultArr[3]);
            
            } else if (calling == 'mypoll') {
                $('#reloadend').val(start);
                if ($('#see-more-feeds' + start)) $('#see-more-feeds' + start).html(resultArr[0]);
                $('#startnewfav').val(resultArr[3]);
            } else if (calling == 'dbcat') {
                //alert(resultArr[0])
                $('#reloadend').val(start);
                if ($('#see-more-feeds' + start)) $('#see-more-feeds' + start).html(resultArr[0]);
                $('#startnewcat').val(resultArr[3]);

                if(resultArr[2]=='')
                {
                    window.busy = true;
                }  
            } else if (calling == 'mostcommented') {
                $('#reloadend').val(start);
                if ($('#see-more-feeds' + start)) $('#see-more-feeds' + start).html(resultArr[0]);
                $('#startnewmc').val(resultArr[3]);
            } else if (calling == 'filtertype') {
                $('#reloadend').val(start);
                if ($('#see-more-feeds' + start)) $('#see-more-feeds' + start).html(resultArr[0]);
                $('#startnewtype').val(resultArr[3]);
            } else if (calling == 'myfiltertype') {
                $('#reloadend').val(start);
                if ($('#see-more-feeds' + start)) $('#see-more-feeds' + start).html(resultArr[0]);
                $('#startnewtype').val(resultArr[3]);             
             } else if (calling == 'liveBroadcast') {
                $('#reloadend').val(start);
                if ($('#see-more-feeds' + start)) $('#see-more-feeds' + start).html(resultArr[0]);
                $('#startnewtype').val(resultArr[3]);
            }
            else if (calling == 'filterscore') {
                $('#reloadend').val(start);
                if ($('#see-more-feeds' + start)) $('#see-more-feeds' + start).html(resultArr[0]);
                $('#startnewfilter').val(resultArr[3]);
            } else if (calling == 'movedposts') {
                $('#reloadend').val(start);
                if ($('#see-more-feeds' + start)) $('#see-more-feeds' + start).html(resultArr[0]);
                $('#startnewfilter').val(resultArr[3]);
                $('#masterUsers').val(from);
            } else if (calling == 'groupCatsPost') {
                $('#reloadend').val(start);
                if ($('#see-more-feeds' + start)) $('#see-more-feeds' + start).html(resultArr[0]);
                $('#startnewfilter').val(resultArr[3]);
                $('#masterUsers').val(from);
                if(resultArr[2]=='')
                {
                    window.busy = true;
                }
            }
            
            //console.log(resultArr);
            
            
            if($.trim(resultArr[2])){
                getMentionUser($.trim(resultArr[2]));
            }
            $dbTip();

        }
    });
}

function seeglobaldisplayby(id, from, chkclass, controller, action, calling) {
    chklen = $('.' + chkclass + ' input:checked').length;

    /* if(chklen==0)
     {
         $messageError('Please choose your creteria first to find the results...');
         return false;
     }*/





    var checkedVal = '';
    if (calling == 'filtertype' && action != 'mylivevideofilter') {
       
        $('.' + chkclass + ' input:checked').each(function() {
            checkedVal += 'c.type = ' + $(this).val() + ' OR ';
        });
        var data = "type=" + checkedVal;
       
    }
    else if (calling == 'liveBroadcast') {
       $('.tabLinks #spacialdb_section').removeClass('active');
        var typeval=$('.' + chkclass).val()
        checkedVal += 'c.type = 15 OR ';
        
        var data = "type=" + checkedVal;
      
    }
    else if (action == 'mylivevideofilter') {
        
        var typeval=$('.' + chkclass).val()
        checkedVal += 'c.type = 15 OR ';
        
        var data = "type=" + checkedVal;
    } else {
        $('.' + chkclass + ' input:checked').each(function() {
            checkedVal += 's.Score = ' + $(this).val() + ' OR ';
        });
        var data = "score=" + checkedVal;

    }
    //alert(controller)
    $('#' + calling).val(checkedVal);
    var url = BASE_URL + "/" + controller + "/" + action;


    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        dataType: 'html',
        beforeSend: function() {
            if (calling == 'liveBroadcast'){
                 $('.postListing').html('<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>');
              
            }else
            {              
                 $('#leftListing').html('<ul id="dbee-feeds" class="postListing"><li> </li></ul>');
            }
           
           
            if ($('#dbee-feeds').is(':visible') == true) {
                $('#dbee-feeds').html('<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>');
            } else {
                //$('.group-highlighted').hide();
                $('#my-dbees').html('<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div>/div>');

            }

        },
        success: function(data) {
             var resultArr = data.split('~#~');
             window.busy = false;
             var nofound='';

         
            if(action=='myfiltertype')
            {
                if(resultArr[2]=="")
                {
                 nofound ='<div style="cursor:pointer; color:#333333; text-align:center;"><div class="userVisibilityHide">\
                                    <span class="fa-stack fa-2x">\
                                      <i class="fa fa-film fa-lg"></i></span><br>\
                                    No Video Events found.</div></div>';         
                }
            }
            if(calling=='liveBroadcast')
            { 
                if(resultArr[2]=="")
                {
                 nofound ='<div  style="cursor:pointer; color:#333333; text-align:center;"><div class="userVisibilityHide" style="background-color:#FFFFFF;">\
                                    <span class="fa-stack fa-2x">\
                                      <i class="fa fa-television fa-lg"></i></span><br>\
                                    No Live Video Broadcasts found.</div></div>';         
                }
            }

             if (calling == 'filtertype' && action != 'mysurveyfiltertype' && action!='myfiltertype' && action != 'mylivevideofilter') {
                if(resultArr[2]=="" && checkedVal=='c.type = 7 OR ')
                {
                 nofound ='<div  style="cursor:pointer; color:#333333; text-align:center;"><div class="userVisibilityHide">\
                                    <span class="fa-stack fa-2x">\
                                      <i class="fa fa-check-square-o fa-lg"></i></span><br>\
                                    Sorry there are no surveys.</div></div>';         
                }

                if(resultArr[2]=="" && checkedVal=='c.type = 6 OR ')
                {
                 nofound ='<div  style="cursor:pointer; color:#333333; text-align:center;"><div class="userVisibilityHide">\
                                    <span class="fa-stack fa-2x">\
                                      <i class="fa fa-film fa-lg"></i></span><br>\
                                    Sorry there are no video events found.</div></div>';         
                }
             }

            
            if (calling == 'liveBroadcast') {
                $('#dbee-feeds').html(resultArr[0] + nofound)
               
            }else
            {
                $('#leftListing').html('<ul id="dbee-feeds" class="postListing">' + resultArr[0] + nofound +'</ul>')
            }


            if (calling == 'filtertype') {  
                
                $('#feedtype').val('type');
                $('#startnewtype').val(PAGE_NUM);
            } else if (calling == 'filterscore') {
                $('#feedtype').val('filter');
                $('#startnewfilter').val(PAGE_NUM);            
            }else if (calling == 'liveBroadcast') {
                $('#feedtype').val('livevideo');
                 $('#xxfeedtype').val('livevideo');
                $('#startnewtype').val(PAGE_NUM);
            }
            //console.log(resultArr[2]);
            if($.trim(resultArr[2])){
                getMentionUser($.trim(resultArr[2]));
            }

        }
    });
}