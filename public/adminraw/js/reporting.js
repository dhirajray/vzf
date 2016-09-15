/*
this file contain all type of search represantations, being used on channels for dbee
@ authour Deepak Nagar
@ Lincesed to Dbee.me
@ Start date 28 May 2013
*/

    function filterReportUser(thisVal){
        var thisEl = $(thisVal);
        var sorting = thisEl.val();
        var id = $("input[socialFriendlist='true']").attr('id');
        var count = 0;
        var findTr = thisEl.closest('table').find('tbody tr');
      
       findTr.each(function(index) {
          
        if($('td:eq(1)', this).text()){
           if($('td:eq(1)', this).text().match(new RegExp(sorting, "i"))){
            $(this).fadeIn('slow'); 
            count++;
           }
           else{
        
              $(this).fadeOut('slow');
            }
        }
      });

     // $('.srcUsrtotal').show();
      //$(".srcUsrtotal").html(count+' <i>total</i>');
 }  
  
    $("document").ready(function(){
        $('body').on('click','.rpCsvExport',function(){
            $(this).closest('form').submit();
           
        });

        
    $('body').on('click','#hashTagSelectedWrapper h2',function(event){
    var parentDiv =   $(this).closest('#hashTagSelectedWrapper')
    var cnt = $(this).siblings('.listing');   
      parentDiv.find('.listing').not($(this).siblings('.listing')).slideUp();
      $(this).closest('h2').find('.fa-minus').removeClass('fa-minus').addClass('fa-plus');
      if(cnt.is(':visible')==false){
        $('i', this).removeClass('fa-plus').addClass('fa-minus');
        $('#toolboxPlatform', cnt).addClass('animated');
        if(isMobile!=null){
          
      
           $('#toolboxPlatform', cnt).removeClass('bounceInLeft');
          
        }
        cnt.closest('#platformInterfaceWrp').addClass('activeThemeEditing');
      }else{
          $('i', this).removeClass('fa-minus').addClass('fa-plus');
          if(isMobile==null){
           $('#toolboxPlatform', cnt).removeClass('animated bounceOutLeft bounceInLeft');
           }
           cnt.closest('#platformInterfaceWrp').removeClass('activeThemeEditing');
          
      }
      $(this).siblings('.listing').slideToggle();


    });

    $('.hasTagCloud').each(function(index, el) {
      $(this).css({fontSize:Math.floor((Math.random()*36)+12)})
    });



  $('body').on('click','#usergrouplinkforadd',function(){   
      $('#gname').val('');
      $('#grpDescription').val('');
       $('#mctag .addToGrpWrap .dropDownTarget select option').removeAttr("selected");
       $(this).closest('.groupinsertWrapper').find('.creatGWrap').addClass('saveFilterNow');
        $('#gname').val('');
        $('#grpDescription').val('');
        $('.grpWrapperBox').css('min-height', '150px');

    });

  $('body').on('click', '#saveGrpName',function(){
	  
    var mainParentEl = $(this).closest('#hashTagSelectedWrapper');
    var parentEl = $(this).closest('li');
    if(mainParentEl.attr('id')=='hashTagSelectedWrapper'){
      var groupname = parentEl.find('input[name="filterName"]').val();
      var groupdiscription = parentEl.find('#grpDescription').val();
   
    }else{    	
        var parentThis = $(this).closest('.grpWrapperBox');
        var groupname = $('#gname', parentThis).val();
        var groupdiscription = $('#grpDescription', parentThis).val();
        var ugcat = $("#ugcat").val();
    }      

       
        var thisEl = $(this);
            if(groupname=='') {
                 $messageError("Group should not be empty");
                 return false;
            }            
             if(groupdiscription==''){
                 $messageError("Group description should not be empty");
                     return false;
            }  
            
                $.ajax({
                    type : "POST",
                    dataType : 'json',
                    url : BASE_URL + '/admin/usergroup/addgroup',
                    data:{'groupname':groupname,'discription':groupdiscription,'ugcat':ugcat},   
                    success : function(response) {                                      
                       $messageSuccess("Added successfully"); 
                      
                        $('#groupuserinsert .noFound').remove();                       
                        thisEl.siblings('.cancelSaveFilter').click();                       
                        if(mainParentEl.attr('id')=='hashTagSelectedWrapper'){
                        	parentEl.find('select option').removeAttr('selected');
                        	parentEl.find('select option:first').after('<option value="'+response.content+'" selected="selected">'+groupname+'</option>');
                        	 $select(parentEl.find('select'));
                        	 parentEl.find('#addgroupBtn').trigger('click');
                         }else{                        	
                        	 $('#grpselect').append('<option value="'+response.content+'" selected="selected">'+groupname+'</option>');
                        	 $select('#grpselect');
                        	 $('#addgroupBtn').trigger('click'); 
                         }
                       
                        $('.goupuser, input[name="goupuserid"], #grpchkall, .goupusermain').prop('checked', false);
                        $('.dropDown').removeClass('on');
                    },
                    error : function(error) {
                    $messageError("Some problems have occured. Please try again later: "+ error);
                       
                    }
                    
                    });
                return false;

  });
  
  
  $('body').on('click', '#saveGrpName2',function(){	  
	  
	    var mainParentEl = $(this).closest('#hashTagSelectedWrapper');
	    var parentEl = $(this).closest('li');
	    if(mainParentEl.attr('id')=='hashTagSelectedWrapper'){
	       var groupname = parentEl.find('input[name="filterName"]').val();
	        var groupdiscription = parentEl.find('#grpDescription').val();
	   
	    }else{    	
	        var parentThis = $(this).closest('.grpWrapperBox');
	        var groupname = $('#gname', parentThis).val();
	        var groupdiscription = $('#grpDescription', parentThis).val();
	        var ugcat = $("#ugcat").val();
	    }	       
	        var thisEl = $(this);	            
	            if(groupname=='') {
	                 $messageError("Group should not be empty");
	                 return false;
	            }            
	             if(groupdiscription==''){
	                 $messageError("Group description should not be empty");
	                     return false;
	            }  
	            
	                $.ajax({
	                    type : "POST",
	                    dataType : 'json',
	                    url : BASE_URL + '/admin/usergroup/addgroup',
	                    data:{'groupname':groupname,'discription':groupdiscription,'ugcat':ugcat},   
	                    success : function(response) {                                      
	                       $messageSuccess("Added successfully");    
	                      
	                        $('#groupuserinsert .noFound').remove();
	                        //$('.dropDown').removeClass('on');
							//$('#dropDownList').removeClass('on');
	                       thisEl.siblings('.cancelSaveFilter').click();  
	                       
	                        if(mainParentEl.attr('id')=='hashTagSelectedWrapper'){
	                        	parentEl.find('select option').removeAttr('selected');
	                        	parentEl.find('select option:first').after('<option value="'+response.content+'" selected="selected">'+groupname+'</option>');
	                        	 $select(parentEl.find('select'));
	                        	 parentEl.find('#addgroupBtn').trigger('click');
	                         }else{
	                        	
	                        	 $('#grpselect2').append('<option value="'+response.content+'" selected="selected">'+groupname+'</option>');
	                        	
	                        	 $select('#grpselect2');
	                        	  $('#addgroupBtn2').trigger('click');
	                         }
	                       
	                        $('.goupuser, input[name="goupuserid"], #grpchkall, .goupusermain').prop('checked', false);
	                        $('.dropDown').removeClass('on');
	                    },
	                    error : function(error) {
	                    $messageError("Some problems have occured. Please try again later: "+ error);
	                       
	                    }
	                    
	                    });
	                return false;

	  });

$('body').on('click','#seeallsocialsharedpost',function(e){
     e.stopPropagation();
    
     $.ajax({
            type : "POST",
            dataType : 'html',
            url : BASE_URL + '/admin/reporting/socialsharedposts',         
            data : {'groupid': ''},                     
            success : function(response) { 
               
                 $('#socialusers').html(response);   
                 var abc = $('#socialusers').offset().top;     
               $('html,body').animate({ scrollTop: abc }, 'slow', function () {
                           });      
            },
            error : function(error) {
             $messageError("Some problems have occured. Please try again later: "+ error);
          
            }
      });
});


$('body').on('click','#addgroupBtn',function(e){ 
    
    var taguser = $('#mctag');
    var mainParentEl = $(this).closest('#hashTagSelectedWrapper');
    var parentEl = $(this).closest('#taguser');

    var parentEl2 = $(this).closest('.dataWrpCheckbox');

    if(mainParentEl.attr('id')=='hashTagSelectedWrapper'){
   	   var groupid   = parentEl.find('#grpselect option:selected').val();
   	   var gaction    = parentEl.find('#action2').val();
   	   var chktype    = parentEl.find('.goupusermain2[value="allInResults"]').is(':checked');    
    }else{
   	 
   	   var parentThis = $(this).closest('.grpWrapperBox');
   	   var groupid    = $('#grpselect option:selected', parentThis).val();
   	   var gaction    = $('#action2').val();
   	   var chktype    = $('.goupusermain[value="allInResults"]').is(':checked'); 
    }

    if($('#action2').val()=="scoringandleagues")
    {        
       var gaction ='scoringandleagues';
       var chktype = false; 
    }
    

  
    var chktypepage    = $('.goupusermain[value="allOnPage"]').is(':checked');
    var provider   = $('#provider').val();
    var continentcode = $('#continentcode').val();
    var category = $('#category').val();
    var cattype = $('#cattype').val();   
    var tag = $('#hellotags').val();     
    var tag1  = typeof (tag) != 'undefined' ? tag : '';

    var caller = $('#userdetails').val();  
    var callingpage  = typeof (caller) != 'undefined' ? caller : '';
   
    if(groupid==0){
   	 $messageError("Please select a user set");
   	 return false;
    }
 
    if($('.goupuser').attr('checked')==false) {
   	 $messageError("Please select in result or result onpage");
   	 return false;
    }  
     var uid = [];
     if(callingpage=='yes')
     {
       uid.push($('#singleaddgrp').val()); 
     } else 
     {
     
       $('input:checkbox[name=goupuserid]').each(function() {       
           if($(this).is(':checked')){             
                uid.push($(this).val());
           }            
       });
     } 

    //console.log(uid); return false;
    $.ajax({
           type : "POST",
           dataType : 'json',
           url : BASE_URL + '/admin/usergroup/usersgroupstore',         
           timeout : 3000,            
           data : {'groupid': groupid,'uid': uid,'masterchk':chktypepage,'provider':provider,'cat':gaction,'continentcode':continentcode,'category':category,'cattype':cattype,'chktypew':chktype,'tag':tag},                     
           success : function(response) { 
          
              $('.dropDown').removeClass('on');
              $messageSuccess("Added successfully");
              $('input:checkbox[name=goupuserid]').each(function() {       
              $(this).attr('checked', false);
            });
              if($('#quickchkall').is(':checked')){
               $('#quickchkall').attr('checked', false);
              }
              

           },
           
           error : function(error) {
            $messageError("Some problems have occured. Please try again later: "+ error);
         
           }
           
           });
   });


 $('body').on('click','#addgroupBtn2',function(e){ 

     e.stopPropagation();
     e.preventDefault();
     var taguser = $('#mctag');
     var mainParentEl = $(this).closest('#hashTagSelectedWrapper');
     var parentEl = $(this).closest('#taguser');
    

     if(taguser.attr('id')=='mctag'){
      
       var tag = [];
       var title = [];
       var company = [];
       var groupid1   = $('#grpselect2 option:selected').val();

    

    if(groupid1==''){
         $messageError("Please select a user set");
         return false;
     }
    
       $('input:checkbox[name=tag]').each(function() { 
         
            if($(this).is(':checked')){             
                 tag.push($(this).val());

            }            
        });


       $('input:checkbox[name=title]').each(function() { 
            if($(this).is(':checked')){             
                 title.push($(this).val());
            }            
        });
       $('input:checkbox[name=company]').each(function() { 
            if($(this).is(':checked')){             
                 company.push($(this).val());
            }            
        });
      
         $.ajax({
                    type: "POST",
                    dataType: 'json',
                    data: {
                        'tag': tag,
                        'title': title,
                        'company': company,
                        'groupid': groupid1
                    },
                     url : BASE_URL + '/admin/usergroup/addusersgrouptag',
                    success: function(response) {
                       
                        //$messageSuccess(response.message);
                        $messageSuccess('Added successfully');
                        $('.addToGrpWrap input').val('');
                        $('.addToGrpWrap textarea').val('');                      
                       // $('#mctag select option').removeAttr('selected');
                        $('.addToGrpWrap').removeClass('on');
                        $('#mctag .addToGrpWrap .dropDownTarget').addClass('disabled');
                        $('#mctagcontent input:checkbox').attr('checked', false);     
                        //thisEl.dialog("close");
                    }
                });

        return false;
     }
     
    
    	 
    	   var parentThis = $(this).closest('.grpWrapperBox');
    	   var groupid    = $('#grpselect2 option:selected', parentThis).val();
    	   var gaction    = $('#action2').val();
    	   var chktype    = $('.goupusermain[value="allInResults"]').is(':checked'); 
    

     if($('#action2').val()=="scoringandleagues")
     {        
        var gaction ='scoringandleagues';
        var chktype = false; 
     }
     

   
     var chktypepage    = $('.goupusermain[value="allOnPage"]').is(':checked');
     var provider   = $('#provider').val();
     var continentcode = $('#continentcode').val();
     var category = $('#category').val();
     var cattype = $('#cattype').val();   
     var tag = $('#hellotags').val();     
     var tag1  = typeof (tag) != 'undefined' ? tag : '';

     var caller = $('#userdetails').val();  
     var callingpage  = typeof (caller) != 'undefined' ? caller : '';
    
     if(groupid==0){
    	 $messageError("Please select a user set");
    	 return false;
     }
  
     if($('.goupuser').attr('checked')==false) {
    	 $messageError("Please select in result or result onpage");
    	 return false;
     }  
      var uid = [];
      if(callingpage=='yes')
      {
        uid.push($('#singleaddgrp').val()); 
      } else {
        $('input:checkbox[name=goupuserid]').each(function() { 
            if($(this).is(':checked')){             
                 uid.push($(this).val());
            }            
        });
      } 
     $.ajax({
            type : "POST",
            dataType : 'json',
            url : BASE_URL + '/admin/usergroup/usersgroupstore',         
            timeout : 3000,            
            data : {'groupid': groupid,'uid': uid,'masterchk':chktypepage,'provider':provider,'cat':gaction,'continentcode':continentcode,'category':category,'cattype':cattype,'chktypew':chktype,'tag':tag},                     
            success : function(response) { 
            	
               $('.dropDown').removeClass('on');
               $messageSuccess("Added successfully");                 
            },
            
            error : function(error) {
             $messageError("Some problems have occured. Please try again later: "+ error);
          
            }
            
            });
    });

 $('body').on('click','#quickgroupBtn',function(e){
	 e.stopPropagation();
     e.preventDefault();
     
     var cat ='';
     var gender ='';
     var age1 ='';
     var age2 ='';
     var chktypepage1 ='';
     
     var groupid = $('#grpselect option:selected').val();
     if(groupid==0){
    	 $messageError("Please select a user set");
    	 return false;
     } 
     var chbosize = $('input:checkbox[name=goupuserid]:checked').size();		
     if(chbosize==0) {
    	 $messageError("Please select in result or result onpage");
    	 return false;
     }  
     
     var chktypepage    = $('.quickuser[value="quickuser"]').is(':checked');
      chktypepage1  = typeof (chktypepage) != 'undefined' ? chktypepage : '';
     
      if(chktypepage1)
      {  	 
	      cat = $('#qcat').val();
	      gender = $('#qgender').val();
	      age1 = $('#qage1').val();
	      age2 = $('#qage2').val();
      }
      
     var uid = [];
     if(chktypepage1 ==''){
    	 //alert(chktypepage1);
       $('input:checkbox[name=goupuserid]').each(function() {
           if($(this).is(':checked')){             
                uid.push($(this).val());
           }            
       });
     }
     
     $.ajax({
         type : "POST",
         dataType : 'json',
         url : BASE_URL + '/admin/usergroup/usersgroupquick',         
         timeout : 3000,            
         data : {'groupid': groupid,'uid': uid,'cat':cat,'gender':gender,'age1':age1,'age2':age2,'chk':chktypepage1},                     
         success : function(response) {          	
            $('.dropDown').removeClass('on');
            $messageSuccess("Users saved to group successfully");                 
         },
         
         error : function(error) {
          $messageError("Some problems have occured. Please try again later: "+ error);
       
         }
         
         });
     
	 
 });
   $('body').on('click', '.goupusermain', function(e){
            var thisCheckbox = $(this);
            var tblecheckbox=  thisCheckbox.closest('table').find('tbody tr td input[type="checkbox"]');
           //var tblecheckbox=  $('.reportingTable  tbody tr td input[type="checkbox"]');
      			var selval = $('#countreport').val();				
      			$('.dropDownTarget').removeClass('disabled');


            if(thisCheckbox.is(':checked')==true){

              if(thisCheckbox.val()=='allInResults'){    
                  $selmsgrep(selval,selval);
                   tblecheckbox.attr('checked', true).attr('disabled', 'disabled');
                  //thisCheckbox.prop('checked', false);

                }else{         
                    thisCheckbox.prop('checked', false);     
                    tblecheckbox.attr('checked', true).removeAttr('disabled');
                    $('.grpTopTabel .dropDownTarget').removeClass('disabled');
                      
                    if($('.goupusermain[value="allOnPage"]').is(':checked')){
                        var uid = '';   
                         if($('.goupuser').prop('checked'))
                         {           
                             var uid = [];
                             $('input:checkbox[name=goupuserid]').each(function() {
                                 if($(this).is(':checked')){             
                                      uid.push($(this).val());
                                 }            
                             });
                         }        
                      }
                    
                    uidlen = uid.length;           
                    $selmsgrep(uidlen,selval);
                } 

            }else{

                    tblecheckbox.attr('checked', false).removeAttr('disabled');
                    $('.grpTopTabel .dropDownTarget').addClass('disabled');
                     $selmsgrep(0,selval);
              
            

              }


        });

  
    $('body').on('click', '.goupuser', function(){
         var thisCheckbox = $(this);
        var parentTable =  thisCheckbox.closest('table')
        var tblecheckbox = parentTable.find('.goupusermain[value="allOnPage"]');
        var selval = $('#countreport').val();			
        var totalCheckBox = $('tbody input[type="checkbox"]', parentTable).size();
        var totalChecked = $('tbody input[type="checkbox"]:checked', parentTable).size();
        $selmsgrep(totalChecked,selval);
        if(totalChecked==0){
    		$('.grpTopTabel .dropDownTarget').addClass('disabled');
    	}else{
    		
    		$('.grpTopTabel .dropDownTarget').removeClass('disabled');
    	}        
        if(thisCheckbox.is(':checked')==false){
              tblecheckbox.attr('checked', false);
        }else{        	
            if(totalCheckBox==totalChecked){
              tblecheckbox.attr('checked', true);  
            }
        }
    });
    


    //apply limit on reporting users

    $('body').on('click','#viewmorereport',function(){
            var thisEl  = $(this);
            var table  = thisEl.closest('table')
            data    =   "provider="+ $(this).attr('provider' )+"&limit="+ $(this).attr('limit' )+"&offset="+ $(this).attr('offset' )+"&calling=countryusers&continentcode="+ $(this).attr('provider' ) + "&cattype="+ $(this).attr('cattype' ) + "&category="+ $(this).attr('category').replace(/&/g,'%26');
                    
            url     =   BASE_URL+"/admin/reporting/"+$(this).attr('action');
            $.ajax({                                      
	              url: url,                  
	              data: data,                       
	              type:'post',
	              beforeSend:function(){            
	              $loader();
	             },   
	             success: function(data)  
	             {   
	                 $removeLoader();
	                 thisEl.closest('tr').remove();
	                 table.find('tbody').append(data);
	                 var totalCheckBoxww = $('#countreport').val();
	                 var totalCheckedww = $('.reportingTable input[type="checkbox"]').size();
	                // $('.pagemsgrep').html(' Showing ' + totalCheckedww + ' of ' +totalCheckBoxww);
	                 if(totalCheckBoxww > totalCheckedww)
	                 $('.reportingTable .pagemsgrep').html(' Showing ' + totalCheckedww + ' of ' +totalCheckBoxww);
	                 else
	                	 $('.reportingTable .pagemsgrep').html(' Showing ' + totalCheckBoxww + ' of ' +totalCheckBoxww);
	                if($('.goupusermain[value="allInResults"]').is(':checked')){	                	
	                    table.find('tbody input[type="checkbox"]').attr('checked', true).attr('disabled', 'disabled');   
	                }else{
	                      table.find('tbody input[type="checkbox"]').removeAttr('disabled');
	                      $('.goupusermain').attr('checked', false);
	                }                        
	             }
            });
     });
    
    // Delete group user function
    
    $selmsgrep = function(totalChecked,selval){
    	if(totalChecked!=0){
    	 //$('#selmsgrep').html('You have selected: '+totalChecked+ ' of '+selval);    		
    	}else{
    		 $('#selmsgrep').html('Total : '+selval);
    	}
    }

    $('body').on('click','.reportingTable tbody tr',function(e){ 
    	$(this).find("input[type='checkbox']").trigger('click'); 
    });
})


