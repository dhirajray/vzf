
    Dropzone.options.myAwesomeDropzone = false;
    Dropzone.autoDiscover = false;
  $(document).ready(function(){ 

    $('select.sidetemp, select.selectoptEmailtemp').change(function(){
        var w1 = $('.emailtemplate').width();
        var w2 = $('.sidetemplate').width();
        $('.formedit').css({width:(w1+w2+5)});

    });


    $heightFrame = function(iframe){
      setTimeout(function(){
      iframe.closest('.apperanceContainer').height(iframe.contents().find("html").height());
    }, 100);
    }
   
    $('#editIframe').load(function(){
      var iframe = $(this);
      var editBlock =  '.editingActiveBlock';
        $heightFrame(iframe);
        var loadedColor = '';
      
        
        $('.colpicker').colpick({
          layout:'hex',
          submit:0,         
          colorScheme:'dark',
          onBeforeShow:function(){
            $(this).colpickSetColor($(this).siblings('input').val().split('#')[1]);
          },
          onChange:function(hsb,hex,rgb,el,bySetColor) {

            var colorType =  $(el).siblings('input').attr('name');
            $(el).siblings('input').css('background','#'+hex);
            // Fill the text box just if the color was set using the picker, and not the colpickSetColor function.
            if(!bySetColor) $(el).siblings('input').val('#'+hex);
            if(colorType=='background'){  
              var current_td =  iframe.contents().find('.editingActiveBlock').attr('edittype');
              if(current_td=='bodycontent'){$('#bodycontentbacgroColor').val(hex);
              }else if(current_td=='header'){$('#headerbacgroColor').val(hex);} 
              else if(current_td=='contentbody'){$('#contentbodybacgroColor').val(hex);}
              else if(current_td=='footer'){$('#footerbacgroColor').val(hex);
              }                          
              iframe.contents().find(editBlock).css('background-color','#'+hex );

            }else if(colorType=='fontColor'){ 
              var current_td =  iframe.contents().find('.editingActiveBlock').attr('edittype');
              console.log(colorType+' '+current_td+' '+hex+'=====>');
              if(current_td=='bodycontent'){$('#bodycontentfontColor').val(hex);
              }else if(current_td=='header'){$('#headerfontColor').val(hex);} 
              else if(current_td=='contentbody'){$('#contentbodyfontColor').val(hex);}
              else if(current_td=='footer'){$('#footerfontColor').val(hex);
              } 
              iframe.contents().find(editBlock).css('color','#'+hex );
              iframe.contents().find(editBlock+ ' a').css('color','#'+hex );
            }
          }
        });
    
            $('.chooseTexture li a').click(function(e){
        e.preventDefault();
        if($(this).hasClass('noTexture')==true){
          var img  = '';
        }else{
          var img  = $('img', this).attr('src');
        }
         imgUrl = img;
         var current_td =  iframe.contents().find('.editingActiveBlock').attr('edittype');               
                 if(current_td=='bodycontent'){$('#bodycontentTxture').val(imgUrl);
                 }else if(current_td=='header'){$('#headerTxture').val(imgUrl);} else if(current_td=='contentbody'){
                 $('#contentbodyTxture').val(imgUrl);}else if(current_td=='footer'){$('#footerTxture').val(imgUrl);
                 }
         
        iframe.contents().find(editBlock).css('background-image', 'url('+img+')');

      });

      $('.colorPickerWrapper input').focusin(function(event) {
        loadedColor = $(this).val().split('#')[1];
        $(this).siblings('.colpicker').click();
        
      });


      //var logoImg = iframe.contents().find('#bannerHolder').detach();

      $('.bannerCheck .bannerTypeMethod').click(function(){
        var val = $(this).val();
        var toolBoxEditor = $('#toolBoxEditor');
         
        if(val==1){
          $(".selectoptEmailtemp").attr('emailTypelist','admin');
          $(".selectoptEmailtemp option:first").attr('selected','selected');
          $(".bannerCheck").attr('mahvalue','yessel');
          iframe.contents().find('.emailinfochange').show();
          var currtempselect = 'Email body';
          $('#currselection').text(currtempselect);
          $('.msgfortemp').text('Click on the subject box and main body text section respectively to amend the various types of email messages that are sent either by the Admin or from the platform.');
          iframe.contents().find('.emailinfotitle').css("background-color","rgba(204, 204, 204, 0.18)");
          $('select[name="emailtemplateSelect"]').trigger("change");
          toolBoxEditor.removeClass('bounceInLeft animated').addClass('bounceOutLeft animated');

          /***************************************/
          $('.emailtemplate, #resetTemplate').fadeIn();
          $('.sidetemplate').fadeIn();
          iframe.contents().find('.editingBlck').addClass('disabled');
          //iframe.contents().find('[edittype="contentbody"]').attr('contenteditable', 'true');
            iframe.contents().find('.subjectedit').show();
            $('.formedit').show();  
          /***************************************/
           var emailtemptype= $(".selectoptEmailtemp").attr('emailTypelist');
           var emailtempval = $('select[name="emailtemplateSelect"] option:selected').attr('value');
           //var emailtempval = $('select[name="emailtemplateSelect"] option:selected').attr('caseid');
           if(emailtemptype=='admin' && emailtempval==1){          
                iframe.contents().find('#bannerHolder').hide();
                iframe.contents().find('#bannerHolder').attr('alt','');
                iframe.contents().find('#Header').hide();
                iframe.contents().find('.editingBlck').addClass("col-class1")
           }
        }
        if(val==0){
                iframe.contents().find('#bannerHolder').show();
                iframe.contents().find('#bannerHolder').attr('alt','dbee logo');
                iframe.contents().find('#Header').show();
                iframe.contents().find('.editingBlck').removeClass("col-class1");
          iframe.contents().find('.editingBlck').removeClass("col-class1");
          $(".selectoptEmailtemp option:first").attr('selected','selected');
          $(".bannerCheck").attr('mahvalue','nosel');
          var mudefaultmsg = '<div style="height: 100px; font-size: 16px; line-height: 25px;">\
                               MAIL CONTENT GOES HERE. THIS TEXT WILL BE REPLACED BY THE RELEVANT EMAIL CONTENT \
                               DEPENDING ON THE TYPE OF EMAIL BEING SENT OUT FROM THE PLATFORM OR FROM THE ADMIN \
                               AREA.</div>';

          $('.formedit').hide();
          iframe.contents().find('.subjectedit').hide();                     
          iframe.contents().find('#bodyEmailMsg').html(mudefaultmsg);
          iframe.contents().find('.emailinfochange').hide();
          var currtempselect = 'Design';
          $('#currselection').text(currtempselect);
          $('.msgfortemp').text('Click an area on template below to modify. You can change the header, the main body section, the footer and the overall background.');
          iframe.contents().find('.emailinfotitle').css("background-color","transparent");
          toolBoxEditor.show();
          toolBoxEditor.removeClass('bounceOutLeft animated').addClass('bounceInLeft animated');
          $('.emailtemplate, #resetTemplate').fadeOut();
          $('.sidetemplate').fadeOut();
          iframe.contents().find('.editingBlck').removeClass('disabled');
          iframe.contents().find('[edittype="contentbody"]').removeAttr('contenteditable', 'true');
          $heightFrame(iframe);
        }
      });

     $("#bannerTypeMethod").attr('checked', true).trigger('click'); 
     $(".bannerCheck").attr('mahvalue','nosel');
      var myDropzoneBannerval = new Dropzone("#myBanner", {
              url: BASE_URL+'/admin/emailtempsetting/emailbanner',
              maxFiles: 1,
              addRemoveLinks: true,
              uploadMultiple:false,
              parallelUploads: 1,
               acceptedFiles: 'image/jpeg,image/png,image/gif',
            });
    
           myDropzoneBannerval.on("error", function (file, serverFileName) {
              $messageError('Please upload only png, jpg, jpeg, gif file type');
          });
          myDropzoneBannerval.on("maxfilesexceeded", function (file, serverFileName) {

              $messageError('You can upload only 1 file');
          });     
          myDropzoneBannerval.on("success", function (file, serverFileName) {
            var imgUrl = BASE_URL+'/adminraw/img/emailbgimage/'+serverFileName;
              iframe.contents().find('#bannerHolder').attr('src', imgUrl);
              $('#bannerfreshimg').val(imgUrl);
               $heightFrame(iframe);
          });
          myDropzoneBannerval.on("removedfile", function (file) {
            iframe.contents().find('#bannerHolder').attr('src', ''+BASE_URL+'/adminraw/img/bgs/banner-sample.jpg');
             $heightFrame(iframe);

          });

          $('body').on('click','#resetTemplate',function(event) {
              var emailtempidall = $('select[name="emailtemplateSelect"] option:selected').attr('value');
              $.ajax({
                        url: BASE_URL+'/admin/emailtempsetting/emailtempletereset',
                        type: "POST",
                        data: {'etemp_id':emailtempidall},
                        success: function (result) {
                          var resultArr = result.split('~#~');                       
                              iframe.contents().find('#bodyEmailMsg').html('');
                              iframe.contents().find('#subjectEnter').html('');
                             
                            
                               var hrefArray=[];
                               var emailData='';
                        
                                var pattern = /<span contenteditable="false"[^<]/gi;
                                //  alert(resultArr[0]);
                                var filterSpan =  resultArr[0].replace(pattern, '').replace('</span[^<]/gi', '');
                                var filterSpan2 =  resultArr[3].replace(pattern, '').replace('</span[^<]/gi', '');
                                 iframe.contents().find('#subjectEnter').html(filterSpan2);

                              iframe.contents().find('#bodyEmailMsg').html(filterSpan);


                              iframe.contents().find('#bodyEmailMsg a').each(function(index, el) {
                                  hrefArray.push($(this).attr('href'));
                                            $(this).attr('href', '');

                              });
                               emailData =  iframe.contents().find('#bodycontent').html().replace(/\[%%/g, '<span contenteditable="false">[%%').replace(/%%\]/g, '%%]</span>');
                               iframe.contents().find('#bodycontent').html('');
                               iframe.contents().find('#bodycontent').html(emailData);

                                $.each(hrefArray, function(index, el) {                                
                                  iframe.contents().find('#bodyEmailMsg a:eq('+index+')').attr('href', el);
                                });


                              iframe.contents().find('.emailinfochange').text('');
                              iframe.contents().find('.emailinfochange').text(resultArr[1]);
                              
                              $heightFrame(iframe);    
                              $messageSuccess("Email template updated successfully.");                         
                        }
               });
      });

          $('select[name="emailtemplateSelect"]').change(function(event) { 
            var emailtemptype = $('select[name="sideSelect"] option:selected').attr('value');
            var emailtempidall = $('select[name="emailtemplateSelect"] option:selected').attr('value');
            //var emailtempidall = $('select[name="emailtemplateSelect"] option:selected').attr('caseid');
            // dev = 1;pro=226&687  ;sta = 485;alert(emailtempidall);
            if(emailtemptype=='admin' && emailtempidall!=1){
                iframe.contents().find('#bannerHolder').show();
                iframe.contents().find('#bannerHolder').attr('alt','dbee logo');
                iframe.contents().find('#Header').show();
                iframe.contents().find('.editingBlck').removeClass("col-class1");
            }
            if(emailtemptype=='admin' && emailtempidall==1){
                iframe.contents().find('#bannerHolder').hide();
                iframe.contents().find('#bannerHolder').attr('alt','');
                iframe.contents().find('#Header').hide();
                iframe.contents().find('.editingBlck').addClass("col-class1");
            }
            $.ajax({
                        url: BASE_URL+'/admin/emailtempsetting/emailtempletechange',
                        type: "POST",
                        data: {'etemp_id':emailtempidall},
                        success: function (result) {
                          var resultArr = result.split('~#~');                       
                              iframe.contents().find('#bodyEmailMsg').html('');
                              iframe.contents().find('#subjectEnter').html('');
                             
                             // var emailData =  resultArr[0].replace(new RegExp("\\[|$$","g"), "<i>[").replace(new RegExp("$$|\\]","g"), "]</i>");
                             //var placeHolder = {"[%%COMPANY_FOOTERTEXT%%]":"Mike","%AGE%":"26","%EVENT%":"20"};
                               var hrefArray=[];
                               var emailData='';
                           /*  var replacements = {
                                                "\[%%COMPANY_FOOTERTEXT%%\]":"Mantech",
                                                "\[%%dataName%%\]":"kapil kumar",
                                                "\[%%COMPANY_NAME%%\]":"Mantech Globle Solutions",
                                                "\[%%FRONT_URL%%\]":"http://localhost/client-4/home/emailtempsetting"
                                                },*/
                                var pattern = /<span contenteditable="false"[^<]/gi;
                                //  alert(resultArr[0]);
                                var filterSpan =  resultArr[0].replace(pattern, '').replace('</span[^<]/gi', '');
                                var filterSpan2 =  resultArr[3].replace(pattern, '').replace('</span[^<]/gi', '');
                                 iframe.contents().find('#subjectEnter').html(filterSpan2);

                              iframe.contents().find('#bodyEmailMsg').html(filterSpan);


                              iframe.contents().find('#bodyEmailMsg a').each(function(index, el) {
                                  hrefArray.push($(this).attr('href'));
                                            $(this).attr('href', '');

                              });
                               emailData =  iframe.contents().find('#bodycontent').html().replace(/\[%%/g, '<span contenteditable="false">[%%').replace(/%%\]/g, '%%]</span>');
                               iframe.contents().find('#bodycontent').html('');
                               iframe.contents().find('#bodycontent').html(emailData);

                                $.each(hrefArray, function(index, el) {                                
                                  iframe.contents().find('#bodyEmailMsg a:eq('+index+')').attr('href', el);
                                });


                              iframe.contents().find('.emailinfochange').text('');
                              iframe.contents().find('.emailinfochange').text(resultArr[1]);
                              
                              $heightFrame(iframe);                             
                        }
               });


         });
           $('select[name="sideSelect"]').change(function(event){
            var emailtemptype = $('select[name="sideSelect"] option:selected').attr('value');
            var emailtempval = $('select[name="emailtemplateSelect"] option:selected').attr('value');
            if(emailtemptype=='front'){      
                iframe.contents().find('#bannerHolder').show();
                iframe.contents().find('#bannerHolder').attr('alt','dbee logo');
                iframe.contents().find('#Header').show();
                iframe.contents().find('.editingBlck').removeClass("col-class1");
            }
            $('.emailtemplate').fadeIn();
            var emailtemplateside = $('select[name="sideSelect"] option:selected').attr('value');
            $(".selectoptEmailtemp").attr('emailTypelist',emailtemplateside);          
            $.ajax({
               url: BASE_URL+'/admin/emailtempsetting/index',
               type: "POST",
               data: {'areatype':emailtemplateside},
               success: function (result) {
                    $('select[name="emailtemplateSelect"]').html(result);
                    $select('select[name="emailtemplateSelect"]');
                    $('select[name="emailtemplateSelect"]').trigger('change');
               }
            });
          });

       // $('select[name="sideSelect"]').change();

        $('#saveTemplate').click(function(e){
            e.preventDefault();
            var  templatetype =  $(".bannerCheck").attr('mahvalue');
            var mymainformVal = $('#mainForm').serializeArray();
            len = mymainformVal.length,
            dataObj = {};
            for (i=0; i<len; i++) {
              var myVal = mymainformVal[i].value.split('/');
              var ind = myVal.length;
              if(ind > 1){
                myVal = myVal[ind-1];
              }else{
                 myVal = mymainformVal[i].value;
              }
               dataObj[mymainformVal[i].name] = myVal;

               
            } 
            var iframcontentval = iframe.contents().find('#bodyEmailMsg').html();
            var subjectMsgval = iframe.contents().find('#subjectEnter').text();
            var footerMsgval = iframe.contents().find('#footerMsg').text();
            var emailtempidall = $('select[name="emailtemplateSelect"] option:selected').attr('value');
            var bannerTypeMethod = $('input[name=bannerTypeMethod]:checked' ).val();
            var fromname = $('.fromnameval').val();
        
            $.ajax({
                        url: BASE_URL+'/admin/emailtempsetting/emailbannersucces',
                        type: "POST",
                        data: {'firstformval':dataObj,'iframcontentval':iframcontentval,'subjectMsgval':subjectMsgval,
                               'footerMsgval':footerMsgval,'emailtempid':emailtempidall,'templatetype':templatetype,'fromname':fromname},
                        success: function (result) {                               
                            $messageSuccess("Email template updated sucessfully"); 
                        }
               });

        });

        $('.titleEditingType').click(function(event) {
          var toolboxWorkingArea = $('.toolboxMainContainer');
          if($('i',this).hasClass('fa-minus')==true){
            $('i',this).removeClass('fa-minus').addClass('fa-plus');
            toolboxWorkingArea.removeClass('bounceInDown animated').addClass('bounceOutDown animated');         
            toolboxWorkingArea.slideUp();

          }else{
            $('i',this).removeClass('fa-plus').addClass('fa-minus');
            toolboxWorkingArea.slideDown();
            toolboxWorkingArea.removeClass('bounceOutDown animated').addClass('bounceInDown animated');


          }
        });

        $('#footerNotes input').keyup(function(event) {
          var footerText = $(this).val();
          iframe.contents().find('#footerMsg').text(footerText);
          
        });
    }); 


      

      
  });


