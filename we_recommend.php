<?php   
    include_once "includes/func.php"; 
    
    $page = "we_recommend.php";
?>

<div class="section_heading">Highlights</div>

<ul>
<li class="listTitle">
<?php
    $strSearchDate = mktime(0, 0, 0, date("m"), date("d"), date("y"));

    if (date("G") > 17) {
        $strSearchDate = mktime(0, 0, 0, date("m"), date("d")+1, date("y"));
        ?>
        <a href="datesearch.php?a=Tomorrow&b=<?php echo date("Y-m-d", $strSearchDate); ?>">Tomorrow</a>
        <?php
    } else {
        ?>									
        <a href="datesearch.php?a=Today&b=<?php echo date("Y-m-d", $strSearchDate); ?>">Today</a>
        <?php
    }	    
?>
</li>
<?php
    $sql_query1 = sprintf("
        SELECT distinct(l.`id`), l.`title`, w.`start_time`, w.`end_time`,
            (SELECT GROUP_CONCAT(distinct com.name) FROM contact 
                                                inner join community com on contact.communityid = com.id 
                                                WHERE contact.listingsid = l.id and com.name <> '') group2,
            (SELECT GROUP_CONCAT(distinct link.`title`) FROM listings link 
                                                inner join contact c2 on c2.`linkid` = link.`id` and link.`title` <> ''
                                                WHERE c2.`listingsid` = l.`id`) group1
            from `cbwire`.`listings` l
            INNER JOIN `cbwire`.`when` w on w.`listingid` = l.`id`

            WHERE l.`deleted` = 0 and l.`thingstodo` = 1 and
            (
                    (w.`end_date` is null or w.`end_date` = '' or w.`end_date` = w.`start_date`)
                    and w.`start_date` = '%s'
            ) or (
                    '%s' between w.`start_date` and w.`end_date`
            )
            ORDER BY l.`highlight` desc, w.`start_time` desc, l.`title`
            LIMIT 6;",
        mysql_real_escape_string($strSearchDate),
        mysql_real_escape_string($strSearchDate));

    $result1 = mysql_query($sql_query1) or log_error($sql_query1, mysql_error(), $page, false);

    if (mysql_num_rows($result1) > 0) {
        $x = 0;
        while ($result_row = mysql_fetch_assoc($result1)) {
            $strTitle = "";
            $intListingID = 0;
            $strTime1 = "";
            $strTime2 = "";
            $strWhereComm = "";
            $strWhereLink = "";
            if (!is_null($result_row['title'])) { $strTitle = $result_row['title']; }
            if (!is_null($result_row['id'])) { $intListingID = $result_row['id']; }
            if (!is_null($result_row['start_time'])) { 
                $strTemp = $result_row['start_time']; 
                if ($strTemp <> "") {
                        $strTime1 = date("g:iA", $strTemp);
                }												
            }
            if (!is_null($result_row['end_time'])) { 
                $strTemp = $result_row['end_time']; 
                if ($strTemp <> "") {
                        $strTime2 = date("g:iA", $strTemp);
                }											
            }
            if (!is_null($result_row['group2'])) { $strWhereComm = $result_row['group2']; }										
            if (!is_null($result_row['group1'])) { $strWhereLink = $result_row['group1']; }

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

                if ($strTime1 == "") { $strTime1 = "&nbsp;"; }
                echo "<li class=\"$rowCss\">
                        <div style=\"display: none;\" id=\"liID\" class=\"search_result_id\">$intListingID</div>
                        <a href=\"view.php?in=$intListingID\" class=\"li_title\">$strTitle</a>
                        <a href=\"view.php?in=$intListingID\" class=\"li_date\">$strTime1</a> 
                        <a href=\"view.php?in=$intListingID\" class=\"li_location\">$strWhereLink</a>
                        <div style=\"clear:both\"></div>
                        </li>
                ";
            }					
        }   

    } else {            
    }                                                                          
