var ProfileDetailsHttp; 
var currentGroupId = '';
var currentGroupName ='';

function seeuserprofile(id, sid, group) {
    group = typeof(group) != 'undefined' ? group : '0';
    sid = typeof(sid) != 'undefined' ? sid : '0';
    ProfileDetailsHttp = Browser_Check(ProfileDetailsHttp);
    var url = BASE_URL + "/profile/detail";
    var data = "user=" + id + "&sid=" + sid + "&groupvalcheck=" + group;
    ProfileDetailsHttp.open("POST", url, true);
    ProfileDetailsHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ProfileDetailsHttp.setRequestHeader("Content-length", data.length);
    ProfileDetailsHttp.setRequestHeader("Connection", "close");
    ProfileDetailsHttp.onreadystatechange = function() {
        seeuserprofileresult(id);
    };
    ProfileDetailsHttp.send(data);
}

function seeuserprofileresult(id, fade) {
    if (ProfileDetailsHttp.readyState == 4) {
        if (ProfileDetailsHttp.status == 200 || ProfileDetailsHttp.status == 0) {
            var result = ProfileDetailsHttp.responseText;
            var resultArr = result.split('~#~');
            document.getElementById('profile-highlighted').innerHTML = resultArr[0];
            if (resultArr[2] == '1') { // IF USER IS LOGGED IN

                if (resultArr[1] == '1') {
                    document.getElementById('follow-user').className = 'poplight btn btn-yellow';
                    document.getElementById('followme-label').innerHTML = 'Unfollow';
                    document.getElementById('follow-popup').innerHTML = 'You do not follow ' + resultArr[3] + ' any more';
                } else {
                    document.getElementById('follow-user').className = 'poplight btn btn-yellow';
                    document.getElementById('followme-label').innerHTML = 'Follow';
                    document.getElementById('follow-popup').innerHTML = 'You now follow ' + resultArr[3];
                }
            }
        }
    }
}



function TwitterInviteFriendsprofile() {
    $.dbeePopup('close');
    var data;
    var title = '<h2>Invite Twitter followers</h2>';
    var container = ' <div class="srcUsrWrapper">\
                            <div class="fa fa-search fa-lg searchIcon2"></div>\
                            <input type="text" id="userFatchList" class="userFatchLists" value=""  onkeyup="javascript:filtersocailuser()" socialFriendlist="true">\
                            <div id="Usercountfilter" class="Usercountfilter srcUsrtotal" Usercountfilter="true"></div>\
                          </div>\
                          <div id="socalnetworking-twilist">\
                            <div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div>\
                          </div>';
    var btns = '<a href="javascript:void(0);" class="pull-right btn btn-yellow socialInviteLink"  id="SendMessageTwitterprofile" onclick="javascript:selectuserstoTwitterprofile();">Invite</a>';
    var template = title+container;
    var privatePost = false;

    $("#content_data_button").hide();

    if (currentGroupId != '') {
       btns ='<div class="btnGroup pull-right"><select id="PrivatePost" name="PrivatePost"  class="pull-left PrivatePost" tabindex="-1" ><option value="0">Open invite</option><option value="1">Lock to Group</option></select><a href="javascript:void(0);" class="pull-left btn btn-yellow SendMessageTwitterprofile" onclick="javascript:selectuserstoTwitterprofilegroup(\'' + currentGroupId + '\');">Invite</a></div>';
       privatePost=true; 
    }

    setTimeout(function() {
        $.dbeePopup(template, {
            overlayClick: false,
            load:function(){
                    if(privatePost==true)
                    privateHTMLPost();
                },
            otherBtn: btns

        });


    }, 500)
    $.ajax({
        type: "POST",
        dataType: 'json',
        url: BASE_URL + '/social/twitteruser',
        success: function(response) {
           var cnt = $("#socalnetworking-twilist");
                $('.sfakeDiv').remove();
                cnt.append('<div class="sfakeDiv" style="display:none;">'+response.content+'</div>');
                $.dbeePopup('resize');
               $("img", cnt).load(function() {
                    $(".loaderAjWrp", cnt).remove();
                    $(".sfakeDiv", cnt).show();                    
                    $.dbeePopup('resize');
               });
        }
    });
}


