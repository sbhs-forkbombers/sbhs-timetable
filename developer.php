<?php
session_start();
include('./header.php');
?>
<style>
.doc-wrapper * {
	padding-left: 20px;
}
h2,.mono {
	font-family: monospace;
}
.mono {
	font-size: inherit;
	padding: 0;
}
</style>
<body>
<h1>Developer API Information</h1>
There is currently a REST-based developer API that is <strong>in beta</strong>.<br />
The root of the API is <strong style='font-family: monospace'>/api/v1</strong><br />
(note that adding <strong styl='font-family: monospace'>/plain</strong> to the end of any URL will change the response content-type to 'text/plain', without changing the actual content of the response)
<br /><br />
<strong>Want to try it?</strong> - your SID is: <span class='mono'><?php 
if (isset($_SESSION["email"])) { 
	echo session_id();
} else { 
	echo "<em>you need to be logged in to use the majority of the API</em>";
}?></span><br /><br />
Here are the available actions:
<hr />
<h2>timetable/get</h2>
<span class='doc-wrapper'>
<em>sid</em>=&lt;PHPSESSID&gt; - the user's PHP session ID.<br /><br />
<strong>return value</strong>: a JSON-encoded listing of the user's timetable OR a JSON object with an "error" key if an error occured.
</span>
<hr />
<h2>timetable/put</h2>
<span class='doc-wrapper'>
<strong>NOTE:</strong> this API should be used via POST requests<br />
<em>sid</em>=&lt;PHPSESSID&gt; - the user's PHP session ID.<br />
<em>data</em>=&lt;timetable&gt; - the user's timetable in JSON format (as returned by <span class='mono'>timetable/get</span>)<br /><br />
<strong>return value</strong>: a JSON object with a "success" key if the operation was successful, otherwise one with an "error" key.
</span>
<hr />
<h2>diary/get</h2>
<span class='doc-wrapper'>
<em>sid</em>=&lt;PHPSESSID&gt; - the user's PHP session ID.<br /><br />
<strong>return value</strong>: a JSON-encoded listing of the user's diary entries, encoded as follow:
<pre>
DIARY = [
{
&nbsp;"name": "name",
&nbsp;"subject": "subject",
&nbsp;"notes": "notes", // a collection of additional notes that are part of the entry.
&nbsp;"due": "YYY-MM-DD",
&nbsp;"duePeriod": 1|2|3|4|...,
&nbsp;"done": true|false
}, ...
]
</pre>
The return value may also be an object with an "error" key.
</span>
<hr />
<h2>diary/put</h2>
<span class='doc-wrapper'>
<strong>NOTE:</strong> this API should be used via POST requests<br />
<em>sid</em>=&lt;PHPSESSID&gt; - the user's PHP session ID.<br />
<em>data</em>=&lt;diary&gt; - the user's diary in JSON format (as returned by <span class='mono'>diary/get</span>)<br /><br />
<strong>return value</strong>: a JSON object with a "success" key if the operation was successful, otherwise one with an "error" key.
</span>
<hr />
<h2>notices</h2>
<span class='doc-wrapper'>
<em>date</em>=YYYY-MM-DD <em>optional</em> - the date to get notices for. Note that notices are only stored for the <strong>current year</strong> and are purged at the end of each year.<br /><br />
<strong>return value</strong>: a JSON object with three keys. The first, 0, contains an array of all the notices.<br />
In each object in this array are five keys: 
<ul style='margin-left: 10px; padding: 0'>
<li><strong>title</strong> - the title of the notice</li>
<li><strong>applicability</strong> - a nicely-formatted version of the years the notice is for (appropriate for display)</li>
<li><strong>content</strong> - contains the actual text of the notice (in HTML). The following classes may be styled: <span class='mono'>meeting-details datetime title data date time location</span>. Note that &lt;p&gt; is used a lot, and you may wish to replace these with line breaks.</li>
<li><strong>author</strong> - the person who is responsible for the submission of the notice.</li>
<li><strong>years</strong> - an array of the years this is applicable for.</li>
</ul>
The second, 1, contains a list of years (7, 8, 9, 10, 11, 12, Staff) and indexes in the first array that are applicable for it.<br />
There is an "error" key that will have a contents if an error occurred.
</span>

</body>
</html>
