<?php   
    include_once "includes/func.php"; 
    
    $page = "attraction_highlights.php";
?>

<div class="section_heading">Highlights</div>

<ul>
<?php
    $sql_query1 = sprintf("
        SELECT distinct(l.`id`), l.`title`, 
            (SELECT GROUP_CONCAT(distinct com.name) FROM contact 
                                                inner join community com on contact.communityid = com.id 
                                                WHERE contact.listingsid = l.id and com.name <> '') group2,
            (SELECT GROUP_CONCAT(distinct link.`title`) FROM listings link 
                                                inner join contact c2 on c2.`linkid` = link.`id` and link.`title` <> ''
                                                WHERE c2.`listingsid` = l.`id`) group1
            from `cbwire`.`listings` l
            WHERE l.`deleted` = 0 and l.`classifieds` = 1 
            ORDER BY l.`highlight` desc, l.`title`
            LIMIT 6;");

    $result1 = mysql_query($sql_query1) or log_error($sql_query1, mysql_error(), $page, true);

    if (mysql_num_rows($result1) > 0) {
        $x = 0;
        while ($result_row = mysql_fetch_assoc($result1)) {
            $strTitle = "";
            $intListingID = 0;
            $strDates = "";
            $strWhereComm = "";
            $strWhereLink = "";
            if (!is_null($result_row['title'])) { $strTitle = $result_row['title']; }
            if (!is_null($result_row['id'])) { $intListingID = $result_row['id']; }
            if (!is_null($result_row['group2'])) { $strWhereComm = $result_row['group2']; }										
            if (!is_null($result_row['group1'])) { $strWhereLink = $result_row['group1']; }

            $sql_query2 = sprintf("SELECT w.`id`, w.`start_date`, w.`end_date`, w.`start_time`, w.`end_time`, w.`recursive`, w.`expiry`
                                from `cbwire`.`when` w 
                                where w.`listingid` = %s and																
                                ((w.`start_date` >= '%s') or (w.`end_date` >= '%s'))
                                ORDER BY w.`start_date`, w.`start_time`, w.`end_time`
                                LIMIT 3;",
                                mysql_real_escape_string($intListingID),
                                mysql_real_escape_string(mktime(0, 0, 0, date("m"), date("d"), date("y"))),
                                mysql_real_escape_string(mktime(0, 0, 0, date("m"), date("d"), date("y"))));

             $result2 = mysql_query($sql_query2) or log_error($sql_query2, mysql_error(), $page, false);
                
             while ($result_row = mysql_fetch_assoc($result2)) {
                $strTime1 = "";
                if (!is_null($result_row['start_time'])) { 
                    $strTemp = $result_row['start_time']; 
                    if ($strTemp <> "") {
                            $strTime1 = date("g:iA", $strTemp);
                    }												
                }
                if (!is_null($result_row['start_date'])) { 
                    $strTemp = $result_row['start_date']; 
                    if ($strTemp <> "") {
                        if ($strTemp == mktime(0, 0, 0, date("m"), date("d"), date("y"))) {
                            $strTime1 = "Today at " . $strTime1;
                        } else if ($strTemp == mktime(0, 0, 0, date("m"), date("d")+1, date("y"))) {
                            $strTime1 = "Tomorrow at " . $strTime1;
                        } else if ($strTemp < mktime(0, 0, 0, date("m"), date("d")+6, date("y"))) {
                            $strTime1 = date("D", $strTemp) . " " . $strTime1;
                        } else {
                            $strTime1 = date("D M d", $strTemp) . " " . $strTime1;
                        }
                    }												
                }
                
                $strDates = $strDates . "<div style=\"display:block;\">" . $strTime1 . "</div>";
             }
             
            if (($intListingID > 0) && ($strTitle <> "")) {
                $rowCss = ($x%2 == 0)? 'li_row_alt': 'li_row'; 
                $x = $x+1;

                if ($strWhereComm <> "") {
                    if ($strWhereLink <> "") {
                        $strWhereLink = $strWhereLink . ", " . $strWhereComm;
                    } else {
                        $strWhereLink = $strWhereComm;
                    }
                }

                if (strlen($strWhereLink) > 75) {
                    $strWhereLink = substr($strWhereLink, 0, 72) . "...";
                }
                if ($strDates == "") { $strDates = "No upcoming scheduled dates"; }
                echo "<li class=\"$rowCss\">
                        <div style=\"display: none;\" id=\"liID\" class=\"search_result_id\">$intListingID</div>
                        <a href=\"view.php?in=$intListingID\" class=\"li_title\">$strTitle</a>
                        <a href=\"view.php?in=$intListingID\" class=\"li_date\">$strDates</a> 
                        <a href=\"view.php?in=$intListingID\" class=\"li_location\">$strWhereLink</a>
                        <div style=\"clear:both\"></div>
                        </li>
                ";
            }					
        }   
        $rowCss = ($x%2 == 0)? 'li_row_alt': 'li_row'; 
        echo "<li class=\"$rowCss\">
                <a href=\"listall.php?a=things\" class=\"li_location\">More...</a>
                <div style=\"clear:both\"></div>
                </li>
        ";
    }  else {
        echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"update_listing_pre.php\">No results.  Click here to add a listing.</a>";
    }                                                                        
?>
</ul>