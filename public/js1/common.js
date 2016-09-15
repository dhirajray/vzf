var metionArray = [];
var faceBookFriend = false;
var faceBookFriendID = '';
var faceBookFriendUserName = '';
var clientType = 1;
var sharefilestore = [];
var QASTATUS='';
var groupidmaster = '';
var isPostOnPlateForm = 0;
var twitterFriend = false;
var twitterFriendID = '';
var twitterFriendUserName = '';
var page_access_token;
var linkedinFriend = false;
var hidePlateFormSocialMenu = false;
var linkedinFriendID = '';
var linkedinFriendUserName = '';
var userContent;
var iamConnectWithFacebook = false;
var iamConnectWithTwitter = false;
var iamConnectWithLinkedin = false;

var platformConnectWithFacebook = false;
var platformConnectWithTwitter = false;
var platformConnectWithLinkedin = false;

var platformConnectWithRSS = false;
var busybooking = false;

var facebookpage_id = '';
var twitterpage_id = '';
var linkedinpage_id = '';

var filter = '';
var restUrl='';
var filterTwitter = '';

var checkFeedComing = '';
var iamLoginNow = '';
var showMyfeed = '';
var showPlatformfeed = '';
var nowShowPlatformfeed = '';

var hideIconsInCreatePost = false;
var hash = window.location.hash;
var showthirdoption = '';
var ie = '';
var newAddedGroups = '';

var closeSDetails;
var closeSocialFeedSidebar;

var twValue = '';
var fbValue = '';
var linkValue = '';
var rssValue = '';

var myFeedLink = ''
var platFormLink = '';
var flagPlatFeed = false;
var flagMyFeed = false;
var enabledMobile = false;

var isMobile = navigator.userAgent.match(/(iPad)|(iPhone)|(iPod)|(android)|(webOS)/i);

var iconexe = {
        'docx':'word',
        'xlsx':'excel',
        'xls':'excel',
        'doc':'word',
        'ppt':'powerpoint',
        'txt':'text',
        'jpeg':'image',
        'jpg':'image',
        'gif':'image',
        'png':'image',                             
        'WAVE':'audio',
        'mp3':'audio',
        'Ogg':'audio',
        'mp4':'video',
        'WebM':'video',
        'Webm':'video',
        'wmv':'video',
        'ogv':'video',
        'pdf':'pdf'
        }         

$(function() 
{ 
  Dropzone.autoDiscover = false;

if (hash == '#videobroadcast' || hash == '#SurveysRightBtnOn' || hash == '#event_sectionOn' || hash == '#my_event_sectionOn' || hash == '#spacialdb_sectionOn' || hash == '#liveBroadcastOn' || hash == '#my_spacialdb_sectionOn' || hash == '#my_liveBroadcastOn'){
    $('#dbee-feeds').html('<div style="margin:20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>');
   
 }

    if (Social_Block == 0) {
        if (SESS_USER_ID != adminID && SESS_USER_ID != '') {
            myFeedLink = '<a href="javascript:void(0);" data-type="my feed">My social feeds</a>';
        }
    }

if(isMobile!=null){
 if(PAGE_DBEE_ID!=''){
        if(dbTypeDetails==15 || dbTypeDetails==6){
            // do nothing
      }else{
        $('#mySideMenu .active').hide();
        $('html').addClass('pageIsDetails');
      }
    }
}


  var windH = $(window).height();
  var windW = $(window).width();
  var headerHeight = 0;
    $.profilePicUpload = function (options){
        defaults = {
            element:'',
            cropAppend:'',
            success:$.noop,
            process:$.noop,
            cropLoad:$.noop
        }
       var settings = $.extend({}, defaults, options);
       var fileList;
        $(settings.element).dropzone({
            url: BASE_URL + "/profile/imguplod",
            maxFilesize: 5,
            addRemoveLinks: true,
            acceptedFiles: '.png, .jpg, .jpeg, .gif, .JPG, .JPEG, .PNG, .GIF',
            maxfilesexceeded: function(file, serverFileName) {
                $dbConfirm({
                    content: 'You can upload only 1 file',
                    yes: false,
                    error: true
                });
            },
            processing:function(){
              settings.process.call();
            },
            error: function(file, serverFileName) {
                $dbConfirm({
                    content: serverFileName,
                    yes: false,
                    error: true
                });
            },
            success: function(file, serverFileName) {
                fileList = "serverFileName = " + serverFileName;
                $('#pixUploader').hide();
               var mycropImage =IMGPATH+'/users/'+serverFileName;
               $.dbCropImage({element:settings.cropAppend,url:mycropImage, load:function(){
                  settings.cropLoad.call();
                 }});

              $('#profile_picture').val(serverFileName);
                settings.success.call();               
                setTimeout(function() {
                    $.dbeePopup('resize');
                }, 220);

            },
            removedfile: function(file) {
                $('#upload-picture').hide();
                $.ajax({
                    url: BASE_URL + "/profile/imgunlink",
                    type: "POST",
                    data: fileList,
                    success: function() {
                        $('#profile_picture').val('');

                    }
                });
            }
        });
    }

    $takeTour = function (){
          var welcomeScreen  = '<div class="welcomeScreenForTour">\
                                  <div class="prPicUploader" style="background:url('+IMGPATH+'/users/'+userpic+'); background-size:cover">\
                                    <i class="fa fa-camera fa-2x"> <input type="file" name="file" accept="image/jpeg,image/gif,image/png"  /></i>\
                                </div>\
                                <div>\
                                    <h2>'+SESS_USER_NAME+'</h2>\
                                    <div>Welcome</div>\
                                    <div class="wlScrnBtnWrp"><a href="javascript:void(0);" class="btn btn-mini btn-yellow startTourNow">Take a tour</a> <a href="javascript:void(0);" class="skipTour">Skip</a></div>\
                                </div>\
                              </div><div class="cropAppendWrp"></div>';
          var tourJson = [{ showElement:'.welcomeScreenForTour', content:welcomeScreen},{showElement:'#dashboarduserDetails', content:'Welcome to your user dashboard!  From here you can edit your profile picture, platform username and customise various features.'},{showElement:'#proCollapseMain .userBtnOver', content:'Click \'Customise my dashboard\' to refine which posts, <br>in which categories, and from whom you wish to see.'},{showElement:'#sortable', content:'This menu allows you to quickly navigate various platform features.<br> You can change the order of the menu by dragging and dropping them.'},{showElement:'.allUserChat', content:'Users who follow one another can use instant message to chat.'},{showElement:'.topSettingMenu', content:'Edit your account settings, including notifications,  privacy and biography.'},{showElement:'.startDbGeader', content:'Create and categorize your posts. <br> Choose to make posts public or private and use @username <br>  referencing to specify who sees them.'},{showElement:'.topSettingMenu', content:'Access and take the tour any time, within User Settings.'}];
         var tourSteps = '';
   

       $.each(tourJson, function (i,v){
          var cnt = v.content;
          var step = v.step;
          var Arrow = '<span class="tourArrow"></span>';
          
            if(i==6){
            var  nextBtn = '<a href="javascript:void(0);" class="completeTour"><i class="fa fa-check"> Complete tour</i></a>';
            }else{
              var nextBtn = '<a href="javascript:void(0);" class="tourNavigation tourNext">Next <i class="fa fa-chevron-circle-right"></i></a>';
            }
             var navigationBtn = '<div class="tourNavigationWrp"><a href="javascript:void(0);" class="tourNavigation tourPrevious"><i class="fa fa-chevron-circle-left"></i> Previous</a>'+nextBtn+'</div>';
          tourSteps +='<div class="tourSteplist tourStep'+i+'" data-index="'+i+'">'+cnt+navigationBtn+Arrow+'</div>';
       });
       var tourTemplate = '<div class="takeATour"><div class="pageCenter" >'+tourSteps+'</div></div>';
        $('.takeATour').remove();
        $('body').prepend(tourTemplate);
        $('html').addClass('startTakeATour');
        $('.takeATour .tourSteplist:first').show();
        $('html').attr('tour-active', 'tourStep0');
       
         $('.startTourNow').click(function (){          
             $('.tourSteplist:visible .tourNext').click();
        });

        $('.tourNavigation').click(function (){
           var dataIndex = parseInt($('.tourSteplist:visible').attr('data-index'));
           if($(this).hasClass('tourPrevious')==true){
             var crntTour = dataIndex-1;
           }else{
             var crntTour = dataIndex+1;
           }  
            if(crntTour==7){
               setTimeout(function(){
                $('.skipTour').trigger('click');
              },1000);
           }

           if(tourJson[crntTour].showElement!=''){
            var showElement =  $(tourJson[crntTour].showElement); 
             $('.activeHighightTourBox').removeClass('activeHighightTourBox');
             if(crntTour!=0){
              showElement.addClass('activeHighightTourBox');
            }
          }
          
           $('.tourSteplist').hide();
           $('html').attr('tour-active', 'tourStep'+crntTour);
           $('.tourSteplist[data-index="'+crntTour+'"]').show();

          
        });

       
           function closeTour  (){
             $('.takeATour').fadeOut(function (){
               $('.activeHighightTourBox').removeClass('activeHighightTourBox');
                $('html').removeClass('startTakeATour').removeAttr('tour-active');
                $(this).remove();
               });
          }
        $('.skipTour').click(function (){
            $('.tourSteplist').hide();
            $('.tourStep7').show();
            $('html').attr('tour-active', 'tourStep7');
            $('.topSettingMenu').addClass('activeHighightTourBox');
            setTimeout(function (){
                closeTour()
            }, 4000);
        });
         $('.completeTour').click(function (){
              closeTour()
         });
         $('.cropAppendWrp').hide();
         $('.prPicUploader input').change( function(evt) {
            $.dbCropImage({element:'.cropAppendWrp', event:evt, load:function(){
                            $('.cropBtnsWrp').remove();
                            $('.takeATour .welcomeScreenForTour').hide();
                            $('.cropAppendWrp').show();
                            $('#myCropTool').after('<div class="cropBtnsWrp"><a href="javascript:void(0);" class="pull-left btn cancelProfilePic">Cancel</a><a href="javascript:void(0);" class="pull-right btn btn-yellow" id="upload-picture">Save</a></div>');
                             $('.cancelProfilePic').click(function (){
                                $('.takeATour .welcomeScreenForTour, #pixUploader').show()
                                $('#pixUploader .dz-preview ').remove();
                                $('.prPicUploader .fa-spin').addClass('fa-camera').removeClass('fa-spin fa-spinner');
                                $('.cropAppendWrp').hide().html('');
                             });

                         }});
         
        });
        /*$.profilePicUpload({
            element:'#uploadDropzoneProfileTour', 
            cropAppend:'.cropAppendWrp',
            success:function(){
               $('.cropBtnsWrp').remove();
              $('.takeATour .welcomeScreenForTour').hide();
              $('.cropAppendWrp').show();
             
            }, 
            process:function(){
              $('.prPicUploader .fa-camera').addClass('fa-spin fa-spinner').removeClass('fa-camera');
            },
            cropLoad:function(){
            $('#myCropTool').after('<div class="cropBtnsWrp"><a href="javascript:void(0);" class="pull-left btn cancelProfilePic">Cancel</a><a href="javascript:void(0);" class="pull-right btn btn-yellow" id="upload-picture">Save</a></div>');
             $('.cancelProfilePic').click(function (){
               $('.takeATour .welcomeScreenForTour, #pixUploader').show()
               $('#pixUploader .dz-preview ').remove();
                $('.prPicUploader .fa-spin').addClass('fa-camera').removeClass('fa-spin fa-spinner');
               $('.cropAppendWrp').hide().html('');
             });
            }
        });*/             
       
    }
    if(TAKEATOUR!=1)
    {
        $('body').on('click','.startTourNow, .skipTour',function(){
          $.ajax({
              type: "POST",
              dataType: 'json',
              url: BASE_URL + '/dashboarduser/takeatour/',
              beforeSend: function() {},
              cache: false,
              success: function(response){
                /*  if (response.success == '') {
                      $messageError ('Oops, something went wrong.');                  
                  }*/
              }

          });
        });
    }
   
   
    $dbreloadimg = function(){
        var numItems = $('.pfpic').length; 
        if(numItems>0){
        var yyn = $('.pfpic img');
        //yyn.addClass('fa fa-spinner fa-spin');        
             $.each(yyn, function(){         
                 var this_image = this;
                 var lsrc = IMGPATH+'/users/small/'+userpic ;        
                     if(lsrc.length > 0){
                         var img = new Image();
                         img.src = lsrc;
                         $(img).load(function() {
                             this_image.src = this.src;
                         });
                   
                 }
             });

        }
        
    }

       
    // start confimbox
    $dbConfirm = function(options) {
        var defaults = {
            content: '',
            yes: true,
            no: true,
            yesLabel: 'Yes',
            noLabel: 'No',
            close: false,
            overlay: true,
            loader: false,
            error: false,
            delay: 4000,
            yesClick: $.noop,
            noClick: $.noop
        }
        var settings = $.extend({}, defaults, options);

        var btns = '';
        var cboxWidth = '';
        var cboxHeight = '';
        var thisBoxID = 'confirmMessageWrp';
        var dbOverlay = '';
        var footerContent = '';
        var confirmErrorClass = '';
        var confirmErrorIcon = '';

        if (settings.yes == false) settings.noLabel = 'Close';

        if (settings.yes == true && settings.loader != true)
            btns = '<a href="javascript:void(0)" class="btn btn-yellow" id="pressYesConfirm">' + settings.yesLabel + '</a>';

        if (settings.no == true && settings.loader != true)
            btns += '<a href="javascript:void(0)" class="btn" id="pressCloseConfirm">' + settings.noLabel + '</a>';


        function closeConfirmbox(trueFalse) {

            $('.dbConfirmOverlay').fadeOut('slow', function() {
                $(this).remove();
            });
            $('#' + thisBoxID).fadeOut('slow', function() {
                $(this).remove();
                if (trueFalse == '') trueFalse = false;
                return trueFalse;
            });
        }

        $(document).keyup(function(e) {
            if (e.keyCode == 27) {
                closeConfirmbox();
            }
        });

        if (settings.close == true) {
            closeConfirmbox();
        }
        if (settings.error == true) {
            confirmErrorClass = 'confirmErrorWrp';
            confirmErrorIcon = '';
        }


        if (settings.overlay == true) {
            dbOverlay = '<div  class="dbConfirmOverlay"></div>';
        }
        if (settings.loader == true) {
            settings.content = '<i class="fa fa-spin fa-spinner fa-4x"></i> ';
            footerContent = '';
        } else {
             footerContent = '<div class="cnfFooter">' + btns + '</div>';
          
        }
        var ConfirmMessageHtml = dbOverlay + '<div id="' + thisBoxID + '" style="display:none" class="' + confirmErrorClass + '">\
                                        <div class="confirmContainer">' + confirmErrorIcon + settings.content + '</div>\
                                        ' + footerContent + '\
                                  </div>';


        if ($('#' + thisBoxID).is(":visible") == true) $('#' + thisBoxID).remove();

        $('body').append(ConfirmMessageHtml);
        //alert($('#'+thisBoxID).width() + '--------'+ $('#'+thisBoxID).width(true))
        cboxWidth = $('#' + thisBoxID).width() / 2;
        cboxHeight = $('#' + thisBoxID).height() / 2;
        $('.dbConfirmOverlay').fadeIn(function() {
            $('#' + thisBoxID).fadeIn();
        });
        if (windW > 480) {
            $('#' + thisBoxID).css({
                marginLeft: -cboxWidth + 'px',
                marginTop: -cboxHeight + 'px'
            });
        } else {
            //var newWinW = windW-20;
            //$('#'+thisBoxID).css({width:newWinW,maxWidth:newWinW,marginLeft:-(newWinW/2)+'px', marginTop:-(cboxHeight-20)+'px'});
        }

        if (settings.noClick == function() {}) {
            $('#pressCloseConfirm').click(function(event) {
                closeConfirmbox();
            });
        } else {
            $('#pressCloseConfirm').click(function(event) {
                settings.noClick.call();
                closeConfirmbox();

            });
        }
        $('#pressYesConfirm').click(function(event) {
            settings.yesClick.call();
            closeConfirmbox();
        });

        if (settings.yes != true) {
            setTimeout(function() {
                closeConfirmbox();
            }, settings.delay);
        }


    }

    // end confimbox

    
  $sharemydropzone = function(options) {

   $('#fadeDropzone1').dropzone({

  url: BASE_URL + "/myhome/sharefile",                
                addRemoveLinks: true,
                parallelUploads: 1,
                maxFilesize: 5,
                acceptedFiles: '.xlsx,.xls,.docx,.doc,.txt,.ppt,.pdf,.png,.jpeg,.mp3,.gif,.jpg,.mp4',              
                error: function(file, serverFileName) {
                    $dbConfirm({
                        content: serverFileName,
                        yes: false
                    });
                },
                processing: function(file, serverFileName) {                            
                    $('#fadeDropzone1 .dz-preview').hide();
                    $('#pageContainer').addClass('blurBgpop');
                },
                totaluploadprogress: function(progress) {
                  
                    if($('#mesageNotfiOverlay').is(':visible')!=true){                             
                      $dbMessageProgressBar({fetchText:'', animate:false,content:'Uploading...'});
                      $('#mesageNotfiOverlay .msgNoticontent').addClass('popupStripBar');
                    }
                      setTimeout(function(){
                    $('#mesageNotfiOverlay .progressBarWrp').attr('data-loaded', Math.round(progress)+'%');
                    $('#mesageNotfiOverlay .progressMsgBar').css({width: progress+'%'});                    
                    if(progress=='100'){
                          
                        $('#mesageNotfiOverlay .loaderShow').hide();
                        $('#mesageNotfiOverlay .prCnt').html('<div class="succMsg"><i class="fa fa-check-circle-o tickMark"></i> Uploaded successfully</div>');                        
                        $('#mesageNotfiOverlay .msgNoticontent').append('<div class="shdBtnpad"><span id="shareBtn" class="gobackBtn btn btn-yellow">Upload another file</span> <span class="closeddPopbtn btn btnsharclose" share="close">Close</span></div>');                        
                        $('#mesageNotfiOverlay .msgNoticontent').append('<div class="orBtn">OR</div>');
                        $('#mesageNotfiOverlay .msgNoticontent').append('<div class="dropShareBtn"><span class="btn btn-yellow" id="sharefoll">Share with Followers</span> <span class="btn btn-yellow" id="userShare">Select users</span></div>')
                        $('#mesageNotfiOverlay .msgNoticontent').append('<div class="userInptxt"><input id="submit_tag_names" type="hidden" value="" name=""><ul class="fieldInput" id="myTags2"></ul> </div>');
                        $('#mesageNotfiOverlay .msgNoticontent .progressBarWrp').hide();                        
                        
                        $('#myTags2').tokenInput(BASE_URL+"/myhome/searchusers/", {
                               preventDuplicates: true,
                               hintText: "type user name",
                               theme: "facebook",
                               resultsLimit:10,
                               resultsFormatter: function(item){ return "<li>" + "<img src='" + item.url + "' title='" + item.name + "' height='25px' width='25px' />" + "<div style='display: inline-block; padding-left: 10px;'><div class='full_name'>" + item.name + "</div><div class='email'>" + item.email + "</div></div></li>" },
                               tokenFormatter: function(item) { return "<li><p><img src='" + item.url + "' title='" + item.name + "' height='25px' width='25px' />" + item.name + "</p></li>" }
                            })


                        $('#mesageNotfiOverlay .msgNoticontent').append('<div class="strtSharbtn"><div id="strtShrBtnpad"><span class="strtShrBtnpad btn btn-yellow shareYlobtn disabled" id="shareYlobtn" share="follow">Start Sharing</span></div> </div>');
                    }

                      },500);
                 
             },
             success : function(file, response){
              sharefilestore.push(response);
            }  


 });

}


$makesselect2 = function(E1,options){
     var defaults = {           
            placeholder: 'Search name'
        }

        var settings = $.extend({}, defaults, options);


            
$(settings.ele).select2({
                    width:'80%',
                    placeholder: settings.placeholder,
                    allowClear: true,
                     tags: true,
                     ajax: {
                        url: BASE_URL+"/myhome/searchtaguser",                      
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params
                            };
                        },
                        results:function(data){
                             return {
                                results: data
                            };
                        },
                        cache: true
                        },
                        minimumInputLength: 3,
                        formatResult:_formatingRes,
                        formatSelection:_formating
                    }); 

function _formating(data) {
                    if(data!="")
                    {
                     return '<img src="'+data.url+'" />' + data.text;
                    }
                    else
                    {
                     return 'No user found!';
                    }
                }

                function _formatingRes(data) {
                    if(data!="")
                    {
                     return '<img src="'+data.url+'" />' + data.text + data.username;
                    }
                    else
                    {
                     return 'No user found!';
                    }
                }
}
    

    // this is for create popup
    $.dbeePopup = function(content, options) {

        var dbOverlay = '';
        var contentHeight = '';
        var popID = 'dbeePopupWrapper';
        var popUpHeader = '';
        var closeBtn = '';
        var width;
        var contentHeaderHeights = '';
        var contentFooterHeights = '';

        if (content == 'resize') {
            var bgColor = $('#' + popID).css('background-color');
        }

        var defaults = {
            master:false,
            overlay: true,
            overlayClick: false,
            closeBtn: true,
            escape: true,
            close: false,
            bar:true,
            closeBtnHide: false,
            height: 'auto',
            closeLabel: 'Close',
            content: '',
            otherBtn: '',
            title: '',
            width: '',
            popClass: '',
            contentHeader: '',
            bg: bgColor,
            load: $.noop,
            scrollbar:true

        }

        var settings = $.extend({}, defaults, options);


        var bgColor = settings.bg;

        function getHeaderFooterHeight() {
            if ($('#' + popID + ' .dbPostPopHeader').is(':visible') == true) {
                contentHeaderHeights = $('#' + popID + ' .dbPostPopHeader').outerHeight(true);
            } else {
                contentHeaderHeights = '';
            }
            if ($('#' + popID + ' .postTypeFooter').is(':visible') == true) {
                contentFooterHeights = $('#' + popID + ' .postTypeFooter').outerHeight(true);
            } else {
                contentFooterHeights = '';
            }

        }

        function closePopup() {
            //$messageError('','close');
            $('#' + popID).css({
                left: -500
            }).removeClass('bounceOpen');
            setTimeout(function() {
                $('.dbeePopupOverlay').show(function() {
                    $('#' + popID + ', .dbeePopupOverlay, .popupStripBar').remove();
                    $('html').removeClass('popupOnNowjs');
                    $(this).remove();
                });
            }, 00);
        }

        // resize popup
        function resizePop() {


                $('.dbeePopContent').perfectScrollbar('destroy');

                var widthDefind = '';
                widthDefind = $('#' + popID).attr('data-width');
                getHeaderFooterHeight();
                setTimeout(function() {

                    $('#' + popID).addClass('bounceOpen');


                    if (settings.height == 'auto') {
                        $('#' + popID).removeAttr('style');
                        $('#' + popID).css({
                            background: "" + settings.bg + ""
                        });
                        contentHeight = $('#' + popID + ' .dbeePopContent').outerHeight(true) + contentHeaderHeights;

                        if (windH < contentHeight) {

                            if (widthDefind != '') {
                                $('#' + popID).css({
                                    top: "5%",
                                    bottom: "5%",
                                    width: widthDefind,
                                    left: '50%',
                                    height:'90%',
                                    marginLeft: -widthDefind / 2,
                                    background: "" + settings.bg + ""
                                });
                            } else {
                                var scrolHeight = windH - (contentFooterHeights + contentHeaderHeights);
                                scrolHeight = (scrolHeight * 100 / windH);
                                $('#' + popID ).css({
                                    top: "5%",
                                    bottom: "5%",
                                    height:'90%',
                                    background: "" + settings.bg + ""
                                });
                                $('#' + popID + ' .dbeePopContent').css({
                                    maxHeight: scrolHeight + '%'
                                });
                                $('.popupStripBar').css({
                                    top: "0",
                                    bottom: "0",
                                    height:'inherit'
                                });
                            }
                             if (windW>979) {
                                setTimeout(function() {
                                      if(settings.scrollbar==true) {                                  
                                         $('.dbeePopContent').perfectScrollbar({
                                              suppressScrollX: true,
                                              wheelSpeed: 80
                                          });  
                                        }
                                   
                                }, 600);
                             }

                        } else {
                            var topSpace = (windH - contentHeight) / 2;
                            topSpace = (topSpace * 100 / windH);

                            if (widthDefind != '') {
                                $('#' + popID).css({
                                    top: "" + topSpace + "%",
                                    left: '50%',
                                    width: widthDefind,
                                    marginLeft: -widthDefind / 2,
                                    background: "" + settings.bg + ""
                                });
                            } else {
                                $('#' + popID).css({
                                    top: "" + topSpace + "%",
                                    background: "" + settings.bg + ""
                                });
                                $('.popupStripBar').css({
                                    height: contentHeight,
                                    top: "" + topSpace + "%"
                                });
                            }

                        }
                    }

                }, 2);
            }
            // end resize popup
        if (content == 'close') {
            closePopup();
            return false;
        }

        if (content == 'resize') {
            resizePop();
            return false;
        }

        if (settings.closeBtn == true) {
            var closeBtn = '<div class="postTypeFooter">\
                                    <a href="#" class="pull-left btn closePostPop">' + settings.closeLabel + '</a>' + settings.otherBtn + '\
                                    <div class="clearfix"></div>\
                                </div>';
        }

        if (settings.closeBtnHide == true) {
            var closeBtn = '<div class="postTypeFooter">' + settings.otherBtn + '\
                                    <div class="clearfix"></div>\
                                </div>';
        }


        /*if(settings.title!=''){
            popUpHeader= '<div class="popUpHeader">\
                            <h2>'+settings.title+'</h2>\
                        </div><div style="height:50px; width100%;"></div>';
        }*/
        
        var isMasterPop='';
         if (settings.master == true) {
            isMasterPop ='isMasterPop';
         }
        if (settings.overlay == true) {
            dbOverlay = '<div id="overlay" class="dbeePopupOverlay '+isMasterPop+'"></div>';
        }

        var isCloseBtn='';
        if (settings.closeBtn == false) {
            isCloseBtn='isCloseBtn';
        }
        var popupStripBar = '<div class="popupStripBar '+isMasterPop+'"></div>';
        if(settings.bar==false){
            popupStripBar='';
        }
        var isWidthPop='isWidthPopUp';
        if(settings.width==''){
            isWidthPop = '';
        }
        var popupTemplate = dbOverlay + popupStripBar+'<div class="postContainer ' + settings.popClass+' ' +isCloseBtn+' '+isMasterPop+' '+isWidthPop+'"   id="' + popID + '" data-width="' + settings.width + '" >\
                                ' + settings.contentHeader + '\
                                <div class="dbeePopContent"><div class="popContainerTemp">'+content+'</div></div>\
                                    ' + closeBtn + '\
                                </div>';


        $('html').addClass('popupOnNowjs').find('body').append(popupTemplate);
        getHeaderFooterHeight();


        $('.dbeePopupOverlay').show();
        if($('.popContainerTemp img').length==0){
            $('.popContainerTemp').removeClass('popContainerTemp');
        }else{

            $dbLoader('.dbeePopContent');
             $('.popContainerTemp img').load(function (){
                $dbLoader('.dbeePopContent', '', 'close');
               $('.popContainerTemp').removeClass('popContainerTemp');
                 resizePop();
            });
        }
         settings.load.call();
          resizePop();
         $(document).ajaxSuccess(function(){
            if($('#' + popID).is(':visible')==true){
                resizePop();
            }
         });
        
          
        // this dbee popup close function

        // end dbee popup close function

        // this dbee popup close function
        $('body').on('click', '.closePostPop', function(e) {
            e.preventDefault();
            closePopup();
        });

       

        // end dbee popup close function

        //espace press
        if (settings.closeBtnHide == false) {
            if (settings.overlay == true && settings.overlayClick == true) {

                $('.dbeePopupOverlay').on('click', function(e) {
                    closePopup();
                });
                $(document).keyup(function(e) {
                    if (e.keyCode == 27) {
                        closePopup();
                    } // esc

                });

            }
        }
        //espace press
    }


    $(window).resize(function() {
        $.dbeePopup('resize');
        windH = $(window).height();
        windW = $(window).width();
    });
    //end dbee popup function

    var os = navigator.platform;
    if ($.browser.msie) {
        ie = $.browser.version;


        if (ie < 10) {
            var massageOldBrowser = '<h2>Sorry, we see you are using an older verison of Internet Explorer. Please update to the latest version</h2>';
            $.dbeePopup(massageOldBrowser, {
                width: 400,
                closeBtnHide: true,
                otherBtn: '<a href="http://windows.microsoft.com/en-us/internet-explorer/download-ie" class="btn btn-green">Update Browser</a>'
            });
        }

    }




    $(document).ajaxSuccess(function(event, xhr, settings) {
        // $("body").getNiceScroll().resize();
        $dbTip();
    });

    var fbIcon = $('.socialfeedWidget .FacebookVisitor');
    var twIcon = $('.socialfeedWidget .TwitterVisitor');
    var linkIcon = $('.socialfeedWidget .LinkedinVisitor');
    var rssIcon = $('.socialfeedWidget .rssVisitor');

    var fbSt = $('#Facebookstatus').val();
    var twSt = $('#Twitterstatus').val();
    var linSt = $('#Linkedinstatus').val();
    var rssSt = $('#ShowRSS').val();

    twValue = $('#twitter_token_secret').val();
    fbValue = $('#facebookid').val();
    linkValue = $('#linkedin_token_secret').val();
    rssValue = 'test';

    //social feed btns
    showMyfeed = function() 
    {
          rssSt=0;
        if (SESS_USER_ID == adminID && SESS_USER_ID != '') 
        {
            twValue = '';        
            fbValue = '';        
        }
        if(allSocialstatus==1 || Social_Block=='YES' || (fbSt==1 && twSt==1 && linSt==1 && rssSt==0))
        {
            flagMyFeed = false;
            myFeedLink = '';
            fbValue=''
            twValue='';
            linkValue='';
            return false;
        }
        
        if(fbSt==1)
            fbValue = '';
        if(twSt==1)
            twValue = '';
        if(rssSt==0)
            rssValue = '';

        if (twValue != '' || fbValue != '' || linkValue != ''){
            flagMyFeed = true;
        } else {
            flagMyFeed = false;
            myFeedLink = '';
        }
        if(fbValue=='' && rssValue=='' && twValue==''){
            myFeedLink = '';
            flagMyFeed = false;
        }

    }
    showMyfeed();

    showPlatformfeed = function() 
    {     
        if (hidePlateFormSocialMenu==false) {
            flagPlatFeed = true;
        } else {           
            flagPlatFeed = false;
            platFormLink = '';
        }
    }


    nowShowPlatformfeed = function() {
        twitterFriend = false;
        faceBookFriend = false;
        linkedinFriend = false;
        rssIcon.hide();
        
        if (facebookpage_id != '') 
        {
            fbIcon.show();
            iamConnectWithFacebook = false;
            platformConnectWithFacebook = true;

        } else {
            fbIcon.hide();
            platformConnectWithFacebook = false;
        }
      

        rssIcon.show();
        if (twitterpage_id != '') 
        {
            twIcon.show();
            iamConnectWithTwitter = false;
            platformConnectWithTwitter = true;
        } else {
            twIcon.hide();
            platformConnectWithTwitter = false;
        }


    }

    checkFeedComing = function() 
    {
         platFormLink = '<a href="javascript:void(0);" data-type="Platform feed"><i class="fa fa-share-alt fa-lg"></i></a>';
          myFeedLink = '<a href="javascript:void(0);" data-type="my feed"><i class="fa fa-share-alt fa-lg"></i></a>'; 
        var drpHTML = '';
        if (SESS_USER_ID == adminID && SESS_USER_ID != '' && flagPlatFeed == true) {
            platFormLink = '<a href="javascript:void(0);" data-type="Platform feed"><i class="fa fa-share-alt fa-lg"></i></a>';
        }else if(flagPlatFeed == false){
            platFormLink = '';
        }

        // Temporarily set as true, may change later
        if(SESS_USER_ID!=adminID && flagMyFeed == true){ 
            myFeedLink = '<a href="javascript:void(0);" data-type="my feed"><i class="fa fa-share-alt fa-lg"></i></a>'; 
        }else{
            myFeedLink='';
        }

        // Temporarily set as true, may change later
        var pullRight =''; 
         if(isMobile==null){
            pullRight = '';
         }
        if (flagPlatFeed == true && flagMyFeed == true) {
            myFeedLink = '<a href="javascript:void(0);" data-type="my feed">My social feeds</a>'; 
            platFormLink = '<a href="javascript:void(0);" data-type="Platform feed">Platform feed</a>'; 
            drpHTML = '<li class="'+pullRight+' socialFeedMenuTop">\
                        <a href="#" class="sfeed"><i class="fa fa-share-alt fa-2x"></i></a>\
                        <ul>\
                            <li>' + myFeedLink + '</li>\
                            <li>' + platFormLink + '</li>\
                        </ul>\
                    </li>';
        } else {

            var linksFeeds = myFeedLink + platFormLink;  
            if(myFeedLink=='' || platFormLink==''){   
                drpHTML = '<li class="'+pullRight+' socialFeedMenuTop">' + linksFeeds + '</li>';    
            }else{
                myFeedLink = '<a href="javascript:void(0);" data-type="my feed">My social feeds</a>'; 
                platFormLink = '<a href="javascript:void(0);" data-type="Platform feed">Platform feed</a>'; 
                drpHTML = '<li class="'+pullRight+' socialFeedMenuTop">\
                        <a href="#" class="sfeed"><i class="fa fa-share-alt fa-2x"></i></a>\
                        <ul>\
                            <li>' + myFeedLink + '</li>\
                            <li>' + platFormLink + '</li>\
                        </ul>\
                    </li>';  
            }       
        }
       $('.socialFeedMenuTop').remove();
      
            if(isMobile==null){
                $('.topmessageMenu').after(drpHTML);
            }else{
               $('#leftsideMenu .mymenuItem').before(drpHTML);  
               $('#leftsideMenu ul ul').closest('li').addClass('subMenuTrue');
            }
      
       
        /*menuOver();*/

    }


    closeSDetails = function() {
        $('.fbClickDescription, .sideBarArrowFeed').fadeOut(function() {
            $(this).remove();
        });
    }
    closeSocialFeedSidebar = function() {
        closeSDetails();
        $('#sideBarFeeds').removeClass('activeSdFeeds');
        $('html').removeClass('activeSocialfeedSidebar');
        showMyfeed();
        iamLoginNow();
        checkFeedComing();
    }

    // init function
    if ($('#login').val()) 
    {
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: BASE_URL + "/dbeedetail/profanityfilter",
            success: function(response) 
            {
                filter = response.result;
                restUrl = response.getrestrictedurl;
                
                clientType = response.clientType;
                allowmultipleexperts = response.allowmultipleexperts;
                filterTwitter = response.fetchTwitter;
                
                if (response.admin_twitter_data == true)
                    twitterpage_id = 'yes';
                else
                    twitterpage_id = '';

                facebookpage_id = response.page_id;
                ShowRSS = response.ShowRSS;

                if(facebookpage_id=='' && twitterpage_id=='' && ShowRSS==0){
                     hidePlateFormSocialMenu = true;
                }
                else
                {
                    showPlatformfeed();
                    hidePlateFormSocialMenu = false;
                }
                checkFeedComing();
            }
        });
    }



    iamLoginNow = function() {
        twitterFriend = false;
        faceBookFriend = false;
        linkedinFriend = false;
        if (twValue != '' || fbValue != '' || linkValue != '' || rssValue !='') {

            if (twValue != '') {
                twIcon.show();
                platformConnectWithTwitter = false;
                iamConnectWithTwitter = true;
            } else {
                twIcon.hide();
                iamConnectWithTwitter = false;
            }
            if (fbValue != '') {
                fbIcon.show();
                platformConnectWithFacebook = false;
                iamConnectWithFacebook = true;

            } else {
                fbIcon.hide();
                iamConnectWithFacebook = false;
            }
            if (linkValue != '') {
                linkIcon.show();
                platformConnectWithLinkedin = false;
                iamConnectWithLinkedin = true;

            } else {
                linkIcon.hide();
                iamConnectWithLinkedin = false;
            }
            if (rssValue != '') {
                rssIcon.show();
            } else {
                rssIcon.hide();
            }
        }
    }





    // resize window script
    $(window).resize(function() {
        windH = $(this).height();
        windW = $(this).width();

        var footer = $('#footer').height() + 20;
      

        $('.pageContainer').css({
            hwacceleration: false,
            overflowx: false,
            minHeight: windH
        });
      
        $('.sideFeedWrp .feedWrpSideContent').perfectScrollbar('update');
        
        if (windW <= 979) {
            $('.headerMenu li').unbind('hover');
            $('.headerMenu ul').attr('style', '');


            enabledMobile = true;
            headerHeight = 85;
           


            $('#rssfeed-wrapper').perfectScrollbar('destroy');
            $('#rssfeed-wrapper').removeAttr('data-scroll');


            if ($('#leftsideMenu').is(':visible') == false) {
                $('a[data-id="mySideMenuBtn"]').remove()
            } else {
                $('a[data-id="mySideMenuBtn"]').show();
            }

            
          

        } else {
            enabledMobile = false

            $('#rssfeed-wrapper').perfectScrollbar('destroy');
            $('#rssfeed-wrapper').attr('data-scroll', 'true');
            $('#rssfeed-wrapper').perfectScrollbar();

            $('.headerMenu, #postMenuLinks').removeClass('on');
            $('html').removeClass('openMysideBar');
            $('body').removeClass('activeNoti');
          
            // show hide left and right container


            var showheaderMenu = false;
            /*menuOver();*/

        }


    }).resize();
    //end resize window script
