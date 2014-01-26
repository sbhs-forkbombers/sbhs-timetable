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

	if ((currentIdx+1) < 15) {
		var rightEl = inOrder[currentIdx+1];
		$('#'+rightEl).css({"left": "150%"});
	}
	$('#'+nowEl).css({"left": "75%", "opacity": "0.5"});
	$('#'+newEl).css({"left": "25%", "opacity": "1"});
	if ((currentIdx-2) > -1) {
		var leftEl = inOrder[currentIdx-2];
		$('#'+leftEl).css({"left": "-25%"});
	}
	currentIdx--;
}
// pull elements from the right
function goRight() {
	if (currentIdx == 14) {
		return;
	}
	
	var nowEl = inOrder[currentIdx];
	var newEl = inOrder[currentIdx+1];

	if ((currentIdx-1) > -1) {
		var leftEl = inOrder[currentIdx-1];
		$('#'+leftEl).css({"left": "-50%"});
	}
	$('#'+nowEl).css({"left": "-25%", "opacity": "0.5"});
	$('#'+newEl).css({"left": "25%", "opacity": "1"});
	if ((currentIdx+2) < 15) {
		var rightEl = inOrder[currentIdx+2];
		$('#'+rightEl).css({"left": "75%"});
	}
	currentIdx++;
}

function startEdit(ev) {
	var el = $(ev.currentTarget);
	if (el.hasClass("editing")) {
		var inputName = el.parent().prev().children()[0];
		var inputRoom = el.parent().children()[0];
		var newName = inputName.value;
		var newRoom = inputRoom.value;
		var path = el.parent().parent().attr("id");
		var req = $.ajax({
			"url": "/update_db.php",
			"type": "POST",
			"data": { "changed": path, "room": newRoom, "name": newName },
			"dataType": "text"
		});
		el.text("Saving...");
		req.done(function(msg) {
			if (/^Ok/.test(msg)) {
				el.parent().prev().html(newName);
				el.parent().html(newRoom + " <span class='edit' id='edit'>Edit</span>");
				el = document.getElementById('edit');
				el.id = "";
				$(el).click(startEdit);
			}
			else {
				// do something to notify the user the request failed. TODO
			}
		});
		req.failed(function() { /*TODO*/ });
	}
	else {
		el.addClass("editing");
		var pName = el.parent().prev().text();
		var pRoom = el.parent().text().replace(el.text(), "");
		el.parent().prev().html("<input type='text' value='"+pName+"' />");
		el.parent().html("<input type='text' value='"+pRoom+"' /> <span class='edit editing' id='edit'>Done!</span>");
		var el = $('#edit');
		el[0].id = "";
		el.click(startEdit);
	}
}


$(document).ready(function() {
	$('.edit').click(startEdit);
});
