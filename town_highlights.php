<?php   
    include_once "includes/func.php"; 
    
    $page = "town_highlights.php";
    global $inTown;
?>

<div class="small_column">
    <div class="section_heading"><a href="listall.php?a=things&b=town&c=<?php echo $inTown; ?>">Things To Do</a></div>
        <ul>
    <?php
        $sql_query1 = sprintf("
            SELECT distinct(l.`id`), l.`title`, l.`description`
                from `cbwire`.`listings` l
                INNER JOIN `cbwire`.`contact` c on c.`listingsid` = l.`id`          
                left outer join 
                    (Select distinct w.`listingid`, min(w.`start_date`) as start_date 
                        from `when` w 
                        where w.`start_date` >= %s group by w.`listingid`
                    ) earliest_date on earliest_date.`listingid` = l.`id`    
                WHERE l.`deleted` = 0 and l.`thingstodo` = 1 and c.`communityid`=%s and earliest_date.start_date is not null
                order by earliest_date.`start_date` is null, earliest_date.`start_date`, l.`highlight` desc, l.`title`
                LIMIT 6;",
                mysql_real_escape_string(mktime(0, 0, 0, date("m"), date("d"), date("y"))),
            mysql_real_escape_string($inTown));

        $result1 = mysql_query($sql_query1) or log_error($sql_query1, mysql_error(), $page, false);

        if (mysql_num_rows($result1) > 0) {
            $x = 0;
            while ($result_row = mysql_fetch_assoc($result1)) {
                $strTitle = "";
                $strDescription = "";
                $intListingID = 0;                
                
                if (!is_null($result_row['title'])) { $strTitle = $result_row['title']; }
                if (!is_null($result_row['description'])) { $strDescription = $result_row['description']; }
                if (!is_null($result_row['id'])) { $intListingID = $result_row['id']; }

                if (($intListingID > 0) && ($strTitle <> "")) {
                    $strDescriptionCode = "";
                    if (strlen($strDescription) > 75) { $strDescription = sprintf("%s...", substr($strDescription, 0, 70)); }
                    if ($strDescription <> "") { $strDescriptionCode = "<a href=\"view.php?in=$intListingID\" class=\"sm_listing_description\">$strDescription</a>"; }
                    
                    $strWhenCode = "";
                    $sql_query_date = sprintf("SELECT id, start_date, end_date, start_time, end_time, recursive, expiry 
                                    from `cbwire`.`when` 
                                    where `listingid`=%s and `parentid` is null
                                    order by start_date, start_time, end_date, end_time, recursive, expiry;",
                                    mysql_real_escape_string($intListingID));
                    $result_date = mysql_query($sql_query_date) or log_error($sql_query_date, mysql_error(), $page, false);
                    $arrDates = array();
                    
                    include_once "includes/funcDateTime.inc.php";
                    $arrDates = format_datetime($result_date);

                    $strWhenCode = implode("<br>", $arrDates);
                    if ($strWhenCode <> "") { $strWhenCode = sprintf("<a href=\"view.php?in=$intListingID\" class=\"sm_listing_when\">%s</a>", $strWhenCode); }
                                                   
                    $strContactCode = "";                    
                    $sql_query2 = sprintf("SELECT contact.`phone`, com.`name` AS community_name, `other_community`,
                                        `location1`, `location2`, `location3`, contact.`linkid`, list.`title` as link_title
                                        from `cbwire`.`contact` contact
                                        LEFT OUTER JOIN `cbwire`.`community` com on com.`id` = contact.`communityid`
                                        LEFT OUTER JOIN `cbwire`.`listings` list on list.`id` = contact.`linkid`
                                        where contact.`listingsid`=%s and (list.`deleted`=0 or list.`deleted` is null)
                                        order by (length(`location1`) > 0), list.`title`, `location1`;",
                                        mysql_real_escape_string($intListingID));

                    $result2 = mysql_query($sql_query2) or log_error($sql_query2, mysql_error(), $page, false);
                    
                    while ($result_row2 = mysql_fetch_assoc($result2)) {
                        $strLinkTitle = "";
                        $strLocation1 = "";
                        $strLocation2 = "";
                        $strLocation3 = "";
                        $strPhone = "";
                        $strCommunity = "";
                        
                        // If the only location field is location1, put location1 and community on same line
                        if (!is_null($result_row2['link_title'])) { $strLinkTitle = "<a href=\"view.php?in=$intListingID\" class=\"sm_listing_linktitle\">" . $result_row2['link_title'] . "</a> "; }
                        if (!is_null($result_row2['location1'])) { if ($result_row2['location1'] <> "") { $strLocation1 = "<a href=\"view.php?in=$intListingID\" class=\"sm_listing_contact_line\">" . $result_row2['location1'] . "</a> "; } }
                        if (!is_null($result_row2['location2'])) { if ($result_row2['location2'] <> "") { $strLocation2 = "<a href=\"view.php?in=$intListingID\" class=\"sm_listing_contact_line\">" . $result_row2['location2'] . "</a> "; } }
                        if (!is_null($result_row2['location3'])) { if ($result_row2['location3'] <> "") { $strLocation3 = "<a href=\"view.php?in=$intListingID\" class=\"sm_listing_contact_line\">" . $result_row2['location3'] . "</a> "; } }
                        if (!is_null($result_row2['phone'])) { if ($result_row2['phone'] <> "") { $strPhone = "<a href=\"view.php?in=$intListingID\" class=\"sm_listing_phone\">" . $result_row2['phone'] . "</a> "; } }
                        if (!is_null($result_row2['community_name'])) { $strCommunity = "<a href=\"view.php?in=$intListingID\" class=\"sm_listing_contact_line nobr\">" . $result_row2['community_name'] . "</a> "; }
                        if ($strCommunity == "") {
                            if (!is_null($result_row2['other_community'])) { if ($result_row2['other_community'] <> "") { $strCommunity = "<a href=\"view.php?in=$intListingID\" class=\"sm_listing_contact_line nobr\">" . $result_row2['other_community'] . "</a> "; } }                           
                        }
                        
                        if (($strLocation2 == "") && ($strLocation3 == "")) {
                            
                        }
                        
                        $strContactCode = $strContactCode . $strLinkTitle . $strPhone . $strLocation1 . $strLocation2 . $strLocation3;
                    }
                    
                    $rowCss = ($x%2 == 0)? 'listing_row_alt': 'listing_row'; 
                    $x = $x+1;

                    echo "<li class=\"$rowCss\">
                            <div style=\"display: none;\" id=\"liID\" class=\"search_result_id\">$intListingID</div>
                            <a href=\"view.php?in=$intListingID\" class=\"sm_listing_title\">$strTitle</a>
                            $strWhenCode
                            $strContactCode
                            $strDescriptionCode
                            <div style=\"clear:both\"></div>
                            </li>
                    ";
                }			
            }  
            
            $rowCss = ($x%2 == 0)? 'listing_row_alt': 'listing_row'; 
            echo "<li class=\"$rowCss\">
                    <a href=\"listall.php?a=things&b=town&c=" . $inTown . "\" class=\"li_location\">More...</a>
                    <div style=\"clear:both\"></div>
                    </li>
            ";	

        } else {
            echo "<a href=\"update_listing_pre.php\"><br>Be the first to add a listing!</a>";
        }                                                                           
    ?>
    </ul>
