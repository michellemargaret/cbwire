<?php   
    include_once "includes/func.php"; 
    
    $page = "search_form.php";    
        
    switch ($searchType) {
        case "date":
            ?>
                <div class="page_intro">Date Search</div>  
                <form id="mainsearch_form" name="mainsearch_form" method="post" action="search.php?in=<?php echo $searchType; ?>&section=<?php echo $section; ?>" enctype="multipart/form-data">                    
                    Find <b>Things To Do</b> between
                    <br>
                    <br>
                    <div class="textbox_labelinline" style="width: 200px;"></div>
                    <div class="textbox_div"> 
                        <input type="text" name="txtSearchFDate" id="txtSearchFDate" value="<?php echo $strSearchFDate; ?>" maxlength="10" class="txt_date">
                    </div>   

                    <div class="textbox_labelinline">and&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                    <div class="textbox_div"> 
                        <input type="text" name="txtSearchTDate" id="txtSearchTDate" value="<?php echo $strSearchTDate; ?>" maxlength="10" class="txt_date">
                    </div>
                    
                    <button class="button2" id="btnMainSearch" name="btnMainSearch">Search</button>  
                    <input type="hidden" id="searchType" name="searchType" value="date">
                    <div style="clear:both"></div>
                    <br><br>
                </form>
                
            <?php
            
            
            break;
        case "category":
            ?>
                <div class="page_intro">Category Search</div>  
                
                <form id="mainsearch_form" name="mainsearch_form" method="post" action="search.php?in=<?php echo $searchType; ?>&section=<?php echo $section; ?>" enctype="multipart/form-data">

                    <input type="text" name="selectedCategories" id="selectedCategories" size="1" style="display:none;">
                    <div class="search_smallbox">
                        1 Choose Sections
                        <ul>
                            <li><input type="checkbox" id="chkSearchSection[]" name="chkSearchSection[]" value="things" <?php if (strpos($strWhere, "activities") !== false) { echo "checked"; } ?>> Things To Do</li>
                            <li><input type="checkbox" id="chkSearchSection[]" name="chkSearchSection[]" value="directory" <?php if (strpos($strWhere, "directory") !== false) { echo "checked"; } ?>> Directory</li>
                            <li><input type="checkbox" id="chkSearchSection[]" name="chkSearchSection[]" value="classifieds" <?php if (strpos($strWhere, "classified") !== false) { echo "checked"; } ?>> Classifieds</li>
                            <li><input type="checkbox" id="chkSearchSection[]" name="chkSearchSection[]" value="attractions" <?php if (strpos($strWhere, "attraction") !== false) { echo "checked"; } ?>> Attractions</li>
                        </ul>
                    </div>
                    
                    <?php 
                        if (($strWhere <> "") && ($strWhere <> " and 1=1")) { 
                    ?>
                            <div class="search_smallbox">
                                2 Choose Main Categories
                                <ul>
                                    <?php 
                                    $sql_query = sprintf("SELECT cat.`id` as id, cat.`title` as title
                                        from `cbwire`.`categories` cat 
                                        where cat.`active`=1 and (cat.`parentid` is null or cat.`parentid`=0)  %s
                                        order by 
                                        cat.`title` = \"Other\", 
                                        cat.`title`;", $strWhere);

                                        $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);

                                        while ($row_category = mysql_fetch_assoc($result)) {
                                            $intCategoryID = 0;
                                            $strCatTitle = "";
                                            $strChecked = "";
                                            if (!is_null($row_category['id'])) { $intCategoryID = $row_category['id']; }
                                            if (!is_null($row_category['title'])) { $strCatTitle = $row_category['title']; }

                                            if (strpos($strSearchMCat, "," . $intCategoryID . ",") !== false) { $strChecked = "checked"; } 
                                            echo "<li><input type=\"checkbox\" id=\"chkSearchMCat[]\" name=\"chkSearchMCat[]\" value=\"" . $row_category['id'] . "\" " . $strChecked . "> " . $row_category['title'] . "</li>";
                                        }
                                    ?>
                                </ul>
                            </div>                    
                        <?php 

                            if ($strSearchMCat <> "") { 
                        ?>
                                <div class="search_smallbox">
                                    3 Choose Sub Categories
                                    <ul>
                                <?php                     
                                    $sql_query = sprintf("SELECT cat.`id` as id, cat.`title` as title, cat2.`title` as parenttitle
                                        from `cbwire`.`categories` cat 
                                        inner join `cbwire`.`categories` cat2 on cat.`parentid`=cat2.`id`
                                        where cat.`active`=1 and (cat.`parentid` in (%s))
                                        order by 
                                        cat2.`title` = \"Other\",
                                        cat2.`title`,
                                        cat.`title` = \"Other\", 
                                        cat.`title`;", $strSearchMCat);

                                    $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);

                                    while ($row_category = mysql_fetch_assoc($result)) {
                                        $strChecked = "";
                                        if (strpos($strSearchSCat, "," . $row_category['id'] . ",") !== false) { $strChecked = "checked"; } 
                                        echo "<li><input type=\"checkbox\" id=\"chkSearchSCat[]\" name=\"chkSearchSCat[]\" value=\"" . $row_category['id'] . "\" " . $strChecked . "> " . $row_category['parenttitle'] . " > " . $row_category['title'] . "</li>";
                                    }                                     
                                ?>
                                    </ul>
                                    <input type="hidden" id="chkSearchSCat2" name="chkSearchSCat2" value="">
                                </div>
                    <?php 
                            } 
                        }
                    ?>
                    <br><br>
                    <input type="hidden" id="searchType" name="searchType" value="category">
                    <button class="button2" id="btnMainSearch" name="btnMainSearch">                        
                        <?php
                            if (($strSearchMCat == "") || ($strWhere == "") || ($strWhere == " and 1=1"))  { echo "Next"; } else { echo "Search"; } 
                        ?>
                    </button>       
                </form>
            <?php
            
            
            break;
        case "age":
            ?>
                <div class="page_intro">Age Search</div>  
                <form id="mainsearch_form" name="mainsearch_form" method="post" action="search.php?in=<?php echo $searchType; ?>&section=<?php echo $section; ?>" enctype="multipart/form-data">
                    Choose age(s)
                    <div class="search_checkboxes">
                        
                    <?php 
                        $sql_query = sprintf("SELECT `id`, `title` from `cbwire`.`ages` where `active`=1 order by `ordernum`;");

                        $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);

                        while ($row_category = mysql_fetch_assoc($result)) {
                            $strChecked = "";
                            if (strpos($strSearchAge, "," . $row_category['id'] . ",") !== false) { $strChecked = "checked"; } 

                            echo "<div class=\"search_option\"><input type=\"checkbox\" id=\"chkSearchAge[]\" name=\"chkSearchAge[]\" value=\"" . $row_category["id"] . "\" " . $strChecked . "> " . $row_category['title'] . "</div> ";
                        }
                    ?>
                    </div>
                    <input type="hidden" id="searchType" name="searchType" value="age">
                    <button class="button2" id="btnMainSearch" name="btnMainSearch">Search</button>  
                    <br><br>
                    <div class="section_notes">* This search is limited to Things To Do</div>
                </form>
            <?php
            
            break;
        case "town":
            ?>
                <div class="page_intro">Town Search</div>  
                
                <form id="mainsearch_form" name="mainsearch_form" method="post" action="search.php?in=<?php echo $searchType; ?>&section=<?php echo $section; ?>" enctype="multipart/form-data">
                    Choose town(s)
                    <div class="search_checkboxes">
                        
                    <?php 
                        $sql_query = sprintf("SELECT `id`, `name` as title from `community` order by `name`;");

                        $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);

                        while ($row_category = mysql_fetch_assoc($result)) {
                            $strChecked = "";
                            if (strpos($strSearchTown, "," . $row_category['id'] . ",") !== false) { $strChecked = "checked"; } 
                            
                            echo "<div class=\"search_option\"><input type=\"checkbox\" id=\"chkSearchTown[]\" name=\"chkSearchTown[]\" value=\"" . $row_category["id"] . "\" " . $strChecked . "> " . $row_category['title'] . "</div> ";
                        }
                    ?>
                    </div>
                    
                    <input type="hidden" id="searchType" name="searchType" value="town">
                    <button class="button2" id="btnMainSearch" name="btnMainSearch">Search</button>  
                </form>
            <?php
            
            break;
        default:
            ?>
                <div class="page_intro">Basic Search</div>  
               <form id="mainsearch_form" name="mainsearch_form" method="post" action="listall.php?a=search" enctype="multipart/form-data">
                    <input type="text" name="txtMainSearch" id="txtMainSearch" maxlength="20" value="" class="txt_login">
                    <input type="hidden" id="searchType" name="searchType" value="basic">
                    <button class="button2" id="btnHomeSearch" name="btnHomeSearch">Go</button>     
                </form>
            <?php
            break;
    }
?>