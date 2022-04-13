<?php   
    include_once "includes/func.php";
    
    $userid = get_user_id();
     
    if (isset($_GET["in"])) {
        if (is_numeric($_GET["in"])) {
            $inListingID = $_GET["in"];
        }
    }
    
    $showEmailForm = false;
    
    // Data for "The Basics"
    $viewListingType = "Directory";
    $strTitle = "";
    $strWebsite = "http://";
    $strCost = "";
    $strDescription = "";
    $chrBuySell = "S";
    $strExpiry = "";
    $strPicture = "";
    $intPictureID = 0;
    $intOwnerID = 0;
    $intAdmin = 0;
    $intHideEmail = 0;
    $strUserName = "";
    $booHighlight = false;
    
    $sql_query = sprintf("SELECT l.`thingstodo`, l.`attractions`, l.`classifieds`, l.`directory`, l.`highlight`,
                                l.`title`, l.`website`, l.`description`, l.`cost`, l.`buysell`, l.`expiry_date`,
                                l.`picture`, l.`pictureid`, l.`userid`, user.`admin`, user.`name` as username
                                from `cbwire`.`listings` l
                                LEFT OUTER JOIN `cbwire`.`users` user on user.`id` = l.`userid`
                                
                                where l.`id`=%s and l.`deleted`=0;",
                                mysql_real_escape_string($inListingID));

    $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);

    if ($result_row = mysql_fetch_assoc($result)) {
            if (!is_null($result_row['attractions'])) { if ($result_row['attractions'] == 1) { $viewListingType = "Attraction"; }  }
            if (!is_null($result_row['directory'])) { if ($result_row['directory'] == 1) { $viewListingType = "Directory"; }  }
            if (!is_null($result_row['classifieds'])) { if ($result_row['classifieds'] == 1) { $viewListingType = "Classified"; }  }
            if (!is_null($result_row['thingstodo'])) { if ($result_row['thingstodo'] == 1) { $viewListingType = "Thing To Do"; }  }
            if (!is_null($result_row['highlight'])) { if ($result_row['highlight'] == 1) { $booHighlight = true; }  }
            if (!is_null($result_row['title'])) { $strTitle = $result_row['title']; }
            if (!is_null($result_row['website'])) { $strWebsite = $result_row['website']; }
            if (!is_null($result_row['cost'])) { $strCost = $result_row['cost']; }
            if (!is_null($result_row['description'])) { $strDescription = $result_row['description']; }
            if (!is_null($result_row['buysell'])) { $chrBuySell = $result_row['buysell']; }
            if (!is_null($result_row['expiry_date'])) { $strExpiryDate = $result_row['expiry_date']; }
            if (!is_null($result_row['picture'])) { $strPicture = $result_row['picture']; }
            if (!is_null($result_row['pictureid'])) { $intPictureID = $result_row['pictureid']; }
            if (!is_null($result_row['userid'])) { $intOwnerID = $result_row['userid']; }
            if (!is_null($result_row['admin'])) { $intAdmin = $result_row['admin']; }
            if (!is_null($result_row['username'])) { $strUserName = $result_row['username']; }
    } else {
        log_error("No results returned for listing " . $inListingID, "User given error page", $page, false);
        exit();
    }                
