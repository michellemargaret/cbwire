<?php
    include_once "../includes/connect.php";
    $link = connect();
    $page = "add_date.php";

    $parent = 0;
    
    if (isset($_GET['in'])) {
        if (is_numeric($_GET['in'])) {
            $parent = $_GET['in'];
        }
    }
    if ($parent > 0) {
        echo "<div class=\"list_heading\">Choose sub-category</div>";
        $sql_query = sprintf("SELECT cat.`id` as id, cat.`title` as title
                                from `cbwire`.`categories` cat 
                                where cat.`active`=1 and cat.`parentid`=%s
                                order by 
                                cat.`title` = \"Other\", 
                                cat.`title`;", $parent);

        $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);	
        
        while ($row_category = mysql_fetch_assoc($result)) {
            $intCategoryID = 0;
            $strCatTitle = "";
            if (!is_null($row_category['id'])) { $intCategoryID = $row_category['id']; }
            if (!is_null($row_category['title'])) { $strCatTitle = $row_category['title']; }

            echo "<a href=\"#\" id=\"category" . $row_category['id'] . "\">" . $row_category['title'] . "</a>";
        }
    }
    			
?>