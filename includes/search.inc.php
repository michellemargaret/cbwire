<?php 
    include_once "funcDateTime.inc.php";
    
     $strBasicFields = " l.`id`, l.`title`, l.`attractions`, l.`directory`, l.`thingstodo`, l.`classifieds`,
                                l.`description`, u.`name`, u.`id` as listing_owner, u.`admin` as listing_admin,
                                l.`cost` ";
     $strGroupFields = " (SELECT GROUP_CONCAT(distinct com.name SEPARATOR ', ') FROM contact contact 
                                        inner join community com on contact.communityid = com.id 
                                        WHERE contact.listingsid = l.id and com.name <> '') group1, 
                                (SELECT GROUP_CONCAT(distinct phone SEPARATOR ', ') FROM contact contact 
                                        WHERE contact.listingsid = l.id and phone <> '') group2,
                                (SELECT GROUP_CONCAT(distinct link.`title` SEPARATOR ', ') FROM listings link 
                                                inner join contact c2 on c2.`linkid` = link.`id` and link.`title` <> ''
                                                WHERE c2.`listingsid` = l.`id`) group3 ";
    
    function returnGeneralNonPublished($tableTitle, $owner, $from, $limit, $page, $filter) {
        returnSearchResults($tableTitle, "b", $owner, $from, $limit, $page, $filter, " ('edt', 'new') ", false);        
    }
    
    function returnGeneralPublished($tableTitle, $owner, $from, $limit, $page, $filter) {
        returnSearchResults($tableTitle, "a", $owner, $from, $limit, $page, $filter, "" , false);        
    }
    
    function returnAdminNonPublished($tableTitle, $userid, $from, $limit, $page, $filter) {
        returnSearchResults($tableTitle, "b", $userid, $from, $limit, $page, $filter, " ('edt', 'new') ", true);        
    }
    
    function returnAdminSubmitted($tableTitle, $from, $limit, $page, $filter) {
        returnSearchResults($tableTitle, "b", 0, $from, $limit, $page, $filter, " ('app') ", true);        
    }
    
    function returnAdminPublished($tableTitle, $from, $limit, $page, $filter) {
        returnSearchResults($tableTitle, "a", 0, $from, $limit, $page, $filter, "" , true);        
    }
    
    function returnQuickSearch($tableTitle, $userid, $from, $limit, $page, $filter) {
        global $strBasicFields;
        global $strGroupFields;
        
        $strMatchIDs = "0";
        
        $sql_query = sprintf("SELECT distinct l.`id`                
                                FROM `listings` l
                                inner join `search_index` s on s.`listingid` = l.`id`
                                where s.word like ('%s') and l.`deleted`=0 
                                ",
                                '%' . mysql_real_escape_string($filter) . '%');

        $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);
        
        $intCount = mysql_num_rows($result);
        
        
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
                                order by earliest_date.`start_date` is null, earliest_date.`start_date`, l.`title`
                                limit %s, %s
                                ",
                            $strBasicFields,
                            $strGroupFields,
                            mktime(0, 0, 0, date("m"), date("d"), date("y")),
                            mysql_real_escape_string($strMatchIDs), 
                            mysql_real_escape_string($from), 
                            mysql_real_escape_string($limit));

        $result1 = mysql_query($sql_query1) or log_error($sql_query1, mysql_error(), $page, false);



        searchDisplay($tableTitle, "a", $from, $limit, $page, 0, $result1, $intCount, 0, $filter, "");
    }
    
    function returnThings($tableTitle, $userid, $from, $limit, $page) {
        global $strBasicFields;
        global $strGroupFields;
        
        $strMatchIDs = "0";
        
        $sql_query = sprintf("SELECT distinct l.`id`                
                                FROM `listings` l
                                where l.`thingstodo`=1 and l.`deleted`=0 
                                ");

        $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);
        
        $intCount = mysql_num_rows($result);

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
                                    
                                where l.`thingstodo`=1 and l.`deleted`=0 
                                group by l.`id`, l.`title`
                                order by earliest_date.`start_date` is null, earliest_date.`start_date`, l.`title`
                                limit %s, %s
                                ",
                            $strBasicFields,
                            $strGroupFields,
                            mktime(0, 0, 0, date("m"), date("d"), date("y")),
                            mysql_real_escape_string($from), 
                            mysql_real_escape_string($limit));

        $result1 = mysql_query($sql_query1) or log_error($sql_query1, mysql_error(), $page, false);



        searchDisplay($tableTitle, "a", $from, $limit, $page, 0, $result1, $intCount, 0, "", "");
    }
    
    function returnDirectory($tableTitle, $userid, $from, $limit, $page) {
        global $strBasicFields;
        global $strGroupFields;
        
        $sql_query = sprintf("SELECT distinct l.`id`                
                                FROM `listings` l
                                where l.`directory`=1 and l.`deleted`=0 
                                ");

        $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);
        
        $intCount = mysql_num_rows($result);

        $sql_query1 = sprintf("SELECT distinct %s, %s                       
                                FROM `listings` l
                                left outer join `contact` con on con.`listingsid` = l.`id`
                                left outer join `community` com on con.`communityid` = com.`id`
                                left outer join `users` u on u.`id` = l.`userid`                        
                                    
                                where l.`directory`=1 and l.`deleted`=0 
                                group by l.`id`, l.`title`
                                order by l.`title`
                                limit %s, %s
                                ",
                            $strBasicFields,
                            $strGroupFields,
                            mysql_real_escape_string($from), 
                            mysql_real_escape_string($limit));

        $result1 = mysql_query($sql_query1) or log_error($sql_query1, mysql_error(), $page, false);



        searchDisplay($tableTitle, "a", $from, $limit, $page, 0, $result1, $intCount, 0, "", "");
    }
    
    function returnAttractions($tableTitle, $userid, $from, $limit, $page) {
        global $strBasicFields;
        global $strGroupFields;
        
        $sql_query = sprintf("SELECT distinct l.`id`                
                                FROM `listings` l
                                where l.`attractions`=1 and l.`deleted`=0 
                                ");

        $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);
        
        $intCount = mysql_num_rows($result);

        $sql_query1 = sprintf("SELECT distinct %s, %s                       
                                FROM `listings` l
                                left outer join `contact` con on con.`listingsid` = l.`id`
                                left outer join `community` com on con.`communityid` = com.`id`
                                left outer join `users` u on u.`id` = l.`userid`                        
                                    
                                where l.`attractions`=1 and l.`deleted`=0 
                                group by l.`id`, l.`title`
                                order by l.`title`
                                limit %s, %s
                                ",
                            $strBasicFields,
                            $strGroupFields,
                            mysql_real_escape_string($from), 
                            mysql_real_escape_string($limit));

        $result1 = mysql_query($sql_query1) or log_error($sql_query1, mysql_error(), $page, false);



        searchDisplay($tableTitle, "a", $from, $limit, $page, 0, $result1, $intCount, 0, "", "");
    }
    
    function returnDateSearch($tableTitle, $userid, $from, $limit, $page, $strInFromDate, $strInToDate) {  
        global $strBasicFields;
        global $strGroupFields;
        
        $strMatchIDs = "0";
        
        $intInFromDate = mktime(0, 0, 0, substr($strInFromDate, 5, 2), substr($strInFromDate, 8, 2), substr($strInFromDate, 0, 4));
	if ($strInToDate == "") { $strInToDate = $strInFromDate; }
	$intInToDate = mktime(23, 59, 59, substr($strInToDate, 5, 2), substr($strInToDate, 8, 2), substr($strInToDate, 0, 4));
	
        
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
        
        $intCount = mysql_num_rows($result);
        
        
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
                                order by earliest_date.`start_date` is null, earliest_date.`start_date`, l.`title`
                                limit %s, %s
                                ",
                            $strBasicFields,
                            $strGroupFields,
                            mktime(0, 0, 0, date("m"), date("d"), date("y")),
                            mysql_real_escape_string($strMatchIDs), 
                            mysql_real_escape_string($from), 
                            mysql_real_escape_string($limit));

        $result1 = mysql_query($sql_query1) or log_error($sql_query1, mysql_error(), $page, false);



        searchDisplay($tableTitle, "a", $from, $limit, $page, 0, $result1, $intCount, 0, $strInFromDate, $strInToDate);
         
    }
    
    function returnCategorySearch($tableTitle, $userid, $from, $limit, $page, $strSearchCat) {  
        global $strBasicFields;
        global $strGroupFields;
        
        $strMatchIDs = "0";
        
        $sql_query = sprintf("SELECT distinct l.`id`                                
                                FROM `listings` l
                                inner join `listing_cat` lc on lc.`listingid` = l.`id`
                                inner join `categories` c on c.`id` = lc.`categoryid`
                                where c.`id` in (%s) and c.`id` > 0 and l.`deleted`=0 
                                ",
                                mysql_real_escape_string($strSearchCat));

        $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);
        
        $intCount = mysql_num_rows($result);
        
        
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
                                order by earliest_date.`start_date` is null, earliest_date.`start_date`, l.`title`
                                limit %s, %s
                                ",
                            $strBasicFields,
                            $strGroupFields,
                            mktime(0, 0, 0, date("m"), date("d"), date("y")),
                            mysql_real_escape_string($strMatchIDs), 
                            mysql_real_escape_string($from), 
                            mysql_real_escape_string($limit));

        $result1 = mysql_query($sql_query1) or log_error($sql_query1, mysql_error(), $page, false);



        searchDisplay($tableTitle, "a", $from, $limit, $page, 0, $result1, $intCount, 0, $strSearchCat, "");
         
    }
    
    function returnTownSearch($tableTitle, $userid, $from, $limit, $page, $strSearchTown) {  
        global $strBasicFields;
        global $strGroupFields;
        
        $strMatchIDs = "0";
        
        $sql_query = sprintf("SELECT distinct l.`id`                                
                                FROM `listings` l
                                inner join `contact` c on c.`listingsid` = l.`id`
                                where c.`communityid` in (%s) and c.`communityid` > 0 and l.`deleted`=0 
                                ",
                                mysql_real_escape_string($strSearchTown));

        $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);
        
        $intCount = mysql_num_rows($result);
        
        
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
                                order by earliest_date.`start_date` is null, earliest_date.`start_date`, l.`title`
                                limit %s, %s
                                ",
                            $strBasicFields,
                            $strGroupFields,
                            mktime(0, 0, 0, date("m"), date("d"), date("y")),
                            mysql_real_escape_string($strMatchIDs), 
                            mysql_real_escape_string($from), 
                            mysql_real_escape_string($limit));

        $result1 = mysql_query($sql_query1) or log_error($sql_query1, mysql_error(), $page, false);



        searchDisplay($tableTitle, "a", $from, $limit, $page, 0, $result1, $intCount, 0, $strSearchTown, "");
         
    }
      
    function returnAgeSearch($tableTitle, $userid, $from, $limit, $page, $strSearchAge) {  
        global $strBasicFields;
        global $strGroupFields;
        
        $strMatchIDs = "0";
        
        $sql_query = sprintf("SELECT distinct l.`id`                                
                                FROM `listings` l
                                inner join `listing_age` la on la.`listingid` = l.`id`
                                where la.`agesid` in (%s) and la.`agesid` > 0 and l.`deleted`=0 
                                ",
                                mysql_real_escape_string($strSearchAge));

        $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);
        
        $intCount = mysql_num_rows($result);
        
        
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
                                order by earliest_date.`start_date` is null, earliest_date.`start_date`, l.`title`
                                limit %s, %s
                                ",
                            $strBasicFields,
                            $strGroupFields,
                            mktime(0, 0, 0, date("m"), date("d"), date("y")),
                            mysql_real_escape_string($strMatchIDs), 
                            mysql_real_escape_string($from), 
                            mysql_real_escape_string($limit));

        $result1 = mysql_query($sql_query1) or log_error($sql_query1, mysql_error(), $page, false);



        searchDisplay($tableTitle, "a", $from, $limit, $page, 0, $result1, $intCount, 0, $strSearchAge, "");
         
    }
  
    function returnClassifieds($tableTitle, $userid, $from, $limit, $page) {        
        global $strBasicFields;
        global $strGroupFields;
        
        $strMatchIDs = "0";
        
        $strCurrentDate = date("Y") . "-" . date("m") . "-" . date("d") . " 00:00:00";
        $sql_query = sprintf("SELECT distinct l.`id`                
                                FROM `listings` l
                                where l.`classifieds`=1 and l.`deleted`=0 and l.`expiry_date` > '%s'
                                ", $strCurrentDate);

        $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);
        
        $intCount = mysql_num_rows($result);
        
        
        while ($result_row = mysql_fetch_assoc($result)) {  
            if (!is_null($result_row['id'])) { $strMatchIDs = $strMatchIDs . "," . $result_row['id']; }
        }

        $sql_query1 = sprintf("SELECT distinct %s, %s                        
                                FROM `listings` l
                                left outer join `contact` con on con.`listingsid` = l.`id`
                                left outer join `community` com on con.`communityid` = com.`id`
                                left outer join `users` u on u.`id` = l.`userid`                       
                                    
                                where l.`classifieds`=1 and l.`deleted`=0 and l.`expiry_date` > '%s'
                                group by l.`id`, l.`title`
                                order by l.`updated_date` desc, l.`title`
                                limit %s, %s
                                ",
                            $strBasicFields,
                            $strGroupFields,
                            $strCurrentDate,
                            mysql_real_escape_string($from), 
                            mysql_real_escape_string($limit));

        $result1 = mysql_query($sql_query1) or log_error($sql_query1, mysql_error(), $page, false);



        searchDisplay($tableTitle, "a", $from, $limit, $page, 0, $result1, $intCount, 0, "", "");
    }
    
    // Title: Title to be displayed as header for results
    // Table: a or b (Default a)
    // Owner: select results for this user; if 0, return results for all users
    // From and Limit: used for paging and restricting number of results returned; if limit is 0, neither is used
    // Status: either an empty string or a string like " ('edt', 'new') " listing statuses to match
    // isAdmin: true or false for current user
    function returnSearchResults($title, $intable, $owner, $from, $limit, $page, $filter, $status, $isAdmin) {
        $strLimit;
        $_b;
        $orderBy = "";
        $whereFilter = " 1=1 ";
        
        
        if ($intable == "b") {
            $_b = "_b";
            $orderBy = " order by l.`modified_date` desc ";
        } else {
            $_b = "";
            $orderBy = " order by l.`updated_date` desc ";
        }
        
        if ($limit == 0) {
            $strLimit = "";
        } else {
            $strLimit = sprintf(" limit %s, %s", $from, $limit);
        }
        
        if ($filter <> "") {
            $whereFilter = $whereFilter . " and (l.`title` like '%" .  mysql_real_escape_string($filter) . "%' or l.`description` like '%" .  mysql_real_escape_string($filter) . "%') ";
        } 
        
        if (($intable == "b") && ($isAdmin == true) && (strpos($status, "app") !== false)) {
            // no user filter
        } else if (($intable == "b") && ($isAdmin == true)) {
            // Admin's can edit any that were opened by admin's, or that were set to edit by self
            $whereFilter = $whereFilter . " and (l.`lastmodifiedby`=" . mysql_real_escape_string($owner) . " or u.`admin`=1) ";
        } else if (($owner > 0) && ($intable == "b")) {
            // Non admin opening nonpublished can open own that were not reopened by someone else
            $whereFilter = $whereFilter . " and (l.`userid` = " . mysql_real_escape_string($owner) . ") and (l.`lastmodifiedby`=l.`userid`) ";;
        } else if ($owner > 0) {
            // Show own
            $whereFilter = $whereFilter . " and (l.`userid` = " . mysql_real_escape_string($owner) . ") ";
        }
        
        if ($status <> "") {
            $whereFilter = $whereFilter . " and  (l.`status` in " . $status . ") ";
        } 
        
        $sql_query1 = sprintf("SELECT distinct l.`id`, l.`title`, l.`attractions`, l.`directory`, l.`thingstodo`, l.`classifieds`,
                                substr(l.`description`, 1, 120) as description, u.`name`, u.`id` as listing_owner, u.`admin` as listing_admin,
                                (SELECT GROUP_CONCAT(distinct com.name SEPARATOR ', ') FROM contact%s contact 
                                        inner join community com on contact.communityid = com.id 
                                        WHERE contact.listings%sid = l.id and com.name <> '') group1, 
                                (SELECT GROUP_CONCAT(distinct phone SEPARATOR ', ') FROM contact%s contact 
                                        WHERE contact.listings%sid = l.id and phone <> '') group2,
                                (SELECT GROUP_CONCAT(distinct link.`title` SEPARATOR ', ') FROM listings link 
                                                inner join contact%s c2 on c2.`linkid` = link.`id` and link.`title` <> ''
                                                WHERE c2.`listings%sid` = l.`id`) group3,
                                l.`cost`
                                FROM `listings%s` l
                                left outer join `contact%s` con on con.`listings%sid` = l.`id`
                                left outer join `community` com on con.`communityid` = com.`id`
                                left outer join `users` u on u.`id` = l.`userid`
                                where %s and l.`deleted`=0 
                                group by l.`id`, l.`title`
                               %s %s",
                            mysql_real_escape_string($_b),
                            mysql_real_escape_string($_b),
                            mysql_real_escape_string($_b),
                            mysql_real_escape_string($_b),
                            mysql_real_escape_string($_b),
                            mysql_real_escape_string($_b),
                            mysql_real_escape_string($_b),
                            mysql_real_escape_string($_b),
                            mysql_real_escape_string($_b),
                            $whereFilter,
                            mysql_real_escape_string($orderBy),
                            mysql_real_escape_string($strLimit));
//echo "<br><br>" . $sql_query1 . "<br><br>";
        $result1 = mysql_query($sql_query1) or log_error($sql_query1, mysql_error(), $page, true);
             
        
        $sql_query_count = sprintf("SELECT count(*)
                                FROM `listings%s` l
                                left outer join `users` u on u.`id` = l.`userid`
                                where %s and l.`deleted`=0",
                            mysql_real_escape_string($_b),
                            $whereFilter);
        $result_count = mysql_query($sql_query_count) or log_error($sql_query_count, mysql_error(), $page, false);
        
        searchDisplay($title, $intable, $from, $limit, $page, $owner, $result1, mysql_result($result_count, 0), $isAdmin, "", "");
        
        unset($result);
    }
    
    function searchDisplay($title, $intable, $from, $limit, $page, $userid, $result, $num_records, $isAdmin, $strSearch1, $strSearch2) {
       $strRecordsReturned = "";
       $intCurrentPage = 0;
       $intNextPage = 0;
       $intPreviousPage = 0;
       
        if (is_numeric($num_records)) {
            if ($num_records == 1) {
                $strRecordsReturned = "(" . $num_records . " result)";
            } else if ($num_records > 1) {
                $strRecordsReturned = "(" . $num_records . " results)";
            }
        }
        
        if (($from == 0) && ($limit > 0)) {
            $intCurrentPage = 1;
        } elseif (($from > 0) && ($limit > 0)) {
            $intCurrentPage = ($from/$limit)+1;
        } // otherwise keep current page as 0 and don't display paging
        
        if ($intCurrentPage > 0) { // paging will be included
            $intPreviousPage = $intCurrentPage - 1;
            
            if (($intCurrentPage*$limit) < $num_records) {
                $intNextPage = $intCurrentPage + 1;
            } // Otherwise this is the last page
        }
        
        $_b;
        if ($intable == "b") {
            $_b = "_b";
        } else {
            $_b = "";
        }        
        
        echo "<div class=\"search_table\">
                    <div class=\"search_title\">$title
                    <div class=\"search_numresults\">$strRecordsReturned</div></div>
            ";

        if (mysql_num_rows($result) > 0) {
            $x = 0;
            while ($result_row = mysql_fetch_assoc($result)) {  
                $rowCss = ($x%2 == 0)? 'search_row': 'search_row_alt'; 
                $x++;           

                $intID = 0;
                $strTitle = "";
                $strDescription = "";
                $strSection = "D";
                $strLink = "";
                $mineCSS = "";
                
                if (!is_null($result_row['id'])) { $intID = $result_row['id']; }
                if ($intable == "b") { $strLink = "edit.php?in=" . $intID; } else { $strLink = "view.php?in=" . $intID; }    
                if ($isAdmin) { if ($result_row['listing_owner'] == $userid) { $mineCSS = " myListing"; } elseif ($result_row['listing_admin'] == 1) { $mineCSS = " myListing"; } }
                $imgThumbnail = sprintf("<a href=\"%s\" class=\"search_section_thumb\" title=\"Directory\">D</a>", $strLink);
                if (!is_null($result_row['classifieds'])) { if ($result_row['classifieds'] == 1) { $strSection = "C"; $imgThumbnail = sprintf("<a href=\"%s\" class=\"search_section_thumb\" title=\"Classified\">C</a>", $strLink); }  }
                if (!is_null($result_row['thingstodo'])) { if ($result_row['thingstodo'] == 1) { $strSection = "T"; $imgThumbnail = sprintf("<a href=\"%s\" class=\"search_section_thumb\" title=\"Thing To Do\">T</a>", $strLink); }  }
                if (!is_null($result_row['attractions'])) { if ($result_row['attractions'] == 1) { $strSection = "A"; $imgThumbnail = sprintf("<a href=\"%s\" class=\"search_section_thumb\" title=\"Attraction\">A</a>", $strLink); }  }
                if (!is_null($result_row['description'])) { $strDescription = $result_row['description'];  if (strlen($strDescription) > 118) { $strDescription = $strDescription . " ..."; } }

                echo sprintf("<div class=\"%s\">\n%s\n<a href=\"%s\" class=\"search_result_title\">\n<div class=\"search_b\" style=\"display:none;\">%s</div>\n<div class=\"search_result_id\" style=\"display:none;\">%s</div>\n%s<br><span class=\"search_result_description%s\">%s</span></a>\n", $rowCss, $imgThumbnail, $strLink, $intable, $intID, $result_row['title'], $mineCSS, $strDescription);
                
                $dates = "";
                if ($strSection == "T") {
                    $sql_query2 = sprintf("SELECT w.`id`, w.`start_date`, w.`end_date`, w.`start_time`, w.`end_time`, w.`recursive`, w.`expiry`
                                from `cbwire`.`when%s` w 
                                where w.`listing%sid` = %s and																
                                ((w.`start_date` >= '%s') or (w.`end_date` >= '%s'))
                                ORDER BY w.`start_date`, w.`start_time`, w.`end_time`
                                LIMIT 3;",
                                mysql_real_escape_string($_b),
                                mysql_real_escape_string($_b),
                                mysql_real_escape_string($intID),
                                mysql_real_escape_string(mktime(0, 0, 0, date("m"), date("d"), date("y"))),
                                mysql_real_escape_string(mktime(0, 0, 0, date("m"), date("d"), date("y"))));
                    
                    $result2 = mysql_query($sql_query2) or log_error($sql_query2, mysql_error(), $page, true);
                    
                    $dateArr = format_datetime($result2);
                    
                    foreach ($dateArr as $dateStr) {
                        if ($dates <> "") {
                            $dates = $dates . "<div class=\"small_spacer\">&nbsp;</div>";
                        }
                        $dates = $dates . $dateStr;
                    } 
                }
                
                echo sprintf("<a href=\"%s\" class=\"search_result_date\">%s&nbsp;</a>\n", $strLink, $dates);
                     
                $location = "";
                if (!is_null($result_row['group1'])) { $location = $location . $result_row['group1']; }
                if (!is_null($result_row['group2'])) { 
                    if (($result_row['group2']) <> "") { 
                        if ($location <> "") { $location = $location . "<br>"; } 
                        $location = $location . $result_row['group2']; 
                       
                     }                    
                }
                
                echo sprintf("<a href=\"%s\" class=\"search_result_location\">%s</a>\n", $strLink, $location);
                
                echo sprintf("<div style=\"clear:both\"></div>\n</div>\n");
            }
            
            if (($intCurrentPage > 0) && (($intPreviousPage > 0) || ($intNextPage > 0))) {
                echo "<div class=\"paging\">";
                echo sprintf("<div id=\"intCurrentPage\" style=\"display:none;\">%s</div>", $intCurrentPage);
                echo sprintf("<div id=\"intFromPage\" style=\"display:none;\">%s</div>", $from);
                echo sprintf("<div id=\"intLimitPage\" style=\"display:none;\">%s</div>", $limit);
                echo sprintf("<div id=\"strTitlePage\" style=\"display:none;\">%s</div>", $title);
                echo sprintf("<div id=\"strSearch1\" style=\"display:none;\">%s</div>", $strSearch1);
                echo sprintf("<div id=\"strSearch2\" style=\"display:none;\">%s</div>", $strSearch2);
                
                
                if ($intPreviousPage > 0) {
                    echo " <a href=\"#\" class=\"previous_page\">PREVIOUS</a> ";
                }
                if ($intNextPage > 0) {
                    echo " <a href=\"#\" class=\"next_page\">NEXT</a> ";
                }
                 echo sprintf("<div style=\"clear:both\"></div>\n");
                 
                // goto_page
                $intFirstPage = max(1, ($intCurrentPage-5));
                $intLastPage = ceil($num_records/$limit); // Maximum last page                
                
                if ($intCurrentPage <= 7) {
                    // Last page can either be max last page (if less than 10), or 10
                    $intLastPage = min($intLastPage, 10);   
                } else {
                    // Last page can either be max last page or current page + 4
                    $intLastPage = min($intLastPage, ($intCurrentPage+4));    
                }
                
                while ($intFirstPage <= $intLastPage) {
                    if ($intFirstPage == $intCurrentPage) {
                        echo $intFirstPage;
                    } else {
                        echo " <a href=\"#\" class=\"goto_page\">$intFirstPage</a> "; 
                    }
                    $intFirstPage = $intFirstPage+1;
                }
                echo "</div>";
            } else {
                echo "<div class=\"paging\"></div>" ;
            }
        } else {         
            echo "<div class=\"search_row\">No matches found.</div><div class=\"paging\"></div>" ;
        }

        echo "     
                </div>";
        
        unset($result2);
        unset($result);
        
    }
            
    // Date searches: strSearchSection = "date", strSearchType = from date, strSEarchValue = to date, filter = ""
    function returnListResults($title, $userid, $from, $limit, $page, $strSearchSection, $strSearchType, $strSearchValue, $strFilter) {  
       // echo "title: " . $title . "userid: " . $userid . ", from: " . $from . ", limit: " . $limit . ", page: " . $page . ", strsearchsection: : " . $strSearchSection . ", strsearchtype: " . $strSearchType . ", strsearchvalue: " . $strSearchValue . ", strfilter: " . $strFilter;
        if (($strFilter == "") && ($strSearchSection == "date")) {
            returnDatedResults($title, $userid, $from, $limit, $page, $strSearchSection, $strSearchType, $strSearchValue, $strFilter);
        } elseif ($strFilter == "") {
            returnNonRankedResults($title, $userid, $from, $limit, $page, $strSearchSection, $strSearchType, $strSearchValue, $strFilter);
        } else {
           returnRankedSearchResults($title, $userid, $from, $limit, $page, $strSearchSection, $strSearchType, $strSearchValue, $strFilter);
        }
    }
    
    function returnRankedSearchResults($title, $userid, $from, $limit, $page, $strSearchSection, $strSearchType, $strSearchValue, $inFilter) {      
        global $strBasicFields;
        global $strGroupFields;
        $sql_query1 = "";
        $sql_query_count = 0;
        
        // Used in ranking
        $arrCleanedSearch = array();
        $arrSearchIDs = array();
        $strSearchIDs = "";
        
        $strFilter = $inFilter;
        $strFilter = strtoupper($strFilter);
        
        if ($strSearchSection == "things") { $strSearchSection = "thingstodo"; }

//echo "Ranked<br>strSearchType: " . $strSearchType . "<br>strSearchSection: " . $strSearchSection . "<br>";
        switch($strSearchType) {
            case "town":
                if ($strSearchSection == "thingstodo") {                    
                
                    $sql_query1 = sprintf("SELECT distinct l.`id`                 
                                    FROM `listings` l
                                    inner join `contact` con on con.`listingsid` = l.`id`
                                    inner join `community` com on con.`communityid` = com.`id`
                                    left outer join `users` u on u.`id` = l.`userid`
                                    left outer join 
                                        (Select distinct w.`listingid`, min(w.`start_date`) as start_date 
                                            from `when` w 
                                            where w.`start_date` >= %s group by w.`listingid`
                                        ) earliest_date on earliest_date.`listingid` = l.`id`    

                                    where l.`deleted`=0 and l.`thingstodo`=1 and com.`id`=%s and earliest_date.`start_date` is not null
                                    group by l.`id`, l.`title`
                                    ",
                                mktime(0, 0, 0, date("m"), date("d"), date("y")),
                                mysql_real_escape_string($strSearchValue));
                } else if ($strSearchSection == "search") {
                    $sql_query1 = sprintf("SELECT distinct  l.`id`            
                                FROM `listings` l
                                inner join `contact` con on con.`listingsid` = l.`id`
                                inner join `community` com on con.`communityid` = com.`id`
                                left outer join `users` u on u.`id` = l.`userid`
                                left outer join 
                                    (Select distinct w.`listingid`, min(w.`start_date`) as start_date 
                                        from `when` w 
                                        where w.`start_date` >= %s group by w.`listingid`
                                    ) earliest_date on earliest_date.`listingid` = l.`id`   
                                where l.`deleted`=0 and com.`id`=%s
                                and (l.`classifieds`=0 or l.`expiry_date` is null 
                                    or l.`expiry_date` = '0000-00-00 00:00:00' or l.`expiry_date` >= '%s')
                                and (l.`thingstodo`=0 or earliest_date.`start_date` is not null)
                                group by l.`id`, l.`title`
                                ",
                            mktime(0, 0, 0, date("m"), date("d"), date("y")),
                            mysql_real_escape_string($strSearchValue),                             
                            date("Y-m-d 00:00:00"));    
               
                } else {
                    $sql_query1 = sprintf("SELECT distinct l.`id`
                                FROM `listings` l
                                inner join `contact` con on con.`listingsid` = l.`id`
                                inner join `community` com on con.`communityid` = com.`id`
                                left outer join `users` u on u.`id` = l.`userid`
                                left outer join 
                                    (Select distinct w.`listingid`, min(w.`start_date`) as start_date 
                                        from `when` w 
                                        where w.`start_date` >= %s group by w.`listingid`
                                    ) earliest_date on earliest_date.`listingid` = l.`id`   
                                where l.`deleted`=0 and l.`%s`=1 and com.`id`=%s
                                and (l.`classifieds`=0 or l.`expiry_date` is null 
                                    or l.`expiry_date` = '0000-00-00 00:00:00' or l.`expiry_date` >= '%s')
                                and (l.`thingstodo`=0 or earliest_date.`start_date` is not null)
                                group by l.`id`, l.`title`
                                ",
                            mktime(0, 0, 0, date("m"), date("d"), date("y")),
                            mysql_real_escape_string($strSearchSection),
                            mysql_real_escape_string($strSearchValue),                             
                            date("Y-m-d 00:00:00"));    
                }
                break;
                
            case "cat":
                if ($strSearchSection == "thingstodo") {                    
                
                    $sql_query1 = sprintf("SELECT distinct  l.`id`                 
                                    FROM `listings` l
                                    inner join `listing_cat` lc on lc.`listingid` = l.`id`
                                    inner join `categories` c on c.`id` = lc.`categoryid`
                                    left outer join `users` u on u.`id` = l.`userid`
                                    left outer join 
                                        (Select distinct w.`listingid`, min(w.`start_date`) as start_date 
                                            from `when` w 
                                            where w.`start_date` >= %s group by w.`listingid`
                                        ) earliest_date on earliest_date.`listingid` = l.`id`    

                                    where l.`deleted`=0 and l.`thingstodo`=1 and (c.`id`=%s or c.`parentid`=%s) and earliest_date.`start_date` is not null
                                    group by l.`id`, l.`title`
                                    ",
                                mktime(0, 0, 0, date("m"), date("d"), date("y")),
                                mysql_real_escape_string($strSearchValue), 
                                mysql_real_escape_string($strSearchValue));
                } else if ($strSearchSection == "search") {
                    $sql_query1 = sprintf("SELECT distinct  l.`id`                 
                                FROM `listings` l
                                    inner join `listing_cat` lc on lc.`listingid` = l.`id`
                                    inner join `categories` c on c.`id` = lc.`categoryid`
                                left outer join `users` u on u.`id` = l.`userid`
                                left outer join 
                                    (Select distinct w.`listingid`, min(w.`start_date`) as start_date 
                                        from `when` w 
                                        where w.`start_date` >= %s group by w.`listingid`
                                    ) earliest_date on earliest_date.`listingid` = l.`id`
                                where l.`deleted`=0 and (c.`id`=%s or c.`parentid`=%s) 
                                and (l.`classifieds`=0 or l.`expiry_date` is null 
                                    or l.`expiry_date` = '0000-00-00 00:00:00' or l.`expiry_date` >= '%s')
                                and (l.`thingstodo`=0 or earliest_date.`start_date` is not null)
                                group by l.`id`, l.`title`
                                ",
                                mktime(0, 0, 0, date("m"), date("d"), date("y")),
                                mysql_real_escape_string($strSearchValue), 
                                mysql_real_escape_string($strSearchValue),                           
                                date("Y-m-d 00:00:00"));  
                } else {
                    $sql_query1 = sprintf("SELECT distinct  l.`id`          
                                FROM `listings` l
                                    inner join `listing_cat` lc on lc.`listingid` = l.`id`
                                    inner join `categories` c on c.`id` = lc.`categoryid`
                                left outer join `users` u on u.`id` = l.`userid`
                                left outer join 
                                    (Select distinct w.`listingid`, min(w.`start_date`) as start_date 
                                        from `when` w 
                                        where w.`start_date` >= %s group by w.`listingid`
                                    ) earliest_date on earliest_date.`listingid` = l.`id`
                                where l.`deleted`=0 and l.`%s`=1 and (c.`id`=%s or c.`parentid`=%s) 
                                and (l.`classifieds`=0 or l.`expiry_date` is null 
                                    or l.`expiry_date` = '0000-00-00 00:00:00' or l.`expiry_date` >= '%s')
                                and (l.`thingstodo`=0 or earliest_date.`start_date` is not null)
                                group by l.`id`, l.`title`
                                ",
                                mktime(0, 0, 0, date("m"), date("d"), date("y")),
                            mysql_real_escape_string($strSearchSection),
                                mysql_real_escape_string($strSearchValue), 
                                mysql_real_escape_string($strSearchValue),                           
                            date("Y-m-d 00:00:00"));   
                }
                break;
            default:                    
                if ($strSearchSection == "thingstodo") {
                    $sql_query1 = sprintf("SELECT distinct  l.`id`             
                                    FROM `listings` l
                                    left outer join `users` u on u.`id` = l.`userid`
                                    left outer join 
                                        (Select distinct w.`listingid`, min(w.`start_date`) as start_date 
                                            from `when` w 
                                            where w.`start_date` >= %s group by w.`listingid`
                                        ) earliest_date on earliest_date.`listingid` = l.`id`    

                                    where l.`deleted`=0 and l.`thingstodo`=1 and earliest_date.`start_date` is not null
                                    group by l.`id`, l.`title`
                                    ",
                                mktime(0, 0, 0, date("m"), date("d"), date("y")));
                } else if ($strSearchSection == "search") {
                    $sql_query1 = sprintf("SELECT distinct  l.`id`           
                                FROM `listings` l
                                left outer join `users` u on u.`id` = l.`userid`
                                left outer join 
                                    (Select distinct w.`listingid`, min(w.`start_date`) as start_date 
                                        from `when` w 
                                        where w.`start_date` >= %s group by w.`listingid`
                                    ) earliest_date on earliest_date.`listingid` = l.`id`    
                                where l.`deleted`=0 
                                and (l.`classifieds`=0 or l.`expiry_date` is null 
                                    or l.`expiry_date` = '0000-00-00 00:00:00' or l.`expiry_date` >= '%s')
                                and (l.`thingstodo` = 0 or earliest_date.`start_date` is not null)
                                group by l.`id`, l.`title`
                                ",      
                            mktime(0, 0, 0, date("m"), date("d"), date("y")),
                            date("Y-m-d 00:00:00"));             
                } else {
                    $sql_query1 = sprintf("SELECT distinct  l.`id`          
                                FROM `listings` l
                                left outer join `users` u on u.`id` = l.`userid`
                                left outer join 
                                    (Select distinct w.`listingid`, min(w.`start_date`) as start_date 
                                        from `when` w 
                                        where w.`start_date` >= %s group by w.`listingid`
                                    ) earliest_date on earliest_date.`listingid` = l.`id`
                                where l.`deleted`=0 and l.`%s`=1 
                                and (l.`classifieds`=0 or l.`expiry_date` is null 
                                    or l.`expiry_date` = '0000-00-00 00:00:00' or l.`expiry_date` >= '%s')
                                and (l.`thingstodo` = 0 or earliest_date.`start_date` is not null)
                                group by l.`id`, l.`title`
                                ",   
                            mktime(0, 0, 0, date("m"), date("d"), date("y")),
                            mysql_real_escape_string($strSearchSection),                           
                            date("Y-m-d 00:00:00"));  
                }
                break;
                
        }

        $result1 = mysql_query($sql_query1) or log_error($sql_query1, mysql_error(), $page, false);  
        
        if (mysql_num_rows($result1) > 0) {
            $temp = array();
            while ($result_row = mysql_fetch_assoc($result1)) { 
                if (!is_null($result_row['id'])) { $temp[] = $result_row['id']; }
            }
            $strSearchIDs = implode(",", $temp);
        } else {
            echo "There were no results to match your search.  Change your search and try again.";
            return;
        }
        
        // Find exact matches - very high ranking
        $sql_query = sprintf("SELECT distinct  l.`id`          
                                FROM `listings` l
                                where l.`id` in (%s) and UPPER(l.`title`)='%s'
                                ",
                            mysql_real_escape_string($strSearchIDs), 
                            mysql_real_escape_string($strFilter));   

        $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);  
        
        while ($result_row = mysql_fetch_assoc($result)) { 
           if (!is_null($result_row['id'])) { 
               $intListingID = $result_row['id'];
               if (array_key_exists($intListingID, $arrSearchIDs)) {
                    $arrSearchIDs[$intListingID] = $arrSearchIDs[$intListingID] + 10000; 
                } else {
                    $arrSearchIDs[$intListingID] = 10000; 
                }
           }
        }
        
        // Find exact matches with description - very high ranking
        $sql_query = sprintf("SELECT distinct  l.`id`          
                                FROM `listings` l
                                where l.`id` in (%s) and UPPER(l.`description`)='%s'
                                ",
                            mysql_real_escape_string($strSearchIDs), 
                            mysql_real_escape_string($strFilter));   
        
        $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);  
        
        while ($result_row = mysql_fetch_assoc($result1)) { 
           if (!is_null($result_row['id'])) {
               $intListingID = $result_row['id'];
               if (array_key_exists($intListingID, $arrSearchIDs)) {
                    $arrSearchIDs[$intListingID] = $arrSearchIDs[$intListingID] + 5000; 
                } else {
                    $arrSearchIDs[$intListingID] = 5000; 
                }
           }
        }
        
        // If it's not a letter or number, replace it with a space
        $strFilter = preg_replace('/[^A-Za-z0-9_]/', " ", $strFilter);
        $arrCleanedSearch = explode(" ", $strFilter);
	
	$intSearchableItems = 0;	
	for ($i=0; $i<count($arrCleanedSearch); $i++) {
            if ((strlen($arrCleanedSearch[$i]) < 3) || ($arrCleanedSearch[$i] == "THE") || ($arrCleanedSearch[$i] == "AND")) {
                // Don't search for THE, AND, or words shorter than three letters
                $arrCleanedSearch[$i] = "";
            } elseif ((substr($arrCleanedSearch[$i], -1) == "S") && (strlen($arrCleanedSearch[$i]) > 3)){
                // Remove s as last letter of words; this means someone searching for "paths" will get results for "path".
                // It will take some legitimate s off the end of words, but those words will still be matched based on the rest of the word
                $arrCleanedSearch[$i] = substr($arrCleanedSearch[$i], 0, strlen($arrCleanedSearch[$i])-1);
            }

            // Remove unnecessary word endings ED and ING		
            if ((strlen($arrCleanedSearch[$i]) > 4) && (substr($arrCleanedSearch[$i], -2) == "ED")) {
                    $arrCleanedSearch[$i] = substr($arrCleanedSearch[$i], 0, strlen($arrCleanedSearch[$i])-2);
            }

            if ((strlen($arrCleanedSearch[$i]) > 5) && (substr($arrCleanedSearch[$i], -3) == "ING")) {
                    $arrCleanedSearch[$i] = substr($arrCleanedSearch[$i], 0, strlen($arrCleanedSearch[$i])-3);
            }

            if ($arrCleanedSearch[$i] <> "") {
                    $intSearchableItems++;
            }
	}
	
	if ($intSearchableItems == 0) {
            // If cleaning above doesn't leave anything to search for,
            // go back to original without special characters
            $arrCleanedSearch = explode(" ", strtoupper($strFilter));
	}
        
        
	foreach ($arrCleanedSearch as $strSearchWord) {	
            $strSearchWord = trim($strSearchWord);
            
            if ($strSearchWord <> "") {		
                /*$sql_search = sprintf("
                    Select distinct `id`
                        From `cbwire`.`categories` 				
                        where upper(`title`) like '%s' " . $strCSectionCondition . "
                        order by `title`;",
                        "%" . mysql_real_escape_string($strSearchWord) . "%");	

                $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);  

                while ($result_row = mysql_fetch_assoc($result)) {
                        $intCategoryID = 0;
                        if (!is_null($result_row['id'])) { $intCategoryID = $result_row['id']; }
                        $arrCategoryIDs[] = $intCategoryID; 
                }*/	


                $sql_query = sprintf("
                    Select distinct `listingid`, sum(`score`) as search_score
                        From search_index 					
                        where `word` like '%s' and `listingid` in (%s)
                        group by `listingid`
                        order by search_score desc;",
                        "%" . mysql_real_escape_string($strSearchWord) . "%", $strSearchIDs);
                $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);  

                while ($result_row = mysql_fetch_assoc($result)) {
                    $intListingID = 0;
                    $intSearchScore = 0;
                    if (!is_null($result_row['listingid'])) { $intListingID = $result_row['listingid']; }
                    if (!is_null($result_row['search_score'])) { $intSearchScore = $result_row['search_score']; }
                    if (array_key_exists($intListingID, $arrSearchIDs)) {
                            $arrSearchIDs[$intListingID] = $arrSearchIDs[$intListingID] + $intSearchScore; 
                    } else {
                            $arrSearchIDs[$intListingID] = $intSearchScore; 
                    }
                }	
            }
	}
        
        if (count($arrSearchIDs) == 0) {
            echo "<ul><li class=\"li_row\">There were no results to match your search.</li></ul>";
            return;            
        } else {
            arsort($arrSearchIDs, SORT_NUMERIC);
            $strSearchResultIDs = implode(", ", array_keys($arrSearchIDs));
            $strSearchResultIDs2 = "'" . implode("', '", array_keys($arrSearchIDs)) . "'";

            $sql_query = sprintf("
                Select distinct %s, %s
                From listings l            
                left outer join `users` u on u.`id` = l.`userid`
                where l.`deleted`=0 and l.`id` in (%s)
                and (l.`classifieds`=0 or l.`expiry_date` is null 
                    or l.`expiry_date` = '0000-00-00 00:00:00' or l.`expiry_date` >= '%s')
                group by l.`id`
                order by field(l.`id`, %s)
                limit %s, %s
                ",
            $strBasicFields,
            $strGroupFields,
            $strSearchResultIDs,          
            date("Y-m-d 00:00:00"),
            $strSearchResultIDs2,
            mysql_real_escape_string($from), 
            mysql_real_escape_string($limit));  

            $sql_query_count = sprintf("
                Select count(distinct l.`id`)
                From listings l    
                where l.`deleted`=0 and l.`id` in (%s)
                and (l.`classifieds`=0 or l.`expiry_date` is null 
                    or l.`expiry_date` = '0000-00-00 00:00:00' or l.`expiry_date` >= '%s')                         
                ",
            $strSearchResultIDs,          
            date("Y-m-d 00:00:00"));

            $result= mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);
            $result_count = mysql_query($sql_query_count) or log_error($sql_query_count, mysql_error(), $page, false);

            searchListDisplay($title, $from, $limit, $page, $userid, $result, mysql_result($result_count, 0), $strSearchSection, $strSearchType, $strSearchValue, $strFilter);
        }
        unset($result);
    }
    
    function returnNonRankedResults($title, $userid, $from, $limit, $page, $strSearchSection, $strSearchType, $strSearchValue, $strFilter) {        
        global $strBasicFields;
        global $strGroupFields;

        $strLimit = "";
        $sql_query1 = "";
        $sql_query_count = 0;
        
        $whereFilter = "";
        
        if ($limit <> 0) {
            $strLimit = sprintf(" limit %s, %s", $from, $limit);
        }
        
        if ($strSearchSection == "things") { $strSearchSection = "thingstodo"; }
//echo "NON Ranked<br>strSearchType: " . $strSearchType . "<br>strSearchSection: " . $strSearchSection . "<br>";
        switch($strSearchType) {
            case "town":
                if ($strSearchSection == "thingstodo") {                    
                
                    $sql_query1 = sprintf("SELECT distinct l.`id`         
                                    FROM `listings` l
                                    inner join `contact` con on con.`listingsid` = l.`id`
                                    inner join `community` com on con.`communityid` = com.`id`
                                    left outer join `users` u on u.`id` = l.`userid`
                                    left outer join 
                                        (Select distinct w.`listingid`, min(w.`start_date`) as start_date 
                                            from `when` w 
                                            where w.`start_date` >= %s group by w.`listingid`
                                        ) earliest_date on earliest_date.`listingid` = l.`id`    

                                    where l.`deleted`=0 and l.`thingstodo`=1 and earliest_date.`start_date` is not null and com.`id`=%s %s
                                    group by l.`id`, l.`title`
                                    order by earliest_date.`start_date` is null, earliest_date.`start_date`, l.`highlight` desc, l.`title`
                                    
                                    ",
                                mktime(0, 0, 0, date("m"), date("d"), date("y")),
                                mysql_real_escape_string($strSearchValue), 
                                $whereFilter);
                } else if ($strSearchSection == "search") {
                    $sql_query1 = sprintf("SELECT distinct  l.`id`               
                                FROM `listings` l
                                inner join `contact` con on con.`listingsid` = l.`id`
                                inner join `community` com on con.`communityid` = com.`id`
                                left outer join `users` u on u.`id` = l.`userid`
                                where l.`deleted`=0 and com.`id`=%s
                                and (l.`classifieds`=0 or l.`expiry_date` is null 
                                    or l.`expiry_date` = '0000-00-00 00:00:00' or l.`expiry_date` >= '%s')
                                %s
                                group by l.`id`, l.`title`
                                order by l.`highlight` desc, l.`title`
                                
                                ",
                            mysql_real_escape_string($strSearchValue),                             
                            date("Y-m-d 00:00:00"),
                            $whereFilter);   
                         
                } else {
                    $sql_query1 = sprintf("SELECT distinct  l.`id`           
                                FROM `listings` l
                                inner join `contact` con on con.`listingsid` = l.`id`
                                inner join `community` com on con.`communityid` = com.`id`
                                left outer join `users` u on u.`id` = l.`userid`
                                where l.`deleted`=0 and l.`%s`=1 and com.`id`=%s
                                and (l.`classifieds`=0 or l.`expiry_date` is null 
                                    or l.`expiry_date` = '0000-00-00 00:00:00' or l.`expiry_date` >= '%s')
                                %s
                                group by l.`id`, l.`title`
                                order by l.`highlight` desc, l.`title`
                                ",
                            mysql_real_escape_string($strSearchSection),
                            mysql_real_escape_string($strSearchValue),                             
                            date("Y-m-d 00:00:00"),
                            $whereFilter);    
                }
                break;
                
            case "cat":
                if ($strSearchSection == "thingstodo") {   
                    $sql_query1 = sprintf("SELECT distinct  l.`id`              
                                    FROM `listings` l
                                    inner join `listing_cat` lc on lc.`listingid` = l.`id`
                                    inner join `categories` c on c.`id` = lc.`categoryid`
                                    left outer join `users` u on u.`id` = l.`userid`
                                    left outer join 
                                        (Select distinct w.`listingid`, min(w.`start_date`) as start_date 
                                            from `when` w 
                                            where w.`start_date` >= %s group by w.`listingid`
                                        ) earliest_date on earliest_date.`listingid` = l.`id`    

                                    where l.`deleted`=0 and l.`thingstodo`=1 and (c.`id`=%s or c.`parentid`=%s)  and earliest_date.`start_date` is not null 
                                    %s
                                    group by l.`id`, l.`title`
                                    order by earliest_date.`start_date` is null, earliest_date.`start_date`, l.`highlight` desc, l.`title`
                                    
                                    ",
                                mktime(0, 0, 0, date("m"), date("d"), date("y")),
                                mysql_real_escape_string($strSearchValue), 
                                mysql_real_escape_string($strSearchValue), 
                                $whereFilter);

                } else if ($strSearchSection == "search") {
                    $sql_query1 = sprintf("SELECT distinct  l.`id`               
                                FROM `listings` l
                                    inner join `listing_cat` lc on lc.`listingid` = l.`id`
                                    inner join `categories` c on c.`id` = lc.`categoryid`
                                left outer join `users` u on u.`id` = l.`userid`
                                where l.`deleted`=0 and (c.`id`=%s or c.`parentid`=%s) 
                                and (l.`classifieds`=0 or l.`expiry_date` is null 
                                    or l.`expiry_date` = '0000-00-00 00:00:00' or l.`expiry_date` >= '%s')
                                %s
                                group by l.`id`, l.`title`
                                order by l.`highlight` desc, l.`title`
                               
                                ",
                                mysql_real_escape_string($strSearchValue), 
                                mysql_real_escape_string($strSearchValue),                           
                            date("Y-m-d 00:00:00"),
                            $whereFilter);    

                } else {
                    $sql_query1 = sprintf("SELECT distinct  l.`id`             
                                FROM `listings` l
                                    inner join `listing_cat` lc on lc.`listingid` = l.`id`
                                    inner join `categories` c on c.`id` = lc.`categoryid`
                                left outer join `users` u on u.`id` = l.`userid`
                                where l.`deleted`=0 and l.`%s`=1 and (c.`id`=%s or c.`parentid`=%s) 
                                and (l.`classifieds`=0 or l.`expiry_date` is null 
                                    or l.`expiry_date` = '0000-00-00 00:00:00' or l.`expiry_date` >= '%s')
                                %s
                                group by l.`id`, l.`title`
                                order by l.`highlight` desc, l.`title`

                                ",
                            mysql_real_escape_string($strSearchSection),
                                mysql_real_escape_string($strSearchValue), 
                                mysql_real_escape_string($strSearchValue),                           
                            date("Y-m-d 00:00:00"),
                            $whereFilter);    

                }
                break;
            default:                    
                if ($strSearchSection == "thingstodo") {
                    $sql_query1 = sprintf("SELECT distinct  l.`id`                 
                                    FROM `listings` l
                                    left outer join `users` u on u.`id` = l.`userid`
                                    left outer join 
                                        (Select distinct w.`listingid`, min(w.`start_date`) as start_date 
                                            from `when` w 
                                            where w.`start_date` >= %s group by w.`listingid`
                                        ) earliest_date on earliest_date.`listingid` = l.`id`    

                                    where l.`deleted`=0 and l.`thingstodo`=1 and earliest_date.`start_date` is not null %s
                                    group by l.`id`, l.`title`
                                    order by earliest_date.`start_date` is null, earliest_date.`start_date`, l.`highlight` desc, l.`title`
  
                                    ",
                                mktime(0, 0, 0, date("m"), date("d"), date("y")),
                                $whereFilter);

                } else if ($strSearchSection == "search") {
                    $sql_query1 = sprintf("SELECT distinct  l.`id`                
                                FROM `listings` l
                                left outer join `users` u on u.`id` = l.`userid`
                                where l.`deleted`=0 
                                and (l.`classifieds`=0 or l.`expiry_date` is null 
                                    or l.`expiry_date` = '0000-00-00 00:00:00' or l.`expiry_date` >= '%s')
                                %s
                                group by l.`id`, l.`title`
                                order by l.`highlight` desc, l.`title`
                                
                                ",                    
                            date("Y-m-d 00:00:00"),
                            $whereFilter);    
                       
                } else {
                    $sql_query1 = sprintf("SELECT distinct  l.`id`               
                                FROM `listings` l
                                left outer join `users` u on u.`id` = l.`userid`
                                where l.`deleted`=0 and l.`%s`=1 
                                and (l.`classifieds`=0 or l.`expiry_date` is null 
                                    or l.`expiry_date` = '0000-00-00 00:00:00' or l.`expiry_date` >= '%s')
                                %s
                                group by l.`id`, l.`title`
                                order by l.`highlight` desc, l.`title`
   
                                ",
                            mysql_real_escape_string($strSearchSection),                           
                            date("Y-m-d 00:00:00"),
                            $whereFilter);    
        
                }
                break;
                
        }

        $result1 = mysql_query($sql_query1) or log_error($sql_query1, mysql_error(), $page, false);  
             
        $strSearchIDs = "";
        if (mysql_num_rows($result1) > 0) {
            $temp = array();
            while ($result_row = mysql_fetch_assoc($result1)) { 
                if (!is_null($result_row['id'])) { $temp[] = $result_row['id']; }
            }
            $strSearchIDs = implode(",", $temp);
        } else {
            echo "<ul><li class=\"li_row\">There were no results to match your search.</li></ul>";
            return;
        }
        
        if ($strSearchIDs <> "") {
            $sql_query = sprintf("
                Select distinct %s, %s
                From listings l            
                left outer join `users` u on u.`id` = l.`userid`
                where l.`deleted`=0 and l.`id` in (%s)
                and (l.`classifieds`=0 or l.`expiry_date` is null 
                    or l.`expiry_date` = '0000-00-00 00:00:00' or l.`expiry_date` >= '%s')
                group by l.`id`
                order by field(l.`id`, %s)
                limit %s, %s
                ",
            $strBasicFields,
            $strGroupFields,
            $strSearchIDs,          
            date("Y-m-d 00:00:00"),
            $strSearchIDs,
            mysql_real_escape_string($from), 
            mysql_real_escape_string($limit));  

            $sql_query_count = sprintf("
                Select count(distinct l.`id`)
                From listings l    
                where l.`deleted`=0 and l.`id` in (%s)
                and (l.`classifieds`=0 or l.`expiry_date` is null 
                    or l.`expiry_date` = '0000-00-00 00:00:00' or l.`expiry_date` >= '%s')                         
                ",
            $strSearchIDs,          
            date("Y-m-d 00:00:00"));

            $result= mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);
            $result_count = mysql_query($sql_query_count) or log_error($sql_query_count, mysql_error(), $page, false);


            searchListDisplay($title, $from, $limit, $page, $userid, $result, mysql_result($result_count, 0), $strSearchSection, $strSearchType, $strSearchValue, $strFilter);
        }
    }
    
   function returnDatedResults($title, $userid, $from, $limit, $page, $strSearchSection, $strSearchType, $strSearchValue, $strFilter) {  
        // strSearchSection = "date" and strFilter = "" to get here
        global $strBasicFields;
        global $strGroupFields;

        $strFromDate = $strSearchType;
        $strToDate = $strSearchValue;
        $strLimit = "";
        $sql_query1 = "";
        $sql_query_count = 0;
        
        if ($limit <> 0) {
            $strLimit = sprintf(" limit %s, %s", $from, $limit);
        }
                        
        $sql_query1 = sprintf("SELECT distinct l.`id`         
                        FROM `listings` l
                        left outer join `users` u on u.`id` = l.`userid`
                        inner join `when` w on w.`listingid` = l.`id`
                        where l.`deleted`=0 and l.`thingstodo`=1 and
                              (                              
                                (w.`start_date` between '%s' and '%s')										  
                                or
                                (w.`end_date` between '%s' and '%s')
                                or
                                ('%s' between w.`start_date` and w.`end_date`)
                                or
                                ('%s' between w.`start_date` and w.`end_date`)
                              )
                        group by l.`id`
                        order by w.`start_date`, w.`start_time`, l.`highlight` desc, l.`title`

                        ",
                    $strFromDate, $strToDate,
                    $strFromDate, $strToDate,
                    $strFromDate, $strToDate);                

        $result1 = mysql_query($sql_query1) or log_error($sql_query1, mysql_error(), $page, false);  
             
        $strSearchIDs = "";
        if (mysql_num_rows($result1) > 0) {
            $temp = array();
            while ($result_row = mysql_fetch_assoc($result1)) { 
                if (!is_null($result_row['id'])) { $temp[] = $result_row['id']; }
            }
            $strSearchIDs = implode(",", $temp);
        } else {
            echo "<ul><li class=\"li_row\">There were no results to match your search.</li></ul>";
            return;
        }
        
        if ($strSearchIDs <> "") {
            $sql_query = sprintf("
                Select distinct %s, %s
                From listings l            
                left outer join `users` u on u.`id` = l.`userid`
                where l.`deleted`=0 and l.`id` in (%s)
                group by l.`id`
                order by field(l.`id`, %s)
                limit %s, %s
                ",
            $strBasicFields,
            $strGroupFields,
            $strSearchIDs,   
            $strSearchIDs,
            mysql_real_escape_string($from), 
            mysql_real_escape_string($limit));  

            $sql_query_count = sprintf("
                Select count(distinct l.`id`)
                From listings l    
                where l.`deleted`=0 and l.`id` in (%s)                        
                ",
            $strSearchIDs,          
            date("Y-m-d 00:00:00"));

            $result= mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);
            $result_count = mysql_query($sql_query_count) or log_error($sql_query_count, mysql_error(), $page, false);


            searchListDisplay($title, $from, $limit, $page, $userid, $result, mysql_result($result_count, 0), $strSearchSection, $strSearchType, $strSearchValue, "");
        }
    }
    
    function searchListDisplay($title, $from, $limit, $page, $userid, $result, $num_records, $strSearchSection, $strSearchType, $strSearchValue, $strFilter) {
       $strRecordsReturned = "";
       $intCurrentPage = 0;
       $intNextPage = 0;
       $intPreviousPage = 0;
       
        if (is_numeric($num_records)) {
            if ($num_records == 1) {
                $strRecordsReturned = "(" . $num_records . " result)";
            } else if ($num_records > 1) {
                $strRecordsReturned = "(" . $num_records . " results)";
            }
        }
        
        if (($from == 0) && ($limit > 0)) {
            $intCurrentPage = 1;
        } elseif (($from > 0) && ($limit > 0)) {
            $intCurrentPage = ($from/$limit)+1;
        } // otherwise keep current page as 0 and don't display paging
        
        if ($intCurrentPage > 0) { // paging will be included
            $intPreviousPage = $intCurrentPage - 1;
            
            if (($intCurrentPage*$limit) < $num_records) {
                $intNextPage = $intCurrentPage + 1;
            } // Otherwise this is the last page
        }
        
        echo "<div class=\"full_column\">
                <div class=\"section_heading\">$title $strRecordsReturned</div>
              <ul>
            ";

        if (mysql_num_rows($result) > 0) {
            $x = 0;
            while ($result_row = mysql_fetch_assoc($result)) { 
                
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
            
            if (($intCurrentPage > 0) && (($intPreviousPage > 0) || ($intNextPage > 0))) {
                echo "<div class=\"listpaging\">";
                echo sprintf("<div id=\"intCurrentPage\" style=\"display:none;\">%s</div>", $intCurrentPage);
                echo sprintf("<div id=\"intFromPage\" style=\"display:none;\">%s</div>", $from);
                echo sprintf("<div id=\"intLimitPage\" style=\"display:none;\">%s</div>", $limit);
                echo sprintf("<div id=\"strTitlePage\" style=\"display:none;\">%s</div>", $title);
                echo sprintf("<div id=\"strSearchSection\" style=\"display:none;\">%s</div>", $strSearchSection);
                echo sprintf("<div id=\"strSearchType\" style=\"display:none;\">%s</div>", $strSearchType);
                echo sprintf("<div id=\"strSearchValue\" style=\"display:none;\">%s</div>", $strSearchValue);
                echo sprintf("<div id=\"strSearch1\" style=\"display:none;\">%s</div>", $strFilter);
                
                
                if ($intPreviousPage > 0) {
                    echo " <a href=\"#\" class=\"previous_page\">PREVIOUS</a> ";
                }
                if ($intNextPage > 0) {
                    echo " <a href=\"#\" class=\"next_page\">NEXT</a> ";
                }
                 echo sprintf("<div style=\"clear:both\"></div>\n");
                 
                // goto_page
                $intFirstPage = max(1, ($intCurrentPage-5));
                $intLastPage = ceil($num_records/$limit); // Maximum last page                
                
                if ($intCurrentPage <= 7) {
                    // Last page can either be max last page (if less than 10), or 10
                    $intLastPage = min($intLastPage, 10);   
                } else {
                    // Last page can either be max last page or current page + 4
                    $intLastPage = min($intLastPage, ($intCurrentPage+4));    
                }
                
                while ($intFirstPage <= $intLastPage) {
                    if ($intFirstPage == $intCurrentPage) {
                        echo $intFirstPage;
                    } else {
                        echo " <a href=\"#\" class=\"goto_page\">$intFirstPage</a> "; 
                    }
                    $intFirstPage = $intFirstPage+1;
                }
                echo "</div>";
            } else {
                echo "<div class=\"listpaging\"></div>" ;
            }
        } else {         
            echo "<div class=\"li_row\">No matches found.  Press <b>Back</b> and try a different search.</div><div class=\"listpaging\"></div>" ;
        }

        echo "      </ul>
                        </div>";
        
        unset($result2);
        unset($result);
        
    }
?>