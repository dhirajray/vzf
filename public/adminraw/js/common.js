var cancelFileDropzone ='';
var CloseLoader = true;
$(window).load(function (){
   setTimeout(function (){
     $('#commonsearch').val('');
 }, 500);
});

/*$(document).ready(function () {
    document.body.oncopy = function () {
        var body_element = document.getElementsByTagName('body')[0];
        var selection;
        selection = window.getSelection();
        var pagelink = "<br />Read more at: <a href='" + document.location.href + "'>" + document.location.href + "</a><br />";
        var copytext = selection + pagelink;
        var newdiv = document.createElement('div');
        body_element.appendChild(newdiv);
        newdiv.innerHTML = copytext;
        selection.selectAllChildren(newdiv);
        window.setTimeout(function () {
            body_element.removeChild(newdiv);
        }, 0);
    };
});*/
$(document).ajaxStart(function() {
    if(SESS_USER_ID=='')
    {
        window.location.href= BASE_URL+"/admin/logout";
    }
    $.ajax({
        type: "POST",
        dataType: 'json',
        url: BASE_URL + '/dbeedetail/socialblock/',
        beforeSend: function() {},
        cache: false,
        success: function(response){
             //window.location.href= BASE_URL+"/admin/login/";
            if (response.ForcedLogout == 'logout') {
                $messageError ('Oops, something went wrong with your current session. You will be logged out if we are unable to reconnect it.');
                setTimeout(function(){
                    window.location.href= BASE_URL+"/admin/index/";
                }, 5000);
               // window.location.href = BASE_URL + "/index/index/sessionkey/" + response.message;
            }
        }

    });
});


/*tutorials videos*/
var adminStatus = true;
Dropzone.options.myAwesomeDropzone = false;
Dropzone.autoDiscover = false;
var abortAjax ;
var dbTutorials = [{
    "userTut": [{
        "title": "User Account Creation",
        "video": "7Bc7rkL7_vk"
    }, {
        "title": "Main Dashboard",
        "video": "csNqYCNBp7E"
    }, {
        "title": "Creating A Post",
        "video": "5ZYiuDMfk6Q"
    }, {
        "title": "Comments & Features",
        "video": "zUS5KFvRJlY"
    }, {
        "title": "Groups",
        "video": "vwE5MjOS0TM"
    }, {
        "title": "Profile Pages",
        "video": "jVXMRHreKB4"
    }, {
        "title": "Messages",
        "video": "D6wvl13XULU"
    }, {
        "title": "Ask The Expert Q & A",
        "video": "VtQj9qSWdZM"
    }, {
        "title": "Leagues",
        "video": "oJWqmshS5q8"
    }],
    "adminTut": [{
        "title": "Administration - Quick Summary",
        "video": "u40EdiekfMs"
    }, {
        "title": "Dashboard Overview",
        "video": "hgmjo8_aTGo"
    }, {
        "title": "Video Posts and Surveys",
        "video": "6p6NfQqEvQk"
    }, {
        "title": "User Management and VIP Accounts",
        "video": "L_O3qiuLef8"
    }, {
        "title": "Reports & Data Filtering",
        "video": "h17t37hHffc"
    }, {
        "title": "Messages & Email Marketing",
        "video": "oy7vd_n49wo"
    }, {
        "title": "Document Library",
        "video": "eEBpXPS89BI"
    }, {
        "title": "Theme Settings & Email Templates",
        "video": "048K-WeiDuE"
    }, {
        "title": "Platform Settings",
        "video": "Q3dCP2W45gw"
    }]
}];

function findVideoTutorial(dataType, dataTittle) {
    var videoCode = '';
    if (dataType == 'frontend') {
        var data = dbTutorials[0].userTut;
    } else {
        var data = dbTutorials[0].adminTut;
    }
    $.each(data, function(i, value) {
        if (value.title == dataTittle) {
            videoCode = value.video;
        }
    });
    return videoCode;

}


/*tutorials videos*/

function ajaxnotification() {
    $.ajax({
        url: BASE_URL + '/admin/index/notification',
        cache: true,
        dataType: 'json',
        success: function(data) //on recieve of reply
        { 
            $(data.result).each(function( index,element ) 
            {
                if(element.act_type==16 || element.act_type==17 || element.act_type==18 || element.act_type == 109){
                    type= '109';
                }else{
                    type = element.act_type;
                }
                switch(type) 
                {
                    case '30':
                        var message = 'Approve posts <span class="newMark">new</span>';
                        break;
                    case '2':
                        var message = 'Comments <span class="newMark">new</span>';
                        break;
                    case '44':
                        var message = 'Polls <span class="newMark">new</span>';
                        break;
                    case '19':
                        var message = 'Surveys <span class="newMark">new</span>';
                        break;
                    case '20':
                        var message = 'Groups <span class="newMark">new</span>';
                        break;
                    case '109':
                        var message = 'Video broadcasts <span class="newMark">new</span>';
                        break;
                    case '15':
                        var message = 'Feedback <span class="newMark">new</span>';
                        break;
                  
                }
                $('.updateNotify[type="'+type+'"]').html(message).addClass('newActiveMark');
                /*if($('.updateNotify[type="'+type+'"]').closest('li').hasClass('active')){
                    callNotify(type);
                }*/
            });

            if (data.count != 0) 
            {
                $('html').addClass('notificationActiveOn');
                $('.dashBlock.notification .newMark').fadeIn();

            }
            else 
            {
                $('html').removeClass('notificationActiveOn');
            }
            //
        }
    });
}

function callNotify(type)
{
    $.ajax({
        type : "POST",
        dataType : 'json',
        url : BASE_URL + '/admin/dashboard/castfilter',
        data : {'type':type},
        success : function(response) 
        {
              if(type==16 || type==17 || type==18 || type == 109){
                type= 109;
              }
              $('.dbtabContent[data-type="'+type+'"]').removeClass('loader').html(response.postlist);
              $select('select');
              ajaxnotification();
        }
      });
}

function checkDataReport(tab) {
    var addToGroupBtn = $('.grpTopTabel .dropDownTarget');
    if (tab == '#user' || tab == '#hatedislike') {

        var checkedSize = $('input:checked', tab).size();
        if (checkedSize == 0) addToGroupBtn.addClass('disabled');
        else addToGroupBtn.removeClass('disabled');

        if ($('#usernotfound', tab).val() == 0) $('.grpTopTabel').hide()
        else $('.grpTopTabel').show();

    } else {
        var checkedSize = $('.dbtabContainer .dbtabContent:first input:checked').size();
        if (checkedSize == 0) addToGroupBtn.addClass('disabled');
        else addToGroupBtn.removeClass('disabled');
        if ($('#postnotfound', tab).val() == 0) $('.grpTopTabel').hide()
        else $('.grpTopTabel').show();
    }
}

function InflunceTypeFilter(val) {

    var type = typeof(val) != 'undefined' ? val : '0';
    var checkboxAll = $('.reportingTable #tlallresult');
    var checkboxAll2 = $('.reportingTable #userAllresult');
    var btnUser = $('button[data-type="user"]');
    var btnPost = $('button[data-type="post"]');

    if (type != '') {
        $.ajax({
            url: BASE_URL + '/admin/influence/influncetypefilter',
            type: "POST",
            dataType: 'json',
            data: {
                'type': type
            },
            beforeSend: function() {

            },
            success: function(data) //on recieve of reply
                {

                    if (data.content != "") {
                        $('#divforcomment').html(data.content);
                        $('#influencecomment').select2({
                            width: 350
                        }).change(function() {
                            var thisFormValue = $(this).closest('form').serializeArray();

                            $.ajax({
                                url: BASE_URL + '/admin/influence/inflist',
                                type: "POST",
                                dataType: 'json',
                                data: thisFormValue,
                                success: function(data) {
                                    if (data.content != "") {
                                        $('#messageWrapper #user tbody').html(data.content);
                                        checkboxAll2.closest('label').show();
                                        btnUser.show();
                                    }
                                    if (data.total == 0) {
                                        btnUser.hide();
                                        checkboxAll2.closest('label').hide();
                                    }
                                    if (data.content2 != "") {
                                        $('#messageWrapper #post tbody').html(data.content2);
                                        checkboxAll.closest('label').show();
                                        btnPost.show();

                                    }
                                    if (data.total2 == 0) {
                                        btnPost.hide();
                                        checkboxAll.closest('label').hide();
                                    }

                                    var tab = location.hash.split('&tab')[0];
                                    checkDataReport(tab);

                                }
                            });



                        });


                        $('#influencecomment').trigger('change');
                        $(checkboxAll, checkboxAll2).closest('label').show();
                    } else {
                        $('#divforcomment').html(data.content);
                    }

                }
        });
    } else {
        location.href = '';
    }

}

function ScoreTypeFilter2(val) {  
     window.location.href = BASE_URL + '/admin/leaguescore/index/type/'+val;
}


function ScoreTypeFilter(val) {
    var type = typeof(val) != 'undefined' ? val : '0';
    var checkboxAll = $('.reportingTable #tlallresult');
    var checkboxAll2 = $('.reportingTable #negativeAllresult');
    var btnLove = $('button[data-type="love"]');
    var btnHate = $('button[data-type="hate"]');
    if (type != '') {
        $.ajax({
            url: BASE_URL + '/admin/leaguescore/scoretypefilter',
            type: "POST",
            dataType: 'json',
            data: {
                'type': type
            },
            beforeSend: function() {

            },
            success: function(data) //on recieve of reply
                {

                    if (data.content != "") {

                        $('#divforcomment').html(data.content);
                        $('#influencecomment').select2({
                            width: 350
                        }).change(function() {

                            /* var thisFormValue = $(this).closest('form').serializeArray();
                             console.log(thisFormValue);*/
                            document.scoreposttype.submit();
                           
                            /*$.ajax({
                                url: BASE_URL + '/admin/leaguescore/scoregiven',
                                type: "POST",
                                dataType: 'json',
                                data: thisFormValue,
                                success: function(data) {

                                    if (data.content != "") {
                                        $('#messageWrapper tbody').html(data.content);
                                        checkboxAll.closest('label').show();
                                        btnLove.show();
                                    }

                                    if (data.total == 0) {
                                        btnLove.hide();
                                        checkboxAll.closest('label').hide();
                                    }

                                    if (data.content2 != "") {
                                        $('#messageWrapper tbody').html(data.content2);
                                        checkboxAll2.closest('label').show();
                                        btnHate.show();

                                    }
                                    if (data.total2 == 0) {
                                        btnHate.hide();
                                        checkboxAll2.closest('label').hide();
                                    }

                                    var tab = location.hash.split('&tab')[0];
                                    checkDataReport(tab);
                                }
                            });*/
                        });


                        //$('#influencecomment').trigger('change');
                        $(checkboxAll, checkboxAll2).closest('label').show();

                    } else {
                        $('#divforcomment').html(data.content);
                    }

                }
        });
    } else {
        location.href = '';
    }
}

$(function() {

    $('body').on('click', '.resetValueUpload', function(event) {
        $(this).closest('.appendType').find('input').val('');
        $(this).unwrap();
        $(this).closest('.appendType').find('.uploaderror').remove();
        $(this).remove();
    });

    $('body').on('click', '.acceptVideoJoin', function(event) {
        act_id = $(this).attr('data-act_id');
        db = $(this).attr('data-id');
        user = $(this).attr('user-id');
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {
                'act_id':act_id,'ateeenuserstring':user,'dbeeID':db
            },
            url: BASE_URL + '/admin/social/updatejoinrequser',
            success: function(response) 
            {
                 socket.emit('testvideo', db,clientID);
                 $messageSuccess(response.message);
                 socket.emit('chkactivitynotification', true,clientID);
                $('#remove_'+act_id).remove();
            }
        });
    });

     $('body').on('click', '.rejectVideoJoin', function(event) {
        act_id = $(this).attr('data-act_id');
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {
                'act_id':act_id
            },
            url: BASE_URL + '/admin/social/rejectvideoevent',
            success: function(response) {
                $messageSuccess(response.message);
                socket.emit('testvideo', 0,clientID);
                socket.emit('chkactivitynotification', true,clientID);
                $('#remove_'+act_id).remove();
            }
        });
    });

     $('body').on('click', '.promotedPost', function(event) {
        dbeeid = $(this).attr('data-id');
        status = $(this).attr('data-status');
        var This = $(this);
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {
                'dbeeid':dbeeid,'status':status
            },
            url: BASE_URL + '/admin/dashboard/promotedpost',
            success: function(response) {
                $messageSuccess('successfully updated');
                //alert(status);
                if(status==0)
                {
                    This.text('Unpromote');
                    This.attr('data-status',1);
                    This.addClass('btn-danger ').removeClass('btn-green');
                }else{
                    This.text('Promote');
                    This.attr('data-status',0);
                    This.addClass('btn-green').removeClass('btn-danger');
                }

            }
        });
    });

    $("body").on('click', '[video-help]', function(event) {
        event.preventDefault();
        $('#detailsDialog').remove();
        var title = $(this).attr('video-help');
        var htmlLightbox = '<div id="detailsDialog"><iframe width="100%" height="400" src="//www.youtube.com/embed/' + findVideoTutorial('', title) + '" frameborder="0" allowfullscreen></iframe></div>';

        $('body').append(htmlLightbox);

        $("#detailsDialog").dialog({
            dialogClass: 'detailsDialogBox',
            width: 800,
            title: title
        });

    });

    $('body').on('click', '.acceptRequestToJoin', function(event) {
        event.preventDefault();
        var userid = $(this).attr('data-id');
        var dbeeid = $(this).attr('dbee-id');
        var thisEl = $(this);

        var msg = '';
        var statusEffect;
        var postURL = '';
        var postData = '';
        var activeCategory = false;
        var msgOnSuccess = '';

        postURL = BASE_URL + "/admin/dashboard/updatejoinrequser";
        postData = {
            'ateeenuserstring': userid,
            'dbeeID': dbeeid
        };
        msgOnSuccess = 'successfully joined';

        msg = 'Do you want to continue?';


        $('#dialogConfirmSetting').remove();
        $('body').append('<div id="dialogConfirmSetting">' + msg + '</div>');

        $("#dialogConfirmSetting").dialog({
            resizable: false,
            title: 'Please confirm',
            width: 400,
            modal: true,
            buttons: {
                "Yes": function() {
                    $(this).dialog("close");
                    $.ajax({
                        url: postURL,
                        data: postData,
                        type: "POST",
                        success: function(data) {
                            $messageSuccess(msgOnSuccess);
                            socket.emit('videoeventstart', dbeeid,clientID);
                            $('#row' + dbeeid + userid).remove();
                        }
                    });
                }
            }
        });

    });

});



