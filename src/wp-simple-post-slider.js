(function($) {
  $(window).load( function() {

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
        direction: 'horizontal',
        autoplay: false
      }, filteredOptions);
      var isVertical = options.direction === 'vertical';
      // console.log(instanceId, options);

      var $wrapper = $instance.find('.thumbs-wrapper');
      var $thumbs = $instance.find('.excerpt');
      var $mask = $instance.find('.mask');
      var numThumbs = $thumbs.length;
      var $firstThumb = $($thumbs[0]);
      // var matches = /(\d+)px/.exec($firstThumb.css('padding'));
      // var thumbPadding = 0;
      // if(matches) {
      //   thumbPadding = 2 * parseInt(matches[1], 10);
      // }
      var thumbWidthPercent;
      var thumbWidth;
      var thumbHeight;
      var horizWrapperWidth = numThumbs * 100 + '%';
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
        if(currentImgIndex >= numThumbs) {
          currentImgIndex = 0;
        }
        if(currentImgIndex < 0) {
          currentImgIndex = numThumbs - 1;
        }
        $( $numberedBtns[currentImgIndex] ).addClass('active');
        if( isVertical ) {
          $wrapper.animate({ top: (- currentImgIndex * totalHeight) + 'px' }, 350);
        }
        else {
          console.log('animate', currentImgIndex, thumbWidth, (- currentImgIndex * thumbWidth) + 'px');
          $wrapper.animate({ left: (- currentImgIndex * thumbWidth) + 'px' }, 350);
        }
      }

      function setContainerHeight() {
        // var labelHeight = $firstThumb.find('h3').height();
        // var imgHeight = $firstThumb.find('img').height();
        // var contentHeight = $firstThumb.find('.thumb-content').height();
        // var maxHeight = Math.max(imgHeight, contentHeight);
        // thumbHeight = 10 + imgHeight + labelHeight;
        // console.log('heights', imgHeight, contentHeight, maxHeight, thumbHeight); //, contentHeight);
        thumbHeight = $firstThumb.height();
        $mask.css('height', thumbHeight + 'px');
        // var maxHeight = 0;
        // var thumbsAndHeights = [];
        // $thumbs.each( function(index, thumbEl) {
        //   var $thumb = $(thumbEl);
        //   var contentHeight = $thumb.find('.thumb-content').outerHeight();
        //   var imgHeight = $thumb.find('.thumb-image img').outerHeight() + 14;
        //   var thumbHeight = Math.max( contentHeight, imgHeight );
        //   if(thumbHeight > maxHeight) maxHeight = thumbHeight;
        //   thumbsAndHeights.push({ thumb: $thumb, height: thumbHeight + h3Height + 12 });
        //   // console.log(h3Height, contentHeight, maxHeight, h3Height + contentHeight);
        // });
        // totalHeight = h3Height + maxHeight + thumbPadding + 12;
        // console.log('total', totalHeight);
        // thumbsAndHeights.forEach(function(th) {
        //   var vertPadding = Math.floor((totalHeight - th.height) / 2);
        //   console.log('total h', totalHeight, 'this h', th.height, 'vp', vertPadding);
        //   // th.thumb.css('padding', vertPadding + 'px 0');
        // });

        // $instance.find('.mask').css('height', totalHeight);
        // $instance.find('.thumbs-wrapper .excerpt').css('height', totalHeight);
      }

      function setWrapperWidth() {
        thumbWidthPercent = (100.0 / numThumbs).toFixed(2) + '%';
        console.log('horizWrapperWidth/thumbWidthPercent', horizWrapperWidth, thumbWidthPercent);
        $wrapper.css('width', horizWrapperWidth);
        $thumbs.css('width', thumbWidthPercent);
        thumbWidth = $firstThumb.width();
        console.log('thumb width px', thumbWidth);

      //   thumbWidth = $firstThumb.outerWidth();
      //   console.log('wrapper width inner/outer/padding', $firstThumb.innerWidth(), $firstThumb.outerWidth(), $firstThumb.css('padding'));
      //   $wrapper.css('width', thumbWidth * numThumbs);
      //   $thumbs.width( thumbWidth - thumbPadding );
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
})(jQuery);
