jQuery(document).ready(function($) {
var interv = setInterval(runSlides, 3000);
var numSlides = $('#slidetick li').length;
var slideCount = 1;
var firstSlide = $('#slidetick li:first');
function runSlides(){
	var currSlide = $('#slidetick li.current-post');
	currSlide.fadeOut('slow', function(){
		currSlide.removeClass('current-post');
		if(slideCount < numSlides){
			currSlide.next().fadeIn('fast', function(){
				currSlide.next().addClass('current-post');
			});
			slideCount += 1;
		} else {
			firstSlide.fadeIn('fast', function(){
				firstSlide.addClass('current-post');
			});
			slideCount = 1;
		}
	});
}
});