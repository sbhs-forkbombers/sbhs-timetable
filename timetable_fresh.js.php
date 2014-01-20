function init() {
	var goodSize = $(window).height()*0.94;
	$('.day-input').height(goodSize);
	$('.day-input').width($(window).width()*0.99);
	$('.info').height(goodSize);
	$('.info').width($(window).width()*0.99);

}

function showTips() {
	// TODO
}

$(document).ready(function() {
	init();
});


