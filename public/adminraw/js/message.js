$(function() {
    var userTypeJson = JSON.parse(jsonusertype);
    var userDtailsJson = JSON.parse(jsonuserdetails);
    callmessageaction = function(subject, message, msgTypeMethodChecked, selectTempId, msgType, groupNameSelect, offset2, thisPopup, usersearchInfo, msg_id, lastinsidfromemail, siteplateform, usertypeval, companyval, jobtitleval) {
        $.ajax({
            type: "POST",
            url: BASE_URL + "/admin/message/messageemail",
            data: {
                "subject": subject,
                "message": message,
                "msgTypeMethod": msgTypeMethodChecked,
                "selecttempId": selectTempId,
                "msg_type": msgType,
                "msg_type_id": groupNameSelect,
                "offsetrange": offset2,
                "limit": '12',
                "userlist": usersearchInfo,
                "msg_id": msg_id,
                'lastinsidfromemail': lastinsidfromemail,
                'siteplateform': siteplateform,
                'usertypeval': usertypeval,
                'companyval': companyval,
                'jobtitleval': jobtitleval
            },
            success: function(result) {
                //alert(result);return false;
                if (result == 'nouser') {
                    $messageError("this group has no members");
                    if (thisPopup != '') thisPopup.dialog("close");
                    return false;
                }

                param = result.split('~');

                $('#subjectval').val('');
                $('#messageval').val('');
                $('.token-input-token-facebook').remove();
                if (selectTempId != 0) {

                    if (param['0'] > 0) {
                        var loaderHtml = '<div class="loaderShow">\
                                                            <span class="loaderImg"></span><br>\
                                                            Sending...\
                                                        </div>\
                                                    Please do not press back or refresh button <br><strong '+param['1'] +'</strong> emails sent sucessfully - <strong>' + param['0'] +'</strong> remaining <br>';
                        $dbLoader({process:true,totalUpload:param['2'], loaderHtml:loaderHtml});
                        
                        $sendMessageEmail(subject, message, msgTypeMethodChecked, selectTempId, msgType, groupNameSelect, param['1'], '', '', param['3'], siteplateform, usertypeval, companyval, jobtitleval);
                        if (thisPopup != '') thisPopup.dialog("close");
                    } else {
                         $dbLoader({process:true,totalUpload:100, loaderHtml:'Emails sent sucessfully'});                     
                      
                        if (thisPopup != '')
                            thisPopup.dialog("close");
                        $msgtablist(msgType, selectTempId);
                        return false;

                    }
                }

                if (selectTempId == 0) 
                {
                    $messageSuccess("message sent sucessfully");
                    thisPopup.dialog("close");
                    if(localTick==false)
                        socket.emit('chkactivitynotification', 1,clientID);
                    $msgtablist(msgType, selectTempId);
                    return false;
                }

            }

        });
    }
    yesCount = 0;
    // This is for sending message and Email functions
    $sendMessageEmail = function(subject, message, msgTypeMethodChecked, selectTempId, msgType, groupNameSelect, offset2, usersearchInfo, msg_id, lastinsidfromemail, siteplateform, usertypeval, companyval, jobtitleval) {
        if (offset2 == '' || offset2 == 0) {
            var confirmD = '<div id="confirBox" title="Please confirm"><p>Are you sure you want to send this message?</p></div>';
            $('#confirBox').remove();
            $('body').append(confirmD);
            //if(yesCount==0){}
            $('#confirBox').dialog({
                minWidth: 300,
                buttons: {
                    "Yes": function() {
                        var thisPopup = $(this);

                        callmessageaction(subject, message, msgTypeMethodChecked, selectTempId, msgType, groupNameSelect, offset2, thisPopup, usersearchInfo, msg_id, lastinsidfromemail, siteplateform, usertypeval, companyval, jobtitleval);
                        $(this).dialog("close");
                    }
                }
            });
        } else {
            var thisPopup = '';
            setTimeout(function() {
                callmessageaction(subject, message, msgTypeMethodChecked, selectTempId, msgType, groupNameSelect, offset2, thisPopup, '', '', lastinsidfromemail, siteplateform, usertypeval, companyval, jobtitleval);
            }, 1000);

        }
    }

    // End this is for sending message and Email functions
    $msgtablist = function(mssageType, msgtemplateID) {
        $.ajax({
            type: "POST",
            url: BASE_URL + "/admin/message/messagelist",
            data: {
                "msg_type": mssageType,
                "emailtemplateid": msgtemplateID
            },
            success: function(result) {
                $('#userlistmsg').html(result);
                $('.copymsgemail').click(function(event) {
                    var findTr = $(this).closest('tr');
                    $('#subjectval').val(findTr.find('td:eq(0)').html());
                    $('#messageval').val(findTr.find('td:eq(1)').html());
                });
                $('.reSendBtn').click(function(event) 
                {
                    var findTr = $(this).closest('tr');
                    var subject = findTr.find('td:eq(0)').html();
                    var message = findTr.find('td:eq(1)').html();
                    var msgTypeMethodChecked = $(this).attr('msgTypeMethodResend');
                    var selectTempId = $(this).attr('emailtemplateid');
                    var msgType = $(this).attr('msg_type');
                    var msg_id = $(this).attr('msg_id');
                    var usersearchInfo = '0';
                    var groupNameSelect = $(this).attr('msg_type_id');

                    var siteplateform = $(this).attr('siteplateform');
                    //var usertypeval = usertypevalid+'~'+usertypevalval;
                    var usertypeval = $(this).attr('usertypeval');
                    var companyval = $(this).attr('companyval');
                    var jobtitleval = $(this).attr('jobtitleval');
                    //alert(siteplateform+'~~~'+usertypeval+'~~~'+companyval+'~~~'+jobtitleval);
                    $('#confirBoxdel').hide();
                    $sendMessageEmail(subject, message, msgTypeMethodChecked, selectTempId, msgType, groupNameSelect, '', usersearchInfo, msg_id, '', siteplateform, usertypeval, companyval, jobtitleval);

                });
                $('.deleteBtn').click(function(event) {
                    var selectTempId = $(this).attr('emailtemplateid');
                    var msgType = $(this).attr('msg_type');
                    var msgemailtable_id = $(this).attr('messageval');
                    $deletemsgemailall(msgemailtable_id, msgType, selectTempId);
                })
            }
        });
    }

    $deletemsgemailall = function(msgemailtableid, msgType, selectTempId) {
        var confirmDe = '<div id="confirBoxdel" title="Please confirm"><p>Are you sure you want to delete this message?</p></div>';
        $('body').append(confirmDe);
        $('#confirBoxdel').dialog({
            minWidth: 300,
            close:function(){
                 $('#confirBoxdel').remove();
            },
            buttons: {
                "Yes": function() {
                    var thisPopup = $(this);
                    $.ajax({
                        type: "POST",
                        url: BASE_URL + "/admin/message/deletemessageemail",
                        data: {
                            "msgemailtable_id": msgemailtableid
                        },
                        success: function(result) {

                            if (result == 'deltrue') {
                                $messageSuccess("Message deleted succesfully");
                                thisPopup.dialog("close");
                                $('#confirBoxdel').remove();
                                $msgtablist(msgType, selectTempId);
                                return false;
                            }

                        }
                    });
                }
            }
        });
    }




    var emailTempDrpDown = $('select[dataName="emailTemplateSelect"]').closest('li').detach();
    var siteGrpDrpDown = $('select[dataName="siteusernameval"]').closest('li').detach();
    var grpTypePublic = $('select[dataName="Public"]').closest('li').detach();
    var grpTypePrivate = $('select[dataName="Private"]').closest('li').detach();
    var grpTypeRequest = $('select[dataName="Request"]').closest('li').detach();
    var siteUserSearch = $('#myTags').closest('li').detach();

    var eventGrpDrpDown = $('select[dataName="eventeusernameval"]').closest('li').detach();

    $('#messageWrapper .dbtab a').bind('click', function(event) {
        var checkValue = $(this).attr('checkValue');
        var checkValue_exst = typeof (checkValue) != 'undefined' ? checkValue : 0;
        var forcontent = $(this).attr('forcontent');
        var msgType = $(this).attr('msgType');
        var siteGrpName = siteGrpDrpDown.html();
        var siteUserField = siteUserSearch.html();
        var eventUserField = eventGrpDrpDown.html();

        var groupTypeSelectTemp = '<div class="searchField">\
                                            <label class="label">User Group type </label>\
                                            <div class="fieldInput">\
                                                <select name="grpType" class="selectoptMsg">\
                                                    <option value="Public Group" selected="selected">Open</option>\
                                                    <option value="Private Group">Close</option>\
                                                    <option value="Request Group">Request</option>\
                                                </select>\
                                            </div>\
                                        </div>';

        // for message search
        /*var userTypeCheckBox = '<div class="usrOptionChk">\
                                        <label for="usrp" class="customchk"><input type="radio" value="0"  class="usrTypeMethod" name="usrTypeMethod" checked="checked" id="usrp"><label for="usrp"></label>Custom users</label>\
                                        <label for="usre" class="plateformchk"><input type="radio" value="1" class="usrTypeMethod" name="usrTypeMethod"  id="usre"><label for="usre"></label >Platform users</label></div>';*/
        var userTypeCheckBox  ='<div class="usrOptionChk" style="padding:0px;">';
        var valckecked; 
            if(clientID!=21 && clientID!=22){ 
            userTypeCheckBox  +='<label for="usre" class="plateformchk">\
                                 <input type="hidden" value="1" class="usrTypeMethod" name="usrTypeMethod"  id="usre" checked="checked"><label for="usre"></label ></label>';
            }
            if(clientID!=21 && clientID!=22){                        
                userTypeCheckBox  +='<label for="usrp" class="customchk">\
                                    <input type="hidden" value="0"  class="usrTypeMethod" name="usrTypeMethod"  id="usrp"><label for="usrp" '+ valckecked +'></label></label></div>'; 
            }
            if(clientID==21 || clientID==22){ 
                 userTypeCheckBox  +='<span style="display:inline-block; margin:5px 0px; color:#999; font-size:13px;">Send a message to all platform users or by individual company or job title.</span>';
                 valckecked = 'checked="checked"';
            }                                                      

        var userTypeDropDown = '<div class="msgSearchRow">\
                                        <input type="hidden" id="userTypeSearch">\
                                        </div>';

        var companyDropDown = '<div class="msgSearchRow">\
                                        <input type="hidden" id="CompanySearch">\
                                        </div>';
        var jobTitleDropDown = '<div class="msgSearchRow">\
                                        <input type="hidden" id="jobTitleSearch">\
                                        </div>';

        // end  message search
        if(clientID!=21 || clientID!=22){                          
        var actionTypeTmp = '<div class="msgOptionChk" style="padding:0px;">\
                                        <label  for="msgp"><input type="hidden" value="0"  class="msgTypeMethod" name="msgTypeMethod" checked="checked" id="msgp"><label for="msgp"></label></label>\
                                        <label for="msge"><input type="hidden" value="1" class="msgTypeMethod" name="msgTypeMethod"  id="msge"><label for="msge"></label > </label></div>';
        }if(clientID==21 || clientID==22){
         var actionTypeTmp = '<div class="msgOptionChk"  style="display:none;">\
                                        <label  for="msgp"><input type="hidden" value="0"  class="msgTypeMethod" name="msgTypeMethod" checked="checked" id="msgp"><label for="msgp"></label></label></div>';
        }    



        var emailSubjectTmp = '<div class="searchField subjectField">\
                                    <label class="label">Subject </label>\
                                    <div class="fieldInput">\
                                        <input name="subject" type="text" id="subjectval" size="50">\
                                    </div>\
                                </div>';
        var mssageBoxTmp = '<div class="searchField messageField">\
                                    <label class="label">Message </label>\
                                    <div class="fieldInput">\
                                        <textarea name="detail"  id="messageval" class="textarea" ></textarea>\
                                    </div>\
                                </div>';
        var sendBtnTmp = '<div class="searchField submitField">\
                                    <label class="label">&nbsp;</label>\
                                    <div class="fieldInput">\
                                        <button type="submit" name="Submit" class="sendmsgemail  btn btn-green"><i class="fa fa-chevron-sign-right"> </i>Send</button>\
                                    </div>\
                                </div>';

        var messageSearchHtml = '<div class="msgSearchContainer">' + userTypeCheckBox + userTypeDropDown + companyDropDown + jobTitleDropDown + '</div>';
        var messageSearch = true;

        if (msgType != 1) {
            userTypeTmp = '';
            messageSearchHtml = '';
            messageSearch = false;

        }
        if (msgType != 3) {
            groupTypeSelectTemp = '';
        }
        if (msgType != 2) {
            siteGrpName = '';
        }
        if (msgType != 4) {
            siteUserField = '';
        }
        if (msgType != 5) {
            eventUserField = '';
        }
        


        var mainContainerTmp = messageSearchHtml + actionTypeTmp + '<div class="msgTabContainer">\
                                        <div class="msgContFrom whiteBox addConditionWrapper spSdbsSearchBox">\
                                            <div id="drpDnOp"> </div>' + siteGrpName + groupTypeSelectTemp + '<div id="empTemplDrp"></div>' + eventUserField + siteUserField + emailSubjectTmp + mssageBoxTmp + sendBtnTmp + '\
                                            <div class="clearfix"></div>\
                                        </div>\
                                        <div class="clearmsgshow whiteBox addConditionWrapper spSdbsSearchBox"></div>\
                                    </div>';
        $msgtablist(msgType, '0');
        if(checkValue_exst==0 && msgType==2){
            mainContainerTmp = '';
            //$('#userlistmsg').hide();
            $(this).hide();
        } 
        if(checkValue_exst==0 && msgType==5){
            mainContainerTmp = '';
            //$('#userlistmsg').hide();
            $(this).hide();
        } 
        var checkValuepub = $(this).attr('checkValuepub');
        var checkValuepub_exst = typeof (checkValuepub) != 'undefined' ? checkValuepub : 0;
        var checkValuepri = $(this).attr('checkValuepri');
        var checkValuepri_exst = typeof (checkValuepri) != 'undefined' ? checkValuepri : 0;
        var checkValuereq = $(this).attr('checkValuereq');
        var checkValuereq_exst = typeof (checkValuereq) != 'undefined' ? checkValuereq : 0;           
        //alert(checkValuepub);alert(checkValuepri);alert(checkValuereq);
        //var checkValue_exst = typeof (checkValue) != 'undefined' ? checkValue : 0;
        //var forcontent = $(this).attr('forcontent');  


        $('#msgCntWraper').html(mainContainerTmp);

        if (messageSearch == true) {
            $("#userTypeSearch").select2({
                width: '25%',
                placeholder: "Search user type",
                allowClear: true,
                data: userTypeJson
            });

            $("#CompanySearch").select2({
                width: '25%',
                placeholder: "Search company",
                allowClear: true,
                data: userDtailsJson.company
            });
            $("#jobTitleSearch").select2({
                width: '25%',
                placeholder: "Search job title",
                allowClear: true,
                data: userDtailsJson.title
            });
        }


        $('.clearmsgshow').hide();
        if (msgType == 3) {
            var grpDrpApp = 'Public';
            var grpTpHt = grpTypePublic.html();
            $('#empTemplDrp').after(grpTpHt);
        }
        $select('select');
        $('#s2id_userTypeSearch').attr('hideusertype', '0');
        /*****ckeck users count in Admin users group********/
        var checkMyval = $(this).attr('msgtype');
        if (checkMyval == 2) {
            var countGroupval = $('select[name="siteusernameval"] option:first').attr('countgroup');
            if (countGroupval == 0) {
                $('.subjectField, .messageField, .submitField').hide();
                $('.clearmsgshow').show().html('You do not have any user.');
            }
            if (countGroupval != 0) {
                $('.subjectField, .messageField, .submitField').show();
                $('.clearmsgshow').hide();
            }
        }
        $('select[name="siteusernameval"]').change(function() {
            var countGroupval = $('select[name="siteusernameval"] option:selected').attr('countgroup');
            if (countGroupval == 0) {
                $('.subjectField, .messageField, .submitField').hide();
                $('.clearmsgshow').show().html('You do not have any user.');
            }
            if (countGroupval != 0) {
                $('.subjectField, .messageField, .submitField').show();
                $('.clearmsgshow').hide();
            }
        });
        $('#s2id_userTypeSearch').attr('hideusertype', '1');
        $('#s2id_userTypeSearch').hide();
        /*****ckeck users count in Admin users group********/

        /*send function*/
        $('.sendmsgemail').click(function(e) {
            e.preventDefault();
            var subject = $.trim($('#subjectval').val());
            var message = $.trim($('#messageval').val());
            var msgTypeMethodChecked = $('input[name="msgTypeMethod"]:checked').val();
            //var selectTempId = $('select[name="emailTemplateSelect"]').val();
            var selectTempId = $('input[name="msgTypeMethod"]:checked').val();
            var groupNameSelectFetch = '';
            var groupNameSelect = $('select[name="groupNameSelect"]').val();
            var eventeusernameval = $('select[name="eventeusernameval"]').val();
            var siteUserNameVal = $('select[name="siteusernameval"]').val();
            var grpType = $('select[name="grpType"]').val();
            var usersearchInfo = $('#myTags').val();
            var msg_id = '0';

            var hideusertype = $('#s2id_userTypeSearch').attr('hideusertype');
            var usertypevalchk = $.trim($('#userTypeSearch').val());
            var companyvalchk = $.trim($('#CompanySearch').val());
            var jobtitlevalchk = $.trim($('#jobTitleSearch').val());
            if (msgType == 1) {
              
              if (hideusertype == 1) {
                       var siteplateform = 'admin';
                 
                          //alert(subject+'~~~~'+message);     
                        if (usertypevalchk != '' || companyvalchk != '' || jobtitlevalchk != '') {
                                var usertypevalid = $.trim($('#userTypeSearch').val());
                                var usertypevalval = $.trim($('#select2-chosen-1').text());
                                var usertypeval = usertypevalid + '~' + usertypevalval;
                                var companyval = $.trim($('#CompanySearch').val());
                                var jobtitleval = $.trim($('#jobTitleSearch').val());
                                //alert(usertypevalid);alert(companyval);alert(jobtitleval);
                                if(subject=='' && message==''){
                                    if(usertypevalid != '' && companyval =='' && jobtitleval ==''){
                                       $messageError('Please select company or job title.');
                                       return false;}else if(usertypevalid != '' && companyval !='' && jobtitleval ==''){
                                       $messageError('Please select job title.');
                                       return false;
                                    }else if(usertypevalid != '' && companyval =='' && jobtitleval !=''){
                                       $messageError('Please select company.');
                                       return false;
                                    }else if(companyval !='' && jobtitleval !='' && usertypevalid == ''){
                                       $messageError('Please select user type.');
                                       return false;
                                    }  
                                }                   
                        } /*else {
                            $messageError('Please select user type, company or job title.');
                            return false;
                        }*/
                    
              }else if (hideusertype == 0) {
                        var siteplateform = 'front';
                   if (companyvalchk != '' || jobtitlevalchk != '') {                       
                        var usertypeval = '';
                        var companyval = $.trim($('#CompanySearch').val());
                        var jobtitleval = $.trim($('#jobTitleSearch').val());
                        if(subject=='' && message==''){
                            if(companyval !='' && jobtitleval ==''){
                               $messageError('Please select job title.');
                               return false;}else if(companyval =='' && jobtitleval !=''){
                                    $messageError('Please select company.');
                                    return false;
                            }
                         }
                        }/*else {
                                $messageError('Please select company or job title.');
                                return false;
                          }*/
              }  

            }
            ///alert(siteplateform);
            if (selectTempId == undefined) {
                selectTempId = 0;
            }

            if (groupNameSelect == undefined && siteUserNameVal == undefined && eventeusernameval == undefined) {
                groupNameSelectFetch = 0;
            } else {
                if (groupNameSelect != undefined) {
                    groupNameSelectFetch = groupNameSelect;
                } else if (eventeusernameval != undefined) {
                    groupNameSelectFetch = eventeusernameval;
                } else {
                    groupNameSelectFetch = siteUserNameVal;
                }
            }

            if (msgType == 3 && grpType == 'Select Group') {
                $messageError('Please select a group type');
                return false;
            }

            if (subject == '' || message == '') {
                $messageError('Please enter a subject and message');
                return false;
            }
            // this place for send functions
            $sendMessageEmail(subject, message, msgTypeMethodChecked, selectTempId, msgType, groupNameSelectFetch, '', usersearchInfo, msg_id, '', siteplateform, usertypeval, companyval, jobtitleval);


        });
        /*end send function*/
        //$msgtablist(msgType, '0');

        $('.msgOptionChk .msgTypeMethod').click(function() {
            var val = $(this).val();
            if (val == 1) {
                $('#empTemplDrp').hide();
            }
            $msgtablist(msgType, val);
            if (val == 1) {
                $('#empTemplDrp').html(emailTempDrpDown.html());
            } else {
                $('#empTemplDrp').html('');
            }
        });

        $('.usrOptionChk .usrTypeMethod').click(function() {
            var val = $(this).val();
            if (val == 1) {
                $('#s2id_userTypeSearch').attr('hideusertype', '1');
                $('#s2id_userTypeSearch').hide();
            }
            if (val == 0) {
                $('#s2id_userTypeSearch').attr('hideusertype', '0');
                $('#s2id_userTypeSearch').hide();
            }
        });

        $('select[name="grpType"]').change(function(event) {
            /******************************************/
                var checkValuepub = $('#userGroups').attr('checkValuepub');
                var checkValuepub_exst = typeof (checkValuepub) != 'undefined' ? checkValuepub : 0;
                var checkValuepri = $('#userGroups').attr('checkValuepri');
                var checkValuepri_exst = typeof (checkValuepri) != 'undefined' ? checkValuepri : 0;
                var checkValuereq = $('#userGroups').attr('checkValuereq');
                var checkValuereq_exst = typeof (checkValuereq) != 'undefined' ? checkValuereq : 0;
                //alert(checkValuepub_exst);alert(checkValuepri_exst);alert(checkValuereq_exst);
                if(checkValuepri_exst!=0 && msgType==3){
                   $('.submitField').show();
                   $("#subjectval,#messageval").removeAttr("disabled");  
                }if(checkValuepri_exst==0 && msgType==3){     
                   $('.submitField, .publicGroup').hide();
                   //$('.publicGroup').hide();
                   $("#subjectval,#messageval").attr("disabled", "disabled");   
                }if(checkValuereq_exst!=0 && msgType==3){
                   $('.submitField').show();
                   $("#subjectval,#messageval").removeAttr("disabled");  
                }if(checkValuereq_exst==0 && msgType==3){     
                   $('.submitField, .publicGroup').hide();
                   //$('.publicGroup').hide();
                   $("#subjectval,#messageval").attr("disabled", "disabled");   
                }
            /*****************************************/
            var thisElement = $(this).val().split('Group');
            var grpDrpApp = $.trim(thisElement[0]);
            var grpTpHt = '';
            if (grpDrpApp == 'Public') {
                grpTpHt = grpTypePublic.html();
            } else if (grpDrpApp == 'Private') {
                grpTpHt = grpTypePrivate.html()
            } else if (grpDrpApp == 'Request') {
                grpTpHt = grpTypeRequest.html()
            }
            $('.grpNameWrapper').remove();
            $('#empTemplDrp').after(grpTpHt);
            var countGroupval = $('select[name="groupNameSelect"] option:first').attr('countgroup');
            if (countGroupval == 0) {
                $('.subjectField, .messageField, .submitField').hide();
                $('.clearmsgshow').show().html('You do not have any user.');
            }
            if (countGroupval != 0) {
                $('.subjectField, .messageField, .submitField').show();
                $('.clearmsgshow').hide();
            }
            $('select[name="groupNameSelect"]').change(function() {
                var countPlateformGroupval = $('select[name="groupNameSelect"] option:selected').attr('countgroup');
                if (countPlateformGroupval == 0) {
                    $('.subjectField, .messageField, .submitField').hide();
                    $('.clearmsgshow').show().html('You do not have any user.');
                }
                if (countPlateformGroupval != 0) {
                    $('.subjectField, .messageField, .submitField').show();
                    $('.clearmsgshow').hide();
                }
            });

            if (grpDrpApp == 'Public') {
                grpTpHt = grpTypePublic.html();
                if(checkValuepub_exst!=0 && msgType==3){
                   $('.submitField').show();
                   $("#subjectval,#messageval").removeAttr("disabled");  
                }
                if(checkValuepub_exst==0 && msgType==3){     
                   $('.submitField,.publicGroup').hide();
                   //$('.publicGroup').hide();
                   $("#subjectval,#messageval").attr("disabled", "disabled");   
                }
            } else if (grpDrpApp == 'Private') {
                grpTpHt = grpTypePrivate.html();
                if(checkValuepri_exst!=0 && msgType==3){
                   $('.submitField').show();
                   $("#subjectval,#messageval").removeAttr("disabled");  
                }
                if(checkValuepri_exst==0 && msgType==3){     
                   $('.submitField,.privateGroup').hide();
                   //$('.privateGroup').hide();
                   $("#subjectval,#messageval").attr("disabled", "disabled");   
                }
            } else if (grpDrpApp == 'Request') {
                grpTpHt = grpTypeRequest.html();
                if(checkValuereq_exst!=0 && msgType==3){
                   $('.submitField').show();
                   $("#subjectval,#messageval").removeAttr("disabled");  
                }
                if(checkValuereq_exst==0 && msgType==3){     
                   $('.submitField,.requestGroup').hide();
                   //$('.requestGroup').hide();
                   $("#subjectval,#messageval").attr("disabled", "disabled");   
                }
            }

        });
        /**************auto complete ***********/
        var userNameTag = [];
        var inputVAlue = '';

        $("#myTags").tokenInput(BASE_URL + "/admin/message/searchusers/", {
            preventDuplicates: true,
            hintText: "type user name",
            theme: "facebook",
            resultsLimit: 10,
            resultsFormatter: function(item) {
                return "<li>" + "<img src='" + item.url + "' title='" + item.name + "' height='25px' width='25px' />" + "<div style='display: inline-block; padding-left: 10px;'><div class='full_name'>" + item.name + "</div><div class='email'>" + item.email + "</div></div></li>"
            },
            tokenFormatter: function(item) {
                return "<li><p><img src='" + item.url + "' title='" + item.name + "' height='25px' width='25px' />" + item.name + "</p></li>"
            }
        });


        /**************auto complete ***********/
        if(checkValuepub_exst!=0 && msgType==3){
           $('.publicGroup').show();
           $("#subjectval,#messageval").removeAttr("disabled");   
        }
        if(checkValuepub_exst==0 && msgType==3){
           $('.publicGroup, .submitField').hide();
           $("#subjectval, #messageval").attr("disabled", "disabled");  
        }

        if(clientID==21 || clientID==22){                          
                $('#s2id_userTypeSearch').attr('hideusertype', '0');
                $('#s2id_userTypeSearch').hide();
            } 
    });


    $('#messageWrapper li:first a').click();


    function checkLength(o, n, min, max) {
        if (o.val().length < min) {
            o.addClass("error");
            $messageError("The " + n + " must be at least " + min + " characters long");
            return false;
        } else {
            return true;
        }
    }

    function checkRegexp(email, o, regexp, n) {
        if (!(regexp.test($.trim(o)))) {
            email.addClass("error");
            $messageError(n);
            return false;
        } else {
            return true;
        }
    }
    $('.sendusermsgemail').click(function(e) {
        e.preventDefault();
        var bValid = true;
        var separatedEmail = [];
        separatedEmail.push($("input[name=idoremail]:checked").val());
        var action = $("input[name=idoremail]:checked").attr('id');

        if (action == 'useremail') {
            actioncall = 1;
            selectTempId = 1;
        } else {
            actioncall = 0;
            selectTempId = 0;

        }
        var subject = $("#subjectval");
        var message = $("#messageval");
        var subjectval = message.val();
        var messageval = message.val();
        var allFields = $([]).add(subject).add(message);
        var siteplateform;
        var usertypeval;
        var companyval;
        var jobtitleval;
        allFields.removeClass("error");

        if (subject.val() == '') {
            subject.addClass("error");
            $messageError("subject should have a value!");
            return false;
        }
        if (message.val() == '') {
            message.addClass("error");
            $messageError("message should have a value!");
            return false;
        }

        $sendMessageEmail(subjectval,messageval, actioncall, selectTempId, msgType = 4, groupNameSelectFetch = '0', '', separatedEmail, msg_id = '0', siteplateform, usertypeval, companyval, jobtitleval);

    });


   $('body').on('click','.viewusersBtn',function(e){
        var uonlinelist  = '<div id="msgemailuserlist"  title=" "><div id="msgemailusrContainer"></div></div>';
        var msgid = $(this).attr('msgid');
        $('body').append(uonlinelist);
        $( "#msgemailuserlist" ).dialog({                      
            width:800,
            height:500,  
            title:'',
            close:function(){
                $(this).dialog("close");
                $("#msgemailuserlist" ).remove();
            },
            open:function()
            {                
                $fluidDialog();
                $('#msgemailusrContainer').append('<div class="loaderOverlay2"></div>'); 
                $.ajax({
                    type : "POST",
                    dataType : 'json',
                    data: {"msgid": msgid,},
                    url:  BASE_URL+'/admin/message/getmsgemailuserlist',        
                    success : function(response) {
                        //alert(response);return false;    
                        $('.loaderOverlay2').remove();
                        $('#msgemailuserlist #msgemailusrContainer').html(response.content);                         
                    }
                });
            }
        });
    });

});