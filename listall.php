<?php 
    include_once "includes/func.php";
    include_once "includes/search.inc.php";    
    
    $page = "listall.php";
    $userid = get_user_id();
    
    $inA = "";
    $inB = "";
    $inC = "";
    $inFilter = "";
    
    $showTowns = false;
    $showCats = false;
    
    $strSearchingFor = "";
    $strLastPageTitle = "";
    $strSection = "";
    
    if (isset($_POST["a"])) { $inA = $_POST["a"]; } else if (isset($_GET["a"])) { $inA = $_GET["a"]; } 
    if (isset($_POST["b"])) { $inB = $_POST["b"]; } else if (isset($_GET["b"])) { $inB = $_GET["b"]; } 
    if (isset($_POST["c"])) { $inC = $_POST["c"]; } else if (isset($_GET["c"])) { $inC = $_GET["c"]; }     
    
    if ($inB == "town") { $showTowns = true; } elseif ($inB == "cat") { $showCats = true; }
    
    if (($inC == "") or !is_numeric($inC)) {
        $inC = "";
        $inB = "";
    }
        
    $sql_query = ""; 
    
    switch ($inA) {
        case "things":
            $strSearchingFor = "Things To Do";
            $inA = "thingstodo";
            break;
        case "directory":
            $strSearchingFor = "Directory Listings";
            break;
        case "classifieds":
            $strSearchingFor = "Classifieds";
            break;
        case "attractions":
            $strSearchingFor = "Attractions";
            break;
        case "attraction":
            $strSearchingFor = "Attractions";
            $inA = "attractions";
            break;
        case "search":
            $strSearchingFor = "Search";
            break;
        default:
            log_error("Shouldn't reach this line 43190", "inA not recognized: " . $inA, $page, true);
            exit();
    }    
    
    if (($inA == "thingstodo") || ($inA == "attractions") || ($inA == "classifieds") || ($inA == "directory")) {
        $strSection = $inA;
    }
   
    $sql_query = "";

    if (isset($_POST["txtMainSearch"])) { 
        $inFilter = $_POST["txtMainSearch"];
    } elseif (isset($_POST["RightSearchTextbox"])) {
        $inFilter = $_POST["RightSearchTextbox"];
    }
    
    if ($strSearchingFor <> "Search") {
        $strSearchingFor = "Search : " . $strSearchingFor;
    }
    
    if (($inB <> "") && ($inC <> "")) {
        switch ($inB) {
            case "town":
                $sql_query = sprintf("
                SELECT `name` as title
                    from `cbwire`.`community` 
                    WHERE `id` = %s;",
                mysql_real_escape_string($inC));
                break;
            case "cat":
                $sql_query = sprintf("
                SELECT `title`
                    from `cbwire`.`categories` 
                    WHERE `id` = %s;",
                mysql_real_escape_string($inC));
                break;
            default:
                $inB = "";
                $inC = "";
                break;
        }
        
        if (($inB <> "") && ($inC <> "")) {
            $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, true);

            if (mysql_num_rows($result) > 0) {
                $result_row = mysql_fetch_assoc($result);                        
                if (!is_null($result_row['title'])) { if ($result_row['title'] <> "") { $strSearchingFor = $strSearchingFor . " : " . $result_row['title']; $strLastPageTitle = $result_row['title']; } }
            }
        }
    } 
        
    if ($inFilter <> "") {
        $strSearchingFor = $strSearchingFor . " : \"" . $inFilter . "\"";
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
<div class="breadcrumbs">
    <a href="index.php">Home</a> >>
    <?php
        if (($inB == "cat") && ($strLastPageTitle <> "") && is_numeric($inC) && ($strSection == ""))  {
            // Figure out section from category            
                $sql_query = sprintf("
                SELECT `activities`, `classifieds`, `attractions`, `directory`
                    from `cbwire`.`categories` 
                    WHERE `id` = %s;",
                mysql_real_escape_string($inC));                
                
                $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);
                $result_row = mysql_fetch_assoc($result);       
                if (!is_null($result_row['activities'])) { if ($result_row['activities'] == 1) { $strSection = "thingstodo"; } }
                if (!is_null($result_row['classifieds'])) { if ($result_row['classifieds'] == 1) { $strSection = "classifieds"; } }
                if (!is_null($result_row['attractions'])) { if ($result_row['attractions'] == 1) { $strSection = "attractions"; } }
                if (!is_null($result_row['directory'])) { if ($result_row['directory'] == 1) { $strSection = "directory"; } }
        }
    
        if ($strSection == "thingstodo") {
            echo "<a href=\"things.php\">Things To Do</a> >> ";
        } else if ($strSection == "directory") {
            echo "<a href=\"directory.php\">Directory</a> >> ";
        } else if ($strSection == "attractions") {
            echo "<a href=\"attractions.php\">Attractions</a> >> ";
        } else if ($strSection == "classifieds") {
            echo "<a href=\"listall.php?a=classifieds \">Classifieds</a> >> ";
        }
        if (($inB == "town") && ($strLastPageTitle <> "") && (is_numeric($inC)))  {
            echo "<a href=\"town.php\">Towns</a> >> <a href=\"town.php?in=" . $inC . "\">" . $strLastPageTitle . "</a> >>";
        } else if (($inB == "cat") && ($strLastPageTitle <> "") && (is_numeric($inC)))  {
            echo "<a href=\"cat.php\">Categories</a> >> <a href=\"cat.php?in=" . $inC . "\">" . $strLastPageTitle . "</a> >>";
        } 
    ?>
