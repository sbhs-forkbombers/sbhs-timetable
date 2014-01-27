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

var wks = ["a","b","c"];
var day = ["mon","tue","wed","thu","fri"];

var inOrder = [];

for (var i = 0; i < 3; i++) {
	for (var j = 0; j < 5; j++) {
		inOrder.push(day[j]+"-"+wks[i]);
	}
}

currentIdx = 0;

// pull elements from the left
function goLeft() {
	if (currentIdx == 0) {
		return;
	}
	
	var nowEl = inOrder[currentIdx];
	var newEl = inOrder[currentIdx-1];
	var rightSemiOffScreenVal = (window.actualMobile ? "150%" : "75%");
	var leftSemiOffScreenVal = (window.actualMobile ? "-150%" : "-25%");

	if ((currentIdx+1) < 15) {
		var rightEl = inOrder[currentIdx+1];
		$('#'+rightEl).css({"left": "150%"});
	}
	$('#'+nowEl).css({"left": rightSemiOffScreenVal, "opacity": "0.5"});
	$('#'+newEl).css({"left": "25%", "opacity": "1"});
	if ((currentIdx-2) > -1) {
		var leftEl = inOrder[currentIdx-2];
		$('#'+leftEl).css({"left": leftSemiOffScreenVal});
	}
	currentIdx--;
}
// pull elements from the right
function goRight() {
	if (currentIdx == 14) {
		return;
	}
	var leftSemiOffScreenVal = (window.actualMobile ? "150%" : "75%");
	var rightSemiOffScreenVal = (window.actualMobile ? "-150%" : "-25%");

	var nowEl = inOrder[currentIdx];
	var newEl = inOrder[currentIdx+1];

	if ((currentIdx-1) > -1) {
		var leftEl = inOrder[currentIdx-1];
		$('#'+leftEl).css({"left": "-150%"});
	}
	$('#'+nowEl).css({"left": rightSemiOffScreenVal, "opacity": "0.5"});
	$('#'+newEl).css({"left": "25%", "opacity": "1"});
	if ((currentIdx+2) < 15) {
		var rightEl = inOrder[currentIdx+2];
		$('#'+rightEl).css({"left": leftSemiOffScreenVal});
	}
	currentIdx++;
}

function startEdit(ev) {
	var el = $(ev.currentTarget);
	if (el.hasClass("editing")) {
		var inputRoom = el.prev().children()[0];
		var inputName = el.prev().prev().children()[0];
		var newName = inputName.value;
		var newRoom = inputRoom.value;
		var path = el.parent().attr("id");
		var req = $.ajax({
			"url": "/update_db.php",
			"type": "POST",
			"data": { "changed": path, "room": newRoom, "name": newName },
			"dataType": "text"
		});
		el.text("Saving...");
		req.done(function(msg) {
			if (/^Ok/.test(msg)) {
				el.prev().prev().html(newName);
				el.prev().html(newRoom);
				el.text("Saved!");
				el.css({"opacity": 1});
				el.removeClass("editing");
				setTimeout(function() { el.text("Edit"); el.css({"opacity": ""}) }, 5000);
			}
			else {
				el.text("Failed :(");
				setTimeout(function() { el.text("Try again") }, 5000);
				// do something to notify the user the request failed. TODO
			}
		});
		req.fail(function() { /*TODO*/ });
	}
	else {
		el.addClass("editing");
		var pName = el.prev().prev().text().replace(/^ +/, "");
		var pRoom = el.prev().text().replace(/^ +/, "");
		el.prev().prev().html("<input type='text' value='"+pName+"' />");
		el.prev().html("<input type='text' value='"+pRoom+"' />");
		el.text("Done");
	}
}


$(document).ready(function() {
	$('.edit').click(startEdit);
});

Modernizr.load([{
	test: Modernizr.touch,
	yep : ["/script/jquery.mobile.custom.min.js"],
	complete: function() {
		if ($.mobile) {
			$(document).ready(function() { 
				$(document).on('swipeleft', function(ev) { 
					goRight();
				});
				$(document).on('swiperight', function(ev) { 
					goLeft();
				});
			});
			if (window.actualMobile || /ipad|android/i.test(navigator.userAgent)) {
				$('#swipe-info').css({"opacity": 1});
				setTimeout(function() { $('#swipe-info').css({"opacity": 0}) }, 5000);
			}
		}
	}
}]);

