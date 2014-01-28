//  SBHS-Timetable Copyright (C) James Ye, Simon Shields 2014
//
//  This program is free software: you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation, either version 3 of the License, or
//  (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
//
//  You should have received a copy of the GNU General Public License
//  along with this program.  If not, see <http://www.gnu.org/licenses/>.

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
	var el = event.srcElement || event.target;
	var id = el.parentNode.id;
	doScroll(NEXT_ANCHOR[id]);	
}

$(document).ready(function() {
	init();
});


