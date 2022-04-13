<?php 
    include_once "includes/func.php";    
    
    $page = "cat.php";
    $inCat = 0;
    $strPicture = "";
    $strText = "";
    $strWebsite = "";
    $strPageTitle = "Categories";
    $strParentTitle = "";
    $strParentID = "";
    $strMetaTitle = "";
    $strMetaDescription = "";
    
    $strPageParagraph = "";
    
    if (isset($_GET["in"])) {
        if (is_numeric($_GET["in"])) {
            $inCat = $_GET["in"];
        }
    } 
    
    $sql_query = "";
    
    if ($inCat > 0) {
        $sql_query = sprintf("
            SELECT `id`, `picture`, `text`, `website`
                from `cbwire`.`sectionpictures` 
                WHERE `type` = 'category' and `value` = %s
                ORDER BY `inserted_date` desc
                LIMIT 3;",
            mysql_real_escape_string($inCat));  

        $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);
       
        if (mysql_num_rows($result) == 0) {            
            $sql_query = sprintf("
                SELECT `id`, `picture`, `text`, `website`
                    from `cbwire`.`sectionpictures`
                    WHERE `type` = 'category' and `value` = (Select `parentid` from `cbwire`.`categories` where `id`=%s limit 1)
                    ORDER BY `inserted_date` desc
                    LIMIT 3;",
                mysql_real_escape_string($inCat));  

            $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);
        }
        
        if ($result_row = mysql_fetch_assoc($result)) {
            if (!is_null($result_row['picture'])) { $strPicture = $result_row['picture']; }
            if ($inCat > 0) {                
                if (!is_null($result_row['text'])) { $strText = $result_row['text']; }
                if (!is_null($result_row['website'])) { $strWebsite = $result_row['website']; }
            } else {         
                if (!is_null($result_row['value'])) { if (is_numeric($result_row['value'])) { $strWebsite = "cat.php?in=" . $result_row['value']; } }
                if (!is_null($result_row['name'])) { $strText = $result_row['name']; } 
            }
         }
    }
    
    if ($inCat > 0) {
        $sql_query = sprintf("
                SELECT c.`title`, c.`parentid`, p.`title` as parentTitle
                from `cbwire`.`categories` c
                left outer join `categories` p on c.`parentid` = p.`id`
                WHERE c.`id` = %s;",
                mysql_real_escape_string($inCat));
        
        $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);
        if (mysql_num_rows($result) > 0) {
            $result_row = mysql_fetch_assoc($result);                        
            if (!is_null($result_row['title'])) { if ($result_row['title'] <> "") { $strPageTitle = $result_row['title']; } }  
            if ((!is_null($result_row['parentTitle'])) && (!is_null($result_row['parentid']))) {
                if (($result_row['parentTitle'] <> "") && ($result_row['parentid'] <> "")) {
                    $strParentTitle = $result_row['parentTitle'];
                    $strParentID = $result_row['parentid'];
                }
            }
        }
    }
    
    $strMetaTitle = $strPageTitle . " on cbwire.ca";
    $strMetaDescription = $strPageTitle . " listings for Conception Bay North";
    
    include_once "includes/header.inc.php";    
?><div id="fb-root"></div>
<script>(function(d, s, id) {
var js, fjs = d.getElementsByTagName(s)[0];
if (d.getElementById(id)) return;
js = d.createElement(s); js.id = id;
js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<div class="breadcrumbs"><a href="index.php">Home</a> >> <a href="cat.php">Categories</a> >></div>

<div class="page_section">
    <div class="page_header">

        <?php
            $strPictureCode = "";

            if ($strWebsite <> "") {
                $strPictureCode = sprintf("<a href=\"%s\">
                                            <img src=\"%s\" class=\"imgTown\" id=\"imgMain\" style=\"display:inline;\">
                                           </a>\n", $strWebsite, $strPicture);
            } else {
                if ($strPicture == "") {
                   $strPicture = "imgs/logo_filler.jpg";
                }

                $strPictureCode = sprintf("<img src=\"%s\" class=\"imgTown\" id=\"imgMain\" style=\"display:inline;\">\n", $strPicture);
            }
            
            echo $strPictureCode;
        ?>            
        <div class="shareRight">
            <?php 
                include_once "share_buttons.php"; 
                include_share_buttons("http://www.cbwire.ca/cat.php?in=" . $inCat, $strPageTitle . " on cbwire.ca");
            ?>
        </div>
        <?php 
            echo "<div class=\"page_header_title\">";            
            if (($strParentID <> "") && ($strParentTitle <> "")) {
                echo sprintf("<a href=\"cat.php?in=%s\">%s >></a> ", $strParentID, $strParentTitle);
            }
            echo $strPageTitle . "</div>";   
    
            if ($inCat > 0) {
                
                $sql_query = sprintf("
                        SELECT c.`title`, c.`id`
                        from `cbwire`.`categories` c
                        WHERE c.`parentid` = %s
                        ORDER BY `title`='Other', `title`;",
                        mysql_real_escape_string(($strParentID == "") ? $inCat : $strParentID));

                $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);
                if (mysql_num_rows($result) > 0) {
                    $strRelatedCategories = "";
                    
                    while ($result_row = mysql_fetch_assoc($result)) {                         
                        if ((!is_null($result_row['title'])) && (!is_null($result_row['id']))) { 
                            if (($result_row['title'] <> "") && ($result_row['id'] <> "") && ($result_row['id'] <> $inCat)) { 
                                $strRelatedCategories = $strRelatedCategories . sprintf("<a href=\"cat.php?in=%s\">%s</a> / ", $result_row['id'], $result_row['title']);
                            }
                        }
                    }
                    
                    if (strlen($strRelatedCategories) > 2) {
                        $strRelatedCategories = substr($strRelatedCategories, 0, strlen($strRelatedCategories)-3);
                    }
                    
                    echo "<div style=\"display:block; margin-left: 90px;\">". $strRelatedCategories . "</div>";
                }
            } else {
                echo "<br>";
            }
        ?>
    </div> 
    
    <?php 
        if ($inCat > 0) {
    ?>

        <div class="clear"></div>    
        <div class="secondRow">
                <?php include_once "cat_highlights.php"; ?>
        </div>
    <?php 
        } else {    
            include_once "all_categories.php";
        } 
    ?>
     
</div>

<?php 
    include_once "right_column.php";
    include_right_column(true);
?>
<?php
    include_once "includes/footer.inc.php";
?>