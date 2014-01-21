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
