<?php 
    include_once "../includes/connect.php";
    $link = connect();
    $page = "userLogout.php";
    if (isset($_SESSION['userid'])) { if (is_numeric($_SESSION['userid'])) {  if ($_SESSION['userid'] > 0) {  $userid = $_SESSION['userid']; }  } }
    
    unset($_SESSION['userid']);
    unset($_SESSION['strName']);
    unset($_SESSION['strEmail']);
    unset($_SESSION);
            
    echo "success";
?>