<?php 

    include_once "../includes/connect.php";
    $link = connect();
    
    $userid = get_user_id();
    $page = "save_pre.php";
    
    if (isset($_POST["txtEmailPre"]) && (isset($_POST["listingtype"]))) {
        $inEmail = $_POST["txtEmailPre"];
        $inListingType = $_POST["listingtype"];

        if (($inEmail <> "") && ($inListingType <> "")) {
            if (isset($_SESSION["editListingID"])) {
                unset($_SESSION["editListingID"]);
            }
            
            $isClassified = 0;
            $isDirectory = 0;
            $isAttraction = 0;
            $isThingToDo = 0;

            if ($inListingType == "Thing To Do") { $isThingToDo = 1; $_SESSION["editListingType"] = "Thing To Do"; }
            if ($inListingType == "Directory") { $isDirectory = 1; $_SESSION["editListingType"] = "Directory"; }
            if ($inListingType == "Classified") { $isClassified = 1; $_SESSION["editListingType"] = "Classified"; }

            $sql_query = sprintf("Insert into `cbwire`.`listings_b` 
                            (`classifieds`, `directory`, `attractions`, `thingstodo`, `userid`, `useremail`, `entered_date`, `modified_date`) values
                            (%s, %s, %s, %s, %s, '%s', '%s', '%s');",
                            $isClassified,
                            $isDirectory,
                            $isAttraction,
                            $isThingToDo,
                            mysql_real_escape_string($userid),
                            mysql_real_escape_string($inEmail),
                            date("Y-m-d H:i:s"), date("Y-m-d H:i:s"));
            $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);

            $_SESSION["editListingID"] = mysql_insert_id();
            $_SESSION["editListingType"] = $inListingType;
            
            echo "success";
            
            exit();
        }
    }
    
    echo "end";   
?>