<div id="sidebar">
	<div id="user-info">
		Logged in as<br />
		<?php echo $results['email'] ?>
		<br />
		<a href="/login.php?logout">Logout</a><span style="font-size: 20px;">&nbsp;&middot;&nbsp;</span><a href="/timetable.php">My timetable</a>
	</div>
	<div id="next-info"></div>
</div>
<span id="period-name"></span><br />
<span id="in">in</span><br />
<span id="countdown"></span>
<div id="slideout-left" class="slideout"></h2></div>
<div id="slideout-left-arrow" class="arrow" onclick="slideOutLeft()"></div>
<div id="slideout-right-arrow" class="arrow" onclick="slideOutRight()"></div>
<div id="slideout-right" class="slideout"></h1></div>
</body>
