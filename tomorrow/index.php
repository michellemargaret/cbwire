<?php 
    $strFromDate = date("Y") . "-" . date("m") . "-" . sprintf("%02d", (date("d")+1));
    $strToDate = date("Y") . "-" . date("m") . "-" . sprintf("%02d", (date("d")+1));
             
    header('Location: http://www.cbwire.ca/datesearch.php?a=Tomorrow&b=' . urlencode($strFromDate) . '&c=' . urlencode($strToDate));
?>