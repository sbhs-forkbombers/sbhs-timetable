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

$HTML = !isset($_REQUEST['codify']);
if (!$HTML) {
	if (!isset($_REQUEST['callback'])) {
		header("Content-type: application/json; charset=utf-8");
	}
	else {
		header("Content-type: application/javascript; charset=utf-8");
	}
}
else {
	header("Content-type: text/html; charset=utf-8");
}
$date = "";
if (isset($_REQUEST["date"])) {
	$date = $_REQUEST["date"];
}
else {
	$date = date('Y-m-d');
}
$yesterday = date('Y-m-d', strtotime($date . ' -1 day'));
$tomorrow  = date('Y-m-d', strtotime($date . ' +1 day'));

//error_reporting(0);
function dnl2Array($nodelist) {
	$ret = array();
	for ($i = 0; $i < $nodelist->length; $i++) {
		$ret[] = $nodelist->item($i);
	}
	return $ret;
}

function getPrettyRange($years) {
	$ys = "Year";
	$ysSuffix = "";
	$rangeStart = -1;
	$rangeLast = -1;
	$rangeEnd = -1;
	$seen = 0;
	sort($years);
	foreach ($years as $year) {
#		echo "$year $rangeStart $seen $rangeLast $rangeEnd\n";
		$seen++;
		if ($year != "Staff") {
			$year = (int) $year;
		}
		else {
			$ysSuffix .= " and Staff";
			$seen--;
			continue;
		}
		if ($year != "Staff" && $rangeStart == -1) {
			$rangeStart = $year;
			$rangeLast = $year;
			$rangeEnd = -1;
		}
		else if ($year - 1 != $rangeLast) {
			$rangeEnd = $rangeLast;
			if ($seen == 2) {
#				echo "single year: $rangeStart\n";
				$seen = 1;
				$ys .= ($ys == "Year" ? "" : ",") . " $rangeStart";
				$rangeStart = $year;
			}
			else {
#				echo "combo: $rangeStart - $rangeEnd";
				$ys .= ($ys == "Year" ? "s" : ",") . " $rangeStart - $rangeEnd";
				$seen = 1;
				$rangeStart = $year;
			}
			$rangeEnd = -1;
			$rangeLast = $rangeStart;
		}
		else if ($year -1 == $rangeLast) {
			$rangeLast = $year;
			continue;
		}
	}
	if ($rangeEnd != -1 && $rangeStart != -1) {
		$ys .= ($ys == "Year" ? "s" : ",") . " $rangeStart - $rangeEnd";
	}
	else if ($rangeStart != -1 && $rangeLast != -1 && $rangeLast != $rangeStart) {
		$ys .= ($ys == "Year" ? "s" : ",") . " $rangeStart - $rangeLast";
	}
	else if ($rangeEnd == -1 && $rangeStart != -1) {
		$ys .= ($ys == "Year" ?  "" : ",") . " $rangeStart";
	}
	else if ($rangeStart > 0 && $seen == 1) {
		$ys .= ", $rangeStart";
	}
	else if ($ysSuffix != "" && $ys == "Year") {
		$ys = "Staff";
		$ysSuffix = "";
	}
	$ys .= $ysSuffix;
	return $ys;

}

function getElementsByClassName(DOMDocument $doc, $class) {
	$els = $doc->getElementsByTagName("*");
	$matched = array();
	$els1 = dnl2Array($els);
	$els = $els1;
	foreach ($els as $node) {
		if (!$node->hasAttribute('class')) {
			continue;
		}
		$classAttr = $node->getAttribute('class');
		if (!$classAttr) {
			continue;
		}
		$classes = preg_split('/ /', $classAttr);
		if (in_array($class, $classes)) {
			$matched[] = $node;
		}
	}
	return $matched;
}





$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://staff.sbhs.net.au/dailynotices/?view=list&type=popup&nosound=1&date=$date");
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

$html = curl_exec($ch);

$err = curl_error($ch);

curl_close($ch);
$ddoc = new DOMDocument("", "utf-8");
$ddoc->loadHTML($html);

$by_year = array(
	"12" => array(),
	"11" => array(),
	"10" => array(),
	"9"  => array(),
	"8"  => array(),
	"7"  => array(),
	"Staff" => array()
);
$notices = array();

$notice_html = getElementsByClassName($ddoc, "notice-brace");
$notice_html = $notice_html[1];//->childNodes->item(1);

