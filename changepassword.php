<?php 
    include_once "includes/func.php";    
    
    $page = "changepassword.php";
    
    include_once "includes/header.inc.php";    
?>

<div class="breadcrumbs"><a href="index.php">Home</a> >></div>
<Br>
                     <div class="section">                    
                        <div class="section_heading">
                            <div class="section_title">Change Password</div>
                            <div class="section_notes">Use the form below to change the password you use to login.
                            </div>
                        </div>
                        
                        <form id="changepw_form" name="changepw_form" method="post" action="" enctype="multipart/form-data">

                            <div class="textbox_label">Old Password</div>
                            <input type="password" name="txtOldPassword" id="txtOldPassword" maxlength="100" value="" class="txt_normal">
                            <div style="clear:both"></div> 

                            <div class="textbox_label">New Password</div>
                            <input type="password" name="txtNewPassword" id="txtNewPassword" maxlength="100" value="" class="txt_normal">
                            <div style="clear:both"></div> 

                            <div class="textbox_label">Re-enter New Password</div>
                            <input type="password" name="txtNewPasswordCopy" id="txtNewPasswordCopy" maxlength="100" value="" class="txt_normal">
                            <div style="clear:both"></div> 
                            <div class="textbox_error" id="txtPasswordCopyErr"></div>  
                            
                            <div class="textbox_label"></div> 
                            <div class="textbox_div">
                                <a href="#" class="button" id="btnChangePw">Change</a>
                                <a href="yourinfo.php" class="button" id="btnCancelChangePw">Cancel</a>
                            </div>
                            <br><br>
                        </form>
                         
                     </div>

<?php
    include_once "includes/footer.inc.php";
?>