$('#leftsideMenu ul ul').closest('li').addClass('subMenuTrue');
$('.headerMenu ul').closest('li').addClass('subMenuTrue');
$('body').on('click','#leftsideMenu ul li a, .headerMenu li a',function (){
    var thisEl = $(this);
    var subMenu = thisEl.closest('li').find('ul');
    var subMenu2 = thisEl.closest('ul');;
       if(subMenu.is(':visible')==true){
            thisEl.closest('li').removeClass('activeSubMenu');
       }else{
            $('.activeSubMenu', subMenu2).removeClass('activeSubMenu');
            thisEl.closest('li').addClass('activeSubMenu');
       }
});

$('#leftsideMenu ul ul a,#leftsideMenu #SurveysRightBtn, .showPoststream,  .headerMenu ul a').click(function (){
    $('.closeNavations').trigger('click');
     if($('html').hasClass('openMyRightSideBar')==true){
            $('[data-id="myRighSideBtn"]').trigger('click');
        }
});
    var notiPopUpHide = false;
    var notiPopUpHidemsg = false;
    // this is for notification dropdown list
    $('body').click(function(e) {
       if (!$(e.target).is('.headerMenu li * ')) {
        $('.headerMenu li').removeClass('activeSubMenu');
      }

        if (!$(e.target).is('#notificationsTop a') && !$(e.target).is('#notificationsTop a *')) {
            if (notiPopUpHide == true) {
                //$('#notiListing').hide();
                $('body').removeClass('activeNoti');
                startactInt();
                notiPopUpHide = false;
            }
             
            if (os == 'Win32') {
                $("#notiListing ul").getNiceScroll().hide();
            }
            
        }

        if (!$(e.target).is('[id="topmessageMenu"]') && !$(e.target).is('[id="topmessageMenu"] *')) {
          
                if (notiPopUpHidemsg == true) {
                $('body').removeClass('activeNotiMsg');
             $('[id="topmessageMenu"]').removeClass('activeNotification');
                notiPopUpHidemsg = false;
            }
            
              if (os == 'Win32') {                
                  $("#notiListingMsg ul").getNiceScroll().hide();
              }
            }

    });

    $('html').on('click', function(e) {
        if ($(e.target).html() != '') {
            closeSDetails();
            $('.dropDown.gtranslator').removeClass('on');

        }
    });

   

    $('body').on('click', '#leftsideMenu li', function() {

        $(this).closest('#leftsideMenu').find('.active').removeClass('active');
        if ($(this).closest('li').is(":visible") == true) {
            $(this).closest('li').addClass('active');
        } else {
            $(this).addClass('active');
        }
    });

    $('body').on('click', '#postMenuLinks li a', function() {
        $(this).closest('#postMenuLinks').find('.active').removeClass('active');
        $(this).addClass('active');
    });

    $('body').on('click', '.fbClickDescription,  .activeSdFeeds', function(event) {
        event.stopPropagation();
    });
    $('#notiListing').click(function(event) {
        event.stopPropagation()
    });


     


    function callnotificationAjax()
    {
        $('.notiContainerList').click(function(event){
              var hastag = location.hash.split('#');
               hastag = hastag[1];
              $('#repliesQA a[data-tab="'+hastag+'"]').trigger('click');
              
        });

        $('.viewDbeeApprove').click( function(e) {
            var thisEl = $(this);
            var dbeeid = thisEl.attr('data-id');
            var status = thisEl.attr('status');
            postURL = BASE_URL + "/admin/dashboard/singlepost";
            postData = {
                'dbeeid': dbeeid,
                'status': status 
            };
            e.preventDefault();
            $('body').click();
            $.ajax({
                url: postURL,
                data: postData,
                type: "POST",

                success: function(data) {
                    $.dbeePopup('<div class="dataListWrapper">'+data+'</div>',{ overlayClick: false,
                        closeBtnHide: true,
                        otherBtn: '<a href="javascript:void(0)" class="pull-left btn closePostPop" id="">Cancel</a><div class="pull-right btnGroup">\
                            <a class="btn  btn-black updateDbeeUserStatusApprove" href="javascript:void(0)" status="2" data-id="'+dbeeid+'" user_id="'+SESS_USER_ID+'"> Approve  </a></div>'});
                    $.dbeePopup('resize');
                              
                }
            });
        }); 



        //$('.updateDbeeUserStatusApprove').click( function(e) {
        $('body').on('click','.updateDbeeUserStatusApprove',function(e){
            var thisEl = $(this);
            var dbeeid = thisEl.attr('data-id');
            var status = thisEl.attr('status');
            postURL = BASE_URL + "/admin/dashboard/updatedbeestatus";
            postData = {
                'dbeeid': dbeeid,
                'status': status
            };
            e.preventDefault();
            $('body').click();
            
            $.ajax({
                url: postURL,
                data: postData,
                type: "POST",
                success: function(data) {
                    //$messageSuccess('Post approved successfully');
                                       
                    if(localTick == false && data.status == 2  ){ // for db approve case
                        socket.emit('checkdbee', true,clientID);
                        socket.emit('chkactivitynotification', true,clientID);
                    }

                    $.dbeePopup('close');
                    $('#actNotification [data-id="'+dbeeid+'"]').closest('.notiContainerList').remove();
                    storeNotifications = $('#actNotification').html();
                }
            });
              
        });

        $('.rejectExpertJoin').click(function(event) {
            event.preventDefault();
            var id = $(this).attr('data-id');            
            var Thhis = $(this);
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {"dbid": id},
                url: BASE_URL + '/myhome/removeinvite',
                cache: false,
                success: function(response) 
                {
                   Thhis.closest('.notiContainerList').remove();
                    storeNotifications = $('#actNotification').html();
                },
                error: function(error) {
                    loadError(error);
                }
            });
        });
        
        $('.acceptExpertJoin').click(function() 
        {
            var Thhis = $(this);
            var dbid = Thhis.attr('data-id'); 
            var pr = Thhis.closest('.notiContainerList');
            createrandontoken(); // creating user session and token for request pass
            data = 'acceptExpertRequest=true&dbid=' + dbid;
            
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: data,
                url: BASE_URL + '/expert/makedbeeexpert',
                success: function(response) 
                { 
                    if (response.used == 'used') 
                    {
                        socket.emit('getexpert',dbid,clientID,response.expertid);
                        $('.expertNotify', pr).html(response.dbeelink);
                        $('.invtExpert', pr).html('');
                        storeNotifications = $('#actNotification').html(); 
                        callsocket();
                        if(response.groupPost==1 || response.eventPost==1)
                                window.location.reload();
                    }
                    else if (response.status == 'error')
                    {
                        $dbConfirm({
                            content: response.message,
                            yes: false,
                            error: true
                        });
                        pr.remove();
                        $('#notiListing').hide();
                    }
                }
            });
        });

        $('.acceptAttendiesJoin').click(function(e) 
        {
            e.stopPropagation();
            
            var Thhis = $(this);
            var dbid = Thhis.attr('data-id'); 
            createrandontoken(); // creating user session and token for request pass
            data = 'acceptExpertRequest=true&dbid=' + dbid;
            
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: data,
                url: BASE_URL + '/expert/acceptvideoattendies',
                success: function(response) 
                { 
                    $('#AttendiesNotify'+dbid).html(response.dbeelink);
                     storeNotifications = $('#actNotification').html();
                    callsocket();
                }
            });
        });

        $('.rejectAttendiesJoin').click(function(event) {
            event.preventDefault();
            var id = $(this).attr('data-id');
            data = 'type=reject&id='+id;
            var Thhis = $(this);
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: data,
                url: BASE_URL + '/expert/rejectvideoattendies',
                cache: false,
                success: function(response) 
                {
                    Thhis.closest('.notiContainerList').remove();
                    storeNotifications = $('#actNotification').html();
                    callsocket();
                },
                error: function(error) {
                    loadError(error);
                }
            });
        });

        $('.acceptRequestAccept').on('click',function(){
        
            var type = $(this).attr('data-type');
            act_id = $(this).attr('data-act_id');
            db = $(this).attr('data-id');
            user = $(this).attr('user-id');
             var Thhis = $(this);
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {'act_id':act_id,'type':type,'db':db,'user':user},
                url: BASE_URL + '/expert/videorequest',
                cache: false,
                success: function(response) 
                {
                    callsocket();
                    Thhis.closest('.notiContainerList').remove();
                    storeNotifications = $('#actNotification').html();
                    socket.emit('updatevideonoti',clientID);
                }
            });
        });

        
        $('.acceptEventJoin').click(function(event) {
           event.preventDefault();
            var id = $(this).attr('data-id');
            createrandontoken(); // creating user session and token for request pass
            data = 'type=join&id='+id+'&'+userdetails
            var Thhis = $(this);
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: data,
                url: BASE_URL + '/event/privateevent',
                cache: false,
                success: function(response) 
                {
                   callsocket();
                   Thhis.closest('.notiContainerList').remove();
                    storeNotifications = $('#actNotification').html();
                },
                error: function(error) {
                    loadError(error);
                }
            });
        });

        $('.rejectEventJoin').click(function(event) {
            event.preventDefault();
            var id = $(this).attr('data-id');
            createrandontoken(); // creating user session and token for request pass
            data = 'type=reject&id='+id+'&'+userdetails
            var Thhis = $(this);
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: data,
                url: BASE_URL + '/event/privateevent',
                cache: false,
                success: function(response) 
                {
                    callsocket();
                    Thhis.closest('.notiContainerList').remove();
                    storeNotifications = $('#actNotification').html();
                },
                error: function(error) {
                    loadError(error);
                }
            });
        });
    }

$('body').on('click','#leftListing .editdb', function(e) {

            var thisEl = $(this);
            var dbeeid = thisEl.attr('id');
            var dbtext = thisEl.closest('.postListContent').find('.listTxtNew').html();

           $.dbeePopup('<div class="hdwrp">\
                                            <h2>Edit post</h2>\
                                        <div class="formRow textfiledPost">\
                                            <div class="formField">\
                                                <textarea id="exitText" class="mentionpost required"  placeholder="Add text">'+dbtext+'</textarea><input type="hidden" id="dbid" val="">\
                                            </div></div></div>',
                                            { overlayClick: false,closeBtnHide: true,
											load:function (){
                                                setTimeout(function (){
                                                  $('#exitText').focus().html(dbtext);
                                                },200);
                                              },
                        otherBtn: '<a href="javascript:void(0)" class="pull-left btn closePostPop" id="">Cancel</a><div class="pull-right btnGroup">\
                            <a class="btn  btn-black dbeditfrnt" href="javascript:void(0)" data-id="'+dbeeid+'" id="aklswin" > Update  </a></div>'});
           $.dbeePopup('resize');
		  
            //var status = thisEl.attr('status');
          
		$('.dbeditfrnt').click(function(e) {
            var El = $(this);
		   $('.fa',El).remove();
			El.removeClass('dbeditfrnt');
			El.append(' <i class="fa fa-spinner fa-spin"></i>');
            var textupdate;
            var dbeeid1 = $(this).attr('data-id');
            textupdate = $('#exitText').val();            
            postURL = BASE_URL + "/myhome/dbupdatetext";
            postData = {
                'dbeeid': dbeeid1,
                'text': textupdate 
            };
          
            $.ajax({
                url: postURL,
                data: postData,
                type: "POST",

                success: function(data) 
				{     					
				$('.fa-spin').remove();		
				El.addClass('dbeditfrnt');
                $('.fa',El).remove();
               
                    $('.closePostPop').trigger('click');
					$.dbeePopup('close');
                    //$messageSuccess('Post updated successfully'); 
                    thisEl.closest('.postListContent').find('.listTxtNew').html(textupdate);       
                }
            });
          });

        }); 
		
		

   
    $('body').on('click', '#notificationsTop a', function(event) {

        var thisOffest = $(this).offset();


        event.preventDefault();
        notiPopUpHide = true;
        clearactInt();
        $('body').removeClass('activeNotiMsg');
        topNotiCount = $('#actnotifications-top').text();
        if(topNotiCount!='') storeNotifications = false;
        var tothiglight = 0;
        var tothiglight = $('[id="actnotifications-top"]').html();
  
        $('li[id="notificationsTop"]').removeClass('activeNotification');
        siteHeadTitle = $('#siteHeadTitle').html();
        $('title').text(siteHeadTitle);
        $('body').addClass('activeNoti');
        $('#actnotifications-top-wrapper').hide();
        $('[id="actnotifications-top"]').html('');

        var data = "type=all&higlight="+tothiglight;
        if ($('body').hasClass('activeNoti') == true) {

            if (os == 'Win32') {
                $("#notiListing ul").getNiceScroll().show();
                $("#notiListing ul").niceScroll({
                    cursorcolor: "#000",
                    cursoropacitymax: 0.5,
                    cursorwidth: 5,
                    disableoutline: true
                });
                $('body').getNiceScroll().hide();
            }
        }
    
        if(storeNotifications==false)
        {    
            $.ajax({
                type: "POST",
                dataType: 'html',
                data: data,
                url: BASE_URL + '/notification/displayactivitynotification',
                //url: BASE_URL + '/notification/showactivitynotification',
                beforeSend: function() {
                    $('#notiListing').show().css('left', thisOffest.left);
                    $('[id="actNotification"]').html('');
                    $dbLoader('[id="actNotification"]', 3, '', 'center');
                },
                cache: false,
                success: function(response) {
                    $('[id="actNotification"]').html(response);
                    callnotificationAjax();
                    storeNotifications = response;
                    $('#btnsharereject').click(function(e) {    
                    var El = $(this);
                 
                        $rejetfilenoti(El,e);
                  });
                }
            });
        } else {
            $('#notiListing').show().css('left', thisOffest.left);
            $('[id="actNotification"]').html('');
            $dbLoader('[id="actNotification"]', 3, '', 'center');
            $('[id="actNotification"]').html(storeNotifications); 
            $('[id="actNotification"] .highlightNotification').removeClass('highlightNotification'); 
            callnotificationAjax();

        }

        $('.closeNoti').click(function(event) {
            $('body').removeClass('activeNoti');

        });


    });


    // end  notification dropdown list
    // message notification dropdown list

 $('body').on('click','#notification-feed .btnsharereject',function(e) {  
        var El = $(this);
        $rejetfilenoti(El,e);
 });

