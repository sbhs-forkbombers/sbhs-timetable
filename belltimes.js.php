<?php ini_set("date.timezone", "Australia/Sydney");
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
	echo "day_offset += " . $wday%7 . ";";
	$now += (24*60*60)*($wday);
}
else {
	echo "weekend = false;\n";
}
echo "//http://student.sbhs.net.au/api/timetable/bells.json?date=" . strftime("%G-%m-%d", $now);
?>

belltimes = <?php echo file_get_contents( "http://student.sbhs.net.au/api/timetable/bells.json?date=" . /*strftime("%G-%m-%d", $now))*/ "2014-01-30") ?>//;
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
week = belltimes["weekType"];
dow  = belltimes["day"];
recalculating = false;
nextBell = null;
nextPeriod = null;
function recalculateNextBell() {
	recalculating = true;
	var now = new Date();
	var hour = now.getHours();
	var min  = now.getMinutes();
	if (after_school || weekend) {
		nextBell = belltimes['bells'][0];
		nextBell["internal"] = [9,0];
		nextPeriod = belltimes['bells'][1];
		nextPeriod["internal"] = [9,5];
		var pName = nextBell['bell'].replace("Roll Call", "School starts").replace("End of Day", "School ends");
		document.getElementById("period-name").innerHTML = pName;
		recalculating = false;
		return;
	}
	var nearestBellIdx = null;
	var nearestBell = null;

	for (var i in belltimes['bells']) {
		var start = belltimes['bells'][i]['time'].split(":");
		start[0] = Number(start[0]);
		start[1] = Number(start[1]);
		if (start[0] == hour && start[1] >= min) {
			if (nearestBell == null || ((nearestBell[0] == start[0] && nearestBell[0] > start[0]) || (nearestBell[0] > start[0]))) {
				nearestBell = start;
				nearestBellIdx = i;
			}
		}
		else if (start[0] > hour) {
			if (nearestBell == null || ((nearestBell[0] == start[0] && nearestBell[0] > start[0]) || (nearestBell[0] > start[0]))) {
				nearestBell = start;
				nearestBellIdx = i;
			}
		}
	}
	nextBell = belltimes['bells'][nearestBellIdx];
	var pName = nextBell['bell'].replace("Roll Call", "School starts").replace("End of Day", "School ends");
	if (/Transition|Lunch 1|Recess/i.test(pName)) {
		var last = belltimes['bells'][nearestBellIdx-1]['bell'];
		if (timetable != null) {
			var lesson = last;
			var details= timetable[week.toLowerCase()][dow.substr(0,3).toLowerCase()][Number(last)];
			var name = details["name"];
			if (name == "") {
				pname = "Free Period";
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
	if (timetable != null) {
		doNextPeriod(nextPeriod);
	}
	else {
		var j = document.getElementById("next-info");
		if (j != null) {
			j.innerHTML = "";
		}
	}
	recalculating = false;
}

function doNextPeriod(nextP) {
	var text = "";
	var nextPeriod = timetable[week.toLowerCase()][dow.substr(0,3).toLowerCase()][Number(nextP["bell"])];
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
	var min = (seconds % 60) + ""; seconds = Math.floor(seconds/60);
	var hrs = seconds + "";
	if (sec.length < 2) {
		sec = "0" + sec;
	}
	if (min.length < 2) {
		min = "0" + min;
	}
	if (hrs.length < 2) {
		hrs = "0" + hrs;
	}
	return hrs + ":" + min + ":" + sec;
}
	

function updateTimeLeft() {
	if (recalculating) {
		return;
	}
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
	if (sec < 60) {
		recalculateNextBell();
		updateTimeLeft();
		return;
	}	
	el.innerHTML = format(sec-60);
}

function slideOutRight() {
	$('#slideout-right').toggleClass('expanded');
	$('#slideout-right-arrow').toggleClass('expanded');
}
function slideOutLeft() {
	$('#slideout-left').toggleClass('expanded');
	$('#slideout-left-arrow').toggleClass('expanded');
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
			text += "You can see. your timetable here.<br />";
			text += "It'll take about five minutes. You'll need a copy of your timetable.<br /><br /><br /><br />";
			text += "<a href='/timetable.php' class='fake-button'>Get Started</a></div>";
		}
		else {
			text = "<div style='text-align: center;'><h1>Your timetable, here.</h1>";
			text += "You can see your timetable here. Sign in using your Google account .<br />";
			text += "You can also sign in with your school email account:<br />";
			text += "&lt;YourStudentID&gt;@student.sbhs.nsw.edu.au<br /><br /><br /><br />"; 
			text += "<a href='/login.php?urlback=/timetable.php' class='fake-button'>Sign In</a></div>";
		}
	}
	else {
		text = "<div style='text-align: center;' ><span class='big'>" + dow + " " + week + "</span><br /><table class='left-table' style='margin-left: auto; margin-right: auto;'><tbody>";
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
	
$(document).ready(function() {
	if (belltimes["status"] == "Error") {
		$('#in').text('');
		$('#period-name').text("Something went wrong :(");
		$('#countdown').text("You can <a href='http://github.com/sbhs-forkbombers/sbhs-timetable'>report a bug</a>, or try again later.");
	}
	else {
		recalculateNextBell();
		updateTimeLeft();
		setInterval(updateTimeLeft, 1000);
		updateLeftSlideout();
		updateRightSlideout();
	}
});

