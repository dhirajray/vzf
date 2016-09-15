// FUNCTION TO LOAD MY GROUPS
function loadmygroups(user, memberof, gtype) {
    memberof = typeof(memberof) != 'undefined' ? memberof : '0';
    gtype = typeof(gtype) != 'undefined' ? gtype : '0';
    $('#my-dbees').html('<div style="margin-left:25px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>');
    $("#my-dbees-profile").addClass("feed-link feed-link-border");
    $('#AllGroups').remove();
    /*var myredbeesprofile = $('#my-redbees-profile');
    if (myredbeesprofile)
    $('#my-dbee-profile').removeClass('active');
    $('#my-redbees-profile').removeClass('active');
    $('#my-comments-profile').removeClass('active');
    $('#my-leaguepos-profile').removeClass('active');
    $('#other-followers-profile').removeClass('active');
    $('#other-following-profile').removeClass('active');
    $('#my-groups-profile').addClass("active");*/
    $.ajax({
        type: "POST",
        url: BASE_URL + "/group/groupdetail",
        dataType : 'json',
        data: {
            "user": user,
            "memberof": memberof,
            "type": gtype
        },
        cache: true,
        success: function(result) {
          //  var resultArr = result.split('~#~');
          if(memberof==0){
            $('#xxgrpCreated').addClass('active');
            $('#grpmemberof').removeClass('active');
           // $('#AllGroups').removeClass('active');
        }
        if(memberof==1){
            $('#xxgrpCreated').removeClass('active');
            $('#grpmemberof').addClass('active');
            //$('#AllGroups').removeClass('active');
        }
           // $('#AllGroups').remove();
            $('#my-dbees').html(result.content);
        }

    });
}

function loadallgroups(user, memberof, gtype) {
    memberof = typeof(memberof) != 'undefined' ? memberof : '0';
    gtype = typeof(gtype) != 'undefined' ? gtype : '0';
    $('#leftListing').removeClass('groupViewListing');
    $('#my-dbees').html('<div style="margin-left:25px; margin-top:10px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>');
    $("#my-dbees-profile").addClass("feed-link feed-link-border");
    $('#xxgrpCreated').remove();
    $('#grpmemberof').remove();
    $('#search-groups').remove();
    $('.group-creategroups').remove();
    
    
    $.ajax({
        type: "POST",
        url: BASE_URL + "/group/allgroups",
        dataType : 'json',
        data: {
            "user": user,
            "memberof": memberof,
            "type": gtype
        },
        cache: true,
        success: function(result) {
          //  var resultArr = result.split('~#~');

           // $('#AllGroups').addClass('active');
            $('#leftListing').addClass('groupViewListing');
            $('#my-dbees').html(result.content);
            $('body').addClass('activeAllGroups');
        }
    });
}

// FILL GROUP FEED VIEW OPTIONS DIV ON PROFILE
function fillgroupfeedoptions(user) {
    $.ajax({
        type: "POST",
        url: BASE_URL + "/group/feeoptiongroup",
        data: {
            "user": user,
        },
        success: function(result) {
            $('#groupfeed-profile-options').html(result);
        }
    });
}

// invite more users fot group
function refreshInvitetogroup(id) {
    createrandontoken(); // creting user session and token for request pass
    data = 'group=' + id + '&' + userdetails;
    $.ajax({
        type: "POST",
        data: data,
        url: BASE_URL + '/group/inviteuserstogroup',
        async: false,
        beforeSend: function() {},
        complete: function() {},
        cache: false,
        success: function(response) {
            $("#content_data").html(response);
            $('.processGroupBtnOk').show();
          setTimeout(function() {
                $.dbeePopup('resize');
            }, 500);
        },
        error: function(error) {
            $("#content_data").html(
                "Some problems have occured. Please try again later: " + error);
        }
    });
}



// FUNCTION TO SKIP GROUP INVITE
function backtoselection() {
    $('#search-invite-list').hide();
    $('#userset').removeClass('active');
     $('#user_list').hide();
    $('#search-list').hide();
    $('#following-list-create').hide();
    $('#followingnlistgroup').removeClass('active');
    $('#followerslistgroup').show().addClass('active');
    $('#search').show().removeClass('active');
    $('#skipinviteval').show().removeClass('active');
    $('#followers-list-create').css('display', 'block');
    $('#backone').show();
    $('#backtwo').show();
    $('#invite-group-members').show();
    $('#invite-selected-div').hide();
    $('#invite-message').hide();
    $('#invite-messageso').hide();
    $('#backbuttonso').hide();
    $('.processGroupBtnOk').show();
    $('.processGroupBtnCreate, .processGroupBtnSendInvite').hide();

    setTimeout(function() {
        $.dbeePopup('resize');
    }, 100);

}

function skipinvitetogroup() {
    $('.processGroupBtnOk').hide();
    $('.processGroupBtnCreate').show();
    $('#invite-group-members').hide();
    $('#invite-message').hide();
    $('#total-users-toinvite').val('');
    $('#invite-selected').hide();
    $('#invite-selected-div').show();
    $('#invitetogroup-header').hide();
    $('#selected-users-label')
        .html(
            'You have chosen to create the group without inviting anyone.');
    setTimeout(function() {
        $.dbeePopup('resize');
    }, 100);
}

// FUNCTION TO GET BACK TO CREATE GROUP FROM INVITE
function backtocreategroup() {
    $('#allrev').hide();
    $('#invite-group-members').hide();
    $('#invite-message').hide();
    $('#create-groups-wrapper').show();
    $('#group-name').val();
    $("#group-type-other").val('');
    $('#group-other-textbox').hide();
    $('.processGroupBtnNext1').hide();
    $('.processGroupBtnNext2').hide();
    $('.processGroupBtnOk').hide();
    $('.createGroupBtn').show();
    $.dbeePopup('resize');
}

// FUNCTION TO INITIATE SEARCH GROUPS
function searchgroupsinitiate() {

    var keyword = $('#groupkeyword').val();
    var type = $('#grouptype').val();
    $.ajax({
        type: "POST",
        dataType: 'json',       
        data: {
            'keyword': keyword,
            'type': type
        },
        url: BASE_URL + '/group/searchgroups',       
        beforeSend: function() {
            $('.searchmygroup').append(
                ' <i class="fa fa-spinner fa-spin"> </i>').css('cursor',
                'default').addClass("disabled");
                 $.dbeePopup('resize');
        },
        complete: function() {
            setTimeout(function() {
                //$('.searchmygroup').attr("onclick",
                //		"javascript:searchgroupsinitiate();");
                $('.searchmygroup .fa-spin').remove();
                $('.searchmygroup').css('cursor', 'pointer');
            }, 700);
        },        
        success: function(response) {
             $('.searchmygroup').removeClass("disabled")
            $("#content_data").html(response.content);
            searchgroups(keyword, type);
            // $dbConfirm({content:"Search completed successfully !",
            // yes:false});
            setTimeout(function() {
                $.dbeePopup('resize');
            }, 2000);
        },
        error: function(error) {
            $("#content_data").html(
                "Some problems have occured. Please try again later: " + error);
        }
    });
}

// FUNCTION TO SEARCH GROUPS
function searchgroups(keyword, type) {
    $('#search-results').html('<div style="margin:20px 0 0 0;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>');
    $.ajax({
        type: "POST",       
        url: BASE_URL + "/group/searchinggroups",

        beforeSend:function(){
             $.dbeePopup('resize');
        },
        data: {
            "keyword": keyword,
            "type": type,
            "serchinggroup": 'serchinggroup'
        },
        success: function(result) {
            $('#search-results').html(result);
             $.dbeePopup('resize');
        }
    });
}

