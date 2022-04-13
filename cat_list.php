<?php   
    include_once "includes/func.php"; 
    
    $page = "cat_list.php";
    
    $activities = 0;
    $classifieds = 0;
    $attractions = 0;
    $directory = 0;
    
    $sql_query = sprintf("
            SELECT `activities`, `classifieds`, `attractions`, `directory`
                from `cbwire`.`categories`
                WHERE `id`=%s;",
            mysql_real_escape_string($inCat));

    $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);
    $result_row = mysql_fetch_assoc($result);
    
    if (!is_null($result_row['activities'])) { $activities = $result_row['activities']; }
    if (!is_null($result_row['classifieds'])) { $classifieds = $result_row['classifieds']; }
    if (!is_null($result_row['attractions'])) { $attractions = $result_row['attractions']; }
    if (!is_null($result_row['directory'])) { $directory = $result_row['directory']; }
    
?>
    <ul>
        <li class="listTitle"><a href="cat.php">All Categories</a></li>  
        <?php
            if ($activities == 1) {
                echo "<li class=\"listTitle\">Things To Do</li>";
                $sql_query = sprintf("
                    SELECT `id`, `title`
                        from `cbwire`.`categories` 
                        WHERE `activities`=1 and (`parentid`=0 or `parentid` is null)
                        ORDER BY `title`='Other', `title`;");

                $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);

                $x = 0;
                while ($result_row = mysql_fetch_assoc($result)) {
                    $rowCss = ($x%2 == 0)? 'li_row_alt': 'li_row'; 
                    $x = $x+1;

                    echo sprintf("<li class=\"%s\"><a href=\"cat.php?in=%s\">%s</a></li>", $rowCss, $result_row['id'], $result_row['title']);
                } 
            }
            if ($directory == 1) {
                echo "<li class=\"listTitle\">Directory</li>";
                $sql_query = sprintf("
                        SELECT `id`, `title`
                            from `cbwire`.`categories`
                            WHERE `directory`=1 and (`parentid`=0 or `parentid` is null)
                            ORDER BY `title`='Other', `title`;");

                $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);

                $x = 0;
                while ($result_row = mysql_fetch_assoc($result)) {
                    $rowCss = ($x%2 == 0)? 'li_row_alt': 'li_row'; 
                    $x = $x+1;

                    echo sprintf("<li class=\"%s\"><a href=\"cat.php?in=%s\">%s</a></li>", $rowCss, $result_row['id'], $result_row['title']);
                } 
            }
            if ($attractions == 1) {
                echo "<li class=\"listTitle\">Attractions</li>";
                $sql_query = sprintf("
                        SELECT `id`, `title`
                            from `cbwire`.`categories`
                            WHERE `attractions`=1 and (`parentid`=0 or `parentid` is null)
                            ORDER BY `title`='Other', `title`;");

                $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);

                $x = 0;
                while ($result_row = mysql_fetch_assoc($result)) {
                    $rowCss = ($x%2 == 0)? 'li_row_alt': 'li_row'; 
                    $x = $x+1;

                    echo sprintf("<li class=\"%s\"><a href=\"cat.php?in=%s\">%s</a></li>", $rowCss, $result_row['id'], $result_row['title']);
                } 
            }
            if ($classifieds == 1) {
                echo "<li class=\"listTitle\">Classifieds</li>";
                $sql_query = sprintf("
                        SELECT `id`, `title`
                            from `cbwire`.`categories`
                            WHERE `classifieds`=1 and (`parentid`=0 or `parentid` is null)
                            ORDER BY `title`='Other', `title`;");

                $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);

                $x = 0;
                while ($result_row = mysql_fetch_assoc($result)) {
                    $rowCss = ($x%2 == 0)? 'li_row_alt': 'li_row'; 
                    $x = $x+1;

                    echo sprintf("<li class=\"%s\"><a href=\"cat.php?in=%s\">%s</a></li>", $rowCss, $result_row['id'], $result_row['title']);
                } 
            }
        ?>