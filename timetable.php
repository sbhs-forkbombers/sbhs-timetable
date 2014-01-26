<?php
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
include "./header.html";
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
	echo "<div id='sidebar'><div id='user-info'>Logged in as<br />$email<br />";
	// TODO a warning when deleting the timetable
	echo "<a href='/login.php?logout'>Logout</a><span style='font-size: 20px;'>&nbsp;&middot;&nbsp;</span><a href='/'>Homepage</a><span style='font-size: 20px;'>&nbsp;&middot;&nbsp;</span><a href='/timetable.php?clear-data' title='UNLEASH THE HOUNDS'>Clear the timetable</a></div></div>\n";

	include "./timetable_old.php";
}

