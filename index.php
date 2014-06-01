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
function crawlerDetect() {
	if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/bot|crawl|slurp|spider/i', $_SERVER['HTTP_USER_AGENT'])) {
		return TRUE;
	}
	else {
		return FALSE;
	}

}

if ($_SERVER["HTTP_HOST"] == "dev.sbhstimetable.tk" || $_SERVER["HTTP_HOST"] == "devel.sbhstimetable.tk") {
	if (crawlerDetect() || preg_match('/https?:\/\/(www)?.google.com/', $_SERVER["HTTP_REFERER"])) {
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: http://sbhstimetable.tk");
	}
}
# https://www.googleapis.com/userinfo/v2/me
session_start();
set_include_path("gapi");
require_once("Google/Client.php");
require_once("Google/Service/Oauth2.php");
require_once("./common.php");
$client = get_client();
unset($_SESSION['new-timetable']);
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
	// logged in
	$results = array();
	if (!isset($_SESSION['email'])) {
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
		$_SESSION['email'] = $results["email"];
	}
	$results["email"] = $_SESSION['email'];
	include("./header.php");
	echo "<script defer src='/belltimes.js' type='application/javascript'>";
//	include "./belltimes.js"; // TODO make this a proper script so it can be optimised
	echo "</script>";
	echo "</head>";
	echo "<body><div class='wrapper'>";
	echo "<a href='/faq.php' id='faq-link' title=\"the link doesn't jiggle!\">FAQ</a>";
	include "./index_acc.php";
}
else {
	// TODO some kind of notice saying what email will be used for, etc.
	include("./header.php");
	echo "<script defer src='./belltimes.js' type='application/javascript'></script>";
	echo "</head>";
	echo "<body><div class='wrapper'>";
	echo "<div id=\"nojs\"><noscript>You need a Javascript-enabled browser to use this site.</noscript></div>"; 
	echo "<a href='/faq.php' id='faq-link' title=\"The link doesn't jiggle!\">FAQ</a>";
	include "./index_new.php";
}

if ($_SERVER["HTTP_HOST"] == "dev.sbhstimetable.tk" || $_SERVER["HTTP_HOST"] == "devel.sbhstimetable.tk") {
	echo "<div id=\"debug\" style=\"position:fixed;top:2px;left:2px;color:#ff4444;font-family:'Roboto Condensed';font-size:16px;\" class='nomobile'>DEVELOPMENT NOTICE<br />This site may not function as intended<br /></div>";
}
/*echo "<script src=\"https://ajax.googleapis.com/ajax/libs/webfont/1.5.0/webfont.js\"></script>";
echo "<script>
  WebFont.load({
	google: {
	  families: ['Roboto:400,100,700', 'Roboto Condensed', 'Roboto Slab:400,700']
	}
  });
</script>";*/
//`echo '<div id="ohai-chrome"><a class="fake-button" href="/chrome/timetable.crx">Install the app!</a></div>';
?>
<div id="bells-changed"></div>
<div id="feedback"><a href="https://docs.google.com/forms/d/1z7uAIRsPjDTQxevO1R5GFn4OrETeHuZ0j2jzBcg3UKM/viewform">Feedback</a></div>
<div id='darkener'></div>
<div id='slideout-top' class='long-slideout'></div>
<div id='slideout-top-arrow' class='arrow' ></div><div id='notices-notice'>Click here to save your homework (new!)</div>
<div id="slideout-bottom" class="long-slideout"></div>
<div id="slideout-bottom-arrow" class="arrow" onclick="slideOutBottom()"></div>
<div id='swipe-info'>Swipe left or right to show more information...</div>
<div id="doge-notify"><a href="http://nodejs.sbhstimetable.tk">Is typing your timetable too much effort? Try this (beta!)</a><br /><a href="http://doge.sbhstimetable.tk/">Like Doge?</a>&nbsp;<sup><a id='nohover' href="javascript:void(0)" onclick="$('#doge-notify').fadeOut();">X</a></sup></div>
<div id='expando-wrapper'>
<a onclick='toggleExpando()'><img id='expand-countdown' alt='expand' src='/expand.png' /><img id='collapse-countdown' alt='collapse' src='/collapse.png' class='hidden' /></a>
</div>
</div>
</body>
</html>
