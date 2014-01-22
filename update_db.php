<?php
session_start();
set_include_path("gapi");
require_once("Google/Client.php");
require_once("Google/Service/Oauth2.php");
require_once("./common.php");

header("Content-type: text/plain; encoding=utf-8");

if (!isset($_SESSION['email'])) {
	echo "Not ok";
	error_log("Attempted to update entry without a valid email in \$_SESSION!");
	exit;
}

if (!isset($_REQUEST['changed']) || !isset($_REQUEST['room']) || !isset($_REQUEST['name'])) {
	echo "Not ok";
	error_log("Attempted to update entry without key/value!");
	exit;
}

$updated_key = $_REQUEST['changed'];
$updated_room = $_REQUEST['room'];
$updated_name = $_REQUEST['name'];

$currently = db_get_data_or_create($_SESSION['email']);

if ($currently['fresh']) {
	echo "Not ok";
	error_log("Attempted to update entry without creating a timetable [illegal access to timetable_old?]");
	exit;
}

$currently = $currently['timetable']['timetable'];
$currently = json_decode($currently);

$parts = preg_split("/-/", $updated_key);

$week = $currently->$parts[0]; //->$parts[1]
$day = $week->$parts[1];
$day[$parts[2]]->name = $updated_name;
$day[$parts[2]]->room = $updated_room;
$week->$parts[1] = $day;
$currently->$parts[0] = $week;

db_store_data($_SESSION['email'], $currently);
echo "Ok";