function callglobalajax(containerId, callingController, callingAction, calling, param, datefrom, dateto, ranglimit, offset,thisEl) {
    var range = typeof(ranglimit) != 'undefined' ? ranglimit : '';
    var offset = typeof(offset) != 'undefined' ? offset : '';
    var thisElement = typeof(thisEl) != 'undefined' ? $(thisEl) : '';

    if(datefrom=='month'){
        var datefrom = typeof(datefrom) != 'undefined' ? datefrom : '';
        var dateto = typeof(dateto) != 'undefined' ? dateto : '';
    }
    else
    {
        var datefrom = typeof(datefrom) != 'undefined' ? datefrom : '';
        var dateto = typeof(dateto) != 'undefined' ? dateto : '';
    }
    
    divexixt = $('#' + containerId).length;
    if(divexixt==0){
        containerId = thisElement.closest('li').find('.loaderCntWrp').attr('id');
    }

    data = 'uid=' + param + '&calling=' + calling + '&datefrom=' + datefrom + '&dateto=' + dateto + '&ranglimit=' + range + '&offset=' + offset;

    url = BASE_URL + "/admin/" + callingController + "/" + callingAction;

    $.ajax({
        url: url,
        data: data,
        method: 'POST',
        //Type: 'json',             
        beforeSend: function() {
            if (calling != 'postvisiting') {
                $("#" + containerId).html('');
            } else {
                $dbLoader({
                    element: "#" + containerId,
                    overlay: true
                });
            }


            if (containerId == 'signedcontainer') {
                $("#" + containerId).closest('.dashBlock').addClass('loader');
            } else {
                if (calling != 'postvisiting') {
                    $("#" + containerId).addClass('loader');
                }
            }


            if (calling == 'vipmembers') {
                var htmlPopup = '<div id="sentiDialog" title="VIP Group members" class="loader">\
                    <div id="beforecall"></div>\
                    <h3 class="sentiDialoglabel"></h3>\
                    <ul id="digForm" ></ul></div>';
                $('#sentiDialog').remove();
                $('body').append(htmlPopup);
                $("#sentiDialog").dialog({
                    width: 800,
                    height: 530,
                    open: function() {
                        $fluidDialog();
                    }
                });
            }
        },
        success: function(result) {
            if (containerId == 'signedcontainer' || containerId == 'piecontainer') {
                $("#" + containerId).closest('.dashBlock').removeClass('loader');
            } else {
                $("#" + containerId).removeClass('loader');

            }
            var response = result.split("~");
            $("#" + containerId).removeClass('removeatresult');
            if (calling == 'topdebating') {

                if (response[0] == 'no') {
                    $('#' + containerId).html('<div class="dashBlockEmpty">no record found</div>');
                } else chartforbrowsersproviders(JSON.parse(response[0]), JSON.parse(response[1]), containerId, "Most active users", 'users on post', 'Total activity', '', '', 'Total activity', 'user', JSON.parse(response[2]), JSON.parse(response[3]));

            } else if (calling == 'postvisiting') {
                $dbLoader({
                    close: true
                });

                if (response[0] == 'no') {
                    $('#' + containerId).html('<div class="dashBlockEmpty">no record found</div>');
                } else chartforbrowsersproviders(JSON.parse(response[0]), JSON.parse(response[1]), containerId, "Most popular posts", 'users on post', 'No. of users', 'postvisiters', 'visitingusers', 'users have visited this post', '', JSON.parse(response[2]), JSON.parse(response[3]), JSON.parse(response[4]));
                if (response[5] != '') {
                    if(thisElement!=''){
                        thisElement.closest('.visitingcontainerPaging').remove();
                    } //else thisElement.closest('.visitingcontainerPaging').remove();
                    
                    //thisElement.closest('div').attr('.visitingcontainerPaging').remove();
                    $('#' + containerId).after(JSON.parse(response[5]));

                }

                // chartforbrowsersproviders(<?php echo $this->browserProvidersdata ?>,<?php echo $this->browserproviderscategory?>,'browserprovidercontainer',"browser sources",'platform on browsers','user count','browers','browserusers','users have used this browser');

            } else if (calling == 'usersignupfromplateform') {

                // alert(containerId);

                if (response[0] == 'no') {
                    $('#' + containerId).html('<div class="dashBlockEmpty">no record found</div>');
                } else chartofdbees(JSON.parse(response[0]), containerId, 'User sign up');
            } else if (calling == 'mosetscore') {
                if (response[0] == 'no') {
                    $('#' + containerId).html('<div class="dashBlockEmpty">no record found</div>');
                } else chartforbrowsersproviders(JSON.parse(response[0]), JSON.parse(response[1]), containerId, 'Most loved', 'score on comments', 'Score count', '', '', 'Total love count', 'user', JSON.parse(response[2]), JSON.parse(response[3]));

            } else if (calling == 'populardebate') {
                headtitle = (containerId != 'popularcontainer') ? 'popular posts' : '';

                if (response[0] == 'no') {
                    $('#' + containerId).html('<div class="dashBlockEmpty">no record found</div>');
                } else chartforbrowsersproviders(JSON.parse(response[0]), JSON.parse(response[1]), containerId, 'Most popular posts', 'uses on post', 'No. of comments', '', '', ' comments', 'dbee', JSON.parse(response[2]), JSON.parse(response[3]), JSON.parse(response[4]), 'Post creator');

            } else if (calling == 'vipmembers') {
                $('#sentiDialog').removeClass('loader')
                $("#digForm").html(result);
                return false;
            } 
            else if (calling == 'SematriaCategories') {
                $("#" + containerId).html(result);

                if (result == 'no') {
                    $('#' + containerId).html('<div class="dashBlockEmpty">no record found</div>');
                }else{

                     $("#" + containerId+' #word-cloud a').each(function (){
                        var fontSize = Math.random()*(40-10)+10;
                        var fontColor = Math.random()*(100000-900000)+900000;
                        var rotate = Math.random()*(45-0)+0;
                        $(this).css({fontSize:fontSize+'px', color:'#'+fontColor});
                       var textWidth =  $(this).width();
                       var textHeight =  $(this).height();
                     });
                     var mainH = $('.SematriaCategoriesArea').height();
                     var cntH =  $("#" + containerId+' #word-cloud').height();
                     if(mainH<cntH){
                          var ratio = mainH/cntH;
                     $("#" + containerId+' #word-cloud').css({zoom:ratio});
                     }
                }
                return false;
            } else {
                $("#" + containerId).html(result);
                
            }
        }
    });

}

function updateactivity() {

    var userid = $('#useridactivity').val();
    var data = 'userid=' + userid;

    $.ajax({
        url: BASE_URL + "/admin/user/reloadactivity",
        data: data,
        dataType: "json",
        type: "POST",
        success: function(response) {
            $('#lastactivity').html(response.content);
        }
    });
}

function updatenotification(thisEl, eventType) {

    $.ajax({
        url: BASE_URL + "/admin/index/ajaxnotification",
        dataType: "json",
        beforeSend: function() {
            //$('#notiactivity').addClass('loader'); 
            if (eventType == 'click') {
                $dbLoader({
                    element: '#notiactivity',
                    overlay: true
                });
            } else {
                $dbLoader({
                    element: '#notiactivity'
                });
            }

        },
        success: function(response) {
            $('#notiactivity').removeClass('loader').html(response.content);
            $('#notiactivity img').imageLoader();
            $('.dashBlock').removeAttr('style');
            if(isMobile==null){
            $('.dashBlock').equalizeHeights();
            }

        }
    });
}

$(function() {

    $('#refreshNotification').click(function(event) {

        updatenotification('', 'click');
        $('.newMark').fadeOut();
    });


    $('.srcMainWrapper input').focusin(function(event) {
        $(this).closest('.srcMainWrapper').addClass('focusIinput');
    });
    $('.srcMainWrapper input').focusout(function(event) {
        $(this).closest('.srcMainWrapper').removeClass('focusIinput');
    });



    // this is for user status
    $('body').on('click', '.deletePost', function(event) {
        event.preventDefault();
        var dbee_id = $(this).attr('dbee_id');
        var thisEl = $(this);

        var msg = '';
        var statusEffect;
        var postURL = '';
        var postData = '';
        var activeCategory = false;
        var msgOnSuccess = '';

        postURL = BASE_URL + "/admin/dashboard/postdelete";
        postData = {
            'dbee_id': dbee_id
        };
        msgOnSuccess = 'Post has been deleted successfully';

        msg = 'Do you wish to continue?';


        $('#dialogConfirmSetting').remove();
        $('body').append('<div id="dialogConfirmSetting">' + msg + '</div>');

        $("#dialogConfirmSetting").dialog({
            resizable: false,
            title: 'Please confirm',
            width: 400,
            modal: true,
            buttons: {
                "Yes": function() {
                    $(this).dialog("close");
                    $.ajax({
                        url: postURL,
                        data: postData,
                        type: "POST",
                        success: function(data) {
                            $messageSuccess(msgOnSuccess);
                        }
                    });
                }
            }
        });

    }); 

   // this is for user status
    $('body').on('click', '.markvipAction', function(event) {
        event.preventDefault();
        var userid = $(this).attr('data-userid');
        var thisEl = $(this);

        var msg = '';
        var statusEffect;
        var postURL = '';
        var postData = '';
        var activeCategory = false;
        var msgOnSuccess = '';

        postURL = BASE_URL + "/admin/user/markedvip";
        postData = {
            'userid': userid
        };
        msgOnSuccess = 'Updated successfully';

        msg = "Are you sure you want to make this user a VIP?<br><br>By selecting 'VIP' status for this user, non-VIP users won't be able to see his/her posts or interact with him/her.  The VIP will be able to comment on all users' posts.";


        $('#dialogConfirmSetting').remove();
        $('body').append('<div id="dialogConfirmSetting">' + msg + '</div>');

        $("#dialogConfirmSetting").dialog({
          
            title: 'Please confirm',
            modal: true,
            width:400,           
            buttons: {
                "Yes": function() {
                    $(this).dialog("close");
                    $.ajax({
                        url: postURL,
                        data: postData,
                        type: "POST",
                        success: function(data) {
                            thisEl.remove();
                            $messageSuccess(msgOnSuccess);
                             if(localTick == false){ // for db approve case
                                socket.emit('makevip', userid,clientID);                                  
                             }
                             window.location.reload();
                        }
                    
                    });
                }
            }
        });

    });

    $('body').on('click', '.updateNotify', function(event) {

        var type = $(this).attr('Type');
        var thisEl = $(this);
        if(thisEl.hasClass('newActiveMark')==false)
            return false;

        $.ajax({
            url: BASE_URL + '/admin/index/updatenotify',
            data: {
                'Type': type
            },
            type: "POST",
            success: function(data) 
            {
                callNotify(type)
                $('.newMark', thisEl).fadeOut(function() 
                {
                    $(this).remove();
                    thisEl.removeClass('newActiveMark');
                });
            }
        });

    });

    // this is for user status
    $('body').on('click', '.userActiveInactive input, .updateDbeeUserStatusApproved', function(event) {
        event.preventDefault();
        var thisEl = $(this);
        var userid = thisEl.attr('user_id');
        var status = thisEl.attr('status');

        var msg = '';
        var statusEffect;
        var postURL = '';
        var postData = '';
        var activeCategory = false;
        var msgOnSuccess = '';
        if ($(this).closest('div').hasClass('updateUserDetailStatus') == true) {
            activeCategory = true;
            postURL = BASE_URL + "/admin/user/updateuserstatus";
            postData = {
                'userid': userid,
                'status': status
            };
            msgOnSuccess = 'User status updated successfully';
            if (thisEl.is(':checked') == false) {
                msg = 'Are you sure you want to make this user account active?';
            } else {
                msg = 'Are you sure you want to make this user account inactive?';
            }
        } else if ($(this).closest('div').hasClass('updateDbeeUserStatus') == true || status==2) {
            activeCategory = false;
            postURL = BASE_URL + "/admin/dashboard/updatedbeestatus";
            postData = {
                'dbeeid': userid,
                'status': status
            };
            msgOnSuccess = 'Post status updated successfully';
            if(status==2)
            {
                msg = 'Are you sure you want to approve this post?';
            }
            else
            {
                if (thisEl.is(':checked') == false) {
                    msg = 'Are you sure you want to activate this post?';

                } else {
                    //msg = 'You are about to make this post inactive. It will not be available on the platform any longer. <br> <br> Do you wish to continue?';
                    msg = 'Are you sure you want to deactivate this post?';
                }
            }
        }

        if (thisEl.closest('.userActiveInactive').hasClass('userStatusListingTable') == true) {

            if (thisEl.is(':checked') == false) {
                msg = 'Are you sure you want to make this user account inactive?';
            } else {
                msg = 'Are you sure you want to make this user account active?';
            }
        }

        $('#dialogConfirmSetting').remove();
        $('body').append('<div id="dialogConfirmSetting">' + msg + '</div>');

        $("#dialogConfirmSetting").dialog({
            resizable: false,
            title: 'Please confirm',
            width: 400,
            modal: true,
            buttons: {
                "Yes": function() {
                    $(this).dialog("close");
                    $.ajax({
                        url: postURL,
                        data: postData,
                        type: "POST",
                        success: function(data) {
                            $messageSuccess(msgOnSuccess);
                            if (thisEl.is(':checked') == false) {

                                if(localTick == false && data.status == 2  ){ // for db approve case
                                    socket.emit('checkdbee', true,clientID);
                                    socket.emit('chkactivitynotification', true,clientID);
                                }
                                if(data.status == 2)
                                {
                                    thisEl.closest('li').find('.activateId').remove();
                                    var tmpInactiveBtn = '<div class="helponoff userActiveInactive updateDbeeUserStatus" style="float:right">\
                                                            <input type="checkbox" id="switcherTarget_'+userid+'" user_id="'+userid+'" status="0">\
                                                            <label for="switcherTarget_'+userid+'">\
                                                            <div class="onHelp" on="Activate" off="Inactive"></div>\
                                                            <div class="onHelptext">\
                                                                <span>Activate</span>\
                                                                <span>Deactivate</span>\
                                                            </div>\
                                                            </label>\
                                                        </div>';

                                    thisEl.closest('.dataListCol2').prepend(tmpInactiveBtn);
                                    thisEl.closest('.updateDbeeUserStatus').remove(); 
                                   
                                }
                                else
                                {

                                    if (thisEl.closest('.userActiveInactive').hasClass('userStatusListingTable') == true) {
                                        thisEl.attr('status', '0');
                                    } else {
                                        thisEl.attr('status', '1');
                                    }
                                    
                                    thisEl.attr('checked', true);
                                    $(thisEl).closest('li').find('span.activateId').fadeIn();

                                    statusEffect = 'Inactive';
                                    statusEffectHtml = '<span style="color:green">Active</span>';

                                    if (activeCategory == true) {
                                        $('.popUserDetails').addClass('bounce');
                                        setTimeout(function() {
                                            $('.popUserDetails').removeClass('bounce');
                                        }, 1000);
                                    }
                                }
                            } else {
                                // for user deactive case
                                socket.emit('userActivaties', {userID:userid, uStatus:'INACTIVE'});
                                
                                thisEl.closest('li').find('.activateId').remove();
                                if (thisEl.closest('.userActiveInactive').hasClass('userStatusListingTable') == true) {
                                    thisEl.attr('status', '1');
                                } else {
                                    thisEl.attr('status', '0');
                                }
                                
                                
                                thisEl.attr('checked', false);

                                $(thisEl).closest('li').find('span.activateId').fadeOut();

                                statusEffect = 'Active';

                                statusEffectHtml = '<span style="color:red">Inactive</span>';
                                if (activeCategory == true) {
                                    $('.popUserDetails').addClass('bounce');
                                    setTimeout(function() {
                                        $('.popUserDetails').removeClass('bounce');
                                    }, 1000);
                                }
                            }
                            if (activeCategory == true) {
                                $('.statusEffect').text(statusEffect);
                            }

                            thisEl.closest('tr').find('.usrStatusTd').html(statusEffectHtml);

                        }
                    });
                }
            }
        });

    });


    $('body').on('click', '.deleteDbee', function(event) {
        event.preventDefault();
        var thisEl = $(this);
        var dbeeID = thisEl.attr('dbeeID');
        var pr = thisEl.closest('li');
        
        var msg = 'Are you sure you want to delete this post?';
        

        $('#dialogConfirmSetting').remove();
        $('body').append('<div id="dialogConfirmSetting">' + msg + '</div>');

        $("#dialogConfirmSetting").dialog({
            resizable: false,
            title: 'Please confirm',
            width: 400,
            modal: true,
            buttons: {
                "Yes": function() {
                    $(this).dialog("close");
                    $.ajax({
                        url: BASE_URL + "/admin/dashboard/deletepost",
                        data: {'dbeeID':dbeeID},
                        type: "POST",
                        success: function(data) {
                            $messageSuccess('Post deleted successfully');                           
                           
                            pr.remove();

                        }
                    });
                }
            }
        });

    });



     /*$('body').on('click', '.clientActiveInactive input', function(event) {

        event.preventDefault();
        var thisEl = $(this);
        var userid = thisEl.attr('ClientId');
        var status = thisEl.attr('status');


        var msg = '';
        var statusEffect;
        var postURL = '';
        var postData = '';
        var activeCategory = false;
        var msgOnSuccess = '';
        if ($(this).closest('div').hasClass('updateUserDetailStatus') == true) {
            activeCategory = true;
            postURL = BASE_URL + "/admin/user/updateuserstatus";
            postData = {
                'userid': userid,
                'status': status
            };
            msgOnSuccess = 'Client status updated successfully';
            if (thisEl.is(':checked') == false) {
                msg = 'Are you sure you want to make this Client account active?';
            } else {
                msg = 'Are you sure you want to make this Client account inactive?';
            }
        } 

    });*/

    // end this is for user status

    $('body').on('click', '.questionName', function(event) {
        var userid = $(this).attr('data-id');
        var username = $(this).attr('data-username');
        $('#userIDval').val(userid);
        $('#username').val(username);
        var dbeeid = $('#dbeeid').val();
        $('.questionName').removeClass('active');
        $(this).addClass('active');
        $.ajax({
            url: BASE_URL + "/admin/survey/ajaxsurveydetails",
            data: {
                'userid': userid,
                'dbeeid': dbeeid
            },
            type: "POST",
            beforeSend: function() {},
            success: function(data) {
                $('#surveyresultuser').html('<h1 class="pageTitle">' + username + '</h1>');
                $('#surveyresult').html(data.content);
                var jsonCode = '';
                $.each(data.json, function(i, value) {

                    jsonCode = $.parseJSON(value.chartjson);
                    // alert(value.chartjson);
                    piechartproviders(jsonCode, value.chartid, "", 'Survey report', '', 'rajtest2', 'pie');
                });

            }
        });
    });

    $('body').on('click', '.similarAnswer', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var answerid = $(this).attr('data-id');
        var ownerdataid = $('#userIDval').val();
        var username = $('#username').val();
        $('#detailsDialog').remove();
        var htmlLightbox = '<div id="detailsDialog"  title="Users who answered the same as ' + username + '">\
                                <div id="datacollect" style="float:none"></div><div id="grouplist2"></div>\
                                <div id="userInfoContainer"></div>\
                            </div>';
        $('body').append(htmlLightbox);

        $("#detailsDialog").dialog({
            dialogClass: 'detailsDialog',
            width: 800,
            height: 500,
            open: function(event, ui) {
                //$('html').attr('dbid', $(document).scrollTop()).css('overflow', 'hidden');
                $(this).dialog('option', 'position', {
                    my: 'center',
                    at: 'center',
                    of: window
                });
                $fluidDialog();
                $("#datacollect").html('');
                $('.ui-dialog-content').append('<div class="loaderOverlay2"></div>');
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    url: BASE_URL + '/admin/dashboard/ajaxsimilaruser',
                    data: {
                        'answerid': answerid,
                        'ownerdataid': ownerdataid
                    },
                    success: function(response) {
                        $('.loaderOverlay2').remove();
                        $('#userInfoContainer').html(response.content);
                        $('#grouplist2').html(response.grouplist);
                    }
                });
            },
            buttons: [{
                text: "Add selected users to admin set",
                click: function() {
                    thisEl = $(this);
                    var groupid = $("#similarusrid option:selected").val();
                    var userInfo = [];
                    if ($("#similarusrid option:selected").val() == '0') {
                        $messageError('Please select set');
                        return false;
                    }
                    if ($('input:checkbox[name=groupuser]').is(':checked') == false) {
                        $messageError('Please select set user');
                        return false;
                    }
                    $('input:checkbox[name=groupuser]').each(function() {
                        if ($(this).is(':checked'))
                            userInfo.push($(this).val());
                    });

                    var stringuserInfo = userInfo.join();

                    $.ajax({
                        type: "POST",
                        dataType: 'json',
                        data: {
                            'groupid': groupid,
                            'userid': stringuserInfo
                        },
                        url: BASE_URL + '/admin/usergroup/addgroupusergrp',
                        success: function(response) {
                            $('#similarAnswerDialog').remove();
                            $('#grpUser li.active').trigger('click');
                            $messageSuccess('set user inserted successfully');
                        },
                        error: function(error) {

                        }
                    });
                },

            }]
        });



    });




    $('.surveyLeftPanel li:first').trigger('click');


    // this is for reporting page
    $('body').on('click', '#reportListingMenu a', function(event) {
        var locations = location.pathname;
        locations = locations.split('/');
        var hasLocations = $(this).attr('href');
        hasLocations = hasLocations.split('#');
        if (locations[locations.length - 1] == 'reporting') {
            $('#reportWrapper a[href="#' + hasLocations[1] + '"]').trigger('click');
        }
    });
    // end this is for reporting page

    $("#commonsearch").keypress(function(e) {
        keyword = $.trim($(this).val());

        if (e.which == 13) {
            if (keyword.length >= 3) {

                data = 'keyword=' + keyword;
                $.ajax({
                    url: BASE_URL + "/admin/index/commonsearch",
                    data: data,
                    type: "POST",
                    beforeSend: function() {
                        $('.srcMainWrapper i').removeClass('fa-search').addClass('fa-spinner fa-spin');
                        $('#globalsearchWrapper').remove();
                        $('#container .pageContent').show()
                        $loader();
                    },
                    success: function(data) {
                        $('.overlayPopup a').trigger('click');
                        $removeLoader();
                        $('.srcMainWrapper i').removeClass('fa-spinner fa-spin').addClass('fa-search');
                        $('.searchAdvance').removeClass('on');
                        var data = data.split('~');
                        var globalsearch = '<div id="globalsearchWrapper"><h1 class="pageTitle">Search result: ' + data[1] + ' <button id="closeGlobalsearch" class="btn btn-black pull-right">Back</button></h1>' + data[0] + ' </div>';
                        $('#container .pageContent').hide().before(globalsearch);

                        $('.userThumberSilider').flexslider({
                            animation: "slide",
                            animationLoop: false,
                            itemWidth: 150,
                            itemMargin: 5
                        });

                        $('#postTotalWrp img.recievedUsePic').imageLoader();


                        $('#closeGlobalsearch').click(function() {
                            $('#globalsearchWrapper').remove();
                            $('#container .pageContent').show()
                        });



                    }
                });



                $(this).removeClass('error');
                return false;

            } else {
                $messageError('Please enter at least 3 characters')
            }
        }


    });


    $.extend($.ui.dialog.prototype.options, {
        modal: true,
        width: 'auto',
        maxWidth: 800,
        height: 'auto',
        fluid: true,
        resizable: false

    });

    Highcharts.setOptions({
        chart: {
            backgroundColor: 'none'
        }
    });

    $messageSuccess = function(content) {
        $('#successMsg').html('<i class="fa fa-check-circle"></i> ' + '<div class="upperTxt">' + content + '</div>').slideDown();
        setTimeout(function() {
            $('#successMsg').slideUp();
        }, 6000);
    }
    $messageError = function(content) {
        $('#errorMsg').html('<i class="fa fa-exclamation-circle"></i> ' + '<div class="upperTxt">' + content + '</div>').slideDown();
        setTimeout(function() {
            $('#errorMsg').slideUp();
        }, 6000);
    }

    $('.ftexpand').click(function() {
        $('.ftContainer').slideToggle();
    });

    $('#expandMenu').click(function() {
        $('body').toggleClass('showMenuLabel');
        $(this).find('i').toggleClass('fa-rotate-180');
    });
    // catch dialog if opened within a viewport smaller than the dialog width


    $fluidDialog = function() {

        var $visible = $(".ui-dialog:visible");
        // each open dialog
        $visible.each(function() {
            var $this = $(this);
            var dialog = $this.find(".ui-dialog-content").data("dialog");
            // if fluid option == true
            if (dialog.options.fluid) {
                var wWidth = $(window).width();
                // check window width against dialog width
                if (wWidth < dialog.options.maxWidth + 50) {
                    // keep dialog from filling entire screen
                    $this.css("max-width", "90%");
                } else {
                    // fix maxWidth bug
                    $this.css("max-width", dialog.options.maxWidth);
                }
                //reposition dialog
                dialog.option("position", dialog.options.position);
            }
        });

    }

    var windW = '';
    // resize window script
    $(window).resize(function() {
        $fluidDialog()
        var windH = $(this).height();
        windW = $(this).width();
        var footer = $('#footer').height() + 20;
        if(isMobile!=null){

            $('body').on('click', '.menuView', function() {
                $(document).scrollTop(0);
                    $('body').addClass('sideMenuOn');
                $('body, html').addClass('noScroll');
                $('#expandMenu').show();
            });


            $('body').on('click', '.closeMenu', function(e) {
                e.preventDefault();
                 $('body').removeClass('sideMenuOn');
                $('#expandMenu').hide();
                 $('body, html').removeClass('noScroll');
                $('body').removeClass('menuHover');

            });
        } else {
            
            $('#header').show().removeAttr('style');
        }


    }).resize();
    //end resize window script

    $('.topHeaderSearch').click(function (){
        $('html').addClass('topSearchActiveNow');
    });
