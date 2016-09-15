var QaActiveTagType = '';
var QaActiveTagPage = 1;
var activeTAB = 'publicqa';
var expertIcon = '<a href="javascript:void(0);" class="detailsMobileIcons expertIconOnMobile"></a>';
var twitterFeedIcon = '<a href="javascript:void(0);" class="detailsMobileIcons twitterIconOnMobile"><i class="fa dbTwitterIcon fa-2x"></i></a>';

$(function() {

    $('body').on('click', '.removeExpert', function() {
        var Ownerid = $(this).attr('ownerid');
        var removedby = $(this).attr('loginid');
        var expertid = $(this).attr('data-expertid');
        var type = $(this).attr('type');
        var dbid = $("#dbid").val();
        var expertText = $('#expertText').val();
        if (type == 'EXPERT')
            var msg = 'Are you sure you want to remove yourself?';
        else
            var msg = 'Are you sure you want to remove this ' + expertText + '?';

        var AskForRemoveExpert = '<div style="text-align:center;font-size:18px;color:#999999;">\
                <div class="formRow" id="content_data"  style="padding:30px 0px; 50px 0px;">' + msg + '</div>\
                <div class="clearfix"></div>\
                </div>';
        $.dbeePopup(AskForRemoveExpert, 
        {
            overlay: true,
            closeLabel: 'No',
            otherBtn: '<a href="javascript:void(0);" data-expert = "'+expertid+'" dbid="' + dbid + '" Ownerid="' + Ownerid + '" Loginid="' + removedby + '" class="pull-right btn btn-yellow"  id="removeExpertConfirm">Yes</a>'
        });
    });
    $('body').on('click', '#removeExpertConfirm', function() {
        $('#removeExpertConfirm').addClass('processBtnLoader').append(' <i class="fa fa-spinner fa-spin"></i>');
        
        var Ownerid = $(this).attr('Ownerid');
        var removedby = $(this).attr('Loginid');
        var expertid = $(this).attr('data-expert');
        var dbid = $("#dbid").val();
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: 
            {
                'removedby': removedby,
                'dbid': dbid,
                'Ownerid': Ownerid,
                'expertid':expertid
            },
            url: BASE_URL + '/expert/remove',
            success: function(response) 
            {
                getexpertremove(dbid,response.expertid);
                $('.closePostPop').trigger('click');
                if (localTick == false) 
                {
                    socket.emit('getexpertremove',dbid,clientID,response.expertid);
                    socket.emit('chkactivitynotification', dbid, clientID);
                }
                
            }
        });
    });

     $('body').on('click', '.hideAskQues .newRadioBtn', function(e){
          e.preventDefault();
            id = $(this).attr('data-expert-ask');
            p = $(this).closest('.hideAskQues');
            dbeeid = $('#dbid').val();
        if ($('input',this).attr('checked')) {          
            return false;
        } else {
          $('input', p).attr('checked', false);
          $('input',this).attr('checked', true);
          
        }
          var status = $('input',this).val();
        $.ajax({
            type: "POST",
            dataType: 'json',
            cache:false,
            data: {'status': status,'id':id,'dbeeid':dbeeid},
            url: BASE_URL + '/expert/expertasksetting',
            success: function(response) 
            { 
                if(status==0)
                {
                    $('#askquestion-'+id).addClass('disabled disabledQuestion').removeClass('AskAnExpert');
                    $('#askquestion-'+id).attr('title','Disabled');
                    socket.emit('askquestion', id, clientID,'hide',dbeeid);
                }
                else
                { 
                    $('#askquestion-'+id).addClass('AskAnExpert').removeClass('disabled disabledQuestion');
                    $('#askquestion-'+id).attr('title','');
                    socket.emit('askquestion',id, clientID,'show',dbeeid);
                }
            }
        });
    });
     $('body').on('click', '.SendMessageDbeeUser', function(event) {

        $(this).addClass('processBtnLoader').append(' <i class="fa fa-spinner fa-spin"></i>');
        var user = [];
        if (!$('#followers-list').is(':hidden')) {
            $("input[name=inviteuser-followers]:Checked").each(function() {

                user.push($(this).val());
            });
        } else if (!$('#following-list').is(':hidden')) {
            $("input[name=inviteuser-following]:Checked").each(function() {

                user.push($(this).val());
            });
        } else if (!$('#search-list').is(':hidden')) {
            $("input[name=inviteuser-search]:Checked").each(function() {

                user.push($(this).val());
            });
        }
        var user_concat_val = user.join(",");

        if (user.length > 0) 
        {
            var dbid = $('#dbid').val();
            $.ajax({
                type: "POST",
                dataType: "json",
                url: BASE_URL + "/expert/dbeeinvite",
                data: {
                    "user_concat_val": user_concat_val,
                    'dbid': dbid
                },
                success: function(result) 
                {
                    $('.fa-spin').remove();
                    if(result.status=='success'){
                        $('.closePostPop').trigger('click');
                        if(localTick == false){
                            socket.emit('chkactivitynotification', dbid, clientID);
                        }
                        getexpertlist(dbid);
                    }else{
                         $dbConfirm({
                                content: result.content,
                                yes: false,
                                error: true
                            });
                            $('.fa-spin').remove();
                            return false;
                    }
                }
            });
        }else{
            $dbConfirm({
                content: 'Please select a user',
                yes: false,
                error: true
            });
            $('.fa-spin').remove();
            return false;
        }

    });
    
    
    $('body').on('click', '.singleselectuser .labelCheckbox input', function() 
    {
         $(this).closest('.singleselectuser').find('.labelCheckbox input').attr('checked', false);
         $(this).attr('checked', true);


    });

    $('body').on('click', '.invitefacebookfriexpert', function() 
    {    
       invitefacebook();
    });

    $('body').on('click', '.invitelinkedinfriexpert', function() 
    {    
        invitelinkedin();
    });

    $('body').on('click', '.invitexport', function() 
    {
        $.dbeePopup('close');
        setTimeout(function (){
        if(allowmultipleexperts==1)
            var reportTemplate = '<h2>Invite from</h2><div style="padding:20px 0px 30px 0px; width:100%; display:inline-block;" id="content_data_button" class="singleselectuser" ></div><div class="clearfix"></div>';
        else
            var reportTemplate = '<h2>Invite from</h2><div style="padding:20px 0px 30px 0px; width:100%; display:inline-block;" id="content_data_button" ></div><div class="clearfix"></div>';
       
        var db = $(this).attr('dbid');
        $.dbeePopup(reportTemplate, { otherBtn:'<a href="javascript:void(0);" class="pull-right btn btn-yellow SendMessageDbeeUser" >Invite</a>'});

        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {
                'db': db
            },
            url: BASE_URL + '/expert/invite',
            success: function(response) 
            {
                $("#content_data_button").html(response.content);
                loadfollowersforexpert();
                $.dbeePopup('resize');
            }
        });

        },2000);
    });

    
    $('body').on('click', '.AskAnExpert', function() 
    {
        busy = false;
        var Tthis = $(this);
        var dbid = $(this).attr('dbeeid');
        var expertid = $(this).attr('expertid');
        var promoted = $(this).attr('data-promoted');
        var pr = $(this).closest('.rboxContainer');
        if(Tthis.hasClass('disabled'))
        { 
            $('.errorWarning').remove();
            $('.dbExpertUserDesc',pr).append("<p class ='errorWarning'>Sorry you can't ask a question at this time.</p>");
            return false;
        }else
        {
            $('.errorWarning').remove();
            Tthis.hide();
            Tthis.before('<div class="expertQAArea">\
                            <textarea placeholder="Please type your question"></textarea>\
                            <div class="pstBriefFt">\
                                <a href="javascript:void(0);"  class="AskQAExpertbox">Ask</a>\
                                <a href="javascript:void(0);" class="cancelQAExpertbox">Cancel</a>\
                            </div>\
                        </div>');
            $(window).trigger('resize');
             $('.cancelQAExpertbox').click(function (){
                    Tthis.siblings('.expertQAArea').remove();
                    Tthis.show();
                     $(window).trigger('resize');
            });
              $('.AskQAExpertbox').click(function (){
                   //-------------
                    var Tthis = $(this);
                   
                    var question = $('textarea', pr).val();
                    if (question != "") {
                        if ($('#commentleagueExist').val() == 0)
                            $('#comment-league-wrapper').hide();

                        $(Tthis).addClass('processBtnLoader').append(' <i class="fa fa-spinner fa-spin"></i>');
                        $.ajax({
                            type: "POST",
                            dataType: 'json',
                            data: {
                                'dbid': dbid,
                                'expertid': expertid,
                                'question': question
                            },
                            url: BASE_URL + '/expert/askanexpertsent',
                            beforeSend: function() {
                                $("#dbee-expert-question").html('<div id="content_dat" ><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div><div class="clearfix"></div>');
                            },
                            success: function(response) {
                                $('.fa-spin').remove();
                                
                                $('.cancelQAExpertbox', pr).trigger('click');
                                if (response.status == 'success' && dbid!='') 
                                {
                                    if (localTick == false) 
                                    {   
                                        socket.emit('chkactivitynotification', dbid, clientID);
                                        showQaTab('myquestion','none');
                                        if(response.refer=='gotoexpert')
                                            socket.emit('askexpert',dbid, clientID,expertid,response.QAID,response.refer);
                                        else if(response.refer=='gotoowner')
                                            socket.emit('askexpert',dbid, clientID,response.Ownerid,response.QAID,response.refer);
                                    }
                                }
                                else if (response.status == 'success' && dbid==0) 
                                {
                                  socket.emit('chkactivitynotification', dbid, clientID);
                                  socket.emit('promotedexpert', clientID,expertid,response.QAID);
                                  
                                } 
                                else
                                {
                                    $dbConfirm({
                                        content: response.content,
                                        yes: false,
                                        error: true
                                    });
                                }
                            }

                        });

                    } else {
                        $dbConfirm({
                            content: "Please enter your question",
                            yes: false,
                            error: true
                        });
                    }
                   //--------------
            });
        }

    });


    $('body').on('click', '.SendQuestionToExpert', function() 
    {
        var Tthis = $(this);
        var dbid = $("#dbidfromexpert").val();
        var question = $('#ask_to_question').val();
        var expertid = $(this).attr('data-expert');
        if (question != "") {
            if ($('#commentleagueExist').val() == 0)
                $('#comment-league-wrapper').hide();

            $(Tthis).addClass('processBtnLoader').append(' <i class="fa fa-spinner fa-spin"></i>');
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {
                    'dbid': dbid,
                    'expertid': expertid,
                    'question': question
                },
                url: BASE_URL + '/expert/askanexpertsent',
                beforeSend: function() {
                    $("#dbee-expert-question").html('<div id="content_dat" ><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div><div class="clearfix"></div>');
                },
                success: function(response) {
                    $('.fa-spin').remove();
                    $('.closePostPop').trigger('click');
                    if (response.status == 'success' && dbid!='') 
                    {
                        if (localTick == false) 
                        {   
                            socket.emit('chkactivitynotification', dbid, clientID);
                            showQaTab('myquestion','none');
                            if(response.refer=='gotoexpert')
                                socket.emit('askexpert',dbid, clientID,expertid,response.QAID,response.refer);
                            else if(response.refer=='gotoowner')
                                socket.emit('askexpert',dbid, clientID,response.Ownerid,response.QAID,response.refer);
                        }
                    }
                    else if (response.status == 'success' && dbid==0) 
                    {
                      socket.emit('chkactivitynotification', dbid, clientID);
                      socket.emit('promotedexpert', clientID,expertid,response.QAID);
                      
                    } 
                    else
                    {
                        $dbConfirm({
                            content: response.content,
                            yes: false,
                            error: true
                        });
                    }
                }

            });

        } else {
            $dbConfirm({
                content: "Please enter your question",
                yes: false,
                error: true
            });
        }

    });


    $('body').on('click', '.Myquestion', function() 
    {
        $('#myQACount').html('').css({display:'none'});
        QaActiveTagPage = 1; 
        $(this).addClass('active');
        var dbid = $("#dbid").val();
        $("#dbee-comments").hide();
        $("#dbee-expert-question").html('<div id="content_dat" ><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div><div class="clearfix"></div>').show();
        busy = false;
        setTimeout(function() {
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {
                    'dbid': dbid,'page':QaActiveTagPage
                },
                url: BASE_URL + '/expert/myquestions',
                success: function(response) 
                {
                    if(response.status=='success' && response.content!='')
                    {
                        QaActiveTagType = 'myquestion';
                        QaActiveTagPage = QaActiveTagPage+1;
                        $("#dbee-expert-question").html(response.content);
                    }
                    else if(response.status=='success')
                    {
                        $('#dbee-expert-question').html('<div class="noFound firstUserCmnt commentWillStart">No my questions found.</div>');
                    }
                    busy = true;
                }
            });
        }, 500);

    });
    $('body').on('click', '.ShowMyquestion', function() 
    {
        $('#LiveQACount').html('').css({display:'none'});
        QaActiveTagPage = 1;
        $(this).addClass('active');
        var dbid = $("#dbid").val();
        $("#dbee-comments").hide();
        $("#dbee-expert-question").html('<div id="content_dat" ><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div><div class="clearfix"></div>').show();
        busy = false;
        setTimeout(function() {
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {
                    'dbid': dbid,'page':QaActiveTagPage
                },
                url: BASE_URL + '/expert/livequestion',
                success: function(response) 
                {
                    if(response.status=='success' && response.content!='')
                    {
                        QaActiveTagType = 'liveqa';
                        QaActiveTagPage = QaActiveTagPage+1;
                        $("#dbee-expert-question").html(response.content);
                    }else if(response.status=='success')
                    {
                        $('#dbee-expert-question').html('<div class="noFound firstUserCmnt commentWillStart"><i class="fa fa-pencil-square-o fa-2x"></i>No answered questions found.</div>');
                    }
                    busy = true;
                }
            });
        }, 500);
    });
    
    $('body').on('click', '.Pendingquestion', function() 
    {
        busy = false;
        $('#pendingQACount').html('').css({display:'none'});
        QaActiveTagPage = 1;
        $("#dbee-comments").hide();
        $("#dbee-expert-question").html('<div id="content_dat" ><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div><div class="clearfix"></div>').show();
        $(this).addClass('active');
        var dbid = $("#dbid").val();
        setTimeout(function() 
        {
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {
                    'dbid': dbid,'page':QaActiveTagPage
                },
                url: BASE_URL + '/expert/pendingquestion',
                success: function(response) 
                {
                
                    if(response.status=='success' && response.content!='')
                    {
                        QaActiveTagType = 'pending';
                        QaActiveTagPage = QaActiveTagPage+1;
                        $("#dbee-expert-question").html(response.content);
                    }else if(response.status=='success')
                    {
                        $('#dbee-expert-question').html('<div class="noFound firstUserCmnt commentWillStart">No pending questions found.</div>');
                    }
                    busy = true;
                }
            });
        }, 300);
    });

    $('body').on('click', '#my-dbees-qa', function() 
    {
        busy = false;
        $(this).addClass('active');
        var dbid = '';
        QaActiveTagPage = 1;
        $('#my-dbees').html('').addClass('myWhiteBg my-dbees-qa');
        $dbLoader('#dbee-feeds');
        setTimeout(function() 
        {
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {
                    'dbid': dbid,'page':QaActiveTagPage
                },
                url: BASE_URL + '/expert/promotedquestion',
                beforeSend: function() {
                     $dbLoader('#my-dbees');
                     $('.CountQA').hide();
                     $('.CountQA').html('');
                },
                success: function(response) 
                {
                    if(response.status=='success' && response.content!='')
                    {
                        QaActiveTagType = 'promoted';
                        QaActiveTagPage = QaActiveTagPage+1;
                        $("#my-dbees").html(response.content);
                    }else if(response.status=='success')
                    {
                        $('#my-dbees').html('<div class="noFound firstUserCmnt commentWillStart">No pending questions found.</div>');
                    }
                    busy = true;
                }
            });
        }, 300);
    });

    
    $('body').on('click', '.publicQa', function() 
    {
        busy = false;
        $('#publicQaCount').html('').css({display:'none'});
        QaActiveTagPage = 1;
        
        $("#dbee-comments").hide();
        $("#dbee-expert-question").html('<div id="content_dat" ><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div><div class="clearfix"></div>').show();
        $(this).addClass('active');
        var dbid = $("#dbid").val();
        setTimeout(function() 
        {
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {
                    'dbid': dbid,'page':QaActiveTagPage
                },
                url: BASE_URL + '/expert/makelivequestion',
                success: function(response) 
                {
                    if(response.status=='success' && response.content!='')
                    {
                        QaActiveTagType = 'makelive';
                        QaActiveTagPage = QaActiveTagPage+1;
                        $("#dbee-expert-question").html(response.content);
                    }else if(response.status=='success')
                    {
                        $('#dbee-expert-question').html('<div class="noFound firstUserCmnt commentWillStart">No live questions found.</div>');
                    }
                    busy = true;
                }
            });
        }, 300);
    });
    
    $(window).scroll(function()
    {
        if  ($(window).scrollTop() == $(document).height() - $(window).height()) 
        {
            if(QaActiveTagType=='pending' && busy == true)
                pendingquestion(QaActiveTagPage);
            else if(QaActiveTagType=='myquestion' && busy == true)
                myquestion(QaActiveTagPage);
            else if(QaActiveTagType=='liveqa' && busy == true)
                livequestion(QaActiveTagPage);
            else if(QaActiveTagType=='makelive' && busy == true)
                makelivequestion(QaActiveTagPage);
            else if(QaActiveTagType=='promoted' && busy == true)
                promotedquestion(QaActiveTagPage);
            
        }
    });
    $('body').on('click', '.makeownexpert', function() {
        var expertText = $('#expertText').val();
        var content = 'Do you want to make yourself the ' + expertText + '?';
        $dbConfirm({
            content: content,
            yes: true,
            yesClick: function() {
                var dbid = $('#dbid').val();
                var dbowners = $('#dbowners').val();
                createrandontoken(); // creating user session and token for request pass
                data = 'acceptExpertRequest=true&dbid=' + dbid + '&dbowners=' + dbowners + '&' + userdetails
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    data: data,
                    url: BASE_URL + '/expert/makeown',
                    success: function(response) 
                    { 
                        if (response.used == 'used') 
                        {
                            socket.emit('getexpert',dbid,clientID,response.expertid);
                            $('.invtExpert').html('');
                            window.location.reload();
                        } else if (response.status == 'success') {
                            $dbConfirm({
                                content: 'Sorry you are late! Another user has already joined as an expert.',
                                yes: false,
                                error: true
                            });
                        } else if (response.status == 'error')
                            $dbConfirm({
                                content: response.message,
                                yes: false,
                                error: true
                            });
                    }
                });
            }
        });

    });


    $('body').on('click','.reply_to_question', function()
    {
        busy = false;
        $(this).append(' <i class="fa fa-spinner fa-spin"> </i>').css('cursor', 'default');
        $(this).removeClass('reply_to_question');
        cmntval     =   $('#reply_question_now').val();
        parentId    =   $('#reply_parentid').val();
        parentdbId  =   $('#reply_dbid').val();
        cmntownerId =   $('#reply_questionownerid').val();
        reply_expert_id =   $('#reply_expert_id').val();
        dbowners    =   $('#dbowners').val();
        if(cmntval=='')
        {
            $dbConfirm({content:'Please add your reply', yes:false,error:true});
            return false;
        }
        url  =  BASE_URL+"/expert/answerposting";
        data = 'dbid='+parentdbId+'&parentid='+parentId+'&answer='+cmntval+'&replytype=answer'+'&expert_id='+reply_expert_id+'&cmntownerId='+cmntownerId+'&dbowners='+dbowners;
        $.ajax({
            url : url,
            data: data,
            dataType:'json',
            type: 'post',
            success : function(result)
            { 
                if(parentdbId!=0)
                {
                    if(result.expertRemoved==true)
                    {
                        $dbConfirm({content:'Sorry, this expert has been removed', yes:false,error:true});
                    }
                    else
                    {
                        $('.replymebox').html('');
                        $('.replymebox').hide();
                        $("#comment-block-question-"+parentId).hide();
                        socket.emit('chkactivitynotification', parentdbId, clientID);
                        if(!$('li a').hasClass('ShowMyquestion'))
                            showQaTab('pendingquestion','none');
                        if(reply_expert_id==dbowners && !$('li a').hasClass('ShowMyquestion'))
                            showQaTab('pendingquestion','nones');
                        socket.emit('makelive',parentdbId, clientID,parentId);

                        if(result.pendingquestionCount==0)
                        {
                            $('.Pendingquestion').remove();
                            $('#dbee-expert-question').html('');
                            showQaTab('publicQa');
                        }
                        setTimeout(function(){
                            $('#reply_to_question .fa-spin').remove();
                            $('#reply_to_question').css('cursor', 'pointer');
                            socket.emit('livequestion',parentdbId, clientID,result.owner,dbowners,parentId);
                        }, 2000);  
                    }
                }else
                {
                    getpromotedquestion(parentId,'normal');
                    socket.emit('chkactivitynotification', parentdbId, clientID);
                    socket.emit('makelive',parentdbId, clientID,parentid);
                }
                busy = true;
            }
        });
        return false;
    });
    
    $('body').on('click', '#rejectExpertRequest', function() {
        $('#warningMsg').hide();
    });

    $('body').on('click', '#acceptExpertRequest', function() 
    {
        var dbid = $('#dbid').val();
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {
                'acceptExpertRequest': true
            },
            url: BASE_URL + '/expert/acceptexpertrequest',
            success: function(response) 
            {
                if (response.used == 'used') 
                { 
                    if(localTick == false){ 
                        callsocket();
                    }
                     socket.emit('getexpert',dbid,clientID,response.expertid); 
                     window.location.href = response.redirect;
                } else {
                    var UsedAcceptPopup = '<div style="text-align:center;font-size:18px;color:#999999;"><div style="padding:30px 0px; 50px 0px;"  class="formRow">Sorry you are late! Another user has already joined as an expert.</div><div class="clearfix"></div></div>';
                    $.dbeePopup(UsedAcceptPopup, {
                        width: 420,
                        overlay: true
                    });
                }
                $('#warningMsg').hide();
            }

        });

    });

    $('body').on('click','.ExpertPostToFacebook',function()
    {
        $(this).addClass('processBtnLoader').append(' <i class="fa fa-spinner fa-spin"></i>');

        var dbid = $("#dbid").val();
        var userInfo = [];
        $('input:checkbox[name=FacebookInvite]').each(function() 
        {    
            if($(this).is(':checked'))
                userInfo.push($(this).val());
        });
        var stringuserInfo = userInfo.join();
        var flag = 0;
        $('input:checkbox[name=FacebookInvite]').each(function() {
            if ($(this).is(':checked')) {
                userInfo.push($(this).val());
                flag = 1;
            }
        });

        if (flag == 0) {
            $dbConfirm({
                content: 'Please select a Facebook friends',
                yes: false,
                error: true
            });
            $('.fa-spin').remove();
            return false;
        }

        $.ajax({
            type: "POST",
            dataType : 'json',
            url:"/social/facebookinviteexpert",
            data:{'stringuserInfo':stringuserInfo,'dbid':dbid},
            success:function(result){
                $('.closePostPop').trigger('click');
                $.dbeePopup('close');
                getexpertlist(dbid);
                $('.fa-spin').remove();
             }   
        });


    });
     $('body').on('click', '.SendMessageTwitter', function() 
    {
        $(this).show().addClass('processBtnLoader').append(' <i class="fa fa-spinner fa-spin"></i>');
        var userInfo = [];
        $('input:checkbox[name=twitter]').each(function() {
            if ($(this).is(':checked'))
                userInfo.push($(this).val());
        });
        var contcat_name = userInfo.join(',');
        var flag = 0;
        $('input:checkbox[name=twitter]').each(function() {
            if ($(this).is(':checked')) {
                userInfo.push($(this).val());
                flag = 1;
            }
        });

        if (flag == 0) {
            $dbConfirm({
                content: 'Please select a Twitter follower',
                yes: false,
                error: true
            });
            $('.fa-spin').remove();
            return false;
        }
        var dbid = $('#dbid').val();
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {
                'acceptExpertRequest': true,
                'contcat_name': contcat_name,
                'dbid': dbid
            },
            url: BASE_URL + '/social/twitterinvitation',
            success: function(response) {
                $('.closePostPop').trigger('click');
                $('.fa-spin').remove();
                getexpertlist(dbid);
                $dbConfirm({
                    content: 'Inivitation sent!',
                    yes: false
                });
            }
        });
    });

    $('body').on('click','.SendMessageLinkedin',function()
    {
        $(this).addClass('processBtnLoader').append(' <i class="fa fa-spinner fa-spin"></i>');
        var userInfo = [];
        $('input:checkbox[name=linkedin]').each(function() 
        {    
            if($(this).is(':checked'))
            userInfo.push($(this).val());
        });
        var flag = 0;
        $('input:checkbox[name=linkedin]').each(function() {
            if ($(this).is(':checked')) {
                userInfo.push($(this).val());
                flag = 1;
            }
        });

        if (flag == 0) {
            $dbConfirm({
                content: 'Please select a LinkedIn connections',
                yes: false,
                error: true
            });
            $('.fa-spin').remove();
            return false;
        }

        var stringuserInfo = userInfo.join();
        var dbid = $('#dbid').val();
            $.ajax({
                type: "POST",
                dataType : 'json',
                url:"/social/sendmessagetolinkedin",
                data:{"userid": stringuserInfo,'dbid':dbid},
                success:function(result){
                    $('.closePostPop').trigger('click');
                    $.dbeePopup('close');
                    $('.fa-spin').remove();
                }   
            });
    });

    $('body').on('click', '.movetocomment', function() {
        var parentid = $(this).attr('parentid');
        var answer_ids = $(this).attr('answer_ids');
        var thisEl = $(this);
        $dbConfirm({
            content: 'Are you sure you want to move this Q & A to the post feed?',
            yesClick: function() {
                var dbid = $('#dbid').val();
                var dbowners = $('#dbowners').val();

                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    data: {
                        'movetoexpert': true,
                        'dbid': dbid,
                        'parentid': parentid,
                        'answer_ids': answer_ids,
                        'dbowners': dbowners
                    },
                    url: BASE_URL + '/expert/movetocomment',
                    success: function(response) {
                        if (localTick == false) 
                        {
                            socket.emit('checkcomment', dbid, clientID);
                            socket.emit('chkactivitynotification', dbid, clientID);
                        }
                        thisEl.removeClass();
                        thisEl.html('<span class="qnamoved">Moved to post feed</span></div>');
                        $('.closePostPop').trigger('click');
                    }
                });
            }
        });
    });
    

    $('body').on('click', '.makelive', function() 
    {
        var parentid = $(this).attr('parentid');
        var answer_ids = $(this).attr('answer_ids');
        var thisEl = $(this);
        $dbConfirm({
            content: 'Are you sure you want to move this Q & A to live questions?',
            yesClick: function() {
                var dbid = $('#dbid').val();
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    data: {
                        'makelive': true,
                        'dbid': dbid,
                        'parentid': parentid,
                        'answer_ids': answer_ids
                    },
                    url: BASE_URL + '/expert/makelive',
                    success: function(response) {
                        if (localTick == false) 
                        {
                            socket.emit('chkactivitynotification', dbid, clientID);
                            if(!$('li a').hasClass('publicQa'))
                                showQaTab('makelive','none');
                            socket.emit('makelive',dbid, clientID,parentid);
                            //$('.closePostPop').trigger('click');
                        }
                        thisEl.removeClass();
                        thisEl.html('<span class="qnamadelive">Live</span></div>');
                        $('.closePostPop').trigger('click');
                    }
                });
            }
        });
    });

    $('body').on('click', '.movetoexpert', function() 
    {
        var dbid = $(this).attr('dbid');
        var commentid = $(this).attr('commentid');
        var type = $(this).attr('data-type');
        var thisEl = $(this);
        var expertText = $('#expertText').val();
        if(type=='movetohide')
        {
            message = 'Are you sure you want to remove this from pending questions?';
            label = '';
        }else if(type=='movetoother'){
            message = "Are you sure you want to verify and 'Mark as other'?";
            label = '<div style="float:right; margin-left:2px;" class="qaother">Marked as other</div>';
        }
        else if(type=='movetoexpert'){
            message = 'Are you sure you want to verify?';
            label = '<div style="float:right; margin-left:2px;" class="qaverified">Verified</div>';
        }
        
        $dbConfirm({
            content: message,
            yesClick: function() {
                var dbid = $('#dbid').val();
                var dbowners = $('#dbowners').val();
                $('.Confirmmovetocomment').addClass('processBtnLoader').append(' <i class="fa fa-spinner fa-spin"></i>');
                $('.processBtnLoader').removeClass('Confirmmovetocomment');
                $('.closePostPop').trigger('click');
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    data: {
                        'movetoexpert': true,
                        'dbid': dbid,
                        'commentid': commentid,'type':type
                    },
                    url: BASE_URL + '/expert/movetoexpert',
                    success: function(response) 
                    {
                        if (localTick == false && response.status=='success') 
                        {                            
                            socket.emit('chkactivitynotification', dbid, clientID);
                            socket.emit('askexpert',dbid, clientID,response.expertid,commentid,'movetoexpert');
                            $('#qalinks_'+commentid).html(label);
                        }
                        else if(response.status=='hides')
                        {
                            $('#comment-block-question-'+commentid).remove();
                            socket.emit('askexpert',dbid, clientID,response.expertid,commentid,'movetoexpert');
                        }else if(response.removed==true)
                        {
                            $dbConfirm({
                                content: response.message,
                                yes: false,
                                error: true
                            });
                            $('#comment-block-question-'+commentid).remove();
                        }
                        $('.closePostPop,.Pendingquestion').trigger('click');
                    }
                });
            }
        });
    });

   
    
    $('body').on('click','.removeanswer', function()
    {
        parentid = $(this).attr('parentid');
        answer_ids = $(this).attr('answer_ids');
        $dbConfirm({
            content:"Are you sure you want to remove your answer?", 
            yesClick:function(){
                $('#removeConfirmanswer').addClass('processBtnLoader');
                dbid = $("#dbid").val();
                // ajax start
                $.ajax({
                    type : "POST",
                    dataType : 'json',
                    data:{'parentid':parentid,'answer_ids':answer_ids,'dbid':dbid},
                    url : BASE_URL + '/expert/removeanswer',
                    beforeSend : function() {},
                    success : function(response)
                    {
                        $('.closePostPop').trigger('click');
                        $('#comment-block-question-'+answer_ids).remove();
                        socket.emit('askexpert',dbid, clientID,response.expertid,parentid,'movetoexpert');
                    }   
                });
                // end  ajax
            }
        });
    });


    $('body').on('click', '.removequestion', function() 
    {
        var commentid = $(this).attr('commentid');
        var expert_id = $(this).attr('expert_id');
        var cmntowner = $(this).attr('cmntowner');
        var dbid = $('#dbid').val();
        var content = 'Do you want to remove this question?';
        $dbConfirm({
            content: 'Do you want to remove this question?',
            yesClick: function() {
                $('.removequestionConfirmed').addClass('processBtnLoader').append(' <i class="fa fa-spinner fa-spin"></i>');
                $('.processBtnLoader').removeClass('removequestionConfirmed');
                $('.closePostPop').trigger('click');

                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    data: {
                        'expert_id': expert_id,
                        'question': commentid,
                        'dbid': dbid,
                        'cmntowner': cmntowner
                    },
                    url: BASE_URL + '/expert/removequestionbyexpert',
                    success: function(response) 
                    {
                        if (response.status == 'success') 
                        {  
                             if (localTick == false) 
                            {
                                showQaTab('myquestion','none');
                                setTimeout(function() {
                                     $('.Myquestion').trigger('click');
                                }, 1000);
                                socket.emit('chkactivitynotification', dbid, clientID);
                                $('#comment-block-question-'+commentid).remove();
                            }
                            
                        } else {
                            $dbConfirm({
                                content: response.message,
                                yes: false,
                                error: true
                            });
                        }
                        //$('.closePostPop').trigger('click');
                    }
                });
            }
        });
    });

    $('body').on('click', '.invtExpertExist', function() 
    {
        var dbid = $(this).attr('dbid');
        var dbid = $('#dbid').val();
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {'dbid': dbid},
            url: BASE_URL + '/expert/checkexistexpert',
            success: function(response) 
            {
                if (response.status == 'success') 
                { 
                    console.log(response);
                    $.dbeePopup(response.profHTML, {overlay:true, otherBtn:'<a href="javascript:void(0);" class="btn btn-yellow pull-right CancelPendingRequest" >Cancel Invite</a>'});
                } 
            }
        });
    });

    $('body').on('click', '.CancelPendingRequest', function() 
    {
        var dbid = $('#dbid').val();
        var userDbee = [];
        var userSocial = [];
        var flag = 0;
        $('input:checkbox[name=socialUser]').each(function() 
        {    
            if($(this).is(':checked') && ($(this).attr('data-type')=='facebook' || $(this).attr('data-type')=='twitter')){
                flag = 1
                userSocial.push($(this).val());
            }else if($(this).is(':checked')){
                flag = 1
                userDbee.push($(this).val());
            }
        });

        if (flag == 0) 
        {
            $dbConfirm({
                content: 'Please select a user',
                yes: false,
                error: true
            });
            $('.fa-spin').remove();
            return false;
        }
        
        $(this).removeClass('CancelPendingRequest').append(' <i class="fa fa-spinner fa-spin"></i>');
        var dbeeUser = userDbee.join();
        var socialUser = userSocial.join();

        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {'dbid': dbid,'userDbee':dbeeUser,'socialUser':socialUser},
            url: BASE_URL + '/expert/cancelpending',
            success: function(response) 
            {
                $.dbeePopup('close');
                getexpertlist(dbid);
                $('.fa-spin').remove();
            }
        });
    });

    $('body').on('click', '.InviteSocial', function(e) 
    {
        $(this).removeClass('InviteSocial');
        $(this).append(' <i class="fa fa-spinner fa-spin"> </i>');
        var userInfo = [];
        Tthis = $(this);
        flag = 0;
        checkedList = $('#PrivatePost').val();
        $('input:checkbox[name=socialUser]').each(function() 
        {    
            if($(this).is(':checked')){
                userInfo.push($(this).val());
                flag = 1;
            }
        });
        if (flag == 0) 
        {
            $dbConfirm({
                content: 'Please select a user',
                yes: false,
                error: true
            });
            $('.fa-spin').remove();

            Tthis.addClass('InviteSocial');
            return false;
        }
        var socialUser = userInfo.join();

        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {"shareType": shareType,'uniqueIDSocial':uniqueIDSocial,'callingfor':callingfor,'socialUser':socialUser,'checkedList':checkedList},
            url: BASE_URL + '/social/socialinvite',
            success: function(response) 
            {
                if(response.status=='success')
                {
                    $('.closePostPop').trigger('click');
                    $.dbeePopup('close');
                    $('.fa-spin').remove();
                    getexpertlist(dbid);
                    if(localTick == false)
                        socket.emit('chkactivitynotification', uniqueIDSocial,clientID);
                }
                else if(shareType=='facebook' && response.failed== true)
                {
                    facebookLogin ='<a href="/social/facebook" class="shareAllSocials fbAllShare">\
                          <div class="signwithSprite signWithSpriteFb">\
                            <i class="fa dbFacebookIcon fa-5x"></i>\
                            <span>Facebook</span>\
                          </div>\
                            </a>';
                    $.dbeePopup(facebookLogin);
                }else if(response.status=='error')
                {
                    $dbConfirm({
                        content: response.content,
                        yes: false,
                        error: true
                    });
                    $('.fa-spin').remove();
                    Tthis.addClass('InviteSocial');
                    return false;
                }
            }
        });
    });

     $('body').on('touchstart click', '#expertlistuser .flex-control-thumbs li img', function(){
        $(this).closest('ol').find('.activeExpert').removeClass('activeExpert');
       $(this).closest('li').addClass('activeExpert');
     });

   
    
    var hastag = $(location).attr('href');
    hastag = hastag.split('#');
    if (typeof(hastag[1])!= 'undefined' && hastag[1]!='') 
        activeTAB = hastag[1];

});


