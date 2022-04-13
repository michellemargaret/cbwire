<?php    
    include_once "func.php";
    include_once "searchfunc.inc.php";   
    
    $page = "justadded.inc.php";

    $result = returnLastestSearch($page, 8);
    
    if (get_resource_type($result) == "mysql result") {
          // Print Results
          echo "<h1>Just Added</h1><br>";
          showListingsTitleOnly($page, $result);
    } else {
          echo "Thanks for stopping by!";
    }
?>