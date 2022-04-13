<?php 
    include_once "includes/func.php";    
    
    $page = "faq.php";
    
    include_once "includes/header.inc.php";    
?>
<div class="breadcrumbs"><a href="index.php">Home</a> >></div>
<div class="page_intro_2">
    Frequently Asked Questions
</div>

<br>               
<div class="section">                    
    <div class="section_heading">
        <div class="section_title">No one asked that!</div>
    </div>  
    <div class="section_content">
        <p>Don't you hate those FAQ sections filled with a bunch of questions that you <i>know</i> have never really been asked?</p>
        
        <p>Me too.  So this is not one of those.</p>
        
        <p>This section needs questions to <i>actually get asked</i> before they get posted.</p>
    </div>    
                   
    <div class="section_heading">
        <div class="section_title">So just ask.</div>
    </div>  
    <div class="section_content">
        <p>Go ahead and ask whatever question you were hoping this page could answer.</p>
        
        <p>Email is optional but will get your answer delivered.  All cbwire.ca-appropriate questions will be posted on this page without identifying who asked.</p>        
          
        <form id="faq_form" name="faq_form" method="post" action="askFAQ.php" enctype="multipart/form-data">
            <div class="textbox_label">Email Address (optional)</div>
            <input type="text" name="txtEmailFAQ" id="txtEmailFAQ" maxlength="100" value="" class="txt_normal">
            <div style="clear:both"></div>
            <div class="textbox_error" id="txtEmailErrFAQ"></div>      

            <div class="textbox_label">Question</div>
            <input type="text" name="txtQuestionFAQ" id="txtQuestionFAQ" maxlength="100" value="" class="txt_normal">
            <div style="clear:both"></div>
            <div class="textbox_error" id="txtQuestionErrFAQ"></div>

            <div class="textbox_label"></div>
            <button class="button" id="btnAskFAQ" name="btnAskFAQ" type="submit">Ask</button>  
            <div style="clear:both"></div>
            <div class="textbox_error"></div>
        </form>
    </div>
</div>

<?php
    include_once "includes/footer.inc.php";
?>