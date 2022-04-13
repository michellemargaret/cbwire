<?php 
    include_once "includes/func.php";    
    
    $page = "contact.php";
    
    include_once "includes/header.inc.php";    
?>
<div class="breadcrumbs"><a href="index.php">Home</a> >></div>
<div class="page_intro_2">
    Contact
</div>

<br>               
<div class="section">                    
    <div class="section_heading">
        <div class="section_title">Give it to us straight</div>
    </div>  
    <div class="section_content">
       Give an opinion, report a bug, or just say hello.<br><br>
       All fields are optional.  But if you want to hear back from us, be sure to leave an email or a phone number.
       <br><br>
       
        <form id="contact_form" name="contact_form" method="post" action="sendContact.php" enctype="multipart/form-data">
            <div class="textbox_label">Name</div>
            <input type="text" name="txtContactUsName" id="txtContactUsName" maxlength="100" value="" class="txt_normal">
            <div style="clear:both"></div>
            <div class="textbox_error"></div>  
            
            <div class="textbox_label">Phone Number</div>
            <input type="text" name="txtContactUsPhone" id="txtContactUsPhone" maxlength="100" value="" class="txt_normal">
            <div style="clear:both"></div>            
            <div class="textbox_error"></div>  
            
            <div class="textbox_label">Email Address</div>
            <input type="text" name="txtContactUsEmail" id="txtContactUsEmail" maxlength="100" value="" class="txt_normal">
            <div style="clear:both"></div>
            <div class="textbox_error"></div>  
                        
            <div class="textbox_label">Description</div>
            <textarea cols=59 rows=10 id="txtContactUsMsg" name="txtContactUsMsg" wrap="soft" class="txt_normal"></textarea>
            <div style="clear:both"></div>
            <div class="textbox_error"></div>  

            <div class="textbox_label"></div>
            <button class="button" id="btnContactUs" name="btnContactUs" type="submit">Send</button>  
            <div style="clear:both"></div>
            <div class="textbox_error"></div>
        </form>
    </div>  
</div>

<?php
    include_once "includes/footer.inc.php";
?>