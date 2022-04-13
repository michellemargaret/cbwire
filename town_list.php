<?php   
    include_once "includes/func.php"; 
    
    $page = "town_list.php";
    
    $sql_query = sprintf("SELECT c.`id`, c.`name`
                                FROM `cbwire`.`community` c
                                ORDER BY c.`name`;");
    $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);	
?>

<?php
    $x = 0;
    while ($result_row = mysql_fetch_assoc($result)) {        
        echo sprintf("<a href=\"town.php?in=%s\">%s</a> ", $result_row['id'], $result_row['name']);
    }
?>