/*    $(window).scroll(function() {
        if (windW <= 320) {
            var scrollTop = $(this).scrollTop();
            if (scrollTop > 100) {
                $('.searchAdvance').fadeOut();
                $('.notificationAlertBell').addClass('SearchOff');

            } else {
                $('.searchAdvance').fadeIn();
                $('.notificationAlertBell').removeClass('SearchOff');
            }
        };
    });*/

    /*dropdown*/

    $('body').on('click', '.dropDownTarget:not(.disabled)', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('.dropDown').not($(this).closest('.dropDown')).removeClass('on');
        $(this).closest('.dropDown').toggleClass('on');
    });

    $('body').on('click', '.dropDownList, .ui-datepicker', function(e) {
        //e.preventDefault();
        e.stopPropagation();
        $('ul.options').hide();
    });


    $('body').on('click', '.closeDialog', function(e) {
        $('.ui-dialog-titlebar-close').trigger("click");
    });

   

    if(isMobile!=null){
         $('html').on('touchstart', '.dropDownList, .ui-datepicker, .select .options, .subMenu ul, .MainMenu li', function(e) {
                e.stopPropagation();
            });
         $('html').on('touchstart', function(event) {
            if (event.target != '') {
                $('.dropDown').removeClass('on');
                $('.styledSelect').removeClass('active');
                $('body').removeClass('menuHover');
                $('.select .options').hide();
            }
        });
    }else{
        $('html').on('click', function(event) {
            if (event.target != '') {
                $('.dropDown').removeClass('on');
            }
        });
    }


    /*custome file upload*/
    $('body').on('change', '.fileType', function() {
        $('.uploaderror').remove();
        var value = $(this).val();
        var abc = $(this).closest('input').attr('value');
        $(this).closest('.uploadType').attr('value', abc)
        var attach_id = "fileType";
        var size = $(this).closest('.' + attach_id)[0].files[0].size;
        fileSize = size / 1048576;
        var vns = $(this).closest('.appendType').find('.uploadType');
        if (fileSize < 10.0) {
            vns.val($(this).closest('.' + attach_id)[0].files[0].name);
        } else {
            vns.val('');
            $(this).closest('.appendType').append("<span class='uploaderror'>Error: Selected file is more than the allowed filesize</span>");
            return false;
        }
    });

    $('.appendType input[type="file"]').change(function(event) {
        var value = $(this).val();
        if(value!=''){ 
            if($(this).closest('.btn-group').is(':visible')==false && $(this).closest('.dropDownList').is(':visible')==false){
                $(this).closest('.btn-black').wrap('<div class="btn-group"></div>').after('<a href="javascript:void(0)"  class="btn resetValueUpload"><i>Clear</i><i class="fa fa-times"></i></a>');              
            }
            if('#signBgImg', $(this).closest('.signinBgRow').is(':visible')==true){
                $('#signinBgReset').trigger('click');
            }
        }
    });
    
    $('body').on('change', '#formknowledgecenter .fileType', function() {
        var adminProfileimg = $(this).attr('name');
        if (adminProfileimg == 'profilepicture') {
            var value = $(this).val();
            $(this).closest('.appendType').find('.uploadType').val(value);
        } else {
            $('.uploaderror').remove();
            var value = $(this).val();
            var attach_id = "fileType";
            var size = $('.' + attach_id)[0].files[0].size;
            var exts = ['pdf'];
            var fname = $('.' + attach_id)[0].files[0].name;
            var get_ext = fname.split('.');
            get_ext = get_ext.reverse();
            /*if ($.inArray(get_ext[0].toLowerCase(), exts) > -1) {} else {
                $messageError("Please select a Pdf file only");
                return false;
            }*/

            fileSize = size / 1048576;
            var vns = $(this).closest('.appendType').find('.uploadType');
            if (fileSize < 15.0) {
                vns.val($('.' + attach_id)[0].files[0].name);
            } else {
                vns.val('');
                vns.after("<span class='uploaderror'>Error: Selected file is more than the allowed filesize</span>");
                return false;
            }
        }

    });
    /*end custome file upload*/
    /*image upload for admin*/
    /*$('body').on('change','.adminProfileimage', function(){  
      var value = $(this).val();
      $(this).closest('.appendType').find('.uploadType').val(value);
    });*/
    /*end image upload for admin*/
    /*Loader */

    //star dbLoader
    $dbLoader = function(options) {
        var defaults = {
            page: false,
            overlay: false,
            append: false,
            overlayColor: 'rgba(0,0,0,0.6)',
            prepend: false,
            class: '',
            color: '',
            height: true,
            loaderHtml: '<i class="fa fa-spinner fa-spin fa-3x"></i>',
            element: '',
            close: false,
            process:false,
            totalUpload:'0%',
            apClass: 'notLoaderCenter'
        }
        var settings = $.extend({}, defaults, options);


        var loaderHtml = '<div class="loaderWrp">' + settings.loaderHtml + '</div>';

        var target = $(settings.element);
        var htmlTemplate = loaderHtml;
        var pageClass = '';

        if ($('.overlay', target).is(':visible') == true) {
            $('.overlay', target).remove();
            $('.loaderWrp', target).remove();
            target.removeClass('loaderCntWrp');
        }

        if (settings.close == true) {
            if ($('.overlay').hasClass('pageTrue') == true) {
                $('.overlay').remove();
            } else {
                $('.overlay', target).remove();
                $('.loaderWrp', target).remove();
                target.removeClass('loaderCntWrp');

            }
            return false;

        }
        if (settings.page == true) {
            pageClass = 'pageTrue';
            htmlTemplate = '<div class="overlay ' + pageClass + '" style="background:' + settings.overlayColor + '; color:' + settings.color + ';">' + htmlTemplate + '</div>';
        }
         if (settings.process == true) {
            $('#mesageNotfiOverlay').remove();
            htmlTemplate = '<div id="mesageNotfiOverlay" class="overlay" style="background:' + settings.overlayColor + '; color:' + settings.color + ';">\
            <div class="msgNoticontent"> ' + settings.loaderHtml + '\
            <br><div class="progressBarWrp" style="margin-top:20px;"" data-loaded="'+settings.totalUpload+'%"><div class="progressBar" style="width:'+settings.totalUpload+'%"></div></div>\
            </div></div>';
            if(settings.totalUpload==100 && CloseLoader == true){
                  setTimeout(function() {
                    $('#mesageNotfiOverlay').fadeOut(function() {
                        $(this).remove();
                    });
                }, 2000);
            }
        }
        

        if (settings.append == false && settings.prepend == false && settings.process == false && settings.page == false && settings.overlay == false || settings.append == true && settings.prepend == true) {
            var heightEl = target.height();
            target.html('');
            target.html(htmlTemplate);
            if (settings.height == true) {
                $('.loaderWrp', target).css({
                    height: heightEl,
                    position: 'relative',
                    color: settings.color
                });
            }
        } else if (settings.overlay == true && settings.page == false) {
            target.addClass('loaderCntWrp').append('<div class="overlay"  style="background:' + settings.overlayColor + '; color:' + settings.color + ';">' + htmlTemplate + '</div>');
        } else {
            if (settings.append == true) target.append(htmlTemplate);
            if (settings.prepend == true) target.prepend(htmlTemplate);
            if (settings.prepend == true || settings.append == true) $('.loaderWrp', target).addClass(settings.apClass);
            if (settings.page == true || settings.process == true) $('body').append(htmlTemplate);
        }
    }



    //end dbLoader

    $loader = function() {
        $('body').append('<div class="loaderOverlay" id="removeLoaderAjax"></div>');
    }
    $removeLoader = function() {
            $('#removeLoaderAjax').remove();
        }
        /*Loader */

    /*Scrolling*/
    $scrolling = function(element, value) {
            var offset = $(element).offset();
            $('body,html').animate({
                scrollTop: offset.top - 135
            });
        }
        /*Scrolling*/


    $select = function(element) {

        // Iterate over each select element
        $(element).each(function() {

            // Cache the number of options
            var $this = $(this),
                numberOfOptions = $(this).children('option').length;

            if ($this.closest('div').hasClass('select') == true) {
                $this.closest('.select').find('.styledSelect, .options').remove();
                $this.unwrap();

            }

            // Hides the select element
            $this.addClass('s-hidden');
            var disabled = '';

            // Wrap the select element in a div
            if ($this.is(':disabled') == true) {
                disabled = 'disabled';
            }
            $this.wrap('<div class="select ' + disabled + '"></div>');


            // Insert a styled div to sit over the top of the hidden select element
            $this.after('<div class="styledSelect oneline"></div>');

            // Cache the styled div
            var $styledSelect = $this.next('div.styledSelect');

            // Show the first select option in the styled div
            $styledSelect.text($this.children('option:selected').text());

            // Insert an unordered list after the styled div and also cache the list
            var $list = $('<ul />', {
                'class': 'options'
            }).insertAfter($styledSelect);

            // Insert a list item into the unordered list for each select option
            var actionsHtml = '';
            if ($(this).attr('actions') != undefined) {
                actionsHtml = $(this).attr('actions');
            }
            for (var i = 0; i < numberOfOptions; i++) {
                $('<li />', {
                    text: $this.children('option').eq(i).text(),
                    rel: $this.children('option').eq(i).val(),
                    style: $this.children('option').eq(i).attr('style')
                }).appendTo($list);
            }

            // Cache the list items
            var $listItems = $list.children('li:not(":first")').append(actionsHtml);


            // Hides the unordered list when clicking outside of it
            $('body,html').click(function() {
                $styledSelect.removeClass('active');
                $list.hide();
            });




        });


    }

    /*********************************this is event for custom dropdown*****************************************************/
    // Show the unordered list when the styled div is clicked (also hides it if the div is clicked again)
    $('body').on('click', '.select:not(.disabled)', function(e) {
        e.stopPropagation();
        $(optionsUl, this).removeClass('bottom');
        var optionsUl = 'ul.options';
        if ($(optionsUl, this).is(':visible') == true) {
            $(optionsUl, this).hide();
            $('.styledSelect', this).removeClass('active');
        } else {
            $(optionsUl).hide().siblings('.active').removeClass('active');
            $(optionsUl, this).show();
            $('.styledSelect', this).addClass('active');
            if ($(this).closest('#topHeader').attr('id') == 'topHeader') {
                $('#topHeader').addClass('activePosition');
                optionHeight = $(optionsUl, this).height();
                optionTopPos = $(optionsUl, this).offset().top;
                $('#topHeader').removeClass('activePosition');

                if ($(window).height() < optionTopPos + optionHeight) {
                    $(optionsUl, this).addClass('bottom');
                }
            }

        }
    });

    // Hides the unordered list when a list item is clicked and updates the styled div to show the selected list item
    // Updates the select element to have the value of the equivalent option
    $('body').on('click ', '.select ul li', function(e) {
        e.stopPropagation();
        var closest = $(this).closest('.select');
        var valueSelected = $(this).attr('rel');
        closest.find('.styledSelect').text($(this).text()).removeClass('active');
        closest.find('select').val($(this).attr('rel')).trigger('change');
        closest.find('option').removeAttr("selected");
        closest.find('option[value="' + valueSelected + '"]').attr("selected", "selected");
        closest.find('ul').hide();
    });

    /*********************************end this is event for custom dropdown*****************************************************/

    $select('select:not(.select2)');

    var subMenuHide = false;
