$(function(){

$('body').on('click','#createdgroup',function(){
  $('.useractGroup').show();
  $('.mostserGroup').hide();
  $(this).siblings('#mostactivatedgroup').removeClass('active');
  $(this).addClass('active');
}); 
$('body').on('click','#mostactivatedgroup',function(){
  $('.mostserGroup').show();
  $('.useractGroup').hide();
  $(this).siblings('#createdgroup').removeClass('active');
  $(this).addClass('active');
});

      //$('.userPic img').imageLoader();
      
        $('.btnRefresh').click(function()
        {                       
            var param   =   $(this).attr('param'); 
            var thisEl  =   $(this);
            var url     =   '';
            var data    =   '';
            var userid = $(this).attr('uid');	
            if(param=='dbee')           
            {  					
              if(userid){
                url = BASE_URL+"/admin/user/livedbee";
               data='uid='+userid;  
              }else{
                url = BASE_URL+"/admin/dashboard/dbeereload";
              }
            }  
            else if (param=='group')    
            {			
              if(userid){
              url = BASE_URL+"/admin/user/livegroupusr";
              data='uid='+userid;  
              }else{
              url = BASE_URL+"/admin/dashboard/groupreload";
              }
            }
            else if (param=='comment')  {

            var userid = $(this).attr('uid');			
            if(userid){
            url = BASE_URL+"/admin/user/livecomment";
            data='uid='+userid;  
            }else{
            url = BASE_URL+"/admin/dashboard/commentreload";
            }
            }
            else if (param=='score')    { 			 
            if(userid){
            url = BASE_URL+"/admin/user/livescoreusr";
            data='uid='+userid;  
            }else{
            url = BASE_URL+"/admin/dashboard/scorereload";
            }
            }
            else if (param=='scoreme')    { 			 
            if(userid){
            url = BASE_URL+"/admin/user/livescoremeusr";
            data='uid='+userid;  
            }
            }
            else if (param=='postvisiter')    {        
              if(userid){
              url = BASE_URL+"/admin/index/callingajaxcontainers";
              data='uid='+userid+'&calling=postvisitinguser';  
              }
            }
            else if (param=='sendmessage')    {        
              if(userid){
              url = BASE_URL+"/admin/user/usermessages";
              data='uid='+userid+'&calling=sendmessage';  
              }
            }
    
            $.ajax({                                      
                  url: url,                  
                  data: data,                        
                  method: 'POST',  
                  beforeSend: function(){                        
                   /* var htmlLoader = $(document.createElement('div')).addClass('loaderBoxOverlay');
                    var parentEl =  thisEl.closest('.dashBlock');
                    parentEl.append(htmlLoader);*/
                      $dbLoader({element:thisEl.closest('.dashBlock').find('.dashContent'),overlay:true});
                  },
                  success: function(result)    
                  {
                   //alert(result);
                    thisEl.closest('.dashBlock').find('.dashContent').html(result);
                    $('.userPic img', thisEl.closest('.dashBlock').find('.dashContent')).imageLoader();

                     $('.dashBlock').equalizeHeights(); 
                  }
            }); 
        });


            $('.btnExpand').click(function(){
                var parentEl = $(this).closest('.dashBlock');
                var title =  parentEl.find('h2').text();
                $('#dialogLineGraph').dialog({
                    modal:true, 
                    title:title, 
                    width:'auto',
                    resizable:false,
                    closeOnEscape:true,
                    open:function(){
                        $('.ui-dialog-titlebar-close').addClass('btn btn-mini btn-warning');
                    }
                });
            });

                $.fn.equalizeHeights = function(){
                    return this.height(
                        Math.max.apply(this,
                            $(this).map(function(i,e){
                                return $(e).height()
                            }).get()
                        )
                    )
                }   

            //$('#dashboardWrapper .dashBlock').equalizeHeights();  
			
            //This is for dragand drop script
           /* var LI_POSITION = 'position';
                  $( "#dashboardWrapper").sortable({
                        opacity:'0.5', 
                        handle:'.dragHandler',
                        connectWith: ".column",
                        start: function(e, ui){
                            ui.placeholder.height(ui.item.height()); 
                           
                        },
                        stop: function(e, ui){
                           // var order = $('#dashboardWrapper').sortable('serialize');
                            //alert(order);
                        },

                       update : function(event, ui) {
                            //create the array that hold the positions...
                            var order = [];
                            //loop trought each li...
                            $('#dashboardWrapper .dashBlock').each(function(e) {
                                //add each li position to the array...
                                // the +1 is for make it start from 1 instead of 0
                                order.push($(this).attr('id') + '=' + ($(this).index() + 1));
                                // alert(order.push($(this).attr('id') + '=' + ($(this).index() + 1)));

                            });
                            // join the array as single variable...
                            var positions = order.join(';');
                            //use the variable as you need!
                          // alert(positions);
                            $.cookie(LI_POSITION, positions, {
                                expires : 10
                            });
                        }
                       
                 });   */
              //  var newOrder = $('#dashboardWrapper').sortable('toArray').toString(); alert(newOrder);
                   


                  
            
            //End this is for dragand drop script

            // this  is show hide script to block
            $('.dashBlock .btnMinimize').click(function(){
                if($(this).closest('.dashBlock').find('.dashContent').is(':visible')==true){
                    $(this).find('.minimizeIcon').removeClass('minimizeIcon').addClass('maximizeIcon');
                }
                else{

                    $(this).find('.maximizeIcon').removeClass('maximizeIcon').addClass('minimizeIcon');
                }
                $(this).closest('.dashBlock').find('.dashContent').fadeToggle();

            });

             // this  is show hide script to block

        /*pie cart*/
        

        $('.proCloundlist  span .removeCompare').click(function  () {
                var thisSelector = $(this);
               var  wordTag = thisSelector.siblings('i').text();
               var confirmD = '<div id="confirBox" title="Please confirm"><p>Are you sure you want to delete <strong>'+wordTag+'</strong>?</p></div>';
               var filterid =  $(this).parent('span').attr("filterid");
              
                $('#confirBox').remove();
                $('body').append(confirmD);
                $('#confirBox').dialog({ 
                       buttons: {
                        "Yes": function() {                         
                            var thisEl =$(this);                        
                           thisSelector.closest('span').fadeOut('fast', function() {
                                $(this).remove();
                                 thisEl.dialog( "close" );
                            });                   
                            $.ajax({
                                type : "POST",
                                dataType : 'json',
                                url : BASE_URL + '/admin/dashboard/profanitydelete',
                                data:{'filterID':filterid},   
                                success : function(response) {                                      
                                 // grouprow.remove();                                  
                                  $messageSuccess("removed successfully");
								  $('#proCount').text(function(i,txt) { return parseInt(txt, 10) + -1; });
                                   thisEl.dialog( "close" );
                                },
                                error : function(error) {
                                    $messageError("Some problems have occured. Please try again later: "+ error);                      
                                }                               
                               });
                        }
                      }
                  });
          
         });

  

    
    $('.filterChart').click(function(event) {

        var parentDas =  $(this).closest('.dashBlock');
        var callfrom  = parentDas.attr('callfrom');
        var calltype  = parentDas.attr('calltype');
        var thisEl = $(this);
        var close  = false;
       
        if(callfrom=='pie') var limit ='';
        else 
        {
           var limit = '<span class="limitDeshboardFl">Limit:</span>\
                    <select >\
                        <option value="5">5</option>\
                        <option value="10">10</option>\
                        <option value="15">15</option>\
                    </select>';
        }

        var filterOptions = '<div class="filiterContainer">\
                <span>From</span><input type="text" name="frm" value="" placeholder="Choose date"><span>to</span>\
                <input type="text" name="frmto" value="" placeholder="Choose date"><div class="mobileViewFilter">'+limit+'\
                <button  class="btn btn-yellow filterSearchBtn" adattr="search"><i class="fa fa-search"> </i> search</button></div><div class="clearfix"></div><div class="clearfix">  </div>\
            </div>';
        
        var myBox =   parentDas.find('.filiterContainer');
         if(parentDas.hasClass('seeLargeView')==false){
              parentDas.toggleClass('activeFilter');
          }
        if(parentDas.hasClass('activeFilter')==false){    
              if(parentDas.hasClass('seeLargeView')==false){
                  parentDas.find('.filiterContainer').remove();
               }

        }else{
         
            if(parentDas.hasClass('seeLargeView')==false){
                parentDas.find('h2:first').after(filterOptions);
                $select(parentDas.find('select'));
            }else{
              
                           $('.overlayPopup').fadeOut();
                          parentDas.removeClass('seeLargeView');
                        //parentDas.find('input[name="frm"]').val('');
                       // parentDas.find('input[name="frmto"]').val('');
                         parentDas.find('select').val(5);
                          $select(parentDas.find('select'));
                         parentDas.find('button.filterSearchBtn').trigger('click');
                          $('.fa-times', thisEl).removeClass('fa-times').addClass('fa-filter');
                         
                         setTimeout(function(){
                          thisEl.attr('title', 'Filter Chart');

                        }, 500);

            }
               var from = parentDas.find('input[name="frm"]');
               var  to = parentDas.find('input[name="frmto"]');
               from.datepicker({
                  changeMonth: true,
                   dateFormat: 'dd-mm-yy',
                  numberOfMonths: 1,
                  onClose: function( selectedDate ) {
                    to.datepicker( "option", "minDate", selectedDate );
                  }
                });
                to.datepicker({
                  changeMonth: true,
                   dateFormat: 'dd-mm-yy',
                  numberOfMonths: 1,
                  onClose: function( selectedDate ) {
                    from.datepicker( "option", "maxDate", selectedDate );
                  }
                });


                from.focus(function(event) {
                  $('#ui-datepicker-div').css('z-index', '99999');
                });
                to.focus(function(event) {
                  $('#ui-datepicker-div').css('z-index', '99999');
                });
               
        }



    });
// end filtercart

 $('.dragHandler .filterSaveBtn, #saveChartGrpName').click(function(event) {
                      event.preventDefault();

                      var BASE_URL  = $("#BASE_URL").val();   
                      if($(this).closest('.dashBlock').is(':visible')==true){
                         var parentDas = $(this).closest('.dashBlock'); 
                      }else{
                         var parentDas = $(this).closest('.reportCharts');
                      }
                         
                      var chart     =  parentDas.attr('calltype');
                      var chartename = parentDas.find('input[name="chartvalue"]').val(); 
                      var groupnm = parentDas.find('input[name="groupvalue"]').val(); 
                      var groupname = typeof(groupnm)!= 'undefined' ? groupnm : '';
                      var thisEl = $(this);
                    
                      if($(this).attr('data-group')=='true'){                     
                         parentDas.find('#groupvalueid option:first').attr('selected', 'selected');
                         $select( parentDas.find('#groupvalueid'));
                      }

                      //  if(chartename=='') var chartename = parentDas.find('input[name="chartvalue2"]').val();
                    
                      var groupid  =  typeof (parentDas.find('[name="groupvalueid"]').val() )!= 'undefined' ? parentDas.find('[name="groupvalueid"]').val() : '';

                      if(chart=='postvisiting')
                      {
                        var dateW  = $('#changevisiting').val(); 
                                              
                        var dateto    = $('#changevisiting option[value="'+dateW+'"]').attr('monthrange');
                        var datefrom    = $('#changevisiting option[value="'+dateW+'"]').attr('monthrange');

                      }
                      else
                      {
                        var datefrom  = parentDas.find('input[name="frm"]').val();  
                        var dateto    = parentDas.find('input[name="frmto"]').val(); 
                      }
                       
                      //  $messageError('please mention chart name'); return false;
                      var selval = (parentDas.find('.filiterContainer select').val())
                      var ranglimit = typeof (selval) != 'undefined' ? selval : '5';

                      if(groupname=='' && groupid=='' ) 
                      {
                        $messageError('Please select or create a report'); return false;
                      }
                      /*if(chartename=='' ) 
                      {
                        $messageError('please mention chart name'); return false;
                      }*/

                      data  = "type="+ chart+"&groupname="+groupname+"&groupid="+groupid+"&chartename="+chartename+"&datefrom="+datefrom+"&dateto="+dateto+"&ranglimit="+ranglimit ;

                      url   = BASE_URL+"/admin/index/savedcharts";
                      $.ajax({                                      
                          url: url,                  //the script to call to get data          
                          data: data,                        //you can insert url argumnets here to pass to api.php
                          method:'POST',
                         // dataType: 'json',                //data format      
                          success: function(data)          //on recieve of reply
                          {
                              data = data.split('~');
                             if(data[0]==1) $messageSuccess(" Saved to 'Saved reports' within reports");
                             else if(data[0]==403) $messageError('group name already exist, please try again');
                             else  $messageError('some thing going wrong, please try again');
                              if(thisEl.attr('data-group')=='true'){  

                              var chartFirst = $('select[name="groupvalueid"]').size();
                              //alert(chartFirst)
                              if(chartFirst>0){
                                $('select[name="groupvalueid"]').each(function(index, el) {
                                  $('option:not(:first)',this).remove();
                                  $('option:first',this).after(data[1]);
                                  $select($(this));
                                 
                                });
                              }
                              else{   
                                  var appendHtml = '<div class="formRow">\
                                                     <select name="groupvalueid" id="groupvalueid">\
                                                      <option value="">Select existing report</option>\
                                                      '+data[1]+'\
                                                     </select>\
                                                    </div>';
                                  $('.createdGrpDrp .clearfix').after(appendHtml);
                                  $('.grpWrapperBox').append('<button style="display:block;" id="addgroupBtn" class="btn btn-green filterSaveBtn fluidBtn removeOr" type="button">  Save to existing report </button>');
                                   $select('select[name="groupvalueid"]');
                              } 

                               parentDas.find('.cancelSaveFilter').trigger('click');  
                                
                                 parentDas.find('#groupvalueid option:last').attr('selected', 'selected');
                                 $select( parentDas.find('#groupvalueid'));

                                // $('select[name="groupvalueid"] option:not(:first)').remove();                
                                // $('select[name="groupvalueid"] option:first').after(data[1]);
                                 //parentDas.find('#groupvalueid option:last').attr('selected', 'selected');
                                // $select($('select[name="groupvalueid"]'));
                               
                              }

                             $('input[name="chartvalue"]').val('');
                             $('input[name="chartvalue2"]').val('');
                          }
                      });
                      return false;
                })

// hash tag new look



  //  search filter click fucntion
   $('body').on('click','.filiterContainer .btn, a[adattr="reload"]', function(event) {
        event.preventDefault();
        event.stopPropagation(); 

        var atrcalled = $(this).attr('adattr');
        var parentDas =  $(this).closest('.dashBlock');
        var parentDas2 =  $(this).closest('.filiterContainer');
        var callfrom  = parentDas.attr('callfrom');
                    var calltype  = parentDas.attr('calltype');
                    var thisEl = $(this);
                  //  parentDas.toggleClass('seeLargeView');

            var BASE_URL  = $("#BASE_URL").val();                      
            var chart     = calltype; 

            
            if(atrcalled=='search')
            {
              var  datefrom  = $.trim(parentDas.find('input[name="frm"]').val());
              var  dateto    = $.trim(parentDas.find('input[name="frmto"]').val());
              if(dateto=='' || datefrom==''){
                $messageError('please select date');
                return false;
              }
              var resetBtn = '<a href="javascript:void(0);" adattr="reload" class="btn btn-yellow pull-right filterreloadBtn btn-mini" >Reset</a>';
             if(parentDas.hasClass('seeLargeView')==true)  resetBtn ='';
              var htmlDateAppend  = '<div class="dateShowOnHeader">From: <strong>'+datefrom+'</strong> to: <strong>'+dateto+'</strong><input type="hidden" name="datefrom2" value="'+datefrom+'"><input type="hidden" name="dateto2" value="'+dateto+'"></div>';
                                $('.dateShowOnHeader, .filterreloadBtn', parentDas).remove();
                                parentDas.find('h2').append(htmlDateAppend+resetBtn);

            }
            else
            {
              var  datefrom  = '';
              var  dateto    = '';
             $('.filterreloadBtn, .dateShowOnHeader, .filiterContainer',  parentDas).remove();   
              parentDas.removeClass('activeFilter');  
              $.trim(parentDas.find('input[name="frm"]').val(''));
              $.trim(parentDas.find('input[name="frmto"]').val(''));
               $('.overlayPopup').fadeOut();
               parentDas.removeClass('seeLargeView');
                $('.filterChart', parentDas).show();

            } 

            var ranglimit = typeof (parentDas2.find('select').val()) != 'undefined' ? parentDas2.find('select').val() : '5';
            parentDas.find('.dashContent').html('');


            if(ranglimit>5){
               if(parentDas.hasClass('seeLargeView')==false){
                  parentDas.addClass('seeLargeView');
                  $('.overlayPopup').fadeIn();
                  $('#closeLargeView').remove();
                  $('.filterreloadBtn, .filterChart', parentDas).hide();
                  $('h2', parentDas).prepend('<a href="javascript:void(0)" class="pull-right" id="closeLargeView" title="Close" adattr="reload"><i class="fa fa-times fa-lg"></i></a>');
                  //$('.fa-filter', parentDas).removeClass('fa-filter').addClass('fa-times').closest('.filterChart').attr('title', 'Close');
               }
              
             }
             /*else{
            
              if(parentDas.hasClass('seeLargeView')==false){
                   $('.icon-remove', thisEl).removeClass('icon-remove').addClass('icon-filter');
                   $('.overlayPopup').fadeOut();
                     parentDas.removeClass('seeLargeView');
                       $('#closeLargeView').remove();
               }
             }*/

                     /*--------------------*/
                    var scoreobj = $.parseJSON( SCORE_OPTION);

                   
                    if(chart=='debatingcontainer')
                    {
                     
                       callglobalajax('debatingcontainer','index','callingajaxcontainers', 'topdebating','topdebateusers',datefrom,dateto,ranglimit);  
                    }
                    else if(chart=='popularcontainer')
                    { 
                        callglobalajax('popularcontainer','index','callingajaxcontainers', 'populardebate','populardebates',datefrom,dateto,ranglimit);
                    }
                    else if(chart=='signedcontainer')
                    {

                        callglobalajax('signedcontainer','index','callingajaxcontainers', 'usersignupfromplateform','usersignup',datefrom,dateto,ranglimit);  
                    }
                    else if(chart==scoreobj.Love || chart==scoreobj.Like || chart==scoreobj.Hate || chart==scoreobj.DisLike  )
                    {
                        data  = "type="+ chart+"&datefrom="+datefrom+"&dateto="+dateto+"&ranglimit="+ranglimit ;
                        url   = BASE_URL+"/admin/index/callscorecharts";
                        $.ajax({                                      
                            url: url,                  //the script to call to get data          
                            data: data,                        //you can insert url argumnets here to pass to api.php
                            method:'POST',
                           // dataType: 'json', 
                           beforeSend:function(){
                            $('.dashContent',parentDas).addClass('loader');
                           },               //data format      
                            success: function(data)          //on recieve of reply
                            {
                              var param=data.split("~");
                             $('.dashContent',parentDas).removeClass('loader');

                             if(param[6]>0) chartforbrowsersproviders(JSON.parse(param[0]),JSON.parse(param[1]),'mostscorecontainer',param[5],'score on comments','Score count','','',param[4],'user',JSON.parse(param[2]),JSON.parse(param[3]));
                            else $('#mostscorecontainer').html('<div class="dashBlockEmpty">no record found</div>');  

                             // chartforbrowsersproviders(JSON.parse(param[0]),JSON.parse(param[1]),'mostscorecontainer',param[5],'score on comments','Score count','','',param[4],'user',JSON.parse(param[2]),JSON.parse(param[3]));

                               
                              }
                            });
                            return false;
                           
                    }
                    else
                    {
                      data  = "type="+ chart+"&datefrom="+datefrom+"&dateto="+dateto+"&ranglimit="+ranglimit ;
                    

                      url   = BASE_URL+"/admin/index/callcharts";
                      $.ajax({                                      
                          url: url,                  //the script to call to get data          
                          data: data,                        //you can insert url argumnets here to pass to api.php
                          method:'POST',
                          //dataType: 'json',                //data format    
                            beforeSend:function(){
                            $('.dashContent',parentDas).addClass('loader');
                           },     
                          success: function(data)          //on recieve of reply
                          {
                            var param=data.split("~");
                            $( "#effect" ).show( );
                          // $('#effect').animate({opacity: 1, left: '',  height: 'toggle' }, 2500, function() {});
                          // callback();
                          $('.dashContent',parentDas).removeClass('loader');

                          switch(param[1])
                          {
                            case ('alladmin'):
                              if(param[2]>0) chartofdbees(JSON.parse(param[0]),'piecontainer',' ‘Admin created’ user activity vs ‘User created’ user activity');
                              else $('#piecontainer').html('<div class="dashBlockEmpty">no record found</div>');
                            break;
                            case ('adminbreck'):
                              if(param[2]>0) chartofdbees(JSON.parse(param[0]),'piecontainer','‘Admin created’ user activity breakdown');
                              else $('#piecontainer').html('<div class="dashBlockEmpty">no record found</div>');
                            break;
                            case ('platformbreck'):
                              if(param[2]>0) chartofdbees(JSON.parse(param[0]),'piecontainer',' ‘User created’ user activity breakdown');
                              else $('#piecontainer').html('<div class="dashBlockEmpty">no record found</div>');
                            break;
                            case ('dbee'):
                              if(param[2]>0) chartofdbees(JSON.parse(param[0]),'piecontainer','total platform activity ');
                              else $('#piecontainer').html('<div class="dashBlockEmpty">no record found</div>');
                              
                            break;
                            case ('dbeetypes'):
                              if(param[2]>0) chartofdbeetypes(JSON.parse(param[0]));
                              else $('#piecontainer').html('<div class="dashBlockEmpty">no record found</div>');
                          
                            break;  
                            case ('score'):
                              if(param[2]>0) chartofscores(JSON.parse(param[0]));
                              else $('#piecontainer').html('<div class="dashBlockEmpty">no record found</div>');
                            
                            break;
                            case ('group'):
                              if(param[2]>0) chartofgrops(JSON.parse(param[0]));
                              else $('#piecontainer').html('<div class="dashBlockEmpty">no record found</div>');
                            break;
                            default:
                            chartofdbees(JSON.parse(param[0]),'piecontainer','total platform activity ');
                          }
                        }
                      });     
                      return false;
                    }
                     /*--------------------*/

    
                  
                });

  //  search filter click fucntion


});


jQuery.cookie = function (key, value, options) {
    
    // key and at least value given, set cookie...
    if (arguments.length > 1 && String(value) !== "[object Object]") {
        options = jQuery.extend({}, options);

        if (value === null || value === undefined) {
            options.expires = -1;
        }

        if (typeof options.expires === 'number') {
            var days = options.expires, t = options.expires = new Date();
            t.setDate(t.getDate() + days);
        }
        
        value = String(value);
        
        return (document.cookie = [
            encodeURIComponent(key), '=',
            options.raw ? value : encodeURIComponent(value),
            options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
            options.path ? '; path=' + options.path : '',
            options.domain ? '; domain=' + options.domain : '',
            options.secure ? '; secure' : ''
        ].join(''));
    }

    // key and possibly options given, get cookie...
    options = value || {};
    var result, decode = options.raw ? function (s) { return s; } : decodeURIComponent;
    return (result = new RegExp('(?:^|; )' + encodeURIComponent(key) + '=([^;]*)').exec(document.cookie)) ? decode(result[1]) : null;
};