$rejetfilenoti = function(El,e){
        e.stopPropagation(e);
        e.preventDefault(e);                       
        //var El = $(this);

        var fileid = El.attr('fileid');
        var act = El.attr('actid');
        var datad = {"fileid":fileid,"act":act};
        
        $.ajax({
                type: "POST",
                dataType: 'json',
                data: datad,
                url: BASE_URL + '/Dashboarduser/removenotishare',
                success: function(response) {                                                  
                    storeNotifications = false;
                    El.closest('.notiContainerList').remove();
                    $('#notiListing').hide();
                }
            });
    }



    $('body').on('click', '[id="topmessageMenu"] > a', function(event) {   

          
        
        var thisOffest = $(this).offset();
        event.preventDefault();
        notiPopUpHidemsg = true;
        clearactInt();
        topNotiCountmsg = $('[id="actnotifications-msg-top"]').text();      
        var tothiglight = '200';
        var tothiglight = $('[id="actnotifications-msg-top"]').text();
  
        $('[id="topmessageMenu"]').removeClass('activeNotificationMsg');
        $('body').addClass('activeNotiMsg');
        $('[id="actnotifications-msg-top-wrapper"]').hide();
        $('[id="actnotifications-msg-top"]').html('');

        var data = "type=msg&higlight="+tothiglight;
        if ($('body').hasClass('activeNotiMsg') == true) {

            if (os == 'Win32') {
                $("#notiListingMsg ul").getNiceScroll().show();
                $("#notiListingMsg ul").niceScroll({
                    cursorcolor: "#000",
                    cursoropacitymax: 0.5,
                    cursorwidth: 5,
                    disableoutline: true
                });
                $('body').getNiceScroll().hide();
            }
        }
    
  
        if(storeNotificationsmsg==false)
        {        
             
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: data,
                url: BASE_URL + '/message/msgnotification',                
                beforeSend: function() {

                    $('[id="notiListingMsg"]').show().css('left', thisOffest.left);
                    $('[id="actNotificationMsg"]').html('');
                    $dbLoader('[id="actNotificationMsg"]', 3, '', 'center'); 

                },
                cache: false,
                success: function(response) {
                 
                    $('[id="notiListingMsg"]').css('visibility',"block");
                   $('[id="notiListingMsg"]').show().css('left', thisOffest.left);

                   var msg ='';
                   var msgcon = response.content;
                 if(response.content.length!=0){
                  // if(msgcon.Message.length>0){
                    $.each(response.content, function(i, value){
                       // console.log(value);
                       msg +='<div class="'+value.class+'" id="message-'+value.msgid+'" to="" from="" search="" read="0" fromadmin="'+value.Fromadmin+'" chkuser="'+value.ChkUser+'">\
                                <div class="notiUserpics">\
                                <img src="'+IMGPATH+'/users/small/'+value.pic+'" width="50" height="50" border="0" />\
                                </div><div class="notiUserContaint"><strong> '+value.userName+'</strong>\
                                sent you a new message<br>'+value.Message+'\
                                </div><div class="notificationDate">'+value.MsgDate+'</div></div>';
                                });
                    msg +='';

               }else{
                       msg +='<div class="notiContainerList" style="font-style: italic; text-align:center" > You currently have no messages </div>';

                }
                    $('[id="actNotificationMsg"]').html(msg);
                      storeNotificationsmsg = msg;
                }

            });
        } else {
            $('[id="notiListingMsg"]').show().css('left', thisOffest.left);
            $('[id="actNotificationMsg"]').html('');
            $dbLoader('[id="actNotificationMsg"]', 3, '', 'center');
            $('[id="actNotificationMsg"]').html(storeNotificationsmsg); 
            $('[id="actNotificationMsg"] .highlightNotification').removeClass('highlightNotification'); 
            callnotificationAjax();

            
    }

        $('.closeNotiMsg').click(function(event) {
            $('body').removeClass('activeNotiMsg');

        });

    });
    




    var nameUser = $('.userWelcomeMsg b').text();
    if (nameUser.length > 15) {
        $('.userWelcomeMsg ').css({
            fontSize: '18px'
        })
    }
    var nameAttach = [];
    var fixed = 'off';

    $('body').on('click', '.checkingaction', function(e) {

        var usertype = typeof($(this).attr('usertype')) != 'undefined' ? $(this).attr('usertype') : '0';
        var openchecking = typeof($(this).attr('currentholder')) != 'undefined' ? $(this).attr('currentholder') : 'on';
        //alert(usertype+' # '+usertype2);
        var privacy = typeof($(this).attr('groupprivacy')) != 'undefined' ? $(this).attr('groupprivacy') : '0';

        var privacy = privacy != '' ? privacy : '0';
        fixed = $('.checkingaction').attr('currentholder');
        if (fixed == 'on') {
            return false;
        }

        topthis = $(this);

         $messageWarning('', 'close');

        if (fixed == 'off') {

            if (usertype != 0 && usertype != 6 && usertype != 10 && usertype != 100 && privacy != 4 && hideIconsInCreatePost != true) {
                $dbConfirm({
                    content: 'This post will be published on the platform’s open forum, not in a VIP Group. Would you still like to post?',
                    yesClick: function() {
                        topthis.attr('currentholder', 'on');
                        $('.checkingaction').attr('currentholder', 'on');
                        $("#header .checkingaction:visible").click();
                        return false;
                    },
                    noClick: function() {
                        $('.checkingaction').attr('currentholder', 'off');
                        $messageWarning('', 'close');
                        return false;
                    }
                });
                $('#pressCloseConfirm').click(function() {

                    $('.checkingaction').attr('currentholder', 'off');

                    return false;
                });
                return false;
            } else {

                $('.checkingaction').attr('currentholder', 'on');
                $("#header .checkingaction:visible").click();

            };
        } 
    });

    // this is function use for get link data 
    $linkCaptureData = function(dataUrl, containerID, inputID) {
            var dataUrl = $.trim(dataUrl);

            if (dataUrl == '') 
                return false;
            
           

            $('#closeLinkUrl.byDefaultClose').hide();
            $(containerID).html('').show().css('min-height', '100px');
            $dbLoader(containerID);
            $(containerID + ' #dbLoaderContainer').css('height', '100px')
            if (dataUrl.indexOf("http") != '-1')
                var murl = dataUrl;
            else 
                var murl = 'http://' + dataUrl;
            var videoUrl = murl;

            var p = /^(?:https?:\/\/)?(?:www\.)?youtube\.com\/watch\?(?=.*v=((\w|-){11}))(?:\S+)?$/;
            var matches = (videoUrl.match(p)) ? RegExp.$1 : 10;

           /* var videoid = videoUrl.match(/(?:https?:\/{2})?(?:w{3}\.)?youtu(?:be)?\.(?:com|be)(?:\/watch\?v=|\/)([^\s&]+)/);
            if(videoid != null) {
              // console.log("video id = ",videoid[1]);
            } else { 
                matches=10;
            }*/

            var url = BASE_URL + "/myhome/linkdetail";
            var data = "dbeeurl=" + murl;
            $('#attchedLinked').show();
            if ($(containerID).closest('.minPostTopBar').is(':visible') == true) $('#dbee_comment').focus();

            if (matches == 10) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    success: function(response) 
                    {
                        $(containerID).removeClass('loader');
                        if ($(containerID).closest('.minPostTopBar').is(':visible') == true) $('#dbee_comment').focus();
                        $dbLoader(containerID, 1, 'close');
                        var GetResult = response;
                        if (GetResult != '0' && GetResult != '-1' && GetResult != '' && GetResult != '-2') 
                        {
                            $('#attachlinkerror').hide();
                            $(containerID).html(GetResult);
                            if ($('#dbeePopupWrapper').is(':visible') == true) {
                                setTimeout(function() {
                                    $.dbeePopup('resize');
                                }, 100);
                            }
                        } else {
                            var errorMessageUrl = '';

                            if (GetResult == '0' || GetResult == '') {
                                errorMessageUrl = 'he website addresss was not found.';
                            } else if (GetResult == '-1') {
                                errorMessageUrl = 'Please add a YouTube URL';
                            } else if (GetResult == '-2') {
                                errorMessageUrl = 'This website is restricted. Please share another link.';
                            }

                            $(containerID).removeAttr('style').append('<div class="error-dbeelink">' + errorMessageUrl + '</div>');
                            $(inputID).val('').focus();
                            $('#closeLinkUrl.byDefaultClose').show();
                            setTimeout(function(){
                                 $(containerID).fadeOut(function(){
                                    $('#attchedLinked').hide();
                                 });

                            }, 3000);
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
                        if ($(containerID).closest('.minPostTopBar').is(':visible') == true) $('#dbee_comment').focus();
                        var htmlVid = '<div class="makelinkWrp">\
                                                <div class="removeCircle" id="closeLinkUrl">\
                                                    <span class="fa-stack">\
                                                      <i class="fa fa-circle fa-stack-2x"></i>\
                                                      <i class="fa fa-times fa-stack-1x fa-inverse"></i>\
                                                    </span>\
                                                </div>\
                                        <input type="hidden"  value="' + title + '" id="youtubetitle"/><input type="hidden"  value="' + matches + '" id="vid"/><input type="hidden"  value="Youtube" id="videosite"/><input type="hidden"  value="' + description + '" id="youtubedescription"/><img border="0" src="http://img.youtube.com/vi/' + matches + '/default.jpg">\
                                        <div class="makelinkDes" ">\
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
            $.dbeePopup('resize');

            $('body').on('click', '#closeLinkUrl', function(event) {
                $(inputID).val('');
                $(containerID).removeAttr('style');
                var cmntBx = $(this).closest('.dbpostWrp').find('.cmntLinkDetailsWrp');
                if (cmntBx.is(':visible') == true) {
                    cmntBx.remove();
                    $('.cmntLinkBtn').removeClass('active');
                }
                $('#replytype').val('text');
                $(this).closest('.makelinkWrp').remove();
                $('#attchedLinked').hide();

                setTimeout(function() {
                    $.dbeePopup('resize');
                }, 200);
            });
        }
        // this is function use for get link data end

    $('body').on('click', '.startdbfromrss', function(e) {

        id = $(this).attr('count');

        var prnt = $(this).closest('.rss-container');
        var desRSS = $('p', prnt).text();
        var titleRSS = $('h3', prnt).html();
        var urlRSS = $('h3 a', prnt).attr('href');

        var usertype = typeof($('.checkingaction').attr('usertype')) != 'undefined' ? $('.checkingaction').attr('usertype') : '0';
        var openchecking = typeof($('.checkingaction').attr('currentholder')) != 'undefined' ? $('.checkingaction').attr('currentholder') : 'on';

        var privacy = typeof($('.checkingaction').attr('groupprivacy')) != 'undefined' ? $('.checkingaction').attr('groupprivacy') : '0';

        var privacy = privacy != '' ? privacy : '0';
        fixed = $('.checkingaction').attr('currentholder');
        if (fixed == 'on') {
            return false;
        }

        topthis = $('.checkingaction');


        $messageWarning('', 'close');

        if (fixed == 'off') {
            if (usertype != 0 && usertype != 6 && usertype != 10 && usertype != 100  && privacy != 4) {
                /*$messageWarning('<div class="">This post will be published within the open platform. Would you still like to post?<div class="hadernotifylink"><a id="thisPostPublic"  class="btn btn-yellow" href="javascript:void(0)">Yes</a><a id="thisPostPublicno"  class="btn btn-yellow" href="javascript:void(0)">No</a></div>');
                        $('#thisPostPublic').click(function(){
                         topthis.attr('currentholder','on');
                         $('.checkingaction').attr('currentholder','on');
                         $messageWarning('', 'close');
                         $("#header .checkingaction:visible").click();
                            $("#dbRssWrapper").parent().show();
                            $("#dbRssWrapper #dbRssTitle").html('<a href="' + $('#rsshiddenlink' + id).val() + '" target="_blank" >' + $('#rsshiddentitle' + id).val() + '</a>');
                            $("#dbRssWrapper #dbRssDesc").html($('#rsshiddendesc' + id).val() + ' <a href="' + $('#rsshiddenlink' + id).val() + '" target="_blank">read more</a>');
                            $("#rssadded").val(1);
                            return false;
                        });
                        $('#thisPostPublicno').click(function(){
                         $messageWarning('', 'close');
                         $.dbeePopup('close');
                         $('.checkingaction').attr('currentholder','off');
                         return false; 
                        });*/


                $dbConfirm({
                    //content:'This post will be published within the open platform. Would you still like to post?',
                    content: 'This post will be published on the platform’s open forum, not in a VIP Group. Would you still like to post?',
                    yesClick: function() {
                        topthis.attr('currentholder', 'on');
                        $('.checkingaction').attr('currentholder', 'on');
                        $messageWarning('', 'close');
                        $("#header .checkingaction:visible").click();
                        $("#dbRssWrapper").parent().show();
                        /*$("#dbRssWrapper #dbRssTitle").html('<a href="' + $('#rsshiddenlink' + id).val() + '" target="_blank" >' + $('#rsshiddentitle' + id).val() + '</a>');
                        $("#dbRssWrapper #dbRssDesc").html($('#rsshiddendesc' + id).val() + ' <a href="' + $('#rsshiddenlink' + id).val() + '" target="_blank"> more</a>');*/
                         $("#dbRssWrapper #dbRssTitle").html(titleRSS);
                        $("#dbRssWrapper #dbRssDesc").html(desRSS + ' <a href="' + urlRSS + '" target="_blank"> more</a>');
                        $("#rssadded").val(1);
                        return false;
                    },
                    noClick: function() {
                        $messageWarning('', 'close');
                        $('.checkingaction').attr('currentholder', 'off');
                        return false;
                    }
                });

                return false;
            } else {
                $('.checkingaction').attr('currentholder', 'on');
                $("#header .checkingaction:visible").click();
                $("#dbRssWrapper").parent().show();
                /*$("#dbRssWrapper #dbRssTitle").html('<a href="' + $('#rsshiddenlink' + id).val() + '" target="_blank" >' + $('#rsshiddentitle' + id).val() + '</a>');
                $("#dbRssWrapper #dbRssDesc").html($('#rsshiddendesc' + id).val() + ' <a href="' + $('#rsshiddenlink' + id).val() + '" target="_blank"> more</a>');*/

                $("#dbRssWrapper #dbRssTitle").html(titleRSS);
                $("#dbRssWrapper #dbRssDesc").html(desRSS + ' <a href="' + urlRSS + '" target="_blank"> more</a>');

                $("#rssadded").val(1);
                return false;

            };
        }


        $('html,body').animate({
            scrollTop: 0
        });
    });



    $('body').on('click', '.postTypeIcons a, .hdwrp .pollBtnForPopup,  #globalstartdbee, #dbPostPopHeader a, #header .checkingaction:visible:not([currentholder="off"])', function(e) {
        var activeClass = '';
        var type = $(this).attr('rel');
        if (fixed != 'on') {
            return false;
        }
        
       var privateEventGroupPost=0;
        if(controllernamed=='event' || controllernamed=='group')
        {
            var EventType = $('.postInGroup').attr('data-EventType');
            EventType = typeof(EventType) != 'undefined' ? EventType : '';
            var GroupType = $('#grouptype').val();
            GroupType = typeof(GroupType) != 'undefined' ? GroupType : '';
            if(EventType==3)
            {
               hideIconsInCreatePost = true;
               privateEventGroupPost=1;
               var dataeventid = $('.postInGroup').attr('data-id');
               dataeventid = typeof(dataeventid) != 'undefined' ? dataeventid : '';
                   $.ajax({
                      type: "POST",
                      dataType: 'json',
                      data: {
                          "DbArray": 2,"id": dataeventid                   
                      },
                      url: BASE_URL + "/ajax/usermentionpost",
                      success: function(response) {
                          var datares = response.html.userlist;
                           $('#mentionpostuser').val(datares);    
                      }
                  });
            }
           
            if(GroupType==2)
            {
               privateEventGroupPost=1;
               hideIconsInCreatePost = true;
               var datagroupid = $('.postInGroup').attr('data-groupid');
               datagroupid = typeof(datagroupid) != 'undefined' ? datagroupid : '';
               $.ajax({
                      type: "POST",
                      dataType: 'json',
                      data: {
                          "DbArray": 3,"id": datagroupid                   
                      },
                      url: BASE_URL + "/ajax/usermentionpost",
                      success: function(response) {
                          var datares = response.html.userlist;
                           $('#mentionpostuser').val(datares);    
                      }
                  });
            }
            
          
        }
        

        userID = SESS_USER_ID;
        $messageWarning('', 'close');
        var notOpenPostDb = typeof($('#openPostDb').val()) != 'undefined' ? $('#openPostDb').val() : '0';

        if (hideIconsInCreatePost == true) {

            if (notOpenPostDb == 0) {
                hideIconsInCreatePost = false;

                $dbConfirm({
                    content: 'Sorry you cannot post at the moment',
                    yes: false
                });

                $('.checkingaction').attr('currentholder', 'off');
                return false;
            }

        }

        e.preventDefault();

        var IsCreatePolls = '1';
        IsCreatePolls = $('.popOver').attr('data-allow_user_create_polls');
        var pollicon = '';

        if (IsCreatePolls == 1 || SESS_USER_ID == adminID){

            pollicon = '<i class="fa fa-signal"></i>';

            }
        

        $('.checkingaction').attr('currentholder', 'off');
        var tip = 'rel="dbTip"';
        if(isMobile){
                tip = '';
            }
        var PicIcon = '<a href="javascript:void(0);" '+tip+' title="Insert picture in post" class="picUplaoderPopup pull-right">\
                                            <i class="fa fa-camera"></i>\
                                            <input type="input" name="PostPix" id="PostPix_" /><form action="/file-upload" class="dropzone" id="uploadDropzone">\
                                                   <div class="fallback">\
                                                    <input type="file" name="file" id="postPix" />\
                                                  </div>\
                                                </form>\
                                            </a>';
       
        if(groupidmaster==0){
            if($(this).hasClass('pollBtnForPopup')==false){
                groupidmaster = $(this).attr('data-groupid');
            }
           }
        
        if ($.isNumeric(groupidmaster))
            //hideIconsInCreatePost = true;

        if (hideIconsInCreatePost == false) {
            var postTypePoll = '';
            var IsLeagueOn = '0';
            

            

            IsLeagueOn = $(this).attr('data-IsLeagueOn');
            
            var postInGroupIcon = '<a href="javascript:void(0);"  '+tip+' title="Post in a Group" data-type="postingrouup"  class="addonpost pull-right ' + activeClass + '"><i class="fa fa-users"></i></a>';
            if (SESS_USER_ID == adminID && clientID!=19) {
                var postInEventIcon = '<a href="javascript:void(0);"  '+tip+' title="Post in an event" data-type="postinevent"  class="addonpost pull-right ' + activeClass + '"><i class="fa fa-calendar"></i></a>';
            } else {
                var postInEventIcon = '';
            }
            if (IsLeagueOn == 404) {
                var postInLeagueIcon = '<a href="javascript:void(0);"  '+tip+' title="Post in a league" data-type="postinleague"  class="addonpost pull-right ' + activeClass + '"><i class="fa fa-shield"></i></a>';
            } else {
                var postInLeagueIcon = '';
            }

            var isgrouppost = '';

        } 
        else if (groupidmaster != '')
        {
            var postInLeagueIcon = '';
            var postInGroupIcon = '';
            var postInEventIcon = '';
            hideIconsInCreatePost = false;
            var isgrouppost = '<input type="hidden" name="isgrouppost" id="isgrouppost" value="true"/>';
        }
        
        var Logintype = $('#Logintype').val();
        var typeTitle = $(this).attr('data-title');
        var typeIcon = '';
        var popCnt = '';
        var popbtn = '';
        var relValue = 'dbpolls';
        $(this).closest('li').addClass('active');
        $('.postTypeBtn').attr('data-active', type);
        var postTypeHTML = '';

        if (type == 'dbtext') {
            var Type = 'Text';
            typeIcon = 'postSprite postText';
        }
        var backbut = '';
        if (type == 'dbpolls') {
            var Type = 'Polls';
           
            typeIcon = 'postSprite postPolls';
            PicIcon = '';
            relValue = 'dbtext';
            activeClass = 'active';
            if(privateEventGroupPost==1){
                postInEventIcon='';
                postInGroupIcon='';
            }
            backbut = '<a href="javascript:void(0)" class="backbutPop active pollBtnForPopup pull-right" rel="dbtext"><i class="fa fa-arrow-circle-left "></i> Back </a>';


        }
         $('#dbeetype').val(Type)

        var Social_Content_Block = $('#Social_Content_Block').val();
        postInGroupIcon = typeof(postInGroupIcon) != 'undefined' ? postInGroupIcon : '';
        isgrouppost = typeof(isgrouppost) != 'undefined' ? isgrouppost : '';
        
        postInEventIcon = typeof(postInEventIcon) != 'undefined' ? postInEventIcon : '';
        backbut = typeof(backbut) != 'undefined' ? backbut : '';
        postInLeagueIcon = typeof(postInLeagueIcon) != 'undefined' ? postInLeagueIcon : '';
        var headerPupup = '<div class="hdwrp">\
                                            <h2>Create post \
                                            <a href="javascript:void(0);"  rel="' + relValue + '" class="pull-right pollBtnForPopup ' + activeClass + '">' + pollicon + '</a>' + PicIcon + postInEventIcon + postInGroupIcon + postInLeagueIcon + backbut + '\
                                            </h2>\
                                        </div>';            
        var headerPollPupup = '<div class="hdwrp">\
                                            <h2>Create poll \
                                            <a href="javascript:void(0);"  rel="' + relValue + '" class="pull-right pollBtnForPopup ' + activeClass + '">' + pollicon + '</a>' + PicIcon + postInEventIcon + postInGroupIcon + postInLeagueIcon + backbut + '\
                                            </h2>\
                                        </div>';

        var postTypeText = '<div class="formRow textfiledPost">' + isgrouppost + '\
                                            <input type="hidden" id="dbeetype" value="' + Type + '">\
                                            <div class="formField">\
                                                <textarea id="PostText" class="mentionpost required"  placeholder="Add text"></textarea><input type="hidden" id="postmentionsuser" val="">\
                                            </div>\
                                        </div>\
                                        <div class="formRow" style="display:none; padding:0px;">\
                                            <div id="dbRssWrapper" class="dbRssWrapperFeed" style="display:block; width:auto; position:relative;">\
                                                <div onclick="javascript:removedbrss()" class="removedbrss"></div>\
                                                <i class="fa fa-rss fa-2x pull-left"></i>\
                                                <div class="rssBoxCnt">\
                                                    <div id="dbRssTitle"></div>\
                                                    <div id="dbRssDesc"></div>\
                                                </div>\
                                            </div>\
                                        </div>';

        var postTypePollText = '<div class="formRow textfiledPost">' + isgrouppost + '\
                                            <input type="hidden" id="dbeetype" value="' + Type + '">\
                                            <div class="formField">\
                                                <textarea maxlength="500" id="PostText" placeholder="Add question or statement" class="required"></textarea>\
                                            </div>\
                                        </div>\
                                        <div class="formRow" style="display:none; padding:0px;">\
                                            <div id="dbRssWrapper" class="dbRssWrapperFeed" style="display:block; width:auto; position:relative;">\
                                                <div onclick="javascript:removedbrss()" class="removedbrss"></div>\
                                                <i class="fa fa-rss fa-2x pull-left"></i>\
                                                <div class="rssBoxCnt">\
                                                    <div id="dbRssTitle"></div>\
                                                    <div id="dbRssDesc"></div>\
                                                </div>\
                                            </div>\
                                        </div>';


        var postTypePoll = '<div class="reqrow">\
                                            <div class="txtContainer pollsfields">Choose your poll options below</div>\
                                            <div class="formField pollsfields">\
                                                <input type="text" placeholder="Option 1" id="poll-option-1" class="required" >\
                                                <input type="text" placeholder="Option 2" id="poll-option-2" class="required">\
                                                <input type="text" placeholder="Option 3" id="poll-option-3" class="required" >\
                                                <input type="text" placeholder="Option 4" id="poll-option-4" class="required">\
                                            </div>\
                                        </div>';

        var DbeeTags ='';
        if(clientID == 19)
        {
            var DbeeTags = '<div class="formRow">\
                                            <div class="formField">\
                                                <div class="twitterHas">\
                                                    <i class="postpopupIcon fa fa-tag fa-lg"></i>\
                                                    <input type="text" id="dbee-tag-text" placeholder="#tag - post">\
                                                    <i class="postpopupIcon pstTwrIcon"></i>\
                                                </div>\
                                            </div>\
                                        </div>';
        }                              
        var dbPostLink = ' <div class="formRow dbpostLinkRow">\
                                            <div class="formField">\
                                                <div class="twitterHas">\
                                                    <input type="text" id="PostLink" placeholder="Add URL">\
                                                    <i class="postpopupIcon  fa fa-link fa-lg"></i>\
                                                </div>\
                                            </div>\
                                        </div><div class="formRow" id="attchedLinked" style="display:none;">\
                                            <div class="formField">\
                                                <div align="center" id="attachlinkloader" style="display:none; padding-top:10px;"><i class="fa fa-spin fa-spinner"></i></div>\
                                                <div align="center" id="attachlinkerror" class="error-dbeelink" style="display:none; margin-left:5px;">\
                                                  <div class="link-post-error-arrow" style="margin-left:5px;"></div>\
                                                  <div id="attachlinkerror-text" style="position:relative;">The website address was not found</div>\
                                                </div>\
                                                <div  id="LinkDesc1" class="txtContainer"></div>\
                                            </div>\
                                        </div>';
        var twitterHasTage ='';
        if($('#twitter_token_secret').val() != '')
        {
            var twitterHasTage = '<div class="formRow twitterHasMain twitterHastag">\
                                              <span class="dbHasHd">Twitter</span>\
                                            <div class="formField">\
                                                <div class="twitterHas">\
                                                    <i class="fa dbTwitterIcon fa-lg postpopupIcon "></i>\
                                                    <input type="text" id="twitter-tag-text" placeholder="#tag - Twitter">\
                                                    <i class="postpopupIcon pstTwrIcon"></i>\
                                                </div>\
                                            </div>\
                                        </div>';
        }

        var attachfile = $('#attachmentlistforsave').val();

        var attachment = '<div class="formRow" class="postCatWrapper"><a href="javascript:void(0)" class="Attachfile"><i class="postpopupIcon fa fa-folder fa-lg"></i> Attach file <span></span> <i class="optionalText">Optional</i></a> <div id="allAttachedFiles"></div></div>';
        if (SESS_USER_ID != adminID) {
            attachment = '';
        }
     
        if($('#allowedPostWithTwitter').val()==0)
        {                     
            if (Social_Content_Block == 'block' || filterTwitter == 0) {
                twitterHasTage = '';
            }
        }

        if($('#allSocialstatus').val()==1 && $('#Twitterstatus').val()==1)
        {
             twitterHasTage = '';
        }

        var dbHasTage = '<div class="formRow dbHasTage">\
                                        <div class="formField">\
                                            <ul id="DbTag"></ul>\
                                            <i class="postpopupIcon fa fa-tag fa-lg"></i>\
                                        </div>\
                                    </div>';

        var sharecheckbox = '';

        if(linSt==0)
        {
            if (linkValue != '' && Social_Content_Block != 'block') {
                sharecheckbox += '<div class="checkboxWrpSl"><label for="isPostOnLinkedin"><input type="checkbox" name="isPostOnLinkedin" id="isPostOnLinkedin" ><label for="isPostOnLinkedin"></label><i class="fa dbLinkedInIcon"></i></label></div>';
            } else {
                sharecheckbox += '<div class="checkboxWrpSl"><label for="isPostOnLinkedin"><input type="checkbox" disabled  name="isPostOnLinkedin" id="isPostOnLinkedin" ><label for="isPostOnLinkedin"></label><i class="fa dbLinkedInIcon"></i></label></div>';
            }
        }
        else
        {
           sharecheckbox +=''; 
        }

        if(twSt==0)
        {
            if (twValue != '' && Social_Content_Block != 'block') {
                sharecheckbox += '<div class="checkboxWrpSl" ><label for="isPostOnTwitter"><input  type="checkbox" name="isPostOnTwitter" id="isPostOnTwitter" ><label for="isPostOnTwitter"></label><i class="fa dbTwitterIcon"></i></label></div>';
            } else {
                sharecheckbox += '<div class="checkboxWrpSl"><label for="isPostOnTwitter"><input  type="checkbox" disabled name="isPostOnTwitter" id="isPostOnTwitter" ><label for="isPostOnTwitter"></label><i class="fa dbTwitterIcon"></i></label></div>';
            }
        }
        else
        {
           sharecheckbox +=''; 
        }

        if(fbSt==0)
        {
            if (fbValue != '' && Social_Content_Block != 'block') {
                sharecheckbox += '<div class="checkboxWrpSl"><label for="isPostOnFacebook"><input type="checkbox" name="isPostOnFacebook" id="isPostOnFacebook" ><label for="isPostOnFacebook"></label><i class="fa dbFacebookIcon"></i></label></div>';
            } else {
                sharecheckbox += '<div class="checkboxWrpSl"><label for="isPostOnFacebook"><input type="checkbox" disabled name="isPostOnFacebook" id="isPostOnFacebook" ><label for="isPostOnFacebook"></label><i class="fa dbFacebookIcon"></i></label></div>';
            }
        }
        else
        {
            sharecheckbox +='';
        }

        var socialNoteText = '<br><small style="font-size:11px; color:#ccc">Link your social accounts to share your post</small>';
        if (twValue != '' && fbValue != '' && Social_Content_Block != 'block') {
            socialNoteText = '';
        }


        if ((Social_Block == 0) && (fbSt==0 || twSt==0 || linSt==0))
            var dbshareOnPost = '<div class="formRow dbshareonPost">\
                                            <div class="formField twitterHas">\
                                                <div class="spoLabel"><i class="postpopupIcon fa fa-share-square-o fa-lg"></i><span>Social share</span>' + socialNoteText + '</div>\
                                                ' + sharecheckbox + '\
                                            </div>\
                                        </div>';
        else
            dbshareOnPost = '';

        //var categoryList=getCategoryList;

        var dbPosttaguser='<div class="formRow dbPosttaguser">\
                                            <div class="formField">\
                                                 <input type="hidden" id="_submit_tag_names_text" value="" name="_submit_tag_names">\
                                            </div>\
                                        </div>';

        var dbmentiononPost = ' ';
        if(twitterHasTage=='')
        {
         
         var styletwit='style="margin-top:0px;"';
         var texthead ='<span class="dbHasHd">Twitter</span>';
        }
        else
        {
            var texthead ='';
            var styletwit='';
        }

        if(twValue != '')
        {
           dbmentiononPost = '<div class="formRow twitterHasMain twitterHasmention" '+styletwit+'>\
                                            '+texthead+'<div class="formField">\
                                               <div class="twitterHas"><i class="fa fa-user fa-lg postpopupIcon"></i><input type="text" id="dbmentiononPost" value="" placeholder="@username invitation" class="tagit-hidden-field"></div>\
                                            </div>\
                                        </div>';
         }

         if (Social_Content_Block == 'block') {

            dbmentiononPost='';
         }

          if (SESS_USER_ID == adminID) {
           getUserSetList=getUserSetList;
        }
        else
        {
            getUserSetList='';
        }

        if(privateEventGroupPost==1)
        {
            getUserSetList='';
        }
         
        if(SESS_USER_ID == adminID)
        {
          QASTATUS = '<div class="formRow"><div class="formField"><div class="spoLabel" style="margin-left:11px;"><label for="QASTATUS"><input type="checkbox" name="QASTATUS" id="QASTATUS" ><label for="QASTATUS"></label><i>Make this post Q&A</i></label></div></div></div><div class="qatime"></div>';
        }

        $('body').on('click', '#QASTATUS', function(e)
        {
            if($('input:checkbox[name=QASTATUS]').is(':checked') == true && SESS_USER_ID == adminID) 
            {
                  $('.qatime').html('<div class="formRow dbpostLinkRow">\
                              <div class="formField">\
                                  <div class="twitterHas">\
                                      <input type="text" id="qatime" placeholder="Select Q&A start date and time">\
                                      <i class="postpopupIcon  fa fa-calendar fa-lg"></i>\
                                  </div>\
                              </div>\
                            </div><div class="formRow dbpostLinkRow">\
                              <div class="formField">\
                                  <div class="twitterHas">\
                                      <input type="text" id="qaendtime" placeholder="Select Q&A end date and time">\
                                      <i class="postpopupIcon  fa fa-calendar fa-lg"></i>\
                                  </div>\
                              </div>\
                            </div>');
                  $('#qatime').datetimepicker({dateFormat: "dd-mm-yy",minDate: 0});
                  $('#qaendtime').datetimepicker({dateFormat: "dd-mm-yy",minDate: 0});
            }else{
              $('.qatime').html('');
            }
           $.dbeePopup('resize');
        });

        if (type == 'dbtext') { 
            postTypeHTML = headerPupup + postTypeText +DbeeTags+dbPostLink + dbHasTage + twitterHasTage + dbmentiononPost + dbshareOnPost + getCategoryList + getUserSetList + attachment+QASTATUS;
        } else if (type == 'dbpolls') {
            postTypeHTML = headerPollPupup + postTypePollText + postTypePoll + getCategoryList;
        } else if (type == 'postInGroup') {
            postTypeHTML = headerPupup + getGroupList;
        } else if (type == 'postinevent') {
            postTypeHTML = headerPupup + geteventList;
        } else if (type == 'postInLeague') {
            postTypeHTML = headerPupup + getLeagueList;
        }




        var adminapprowtext = '';
        var adminapprove = $(this).attr('data-adminapprove');

        if (adminapprove == 1) {
            adminapprowtext = '<div class="clearfix"></div><div>Once submitted, your post will be reviewed by platform admin and made live after approval.</div>';
        }

        var postTypeWrapperHTML = '<div class="" data-popup="' + type + '">\
                                                    <div class="postTypeContent" id="postTypeContent">' + postTypeHTML + adminapprowtext + '</div>\
                                            </div>';




        if ($(this).closest('.hdwrp').is(':visible') == false) {
            $.dbeePopup(postTypeWrapperHTML, {
                overlayClick: false,
                closeBtnHide: true,
                otherBtn: '<a href="javascript:void(0)" class="pull-left btn closePostPop" id="">Cancel</a><div class="pull-right btnGroup">\
                                    <select style="margin-right:10px; display:none" id="PrivatePost" name="PrivatePost"  class="pull-left PrivatePost" tabindex="-1" ><option value="0">Public</option><option value="1">Private</option></select><a href="javascript:void(0);" class="pull-left btn btn-black" onclick="postdbee(0);" id="insertmaindb" on="">Post</a></div>\
                                    <div id="signuploader" style="display:none; float:left; padding-top:5px; padding-right:10px;"><i class="fa fa-spin fa-spinner"></i></div>\
                                    <input type="hidden" value="" id="db-post-cat">'
            });

            setTimeout(function() {
                $.dbeePopup('resize');
            }, 100);

        } else {
            $('#postTypeContent').html(postTypeHTML);

            $.dbeePopup('resize');

        }
        $dbTip();
         
         $showPrivateOption = function (thisEl){
          
          var k =  $('.textfiledPost .mentionATag').text(); 
          var mentionTagLength = $('.twitterHasmention:not(.twitterHastag)  .tagit li').length;
          var usersetxx = typeof($('#userset').val()) != 'undefined' ? $('#userset').val() : '';
        
           if(usersetxx!='' && SESS_USER_ID==adminID && privateEventGroupPost==0)
           {
            $('#s2id_PrivatePost').show();
           }else if(k!='' ){
            $('#s2id_PrivatePost').show();
           }
           else
           {

             $('#s2id_PrivatePost').hide();
           }  
         }

         
        $('#PostText').keyup(function(event) {
          
           $showPrivateOption();

            $.dbeePopup('resize');
        }).focusin(function(){
          $showPrivateOption();
        });

        $('#userset').on('change', function() {
        
         $showPrivateOption();     

        });


        $(".twitterHasmention:not(.twitterHastag) .tagit-new input").width(400);
        $('#dbPostPopHeader li').removeClass('active');
        $('#dbPostPopHeader a[rel="' + type + '"]').closest('li').addClass('active');

        if (type == 'dbtext') {
            var fileList;
            var fileNameUpload = [];
            $('#uploadDropzone').dropzone({
                url: BASE_URL + "/myhome/imguplod",
                maxFiles: 5,
                addRemoveLinks: true,
                parallelUploads: 1,
                acceptedFiles: '.png, .jpg, .jpeg, .gif, .JPG, .JPEG, .PNG, .GIF',
                maxfilesexceeded: function(file, serverFileName) {
                    $dbConfirm({
                        content: serverFileName,
                        yes: false
                    });
                },
                error: function(file, serverFileName) {
                    $dbConfirm({
                        content: serverFileName,
                        yes: false
                    });
                },
                processing: function(file, serverFileName) {
                    if($('.imagePostsPopup').is(':visible')==false){
                        $('.imagePostsPopup').remove();
                        $('#postTypeContent .hdwrp').after('<div class="formRow imagePostsPopup first" style="height:100px;" ><ul></ul></div>');
                          
                    }
                    $('.addMorePicPost').remove();
                  var htmlThumb= '<li data-ckfilename="'+file.name+'" style="min-width:50px;"></li>';
                            
                              $('#postTypeContent .imagePostsPopup ul').append(htmlThumb);
                              $dbLoader('[data-ckfilename="'+file.name+'"]:last', 1);
                           
                  
                },
                success: function(file, serverFileName) {
                    fileList = "serverFileName = " + serverFileName;
                    fileNameUpload.push(serverFileName);
                    $('#PostPix_').val(fileNameUpload);
                    var htmlThumb2 = '<a href="javascript:void(0)"  data-fileName="' + serverFileName + '" class="removeCircle">\
                                                        <span class="fa-stack">\
                                                          <i class="fa fa-circle fa-stack-2x"></i>\
                                                          <i class="fa fa-times fa-stack-1x fa-inverse"></i>\
                                                        </span>\
                                                        </a>';
                      
                      var addMorePic = '<li class="addMorePicPost" style="min-width:50px;"><a href="#"><i class="fa fa-plus fa-2x"></i> <br>Add More</a></li>';
                                             $dbLoader('[data-ckfilename="'+file.name+'"]:last', 1, 'close');

                     $('#postTypeContent .imagePostsPopup ul li[data-ckfilename="'+file.name+'"]').css({background:'url(' + IMGPATH + '/imageposts/small/' + serverFileName + ')', backgroundRepeat:'no-repeat', backgroundSize:'cover'}).html(htmlThumb2);
                     $('#postTypeContent .imagePostsPopup ul').append(addMorePic);
                      if(fileNameUpload.length==5){
                              $('.picUplaoderPopup, .addMorePicPost').hide();
                          }
               

                    setTimeout(function() {
                        $.dbeePopup('resize');
                    }, 100);

                    $('.addMorePicPost').click(function(e) {                      
                      $('.picUplaoderPopup #uploadDropzone').trigger('click');
                    });

                   

                    this.removeFile(file);
                },
                thumbnail: function(ds, dataUrl) {
                    $('#uploadDropzone .dz-error').remove();
                }

            });



        } // this is end point of if type=='dbtext'

         $('body').on('click','.imagePostsPopup .removeCircle',function(e) {
              e.preventDefault();
              e.stopPropagation();
              var fileName = $(this).attr('data-fileName');
              var thisEl = $(this);
               thisEl.closest('li').hide();
               
               if(fileNameUpload.length<=5){
                   fileNameUpload.splice(fileNameUpload.indexOf(fileName), 1);
               }
              if(fileNameUpload.length<5){
                    $('.picUplaoderPopup, .addMorePicPost').show();
                }
                if(fileNameUpload.length==0){
                    $('.imagePostsPopup ').remove();
                }
            $('#PostPix_').val(fileNameUpload);
              $.ajax({
                  url: BASE_URL + "/myhome/imgunlink",
                  type: "POST",
                  data: {serverFileName_:fileName},
                  success: function() {
                      thisEl.closest('li').remove();
                      setTimeout(function() {
                          $.dbeePopup('resize');
                      }, 50);
                  }
              });
          });

        $('.addonpost').click(function(e) {
            e.preventDefault();
            var thisEl = $(this);
            thisEl.addClass('active');


            var addOnDataType = thisEl.attr('data-type');
            var htmlAddOnPost = '';
            if (addOnDataType == 'postingrouup') {

                htmlAddOnPost = getGroupList;
            }
            if (addOnDataType == 'postinevent') {
                htmlAddOnPost = geteventList;
            } else if (addOnDataType == 'postinleague') {
                htmlAddOnPost = getLeagueList;
            }

            if ($('#' + addOnDataType).is(':visible') == false) {
                $('#' + addOnDataType).remove();
                $('#postTypeContent .formRow:last').after(htmlAddOnPost);
                $('#postTypeContent #selectGroupList').append(newAddedGroups);


                $('.closeRowField').click(function(event) {
                    $(this).closest('.formRow').remove();
                    thisEl.removeClass('active');
                    setTimeout(function() {
                        $.dbeePopup('resize');
                    }, 200)
                });

                setTimeout(function() {
                    $.dbeePopup('resize');
                }, 200)
            }

        });

   
        $('.postContainer').addClass('bounceOpen');
        $('.postContainer').css({
            minHeight: $('.contentPostWrapper').outerHeight()
        });
        setTimeout(function() {
            $('#PostText').focus();
            $('.postContainer').css({
                height: 'auto'
            });
            $('.contentPostWrapper').fadeIn();
        }, 300);

        
        // hastag

        // hastag
        $("#DbTag").tagit({
            placeholderText:'#tag - Internal',
            afterTagAdded: function(event, ui) {
                    
                     $("#DbTag input").attr('placeholder', '');
            },
            onTagLimitExceeded:function(){
                $("#DbTag").next('.formErrorPop').remove();
                //$("#DbTag input").val('').attr('placeholder', '');
                $("#DbTag").after('<div class="formErrorPop">Sorry tag limit over</div>');
                setTimeout(function (){
                    $("#DbTag").next('.formErrorPop').remove();
                }, 2000);

            },
            onTagRemoved:function(){
               // $("#DbTag input").val('').attr('placeholder', '#tag - Internal');
                $("#DbTag").next('.formErrorPop').remove();
                //$(".twitterHasmention:not(.twitterHastag) ul.tagit").next('.formErrorPop').remove();
            },
            afterTagRemoved: function(event, ui) {
               
                    if($('ul#DbTag li').length==1)
                    {
                      $("#DbTag input").attr('placeholder', '#tag - Internal');
                    }
                }
        });
        $("#DbTag").focusout(function(event) {
            var e = jQuery.Event("keydown");
            e.which = 13; 
            $("#DbTag input").trigger(e);
        });
        

         $("#dbmentiononPost").tagit({
            placeholderText: '@username invitation',     
            afterTagAdded: function(event, ui) {                
                 $(".twitterHasmention:not(.twitterHastag) .tagit-new input").val('').attr('placeholder', '');
                 $('#s2id_PrivatePost').hide();
                  

            },
            onTagLimitExceeded: function() {                
                $(".twitterHasmention:not(.twitterHastag) ul.tagit").next('.formErrorPop').remove();
                $(".twitterHasmention:not(.twitterHastag) .tagit-new input").val('').attr('placeholder', '');

                $(".twitterHasmention:not(.twitterHastag) ul.tagit").after('<div class="formErrorPop">Sorry tag limit over</div>');                


                setTimeout(function() {
                    $(".twitterHasmention:not(.twitterHastag) ul.tagit").next('.formErrorPop').remove();
                }, 2000);

            },
            onTagRemoved: function() {
                //$("#DbTag input").val('').attr('placeholder', '#tag - Internal');
                $(".twitterHasmention:not(.twitterHastag) ul.tagit").next('.formErrorPop').remove();
            },
            afterTagRemoved: function() {
               
               if($('.twitterHasmention:not(.twitterHastag)  .tagit li').length==1)
                {
                   
                 $(".twitterHasmention:not(.twitterHastag) .tagit-new input").val('').attr('placeholder', '@username invitation');
                    var k =  $('#PostText').prev('.mentions').find('.mentionATag').text(); 
                    if(k!='') $('#s2id_PrivatePost').show();
                    else  $('#s2id_PrivatePost').hide();
                }
             
            }
        });
        $("#dbmentiononPost").focusout(function(event) {            
            var e = jQuery.Event("keydown");
            e.which = 13;          
            $("#dbmentiononPost input").trigger(e);
        });

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
                 $(".twitterHastag:not(.twitterHasmention).tagit-new input").val('').attr('placeholder', '#tag - Twitter (max 4)');
            }, 
            afterTagRemoved: function() {
                          
               if($('.twitterHastag:not(.twitterHasmention).tagit li').length==1)
                {
                 $(".twitterHastag:not(.twitterHasmention).tagit-new input").val('').attr('placeholder', '#tag - Twitter (max 4)');
                }
                 $(".twitterHastag:not(.twitterHasmention).tagit-new input").val('').attr('placeholder', '#tag - Twitter (max 4)');
            }
        });

        $("#twitter-tag-text").focusout(function(event) {
            var e = jQuery.Event("keydown");
            e.which = 13;
            $(".#twitter-tag-text input").trigger(e);
        });
        // end for twitter tag
       
    if(type!= 'dbpolls'){
        if( $('#s2id_PrivatePost').length!=1){
          privateHTMLPost();
         }
  }
  if(type == 'dbpolls'){   
    $('#s2id_PrivatePost').hide();
  }
 
  $makesselect2(this,{"ele":"#_submit_tag_names"});


  $('.mentionpost').mentionsInput({
         
            onDataRequest: function(mode, query, callback) {                
                var data = jQuery.parseJSON($('#mentionpostuser').val());
               
                responseDataMy = _.filter(data, function(item) {
                    return item.name.toLowerCase().indexOf(query.toLowerCase()) > -1
                });
                                  
                callback.call(this, responseDataMy);
            }

        });
  
        $makesselect2("#_submit_tag_names",this);


                
    });




      



    // This is for select media type like you tube, sound cloud...
    $('body').on('click', '#mediaTab a, #linkTab a', function() {
        var data = $(this).attr('rel');
        $(this).closest('div').find('.active').removeClass('active');
        $(this).addClass('active');
        $('[data-media]').hide();
        $('[data-media="' + data + '"]').show();

    });
    // End for select media type like you tube, sound cloud...


    // This is for open categories list

    $('body').on('click', '.Attachfile', function(e) {
        e.preventDefault();

        $("#dbeePopupWrapper").find('#sowattachment').remove();
        $("#dbeePopupWrapper .dbeePopContent").wrapInner("<div id='newWrapper'></div>");
        $("#dbeePopupWrapper .postTypeFooter").wrapInner("<div id='newFooterWrapper'></div>");
        $('#newWrapper').hide();
        $('#newWrapper').after('<div id="sowattachment" ></div>')
        popCnt = $('#newWrapper').detach();
        popBtn = $('#newFooterWrapper').detach();

        $.ajax({
            url: BASE_URL + '/myhome/getfolderslist',
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                $dbLoader('#sowattachment', 1, '', 'center');
                 $.dbeePopup('resize');

            },
            success: function(data) {
                $dbLoader('#sowattachment', 1, 'close');
                $('.postTypeFooter').html('<input type="hidden" name="attachmentlistforsave" id="attachmentlistforsave" value=""><div class="postTypeFooter attachedFilesFooter">\
                                       <a class="pull-left btn attachedFilesClose" href="#">Back</a><a id="attachedFiles" class="pull-right btn btn-yellow"  href="javascript:void(0)">Attach</a>\
                                    <div id="signuploader" style="display:none; float:left; padding-top:5px; padding-right:10px;">\
                                       <input id="db-post-cat" type="hidden" value="">\
                                       <div class="clearfix"></div>\
                                       ');
                $('#sowattachment').html(data.content);

                $.each(nameAttach, function(i, value) {
                    $('#sowattachment input[type="checkbox"][attname="' + value + '"]').attr('checked', true);
                });
                $.dbeePopup('resize');
            }


        });
    });


    $('body').on('click', '.attachedFilesClose', function(e) {
        e.preventDefault();
        $('#sowattachment').before(popCnt);
        $('.postTypeFooter').html(popBtn);
        $("#newWrapper").children().unwrap();
        $("#newFooterWrapper").children().unwrap();
        $('#sowattachment').remove();
        $('#allAttachedFiles').html('');
        nameAttach = [];
        $.dbeePopup('resize');
        //$.dbeePopup('resize');
    });

    $('body').on('click', '#attachedFiles', function(e) {
        e.preventDefault();
        var iconexe = {
                                'docx':'word',
                                'xlsx':'excel',
                                'xls':'excel',
                                'doc':'word',
                                'ppt':'powerpoint',
                                'txt':'text',
                                'jpeg':'image',
                                'jpg':'image',
                                'gif':'image',
                                'png':'image',
                                'mp3':'audio',
                                'mp4':'audio',
                                'pdf':'pdf'
                                }   
        $('#sowattachment').before(popCnt)
        $('.postTypeFooter').html(popBtn);
        $("#newWrapper").children().unwrap();
        $("#newFooterWrapper").children().unwrap();
        $('#sowattachment').css('display', 'none');
        var listAttachmentFiles = '';
        var extfile='';
        $.each(nameAttach, function(i, value) {
             var valuedata = value.split('~');

             extfile = valuedata[1].split('.');
             extfile = extfile[1];
            listAttachmentFiles += '<li  attname="' + value + '"><i class="fa fa-file-'+iconexe[extfile]+'-o "></i> ' + valuedata[0] + ' <a href="javascript:void(0);" class=" pull-right closeFilesFromAttached pull-left"><i class="fa fa-times-circle fa-lg"></i></a></li>';

        });
        $('#allAttachedFiles').html('<ul>' + listAttachmentFiles + '</ul>');
        $.dbeePopup('resize');
    });
    $('body').on('click', '.attachrow li a', function(e) {
        e.preventDefault();
        $('.postfilelist').css('display', 'none');
        $('.mainFilesList .fa').removeClass('fa-folder-open').addClass('fa-folder');
        $(this).next('.postfilelist').slideToggle('fast');
        if ($(this).next('.postfilelist').is(':visible') == true) {
            $('.fa', this).removeClass('fa-folder').addClass('fa-folder-open');
        } else {
            $('.fa', this).removeClass('fa-folder-open').addClass('fa-folder');
        }

        setTimeout(function() {
            $.dbeePopup('resize');
        }, 800);
    });
    $('body').on('click', '.postfilelist input[type=checkbox]', function(e) {
        nameAttach = [];
        $('.postfilelist  input:checked').each(function() {
            var chknameext = $(this).val();
            var chkname = $(this).attr('attname')+'~'+chknameext;
            
            nameAttach.push(chkname);
        });

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

    $('body').on('click', '.eventmore', function() {

        if($('#dbeecontrollers').val()!='dashboarduser')
        {                   
            var href = $('#ProfileLink').attr('href');            
            window.location.href = href+'#event_section';
            return false;
         }

        $('#event_section').trigger('click');
    });


    $('body').on('click', '#openpdflink a,.rtpdftitle', function(e) {
        e.preventDefault();
        var file = $(this).attr('fid');

        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {
                'id': file
            },
            url: BASE_URL + "/dbeedetail/showpdf",
            success: function(response) {
 
                var pageURL = adminURL + "knowledge_center/" + response.dir + '/' + file;
                var windH = $(window).height();
                var heightn = windH - 40;
                $.dbeePopup('<iframe src="' + pageURL + '" width="100%"  scrolling="no" height="' + heightn + '" > </iframe>');
                $.dbeePopup('resize');
                //  window.open(pageURL, "popupWindow", "width=600,height=600,scrollbars=yes");

            }
        });
    });

    $('body').on('blur', '#PostLink', function(e) {
        e.preventDefault();
        $('#attachlinkerror').fadeOut();
        $(this).parents('.singleRow').removeClass('singleRow');
        var link = $.trim($(this).closest('div').find('input').val());
        if (link == '')
            return false;
        var dbeeurl = $('#PostLink').val();
        $linkCaptureData(dbeeurl, '#LinkDesc1', '#PostLink');
    });
    //End  function used for for attachlink




    $commentPicUploader = function(formId) {
        var btnClosest = $(formId).closest('.dbpostWrp');
        var fileList;

        $(formId).dropzone({
            url: BASE_URL + "/myhome/imguplod",
            maxFiles: 1,
            addRemoveLinks: true,
            parallelUploads: 1,
            maxFilesize: 5,
            acceptedFiles: '.png, .jpg, .jpeg, .gif, .JPG, .JPEG, .PNG, .GIF',
            maxfilesexceeded: function(file, serverFileName) {
                $dbConfirm({
                    content: 'You can upload only 1 file',
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
                btnClosest.find('.imageCmntPost').remove();
                $(formId).closest('.cmntBntsWrapper').after('<div class="formRow imageCmntPost first" style="height:100px;" ></div>');
                $dbLoader(btnClosest.find('.imageCmntPost'), 1);
                $(formId).closest('.cmntBntsWrapper').find('#myhomedbee_comment').focus();
            },
            success: function(file, serverFileName) {
                $dbLoader(btnClosest.find('.imageCmntPost'), 1, 'close');
                fileList = "serverFileName = " + serverFileName;
                //$(formId+' input[name="PostCommentPixList"]').val(serverFileName);
                btnClosest.find('.imageCmntPost').css({
                    position: 'relative',
                    minHeight: '100px',
                    background: 'url('+IMGPATH+'/imageposts/small/' + serverFileName + ')  no-repeat',
                    backgroundPosition: 'left top',
                    backgroundSize: 'contain'
                }).append('<a href="javascript:void(0)" id="closedCmntbImage" data-fileName="' + serverFileName + '" class="removeCircle">\
                                                        <span class="fa-stack">\
                                                          <i class="fa fa-circle fa-stack-2x"></i>\
                                                          <i class="fa fa-times fa-stack-1x fa-inverse"></i>\
                                                        </span>\
                                                        </a> <input type="hidden" name="PostCommentPixList" value="' + serverFileName + '"></div>');


                $('a[data-fileName].removeCircle').click(function(e) {
                    var fileName = $(this).attr('data-fileName');
                    btnClosest.find('.imageCmntPost').remove();
                    $.ajax({
                        url: BASE_URL + "/myhome/imgunlink",
                        type: "POST",
                        data: fileList,
                        success: function() {
                            $('#PostCommentPix').val('');
                        }
                    });
                });
                this.removeFile(file);
            },
            thumbnail: function(ds, dataUrl) {
                $(formId + ' .dz-error').remove();
            }

        });




    }

    $('body').on('click', '.categoriesBtn', function(e) {
        e.stopPropagation();
        e.preventDefault();
        $(this).toggleClass('active');
        if ($(this).hasClass('active') == true) {
            $('.postpopupIcon', this).removeClass('fa-folder').addClass('fa-folder-open');
        } else {
            $('.postpopupIcon', this).removeClass('fa-folder-open').addClass('fa-folder');
        }

        $('.categoryList').slideToggle('fast');
        $('.categoryList input:not([cat-name="Miscellaneous"])').attr('checked', false);
        //$('.categoryList input:checkbox[cat-name="Miscellaneous"]').attr('checked', true).trigger('click');
        //$("input:checkbox[value='5']").attr("checked","true")
        setTimeout(function() {
            $.dbeePopup('resize');
        }, 200)

    });

    $('body').on('click', '.categoryList input', function() {
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

        $('#db-post-cat').val('');
        var checkedValue = new Array();
        $.each($('.categoryList input[type="checkbox"]:checked'), function(key, value) {
            checkedValue.push($(this).val());
        });
        $('#db-post-cat').val(checkedValue);


    });


    $('body').on('click', '#FollowToUser', function(e) {
        var tweetscreenname = $(this).attr('tweetscreenname');
        var tweetid = $(this).attr('tweetid');
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {
                'screen_name': tweetscreenname
            },
            url: BASE_URL + "/social/makefollowuptwitter",
            success: function(response) {
                $('a[data-type="CommentText"]').trigger('click');
                document.getElementById('twitter-reply-box').style.display = 'block';
                document.getElementById('twitter-db-reply').innerHTML = document.getElementById('twitter-result-' + tweetid).value;
                document.getElementById('hiddentwitterreply').value = document.getElementById('twitter-result-' + tweetid).value;
                document.getElementById('hiddentwittername').value = tweetscreenname;
                //$dbConfirm({content:response.message, yes:false});
                 $('.closeBarComment a').trigger('click');
                $('.closePostPop').trigger('click');
               

                firstTweet = true;
            }
        });
    });

    $('body').on('click', '.socialFeedMenuTop a[data-type]', function(e) { 
        $('#sideBarFeeds').addClass('activeSdFeeds');
        var dataType = $(this).attr('data-type');
        $('.socialfeedWidget').removeClass('notOpenFeed');
        if (dataType == 'my feed') {
            iamLoginNow();
            showPlatformfeed();
        } else {
            nowShowPlatformfeed();
            showMyfeed();
        }

         if ($('.socialfeedWidget a:visible').hasClass('active') == true) {
            $('.socialfeedWidget a:visible.active, .closeNavations').trigger('click');
         }else{
            $('.socialfeedWidget a:visible:first, .closeNavations').trigger('click');
         }
    });





    $('body').on('click', '.twitterconnected', function(e) {
        e.preventDefault();
        $('.socialfeedWidget').removeClass('notOpenFeed');
        showMyfeed();
        showPlatformfeed();
        twitterFriendID = $(this).attr('id');
        twitterFriendUserName = $(this).attr('username');
        twitterFriend = true;

        if (twValue != '' || fbValue != '' || linkValue != '') {
            $('.socialfeedWidget .myFeedsBtns').addClass('myFeedsBtnsActive');
        }
        $('#sideBarFeeds').addClass('activeSdFeeds');
        $('.socialfeedWidget, .socialfeedWidget .TwitterVisitor').fadeIn();
        $('.socialfeedWidget .FacebookVisitor, .socialfeedWidget .LinkedinVisitor').fadeOut('fast');
        $('.socialfeedWidget .TwitterVisitor').trigger('click');


    });

    $('body').on('click', '.facebookconnected', function(e) {

        $('.socialfeedWidget').removeClass('notOpenFeed');
        showMyfeed();
        showPlatformfeed();

        faceBookFriendID = $(this).attr('id');
        faceBookFriendUserName = $(this).attr('username');
        faceBookFriend = true;


        if (twValue != '' || fbValue != '' || linkValue != '') {
            $('.socialfeedWidget .myFeedsBtns').addClass('myFeedsBtnsActive');
        }
         $('#sideBarFeeds').addClass('activeSdFeeds');
        $('.socialfeedWidget, .socialfeedWidget .FacebookVisitor').fadeIn();
        $('.socialfeedWidget .TwitterVisitor, .socialfeedWidget .LinkedinVisitor').fadeOut('fast');
        $('.socialfeedWidget .FacebookVisitor').trigger('click');

    })


    $('body').on('click', '.linkedinconnected', function(e) {

      
        $('.socialfeedWidget').removeClass('notOpenFeed');
        showMyfeed();
        showPlatformfeed();

        linkedinFriendID = $(this).attr('id');
        linkedinFriendUserName = $(this).attr('username');
        linkedinFriend = true;

        if (twValue != '' || fbValue != '' || linkValue != '') {
            $('.socialfeedWidget .myFeedsBtns').addClass('myFeedsBtnsActive');
        }
        $('#sideBarFeeds').addClass('activeSdFeeds');
        $('.socialfeedWidget, .socialfeedWidget .LinkedinVisitor').fadeIn();
        $('.socialfeedWidget .TwitterVisitor, .socialfeedWidget .FacebookVisitor').fadeOut('fast');
        $('.socialfeedWidget .LinkedinVisitor').trigger('click');

    });



    $('body').on('click', '.fbCloseDetailIcon', function() {
        closeSDetails();
    });

    $('body').on('keyup', '.retweetTextarea', function(event) {
        var text = $(this).text().length;
        var maxChar = $(this).attr('maxlength');
        var availableChar = maxChar - text;
        if (availableChar < 0) {
            $(this).closest('.formRow').addClass('overLimit').find('.limitLength').text('over limit characters ');
        } else {
            $(this).closest('.formRow').removeClass('overLimit').find('.limitLength').text(availableChar + ' limit');
        }
    });

    $('body').on('click', '.socialfeedWidget a:not(.disabled), #sideBarFeeds .disabledRefreshIcon, .totalRssHeader a', function(e) {
        e.preventDefault();
        var vTp = 90;       
        var heightFeedSide = windH-vTp;
        var  clickRefreshFeed = false;
        var dataURL='';
        var dataParam = '';
         var feedType = $(this).attr('data-type');
         var dataId = $(this).attr('data-id');
             var indexRss = 0;
             if(typeof dataId==='undefined') {
                dataId ='';
            }else{
              indexRss =  ($('.totalRssHeader .totalCountWrp a[data-id="'+dataId+'"]').text() -1);
            }
        if($(this).hasClass('disabledRefreshIcon')==true){
            clickRefreshFeed = true;
        }

        $('.socialfeedWidget a').addClass('disabled');
        $('.sideFeedWrp').animate({
            top: windH
        }, 'fast', function() {
            $(this).remove();
        });
        closeSDetails();
       
        $('.sideFeedWrp').remove(); 
 
            if ($(this).hasClass('disabledRefreshIcon') != true) {
                $('.socialfeedWidget a').removeClass('active');
                $(this).addClass('active');
            } else {
                $('#dbTipWrapper').remove();
                $('#sideBarFeedsFb').html('');
            }

        $('#sideBarFeeds').addClass('activeSdFeeds loader').append('<i class="fa fa-spinner fa-spin fa-3x pull-left socialFeedLoader"></i>');

        $('html').addClass('activeSocialfeedSidebar');
        $('.allUserChat .expandCollapse').trigger('click');
        if($('.allUserChat').hasClass('offCh')==false){
          $('.allUserChat .expandCollapse').trigger('click');
        }
      
        
        var comBtn = '<i class="fa fa-refresh fa-lg disabledRefreshIcon" rel="dbTip" title="Refresh"   data-type="'+feedType+'"></i>\
                            <a class="fa fa-times minimisesocal" href="javascript:void(0);" title="Close" data-icon="FacebookVisitor"></a>';

        if (feedType == 'facebook') {
             dataURL = BASE_URL + "/social/facebookfeedhometimeline";
            var profilePic = '';
            var myJsonValue = 'mysocialFeed';

            var buttons = comBtn+'<i class="fbStatusUpdate postSocialIcons fa fa-pencil-square-o fa-lg" title="Update status"  data-type="facebook"></i>\
                       <i class="fbPicUpload postSocialIcons  fa fa-camera fa-lg" title="Add a picture"  data-type="facebook"></i>';

            if (faceBookFriend == true) {
                dataURL = BASE_URL + "/social/facebookfriendfeedhometimeline";
                myJsonValue = 'faceBookFriend';
                dataParam = {
                    'facebookid': faceBookFriendID,
                    'dbeeUsername': faceBookFriendUserName
                };
                buttons = comBtn;
                var titleFeedTop = '';

            } else if (platformConnectWithFacebook == true) {
                 myJsonValue = 'platformConnectWithFacebook';
                dataURL = BASE_URL + "/social/facebookpage";
                var titleFeedTop = '<strong>Platform feed</strong>';
                 if(adminID != SESS_USER_ID){
                    buttons = comBtn;
                }
            } else {
                var titleFeedTop = '<strong>My feed</strong>';

            }
         
        } else if (feedType == 'linkedin') {
             dataURL = BASE_URL + "/social/linkedinmyprofile";
            var profilePic = '';
            var titleFeedTop = '';
            var myJsonValue = 'mySocialLinkedinFeed';
            var buttons = comBtn;
            if (linkedinFriend == true) {
                 var myJsonValue = 'linkedinFriend';
                titleFeedTop = '';
                var urlpic = $('#profileimage').attr("datapic-url");
                profilePic = '<img src="' + urlpic + '"  class="twProPicTop"/>';
                dataURL = BASE_URL + "/social/linkedinuserprofile";
                dataParam = {
                    'dbeeUsername': linkedinFriendUserName
                };

            } else if (platformConnectWithLinkedin == true) {
                  var myJsonValue = 'platformConnectWithLinkedin';
                titleFeedTop = '<strong>Platform feed</strong>';
                dataURL = BASE_URL + "/social/linkedinplateformfeed";
            } else {
                titleFeedTop = '<strong>My feed</strong>';
            }
           

        } else if (feedType == 'twitter') {
             dataURL = BASE_URL + "/social/twitterfeedhometimeline";
            var profilePic = '';
            var titleFeedTop = '';
            var myJsonValue = 'mySocialTwitterFeed';

            var buttons = comBtn+'<i class="sprite twStatusUpdate postSocialIcons" title="Compose new tweet" data-type="twitter"></i>';
            if (twitterFriend == true) {
                dataURL = BASE_URL + "/social/twitteruserfeedhometimeline";
                var myJsonValue = 'twitterFriend';
                dataParam = {
                    'screenname': twitterFriendID,
                    'dbeeUsername': twitterFriendUserName
                };
                buttons = '';
                titleFeedTop = '';

            } else if (platformConnectWithTwitter == true) {
                dataURL = BASE_URL + "/social/twitteradminfeedhometimeline";
                var myJsonValue = 'platformConnectWithTwitter';
                if(adminID != SESS_USER_ID){
                    buttons = comBtn;
                }
                titleFeedTop = '<strong>Platform feed</strong>';
            } else {

                titleFeedTop = '<strong>My feed</strong>';
            }
        }
       
        // rss
        else if (feedType == 'rss') {
             //dataURL = BASE_URL + "/social/twitterfeedhometimeline";
           var profilePic = '';
           var titleFeedTop = '';

           var dataURL = BASE_URL + "/myhome/convertrss2";
           var titleFeedTop = '<strong>Platform feed</strong>';
           var myJsonValue = 'mySocialRssFeed'+indexRss;
           var dataParam = {id:dataId};
           var buttons = '';
           var buttons = '<a class="fa fa-times minimisesocal" title="Close"  href="javascript:void(0);"></a>';
           //var buttons = '<a class="fa fa-times minimisesocal" title="Close"  href="javascript:void(0);"></a><i class="postSocialIcons rssEditBtn" title="Edit" data-type="rss" onclick="javascript:openrssselector()"><b class="btn btn-yellow btn-mini">Edit</b></i>';
            if (platformConnectWithTwitter == true) {
              titleFeedTop = '<strong>Platform feed</strong>';
             }
             $('.socialfeedWidget .rssVisitor').addClass('active');
        }


        
        if (feedType == 'twitter' || feedType == 'linkedin' || feedType == 'facebook' || feedType == 'rss'){
            function jsonFunctionImplement(response, buttons, titleFeedTop, heightFeedSide, profilePic, windH, myJsonValue, vTp, dataId, indexRss){
                if( feedType == 'facebook'){
                    fbSocialFeed(response, buttons, titleFeedTop, heightFeedSide, profilePic, windH, vTp);
                }
                else if( feedType == 'linkedin'){
                   linkedInSocialFeed(response, buttons, titleFeedTop, heightFeedSide, profilePic, windH, myJsonValue, vTp)
                }
                else if( feedType == 'twitter'){
                   twitterSocialFeed(response, buttons, titleFeedTop, heightFeedSide, profilePic, windH, myJsonValue, vTp);
                }
                else if(feedType == 'rss' && $('#ShowRSS').val()==1){   
                   
                   rssSocialFeed(response, buttons, titleFeedTop, heightFeedSide, profilePic, windH, myJsonValue, vTp, dataId, indexRss,response.nofeed);
                
                }

                  $('.feedWrpSideContent').scroll(function() {                          
                             closeSDetails();
                             console.log('scroll');
                        });
            }

            if(clickRefreshFeed) delete  mySocialFeedData[myJsonValue];
            
             if(typeof mySocialFeedData[myJsonValue]==='undefined'){
                    $.ajax({ 
                        url: dataURL,
                        type: "POST",
                        dataType: 'json',
                        data: dataParam,
                        
                        success: function(response) 
                        {

                            if(feedType == 'linkedin')
                            var response =  $.parseJSON(response['getnetwork']);

                            mySocialFeedData[myJsonValue] = response;
                            jsonFunctionImplement(response, buttons, titleFeedTop, heightFeedSide, profilePic, windH, myJsonValue, vTp, dataId, indexRss);
                            

                        }

                    });
                    //end ajax
                }else{
                    var response =  mySocialFeedData[myJsonValue]; 
                   
                    jsonFunctionImplement(response, buttons, titleFeedTop, heightFeedSide, profilePic, windH, myJsonValue, vTp, dataId, indexRss);
                }

                // console.log(mySocialFeedData);
                
               
        }

    });

    $('body').on('click', '.minimisesocal', function() {
       // closeSocialFeedSidebar();
        $('#sideBarFeeds').removeClass('activeSdFeeds');
        $('html').removeClass('activeSocialfeedSidebar');
        $('body').removeClass('offChatAlluser');
        closeSDetails();


    });


     $('body').on('click', '#sharePlfrm', function() 
     {
        //if($(this).closest('.fbClickDescription').find('.facebookTextArea').is(':visible')==false){
            if ($(this).is(':checked')) 
            {
                $('#fbsideComment').append('<label class="shareOnEmbed"><input type="checkbox"  value="" id="shareOnEmbed"><label class="checkbox" for="shareOnEmbed"></label>Embed link to this post</label>');
            }else{
                $('.shareOnEmbed').remove();
            }
         //}
        
    });

     $('body').on('click', '#shareOnEmbed', function() 
     {
        if ($(this).is(':checked')) 
        {
            var charLenght = $('#ssUpdate').val().length;
            var count = (124 - charLenght);
            $('#ssUpdate').attr('maxlength', 124);
            $('.limitLengths').html(count + ' limit');

        }else
        {
            var charLenght = $('#ssUpdate').val().length;
            var count = (139 - charLenght);
            $('#ssUpdate').attr('maxlength', 139);
            $('.limitLengths').html(count + ' limit');
        }
    });
    
    $('body').on('keyup', '#ssUpdate:not(.facebookTextArea)', function() 
    {
       if ($('#shareOnEmbed').is(':checked')) 
       {
            var charLenght = $('#ssUpdate').val().length;
            var count = (124 - charLenght);
            $('#ssUpdate').attr('maxlength', 124);
            $('.limitLengths').html(count + ' limit');
        }
        else
        {
            var charLenght = $('#ssUpdate').val().length;
            var count = (139 - charLenght);
            $('.limitLengths').html(count + ' limit');
        }
    });


    $('body').on('click', '.postSocialIcons', function() 
    {
        var dataType = $(this).attr('data-type');
        var titlePost = 'Update status';
        var titleBg = '';
        var labelShareBnt = 'Twitter';
        var btnClass = 'btn-facebook';
        var postData = 1;
        var shareLinks = '';
        var dataBtn = '<a href="#" class="btn ' + btnClass + ' pull-right">Post to Facebook</a>';
        var postTypeImage = false;
        var singleRow = '';
        if (dataType == 'rss')
            return false;
        if (dataType == 'twitter') 
        {
            titlePost = 'Compose new tweet';
            titleBg = "#29b2e4";
            btnClass = 'btn-twitter';
            labelShareBnt = 'Facebook';
            postData = 2;
            shareLinks = '';
            dataBtn = '<a href="#" class="btn ' + btnClass + ' pull-right">Tweet</a>';
            singleRow = 'singleRow';
        }

        if(postData==2)
         var updateFiled = '<div class="postTypeContent">\
                            <div class="formRow ' + singleRow + '">\
                                <textarea id="ssUpdate" placeholder="What\'s on your mind?" maxlength = "140" ></textarea>\
                                <div class="formSubtitle"><span class="pull-right limitLengths">139 limit</span></div>\
                            </div>\
                            ' + shareLinks + '\
                       </div>'
        else
         var updateFiled = '<div class="postTypeContent">\
                            <div class="formRow ' + singleRow + '">\
                                <textarea id="ssUpdate" class="facebookTextArea" placeholder="What\'s on your mind?" ></textarea>\
                            </div>\
                            ' + shareLinks + '\
                       </div>';

        var post = $('#sideBarFeeds').position();
        var sideWidth = $('#sideBarFeeds').width();

        //alert(iamConnectWithTwitter);
        twitterchecked = '';

        if (dataType == 'twitter' && iamConnectWithFacebook == true)
            twitterchecked = '<label><input type="checkbox"  value="" id="shareOnScl"><label class="checkbox" for="shareOnScl"></label>Share on ' + labelShareBnt + '</label>';

        if (dataType != 'twitter' && iamConnectWithTwitter == true)
            twitterchecked = '<label><input type="checkbox"  value="" id="shareOnScl"><label class="checkbox" for="shareOnScl"></label>Share on ' + labelShareBnt + '</label>';


        if ($(this).hasClass('fbPicUpload') == true) {
            postTypeImage = true;
            var titlePost = 'Add a photo';
            dataBtn = '<a href="#" class="btn ' + btnClass + ' pull-right">Post to Facebook</a>';
            var updateFiled = '<div class="postTypeContent">\
                            <div class="formRow dropzone" id="uploadSocialPic"></div>\
                            <div class="formRow">\
                                <textarea id="ssUpdate" placeholder="Write something about this photo"></textarea>\
                            </div>\
                       </div>';

        }
        if ($(this).hasClass('fbPicUpload') == true) {
            var postHTML = '<div class="fbClickDescription" data-result="' + dataType + '" style="top:' + (post.top + 50) + 'px; right:' + (sideWidth + 15) + 'px;">\
                        <div class="fbClickCnt">\
                            <h2 class="socialUpdatesTitle" style="background:' + titleBg + '">' + titlePost + ' <i class="fa fa-times fbCloseDetailIcon"></i></h2>\
                        ' + updateFiled + '\
                         </div>\
                        <div id="fbsideComment">\
                            <label style="margin-right:5px;"><input type="checkbox"  value="" id="sharePlfrm"><label class="checkbox" for="sharePlfrm"></label>Share on platform</label>\
                        ' + dataBtn + '\
                        <div class="clear"></div></div>\
                   </div>';
        } else {
            var postHTML = '<div class="fbClickDescription" data-result="' + dataType + '" style="top:' + (post.top + 50) + 'px; right:' + (sideWidth + 15) + 'px;">\
                        <div class="fbClickCnt">\
                            <h2 class="socialUpdatesTitle" style="background:' + titleBg + '">' + titlePost + ' <i class="fa fa-times fbCloseDetailIcon"></i></h2>\
                        ' + updateFiled + '\
                         </div>\
                        <div id="fbsideComment">\
                            <label style="margin-right:5px;"><input type="checkbox"  value="" id="sharePlfrm"><label class="checkbox" for="sharePlfrm"></label>Share on platform</label>' + twitterchecked + '\
                        ' + dataBtn + '\
                        <div class="clear"></div></div>\
                   </div>';
        }
        closeSDetails();
        $('body').append(postHTML);
        $('.fbClickDescription').fadeIn();
        if ($('#uploadSocialPic').is(':visible') == true) {

            var postPicture = new Dropzone("#uploadSocialPic", {
                url: BASE_URL + "/myhome/imguplod",
                maxFiles: 1,
                addRemoveLinks: true,
                parallelUploads: 1,
                acceptedFiles: '.png, .jpg, .jpeg, .gif'
            });
            postPicture.on("success", function(file, serverFileName) {
                $('#uploadSocialPic').append('<input type="hidden" name="socialpich" id="socialpich"  value="' + serverFileName + '" />');
            });
            postPicture.on("removedfile", function(file) {
                $('#socialpich').val('');
            });
        }


        // start post functions
        $('#fbsideComment .btn').click(function() {
            $('.btn-twitter').append(' <i class="fa fa-spinner fa-spin"> </i>').css('cursor', 'default');
            var PostText = conMentionTotext($('#ssUpdate').val(), false);
            var shareUrl = $('#shareUrl').val();
            $('.btn-facebook').append(' <i class="fa fa-spinner fa-spin"> </i>').css('cursor', 'default');

            if (postTypeImage == true) {
                var socialpich = $('#socialpich').val();
                if (socialpich == '') {
                    //$messageError('Please upload picture');
                    $dbConfirm({
                        content: 'Please upload picture',
                        yes: false,
                        error: true
                    });
                    return false
                } else {

                    if ($('#shareOnScl').is(':checked')) {
                        postData = 3;
                    }

                    if ($('#sharePlfrm').is(':checked')) {
                        isPostOnPlateForm = 1;
                    } else {
                        isPostOnPlateForm = 2;
                    }


                    $.ajax({
                        url: BASE_URL + "/social/photoupload",
                        type: "POST",
                        dataType: 'json',
                        data: {
                            'PostText': PostText,
                            'socialpich': socialpich,
                            'isPostOnPlateForm': isPostOnPlateForm,
                            "postData": postData,
                            "shareOnEmbed":shareOnEmbed
                        },
                        success: function(response) 
                        {
                            //$dbConfirm({content:response.message, yes:false});
                            $('#ssUpdate').val('');
                            $('#socialpich').val('');
                            closeSDetails();
                            $('.btn-twitter .fa-spin').remove();
                            $('.btn-facebook .fa-spin').remove();
                            $('.customDashboard li a.active').removeClass('active');
                            if (isPostOnPlateForm == '1' && ISUSERPROFILE == true)
                                seeglobaldbeelist(SESS_USER_ID, 4, 'my-dbees', 'myhome', 'mydbee', 'mydb');
                            else{
                                $('.formcat').show();
                                $('#filtercat').hide();
                                $('#feedtype').val('all');
                                //fetchintialfeeds();
                            }
                        }
                    });
                }
                return false;
            }

            if (PostText == '') {
                //$messageError('Please enter your status');
                $dbConfirm({
                    content: 'Please enter your status',
                    yes: false,
                    error: true
                });
                return false
            } else {


                if ($('#shareOnScl').is(':checked')) {
                    postData = 3;
                }

                if ($('#sharePlfrm').is(':checked')) {
                    isPostOnPlateForm = 1;
                } else {
                    isPostOnPlateForm = 2;
                }

                if ($('#shareOnEmbed').is(':checked')) {
                    var shareOnEmbed = 1;
                } else {
                    var shareOnEmbed = 2;
                }

                $.ajax({
                    url: BASE_URL + "/social/updatestatus",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        'PostText': PostText,
                        'isPostOnPlateForm': isPostOnPlateForm,
                        "postData": postData,
                        "shareUrl":shareUrl,
                        "shareOnEmbed":shareOnEmbed
                    },
                    success: function(response) {

                        //$dbConfirm({content:response.message, yes:false});
                        $('#ssUpdate').val('');
                        closeSDetails(); 
                        $('.customDashboard li a.active').removeClass('active');
                        if (isPostOnPlateForm == '1' && ISUSERPROFILE == true)
                             seeglobaldbeelist(SESS_USER_ID, 4, 'my-dbees', 'myhome', 'mydbee', 'mydb');
                        else
                        {
                            $('.formcat').show();
                            $('#filtercat').hide();
                            $('#feedtype').val('all');
                            //fetchintialfeeds();
                        }
                    }
                });
            }
        });

        // end post functions


    });

  

    $('body').on('click', '.miniMizeIcon', function() {

        $('.socialfeedWidget .active').trigger('click');
        $('#dbTipWrapper').remove();

    });


   String.prototype.repeat = function(num) {
        return new Array(num + 1).join(this);
    }
    var totalStarts = 0;

    $('body').on('keyup', 'textarea:not(#frontPage2 textarea):not(.chatInputAreaBox textarea)', function(e) {
        var length = $(this).val().length;
        var maxlength = $(this).attr('maxlength');
        var postBtn = '';
        var postFn = '';
        if ($(this).closest('.minPostTopBar').hasClass('minPostTopBar') == true) {
            var postBtn = $('#btn-dbit');
            postFn = 'javascript:postcomment();';
        } else {
            var postBtn = $('#insertmaindb');
            postFn = 'javascript:postdbee(0);';
        }
        if(filter!=null){
        $(this).val(function(i, txt) {
          var mentionTxt =   $(this).prev('.mentions').find('div').html();
          if(typeof mentionTxt =='undefined'){
                mentionTxt ='';
              }

            if(filter != undefined && filter!=0){
                for (var i = 0; i < filter.length; i++) {
                    var pattern = new RegExp('\\b' + filter[i].name + '\\b', 'i');
                    var replacement = '*'.repeat(filter[i].name.length);
                    txt = txt.replace(pattern, replacement);
                    mentionTxt = mentionTxt.replace(pattern, replacement);
                }
            }
            if(restUrl != undefined && restUrl!=0){
                for (var i = 0; i < restUrl.length; i++) {
                    var regx = /(https?|ftp|file):\/\/|www./g;
                    var url = restUrl[i].linkurl;
                    var sortRestUrl = url.replace(regx, '');
                    if(txt.indexOf(url)==-1){
                        var patternUrl = new RegExp('\\b' + sortRestUrl + '\\b', 'i');
                         var replacementUrl = '*'.repeat(sortRestUrl.length);
                    }else{
                        var patternUrl = new RegExp('\\b' + url + '\\b', 'i');
                         var replacementUrl = '*'.repeat(url.length);
                    }
                   
                     
                    txt = txt.replace(patternUrl, replacementUrl);
                    mentionTxt = mentionTxt.replace(patternUrl, replacementUrl);
                }
            }

            totalStarts = txt.split('**');

            if (totalStarts.length >1) {
                postBtn.attr('onClick', false).addClass('disabled');
                if (e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38  && e.keyCode != 39 && e.keyCode != 40 && e.keyCode != 46  && e.keyCode != 35 && e.keyCode != 36  ) {
                    $messageError('Sorry you cannot use this word/phrase. <div style="padding-top:5px;"><a href="javascript:void(0)" class="btn btn-yellow" id="abusiveWordCloseBtn">OK</a></div>', 'stop');
                 }else{
                     $messageError('', 'close');
                 }
                //$dbConfirm({content:'Sorry you cannot use this word/phrase. <div style="padding-top:5px;"><a href="javascript:void()" class="btn btn-yellow" id="abusiveWordCloseBtn">OK</a></div>', 'stop', yes:false});
                $('#abusiveWordCloseBtn').click(function(event) {
                    $messageError('', 'close');
                });
           

            } else {
                postBtn.attr('onClick', postFn).removeClass('disabled');
                //$messageError('', 'close');               

            }
            $(this).prev('.mentions').find('div').html(mentionTxt);
            return txt;
           
            });
         }

        if ($(this).closest('#cmntContainer').attr('id') == 'cmntContainer') {
            $(this).parents('.textareaWithList').next('div').find('.limitLength').text(maxlength - length + ' limit');
        } else {
            $(this).next('div').find('.limitLength').text(maxlength - length + ' limit');
        }

    });
    $('body').on('keydown', '#dbee_comment,#myhomedbee_comment, #PollComment', function(e) {
        if (e.keyCode == 13 && e.shiftKey != true) {
            e.preventDefault();
        }
    });

    $('body').on('keyup', '#myhomedbee_comment', function(e) {
  
        e.stopPropagation();

        if ($('.mentions-autocomplete-list').is(':visible') != true) {
            if (e.keyCode == 13 && e.shiftKey != true) {

                var mentionsIds = '';

                var gtag = $(this).closest('.mentions-input-box').find('.mentions:first');
                var comment = gtag.find('div:eq(0)').html();
                if (typeof comment != 'undefined') {
                    gtag.find('a').each(function() {
                       var  urlMention = $(this).attr('href').split('/') ;
                            mentionsIds += urlMention[urlMention.length-1]+',';
                    });
                    if (mentionsIds != '')
                        var data = "comment=" + comment;
                } else {
                    comment = $(this).val();
                }
                var com_pix = $(this).closest('.dbpostWrp').find('input[name="PostCommentPixList"]').val();
                var dbeeId = $(this).closest('li').attr('id').split('dbee-id-');
                var picdata = '';
                comment = typeof(comment) != 'undefined' ? comment : '';
                com_pix = typeof(com_pix) != 'undefined' ? com_pix : '';

                var thisEl = $(this);
                thisEl.removeAttr('style');

                if (comment == '') {
                    $dbConfirm({
                        content: 'Please write a comment',
                        yes: false,
                        error: true
                    });
                    return false;
                }

                var PollComments_On_Option = $('#PollComments_On_Option').val();

                if (com_pix != '') {
                    picdata = "&pic=" + com_pix;
                    commentpix = '<div class="clear"></div><div style="position:relative; " popup-image="' + com_pix + '" popup="true" class="cmntPhotoWrp"><img src="' + IMGPATH + '/imageposts/small/' + com_pix + '"><i class="fa fa-search-plus"></i> </div>';
                } else commentpix = '';

                if (PollComments_On_Option == 5) {
                    //comment==''
                }

                thisEl.removeAttr('style').mentionsInput('reset').elastic();
                //comment.replace('.mentionATag', )
              
                var appdiv = '';
               // console.log(comment);
               comment = conMentionTotext(comment);

                appdiv = '<div class="comment-list2 commentodd1 latestcommentadd">\
                                <div class="userPicLink">\
                                    <a class="cmntuserLink" href="javascript:void(0);">\
                                        <img border="0" src="' + thisEl.closest('.minPostTopBar').find('.pfpic img').attr('src') + '">\
                                    </a>\
                                </div>\
                                <div class="dbcomment-speechwrapper" id="dbcomment-speechwrapper">\
                                    <div class="dbcmntspeech">\
                                        <div>\
                                            <div title="" rel="dbTip" style="float:left; margin-bottom:5px;"><a class="cmntuserLink" href="javascript:void()">' + SESS_USER_NAME + '</a>&nbsp;' + comment + '</div>\
                                        </div>' + commentpix + '<div class="next-line"></div>\
                                        <div style="color:#999; margin:0 10px 0 0; width:auto;">\
                                            <div style="float:left;" class="cmntDateTime">a few seconds ago</div>\
                                            <br style="clear:both; font-size:1px;">\
                                        </div>\
                                    </div>\
                                </div>\
                                <div class="next-line"></div>\
                            </div>';

                thisEl.closest('.listingCommentLatest').find('.minPostTopBar').before(appdiv);
                thisEl.closest('.imOnCommentBox').find('.listingCommentLatest').append(appdiv);
                var sh = thisEl.closest('li').find('.postInnerLargeView').prop('scrollHeight');
               thisEl.closest('li').find('.postInnerLargeView').scrollTop(sh);
                thisEl.closest('.dbpostWrp').find('.imageCmntPost').remove();
                
                var data = {calling:'front',comment:comment,pic:com_pix,db:dbeeId[1],mentionsIds:mentionsIds};

                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    data: data,
                    url: BASE_URL + '/comment/insertdata',
                    success: function(response) {
                        if (response.status == 'success') {
                            if(localTick == false){
                                socket.emit('loadComment', response.insertId,dbeeId[1],clientID);
                                socket.emit('checkdbee', true,clientID,SESS_USER_ID);
                                callsocket();
                            }
                            thisEl.closest('li').find('.activeUserCount span strong').text(response.newcomment);
                            thisEl.closest('li').find('.activeUserCount strong.auc').text(response.countusers+' user');
                            thisEl.closest('.listingCommentLatest').find('.latestcommentadd').removeClass('latestcommentadd');
                        } else {
                            $dbConfirm({
                                content: 'something went wrong, please try again!',
                                yes: false,
                                error: true
                            });
                        }
                    }

                });

            }
        }
    });




    $('.feedTab a').click(function(e) {
        e.preventDefault();
        var feedContent = $(this).attr('rel');
        $('.feedTab a').removeClass('active');
        $(this).addClass('active');
        $('.feedContent .fdContent').hide();
        $('.feedContent div[data-feed="' + feedContent + '"]').show();
    });




    /*End default popup for dbee*/



    /*Start tooltip  function from here */
    $dbTip = function() {
        var targets = $('[rel~=dbTip]'),
            target = false,
            tooltip = false,
            title = false;

        targets.not('.noTip').bind('mouseenter', function() {
            target = $(this);
            var tip = target.attr('title');
            var tipAlign = target.attr('tip-align');
            tooltip = $('<div id="dbTipWrapper" class="dbtipWrp"></div>');

            if (!tip || tip == '')
                return false;
            $('.dbtipWrp').remove();
            target.removeAttr('title');
            tooltip.css('opacity', 0)
                .html(tip)
                .appendTo('body');

            var init_tooltip = function() {
                if ($(window).width() < tooltip.outerWidth() * 1.5 )
                    tooltip.css('max-width', $(window).width() / 2);
                else
                    tooltip.css('max-width', 340);

                var pos_left = target.offset().left + (target.outerWidth() / 2) - (tooltip.outerWidth() / 2),
                    pos_top = target.offset().top - tooltip.outerHeight() - 20;

                if (pos_left < 0 ||  tipAlign=='left') {
                    pos_left = target.offset().left + target.outerWidth() / 2 - 20;
                    tooltip.addClass('left');
                } else
                    tooltip.removeClass('left');

                if (pos_left + tooltip.outerWidth() > $(window).width() ||  tipAlign=='right') {
                    pos_left = target.offset().left - tooltip.outerWidth() + target.outerWidth() / 2 + 20;
                    tooltip.addClass('right');
                } else
                    tooltip.removeClass('right');

                if (pos_top + 10 < 0  || tipAlign=='bottom') {
                    var pos_top = target.offset().top + target.outerHeight();

                    tooltip.addClass('top');
                } else
                    tooltip.removeClass('top');
                if ($('.dbDetailsPageMain').is(':visible') == true) {
                    tooltip.css({
                            left: pos_left,
                            top: (pos_top - 60)
                        })
                        .animate({
                            top: '+=10',
                            opacity: 1
                        }, 50);
                } else {
                    tooltip.css({
                            left: pos_left,
                            top: pos_top
                        })
                        .animate({
                            top: '+=10',
                            opacity: 1
                        }, 50);
                }

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
                $('.dbtipWrp').remove();
            };

            target.not('.noTip').bind('mouseleave', remove_tooltip);
            target.not('.noTip').blur(remove_tooltip);
            tooltip.not('.noTip').bind('click', remove_tooltip);
        });
    }
    $dbTip();
    /*End  tooltip function from here */

    // this is loader
   
    $dbLoader = function(id, loaderType, loaderAction, center, overlayLoader) {
            $(id).find('.dbLoader').remove();


            var loaderPath = '<div class="dbLoader" id="dbLoaderContainer" style="min-height:100px; height:' + $(id).height() + 'px"><i class="fa fa-spinner fa-spin fa-3x"></i></div>';
            if (loaderType == 'progress') {
                 loaderPath = '<div class="dbLoader dbLoaderProgress" id="dbLoaderContainer" style="min-height:30px; height:' + $(id).height() + 'px"><span class="animateProgress"><strong>Loading...</strong></span></div>';
            }
            else if (loaderType=='center'){
                     //$(id).find('.dbLoader').css({position:'absolute', top:'50%', left:'50%', marginLeft:-15, marginTop:-15, marginBottom:0});
                       loaderPath = '<div class="dbLoader" id="dbLoaderContainer" style="background:rgba(0,0,0, 0.5); position:absolute; top:0px; width:100%; z-index:1;  min-height:100px; height:100%; color:#fff"><i class="fa fa-spinner fa-spin fa-3x" style=" top:50%; position:absolute;  left:50%; marginLeft:-15; marginTop:-15; marginBottom:0"></i></div>';
            }

            $(id).prepend(loaderPath);
            /*if(center=='center' && overlayLoader!=true && typeof(overlayLoader)=='undefined'){
               
            }*/
            if (loaderAction == 'close') {
                $(id).find('.dbLoader').remove();
            }

        }
        // end loader from here 

    // this is load leagues on click
    $('body').on('click', '.leaguesPostScores ul a:not(#myPlatformPositions)', function(e) {
        e.preventDefault();
        $('.leaguesPostScores ul a').removeClass('active');
        $(this).addClass('active');
        var userId = $('#myPlatformPositions').attr('secure_uid');
        var leagueType = $(this).attr('dataType');
        if ($(window).width() <= 800) {
            $('.leaguesPostScores ul').css('display', 'none');
            setTimeout(function() {
                $('.leaguesPostScores ul').attr('style', '')
            }, 100);
        }
        var showtabs = typeof(showtabs) != 'undefined' ? showtabs : '0';
        var data = "user=0&league=" + leagueType + "&showtabs=" + showtabs + "&dbleague=1";
        if ($('.leaguesPostScores ul').attr('data-myleagues') != 'true') {
            $.ajax({
                url: BASE_URL + '/dbleagues/userleague',
                type: 'POST',
                data: data,
                beforeSend: function() {
                    $('#topthree-leagues, #leagues-feed').html('');
                    $dbLoader('#leaguesListsContainer', 1);

                },
                success: function(data) {
                    $dbLoader('#leaguesListsContainer', 1, 'close');
                    var resultArr = data.split('~#~');
                    $('#topthree-leagues').html(resultArr[0]);
                    $('#leagues-feed').html(resultArr[1]);
                }

            });

        } else {
            $('#leaguesListsContainer').html('<div id="leagues-feed"><ul id="league-table-afterthree" class="leaguesLists"></ul></div>')
            seeuserscoreposition(leagueType, userId, 'league-table-afterthree');
        }
    });

   

    $('body').on('click', '#myPlatformPositions', function(e) {

        if ($(this).text() == 'my platform positions') {
            $(this).closest('.tabLinks').attr('data-myleagues', 'true');
            $(this).text('back to main');
        } else {
            $(this).text('my platform positions');
            $(this).closest('.tabLinks').removeAttr('data-myleagues');
            $('#leaguesListsContainer').html('<div id="topthree-leagues"></div><div id="leagues-feed"> </div>');
        }
        $('.leaguesPostScores a[datatype="love"]').trigger('click');
    });
    // End  is load leagues on click


    // change random background images
    /*var bgArray = [
                    '/images/bg/colorful-background-preview-5.jpg',
                    '/images/bg/colorful-background-preview-9.jpg',
                    '/images/bg/colorful-background-preview-16.jpg',
                    '/images/bg/colorful-background-preview-20.jpg',
                    '/images/bg/colorful-background-preview-22.jpg',
                    '/img/dbee-me-BG.png'
                 ]  
    $('#fullScreenBg').css({
    //background:'url('+bgArray[Math.floor(Math.random() * bgArray.length)] +')',
        background:'url('+bgArray[5] +')',
        backgroundPosition:'50% 50%',
        backgroundRepeat:'no-repeat',
        backgroundSize:'cover'

        });*/
    // end change random background images




    /*this function error*/
    $messageSuccess = function(content, action) {
        var msgEl = $('#successMsg');
        msgEl.hide().removeClass('bounceInDown').removeClass('bounceOutUp').removeClass('animated');
        if (action == 'close') {
            msgEl.removeClass('bounceInDown animated');
            msgEl.show().addClass('bounceOutUp animated');
            return false;
        }
        msgEl.show().html(content).addClass('bounceInDown animated');
        if (action != 'stop') {
            setTimeout(function() {
                msgEl.removeClass('bounceInDown animated');
                msgEl.addClass('bounceOutUp animated');

            }, 3000);
        }
    }
    $messageError = function(content, action) {
        var msgEl = $('#errorMsg');
        msgEl.hide().removeClass('bounceInDown').removeClass('bounceOutUp').removeClass('animated');
        if (action == 'close') {
            msgEl.removeClass('bounceInDown animated');
            msgEl.show().addClass('bounceOutUp animated');
            return false;
        }
        msgEl.html(content).show().addClass('bounceInDown animated');
        if (action != 'stop') {
            setTimeout(function() {
                msgEl.removeClass('bounceInDown animated');
                msgEl.addClass('bounceOutUp animated');
            }, 3000);
        }
    }

    $messageWarning = function(content, action) {
        var msgEl = $('#warningMsg');
        msgEl.removeClass('bounceInDown').removeClass('bounceOutUp').removeClass('animated');
        if (action == 'close') {
            msgEl.removeClass('bounceInDown animated');
            msgEl.addClass('bounceOutUp animated');
            return false;
        } else {
            msgEl.show().addClass('bounceInDown animated').find('#warningMsgContent').html(content);
        }
    }
    $ghostMessage = function(content) {
            $('#ghstpopup').addClass('active').find('#ghstpopup-text').html(content);
            setTimeout(function() {
                $('#ghstpopup').removeClass('active');
            }, 8000);
        }
        /*this function error*/

    $('body').on('click', '.tabHeader a', function(e) {
        e.preventDefault();
        var tabId = $(this).attr('rel');
        if ($(this).hasClass('active') == true) {
            return false;
        }

        $(this).closest('.tabHeader').find('.active').removeClass('active');
        $(this).addClass('active');
        $(this).closest('.tabHeader').next('.tabcontainer').find('.tabcontent').hide();
        $(this).closest('.tabHeader').next('.tabcontainer').find('#' + tabId + '').show();
        $.dbeePopup('resize');


    });

    //db meesage progress bar
    $dbMessageProgressBar = function(options) {
            var defaults = {
                fetchText: 'Verifying...',
                content: 'This process will take some time.. Kindly be patient',
                close: false,
                time: 5000,
                animate:true,
                complete: false
            }
            var settings = $.extend({}, defaults, options);

            function closeMSPro() {
                $('#mesageNotfiOverlay').fadeOut(function() {
                    $(this).remove();
                });
            }

            if (settings.close == true) {
                closeMSPro();
                return false;
            }

            if (settings.complete == true) {
                $('#mesageNotfiOverlay .progressMsgBar').animate({
                    width: '100%'
                }, 1000, function() {
                    $('#mesageNotfiOverlay .progressBarWrp').attr('data-loaded', '100%');
                    closeMSPro();
                });
                return false;
            }

            $('body').append('<div id="mesageNotfiOverlay" class="loaderOverlay3"> </div>');
            $('#mesageNotfiOverlay').html('<div class="msgNoticontent">\
                                    <div class="loaderShow">\
                                        <span class="loaderImg"><i class="fa fa-refresh fa-spin fa-3x"></i></span><br>\
                                        <span class="totalPr">' + settings.fetchText + '</span>\
                                    </div>\
                                    <div class="prCnt">' + settings.content + '</div>\
                                    <div class="progressBarWrp" style="margin-top:20px;" data-loaded=""><div class="progressMsgBar" ></div></div>\
                                </div>');
            if(settings.animate==true){
                $('#mesageNotfiOverlay .progressMsgBar').animate({
                    width: '90%'
                }, settings.time);
            }

        }
        // end db meesage progress bar



    $('#globalstartdbee').on('click', function() {

        var rel = $(this).attr('rel');

        $('.postTypeIcons a[rel="' + rel + '"]').trigger('click');
    });


}); // end jquery load function
$(document).ready(function() {




    $('body').on('click', '#account_setting', function() {
        accountSetttingPopup();
    });

    $('body').on('click', '.accountSettingTab a ', function() {
        var dataType = $(this).attr('data-type');
         $.dbeePopup('resize');
        if (chknewupdateprofilepop == 1) {
            $dbConfirm({
                content: 'You haven\'t saved your changes yet. Do you want to continue without saving?',
                noClick: function() {
                    return false;
                },
                yesClick: function() {
                    accountSetttingPopup(dataType);
                    chknewupdateprofilepop = 0;
                }
            });
        } else {
            accountSetttingPopup(dataType);
        }



    });


    $('body').on('click', '#my_notification', function() {

        var NotificationTemplate = '<div  id="mynotificationContainer" ><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>\
                 <div class="clearfix"></div>';
        $.dbeePopup(NotificationTemplate);

        $.ajax({
            type: "POST",
            dataType: 'json',
            url: BASE_URL + '/settings/notificationsettings',
            success: function(response) {
                $("#mynotificationContainer").html(response.content);
                $.dbeePopup('resize');
            }

        });

    });

    $('body').on('click', '#send_msgtop', function() {

        var msgtopTemplate = '<div  id="mescontainertop" ><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>\
                 <div class="clearfix"></div>';

        $.dbeePopup(msgtopTemplate, {
            overlay: true,
            otherBtn: '<a href="javascript:void(0);" class="pull-right btn btn-yellow"  id="SendMessagetp">Send</a>'
        });
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: BASE_URL + '/message/sendmsgtop',
            success: function(response) {
                $("#mescontainertop").html(response.content);
                setTimeout(function(){

                }, 100);
                $.dbeePopup('resize');
                function _formating(data) {
                    return '<img src="' + data.url + '" />' + data.text;
                }
               $makesselect2(this,{'ele':'#_submit_tag_names','placeholder':'Type username'});
            }
        });

       //$('#SendMessagetp:not(.disable)').click(function() 
      
    });
  $('body').on('click','#SendMessagetp:not(.disable)', function(event) {
        //{
            var userid = $('#_submit_tag_names').val();
            var message = $('#message-reply-top').val();
            var el = $(this);
            if(userid == ''){
                $('#createmsgContainer .select2-input').focus();               
                return false;
            }
            
            if(message == ''){
                $('#message-reply-top').focus();               
                return false;
            }
            userid = userid.split(',');
            var loginid = $('#hiddenuser').val();
           
            message = conMentionTotext(message, false);
            $(this).addClass('disable');
             $('.fa-spin',this).remove()
            $(this).append(' <i class="fa fa-spinner fa-spin"></i>');

            $.ajax({
                type: "POST",
                data: {
                    user: userid,
                    message: message,
                    from: '1'                    
                },
                dataType: 'json',
                url: BASE_URL + '/message/add',
                success: function(response) {                
                    if(localTick == false)
                        callsocket();
                    el.removeClass('disabled');
                    $('el i').remove();
                    $('.closePostPop').trigger('click');

                }
            });
        });
 //$('.message-reply-top').keypress(function(event) {
   /* $('body').on('keypress', '.message-reply-top', function(event) {
           // alert();
                        if (event.which == 13 && !event.shiftKey) {
                            
                            
                           event.preventDefault();
                               $('#SendMessagetp').trigger('click');
                           
                            }
        });*/



    $('body').on('click', '#twittersphere', function() {
        var dbid = $('#dbid').val();

        var SearchTwiiterMentionTemplate = '<div id="content_data" ><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>\
                 <div class="clearfix"></div>';


        $.dbeePopup(SearchTwiiterMentionTemplate, {
            otherBtn: '<a href="javascript:void(0)" class="btn btn-twitter pull-right" id="twittermention" onclick="javascript:Twittersphereshare()">Tweet</a>\
                                            '
        });

        $.ajax({
            type: "POST",
            data: {
                'dbeeid': dbid
            },
            dataType: 'html',
            url: BASE_URL + '/dbeedetail/searchtwitteruser',
            success: function(response) {

                $("#content_data").html(response);

                var prevHiddenFiled = $('#preview_twitterSphere');
                var twitterKeyFiled = $('#twitteruserkeyword');
                var prevHtml = $('#preview_twitterSphereHtml');
                var dataURL = prevHtml.attr('g-url');
                var titleForTweet = $('#titleForTweet').html();


                var previewVal = prevHiddenFiled.html()+ ' ' + dataURL;
                var dataTITLE = titleForTweet;
                var urlLength = dataURL.length;
                var restlength = parseInt('100') - parseInt(urlLength);
                $('#twitteruserkeyword').attr('maxlength', parseInt(restlength));

                var titleLength = dataTITLE.length;
                var previewVal2 = ' <a href="javascript:void(0);">' + dataURL + '</a>';
                var _length = previewVal.length;
                $('#_preLength').text(_length);
                  prevHiddenFiled.val(previewVal);
                  prevHtml.html(titleForTweet+previewVal2);
                  $.dbeePopup('resize');
                    twitterKeyFiled.keyup(function(e) {
                    var keyVal = $(this).val();
                    keyVal = keyVal.split(',');
                    var userTxt = '';
                    var usertextWithUrl = '';
                    $.each(keyVal, function(i, val) {
                        var val = val.replace('@', '');
                        if ($.trim(val) != '') {
                            userTxt += '@' + $.trim(val) + ' ';
                            usertextWithUrl += '<a href="javascript:void(0);">@' + $.trim(val) + ' </a>';
                        }
                    });

                    var combintext = userTxt + previewVal;
                    var combintext2 = usertextWithUrl + previewVal;
                    //var combintext2 = usertextWithUrl+previewVal;


                     titlelength = titleForTweet.length;
                    _length = combintext.length;
                    
                    if (_length > 139) {                       
                        var newval = parseInt(_length) - parseInt(urlLength);
                        dataTITLE = combintext.substr(0, parseInt(titlelength - 1))
                        dataTITLE  =  dataTITLE .replace(dataURL,'').replace(userTxt, '');
                       prevHtml.html(usertextWithUrl+dataTITLE+previewVal2);

                    } else {
                         dataTITLE = combintext.replace(dataURL,'').replace(userTxt, '');
                        prevHtml.html(usertextWithUrl+dataTITLE+previewVal2);
                    }

                    prevHiddenFiled.val(combintext);

                   

                    $('#_preLength').text(_length);
                });
            }

        });


    });



    $('body').on('click', '#search-groups', function() {

        var SearchGroupTemplate = '<div id="content_data" ><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>\
                 <div class="clearfix"></div>';


        $.dbeePopup(SearchGroupTemplate, {
            otherBtn: '<a href="javascript:void(0)" class="btn btn-yellow pull-right searchmygroup" id="grpsinvite">Search</a>\
                                            '
        });

        $.ajax({
            type: "POST",
            dataType: 'json',
            url: BASE_URL + '/group/searchgroups',
            success: function(response) {
                $("#content_data").html(response.content);
                $.dbeePopup('resize');
            }

        });
        $('body').on('keyup', '#groupkeyword', function(e) {
            e.stopPropagation();
            e.preventDefault();
            if (e.keyCode == 13) {
                searchgroupsinitiate();

            }
        });

        $('body').on('click', '#grpsinvite', function(e) {
            e.stopPropagation();
            e.preventDefault();
            searchgroupsinitiate();
        });


    });


    $('body').on('click', '#group-notifications', function() {

        var SearchNotiTemplate = '<div id="content_data" ><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>\
                 <div class="clearfix"></div>';
        $.dbeePopup(SearchNotiTemplate);

        $.ajax({
            type: "POST",
            dataType: 'json',
            url: BASE_URL + '/group/ajaxnotifications',
            success: function(response) {
                $("#content_data").html(response.content);
                showgroupnotifications();
                setTimeout(function() {
                    $.dbeePopup('resize');
                }, 500);

            }

        });

    });

    $('#group-creategroups').click();

     $('body').on('click', '.group-creategroups', function() {
        $('#allrev').hide();
        $('#processGroupBtnOk').hide();
        var CreategroupTemplate = '<div id="content_data" ><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>\
                 <div class="clearfix"></div>';
        $.dbeePopup(CreategroupTemplate, {
            overlayClick: false,
            otherBtn: '<a href="javascript:void(0)" class="btn btn-yellow pull-right createGroupBtn" onclick="javascript:creategroupstep2();">Next</a> \
                            <div class="processGroupBtnNext1" style="display:none">\
                                <a href="javascript:void(0)" class="btn btn-yellow pull-right" onclick="javascript:proceedcreategroup2();">Next</a>\
                            </div>\
                            <div class="processGroupBtnNext2" style="display:none">\
                                <a href="javascript:void(0)" class="btn btn-yellow pull-right" onclick="javascript:proceedcreategroup();">Next</a>\
                            </div>\
                            <div class="backGroupBtn" style="display:none">\
                                <a href="javascript:void(0)" class="btn btn-yellow pull-right" onclick="javascript:backtocreategroup();">back</a>\
                            </div>\
                            <div class="processGroupBtnOk" style="display:none">\
                                <a href="javascript:void(0)" class="btn btn-yellow disabled pull-right" id="nextTogo">Next</a>\
                                <a href="javascript:void(0);"  style="margin-right:5px;" class="btn  pull-right" id="skipinviteval"  onclick="javascript:skipinvitetogroup();">Skip invite</a>\
                            </div>\
                            <div class="processGroupBtnCreate groupBackbtn" style="display:none">\
                                <a href="javascript:void(0);" onClick="backtoselection();" class="btn grpBacksec">Back to selection</a>\
                                <a href="javascript:void(0)" class="btn btn-yellow pull-left" onclick="javascript:creategroup();">Create Group</a>\
                            </div>\
                                            '
        });
       
        var content = '';		
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: BASE_URL + '/group/creategroup',
            success: function(response) {		

		content += '<h2 class="titlePop">Create a Group</h2><div id="create-groups-wrapper" class="maindb-wrapper-border">\
		<form name="create" id="create"><div class="postTypeContent" id="passform">\
		<div class="formRow"><div class="formField"><input type="text" id="group-name" class="textfield required">\
		<label for="group-name" class="optionalText">group name </label></div></div>';
		var counter = 1;
		var i       = 1;
    var userselects = '';
    var userselectstab = '';

    if(SESS_USER_ID ==adminID )
    {
     userselectstab = '<li><a href="#" rel="user_list" id="userset">User set</a></li>';
     userselects = '<select class="selectgropns" id="selectgropns"><option for="selectgropns"  value="">Select user set</option>';
    var userselect = response.usergroup;
    		if(userselect.length > 0){
          $.each(userselect,function(i,usv){
            userselects +='<option value="'+usv.ugid+'">'+usv.ugname+'</option>';
          });
        }
    userselects += '</select>';
  }
 
		var dst = response.allgrouptypes;
		if(dst.length > 0) {
      content += '<div class="formRow"><div class="formField">';
			$.each(dst, function(i, allgrp) {
  			content+= '<div class="crtGrplabel"><label><input type="radio" name="group-type" id="group-type" onClick="javascript:checkothergrouptype('+allgrp.TypeID+')" value="'+allgrp.TypeID+'"><span class="grpLabeltxt">'+allgrp.TypeName+'</span></label></div>';
  			if (counter % 3 == 0) {
  				content += '<div class="next-line"></div>';
  				i = 0;
  			}
  			counter++;
  			i++;
  		});	

      content += ' <div style="position: relative; height: 33px; clear:both;"> <label for="group-type" class="optionalText" style="right:0px;">group category (optional)</label></div></div></div>';

    }			
		content += '<div class="formRow" id="group-other-textbox" style="display:none;"><div class="formField"><input type="text" class="textfield" id="group-type-other">\
		<i class="optionalText">specify</i></div></div><div class="formRow"><div class="formField"><label>\
		<input type="radio" name="group-privacy" id="group-privacy" value="1" checked="checked">Open</label><br />\
		<label><input type="radio" name="group-privacy" id="group-privacy" value="3">Request<span style="font-size:10px; margin-left:10px;">(non-invitees can request to join)</span></label><br /><label>\
		<input type="radio" name="group-privacy" id="group-privacy" value="2">Private<span style="font-size:10px; margin-left:10px;">(by invitation only)</span></label>\
		<i class="optionalText">group privacy</i></div></div><div class="formRow"><div class="formField"><label class="labelCheckbox" for="crtgrprest"><input type="checkbox" value="1" id="crtgrprest" name="rest_groupdb">\
		<label class="checkbox" for="crtgrprest"></label><span>Restrict posting to me</span></label><label class="optionalText" for="crtgrprest">post restriction</label></div></div></div></form>\
		</div></div><div id="creategroup-step2" style="line-height:20px; display:none"><div class="postTypeContent" id="passform"><div class="formRow uploadGrpRow"><div class="formField"><div id="upload_area">\
		<form action="" class="dropzone" id="uploadGroupDropzone"><div class="fallback"><input type="file" name="file"  /></div></form><input type="hidden" id="PostGroupPix" value=""></div>\
		</div></div><div class="formRow"  style="margin-bottom:10px;"><div class="formField"><textarea id="group-desc" class="textareafield" placeholder="Description (optional)"></textarea></div></div></div></div>';
		
		if(response.groupbg==1 || response.userid==response.adminID)
		{
			content+='<div id="creategroup-step3" style="line-height:20px; display:none"><div class="postTypeContent" id="passform"><div class="formRow uploadGrpRow"><div class="formField"><div id="upload_area">\
					<form action="" class="dropzone" id="uploadBgDropzone"><div class="fallback"><input type="file" name="file"  /></div></form><input type="hidden" id="GroupBackgroundPix" value=""></div></div></div></div></div>';
		}

		content+='<div id="invite-group-members" style="display:none; z-index:100;"><div style="font-size:14px; margin-bottom:10px;">Invite your followers and people you follow, and search for other users. Once your Group is created you can also invite Facebook, Twitter and Linkedin connections.</div>\
    <div class="clearfix"> </div><div id="invite-messageso" style="display:none;"><div style="font-size:12px; margin-top:10px; background-color:#F5F5F5; padding:10px; text-align:center;">Groups create and invitation sent to social users (if any).</div></div><ul id="invitetabs" class="tabHeader tabLinks">\
    <li><a href="#" rel="followers-list-create" id="followerslistgroup" class="active">Followers</a></li><li><a href="#" rel="following-list-create"  id="followingnlistgroup">Following</a></li>' +response.socialnetworkusers +'<li><a href="#" rel="search-list" id="search">Search</a></li>'+userselectstab+'</ul><div class="tabcontainer tabContainerWrapper" style="padding:10px;">\
    <div id="followers-list-create" class="tabcontent" style="display:block"></div><div id="following-list-create" class="tabcontent"></div><div class="tabcontent" id="socalnetworking-list"></div><div id="search-list" class="tabcontent"><div id="passform" class="postTypeContent" style="margin-top:0px;"><div class="singleRow"><div class="formField">\
    <div id="searchUserBox" ><div name="searchGroupUsers" class="srcUsrWrapper" id="searchGroupUsers"><div class="fa fa-search fa-lg searchIcon2"></div><input type="text" id="groupkeywords" class="textfieldSearch"  value=""></div></div></div></div><div id="search-invite-list" class="formRow singleRow" style="display:none"><div class="formField"></div></div>\
    </div><br style="clear:both; font-size:1px"></div><div id="user_list" class="tabcontent"><div id="passform1" class="postTypeContent1"> '+userselects+' </div><br style="clear:both; font-size:1px"></div></div><input type="hidden" id="groupid"><input type="hidden" id="total-followers"><input type="hidden" id="total-following"><input type="hidden" id="total-search"><input type="hidden" id="total-users-toinvite"><input type="hidden" id="total-users-toinvite-social"><input type="hidden" id="from"></div>\
    <div id="invite-selected-div" style="display:none;"><div id="invitetogroup-header" style="float:left; font-size:14px; margin-bottom:10px;">Invite your followers and people you follow, and search for other users. Once your Group is created you can also invite Facebook, Twitter and Linkedin connections.</div>\
    <div class="next-line"></div><div class="maindb-wrapper-border" style="padding:10px;"><div id="selected-users-label" style="font-size:12px; margin:0 10px 0 -10px;">Users selected by you to invite to groups.</div>\
    <div class="next-line"></div><div id="invite-selected"></div><div class="next-line"></div><div id="invite-message" style="display:none;"><div style="font-size:12px; margin-top:10px; background-color:#F5F5F5; padding:10px; text-align:center;">\
    Groups create and invitation sent to social users (if any). </div></div><br style="clear:both; font-size:1px"></div></div><div id="backbuttonso" style="display:none;"><div style="float:right; margin-top:35px;"><a onclick="backtoselection();" href="javascript:void(0);">back to selection</a></div></div><div class="next-line"></div><div id="allrev" style="display:none;">\
    <div style="margin-top:15px; overflow:hidden"><div style="float:left; margin-top:10px;"></div></div></div>';
		$("#content_data").html(content);
     $('#selectgropns').select2({width:'100%'});
			loadfollowersforgroup();
            loadfollowingforgroup();
            $('.closePostPop').show();
            $('.createGroupBtn').show();		
      }
        });

        $('body').on('keyup', '#groupkeywords', function(e) {

            if (e.keyCode == 13) {
                e.stopPropagation();
                e.preventDefault();
                searchuserstoinvite();

            }
        });
        $('body').on('click', '#bntsubmintgrpinvite', function(e) {
            e.stopPropagation();
            e.preventDefault();
            searchuserstoinvite();
        });

    });



    $('body').on('click', '#report-abuse', function() {

        var reportTemplate = '<div id="content_data" ><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>\
                 <div class="clearfix"></div>';
        $.dbeePopup(reportTemplate, {
            otherBtn:'<a href="#" id="reportbug" class="pull-right btn btn-yellow"  onClick="javascript:sendbugreport()" >Send</a>'
        });
        var db = $(this).attr('dbid');
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {
                'db': db
            },
            url: BASE_URL + '/report/reportabug',
            success: function(response) {
                $("#content_data").html(response.content);


            }

        });



    });


  
    $('body').on('click', '.closePopup', function() {
        $('#warningMsg').hide();

    });

    



    




    $('body').on('click', '.report-abusetwo', function() {


        var db = $(this).attr('dbid');
        var type = $(this).attr('type');
        var commentID = $(this).attr('commentID');
        //createrandontoken(); // creating user session and token for request pass
        data = 'db=' + db + '&type=' + type + '&commentID=' + commentID;
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: data,
            url: BASE_URL + '/report/abuse',
           /* beforeSend: function() {
                setTimeout(function() {
                    $dbConfirm({
                        content: 'Are you sure you would like to report this as abuse? ',            

                    });
                }, 200);
            },*/
            success: function(response) {
                //$("#content_data").html(response.content);
                setTimeout(function() {
                    $dbConfirm({
                        content: response.content,
                        yesClick: function() {
                            var db = $("#db_report").val();
                            var comment_report = $("#comment_report").val();
                            var type = $("#type_report").val();
                            //createrandontoken(); // creating user session and token for request pass
                            data = 'db=' + db + '&type=' + type + '&comment=' + comment_report

                            $.ajax({
                                type: "POST",
                                dataType: 'json',
                                data: data,
                                url: BASE_URL + '/report/sentabuse',
                                beforeSend: function() {
                                    $('#reportsend').hide();
                                },
                                success: function(response) {
                                    //$dbConfirm({content:response.message, yes:false});            
                                }
                            });
                        }

                    });
                }, 200);

            }
        });

    });


    $('body').on('click', '.removeComment', function() {

        var commentID       = $(this).attr('comment_ids');
        var islatestComment = $(this).attr('islatestComment');
        $dbConfirm({
            content: 'Are you sure you want to remove this comment?',
            yesClick: function() {
                removecomment(commentID, 1,islatestComment)
            }
        });
    });

    $('body').on('click', '.disconnectFromTwitter', function() {
        var thisEl = $(this);
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: BASE_URL + '/settings/desconnectfromtwitter',
            async: false,
            success: function(response) {
                var visibleFeed = $('.socialfeedWidget a:visible').size();
                twValue = '';
                closeSocialFeedSidebar();
                dashboardprofile($('#profileuser').val(), '0', '1');               
                thisEl.replaceWith('<a href="' + BASE_URL + '/twitter"  class="shareAllSocials twAllShare">\
                              <div class="signwithSprite signWithSpriteTwitter">\
                              <i class="fa dbTwitterIcon fa-5x"></i>\
                                <span>Twitter</span>\
                              </div>\
                               <div class="socialCntSts cntSts"><i class="signCntSprite signCntIcon"></i> Connect</div>\
                            </a>');
            }


        });

    });

    $('body').on('click', '.disconnectFromLinkedin', function() {
        var thisEl = $(this);
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: BASE_URL + '/settings/desconnectfromlinkedin',
            async: false,
            success: function(response) {
                //var visibleFeed = $('.socialfeedWidget a:visible').size();
                linkValue = '';
                closeSocialFeedSidebar();


                dashboardprofile($('#profileuser').val(), '0', '1');                
                thisEl.replaceWith('<a href="' + BASE_URL + '/social/linkedin"  class="shareAllSocials lnAllShare">\
                              <div class="signwithSprite signWithSpriteLinkden">\
                              <i class="fa dbLinkedInIcon fa-5x"></i>\
                                <span>LinkedIn</span>\
                              </div>\
                               <div class="socialCntSts cntSts"><i class="signCntSprite signCntIcon"></i> Connect</div>\
                            </a>');
            }
        });
    });

    $('body').on('click', '.disconnectFromFacebook', function() {
        var thisEl = $(this);
        var dataUrl = thisEl.attr('data-url');
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: BASE_URL + '/settings/desconnectfromfacebook',
            success: function(response) {
                dashboardprofile($('#profileuser').val(), '0', '1');
                //var visibleFeed = $('.socialfeedWidget a:visible').size();
                fbValue = '';
                closeSocialFeedSidebar();
                thisEl.replaceWith('<a  href="' + dataUrl + '" class="shareAllSocials fbAllShare" >\
                  <div class="signwithSprite signWithSpriteFb">\
                   <i class="fa dbFacebookIcon fa-5x"></i>\
                    <span>Facebook</span>\
                  </div>\
                  <div class="socialCntSts cntSts"><i class="signCntSprite signCntIcon"></i> Connect</div>\
                </a>');
            }

        });

    });




    $('body').on('click', '.dbeePostCmnt', function(event) {
        event.preventDefault();
        $(this).closest('li').find('#myhomedbee_comment').focus();
    });

    $('body').on('click', '#followme-label', function() {


        var datauserid = $('#followme-label').attr('data-id');


        var datauser = $('#followme-label').attr('data-user');

        if (datauser == 'following') {
            $(this).closest('li').remove();
        }

        if ($(".usrList").size() == 0) {

            $('#my-dbees .noFound').show();
        }

    });


   var offsetvar= 0; 


    $('body').on('click', '#other-following-profile,#my-following-profile,#show-more-other-following-profile,#FollowingLatestUser,#FollowingEarliestUser,.SortAlphabetFollwing ', function() {

        var FollowingTemplate = '<div class="formRow" id="content_data" ><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>';

        if($('#dbeecontrollers').val()!='dashboarduser')
        {                   
            var href = $('#ProfileLink').attr('href');            
            window.location.href = href+'#my-following-profile';
            return false;
         }

        var OtherUser = $(this).attr('data-OtherUser');
        OtherUser = typeof(OtherUser) != 'undefined' ? OtherUser : '0';  
        offsetvar= 0;

        var extraparam = '';        
        var sortval = $(this).attr('data-sort');
        sortval = typeof(sortval) != 'undefined' ? sortval : '';

        var regsortval = $(this).attr('data-char');
        regsortval = typeof(regsortval) != 'undefined' ? regsortval : '';        
        $('ul.follower-show-more li:first').hide();
        $('#follower-dbees-more').html('');
        $('.follower-show-more').hide();
        $('.btnShowMore').hide();

        if (this.id == 'my-following-profile') {
            extraparam = 'MyProfile';
        }

        if (this.id == 'other-following-profile') {
            extraparam = 'OtherProfile';
        }
         var pr = $(this).closest('.tabLinks');
         if(pr.length!=1 && OtherUser==0){
           $('#leftListing .tabLinks').remove();
         }
        
        var offset = $(this).attr('currentOffset');
        offset = (typeof(offset) == 'undefined') ? '0' : offset;

        var caller = $(this).attr('caller');
        caller = (typeof(caller) == 'undefined') ? '' : caller;

        if(OtherUser==1)
        {
            extraparam = 'OtherProfile';

        }

        var userid = $(this).attr('following_user_uid');
        var fellowby = $(this).attr('fellowby');
        if (fellowby != 'profile' && OtherUser!=1) {
            $('#dashboarduserDetails,#customFieldsDown,#postMenu,#leftListing .profileStatsWrp,.customizeDeshboard,#leftListing .group-highlighted').fadeOut('fast');           
        }
        var elId = '#dbee-feeds, #my-dbees';
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {
                'userID': userid,
                'extraparam': extraparam,
                'offset': offset,
                'caller': caller,
                'sortval': sortval,
                'regsortval':regsortval,
                'OtherUser':OtherUser
            },
            url: BASE_URL + '/following/followeruser',
            beforeSend: function() {
                if (sortval=='' && regsortval=='') {
                    var loaderHtml = '<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>';
                    if(OtherUser==1){
                        $(elId).html('');
                        $dbLoader(elId);
                        $(elId).addClass('myWhiteBg');
                        
                    }else{
                        $(elId).html(loaderHtml);
                    }
                }
                else
                {
                     $dbLoader('.forlader');
                     $('.searchMemberList').css('display','none');
                }

            },
            cache: false,

            success: function(response) {
               if(OtherUser!=1)
               {
                if ($(document).scrollTop() != 0) {
                    $('html, body').animate({
                        scrollTop: 0
                    }, 'fast');
                }
              }
                
                if (caller == '') {
                   // if ($('#dbee-feeds').is(':visible') == true) {
                        $('#dbee-feeds, #my-dbees').html(response.content);
                  //  } else {
                     //   $('#my-dbees').html(response.content);
                  //  }

                } else {
                    $('#following-dbees-more').append(response.content);
                }

                if (response.total > 19) {
                    //alert(response.offset)
                    $('.following-show-more').show();
                    $('.following-show-more').attr('currentOffset', (response.offset + 1));
                } else {
                    $('.following-show-more').hide();
                }


            }

        });
    });




    $('body').on('click', '#my-contacts,#ContactsLatestUser,#ContactsEarliestUser,.SortAlphabetContacts', function() {

        var FollowingTemplate = '<div class="formRow" id="content_data" ><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>';

        offsetvar= 0;

        if($('#dbeecontrollers').val()!='dashboarduser')
        {                   
            var href = $('#ProfileLink').attr('href');            
            window.location.href = href+'#my-contacts';
            return false;
        }

        var extraparam = '';

        var sortval = $(this).attr('data-sort');
        sortval = typeof(sortval) != 'undefined' ? sortval : '';

        var regsortval = $(this).attr('data-char');
        regsortval = typeof(regsortval) != 'undefined' ? regsortval : '';


        if (this.id == 'my-contacts') {
            extraparam = 'MyProfile';
        }

        if (this.id == 'other-contacts-profile') {
            extraparam = 'OtherProfile';
        }


        var addtocontacthidden = $('#addtocontacthidden').val();

        var addtocontacoff = '';
        if (addtocontacthidden == 0) {
            var addtocontacoff = '<p style="margin-bottom:10px;">The platform admin has turned OFF the ability to add users to your Contacts list. </p>';
        }

        var userid = $(this).attr('data_myid');

        $('#dashboarduserDetails').fadeOut('fast');
        $('#postMenu').fadeOut('fast');
        $('.customizeDeshboard').fadeOut('fast');
        $('.biogrophydisplay').fadeOut('fast');


        $('#leftListing .profileStatsWrp').fadeOut('fast');
        $('#leftListing .group-highlighted').fadeOut('fast');


        $.ajax({

            type: "POST",
            dataType: 'json',
            data: {
                'userID': userid,
                'extraparam': extraparam,                
                'sortval': sortval,
                'regsortval':regsortval
            },
            url: BASE_URL + '/following/contactlist',
            beforeSend: function() {
                 if (sortval=='' && regsortval=='') {

                    if ($('#dbee-feeds').is(':visible') == true) {
                        $('#dbee-feeds').html('<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>');
                    } else {

                        $('#my-dbees').html('<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>');
                    }
                }
                else
                {
                     $dbLoader('.forloader');
                     $('.searchMemberList').css('display','none');
                }

            },

            cache: false,

            success: function(response) {

                if ($(document).scrollTop() != 0) {
                    $('html, body').animate({
                        scrollTop: 0
                    }, 'fast');
                }

                if ($('#dbee-feeds').is(':visible') == true) {
                    $('#dbee-feeds').html(response.content);
                } else {
                    if (typeof(response.content) == 'undefined') {

                        $('#my-dbees').html('<div id="middleWrpBox"><div style="margin-bottom:15px" class="user-name">My Contacts List</div>' + addtocontacoff + '<div class="next-line"></div><div class="noFound"><strong>You have not added any contacts.</strong></div></div>');

                    }

                    $('#my-dbees').html(response.content);
                }
            }

        });


    });


