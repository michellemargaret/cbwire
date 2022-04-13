<?php 
    include_once "includes/func.php"; 
    include_once "includes/search.inc.php";
    
    $page = "search.php";
    $searchType = "word";
    $userid = get_user_id();
    if (isset($_GET["in"])) { $searchType = $_GET["in"]; }  
    
    $searchType = "";
    $searchFor1 = "";
    $searchFor2 = "";
    $section = "";
    $strWhere = " and 1=1";
    
    $intThingsToDo = 0;
    $intDirectory = 0;
    $intClassifieds = 0;
    $intAttractions = 0;
    
    if (isset($_GET["in"])) { $searchType = $_GET["in"]; }
    if (isset($_GET["a"])) { $searchFor1 = $_GET["a"]; }
    if (isset($_GET["b"])) { $searchFor2 = $_GET["b"]; }
    if (isset($_GET["section"])) { $section = $_GET["section"]; }
    
    if ($section == "things") {
        $strWhere = " and `activities`=1";
        $intThingsToDo = 1;
    } elseif ($section == "directory") {
        $strWhere = " and `directory`=1";
        $intDirectory = 1;
    } elseif ($section == "classifieds") {
        $strWhere = " and `classifieds`=1";
        $intClassifieds = 1;
    } elseif ($section == "attractions") {
        $strWhere = " and `attractions`=1";
        $intAttractions = 1;
    }
    
    $strSearchFDate = "";
    if (isset($_POST["txtSearchFDate"])) { $strSearchFDate = $_POST["txtSearchFDate"]; } else { $strSearchFDate = $searchFor1; }
    
    $strSearchTDate = "";
    if (isset($_POST["txtSearchTDate"])) { $strSearchTDate = $_POST["txtSearchTDate"]; } else { $strSearchTDate = $searchFor2; }
    
    if (isset($_POST["chkSearchSection"])) {
        $strWhere = "";
        $strTemp = implode(",", $_POST["chkSearchSection"]);
        if (strpos($strTemp, "things") !== false) { $strWhere = $strWhere . " or `activities`=1 "; }
        if (strpos($strTemp, "directory") !== false) { $strWhere = $strWhere . " or `directory`=1 "; }
        if (strpos($strTemp, "classifieds") !== false) { $strWhere = $strWhere . " or `classifieds`=1 "; }
        if (strpos($strTemp, "attractions") !== false) { $strWhere = $strWhere . " or  `attractions`=1 "; }
        
        if ($strWhere <> "") { $strWhere = sprintf(" and (0=1 %s) ", $strWhere); } else { $strError = "Choose a section and then choose categories.<br>"; $strWhere = " 1=1"; }
    }
    
    $strSearchMCat = "";
    if (isset($_POST["chkSearchMCat"])) {
        $strSearchMCat = "0," . implode(",", $_POST["chkSearchMCat"]) . ",0";
    }
    
    $strSearchSCat = "";
    if (isset($_POST["chkSearchSCat"])) {
        $strSearchSCat = "0," . implode(",", $_POST["chkSearchSCat"]) . ",0";
    } elseif ($searchFor1 <> "") {
        $strSearchSCat = "0," . $searchFor1 . ",0";
    }
    
    $strSearchAge = "";
    if (isset($_POST["chkSearchAge"])) {
        $strSearchAge = "0," . implode(",", $_POST["chkSearchAge"]) . ",0";
    } elseif ($searchFor1 <> "") {
        $strSearchAge = "0," . $searchFor1 . ",0";
    }
    
    $strSearchTown = "";
    if (isset($_POST["chkSearchTown"])) {
        $strSearchTown = "0," . implode(",", $_POST["chkSearchTown"]) . ",0";
    } elseif ($searchFor1 <> "") {
        $strSearchTown = "0," . $searchFor1 . ",0";
    }
    
    $strMainSearch = "";
    if (isset($_POST["txtMainSearch"])) {
        $strMainSearch = $_POST["txtMainSearch"];
    }
        
    include_once "includes/header.inc.php";    
?><div id="fb-root"></div>
<script>(function(d, s, id) {
var js, fjs = d.getElementsByTagName(s)[0];
if (d.getElementById(id)) return;
js = d.createElement(s); js.id = id;
js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<div class="breadcrumbs"><a href="index.php">Home</a> >> <a href="search.php">Search</a> >></div>
                
                
<?php 
    include_once "right_column.php";
    include_right_column(true);
?>
                <div class="search_column">    
                    <div class="alternate_links">
                        Search By
                        <ul>
                            <li><?php if ($searchType == "word") { echo "Basic"; } else { echo "<a href=\"search.php?in=word\">Basic</a>"; } ?></li>
                            <li><?php if ($searchType == "date") { echo "Date"; } else { echo "<a href=\"datesearch.php\">Date</a>"; } ?></li>
                            <li><?php if ($searchType == "age") { echo "Age"; } else { echo "<a href=\"search.php?in=age\">Age</a>"; } ?></li>
                        </ul>
                    </div>                    
                </div>
                <div class="search_column">    
                    <?php include_once "search_form.php"; ?>    
                    <div style="clear:both"></div>
                </div>
                <div id="search_column" class="search_column"> 
                    <?php    
                        if (isset($_POST["searchType"])) { $searchType = $_POST["searchType"]; }                        
                        
                        switch ($searchType) {
                            case "date":                                
                                if (($strSearchFDate <> "") || ($strSearchTDate <> "")) {
                                    returnDateSearch("Date Search", $userid, 0, 10, $page, $strSearchFDate, $strSearchTDate);
                                } else {
                                    echo "<div class=\"textbox_error\">Please enter a date and try again.</div>";
                                }
                                break;
                            case "category": 
                                if (((isset($_POST["chkSearchSCat2"])) && ($strSearchMCat <> "")) || ($searchFor1 <> "")) {
                                    if ($strSearchSCat <> "") {
                                        returnCategorySearch("Category Search", $userid, 0, 10, $page, $strSearchSCat);
                                    } else {
                                        echo "<div class=\"textbox_error\">Please choose sub-categories and try again.</div>";                                    
                                    }
                                }
                                break;
                            case "age":   
                                if (($strSearchAge <> "") && ($strSearchAge <> "0,,0")) {
                                    returnAgeSearch("Age Search", $userid, 0, 10, $page, $strSearchAge);    
                                } else {
                                    echo "<div class=\"textbox_error\">Please choose ages and try again.</div>";                                    
                                }                            
                                break;
                            case "town":        
                                if ($strSearchTown <> "") {
                                    returnTownSearch("Town Search", $userid, 0, 10, $page, $strSearchTown);
                                } else {
                                    echo "<div class=\"textbox_error\">Please choose towns and try again.</div>";                                    
                                }
                                break;
                            default:
                                if ($strMainSearch <> "") {                                
                                    returnQuickSearch("Basic Search", $userid, 0, 10, $page, $strMainSearch);  
                                } else {
                                    echo "<div class=\"textbox_error\">Please enter search text and try again.</div>";                                    
                                }                              
                                break;
                        }
                    ?>
                </div>
                    <?php
    include_once "includes/footer.inc.php";
?>