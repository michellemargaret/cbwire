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
            WHERE l.`deleted` = 0 and l.`directory` = 1 
            ORDER BY l.`highlight` desc, l.`title`
            LIMIT 20;");

    $result1 = mysql_query($sql_query1) or log_error($sql_query1, mysql_error(), $page, true);

    if (mysql_num_rows($result1) > 0) {
        $x = 0;
        while ($result_row = mysql_fetch_assoc($result1)) {
            $strTitle = "";
            $intListingID = 0;
            $strWhereComm = "";
            $strWhereLink = "";
            if (!is_null($result_row['title'])) { $strTitle = $result_row['title']; }
            if (!is_null($result_row['id'])) { $intListingID = $result_row['id']; }
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
                
                echo "<li class=\"$rowCss\">
                        <div style=\"display: none;\" id=\"liID\" class=\"search_result_id\">$intListingID</div>
                        <a href=\"view.php?in=$intListingID\" class=\"li_title\">$strTitle</a>
                        <a href=\"view.php?in=$intListingID\" class=\"li_location\">$strWhereLink</a>
                        <div style=\"clear:both\"></div>
                        </li>
                ";
            }					
        }   
        $rowCss = ($x%2 == 0)? 'li_row_alt': 'li_row'; 
        echo "<li class=\"$rowCss\">
                <a href=\"listall.php?a=directory\" class=\"li_location\">More...</a>
                <div style=\"clear:both\"></div>
                </li>
        ";
    }  else {
        echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"update_listing_pre.php\">No results.  Click here to add a listing.</a>";
    }                                                                        
?>
</ul>