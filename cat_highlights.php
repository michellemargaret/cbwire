<?php   
    include_once "includes/func.php"; 
    include_once "includes/funcDateTime.inc.php";
    
    $page = "cat_highlights.php";
    global $inCat;
    $boolHasHighlights = false;
?>

    <?php
        $sql_query_things = sprintf("
            SELECT distinct(l.`id`), l.`title`, l.`description`
                from `cbwire`.`listings` l
            INNER JOIN `cbwire`.`listing_cat` lc on lc.`listingid` = l.`id`
            INNER JOIN `cbwire`.`categories` c on lc.categoryid = c.`id`        
                left outer join 
                    (Select distinct w.`listingid`, min(w.`start_date`) as start_date 
                        from `when` w 
                        where w.`start_date` >= %s group by w.`listingid`
                    ) earliest_date on earliest_date.`listingid` = l.`id`    
                WHERE l.`deleted` = 0 and l.`thingstodo` = 1 and (c.`id` = %s or c.`parentid`=%s) and earliest_date.start_date is not null
                order by earliest_date.`start_date` is null, earliest_date.`start_date`, l.`highlight` desc, l.`title`
                LIMIT 6;",
                mysql_real_escape_string(mktime(0, 0, 0, date("m"), date("d"), date("y"))),
            mysql_real_escape_string($inCat),
            mysql_real_escape_string($inCat));

        $result_things = mysql_query($sql_query_things) or log_error($sql_query_things, mysql_error(), $page, false);
        
        
        
        $sql_query_directory = sprintf("
            SELECT distinct(l.`id`), l.`title`, l.`description`
                from `cbwire`.`listings` l
            INNER JOIN `cbwire`.`listing_cat` lc on lc.`listingid` = l.`id`
            INNER JOIN `cbwire`.`categories` c on lc.categoryid = c.`id`    
                WHERE l.`deleted` = 0 and l.`directory` = 1 and (c.`id` = %s or c.`parentid`=%s)
                ORDER BY l.`highlight` desc, l.`title`
                LIMIT 8;",
            mysql_real_escape_string($inCat),
            mysql_real_escape_string($inCat));

        $result_directory = mysql_query($sql_query_directory) or log_error($sql_query_directory, mysql_error(), $page, false);
        
        
        
        $sql_query_attractions = sprintf("
            SELECT distinct(l.`id`), l.`title`, l.`description`
                from `cbwire`.`listings` l
            INNER JOIN `cbwire`.`listing_cat` lc on lc.`listingid` = l.`id`
            INNER JOIN `cbwire`.`categories` c on lc.categoryid = c.`id`    
                WHERE l.`deleted` = 0 and l.`attractions` = 1 and (c.`id` = %s or c.`parentid`=%s)
                ORDER BY l.`highlight` desc, l.`title`
                LIMIT 8;",
            mysql_real_escape_string($inCat),
            mysql_real_escape_string($inCat));

        $result_attractions = mysql_query($sql_query_attractions) or log_error($sql_query_attractions, mysql_error(), $page, false);
        
        $numSections = 0;
        $numThings = mysql_num_rows($result_things);
        $numDirectory = mysql_num_rows($result_directory);
        $numAttractions = mysql_num_rows($result_attractions);
        
        if ($numThings > 0) { $numSections = $numSections + 1; }
        if ($numDirectory > 0) { $numSections = $numSections + 1; }
        if ($numAttractions > 0) { $numSections = $numSections + 1; }
        
        if ($numSections == 0) {
            ?>
                <div class="full_column">
                    <div class="section_heading"><a href="update_listing_pre.php">No matches</a></div>
                    <ul>
                        <li class="listing_row_alt"><a href="update_listing_pre.php">Be the first to add a listing!</a></li>
                    </ul>                    
                </div>
            <?php
        } else if ($numSections > 1) {
            if (($numThings > 0) && ($numDirectory > 0)) {  echo "<div class=\"section_jump_heading\"><a href=\"#directory\">See Directory ($numDirectory listings)</a></div> "; }
            if ($numAttractions > 0) {  echo "<div class=\"section_jump_heading\"><a href=\"#attractions\">See Attractions ($numAttractions listings)</a></div> "; }
        }
        
        
        if ($numThings > 0) {
?>

            <div class="full_column">
                <div class="section_heading"><a name="things" href="listall.php?a=things&b=cat&c=<?php echo $inCat; ?>">Things To Do (<?php echo $numThings; ?> listings)</div>
                    <ul>

            <?php       
                $x = 0;
                while ($result_row = mysql_fetch_assoc($result_things)) {
                    $strTitle = "";
                    $strDescription = "";
                    $intListingID = 0;                

                    if (!is_null($result_row['title'])) { $strTitle = $result_row['title']; }
                    if (!is_null($result_row['description'])) { $strDescription = $result_row['description']; }
                    if (!is_null($result_row['id'])) { $intListingID = $result_row['id']; }

                    if (($intListingID > 0) && ($strTitle <> "")) {
                        $strDescriptionCode = "";
                        if (strlen($strDescription) > 205) { $strDescription = sprintf("%s...", substr($strDescription, 0, 200)); }
                        if ($strDescription <> "") { $strDescriptionCode = "<a href=\"view.php?in=$intListingID\" class=\"lg_listing_description\">$strDescription</a>"; }

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

                        $strWhenCode = implode(", ", $arrDates);
                        if ($strWhenCode <> "") { $strWhenCode = sprintf("<a href=\"view.php?in=$intListingID\" class=\"lg_listing_when\">%s</a>", $strWhenCode); }

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
                            if (!is_null($result_row2['link_title'])) { $strLinkTitle = "<a href=\"view.php?in=$intListingID\" class=\"lg_listing_linktitle\">" . $result_row2['link_title'] . "</a> "; }
                            if (!is_null($result_row2['location1'])) { if ($result_row2['location1'] <> "") { $strLocation1 = "<a href=\"view.php?in=$intListingID\" class=\"lg_listing_contact_line\">" . $result_row2['location1'] . "</a> "; } }
                            if (!is_null($result_row2['location2'])) { if ($result_row2['location2'] <> "") { $strLocation2 = "<a href=\"view.php?in=$intListingID\" class=\"lg_listing_contact_line\">" . $result_row2['location2'] . "</a> "; } }
                            if (!is_null($result_row2['location3'])) { if ($result_row2['location3'] <> "") { $strLocation3 = "<a href=\"view.php?in=$intListingID\" class=\"lg_listing_contact_line\">" . $result_row2['location3'] . "</a> "; } }
                            if (!is_null($result_row2['phone'])) { if ($result_row2['phone'] <> "") { $strPhone = "<a href=\"view.php?in=$intListingID\" class=\"lg_listing_phone\">" . $result_row2['phone'] . "</a> "; } }
                            if (!is_null($result_row2['community_name'])) { $strCommunity = "<a href=\"view.php?in=$intListingID\" class=\"lg_listing_community\">" . $result_row2['community_name'] . "</a> "; }
                            if ($strCommunity == "") {
                                if (!is_null($result_row2['other_community'])) { if ($result_row2['other_community'] <> "") { $strCommunity = "<a href=\"view.php?in=$intListingID\" class=\"lg_listing_community\">" . $result_row2['other_community'] . "</a> "; } }                           
                            }

                            if (($strLocation2 == "") && ($strLocation3 == "")) {

                            }

                            $strContactCode = $strContactCode . $strLinkTitle . $strPhone . $strLocation1 . $strLocation2 . $strLocation3 . $strCommunity;
                        }

                        $rowCss = ($x%2 == 0)? 'listing_row_alt': 'listing_row'; 
                        $x = $x+1;

                        echo "<li class=\"$rowCss\">
                                <div style=\"display: none;\" id=\"liID\" class=\"search_result_id\">$intListingID</div>
                                <a href=\"view.php?in=$intListingID\" class=\"lg_listing_title\">$strTitle</a>
                                <div class=\"lg_listing_contact_block\" id=\"contact_block\">$strContactCode</div>
                                $strWhenCode
                                $strDescriptionCode
                                <div style=\"clear:both\"></div>
                                </li>
                        ";
                    }			
                }  

                $rowCss = ($x%2 == 0)? 'listing_row_alt': 'listing_row'; 
                echo "<li class=\"$rowCss\">
                        <a href=\"listall.php?a=things&b=cat&c=" . $inCat . "\" class=\"li_location\">More...</a>
                        <div style=\"clear:both\"></div>
                        </li>
                ";	
                
                
                echo "</ul>
                    </div>";

            }   
            
            
        if ($numDirectory > 0) {
            ?>
                        
        <div class="full_column">
            <div class="section_heading"><a name="directory" href="listall.php?a=directory&b=cat&c=<?php echo $inCat; ?>">Directory (<?php echo $numDirectory; ?> listings)</a></div>
            <ul>

        <?php
        $x = 0;
            while ($result_row = mysql_fetch_assoc($result_directory)) {
                $strTitle = "";
                $strDescription = "";
                $intListingID = 0;                
                
                if (!is_null($result_row['title'])) { $strTitle = $result_row['title']; }
                if (!is_null($result_row['description'])) { $strDescription = $result_row['description']; }
                if (!is_null($result_row['id'])) { $intListingID = $result_row['id']; }

                if (($intListingID > 0) && ($strTitle <> "")) {
                    $strDescriptionCode = "";
                    if (strlen($strDescription) > 205) { $strDescription = sprintf("%s...", substr($strDescription, 0, 200)); }
                    if ($strDescription <> "") { $strDescriptionCode = "<a href=\"view.php?in=$intListingID\" class=\"lg_listing_description\">$strDescription</a>"; }
                    
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
                        if (!is_null($result_row2['link_title'])) { $strLinkTitle = "<a href=\"view.php?in=$intListingID\" class=\"lg_listing_linktitle\">" . $result_row2['link_title'] . "</a> "; }
                        if (!is_null($result_row2['location1'])) { if ($result_row2['location1'] <> "") { $strLocation1 = "<a href=\"view.php?in=$intListingID\" class=\"lg_listing_contact_line\">" . $result_row2['location1'] . "</a> "; } }
                        if (!is_null($result_row2['location2'])) { if ($result_row2['location2'] <> "") { $strLocation2 = "<a href=\"view.php?in=$intListingID\" class=\"lg_listing_contact_line\">" . $result_row2['location2'] . "</a> "; } }
                        if (!is_null($result_row2['location3'])) { if ($result_row2['location3'] <> "") { $strLocation3 = "<a href=\"view.php?in=$intListingID\" class=\"lg_listing_contact_line\">" . $result_row2['location3'] . "</a> "; } }
                        if (!is_null($result_row2['phone'])) { if ($result_row2['phone'] <> "") { $strPhone = "<a href=\"view.php?in=$intListingID\" class=\"lg_listing_phone\">" . $result_row2['phone'] . "</a> "; } }
                        if (!is_null($result_row2['community_name'])) { $strCommunity = "<a href=\"view.php?in=$intListingID\" class=\"lg_listing_community\">" . $result_row2['community_name'] . "</a> "; }
                        if ($strCommunity == "") {
                            if (!is_null($result_row2['other_community'])) { if ($result_row2['other_community'] <> "") { $strCommunity = "<a href=\"view.php?in=$intListingID\" class=\"lg_listing_community\">" . $result_row2['other_community'] . "</a> "; } }                           
                        }
                        
                        if (($strLocation2 == "") && ($strLocation3 == "")) {
                            
                        }
                        
                        $strContactCode = $strContactCode . $strLinkTitle . $strPhone . $strLocation1 . $strLocation2 . $strLocation3 . $strCommunity;
                    }
                    
                    $rowCss = ($x%2 == 0)? 'listing_row_alt': 'listing_row'; 
                    $x = $x+1;

                    echo "<li class=\"$rowCss\">
                            <div style=\"display: none;\" id=\"liID\" class=\"search_result_id\">$intListingID</div>
                            <a href=\"view.php?in=$intListingID\" class=\"lg_listing_title\">$strTitle</a>
                            <div class=\"lg_listing_contact_block\" id=\"contact_block\">$strContactCode</div>
                            $strDescriptionCode
                            <div style=\"clear:both\"></div>
                            </li>
                    ";
                }			
            }  
            
            $rowCss = ($x%2 == 0)? 'listing_row_alt': 'listing_row'; 
            echo "<li class=\"$rowCss\">
                    <a href=\"listall.php?a=directory&b=cat&c=" . $inCat . "\" class=\"li_location\">More...</a>
                    <div style=\"clear:both\"></div>
                    </li>
            ";	
                
                
                echo "</ul>
                    </div>";

        }   
        
        
        if ($numAttractions > 0) {
            ?>
        
<div class="full_column">
    <div class="section_heading"><a name="attractions" href="listall.php?a=attractions&b=cat&c=<?php echo $inCat; ?>">Attractions (<?php echo $numAttractions; ?> listings)</a></div>
    <ul>
        <?php
            $x = 0;
            while ($result_row = mysql_fetch_assoc($result_attractions)) {
                $strTitle = "";
                $strDescription = "";
                $intListingID = 0;                
                
                if (!is_null($result_row['title'])) { $strTitle = $result_row['title']; }
                if (!is_null($result_row['description'])) { $strDescription = $result_row['description']; }
                if (!is_null($result_row['id'])) { $intListingID = $result_row['id']; }

                if (($intListingID > 0) && ($strTitle <> "")) {
                    $strDescriptionCode = "";
                    if (strlen($strDescription) > 205) { $strDescription = sprintf("%s...", substr($strDescription, 0, 200)); }
                    if ($strDescription <> "") { $strDescriptionCode = "<a href=\"view.php?in=$intListingID\" class=\"lg_listing_description\">$strDescription</a>"; }
                    
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
                        if (!is_null($result_row2['link_title'])) { $strLinkTitle = "<a href=\"view.php?in=$intListingID\" class=\"lg_listing_linktitle\">" . $result_row2['link_title'] . "</a> "; }
                        if (!is_null($result_row2['location1'])) { if ($result_row2['location1'] <> "") { $strLocation1 = "<a href=\"view.php?in=$intListingID\" class=\"lg_listing_contact_line\">" . $result_row2['location1'] . "</a> "; } }
                        if (!is_null($result_row2['location2'])) { if ($result_row2['location2'] <> "") { $strLocation2 = "<a href=\"view.php?in=$intListingID\" class=\"lg_listing_contact_line\">" . $result_row2['location2'] . "</a> "; } }
                        if (!is_null($result_row2['location3'])) { if ($result_row2['location3'] <> "") { $strLocation3 = "<a href=\"view.php?in=$intListingID\" class=\"lg_listing_contact_line\">" . $result_row2['location3'] . "</a> "; } }
                        if (!is_null($result_row2['phone'])) { if ($result_row2['phone'] <> "") { $strPhone = "<a href=\"view.php?in=$intListingID\" class=\"lg_listing_phone\">" . $result_row2['phone'] . "</a> "; } }
                        if (!is_null($result_row2['community_name'])) { $strCommunity = "<a href=\"view.php?in=$intListingID\" class=\"lg_listing_community\">" . $result_row2['community_name'] . "</a> "; }
                        if ($strCommunity == "") {
                            if (!is_null($result_row2['other_community'])) { if ($result_row2['other_community'] <> "") { $strCommunity = "<a href=\"view.php?in=$intListingID\" class=\"lg_listing_community\">" . $result_row2['other_community'] . "</a> "; } }                           
                        }
                        
                        if (($strLocation2 == "") && ($strLocation3 == "")) {
                            
                        }
                        
                        $strContactCode = $strContactCode . $strLinkTitle . $strPhone . $strLocation1 . $strLocation2 . $strLocation3 . $strCommunity;
                    }
                    
                    $rowCss = ($x%2 == 0)? 'listing_row_alt': 'listing_row'; 
                    $x = $x+1;

                    echo "<li class=\"$rowCss\">
                            <div style=\"display: none;\" id=\"liID\" class=\"search_result_id\">$intListingID</div>
                            <a href=\"view.php?in=$intListingID\" class=\"lg_listing_title\">$strTitle</a>
                            <div class=\"lg_listing_contact_block\" id=\"contact_block\">$strContactCode</div>
                            $strDescriptionCode
                            <div style=\"clear:both\"></div>
                            </li>
                    ";
                }			
            }  
            
            $rowCss = ($x%2 == 0)? 'listing_row_alt': 'listing_row'; 
            echo "<li class=\"$rowCss\">
                    <a href=\"listall.php?a=attractions&b=cat&c=" . $inCat . "\" class=\"li_location\">More...</a>
                    <div style=\"clear:both\"></div>
                    </li>
            ";	
                
                
                echo "</ul>
                    </div>";

        }                                                                           
    ?>