// FUNCTION TO LOAD FOLLOWERS TO INVITE TO GROUP
function loadfollowersforgroup(from, group, calling) {
    // alert('in here');
    calling = typeof(calling) != 'undefined' ? calling : '';
    if (calling == 'followers') {
        loading = $('.showMoreFollowerPopup').attr('loadmore');
        offset = $('.showMoreFollowerPopup').attr('currentOffset');
    }
	if(from=='directinvite') offset = '0';
    loading = typeof(loading) == 'undefined' ? '' : loading;
    offset = typeof(offset) == 'undefined' ? '0' : offset;

    $('#allrev').hide();
    from = typeof(from) != 'undefined' ? from : '0';
    group = typeof(group) != 'undefined' ? group : '0';
    $.ajax({
        type: "POST",
        url: BASE_URL + "/group/invitefollowing",        
        data: {
            "groupname": 'name',
            "usertype": 'followers',
            "from": from,
            "group": group,
            'offset': offset,
            'loading': loading
        },
        success: function(result) {
            var resultArr = result.split('~#~');
            loadbtn = '<div class="btn btn-green pull-right showMoreFollowerPopup" style="display:none" loadmore="true" currentOffset="0" onclick=loadfollowersforgroup("","","followers")>show more</div>';
            $(".showMoreFollowerPopup").remove();
            if (loading == '') {
                $('#followers-list-create').html(resultArr[0]);
                if (resultArr[2] > 24) {
                    $("#followers-list-create").append(loadbtn);
                    $('.showMoreFollowerPopup').show();
                    $('.showMoreFollowerPopup').attr('currentOffset', Number(resultArr[3]));
                 
                } else {
                    $('.showMoreFollowerPopup').hide();
                }
            } else {

                $('#followers-list-create').append(resultArr[0]);
                if (resultArr[2] > 24) {
                    $("#followers-list-create").append(loadbtn);
                    $('.showMoreFollowerPopup').show();
                    $('.showMoreFollowerPopup').attr('currentOffset', Number(resultArr[3]));
                  
                } else {
                    $('.showMoreFollowerPopup').hide();
                }
                  
            }
            $.dbeePopup('resize');
            $('#total-followers').val(resultArr[1]);
        }
    });
}

// FUNCTION TO LOAD FOLLOWING USERS TO INVITE TO GROUP
function loadfollowingforgroup(from, group, calling) {
    calling = typeof(calling) != 'undefined' ? calling : '';
    if (calling == 'following') {
        loading = $('.showMoreFollowingPopup').attr('loadmore');
        offset = $('.showMoreFollowingPopup').attr('currentOffset');
    }
	if(from=='directinvite') offset = '0';
    loading = typeof(loading) == 'undefined' ? '' : loading;
    offset = typeof(offset) == 'undefined' ? '0' : offset;

    $('#allrev').hide();
    from = typeof(from) != 'undefined' ? from : '0';
    group = typeof(group) != 'undefined' ? group : '0';
    $.ajax({
        type: "POST",
        url: BASE_URL + "/group/invitefollowing",       
        data: {
            "groupname": 'name',
            "usertype": 'following',
            "from": from,
            "group": group,
            'offset': offset,
            'loading': loading
        },
        success: function(result) {
            var resultArr = result.split('~#~');
            loadbtn = '<div class="btn btn-green pull-right showMoreFollowingPopup" style="display:none" loadmore="true" currentOffset="0" onclick=loadfollowingforgroup("","","following")>show more</div>';
            $(".showMoreFollowingPopup").remove();
            if (loading == '') {
                $('#following-list-create').html(resultArr[0]);

                if (resultArr[2] > 24) {
                    $("#following-list-create").append(loadbtn);
                    $('.showMoreFollowingPopup').show();
                    $('.showMoreFollowingPopup').attr('currentOffset', Number(resultArr[3]));
                    $.dbeePopup('resize');
                } else {
                    $('.showMoreFollowingPopup').hide();
                }
            } else {

                $('#following-list-create').append(resultArr[0]);
                if (resultArr[2] > 24) {

                    $("#following-list-create").append(loadbtn);
                    $('.showMoreFollowingPopup').show();
                    $('.showMoreFollowingPopup').attr('currentOffset', Number(resultArr[3]));
                    $.dbeePopup('resize');
                } else {
                    $('.showMoreFollowingPopup').hide();
                }
            }

            $('#total-following').val(resultArr[1]);
        }
    });
}

// FUNCTION TO SHOW SELECTED USERS FOR INVITE
function selectuserstoinvite() {

    var err = false;
    var userInfo = [];

    if ($('#Logintype').val() == 1) {
        $('input:checkbox[name=FacebookInvite]').each(function() {
            if ($(this).is(':checked'))
                userInfo.push($(this).val());
        });
        var stringuserInfo = userInfo.join();
        $("#total-users-toinvite-social").val(stringuserInfo);
    } else if ($('#Logintype').val() == 2) {
        $('input:checkbox[name=twitter]').each(function() {
            if ($(this).is(':checked'))
                userInfo.push($(this).val());

        });
        var stringuserInfo = userInfo.join();
        $("#total-users-toinvite-social").val(stringuserInfo);
    } else if ($('#Logintype').val() == 3) {
        $('input:checkbox[name=linkedin]').each(function() {
            if ($(this).is(':checked'))
                userInfo.push($(this).val());

        });
        var stringuserInfo = userInfo.join();
        $("#total-users-toinvite-social").val(stringuserInfo);
    }
    $("#allrev").hide();
    $("#invitetogroup-header").show();
    $("#invite-selected").show();
    $("#selected-users-label")
        .html('Users selected by you to invite to group.');
    var totalfollowers = $('#total-followers').val();
    var totalfollowing = $('#total-following').val();
    var totalsearch = $('#total-search').val();
    var userset = $('#selectgropns').val();
   
    var users = [];
    $('input[id^=inviteuser-]').each(function(index, el) {
        if ($(this).is(':checked')) users.push($(this).val());
    });

       if(userset!=''){
          $.ajax({
            type: "POST",
            dataType: "json",
            data: {
                'setid': userset                
            },
            url: BASE_URL + '/group/getuserset',  
            async:false,          
            success: function(response) { 
                var nsets = response.usergroupset;
               
                 $.each(nsets,function(i,nset) {                    
                     users.push(nset.userid);  
                });
            console.log(users);                  

            },
            error: function(error) {

            }

        });

    }   
   
    if (users.length === 0) {
        err = true;
    }   

    $('#total-users-toinvite').val(users);
    if (err == true) {
        /*$('.processGroupBtnOk').hide();
		$('.processGroupBtnCreate').show();
		$('.processGroupBtnSendInvite').show();
		$('#invite-group-members').hide();*/
        var soval = $("#total-users-toinvite-social").val();
        if ($("#total-users-toinvite-social").val() != '' || users != '') {
            $("#invite-message").show(); // show message for social type
            $("#invite-messageso").show();
            //$("#backbuttonso").show();
        } else {
            $('#invite-messageso')
                .html(
                    "<font color='red'>Please select users to invite to group.</font>");
            $("#invite-messageso").show();
            //$("#backbuttonso").show();
            setTimeout(function() {
                $("#invite-message").fadeOut("slow", function() {
                    $("#invite-message").html('');

                });
            }, 2000);
        }
        $.dbeePopup('resize');
        return true;
    }
    if (!err) {
        $.ajax({
            type: "POST",
            url: BASE_URL + "/group/selecttoinvite",
            data: {
                "users": users
            },
            success: function(result) {
                $('.processGroupBtnOk').hide();
                $('.processGroupBtnCreate').show();
                $('.processGroupBtnSendInvite').show();

                if ($("#total-users-toinvite-social").val() != '') {
                    $("#invite-message").show(); // show message for social
                    // type
                }

                var resultArr = result.split('~#~');
                $('#invite-group-members').hide();
                $('#invite-selected-div').show();
                $('#invite-selected').html(resultArr[0]);
                $.dbeePopup('resize');

            }
        });
    }
}