$('body').on('click', '#my-tagged_db', function() {

        var FollowingTemplate = '<div class="formRow" id="content_data" ><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>';

        var extraparam = '';
        
        if($('#dbeecontrollers').val()!='dashboarduser')
        {                   
            var href = $('#ProfileLink').attr('href');            
            window.location.href = href+'#my-tagged_db';
            return false;
        }

        var userid = $(this).attr('data_myid');

        $('#dashboarduserDetails,.customizeDeshboard,.biogrophydisplay,#customFieldsDown,#leftListing .profileStatsWrp,#leftListing .group-highlighted,#postMenu').fadeOut('fast');
        $('#leftListing .tabLinks').remove();
        $.ajax({

            type: "POST",
            data: {'userID': userid},
            url: BASE_URL + '/myhome/mytaggeddbee',
            beforeSend: function() {
                if ($('#dbee-feeds').is(':visible') == true) {
                    $('#dbee-feeds').html('<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>');
                } else {

                    $('#my-dbees').html('<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>');
                }

            },
            cache: false,
            success: function(response) {
                if ($(document).scrollTop() != 0) {
                    $('html, body').animate({
                        scrollTop: 0
                    }, 'fast');
                }
                var resultArr = response.split('~#~');
              
                $('#feedtype').val('tagpost');
                $('#startnewmydb').val(resultArr[1]);
                if ($('#dbee-feeds').is(':visible') == true) {
                    $('#dbee-feeds').html(resultArr[0]);
                } else {
                    if (typeof(resultArr[0]) == 'undefined') {

                        $('#my-dbees').html('<div style="margin-bottom:15px" class="user-name">My Tagged Posts</div>' + addtocontacoff + '<div class="next-line"></div><div class="noFound" style="margin-top:50px;"><strong>You have not added any contacts.</strong></div>');

                    }

                    $('#my-dbees').html('<div id="middleWrpBox" style="padding:13.7px;"><div style="float:none;" class="user-name titleHdpad">My Tagged Posts</div></div>' + resultArr[0]);
                    getMentionUser(resultArr[3]);            
                    $dbTip();
                }
            }

        });


    });




    /*user search start here */
    var searchMember = '';
    var searchMemberByfield = false;
    var previousSearchValue = "";
    var copyListUser;
    userFinder = function(thisEl, userName) {
        var thisElement = thisEl;
        var data = '';

        if (thisElement != undefined) {
            var latestval = thisEl.attr('data-xx');
            var fellowby = thisEl.attr('fellowby');
            var sortby = thisEl.attr('data-char');
            var sortbycompany = thisEl.attr('data-charcomapny');

        } else {
            var latestval = '';
        }


        searchMember = $.trim($("#searchMember").val());
        selectedfilter = [];

        var n = $(".FilterUsers:checked").length;


        if (n > 0) {
            $(".FilterUsers:checked").each(function() {
                selectedfilter.push($(this).val());
            });

            selectedfilter = $.unique(selectedfilter);
        }



        if (searchMember == "") {
            searchMember = $.trim($("input[type='hidden'].SearchUser").val());
        }else{
            if(searchMember.length<3){
                $dbConfirm({content:'Please type at least 3 characters', yes:false});
                return false;
            }
        }
        if (fellowby != 'profile') {

            $('#dashboarduserDetails, #postMenu, #leftListing .profileStatsWrp, #rightListing .proinfo,#leftListing .group-highlighted,#rightListing .contactInfo').fadeOut('fast');
        }

        if (latestval == '' && searchMemberByfield == false) {
            var FollowingTemplate = '<div class="formRow" id="content_data" ><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>';
            if ($('#dbee-feeds').is(':visible') == true) {
                $('#dbee-feeds').html('<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>');

            } else {
                $('#my-dbees').html('<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>');
            }
        }
        if (latestval == 'alphabetically') {
           
            copyListUser = $('.searchMemberList li').detach();
            $dbLoader('.searchMemberList');
            data = {
                'filtertby': selectedfilter,
                'sortby': sortby,
                'SearchUserTextXX': searchMember,
                'sortbycompany': sortbycompany
            };
        }
        if (latestval == 'comapnyalphabetically') {

            copyListUser = $('.searchMemberList li').detach();
            $dbLoader('.searchMemberList');
            data = {
                'filtertby': selectedfilter,
                'sortby': sortby,
                'SearchUserTextXX': searchMember,
                'sortbycompany': sortbycompany
            };
        }
        if (latestval == 'FilterUsers') {
            $('html,body').animate({
                scrollTop: 0
            });

            copyListUser = $('.searchMemberList li').detach();
            $dbLoader('.searchMemberList');
            data = {
                filtertby: selectedfilter,
                SearchUserTextXX: searchMember,
                searchMember: searchMember
            };
            if($('#searchUserAllMenu .active').attr('data-xx')=='comapnyalphabetically'){
                var sortbycompany = $('#CompanySortAlphbet .active').text();
               // data['sortbycompany'] = sortbycompany;
            }else if($('#searchUserAllMenu .active').attr('data-xx')=='alphabetically'){
                 var sortby= $('#MemberSortAlphbet .active').text();
                data['sortby'] = sortby;
            }

        } else if (latestval == 'noload') {


            data = {
                'filtertby': selectedfilter,
                'orderby': 1,
                'SearchUserTextXX': searchMember,
                'sortbycompany': sortbycompany
            };
            copyListUser = $('.searchMemberList li').detach();
            $dbLoader('.searchMemberList');
        } else if (latestval == 'viewAll') {
            searchMember = '';
            selectedfilter.splice(0, selectedfilter.length);
            $(".FilterUsers").prop("checked", false);

            data = {SearchUserTextXX:'',orderby:1};
            copyListUser = $('.searchMemberList li').detach();
            $dbLoader('.searchMemberList');

        } else if (latestval == '' && searchMemberByfield == true) {
           if(searchMember.length<1) {
              return false;
            }            
            $('.searchIcon2').hide();
            if($('#search-results-users').is(':empty') == true) {
                $dbLoader('#search-results-users');
                $('#search-results-users').show();
            }

            $('.srcUsrWrapper').append('<div id="userSearchLoader" class="fa fa-spin fa-spinner fa-lg searchIcon2"></div>');
            data = {
                'filtertby': selectedfilter,
                'searchMember': searchMember
            };
        }



        $.ajax({
            type: "POST",
            dataType: 'json',
            data: data,
            url: BASE_URL + '/following/allusers',
            beforeSend: function() {
                $('div#last_msg_loader').remove();

            },
            success: function(response) {

                $('#dbee-feeds, #my-dbees').removeClass('postListing');

                if ($('#dbee-feeds').is(':visible') == true) {
                    $('#dbee-feeds').html(response.content);



                } else if ($('#search-results-users').is(':visible') == true) {


                    $('#searchMember').remove();

                    $('#search-results-users').html(response.content);
                    //$('.SearchWordSpan').html(searchMember);     


                    var UserCount = $("#UserCount").val();

                    if ($.trim(UserCount) == "") {
                        $('#search-results-users-tab').html('Users');
                        $('#allLatestUser').hide();
                        $('.SortAlphabet ').hide();
                        $('#searchMember').val(searchMember);
                    } else {
                        $('#search-results-users-tab').html('Users');
                        $('#searchMember').val(searchMember);
                    }

                    $('#middleWrpBox .pageTitle').remove();
                    var ThirdOptionHidden = $('#ThirdOptionHidden').val();

                    $('#searchUserAllMenu a[data-xx="viewAll"]').remove();

                    if (showthirdoption == true) {
                        //$('#ThirdOption').html('<a href="javascript:void(0);" id="ThirdOption" data-third="'+ThirdOptionHidden+'">Back to searched keyword</a>');
                    }
                    $('#ThirdOption').click(function() {
                        var thirdVl = $('#ThirdOptionHidden').val();
                        $('#searchMember').val(thirdVl);
                        searchMemberByfield = true;
                        showthirdoption = '';
                        userFinder();
                    });

                } else {



                    $('#my-dbees').html(response.content);                      
                    $('#searchMember').val(searchMember);
                    var ResulttedUserCount = $("#ResulttedUserCount").val();
                    if (typeof ResulttedUserCount == 'undefined') {
                        ResulttedUserCount = 0;
                    }
                    $('#Usercountfilter').html(ResulttedUserCount + ' <i>total <br>user</i>');

                }

                searchMemberByfield = false;
                $('#filterTag a').click(function() {
                    var valueClosed = $(this).attr('data-filterclose');

                    var aindex = selectedfilter.indexOf(valueClosed);
                    selectedfilter.splice(aindex);
                    $('.searchByThis input[value="' + valueClosed + '"]').trigger('click');

                });
            },
            error: function() {
                searchMemberByfield = false;
                $dbLoader('.searchMemberList', '', 'close');
                if (copyListUser) {
                    copyListUser.appendTo('.searchMemberList');
                    copyListUser = null;
                }
            }
        });

    }

    $('body').on('click', '[data-xx]', function() {
        var thisEl = $(this);
        if (thisEl.attr('data-type') == 'search-results-users') {
            searchMemberByfield = true;
        }
        userFinder(thisEl);
    });

    $('body').on('keyup', '#searchMember', function(e) {
        if (e.keyCode == 13) {
            searchMemberByfield = true;
            showthirdoption = true;
            userFinder();
        }
    });
    $('body').on('click', '.userSearchResultTap a', function() {
        var tabContainerId = $(this).attr('data-type');
        $(this).closest('ul').find('.active').removeClass('active');
        $(this).addClass('active');
        $('.maindb-wrapper-border').hide();
        $('#' + tabContainerId).show();
    });

    var selectedfilter = new Array();

    function togglesearchres(n) {
        if (n == 'search-results-users') {
            userFinder();
        }
    }
    $(document).ready(function() {
        var scrollAjaxFreez = true;
        $(window).scroll(function() {
            
            var winTop = $(window).scrollTop();
            var IsMemberSearch = $('#SearchMemberScroll').val();

            if (IsMemberSearch == 1) {
                if (winTop > 250) {
                    $('.searchHeaderAllUser').addClass('scrollOnUseAll');
                } else {
                    $('.searchHeaderAllUser').removeClass('scrollOnUseAll');
                }
            }


            if (winTop == $(document).height() - $(window).height()) {

                var IsSearchText = $('#SearchMemberTextScroll').val();
                var IsSortBy = $('#SortByScroll').val();
                var SearchUser = $('.SearchUser').val();
                var SortByScrollCompany = $('#SortByScrollCompany').val();
                //var filterhidden=$('#filterhidden').val();

                if (IsMemberSearch == 1) {

                    var ID = $("#page").val();
                    ID = $("#page").val();

                    var UserCount = $("#UserCount").val();

                    var n = jQuery(".FilterUsers:checked").length;
                    if (n > 0) {
                        jQuery(".FilterUsers:checked").each(function() {
                            selectedfilter.push($(this).val());
                        });
                    }

                    var filterdatarray = jQuery.unique(selectedfilter);                     


                    if (UserCount > 39) {

                        if (scrollAjaxFreez == true) {
                            $.ajax({
                                type: "POST",
                                dataType: 'json',
                                data: {
                                    'ID': ID,
                                    'filtertby': filterdatarray,
                                    'IsSearchText': IsSearchText,
                                    'IsSortBy': IsSortBy,
                                    'UserCount': UserCount,
                                    'SearchUser': SearchUser,
                                    'sortbycompany': SortByScrollCompany
                                },
                                url: BASE_URL + '/following/allusers',
                                beforeSend: function() {
                                  if($('#last_msg_loaders').length==0){
                                        $('#last_msg_loaders').remove();
                                        $('.searchMemberList').append('<div id="last_msg_loaders" class="clear"></div>');
                                    }
                                  $dbLoader('#last_msg_loaders');
                                    scrollAjaxFreez = false;
                                },
                                success: function(response) {
                                    scrollAjaxFreez = true;
                                    $dbLoader('#last_msg_loaders', 1, 'close');
                                    if (response.content != '' || response.content != '<div class="clear noUserFoundResult noFound">no more users found</div>') {
                                        $(".usrList:last").after(response.content);
                                        if (response.page != 1)
                                            $("#page").val(response.page);
                                    } else {
                                        $('div#last_msg_loader').after('<div class="clear noUserFoundResult noFound">no more users found</div>');
                                        $('div#last_msg_loader').remove();
                                    }
                                    $('div#last_msg_loader').empty();

                                }
                            }); // ajax end here
                        }
                    }

                }

            }

        });

    });


    $('body').on('mouseover', '.searchMemberList li', function() {
        $('i', this).removeClass('fa-envelope-o').addClass('fa-envelope');
    });
    $('body').on('mouseleave', '.searchMemberList li', function() {
        $('i', this).removeClass('fa-envelope').addClass('fa-envelope-o');
    });
    /* user search end here */


    //$('body').on('click','#other-followers-profile',function(){
    $('body').on('click', '#other-followers-profile,#show-more-other-followers-profile,#my-followers-profile,#FollowersLatestUser,#FollowersEarliestUser,.SortAlphabetFollowers ', function() {

        var FollowerTemplate = '<div id="content_data"><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>';
        offsetvar= 0;

        if($('#dbeecontrollers').val()!='dashboarduser')
        {                   
            var href = $('#ProfileLink').attr('href');            
            window.location.href = href+'#my-followers-profile';
            return false;
         }

         var pr = $(this).closest('.tabLinks');
         var OtherUser = $(this).attr('data-OtherUser');
            OtherUser = typeof(OtherUser) != 'undefined' ? OtherUser : '0'; 
         if(pr.length!=1 && OtherUser==0){
           $('#leftListing .tabLinks').remove();
         }
       
        var sortval = $(this).attr('data-sort');
        sortval = typeof(sortval) != 'undefined' ? sortval : '';

        var regsortval = $(this).attr('data-char');
        regsortval = typeof(regsortval) != 'undefined' ? regsortval : '';

        $('#following-dbees-more').html('');
        $('.following-show-more').hide();
        //$.dbeePopup(FollowerTemplate);

        if (this.id == 'my-followers-profile') {
            extraparam = 'MyProfile';
        }

        if (this.id == 'other-followers-profile') {
            extraparam = 'OtherProfile';
        }

        if(OtherUser==1)
        {
           extraparam = 'OtherProfile';

        } 

        var offset = $(this).attr('currentOffset');
        offset = (typeof(offset) == 'undefined') ? '0' : offset;

        var caller = $(this).attr('caller');
        caller = (typeof(caller) == 'undefined') ? '' : caller;

        var userid = $(this).attr('follow_user_uid');
        var fellowby = $(this).attr('fellowby');

        if (fellowby != 'profile' && OtherUser!=1) {
            $('#dashboarduserDetails,#customFieldsDown,#postMenu,#leftListing .profileStatsWrp,#leftListing .group-highlighted,.customizeDeshboard,.biogrophydisplay').fadeOut('fast');           
        }


var elId = '#dbee-feeds, #my-dbees';
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {
                'userID': userid,
                'offset': offset,
                'extraparam':extraparam,
                'caller': caller,
                'sortval': sortval,
                'regsortval':regsortval,
                'OtherUser':OtherUser
            },
            url: BASE_URL + '/following/followinguser',
            beforeSend: function() {
                
                if (sortval=='' && regsortval=='') {
                    var loaderHtml = '<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>';
                    if(OtherUser==1){
                         $(elId).html('');
                        $dbLoader(elId);
                        $(elId).addClass('myWhiteBg');
                        
                    }else{
                        $(elId).html(loaderHtml);
                    }
                    
                     
                   /* if ($('#dbee-feeds').is(':visible') == true) {
                        $('#dbee-feeds').html('<div style="margin:20px 0 0 20px;">Loading...</div>');
                    } else {

                        $('#my-dbees').html('<div style="margin:20px 0 0 20px;">Loading...</div>');
                    }*/
                }
                else
                {
                     $dbLoader('.forloader');
                     $('.searchMemberList').css('display','none');
                }
            },
            cache: false,
            success: function(response) {
            if(OtherUser!=1){
                if ($(document).scrollTop() != 0) {
                    $('html, body').animate({
                        scrollTop: 0
                    }, 'fast');
                }
            }
                $(elId).removeClass('myWhiteBg');
                if ($('#dbee-feeds').is(':visible') == true) {
                    $('#dbee-feeds').html(response.content);

                } else {
                    if (caller == '')
                        $('#my-dbees').html(response.content);
                    else
                        $('#follower-dbees-more').append(response.content);

                    if (response.total > 19) {
                        //alert(response.offset)
                        $('.follower-show-more').show();
                        $('.follower-show-more').attr('currentOffset', (response.offset + 1));
                    } else {
                        $('.follower-show-more').hide();
                    }
                }

            }
        });

    });

