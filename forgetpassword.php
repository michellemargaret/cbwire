<?php 
    include_once "includes/func.php";  
    include_once "includes/funcEmail.php";  
    
    $page = "forgetpassword.php";
    
         
    include_once "includes/header.inc.php";   
?>
<div class="breadcrumbs"><a href="index.php">Home</a> >></div>
<Br>

                     <div class="section">                    
                        <div class="section_heading">
                            <div class="section_title">Forget Password</div>
                            <div class="section_notes">
                                Using the form below, a new password will be generated 
                                automatically and sent to your email.
                            </div>
                        </div>
                        
                        <form id="forgetpassword_form" name="forgetpassword_form" method="post" action="includes/forget_password.php" target="_blank" enctype="multipart/form-data">

                            <div class="textbox_label">Email Address</div>
                            <input type="text" name="txtForgetEmail" id="txtForgetEmail" maxlength="100" value="" class="GeneralTextbox">
                            <div style="clear:both"></div>
                            
                            <div class="textbox_label"></div> 
                            <div class="textbox_div">
                                <button id="btnForgetPassword" name="btnForgetPassword">Send</button>
                            </div>
                            <br><br>
                        </form>
                         
                     </div>

<?php
    include_once "includes/footer.inc.php";
?>