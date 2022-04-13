<?php
    include_once "../includes/connect.php";
    $link = connect();
    $page = "getExistingIDs.php";
    $editListingID = $_SESSION["editListingID"];
    $editListingType = $_SESSION["editListingType"];
    
    $strResults = "";
    
    if (isset($_GET['in'])) {
        if (($_GET['in'] == "contact") && ($editListingType == "Thing To Do")) {
            $sql_query = sprintf("SELECT `id`
			from `cbwire`.`contact_b` 
                        where `listings_bid`=%s and `activity_contact`=1;",
			mysql_real_escape_string($editListingID));

		$result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);
                
		while ($result_row = mysql_fetch_assoc($result)) {						
			if (!is_null($result_row['id'])) { $strResults = $strResults . $result_row['id'] . ","; }	
		} 
        } elseif (($_GET['in'] == "contact") || ($_GET['in'] == "location")) {
            $sql_query = sprintf("SELECT `id`
			from `cbwire`.`contact_b` 
                        where `listings_bid`=%s and `activity_contact`=0;",
			mysql_real_escape_string($editListingID));
		$result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);
                
		while ($result_row = mysql_fetch_assoc($result)) {						
			if (!is_null($result_row['id'])) { $strResults = $strResults . "" . $result_row['id'] . ","; }	
		} 
        } elseif ($_GET['in'] == "date") {
            $sql_query = sprintf("SELECT `id`
			from `cbwire`.`when_b` 
                        where `listing_bid`=%s;",
			mysql_real_escape_string($editListingID));
		$result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);
                
		while ($result_row = mysql_fetch_assoc($result)) {						
			if (!is_null($result_row['id'])) { $strResults = $strResults . "" . $result_row['id'] . ","; }	
		} 
        } else {
            log_error("$_GET[in] case not handled: " . $_GET["in"], "mysql_error()", $page, false);
        }
   }

   if (strlen($strResults) > 0) {
       $strResults = substr($strResults, 0, strlen($strResults) - 1);
   }
   
   echo $strResults;
?>	