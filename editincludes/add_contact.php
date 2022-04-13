<?php
    include_once "../includes/connect.php";
    $link = connect();
    $page = "add_contact.php";
    $editListingType = $_SESSION["editListingType"];
    $editListingID = $_SESSION["editListingID"];
    
    $intCommunityID = 0;
    $strOtherCommunity = "";
    $strLocation1 = "";
    $strLocation2 = "";
    $strLocation3 = "";
    $intLinkID = 0;
    $strLink = "";
    $strName = "";
    $strPhone = "";
    $strEmail = "";
    $intHideEmail = 0;
    
    if (isset($_GET['in'])) {
        if (is_numeric($_GET['in'])) {
            $sql_query = sprintf("SELECT c.`id`, c.`communityid`, c.`other_community`, c.`location1`, c.`location2`, c.`location3`, c.`linkid`,
			c.`name`, c.`phone`, c.`email`, c.`hide_email`, l.`title`                        
			from `cbwire`.`contact_b` c 
                        left outer join `listings` l on l.`id` = c.`linkid`
                        where c.`id`=%s and c.`listings_bid`=%s;",
			mysql_real_escape_string($_GET['in']), mysql_real_escape_string($editListingID));

		$result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);						
							
		if ($result_row = mysql_fetch_assoc($result)) {		
			if (!is_null($result_row['name'])) { $strName = $result_row['name']; }					
			if (!is_null($result_row['title'])) { $strLink = $result_row['title']; }
			if (!is_null($result_row['phone'])) { $strPhone = $result_row['phone']; }
			if (!is_null($result_row['email'])) { $strEmail = $result_row['email']; }
			if (!is_null($result_row['hide_email'])) { $intHideEmail = $result_row['hide_email']; }
			if (!is_null($result_row['linkid'])) { $intLinkID = $result_row['linkid']; }
			if (!is_null($result_row['communityid'])) { $intCommunityID = $result_row['communityid']; }
			if (!is_null($result_row['other_community'])) { $strOtherCommunity = $result_row['other_community']; }
			if (!is_null($result_row['location1'])) { $strLocation1 = $result_row['location1']; }
			if (!is_null($result_row['location2'])) { $strLocation2 = $result_row['location2']; }
			if (!is_null($result_row['location3'])) { $strLocation3 = $result_row['location3']; }		
		} 
        }
   }
?>	
                        <div id="add_contactsecnum" class="add_contact">
                            <!-- Dummy Item -->                            
                            <div class="textbox_label"></div>
                            <div style="clear:both"></div> 			

                            <div class="textbox_label">Contact Name</div>
                            <input type="text" name="txtContactNamesecnum" id="txtContactNamesecnum" maxlength="100" value="<?php echo $strName; ?>" class="txt_normal">
                            <div style="clear:both"></div> 
                            <div class="textbox_error" id="txtNameErr"></div>			

                            <div class="textbox_label">Phone Number</div>
                            <input type="text" name="txtPhonesecnum" id="txtPhonesecnum" maxlength="60" value="<?php echo $strPhone; ?>" class="txt_normal">
                            <div style="clear:both"></div> 
                            <div class="textbox_error" id="txtPhoneErrsecnum"></div>

                            <div class="textbox_label">Email Address</div>
                            <input type="text" name="txtEmailsecnum" id="txtEmailsecnum" maxlength="100" value="<?php echo $strEmail; ?>" class="txt_normal"><?php if ($editListingType == "Classified") { ?>&nbsp;&nbsp;<input type="checkbox" name="chkHideEmailsecnum" id="chkHideEmailsecnum" class="chk_normal" <?php if ($intHideEmail == 1) { echo "checked"; }?>> [Keep hidden]<?php } ?>
                            <div style="clear:both"></div>
                            <div class="textbox_error" id="txtEmailErrsecnum"></div>
                            
<?php if (($editListingType == "Directory") || ($editListingType == "Attraction")) { ?>                            
                             <div class="textbox_label" style="z-index: 10;">Link to Directory Listing
                                    <div class="liveLinkSearch" id="liveLinkSearchsecnum" style="z-index: 1;">
                                        <a href="#" id="closeLiveLinkSearchsecnum">X</a><br>
                                        <div id="liveLinkSearchResultssecnum" class="liveLinkSearchResults"></div>
                                    </div>                                   
                             </div>
                            <input type="text" name="txtLinksecnum" id="txtLinksecnum" maxlength="60" value="<?php echo $strLink; ?>" class="txtLink txt_normal">
                            <input type="text" name="txtLinkIDsecnum" id="txtLinkIDsecnum" class="txtLinkID" maxlength="5" value="<?php echo $intLinkID; ?>" size="1" style="display: none;">
                            <div style="clear:both"></div> 
                            <div class="textbox_error" id="txtLinkErrsecnum"></div>	
 
                            <div class="textbox_label">Address / Location</div>
                            <input type="text" name="txtLocation1secnum" id="txtLocation1secnum" maxlength="50" value="<?php echo $strLocation1; ?>" class="txt_normal">
                            <div style="clear:both"></div>
                            <div class="textbox_label"></div>
                            <input type="text" name="txtLocation2secnum" id="txtLocation2secnum" maxlength="50" value="<?php echo $strLocation2; ?>" class="txt_normal">
                            <div style="clear:both"></div>
                            <div class="textbox_label"></div>
                            <input type="text" name="txtLocation3secnum" id="txtLocation3secnum" maxlength="50" value="<?php echo $strLocation3; ?>" class="txt_normal">
                            <div style="clear:both"></div>
                            <div class="textbox_error" id="txtLocationErrsecnum"></div>
<?php } ?> 
<?php if (($editListingType == "Directory") || ($editListingType == "Classified") || ($editListingType == "Attraction")) { ?>
                            <div class="textbox_label">Community</div>         
                            <select id="ddlCommunitysecnum" name="ddlCommunitysecnum" class="ddl_normal">
                                <option value="">-- Select --</option>
                                <?php
                                    // Retrieve info from database
                                    $sql_query = sprintf("SELECT `id`, `name` from `community` order by `name`;");
                                    $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);
                                    
                                    while ($result_row = mysql_fetch_assoc($result)) {  
                                        if ((!is_null($result_row['id'])) && (!is_null($result_row['name']))) {
                                            echo "<option value=\"" . $result_row['id'] . "\"";
                                            if (($intCommunityID > 0) && ($result_row['id'] == $intCommunityID)) { 
                                                echo " selected"; 
                                            }
                                            echo ">" . $result_row['name'] . "</option>";
                                        }
                                    }  
                                ?>  
                                <option value="0"<?php
                                    if (($intCommunityID == 0) && ($strOtherCommunity <> "")) {
                                        echo " selected";
                                    }                                        
                                ?>>Other</option>
                            </select>
                            <div class="textbox_label" id="labelOthersecnum">Please specify:</div>      
                            <input type="text" name="txtOtherCommunitysecnum" id="txtOtherCommunitysecnum" maxlength="50" value="<?php echo $strOtherCommunity; ?>" class="txt_other">
                            <div style="clear:both"></div> 
                            <div class="textbox_error" id="txtCommunityErrsecnum"></div>
<?php } ?> 
<?php if (($editListingType == "Directory") || ($editListingType == "Attraction")) { ?>                        
                            <hr class='seperator'>
<?php } ?> 
                        </div>