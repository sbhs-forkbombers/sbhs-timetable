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
// this is the RESTful API for SBHS Timetable.
// All functions should return a PHP object that can be encoded into JSON or (potentially) XML.
// TODO API docs
ini_set('include_path', ini_get('include_path') . ":" . $_SERVER['DOCUMENT_ROOT'] . ":" . $_SERVER['DOCUMENT_ROOT'] . "/gapi");
require_once("Google/Client.php");
require_once("Google/Service/Oauth2.php");
require_once('common.php');
require_once("api/RESTResponse.class.php");

class TimetableAPI extends RESTResponse {
    protected $SID;
    protected $email;
    
    public function __construct($req) {
        parent::__construct($req);
		if (isset($_COOKIE['PHPSESSID']) && !isset($this->request['sid'])) {
			$this->request['sid'] = $_COOKIE['PHPSESSID'];
		}
    }
    
    protected function authenticate() {
        if ($this->method == 'GET' || $this->method == 'POST') {
            // authenticate, and return a session ID.
            session_start();
            //$token = $this->request->
            $client = get_client();
            $client->authenticate($request['token']);
            $service = new Google_Service_Oauth2($client);
            $results = array();
            try {
                $results = $service->userinfo_v2_me->get();
            }
            catch (Exception $e) {
                error_log("EXCEPTION: " . $e->getMessage() . "\n");
                // the token has probably expired. - but this isn't browser-based, so not much we can do other than complain
                return array("error" => "Error getting user email address");
            }
            $_SESSION['email'] = $results["email"];
            $_SESSION['access_token'] = $client->getAccessToken();
            $result = array();
            $result["sid"] = session_id();
            return $this->request;
        }
        else {
            return "Unmodifiable resource.";
        }
    }

    protected function timetable() {
        if ($this->method == 'GET' || $this->method == 'POST') {
            if (!isset($this->request['sid'])) {
                //$this->_response("Need to authenticate first", 403);
                return array("error" => "Need to authenticate first.");
            }
            session_id($this->request['sid']);
            session_start();
            if (!isset($_SESSION['email'])) {
                //$this->_response("Invalid session ID", 500);
                return array("error" => "Invalid Session ID");
            }
            $email = $_SESSION['email'];
            $result = db_get_data_or_create($_SESSION['email']);
			if ($this->verb == 'get') {
				return array("timetable" => json_decode($result['timetable']['timetable']), "year" => $result['timetable']['year']);
//                return json_decode($result['timetable']);
			}
			else if ($this->verb == 'put') {
				if (!isset($this->request['data'])) {
					return array('error' => 'Need a new timetable');
				}
				$object = json_decode($this->request['data'], true);
				$OK = true;
				if (!is_array($object)) {
					return array('error' => 'Invalid JSON input');
				}
				// input sanitisation
				foreach (array("a","b","c") as $wk) {
					if (!isset($object[$wk])) {
						$OK = false;
						break;
					}
					foreach (array("mon","tue","wed","thu","fri") as $day) {
						if (!isset($object[$wk][$day])) {
							$OK = false;
							break 2;
						}
						for ($i = 0; $i < 5; $i++) {
							if (!isset($object[$wk][$day][$i])) {
								$OK = false;
								break 3;
							}
						}
					}
				}
				if (!$OK) {
					return array("error" => "Incomplete timetable input");
				}
				// well, all seems good. For science!
				$year = db_get_data_or_create($email)["year"];
				db_store_data($email, $this->request['data'], $year);
				return array('success' => 'Timetable updated');
			}
			return array('error' => 'Unknown verb');
            
		}
        else {
            return "Bad method.";
        }
	}

	protected function diary() {
		if ($this->method == "GET" || $this->method == "POST") {
			if (!isset($this->request['sid'])) {
				return array('error' => 'Need to authenticate first.');
			}
			session_id($this->request['sid']);
			session_start();
			if (!isset($_SESSION['email'])) {
				return array('error' => 'Invalid Session ID');
			}

			$email = $_SESSION['email'];
			if ($this->verb == 'get') {
				return json_decode(db_get_diary_or_create($email));
			}
			else if ($this->verb == 'put') {
				if (!isset($this->request['data'])) {
					return array('error' => 'Need a new diary!');
				}
				$obj = json_decode($this->request['data'], true);
				if (!is_array($obj)) {
					return array('error' => 'Invalid JSON input');
				}
				$OK = true;
				foreach ($obj as $el) {
					foreach (array("name","subject","notes","due","duePeriod","done") as $t) {
						if (!isset($el[$t])) {
							$OK = false;
							break 2;
						}
					}
				}
				if (!$OK) {
					return array('error' => 'Each diary entry needs name, subject, notes, due, duePeriod and done entries');
				}
				// good enough
				db_store_diary($email, $this->request['data']);
				return array('success' => 'Diary updated');
			}
			return array('error' => 'Invalid verb');
		}
		return array('error' => 'Invalid method');
	}

	protected function notices() {
		if ($this->method == 'GET') {
			$CODIFY = true;
			$DATE = $this->request['date'];
			include('notices/dailynotices.php');
		}
	}

			
}


try {
    $API = new TimetableAPI($_REQUEST['request']);
    echo $API->processAPI();
} catch (Exception $e) {
    echo json_encode(Array('error' => $e->getMessage()));
}
