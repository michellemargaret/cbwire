<?php 
    include_once "includes/func.php";    
    
    $page = "attractions.php";    
    $strMetaTitle = "Attractions on cbwire.ca";
    $strMetaDescription = "Post and search attraction listings for Conception Bay North";
    
    include_once "includes/header.inc.php";    
?><div id="fb-root"></div>
<script>(function(d, s, id) {
var js, fjs = d.getElementsByTagName(s)[0];
if (d.getElementById(id)) return;
js = d.createElement(s); js.id = id;
js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<div class="breadcrumbs"><a href="index.php">Home</a> >> <a href="attractions.php">Attractions</a> >></div>

<div class="page_section">
    <div class="page_intro_2">
        <div class="shareRight">
            <?php 
                include_once "share_buttons.php"; 
                include_share_buttons("http://www.cbwire.ca/attractions.php", "Attractions on cbwire.ca");
            ?>
        </div>
        Attractions</div> 
    
    
    <script type="text/javascript">
        var mainImgH = new Array(); // header for image
        var mainImgT = new Array(); // text description
        var mainImgHLines = new Array(); // number of lines for Header text

        mainImgH[0] = 'Tourist Attractions';

        mainImgHLines[0] = 1;

        mainImgT[0] = '';
    </script>

        <div id="slider2">
            <a href="cat.php?in=392"><img src="imgs/harbourgrace2.jpg" class="imgMain" id="imgMain0" style="display:inline;"></a>
            <div class="clear"></div>
        </div>
        <div class="center_column"> 
            <div class="page_subtitle">Welcome</div>        
            <div class="quote">
                Use this page to browse attractions in Conception Bay North. 
                <a href="update_listing_pre.php">Click here</a> to add your listing here for free.
            </div>
            <div class="quote_credit"></div>
            <br><br>
            <div class="page_subtitle">Search Attractions</div>  
            <form id="mainsearch_form" name="mainsearch_form" method="post" action="listall.php?a=attractions" enctype="multipart/form-data">
                <input type="text" name="txtMainSearch" id="txtMainSearch" maxlength="20" value="" class="txt_login">
                <input type="hidden" id="searchType" name="searchType" value="basic">
                <button class="button2" id="btnHomeSearch" name="btnHomeSearch">Go</button>     
            </form>

            <div style="float: right; font-size: 0.8em; padding-top: 6px; display: none;">
            <a href="advanced_search.php">Advanced<br>Search</a></div>
        </div>

        <div class="clear"></div>    
        <div class="secondRow">
            <div class="dataColumnSmall2">
                <div class="section_heading">Quick Links</div>

                <ul>
                    <li class="listTitle"><a href="cat.php">Categories</small></a></li> 
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
                <?php include_once "town_list_short.php"; ?>
            </div>
            <div class="dataColumnLarge">
                <?php include_once "attraction_highlights.php"; ?>
            </div>   
        </div>
</div>
<?php 
    include_once "right_column.php";
    include_right_column(true);
?>
<?php
    include_once "includes/footer.inc.php";
?>