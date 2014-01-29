<?php
/*
    SBHS-Timetable Copyright (C) James Ye, Simon Shields 2014

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
session_start();
set_include_path("gapi");
require_once "Google/Client.php";
require_once "./common.php";
$client = get_client();

if (isset($_GET['logout'])) {
	$_SESSION = array();
	$params = session_get_cookie_params();
	setcookie(session_name(), '', time() - 42000,
		$params["path"], $params["domain"],
		$params["secure"], $params["httponly"]
	);
	session_destroy();
	header("Location: /");
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
	$root = $_SERVER["HTTP_HOST"];
	$_SESSION['urlback'] = "http://$root/";
	if (isset($_GET['urlback'])) {
		$redir = $_GET['urlback'];
		$_SESSION['urlback'] = "http://$root/$redir";
	}
	if (isset($_GET['new-timetable'])) {
		$_SESSION['new-timetable'] = true;
	}
	$authUrl = $client->createAuthUrl();
	header("Location: $authUrl");
	echo "Redirecting...";
}
?>
