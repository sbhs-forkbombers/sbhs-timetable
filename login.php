<?php
session_start();
set_include_path("gapi");
require_once "Google/Client.php";
require_once "./common.php";
$client = get_client();

if (isset($_GET['logout'])) {
	unset($_SESSION['access_token']);
	header("Location: ". $_SESSION['urlback']);
	exit;
}
if (isset($_GET['code'])) {
	$client->authenticate($_GET['code']);
	$_SESSION['access_token'] = $client->getAccessToken();
	header("Location: /");

}

if (isset($_SESSION['access_token']) && $_SESSION['access_token'] && !isset($_GET['refresh-token'])) {
	header("Location: /"); // erroneous login request
}
else {
	unset($_SESSION['access_token']);
	$_SESSION['urlback'] = "http://sbhstimetable.tk/";
	if (isset($_GET['urlback'])) {
		$redir = $_GET['urlback'];
		$_SESSION['urlback'] = "http://sbhstimetable.tk/$redir";
	}
	$authUrl = $client->createAuthUrl();
	header("Location: $authUrl");
	echo "Redirecting...";
}
?>
