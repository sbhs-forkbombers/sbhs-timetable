var g_positionY = -1;
var g_scrolling = false;
var g_state     = 'waiting';
var g_countdown = 0;
var c_interval  = 75;
var c_startwait = 15000;
var c_movewait  = 10000;
var c_endwait   = 20000;

function pageScrollStart() {
	g_scrolling = true;
	g_state     = 'scroll-down';
	pageScroll();
}
      
function pageScrollStop() {	
	g_scrolling = false;
	g_state     = 'waiting';
}

function pageScrollToggle() {
	if (g_scrolling) {
		pageScrollStop();
	}
	else {
		pageScrollStart();
	}
}

      
function pageScroll() {
	// set next scroll event
	if (g_scrolling) {
		setTimeout('pageScroll()', c_interval);        
	}
	else {
		return;
	}
	// scroll in scroll state
	if (g_state == 'scroll-down') {
		window.scrollBy(0,1);
		// detect bottom of page        
		if ($(window).scrollTop()== $(document).height()-$(window).height()) {
			g_state = 'end-wait';
			g_countdown = c_endwait;
		}    
	}

	if (g_state == 'end-wait') {
		g_countdown -= c_interval;
		if (g_countdown <= 0) {
			//g_state = 'scroll-up';
			$('body,html').animate({ scrollTop: 0 }, 800);
			pageScrollStop();
			setTimeout('pageScrollStart()', c_movewait);
		}
	}

}



/*$(window).mousemove(function(e) {
	if ((g_state == 'scroll-down') || (g_state == 'waiting')) {
		if ((g_positionY > -1) && (Math.abs(e.clientY - g_positionY) > 1)) {
			pageScrollStop();
			setTimeout('pageScrollStart()', c_movewait);
		}
		g_positionY = e.clientY;
	}
});       

     /* 
      $(window).load(function() {
        setTimeout('pageScroll()', c_interval);        
        setTimeout('pageScrollStart()', c_startwait);
      });*/