</div>
<div class="page_section">    
    <div class="dataColumn">
        <br><br>
        <form id="mainsearch_form" name="mainsearch_form" method="post" action="listall.php?a=search" enctype="multipart/form-data">
            <input type="text" name="txtMainSearch" id="txtMainSearch" maxlength="20" value="<?php echo $inFilter; ?>" class="txt_login">
            <?php
                echo "<select id=\"a\" name=\"a\" class=\"ddl_normal\">
                    <option value=\"search\">- All Sections -</option>
                    <option value=\"things\"";  if (($strSection=="thingstodo") || ($strSection =="activities")) { echo " selected"; } echo ">Things To Do</option>
                    <option value=\"directory\"";  if ($strSection=="directory") { echo " selected"; } echo ">Directory</option>
                    <option value=\"attractions\"";  if ($strSection=="attractions") { echo " selected"; } echo ">Attractions</option>
                    <option value=\"classifieds\"";  if ($strSection=="classifieds") { echo " selected"; } echo ">Classifieds</option>
                    </select>
                    ";
           
                if (($showTowns) || ($strSection == "")) {
                    $inTown = $inC;
                    if (!is_numeric($inTown)) { $inTown = 0; }
                    ?>      <input type="hidden" id="b" name="b" value="town">
                            <select id="c" name="c" class="ddl_normal">
                                <option value="">- All Towns -</option>
                                <?php
                                    // Retrieve info from database
                                    $sql_query = sprintf("SELECT `id`, `name` from `community` order by `name`;");
                                    $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);
                                    
                                    while ($result_row = mysql_fetch_assoc($result)) {  
                                        if ((!is_null($result_row['id'])) && (!is_null($result_row['name']))) {
                                            echo "<option value=\"" . $result_row['id'] . "\"";
                                            if (($inTown > 0) && ($result_row['id'] == $inTown)) { 
                                                echo " selected"; 
                                            }
                                            echo ">" . $result_row['name'] . "</option>";
                                        }
                                    }  
                                ?>                                  
                            </select>
                    <?php
                } else if ($strSection <> "") {
                    $inCat = $inC;
                    
                    if (!is_numeric($inCat)) { $inCat = 0; }
                    if ($strSection == "thingstodo") { $strSection = "activities"; }
                    if (($strSection == "activities") || ($strSection == "directory") || ($strSection == "attractions")) {
                    ?>      <input type="hidden" id="b" name="b" value="cat">
                            <select id="c" name="c" class="ddl_normal">
                                <option value="">- All Categories -</option>
                                <?php
                                    // Retrieve info from database
                                    $sql_query = sprintf("SELECT c.`id`, c.`title`, ifnull(c.`parentid`, 0) as parentid,
                                                            ifnull(c2.`title`, c.`title`) as parenttitle
                                                            from `categories` c 
                                                            left outer join `categories` c2 on c.`parentid` = c2.`id`
                                                            where c.`active`=1 and c.`%s`=1
                                                            order by ifnull(c2.title,c.title) = \"Other\", ifnull(c2.title,c.title), c.parentid, (c.parentid!=c.id), c.title = \"Other\", c.title;",
                                                            mysql_real_escape_string($strSection));
                                    $result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);
                                    
                                    while ($result_row = mysql_fetch_assoc($result)) {  
                                        if ((!is_null($result_row['id'])) && (!is_null($result_row['title']))) {
                                            echo "<option value=\"" . $result_row['id'] . "\"";
                                            if (($inCat > 0) && ($result_row['id'] == $inCat)) { 
                                                echo " selected"; 
                                            }
                                            echo ">";
                                            if ((!is_null($result_row['parentid'])) && (!is_null($result_row['id']))) {
                                                if (($result_row['parentid'] > 0) && ($result_row['parentid'] <> $result_row['id'])) {
                                                    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                                                } 
                                            }
                                            echo $result_row['title'] . "</option>";
                                        }
                                    }  
                                ?>                                  
                            </select>
                    <?php                        
                    }
                    
                }
                ?>
            <button class="button2" id="btnHomeSearch" name="btnHomeSearch">Search</button>     
        </form>
    </div>
    <div style="clear:both"></div>
    <div class="dataColumn">  
        <br>
        <div id="search_column">            
            <?php 
                returnListResults($strSearchingFor, $userid, 0, 10, $page, $inA, $inB, $inC, $inFilter); 
           // returnListResults("Test", 0, 110, 10, 12, "directory", "town", "12");
            ?>
        </div>
    </div>
   
    

</div>

<?php 
    include_once "right_column.php";
    include_right_column(false);
?>
<?php
    include_once "includes/footer.inc.php";
?>