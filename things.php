<?php 
    include_once "includes/func.php";    
    
    $page = "things.php";    
    
   $strMetaTitle = "Things To Do on cbwire.ca";
    $strMetaDescription = "Post and search things to do for Conception Bay North";
    
    include_once "includes/header.inc.php";    
?><div id="fb-root"></div>
<script>(function(d, s, id) {
var js, fjs = d.getElementsByTagName(s)[0];
if (d.getElementById(id)) return;
js = d.createElement(s); js.id = id;
js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<div class="breadcrumbs"><a href="index.php">Home</a> >> <a href="things.php">Things To Do</a> >></div>

<div class="page_section">
    <div class="page_intro_2">
        <div class="shareRight">
            <?php 
                include_once "share_buttons.php"; 
                include_share_buttons("http://www.cbwire.ca/things.php", "Things To Do on cbwire.ca");
            ?>
        </div>
        Things To Do
    </div> 
    
    
    <script type="text/javascript">
        var mainImgH = new Array(); // header for image
        var mainImgT = new Array(); // text description
        var mainImgHLines = new Array(); // number of lines for Header text

        mainImgH[0] = 'Swimming';

        mainImgHLines[0] = 1;

        mainImgT[0] = '';
    </script>

        <div id="slider2">
            <a href="cat.php?in=519"><img src="imgs/swim.jpg" class="imgMain" id="imgMain0" style="display:inline;"></a>
            <div class="clear"></div>
        </div>
        <div class="center_column"> 
            <div class="page_subtitle">Welcome</div>        
            <div class="quote">
                There's plenty happening every day in Conception Bay North.  Use this page to get information on the events and activities you're interested in.
            
                <a href="update_listing_pre.php">Click here</a> to add your listing here for free.
            </div>
            <div class="quote_credit"></div>
            <br>
            <div class="page_subtitle">Search Things To Do</div>  
            <form id="mainsearch_form" name="mainsearch_form" method="post" action="listall.php?a=things" enctype="multipart/form-data">
                <input type="text" name="txtMainSearch" id="txtMainSearch" maxlength="20" value="" class="txt_login">
                <input type="hidden" id="searchType" name="searchType" value="basic">
                <button class="button2" id="btnHomeSearch" name="btnHomeSearch">Go</button>     
            </form>
            <br>
            or return listings scheduled on
            <br>
            <form id="mainsearch_form" name="mainsearch_form" method="get" action="datesearch.php" enctype="multipart/form-data">
                <input type="hidden" name="a" id="a" value="Date Search">                
                <input type="text" name="b" id="b" value="" maxlength="10" class="txt_date">
                <button class="button2">Search</button>     
            </form>

            <div style="float: right; font-size: 0.8em; padding-top: 6px; display: none;">
                <a href="advanced_search.php">Advanced<br>Search</a>
            </div>
            
        </div>

        <div class="clear"></div>    
        <div class="secondRow">
            <div class="dataColumnSmall2">
                <div class="section_heading">Quick Links</div>

                <ul>
                    <li class="listTitle"><a href="cat.php">Categories</a></li> 

                </ul>
                
                <?php include_once "town_list_short.php"; ?>
            </div>
            <div class="dataColumnLarge">
                <?php include_once "things_highlights.php"; ?>
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