function chartforcategories(cdata,categories,id,heading,name,text,action,internalid,formater,catarrpoint)
{
  catarrpoint  = typeof (catarrpoint) != 'undefined' ? catarrpoint : '';
  var count = 0;
  var totlen = (cdata.length);
        $('#'+id).highcharts({
    
            chart: {
                type: 'column'
            },
    
            title: {
                text: heading
            },
    
            xAxis: {

                categories: categories,
                labels: {
                    rotation: 0,
                    align: 'right',
                    style: {
                        fontSize: '9px',
                        fontFamily: 'Verdana, sans-serif',
                        backgroundColor:'rgba(255, 255, 255, 0.1)'
                    },
                    
                   
                }
            },
    
            yAxis: {
                allowDecimals: false,
                min: 0,
                title: {
                    text: text
                }
            },
    
            tooltip: {

                formatter: function() {
                    //return ' '+ catarrpoint[this.series.data.indexOf( this.point )] +' <br/>'+ this.series.name +': '+ this.y +'<br/>'+ 'Total Users: '+ this.point.stackTotal;
                    return ' '+ catarrpoint[this.series.data.indexOf( this.point )] +' <br/>'+ this.series.name +': '+ this.y +'<br/>';    

                }
            },
    
            plotOptions: {
                column: {
                    cursor:'pointer',
                   // stacking: 'normal',
                    events : {
                      legendItemClick: function () {
                        if(this.visible== false)
                          count--;
                        if((count+1)==totlen)
                              return false;
                        if(this.visible== true)
                              count++;

                      }
                    },
                    point: {
                      events: {
                          
                          click: function(e) 
                          {
                              sendcat = catarrpoint[this.x];
                              data    =   "cattype="+this.series.name+"&category="+ sendcat.replace(/&/g,'%26')  ;
                              url     =   BASE_URL+"/admin/reporting/"+action;                        
                              $.ajax({                                      
                                  url: url,                 
                                  data: data,                      
                                  type:'post',                         
                                 beforeSend:function(){
                                    $('.loaderOverlay').remove();
                                    $('#'+internalid).html('');
                                     $loader();
                                 },               
                                 success: function(data)        
                                 {
                                    res = data.split('~');

                                    $removeLoader();
                                    $scrolling('#'+internalid); 
                                    if(data!='')
                                    {   
                                        $("#"+internalid).html(res[0]);                                   
                                    }
                                    else
                                    {
                                        updateTips(" Invitation sent successfully");
                                    }
                                    
                                 }
                              });
                              //location.href = e.point.url;
                              e.preventDefault();
                          },                        
                      },
                    } 
                }
            },
    
            series: cdata
        });

}
function internalchartforbrowsersproviders(cdata,categories,id,heading,name,text,action,internalid,formater)
{
   $('.contryopenonclick').show();
    var colors = Highcharts.getOptions().colors,
        categories = categories, // ['MSIE', 'Firefox', 'Chrome', 'Safari', 'Opera'],
        name = name,
        data = cdata
    

    var chart = $('#'+id).highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: heading
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            categories: categories
        },
        yAxis: {
            title: {
                text: text
            }
        },
        plotOptions: {
            column: {
                cursor: 'pointer',
                colorByPoint: true,
                point: {
                    events: {
                        click: function(e) {
                            var drilldown = this.drilldown;
                            //alert(this.category);
                            data    =   "continentcode="+ this.category +"&calling=countryusers" ;
                            url     =   BASE_URL+"/admin/reporting/"+action;
                            $.ajax({                                      
                              url: url,                  //the script to call to get data          
                              data: data,                        //you can insert url argumnets here to pass to api.php
                              type:'post',                            
                             beforeSend:function(){
                                $('#countryusers').html('');
                                $loader();
                             },               //data format    
                             success: function(data)          //on recieve of reply
                             {
                               if(data!='')
                                {   
                                    $removeLoader();
                                    $("#countryusers").html(data);                                    
                                     $scrolling('#countryusers');                                   
                                }                            
                             }
                            });
                            //location.href = e.point.url;
                            e.preventDefault();                        
                            if (drilldown) { // drill down
                              //  setChart(drilldown.name, drilldown.categories, drilldown.data, drilldown.color);
                            } else { // restore
                              //  setChart(name, categories, data);
                            }
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                   // color: colors[0],
                    style: {
                        fontWeight: 'bold'
                    },
                    formatter: function() {
                        return this.y +' ';
                    }
                }
           
            }
        },
        tooltip: {
            formatter: function() {
                var point = this.point,
                    s = this.x +' : <b>'+ this.y +' '+ formater+'</b><br/>';
                if (point.drilldown) {
                    s += ' view '+ point.category +' versions';
                } else {
                    s += '';
                }
                return s;
            }
        },
        series: [{
            showInLegend: false,
            name: name,
            data: data,
        }],
        exporting: {
            enabled: true
        }
    })
    .highcharts(); // return chart
}

