<?php 
    include_once "includes/func.php";    
    
    $userid = get_user_id();
    
    $page = "update_listing.php";   
    $editListingType = "";
    $editListingID = 0;
     
    if (isset($_SESSION["editListingID"]) && isset($_SESSION["editListingType"]) && ($_SESSION["editListingType"] <> "") && is_numeric($_SESSION["editListingID"])) {     
        $editListingType = $_SESSION["editListingType"];
        $editListingID = $_SESSION["editListingID"];
    } else {
        if (isLoggedIn()) {
            header( 'Location:yourinfo.php');
        } else {
            header( 'Location: update_listing_pre.php');
        }
            
        exit();
    }
    
    // Get selected categories
    $initialize_categories = "$(\"#divMainCategory a:first\").click();\n";
    $sql_query_cat = sprintf("SELECT c.`id` as `categoryid`, p.`title` as parent_title, c.`title` as category_title
                                from `cbwire`.`listing_cat_b` lc 
                                inner join `cbwire`.`categories` c on c.`id` = lc.`categoryid`
                                inner join `cbwire`.`categories` p on p.`id` = c.`parentid`
                                where lc.`listing_bid`=%s;",
                                mysql_real_escape_string($editListingID));

    $result_cat = mysql_query($sql_query_cat) or log_error($sql_query_cat, mysql_error(), $page, false);

    
    if (mysql_num_rows($result_cat) > 0) {
        $initialize_categories = $initialize_categories . "$(\"#aRemoveCategory\").show();\n$(\"#divSelectedCategoryBox\").show();\n";        
    }
    
    while ($result_row = mysql_fetch_assoc($result_cat)) {
        $initialize_categories = $initialize_categories . "$(\"#divSelectedCategoryBox\").html($(\"#divSelectedCategoryBox\").html() + '<a href=\"#\" id=\"category" . $result_row['categoryid'] . "\">" . $result_row['parent_title'] . " >> " . $result_row['category_title'] . "</a>');\n";        
        $initialize_categories = $initialize_categories . "$(\"#selectedCategories\").val($(\"#selectedCategories\").val() + \"x\" + " . $result_row['categoryid'] . " + \"x\");\n";  
    }
    
    $initialize_categories = $initialize_categories . "$(\"#divSelectedCategoryBox a\").click(function() {\n     var a = $(this);\n    if (a.hasClass(\"selected\")) {\n     a.removeClass(\"selected\");\n     } else {\n  a.addClass(\"selected\");\n   }\n   return false;\n });\n";
                
