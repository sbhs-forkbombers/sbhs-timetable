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
include("./header.php");
?>
<style>
body {
	position: fixed;
	width: 100%;
}
.faq {
	border: 1px solid white;
	width: 99%;
	left: -5px;
	text-align: center;
	position: relative;
	cursor: pointer;
/*	-webkit-transition: all 500ms ease;
	-moz-transition: all 500ms ease;
	-ms-transition: all 500ms ease;
	-o-transition: all 500ms ease;
	transition: all 500ms ease;
*/
}

.faq-ans {
	font-family: Roboto; 
	font-size: 18px; 
	margin: 10px; 
	text-align: left;
/*	-webkit-transition: all 500ms ease;
	-moz-transition: all 500ms ease;
	-ms-transition: all 500ms ease;
	-o-transition: all 500ms ease;
	transition: all 500ms ease;
*/
}

#back {
	position: fixed;
	top: 0; left: 0;
	padding: 4px;
	background-color: #33b5e5;
	border-radius: 0 0 5px 0;
}

#back:hover {
	color: #33b5e5;
	background-color: white;
}
</style>
</head>
<body>
<h1>FAQ</h1>
<a href="/" id="back">Home</a>
<div onclick="toggleHeader('#faq-1')" class="faq" id="faq-1">
<span style="font-family: Roboto Slab; font-size: 30px; ">About this site</span><br /><br />
<div id="faq-1-ans" class="faq-ans" >
This site is the spiritual successor to <a href='http://sbhsbelltimes.tk/'>http://sbhsbelltimes.tk</a>
<br />
The code can be found on <a href='https://github.com/sbhs-forkbombers/sbhs-timetable'>GitHub</a>
<br />
Improvements include:
<ul>
<li><strong>Integrated timetable</strong> - on the front page, you can see what class you've got next, what you've got tomorrow, and what the belltimes are for today</li>
<li><strong>Designed with UX in mind</strong> - No annoying jiggly links, no attention-grabbing popups. Just a plain, clean look.</li>
<li><strong>Intuitive touch-screen support</strong> - Why click when you can swipe? On your phone or tablet, swipe from the left and right of the screen to view more information</li>
</ul>
</div>
</div>

<div onclick="toggleHeader('#faq-2')" class="faq" id="faq-2">
<span style="font-family: Roboto Slab; font-size: 30px; ">Why does this site look bad / not work on my computer?</span><br /><br />
<div id="faq-2-ans" class="faq-ans" >
It's probably your computer and web browser. You should switch to the latest version of either <a href="https://www.google.com/chrome">Google Chrome</a> or <a href="https://www.mozilla.org/firefox">Mozilla Firefox</a>. If you're unfortunate and on a DER-loaned laptop and don't want to violate your user / loan charter, you can update to the latest Internet Explorer by clicking "Check online for updates from Windows Update", underneath "Managed by your system administrator" in the Windows Update section of Control Panel.
<br />
</div>
</div>

<div onclick="toggleHeader('#faq-3')" class="faq" id="faq-3">
<span style="font-family: Roboto Slab; font-size: 30px;">Legal, standard, boring stuff</span><br /><br />
<div id="faq-3-ans" class="faq-ans">
This project is licensed under the GNU Affero General Public License. It can be found <a href="/COPYING">here</a> or at the <a href="//gnu.org/licenses/agpl-3.0.txt">GNU website</a>
This project also uses other third-party libraries available under other licenses. These are:
<ul>
<li>jQuery - MIT </li>
<li>Modernizr - MIT </li>
<li>WebFont Loader - Apache 2.0</li>
<li>Datejs - MIT </li>
<li>spin.js - MIT </li>
<li>yepnope.js - WTFPL</li>
</ul>
See the individual projects for their license notices.<br>
Full text for the: <a href="http://mit-license.org">MIT License</a> | <a href="//www.apache.org/licenses/LICENSE-2.0.txt">Apache 2.0 License</a> | <a href="http://www.wtfpl.net/txt/copying/">WTF Public License</a>
</div>
</div>

<div onclick="toggleHeader('#faq-4')" class="faq" id="faq-4">
<span style="font-family: Roboto Slab; font-size: 30px;">Help, support, complaints, else</span><br /><br />
<div id="faq-4-ans" class="faq-ans">
For all quick questions and suggestions: <a href="http://webchat.freenode.net?channels=%23%23sbhstimetable">webchat</a> (not allowed by DEC, you can use your whatever censorship evasion)
<br /><br />
If you want to give detailed feeback or receieve a detailed reply, please complete this form: <a href="https://docs.google.com/forms/d/1z7uAIRsPjDTQxevO1R5GFn4OrETeHuZ0j2jzBcg3UKM/viewform">feedback</a>
<script>
function toggleHeader(id) {
	$(id+"-ans").slideToggle();
}

$('.faq-ans').slideUp();
</script>
</body>
</html>
