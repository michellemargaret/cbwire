<?php 
    include_once "../includes/connect.php";
    include_once "../includes/funcEmail.php";
    $link = connect();
    
    $page = "save_registration.php";
    
    if (isset($_POST["txtName"]) && isset($_POST["txtEmail"]) && (isset($_POST["txtPassword"]) && (isset($_POST["chkAgree"])))) {
        $strName = htmlentities(trim($_POST["txtName"]));
        $strEmail = htmlentities(trim($_POST["txtEmail"]));
        $strPassword = htmlentities(trim($_POST["txtPassword"]));
        $booAgree = $_POST['chkAgree']; 

        $strError = "";

        if ($strName == "") {
            $strError = $strError . "Name is required\n";
        }

        if ($strEmail == "") {
            $strError = $strError . "Email is required\n";
        } else {
            if (!validate_email($strEmail)){ 
                log_error("Email found to be invalid: " . $strEmail, "Invalid email", $page, false);
                $strError = $strError . "Email Address is not recognized as a valid email.";
            } else {
                // Check to make sure this email not already registered
                $sql_query = sprintf("SELECT 1 from users where upper(`email`) = upper('%s');", mysql_real_escape_string($strEmail));
                $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);                
		
		if (mysql_num_rows($result)>0) {
			$strError = $strError . "This email address is already registered.";
                        log_error("Attempted to register email that was already registered. Email: " . $strEmail . ", Name: " . $strName, "Attempted to register email that was already registered", $page, false);  
		}
            }        
        }

        if ($strPassword == "") {
            $strError = $strError . "Password is required\n";
        } else {
            if (strlen($strPassword) < 6) { 
                $strError = $strError . "Password is too short.  It must be between 6 and 30 characters in length\n";
            } elseif (strlen($strPassword) > 30) { 
                $strError = $strError . "Password is too long.  It must be between 6 and 30 characters in length\n";
            } 
        }
        
        if ($booAgree <> "on") { 
		$strError = $strError . "You must agree with the Terms of Use and Privacy Policy in order to continue"; 
	}
	

        if ($strError == "") {
            $strPassword = sha1($strPassword);
		
            // save to database
            $sql_query = sprintf("Insert into `cbwire`.`users` 
                            (`email`, `password`, `name`, `entered_date`, `modified_date`) values 
                            ('%s', '%s', '%s', '%s', '%s');",
                            mysql_real_escape_string($strEmail), 
                            mysql_real_escape_string($strPassword), 
                            mysql_real_escape_string($strName), 
                            date("Y-m-d H:i:s"), 
                            date("Y-m-d H:i:s"));
            $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);
    
            $mysqlInsertID = mysql_insert_id();
            if ($mysqlInsertID > 0) {
                echo "success";
                
                include_once "../includes/clsEmailMessage.php";
                $email = new EmailMessage();

                if ($email->sendRegistrationInformation($mysqlInsertID)) {
                } else {
                    log_error("Issue with email sending after registration", "Registration Email did not send properly, but registration is saved in database. new user id: " . $mysqlInsertID, $page, false);
                }  
            } else {
                log_error("Attempted to register but insert id not > 0.  Returned error to user. Email: " . $strEmail . ", Name: " . $strName, "Attempted to register but failed", $page, false);
            }
        } else {
            echo "display" . $strError;
        }
    } else {
        log_error("Post variable not properly set Name: " . $_POST["txtName"] . ", Email" . $_POST["txtEmail"] . ", Password set: " . isset($_POST["txtPassword"]) . ", chkAgree " . $_POST["chkAgree"], "Attempted to register but failed", $page, false);
    }
?>
        
        
        
        
        
        
        
        
        
        
  