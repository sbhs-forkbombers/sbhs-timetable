<?php
/*
    SBHS-Timetable Copyright (C) James Ye, Simon Shields 2014

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

ini_set("date.timezone", "Australia/Sydney");
if (!isset($results)) { header("Content-type: application/javascript"); }
function is_after_school($hour,$min) {
	if ($hour == 15 && $min >= 15) {
		return true;
	}
	else if ($hour > 15) {
		return true;
	}
	else {
		return false;
	}
}
$dateOffset = 0;
$now = time();
$localtime = localtime($now,true);
$hour = $localtime["tm_hour"];
$min  = $localtime["tm_min"];
$wday = $localtime["tm_wday"]+1;
echo "day_offset = 0;";
if (is_after_school($hour,$min) && ($wday%7) >= 2) {
	echo "after_school = true;\n";
	$wday+=1;
	$now += 24*60*60;
}
else {
	echo "after_school = false;\n";
}
if ($wday%7 < 2) {
	echo "weekend = true;\n";
	$rWday = $wday;
	if ($wday==0) {
		$wday = 2;
	}
	else {
		$wday = $wday%7;
	}
	if (is_after_school($hour,$min)) {
		$wday--;		
	}
	echo "day_offset += " . $wday . ";";
	$now += (24*60*60)*($wday);
}
else {
	echo "weekend = false;\n";
}
$NOW = $now;
echo "window.NOW = new Date(" . strftime("%G, %m - 1, %d", $now) . ");";
echo "//http://student.sbhs.net.au/api/timetable/bells.json?date=" . strftime("%G-%m-%d", $now);
?>

belltimes = {"status": "error"};
<?php //belltimes = <?php echo file_get_contents("http://localhost:8081/bells.json") ?>;
<?php if (isset($results)) {
	echo "// results set\n";
	echo "loggedIn = true;\n";
	$udata = db_get_data_or_create($results['email']);
	if ($udata['fresh']) {
		echo "//fresh\n";
		echo "timetable = null;";
	}
	else {
		$timetable = $udata['timetable']['timetable'];
		if (!preg_match('/^"/', $timetable)) {
			$timetable = json_encode($timetable);
		}
		echo "timetable = JSON.parse(" . $timetable . ");";
	}
}
else {
	echo "// results unset\n";
	echo "timetable = null;";
	echo "loggedIn = false;\n";
}
?>
Date.prototype.getYearDay = function() {
	var onejan = new Date(this.getFullYear(),0,1);
	return Math.ceil((this - onejan) / 86400000);
} 
Date.prototype.getDateStr = function() { return this.getFullYear() + "-" + (this.getMonth()+1) + "-" + this.getDate() }; 
week = null; 
dow = null;
recalculating = false;
nextBell = null;
nextBellIdx = null;
nextPeriod = null;
startDate = new Date();
function recalculateNextBell() {
	recalculating = true;
	var now = new Date();
	if (now.getDateStr() != startDate.getDateStr()) {
		// we've changed days
		startDate = now;
		after_school = false;
		day_offset--;
		if (day_offset <= 0) { weekend = false }
	}
	now.setMinutes(now.getMinutes() + 1);
	var hour = now.getHours();
	var min  = now.getMinutes();
	if (nextBell != null && nextBell["bell"] == "End of Day") {
		// it's now after school.
		after_school = true;
		// should get the next set of bells here
		NOW.setDate(NOW.getDate()+1);
		if (NOW.getDay() == 6) {
			NOW.setDate(now.getDate() + 2);
		}
		else if (NOW.getDay() == 0) {
			NOW.setDate(now.getDate() + 1);
		}
		if (now.getDay() >= 5) {
			// weekend!
			weekend = true;
		}
		$('#period-name').text("Updating bells...");
		$('#countdown').text('');
		$.getScript("http://student.sbhs.net.au/api/belltimes/bells.json?date=" + NOW.getDateStr() + "&callback=loadTimetable");
	}

	if (after_school || weekend) {
		nextBell = belltimes['bells'][0];
		nextBell["internal"] = [9,0];
		nextPeriod = belltimes['bells'][1];
		nextPeriod["internal"] = [9,5];
		var pName = nextBell['bell'].replace("Roll Call", "School starts").replace("End of Day", "School ends");
		document.getElementById("period-name").innerHTML = pName;
		recalculating = false;
		doReposition();
		return;
	}
	var nearestBellIdx = null;
	var nearestBell = null;
//	if (nextBellIdx == null) {
	for (var i = 0; i < belltimes['bells'].length; i++) {
			var start = belltimes['bells'][i]['time'].split(":");
			start[0] = Number(start[0]);
			start[1] = Number(start[1]);
			if (start[0] == hour && start[1] >= min) {
				if (nearestBell == null || ((nearestBell[0] == start[0] && nearestBell[1] > start[1]) || (nearestBell[0] > start[0]))) {
					nearestBell = start;
					nearestBellIdx = i;
				}
			}
			else if (start[0] > hour) {
				if (nearestBell == null || ((nearestBell[0] == start[0] && nearestBell[1] > start[1]) || (nearestBell[0] > start[0]))) {
					nearestBell = start;
					nearestBellIdx = i;
				}
			}
			if (nearestBell != null && ((nearestBell[0] == start[0] && nearestBell[1] < start[1]) || nearestBell[0] < start[0]) && ((nearestBell[0] == hour && nearestBell[1] > min) || nearestBell[0] > hour)) {
				// we're done!
				break;
			}
		}
	nextBell = belltimes['bells'][nearestBellIdx];
	var pName = nextBell['bell'].replace("Roll Call", "School starts").replace("End of Day", "School ends");
	if (/Transition|Lunch 1|Recess/i.test(pName)) {
		var last = belltimes['bells'][nearestBellIdx-1]['bell'];
		if (timetable != null) {
			var lesson = last;
			var details= timetable[week.toLowerCase()][dow.substr(0,3).toLowerCase()][Number(last)-1];
			var name = details["name"];
			if (name == "") {
				pName = "Free Period";
			}
			else {
				pName = name + " ends";
			}
		}
		else {
			pName = last + " ends";
		}
	}
	if (!/ (starts)| (ends)$/.test(pName)) {
		pName += " starts";
	}
	if (/^\d/.test(pName)) {
		pName = "Period " + pName;
	}
	nextBell["internal"] = nearestBell;
	if (/^\d/.test(nextBell['bell']) ) {
		nextPeriod = nextBell;
	}
	else if (nextBell['bell'] == "End of Day") {
		nextPeriod = null; // period one tomorrow
	}
	else {
		var j = "";
		var idx = nearestBellIdx;
		while (!/^\d/.test(j)) {
			idx++;
			j = belltimes['bells'][idx]['bell'];
		}
		nextPeriod = belltimes['bells'][idx];
		var times = nextPeriod['time'].split(":");
		times[0] = Number(times[0])
		times[1] = Number(times[1])
		nextPeriod['internal'] = times;

	}
	
	document.getElementById("period-name").innerHTML = pName;
	if (timetable != null && nextPeriod != null) {
		doNextPeriod(nextPeriod);
	}
	else {
		var j = document.getElementById("next-info");
		if (j != null) {
			j.innerHTML = "";
		}
	}
	recalculating = false;
	doReposition();	
}

function doNextPeriod(nextP) {
	var text = "";
	var nextPeriod = timetable[week.toLowerCase()][dow.substr(0,3).toLowerCase()][Number(nextP["bell"]-1)];
	if (nextPeriod == null) {
		text = "No more periods today!";
	}
	else {
		text = "<strong>Next Period:</strong>";
		if (nextPeriod["name"] == "") {
			text += " <span class='next-period'>Free Period!";
		}
		else {
			if (nextPeriod["room"] == "") {
				text += " <span class='next-period'>" + nextPeriod["name"] + "</span>";
			}
			else {
				text += " <span class='next-period'>" + nextPeriod["name"] + " in " + nextPeriod["room"] + "</span>";
			}
		}
	}
	document.getElementById("next-info").innerHTML = text;
}

function format(seconds) {
	var sec = (seconds % 60) + ""; seconds = Math.floor(seconds/60);
	var min = (seconds % 60); 
	if (min + (seconds-min) < 100) {
		min += (seconds-min);
		var hrs = 0;
	}
	else {
		seconds = Math.floor(seconds/60);
		min += "";
		if (min.length < 2) {
			min = "0" + min;
		}
		var hrs = seconds;
	}

	if (sec.length < 2) {
		sec = "0" + sec;
	}
	return (hrs > 0 ? (hrs + "h ") : "") + min + "m " + sec + "s";
}

function isAfterSchool(hour, min) {
	if (hour == 15 && min >= 15) {
		return true;
	}
	else if (hour > 15) {
		return true;
	}
	return false;
}

function updateTimeLeft() {
	if (recalculating) {
		return;
	}
	var n = new Date();
	var teststr = n.getDateStr();
	var el = document.getElementById("countdown");
	var start = nextBell["internal"];
	if (weekend || after_school) {
		var now = new Date();
		now.setDate(now.getDate()+1+day_offset);
		now.setHours(0,0,0);
	}
	else {
		var now = new Date();
	}
	var hour = now.getHours();
	var min = now.getMinutes() + hour*60;
	var startMin = start[0]*60 + start[1];
	min = startMin - min;
	var sec = min * 60;
	if (weekend || after_school) {
		sec += (24*60*60)*day_offset;
		sec += Math.floor((now.valueOf() - Date.now())/1000);
	}
	sec += (60 - now.getSeconds());
	if (sec < 60 || n.getDateStr() != startDate.getDateStr()) {
		recalculateNextBell();
		updateTimeLeft();
		return;
	}	
	el.innerHTML = format(sec-60);
}

var rightEx = false;
var leftEx = false;
var topEx = false;

var noticesLoaded = false;
function slideOutTop() {
	if (rightEx) slideOutRight();
	if (leftEx) slideOutLeft();
	var opts = { // spinner settings
		lines: 10,
		length: 40,
		width: 10,
		radius: 30,
		corners: 1,
		direction: 1,
		color: '#fff',
		speed: 1,
		trail: 60,
		shadow: true,
	};
	if (!noticesLoaded) {
		var target = document.getElementById("slideout-top");
		window.currentNoticesSpinner = new Spinner(opts).spin(target);
	}
	$('#slideout-top,#slideout-top-arrow').toggleClass("expanded");
	if (!noticesLoaded) {
		getNotices();
	}
	topEx = !topEx;
	noticesLoaded = true;
}

function slideOutRight() {
	if (window.oneSlider && leftEx) {
		slideOutLeft();
	}
	rightEx = !rightEx;
	$('#slideout-right').toggleClass('expanded');
	$('#slideout-right-arrow').toggleClass('expanded');
	if (rightEx) {
		$('#darkener').addClass('visible');
	}
	else if (!leftEx) {
		$('#darkener').removeClass('visible');
	}
}

function slideOutLeft() {
	if (window.oneSlider && rightEx) {
		slideOutRight();
	}
	leftEx = !leftEx;
	$('#slideout-left').toggleClass('expanded');
	$('#slideout-left-arrow').toggleClass('expanded');
	if (leftEx) {
		$('#darkener').addClass('visible');
	}
	else if (!rightEx) {
		$('#darkener').removeClass('visible');
	}
}

function updateRightSlideout() { // belltimes here
	var text;
	text = "<div style='text-align: center;'><span class='big'>" + dow + " " + week + "</span><br /><table class='right-table' style='margin-left: auto; margin-right:auto;'><tbody>";
	for (i in belltimes['bells']) {
		var part = belltimes['bells'][i];
		text +="<tr><td class='bell-desc'>" + part['bell'].replace(/^(\d)/, function(v) { return "Period " + v; }) + "</td><td class='bell-time'>" + part['time'] + "</td></tr>";
	}
	text += "</tbody></table></div>";
	document.getElementById("slideout-right").innerHTML = text;
}
function updateLeftSlideout() {// timetable here
	var text;
	if (timetable == null) {
		// prompt them to log in and create a timetable
		if (loggedIn) {
			text = "<div style='text-align: center;'><h1>Your timetable, here.</h1>";
			text += "You can see your timetable here.<br />";
			text += "It'll take about five minutes. You'll need a copy of your timetable.<br /><br /><br /><br />";
			text += "<a href='/timetable.php' class='fake-button'>Get Started</a></div>";
		}
		else {
			text = "<div style='text-align: center;'><h1>Your timetable, here.</h1>";
			text += "You can see your timetable here. Sign in using your Google account.<br />";
			text += "You can also sign in with your school email account:<br />";
			text += "<span style='word-wrap: break-word'>&lt;YourStudentID&gt;@student.sbhs.nsw.edu.au</span><br /><br /><br /><br />"; 
			text += "<a href='/login.php?urlback=/timetable.php&new-timetable' class='fake-button'>Sign In</a></div>";
		}
	}
	else {
		text = "<div style='text-align: center;' ><span class='big-title'>" + dow + " " + week + "</span><br /><table class='left-table' style='margin-left: auto; margin-right: auto;'><tbody>";
		var day = dow.substr(0,3).toLowerCase();
		var wk  = week.toLowerCase();
		var today = timetable[wk][day];
		for (i in today) {
			var name = today[i]["name"];
			if (name == "") {
				name = "Free Period";
			}
			text += "<tr><td class='big'>" + (Number(i)+1) + "</td><td class='sidebar-name'>" + name + "</td><td class='sidebar-room'>" + today[i]["room"] + "</td></tr>";
		}
		text += "</tbody></table></div>";
	}
	document.getElementById("slideout-left").innerHTML = text;
}
$.getScript('<?php
echo "http://student.sbhs.net.au/api/timetable/bells.json?date=" . strftime("%G-%m-%d", $NOW)/* "2014-01-30"*/ . "&callback=loadTimetable";
?>');

