<?php 
    include_once "../includes/connect.php";
    $link = connect();
    
    $page = "change_password.php";
    $userid = get_user_id();
    
    if ((isset($_POST["txtOldPassword"]) && (isset($_POST["txtNewPassword"]) && (isset($_POST["txtNewPasswordCopy"]))))) {
        $strNewPassword = htmlentities(trim($_POST["txtNewPassword"]));
        $strOldPassword = htmlentities(trim($_POST["txtOldPassword"]));
        $strNewPasswordCopy = htmlentities(trim($_POST["txtNewPasswordCopy"]));

        $strError = "";

        if ($strOldPassword == "") {
            $strError = $strError . "Old Password is required\n";
        } 
        
        if ($strNewPassword == "") {
            $strError = $strError . "New Password is required\n";
        } else if ($strNewPasswordCopy == "") {
            $strError = $strError . "Re-enter New Password is required\n";
        } else if ($strNewPassword <> $strNewPasswordCopy) {
            $strError = $strError . "New Password and Re-enter New Password do not match\n";
        } else if (strlen($strNewPassword) < 6) { 
                $strError = $strError . "New Password is too short.  It must be between 6 and 30 characters in length\n";
        } elseif (strlen($strNewPassword) > 30) { 
                $strError = $strError . "Password is too long.  It must be between 6 and 30 characters in length\n";
        }
        
        if ($strError == "") {
            $strOldPassword = sha1($strOldPassword);
            $sql_query = sprintf("SELECT 1 from users where `id`=%s and `password`='%s';", mysql_real_escape_string($userid), mysql_real_escape_string($strOldPassword));
            $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);

            if (mysql_num_rows($result)==0) {
                $strError = $strError . "Old Password not correct.\n";
                log_error("Attempted to change password but old password not entered correctly.", "Attempted to change password but failed", $page, false);
            }
        }

        if (($strError == "") && (isLoggedIn())) {
            $strNewPassword = sha1($strNewPassword);
	
            $sql_query = sprintf("Update `cbwire`.`users` set `password` = '%s', `modified_date`='%s' where `id`=%s" , mysql_real_escape_string($strNewPassword), date("Y-m-d H:i:s"), mysql_real_escape_string($userid));
            $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);
    
            if (mysql_affected_rows() > 0) {
                echo "success";
            } else {
                log_error("Attempted to change password but affected rows = 0.  Returned error to user.", "Attempted to change password but failed", $page, false);
            }
        } else {
            if ($strError <> "") {
                echo "display" . $strError;
            } else {
                
            }
        }
    } else {
        log_error("Post variable not properly set old pw: " . isset($_POST["txtOldPassword"]) . ", new pw: " . isset($_POST["txtNewPassword"]) . ", new pw copy: " . isset($_POST["txtNewPasswordCopy"]), "Attempted to change password but failed", $page, false);
    }
?>
        
        
        
        
        
        
        
        
        
        
  