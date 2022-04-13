<?php   
    include_once "includes/func.php"; 
    
    $page = "this_week.inc.php";
?>

<?php
    $strSearchDate = mktime(0, 0, 0, date("m"), date("d"), date("y"));
?>
<?php
    $sql_query1 = sprintf("
        SELECT distinct(l.`id`), l.`title`, l.`description`
            from `cbwire`.`listings` l
            INNER JOIN `cbwire`.`when` w on w.`listingid` = l.`id`

            WHERE l.`deleted` = 0 and l.`thingstodo` = 1 and
            (
                    (w.`end_date` is null or w.`end_date` = '' or w.`end_date` = w.`start_date`)
                    and w.`start_date` = '%s'
            ) or (
                    '%s' between w.`start_date` and w.`end_date`
            )
            ORDER BY l.`highlight` desc, w.`start_date` asc, l.`title`
            LIMIT 15;",
        mysql_real_escape_string($strSearchDate),
        mysql_real_escape_string($strSearchDate));

    $result1 = mysql_query($sql_query1) or log_error($sql_query1, mysql_error(), $page, false);

    if (mysql_num_rows($result1) > 0) {
        $x = 0;
        $numRows = 0;
        while (($result_row = mysql_fetch_assoc($result1)) && ($numRows < 20)) {
            $strTitle = "";
            $strDescription = "";
            $intListingID = 0;
            
            if (!is_null($result_row['title'])) { $strTitle = $result_row['title']; }
            if (!is_null($result_row['description'])) { $strDescription = $result_row['description']; }
            if (!is_null($result_row['id'])) { $intListingID = $result_row['id']; }
           
            if (($intListingID > 0) && ($strTitle <> "")) {
                $rowCss = ($x%2 == 0)? 'index_row_alt': 'index_row'; 
                $x = $x+1;
                $numRows = $numRows + 2; // Row for title, row for space between this and next listing

                echo "
                        <div class=\"" . $rowCss . "\">
                            <div style=\"display: none;\" id=\"liID\" class=\"search_result_id\">$intListingID</div>
                            <a href=\"view.php?in=$intListingID\" class=\"li_index_title\">$strTitle</a>
                     ";
                
                if ($strDescription <> "") {
                    if (strlen($strDescription) > 60) {
                            $strDescription = substr($strDescription, 0, 57) . "...";
                    }
                    $numRows = $numRows + 2;
                    echo "<a href=\"view.php?in=$intListingID\" class=\"li_index_desc\">$strDescription</a>";               
                }
                
                echo "
                        </div>";
            }					
        }   

    } else {            
    }                                                                          
?>
