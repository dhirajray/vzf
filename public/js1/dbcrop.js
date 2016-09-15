var cropPrevImgW = '';
var cropPrevImgH = '';
	var movestart = false;
	var flipHorizontal = false;
	var flipVertical = false;
	var mystartXp = 0;
	var mystartYp= 0;
	var previewXp= 0;
	var previewYp= 0;
	var objX= 0;
	var objY= 0;
	var pX= 0;
	var pY= 0;
	var xp= 0;
	var yp= 0;
	var xPos= 0;
	var yPos = 0;
	var rotateValue = 0;
	var rotateCount = 0;
	var dataURL ;
	var cropcropFrameW = 0;
	var cropcropFrameH = 0;
	var increaseValue = 1.2;
	
	function getCropDataUrl(){

		return dataURL;

	}
function getCropedImage (){
    	$('#canvasCropImg').remove();
			//var imageName = $('#cropingImage').attr('src').split('/')[1].split('.')[0];
			
			 $('#myCropTool').append('<canvas id="canvasCropImg" width="'+cropPrevImgW+'" height="'+cropPrevImgH+'" ></canvas>');
			var imgW =  $('#cropingImage').width();
			var imgH =  $('#cropingImage').height();
			 c = $("#canvasCropImg")[0];
			var ctx = c.getContext("2d");
			var imgObj = $("#myCropTool .cropingImage");
			var img = new Image();
			
				var src = imgObj.attr('src');
				img.src = src;
				var rtV = rotateValue*Math.PI/180;
				img.onload = function(){
					var ctxImgXp = xp-previewXp;
					var ctxImgYp = yp-previewYp;
					
					if(rotateCount!=0){
						ctx.save();					
						ctx.translate(cropPrevImgW/2,cropPrevImgH/2);
						ctx.rotate(rtV);
						ctx.translate(-cropPrevImgW/2,-cropPrevImgH/2);
						ctx.drawImage(this, ctxImgXp, ctxImgYp,imgW, imgH );
						ctx.restore();
					}else{
						ctx.drawImage(this, ctxImgXp, ctxImgYp,imgW, imgH );
					}

												
					
					 dataURL = c.toDataURL("image/jpeg",{
					 	   quality: 0.1,
					 	    progressive: true
					 });
					 $('.cropedImageView').html('<div style="background:url('+getCropDataUrl()+') no-repeat;  background-size:contain; background-position:center center; border-radius:50%; margin-left:-'+cropPrevImgW/2+'px; margin-top:-'+cropPrevImgH/2+'px; left:50%; top:50%; position:absolute; width:'+cropPrevImgW+'px; height:'+cropPrevImgH+'px;">');
				
				}
				$('#canvasCropImg').remove();
				
				
    }

   function fileReader(evt){
		var input = evt.target;
		//console.log(input.files[0])
		var fileReader = new FileReader;
		fileReader.onload = function() {
			var dataURL = fileReader.result;
			$('#myCropTool img').attr('src', dataURL);
		}

		return fileReader.readAsDataURL(input.files[0]);
	};

