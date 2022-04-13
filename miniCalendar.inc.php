<?php   
    $page = "miniCalendar.inc.php";
?>

        <div style="margin-left: 10px;">
            <?php 
                include_once "includes/calendarMonth.inc.php";
                $date = date("Y-m-d");
                
                printMonth(date("m"), date("Y"), true);
                echo "<br>";
                $date = strtotime(date("Y-m-d", strtotime($date)) . " +1 month");
                printMonth(date("m", $date), date("Y", $date), false);                
            ?>
        </div>