function chartforbrowsersproviders(cdata,categories,id,heading,name,yAxistitle,action,internalid,formater,redirectto,redirectids,userImages,dbdesc,xAxistitle)
{
    redirectto  = typeof (redirectto) != 'undefined' ? redirectto : '';
    redirectids = typeof (redirectids) != 'undefined' ? redirectids : '';
    userImages  = typeof (userImages) != 'undefined' ? userImages : '';
    dbdesc      = typeof(dbdesc) != 'undefined' ? dbdesc : '';
    xAxistitle  = typeof(xAxistitle) != 'undefined' ? xAxistitle : '';
    
    //console.log(userImages); return false;
    var colors  = Highcharts.getOptions().colors,
        categories = categories, // ['MSIE', 'Firefox', 'Chrome', 'Safari', 'Opera'],
        name = name,
        data = cdata
  

    var chart = $('#'+id).highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: heading
        },
        subtitle: {
            text: ''
        },
        xAxis: {
          useHTML : true,
            categories: categories,
            title: {
                text: xAxistitle
            }
        },
        yAxis: {
            title: {
                text: yAxistitle
            }
        },
        plotOptions: {
            column: {
                cursor: 'pointer',
                colorByPoint: true,
                point: {
                    events: {
                        click: function(e) {
                            var drilldown = this.drilldown;

                            if(redirectto!='')
                            {
                              red_url = BASE_URL+'/'+redirectto+'/'+redirectids[this.x];
                              var win=window.open(red_url, '_blank');
                              win.focus();
                            }
                            if(action!='')
                            { 
                              if(action=='socialusers' || action=='postvisiters') prov = redirectids[this.x]; else prov = this.category;

                              if(action =='eachdaylogins'){
                                prov = prov+' '+heading;
                              }

                              data    =   "provider="+ prov  ;
                      
                              url     =   BASE_URL+"/admin/reporting/"+action;
                              $.ajax({                                      
                                url: url,                  //the script to call to get data          
                                data: data,                        //you can insert url argumnets here to pass to api.php
                                type:'post',
                               // dataType: 'json', 
                               beforeSend:function(){
                                 
                                  $('#'+internalid).html('');
                                   $loader();
                               },   //data format    
                               success: function(data)  //on recieve of reply
                               {   
                                    $removeLoader();
                                    $scrolling('#'+internalid);                            

                                    if( action!="countrycontainer")
                                    {   
                                        $("#"+internalid).html(data);                                      
                                    }
                                    else
                                    {
                                        $scrolling('#countryusers'); 
                                        var param=data.split("~");
                                        internalchartforbrowsersproviders(JSON.parse(param[1]),JSON.parse(param[0]),internalid,param[2]+': User breakdown by country ','platform users in '+param[2]+' Continent','Countries',action,internalid,formater);
                                    }
                               }
                              });
                            }
                            //location.href = e.point.url;

                            e.preventDefault();
                        
                            if (drilldown) { // drill down
                              //  setChart(drilldown.name, drilldown.categories, drilldown.data, drilldown.color);
                            } else { // restore
                              //  setChart(name, categories, data);
                            }
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                   // color: colors[0],
                    style: {
                        fontWeight: 'bold'
                    },
                    formatter: function() {
                        return this.y +' ';
                    }
                }
           
            }
        },
        tooltip: {
            useHTML: true,
            backgroundColor: '#333333',
            borderColor: '#333333',
            formatter: function() {
              var point = this.point;
              var s = '';

                if(redirectto=='' && action =='postvisiters')
                {

                   catindex = categories.indexOf(this.x);
                   
                      s += '<div class="chartTipUser"><img  src="'+IMGPATH+'/users/small/'+userImages[catindex]+'" width="35" height="35"></div>';
                      s += '<div class="chartTipUserDetails"><div>'+dbdesc[catindex]+'</div>';
                      s += '<div>'+ this.y +' '+ formater+'</div></div>';

                } 
                else if(redirectto!='' || action =='socialusers')
                {
                   catindex = categories.indexOf(this.x);
                   
                   if(action =='socialusers') { if(this.y>1) formater ='shares'; else  formater ='share'; }
                   
                   if(redirectto=='dbee' || action =='socialusers'){
                      s += '<div class="chartTipUser"><img  src="'+IMGPATH+'/users/small/'+userImages[catindex]+'" width="35" height="35"></div>';
                      s += '<div class="chartTipUserDetails"><div>'+dbdesc[catindex]+'</div>';
                      s += '<div>'+ this.y +' '+ formater+'</div></div>';

                   } else {
                      if(userImages[catindex]!='')
                      s += '<div class="chartTipUser"><img  src="'+IMGPATH+'/users/small/'+userImages[catindex]+'" width="35" height="35"></div>';
                      s += '<div  class="chartTipUserDetails"><div>'+this.x+'</div><div> '+formater+': '+this.y+'</div></div>';
                      if (point.drilldown) {
                          s += ' view '+ point.category +' versions';
                      } else {
                          s += '';
                      }
                    }
                } 
                else if(action =='deviceusers')
                {
                  
                   if(this.y>1) formater ='users'; else  formater ='user'; 
                   
                    s += '<span style="color:#fff">'+this.x +' : <b>'+ this.y +' '+ formater+'</span></b><br/>';
                    if (point.drilldown) {
                        s += '<span style="color:#fff"> view '+ point.category +' versions</span>';
                    } else {
                        s += '';
                    }
                } 
                else 
                {
                    catindex = categories.indexOf(this.x);
                    //if(action =='eachdaylogins') formater = formater+' '+heading;

                    
                   
                    if(action =='eachdaylogins'){
                      s += '<span style="color:#fff">Total : <b>'+ this.y +' </span></b><br/>';
                      s += '<div style="color:#fff"> Unique  : '+userImages[catindex]+'</div>';
                    }
                    else
                    {
                      s += '<span style="color:#fff">'+this.x +' : <b>'+ this.y +' '+ formater+' </span></b><br/>';
                    }
                    
                    if (point.drilldown) {
                        s += '<span style="color:#fff"> view '+ point.category +' versions</span>';
                    } else {
                        s += '';
                    }
                }
                return s;
            }
        },
        series: [{
            showInLegend: false, 
            name: name,
            data: data
            
        }],
        exporting: {
            enabled: true
        }
    })
    .highcharts(); // return chart
}

