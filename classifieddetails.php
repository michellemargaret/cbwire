<?php 
    $in = "";
    if (isset($_GET["id"])) { $in = $_GET["id"]; }
    header( 'Location:index.php?in=' . $in);
?>