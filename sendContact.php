<?php 
    include_once "includes/func.php";    
    
    $page = "sendContact.php";
    
    include_once "includes/header.inc.php";    
?>

<div class="page_intro">
    Contact
</div>

<br>               
<div class="section">                    
    <div class="section_heading">
        <div class="section_title">Thank you!</div>
    </div>  
    <div class="section_content">
       <?php include_once "includes/send_contact.php"; ?>
    </div>  
</div>

<?php
    include_once "includes/footer.inc.php";
?>