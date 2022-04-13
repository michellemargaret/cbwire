<?php 
    include_once "includes/func.php";
    $userid = get_user_id();
   
    include_once "includes/search.inc.php";
        
    $page = "printsearch.php";
        
    $inFilter = "";
    $inGrid = "";
    $isAdmin = isAdmin();
    
    $inTitle = "";
    $inCurrentPage = 1;
    $inLimit = 10;
    $inFrom = 0; 
    $inSearch1 = "";
    $inSearch2 = "";
    $inSearchSection = "";
    $inSearchType = "";
    $inSearchValue = "";
    
    $owner = 0;
  
    if (isset($_GET["in"])) { $inGrid = $_GET["in"]; }
   
                
    if ($inGrid == "seepost") {       
        if (isset($_POST["title"])) { $inTitle = $_POST["title"]; } 
        switch ($inTitle) {
            case "Awaiting Approval":
                $inGrid = "sub";
                break;
            case "Not Published":
                $inGrid = "notpub";
                break;
            case "Published":
                $inGrid = "pub";
                break;
            case "Matches":
                $inGrid = "search";
                break;
            case "Age Search":
                $inGrid = "age";
                break;
            case "Category Search":
                $inGrid = "cat";
                break;
            case "Basic Search":
                $inGrid = "basic";
                break;
            case "Town Search":
                $inGrid = "town";
                break;
            case "Classifieds":
                $inGrid = "classifieds";
                break;
            case "Things To Do":
                $inGrid = "things";
                break;
            case "Directory":
                $inGrid = "directory";
                break;
            case "Attractions":
                $inGrid = "attractions";
                break;
            default:
                $inGrid = "listall";
                break;
        }
              
        if (isset($_POST["current"])) { $inCurrentPage = intval($_POST["current"]); } 
        if (isset($_POST["limit"])) { $inLimit = intval($_POST["limit"]); } 
        if (isset($_POST["from"])) { $inFrom = intval($_POST["from"]); }   
        if (isset($_POST["filter"])) { $inFilter = $_POST["filter"]; } 
        if (isset($_POST["search1"])) { $inSearch1 = $_POST["search1"]; } 
        if (isset($_POST["search2"])) { $inSearch2 = $_POST["search2"]; }
        if (isset($_POST["searchsection"])) { $inSearchSection = $_POST["searchsection"]; }
        if (isset($_POST["searchtype"])) { $inSearchType = $_POST["searchtype"]; }
        if (isset($_POST["searchvalue"])) { $inSearchValue = $_POST["searchvalue"]; }

    } else {
        if (isset($_POST["txtFilterOwn"])) { $inFilter = $_POST["txtFilterOwn"]; }
    }
    
    switch ($inGrid) {
        case "search":
            returnQuickSearch("Basic Search", $userid, $inFrom, $inLimit, $page, $inSearch1);
            break;
        case "sub":
            if ($isAdmin) {     
                returnAdminSubmitted("Awaiting Approval", $inFrom, $inLimit, $page, $inFilter); 
            }
            break;
        case "notpub":
            if ($isAdmin) {
                returnAdminNonPublished("Not Published", $userid, $inFrom, $inLimit, $page, $inFilter);
            } else {
                returnGeneralNonPublished("Not Published", $userid, $inFrom, $inLimit, $page, $inFilter);
            }
            break;
        case "pub":
            if ($isAdmin) {
                returnAdminPublished("Published", $inFrom, $inLimit, $page, $inFilter);
            } else {
                returnGeneralPublished("Published", $userid, $inFrom, $inLimit, $page, $inFilter);
            } 
            break;
        case "age":
            returnAgeSearch("Age Search", $userid, $inFrom, $inLimit, $page, $inSearch1);
            break;
        case "cat":
            returnCategorySearch("Category Search", $userid, $inFrom, $inLimit, $page, $inSearch1);
            break;
        case "basic":
            returnQuickSearch("Basic Search", $userid, $inFrom, $inLimit, $page, $inSearch1);
            break;
        case "town":
            returnTownSearch("Town Search", $userid, $inFrom, $inLimit, $page, $inSearch1);
            break;
        case "classifieds":
            returnClassifieds("Classifieds", $userid, $inFrom, $inLimit, $page); 
            break;
        case "things":
            returnListResults($inTitle, $userid, $inFrom, $inLimit, $page, $inSearchSection, $inSearchType, $inSearchValue, $inSearch1);
            break;
        case "directory":
            returnListResults($inTitle, $userid, $inFrom, $inLimit, $page, $inSearchSection, $inSearchType, $inSearchValue, $inSearch1);
            break;
        case "attractions":
            returnListResults($inTitle, $userid, $inFrom, $inLimit, $page, $inSearchSection, $inSearchType, $inSearchValue, $inSearch1);
            break;
        case "listall":
            returnListResults($inTitle, $userid, $inFrom, $inLimit, $page, $inSearchSection, $inSearchType, $inSearchValue, $inSearch1);
            break;
    }
    
     
            
    
    
    
    

?>