// remove the users in the invite list to create group
function removeuserfrominvite(user) {
    var users = $('#total-users-toinvite').val();
    users = users.replace(user + ',', "");
    $('#total-users-toinvite').val(users);
    $('#select-invite-' + user).hide();
}

// FUNCTION TO MOVE TO STEP 2 OF CREATE GROUP
function creategroupstep2() {
    err = false;
    len = $.trim($('#group-name').val());

    var elm = $('#passform .required');
        $checkError(elm);
       if($checkError(elm)==false){
        return false;
       }
   
    /*if (len == "") {
        $dbConfirm({
            content: 'Please enter your group name.',
            yes: false,
            error: true
        });
        $("#group-name").focus();
        err = true;
    } else {}*/
    if (!err) {
        $('#create-groups-wrapper').hide();
        $('#creategroup-step2').show();
        $('.postTypeFooter .createGroupBtn').hide();
        $('.postTypeFooter .processGroupBtnNext1').show();
        $('.titlePop').text('Add a Group logo & description (optional)');
        var myDropzoneGroup = new Dropzone("#uploadGroupDropzone", {
            url: BASE_URL + "/picture/groupimageupload",
            maxFiles: 1,
            maxFilesize: 5,
            addRemoveLinks: true,
            parallelUploads: 1,
            acceptedFiles: '.png, .jpg, .jpeg, .gif, .JPG, .JPEG, .PNG, .GIF'
        });
        var fileList;
        myDropzoneGroup.on("maxfilesexceeded", function(file, serverFileName) {
            // $messageError('You can upload only 1 file');
            $dbConfirm({
                content: 'You can upload only 1 file',
                yes: false,
                error: true
            });
        });

        /*
         * myDropzoneGroup.on("error", function (file, serverFileName) {
         * $dbConfirm({content:'Please upload only png, jpg, jpeg, gif file
         * type', yes:false,error:true}); });
         */

        myDropzoneGroup.on("error", function(file, serverFileName) {
            $dbConfirm({
                content: serverFileName,
                yes: false,
                error: true
            });
        });

        myDropzoneGroup
            .on(
                "success",
                function(file, serverFileName) {
                   /* if (file.width < 200) {
                        myDropzoneGroup.removeFile(file);
                        $dbConfirm({
                            content: 'Image width is too small.Smallest Image width should be: 200px.',
                            yes: false,
                            error: true
                        });
                    }*/

                    fileList = "serverFileName = " + serverFileName;
                    $('#PostGroupPix').val(serverFileName);
                    $.dbeePopup('resize');
                });
        myDropzoneGroup.on("removedfile", function(file) {

            createrandontoken();
            data += fileList + '&' + userdetails;
            $.ajax({
                url: BASE_URL + "/picture/groupimageunlink",
                type: "POST",
                dataType: "json",
                data: data,
                success: function() {
                    $('#PostGroupPix').val('');
                    $.dbeePopup('resize');
                }
            });

        });
    }
     $.dbeePopup('resize');
}

function SplitTheString(CommaSepStr) {
    var ResultArray = null;

    if (CommaSepStr != null) {
        var SplitChars = ',';
        if (CommaSepStr.indexOf(SplitChars) >= 0) {
            ResultArray = CommaSepStr.split(SplitChars);

        }
    }
    return ResultArray;
}

function sendMessageToSocialSite(groupid) {
    string_val = $("#total-users-toinvite-social").val();
    if (string_val != '') {
        if ($("#Logintype").val() == 1) {
            var social_type = 'Facebook';

        }
        if ($("#Logintype").val() == 2) {
            var social_type = 'Twitter';
        }
        if ($("#Logintype").val() == 3) {
            var social_type = 'Linkedin';
        }
        $.ajax({
            type: "POST",
            dataType: "json",
            data: {
                'userid': string_val,
                'groupid': groupid,
                'social_type': social_type
            },
            url: BASE_URL + '/social/invite',            
            success: function(response) {

            },
            error: function(error) {

            }

        });
    }

}

// FUNCTION TO CREATE GROUP
var CreateGroupHttp;

function creategroup() {

    createrandontoken(); // creting user session and token for request pass

    err = false;
    if ($('#group-name').val() == '') {
        $('#group-name').focus();
        err = true;
    } else {
        var groupname = $.trim($('#group-name').val());
    }
   
    var grouptype = $('input[name=group-type]:checked', '#create').val();
    var grouptypeother = $("#group-type-other").val();

    var groupprivacy = $('input[name=group-privacy]:checked', '#create').val();
    var restgroupdb = $('input[name=rest_groupdb]:checked', '#create').val();
    var invitetoexpert = $('input[name=invite_expert]:checked', '#create')
        .val();
    grouptype = typeof(grouptype) != 'undefined' ? grouptype : '0';
    grouptypeother = grouptypeother == '' ? '0' : grouptypeother;
    groupprivacy = typeof(groupprivacy) != 'undefined' ? groupprivacy : '0';
    restgroupdb = typeof(restgroupdb) != 'undefined' ? restgroupdb : '0';
    invitetoexpert = typeof(invitetoexpert) != 'undefined' ? invitetoexpert : '0';
    groupbgimage = $('#GroupBackgroundPix').val();
    groupbgimage = typeof(groupbgimage) != 'undefined' ? groupbgimage : '';
    if (document.getElementById("PostGroupPix")) {
        var grouppicpre = $('#PostGroupPix').val();
    } else {
        var grouppicpre = 'default-avatar.jpg';
    }
    if (!grouppicpre) {
        var grouppic = 'default-avatar.jpg';
    } else {
        var grouppic = $('#PostGroupPix').val();
    }
    var groupdesc = $('#group-desc').val();
    groupdesc = conMentionTotext(groupdesc);
    if (grouptype == '')
        grouptype = 0;
    if (!err) {
        CreateGroupHttp = Browser_Check(CreateGroupHttp);
        var url = BASE_URL + "/group/insertdata";
        //var data = "groupbgimage=" + groupbgimage + "&groupname=" + groupname + "&grouptype=" + grouptype + "&grouptypeother=" + grouptypeother + "&restgroupdb=" + restgroupdb + "&invitetoexpert=" + invitetoexpert + "&groupprivacy=" + groupprivacy + "&grouppic=" + grouppic + "&groupdesc=" + groupdesc + '&' + userdetails;
        
        var data = {groupbgimage:groupbgimage,groupname:groupname,grouptype:grouptype,grouptypeother:grouptypeother,restgroupdb:restgroupdb,invitetoexpert:invitetoexpert,groupprivacy:groupprivacy,grouppic:grouppic,groupdesc:groupdesc}

        margeArray(data, userdetails2);


        $('.processGroupBtnCreate a.btn-yellow').append(' <i class="fa fa-spinner fa-spin"> </i>').css('cursor','default').removeAttr("onclick");
     
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success:function(response){
                    var result = response;
                    setTimeout(function() {
                        $('.processGroupBtnCreate a').attr("onclick",
                            "javascript:updateprofile();");
                        $('.processGroupBtnCreate .fa-spin').remove();
                        $('.processGroupBtnCreate a').css('cursor', 'pointer');
                    }, 2000);
                if (result != '0') {
                    sendgroupinvite(result);
                    sendMessageToSocialSite(result);
                    var userid = $("#profileuser").val();
                    var grouphidden = $('#groupsec').val();
                    if (grouphidden == 'groupsec0' && userid != '') {
                        loadmygroups(userid);
                    }
                    var gPrivacy = ['','Open','Private','Requet to join'];
                    newAddedGroups+='<option value="'+result+'" groupname="'+groupname+'" grouptype="'+groupprivacy+'">'+groupname+' - '+gPrivacy[groupprivacy]+'</option>';
                    
                    $('.closePostPop').trigger('click');
                  
                } else {
                        $('#invite-group-members').hide();
                        $('#invite-message').show();
                        $('#invite-message').html(result);
                }
                callsocket();
            }
        });
        


        // $('.processGroupBtnCreate').hide();
    }
}



