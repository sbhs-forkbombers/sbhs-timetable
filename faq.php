<?php
include("./header.html");
?>
<script>
function toggleHeader(id) {
	if ($(id).hasClass("expanded")) {
		$(id + "-ans").css({"display": "none"});
		$(id).removeClass("expanded");
	}
	else {
		$(id + "-ans").css({"display": "block"});
		$(id).addClass("expanded");
	}
}
</script>
</head>
<body>
<h1>FAQ</h1>
<div onclick="toggleHeader('#faq-1')" style="border:1px solid white; text-align: center; position: absolute; width: 98%; margin-left: 1%; margin-right: 1%" id="faq-1" >
<span style="font-family: Roboto Slab; font-size: 30px; ">About this site</span><br /><br />
<div id="faq-1-ans" style="display:none; font-family: Roboto; font-size: 18px; margin: 10px; text-align: left;">
This site is the spiritual successor to <a href='http://sbhsbelltimes.tk/'>http://sbhsbelltimes.tk</a>
<br />
The code can be found on <a href='https://github.com/sbhs-forkbombers/sbhs-timetable'>GitHub</a>
</div>
</span>
</div>
</body>
</html>