</div>
<div class="small_column">
    <div class="section_heading"><a href="listall.php?a=directory&b=town&c=<?php echo $inTown; ?>">Directory</a></div>
    <ul>
    <?php
        $sql_query1 = sprintf("
            SELECT distinct(l.`id`), l.`title`, l.`description`
                from `cbwire`.`listings` l
                INNER JOIN `cbwire`.`contact` c on c.`listingsid` = l.`id`
                WHERE l.`deleted` = 0 and l.`directory` = 1 and c.`communityid`=%s
                ORDER BY l.`highlight` desc, l.`title`
                LIMIT 8;",
            mysql_real_escape_string($inTown));

        $result1 = mysql_query($sql_query1) or log_error($sql_query1, mysql_error(), $page, false);

        if (mysql_num_rows($result1) > 0) {
            $x = 0;
            while ($result_row = mysql_fetch_assoc($result1)) {
                $strTitle = "";
                $strDescription = "";
                $intListingID = 0;                
                
                if (!is_null($result_row['title'])) { $strTitle = $result_row['title']; }
                if (!is_null($result_row['description'])) { $strDescription = $result_row['description']; }
                if (!is_null($result_row['id'])) { $intListingID = $result_row['id']; }

                if (($intListingID > 0) && ($strTitle <> "")) {
                    $strDescriptionCode = "";
                    if (strlen($strDescription) > 75) { $strDescription = sprintf("%s...", substr($strDescription, 0, 70)); }
                    if ($strDescription <> "") { $strDescriptionCode = "<a href=\"view.php?in=$intListingID\" class=\"sm_listing_description\">$strDescription</a>"; }
                    
                    $strContactCode = "";                    
                    $sql_query2 = sprintf("SELECT contact.`phone`, com.`name` AS community_name, `other_community`,
                                        `location1`, `location2`, `location3`, contact.`linkid`, list.`title` as link_title
                                        from `cbwire`.`contact` contact
                                        LEFT OUTER JOIN `cbwire`.`community` com on com.`id` = contact.`communityid`
                                        LEFT OUTER JOIN `cbwire`.`listings` list on list.`id` = contact.`linkid`
                                        where contact.`listingsid`=%s and (list.`deleted`=0 or list.`deleted` is null)
                                        order by (length(`location1`) > 0), list.`title`, `location1`;",
                                        mysql_real_escape_string($intListingID));

                    $result2 = mysql_query($sql_query2) or log_error($sql_query2, mysql_error(), $page, false);
                    
                    while ($result_row2 = mysql_fetch_assoc($result2)) {
                        $strLinkTitle = "";
                        $strLocation1 = "";
                        $strLocation2 = "";
                        $strLocation3 = "";
                        $strPhone = "";
                        $strCommunity = "";
                        
                        // If the only location field is location1, put location1 and community on same line
                        if (!is_null($result_row2['link_title'])) { $strLinkTitle = "<a href=\"view.php?in=$intListingID\" class=\"sm_listing_linktitle\">" . $result_row2['link_title'] . "</a> "; }
                        if (!is_null($result_row2['location1'])) { if ($result_row2['location1'] <> "") { $strLocation1 = "<a href=\"view.php?in=$intListingID\" class=\"sm_listing_contact_line\">" . $result_row2['location1'] . "</a> "; } }
                        if (!is_null($result_row2['location2'])) { if ($result_row2['location2'] <> "") { $strLocation2 = "<a href=\"view.php?in=$intListingID\" class=\"sm_listing_contact_line\">" . $result_row2['location2'] . "</a> "; } }
                        if (!is_null($result_row2['location3'])) { if ($result_row2['location3'] <> "") { $strLocation3 = "<a href=\"view.php?in=$intListingID\" class=\"sm_listing_contact_line\">" . $result_row2['location3'] . "</a> "; } }
                        if (!is_null($result_row2['phone'])) { if ($result_row2['phone'] <> "") { $strPhone = "<a href=\"view.php?in=$intListingID\" class=\"sm_listing_phone\">" . $result_row2['phone'] . "</a> "; } }
                        if (!is_null($result_row2['community_name'])) { $strCommunity = "<a href=\"view.php?in=$intListingID\" class=\"sm_listing_contact_line nobr\">" . $result_row2['community_name'] . "</a> "; }
                        if ($strCommunity == "") {
                            if (!is_null($result_row2['other_community'])) { if ($result_row2['other_community'] <> "") { $strCommunity = "<a href=\"view.php?in=$intListingID\" class=\"sm_listing_contact_line nobr\">" . $result_row2['other_community'] . "</a> "; } }                           
                        }
                        
                        if (($strLocation2 == "") && ($strLocation3 == "")) {
                            
                        }
                        
                        $strContactCode = $strContactCode . $strLinkTitle . $strPhone . $strLocation1 . $strLocation2 . $strLocation3;
                    }
                    
                    $rowCss = ($x%2 == 0)? 'listing_row_alt': 'listing_row'; 
                    $x = $x+1;

                    echo "<li class=\"$rowCss\">
                            <div style=\"display: none;\" id=\"liID\" class=\"search_result_id\">$intListingID</div>
                            <a href=\"view.php?in=$intListingID\" class=\"sm_listing_title\">$strTitle</a>
                            $strContactCode
                            $strDescriptionCode
                            <div style=\"clear:both\"></div>
                            </li>
                    ";
                }			
            }  
            
            $rowCss = ($x%2 == 0)? 'listing_row_alt': 'listing_row'; 
            echo "<li class=\"$rowCss\">
                    <a href=\"listall.php?a=directory&b=town&c=" . $inTown . "\" class=\"li_location\">More...</a>
                    <div style=\"clear:both\"></div>
                    </li>
            ";	

        } else {
            echo "<a href=\"update_listing_pre.php\"><br>Be the first to add a listing!</a>";
        }                                                                           
    ?>
    </ul>
