<?php 
    
    $page = "ask_faq.php";
    
    $type = ""; 
    $strQuestion = "";
    $strEmail = "";

    if (isset($_GET["type"])) {
        if ($_GET["type"] == "ajax") {
            $type = "ajax";
        }
    }
     
    if (isset($_POST["txtQuestionFAQ"])) {
        $strQuestion = $_POST["txtQuestionFAQ"];
    }
     
    if (isset($_POST["txtEmailFAQ"])) {
        $strEmail = $_POST["txtEmailFAQ"];
    }
    
    if ($type == "ajax") {        
        include_once "../includes/connect.php";    
        $link = connect();
    
        if ($strQuestion == "") {
            log_error("No question?", "This line shouldn't be reached.", $page, false);
        } else {
            include_once "../includes/clsEmailMessage.php";
            $email = new EmailMessage();

            if ($email->newFAQ($strEmail, $strQuestion)) {
               echo "success";
            } else {
                log_error("Issue with email for FAQ", "Couldn't email faq. Email: " . $strEmail . ", Question: " . $strQuestion, $page, false);
                echo "success";
            } 
        } 
    } else {     
        include_once "includes/connect.php";    
        $link = connect();
    
        if ($strQuestion == "") {
            log_error("No question?", "This line shouldn't be reached.", $page, false);
        } else {
            include_once "includes/clsEmailMessage.php";
            $email = new EmailMessage();

            $strSuccessMessage = "Your question was successfully sent!";

            if ($email->newFAQ($strEmail, $strQuestion)) {
                echo $strSuccessMessage;
            } else {
                log_error("Issue with email for FAQ", "Couldn't email faq. Email: " . $strEmail . ", Question: " . $strQuestion, $page, false);
                echo $strSuccessMessage;
            }         
        }
    }
    
?>