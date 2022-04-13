<?php 
    include_once "includes/func.php";
    include_once "includes/search.inc.php";    
    
    $page = "weekend.php";
    global $userid;
        
    $inA = "";
    $inB = "";
    $inC = "";
    
    if (isset($_POST["a"])) { $inA = $_POST["a"]; } else if (isset($_GET["a"])) { $inA = $_GET["a"]; } 
    if (isset($_POST["b"])) { $inB = $_POST["b"]; } else if (isset($_GET["b"])) { $inB = $_GET["b"]; } 
    if (isset($_POST["c"])) { $inC = $_POST["c"]; } else if (isset($_GET["c"])) { $inC = $_GET["c"]; }     
    
    // inA must have predefined values: This Weekend or This Week
    if (!(($inA == "This Weekend") || ($inA == "This Week"))) {
        $inA = "";
    }
    
    if (!((is_numeric($inB)) && (is_numeric($inC) || ($inC == "")))) {
        query_error("Shouldn't reach this line 43140", "inB needs to be numeric; inC needs to be blank or numeric.  inB: " . $inB . ", $inC: " . $inC, $page, $userid, true);
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
        <div id="search_column">            
            <?php   
                
                returnListResults($inA, $userid, 0, 10, $page, "date", $inB, $inC, "");
            ?>
        </div>
    </div>
   
    

</div>
<div class="right_column">     
    <div id="divWeather" align="center" style="margin-bottom: 10px; padding: 0px 1px 0px 0px; border-width: 0px 0px 0px 0px; border-style:solid;"></div>
    <div class="twitter_border">
        <script charset="utf-8" src="http://widgets.twimg.com/j/2/widget.js"></script>
        <script>
            new TWTR.Widget({
            version: 2,
            type: 'search',
            search: '#cbwire',
            interval: 4000,
            title: 'Updates From Twitter',
            subject: '',
            width: 231,
            height: 280,
            theme: {
                shell: {
                background: '#FFFFFF',
                color: '#000000'
                },
                tweets: {
                background: '#FFFFFF',
                color: '#000000',
                links: '#000099'
                }
            },
            features: {
                scrollbar: true,
                loop: true,
                live: false,
                behavior: 'default'
            }
            }).render().start();
        </script>
    </div>
    <br>
    <div class="fb-like-box" data-href="http://www.facebook.com/#!/pages/CB-Wire/158827334182841" data-width="234" data-show-faces="false" data-stream="true" data-header="true"></div>
</div>
<?php
    include_once "includes/footer.inc.php";
?>