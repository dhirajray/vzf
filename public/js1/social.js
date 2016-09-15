var ProfileDetailsHttp; 
var currentGroupId = '';
var currentGroupName ='';
var shareType = '';
var uniqueIDSocial = '';
var callingfor = '';

$(function() {

    

    $('body').on('click', '.socialfriends', function() 
    {
        $.dbeePopup('close');
        setTimeout(function (){

        forto = $(this).attr('data-for');
        uniqueValue = $(this).attr('data-uniqueValue');
        var reportTemplate = '<h2 class="headingfor">Invite social connections</h2><div  id="content_data_button" style="padding:10px 0px;; overflow:hidden;"><div class="loaderAjWrp"><i class="fa fa-spinner fa-spin fa-3x"></i></div></div>';
        $.dbeePopup(reportTemplate, {
            overlayClick: false
        });
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {"forto": forto,'uniqueValue':uniqueValue},
            url: BASE_URL + '/social/socialtype',
            success: function(response) {
                $("#content_data_button").html(response.content);
                $('.headingfor').html(response.heading);
                $.dbeePopup('resize');
            }
        });
        },2000);
    });

    $('body').on('keyup', '.findsocialfriend', function(e) 
    {
        if (e.keyCode == 13) 
        {
            var type = $(this).attr('shareType');
            checkedList = $('#PrivatePost').val();
            keywords = $('.findsocialfriend').val();
            var prt = $(this).closest('.srcUsrWrapper');
             $('.InviteSocial').hide();
            if (keywords.length==0) 
                return false;
            $.ajax({
                type: "POST",
                dataType: 'json',
                data: {"type": type,'keywords':keywords,'callingfor':callingfor,'checkedList':checkedList},
                url: BASE_URL + '/social/socialsearch',
                beforeSend:function(){
                    $('.searchIcon2', prt).removeClass('fa-search').addClass('fa-spinner fa-spin');
                },
                success: function(response) 
                {
                    
                    $('.userFatchList').remove();
                    $('.notfoundSocial').remove();
                    var totalLoad = 0 ;
                    $("#socalnetworking-list").append('<div id="temImgLoadWrp" style="display:none">'+response.UserData+'</div>');

                    if(response.twitter!='error')
                    {
                        var totalUserImg  = $('#temImgLoadWrp img').size();
                        if($('#temImgLoadWrp div').hasClass('notfoundSocial')!=true){          
                             $('#temImgLoadWrp img').load(function(){
                                    totalLoad++;
                                     if(totalUserImg  == totalLoad || $('#temImgLoadWrp div').hasClass('notfoundSocial')==true){
                                        $('#temImgLoadWrp').remove();
                                        $("#socalnetworking-list").append(response.UserData);
                                        $dbTip();
                                        $('.searchIcon2', prt).removeClass('fa-spinner fa-spin').addClass('fa-search');
                                            if(response.userCount==true){
                                            $('.InviteSocial').show();
                                            if(callingfor=='inviteToGroup')
                                             $('#PrivatePost').attr('disabled',true);
                                            }
                                            else{
                                                $('.InviteSocial').hide();
                                            }
                                            if(callingfor =='inviteToExpert' && allowmultipleexperts==1)
                                                $('#socalnetworking-list').addClass('singleselectuser');
                                            $.dbeePopup('resize');
                                    }
                             });
                        }                    
                        else{
                            $('.searchIcon2', prt).removeClass('fa-spinner fa-spin').addClass('fa-search');
                            $('#temImgLoadWrp').remove();
                             $("#socalnetworking-list").append(response.UserData);
                             $.dbeePopup('resize');
                        }
                    }else
                    {
                        $('#socalnetworking-list').html('Oops, you have exceeded the maximum allowed limit. Please try again in '+response.minute+' minutes.');
                    }
                }
            });
        }
    });

    $('body').on('click', '.singleselectuser .labelCheckbox input', function() 
    {
         $(this).closest('.singleselectuser').find('.labelCheckbox input').attr('checked', false);
         $(this).attr('checked', true);
    });



});


function SocialUserSearch(callingfors,uniqueID,type) 
{
    uniqueIDSocial = uniqueID;
    callingfor = callingfors;
    shareType = type;
    //title = $('#siteHeadTitle').val();

    if(type=='linkedin'){
        searchForMessage = 'Search LinkedIn connections';
        placeholder = 'type a connections name and hit enter';
    }else if(type=='twitter'){
        searchForMessage = 'Search Twitter followers';
        placeholder = 'search Twitter username';
    }else{
        searchForMessage = 'Search Facebook friends';
        placeholder = 'type a friends name and hit enter';
    }
    /*if(callingfors=='inviteToExpert'){
        searchForMessage = searchForMessage+' '+title
    }*/
    $.dbeePopup('close');
    if(callingfor=='inviteToGroup')
    {
        btns = '<div class="btnGroup pull-right" ><select id="PrivatePost" name="PrivatePost"  class="pull-left PrivatePost" tabindex="-1" ><option value="0">Open invite</option><option value="1">Lock to Group</option></select><a href="javascript:void(0);" class="pull-right btn btn-yellow InviteSocial" style="display:none;" >Invite</a></div>';
    }
    else
        btns ='<a href="javascript:void(0);" class="pull-right btn btn-yellow InviteSocial" style="display:none;" >Invite</a>';

    setTimeout(function() {
        var container = '<h2>'+searchForMessage+'</h2><div id="socalnetworking-list" class="clearfix">\
        <div class="srcUsrWrapper">\
                    <i class="fa fa-search fa-lg searchIcon2"></i>\
                    <input type="text" id="userFatchList" class="findsocialfriend" placeholder="'+placeholder+'" shareType="'+type+'"></div>\
                </div>';
        btns = btns;
        $.dbeePopup(container, {
        overlay: true,
        otherBtn: btns,
        overlayClick:false,
        load:function(){ 
           if(callingfor=='inviteToGroup')
                privateHTMLPost()
        },
        });
    }, 500);
    //console.log('shareType='+type+' uniqueIDSocial= '+uniqueIDSocial+' '+callingfor);
}