$(document).on('click','.show_more',function(){
        
        var ID = $(this).attr('id');
        var datalist = $(this).attr('data-list');
        var userid = $(this).attr('data-userID');
        var caller = $(this).attr('data-caller');
        var sortval = $(this).attr('data-sortval');
        var regsortval = $(this).attr('data-regsortval');
        var regsortvalcon = $(this).attr('data-regsortvalcon');
         offsetvar += 10;
        var extraparam='MyProfile';      
        var OtherUser = $(this).attr('data-OtherUserx');
        var URL = '';
        if(datalist=='Following')
        {
            URL=BASE_URL + '/following/followeruserloadmore';
        }
        else if(datalist=='Followers')
        {
            URL=BASE_URL + '/following/followinguserloadmore';
        }
        else if(datalist=='Contacts')
        {
            URL=BASE_URL + '/following/contactlistloadmore';
        }

        $('.show_more').hide();
        $('.loding_txt').show();
        
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: URL,
            data: {
                'userID': userid,
                'extraparam': extraparam,
                'offset': offsetvar,
                'caller': caller,
                'sortval': sortval,
                'regsortval':regsortval,
                'OtherUser':OtherUser
            },
            success:function(response){
               $('#show_more_main'+ID).remove();
               $('#dbee-feeds .searchMemberList , #my-dbees .searchMemberList ').append(response.content);
            }
        });
        
    });

    $('body').on('click', '#my-leaguepos-profile', function(e) {
        $('#my-leaguepos-profile').append(' <i class="fa fa-spinner fa-spin"> </i>').css('cursor', 'default');
        e.preventDefault();
        e.stopPropagation();
        var LeagueposTemplate = '<div id="content_data" class="leaguesPostPopUp"><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>';
        $.dbeePopup(LeagueposTemplate);
        var userid = $(this).attr('secure_uid');
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {
                'userID': userid
            },
            url: BASE_URL + '/profile/league',
            async: false,
            complete: function() {
                setTimeout(function() {
                    $('#my-leaguepos-profile .fa-spin').remove();
                    $('#my-leaguepos-profile').css('cursor', 'pointer');
                }, 2000);
            },

            cache: false,

            success: function(response) {
                $("#content_data").html(response.content);
                seeuserleaguepop('mostfollowed', userid);
                setTimeout(function() {
                    $.dbeePopup('resize');
                }, 300);

            }

        });


    });


    $('body').on('click', '.slides img', function(e) {
         closeSDetails();
    });
    
    $('body').on('click', '.headerBannerfull', function(e) {
        e.stopPropagation();
          closeSDetails();
        var adid = $('.rightbannerAdvertisement, .headerBannerfull').attr('data-adid');
        var bannerid = '';

        var position = '';
        if ($(this).closest('div').hasClass('rightbannerAdvertisement') == true) {
            position = 'right';
            bannerid = $(this).closest('.rightbannerAdvertisement').find('.headerBannerfull').attr('data-bannerid');
        } else {
            position = 'header';
            bannerid = $(this).attr('data-bannerid');
        }

        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {
                'adid': adid,
                'position': position,
                'bannerid': bannerid
            },
            url: BASE_URL + "/advert/trackadvert",
            async: false,
            success: function(response) {
                //$("#content_data").html(response.content);
                // do notheing          

            }

        });

    });

      


    $('body').on('click', '#upload-picture', function() {
       /* console.log( getCropDataUrl())
        return false;*/
        $('#upload-picture').append(' <i class="fa fa-spinner fa-spin"> </i>').css('cursor', 'default');
        $("#upload-picture").attr("disabled", "disabled");
        pictureData = getCropDataUrl();

        $.post(BASE_URL + '/profile/changepic', {
            picture: pictureData
        }, function(data) {
            $('#upload-picture .fa-spin').remove();
            if (data.status == 'success') 
            {
                picture = data.picture;
                setTimeout(function() {

                    $('#upload-picture').css('cursor', 'pointer');
                }, 200);

                $('.closePostPop').trigger('click');

                $('#profileimage').removeAttr('style').css({
                    backgroundPosition: 'left top',
                    backgroundImage: 'url('+IMGPATH+'/users/medium/' + picture + ')',
                    backgroundRepeat: 'no-repeat',
                    backgroundSize: 'contain'
                });
                $('.bgProfile img').attr('src', IMGPATH+'/users/medium/' + picture );
                 userpic = picture;
                $('.myPicForChat img').attr('src',  IMGPATH+'/users/small/' + picture + '');
                $('.postListContent .psUserName img, .mymenuItem img').attr('src', IMGPATH+'/users/small/' + picture);
                $('.minPostTopBar .pfpic img, .listingCommentLatest .cmntuserLink img').attr('src', IMGPATH+'/users/small/' + picture);
                if($('.cropAppendWrp').is(':visible')==true){
                  $('.cropAppendWrp').hide();
                  $('.welcomeScreenForTour').show();
                  $('.welcomeScreenForTour .prPicUploader').css({background:'url('+IMGPATH+'/users/' + picture+')', backgroundSize:'cover'});
                  $('.welcomeScreenForTour #pixUploader').show();
                  $('.welcomeScreenForTour .prPicUploader .dz-preview').remove();
                  $('.prPicUploader .fa-spin').addClass('fa-camera').removeClass('fa-spin fa-spinner');
                }
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
                    }
                });


            } else {
                $messageError(data.message);

            }
        }, 'json');

    });

