<!DOCTYPE html>
<html>
<head>
	<title>Daily Notices</title>
	<link rel="stylesheet" href="/font-face.css" />
	<link rel="stylesheet" href="/common.css" />
<script>

function go() {
	window.location = "dailynotices.php?date=" + 
		document.getElementById("year").value+"-"+document.getElementById("month").value+"-"+document.getElementById("day").value + "&pretty=yes";
}

</script>
<style>
<?php
if (isset($_REQUEST['devmode'])) {
?>
body {
	color: #15E615;
	font-family: monospace;
}
h1,h2,h3 {
	font-family: monospace;
}
strong {
	font-family: monospace;
	font-weight: bold;
}
input,select {
	color: inherit;
	background-color: #303630;
	border: none;
	font-family: monospace;
	margin-top: 5px;
}
a {
	color: #15E615;
}
a:hover {
	color: #0FA80F;
}
<?php
} 
else {
?>
input,select {
	color:inherit;
	background-color: #303630;
	border: 1px solid white;
	font-family: inherit;
	margin-top: 5px;
}
<?php
}
?>
td {
	padding: 10px;
}
</style>
</head>
<body>
<h1>View past Daily Notices</h1>
<input type="hidden" inputmode="numeric" id="year" name="year" placeholder="YYYY" value="<?php echo date('Y')?>"/>
<select id="day">
<?php for ($i = 1; $i < 32; $i++) { echo "<option value='$i'" . (((int)date('d')) == $i ? " selected" : "") . " >$i</option>"; } ?>
</select>
&nbsp;&nbsp;
/
&nbsp;&nbsp;
<!--<input type="text" inputmode="numeric" id="month" name="month" placeholder="MM" value="<?php echo date('m')?>"/>-->
<select id="month">
<?php for ($i = 1; $i < 13; $i++) { echo "<option value='$i'" . (((int)date('m')) == $i ? " selected" : "") . " >$i</option>"; } ?>
</select>
&nbsp;&nbsp;
/
&nbsp;&nbsp;
<?php echo date('Y'); ?>
<br /><br />
<!--<input type="text" inpurmode="numeric" id="day" name="day" placeholder="DD" value="<?php echo date('d')?>" /><br />-->

<input type="submit" value="Go!" onclick="go()"/>
<input type="submit" value="Today's notices" onclick="window.location='dailynotices.php?pretty=yes'" />
<br /><br />
<?php
if (isset($_REQUEST['devmode'])) {
?>
<a href="index.php" title="sanity!" style="position: fixed; bottom: 0; right: 0; font-size: 12px;">back to safety</a><br />
<h2>Developing for DailyNotices</h2>
<strong>querystring paramters</strong><br />
<table>
<tr><td>callback&#61;<em>js function</em></td><td>JS function to wrap JSON in</td></tr>
<tr><td>pretty&#61;<em>text</em></td><td>if this is set, JSON/JS output will be disabled and the content will be output as HTML</td></tr>
<tr><td>date&#61;<em>YYYY-MM-DD</em></td><td>date to show notices for</td></tr>
</table><br /><br />
<strong>json layout</strong><br /><br />
the json array returned consists of two elements.<br />
the first is an array of objects with keys <em>title</em>, <em>applicability</em>, <em>content</em> and <em>author</em>.<br />
<ul>
<li><strong>title</strong> is the title of the notice, for example, "Cricket training"</li>
<li><strong>applicability</strong> is the pretty form of who the notice is for, for example, "Year 8, 9"</li>
<li><strong>content</strong> is the <strong>html</strong> for the notice itself. While most HTML is merely &lt;p&gt;,
	 some notices use HTML for bold or bullet point lists.</li>
<li><strong>author</strong> is the author of the content - for example "S. Johnson"</li>
</ul>
<br />
the other element is an object in which the keys are year groups - 7, 8, 9, 10, 11, 12 and Staff.<br />
each of these keys' value is a list of indexes in the <em>first</em> array that are relevant to this year group.<br />
<br /><br /><br />
<?php
} else {
?>
<a href="?devmode=true" title="devmode!" style="position: fixed; bottom: 0; right: 0; font-size: 12px;">developer info</a><br />
<?php
}
?>
</body>
</html>
