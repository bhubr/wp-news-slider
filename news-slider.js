(function($) {
  $(document).ready( function() {

 		function sliderWidget(index, instance) {
 			var $instance = $(instance);
 			var instanceId = $instance.attr('id');
 			var _options =  $instance.data('options');
 			var filteredOptions = {
 				interval: _options.interval * 1000,
 				orientation: _options.orientation,
 				autoplay: _options.autoplay
 			};

	  	// Get options
	  	var options = $.extend({
	  		interval: 5000,
	  		orientation: 'vertical',
	  		autoplay: true
	  	}, filteredOptions);
	  	console.log(instanceId, options);

	    var $thumbs = $instance.find('.excerpt');
	    var $firstThumb = $($thumbs[0]);
	    var timer = setInterval(fadeImages, options.interval);
	    var currentImgIndex = 0;
	  	var totalHeight = 250;

	    function fadeImages(imgIndex) {
	      var $currentBefore = $( $thumbs[currentImgIndex] );
	      var $currentAfter;

	      if(imgIndex) {
	      	currentImgIndex = imgIndex;
	      }
	      else {
		      currentImgIndex++;
	      }
	      if(currentImgIndex >= $thumbs.length) {
	        currentImgIndex = 0;
	      }
	      $currentBefore.parent().animate({ top: (- currentImgIndex * totalHeight) + 'px' }, 350);
	    }

	    function setContainerHeight() {
	    	var h3Height = $firstThumb.find('h3').outerHeight();
	    	var buttonsHeight = $firstThumb.find('p.buttons').outerHeight();
	    	var maxHeight = 0;
	    	$thumbs.each( function(index, thumbEl) {
	    		var contentHeight = $(thumbEl).find('.thumb-content').outerHeight();
	    		if(contentHeight > maxHeight) maxHeight = contentHeight;
	    		console.log(buttonsHeight,  h3Height, contentHeight, maxHeight, buttonsHeight + h3Height + contentHeight);
	    	});
	    	totalHeight = buttonsHeight + h3Height + maxHeight;
	    	console.log('total', totalHeight);

	    	$instance.find('.mask').css('height', totalHeight);
	    	$instance.find('.thumbs-wrapper .excerpt').css('height', totalHeight);
	    }

	    setContainerHeight();
	    $(window).on('resize', setContainerHeight);

	    $('.wp-sps-wrapper .numbered-btn').click( function() {
	    	fadeImages( $(this).data('idx') - 1 );
	    } );
	    $('.wp-sps-wrapper .stop-btn').click( function() {
	    	clearInterval(timer);
	    } );
	    $('.wp-sps-wrapper .play-btn').click( function() {
	    	fadeImages();
	    	timer = setInterval(fadeImages, options.interval);
	    } );
	  }

  	// Get instances
  	var $instances = $('.wp-sps-wrapper');
		$instances.each(sliderWidget);


 	});
})(jQuery);
