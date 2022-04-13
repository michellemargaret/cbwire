<?php 
    include_once "includes/connect.php";
    $link = connect();   
        
    $page = "edit.php";
    $userid = get_user_id();
    
    $inID = 0;
        
    if (isset($_GET["in"])) { if (is_numeric($_GET["in"])) { $inID = $_GET["in"]; } }
    
    // Make sure this listing exists and is not deleted
    // Make sure this user has access to edit
    
    $sql_query = sprintf("SELECT l.`id`, l.`userid`, l.`attractions`, l.`directory`, l.`thingstodo`, l.`classifieds`
                                FROM `listings_b` l
                                where l.`id`=%s and l.`deleted`=0 ",
                            mysql_real_escape_string($inID));

    $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);

    if ($result_row = mysql_fetch_assoc($result)) {      
        if (isAdmin() || (($result_row['userid'] == $userid) && (isLoggedIn()) && ($userid > 0))) { 
            $_SESSION["editListingID"] = $inID;
        } else {
            $_SESSION["editListingID"] = 0;
        } 
        
        if (!is_null($result_row['attractions'])) { if ($result_row['attractions'] == 1) { $_SESSION["editListingType"] = "Attraction"; }  }
        if (!is_null($result_row['directory'])) { if ($result_row['directory'] == 1) { $_SESSION["editListingType"] = "Directory"; }  }
        if (!is_null($result_row['classifieds'])) { if ($result_row['classifieds'] == 1) { $_SESSION["editListingType"] = "Classified"; }  }
        if (!is_null($result_row['thingstodo'])) { if ($result_row['thingstodo'] == 1) { $_SESSION["editListingType"] = "Thing To Do"; }  }
     } else {
        $_SESSION["editListingID"] = 0;
        $_SESSION["editListingType"] = "";
    }
    
    
    header( 'Location: update_listing.php' );
    exit();
?>