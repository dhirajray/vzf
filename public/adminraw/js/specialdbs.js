$(function() {
    $('body').on('click', 'input.checkAllUser', function() {

        if ($(this).is(':checked') == true) {
            $('#userInfoContainer .boxFlowers:visible input[type="checkbox"]').attr('checked', true);
        } else {
            $('#userInfoContainer .boxFlowers:visible input[type="checkbox"]').attr('checked', false);
        }
        var checkNumber = $('#userInfoContainer input[type="checkbox"]:checked').length;
        if (checkNumber != 0) {
            $('#totalCheckUserPop').fadeIn();
        } else {
            $('#totalCheckUserPop').fadeOut();
        }
        $('#totalCheckUserPop span').html(checkNumber);

    });

    $('body').on('click', '.resetform', function() {
        $('#yt_url').val('');
        $('#yt_des').val('');
        $('#slideshare').val('');
        $('#eventstart').val('');
        $('#DbeeID').val('');
        $('#specialdbsubmit').val(' Save ');
        //$('#searchContainer').css('display','none');
    });

    $('body').on('click', '.labelCheckbox input', function() {
        var checkNumber = $('#userInfoContainer input[type="checkbox"]:checked').length;
        if (checkNumber != 0) {
            $('#totalCheckUserPop').fadeIn();
        } else {
            $('#totalCheckUserPop').fadeOut();
        }
        $('#totalCheckUserPop span').html(checkNumber);

    });
    $("#eventstart").datetimepicker({
        dateFormat: 'yy-mm-dd',
        minDate: 0
    });
    $("#eventend").datetimepicker({
        dateFormat: 'yy-mm-dd',
        minDate: 0
    });



    $saveVideoPost = function(fileresult) {
      
        // end  video file upload
        formdata = $('form#specialdb').serialize();
        
        filename = $('#filename').val();
        
        if(fileresult!='')
        {
          formdata = formdata + '&eventend=' + fileresult.eventend+'&filename=' + fileresult.file+ '&youtube=false';
        }
        else if(filename!='' && filename!=undefined){
          formdata = formdata + '&youtube=false';
        }else{
          formdata = formdata+'&youtube=true';
        }
        
        DbeeID = $('#DbeeID').val();

        yt_url = $('#yt_url').val();
        yt_des = $('#yt_des').val();
        eventstart = $('#eventstart').val();
        if(yt_url!='' && yt_des!='' && eventstart!='')
        {
            if (DbeeID != '') 
            {
                var thisEl = $(this);
                var url = BASE_URL + '/admin/dashboard/editvideodb';
                var redirectUrl = BASE_URL + '/admin/dashboard/specialdbs';
                postdata = formdata + '&DbeeID=' + DbeeID;
                $messageSuccess('Updated successfully');

            } 
            else 
            {
                var thisEl = $(this);
                var url = BASE_URL + '/admin/dashboard/createdb';
                var redirectUrl = BASE_URL + '/admin/dashboard/specialdbs/task/create/';
                postdata = formdata;
                $messageSuccess('Video broadcast added successfully');
            }
            $.ajax({
                url: url,
                data: postdata,
                method: 'POST',
                type: 'json',
                success: function(data) 
                {
                    if (localTick == false)
                        socket.emit('checkdbee', true, clientID);
                   location.href = redirectUrl;
                }
            });
        }
    }

    $('#specialdb').submit(function() {
        // video file upload
        filename = $('#filename').val();
        if(filename!='' && filename!=undefined){
            uploadVideo = false;
        }
        if(uploadVideo==true)
            VideoDropzone.processQueue();
        else
            $saveVideoPost('');
        return false;
    });

       $('body').on('click', '.updateSpecialDbee', function(e) {

        $('html, body').animate({
            scrollTop: $("body").offset().top
        }, 800);
        var videoid = $(this).attr('videoid');
        data = "id=" + videoid;
        url = BASE_URL + "/admin/dashboard/specialdbsedit";
        $.ajax({
            url: url, //the script to call to get data          
            data: data,
            dataType: 'json', //you can insert url argumnets here to pass to api.php,
            type: 'post',
            beforeSend: function() {
                $loader();
            }, //data format    
            success: function(data) //on recieve of reply
            {
                $removeLoader();
                if(data.result.VidSite=='youtube')
                {
                    youTubeVideo();
                    $('#yt_url').val(data.result.VidID);
                    $('#videotype').val(1).attr('disabled',true);
                }else if(data.result.VidSite=='dbcsp')
                {
                    videoUploader('videopost');
                    $('#VideoTitle').val(data.result.Text).attr('disabled',true);
                    $('#videotype').val(2).attr('disabled',true);
                    
                    var html = '<div class="searchField videoChoosed">\
                                            <label class="label"></label>\
                                            <div class="fieldInput">\
                                                <div class="acntImgThumb">\
                                                     <div id="newpic" class="proPic">\
                                                         <img src="'+BASE_URL+'/timthumb.php?src=/adminraw/knowledge_center/video_'+clientID+'/'+data.result.Vid+'.jpg&amp;q=100&amp;w=130" class="imgStyle ">\
                                                     </div>\
                                                </div><input type="hidden" value="'+data.fileInfo.duration+'" id="eventend" name="eventend"><input type="hidden" value="'+data.result.Vid+'" id="filename" name="filename">\
                                                <div class="videoChoosedTitle">'+data.fileInfo.videotitle+'</div>\
                                            </div>\
                                    </div>';
                    $('.uploadVideoTemp').append(html); 
                }
                $select('#videotype');
                
                $('#yt_des').val(data.result.VidDesc);
                $('#eventstart').val(data.videoTime);
                $('#timezoneevent').val(data.result.eventzone);

                $('#eventtype').val(data.result.eventtype);
                $select('#eventtype');

                if (data.result.commentduring == 1)
                    $('#commentduring').attr('checked', true);

                if (data.result.notification == 1)
                    $('#notification').attr('checked', true);

                $('#editvideoPost').val(1);
                $('#DbeeID').val(data.result.DbeeID);

                //$('#specialdbsubmit').val(' +Edit ');
                $('#searchContainer').show();

            }
        });

    });

    $('body').on('click', '#viewmorespecialdb', function() {
        var thisEl = $(this);
        var cnt = $(this).attr('offset');

        data = "cnt=" + cnt;
        url = BASE_URL + "/admin/dashboard/specialdbsreload";
        $.ajax({
            url: url,
            data: data,
            type: 'post',
            beforeSend: function() {
                $loader();
            },
            success: function(data) {
                $removeLoader();
                $('#vewpagebottom').remove();
                $('#searchresulthide').append(data);
            }
        });

    });
    $('body').on('click', '.resetValueUpload', function(event) {
        $(this).closest('.appendType').find('input').val('');
        $("#addpdflink").show();
        $(".orSpan").show();
    });
    $('body').on('keyup', '#pdflink', function() {
        if ($(this).val() != '') {
            $("#uploadpdf").hide();
            $(".orSpan").hide();
        } else {
            $("#uploadpdf").show();
            $(".orSpan").show();
        }
    });

    $('input[type=file]').change(function() {   
        $("#addpdflink").hide();
        $("#searchWrapper .orSpan").hide();
    });

    $('body').on('click', '#viewmoresurvey', function() {
        var thisEl = $(this);
        var cnt = $(this).attr('offset');

        data = "start=" + cnt;
        url = BASE_URL + "/admin/survey/index";
        $.ajax({
            url: url,
            data: data,
            type: 'post',
            beforeSend: function() {
                $loader();
            },
            success: function(data) {
                $removeLoader();
                $('#vewpagebottom').remove();
                $('#searchresulthide').append(data);
            }
        });

    });
    var totalAns = 1;
    $('body').on('click', '.addsurveyQuestion', function() {
        var thisEl = $(this);
        var dbeeid = $(this).attr('dbeeid');
        var status = $(this).attr('status');
        var title = $(this).attr('data-title');
        var totalQuestion = parseInt($(this).attr('count-id'));

        totalAns = 1;
        if (status == 1) {
            var htmlLightbox = '<div id="detailsDialog" data-pop="delete">\
           <div class="dbDcenter">To add a new question you must unpublish this survey\ first. Do you wish to continue?\
</div></div>';
            $('#detailsDialog').remove();
            $('body').append(htmlLightbox);
            $("#detailsDialog[data-pop='delete']").dialog({
                width: 300,
                title: 'Confirmation',
                modal: true,
                draggable: false,
                buttons: {
                    "Yes": function() {
                        $.ajax({
                            url: BASE_URL + '/admin/survey/publishsurvey',
                            type: 'POST',
                            data: {
                                "surveyid": dbeeid,
                                'status': 1
                            },
                            async: false,
                            success: function(responce, textStatus, jqXHR) {
                                $messageSuccess(responce.message);
                                addQuestion(dbeeid, title, totalQuestion);
                                $('#addsurveyQuestion' + dbeeid).attr('status', 0);
                                $('#publish' + dbeeid).attr('status', 0);
                                $('#publish' + dbeeid).html('Publish');
                                if (localTick == false)
                                    socket.emit('checkdbee', true, clientID);
                            }
                        });
                        $(this).dialog("close");
                    }
                }
            });
        } else {
            addQuestion(dbeeid, title, totalQuestion);
        }
    });


    $('body').on('keypress', '#invietuserdbee', function(e) {
        thisval = $(this);
        keyword = $.trim($(this).val());
        if (e.which == 13) {
            if (keyword.length >= 2) {
                data = {
                    'keyword': keyword,
                    'type': 'video'
                };
                $.ajax({
                    url: BASE_URL + "/admin/usergroup/dbeeuser",
                    data: data,
                    dataType: 'json',
                    type: "POST",
                    beforeSend: function() {
                        $('.ui-dialog-content').append('<div class="loaderOverlay2"></div>');
                    },
                    success: function(responce) {
                        $('.loaderOverlay2').remove();
                        $('#userInfoContainer').html(responce.content);

                        $('#userInfoContainer').append("<input id='checktbltwittertaguser' name='checktbltwittertaguser' type='hidden' value='checkplateformuser' ></p>");
                        $('#userInfoContainer').flexslider({
                            animation: "slide",
                            animationLoop: false,
                            itemWidth: 140,
                            itemMargin: 2
                        });
                    }
                });

                $(this).removeClass('error');
                return false;

            } else {
                $messageError('Please type minimum 2 characters')
            }
        }


    });

    // add questions
   
    var addQuestion = function(surveyId, title, totalQuestion) {

        var htmlLightbox = '<div id="detailsDialog" data-id="' + surveyId + '" >\
                                    <div id="datacollect" style="float:none"></div>\
                                    <div id="userInfoContainer"></div>\
                                  </div>';
        var currentQuestion = parseInt(totalQuestion) + 1;
        var AddQuestion = currentQuestion + 1;
        var completeSurvey = false;
        var htmlcontent = '<form name="surveyquestion" id="surveyquestion"><h2>Question ' + currentQuestion + '</h2>\
               <textarea name="question" id="question" placeholder="Add question" class="fluid"></textarea>\
               <div class="row"><div class="cortAnsLabel">If you wish to assess the survey results please mark an answer as \'correct\'</div><div class="correctCheck">Correct</div><input type="radio" style="left:-px;" value="0" id="correct_answer_0" name="correct_answer" class="cortRadio"><label for="correct_answer_0"></label><input type="text" id="ans_0" name="answer[]" class="answer fluid addMoreInput" value="" placeholder="Add answer"/><button class="btn btn-black pull-left addMoreAnswers" type="button"><i class="fa fa-plus"></i> Add next answer</button></div></form>';
        $("#detailsDialog").remove();
        $('body').append(htmlLightbox);

        $("#detailsDialog").dialog({
            width: 800,
            height: 450,
            dialogClass: "suryveDialogbox",
            title: 'Add questions to <font color="#FFA439">' + title + '</font>',
            modal: true,
            draggable: false,
            open: function() {
                $fluidDialog();
                $('.loaderOverlay2').hide();
                $('#userInfoContainer').html(htmlcontent);
                $('.suryveDialogbox .ui-dialog-buttonpane button:first span').text('Add question ' + AddQuestion);
                $('.suryveDialogbox .ui-dialog-buttonpane button:first').after('<span class="orTxtButton">Or</span>');

            },
            buttons: {
                "Add question": function() {
                    $('.ui-dialog-buttonset')
                    var dbeeid = $('#detailsDialog').attr('data-id');
                    var formEl = $('form#surveyquestion');
                    var errorAnsEmpty = false;
                    var thisPop = $(this);
                    var numItems = $(':radio[name="correct_answer"]:checked, :radio[name="correct_answer"]:checked').length;
       
                    var totalradio = $('.cortRadio').length;
                    for (var i = 0; i < totalradio; i++) {
                        if($('#correct_answer_' + i).is(":checked")) {
                            $('#correct_answer_' + i).val($('#ans_' + i).val());
                        }
                    };

                    formdata = formEl.serialize() + '&dbeeid=' + dbeeid;
                    totalAns = 1;
                    var totalAnsA = $('.row', formEl).size();
                    var Qvalue = $('textarea', formEl).val();
                    if (Qvalue == '') {
                        $messageError('Please enter question');
                        $('textarea', formEl).focus();
                        return false;
                    }


                    $('input', formEl).each(function(index, el) {
                        var AnsValue = $(this).val();
                        $(this).focus();
                        if (AnsValue == '') {
                            $messageError('Please enter answer');
                            errorAnsEmpty = true;
                        }

                    });

                    if (errorAnsEmpty == true) {
                        return false;
                    }


                    if (totalAnsA < 2) {
                        $messageError('Please add at least 2 answers');
                        return false;
                    }

                    $.ajax({
                        url: BASE_URL + "/admin/survey/createsurveyquestion",
                        data: formdata,
                        type: "POST",
                        success: function(result) {
                            if (result.status == 'success') {
                                $messageSuccess(result.message);
                                $('#userInfoContainer').html(htmlcontent);
                                $('.addsurveyQuestion[dbeeid="' + surveyId + '"]').attr('count-id', result.count);
                                $('#surveyquestion h2').text('Questions: ' + (result.count + 1));
                                $('#QuestionsCount' + surveyId).text('Questions: ' + (result.count));

                                if (completeSurvey == true) {
                                    thisPop.dialog("close");
                                } else {
                                    $('#surveyDetailsPage').append(result.content);
                                    $('.suryveDialogbox .ui-dialog-buttonpane button:first span').text('Add question ' + (result.count + 2));
                                }
                                if ($('div').hasClass('questionlistitem'))
                                    surveyQuestion(surveyId);

                                if (localTick == false)
                                    socket.emit('checkdbee', true, clientID);

                            } else {
                                $messageError(result.message);
                            }

                        }
                    });
                },
                "Complete survey": function() {
                    completeSurvey = true;
                    $('.suryveDialogbox .ui-dialog-buttonpane button:first').trigger('click');
                }
            }

        });


    }

    $('body').on('click', '.addMoreAnswers', function(event) {

        //$('.removeMoreAnsRow').remove();
        var htmlAddmoreAns = '<div class="row"><input type="radio" value="' + totalAns + '" id="correct_answer_' + totalAns + '" name="correct_answer" class="cortRadio"><label for="correct_answer_' + totalAns + '"></label><input type="text" id="ans_' + totalAns + '" name="answer[]" class="answer fluid addMoreInput" value="" placeholder="Add answer"/><label><button class="btn pull-left removeMoreAnsRow" type="button"><i class="fa fa-minus"></i></button></div>';
        $(this).closest('#surveyquestion').append(htmlAddmoreAns);
        totalAns++;

        $('.removeMoreAnsRow').click(function() {
            $(this).closest('.row').fadeOut(function() {
                totalAns--;
                $(this).remove();
            });
        });
    });
    // end add questions

    $("#serverydb").submit(function(e) {

        var surveyTitle = $('#surveyTitle').val();
        var submitBtn = $('#specialdbsubmit');
        if (surveyTitle == '') {
            $messageError('Please enter your survey title');
            return false;
        } else {
            submitBtn.addClass('processBtnLoader').append(' <i class="fa fa-spinner fa-spin"></i>').attr('disabled', 'disabled');
            var formObj = $(this);
            var formURL = formObj.attr("action");
            var formData = new FormData(this);
            $.ajax({
                url: formURL,
                type: 'POST',
                data: formData,
                mimeType: "multipart/form-data",
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function(response) {
                    if (response.status == 'success') {

                        submitBtn.removeAttr('disabled').removeClass('processBtnLoader');
                        $('i', submitBtn).remove();
                        //$('#specialdbsubmit .fa').remove();
                        $messageSuccess(response.message);
                        $('#surveyTitle').val('');
                        $('#serverPDF').val('');
                        $('.uploadType').val('');
                        $('#searchContainer').css('display', 'none');
                        $.ajax({
                            //url: BASE_URL+"/admin/survey/surveyreload",                   
                            url: BASE_URL + "/admin/survey/index",
                            type: 'post',
                            beforeSend: function() {
                                $loader();
                            },
                            success: function(data) {
                                $removeLoader();
                                $('#searchContainer').css('display', '');
                                $('#vewpagebottom').remove();
                                $('#searchresulthide').html(data);

                                $scrolling('#searchresulthide');
                            }
                        });


                        addQuestion(response.surveyid, response.surveyTitle, 0);

                    } else {
                        $messageError(response.message);
                    }
                }

            });
            e.preventDefault(); //Prevent Default action.
        }
    });

    $('#addNewFile, .cancelUploadFile').click(function() {
        $('.addPdfFileWrapper').slideToggle();
    });

    $('.surveyquestionedit').click(function() {
        $('.edittext').hide();
        var id = $(this).attr('data-id');
        $('#' + id).show();
    });


    $(".specialdbsubmitedit").on('click', function(e) {

        var id = $(this).attr('form-id');
        formdata = $('form#surveyquestion-' + id).serialize();
        yt_url = $('#yt_url').val();
        yt_des = $('#yt_des').val();
        eventstart = $('#eventstart').val();
        if(yt_url!='' && yt_des!='' && eventstart!='')
        {
            $.ajax({
                url: BASE_URL + '/admin/survey/editsurvey',
                type: 'POST',
                data: formdata,
                async: false,
                success: function(responce, textStatus, jqXHR) {
                    $messageSuccess(responce.message);
                }

            });
         }
        e.preventDefault(); //Prevent Default action.
    });


    var editQA = false;
    var dataIdQuetion = '';



    $('.surveyTitlEdit').toggle(function() {
        var thisBtn = $(this);
        thisBtn.removeClass('btn-black').addClass('btn-green').text('Save');
        var titleEdit = $.trim(thisBtn.next('ul').find('.surveyDetailsTitle').text());
        thisBtn.next('ul').find('.surveyDetailsTitle').replaceWith('<textarea class="fluid" id="surveytitlepost">' + titleEdit + '</textarea>');
        $('#surveytitlepost').focus().select();

    }, function() {
        var thisBtn = $(this);
        var titleEdited = $.trim(thisBtn.next('ul').find('textarea').val());
        var dataID = thisBtn.closest('.surveryWrpTitle').find('li').attr('data-id');


        $.ajax({
            url: BASE_URL + '/admin/survey/editsurveytitle',
            type: 'POST',
            data: {
                "dbeeid": dataID,
                "surveytitle": titleEdited
            },
            async: false,
            success: function(responce, textStatus, jqXHR) {
                $messageSuccess(responce.message);
            }

        });

        if (titleEdited == '') {
            $messageError('survey title should not be empty');
            return false;
        }
        thisBtn.removeClass('btn-green').addClass('btn-black').text('Edit');

        thisBtn.next('ul').find('textarea').replaceWith('<div class="surveyDetailsTitle">' + titleEdited + '</div>');


    });

    $('body').on('click', '.deletesurvey', function() {
        var thisBtn = $(this);
        var htmlLightbox = '<div id="detailsDialog" data-pop="delete"><div class="dbDcenter">Are you sure you want to delete this survey?</div></div>';
        $('#detailsDialog').remove();
        $('body').append(htmlLightbox);
        $("#detailsDialog[data-pop='delete']").dialog({
            width: 300,
            title: 'Please confirm',
            modal: true,
            draggable: false,
            buttons: {
                "Yes": function() {
                    var dbeeid = thisBtn.attr('data-id');
                    $.ajax({
                        url: BASE_URL + '/admin/survey/deletesurvey',
                        type: 'POST',
                        data: {
                            "id": dbeeid
                        },
                        async: false,
                        success: function(responce, textStatus, jqXHR) {
                            $messageSuccess(responce.message);
                            $('#remove_' + dbeeid).remove();
                        }
                    });

                    $(this).dialog("close");
                }
            }

        });

    });

    $('body').on('click', '.publishSurvey', function() {
        var thisBtn = $(this);
        var status = thisBtn.attr('status');
        if (status == 0)
            statusText = 'publish';
        else
            statusText = 'unpublish';
        var htmlLightbox = '<div id="detailsDialog" data-pop="delete"><div class="">Are you sure you want to ' + statusText + ' this survey?</div></div>';
        $('#detailsDialog').remove();
        $('body').append(htmlLightbox);
        $("#detailsDialog[data-pop='delete']").dialog({
            title: 'Please confirm',
            modal: true,
            draggable: false,
            buttons: {
                "Yes": function() {
                    var dbeeid = thisBtn.attr('data-id');
                    var status = thisBtn.attr('status');
                    $.ajax({
                        url: BASE_URL + '/admin/survey/publishsurvey',
                        type: 'POST',
                        data: {
                            "surveyid": dbeeid,
                            'status': status
                        },
                        async: false,
                        success: function(responce, textStatus, jqXHR) {
                            if (responce.status == 'success') {
                                $messageSuccess(responce.message);
                                if (status == 0) {
                                    thisBtn.text('UnPublish');
                                    thisBtn.addClass('btn-danger');
                                    thisBtn.attr('status', 1);
                                    $('#addsurveyQuestion' + dbeeid).attr('status', 1);
                                    $('#' + dbeeid).hide();
                                } else {
                                    thisBtn.text('Publish');
                                    thisBtn.removeClass('btn-danger');
                                    thisBtn.attr('status', 0);
                                    $('#addsurveyQuestion' + dbeeid).attr('status', 0);
                                    $('#' + dbeeid).show();
                                }
                                if (localTick == false)
                                    socket.emit('checkdbee', true, clientID);
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

    // this is for delete question and answers
    $('body').on('click', '.questionsWrp li .plBtnRed_', function() {
        var thisBtn = $(this);
        var list = thisBtn.closest('li')
        var listID = thisBtn.closest('li').attr('data-id');

        var mainDiv = thisBtn.closest('.questionsWrp');
        var textedt = thisBtn.closest('li').find('.plBtnGreen_').text();

        if (textedt == 'Save') {
            list.removeClass('active');
            $('.editOption', list).remove();
                list.append('<div class="editOption">\
              <a class="plBtnGreen_" href="javascript:void(0);"><i class="fa fa-pencil"> </i></a>\
              <a class="plBtnRed_" href="javascript:void(0);"><i class="fa fa-times"> </i></a>\
            </div>');
                $('input[type="text"]', list).replaceWith('<strong>' +   $('input[type="text"]', list).val() + '<strong>');
           // $('.editOption', list).remove();

            return false;
        }

        var limitAnws = mainDiv.find('li').size();
        var limitMainDiv = $('.questionsWrp').size();

        if ($("#rad_" + listID).is(':checked')) {
            var message1 = '';
            var htmlLightbox1 = '<div id="detailsDialog"><div class="dbDcenter">' + message1 + '</div></div>';
            $('#detailsDialog').remove();
            $('body').append(htmlLightbox1);
            /*$( "#detailsDialog" ).dialog({
                  width:300,
                  title:'Please mark another answer as correct before deleting this answer', 
                  modal:true,
                  draggable:false
            });*/

            $messageError('Please mark another answer as correct before deleting this answer');

            return false;
        }

        if (list.hasClass('questionName') != true) {
            message = 'Are you sure you want to delete this answer?';
            if (limitAnws <= 3) {
                $messageError('Not allowed! A question should at least have 2 answer options');
                return false;
            }
        } else {
            message = 'Are you sure you want to delete this answer?';
            if (limitMainDiv == 1) {
                $messageError('it should not be remove');
                return false;
            }
        }
        var htmlLightbox = '<div id="detailsDialog"><div class="dbDcenter">' + message + '</div></div>';
        $('#detailsDialog').remove();
        $('body').append(htmlLightbox);

        $("#detailsDialog").dialog({
            width: 300,
            title: 'Confirmation',
            modal: true,
            draggable: false,
            buttons: {
                "Yes": function() {

                    if (list.hasClass('questionName') == true) {
                        mainDiv.fadeOut(function() {
                            $(this).remove();
                        });
                        type = 'question';

                    } else {
                        list.fadeOut('fast', function() {
                            $(this).remove();
                        });
                        type = 'answer';
                    }

                    $.ajax({
                        url: BASE_URL + '/admin/survey/deletesurveypost',
                        type: 'POST',
                        data: {
                            "id": listID,
                            "type": type
                        },
                        async: false,
                        success: function(responce, textStatus, jqXHR) {
                            $messageSuccess(responce.message);
                        }
                    });

                    $(this).dialog("close");
                }
            }

        });

    });
    //end this is for delete question and answers

    $('body').on('click', '.questionsWrp li .plBtnGreen_', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var thisBtn = $(this);
        thisBtn.text('Save');
        var _parent = thisBtn.closest('li');
        _parent.addClass('active');

        var data = _parent.find('strong').text();
        dataIdQuetion = _parent.attr('data-id');
        var parrentId = _parent.attr('data-parrentId');

        _parent.find('strong').replaceWith('<input type="text" name=""  value="' + data + '"/>');
        var ansradio = $("#rad_" + dataIdQuetion).prop('disabled', false);

        $("#rad_" + dataIdQuetion).focus().select();



        _parent.find('input').focus().select();

        editQA = true;

        $('.plBtnGreen_', _parent).click(function() {
            var thisBtn = $(this);
            var thisVal = $('input[type="text"]', _parent).val();
            $('input[type="text"]', _parent).replaceWith('<strong>' + thisVal + '<strong>');

            $("#rad_" + dataIdQuetion).prop('disabled', true);
            var titleEdited = thisVal;

            var correct_answer = 0;
            if ($("#rad_" + dataIdQuetion).is(':checked')) {
                correct_answer = 1;
            } else {
                correct_answer = 0;
            }

            if (editQA == true) {
                editQA = false;
                $.ajax({
                    url: BASE_URL + '/admin/survey/editsurvey',
                    type: 'POST',
                    data: {
                        "id": dataIdQuetion,
                        "text": titleEdited,
                        "correct_answer": correct_answer,
                        "parrentId": parrentId
                    },
                    async: false,
                    success: function() {
                        _parent.removeClass('active');
                        $('.editOption', _parent).remove();
                          _parent.append('<div class="editOption">\
                          <a class="plBtnGreen_" href="javascript:void(0);"><i class="fa fa-pencil"> </i></a>\
                          <a class="plBtnRed_" href="javascript:void(0);"><i class="fa fa-times"> </i></a>\
                        </div>');
                        $messageSuccess('Saved successfully');
                    }
                });

            }
        });
    });

 

    $('body').on('click', '.attendiesuser', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var DbeeID = $(this).attr('dbid');
        var type = $(this).attr('type');

        $('#detailsDialog').remove();
        //var dbid = $('#dbid').val();
        var htmlLightbox = '<div id="detailsDialog"  title="Attendees">\
                                        <div class="srcUsrWrapper">\
                                        </div>\
                                                        <div id="datacollect" style="float:none"></div>\
                                                        <div id="userInfoContainer"></div>\
                                                    </div>';
        $('body').append(htmlLightbox);

        $("#detailsDialog").dialog({
            dialogClass: 'detailsDialogBox',
            width: 800,
            height: 500,
            open: function(event, ui) {
                $('html').attr('dbid', $(document).scrollTop()).css('overflow', 'hidden');
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
                    url: BASE_URL + '/admin/dashboard/attendiesuserlist',
                    data: {
                        'id': DbeeID,
                        'type': type
                    },
                    success: function(response) {
                        $('.loaderOverlay2').remove();
                        $('.ui-dialog-title').after(response.content2);
                        $('#userInfoContainer').html(response.content);

                        $('#attendisListTarget').click(function() {
                            status = $(this).is(":checked");
                            $.ajax({
                                type: "POST",
                                dataType: 'json',
                                url: BASE_URL + '/admin/dashboard/hideattendieslist',
                                data: {
                                    'dbeeID': DbeeID,
                                    'status': status
                                },
                                success: function(response) {
                                    $messageSuccess(response.message);
                                }
                            });
                        });

                    }
                });
            },
            close: function(event, ui) {
                var scrollTop = $('html').css('overflow', 'auto').attr('data-scrollTop') || 0;
                if (scrollTop) $('html').scrollTop(scrollTop).attr('data-scrollTop', '');
            }
        });
    });
    
    $('body').on('click', '.reqtojoinuser', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var DbeeID = $(this).attr('dbid');
        var type = $(this).attr('type');
        $('#detailsDialog').remove();
        var htmlLightbox = '<div id="detailsDialog"  title="Users who have requested to join">\
            <div id="datacollect" style="float:none"></div>\
            <div id="userInfoContainer"></div>\
        </div>';
        $('body').append(htmlLightbox);
        $("#detailsDialog").dialog({
            dialogClass: 'detailsDialogBox',
            width: 800,
            height: 500,
            open: function(event, ui) {
                $fluidDialog();
                $("#datacollect").html('');
                $('.ui-dialog-content').append('<div class="loaderOverlay2"></div>');
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    url: BASE_URL + '/admin/dashboard/attendiesuserlist1',
                    data: {
                        'id': DbeeID,
                        'type': type
                    },
                    success: function(response) {
                        $('.loaderOverlay2').remove();
                        $('#userInfoContainer').html(response.content);
                    }
                });
            },
            buttons: {
                "Accept Request": function() {
                    var dbeeID = $('#dbeeid').val();
                    var attenusr = [];
                    var thisEl = $(this);
                    $('input:checkbox[name=attendeduser]').each(function() {
                        if ($(this).is(':checked'))
                            attenusr.push($(this).val());
                    });

                    if (attenusr.length == '')
                        $messageError('Please select a user');

                    var ateeenuserstring = attenusr.join();
                    if (attenusr.length == 0)
                        return false;

                    $.ajax({
                        type: "POST",
                        dataType: 'json',
                        data: {
                            'ateeenuserstring': attenusr,
                            'dbeeID': dbeeID
                        },
                        url: BASE_URL + '/admin/social/updatejoinrequser',
                        beforeSend: function() {
                            $(".ui-button-text-only").after('<i class="fa fa-spinner fa-spin"></i>');
                            $(".ui-button-text-only").attr('disabled', true);
                        },
                        success: function(response) {
                            $messageSuccess(response.message);
                            thisEl.dialog("close");
                            socket.emit('testvideo', dbeeID, clientID);
                            socket.emit('chkactivitynotification', true, clientID);
                            window.location.reload();
                        }
                    });
                }
            },
            close: function(event, ui) {
                var scrollTop = $('html').css('overflow', 'auto').attr('data-scrollTop') || 0;
                if (scrollTop) $('html').scrollTop(scrollTop).attr('data-scrollTop', '');
            }
        });

    });
    
    $( "body" ).on('click','.closeAuth',function() 
    {
        $('.uploadBVideo').html('');
        $('#videotype option[value="0"]').attr("selected", "selected");
        $select('select');
        $('#mesageNotfiOverlay').hide();
    });

    $( "body" ).on('change','#videotype',function() {
        type = $(this).val();
        if(type==1){
            $('#mesageNotfiOverlay').show();
            youTubeVideo();
        }
        else if(type==2){
            videoUploader('videopost');
            $('.uploadVideoTemp').removeClass('selectedFromGallery');
            $('.videoChoosed').remove();
        }else{
             $('.uploadBVideo').html('');
            $('#videotype option[value="0"]').attr("selected", "selected");
            $select('select');
        }
    });

  
  /*start surveys radio button*/
    $('body').on('click', "#surveyquestion input[type='radio']", function(){
       var previousValue = $(this).attr('previousValue');
          var name = $(this).attr('name');             
          if (previousValue == 'checked')
          {
            $(this).removeAttr('checked');
            $(this).attr('previousValue', false);
          }
          else
          {
            $("input[name="+name+"]:radio").attr('previousValue', false);
            $(this).attr('previousValue', 'checked');
          }
    });
  /*end surveys radio button*/

    
});

function surveyQuestion(dbeeid) 
{
    $.ajax({
        url: BASE_URL + '/admin/survey/ajaxsurveyquestion',
        type: 'POST',
        data: {
            "dbeeid": dbeeid
        },
        success: function(responce) {
            $('.questionlistitem').html(responce);
        }
    });
}

function youTubeVideo()
{
    $('.uploadBVideo').html('<div class="searchField">\
        <label class="label">YouTube link </label>\
        <div class="fieldInput"><input type="text" placeholder="Please ensure the YouTube video is not private" id="yt_url" name="yt_url" style="width: calc(100% - 140px); -webkit-width: calc(100% - 140px); float:left; margin-right:5px" ><a href="https://www.youtube.com" target="_blank" class="btn btn-yellow btn-small" style="float:left">\
            <i class="media_hide_480">Go to YouTube</i>\
            <i class="fa fa-youtube-play media_480"></i>\
        </a></div>\
    </div>');
}