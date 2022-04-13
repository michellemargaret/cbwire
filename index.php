<?php 
    include_once "includes/func.php";    
    
    $page = "index.php";
    
    if (isset($_GET["in"])) {
        if (is_numeric($_GET["in"])) {
            $inListingID = $_GET["in"];
            
            if ($inListingID > 0) {
                header('Location: view.php?in=' . $inListingID);
            }
        }
    } else if (isset($_GET["goto"])) {
        if (is_numeric($_GET["goto"])) {
            $inListingID = $_GET["goto"];
            
            if ($inListingID > 0) {
                header('Location: view.php?in=' . $inListingID);
            }
        }
    }
    
    
    $strMetaTitle = "cbwire.ca";
    $strMetaDescription = "Conception Bay North's Online Bulletin Board";    
    
    include_once "includes/header.inc.php";
?>

<div id="homeSlides">
        
</div>

<div style="float:left; padding: 10px 0px 0px 0px; margin: 0px;">
    <div class="homeSquare" id="homeSquare1">
        <h1>What's Happening</h1>
        <img class="loading" src="imgs/wait.gif">
    </div>
    <div class="homeSquare" id="homeSquare2">
        <h1>Links</h1>
        <img class="loading" src="imgs/wait.gif">        
    </div>
    <div style="clear:both;"></div>
    <div class="homeSquare" id="homeSquare3">
        <h1>Just Added</h1>
        <img class="loading" src="imgs/wait.gif">
    </div>
    <div class="homeSquare" id="homeSquare4">
        <h1><a href="http://blog.cbwire.ca">blog.cbwire.ca</a></h1>
        <img class="loading" src="imgs/wait.gif">
    </div>
</div>
<?php 
    include_once "right_column.php";
    include_right_column(true);
?>

<?php
    include_once "includes/footer.inc.php";
?>