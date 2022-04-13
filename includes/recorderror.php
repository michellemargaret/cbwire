<?php
    include_once "func.php"; 
    $strMessage = "------------------------------------------------------\n" . date("r") . "\n---------------------------------------------------------------------\n";
    
    $strMessage = $strMessage . "UserID: " . get_user_id() . "\n";

    if (isset($_SESSION["editListingID"])) {
        $strMessage = $strMessage . "editListingID: " . $_SESSION["editListingID"] . "\n";
    }
    if (isset($_SESSION["editListingType"])) {
        $strMessage = $strMessage . "editListingID: " . $_SESSION["editListingType"] . "\n";
    }
    if (isset($_POST["page"])) {
        $strMessage = $strMessage . "Page: " . $_POST["page"] . "\n";
    }
    if (isset($_POST["message"])) {
        $strMessage = $strMessage . "Message from page: " . $_POST["message"] . "\n";
    }
       
    $strMessage = $strMessage . "\n\r";
    
    $myFile = "errs.log";
    $fh = fopen($myFile, 'a') or die();
    
    fwrite($fh, $strMessage);
   
    fclose($fh);
       
    log_error("Record Error", $strMessage, "recorderror.php", false);              
?>