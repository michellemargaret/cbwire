<?php 
    $strSearchDate = mktime(0, 0, 0, date("m"), date("d")+1, date("y"));
    $intWeekend1 = 6-date("N");
    $intWeekend2 = $intWeekend1+1;    
    $strFromDate = date("Y") . "-" . date("m") . "-" . sprintf("%02d", (date("d")+$intWeekend1));
    $strToDate = date("Y") . "-" . date("m") . "-" . sprintf("%02d", (date("d")+$intWeekend2));
  
    header('Location: http://www.cbwire.ca/datesearch.php?a=This Weekend&b=' . urlencode($strFromDate) . '&c=' . urlencode($strToDate));
?>