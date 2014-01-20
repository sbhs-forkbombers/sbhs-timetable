<?php
$weeks = array("a","b","c");

$days = array("Monday","Tuesday","Wednesday","Thursday","Friday");

?>
<div class='info'>
<h1>1-Tap Timetable (Beta!)</h1>
Save your classes, and access them from any <span title="Maybe not your fridge">device</span>.<br />
Setup will take about five minutes, and you will need a copy of your timetable in order to complete it.<br /><br /><br /><a class="button" href="#Monday-a" onclick="showTips()">Begin!</a>
</div>
<?php

echo "<form action='process_timetable.php' method='POST'>";
foreach ($weeks as $wkey => $week) {
	foreach ($days as $dkey => $day) {
		echo "<div class='day-input' id='$day-$week'>";
		echo "<a name='$day-$week'></a>"; # todo is it name or id?
		echo "$day " . strtoupper($week) . "<br />";
		for ($i = 0; $i < 5; $i++) {
			echo "Period " . ($i+1) . " <input name='$day-$week-$i-name' type='text'/>@<input name='$day-$week-$i-room' type='text' class='room' /><br />\n";
		}
		$nextday = $dkey+1;
		$nextweek = $nextday > 4 ? $wkey+1 : $wkey;
		$nextday %= 5;
		echo "<br /><br />";
		if ($nextweek > 2) {
			echo "<input type='submit' value='Save it!' />";
		}
		else {
			echo "<a class='button' href='#" . $days[$nextday] . "-" . $weeks[$nextweek] . "'>Next</a>";
		}
		echo "</div>";
	}
}
?>
</form>