$('body').on('change', '.pic-edit-action input', function(e) {
   $.dbeePopup('<div></div>', {
              otherBtn: '<a href="javascript:void(0);" style="display:none;" class="pull-right btn btn-yellow"  id="upload-picture">Save picture</a>',
              load:function (){
                 $.dbCropImage({element:'.dbeePopContent', event:e, load:function(){
                    $('#upload-picture').show()
                 }});
              }
          });
  
    
});
      /*$('body').on('click', '.pic-edit-action', function() {
          var profilePicTemplate = '<div class="newPicWrapper" >\
                                          <h2 class="titlePop">Choose new profile picture</h2>\
                                          <div class="formRow" id="pixUploader">\
                                              <form action="/file-upload" class="dropzone" id="uploadDropzoneProfile">\
                                                 <div class="fallback">\
                                                  <input type="file" name="file"  />\
                                                </div>\
                                              </form>\
                                              <input type="hidden" name="PostPix" id="profile_picture" value="" />\
                                          </div>\
                                   </div>';

          $.dbeePopup(profilePicTemplate, {
              otherBtn: '<a href="javascript:void(0);" style="display:none;" class="pull-right btn btn-yellow"  id="upload-picture">Save picture</a>'
          });
          $.profilePicUpload({element:'#uploadDropzoneProfile', cropAppend:'.dbeePopContent', cropLoad:function(){
            $('#upload-picture').show();
          }});
          $('#uploadDropzoneProfile').trigger('click');
      });*/




    $('body').on('click', '#mynotificationContainer .notiSettingBtn', function(event) {
        event.preventDefault();
        var dataType = $(this).attr('data-type');
        updatenotificationsetting($(this).attr('data-type'));
    });
    
    $('body').on('click', '#hiddenfeed', function(event) {
        //event.preventDefault();       
        var uid = $(this).attr('data-type');


        if ($(this).is(':checked') == true) {
            var feed = 1;
        } else {
            var feed = 0;
        }

        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {
                'uid': uid,
                'feed': feed
            },
            url: BASE_URL + '/dashboarduser/updatehidefeed',
            success: function(response) {
                //          setTimeout(function(){
                //  $dbConfirm({content:response.msg, yes:false});
                // },500);                   
            },
            error: function(request, status, error) {
                $("#content_data").html("Some problems have occured. Please try again later: " + error);
            }

        });

    });

    $('body').on('click', '#my-comments-home', function(event) {

    //alert($('#dbeecontrollers').val()); 
     $('#leftListing .tabLinks').remove();    
    if($('#dbeecontrollers').val()!='dashboarduser')
    { 
       // alert('wqwqwq');
        //$.cookie('MyMenuItem', 'my-comments-home');
               
        var href = $('#ProfileLink').attr('href');
        //setTimeout(function(){ window.location = href; }, 1000);
         window.location.href = href+'#my-comments-home';
        return false;
     }
        event.preventDefault();
        $('.customizeDeshboard').hide();
        $('.biogrophydisplay').fadeOut('fast');
        user = $(this).attr('commentsid');
        if ($('#dbee-feeds').is(':visible') == true) {
            $('#dbee-feeds').html('<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>');
        }
        seeglobaldbeelist(user, '1', 'my-comments-home', 'comment', 'index', 'comment');
       

    });


    $('html').on('touchstart', function() {
        $('html').addClass('noTouch');

    });
 
    // group page
    $('body').on('click', '.groupOptionsContainer, .group-feed .pstBriefFt', function(event) {
        event.stopPropagation();
    });
    $('body').on('click', '.group-feed', function(event) {
        var url = $(this).attr('data-url');
        window.location.href = url;
    });
    // group page

    // this is for twitter feed view
    $('body').on('click', '#alltweet .twTagsTab li', function() {
        $(this).closest('ul').find('.active').removeClass('active');

        var index = $('#alltweet .twTagsTab li').index(this) + 1;
        $('#twiterKeyContainer li .twKeyDetails').hide('fast');
        //if($('#twiterKeyContainer li:nth-child('+index+') .twKeyDetails').is(':visible')!=true){
        $(this).addClass('active');
        $('#twiterKeyContainer li:nth-child(' + index + ') .twKeyDetails').show();
        //}
    });

    // this is for twitter feed view

    $(window).scroll(function(event) {
        var scrollTop = $(this).scrollTop();
        if (scrollTop > 300) {
            $('#scrollTop').fadeIn();
        } else {
            $('#scrollTop').fadeOut();
        }

    });
    
    $('#scrollTop').click(function(e) {
        e.preventDefault();
        $('body, html').animate({
            scrollTop: 0
        });
    });

    // equal height
    $.fn.equalizeHeights = function() {
        return this.height(
            Math.max.apply(this,
                $(this).map(function(i, e) {
                    return $(e).height()
                }).get()
            )
        )
    }


    // equal height


    $('body').on('click', '#notify-comment-div label', function(event) {
        event.preventDefault();
        var $check = $(':checkbox', this);
        $check.prop('checked', !$check.prop('checked'));
        var db = $('#dbid').val();
        createrandontoken(); // creting user session and token for request pass 
        data += "&db=" + db + "&type=1" + '&' + userdetails;
        $.ajax({
                url: BASE_URL + '/comment/commentnotify',
                type: 'POST',
                data: data,
            })
            .done(function(data) {
                var resultArr = data.split('~');
                if (resultArr[0] == '1') { // IF TURNED ON
                    if (resultArr[1] == '1') { // IF TOGGLE EMAIL
                        $('#notify-email-on').addClass('notify-email-status-on');
                        $('#notify-email-off').removeClass('notify-email-status-on');
                    } else if (resultArr[1] == '2') { // IF TOGGLE POPUP NOTES
                    }
                } else if (resultArr[0] == '0') { // IF TURNED OFF
                    if (resultArr[1] == '1') { // IF TOGGLE EMAIL
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


            });


    });

    $('body').on('click', '#embedlinkPost', function() {
        if ($(this).is(':checked')) {
            $('#shortUrlLink').show();
            var shortUrl = $('#shortUrl').val();
            var retweetTextarea = $('.retweetTextarea').val().length;
            var charLenght = $('#hiddentwittername').val().length;
            var charLenght1 = $('#shortUrl').val().length;
            charLenght = (charLenght1 + charLenght+retweetTextarea);
            $('.retweetTextarea').attr('maxlength', (139 - charLenght));
            $('.limitLength').html((139 - charLenght) + ' limit');
        } else 
        {
            $('#shortUrlLink').hide();
            var shortUrl = $('#shortUrl').val();
            var retweetTextarea = $('.retweetTextarea').val().length;
            var charLenght = $('#hiddentwittername').val().length;
            charLenght = (retweetTextarea + charLenght);
            $('.retweetTextarea').attr('maxlength', (139 - charLenght));
            $('.limitLength').html((139 - charLenght) + ' limit');
        }
    });
    
    $('body').on('keyup', '.retweetTextarea', function() 
    {
       if ($('#embedlinkPost').is(':checked')) 
       {
            $('#shortUrlLink').show();
            var shortUrl = $('#shortUrl').val();
            var retweetTextarea = $('.retweetTextarea').val().length;
            var charLenght = $('#hiddentwittername').val().length;
            var charLenght1 = $('#shortUrl').val().length;
            charLenght = (charLenght1 + charLenght+retweetTextarea);
            $('.limitLength').html((139 - charLenght) + ' limit');
        } 
        else 
        {
            $('#shortUrlLink').hide();
            var shortUrl = $('#shortUrl').val();
            var retweetTextarea = $('.retweetTextarea').val().length;
            var charLenght = $('#hiddentwittername').val().length;
            charLenght = (retweetTextarea + charLenght);
            $('.limitLength').html((139 - charLenght) + ' limit');
        }
    });


    $('body').on('click', '.GoInvite', function(event) {

        event.preventDefault();
        var keyword = $.trim($('#keyword').val());
        var keywordlength = keyword.length;
        if (keywordlength < 3) {
            $dbConfirm({
                content: 'Please enter at least three characters for search',
                yes: false,
                error: true
            });
            return false;
        }
        $('#search-invite-list').show().find('.formField').html('<div style="margin:20px 0 0 0;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>');

        $.ajax({
            type: "POST",
            url: BASE_URL + "/dbeedetail/invitesearchuser",
            data: {
                "keyword": keyword
            },
            success: function(result) {
                var resultArr = result.split('~#~');
                if (resultArr[0] != '0') {
                    $('#search-list .formRow').removeClass('singleRow');
                    $('#search-invite-list').show().find('.formField').html(resultArr[0]);
                    $.dbeePopup('resize');
                    $('#total-search').val(resultArr[1]);
                }
            }
        });
    });

   

    // for create group  invite user search
    $('body').on('keypress', '#searchusers #keyword', function(event) {
        if (event.which == 13) {
            $('#searchusers .GoInvite').trigger('click');
            return false;
        }
    });

    $('body').on('click', '#notify-expertNotificationOn .newRadioBtn', function(e) {
        e.preventDefault();
        var p = $(this).closest('#notify-expertNotificationOn');
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
            data: {
                'emailSetting': status
            },
            url: BASE_URL + '/profile/updatenotificationexpert',
            success: function(response) {

                $dbConfirm({
                    content: response.message,
                    yes: false
                });
            }
        });
    });

   

    // for create group  invite user search


    $('body').on('click', '#shareOnWall, .bookMarksShare', function() {
       
        var reportTemplate = '<h2>Share post on</h2> <div id="content_data"><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>';
        $.dbeePopup(reportTemplate,{ overlayClick: false});
        var dbeeID = $('#dbid').val();
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {
                'dbeeID': dbeeID
            },
            url: BASE_URL + '/social/shareonwall',
            success: function(response) {
                $('#content_data').html(response.content);
            }
        });
    });

    // special dbee code here 

    

    $('body').on('click', '#ShowWarningPopup', function() {
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: BASE_URL + '/dbeedetail/expiresocial',
            success: function(response) {
                 $('#warningMsg').hide();
            }
        });
    });
    

    $('body').on('click', '.tweetDelete', function() {
        id = $(this).attr('data-id');
        $dbConfirm({
            content: 'Are you sure you want to remove this post tweet',
            yes: true,
            yesLabel: 'Yes',
            no: true,
            noLabel: 'No',
            yesClick: function() {
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    data :{'id':id},
                    url: BASE_URL + '/ajax/tweetdelete',
                    success: function(response) {
                       $('#tweet_'+id).hide();
                       socket.emit('removetweet', id); 
                    }
                });
            }
        });
    });

    $('body').on('click', '#ShowWarningPopupLogin', function() {
        $('#warningMsg').hide();
        window.location.reload(true);
    });

   

    $('body').on('click', '#RejectDbeeRequest', function() {
        $('#warningMsg').hide();
    });


 $('body').on('click', '#LiveRejectDbeeRequest', function() {
        $('#warningMsg').hide();
        $("#overlay").show();
        window.location.href= BASE_URL + '/myhome/';
    });

    // special dbee code here 




    $('body').on('click', '#socialConnect', function() {

        var reportTemplate = '<h2>Connect with</h2><div  id="content_data_button" class="SocialfeedConnect" style="padding:0 0 20px; overflow:hidden;"><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>';
        $.dbeePopup(reportTemplate, {
            overlayClick: false,
            otherBtn: ''
        });
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: '',
            url: BASE_URL + '/settings/socialconnect',
            async: false,
            success: function(response) {
                $("#content_data_button").html(response.content);
                $.dbeePopup('resize');

            }
        });
    });

    $('body').on('click', '.dbExpertUserDesc .iconEdit', function() {
        var saveTemplate = '<a href="javascript:void(0)" id="saveEditExpert" class="fa fa-check iconSaveEdit"></a>';
        var CancelEdit = '<a href="javascript:void(0)"  id="CancelEditExpert" class="fa fa-pencil iconCancelEdit"></a>';
        $('.iconSaveEdit, .iconCancelEdit').remove();
        $('.experttxt').each(function() {
            var thisVal = $.trim($(this).val());
            if (thisVal == '') {
                thisVal = $(this).attr('placeholder');
            }
            $(this).hide();
            $(this).closest('span').html(thisVal);
            $(this).remove();
        });
        $('.iconEdit').show();
        $(this).hide();
        $(this).after( CancelEdit+saveTemplate);
        $(this).prevAll('span').html('<input type="text" class="experttxt" placeholder="' + $(this).prevAll('span').text() + '" value=""/><input type="hidden" name="experttxthidden" class="experttxthidden" value="' + $(this).prevAll('span').text() + '" />');
        $('.experttxt').focus();


    });

    $('body').on('click', '.dbExpertUserDesc .iconCancelEdit', function() {
        $('.iconCancelEdit, .iconSaveEdit').hide();
        $(this).prevAll('.iconEdit').fadeIn();
        var textVal = $('.experttxthidden').val();

        $('.experttxt').remove();
        $(this).prevAll('span').text(textVal).removeAttr('contenteditable');
        $('.experttxthidden').val('');

    });

    $('body').on('click', '.dbExpertUserDesc .iconSaveEdit', function() {
        var valueSave = '';
        if ($(this).parents('p').hasClass('dbxprtTitle') == true) {
            valueSave = $(this).parents('p').find('input').val();
            if ($.trim(valueSave) == '') {
                valueSave = $(this).parents('p').find('input').attr('placeholder');
            }

            if (valueSave.trim() == '') {
                $dbConfirm({
                    content: 'Please enter your title',
                    yes: false,
                    error: true
                });
                return false;
            }

            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {
                    'experttxtName': valueSave
                },
                url: BASE_URL + '/profile/updatetitleexpert',
                success: function(response) 
                {

                    $dbConfirm({
                        content: response.message,
                        yes: false
                    });
                    $('.experttxthidden').val(valueSave);
                    $('.iconCancelEdit').trigger('click');
                    $('.dbExpertUserDesc').attr('style', 'font-style:normal');

                }
            });
        } else if ($(this).parents('p').hasClass('dbxprtTitleCompany') == true) {
            valueSave = $(this).parents('p').find('input').val();
            if ($.trim(valueSave) == '') {
                valueSave = $(this).parents('p').find('input').attr('placeholder');
            }
            if (valueSave.trim() == '') {
                //$messageError('Please enter your company');
                $dbConfirm({
                    content: 'Please enter your company',
                    yes: false,
                    error: true
                });
                return false;
            }

            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {
                    'experttxtName': valueSave
                },
                url: BASE_URL + '/profile/updatecompanyexpert',
                success: function(response) {                 
                    $('.experttxthidden').val(valueSave);
                    $('.iconCancelEdit').trigger('click');
                    $('.dbxprtTitleCompany').attr('style', 'font-style:normal');

                }
            });
        } else if ($(this).parents('p').hasClass('dbxprtTitleBio') == true) {
            valueSave = $(this).parents('p').find('input').val();
            if ($.trim(valueSave) == '') {
                valueSave = $(this).parents('p').find('input').attr('placeholder');
            }
            if (valueSave.trim() == '') {
                //$messageError('Please enter your biography');
                $dbConfirm({
                    content: 'Please enter your biography',
                    yes: false,
                    error: true
                });
                return false;
            }
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {
                    'experttxtName': valueSave
                },
                url: BASE_URL + '/profile/updatebioexpert',
                success: function(response) {
                    $('.experttxthidden').val(valueSave);
                    $('.iconCancelEdit').trigger('click');
                    $('.dbxprtTitleBio').attr('style', 'font-style:normal');
                }
            });
        } else {
            valueSave = $(this).parents('h3').find('input').val();
            if ($.trim(valueSave) == '') {
                valueSave = $(this).parents('h3').find('input').attr('placeholder');
            }
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {
                    'experttxtName': valueSave
                },
                url: BASE_URL + '/profile/updatefromexpert',
                success: function(response) {                 
                    $('.experttxthidden').val(valueSave);
                    $('.iconCancelEdit').trigger('click');
                    $('.experttxthidden').val('');
                }
            });
        }

    });

    // make fav dbee

    $('body').on('click', '.makefavourite', function() {
        var dbid = $(this).attr('data-id');
        createrandontoken(); // creating user session and token for request pass
        data = 'dbeeID=' + dbid + '&' + userdetails
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: data,
            url: BASE_URL + '/favourite',
            success: function(response) {
                if (response.status == 'success') {
                    $dbConfirm({
                        content: response.message,
                        yes: false
                    });

                }
            }
        });

    });

    $('body').on('click', '.eventJoin', function() {
        var id = $(this).attr('event-id');
        var type = $(this).attr('event-type');
        createrandontoken(); // creating user session and token for request pass
        data = 'type=' + type + '&id=' + id + '&' + userdetails
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: data,
            url: BASE_URL + '/event/confirmattendies',
            beforeSend: function() {
                $dbLoader('#memboxgr', 1);
                $('.eventJoin').addClass('processBtnLoader').append(' <i class="fa fa-spinner fa-spin"></i>');
                $(this).removeClass('eventJoin');
            },
            complete: function() {

            },
            cache: false,
            success: function(response) 
            {
                if (response.status == 'success') 
                {
                    $('.dbConfirmOverlay').fadeOut('slow', function() {
                        $(this).remove();
                    });
                    if(type!=1)
                        window.location.reload();
                    else if (response.totalMember != 0) 
                    {
                        $('#memboxgr').html(response.member);
                        if (parseInt(response.totalMember) == 1)
                            $('#attendiedCount').html(response.totalMember + ' attendee');
                        else if (parseInt(response.totalMember) == 0)
                            $('#attendiedCount').html(response.totalMember + ' attendees');
                        else
                            $('#attendiedCount').html(response.totalMember + ' attendees');

                        $('#memboxgr').flexslider({
                            animation: "slide",
                            animationLoop: false,
                            itemMargin: 5
                        });
                        $dbTip(); 
                    }
                    $('#postInEvent').html('<div is-event="isevent" data-id="'+id+'" data-EventType="'+type+'" class="pull-right btn btn-yellow postInGroup" >Post in this event</div>');
                    $dbTip();
                    $('.eventJoin').remove();
                    $('.firstUserCmnt').html("<i class='fa fa-pencil-square-o fa-2x'></i>  Click 'Post in this event' to start a new post.");
                } else
                    $dbConfirm({
                        content: response.message,
                        yes: false,
                        error: true
                    });
            },
            error: function(error) {
                loadError(error);
            }
        });

    });

     $('body').on('click', '.acceptEventJoin', function(e) 
    {
        e.stopPropagation();
        var id = $(this).attr('data-id');
        
        createrandontoken(); // creating user session and token for request pass
        data = 'type=join&id='+id+'&'+userdetails
        var Thhis = $(this);
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: data,
            url: BASE_URL + '/event/privateevent',
            cache: false,
            success: function(response) 
            {
                if(response.success){
                    callsocket();
                    Thhis.closest('.notiContainerList').remove();
                }else{
                    $dbConfirm({
                        content: 'You have already joined',
                        yes: false,
                        error: true
                    });
                    return false;
                }
            },
            error: function(error) {
                loadError(error);
            }
        });
    });

     $('body').on('click', '.rejectEventJoin', function() 
     {
        var id = $(this).attr('data-id');
        createrandontoken(); // creating user session and token for request pass
        data = 'type=reject&id='+id+'&'+userdetails
        var Thhis = $(this);
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: data,
            url: BASE_URL + '/event/privateevent',
            cache: false,
            success: function(response) 
            {
                
               if(localTick == false){ 
                           callsocket();
                            }
               Thhis.closest('.notiContainerList').remove();
            },
            error: function(error) {
                loadError(error);
            }
        });
    });


    $updateHidden = function(type, El) {
        var dbid = El.attr('data-id');
        var dbuser = El.attr('dbusr');
        var dbeeli = El.closest('li');
        data = 'dbeeID=' + dbid + '&' + 'dbuser=' + dbuser + '&' + 'action=' + type + '&' + userdetails
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: data,
            url: BASE_URL + '/myhome/adddashboard',
            success: function(response) {
               
                if (response.type == 2) {
                    $('#dbee-id-'+response.dbid).fadeOut('slow');
                }
            }
        });
    }

    $moveAnimationTop = function(type, El) {
            var dbeeli = El.closest('li');
            if (type == 1) {

                var targetEl = $('#ProfileLink');
                $('#header').css({
                    position: 'relative'
                });
                var targetPostion = targetEl.offset();
                var targetPostionLeft = targetPostion.left;
                var targetPostionTop = targetPostion.top;
                var parentEl = El.closest('.listingTypeMedia');
                $('#header').css({
                    position: 'fixed'
                });
                var parentElPositionLeft = parentEl.offset().left - $(window).scrollLeft();
                var parentElPositionTop = parentEl.offset().top - $(window).scrollTop();
                $('#fakeMovedb').remove();
                $('body').append('<div id="fakeMovedb" style="overflow:hidden"><div id="leftListingFake" style="min-height:inherit; margin-left:0px; padding-left:0px;"><ul  class="postListing"> <ul></div></div>');
                parentEl.clone().appendTo('#fakeMovedb #leftListingFake ul:first');
                $('#fakeMovedb').css({
                        width: parentEl.width(),
                        height: parentEl.height(),
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
                    }, 1000, function (){
                         $('#fakeMovedb').remove();
                    });

                El.html('moved to dashboard').addClass('disabled');
                $updateHidden(type, El);
            } else if (type == 2) {
                $dbConfirm({
                    content: 'Are you sure you want to remove this post from your live stream? You won\'t be able to undo this. ',
                    yes: true,
                    yesLabel: 'Yes',
                    no: true,
                    noLabel: 'No thanks',
                    yesClick: function() {
                        $updateHidden(type, El);
                    }
                });
            }
        } // end function $moveAnimationTop


    $('body').on('click', '.movetodashboard:not(.disabled)', function() {
        
        var dbid = $(this).attr('data-id');
        var type = $(this).attr('type');
        var dbusrname = $(this).attr('dbusrname');
        var dbusr = $(this).attr('dbusr');
        var cat = $(this).attr('cat');
        var El = $(this);
        var dbeeli = El.closest('li');

        data2 = 'dbusr=' + dbusr; 
        if (type == 1) {
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: data2,
                url: BASE_URL + '/myhome/chkfollowing',
                success: function(response) {
                   // console.log(response);
                    if (response.success == '1') {
                        $moveAnimationTop(type, El);

                    } else {
                        $dbConfirm({
                            content: 'You need to follow  <span class="orange">' + dbusrname + '</span>, in order to move to your dashboard ',
                            yes: true,
                            yesLabel: 'Follow',
                            no: true,
                            noLabel: 'No thanks',
                            yesClick: function() {
                                    $.ajax({
                                        type: "POST",
                                        dataType: 'json',
                                        data: data2,
                                        url: BASE_URL + '/myhome/insertfollow',
                                        success: function(response) {
                                                if (response.success == 'success') {
                                                    $dbConfirm({
                                                        content: 'Post categorised within <span class="orange">' + cat + '</span>. Would you like to see more these type of posts on your dashboard?',
                                                        yes: true,
                                                        yesLabel: 'Yes',
                                                        no: true,
                                                        noLabel: 'No thanks',
                                                        yesClick: function() {
                                                            $moveAnimationTop(type, El);
                                                        },
                                                        noClick: function() {
                                                            $moveAnimationTop(type, El);
                                                        }
                                                    });

                                                } else {
                                                    dbeeli.parents('.listingTypeMedia').fadeOut('slow', function() {
                                                        dbeeli.parents('.listingTypeMedia').after('<li id="msg' + response.dbid + '" class="listingTypeMedia"><div class="dontlikemsg">This post is hide from live stream <a href="#" class="iconstyle"> <i class="fa fa-undo"></i> </a></div><div style="float:right" class="deldbmsg"><a href="#" class="iconstyle"><i class="fa fa-times"></i></i> </a></div></div>');
                                                    });
                                                }
                                            } //success end here    

                                    }); // ajax end here

                                } // yes click end here
                        }); // confirmbox end here                      

                    } //else end here type 1

                }
            });
        } // end type 1 if condition
        else if (type == 2) {
            $moveAnimationTop(type, El);

        }
    });


    $('body').on('click', '.dontlikemsg a', function(e) {
        e.preventDefault();
        var msobjid = $(this).parents('.listingTypeMedia').attr('id');
        $('#' + msobjid).remove();
        var dbeeId = msobjid.split('msg');
        $('#dbee-id-' + dbeeId['1']).show('fast', function() {

            data = 'dbeeID=' + dbeeId['1'] + '&' + userdetails
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: data,
                url: BASE_URL + '/myhome/removefromdashboard',
                success: function(response) {
                    //alert('success');
                }
            });
        });

    });

    


    $('body').on('click', '.deldbmsg', function(e) {
        e.preventDefault();
        $(this).parents('li').remove();
    });
    $('body').on('click', '.connectToTwitter', function(e) {
       
        id = $(this).attr('data-id');
        var fromplace = $(this).attr('data-fromplace');
        var reportTemplate = '<h2>Connect with</h2><div  id="content_data_button" class="SocialfeedConnect" style="padding:0 0 20px; overflow:hidden;"><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>';
        $.dbeePopup(reportTemplate, {
            width: 400,
            overlayClick: false,
            otherBtn: ''
        });
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {
                'id': id,
                'fromplace': fromplace
            },
            url: BASE_URL + '/settings/socialconnect2',
            async: false,
            success: function(response) {
                $("#content_data_button").html(response.content);
                $.dbeePopup('resize');

            }
        });
    });


    $checkError = function (thisEl){
         if(thisEl.hasClass('required')==true){
           var errorCheck = false;
   
          thisEl.each(function (){
                var thisEl = $(this);
                var type = thisEl.prop('type');
                var val = $.trim(thisEl.val());
                var  rowFrom2 =  thisEl.parents().hasClass('centerLoginBar');
                var  rowFrom =  thisEl.parents().hasClass('formRow');
                 var  pr =  thisEl.closest('.formRow');
                if(rowFrom2==true){
                    rowFrom = false;
                }
               
                
                    var isEmail = true;
                    if(type=="email"){
                         var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                         isEmail = filter.test(val);
                    }
                    if(val=='' || isEmail == false){
                        if(rowFrom==true){
                             pr.addClass('errormsg');
                             $('input, textarea', pr).focus();
                        }else{
                             thisEl.focus().addClass('errormsg');
                        }
                        errorCheck = true;
                        return false;
                    }else{
                         if(rowFrom==true){
                             pr.removeClass('errormsg');
                        }else{
                            thisEl.removeClass('errormsg');
                        }
                        errorCheck = false;
                    }
                


        });
            if(errorCheck==true){
               return false;
            }
        }
     
    }


    $('body').on('keyup','.required', function(){
        var thisEl  = $(this);
         $checkError(thisEl);
    });


     $('body').on('change','.required', function(){
        var thisEl  = $(this);
         $checkError(thisEl);
    });     

     


    // This is for open categories list
    $('body').on('click', '.signinBtnGroup #signup', function(e) {
        e.preventDefault();
        var firstname = $.trim($('#firstname').val());
        var lastname = $.trim($('#lastname').val());
        var email = $('#email').val();
        var passworddbee = $('#passworddbee').val();
        var gender = $("#gender option:selected").val();
        var birthdayday = $("#birthdayday option:selected").val();
        var birthmonth = $("#birthmonth option:selected").val();
        var birthdayyear = $("#birthdayyear option:selected").val();
       
        var Ethis = $(this);
        var elm = $('#creatAccountBlock .required');
        $checkError(elm);
       if($checkError(elm)==false){
        return false;
       }

       if ($("#accept_terms_condition").is(':checked'))
           var accept_terms_condition = 1;
       else
            var accept_terms_condition = 0;
        if(accept_terms_condition=='0'){            
             $dbConfirm({
                 content: "Please accept the terms and conditions",
                 yes: false
             });
        }
      
       if ($("#cookiescheck").is(':checked'))
           var cookiescheck = 1;
       else
        var cookiescheck = 0;
       if(cookiescheck=='0' && false){           
         $dbConfirm({
                content: "Please accept the use of cookies",
                yes: false                
            });
        return false;
       }
            createrandontokenLogin(); // creting user session and token for request pass 
          
            $(this).css('cursor', 'inherit').attr('id', '');
            data = 'firstname=' + firstname + '&lastname=' + lastname + '&email=' + email + '&gender=' + gender + '&passworddbee=' + passworddbee + '&birthdayday=' + birthdayday + '&birthmonth=' + birthmonth + '&birthdayyear=' + birthdayyear + '&accept_terms_condition=' + accept_terms_condition + '&cookiescheck=' + cookiescheck + '&' + userdetailsLogon;
            $(this).closest('.signinBtnGroup button').append('<i class="fa fa-spinner fa-spin"> </i>');

            $.ajax({
                type: "POST",
                dataType: "json",
                url: BASE_URL + "/index/signup",
                data: data,
                success: function(response) {
                    if (response.error_message == '') {
                        $(function() {                           
                            var content = '<div class="signtbar"><span class="spirteVerified emailVerifiedIcon"> </span> Your sign up is complete.</div>\
                                    <div class="signCmntCnt">\
                                        <p style="margin-bottom:10px;">An email has been sent to you containing an account activation link.</p>\
                                        <p style="margin-bottom:10px;">Please check your spam/junk folder if this has not been received in the next 10 minutes.</p>\
                                        <p style="margin-bottom:10px;">Your account username is <span class="cntusrname">@' + response.username + '</span></p>\
                                        <p style="margin-bottom:10px;">We look forward to seeing you on the platform.</p>\
                                    </div>';
                            var PopupTemplate = '<div id="content_data" >' + content + '</div>';
                            $.dbeePopup(PopupTemplate);
                            $('#creatAccountBlock').hide();
                            $('body').removeClass('activeCreateAccount');
                            $('#signInBlock').show();
                            $('#creatAccountBlock .signinBtnGroup').html('<button style="width:100%" class="btn btn-orange btn-large pull-left" id="signup" name="submit" type="submit">Create Account</button>');
                            $('#creatAccountBlock input, #creatAccountBlock select').val('');
                            $('#creatAccountBlock input').attr('checked', false);
                            $('#creatAccountBlock .strength_meter .passworddbee').text('');
                        });
                    } else {                        
                        $dbConfirm({
                            content: response.error_message,
                            yes: false,
                            error: true
                        });
                        $('.signinBtnGroup .fa-spin').remove();
                        $('.signinBtnGroup button').css('cursor', 'pointer').attr('id', 'signup');

                    }

                }

            });
       

        setTimeout(function() {
            $.dbeePopup('resize');
        }, 200)

    });
    $('body').on('click', '.signinBtnGroup #requestsignup', function(e) {
        e.preventDefault();
        var firstname = $.trim($('#firstname').val());
        var lastname = $.trim($('#lastname').val());
        var email = $('#email').val();
        var carrerid = $('#carrerid').val();
       
        var Ethis = $(this);
        var elm = $('#requestAccountBlock .required');
        $checkError(elm);
        if($checkError(elm)==false){
          return false;
        }
      
        createrandontokenLogin(); // creting user session and token for request pass 
      
        $(this).css('cursor', 'inherit').attr('id', '');
        
        data = 'firstname=' + firstname + '&lastname=' + lastname + '&email=' + email +  '&carrerid=' + carrerid + '&caller= requesttojoin'+ userdetailsLogon;
        
        $(this).closest('.signinBtnGroup button').append('<i class="fa fa-spinner fa-spin"> </i>');

        $.ajax({
            type: "POST",
            dataType: "json",
            url: BASE_URL + "/index/requestsignup",
            data: data,
            success: function(response) {
                if (response.error_message == '') {
                    $(function() {                           
                        var content = '<div class="signtbar"><span class="spirteVerified emailVerifiedIcon"> </span> Request sent</div>\
                                <div class="signCmntCnt">\
                                    <p style="margin-bottom:10px;">An email has been sent to the platform admin. Your details will be reviewed and you will be sent an activation link soon.</p>\
                                    <p style="margin-bottom:10px;">We look forward to seeing you on the platform.</p>\
                                </div>';
                        var PopupTemplate = '<div id="content_data" >' + content + '</div>';
                        $.dbeePopup(PopupTemplate);
                        
                        $('body').removeClass('activeCreateAccount');
                        $('#signInBlock').show();
                        $('#requestAccountBlock input').val('');
                        $('.signinBtnGroup .fa-spin').remove();
                        $('.requestsignupProcess').css('cursor', 'pointer').attr('id', 'requestsignup');
                        $('#requestAccountBlock').hide();
                    });
                } else {                        
                    $dbConfirm({
                        content: response.error_message,
                        yes: false,
                        error: true
                    });
                    $('.signinBtnGroup .fa-spin').remove();
                    $('.requestsignupProcess').css('cursor', 'pointer').attr('id', 'requestsignup');

                }

            }

        });
       

        setTimeout(function() {
            $.dbeePopup('resize');
        }, 200)

    });

    $.showvippopup = function(userid, name) {

        var UpdatepassTemplate = '<h2 class="titlePop">Update password and personal details</h2>\
                <div id="create-league-wrapper" class="maindb-wrapper-border">\
                <form name="create" id="create"><div class="postTypeContent" id="passform">\
            <div class="formRow">\
            <div class="formField">\
                <input id="vippassword" class="strength textfield" type="password"  value="" name="password" data-password="vippassword"><i class="optionalText">Password</i>\
                <input class="strength" type="text" value="" name="" data-password="vippassword" style="display:none">\
            <i class="optionalText"> </i></div></div>\
                <div class="formRow"><div class="formField">\
                <input type="password" id="cpassword" class="textfield" value=""><i class="optionalText">Confirm password</i></div></div>\
                <div class="formRow">\
              <div class="formField">\
                 <i class="optionalText">please select your gender</i>\
                 <select  id="gender" name="gender" class="required gender">\
                    <option value="">Select gender</option>\
                    <option value="Male">Male</option>\
                    <option value="Female">Female</option>\
                    <option value="No Response">Prefer not to say</option>\
                </select>\
              </div>\
            </div>\
            <div class="formRow" id="useridrow" style="display:none;">\
            <div class="formField"><input type="text" class="textfield" id="userid" value="' + userid + '"><i class="optionalText">specify</i></div>\
            </div></form></div></div>';
        $.dbeePopup(UpdatepassTemplate, {
            closeBtnHide: true,
            overlayClick: false,
            escape: false,
            otherBtn: '<div class="processUpdateprofile" style="display:block">\
                                                        <a href="javascript:void(0)" class="btn btn-yellow pull-right upassword">Update details</a>\
                                                    </div>\
                                            '
        });
        $('.processUpdateprofile').show();
        setTimeout(function() {
            $.dbeePopup('resize');
        }, 100);

        $('#vippassword').strength({
            strengthClass: 'strength',
            strengthMeterClass: 'strength_meter_vip',
            strengthButtonClass: 'button_strength',
            strengthButtonText: 'Show Password',
            strengthButtonTextToggle: 'Hide Password'
        });

        /*
        $("#birthdate").datetimepicker({
            dateFormat: 'yy-mm-dd',
            showTimepicker: false,
            defaultValue: '1978-07-25',
            showButtonPanel: false,
            yearRange: "-100:+0",
            changeYear: true
        });
        */
        $('.upassword').click(function(event) {
            event.preventDefault();
            var password = $.trim($('#vippassword').val());
            var cpassword = $('#cpassword').val();
            //var birthdate = $('#birthdate').val();

            var userid = $('#userid').val();
            var gender = $("#gender").val();
            //var name = $('#username').val();
            if (password == '' || cpassword == '') {   
                $dbConfirm({
                    content: "Please enter a password",
                    yes: false,
                    error: true
                });
                return false;
            }
            if (password != cpassword) {
                $dbConfirm({
                    content: "Confirm password does not match!",
                    yes: false,
                    error: true
                });
                return false;
            }
            if (gender == '') {
                $dbConfirm({
                    content: "Please select a gender",
                    yes: false,
                    error: true
                });
                return false;
            }
            $.ajax({
                type: "POST",
                dataType: "json",
                url: BASE_URL + "/myhome/updateuserpopup",
                data: {
                    "password": password,
                    //"birthdate": birthdate,
                    'userid': userid,
                    'gender': gender
                },
                success: function(response) {
                  
                    if (response.content == 0) {}                   
                    else
                    $.dbeePopup('close');
                   
                    setTimeout(function() {
                      if(TAKEATOUR!=1)   $takeTour();
                       
                    }, 500);

                }

            });

        });
    }

    // play youtube video event start from here.
    $('body').on('click', '.youVideoPost', function(e) {
        e.preventDefault();
        var videoID = $('img', this).attr('video-id');
        if (videoID != undefined) {
            var playVideoHTml = '<iframe src="//fast.wistia.net/embed/iframe/'+videoID+'?videoFoam=true&autoPlay=true" allowtransparency="true&playerColor=CCCCCC" frameborder="0" scrolling="no" class="wistia_embed" name="wistia_embed" width="100%" height="360"></iframe>';
            $(this).html(playVideoHTml);
        }
    });

    $('body').on('click', '.youTubeVideoPost:not(.picMediaComboWrp .youTubeVideoPost, .dbcomment-speechwrapper .youTubeVideoPost, .specialDbNotPlay .youTubeVideoPost)', function(e) {
        e.preventDefault();
        var videoID = $('img', this).attr('video-id');
        if (videoID != 'undefined' || videoID != '') {
            var playVideoHTml = '<iframe id="ytplayer" width="100%" allowtransparency="true" style="background: #000;" height="360" src="//www.youtube.com/embed/' + videoID + '?rel=0&autoplay=1&origin=https://development.db-csp.com&version=3" frameborder="0" allowfullscreen=""></iframe>';
            $(this).html(playVideoHTml);
        }
    });
    // play youtube video event stop.

    $('body').on('click', '[popup=true]', function(e) {
        e.preventDefault();
        var thisEl = $(this);
      /*  var nextImg = thisEl.next().attr('popup-image');
        var prevImg = thisEl.prev().attr('popup-image');
        console.log(nextImg);
        console.log(prevImg);*/
        
        var videoType = $.trim($('img', this).attr('video-id'));
        var playVideoHtml = '<iframe id="ytplayer" width="100%" allowtransparency="true" style="background: #000;" height="360" src="//www.youtube.com/embed/' + videoType + '?rel=0&autoplay=1&origin=https://development.db-csp.com&version=3" frameborder="0" allowfullscreen=""></iframe>';
        if (videoType == '') {
            var imgName = $(this).attr('popup-image');
            var playVideoHtml = '<div class="videImgWrpPopup imagePopupView"  data-imagename="'+imgName+'" ></div>';
        }
        var allImages = [];
        thisEl.closest('.pixPostWrp').find('[popup=true]').each(function (){
            allImages.push($(this).attr('popup-image'))
        });

      nivgationBtn ='<a href="#" class="prevSlideImg" style="margin-right:10px;"><i class="fa fa-arrow-circle-left fa-2x"></i></a>';
      nivgationBtn +='<a href="#" class="nextSlideImg"><i class="fa fa-arrow-circle-right fa-2x"></i></a>';

        var nivgationBtnWrp='<div class="mimageNav">'+nivgationBtn+'</div>';
     
        function checkNavi (){
          if(allImages.indexOf(currentImage)+1==totalMultiImage){
              $('.nextSlideImg').hide();
           }else{
              $('.nextSlideImg').show();
           }
            if(allImages.indexOf(currentImage)==0){
              $('.prevSlideImg').hide();
           }else{
              $('.prevSlideImg').show();
           }
           // image loader
            var imgUrl = IMGPATH+'/imageposts/'+$('.imagePopupView').attr('data-imagename');
            var newImg = new Image();
            newImg.src = imgUrl;
            $dbLoader('.imagePopupView');
              $(newImg).load(function() {
                var bgSize='contain';
                console.log($('.imagePopupView').height())
                console.log(this.height)
                 if($('.imagePopupView').height() > this.height ){
                   bgSize = 'inherit';
                 }
                 if($('.imagePopupView').width() < this.width ){
                   bgSize = 'contain';
                 }
                $dbLoader('.imagePopupView', 1, 'close');
                 $('.imagePopupView').css({background:'url('+imgUrl+')', backgroundSize:bgSize, backgroundPosition:'center', backgroundRepeat:'no-repeat'});

              });

        }
        var totalMultiImage = allImages.length;
        if(totalMultiImage==0) {
           nivgationBtnWrp = '';
        }
        $.dbeePopup(playVideoHtml,{otherBtn:nivgationBtnWrp+'<a class="closePostPop fa fa-times" href="#"></a>',closeBtnHide:true, popClass:'imagePopViewWrap', bar:false, scrollbar:false});
        var currentImage =   $('.imagePopupView').attr('data-imagename');
        $('.nextSlideImg, .prevSlideImg').click(function(e){
          e.preventDefault();
           var cntImg  =  $('.imagePopupView').attr('data-imagename'); 
              if($(this).hasClass('prevSlideImg')==true){
                 var nextImgName  = allImages[allImages.indexOf(cntImg)-1];
               }else{
                 var nextImgName  = allImages[allImages.indexOf(cntImg)+1];
               }    
         // $('.imagePopupView').css({background:'url('+IMGPATH+'/imageposts/'+nextImgName+')', backgroundSize:'contain', backgroundPosition:'center', backgroundRepeat:'no-repeat'});
          $('.imagePopupView').attr('data-imagename', nextImgName);
           currentImage =   $('.imagePopupView').attr('data-imagename');
           checkNavi();

        });
        
        checkNavi();
     
        

    });


    
    $('body').on('click', '.cmntScoreState span:not(.disbledClick, .whatAreThese)', function() {
        var data = $(this).attr('data');
        data = data.split(',');
        scoredbee(data[0], data[1], data[2], data[3], data[4], data[5], 'mainpost');
    });

	$('body').on('click', '.seeMoreFulltextgrp', function(e) {

        e.preventDefault();
        e.stopPropagation();
       
        var E1 = $(this);
        var chkn = E1.attr('isshow');
        
        var lessHtml = " <a href='javascript:void(0);' class='seeMoreFulltextgrp lessClass' isshow='0'><i class='fa fa-plus-circle'></i>  more</a>";

         var snc = E1.closest('.GroupDesc');

        var fulldata = snc.attr('fulldata');

        var lessdata = fulldata.substring(0,250);        
        
        if(chkn==1){
             snc.html(lessdata+lessHtml);
        }else{
            
            lessHtml2 = ' <a class="seeMoreFulltextgrp" isshow="1" href="javascript:void(0);"><i class="fa fa-minus-circle"></i>  less</a>';
            snc.html(fulldata+lessHtml2);
			
        }
        

     });
	 
    $('body').on('click', '.seeMoreFulltext', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var thisElement = $(this);
        var lessHtml = '';
         var fullcnt = '';
        var mainCnt = thisElement.closest('[fulldata]');
        if ($(this).hasClass('lessClass') == false)
            lessHtml = " <a href='#' class='seeMoreFulltext lessClass'><i class='fa fa-minus-circle'></i> less</a>";

        var fullData = $('.fulldataContainers', mainCnt).html();
        var fullcnt = $('.fulldataContainers', mainCnt).clone();
        $('.fulldataContainers', mainCnt).remove();
        var lessData = mainCnt.html();
        
        mainCnt.html(fullData + lessHtml);
        mainCnt.prepend(fullcnt);
        $('.fulldataContainers', mainCnt).html(lessData);
        $('.lessClass:not(:first)', mainCnt).remove();

    });
     $('body').on('click', '.seeMoreFulltextgrp', function(e) {
        e.preventDefault();
        e.stopPropagation();
       
        var E1 = $(this);
        var lessHtml = " <a href='javascript:void(0);' class='seeMoreFulltextgrp lessClass' isshow='0'><i class='fa fa-plus-circle'></i>  more</a>";

         var snc = E1.closest('.GroupDesc');

        var fulldata = snc.attr('fulldata');

        var lessdata = fulldata.substring(0,300);
       
       var chkn = E1.attr('isshow');
        
        if(chkn==1){
             snc.html(lessdata+lessHtml);       
        }else{
            
            lessHtml2 = ' <a class="seeMoreFulltextgrp" isshow="1" href="javascript:void(0);"><i class="fa fa-minus-circle"></i>  less</a>';
            snc.html(fulldata+lessHtml2);
            
        }
        

     });
     
     $('body').on('click', '.seeMoreFulltextdb', function(e) {
        e.preventDefault();
        e.stopPropagation();
       
        var E1 = $(this);
        var lessHtml = " <a href='javascript:void(0);' class='seeMoreFulltextdb lessClass' isshow='0'><i class='fa fa-plus-circle'></i>  more</a>";

         var snc = E1.closest('.dbsmore');

        var fulldata = snc.attr('fulldata');

        var lessdata = fulldata.substring(0,300);
       
       var chkn = E1.attr('isshow');
        
        if(chkn==1){
             snc.html(lessdata+lessHtml);       
        }else{
            
            lessHtml2 = ' <a class="seeMoreFulltextdb" isshow="1" href="javascript:void(0);"><i class="fa fa-minus-circle"></i>  less</a>';
            snc.html(fulldata+lessHtml2);
            
        }
        

     });

    $('body').on('click', '#SurveysRightBtn', function() {
        if($('#leftListing').length==0){
          location = BASE_URL+'/myhome#SurveysRightBtnOn';
         return false;
        }
        $('.formcat').hide();
        $('#dashboarduserDetails').remove();
        $('.searchByThis').remove();
        $('#customFieldsDown').hide(); // to hide customised dashborad filters  
        $('.customizeDeshboard').hide();
        $('.biogrophydisplay').fadeOut('fast');
        if ($('#dbee-feeds').is(':visible') == false) {
            $('#dashboarduserDetails').fadeOut('fast');
            $('#postMenu').fadeOut('fast');
            $('#leftListing').removeClass('groupViewListing');
            $('#leftListing .profileStatsWrp').fadeOut('fast');
            $('#rightListing .proinfo').fadeOut('fast');
            $('#leftListing .group-highlighted').fadeOut('fast');
            $('#rightListing .contactInfo').fadeOut('fast');
        }
        seeglobaldisplayby("nouserid", 1, "servey", "myhome", 'filtertype', "filtertype");
        if ($(document).scrollTop() != 0) {
            $('html, body').animate({
                scrollTop: 0
            }, 'fast');
        }
    });
    $('body').on('click', '#mysurvey', function() {
         if($('#leftListing').length==0){
              location = BASE_URL+'/myhome#SurveysRightBtnOn';
             return false;
         }
        $('.formcat').hide();
        $('#dashboarduserDetails').remove();
        $('.searchByThis').remove();
        $('#customFieldsDown').hide(); // to hide customised dashborad filters  
        $('.customizeDeshboard').hide();
        $('.biogrophydisplay').fadeOut('fast');
        if ($('#dbee-feeds').is(':visible') == false) {
            $('#dashboarduserDetails').fadeOut('fast');
            $('#postMenu').fadeOut('fast');
            $('#leftListing .profileStatsWrp').fadeOut('fast');
            $('#rightListing .proinfo').fadeOut('fast');
            $('#leftListing .group-highlighted').fadeOut('fast');
            $('#rightListing .contactInfo').fadeOut('fast');
        }
        seeglobaldisplayby("nouserid", 1, "servey", "myhome", 'mysurveyfiltertype', "filtertype");
        if ($(document).scrollTop() != 0) {
            $('html, body').animate({
                scrollTop: 0
            }, 'fast');
        }
    });


    $('body').on('click', '#event_section', function() {
         if($('#leftListing').length==0){
              location = BASE_URL+'/myhome#event_sectionOn';
             return false;
         }
         var datafrom=$(this).attr('data-from');

         //alert(datafrom);
        $('#dashboarduserDetails').remove();

        if(datafrom=='main'){
         $('#leftListing').html('<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>');
        }else{       
         $('.postListing').html('<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>');
        }
        $('.searchByThis').remove();

        $('.customizeDeshboard').hide();
        $('.biogrophydisplay').fadeOut('fast');
        $('#customFieldsDown').hide(); // to hide customised dashborad filters   
        if ($('#dbee-feeds').is(':visible') == false) {
            $('#dashboarduserDetails').fadeOut('fast');
            $('#postMenu').fadeOut('fast');
            $('#leftListing .profileStatsWrp').fadeOut('fast');
            $('#rightListing .proinfo').fadeOut('fast');
            $('#leftListing .group-highlighted').fadeOut('fast');
            $('#rightListing .contactInfo').fadeOut('fast');
        }
       

            $.ajax({
                type: "POST",
                data: {
                    type: "eventlist"
                },
                url: BASE_URL + '/myhome/eventlist',
                beforeSend: function() {},
                success: function(response) {
                    $('#leftListing').removeClass('groupViewListing');
                    $('#leftListing').html(response);
                }
            });
       

        if ($(document).scrollTop() != 0) {
            $('html, body').animate({
                scrollTop: 0
            }, 'fast');
        }
    });

    $('body').on('click', '#my_event_section', function() {
        if($('#leftListing').length==0){
              location = BASE_URL+'/myhome/#my_event_sectionOn';
              return false;
         }
        $('.tabLinks').remove();
        $('#dashboarduserDetails').remove();
        $('.postListing').html('<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>');
        $('.searchByThis').remove();

        $('.customizeDeshboard').hide();
        $('.biogrophydisplay').fadeOut('fast');
        $('#customFieldsDown').hide(); // to hide customised dashborad filters   
        if ($('#dbee-feeds').is(':visible') == false) {
            $('#dashboarduserDetails').fadeOut('fast');
            $('#postMenu').fadeOut('fast');
            $('#leftListing .profileStatsWrp').fadeOut('fast');
            $('#rightListing .proinfo').fadeOut('fast');
            $('#leftListing .group-highlighted').fadeOut('fast');
            $('#rightListing .contactInfo').fadeOut('fast');
        }


        $.ajax({
            type: "POST",
            data: {
                type: "eventlist"
            },
            url: BASE_URL + '/myhome/myeventlist',
            beforeSend: function() {},
            success: function(response) {
                 
                $('#leftListing').removeClass('groupViewListing');
                $('#leftListing').html(response);
            }
        });

        if ($(document).scrollTop() != 0) {
            $('html, body').animate({
                scrollTop: 0
            }, 'fast');
        }
    });


    $('body').on('click', '.removeAttendee', function() {
        var thisEl = $(this);
        var dataeventid = thisEl.attr('data-eventidxx');
        $dbConfirm({
            content: 'Are you sure you want to remove yourself from this event?',

            yesClick: function() {
                $.ajax({
                    type: "POST",
                    data: {
                        removeattendee: dataeventid
                    },
                    url: BASE_URL + '/myhome/myeventlist',
                    beforeSend: function() {
                        thisEl.append(' <i class="fa fa-spin fa-spinner"></i>');
                    },
                    success: function(response) {
                        thisEl.closest('li.eventListing').slideUp(function() {
                            $(this).remove();
                        });

                        if ($('li.eventListing').length == 1) {
                            $('#leftListing').html('<ul id="dbee-feeds" class="postListing"><div style="cursor:pointer; color:#333333; text-align:center;"><div class="userVisibilityHide">\
                                    <span class="fa-stack fa-4x">\
                                      <i class="fa fa-exclamation-triangle"></i></span><br>\
                                    No Live Events found.</div></div></ul>');
                            $('body,html').animate({
                                scrollTop: 0
                            });
                        }
                       
                    }
                });
            }
        });
    });

    $('body').on('click', '.removeVideoAttendee', function() {
        var thisEl = $(this);
        var dataeventid = thisEl.attr('data-videoeventidxx');

        $dbConfirm({
            content: 'Are you sure you want to remove yourself from this Video Broadcast?',

            yesClick: function() {
                $.ajax({
                    type: "POST",
                    data: {
                        removeattendee: dataeventid,
                        type: '6'
                    },
                    url: BASE_URL + '/myhome/myfiltertype',
                    beforeSend: function() {
                        thisEl.append(' <i class="fa fa-spin fa-spinner"></i>');
                    },
                    success: function(response) {
                        thisEl.closest('li.listingTypeMedia').slideUp(function() {
                            $(this).remove();
                        });

                        if ($('li.listingTypeMedia').length == 1) {
                            $('#leftListing').html('<ul id="dbee-feeds" class="postListing"><div style="cursor:pointer; color:#333333; text-align:center;"><div class="userVisibilityHide" style="background-color:#FFFFFF;">\
                                    <span class="fa-stack fa-4x">\
                                      <i class="fa fa-exclamation-triangle"></i></span><br>\
                                    No Video Events found.</div></div></ul>');
                            $('body,html').animate({
                                scrollTop: 0
                            });
                        }
                       
                    }
                });
            }
        });
    });

