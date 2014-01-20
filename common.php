<?php
$handle = new SQLite3(".httimetable.db");
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

$data = preg_split("/\n/", file_get_contents(".htsecret"));

function get_client() {
	global $data;
	$client_id = $data[0];
	$client_secret = $data[1];
	$redirect_uri = 'http://sbhstimetable.tk/login.php';
	//$redirect_uri = 'http://localhost:8081/login.php';

	$client = new Google_Client();
	$client->setClientId($client_id);
	$client->setClientSecret($client_secret);
	$client->setRedirectUri($redirect_uri);
	$client->setScopes('email'); // email will be the identifier
	return $client;
}


function db_get_data_or_create($email) {
	global $handle;
	$result = $handle->querySingle('SELECT * FROM timetable WHERE email="' . SQLite3::escapeString($email) . '"', true);
	if ($result === false) {
		echo "FATAL ERROR - INVALID QUERY. ";
		echo $handle->lastErrorCode() . " - " . $handle->lastErrorMsg() . "\n";
	}
	else if (!isset($result['email'])) {
		// create an entry
		$handle->exec("INSERT INTO timetable VALUES (\"" . SQLite3::escapeString($email) . "\", \"" . SQLite3::escapeString(json_encode($timetable_structure)) . "\")");
		$result = $handle->querySingle('SELECT * FROM timetable WHERE email="' . SQLite3::escapeString($email) . '"', true);
		return array("timetable" => $result, "fresh" => true);;
	}
	else {
		return array("timetable" => $result, "fresh" => isset($result['timetable']['a']['mon'][0]));
	}
}

function db_store_data($email, $timetable) {
	$timetable = SQLite3::escapeString(json_encode($timetable));
	$email = SQLite3::escapeString($email);
	return $handle->exec("UPDATE timetable SET timetable=\"$timetable\" WHERE email=\"$email\"");
}
