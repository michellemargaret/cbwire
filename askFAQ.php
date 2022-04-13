<?php 
    include_once "includes/func.php";    
    
    $page = "askFAQ.php";
    
    include_once "includes/header.inc.php";    
?>
<div class="breadcrumbs"><a href="index.php">Home</a> >></div>
<div class="page_intro_2">
    Frequently Asked Questions
</div>

<br>               
<div class="section">                    
    <div class="section_heading">
        <div class="section_title">Thank you!</div>
    </div>  
    <div class="section_content">
       <?php include_once "includes/ask_faq.php"; ?>
    </div>  
</div>

<?php
    include_once "includes/footer.inc.php";
?>