$ary = dnl2Array($notice_html->childNodes);
error_reporting(E_ALL);
foreach ($ary as $el) {
	
	//$years = preg_split("/ /", preg_replace("/year-/", "", $el->getAttribute("class")));
	$chld = $el->childNodes->item(2);
	$j = $el->childNodes->item(0)->childNodes->item(3)->textContent;
	$years = preg_split("/\s+/", trim($j));
	$data = array();
	$data["title"] = trim(preg_replace("/\s+/", " ", $chld->childNodes->item(1)->textContent));
#	echo $data["title"] . "\n";
	$content = "";
	$ccnodes = $chld->childNodes->item(3)->childNodes;
	for ($i = 0; $i < $ccnodes->length-2; $i++) {
		if (get_class($ccnodes->item($i)) == "DOMText") {
			continue;
		}
		$content .= trim(preg_replace("/\s+/", " ", $ccnodes->item($i)->C14N()));
	}
	$data["applicability"] = getPrettyRange($years);
	$data["content"] = $content;
	$data["author"] = trim($ccnodes->item($ccnodes->length-2)->textContent);
		foreach ($years as $j) {
			array_push($by_year[$j],$id);
		}
	$data["years"] = $years;
	array_push($notices,$data);
	$id = sizeof($notices) - 1;




}
error_reporting(E_NOTICE);
if (!$HTML) {
	$json = "";
//	if (!$err) {
		$json = json_encode(array($notices, $by_year, "error" => $err)) ;
//	}
//	else {
//		$json = "[\"error\": \"$err\"]";
//	}
	echo (isset($_REQUEST['callback']) ? $_REQUEST['callback'] . '(' : '') . $json . (isset($_REQUEST['callback']) ? ');' : '');
	exit;
}
//echo $html;
require_once("Mobile_Detect.php");
$detect = new Mobile_Detect;
$MOBILE = $detect->isMobile();
if (isset($_REQUEST['mobile'])) {
	$MOBILE = true;
}
else if (isset($_REQUEST['nomobile'])) { // Q_Q
	$MOBILE = false;
}
set_include_path("..");
include "../header.php";
if ($MOBILE) {
?>
<meta name="viewport" content="width=device-width, user-scalable=yes" />
<style>
select {
	width: 95%;
	text-align: center;
	
	font-size: 30px;
}
table {
	table-layout: fixed;
	width: 100%;
	margin-right: 2px;
	margin-left: 2px;
}
.for {
	font-family: "Roboto Slab";
	font-size: 16px;
	width: 40px !important;
	height: 50px;
/*	vertical-align: middle;*/
	vertical-align: top;
	padding-top: 22px;
	padding-right: 5px;
}
</style>
<?php } ?>
<script async src="script.js"></script>
<link rel="stylesheet" href="notices.css" />
<script>
$(document).ready(function() {
	$('#what').on('change', updateSet);
});
function updateSet(a) {
	var val = document.getElementById("what").value;

	$('#no-notice').css({'display': 'none'});
	if (val=="") {
		$('.notice').css({'display': 'inline'});
		return;
	}
	$('.notice').css({'display': 'none'});
	$('.'+val).css({'display': 'table-row'});
	if ($('.'+val).length == 0) {
		$('#no-notice').css({'display': 'block'});
	}

}
expanded = false;
var ary = [];
total = <?php echo sizeof($notices)?>;
function toggleAll() {
//	if (expanded == 0) {
//		expanded = 1;
		for (id in ary) {
			if (ary[id] == expanded)
			toggle(id);
		}
		expanded = !expanded;
/*	}
	else {
		expanded = 0;
		for (i = 0; i < total; i++) {
			if (i in ary) {
				continue;
			}
			toggle(i);
		}
	}

/*	for (i=0; i< total; i++) {
		toggle(i);
	}
}
ary = [];
 */
}
function toggle(id) {
/*	if (id in ary) {
//		$('#'+id).css({'height': 0, 'opacity': 0, 'z-index': id });
		$('#'+id).css({'display': 'none'});
		$('.td'+id).css({"z-index": '-'+(200-id)});
		delete(ary[id]);
	}
	else {
		$('#'+id).css({'display': 'table-cell'});
//		$('#'+id).css({'height': 'initial', 'opacity': 1, 'z-index':2000});
		$('.td'+id).css({"z-index": 2});
		ary[id] = true;

}*/
	if (!id in ary) {
		ary[id] = true;
	}
	else {
		ary[id] = (!ary[id]);
	}
	$('#'+id).slideToggle();
}

function requestFullScreen(element) {
	return;
    // Supports most browsers and their versions.
    var requestMethod = element.requestFullScreen || element.webkitRequestFullScreen || element.mozRequestFullScreen || element.msRequestFullScreen;

    if (requestMethod) { // Native full screen.
        requestMethod.call(element);
    } else if (typeof window.ActiveXObject !== "undefined") { // Older IE.
        var wscript = new ActiveXObject("WScript.Shell");
        if (wscript !== null) {
            wscript.SendKeys("{F11}");
        }
    }
}
function unFullScreen(element) {
	return; // this stops scrolling :(
    // Supports most browsers and their versions.
    var requestMethod = element.cancelFullScreen || element.webkitCancelFullScreen || element.mozCancelFullScreen || element.msCancelFullScreen;

    if (requestMethod) { // Native full screen.
        requestMethod.call(element);
    } else if (typeof window.ActiveXObject !== "undefined") { // Older IE.
        var wscript = new ActiveXObject("WScript.Shell");
        if (wscript !== null) {
            wscript.SendKeys("{F11}");
        }
    }
}

