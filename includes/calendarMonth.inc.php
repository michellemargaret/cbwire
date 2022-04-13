<?php   
 //   include_once "func.php"; 
    
    $page = "includes/calendarMonth.inc.php";
    
    
    function printMonth($inMonth, $inYear, $markPastDays) {   
        $printingDate = mktime(0, 0, 0, $inMonth, 1, $inYear);     
        $thisMonth = $inMonth;    
        $daysInMonth = date("t", $printingDate);
        $startDayOfWeek = date("w", $printingDate);
        $printMonth = $thisMonth;
        $pastDaysCSS = "";
        
        if ($markPastDays == true) {
            $pastDaysCSS = "pastCalendarDay";
        }
        
        echo "<div class=\"calendarMonth\">" . date("F", $printingDate) . "</div>
                <div style=\"clear: both\"></div>
";
        
        ?>
                    <div class="calendarRow">
                        <div class="calendarDayOfTheWeek">Sun</div>
                        <div class="calendarDayOfTheWeek">Mon</div>
                        <div class="calendarDayOfTheWeek">Tue</div>
                        <div class="calendarDayOfTheWeek">Wed</div>
                        <div class="calendarDayOfTheWeek">Thu</div>
                        <div class="calendarDayOfTheWeek">Fri</div>
                        <div class="calendarDayOfTheWeek">Sat</div>
                    </div>
                    <div style="clear:both;"></div>
                    <div class="calendarEndLine"></div>
                    <div style="clear:both;"></div>
        <?php
            for ($i=0; $i<$startDayOfWeek; $i++) {
                    echo "<div class=\"emptyCalendarCell\"></div>
                         ";                
            }          
            
               while ($printMonth == $thisMonth) {
                    $formattedDate = date("Y-m-d", $printingDate);
                    $printDay = date("j", $printingDate);
                    
                    if ($markPastDays == true) {
                        switch ($pastDaysCSS) {
                            case "":
                                break;
                            case "today":
                                $pastDaysCSS = "";
                                break;
                            default:
                                $currentDate = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
                                if ($currentDate == $printingDate) { $pastDaysCSS = "today"; }
                                break;
                        }
                    }
                    echo "<div class=\"calendarCell " . $pastDaysCSS . "\">
                        <a href=\"datesearch.php?a=Date+Search&b=" . $formattedDate . "\">" .  $printDay . "</a>  
                        <div style=\"display:none\" class=\"printingDate\">" . date("l, F jS", $printingDate) . "</div> 
                        <div style=\"display:none\" class=\"formattedDate\">" . $formattedDate . "</div> 
                    </div>
                   ";
                    $printingDate = mktime(0, 0, 0, date("m", $printingDate), date("d", $printingDate)+1, date("Y", $printingDate));
                    $printMonth = date("m", $printingDate);                  
                }
                
                echo "<div style=\"clear:both;\"></div>";        
     }  
?>   