// Start of Email like gmail, yahoo

function piechartproviders(cdata,id,heading,titleName,action,internalid,extraParam)
{
    var count = 0;   
    var totlen = (cdata.length); 
    $('#'+id).highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,           
            spacingTop: 25,
            spacingLeft: 20
        },        
        title: {
            text: heading
        },
        tooltip: {
          pointFormat: '{series.name}: <b>{point.percentage}%</b>',
          percentageDecimals: 1,
      formatter: function() {
        var y = this.y;
        var p = Math.round(this.percentage*100)/100;
         return '<b>'+ this.point.name +'</b>: ' + y + ' (' + p.toFixed(2) + '%)';
      }
        },
    legend:{
      "layout":"horizontal",
       "align": "right",
      "style":{
       "left":"auto",
       "bottom":"auto",
       "right":"auto",
       "top":"20px"
      }
     },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    color: '#000000',
                    connectorColor: '#000000',
                    formatter: function() {
                        return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
                    }
                },
                point: {
                    events: {
                        legendItemClick: function () {
                           
                            if(this.visible== false)
                                count--;
                            if((count+1)==totlen)
                                return false;
                            if(this.visible== true)
                                count++;
                         }
                    }    
                },
        
                showInLegend: true
            }
        },
        series: [{
            type: 'pie',
            name: titleName,
            point: {
            events: {
                click: function(e) {
                    //this.slice();
                    //console.log(e);
                  //  alert(e.point.name);
                  if(action!=''){
                    data    =   "provider="+ e.point.name +"&require="+extraParam ;
                    url     =   BASE_URL+"/admin/reporting/"+action;
                    $.ajax({                                      
                      url: url,                  //the script to call to get data          
                      data: data,                        //you can insert url argumnets here to pass to api.php
                      type:'post',
                     // dataType: 'json', 
                     beforeSend:function(){
                        $('#'+internalid).html('');
                        $loader();
                     },               //data format    
                     success: function(data)          //on recieve of reply
                     {  
                        $removeLoader();
                         $scrolling('#'+internalid);

                        if(data!='')
                        {   
                            $("#"+internalid).html(data);
                        }
                        else
                        {
                            updateTips(" Invitation sent successfully");
                        }
                        
                     }
                    });
                    //location.href = e.point.url;

                    e.preventDefault();
                    }
                }
             }
            },
            data: cdata
        }]
    });
}