function getGroupType(forms) {
    var oRadio = document.forms[0].elements['group-type'];
    for (var i = 0; i < oRadio.length; i++) {
        if (oRadio[i].checked) {
            return oRadio[i].value;
        }
    }
    return '';
}

function getGroupPrivacy(forms) {
    var oRadio = document.forms[0].elements['group-privacy'];
    for (var i = 0; i < oRadio.length; i++) {
        if (oRadio[i].checked) {
            return oRadio[i].value;
        }
    }
    return '';
}

function autoselectgrouptype(type) {
    var oRadio = document.edit.elements['group-type'];
    for (var i = 0; i < oRadio.length; i++) {
        if (oRadio[i].value == type) {
            oRadio[i].checked = true;
            break;
        }
    }
}

function checkothergrouptype(n) {
    if (n == '-1') {
        $('#group-other-textbox').show();
        $('#group-type-other').focus();
    } else {
        $('#group-other-textbox').hide();
    }
}

function getGroupType2() {
    var oRadio = document.edit.elements['group-type'];
    for (var i = 0; i < oRadio.length; i++) {
        if (oRadio[i].checked) {
            return oRadio[i].value;
        }
    }
    return '';
}

function getEditGroupPrivacy2() {
        var oRadio = document.edit.elements['group-privacy'];
        for (var i = 0; i < oRadio.length; i++) {
            if (oRadio[i].checked) {
                return oRadio[i].value;
            }
        }
        return '';
    }
    // FUNCTION TO EDIT GROUP
var EditGroupHttp;

function editgroup() {

    err = false;
    if ($('#group-name').val() == '') {
        $('#group-name').css('#F3F2F1');
        $("#group-name").focus();
        err = true;
    } else {
        var groupname = $("#group-name").val();
        $('#group-name').css('#FFF');
    }
    var grouptype = getGroupType2();
    var groupprivacy = getEditGroupPrivacy2();
    var grouptypeother = $("#group-type-other").val();
    if ($("#group-pic").val())
        var grouppic = $("#group-pic").val();
    else
        var grouppic = $("#group-pic1").val();

    grouppicbg = $("#group-pic-bg").val();

    var GroupRes = $("#rest_groupdb").is(':checked') ? 1 : 0;
    var groupdesc = $("#group-desc").val();
    var groupid = $("#groupid").val();
    var invitetoexpert = $("#invitetoexperts").is(':checked') ? 1 : 0;

    if (!err) {
        $.ajax({
            type: "POST",
            url: BASE_URL + "/group/editgroupre",
            data: {
                "groupname": groupname,
                "grouptype": grouptype,
                "grouptypeother": grouptypeother,
                "groupprivacy": groupprivacy,
                "grouppic": grouppic,
                "groupdesc": groupdesc,
                "group": groupid,
                "GroupRes": GroupRes,
                "invitetoexpert": invitetoexpert,
                "grouppicbg": grouppicbg
            },
            success: function(result) {
                var resultArr = result.split('~#~');
                if (resultArr[0] != '0') {
                    window.parent.loadmygroups(resultArr[3], 0);

                    /*$dbConfirm({
                        content: 'Group details has been updated.',
                        yes: false
                    });*/
                    $.dbeePopup('close');
                }
            }
        });
    }
}

function getEditGroupPrivacy() {
    var oRadio = document.forms[0].elements['group-privacy'];
    for (var i = 0; i < oRadio.length; i++) {
        if (oRadio[i].checked) {
            return oRadio[i].value;
        }
    }
    return '';
}

// FUNCTION TO SHOW NEW MESSAGES
function showgroupnotifications(n) {
    $('#group-notification-feed').html(
        '<div style="margin-left:25px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>');
    $.ajax({
        type: "POST",
        url: BASE_URL + "/group/showgroupnotifications",
        data: {},
        success: function(result) {
            var resultArr = result.split('~#~');
            $('#group-notification-feed').html(resultArr[0]);
            setTimeout(function() {
                $.dbeePopup('resize');
            }, 500);
        }
    });
}

// FUNCTION TO RESPOND TO GROUP INVITATION
function respondgroupinvite(action, id, groupid, groupownerid, userid, type) {
    $
        .ajax({
            type: "POST",
            url: BASE_URL + "/group/respondinvite",
            data: {
                "valaction": action,
                "id": id,
                "groupid": groupid,
                "groupownerid": groupownerid,
                "userid": userid,
                "type": type
            },
            success: function(result) {

                var resultArr = result.split('~');
                if (action == 0)
                    $('#grpnote-' + id).remove();
                else {
                    $('#acept_' + resultArr[0]).html('<div class="btn btn-green btn-mini "><a href="' + BASE_URL + '/group/groupdetails/group/' + resultArr[3] + '"><span>Click here to go to group</span></a></div>');
                    if (resultArr[1] == 'no') {
                        jQuery(".grp-notification-feed")
                            .find("div")
                            .each(
                                function() {
                                    if ($(this).is(':hidden')) {
                                        $('#group-notification-feed')
                                            .html('No pending notifications.');
                                        setTimeout("closepopup('fade')", 2000);
                                    }
                                });
                    }
                }

                if (resultArr[2] == '0') {

                    // $('#notification-feed').html('<div align="center"
                    // style="margin-top:55px;" class="no-record-msg">No new
                    // notifications.</div>');
                }
                callsocket();
            }
        });
}

// FUNCTION TO SHOW GROUP DBEES
function loadgroupdbees(id,reload) {

    var start = $('#startnewmydb').val();
    var reload = (typeof(reload)!='undefined')?reload:'0';
    
   if(reload==0) start=0;
    $.ajax({
        type: "POST",
        dataType:'json',
        url: BASE_URL + "/group/groupdbees",
        data: {
            "group": id,
            "start":start
        },
        beforeSend:function(){
            if(reload==1){
                 $dbLoader('#see-more-feeds'+start,'progress');
            }
        },
        success: function(response) {
          
            if(reload==0){
                $('#dbee-feeds').html(response.content);
                 getMentionUser(response.dblistarry);
                $('#feedtype').val('group');
               
            }else{
             if($('#dbee-feeds .noFound').is(':visible')!=true){     
              $('#see-more-feeds'+start).remove();           
             $('#dbee-feeds').append(response.content);
             
                getMentionUser(response.dblistarry);
             $('#feedtype').val('group');
             $('#startnewmydb').val(response.startnew);
            }
            
             $('#startnewmydb').val(response.startnew);


            }
            $('#startnewmydb').val(response.startnew);
            if(response.locked=='1') {
                $('a:not(.joinDiscusstion, .toprightlogout, .signWithSMContainer)').attr('href','javascript:void(0)');
                $('a:not(.joinDiscusstion, .toprightlogout, .signWithSMContainer)').css('cursor', 'default');
                $('.fa-angle-down').hide();
            }
        //}
        }
    });
}

// FUNCTION TO JOIN GROUP
function joingroupreq(group, owner, remind, frompopup) {
    $.ajax({
        type: "POST",
        dataType: 'json',
        data: {
            'group': group,
            'owner': owner,
            'remind': remind
        },
        url: BASE_URL + '/group/joingroup',

        async: false,
        beforeSend: function() {
            $('#remgroupow a')
                .append(' <i class="fa fa-spinner fa-spin"> </i>').css(
                    'cursor', 'default').removeAttr("onclick");
        },

        complete: function() {

        },        
        success: function(response) {
            $("#requesttojoin").hide();
            $("#remgroupow").hide();
            $("#requesttojoin-message").show();
            $("#request-group-message").html(
                'YOUR REQUEST IS PENDING APPROVAL BY THE GROUP OWNER');
            $('#remgroupow .fa-spin').remove();
            
            callsocket();

        },

        error: function(error) {

        }

    });

}

