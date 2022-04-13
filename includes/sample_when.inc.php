<?php 

include_once "../includes/funcDateTime.inc.php";

// page variables
$strStartDate = "";
$strStartTime = "";
$strStartAM = "A";
$strEndTime = "";
$strEndAM = "A";
$strRecursive = "";
$strExpiry = "";

$strStartTimeStamp = "";
$strEndTimeStamp = "";
$strSDateTimeStamp = "";
$strEDateTimeStamp = "";

	if (isset($_GET['ondate'])) {
		$strStartDate = htmlentities(trim($_GET['ondate']));
	}
	
	if (isset($_GET['fromtime'])) {
		$strStartTime = htmlentities(trim($_GET['fromtime']));
	}
	
	if (isset($_GET['totime'])) {
		$strEndTime = htmlentities(trim($_GET['totime']));
	}
			
	if (validate_date($strStartDate)) {			
		$strSDateTimeStamp = strtotime($strStartDate . " 12:00 AM");
	}
	
	// validate time
	if ($strStartTime <> "") {
		if (validate_time($strStartTime)) {	
			if (isset($_GET['onam'])) {
				$temp = trim($_GET['onam']);
				if (($temp == 'A') || ($temp == 'P')) {
					$strStartAM = $temp;
				}
			}				
			$strStartTimeStamp = strtotime("2010-01-01" . " " . $strStartTime . " " . $strStartAM . "M");
		}					
	}
		
	if ($strEndTime <> "") {
		if (validate_time($strEndTime)) {	
			if (isset($_GET['toam'])) {
				$temp = trim($_GET['toam']);
				if (($temp == 'A') || ($temp == 'P')) {
					$strEndAM = $temp;
				}
			}				
			$strEndTimeStamp = strtotime("2010-01-01" . " " . $strEndTime . " " . $strEndAM . "M");
		}					
	}
		
	if (isset($_GET['recursive'])) {
		$strRecursive = trim($_GET['recursive']);
	}
		
	if (isset($_GET['until'])) {
		$strExpiry = htmlentities(trim($_GET['until']));
	}
	
	if ($strRecursive <> "") {
		if ($strExpiry <> "") {			
			if (validate_date($strExpiry)) {	
				// Convert Expiry Date into properly formated array for isDateEarlier function
				$arrExpiry = formatDateToArray($strExpiry);
				$arrCurrent = formatDateToArray(date("Y-m-d H:i:s"));
				$arrLimit = formatDateToArray(date("Y-m-d H:i:s", mktime(0,0,0, date("m"), date("d"), date("Y")+1)));			
			}
		} else {
			// Expiry date wasn't entered, use default expiry date of one year from now
			$strExpiry = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d"), date("Y")+1));
		}
	} 
	

	$arrDateTimeInfo = array();
	$arrDateTimeInfo["start_date"] = $strSDateTimeStamp;
	$arrDateTimeInfo["recursive"] = $strRecursive;
	$arrDateTimeInfo["end_date"] = $strEDateTimeStamp;
	$arrDateTimeInfo["start_time"] = $strStartTimeStamp;
	$arrDateTimeInfo["end_time"] = $strEndTimeStamp;
	$arrDateTimeInfo["expiry"] = $strExpiry;
	
	$strDateTimeString = format_singledatetime($arrDateTimeInfo);
	if ($strDateTimeString <> "") {
		echo $strDateTimeString;
	} else {
		echo "&nbsp;";
	}
	
	
?>