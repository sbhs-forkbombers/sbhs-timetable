<?php
include("./header.html");
?>
<style>
body {
	position: fixed;
	width: 100%;
}
.faq {
	border: 1px solid white;
	width: 100%;
	text-align: center;
	position: relative;
}

.faq-ans {
	font-family: Roboto; 
	font-size: 18px; 
	margin: 10px; 
	text-align: left;
	height: 0;
}
</style>
</head>
<body>
<h1>FAQ</h1>
<div onclick="toggleHeader('#faq-1')" class="faq" id="faq-1">
<span style="font-family: Roboto Slab; font-size: 30px; ">About this site</span><br /><br />
<div id="faq-1-ans" class="faq-ans">
This site is the spiritual successor to <a href='http://sbhsbelltimes.tk/'>http://sbhsbelltimes.tk</a>
<br />
The code can be found on <a href='https://github.com/sbhs-forkbombers/sbhs-timetable'>GitHub</a>
</div>
</div>
<div onclick="toggleHeader('#faq-2')" class="faq" id="faq-2">
<span style="font-family: Roboto Slab; font-size: 30px; ">Why does this site look bad / not work on my computer, but works nicely on others?</span><br /><br />
<div id="faq-2-ans" style="display:none; font-family: Roboto; font-size: 18px; margin: 10px; text-align: left;">
It's probably your computer and web browser. You should switch to the latest version of either <a href="https://www.google.com/chrome">Google Chrome</a> or <a href="https://www.mozilla.org/firefox">Mozilla Firefox</a>. If you're unfortunate and on a DER-loaned laptop and don't want to violate your user / loan charter, you can update to the latest Internet Explorer by clicking "Check online for updates from Windows Update", underneath "Managed by your system administrator" in the Windows Update section of Control Panel.
<br />
</div>
</div>
<script>
function toggleHeader(id) {
	if ($(id).hasClass("expanded")) {
		$(id + "-ans").css({"height": "0"});
		$(id).removeClass("expanded");
	}
	else {
		$(id + "-ans").css({"height": "auto"});
		$(id).addClass("expanded");
	}
}
</script>
</body>
</html>