function getexpertremove(dbid,expert_id) {
    ddbid = $('#dbid').val();
    if(dbid == ddbid){
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {
                dbeeid: dbid
            },
            url: BASE_URL + '/ajax/rightpart',
            cache: false,
            success: function(response) 
            {
                if (response.status == 'success') 
                { 
                    if (response.html.twittertag != '') 
                    {
                        $('.rightSideListTwitter').html(response.html.twittertag);
                        $('body').append(twitterFeedIcon);
                        $('html').addClass('twitterFeedIconIsOn');
                        $('#alltweet .twTagsTab li:first').trigger('click');
                    }
                    if (response.html.expert != '') 
                    {
                        $('.rightSideListExpert').html(response.html.expert);
                        $('.expertIconOnMobile').remove();
                        $('body').append(expertIcon);
                        $('.asktoquestion').html(response.html.askquestion);
                        if(response.html.countExpert!=1){
                            $('#expertlistuser').flexslider({
                              animation: "slide",
                              controlNav: "thumbnails",
                              slideshow: false, 
                              directionNav: false
                            });

                        }else
                        {
                            $('#expertlistuser .whiteBox').css("padding-top", "0px"); 
                            $('#expertlistuser').flexslider({
                              animation: "slide",
                              slideshow: false, 
                              directionNav: false 
                            });
                        }
                    }else{
                        $('.rightSideListExpert').html("");
                    }
                    $('.invtExpert').html(response.html.expertLink);  
                }else{
                        $('.rightSideListExpert').html("");
                    }
                               
            }
        });
    }
}
var clockExpert;
function getexpertAsk(dbid) 
{
    ddbid = $('#dbid').val();
    if(dbid == ddbid){
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {
                dbeeid: dbid
            },
            url: BASE_URL + '/ajax/rightpart',
            success: function(response) 
            {
                if (response.status == 'success') 
                { 
                    if (response.html.twittertag != '') 
                    {
                        $('.rightSideListTwitter').html(response.html.twittertag);
                        $('body').append(twitterFeedIcon);
                        $('html').addClass('twitterFeedIconIsOn');
                        $('#alltweet .twTagsTab li:first').trigger('click');
                    }
                    if (response.html.expert != '') 
                    {
                        $('.rightSideListExpert').html(response.html.expert);
                        $('.expertIconOnMobile').remove();
                        if(response.html.diff!=0)
                        {
                            clockExpert = $('.expertQAENDs').FlipClock(response.html.diff, {
                              clockFace: 'DailyCounter',
                              countdown: true
                            });

                            $('.expertQAEND').show();

                        }else{
                            $('.expertQAEND').hide();
                        }
                        $('body').append(expertIcon);
                        if(response.html.countExpert!=1){
                            $('#expertlistuser').flexslider({
                              animation: "slide",
                              controlNav: "thumbnails",
                              slideshow: false, 
                              directionNav: false
                            });

                        }else
                        {
                            $('#expertlistuser .whiteBox').css("padding-top", "0px"); 
                            $('#expertlistuser').flexslider({
                              animation: "slide",
                              slideshow: false, 
                              directionNav: false 
                            });
                        }
                    }

                }             
            }
        });
    }
}
function getexpert(dbid,expert_id) {
    ddbid = $('#dbid').val();
    if(dbid == ddbid){
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {
                dbeeid: dbid
            },
            url: BASE_URL + '/ajax/rightpart',
            cache: false,
            success: function(response) 
            {

                if (response.status == 'success') 
                { 
                    if (response.html.twittertag != '') 
                    {
                        $('.rightSideListTwitter').html(response.html.twittertag);
                        $('body').append(twitterFeedIcon);
                        $('html').addClass('twitterFeedIconIsOn');
                        $('#alltweet .twTagsTab li:first').trigger('click');
                    }
                    if (response.html.countExpert != 0) 
                    {
                        $('.rightSideListExpert').html(response.html.expert);
                        $('.expertIconOnMobile').remove();
                        $('body').append(expertIcon);
                        $('.asktoquestion').html(response.html.askquestion);
                        if(response.html.countExpert!=1){
                            $('#expertlistuser').flexslider({
                              animation: "slide",
                              controlNav: "thumbnails",
                              slideshow: false, 
                              directionNav: false
                            });

                        }else
                        {
                            $('#expertlistuser .whiteBox').css("padding-top", "0px"); 
                            $('#expertlistuser').flexslider({
                              animation: "slide",
                              slideshow: false, 
                              directionNav: false 
                            });
                        }
                    }
                    $('.invtExpert').html(response.html.expertLink);                    
                    if(parseInt(response.html.Loginid)!=parseInt(expert_id))
                    {
                        setTimeout(function() 
                        {                 
                            $('.expertOverfade').show();
                            $('#expertlistuser').addClass('active');
                        }, 1000);
                    }
                }
                               
            }
        });
    }
}
function pushNewQuestion(dbid,qaid) 
{   
    busy = false;
    $('#pendingQACount').html('').css({display:'none'});
    $.ajax({
        type: "POST",
        dataType: 'json',
        data: {
            'qaid': qaid,'dbid':dbid
        },
        url: BASE_URL + '/expert/pendingquestion',
        success: function(response) 
        {
            if(response.status=='success')
            {
                $('.noFound').remove();
                $("#dbee-expert-question").prepend(response.content);
                busy = true;
            }
        }
    });
}

