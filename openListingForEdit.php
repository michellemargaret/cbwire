<?php 

    include_once "includes/func.php";    
    
    $userid = get_user_id();
        
    $page = "openListingForEdit.php"; 
    
    $viewListingType = "Directory";
    $inListingID = 0;
    $continue = false;
     
    if ((isset($_GET["in"]) && (is_numeric($_GET["in"])))) {     
        $inListingID = $_GET["in"];        
        
        $sql_query = sprintf("SELECT l.`thingstodo`, l.`attractions`, l.`classifieds`, l.`directory`, l.`userid`
                                    from `cbwire`.`listings` l
                                    where l.`id`=%s;",
                                    mysql_real_escape_string($inListingID));

        $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);              
                
        $intOwnerID = 0;
        if ($result_row = mysql_fetch_assoc($result)) {     

            if (!is_null($result_row['attractions'])) { if ($result_row['attractions'] == 1) { $viewListingType = "Attraction"; }  }
            if (!is_null($result_row['directory'])) { if ($result_row['directory'] == 1) { $viewListingType = "Directory"; }  }
            if (!is_null($result_row['classifieds'])) { if ($result_row['classifieds'] == 1) { $viewListingType = "Classified"; }  }
            if (!is_null($result_row['thingstodo'])) { if ($result_row['thingstodo'] == 1) { $viewListingType = "Thing To Do"; }  }
            if (!is_null($result_row['userid'])) { $intOwnerID = $result_row['userid']; }
        }  
        
        if ((isLoggedIn()) && (($intOwnerID == $userid) || (isAdmin()))) {
            $continue = true;
        } else {
            log_error("User directed to error page. intOwnerID: " . $intOwnerID . ", isAdmin: " . isAdmin(), "This line shouldn't be reached.", $page, true);
            exit();
        }
    } else {
        log_error("User directed to error page. openListingForEdit hit without listing id being passed in", "This line shouldn't be reached.", $page, true);
        exit();
    }

    if ((isLoggedIn()) && $continue && (($intOwnerID == $userid) || (isAdmin())) && ($inListingID > 0)) {
        // Determine if there is already a version of this listing in the listing b table.
        // If so, load that into details.  If not, copy this listing into listing b table,
        // then open that into details.

        $intListingBID = 0;
        $sql_query2 = sprintf("SELECT `id` FROM `listings_b` WHERE `listingsid`=%s && `deleted`=0;",
                        mysql_real_escape_string($inListingID));
        $result2 = mysql_query($sql_query2) or log_error($sql_query2, mysql_error(), $page, true);              

        if ($result_row2 = mysql_fetch_assoc($result2)) {		
                if (!is_null($result_row2['id'])) { $intListingBID = $result_row2['id']; }
        }

        if ($intListingBID == 0) {
            $booSuccess = true;

            $sql_query_start = "	START TRANSACTION;";
            mysql_query($sql_query_start) or log_error($sql_query_start, mysql_error(), $page, false);              

            // Copy this listing into the listing b table
            $sql_insert_query = sprintf("Insert into `cbwire`.`listings_b` (`listingsid`, `status`, `thingstodo`, `attractions`, `classifieds`, `directory`, `buysell`, 
                                                            `cost`, `description`, `expiry_date`, 
                                                            `title`, `picture`, `pictureid`, `highlight`, `website`, `deleted`, `userid`, `modified_date`, `entered_date`, `lastmodifiedby`) 
                                                            SELECT %s, 'edt', `thingstodo`, `attractions`, `classifieds`, `directory`, `buysell`, 
                                                            `cost`, `description`, `expiry_date`, 
                                                            `title`, `picture`, `pictureid`, `highlight`, `website`, 0, `userid`, '%s', `inserted_date`, %s
                                                            FROM `cbwire`.`listings` WHERE `id`=%s",
                                                            mysql_real_escape_string($inListingID),
                                                            mysql_real_escape_string(date("Y-m-d H:i:s")),
                                                            mysql_real_escape_string($userid),
                                                            mysql_real_escape_string($inListingID));
            $result_insert = mysql_query($sql_insert_query) or log_error($sql_insert_query, mysql_error(), $page, false);

            $intListingBID =  mysql_insert_id();

            if ((mysql_affected_rows() <> 1) || ($intListingBID < 1) || (!$result_insert)) {
                log_error("After insert into listings_b table, mysql affected rows is not 1 or listings b id is less than 1 or result_insert is false.  mysql affected rows: " . mysql_affected_rows() . ", listingsbid: " . $intListingBID . ", ListingBID: " . $intListingBID, "This line shouldn't be reached", $page, true);
                exit();
            }		

            //   Insert new When records
            $sql_when_query = sprintf("Insert into `cbwire`.`when_b` (`listing_bid`, `start_date`, `start_time`, `end_date`, `end_time`, `parentid`, `recursive`, `expiry`) 
                                                            SELECT %s, `start_date`, `start_time`, `end_date`, `end_time`, `parentid`, `recursive`, `expiry`
                                                            FROM `cbwire`.`when` WHERE `listingid`=%s and `parentid` is null", 
                                                            mysql_real_escape_string($intListingBID),
                                                            mysql_real_escape_string($inListingID));
            $result_when = mysql_query($sql_when_query) or log_error($sql_when_query, mysql_error(), $page, false);

            if (!$result_when) {
                $booSuccess = false;
                log_error("result_when is false.", "This line shouldn't be reached", $page, false);
            }

            //   Insert new Location records
            $sql_location_query = sprintf("Insert into `cbwire`.`contact_b` (`listings_bid`, `name`, 
                                                            `phone`, `email`, `hide_email`, `communityid`, `other_community`, 
                                                            `location1`, `location2`, `location3`, `linkid`, `activity_contact`) 
                                                            SELECT %s,  `name`, 
                                                            `phone`, `email`, `hide_email`, `communityid`, `other_community`, 
                                                            `location1`, `location2`, `location3`, `linkid`, `activity_contact`
                                                            FROM `cbwire`.`contact` WHERE `listingsid`=%s", 
                                                            mysql_real_escape_string($intListingBID),
                                                            mysql_real_escape_string($inListingID));
            $result_location = mysql_query($sql_location_query) or log_error($sql_location_query, mysql_error(), $page, false);

            if (!$result_location) {
                $booSuccess = false;
                log_error("result_location is false.", "This line shouldn't be reached", $page, false);
            }

            //   Insert new Listing_Cat records
            $sql_category_query = sprintf("Insert into `cbwire`.`listing_cat_b` (`listing_bid`, `categoryid`) 
                                                            SELECT %s, `categoryid`
                                                            FROM `cbwire`.`listing_cat` WHERE `listingid`=%s", 
                                                            mysql_real_escape_string($intListingBID),
                                                            mysql_real_escape_string($inListingID));
            $result_category = mysql_query($sql_category_query) or log_error($sql_category_query, mysql_error(), $page, false);

            //   Insert new Listing_Age records
            $sql_age_query = sprintf("Insert into `cbwire`.`listing_age_b` (`listing_bid`, `agesid`) 
                                                            SELECT %s, `agesid`
                                                            FROM `cbwire`.`listing_age` WHERE `listingid`=%s", 
                                                            mysql_real_escape_string($intListingBID),
                                                            mysql_real_escape_string($inListingID));
            $result_age = mysql_query($sql_age_query) or log_error($sql_age_query, mysql_error(), $page, false);

            if (!$result_age) {
                $booSuccess = false;
                log_error("result_age is false.", "This line shouldn't be reached", $page, false);
            }


    //    If any problems, roll back
    //    otherwise, Commit

            if ($booSuccess) {
                $sql_query_commit = "COMMIT;";
                $result_commit = mysql_query($sql_query_commit) or log_error($sql_query_commit, mysql_error(), $page, false);
            } else {
                $sql_query_rollback = "ROLLBACK;";
                $result_rollback = mysql_query($sql_query_rollback) or log_error($sql_query_rollback, mysql_error(), $page, false);
                log_error("Rollback required.", "This line shouldn't be reached", $page, true);
                exit();
            }
        }

        $_SESSION["editListingID"] = $intListingBID;
        $_SESSION["editListingType"] = $viewListingType;

        header( 'Location: update_listing.php' ); 
        exit();       
    }
    
    log_error("This should not be reached. intListingID: " . $inListingID . ", intOwnerID: " . $intOwnerID, "This line shouldn't be reached", $page, true);
    exit();

?>