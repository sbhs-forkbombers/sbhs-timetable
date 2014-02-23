<?php
/*
    Copyright (C) 2014  James Ye  Simon Shields

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

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
