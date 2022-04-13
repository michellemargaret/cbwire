<?php 
    include_once "includes/func.php";
    include_once "includes/search.inc.php";    
    
    $page = "weekend.php";
        
    $booDoSearch = true;
    $inA = "";
    $inB = "";
    $inC = "";
    
    if (isset($_POST["a"])) { $inA = $_POST["a"]; } else if (isset($_GET["a"])) { $inA = $_GET["a"]; } 
    if (isset($_POST["b"])) { $inB = $_POST["b"]; } else if (isset($_GET["b"])) { $inB = $_GET["b"]; } 
    if (isset($_POST["c"])) { $inC = $_POST["c"]; } else if (isset($_GET["c"])) { $inC = $_GET["c"]; }     
    
    // inA must have predefined values: This Weekend or This Week
    if (!(($inA == "This Weekend") || ($inA == "This Week") || ($inA == "Date Search") || ($inA == "Today") || ($inA == "Tomorrow"))) {
        $inA = "Date Search";
    }
      
    $strSearchFromDate = "";
    $strSearchToDate = "";
    
    $fromY = "";
    $fromM = "";
    $fromD = "";
    
    $toY = "";
    $toM = "";
    $toD = "";
    
    //  Make sure b is a good date in format YYYY-MM-DD
    if (strlen($inB) == 10) {
        $fromY = substr($inB, 0, 4);
        $fromM = substr($inB, 5, 2);
        $fromD = substr($inB, 8, 2);
    }
    
    if (is_numeric($fromY) && (is_numeric($fromM) && (is_numeric($fromD)))) {
        $strSearchFromDate = mktime(0, 0, 0, $fromM, $fromD, $fromY);
    }
    
    //  Make sure b is a good date in format YYYY-MM-DD
    if (strlen($inC) == 10) {
        $toY = substr($inC, 0, 4);
        $toM = substr($inC, 5, 2);
        $toD = substr($inC, 8, 2);
    }
    
    // Make sure c is either blank or a good date in format YYYY-MM-DD
    if (is_numeric($toY) && (is_numeric($toM) && (is_numeric($toD)))) {
        $strSearchToDate = mktime(23, 59, 59, $toM, $toD, $toY);
    } elseif (is_numeric($fromY) && (is_numeric($fromM) && (is_numeric($fromD)))) {
        // To Date not valid; search based on From date
        $strSearchToDate = mktime(23, 59, 59, $fromM, $fromD, $fromY);
    }
    
   
    if (!((is_numeric($strSearchFromDate)) && (is_numeric($strSearchToDate) || ($strSearchToDate == "") || ($strSearchFromDate == "")))) {        
        $booDoSearch = false;
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
    <a href="../index.php">Home</a> >> <a href="../things.php">Things To Do</a> >>
</div>
<div class="page_section">    
    <div class="dataColumn">  
        <br>
        <form id="mainsearch_form" name="mainsearch_form" method="get" action="" enctype="multipart/form-data">
            <input type="hidden" name="a" id="a" value="Date Search">
            Return listings scheduled from
            <input type="text" name="b" id="b" value="<?php echo $inB; ?>" maxlength="10" class="txt_date">
            to
            <input type="text" name="c" id="c" value="<?php echo $inC; ?>" maxlength="10" class="txt_date">
            <button class="button2">Search</button>     
        </form>
    </div>
    <div style="clear:both"></div>
    <div class="dataColumn">  
        <br>
        <div id="search_column">            
            <?php    
                if ($booDoSearch) {
                    returnListResults($inA, get_user_id(), 0, 10, $page, "date", $strSearchFromDate, $strSearchToDate, "");
                } else {
                    echo "<div class=\"textbox_error\">There was an error with the dates.<br>Please make sure you enter a From Date,<br>and use the format YYYY-MM-DD</div>";
                }
            ?>
        </div>
    </div>
   
    

</div>

<?php 
    include_once "right_column.php";
    include_right_column(true);
?>
<?php
    include_once "includes/footer.inc.php";
?>