</div>
<div class="small_column">
    <div class="section_heading"><a href="listall.php?a=attractions&b=town&c=<?php echo $inTown; ?>">Attractions</a></div>
    <ul>
    <?php
        $sql_query1 = sprintf("
            SELECT distinct(l.`id`), l.`title`, l.`description`
                from `cbwire`.`listings` l
                INNER JOIN `cbwire`.`contact` c on c.`listingsid` = l.`id`
                WHERE l.`deleted` = 0 and l.`attractions` = 1 and c.`communityid`=%s
                ORDER BY l.`highlight` desc, l.`title`
                LIMIT 8;",
            mysql_real_escape_string($inTown));

        $result1 = mysql_query($sql_query1) or log_error($sql_query1, mysql_error(), $page, false);

        if (mysql_num_rows($result1) > 0) {
            $x = 0;
            while ($result_row = mysql_fetch_assoc($result1)) {
                $strTitle = "";
                $strDescription = "";
                $intListingID = 0;                
                
                if (!is_null($result_row['title'])) { $strTitle = $result_row['title']; }
                if (!is_null($result_row['description'])) { $strDescription = $result_row['description']; }
                if (!is_null($result_row['id'])) { $intListingID = $result_row['id']; }

                if (($intListingID > 0) && ($strTitle <> "")) {
                    $strDescriptionCode = "";
                    if (strlen($strDescription) > 75) { $strDescription = sprintf("%s...", substr($strDescription, 0, 70)); }
                    if ($strDescription <> "") { $strDescriptionCode = "<a href=\"view.php?in=$intListingID\" class=\"sm_listing_description\">$strDescription</a>"; }
                    
                    $strContactCode = "";                    
                    $sql_query2 = sprintf("SELECT contact.`phone`, com.`name` AS community_name, `other_community`,
                                        `location1`, `location2`, `location3`, contact.`linkid`, list.`title` as link_title
                                        from `cbwire`.`contact` contact
                                        LEFT OUTER JOIN `cbwire`.`community` com on com.`id` = contact.`communityid`
                                        LEFT OUTER JOIN `cbwire`.`listings` list on list.`id` = contact.`linkid`
                                        where contact.`listingsid`=%s and (list.`deleted`=0 or list.`deleted` is null)
                                        order by (length(`location1`) > 0), list.`title`, `location1`;",
                                        mysql_real_escape_string($intListingID));

                    $result2 = mysql_query($sql_query2) or log_error($sql_query2, mysql_error(), $page, false);
                    
                    while ($result_row2 = mysql_fetch_assoc($result2)) {
                        $strLinkTitle = "";
                        $strLocation1 = "";
                        $strLocation2 = "";
                        $strLocation3 = "";
                        $strPhone = "";
                        $strCommunity = "";
                        
                        // If the only location field is location1, put location1 and community on same line
                        if (!is_null($result_row2['link_title'])) { $strLinkTitle = "<a href=\"view.php?in=$intListingID\" class=\"sm_listing_linktitle\">" . $result_row2['link_title'] . "</a> "; }
                        if (!is_null($result_row2['location1'])) { if ($result_row2['location1'] <> "") { $strLocation1 = "<a href=\"view.php?in=$intListingID\" class=\"sm_listing_contact_line\">" . $result_row2['location1'] . "</a> "; } }
                        if (!is_null($result_row2['location2'])) { if ($result_row2['location2'] <> "") { $strLocation2 = "<a href=\"view.php?in=$intListingID\" class=\"sm_listing_contact_line\">" . $result_row2['location2'] . "</a> "; } }
                        if (!is_null($result_row2['location3'])) { if ($result_row2['location3'] <> "") { $strLocation3 = "<a href=\"view.php?in=$intListingID\" class=\"sm_listing_contact_line\">" . $result_row2['location3'] . "</a> "; } }
                        if (!is_null($result_row2['phone'])) { if ($result_row2['phone'] <> "") { $strPhone = "<a href=\"view.php?in=$intListingID\" class=\"sm_listing_phone\">" . $result_row2['phone'] . "</a> "; } }
                        if (!is_null($result_row2['community_name'])) { $strCommunity = "<a href=\"view.php?in=$intListingID\" class=\"sm_listing_contact_line nobr\">" . $result_row2['community_name'] . "</a> "; }
                        if ($strCommunity == "") {
                            if (!is_null($result_row2['other_community'])) { if ($result_row2['other_community'] <> "") { $strCommunity = "<a href=\"view.php?in=$intListingID\" class=\"sm_listing_contact_line nobr\">" . $result_row2['other_community'] . "</a> "; } }                           
                        }
                        
                        if (($strLocation2 == "") && ($strLocation3 == "")) {
                            
                        }
                        
                        $strContactCode = $strContactCode . $strLinkTitle . $strPhone . $strLocation1 . $strLocation2 . $strLocation3;
                    }
                    
                    $rowCss = ($x%2 == 0)? 'listing_row_alt': 'listing_row'; 
                    $x = $x+1;

                    echo "<li class=\"$rowCss\">
                            <div style=\"display: none;\" id=\"liID\" class=\"search_result_id\">$intListingID</div>
                            <a href=\"view.php?in=$intListingID\" class=\"sm_listing_title\">$strTitle</a>
                            $strContactCode
                            $strDescriptionCode
                            <div style=\"clear:both\"></div>
                            </li>
                    ";
                }			
            }  
            
            $rowCss = ($x%2 == 0)? 'listing_row_alt': 'listing_row'; 
            echo "<li class=\"$rowCss\">
                    <a href=\"listall.php?a=attractions&b=town&c=" . $inTown . "\" class=\"li_location\">More...</a>
                    <div style=\"clear:both\"></div>
                    </li>
            ";	

        } else {
            echo "<a href=\"update_listing_pre.php\"><br>Be the first to add a listing!</a>";
        }                                                                           
    ?>
    </ul>
</div>