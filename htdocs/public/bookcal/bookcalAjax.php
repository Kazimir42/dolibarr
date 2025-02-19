<?php
/*
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 *	\file       /htdocs/public/bookcal/bookcalAjax.php
 *	\brief      File to make Ajax action on Book cal
 */

if (!defined('NOTOKENRENEWAL')) {
	define('NOTOKENRENEWAL', '1'); // Disables token renewal
}
if (!defined('NOREQUIREHTML')) {
	define('NOREQUIREHTML', '1');
}
if (!defined('NOREQUIREAJAX')) {
	define('NOREQUIREAJAX', '1');
}
if (!defined('NOREQUIRESOC')) {
	define('NOREQUIRESOC', '1');
}
// If there is no need to load and show top and left menu
if (!defined('NOREQUIREMENU')) {
	define('NOREQUIREMENU', '1');
}
if (!defined("NOLOGIN")) {
	define("NOLOGIN", '1');
}
if (!defined('NOBROWSERNOTIF')) {
	define('NOBROWSERNOTIF', '1');
}

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

$action = GETPOST('action', 'aZ09');
$id = GETPOST('id', 'int');
$datetocheckbooking = GETPOST('datetocheck', 'int');
$error = 0;

// Security check
/*if (!defined("NOLOGIN")) {	// No need of restrictedArea if not logged: Later the select will filter on public articles only if not logged.
	restrictedArea($user, 'knowledgemanagement', 0, 'knowledgemanagement_knowledgerecord', 'knowledgerecord');
}*/

$result = "{}";


/*
 * Actions
 */

top_httphead('application/json');


if ($action == 'verifyavailability') {
	$response = array();
	if (empty($id)) {
		$error++;
		$response["code"] = "MISSING_ID";
		$response["message"] = "Missing parameter id";
		header('HTTP/1.0 400 Bad Request');
		echo json_encode($response);
		exit;
	}
	if (empty($datetocheckbooking)) {
		$error++;
		$response["code"] = "MISSING_DATE_AVAILABILITY";
		$response["message"] = "Missing parameter datetocheck";
		header('HTTP/1.0 400 Bad Request');
		echo json_encode($response);
		exit;
	}

	// First get all ranges for the calendar
	if (!$error) {
		// TODO Select in database all availabilities

		// TODO Select also all not available ranges

		// Build the list of hours available (key = hour, value = duration)
		$hour = '08'; $min = '00';
		$response["availability"][$hour.":".$min] = 30;
	}

	// Now get ranges already reserved
	// TODO Remove this
	if (!$error) {
		$datetocheckbooking_end = dol_time_plus_duree($datetocheckbooking, 1, 'd');

		$sql = "SELECT b.datep, b.id";
		$sql .= " FROM ".MAIN_DB_PREFIX."actioncomm as b";
		$sql .= " WHERE b.datep >= '".$db->idate($datetocheckbooking)."'";
		$sql .= " AND b.datep < '".$db->idate($datetocheckbooking_end)."'";
		$sql .= " AND fk_bookcal_availability IN (SELECT rowid FROM ".MAIN_DB_PREFIX."bookcal_availabilities WHERE fk_bookcal_calendar = ".((int) $id).")";
		//$sql .= " AND b.transparency"
		$resql = $db->query($sql);
		if ($resql) {
			$num = $db->num_rows($resql);
			$i = 0;
			$response = array();
			$response["content"] = array();
			while ($i < $num) {
				$obj = $db->fetch_object($resql);
				$dateobject = $obj->datep;
				$dateobject = explode(" ", $dateobject)[1];
				$dateobject = explode(":", $dateobject);

				$dateobjectstring = $dateobject[0].$dateobject[1];

				$response["content"][] = $dateobjectstring;
				$i++;
			}
			if ($i == 0) {
				$response["code"] = "NO_DATA_FOUND";
			} else {
				$response["code"] = "SUCCESS";
			}
		} else {
			dol_print_error($db);
		}
	}
	$result = $response;
}


/*
 * View
 */

echo json_encode($result);