if(isMobile==null){
    $('.MainMenu > li a').mouseover(function() {
        $('body').addClass('menuHover');
        if ($(this).closest('li').find('ul').length != 0) {
            var offest = $(this).offset();
            $('.subMenu ul').remove('');
            $(this).next().clone().appendTo('.subMenu').show().css({
                top: 60
            });
            var subMenuHide = false;
        } else {
            subMenuHide = true;
            $('body').removeClass('menuHover');
        }

    });

    $('.subMenu').mouseleave(function() {
        $('body').removeClass('menuHover');
        subMenuHide = true;
    });
     $('body:not(.MainMenu > li)').on('click', function(e) {
        e.stopPropagation();
        $('body').removeClass('menuHover');
    });
}else{
    $('.subMenu').css({zIndex:'1001', width:'100%'});
    $('.MainMenu > li a').click(function() {
           
            if( $(this).closest('li').hasClass('active')==true){
                 $('body').removeClass('menuHover');
                  $(this).closest('li').removeClass('active');
            }else{
                $(this).closest('.MainMenu').find('li').removeClass('active');
                $(this).closest('li').addClass('active');
                 $('body').addClass('menuHover');
                  if ($(this).closest('li').find('ul').length != 0) {
                    var offest = $(this).offset();
                    $('.subMenu ul').remove('');
                    $(this).next().clone().appendTo('.subMenu').show();
                    var subMenuHide = false;
                } else {
                    subMenuHide = true;
                    $('body').removeClass('menuHover');
                }

           }
       
    });
}

   


    // ****** Showing individual users  details from users section
    $("body").on('click', '.delcomm', function(e) {
        Anithis = $(this);
        var comment_id = Anithis.attr('commid');
        //alert(comment_id);
        var confirmDe = '<div id="confirBoxdel" title="Please confirm"><p>Are you sure you would like to delete this comment?</p></div>';
        $('body').append(confirmDe);
        $('#confirBoxdel').dialog({
            minWidth: 300,
            buttons: {
                "Yes": function() {
                    var thisPopup = $(this);
                    $.ajax({
                        type: "POST",
                        url: BASE_URL + "/admin/dashboard/deletecomment",
                        data: {
                            "CommentID": comment_id
                        },
                        success: function(result) {
                            if (result == 'deltrue') {
                                $messageSuccess("Comment deleted succesfully");
                                thisPopup.dialog("close");
                                $('#confirBoxdel').remove();
                                Anithis.closest('li').remove();
                                return false;
                            }

                        }
                    });
                }
            }
        });

    });



    $("body").on('click', '.show_details_user', function(e) {
        e.preventDefault();
        $("#detailsDialog").remove();
        var userId = $(this).attr('userid');
        data = "userid=" + userId;
        url = BASE_URL + "/admin/user/getuserdetails";
        var htmlLightbox = '<div id="detailsDialog"  title="User Information">\
                                <div id="datacollect" style="float:none"></div>\
                                <div id="userInfoContainer"></div>\
                            </div>';

        $('body').append(htmlLightbox);

        $("#detailsDialog").dialog({
            dialogClass: 'detailsUserDialogBox',
            width: 800,
            height: 650,
            modal: true,
            draggable: false,
            open: function() {
                $fluidDialog();
                $.ajax({
                    url: url,
                    data: data,
                    method: 'POST',
                    beforeSend: function() {
                        $("#datacollect").html('');
                        $('.ui-dialog-content').append('<div class="loaderOverlay2"></div>');
                    },
                    success: function(result) {
                        $('.loaderOverlay2').remove();
                        var param = result.split("~");
                        $("#datacollect").html(param[0]);
                        $select('.popUserDetails #grpselect');
                        //   $('.detailsUserDialogBox .ui-dialog-title').after(param[4]);

                        if (param[1] == 99) {
                            $("#userInfoContainer").html('<h2 class="notfound">no user activity found</h2>');
                        } else {

                            $("#userInfoContainer").css({
                                height: 300
                            }).html(donuteofdbees(JSON.parse(param[1]), 'userInfoContainer', 'My Dbee Activity '));
                            $('#datacollect').after('<div class="popupUserDetails">' + param[3] + '</div>');
                        }

                        $('#datacollect .box_image img').imageLoader();
                        //$('.ui-dialog').draggable();

                    }
                });
            }
        });



    });
    // ****** Showing individual users  details from DBEE section
    var SelectDropDown = {
        "User name": ["Is", "Similar to"],
        "Email address": ["Is", "Similar to"],
        "Login date": ["On"],
        "Register date": ["On", "Before", "After"],
        "Posts": ["Containing"],
        "Comments": ["By", "On user's posts"],
        "Group name": ["Containing"],
        "Groups type": {
            "0": "Is",
            "more": {
                "Open": "Open",
                "Request": "Request",
                "Closed": "Closed"
            }
        },
        "Group category": ["Is"],
        /*"@User references": ["To", "By"]*/
    }

    var selectOptions = '<select class="operator" name="operator[]"></select>';
    $('.popupHeader').append(selectOptions);

    $.each(SelectDropDown, function(key, value) {
        var opVal = '<option value="' + key + '">' + key + '</option>';
        if ((key == 'Scored' && PLATEFORM_SCORING == '1') || (key == 'Score Type' && PLATEFORM_SCORING == '1')) opVal = '';
        $('.popupHeader .operator').append(opVal);
        $select('.popupHeader .operator');

    });
    $('body').on('change', '.operator', function() {
        var ddValue = $(this).val();

        var selectedValue = SelectDropDown[$(this).val()];
        var thisEl = $(this);
        var header = '';
        var singleValueCondition = false;


        var selectOptions = '<select class="selectOptions" ></select>';
        if (ddValue == 'Group category' || ddValue == 'Groups type' || ddValue == 'Group name' || ddValue == 'Posts') {
            singleValueCondition = true;
            var selectConditions = '<input type="text" readonly class="conditions" name="conditions[]" style="width:80px; min-width:80px; margin-right: 5px;">';
        } else {
            var selectConditions = '<select class="conditions" name="conditions[]" ></select>';
        }

        var addBtn = '<button type="button" class="btn btn-green pull-right" id="addConditions">\
                         Add\
                    </button>';

        var removeBtn = '<button class="btn pull-right removeCondition">\
                       <i class="fa fa-minus"></i>\
                    </button>';

        $(this).closest('.select').nextAll().remove();
        $(this).closest('.select').after(selectConditions);

        if ($(this).closest('.popupHeader').hasClass('popupHeader')) {
            header = '.popupHeader';
            $(header).append(addBtn);

        } else {
            header = '.conditionRow';
            thisEl.closest(header).append(removeBtn);
        }


        $.each(selectedValue, function(key, value) {
            if (key != "more") {
                var placeholder = '';

                if (ddValue == "Email address") placeholdertext = 'Enter user\'s email';
                else if (ddValue == "Group name") placeholdertext = 'Enter keyword';
                else if (ddValue == "Posts") placeholdertext = 'Enter keyword';
                else if (ddValue == "Register date" || ddValue == "Login date") placeholdertext = 'Select date';
                else placeholdertext = "Enter user's name";

                if (ddValue == 'Group category') {
                    thisEl.closest(header).find('.conditions').after(selectOptions);
                    thisEl.closest(header).find('.fieldCondition').remove();
                    var firstValueCate = '';
                    var countV = 0;
                    //alert(''); console.log(GROUP_CATEGORIES)
                    $.each(GROUP_CATEGORIES, function(gkeyMore, gvalueMore) {
                        if (countV == 0) {
                            firstValueCate = gvalueMore;
                        }

                        thisEl.closest(header).find('.selectOptions').append('<option value="' + gkeyMore + '">' + gvalueMore + '</option>');
                        countV += 1;
                    });


                    thisEl.closest(header).append('<input type="hidden" class="fieldCondition" name="selectOptions[]" value="' + firstValueCate + '">');
                    thisEl.closest(header).find('.selectOptions').removeAttr('name').trigger('change');


                } else if (key == 0) {
                    if (value == 'Similar to') {
                        thisEl.closest(header).append('<input type="text" class="pull-right fieldCondition likeCondition" name="selectOptions[]" placeholder="' + placeholdertext + '" >');
                    } else if (value == 'On' || value == 'By') {
                        thisEl.closest(header).append('<input type="text" class="fieldCondition" name="onOptions[]"  value="" placeholder="' + placeholdertext + '" >');

                    } else {

                        thisEl.closest(header).append('<input type="text" placeholder="' + placeholdertext + '" class="fieldCondition" name="selectOptions[]" >');
                    }

                }

                if (value == 'Before' || value == 'After' || value == 'Same as' || (ddValue=='Login date' && value=='On')) {
                    $('.fieldCondition').datepicker({
                        changeMonth: true,
                        changeYear: true,
                        dateFormat: 'dd-mm-yy',
                        onSelect: function(date) {
                            $(this).next('input[type="hidden"]').val(date);
                        }
                    }).attr('readonly', true);
                    $("#ui-datepicker-div").addClass('topSrcPicker');


                }
                if (singleValueCondition == true) {
                    thisEl.closest(header).find('.conditions').val(value);

                } else {
                    thisEl.closest(header).find('.conditions').append('<option value="' + value + '">' + value + '</option>');

                }


            } else if (key == "more") {

                thisEl.closest(header).find('.conditions').after(selectOptions);
                thisEl.closest(header).find('.fieldCondition').remove();
                var countNum = 0;
                var defaultValSelectOption = '';
                $.each(value, function(keyMore, valueMore) {
                    if (countNum == 0) defaultValSelectOption = valueMore;
                    thisEl.closest(header).find('.selectOptions').append('<option value="' + keyMore + '">' + valueMore + '</option>');
                    countNum++;
                });
                thisEl.closest(header).find('.selectOptions').removeAttr('name');
                thisEl.closest(header).append('<input type="hidden" class="fieldCondition" name="selectOptions[]" value="' + defaultValSelectOption + '">');
            }

        });




        $select('' + header + ' select.conditions, ' + header + ' .selectOptions');
    });

    $('body').on('change', '.selectOptions', function() {
        var thisEl = $(this);
        var header = '.popupHeader';
        thisEl.closest(header).find('.fieldCondition').remove();
        thisEl.closest(header).append('<input type="hidden" class="fieldCondition" name="selectOptions[]" value="' + thisEl.val() + '">');

    });

    $('body').on('change', '.conditions', function() {
        var thisCon = $(this);

        var value = thisCon.val();

        var header = '';
        if (thisCon.closest('.popupHeader').hasClass('popupHeader')) {
            header = '.popupHeader';
        } else {
            header = '.conditionRow';
        }
        var opratorValue = thisCon.closest(header).find('.operator').val();

        if (opratorValue == "Email address") placeholdertext = 'Enter user\'s email';
        else if (opratorValue == "Group name") placeholdertext = 'Enter keyword';
        else if (opratorValue == "Posts") placeholdertext = 'Enter keyword';
        else if (opratorValue == "Register date" || opratorValue == "Login date" ) placeholdertext = 'Select date';
        else placeholdertext = "Enter user's name";

        var prnt = thisCon.closest(header);
        if (value == 'Similar to') {
            thisCon.closest(header).find('.fieldCondition').remove();
            thisCon.closest(header).append('<input placeholder="' + placeholdertext + '"  type="text" class="fieldCondition likeCondition" name="likeOptions[]" >');
        } else if (value == 'On' || value == 'By') {
            thisCon.closest(header).find('.fieldCondition').remove();
            thisCon.closest(header).append('<input placeholder="' + placeholdertext + '" type="text" placeholder="Enter user" value="" class="fieldCondition" name="onOptions[]">');
        } else {
            if (opratorValue != 'Post Type' && opratorValue != 'Comment Type' && opratorValue != 'Score Type' && opratorValue != 'Groups type') {
                thisCon.closest(header).find('.fieldCondition').remove();
                thisCon.closest(header).append('<input placeholder="' + placeholdertext + '" type="text" class="fieldCondition" name="selectOptions[]" >');
            }
        }


        if (value == 'Before' || value == 'After' || value == 'On') {
            $('.fieldCondition').datepicker({
                    changeMonth: true,
                    changeYear: true,
                    dateFormat: 'dd-mm-yy',
                    onSelect: function(date) {
                        $(this).next('input[type="hidden"]').val(date);
                    }
                })
                .attr('readonly', true);
            $("#ui-datepicker-div").addClass('topSrcPicker');
        }
    });


    $('#saveSearchCondition').click(function() {
        $(this).closest('.dropDownList').find('.popupContent').addClass('saveFilterNow');

    });


    $('body').on('click', '.cancelSaveFilter', function() {

        if ($(this).parents().hasClass('creatGWrap') == true) {
            $(this).parents('.creatGWrap').removeClass('saveFilterNow');
            $('.grpWrapperBox').removeAttr('style');
        } else {
            $(this).closest('.dropDownList ').find('.popupContent ').removeClass('saveFilterNow');
        }

    });

    $('.searchAdvance').on('click', '#addConditions', function() {
        $('.popupHeader input[type="text"]').removeClass('error');
        var inputVal = $('.popupHeader input[type="text"]').val();
        var opratorValue = $('.popupHeader .operator').val();
        if ($.trim(inputVal) == '' && inputVal != undefined) {
            $('.popupHeader input[type="text"]').addClass('error').focus();
            $messageError('Please fill required field');
            return false;
        }

        $('.popupContent h2.notfound').fadeOut('fast');
        $('#saveSearchCondition, #clearSearchFilters').fadeIn('fast');
        var headerValues = $('.popupHeader').serializeArray();
        var headerValuesFileds = '';
        var headerValuesHtml = '';
        $.each(headerValues, function(index, val) {
            headerValuesFileds += '<input type="hidden" value="' + val.value + '" name="' + val.name + '" readonly>';
            if (index == 2) {
                if (opratorValue == 'Group category') {
                    headerValuesHtml += ' <strong>' + $('.popupHeader .selectOptions option[value="' + val.value + '"]').text() + '</strong>';
                } else {
                    headerValuesHtml += ' <strong>' + val.value + '</strong>';
                }
            } else {
                headerValuesHtml += val.value + ' ';
            }

        });

        $('#selectLoadFilter').hide();
        $('.popupContent').append('<div class="whiteBox conditiontext conditionRow">\
                ' + headerValuesHtml + '\
                  <span style="display:none" class="conditiontext">' + headerValuesFileds + '</span>\
                   <a class="closeCondition removeCondition" href="javascript:void(0)"></a>\
              </div>');




    });

    var searchNotFound = function(conditionsFlag) {
        var loadFilter = $('#loadSelectedFilter');
        if (loadFilter.is(':visible') == true && loadFilter.html() != '') {
            conditionsFlag = false;
        }
        if (conditionsFlag == true) {
            $('.popupContent h2.notfound').fadeIn('fast');
            $('#saveSearchCondition, #clearSearchFilters').fadeOut('fast');
        }
    }

    // remove conditions
    $('body').on('click', '.removeCondition', function() {
        var conditionsFlag = false;
        var totalRow = $('.popupContent .conditionRow:not(.filterRows)').size();
        $(this).closest('.conditionRow').fadeOut(function() {
            $(this).remove();
            if (totalRow == 1) {
                conditionsFlag = true;
            }
        });
        setTimeout(function() {
            searchNotFound(conditionsFlag);
        }, 500)

    });
    // remove conditions


    $.fn.imageLoader = function() {
        $(this).hide();
        this.each(function() {
            $(this).after('<div class="recievedUsePic"></div>');
            $(this).load(function() {
                var thisEL = $(this);
                $(this).siblings('div.recievedUsePic').fadeOut(function() {
                    thisEL.fadeIn();
                    $(this).remove();
                });

            });
        });
    }

    //this is click event for search btn
    $('.searchAdvance').on('click', '#advanceSearchBtn', function() {

        //$('.searchAdvance .popupContent input[type="text"]:not(#fname), .popupContent input[type="hidden"], .searchAdvance .popupContent select').each(function(){ 
        //  if($(this).val()==''){
        //  $(this).addClass('error').focus();
        // $messageError('Fields are required');
        // }
        // else{

        formValue = $('#searchAdvanceForm:not(#fname)').serializeArray();

        //console.log(formValue);
        var count = 0;
        var bigvar = '';
        var condi = '';
        var errorFlag = false;

        if (formValue.length == 1) {
            $messageError("Please click the 'Add' button");
            errorFlag = true;
        }


        $.each(formValue, function(i, fd) {

            if (fd.name != 'filterName') {
                if ($.trim(fd.value) == '') {
                    $messageError('Fields are required');
                    $('#searchAdvanceForm input[name="' + fd.name + '"]').addClass('error').focus();
                    errorFlag = true;
                } else {
                    $('#searchAdvanceForm input[name="' + fd.name + '"]').removeClass('error');
                }

                if (count % 3 == 0) {
                    condi = '';
                }

                if (fd.name != 'selectOptions[]') {
                    condi += fd.value + '~';
                    //condi  += fd.value+'~'+getGlobalfieldsandoperators(fd.value)+'~'; 
                } else {
                    condi += fd.value;
                }
                count++;
            }
            if (count % 3 == 0) {
                bigvar += condi + ',';
            }
        });

        //console.log(condi);
        //'Text like %'+keyword+'% OR VidDesc like %'+keyword+'% OR LinkDesc like %'+keyword+'% OR PicDesc like %'+keyword+'% OR PollText like %'+keyword+'%';
        //console.log( trim( bigvar, ',' ));
        data = 'allinone =' + trim(bigvar, ',');



        if (errorFlag == true) {
            return false;
        }
        $.ajax({
            url: BASE_URL + "/admin/index/globalsearch",
            data: data,
            type: "POST",
            beforeSend: function() {
                $('#globalsearchWrapper').remove();
                $('#container .pageContent').show()
                $removeLoader();
                $loader();
            },
            success: function(data) {
                  $('.overlayPopup a').trigger('click');
                $removeLoader();
                //$('.searchAdvance').removeClass('on');
                $('.dropDown').removeClass('on');
                var data = data.split('~');
                var globalsearch = '<div id="globalsearchWrapper"><h1 class="pageTitle">Search results: ' + data[1] + ' <button id="closeGlobalsearch" class="btn btn-black pull-right">Back</button></h1>' + data[0] + ' </div>';
                $('#container .pageContent').hide().before(globalsearch);

                /////////kkk
                $('#globalsearchWrapper img.recievedUsePic').imageLoader();

                $('#closeGlobalsearch').click(function() {
                    $('#globalsearchWrapper').remove();
                    $('#container .pageContent').show()
                });

            }
        });



        // }
        //  });

    });



    //this is click event for search btn

    function trim(str, chr) {
        var rgxtrim = (!chr) ? new RegExp('^\\s+|\\s+$', 'g') : new RegExp('^' + chr + '+|' + chr + '+$', 'g');
        return str.replace(rgxtrim, '');
    }

    function getGlobalfieldsandoperators(type, keyword) {
        switch (type) {
            case 'User':
                return 'Name';
                break;
            case 'Email':
                return 'Email';
                break;
            case 'Register date':
                return 'RegistrationDate';
                break;
            case 'Dbee':
                return 'dbee';
                break;
            case 'Dbee Options':
                return 'Type';
                break;
            case 'Comments':
                return 'UserID';
                break;
            case 'Comment Options':
                return 'Type';
                break;
            case 'Score':
                return 'UserID';
                break;
            case 'Score Options':
                return 'Score';
                break;
            case 'Groups':
                return 'GroupName';
                break;
            case 'Groups Options':
                return 'GroupPrivacy';
                break;
            case 'Mentions':
                return 'Name';
                break;
            case 'Is':
                return dbcondi = '=';
                break;
            case 'Is Not':
                return dbcondi = '!=';
                break;
            case 'Like':
                return dbcondi = 'like';
                break;
            case 'sameas':
                return dbcondi = '=';
                break;
            case 'Before':
                return dbcondi = '<';
                break;
            case 'After':
                return dbcondi = '>';
                break;
            case 'On':
                return dbcondi = 'on';
                break;
            case 'By':
                return dbcondi = 'by';
                break;
            default:
                return ' ';
                break;
        }
        //return ret;
    }

    $('body').on('click', '#loadFilterAdv', function() {
        $('.popupContent .conditionRow:not(.filterRows)').remove();
        $('.popupContent .notfound, #loadSelectedFilter, #clearSearchFilters,#clearSearchFilters,#saveSearchCondition').fadeOut(function() {
            $('#selectLoadFilter').fadeIn();
        });


    });
    //this is click event for save filter name  btn
    $('#saveFilterName').click(function() {
        var filterName = $('#fname').val();
        if (filterName == '') {
            $messageError('Please insert Filter name');
            $('#fname').addClass('error');
        } else {
            $('#fname').removeClass('error');

            $('.searchAdvance  .popupContent input:not(#fname)').each(function() {
                if ($(this).val() == '') {
                    $(this).addClass('error').focus();
                    $messageError('Fields are required');
                } else {
                    formValue = $('#searchAdvanceForm:not(#fname)').serializeArray();

                    console.log(formValue);
                    var count = 0;
                    var bigvar = '';
                    var condi = '';
                    $.each(formValue, function(i, fd) {

                        if (fd.name != 'filterName') {
                            if (count % 3 == 0) {
                                condi = '';
                            }

                            if (fd.name != 'selectOptions[]') {
                                condi += fd.value + '~';
                                //condi  += fd.value+'~'+getGlobalfieldsandoperators(fd.value)+'~'; 
                            } else {
                                condi += fd.value;
                            }
                            count++;
                        }
                        if (count % 3 == 0) {
                            bigvar += condi + ',';
                        }
                    });

                    //console.log(condi);
                    //'Text like %'+keyword+'% OR VidDesc like %'+keyword+'% OR LinkDesc like %'+keyword+'% OR PicDesc like %'+keyword+'% OR PollText like %'+keyword+'%';

                    data = 'allinone =' + trim(bigvar, ',') + '&filtername=' + filterName;
                    console.log(data);
                    $.ajax({
                        url: BASE_URL + "/admin/index/saveglobalfiletrs",
                        data: data,
                        type: "POST",
                        beforeSend: function() {
                            //$('#globalsearchWrapper').remove();
                        },
                        success: function(data) {
                            if (data == '403') {
                                $messageError('You cannot save duplicate filter names')
                            } else if (data == '404') {
                                $messageError('filter names can not be null')
                            } else {
                                $messageSuccess('Filter saved successfully');
                                var selectLoadFilter = data;
                                $('#loadFilterAdv').fadeIn();
                                $('#selectLoadFilter').html(selectLoadFilter);
                                $('#fname').val('');
                                $('.popupContent').removeClass('saveFilterNow');
                            }
                        }
                    });
                    $(this).removeClass('error');
                    return false;
                }


            });
        }
    });
    //this is click event for save filter name  btn

    //Global filter on change

    $('body').on('click', '.conditionRow .loadFilter', function() {
        var filterid = $(this).closest('.conditionRow').attr('data-value');
        data = 'filterid=' + filterid + '&global=1';
        $.ajax({
            url: BASE_URL + "/admin/index/loadglobalfiletrs",
            data: data,
            type: "POST",
            beforeSend: function() {
                //$('#globalsearchWrapper').remove();
            },
            success: function(data) {
                $('#loadSelectedFilter').remove();
                $('.conditionRow:not(.filterRows)').remove();
                $('.popupContent .notfound, #selectLoadFilter').fadeOut();
                $('#clearSearchFilters').fadeIn();
                $('.popupContent').append('<div id="loadSelectedFilter">' + data + '</div>');
            }
        });
    });
    $('body').on('click', '.ui-widget-overlay', function(event) {
        event.stopPropagation();
        /* Act on the event */
    });

    $('body').on('click', '.removeFilter', function(e) {
        var thisBtn = $(this);
        var msg = 'Are you sure you want to remove this filter?';
        $('#dialogConfirmSetting').remove();
        $('body').append('<div id="dialogConfirmSetting">' + msg + '</div>');

        filterid = thisBtn.closest('.conditionRow').attr('data-value');
        var countFilter = $('#selectLoadFilter .filterRows').size();

        $("#dialogConfirmSetting").dialog({
            resizable: false,
            title: 'Please confirm',
            width: 400,
            modal: true,
            buttons: {
                "Yes": function(e) {
                    e.stopPropagation();
                    $.ajax({
                        url: BASE_URL + "/admin/index/deleteglobalfiletrs",
                        data: {
                            filterid: filterid
                        },
                        type: "POST",
                        success: function(data) {
                            thisBtn.closest('.conditionRow').remove();
                            if (countFilter == 1) {
                                $('#loadFilterAdv').fadeOut();
                                $('.popupContent .notfound').fadeIn();
                            }
                            if (data == 1) $messageSuccess('Filter deleted successfully');
                            else $messageError('Something went wrong, please try again');
                        }
                    });
                    $(this).dialog("close");
                },
                Cancel: function(e) {
                    e.stopPropagation();
                    $(this).dialog("close");

                }
            }
        });
    });


    // this is for selected first oprator
    $('.popupHeader .select li:first').click();
    // this is for selected first oprator

    $('#clearSearchFilters').click(function() {
        $('#loadSelectedFilter').fadeOut('fast', function() {
            $(this).remove();

        });
        $('.popupContent .conditionRow:not(.filterRows)').fadeOut('fast', function() {
            $(this).remove();
        });
        $('.popupContent h2.notfound').fadeIn('fast');
        $('#saveSearchCondition, #clearSearchFilters').fadeOut('fast');
    });


    // advances search autocomplete 

    $('body').on('click', '.howadswork', function() {
        $('.adsnote').slideToggle();
    });


    /*Advances main search */


    $('body').on('click', '.dbtab li a', function(e) {
        e.stopPropagation();
        e.preventDefault();
        var currentEl = $(this);
        var offTop = currentEl.offset().top;
        currentEl.closest('ul').find('.active').removeClass('active');
        currentEl.closest('li').addClass('active');
        var dbTabContent = currentEl.attr('href');
        window.location.hash = dbTabContent + '&tab';
        currentEl.closest('.dbtabContainer').find('.dbtabContent').hide();
        $(dbTabContent).show();
        $(window).trigger('resize');
    });

    var hasTapValue = location.hash.split('&tab')[0];
   $('.dbtab li a[href="' + hasTapValue + '"]').trigger('click');

    $('body').on('click', '.surveychkall input', function() {

        var popInput = $('.ui-dialog  input[name="goupuserid"]');
        var popAddGrpBtn = $('.ui-dialog .addToGrpWrap .dropDownTarget');
        if ($(this).is(':checked') == true) {
            popInput.attr('checked', true);
            popAddGrpBtn.removeClass('disabled');
        } else {
            popInput.attr('checked', false);
            popAddGrpBtn.addClass('disabled');
        }
    });

    $('body').on('click', '.inviteuser-search', function() {

        var popInput = $('.ui-dialog  input[name="goupuserid"]');
        var popAddGrpBtn = $('.ui-dialog .addToGrpWrap .dropDownTarget');
        var numberOfChecked = $('input:checkbox:checked').length;

        if ($(this).is(':checked') == true) {
            popAddGrpBtn.removeClass('disabled');
        } else {

            if (numberOfChecked == 0) {
                popAddGrpBtn.addClass('disabled');
            }
        }
    });



