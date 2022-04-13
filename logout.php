<?php 
    include_once "includes/func.php";    
    
    $page = "logout.php";
    
    logout();    
      
    if (isLoggedIn() === false) {
        header( 'Location: index.php' );
        exit();
    }
    
    include_once "includes/header.inc.php";    
?>                
    <div class="breadcrumbs"><a href="index.php">Home</a> >></div>
    <div class="page_intro_2">
        Logout
    </div>
    <br>               
    <div class="page">                    
        <div class="section_heading">
            <div class="section_title">Logout successful.</div>   
            <div class="section_notes"></div>
        </div>
        <div class="section_content">
            Come back soon!
        </div>        
    </div>
    

<?php
    include_once "includes/footer.inc.php";
?>    