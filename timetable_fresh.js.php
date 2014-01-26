function init() {
	var goodSize = $(window).height()*0.94;
	$('.day-input').height(goodSize);
	$('.day-input').width($(window).width()*0.99);
	$('.info').height(goodSize);
	$('.info').width($(window).width()*0.99);

}

function showTips() {
	
}

function doIE9InitialScroll() {
	showTips();
	doScroll("#Monday-a");
}

function doScroll(id) {
	jQuery('html,body').animate({scrollTop: jQuery(id).offset().top}, 1000);
}

function ie9Scroll(event) {
	var id = event.srcElement.parentNode.id;
	doScroll(NEXT_ANCHOR[id]);	
}

$(document).ready(function() {
	init();
});


