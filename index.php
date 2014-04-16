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
	echo "<script defer type='application/javascript'>";
	include "./belltimes.js.php";
	echo "</script>";
	echo "</head>";
	echo "<body><div class='wrapper'>";
	echo "<a href='/faq.php' id='faq-link' title=\"the link doesn't jiggle!\">Such Questions</a>";
	include "./index_acc.php";
}
else {
	// TODO some kind of notice saying what email will be used for, etc.
	include("./header.php");
	echo "<script defer src='./belltimes.js.php' type='application/javascript'></script>";
	echo "</head>";
	echo "<body><div class='wrapper'>";
	echo "<div id=\"nojs\"><noscript>You need a Javascript-enabled browser to use this site.</noscript></div>"; 
	echo "<a href='/faq.php' id='faq-link' title=\"The link doesn't jiggle!\">Such Questions</a>";
	include "./index_new.php";
}

if ($_SERVER["HTTP_HOST"] == "dev.sbhstimetable.tk" || $_SERVER["HTTP_HOST"] == "devel.sbhstimetable.tk") {
echo "<div id=\"debug\" style=\"position:fixed;top:2px;left:2px;color:#ff4444;font-family:'Roboto Condensed';font-size:16px;\" class='nomobile'>DEVELOPMENT NOTICE<br />This site may not function as intended<br /><a href='http://sbhstimetable.tk'>Use the site that actually works</a></div>";
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
<!--<div id='chrome-install-instructions'><h1>How to install on chrome</h1>
When the extension has finished downloading, click <a href="about:extensions" target="_blank">Here</a> (opens in new tab) and drag and drop the downloaded file into the extensions section.
Then, open <a href="about:apps" target="_blank">The apps screen</a> and you should see a link to the extension!
</div>-->
<div id="ie9-warn"><!--<strong>Hey there</strong>! You seem to be using Internet Explorer 9. For a better experience, you\'ll need to upgrade. <a href="/ie9_faq.php">Read more...</a> <a href="javascript:void(0)" onclick="dismissIE9()">Dismiss</a>--></div>
<div id="old-ie-warn"><!--<strong>You\'re running an old version of Internet Explorer.</strong> We recomend that you upgrade to a newer version of IE, or <a href="http://firefox.com">Firefox</a> or <a href="http://chrome.google.com">Google Chrome</a>. This website will not work on Internet Explorer 8 or older!--></div>
<div id="feedback"><a href="https://docs.google.com/forms/d/1z7uAIRsPjDTQxevO1R5GFn4OrETeHuZ0j2jzBcg3UKM/viewform">Much complain.</a></div>
<div id='darkener'></div>
<div id='slideout-top' class='long-slideout'></div>
<div id='slideout-top-arrow' class='arrow' ><img class='nomobile' src="http://d1e3ezyatlol8u.cloudfront.net/img/212/doge-sunglasses-212.gif" /></div><!--<div id='notices-notice'>Click here to save your homework (new!)</div>-->
<div id="slideout-bottom" class="long-slideout"></div>
<div id="slideout-bottom-arrow" class="arrow" onclick="slideOutBottom()"><img class='nomobile' style='bottom: 0; position: absolute;' src="http://d1e3ezyatlol8u.cloudfront.net/img/212/doge-rainbow-212.gif" /></div>
<div id="centered-bottom"><a href="http://sbhstimetable.tk">Wow. Many doge. Such ugly</a></div>
<div id='swipe-info'>Wow. Very swipe. Many things.</div>
<div id='redesign-popover'>
<h1>Wow. Such redesign.</h1>
Many improvement. Such user-friendliness. So redesign. Much looks.<br />
Very change. So demanded.<br /><br />
<a href="javascript:void(0)" onclick="window.localStorage.seen = true; $('#redesign-popover').fadeOut()">Such dismiss</a>
</div>
<div id='expando-wrapper'>
<a onclick='toggleExpando()'><img id='expand-countdown' alt='expand' src='/expand.png' /><img id='collapse-countdown' alt='collapse' src='/collapse.png' class='hidden' /></a>
</div>
</div>
</body>
</html>
