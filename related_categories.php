<?php   
    include_once "includes/func.php"; 
    
    $page = "related_categories.php";
    global $inCat;
    $intParentID = -1;
    $arrSizes = array("1.0", "1.4", "0.9", "1.2", "1.6", "1.2", "1.4", "1", "1.6","1.0", "1.4", "0.9", "1.2", "1.6", "1.2", "1.4", "1", "1.6","1.0", "1.4", "0.9", "1.2", "1.6", "1.2", "1.4", "1", "1.6","1.0", "1.4", "0.9", "1.2", "1.6", "1.2", "1.4", "1", "1.6","1.0", "1.4", "0.9", "1.2", "1.6", "1.2", "1.4", "1", "1.6","1.0", "1.4", "0.9", "1.2", "1.6", "1.2", "1.4", "1", "1.6");
    
?>

        <?php
            $sql_query = sprintf("SELECT `parentid` from `cbwire`.`categories` WHERE `id`=%s;", mysql_real_escape_string($inCat));
            $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);
            $result_row = mysql_fetch_assoc($result); 
            if (!is_null($result_row['parentid'])) {
                $intParentID = $result_row['parentid'];
                if ($intParentID == 0) { $intParentID = -1; }
            }
            
            $sql_query = sprintf("
                    SELECT `id`, `title`
                        from `cbwire`.`categories`
                        WHERE `parentid`=%s or `parentid`=%s or `id`=%s
                        ORDER BY `parentid`<>0, `parentid` is null, `title`='Other', `title`;",
                    mysql_real_escape_string($intParentID), 
                    mysql_real_escape_string($inCat),
                    mysql_real_escape_string($intParentID));

            $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);

            if (mysql_num_rows($result) > 20) {
                $arrSizes = array("0.6", "0.6", "0.6", "0.6", "0.6", "0.6", "0.6", "0.6", "0.6", "0.6", "0.6", "0.6", "0.6", "0.6", "0.6", "0.6", "0.6", "0.6", "0.6", "0.6", "0.6", "0.6", "0.6", "0.6", "0.6", "0.6", "0.6", "0.6", "0.6", "0.6", "0.6", "0.6", "0.6", "0.6", "0.6", "0.6", "0.6", "0.6", "0.6");
            } else if (mysql_num_rows($result) > 10) {
                $arrSizes = array("0.9", "0.9", "0.9", "0.9", "0.9", "0.9", "0.9", "0.9", "0.9", "0.9", "0.9", "0.9", "0.9", "0.9", "0.9", "0.9", "0.9", "0.9", "0.9", "0.9", "0.9", "0.9", "0.9", "0.9", "0.9", "0.9", "0.9", "0.9", "0.9", "0.9", "0.9", "0.9", "0.9", "0.9", "0.9", "0.9", "0.9", "0.9", "0.9");                
            }
            
            $x = 0;
            while ($result_row = mysql_fetch_assoc($result)) {
                $intTempID = $result_row['id'];
                
                $sql_query_count = sprintf("
                        SELECT count(distinct lc.`listingid`) as count
                            from `cbwire`.`listing_cat` lc
                            inner join `cbwire`.`categories` c on c.`id` = lc.`categoryid`
                            inner join `cbwire`.`listings` l on l.`id` = lc.`listingid`
                            left outer join (Select w.`listingid`, count(*) as count from `cbwire`.`when` w where w.`start_date`> '%s' group by w.`listingid`) w on w.`listingid` = lc.`listingid`
                            WHERE (c.`id`=%s or c.`parentid`=%s) and l.`deleted`=0
                            and (l.`classifieds`=0 or l.`expiry_date` is null 
                            or l.`expiry_date` = '0000-00-00 00:00:00' or l.`expiry_date` >= '%s 00:00:00')
                            and (l.`thingstodo` = 0 or w.`count` > 0);",
                        mktime(0, 0, 0, date("m"), date("d"), date("y")),
                        mysql_real_escape_string($intTempID),
                        mysql_real_escape_string($intTempID),
                        date("Y-m-d 00:00:00"));

                $result_count = mysql_query($sql_query_count) or log_error($sql_query_count, mysql_error(), $page, true);
                $intCount = mysql_result($result_count, 0); 
                if ((!is_null($intCount)) && ($intCount <> "")) {
                    $intCount = sprintf("(%s)", $intCount);
                }
                echo sprintf(" &nbsp;&nbsp;<a href=\"cat.php?in=%s\" style=\"font-size: %sem;\">%s <span style=\"font-size: 10px\">%s</span></a>&nbsp;&nbsp; ", $intTempID, $arrSizes[$x], $result_row['title'], $intCount);
                
                $x = $x+1;
            } 
        ?>