if(isMobile==null){
    $dbTip = function() {
        var targets = $('[rel~=dbTip]'),
            target = false,
            tooltip = false,
            title = false;

        targets.bind('mouseenter', function() {
            target = $(this);
            var tip = target.attr('title');
            tooltip = $('<div id="dbTipWrapper" class="dbtipWrp"></div>');

            if (!tip || tip == '')
                return false;
            $('.dbtipWrp').remove();
            target.removeAttr('title');
            tooltip.css('opacity', 0)
                .html(tip)
                .appendTo('body');

            var init_tooltip = function() {
                if ($(window).width() < tooltip.outerWidth() * 1.5)
                    tooltip.css('max-width', $(window).width() / 2);
                else
                    tooltip.css('max-width', 340);

                var pos_left = target.offset().left + (target.outerWidth() / 2) - (tooltip.outerWidth() / 2),
                    pos_top = target.offset().top - tooltip.outerHeight() - 20;

                if (pos_left < 0) {
                    pos_left = target.offset().left + target.outerWidth() / 2 - 20;
                    tooltip.addClass('left');
                } else
                    tooltip.removeClass('left');

                if (pos_left + tooltip.outerWidth() > $(window).width()) {
                    pos_left = target.offset().left - tooltip.outerWidth() + target.outerWidth() / 2 + 20;
                    tooltip.addClass('right');
                } else
                    tooltip.removeClass('right');

                if (pos_top < 0) {
                    var pos_top = target.offset().top + target.outerHeight();

                    tooltip.addClass('top');
                } else
                    tooltip.removeClass('top');

                tooltip.css({
                        left: pos_left,
                        top: pos_top
                    })
                    .animate({
                        top: '+=10',
                        opacity: 1
                    }, 50);
            };

            init_tooltip();
            $(window).resize(init_tooltip);

            var remove_tooltip = function() {
                tooltip.animate({
                    top: '-=10',
                    opacity: 0
                }, 50, function() {
                    $(this).remove();
                });
                target.attr('title', tip);
            };

            target.bind('mouseleave', remove_tooltip);
            target.blur(remove_tooltip);
            tooltip.bind('click', remove_tooltip);
        });
    }
}else{
    $dbTip = function() {}; 
}
    $dbTip();




    if (!("ontouchstart" in document.documentElement)) {
        document.documentElement.className += " no-touch";
    }

});


