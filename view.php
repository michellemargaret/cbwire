<?php 
    include_once "includes/func.php";    
    
    $page = "view.php";
    $inListingID = 0; 
    $strMetaTitle = "";
    $strMetaDescription = "";
    $showListing = true;
    
    if (isset($_GET["in"])) {
        if (is_numeric($_GET["in"])) {
            $inListingID = $_GET["in"];
        }
    }
    
    if ($inListingID > 0) {
        $sql_query = sprintf("SELECT l.`title`, l.`description`
                                from `cbwire`.`listings` l                                
                                where l.`id`=%s and l.`deleted`=0;",
                                mysql_real_escape_string($inListingID));

        $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);

        if ($result_row = mysql_fetch_assoc($result)) {
            $strTitle = "";
            $strDescription = "";
            if (!is_null($result_row['title'])) { $strTitle = $result_row['title']; }
            if (!is_null($result_row['description'])) { $strDescription = $result_row['description']; }
            
            if ($strTitle <> "") {
                $strMetaTitle = $strTitle . " [cbwire.ca] ";
            } 
            if (($strTitle <> "") && ($strDescription <> "")) {
                $strMetaDescription = $strMetaTitle . ": " . $strDescription;
            } else if ($strDescription <> "") {
                $strMetaDescription = $strDescription;
            } else {
                $strMetaDescription = $strTitle;
            }
            
        }  else {
            log_error("No results returned for listing " . $inListingID, "User NOT given error page", $page, false);
            $showListing = false;
        }        
    }
    include_once "includes/header.inc.php";
?>
<div class="breadcrumbs"><a href="index.php">Home</a> >></div>
                
                
                
<?php 
    include_once "right_column.php";
    include_right_column(true);
?>
                <div class="listing_column1"> 
<?php

    if ($showListing) {
        include_once "view_details.php";
    } else {
        echo "<div class=\"page_message\">The listing you are looking for could not be found.  This normally means it has been deleted.  If you continue to have problems, please contact admin@cbwire.ca</div> ";
    }
?>
                </div>
                    <?php
    include_once "includes/footer.inc.php";
?>