<?php
# https://www.googleapis.com/userinfo/v2/me
session_start();
set_include_path("gapi");
require_once("Google/Client.php");
require_once("Google/Service/Oauth2.php");
require_once("./common.php");
$client = get_client();
echo "</head>";
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
	// logged in
	$client->setAccessToken($_SESSION['access_token']);
	$service = new Google_Service_Oauth2($client);
	try {
		$results = $service->userinfo_v2_me->get();
	}
	catch (Exception $e) {
		error_log("EXCEPTION: " . $e->getMessage() . "\n");
		# the token has probably expired.
		header("Location: /login.php?refresh-token");
	}
	include("./header.html");
	echo "<script src='/belltimes.js.php' type='application/javascript'></script></head>";
	include "./index_acc.php";
}
else {
	// TODO some kind of notice saying what email will be used for, etc.
	include("./header.html");
	echo "<script src='/belltimes.js.php' type='application/javascript'></script>";
	echo "</head>";
	include "./index_new.php";
}


