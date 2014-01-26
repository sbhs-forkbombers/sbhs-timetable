<?php
$weeks = array("a","b","c");

$days = array("Monday","Tuesday","Wednesday","Thursday","Friday");

?>
<div class='info'>
Save your classes and access them anywhere.<br />
It'll take about five minutes. You will need a copy of your timetable.<br /><br /><br /><a class="button" href="javascript:void(0)" onclick="doIE9InitialScroll()">Begin!</a>
</div>
<?php
$ie9fix = array(); // IE9. Bad.
echo "<form action='process_timetable.php' method='POST'>";
foreach ($weeks as $wkey => $week) {
	foreach ($days as $dkey => $day) {
		
		echo "<div class='day-input' id='$day-$week'>";
		echo "<a name='$day-$week'></a>"; 
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
			$ie9fix["$day-$week"] = "#" . $days[$nextday] . "-" . $weeks[$nextweek];
			echo "<a class='button' href='javascript:void(0)' onclick='ie9Scroll(event);'>Next</a>";
		}
		echo "</div>";
	}
}
?>
</form>
<?php echo "<script>window.NEXT_ANCHOR = " . json_encode($ie9fix) . ";</script>" ?>
</body></html>