$('body').on('click', '.removeVideoAttendes', function() {
        var thisEl = $(this);
        var dataeventid = thisEl.attr('data-videoeventidxx');

        $dbConfirm({
            content: 'Are you sure you want to remove yourself from this Live Video Broadcast?',

            yesClick: function() {
                $.ajax({
                    type: "POST",
                    data: {
                        removeattendee: dataeventid,
                        type: '6'
                    },
                    url: BASE_URL + '/myhome/myfiltertype',
                    beforeSend: function() {
                        thisEl.append(' <i class="fa fa-spin fa-spinner"></i>');
                    },
                    success: function(response) {
                        thisEl.remove();
                      

                        if ($('li.listingTypeMedia').length == 1) {
                            $('#leftListing').html('<ul id="dbee-feeds" class="postListing"><div style="cursor:pointer; color:#333333; text-align:center;" class="noFound"><div class="userVisibilityHide" style="background-color:#FFFFFF;">\
                                    <span class="fa-stack fa-4x">\
                                      <i class="fa fa-battery-empty"></i>\
                                      <i class="fa fa-ban fa-stack-2x"></i>\
                                    </span><br>\
                                    No Live Video Broadcasts found.</div></div></ul>');
                            $('body,html').animate({
                                scrollTop: 0
                            });
                        }
                       
                    }
                });
            }
        });
    });
    
    $('body').on('click', '#liveBroadcast', function() {
         if($('#leftListing').length==0){
              location = BASE_URL+'/myhome/#liveBroadcastOn';
              return false;
         }
        var body = $("body, html");
        $('#customFieldsDown').hide(); // to hide customised dashborad filters   
        $('.searchByThis').remove();
        if ($('#dbee-feeds').is(':visible') == false) {
            $('#dashboarduserDetails').fadeOut('fast');
            $('#postMenu').fadeOut('fast');
            $('#leftListing .profileStatsWrp').fadeOut('fast');
            $('#rightListing .proinfo').fadeOut('fast');
            $('#leftListing .group-highlighted').fadeOut('fast');
            $('#rightListing .contactInfo').fadeOut('fast');
            $('.customizeDeshboard').hide();
            $('.biogrophydisplay').fadeOut('fast');
        }
        
        $('#spacialdb_section').removeClass('active');
        $(this).addClass('active');
        seeglobaldisplayby("nouserid", 1, "dbliveBroadcast", "myhome", 'filtertype', "liveBroadcast");
        if ($(document).scrollTop() != 0) {
            $('html, body').animate({
                scrollTop: 0
            }, 'fast');
        }

    });

    $('body').on('click', '#my_spacialdb_section', function() {
         if($('#leftListing').length==0){
              location = BASE_URL+'/myhome/#my_spacialdb_sectionOn';
              return false;
         }
        var body = $("body, html");
        var thisEl = $(this);
        var datavideotype = thisEl.attr('data-videotype');
      
        $('#customFieldsDown').hide(); // to hide customised dashborad filters   
        $('.searchByThis').remove();
        if ($('#dbee-feeds').is(':visible') == false) {
            $('#dashboarduserDetails').fadeOut('fast');
            $('#postMenu').fadeOut('fast');
            $('#leftListing .profileStatsWrp').fadeOut('fast');
            $('#rightListing .proinfo').fadeOut('fast');
            $('#leftListing .group-highlighted').fadeOut('fast');
            $('#rightListing .contactInfo').fadeOut('fast');
            $('.customizeDeshboard').hide();
            $('.biogrophydisplay').fadeOut('fast');
        }       
        seeglobaldisplayby("nouserid", 1, "dbspacial", "myhome", 'myfiltertype', "filtertype");
        if ($(document).scrollTop() != 0) {
            $('html, body').animate({
                scrollTop: 0
            }, 'fast');
        }

    }); 

$('body').on('click', '#my_liveBroadcast', function() {
         if($('#leftListing').length==0){
              location = BASE_URL+'/myhome/#my_liveBroadcastOn';
              return false;
         }
        var body = $("body, html");
        var thisEl = $(this);
        var datavideotype = thisEl.attr('data-videotype');
        $('#customFieldsDown').hide(); // to hide customised dashborad filters   
        $('.searchByThis').remove();
        if ($('#dbee-feeds').is(':visible') == false) {
            $('#dashboarduserDetails').fadeOut('fast');
            $('#postMenu').fadeOut('fast');
            $('#leftListing .profileStatsWrp').fadeOut('fast');
            $('#rightListing .proinfo').fadeOut('fast');
            $('#leftListing .group-highlighted').fadeOut('fast');
            $('#rightListing .contactInfo').fadeOut('fast');
            $('.customizeDeshboard').hide();
            $('.biogrophydisplay').fadeOut('fast');
        }       
        seeglobaldisplayby("nouserid", 1, "dbspacial", "myhome", 'mylivevideofilter', "filtertype"); 

        if ($(document).scrollTop() != 0) {
            $('html, body').animate({
                scrollTop: 0
            }, 'fast');
        }

    });




    var linkedinGroupLoad = false;
    $('body').on('click', '#linkedinGroup', function() {
        $('#dashboarduserDetails, .searchByThis').remove();
        $('#customFieldsDown').hide(); // to hide customised dashborad filters   
        if ($('#dbee-feeds').is(':visible') == false) {
            $('#dashboarduserDetails').fadeOut('fast');
            $('#postMenu').fadeOut('fast');
            $('#leftListing .profileStatsWrp').fadeOut('fast');
            $('#rightListing .proinfo').fadeOut('fast');
            $('#leftListing .group-highlighted').fadeOut('fast');
            $('#rightListing .contactInfo').fadeOut('fast');
        }
        linkedinGroupLoad = true;
        loadLingedinGroup(0);
    });

    var forGrouplisting = true;

    $(window).scroll(function() {
        if (forGrouplisting == true) {
            if (noGroupLinkedinAvailable == false) {
                if ($(window).scrollTop() == $(document).height() - $(window).height()) {
                    if (linkedinGroupLoad == true) {
                        page = $('#pagevalue').val();
                        loadLingedinGroup(page, true);
                    }
                }
            }
        }
    });



    $('body').on('click', '.dbAccordion li:nth-child(n+1)', function() {
        var thisEl = $(this);
        var goParent = thisEl.closest('.dbAccordion');


        if (thisEl.hasClass('active') == true) {
            thisEl.removeClass('active');
            $('ul:first', thisEl).slideUp('fast');
        } else {
            $('.active', goParent).removeClass('active');
            thisEl.addClass('active');
            $('ul', goParent).slideUp('fast');
            $('ul:first', thisEl).slideDown('fast');

        }

    });

    $('body').on('click', '.dontShowMeAgain', function() {
        var cnt = $(this).closest('.dashboarprofleWrp').find('.middleDescriptionBox');
        if (cnt.is(':visible') == true) $(this).text($(this).attr('data-close'));
        else $(this).text('OK, got it :-)');
        cnt.slideToggle();
    });

var sideBarOpenClose = function (param){
    if(param=='open'){
          if ($('html').hasClass('openMysideBar') == true) {
            $('html').removeClass('openMysideBar');
            return false;
        }
        // $('.headerMenu li ul').show();
        $('html').addClass('openMysideBar');

        $('#postMenu #postMenuLinks, .headerMenu').attr('style', '');
        setTimeout(function() {
            $('html').addClass('noScroll')
        }, 200);
    }else{
          if ($('html').hasClass('openMysideBar') != true) {
            $('html').removeClass('openMysideBar');

        } else {
            $('html').removeClass('openMysideBar');
        }
        setTimeout(function() {
            $('html').removeClass('noScroll')
        }, 200);
    }
}

    $('.mobileDevicesHeader li a.mSideBar').click(function() {
        if($('html').hasClass('openTopMenu')==true) sideBarOpenClose();
        else sideBarOpenClose('open');
    });

    $('body').on('click', '.closeNavations', function() {
      sideBarOpenClose();
    });

    $('#mySideMenu [data-id="myRighSideBtn"]').click(function(event) {
        var thisEl = $(this);
        var dataType = thisEl.attr('data-id');
        var leftWidth = $('#leftListing').width();

        if (dataType == 'mySideMenuBtn') {
            $('html').removeClass('openMysideBar openTopMenu');
            $('html').toggleClass('openMysideBar');
        } else {
            $(window).scrollTop(0);
            $('html').removeClass('openMysideBar openTopMenu');
            $('html').toggleClass('openMyRightSideBar');
            if ($('i', thisEl).hasClass('fa-arrow-right') == true) {
                $('i', thisEl).removeClass('fa-arrow-right').addClass('fa-arrow-left');
            } else {
                $('i', thisEl).removeClass('fa-arrow-left').addClass('fa-arrow-right');
            }
        }
        if ($('html').hasClass('openMysideBar') == true || $('html').hasClass('openMyRightSideBar') == true) {
            setTimeout(function() {
                $('html, body').addClass('noScrollX')
            }, 200);
        }

    });

    $('.userMenuSideBar a').click(function() {
        $('body,html').animate({
            scrollTop: 0
        });

    });

    $('body').on('click', '.whatAreThese', function() {
        var cnt = 'Let contributors know what you think of their posts by scoring them. A ‘thumbs up’ or ‘smiley face’ denotes that you have a positive or very positive opinion of a post or comment. Conversely, a ‘thumbs down’ or ‘sad face’ denotes a negative or very negative score.<br><br>In addition, clicking the ‘light bulb’ icon will add a post - whether deemed positive or negative - to your Influencers list on your dashboard.<br><br><img src="' + BASE_URL + '/img/influencegrab.png">';
        $.dbeePopup(cnt);
        //$dbConfirm({content:cnt});
    });

}); // end document ready


$.fn.dbThumbSlider = function(options) {
    var thumbWidth = 0;
    var fullWidthSilder = 0;
    var navBar = '';
    var plugin = this;

    var settings = $.extend({
        color: "#556b2f",
        customClass: "",
        visibleThumb: 6,
        navigation: true,
        nextArrowClass: '',
        preArrowClass: '',
        nextArrowText: '>',
        preArrowText: '<',
    }, options);

    var currentItem = settings.visibleThumb;
    var totalThumb = $('li', plugin).size();
    var pluginList = $('li', plugin);
    pluginList.each(function(index, el) {


        fullWidthSilder += $('li', plugin).width();
        if (index < settings.visibleThumb) {
            thumbWidth += $('li', plugin).width();
        }
    });
    plugin.css({
        width: fullWidthSilder
    });

    return this.each(function(index, el) {

        if (settings.navigation != false && settings.visibleThumb < totalThumb) {
            $(this).wrapAll('<div class="dbSlider ' + settings.customClass + '" style="width:' + (thumbWidth + 40) + 'px;"><div class="dbtumbWrapper" style="width:' + thumbWidth + 'px; margin:0px 20px; "></div></div>');
            navBar = '<div class="dbtsNavBar"><a href="javascript:void(0);" class="dbtsPrevArrow ' + settings.preArrowClass + '">' + settings.preArrowText + '</a><a href="javascript:void(0);" class="dbtsNextArrow ' + settings.nextArrowClass + '">' + settings.nextArrowText + '</a></div>';
            $('.dbSlider').append(navBar);
            $('.dbtumbWrapper', plugin).css('margin', '0px 20px');
            $('.dbtsNextArrow').click(function(event) {
                var thisSlider = $(this).parents('.dbSlider').find('ul');
                if (currentItem < totalThumb) {
                    currentItem++;
                    var currentW = $('li:eq(' + (currentItem - 1) + ')', plugin).width();
                    thisSlider.animate({
                        left: "-=" + currentW + "px",
                    }, 'fast');
                }
            });

            $('.dbtsPrevArrow').click(function(event) {
                var thisSlider = $(this).parents('.dbSlider').find('ul');
                if (settings.visibleThumb < currentItem) {
                    currentItem--;
                    var currentW = $('li:eq(' + (currentItem) + ')', plugin).width();
                    thisSlider.animate({
                        left: "+=" + currentW + "px",
                    }, 'fast');
                }
            });
        } else {
            $(this).wrapAll('<div class="dbSlider ' + settings.customClass + '" style="width:' + (thumbWidth) + 'px"><div class="dbtumbWrapper" style="width:' + thumbWidth + 'px; "></div></div>');
        }

    });


}

function filtersocailuser() {
    var sorting = $("input[socialFriendlist='true']").val();
    var id = $("input[socialFriendlist='true']").attr('id');
    var count = 0;
    $("." + id).each(function(index) {
        if ($(this).attr('title')) {
            if ($(this).attr('title').match(new RegExp(sorting, "i"))) {
                $(this).show();

                count++;
            } else {
                $(this).hide();
            }
        }
    });

     $.dbeePopup('resize');
    //$("#Usercountfilter").show().html(count+'<i>total <br>user</i>');
}


