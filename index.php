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
# https://www.googleapis.com/userinfo/v2/me
session_start();
set_include_path("gapi");
require_once("Google/Client.php");
require_once("Google/Service/Oauth2.php");
require_once("./common.php");
$client = get_client();
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
	include("./header.php");
	echo "<script defer type='application/javascript'>";
	include "./belltimes.js.php";
	echo "</script>";
	echo "</head>";
	echo "<body>";
	echo "<a href='/faq.php' id='faq-link' title=\"the link doesn't jiggle!\">FAQ</a>";
	include "./index_acc.php";
}
else {
	// TODO some kind of notice saying what email will be used for, etc.
	include("./header.php");
	echo "<script defer src='./belltimes.js.php' type='application/javascript'></script>";
	echo "</head>";
	echo "<body>";
	echo "<div id=\"nojs\"><noscript>You need a Javascript-enabled browser to use this site.</noscript></div>"; 
	echo "<a href='/faq.php' id='faq-link' title=\"The link doesn't jiggle!\">FAQ</a>";
	include "./index_new.php";
}

if ($_SERVER["HTTP_HOST"] == "dev.sbhstimetable.tk") {
echo "<div id=\"debug\" style=\"position:fixed;top:2px;left:2px;color:#ff4444;font-family:'Roboto Condensed';font-size:16px;\">DEVELOPMENT NOTICE<br />This site may not function as intended</div>";
}
/*echo "<script src=\"https://ajax.googleapis.com/ajax/libs/webfont/1.5.0/webfont.js\"></script>";
echo "<script>
  WebFont.load({
    google: {
      families: ['Roboto:400,100,700', 'Roboto Condensed', 'Roboto Slab:400,700']
    }
  });
</script>";*/
echo "<div id='darkener'></div>";
echo "<div id='swipe-info'>Swipe left or right to show more information...</div>";
echo "</body>";
echo "</html>";