var DOCUMENT_READY = false;
var BELLTIMES_DONE = false;
function loadTimetable(obj) {
	window.belltimes = obj;
	BELLTIMES_DONE = true;
	if (DOCUMENT_READY) {
		begin();
	}
}

$(document).ready(function() { 
	$('#old-ie-warn').css({"opacity": 0, "font-size": 0}); // we got this far...
	DOCUMENT_READY = true;  
	if (BELLTIMES_DONE) begin();
	if (window.actualMobile) return;
	$('#slideout-top-arrow').click(slideOutTop);
	$('#slideout-top-arrow').css({"opacity": 1});
	$('#notices-notice').css({"opacity": 1});
	if (/compatible; MSIE 9.0;/.test(window.navigator.userAgent) && !window.localStorage["noIE9annoy"] && false ) { // TODO enable this. It might scare people off, though.
		$('#ie9-warn').css({"opacity": 1});
	}
	setTimeout(function() {
		$('#slideout-top-arrow').css({"opacity": ""});
		$('#notices-notice').css({"opacity": 0});
	}, 5000);
	setTimeout(function() {$('#ie9-warn').css({"opacity": 0})}, 10000);
});

function begin() {
	if (belltimes["status"] == "Error") {
		$('#countdown').text('');
		$('#period-name').text("Something went wrong :(");
		$('#in').html("You can <a href='https://docs.google.com/forms/d/1z7uAIRsPjDTQxevO1R5GFn4OrETeHuZ0j2jzBcg3UKM/viewform'>report a bug</a>, or try again later.");
	}
	else {
		week = belltimes["weekType"];
		dow  = belltimes["day"];
		recalculateNextBell();
		updateTimeLeft();
		setInterval(updateTimeLeft, 1000);
		updateLeftSlideout();
		updateRightSlideout();
	}
	$(window).on('resize', doReposition);
}
function doReposition() {
	if (window.innerWidth <= 510 || window.MOBILE) {
		$('.slideout').css({"width": "100%", "padding": "0"});
		window.oneSlider = true;
	}
	else if (window.innerWidth <= 625 && window.falseMobile) {
		$('.slideout').css({"width": "50%", "padding": "0"});
		window.oneSlider = false;
	}
	else {
		$('.slideout').css({"width": "40%", "padding": 0});
		window.oneSlider = false;
	}


	if (window.innerWidth <= 395) {
		window.MOBILE = true;
		window.falseMobile = true;
	}
	else if (window.falseMobile) {
		window.MOBILE = false;
	}
	if (window.MOBILE) {
		$('#period-name').css({"font-family": "Roboto", "font-size": "40px"});
		$('#countdown').css({"font-family": "Roboto", "font-size": "50px"});
	}
	var top1 = $('#period-name').height() + 80;
	$('#in').css({"top": top1});
	var top2 = $('#in').height();
	$('#countdown').css({"top": top1+top2});

	
}

