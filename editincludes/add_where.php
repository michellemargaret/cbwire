<?php
    include_once "../includes/connect.php";
    $link = connect();
    $page = "add_where.php";
    $editListingType = $_SESSION["editListingType"];
    $editListingID = $_SESSION["editListingID"];
    
    $intCommunityID = 0;
    $strOtherCommunity = "";
    $strLocation1 = "";
    $strLocation2 = "";
    $strLocation3 = "";
    $intLinkID = 0;
    $strLink = "";
    
    if (isset($_GET['in'])) {
        if (is_numeric($_GET['in'])) {
            $sql_query = sprintf("SELECT c.`id`, c.`communityid`, c.`other_community`, c.`location1`, c.`location2`, c.`location3`, 
                        c.`linkid`, l.`title`                        
			from `cbwire`.`contact_b` c 
                        left outer join `listings` l on l.`id` = c.`linkid`
                        where c.`id`=%s and c.`listings_bid`=%s;",
			mysql_real_escape_string($_GET['in']), mysql_real_escape_string($editListingID));

		$result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);						
							
		if ($result_row = mysql_fetch_assoc($result)) {										
			if (!is_null($result_row['title'])) { $strLink = $result_row['title']; }
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
                        <div id="add_locationsecnum" class="add_location">
                            <!-- Dummy Item -->
                            <div class="textbox_label"></div>
                            <div style="clear:both"></div>                             
                            
                             <div class="textbox_label" style="z-index: 10;">Link to Directory Listing
                                    <div class="liveLinkSearch" id="liveLinkSearchWsecnum">
                                        <a href="#" id="closeLiveLinkSearchWsecnum">X</a><br>
                                        <div id="liveLinkSearchResultsWsecnum" class="liveLinkSearchResults"></div>
                                    </div>                                   
                             </div>
                            <input type="text" name="txtLinkWsecnum" id="txtLinkWsecnum" maxlength="60" value="<?php echo $strLink; ?>" class="txtLink txt_normal">
                            <input type="text" name="txtLinkIDWsecnum" id="txtLinkIDWsecnum" class="txtLinkID" maxlength="5" value="<?php echo $intLinkID; ?>" size="1" style="display: none;">
                            <div style="clear:both"></div> 
                            <div class="textbox_error" id="txtLinkErrWsecnum"></div>	
                            
                            <div class="textbox_label">Address / Location</div>
                            <input type="text" name="txtLocation1Wsecnum" id="txtLocation1Wsecnum" maxlength="50" value="<?php echo $strLocation1; ?>" class="txt_normal">
                            <div style="clear:both"></div>
                            <div class="textbox_label"></div>
                            <input type="text" name="txtLocation2Wsecnum" id="txtLocation2Wsecnum" maxlength="50" value="<?php echo $strLocation2; ?>" class="txt_normal">
                            <div style="clear:both"></div>
                            <div class="textbox_label"></div>
                            <input type="text" name="txtLocation3Wsecnum" id="txtLocation3Wsecnum" maxlength="50" value="<?php echo $strLocation3; ?>" class="txt_normal">
                            <div style="clear:both"></div>
                            <div class="textbox_error" id="txtLocationErrWsecnum"></div>
                                                        
                            <div class="textbox_label">Community</div>         
                            <select id="ddlCommunityWsecnum" name="ddlCommunityWsecnum" class="ddl_normal">
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
                            <div class="textbox_label" id="labelOtherWsecnum">Please specify:</div>      
                            <input type="text" name="txtOtherCommunityWsecnum" id="txtOtherCommunityWsecnum" maxlength="50" value="<?php echo $strOtherCommunity; ?>" class="txt_other">
                            <div style="clear:both"></div> 
                            <div class="textbox_error" id="txtCommunityErrWsecnum"></div>
                            
                            <hr class='seperator'>
                        </div>