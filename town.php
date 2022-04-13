<?php 
    include_once "includes/func.php";    
    
    $page = "town.php";
    $inTown = 0;    
    $strPicture = "";
    $strText = "";
    $strWebsite = "";
    $strExternalLink = "";
    $strPageTitle = "Towns";
    
    $strPageParagraph = "";
    
    if (isset($_GET["in"])) {
        if (is_numeric($_GET["in"])) {
            $inTown = $_GET["in"];
        }
    } 
    
    $sql_query = "";
    
    if ($inTown > 0) {
        $sql_query = sprintf("
            SELECT `id`, `picture`, `text`, `website`, `externalLink`
                from `cbwire`.`sectionpictures` 
                WHERE `type` = 'town' and `value` = %s
                ORDER BY `inserted_date` desc
                LIMIT 3;",
            mysql_real_escape_string($inTown));   

        $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);

        
        if ($result_row = mysql_fetch_assoc($result)) {
            if (!is_null($result_row['picture'])) { $strPicture = $result_row['picture']; }                          
            if (!is_null($result_row['text'])) { $strText = $result_row['text']; }
            if (!is_null($result_row['website'])) { $strWebsite = $result_row['website']; }
            if (!is_null($result_row['externalLink'])) { $strExternalLink = $result_row['externalLink']; }            
        }
        
        $sql_query = sprintf("
                SELECT c.`name`, p.`text`
                    from `cbwire`.`community` c
                    left outer join `cbwire`.`sectionpage` p on p.`type`='town' and p.`value`=c.`id`
                    WHERE c.`id` = %s;",
                mysql_real_escape_string($inTown));
        
        $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);
        
        if (mysql_num_rows($result) > 0) {
            $result_row = mysql_fetch_assoc($result);                        
            if (!is_null($result_row['name'])) { if ($result_row['name'] <> "") { $strPageTitle = $result_row['name']; } }                                  
            if (!is_null($result_row['text'])) { $strPageParagraph = $result_row['text']; }
        }
    }
    
    $strMetaTitle = $strPageTitle . " on cbwire.ca";
    if ($inTown > 0) {
        $strMetaDescription = "Things to do and places to go in " . $strPageTitle;
    } else {
        $strMetaDescription = "Things to do and places to go, by town, in Conception Bay North";        
    }
    include_once "includes/header.inc.php";    
?><div id="fb-root"></div>
<script>(function(d, s, id) {
var js, fjs = d.getElementsByTagName(s)[0];
if (d.getElementById(id)) return;
js = d.createElement(s); js.id = id;
js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<div class="breadcrumbs"><a href="index.php">Home</a> >> <a href="town.php">Towns</a> >></div>

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
                include_share_buttons("http://www.cbwire.ca/town.php?in=" . $inTown, $strPageTitle . " on cbwire.ca");
            ?>
        </div>
        <?php 
            echo "<div class=\"page_header_title\">" . $strPageTitle . "</div>"; 
            if ($strExternalLink <> "") {
                echo sprintf("&nbsp;&nbsp;<a href=\"%s\" target=\"_blank\">%s</a>", $strExternalLink, $strExternalLink);
            } else {
                echo "<br>";
            }
            
        ?>
    </div> 
    
    <?php if ($inTown > 0) { ?>  

        <div class="clear"></div>    
        <div class="secondRow">
                <?php include_once "town_highlights.php"; ?>
        </div>
     <?php     
        } else { 
     ?>        
        <div class="link_list">
                <?php include_once "town_list.php"; ?>
        </div>
      <?php
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