FULLSCREEN = false;
function fullscreen() {
	if (!FULLSCREEN) {
		requestFullScreen(document.body);
		$('#header').animate({'top': '-700px'});
		$('body').animate({'padding-top': 0});
		$('#footer').animate({'opacity': 0});
		$('#floating-controls').addClass('faded');
	}
	else {
		unFullScreen(document);
		$('#header').animate({'top': 0});
		$('body').animate({'padding-top': '120px'});
		$('#footer').animate({'opacity': 1});
		$('#floating-controls').removeClass('faded');
	}

	FULLSCREEN = !FULLSCREEN;
}
$(document).ready(function() {
<?php
if (isset($_REQUEST['scroll'])) {
	echo 'pageScrollStart();' ."\n";
}
if (isset($_REQUEST['fullscreen']) || $MOBILE) {
	echo "fullscreen();\n";
}
if (isset($_REQUEST['expand']) || $MOBILE) {
	echo "toggleAll();\n";
}
?>
toggleAll();
});

</script>
</head>
<body>
<?php include_once('getweek.php') ?>
<div id="header">
<h1 style="display: inline">Daily Notices</h1> <?php echo date('l') . ' ' . date('d/m/Y') . ' (' . 'Week ' . getWeek() . ')' ?><br />
<strong>Show notices for: </strong>

<?php 
$str = '<select onchange="updateSet" id="what"><option value="">Everyone!</option>';
foreach (array(7,8,9,10,11,12,"Staff") as $el) { 
	$str .= "<option value='notice-$el'>";
	if ($el == "Staff") { 
		$str .= "Staff";
	}
	else {
		$str .= "Year $el";
	}
	$str .= "</option>\n";
}
$str .= '</select>';
if (!$MOBILE) echo $str;
?>

</div>
<?php if ($MOBILE) {
	echo $str;
}
else { ?>
	<span id="floating-controls" style="position: fixed; top: 2px; right: 2px; text-align: right ">
	<a href="javascript:void(0)" onclick="toggleAll()">expand/collapse all notices</a><br />
	<a  href="javascript:void(0)" onclick="fullscreen()">fullscreen?</a><br />
	<a href="javascript:pageScrollToggle()">toggle scrolling</a><br />
</span>
<?php
}
?>
<div style="display: <?php if (sizeof($notices) > 0) { echo "none"; } else { echo "table-cell"; } ?>; position: fixed; height: 100%; width: 100%; vertical-align: middle; text-align: center; font-size: 48px;" id="no-notice">
<strong>(there are no notices)</strong>
<?php if ($err) { ?>
<br />
<span style="font-size: 16px">
<strong>but there is an error:</strong><Br />
<?php echo $err; ?>
<br /><strong>that's all I know.</strong></span>
<?php } ?>
</div>

<?php if (sizeof($notices) > 0) { ?>
	<table>
<?php
$i = 0;
	for ($i = 0; $i < sizeof($notices); $i++) {
		$years = "";
		$for = "";
		foreach (array_keys($by_year) as $els) {
			if (in_array($i, $by_year[$els])) {
				$years .= "notice-$els ";
				$for .= "$els ";
			}
		}
		$notice = $notices[$i];
		echo "<tr class='notice $years'><td class='for'><div class='legit'>".$notice['applicability']."</div></td>".
			"<td style='z-index: $i' class='message td$i' onclick='toggle($i)'>".
			"<div class='title'>".$notice['title'].
			"</div><div id='$i' class='notice-text'>".$notice['content']."<div class='author'>".$notice['author']."</div></div></td></tr>";
	}
	?>
	</table>
<?php } ?>
<span id="footer" style="position: fixed; bottom: 0; width: 100%; text-align: center; background-color: inherit;">
<a style="position:fixed; bottom: 0; left: 0; text-align: left" href="date=<?php echo $yesterday?>">yesterday's notices</a>
<a href="" title="today's notices (permalink)">today's notices </a><sup>permalink!</sup>
<span style="position:fixed; right: 0; text-align: right; bottom: 0">
<?php if (date('Y-m-d') != $date) {?>
<a href="?date=<?php echo $tomorrow?>">tomorrow's notices</a>
<?php } else { ?>
<a title="there are none." href="javascript:void(0)">tomorrow's notices</a>
<?php } ?>
</span>
</span>
<script>
for (i = 0; i < total; i++) {
	ary[i] = false;
}
</script>
<br /><br />
</body>
</html>