function chartDatepicker() {
    var pickerClose = false;
    var pickerMaxClose = false;
    setTimeout(function() {
        $('input.highcharts-range-selector').datepicker({
            beforeShow: function(thisEl) {
                if ($(this).attr('name') != 'max') {
                    if (pickerClose == true)
                        $('#ui-datepicker-div').addClass('chartPickerMin');
                } else {
                    if (pickerMaxClose == true)
                        $('#ui-datepicker-div').addClass('chartPickerMax');
                }
            },
            onClose: function(thisEl) {
                if ($(this).attr('name') != 'max') {
                    pickerClose = true;
                    $('#ui-datepicker-div').removeClass('chartPickerMin');
                } else {
                    pickerMaxClose = true;
                    $('#ui-datepicker-div').removeClass('chartPickerMax');
                }
            }
        });

    }, 0);
}
var VideoDropzone;
var uploadVideo = false;
function videoUploader (from)
{
  uploadVideo = true;
  var videoUploader = '<div class="searchField">\
                         <label class="label">Video</label>\
                         <div class="fieldInput">\
                            <div class="uploadVideoRelatedWrp">\
                                <div class="videoCols"><span class="upLb"><i class="fa fa-upload"></i> Upload </span>\
                                <div id="uploadVideo" class="dropzone">\
                                     <div class="fallback">\
                                        <input type="file" name="videofile"  />\
                                     </div>\
                                  </div>\
                                </div>\
                                <div class="videoCols" id="chooseFromGallery"><i class="fa fa-youtube-play"></i> Choose from gallery</div>\
                            </div>\
                         </div>\
                      </div><div class="searchField VideoTitle">\
                         <label class="label">Video Title</label>\
                         <div class="fieldInput">\
                            <div id="preview-template"></div>\
                            <div class="twitterHas"><input id="VideoTitle" type="text" name="VideoTitle">\
                            <i class="postpopupIcon pstLinkIcon"></i></div>\
                         </div>\
                      </div>';
     
     if(from=='createpost')
      $('.uploadVideo').html(videoUploader);
     else
      $('.uploadBVideo').html(videoUploader);

      VideoDropzone = new Dropzone("#uploadVideo",
      {
        url: BASE_URL+'/admin/dashboard/videoupload',
        maxFiles: 1,
        addRemoveLinks: true,
        uploadMultiple:false,
        parallelUploads: 1,
        previewTemplate:'<div class="dz-preview dz-file-preview">\
                          <div class="dz-details" style="text-align: center;">\
                            <i class="fa fa-video-camera fa-5x"></i>\
                            <div class="dz-filename"><span data-dz-name></span></div>\
                            <div class="dz-size" data-dz-size></div>\
                            <img data-dz-thumbnail />\
                          </div>\
                          <div class="dz-progress" style="display:none"><span class="dz-upload" data-dz-uploadprogress></span></div>\
                          <div class="dz-success-mark"><span>✔</span></div>\
                          <div class="dz-error-mark"><span>✘</span></div>\
                          <div class="dz-error-message"><span data-dz-errormessage></span></div>\
                        </div>',
        autoProcessQueue:false,
        acceptedFiles: '.mp4,.mkv,.avi'
      });

   
     VideoDropzone.on("addedfile", function (file,response) {
        $('.videoChoosed').remove();
        $('#VideoTitle').attr('disabled',false);
        uploadVideo = true;
        if(cancelFileDropzone!=''){
            VideoDropzone.removeFile(cancelFileDropzone);
       }
      $('.uploadVideoTemp').addClass('uploadVideoSelected');
       cancelFileDropzone = file;
    });
 
     VideoDropzone.on("uploadprogress", function (file,response) {
       CloseLoader = false; 
       if(Math.round(file.upload.progress)<100){
        $dbLoader({process:true, totalUpload:Math.round(file.upload.progress), loaderHtml:'<i class="fa fa-spinner fa-spin fa-3x"></i><br> Uploading video <a href="javascript:void(0);" class="uploadCancel">Cancel</a>'});
       }
    });
      VideoDropzone.on("removedfile", function (file,response) {
         $('.uploadVideoTemp').removeClass('uploadVideoSelected');
         cancelFileDropzone = '';
    });
     
    VideoDropzone.on("success", function (file, response) 
    {
        if(from=='createpost' && response.status==true)
          $savePost(response);
        else if(response.status==true)
          $saveVideoPost(response);
    });
    nofoundVideo = false;
    $('#chooseFromGallery').click(function ()
    {
        $.ajax({
            url: BASE_URL+"/admin/knowledgecenter/videolist",
            type: "POST",
            dataType:"json",
            success:function(data)
            {
                list ='';
                $.each(data.listData, function( index, value ) 
                {
                    list += '<li><i class="fa fa-times videoCrossIcon" data-id="'+value.hashed_id+'" ></i><label id="videoChoose"><input type="radio" name="videoChoose" id="'+value.hashed_id+'" video_duration ="'+value.duration+'" /><i class="fa fa-play-circle-o fa-3x"></i><div class="titleVid oneline">'+value.name+'</div><img src="'+BASE_URL +'/timthumb.php?src=/adminraw/knowledge_center/video_'+clientID+'/'+value.thumbnail+'&amp;q=100&amp;w=130"></label></li>';
                });
                if(list==''){
                    list = '<li>No video found.</li>';
                    nofoundVideo = true;
                }
                $('#detailsDialog').remove();
                var htmlLightbox = '<div id="detailsDialog" class="chooseFromGallery"><ul>'+list+'</ul></div>';
                $('body').append(htmlLightbox);
                $("#detailsDialog").dialog({
                    title: 'Choose Video',
                    width:730,
                    open: function() 
                    {
                        $fluidDialog();
                    },
                    buttons: {
                        "Done": function() {
                            thisEl = $(this);
                            var pr = $('.chooseFromGallery input:checked').closest('li');
                            var videoThumb = $('img', pr).attr('src');
                            var file = $('input', pr).attr('id');
                            var video_duration = $('input', pr).attr('video_duration');
                            var videoTitle = $('.titleVid', pr).text();
                            var html = '<div class="searchField videoChoosed">\
                                            <label class="label"></label>\
                                            <div class="fieldInput">\
                                                <div class="acntImgThumb">\
                                                     <div id="newpic" class="proPic">\
                                                         <img src="'+videoThumb+'" class="imgStyle ">\
                                                         <a href="javascript:void(0);" rel="dbTip" title="" class="removeCompare">x</a>\
                                                     </div>\
                                                </div><input type="hidden" value="'+file+'" id="filename" name="filename"><input type="hidden" value="'+video_duration+'" id="eventend" name="eventend">\
                                                <div class="videoChoosedTitle">'+videoTitle+'</div>\
                                            </div>\
                                    </div>';
                               $('.videoChoosed').remove();
                               $('.uploadVideoTemp').addClass('selectedFromGallery');
                                $('.uploadVideoTemp').append(html); 
                              uploadVideo = false;
                              $('.videoChoosed .removeCompare').click(function(){
                                 $('.uploadVideoTemp').removeClass('selectedFromGallery');
                                 $('.videoChoosed').remove();
                              });
                              thisEl.dialog("close");
                        }
                    }
                });

                    if(nofoundVideo==true){
                        $('.ui-button-text-only').hide();
                    }
            }
        });
      
    });

}

