<?php 
    include_once "../includes/connect.php";
    $link = connect();
    $page = "userLogin.php";
    
    $inEmail = "";
    $inPassword = "";
    
    if (isset($_POST["LoginEmailTextbox"]) && (isset($_POST["LoginPasswordTextbox"]))) {
        $inEmail = $_POST["LoginEmailTextbox"];
        $inPassword = $_POST["LoginPasswordTextbox"];
    } elseif (isset($_POST["txtLoginEmailP"]) && (isset($_POST["txtLoginPasswordP"]))) {
        $inEmail = $_POST["txtLoginEmailP"];
        $inPassword = $_POST["txtLoginPasswordP"];
    } else {
        if (!isset($_POST["txtLoginEmail"])) {
            echo "\nPOST Email wasn't set";
        }
        if (!isset($_POST["txtLoginPassword"])) {
            echo "\nPOST Password wasn't set";
        }
        
    }

    $sql_query_log = sprintf("Insert into `cbwire`.`log_login_attempts` (`email`, `status`, `session_id`, `date`) values ('%s', '1 Login Attempt Start', '%s', '%s');", mysql_real_escape_string($inEmail), mysql_real_escape_string(session_id()),date("Y-m-d H:i:s"));
    mysql_query($sql_query_log) or log_error($sql_query_log, mysql_error(), $page, false);
                        
    if (($inEmail <> "") && ($inPassword <> "")) {        
        $sql_query_log = sprintf("Insert into `cbwire`.`log_login_attempts` (`email`, `status`, `session_id`, `date`) values ('%s', '2 Login Attempt Progress', '%s', '%s');", mysql_real_escape_string($inEmail), mysql_real_escape_string(session_id()),date("Y-m-d H:i:s"));
        mysql_query($sql_query_log) or log_error($sql_query_log, mysql_error(), $page, false);
         
        if (isset($_SESSION["userid"])) {
            unset($_SESSION["userid"]);            
        }
        
        $inPassword = sha1($inPassword);
        // get users info
        $sql_query = sprintf("Select u.`id` as userID, u.`name`, s.`sessionid` from `users` u
                                LEFT OUTER JOIN (
                                    SELECT max(`id`) AS sessionid, `userid` 
                                    FROM `session` 
                                    GROUP BY `userid` 
                                ) s ON s.`userid` = u.`id` 
                                WHERE `email` = '%s' and `password` = '%s'" , 
                            mysql_real_escape_string($inEmail),  mysql_real_escape_string($inPassword));
        $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);

        if (mysql_num_rows($result) > 0) {
            
            $sql_query_log = sprintf("Insert into `cbwire`.`log_login_attempts` (`email`, `status`, `session_id`, `date`) values ('%s', '3 Login Match', '%s', '%s');", mysql_real_escape_string($inEmail), mysql_real_escape_string(session_id()),date("Y-m-d H:i:s"));
            mysql_query($sql_query_log) or log_error($sql_query_log, mysql_error(), $page, false);
      
            $result_row = mysql_fetch_assoc($result);
            $intUserID = 0;
            $strName = "";
            $intSessionID = 0;

            if (!is_null($result_row['userID'])) { $intUserID = $result_row['userID']; }
            if (!is_null($result_row['name'])) { $strName = $result_row['name']; }
            if (!is_null($result_row['sessionid'])) { $intSessionID = $result_row['sessionid']; }

            if ($intUserID > 0) {                
                $userid = $intUserID;
                $sql_query = sprintf("Update `cbwire`.`users` set `lastlogin_date`='%s' where `id`=%s", date("Y-m-d H:i:s"), $intUserID);
                $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);                    

                session_regenerate_id();
                $strNewSessionID = session_id();

                $_SESSION['userid'] = $intUserID;	
                $_SESSION['strName'] = $strName;
                $_SESSION['strEmail'] = $inEmail;

                if ($intSessionID > 0) {
                    $sql_query = sprintf("Update `cbwire`.`session` set
                                        `sessionid`='%s', `userid`=%s, `start_date`='%s', `updated_date`='%s', `expired_date`='%s', `active`=1;",
                                    mysql_real_escape_string($strNewSessionID), 
                                    mysql_real_escape_string($intUserID),  
                                    date("Y-m-d H:i:s"), 
                                    date("Y-m-d H:i:s"), 
                                    date("Y-m-d H:i:s", strtotime("+1 Hour")));
                        mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);
                } else {
                    $sql_query = sprintf("Insert into `cbwire`.`session` (`sessionid`, `userid`, `start_date`, `updated_date`, `expired_date`, `active`) values ('%s', %s, '%s', '%s', '%s', 1);",
                                    mysql_real_escape_string($strNewSessionID), 
                                    mysql_real_escape_string($intUserID),  
                                    date("Y-m-d H:i:s"), 
                                    date("Y-m-d H:i:s"), 
                                    date("Y-m-d H:i:s", strtotime("+1 Hour")));
                        mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);
                } 

                $sql_query_log = sprintf("Insert into `cbwire`.`log_login_attempts` (`email`, `status`, `session_id`, `date`) values ('%s', '4 Login Success', '%s', '%s');", mysql_real_escape_string($inEmail), mysql_real_escape_string(session_id()),date("Y-m-d H:i:s"));
                mysql_query($sql_query_log) or log_error($sql_query_log, mysql_error(), $page, false);
                echo "success";
            } else {                
                $sql_query_log = sprintf("Insert into `cbwire`.`log_login_attempts` (`email`, `status`, `session_id`, `date`) values ('%s', '3b Failed Login with intID: %s', '%s', '%s');", mysql_real_escape_string($inEmail), mysql_real_escape_string($intID), mysql_real_escape_string(session_id()),date("Y-m-d H:i:s"));
                mysql_query($sql_query_log) or log_error($sql_query_log, mysql_error(), $page, false);
      
                echo "intUserID should be greater than 0.  It was " . $intUserID;                
            }            
        } else {
            $sql_query_log = sprintf("Insert into `cbwire`.`log_login_attempts` (`email`, `status`, `session_id`, `date`) values ('%s', '3c Failed Login', '%s', '%s');", mysql_real_escape_string($inEmail), mysql_real_escape_string(session_id()),date("Y-m-d H:i:s"));
            mysql_query($sql_query_log) or log_error($sql_query_log, mysql_error(), $page, false);
      
            echo mysql_num_rows($result) . " Results were returned.  Expected 1. Email: " . $inEmail;
        }
    } else {
        echo "\nEmail " . $inEmail . " \nPassword " . $inPassword . " ";
        
        $sql_query_log = sprintf("Insert into `cbwire`.`log_login_attempts` (`email`, `status`, `session_id`, `date`) values ('%s', '2b Failed Login with email: %s and password: %s', '%s', '%s');", mysql_real_escape_string($inEmail), mysql_real_escape_string($inEmail), mysql_real_escape_string($inPassword), mysql_real_escape_string(session_id()),date("Y-m-d H:i:s"));
        mysql_query($sql_query_log) or log_error($sql_query_log, mysql_error(), $page, false);
      
    }
    
    echo " \n end_error";   
?>