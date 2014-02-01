<?php
session_start();
set_include_path("gapi");
require_once("Google/Client.php");
require_once("Google/Service/Oauth2.php");
require_once("./common.php");


$data = db_get_data_or_create($_SESSION["email"]);


$year = $_POST["year"];

$r = db_store_data($_SESSION["email"], $data["timetable"]["timetable"], $year);

if ($r) {
	echo "Ok";
}
else {
	echo "Not ok";
}

?>
