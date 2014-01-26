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
echo "<h1>Your timetable</h1>";


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
		foreach ($day as $num => $data) {
			$dnum = $num + 1;
			echo "<span id='$wn-$dn-$num'><span class='big-number'>$dnum</span>: <span class='name'>" 
				. $data->name 
				. "</span>&nbsp;&nbsp;<span class='room'>" 
				. $data->room 
				. " <span class='edit'>Edit</span></span></span><br />";
		}
		echo "</div>";
	}
}
			

?>

<div class='arrow left' style="position: fixed; top: 75%; left: 10%; cursor: pointer" onclick="goLeft()"></div>
<div class='arrow right' style="position:fixed; top: 75%; right: 10%; cursor: pointer" onclick="goRight()"></div>
