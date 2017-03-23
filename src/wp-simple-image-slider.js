(function($) {
  $(document).ready(() => {

    $.fn.simpleSlider = function() {
      function getNextIndex() {
        return currentIndex === maxIndex ? 0 : currentIndex + 1;
      }
      var self = this;
      var deferredImages = this.data('images');
      var $wrapper = this.find('.imageholder');
      var $images;
      var maxIndex;
      var currentIndex;
      var lastIndex;
      var nextIndex;
      setInterval(() => {
        nextIndex = getNextIndex();
        $($images[currentIndex]).addClass('offset-left');
        $($images[nextIndex]).removeClass('offset-right');
        $($images[lastIndex]).removeClass('offset-left').addClass('offset-right');
        lastIndex = currentIndex;
        currentIndex = nextIndex;
        }, 4000);
      setTimeout(() => {
        deferredImages.forEach(function (obj) {
          $wrapper.append(obj.thumbnail);
          $images = self.find('img.slide');
          lastIndex = maxIndex = $images.length - 1;
          currentIndex = 0;
          nextIndex = 1;
        }, 1000);
      })
    }

    $('.slider').simpleSlider();
  });
})(Zepto !== undefined ? Zepto : jQuery);