$('document').ready(function() {

     $('body').on('click','.uploadCancel', function (e){
             e.preventDefault();
            cancelFileDropzone.xhr.abort();
            $('#mesageNotfiOverlay').remove();
    });

    $('body').on('click','.videoCrossIcon', function (e)
    {
        This = $(this);
        hashed_id = $(this).attr('data-id');
        
        var htmlLightbox = '<div id="detailsDialog" data-pop="delete"><div class="">Are you sure you want to delete?</div></div>';
        $('#detailsDialog').remove();
        $('body').append(htmlLightbox);
        $("#detailsDialog[data-pop='delete']").dialog({
            title: 'Please confirm',
            modal: true,
            draggable: false,
            buttons: {
                "Yes": function() 
                {
                    $.ajax({
                        url: BASE_URL + '/admin/dashboard/deletevideo',
                        type: 'POST',
                        data: {
                           'hashed_id':hashed_id,
                        },
                        async: false,
                        success: function(responce, textStatus, jqXHR) {
                            if (responce.status == 'success') 
                            {
                                $messageSuccess(responce.message);
                                This.closest('li').remove();
                            } else {
                                $messageError(responce.message);
                            }
                        }
                    });
                    $(this).dialog("close");
                }
            }

        });
    });

    $('body').on('click', '.LivePostSubmit', function() {
        //alert(uploadVideo);
        // video file upload
        if(uploadVideo==true)
            VideoDropzone.processQueue();
        else
            $savePost('');
        
        // end  video file upload

    });

    function conMentionTotext(text, mention){
     $('.trickCmntDiv').remove();
      text  = text.replace(/<br>/gi, '##br##');
        $('body').append('<textarea  class="trickCmntDiv" style="display:none">'+text+'</textarea>');        
        text = $('.trickCmntDiv').html();
        $('.trickCmntDiv').remove();
        text  = text.replace(/##br##/gi, '<br />');
        if(mention!=false){
            text =text.replace(/&lt;a href="##/gi, '<a href="');
            text=  text.replace(/class="mentionATag"&gt;&lt;strong&gt;&lt;span&gt;/gi, 'class="mentionATag"><strong><span>@');
            text = text.replace(/&lt;\/span&gt;&lt;\/strong&gt;&lt;\/a&gt;/gi, '</span></strong></a>');
            
        }
        return text;
 }

    
    $savePost = function(fileresult){
        var popCnt = "";
        var postText = $('#PostText').val();

        if ($.trim(postText) == '') {
            $messageError('Please enter post text');
            return false;
        }

        //var schdulelater = $('#postlater').attr('checked');
        var schdulelater = typeof($('#postlater').attr('checked')) != 'undefined' ? $('#postlater').attr('checked') : '';
        if (schdulelater == 'checked') 
        {
            var datesch = $('#scheduledate').val();
            var dateddsch = $('#posttimings').val();

            if (dateddsch == 0) {
                $messageError('Please enter schedule hours for later post');
                return false;

            } else if (dateddsch == 99 && datesch == '') {
                    $messageError('Please enter schedule date for later post');
                    return false;
            }
        }
        var c = '';
        var len = $('.tagit-hidden-field').not('#twitter-tag-text').length;
        $(".tagit-hidden-field").not('#twitter-tag-text').each(function(index, element) {
            if (index == len - 1) {
                c += $(this).val();
            } else {
                c += $(this).val() + ' ';
            }

        });
        $('#tagitent').val(c);
        //return true;
        postText =  conMentionTotext(postText);
        thisFormValue = $('#postForm').serializeArray();
        var TaggedUsers='';

        $('textarea.mention').mentionsInput('getMentions', function(data) {     
        var jsons = JSON.stringify(data);
       
          var i=0;
        //
          $.each($.parseJSON(jsons), function(idx, obj) {
                
               if(i==0)
                {
                 TaggedUsers+=obj.id;
                }
                else
                {
                 TaggedUsers+=','+obj.id;   
                }

                i++;
          });

        
        }); 

        if(TaggedUsers!="")
        {           
           TaggedUsers = {name:'TaggedUsers', value:TaggedUsers};
            thisFormValue.push(TaggedUsers);

            var gtag = $('#PostText').prev('.mentions');
            var text = gtag.find('div:eq(0)').html();
             //text = text.replace(/&/g, '%26');
            text     = text.replace('  ', ' ');
            postText = conMentionTotext(text);
        }


         

        
        var postV = {name:'text', value:postText};
        thisFormValue.push(postV);

        if(fileresult.file!='' && uploadVideo == true)
        {
            fileData ={name:'filename',value:fileresult.file};
            fileData1 ={name:'video_duration',value:fileresult.eventend};
            thisFormValue.push(fileData);
            thisFormValue.push(fileData1);
            
            var VideoTitle = $('#VideoTitle').val();
            if (VideoTitle == '') {
                $messageError('Please enter video title');
                return false;
            }
        }
         $.ajax({
            url: BASE_URL + '/admin/dashboard/postsubmit',
            type: "POST",
            dataType: 'json',
            data: thisFormValue,
            success: function(data) 
            {
                if (data.content != "" && data.content == 1) 
                {
                    
                    $messageSuccess('Post added sucessfully');
                    if (schdulelater == '') 
                    { 
                        socket.emit('checkdbee', true,clientID); 
                        socket.emit('chkactivitynotification', true,clientID);
                    }
                    
                    resestCreatepost(true);
                    location.reload(true);
                }
            }
        });
      }

    $('body').on('click', '#successMsg, #errorMsg', function() {
        $(this).slideUp(100);
    });

    


    // atteched file section start here
    $('body').on('click', '.Attachfile', function(e) {

        popCnt = $('#newWrapper').detach();

        $.ajax({
            url: BASE_URL + '/admin/dashboard/getfolderslist',
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                $dbLoader('#sowattachment', 1, '', 'center');

            },
            success: function(data) {

                $('#allAttachedFiles').show();
                $('.attachedFilesClose').remove();
                $('#allAttachedFiles').html(data.content).before('<a href="javascript:void(0);" class="fa-stack closeRowField attachedFilesClose" >\
                                                <i class="fa fa-circle fa-stack-2x"></i>\
                                                <i class="fa fa-times fa-stack-1x fa-inverse"></i>\
                                              </a>');

                $('#allAttachedFiles').html(data.content).after('<input type="hidden" name="attachmentlistforsave" id="attachmentlistforsave" value=""><div class="postTypeFooter attachedFilesFooter">\
                       <a ref="#"></a><a  href="javascript:void(0)"></a>\
                    <div id="signuploader" style="display:none; float:left; padding-top:5px; padding-right:10px;">\
                       <input id="db-post-cat" type="hidden" value="10">\
                       <div class="clearfix"></div>\
                       ');

                $.each(nameAttach, function(i, value) {
                    $('#allAttachedFiles input[type="checkbox"][attname="' + value + '"]').attr('checked', true);
                });

            }


        });
    });

    $('body').on('click', '.attachedFilesClose', function(e) {
        e.preventDefault();
        $('#allAttachedFiles').hide();
        $('.attachedFilesClose').remove();

    });

    $('body').on('click', '#attachedFiles', function(e) {
        e.preventDefault();

        $('#allAttachedFiles').before(popCnt)
        $("#newWrapper").children().unwrap();
        $('#allAttachedFiles').css('display', 'none');

        var listAttachmentFiles = '';
        $.each(nameAttach, function(i, value) {
            listAttachmentFiles += '<li  attname="' + value + '"><i class="fa fa-file-pdf-o"></i> ' + value + ' <a href="javascript:void(0);" class=" pull-right closeFilesFromAttached pull-left"><i class="fa fa-times-circle fa-lg"></i></a></li>';
        });

        $('#allAttachedFiles').html('<ul>' + listAttachmentFiles + '</ul>');

    });
    $('body').on('click', '.attachrow li a', function(e) {
        e.preventDefault();
        $('.postfilelist').css('display', 'none');

        $(this).next('.postfilelist').slideToggle('fast');
         $.dbeePopup('resize');

    });


    $('body').on('click', '#allAttachedFiles li', function() {
        var removename = $(this).attr('attname');
        var listAttachmentFiles = '';
        var i = nameAttach.indexOf(removename);
        if (i != -1) {
            nameAttach.splice(i, 1);
        }
        $(this).remove();

    });


    // atteched file section
    // play youtube video event start from here.
    $('body').on('click', '.youVideoPost:not(.picMediaComboWrp .youTubeVideoPost, .dbcomment-speechwrapper .youTubeVideoPost, .specialDbNotPlay .youTubeVideoPost)', function(e) {
        e.preventDefault();
        var videoID = $('img', this).attr('video-id'); 
        if (videoID != undefined) {
            var playVideoHTml = '<video width="400" controls autoplay>\
                                  <source src="'+BASE_URL+'/adminraw/knowledge_center/video_'+clientID+'/'+videoID+'.mp4" type="video/mp4">\
                                  Your browser does not support HTML5 video.\
                                </video>';
            $(this).html(playVideoHTml);
        }
    });

    $('body').on('click', '.youTubeVideoPost:not(.picMediaComboWrp .youTubeVideoPost, .dbcomment-speechwrapper .youTubeVideoPost, .specialDbNotPlay .youTubeVideoPost)', function(e) {
        e.preventDefault();
        var videoID = $('img', this).attr('video-id');
        if (videoID != 'undefined' || videoID != '') {
            var playVideoHTml = '<iframe width="186" allowtransparency="true" style="background: #000;" height="139" src="//www.youtube.com/embed/' + videoID + '?rel=0&autoplay=1" frameborder="0" allowfullscreen></iframe>';
            $(this).html(playVideoHTml);
        }
    });

    $('body').on('click', '.eventdbpost,.crtPostevent', function(e) {
        e.preventDefault();
        var eventid = '';
        if($(this).hasClass('disabled'))
        return false;
      
        eventid = $(this).attr('data-id');
        $("#surveyDialog1").remove();
        var html = '<div id="surveyDialog1"> </div>';
        $('body').append(html);

        $("#surveyDialog1").dialog({
            dialogClass: 'surveyDialog',
            width: 950,
            height: 620,
            close:function(){
                $("#surveyDialog1").remove();
            },
            open: function() {

                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    data: {
                        eventid: eventid
                    },
                    url: BASE_URL + '/admin/event/showdbpostform',
                    success: function(response) {

                        $('#surveyDialog1').html(response.content);
                        $('#eventfield').show();
                        $('.addConditionWrapper').addClass('eventCrtPost');
                        $('.addConditionWrapper .postBntsRow a.addonpost').remove();
                        $('.eventCrtPost .schPostRow').remove();
                        $('.eventCrtPost .resetform').remove();

                        $("#DbTag").tagit();


                        $('.categoryList input:not([cat-name="Miscellaneous"])').attr('checked', false);

                        $('.categoryList input').click(function() {
                            var totalchecked = $(this).closest('.categoryList').find('input:checked').size();
                            var miscellaneous = $('.categoryList input[cat-name="Miscellaneous"]');
                            if ($(this).attr('cat-name') != 'Miscellaneous') {
                                if (totalchecked >= 1) {
                                    miscellaneous.attr('checked', false);
                                } else {
                                    miscellaneous.attr('checked', true);
                                }
                            } else {
                                if (totalchecked == 0) {
                                    miscellaneous.attr('checked', true);
                                }
                            }

                        });

                    }

                });
            },
            buttons: {              
                Submit: function() {
                    $('#LivePostSubmit').click();
                }
            }
        });

    });


    $('.categoryList input:not([cat-name="Miscellaneous"])').attr('checked', false);

    $('.categoryList input').click(function() {
        var totalchecked = $(this).closest('.categoryList').find('input:checked').size();
        var miscellaneous = $('.categoryList input[cat-name="Miscellaneous"]');
        if ($(this).attr('cat-name') != 'Miscellaneous') {
            if (totalchecked >= 1) {
                miscellaneous.attr('checked', false);
            } else {
                miscellaneous.attr('checked', true);
            }
        } else {
            if (totalchecked == 0) {
                miscellaneous.attr('checked', true);
            }
        }

    });

   $("#uploadDropzoneImg").dropzone({
        url: BASE_URL + '/admin/dashboard/imguplod',
        maxFiles: 1,
        addRemoveLinks: true,
        uploadMultiple: true,
        parallelUploads: 1,
        acceptedFiles: '.png, .jpg, .jpeg, .JPG,.gif',
        error: function(file, serverFileName) {
            $messageError(serverFileName);
        },
        processing: function(file, serverFileName) {
            //alert(serverFileName)           
            $('.addPostImg').remove();
            $('#addTextWrp').before('<div class="addPostImg" style="height:100px;" ></div>');
            $dbLoader({
                element: '.addPostImg'
            });
        },
        success: function(file, serverFileName) {
            $('#PostPix_').val(serverFileName.img);
            var fileList = "serverFileName = " + serverFileName.img;
            //$('#dbimg').append(serverFileName.fileLinkText);
            var ImgHtml = '<a href="javascript:void(0)" id="closedbImage" class="fa-stack removeCircle" style="left:160px;">\
                                               <i class="fa fa-circle fa-stack-2x"></i>\
                                               <i class="fa fa-times fa-stack-1x fa-inverse"></i>\
                                           </a>';
            $('.addPostImg').css({
                background: 'url(' + IMGPATH + '/imageposts/medium/' + serverFileName.img + ') no-repeat scroll'
            }).html(ImgHtml);

            $('#closedbImage').click(function(event) {
                $('.addPostImg').remove();
                $.ajax({
                    url: BASE_URL + "/admin/dashboard/imgunlink",
                    type: "POST",
                    data: fileList,
                    success: function() {
                        $('#PostPix_').val('');
                    }
                });
            });

            this.removeFile(file);
        }
    });

    $("#DbTag").tagit();

    // for twitter tag
        $("#twitter-tag-text").tagit({
            placeholderText: '#tag - Twitter (max 4)',
            tagLimit: 4,
            beforeTagAdded: function(event, ui) {                 
            if ($('.twitterHastag:not(.twitterHasmention) .tagit li') >= 5) return false;
             
            },            
            afterTagAdded: function(event, ui) {                
             $(".twitterHastag:not(.twitterHasmention) .tagit-new input").val('').attr('placeholder', '');
             
            },
            onTagLimitExceeded: function() {
                $(".twitterHastag:not(.twitterHasmention) ul.tagit").next('.formErrorPop').remove();
                $(".twitterHastag:not(.twitterHasmention) .tagit-new input").val('').attr('placeholder', '');
                /*$(".twitterHastag:not(.twitterHasmention) ul.tagit").after('<div class="formErrorPop">Sorry tag limit over</div>');*/
                $("#dbeePopupWrapper").append('<div class="twitterErrorMsg"><span class="twtrMsgTxt">Sorry, you can only add upto 4 tags</span></div>');

                setTimeout(function() {
                    /*$(".twitterHastag:not(.twitterHasmention) ul.tagit").next('.formErrorPop').remove();*/
                      $("#dbeePopupWrapper .twitterErrorMsg").remove();
                }, 5000);

            },
            onTagRemoved: function() {                
                $(".twitterHastag:not(.twitterHasmention) ul.tagit").next('.formErrorPop').remove();
            }, 
            afterTagRemoved: function() {
                          
              if($('.twitterHastag:not(.twitterHasmention).tagit li').length==1)
                {
                 $(".twitterHastag:not(.twitterHasmention).tagit-new input").val('').attr('placeholder', '#tag - Twitter (max 4)');
                }
            }
        });

        $("#twitter-tag-text").focusout(function(event) {
            var e = jQuery.Event("keydown");
            e.which = 13;
            $(".#twitter-tag-text input").trigger(e);
        });
        // end for twitter tag

    

    $('body').on('focusin', '.tagit input', function(event) {
        $(this).closest('.tagit').addClass('activeFocus');
    });
    $('body').on('focusout', '.tagit input', function(event) {
        $(this).closest('.tagit').removeClass('activeFocus');
    });



    $('body').on('click', '.postBntsRow a[data-type]', function() {

        var data = $(this).attr('data-type');
        if (data == 'postinleague') {
            $('#leagfield').show();
            $('#InsertInLeg').val('1');
            $(this).addClass('active');
        } else if (data == 'postingrouup') {
            $('#groupfield').show();
            $('#InsertInGrp').val('1');
            $(this).addClass('active');
        } else if (data == 'postinevent') {
            $('#eventfield').show();
            $('#InsertInGrp').val('1');
            $(this).addClass('active');
        }
        else if (data == 'postvideo') {
            
            if($(this).hasClass('active')==true){
                $(this).removeClass('active');
                $('#DataNotInPoll .uploadVideo').html('');
            }else{
                 $(this).addClass('active');
                 videoUploader('createpost');
            }
        }

    });


    $('body').on('change', '#selectGroupList', function() {
        var thisVal = $(this).val();
        var grouptype = $(this).find('option[value="' + thisVal + '"]').attr('grouptype');
        $('#groupty').val(grouptype);
    });

    $('body').on('click', '#closegrp', function() {
        $('#groupfield').hide();
        $('#InsertInGrp').val('');
        $('.postBntsRow a[data-type="postingrouup"]').removeClass('active');
    });

    $('body').on('click', '#closeevent', function() {
        $('#eventfield').hide();
        $('.postBntsRow a[data-type="postinevent"]').removeClass('active');
    });

    $('body').on('click', '#closeleg', function() {

        $('#leagfield').hide();
        $('#InsertInLeg').val('');
        $('.postBntsRow a[data-type="postinleague"]').removeClass('active');
    });

    $.mentionInt = function (){
        $('#addTextWrp textarea').mentionsInput({
            onDataRequest: function(mode, query, callback) {                
              var data = jQuery.parseJSON($('#mentionpostuser').val());

              responseDataMy = _.filter(data, function(item) {
                  return item.name.toLowerCase().indexOf(query.toLowerCase()) > -1
              });
                                
              callback.call(this, responseDataMy);
            }
        });
    }
    $('body').on('click', '.pollBtnForPopup', function() {
        $('.postBntsRow a[data-type]').removeClass('active');
        if ($('.pollBtnForPopup').attr('data-type') == "") {

            $('#DataNotInPoll, .picUplaoderPopup, #groupfield, #leagfield, #eventfield').hide();
            $('#DataInPoll').show();
            $('.backbutPop').show();
            $('.backbutPop').addClass('active');
            $('#PostText').attr('name', 'polltext');
            $('#dbeetype').val('Polls');
            $('#InsertInLeg').val('');
            $('#InsertInGrp').val('');
            $('.pollBtnForPopup').attr('data-type', 1);
            $(this).addClass('active');
            $('.addPostImg,.makelinkWrp, #addTextWrp .mentions-input-box, #addTextWrp .fieldInput #PostText').remove();
            $('#LinkDesc1').removeAttr('style');
            $('#PrivatePost').closest('.select').hide();
            $('#postinuserset').hide();
            $('#addTextWrp .fieldInput').prepend('<textarea name="polltext" id="PostText"></textarea>');

        } else if ($('.pollBtnForPopup').attr('data-type') == 1) {
            $('#DataNotInPoll, .picUplaoderPopup').show();
            $('#DataInPoll, #groupfield, #leagfield').hide();
            $('.backbutPop').hide();
            $('#PostText').attr('name', 'text');
            $('#dbeetype').val('Text');
            $('.pollBtnForPopup').attr('data-type', '');
            $(this).removeClass('active');
            $.mentionInt();
        }
        $('#postForm')[0].reset();
    });



    $('body').on('blur', '#PostLink', function(e) 
    {
        e.preventDefault();
        $(this).parents('.singleRow').removeClass('singleRow');
        var link = $.trim($(this).closest('div').find('input').val());
        if (link == '')
            return false;
        var dbeeurl = $('#PostLink').val();
        var html = '<div id="LinkDesc1"></div>';
        $('#LinkDesc1').remove();
        $(this).after(html);
        $linkCaptureData(dbeeurl, '#LinkDesc1', '#PostLink');
    });
    
   function resestCreatepost (open)
    {
        $('.postLaterclass').hide();
        if($('#postForm').is(':visible')==true){
            $('#postForm')[0].reset();
        }
        $('.uploadVideo').html('');
        $('#videotype').removeAttr('disabled');
        $('.select').removeClass('disabled');
        
        $('.uploadBVideo').html('');
        $('#videotype option[value="0"]').attr("selected", "selected");
        $select('select');

        $('#select_type').show();
        $('.attachedFilesClose').trigger('click');
        $('input[cat-name="Miscellaneous"]').attr('checked', true);
        $('#DbTag').tagit('removeAll');
        if ($('.pollBtnForPopup').hasClass('active') == true) {
            $('.pollBtnForPopup').trigger('click');

        } else {
            $('#LinkDesc1').removeAttr('style');
            $('.addPostImg,.makelinkWrp').remove();
            $('#groupfield, #leagfield').hide();
            $('.postBntsRow a').removeClass('active');
        }

        if(open==true)  $('#openCreateBtn').removeClass('openSearchBlock');
    }
    $('body').on('click', '.openSearchBlock', function(event) {
       resestCreatepost();
    });

    // this is function use for get link data 

    $linkCaptureData = function(dataUrl, containerID, inputID) {

            var dataUrl = $.trim(dataUrl);

            if (dataUrl == '') {
                return false;
            }
            $(containerID).html('').css({minHeight:100, position:'relative'});
            $dbLoader({
                element: containerID
            });
            var closeHTML = '<div id="closeLinkUrl" class="removeCircle">\
                                <span class="fa-stack">\
                                  <i class="fa fa-circle fa-stack-2x"></i>\
                                  <i class="fa fa-times fa-stack-1x fa-inverse"></i>\
                                </span>\
                            </div>';
            $(containerID).append(closeHTML);

            $(containerID + ' #dbLoaderContainer').css('height', '100px')
            if (dataUrl.indexOf("http") != '-1') {
                var murl = dataUrl;
            } else {
                var murl = 'http://' + dataUrl;
            }

            var videoUrl = murl;
            var p = /^(?:https?:\/\/)?(?:www\.)?youtube\.com\/watch\?(?=.*v=((\w|-){11}))(?:\S+)?$/;
            var matches = (videoUrl.match(p)) ? RegExp.$1 : 10;


            //$('#attachlinkloader').show();
            var url = BASE_URL + "/admin/dashboard/linkdetail";
            var data = "dbeeurl=" + murl;
            $('#attchedLinked').show();
            if ($(containerID).closest('.minPostTopBar').is(':visible') == true) $('#dbee_comment').focus();

            if (matches == 10) {
               var abortAjax = $.ajax({
                    async:true,
                    url: url,
                    type: 'POST',
                    data: data,
                    success: function(data) {
                        $(containerID).removeClass('loader');
                        if ($(containerID).closest('.minPostTopBar').is(':visible') == true) $('#dbee_comment').focus();
                        //$dbLoader(containerID, 1, 'close');
                        var GetResult = data;
                        if (GetResult != '0' && GetResult != '-1' && GetResult != '' && GetResult != '-2') {
                            $('#attachlinkerror').hide();
                            $(containerID).html(GetResult);


                        } else {

                            if (GetResult == '0' || GetResult == '') {
                                $('#attachlinkerror-text').html('The website addresss was not found.');
                                $('#attachlinkerror').show();
                            } else if (GetResult == '-1') {
                                $('#attachlinkerror-text').html('To post a video click on \'post vidz\'');
                                $('#attachlinkerror').show();;
                            } else if (GetResult == '-2') {
                                $('#attachlinkerror-text').html('Restrictd url please user other');
                                $('#attachlinkerror').show();;
                            }
                        }



                    }

                });
            } else {
                 var URL = "https://www.googleapis.com/youtube/v3/videos?id="+matches+"&key=AIzaSyDSDjQKQ78A7hgpq35ozno7K4Pf4OhVUEk&part=snippet";
                $.ajax({
                    type: "GET",
                    url: URL,
                    cache: false,
                    dataType: 'jsonp',
                    success: function(data) {
                        var title = data.items[0].snippet.title;
                        var description = data.items[0].snippet.description;
                        $(containerID).removeClass('loader');
                        $(containerID).append('<input name="Vid" id="Vid" type="hidden" value="">\
                <input name="VidDesc" id="VidDesc" type="hidden" value="">\
                <input name="VidSite" id="VidSite" type="hidden" value="">\
                <input name="VidID" id="VidID" type="hidden" value="">\
                <input name="VidTitle" id="VidTitle" type="hidden" value="">');

                        if ($(containerID).closest('.minPostTopBar').is(':visible') == true) $('#dbee_comment').focus();
                        var htmlVid = '<div class="makelinkWrp">\
                          <div class="removeCircle" id="closeLinkUrl">\
                            <span class="fa-stack">\
                              <i class="fa fa-circle fa-stack-2x"></i>\
                              <i class="fa fa-times fa-stack-1x fa-inverse"></i>\
                            </span>\
                          </div>\
                      <input type="hidden"  value="' + title + '" id="youtubetitle" name="VidTitle"/>\
                      <input type="hidden"  value="' + description + '" id="youtubedescription" name="VidDesc"/>\
                      <input type="hidden"  value="youtube" id="VidSite" name="VidSite"/>\
                      <input type="hidden"  value="' + matches + '" id="VidID" name="VidID"/>\
                      <input type="hidden"  value="' + videoUrl + '" id="Vid" name="Vid"/>\
                      <img border="0" src="http://img.youtube.com/vi/' + matches + '/default.jpg">\
                      <div class="makelinkDes">\
                        <h2>' + title.substring(0, 80) + '...</h2>\
                        <div class="desc">' + description.substring(0, 100) + '...</div>\
                        <div class="makelinkshw"><a target="_blank" href="">' + videoUrl + '</a></div>\
                      </div>\
                    </div>';
                        $(containerID).html(htmlVid);


                        if ($('#dbeePopupWrapper').is(':visible') == true) {
                            setTimeout(function() {
                                $.dbeePopup('resize');
                            }, 100);
                        }
                    }

                });


            }

         /*   $('#closeLinkUrl').ajaxStop(function(){
                    alert("All AJAX requests completed");
            });*/

            $('body').on('click', '#closeLinkUrl', function(event) {
                abortAjax.abort();


                $(inputID).val('');
                $(containerID).removeAttr('style').html('');
                var cmntBx = $(this).closest('.dbpostWrp').find('.cmntLinkDetailsWrp');
                if (cmntBx.is(':visible') == true) {
                    cmntBx.remove();
                    $('.cmntLinkBtn').removeClass('active');
                }
                $('#replytype').val('text');
                $(this).closest('.makelinkWrp').remove();
                $('#attchedLinked').hide();


            });
        }
        // this is function use for get link data end

});