function getNotices() {
	$.getJSON("notices/dailynotices.php?codify=yes&date="+NOW.getDateStr(), processNotices);
}

function processNotices(data) {

	var res = "<h1 style='text-align: center'>Notices for " + dow + " " + week + "</h1>";
	res += "Pick a year: <select id='notice-filter'><option value='.notice-row'>All years</option>";
	for (i=7; i <= 12; i++) {
		res += "<option value='.notice-"+i+"'>Year " + i + "</option>";
	}
	res += "<option value='.notice-Staff'>Staff</option></select>";
	res += "<span class='rightside'>" /*<a href='javascript:void(0)' id='noticetoggle'>Expand/collapse all notices</a><span style='font-size:14px'>&#9679;</span>*/+"<a href='/notices/dailynotices.php?date="+NOW.getDateStr()+"'>Full notices</a></span>";
	res += "<table id='notices'><tbody>";
	var allNotices = data[0];
	i = 0;
	for (i in allNotices) {
		var n = allNotices[i];
		var classes = 'notice-row';
		var ylist = n["years"];
		for (j in ylist) {
			classes += " notice-"+ylist[j];
		}
		res += "<tr id='notice-"+i+"' class='"+classes+"'><td class='for'>"+n["applicability"]+"</td><td class='info'><span class='notice-title'>"+n["title"]+"</span><span class='content'>"+n["content"]+"<span class='author'>"+n["author"]+"</span></span></td></tr>";
	}
	res+="</tbody></table>";
	if (i==0) {
		res += "<h1>There are no notices!</h1>";
	}
	window.currentNoticesSpinner.stop();
	$('#slideout-top').html(res);
	doneNoticeLoad();
}
function doneNoticeLoad() {
	$('.info').click(function(ev) {
		$($(this).children('.content')[0]).slideToggle();
	});
	$('.content').slideToggle();
	$('#notice-filter').change(function() {
		$('.notice-row:not('+$(this).val()+')').fadeOut();
		$($(this).val()).fadeIn();
	});
	$('#notice-toggle').click(function() {
		$('.content').slideToggle();
	});
}