function pushNewQA(dbid,qaid) 
{
    busy = false;
    $('#LiveQACount').html('').css({display:'none'});
    $.ajax(
    {
        type: "POST",
        dataType: 'json',
        data: {
            'qaid': qaid,'dbid':dbid
        },
        url: BASE_URL + '/expert/livequestion',
        success: function(response) 
        {
            if(response.status=='success')
            {
                $('.noFound').remove();
                $("#dbee-expert-question").prepend(response.content);
                busy = true;
            }
        }
    });
}

function pushMakeLiveQA(dbid,qaid) 
{
    busy = false;
    $('#publicQaCount').html('').css({display:'none'});
    $.ajax(
    {
        type: "POST",
        dataType: 'json',
        data: {
            'qaid': qaid,'dbid':dbid
        },
        url: BASE_URL + '/expert/makelivequestion',
        success: function(response) 
        {
            if(response.status=='success')
            {
                $('.noFound').remove();
                $("#dbee-expert-question").prepend(response.content);
                busy = true;
            }
        }
    });
}
function pendingquestion(page)
{
    busy = false;
    $('#pendingQACount').html('').css({display:'none'});
    $.ajax({
        type: "POST",
        dataType: 'json',
        data: {
            'dbid': dbid,'page':page
        },
        url: BASE_URL + '/expert/pendingquestion',
        success: function(response) 
        {
            if(response.status=='success')
            {
                QaActiveTagPage = QaActiveTagPage+1;
                $('.more').remove();
                $("#dbee-expert-question").append(response.content);
                busy = true;
            }
        }
    });
}