$(document).ready(function() {

    $('body').on('click', '.closeNofifyBtn', function() {
        $('.notifyDataBox .autoScroll').slideToggle('fast');
        $(this).toggleClass('fa-angle-down');
    });
    $(document).ajaxSuccess(function(event, xhr, settings) {
        // $("body").getNiceScroll().resize();
        $dbTip();
    });
});

var htmlCheckAll = '<div class="checkAllUsers"><label class="checkAllUser">\
          <input type="checkbox" value="" class="checkAllUser" name="checkAllUser" id="checkAllUser">\
          <label for="checkAllUser"></label>Select all\
        </label>\
      <div class="pull-left" id="totalCheckUserPop">\
        Total Selected: <span>0</span>\
      </div></div>';


function facebookUser(id, type) 
{
    if (type == 'expert')
        title = 'Invite guest speaker from Facebook to';
    else
        title = 'Invite Facebook friends to';

    $('#detailsDialog').remove();
    var htmlLightbox = '<div id="detailsDialog" title="Facebook Friends">\
      <div class="srcUsrWrapper">\
      <input type="hidden" name="DbeeID" id="DbeeID" value="' + id + '" />\
      <div class="sprite searchIcon2"></div>\
      <input type="text" id="userFatchList" class="userFatchList" onkeyup="javascript:filtersocailuser()"\ socialFriendlist="true"  >\
      <div class="srcUsrtotal" style="display:none;"><i>total</i></div> \
      </div><div id="datacollect" style="float:none"></div>\
      <div id="userInfoContainer"></div></div>';
    $('body').append(htmlLightbox);
    $('#detailsDialog').hide();
    $.ajax({
        type: "POST",
        dataType: 'json',
        url: BASE_URL + "/admin/social/facebookfriends",
        data: {
            "type": type,
            'DbeeID': id
        },
        success: function(result) {
            if(result.status=='success')
            {
                $("#detailsDialog").dialog({
                    dialogClass: 'detailsDialogBox',
                    width: 800,
                    height: 500,
                    title: title + ' <span style="color:#FF8C03;">' + result.post_title + '</span>',
                    open: function() 
                    {
                        $fluidDialog();
                        $("#datacollect").html('');
                        $('.ui-dialog-content').append('<div class="loaderOverlay2"></div>');
                        $('#userInfoContainer').html(result.return);
                        $('.loaderOverlay2').remove();
                        $('.checkAllUsers').remove();
                        $('.ui-dialog-buttonset').before(htmlCheckAll);
                        $('#userInfoContainer .follower-box .usImg img').imageLoader();
                        $('#detailsDialog').show();
                    },
                    buttons: {
                        "Invite": function() {

                            var facebookUserid = $('#facebookUserid').val();
                            var facebookUsername = $('#facebookUsername').val();
                            var userInfo = [];
                            var thisEl = $(this);
                            $('input:checkbox[name=FacebookInvite]').each(function() {
                                if ($(this).is(':checked'))
                                    userInfo.push($(this).val());
                            });
                            var stringuserInfo = userInfo.join();

                            if(userInfo.length==0){
                                $messageError('Please select a user');
                                return false
                            }
                            $messageSuccess("Invitation sent successfully");
                            thisEl.dialog("close");

                            $.ajax({
                                type: "POST",
                                dataType: 'json',
                                data: {
                                    'stringuserInfo': stringuserInfo,
                                    'dbeeID': id,
                                    'type': type,
                                    'facebookUserid': facebookUserid,
                                    'facebookUsername': facebookUsername
                                },
                                url: BASE_URL + '/admin/social/facebookinvite',
                                success: function(response) {
                                    $messageSuccess("Invitation sent successfully");
                                    thisEl.dialog("close");
                                },
                                error: function(error) {}

                            });

                        }
                    }
                });
            }
        }
    });
}

function filtersocailuser() {
    var sorting = $("input[socialFriendlist='true']").val();
    var id = $("input[socialFriendlist='true']").attr('id');
    $('input.checkAllUser').attr('checked', false);
    var count = 0;
    $("." + id).each(function(index) {

        if ($(this).attr('title')) {
            if ($(this).attr('title').match(new RegExp(sorting, "i"))) {
                $(this).show("slow");
                count++;
            } else {
                $(this).hide("fast");
            }
        }
    });

    $('.srcUsrtotal').show();
    $(".srcUsrtotal").html(count + ' <i>total</i>');
}


function twitterUser(DbeeID, type) {
    if (type == 'expert')
        title = 'Invite guest speaker from Twitter to';
    else
        title = 'Invite Twitter followers to';

    $('#detailsDialog').remove();
    var htmlLightbox = '<div id="detailsDialog"  title="Twitter Friends">\
                <div class="srcUsrWrapper">\
                    <input type="hidden" name="DbeeID" id="DbeeID" value="' + DbeeID + '" />\
                    <div class="sprite searchIcon2"></div>\
                    <input type="text" id="userFatchList" class="userFatchList" onkeyup="javascript:filtersocailuser()"\ socialFriendlist="true" >\
                  <div class="srcUsrtotal" style="display:none;">43 <i>total</i></div>  \
                </div><div id="datacollect" style="float:none"></div>\
                <div id="userInfoContainer"></div></div>';

    $('body').append(htmlLightbox);
    $("#detailsDialog").dialog({
        dialogClass: 'detailsDialogBox',
        width: 800,
        title: 'Invite Twitter followers',
        height: 500,
        open: function() {
            $fluidDialog();
            $("#datacollect").html('');
            $('.ui-dialog-content').append('<div class="loaderOverlay2"></div>');

            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {
                    'DbeeID': DbeeID,
                    'type': type
                },
                url: BASE_URL + '/admin/social/twitteruser',
                success: function(response) {
                    $('.ui-dialog-title').html(title + ' <span style="color:#FF8C03;">' + response.post_title + '</span>');
                    $('#userInfoContainer').html(response.content);
                    $('#userInfoContainer .follower-box .usImg img').imageLoader();
                    $('.loaderOverlay2').remove();
                    $('.ui-dialog-buttonset').before(htmlCheckAll);
                }
            });

        },
        buttons: {
            "Invite": function() {

                //var dbeeID = $('#DbeeID').val();
                thisEl = $(this);
                var userInfo = [];
                $('input:checkbox[name=twitter]').each(function() {
                    if ($(this).is(':checked'))
                        userInfo.push($(this).val());
                });
                var stringuserInfo = userInfo.join();

                if(userInfo.length==0){
                    $messageError('Please select a user');
                    return false
                }
                $messageSuccess("Invitation sent successfully");
                thisEl.dialog("close");
               
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    data: {
                        'stringuserInfo': stringuserInfo,
                        'dbeeID': DbeeID,
                        'type': type
                    },
                    url: BASE_URL + '/admin/social/twitterinvitation',
                    
                    success: function(response) {
                        $messageSuccess('Invitation sent successfully');
                        thisEl.dialog("close");
                    }
                });
            }
        }
    });
}


function linkedinUser(DbeeID, type) {
    if (type == 'expert')
        title = 'Invite guest speaker from LinkedIn to';
    else
        title = 'Invite LinkedIn connections to';

    $('#detailsDialog').remove();
    var htmlLightbox = '<div id="detailsDialog"  title="Linkedin Friends">\
                <div class="srcUsrWrapper">\
                  <input type="hidden" name="DbeeID" id="DbeeID" value="' + DbeeID + '" />\
                    <div class="sprite searchIcon2"></div>\
                    <input type="text" id="userFatchList" class="userFatchList" onkeyup="javascript:filtersocailuser()"\ socialFriendlist="true"  >\
                  <div class="srcUsrtotal" style="display:none;">43 <i>total</i></div>  \
                </div>\
                                <div id="datacollect" style="float:none"></div>\
                                <div id="userInfoContainer"></div>\
                            </div>';

    $('body').append(htmlLightbox);
    $("#detailsDialog").dialog({
        dialogClass: 'detailsDialogBox',
        width: 800,
        height: 500,
        title: 'Invite LinkedIn connections',
        open: function() {
            $fluidDialog();
            $("#datacollect").html('');
            $('.ui-dialog-content').append('<div class="loaderOverlay2"></div>');

            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {
                    'DbeeID': DbeeID,
                    'type': type
                },
                url: BASE_URL + '/admin/social/linkedinfriends',
                success: function(response) {
                    $('.ui-dialog-title').html(title + ' <span style="color:#FF8C03;">' + response.post_title + '</span>');
                    $('.loaderOverlay2').remove();
                    $('#userInfoContainer').html(response.content);
                    $('#userInfoContainer .follower-box .usImg img').imageLoader();
                    $('.ui-dialog-buttonset').before(htmlCheckAll);
                }
            });
        },
        buttons: {
            "Invite": function() {
                //var dbeeID = $('#DbeeID').val();

                thisEl = $(this);

                var userInfo = [];
                $('input:checkbox[name=linkedin]').each(function() {
                    if ($(this).is(':checked'))
                        userInfo.push($(this).val());
                });

                var stringuserInfo = userInfo.join();

                if(userInfo.length==0){
                    $messageError('Please select a user');
                    return false
                }
                $messageSuccess("Invitation sent successfully");
                thisEl.dialog("close");
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    data: {
                        'stringuserInfo': stringuserInfo,
                        'dbeeID': DbeeID,
                        'type': type
                    },
                    url: BASE_URL + '/admin/social/sendmessagetolinkedin',
                    success: function(response) {
                        $messageSuccess('Invitation sent successfully');
                        thisEl.dialog("close");
                    }
                });
            }
        }
    });
}


function dbeeUser(DbeeID, posttitle, type) {
    $('#detailsDialog').remove();
    var htmlLightbox = '<div id="detailsDialog"  title="Platform users">\
                <div class="srcUsrWrapper">\
                  <input type="hidden" name="DbeeID" id="DbeeID" value="' + DbeeID + '" />\
                  <div class="sprite searchIcon2"></div>\
                    <input type="text" id="invietuserdbee" class="userFatchList" socialFriendlist="true"  placeholder="enter user name">\
                  <div class="srcUsrtotal" style="display:none;">40 <i>total</i></div>\
                </div>\
                                <div id="datacollect" style="float:none"></div>\
                                <div id="userInfoContainer"></div>\
              </div>';

    $('body').append(htmlLightbox);

    $("#detailsDialog").dialog({
        dialogClass: 'detailsDialogBox',
        width: 800,
        height: 500,
        title: 'Invite Platform users',
        open: function() {
            $fluidDialog();
            $("#datacollect").html('');

            $('.ui-dialog-title').html('Invite platform users to <span style="color:#FF8C03;">' + posttitle + '</span>');
            //$('#userInfoContainer').html('Plese select user');
        },
        buttons: {
            "Invite": function() {
                thisEl = $(this);
                var userInfo = [];
                $('input:checkbox[name=groupuser]').each(function() {
                    if ($(this).is(':checked'))
                        userInfo.push($(this).val());
                });
                var stringuserInfo = userInfo.join();
                if(userInfo.length==0){
                    $messageError('Please select a user');
                    return false
                }
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    data: {
                        'stringuserInfo': stringuserInfo,
                        'dbeeID': DbeeID,
                        'type': type
                    },
                    url: BASE_URL + '/admin/social/invitedbeeuser',
                    success: function(response) {
                        $messageSuccess(response.message);
                        thisEl.dialog("close");
                        if(localTick == false){
                            socket.emit('chkactivitynotification', true,clientID);
                        } 

                    }
                });
            }
        }
    });
}

$(document).ready(function() {
    /*$('body').on('click', '#spcialDBbody ul.dbtab li a', function() {
        $('#spcialDBbody .listStyle1').hide();
        $('#' + this.rel).fadeIn(100);
    });*/


    $('body').on('click', '.socConnTab a', function() {
        $('.socConntList').hide();
        $('#' + this.rel).fadeIn(300);
    });

    $('body').on('click','.adTips a', function(){
           //adTopbnr ='<img src="'+BASE_URL+'/img/top-bnner.png"/>';
           //adRtbnr ='<img src="'+BASE_URL+'/img/right-bnner.png"/>';
           var dataId = $(this).attr('data-id');           
           var thisObj = $(this);
           if(dataId=='topBnrAd')
           {
             //$('body').append('<div id="dialogConfirmSetting">'+adTopbnr+'</div>');
             $( "#dialogTopBnr" ).dialog({
                resizable: false,
                title:'Top banner',
                modal: true,            
              });
             } 
           else if(dataId=='rtBnrAd')
           {
             //$('body').append('<div id="dialogConfirmSetting">'+adRtbnr+'</div>');
             $( "#dialogRtBnr" ).dialog({
                resizable: false,
                title:'Right banner',
                modal: true,            
              });
           }         
    });

  


   
$('.overlayPopup a').click(function (){
    $('html').removeClass('topSearchActiveNow');
});
$('.topTitleSearch a').click(function (){
    $('.advancesSearchWrp').removeClass('on');
});




}); /*ready close*/

function usershorting(fieldval, caller) {

    var statussearch = $("#statussearch").val();
    var orderfield = $("#orderfield").val();
    var controller = $("#controll").val();

    var modulURL = BASE_URL + '/admin/' + controller;

    //alert(fieldval+' - '+caller);
    if (caller == 'orderfield') {
        if (statussearch != '') {
            url = modulURL + '/index/statussearch/' + statussearch + '/orderfield/' + fieldval;
        } else {
            url = modulURL + '/index/orderfield/' + fieldval;
        }
    } else if (caller == 'statussearch') {
        if (orderfield != '') {
            url = modulURL + '/index/statussearch/' + fieldval + '/orderfield/' + orderfield;
        } else {
            url = modulURL + '/index/statussearch/' + fieldval;
        }
    } else {
        url = modulURL + '/index/';
    }
    //alert(url); return false;
    window.location.href = url;

}

function sentimentsshorting(fieldval, caller) {

    var statussearch = $("#statussearch").val();
    var orderfield = $("#orderfield").val();
    var entity = $("#entity").val();

    var modulURL = BASE_URL + '/admin/dashboard/userstalkingon/entity/' + entity+'/polarity/'+fieldval;


    window.location.href = modulURL;

}

/*function conMentionTotext(text){
     $('.trickCmntDiv').remove();
      text  = text.replace(/<br>/gi, '##br##');
        $('body').append('<textarea  class="trickCmntDiv" style="display:none">'+text+'</textarea>');        
        text = $('.trickCmntDiv').html();
        $('.trickCmntDiv').remove();
        text  = text.replace(/##br##/gi, '<br />');
       
        return text;
 }*/

 function fetchDbeeUser(thisEl,type) {
   var thisEl = $(thisEl);
     if(thisEl.closest('#RefPostTargetTwitterTable').hasClass('disabled')==true) {
        thisEl.addClass('disabled');
        return false;
    }
    $('#detailsDialog').remove();
    var htmlLightbox = '<div id="detailsDialog" title="Platform users">\
                <div class="srcUsrWrapper">\
                  <div class="sprite searchIcon2"></div>\
                    <input type="text" data-type="'+type+'" id="userlisttwittertag" class="userFatchList" socialFriendlist="true"  placeholder="type user name and hit enter">\
                  <div class="srcUsrtotal" style="display:none;">40 <i>total</i></div>\
                </div>\
                <div id="datacollect" style="float:none"></div>\
                <div id="userInfoContainer"></div>\
              </div>';

    $('body').append(htmlLightbox);

    $("#detailsDialog").dialog({
        dialogClass: 'detailsDialogBox',
        width: 800,
        height: 500,
        title: 'Invite Platform users',
        open: function() 
        {
            $fluidDialog();
            $("#datacollect").html('');
            $('.ui-dialog-title').html('Please select user(s)');
        },
        buttons: {
            "Submit": function() {
                thisEl = $(this);
                var userInfo = [];
                $('input:checkbox[name=groupuser]').each(function() 
                {
                    if ($(this).is(':checked'))
                        userInfo.push($(this).val());
                });
                if(userInfo.length==''){
                    $messageError('Please select a user');
                    return false
                }
                var stringuserInfo = userInfo.join();
                var onlyplateformuser = $('#checktbltwittertaguser').val();
                var fortype = $('#fortype').val();
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    data: {
                        'stringuserInfo': stringuserInfo,
                        'fortype':fortype,
                        'status':1
                    },
                    url: BASE_URL + '/admin/myaccount/connectplateformuser',
                    success: function(response) {
                        $messageSuccess(response.message);
                        userlist(fortype);
                        thisEl.dialog("close");
                    }
                });
            }
        }
    });
}