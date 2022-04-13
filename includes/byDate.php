<?php 
    include_once "func.php";
    include_once "searchfunc.inc.php";    
    
    $page = "includes/byDate.php";
    
    $inDate = "";
    
    if (isset($_GET["inDate"])) { $inDate = $_GET["inDate"]; } 
      
    $booDoSearch = true;
    
    $strSearchFromDate = "";
    $strSearchToDate = "";
    
    $printedDate = "";
    
    $fromY = "";
    $fromM = "";
    $fromD = "";
    
    $toY = "";
    $toM = "";
    $toD = "";
    
    //  Make sure b is a good date in format YYYY-MM-DD
    if (strlen($inDate) == 10) {
        $fromY = substr($inDate, 0, 4);
        $fromM = substr($inDate, 5, 2);
        $fromD = substr($inDate, 8, 2);
    } 
    
    if (is_numeric($fromY) && (is_numeric($fromM) && (is_numeric($fromD)))) {
        $strSearchFromDate = mktime(0, 0, 0, $fromM, $fromD, $fromY);
        $strSearchToDate = mktime(23, 59, 59, $fromM, $fromD, $fromY);
        $printingDate = mktime(0, 0, 0, $fromM, $fromD, $fromY);
        $printedDate = date("l, F jS", $printingDate);
    } 
   
    if (!((is_numeric($strSearchFromDate)) && (is_numeric($strSearchToDate) || ($strSearchToDate == "") || ($strSearchFromDate == "")))) {        
        $booDoSearch = false;
    } 
          
   
?>
    <div class="calendarHoverDate"><?php echo $printedDate; ?></div>
        <?php    
                if ($booDoSearch) {
                    $result = returnDateSearch($page, $strSearchFromDate, $strSearchToDate);
                    if (get_resource_type($result) == "mysql result") {
                        // Print Results
                        showCalendarHoverListings($page, $result, $strSearchFromDate, $strSearchToDate);
                    } else {
                        echo "Oops. Something went wrong. If this happens again, please let us know! admin@cbwire.ca";
                    }
                } else {
                    echo "Oops. Something went wrong. If this happens again, please let us know! admin@cbwire.ca";
                }
        ?>

           