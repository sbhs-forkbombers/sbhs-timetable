<?php
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


function db_get_data_or_create($email) {
	$handle = new SQLite3("/srv/http/timetable/.httimetable.db");
	global $timetable_structure;
	$result = $handle->querySingle('SELECT * FROM timetable WHERE email="' . SQLite3::escapeString($email) . '"', true);
	if ($result === false) {
		echo "FATAL ERROR - INVALID QUERY. ";
		echo $handle->lastErrorCode() . " - " . $handle->lastErrorMsg() . "\n";
		$handle->close();
	}
	else if (!isset($result['email'])) {
		// create an entry
		$handle->exec("INSERT OR REPLACE INTO timetable VALUES (\"" . SQLite3::escapeString($email) . "\", '" . SQLite3::escapeString(json_encode($timetable_structure)) . "');");
		$result = $handle->querySingle('SELECT * FROM timetable WHERE email="' . SQLite3::escapeString($email) . '"', true);
		$handle->close();
		return array("timetable" => $result, "fresh" => true);;
	}
	else {
		$handle->close();
		$decoded = json_decode($result['timetable']);
		//var_dump($decoded);
		$fresh = (count($decoded->a->mon[0]) == 0) ;
		
		return array("timetable" => $result, "fresh" => $fresh);
	}
}

function db_store_data($email, $timetable) {
	$handle = new SQLite3("/srv/http/timetable/.httimetable.db");
	$timetable = SQLite3::escapeString(json_encode($timetable));
	$email = SQLite3::escapeString($email);
	$handle->exec("UPDATE timetable SET timetable='$timetable' WHERE email=\"$email\"");
	$handle->close();
}
