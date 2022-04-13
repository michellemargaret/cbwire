<?php 
    $strFromDate = date("Y") . "-" . date("m") . "-" . date("d");
    $strToDate = date("Y") . "-" . date("m") . "-" . sprintf("%02d", (date("d")+7));
  
    header('Location: http://www.cbwire.ca/datesearch.php?a=This Week&b=' . urlencode($strFromDate) . '&c=' . urlencode($strToDate));
?>