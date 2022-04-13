<?php 
    include_once "includes/func.php";    
    
    $page = "register.php";
    
    include_once "includes/header.inc.php";    
?>

<div class="breadcrumbs"><a href="index.php">Home</a> >></div>
<br>
                     <div class="section">                    
                        <div class="section_heading">
                            <div class="section_title">Register</div>
                            <div class="section_notes">Registration lets you log in to track your listings, make changes,  
                            and get your listings published to the public more quickly.
                            </div>
                        </div>
                        
                        <form id="register_form" name="register_form" method="post" action="" enctype="multipart/form-data">
                            <div class="textbox_label">Name</div>
                            <input type="text" name="txtName" id="txtName" maxlength="100" value="" class="txt_normal">
                            <div style="clear:both"></div> 
                            <div class="textbox_error" id="txtNameErr"></div>

                            <div class="textbox_label">Email Address</div>
                            <input type="text" name="txtEmail" id="txtEmail" maxlength="100" value="" class="txt_normal">
                            <div style="clear:both"></div>

                            <div class="textbox_label">Re-enter Email Address</div>
                            <input type="text" name="txtEmailCopy" id="txtEmailCopy" maxlength="100" value="" class="txt_normal">
                            <div style="clear:both"></div>
                            <div class="textbox_error" id="txtEmailCopyErr"></div>

                            <div class="textbox_label">Password</div>
                            <input type="password" name="txtPassword" id="txtPassword" maxlength="100" value="" class="txt_normal">
                            <div style="clear:both"></div> 

                            <div class="textbox_label">Re-enter Password</div>
                            <input type="password" name="txtPasswordCopy" id="txtPasswordCopy" maxlength="100" value="" class="txt_normal">
                            <div style="clear:both"></div> 
                            <div class="textbox_error" id="txtPasswordCopyErr"></div>  
                            
                            <div class="textbox_label"></div>
                            <input type="checkbox" name="chkAgree" id="chkAgree" class="chk_normal">
                            I agree to the CB Wire 
                            <a href="termsofuse.php#terms" target="_blank" onclick="javascript:popupwindow('termsofuse.php#terms'); return false;">Terms of Use</a> and 
                            <a href="termsofuse.php#privacy" target="_blank" onclick="javascript:popupwindow('termsofuse.php#privacy'); return false;">Privacy Policy</a>
                            <div style="clear:both"></div> 
                            <div class="textbox_error" id="txtPasswordCopyErr"></div>  
                            
                            <div class="textbox_label"></div> 
                            <div class="textbox_div">
                                <a href="#" class="button" id="btnRegister">Register</a>
                            </div>
                            <br><br>
                        </form>
                         
                     </div>

<?php
    include_once "includes/footer.inc.php";
?>