?>
<!doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN">
<html lang='en'>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=9" /> 
        <LINK href="css/styles.css" rel="stylesheet" type="text/css">
        <LINK href="css/estyles.css" rel="stylesheet" type="text/css">
        <LINK href="css/jquery-ui-1.8.17.custom.css" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="https://ajax.microsoft.com/ajax/jQuery/jquery-1.4.2.min.js"></script>        
        
        <script language="javascript" type="text/javascript">
            function initializeCategories() { 
                if ($("#divSelectedCategoryBox").length > 0) {                   
                    
                    <?php // Initialize values via jquery
                        echo $initialize_categories;
                    ?>   
                    return;
                } else {
                    setTimeout(function() { initializeCategories();},250);
                }
            }       
        </script>
        
        <script language="javascript" type="text/javascript" src="js/scripts.js"></script>
        <script language="javascript" type="text/javascript" src="js/ejquery.js"></script>
	<script src="js/jqueryui/jquery.ui.core.min.js"></script>
	<script src="js/jqueryui/jquery.ui.widget.min.js"></script>
	<script src="js/jqueryui/jquery.ui.datepicker.js"></script>           
        
        <script language="javascript" type="text/javascript">            
            $(document).ready(function(){
                <?php if (isLoggedIn()) { ?>
                    $("#loginstrip").hide();
                    $("#loggedinstrip").show();
                <?php } else { ?>
                    $("#loginstrip").show();
                    $("#loggedinstrip").hide();                    
                <?php } ?>                
            })         
        </script>
    </head>
    <body>
        <img src="imgs/overlay.gif" id="overlay_back"></div><div id="divLoading"><img src="imgs/wait2.gif"></div>
        <div class="container">
           <?php include_once "includes/topmenu.inc.php"; ?>
           <form id="jquery_done" method="post" action="<?php if (isLoggedIn()) { echo "yourinfo.php"; } else { echo "index.php"; } ?>" enctype="multipart/form-data"></form>
           <form id="update_listing" method="post" action="" enctype="multipart/form-data">
                <div id="maincell" class="maincell"> 
                    <div class="page_intro">
                        Tell us all about it
                    </div>
                    <br><?php
                    // Data for "The Basics"
                    $strTitle = "";
                    $strWebsite = "http://";
                    $strCost = "";
                    $strDescription = "";
                    $chrBuySell = "S";
                    $strExpiry = "";
                    $strHighlight = "";
                    $strAttraction = "";
                    $intOwnerID = 0;
                    $strUserName = "";
                    $intLastModifiedBy = 0;
                    $strLastModifiedBy = "";
                    $nonRegisteredEmail = "";
                    
                    $sql_query = sprintf("SELECT l.`title`, l.`website`, l.`description`, l.`attractions`, l.`cost`, l.`buysell`, l.`expiry_date`, l.`highlight`,
                                                l.`lastmodifiedby`, l.`userid`, l.`useremail`, user.`admin`, user.`name` as username, user2.`name` as lastmodifiedname
						from `cbwire`.`listings_b` l
                                                LEFT OUTER JOIN `cbwire`.`users` user on user.`id` = l.`userid`
                                                LEFT OUTER JOIN `cbwire`.`users` user2 on user2.`id` = l.`lastmodifiedby`
                                                where l.`id`=%s;",
						mysql_real_escape_string($editListingID));

                    $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);

                    if ($result_row = mysql_fetch_assoc($result)) {
                            if (!is_null($result_row['title'])) { $strTitle = $result_row['title']; }
                            if (!is_null($result_row['website'])) { $strWebsite = $result_row['website']; }
                            if (!is_null($result_row['cost'])) { $strCost = $result_row['cost']; }
                            if (!is_null($result_row['description'])) { $strDescription = $result_row['description']; }
                            if (!is_null($result_row['buysell'])) { $chrBuySell = $result_row['buysell']; }
                            if (!is_null($result_row['expiry_date'])) { 
                                    $strExpiry = $result_row['expiry_date']; 
                                    if (strlen($strExpiry) > 10) {
                                        $strExpiry = substr($strExpiry, 0, 10);
                                    }
                            }
                            if (!is_null($result_row['highlight'])) { $strHighlight = $result_row['highlight']; }
                            if (!is_null($result_row['attractions'])) { $strAttraction = $result_row['attractions']; }
                            if (!is_null($result_row['userid'])) { $intOwnerID = $result_row['userid']; }
                            if (!is_null($result_row['lastmodifiedby'])) { $intLastModifiedBy = $result_row['lastmodifiedby']; }
                            if (!is_null($result_row['lastmodifiedname'])) { $strLastModifiedBy = $result_row['lastmodifiedname']; }
                            if (!is_null($result_row['username'])) { $strUserName = $result_row['username']; }
                            if (!is_null($result_row['useremail'])) { $nonRegisteredEmail = $result_row['useremail']; }
                    }                 
                    
                    
                    if (isAdmin()) {
                        echo "Created by: " . $intOwnerID . " " . $strUserName . " / Last modified by: " . $intLastModifiedBy . " " . $strLastModifiedBy . " / Non registered email: " . $nonRegisteredEmail . "<br><br>";
                    }
                   ?>
                     
                    
                     <div class="section">                    
                        <div class="section_heading">
                            <div class="section_title">The Basics</div>
                            <div class="section_notes">Be sure to add plenty to the description.  This will help searches find your listing more often.</div>
                        </div>

                        <!-- Dummy Item -->
                        <div class="textbox_label"></div>
                        <div style="clear:both"></div> 
                        <input type="hidden" id="valEListID" name="valEListID" value="<?php echo $editListingID; ?>">
                        <input type="hidden" id="valListType" name="valListType" value="<?php echo $editListingType; ?>">

    <?php if ($editListingType == "Classified") { ?>            
                        <div class="textbox_label"></div>
                        <input type="radio" name="rdoBuySell" id="rdoBuySell" value="S" class="radio_normal" <?php if ($chrBuySell == "S") { echo "checked"; }?>>
                        <span class="checkbox_label">Sell</span>                  
                        <input type="radio" name="rdoBuySell" id="rdoBuySell" value="B" class="radio_normal" <?php if ($chrBuySell == "B") { echo "checked"; }?>>
                        <span class="checkbox_label">Buy</span>
                        <div style="clear:both"></div> 
                        <div class="textbox_error"></div>				
    <?php } ?>    

                        <div class="textbox_label">Title</div>
                        <input type="text" name="txtTitle" id="txtTitle" maxlength="60" value="<?php echo $strTitle; ?>" class="txt_normal">
                        <div style="clear:both"></div> 
                        <div class="textbox_error" id="txtTitleErr"></div>	                        
                        
    <?php if (isAdmin()) { ?>
                         <div class="textbox_label">Highlight</div>
                         &nbsp;&nbsp;&nbsp;<input type="checkbox" name="chkAdminUse" id="chkAdminUse" class="chk_normal" <?php if ($strHighlight <> "0") { echo "checked"; } ?>> [Admin Use Only] &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <?php if ($editListingType == "Directory") { ?>         
                         Attraction <input type="checkbox" name="chkAttraction" id="chkAttraction" class="chk_normal" <?php if ($strAttraction <> "0") { echo "checked"; } ?>> [Admin Use Only] 
                <?php } ?>
                        <div style="clear:both"></div> 
                        <div class="textbox_error"></div>	                  
    <?php } ?>

    <?php if (($editListingType == "Classified") || ($editListingType == "Thing To Do")) { ?>
                        <div class="textbox_label"><?php if ($editListingType == "Classified") { echo "Price"; } elseif ($editListingType == "Thing To Do") { echo "Cost"; } ?></div>
                        <input type="text" name="txtCost" id="txtCost" maxlength="60" value="<?php echo $strCost; ?>" class="txt_normal">
                        <div style="clear:both"></div> 
                        <div class="textbox_error"></div>
    <?php } ?>

                        <div class="textbox_label">Website</div>
                        <input type="text" name="txtWebsite" id="txtWebsite" maxlength="400" value="<?php echo $strWebsite; ?>" class="txt_normal">
                        <div style="clear:both"></div>
                        <div class="textbox_error" id="txtWebsiteErr"></div>                    

    <?php if ($editListingType == "Thing To Do") { ?>
                        <div class="textbox_label">Suggested Audience</div>         
                        <div class="checkboxes">
                            <?php
                                // Retrieve info from database
                                $sql_queryage = sprintf("SELECT `id`, `title` from `cbwire`.`ages` where `active`=1 order by `ordernum`;");
                                $resultage = mysql_query($sql_queryage) or log_error($sql_queryage, mysql_error(), $page, false);
                                $intCount = 0;                                
                                
                                // Get existing ages already selected for this record
                                $arrSelectedAges = array();
                                $sql_queryage2 = sprintf("SELECT `agesid` from `cbwire`.`listing_age_b` where `listing_bid`=%s;",
                                        mysql_real_escape_string($editListingID));
                                $result_age2 = mysql_query($sql_queryage2) or log_error($sql_queryage2, mysql_error(), $page, false);

                                while ($result_row = mysql_fetch_assoc($result_age2)) {                                    
                                    if (!is_null($result_row['agesid'])) { 
                                        $arrSelectedAges[$result_row['agesid']] = 1;                                        
                                    }
                                }

                                while ($result_row = mysql_fetch_assoc($resultage)) {  
                                    $checked = "";
                                    if (isset($arrSelectedAges[$result_row['id']])) { $checked = " checked "; }
                                    ?>
                                    <input type="checkbox" name="chk<?php echo $result_row['id']; ?>" id="chk<?php echo $result_row['id']; ?>" value="on" class="chk_normal"<?php echo $checked; ?>>
                                    <span class="checkbox_label"><?php echo $result_row['title']; ?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <?php 
                                    $intCount++;

                                    if ($intCount == 3) { echo "<br>"; $intCount = 0; }
                                }
                                
                                
                            ?>     
                        </div>
                        <div style="clear:both"></div>
    <?php } ?>

                        <div class="textbox_label">Description</div>
                        <textarea cols=59 rows=10 id="txtDescription" name="txtDescription" wrap="soft" class="txt_normal"><?php echo $strDescription; ?></textarea>
                        <div style="clear:both"></div>
                        <div id="charCounter" class="char_count"><?php echo 500-strlen($strDescription); ?></div>                    			

    <?php if ($editListingType == "Classified") { ?>
                        <div class="textbox_label">Expiry Date</div>
                        <div class="textbox_div">
                            <input type="text" name="txtExpiry" id="txtExpiry" maxlength="10" value="<?php echo $strExpiry; ?>" class="txt_date">
                        </div>
                        <div style="clear:both"></div> 
                        <div class="textbox_error" id="txtExpiryErr"></div>
    <?php } ?>

                    </div> 
                    
                    <br>               
                    <div class="section" id="section_categories">                    
                        <div class="section_heading">
                            <div class="section_title">Categories</div>
                            <div class="section_notes">Which category does this listing fall into?  Choose at least one.  The more categories you choose, the easier it will be for people to find.</div>
                        </div>
                    <div id="add_categories" class="add_categories">                       
                    </div>
                    </div>
                    
                    <br>
                    <input type="text" name="contactSections" id="contactSections" size="1" style="display:none;">
                    <div class="section" id="section_contact">                    
                        <div class="section_heading">
                            <div class="section_title">Contact Information</div>
                            <div class="section_notes">How can people get more information?</div>
                        </div>
                        <div id="add_contacts" class="add_contacts">                       
                        </div>
    <?php if (($editListingType == "Directory") || ($editListingType == "Attraction")) { ?>
                        <a href="#" id="link_add_contact" class="add"><img src="imgs/add.gif" border="0" id="imag_add_contact">&nbsp;&nbsp;Add another contact entry</a>
    <?php } ?>                    
                    </div>   
                    <br>
                    <input type="text" name="locationSections" id="locationSections" size="1" style="display:none;">
    <?php if ($editListingType == "Thing To Do") { ?>
                    <div class="section" id="section_location">                    
                        <div class="section_heading">
                            <div class="section_title">Location</div>
                            <div class="section_notes">Where is this taking place?</div>
                        </div>
                        <div id="add_locations" class="add_locations">                       
                        </div>
                        <a href="#" id="link_add_location" class="add"><img src="imgs/add.gif" border="0" id="imag_add_location">&nbsp;&nbsp;Add another location entry</a>
                    </div>
                    <br>                   
                    <input type="text" name="dateSections" id="dateSections" size="1" style="display:none;">
                    <div class="section" id="section_date">                    
                        <div class="section_heading">
                            <div class="section_title">Date & Time</div>
                            <div class="section_notes">When is this taking place?  If it recurs regularly at the same time, select the appropriate "Repeating" option - Daily, Weekly or Montly</div>
                        </div>
                        <div id="add_dates" class="add_dates">
                        </div>
                        <a href="#" id="link_add_date" class="add"><img src="imgs/add.gif" border="0" id="imag_add_contact">&nbsp;&nbsp;Add another date entry</a>
                    </div>               
                    <br>
    <?php } ?>
                    
    <?php   
        $strThumbnail = "";
        $intPictureID = "";
        $strHeight = "100";
        $strWidth = "100";
        $strDimensions = "";
        $sql_query = sprintf("SELECT p.`id`, `thumbnail`, `smallWidth`, `smallHeight`
                                    from `cbwire`.`pictures` p
                                    inner join `cbwire`.`listings_b` l on l.`pictureid` = p.`id`
                                    where l.`id`=%s;",
                                    mysql_real_escape_string($editListingID));

        $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);

        if ($result_row = mysql_fetch_assoc($result)) {
            if (!is_null($result_row['id'])) { $intPictureID = $result_row['id']; }
            if (!is_null($result_row['thumbnail'])) { $strThumbnail = $result_row['thumbnail']; }
            if (!is_null($result_row['smallWidth'])) { $strWidth = $result_row['smallWidth']; }
            if (!is_null($result_row['smallHeight'])) { $strHeight = $result_row['smallHeight']; }
        }
        
        if (($strWidth <> "") && ($strHeight <> "")) {
            $strDimensions = "width: " . $strWidth . "; height: " . $strHeight . ";";
        }
    ?>
                    <div class="section" id="section_picture">                    
                        <div class="section_heading">
                            <div class="section_title">Picture</div>
                            <div class="section_notes">Upload a picture to display with your listing.</div>
                        </div>
                        <div id="upload_picture" class="upload_picture">
                            <div class="textbox_label" style="margin-top: 5px;">Upload</div>
                            <iframe frameborder=0 height="60" src="upload.php" style="float:left;"></iframe>
                            <div style="clear:both"></div>         

                            <div  class="textbox_label"></div><div id="pictureMessage"></div>
                            <div style="clear:both"></div> 
                        </div>
                        <div id="select_picture" class="select_picture">

                        </div> 
                        <div class="textbox_label" >Current Selection:</div>
                        <div id="pictureDisplayBox" style="<?php echo $strDimensions; ?>">
                            <?php 
                                if ($strThumbnail <> "") {                            
                                    echo "<img src=\"uploads\\" . $strThumbnail . "\">";
                                }
                            ?>
                        </div>
                        <div class="remove_images">
                            <a href="#" id="removeImage">X</a>
                        </div>
                        <div style="clear:both"></div>    

                        <input type="text" name="txtPictureID" id="txtPictureID" class="txtPictureID" value="<?php echo $intPictureID; ?>" maxlength="5" value="" size="1" style="display:none;">
                    </div>

                    
                    <div id="finished_buttons">
                        <?php if (isLoggedIn()) { ?>
                            <a href="#" class="button" id="btnSaveForLater">Save</a>
                            <a href="#" class="button" id="btnPublish">Publish</a>   
                            <a href="#" class="button" id="btnDelete">Delete</a>          
                            <a href="#" class="button" id="btnCancel2">Cancel</a>

                        <?php } else { ?>
                            <a href="#" class="button" id="btnSave">Save</a>             
                            <a href="#" class="button" id="btnCancel">Cancel</a>
                        <?php } ?>
                    </div>
                    
                </div>
           </form>
       </div>
    </body>
</html>
