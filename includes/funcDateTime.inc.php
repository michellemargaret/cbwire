<?php    	
	// Return true if date is valid (format YYYY-MM-DD)
	// Adapted from http://www.phpro.org/examples/Validate-Date-Using-PHP.html
	function validate_date($inDate){
		if (strlen($inDate) <> 10) {
			return false;
		}
		if (substr_count($inDate, '-') <> 2) {
			// There should be two occurrances of '-' in the date and there are not
			return false;
		}
        list( $y, $m, $d ) = preg_split( '/[-\.\/ ]/', $inDate );            
        return checkdate( $m, $d, $y );
    }
	
	// Return true if time is valid (between 1:00 and 12:59)
	function validate_time($inTime){		
		if (substr_count($inTime, ':') <> 1) {
			// There should be exactly one occurrance of ':' in the time and there are not
			 return false;
		}
        $arrTime = explode(":", $inTime);

		if (count($arrTime) <> 2) {  return false; }
		
		if (!is_numeric($arrTime[0]) || !is_numeric($arrTime[1])) { return false; }
		
		if ((strlen($arrTime[0]) < 1) || (strlen($arrTime[0]) > 2)) { return false; }
		
		if (strlen($arrTime[1]) <> 2) { return false; }
		
		if (($arrTime[0] < 1) || ($arrTime[0] > 12)) {  return false; }
		
		if (($arrTime[1] < 0) || ($arrTime[1] > 59)) {  return false; }
		
        return true;
    }
    
    
	
	// Take a string date in the form "YYYY-mm-dd hh:ii:ss" or "YYYY-mm-dd"
	// and convert to an array with six values 
	// If time is not included, three time values will equal 0
	//
	//	Parameter: string representation of date (and time)
	//
	//  Returns: array representation of date (and time)
	//
	function formatDateToArray($dateString) {
		$strHour = "";
		$strMinute = "";
		$strSecond = "";
		$strMonth = "";
		$strDay = "";
		$strYear = "";
		
		$intLength = strlen($dateString);
		
		if ($intLength == 10) {
			$strHour = "0";
			$strMinute = "0";
			$strSecond = "0";
			
			$strYear = substr($dateString, 0, 4);
			$strMonth = substr($dateString, 5, 2);
			$strDay = substr($dateString, 8, 2);			
		} elseif ($intLength == 19) {
			$strHour = substr($dateString, 11, 2);
			$strMinute = substr($dateString, 14, 2);
			$strSecond = substr($dateString, 17, 2);
			
			$strYear = substr($dateString, 0, 4);
			$strMonth = substr($dateString, 5, 2);
			$strDay = substr($dateString, 8, 2);			
		} else {
			return null;
		}
		
		if (!is_numeric($strYear) || !is_numeric($strMonth) || !is_numeric($strDay) || !is_numeric($strHour) || !is_numeric($strMinute) || !is_numeric($strSecond)) {
			return null;
		}
	
		return array("Y"=>$strYear,"m"=>$strMonth,"d"=>$strDay,"H"=>$strHour,"i"=>$strMinute,"s"=>$strSecond);
	}
		
	// Determine if first date is earlier or equal to second date
	// Dates input as arrays with six entries each: H, i, s, m, d, Y for Hour Minute Second Month Day Year
	// Date comparison adapted from http://www.phpf1.com/tutorial/php-date-compare.html
	function isDateEarlier($firstDate, $secondDate) {
		if (!is_array($firstDate) || !is_array($secondDate)) {
			return false;
		}
		if (!array_key_exists ("H", $firstDate) || !array_key_exists ("i", $firstDate) || !array_key_exists ("s", $firstDate) || 
			!array_key_exists ("m", $firstDate) || !array_key_exists ("d", $firstDate) || !array_key_exists ("Y", $firstDate) ||
			!array_key_exists ("H", $secondDate) || !array_key_exists ("i", $secondDate) || !array_key_exists ("s", $secondDate) || 
			!array_key_exists ("m", $secondDate) || !array_key_exists ("d", $secondDate) || !array_key_exists ("Y", $secondDate)) {
					return false;
		}

			$intFirst = mktime($firstDate["H"], $firstDate["i"], $firstDate["s"], $firstDate["m"], $firstDate["d"], $firstDate["Y"]);
			$intSecond = mktime($secondDate["H"], $secondDate["i"], $secondDate["s"], $secondDate["m"], $secondDate["d"], $secondDate["Y"]);
			
			$intDiff = $intFirst-$intSecond;
			
			if ($intDiff < 0) {
				return true;
			} 
			return false;
	}
        
        	// format_singledatetime accepts array of datetime values
	// and returns single datetime text string
	function format_singledatetime($result_row) {
		$strItemStartDate = "";
		$strItemStartDateR = "";
		$strItemEndDate = "";
		$strItemStartTime = "";
		$strItemEndTime = "";
		$strItemDateTime = ".";
		$strItemExpiry = "";
		$strItemRecursive = "";
		$strItemStartDayWeek = "";
		$strItemStartDayMonth = "";
		$strTemp = "";		
		
		if (!is_null($result_row['start_date'])) { 
			$strTemp = $result_row['start_date']; 				
			if ($strTemp <> "") {
				$strItemStartDate = date("D, F j, Y", $strTemp);
				if ($strTemp > strtotime("now")) {
					if (!is_null($result_row['recursive'])) { 
						switch ($result_row['recursive']) {
							case "week":
								if ($strTemp > (strtotime("now")+ (6 * 24 * 60 * 60))) {
									$strItemStartDateR = date("M d, Y", $strTemp);
								}
								break;								
							case "month":
								if ($strTemp > (strtotime("now")+ (28 * 24 * 60 * 60))) {
									$strItemStartDateR = date("M d, Y", $strTemp);
								}
								break;
							default:
								$strItemStartDateR = date("M d, Y", $strTemp);
								break;
						}
					}
				}
				$strItemStartDayWeek = date("l", $strTemp);
				$strItemStartDayMonth = date("j", $strTemp);	
				if (($strItemStartDayMonth == "1") ||($strItemStartDayMonth == "21") ||($strItemStartDayMonth == "31")) {
					$strItemStartDayMonth = $strItemStartDayMonth . "st";
				} elseif (($strItemStartDayMonth == "2") ||($strItemStartDayMonth == "22")) {
					$strItemStartDayMonth = $strItemStartDayMonth . "nd";
				} elseif (($strItemStartDayMonth == "3") ||($strItemStartDayMonth == "23")) {
					$strItemStartDayMonth = $strItemStartDayMonth . "rd";
				} else {
					$strItemStartDayMonth = $strItemStartDayMonth . "th";
				}
			}
		}
		if (!is_null($result_row['end_date'])) { 
			$strTemp = $result_row['end_date']; 
			if ($strTemp <> "") {
				$strItemEndDate = date("D, F j, Y", $strTemp);
			}
		}
		if (!is_null($result_row['start_time'])) { 
			$strTemp = $result_row['start_time']; 
			if ($strTemp <> "") {
				$strItemStartTime = date("g:i A", $strTemp);
			}								
		}
		if (!is_null($result_row['end_time'])) { 
			$strTemp = $result_row['end_time']; 
			if ($strTemp <> "") {
				$strItemEndTime = date("g:i A", $strTemp);
			}								
		}
		if (!is_null($result_row['expiry'])) { 
			$strTemp = substr($result_row['expiry'], 0, 10);
			if ($strTemp < date("Y-m-d", strtotime("+2 months"))) {
				$strItemExpiry = date("M d, Y", mktime(0, 0, 0, substr($strTemp, 5, 2), substr($strTemp, 8, 2), substr($strTemp, 0, 4)));
			}
		}
		if (!is_null($result_row['recursive'])) { 
			$strTemp =$result_row['recursive'];
			if ($strTemp == "day") {
				$strItemRecursive = "Daily";
			} elseif ($strTemp == "week") {
				$strItemRecursive = "Weekly";
			} elseif ($strTemp == "month") {
				$strItemRecursive = "Monthly";
			}
		}
		
		$strRange = "";
		if (($strItemStartDateR <> "") && ($strItemExpiry <> "")) {
			$strRange = sprintf("&nbsp;<span class=\"tiny\">[%s - %s]</span>", $strItemStartDateR, $strItemExpiry);
		} elseif ($strItemStartDateR <> "") {
			$strRange = sprintf("&nbsp;<span class=\"tiny\">[starting %s]</span>", $strItemStartDateR);
		} elseif ($strItemExpiry <> "") {
			$strRange = sprintf("&nbsp;<span class=\"tiny\">[until %s]</span>", $strItemExpiry);
		}
		if ($strItemRecursive <> "") {
			switch ($strItemRecursive) {
				case "Daily":
					// For daily recursive items, may have just start date, start date and start time, or start date and start time and end time.
					if (($strItemEndTime == "") && ($strItemStartTime == "")) {
						// Just start date
						$strItemDateTime = sprintf("Daily %s", $strRange);
					} elseif (($strItemEndTime == "") && ($strItemStartTime <> "")) {
						// Start date and start time
						$strItemDateTime = sprintf("Daily at %s %s", $strItemStartTime, $strRange);
					} elseif (($strItemEndTime <> "") && ($strItemStartTime <> "")) {
						// Start date and start time and end time
						$strItemDateTime = sprintf("Daily %s to %s %s", $strItemStartTime, $strItemEndTime, $strRange);
					}
					break;
				case "Weekly":
					// For weekly recursive items, may have just start date, start date and start time, or start date and start time and end time.
					if (($strItemEndTime == "") && ($strItemStartTime == "")) {
						// Just start date
						$strItemDateTime = sprintf("%s's %s", $strItemStartDayWeek, $strRange);
					} elseif (($strItemEndTime == "") && ($strItemStartTime <> "")) {
						// Start date and start time
						$strItemDateTime = sprintf("%s's %s %s", $strItemStartDayWeek, $strItemStartTime, $strRange);
					} elseif (($strItemEndTime <> "") && ($strItemStartTime <> "")) {
						// Start date and start time and end time
						$strItemDateTime = sprintf("%s's %s to %s %s", $strItemStartDayWeek, $strItemStartTime, $strItemEndTime, $strRange);
					}
					break;
				case "Monthly":
					// For daily recursive items, may have just start date, start date and start time, or start date and start time and end time.
					if (($strItemEndTime == "") && ($strItemStartTime == "")) {
						// Just start date
						$strItemDateTime = sprintf("%s of each month %s", $strItemStartDayMonth, $strRange);
					} elseif (($strItemEndTime == "") && ($strItemStartTime <> "")) {
						// Start date and start time
						$strItemDateTime = sprintf("%s on the %s of each month %s", $strItemStartTime, $strItemStartDayMonth, $strRange);
					} elseif (($strItemEndTime <> "") && ($strItemStartTime <> "")) {
						// Start date and start time and end time
						$strItemDateTime = sprintf("From %s to %s on the %s of each month %s", $strItemStartTime, $strItemEndTime, $strItemStartDayMonth, $strRange);
					}
					break;
			}
		} elseif (($strItemEndDate <> "") && ($strItemStartTime <> "") && ($strItemEndTime <> "")) {
			$strItemDateTime = sprintf("From %s at %s to %s at %s", $strItemStartDate, $strItemStartTime, $strItemEndDate, $strItemEndTime);							
		} elseif (($strItemEndDate == "") && ($strItemStartTime <> "") && ($strItemEndTime <> "")) {
			// No end date
			$strItemDateTime = sprintf("%s %s to %s", $strItemStartDate, $strItemStartTime, $strItemEndTime);														
		} elseif (($strItemEndDate == "") && ($strItemStartTime <> "") && ($strItemEndTime == "")) {
			// No end date or end time
			$strItemDateTime = sprintf("%s at %s", $strItemStartDate, $strItemStartTime);	
		} elseif (($strItemEndDate <> "") && ($strItemStartTime == "") && ($strItemEndTime == "")) {	
			// No times
			$strItemDateTime = sprintf("From %s to %s", $strItemStartDate, $strItemEndDate);
		} elseif (($strItemEndDate == "") && ($strItemStartTime == "") && ($strItemEndTime == "")) {	
			// Just a start date
			$strItemDateTime = sprintf("%s", $strItemStartDate);
		} elseif (($strItemEndDate <> "") && ($strItemStartTime <> "") && ($strItemStartDate <> "") && ($strItemEndTime == "")) {	
			// Start Date, Start Time, End Date, no end time
			$strItemDateTime = sprintf("From %s at %s to %s", $strItemStartDate, $strItemStartTime, $strItemEndDate);							
		}
		
		return $strItemDateTime;
	}
	
	// format_datetime accepts the $result
	// returned from 
	// SELECT id, start_date, end_date, start_time, end_time, recursive, expiry 
	//			from `cbwire`.`when` where `listingid`=%s order by start_date, start_time, end_date, end_time, recursive, expiry
	// and returns the formatted dates in an array of strings			
	function format_datetime($in_result) {
		$arrFormattedDates = array();
		
		while ($result_row = mysql_fetch_assoc($in_result)) {
			$intItemID = 0;
			if (!is_null($result_row['id'])) { $intItemID = $result_row['id']; }
			
			$arrFormattedDates[$intItemID] = format_singledatetime($result_row);
		}
		
		return $arrFormattedDates;
	}
        
        	
	// format_datetime_short accepts the start_date, start_time, end_date and end_time	// 
	// and returns the formatted date in short form
	// End dates are not included in returned result
	function format_datetime_short($in_start_date, $in_start_time, $in_end_date, $in_end_time) {
		$strItemStartDate = "";
		$strItemStartTime = "";
		$strItemEndTime = "";
		$strItemDateTime = "";
		$strTemp = "";
		
		if ($in_start_date <> "") { 
			$strItemStartDate = date("D M j", $in_start_date);				
		}
		if ($in_start_time <> "") { 
			$strItemStartTime = date("g:ia", $in_start_time);				
		}
		if (($in_end_date == "") && ($in_end_time <> "")) { 
			$strItemEndTime = date("g:ia", $in_end_time);				
		}
				
		if (($strItemStartDate <> "") && ($strItemStartTime <> "") && ($strItemEndTime <> "")) {
			$strItemDateTime = sprintf("%s %s to %s", $strItemStartDate, $strItemStartTime, $strItemEndTime);							
		} elseif (($strItemStartDate <> "") && ($strItemStartTime <> "") && ($strItemEndTime == "")) {
			$strItemDateTime = sprintf("%s %s", $strItemStartDate, $strItemStartTime);												
		} elseif (($strItemStartDate <> "") && ($strItemStartTime == "") && ($strItemEndTime == "")) {
			$strItemDateTime = sprintf("%s", $strItemStartDate);		
		}
		
		return $strItemDateTime;
	}
        
        
	// function hasDateTime determines if the given listing has any datetime entries.
	// Returns true if it does, false otherwise
	function hasDateTime($chrDTVersion, $intDTListingID) {
		// Time info
		$sql_query2 = "";
		if ($chrDTVersion == "A") {
			$sql_query2 = sprintf("SELECT count(*) from `cbwire`.`when` where `listingid`=%s;",
				mysql_real_escape_string($intDTListingID));
		} else {
			$sql_query2 = sprintf("SELECT count(*) from `cbwire`.`when_b` where `listing_bid`=%s;",
				mysql_real_escape_string($intDTListingID));		
		}
		$result2 = mysql_query($sql_query2) or log_error($sql_query2, mysql_error(), "datetime.inc.php", false);
		$intWhenCount = mysql_result($result2, 0);
		
		if (is_numeric($intWhenCount)) {
			if ($intWhenCount > 0) {
				return true;
			}
		}
		
		return false;
	}
        
        
	// insert_datetime
	//	Retrieves date and time info from database and inserts into the page
	//	Parameters:
	//    $chrVersion should be A to retrieve data from the When table, or
	//					B to retrieve data from the When_B table.
	//    $intDTListingID should be the corresponding listing id (Listings.id or Listings_B.id)
	function insert_datetime($chrDTVersion, $intDTListingID, $intBullet) {
		// Time info
		$sql_query2 = "";
		if ($chrDTVersion == "A") {
			$sql_query2 = sprintf("SELECT id, start_date, end_date, start_time, end_time, recursive, expiry 
				from `cbwire`.`when` 
				where `listingid`=%s and `parentid` is null
				order by start_date, start_time, end_date, end_time, recursive, expiry;",
				mysql_real_escape_string($intDTListingID));
		} else {
			$sql_query2 = sprintf("SELECT id, start_date, end_date, start_time, end_time, recursive, expiry 
				from `cbwire`.`when_b` 
				where `listing_bid`=%s and `parentid` is null
				order by start_date, start_time, end_date, end_time;",
				mysql_real_escape_string($intDTListingID));		
		}

		$result2 =  mysql_query($sql_query2) or log_error($sql_query2, mysql_error(), "datetime.inc.php", false);

		$arrDates = format_datetime($result2);
		
		if ($intBullet == 1) {
			echo "<ul class=\"ul_date\">";
			foreach ($arrDates as $strDates) {			
				echo "<li>" . $strDates . "";
			}
			echo "</ul>";
		} else {					
			foreach ($arrDates as $strDates) {			
				echo $strDates . "<br>";
			}
		}
	}
?>