// FUNCTION TO SEARCH USERS TO INVITE TO GROUP
var contentres;

groupsearch = function(thisEl, keywordfield) {
    var thisElement = thisEl;
    var data = '';
    $('#allrev').hide();

    var keyword = $.trim(keywordfield);
    var keywordlength = keyword.length;
    if (keywordlength < 3) {
        // $messageError("Please enter at least three characters for search");
        $dbConfirm({
            content: "Please enter at least three characters for search",
            yes: false,
            error: true
        });
        return false;
    }

    var from = $('#from').val();
    var group = $('#groupid').val();
    $.ajax({
        type: "POST",
        url: BASE_URL + "/group/invitefollowing",
        data: {
            "searchuser": 1,
            "keyword": keyword,
            "from": from,
            "group": group,
            "searchval": 'searchval'
        },
        success: function(result) {
            var resultArr = result.split('~#~');
            if (resultArr[0] != '0') {
                $('#search-invite-list').show().find('.formField').html(
                    resultArr[0]);
                $.dbeePopup('resize');
                $('#total-search').val(resultArr[1]);
                $('#search-list .formRow').removeClass('singleRow');
            }
        }
    });

}


function searchuserstoinvite() {
    $('#allrev').hide();

    var keyword = $.trim($('#groupkeywords').val());
    var keywordlength = keyword.length;
     if (keywordlength < 1) {
        return false;
    }
    /*if (keywordlength < 3) {
		// $messageError("Please enter at least three characters for search");
		$dbConfirm({
			content : "Please enter at least three characters for search",
			yes : false,
			error : true
		});
		return false;
	}*/

    var from = $('#from').val();
    var group = $('#groupid').val();
    $.ajax({
        type: "POST",
        url: BASE_URL + "/group/invitefollowing",
        data: {
            "searchuser": 1,
            "keyword": keyword,
            "from": from,
            "group": group,
            "searchval": 'searchval'
        },
        beforeSend: function() {
            $('#searchGroupUsers .searchIcon2').removeClass('fa-spin fa-search').addClass('fa-spin fa-spinner');
        },
        success: function(result) {

            var resultArr = result.split('~#~');
            if (resultArr[0] != '0') {
                $('#searchGroupUsers .searchIcon2').removeClass('fa-spin fa-spinner').addClass('fa-search');
                $('#search-invite-list').show().find('.formField').html(resultArr[0]);
                $.dbeePopup('resize');
                $('#total-search').val(resultArr[1]);
                $('#search-list .formRow').removeClass('singleRow');
            }
        }
    });
}

// FUNCTION TO SEE GROUP DETAILS ON MAIN GROUP PAGE
function seegroupdetails(group, owner, joinlink, dbpage) {
    dbpage = typeof(dbpage) != 'undefined' ? dbpage : '0';
    $.ajax({
        type: "POST",
        url: BASE_URL + "/group/agroupdetails",
        data: {
            "group": group,
            "owner": owner,
            "joinlink": joinlink,
            "dbpage": dbpage
        },
        success: function(result) {
            var resultArr = result.split('~#~');
            $('#group-highlighted').html(resultArr[0]);
            Shadowbox.init();
            Shadowbox.setup();
        },
        error: function(error) {

        }
    });
}

function proceedcreategroup() {
    err = false;
    if (!err) {
        $('#creategroup-step3').hide();
        $('#invite-group-members').show();
        $('.processGroupBtnNext1').hide();
        $('.processGroupBtnNext2').hide();
        $('.processGroupBtnOk').show();
        $('.titlePop').text('Invite people to your Group');
        $.dbeePopup('resize');


    }
}

function proceedcreategroup2() {
    err = false;
    groupbg = $('#groupbg').val();
    if (groupbg == 0) {
        $('#creategroup-step2').hide();
        $('.processGroupBtnNext1').hide();
        $('#creategroup-step3').hide();
        $('#invite-group-members').show();
        $('.processGroupBtnNext1').hide();
        $('.processGroupBtnNext2').hide();
        $('.processGroupBtnOk').show();
        $.dbeePopup('resize');
        return false;
    }
    if (!err) {
        $('#creategroup-step2').hide();
        $('#creategroup-step3').show();
        $('.processGroupBtnNext1').hide();
        $('.processGroupBtnNext2').show();
        $('.titlePop').text('Add a Group background (optional)');
        $.dbeePopup('resize');
        var myDropzoneGroup = new Dropzone("#uploadBgDropzone", {
            url: BASE_URL + "/picture/groupimageupload",
            maxFiles: 1,
            maxFilesize: 5,
            addRemoveLinks: true,
            parallelUploads: 1,
            acceptedFiles: '.png, .jpg, .jpeg, .gif, .JPG, .JPEG, .PNG, .GIF'
        });
        var fileList;
        myDropzoneGroup.on("maxfilesexceeded", function(file, serverFileName) {
            $dbConfirm({
                content: 'You can upload only 1 file',
                yes: false,
                error: true
            });
        });

        myDropzoneGroup.on("error", function(file, serverFileName) {
            $dbConfirm({
                content: serverFileName,
                yes: false,
                error: true
            });
        });

        myDropzoneGroup
            .on(
                "success",
                function(file, serverFileName) {
                    /*
                    if (file.width < 200) {
                        myDropzoneGroup.removeFile(file);
                        $dbConfirm({
                            content: 'Image width is too small.Smallest Image width should be: 200px.',
                            yes: false,
                            error: true
                        });
                    }
                    */
                    fileList = "serverFileName = " + serverFileName;
                    $('#GroupBackgroundPix').val(serverFileName);
                    $.dbeePopup('resize');
                });
        myDropzoneGroup.on("removedfile", function(file) {
            createrandontoken();
            data += fileList + '&' + userdetails;
            $.ajax({
                url: BASE_URL + "/picture/groupimageunlink",
                type: "POST",
                dataType: "json",
                data: data,
                success: function() {
                    $('#GroupBackgroundPix').val('');
                    $.dbeePopup('resize');
                }
            });

        });

    }
}

// FUNCTION TO DELETE GROUP
function deletegroup(group) {
    $.ajax({
            type: "POST",
            url: BASE_URL + "/group/groupdelete",
            data: {
                "group": group
            },
            success: function(result) {
                var resultArr = result.split('~#~');
                if (resultArr[0] != '0') {
                    $('#delete-group-wrapper').hide();
                    $('#delete-group-message').show();
                    $('#mygroup-row-' + resultArr[1]).remove();
                    $.dbeePopup('close');
                   //$('#mygroup-row-'+group).remove();                               
                     if ( $('#my-dbees li').length == 0) {
                        $('.postListing')
                             .append('<div align="center" class="noFound">You are not a member of any Groups</div>');
                         }

                }
            }
        });

}