function chartforemailproviders(cdata,id,heading)
{
    var count = 0;

    var totlen = (cdata.length);
    $('#'+id).highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
			backgroundColor: '#FFFFFF'
        },
        title: {
            text: heading
        },
    
        tooltip: {
    	    pointFormat: '{series.name}: <b>{point.percentage}%</b>',
        	percentageDecimals: 1,
			formatter: function() {
				var y = this.y;
				var p = Math.round(this.percentage*100)/100;
				 return '<b>'+ this.point.name +'</b>: ' + y + ' (' + p.toFixed(2) + '%)';
			}
        },
		legend:{
		  "layout":"horizontal",
		  "style":{
			 "left":"auto",
			 "bottom":"auto",
			 "right":"auto",
			 "top":"auto"
		  }
	   },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    color: '#000000',
                    connectorColor: '#000000',
                    formatter: function() {
                        return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
                    }
                },
                point: {
                    events: {
                        legendItemClick: function () {
                           
                            if(this.visible== false)
                                count--;

                            if((count+1)==totlen)
                                return false;
                            

                            if(this.visible== true)
                                count++;

                            //alert(count);
                         }
                    }    
                },
				
                showInLegend: true
            }
        },
        series: [{
            type: 'pie',
            name: 'Browser share',
            point: {
            events: {
                click: function(e) {
                    //this.slice();
                    //console.log(e);
                  //  alert(e.point.name);
                    data    =   "provider="+ e.point.name +"&require=reportofemail" ;
                    url     =   BASE_URL+"/admin/reporting/emailproviders";
                    $.ajax({                                      
                      url: url,                  //the script to call to get data          
                      data: data,                        //you can insert url argumnets here to pass to api.php
                      type:'post',
                     // dataType: 'json', 
                     beforeSend:function(){
                        $('#searchprovidercontainer').html('');
                        $loader();
                     },               //data format    
                     success: function(data)          //on recieve of reply
                     {  
                        $removeLoader();
                         $scrolling('#searchprovidercontainer');

                        if(data!='')
                        {   
                            $("#searchprovidercontainer").html(data);

                           


                        }
                        else
                        {
                            updateTips(" Invitation sent successfully");
                        }
                        
                     }
                    });
                    //location.href = e.point.url;

                    e.preventDefault();
                }
             }
            },
            data: cdata
        }]
    });
}