function dismissIE9() {
	window.localStorage["noIE9annoy"] = true;
	$('#ie9-warn').css({"opacity": 0});
}

yepnope([{
		test: Modernizr.touch,
		yep : ["/script/jquery.mobile.custom.min.js"],
/*		callback: function(url, result, key) {
			console.log("CALLBACKED! " + url +"," + result +"," + key);
		},*/
		complete: function() {
			if ($.mobile) {
				$(document).ready(function() { 
					$(document).on('swipeleft', function(ev) { 
						var start = ev.swipestart.coords[0];
						var rightPanel = (start > (window.innerWidth/2));
						if (leftEx && (window.oneSlider || !rightPanel)) {
							slideOutLeft();
							/*if (rightPanel) {
								slideOutRight();
							}*/
						}
						else if ((rightPanel || window.oneSlider) && !rightEx) {
							slideOutRight();
						}
		
					});
					$(document).on('swiperight', function(ev) { 
						var start = ev.swipestart.coords[0];
						var leftPanel = (start < (window.innerWidth/2));
						if (rightEx && (window.oneSlider || !leftPanel)) {
							slideOutRight();
							/*if (leftPanel) {
								slideOutLeft();
							}*/
						}
						else if ((leftPanel || window.oneSlider) && !leftEx) {
							slideOutLeft();
						}
					});
					$(document).on('swipeup', function(ev) {
						if (topEx) {
							slideOutTop();
						}
					});
					$(document).on('swipedown', function(ev) {
						if (!topEx) {
							slideOutTop();
						}
					});
				});
				if (window.actualMobile || /ipad|android/i.test(navigator.userAgent)) {
					$('#swipe-info').css({"opacity": 1});
					setTimeout(function() { $('#swipe-info').css({"opacity": 0}) }, 5000);
				}
			}
		}
	}]);


(function(a,b){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))window.MOBILE=true})(navigator.userAgent||navigator.vendor||window.opera,'http://detectmobilebrowser.com/mobile');
