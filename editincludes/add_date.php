<?php
    include_once "../includes/connect.php";
    $link = connect();
    $page = "add_date.php";
    $editListingType = $_SESSION["editListingType"];
    $editListingID = $_SESSION["editListingID"];
    
    
    $strStartDate = "";
    $strEndDate = "";
    $strStartTime = "";
    $strStartAM = "A";
    $strEndTime = "";
    $strEndAM = "A";
    $strRecursive = "";
    $strExpiry = "";

    if (isset($_GET['in'])) {
        if (is_numeric($_GET['in'])) {
            
            $sql_query = sprintf("SELECT id, start_date, start_time, end_time, end_date, recursive, expiry " .
                                                            "from `cbwire`.`when_b` where `id`=%s and `listing_bid`=%s;",
                    mysql_real_escape_string($_GET['in']), mysql_real_escape_string($editListingID));

            $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);
            
            if ($result_row = mysql_fetch_assoc($result)) {						
                if (!is_null($result_row['start_date'])) { 
                        $strTemp = $result_row['start_date']; 
                        if ($strTemp <> "") {
                                $strStartDate = date("Y-m-d", $strTemp);
                        }
                }					
                if (!is_null($result_row['end_date'])) { 
                        $strTemp = $result_row['end_date']; 
                        if ($strTemp <> "") {
                                $strEndDate = date("Y-m-d", $strTemp);
                        }
                }
                if (!is_null($result_row['start_time'])) { 
                        $strTemp = $result_row['start_time']; 
                        if ($strTemp <> "") {
                                $strStartTime = date("g:i", $strTemp);
                                if (date("A", $strTemp) == "PM") {
                                        $strStartAM = "P";
                                } else {
                                        $strStartAM = "A";
                                }
                        }								
                }
                if (!is_null($result_row['end_time'])) { 
                        $strTemp = $result_row['end_time']; 
                        if ($strTemp <> "") {
                                $strEndTime = date("g:i", $strTemp);
                                if (date("A", $strTemp) == "PM") {
                                        $strEndAM = "P";
                                } else {
                                        $strEndAM = "A";
                                }
                        }								
                }					
                if (!is_null($result_row['recursive'])) { 
                        $strRecursive = $result_row['recursive']; 
                }					
                if (!is_null($result_row['expiry'])) { 
                        $strExpiry = substr($result_row['expiry'], 0, 10);
                        if ($strExpiry == "0000-00-00") { $strExpiry = ""; }
                }					
            } 
        }
    }
?>

<div id="add_datesecnum" class="add_date">
    <div id="copydatesecnum" class="copy_date" align="center">
        <a href="#" id="copyPreviousDate">Copy Down</a>
    </div>
    <div class="textbox_labelinline">On</div>
    <div class="textbox_div"> <input type="text" name="txtStartDatesecnum" id="txtStartDatesecnum" value="<?php echo $strStartDate; ?>" maxlength="10" class="txt_date"></div>   
    
    <div class="textbox_labelinline">From</div>
    <div class="textbox_div">
        <input type="text" maxlength="5" name="txtStartTimesecnum" id="txtStartTimesecnum" value="<?php echo $strStartTime; ?>" class="txt_time">
        <select id="ddlStartAMsecnum" name="ddlStartAMsecnum"><option value="A"<?php if ($strStartAM == "A") { echo " selected"; } ?>>AM</option><option value="P"<?php if ($strStartAM == "P") { echo " selected"; } ?>>PM</option></select>
    </div>
    <div class="textbox_labelinline">To</div>
    <div class="textbox_div">
        <input type="text" maxlength="5" name="txtEndTimesecnum" id="txtEndTimesecnum" value="<?php echo $strEndTime; ?>" class="txt_time">
        <select id="ddlEndAMsecnum" name="ddlEndAMsecnum"><option value="A"<?php if ($strEndAM == "A") { echo " selected"; } ?>>AM</option><option value="P"<?php if ($strEndAM == "P") { echo " selected"; } ?>>PM</option></select>
    </div>
    
    <div class="textbox_labelinline">Repeating</div>
    <div class="textbox_div"><select id="ddlRecurrancesecnum" name="ddlRecurrancesecnum" class="ddlRecurrance">
                                <option value="none"<?php if ($strRecursive == "none") { echo " selected"; } ?>>No</option>
                                <option value="day"<?php if ($strRecursive == "day") { echo " selected"; } ?>>Daily</option>
                                <option value="week"<?php if ($strRecursive == "week") { echo " selected"; } ?>>Weekly</option>
                                <option value="month"<?php if ($strRecursive == "month") { echo " selected"; } ?>>Monthly</option>
                            </select></div>
        
    <div class="textbox_labelinline" id="label_untilsecnum">Until</div>
    <div class="textbox_div"> <input type="text" name="txtExpirysecnum" id="txtExpirysecnum" value="<?php echo $strExpiry; ?>" maxlength="10" class="txt_date"></div>   

    <div style="clear:both"></div>
    <div class="textbox_error" id="txtDateErrsecnum"></div>   
    <div class="textbox_notes" id="txtDateSamplesecnum"></div>   
    
    <hr class='seperator'>
</div>	