$(function() {
    // group page
    $('body').on('click', '#rightListing .clseSideBr', function() {

        $(this).closest('div').css('display', 'none');
    });

    $('body').on('click', '.gcbygtm', function(event) {
        event.stopPropagation();
    });
    $('body').on('click', '.group-feed', function(event) {
        var url = $(this).attr('data-url');
        window.location.href = url;
    });
    // group page
    $('body').on(
            'click',
            '.deletegroup',
            function() {
                var group = $(this).attr('group_id');
                var groupname = $(this).attr('groupname');
               
    var popup_content = 'Are you sure you want to delete<br /><span style="color:#faa80b">'+groupname+'</span> group?\
                <br />This will delete all posts within it.';

                 $dbConfirm({
                            content: popup_content,
                            yesClick: function() {
                           deletegroup(group);
                       }
                     });

            });
    $('body').on('click','.editgroup', function(event) {

                var groupEditTemplate = '<div id="content_data" ><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>\
				 <div class="clearfix"></div>';
                $
                    .dbeePopup(
                        groupEditTemplate, {
                            otherBtn: '<div class="processGroupBtn">\
														<a href="javascript:void(0)" class="btn btn-yellow pull-right" onclick="javascript:editgroup();">Update</a>\
													</div>\
											'
                        });

                var group = $(this).attr('group_id');
                $.ajax({
                    type: "POST",
                    data: {
                        'group': group
                    },
                    url: BASE_URL + '/group/editgroup',                   
                    beforeSend: function() {

                    },

                    complete: function() {

                    },                    

                    success: function(response) {

                        $("#content_data").html(response);
                         $.dbeePopup('resize');

                    },

                    error: function(error) {

                        $("#content_data").html(
                            "Some problems have occured. Please try again later: " + error);

                    }

                });

            });
    $('body').on('click', '.groupmembers', function() {

        var group = $(this).attr('group_id');
        createrandontoken(); // creting user session and
        // token for request pass
        data = 'group=' + group + '&' + userdetails;
        var GmemberTemplate = '<div id="content_data"><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>\
			<div class="clearfix"></div>';

        $.dbeePopup(GmemberTemplate, {
            otherBtn: '<div class="processGroupBtnOk" style="display:none">\
							<a href="javascript:void(0)" class="btn btn-yellow pull-right" onclick="javascript:selectuserstoinvite();">OK</a>\
						</div><div class="processGroupBtnSendInvite" style="display:none">\
							<a href="javascript:void(0)" class="btn btn-yellow pull-right" onclick="javascript:sendgroupinvite(' + group + ');">Send invite</a>\
							<a href="javascript:void(0);" class="btn pull-right" onClick="backtoselection();" style="margin-right:5px;">back to selection</a>\
						</div>\
							'
        });
        $.ajax({
            type: "POST",
            data: data,
            dataType: 'json',
            url: BASE_URL + '/group/groupmembers',            
            success: function(response) {
                 var i = 0;
                var cnt =  $("#content_data");

               cnt.append('<div class="tempID" style="display:none;">'+response.content+'</div>');
              var imgCount =   $("img", cnt).size();
              if(imgCount>0){
                $("img", cnt).load(function() {
                    i  += 1;
                    if(imgCount==i){
                        cnt.html(response.content);
                        $('.tempID', cnt).remove();
                        $.dbeePopup('resize');
                    }
                });
            }else{
                $('.tempID', cnt).remove();
                 cnt.html(response.content);
            }
              

            },
            error: function(error) {

                $("#content_data").html(
                    "Some problems have occured. Please try again later: " + error);

            }

        });

        $('body').on('click', '.delmem', function() {   
        	var group = $(this).attr('group_id');
            var user = $(this).attr('userid');
            var name = $(this).attr('name');

        	var content = "Are you sure you want to remove <span style='color:#faa80b'> "+name+"</span> from this group?" ;
        	var e1 = $(this);
        	 $dbConfirm({
                 content: content,
                 yesClick: function() {
                	 $.ajax({
                	        type: "POST",
                	        url: BASE_URL + "/group/removegroupmember",
                	        data: {
                	            "group": group,
                	            "user": user
                	        },
                	        success: function(result) {                                          	          
                	           e1.closest('.group_mem_box').remove();
                               loadmygroups(result.content,0,0);
                              
                                if($('.delmem').length==0){
                                   $('#content_data .noFound').remove();
                                   $('#content_data').append('<div  class="noFound">This group has no members.</div>'); 
                                }

                	        }
                	    });
                 }
             });
        
    });


    });
   $('body').on('click', '.deletefromgrpmem', function() {   
            var group = $(this).attr('group_id');
            var user = $(this).attr('user');
            var name = $(this).attr('name');

           // var content = "Are you sure you want to remove <span style='color:#faa80b'> "+name+"</span> from this group?" ;
            var content = "Are you sure you want to remove yourself from this Group?" ;
           
           var e1 = $(this);
            $dbConfirm({
                 content: content,
                 yesClick: function() {
                     $.ajax({
                            type: "POST",
                            url: BASE_URL + "/group/removegroupmember",
                            data: {
                                "group": group,
                                "user": user
                            },
                            success: function(result) {                                                       
                               $('#mygroup-row-'+group).remove();                                                              
                                if ( $('#my-dbees li').length == 0) {
                                    $('.postListing')
                                        .append('<div align="center" class="noFound">You are not a member of any Groups</div>');
                                }

                            }
                        });
                 }
             });
        
    });   
    
});



// FILL REMOVE MEMBER CONFIRMATION POPOUP
function fillremovememberpopup(group, user) {
    document.getElementById('removemember-popup').innerHTML = 'Remove member from group?<p align="center"><a href="javascript:void(0);" onclick="javascript:removegroupmember(' + group + ',' + user + ')">Yes</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="javascript:closepopup(\'fade\');">Cancel</a></p>';
}

function trim_last(str_val) {
    var l;
    l = str_val.length;
    str_val = str_val.substr(0, l - 1);
    return str_val;
}
var InviteToGroupHttp;

function sendgroupinvite(groupid) {
    $('.processGroupBtnSendInvite a').append(
            ' <i class="fa fa-spinner fa-spin"> </i>').css('cursor', 'default')
        .removeAttr("onclick");
    from = typeof(from) != 'undefined' ? from : '0';
    var users = document.getElementById('total-users-toinvite').value;

    InviteToGroupHttp = Browser_Check(InviteToGroupHttp);
    var url = BASE_URL + "/group/sendgroupinvite";
    var data = "group=" + groupid + "&users=" + users + "&from=" + from;
    InviteToGroupHttp.open("POST", url, true);
    InviteToGroupHttp.setRequestHeader("Content-type",
        "application/x-www-form-urlencoded");
    InviteToGroupHttp.setRequestHeader("Content-length", data.length);
    InviteToGroupHttp.setRequestHeader("Connection", "close");
    InviteToGroupHttp.onreadystatechange = sendgroupinviteresult;
    InviteToGroupHttp.send(data);
    $('.processGroupBtnSendInvite').hide();
}

function sendgroupinviteresult() {
    if (InviteToGroupHttp.readyState == 4) {
        if (InviteToGroupHttp.status == 200 || InviteToGroupHttp.status == 0) {
            setTimeout(function() {
                $('.processGroupBtnSendInvite a').attr("onclick",
                    "javascript:sendgroupinvite();");
                $('.processGroupBtnSendInvite .fa-spin').remove();
                $('.processGroupBtnSendInvite a').css('cursor', 'pointer');
            }, 2000);

            var totalfollowers = $('#total-followers').val();
            for (i = 1; i <= totalfollowers; i++) {
                if (document.getElementById('inviteuser-followers' + i).checked == true) {
                    document.getElementById('inviteuser-followers' + i).checked = false;
                }
            }
            var totalfollowing = $('#total-following').val();
            for (i = 1; i <= totalfollowing; i++) {
                if (document.getElementById('inviteuser-following' + i).checked == true) {
                    document.getElementById('inviteuser-following' + i).checked = false;
                }
            }
            var totalsearch = $('#total-search').val();
            for (i = 1; i <= totalsearch; i++) {
                if (document.getElementById('inviteuser-search' + i).checked == true) {
                    document.getElementById('inviteuser-search' + i).checked = false;
                }
            }
            var result = InviteToGroupHttp.responseText;
            // $messageSuccess('Invitation sent successfully');
            $('.closePostPop').trigger('click');
            // alert(result);return false;
            $('#invite-selected-div').hide();
            $('#invite-message').show();
            $('#invite-message').show();
            callsocket();
        } 
    }
}

