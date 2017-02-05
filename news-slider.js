(function($) {
  $(document).ready( function() {

  	// Get options
  	var $intervalInput = $('#wpnsw_interval');
  	var slideInterval;
  	var slideOrientation = 'vertical';

  	if( $intervalInput.length === 0 ) {
  		console.log('No interval specified (missing #wpnsw_interval input). Using default (4 seconds)');
  		slideInterval = 2000;
  	}
  	else {
  		slideInterval = $intervalInput.val();
  	}

  	// GERER PLUSIEURS SLIDERS !!!
    var $thumbs = $('.excerpt');
    var $firstThumb = $($thumbs[0]);
    var timer = setInterval(fadeImages, slideInterval);
    var currentImgIndex = 0;
  	var totalHeight = 250;

    function fadeImages() {
      var $currentBefore = $( $thumbs[currentImgIndex] );
      var $currentAfter;

      currentImgIndex++;
      if(currentImgIndex >= $thumbs.length) {
        currentImgIndex = 0;
      }
      $currentBefore.parent().animate({ top: (- currentImgIndex * totalHeight) + 'px' }, 350);
    }

    function setContainerHeight() {
    	var h3Height = $firstThumb.find('h3').outerHeight();
    	var maxHeight = 0;
    	$thumbs.each( function(index, thumbEl) {
    		var contentHeight = $(thumbEl).find('.thumb-content').outerHeight();
    		if(contentHeight > maxHeight) maxHeight = contentHeight;
    	});
    	totalHeight = h3Height + maxHeight + 12;
    	$('.wp-sps-wrapper .mask').css('height', totalHeight);
    	$('.wp-sps-wrapper .thumbs-wrapper .excerpt').css('height', totalHeight);
    }

    setContainerHeight();
    $(window).on('resize', setContainerHeight);

  } );
})(jQuery);