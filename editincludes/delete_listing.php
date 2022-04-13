<?php 
    include_once "../includes/connect.php";    
    $link = connect();
    $page = "delete_listing.php";
    $userid = get_user_id();
    $trace = "";

    $editListingID = 0;
    
    if (isset($_POST["valEListID"]) && is_numeric($_POST["valEListID"])) {    
        $editListingID = $_POST["valEListID"];  
    }
    
    if (($editListingID > 0) && ($userid > 0) && (isLoggedIn())) {   
        
        $sql_query = "";
        
        if (isAdmin()) {
            $sql_query = sprintf("Update `cbwire`.`listings_b` set `deleted`=1, `modified_date`='%s', 
                                    `lastmodifiedby`=%s where id=%s;",
                    date("Y-m-d H:i:s"),		
                    mysql_real_escape_string($userid),
                    mysql_real_escape_string($editListingID));
        } else {
            $sql_query = sprintf("Update `cbwire`.`listings_b` set `deleted`=1, `modified_date`='%s', 
                                    `lastmodifiedby`=%s where id=%s and userid=%s;",
                    date("Y-m-d H:i:s"),		
                    mysql_real_escape_string($userid),
                    mysql_real_escape_string($editListingID),		
                    mysql_real_escape_string($userid));
        }
        $trace = $trace . $sql_query . "\n\n";
	mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);
        
        echo "success " . $trace; // Prompts calling page to give confirmation message   

        exit();
        
    }
    
    log_error("unsuccessful attempt to delete a record.", "id: " . $editListingID, $page, false);
    echo "end_err;";

?>