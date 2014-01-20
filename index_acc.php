<body>
<div id="sidebar">
	<div id="user-info">
		Logged in as<br />
		<?php echo $results['email'] ?>
		<br />
		<a href="/login.php?logout">Logout</a><span style="font-size: 20px;">&nbsp;&middot;&nbsp;</span><a href="/timetable.php">My timetable</a>
	</div>
</div>
<span id="period-name"></span><br />
<span id="in">in</span><br />
<span id="countdown" />
</body>
</html>
