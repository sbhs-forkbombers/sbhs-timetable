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
session_start();
// todo google stuff
set_include_path("gapi");
require_once("Google/Client.php");
require_once("Google/Service/Oauth2.php");
require_once("./common.php");
if (!(isset($_SESSION['access_token']) && $_SESSION['access_token'])) {
	header("Location: /login.php?refresh-token&urlback=timetable.php");
}
$client = get_client();
$client->setAccessToken($_SESSION['access_token']);
$service = new Google_Service_Oauth2($client);
try {
	$results = $service->userinfo_v2_me->get();
}
catch (Exception $e) {
	error_log("EXCEPTION: " . $e->getMessage() . "\n");
	header("Location: /login.php?refresh-token&urlback=/timetable.php");
}
$email = $results['email'];
if ($email == "") {
	header("Location: /login.php?logout");
}
if (isset($_REQUEST['clear-data'])) {
	db_clear_data($email);
}
$user_data = db_get_data_or_create($email);
if (!$user_data["fresh"] && isset($_SESSION['new-timetable'])) {
	unset($_SESSION['new-timetable']);
	header("Location: /");
}
include "./header.php";
echo "<link rel='stylesheet' href='/style/timetable.css' />";
if ($user_data["fresh"]) {
	// new user, display that page.
	echo "<script src='/timetable_fresh.js.php'></script>";
	echo "</head><body>";
	echo "<div id='sidebar'><div id='user-info'>Logged in as<br />$email<br />";
	echo "<a href='/login.php?logout'>Logout</a><span style='font-size: 20px;'>&nbsp;&middot;&nbsp;</span><a href='/'>Homepage</a></div></div>\n";

	include "./timetable_fresh.php";
}
else {
	$timetable = json_decode($user_data["timetable"]["timetable"]);
	echo "<script src='/timetable_old.js.php'></script>";
	echo "</head><body>";
	echo "<div id='sidebar'><div id='user-info'><span class='nomobile'>Logged in as<br /></span>$email<br />";
	// TODO a warning when deleting the timetable
	echo "<a href='/login.php?logout'>Logout</a><span style='font-weight: bold;'>&nbsp;&middot;&nbsp;</span><a href='/'>Homepage</a><span style='font-weight: bold;'>&nbsp;&middot;&nbsp;</span><a href='/timetable.php?clear-data' title='UNLEASH THE HOUNDS'>Clear the timetable</a></div></div>\n";

	include "./timetable_old.php";
}

