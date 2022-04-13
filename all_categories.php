<?php   
    include_once "includes/func.php"; 
    
    $page = "all_categories.php";
?>

<div style="float:right;width:30px;">&nbsp;</div>
<div class="dataColumnSmall">
    <ul>
        <li class="listTitle">Attractions</li>  
        <?php
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
        ?>
    </ul>
    <ul>
        <li class="listTitle">Classifieds</li>  
        <?php
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
        ?>
    </ul>
</div>
<div style="float:right;width:30px;">&nbsp;</div>
<div class="dataColumnSmall">
    <ul>
        <li class="listTitle">Things To Do</li>  
        <?php
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
        ?>
    </ul>
    <ul>
        <li class="listTitle">Directory</li>  
        <?php
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
        ?>
    </ul>
</div>