// OPEN WHY I NEED TO PROVIDE BIRTHDAY POPUP
function openpostdb(group) {
    group = typeof(group) != 'undefined' ? group : '0';
    OpenShadowbox('/group/insertdb/groupval/' + group, '', '420', '1000');
}

// FUNCTION TO OPEN AND RESIZE GROUPS TOP SUBMENU MENU LINKS WHEN OPENED FROM
// WITHIN POPUP
function refreshSearchgroups() {
    RefreshShadowbox('/group/searchgroups', '', '125', '640');
}

function refreshCreategroup() {
    RefreshShadowbox('/group/creategroup', '', '325', '600');
}

function refreshGroupnotifications() {
    RefreshShadowbox('/group/notifications', '', '320', '600');
}

function FacebookInviteFriendsgroup(r) {

    $(document)
        .ready(
            function() {

                FB
                    .login(
                        function(response) {

                            var access_token = FB
                                .getAuthResponse()['accessToken'];

                            if (access_token) {

                                FB
                                    .api(
                                        '/me',
                                        function(
                                            response) {

                                            var query = FB.Data
                                                .query(
                                                    'select first_name , last_name, email, name, birthday, hometown_location, current_location ,political, religion, sex, pic_big  from user where uid={0}',
                                                    response.id);

                                            query
                                                .wait(function(
                                                    rows) {

                                                    $
                                                        .ajax({
                                                            type: "POST",
                                                            dataType: 'json',
                                                            url: BASE_URL + "/dbeedetail/facebooktoken",
                                                            data: {
                                                                "access_token": access_token
                                                            },
                                                            success: function(
                                                                result) {
                                                                showFacebookFriendsgroup();
                                                            }
                                                        });

                                                });
                                        });

                            }

                        }, {
                            scope: 'read_stream,publish_stream,offline_access,email,user_birthday,publish_actions'
                        });
            });
}

function showFacebookFriendsgroup() {
    var content = '';
    FB
        .api(
            '/me/friends',
            function(response) {
                content += "<input type='hidden' name='logined' value='logined' id='logined'>\
			<div class='srcUsrWrapper'>\
				<div class='fa fa-search fa-lg searchIcon2'></div>\
				<input type='text' id='facebookfriendlist' class='findsocialfriend' value='' placeholder='filter social friends' onkeyup='javascript:filtersocailuser()' socialFriendlist='true'>\
	        	<div id='Usercountfilter' class='Usercountfilter srcUsrtotal' Usercountfilter='true'></div>\
        	</div>";
                for (var i = 0; i < response.data.length; i++) {

                    content += "<div class='facebookfriendlist boxFlowers' title='" + response.data[i].name + "' socialFrienduser='true'>\
			 <input type='hidden' name='userid' value='" + response.data[i].user + "' id='userid'>\
            <label class='labelCheckbox'><input type='checkbox' value='" + response.data[i].id + "_" + response.data[i].name + "' class='inviteuser-search' name='FacebookInvite'>\
<div class='follower-box'>\
            <img  class=img border height=30 align='left' src='https://graph.facebook.com/" + response.data[i].id + "/picture'>\
            <br>" + response.data[i].name + "</div></label>\
            </div></div>";
                }
                $("#socalnetworking-list").html(content);
                $("#allrev").show();
                $(".facebookfriendlist").equalizeHeights();
                setTimeout(function() {
                    $.dbeePopup('resize');
                }, 500);
            });
}

function TwitterInviteFriendsgroup() {
    $.ajax({
        type: "POST",
        dataType: 'json',
        url: BASE_URL + '/social/twitteruser',

        cache: false,
        beforeSend: function() {
            $dbLoader('#socalnetworking-list', 1, '', 'center');
        },
        success: function(response) {

            $dbLoader('#socalnetworking-list', 1, 'close');
            $("#socalnetworking-list").html(response.content);
            $("#allrev").show();
            //$(".userFatchList").equalizeHeights();
            setTimeout(function() {
                $.dbeePopup('resize');
            }, 500);

        },
        error: function(error) {

            $("#content_data").html(
                "Some problems have occured. Please try again later: " + error);

        }

    });

}

$(function() {
    $('body')
        .on(
            'click',
            '#acceptGroupRequest',
            function() {
                $
                    .ajax({
                        type: "POST",
                        dataType: 'json',
                        data: {
                            'acceptGroupRequest': true
                        },
                        url: BASE_URL + '/group/acceptgroupsocialrequest',

                        async: false,
                        beforeSend: function() {},
                        complete: function() {},
                        cache: false,
                        success: function(response) {
                            if (response.status == 'success') {
                                window.location.href = response.redirect;
                            } else {
                                var usedPopup = '<div style="margin-top:5px; text-align:center" >This link is no longer active. Seems like you have already taken the required action.</div>';

                                $messageWarning(usedPopup);
                            }

                        },
                        error: function(error) {
                            $("#content_data").html(
                                "Some problems have occured. Please try again later: " + error);
                        }
                    });
            });

    $('body')
        .on(
            'click',
            '#rejectGroupRequest',
            function() {
                $
                    .ajax({
                        type: "POST",
                        dataType: 'json',
                        data: {
                            'rejectGroupRequest': true
                        },
                        url: BASE_URL + '/group/rejectgroupsocialrequest',

                        async: false,
                        beforeSend: function() {},
                        complete: function() {},
                        cache: false,
                        success: function(response) {
                            if (response.status == 'success') {
                                window.location.href = response.redirect;
                            } else {
                                var usedPopup = '<div style="margin-top:5px; text-align:center" >This link is no longer active. Seems like you have already taken the required action.</div>';

                                $messageWarning(usedPopup);

                            }

                        },
                        error: function(error) {
                            $("#content_data").html(
                                "Some problems have occured. Please try again later: " + error);
                        }
                    });
            });

    $('body').on('click', '#invitetabs', function() {
        $('#allrev').hide();
		var offset = '0';
    });

});

function linkedinUserGroup() {
    $.ajax({
        type: "POST",
        dataType: 'json',
        url: BASE_URL + '/social/linkedinuser',

        async: false,
        beforeSend: function() {},

        complete: function() {

        },

        cache: false,

        success: function(response) {
            $("#socalnetworking-list").html(response.content);

            $(".processGroupBtnOk").show();

            //(".userFatchList").equalizeHeights();

            setTimeout(function() {
                $.dbeePopup('resize');
            }, 300);

        },

        error: function(error) {}

    });
}
 
function selectuserstoTwitterprofilegroup(groupid) 
{
    var userInfo = [];
    var checkedList = $('#PrivatePost').val();
    $('input:checkbox[name=twitter]').each(function() {
        if($(this).is(':checked'))
            userInfo.push($(this).val());

    });
    
    if (userInfo == '') 
    {
        $dbConfirm({
            content: 'Please select a user',
            yes: false,
            error: true
        });
        $('.fa-spin').remove();
        return false;
    }

    $('.SendMessageTwitterprofile').append('<i class="fa fa-spinner fa-spin"> </i>').css('cursor', 'default');
    $('.SendMessageTwitterprofile').removeAttr('onclick');

    var stringuserInfo = userInfo.join();
    var social_type = 'Twitter';
    if(checkedList==0){
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: BASE_URL + "/social/invite",
                data: {
                    'userid': stringuserInfo,
                    'groupid': groupid,
                    'social_type': social_type,
                    'checkedList':checkedList
                },
                success: function(result) {
                    $('.closePostPop').trigger('click');
                   /* $dbConfirm({
                        content: 'Group invitation sent',
                        yes: false
                    });*/
                    $.dbeePopup('close');

                }
            });
        }else
        {
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: BASE_URL + "/social/precheckinvite",
                data: {
                    'userid': stringuserInfo,
                    'social_type': social_type,
                    'checkedList':checkedList
                },
                success: function(response) {
                    $('.closePostPop').trigger('click');
                     $.dbeePopup('close');
                    if(response.status==false)
                    {
                        setTimeout(
                            function() {
                                 $.dbeePopup(response.content, {
                            overlay: true,
                            otherBtn: '<a href="javascript:void(0);" groupid="'+groupid+'" data-list="'+stringuserInfo+'" social-type="'+social_type+'" class="pull-right btn btn-yellow continueInvite" >Continue</a>'
                            });
                        }, 500);
                       
                    }else{
                        sendtosocialpost(stringuserInfo,groupid,social_type);
                    }
                }
            });
        }
}

