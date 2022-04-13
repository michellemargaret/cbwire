<?php 
    include_once "../includes/connect.php";
    include_once "../includes/funcEmail.php";
    $link = connect();
    
    $page = "includes/forget_password.php";
    
    /**  * The letter l (lowercase L) and the number 1  
    * have been removed, as they can be mistaken  
    * for each other.  As well as the letter o and
    * the number 0
    http://www.totallyphp.co.uk/code/create_a_random_password.htm
    */  
    function createRandomPassword() {     
            $chars = "abcdefghijkmnpqrstuvwxyz23456789";     
            srand((double)microtime()*1000000);     
            $i = 0;     $pass = '' ;     
            while ($i <= 8) {        
                    $num = rand() % 33;         
                    $tmp = substr($chars, $num, 1);         
                    $pass = $pass . $tmp;         
                    $i++;    
            }      
            return $pass;  
    }  	
        
    if (isset($_POST["txtForgetEmail"])) {
        $strEmail = htmlentities(trim($_POST["txtForgetEmail"]));

        $strError = "";

        if ($strEmail == "") {
            $strError = $strError . "Email is required\n";
        } else {
            if (!validate_email($strEmail)){ 
                log_error("Email found to be invalid: " . $strEmail, "Invalid email", $page, false);
                $strError = $strError . "Email Address is not recognized as a valid email.";
            } else {
                // Check to make sure this email exists
                $sql_query = sprintf("SELECT 1 from users where upper(`email`) = upper('%s');", mysql_real_escape_string($strEmail));
                $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);                
		
		if (mysql_num_rows($result)==0) {
			$strError = $strError . "This email address is not registered.";
                        log_error("Attempted to reset password on email that was not registered. Email: " . $strEmail, "Attempted to reset password on email that was not registered", $page, false);
		}
            }        
        }	

        if ($strError == "") {            
            // hash password
            $newPassword = createRandomPassword();
            $strPassword = sha1($newPassword);
            // save to database
            $sql_query = sprintf("Update `cbwire`.`users` set `password` = '%s', `modified_date`='%s' where upper(`email`)=upper('%s')" , mysql_real_escape_string($strPassword), date("Y-m-d H:i:s"), mysql_real_escape_string($strEmail));
            $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);
		
            if (mysql_affected_rows() > 0) {
                include_once "../includes/clsEmailMessage.php";
                $email = new EmailMessage();

                if ($email->forgotPassword($strEmail, $newPassword)) {
                    echo "success";
                } else {
                    log_error("Issue with email sending after reset password", "Reset Password Email did not send properly. Email: " . $strEmail, $page, false);
                    echo "....There was an error trying to send your email.  Please try again later or contact admin@cbwire.ca";
                }  
            } else {
                log_error("Attempted to reset password but affected rows = 0.  Returned error to user. Email: " . $strEmail, "Attempted to reset password but failed", $page, false);
            }
        } else {
            echo "...." . $strError;
        }
    } else {
        log_error("Post variable not properly set Email" . $_POST["txtForgetEmail"], "Attempted to reset password but failed", $page, false);
    }
?>
        
        
        
        
        
        
        
        
        
        
  