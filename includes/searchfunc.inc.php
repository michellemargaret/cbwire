<?php 
    include_once "funcDateTime.inc.php";
    
     $strBasicFields = " l.`id`, l.`title`, l.`attractions`, l.`directory`, l.`thingstodo`, l.`classifieds`,
                                substr(l.`description`, 1, 120) as description, u.`name`, u.`id` as listing_owner, u.`admin` as listing_admin,
                                l.`cost` ";
     $strGroupFields = " (SELECT GROUP_CONCAT(distinct com.name SEPARATOR ', ') FROM contact contact 
                                        inner join community com on contact.communityid = com.id 
                                        WHERE contact.listingsid = l.id and com.name <> '') group1, 
                                (SELECT GROUP_CONCAT(distinct phone SEPARATOR ', ') FROM contact contact 
                                        WHERE contact.listingsid = l.id and phone <> '') group2,
                                (SELECT GROUP_CONCAT(distinct link.`title` SEPARATOR ', ') FROM listings link 
                                                inner join contact c2 on c2.`linkid` = link.`id` and link.`title` <> ''
                                                WHERE c2.`listingsid` = l.`id`) group3 ";
    
    
    function returnDateSearchHasResults($strInFromDate, $strInToDate) {  
        global $strBasicFields;
        global $strGroupFields;

        $strMatchIDs = "0";
        $intInFromDate = $strInFromDate;
        $intInToDate = $strInToDate;
        
    //    $intInFromDate = mktime(0, 0, 0, substr($strInFromDate, 5, 2), substr($strInFromDate, 8, 2), substr($strInFromDate, 0, 4));
	//if ($strInToDate == "") { $strInToDate = $strInFromDate; }
	//$intInToDate = mktime(23, 59, 59, substr($strInToDate, 5, 2), substr($strInToDate, 8, 2), substr($strInToDate, 0, 4));
	        
        $sql_query = sprintf("SELECT distinct l.`id`
                                FROM `listings` l
                                inner join `when` w on w.`listingid` = l.`id`
                                where 
                                (
                                    (w.`start_date` between '%s' and '%s')										  
                                    or
                                    (w.`end_date` between '%s' and '%s')
                                    or
                                    ('%s' between w.`start_date` and w.`end_date`)
                                    or
                                    ('%s' between w.`start_date` and w.`end_date`)
                                )
                                and l.`deleted`=0 
                                ",                                
                                mysql_real_escape_string($intInFromDate),
                                mysql_real_escape_string($intInToDate),
                                mysql_real_escape_string($intInFromDate),
                                mysql_real_escape_string($intInToDate),
                                mysql_real_escape_string($intInFromDate),
                                mysql_real_escape_string($intInToDate));

        $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);    
        
        if ($result_row = mysql_fetch_assoc($result)) {  
            return true;
        }
        
        return false;
    }    
    
    function returnDateSearch($page, $strInFromDate, $strInToDate) {  
        global $strBasicFields;
        global $strGroupFields;

        $strMatchIDs = "0";
        
        $sql_query = sprintf("SELECT distinct l.`id`
                                FROM `listings` l
                                inner join `when` w on w.`listingid` = l.`id`
                                where 
                                (
                                    (w.`start_date` between '%s' and '%s')										  
                                    or
                                    (w.`end_date` between '%s' and '%s')
                                    or
                                    ('%s' between w.`start_date` and w.`end_date`)
                                    or
                                    ('%s' between w.`start_date` and w.`end_date`)
                                )
                                and l.`deleted`=0 
                                ",                                
                                mysql_real_escape_string($strInFromDate),
                                mysql_real_escape_string($strInToDate),
                                mysql_real_escape_string($strInFromDate),
                                mysql_real_escape_string($strInToDate),
                                mysql_real_escape_string($strInFromDate),
                                mysql_real_escape_string($strInToDate));

        $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);    
        
        while ($result_row = mysql_fetch_assoc($result)) {  
            if (!is_null($result_row['id'])) { $strMatchIDs = $strMatchIDs . "," . $result_row['id']; }
        }

        $sql_query1 = sprintf("SELECT distinct %s, %s, earliest_date.`start_date`                        
                                FROM `listings` l
                                left outer join `contact` con on con.`listingsid` = l.`id`
                                left outer join `community` com on con.`communityid` = com.`id`
                                left outer join `users` u on u.`id` = l.`userid`
                                left outer join 
                                    (Select distinct w.`listingid`, min(w.`start_date`) as start_date 
                                        from `when` w 
                                        where w.`start_date` >= %s group by w.`listingid`
                                     ) earliest_date on earliest_date.`listingid` = l.`id`                            
                                    
                                where l.`id` in (%s) and l.`deleted`=0 
                                group by l.`id`, l.`title`
                                order by highlight desc, earliest_date.`start_date` is null, earliest_date.`start_date`, 
                                l.`highlight` desc, l.`title`
                                ",
                            $strBasicFields,
                            $strGroupFields,
                            mktime(0, 0, 0, date("m"), date("d"), date("y")),
                            mysql_real_escape_string($strMatchIDs));

        $result1 = mysql_query($sql_query1) or log_error($sql_query1, mysql_error(), $page, false);

        return $result1;         
    }
    
    
    
    function returnLastestSearch($page, $limit) {  
        global $strBasicFields;

        $sql_query1 = sprintf("SELECT distinct %s                      
                                FROM `listings` l
                                left outer join `users` u on u.`id` = l.`userid`                     
                                    
                                where l.`deleted`=0 
                                order by l.`inserted_date` desc
                                limit %s
                                ",
                            $strBasicFields,
                            mysql_real_escape_string($limit));

        $result1 = mysql_query($sql_query1) or log_error($sql_query1, mysql_error(), $page, false);

        return $result1;         
    }
    
    function showCalendarHoverListings($page, $result, $strInFromDate, $strInToDate) {
       $intCurrentPage = 0;
       $intNextPage = 0;
       $intPreviousPage = 0;
       $num_records = mysql_num_rows($result);
                       
        if ($num_records > 0) {
            $x = 0;
            echo "<div class=\"hoverListingsContainer\">";
            while ($result_row = mysql_fetch_assoc($result)) {  
                $rowCss = ($x%2 == 0)? 'calResultRow': 'calResultRowAlt'; 
                $x++;           

                $intID = 0;
                $strTitle = "";
                $strDescription = "";
                $strLink = "";
                
                if (!is_null($result_row['id'])) { $intID = $result_row['id']; }
                $strLink = "view.php?in=" . $intID;
                if (!is_null($result_row['description'])) { $strDescription = $result_row['description'];  if (strlen($strDescription) > 118) { $strDescription = $strDescription . " ..."; } }

                echo sprintf("<div class=\"%s\"><a href=\"%s\"><div class=\"search_result_id\" style=\"display:none;\">%s</div>%s</a>", $rowCss, $strLink, $intID, $result_row['title']);
                
                echo sprintf("<div style=\"clear:both\"></div>\n</div>\n");
            }
            echo "</div>";
             echo sprintf("<div style=\"clear:both\"></div>\n"); 

        } else {         
            echo "<div class=\"calResultRow\">No matches found.</div>" ;
        }
        
        unset($result);
    }
    
    function showListingsTitleOnly($page, $result) {
       $num_records = mysql_num_rows($result);
                       
        if ($num_records > 0) {
            $x = 0;
            echo "<div class=\"listTitlesOnlyContainer\">";
            while ($result_row = mysql_fetch_assoc($result)) {  
                $rowCss = ($x%2 == 0)? 'listing_row': 'listing_row_alt'; 
                $x++;           

                $intID = 0;
                $strTitle = "";
                $strLink = "";
                
                if (!is_null($result_row['id'])) { $intID = $result_row['id']; }
                $strLink = "view.php?in=" . $intID;

                echo sprintf("<div class=\"%s\"><a href=\"%s\"><div class=\"search_result_id\" style=\"display:none;\">%s</div>%s</a>", $rowCss, $strLink, $intID, $result_row['title']);
                
                echo sprintf("<div style=\"clear:both\"></div>\n</div>\n");
            }
            echo "</div>";
             echo sprintf("<div style=\"clear:both\"></div>\n"); 

        } else {         
            echo "<div class=\"calResultRow\">No matches found.</div>" ;
        }
        
        unset($result);
    }
    
?>