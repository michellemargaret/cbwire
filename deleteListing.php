<?php 

    include_once "includes/func.php";    
    
    $userid = get_user_id();
    
    $page = "deleteListing.php"; 
    $inListingID = 0;
    $continue = false;
    $redirect = true;
    
    if (isset($_GET["type"])) {
        if ($_GET["type"] == "ajax") {
            $redirect = false;
        }
    }
     
    if ((isset($_GET["in"]) && (is_numeric($_GET["in"])))) {     
        $inListingID = $_GET["in"];    
        
        $sql_query = sprintf("SELECT l.`userid` from `cbwire`.`listings` l where l.`id`=%s and l.`deleted`=0;",
                                    mysql_real_escape_string($inListingID));

        $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, $redirect);              
                
        $intOwnerID = 0;
        if ($result_row = mysql_fetch_assoc($result)) {  
            if (!is_null($result_row['userid'])) { $intOwnerID = $result_row['userid']; }
        }  
        
        if ((isLoggedIn()) && ($userid > 0) && (($intOwnerID == $userid) || (isAdmin()))) {
            $continue = true;
        } else {
            log_error("User directed to error page. intOwnerID: " . $intOwnerID . ", isAdmin: " . isAdmin(), "This line shouldn't be reached.", $page, $redirect);
        }
    } else {
        log_error("User directed to error page. deleteListing hit without listing id being passed in", "This line shouldn't be reached.", $page, $redirect);
    } 
    
    if (($userid > 0) && $continue && (($intOwnerID == $userid) || (isAdmin())) && ($inListingID > 0)) {
        $sql_query = sprintf("Update `cbwire`.`listings` SET `deleted` = 1, `deleted_by`=%s, `updated_date` = '%s' WHERE `id`=%s and `deleted`=0",									 
                                                                            mysql_real_escape_string($userid),
                                                                            mysql_real_escape_string(date("Y-m-d H:i:s")),
                                                                            mysql_real_escape_string($inListingID));
        mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, $redirect);   
        $affectedRows = mysql_affected_rows();
        if ($affectedRows == 1) { 
            if ($redirect) {
                header( 'Location:yourinfo.php?from=delete&result=success');
                exit();
            } else {
                echo "success";
            }
        } else {
            log_error("User directed to error page. deleteListing affected rows not 1 after delete. mysql affected rows: " . mysql_affected_rows(), "This line shouldn't be reached.", $page, $redirect);
            exit();
        }     
    }
?>