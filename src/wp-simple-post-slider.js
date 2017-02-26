// Array.reduce polyfill
// Production steps of ECMA-262, Edition 5, 15.4.4.21
// Reference: http://es5.github.io/#x15.4.4.21
// https://tc39.github.io/ecma262/#sec-array.prototype.reduce
if (!Array.prototype.reduce) {
  Object.defineProperty(Array.prototype, 'reduce', {
    value: function(callback /*, initialValue*/) {
      if (this === null) {
        throw new TypeError('Array.prototype.reduce called on null or undefined');
      }
      if (typeof callback !== 'function') {
        throw new TypeError(callback + ' is not a function');
      }

      // 1. Let O be ? ToObject(this value).
      var o = Object(this);

      // 2. Let len be ? ToLength(? Get(O, "length")).
      var len = o.length >>> 0;

      // Steps 3, 4, 5, 6, 7
      var k = 0;
      var value;

      if (arguments.length == 2) {
        value = arguments[1];
      } else {
        while (k < len && !(k in o)) {
          k++;
        }

        // 3. If len is 0 and initialValue is not present, throw a TypeError exception.
        if (k >= len) {
          throw new TypeError('Reduce of empty array with no initial value');
        }
        value = o[k++];
      }

      // 8. Repeat, while k < len
      while (k < len) {
        // a. Let Pk be ! ToString(k).
        // b. Let kPresent be ? HasProperty(O, Pk).
        // c. If kPresent is true, then
        //    i. Let kValue be ? Get(O, Pk).
        //    ii. Let accumulator be ? Call(callbackfn, undefined, « accumulator, kValue, k, O »).
        if (k in o) {
          value = callback(value, o[k], k, o);
        }

        // d. Increase k by 1.
        k++;
      }

      // 9. Return accumulator.
      return value;
    }
  });
}

(function($) {
  $(window).on('load', function() {

    function sliderWidget(index, instance) {
      var $instance = $(instance);
      var $parentBox = $instance.parent();
      var instanceId = $instance.attr('id');
      var _options =  $instance.data('options');
      var filteredOptions = {
        interval: _options.interval * 1000,
        direction: _options.direction,
        autoplay: _options.autoplay,
        aspectRatio: parseFloat(_options.aspect_ratio)
      };
      var $innerMask = $instance.find('.inner-mask');

      // Get options
      var options = $.extend({
        interval: 5000,
        direction: 'vertical',
        autoplay: false,
        aspectRatio: 1.25
      }, filteredOptions);
      var isVertical = options.direction === 'vertical';

      var $wrapper = $instance.find('.thumbs-wrapper');
      var $thumbs = $instance.find('.excerpt');
      var $outerMask = $instance.find('.mask');
      var pxRegex = /(\d+)px.*/;
      var maskExtraWidth = ['padding', 'border'].reduce(function(total, prop) {
        var matches = pxRegex.exec($outerMask.css(prop));
        if(matches !== null) {
          return total + 2 * parseInt(matches[1], 10);
        }
      }, 0);

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
      // var aspectRatio = 1.33;
      console.log(options);

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
          $wrapper.css({ left: (2 - currentImgIndex * thumbWidth) + 'px' });
        }
      }
      function setContainerDimensions() {
        console.log('outer mask dims', $outerMask.width() + 'x' + $outerMask.height());
        console.log('inner mask dims', $innerMask.width() + 'x' + $innerMask.height());
        var viewportWidth = $parentBox.width() - maskExtraWidth;

        var viewportHeight = viewportWidth / options.aspectRatio;
        $thumbs.width(viewportWidth);
        $thumbs.height(viewportHeight);

        $outerMask.css('height', viewportHeight);
        // $instance.find('.thumbs-wrapper .excerpt').css('height', viewportWidth * 3.0 / 4);

        console.log('thumbs dims', $thumbs.width() + 'x' + $thumbs.height());
        console.log('viewport dims', viewportWidth + 'x' + viewportHeight);
        console.log('mask dims', $outerMask.width(), $outerMask.height());

      }

      function setWrapperWidth() {
        thumbWidth = $firstThumb.width();
        $wrapper.css('width', thumbWidth * $thumbs.length);
        $thumbs.width( thumbWidth );
        fadeImages( currentImgIndex );
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
      setContainerDimensions();
      $(window).on('resize', setContainerDimensions);
      if( ! isVertical ) {
        setWrapperWidth();
        $(window).on('resize', setWrapperWidth);
      }

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