?>
                    <a href="#" id="listing_x">X</a>                       
                    <div class="listing_column2" id="listing_column2">  
                        <?php 
                            include_once "share_buttons.php"; 
                            include_share_buttons("http://www.cbwire.ca/view.php?in=" . $inListingID, $strTitle);
                        ?>
                         
                                <?php 
                            if (($viewListingType == "Directory") || ($viewListingType == "Attraction")) {
                                $sql_query_here = sprintf("SELECT distinct l.`id`, l.`title` from `cbwire`.`listings` l
                                                                inner join `cbwire`.`contact` contact on contact.`listingsid` = l.`id`
                                                                left outer join (Select `listingid`, count(*) as counter from `cbwire`.`when`
                                                                        where 
                                                                        (`start_date` >= '%s' or `end_date` >= '%s') group by `listingid`
                                                                ) `future` on `future`.`listingid` = l.`id`
                                                                left outer join (Select `listingid`, count(*) as counter from `cbwire`.`when` group by `listingid`														
                                                                ) `alldates` on `alldates`.`listingid` = l.`id`
                                                                where `contact`.linkid = %s and l.`thingstodo`=1 and l.`deleted`=0 and 
                                                                    (`future`.counter > 0 or `alldates`.counter = 0);",
                                                                mysql_real_escape_string(mktime(0, 0, 0, date("m"), date("d"), date("y"))),
                                                                mysql_real_escape_string(mktime(0, 0, 0, date("m"), date("d"), date("y"))),
                                                                mysql_real_escape_string($inListingID));
                                $result_here = mysql_query($sql_query_here) or log_error($sql_query_here, mysql_error(), $page, false);   
                                
                                if (mysql_num_rows($result_here) > 0) {                        
                                    echo "<div class=\"relatedTitle\">Happening here:</div>";
                                    echo "<ul>";
                                    while ($result_row = mysql_fetch_assoc($result_here)) {                                
                                            echo sprintf("<li class=\"random%s\"><a href=\"view.php?in=%s\">%s</a></li>", rand(1, 5), $result_row['id'], $result_row['title']);
                                    }   
                                    echo "</ul>";
                                }
                            } else if ($viewListingType == "Thing To Do") {
                                $sql_query_here = sprintf("SELECT distinct l.`id`, l.`title` from `cbwire`.`listings` l
                                                                inner join `cbwire`.`contact` contact on contact.`listingsid` = l.`id`
                                                                left outer join (Select `listingid`, count(*) as counter from `cbwire`.`when`
                                                                        where 
                                                                        (`start_date` >= '%s' or `end_date` >= '%s') group by `listingid`
                                                                ) `future` on `future`.`listingid` = l.`id`
                                                                left outer join (Select `listingid`, count(*) as counter from `cbwire`.`when` group by `listingid`														
                                                                ) `alldates` on `alldates`.`listingid` = l.`id`
                                                                where 
                                                                `contact`.linkid in (Select c.`linkid` from `cbwire`.`contact` c where c.`listingsid`=%s and c.`linkid`>0)
                                                                and l.`id` <> %s
                                                                and l.`thingstodo`=1 and l.`deleted`=0 and 
                                                                    (`future`.counter > 0 or `alldates`.counter = 0);",
                                                                mysql_real_escape_string(mktime(0, 0, 0, date("m"), date("d"), date("y"))),
                                                                mysql_real_escape_string(mktime(0, 0, 0, date("m"), date("d"), date("y"))),
                                                                mysql_real_escape_string($inListingID),
                                                                mysql_real_escape_string($inListingID));
                                $result_here = mysql_query($sql_query_here) or log_error($sql_query_here, mysql_error(), $page, false);    
                                
                                if (mysql_num_rows($result_here) > 0) {                        
                                    echo "<div class=\"relatedTitle\">Also happening here:</div>";
                                    echo "<ul>";
                                    while ($result_row = mysql_fetch_assoc($result_here)) {                                
                                            echo sprintf("<li class=\"random%s\"><a href=\"view.php?in=%s\">%s</a></li>", rand(1, 5), $result_row['id'], $result_row['title']);
                                    }   
                                    echo "</ul>";
                                }
                            }
                                      
                              

                                // Category links at bottom
                                $sql_query_cat = sprintf("SELECT cat.`id`, cat.`title`, cat.`parentid`, cat2.`title` as parenttitle from `cbwire`.`listing_cat` list_cat 
                                                            inner join `cbwire`.`categories` cat on cat.`id`=list_cat.`categoryid`
                                                            inner join `cbwire`.`categories` cat2 on cat.`parentid` = cat2.`id`
                                                            where list_cat.`listingid` = %s and cat.`active`=1
                                                            order by cat2.`title`, cat.`title`",
                                                            mysql_real_escape_string($inListingID));

                                $result_cat = mysql_query($sql_query_cat) or log_error($sql_query_cat, mysql_error(), $page, false);
                                $catParents = array();

                                if (mysql_num_rows($result_cat) > 0) {                        
                                    echo "<div class=\"relatedTitle\">Related Categories:</div>";
                                    echo "<ul>";
                                    while ($result_row = mysql_fetch_assoc($result_cat)) {
                                            if (!in_array($result_row['parentid'], $catParents)) {
                                                $catParents[] = $result_row['parentid'];
                                                echo sprintf("<li class=\"random%s\"><a href=\"cat.php?in=%s\">%s</a></li>", rand(1, 5), $result_row['parentid'], $result_row['parenttitle']);
                                            }
                                            if ($result_row['title'] <> "Other") {
                                                echo sprintf("<li class=\"random%s\"><a href=\"cat.php?in=%s\">%s</a></li>", rand(1, 5), $result_row['id'], $result_row['title']);
                                            }
                                    }   
                                    echo "</ul>";
                                }                    

                                $sql_query_age = sprintf("SELECT age.`id`, age.`title` from `cbwire`.`listing_age` la 
                                                        inner join `cbwire`.`ages` age on la.`agesid` = age.`id` 
                                                        where la.`listingid`=%s and age.`active`=1 
                                                        order by age.`ordernum`;",
                                                        mysql_real_escape_string($inListingID));			

                                $result_age = mysql_query($sql_query_age) or log_error($sql_query_age, mysql_error(), $page, false);

                                if (mysql_num_rows($result_age) > 0) {                                        
                                    echo "<div class=\"relatedTitle\">Age Groups:</div>";
                                    echo "<ul>";
                                    while ($result_row = mysql_fetch_assoc($result_age)) {
                                        echo sprintf("<li class=\"random%s\"><a href=\"search.php?in=age&a=%s\">%s</a></li>", rand(1, 5), $result_row['id'], $result_row['title']);
                                    } 
                                    echo "</ul>";
                                }  
                        ?>
                    </div>                
                    <div class="listing_title">
                        <?php echo $strTitle; ?>
                    </div>
                    <?php if (($chrBuySell <> "") && ($viewListingType == "Classified")) { ?>
                        <div class="nobr">
                        <div class="listing_field">Looking to</div>
                            <div class="listing_value">
                                <?php if ($chrBuySell == "B") { echo "Buy"; } else { echo "Sell"; } ?>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ($strCost <> "") { ?>
                        <div class="nobr">
                            <div class="listing_field">Cost:</div>
                            <div class="listing_value"><?php echo $strCost; ?></div>
                        </div>
                    <?php } ?>
                    <?php if ($strWebsite <> "") { 
                        if (strlen($strWebsite) > 8) {
                            if ((strrpos(strtolower($strWebsite), "http://") === false) && (strrpos(strtolower($strWebsite), "https://") === false)) {
                                $strWebsite = "http://" . $strWebsite;
                            }
                        }
                        ?>
                        <div class="nobr">
                            <div class="listing_value"><a href="<?php echo $strWebsite; ?>" target="_blank"><?php 
                                if (strlen($strWebsite) > 50) {
                                    echo substr($strWebsite, 0, 50) . "..."; 
                                } else {
                                    echo $strWebsite;                                    
                                }
                            ?></a></div>
                        </div>
                    <?php } ?>                        
                   
                        <?php // Picture
                            if (($strPicture <> "") && (file_exists($strPicture))) {
                                echo "<img src=\"" . $strPicture . "\" height=\"250\" class=\"listing_picture\">";
                            } elseif ($intPictureID > 0) {    
                                $strThumbnail = "";
                                $strLarge = "";

                                $sql_query = sprintf("SELECT `thumbnail`, `large`, `smallWidth`, `smallHeight`
                                                            from `cbwire`.`pictures` 
                                                            where `id`=%s;",
                                                            mysql_real_escape_string($intPictureID));

                                $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);

                                if ($result_row = mysql_fetch_assoc($result)) {
                                        if (!is_null($result_row['thumbnail'])) { $strThumbnail = $result_row['thumbnail']; }
                                        if (!is_null($result_row['large'])) { $strLarge = $result_row['large']; }

                                        if (($strThumbnail <> "") && ($strLarge <> "") && file_exists("uploads/" . $strThumbnail) && file_exists("uploads/" . $strLarge)) {
                                            echo "<a href=\"uploads/" . $strLarge . "\" target=\"_blank\"><img src=\"uploads/" . $strThumbnail . "\"  class=\"listing_picture\" width=\"200px\"></a>";
                                        } else if (($strThumbnail <> "") && file_exists("uploads/" & $strThumbnail) && file_exists("uploads/" . $strLarge)) {    
                                            echo "<img src=\"uploads/" . $strThumbnail . "\" class=\"listing_picture\" width=\"200px\">";
                                        }
                                } 
                            } else {
                                echo "";
                            }
                        ?> 
                        <div class="listing_description"><?php echo nl2br($strDescription); ?></div>
                        <div style="clear:both" ></div>                        
                        
                   <?php    // Date and time
                        if ($viewListingType == "Thing To Do") { 
                            include_once "includes/funcDateTime.inc.php";
                            
                            if (hasDateTime("A", $inListingID)) {
                                echo "<div class=\"listing_table\">                                        
                                    <div class=\"listing_table_top\"></div>
                                    <div class=\"listing_table_title\">When</div>
                                ";
                                
                                $sql_query_date = sprintf("SELECT id, start_date, end_date, start_time, end_time, recursive, expiry 
                                    from `cbwire`.`when` 
                                    where `listingid`=%s and `parentid` is null
                                    order by start_date, start_time, end_date, end_time, recursive, expiry;",
                                    mysql_real_escape_string($inListingID));
                                $result_date = mysql_query($sql_query_date) or log_error($sql_query_date, mysql_error(), $page, false);
                                $arrDates = array();
                                $arrDates = format_datetime($result_date);
                            
                                $x = 0;
                                
                               foreach ($arrDates as $strDates) {				
                                    $rowCss = ($x%2 == 0)? 'listing_row_alt': 'listing_row'; 
                                    
                                    echo sprintf("<div class=\"%s\">", $rowCss);
                                    echo $strDates;
                                    echo "</div>";

                                    $x = $x+1;
                                }
                                
                                echo "
                                </div>";
                            }   
                        }
                   ?>
                        
                       
                 <?php
                        // Location Information
                 
                        $strName = "";
                        $strPhone = "";
                        $strEmail = "";
                        $strEmailBr = "";
                        $strCommunityName = "";
                        $strOtherCommunity = "";
                        $strLocation1 = "";
                        $strLocation2 = "";
                        $strLocation3 = "";
                        $strLinkTitle = "";
                        $strLinkID = "";
                        
                        $sql_query_contact2 = sprintf("SELECT contact.`id`, contact.`name`, contact.`phone`, contact.`email`, contact.`hide_email`,
                                        com.`name` AS community_name, `other_community`,
                                        `location1`, `location2`, `location3`, contact.`linkid`, list.`title` as link_title
                                        from `cbwire`.`contact` contact
                                        LEFT OUTER JOIN `cbwire`.`community` com on com.`id` = contact.`communityid`
                                        LEFT OUTER JOIN `cbwire`.`listings` list on list.`id` = contact.`linkid`
                                        where contact.`listingsid`=%s and contact.`activity_contact`=0 and (list.`deleted`=0 or list.`deleted` is null)
                                        order by (length(`location1`) > 0), list.`title`, `location1`;",
                                        mysql_real_escape_string($inListingID));
                       
                        $result_contact2 = mysql_query($sql_query_contact2) or log_error($sql_query_contact2, mysql_error(), $page, false);

                        if (mysql_num_rows($result_contact2) > 0) {
                            $strTableTitle = "Where";
                            
                            if (($viewListingType == "Directory") || ($viewListingType == "Attraction")) {
                                $strTableTitle = "Location / Contact Information";
                            }
                            
                            echo "<div class=\"listing_table\">                                        
                                    <div class=\"listing_table_top\"></div>
                                    <div class=\"listing_table_title\">$strTableTitle</div>
                                ";
                            
                            $x = 0;
                            while ($result_row = mysql_fetch_assoc($result_contact2)) {  
                                $rowCss = ($x%2 == 0)? 'listing_row_alt': 'listing_row'; 
                                		
                                if (!is_null($result_row['name'])) { if ($result_row['name'] <> "") { $strName = $result_row['name'] . "<br>"; } }
                                if (!is_null($result_row['phone'])) { if ($result_row['phone'] <> "") { $strPhone = $result_row['phone'] . "<br>"; } }
                                if (!is_null($result_row['email'])) { if ($result_row['email'] <> "") { $strEmail = $result_row['email']; $strEmailBr = $result_row['email'] . "<br>"; } }
                                if (!is_null($result_row['hide_email'])) { if ($result_row['hide_email'] == "1") { $intHideEmail = 1; } }
                                if (!is_null($result_row['community_name'])) { if ($result_row['community_name'] <> "") { $strCommunityName = $result_row['community_name'] . "<br>"; } }
                                if (!is_null($result_row['other_community'])) { if ($result_row['other_community'] <> "") { $strOtherCommunity = $result_row['other_community'] . "<br>"; } }
                                if (!is_null($result_row['location1'])) { if ($result_row['location1'] <> "") { $strLocation1 = $result_row['location1'] . "<br>"; } }
                                if (!is_null($result_row['location2'])) { if ($result_row['location2'] <> "") { $strLocation2 = $result_row['location2'] . "<br>"; } }
                                if (!is_null($result_row['location3'])) { if ($result_row['location3'] <> "") { $strLocation3 = $result_row['location3'] . "<br>"; } }
                                if (!is_null($result_row['link_title'])) { if ($result_row['link_title'] <> "") { $strLinkTitle = $result_row['link_title'] . "<br>"; } }
                                if (!is_null($result_row['linkid'])) { $strLinkID = $result_row['linkid']; }
                            

                                if (($strCommunityName <> "") || ($strOtherCommunity <> "") || ($strLocation1 <> "") || ($strLocation2 <> "") || ($strLocation3 <> "") || ($strLinkTitle <> "")) {
                                    
                                    echo sprintf("<div class=\"%s\">", $rowCss);
                                    if (($strLinkTitle <> "") && ($strLinkID <> "")) {
                                        echo sprintf("<a href=\"view.php?in=%s\">%s</a>", $strLinkID, $strLinkTitle);
                                    }
                                    echo $strName . $strPhone;
                                    
                                    if (($viewListingType == "Classified") && ($intHideEmail == 1)) {                                        
                                        
                                    } else {
                                        echo $strEmailBr;
                                    }
                                            
                                    echo $strLocation1 . $strLocation2 . $strLocation3 . $strCommunityName . $strOtherCommunity;
                                    
                                    echo "</div>";
                                    
                                    $x = $x+1;
                                }

                   
                            }
                            
                            echo "
                                </div>";
                       }   
                  ?>     
                   
                   <?php    // Contact information
                        if ($viewListingType == "Thing To Do") { 
                            $strName = "";
                            $strPhone = "";
                            $strEmail = "";
                            $sql_query_contact = sprintf("SELECT `id`, `name`, `phone`, `email`					
                                                    from `cbwire`.`contact` where `listingsid`=%s and `activity_contact`=1;",
                                                    mysql_real_escape_string($inListingID));
                            $result_contact = mysql_query($sql_query_contact) or log_error($sql_query_contact, mysql_error(), $page, false);

                            if ($result_row = mysql_fetch_assoc($result_contact)) {		
                                if (!is_null($result_row['name'])) { $strName = $result_row['name']; }
                                if (!is_null($result_row['phone'])) { $strPhone = $result_row['phone']; }
                                if (!is_null($result_row['email'])) { $strEmail = $result_row['email']; }
                            }

                            if (($strName <> "") || ($strPhone <> "") || ($strEmail <> "")) {
                  ?> 
                        <div class="listing_table">
                            <div class="listing_table_top"></div>
                            <div class="listing_table_title">For more information, contact</div>
                            <div class="listing_row_alt">
                                <?php if ($strName <> "") { ?>
                                <div class="listing_table_details"><?php echo $strName; ?></div>
                                <?php } ?>
                                
                                <?php if ($strPhone <> "") { ?>
                                <div class="listing_table_details"><?php echo $strPhone; ?></div>
                                <?php } ?>
                                
                                <div class="listing_table_details"><?php echo $strEmail; ?></div>
                            </div>
                        </div>
                        
                  <?php
                            }
                        }
                        
                        
                        if (($viewListingType == "Classified") && ($intHideEmail == 1)) {         
                    ?>
                    <form id="message_poster" name="message_poster" method="post" action="messagePoster.php" target="_blank" enctype="multipart/form-data">
                        <div class="listing_table">                                        
                            <div class="listing_table_top"></div>
                            <div class="listing_table_title">Send Message to Poster</div>
                            <div class="listing_row_alt" id="divYourMessage">
                                <div class="message_label">Your Name</div>
                                <input type="text" name="txtYourName" id="txtYourName" maxlength="100" value="" class="txt_message">
                                <div style="clear:both"></div> 		

                                <div class="message_label">Your Phone Number (optional)</div>
                                <input type="text" name="txtYourPhone" id="txtYourPhone" maxlength="60" value="" class="txt_message">
                                <div style="clear:both"></div> 

                                <div class="message_label">Your Email Address</div>
                                <input type="text" name="txtYourEmail" id="txtYourEmail" maxlength="100" value="" class="txt_message">
                                <div style="clear:both"></div>

                                <textarea cols=39 rows=10 id="txtYourMessage" name="txtYourMessage" wrap="soft" class="txt_normal"></textarea>
                                <div style="clear:both"></div>
                                <div id="charCounter" class="char_count">500</div> 
                                
                                <div id="message_buttons">      
                                    <input type="hidden" id="intYourMessageID" name="intYourMessageID" value="<?php echo $inListingID; ?>">
                                    <input type="submit" class="button" id="btnSendMessage" value="Send">
                                    <input type="reset" class="button" id="btnClearMessage" value="Clear"> 
                                </div>
                            </div>
                        </div>
                    </form>
                    <?php
                        } 
                 
                        if (isAdmin()) {
                            ?>
                            <div class="listing_table">                                        
                                <div class="listing_table_top"></div>
                                <div class="listing_table_title"></div>
                                <div class="listing_row_alt">
                                    Name
                                    <b><?php echo $intOwnerID . " " . $strUserName; ?></b>&nbsp;&nbsp;&nbsp;
                                    <?php if ($booHighlight) { echo "Highlight"; } else { echo "Not highlight"; } ?>
                                </div>
                            </div>
                            <?php
                        }
                        echo "<div style=\"clear:both\" ></div><br>";
                        echo "<div class=\"listing_notes\">";
                        if (($intOwnerID > 0) && ($intOwnerID == $userid)) {
                            echo "<a href=\"openListingForEdit.php?in=" . $inListingID . "\">EDIT</a> ";
                            echo "<a href=\"deleteListing.php?in=" . $inListingID . "\" id=\"linkDeleteListing\">DELETE</a> ";
                        } elseif (isAdmin()) {
                            echo "<a href=\"openListingForEdit.php?in=" . $inListingID . "\">ADJUST</a> ";
                            echo "<a href=\"deleteListing.php?in=" . $inListingID . "\" id=\"linkDeleteListing\">DELETE</a> ";						
                        }

                        echo  "<a href=\"#\" id=\"listing_close\">CLOSE</a></div>";
                    ?>