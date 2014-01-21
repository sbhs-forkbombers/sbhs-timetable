<?php
session_start();
set_include_path("gapi");
require_once("Google/Client.php");
require_once("Google/Service/Oauth2.php");
require_once("./common.php");


$timetable = $timetable_structure;


foreach ($_POST as $k => $v) {
	$data = preg_split("/-/", strtolower($k));
	$timetable[$data[1]][substr($data[0], 0, 3)][$data[2]][$data[3]] = $v;
}


$email = get_client_email();

db_store_data($email,$timetable);


header("Location: /timetable.php");	
