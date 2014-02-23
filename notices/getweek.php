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

function getWeek() {
	if (!file_exists('week') || date('W', filemtime('week')) != date('W')) {
		$ch = curl_init("https://student.sbhs.net.au/api/timetable/bells.json");
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$json = curl_exec($ch);
		if ($json == false) { // try and reload next time.
			return "Unknown";
		}
		else {
			$js = json_decode($json);
			$week = $js->week . $js->weekType;
			file_put_contents('week', $week);
			return $week;
		}
	}
	else {
		return file_get_contents('week');
	}
}


?>
