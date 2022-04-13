<?php
    include_once "../includes/connect.php";
    $link = connect();
    $page = "add_date.php";

    $editListingType = $_SESSION["editListingType"];
    $strWhere = "1=1";
    
    if ($editListingType == "Thing To Do") {
        $strWhere = "`activities`=1";
    } elseif ($editListingType == "Directory") {
        $strWhere = "`directory`=1";
    } elseif ($editListingType == "Classified") {
        $strWhere = "`classifieds`=1";
    } elseif ($editListingType == "Attraction") {
        $strWhere = "`attractions`=1";
    }
    
    $sql_query = sprintf("SELECT cat.`id` as id, cat.`title` as title
                            from `cbwire`.`categories` cat 
                            where cat.`active`=1 and (cat.`parentid` is null or cat.`parentid`=0) and %s
                            order by 
                            cat.`title` = \"Other\", 
                            cat.`title`;", $strWhere);

   $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);					
?>

<input type="text" name="selectedCategories" id="selectedCategories" size="1" style="display:none;">
<div class="ddl_box_out" id="divMainCategory">    
    <div class="list_heading">Choose category</div>
    <?php 
        while ($row_category = mysql_fetch_assoc($result)) {
            $intCategoryID = 0;
            $strCatTitle = "";
            if (!is_null($row_category['id'])) { $intCategoryID = $row_category['id']; }
            if (!is_null($row_category['title'])) { $strCatTitle = $row_category['title']; }

            echo "<a href=\"#\" id=\"category" . $row_category['id'] . "\">" . $row_category['title'] . "</a>";
        }
    ?>
</div>

<div class="ddl_box_out" id="divSubCategoryBox">    
</div>

<div class="add_remove" id="divAddRemove">
    <a href="#" alt="Select Category" id="aSelectCategory">&nbsp;&nbsp;&nbsp; Add &nbsp;&nbsp; >></a>
    <a href="#" alt="Remove Category" id="aRemoveCategory"><< &nbsp;&nbsp;Remove &nbsp;&nbsp;&nbsp;</a>
</div>

<div class="ddl_box_selected" id="divSelectedCategoryBox">
    <div class="list_heading">Your Selections</div>
</div>