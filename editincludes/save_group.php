<?php 
    include_once "../includes/connect.php";
    
    $link = connect();
    $page = "save_group.php";
    
    $trace = "";
    
    $editGroupID = 0;
    
        
    if (isset($_SESSION["editGroupID"]) && is_numeric($_SESSION["editGroupID"])) {    
        $editGroupID = $_SESSION["editGroupID"]; 
    }
    
    $strTitle = ""; 
    $strDescription = "";
    $intPictureID = 0;

    if(isset($_POST['txtTitle'])) { $strTitle = htmlentities(trim($_POST['txtTitle']));  } 
    if(isset($_POST['txtDescription'])) { 
        $strDescription = trim($_POST['txtDescription']); 
        $strDescription = htmlentities($strDescription);
    }
    if(isset($_POST["txtPictureID"])) {
        if (is_numeric($_POST["txtPictureID"])) {
            $intPictureID = $_POST["txtPictureID"];
        }
    }
    
    if ($editGroupID > 0) {        
        $sql_query = sprintf("Update `cbwire`.`group` set `pictureid`=%s,
		`title`='%s', `description`='%s' where id=%s;",
		mysql_real_escape_string($intPictureID), 
		mysql_real_escape_string($strTitle), 
		mysql_real_escape_string($strDescription),
		mysql_real_escape_string($editGroupID));
        $trace = $trace . $sql_query . "\n\n";
	$result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);
         
        echo $editGroupID; // Prompts calling page to give confirmation message   
    } else {          
        $sql_query = sprintf("Insert into `cbwire`.`group` (`pictureid`, `title`, `description`) values (%s, '%s', '%s');",
		mysql_real_escape_string($intPictureID), 
		mysql_real_escape_string($strTitle), 
		mysql_real_escape_string($strDescription));
        $trace = $trace . $sql_query . "\n\n";
	$result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);
         
        echo mysql_insert_id(); // Prompts calling page to give confirmation message   
    }
?>