var broadcastingMsg='';
// ******************************************************************************************************************************
// PRELOAD ROLLOVER IMAGES   
var isFollowNotification = false;

var myimages = new Array()
var dbscrolevent = true;
function preloadimages() {
        for (i = 0; i < preloadimages.arguments.length; i++) {
            myimages[i] = new Image()
            myimages[i].src = preloadimages.arguments[i]
        }
    }
    //Enter path of images to be preloaded inside parenthesis. Extend list as desired.
    // ******************************************************************************************************************************
function dbeeInactive()
{
    socket.emit('dbeeInactive', true,clientID);
}

function socialFeedlayout(dataType,qt)
{
    //$('#sideBarFeeds').addClass('activeSdFeeds');
    $('.socialfeedWidget').removeClass('notOpenFeed');

    if (dataType == 'my feed') 
    { 
        iamLoginNow();
        showPlatformfeed();
    }
    if ($('.socialfeedWidget a:visible').hasClass('active') == true) {
        $('.socialfeedWidget a:visible.active, .closeNavations').trigger('click');
    }else{
        $('.socialfeedWidget a:visible:first, .closeNavations').trigger('click');
    }
    if(qt==4){
        $('.sclIconsSide FacebookVisitor').trigger('click');
    }
    if(qt==5){
        $('.sclIconsSide TwitterVisitor').trigger('click');
    }
}
function facebookLogin() 
{
    $.dbeePopup('close');
    setTimeout(function() {
        var reportTemplate = 'Oops there seems to be a problem fetching your Facebook data, please try linking your account again by clicking on the icon below<div  id="content_data_button" class="SocialfeedConnect" style="padding:0 0 20px; overflow:hidden;"><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>';
        $.dbeePopup(reportTemplate, {
            width: 400,
            overlayClick: false,
            otherBtn: ''
        });
        $('html').removeClass('activeSocialfeedSidebar');
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {
                'id': 1
            },
            url: BASE_URL + '/settings/socialconnectfacebook',
            async: false,
            success: function(response) {
                $("#content_data_button").html(response.content);
                $.dbeePopup('resize');
            }
        });
    }, 500);
    return false;
}

function loadError(error) {
    console.log(error);
    $.ajax({
        type: "POST",
        dataType: 'json',
        data: {
            'status': error.status,
            'responseText': error.responseText
        },
        url: BASE_URL + '/myhome/logmaintain',
        async: false, // Dont remove this neccessary for returning value of respnce
        success: function(response) {
            console.log(response);
        },
    });
}
function getpromotedExpert() 
{
    $.ajax({
        type: "POST",
        dataType: 'json',
        url: BASE_URL + '/ajax/getpromotedexpert',
        success: function(response) 
        {
            $('#rightListing').prepend(response.html);
        },
    });
}

function loadComment(commentid,type,dbeeid) 
{
    lastID = $('.comment-list').last().attr('data-cid');
    busy = false;
    //console.log(busy);
    order = $('.commentSorting').val();
    if(type=='push' && order == 'ASC'){
        commentid = '';
        order = 'DESC';
        document.getElementById('sort-latest').className = 'sort-active';
        document.getElementById('sort-oldest').className = 'sort-nonactive';
        $('.commentSorting').val(order);
    }
    
    $.ajax({
        type: "POST",
        dataType: 'json',
        async:false,
        data: {'type': type,'dbeeid': dbeeid,'commentid':commentid,'lastID':lastID,'order':order},
        url: BASE_URL + '/ajax/loadcommentrow',
        beforeSend: function() {
            $('#more-feeds-loader').remove();
            //$(".comment-feed2").append('<div id="more-feeds-loader" style="cursor:pointer; color:#333333; text-align:center;" >loading..</div>');
        },
        cache: false,
        success: function(response) 
        {
           // alert(response.html.isprivate);
            $('#more-feeds-loader').remove(); 
           /* if(response.html.isprivate=='1'){
                window.parent.location= BASE_URL+'/myhome'
            }*/
            if(response.html.sortAndTweeterfeedWrapper==false)
            {
                $('.sortAndTweeterfeedWrapper').remove();
            }
            if(type=='push' && order == 'DESC')
            { 
                if(response.html.sortAndTweeterfeedWrapper==false)
                {
                    $('.surveyComplete').remove();
                }
                if(typeof lastID == 'undefined')
                {
                    $('.noFound').remove();
                }
                $('#comment-block-'+commentid).remove();
                $(".comment-feed2").prepend(response.html.commentlist);
            }
            else if(type=='scroll')
            { 
                if(response.html.counts==0)
                    $('.noMoreComments').remove();
                if(typeof lastID != 'undefined')
                    $('.comment-feed2').append(response.html.commentlist);
            }else {
                $('.comment-feed2').html(response.html.commentlist);
            }

            if(response.html.timeinsecond!='' && response.html.Type==20)
            {
              clockExpert =  $('.countDown').FlipClock(response.html.timeinsecond, {
                  clockFace: 'DailyCounter',
                  countdown: true
                });

               
            }
            //alert(response.html.getmention);
            $('#mentionusers').val(response.html.getmention);
            $('#dbeeCommentCount'+dbeeid).val(response.html.TotalCount);
            busy = true;

        },
    });
}

function sortcomments(sortBy) 
{
    dbeeid = $('#dbid').val();
    if (sortBy == 'ASC') {
        document.getElementById('sort-latest').className = 'sort-nonactive';
        document.getElementById('sort-oldest').className = 'sort-active';
        $('.commentSorting').val(sortBy);
    } else if (sortBy == 'DESC') {
        document.getElementById('sort-latest').className = 'sort-active';
        document.getElementById('sort-oldest').className = 'sort-nonactive';
        $('.commentSorting').val(sortBy);
    }
    $.ajax({
        type: "POST",
        dataType: 'json',
        data: {'type': 'loadfirst','dbeeid': dbeeid,'order':sortBy},
        url: BASE_URL + '/ajax/loadcommentrow',
        success: function(response) 
        {
            $('.comment-feed2').html(response.html.commentlist);
            $('#mentionusers').val(response.html.getmention);
            //getMentionUser(dbeeid);
        },
    });
}
var userdetailsLogon = '';

function createrandontokenLogin(callback) {
    capcha = $('#captcha').val();
    data = 'captchakey=' + capcha;
    $.ajax({
        type: "POST",
        dataType: 'json',
        data: data,
        url: BASE_URL + '/index/createrandontoken',
        async: false, // Dont remove this neccessary for returning value of respnce
        success: function(response) {
            if (response.errormsg != 404) {
                // $messageError(response.errormsg);
                $dbConfirm({
                    content: response.errormsg,
                    yes: false,
                    error: true
                });
                return false;
            } else {
                userdetails = "SessUser__=" + response.SessUser__ + "&SessId__=" + response.SessId__ + "&SessName__=" + response.SessName__ + "&Token__=" + response.Token__ + "&Key__=" + response.Key__;
            }

        },
    })

}

function check_number(obj_val) {
    //Returns true if value is a number or is NULL
    //otherwise returns false	
    if (obj_val.length == 0) return true;
    //Returns true if value is a number defined as
    //   having an optional leading +.
    //   having at most 1 decimal point.chk
    //   otherwise containing only the characters 0-9.
    var start_format = " .+0123456789";
    var number_format = " .0123456789";
    var check_char;
    var decimal = false;
    var trailing_blank = false;
    var digits = false;
    //The first character can be + .  blank or a digit.
    check_char = start_format.indexOf(obj_val.charAt(0))
        //Was it a decimal?
    if (check_char == 1) decimal = true;
    else if (check_char < 1) return false;

    //Remaining characters can be only . or a digit, but only one decimal.
    for (var i = 1; i < obj_val.length; i++) {
        check_char = number_format.indexOf(obj_val.charAt(i))
        if (check_char < 0) return false;
        else if (check_char == 1) {
            if (decimal) // Second decimal.
                return false;
            else
                decimal = true;
        } else if (check_char == 0) {
            if (decimal || digits)
                trailing_blank = true;
            // ignore leading blanks
        } else if (trailing_blank) return false;
        else digits = true;
    }
    //All tests passed, so...
    return true
}

function echeck(str) {
    var at = "@"
    var dot = "."
    var lat = str.indexOf(at)
    var lstr = str.length
    var ldot = str.indexOf(dot)

    if (str.indexOf(at) == -1) {
        return false
    }
    if (str.indexOf(at) == -1 || str.indexOf(at) == 0 || str.indexOf(at) == lstr) {
        return false
    }
    if (str.indexOf(dot) == -1 || str.indexOf(dot) == 0 || str.indexOf(dot) == lstr) {
        return false
    }
    if (str.indexOf(at, (lat + 1)) != -1) {
        return false
    }
    if (str.substring(lat - 1, lat) == dot || str.substring(lat + 1, lat + 2) == dot) {
        return false
    }
    if (str.indexOf(dot, (lat + 2)) == -1) {
        return false
    }

    if (str.indexOf(" ") != -1) {
        return false
    }
    return true
}

function isSpaces(s) {
    var len = s.length;
    var i;
    for (i = 0; i < len; ++i) {
        if (s.charAt(i) == " ") return true;
    }
    return false;
}

function isBlank(s) {
    var len = s.length;
    var i;
    for (i = 0; i < len; ++i) {
        if (s.charAt(i) != " ") return false;
    }
    return true;
}

function expandtext(textArea) {
    while (
        textArea.rows > 1 &&
        textArea.scrollHeight < textArea.offsetHeight
    ) {
        textArea.rows--;
    }

    while (textArea.scrollHeight > textArea.offsetHeight) {
        textArea.rows++;
    }
    textArea.rows++
}

function fadepopup() {
    //When you click on a link with class of poplight and the href starts with a # 
    $('a.poplight[href^=#]').click(function() {

        var popID = $(this).attr('rel'); //Get Popup Name
        var popURL = $(this).attr('href'); //Get Popup href to define size

        //Pull Query & Variables from href URL
        var query = popURL.split('?');
        var dim = query[1].split('&');
        var popWidth = dim[0].split('=')[1]; //Gets the first query string value
        //Fade in the Popup and add close button
        $('#' + popID).fadeIn().css({
            'width': Number(popWidth)
        });

        //Define margin for center alignment (vertical + horizontal) - we add 80 to the height/width to accomodate for the padding + border width defined in the css
        var popMargTop = ($('#' + popID).height() + 80) / 2;
        var popMargLeft = ($('#' + popID).width() + 80) / 2;

        //Apply Margin to Popup
        $('#' + popID).css({
            'margin-top': -popMargTop,
            'margin-left': -popMargLeft
        });

        //Fade in Background
        $('body').append('<div id="fade"></div>'); //Add the fade layer to bottom of the body tag.
        $('#fade').css({
            'filter': 'alpha(opacity=80)'
        }).fadeIn(); //Fade in the fade layer 

        return false;
    });


    /*
	//Close Popups and Fade Layer
	jqnc('a.close, #fade').live('click', function() { //When clicking on the close or fade layer...
	  	jqnc('#fade , .popup_block').fadeOut(function() {
			jqnc('#fade, a.close').remove();  
	}); //fade them both out
		
		return false;
	});
*/
}

/*function openFadePopup() {
    $('a.poplight').click(function () {
        var popAttr = $(this).attr('rel'); //Get Popup Name
        var popAttrArr = popAttr.split('~');
        var popID = popAttrArr[0]; //Get Popup Name
        var popWidth = popAttrArr[1]; //Get Popup href to define size

        //Fade in the Popup and add close button
        $('#' + popID).fadeIn().css({
            'width': Number(popWidth)
        });

        //Define margin for center alignment (vertical + horizontal) - we add 80 to the height/width to accomodate for the padding + border width defined in the css
        var popMargTop = ($('#' + popID).height() + 80) / 2;
        var popMargLeft = ($('#' + popID).width() + 80) / 2;

        //Apply Margin to Popup
        $('#' + popID).css({
            'margin-top': -popMargTop,
            'margin-left': -popMargLeft
        });

        //Fade in Background
        $('body').append('<div id="fade"></div>'); //Add the fade layer to bottom of the body tag.
        $('#fade').css({
            'filter': 'alpha(opacity=80)'
        }).fadeIn(); //Fade in the fade layer 

        return false;
    });
}
*/
function limitText(text_area_id, total_limit, counter_show_id) {

    var TextAreaId = document.getElementById(text_area_id);

    if (TextAreaId.value.length > total_limit) {
        TextAreaId.value = TextAreaId.value.substring(0, total_limit);
    } else {
        if (TextAreaId.value.length != 0)
            document.getElementById(counter_show_id).innerHTML = total_limit - TextAreaId.value.length + ' limit';
        else
            document.getElementById(counter_show_id).innerHTML = total_limit - TextAreaId.value.length + ' limit';
    }
}

function getVoteOption() {
    document.forms[1].elements['pollradio'];
    var oRadio = $('input[name="pollradio"]:checked').val();
    return oRadio;

}

function toggleLoginField(id) {
    if (id == 'loginpass') {
        document.getElementById('loginpassdiv').innerHTML = '<input name="loginpass" type="password" id="loginpass" class="textfieldLogin" value="" />';
        $("#loginpass").focus(function() {
            $(this).addClass("curFocus");
        });
        $("#loginpass").blur(function() {
            $(this).removeClass("curFocus")
        });
        document.getElementById('loginpass').focus();
    }
}

function toggleLoginFieldBack(id) {
    if (id == 'loginpass') {
        document.getElementById('loginpassdiv').innerHTML = '<input name="loginpass" type="text" id="loginpass" class="textfieldLogin" value="password" />';
        document.getElementById('loginpass').focus();
    }
}

function toggleloginclass(id) {
    if (document.getElementById(id).className == 'textfieldLogin') document.getElementById(id).className = 'textfieldLoginBlack';
    else {
        if (id == 'loginemail' && document.getElementById(id).value == 'email')
            document.getElementById(id).className = 'textfieldLogin';
    }
}

function toggletwsearchclass(id) {
        if (id == 'q') var tag = '#hashtag or keyword';
        else var tag = 'add Twitter username';
        var fld = document.getElementById(id);
        if (fld.className == 'twitter-search-field-grey') fld.className = 'twitter-search-field';
        else {
            if (fld.value == tag) fld.className = 'twitter-search-field-grey';
            else fld.className = 'twitter-search-field';
        }
    }
    // OPEN WHY I NEED TO PROVIDE BIRTHDAY POPUP

function seevideo(video) {

    var PopupTemplate = '<div id="content_data" ><img src="' + BASE_URL + '/images/rsslogos/ajaxloader-big.gif" /></div>\
				 <div class="clearfix"></div>';
    $.dbeePopup(PopupTemplate);

    $.ajax({
        type: "POST",
        dataType: 'json',
        data: {
            'video': video
        },
        url: BASE_URL + '/comment/viewvideo',

        async: false,
        beforeSend: function() {

        },

        complete: function() {

        },

        cache: false,

        success: function(response) {
            $("#content_data").html(response.content);

        },

        error: function(request, status, error) {

            $("#content_data").html("Some problems have occured. Please try again later: " + error);

        }

    });

}


function seeslideshare(slideid) {
    $.dbeePopup('close');

    $("#content_data").hide();

    var PopupTemplate = '<div id="content_data" ><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>\
				 <div class="clearfix"></div>';
    setTimeout(function() {
        $.dbeePopup(PopupTemplate, {
            width: 450
        });
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {
                'slideid': slideid
            },
            url: BASE_URL + '/comment/viewslideshare',
            cache: false,
            success: function(response) {
                $("#content_data").html(response.content);
                setTimeout(function() {
                    $.dbeePopup('resize');
                }, 500);
            }
        });
    }, 500);
}


// OPEN WHY I NEED TO PROVIDE BIRTHDAY POPUP



function UpdateEmail() {

    $.ajax({
        type: "POST",
        dataType: 'json',
        data: {
            email: $('#emailids').val(),
            filename: $('#filename').val()
        },
        url: BASE_URL + '/profile/updateemail',

        async: false,
        cache: false,
        success: function(response) {
            if (response.status == 'success') {
                $('#content_data').html(response.message);
                $('.hideafter').hide();
                $('.closePostPop').html('Close');

            } else {
                //$messageError(response.message);
                $dbConfirm({
                    content: response.message,
                    yes: false,
                    error: true
                });
            }
        },
        error: function(error) {
            loadError(error);

        }

    });

}

function seeaudio(audio) {

        var PopupTemplate = '<div style="padding:20px;">\
				 <div id="content_data" ><img src="' + BASE_URL + '/images/rsslogos/ajaxloader-big.gif" /></div>\
				 <div class="clearfix"></div>\
			 </div>\
			  <div class="postTypeFooter">\
					<a href="#" class="pull-left btn closePostPop">Close</a>\
					<div class="clearfix"></div>\
				</div>';
        $.dbeePopup(PopupTemplate, {
            overlay: true
        });

        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {
                'audio': audio
            },
            url: BASE_URL + '/comment/viewaudio',

            async: false,
            beforeSend: function() {

            },

            complete: function() {

            },

            cache: false,

            success: function(response) {
                $("#content_data").html(response.content);

            },

            error: function(request, status, error) {

                $("#content_data").html("Some problems have occured. Please try again later: " + error);

            }

        });

    }
    // OPEN WHY I NEED TO PROVIDE BIRTHDAY POPUP

function opencloseaccount() {
        var r = confirm("Are you sure you want to deactivate your account?");
        if (r == true) {
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: BASE_URL + '/settings/closeaccount',

                async: false,
                beforeSend: function() {

                },

                complete: function() {

                },

                cache: false,

                success: function(response) {

                    $dbConfirm({
                        content: response.message,
                        yes: false
                    });
                },

                error: function(error) {
                    loadError(error);
                }


            });
        }
        //OpenShadowbox('/settings/closeaccount', '', '150', '400');
    }
    // OPEN WHY I NEED TO PROVIDE BIRTHDAY POPUP

function openpostdb(group) {
        group = typeof(group) != 'undefined' ? group : '0';
        OpenShadowbox('/group/insertdb/groupval/' + group, '', '420', '1000');
    }
    // OPEN WHY I NEED TO PROVIDE BIRTHDAY POPUP

function opennewcomm() {

    var commentTemplate = '<div style="padding:20px;">\
				 <div id="content_data" ><img src="' + BASE_URL + '/images/rsslogos/ajaxloader-big.gif" /></div>\
				 <div class="clearfix"></div>\
			 </div>\
			  <div class="postTypeFooter">\
					<a href="#" class="pull-left btn closePostPop">Close</a>\
					<div class="clearfix"></div>\
				</div>';
    $.dbeePopup(commentTemplate, {
        overlay: true
    });


    $.ajax({
        type: "POST",
        dataType: 'json',
        url: BASE_URL + '/myhome/newcommpop',

        async: false,
        beforeSend: function() {

        },

        complete: function() {

        },

        cache: false,

        success: function(response) {
            $("#content_data").html(response.content);

        },

        error: function(request, status, error) {

            $("#content_data").html("Some problems have occured. Please try again later: " + error);

        }

    });

    //OpenShadowbox('/myhome/newcommpop', '', '220', '700');
}

function openwhybirthday() {
    OpenShadowbox('/index/whybirthday', '', '210', '450');
}

function picUploaderfallback(thisEl, url) {
    var pic = $(thisEl).val();
    $.ajax({
        url: url,
        type: 'POST',
        contentType: 'multipart/form-data',
        success: function(data, textStatus, xhr) {
            alert(data)
        }
    });


}

function showEmailBoxs(imagestatus) {

    var profilePicTemplate = '';
    if (imagestatus == 1) {
        var profilePicTemplate = '<div class="newPicWrapper" >\
										<div class="formRow" id="pixUploader" style="margin-top:10px;">\
											<form action="/file-upload" class="dropzone" id="uploadDropzoneProfile">\
											   <div class="fallback">\
												<input type="file" name="file"  />\
											  </div>\
											</form>\
										</div>\
									 </div>';

    }

    var PopupTemplate = '<div id="content_data" ><h2 id="searchgroups">Enter your email address below to receive platform email notifications</h2><div class="clearfix"></div>\
<form name="form-search-email" id ="email" >\
    <div class="postTypeContent" id="passform">\
    <div class="formRow singleRow">\
     <div class="formField">\
         <input type="text" id="emailids" name="email" class="textfield"  value="">\
		 <input type="hidden" id="filename" class="textfield" value="" name="filename">\
         <i class="optionalText">enter your email</i>\
     </div>\
    </div>\
    </form>\
	' + profilePicTemplate + '\
</div></div>\
			 ';



    $.dbeePopup(PopupTemplate, {
        closeLabel: 'No Thanks',
        otherBtn: '<input type="hidden" name="PostPix" id="profile_picture" value="" /><label class="labelCheckbox hideafter" style="margin-left:10px; color:#CCC; vertical-align:middle;"><input type="checkbox" name="nothanx" onclick="stopEmailbox();" value="1" class="hideafter"><label class="checkbox hideafter"></label>Don\'t show me again</label>\
                                        <a href="#" class="pull-right btn btn-yellow hideafter"  onclick="javascript:UpdateEmail();" >Update</a>'
    });

    if (imagestatus == 1) {


        var profilePicFirst = new Dropzone("#uploadDropzoneProfile", {
            url: BASE_URL + "/profile/imguplod",
            maxFiles: 1,
            maxFilesize: 1,
            addRemoveLinks: true,
            parallelUploads: 1,
            acceptedFiles: 'image/jpeg,image/png,image/gif',

        });
        var fileList;

        profilePicFirst.on("maxfilesexceeded", function(file, serverFileName) {
            //$messageError('You can upload only 1 file');
            $dbConfirm({
                content: 'You can upload only 1 file',
                yes: false,
                error: true
            });
        });


        /*profilePicFirst.on("error", function(file, serverFileName) {					
		$dbConfirm({content:'Please upload only png, jpg, jpeg, gif file type', yes:false,error:true});					  	
	});*/

        profilePicFirst.on("error", function(file, serverFileName) {
            $dbConfirm({
                content: serverFileName,
                yes: false,
                error: true
            });
        });


        profilePicFirst.on("success", function(file, serverFileName) {
            if (file.width < 200) {
                profilePicFirst.removeFile(file);
                $dbConfirm({
                    content: 'Image width is too small.Smallest Image width should be: 200px.',
                    yes: false,
                    error: true
                });
            }
            fileList = "serverFileName = " + serverFileName;
            $('#filename').val(serverFileName);
        });
        profilePicFirst.on("removedfile", function(file) {
            $.ajax({
                url: BASE_URL + "/profile/imgunlink",
                type: "POST",
                data: fileList,
                success: function() {
                    $('#profile_picture').val('');
                }
            });
        });

    }

}










//start fullwidth dropzone//
/*
function fadeDropzonebox() { 

$('#fadeDropzone1').dropzone({

  url: BASE_URL + "/profile/sharefile",
                //maxFiles: 1,
                addRemoveLinks: true,
                parallelUploads: 1,
                acceptedFiles: '.png, .jpg, .jpeg, .gif, .JPG, .JPEG, .PNG, .GIF',    

                error: function(file, serverFileName) {
                    $dbConfirm({
                        content: serverFileName,
                        yes: false
                    });
                },
               

                processing: function(file, serverFileName) {                            
                    $('#fadeDropzone1 .dz-preview').hide();
                },
              
                totaluploadprogress: function(progress) {

                    if($('#mesageNotfiOverlay').is(':visible')!=true){                        
                      $dbMessageProgressBar({fetchText:'', animate:false});
                      //$('#mesageNotfiOverlay .progressBarWrp').attr('data-loaded', progress+'%');
                      $('#mesageNotfiOverlay .msgNoticontent').addClass('popupStripBar');
                 }else{
                    $('#mesageNotfiOverlay .progressBarWrp').attr('data-loaded', progress+'%');
                    $('#mesageNotfiOverlay .progressMsgBar').css({width: progress+'%'});                    
                    if(progress==100){
                        $('#mesageNotfiOverlay .loaderShow').hide();
                        $('#mesageNotfiOverlay .prCnt').html('<i class="fa fa-check-circle-o tickMark"></i> Successfully uploaded files');                        
                        $('#mesageNotfiOverlay .msgNoticontent').append('<form action="/file-upload" class="dropzone" id="nxtDropzone"><div class="shdBtnpad"><span class="gobackBtn btn btn-yellow">Upload Another</span></div><div class="fallback"><input name="file" type="file"/></div></form>');
                        $('#mesageNotfiOverlay .msgNoticontent #nxtDropzone').append('<div class="closePopbtn btn btn-yellow">Close</div>');
                        $('#mesageNotfiOverlay .msgNoticontent').append('<div class="orBtn">OR</div>');
                        $('#mesageNotfiOverlay .msgNoticontent').append('<div class="dropShareBtn"><span class="btn btn-yellow" id="sharefoll">Share with my followers</span> <span class="btn btn-yellow" id="shareContact">Share with my contacts</span> <span class="btn btn-yellow" id="userShare">Select users to share</span></div>')
                        $('#mesageNotfiOverlay .msgNoticontent').append('<div class="userInptxt"><input id="submit_tag_names" type="hidden" value="" name=""><ul class="fieldInput" id="myTags2"></ul> </div>');
                        $('#myTags2').tokenInput(BASE_URL+"/myhome/searchusers/", {
                               preventDuplicates: true,
                               hintText: "type user name",
                               theme: "facebook",
                               resultsLimit:10,
                               resultsFormatter: function(item){ return "<li>" + "<img src='" + item.url + "' title='" + item.name + "' height='25px' width='25px' />" + "<div style='display: inline-block; padding-left: 10px;'><div class='full_name'>" + item.name + "</div><div class='email'>" + item.email + "</div></div></li>" },
                               tokenFormatter: function(item) { return "<li><p><img src='" + item.url + "' title='" + item.name + "' height='25px' width='25px' />" + item.name + "</p></li>" }
                            })


                        $('#mesageNotfiOverlay .msgNoticontent').append('<div class="strtSharbtn"><span class="strtShrBtnpad btn btn-yellow">START SHARING</span> </div>');
                    }
                 }
             },
             success : function(file, response){
                console.log(file);
                console.log(response);
            }  


 });
   

}*/
/*
$(document).ready(function(){

   fadeDropzonebox(); 

   $('body').on('click','.closePopbtn', function(){     
     $('#mesageNotfiOverlay').remove();
   });

 
   $('body').on('click','#sharefoll, #shareContact', function(){
     $('.msgNoticontent .strtSharbtn').show().removeClass('disabled');
     $('.msgNoticontent .userInptxt').hide();
   });

   $('body').on('click','.dropShareBtn #userShare', function(){
     $('.msgNoticontent .userInptxt').show();
     $('.msgNoticontent .strtSharbtn').show().addClass('disabled');
      
   });

   $('body').on('click','.dropShareBtn span', function(){
      $('.dropShareBtn span').removeClass('active');
      $(this).addClass('active');

   });
   $("body").on('change','#myTags2', function(){
        //$('#resetusersetr').show();       
        if($('#myTags2').val()!=0){      
             alert($('#myTags2').val());
            $('.msgNoticontent .strtSharbtn').show().removeClass('disabled');
        }else{
            $('.msgNoticontent .strtSharbtn').show().addClass('disabled');
           
        }
     });

 $("body").on('click','#sharefoll, #shareContact, #userShare', function(){
        //$('#resetusersetr').show();
        alert($('#myTags2').val());
        var userid = $('#myTags2').val();
        var type = $('#sharetype').val();
        var sharefile = $('#sharetype').val();

        
        url     =   BASE_URL+"/admin/knowledgecenter/createfilesbase";
        $.ajax({                                      
            url: url,                  //the script to call to get data          
            data: formData,                        //you can insert url argumnets here to pass to api.php
            type: 'POST',
            beforeSend: function()
            {
                $("#beforecallfile").show();
                $("#kc_submit ").append(' <span class="invitemsgbox"> <i class="fa fa-spinner fa-spin"></i></span>');
             },  
            success: function(data)          //on recieve of reply
            {
                
                $("#beforecallfile").hide();
                $("#exp_condition").hide();
                
                data = data.split('~');

                if(data[1]==1)
                {
                    $('body').append($('<form/>', {
                        id: 'form',
                        method: 'POST',
                        action: '#'
                    }));

                    $('#form').append($('<input/>', {
                        type: 'hidden',
                        name: 'parentId',
                        value: folderId
                    }));

                    $('#form').append($('<input/>', {
                        type: 'hidden',
                        name: 'folderName',
                        value: folderDir
                    }));
                    if(localTick==false)
                    socket.emit('chkactivitynotification', 1,clientID);
                    $('#form').submit();
                }

            },
            cache: false,
            contentType: false,
            processData: false
        });
});




});*/


//end fullwidth dropzone//
















function showcookie(user) {

    var CookieaccptTemplate = '<div style="text-align:center">We use cookies to ensure that we give you the best experience on our website.<br><br>By clicking Yes you allow us to receive all cookies on this website.</div>';
    $.dbeePopup(CookieaccptTemplate, {
        closeBtnHide: true,
        overlayClick: false,
        escape: false,
        otherBtn: '<a id="coockieaccept" user="' + user + '" class="btn btn-yellow pull-right" href="#">Yes</a>'
    });
}


function showForceLogout() {

    var CookieaccptTemplate = '<div class="noFound" style="margin-top:0px;">Sorry your account has been made dormant by the platform administrator.<br /><br />You are now being logged out.</div>';
    $.dbeePopup(CookieaccptTemplate, {
        closeBtnHide: true,
        overlayClick: false,
        escape: false,
        otherBtn: '<a class="btn btn-yellow pull-right" href="#">Ok</a>'
    });
}


function uploadprofilepicbox(Name, lname) {
    lname = (typeof(lname) == 'undefined') ? '' : lname;

   
             var profilePicTemplate = '<div class="newPicWrapper" >\
                                    <div class="welcomeScreenForTour">\
                                        <div class="prPicUploader" style="background:url('+IMGPATH+'/users/'+userpic+'); background-size:cover">\
                                            <i class="fa fa-camera fa-2x"><input type="file" name="file" accept="image/jpeg,image/gif,image/png"  /></i>\
                                        </div>\
                                        <div>\
                                            <h2>Hi ' + Name + ' ' + lname + '</h2>\
                                            <div>Why not upload a profile picture now? It will improve your visibility across the site.</div>\
                                        </div>\
                                    </div>\
                                 </div>';

    $.dbeePopup(profilePicTemplate, {
        closeLabel: 'No Thanks',
        popClass:'picUploadPopupWrp',
        otherBtn: '<input type="hidden" name="PostPix" id="profile_picture" value="" />\
                    <label class="labelCheckbox" style="margin-left:10px; margin-top:3px; color:#CCC">\
                    <input type="checkbox" onclick="stopppbox();" value="1">\
                    <label class="checkbox"></label>Don\'t show me again</label>\
        <a href="javascript:void(0);" class="pull-right btn btn-yellow" style="display:none;" id="upload-pic" >Save</a>',
       load:function (){
            $('.prPicUploader input').change(function(evt) {
                    $('.welcomeScreenForTour').hide();
                    $.dbCropImage({element:'.dbeePopContent', event:evt, load:function(){
                            $('#upload-pic').show();
                             $.dbeePopup('resize');
                        }
                     });
            });
                
        }
    })
}

function openmutualscores(user) {

        var MutualTemplate = '<div class="formRow" id="Mutualpostion" ><img src="' + BASE_URL + '/images/rsslogos/ajaxloader-big.gif" /></div>';


        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {
                'userID': user
            },
            url: BASE_URL + '/profile/mutualscores',
            async: true,
            beforeSend: function() {
                //$.dbeePopup(MutualTemplate, {overlay:true});
            },

            complete: function() {

            },
            cache: false,

            success: function(response) {
                $("#Mutualpostion").html(response.content);
            },

            error: function(request, status, error) {

                $("#Mutualpostion").html("Some problems have occured. Please try again later: " + error);

            }

        });

    }
    // OPEN WHY I NEED TO PROVIDE BIRTHDAY POPUP


function updatelabel(val) {
    var k = 1;

    var splitVal = val.split(',');

    var total = splitVal.length;

    for (i = 0; i < total; i++) {

        $("#label" + splitVal[i]).html('<span class="rssfeedspopIcon"><i class="fa fa-rss fa-lg"></i> ' + k + '</span>');

        k++;

    }
}

function filter() {

    var array = $("#rss-sites").val().split(",");

    // If you are dealing with numeric values then you will want to cast the string as a number

    var numbers = array.map(function(v) {
        return parseInt(v)
    });

    // Remove anything which is not a number

    var filtered = numbers.filter(function(v) {
        return !isNaN(v)
    });

    // If you want to rejoin your values

    var joined = filtered.join(",");

    return joined;

}

function openrssselector() {
    $.post(BASE_URL + '/myhome/dbeerss', {
        rss: true
    }, function(data) {
        if (data.status == 'success') {

            var rssTemplate = '<div class="newPicWrapper" >' + data.content + '</div>';

            $.dbeePopup(rssTemplate, {
                otherBtn: '<a href="#" class="pull-right btn btn-yellow savetorss"  onclick="javascript:saveuserrss();">Save</a>'
            });
            
            if($('.labelCheckbox').hasClass('rssdeactive')==true)
            {
                $('.dbeePopContent .itemGuideMark').show();
            }

        } else {
            //$messageError(data.message);
            $dbConfirm({
                content: data.message,
                yes: false,
                error: true
            });
            $('.dbeePopContent .itemGuideMark').hide();
        }
    }, 'json');


}

function showuploadpic() {
    document.getElementById('whynotupload').style.display = 'none';
    document.getElementById('selectnew').style.display = 'block';
}

function closeuploadpic() {
    parent.Shadowbox.close();
}

function closeaccountmessage() {
        OpenShadowbox('accountisclosed.php', '', '210', '450');
    }
    // OPEN SEND MESSAGE BOX

function opensendmessage(user, name, thisBtn) {
    var thisEL = $(thisBtn);
    var PopupTemplate = '<div id="content_data"><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>\
				 <div class="clearfix"></div>';

    $.dbeePopup(PopupTemplate, {
        otherBtn: '<a href="javascript:void(0)" id="sendmessage" class="btn btn-yellow pull-right"onclick="javascript:postmessagetwo();">Send Message</a>\
											'
    });

    $.ajax({
        type: "POST",
        dataType: 'json',
        data: {
            'user': user,
            'name': name
        },
        url: BASE_URL + '/message/sendmessage',

        async: false,
        beforeSend: function() {

            thisEL.append(' <i class="fa fa-spinner fa-spin"></i>');
        },

        complete: function() {
            $('.fa-spin', thisEL).remove();

        },

        cache: false,

        success: function(response) {
            callsocket();
            $("#content_data").html(response.content);
            if (response.hidebutun) {
                $('#sendmessage').hide();
            }

        },

        error: function(request, status, error) {

            $("#content_data").html("Some problems have occured. Please try again later: " + error);

        }

    });
}



// OPEN TERMS

function openterms(data) {
    var termsde = '<div id="content_data" ><i class="fa fa-spinner fa-spin"></i></div>\
				 <div class="clearfix"></div>';
          if($('#dbeePopupWrapper').is(':visible')==false){
                $.dbeePopup(termsde,{popClass:'notSelected'});
            }
    $.ajax({
        type: "POST",
        dataType: 'html',
        data: {
            'data': data
        },
        url: BASE_URL + '/update/terms',
        async: false,
        cache: false,
        success: function(response) {
            $("#content_data").html(response);
            $.dbeePopup('resize');
        },

        error: function(request, status, error) {

            $("#content_data").html("Some problems have occured. Please try again later: " + error);

        }

    });


}

function opencookies(data) {
    var termsde = '<div id="content_data" ><i class="fa fa-spinner fa-spin"></i></div>\
				 <div class="clearfix"></div>';
    if($('#dbeePopupWrapper').is(':visible')==false){
        $.dbeePopup(termsde,{popClass:'notSelected'});
    }
    
    $.ajax({
        type: "POST",
        dataType: 'html',
        data: {
            'data': data
        },
        url: BASE_URL + '/update/cookies',
        async: false,
        cache: false,
        success: function(response) {
            $("#content_data").html(response);
            $.dbeePopup('resize');
        },

        error: function(request, status, error) {

            $("#content_data").html("Some problems have occured. Please try again later: " + error);

        }

    });


}

function opentermsofuse(data) {
    var termsde = '<div id="content_data" ><i class="fa fa-spinner fa-spin"></i></div>\
                 <div class="clearfix"></div>';
    if($('#dbeePopupWrapper').is(':visible')==false){
        $.dbeePopup(termsde,{popClass:'notSelected'});
    }
    $.ajax({
        type: "POST",
        dataType: 'html',
        data: {
            'data': data
        },
        url: BASE_URL + '/update/termsofuse',
        async: false,
        cache: false,
        success: function(response) {
           
            $("#content_data").html(response);
            $.dbeePopup('resize');
        },

        error: function(request, status, error) {

            $("#content_data").html("Some problems have occured. Please try again later: " + error);

        }

    });


}

// OPEN PRIVACY

function openprivacy() {
    var terms = '<div id="content_data" ><i class="fa fa-spinner fa-spin"></i></div>\
				 <div class="clearfix"></div>';
    if($('#dbeePopupWrapper').is(':visible')==false){
          $.dbeePopup(terms, {
            overlay: true,
            popClass:'notSelected'
        });
    }
    
    $.ajax({
        type: "POST",
        dataType: 'html',
        url: BASE_URL + '/update/privacy',

        async: false,
        beforeSend: function() {

        },

        complete: function() {

        },

        cache: false,

        success: function(response) {
            $("#content_data").html(response);


        }


    });
}


// OPEN ABOUT

function openabout() {
    var terms = '<div id="content_data" ><img src="' + BASE_URL + '/images/rsslogos/ajaxloader-big.gif" /></div>\
				 <div class="clearfix"></div>';
   
     if($('#dbeePopupWrapper').is(':visible')==false){
              $.dbeePopup(terms, {
                overlay: true,
                popClass:'notSelected'
             });
        }
    $.ajax({
        type: "POST",
        dataType: 'json',
        url: BASE_URL + '/update/about',

        async: false,
        beforeSend: function() {

        },

        complete: function() {

        },

        cache: false,

        success: function(response) {
            $("#content_data").html(response.content);


        }


    });
}


function sendfeedback() {
        $('#sendfeedback').append(' <i class="fa fa-spinner fa-spin"> </i>');
        var feedbacktext = $("#feedbacktext").val();
        var feedbackname = $("#feedbackname").val();
        var feedbackemail = $("#feedbackemail").val();
        createrandontokenLogin();
        var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if (feedbackname == '') {
            $dbConfirm({
                content: 'Please enter your name',
                yes: false,
                error: true
            });
            $('#sendfeedback .fa-spin').remove();
            return false;
        } else if (feedbackemail == '') {
            //$messageError('Please enter your email');
            $dbConfirm({
                content: 'Please enter your email',
                yes: false,
                error: true
            });
            $('#sendfeedback .fa-spin').remove();
            return false;
        } else if (!filter.test(feedbackemail)) {
            $dbConfirm({
                content: 'Please enter your valid email address.',
                yes: false,
                error: true
            });
            $('#sendfeedback .fa-spin').remove();
            return false;
        } else if (feedbacktext == '') {
            $dbConfirm({
                content: 'Please enter feed back message',
                yes: false,
                error: true
            });
            $('#sendfeedback .fa-spin').remove();
            return false;
        } else {
            data = 'feedbacktext=' + feedbacktext + '&feedbackname=' + feedbackname + '&feedbackemail=' + feedbackemail + '&' + userdetailsLogon;

            $.ajax({
                type: "POST",
                dataType: 'json',
                url: BASE_URL + '/update/sendfeedback',
                data: data,

                async: false,
                cache: false,
                beforeSend: function() {

                },
                complete: function() {
                    $('#sendfeedback .fa-spin').remove();
                },
                success: function(response) {

                    if (response.status == 'success') {
                        $.dbeePopup('close');
                        $dbConfirm({
                            content: response.message,
                            yes: false
                        });
                        //$('#sendfeedback .fa-spin').remove();
                    } else {
                        $dbConfirm({
                            content: response.message,
                            yes: false,
                            error: true
                        });
                    }

                }

            });
        }
    }
    // OPEN FEEDBACK FORM

function openfeedback() {
    var terms = '<div id="content_data" ><i class="fa fa-spinner fa-spin"></i></div>\
				 <div class="clearfix"></div>';
    $.dbeePopup(terms, {
        otherBtn: '<a id="sendfeedback" class="btn btn-yellow pull-right"  onclick="javascript:sendfeedback()">Send feedback</a>'
    });
    $.ajax({
        type: "POST",
        dataType: 'json',
        url: BASE_URL + '/update/feedback',

        async: false,
        cache: false,
        success: function(response) {
            $("#content_data").html(response.content);
        }

    });
}


function showhidedbav(div) {
        document.getElementById('dbmedia-vidz').style.display = 'none';
        document.getElementById('dbmedia-audio').style.display = 'none';
        document.getElementById('dbmedia-' + div).style.display = 'block';
        document.getElementById('hiddenmediaposted').value = div;
    }
    // FETCH MUTUAL SCORES
var MutualScoresHttp;

function mutualscores(user) {
    MutualScoresHttp = Browser_Check(MutualScoresHttp);

    var url = BASE_URL + "ajax_mutualscores.php";
    var data = "user=" + user;

    MutualScoresHttp.open("POST", url, true);
    MutualScoresHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    MutualScoresHttp.setRequestHeader("Content-length", data.length);
    MutualScoresHttp.setRequestHeader("Connection", "close");
    MutualScoresHttp.onreadystatechange = mutualscoresresult;
    MutualScoresHttp.send(data);
}

function mutualscoresresult() {
        if (MutualScoresHttp.readyState == 4) {
            if (MutualScoresHttp.status == 200 || MutualScoresHttp.status == 0) {
                var GetResult = MutualScoresHttp.responseText;
                document.getElementById("mutual-scores").innerHTML = GetResult;
            } else {};
        }
    }
    // CONFIRM RESET PASSWORD CODE
var ResetCodeHttp;

function checkresetcode() {
    document.getElementById('resetcode-error').innerHTML = '';
    var resetcode = document.getElementById('resetcode').value;
    var user = document.getElementById('user').value;
    ResetCodeHttp = Browser_Check(ResetCodeHttp);
    if (resetcode != '') {
        var url = BASE_URL + "/index/checkresetcode";
        var data = "resetcode=" + resetcode + "&user=" + user;
        ResetCodeHttp.open("POST", url, true);
        ResetCodeHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        ResetCodeHttp.setRequestHeader("Content-length", data.length);
        ResetCodeHttp.setRequestHeader("Connection", "close");

        ResetCodeHttp.onreadystatechange = checkresetcoderesult;
        ResetCodeHttp.send(data);
    }
}

function checkresetcoderesult() {
        if (ResetCodeHttp.readyState == 4) {
            if (ResetCodeHttp.status == 200 || ResetCodeHttp.status == 0) {
                var GetResult = ResetCodeHttp.responseText;
                var resultArr = GetResult.split('~');
                if (resultArr[0] == 1) {
                    document.getElementById("resetcode-wrapper").style.display = 'none';
                    document.getElementById("resetpass-wrapper").style.display = 'block';
                    document.getElementById("resetpass-wrapper").innerHTML = resultArr[1];
                } else {
                    document.getElementById('resetcode-error').innerHTML = '<font color="#CC0000">incorrect code entered!</font>';
                }
            } else {};
        }
    }
    // RESET PASSWORD
var ResetPassHttp;

function resetpass(thisElement) {
    $('#resetpass-error').innerHTML = '';
    var newpass = $.trim($('#newpass').val());
    var newpassrepeat = $.trim($('#newpassrepeat').val());
    var user = $('#user').val();
    var thisEl = $(thisElement);
    if (newpassrepeat != newpass && newpass != '') {
        $dbConfirm({
            content: 'Passwords do not match',
            yes: false,
            error: true
        });
        return false;
    } else {
        var url = BASE_URL + "/index/resetpass";
        var data = "newpass=" + newpass + "&user=" + user;
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            beforeSend: function() {
                $('.fa-spin', thisEl).remove();
                thisEl.append('<i class="fa fa-spinner fa-spin"></i>');
            },
            success: function(GetResult) {
                $('.fa-spin', thisEl).remove();
                $("#resetpass-wrapper").hide();
                $("#resetpass-msg-wrapper").show();
            }
        });
    }
}

/*function resetpassresult() {
    if (ResetPassHttp.readyState == 4) {
        if (ResetPassHttp.status == 200 || ResetPassHttp.status == 0) {
            var GetResult = ResetPassHttp.responseText;
            if (GetResult == 1) {
                document.getElementById("resetpass-wrapper").style.display = 'none';
                document.getElementById("resetpass-msg-wrapper").style.display = 'block';
            } else {}
        } else {};
    }
}*/
// SEND FORGOTTEN PASSWORD
var SendPassHttp;

function sendpass(n, id) {
    $('.sendpassina').append(' <i class="fa fa-spinner fa-spin"> </i>').css('cursor', 'default');
    n = typeof(n) != 'undefined' ? n : '0';
    id = typeof(id) != 'undefined' ? id : '0';
    calling = $('#fromfront').val();
    if (n == 0) {
        var email = $('#forgotemail').val();
        var birthday = $('#birthday').val();
        var birmons = $("#birmon option:selected").val();
        var birthyear = $('#birthyear').val();
        var pdata = {
            'email': email,
            'birthday': birthday,
            'birthmonth': birmons,
            'birthyear': birthyear,
            'fromlogin': '1'
        };
        url = BASE_URL + '/index/sendpassword';
    } else {
        var pdata = {
            'user': id
        };
        url = BASE_URL + '/settings/sendpassword';
    }

    $.ajax({
        type: "POST",
        dataType: 'json',
        data: pdata,
        url: url,
        complete: function() {
            setTimeout(function() {
                $('.sendpassina .fa-spin').remove();
                $('.sendpassina').css('cursor', 'pointer');
            }, 2000);
        },
        cache: false,
        success: function(response) {

            if (response.SubmitMessage == 0) {
                $("#forgotpassm").hide();
                $("#sendlink").hide();
                $dbConfirm({
                    content: "Password reset link has been sent to your registered email address.",
                    yes: false
                });
                $.dbeePopup('close');
            } else {
                //$messageError(response.message);

                if (n == 0)
                    $dbConfirm({
                        content: response.message,
                        yes: false,
                        error: true
                    });
                $.dbeePopup('close');
            }
        }
    });
}

// FUNCTION TO CONFIRM COOKIE ACCEPTANCE ON SIGN IN
var AcceptCookiesHttp;

function cookiesaccepted(user) {
    AcceptCookiesHttp = Browser_Check(AcceptCookiesHttp);

    if (document.getElementById('acceptcookies').checked) {
        var url = "ajax_cookiesaccepted.php";
        var data = "user=" + user;

        AcceptCookiesHttp.open("POST", url, true);
        AcceptCookiesHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        AcceptCookiesHttp.setRequestHeader("Content-length", data.length);
        AcceptCookiesHttp.setRequestHeader("Connection", "close");

        AcceptCookiesHttp.onreadystatechange = cookiesacceptedresult;
        AcceptCookiesHttp.send(data);
    } else {
        document.getElementById('acceptcookies').focus();
    }
}

function cookiesacceptedresult() {
        if (AcceptCookiesHttp.readyState == 4) {
            if (AcceptCookiesHttp.status == 200 || AcceptCookiesHttp.status == 0) {
                var GetResult = AcceptCookiesHttp.responseText;
                if (GetResult == '1') {
                    parent.document.location.href = 'processlogin.php?ca';
                }
            } else {};
        }
    }
    // DONT SHOW PROFILE PIC UPLOAD BOX AGAIN ON SIGN IN
var StopPPBoxHttp;

function stopppbox() {
    $.post(BASE_URL + '/myhome/stopppbox', {
        hide: true
    }, function(data) {
        if (data.status == 'success')
            $('.closePostPop').trigger('click');
    }, 'json');

}

function stopEmailbox() {
    $.post(BASE_URL + '/myhome/stopemailbox', {
        hide: true
    }, function(data) {
        if (data.status == 'success')
            $('.closePostPop').trigger('click');
    }, 'json');
}

function stopppboxresult() {
    if (StopPPBoxHttp.readyState == 4) {
        if (StopPPBoxHttp.status == 200 || StopPPBoxHttp.status == 0) {
            var GetResult = StopPPBoxHttp.responseText;
        } else {};
    }
}

// SEND BUG REPORT
var SendBugReportHttp;

function sendbugreport() {
    var err = false;
    var bug = $('#bug').val();
    var userbrowser = $('#userbrowser').val();
    if (bug == '') {
        $('#bug').focus();
        err = true;
    } else if (userbrowser == '0') {
        $('#userbrowser').focus();
        err = true;
    }
    if (!err) {
        //createrandontoken();

        var url = BASE_URL + "/report/reportabugprcess";
        data += "&bug=" + bug + "&userbrowser=" + userbrowser;
   

          $.ajax({
                url: url,
                type: 'POST',
                data: data,
                beforeSend:function(){
                    $('a#reportbug').append(' <i class="fa fa-spinner fa-spin"> </i>').css('cursor', 'default').removeAttr("onclick");
                },
                success:function(response){
                        // in case of invalid authentication
                       var GetResult =  response.status;
                    if (GetResult == 'false') {
                        window.location.href = BASE_URL + "/myhome/logout/";
                        return false;
                    }
                    // in case of invalid authentication
                    if (GetResult == '1') {
                        $('#bugform').closest('.formRow').hide();
                        $('#bugreport-message').html('<div align="center" style="margin:70px 0 0 0;">Thank you for your valued feedback :)<br /><br />We have received the bug report and will act on it soon.</div>');
                       
                    } else {
                        $.dbeePopup('close');
                        $dbConfirm({
                            content: 'Thank you, we will look into this issue as soon as possible.',
                            yes: false
                        });

                        //$dbConfirm({content:"Bug report send successfully!", yes:false});
                        
                        setTimeout(function() {
                            $('#reportbug').attr("onclick", "javascript:sendbugreport();");
                            $('#reportbug .fa-spin').remove();
                            $('a#reportbug').css('cursor', 'pointer');
                        }, 2000);
                    }
                }
            });

    }
}


    // GO TO DB WHEN NEW COMMENT IS CLICKED FROM NEW COMMENT POPUP
var GoToDbHttp;

function gotodb(id, comm) {

    GoToDbHttp = Browser_Check(GoToDbHttp);

    //var url="ajax_gotodb.php";
    var url = BASE_URL + "/comment/gotodb";
    var data = "db=" + id + "&comm=" + comm;
    GoToDbHttp.open("POST", url, true);
    GoToDbHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    GoToDbHttp.setRequestHeader("Content-length", data.length);
    GoToDbHttp.setRequestHeader("Connection", "close");
    GoToDbHttp.onreadystatechange = gotodbresult;
    GoToDbHttp.send(data);
}

function gotodbresult() {
        if (GoToDbHttp.readyState == 4) {
            if (GoToDbHttp.status == 200 || GoToDbHttp.status == 0) {
                var result = GoToDbHttp.responseText;
                var resultArr = result.split('~');
                if (resultArr[0] == '1') {
                    parent.document.location.href = '/dbeedetail/home/id/' + resultArr[1];
                } else {}
            } else {};
        }
    }
    // FUNCTION TO HANDLE NEW COMMENT NOTIFY
var CommentNotifyHttp;

/*
function commentnotify(db, type) {
    CommentNotifyHttp = Browser_Check(CommentNotifyHttp);

    if (type == '1') {
        //document.getElementById("notify-email-status").style.display = 'none';
        //document.getElementById("commnoteloader").style.display = 'block';
    }
    var url = "/comment/commentnotify";
    var data = "db=" + db + "&type=" + type;
    
    CommentNotifyHttp.open("POST", url, true);
    CommentNotifyHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    CommentNotifyHttp.setRequestHeader("Content-length", data.length);
    CommentNotifyHttp.setRequestHeader("Connection", "close");
    CommentNotifyHttp.onreadystatechange = commentnotifyresult;
    CommentNotifyHttp.send(data);
}

function commentnotifyresult() {
    if (CommentNotifyHttp.readyState == 4) {
        if (CommentNotifyHttp.status == 200 || CommentNotifyHttp.status == 0) {
            var result = CommentNotifyHttp.responseText;
          
            var resultArr = result.split('~');

            if (resultArr[1] == '1') {
                //document.getElementById("notify-email-status").style.display = 'block';
                //document.getElementById("commnoteloader").style.display = 'none';
            }
            if (resultArr[0] == '1') { // IF TURNED ON
                if (resultArr[1] == '1') { // IF TOGGLE EMAIL
                    //$(".qtip").remove();
                    //loadQtipAboveMaindbFix('notify-email-status','Turn OFF email notifications for this dbee');
                    //document.getElementById("notify-email-status").className = 'radioTick';
                	$('#notify-email-on').addClass('notify-email-status-on');
                	$('#notify-email-off').removeClass('notify-email-status-on');
				} else if (resultArr[1] == '2') { // IF TOGGLE POPUP NOTES
                }
            } else if (resultArr[0] == '0') { // IF TURNED OFF
                if (resultArr[1] == '1') { // IF TOGGLE EMAIL
                    //$(".qtip").remove();
                    //	loadQtipAboveMaindbFix('notify-email-status','Turn ON email notifications for this dbee');
                    //document.getElementById("notify-email-status").className = 'radio';
                	$('#notify-email-on').removeClass('notify-email-status-on');
                	$('#notify-email-off').addClass('notify-email-status-on');
                } else if (resultArr[1] == '2') { // IF TOGGLE POPUP NOTES
                    var countdown = window.parent.document.getElementById('notifications-top-comment-hidden').value - resultArr[3];
                    window.parent.document.getElementById('notifications-top-comment-hidden').value = countdown;
                    window.parent.document.getElementById('sticky-left-newcomm-num').innerHTML = window.parent.document.getElementById('notifications-top-comment-hidden').value;
                    $(".cmntnote-" + resultArr[2]).remove();
                    if (countdown <= 0) {
                        window.parent.document.getElementById('sticky-left-newcomm').style.display = 'none';
                        document.getElementById('nocomment-msg').style.display = 'block';
                    }
                }
            }
            if (resultArr[1] == '1') {
                setTimeout("closepopup('fade')", 3000);
                setTimeout("document.getElementById('commentnotify-popup').innerHTML=''", 4000);
            }
        } else {};
    }
}*/
// CHECK SEARCH

function checksearch() {
    var searchid = document.getElementById("searchid").value;

    return false;
    if (searchtype != '') {
        if (searchtype == '1') {
            var dbee = document.getElementById('searchid').value;
            document.searchform.action = '/dbeedetail/home/id/' + dbee;
        } else if (searchtype == '2') {
            var user = document.getElementById('searchid').value;
            document.searchform.action = '/profile/index/id/' + user;
        }
    }
}

function seemoresearch() {
    //document.getElementById('searchtype').value = id;
    document.getElementById("searchform").submit();
}


// FILL CATEGORY SORT OPTIONS DIV ON HOME

function fillcategory() {
    if ($('#login').val() == '')
        return false;
    //var url="ajax_categorysortoptions.php";
    var url = BASE_URL + "/myhome/categorysort";
    var data = "";

    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        success:function(result){
             $('#category-options').html(result);
                $('#dbfeedfilterbycatWrp').perfectScrollbar();
        }
    });
    
}


    // FILL DISPLAY BY OPTIONS DIV ON HOME
var DisplayByHttp;

function filldisplayby() {
    DisplayByHttp = Browser_Check(DisplayByHttp);

    //var url="ajax_displayby.php";
    var url = BASE_URL + "/myhome/displayby";
    var data = "";

    DisplayByHttp.open("POST", url, true);
    DisplayByHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    DisplayByHttp.setRequestHeader("Content-length", data.length);
    DisplayByHttp.setRequestHeader("Connection", "close");
    DisplayByHttp.onreadystatechange = filldisplaybyresult;
    DisplayByHttp.send(data);
}

function filldisplaybyresult() {
    if (DisplayByHttp.readyState == 4) {
        if (DisplayByHttp.status == 200 || DisplayByHttp.status == 0) {
            var result = DisplayByHttp.responseText;
            document.getElementById("display-by-options").innerHTML = result;
        } else {};
    }
}

// OPEN DIV TO POST DBEE

function showdbeepostoption(id) {
        document.getElementById("DbeeTextTab").className = 'dbee-post-tab-Text-close';
        document.getElementById("DbeeLinkTab").className = 'dbee-post-tab-Link-close';
        document.getElementById("DbeePixTab").className = 'dbee-post-tab-Pix-close';
        document.getElementById("DbeeVidzTab").className = 'dbee-post-tab-Vidz-close';
        document.getElementById("DbeePollsTab").className = 'dbee-post-tab-Polls-close';
        document.getElementById("Dbee" + id + "Tab").className = 'dbee-post-tab-' + id + '-open';
        document.getElementById("dbee-post-options-wrapper").style.display = 'block';
        document.getElementById("DbeeText").style.display = 'none';
        document.getElementById("DbeeLink").style.display = 'none';
        document.getElementById("DbeePix").style.display = 'none';
        document.getElementById("DbeeVidz").style.display = 'none';
        document.getElementById("DbeePolls").style.display = 'none';
        document.getElementById("DbeeCat").style.display = 'none';
        $("#Dbee" + id).fadeIn("slow");
        //	document.getElementById("Dbee"+id).style.display='block';
        if (id == 'Text') {
            document.getElementById('dbee-post-options-wrapper').style.borderTopLeftRadius = "0px";
            document.getElementById('dbee-post-options-wrapper').style.borderTopRightRadius = "8px";
        } else if (id == 'Polls') {
            document.getElementById('dbee-post-options-wrapper').style.borderTopLeftRadius = "8px";
            document.getElementById('dbee-post-options-wrapper').style.borderTopRightRadius = "0px";
        } else {
            document.getElementById('dbee-post-options-wrapper').style.borderTopLeftRadius = "8px";
            document.getElementById('dbee-post-options-wrapper').style.borderTopRightRadius = "8px";
        }
        document.getElementById("dbeetype").value = id;
    }
    // OPEN DIV TO POST DBEE

function closedbeepostoption() {
        //	document.getElementById("dbee-post-options-wrapper").style.display='none';
        $("#dbee-post-options-wrapper").slideUp("slow");
        document.getElementById("DbeeTextTab").className = 'dbee-post-tab-Text-close';
        document.getElementById("DbeeLinkTab").className = 'dbee-post-tab-Link-close';
        document.getElementById("DbeePixTab").className = 'dbee-post-tab-Pix-close';
        document.getElementById("DbeeVidzTab").className = 'dbee-post-tab-Vidz-close';
        document.getElementById("DbeePollsTab").className = 'dbee-post-tab-Polls-close';
    }
    // OPEN DIV TO REPLY DBEE

function showreplyoption(id) {
        $('#dbreply-icon-text').fadeTo(30, 0.20);
        $('#dbreply-icon-link').fadeTo(30, 0.20);
        $('#dbreply-icon-pix').fadeTo(30, 0.20);
        $('#dbreply-icon-vidz').fadeTo(30, 0.20);
        $('#dbreply-icon-' + id).fadeTo(30, 1);
        document.getElementById("dbreply-text").style.display = 'none';
        document.getElementById("dbreply-link").style.display = 'none';
        document.getElementById("dbreply-pix").style.display = 'none';
        document.getElementById("dbreply-vidz").style.display = 'none';
        document.getElementById("dbreply-" + id).style.display = 'block';

        if (id != 'text')
            document.getElementById("dbcommentcountdown").style.display = 'none';
        else
            document.getElementById("dbcommentcountdown").style.display = 'block';

        document.getElementById("replytype").value = id;
    }
    // ATTACH LINK TO DBEE
var AttachlinkHttp;

function attachlinkdbeeresult() {
        if (AttachlinkHttp.readyState == 4) {
            if (AttachlinkHttp.status == 200 || AttachlinkHttp.status == 0) {
                var GetResult = AttachlinkHttp.responseText;
                if (GetResult != '0' && GetResult != '-1' && GetResult != '') {
                    document.getElementById('attachlinkerror').style.display = 'none';
                    document.getElementById('attachlinkloader').style.display = 'none';
                    //document.getElementById('LinkInfoWrapper').style.display='block';
                    document.getElementById('LinkInfo').innerHTML = GetResult;
                } else {
                    if (GetResult == '0' || GetResult == '') {
                        document.getElementById('attachlinkloader').style.display = 'none';
                        document.getElementById('attachlinkerror-text').innerHTML = 'The website addresss was not found.';
                        document.getElementById('attachlinkerror').style.display = 'block';
                    } else if (GetResult == '-1') {
                        document.getElementById('attachlinkloader').style.display = 'none';
                        document.getElementById('attachlinkerror-text').innerHTML = 'To post a video click on \'post vidz\'';
                        document.getElementById('attachlinkerror').style.display = 'block';
                    }
                }
            } 
        }
    }
    // VALIDATE VIDEO URL
var ValidateVideoHttp;

function validatevideo(vid) {
    ValidateVideoHttp = Browser_Check(ValidateVideoHttp);
    var url = BASE_URL + "/comment/validatevideo";
    var data = "video=" + vid;
    ValidateVideoHttp.open("POST", url, true);
    ValidateVideoHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ValidateVideoHttp.setRequestHeader("Content-length", data.length);
    ValidateVideoHttp.setRequestHeader("Connection", "close");
    ValidateVideoHttp.onreadystatechange = validatevideoresult;
    ValidateVideoHttp.send(data);
}

function validatevideoresult() {
        if (ValidateVideoHttp.readyState == 4) {
            if (ValidateVideoHttp.status == 200 || ValidateVideoHttp.status == 0) {
                var GetResult = ValidateVideoHttp.responseText;

                if (GetResult == '1') {
                    return true;
                } else {

                    return false;
                }
            } else alert("Retrieval Error: " + ValidateVideoHttp.statusText);
        }
    }
    // SHOW/HIDE DB POST CATEGORY OPTIONS

function showdbpostcat(type) {
    var err = false;
    if (type == 'Text') {
        if (document.getElementById('PostText').value == "what's on your mind?") {
            err = true;
            document.getElementById('PostText').focus();
        }
    } else if (type == 'Link') {
        if (document.getElementById('LinkInfo').innerHTML == "") {
            err = true;
            document.getElementById('PostLink').focus();
        }
    } else if (type == 'Pix') {
        if (!(document.getElementById('PostPix')) || document.getElementById('PostPix').value == "") {
            err = true;
            document.getElementById('ajaxfilename').focus();
        }
    } else if (type == 'Vidz') {
        if (document.getElementById('PostVidz').value == "paste YouTube link here" && document.getElementById('PostAudio').value == "paste SoundCloud embed code here") {
            err = true;
            var mediaposted = document.getElementById('hiddenmediaposted').value;
            if (mediaposted == 'vidz')
                document.getElementById('PostVidz').focus();
            else if (mediaposted == 'audio')
                document.getElementById('PostAudio').focus();
        }
    }

    if (!err) {
        document.getElementById('Dbee' + type).style.display = 'none';
        document.getElementById('dbpostcat-return').innerHTML = '<div style="float:right; margin-left:25px; font-size:14px;">3. Categorise post</div><div style="float:right; font-size:14px; color:#999999; cursor:pointer;" onclick="javascript:hidedbpostcat(\'' + type + '\')">1. add text&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2. add hashtag</div>';
        document.getElementById('DbeeCat').style.display = 'block';
    }
}

function hidedbpostcat(type) {
    document.getElementById('DbeeCat').style.display = 'none';
    document.getElementById('Dbee' + type).style.display = 'block';
}

function doSharingTest() {
        IN.API.Raw("/people/~/shares")
            .method("POST")
            .body(JSON.stringify({
                "content": {
                    "submitted-url": "http://developer.linkedinlabs.com/jsapi-console",
                    "title": "JSAPI Console",
                    "description": "JSAPI Developer Console",
                    "submitted-image-url": "http://developer.linkedin.com/servlet/JiveServlet/downloadImage/102-1101-13-1003/30-25/LinkedIn_Logo30px.png"
                },
                "visibility": {
                    "code": "anyone"
                },
                "comment": "This is a test posting from the LinkedIn JSAPI Console"
            }))
            .result(function(r) {
                alert("POST OK");
            })
            .error(function(r) {
                alert("POST FAIL");
            });
    }
    // SHOW/HIDE DB POST CATEGORY OPTIONS
    // POST DBEE
var PostdbeeHttp;
var userdetails = '';
var userdetails2 = '';


function createrandontoken(callback) {
    capcha = 'deshu';
    data = 'captchakey=' + capcha;
    $.ajax({
        type: "POST",
        dataType: 'json',
        data: data,
        url: BASE_URL + '/myhome/createrandontoken',
        async: false, // Dont remove this neccessary for returning value of respnce
        success: function(response) {
            if (response.errormsg != 404) {
                //$messageError(response.errormsg);
                $dbConfirm({
                    content: response.errormsg,
                    yes: false,
                    error: true
                });
                return false;
            } else {
                userdetails = "SessUser__=" + response.SessUser__ + "&SessId__=" + response.SessId__ + "&SessName__=" + response.SessName__ + "&Token__=" + response.Token__ + "&Key__=" + response.Key__;
                userdetails2 = {SessUser__:response.SessUser__,SessId__:response.SessId__ ,SessName__:response.SessName__,Token__:response.Token__,Key__:response.Key__};
              /*  SessUser__ = response.SessUser__;
                SessId__   = response.SessId__;
                SessName__ = response.SessName__;
                Token__    = response.Token__;
                Key__      = response.Key__ ;*/
            }

        },
    })

}
var userGlobalType = $('#usergloabtype').val();

 var TaggedUsers='';
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
 var QASTATUS='';
 function postdbee(group, reloadfeed) {
        var TaggedUsers='';
        var selectedGroup = $('#selectGroupList').val();
        selectedGroup = typeof(selectedGroup) != 'undefined' ? selectedGroup : '';
        startbutid = $('#startdbHeaderBtn').attr('controlName');
        var groupname = '';
        var events = $('#eventid').val();
        var group = typeof(group) != 'undefined' ? group : '';
        var events = typeof(events) != 'undefined' ? events : '';
        var dbmentiononPost = $('#dbmentiononPost').val();
        var dbmentiononPost = typeof(dbmentiononPost) != 'undefined' ? dbmentiononPost : '';
        var Dbtag2 = $("#DbTag").tagit("assignedTags");
        var chkval = '';

        
        
        if ($('#tagmsgcon input:checkbox[name=tagmsgconsme]').is(':checked') == true) {
             chkval = 1; // user for tagchk validate
             $('#chktagval').val(0);
        }

        if ($('input:checkbox[name=QASTATUS]').is(':checked') == true) {
          var  QASTATUS = 1; // user for tagchk validate
          var qatime = $('#qatime').val();
          var qaendtime = $('#qaendtime').val();
        }else{
           var QASTATUS = 0;
           var qatime = '';
           var qaendtime = '';
        }
     
        $('textarea.mentionpost').mentionsInput('getMentions', function(data) {     
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

    var PrivatePost = $('#PrivatePost').val();

    if(dbmentiononPost!="")
    {
       PrivatePost =0; 
    }


    var isgrouppost = $('#isgrouppost').val();
    var Privategroup = '0';
    isgrouppost = typeof(isgrouppost) != 'undefined' ? isgrouppost : '';


    if (selectedGroup != '' && group != '') {
        group = selectedGroup;
    } else if (selectedGroup != '') {
        group = selectedGroup;
    }

    if (isgrouppost != 'true') {
        var grouptype = $('#selectGroupList').find('option:selected').attr('grouptype');
        var groupname = $('#selectGroupList').find('option:selected').attr('groupname');
        var groupty = typeof(grouptype) != 'undefined' ? grouptype : '0';
    } else {
        group = groupidmaster;
        var groupname = $('#groupname').val();;
        var grouptype = $('#grouptype').val();;
        var groupty = typeof(grouptype) != 'undefined' ? grouptype : '0';
    }
    if (groupty == '2' || groupty == '4') {
        Privategroup = '1';
    }
    group = typeof(group) != 'undefined' ? group : '';
    groupname = typeof(groupname) != 'undefined' ? groupname : '';
    var selectedleague = $('#leagueId').val();
    var lgenddate = $('#lgenddate').val();
    var AdminUserSet = $('#userset').val();
    
    AdminUserSet = typeof(AdminUserSet) != 'undefined' ? AdminUserSet : '';
    selectedLeague = typeof(selectedleague) != 'undefined' ? selectedleague : '';
    lgenddate = typeof(lgenddate) != 'undefined' ? lgenddate : '';
    var snc = $('#snc').val();
    var usergloabtype = $('#usergloabtype').val();
    reloadfeed = typeof(reloadfeed) != 'undefined' ? reloadfeed : '1';
    var err = false;
    var caterr = false;
    var rssfeed = '';
    var rssLink = '';
    var rssTitle = '';
    var data = '';
    var dbeetype = $('#dbeetype').val();
    var user = $('#dbeeuser').val();
    laodgroup = $('#laodgroup').val();
    var laodgroup = typeof(laodgroup) != 'undefined' ? laodgroup : '0';

    if (laodgroup != '0')
        reloadfeed = '0';    

    var dbpicture = $('#PostPix_').val();
    dbpicture = typeof(dbpicture) != 'undefined' ? dbpicture : '';
    var rssadded = $('#rssadded').val();
    rssadded = typeof(rssadded) != 'undefined' ? rssadded : '';
    var twet = $('#twitter-tag-text').val();

    if (twet != '#hastag/keyword') {
        var twittertag = $('#twitter-tag-text').val();
    } else var twittertag = '';
    if (rssadded != '') {
        rssfeed = $('#dbRssWrapper').html();
        rssLink = $('#dbRssWrapper a').attr('href');
        rssTitle = $('#dbRssWrapper #dbRssTitle').text();
    }

    var text = $.trim($('#PostText').val());
   // text = text.replace(/&/g, '%26');
    rssfeed = rssfeed.replace(/&/g, '%26');   

    if (TaggedUsers != '')
    {
             var gtag = $('#PostText').prev('.mentions');
             var text = gtag.find('div:eq(0)').html();
             //text = text.replace(/&/g, '%26');
             text = text.replace('  ', ' ');
             
    }
     var elm = $('#postTypeContent .required');
    if(dbeetype=='Text'){
            $checkError(elm);
           if($checkError(elm)==false){
              err = true;
             return false;
          }

      }
      text = conMentionTotext(text);

      data = {timezone:timezone,qatime:qatime,qaendtime:qaendtime,PrivatePost:PrivatePost,TaggedUsers:TaggedUsers,AdminUserSet:AdminUserSet,dbmentiononPost:dbmentiononPost,text:text,user:user,groupty:Privategroup,groupname:groupname,dbeetype:dbeetype,twittertag:twittertag,rssfeed:rssfeed,rssLink:rssLink,rssTitle:rssTitle,chkval:chkval}
      
    var linkurl = $('#PostLink').val();
    if (linkurl != '' && dbeetype != 'Polls') {
        var youtubetitle = $("#youtubetitle").val();
        var linktitle = $('#LinkTitle').val();


        linktitle = typeof(linktitle) != 'undefined' ? linktitle : '';
        youtubetitle = typeof(youtubetitle) != 'undefined' ? youtubetitle : '';

        if (youtubetitle != '' || linktitle != '') {
            if (linktitle != '') {
                if (linkurl.indexOf("http") != '-1') {
                    var linkurl = $('#PostLink').val();
                } else {
                    var linkurl = 'http://' + $('#PostLink').val();
                }
                var LinkPic = $('#LinkPic').val();
                var linkdesc = $('#LinkDesc').val();
                //data += "&url=" + linkurl + "&linktitle=" + linktitle + "&linkdesc=" + linkdesc + "&LinkPic=" + LinkPic;
               var  dataLink  = {url:linkurl,linktitle:linktitle,linkdesc:linkdesc,LinkPic:LinkPic};
               margeArray(data, dataLink);

            } else if (youtubetitle != '') {
                var vid = $('#PostLink').val();
                var VidChk = true;
                var videosite = '';
                var videoid = '';
                if (VidChk) {
                    var VideoURLArr = vid.split(".com");
                    if (VideoURLArr[0] == 'http://www.youtube' || VideoURLArr[0] == 'http://youtube' || VideoURLArr[0] == 'https://www.youtube' || VideoURLArr[0] == 'https://youtube' || VideoURLArr[0] == 'www.youtube' || VideoURLArr[0] == 'youtube') {
                        videosite = "Youtube";
                        var VideoURLIDArr = vid.split("watch?v=")[1].split("&");
                        videoid = VideoURLIDArr[0];
                    } else {
                        $dbConfirm({
                            content: 'Please insert valid source of url..',
                            yes: false,
                            error: true
                        });
                        err = true;
                        return false;
                    }
                    var viddesc = $("#youtubedescription").val();
                    viddesc = typeof(viddesc) != 'undefined' ? viddesc : '';
                    viddesc = viddesc.replace(/&/g, '%26');
                    if (vid != "") {
                       // data += "&vid=" + vid + "&viddesc=" + viddesc + "&videosite=" + videosite + "&videoid=" + videoid + "&VidTitle=" + youtubetitle;
                        var dataVideo = {vid:vid,viddesc:viddesc,videosite:videosite,videoid:videoid,VidTitle:youtubetitle};
                         margeArray(data, dataVideo);
                    } else {
                        $dbConfirm({
                            content: 'fields can not be blank, Please insert valid source of url..',
                            yes: false,
                            error: true
                        });
                        err = true;
                        return false;
                    }
                }
            } else {
                $dbConfirm({
                    content: 'Please use valid url in order to attach link with post',
                    yes: false,
                    error: true
                });
                err = true;
                return false;
            }
        } else {
            $dbConfirm({
                content: 'Please use valid url in order to attach link with post',
                yes: false,
                error: true
            });
            err = true;
            return false;
        }
    }
   
    if (dbeetype == 'Polls') {

        var twittertag = '';
        var polltext = $('#PostText').val();
        polltext = conMentionTotext(polltext);

        var polloption1 = document.getElementById('poll-option-1').value;
        var polloption2 = document.getElementById('poll-option-2').value;
        var polloption3 = document.getElementById('poll-option-3').value;
        var polloption4 = document.getElementById('poll-option-4').value;
        var k = 0;

        $('.pollsfields input').each(function(){
            var v  = $(this).val();            
            if(v!=''){
               k++; 
            }
            
        });
        
       // alert(k);
        var elm = $('#postTypeContent .required');
            $checkError(elm);
           if($checkError(elm)== false && k<2){
              err = true;
             return false;
          }


        //if (polltext != "" && polloption1 != "" && polloption2 != "") {
           // var polltext = $('#PostText').val();
           // var data = "polltext=" + polltext + "&polloption1=" + polloption1 + "&polloption2=" + polloption2 + "&polloption3=" + polloption3 + "&polloption4=" + polloption4 + "&user=" + user + "&dbeetype=" + dbeetype + "&twittertag=" + twittertag;
            var data ={polltext:polltext,polloption1:polloption1,polloption2:polloption2,polloption3:polloption3,polloption4:polloption4,user:user,dbeetype:dbeetype,twittertag:twittertag};
        //} else {
            //$messageError('Please enter poll text and at least 2 options.');
           
        //}
    }
    
    var cat = $.trim($('#db-post-cat').val());
    if(clientID==19)
        var dbeeTag = $.trim($('#dbee-tag-text').val());
    else
        var dbeeTag = '';

    if ($('#isPostOnFacebook').is(':checked')) {
       // data += "&Facebook=11";
        data['Facebook'] =11;
    }
    if ($('#isPostOnTwitter').is(':checked')) {
       // data += "&Twitter=12";
        data['Twitter'] =12;
    }
    if ($('#isPostOnLinkedin').is(':checked')) {
       // data += "&linkedin=13";
        data['linkedin'] =13;
    }
    if ($('.postfilelist input[type=checkbox]:checked').is(':checked')) {
        var attti = '';
        var attachment = {};
        $('.postfilelist  input:checked').each(function() {
            attti = $(this).attr('attname');
            attachment[attti] = $(this).val();
        });
       // data += "&attachment=" + JSON.stringify(attachment);
        data['attachment'] =JSON.stringify(attachment);
    }



    var chkrtnattr = $('#insertmaindb').attr('on');
   
   var tagnchk = $('#chktagval').val();
   
    if(stses==1 && Dbtag2=='' && chkrtnattr !='rtnsyes' && tagnchk!='0'){       
        
      if(navigator.userAgent.match(/(iPad)|(iPhone)|(iPod)|(android)|(webOS)/i)){  
        
       }else{
           $chktagpost(group, reloadfeed);
           return false;
       }
      
    }
    
    if (!err) {
        $('#cat-req-msg').fadeOut('slow');
        $('#insertmaindb').removeAttr('onclick');
        $('#insertmaindb').addClass('processBtnLoader').append(' <i class="fa fa-spinner fa-spin"></i>');
        var url = BASE_URL + "/myhome/dbinsertdata";
        var Miscecval = $('.categoryList input:checkbox[cat-name="Miscellaneous"]').val();

        cat = typeof(cat) != 'undefined' ? cat : Miscecval;
        if (cat == '') cat = Miscecval;


        createrandontoken(); 
        // creting user session and token for request pass

       // data += "&pic=" + dbpicture + "&cat=" + cat + "&group=" + group + "&events=" + events + "&adddbtoleague=" + selectedLeague + "&lgenddate=" + lgenddate + "&DbTag=" + Dbtag2 + '&' + userdetails + '&' + groupname;
        if (dbeetype == 'Polls') {
            var dataNew = {pic:dbpicture,cat:cat,group:group,events:events,adddbtoleague:selectedLeague,lgenddate:lgenddate,groupname:groupname};
        }else{
            var dataNew = {dbeeTag:dbeeTag,pic:dbpicture,cat:cat,group:group,events:events,adddbtoleague:selectedLeague,lgenddate:lgenddate,DbTag:Dbtag2,groupname:groupname};
        }        
        
        margeArray(data, dataNew);
        margeArray(data, userdetails2);
        margeArray(data,{'QASTATUS':QASTATUS});

       if (PrivatePost == 1 && TaggedUsers=='' && AdminUserSet=='') {
         $dbConfirm({
        content: 'You cannot post privately without tagging another user. Do you wish to post this publicly?',                
        yesClick: function() {
        $.ajax({
            url:url,
            type: 'POST',
            processData: false,
            contentType : 'multipart/form-data',
            data: data,
            beforeSend:function (){
                 if(QASTATUS=='1' || QASTATUS==1){
                   $dbConfirm({content:'Please wait, redirecting to the post page...', yes: false, no:false});
                 }
            },
            success:function(GetResult){

             $("#DbTag").tagit("removeAll");
             var frompop = $('#frompop').val();

            
            // in case of invalid authentication
            var resultArr = GetResult.split('~');
             
            if (resultArr[0] == 'errors') 
            {
                $dbConfirm({
                    content: resultArr[1],
                    yes: false,
                    error: true
                });
                $('#insertmaindb').attr('onclick', 'javascript:postdbee(0);').removeClass('processBtnLoader');
                $('#insertmaindb .fa').remove();
                return false;
            }
            else if(localTick == false)
            {
                socket.emit('checkdbee', true,clientID);
                callsocket();
                console.log(resultArr);
                if(QASTATUS=='1' || QASTATUS==1){
                    window.location.href=resultArr[10];
                }
            }
            
           twittertag = typeof(twittertag) != 'undefined' ? twittertag : '';

            if (resultArr[0] == '1') {
                if (resultArr[4] !== '' || resultArr[4] != '# hashtag/keyword') {
                    if(twittertag!='')
                    {
                     searchnsaverwitter(resultArr[4], resultArr[3]);
                    }
                }
                $(".check").each(function() {
                    if ($(this).attr('checked')) {
                        $(this).removeAttr("checked");
                    }
                });

                document.getElementById('signuploader').style.display = 'none';
                if (reloadfeed == '1') {
                    $('.closePostPop').trigger('click');
                    if (group == '' && events == '') {
                        $('#dbee-feeds').fadeOut('slow').load(BASE_URL + '/myhome/dbeereload/5', function() {}).fadeIn("slow");
                    }
                } else {
                    $('.closePostPop').trigger('click');
                }

                var events = $('#eventid').val();
                var events = typeof(events) != 'undefined' ? events : '';
                var groupname = $('#groupname').val();
                if (events) {
                    $('.noFound').remove();
                    loadeventFeeds(resultArr[3]);
                } else if (groupidmaster!=0)
                    loadgroupdbees(groupidmaster);
                else if(groupidmaster===0){
                        fetchintialfeeds('0');
                }
                else if (startbutid == 'Dashboarduser' || startbutid == 'dashboarduser') 
                {
                    $('.customDashboard li a.active').removeClass('active');
                    seeglobaldbeelist(SESS_USER_ID, 4, 'my-dbees-profile', 'myhome', 'mydbee', 'mydb')
                }    
                else  
                {
                     if(controllernamed!='group')                       
                        fetchintialfeeds('0');
                }

                if (resultArr[5] != '') lgmsg = ' post added to league successfully';
                    lgmsg = typeof(lgmsg) != 'undefined' ? lgmsg : '';

                if (resultArr[7] == '1') {
                    $dbConfirm({
                        content: "Thank you, your post has been submitted for admin review." + lgmsg,
                        yes: false
                    });
                }
                $('#snc').val('')
                $("#rssadded").val('');
            }


            }
        })// end ajax
       
        },
          noClick: function() {
           $('.fa-spinner').remove();
            $('#insertmaindb').attr('onclick', 'javascript:postdbee(0);').removeClass('processBtnLoader');                
           return false;
        }
      });


    }
    else
    {
        $.ajax({
            url:url,
            type: 'POST',
            data: data,            
            beforeSend:function (){
                 if(QASTATUS=='1' || QASTATUS==1){
                    $dbConfirm({content:'Please wait, redirecting to the post page...', yes: false, no:false});
                 }
            },
            success:function(GetResult){

            $("#DbTag").tagit("removeAll");
            var frompop = $('#frompop').val();
            // in case of invalid authentication
            var resultArr = GetResult.split('~');
            if (resultArr[0] == 'errors') 
            {
                $dbConfirm({
                    content: resultArr[1],
                    yes: false,
                    error: true
                });
                $('#insertmaindb').attr('onclick', 'javascript:postdbee(0);').removeClass('processBtnLoader');
                $('#insertmaindb .fa').remove();
                return false;
            }else if(localTick == false){
                socket.emit('checkdbee', true,clientID);
                callsocket();
                if(QASTATUS=='1' || QASTATUS==1){
                    console.log(resultArr[10]);
                    window.location.href=resultArr[10];
                }
            }

            twittertag = typeof(twittertag) != 'undefined' ? twittertag : '';

            if (resultArr[0] == '1') {
                if (resultArr[4] !== '' || resultArr[4] != '# hashtag/keyword') {
                    if(twittertag!='')
                    { 
                     searchnsaverwitter(resultArr[4], resultArr[3]);
                    }
                }
                $(".check").each(function() {
                    if ($(this).attr('checked')) {
                        $(this).removeAttr("checked");
                    }
                });

                document.getElementById('signuploader').style.display = 'none';
                if (reloadfeed == '1') {
                    $('.closePostPop').trigger('click');
                    if (group == '' && events == '') {
                        $('#dbee-feeds').fadeOut('slow').load(BASE_URL + '/myhome/dbeereload/5', function() {}).fadeIn("slow");
                    }
                } else {
                    $('.closePostPop').trigger('click');
                }
                
                var events = $('#eventid').val();
                var events = typeof(events) != 'undefined' ? events : '';
                var groupname = $('#groupname').val();
                if (events) {
                    $('.noFound').remove();
                    loadeventFeeds(resultArr[3]);
                } else if (groupidmaster!=0)
                    loadgroupdbees(groupidmaster);
                else if(groupidmaster===0)
                        fetchintialfeeds('0');
                else if (startbutid == 'Dashboarduser' || startbutid == 'dashboarduser') {
                        $('.customDashboard li a.active').removeClass('active');
                        seeglobaldbeelist(SESS_USER_ID, 4, 'my-dbees-profile', 'myhome', 'mydbee', 'mydb')
                }
                else
                {
                     if(controllernamed!='group')                       
                        fetchintialfeeds('0');
                }
                    
     
                if (resultArr[5] != '') lgmsg = ' post added to league successfully';
                    lgmsg = typeof(lgmsg) != 'undefined' ? lgmsg : '';


                if (resultArr[7] == '1') {
                    $dbConfirm({
                        content: "Thank you, your post has been submitted for admin review." + lgmsg,
                        yes: false
                    });
                }
                $('#snc').val('')

            }


            }
        })// end ajax

    }

    }
   
}


var $chktagpost = function(group, reloadfeed){    
    var rtn = false;
    cnfhtml = '<div id="tagmsgcon" style="display:none"><div class="msg">Why not add a <span>#tag</span>, it will help your posts visibilty across the platform.</div><div class="cnfFooter"><a href="javascript:void(0)" class="btn" id="tagmsgconnobtn"  class="noConfirm">Okay, let me add now</a><a href="javascript:void(0)" class="btn btn-yellow" id="tagmsgconyesbtn" class="yesConfirm">Post without #tag</a></div>';
    var chktexttemp = '<div class="taglebeldiv"><label class="labelCheckbox" for="tagmsgconsme">\
                <input id="tagmsgconsme" type="checkbox" name="tagmsgconsme" value="0">\
                <label class="checkbox" for="tagmsgconsme"></label>\
                Don\'t show me again\
                </label></div></div>';
    cnfhtml = cnfhtml+chktexttemp;
    if ($('#tagmsgcon').is(":visible") == true) 
    $('#tagmsgcon').remove();

    $('#dbeePopupWrapper .dbHasTage').prepend(cnfhtml);    
    $('.dbHasTage').closest('#dbeePopupWrapper').find('.dbeePopContent').addClass('hasTageInter');
    $('#tagmsgcon').fadeIn();
    $('#postTypeContent .formRow.dbHasTage').addClass('active');

     $('body').on('click','#tagmsgconyesbtn',function(){               
        //$('#tagmsgcon').remove();    
        $('#insertmaindb').attr('on',"rtnsyes");         
       // postdbee(group, reloadfeed);
       $('#insertmaindb').trigger('click');
      
       return false;
    });

    $('body').on('click','#tagmsgconnobtn',function(){  
        $('#tagmsgcon').remove();      
        $('#insertmaindb').attr('on',"rtnsno");     
        $('#DbTag input').focus(); 
        $('#postTypeContent .formRow.dbHasTage').removeClass('active');
        return false;         
    });

    /*$('body').on('change','#tagmsgconsme',function(){       
         var chk = '';
         if ($('input:checkbox[name=tagmsgconsme]').is(':checked') == true) {
            chk = 0;
        }
         
         console.log(chk);
         var data = {'chk':chk}
                   $.ajax({
                    type: "POST",
                    dataType: 'json',
                    data: data,
                    url:  BASE_URL+'/myhome/updateshowtagmsg',                    
                    success: function(response){

                    }       
                });
            });*/
}



var SearchStoreTweetsHttp;

function searchnsaverwitter(keyword, db) {

    SearchStoreTweetsHttp = Browser_Check(SearchStoreTweetsHttp);
    var url = BASE_URL + "/edf/index";
    var data = "keyword=" + keyword + "&db=" + db + "&update=  " + 1;

    SearchStoreTweetsHttp.open("POST", url, true);
    SearchStoreTweetsHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    SearchStoreTweetsHttp.setRequestHeader("Content-length", data.length);
    SearchStoreTweetsHttp.setRequestHeader("Connection", "close");
    SearchStoreTweetsHttp.onreadystatechange = searchnsaverwitterresult;
    SearchStoreTweetsHttp.send(data);
}

function searchnsaverwitterresult() {
        if (SearchStoreTweetsHttp.readyState == 4) {
            if (SearchStoreTweetsHttp.status == 200 || SearchStoreTweetsHttp.status == 0) {
                var result = SearchStoreTweetsHttp.responseText;

            } else {};
        }
    }
    // OPEN EDIT DBEE

function openeditdbee() {
    document.getElementById('non-editable').style.display = 'none';
    document.getElementById('editable').style.display = 'block';
    var dbtype = document.getElementById('dbeetype_edit').value;
    if (dbtype == '1') document.getElementById('PostText1').focus();
    else if (dbtype == '2') document.getElementById('PostLinkDesc1').focus();
    else if (dbtype == '3') document.getElementById('PostPixDesc1').focus();
    else if (dbtype == '4') document.getElementById('PostVidzDescComm1').focus();
}
var EditdbeeHttp;

function editdbee() {
    var id = document.getElementById('dbid').value;
    var err = false;
    var dbeetype = document.getElementById('dbeetype_edit').value;

    if (dbeetype == '1') {
        var text = document.getElementById('PostText1').value;
        text = text.replace(/&/g, '%26');
        if (text != "" && text != "what's on your mind?") {
            var data = "text=" + text + "&id=" + id + "&dbeetype=" + dbeetype;
        } else err = true;
    }

    if (dbeetype == '2') {
        var userlinkdesc = document.getElementById('PostLinkDesc1').value;
        userlinkdesc = userlinkdesc.replace(/&/g, '%26');
        if (userlinkdesc != "") {
            var data = "userlinkdesc=" + userlinkdesc + "&id=" + id + "&dbeetype=" + dbeetype;
        } else err = true;
    }

    if (dbeetype == '3') {
        var picdesc = document.getElementById('PostPixDesc1').value;
        picdesc = picdesc.replace(/&/g, '%26');
        if (picdesc != "") {
            var data = "picdesc=" + picdesc + "&id=" + id + "&dbeetype=" + dbeetype;
        } else err = true;
    }

    if (dbeetype == '4') {
        var viddesc = document.getElementById('PostVidzDescComm1').value;
        viddesc = viddesc.replace(/&/g, '%26');
        if (viddesc != "") {
            var data = "viddesc=" + viddesc + "&id=" + id + "&dbeetype=" + dbeetype;
        } else err = true;
    }
    if (!err) {
        EditdbeeHttp = Browser_Check(EditdbeeHttp);
        var url = BASE_URL + "/dbeedetail/editdbee";
        EditdbeeHttp.open("POST", url, true);
        EditdbeeHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        EditdbeeHttp.setRequestHeader("Content-length", data.length);
        EditdbeeHttp.setRequestHeader("Connection", "close");

        EditdbeeHttp.onreadystatechange = editdbeeresult;
        EditdbeeHttp.send(data);
    }
}

function editdbeeresult() {
    if (EditdbeeHttp.readyState == 4) {
        if (EditdbeeHttp.status == 200 || EditdbeeHttp.status == 0) {
            var GetResult = EditdbeeHttp.responseText;
            var resultArr = GetResult.split('~#~');
            if (resultArr[0] == 1) {
                document.getElementById('editable').style.display = 'none';
                document.getElementById('non-editable').innerHTML = resultArr[1];
                document.getElementById('non-editable').style.display = 'block';
                $('#msg-db-updated').fadeIn('slow');
                setTimeout("$('#msg-db-updated').fadeOut('slow')", 2000);
            }
        } else {};
    }
}

function fetchadvertmessage() {
    $.ajax({
        type: "POST",
        dataType: 'json',
        url: BASE_URL + "/advert/fetch",
        success: function(response) {
            if (response.rightbanner) {
                $('#rightListingads').prepend(response.rightbanner);
                if (response.slidershow == 1)
                    $('.rightbannerAdvertisement').flexslider({
                        useCSS: false,
                        animation: response.rightbannereffect,
                        slideshowSpeed: response.rightbannerspeed,
                        controlNav: false,
                        directionNav: false
                    });
            }
            if (response.headerposition == 'full' && response.headerbanner) {
                $('.brandLogo').remove();
                $('.userWelcomeMsg').addClass('fullHeaderBanner').css('float', 'none');
                $('.userWelcomeMsg').css('margin-right', '0px');
            } else
                $('.brandLogo').html(response.logo);

            if (response.headerbanner) {
                $('.userWelcomeMsg').html(response.headerbanner);
                $('.flideslide').flexslider({
                    useCSS: false,
                    controlNav: false,
                    directionNav: false,
                    animation: response.headerbannereffect,
                    slideshowSpeed: response.headerbannerspeed
                });
            }
        }
    });
}

function fetchadvert(page) { 
    $.ajax({
        type: "POST",
        dataType: 'json',
        url: BASE_URL + "/advert/fetch",
        success: function(response) { 
            if (response.rightbanner) 
            {
                if(page=='viewAll')
                    $('#rightListing').append(response.rightbanner);
                else
                    $('#rightListing').prepend(response.rightbanner);
                if (response.slidershow == 1)
                {
                    $('.rightbannerAdvertisement').flexslider({
                        useCSS: false,
                        animation: response.rightbannereffect,
                        slideshowSpeed: response.rightbannerspeed,
                        controlNav: false,
                        directionNav: false
                    });
                }
            }
            if (response.headerposition == 'full' && response.headerbanner) {
                $('.brandLogo').remove();
                $('.userWelcomeMsg').addClass('fullHeaderBanner').css('float', 'none');
                $('.userWelcomeMsg').css('margin-right', '0px');
            } else
                $('.brandLogo').html(response.logo);

            if (response.headerbanner) {
                $('.userWelcomeMsg').html(response.headerbanner);
               var slide =  $('.flideslide').flexslider({
                    useCSS: false,
                    slideshowSpeed: response.headerbannerspeed,
                    controlNav: false,
                    directionNav: false
                });
               //slide.data('flexslider').resize();
            }
            getpromotedExpert();
        }
    });
}

function fetchspecificadvert(relationID, type) {
    if ($('#login').val() == '')
        return false;
    data = 'relationID=' + relationID + '&type=' + type;
    $.ajax({
        type: "POST",
        dataType: 'json',
        data: data,
        url: BASE_URL + "/advert/fetchrelation",
        success: function(response) 
        {
            if(response.rightbannerspeed)
                rightbannerspeed = response.rightbannerspeed;
            else
                rightbannerspeed = 3000;

            if(response.headerbannerspeed!='' && response.headerbannerspeed!=0)
                headerbannerspeed = response.headerbannerspeed;
            else
                headerbannerspeed = 3000;

            if (response.rightbanner!='') 
            {
                if (type == 7) {
                    $('#eventAdvert').html(response.rightbanner);
                    $('#dbeeEdvert').html(response.rightbanner);
                } else if (type == 5) {
                    $('#dbeeEdvert').html(response.rightbanner);
                } else if (type == 2) {
                    $('#groupEdvert').html(response.rightbanner);
                } else if (type == 4) { 
                    $('#userEdvert').html(response.rightbanner);
                }

                if (response.slidershow == 1){
                    $('.rightbannerAdvertisement').flexslider({
                        useCSS: false,
                        animation: response.rightbannereffect,
                        slideshowSpeed: rightbannerspeed,
                        controlNav: false,
                        directionNav: false
                    });
                }
            }

            if (response.headerposition == 'full' && response.headerbanner!='') 
            {
                $('.brandLogo').remove();
                $('.userWelcomeMsg').addClass('fullHeaderBanner').css('float', 'none');
                $('.userWelcomeMsg').css('margin-right', '0px');
            } else
                $('.brandLogo').html(response.logo);

            if (response.headerbanner!='') 
            {   
                $('.userWelcomeMsg').html(response.headerbanner);
               $(window).load(function(){
                
                 $('.flideslide').flexslider({
                    useCSS: false,
                    controlNav: false,
                    directionNav: false,
                    animation: response.headerbannereffect,
                    slideshowSpeed: headerbannerspeed
                });
             });
            }
        }
    });
}

function fetchintialfeeds(showloader) {
    typedbee = '';
    showloader = typeof(showloader) != 'undefined' ? showloader : '1';
    $('#all-dbees-home').addClass('active');
    $('#dbee-feed-favourite').removeClass('active');
    $('#dbee-feed-following').removeClass('active');
    $('#my-comments-home').removeClass('active');
    $('#my-dbees-home').removeClass('active');
    $('#dbee-feed-category').removeClass('active');
    $('#dbee-feed-displayby').removeClass('active');
    $('.tabLinks').remove();
    if (showloader == '1')
        $('#dbee-feeds').html('<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>');
    if (showloader == '0') {
        var end = $('#reloadend').val();
        end = parseInt(end) + 5;
    }
    type = $('#feedtype').val();
   
    $.ajax({
        url: BASE_URL + '/myhome/fetchdbee',
        type: 'POST',
        data: {
            check: "0",
            initial: "1",
            end: end,
            type: type
        },
        dataType: 'html',
        success: function(respons) 
        {
            var response = $.parseJSON(respons);
            
            $('#feedtype').val(type);
            $('#dbee-feeds').html(response.content);
            $('#my-dbees').html(response.content);
            $('#startnewall').val(response.startnew);
            // RESET NEW DB NOTES ON ALL
            $('#notifications-top-wrapper-dbee').hide();
            $('#notifications-top-dbee').html(parseInt(0));

            if(response.end==5)
            {
                $('#my-dbees').html(response.content);
            }
            else
            {
                $('#reloadend').val(response.end);
                $('#see-more-feeds' + start).html(response.content);
                $('#startnewall').val(response.startnew);
            }
            
            // mention
            $.each(response, function(index, value) {              
                    if (index != '') {
                        $('.mentionto_' + index).mentionsInput({
                            onDataRequest: function(mode, query, callback) {

                                data = $.parseJSON(value);
                                responseDataMy = _.filter(data, function(item) {
                                    return item.name.toLowerCase().indexOf(query.toLowerCase()) > -1
                                });
                                console.log(responseDataMy);
                                callback.call(this, responseDataMy);
                            }
                        });
                    }
                });
            $('.rightTwitterDetails').css('margin-left','90px');
        }
    });

       

}


function getMentionUser(DbArray) 
{	
    if($('#login').val()=='')
		return false;

        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {
                DbArray: DbArray
            },
            url: BASE_URL + "/ajax/usermention",
            success: function(response) {
                $.each(response.html, function(index, value) {
                    if (index != '') {
                        $('.mentionto_' + index).mentionsInput({
                            onDataRequest: function(mode, query, callback) {
                                data = $.parseJSON(value);
                                responseDataMy = _.filter(data, function(item) {
                                    return item.name.toLowerCase().indexOf(query.toLowerCase()) > -1
                                });
                                console.log(responseDataMy);
                                callback.call(this, responseDataMy);
                            }
                        });
                    }
                });
                // end  init function
            }
        });
    }



    /* jquery ajax for fetchinitialfeeds */
    // FETCH INTIAL 5 FEEDS ON HOMEPAGE WHEN MY COMMENTS IS CLICKED
var MyCommentsFeedHttp;

function dbfeedmycomments(n, id) {


    n = typeof(n) != 'undefined' ? n : '0';
    id = typeof(id) != 'undefined' ? id : '0';
    if (n == 0) {
        $('#dbee-feeds').html('<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>');
        $('#postMenuLinks li a:not(#rssFeed a)').removeClass('active');
        $('#my-comments-home').addClass('active');
    } else if (n == 1) {
        $('#my-dbees').html('<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>');
        if (document.getElementById('my-redbees-profile'))
            $('#my-comments-profile').addClass('active');
        $('my-following-profile, #my-followers-profile, #my-leaguepos-profile, #my-redbees-profile').addClass('feed-link feed-link-border');
        $('#my-groups-profile').addClass('popOver feed-link');
    }

    var OtherUser = $(this).attr('data-OtherUser');
        OtherUser = typeof(OtherUser) != 'undefined' ? OtherUser : '0';


    var postUrl = BASE_URL + "/Comment/index";
    // var data = "user=" + id;
    $.ajax({
        url: postUrl,
        type: 'POST',
        data: {
            'user': id
        },
        success: function(result) {
            var resultArr = result.split('~#~');
            $('#feedtype').val('mycomments');
            if ($('#dbee-feeds').is(':visible') == true) {
                $('#dbee-feeds').html(resultArr[0]);
            } else {
                $('.userproLinks a').removeClass('active');
                $('#my-comments-profile').addClass('active');
                $('#my-dbees').html(resultArr[0]);
            }
            $('#startnewmycomments').val(PAGE_NUM);
        }
    });


}



function dbfeedmycommentsresultfromhome() {
        if (MyCommentsFeedHttp.readyState == 4) {
            if (MyCommentsFeedHttp.status == 200 || MyCommentsFeedHttp.status == 0) {
                var result = MyCommentsFeedHttp.responseText;
                var resultArr = result.split('~#~');
                document.getElementById('feedtype').value = 'mycomments';
                document.getElementById('dbee-feeds').innerHTML = resultArr[0];
                document.getElementById('startnewmycomments').value = PAGE_NUM;
            } else {};
        }
    }
    // FETCH INTIAL 5 FEEDS ON HOMEPAGE WHEN FOLLOWING IS CLICKED
var FollowingFeedHttp;

function dbfeedfollowing() {
    document.getElementById('dbee-feeds').innerHTML = '<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>';
    $('#dbee-feed-following').addClass('active');
    $('#my-comments-home').removeClass('active');
    $('#all-dbees-home').removeClass('active');
    $('#my-dbees-home').removeClass('active');
    $('#dbee-feed-favourite').removeClass('active');
    $('#dbee-feed-category').removeClass('active');
    $('#dbee-feed-displayby').removeClass('active');
    //document.getElementById('startnewfollowing').value='5';
    //	changedbfeedtabClass(2);
    FollowingFeedHttp = Browser_Check(FollowingFeedHttp);

    var url = BASE_URL + "/following/index";
    var data = "";
    FollowingFeedHttp.open("POST", url, true);
    FollowingFeedHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    FollowingFeedHttp.setRequestHeader("Content-length", data.length);
    FollowingFeedHttp.setRequestHeader("Connection", "close");
    FollowingFeedHttp.onreadystatechange = dbfeedfollowingresult;
    FollowingFeedHttp.send(data);
}

function dbfeedfollowingresult() {
        if (FollowingFeedHttp.readyState == 4) {
            if (FollowingFeedHttp.status == 200 || FollowingFeedHttp.status == 0) {
                var result = FollowingFeedHttp.responseText;
                var resultArr = result.split('~#~');
                document.getElementById('notifications-top-hidden').value = 0;
                document.getElementById('feedtype').value = 'following';
                //document.getElementById('filter-label').style.display='none';
                document.getElementById('newdbcount-wrapper').style.display = 'none';
                //			document.getElementById('icon-dbfeed-buzz-following').className='icon-dbfeed-buzz';
                document.getElementById('dbee-feeds').innerHTML = resultArr[0];


                document.getElementById('startnewfollowing').value = PAGE_NUM;


            } else {};
        }
    }
    // FETCH INTIAL 5 FEEDS ON HOMEPAGE WHEN FAVOURITE IS CLICKED
var FavouriteFeedHttp;

function dbfeedfavourite() {
    document.getElementById('dbee-feeds').innerHTML = '<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>';
    $('#dbee-feed-favourite').addClass('active');
    $('#dbee-feed-following').removeClass('active');
    $('#my-comments-home').removeClass('active');
    $('#all-dbees-home').removeClass('active');
    $('#my-dbees-home').removeClass('active');
    $('#dbee-feed-category').removeClass('active');
    $('#dbee-feed-displayby').removeClass('active');
    //document.getElementById('startnewfav').value='5';
    //	changedbfeedtabClass(2);
    FavouriteFeedHttp = Browser_Check(FavouriteFeedHttp);

    var url = BASE_URL + "/favourites/index";
    var data = "";

    FavouriteFeedHttp.open("POST", url, true);
    FavouriteFeedHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    FavouriteFeedHttp.setRequestHeader("Content-length", data.length);
    FavouriteFeedHttp.setRequestHeader("Connection", "close");
    FavouriteFeedHttp.onreadystatechange = dbfeedfavouriteresult;
    FavouriteFeedHttp.send(data);
}

function dbfeedfavouriteresult() {
        if (FavouriteFeedHttp.readyState == 4) {
            if (FavouriteFeedHttp.status == 200 || FavouriteFeedHttp.status == 0) {
                var result = FavouriteFeedHttp.responseText;
                var resultArr = result.split('~#~');
                document.getElementById('feedtype').value = 'favourite';
                //document.getElementById('filter-label').style.display='none';
                document.getElementById('newdbcount').style.display = 'none';
                document.getElementById('dbee-feeds').innerHTML = resultArr[0];
                document.getElementById('startnewfav').value = PAGE_NUM;


            } else {};
        }
    }
    // FETCH INTIAL 5 FEEDS ON HOMEPAGE WHEN MOST COMMENTED IS CLICKED
var MostCommentedFeedHttp;

function dbfeedmostcommented(n) {
    n = typeof(n) != 'undefined' ? n : '0';
    document.getElementById('dbee-feeds').innerHTML = '<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>';
    if (n == 0) {
        $('#dbee-feed-favourite').removeClass('active');
        $('#dbee-feed-following').removeClass('active');
        $('#my-comments-home').removeClass('active');
        $('#all-dbees-home').removeClass('active');
        $('#my-dbees-home').removeClass('active');
        $('#dbee-feed-category').removeClass('active');
        $('#dbee-feed-displayby').removeClass('active');

        //document.getElementById('startnewmc').value='5';
    }
    //	changedbfeedtabClass(2);
    MostCommentedFeedHttp = Browser_Check(MostCommentedFeedHttp);
    var url = BASE_URL + "/myhome/mostcommented";
    var data = "";

    MostCommentedFeedHttp.open("POST", url, true);
    MostCommentedFeedHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    MostCommentedFeedHttp.setRequestHeader("Content-length", data.length);
    MostCommentedFeedHttp.setRequestHeader("Connection", "close");
    MostCommentedFeedHttp.onreadystatechange = function() {
        dbfeedmostcommentedresult(n);
    };
    MostCommentedFeedHttp.send(data);
}

function dbfeedmostcommentedresult(n) {
        if (MostCommentedFeedHttp.readyState == 4) {
            if (MostCommentedFeedHttp.status == 200 || MostCommentedFeedHttp.status == 0) {
                var result = MostCommentedFeedHttp.responseText;
                var resultArr = result.split('~#~');
                if (n == 0) {
                    document.getElementById('feedtype').value = 'mostcommented';
                    //document.getElementById('filter-label').style.display='none';
                    document.getElementById('newdbcount-wrapper').style.display = 'none';
                }
                document.getElementById('dbee-feeds').innerHTML = resultArr[0];
                document.getElementById('startnewmc').value = PAGE_NUM;


            } else alert("Retrieval Error: " + MostCommentedFeedHttp.statusText);
        }
    }
    // FETCH INTIAL 5 FEEDS ON HOMEPAGE FOR A SCORE WHEN FILTERED BY CAT
var FilterFeedCatHttp;

function dbfeedfilterbycat() {
    chklen = $('.dbfeedfilterbycat input:checked').length;
    if (chklen == 0) {
        $dbConfirm({
            content: 'Please choose your criteria to find the results',
            yes: false,
            error: true
        });
        return false;
    }

    document.getElementById('dbee-feeds').innerHTML = '<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>';
    $('#all-dbees-home,#dbee-feed-favourite,#dbee-feed-following,#my-comments-home,#my-dbees-home,#dbee-feed-category,#dbee-feed-displayby').removeClass('active');

    var cat = '';
    $('.dbfeedfilterbycat input:checked').each(function() {
        cat += $(this).val() + ', ';
    });
    document.getElementById('filtercat').value = cat;
    FilterFeedCatHttp = Browser_Check(FilterFeedCatHttp);
    var url = BASE_URL + "/myhome/catetorylist";
    var data = "cat=" + cat + "&type=category";

    FilterFeedCatHttp.open("POST", url, true);
    FilterFeedCatHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    FilterFeedCatHttp.setRequestHeader("Content-length", data.length);
    FilterFeedCatHttp.setRequestHeader("Connection", "close");
    FilterFeedCatHttp.onreadystatechange = dbfeedfilterbycatresult;
    FilterFeedCatHttp.send(data);
}

function dbfeedfilterbycatresult() {
        if (FilterFeedCatHttp.readyState == 4) {
            if (FilterFeedCatHttp.status == 200 || FilterFeedCatHttp.status == 0) {
                var result = FilterFeedCatHttp.responseText;
                var resultArr = result.split('~#~');
                document.getElementById('feedtype').value = 'cat';
                document.getElementById('dbee-feeds').innerHTML = resultArr[0];
                $("#callcatfirsttime").val(1);
                $('#startnewcat').val(PAGE_NUM);
            }
        }
    }
    // FETCH INTIAL 5 FEEDS ON HOMEPAGE FOR A SCORE WHEN FILTERED BY TYPE

    // FETCH INTIAL 5 FEEDS ON HOMEPAGE FOR A SCORE WHEN FILTERED
var FilterFeedHttp;

function dbfeedfilter() {

    chklen = $('.dbfeedfilter  input:checked').length;

    if (chklen == 0) {
        //$messageError('Please choose your criteria to find the results');
        $dbConfirm({
            content: 'Please choose your criteria to find the results',
            yes: false,
            error: true
        });
        return false;
    }


    document.getElementById('dbee-feeds').innerHTML = '<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>';

    $('#all-dbees-home,#dbee-feed-favourite,#dbee-feed-following,#my-comments-home,#my-dbees-home,#dbee-feed-category,#dbee-feed-displayby').removeClass('active');

    var score = '';

    $('.dbfeedfilter input:checked').each(function() {
        score += 's.Score = ' + $(this).val() + ' OR ';
    });
    //console.log(score);

    document.getElementById('filterscore').value = score;
    FilterFeedHttp = Browser_Check(FilterFeedHttp);
    var url = BASE_URL + "/myhome/dbfeedfilter";
    var data = "score=" + score;
    FilterFeedHttp.open("POST", url, true);
    FilterFeedHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    FilterFeedHttp.setRequestHeader("Content-length", data.length);
    FilterFeedHttp.setRequestHeader("Connection", "close");
    FilterFeedHttp.onreadystatechange = dbfeedfilterresult;
    FilterFeedHttp.send(data);
}

function dbfeedfilterresult() {
    if (FilterFeedHttp.readyState == 4) {
        if (FilterFeedHttp.status == 200 || FilterFeedHttp.status == 0) {
            var result = FilterFeedHttp.responseText;
            var resultArr = result.split('~#~');
            document.getElementById('feedtype').value = 'filter';
            document.getElementById('dbee-feeds').innerHTML = resultArr[0];
            document.getElementById('startnewfilter').value = PAGE_NUM;


        } else alert("Retrieval Error: " + FilterFeedHttp.statusText);
    }
}

function changedbfeedtabClass(n) {
    if (n == '1') {
        var chk = document.getElementById('dbee-feed-following');
        document.getElementById('dbee-feed-all').className = 'view-feed-active';
        if (chk) document.getElementById('dbee-feed-following').className = 'view-feed';
    } else if (n == '2') {
        document.getElementById('dbee-feed-all').className = 'view-feed';
        document.getElementById('dbee-feed-following').className = 'view-feed-active';
    }
}

function groupnotification() {

    $.ajax({
        type: "POST",
        dataType: 'json',
        data: {},
        url: BASE_URL + '/myhome/groupnotification',

        beforeSend: function() {},
        complete: function() {},
        cache: false,
        success: function(response) {

            if (response.TotalGroups != '0' && response.TotalGroups != '' && typeof(response.TotalGroups) !== 'undefined') {
                $('#notifications-group-top-wrapper').css('display', 'block');
                $('#notifications-group-top').html(response.TotalGroups);
                $('#curr-notification-count').val(response.TotalGroups);

                var notegrpinvitehidden = $('#notifications-top-grpinvite-hidden').val();
                if (notegrpinvitehidden != response.TotalGroupInvite) {
                    $ghostMessage('<strong>' + response.TGIRow + '</strong> just invited you to his group.');
                    $('#notifications-top-grpinvite-hidden').val(response.TotalGroupInvite);

                }

                var notegrpreqhidden = $('#notifications-top-grpreq-hidden').val();
                if (notegrpreqhidden != response.TotalGroupRequests) {
                    $ghostMessage('<strong>' + response.TGIRow + '</strong> just requested to join your group.');
                    $('#notifications-top-grpreq-hidden').val(response.TotalGroupRequests);
                }
            } else {
                $('#notifications-group-top-wrapper').css('display', 'none');
            }
        }

    });
}

function chktopnotification(n) {

    $.ajax({
        type: "POST",
        dataType: 'json',
        data: {
            'checkFlag': +n
        },
        url: BASE_URL + '/myhome/chknotification',

        async: false,
        beforeSend: function() {},
        complete: function() {},
        cache: false,
        success: function(response) {

            // NEW DB NOTIFICATION
            if (response.TotalDbees != '0' && response.TotalDbees != '' && typeof(response.TotalDbees) !== 'undefined') {


                var notedbhidden = $('#notifications-top-hidden').val();

                if (notedbhidden != response.TotalDbees) {
                    $ghostMessage('<strong>' + response.TDBRow + '</strong> just added a new dbee.');
                    $('#notifications-top-hidden').val(response.TotalDbees);
                }
            } else
                $('#notifications-top-wrapper').css('display', 'none');

            // NEW SCORE NOTIFICATION		            
            if (response.TotalScore != '0' && response.TotalScore != '' && typeof(response.TotalScore) !== 'undefined') {
                var notescorehidden = $('#notifications-top-score-hidden').val();
                if (notescorehidden != response.TotalScore) {

                    if (response.ScoreText != 'food for thought')
                        $ghostMessage('<strong>' + response.ScoreUser + ' </strong> ' + response.ScoreText + ' your <strong>' + response.ScoreType + '</strong><br /><strong><i>' + response.excerpt + '</i></strong>');
                    else
                        $ghostMessage('<strong>' + response.ScoreUser + '</strong> think your <strong>' + response.ScoreType + '</strong> is ' + response.ScoreText + '<br /><strong><i>' + response.excerpt + '</i></strong>');
                    $('#notifications-top-score-hidden').val(response.TotalScore);
                }
            } else {}
            // *****************************************

            // NEW COMMENT NOTIFICATION		            
            if (response.TotalComments != '0' && response.TotalComments != '' && typeof(response.TotalComments) !== 'undefined') {

                // $('#sticky-left-newcomm').show('slow');                	
                if (response.mycomment == 0) {

                    var commentdbeeid = $('#dbid').val();
                    if (response.excerpt != commentdbeeid) {
                        //alert('comment forund2');
                        var notecommenthidden = $('#notifications-top-comment-hidden').val();
                        //alert(notecommenthidden);
                        if (response.TotalComments != 0 && notecommenthidden != response.TotalComments) {

                            if (response.TotalComments > notecommenthidden) {
                                $ghostMessage('<strong>' + response.CommentUserName + '</strong> commented on a post you are involved in.');
                                $('#notifications-top-comment-hidden').val(response.TotalComments);
                            } else if (response.TotalComments < notecommenthidden) {
                                $('#sticky-left-newcomm-num').html(response.TotalComments);
                                $ghostMessage('<strong>' + response.CommentUserName + '</strong> removed his comment from a post you are involved in.');
                                $('notifications-top-comment-hidden').val(response.TotalComments);
                            }
                        }
                    }
                    if (response.TotalComments == 0) $('#notifications-top-comment-hidden').val('0');
                }
            } else {
                if (response.TotalComments == 0) {
                    $('#sticky-left-newcomm').css('display', 'none');
                    $('#sticky-left-newcomm').hide('slow');
                }
            }
            // ***************************************** 	

            // NEW MENTION NOTIFICATION		          
            if (response.mymention != '0' && response.mymention != '' && typeof(response.mymention) !== 'undefined') {
                //$('#notifications-top-wrapper').css('display','block');              
                //$('#notifications-top').html(response.totalnotify);              
                //$('#curr-notification-count').val(response.TotalMention);                   		                
                //$('#notifications-onpage-wrapper').css('display','block');  
                //$('#notifications-onpage-wrapper').html(response.TotalMention);		                
                var notedbhidden = $('#notifications-top-hidden').val();
                var mentionhidden = $('#chkmention-hidden').val();
                if (mentionhidden != response.TotalMention) {
                    $ghostMessage('<strong>' + response.MentionName + '</strong> mentioned you in comment.');
                    $('#chkmention-hidden').val(response.TotalMention);
                }
            } else
                $('#notifications-onpage-wrapper').css('display', 'none');

            // *****************************************		           
            // NEW REDBEE		           
            if (response.myredbee != '0' && response.myredbee != '' && typeof(response.myredbee) !== 'undefined') {

                //$('#notifications-top-wrapper').css('display','block');              
                //$('#notifications-top').html(response.totalnotify);              
                //$('#curr-notification-count').val(response.TotalRedbee);            

                //$('#notifications-onpageredbee-wrapper').css('display','block');  
                //$('#notifications-onpageredbee-wrapper').html(response.TotalRedbee);

                var redbeehidden = $('#redbee-hidden').val();
                if (redbeehidden != response.TotalRedbee) {
                    $ghostMessage('<strong>' + response.RedbeeName + '</strong> re posts your post');
                    $('#redbee-hidden').val(response.TotalRedbee);
                }
            } else
                $('#notifications-onpageredbee-wrapper').css('display', 'none');

            // NEW Follow	

            if (response.following != '0' && response.following != '' && typeof(response.following) !== 'undefined') {

                var follownotify = $('#follownotify-hidden').val();
                if (follownotify != response.TotalFollwing) {
                    $ghostMessage('<strong>' + response.followingname + '</strong> now follows you');
                    $('#follownotify-hidden').val(response.TotalFollwing);
                }
            } else
            //  $('#notifications-onpageredbee-wrapper').css('display','none');  

            // *****************************************
            // NEW MESSAGE NOTIFICATION
            if (response.TotalMessages != '0' && response.TotalMessages != '' && typeof(response.TotalMessages) !== 'undefined') {
                /*$('#notifications-msg-top-wrapper').css('display','block');              
	                 $('#notifications-msg-top').html(response.TotalMessages);              
	                 $('#curr-notification-count').val(response.TotalMessages);   */

                var notemsghidden = $('#notifications-top-msg-hidden').val();
                if (notemsghidden != response.TotalMessages) {
                    $ghostMessage('<strong>' + response.TMRow + '</strong> just sent you a new message.');
                    $('#notifications-top-msg-hidden').val(response.TotalMessages);

                }
            } else
                $('#notifications-msg-top-wrapper').css('display', 'none')

            // *****************************************
            // NEW GROUP INVITE/REQUEST NOTIFICATION
            if (response.TotalGroups != '0' && response.TotalGroups != '' && typeof(response.TotalGroups) !== 'undefined') {
                /*$('#notifications-group-top-wrapper').css('display','block');
                $('#notifications-group-top').html(response.TotalGroups);
                $('#curr-notification-count').val(response.TotalGroups);*/

                var notegrpinvitehidden = $('#notifications-top-grpinvite-hidden').val();
                if (notegrpinvitehidden != response.TotalGroupInvite) {
                    $ghostMessage('<strong>' + response.TGIRow + '</strong> just invited you to his group.');
                    $('#notifications-top-grpinvite-hidden').val(response.TotalGroupInvite);
                }

                var notegrpreqhidden = $('#notifications-top-grpreq-hidden').val();
                if (notegrpreqhidden != response.TotalGroupRequests) {
                    $ghostMessage('<strong>' + response.TGRRow + '</strong> just requested to join your group.');
                    $('#notifications-top-grpreq-hidden').val(response.TotalGroupRequests);
                }
            } else {
                $('#notifications-group-top-wrapper').css('display', 'none');
            }


        },

        error: function(request, status, error) {

            $("#content_data").html("Some problems have occured. Please try again later: " + error);

        }

    });
}

function dbeenotification() {
    $.ajax({
        type: "POST",
        dataType: 'json',
        data: {
            'checkFlag': +n
        },
        url: BASE_URL + '/dbeenotification',

        async: false,
        cache: false,
        success: function(response) {
            // NEW DB NOTIFICATION
            if (response.TotalDbees != '0' && response.TotalDbees != '' && typeof(response.TotalDbees) !== 'undefined') {
                $('#notifications-top-wrapper').css('display', 'block');
                $('#notifications-top').html(response.totalnotify);
                $('#notifications-onpagedbee-wrapper').css('display', 'block');
                $('#notifications-onpagedbee-wrapper').html(response.TotalDbees);
                $('#curr-notification-count').val(response.TotalDbees);

                var notedbhidden = $('#notifications-top-hidden').val();

                if (notedbhidden != response.TotalDbees) {
                    $ghostMessage('<strong>' + response.TDBRow + '</strong> just added a new post.');
                    $('#notifications-top-hidden').val(response.TotalDbees);

                }
            } else
                $('#notifications-top-wrapper').css('display', 'none');
            // NEW SCORE NOTIFICATION		            
            if (response.TotalScore != '0' && response.TotalScore != '' && typeof(response.TotalScore) !== 'undefined') {
                var notescorehidden = $('#notifications-top-score-hidden').val();

                if (notescorehidden != response.TotalScore) {

                    if (response.ScoreText != 'food for thought')
                        $ghostMessage('<strong>' + response.ScoreUser + '</strong> ' + response.ScoreText + ' your <strong>' + response.ScoreType + '</strong><br /><strong><i>' + response.excerpt + '</i></strong>');
                    else
                        $ghostMessage('<strong>' + response.ScoreUser + '</strong> think your <strong>' + response.ScoreType + '</strong> is ' + response.ScoreText + '<br /><strong><i>' + response.excerpt + '</i></strong>');
                    $('#notifications-top-score-hidden').val(response.TotalScore);
                }
            } else {}
            // *****************************************
            // NEW COMMENT NOTIFICATION		            
            if (response.TotalComments != '0' && response.TotalComments != '' && typeof(response.TotalComments) !== 'undefined') {
                $('#sticky-left-newcomm').show('slow');
                if (response.mycomment == 0) {
                    var commentdbeeid = $('#dbid').val();
                    //if(response.excerpt!=commentdbeeid){	                  
                    var notecommenthidden = $('#notifications-top-comment-hidden').val();
                    if (response.TotalComments != 0 && notecommenthidden != response.TotalComments) {
                        if (response.TotalComments > notecommenthidden) {
                            $('#sticky-left-newcomm-num').html(response.TotalComments);
                            $('#sticky-left-newcomm').fadeIn('slow');

                            $ghostMessage('<strong>' + response.CommentUserName + '</strong> commented on a post you are involved in.');
                            $('#notifications-top-comment-hidden').val(response.TotalComments);

                        } else if (response.TotalComments < notecommenthidden) {
                            $('#sticky-left-newcomm-num').html(response.TotalComments);
                            $ghostMessage('<strong>' + response.CommentUserName + '</strong> removed his comment from a post you are involved in.');
                            $('notifications-top-comment-hidden').val(response.TotalComments);
                        }


                    }
                }
                if (response.TotalComments == 0) $('#notifications-top-comment-hidden').val('0');
                // }
            } else {
                if (response.TotalComments == 0) {
                    $('#sticky-left-newcomm').css('display', 'none');
                    $('#sticky-left-newcomm').hide('slow');
                }
            }
            // ***************************************** 		          
            // NEW MENTION NOTIFICATION		          
            if (response.mymention != '0' && response.mymention != '' && typeof(response.mymention) !== 'undefined') {
                $('#notifications-top-wrapper').css('display', 'block');
                $('#notifications-top').html(response.totalnotify);
                $('#curr-notification-count').val(response.TotalMention);
                $('#notifications-onpage-wrapper').css('display', 'block');
                $('#notifications-onpage-wrapper').html(response.TotalMention);
                var notedbhidden = $('#notifications-top-hidden').val();
                var mentionhidden = $('#chkmention-hidden').val();
                if (mentionhidden != response.TotalMention) {
                    $ghostMessage('<strong>' + response.MentionName + '</strong> mention you in comment.');
                    $('#chkmention-hidden').val(response.TotalMention);
                }
            } else
                $('#notifications-onpage-wrapper').css('display', 'none');

            // *****************************************		           
            // NEW REDBEE		           
            if (response.myredbee != '0' && response.myredbee != '' && typeof(response.myredbee) !== 'undefined') {

                $('#notifications-top-wrapper').css('display', 'block');
                $('#notifications-top').html(response.totalnotify);
                $('#curr-notification-count').val(response.TotalRedbee);

                $('#notifications-onpageredbee-wrapper').css('display', 'block');
                $('#notifications-onpageredbee-wrapper').html(response.TotalRedbee);

                var redbeehidden = $('#redbee-hidden').val();

                if (redbeehidden != response.TotalRedbee) {
                    $ghostMessage('<strong>' + response.RedbeeName + '</strong> re post your post.');
                    $('#redbee-hidden').val(response.TotalRedbee);
                }
            } else
                $('#notifications-onpageredbee-wrapper').css('display', 'none');

            // NEW Follow	

            if (response.following != '0' && response.following != '' && typeof(response.following) !== 'undefined') {

                var follownotify = $('#follownotify-hidden').val();
                if (follownotify != response.TotalFollwing) {
                    $ghostMessage('<strong>' + response.followingname + '</strong> follow you.');
                    $('#follownotify-hidden').val(response.TotalFollwing);
                }
            } else
            //  $('#notifications-onpageredbee-wrapper').css('display','none');  

            // *****************************************
            // NEW MESSAGE NOTIFICATION
            if (response.TotalMessages != '0' && response.TotalMessages != '' && typeof(response.TotalMessages) !== 'undefined') {
                $('#notifications-msg-top-wrapper').css('display', 'block');
                $('#notifications-msg-top').html(response.TotalMessages);
                $('#curr-notification-count').val(response.TotalMessages);
                var notemsghidden = $('#notifications-top-msg-hidden').val();
                if (notemsghidden != response.TotalMessages) {
                    $ghostMessage('<strong>' + response.TMRow + '</strong> just sent you a new message.');
                    $('#notifications-top-msg-hidden').val(response.TotalMessages);
                }
            } else
                $('#notifications-msg-top-wrapper').css('display', 'none')

            // *****************************************
            // NEW GROUP INVITE/REQUEST NOTIFICATION
            if (response.TotalGroups != '0' && response.TotalGroups != '' && typeof(response.TotalGroups) !== 'undefined') {
                $('#notifications-group-top-wrapper').css('display', 'block');
                $('#notifications-group-top').html(response.TotalGroups);
                $('#curr-notification-count').val(response.TotalGroups);

                var notegrpinvitehidden = $('#notifications-top-grpinvite-hidden').val();
                if (notegrpinvitehidden != response.TotalGroupInvite) {
                    $ghostMessage('<strong>' + response.TGIRow + '</strong> just invited you to his group.');
                    $('#notifications-top-grpinvite-hidden').val(response.TotalGroupInvite);
                }

                var notegrpreqhidden = $('#notifications-top-grpreq-hidden').val();
                if (notegrpreqhidden != response.TotalGroupRequests) {
                    $ghostMessage('<strong>' + response.TGRRow + '</strong> just requested to join your group.');
                    $('#notifications-top-grpreq-hidden').val(response.TotalGroupRequests);
                }
            } else {
                $('#notifications-group-top-wrapper').css('display', 'none');
            }


        },

        error: function(request, status, error) {

            $("#content_data").html("Some problems have occured. Please try again later: " + error);

        }


    });
}


// CHECK NEW DB COUNT

function chknewfollowingdb(n) {

    $.ajax({
        type: "POST",
        dataType: 'json',
        data: {
            'checkFlag=': n
        },
        url: BASE_URL + '/following/checknewfollowing',

        async: false,
        success: function(response) {
            if (response.status == 'success') {
                if (response.TotalDbees != '0' && response.TotalDbees != '' && typeof(response.TotalDbees) !== 'undefined') {
                    if ($('#newdbcount-wrapper').is(':visible') == true) {
                        $('#newdbcount-wrapper').style.display = 'block';
                        $('#newdbcount').html(response.TotalDbees);
                    }
                }
            }

        }
    });
}

function firetask() {
        taskFired = false;
    }
    // FUNCTION TO SEE MORE FEEDS


var busy = false;
function seemorefeeds(start, end, progressNow) {
   // start = start.replace(/(^[\s]+|[\s]+$)/g, '');
    //if(dbscrolevent==true){
        var totDbsLi = $('.postListing > li').length;
        if ( totDbsLi < 5 ){ return false; }
        
        var url = BASE_URL + "/myhome/fetchdbee";
        dbscrolevent = false;
        
        var data = {check:0,seemore:1,start:start,end:end}
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            dataType:'json',
            beforeSend: function() {
                
                $dbLoader('#more-feeds-loader', 'progress');
                $('#more-feeds-loader').css({
                    marginBottom: 50
                });

            },
            success: function(response) 
            {               
                $('#reloadend').val(start);
                
                if(response.postcount==0){ window.busy = true; }
                else { window.busy = false; }

                $('#see-more-feeds' + start).html(response.content);
                $('#startnewall').val(response.startnew);                      
                $.each(response, function(index, value) {              
                    if (index != '') {
                        $('.mentionto_' + index).mentionsInput({
                            onDataRequest: function(mode, query, callback) {

                                data = $.parseJSON(value);
                                responseDataMy = _.filter(data, function(item) {
                                    return item.name.toLowerCase().indexOf(query.toLowerCase()) > -1
                                });
                                console.log(responseDataMy);
                                callback.call(this, responseDataMy);
                            }
                        });
                    }
                });
                $('.rightTwitterDetails').css('margin-left','90px');
                dbscrolevent = true;
            }
        });

  //  }
}


// FUNCTION TO SEE MORE FOLLOWING FEEDS
var MoreMyCommentsFeedsHttp;

function seemoremycomments(start, end, id) {
    taskFired = true;
    setTimeout(firetask, 2000);
    start = start.replace(/(^[\s]+|[\s]+$)/g, '');
    id = typeof(id) != 'undefined' ? id : '0';
    
    MoreMyCommentsFeedsHttp = Browser_Check(MoreMyCommentsFeedsHttp);
    if (document.getElementById('more-feeds-loader'))
    {
      $dbLoader('#more-feeds-loader', 'progress');
                $('#more-feeds-loader').css({
                    marginBottom: 50
                });
    }
    var url = BASE_URL + "/Comment/index";
    var data = "seemore=1&start=" + start + '&end=' + end + '&user=' + id + '&dbeenotavailmsg=1';
    MoreMyCommentsFeedsHttp.open("POST", url, true);
    MoreMyCommentsFeedsHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    MoreMyCommentsFeedsHttp.setRequestHeader("Content-length", data.length);
    MoreMyCommentsFeedsHttp.setRequestHeader("Connection", "close");
    MoreMyCommentsFeedsHttp.onreadystatechange = function() {
        seemoremycommentsresult(start);
    };
    MoreMyCommentsFeedsHttp.send(data);
}

function seemoremycommentsresult(id) {
    if (MoreMyCommentsFeedsHttp.readyState == 4) {
        if (MoreMyCommentsFeedsHttp.status == 200 || MoreMyCommentsFeedsHttp.status == 0) {
            var result = MoreMyCommentsFeedsHttp.responseText;
            var resultArr = result.split('~#~');
            document.getElementById('reloadend').value = id;
            if (document.getElementById('see-more-feeds' + id))
                document.getElementById('see-more-feeds' + id).innerHTML = resultArr[0];
            document.getElementById('startnewmycomments').value = resultArr[3];
            Shadowbox.init();
            Shadowbox.setup();

        } else {};
    }
}

//SAVE RSS SITES SELECTED BY USER TO SHOW ON HOME

function saveuserrss() {
    $('.savetorss').append(' <i class="fa fa-spinner fa-spin"> </i>').css('cursor', 'default').removeAttr("onclick");
    var sites = $('#rss-sites').val();
    var sites1 = $('#rss-sites1').val();
    if (sites != sites1) {
        var err = false;
        if (sites == '') err = true;
        if (!err) {
            $("#msg").fadeOut('slow');
            $("#msg").html('You can select a maximum of 4');

            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {
                    'sites': sites
                },
                url: BASE_URL + '/myhome/savesite',
                async: false,
                success: function(response) {
                    /*$dbConfirm({
                        content: 'Rss feeds updated successfully.',
                        yes: false
                    });*/
             $('.topMenu').removeClass('activeSubMenu');
                   /* $('.totalCountWrp a').each(function(i){
                       var myJsonValue = 'mySocialRssFeed'+i;
                       delete mySocialFeedData[myJsonValue];
                           mySocialFeedData[myJsonValue]=='';
                    });*/
                     mySocialFeedData = {};
                    $('#sideBarFeeds a[data-type=rss]').trigger('click');
                   /*  $('.totalCountWrp a:first').trigger('click');
                    $('.socialFeedMenuTop a[data-type]').trigger('click');*/
                    //$('#topMenu[data-type="Platform feed"]').trigger('click');
                    //reloaduserrssicons('1');
                   // myfeeds(response.id, response.logo, response.name, 1, 1);
                }
            });

            $('.closePostPop').trigger('click');
        } else {
            $("#msg").html('Please select at least one RSS feed');
            $("#msg").fadeIn('slow');
            $('.savetorss').attr("onclick", "javascript:saveuserrss();");
            $('.savetorss .fa-spin').remove();
            $('.savetorss').css('cursor', 'pointer');
        }
    } else {
        $("#msg").html('No change found. Nothing to save!');
        $("#msg").fadeIn('slow');
        $('.savetorss').attr("onclick", "javascript:saveuserrss();");
        $('.savetorss .fa-spin').remove();
        $('.savetorss').css('cursor', 'pointer');
    }
}


//SAVE RSS SITES SELECTED BY USER TO SHOW ON HOME
var ReloadRssHttp;

function reloaduserrssicons(from) {
    from = typeof(from) != 'undefined' ? from : '0';
    ReloadRssHttp = Browser_Check(ReloadRssHttp);
    window.parent.document.getElementById('rss-icons').innerHTML = '<div  class="rss-icon" style="margin-left:20px;padding: 8px;width:220px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>';
    var url = BASE_URL + "/myhome/fetchicon";
    var data = "";
    ReloadRssHttp.open("POST", url, true);
    ReloadRssHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ReloadRssHttp.setRequestHeader("Content-length", data.length);
    ReloadRssHttp.setRequestHeader("Connection", "close");
    ReloadRssHttp.onreadystatechange = function() {
        reloaduserrssiconsresult(from);
    };
    ReloadRssHttp.send(data);
}

function reloaduserrssiconsresult(from) {
    if (ReloadRssHttp.readyState == 4) {
        if (ReloadRssHttp.status == 200 || ReloadRssHttp.status == 0) {
            var result = ReloadRssHttp.responseText;
            if (from == '1') {
                window.parent.document.getElementById('rss-icons').innerHTML = result;
                //				$('#rss1').css({'background-color': '#EFEFEF', 'border': '1px solid #FACE3F'});
                $(this).parents('#rss1').css({
                    'background-color': '#EFEFEF',
                    'border': '1px solid #FACE3F'
                });
            } else document.getElementById('rss-icons').innerHTML = result;
        } else {};
    }
}

var MyfeedsHttp;

function myfeeds(id, logo, newsrssname, counter, from) {
    from = typeof(from) != 'undefined' ? from : '0';
    MyfeedsHttp = Browser_Check(MyfeedsHttp);
    $('#rss1, #rss2,#rss3, #rss4').removeClass('rss-active');
    $('#rss' + counter).addClass('rss-active');
    if (from == '0') {
        if (document.getElementById('rssfeed-wrapper')) {
            $('#rssfeed-logo').html('<img src="' + BASE_URL_IMAGES + '/timthumb.php?src=/images/rsslogos/' + logo + '&q=100&w=158&h=30" border="0" />');
            //$('#rssfeed-logo').html(newsrssname);
            document.getElementById('rssfeed-logo').style.display = 'block';
            document.getElementById('rssfeed-wrapper').style.display = 'block';
            $('#rssfeed-wrapper').html('<div align="center" style="position:absolute; top:50%; left:50%; margin-left:-22px; margin-top:-22px;"><i class="fa fa-spinner fa-spin fa-3x"></i></div>');
        }
    } else {
        if (window.parent.document.getElementById('rssfeed-wrapper')) {
            window.parent.document.getElementById('rssfeed-logo').style.display = 'block';
            //window.parent.document.getElementById('rssfeed-logo').innerHTML = newsrssname;
            $('#rssfeed-logo').html('<img src="' + BASE_URL_IMAGES + '/timthumb.php?src=/images/rsslogos/' + logo + '&q=100&w=158&h=30" border="0" />');
            window.parent.document.getElementById('rssfeed-wrapper').style.display = 'block';
            window.parent.document.getElementById('rssfeed-wrapper').innerHTML = '<div align="center" style="position:absolute; top:50%; left:50%; margin-left:-22px; margin-top:-22px;"><i class="fa fa-spinner fa-spin fa-3x"></i></div>';
        }
    }
    var url = BASE_URL + "/myhome/convertrss";
    var data = "id=" + id;

    MyfeedsHttp.open("POST", url, true);
    MyfeedsHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    MyfeedsHttp.setRequestHeader("Content-length", data.length);
    MyfeedsHttp.setRequestHeader("Connection", "close");
    MyfeedsHttp.onreadystatechange = function() {
        myfeedsresult(from);
    };
    MyfeedsHttp.send(data);
}

function myfeedsresult(from) {

        if (MyfeedsHttp.readyState == 4) {
            if (MyfeedsHttp.status == 200 || MyfeedsHttp.status == 0) {
                var result = MyfeedsHttp.responseText;

                if (from == '0') {
                    $('#rssfeed-wrapper').html(result);
                } else if (from != '0') {
                    window.parent.document.getElementById('rssfeed-wrapper').innerHTML = result;

                }
                $('#rssfeed-wrapper').perfectScrollbar('destroy');
                $('#rssfeed-wrapper').perfectScrollbar();

            }

            //else alert("Retrieval Error: " + MyfeedsHttp.statusText);
        }
    }
    // FUNCTION TO SEE MORE FOLLOWING FEEDS
var MoreFollowinfFeedsHttp;

function seemorefollowing(start, end) {
    taskFired = true;
    setTimeout(firetask, 2000);
    start = start.replace(/(^[\s]+|[\s]+$)/g, '');
    MoreFollowinfFeedsHttp = Browser_Check(MoreFollowinfFeedsHttp);

    if (document.getElementById('more-feeds-loader'))
        document.getElementById('more-feeds-loader').innerHTML = '<img src="/images/ajaxloader.gif">';

    //var url="ajax_dbfeedfollowing.php"; 
    var url = BASE_URL + "/following/index";
    var data = "seemore=1&start=" + start + '&end=' + end + '&dbeenotavailmsg=1';


    MoreFollowinfFeedsHttp.open("POST", url, true);
    MoreFollowinfFeedsHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    MoreFollowinfFeedsHttp.setRequestHeader("Content-length", data.length);
    MoreFollowinfFeedsHttp.setRequestHeader("Connection", "close");
    MoreFollowinfFeedsHttp.onreadystatechange = function() {
        seemorefollowingresult(start);
    };

    MoreFollowinfFeedsHttp.send(data);
}

function seemorefollowingresult(id) {
        if (MoreFollowinfFeedsHttp.readyState == 4) {
            if (MoreFollowinfFeedsHttp.status == 200 || MoreFollowinfFeedsHttp.status == 0) {
                var result = MoreFollowinfFeedsHttp.responseText;
                var resultArr = result.split('~#~');
                //			document.getElementById('icon-dbfeed-buzz-following').className='icon-dbfeed-buzz';
                document.getElementById('reloadend').value = id;
                if (document.getElementById('see-more-feeds' + id))
                    document.getElementById('see-more-feeds' + id).innerHTML = resultArr[0];
                document.getElementById('startnewfollowing').value = resultArr[3];
                Shadowbox.init();
                Shadowbox.setup();



            } else alert("Retrieval Error: " + MoreFollowinfFeedsHttp.statusText);
        }
    }
    // FUNCTION TO SEE MORE FAVOURITE FEEDS
var MoreFavouriteFeedsHttp;

function seemorefavourites(start, end) {
    taskFired = true;
    setTimeout(firetask, 2000);
    start = start.replace(/(^[\s]+|[\s]+$)/g, '');
    MoreFavouriteFeedsHttp = Browser_Check(MoreFavouriteFeedsHttp);
    if (document.getElementById('more-feeds-loader'))
        document.getElementById('more-feeds-loader').innerHTML = '<img src="/images/ajaxloader.gif">';
    var url = BASE_URL + "/favourites/index";
    var data = "seemore=1&start=" + start + '&end=' + end + '&dbeenotavailmsg=1';
    MoreFavouriteFeedsHttp.open("POST", url, true);
    MoreFavouriteFeedsHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    MoreFavouriteFeedsHttp.setRequestHeader("Content-length", data.length);
    MoreFavouriteFeedsHttp.setRequestHeader("Connection", "close");
    MoreFavouriteFeedsHttp.onreadystatechange = function() {
        seemorefavouritesresult(start);
    };
    MoreFavouriteFeedsHttp.send(data);
}

function seemorefavouritesresult(id) {
        if (MoreFavouriteFeedsHttp.readyState == 4) {
            if (MoreFavouriteFeedsHttp.status == 200 || MoreFavouriteFeedsHttp.status == 0) {
                var result = MoreFavouriteFeedsHttp.responseText;
                var resultArr = result.split('~#~');
                document.getElementById('reloadend').value = id;
                if (document.getElementById('see-more-feeds' + id))
                    document.getElementById('see-more-feeds' + id).innerHTML = resultArr[0];
                document.getElementById('startnewfav').value = resultArr[3];
                Shadowbox.init();
                Shadowbox.setup();



            } else alert("Retrieval Error: " + MoreFavouriteFeedsHttp.statusText);
        }
    }
    // FUNCTION TO SEE MORE MOST COMMENTED FEEDS
var MoreMostCommentedFeedsHttp;

function seemoremostcommented(start, end) {
    taskFired = true;
    setTimeout(firetask, 2000);
    MoreMostCommentedFeedsHttp = Browser_Check(MoreMostCommentedFeedsHttp);
    if (document.getElementById('more-feeds-loader'))
        document.getElementById('more-feeds-loader').innerHTML = '<img src="/images/ajaxloader.gif">';
    var url = BASE_URL + "/myhome/mostcommented";
    var data = "seemore=1&start=" + start + '&end=' + end;
    MoreMostCommentedFeedsHttp.open("POST", url, true);
    MoreMostCommentedFeedsHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    MoreMostCommentedFeedsHttp.setRequestHeader("Content-length", data.length);
    MoreMostCommentedFeedsHttp.setRequestHeader("Connection", "close");
    MoreMostCommentedFeedsHttp.onreadystatechange = function() {
        seemoremostcommentedresult(start);
    };
    MoreMostCommentedFeedsHttp.send(data);
}

function seemoremostcommentedresult(id) {
        if (MoreMostCommentedFeedsHttp.readyState == 4) {
            if (MoreMostCommentedFeedsHttp.status == 200 || MoreMostCommentedFeedsHttp.status == 0) {
                var result = MoreMostCommentedFeedsHttp.responseText;
                var resultArr = result.split('~#~');
                document.getElementById('reloadend').value = id;
                if (document.getElementById('see-more-feeds' + id))
                    document.getElementById('see-more-feeds' + id).innerHTML = resultArr[0];
                document.getElementById('startnewmc').value = resultArr[3];
                Shadowbox.init();
                Shadowbox.setup();



            } else alert("Retrieval Error: " + MoreMostCommentedFeedsHttp.statusText);
        }
    }
    // FUNCTION TO SEE MORE CAT FEEDS
var MoreCatFeedsHttp;

function seemorecat(start, end, cat) {
    
    taskFired = true;
    setTimeout(firetask, 2000);
    MoreCatFeedsHttp = Browser_Check(MoreCatFeedsHttp);
    if (document.getElementById('more-feeds-loader'))
        document.getElementById('more-feeds-loader').innerHTML = '<img src="/images/ajaxloader.gif">';

    var url = BASE_URL + "/myhome/catetorylist";
    var data = "seemore=1&cat=" + cat + "&start=" + start + '&end=' + end + '&dbeenotavailmsg=1&type=category';
    MoreCatFeedsHttp.open("POST", url, true);
    MoreCatFeedsHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    MoreCatFeedsHttp.setRequestHeader("Content-length", data.length);
    MoreCatFeedsHttp.setRequestHeader("Connection", "close");
    MoreCatFeedsHttp.onreadystatechange = function() {
        seemorecatresult(start);
    };
    MoreCatFeedsHttp.send(data);
}

function seemorecatresult(id) {
        if (MoreCatFeedsHttp.readyState == 4) {
            if (MoreCatFeedsHttp.status == 200 || MoreCatFeedsHttp.status == 0) {
                var result = MoreCatFeedsHttp.responseText;
                var resultArr = result.split('~#~');
                document.getElementById('reloadend').value = id;
                if (document.getElementById('see-more-feeds' + id))
                    document.getElementById('see-more-feeds' + id).innerHTML = resultArr[0];
                document.getElementById('startnewcat').value = resultArr[3];
                Shadowbox.init();
                Shadowbox.setup();



            } else {};
        }
    }
    // FUNCTION TO SEE MORE TYPE FEEDS
var MoreTypeFeedsHttp;

function seemoretype(start, end, type) { 
    taskFired = true;
    setTimeout(firetask, 2000);
    MoreTypeFeedsHttp = Browser_Check(MoreTypeFeedsHttp);
    if (document.getElementById('more-feeds-loader'))
        document.getElementById('more-feeds-loader').innerHTML = '<img src="/images/ajaxloader.gif">';
    //var url = "/dbeeall/mydbeesortby";
    var url = BASE_URL + "/myhome/filtertype";
    var data = "seemore=1&type=" + type + "&start=" + start + '&end=' + end;

    MoreTypeFeedsHttp.open("POST", url, true);
    MoreTypeFeedsHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    MoreTypeFeedsHttp.setRequestHeader("Content-length", data.length);
    MoreTypeFeedsHttp.setRequestHeader("Connection", "close");
    MoreTypeFeedsHttp.onreadystatechange = function() {
        seemoretyperesult(start);
    };
    MoreTypeFeedsHttp.send(data);
}

function seemoretyperesult(id) {
        if (MoreTypeFeedsHttp.readyState == 4) {
            if (MoreTypeFeedsHttp.status == 200 || MoreTypeFeedsHttp.status == 0) {
                var result = MoreTypeFeedsHttp.responseText;
                var resultArr = result.split('~#~');
                document.getElementById('reloadend').value = id;
                if (document.getElementById('see-more-feeds' + id))
                    document.getElementById('see-more-feeds' + id).innerHTML = resultArr[0];
                document.getElementById('startnewtype').value = resultArr[3];
                Shadowbox.init();
                Shadowbox.setup();



            } else {};
        }
    }
    // FUNCTION TO SEE MORE FILTERED FEEDS
var MoreFilterFeedsHttp;

function seemorefilterdb(start, end, score) {
    taskFired = true;
    setTimeout(firetask, 2000);
    MoreFilterFeedsHttp = Browser_Check(MoreFilterFeedsHttp);
    if (document.getElementById('more-feeds-loader'))
        document.getElementById('more-feeds-loader').innerHTML = '<img src="/images/ajaxloader.gif">';
    document.getElementById('filterscore').value = score;
    //var url="/dbeeall/dbfeedfilter";
    var url = BASE_URL + "/myhome/dbfeedfilter";
    var data = "seemore=1&start=" + start + '&end=' + end + "&score=" + score;

    MoreFilterFeedsHttp.open("POST", url, true);
    MoreFilterFeedsHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    MoreFilterFeedsHttp.setRequestHeader("Content-length", data.length);
    MoreFilterFeedsHttp.setRequestHeader("Connection", "close");
    MoreFilterFeedsHttp.onreadystatechange = function() {
        seemorefilterdbresult(start);
    };

    MoreFilterFeedsHttp.send(data);
}

function seemorefilterdbresult(id) {
        if (MoreFilterFeedsHttp.readyState == 4) {
            if (MoreFilterFeedsHttp.status == 200 || MoreFilterFeedsHttp.status == 0) {
                var result = MoreFilterFeedsHttp.responseText;
                var resultArr = result.split('~#~');
                document.getElementById('reloadend').value = id;
                if (document.getElementById('see-more-feeds' + id))
                    document.getElementById('see-more-feeds' + id).innerHTML = resultArr[0];
                document.getElementById('startnewfilter').value = resultArr[3];
                Shadowbox.init();
                Shadowbox.setup();



            } else alert("Retrieval Error: " + MoreFilterFeedsHttp.statusText);
        }
    }
    // FUNCTION TO CHECK AND REALOD NEW COMMENTS AUTOMATICALLY

function LoadNewComments(LastCommentSeenDate,dbeeid) 
{
    if ($('#currcommentsortorder').val())
        var sortorder = $('#currcommentsortorder').val();
    else var sortorder = 'DESC';

    var dbid = $('#dbid').val();
    var dbowners = $('#dbowners').val()

    if (sortorder == 'DESC' && dbeeid==dbid) 
    {
        $.ajax({
            url: BASE_URL + '/comment/loadnewcomment',
            type: 'POST',
            dataType: 'json',
            data: {
                db: dbid,
                sortorder: sortorder,
                LastCommentSeenDate: LastCommentSeenDate
            },
            success: function(response) 
            {    
                if ($('div').hasClass('comment-list')) 
                {
                    var CommentsID = $('#CommentsID').val();
                    if (CommentsID) {
                        $('#comment-block-' + CommentsID).remove();
                        $('#CommentsID').val('');
                    }
                    $('#dbee-comments').prepend(response.content);
                    var startnew = parseInt($('#totalcomments').val());
                    var counter = startnew + parseInt(response.counter);
                    $('#totalcomments').val(counter);
                    $('.noMoreComments').hide();
                    getMentionUser(dbid);
                } 
                else
                {
                    $('#dbee-comments').html('<div class="comment-feed">' + response.content + '</div>');
                    $('.ShowMycomment').trigger('click');
                    getMentionUser(dbid);
                }
            }
        });
    }

}

var commentBlockStatus = true;
var videoStartStatus = false;
var videoFinished = false;


function closeConfirmboxRaj(trueFalse) {

            $('.dbConfirmOverlay').fadeOut('slow', function() {
                $(this).remove();
            });
            $('#' + thisBoxID).fadeOut('slow', function() {
                $(this).remove();
                if (trueFalse == '') trueFalse = false;
                return trueFalse;
            });
}

function checknewcomments(db) 
{
    if ($('#hiddendb').val()) 
    {
        var dbid = $("#dbid").val();
        var dbeeOwner = $('#dbowners').val();
        var dbtype = $('#dbeetype_edit').val();

        $.ajax({
            url: BASE_URL + '/dbeedetail/chknewcomments',
            type: 'POST',
            dataType: 'json',
            data: {
                check: '1',
                db: db,
                dbeeOwner: dbeeOwner
            },
            success: function(response) 
            {
                if (response.videoStartStatus == true && videoStartStatus == false && videoFinished == false && response.userAttendStatus != 0) {
                    playVideo();
                    videoStartStatus = true;
                }
                if (response.removeVideoLayer == true && response.userAttendStatus != 0)
                    $('.videoOverlay').remove();

                if (response.timeexpired == true && response.userAttendStatus != 0)
                    $('#warningMsg').hide()
            }
        });
    }
}
function checkdbeestatus()
{
    var end=$('#reloadend').val();
    end=parseInt(end)+5;
    var url=BASE_URL+"/myhome/dbeechk";
    var data="check=1&start="+end;
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'html',
        data: data,
        success:function(data)
        {
            var resultArr = data.split('~#~');             
            if(resultArr[0]>='1')           
                reloadmydbee(resultArr[0]);
            else
                $('#notifications-top-wrapper-dbee').hide();
        }
    });
}


function CheckSocialLinkingStatus()
{
    
    var url=BASE_URL+"/myhome/checksociallinking";
    var data="check=1";
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        success:function(response)
        {
           if((response.Facebookstatus==1 && response.Twitterstatus==1 && response.Linkedinstatus==1) || (response.allSocialstatus==1))
           {
                 $("#socialConnect").css("display", "none");
                 $(".socialfriends").css("display", "none");
                 $("#linkedinGroup").css("display", "none");
           }
           else
           {

            $("#socialConnect").css("display", "block");
            $(".socialfriends").css("display", "block");
            $("#linkedinGroup").css("display", "block");

           }
        }
    });
}

    // FUNCTION TO REMOVE TWEET FROM COMMENT
var RemoveTweetHttp;

function removetweet(comment, user, owner) {
    var db = document.getElementById('hiddendb').value;
    if (document.getElementById('notifyremovetweet'))
        var notify = document.getElementById('notifyremovetweet').value;
    RemoveTweetHttp = Browser_Check(RemoveTweetHttp);

    var url = "ajax_removetweet.php";
    var data = "comment=" + comment + "&user=" + user + "&owner=" + owner + "&db=" + db + "&notify=" + notify;

    RemoveTweetHttp.open("POST", url, true);
    RemoveTweetHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    RemoveTweetHttp.setRequestHeader("Content-length", data.length);
    RemoveTweetHttp.setRequestHeader("Connection", "close");

    RemoveTweetHttp.onreadystatechange = removetweetresult;
    RemoveTweetHttp.send(data);
}

function removetweetresult() {
        if (RemoveTweetHttp.readyState == 4) {
            if (RemoveTweetHttp.status == 200 || RemoveTweetHttp.status == 0) {
                var result = RemoveTweetHttp.responseText;
                var resultArr = result.split('~');
                if (resultArr[0] != 0) {
                    document.getElementById('tweet-block-' + resultArr[0]).style.display = 'none';
                    closepopup('fade');
                }
            } else {};
        }
    }
    // FUNCTION TO REMOVE MY COMMENT
var RemoveCommentHttp;

function removecomment(comment, n,islatestComment) 
{
    n               = typeof(n) != 'undefined' ? n : '0';
    islatestComment = typeof(islatestComment) != 'undefined' ? islatestComment : '0';
    owner           = typeof(owner) != 'undefined' ? owner : '0';
    var db          = document.getElementById('hiddendb').value;
    RemoveCommentHttp = Browser_Check(RemoveCommentHttp);
    if (n == '1') {
        if (document.getElementById('notifyremovetweet'))
            var notify = document.getElementById('notifyremovetweet').value;
    }
    var url = BASE_URL + "/comment/removecomments";
    createrandontoken(); // creating user session and token for request pass
    var data = userdetails+"&islatestComment="+islatestComment+"&comment="+ comment;

    RemoveCommentHttp.open("POST", url, true);
    RemoveCommentHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    RemoveCommentHttp.setRequestHeader("Content-length", data.length);
    RemoveCommentHttp.setRequestHeader("Connection", "close");

    RemoveCommentHttp.onreadystatechange = removecommentresult;
    RemoveCommentHttp.send(data);
    $('.closePostPop').trigger('click');
}

function removecommentresult() 
{
    if (RemoveCommentHttp.readyState == 4) {
        if (RemoveCommentHttp.status == 200 || RemoveCommentHttp.status == 0) {
            var result = RemoveCommentHttp.responseText;
            var resultArr = result.split('~');
            if (resultArr[0] != 0) 
            { 
                $('#comment-block-' + resultArr[0]).remove();
                if(!$('.comment-list').hasClass('notqa'))
                { 
                    $('#dbee-comments').html('<div class="comment-feed2"><div class="noFound firstUserCmnt commentWillStart"><i class="fa fa-pencil-square-o fa-2x"></i> Be the first user to comment.</div></div></div>');
                }
                if(localTick == false)
                {
                    socket.emit('checkdbee', true,clientID);
                    callsocket();
                }
            }
        };
    }
}




function changeClass(n) {
        if (n == '1') {
            var chk = document.getElementById('my-redb-profile');
            document.getElementById('my-db-profile').className = 'user-name';
            if (chk) document.getElementById('my-redb-profile').className = 'user-name-grey';
        } else if (n == '2') {
            document.getElementById('my-db-profile').className = 'user-name-grey';
            document.getElementById('my-redb-profile').className = 'user-name';
        }
    }
    // FUNCTION TO SEE RE DBEE'S ON PROFILE
    // FUNCTION TO SEE DBEE HISTORY ON PROFILE
var SeeMyHistoryHttp;

function seehistorylist(id, months) {
    $('div#maindb-wrapper').removeClass('maindb-wrapper-border');
    document.getElementById('mydbcontrols').style.display = 'none';
    document.getElementById('my-dbees').innerHTML = '<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>';
    SeeMyHistoryHttp = Browser_Check(SeeMyHistoryHttp);
    var url = "ajax_mydbees.php";
    var data = "user=" + id + "&months=" + months;
    SeeMyHistoryHttp.open("POST", url, true);
    SeeMyHistoryHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    SeeMyHistoryHttp.setRequestHeader("Content-length", data.length);
    SeeMyHistoryHttp.setRequestHeader("Connection", "close");

    SeeMyHistoryHttp.onreadystatechange = seehistorylistresult;

    SeeMyHistoryHttp.send(data);
}

function seehistorylistresult() {
        if (SeeMyHistoryHttp.readyState == 4) {
            if (SeeMyHistoryHttp.status == 200 || SeeMyHistoryHttp.status == 0) {
                var result = SeeMyHistoryHttp.responseText;
                var resultArr = result.split('~#~');
                document.getElementById('totaldbees').value = resultArr[5];
                //document.getElementById('startnewmydb').value='5';
                document.getElementById('my-dbees').innerHTML = resultArr[0];
                //fadepopup();
            } else alert("Retrieval Error: " + SeeMyHistoryHttp.statusText);
        }
    }
    // FUNCTION TO SEE BIOGRAPHY ON PROFILE
var SeeBioHttp;

function seebio(id) {
    $('div#maindb-wrapper').removeClass('maindb-wrapper-border');
    document.getElementById('mydbcontrols').style.display = 'none';
    document.getElementById('my-dbees').innerHTML = '<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>';
    SeeBioHttp = Browser_Check(SeeBioHttp);

    var url = "ajax_bio.php";
    var data = "user=" + id;

    SeeBioHttp.open("POST", url, true);
    SeeBioHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    SeeBioHttp.setRequestHeader("Content-length", data.length);
    SeeBioHttp.setRequestHeader("Connection", "close");

    SeeBioHttp.onreadystatechange = seebioresult;

    SeeBioHttp.send(data);
}

function seebioresult() {
        if (SeeBioHttp.readyState == 4) {
            if (SeeBioHttp.status == 200 || SeeBioHttp.status == 0) {
                var result = SeeBioHttp.responseText;
                var resultArr = result.split('~#~');
                document.getElementById('my-dbees').innerHTML = resultArr[0];
            } else alert("Retrieval Error: " + SeeBioHttp.statusText);
        }
    }
    // FUNCTION TO SEE USER POSITION IN LEAGUES TABLE ON PROFILE
var SeeUserLeagueHttp;

function seeuserleague(league, id, showtabs) {
    showtabs = typeof(showtabs) != 'undefined' ? showtabs : '0';
    $('div#maindb-wrapper').removeClass('maindb-wrapper-border');
    document.getElementById('mydbcontrols').style.display = 'none';
    if (showtabs == 0) {
        document.getElementById('leagues-tab-love').className = 'leagues-tab';
        document.getElementById('leagues-tab-rogue').className = 'leagues-tab';
        document.getElementById('leagues-tab-mostfollowed').className = 'leagues-tab';
        document.getElementById('leagues-tab-philosopher').className = 'leagues-tab';
        document.getElementById('leagues-tab-' + league).className = 'leagues-tab-active';
    }

    if (showtabs == '1')
        document.getElementById('my-dbees').innerHTML = '<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>';
    else
        document.getElementById('league-table').innerHTML = '<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>';
    SeeUserLeagueHttp = Browser_Check(SeeUserLeagueHttp);

    var url = "ajax_userleague.php";
    var data = "user=" + id + "&league=" + league + "&showtabs=" + showtabs;

    SeeUserLeagueHttp.open("POST", url, true);
    SeeUserLeagueHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    SeeUserLeagueHttp.setRequestHeader("Content-length", data.length);
    SeeUserLeagueHttp.setRequestHeader("Connection", "close");

    SeeUserLeagueHttp.onreadystatechange = seeuserleagueresult;

    SeeUserLeagueHttp.send(data);
}

function seeuserleagueresult() {
        if (SeeUserLeagueHttp.readyState == 4) {
            if (SeeUserLeagueHttp.status == 200 || SeeUserLeagueHttp.status == 0) {
                var result = SeeUserLeagueHttp.responseText;
                var resultArr = result.split('~#~');
                if (resultArr[1] == 1)
                    document.getElementById('my-dbees').innerHTML = resultArr[0];
                else if (resultArr[1] == 0)
                    document.getElementById('league-table').innerHTML = resultArr[0];
            } else alert("Retrieval Error: " + SeeUserLeagueHttp.statusText);
        }
    }
    // FUNCTION TO SEE USER LEAGUE POSITION IN POPUP FROM PROFILE
var SeeUserLeaguePopHttp;

function seeuserleaguepop(league, id, containerDiv) {
    $.ajax({
        url: BASE_URL + "/profile/userleaguepopup",
        type: "POST",
        data: {
            'user': id,
            'league': league
        },
        beforeSend: function() {
            $('#league-table').html('<div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div>');
        },
        success: function(data) {
            var result = data;
            var resultArr = result.split('~#~');
            $('#league-table').html(resultArr[0]);
            $.dbeePopup('resize');
        }
    });
}

function seeuserscoreposition(league, id, containerDiv, calle) {
    var callingfrom = (typeof(calle) != 'undefined') ? calle : '';
    $.ajax({
        url: BASE_URL + "/profile/userleaguepopup",
        type: "POST",
        data: {
            'user': id,
            'league': league,
            'callforprofile': callingfrom
        },
        beforeSend: function() {
            $('#' + containerDiv).html('<div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div>');
        },
        success: function(data) {
            var result = data;
            var resultArr = result.split('~#~');
            $('#' + containerDiv).html(resultArr[0]);

        }
    });
}



// FUNCTION TO SEE MORE LEAGUES
var MoreLeaguesHttp;

function seemoreleagues(start, end, league, counter) {

    MoreLeaguesHttp = Browser_Check(MoreLeaguesHttp);
    document.getElementById('more-leagues-loader').innerHTML = '<img src="/images/ajaxloader.gif">';

    var url = BASE_URL + "/dbleagues/userleaguemore";
    var data = "seemore=1&league=" + league + "&start=" + start + '&end=' + end + '&counter=' + counter;
    MoreLeaguesHttp.open("POST", url, true);
    MoreLeaguesHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    MoreLeaguesHttp.setRequestHeader("Content-length", data.length);
    MoreLeaguesHttp.setRequestHeader("Connection", "close");

    MoreLeaguesHttp.onreadystatechange = function() {
        seemoreleaguesresult(start);
    };

    MoreLeaguesHttp.send(data);
}

function seemoreleaguesresult(id) {
    if (MoreLeaguesHttp.readyState == 4) {
        if (MoreLeaguesHttp.status == 200 || MoreLeaguesHttp.status == 0) {
            var result = MoreLeaguesHttp.responseText;
            var resultArr = result.split('~#~');
            if (document.getElementById('see-more-leagues' + id))
                document.getElementById('see-more-leagues' + id).innerHTML = resultArr[0];
            //			document.getElementById('startnewall').value=resultArr[3];
        } else {};
    }
}
var DbeeuserHttp;

function dbeeuserall() {
    DbeeuserHttp = Browser_Check(DbeeuserHttp);

    var url = BASE_URL + "/profile/dbeeusercomment";
    var data = "";

    DbeeuserHttp.open("POST", url, true);
    DbeeuserHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    DbeeuserHttp.setRequestHeader("Content-length", data.length);
    DbeeuserHttp.setRequestHeader("Connection", "close");

    DbeeuserHttp.onreadystatechange = function() {
        dbeeuserallresult();
    };

    DbeeuserHttp.send(data);
}

function dbeeuserallresult() {
        if (DbeeuserHttp.readyState == 4) {
            if (DbeeuserHttp.status == 200 || DbeeuserHttp.status == 0) {
                var result = DbeeuserHttp.responseText;
                var resultArr = result.split('~#~');
                document.getElementById('dbee-highlighted').innerHTML = resultArr[0];
                document.getElementById('dbee-post-comment').value = resultArr[3];
                document.getElementById('dbee-comments').value = resultArr[2];
                //fadepopup();
            } else {};
        }
    }
    // FUNCTION TO SEE DBEE DETAILS ON PROFILE

// start see dbee functions from here

function seedbee(id, GroupID, fade) {
    var url = BASE_URL + "/dbeedetail/index";
    var data = {
        db: id,
        GroupID: GroupID
    };
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        async: false,
        success: function(data) {
            var result = data;


            var resultArr = result.split('~#~');
            if (resultArr[0] != '0') { 
                var htmlTraslation = '<div id="google_translate_element">\
                	<script type="text/javascript">\
						function googleTranslateElementInit() {\
						  new google.translate.TranslateElement({pageLanguage: "es", layout: google.translate.TranslateElement.InlineLayout.SIMPLE, autoDisplay: false}, "google_translate_element");\
						}\
						</script>\
						<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>\
                	</div>';
                    
                $('.gtranslator .dropDownList').html(htmlTraslation);
                $('#google_translate_element').click(function(event) {
                    event.stopPropagation();
                   $(this).closest('.gtranslator').addClass('on');
                   $( ".goog-te-menu-frame" ).contents().find( "a" ).click(function(event) {
                       event.stopPropagation();
                       $('.dropDown.gtranslator').click();
                    });
                });

                /*********** ***** *******/
                if (document.getElementById('groupWrapperHilighted')) {
                    $('#shareHighlighted').css({
                        position: 'abolute',
                        top: '228px'
                    });
                }
                if (resultArr[8] == '0') {
                    $('#dbee-post-comment').html(resultArr[1]);
                      if($('#dbee-post-comment').is(':empty')==true){
                        $('.dbDetailsPageMain').addClass('noCommentBox');
                    }
                    $('#dbee-comments').html(resultArr[2]);
                    if (resultArr[6] == 0) {
                        $('#sort-comments').hide();
                    }

                    if ($('.dbee-reply-wrapper').hasClass('surveyComplete') == true) $('.sortAndTweeterfeedWrapper').remove();
                    var fileList;

                    $('#uploadCoommentDropzone').dropzone({
                        url: BASE_URL + "/myhome/imguplod",
                        maxFiles: 1,
                        addRemoveLinks: true,
                        parallelUploads: 1,
                        maxFilesize: 1,
                        acceptedFiles: '.png, .jpg, .jpeg, .gif',
                        maxfilesexceeded: function(file, serverFileName) {
                            $dbConfirm({
                                content: serverFileName,
                                yes: false,
                                error: true
                            });
                        },
                        error: function(file, serverFileName) {
                            $dbConfirm({
                                content: serverFileName,
                                yes: false,
                                error: true
                            });
                        },
                        processing: function(file, serverFileName) {
                            $('.imageCmntPost').remove();
                            $('.dbpostWrp .cmntBntsWrapper').after('<div class="formRow imageCmntPost first" style="height:100px;" ></div>');
                            $dbLoader('.imageCmntPost', 1);
                            $('#dbee_comment').focus();
                        }, 
                        success: function(file, serverFileName) {

                            this.removeFile(file);
                            fileList = "serverFileName = " + serverFileName;
                            $dbLoader('.imageCmntPost', 1, 'close');
                            $('.miniPostWraper .imageCmntPost').css({
                                position: 'relative',
                                minHeight: '100px',
                                background: 'url(' +IMGPATH+'/imageposts/small/' + serverFileName + ')  no-repeat',
                                backgroundPosition: 'left top',
                                backgroundSize: 'contain'
                            }).append('<a href="javascript:void(0)" id="closedCmntbImage" data-fileName="' + serverFileName + '" class="removeCircle">\
														<span class="fa-stack">\
														  <i class="fa fa-circle fa-stack-2x"></i>\
														  <i class="fa fa-times fa-stack-1x fa-inverse"></i>\
														</span>\
														</a><input type="hidden" id="PostCommentPix" value="' + serverFileName + '"></div>');

                            $('#closedCmntbImage').click(function(e) {
                                var fileName = $(this).attr('data-fileName');

                                $('.imageCmntPost').remove();
                                $('#PostCommentPix').val('');
                                $.ajax({
                                    url: "/myhome/imgunlink",
                                    type: "POST",
                                    data: fileList,
                                    success: function() {
                                        $('#PostCommentPix').val('');
                                        myDropzone.removeFile(file);
                                    }
                                });

                            });
                            $('#dbee_comment').focus();
                        },
                        thumbnail: function(ds, dataUrl) {
                            $('#uploadCoommentDropzone .dz-error').remove();
                        }

                    });




                    $('.cmntLinkBtn').click(function(event) {
                        var thisEl = $(this);
                        var htmlAttachLink = '<div class="cmntLinkDetailsWrp">\
												<input type="text" name="cmntLink" id="cmntLink" value="" placeholder="Paste your link here..." />\
												<div class="removeCircle byDefaultClose" id="closeLinkUrl">\
													<span class="fa-stack">\
													  <i class="fa fa-circle fa-stack-2x"></i>\
													  <i class="fa fa-times fa-stack-1x fa-inverse"></i>\
													</span>\
												</div>\
												<div id="cmntLinkContainer"></div>\
											</div>';
                        if ($('.cmntLinkDetailsWrp').is(':visible') == false) {
                            thisEl.addClass('active');
                            $('.cmntLinkDetailsWrp').remove();
                            $('.cmntBntsWrapper').after(htmlAttachLink);
                            $('#cmntLink').focus();
                            $('.byDefaultClose').click(function(event) {
                                $('.cmntLinkDetailsWrp').remove();
                                thisEl.removeClass('active');
                            });
                            $('#cmntLink').blur(function(e) {
                                var url = $(this).val();
                                $linkCaptureData(url, '#cmntLinkContainer', '#cmntLink');
                            });
                        }
                    });
                    /*$("#dbee_comment").focusin(function(event) {
							$('#postCommentBtn').fadeIn();
						});*/


                } else {
                  
                    if (resultArr[17] >= 1) {
                        if (resultArr[12] != '' && resultArr[12] != 'undefined') {
                            drawChart(resultArr[9], resultArr[10], resultArr[11], resultArr[12], resultArr[13], resultArr[14], resultArr[15], resultArr[16], resultArr[17]);
                            if (resultArr[18] != '' && resultArr[18] != 'undefined') {

                            }
                        } else {
                            drawChart(resultArr[9], resultArr[10], resultArr[11], '-1', resultArr[13], resultArr[14], resultArr[15], '0', resultArr[17]);

                        }
                        $('#PollPieChart, .pollOptionLeft').removeClass('notStatesPoll')
                    } else {
                        $('#PollPieChart').addClass('notStatesPoll').html('<div class="noFound">No stats currently available</div>');
                        $('.pollOptionLeft').addClass('notStatesPoll');
                    }
                    if (resultArr[2] != '0') document.getElementById('dbee-comments').innerHTML = resultArr[2];
                }


                if (resultArr[3] != '') {
                    $('#' + resultArr[3]).addClass('active');
                }

                // FADE NON-TEXT DB REPLY ICONS
                $('#dbreply-icon-link, #dbreply-icon-pix, #dbreply-icon-vidz').fadeTo(30, 0.20);

                // ADD ORANGE BORDER TO ALL TEXTBOXES AND TEXTAREAS FOR COMMENT

                if (resultArr[7] == '1') { // IF USER IS LOGGED IN
                    if (resultArr[4] != '-1') {
                        if (resultArr[4] == '1') {
                            $('#follow-user').addClass('poplight');
                            $('#followme-label').html('Unfollow');
                            $('#follow-popup').html('You do not follow ' + resultArr[5] + ' any more');
                        } else {
                            $('#follow-user').addClass('poplight');
                            $('followme-label').html('follow');
                            //$('follow-popup').html ('You now follow ' + resultArr[5]);
                        }
                    }
                }

                if (resultArr[19] == '1') {
                    $('#notify-comment-div').show();
                    $('#notify-email-on').addClass('notify-email-status-on');
                } else if (resultArr[19] == '0') {
                    $('#notify-comment-div').show();
                    $('#notify-email-off').addClass('notify-email-status-on');
                } else if (resultArr[19] == '-1') {
                    $('#notify-comment-div').hide();
                }

                if (resultArr[6] != 0)
                    $('#totalcomments').val(resultArr[6]);


                //loadcommentleague(id);

                if ($('#Logineduser').val() != '') {
                    loadcommentleague(id);
                    startInt(id);
                }

               /* var hastag = $(location).attr('href');
                hastag = hastag.split('#');
                if (typeof(hastag[1]) != 'undefined' && hastag[1]!='') 
                {
                    var cmntPostition = $('#' + hastag[1]).offset().top;
                    $('body, html').scrollTop(cmntPostition - 50);
                    $('#' + hastag[1]).addClass('selectedCmnt');

                    setTimeout(function() {
                        $('#' + hastag[1]).addClass('selectedCmnt');
                    }, 3000);
                }*/

                $('.mentionme').mentionsInput({
                    onDataRequest: function(mode, query, callback) {
                        var responseData = jQuery.parseJSON($('#mentionusers').val());
                        //console.log(responseData);
                        responseData = _.filter(responseData, function(item) {
                            return item.name.toLowerCase().indexOf(query.toLowerCase()) > -1
                        });
                        //console.log(responseData);
                        callback.call(this, responseData);
                    }
                });

            } else document.getElementById('dbee-highlighted').innerHTML = 'This post is no longer active.';

        }

    });

}

// end see dbee functions from here


var adminStatus = false;
var chkcommid;

function startInt(parentdbId) {
    //chkcommid = setInterval("checknewcomments(" + parentdbId + ")", 10000);
}

function clearInt() {
        clearInterval(chkcommid);
        chkcommid = 0;
    }
    // FUNCTION TO SEE DBEE DETAILS ON PROFILE
var SeeDbeeHttp2;

function seedbee2(id, fade) {
    SeeDbeeHttp2 = Browser_Check(SeeDbeeHttp2);

    //	document.getElementById('more-feeds-loader').innerHTML='<img src="images/ajaxloader.gif">';
    var url = "fetch_dbee2.php";
    var data = "db=" + id;

    SeeDbeeHttp2.open("POST", url, true);
    SeeDbeeHttp2.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    SeeDbeeHttp2.setRequestHeader("Content-length", data.length);
    SeeDbeeHttp2.setRequestHeader("Connection", "close");

    SeeDbeeHttp2.onreadystatechange = function() {
        seedbeeresult2(id, fade);
    };

    SeeDbeeHttp2.send(data);
}

function seedbeeresult2(id, fade) {
        if (SeeDbeeHttp2.readyState == 4) {
            if (SeeDbeeHttp2.status == 200 || SeeDbeeHttp2.status == 0) {
                var result = SeeDbeeHttp2.responseText;
                var resultArr = result.split('~#~');
                document.getElementById('dbee-highlighted').innerHTML = resultArr[0];
                document.getElementById('dbee-post-comment').innerHTML = resultArr[1];
                document.getElementById('dbee-comments').innerHTML = resultArr[2];
                if (resultArr[3] != '') {
                    $('#' + resultArr[3]).fadeTo(30, 0.20);
                }
                if (resultArr[7] == '1') { // IF USER IS LOGGED IN
                    if (resultArr[4] != '-1') {
                        if (resultArr[4] == '1') {
                            document.getElementById('follow-user').className = 'poplight btn btn-yellow';
                            document.getElementById('followme-label').innerHTML = 'Unfollow';
                            document.getElementById('follow-popup').innerHTML = 'You do not follow ' + resultArr[5] + ' any more';
                        } else {
                            document.getElementById('follow-user').className = 'poplight btn btn-yellow';
                            document.getElementById('followme-label').innerHTML = 'follow';
                            //document.getElementById('follow-popup').innerHTML = 'You now follow ' + resultArr[5];
                        }
                    }
                }

                document.getElementById('totalcomments').value = resultArr[6];
                //fadepopup();
                createdrop(1, 5);
                //setInterval("checknewcomments()", 10000);
            } else alert("Retrieval Error: " + SeeDbeeHttp2.statusText);
        }
    }
    // FUNCTION TO SORT DB COMMENTS
var SortCommentsHttp;




    // FUNCTION TO HANDLE CAST VOTE ON POLLS

function castvote(poll) {

    var vote = getVoteOption();
    var pollcomment = $('#PollComment').val();
    var dbid = $('#dbid').val();
    var votBtn = $('#postCommentBtn a');
    var url = BASE_URL + "/dbeedetail/castvote";
    if (pollcomment == '') {
        $dbConfirm({
            content: 'Please enter your comment',
            yes: false,
            error: true
        });
        return false;
    }
    if (votBtn.hasClass('disable') == true) return false;
    votBtn.addClass('disable').append(' <i class="fa fa-spin fa-spinner"></i>');
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'html',
        data: {
            vote: vote,
            poll: poll,
            pollcomment: pollcomment,
            dbid: dbid
        },
        success: function(data) 
        {

            if (data == 0) 
            {
                $dbConfirm({
                    content: 'Please choose your vote',
                    yes: false,
                    error: true
                });
                votBtn.removeClass('disable');
                $('i', votBtn).remove();
                return false;
            } 
            else if (data == 3) 
            {
                $dbConfirm({
                    content: 'Sorry you have already given',
                    yes: false,
                    error: true
                });
                return false;
            }
            votBtn.removeClass('disable');
            $('i', votBtn).remove();
            var result = data;
            var resultArr = result.split('~#~');
            $('.pollOptionLeft,#PollPieChart').removeClass('notStatesPoll');
            $('.pollCmntOptnTop, .miniPostWraper').remove();
            $('input[name="pollradio"], #PollComment').prop('disabled', 'disabled');
            $('#poll-comment').html('<div class="pollOptnSubmit alreadyVoteSubmit">You have submitted your vote</div>');
            if(localTick == false)
            { 
                socket.emit('loadComment', resultArr[17],dbid,clientID);
                socket.emit('checkdbee', true,clientID);
                callsocket();
            }
            console.log(resultArr);
            if (resultArr[6] != '' && resultArr[6] != 'undefined'){ 
                drawChart(resultArr[3], resultArr[4], resultArr[5], resultArr[6], resultArr[7], resultArr[8], resultArr[9], resultArr[10], resultArr[17]);
            }
            else{ 

                drawChart(resultArr[3], resultArr[4], resultArr[5], '-1', resultArr[7], resultArr[8], resultArr[9], '0', resultArr[17]);
            }
        },
        error: function(error) {
            loadError(error);
        }
    });
}



function showmyvote(txt) {
    document.getElementById('myvote').innerHTML = txt;
    $('#myvote').fadeIn('slow');
}

function toggledbreplywrapper() {
        if (document.getElementById('dbee-reply-textarea-wrapper').className == 'dbee-reply-textarea-wrapper')
            document.getElementById('dbee-reply-textarea-wrapper').className = 'dbee-reply-textarea-wrapper-shaddow';
        else
            document.getElementById('dbee-reply-textarea-wrapper').className = 'dbee-reply-textarea-wrapper';
    }
    //

function togglemsgreplywrapper() {
    if (document.getElementById('message-reply-wrapper-2').className == 'message-reply-wrapper')
        document.getElementById('message-reply-wrapper-2').className = 'message-reply-wrapper-shadow';
    else
        document.getElementById('message-reply-wrapper-2').className = 'message-reply-wrapper';
}

function firsttweetsend() {
    var twitterscreenname = $('#twitterscreenname').val();
    var dbid = $('#dbid').val();
    var shortUrl = $('#shortUrl').val();
    if ($("#embedlinkPost").is(':checked')) {
        shortUrlLink = shortUrl;
    } else {
        shortUrlLink = '';
    }
    var retweetTextarea = $('.retweetTextarea').val();
    if (retweetTextarea == '') {
        $dbConfirm({
            content: 'Please write your tweet',
            yes: false,
            error: true
        });
        return false
    }
    $('.firsttweetsend').addClass('processBtnLoader').append(' <i class="fa fa-spinner fa-spin"></i>');
    $.ajax({
        type: "POST",
        dataType: 'json',
        data: {
            'embedlinkPost': shortUrlLink,
            'dbid': dbid,
            'twitterscreenname': twitterscreenname,
            'tweet': retweetTextarea
        },
        url: BASE_URL + "/social/twitterstatuspost",
        success: function(response) {
            $('.closePostPop').trigger('click');
            firstTweet = false;
            postcomment();
            $('.fa-spin').remove();
        }
    });
}

function tweet() {
    var hiddentwittername = $('#hiddentwittername').val();
    var shortUrl = $('#shortUrl').val();
    var charLenght = $('#hiddentwittername').val().length;

    if (hiddentwittername == '') {
        $dbConfirm({
            content: 'Please write your tweet',
            yes: false,
            error: true
        });
        return false
    }
    if (document.getElementById('hiddentwitterreply')) 
    {
        var favTemplate = '<div class="fbClickDescription2" style="padding-bottom:0px">\
					<div class="postTypeContent">\
						<div class="formRow">\
							<input type="hidden" name="twitterscreenname" id="twitterscreenname" value="' + hiddentwittername + '" />\
							<span class="twScrnName">@' + hiddentwittername + '</span>\
							<span class="twScrnName" id="shortUrlLink" style="display:none;">' + shortUrl + '</span>\
							<textarea class="retweetTextarea" contenteditable="true" data-editing="true"  maxlength="' + (139 - charLenght) + '" ></textarea>\
							<div class="formSubtitle"><span class="pull-right limitLength">' + (139 - charLenght) + ' limit</span></div>\
						</div>\
						<div class="formRow embedlinkPostRow">\
						<label for="embedlinkPost" class="labelCheckbox">\
							<input type="checkbox" id="embedlinkPost">\
							<label for="embedlinkPost" class="checkbox"></label>Embed link to this post\
						</label>\
						</div>\
					</div>\
			   </div>';

        $.dbeePopup(favTemplate, {
            contentHeader: '<div class="fbClickCnt"><h2 class="socialUpdatesTitle" style="background:#41c8f5">Invite tweeter to post</h2></div>',
            width: 350,overlayClick: false,
            bg:'#3E3E3E',
            otherBtn: '<a href="javascript:void(0);" class="btn btn-twitter firsttweetsend pull-right" onclick="javascript:firsttweetsend()">Tweet</a>'
        });

        setTimeout(function() {
            $('.retweetTextarea').focus()
        }, 1000);

        var twittercomment = document.getElementById('hiddentwitterreply').value;
    }
}

function load_qtip(apply_qtip_to) {
        $(apply_qtip_to).each(function() {
            $(this).qtip({
                content: $(this).attr('tooltip'), // Use the tooltip attribute of the element for the content
                style: 'dark' // Give it a crea mstyle to make it stand out
            });
        });
    }
    // FUNCTION TO POST DBEE COMMENT
var PostCommentHttp;
var appendmefirst = '';

function margeArray(arrayName, newArray){
     $.each(newArray, function(index, el) {  arrayName[index] = el; });
}

function postcomment() {
    appendmefirst = '';
    var hiddentwittername = $('#hiddentwittername').val();
    if (firstTweet == true && hiddentwittername != '') {
        tweet();
        return false;
    }
    if (commentBlockStatus == true && $('#dbeetype_edit').val() == 6) {
        //$messageError("Comments on this post will start after the video ends");
        $dbConfirm({
            content: 'Comments on this post will start after the video ends',
            yes: false,
            error: true
        });
        return false;
    }

    var db = document.getElementById('hiddendb').value;
    if (document.getElementById('hiddentwitterreply')) {
        var twittercomment = document.getElementById('hiddentwitterreply').value;
    } else var twittercomment = '';
    twittercomment = twittercomment.replace(/&/gi, "%26");
    err = false;

    var mentionsIds = '';
    var comment = $('#dbee_comment').val();
    linkdesc = '';
    linktitle = '';
    var videosite = '';
    var videoid = '';
    var viddesc = '';
    var audio = '';
    var url = $('#cmntLink').val();
    if (typeof(url) === "undefined") {
        url = '';
    }
    var LinkTitle = $('#LinkTitle').val();
    if (typeof(LinkTitle) === "undefined") {
        LinkTitle = '';
    }
    var linkdesc = $('#LinkDesc').val();
    if (typeof(linkdesc) === "undefined") {
        linkdesc = '';
    }
    
    comment = comment.replace(/&/g, '%26');
    if (!isBlank(comment)) {
        //var data = "comment=" + comment;
        var data = {comment:comment};
        var myComment = '&nbsp;' + comment;
    } else {
        //$messageError('Please write your comment');
        $dbConfirm({
            content: 'Please write your comment',
            yes: false,
            error: true
        });
        document.getElementById('dbee_comment').focus();
        err = true;
        return false;
    }
    var mentionsIds = '';
    var gtag = $('#dbee_comment').prev('.mentions');
    var comment = gtag.find('div:eq(0)').html();

    var PollComments_On_Option = $('#PollComments_On_Option').val();


    comment = conMentionTotext(comment);

    gtag.find('a').each(function() {
          var  urlMention = $(this).attr('href').split('/') ;
           mentionsIds += urlMention[urlMention.length-1]+',';
    });
    
    if (mentionsIds != '')
        var data = {comment:comment};

    pic = $('#PostCommentPix').val();
    if (typeof(pic) === "undefined") {
        pic = '';
    }
    if (pic != '') 
    {
        var dataPic = {pic:pic};
        margeArray(data,dataPic);
    }
    VidTitle = $('#youtubetitle').val();
    vid = $('#vid').val();
    viddesc = $('#youtubedescription').val();
    videosite = $('#videosite').val();
    if (VidTitle != '') 
    {
        
        var vid = {vid:vid};
        margeArray(data,vid);
        
        var VidTitle = {VidTitle:VidTitle};
        margeArray(data,VidTitle);

        var videosite = {videosite:videosite};
        margeArray(data,videosite);
        
        var viddesc = {viddesc:viddesc};
        
        margeArray(data,viddesc);

    }
   
    if (!err) 
    {
        usersindb = $('#usersindb').val(); 
        var dataBAl = {db:db,twittercomment:twittercomment,leaguedb:usersindb,mentionsIds:mentionsIds,url:url,linktitle:LinkTitle,linkdesc:linkdesc};
        margeArray(data,dataBAl);
        $('#postCommentBtn a').attr('data-click', 'false');
        $('#postCommentBtn a').append(' <i class="fa fa-spinner fa-spin"> </i>');
        $.ajax({
            url: BASE_URL + "/comment/insertdata",
            type: 'POST',
            data: data,
            dataType: 'json',
            
            beforeSend: function() {
                $('#startnew').val('20');
                $('.cmntLoderbg').show();                  
                         
            },
            success: function(response) {
                if (response.status == 'success') 
                {
                
                    $('#postCommentBtn a').attr('data-click', 'true');
                    $('#postCommentBtn a i').remove();
                    $('#closeLinkUrl').trigger('click');
                    $('.imageCmntPost').remove();
                    $('#CommentsID').val(response.insertId);
                    loadComment(response.insertId,'push',db);
                    //$(".comment-feed2").prepend(dbee_comments); //return false;
                    $('#cmntLink').val('');
                    $('.cmntBntsWrapper ').val('');
                    if(localTick == false)
                    {
                        socket.emit('loadComment', response.insertId,db,clientID);
                        socket.emit('checkdbee', true,clientID);
                        callsocket();
                    }
                    $('#total_comments').html('<i class="sprite pstCmtsIcon"></i>' + response.newcomment + ' ');
                  
                    if (document.getElementById('twitter-reply-box')) 
                    {
                        document.getElementById('twitter-reply-box').style.display = 'none';
                        var twittername = document.getElementById('hiddentwittername').value;
                        document.getElementById('hiddentwitterreply').value = '';
                    }
                    $('#dbee_comment').focus();
                    if (response.lgpart == '') 
                    {
                        lgname = $('#leaguename').val();
                        warnmsg = 'Thank you for your comment.  You are now a member of the "' + lgname + '" league';
                        $dbConfirm({
                            content: warnmsg,
                            yes: false
                        });
                    }
                }
                $('#dbee_comment').removeAttr('style').mentionsInput('reset').elastic();
                $('.cmntLoderbg').hide();                

            }
        });

    }
}
// FILL BLOCK USER POPOUP
function isValidURL(url) {
    var encodedURL = encodeURIComponent(url);
    var isValid = false;

    $.ajax({
        url: "https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20html%20where%20url%3D%22" + encodedURL + "%22&format=json",
        type: "get",
        async: false,
        dataType: "json",
        success: function(data) {
            isValid = data.query.results != null;
        },
        error: function() {
            isValid = false;
        }
    });

    return isValid;
}

function fillblockuserpopup(user, owner, db) {
        document.getElementById('blockuser-popup').innerHTML = 'This user will no longer be able to post a comment on this post. Are you sure you want to continue?<br><br><label><input type="checkbox" id="notifyblockuser" value="1"><span style="color:#999">Notify user</span></label><p align="center"><a href="javascript:void(0);" onclick="javascript:blockusertocomment(' + user + ',' + owner + ',' + db + ')">Yes</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="javascript:closepopup(\'fade\');">Cancel</a></p>';
    }
    // FUNCTION TO POST DBEE COMMENT
var BlockUserHttp;

function blockusertocomment(user, owner, db) {
    BlockUserHttp = Browser_Check(BlockUserHttp);

    if (document.getElementById('notifyblockuser'))
        var notify = document.getElementById('notifyblockuser').value;
    var url = "ajax_blockuser.php";
    var data = "db=" + db + "&user=" + user + "&owner=" + owner + "&notify=" + notify;

    BlockUserHttp.open("POST", url, true);
    BlockUserHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    BlockUserHttp.setRequestHeader("Content-length", data.length);
    BlockUserHttp.setRequestHeader("Connection", "close");

    BlockUserHttp.onreadystatechange = blockusertocommentresult;
    BlockUserHttp.send(data);
}

function blockusertocommentresult() {
        if (BlockUserHttp.readyState == 4) {
            if (BlockUserHttp.status == 200 || BlockUserHttp.status == 0) {
                var result = BlockUserHttp.responseText;
                var resultArr = result.split('~');
                if (resultArr[0] == '1') {
                    $(".blockuser-" + resultArr[2]).html('<a href="javascript:void(0);" onclick="javascript:unblockuser(' + resultArr[2] + ',' + resultArr[3] + ',' + resultArr[4] + ');"><span style="color:#EB8649">Unblock</span></a><div style="width:1px; height:10px;"></div>');
                    //				$(".blockuser-"+resultArr[2]).fadeTo(30, 0.80);
                    document.getElementById('blockuser-popup').innerHTML = resultArr[1] + ' has been blocked from this post.';
                    setTimeout("closepopup('fade')", 2000);
                }
            } else {};
        }
    }
    // FUNCTION TO UNBLOCK USER FROM DB
var UnBlockUserHttp;

function unblockuser(user, owner, db) {
    UnBlockUserHttp = Browser_Check(UnBlockUserHttp);

    var url = "ajax_blockuser.php";
    var data = "db=" + db + "&user=" + user + "&owner=" + owner + "&unblock=1";

    UnBlockUserHttp.open("POST", url, true);
    UnBlockUserHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    UnBlockUserHttp.setRequestHeader("Content-length", data.length);
    UnBlockUserHttp.setRequestHeader("Connection", "close");

    UnBlockUserHttp.onreadystatechange = unblockuserresult;
    UnBlockUserHttp.send(data);
}

function unblockuserresult() {
        if (UnBlockUserHttp.readyState == 4) {
            if (UnBlockUserHttp.status == 200 || UnBlockUserHttp.status == 0) {
                var result = UnBlockUserHttp.responseText;
                var resultArr = result.split('~');
                if (resultArr[0] == '1') {
                    $(".blockuser-" + resultArr[2]).html('<a href="#?w=400" rel="blockuser-popup" class="poplight" onclick="javascript:fillblockuserpopup(' + resultArr[2] + ',' + resultArr[3] + ',' + resultArr[4] + ');"><span style="color:#F40B0B">Block</span></a>');
                    $(".blockuser-" + resultArr[2]).fadeTo(30, 1);
                    document.getElementById('blockuser-popup').innerHTML = resultArr[1] + ' un-blocked. ' + resultArr[1] + ' can again post comments now.';
                    setTimeout("closepopup('fade')", 2000);
                    //fadepopup();
                }
            } else {};
        }
    }
    // FUNCTION TO BLOCK USER FROM MESSAGING YOU
var BlockUserMsgHttp;

function blockuserfrommsg(user, id) {
    BlockUserMsgHttp = Browser_Check(BlockUserMsgHttp);

    var url = BASE_URL + "/message/blockuserfrommsg";
    var data = "user=" + user + "&id=" + id;

    BlockUserMsgHttp.open("POST", url, true);
    BlockUserMsgHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    BlockUserMsgHttp.setRequestHeader("Content-length", data.length);
    BlockUserMsgHttp.setRequestHeader("Connection", "close");

    BlockUserMsgHttp.onreadystatechange = blockuserfrommsgresult;
    BlockUserMsgHttp.send(data);
}

function blockuserfrommsgresult() {
    if (BlockUserMsgHttp.readyState == 4) {
        if (BlockUserMsgHttp.status == 200 || BlockUserMsgHttp.status == 0) {
            var result = BlockUserMsgHttp.responseText;
            var resultArr = result.split('~');
            if (resultArr[0] == '1') {
                document.getElementById('blockusermsg-popup').innerHTML = resultArr[1] + ' has been blocked from messaging you.';
                document.getElementById('blockuser-div-' + resultArr[2] + '').innerHTML = '<div style="float:left; margin-left:10px; color:#CC0000;" title="' + resultArr[1] + ' is blocked from messaging you">[' + resultArr[1] + ' is blocked]</div>';
                setTimeout("closepopup('fade')", 2000);
            }
        } else {};
    }
}

function noscoredbee(score, tagName, type, cid, callingFrom) {

    //
}


// FUNCTION TO SCORE DBEE

function scoredbee(score, tagName, type, cid, ParentId, ParentType, callingFrom) {
    ParentId = typeof(ParentId) != 'undefined' ? ParentId : '0';
    ParentType = typeof(ParentType) != 'undefined' ? ParentType : '0';
    callingFrom = typeof(callingFrom) != 'undefined' ? callingFrom : '';

    var tag = tagName.split('###')[0];
    var className = tagName.split('###')[1];
    //$(this).click(function() {
    //var link_class = $(this).find('i').attr('class'); // find this class name
    //alert(this.id);
    // alert("link class name  ="+ link_class);



    $.dbeePopup('close');
    if (callingFrom != 'mainpost') var db = $('#hiddendb').val();
    else var db = cid;

    var url = BASE_URL + "/comment/scoredbee";
    //createrandontoken(); // creating user session and token for request pass
    var data = "db=" + db + "&ParentId=" + ParentId + "&ParentType=" + ParentType + "&comment=" + cid + "&score=" + score + "&type=" + type;

    var scoreClass = '';
    var scoreIcons = '';

     if (tag == 'love') {
        tag = 'love';
        scoreClass = 'Love';
        scoreIcons = ScoreIcon1;
    } else if (tag == 'like') {
        tag = 'like';
        scoreClass = 'Like';
        scoreIcons = ScoreIcon2;
    } else if (tag == 'dislike') {
        tag = 'dislike';
        scoreClass = 'Dislike';
        scoreIcons = ScoreIcon3;
    } else if (tag == 'hate') {
        tag = 'hate';
        scoreClass = 'Hate';
        scoreIcons = ScoreIcon4;
    }
    
    /* if(tag=='love'){
          scoreClass='Love'; 
      	 scoreIcons = '<i class="fa fa-smile-o"></i>';
      }
     else if(tag=='like') {
         scoreClass='Like'; 
         scoreIcons = '<i class="fa fa-thumbs-up fa-flip-horizontal"></i>';
         }      
     else if(tag=='philosopher') {
         scoreClass='Philosopher'; 
         scoreIcons = '<i class="fa fa-lightbulb-o"></i>';
         }
      else if(tag=='dislike') {
         scoreClass='Dislike'; 
         scoreIcons = '<i class="fa fa-thumbs-down fa-flip-horizontal"></i>';
         }
     else if(tag=='hate') {
         scoreClass='Hate'; 
         scoreIcons = '<i id="hate-dbee" class="fa fa-frown-o"></i>';
         }*/
    if (callingFrom != 'mainpost') {
        scoreClass = tag;
    }
    //scoreIcons = '<i class="' + className + '"></i>';
    var clst = $('#listingCommentLatest' + db);
    var cmntWrp = $('#comment-block-' + cid);
    var tagCurrent = '#' + tag + '-dbee';
    var tagCurrentTotalValue = parseInt($(tagCurrent + ' strong').text());
    var containerClass = $('.postDetailsScore');
    var scoredEl;
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        dataType: 'json',
        beforeSend: function() {

            if (callingFrom == 'mainpost') {
                scoredEl = $(tagCurrent + ' i', clst).detach();
                $(tagCurrent , clst).append('<b class="fa fa-spinner fa-spin"></b> ');


            } else {
                if (cmntWrp.is(':visible') == false) {
                    $(tagCurrent+' .fa', containerClass).before('<b class="fa fa-spinner fa-spin"></b> ');
                } else {
                    $(tagCurrent+' .fa', cmntWrp).before('<b class="fa fa-spinner fa-spin"></b> ');
                }
            }

        },
        success: function(data) {
            $dbTip();
            if(localTick == false){
                callsocket();
            }
            if (data.type == '1' && data.SubmitMsg == '1') {
                if (callingFrom == 'mainpost') {
                    $('.cmntScoreState span', clst).removeClass('active');
                    $(tagCurrent + ' .fa-spin', clst).remove();
                    scoredEl.prependTo('#listingCommentLatest' + db + ' #' + tag + '-dbee');
                    $(tagCurrent, clst).addClass('active');
                    var scoreTotal = parseInt($(tagCurrent + ' strong', clst).text());
                    if (data.deleted == '0')
                        $(tagCurrent + ' strong', clst).html(scoreTotal + 1);

                    // IF NO PREVIOUS SCORE WAS DELETED
                    if (data.mylastscore != '0') // IF PREVIOUS SCORE EXISTED AND WAS DELETED
                    {
                        var scoreTotalLast = $('#' + data.mylastscore + '-dbee strong', clst).text();
                        $('#' + data.mylastscore + '-dbee strong', clst).html(scoreTotalLast - 1);
                        $('#' + data.mylastscore + '-dbee', clst).removeClass('active');
                    }

                    $('#' + tag + '-dbee', clst).closest('.scoreComnt').find('.otheruserlike').remove();
                    $('#' + tag + '-dbee', clst).closest('.scoreComnt').append(data.scorelink);

                } else {

                    $(tagCurrent + ' .fa-spin', containerClass).remove();
                    $(tagCurrent + ' strong', containerClass).text(tagCurrentTotalValue);
                    $('a', containerClass).removeClass('active');

                    $('#' + tag + '-dbee').addClass('active');
                    var scoreTotal = parseInt($(tagCurrent + ' strong', containerClass).text());
                    if (data.deleted == '0')
                        $(tagCurrent + ' strong', containerClass).html(scoreTotal + 1);
                    // IF NO PREVIOUS SCORE WAS DELETED
                    if (data.mylastscore != '0') // IF PREVIOUS SCORE EXISTED AND WAS DELETED
                    {

                        var scoreTotalLast = parseInt($('#' + data.mylastscore + '-dbee strong', containerClass).text());
                        //alert(scoreTotalLast)
                        $('#' + data.mylastscore + '-dbee strong', containerClass).html(scoreTotalLast - 1);
                        $('#' + data.mylastscore + '-dbee', containerClass).removeClass('active');

                    }
                }
            } else if (data.type == '2' && data.SubmitMsg == '1') {
                if ($('#comment-score' + cid)) {
                    $(tagCurrent + ' .fa-spin', cmntWrp).remove();
                     $('#' + tag + '-dbee', cmntWrp).closest('.ftrCmnt').find('.active').removeClass('active');
                      $('#' + tag + '-dbee', cmntWrp).addClass('active');
                    if (data.deleted == '0')
                        $('#comment-score' + cid).html('you scored ' + scoreIcons + ' | ');
                    else if (data.deleted == '1')
                        $('#comment-score' + cid).html('');
                }
            }

            //var msgHtml ='Score submitted successfully <br><span class="scoreMgCnf">'+scoreIcons+' '+tag+'</span>';
            //$dbConfirm({content:msgHtml, yes:false});
            //$messageSuccess('Score submitted successfully');
        }

    })
}




// FUNCTION TO SCORE DBEE
// FILL HIDE USER DB POPOUP

function fillhideuserdbpopup(user) {

        /*var favTemplate = '<div style="padding:20px;">\
					<div class="formRow" id="content_data" >You are about to hide this users dbees from your homepage.<br><br>This action can\'t be undone. Are you sure you want to continue?</div>\
					<div class="clearfix"></div>\
					</div>';
				
					$.dbeePopup(favTemplate,{width:300,otherBtn:'<a href="javascript:void(0);" class="btn btn-yellow pull-right" onclick="javascript:hideuserdb(' + user + ')">Yes</a>\
											'});*/
        $dbConfirm({
            content: 'You are about to hide this users posts from your homepage.<br><br>This action can\'t be undone. Are you sure you want to continue?',
            yesClick: function() {
                hideuserdb(user)
            }
        });



    }
    // FUNCTION TO HIDE DBEES FROM A USER
var HideUserDbsHttp;

function hideuserdb(id) {
    HideUserDbsHttp = Browser_Check(HideUserDbsHttp);

    var url = BASE_URL + "/myhome/hideuserdb";
    var data = "user=" + id;
    HideUserDbsHttp.open("POST", url, true);
    HideUserDbsHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    HideUserDbsHttp.setRequestHeader("Content-length", data.length);
    HideUserDbsHttp.setRequestHeader("Connection", "close");

    HideUserDbsHttp.onreadystatechange = function() {
        hideuserdbresult(id);
    };
    HideUserDbsHttp.send(data);
}

function hideuserdbresult(id) {
    if (HideUserDbsHttp.readyState == 4) {
        if (HideUserDbsHttp.status == 200 || HideUserDbsHttp.status == 0) {
            var result = HideUserDbsHttp.responseText;
            var resultArr = result.split('~');
            if (resultArr[0] == '1') {
                $('#content_data').html('<div align="center" style="color:#999; font-size:18px;">You will not see posts from this user on your homepage any more.</div>');
                $('.btn btn-yellow pull-right').hide();
                reloadFeeds();

                setTimeout(function() {
                    jQuery('.closePostPop').trigger('click');
                }, 2000);
            }
        } else {};
    }
}




function addtoContactAjax(thisEl, id, thisBtn, dbusrname, followingoption) {
    createrandontoken(); // creting user session and token for request pass
    var addtocontacthidden = $('#addtocontacthidden').val();
    var addtocontacoff = '';
    if (addtocontacthidden == 0) {
        var addtocontacoff = '<p style="margin-bottom:10px;">The platform admin has turned OFF the ability to add users to your Contacts list. </p>';
    }

    var data = "user=" + id + '&followingoption=' + followingoption + '&' + userdetails;
    $.ajax({
        type: "POST",
        dataType: 'json',
        data: data,
        url: BASE_URL + '/following/addtocontact',
        async: false,
        success: function(response) {


            if (response.status == 'success') {

               var  listCount = $('.searchMemberList li').size();
               var  alphaCount = $('#MemberSortAlphbet .alphaMenu li').size();
                if (response.types == 'Add to Contacts') {
                    $('#contact_' + id).animate({
                        'backgroundColor': '#faa80b'
                    }, 300).animate({
                        opacity: 0.35
                    }, "slow");;
                    if ($('#contact_' + id)) {

                        $('#contact_' + id).fadeOut(300, function() {
                            $(this).remove();
                        });


                        if ($("#CountContactList").val() < 1) {
                            $('#my-dbees').html('<div id="middleWrpBox"><div style="margin-bottom:15px" class="user-name">My contacts list</div>' + addtocontacoff + '<div class="next-line"></div><div class="noFound" ><strong>Your contacts list is empty.</strong></div></div>');
                        }
                    }

                    if($( "#searchUserAllMenu .SortAlphabetContacts" ).hasClass( "active" )==true)
                    {   if(alphaCount>1){
                            if(listCount==1){

                                 $('#MemberSortAlphbet .alphaMenu li .active').closest('li').remove();
                                 $('#MemberSortAlphbet .alphaMenu li:first a').trigger('click');
                            }
                        }else{
                           $( "#searchUserAllMenu .SortAlphabetContacts" ).trigger('click'); 
                        }
                        //
                    }


                    if (addtocontacthidden == 1) {
                        $('#contact-label', thisEl).html('Add to Contacts');
                    } else {
                        $('#contact-label', thisEl).html('');
                        thisEl.remove();
                    }

                }
                if (response.types == 'Remove from Contacts') {
                    $('#contact-label', thisEl).html('Remove from Contacts');

                    var targetEl = $('#ProfileLink');
                    $('#header').css({
                        position: 'relative'
                    });
                    var targetPostion = targetEl.offset();
                    var targetPostionLeft = targetPostion.left;
                    var targetPostionTop = targetPostion.top;
                    $('#header').css({
                        position: 'fixed'
                    });
                    var parentElPositionLeft = thisEl.offset().left - $(window).scrollLeft();
                    var parentElPositionTop = thisEl.offset().top - $(window).scrollTop();
                    $('#fakeMovedb').remove();
                    $('body').append('<div id="fakeMovedb"  style="overflow:hidden; font-weight:bold; padding:10px; background:#fff; box-shadow:0 0 5px rgba(0,0,0,0.5)">' + dbusrname + '</div>');
                    $('#fakeMovedb').css({
                            width: thisEl.width(),
                            position: 'fixed',
                            left: parentElPositionLeft,
                            top: parentElPositionTop,
                            zIndex: 9999
                        })
                        .animate({
                            left: targetPostionLeft + 15,
                            top: targetPostionTop + 13,
                            width: 20,
                            height: 20,
                            opacity: 0
                        }, 1000, function(){
                             $('#fakeMovedb').remove();
                        });


                }

                if (response.types2 == 'Follow') {
                    $('#followme-label', thisEl).html('Follow');
                }

                if (response.types2 == 'Unfollow') {
                    thisEl.closest('.profileDashBtn').find('#followme-label').html(response.types2);
                }


                //
                //$dbConfirm({content:response.message, yes:false});
            } else if (response.status == 'error')
            //$messageError(response.message);
            //$dbConfirm({content:response.message, yes:false,error:true});
                thisEl.removeClass('processBtnLoader');
            $('.fa', thisEl).remove();
            thisEl.css('cursor', 'pointer');
            $('#contact-label', thisEl).css('cursor', 'pointer');

        },
        error: function(error) {
            loadError(error);
        }
    });
}

function addtocontact(id, thisBtn, dbusrname, followingoption) {
        var thisEl = $(thisBtn);

        //$('#contact-label', thisEl).append(' <i class="fa fa-spinner fa-spin"></i>').css('cursor', 'default');
        // thisEl.addClass('processBtnLoader').find('div').append(' <i class="fa fa-spinner fa-spin"></i>').css('cursor', 'default');
        var addTocontact = $.trim(thisEl.text());

        var followingoptionxx = $(".followme" + id).text();

        if (followingoptionxx == 'Follow' && addTocontact == 'Add to Contacts') {
            $dbConfirm({
                content: 'You need to follow  <span class="orange">' + dbusrname + '</span>, in order to add to your contacts ',
                yes: true,
                yesLabel: 'Follow',
                no: true,
                noLabel: 'No thanks',
                yesClick: function() {
                    thisEl.addClass('processBtnLoader').find('div').append(' <i class="fa fa-spinner fa-spin"></i>').css('cursor', 'default');
                    addtoContactAjax(thisEl, id, thisBtn, dbusrname, followingoptionxx);
                }
            });
        } else if (addTocontact == 'Remove from Contacts') {
            $dbConfirm({
                content: 'Are you sure to remove this user from your contacts?',
                yes: true,
                yesLabel: 'Yes',
                no: true,
                noLabel: 'No',
                yesClick: function() {
                    thisEl.addClass('processBtnLoader').find('div').append(' <i class="fa fa-spinner fa-spin"></i>').css('cursor', 'default');
                    addtoContactAjax(thisEl, id, thisBtn, dbusrname, followingoptionxx);
                }
            });
        } else {
            thisEl.addClass('processBtnLoader').find('div').append(' <i class="fa fa-spinner fa-spin"></i>').css('cursor', 'default');
            addtoContactAjax(thisEl, id, thisBtn, dbusrname, followingoptionxx);
        }

    } // end function add to contact


function setpopupHTML(flag, name) {
        //    if (flag == '0') document.getElementById('follow-popup').innerHTML = 'You now follow ' + name;
        //   else if (flag == '1') document.getElementById('follow-popup').innerHTML = 'You do not follow ' + name + ' any more';
    }
    // FUNCTION TO FOLLOW USER
    // FUNCTION TO RE DB
var ReDbHttp;

function redbee(db, user) {
    ReDbHttp = Browser_Check(ReDbHttp);
    var url = BASE_URL + "/myhome/insertredbee";
    var data = "db=" + db + "&dbOwner=" + user;
    ReDbHttp.open("POST", url, true);
    ReDbHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ReDbHttp.setRequestHeader("Content-length", data.length);
    ReDbHttp.setRequestHeader("Connection", "close");
    ReDbHttp.onreadystatechange = redbeeresult;
    ReDbHttp.send(data);
}

function redbeeresult() {
    if (ReDbHttp.readyState == 4) {
        if (ReDbHttp.status == 200 || ReDbHttp.status == 0) {
            var result = ReDbHttp.responseText;
            var resultArr = result.split('~');
            if (resultArr[0] == '1') {
                //$dbConfirm({content:'post successfully posted on your profile', yes:false});
                var groups = $('#group').val();
                if (groups == '') {
                    $('#dbee-feeds').fadeOut('slow').load('/myhome/dbeereload/5', function() {
                        $('html,body').animate({
                            scrollTop: 0
                        });
                    }).fadeIn("slow");
                } else {
                    $('#dbee-feeds').fadeOut('slow').load('/myhome/dbeereload/group/' + groups, function() {

                        $('html,body').animate({
                            scrollTop: 0
                        });
                    }).fadeIn("slow");  
                }
            } else alert("Retrieval Error: " + ReDbHttp.statusText);
        }
    }
}

function filldeletedbcontrols(db) {
    // var favTemplate = '<div id="content_data"style="color:#999999;padding:10px 0px 10px 0px; height:50px; text-align:center; font-size:18px;" class="msgCntWrapper">Do you really want to delete this post?</div>';
    //$.dbeePopup(favTemplate, {width:300,otherBtn:'<a class="btn btn-yellow pull-right" id="deletebutton" href="javascript:deletedbee(' + db + ',\'main\')">Yes</a>'});
    $dbConfirm({
        content: 'Do you really want to delete this post?',
        yesClick: function() {
            deletedbee(db, 'main')
        }
    });
}

function filldeleteredbcontrols(db) {
    // var favTemplate = '<div  id="content_data" style="color:#999999;padding:10px 0px 10px 0px; height:50px; text-align:center; font-size:18px;" class="msgCntWrapper">Do you really want to delete this post?</div>';
    //$.dbeePopup(favTemplate, {width:300,otherBtn:'<a class="btn btn-yellow pull-right" id="deletebutton" href="javascript:deletedbee(' + db + ',\'redb\')">Yes</a>'});
    $dbConfirm({
        content: 'Do you really want to delete this post?',
        yesClick: function() {
            deletedbee(db, 'redb')
        }
    });


}

function filldeletefavouritecontrols(db) {
        // var favTemplate = '<div id="content_data" style="color:#999999;padding:10px 0px 10px 0px; height:50px; text-align:center; font-size:18px;" class="msgCntWrapper">Do you really want to delete this post from favourites?</div>';
        //$.dbeePopup(favTemplate, {width:300,otherBtn:'<a class="btn btn-yellow pull-right" id="deletebutton"  href="javascript:deletedbee(' + db + ',\'favourite\')">Yes</a>'});
        $dbConfirm({
            content: 'Do you really want to delete this post from favourites?',
            yesClick: function() {
                deletedbee(db, 'favourite')
            }
        });

    }
    // FUNCTION TO DELETE DB FROM YOUR PROFILE
var DeleteDbHttp;

function deletedbee(db, type) {
    DeleteDbHttp = Browser_Check(DeleteDbHttp);
    
    var url = BASE_URL + "/myhome/deletedb";
    var data = "db=" + db + "&type=" + type;
    DeleteDbHttp.open("POST", url, true);
    DeleteDbHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    DeleteDbHttp.setRequestHeader("Content-length", data.length);
    DeleteDbHttp.setRequestHeader("Connection", "close");
    DeleteDbHttp.onreadystatechange = deletedbeeresult;
    DeleteDbHttp.send(data);
    $('#deletebutton').hide();
}

function deletedbeeresult() {
    if (DeleteDbHttp.readyState == 4) {
        if (DeleteDbHttp.status == 200 || DeleteDbHttp.status == 0) {
            var result = DeleteDbHttp.responseText;
            var resultArr = result.split('~');
            
            if(localTick == false)
            {
                callsocket();
                socket.emit('checkdbee', true,clientID);
            }
            if (result == "999") {
                $dbConfirm({
                    content: 'This post has received a comment and it can\'t be deleted!',
                    error: true,
                    yes: false
                });
                setTimeout(function() {
                    window.location.href = BASE_URL;
                }, 4000)
            }
            if (resultArr[0] == '1' && resultArr[2] == 'main') {
                //document.getElementById('dbee-id-' + resultArr[1]).style.display = 'none';
                $('#dbee-id-' + resultArr[1]).remove();
                //$('#content_data').html('Post deleted successfully.');
                //seeglobaldbeelist(SESS_USER_ID,4,"my-dbees","myhome","mydbee","mydb")
                
                ulLen = $("ul#my-dbees > li").size();               
                if(ulLen<1) $("#my-dbees").html('<div class="noFound"><strong>No posts found.</strong></div>');

                setTimeout(function() {
                    jQuery('.closePostPop').trigger('click');
                }, 2000);
            }
            if (resultArr[0] == '1' && resultArr[2] == 'redb') {
                document.getElementById('dbee-id-' + resultArr[1]).style.display = 'none';
                $('#content_data').html('Post removed from your profile successfully.');
                setTimeout(function() {
                    jQuery('.closePostPop').trigger('click');
                }, 2000);
            }
            if (resultArr[0] == '1' && resultArr[2] == 'favourite') {
                document.getElementById('dbee-id-' + resultArr[1]).style.display = 'none';
                if (resultArr[4] == 0)
                    document.getElementById('dbee-feeds').innerHTML = resultArr[3];
                $('#content_data').html('Post removed from your favourites.');
                setTimeout(function() {
                    jQuery('.closePostPop').trigger('click');
                }, 2000);
            }
        } else loadError(DeleteDbHttp);
    }
}

// GENERIC FUNCTION TO AUTO CLOSE FADE POPUP

function closepopup(id, flag, name) {
        flag = typeof(flag) != 'undefined' ? flag : '-1';
        name = typeof(name) != 'undefined' ? name : '';
        $('#' + id + ', .popup_block').fadeOut(function() {
            $('#' + id + ', a.close').remove();
        });
        if (flag != '-1') setTimeout("setpopupHTML(" + flag + ",'" + name + "')", 1000);;
    }
    // GENERIC FUNCTION TO AUTO CLOSE FADE POPUP
    // FUNCTION TO SEE USER FOLLOWERS
var FollowersHttp;

function seefollowers(id) {
    FollowersHttp = Browser_Check(FollowersHttp);
    var url = "ajax_followers.php";
    var data = "user=" + id;
    FollowersHttp.open("POST", url, true);
    FollowersHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    FollowersHttp.setRequestHeader("Content-length", data.length);
    FollowersHttp.setRequestHeader("Connection", "close");

    FollowersHttp.onreadystatechange = seefollowersresult;

    FollowersHttp.send(data);
}

function seefollowersresult() {
        if (FollowersHttp.readyState == 4) {
            if (FollowersHttp.status == 200 || FollowersHttp.status == 0) {
                var result = FollowersHttp.responseText;
                //			var resultArr=result.split('~#~');
                document.getElementById('maindb-wrapper').innerHTML = result;
            } else loadError(FollowersHttp);
        }
    }
    // FUNCTION TO SEE USER FOLLOWING
var FollowingHttp;

function seefollowing(id) {
    FollowingHttp = Browser_Check(FollowingHttp);

    var url = BASE_URL + "/following/index";
    var data = "user=" + id;

    FollowingHttp.open("POST", url, true);
    FollowingHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    FollowingHttp.setRequestHeader("Content-length", data.length);
    FollowingHttp.setRequestHeader("Connection", "close");
    FollowingHttp.onreadystatechange = seefollowingresult;

    FollowingHttp.send(data);
}

function seefollowingresult() {
        if (FollowingHttp.readyState == 4) {
            if (FollowingHttp.status == 200 || FollowingHttp.status == 0) {
                var result = FollowingHttp.responseText;
                //			var resultArr=result.split('~#~');
                document.getElementById('maindb-wrapper').innerHTML = result;
            } else alert("Retrieval Error: " + FollowingHttp.statusText);
        }
    }
    //FUNCTION TO SEE NOTIFICATIONS WHEN LINK IS CLICKED FROM HEADER
var NotificationHttp;

function seenotification(end, type, date1, date2) {

    NotificationHttp = Browser_Check(NotificationHttp);
    document.getElementById("notifications-top-wrapper").style.display = 'none';
    document.getElementById('noti-type').value = type;
    document.getElementById('notification-feed').innerHTML = '<div style="margin:10px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>';
    document.getElementById('notes-tab-1').className = 'notestab';
    document.getElementById('notes-tab-2').className = 'notestab';
    document.getElementById('notes-tab-3').className = 'notestab';
    document.getElementById('notes-tab-' + type).className = 'notestab active';
    $('.notificationSorting li').removeClass('sortresnotify-active');
    $('.notificationSorting li:nth-child(1)').addClass('sortresnotify-active');

    if (type == '2') {
        if (document.getElementById("newredb-icon"))
            document.getElementById("newredb-icon").style.display = 'none';
    }

    if (type == '3') {
        if (document.getElementById("newmention-icon"))
            document.getElementById("newmention-icon").style.display = 'none';
    }

    var url = BASE_URL + "/notification/fetchnotification";
    var data = "end=" + end + "&type=" + type + "&date1=" + date1 + "&date2=" + date2;
    NotificationHttp.open("POST", url, true);
    NotificationHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    NotificationHttp.setRequestHeader("Content-length", data.length);
    NotificationHttp.setRequestHeader("Connection", "close");

    NotificationHttp.onreadystatechange = seenotificationresult;

    NotificationHttp.send(data);
}

function seenotificationresult() {
        if (NotificationHttp.readyState == 4) {
            if (NotificationHttp.status == 200 || NotificationHttp.status == 0) {
                var result = NotificationHttp.responseText;
                var resultArr = result.split('~');
                document.getElementById('notification-feed').innerHTML = resultArr[0];
                $("html").getNiceScroll().resize();

            } else {};
        }
    }
    // FUNCTION TO SHOW USER PIC ON MESSAGE
//var MessageUserHttp;
function messagefeed(myjson) {
    
   if(myjson.fromadmin==1){
     return false;
     $('#topmessageMenu').trigger('click');
   }
    $dbLoader('#message-feed', 1);  
    var thisul = $('#leftListing');  
    var data = {"user": myjson.uid, "read" : myjson.read, "mid" : myjson.mid, "datefrom": myjson.datefrom, "dateto" : myjson.dateto, "search" : myjson.search};
        $.ajax({
        type: "POST",
        dataType: 'json',
        data: data,
        url: BASE_URL + '/message/messagefeed2',
        cache: false,
        beforeSend: function() {
            thisul.html('<div id="dbee-feeds" class="postListing" style="margin-top:0"><div id="message-feed"></div><br style="clear:both; font-size:1px;" /></div>  ');
           
            //$('.profileStatsWrp').fadeOut();
            //$('.whiteBox').fadeOut();
            //$('#sortable').fadeOut();

            $dbLoader('#message-feed', 1);  

           // $('#message-feed', thisul).addClass('fa-spinner fa-spin');

        },        
        
        success: function(response) {
           // messagefeed('<?php echo $this->user;?>','<?php echo $read;?>','<?php echo $this->msgid;?>','<?php echo $this->dateFrom;?>','<?php echo $this->dateTo;?>','<?php echo $this->search;?>');
         if (response.content!= '') {
        
            var messagefeed = '';
                   
            messagefeed+='<div class="next-line"></div><div id="message-feed-wrapper" class="message-feed-wrapper">';

             $.each(response.content, function(i, value){
                   var msgTxt = value.Message;
                 msgTxt = msgTxt.replace(/(?:<br[^>]*>\s*){2,}/g, '<br />');

                if(value.userid==value.MessageFrom) // MESSAGE NOT SENT BY PROFILE HOLDER
                    messagefeed+='<div class="msgouter"><div class="imgfloatmsg" >\
            <img src="'+IMGPATH+'/users/small/'+value.pic+'" border="0" /></div>\
        <div class="default-speechwrapper-lightgrey speechWrpBox" ><div class="msgTxtboxFeed"><span class="msgdate">'+value.ago+'</span>'+msgTxt+'</div>\
        </div></div>';
                else // MESSAGE SENT BY PROFILE HOLDER
                    messagefeed+='<div class="msgouter"><div class="imgfloatmsg-right">\
        <img src="'+IMGPATH+'/users/small/'+value.pic+'" border="0" /></div>\
        <div class="default-speechwrapper-darkgrey speechWrpBox">\
        <div class="msgTxtbox"><span class="msgdate">'+value.ago+'</span>\
        '+msgTxt+'</div>\
        </div></div>';

            });

            messagefeed+='</div>';

            if(response.reload==null){               
                if(response.sendmessage) {                    
                    messagefeed+="<div id='message-reply-wrapper'>\
        <div id='message-reply-wrapper-2' class='message-reply-wrapper'>\
            <textarea id='message-reply' class='message-reply' placeholder='leave a message...'></textarea></div>\
        <div class='postMessagefotter'>\
            <div id='messagelist' class='btn' >Back to my messages</div>\
            <div class='btn btn-yellow pull-right' id='postmessagesend' onclick='javascript:postmessage();'>Send</div>\
        </div><div id='postmessagesendsucc'></div><input type='hidden' id='hiddenuser' value='"+response.user+"'><input type='hidden' id='parent' value='"+response.mid+"'></div>";
                } else {
                    if(response.search=='')
                        messagefeed+='<div class="user-blocked-box">You are blocked from sending a message.</div>';
                }
            }
            else {
                messagefeed+='<div align="center" class="noFound" style="margin-top:140px;">message feed empty</div>';
            }
            
            
        }else{
             messagefeed+='<div align="center" class="noFound" style="margin-top:140px;">message feed empty</div>';
        }
        messagefeed+='</div>';
                    //$('#message-feed-wrapper').perfectScrollbar({ includePadding:true,wheelSpeed: 100});
            $('#message-feed').html(messagefeed);
                    $('#message-reply').keypress(function(event) {
                        if (event.which == 13) {                           
                            if (!event.shiftKey) postmessage();
                        } else {
                            this.focus();
                        }

                    });

                    messageInputBox();

                    $(window).resize(function() {
                        messageInputBox();
                    })




                    //} else alert("Retrieval Error: " + MessageUserHttp.statusText);
        }


        //}

    });

   
}



function messageInputBox() {
    var windH = $(window).height();
    var messageHeight = $('#message-feed').height();
    var messageWidth = $('#message-feed').width();
    if (windH < messageHeight) {
        $('#message-reply-wrapper').css({
            width: messageWidth
        }).addClass("messageBoxFixed");
    }

    $(window).scroll(function(event) {
        if ($(document).scrollTop() == ($(document).height() - windH)) {
            $('#message-reply-wrapper').removeAttr('style').removeClass("messageBoxFixed");
        } else if ($(document).scrollTop() < ($(document).height() - windH - 200)) {
            if (windH < messageHeight) {
                $('#message-reply-wrapper').css({
                    width: messageWidth
                }).addClass("messageBoxFixed");
            }
        }

    });
}

function messagefeedresult() {
        if (MessageUserHttp.readyState == 4) {
            if (MessageUserHttp.status == 200 || MessageUserHttp.status == 0) {
                var result = MessageUserHttp.responseText;
                var resultArr = result.split('~#~');
                document.getElementById('message-feed').innerHTML = resultArr[1];
                //$('#message-feed-wrapper').perfectScrollbar({ includePadding:true,wheelSpeed: 100});

                $('#message-reply').keypress(function(event) {
                    if (event.which == 13) {
                        event.preventDefault();
                        postmessage();
                    } else {
                        thisEl.focus();
                    }

                });

                messageInputBox();

                $(window).resize(function() {
                    messageInputBox();
                })




            } else alert("Retrieval Error: " + MessageUserHttp.statusText);
        }
    }
    // FUNCTION TO SHOW MY MESSAGES
var MyMessagesHttp;

function allmessagefeed(n) {
    document.getElementById('all-message-feed').innerHTML = '<div style="margin-left:25px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>';
    MyMessagesHttp = Browser_Check(MyMessagesHttp);

    var url = "ajax_mymessages.php";
    var data = "archive=" + n;
    MyMessagesHttp.open("POST", url, true);
    MyMessagesHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    MyMessagesHttp.setRequestHeader("Content-length", data.length);
    MyMessagesHttp.setRequestHeader("Connection", "close");

    MyMessagesHttp.onreadystatechange = allmessagefeedresult;
    MyMessagesHttp.send(data);
}

function allmessagefeedresult() {
        if (MyMessagesHttp.readyState == 4) {
            if (MyMessagesHttp.status == 200 || MyMessagesHttp.status == 0) {
                var result = MyMessagesHttp.responseText;
                document.getElementById('all-message-feed').innerHTML = result;
            } else alert("Retrieval Error: " + MyMessagesHttp.statusText);
        }
    }
    // FUNCTION TO SHOW NEW MESSAGES
var NewMessagesHttp;

function newmessagefeed(n, shortid, company, dateFrom, dateTo, search) {

    var url = BASE_URL + "/message/dbeemessage";
    shortid = (typeof shortid == 'undefined') ? '' : shortid;
    var search = (typeof search == 'undefined') ? '' : search;
    var companyName = $.trim($('#sertextmsg-compny').val());
    company = (typeof company == 'undefined') ? '' : company;
    var dateFrom = $('#sertextmsgfrom').val();
    dateFrom = (typeof dateFrom == 'undefined') ? '' : dateFrom;
    var dataTo = $('#sertextmsgto').val();
    dateTo = (typeof dateTo == 'undefined') ? '' : dateTo;
    var data = "shortid=" + shortid + "&archive=" + n + "&company=" + company + "&dateFrom=" + dateFrom + "&dateTo=" + dateTo + "&search=" + search;
    $.ajax({
        url: url,
        type: "POST",
        data: data,
        beforeSend: function() {
            $('#dbee-feeds').html('');
            $dbLoader('#dbee-feeds', 1, '', 'center');

          //  $('#btnsubmsgsearch i').remove();
           // $('#btnsubmsgsearch').append(' <i class="fa fa-spinner fa-spin"></i>');

        },
        success: function(response) {            
            var msg ='';
           
            $('#btnsubmsgsearch i').remove();
            if (response.success == 0) msg = '<li><div class="noFound">You currently have no messages</div></li>';

            if (response.success == 1) {
                $(".msgDrpDwn").css('display', 'block');
            }
            if ($('#dbees-feeds-wrapper').is(':visible') == true) {
                $('#dbees-feeds-wrapper').html('').append('<ul class="postListing"></ul>');
            }
             if (response.success == 1) {
            $.each(response.content, function(i, value){
                var msgTxt = value.Message;
                 msgTxt = msgTxt.replace(/(?:<br[^>]*>\s*){2,}/g, '<br />');
                   msg +='<li id="message-'+value.rowid+'" class="'+value.class+'" ChkUser="'+value.ChkUser+'" read="'+value.read+'" search="'+value.search+'" from="'+value.from+'" to="'+value.to+'">\
                      <div style="float:left; width:50px; height:50px;">'+value.profilelink+'<img src="'+IMGPATH+'/users/small/'+value.pic+'" width="50" height="50" border="0" /></a></div>\
                        <div id="dbcomment-speechwrapper1" '+value.cursor+'><div class="msgTxtbox"><div class="messageTextTop">\
                        <div style="float:left">'+value.sendlabel+'<b rel="dbTip" title="'+value.userTypenal+'">'+value.userName+'</b></div>'+value.archiveDiv+'<div id="blockuser-div-'+value.rowid+'" class="pull-left">'+value.blockDiv+'</div>\
                    </div>\
                    <div class="messageTextList cc">'+msgTxt+'</div>'+value.morebtn+'</div><div style="float:right; width:auto; font-size:10px; color:#666">'+value.MsgDate+'</li>';
                    //$("#dbee-feeds, #dbees-feeds-wrapper ul").append(msg);
               
              });
        }
     $("#dbee-feeds, #dbees-feeds-wrapper ul").html(msg);
           

            //$("#dbee-feeds, #dbees-feeds-wrapper ul").html(data);



        }
    });
}


// FUNCTION TO POST MESSAGE

function topmessagedisplay(idorclass, inorout, userid, editable) {
    userid = typeof(userid) != 'undefined' ? userid : '';
    inorout = typeof(inorout) != 'undefined' ? inorout : '';
    editable = typeof(editable) != 'undefined' ? editable : '';
    var data = 'userID=' + userid + '&call=' + inorout + '&editable=' + editable;
    $.ajax({
        type: "POST",
        dataType: 'json',
        data: data,
        beforeSend: function() {
            $('.fa-refresh', $(idorclass)).addClass('fa-spin')
        },
        url: BASE_URL + "/message/topmessage",
        success: function(response) {
            $(idorclass).html(response);
            if (editable == 'yesedit') {
                $('.showmsgarrive').html('No new messages');
                $('.showmsghighlight .prstTitle a').text('see previous');
                $('.showmsghighlight').removeClass('msgHighlight');
            }
        }
    });
}
var PostMessageHttp;

function postmessage(from) {
    var msg = $('#message-reply');
    if (msg.val() == "") {
        msg.focus();

        if (from != 'popup') {
            document.getElementById('message-reply-wrapper-2').setAttribute('class', 'message-reply-wrapper-shadow');
        }
    } else {
        if (from != 'popup') {
            document.getElementById('message-reply-wrapper-2').setAttribute('class', 'message-reply-wrapper');
        }
        from = typeof(from) != 'undefined' ? from : 'msgmain';
        var user = $('#hiddenuser').val();
        var message = $('#message-reply').val();
        var parent = $('#parent').val();

        $('#postmessagesend').removeAttr('onclick');
        $('#postmessagesend').addClass('processBtnLoader').append(' <i class="fa fa-spinner fa-spin"></i>');
        //$dbLoader('#message-feed-wrapper', 1);
        createrandontoken();
        // var data = "&user=" + user + "&message=" + message + "&parent=" + parent + '&'+userdetails; 

    message = conMentionTotext(message, false);

        $.ajax({
            type: "POST",
            data: {
                user: user,
                message: message,
                parent: parent
            },
            url: BASE_URL + "/message/add",
            success: function(response) {
                if (response == 'false') {
                    window.location.href = BASE_URL + "/myhome/logout/";
                    return false;
                    
                }
                if(localTick == false){
                    callsocket();
                }
                $("#message-feed-wrapper").animate({"scrollTop": $('#message-feed-wrapper')[0].scrollHeight}, "slow");
                $('#postmessagesend').removeClass('processBtnLoader');
                $('#postmessagesend .fa').remove();
                $('#postmessagesend').attr('onclick', 'javascript:postmessage();');
                if (response.user != '-1' && response.status != 'error') {
                    if (from == 'msgmain') {

                        $.ajax({
                            type: "POST",
                            data: {
                                user: response.user,
                                rand: response.rand                                
                            },
                            url: BASE_URL + '/message/reloadmessages',
                            success: function(response) {
                                $('#message-feed-wrapper').fadeOut('slow');
                                $('#message-feed-wrapper').perfectScrollbar({
                                    includePadding: true
                                });
                                $('#message-feed-wrapper').fadeIn("slow");
                                $('#message-reply').val('');
                                 message = response.content.replace(/(?:<br[^>]*>\s*){2,}/g, '<br />');
                                $('#message-feed-wrapper').prepend(message);
                            }
                        });

                    } else if (from == 'popup') {
                        $('#sendmessagepopup-content').css('display', 'none');
                        $('#sendmessage-msg').css('display', 'block');
                        setTimeout("parent.Shadowbox.close()", 3000);
                    }
                } else {
                    if (response.vip == 1) {
                        $('#message-reply-wrapper').html('<div class="user-blocked-box">You cannot message this user.</div>');
                    } else {
                        $('#message-reply-wrapper').html('<div class="user-blocked-box">You cannot send a message as ' + response.username + ' has blocked you.</div>');
                    }
                }

            }
        });


    };


}



function postmessageresult(from) {
    if (PostMessageHttp.readyState == 4) {
        if (PostMessageHttp.status == 200 || PostMessageHttp.status == 0) {
            var result = PostMessageHttp.responseText;
            //alert(result);return false;
            // in case of invalid authentication
            if (result == 'false') {
                window.location.href = BASE_URL + "/myhome/logout/";
                return false;
            }
            // in case of invalid authentication
            var resultArr = result.split('~');
           // document.getElementById('signuploader').style.display = 'none';
            $('#postmessagesend').removeClass('processBtnLoader');
            $('#postmessagesend .fa').remove();
            $('#postmessagesend').attr('onclick', 'javascript:postmessage();');
            if (resultArr[0] != '-1') {
                if (from == 'msgmain') {
                    setTimeout('reloadMessages(' + resultArr[0] + ',' + resultArr[1] + ')', 700);
                    //setTimeout('gotomessagereload(' + resultArr[0] + ',' + resultArr[2] + ')', 700);

                    $('#message-reply').value = '';
                } else if (from == 'popup') {
                    $('#sendmessagepopup-content').css('display', 'none');
                    $('#sendmessage-msg').css('display', 'block');
                    setTimeout("parent.Shadowbox.close()", 3000);
                }
            } else {
                document.getElementById('message-reply-wrapper').innerHTML = '<div class="user-blocked-box">You cannot send a message as ' + resultArr[2] + ' has blocked you.</div>';
            }
        } else {};
    }
}

function postmessagetwo(from) {
    $('#sendmessage').append(' <i class="fa fa-spinner fa-spin"> </i>').css('cursor', 'default').removeAttr("onclick");
    var user = $('#hiddenuser').val();
    var message = $('#message-reply').val();
    var parent = $('#parent').val();
    var err = 'false';
    if (message == '') {
        //$messageError("You don't send any message!");
        $dbConfirm({
            content: "You don't send any message!",
            yes: false,
            error: true
        });
        $('#sendmessage').attr("onclick", "javascript:postmessagetwo();");
        $('#sendmessage .fa-spin').remove();
        $('#sendmessage').css('cursor', 'pointer');
        err = 'true';
        return false;
    }
    if (err == 'false') {
        createrandontoken(); // creting user session and token for request pass 		
        var messagedbeeval = 'messagedbeeval';
        //var data = "user=" + user + "&message=" + message+ "&messagedbeeval=" + messagedbeeval + "&parent=" + parent+'&'+userdetails;	

         message = conMentionTotext(message, false);

        $.ajax({
            type: "POST",
            data: {
                user: user,
                message: message,
                messagedbeeval: messagedbeeval,
                parent: parent
            },
            url: BASE_URL + '/message/add',

            dataType: 'json',
            async: false,
            beforeSend: function() {
                //$('#sendmessage').append(' <i class="fa fa-spinner fa-spin"> </i>').css('cursor', 'default').removeAttr("onclick");
            },
            complete: function() {
                setTimeout(function() {
                    $('#sendmessage').attr("onclick", "javascript:postmessagetwo();");
                    $('#sendmessage .fa-spin').remove();
                    $('#sendmessage').css('cursor', 'pointer');
                }, 2000);
            },
            cache: false,
            success: function(response) {
                
                if (response.status == "error") {
                    $dbConfirm({
                        content: 'Sorry you cannot message this user',
                        yes: false,
                        error: true
                    });
                    return false;
                }
                if(localTick == false){
                    callsocket();
                }
                if (response.success != "") {
                    //$dbConfirm({content:'Your message has been sent', yes:false});
                    $.dbeePopup('close');
                } else {
                    $dbConfirm({
                        content: 'Sorry you cannot message this user',
                        yes: false
                    });
                }
            }
        });
    }
}



function showoldmessagesresult() {
    if (OldMessagesHttp.readyState == 4) {
        if (OldMessagesHttp.status == 200 || OldMessagesHttp.status == 0) {
            var result = OldMessagesHttp.responseText;
            document.getElementById('message-feed').innerHTML = result;
            document.getElementById('send-message').style.display = 'block';
        } else alert("Retrieval Error: " + OldMessagesHttp.statusText);
    }
}

function gotomessage(uid, mid, read) {
    window.location = '/message/messagedbee/user/' + uid + '/id/' + mid + '/read/' + read;
}
var MessagereloadHttp;

function gotomessagereload(uid, mid) {
    //window.location='messagedetail'+mid;
    document.getElementById('message-feed-wrapper').innerHTML = '<div style="margin-left:25px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>';
    MessagereloadHttp = Browser_Check(MessagereloadHttp);

    var url = BASE_URL + "/message/messagereload";
    var data = "id=" + mid + "&user=" + uid;
    MessagereloadHttp.open("POST", url, true);
    MessagereloadHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    MessagereloadHttp.setRequestHeader("Content-length", data.length);
    MessagereloadHttp.setRequestHeader("Connection", "close");
    MessagereloadHttp.onreadystatechange = messagereloadresult2;
    MessagereloadHttp.send(data);
}

function messagereloadresult2() {

        if (MessagereloadHttp.readyState == 4) {
            if (MessagereloadHttp.status == 200 || MessagereloadHttp.status == 0) {
                var result = MessagereloadHttp.responseText;
                document.getElementById('message-feed-wrapper').innerHTML = result;
                //fadepopup();
            } else {};
        }
    }
    // FUNCTION TO ARCHIVE MESSAGE
var ArchiveMessageHttp;

function archivemessage(user, message, status) {

    $.ajax({
        url: BASE_URL + '/message/archivemessage',
        type: 'POST',
        data: {
            user: user,
            message: message,
            status: status
        },
        dataType: 'html',
        success: function(data) {

            $('[id="message-'+data+'"]').remove();   
            $('#actNotificationMsg #message-'+data+'').remove(); 
            setTimeout("closepopup('fade')", 2000);
        }
    });
}

function removeuserfrominvite(user) {
    var users = document.getElementById('total-users-toinvite').value;
    users = users.replace(user + ',', "");
    document.getElementById('total-users-toinvite').value = users;
    document.getElementById('select-invite-' + user).style.display = 'none';
}

// FUNCTION TO OPEN AND RESIZE GROUPS TOP SUBMENU MENU LINKS WHEN OPENED FROM WITHIN POPUP

function refreshSearchgroups() {
    RefreshShadowbox('/group/searchgroups', '', '125', '640');
}

function refreshCreategroup() {
    RefreshShadowbox('/group/creategroup', '', '325', '600');
}

function refreshGroupnotifications() {
    RefreshShadowbox('/group/notifications', '', '320', '600');
}



// FUNCTION TO FETCH PROFILE DETAILS FOR EDIT
var ProfileDetailsHttp;

// FUNCTION TO SEARCH USERS TO INVITE TO GROUP


// FUNCTION TO LOAD FOLLOWERS TO INVITE TO GROUP
function loadfollowersforexpert() {
    $.ajax({
        type: "POST",
        data: {
            "usertype": 'followers'
        },
        url: BASE_URL + "/dbeedetail/invitefollowing",
        success: function(result) {
            var resultArr = result.split('~#~');
            $('#followers-list').html(resultArr[0]);
            $('#total-followers').val(resultArr[1]);
            loadfollowingforexpert();
        },
        error: function(request, status, error) {

        }
    });
}

// FUNCTION TO LOAD FOLLOWING USERS TO INVITE TO GROUP
function loadfollowingforexpert() {

    $.ajax({
        type: "POST",
        data: {
            "usertype": 'following'
        },
        url: BASE_URL + "/dbeedetail/invitefollowing",
        success: function(result) {
            var resultArr = result.split('~#~');
            $('#following-list').html(resultArr[0]);
            $('#total-following').val(resultArr[1]);
            $.dbeePopup('resize');
        },
        error: function(request, status, error) {

        }
    });
}
var chknewupdateprofilepop = false;
var enableSaveBtn = function() {
    var chknewupdateprofilepop = 1;

}

function checkAnyFormFieldEdited() {

    $('body').on('keypress', ':text', function(e) { // text written 

        chknewupdateprofilepop = '1';
    });
    $('body').on('keypress', '#lname', function(e) { // text written       
        chknewupdateprofilepop = '1';
    });

    $(':text').bind('paste', function(e) { // text pasted
        chknewupdateprofilepop = '1';
    });


    $('body').on('change', 'select', function(e) { // text written 

        chknewupdateprofilepop = '1';
    });

    $('body').on('change', '#makenumberprivate,#makedobprivate,#makeemailprivate', function(e) { // text written       
        chknewupdateprofilepop = '1';
    });

    $('body').on('keypress', 'textarea', function(e) { // text written       
        chknewupdateprofilepop = '1';
    });
    $('body').on('paste', 'textarea', function(e) { // text written       
        chknewupdateprofilepop = '1';
    });



}

function accountSetttingPopup(dataType) {
    plateform_scoring = $('#plateform_scoring').val();
    var asHtml = '<div id="content_data">\
							<h2 class="titlePop">Edit</h2>\
	                		<ul class="tabLinks accountSettingTab">\
		                		<li>\
		                			<a href="javascript:void(0)" id="edit-account-title" class="user-name-grey" data-type="account">Account</a>\
		                		</li>\
		                		<li style="display:none">\
		                			<a href="javascript:void(0)" id="edit-location-title" class="user-name-grey" data-type="location">Location</a>\
		                		</li>\
		                		<li>\
		                			<a href="javascript:void(0)" id="edit-bio-title" class="user-name-grey" data-type="biography">Biography</a>\
		                		</li>';
  /*  if (allSocialstatus == 0) {
        asHtml += '<li>\
								<a href="javascript:void(0)" id="edit-social-title" class="user-name-grey" data-type="social">Social</a>\
		                	  </li>';
    }
*/
    if (Socialtype == 0 && userID != adminID) {
        asHtml += '<li>\
								<a href="javascript:void(0)" id="edit-password-title" class="user-name-grey" data-type="password">Password</a>\
		                	  </li>';
    }
    asHtml += '<li>\
            			<a href="javascript:void(0)" id="makePrivate" class="user-name-grey" data-type="privacy">Privacy</a>\
            		</li>';
    /*
					if(plateform_scoring==0){
            		asHtml +='<li>\
            			<a href="javascript:void(0)"id="edit-scoring-title" class="user-name-grey" data-type="scoring">Scoring</a>\
            		</li>';
            	}
				*/
    asHtml += '</ul>\
        		<div id="profile-edit" class="tabContainerWrapper"></div>\
	        		<div id="bio-edit"></div>\
	                <input type="hidden" id="reloadend">\
			</div>';
    if ($('#dbeePopupWrapper').is(':visible') != true) {
        $.dbeePopup(asHtml, {
            otherBtn: '<div class="processUpdateprofile" style="display:none">\
													<a style="margin-left:5px;" href="javascript:void(0)" data-saveclose="1" class="btn btn-yellow pull-right closeclass" onclick="javascript:updateprofile(1);">Update & Close</a>&nbsp;&nbsp;<a href="javascript:void(0)" class="btn btn-yellow pull-right" onclick="javascript:updateprofile();">Update</a>\
													</div><div class="processUpdatebio" style="display:none">\
														<a style="margin-left:5px;" href="javascript:void(0)" data-saveclose="1" class="btn btn-yellow pull-right closeclass" onclick="javascript:updatebio(1);">Update & Close</a>&nbsp;&nbsp;<a href="javascript:void(0)" class="btn btn-yellow pull-right" onclick="javascript:updatebio();">Update</a>\
													</div><div class="processUpdatelocation" style="display:none">\
														<a style="margin-left:5px;" href="javascript:void(0)" data-saveclose="1" class="btn btn-yellow pull-right closeclass" onclick="javascript:updatelocation(1);">Update & Close</a>&nbsp;&nbsp;<a href="javascript:void(0)" class="btn btn-yellow pull-right" onclick="javascript:updatelocation();">Update</a>\
													</div><div id="forgotpassm"></div><div class="processUpdatesocial" style="display:none">\
														<a style="margin-left:5px;" href="javascript:void(0)" data-saveclose="1" class="btn btn-yellow pull-right closeclass" onclick="javascript:updatesocial(1);">Update & Close</a>&nbsp;&nbsp;<a href="javascript:void(0)" class="btn btn-yellow pull-right" onclick="javascript:updatesocial();">Update</a>\
													</div><div id="updateprivacy"></div><div class="updateprivacy" style="display:none">\
														<a style="margin-left:5px;" href="javascript:void(0)" data-saveclose="1" class="btn btn-yellow pull-right closeclass" onclick="javascript:updateprivacy(1);">Update & Close</a>&nbsp;&nbsp;<a href="javascript:void(0)" class="btn btn-yellow pull-right" onclick="javascript:updateprivacy();">Update</a>\
													</div>\
											'
        });
    }

    $dbLoader('#profile-edit');
    if (dataType == 'biography') {
        fetchbiodetails()
    } else if (dataType == 'social') {
        fetchsocialdetails()
    } else if (dataType == 'password') {
        fetchpassworddetails()
    } else if (dataType == 'privacy') {
        makePrivate();
    } else if (dataType == 'scoring') {
        //fetchscoringdetails();
    } else if (dataType == 'location') {
        fetchlocationdetails();
    } else {
        fetchprofiledetails();
    }

    checkAnyFormFieldEdited();

};

function makePrivate() {
    $('.dbeePopContent .tabLinks a').removeClass('active');
    $('#makePrivate').addClass('active');
    $.ajax({
        type: "POST",
        dataType: 'json',
        url: BASE_URL + "/settings/makeprivate",
        beforeSend: function() {
            $('#profile-edit').html('');
            $dbLoader('#profile-edit', 1);
        },
        success: function(response) {
            $('#profile-edit').html(response.content);
            $('.updateprivacy').show();
            $('.processUpdateprofile').hide();
            $('.processUpdatebio').hide();
            $('.processUpdatelocation').hide();
            $('.processUpdatesocial').hide();
            $.dbeePopup('resize');
        }
    });
};

function fetchprofiledetails() {
    ProfileDetailsHttp = Browser_Check(ProfileDetailsHttp);
    $('#profile-edit').html('');
    $dbLoader('#profile-edit', 1);
    $('.dbeePopContent .tabLinks a').removeClass('active');
    $('#edit-account-title').addClass('active');
    var url = BASE_URL + "/settings/accountedit";
    var data = "";
    ProfileDetailsHttp.open("POST", url, true);
    ProfileDetailsHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ProfileDetailsHttp.setRequestHeader("Content-length", data.length);
    ProfileDetailsHttp.setRequestHeader("Connection", "close");
    ProfileDetailsHttp.onreadystatechange = fetchprofiledetailsresult;
    ProfileDetailsHttp.send(data);
    $('.processUpdateprofile').show();
    $('.processUpdatebio').hide();
    $('.processUpdatesocial').hide();
    $('.updateprivacy').hide();
    $('.processUpdatelocation').hide();
}

function fetchprofiledetailsresult() {
        if (ProfileDetailsHttp.readyState == 4) {
            if (ProfileDetailsHttp.status == 200 || ProfileDetailsHttp.status == 0) {
                var result = ProfileDetailsHttp.responseText;
                var resultArr = result.split('~#~');
                document.getElementById('profile-edit').innerHTML = resultArr[0];
                $.dbeePopup('resize');


                for (i = 0; i < document.getElementById('birthmonth').length; i++) {
                    if (document.getElementById('birthmonth').options[i].value == resultArr[1]) {
                        document.getElementById('birthmonth').options[i].selected = true;
                        break;
                    }
                }


            } else alert("Retrieval Error: " + ProfileDetailsHttp.statusText);
        }
    }
    // FUNCTION TO FETCH USER BIOGRAPHY DETAILS FOR EDIT
var BioDetailsHttp;

function fetchbiodetails() {

    $('#profile-edit').html('');
    $dbLoader('#profile-edit', 1);
    $('.dbeePopContent .tabLinks a').removeClass('active');
    $('#edit-bio-title').addClass('active');

    var url = BASE_URL + '/settings/biography';
    var data = "";
      $('.processUpdateprofile').hide();
    $('.processUpdatebio').show();
    $('.processUpdatesocial').hide();
    $('.updateprivacy').hide();
    $('.processUpdatelocation').hide();

   $.ajax({
        url:url,
        type: 'POST',
        data: data,
        success:function(response){
             $('#profile-edit').html(response);
             $.dbeePopup('resize');
        }
    });

  
}

    // FUNCTION TO FETCH USERS SOCIAL DETAILS FOR EDIT

function fetchlocationdetails() {
    BioDetailsHttp = Browser_Check(BioDetailsHttp);
    $('#profile-edit').html('');
    $dbLoader('#profile-edit', 1);
    $('.dbeePopContent .tabLinks a').removeClass('active');
    $('#edit-location-title').addClass('active');

    var url = BASE_URL + '/settings/location';
    var data = "";

    BioDetailsHttp.open("POST", url, true);
    BioDetailsHttp.setRequestHeader("Content-type", "application/json");
    BioDetailsHttp.setRequestHeader("Content-length", data.length);
    BioDetailsHttp.setRequestHeader("Connection", "close");
    BioDetailsHttp.onreadystatechange = fetchlocationdetailsresult;
    BioDetailsHttp.send(data);
    $('.processUpdateprofile').hide();
    $('.processUpdatebio').hide();
    $('.processUpdatesocial').hide();
    $('.updateprivacy').hide();
    $('.processUpdatelocation').show();
}

function format(state) {
    if (!state.countycode) return state.countyname; // optgroup
    return "<img class='flag' src='/img/country/" + state.countycode.toLowerCase() + ".gif'/>" + state.countyname;
}



function fetchlocationdetailsresult() {
        if (BioDetailsHttp.readyState == 4) {
            if (BioDetailsHttp.status == 200 || BioDetailsHttp.status == 0) {
                var result = BioDetailsHttp.responseText;
                dataJson = $.parseJSON(result);
                document.getElementById('profile-edit').innerHTML = dataJson.html.locationDiv;
                locationD = dataJson.html.locationData;
                var select2address = '<div class="formRow">\
				<div class="formField">\
				<input type="text" name="countryAddrss" id="countryAddrss" class="" value="">\
				<i class="optionalText">Address</i>\
				</div>\
				</div>';

                if (dataJson.html.countryis != '') {
                    $("#countryauto").tokenInput(locationD, {
                        minChars: 3,
                        'tokenLimit': 1,
                        onAdd: function(item) {
                            $("#countryAddrss").tokenInput(BASE_URL + '/settings/getaddress?id=' + item.id, {
                                'tokenLimit': 1,
                                minChars: 3
                            });
                        },
                        onDelete: function(item) {
                            $('#select2address').html(select2address);
                        },
                        prePopulate: [{
                            id: dataJson.html.country.countryId,
                            name: dataJson.html.country.countryname,
                            codes: dataJson.html.country.codes
                        }],
                        propertyToSearch: "name",
                        resultsFormatter: function(item) {
                            return "<li>" + "<img src='/img/country/" + item.codes + ".gif' title='" + item.name + "' /> " + item.name + "</li>";
                        },
                        tokenFormatter: function(item) {
                            console.log(item);
                            return "<li><img src='/img/country/" + item.codes + ".gif' title='" + item.name + "'  /> " + item.name + "</li>";
                        }
                    });
                } else {
                    $("#countryauto").tokenInput(locationD, {
                        minChars: 3,
                        'tokenLimit': 1,
                        onAdd: function(item) {
                            $("#countryAddrss").tokenInput(BASE_URL + '/settings/getaddress?id=' + item.id, {
                                'tokenLimit': 1,
                                minChars: 3
                            });
                        },
                        onDelete: function(item) {
                            $('#select2address').html(select2address);
                        },
                        propertyToSearch: "name",
                        resultsFormatter: function(item) {
                            console.log(item);
                            return "<li>" + "<img src='/img/country/" + item.codes + ".gif' title='" + item.name + "' /> " + item.name + "</li>";
                        },
                        tokenFormatter: function(item) {
                            console.log(item);
                            return "<li><img src='/img/country/" + item.codes + ".gif' title='" + item.name + "'  /> " + item.name + "</li>";
                        }
                    });
                }


                if (dataJson.html.countryis != '') {
                    $("#countryAddrss").tokenInput(BASE_URL + '/settings/getaddress?id=' + dataJson.html.country.countryId, {
                        'tokenLimit': 1,
                        minChars: 3,
                        prePopulate: [{
                            id: dataJson.html.city.cityId,
                            name: dataJson.html.city.cityname
                        }]
                    });
                }

                $.dbeePopup('resize');
            } else alert("Retrieval Error: " + BioDetailsHttp.statusText);
        }
    }
    // FUNCTION TO FETCH USERS SOCIAL DETAILS FOR EDIT

var SocialDetailsHttp;

function fetchsocialdetails() {

    SocialDetailsHttp = Browser_Check(SocialDetailsHttp);

    $('#profile-edit').html('');
    $dbLoader('#profile-edit', 1);
    $('.dbeePopContent .tabLinks a').removeClass('active');
    $('#edit-social-title').addClass('active');

    var url = BASE_URL + "/settings/social";
    var data = "";

    SocialDetailsHttp.open("POST", url, true);
    SocialDetailsHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    SocialDetailsHttp.setRequestHeader("Content-length", data.length);
    SocialDetailsHttp.setRequestHeader("Connection", "close");
    SocialDetailsHttp.onreadystatechange = fetchsocialdetailsresult;
    SocialDetailsHttp.send(data);
    $('.processUpdateprofile').hide();
    $('.processUpdatebio').hide();
    $('.updateprivacy').hide();
    $('.processUpdatesocial').show();
    $('.processUpdatelocation').hide();

}

function fetchsocialdetailsresult() {
        if (SocialDetailsHttp.readyState == 4) {
            if (SocialDetailsHttp.status == 200 || SocialDetailsHttp.status == 0) {
                var result = SocialDetailsHttp.responseText;
                var resultArr = result.split('~#~');
                document.getElementById('profile-edit').innerHTML = resultArr[0];
                $.dbeePopup('resize');

            } else {};
        }
    }
    // FUNCTION TO FETCH USER PASSWORD DETAILS FOR EDIT
var PasswordDetailsHttp;

function fetchpassworddetails() {
    PasswordDetailsHttp = Browser_Check(PasswordDetailsHttp);
    $('#profile-edit').html('');
    $dbLoader('#profile-edit', 1);
    $('.dbeePopContent .tabLinks a').removeClass('active');
    $('#edit-password-title').addClass('active');
    var url = BASE_URL + "/settings/changepassword";
    var data = "";

    PasswordDetailsHttp.open("POST", url, true);
    PasswordDetailsHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    PasswordDetailsHttp.setRequestHeader("Content-length", data.length);
    PasswordDetailsHttp.setRequestHeader("Connection", "close");
    PasswordDetailsHttp.onreadystatechange = fetchpassworddetailsresult;
    PasswordDetailsHttp.send(data);
    $('.processUpdateprofile').hide();
    $('.processUpdatebio').hide();
    $('.updateprivacy').hide();
    $('.processUpdatesocial').hide();
}

function fetchpassworddetailsresult() {
        if (PasswordDetailsHttp.readyState == 4) {
            if (PasswordDetailsHttp.status == 200 || PasswordDetailsHttp.status == 0) {
                var result = PasswordDetailsHttp.responseText;
                document.getElementById('profile-edit').innerHTML = result;
                $.dbeePopup('resize');
            } else alert("Retrieval Error: " + PasswordDetailsHttp.statusText);
        }
    }
    // FUNCTION TO FETCH USER SCORING SETTINGS FOR EDIT
function fetchscoringdetails(calling) {
    if (calling != 'userprofile') {
        $('#profile-edit').html('');
        $dbLoader('#profile-edit', 1);
        $('.dbeePopContent .tabLinks a').removeClass('active');
        $('#edit-scoring-title').addClass('active');
    }
    $.ajax({
        type: "POST",
        dataType: 'json',
        url: BASE_URL + '/settings/score',

        async: false,
        beforeSend: function() {

        },

        complete: function() {

        },

        cache: false,

        success: function(response) {
            //$('.processUpdateprofile').hide();
            $('.processUpdatebio').hide();
            $('.updateprivacy').hide();
            $('.processUpdatesocial').hide();
            $('#profile-edit').html(response.content);
            if (calling != 'userprofile') $.dbeePopup('resize');


            if (response.scoredNot == 1) // case of score on off from profile
            {
                $('.hidescoreline, .swichedOff').hide();
                $('.swichedOn,.myposition').show();
            } else {
                $('.hidescoreline, .swichedOff').show();
                $('.swichedOn,.myposition').hide();
            }
        },

        error: function(request, status, error) {

            $("#content_data").html("Some problems have occured. Please try again later: " + error);

        }

    });
}

// FUNCTION TO FETCH USER NOTIFICATION SETTINGS FOR EDIT
var NotificationSettingsHttp;

function fetchnotificationdetails() {
    NotificationSettingsHttp = Browser_Check(NotificationSettingsHttp);
    $dbLoader('#profile-edit', 1);

    var url = BASE_URL + "/settings/detail";


    var data = "";

    NotificationSettingsHttp.open("POST", url, true);
    NotificationSettingsHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    NotificationSettingsHttp.setRequestHeader("Content-length", data.length);
    NotificationSettingsHttp.setRequestHeader("Connection", "close");
    NotificationSettingsHttp.onreadystatechange = fetchnotificationdetailsresult;
    NotificationSettingsHttp.send(data);
}

function fetchnotificationdetailsresult() {
    if (NotificationSettingsHttp.readyState == 4) {
        if (NotificationSettingsHttp.status == 200 || NotificationSettingsHttp.status == 0) {
            var result = NotificationSettingsHttp.responseText;
            document.getElementById('profile-edit').innerHTML = result;
            $.dbeePopup('resize');
        } 
    }
}


function updateprivacy(close) {
        $('.updateprivacy .fa-spin').remove();
        var close = typeof(close) != 'undefined' ? close : '0';

        chknewupdateprofilepop = 0;
        if (close == 1) {
            $('.updateprivacy a[data-saveclose]').append(' <i class="fa fa-spinner fa-spin"> </i>').css('cursor', 'default').removeAttr("onclick");
        } else {
            $('.updateprivacy a:not([data-saveclose])').append(' <i class="fa fa-spinner fa-spin"> </i>').css('cursor', 'default').removeAttr("onclick");
        }

        if ($('#makeemailprivate').is(':checked')) {
            var makeemailprivate = 1;
        } else {
            var makeemailprivate = 0;
        }
        if ($('#makenumberprivate').is(':checked')) {
            var makenumberprivate = 1;
        } else {
            var makenumberprivate = 0;
        }
        if ($('#makedobprivate').is(':checked')) {
            var makedobprivate = 1;
        } else {
            var makedobprivate = 0;
        }
        /*
               createrandontoken() 
               -- this will give user seeion value
               -- token value
               -- token keys for security reason
               -- pass its response with data, conconete  +'&'+userdetails at the end of data pass on to Action
           */
        createrandontoken(); // creting user session and token for request pass 
        data += "&makeemailprivate=" + makeemailprivate + "&makenumberprivate=" + makenumberprivate + "&makedobprivate=" + makedobprivate + '&' + userdetails;
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: data,
            url: BASE_URL + '/settings/updateprivacy',

            async: false,
            beforeSend: function() {

            },
            complete: function() {
                setTimeout(function() {
                    $('.updateprivacy a:not([data-saveclose])').attr("onclick", "javascript:updateprivacy();");
                    $('.updateprivacy .fa-spin').remove();
                    $('.updateprivacy a').css('cursor', 'pointer');
                }, 2000);
            },
            cache: false,
            success: function(response) {
                if (response == 'false') {
                    window.location.href = BASE_URL + "/myhome/logout/";
                    return false;
                }
                // in case of invalid authentication
                if (response.status == 'success') {
                    //$dbConfirm({content:response.content, yes:false});
                    if (close == 1) {
                        $.dbeePopup('close');
                    }
                    dashboardprofile(response.user, '0', '1');
                }
            },

            error: function(request, status, error) {

                var errormsg = "Some problems have occured. Please try again later: " + error;
                $dbConfirm({
                    content: errormsg,
                    yes: false
                });

            }

        });
    }
    // FUNCTION TO EDIT PROFILE DETAILS
var EditProfileHttp;

function updateprofile(close) {

    $('.processUpdateprofile .fa-spin').remove();
    chknewupdateprofilepop = 0;
    var close = typeof(close) != 'undefined' ? close : '0';

    var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    var fullname = $.trim($('#fullname').val());
    var lastname = $.trim($('#lname').val());
    var email = $('#email').val();
    var gender = $('#gender').val();
    var birthday = $('#birthday').val();
    var birthmonth = $('#birthmonth').val();
    var birthyear = $('#birthyear').val();
    var title = $('#title').val();
    var company = $('#company').val();
    var mobile = $('#mobile').val();
    var oldemail = $('#oldemail').val();


    var elm = $('#passform required');
        $checkError(elm);
       if($checkError(elm)==false){
        return false;
       }
    if(email=='twitteruser@db-csp.com' || email=='gplususer@db-csp.com'){
         $dbConfirm({
                content: 'Please update your email address',
                yes: false,
                error: true
            });
            return false;
    }
    if (mobile != '') {
        if (mobile.length > 15 || mobile.length < 8) 
        {
            $dbConfirm({
                content: 'Mobile no. is not valid.',
                yes: false,
                error: true
            });
            return false;
        }
        var matchedPosition = mobile.search(/[a-z]/i);
        if (matchedPosition != -1) {
            $dbConfirm({
                content: 'Alphabetic is not allowed in Mobile no. ',
                yes: false,
                error: true
            });
            return false;
        }
    }
    if (close == 1) {
        $('.processUpdateprofile a[data-saveclose]').append('<i class="fa fa-spinner fa-spin"> </i>').css('cursor', 'default').removeAttr("onclick");
    } else {
        $('.processUpdateprofile a:not([data-saveclose])').append(' <i class="fa fa-spinner fa-spin"> </i>').css('cursor', 'default').removeAttr("onclick");
    }

    if (oldemail != email) {
        $dbConfirm({
            content: 'It seems you have changed your registered email address to <span style="color: #cb8129;" >' + email + '</span>. We will send confirmation to your existing registered email address with a link to update it to the new one.<br><br>Do you wish to continue?',
            yes: true,
            yesClick: function() {
                createrandontoken(); // creting user session and token for request pass 

                data += "&title=" + title + "&company=" + company + "&fullname=" + fullname + "&lastname=" + lastname + "&email=" + email + "&gender=" + gender + "&birthday=" + birthday + "&birthmonth=" + birthmonth + "&birthyear=" + birthyear + "&makeprivate=" + makeprivate + "&mobile=" + mobile + '&' + userdetails;
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    data: data,
                    url: BASE_URL + '/settings/update',
                    async: false,
                    complete: function() {
                        setTimeout(function() {
                            $('.processUpdateprofile a:not([data-saveclose])').attr("onclick", "javascript:updateprofile();");
                            $('.processUpdateprofile .fa-spin').remove();
                            $('.processUpdateprofile a').css('cursor', 'pointer');
                        }, 2000);
                    },

                    cache: false,

                    success: function(response) {
                        if (close == 1) {
                            $.dbeePopup('close');
                        }
                        if (response.status == 'error') {
                            $dbConfirm({
                                content: response.message,
                                yes: false,
                                error: true
                            });
                            if (close == 1) {
                                //setTimeout(function(){$.dbeePopup('close');}, 5000);
                                $.dbeePopup('close');
                            }
                            return false;
                        }
                        if (response.status == 'emailsuccess') 
                        {
                            $dbConfirm({
                                content: response.message,
                                yes: false,
                                error: true,
                                delay: 5000
                            });
                            if (close == 1) {
                                $.dbeePopup('close');
                            }
                            $('#oldemail').val(email);
                            $('#email').val(email);
                            $('#email').attr('disabled','disabled');
                            return false;
                        }
                    }
                });

            },
            noClick: function() {
                $('.processUpdateprofile a[data-saveclose]').attr("onclick", "javascript:updateprofile(1);");
                $('.processUpdateprofile a:not([data-saveclose])').attr("onclick", "javascript:updateprofile();");
                $('.processUpdateprofile .fa-spin').remove();
                $('.processUpdateprofile a').css('cursor', 'pointer');

            }
        });

        return false;
    }

    if ($('#makeprivate').is(':checked')) {
        var makeprivate = 1;
    } else {
        var makeprivate = 0;
    }


    createrandontoken(); // creting user session and token for request pass 
    data += "&title=" + title + "&company=" + company + "&fullname=" + fullname + "&lastname=" + lastname + "&email=" + email + "&gender=" + gender + "&birthday=" + birthday + "&birthmonth=" + birthmonth + "&birthyear=" + birthyear + "&makeprivate=" + makeprivate + "&mobile=" + mobile + '&' + userdetails;
    $.ajax({
        type: "POST",
        dataType: 'json',
        //data:{'title':title,'company':company,'fullname':fullname, 'email':email, 'gender':gender, 'birthday':birthday, 'birthmonth':birthmonth, 'birthyear':birthyear,'makeprivate':makeprivate},
        data: data,
        url: BASE_URL + '/settings/update',

        async: false,
        beforeSend: function() {
            // $('.processUpdateprofile a').append(' <i class="fa fa-spinner fa-spin"> </i>').css('cursor', 'default').removeAttr("onclick");
        },

        complete: function() {
            setTimeout(function() {
                $('.processUpdateprofile a:not([data-saveclose])').attr("onclick", "javascript:updateprofile();");
                $('.processUpdateprofile .fa-spin').remove();
                $('.processUpdateprofile a').css('cursor', 'pointer');
            }, 2000);
        },

        cache: false,

        success: function(response) {
            //alert(response);return false;
            // in case of invalid authentication

            if (response == 'false') {
                window.location.href = BASE_URL + "/myhome/logout/";
                return false;
            }
            // in case of invalid authentication
            if (response.status == 'success') {

                //$dbConfirm({content:response.message, yes:false});					
                dashboardprofile(response.user, '0', '1');

                if (close == 1) {
                    //setTimeout(function(){$.dbeePopup('close');}, 2000);
                    $.dbeePopup('close');
                }

            } else if (response.notify == 1) {
                $('#passform').html(response.message);
            }
        },

        error: function(request, status, error) {

            //$messageError("Some problems have occured. Please try again later: "+ error);
            var errormsg = "Some problems have occured. Please try again later: " + error;
            $dbConfirm({
                content: errormsg,
                yes: false,
                error: true
            });

        }

    });

}

function updatelocation(close, fetch) {
    var fetch = typeof(fetch) != 'undefined' ? fetch : false;
    chknewupdateprofilepop = 0;
    var url = BASE_URL + "/settings/editlocation";
    if (fetch != true) {
        $('.processUpdatelocation .fa-spin').remove();
        var close = typeof(close) != 'undefined' ? close : '0';
        if (close == 1) {
            $('.processUpdatelocation a[data-saveclose]').append(' <i class="fa fa-spinner fa-spin"> </i>').css('cursor', 'default').removeAttr("onclick");
        } else {
            $('.processUpdatelocation a:not([data-saveclose])').append(' <i class="fa fa-spinner fa-spin"> </i>').css('cursor', 'default').removeAttr("onclick");
        }

        var country = $("#countryauto").val();
        var city = $("#countryAddrss").val();
        if (country == '' || city == '') {
            $dbConfirm({
                content: 'Please select country and city',
                yes: false,
                error: true
            });
            return false;
        }

        $("#SignupErr").hide();
        $("#signuploader").show();
        createrandontoken();
        var data = "city=" + city + "&country=" + country;
    } else {
        var data = {
            fetch: true,
            UserID: userID
        }
    }
    $.ajax({
        url: url,
        type: "POST",
        data: data,
        dataType: 'json',
        success: function(response) {
            dashboardprofile(userID, '0', '1');
            if (close == 1) {
                setTimeout(function() {
                    $.dbeePopup('close');
                }, 2000);
            } else {
                setTimeout(function() {
                    $('.processUpdatelocation a:not([data-saveclose])').attr("onclick", "javascript:updatelocation();");
                    $('.processUpdatelocation .fa-spin').remove();
                    $('.processUpdatelocation a').css('cursor', 'pointer');
                }, 2000);
            }
        }
    });

}

// FUNCTION TO EDIT BIO DETAILS
var EditBioHttp;

function updatebio(close, fetch) {
    var fetch = typeof(fetch) != 'undefined' ? fetch : false;
    chknewupdateprofilepop = 0;
    var url = BASE_URL + "/settings/editbio";
    if (fetch != true) {
        $('.processUpdateprofile .fa-spin').remove();
        var close = typeof(close) != 'undefined' ? close : '0';
        if (close == 1) {
            $('.processUpdatebio a[data-saveclose]').append(' <i class="fa fa-spinner fa-spin"> </i>').css('cursor', 'default').removeAttr("onclick");
        } else {
            $('.processUpdatebio a:not([data-saveclose])').append(' <i class="fa fa-spinner fa-spin"> </i>').css('cursor', 'default').removeAttr("onclick");
        }

        var thisFormValue = $('#bioformraj').serializeArray();

        $("#SignupErr").hide();
        $("#signuploader").show();
        createrandontoken();

        var data = thisFormValue;
        data.push({
            name: 'fetch',
            value: false
        });
        data.push({
            name: 'update',
            value: 1
        });

    } else {
        var data = {
            fetch: true,
            UserID:$('#dbeeuser').val(),
            update: 0
        }
    }

    $.ajax({
        url: url,
        type: "POST",
        data: data,
        success: function(response) {
            var obj = response;

            updatebioresult(obj, fetch);
            if (close == 1) {
                setTimeout(function() {
                    $.dbeePopup('close');
                }, 2000);
            }
        }
    });

}


function updatebioresult(obj, fetch) {

        var bioData = obj.content;



        $('#bioData').html(bioData);

        if (fetch == false) {
            setTimeout(function() {
                $('.processUpdatebio a:not([data-saveclose])').attr("onclick", "javascript:updatebio();");
                $('.processUpdatebio .fa-spin').remove();
                $('.processUpdatebio a').css('cursor', 'pointer');
            }, 2000);

            // in case of invalid authentication
            if (obj.resval == '1') {
                $('#signuploader').hide();
                // $.dbeePopup('close');
                var id = $('#profileuser').val();
                $.ajax({
                    url: BASE_URL + "/Dashboarduser/profilepersent",
                    type: "POST",
                    data: {
                        "userid": id
                    },
                    success: function(response) {
                        $('#lkdac2').html('+' + response.profilepersent + '%');
                        $('#plink2 span.progressComplete').css('width', response.profilepersent + '%');
                        //$.dbeePopup('close');
                    }
                });
            }

        }

    }
    // FUNCTION TO EDIT SOCIAL DETAILS
var EditSocialHttp;

function updatesocial(close) {
    var close = typeof(close) != 'undefined' ? close : '0';
    chknewupdateprofilepop = 0;
    if (close == 1) {
        $('.processUpdatesocial a[data-saveclose]').append(' <i class="fa fa-spinner fa-spin"> </i>').css('cursor', 'default').removeAttr("onclick");
    } else {
        $('.processUpdatesocial a:not([data-saveclose])').append(' <i class="fa fa-spinner fa-spin"> </i>').css('cursor', 'default').removeAttr("onclick");
    }

    err = false;
    var socialfb = $("#socialfb").val();
    var socialtwitter = $("#socialtwitter").val();
    var sociallinkedin = $("#sociallinkedin").val();
    var cheditsoci = $("#cheditsoci").val();
    var UserID = $("#UserID").val();
    var url = $('input.surl').val();
    url_validate = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
    if (!url_validate.test(socialfb) && socialfb != '') {
        $('.processUpdatesocial a:not([data-saveclose])').attr("onclick", "javascript:updatesocial();");
        $('.processUpdatesocial .fa-spin').remove();
        $('.processUpdatesocial a').css('cursor', 'pointer');
        $dbConfirm({
            content: 'facebook link not valid',
            yes: false,
            error: true
        });
        return false;
    }
    if (!url_validate.test(socialtwitter) && socialtwitter != '') {
        $('.processUpdatesocial a:not([data-saveclose])').attr("onclick", "javascript:updatesocial();");
        $('.processUpdatesocial .fa-spin').remove();
        $('.processUpdatesocial a').css('cursor', 'pointer');
        $dbConfirm({
            content: 'twitter link not valid',
            yes: false,
            error: true
        });
        return false;
    }
    if (!url_validate.test(sociallinkedin) && sociallinkedin != '') {
        $('.processUpdatesocial a:not([data-saveclose])').attr("onclick", "javascript:updatesocial();");
        $('.processUpdatesocial .fa-spin').remove();
        $('.processUpdatesocial a').css('cursor', 'pointer');
        $dbConfirm({
            content: 'linkedin link not valid',
            yes: false,
            error: true
        });
        return false;
    }
    if (!err) {
        $.ajax({
            type: "POST",
            url: BASE_URL + "/settings/social",
            data: {
                "socialfb": socialfb,
                "socialtwitter": socialtwitter,
                "sociallinkedin": sociallinkedin,
                "cheditsoci": cheditsoci,
                "UserID": UserID
            },
            complete: function() {
                setTimeout(function() {
                    $('.processUpdatesocial a:not([data-saveclose])').attr("onclick", "javascript:updatesocial();");
                    $('.processUpdatesocial .fa-spin').remove();
                    $('.processUpdatesocial a').css('cursor', 'pointer');
                }, 2000);
            },
            success: function(data) {
                if (data == 'false') {
                    window.location.href = BASE_URL + "/myhome/logout/";
                    return false;
                }
                if (data == '1') {
                    //$dbConfirm({content:'update successfully-1', yes:false});
                    //setTimeout("closepopup('fade')", 2000);
                    //$('.findMeOn').html(data.content);
                    // if (obj.resval == '1') {
                $('#signuploader').hide();
                // $.dbeePopup('close');
                var id = $('#profileuser').val();
                $.ajax({
                    url: BASE_URL + "/Dashboarduser/profilepersent",
                    type: "POST",
                    data: {
                        "userid": id
                    },
                    success: function(response) {
                        $('#lkdac2').html('+' + response.profilepersent + '%');
                        $('#plink2 span.progressComplete').css('width', response.profilepersent + '%');
                        //$.dbeePopup('close');
                    }
                });
                    //  }

                }


            }
        });
    } else {
        //$messageError('Something is wrong');
        $dbConfirm({
            content: 'Something is wrong',
            yes: false,
            error: true
        });
    }



    if (!err) {
        //document.getElementById("signuploader").style.display = 'block';
        EditSocialHttp = Browser_Check(EditSocialHttp);
        /*
             createrandontoken() 
             -- this will give user seeion value
             -- token value
             -- token keys for security reason
             -- pass its response with data, conconete  +'&'+userdetails at the end of data pass on to Action

         */
        createrandontoken(); // creting user session and token for request pass
        var url = BASE_URL + "/settings/social";
        var data = "socialfb=" + socialfb + "&socialtwitter=" + socialtwitter + "&sociallinkedin=" + sociallinkedin + "&cheditsoci=" + cheditsoci + "&UserID=" + UserID + '&' + userdetails;

        EditSocialHttp.open("POST", url, true);
        EditSocialHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        EditSocialHttp.setRequestHeader("Content-length", data.length);
        EditSocialHttp.setRequestHeader("Connection", "close");
        EditSocialHttp.onreadystatechange = updatesocialresult;
        EditSocialHttp.send(data);

        if (close == 1) {
            setTimeout(function() {
                $.dbeePopup('close');
            }, 3000);
        }

    } else {
        document.getElementById("SignupErr").style.display = 'block';
    }
}

function updatesocialresult() {
        //var close = typeof (close) != 'undefined' ? close : '0';
        if (EditSocialHttp.readyState == 4) {
            if (EditSocialHttp.status == 200 || EditSocialHttp.status == 0) {
                var GetResult = EditSocialHttp.responseText;
                // in case of invalid authentication

                if (GetResult == 'false') {
                    window.location.href = BASE_URL + "/myhome/logout/";
                    return false;
                }
                // in case of invalid authentication
                if (GetResult == '1') {
                    //document.getElementById("signuploader").style.display = 'none';         
                    //$dbConfirm({content:'Your social has been updated', yes:false});

                }
            } else {};
        }
    }
    // FUNCTION TO EDIT PASSWORD
var EditPasswordHttp;

function updatepassword() {
    err = false;
    var curr_password = document.getElementById("curr_password").value;
    var new_password = document.getElementById("new_password").value;
    var new_repeat_password = document.getElementById("new_repeat_password").value;

    if (new_password != new_repeat_password)
        err = true;

    if (!err) {
        document.getElementById("PassErr").style.display = 'none';
        document.getElementById("ConfirmPassErr").style.display = 'none';
        //document.getElementById("signuploader").style.display='block';
        EditPasswordHttp = Browser_Check(EditPasswordHttp);

        var url = "ajax_editpassword.php";
        var data = "curr_password=" + curr_password + "&new_password=" + new_password + "&new_repeat_password=" + new_repeat_password;

        EditPasswordHttp.open("POST", url, true);
        EditPasswordHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        EditPasswordHttp.setRequestHeader("Content-length", data.length);
        EditPasswordHttp.setRequestHeader("Connection", "close");
        EditPasswordHttp.onreadystatechange = updatepasswordresult;
        EditPasswordHttp.send(data);
    } else {
        document.getElementById("ConfirmPassErr").style.display = 'block';
    }
}

function updatepasswordresult() {
        if (EditPasswordHttp.readyState == 4) {
            if (EditPasswordHttp.status == 200 || EditPasswordHttp.status == 0) {
                var GetResult = EditPasswordHttp.responseText;
                if (GetResult == '0') {
                    document.getElementById("PassErr").style.display = 'block';
                    document.getElementById("signuploader").style.display = 'none';
                } else {
                    document.getElementById("curr_password").value = '';
                    document.getElementById("new_password").value = '';
                    document.getElementById("new_repeat_password").value = '';
                    document.getElementById("signuploader").style.display = 'none';
                    document.getElementById("PassChanged").style.display = 'block';
                    setTimeout("document.getElementById('PassChanged').style.display='none'", 3000);
                }
            } 
        }
    }
    // FUNCTION TO EDIT SCORING SETTING
var EditScoringHttp;

function updatescoring(e) {
    e.preventDefault();
    if ($('#scoring-radio input').is(':checked') == true) {
        $('#scoring-radio input').removeAttr('checked');
    } else {
        $('#scoring-radio input').attr('checked', 'checked');
    }
    //$dbConfirm({content:'Your score updated successfully.', yes:false});
    $.ajax({
        type: "POST",
        dataType: 'json',
        data: {
            update_toscoring: 'updatetoscoring'
        },
        url: BASE_URL + '/settings/score',
        success: function(response) {
            if (response.status == 'success') {
                $('#profile-edit').html(response.content);
                if (response.scoredNot == 1) // case of score on off from profile
                {
                    $('.hidescoreline, .swichedOff').hide();
                    $('.swichedOn,.myposition').show();
                } else {
                    $('.hidescoreline, .swichedOff').show();
                    $('.swichedOn,.myposition').hide();
                }

            }
        },

        error: function(request, status, error) {

            $("#content_data").html("Some problems have occured. Please try again later: " + error);

        }

    });


}

// FUNCTION TO EDIT SCORING SETTING

function updatenotificationsetting(id) {
    var notificationsToken = $("#notificationsToken").val();


    $.ajax({
        type: "POST",
        dataType: 'json',
        data: {
            'id': id,
            'notificationsToken': notificationsToken
        },
        url: BASE_URL + '/settings/updatenotification',

        async: true,
        beforeSend: function() {
            if ($('#scoring-radio-' + id + ' input').is(':checked') == true) {
                $('#scoring-radio-' + id + ' input').removeAttr('checked');
            } else {
                $('#scoring-radio-' + id + ' input').attr('checked', 'checked');
            }
        },
        success: function(response) {

            if (response.id == '2') append = '<div class="peoFollow">from people I follow</div>';
            else if (response.id == '4') append = '<div class="peoFollow">from people I dont follow</div>';
            else append = '';

            if (response.status == '1') {
                $('#scoring-radio-' + response.id).find('.peoFollow').remove();
                $('#scoring-radio-' + response.id).parents('.fieldClass').append(append);


                // $('#scoring-radio-' + response.id).html('<a href="javascript:void(0)"  onclick="javascript:updatenotificationsetting(' + response.id + ');"><div id="scoring-status" class="radioTick"></div></a>' + append);
            } else if (response.status == '0') {

                //$('#scoring-radio-' + response.id).find('input[type="checkbox"]').attr('checked', 'checked');
                // $('#scoring-radio-' + response.id).html('<a href="javascript:void(0)" onclick="javascript:updatenotificationsetting(' + response.id + ');"><div id="scoring-status" class="radio"></div></a>' + append);
            }
            $("#notificationsToken").val(response.notificationsToken);
            $dbConfirms({
                content: response.message,
                yes: false
            });
        },

        error: function(request, status, error) {

            $("#content_data").html("Some problems have occured. Please try again later: " + error);

        }

    });


}

// FUNCTION TO CHANGE PROFILE PICTURE
var ChangeProfilePicHttp;

function changeprofilepic(user) {
    ChangeProfilePicHttp = Browser_Check(ChangeProfilePicHttp);
    document.getElementById('editProfilePicDiv').style.display = 'none';
    document.getElementById('AjaxWaitingDiv').style.display = 'block';
    var pic = document.getElementById('UserProfilePic').value;
    var url = BASE_URL + "/profile/changepic";
    var data = "user=" + user + "&picture=" + pic;
    ChangeProfilePicHttp.open("POST", url, true);
    ChangeProfilePicHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ChangeProfilePicHttp.setRequestHeader("Content-length", data.length);
    ChangeProfilePicHttp.setRequestHeader("Connection", "close");
    ChangeProfilePicHttp.onreadystatechange = changeprofilepicresult;
    ChangeProfilePicHttp.send(data);
}

function changeprofilepicresult() {
        if (ChangeProfilePicHttp.readyState == 4) {
            if (ChangeProfilePicHttp.status == 200 || ChangeProfilePicHttp.status == 0) {
                var result = ChangeProfilePicHttp.responseText;
                if (result == '1') {
                    document.getElementById('AjaxWaitingDiv').style.display = 'none';
                    document.getElementById('profile-change-message').innerHTML = 'Profile picture changed successfully';
                    setTimeout('parent.window.location.href=parent.window.location.href;', 2000);
                } else {
                    document.getElementById('AjaxWaitingDiv').style.display = 'none';
                    document.getElementById('profile-change-message').innerHTML = 'There was an error saving your new picture. Please close and try again.';
                }
            } 
        }
    }
    // FUNCTION TO CLOSE USERS DBEE LIFE ACCOUNT
var CloseAccountHttp;

function closeaccount(userid) {
    document.getElementById('confirmclose').innerHTML = '<img src="../images/ajaxloader-big.gif">';
    CloseAccountHttp = Browser_Check(CloseAccountHttp);
    var url = BASE_URL + "/settings/closeaccount";
    var data = "UserID=" + userid;
    CloseAccountHttp.open("POST", url, true);
    CloseAccountHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    CloseAccountHttp.setRequestHeader("Content-length", data.length);
    CloseAccountHttp.setRequestHeader("Connection", "close");
    CloseAccountHttp.onreadystatechange = closeaccountresult;
    CloseAccountHttp.send(data);
}

function closeaccountresult() {
        if (CloseAccountHttp.readyState == 4) {
            if (CloseAccountHttp.status == 200 || CloseAccountHttp.status == 0) {
                var result = CloseAccountHttp.responseText;
                if (result == '1') {
                    document.getElementById('confirmclose').style.display = 'none';
                    document.getElementById('closeaccountmessage').style.display = 'block';
                    setTimeout('parent.parent.window.location.href="../myhome/index"', 5000);
                }
            } else {};
        }
    }
    //FUNCTION TO START TWITTER SEARCH
var SearchTwitterHttp;

function searchtwitter(showloader, stoptweetreply, tweetnum, profileholder, q) {
    var n = '';
    var u = '';
    showloader = typeof(showloader) != 'undefined' ? showloader : '1';
    stoptweetreply = typeof(stoptweetreply) != 'undefined' ? stoptweetreply : '0';
    profileholder = typeof(profileholder) != 'undefined' ? profileholder : '0';
    if (document.getElementById('from')) from = document.getElementById('from').value;
    else from = '';
    if (from == '') from = 0;
    if (showloader == '1') {
        q = typeof(q) != 'undefined' ? q : document.getElementById('q').value;
        if (q == '#hashtag or keyword') q = '';
        document.getElementById('hiddentwittertagtosend').value = q;
        if (document.getElementById('uname')) {
            u = document.getElementById('uname').value;
        }
        if (u != '' && u != 'add Twitter username') n = q + ' ' + 'from:' + u;
        else n = q;
        document.getElementById('hiddentwittertag').value = n;
    } else {
        n = document.getElementById('hiddentwittertag').value;
    }
    SearchTwitterHttp = Browser_Check(SearchTwitterHttp);
    if (n != '') {
        document.getElementById('twitter-results').style.display = 'block';
        if (showloader == '1') {
            document.getElementById('twitter-results').innerHTML = '<img src="/images/ajaxloader.gif" align="center">';
        }
        var url = BASE_URL + "/myhome/searchtwitter";
        var data = "q=" + n + "&stoptweetreply=" + stoptweetreply + "&profileholder=" + profileholder + "&tweetnum=" + tweetnum + "&from=" + from;
        SearchTwitterHttp.open("POST", url, true);
        SearchTwitterHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        SearchTwitterHttp.setRequestHeader("Content-length", data.length);
        SearchTwitterHttp.setRequestHeader("Connection", "close");
        SearchTwitterHttp.onreadystatechange = searchtwitterresult;
        SearchTwitterHttp.send(data);
    } else {}
}

function searchtwitterresult() {
    if (SearchTwitterHttp.readyState == 4) {
        if (SearchTwitterHttp.status == 200 || SearchTwitterHttp.status == 0) {
            var result = SearchTwitterHttp.responseText;
            var resultArr = result.split('~#~');
            if (resultArr[0] != '') {
                document.getElementById('twitter-results').innerHTML = resultArr[0];
                document.getElementById('twitter-results-hidden').innerHTML = resultArr[1];
                //				var refreshIntervalId=setInterval("searchtwitter('"+resultArr[2]+"','0')",10000);
            } else if (resultArr[0] == '') {
                document.getElementById('twitter-results').innerHTML = '<font color="#666">No tweet results for ' + resultArr[2] + '</font>';
            }
        } else {};
    }
}

var TweetNoteHttp;

function sendtweetnotification(twittername, db, excerpt) {
    TweetNoteHttp = Browser_Check(TweetNoteHttp);

    var url = BASE_URL + "/myhome/sendtweetnote";
    var data = "name=" + twittername + "&db=" + db + "&excerpt=" + excerpt;

    TweetNoteHttp.open("POST", url, true);
    TweetNoteHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    TweetNoteHttp.setRequestHeader("Content-length", data.length);
    TweetNoteHttp.setRequestHeader("Connection", "close");
    TweetNoteHttp.onreadystatechange = sendtweetnotificationresult;
    TweetNoteHttp.send(data);
}

function sendtweetnotificationresult() {
        if (TweetNoteHttp.readyState == 4) {
            if (TweetNoteHttp.status == 200 || TweetNoteHttp.status == 0) {
                var result = TweetNoteHttp.responseText;
                document.getElementById('hiddentwittername').value = '';
            } else {};
        }
    }
    // FUNCTION TO ADD TWITTER RESULTS TO DB REPLY
var firstTweet = true;

function addtoreply(id, twittername) {
        dbid = $('#dbid').val();  
        //var thisEl = $(thisEl);      
       // $dbLoader(thisEl, 1);

       //$('.addtoreply'+id).removeAttr('onclick');
       $('.addtoreply'+id).addClass('processBtnLoader').append(' <i class="fa fa-spinner fa-spin"></i>');
         
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {
                'screen_name': twittername,
                'dbeeid': dbid
            },
            url: BASE_URL + "/social/checkfollowstatus",
            success: function(response) {
                //$('.addtoreply'+id).removeAttr('onclick');
                $('.addtoreply'+id).removeClass('processBtnLoader');
                $('.addtoreply'+id+' .fa').remove();
                window.scrollTo(500, 0);
               // $('.rightSideListTwitter .fa-spin').remove();
               // $dbLoader(thisEl, 1, 'close');
                if (response.result.relationship.target.following == false && response.result.relationship.target.followed_by == false) {
                    var FollowTemplate = '<div id="socalnetworking-facebooklist"><h2>Following required</h2><div class="twtInfoFltext"> Please click \'Follow\' below to follow this user on Twitter and enable you to add this tweet to this post.</div>\
					<div class="twBntWrp"><a href="javascript:void(0);" tweetid="' + id + '" tweetscreenname="' + twittername + '" class=" btn btn-following"  id="FollowToUser" ><i class="sprite iconTwitterFl"></i> Follow</a></div></div>';

                    setTimeout(function() {
                        $.dbeePopup(FollowTemplate, {
                            bg: '#29B2E4',
                            width: 400,
                            overlayClick: false
                        });
                        //$('#socalnetworking-facebooklist').html('');
                    }, 500);
                    $('#shortUrl').val(response.shortUrl);
                } else {

                    $('#shortUrl').val(response.shortUrl);
                    $('a[data-type="CommentText"]').trigger('click');
                    document.getElementById('twitter-reply-box').style.display = 'block';                    
                    document.getElementById('twitter-db-reply').innerHTML = document.getElementById('twitter-result-' + id).value;
                    document.getElementById('hiddentwitterreply').value = document.getElementById('twitter-result-' + id).value;
                    document.getElementById('hiddentwittername').value = twittername;
                }
            }
        });


        /*$('a[data-type="CommentText"]').trigger('click');
           document.getElementById('twitter-reply-box').style.display = 'block';
           document.getElementById('twitter-db-reply').innerHTML = document.getElementById('twitter-result-' + id).value;
           document.getElementById('hiddentwitterreply').value = document.getElementById('twitter-result-' + id).value;
           document.getElementById('hiddentwittername').value = twittername;*/
    }
    // FUNCTION TO REMOVE TWITTER RESULT FROM DB REPLY

function removetweetfromnewcomment() {
        document.getElementById('twitter-reply-box').style.display = 'none';
        document.getElementById('twitter-db-reply').innerHTML = '';
        document.getElementById('hiddentwitterreply').value = '';
        document.getElementById('hiddentwittername').value = '';
    }
    // FUNCTION TO SEND TWEET USED NOTIFICATION
var TweetReplyHttp;

function stoptweetreply(db) {
    TweetReplyHttp = Browser_Check(TweetReplyHttp);
    var url = BASE_URL + "/myhome/twtsreply";
    var data = "db=" + db;

    TweetReplyHttp.open("POST", url, true);
    TweetReplyHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    TweetReplyHttp.setRequestHeader("Content-length", data.length);
    TweetReplyHttp.setRequestHeader("Connection", "close");
    TweetReplyHttp.onreadystatechange = stoptweetreplyresult;
    TweetReplyHttp.send(data);
}

function stoptweetreplyresult() {
        if (TweetReplyHttp.readyState == 4) {
            if (TweetReplyHttp.status == 200 || TweetReplyHttp.status == 0) {
                var result = TweetReplyHttp.responseText;
            } else {};
        }
    }
    // FUNCTION TO CHANGE NUMBER OF TWEETS SHOWN
var TweetNumHttp;

function changetweetnum(db) {
    TweetNumHttp = Browser_Check(TweetNumHttp);
    var tweetnum = document.getElementById('numtweets').value;
    var url = "ajax_changetweetnum.php";
    var data = "db=" + db + "&tweetnum=" + tweetnum;

    TweetNumHttp.open("POST", url, true);
    TweetNumHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    TweetNumHttp.setRequestHeader("Content-length", data.length);
    TweetNumHttp.setRequestHeader("Connection", "close");
    TweetNumHttp.onreadystatechange = changetweetnumresult;
    TweetNumHttp.send(data);
}

function changetweetnumresult() {
    if (TweetNumHttp.readyState == 4) {
        if (TweetNumHttp.status == 200 || TweetNumHttp.status == 0) {
            var result = TweetNumHttp.responseText;
            if (result != 0) {
                var IntSet = document.getElementById('twitterIntervalSet').value;
                if (IntSet == '1')
                    clearInterval(intReloadTwFeed);
                n = document.getElementById('hiddentwittertag').value;
                searchtwitter('1', '0', result, '1', n);
                intReloadTwFeed = setInterval("searchtwitter('0','0','" + result + "','1','" + n + "')", 7000);
                document.getElementById('twitterIntervalSet').value = '1';
            }
        } else {};
    }
}




function removedbrss() {
    $("#dbRssWrapper").closest('.formRow').fadeOut('fast');
    $("#dbRssTitle").closest('.formRow').html('');
    $("#dbRssDesc").closest('.formRow').html('');
    $("#rssdbadded").closest('.formRow').val('');
    $("#rssadded").val('');

}

function searchtwitterfunction() {
    var tweetnum = document.getElementById('numtweets').value;
    searchtwitter('1', '0', tweetnum, '1');
}

function searchtwitternew(q) {
        new TWTR.Widget({
            version: 2,
            type: 'search',
            search: q,
            interval: 30000,
            title: '',
            subject: '',
            width: 250,
            height: 300,
            theme: {
                shell: {
                    background: '#8ec1da',
                    color: '#ffffff'
                },
                tweets: {
                    background: '#ffffff',
                    color: '#444444',
                    links: '#1985b5'
                }
            },
            features: {
                scrollbar: true,
                loop: true,
                live: true,
                behavior: 'default'
            }
        }).render().start();
    }
    //FUNCTION TO SEE USER LEAGUE POSITION IN POPUP FROM PROFILE
var CommLeagueHttp;

function loadcommentleague(id) {
    return false;
    CommLeagueHttp = Browser_Check(CommLeagueHttp);
    var url = BASE_URL + "/dbleagues/commentleague";
    var data = "dbid=" + id;
    CommLeagueHttp.open("POST", url, true);
    CommLeagueHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    CommLeagueHttp.setRequestHeader("Content-length", data.length);
    CommLeagueHttp.setRequestHeader("Connection", "close");
    CommLeagueHttp.onreadystatechange = loadcommentleagueresult;

    CommLeagueHttp.send(data);
}

function loadcommentleagueresult() {
        if (CommLeagueHttp.readyState == 4) {
            if (CommLeagueHttp.status == 200 || CommLeagueHttp.status == 0) {
                var result = CommLeagueHttp.responseText;

                var res = result.split('~');
                if (res[1] != '' && res[1] != 0) {
                    $('#comment-league-title-wrapper').show();
                    //document.getElementById('comment-league-wrapper').style.display = 'block';
                    $('#singledbleages').show();
                    $('#commentleagueExist').val(1);
                    // document.getElementById('comment-league').innerHTML = res[0];
                    if ($('.ShowMyquestion').is('.active')) {
                        $('#singledbleages').hide(); /*$('#comment-league-wrapper').hide(); */
                    }
                    if ($('.Pendingquestion').is('.active')) {
                        $('#singledbleages').hide(); /*$('#comment-league-wrapper').hide(); */
                    }
                    if ($('.Myquestion').is('.active')) {
                        $('#singledbleages').hide(); /*$('#comment-league-wrapper').hide();*/
                    }
                }
            } else {};
        }
    }
    //FUNCTION TO SEARCH GOOGLE FOR LINK DBEE
var SearchGoogleHttp;

function searchgooglelinkdb(si) {

    si = typeof(si) != 'undefined' ? si : '1';
    var err = false;
    SearchGoogleHttp = Browser_Check(SearchGoogleHttp);
    if (document.getElementById('searchterm').value == "search Google") {
        err = true;
        document.getElementById('searchterm').focus();
    }
    if (!err) {
        var searchterm = document.getElementById('searchterm').value;
        $('#attchedLinked').show();
        document.getElementById('attachlinkloader').innerHTML = '<div style="margin-left:200px"><img src="images/ajaxloader.gif"></div>';
        //		grayOut('googleresults',true);
        var url = BASE_URL + "/customsearch/index";
        var data = "searchterm=" + searchterm + "&startindex=" + si;
        SearchGoogleHttp.open("POST", url, true);
        SearchGoogleHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        SearchGoogleHttp.setRequestHeader("Content-length", data.length);
        SearchGoogleHttp.setRequestHeader("Connection", "close");

        SearchGoogleHttp.onreadystatechange = searchgooglelinkdbresult;

        SearchGoogleHttp.send(data);
    }
}

function searchgooglelinkdbresult() {
        if (SearchGoogleHttp.readyState == 4) {
            if (SearchGoogleHttp.status == 200 || SearchGoogleHttp.status == 0) {
                //			grayOut('googleresults',false);
                var result = SearchGoogleHttp.responseText;
                var resultArr = result.split('~#~');
                $('#attchedLinked').show();
                document.getElementById('LinkDesc1').innerHTML = resultArr[0];
                setTimeout(function() {
                    $.dbeePopup('resize');
                }, 100);
            } else {};
        }
    }
    //FUNCTION TO START DB FROM LINK

function startdbfromlink(id) {
    //goToByScroll('Logo');
    $("#PostLink").val(id);
    attachlinkdbee();
}

function goToByScroll(id) {
    $('html,body').animate({
        scrollTop: $("#" + id).offset().top
    }, 'slow');
}

function reloadmydbee(currval) 
{   
    $('#notifications-top-wrapper-dbee').show();
    $("#notifications-top-dbee").html(parseInt(currval));
    $('#notifications-top-wrapper-dbee').css('cursor', 'pointer');
}
    //FUNCTION TO CHANGE NUMBER OF TWEETS SHOWN
var TweetdataHttp;

function changetweetdata(db, keyword, from) {
    from = typeof(from) != 'undefined' ? from : '0';

    TweetdataHttp = Browser_Check(TweetdataHttp);

    document.getElementById('reftwiterright').style.display = 'none';
    document.getElementById('reftwiterright-loader').style.display = 'block';
    var url = BASE_URL + "/edf/index";
    var data = "db=" + db + "&keyword=" + keyword + "&update=1";
    TweetdataHttp.open("POST", url, true);
    TweetdataHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    TweetdataHttp.setRequestHeader("Content-length", data.length);
    TweetdataHttp.setRequestHeader("Connection", "close");

    TweetdataHttp.onreadystatechange = function() {
        changetweetdataresult(from);
    };
    TweetdataHttp.send(data);
}

function changetweetdataresult(from) {
    if (TweetdataHttp.readyState == 4) {
        if (TweetdataHttp.status == 200 || TweetdataHttp.status == 0) {
            var result = TweetdataHttp.responseText;
            var resultArr = result.split('~#~');
            Twiterright(resultArr[1]);
            timerdbee();
        } else {};
    }
}

function Twiterright(db) {
    $.ajax({
        type: "POST",
        dataType: 'json',
        data: {
            dbeeid: db
        },
        url: BASE_URL + '/ajax/fetchtwitdataright',
        cache: false,
        success: function(response) {
            if (response.status == 'success') {
                if (response.html.twittertag != '') {
                    $('.rightSideListTwitter').html(response.html.twittertag);
                    $('#reftwiterright-loader').remove();
                    $('#reftwiterright i').removeClass('fa-spin');
                    $('.twTagsTab li:first').trigger('click');
                }
            }
        }
    });
}

function stopshowAttendies(db) {
    $.ajax({
        type: "POST",
        data: {
            db: db
        },
        url: BASE_URL + '/myhome/attendiesreply',
        cache: false,
        success: function(response) {}
    });
}


function changetweetdataRefresh(db, keyword, from) {
    from = typeof(from) != 'undefined' ? from : '0';

    TweetdataHttp = Browser_Check(TweetdataHttp);
    if ($('#reftwiterright i').hasClass('fa-spin') == true) {
        return false;
    }
    $('#reftwiterright i').addClass('fa-spin');
    // document.getElementById('reftwiterright-loader').style.display = 'block';
    var loader = '<div id="reftwiterright-loader" style="display:block"><img src="' + BASE_URL + '/img/25.gif"  style="position:absolute; top:50%; left:50%; margin-left-15px; margin-top-15px;"></div>';
    $('#alltweet').append(loader);
    var url = BASE_URL + "/edf/index";
    var data = "db=" + db + "&keyword=" + keyword + "&update=1";
    TweetdataHttp.open("POST", url, true);
    TweetdataHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    TweetdataHttp.setRequestHeader("Content-length", data.length);
    TweetdataHttp.setRequestHeader("Connection", "close");

    TweetdataHttp.onreadystatechange = function() {
        changetweetdataresultRefresh(from,db);
    };
    TweetdataHttp.send(data);

}

function changetweetdataresultRefresh(from,db) {
    if (TweetdataHttp.readyState == 4) {
        if (TweetdataHttp.status == 200 || TweetdataHttp.status == 0) {
            var result = TweetdataHttp.responseText;
            var resultArr = result.split('~#~');
            if(resultArr[0]==5){
                $dbConfirm({
                content: 'Oops, you have exceeded the maximum allowed limit. Please try again in approximately '+resultArr[0]+' minutes',
                yes: false
                });
                $('.disabledRefreshIcon').hide();
                $('#reftwiterright').show();
            }else
            {
                Twiterright(resultArr[1]);
                timerdbee();
                socket.emit('refreshtweet',db);
            }
        }
    }
}

var count = 30;

function timerdbee() {
    count = count - 1;
    if (count <= 0) {
        count = 30;
        document.getElementById('timer-msg').innerHTML = '';
        $('.disabledRefreshIcon').hide();
        $('#reftwiterright').show();
        return;
    } else {
        setTimeout('timerdbee()', 1000);
    }
    //Do code for showing the number of seconds here
}

function refreshtweet(db) 
{
    dbid = $('#dbid').val();
    if(db==dbid)
    { 
        $.ajax({
            type: "POST",
            data: {
                db: db
            },
            url: BASE_URL + '/ajax/gettweet',
            success: function(response) {
                $('.rightSideListTwitter').html(response.html);
                $('#twiterKeyContainer .twKeyDetails').show();
            }
        });
    }
}

function linkedinUserDirect2() {
    $.dbeePopup('close');

    var linkedinTemplate = '<h2>Invite LinkedIn connections</h2><div id="socalnetworking-linkedinlist" class="clearfix"><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>';
    setTimeout(function() {
        $.dbeePopup(linkedinTemplate, {
            overlay: true,
            otherBtn: '<a href="javascript:void(0);" class="pull-right btn btn-yellow"  id="SendMessageLinkedin">Invite</a>'
        });

        $.ajax({
            type: "POST",
            dataType: 'json',
            url: BASE_URL + '/social/linkedinuser',

            beforeSend: function() {

            },
            complete: function() {

            },
            cache: false,
            success: function(response) {
                $("#socalnetworking-linkedinlist").html(response.content);

                setTimeout(function() {
                    $.dbeePopup('resize');
                }, 300);
            },
            error: function(request, status, error) {}
        });

    }, 500);
}

function linkedinUserDirect() {
    $.ajax({
        type: "POST",
        dataType: 'json',
        url: BASE_URL + '/social/linkedinuser',

        beforeSend: function() {

        },
        complete: function() {

        },

        cache: false,

        success: function(response) {

            var linkedinTemplate = '<h2>Invite LinkedIn connections</h2><div id="socalnetworking-linkedinlist" class="clearfix"><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>';

            $.dbeePopup(linkedinTemplate, {
                overlay: true,
                otherBtn: '<a href="javascript:void(0);" class="pull-right btn btn-yellow"  id="SendMessageLinkedin">Invite</a>'
            });


            $("#socalnetworking-linkedinlist").html(response.content);

            setTimeout(function() {
                $.dbeePopup('resize');
            }, 300);

        },

        error: function(request, status, error) {}

    });
}

function TwittershareOnWall(thisEl) {
    var dbid = $('#dbid').val();
    var  thisEl = $(thisEl);
    $.ajax({
        type: "POST",
        data: {
            'dbeeid': dbid
        },
        dataType: 'json',
        url: BASE_URL + "/social/shareontwitterwall",
        beforeSend:function (){
            $dbLoader(thisEl,'center');
        },
        success: function(response) {
            $dbConfirm({
                content: response.message,
                yes: false,
                no:false
            });
            $('.closePostPop').trigger('click');
        }
    });
}

function Twittersphereshare() {
    var dbid = $('#dbid').val();
    var username = $('#twitteruserkeyword').val();
    var sharecontent = $('#preview_twitterSphereHtml').text();
   

    $.ajax({
        type: "POST",
        data: {
            'dbeeid': dbid,
            'username': username,
            'sharecontent': sharecontent
        },
        dataType: 'json',
        url: BASE_URL + "/social/twittersphereshare",
        success: function(response) {
            /*$dbConfirm({
                content: response.message,
                yes: false
            });*/
            $('.closePostPop').trigger('click');
        }
    });
}

function LinkedinshareOnWall(thisEl) {
    var dbid = $('#dbid').val();
    var thisEl = $(thisEl);
    $.ajax({
        type: "POST",
        data: {
            'dbeeid': dbid
        },
        dataType: 'json',
        url: BASE_URL + "/social/shareonlinkedinwall",
        beforeSend:function(){
            $dbLoader(thisEl,'center');
        },
        success: function(response) {
            $dbConfirm({
                content: response.message,
                yes: false
            });
            $('.closePostPop').trigger('click');
        }
    });
}

function popupTwitterShare() {
    $.dbeePopup('close');
    setTimeout(function() {
        var favTemplate = '<div style="padding:20px;">\
        <div class="formRow" id="content_data" >Do you want to share this post on twitter?</div>\
        <div class="clearfix"></div>\
        </div>';
        $.dbeePopup(favTemplate, {
            width: 300,
            otherBtn: '<a href="javascript:void(0);" class="btn btn-yellow pull-right" onclick="javascript:TwittershareOnWall()">Yes</a>\
        '
        });
    }, 1000);
}

function popupLinkedinShare() {
    $.dbeePopup('close');
    setTimeout(function() {
        var favTemplate = '<div style="padding:20px;">\
        <div class="formRow" id="content_data" >Do you want to share this post on linkedin?</div>\
        <div class="clearfix"></div>\
        </div>';
        $.dbeePopup(favTemplate, {
            width: 350,
            otherBtn: '<a href="javascript:void(0);" class="btn btn-yellow pull-right" onclick="javascript:LinkedinshareOnWall()">Yes</a>\
        '
        });
    }, 1000);
}

function scoreProfile(userId, containerElement) {
    $.ajax({
        type: "POST",
        dataType: 'json',
        url: BASE_URL + "/profile/league",
        data: {
            "userID": userId
        },
        beforeSend: function() {
            $dbLoader(containerElement, 1);
        },
        success: function(data) {
            $dbLoader(containerElement, 1, 'close');
            $(containerElement).html(data.content);
        }
    });
}


///////////////////// message update function //////////////
$(function() {

   $('body').on('click', '#archivemessage', function(e) {
        e.stopPropagation();
        e.preventDefault();

        var user = $(this).attr('usr');
        var rowid = $(this).attr('rowid');

        var sra = $(this);
        //var content = 'Do you want to remove this message?';
        //var makeTemplate = '<div id="content_data" style="color:#999999;padding:10px 0px 10px 0px; height:50px; text-align:center; font-size:18px;">'+content+'</div>';
        //$.dbeePopup(makeTemplate, {width:400,closeLabel:'No', otherBtn:'<a href="javascript:void(0);" class="pull-right btn btn-yellow removemessage" >Yes</a>'});
        $dbConfirm({
            content: 'Do you want to remove this message?',
            yesClick: function() {
                /*$('.removemessage').click(function()
					{*/
                $('.removemessage').append(' <i class="fa fa-spinner fa-spin"> </i>').css('cursor', 'default');
                var user = $(sra).closest('.msgblockndelete');
                var user1 = user.attr('chkuser');
                var uid = user.attr('id');
                var message = uid.split('-');
                var status = '1';
                var rowid = user.attr('rowid');
                $.ajax({
                    url: BASE_URL + "/message/archivemessage",
                    type: "POST",
                    data: {
                        'user': user1,
                        'message': message['1'],
                        'status': '1',
                        'rowid': rowid
                    },
                    success: function(data) {
                         var len = $('#dbee-feeds').find('.newmsgrow').length;                        
                        storeNotificationsmsg='';
                        $('[id="message-'+data+'"]').remove();
                        $('#actNotificationMsg #message-'+data+'').remove();
                       //alert(len);
                        if(len==0){
                            $('#leftListing').html('<ul id="dbee-feeds" class="postListing"><li><div class="noFound">You currently have no new messages</div></li></ul>');
                        }                        
                        $('.closePostPop').trigger('click');
                    }
                });
                //});
            }
        });

    });

 $('body').on('click', '#dbee-feeds li .blockmessage', function(e) {
        
        e.stopPropagation();
        e.preventDefault();
        var user = $(this).attr('usr');
        var rowid = $(this).attr('rowid');
        var name = $(this).attr('name');
        var sra = $(this);
        $dbConfirm({
            content: 'You are about to block <b>' + name + '</b> from messaging you. This action cannot be undone. ',
            yesClick: function() {
                var user = $(sra).closest('.msgblockndelete');
                var user1 = user.attr('chkuser')
                var uid = user.attr('id');
                var message = uid.split('-');
                $.ajax({
                    url: BASE_URL + "/message/blockuserfrommsg",
                    type: "POST",
                    data: {
                        'user': user1,
                        'id': message['1']
                    },
                    success: function(data) {
                        var lns = $('#blockuser-div-' + rowid+ ' div');
                        lns.css('color', '#CC0000'); 
                        lns.removeClass('blockmessage');

                        $dbConfirm({
                            content: 'successfully block user!',
                            yes: false
                        });
                        $('.closePostPop').trigger('click');
                    }
                });

                $('.closePostPop').trigger('click');
               
            }
        });

    });
    $('body').on('click', '.newmsgrow', function(e) {
      
        var uid = $(this).attr('id');
        var dateFrom = $(this).attr('from');
        var dateTo = $(this).attr('to');
        var fromadmin = $(this).attr('fromadmin');
        var search = $(this).attr('search');
        var adurlextra = '';
        
       /* if (dateFrom != '') {
            var adurlextra = '/from/' + dateFrom + '/to/' + dateTo + '/search/' + search
        }*/
        var mid = uid.split('-');
        var ChkUser = $(this).attr('ChkUser');
        var read = $(this).attr('read');
        var thisEl = $(this);
        var myjson = {'uid':ChkUser, 'read':read, 'mid':mid[1], 'datefrom':dateFrom, 'dateto':dateTo, 'search':search, 'fromadmin':fromadmin}
        messagefeed(myjson);
        if(fromadmin!=1){
        $('html, body').animate({
            scrollTop: $("body").offset().top
         }, 500);
        }
       // loadselect();
    });
    //


    $('body').on('keyup', '#dbee_comment', function(e) {
        if (e.keyCode == 13 && e.shiftKey != true) {
            if ($('.mentions-autocomplete-list').is(':visible') == false) {
                postcomment();
            }
        }
    });

    $('body').on('click', '#postCommentBtn a[data-click="true"]', function(e) {
        postcomment();
    });

    $('body').on('click', '#messagelist', function(e) {
          e.stopPropagation();
          e.preventDefault();    
          $('#dbees-feeds-wrapper').remove();
          $('#leftListing').html('<ul id="dbee-feeds" class="postListing"></ul>');
          newmessagefeed('0');
          $('#issearch').val('0');
    });
});



function influence(UserId, ParrentId, ParrentType, ArticleId, ArticleType, CommentId, thisE) {

    var UserId = typeof(UserId) != 'undefined' ? UserId : '0';
    var ParrentId = typeof(ParrentId) != 'undefined' ? ParrentId : '0';
    var ParrentType = typeof(ParrentType) != 'undefined' ? ParrentType : '0';
    var ArticleId = typeof(ArticleId) != 'undefined' ? ArticleId : '0';
    var ArticleType = typeof(ArticleType) != 'undefined' ? ArticleType : '0';
    var CommentId = typeof(CommentId) != 'undefined' ? CommentId : '0';
    var thisEl = $(thisE);

    $.ajax({
        type: "POST",
        dataType: 'json',
        data: {
            'UserId': UserId,
            'ParrentId': ParrentId,
            'ParrentType': ParrentType,
            'ArticleId': ArticleId,
            'ArticleType': ArticleType,
            'CommentId': CommentId
        },
        url: BASE_URL + '/dbeedetail/influence',
        cache: false,
        beforeSend: function() {
            $('.fa', thisEl).removeClass('fa-spinner fa-spin').addClass('fa-lightbulb-o');
            $('.fa', thisEl).removeClass('fa-lightbulb-o').addClass('fa-spinner fa-spin');
        },
        success: function(response) {

            $('.fa', thisEl).removeClass('fa-spinner fa-spin').addClass('fa-lightbulb-o');
            if (response.success == 'insert') {
                //$('i', thisEl).css('color', '#faa80b');
                $('i', thisEl).prop('title', 'Remove from my Influence list');
                thisEl.closest('.dbRelbox').addClass('active');
                thisEl.closest('.comment-list').addClass('active');
                thisEl.closest('.dbRelbox').find(".inflSpan").show();
                thisEl.closest('.comment-list').find(".comntInflTxt").show();

                thisEl.closest('.postListing li.listingTypeMedia').addClass('active');


                setTimeout(function() {
                    $(thisEl.closest('.dbRelbox').find(".inflSpan")).fadeOut('fast');
                    thisEl.closest('.comment-list').find(".comntInflTxt").fadeOut('fast');


                }, 3000);
                if(localTick == false){
                    callsocket();
                }
            } else {
               // $('i', thisEl).css('color', '#919191');
                $('i', thisEl).prop('title', 'Add to my Influence list');
                thisEl.closest('.dbRelbox').removeClass('active');
                thisEl.closest('.dbRelbox').find(".inflSpan").hide();

                thisEl.closest('.comment-list').removeClass('active');
                thisEl.closest('.comment-list').find(".comntInflTxt").hide();

                thisEl.closest('.postListing li.listingTypeMedia').removeClass('active');
            }

        }


    });
}

//$('body').on('click','#coockieaccept',function(){		

function acceptCookie(userid) {

    //var user = $(this).attr('user');	
    var user = userid;
    $.ajax({
        type: "POST",
        dataType: 'json',
        data: {
            'user': user
        },
        url: BASE_URL + '/update/coockieupdate',
        cache: false,
        beforeSend: function() {
            $('#coockieaccept i').remove();
            //$('#coockieaccept').append(' <i class="fa fa-spin fa-spinner"></i>');
        },
        success: function(response) {
            $('#coockieaccept i').remove();
            $('.infoMessageFromBottom').removeClass('slideUpAnim').addClass('slideDownAnim');
            //location.reload();
            /*$('#dbeePopupWrapper').css({top:-500}).removeClass('bounceOpen');
					setTimeout(function(){
						$('.dbeePopupOverlay').fadeOut(function(){
							$('#dbeePopupWrapper', '.dbeePopupOverlay').remove();	
							$(this).remove();
							location.reload();
						});
					}, 500);*/

        }

    });

}
function acceptNewFeatures(userid) {

    //var user = $(this).attr('user');  
    var user = userid;
    $.ajax({
        type: "POST",
        dataType: 'html',
        data: {'user': user,'clearcache':1 },
        url: BASE_URL + '/myhome/logout',
        beforeSend: function() {

        },
        success: function(response) {
            window.location.reload();
        }

    });
}

function  fbSocialFeed(response, buttons, titleFeedTop, heightFeedSide, profilePic, windH, vTp){
    

                    
                    if (response.status == 'error') 
                    {
                        $('#sideBarFeeds').hide();
                        $('.socialfeedWidget a').removeClass('disabled');
                        $('.socialfeedWidget a').removeClass('active');
                        if(faceBookFriend == true)
                        {
                            $.dbeePopup('close');
                            setTimeout(function() {
                                var reportTemplate = 'There seems to be a problem fetching Facebook feed for this user. Please check again later.';
                                $.dbeePopup(reportTemplate, {
                                    width: 400,
                                    overlayClick: false,
                                    otherBtn: ''
                                });
                            $('html').removeClass('activeSocialfeedSidebar');
                             });
                        }else{
                            facebookLogin();
                        }
                    }
                    if (faceBookFriend == true) {
                        var urlpic = $('#profileimage').attr("datapic-url");
                        profilePic = '<img src="' + urlpic + '"  class="twProPicTop"/>';
                    }

                    $('#sideBarFeeds').removeClass('loader');
                    $('.socialFeedLoader').remove();
                    $('.socialfeedWidget a').removeClass('disabled');


                    if (typeof(response.friendStatus) != 'undefined') {
                        if (response.friendStatus.data == '') {
                            titleFeedTop = '<a href="javascript:void(0);" onclick="sendrequest(' + response.facebook_id + ');" class="btn btn-mini addFriendBtn ">Add Friend</a>';
                        } else {
                            titleFeedTop = '<i class="btn btn-mini addFriendBtn " style="cursor:default">&#x2713; Friends</i>';
                        }
                    }

                    var htmlSideBar = '<div id="sideBarFeedsFb" style="height:' + windH + 'px" class="sideFeedWrp"><h2 class="feddmtitle"><span><i class="fa fa-share-alt fa-lg"></i></span>' + profilePic + titleFeedTop + buttons + '</h2><div class="feedWrpSideContent"><ul></ul></div></div>';


                    $('#sideBarFeedsFb').remove();

                    $('#sideBarFeeds').append(htmlSideBar);
                    var htmlList='';
                    $.each(response.content.data, function(i, el) {
                        //      var dataTime = response.content[i].created_at.split('+');

                        var data = response.content.data[i];
                        if(typeof(response.page_access_token)=='undefined'){
                            page_access_token = response.access_token;
                        }else{
                             page_access_token = response.page_access_token;
                        }
                       
                         var type = '';
                            var userName = '';
                            var userPic = '';
                            var picture = '';
                            var link = '';
                            var source = '';

                            var created_time = '';
                            var message = '';
                            var story = '';

                            var name = '';
                            var caption = '';
                            var description = '';
                            var application = '';                      
                            var id = '';
                            var statusType = '';
                            var fbDataTime = '';
                            var userPicImg = '';
                        if( clientID!=53)
                        {
                             type = data.type;
                             userName = data.from.name;
                             userPic = '//graph.facebook.com/' + data.from.id + '/picture?width=32&height=32';
                             picture = data.picture;
                             link = data.link;
                             source = data.source;

                             created_time = data.created_time;
                             message = $.trim(data.message);
                             story = data.story;

                             name = data.name;
                             caption = data.caption;
                             description = data.description;
                             application = data.application;                            
                            
                             statusType = data.status_type;
                             fbDataTime = created_time.split('+');
                             id = data.id;
                        }else
                        {
                            userPic = '//graph.facebook.com/' + data.id.split('_')[0] + '/picture?width=32&height=32'; 
                            created_time = data.created_time;
                            message = $.trim(data.message);
                            story = data.story;
                            if(typeof(story)=='undefined'){
                               type = 'status' ;
                            }
                            if(typeof(response.facebook_name)=='undefined'){
                                userName = response.facebookpagename;
                            }else{
                                userName = response.facebook_name;
                            }

                            id = data.id;
                        }

                        if(userPic!=''){
                            userPicImg = '<img src="' + userPic + '"/>';
                        }

                        if (typeof(message) != 'undefined') {
                            var messageCnt = '<p class="fbDes">' + message + '</p>';
                        }

                        if (typeof(story) != 'undefined') {
                            var filterStrory = story.replace(userName, '<strong>' + userName + '</strong>');
                             htmlList += '<li class="fbList" data-id="' + id + '" data-index="' + i + '">\
                                        '+userPicImg+'\
                                        <div class="sideTwFdContent">\
                                            <div class="fbsdCnt">\
                                                <p class="fbDes">' + filterStrory + '</p>\
                                            </div>\
                                        </div>\
                                    </li>';

                        } else {
                            if (type == 'photo') {
                                if (statusType == 'added_photos') {
                                    var createStory = '<strong>' + userName + '</strong> added a new photo'
                                     htmlList += '<li class="fbList" data-id="' + id + '" data-index="' + i + '">\
                                        '+userPicImg+'\
                                        <div class="sideTwFdContent">\
                                            <div class="fbsdCnt">\
                                                <p class="fbDes">' + createStory + '</p>\
                                            </div>\
                                        </div>\
                                    </li>';

                                } else if (statusType == 'shared_story') {
                                    if(typeof(data.properties)=='undefined') var ptxt = '';
                                        else   var ptxt = data.properties.text;
                                    var createStory = '<strong>' + userName + '</strong> shared <strong>' + ptxt + '\'s </strong> photo';
                                     htmlList += '<li class="fbList" data-id="' + id + '" data-index="' + i + '">\
                                        '+userPicImg+'\
                                        <div class="sideTwFdContent">\
                                            <div class="fbsdCnt">\
                                                <p class="fbDes">' + createStory + '</p>\
                                            </div>\
                                        </div>\
                                    </li>';
                                }
                            } else if (type == 'status') {
                                if (statusType == 'wall_post') {

                                    var createStory = '<strong>' + userName + '</strong> posted on <strong>' + data.to.data[0].name + '\'s </strong> wall';

                                     htmlList += '<li class="fbList" data-id="' + id + '" data-index="' + i + '">\
                                        '+userPicImg+'\
                                        <div class="sideTwFdContent">\
                                            <div class="fbsdCnt">\
                                                <p class="fbDes">' + createStory + '</p>\
                                            </div>\
                                        </div>\
                                    </li>';

                                } else if (statusType == 'mobile_status_update') {
                                    var createStory = '<strong>' + userName + '</strong> ' + message.substring(0, 50);
                                     htmlList += '<li class="fbList" data-id="' + id + '" data-index="' + i + '">\
                                        '+userPicImg+'\
                                        <div class="sideTwFdContent">\
                                            <div class="fbsdCnt">\
                                                <p class="fbDes">' + createStory + '</p>\
                                            </div>\
                                        </div>\
                                    </li>';

                                }
                                else  {
                                    var createStory = '<strong>' + userName + '</strong> ' + message.substring(0, 50);
                                     htmlList += '<li class="fbList" data-id="' + id + '" data-index="' + i + '">\
                                        '+userPicImg+'\
                                        <div class="sideTwFdContent">\
                                            <div class="fbsdCnt">\
                                                <p class="fbDes">' + createStory + '</p>\
                                            </div>\
                                        </div>\
                                    </li>';

                                }


                            } else if (type == 'link') {
                                if (statusType == 'shared_story') {
                                    var createStory = '<strong>' + userName + '</strong> shared a link';
                                     htmlList += '<li class="fbList" data-id="' + id + '" data-index="' + i + '">\
                                        '+userPicImg+'\
                                        <div class="sideTwFdContent">\
                                            <div class="fbsdCnt">\
                                                <p class="fbDes">' + createStory + '</p>\
                                            </div>\
                                        </div>\
                                    </li>';
                                }
                                else if (statusType == 'published_story') {
                                    var createStory = '<strong>' + userName + '</strong> shared a link';
                                     htmlList += '<li class="fbList" data-id="' + id + '" data-index="' + i + '">\
                                        '+userPicImg+'\
                                        <div class="sideTwFdContent">\
                                            <div class="fbsdCnt">\
                                                <p class="fbDes">' + createStory + '</p>\
                                            </div>\
                                        </div>\
                                    </li>';
                                }

                            }
                        }

                       

                    });
                     $('#sideBarFeedsFb ul').html(htmlList);
                    $('.sideFeedWrp .feedWrpSideContent').perfectScrollbar({
                        includePadding: true
                    });
                    $dbTip();




                    $('#sideBarFeedsFb li').click(function(event) {
                        event.preventDefault();
                        $('#sideBarFeeds').addClass('fakePositon');
                        var offset = $(this).position();
                        $('#sideBarFeeds').removeClass('fakePositon');
                        var feedId = $(this).attr('data-id');
                        var i = $(this).attr('data-index');
                      
                        //var userContent = userContents;
                        var msgCont = '';
                        var desCont = '';
                        var type = '';
                        var statusType = '';
                          var sideBarWidth = $('#sideBarFeeds').width();
                        var thisList = $(this);
                        var thisHeight = thisList.height();
                        $('.fbOpenDetailsLoader').remove();
                        $('img', this).hide().after('<i class="fa fa-spinner fa-spin fa-2x pull-left fbOpenDetailsLoader"></i>');
                       
                      $.getJSON('https://graph.facebook.com/v2.7/' + feedId + '?access_token='+page_access_token+'&fields=link,message,created_time,comments{created_time,from,message},description,caption,from,id,application,type,status_type,full_picture,picture,source', function(fbResult) {
                          var userContent = fbResult;
                            var  type = userContent.type;
                            var  statusType = userContent.status_type;
                            var   msg = userContent.message;
                          
                            var  description = userContent.description;
                            var  caption = userContent.caption;
                            var link = userContent.link;

                            var picture = userContent.picture;
                            var  name = userContent.name;
                            var fromName = userContent.from.name;
                            var fromID = userContent.from.id;
                            var createdDate = userContent.created_time;

                            var comments = userContent.comments;
                            var application = userContent.application;

                            var mainUserId = response.facebook_id;
                            var  mainUserName = response.facebook_name;
                            var mainUserPic = '//graph.facebook.com/' + mainUserId + '/picture?width=32&height=32';
                            

                          

                              console.log(JSON.stringify(fbResult));
                                    
                            

                           
                             
                       
                    
                        var userPostLike = false;
                        
                      
                        if (typeof(picture) == 'undefined' ) {
                            picture = '';
                        } else {                  
                            picture = picture.replace('_s.', '_n.');
                            picture = picture.replace('_t.', '_n.');                           
                            picture = '<img src="' + picture + '"  class="fbBigPicture"/>';
                        }

                        if (typeof(msg) != 'undefined') {
                            var msgCont = '<div class="fbSdmsgTxt">' + msg + '</div>';
                        }else{
                            msgCont = '';
                        }

                        if (typeof(description) != 'undefined' ) {
                            var desCont = '<div class="fbSdmsgTxt fbSdDesTxt">' + description + '</div>';
                        }else{
                            desCont = '';
                        }

                        if (type == 'photo' || type == 'status' || type == 'link' || type == 'video') {
                            //if(statusType=='added_photos'){
                             sideHeaderUserName = fromName;
                            var userID = fromID;

                            //}
                        }
                        var linkWrp = '';
                        if (type == 'link') {
                            if (typeof(application) != 'undefined') {
                                if (typeof(caption) != 'undefined') {
                                    caption = '<i>' + caption + '</i>';
                                } else {
                                    caption = '';
                                }
                                if (application.name != 'Pages') {
                                    var linkWrp = '<a href="' + link + '" class="fbSdLinkWrp" target="_blank">\
                                        <div class="fbSdLinkpic">' + picture + '</div>\
                                        <div class="fbSdLinkCnt">\
                                            <h2>' + application.category + caption + '</h2>\
                                            <p>' + description + '</p>\
                                        </div>\
                                    </a>';
                                } else {
                                    var pageName = '';
                                    $.each(userContent.story_tags, function(index, val) {
                                        pageName = val[0].name;
                                    });

                                    var linkWrp = '<a href="' + link + '" class="fbSdLinkWrp" target="_blank">\
                                        <div class="fbSdLinkpic">' + picture + '</div>\
                                        <div class="fbSdLinkCnt">\
                                            <h2>' + pageName + '<i>' + link + '</i></h2>\
                                        </div>\
                                    </a>';
                                }
                            }
                            picture = linkWrp;


                        } else if (type == 'video') {
                            picture = '<a href="'+userContent.source+'" class="fbpostvideo"><img src="'+userContent.picture+'" /></a><div class="fbVideoPlayer"></div>';
                        }




                        var sideHeaderHTML = '<div id="fbSideHeader"><i class="fa fa-times fbCloseDetailIcon"></i>\
                                            <img src="//graph.facebook.com/' + userID + '/picture?width=40&height=40" class="fbSdImgUsr">\
                                            <div class="fbSideHeader">\
                                                <h2>' + sideHeaderUserName + ' <br>  <span  class="timeago" title="' + createdDate + '">' + createdDate + '</span></h2>\
                                            </div>\
                                        </div>';


                        var likes = 'Like';
                        if (userPostLike == true) {
                            likes == 'Unike';
                        } else {
                            if (typeof(userContent.likes) != 'undefined') {
                                $.each(userContent.likes.data, function(index, val) {
                                    if (mainUserId == val.id) {
                                        likes = 'Unike';

                                    }
                                });
                            }
                        }

                        var likeHTML = '<div class="likeCmntWrp"><a href="javascript:void(0)" class="likeOnPost" data-type="' + likes + '">' + likes + '</a> - <a href="javascript:void(0)" class="cmntOnPost">Comment</a></div>';
                        var commentHTML = '<div id="fbsideComment">\
                                            <img src="' + mainUserPic + '" class="fbSideCmntUsr">\
                                            <div class="fbCmntWrp"><input type="text" placeholder="write a comment..."/></div>\
                                        </div>';

                        if (iamConnectWithFacebook == false) {
                            likeHTML = '';
                            commentHTML = '';
                        }
                        var commentListAppend = '';
                        var commentListHTML = '';
                        if (typeof(comments) != 'undefined' && comments!='') {
                            $.each(comments.data, function(i, val) {
                                var cmntlikes = 'Like';

                                //if(mainUserId==val.from.id){
                                if (val.user_likes == true) {
                                    cmntlikes = 'Unlike';
                                }
                                //}
                                var liksHt = '- <a href="javascript:void(0)" class="cmntLike" data-id="" data-type="' + cmntlikes + '">' + cmntlikes + '</a>';
                                if (iamConnectWithFacebook == false) {
                                    liksHt = '';
                                }
                                commentListAppend += '<li data-id="' + val.id + '">\
                                                <img src="//graph.facebook.com/' + val.from.id + '/picture?width=32&height=32" class="fbCmntImgUsr">\
                                                <div class="fbCmntCont">\
                                                    <div class="cmntBX"><strong>' + val.from.name + '</strong> ' + val.message + '</div>\
                                                    <div class="cmntLikeDate">\
                                                        <i class="timeago" title="' + val.created_time + '">' + val.created_time + '</i>' + liksHt + '\
                                                    </div>\
                                                </div>\
                                            </li>';
                            });

                            commentListHTML = commentListAppend;

                        }

                        var htmlShow = '<div class="fbClickDescription" data-feed="'+feedId+'" data-list="click"  style=" top:' + (offset.top + vTp) + 'px; right:' + (sideBarWidth + 20) + 'px">\
                                        <div class="fbClickCnt">\
                                        <div class="fbClickCntWrp">' + sideHeaderHTML + '\
                                        ' + msgCont + '\
                                        ' + picture + '\
                                        ' + desCont + '\
                                        ' + likeHTML + '</div>\
                                            <div class="CommentWrp">' + commentListHTML + '</div>\
                                        </div>\
                                        ' + commentHTML + '\
                                    </div><div class="sideBarArrowFeed sprite" ></div>';


                        $('.fbClickDescription, .sideBarArrowFeed').remove();
                        $('body').append(htmlShow);
                        //  console.log(msg)
                         // console.log(msg)
                          //console.log(picture)
                      
                        var resizeSideDetails = function() {
                            var heightBx = $('.fbClickDescription').height();
                            var totalOffHeight = offset.top + heightBx;
                            $('.sideBarArrowFeed').css({
                                top: (offset.top + vTp + thisHeight / 2),
                                right: sideBarWidth + 6
                            });
                            if (windH <= totalOffHeight + vTp) {
                                $('.fbClickDescription').css({
                                    top: 'auto',
                                    bottom: 0,
                                    position: 'fixed'
                                });
                            }
                            $('.fbOpenDetailsLoader').fadeOut(function() {
                                $(this).remove();
                                $('img', thisList).fadeIn();
                            });

                            $('.fbClickCnt').perfectScrollbar({
                                includePadding: true
                            });
                        }


                        if ($('.fbClickDescription img').hasClass('fbBigPicture') == true) {
                            $('.fbClickDescription .fbBigPicture').load(function() {
                                $('.fbClickDescription, .sideBarArrowFeed').fadeIn();
                                resizeSideDetails();

                            });
                        } else {
                            $('.fbClickDescription, .sideBarArrowFeed').fadeIn();
                            resizeSideDetails();

                        }


                        $(".timeago").timeago();


                        $('.fbpostvideo').click(function(event) {
                            event.preventDefault();
                            $(this).hide();
                            $(this).next('.fbVideoPlayer').html('<iframe src="' + $(this).attr('href') + '" allowscriptaccess=”always” allowfullscreen=”true”></iframe>');

                            resizeSideDetails();

                        });

                        $('.cmntOnPost').click(function(event) {
                            event.preventDefault();
                            $('.fbCmntWrp input').focus();

                        });


                        // for commnet 
                        $('.fbCmntWrp input').keypress(function(event) {
                             var thisEl = $(this);
                            if (event.which == 13) {
                                event.preventDefault();
                               
                                var cmmnt = $.trim(thisEl.val());
                                if (cmmnt != '') {
                                    $.ajax({
                                        url: BASE_URL + '/social/commentsfacebook',
                                        type: 'POST',
                                        data: {
                                            facebook_post_comment: cmmnt,
                                            facebook_post_id: feedId
                                        },
                                        success: function(data) {
                                            $('.CommentWrp').append('<li>\
                                            <img src="' + mainUserPic + '" class="fbSideCmntUsr">\
                                            <div class="fbCmntCont">\
                                                <strong>' + mainUserName + '</strong> ' + cmmnt + '\
                                             </div>\
                                            </li>');
                                            var srllH = $('.fbClickCnt').prop("scrollHeight");
                                            $('.fbClickCnt').scrollTop(srllH);
                                            thisEl.val('');
                                        }

                                    });
                                }
                            } else {
                                thisEl.focus();
                            }

                        });
                        // end commnet 


                        // for like 
                        $('.likeOnPost, .cmntLike').click(function(event) {
                            event.preventDefault();
                            var thisEl = $(this);
                            var likeID = feedId;
                            var dataType = thisEl.attr('data-type');
                            var likeOnPost = true;
                            if (dataType == 'Like') {
                                var url = BASE_URL + '/social/likefacebook';
                            } else {
                                var url = BASE_URL + '/social/unlikefacebook';
                            }

                            if ($(this).hasClass('cmntLike') == true) {
                                likeID = $(this).closest('li').attr('data-id');
                                likeOnPost = false;
                            }

                            $.ajax({
                                url: url,
                                type: 'POST',
                                data: {
                                    facebook_post_id: likeID
                                },
                                success: function(data) {
                                    if (dataType == 'Like') {
                                        thisEl.text('Unlike');
                                        thisEl.attr('data-type', 'Unlike');
                                        if (likeOnPost == false) {
                                            $.each(comments.data, function(index, val) {
                                                if (val.id == likeID) val.user_likes = true;
                                            });
                                        }

                                    } else {
                                        thisEl.text('Like');
                                        thisEl.attr('data-type', 'Like');
                                        if (likeOnPost == false) {
                                            $.each(comments.data, function(index, val) {
                                                if (val.id == likeID) val.user_likes = false;
                                            });
                                        }


                                    }

                                }

                            });
                        });
                        // end commnet 

                     });

            });


}


function OnCallBack(r, s) 
{
    console.log(r);
}

function  linkedInSocialFeed(response, buttons, titleFeedTop, heightFeedSide, profilePic, windH, myJsonValue, vTp){

                    $('#sideBarFeeds').removeClass('loader');
                    $('.socialFeedLoader').remove();
                    $('.socialfeedWidget a').removeClass('disabled');

                    var htmlSideBar = '<div id="sideBarFeedsLinkedin" class="sideFeedWrp">\
                                <h2 class="feddmtitle">\
                                    <span>\
                                        <i class="fa fa-share-alt fa-lg"></i>\
                                    </span>\
                                    ' + profilePic + titleFeedTop + buttons + '\
                                </h2>\
                                <div class="feedWrpSideContent"><ul></ul></div>\
                            </div>';

                    $('#sideBarFeeds').append(htmlSideBar);

                    checkProfilePic = function(node) {
                        if (typeof node != 'undefined') {
                            return node;
                        } else {
                            return BASE_URL + '/userpics/default-avatar.jpg';
                        }

                    }
                    var htmlList = '';
                    $.each(response.values, function(i, el) {
                        var updateType = el.updateType;
                        var personFirstName = '';
                        var personLastName = '';
                        var personName = '';
                        var personPicUrl = '';
                        var story = '';
                        var personPic = '';
                        var notAppend = true;
                        var notPicClass = '';
                        var creator = '';
                        
                       
                    if(myJsonValue=='platformConnectWithLinkedin'){

                          creator = el.creator;
                          personPicUrl = checkProfilePic(creator.pictureUrl);                        
                          personName = creator.firstName + ' ' + creator.lastName;
                         var title = el.title;
                         var summary = el.summary;
                         var createdTime = el.creationTimestamp;

                        if (personPicUrl != '') {
                             personPic = '<img src="' + personPicUrl + '" width="32">';
                        }
                          htmlList += '<li class="linkdList ' + notPicClass + '"  data-index="'+i+'">\
                                                    ' + personPic + '\
                                                    <div class="sideTwFdContent">\
                                                        <div class="fbsdCnt">\
                                                            <div class="fbDes">\
                                                                <h2>\
                                                                    <span>' + personName + '</span>\
                                                                </h2>\
                                                                '+title + '\
                                                            </div>\
                                                        </div>\
                                                    </div>\
                                                </li>';
                                                // <i class="timeago" title="' + createdTime + '">'+createdTime+'</i>\
                    } else{
                            if (updateType == 'SHAR') {
                                personName = el.updateContent.person.firstName + ' ' + el.updateContent.person.lastName;
                                story = 'shared a link.';
                                personPicUrl = checkProfilePic(el.updateContent.person.pictureUrl);
                            } else if (updateType == 'CONN') {
                                if (el.updateContent.person.id != 'private') {
                                    personName = el.updateContent.person.firstName + ' ' + el.updateContent.person.lastName;
                                    personPicUrl = checkProfilePic(el.updateContent.person.pictureUrl);
                                    secondPersonPicUrl = checkProfilePic(el.updateContent.person.connections.values[0].pictureUrl);
                                    story = 'has a new connection.' + '<div class="secondPerson"><img src="' + secondPersonPicUrl + ' " style="width:20px;"/><div>' + el.updateContent.person.connections.values[0].firstName + ' ' + el.updateContent.person.connections.values[0].lastName + '</div></div>';
                                } else {
                                    notAppend = false;
                                }
                            } else if (updateType == 'CMPY') {
                                notPicClass = 'notPicAvailable';
                                personName = el.updateContent.company.name;
                                if (el.updateContent.companyJobUpdate !== undefined) {
                                    story = 'posted a new job';
                                }
                                if (el.updateContent.companyStatusUpdate !== undefined) {
                                    story = 'shared content. <br>' + el.updateContent.companyStatusUpdate.share.content.title;
                                }
                            }
                            if (personPicUrl != '') {
                                var personPic = '<img src="' + personPicUrl + '" width="32">';
                            }

                            if (updateType == 'SHAR' || updateType == 'CONN' || updateType == 'CMPY') {
                                if (notAppend != false) {
                                     htmlList += '<li class="linkdList ' + notPicClass + '" >\
                                                        ' + personPic + '\
                                                        <div class="sideTwFdContent"><div class="fbsdCnt"><div class="fbDes"><strong>' + personName + '</strong> ' + story + '</div></div></div>\
                                                    </li>';
                                }
                            }


                    }

                        $('#sideBarFeedsLinkedin ul').html(htmlList);
                        $('.sideFeedWrp .feedWrpSideContent').perfectScrollbar({
                            includePadding: true
                        });
                        $("#sideBarFeedsLinkedin .timeago").timeago();

                        $('#sideBarFeedsLinkedin li[data-index]').click(function(event) {
                            event.preventDefault();
                            var thisList = $(this);
                            $('#sideBarFeeds').addClass('fakePositon');
                            var offset = thisList.position();
                            $('#sideBarFeeds').removeClass('fakePositon');
                            var sideBarWidth = $('#sideBarFeeds').width();

                                var thisHeight = thisList.height();
                                $('.fbOpenDetailsLoader').remove();
                                var sideHeaderHTML= '';
                                var msgCont= '';
                                var picture= '';
                                var desCont= '';
                                var likeHTML= '';
                                var commentListHTML= '';
                                var commentHTML= '';
                                
                                var dataIndex = thisList.attr('data-index');
                                var json = response.values;
                                var personPicUrl = checkProfilePic(json[dataIndex].creator.pictureUrl);                        
                                var  personName = json[dataIndex].creator.firstName + ' ' +json[dataIndex].creator.lastName;
                                var title = json[dataIndex].title;
                                var summary = json[dataIndex].summary;
                                var createdTime = json[dataIndex].creationTimestamp; 
                                var attachment = ''; 
                                var noAttachmentImage = 'style="margin-left:0px;"'; 
                                if (typeof(json[dataIndex].attachment) !='undefined') {
                                      attachment ='<div class="makelinkWrp">';
                                     if (typeof(json[dataIndex].attachment.imageUrl) !='undefined') {
                                        attachment +='<img border="0" src="'+json[dataIndex].attachment.imageUrl+'" style="max-width:70px;">';
                                         noAttachmentImage = 'style="margin-left:80px;"'; 
                                     }
                                       attachment +='<div class="makelinkDes" '+noAttachmentImage+'>';
                                        if (typeof(json[dataIndex].attachment.title) !='undefined') {
                                             attachment +='<h2>'+json[dataIndex].attachment.title+'</h2>';
                                         }
                                         if (typeof(json[dataIndex].attachment.summary) !='undefined') {
                                             attachment +='<div class="desc">'+json[dataIndex].attachment.summary+'</div>';
                                         }
                                        
                                      if (typeof(json[dataIndex].attachment.contentUrl) !='undefined') {
                                         attachment += '<div class="makelinkshw"><a href="'+json[dataIndex].attachment.contentUrl+'" target="_blank">'+json[dataIndex].attachment.contentUrl+'</a></div>';
                                     }
                                      
                                        attachment += '</div></div>';
                                  
                                }
                                 if (personPicUrl != '') {
                                         var personPic = '<img src="' + personPicUrl + '" width="40" class="pull-left">';
                                    }
                                  var sideHeaderHTML = '<div id="fbSideHeader">\
                                            '+personPic+'\
                                            <div class="fbSideHeader">\
                                                <h2>\
                                                '+title+'\
                                                 <br>' + personName + ' </h2>\
                                            </div>\
                                        </div>';
                                        //<br>  <span  class="timeago" title="' + createdTime + '">' + createdTime + '</span>
                               var desCont = '<div class="fbSdmsgTxt fbSdDesTxt">' + summary + '</div>';

                           var htmlShow = '<div class="fbClickDescription" data-list="click"  style=" top:' + (offset.top + vTp) + 'px; right:' + (sideBarWidth + 20) + 'px">\
                                        <div class="fbClickCnt">\
                                        <div class="fbClickCntWrp">' + sideHeaderHTML + '\
                                        ' + desCont + '\
                                        ' + attachment + '</div>\
                                        ' + commentListHTML + '\
                                        </div>\
                                        ' + commentHTML + '\
                                    </div><div class="sideBarArrowFeed sprite" ></div>';


                            $('.fbClickDescription, .sideBarArrowFeed').remove();
                            $('body').append(htmlShow); 
                             $('.fbClickDescription, .sideBarArrowFeed').fadeIn();
                             var resizeSideDetails = function() {
                                    var heightBx = $('.fbClickDescription').height();
                                    var totalOffHeight = offset.top + heightBx;
                                    $('.sideBarArrowFeed').css({
                                        top: (offset.top + vTp + thisHeight / 2),
                                        right: sideBarWidth + 6
                                    });
                                    if (windH <= totalOffHeight + vTp) {
                                        $('.fbClickDescription').css({
                                            top: 'auto',
                                            bottom: 0,
                                            position: 'fixed'
                                        });
                                    }
                                    $('.fbOpenDetailsLoader').fadeOut(function() {
                                        $(this).remove();
                                        $('img', thisList).fadeIn();
                                    });

                                    $('.fbClickCnt').perfectScrollbar({
                                        includePadding: true
                                    });
                                    $(".fbClickDescription .timeago").timeago();
                                }
                              resizeSideDetails() ;

                        });

                        

        });
}


function twitterSocialFeed(response, buttons, titleFeedTop, heightFeedSide, profilePic, windH, myJsonValue, vTp){

                    if (twitterFriend == true) {

                        var urlpic = $('#profileimage').attr("datapic-url");
                        profilePic = '<img src="' + urlpic + '"  class="twProPicTop"/>';
                    }

                    if (typeof(response.followstatus) != 'undefined') {
                        if (response.followstatus.relationship.source.following == false) {
                            titleFeedTop = '<a href="javascript:void(0);" data-follow="Follow" class="btn btn-mini addFriendBtn twBtnForFollowing ">Follow</a>';
                        } else {
                            titleFeedTop = '<a href="javascript:void(0);"class="btn btn-mini addFriendBtn twBtnForFollowing" data-follow="Following" >&#x2713; Following</a>';
                        }
                    }

                    $('#sideBarFeedsTw').remove();
                    var htmlSideBar = '<div id="sideBarFeedsTw"  class="sideFeedWrp"><h2 class="feddmtitle"><span><i class="fa fa-share-alt fa-lg"></i></span>' + profilePic + titleFeedTop + '\
                            ' + buttons + '</h2><div class="feedWrpSideContent"><ul></ul></div></div>';

                    $('#sideBarFeeds').removeClass('loader');
                     $('.socialFeedLoader').remove();
                    $('.socialfeedWidget a').removeClass('disabled');


                    $('#sideBarFeeds').append(htmlSideBar);
                    //alert(response.status);
                    var htmlList ='';
                    if(response.status=='success')
                    {
                        $('#sideBarFeeds').show();
                        $.each(response.content, function(i, el) {
                            var dataTime = el.created_at.split('+');
                            var strID = response.content[i].id_str;
                            var ID = response.content[i].id;
                            var Retweet = 'Retweet';
                            var Favorite = 'Favourite';
                            if (el.favorited == true) {
                                Favorite = 'Favourited';
                            }
                            if (el.retweeted == true) {
                                Retweet = 'Retweeted';
                            }
                            var twitterActions = '<div class="twSideLinks"><a href="#" data-id="' + strID + '" data-type="Reply">Reply</a><a href="#" data-type="' + Retweet + '" data-id="' + strID + '" class="' + Retweet + '">' + Retweet + '</a> <a href="#" class="twFav ' + Favorite + '" data-type="' + Favorite + '" data-id="' + strID + '">' + Favorite + '</a></div>';
                            if (iamConnectWithTwitter == false) {
                                twitterActions = '';
                            }
                             htmlList += '<li class="twList" data-sname="' + el.user.screen_name + '">\
                                                <img src="' + el.user.profile_image_url_https + '">\
                                                <div class="sideTwFdContent"><h2><span>' + el.user.name + '</span>\
                                                <i>' + dataTime[0] + '</i></h2>' + el.text + twitterActions + '</div>\
                                            </li>';
                        });
                    $('#sideBarFeedsTw ul').html(htmlList);
                     $('.sideFeedWrp .feedWrpSideContent').scrollTop(0).perfectScrollbar({
                        includePadding: true
                    });
                    }else{
                        htmlList = '<div class="sorryWrong"><i class="fa fa-frown-o fa-5x"></i><span>Sorry!</span> Something went wrong. <br>Please try again in some time.</div>';
                        $('#sideBarFeedsTw .feedWrpSideContent').css({left:0, right:0}).html(htmlList);

                    }
                   


                    $('.twBtnForFollowing').click(function(event) {
                        var dataF = $(this).attr('data-follow');
                        var thisFlollowBtn = $(this);
                        if (dataF == 'Follow') {
                            url = BASE_URL + '/social/makefollowuptwitter';
                        } else {
                            url = BASE_URL + '/social/makeunfollowuptwitter';
                        }
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                'screen_name': response.userResult[0].screen_name
                            },
                            dataType: 'json',
                            success: function() {
                                if (dataF == 'Follow') {
                                    thisFlollowBtn.html('&#x2713; Following').attr('data-follow', 'Following');

                                } else {
                                    thisFlollowBtn.text('Follow').attr('data-follow', 'Follow');
                                }

                            }
                        });

                    });


                    // click btn
                    $('.twSideLinks a:not([data-type="Favourite"], [data-type="Favourited"])').click(function(e) {
                        e.preventDefault();
                        $('#sideBarFeeds').addClass('fakePositon');
                        var sideBarWidth = $('#sideBarFeeds').width();
                        var thisList = $(this);
                        var thisHeight = thisList.closest('li.twList').height();
                        var offset = thisList.closest('li.twList').position();
                        var scrnName = thisList.closest('li.twList').attr('data-sname');
                        var dataType = thisList.attr('data-type');
                        var tweetID = thisList.attr('data-id');
                        var btns = '';
                        var contentsTw = '';
                        var twTitle = '';

                        if (dataType == 'Reply') {
                            twTitle = 'Reply';
                            contentsTw = '<div class="formRow singleRow">\
                                        <div class="retweetTextarea" contenteditable="true" data-editing="true"  maxlength="140"><a href="javascript:void(0);">@' + scrnName + '</a><span tabindex="0">&nbsp;</span></div>\
                                        <div class="formSubtitle"><span class="pull-right limitLength">140 limit</span></div>\
                                    </div>';
                            btns = '<a href="javascript:void(0);" class="btn pull-left btnCancelSDT">Cancel</a>\
                        <a href="javascript:void(0);" class="btn-twitter btn pull-right">Tweet</a>';

                        } else if (dataType == 'Retweet') {
                            twTitle = 'Retweet';
                            contentsTw = '<h2 class="sdTweetMsg">Do you Retweet this to your followers?</h2>';
                            btns = '<a href="javascript:void(0);" class="btn pull-left btnCancelSDT">Cancel</a>\
                                <a href="javascript:void(0);" class="btn-twitter btn pull-right">Retweet</a>';

                        }

                        $('#sideBarFeeds').removeClass('fakePositon');



                        var htmlShow = '<div class="fbClickDescription" data-list="click"  data-type="' + dataType + '" style=" top:' + (offset.top + vTp) + 'px; right:' + (sideBarWidth + 20) + 'px">\
                                            <div class="fbClickCnt">\
                                                <h2 class="socialUpdatesTitle"  style="background:#29b2e4;">' + twTitle + '</h2>\
                                                <div class="postTypeContent">\
                                                    ' + contentsTw + '\
                                                </div>\
                                            </div>\
                                             <div id="fbsideComment" class="twitterTwSideBox">\
                                                ' + btns + '\
                                                <div class="clear"></div>\
                                             </div>\
                                        </div>\
                                        <div class="sideBarArrowFeed sprite" ></div>';


                        $('.fbClickDescription, .sideBarArrowFeed').remove();
                        if (dataType != 'Retweeted') {
                            $('body').append(htmlShow);
                            $('.fbClickDescription, .sideBarArrowFeed').fadeIn();
                        }

                        $('#sideBarFeedsTw').scroll(function() {
                            $('.fbClickDescription[data-list="click"],.sideBarArrowFeed ').fadeOut();
                        });


                        var resizeSideDetails = function() {
                            var heightBx = $('.fbClickDescription').height();
                            var totalOffHeight = offset.top + heightBx;
                            $('.sideBarArrowFeed').css({
                                top: (offset.top + vTp + thisHeight / 2),
                                right: sideBarWidth + 6
                            });
                            if (windH <= totalOffHeight + vTp) {
                                $('.fbClickDescription').css({
                                    top: 'auto',
                                    bottom: 0,
                                    position: 'fixed'
                                });
                            }
                            $('.fbOpenDetailsLoader').fadeOut(function() {
                                $(this).remove();
                                $('img', thisList).fadeIn();
                            });

                            $('.fbClickCnt').perfectScrollbar({
                                includePadding: true
                            });
                        }
                        resizeSideDetails();


                        $('.btnCancelSDT').click(function(event) {
                            event.preventDefault();
                            closeSDetails();
                        });




                        $('.retweetTextarea').keyup(function(event) {
                            resizeSideDetails();
                        });

                        $('.retweetTextarea').trigger('keyup');


                        $('.twitterTwSideBox .btn-twitter').click(function(event) {
                            event.preventDefault();
                            var url = '';
                            var data = '';
                            if (dataType == 'Retweet' || dataType == 'Retweeted') {
                                url = BASE_URL + '/social/retweet';
                                data = {
                                    tweet_id: tweetID
                                }

                            } else if (dataType == 'Reply') {

                                url = BASE_URL + '/social/twitterstatuspost';
                                data = {
                                    tweet: $('.retweetTextarea').text(),
                                    'tweet_id': tweetID
                                }

                            }
                            $.ajax({
                                url: url,
                                type: 'POST',
                                data: data,
                                success: function() {
                                    if (dataType == 'Retweet') {
                                        thisList.text('Retweeted').addClass('Retweeted').attr('data-type', 'Retweeted');
                                    } else if (dataType == 'Retweeted') {
                                        thisList.text('Retweet').removeClass('Retweeted').attr('data-type', 'Retweet');
                                    }
                                    closeSDetails();
                                }
                            });
                        });


                    });

                    // end click btn
                    // click on favorite btn
                    $('.twSideLinks .twFav').click(function(event) {
                        event.preventDefault();
                        var thisEl = $(this);
                        var dataType = thisEl.attr('data-type');
                        var tweetID = thisEl.attr('data-id');
                        var changeDataType = '';
                        var url = '';
                        closeSDetails();
                        if (dataType == 'Favourite') {
                            changeDataType = 'Favourited';
                            url = BASE_URL + '/social/maketweetfav';
                        } else if (dataType == 'Favourited') {
                            url = BASE_URL + '/social/maketweetunfav';
                            changeDataType = 'Favourite';
                        }

                        $.ajax({
                            url: url,
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                tweet_id: tweetID
                            },
                            success: function() {
                                thisEl.text(changeDataType);

                                thisEl.removeClass('Favourited Favourite').addClass(changeDataType).attr('data-type', changeDataType);
                            }
                        });


                    });
                    // end click on favorite btn
}

function rssSocialFeed(response, buttons, titleFeedTop, heightFeedSide, profilePic, windH, myJsonValue, vTp, dataId, indexRss,nofeed){
                   
               if(nofeed==1)
                 {
                        $('#sideBarFeeds .rssVisitor').remove();

                         return false;

                }else{

                    var totalRss ='';
                    var rssLogo = response.totalrss[indexRss].Name;
                    var stot = response.totalrss;
                    var stot2 = response.totalsitecnt;

                 $.each(stot, function(i, el) {
                        if(i==indexRss){

                             totalRss +='<a href="javascript:void(0);" data-type="rss" data-id="'+el.ID+'" class="active">'+(i+1)+'</a>';
                        
                        }else{

                             totalRss +='<a href="javascript:void(0);" data-type="rss" data-id="'+el.ID+'">'+(i+1)+'</a>';
                        
                        }
                       
                    });

                  if(stot.length==1){                    
                    totalRss = '';                                    
                   }
                   if(stot2<2){                    
                    //buttons = '';                                    
                   }

    
                    $('#sideBarFeedsTw').remove();
                    var htmlSideBar = '<div id="sideBarFeedsTw"  class="sideFeedWrp"><h2 class="feddmtitle"><span><i class="fa fa-share-alt fa-lg"></i></span>' + profilePic + titleFeedTop + '\
                            ' + buttons + '<div class="totalRssHeader"><div class="totalCountWrp">'+totalRss+'</div><div class="rssLogosSide">'+rssLogo+'</div></div></h2><div class="feedWrpSideContent" style="top:147px"><ul></ul></div></div>';

                    $('#sideBarFeeds').removeClass('loader');
                     $('.socialFeedLoader').remove();
                    $('.socialfeedWidget a').removeClass('disabled');
                    $('#sideBarFeeds').append(htmlSideBar);
                    var htmlList ='';
                    if(response.content.nofeed!=true || response.nofeed!=true){ 

                        $.each(response.content, function(i, el) {
                          
                             htmlList += '<li class="rss-container">\
                                            <h2>'+el.date+'</h2>\
                                            <h3><a href="'+el.hlink+'" target="_blank">'+el.title+'</a></h3>\
                                            <p>'+el.dis+'</p>\
                                            <div class="rssPostThisbtn"><a href="javascript:void(0)" class="startdbfromrss btn btn-yellow btn-mini" count="1">'+el.postname+' this</a></div>\
                                        </li>';                          


                        });
                    }

                    $('#sideBarFeedsTw ul').html(htmlList);
                    $('.sideFeedWrp .feedWrpSideContent').scrollTop(0).perfectScrollbar({
                        includePadding: true
                    });

                }
}


function privateHTMLPost (){
     function formatStated(state) {
                    var iconF = '<i class="fa fa-globe"></i> ';
                    if(state.id==1) iconF = '<i class="fa fa-lock"></i> ';
                    return iconF + state.text;
                };
                 $(".PrivatePost").select2({
                    formatResult  : formatStated,
                    formatSelection   : formatStated,
                    dropdownCssClass : 'PrivateDropDownCls',
                    minimumResultsForSearch: -1,
                    width:220
                });
}

function seencategories(catid){
    var only ='onlysle';
    var abc =' ';
     $('.dbfeedfilterbycat  input').attr('checked', false);
    //seeglobaldbeelist('nouserid',5,abc,'myhome','catetorylist','dbcat',catid,only);
    seeglobaldbeelist(catid,3,"","myhome","catetorylist","dbcatDropdwn");
 }

function gologout(caller)
{
    caller = typeof(caller)=='undefined'?'':caller;
    var attr = '';
    
    if(caller==1){
        attr = 'caller/caller';
    }
    $.usergoesoffline(0) // goes offline
    window.location.href= BASE_URL+"/myhome/logout/"+attr;
}


//-->    DO NOT CHANGE THE CODE BELOW!    <--
var montharray=new Array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");

function countdown(yr,m,d,hr,min,button,msg,expertId,VideoID){
    var expertId = parseInt(expertId);
    var VideoID = VideoID;
    console.log('exprt ID:'+expertId+' videoID:'+VideoID+' userID:'+userID);
    $('.youTubeVideoPostWrp').remove();
   theyear=yr;themonth=m;theday=d;thehour=hr;theminute=min;
    var today=new Date();
    var todayy=today.getYear();
    if($('#HangoutButton2').length==0){
        $('#HangoutButton').after(button);
    }
    broadcastingMsg=msg;
    
    if (todayy < 1000) {todayy+=1900;}
    var todaym=today.getMonth();
    var todayd=today.getDate();
    var todayh=today.getHours();
    var todaymin=today.getMinutes();
    var todaysec=today.getSeconds();
    var todaystring1=montharray[todaym]+" "+todayd+", "+todayy+" "+todayh+":"+todaymin+":"+todaysec;
    var todaystring=Date.parse(todaystring1)+(tz*1000*60*60);
    var futurestring1=(montharray[m-1]+" "+d+", "+yr+" "+hr+":"+min);
    var futurestring=Date.parse(futurestring1)-(today.getTimezoneOffset()*(1000*60));
    var dd=futurestring-todaystring;
    var dday=Math.floor(dd/(60*60*1000*24)*1);
    var dhour=Math.floor((dd%(60*60*1000*24))/(60*60*1000)*1);
    var dmin=Math.floor(((dd%(60*60*1000*24))%(60*60*1000))/(60*1000)*1);
    var dsec=Math.floor((((dd%(60*60*1000*24))%(60*60*1000))%(60*1000))/1000*1);    
    
    if(dday<=0&&dhour<=0&&dmin<=0&&dsec<=0){ 
                    
        if(expertId!=SESS_USER_ID){
            console.log('now enter video expert:'+expertId+'session:'+SESS_USER_ID);
            var iframeVideo = '<div class="youTubeVideoPostWrp" popup="false"><div class="youTubeVideoPost"><iframe width="100%" height="360" frameborder="0" allowfullscreen="" src="//www.youtube.com/embed/'+VideoID+'?rel=0&amp;autoplay=1&amp;origin=https://development.db-csp.com&amp;version=3" style="background: #000;" allowtransparency="true" id="ytplayer"></iframe></div></div>';

                if(dday==0&&dhour==0&&dmin==0){ 
                    $('#HangoutButton').html('Please wait video is starting soon <span class="saving"><i>.</i><i>.</i><i>.</i></span>');  
                    $('#HangoutButton').addClass('nowStartBroadcasting2');               
                    setTimeout(function(){
                      $('#HangoutButton').after(iframeVideo);
                      $('#HangoutButton').remove();
                      document.getElementById('RefreshYoutube').style.display="inline-block";
                      document.getElementById('RefreshYoutubespan').style.display="inline-block";
                    }, 100000);
                }else{
                    $('#HangoutButton').after(iframeVideo);
                    $('#HangoutButton').remove();
                    document.getElementById('RefreshYoutube').style.display="inline-block";
                    document.getElementById('RefreshYoutubespan').style.display="inline-block";
                }
            
        }else{
            console.log('now enter button');
                $('#HangoutButton').addClass('nowStartBroadcasting');

                $('#HangoutButton').html('Click to start live broadcasting');

           }
        return;
    }
    else {

       
        //$('#HangoutButton2').addClass('disable');
        document.getElementById('count2').style.display="block"; 
        if(expertId!=SESS_USER_ID){
        document.getElementById('RefreshYoutube').style.display="none"; 
        document.getElementById('RefreshYoutubespan').style.display="none";
        document.getElementById('count2').innerHTML="Going live in:";
        }        
        //document.getElementById("HangoutButton2").disabled = true;
        document.getElementById('dday').innerHTML=dday;
        document.getElementById('dhour').innerHTML=dhour;
        document.getElementById('dmin').innerHTML=dmin;
        document.getElementById('dsec').innerHTML=dsec;
        setTimeout(function(){
            countdown(theyear,themonth,theday,thehour,theminute,button, msg,expertId,VideoID);
        },1000);
    }
}
$(document).ready(function(){
 $('body').on('click','.nowStartBroadcasting',function (){
                   
                    $dbConfirm({
                                content:broadcastingMsg, 
                                yes: true,
                                no: true,
                                yesLabel: 'Authenticate yourself',
                                noLabel: 'Cancel',
                                yesClick:function (e){
                                    var gLink = $('#googleauthlink').attr('href');
                                    window.location = gLink;
                                }
                            });
                    
                    $dbTip();
                    
                });
});
