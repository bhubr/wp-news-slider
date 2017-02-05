// /* This is where we use noobSlide class */
// window.addEvent('domready',function(){
// 	//SAMPLE 8
// 	if( $('wpnsw_interval') == null) return;
// 	var handles8_more = $$('#handles8_more span');
// 	var nS8 = new noobSlide({
// 		box: $('box8'),
// 		items: $$('#box8 h3'),
// 		size: 240,
// 		interval: $('wpnsw_interval').get('value'),
// 		autoPlay: true,
// 		mode: 'vertical',
// 		handles: $$('#handles8 span'),
// 		addButtons: { // previous: $('prev8'), 
// 				play: $('play8'), 
// 				stop: $('stop8'), 
// 				//next: $('next8') 
// 			},
// 		onWalk: function(currentItem,currentHandle){
// 			//style for handles
// 			$$(this.handles,handles8_more).removeClass('active');
// 			$$(currentHandle,handles8_more[this.currentIndex]).addClass('active');
// 			//text for "previous" and "next" default buttons
// 			//$('prev8').set('html','&lt;&lt; '+this.items[this.previousIndex].innerHTML);
// 			//$('next8').set('html',this.items[this.nextIndex].innerHTML+' &gt;&gt;');
// 		}
// 	});
// 	//more "previous" and "next" buttons
// 	nS8.addActionButtons('previous',$$('#box8 .prev'));
// 	nS8.addActionButtons('next',$$('#box8 .next'));
// 	//more handle buttons
// 	nS8.addHandleButtons(handles8_more);
// 	//walk to item 3 witouth fx
// 	nS8.walk(0,false,true);

// });

(function($) {
  $(document).ready( function() {
  	var $intervalInput = $('#wpnsw_interval');
  	var slideInterval;
  	var slideOrientation = 'vertical';
  	if( $intervalInput.length === 0 ) {
  		console.log('No interval specified (missing #wpnsw_interval input). Using default (4 seconds)');
  		slideInterval = 1000;
  	}
  	else {
  		slideInterval = $intervalInput.val();
  	}
    var $thumbs = $('.excerpt');
    var timer = setInterval(fadeImages, slideInterval);
    var currentImgIndex = 0;

    function fadeImages() {
      var $currentBefore = $( $thumbs[currentImgIndex] );
      var $currentAfter;

      currentImgIndex++;
      if(currentImgIndex >= $thumbs.length) {
        currentImgIndex = 0;
      }

      // $currentAfter = $( $thumbs[currentImgIndex] );
      $currentBefore.parent().animate({ top: (- currentImgIndex * 240) + 'px' }, 300);
      // $currentAfter.animate({ opacity: 1 }, 500);
    }

    function setContainerHeight() {
      //var containerWidth = $imagesContainer.width();
      //var containerHeight = aspectRatio * containerWidth;
      //$imagesContainer.css('height', containerHeight + 'px');
    }

    setContainerHeight();
    $(window).on('resize', setContainerHeight);

    // $thumbs.css('opacity', 0);
    // $( $thumbs[0] ).css('opacity', 1);

  } );
})(jQuery);