function selectuserstoTwitterprofile() 
{
    $('#SendMessageTwitterprofile').append(' <i class="fa fa-spinner fa-spin"> </i>');
    var userInfo = [];
    var flag = 0;
    $('input:checkbox[name=twitter]').each(function() {
        if ($(this).is(':checked')) {
            userInfo.push($(this).val());
            flag = 1;
        }
    });
    if (flag == 0) {
        $dbConfirm({
            content: 'Please select a twitter follower',
            yes: false,
            error: true
        });
        $('.fa-spin').remove();
        return false;
    }

    var stringuserInfo = userInfo.join();
    //alert(stringuserInfo);
    $.ajax({
        type: "POST",
        url: BASE_URL + "/social/sendtotwittermessage",
        data: {
            "frienddata": stringuserInfo
        },
        success: function(result) {

           /* $dbConfirm({
                content: 'Invitation sent',
                yes: false
            });*/
            $('.closePostPop').trigger('click');
        },
        error: function(error) {

        }
    });
}



$(function() {


    $('body').on('click', '.SendMessageProfileLinkedin', function() 
    {

        $(this).append(' <i class="fa fa-spinner fa-spin"> </i>');
        var userInfo = [];
        var flag = 0;

        $('input:checkbox[name=linkedin]').each(function() {
            if ($(this).is(':checked')) {
                userInfo.push($(this).val());
                flag = 1;
            }
        });

        if (flag == 0) {
            $dbConfirm({
                content: 'Please select a Linkedin connections',
                yes: false,
                error: true
            });
            $('.fa-spin').remove();
            return false;
        }
        $(this).removeClass('SendMessageProfileLinkedin');
        var stringuserInfo = userInfo.join();

        var dbid = $('#dbid').val();


        $.ajax({
            type: "POST",
            dataType: 'json',
            url: BASE_URL + "/social/sendmessagelinkedin",
            data: {
                "userid": stringuserInfo
            },
            success: function(result) {
                $('.closePostPop').trigger('click')
                $dbConfirm({
                    content: 'Invitation sent',
                    yes: false
                });
                $.dbeePopup('close');
            }
        });
    });


  $('body').on('click', '.SendMessageProfileFacebook', function() {
        
        $(this).append(' <i class="fa fa-spinner fa-spin"> </i>');
        var userInfo = [];
        var flag = 0;
        $('input:checkbox[name=FacebookInvite]').each(function() 
        {
            if ($(this).is(':checked')) {
                userInfo.push($(this).val());
                flag = 1;
            }
        });
        if (flag == 0) 
        {
            $dbConfirm({
                content: 'Please select a facebook friends',
                yes: false,
                error: true
            });
            $('.fa-spin').remove();
            return false;
        }
        $(this).removeClass('SendMessageProfileFacebook');

        var stringuserInfo = userInfo.join();
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: BASE_URL + "/social/detailfriendsend",
            data: {"userid": stringuserInfo},
            success: function(result) {
                if(result.status=='success')
                {
                  $('.closePostPop').trigger('click');
                  $dbConfirm({
                      content: 'Invitation sent',
                      yes: false
                  });
                  $.dbeePopup('close');
                }else{
                  facebookLogin();
                }

            }
        });
    });

    $('body').on('click', '.invitetwwifri', function() {
        TwitterInviteFriendsprofile();
    });

    $('body').on('click', '.invitefacebookfri', function() {    
        facebookfriends();
    });

});



