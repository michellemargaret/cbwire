<?php 
    
    include_once "func.php";  
    $page = "send_contact.php";
    
    $type = ""; 
    $strName = "";
    $strEmail = "";
    $strPhone = "";
    $strMessage = "";
    $userid = get_user_id();

    if (isset($_GET["type"])) {
        if ($_GET["type"] == "ajax") {
            $type = "ajax";
        }
    }
     
    if (isset($_POST["txtContactUsName"])) {
        $strName = $_POST["txtContactUsName"];
    }
    if (isset($_POST["txtContactUsPhone"])) {
        $strPhone = $_POST["txtContactUsPhone"];
    }
    if (isset($_POST["txtContactUsEmail"])) {
        $strEmail = $_POST["txtContactUsEmail"];
    }
    if (isset($_POST["txtContactUsMsg"])) {
        $strMessage = $_POST["txtContactUsMsg"];
    }
    
    if ($type == "ajax") {        
        include_once "../includes/connect.php";    
        $link = connect();    
        
        include_once "../includes/clsEmailMessage.php";
        $email = new EmailMessage();

        if ($email->contactCBWire($userid, $strName, $strEmail, $strMessage, $strPhone)) {
            echo "success";
        } else {
            log_error("Issue with email for contact", "Couldn't email contact. Name: " . $strName . ", Email: " . $strEmail . ", Phone: " . $strPhone . ", Message: " . $strMessage, $page, false);
            echo "success";
        } 
    } else {     
        include_once "includes/connect.php";    
        $link = connect();
    
        include_once "includes/clsEmailMessage.php";
        $email = new EmailMessage();

        $strSuccessMessage = "Your message was successfully sent!";

        if ($email->contactCBWire($userid, $strName, $strEmail, $strMessage, $strPhone)) {
            echo $strSuccessMessage;
        } else {
            log_error("Issue with email for contact", "Couldn't email contact. Name: " . $strName . ", Email: " . $strEmail . ", Phone: " . $strPhone . ", Message: " . $strMessage, $page, false);
            echo $strSuccessMessage;
        } 
    }
    
?>