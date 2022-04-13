<?php 
    include_once "includes/connect.php";
    $link = connect();
    $page = "addnew.php";
    $inListingType = "";
    
    $userid = get_user_id();
    
    if (isset($_GET["in"]) && ($userid > 0)) {
        $inListingType = $_GET["in"];

        if (($inListingType == "T") || ($inListingType == "C") || ($inListingType == "D") || ($inListingType == "A")) {
            unset($_SESSION["editListingID"]);

            $isClassified = 0;
            $isDirectory = 0;
            $isAttraction = 0;
            $isThingToDo = 0;

            if ($inListingType == "T") { $isThingToDo = 1; $_SESSION["editListingType"] = "Thing To Do"; $inListingType="Thing To Do"; }
            if ($inListingType == "D") { $isDirectory = 1; $_SESSION["editListingType"] = "Directory"; $inListingType="Directory"; }
            if ($inListingType == "C") { $isClassified = 1; $_SESSION["editListingType"] = "Classified"; $inListingType="Classified";}
            if ($inListingType == "A") { $isAttraction = 1; $_SESSION["editListingType"] = "Attraction"; $inListingType="Attraction";}


            $sql_query = sprintf("Insert into `cbwire`.`listings_b` 
                            (`classifieds`, `directory`, `attractions`, `thingstodo`, `userid`, `entered_date`, `modified_date`) values
                            (%s, %s, %s, %s, %s, '%s', '%s');",
                            $isClassified,
                            $isDirectory,
                            $isAttraction,
                            $isThingToDo,
                            mysql_real_escape_string($userid),
                            date("Y-m-d H:i:s"), date("Y-m-d H:i:s"));

            $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);

            $_SESSION["editListingID"] = mysql_insert_id();
            $_SESSION["editListingType"] = $inListingType;
            
             header( 'Location: update_listing.php' );
             exit();
        }
    }
    
    log_error("Shouldn't reach this line", "isset(\$_GET[\"in\"]: " . isset($_GET["in"]) . ", $inListingType: " . $inListingType, $page, true);
    exit();
?>