<?php

	// Format a phone number
	// Input should be a phone number string of 7, 10 or 11 numbers
	// If it is 11 numbers, the first number must be a 1 (1-709-555-5555)
	// String is stripped of all characters besides numbers
	// Seven digit numbers have 709- added to the front
	// A blank string is returned if input doesn't have 7, 10 or 11 (with first digit 1) digits
	function format_phone($phone){
		$new_phone = array();
		$arr_inphone = str_split(strtolower($phone));
		$intExtensionStart = 0;
		foreach ($arr_inphone as $single) {
			if (is_numeric($single)) {
				// Add each numeric character to the new_phone array
				$new_phone[] = $single;
			} elseif ((($single == "x") || ($single == "e")) && ($intExtensionStart == 0)) {
				$intExtensionStart = count($new_phone);
			}
		}

		$count = count($new_phone);
		
		// Reset phone
		$phone = "";
		
		// If appropriate number of numbers, format and return
		if ($count > 0) {

			if ($intExtensionStart == 0) {

				if($new_phone[0] == 1) { $nothing = array_shift($new_phone); $count--;} // if 1- is included, remove
				if ($count == 7) {
					$phone = "709-" . $new_phone[0] . $new_phone[1] . $new_phone[2] . "-" . $new_phone[3] . $new_phone[4] . $new_phone[5] . $new_phone[6];
				} else if ($count == 10) {
					$phone = $new_phone[0] . $new_phone[1] . $new_phone[2] . "-" . $new_phone[3] . $new_phone[4] . $new_phone[5] . "-" . $new_phone[6] . $new_phone[7]  . $new_phone[8]  . $new_phone[9];
				} 
			} elseif ($intExtensionStart > 0) {	
		
				if($new_phone[0] == 1) { $nothing = array_shift($new_phone); $count--; $intExtensionStart--; } // if 1- is included, remove
				if ($intExtensionStart == 7) {
					$phone = "709-" . $new_phone[0] . $new_phone[1] . $new_phone[2] . "-" . $new_phone[3] . $new_phone[4] . $new_phone[5] . $new_phone[6] . " ext ";
					for ($i=7; $i<$count; $i++) {
						$phone = $phone . $new_phone[$i];
					}						
				} else if ($intExtensionStart == 10) {
					$phone = $new_phone[0] . $new_phone[1] . $new_phone[2] . "-" . $new_phone[3] . $new_phone[4] . $new_phone[5] . "-" . $new_phone[6] . $new_phone[7]  . $new_phone[8]  . $new_phone[9] . " ext ";
					for ($i=10; $i<$count; $i++) {
						$phone = $phone . $new_phone[$i];
					}
				} 			
			}
		}
		return $phone;	
	}
?>