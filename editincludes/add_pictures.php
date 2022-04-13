<?php
    include_once "../includes/func.php";
    $link = connect();
    $page = "add_pictures.php";
    $userid = get_user_id(); 
    
    if ($userid > 0) {    
        $sql_query = sprintf("SELECT `id`, `thumbnail`, `smallWidth`, `smallHeight`
                                from `cbwire`.`pictures` 
                                where `userid` = %s
                                order by 
                                `insertDate` desc;", $userid);
        $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);					

        if (mysql_num_rows($result) > 0) { 
            echo "<div class=\"textbox_label\" style=\"margin-top: 15px;\">or select picture:</div>";
            echo "<div class=\"list_images\">";

            while ($result_row = mysql_fetch_assoc($result)) {
                    $strThumbnail = "";
                    $intThisID = 0;

                    if (!is_null($result_row['id'])) { $intThisID = $result_row['id']; }
                    if (!is_null($result_row['thumbnail'])) { $strThumbnail = $result_row['thumbnail']; }

                    if (file_exists("../uploads/" . $strThumbnail)) {
                        echo "<a href=\"#\" id=\"savedPicture" . $intThisID . "\" class=\"savedPicture\"><img src=\"uploads/" . $strThumbnail . "\"></a> ";
                    }
            }

            echo "  </div>
                <div style=\"clear:both\"></div>";
        }
    }  
?>