function promotedquestion(page)
{
    busy = false;
    $('#promotedQACount').html('').css({display:'none'});
    $.ajax({
        type: "POST",
        dataType: 'json',
        data: {
            'dbid': dbid,'page':page
        },
        url: BASE_URL + '/expert/promotedquestion',
        success: function(response) 
        {
            if(response.status=='success')
            {
                 $('.more').remove();
                 QaActiveTagPage = QaActiveTagPage+1;
                 $("#my-dbees").append(response.content);
                busy = true;
            }
        }
    });
}

function getpromotedquestion(qaid,callfrom)
{
    busy = false;
    $.ajax({
        type: "POST",
        dataType: 'json',
        data: {'qaid': qaid,'callfrom':callfrom},
        url: BASE_URL + '/expert/promotedquestion',
        success: function(response) 
        {
            if(response.status=='success' && callfrom!='socket')
            {   
                 $('.more').remove();
                 $("#comment-block-question-"+qaid).html(response.content);
                 busy = true;
            }else if(response.status=='success' && callfrom =='socket')
            {
                if($('.CountQA').html()=='')
                    countp = 1;
                else
                    countp = parseInt($('.CountQA').html())+1;
                 
                $('.CountQA').html(countp).show();
            }
        }
    });
}

function myquestion(page)
{
    busy = false;
    $('#pendingQACount').html('').css({display:'none'});
    $.ajax({
        type: "POST",
        dataType: 'json',
        data: {
            'dbid': dbid,'page':page
        },
        url: BASE_URL + '/expert/myquestions',
        success: function(response) 
        {
            if(response.status=='success')
            {
                QaActiveTagPage = QaActiveTagPage+1;
                $('.more').remove();
                $("#dbee-expert-question").append(response.content);
                busy = true;
            }
        }
    });
}