function linkedinUserProfile() {
    $.dbeePopup('close');
    var dbid = $('#dbid').val();
    var title = '<h2>Invite LinkedIn connections</h2>';
    var container = '<div id="socalnetworking-linkedinlist" class="clearfix"><div class="loaderAjWrp" style="height: 50px;background:#666;"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>';
    if(dbid!=''){
        var btns = '<a href="javascript:void(0);" class="pull-right btn btn-yellow socialInviteLink SendMessageLinkedin" >Invite</a>';
    }else{
        var btns = '<a href="javascript:void(0);" class="pull-right btn btn-yellow socialInviteLink SendMessageProfileLinkedin" >Invite</a>';
    }
    var data;
    var template = title+container;
    var privatePost = false;
    if (currentGroupId != '') 
    {
        privatePost = true;
        data = {groupid: currentGroupId};
        btns = '<div class="btnGroup pull-right"><select id="PrivatePost" name="PrivatePost"  class="pull-left PrivatePost" tabindex="-1" ><option value="0">Open invite</option><option value="1">Lock to Group</option></select><a href="javascript:void(0);" class="pull-right btn btn-yellow SendMessageProfileLinkedingroup"  group-id="'+currentGroupId+'">Invite</a></div>';
    }
    setTimeout(function() {
        $.dbeePopup(template, {
            overlay: true,
            load:function(){
                if(privatePost==true)
                privateHTMLPost();
            },
            otherBtn: btns
        });

        $.ajax({
            type: "POST",
            dataType: 'json',
            data:data,
            url: BASE_URL + '/social/linkedinuser',
            success: function(response) {
                var cnt =   $("#socalnetworking-linkedinlist");
                $('.sfakeDiv').remove();
                 cnt.append('<div class="sfakeDiv" style="display:none;">'+response.content+'</div>');
                $.dbeePopup('resize');
               $("img", cnt).load(function() {
                     $(".loaderAjWrp", cnt).remove();
                     $(".sfakeDiv", cnt).show();                  
                     $.dbeePopup('resize');
               });
            }
        });

    }, 500);
}




function facebookfriends() 
{
    $.dbeePopup('close');
    var dbid = $('#dbid').val();
    var title = '<h2>Invite Facebook friends </h2>';
    var container = '<div id="socalnetworking-facebooklist"><div class="loaderAjWrp" style="height: 50px;background:#666;"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>';
    if(dbid!=''){
         var btns = '<a  href="javascript:void(0);" class="pull-right socialInviteLink btn btn-yellow ExpertPostToFacebook" >Invite</a>';
    }else{
        var btns = '<a href="javascript:void(0);" class="pull-right socialInviteLink SendMessageProfileFacebook btn btn-yellow" >Invite</a>';
    }
    var template = title+container;
    var privatePost = false;
    var data;
    setTimeout(function() {
    if (currentGroupId != '') {
        privatePost = true;
         template = title+currentGroupName+container;
         btns ='<div class="btnGroup pull-right"><select id="PrivatePost" name="PrivatePost"  class="pull-left PrivatePost" tabindex="-1" ><option value="0">Open invite</option><option value="1">Lock to Group</option></select><a href="javascript:void(0);" class="pull-right socialInviteLink btn btn-yellow SendPostToFacebookgroup"  groupidfacebook="'+currentGroupId+'">Invite</a></div>';        
         data = {groupid:currentGroupId};
    }
        $.dbeePopup(template, {
            overlay: true,
            load:function(){ 
                if(privatePost==true)
                privateHTMLPost()
            },
            otherBtn: btns
        });

    $.ajax({
        type: "POST",
        dataType: 'json',
        data:data,
        url: BASE_URL + '/social/facebookfriends',
        success: function(response) 
        {
            if(response.status=='success')
            {
                var cnt =   $("#socalnetworking-facebooklist");
                $('.sfakeDiv').remove();
                cnt.append('<div class="sfakeDiv" style="display:none;">'+response.content+'</div>');
                $.dbeePopup('resize');
                $("img", cnt).load(function() {
                    $(".loaderAjWrp", cnt).remove();
                    $(".sfakeDiv", cnt).show();
                    $.dbeePopup('resize');
                });
           }
           else
            {
                facebookLogin ='<a href="/social/facebook" class="shareAllSocials fbAllShare">\
                      <div class="signwithSprite signWithSpriteFb">\
                        <i class="fa dbFacebookIcon fa-5x"></i>\
                        <span>Facebook</span>\
                      </div>\
                        </a>';
                $.dbeePopup(facebookLogin);
            }

        }
      });

    }, 500);
}