$(document).ready(function() {

    $('body').on('click', '.SendMessageProfileLinkedingroup', function() {
        var userInfo = [];
        var checkedList = $('#PrivatePost').val();
        $('input:checkbox[name=linkedin]').each(function() {
            if ($(this).is(':checked'))
                userInfo.push($(this).val());
        });
        if (userInfo == '') {
            $dbConfirm({
                content: 'Please select a user',
                yes: false,
                error: true
            });
            return false;
        }
        $(this).append('<i class="fa fa-spinner fa-spin"> </i>').css('cursor', 'default');
        var stringuserInfo = userInfo.join();
        var social_type = 'Linkedin';
        var dbid = $('#dbid').val();
        var groupid = $(this).attr('group-id');
        if(checkedList==0){
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: BASE_URL + "/social/invite",
                data: {
                    'userid': stringuserInfo,
                    'groupid': groupid,
                    'social_type': social_type,
                    'checkedList':checkedList
                },
                success: function(result) {
                    $('.closePostPop').trigger('click');
                   /* $dbConfirm({
                        content: 'Group invitation sent',
                        yes: false
                    });*/
                    $.dbeePopup('close');

                }
            });
        }else
        {
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: BASE_URL + "/social/precheckinvite",
                data: {
                    'userid': stringuserInfo,
                    'social_type': social_type,
                    'checkedList':checkedList
                },
                success: function(response) {
                    $('.closePostPop').trigger('click');
                     $.dbeePopup('close');
                    if(response.status==false)
                    {
                        setTimeout(
                            function() {
                                 $.dbeePopup(response.content, {
                            overlay: true,
                            otherBtn: '<a href="javascript:void(0);" groupid="'+groupid+'" data-list="'+stringuserInfo+'" social-type="'+social_type+'" class="pull-right btn btn-yellow continueInvite" >Continue</a>'
                            });
                        }, 500);
                       
                    }else{
                        sendtosocialpost(stringuserInfo,groupid,social_type);
                    }
                }
            });
        }

    });

});



$(document).ready(function() {
    $('body').on('click', '#invite-group-members input[type="checkbox"]', function(event) {
        var k = $('#invite-group-members input[type="checkbox"]:checked').size();
        if (k > 0) {
            $('#nextTogo').removeClass('disabled').attr('onclick', 'javascript:selectuserstoinvite()');
        } else {
            $('#nextTogo').addClass('disabled').removeAttr('onclick');
        }
    });
  
     $('body').on('change', '#selectgropns', function(event) {
        var k = $('#selectgropns').val();
        if (k > 0) {
            $('#nextTogo').removeClass('disabled').attr('onclick', 'javascript:selectuserstoinvite()');
        } else {
            $('#nextTogo').addClass('disabled').removeAttr('onclick');
        }
    });


    $('body').on('click', '.SendPostToFacebookgroup', function() {
        var userInfo = [];
        var checkedList = $('#PrivatePost').val();
        $('input:checkbox[name=FacebookInvite]').each(function() {
            if ($(this).is(':checked'))
                userInfo.push($(this).val());
        });
        $(this).append(' <i class="fa fa-spinner fa-spin"> </i>');
        if (userInfo == '') {
            $dbConfirm({
                content: 'Please select a user',
                yes: false,
                error: true
            });
            $('.spin').remove();
            return false;
        }
        $(this).removeClass('SendMessageProfileFacebook');
        var stringuserInfo = userInfo.join();
        var groupid = $(this).attr('groupidfacebook');
        var social_type = 'Facebook';
        if(checkedList==0){
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: BASE_URL + "/social/invite",
                data: {
                    'userid': stringuserInfo,
                    'groupid': groupid,
                    'social_type': social_type,
                    'checkedList':checkedList
                },
                success: function(result) {
                    $('.closePostPop').trigger('click');
                    /*$dbConfirm({
                        content: 'Group invitation sent',
                        yes: false
                    });*/
                    $.dbeePopup('close');

                }
            });
        }else
        {
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: BASE_URL + "/social/precheckinvite",
                data: {
                    'userid': stringuserInfo,
                    'social_type': social_type,
                    'checkedList':checkedList
                },
                success: function(response) {
                    $('.closePostPop').trigger('click');
                     $.dbeePopup('close');
                    if(response.status==false)
                    {
                        setTimeout(
                            function() {
                                 $.dbeePopup(response.content, {
                            overlay: true,
                            otherBtn: '<a href="javascript:void(0);" groupid="'+groupid+'" data-list="'+stringuserInfo+'" social-type="'+social_type+'" class="pull-right btn btn-yellow continueInvite" >Continue</a>'
                            });
                        }, 500);
                       
                    }else{
                        sendtosocialpost(stringuserInfo,groupid,social_type);
                    }
                }
            });
        }
    });


    $('body').on('click', '.continueInvite', function() 
    {
        var group = $(this).attr('groupid');
        var datalist = $(this).attr('data-list');
        var social_type = $(this).attr('social-type');
        sendtosocialpost(datalist,group,social_type);
    });

    $('body').on('click', '.groupmembers_invite', function() {
                var group = $(this).attr('group_id');
                createrandontoken(); // creting user
                // session and
                // token for
                // request pass
                var data = 'group=' + group + '&' + userdetails;;
                var GmemberTemplate = '<div id="content_data" ><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>\
																<div class="clearfix"></div>';
                $.dbeePopup(GmemberTemplate, {
                        otherBtn: '<div class="processGroupBtnOk" style="display:none">\
                        <a href="javascript:void(0)" class="btn btn-yellow pull-right" onclick="javascript:selectuserstoinvite();">OK</a>\
                        </div>\
                        <div class="processGroupBtnSendInvite" style="display:none">\
                        <a href="javascript:void(0)" class="btn btn-yellow pull-right" onclick="javascript:sendgroupinvite(' + group + ');">Send invite</a>\
                        </div>\
																		'
                });

                $.ajax({
                    type: "POST",
                    data: data,
                    url: BASE_URL + '/group/inviteuserstogroup',
                    async: false,
                    beforeSend: function() {},
                    complete: function() {},
                    cache: false,
                    success: function(response) {
                        $("#content_data")
                            .html(response);
                        $('.processGroupBtnOk')
                            .show();
                        setTimeout(
                            function() {
                                $.dbeePopup('resize');
                            }, 500);
                    },
                    error: function(error) {
                        $("#content_data")
                            .html(
                                "Some problems have occured. Please try again later: " + error);
                    }
                });

            });

});

function sendtosocialpost(datalist,groupid,social_type)
{
    $.ajax({
            type: "POST",
            dataType: 'json',
            url: BASE_URL + "/social/lockinvite",
            data: {
                'userid': datalist,
                'groupid': groupid,
                'social_type': social_type
            },
            success: function(result) {
                $('.closePostPop').trigger('click');
                $.dbeePopup('close');
            }
        });
}