$(function(){

 $.dbCropImage = function (options){
 	  var defaults = {
 	  		url:'',
 	  		element:'body',
 	  		toolBox:false,
 	  		width:250,
 	  		height:250,
 	  		cropBtn:'',
 	  		event:'',
 	  		load:$.noop()
        }

		movestart = false;
		flipHorizontal = false;
		flipVertical = false;
		mystartXp = 0;
		mystartYp= 0;
		previewXp= 0;
		previewYp= 0;
		objX= 0;
		objY= 0;
		pX= 0;
		pY= 0;
		xp= 0;
		yp= 0;
		xPos= 0;
		yPos = 0;
		rotateValue = 0;
		rotateCount = 0;
	var settings = $.extend({}, defaults, options);
	var element = $(settings.element);
	var toolBox  = '';

      if(settings.toolBox==true){
      	toolBox = '';
      }
 	var cropTemplate = '<div id="myCropTool" ><div id="zoomAction">\
 	<a href="javascript:void(0);" action-type="crop" title="Crop" class="fa fa-crop" rel="dbTip"></a>\
 	<a href="javascript:void(0);" action-type="rotate" title="Rotate" class="fa fa-refresh disabled" rel="dbTip"></a>\
 	<a href="javascript:void(0);" action-type="zoomIn"  title="Zoom in" class="fa fa-plus-circle" rel="dbTip"></a>\
 	<a href="javascript:void(0);" action-type="zoomOut"  title="Zoom out" class="fa fa-minus-circle" rel="dbTip"></a>\
 	<a href="javascript:void(0);" action-type="backToOrignalState">Revert back</a>\
 	</div>\
		'+toolBox+'\
		<div class="cropAreaWrp">\
			<div class="moveFrame"></div>\
			<div class="moveFrameDark"></div>\
				<div class="cropArea imageWrapper">\
					<img src="'+settings.url+'">\
				</div>\
			<div class="imageWrapper">\
				<img src="'+settings.url+'" class="cropingImage" id="cropingImage" >\
			</div>\
		</div>\
	</div>';
$('#myCropTool').remove();
element.append(cropTemplate);
$dbTip();
var evt = settings.event;
fileReader(evt);


	
	 cropFrameW = element.width();
	 cropFrameH = element.height();
	 cropPrevImgW = settings.width;
	 cropPrevImgH = settings.height;
	var frame = $('#myCropTool');
	var cropingImage = $('.cropingImage', frame);
	var cropPrevImg = $('.cropArea');
	var imageWrapper = $('.imageWrapper', frame);
	frame.css({width:cropFrameW, height:cropFrameH})
	 increaseValue = 1.2;
	var c ;
	var ext ='png';
	$('.cropWidthInput').val(cropPrevImgW);
	$('.cropHeightInput').val(cropPrevImgH);
	var scaleValue = '';
		$('#myCropTool').addClass('loadingNow');
		$('.cropLoader').remove();
		$('#myCropTool').append('<div class="cropLoader"><i class="fa fa-cog fa-spin"></i></div>');
		cropPrevImg.css({width:cropPrevImgW, height:cropPrevImgH, marginLeft:-cropPrevImgW/2, marginTop:-cropPrevImgH/2});

	function cropArea (){
		$('.cropLoader').remove();
		$('#myCropTool').removeClass('loadingNow');
		 previewXp = cropPrevImg.position().left-cropPrevImgW/2;
		 previewYp = cropPrevImg.position().top-cropPrevImgH/2;
		 if(xp==0 || yp == 0){
		 	 $('#myCropTool img').css({width:cropFrameW});
			 $('img', cropPrevImg).css({transform:'translate('+-previewXp+'px,'+-previewYp+'px)' });
		 }else{
		 	 $('img', cropPrevImg).css({transform: 'translate('+(xp-previewXp)+'px,'+(yp-previewYp)+'px)'});
		 }
		
	}
	$('.cropingImage').load(function (){	
			getCropedImage();
			settings.load.call();
			cropArea();
			$('html').addClass('cropToolLoaded');		
	});

	$('#myCropTool .cropAreaWrp').bind('mousewheel DOMMouseScroll',function (event){
		 if (event.originalEvent.wheelDelta > 0 || event.originalEvent.detail < 0) {
		        // scroll up
		        cropFrameW = cropFrameW*increaseValue;
		        cropFrameH = cropFrameH*increaseValue;
		        $('#myCropTool img').css({width:cropFrameW});
		    }
		    else {
		        // scroll down
		         cropFrameW -= cropFrameW*increaseValue-cropFrameW;
		         cropFrameH -= cropFrameH*increaseValue-cropFrameH;
		        $('#myCropTool img').css({width:cropFrameW});
		    }
		    $('.resizer').css({width:cropFrameW, height:cropFrameH,transform: 'translate('+xp+'px,'+yp+'px)'});
	});
	

	
if(isMobile==null){

    $('#myCropTool .cropAreaWrp').on('mousedown',function (e){
    		 mystartXp = e.offsetX;
    		 mystartYp = e.offsetY;
			movestart = true;

    }).on('mousemove',function (e){
    	if(movestart==true){
    	var img = $(this).find('.cropingImage');
			 xPos = e.offsetX;
			 yPos = e.offsetY;
    		 xp = objX+xPos-mystartXp;
			 yp = objY+yPos-mystartYp;	
			 var scaleValueMove = '';
			 if(scaleValue!=''){
			 	scaleValueMove = 'scale('+scaleValue+')';
			 }
			img.css({transform: 'translate('+xp+'px,'+yp+'px) '+scaleValueMove+''});
			$('img', cropPrevImg).css({transform: 'translate('+(xp-previewXp)+'px,'+(yp-previewYp)+'px) '+scaleValueMove+''});

			}
    }).on('mouseup',function(e){
    		objX = xp;
			objY = yp;
			getCropedImage();
    		movestart = false;
    });

  	

}else{
  	$('#myCropTool .cropAreaWrp').on('touchstart',function (e){
  			 var e = e.originalEvent;
  			// console.log(e.touches[0].pageX);
    		 mystartXp = e.touches[0].pageX;
    		 mystartYp = e.touches[0].pageY;
			movestart = true;

    }).on('touchmove', function (e){    		
    	if(movestart==true){
    		var e = e.originalEvent;
    	var img = $(this).find('.cropingImage');
			 xPos = e.touches[0].pageX;
			 yPos = e.touches[0].pageY;
    		 xp = objX+xPos-mystartXp;
			 yp = objY+yPos-mystartYp;	
			 var scaleValueMove = '';
			 if(scaleValue!=''){
			 	scaleValueMove = 'scale('+scaleValue+')';
			 }
			img.css({transform: 'translate('+xp+'px,'+yp+'px) '+scaleValueMove+''});
			$('img',cropPrevImg).css({transform: 'translate('+(xp-previewXp)+'px,'+(yp-previewYp)+'px) '+scaleValueMove+''});

			}
    }).on('touchend',function(e){
    		var e = e.originalEvent;
    		objX = xp;
			objY =yp;
			getCropedImage();
    	movestart = false;
    });
  }
  	
  		
  		$(settings.cropBtn).click(function (){
  			getCropedImage();
  		});

  		


  		


		


	}

});
function cropImageAction (){
			rotateValue = 0;
			rotateCount = 0;
  			getCropedImage();
  			$('.cropedImageView').remove();
  			$('#myCropTool').prepend('<div class="cropedImageView"><div style="background:url('+getCropDataUrl()+') no-repeat; background-size:contain; background-position:center center; border-radius:50%; margin-left:-'+cropPrevImgW/2+'px; margin-top:-'+cropPrevImgH/2+'px; left:50%; top:50%; position:absolute; width:'+cropPrevImgW+'px; height:'+cropPrevImgH+'px;"></div>');
  		}	

  		function rotateImageAfterCrop(){
  				rotateValue +=60;
				rotateCount+=1;
				if(rotateValue>=360){
					rotateValue= 0;
					rotateCount= 0;
				}
				getCropedImage();

  		}
  		function backToOrignalState(){
			rotateValue= 0;
			rotateCount= 0;
			getCropedImage();
  		}

$(function (){
	$('body').on('click', '#zoomAction a:not(.disabled)',function (e){
  			e.stopPropagation();
  			var action = $(this).attr('action-type');
  			var p = $(this).closest('#zoomAction');
  			if (action=='zoomIn') {
		        cropFrameW = cropFrameW*increaseValue;
		        cropFrameH = cropFrameH*increaseValue;
		        $('#myCropTool img').css({width:cropFrameW});
		    }
		    else if(action=='zoomOut'){
		         cropFrameW -= cropFrameW*increaseValue-cropFrameW;
		         cropFrameH -= cropFrameH*increaseValue-cropFrameH;
		        $('#myCropTool img').css({width:cropFrameW});
		    }
		   else if(action=='crop'){
		   		cropImageAction();				
		   		$(this).addClass('disabled');
		   		$('a[action-type="rotate"]', p).removeClass('disabled');
		   }
		   else if(action=='rotate'){
		   		rotateImageAfterCrop();
		   }
		   else if(action=='backToOrignalState'){
		   	backToOrignalState();
		   	$('.cropedImageView').remove();
			$('a[action-type="rotate"]', p).addClass('disabled');
			$('a[action-type="crop"]', p).removeClass('disabled');
		   		
		   }

  		});
});