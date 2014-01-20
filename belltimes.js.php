<?php ini_set("date.timezone", "Australia/Sydney");
header("Content-type: application/javascript");
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

nextBell = null;
function recalculateNextBell() {
	var now = new Date();
	var hour = now.getHours();
	var min  = now.getMinutes();
	if (after_school || weekend) {
		nextBell = belltimes['bells'][0];
		nextBell["internal"] = [9,0];
		var pName = nextBell['bell'].replace("Roll Call", "School starts").replace("End of Day", "School ends");
		document.getElementById("period-name").innerHTML = pName;
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
	if (!/ (starts)| (ends)$/.test(pName)) {
		pName += " starts";
	}
	if (/^\d/.test(pName)) {
		pName = "Period " + pName;
	}
	document.getElementById("period-name").innerHTML = pName;
	nextBell["internal"] = nearestBell;
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
	if (sec < -1) {
		recalculateNextBell();
		updateTimeLeft();
		return;
	}	
	el.innerHTML = format(sec-60);
}
	
$(document).ready(function() {
	recalculateNextBell();
	updateTimeLeft();
	setInterval(updateTimeLeft, 1000);
});

