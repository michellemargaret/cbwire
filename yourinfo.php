<?php 
    include_once "includes/func.php";
    include_once "includes/search.inc.php";
    
    global $page;
    $userid = get_user_id();
    
    if (isset($_SESSION["editListingID"])) {
        unset($_SESSION["editListingID"]);
    }
    if (isset($_SESSION["editListingType"])) {
        unset($_SESSION["editListingType"]);
    }    
    
    $page = "yourinfo.php";
        
    if (isLoggedIn() === false) {
        header( 'Location: login.php' );
        exit();
    }
    include_once "includes/header.inc.php";   
?>         
    <div class="page_intro">
        Your Listings
    </div>  
    
    <div class="page">
        <div class="section_content">
            <form id="filterFormYI" method="post" action="" enctype="multipart/form-data">
                <div class="leftalign_label">Filter</div>
                <div class="textbox_div"><input type="text" id="txtFilterOwn" name="txtFilterOwn" class="txt_normal"></div>
            </form>
            <div class="rightStrip">        
                <strong>Add New</strong>: <a href="addnew.php?in=T" id="addNewT">Thing To Do</a> | <a href="addnew.php?in=C" id="addNewC">Classified</a> | <a href="addnew.php?in=D" id="addNewD">Directory</a><?php if (isAdmin()) { ?>  | <a href="addnew.php?in=A" id="addNewA">Attraction</a> <?php } ?>
            </div>               
            <div style="clear:both"></div> 

            <?php  if (isAdmin()) { ?>
            <div id="yourInfoSub">
                <?php returnAdminSubmitted("Awaiting Approval", 0, 5, $page, ""); ?>
            </div>          
            <?php } ?>
            
            <div id="yourInfoNotPub">
                <?php 
                    if (isAdmin()) {
                        returnAdminNonPublished("Not Published", $userid, 0, 5, $page, "");
                    } else {
                        returnGeneralNonPublished("Not Published", $userid, 0, 5, $page, "");
                    }
                ?>
            </div>
            
            <div id="yourInfoPub">
                <?php 
                    if (isAdmin()) {
                        returnAdminPublished("Published", 0, 5, $page, "");
                    } else {
                        returnGeneralPublished("Published", $userid, 0, 5, $page, "");
                    }
                ?>
            </div>
        </div>        
    </div>

<?php
    include_once "includes/footer.inc.php";
?>