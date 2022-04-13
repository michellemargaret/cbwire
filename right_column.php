<?php   
    include_once "inc/func.inc.php"; 
    
    $page = "right_column.php";
    
    function include_right_column($showSearch) {
?>

<div class="right_column">   
    <?php if ($showSearch == true) { ?>  
        <form id="RightSearchForm" name="RightSearchForm" method="post" action="listall.php?a=search" enctype="multipart/form-data">
            <input type="text" name="RightSearchTextbox" id="RightSearchTextbox" maxlength="50" value="">
            <input type="hidden" id="searchType" name="searchType" value="basic">
            <button>Search</button>     
        </form>  
    <div style="height:10px"></div>
    <?php } ?>
    
    <div id="Right_Login_Panel">
    <?php
        if (isLoggedIn() === false) {
            include_once ("login_panel.php");            
        } else {
            include_once ("loggedin_panel.php");
        } 
    ?>
    </div>
    
    <div id="Right_Advertise">
        <a href="mailto:advertise@cbwire.ca" title="Email:advertise@cbwire.ca">
            Your<br>
            customers<br>
            are<br>
            reading<br>
            this<br>
            too.
            <div style="height: 400px"></div>
            advertise@cbwire.ca
        </a>
    </div>
    
</div>

<?php } ?>

