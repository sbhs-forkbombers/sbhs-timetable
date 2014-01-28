<?php

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