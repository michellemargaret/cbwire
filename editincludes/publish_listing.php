<?php 
    include_once "../includes/connect.php";    
    $link = connect();
    $page = "publish_listing.php";
    
    global $intNewListingID;
    $intNewListingID = 0;
    
	
    function add_month($timestamp) {
            $inDate = getdate($timestamp);
            $inDate['mon'] = $inDate['mon'] + 1;
            $timestamp = mktime($inDate['hours'], $inDate['minutes'], $inDate['seconds'], $inDate['mon'], $inDate['mday'], $inDate['year']); // Convert back to timestamp
        return $timestamp;
    }

    function add_day($timestamp) {
            $inDate = getdate($timestamp);
            $inDate['mday'] = $inDate['mday'] + 1;
            $timestamp = mktime($inDate['hours'], $inDate['minutes'], $inDate['seconds'], $inDate['mon'], $inDate['mday'], $inDate['year']); // Convert back to timestamp
        return $timestamp;
    }

    function add_week($timestamp) {
            $inDate = getdate($timestamp);
            $inDate['mday'] = $inDate['mday'] + 7;
            $timestamp = mktime($inDate['hours'], $inDate['minutes'], $inDate['seconds'], $inDate['mon'], $inDate['mday'], $inDate['year']); // Convert back to timestamp
        return $timestamp;
    }
    
    // insert_when_records is called from  publish_listing to handle the when and when_b insertion
    function insert_when_records($intListingsID, $intListingBID) {
        global $page;
    // ************************* START: COPY WHEN_B TO WHEN ************************************** //
		$sql_when_query = sprintf("Insert into `cbwire`.`when` (`listingid`, `start_date`, `start_time`, `end_date`, `end_time`, `parentid`, `recursive`, `expiry`) 
									 SELECT %s, `start_date`, `start_time`, `end_date`, `end_time`, `parentid`, `recursive`, `expiry`
									 FROM `cbwire`.`when_b` WHERE `listing_bid`=%s", 
									 mysql_real_escape_string($intListingsID),
									 mysql_real_escape_string($intListingBID));
		$result_when = mysql_query($sql_when_query) or log_error($sql_when_query, mysql_error(), $page, false);
		
		if (!$result_when) {
			$booSuccess = false;
                        log_error("<br>Approval Failed on copying dates and times", "result_when is false.", $page, false);
		}
		
		// Get IDs of dates just entered
		$sql_rec1 = sprintf("Select `id` from `cbwire`.`when` where `listingid`=%s",
									 mysql_real_escape_string($intListingsID));
		$result_rec1 = mysql_query($sql_rec1) or log_error($sql_rec1, mysql_error(), $page, false);
		$newWhenIDs = array();
		while ($result1 = mysql_fetch_assoc($result_rec1)) {
			$newWhenIDs[] = $result1['id'];
		}
		
		foreach ($newWhenIDs as $intParentID) { 
			
			if ($intParentID > 0) {
		
				// Handle recursive dates
				$sql_recursive = sprintf("Select `start_date`, `end_date`, `start_time`, `end_time`, `recursive`, `expiry` from `cbwire`.`when` where `id`=%s",
									 mysql_real_escape_string($intParentID));
		
                                $result_recursive = mysql_query($sql_recursive) or log_error($sql_recursive, mysql_error(), $page, false);
				if ($result_row = mysql_fetch_assoc($result_recursive)) {	
					$strRecursive = "";
					$strExpiry = "";
					$strStartDate = "";
					$strStartTime = "";
					$strEndTime = "";
					if (!is_null($result_row['recursive'])) { $strRecursive = $result_row['recursive']; }
					if (!is_null($result_row['start_date'])) { $strStartDate = $result_row['start_date']; }
					if (!is_null($result_row['start_time'])) { $strStartTime = $result_row['start_time']; }
					if (!is_null($result_row['end_time'])) { $strEndTime = $result_row['end_time']; }
					if (!is_null($result_row['expiry'])) { 
						$strExpiry = substr($result_row['expiry'], 0, 10);				
					}
			
					if (($strRecursive <> "") && ($strExpiry <> "") && ($strStartDate <> "")) {
						$intMaxCount = 0;
						$intAddTime = 0;
						$intStartTimeStamp = $strStartDate;
						switch ($strRecursive) {
							case "week":
								$intMaxCount = 52;
								$intStartTimeStamp = add_week($intStartTimeStamp);
								break;
							case "month":
								$intMaxCount = 12;
								$intStartTimeStamp = add_month($intStartTimeStamp);
								break;						
							case "day":
								$intMaxCount = 365;
								$intStartTimeStamp = add_day($intStartTimeStamp);
								break;
						}				
						
						$strExpiryTime = mktime(0, 0, 0, substr($strExpiry, 5, 2), substr($strExpiry, 8, 2), substr($strExpiry, 0, 4));
										
						while (($intMaxCount >= 0) &&
							($strExpiryTime >= $intStartTimeStamp)) {
							
							$sql_when_query = sprintf("Insert into `cbwire`.`when` (`listingid`, `start_date`, `start_time`, `end_time`, `parentid`) 
										values (%s, '%s', '%s', '%s', %s)", 
										mysql_real_escape_string($intListingsID),
										mysql_real_escape_string($intStartTimeStamp),
										mysql_real_escape_string($strStartTime),
										mysql_real_escape_string($strEndTime),
										mysql_real_escape_string($intParentID));
                                                        $result_when = mysql_query($sql_when_query) or log_error($sql_when_query, mysql_error(), $page, false);

							$intMaxCount--;
						
							switch ($strRecursive) {
								case "week":
									$intStartTimeStamp = add_week($intStartTimeStamp);
									break;
								case "month":
									$intStartTimeStamp = add_month($intStartTimeStamp);
									break;						
								case "day":
									$intStartTimeStamp = add_day($intStartTimeStamp);
									break;
							}
						}
					}
				}
			}
		}
    // ************************* END: COPY WHEN_B TO WHEN ************************************** //
    }

    // Call publish_listing on approval of a listing
    // The function will determine if this is a new listing
    // or a change of a current listing
    // If it is a change, it will update the listings fields and set
    //   the listings_b entry to inactive
    // If it is a new entry, it will insert into the listings table and
    //   set the listings_b entry to inactive
    // Parameters:
    //    $intListingBID
    //    $intUserID: id of the approver
    // Return:
    //	  TRUE if sucessful
    //	  FALSE otherwise
    function publish_listing($intListingBID, $intUserID) {
        global $page;
        
            $intListingsID = 0;

            if (!is_numeric($intListingBID)) {
                    log_error("ListingBID not a positive number: " . $intListingBID, "", $page, true);	
                    exit();
            }

            // Determine if new entry
            $sql_query = sprintf("SELECT listingsid FROM `listings_b` where `id` = %s;", mysql_real_escape_string($intListingBID));
            // $result = run_query($sql_query, "admin/funct.inc.php: publish_listing", $intUserID);
            $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);
            
            if (!is_null(mysql_result($result, 0))) {
                    if (is_numeric(mysql_result($result, 0))) {
                            $intListingsID = mysql_result($result, 0);
                    }
            }

            if ($intListingsID == 0) {
                    $booSuccess = true;

                    $sql_query_start = "	START TRANSACTION;";
                   // $result_start = run_query($sql_query_start, "admin/func.inc.php: publish_listing: start", $intUserID);
                    $result_start = mysql_query($sql_query_start) or log_error($sql_query_start, mysql_error(), $page, false);

                    // New entry; insert into Listings table
                    $sql_insert_query = sprintf("Insert into `cbwire`.`listings` (`thingstodo`, `attractions`, `classifieds`, `directory`, 
                                                                            `highlight`, `pictureid`, `buysell`, 
                                                                            `cost`, `description`, `expiry_date`, 								 
                                                                            `title`, `picture`, `website`, `deleted`, `userid`, `approved_by`, `approved_date`, `updated_date`, `inserted_date`, `listings_bid`) 
                                                                            SELECT `thingstodo`, `attractions`, `classifieds`, `directory`, 
                                                                            `highlight`, `pictureid`, `buysell`, 
                                                                            `cost`, `description`, `expiry_date`, 								
                                                                            `title`, `picture`, `website`, 0, `userid`, %s, '%s', `modified_date`, '%s', `id`
                                                                            FROM `cbwire`.`listings_b` WHERE `id`=%s", 
                                                                            mysql_real_escape_string($intUserID),
                                                                            mysql_real_escape_string(date("Y-m-d H:i:s")),
                                                                            mysql_real_escape_string(date("Y-m-d H:i:s")),
                                                                            mysql_real_escape_string($intListingBID));

                    $result_insert = mysql_query($sql_insert_query) or log_error($sql_insert_query, mysql_error(), $page, false);

                    $intListingsID =  mysql_insert_id();
                    $intNewListingID = $intListingsID;
                    if ((mysql_affected_rows() <> 1) || ($intListingsID < 1) || (!$result_insert)) {
                        log_error("<br>Approval Failed on listing insert", "After insert into listings table, mysql affected rows is not 1 or listings id is less than 1 or result_insert is false.  mysql affected rows: " . mysql_affected_rows() . ", listingsid: " . $intListingsID . ", ListingBID: " . $intListingBID, $page, false);
                    }

                    //   Insert new When records				
                    insert_when_records($intListingsID, $intListingBID);

                    //   Insert new Contact records
                    $sql_contact_query = sprintf("Insert into `cbwire`.`contact` (`listingsid`, `name`, 
                                                                            `phone`, `email`, `hide_email`, `communityid`, `other_community`, 
                                                                            `location1`, `location2`, `location3`, `linkid`, `activity_contact`) 
                                                                            SELECT %s,  `name`, 
                                                                            `phone`, `email`, `hide_email`, `communityid`, `other_community`, 
                                                                            `location1`, `location2`, `location3`, `linkid`, `activity_contact`
                                                                            FROM `cbwire`.`contact_b` WHERE `listings_bid`=%s", 
                                                                            mysql_real_escape_string($intListingsID),
                                                                            mysql_real_escape_string($intListingBID));
                    //$result_contact = run_query($sql_contact_query, "admin/func.inc.php:contact", $intUserID);
                    $result_contact = mysql_query($sql_contact_query) or log_error($sql_contact_query, mysql_error(), $page, false);

                    if (!$result_contact) {
                            $booSuccess = false;
                            log_error("<br>Approval Failed on copying contact", "result_contact is false.", $page, false);
                    }

                    //   Insert new Listing_Cat records
                    $sql_category_query = sprintf("Insert into `cbwire`.`listing_cat` (`listingid`, `categoryid`) 
                                                                            SELECT %s, `categoryid`
                                                                            FROM `cbwire`.`listing_cat_b` WHERE `listing_bid`=%s", 
                                                                            mysql_real_escape_string($intListingsID),
                                                                            mysql_real_escape_string($intListingBID));
                    $result_category = mysql_query($sql_category_query) or log_error($sql_category_query, mysql_error(), $page, false);

                    if (!$result_category) {
                            $booSuccess = false;
                            log_error("<br>Approval Failed on copying categories", "result_category is false.", $page, false);
                    }

                    // Insert new Listing_Age records
                    $sql_age_query = sprintf("Insert into `cbwire`.`listing_age` (`listingid`, `agesid`) 
                                                                            SELECT %s, `agesid`
                                                                            FROM `cbwire`.`listing_age_b` WHERE `listing_bid`=%s", 
                                                                            mysql_real_escape_string($intListingsID),
                                                                            mysql_real_escape_string($intListingBID));
                    $result_age = mysql_query($sql_age_query) or log_error($sql_age_query, mysql_error(), $page, false);

                    if (!$result_age) {
                            $booSuccess = false;
                            log_error("<br>Approval Failed on copying ages", "result_age is false.", $page, false); 
                    }

                    // Set Listing_B to inactive
                    $sql_inactive_query = sprintf("Update `cbwire`.`listings_b` set " .
                                            "`status`='del', `deleted`='1', `modified_date`='%s' where id=%s;",
                                            date("Y-m-d H:i:s"),				
                                            mysql_real_escape_string($intListingBID));
                     $result_inactive = mysql_query($sql_inactive_query) or log_error($sql_inactive_query, mysql_error(), $page, false);

                    if (!$result_inactive) {
                            $booSuccess = false;
                            log_error("<br>Approval Failed on setting b to inactive (1)", "result_inactive is false.", $page, false);
                    }		

                    if (mysql_affected_rows() <> 1) {
                            $booSuccess = false;
                            log_error("<br>Approval Failed on setting b to inactive (2)", "After setting listingb entry to deleted, mysql affected rows is not 1 .  mysql affected rows: " . mysql_affected_rows() . ", ListingBID: " . $intListingBID, $page, false);
                    }

                    //    If any problems, roll back
                    //    otherwise, Commit
                    if ($booSuccess) {
                            $sql_query_commit = "COMMIT;";
                            $result_commit = mysql_query($sql_query_commit) or log_error($sql_query_commit, mysql_error(), $page, false);
                            insert_listing_search($intListingsID);
                            return $intListingsID;
                    } else {
                            $sql_query_rollback = "ROLLBACK;";
                            //$result_rollback = run_query($sql_query_rollback, "admin/func.inc.php:rollback publish_listing", $intUserID);
                            $result_rollback = mysql_query($sql_query_rollback) or log_error($sql_query_rollback, mysql_error(), $page, false);
                          //  echo "<br>Approval Failed";
                            log_error("<br>Approval Failed", "<br>Approval Failed", $page, false);
                            return 0;
                    }		
            } else {

                    $booSuccess = true;

                    $sql_query_start = "	START TRANSACTION;";
                    $result_start =  mysql_query($sql_query_start) or log_error($sql_query_start, mysql_error(), $page, true);

                    // New entry; insert into Listings table
                    $sql_insert_query = sprintf("Update `cbwire`.`listings` A, `cbwire`.`listings_b` B
                                                                            SET A.`thingstodo` = B.`thingstodo`, 
                                                                                A.`attractions` = B.`attractions`, 
                                                                                    A.`classifieds` = B.`classifieds`, 
                                                                                    A.`directory` = B.`directory`, 
                                                                                    A.`highlight` = B.`highlight`,
                                                                                    A.`pictureid` = B.`pictureid`,
                                                                                    A.`buysell` = B.`buysell`, 
                                                                                    A.`cost` = B.`cost`, 
                                                                                    A.`description` = B.`description`, 
                                                                                    A.`expiry_date` = B.`expiry_date`,
                                                                                    A.`title` = B.`title`, 
                                                                                    A.`picture` = B.`picture`, 
                                                                                    A.`website` = B.`website`, 
                                                                                    A.`deleted` = B.`deleted`, 
                                                                                    A.`userid` = B.`userid`, 
                                                                                    A.`approved_by` = %s, 
                                                                                    A.`approved_date` = '%s', 
                                                                                    A.`updated_date` = B.`modified_date`,
                                                                                    A.`inserted_date` = B.`entered_date`,
                                                                                    A.`listings_bid` = B.`id`
                                                                            WHERE A.`id`=%s and B.`id`=%s",									 
                                                                            mysql_real_escape_string($intUserID),
                                                                            mysql_real_escape_string(date("Y-m-d H:i:s")),
                                                                            mysql_real_escape_string($intListingsID),
                                                                            mysql_real_escape_string($intListingBID));
                    $result_insert = mysql_query($sql_insert_query) or log_error($sql_insert_query, mysql_error(), $page, false);
                    $intNewListingID = $intListingsID;
                    if ((mysql_affected_rows() <> 1) || (!$result_insert)) {
                            $booSuccess = false;
                            log_error("<br>Approval Failed on listing insert", "After update to listings table, mysql affected rows is not 1 or result_insert is false.  mysql affected rows: " . mysql_affected_rows() . ", listingsid: " . $intListingsID . ", ListingBID: " . $intListingBID, $page, false);
                            
                    }

                    //   Delete old When then Insert new When records
                    $sql_when_query = sprintf("Delete from `cbwire`.`when` WHERE `listingid`=%s", 
                                                                            mysql_real_escape_string($intListingsID));
                    $result_when = mysql_query($sql_when_query) or log_error($sql_when_query, mysql_error(), $page, false);

                    if (!$result_when) {
                            $booSuccess = false;
                            log_error("<br>Approval Failed on deleted dates and times", "result_when is false.", $page, false);
                    }

                    insert_when_records($intListingsID, $intListingBID);

                    //   Delete old contact then Insert new contact records
                    $sql_contact_query = sprintf("DELETE FROM `cbwire`.`contact` WHERE `listingsid`=%s", 
                                                                            mysql_real_escape_string($intListingsID));

                    $result_contact = mysql_query($sql_contact_query) or log_error($sql_contact_query, mysql_error(), $page, false);

                    if (!$result_contact) {
                            $booSuccess = false;
                            log_error("<br>Approval Failed on deleting contact", "result_contact is false.", $page, false);
                    }

                    $sql_contact_query = sprintf("Insert into `cbwire`.`contact` (`listingsid`, `name`, 
                                                                            `phone`, `email`, `hide_email`, `communityid`, `other_community`, 
                                                                            `location1`, `location2`, `location3`, `linkid`, `activity_contact`) 
                                                                            SELECT %s,  `name`, 
                                                                            `phone`, `email`, `hide_email`, `communityid`, `other_community`, 
                                                                            `location1`, `location2`, `location3`, `linkid`, `activity_contact`
                                                                            FROM `cbwire`.`contact_b` WHERE `listings_bid`=%s", 
                                                                            mysql_real_escape_string($intListingsID),
                                                                            mysql_real_escape_string($intListingBID));
                    //$result_contact = run_query($sql_contact_query, "admin/func.inc.php:contact", $intUserID);
                    $result_contact = mysql_query($sql_contact_query) or log_error($sql_contact_query, mysql_error(), $page, false);

                    if (!$result_contact) {
                            $booSuccess = false;
                            log_error("<br>Approval Failed on copying contact", "result_contact is false.", $page, false);
                    }

                    //   Delete old Age then Insert new Age records
                    $sql_age_query = sprintf("DELETE FROM `cbwire`.`listing_age` WHERE `listingid`=%s", 
                                                                            mysql_real_escape_string($intListingsID));
                   
                    $result_age = mysql_query($sql_age_query) or log_error($sql_age_query, mysql_error(), $page, false);

                    if (!$result_age) {
                            $booSuccess = false;
                            log_error("<br>Approval Failed on deleting age", "result_age is false.", $page, false);
                    }

                    $sql_age_query = sprintf("Insert into `cbwire`.`listing_age` (`listingid`, `agesid`) 
                                                                            SELECT %s, `agesid`
                                                                            FROM `cbwire`.`listing_age_b` WHERE `listing_bid`=%s", 
                                                                            mysql_real_escape_string($intListingsID),
                                                                            mysql_real_escape_string($intListingBID));
                    $result_age = mysql_query($sql_age_query) or log_error($sql_age_query, mysql_error(), $page, false);

                    if (!$result_age) {
                            $booSuccess = false;
                            log_error("<br>Approval Failed on copying age", "result_age is false.", $page, false);
                    }

                    //   Insert new Listing_Cat records
                    $sql_category_query = sprintf("DELETE FROM `cbwire`.`listing_cat` WHERE `listingid`=%s", 
                                                                            mysql_real_escape_string($intListingsID));
                    $result_category = mysql_query($sql_category_query) or log_error($sql_category_query, mysql_error(), $page, false);

                    if (!$result_category) {
                            $booSuccess = false;
                          //  echo "<br>Approval Failed on deleting categories";
                          //  generalError("result_category is false.", "admin/func.inc.php", "239", 0, intIDFromSession());
                            log_error("<br>Approval Failed on deleting categories", "result_category is false.", $page, false);
                    }

                    $sql_category_query = sprintf("Insert into `cbwire`.`listing_cat` (`listingid`, `categoryid`) 
                                                                            SELECT %s, `categoryid`
                                                                            FROM `cbwire`.`listing_cat_b` WHERE `listing_bid`=%s", 
                                                                            mysql_real_escape_string($intListingsID),
                                                                            mysql_real_escape_string($intListingBID));
                    //$result_category = run_query($sql_category_query, "admin/func.inc.php:category", $intUserID);
                    $result_category = mysql_query($sql_category_query) or log_error($sql_category_query, mysql_error(), $page, false);

                    if (!$result_category) {
                            $booSuccess = false;
                            log_error("<br>Approval Failed on copying categories", "result_category is false.", $page, false);
                    }

                    // Set Listing_B to inactive
                    $sql_inactive_query = sprintf("Update `cbwire`.`listings_b` set " .
                                            "`status`='del', `deleted`='1', `modified_date`='%s' where id=%s;",
                                            date("Y-m-d H:i:s"),				
                                            mysql_real_escape_string($intListingBID));
                    $result_inactive = mysql_query($sql_inactive_query) or log_error($sql_inactive_query, mysql_error(), $page, false);

                    if (!$result_inactive) {
                            $booSuccess = false;
                            log_error("<br>Approval Failed on setting b to inactive (2)", "result_inactive is false.", $page, false);
                    }		

                    if (mysql_affected_rows() <> 1) {
                            $booSuccess = false;
                            log_error("<br>Approval Failed on setting b to inactive (2)", "After setting listingb entry to deleted, mysql affected rows is not 1 .  mysql affected rows: " . mysql_affected_rows() . ", ListingBID: " . $intListingBID, $page, false);
                    }

                    //    If any problems, roll back
                    //    otherwise, Commit

                    if ($booSuccess) {
                            $sql_query_commit = "COMMIT;";
                            $result_commit = mysql_query($sql_query_commit) or log_error($sql_query_commit, mysql_error(), $page, false);

                            delete_listing_search($intListingsID);
                            insert_listing_search($intListingsID);

                            return $intListingsID;
                    } else {
                            $sql_query_rollback = "ROLLBACK;";
                            $result_rollback = mysql_query($sql_query_rollback) or log_error($sql_query_rollback, mysql_error(), $page, false);
                            log_error("<br>Approval Failed", "<br>Approval Failed", $page, false);
                            return 0;
                    }		
            }
            
            return $intNewListingID;
    }	

    
    // function insert_search_index takes a sentence and inserts each word
    // into it's own entry in the search index table
    function insert_search_index($intListingID, $strSentence, $intScore) {
        global $page;
        
            // List of words that we do not want added to search index table
            $arrDoNotAdd = array("THE", "IN", "AN", "IT", "OF", "IS", "TO", "AND", "AT");

            // Make sentence uppercase
            $strSentence = strtoupper($strSentence);

            // Remove anything that's not alpha-numeric and replace with a space
            $strSentence = preg_replace('/[^A-Za-z0-9_]/', " ", $strSentence);

            // Create an array of all the words in the sentence
            $arrSentence = explode(" ", $strSentence);

            // add each word to the table as long as it is not a word in the do_not_add array
            foreach  ($arrSentence as $strWord) {
                    $strWord = trim($strWord);

                    if ((strlen($strWord) > 1) and (!in_array($strWord, $arrDoNotAdd))) {

                            if (($intListingID > 0) and ($intScore > 0)) {
                                    $sql_query = sprintf("Insert into `cbwire`.`search_index` (`listingid`, `word`, `score`) 
                                                                                    value (%s, '%s', %s)",
                                                                            mysql_real_escape_string($intListingID),
                                                                            mysql_real_escape_string($strWord),
                                                                            mysql_real_escape_string($intScore));				
                                   // run_query($sql_query, "admin/func.inc.php:insert_search_index", 0);
                                    mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);
                            }
                    }
            }

            unset($arrSentence);
    }

    // function delete_listing_search($intListingID) deletes all search_index related to this listing
    // Used when a listing is editted - deletes all search data first, then re-enters, ensuring search
    // matches new data
    function delete_listing_search($intListingID) {
        global $page;
        
        $sql_query = sprintf("Select `id` from listings where id=%s", mysql_real_escape_string($intListingID));
        $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);

        if (mysql_num_rows($result) == 1) {
                $sql_query = sprintf("Delete from `cbwire`.`search_index` where `listingid`=%s", mysql_real_escape_string($intListingID));
                mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);
        } else {
            log_error("561 num rows", mysql_num_rows($result), $page, false);
        }
    }

    // function insert_listing_search($intListingsID) runs through all tables that contain
    // info to be inserted into search_index table
    function insert_listing_search($intListingID) {
        global $page;
        
            $sql_query = sprintf("Select `title`, `description`, `cost`, `website` from listings where id=%s", mysql_real_escape_string($intListingID));
            $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);
            while ($result_row = mysql_fetch_assoc($result)) {
                    if (!is_null($result_row['title'])) { insert_search_index($intListingID, $result_row['title'], 1000); }
                    if (!is_null($result_row['description'])) { insert_search_index($intListingID, $result_row['description'], 100); }
                    if (!is_null($result_row['cost'])) { insert_search_index($intListingID, $result_row['cost'], 25); }
                    if (!is_null($result_row['website'])) { insert_search_index($intListingID, $result_row['website'], 25); }
            }

            $sql_query = sprintf("Select contact.`name`, contact.`phone`, contact.`location1`, contact.`location2`, 
                                                            contact.`location3`, contact.`other_community`, 
                                                            com.`name` as community_name, link.`title` 
                                                            from contact 
                                                            left outer join community com on com.id = contact.communityid
                                                            left outer join listings link on contact.linkid = link.id
                                                            where listingsid=%s", mysql_real_escape_string($intListingID));
            $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);
            while ($result_row = mysql_fetch_assoc($result)) {
                    if (!is_null($result_row['name'])) { insert_search_index($intListingID, $result_row['name'], 25); }
                    if (!is_null($result_row['phone'])) { insert_search_index($intListingID, $result_row['phone'], 25); }
                    if (!is_null($result_row['location1'])) { insert_search_index($intListingID, $result_row['location1'], 25); }
                    if (!is_null($result_row['location2'])) { insert_search_index($intListingID, $result_row['location2'], 25); }
                    if (!is_null($result_row['location3'])) { insert_search_index($intListingID, $result_row['location3'], 25); }
                    if (!is_null($result_row['other_community'])) { insert_search_index($intListingID, $result_row['other_community'], 15); }
                    if (!is_null($result_row['community_name'])) { insert_search_index($intListingID, $result_row['community_name'], 15); }
                    if (!is_null($result_row['title'])) { insert_search_index($intListingID, $result_row['title'], 15); }
            }

            $sql_query = sprintf("Select ages.title
                                                            from listing_age la
                                                            inner join ages on la.agesid = ages.id
                                                            where la.listingid=%s", mysql_real_escape_string($intListingID));
            $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);
            while ($result_row = mysql_fetch_assoc($result)) {
                    if (!is_null($result_row['title'])) { insert_search_index($intListingID, $result_row['title'], 5); }
            }

            $sql_query = sprintf("Select categories.title
                                                            from listing_cat la
                                                            inner join categories on la.categoryid = categories.id
                                                            where la.listingid=%s", mysql_real_escape_string($intListingID));
            $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);
            while ($result_row = mysql_fetch_assoc($result)) {
                    if (!is_null($result_row['title'])) { insert_search_index($intListingID, $result_row['title'], 5); }
            }
    }

    $trace = "";
    $userid = get_user_id();
    $editListingID = 0;
    
    if (isset($_POST["valEListID"]) && is_numeric($_POST["valEListID"])) {    
        $editListingID = $_POST["valEListID"];  
    }
    
    if (($editListingID > 0) && (isLoggedIn())) { 

        $intNewListingID = publish_listing($editListingID, $userid);   

        $sendEmail = true;
        
        // Don't send email if this listing was previously published
        $sql_query = sprintf("Select listingsid from listings_b where `id`=%s", mysql_real_escape_string($editListingID));
        $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);        
        if ($result_row = mysql_fetch_assoc($result)) {
                if (!is_null($result_row['listingsid'])) { if (is_numeric($result_row['listingsid'])) { if ($result_row['listingsid'] > 0) { $sendEmail = false; } } }
        }
        
        if ($intNewListingID > 0) {
            echo "success";        
                
            include_once "../includes/clsEmailMessage.php";
            
            if ($sendEmail == true) {
                $email = new EmailMessage();

                if (isAdmin()) {
                    if ($email->sendAdminPublished($editListingID, $intNewListingID)) {
                    } else {
                        log_error("Issue with email sending after publish1", "Info Email did not send properly, but approval is saved in database. new listing id: " . $intNewListingID, $page, false);
                    }
                } else {
                    if ($email->alertAdminNewListing($intNewListingID)) {
                    } else {
                        log_error("Issue with email sending after publish3", "Info Email did not send properly, but approval is saved in database. new listing id: " . $intNewListingID, $page, false);
                    }
                    if ($email->sendPublished($intNewListingID)) {
                    } else {
                        log_error("Issue with email sending after publish2", "Info Email did not send properly, but approval is saved in database. new listing id: " . $intNewListingID, $page, false);
                    }
                }   
            }
        } else {
            echo "publish function returned: " . $intNewListingID . " .  It should have returned a number greater than 0";
        }        
    } else { echo "error 131213 on publish listing: " . $editListingID; }
?>