?>
</ul>
<ul>
    <?php   
        $intWeekend1 = 6-date("N");
        $intWeekend2 = $intWeekend1+1;    
        
        $strFromDate = date("Y") . "-" . date("m") . "-" . sprintf("%02d", (date("d")+$intWeekend1));
        $strToDate = date("Y") . "-" . date("m") . "-" . sprintf("%02d", (date("d")+$intWeekend2));
        
        $intInFromDate = mktime(0, 0, 0, date("m"), date("d")+$intWeekend1, date("y"));
        $intInToDate = mktime(0, 0, 0, date("m"), date("d")+$intWeekend2, date("y"));
        
    ?>
                                                                                
    <li class="listTitle"><a href="datesearch.php?a=This Weekend&b=<?php echo urlencode($strFromDate); ?>&c=<?php echo urlencode($strToDate); ?>">This Weekend</a></li>
<?php
    $sql_query1 = sprintf("
        SELECT distinct(l.`id`), l.`title`, w.`start_time`, w.`end_time`, w.`start_date`,
            (SELECT GROUP_CONCAT(distinct com.name) FROM contact 
                                                    inner join community com on contact.communityid = com.id 
                                                    WHERE contact.listingsid = l.id and com.name <> '') group2,
            (SELECT GROUP_CONCAT(distinct link.`title`) FROM listings link 
                                                    inner join contact c2 on c2.`linkid` = link.`id` and link.`title` <> ''
                                                    WHERE c2.`listingsid` = l.`id`) group1
            from `cbwire`.`listings` l
            INNER JOIN `cbwire`.`when` w on w.`listingid` = l.`id`

            WHERE l.`deleted` = 0 and l.`thingstodo` = 1 and
            (
                                (w.`start_date` between '%s' and '%s')										  
                                or
                                (w.`end_date` between '%s' and '%s')
                                or
                                ('%s' between w.`start_date` and w.`end_date`)
                                or
                                ('%s' between w.`start_date` and w.`end_date`)
            )
            ORDER BY l.`highlight` desc, w.`start_date`, w.`start_time`, l.`title`
            LIMIT 6;",
            mysql_real_escape_string($intInFromDate),
            mysql_real_escape_string($intInToDate),
            mysql_real_escape_string($intInFromDate),
            mysql_real_escape_string($intInToDate),
            mysql_real_escape_string($intInFromDate),
            mysql_real_escape_string($intInToDate));

    $result1 = mysql_query($sql_query1) or log_error($sql_query1, mysql_error(), $page, false);

    if (mysql_num_rows($result1) > 0) {
        $x = 0;
        while ($result_row = mysql_fetch_assoc($result1)) {
            $strTitle = "";
            $intListingID = 0;
            $strTime1 = "";
            $strTime2 = "";
            $strWhereComm = "";
            $strWhereLink = "";
            if (!is_null($result_row['title'])) { $strTitle = $result_row['title']; }
            if (!is_null($result_row['id'])) { $intListingID = $result_row['id']; }
            if (!is_null($result_row['start_time'])) { 
                $strTemp = $result_row['start_time']; 
                if ($strTemp <> "") {
                        $strTime1 = date("g:iA", $strTemp);
                }												
            }
            if (!is_null($result_row['end_time'])) { 
                $strTemp = $result_row['end_time']; 
                if ($strTemp <> "") {
                        $strTime2 = date("g:iA", $strTemp);
                }											
            }
            if (!is_null($result_row['start_date'])) { 
                $strTemp = $result_row['start_date']; 
                if ($strTemp <> "") {
                        $strTime1 = date("D", $strTemp) . " " . $strTime1;
                }												
            }
            if (!is_null($result_row['group2'])) { $strWhereComm = $result_row['group2']; }										
            if (!is_null($result_row['group1'])) { $strWhereLink = $result_row['group1']; }

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

                if ($strTime1 == "") { $strTime1 = "&nbsp;"; }
                echo "<li class=\"$rowCss\">
                        <div style=\"display: none;\" id=\"liID\" class=\"search_result_id\">$intListingID</div>
                        <a href=\"view.php?in=$intListingID\" class=\"li_title\">$strTitle </a>
                        <a href=\"view.php?in=$intListingID\" class=\"li_date\">$strTime1 </a> 
                        <a href=\"view.php?in=$intListingID\" class=\"li_location\">$strWhereLink </a>
                        <div style=\"clear:both\"></div>
                        </li>
                ";
            }					
        }   

    } else {            
    }                                                                          
?>
</ul>