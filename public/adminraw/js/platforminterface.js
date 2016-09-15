  var bgImageDrp='';
   var bgImage = '';

    var startColor ='';
    var endColor ='';
    var borderColor ='';
    var borderRadius = 0;
    var borderWidth = 1;
    var buttonTextColor = '#fff';
    var hoverFontColor = '#fff';
    var iconColor = '#fff';

  $(function(){

   


  function rgb2hex(rgb){
   rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
   return "#" +
    ("0" + parseInt(rgb[1],10).toString(16)).slice(-2) +
    ("0" + parseInt(rgb[2],10).toString(16)).slice(-2) +
    ("0" + parseInt(rgb[3],10).toString(16)).slice(-2);
  }



     
     $('#platformInterface').load(function() {
       /* Act on the event */
       /* $('.colorFill[date-type="end"]', parentWin).css({background:btn.fill.startColor});
        $('.colorFill[date-type="borderColor"]', parentWin).css({background:btn.borderColor});
        $('.colorFill[date-type="buttonTextColor"]', parentWin).css({background:btn.color});*/
        
       var iframe = $(this).contents(), iframeH = $(this).height();
        

        $('.colorFill').colpick({
            layout:'hex',
            submit:0,
            colorScheme:'dark',
            color:'#ffffff',
            onChange:function(hsb,hex,rgb,el,bySetColor) {
              var colorType =  $(el).attr('date-type');    
                 startColor = $('.colorFill[date-type="start"]').css('background-color');
                 endColor = $('.colorFill[date-type="end"]').css('background-color');
                 borderColor = $('.colorFill[date-type="borderColor"]').css('background-color');
                 buttonTextColor = $('.colorFill[date-type="buttonTextColor"]').css('background-color');      
                 hoverFontColor = $('.colorFill[date-type="hoverFontColor"]').css('background-color');      
                 iconColor = $('.colorFill[date-type="iconColor"]').css('background-color');

              $(el).css('background','#'+hex); 

              if(colorType=='start'){
                  startColor = '#'+hex;
                  endColor = $('.colorFill[date-type="end"]').css('background-color');
                  if($('#fillColorGradiantInput').is(':checked')!=true){
                    $('input[name="endColor"]', iframe).val(startColor);
                    $('.colorFill[date-type="end"]').css('background','#'+hex); 
                    endColor = startColor;
                  }
                  $('input[name="startColor"]', iframe).val(startColor);

              }else if(colorType=='end')  {
                 endColor = '#'+hex;
                 startColor = $('.colorFill[date-type="start"]').css('background-color');
                 $('input[name="endColor"]', iframe).val(endColor);

              }else if(colorType=='borderColor'){
                borderColor = '#'+hex;
                 $('input[name="borderColor"]', iframe).val(borderColor);
              } 
              else if(colorType == 'buttonTextColor'){
                buttonTextColor  ='#'+hex;
                $('input[name="color"]', iframe).val(buttonTextColor);
              }
              else if(colorType == 'hoverFontColor'){
                hoverFontColor  ='#'+hex;
                $('input[name="hoverFontColor"]', iframe).val(hoverFontColor);
              }
              else if(colorType == 'iconColor'){
                iconColor  ='#'+hex;
                $('input[name="iconColor"]', iframe).val(iconColor);
              }
              $('html[data-type-active="body"] [data-type="body"]', iframe).hide();
              $('.activeBlock .thisActive:not([data-type="body"]):not([data-type="searchHeader"])', iframe).css({background:'linear-gradient(0deg, '+startColor+', '+endColor+')', borderColor:borderColor, color:buttonTextColor});

              $('.activeBlock .thisActive[data-type="body"]', iframe).closest('body').css({background:'linear-gradient(0deg, '+startColor+', '+endColor+')', borderColor:borderColor, color:buttonTextColor});
              $('.activeBlock .thisActive[data-type="searchHeader"] input', iframe).css({background:'linear-gradient(0deg, '+startColor+', '+endColor+')',  color:buttonTextColor});
              $('.activeBlock[inner-type="leftSideActive"] .text, .activeBlock[inner-type="leftSideContent"] .text, .activeBlock[inner-type="rightSideContent"] .text, .activeBlock[inner-type="rightSideTitleActive"] .text, .activeBlock[inner-type="header"] .text', iframe).css({backgroundColor:buttonTextColor, color:buttonTextColor});
              $('.activeBlock .fontIcon', iframe).css({color:iconColor});
            }
          }).mousedown(function(){
            var colorHex = rgb2hex(this.style.backgroundColor);
            var thisType =  $(this).attr('date-type');
            $('html[data-type-active="body"] [data-type="body"]', iframe).show();
            $(this).colpickSetColor(colorHex.split('#')[1]);
          });


        $('input[type=range]').change(function(){
           var rangType = $(this).attr('data-type');
           var value = $(this).val();
           if(rangType=='radius'){
             $('input[name="borderRadius"]', iframe).val(value);
             $('#borderRadiusRow h4 strong').text(value+'px');
           }else if(rangType=='borderWidth'){
              $('input[name="borderWidth"]', iframe).val(value);
              $('#borderWidthRow h4 strong').text(value+'px');
            }
           borderRadius =  $('input[name="borderRadius"]', iframe).val();
           borderWidth =  $('input[name="borderWidth"]', iframe).val();
           console.log(borderWidth)
          $('.activeBlock .thisActive', iframe).css({borderRadius:borderRadius+'px', borderWidth:borderWidth});
        });

       
       $('#resestToDefault').click(function(event) {
         event.stopPropagation();
         var msg = 'Are you sure you want to reset to the default theme?';
        $('#dialogConfirmSetting').remove();
        $('body').append('<div id="dialogConfirmSetting">' + msg + '</div>');

          $("#dialogConfirmSetting").dialog({
              resizable: false,
              title: 'Please confirm',
              modal: true,
              buttons: {
                  "Yes": function(e) {
                    $.post(BASE_URL+'/admin/myaccount/ajaxplateforminterface', {plateformInterface:''}, function(){
                      location.href ='';
                   });
                  }
              }
          });
       
       });
       
        bgImageDrp = new Dropzone("#BgImage", {
              url: BASE_URL+'/admin/myaccount/ajaxbg',
              maxFiles: 1,
              addRemoveLinks: true,
              uploadMultiple:false,
              parallelUploads: 1,
              acceptedFiles: 'image/jpeg,image/png,image/gif',
              thumbnailWidth: iframeH,
              thumbnailHeight: iframeH
            });
    
           bgImageDrp.on("error", function (file, serverFileName) {
              $messageError('Please upload only png, jpg, jpeg, gif file type.');
          });
          bgImageDrp.on("thumbnail", function (file, dataURl) {
                bgImage=dataURl;
               // $('.activeBlock .thisActive', iframe).css({backgroundImage:'url('+dataURl+')', backgroundSize:'cover'});
          });
          bgImageDrp.on("maxfilesexceeded", function (file, serverFileName) {
              $messageError('You can upload only 1 file');
          });
          bgImageDrp.on("processing", function (file, serverFileName) {
             $('.activeBlock .saveEditing', iframe).text('Uploading...').addClass('disabled');
          });
          
          bgImageDrp.on("success", function (file, serverFileName) {
            bgImage = BASE_URL+'/img/'+serverFileName.filename;
            $('input[name="contentbodyTxture"]', iframe).val(bgImage); 
            $('.activeBlock .thisActive', iframe).css({backgroundImage:'url('+bgImage+')', backgroundSize:'cover'});
            $('input[name="bodyBgImg"]', iframe).val("true");
            $('.activeBlock .saveEditing', iframe).text('Save').removeClass('disabled');
             
          });
          bgImageDrp.on("removedfile", function (file) {
            bgImage ='';
             $('input[name="bodyBgImg"]', iframe).val("false");
           $('.activeBlock .thisActive', iframe).css({backgroundImage:'', backgroundSize:'inherit'});
            

          });

            $('#closeImgFile').click(function(e){
              bgImageDrp.removeAllFiles(true);
            });
             

          
           $('body').on('click','.chooseTexture li a', function(e){
            e.preventDefault();
            var bgS='initial';
            var img ='';
            if($(this).hasClass('noTexture')==true){
             if(bgImage!=''){
               img  = bgImage;
               bgS='cover';
               $('input[name="bodyBgImg"]', iframe).val("true");
             }else{
               img  = '';
               $('input[name="bodyBgImg"]', iframe).val("false");
             }            
            }else{
               img  = $('img', this).attr('src');
               $('input[name="bodyBgImg"]', iframe).val("false");
            }
            //if($('input[name="bodyBgImg"]', iframe).val()=='true') bgS='cover';

            $('input[name="contentbodyTxture"]', iframe).val(img);           
            $('.activeBlock .thisActive', iframe).css({backgroundImage:'url('+img+')', backgroundSize:bgS});
      });

      
           $('div.titleEditingType').click(function (){
             $(this).closest('#toolboxPlatform').removeClass('bounceInLeft');
             $(this).closest('#toolboxPlatform').addClass('bounceOutLeft');
           });
           $('#toolboxPlatform h2').click(function(event) {
             event.preventDefault();
             event.stopPropagation();
             var _p = $(this).closest('#toolboxPlatform');
             if( $('i',this).hasClass('fa-minus')==true){
              $('ul', _p).slideUp();
              $('i',this).removeClass('fa-minus').addClass('fa-plus');
              $('ul', _p).removeClass('bounceInDown animated');
              $('ul', _p).addClass('bounceOutDown animated');
             }else{
              $('i',this).removeClass('fa-plus').addClass('fa-minus');
               $('ul', _p).slideDown();
               $('ul', _p).removeClass('bounceOutDown animated');
               $('ul', _p).addClass('bounceInDown animated');
             }
             
           });

           $('#activeElement a').click(function(event) {
            if($(this).hasClass('active')==true){
              return false;
            }
            $('input[name="active"]', iframe).trigger('click');
              $(this).closest('li').find('a').removeClass('active');
              $(this).addClass('active');
              if($(this).hasClass('activeState')==true){
              $('input[name="active"]', iframe).prop('checked',true);
              }else{
                  $('input[name="active"]', iframe).prop('checked',false);
              }
           });



     });
      /*change platform interface*/ 

      
  });

 