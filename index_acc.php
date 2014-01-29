<!--
    SBHS-Timetable Copyright (C) James Ye, Simon Shields 2014

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
-->
<div id="sidebar">
	<div id="user-info">
		<?php
			echo "<span class='nomobile'>Logged in as</br></span>";
			echo $results['email'];
		?><br />
		<a href="/login.php?logout">Logout</a> <span style="font-size: 14px;">&#9679;</span> <a href="/timetable.php">My timetable</a>
<!--		<span style="font-size: 14px;">&#9679;</span> <a href="/notices/dailynotices.php">Today's Notices</a>-->
	</div>
	<div id="next-info"></div>
</div>
<span id="period-name"></span><br />
<span id="in">in</span><br />
<span id="countdown"></span>
<div id="slideout-left" class="slideout"></div>
<div id="slideout-left-arrow" class="arrow" onclick="slideOutLeft()"></div>
<div id="slideout-right-arrow" class="arrow" onclick="slideOutRight()"></div>
<div id="slideout-right" class="slideout"></div>
