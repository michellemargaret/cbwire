<?php 
    $strFromDate = date("Y") . "-" . date("m") . "-" . sprintf("%02d", (date("d")));
    $strToDate = date("Y") . "-" . date("m") . "-" . sprintf("%02d", (date("d")));
             
    header('Location: http://www.cbwire.ca/datesearch.php?a=Today&b=' . urlencode($strFromDate) . '&c=' . urlencode($strToDate));
?>