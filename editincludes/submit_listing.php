<?php 
    include_once "../includes/connect.php";
    
    $link = connect();
    $page = "submit_listing.php";

    if (isset($_SESSION["editListingID"]) && is_numeric($_SESSION["editListingID"])) {    
        $editListingID = $_SESSION["editListingID"];        
                
        $sql_query = sprintf("Update `cbwire`.`listings_b` set `status`='app', 
                `modified_date`='%s', `lastmodifiedby`=%s where id=%s;",
		date("Y-m-d H:i:s"),		
                mysql_real_escape_string($userid),
		mysql_real_escape_string($editListingID));
        $trace = $trace . $sql_query . "\n\n";
	$result = mysql_query($sql_query) or query_error($sql_query, mysql_error(), $page, $userid, false);
        
        echo "success " . $trace; // Prompts calling page to give confirmation message   

        unset($_SESSION["editListingID"]);
        unset($_SESSION["editListingType"]);        
    }
?>