function livequestion(page)
{
    busy = false;
    $('#LiveQACount').html('').css({display:'none'});
    $.ajax({
        type: "POST",
        dataType: 'json',
        data: {
            'dbid': dbid,'page':page
        },
        url: BASE_URL + '/expert/livequestion',
        success: function(response) 
        {
            if(response.status=='success')
            {
                QaActiveTagPage = QaActiveTagPage+1;
                $('.more').remove();
                $("#dbee-expert-question").append(response.content);
                busy = true;
            }
        }
    });
}


function makelivequestion(page)
{
    busy = false;
    $('#publicQaCount').html('').css({display:'none'});
    $.ajax({
        type: "POST",
        dataType: 'json',
        data: {
            'dbid': dbid,'page':page
        },
        url: BASE_URL + '/expert/makelivequestion',
        success: function(response) 
        {
            if(response.status=='success')
            {
                QaActiveTagPage = QaActiveTagPage+1;
                $('.more').remove();
                $("#dbee-expert-question").append(response.content);
                busy = true;
            }
        }
    });
}

function showQaTab(tab,showcount)
{   
    active = '';
    busy = false;
    if($('li a').hasClass('publicQa') && $('.publicQa').hasClass('active') && showcount!='none')
    {
        active = 'publicQa';
    }
    else if($('li a').hasClass('Pendingquestion') && $('.Pendingquestion').hasClass('active') && showcount!='none')
    {
        active = 'Pendingquestion';
    }
    else if($('li a').hasClass('Myquestion') && $('.Myquestion').hasClass('active') && showcount!='none')
    {
        active = 'Myquestion';
    }
    else if($('li a').hasClass('ShowMyquestion') && $('.ShowMyquestion').hasClass('active') && showcount!='none')
    {
        active = 'ShowMyquestion';
    }
    dbid = $('#dbid').val();
    $.ajax({
        type: "POST",
        dataType: 'json',
        data: {
            'dbid': dbid
        },
        url: BASE_URL + '/expert/makeqatab',
        success: function(response) 
        {
            if(response.status=='success')
            { 
                $('.noMoreComments').remove();
                $(".tabing").html(response.content).show();
                if(active!='')
                {
                   if($('li a').hasClass(active)){
                        $('.tabing ul li a').removeClass('active');
                    }
                    $('.'+active).addClass('active');
                }
                if(tab=='myquestion')
                    $('.Myquestion').trigger('click');
                else if(tab=='pendingquestion')
                    $('.Pendingquestion').trigger('click');
                else if(tab=='publicQa')
                    $('.publicQa').trigger('click');
                else
                    $('.Pendingquestion').trigger('click');


                if(showcount=='liveqa')
                {
                     preCount = $.trim($('#LiveQACount').html());
                    if(preCount=='')
                        preCount = 0;
                    count = parseInt(parseInt(preCount)+1);
                    $('#LiveQACount').text(count).css({display:'inline-block'});
                }

                if(showcount=='pending')
                {
                    preCount = $.trim($('#pendingQACount').html());
                    if(preCount=='')
                        preCount = 0;
                    count = parseInt(parseInt(preCount)+1);
                    $('#pendingQACount').text(count).css({display:'inline-block'});
                }

                if(showcount=='makelive')
                {
                    preCount = $.trim($('#publicQaCount').html());
                    if(preCount=='')
                        preCount = 0;
                    count = parseInt(parseInt(preCount)+1);
                    $('#publicQaCount').text(count).css({display:'inline-block'});
                }
            }
            /*if(activeTAB!='')
               $('#repliesQA a[data-tab="'+activeTAB+'"]').trigger('click');*/

            if(response.Type==20 && SESS_USER_ID==adminID && response.expertLinkPopup==true)
            {
                $.dbeePopup('<h2>Invite '+$('#expertText').val()+'</h2><div id="expertLink" class="clearfix">'+response.expertLink+'</div>',{closeBtnHide:true});
                $.dbeePopup('resize');
            }
            busy = true;
        }        
    });
}

