<?php
session_start();
set_include_path("gapi");
require_once("Google/Client.php");
require_once("Google/Service/Oauth2.php");
require_once("./common.php");

if (!(isset($_SESSION['access_token']) && $_SESSION['access_token'])) {
	header("Location: /login.php?refresh-token&urlback=diary.php");
}
$client = get_client();
$client->setAccessToken($_SESSION['access_token']);
$service = new Google_Service_Oauth2($client);
try {
	$results = $service->userinfo_v2_me->get();
}
catch (Exception $e) {
	error_log("EXCEPTION: " . $e->getMessage() . "\n");
	header("Location: /login.php?refresh-token&urlback=diary.php");
}
$email = $results['email'];
if ($email == "") {
	header("Location: /login.php?logout");
}
if (isset($_REQUEST['clear-data'])) {
	db_clear_diary($email);
}
$user_diary = db_get_diary_or_create($email);

if (isset($_REQUEST['update'])) {
	$new = $_REQUEST["json"];
	if (isset($_REQUEST['append'])) {
		$ud = json_decode($user_diary);
		array_push($ud, json_decode($new));
		$new = json_encode($ud);
	}
	db_store_diary($email, $new);
	echo "Ok";
	exit;
}

//if (isset($_REQUEST['raw-json'])) {
	header("Content-type: application/javascript; encoding=utf-8");
	echo $user_diary;
/*}

else {
	header("Content-type: text/plain");
	echo "// TODO";
}*/

