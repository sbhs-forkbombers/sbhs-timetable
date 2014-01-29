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
echo "<h1 id='timetable-h1'>Your timetable</h1>";


$ldays = array(
	"mon" => "Monday",
	"tue" => "Tuesday",
	"wed" => "Wednesday",
	"thu" => "Thursday",
	"fri" => "Friday"
);

$_SESSION['email'] = $email; // just in case the session invalidates before the user clicks 'save'

foreach (get_object_vars($timetable) as $wn => $week) {
	foreach (get_object_vars($week) as $dn => $day) {
		echo "<div id='$dn-$wn' class='day'>";
		echo "<span class='day-heading'>" . $ldays[$dn] . " " . strtoupper($wn) . "</span><br />";
		echo "<table><tbody>";
		foreach ($day as $num => $data) {
			$dnum = $num + 1;
			$name = $data->name;
			$room = $data->room;
			echo "<tr id='$wn-$dn-$num'><td class='big-number'>$dnum</td> <td class='name'>" 
				. $name 
				. "</td><td class='room'>" 
				. $room 
				. "</td><td class='edit'>Edit</td></tr>";
		}
		echo "</tbody></table></div>";
	}
}
			

?>
<div id="swipe-info">Swipe left/right to see other days</div>
<div class='timetable arrow left'  style="position: fixed; top: 75%; left: 10%; cursor: pointer" onclick="goLeft()"></div>
<div class='timetable arrow right' style="position:fixed; top: 75%; right: 10%; cursor: pointer" onclick="goRight()"></div>

<select id="day-select">
<option value='0'>Jump to a day...</option>
<?php
$weeks = array("A","B","C");
$days = array("mon","tue","wed","thu","fri");
for ($i = 0; $i < 15; $i++) {
	$wk = floor($i / 5);
	$day = $i % 5;
	echo "<option value='".$i."'>".$ldays[$days[$day]]." ".$weeks[$wk]."</option>";
}
?>
</select>
<script>
$('#day-select').change(function() {
	var val = $(this).val();
	if (val < currentIdx) {
		while (currentIdx != val) {
			goLeft();
		}
	}
	else if (val > currentIdx) {
		while (currentIdx != val) {
			goRight();
		}
	}
});
</script>
</body>
</html>
