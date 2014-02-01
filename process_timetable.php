<!--
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
-->
<?php
session_start();
set_include_path("gapi");
require_once("Google/Client.php");
require_once("Google/Service/Oauth2.php");
require_once("./common.php");


$timetable = $timetable_structure;

if (isset($_POST["student-year"])) {
	$year = $_POST["student-year"];
	unset($_POST["student-year"]);
}
else {
	$year = "";
}

foreach ($_POST as $k => $v) {
	$data = preg_split("/-/", strtolower($k));
	$timetable[$data[1]][substr($data[0], 0, 3)][$data[2]][$data[3]] = $v;
}


$email = get_client_email();

db_store_data($email,$timetable,$year);


header("Location: /timetable.php");	
