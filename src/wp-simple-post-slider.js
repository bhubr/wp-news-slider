(function($) {
  $(window).on('load', function() {

 		function sliderWidget(index, instance) {
 			var $instance = $(instance);
 			var instanceId = $instance.attr('id');
 			var _options =  $instance.data('options');
 			var filteredOptions = {
 				interval: _options.interval * 1000,
 				direction: _options.direction,
 				autoplay: _options.autoplay
 			};

	  	// Get options
	  	var options = $.extend({
	  		interval: 5000,
	  		direction: 'vertical',
	  		autoplay: false
	  	}, filteredOptions);
 			var isVertical = options.direction === 'vertical';
	  	console.log(instanceId, options);

	  	var $wrapper = $instance.find('.thumbs-wrapper');
	    var $thumbs = $instance.find('.excerpt');
	    var $firstThumb = $($thumbs[0]);
	    var thumbWidth;
	    var $numberedBtns = $instance.find('.numbered-btn');
	    var $playBtn = $instance.find('.icon-play2');
	    var $stopBtn = $instance.find('.icon-stop');
	    var $prevBtn = $instance.find('.icon-backward');
	    var $nextBtn = $instance.find('.icon-forward2');
	    var timer = null;
	    var currentImgIndex = 0;
	  	var totalHeight = 250;

	    function fadeImages(imgIndex) {
	      var $currentBefore = $( $thumbs[currentImgIndex] );
	      var $currentAfter;

      	$( $numberedBtns[currentImgIndex] ).removeClass('active');

	      if(imgIndex !== undefined) {
	      	currentImgIndex = imgIndex;
	      }
	      else {
		      currentImgIndex++;
	      }
	      if(currentImgIndex >= $thumbs.length) {
	        currentImgIndex = 0;
	      }
	      if(currentImgIndex < 0) {
	        currentImgIndex = $thumbs.length - 1;
	      }
	      $( $numberedBtns[currentImgIndex] ).addClass('active');
	      if( isVertical ) {
	      	$wrapper.css({ top: (- currentImgIndex * totalHeight) + 'px' });
	      }
	      else {
	      	$wrapper.css({ left: (- currentImgIndex * thumbWidth) + 'px' });
	      }
	    }

	    function setContainerHeight() {
	    	var h3Height = $firstThumb.find('h3').height();
	    	var maxHeight = 0;
	    	var thumbsAndHeights = [];
	    	$thumbs.each( function(index, thumbEl) {
	    		var $thumb = $(thumbEl);
	    		var contentHeight = $thumb.find('.thumb-content').height();
	    		var imgHeight = $thumb.find('.thumb-image img').height();
	    		var thumbHeight = Math.max( contentHeight, imgHeight );
	    		if(thumbHeight > maxHeight) maxHeight = thumbHeight;
	    		console.log(thumbHeight, h3Height);
	    		thumbsAndHeights.push({ thumb: $thumb, height: thumbHeight + h3Height });
	    		// console.log(h3Height, contentHeight, maxHeight, h3Height + contentHeight);
	    	});
	    	totalHeight = h3Height + maxHeight; // + 12;
	    	// console.log('total', totalHeight);
	    	// thumbsAndHeights.forEach(function(th) {
	    	// 	var vertPadding = Math.floor((totalHeight - th.height) / 2);
	    	// 	th.thumb.css('padding', vertPadding + 'px 0');
	    	// });

	    	$instance.find('.mask').css('height', totalHeight);
	    	$instance.find('.thumbs-wrapper .excerpt').css('height', totalHeight);
	    }

	    function setWrapperWidth() {
	    	thumbWidth = $firstThumb.width();
	    	$wrapper.css('width', thumbWidth * $thumbs.length);
	    	$thumbs.width( thumbWidth );
	    }

	    function isTimerRunning() {
	    	return timer !== null;
	    }

	    function resetTimer( doRestart ) {
	    	clearInterval(timer);
	    	if ( doRestart && isTimerRunning() ) {
	    		timer = setInterval(fadeImages, options.interval);
	    	}
	    	else {
		    	timer = null;
	    	}
	    }

	    if( options.autoplay ) {
	    	timer = setInterval(fadeImages, options.interval);
	    }
	    if( ! isVertical ) {
	    	setWrapperWidth();
	    	$(window).on('resize', setWrapperWidth);
	    }
	    setContainerHeight();
	    $(window).on('resize', setContainerHeight);

	    $numberedBtns.click( function(evt) {
	    	fadeImages( $(this).data('idx') - 1 );
	    	resetTimer( true );
	    } );
	    $prevBtn.click( function(evt) {
	    	fadeImages( currentImgIndex - 1 );
	    	resetTimer( true );
	    } );
	    $nextBtn.click( function() {
	    	fadeImages( currentImgIndex + 1 );
	    	resetTimer( true );
	    } );
	    $stopBtn.click( function() {
	    	resetTimer( false );
	    } );
	    $playBtn.click( function() {
	    	fadeImages();
	    	timer = setInterval(fadeImages, options.interval);
	    } );
	  }

  	// Get instances
  	var $instances = $('.wp-sps-wrapper');
		$instances.each(sliderWidget);


 	});
})(Zepto !== undefined ? Zepto : jQuery);
