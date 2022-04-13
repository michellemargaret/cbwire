<?php
    include_once "../includes/connect.php";
    $link = connect();
    $page = "live_link_search.php";
    
    $search = 0;
    
    if (isset($_GET['in'])) {
       $search = urldecode($_GET['in']);
    }
    if ($search <> "") {
        

        $sql_query = sprintf("SELECT l.id, l.title, (SELECT GROUP_CONCAT(distinct com.name SEPARATOR ', ') FROM contact contact 
                                        inner join community com on contact.communityid = com.id 
                                        WHERE contact.listingsid = l.id and com.name <> '') group1 from listings l
                                where l.deleted = 0 and (l.attractions=1 or l.directory=1) 
                                and UPPER(l.title) like '%s'
                                order by l.title;",
			mysql_real_escape_string("%" . $search . "%"));

        $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);	
        
        if (mysql_num_rows($result) == 0) { 
                echo "No matches.";
        } else {
            $intCount = 0;
            echo "<b>Click the listing you wish to link</b><br>";
            while (($result_row = mysql_fetch_assoc($result)) && ($intCount < 10)) {
                    $strTitle = "";
                    $strCommunity = "";
                    $intThisID = 0;
                    $intDirectory = 0;

                    if (!is_null($result_row['id'])) { $intThisID = $result_row['id']; }
                    if (!is_null($result_row['title'])) { $strTitle = $result_row['title']; }
                    if (!is_null($result_row['group1'])) { $strCommunity = $result_row['group1']; }
                    
                    if ($strCommunity <> "") { $strCommunity = " (" . $strCommunity . ")"; }
                    
                    echo "<a href=\"#\" id=\"livelink" . $intThisID . "\">" . $strTitle . $strCommunity . "</a>";
                    
                    $intCount++;

            }
            
            if (mysql_num_rows($result) > 10) {
                echo "&nbsp;&nbsp;&nbsp; ......... <br>";
            }
        }  
    }
    			
?>