function showrightPart(dbeeid){
    $.ajax({
            type : "POST",
            dataType : 'json',
            data:{dbeeid:dbeeid},
            url : BASE_URL + '/ajax/postcreater',
            cache : false,      
            success : function(response) {
              if(response.status=='success')
              {
                  if(response.html.postcreater!='')
                  {
                    $('#postcreater').append(response.html.postcreater);
                    $('.invtExpert').html(response.html.expertLink);
                  }
               }
             }
          });
}

function getexpertlist(dbid) {

        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {
                dbeeid: dbid
            },
            url: BASE_URL + '/ajax/rightpart',
            cache: false,
            success: function(response) 
            {
                if (response.status == 'success') 
                {
                    if (response.html.slideshare != '')
                        $('.rightSideList').append(response.html.slideshare);

                    if (response.html.attendies != '') 
                    {
                        $('.rightSideList').append(response.html.attendies);

                        if(response.html.countAten!=0 && response.html.countAten>5)
                        $('.attendeesBox').flexslider({
                            animation: "slide",
                            animationLoop: false,
                            itemMargin: 5,
                            slideshow: false,
                            controlNav: false,
                            prevText: '',
                            nextText: ''
                        });
                        $dbTip();

                    }
                    if (response.html.twittertag != '') 
                    {
                        $('.rightSideListTwitter').html(response.html.twittertag);
                        $('body').append(twitterFeedIcon);
                        $('html').addClass('twitterFeedIconIsOn');
                        $('#alltweet .twTagsTab li:first').trigger('click');
                    }
                    if (response.html.expert != '') 
                    {
                        $('.rightSideListExpert').html(response.html.expert);
                        $('.expertIconOnMobile').remove();
                        $('body').append(expertIcon);
                        $('.asktoquestion').html(response.html.askquestion);
                        if(response.html.countExpert!=1)
                        {
                            $('#expertlistuser').flexslider({
                              animation: "slide",
                              controlNav: "thumbnails",
                              slideshow: false,                               
                              directionNav: false,
                              smoothHeight:true,                              
                              useCSS:false,
                              start:function (){
                                $('#expertlistuser .flex-control-thumbs li:first img').trigger('click');                                
                                $('#expertlistuser').find('.flex-viewport').css('height','');                                 
                              }
                            });
                        }
                        else
                        {
                            $('#expertlistuser .whiteBox').css("padding-top", "0px"); 
                            $('#expertlistuser').flexslider({
                              animation: "slide",
                              slideshow: false, 
                              directionNav: false,                                                             
                            });
                        }
                    }
                    $('.invtExpert').html(response.html.expertLink);
                    if (response.html.postTag != '') 
                    {
                        $('.rightSideList').append(response.html.postTag);
                        $('.hashTagresult').show();
                        $('.hashTagresult').addClass('popularTagsListing');
                        $('.popularTagTitle').show();
                        $('.popularTagsListing .hasTagCloud').each(function(index, el) {
                            $(this).css({
                                //fontSize: Math.floor((Math.random() * 30) + 10)
                            })
                        });
                        if (response.html.postTag == '')
                            $('.popularTagsListing').hide();
                    }
                }
            }
        });
}

