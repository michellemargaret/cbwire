<?php 

    include_once "includes/connect.php";
    $link = connect();
 
    $page = "messagePosterDetails.php";
    
    $inName = "";
    $inPhone = "";
    $inEmail = "";
    $inMessage = "";
    $inID = 0;
    
    $errMessage = "";

    if (isset($_POST["intYourMessageID"])) {
        if (is_numeric($_POST["intYourMessageID"])) {
            $inID = $_POST["intYourMessageID"];
        }
    }
    
    if (!($inID > 0)) {
        log_error("Shouldn't reach this line", "inID: " . $inID, $page, true);
        exit();
    }
    
    if (isset($_POST["txtYourName"])) {
        $inName = $_POST["txtYourName"];
    }
    
    if (isset($_POST["txtYourPhone"])) {
        $inPhone = $_POST["txtYourPhone"];
    }
    
    if (isset($_POST["txtYourEmail"])) {
        $inEmail = $_POST["txtYourEmail"];
    }
    
    if (isset($_POST["txtYourMessage"])) {
        $inMessage = $_POST["txtYourMessage"];
    }
    
    if ($inName == "") {
        $errMessage = $errMessage . "<li>Name is required.</li>";
    }
    
    if (($inPhone == "") && ($inEmail == "")) {
        $errMessage = $errMessage . "<li>You must enter a phone number or email address so the poster can contact you.</li>";        
    }
    
    if ($inMessage == "") {
        $errMessage = $errMessage . "<li>A Message is required.</li>";
    } else if (strlen($inMessage) > 500) {
        $errMessage = $errMessage . "<li>Message is too long.  Please keep it to less than 500 characters.</li>";
    }
    
    if ($errMessage <> "") {
        echo "Oops. There was an error that prevented the message from sending:";
        echo "<ul>" . $errMessage . "</ul>";
        echo "Please try again.";
    } else {        
        echo "success";
        include_once "includes/clsEmailMessage.php";
        $email = new EmailMessage();

        if ($email->contactClassified(get_user_id(), $inName, $inEmail, $inMessage, $inPhone, $inID)) {
            
        } else {;
            log_error("Issue with classified contact email", "Classified contact email did not send properly. listing id: " . $inID . ", name: " . $inName . ", phone: " . $inPhone . ", email: " . $inEmail . ", message: " . $inMessage, $page, false);
        } 
    }
?>