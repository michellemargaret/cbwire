<?php 
    include_once "../includes/connect.php";
    include_once "func_format_phone.php";
    include_once "../includes/funcDateTime.inc.php";
    
    $link = connect();
    $page = "save_listing.php";
    
    $trace = "";
    $strType = "";
    
    $userid = get_user_id();
            
    if (isset($_POST["valEListID"]) && is_numeric($_POST["valEListID"])) {    
        $editListingID = $_POST["valEListID"];  
        $editListingType = "Thing To Do";
        $strTitle = ""; 
        $strWebsite = "";
        $strCost = "";
        $strDescription = "";
        $chrBuySell = "S";
        $strExpiry = "";
        $intPictureID = 0;
        $intHighlight = 1;
        $intAttraction = 0;
        
        if(isset($_POST['valListType'])) { $editListingType = trim($_POST['valListType']); } 
        if(isset($_POST['rdoBuySell'])) { $chrBuySell = trim($_POST['rdoBuySell']); } 
        if(isset($_POST['txtTitle'])) { $strTitle = htmlentities(trim($_POST['txtTitle']));  } 
        if(isset($_POST['txtCost'])) {  $strCost = htmlentities(trim($_POST['txtCost']));  }
        if(isset($_POST['chkAdminUse'])) { 
            if ($_POST['chkAdminUse'] == "on") {
                $intHighlight = 1;
            } else {
                $intHighlight = 0;
            }
        } else {
            $intHighlight = 0;
        }
        if ($editListingType == "Attraction") {
            $intAttraction = 1;
        } elseif(isset($_POST['chkAttraction'])) { 
            if ($_POST['chkAttraction'] == "on") {
                    $intAttraction = 1;
            }
        }
        
        // Website is not required.  Make sure if it is set, it is valid.
        if(isset($_POST['txtWebsite'])) {
            $strWebsite = htmlentities(trim($_POST['txtWebsite']));
            if ((!(strrpos(strtolower($strWebsite), "http://") === false)) && (strrpos(strtolower($strWebsite), "http://") == 0)) {
                    $strWebsite = substr($strWebsite, 7, strlen($strWebsite)-1);
            }
        } 

        if(isset($_POST['txtDescription'])) { 
            $strDescription = trim($_POST['txtDescription']); 
            $strDescription = htmlentities($strDescription);
        }

        if(isset($_POST['txtExpiry']) && ($_POST['txtExpiry'] <> "")) {
            $strExpiry = htmlentities(trim($_POST['txtExpiry']));
            $arrExpiry = formatDateToArray($strExpiry);
            $arrCurrent = formatDateToArray(date("Y-m-d H:i:s"));
            $arrLimit = formatDateToArray(date("Y-m-d H:i:s", mktime(0,0,0, date("m")+2, date("d"), date("Y"))));			
        } elseif(isset($_POST['txtExpiry'])) {
            // Expiry date wasn't entered, use default expiry date of two months from now
            $strExpiry = date("Y-m-d", mktime(0, 0, 0, date("m")+2, date("d"), date("Y")));
        }
        
        if(isset($_POST["txtPictureID"])) {
            if (is_numeric($_POST["txtPictureID"])) {
                $intPictureID = $_POST["txtPictureID"];
            }
        }
        
        $sql_query = sprintf("Update `cbwire`.`listings_b` set `pictureid`=%s,
		`buysell`='%s', `title`='%s', `cost`='%s', `description`='%s',  `website`='%s',
		`expiry_date`='%s', `highlight`=%s, `attractions`=%s, `modified_date`='%s', `lastmodifiedby`=%s where id=%s;",
		mysql_real_escape_string($intPictureID), 
		mysql_real_escape_string($chrBuySell), 
		mysql_real_escape_string($strTitle), 
		mysql_real_escape_string($strCost), 
		mysql_real_escape_string($strDescription),
		mysql_real_escape_string($strWebsite),   
		mysql_real_escape_string($strExpiry),   
		mysql_real_escape_string($intHighlight),
		mysql_real_escape_string($intAttraction), 
		date("Y-m-d H:i:s"),		
                mysql_real_escape_string($userid),
		mysql_real_escape_string($editListingID));
        $trace = $trace . $sql_query . "\n\n";
	$result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);
        
        
        // Save age info
        //  1. Delete all old info related to this listing
        $sql_query = sprintf("Delete from `cbwire`.`listing_age_b` where `listing_bid`=%s; ",
                        mysql_real_escape_string($editListingID));
        $trace = $trace .  $sql_query . "\n\n";
        $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);

        // Get age ids from database to determine what ids coming in will be
        $sql_queryage = sprintf("SELECT `id`, `title` from `cbwire`.`ages` where `active`=1 order by `ordernum`;");
        $resultage = mysql_query($sql_queryage) or log_error($sql_query, mysql_error(), $page, false);

        while ($result_row = mysql_fetch_assoc($resultage)) {
            $intAgeID = $result_row['id'];	
            if(isset($_POST['chk' . $intAgeID])) { 
                if ($_POST['chk' . $intAgeID] == "on") {
                        // This id was checked - save to database
                        $sql_query = sprintf("Insert into `cbwire`.`listing_age_b` (`listing_bid`, `agesid`) values (%s, %s) ",
                                mysql_real_escape_string($editListingID),
                                mysql_real_escape_string($intAgeID));
                            mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);
                }
            }
        }
                
        if(isset($_POST["selectedCategories"])) {
            $sql_query = sprintf("Delete from `cbwire`.`listing_cat_b` where `listing_bid`=%s ",
                                                                            mysql_real_escape_string($editListingID));
            mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false); 
            
            $strCategories = str_replace("xx", "x", $_POST["selectedCategories"]);
            $arrCategories = preg_split("/x/", $strCategories);
            
            foreach ($arrCategories as $strCategory) {
                if (is_numeric($strCategory)) {
                    $sql_query = sprintf("Insert into `cbwire`.`listing_cat_b` (`listing_bid`, `categoryid`) values (%s, %s) ",
									mysql_real_escape_string($editListingID),
									mysql_real_escape_string($strCategory));
        $trace = $trace .  $sql_query . "\n\n";
                    mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);
                }
            }
        }       
        
        $sql_query = sprintf("Delete from `cbwire`.`contact_b` where `listings_bid`=%s ", mysql_real_escape_string($editListingID));
        mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);  
        
        if (isset($_POST["contactSections"])) {
            $trace = $trace . "\ncontactsections: " . $_POST["contactSections"] . "\n";
            $strSections = str_replace("xx", "x", $_POST["contactSections"]);
            $arrSections = preg_split("/x/", $strSections);
            
            foreach ($arrSections as $strSection) {
                if (is_numeric($strSection)) {
                    $trace = $trace .  "strSection: " . $strSection . "\n";
                    $intCommunityID = 0;
                    $strOtherCommunity = "";
                    $strLocation1 = "";
                    $strLocation2 = "";
                    $strLocation3 = "";
                    $intLinkID = 0;
                    $intActivityContact = 0;
                    if ($editListingType == "Thing To Do") { $intActivityContact = 1; }
                    $strName = "";
                    $strPhone = "";
                    $strEmail = "";
                    $intHideEmail = 0;

                    // Perform Save
                    if(isset($_POST['txtContactName' . $strSection])) { 
                            $trace = $trace .  "contact name set\n";
                            $strName = htmlentities(trim($_POST['txtContactName' . $strSection])); 
                    } else {
                        $trace = $trace .  "contact name not set (" . 'txtContactName' . $strSection . "\n";
                    }

                    // Phone is not required;  If set, make sure it's valid.  Otherwise, ignore
                    if(isset($_POST['txtPhone' . $strSection])) {
                            $strPhone = htmlentities(trim($_POST['txtPhone' . $strSection]));
                         //   if ($strPhone <> "") {
                         //           $strPhone = format_phone($strPhone);
                         //   }
                    }

                    // Email is required.  Make sure it is set and valid.
                    if(isset($_POST['txtEmail' . $strSection]) && (trim($_POST['txtEmail' . $strSection]) <> "")) {
                            $strEmail = htmlentities(trim($_POST['txtEmail' . $strSection]));	
                    }

                    if(isset($_POST['chkHideEmail' . $strSection])) {
                            if ($_POST['chkHideEmail' . $strSection] == "on") {
                                    $intHideEmail = 1;
                            } else {
                                    $intHideEmail = 0;
                            }
                    } else {
                            $intHideEmail = 0;
                    }

                    if(isset($_POST['txtLocation1' . $strSection])) { 
                            $strLocation1 = htmlentities(trim($_POST['txtLocation1' . $strSection])); 
                    } 

                    if(isset($_POST['txtLocation2' . $strSection]) ) { 
                            $strLocation2 = htmlentities(trim($_POST['txtLocation2' . $strSection])); 
                    } 

                    if(isset($_POST['txtLocation3' . $strSection])) { 
                            $strLocation3 = htmlentities(trim($_POST['txtLocation3' . $strSection])); 
                    } 

                    // Community or Other Community is required
                    if(isset($_POST['ddlCommunity' . $strSection])) {		
                            if (is_numeric( $_POST['ddlCommunity' . $strSection])) {			
                                    $intCommunityID = intval($_POST['ddlCommunity' . $strSection]); 
                            } else {
                                    $intCommunityID = 0;
                            }
                    } 
                    if ($intCommunityID <= 0) { 
                            if(isset($_POST['txtOtherCommunity' . $strSection])) { 
                                    $strOtherCommunity = htmlentities(trim($_POST['txtOtherCommunity' . $strSection])); 
                            } 
                    }


                    if(isset($_POST['txtLinkID' . $strSection])) {
                            if (is_numeric( $_POST['txtLinkID' . $strSection])) {
                                    $intLinkID = intval($_POST['txtLinkID' . $strSection]);
                            } else {
                                    $intLinkID = 0;
                            }
                    }
                    $trace = $trace . "name: " . $strName . "\n";
                    if (($intCommunityID == 0) && ($strOtherCommunity == "") && ($strLocation1 == "") && ($strLocation2 == "") && ($strLocation3 == "") && ($intLinkID == 0) && ($strName == "") && ($strPhone == "") && ($strEmail == "")) {
                        // don't save   
                        $trace = $trace . "empty contact section\n";
                    } else {
                        // Update contact_b table                        
                        $sql_query = sprintf("Insert into `cbwire`.`contact_b` 
                                                (`communityid`, `other_community`, `location1`, `location2`, `location3`, 
                                                `linkid`, `name`, `phone`, `email`, `hide_email`, `listings_bid`, `activity_contact`)			
                                                values
                                                (%s, '%s', '%s', '%s', '%s', %s, '%s', '%s', '%s', %s, %s, %s);",
                                                mysql_real_escape_string($intCommunityID), 
                                                mysql_real_escape_string($strOtherCommunity), 
                                                mysql_real_escape_string($strLocation1), 
                                                mysql_real_escape_string($strLocation2), 
                                                mysql_real_escape_string($strLocation3), 
                                                mysql_real_escape_string($intLinkID),
                                                mysql_real_escape_string($strName), 
                                                mysql_real_escape_string($strPhone), 
                                                mysql_real_escape_string($strEmail), 
                                                mysql_real_escape_string($intHideEmail), 				
                                                mysql_real_escape_string($editListingID),
                                                mysql_real_escape_string($intActivityContact));
                        $trace = $trace .  $sql_query . "\n";		
                        mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);                            
                    }
                }
            }
        }
        
        if (isset($_POST["locationSections"])) {
            $strSections = str_replace("xx", "x", $_POST["locationSections"]);
            $arrSections = preg_split("/x/", $strSections);
            
            foreach ($arrSections as $strSection) {
                if (is_numeric($strSection) && ($strSection > 0)) {
                   $intCommunityID = 0;
                    $strOtherCommunity = "";
                    $strLocation1 = "";
                    $strLocation2 = "";
                    $strLocation3 = "";
                    $intLinkID = 0;

                    // Perform Save
                    if(isset($_POST['txtLocation1W' . $strSection])) { 
                            $strLocation1 = htmlentities(trim($_POST['txtLocation1W' . $strSection])); 
                    } 

                    if(isset($_POST['txtLocation2W' . $strSection])) { 
                            $strLocation2 = htmlentities(trim($_POST['txtLocation2W' . $strSection])); 
                    } 

                    if(isset($_POST['txtLocation3W' . $strSection])) { 
                            $strLocation3 = htmlentities(trim($_POST['txtLocation3W' . $strSection])); 
                    } 

                    // Community or Other Community is required
                    if(isset($_POST['ddlCommunityW' . $strSection])) {
                            if (is_numeric($_POST['ddlCommunityW' . $strSection])) {
                                    $intCommunityID = intval($_POST['ddlCommunityW' . $strSection]);
                            } else {
                                    $intCommunityID = 0;
                            }
                    } 
                    if ($intCommunityID <= 0) { 
                            if(isset($_POST['txtOtherCommunityW' . $strSection])) { 
                                    $strOtherCommunity = htmlentities(trim($_POST['txtOtherCommunityW' . $strSection])); 
                            } 
                    }

                    if(isset($_POST['txtLinkIDW' . $strSection])) {
                            if (is_numeric($_POST['txtLinkIDW' . $strSection])) {
                                    $intLinkID = intval($_POST['txtLinkIDW' . $strSection]);
                            } else {
                                    $intLinkID = 0;
                            }
                    }

                    if (($intCommunityID == 0) && ($strOtherCommunity == "") && ($strLocation1 == "") && ($strLocation2 == "") && ($strLocation3 == "") && ($intLinkID == 0)) {
                        // no data - don't save                      
                    } else {
                        
                    $sql_query = sprintf("Insert into `cbwire`.`contact_b` 			
                                    (`communityid`, `other_community`, `location1`, `location2`, `location3`, `linkid`, `listings_bid`, `activity_contact`) values
                                    (%s, '%s', '%s', '%s', '%s', %s, %s, 0)",
                                    mysql_real_escape_string($intCommunityID), 
                                    mysql_real_escape_string($strOtherCommunity), 
                                    mysql_real_escape_string($strLocation1), 
                                    mysql_real_escape_string($strLocation2), 
                                    mysql_real_escape_string($strLocation3), 
                                    mysql_real_escape_string($intLinkID), 
                                    mysql_real_escape_string($editListingID));   
        $trace = $trace .  $sql_query . "\n\n";                
                    mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);                    
                    }
                }
            }
        }
        
        $sql_query = sprintf("Delete from `cbwire`.`when_b` where `listing_bid`=%s ", mysql_real_escape_string($editListingID));
        mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);  
        
        if (isset($_POST["dateSections"])) {            
            $strSections = str_replace("xx", "x", $_POST["dateSections"]);
            $arrSections = preg_split("/x/", $strSections);

            foreach ($arrSections as $strSection) {
                if (is_numeric($strSection)) {                
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


                    if (isset($_POST['txtStartDate' . $strSection])) {
                            $strStartDate = htmlentities(trim($_POST['txtStartDate' . $strSection]));
                    }
                    
                    if ($strStartDate <> "") {

                        if (isset($_POST['txtStartTime' . $strSection])) {
                                $strStartTime = htmlentities(trim($_POST['txtStartTime' . $strSection]));
                        }

                        if (isset($_POST['txtEndTime' . $strSection])) {
                                $strEndTime = htmlentities(trim($_POST['txtEndTime' . $strSection]));
                        }

                        if (validate_date($strStartDate)) {			
                                $strSDateTimeStamp = strtotime($strStartDate . " 12:00 AM");
                        }	

                        // validate time
                        if ($strStartTime <> "") {
                                if (validate_time($strStartTime)) {	
                                        if (isset($_POST['ddlStartAM' . $strSection])) {
                                                $temp = trim($_POST['ddlStartAM' . $strSection]);
                                                if (($temp == 'A') || ($temp == 'P')) {
                                                        $strStartAM = $temp;
                                                }
                                        }				
                                        $strStartTimeStamp = strtotime("2010-01-01" . " " . $strStartTime . " " . $strStartAM . "M");
                                }					
                        }

                        if ($strEndTime <> "") {
                                if (validate_time($strEndTime)) {	
                                        if (isset($_POST['ddlEndAM' . $strSection])) {
                                                $temp = trim($_POST['ddlEndAM' . $strSection]);
                                                if (($temp == 'A') || ($temp == 'P')) {
                                                        $strEndAM = $temp;
                                                }
                                        }				
                                        $strEndTimeStamp = strtotime("2010-01-01" . " " . $strEndTime . " " . $strEndAM . "M");
                                }					
                        }

                        if (isset($_POST['ddlRecurrance' . $strSection])) {
                                $strRecursive = trim($_POST['ddlRecurrance' . $strSection]);
                        }

                        if (($strRecursive <> "") && ($strRecursive <> "none") && (isset($_POST['txtExpiry' . $strSection]))) {
                                $strExpiry = htmlentities(trim($_POST['txtExpiry' . $strSection]));
                        }

                        if (($strRecursive <> "") && ($strRecursive <> "none")) {
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

                        // save as a new listing	

                        $sql_query = sprintf("Insert into `cbwire`.`when_b` 
                                (`start_date`, `start_time`, `end_time`, `recursive`, `expiry`, `listing_bid`)
                                values
                                ('%s', '%s', '%s', '%s', '%s', %s)",
                                mysql_real_escape_string($strSDateTimeStamp),
                                mysql_real_escape_string($strStartTimeStamp), 
                                mysql_real_escape_string($strEndTimeStamp), 
                                mysql_real_escape_string($strRecursive), 	
                                $strExpiry,  // Can't do mysql_real_escape_string because need to use ' in string			
                                mysql_real_escape_string($editListingID));
            $trace = $trace .  $sql_query . "\n\n";
                        mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);
                    }
                }
            }
        }
                
        
            
        if ($userid == 0) {        
            $sql_query = sprintf("Update `cbwire`.`listings_b` set `status`='app',
                    `modified_date`='%s', `lastmodifiedby`=%s where id=%s;",
                    date("Y-m-d H:i:s"),		
                    mysql_real_escape_string($userid),
                    mysql_real_escape_string($editListingID));
            $trace = $trace . $sql_query . "\n\n";
            $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);
        }        
        
        echo "success"; // Prompts calling page to give confirmation message 
        
        if ($userid == 0) {
            // send email to alert admin to approve
            include_once "../includes/clsEmailMessage.php";
            $email = new EmailMessage();            
            
            if ($email->alertAdmin($editListingID)) {
            } else {
                log_error("Issue with email to alert admin that listing awaiting approval", "Email issue afters saving listing bid: " . $editListingID, $page, false);
            }
        }
    }
?>