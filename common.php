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

$dow = array(
	"mon" => array(),
	"tue" => array(),
	"wed" => array(),
	"thu" => array(),
	"fri" => array()
);

$timetable_structure = array(
	"a"	=> $dow,
	"b"	=> $dow,
	"c"	=> $dow
);
$prefix = $_SERVER['DOCUMENT_ROOT'];
$data = preg_split("/\n/", file_get_contents($_SERVER['DOCUMENT_ROOT']."/.htsecret"));

function get_client() {
	global $data;
	$client_id = $data[0];
	$client_secret = $data[1];
	$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/login.php';
	//$redirect_uri = 'http://localhost:8081/login.php';

	$client = new Google_Client();
	$client->setClientId($client_id);
	$client->setClientSecret($client_secret);
	$client->setRedirectUri($redirect_uri);
	$client->setScopes('email'); // email will be the identifier
	return $client;
}

function get_client_email($urlback = "/") {
	$client = get_client();
	$client->setAccessToken($_SESSION['access_token']);
	$service = new Google_Service_Oauth2($client);
	try {
		    $results = $service->userinfo_v2_me->get();
	}
	catch (Exception $e) {
		    error_log("EXCEPTION: " . $e->getMessage() . "\n");
		    header("Location: /login.php?refresh-token&urlback=$urlback");
	}
	return $results["email"];
}
function get_db_handle() {
	global $prefix;
	return new SQLite3("$prefix/.httimetable.db");
}

function db_get_data_or_create($email) {
	$handle = get_db_handle();
	global $timetable_structure;
	$result = $handle->querySingle('SELECT * FROM timetable WHERE email="' . SQLite3::escapeString($email) . '"', true);
	if ($result === false) {
		echo "FATAL ERROR - INVALID QUERY. ";
		echo $handle->lastErrorCode() . " - " . $handle->lastErrorMsg() . "\n";
		$handle->close();
	}
	else if (!isset($result['email'])) {
		// create an entry
		$handle->exec("INSERT OR REPLACE INTO timetable VALUES (\"" . SQLite3::escapeString($email) . "\", '" . SQLite3::escapeString(json_encode($timetable_structure)) . "', '');");
		$result = $handle->querySingle('SELECT * FROM timetable WHERE email="' . SQLite3::escapeString($email) . '"', true);
		$handle->close();
		return array("timetable" => $result, "fresh" => true, "year" => $result['year']);
	}
	else {
		$handle->close();
		$decoded = json_decode($result['timetable']);
		//var_dump($decoded);
		$fresh = (!array_key_exists(0, $decoded->a->mon) || count($decoded->a->mon[0]) == 0) ;
		
		return array("timetable" => $result, "fresh" => $fresh, "year" => $result['year']);
	}
}

function db_clear_data($email) {
	$handle = new SQLite3(".httimetable.db");
	$handle->exec("DELETE FROM timetable WHERE email=\"" . SQLite3::escapeString($email) . "\";");
	$handle->close();
}

function db_store_data($email, $timetable, $year=null) {
	if ($year == null) {
		$j = debug_backtrace();
		error_log("db_store_data was called without a year arg from " . $j['file'] .":". $j['line']);
		$year = "";
	}
	$handle = get_db_handle();
	if (!is_string($timetable)) {
		$timetable = SQLite3::escapeString(json_encode($timetable));
	}
	else {
		$timetable = SQLite3::escapeString($timetable);
	}
	$email = SQLite3::escapeString($email);
	$year = SQLite3::escapeString($year);
	$r = $handle->exec("UPDATE timetable SET timetable='$timetable',year='$year' WHERE email=\"$email\"");
	$handle->close();
	return $r;
}

function db_get_diary_or_create($email) {
	$handle = get_db_handle();
	$result = $handle->querySingle('SELECT * FROM todo WHERE email="' . SQLite3::escapeString($email) . '"', true);
	if ($result === false) {
		echo "FATAL ERROR - INVALID QUERY. ";
		echo $handle->lastErrorCode() . " - " . $handle->lastErrorMsg() . "\n";
		$handle->close();
	}
	else if (!isset($result['email'])) {
		// create an entry
		$handle->exec("INSERT OR REPLACE INTO todo VALUES (\"" . SQLite3::escapeString($email) . "\", '[]');");
		$result = $handle->querySingle('SELECT * FROM todo WHERE email="' . SQLite3::escapeString($email) . '"', true);
		$handle->close();
		return $result["data"];
	}
	else {
		return $result["data"];	
	}
}

function db_clear_diary($email) {
	$handle = get_db_handle();
	$handle->exec("DELETE FROM todo WHERE email=\"" . SQLite3::escapeString($email) . "\";");
	$handle->close();
}

function db_store_diary($email, $json) {
	$handle = get_db_handle();
	$json = SQLite3::escapeString($json);
	$email = SQLite3::escapeString($email);
	$r = $handle->exec("UPDATE todo SET data='$json' WHERE email='$email'");
	$handle->close();
	return $r;
}