function filterUserType(thisEl, searchContainer) {
    var sorting = $(thisEl).val();
    var id = $(thisEl).attr('id');
    var count = 0;
    $(searchContainer + " li").each(function(index) {
        if ($(this).attr('title')) {
            if ($(this).attr('title').match(new RegExp(sorting, "i"))) {
                $(this).show();
                count++;
            } else {
                $(this).hide();
            }
        }
    });

    if (count == 0) {
        $(searchContainer + ' .noFound').remove();
        $(searchContainer).append('<li class="noFound">no result found!</li>');
    } else {
        $(searchContainer + ' .noFound').remove();
    }

}

// start ajax start function
var forceclose = true;
$(document).ajaxStart(function() 
{

    if (!$('#login').val()) {
        return false;
    }
    if ($('.ajaxsocialblock').is(':visible')) {
        return false;
    } else {
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: BASE_URL + '/dbeedetail/socialblock/',
            cache: false,
            success: function(response) {
                if (response.ForcedLogout == 'logout') {
                    $.dbeePopup('<span style="color:#fff;font-size:16px;padding-top:45px;display:inline-block">Oops, something went wrong with your current session. You will be logged out if we are unable to reconnect it.</span>',{closeBtnHide:true});
                    setTimeout(function(){
                        window.location.href= BASE_URL+"/index/index/sessionkey/"+response.message;
                    }, 5000);
                   // window.location.href = BASE_URL + "/index/index/sessionkey/" + response.message;
                } else if (response.ForceLogout == 'logout') {
                    if (forceclose == true) {
                        showForceLogout();
                        forceclose = false;
                        setTimeout("window.parent.location='/myhome/logout'", 5000);
                    }
                }
            }

        });
    }
});
// end ajax start function
var noGroupLinkedinAvailable = false;

function loadLingedinGroup(page, pageScroll, sideGroups) {
    $.ajax({
        type: "POST",
        data: {
            page: page
        },
        dataType: 'json',
        async: false,
        url: BASE_URL + '/social/linkedingroup',
        beforeSend: function() {
            if (pageScroll == true) {
                $('.loadingPage').remove();
                $('#leftListing, #sidelinkedingGroup .rboxContainer').append('<div class="loadingPage"></div>');
                $dbLoader('.loadingPage');
            } else {
                $('#leftListing').html('<ul class="postListing"><li> </li></ul>');
                $dbLoader('#leftListing .postListing li');
            }
            if (sideGroups == true) {
                $dbLoader('#sidelinkedingGroup .rboxContainer');
            }
        },
        success: function(response) {
            htmlData = '';
            dataJosn = $.parseJSON(response.getnetwork);

            var sideHtml = '';

            if (dataJosn._total != 0) {

                $.each(dataJosn.values, function(key, value) {
                    //console.log(value.largeLogoUrl);
                    htmlData += '<li class="linkedinLisiting" id="group-id-' + value.group.id + '">';
                    var groupLogo = '';
                    if (typeof value.group.largeLogoUrl == 'undefined') {
                        groupLogo = '<div class="linkedinGrpLogo fa dbLinkedInIcon fa-3x"></div>';
                    } else {
                        groupLogo = '<img src="' + value.group.largeLogoUrl + '" >';
                    }
                    htmlData += '';
                    if (typeof value.group.description != 'undefined') {
                        var description = '<div class="listTxtNew">\
                        ' + value.group.shortDescription + '\
                      </div>';
                    }

                    sideHtml += '<div class="rbcRow">\
                                    <a href="' + BASE_URL + '/social/linkedingroupdetails/groupid/' + value.group.id + '" >\
                                        ' + groupLogo + '\
                                        <div class="sidgrpLinkedin">\
                                            <div class="oneline">' + value.group.name + '</div>\
                                        </div>\
                                    </a>\
                             </div>';
                    htmlData += '<div class="postListContent">\
                        <div class="pstListTitle">\
                        <a class="psUserName" href="' + BASE_URL + '/social/linkedingroupdetails/groupid/' + value.group.id + '">\
                          ' + groupLogo + '\
                          <div class="psName">\
                           ' + value.group.name + '\
                          </div>\
                          </a>\
                      </div>' + description + '\
                      <div class="psListingFt">\
                      <a href="' + BASE_URL + '/social/linkedingroupdetails/groupid/' + value.group.id + '" class="btn btn-linkedin btn-mini pull-right">Go to LinkedIn Group</a>\
                        </div>\
                    </div>\
                  <div class="clearfix"></div>\
                    </li>';
                });
                if (pageScroll == true) {
                    $('.postListing').append(htmlData);
                    $dbLoader('.loadingPage', '', 'close');
                } else {
                    $dbLoader('#leftListing', '', 'close');
                    $('#leftListing').html('<input type="hidden" id="pagevalue" name="pagevalue" value="0" ><ul class="postListing">' + htmlData + '</ul>');

                }
                if (sideGroups == true) {
                    if (pageScroll == true) {
                        $('#sidelinkedingGroup .rboxContainer').append(sideHtml);
                        $dbLoader('#sidelinkedingGroup .rboxContainer', '', 'close');
                    } else {
                        $dbLoader('#leftListing', '', 'close');
                        $('#sidelinkedingGroup .rboxContainer').html('<input type="hidden" id="sidepagevalue" name="pagevalue" value="0" >' + sideHtml + '</ul>');
                        $dbLoader('#sidelinkedingGroup .rboxContainer', '', 'close');

                    }
                }

                $('#pagevalue').val(1 + parseInt(page));
                $('#sidepagevalue').val(1 + parseInt(page));

            } // if close
            else {
                $('#leftListing').html('<ul class="postListing"><li><div class="noFound">No LinkedIn Group found</div></li></ul>');
                noGroupLinkedinAvailable = true;
            }
        }
    });

}

 
            
        



/*Start menu collapse*/

$(document).ready(function() {
    if($('#login').val()!='')
    {
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {
                DbArray: 1
            },
            url: BASE_URL + "/ajax/usermentionpost",
            success: function(response) {
                var datares = response.html.userlist;
                 $('#mentionpostuser').val(datares);    
            }
        });
    }

   /* $('#mymenuItem:not(.hmRtmenu #mymenuItem)').css({
        display: 'none'
    });*/
    $('#itemList1').addClass('fa-angle-down');

    $('body').on('click', '#rightListing h2:not(.mymenuItem)', function() {
        var navmenucls = $(this).attr('class');        
        $('#' + navmenucls).slideToggle(100);
        $(this).closest('.whiteBox').toggleClass('active');
        if(!$(this).closest('.whiteBox').hasClass('active')){
            $('.catmain').hide();
        }else{
                chklen = $('.dbfeedfilterbycat li .checkInplist input:checked').length;
                if (chklen == 0) {
                    $('.catmain').hide();
                }else{$('.catmain').show(); }
        }
                               
    });
    
    $('body').on('click','.noCollapse.active', function(){
       $(this).addClass('active');
    });

     $('body').on('click', '#rightListing h2 .catRtbtn a', function(e) {
        e.stopPropagation();                           
    });
     
    /*$('body').on('click', '.navAllLink a', function() {                    
        $('#' + this.rel).slideDown(100);
    });*/




    $('input[type=radio][name="pollradio"]').click(function() {

        $('#postCommentBtn').fadeIn();
    });


    var loginht = $(window).height();
    $('#fullScreenBg li.loginsetbg').height(loginht);
    
    $(window).resize(function() {
        var loginht = $(window).height();
        $('#fullScreenBg li.loginsetbg').height(loginht);
       
    });




    $('body').on('click', '.profileDes .profileDownAro', function() {
        $('.userIdOrgTxt').toggleClass('hideId');
        $(this).toggleClass('fa-angle-down');
        $('#dashboarduserDetails').toggleClass('dashuserActive');
    });

    $('#dashboarduserDetails.dashuser').removeClass('dashuserActive');




    $('body').on('click','#sliderInfo1 .flex-direction-nav a', function(){
       $('#sliderInfo1 li').width('40');
    });

    $('.showPoststream').click(function(){

        var thisEl = $(this);
        var call = thisEl.attr('call');

        var Iamon = $('#whereIam').val();
        //socket.emit('showPoststream', Iamon, fetchintialfeeds()); 
        if(Iamon=='myhome')
        {
            if(call=='livepost')
            {                 
             $('.formcat').show();
                $('#filtercat').hide();
                $('#feedtype').val('all');
                $('.whiteBox,#rightListing').fadeIn();
                $('.profileStatsWrp,#rightListing').fadeIn();
                $('#sortable','#rightListing').fadeIn();
                fetchintialfeeds();
            }
            else
            {        

                $('.whiteBox,#rightListing').fadeIn();        
                $('.profileStatsWrp').fadeIn();
                $('#sortable').fadeIn();
                $('.formcat').hide();
                $('#filtercat').hide();
                seeglobaldbeelist(SESS_USER_ID,4,"my-dbees","myhome","mydbee","livemydb");
            }
        }
        else
        {

            if(call=='livepost')
            {
                window.location.href = BASE_URL;
            }
            else
            {
                window.location.href = BASE_URL+'/myhome/index/mypost/1';
            }
        }
        
    })

     
 



if(navigator.userAgent.match(/(iPhone)|(iPod)|(android)|(webOS)/i)){ 
   $('.footerLinksWrp').hide(); 
   $('.userScore').addClass('dropDown');   
   $('.userScore ul.scoreList').addClass('dropDownList');          
   $('.userScore .scoreTxt').css('display','block');   
   $('.userScore ul.scoreList .scoreSpantxt').css({display:'inline-block', float:'right'});
}


$('body').on('click','.rssdeactive',function(e){
    //if($(this).attr('checked')){
            var e1 = $(this);
             $dbConfirm({
                content: 'This feed has been disabled by the platform admin. If you deselect it, it will not be available to select again. Do you wish to continue? ',
                yes: true,
                yesLabel: 'Yes',
                no: true,
                noLabel: 'No',
                yesClick: function() {
                    e1.remove();
                    e1.after('span').remove();
                },
                 noClick: function() {  

                    $('.check',e1).attr('checked','checked');
                }
            });
        //  }
}); 

$('body').on('click','.check',function(){

        $("#rss-sites").val('');
        $(".check").each(function(){

            if($(this).attr('checked')){       

                if($("#rss-sites").val()!='')
                    $("#rss-sites").val($("#rss-sites").val()+','+$(this).val());
                else
                    $("#rss-sites").val($(this).val());
            } else {
                $("#label"+$(this).val()).html('');
            }

        });

        var val=$("#rss-sites").val();
        var splitVal=val.split(',');
        var total=splitVal.length;
        if(total>4) {
            $("#msg").html('You can select a maximum of 4 RSS feeds, please remove an existing feed before adding a new one.');
            $("#msg").fadeIn('slow');
            $(this).attr('checked', false);
            $("#rss-sites").val($("#rss-sites").val().replace($(this).val(), ""));

        } else {
            $("#msg").fadeOut('slow');
            updatelabel(val);
        }       
    });

  $('body').on('click','#dbexpertRightCont .crossIcon', function(){
     $(this).closest('.expertOverbox').removeClass('active');
     $('.overlayfade').fadeOut(100);
  });


  $('body').on('click','#expertlistuser .crossIcon', function(){
     $(this).closest('#expertlistuser').removeClass('active');
     $('.expertOverfade').fadeOut(100);
  });


 $('body').on('change','select[name="categories"]',function(event) { 
      var   selectcatid = $('option:selected',  this).attr('catid');
        var only ='onlysle';
        var abc =' ';
         $('.dbfeedfilterbycat  input').attr('checked', false);
        seeglobaldbeelist('nouserid',5,abc,'myhome','catetorylist','dbcat',selectcatid,only);       
     });
 
 
    $('body').on('click','.catlistval', function(event) {
        var EThis = $(this);           
        var cattitleid = $(this).attr('catid');
        var only ='onlyclk'; 
        var abc =' ';
        seeglobaldbeelist('nouserid',6,abc,'myhome','catetorylist','dbcat',cattitleid,only);
    });

    $('body').on('change','.dbfeedfilterbycat .checkInplist .onlychk',function(){
        $('.catmain').show();
        chklen = $('.dbfeedfilterbycat li .checkInplist input:checked').length;
        if (chklen == 0) {
            $('.catmain').hide();
        }
    })

    $('body').on('click','.cancelbtn',function(event){
        var catidval = $(this).attr('catid');
        //alert(catidval)
        if($(this).closest('li').find('.catlistval').attr("catid")==catidval){
            $(this).closest('li').find('.catlistval').remove();
            //$(this).remove();
            var Listval = $('#dbfeedfilterbycat'+catidval +'').val();
            if(Listval==catidval){
              $('#dbfeedfilterbycat'+catidval +'').prop('checked',false);
            }
        } 
       return false;
    })

    $('body').on('click','#event_section,#my_event_section,#spacialdb_section,#my_liveBroadcast,#my_spacialdb_section,#SurveysRightBtn',function(event){
          $('#filtercat').hide();
    })
    
    $(".eventListSlide").flexslider({
        animation:"slide", 
        slideshow:false,       
        useCSS:false,
        smoothHeight:true
    }); 
  
   $('body').on('click','.cmntOpen',function (){
    var pr = $(this).closest('li');
    var st = pr.offset().top - pr.prop('scrollHeight');

        $('body .imOnCommentBox').removeClass('imOnCommentBox');
        $('html').addClass('imOnComment');
        if($('div', pr).hasClass('postInnerLargeView')==true){
         $('.postListContent', pr).unwrap();
         }
         pr.wrapInner('<div class="postInnerLargeView"></div>');
        pr.addClass('imOnCommentBox');

        
       pr.prepend('<div class="closeBarComment"><a href="javascript:void(0);" class="fa fa-times"></a></div>');
        var dbId  = $(this).attr('dbid');
        var eventId  = $(this).attr('eventid');
        var groupId  = $(this).attr('groupid');
        var dbOwner  = $(this).attr('dbowner');
        var checkDiv = $('#listingCommentLatest'+dbId).length;
        if(checkDiv==0){
            $('.postInnerLargeView',pr).append('<div id="listingCommentLatest'+dbId+'" class="listingCommentLatest"></div>');
            $.ajax({
                 type: "POST",
                url:BASE_URL+'/myhome/clickcomment',
                data:{dbId:dbId, eventId:eventId, groupId:groupId, dbOwner:dbOwner, userId:SESS_USER_ID},
                beforeSend:function(){
                    $dbLoader('#listingCommentLatest'+dbId);
                },
                success:function(data){
                  
                    $('#listingCommentLatest'+dbId).html(data);
                      var cmntBox = $('.minPostTopBar', pr).detach();
                        cmntBox.appendTo(pr);

                    /*setTimeout(function(){
                        $('textarea',pr).focus();
                    },1000)*/
                     $('textarea', pr).mentionsInput({
                                onDataRequest: function(mode, query, callback) {

                                    data = $.parseJSON(value);
                                    responseDataMy = _.filter(data, function(item) {
                                        return item.name.toLowerCase().indexOf(query.toLowerCase()) > -1
                                    });
                                   // console.log(responseDataMy);
                                    callback.call(this, responseDataMy);
                                }
                            });
                

                }
            });
        }//close if checkdiv
        else{
             var cmntBox = $('.minPostTopBar', pr).detach();
                        cmntBox.appendTo(pr);
        }

         $('.closeBarComment a').click(function (e){
            e.preventDefault();
            $('body .imOnCommentBox').removeClass('imOnCommentBox');
            $('html').removeClass('imOnComment');                    
            $('.closeBarComment').remove();
             $('body').scrollTop(st);
         });
   });

 

}) /*ready close*/ 



$(document).ready(function(){

   $sharemydropzone(); 


   $('body').on('click','.closePopbtn', function(){     
     $('#mesageNotfiOverlay').remove();
     $('#pageContainer').removeClass('blurBgpop');
     sharefilestore=[];
   });

    $('body').on('click','#mymenuItem a, .eventmore', function(e){   

       $('#filtercat').remove();
       $('[data-id="myRighSideBtn"]').trigger('click');
    });


   $('body').on('click','#shareContact', function(){
     $('.msgNoticontent .strtSharbtn').show().removeClass('disabled');
     $('.msgNoticontent .shareYlobtn').show().removeClass('disabled');
     $('.msgNoticontent .strtShrBtnpad').attr('share','contact');
     $('.msgNoticontent .userInptxt').hide();
   });

   $('body').on('click','#sharefoll', function(){
     $('.msgNoticontent .strtSharbtn').show().removeClass('disabled');
     $('.msgNoticontent .shareYlobtn').show().removeClass('disabled');
     $('.msgNoticontent .strtShrBtnpad').attr('share','follow');
     $('.msgNoticontent .userInptxt').hide();
   });

   $('body').on('click','.dropShareBtn #userShare', function(){
     $('.msgNoticontent .userInptxt').show();
     $('.msgNoticontent .strtSharbtn').show().addClass('disabled');
     $('.msgNoticontent .shareYlobtn').show().addClass('disabled');
     $('.msgNoticontent .strtShrBtnpad').attr('share','user');
      
   });

   $('body').on('click','.dropShareBtn span', function(){
      $('.dropShareBtn span').removeClass('active');
      $(this).addClass('active');

   });
   $("body").on('change','#myTags2', function(){
        //$('#resetusersetr').show();       
        if($('#myTags2').val()!=0){      
            
            $('.msgNoticontent .strtSharbtn').show().removeClass('disabled');
            $('.msgNoticontent .shareYlobtn').show().removeClass('disabled');
        }else{
            
            $('.msgNoticontent .strtSharbtn').show().addClass('disabled');
            $('.msgNoticontent .shareYlobtn').show().addClass('disabled');
           
        }
     });


   $('body').on('click','#shareBtn', function(){
        $dbMessageProgressBar({close:true});
        
       $('#fadeDropzone1').trigger('click');
       
        /* $sharemydropzone.on("complete", function (file) {
            console.log(myDropzone.getUploadingFiles());
        /* if (myDropzone.getUploadingFiles().length === 0 && myDropzone.getQueuedFiles().length === 0) {
        window.location.reload();
        }*/
      //});*/
               
    });

 $("body").on('click','.shareYlobtn:not(.disabled),.btnsharclose', function(){
        El = $(this);
        //$('#resetusersetr').show();
        // alert(sharefilestore.file);
        // console.log(sharefilestore);
        var btnattr = $(this).attr('share');
       // alert(btnattr);
        var userid = $('#myTags2').val();
       
        //var sharefile = $('#sharetype').val();

        var sdata = {'typehh':btnattr,'userid':userid,'sfile':sharefilestore}
        //var formData = new FormData('form#fadeDropzone1');
       
         $.ajax({
            type: "POST",
            dataType: 'json',
            data: sdata,
            url: BASE_URL + "/myhome/sharefileinsert",
            beforeSend: function(){
                $('.shareYlobtn').hide();
                $dbLoader('#strtShrBtnpad','progress');
            },
            success: function(response) {
               sharefilestore=[];
               $('#mesageNotfiOverlay').remove(); 
               $('#pageContainer').removeClass('blurBgpop');  
               loaduserpdf('.userpdfleft');  
              // $messageSuccess('file send to specified user'); 
               callsocket();         
            }
        });
});

$('body').on('click', '#sfiledel:not(.disabled)', function() {
        
        var El = $(this);
        var parentsdiv = El.closest('.downloadPdflist').attr('data');
        var is_front = El.closest('.downloadPdflist').attr('is_front');
        var myupload = El.closest('.downloadPdflist').attr('nsn');

        var parentdivid = "sfile"+parentsdiv;
       
        var lsncon = 'Are you sure to remove this file.';
        var nsn = true;
        /*if(myupload==1){              
            var is_frontns = is_front;
        }*/
         var data = {'fileid':parentsdiv,'myupload':myupload}
        /*else{
            var lsncon = 'You can\'t remove this file';
            var nsn = false;
        }*/

        $dbConfirm({
            content: lsncon,
            yes: nsn,                           
            no: nsn,                            
            yesClick: function() {
              $.ajax({
                     type: "POST",
                     dataType: 'json',
                     data: data,
                     url: BASE_URL + '/myhome/deletesfile',
                     success: function(response) { 
                        El.closest('.downloadPdflist').remove();   
                                                                                  
                    }  
                    }); // ajax end here

                 } // yes click end here
        }); // confirmbox end here  
        
});


/*$('body').on('click', '.notifyAccptBtn', function(e) {
    alert('');
 });*/

$('body').on('click', '.shareArrow li:not(.disabled)', function(e) {

        e.preventDefault();
        e.stopPropagation();
        
        var El = $(this);
        var fileid = El.attr('fileid');
        var type = El.attr('type'); 
        var data = {'type':type,'fileid':fileid}        


        if(type!='user'){
            if(type=='contact'){
              var scontent = 'Are you sure you want to share this file with your Contacts?';
            }else{
              var scontent = 'Are you sure you want to share this file with your Followers?';
            }
            $dbConfirm({
                content: scontent,
                yes: true,                           
                yesLabel: 'Yes',
                no: true,
                noLabel: 'No',
                
                yesClick: function() {
                  $.ajax({
                         type: "POST",
                         dataType: 'json',
                         data: data,
                         url: BASE_URL + '/myhome/sharefileupdate',
                         success: function(response) { 
                             callsocket();  
                           //$messageSuccess('File send to specified user');                                 
                        }  
                        }); // ajax end here

                     },

                 noClick: function() { 
                    return false;
                }
     
            }); // confirmbox end here  

         }else{
            $('#mesageNotfiOverlay').remove();

              if($('#mesageNotfiOverlay').is(':visible')!=true){  
              
               $('body').append('<div id="mesageNotfiOverlay" class="loaderOverlay3"> </div>');
               
            $('#mesageNotfiOverlay').html('<div class="msgNoticontent">Search users<div class="ansi"><input id="submit_tag_names" type="hidden" value="" name=""><ul class="fieldInput" id="myTagsshare"></ul> </div>\
                <div class="sharejsf"><span class="shareupdatebtn btn btn-yellow" id="shareupdate" fileid="'+fileid+'" type="user">Start Sharing</span><span class="closePopbtn btn">Close</span></div> </div>');
            $('#mesageNotfiOverlay .msgNoticontent').addClass('popupStripBar');

                        $('#myTagsshare').tokenInput(BASE_URL+"/myhome/searchusers/", {
                               preventDuplicates: true,
                               hintText: "type user name",
                               theme: "facebook",
                               resultsLimit:10,
                               resultsFormatter: function(item){ return "<li>" + "<img src='" + item.url + "' title='" + item.name + "' height='25px' width='25px' />" + "<div style='display: inline-block; padding-left: 10px;'><div class='full_name'>" + item.name + "</div><div class='email'>" + item.email + "</div></div></li>" },
                               tokenFormatter: function(item) { return "<li><p><img src='" + item.url + "' title='" + item.name + "' height='25px' width='25px' />" + item.name + "</p></li>" }
                            })

                     $("#shareupdate").click(function(){

                            El = $(this);                           
                            var type = $(this).attr('type'); 
                            var fileid = $(this).attr('fileid');                         
                            var userid = $('#myTagsshare').val();                           
                            var sdata = {'type':type,'userid':userid,'fileid':fileid} 

                            if(userid!=''){                   
                                 $.ajax({
                                    type: "POST",
                                    dataType: 'json',
                                    data: sdata,
                                    url: BASE_URL + "/myhome/sharefileupdate",
                                    beforeSend: function(){
                                        $('#shareupdate').hide();
                                        $dbLoader('#shareupdate','progress');
                                    },
                                    success: function(response) {                                   
                                       $('#mesageNotfiOverlay').remove(); 
                                       $('#pageContainer').removeClass('blurBgpop');    
                                       //$messageSuccess('File send to specified user'); 
                                        callsocket();         
                                    }
                                });
                            }else{
                                $messageError('Please select user'); 
                                return false;
                            }
                        });

                 }

         }
        
});



$('body').on('click', '#myuploadsvew', function(e) {
        e.preventDefault();
        $('#leftListing .tabLinks').remove();
        $('#dashboarduserDetails').remove();
        if ($('#dbee-feeds').is(':visible') == true) {                  
               $('#dbee-feeds').html('<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>');
            } else {
               $('#my-dbees').html('<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>');
            }
        $('.searchByThis').remove(); 
        $('.customizeDeshboard').hide();
        $('.biogrophydisplay').fadeOut('fast');
        $('#customFieldsDown').hide(); // to hide customised dashborad filters   
        if ($('#dbee-feeds').is(':visible') == false) {
            $('#dashboarduserDetails').fadeOut('fast');
            $('#postMenu').fadeOut('fast');
            $('#Selectedposts').fadeOut('fast');
            $('#leftListing .profileStatsWrp').fadeOut('fast');
            $('#rightListing .proinfo').fadeOut('fast');
            $('#leftListing .group-highlighted').fadeOut('fast');
            $('#rightListing .contactInfo').fadeOut('fast');

        }

        $.ajax({
            type: "POST",
            data: {
                type: "share"
            },
            url: BASE_URL + '/dashboarduser/loaduserpdfdetail',
            beforeSend: function() {},
            success: function(response) {
           
                if(response.success==1){         

                var typeIcon = '<i class="fa fa-file-pdf-o pdfPostIcon"></i>';
                var pdf ='';
                var datacontent = '';
                var extfile = '';
                datacontent += '<div class="user-name titleHdpad">Uploads</div><li class="pdfListing">';
                $.each(response.content, function(i, value){
                extfile = value.kc_file.split('.');
                extfile = extfile[1];
                typeIcon = '<i class="fa fa-file-'+iconexe[extfile]+'-o pdfPostIcon"></i>';
                datacontent +='<div class="downloadPdflist" data="'+value.id+'" nsn="1" is_front="'+value.is_front+'"><div class="rtpdflinkd">'+typeIcon+' '+value.title+' <span class="subtitle">Upload on '+value.adddate+' at '+value.stime+'</span></div>\
                  <div class="rtpdfIcons"><span id="sharedetail" class="shareArrow" rel="dbTip" title="Share">\
                  <a title="share"><i class="fa fa-share-alt"></i></a> <ul><li fileid="'+value.id+'" type="following">Share with Followers</li>\
                  <li fileid="'+value.id+'" type="user">Select users</li>\
                  </ul></span><span  title="Download"><a href="' + BASE_URL + '/dashboarduser/downloadpdfuser/pdf/' +value.kc_file+ '/isf/' +value.is_front+ '/id/' +value.kc_id+ '" rel="dbTip" title="Download" ><i class="fa fa-download"></i></a></span>\
                  <span id="sfiledel"><a rel="dbTip" title="Delete" title="Delete"><i class="fa fa-times"></i></a></span>\
                </div></div>';  

                });
                datacontent += '</li>';
                if ($('#dbee-feeds').is(':visible') == true) {
                    $('#dbee-feeds').html(datacontent);
                } else {
                   
                    $('#my-dbees').html(datacontent);
                }
                //$('#leftListing').html(datacontent);

            }else{
                     $('#leftListing').html( 'no results found' );
                }

            }
        });

        if ($(document).scrollTop() != 0) {
            $('html, body').animate({
                scrollTop: 0
            }, 'fast');
        }
    });


    $('body').on('focus','.frntSignIn input, .frntSignIn select', function(){
          $(this).closest('.signInInput, .rowfrontInput').addClass('active');
          $(this).css({color:'#fff'});
      });

      $('body').on('blur','.frntSignIn input, .frntSignIn select', function(){
          $(this).closest('.signInInput, .rowfrontInput').removeClass('active');
          //$(this).css({background:'none'});
      });



     if($.cookie('navcookie')=='30'){
        $('#pageContent').addClass('active');
     }
     
   $('body').on('click','.navMinicon', function(){
       $(this).closest('#pageContent').toggleClass('active');
        $('#leftsideMenu ul li a').removeAttr('rel');
       if($(this).closest('#pageContent').hasClass('active')==true){
          $.cookie('navcookie','30', { path: '/', domain: SERVER_NAME});
          $('#leftsideMenu ul li a').attr('rel','dbTip');
        }
       else
        {
          $.cookie('navcookie','', { path: '/', domain: SERVER_NAME});
        }

   });

   $('body').on('click','#RefreshYoutube', function(){
     var src = $('#ytplayer').attr('src'); 
     $('#ytplayer').attr('src', src);
    });


    $('body').on('click','.expertIconOnMobile', function(){
        $('.rightSideListExpert').removeAttr('id');
        $('.closeBarComment').remove();
        $('html').addClass('onExpertFull');
        $('body').append('<div class="closeBarComment"><a href="javascript:void(0);" class="fa fa-times"></a></div>');
        $(window).trigger('resize');

    });
    $('body').on('click','.twitterIconOnMobile', function(){
        $('.rightSideListTwitter').removeAttr('id');
        $('.closeBarComment').remove();
        $('html').addClass('onTiwitterFull');
         $('body').append('<div class="closeBarComment"><a href="javascript:void(0);" class="fa fa-times"></a></div>');
    });
    $('body').on('click','.closeBarComment', function(){
        $('html').removeClass('onTiwitterFull imOnComment onExpertFull');
    });

    $('body').on('click','.otheruserlike:not(.single)', function(e){
      var thisEl = $(this);
      var dbeeid = thisEl.attr('data-dbeeid');
      var score = thisEl.attr('data-score');
      var sdata = {'dbeeid':dbeeid,'score':score}
        $.ajax({
                type: "POST",
                dataType: 'json',
                data: sdata,
                url: BASE_URL + "/myhome/scoreuserlist",
                beforeSend: function(){
                    
                    //$dbLoader('#shareupdate','progress');
                },
                success: function(response) {                                   
                     
                   $.dbeePopup(response.content);         
                }
            });
        
    });


    $('body').on('click','#upload-pic',function(){ 
        var picture = $("#profile_picture").val();  
         pictureData = getCropDataUrl();                                    
        $.post(BASE_URL+'/profile/changepic', { picture: pictureData }, 
        function (data) 
        {
          
            if(data.status == 'success'){
               picture = data.picture;
                userpic = picture;
                $('#profileimage').removeAttr('style').css({
                    backgroundPosition: 'left top',
                    backgroundImage: 'url(' +IMGPATH+'/users/' + picture + ')',
                    backgroundRepeat: 'no-repeat',
                    backgroundSize: 'contain'
                });
                
                $('.myPicForChat img, .mymenuItem img').attr('src',  IMGPATH+'/users/small/' + picture );
                $('.closePostPop').trigger('click'); 
                location.reload();
            }
            else
            {
                $dbConfirm({content:data.message, yes:false});
            }   
        }, 'json');
    });

 

    if (hash == '#videobroadcast'){
        $('#spacialdb_section').trigger('click');
    }
    else if (hash == '#SurveysRightBtnOn'){
        $('#SurveysRightBtn').trigger('click');
    }
    else if (hash == '#event_sectionOn'){
        $('#event_section').trigger('click');
    }
    else if (userID == adminID && hash == '#event_section'){ 
        $('#my_event_section').trigger('click');
    }
    else if (hash == '#event_section'){ 
        $('#event_section').trigger('click');
    }
    else if (hash == '#my_event_sectionOn'){
        $('#my_event_section').trigger('click');
    }
    else if (hash == '#spacialdb_sectionOn'){
        $('#spacialdb_section').trigger('click');
    }
    else if (hash == '#liveBroadcastOn'){
        $('#liveBroadcast').trigger('click');
    }
    else if (hash == '#my_spacialdb_sectionOn'){
        $('#my_spacialdb_section').trigger('click');
    }
    else if (hash == '#my_liveBroadcastOn'){
        $('#my_liveBroadcast').trigger('click');
    }
    else if (hash == '#myqap'){
        $('#my-dbees-qa').trigger('click');
    }

    $('body').on('click', '#get-booking', function() 
      {
          var carrID = $(this).attr('carrID');
          var page = 1;
          var Thiss = $(this);
          $('#dbee-feeds').html('');
          busybooking = true;
          getBooking(carrID,page,Thiss);
      });

    $(window).scroll(function()
    {
        if  ($(window).scrollTop() == $(document).height() - $(window).height()) 
        {
            if(busybooking == true)
            {
                var Thiss = $('#get-booking');
                var carrID = Thiss.attr('carrID');
                var page = Thiss.attr('data-page');
                if(typeof carrID == 'undefined')
                  return false;
                getBooking(carrID,page,Thiss);
            }            
        }
    });


    /*var sessionTimer =0;

    setInterval(function(){
      sessionTimer++;
      if(sessionTimer==5) gologout();
    },10000);*/


  $('body').on('click', '#passform input[type="text"], #passform textarea',function (){
      $(this).closest('#passform').find('.formRow.activeRow').removeClass('activeRow');
      $(this).closest('.formRow').addClass('activeRow');
  });

});




function followme(id, thisBtn) {
    var thisEl = $(thisBtn);
    thisEl.addClass('processBtnLoader');
    if (thisEl.closest('div').attr('id') == 'profileCreatorBox') {
        $('div', thisEl).prepend(' <i class="fa fa-spinner fa-spin"></i>').css('cursor', 'default');
    } else {
       $('div', thisEl).append(' <i class="fa fa-spinner fa-spin"></i>').css('cursor', 'default');
    }
    $('#followme-label', thisEl).css('cursor', 'default');
    setTimeout(function (){
        createrandontoken(); // creting user session and token for request pass
        var data = "user=" + id + '&' + userdetails;
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: data,
            url: BASE_URL + '/following/makefollow',
            async: false,
            success: function(response) {
                
                if (response.status == 'success') {

                    if(response.chatList!='')
                    {
                        if(response.username!='' && response.ProfilePic!='') $.callallchatusers(response.chatList,id,response.username,response.ProfilePic);                      
                    }

                    if(localTick == false){
                        callsocket();
                        
                    }
                    if (response.types == 'Follow')
                        $('#followme-label', thisEl).html('Follow');
                    
                    else{
                        $('#followme-label', thisEl).html('Unfollow');
                         var profileUserId =  $('#profileuser').val();
                         if(id==profileUserId){
                            $('#dashboarduserDetails a[followid="'+id+'"] #followme-label').html('Unfollow');
                         }
                      
                        $('#actNotification a[followid="'+id+'"]').remove();
                        storeNotifications = $('#actNotification').html();

                    }
                   
                } else if (response.status == 'error')
               
                    $dbConfirm({
                    content: response.message,
                    yes: false,
                    error: true
                });
                thisEl.removeClass('processBtnLoader');
                $('.fa', thisEl).remove();
                thisEl.css('cursor', 'pointer');
                $('#followme-label', thisEl).css('cursor', 'pointer');

            },
            error: function(error) {
                loadError(error);
            }
        });
    }, 500);

}

function getBooking(carrID,page,Thiss)
{
    if(page!=1)
    {
      $('#more-feeds-loader').remove();
      $('#dbee-feeds').after('<div id="more-feeds-loader"></div>');
      $dbLoader('#more-feeds-loader', 'progress');
      $('#more-feeds-loader').css({
          marginBottom: 50
      });
    }
     busybooking = false;

      $.ajax({
        url: BASE_URL + '/ajax/soap',
        type: 'POST',
        data: {carrID: carrID,page:page},
        dataType: 'json',
        beforeSend: function() 
        {
            if (page == 1) 
            {
              $('#leftListing').html('');
              $('#leftListing').append('<ul id="dbee-feeds" class="postListing"></ul>');
              $('#dbee-feeds').html('<div style="margin:20px 0 0 20px;"><div class="spinnerLoader"><div></div><div></div><div></div><div></div></div></div>');            
            }
        },
        success: function(data) 
        {
            if (data.status == 'success') 
            {
              busybooking = true;
              var contentList = '';
              listData = data.html;
              if(listData.length<10){
                $('#more-feeds-loader').remove();
              }
              if(page==1){
                contentList +='<div id="middleWrpBox" style="padding:13.7px;"><div style="float:left;width:200px;" class="user-name titleHdpad">My Bookings</div><span style="float:right;"><a href="">Back to profile</a></span></div>';
              }
              $.each(listData, function (key, data1) 
              {
                
                contentList += '<li class="listingTypeMedia BookingClientList"><div class="postListContent">';
                contentList += '<input type="checkbox" id="mylistcheck_'+key+'">';
                contentList += '<div class="acntNameCnt">\
                                  <i class="fa fa-user" aria-hidden="true"></i>\
                                  <div class="acntName">\
                                     <h4>'+data1['AccountName']+'</h4>\
                                     <span>Arrive: '+data1['StartDate']+' - Depart: '+data1['EndDate']+'</span>\
                                  </div>\
                                  <label for="mylistcheck_'+key+'" class="btn btn-yellow btn-mini "><i>Client Details</i><i>Hide Details</i></a>\
                                </div>';

                contentList += '<div class="clntDetailsWrp">\
                                   <h5>Client details</h5>';
                
                if(data1['AccountName']){                    
                  contentList += '<p class="cntAcntName">'+data1['AccountName']+'</p>';
                }
                if(data1['ClientHomePhone']){                    
                  contentList += '<p><i class="fa fa-phone-square" aria-hidden="true"></i> <span>'+data1['ClientHomePhone']+'</span></p>';
                }
                if(data1['ClientAddressLine1'] && data1['ClientAddressLine2'] && data1['ClientAddressLine3']){                    
                  contentList += '<p><i class="fa fa-map-marker" aria-hidden="true"></i> <span>'+data1['ClientAddressLine1']+', '+data1['ClientAddressLine2']+', '+data1['ClientAddressLine3']+', '+data1['ClientPostCode']+'</span></p>';
                }

                if(data1['ClientProfile']){                
                  contentList +='<div class="cntPr"><h5>Profile</h5>\
                                  <p>'+data1['ClientProfile']+'</p></div>';
                }
                contentList += '</div></div></li>';
              });
              
              page = parseInt(page);
              Thiss.attr('data-page',parseInt(page+1));
              if(page==1)
                $('#dbee-feeds, #my-dbees').html(contentList);
              else
                $('#dbee-feeds, #my-dbees').append(contentList);

              $('#more-feeds-loader').remove();
          }else{
            $('#more-feeds-loader').remove();
          }
        }
    });
}