<?php 
    include_once "includes/func.php"; 
        
    if (isLoggedIn()) { 
        header( 'Location:yourinfo.php');
        exit();
    }
    
    $page = "update_listing_pre.php";
    if (isset($_SESSION["editListingID"])) {
        unset($_SESSION["editListingID"]);
    }
    if (isset($_SESSION["editListingType"])) {
        unset($_SESSION["editListingType"]);
    }
    
?>
<!doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN">
<html lang='en'>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=9" /> 
        <LINK href="css/styles.css" rel="stylesheet" type="text/css">
        <LINK href="css/estyles.css" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="https://ajax.microsoft.com/ajax/jQuery/jquery-1.4.2.min.js"></script>
        <script language="javascript" type="text/javascript" src="js/scripts.js"></script>
        <script language="javascript" type="text/javascript" src="js/ejquery.js"></script>

           
        <script language="javascript" type="text/javascript">
            function initializeValues() { 
                
            }
            
            $(document).ready(function(){
                    $("#loginstrip").show();
                    $("#loggedinstrip").hide();   
            })         
        </script>
	<script src="js/jqueryui/jquery.ui.core.min.js"></script>
	<script src="js/jqueryui/jquery.ui.widget.min.js"></script>
	<script src="js/jqueryui/jquery.ui.datepicker.js"></script>
    </head>
    <body><img src="imgs/overlay.gif" id="overlay_back"></div><div id="divLoading"><img src="imgs/wait2.gif"></div>
      <div class="container" id="container">
            <?php include_once "includes/topmenu.inc.php"; ?>
            <form id="query_update" method="post" action="update_listing.php" enctype="multipart/form-data"></form>
            <form id="update_listing_pre" method="post" action="save_pre.php" enctype="multipart/form-data">
                <div class="maincell">
                    <div class="breadcrumbs"><a href="index.php">Home</a> >></div>
                    <div class="page_intro_2">
                        Tell us all about it
                    </div>
                    
                    <br>               
                    <div class="section">                    
                        <div class="section_heading">
                            <div class="section_title">Did you know...</div>
                            <div class="section_notes"></div>
                        </div>  
                        <div class="section_content">
                            <div class="list_heading">Registering has advantages</div>
                            Registration lets you log in to track your listings, make changes,  
                            and get your listings published to the public more quickly.  
                            <br><br>
                            <a href="register.php">Click here to register</a> or <a href="login.php">here to login</a>.
                            <br><br>
                            You can use the form below to add listings without registering, but keep in mind
                            <ul>
                                <li>You will not be able to edit the listing after you enter it</li>
                                <li>The listing will need to be approved by an administrator before it is visible to the public</li>
                            </ul>
                        </div>
                    </div>
                    
                    <br>               
                    <div class="section" id="section_pre">                    
                        <div class="section_heading">
                            <div class="section_title">Not ready to register yet?</div>
                            <div class="section_notes">Please provide an email address so we can let you know when your listing has been approved.</div>
                        </div>                    
                        <div id="add_pre" class="add_pre">                         
                            <div class="textbox_label">Email Address</div>
                            <input type="text" name="txtEmailPre" id="txtEmailPre" maxlength="100" value="" class="txt_normal">
                            <div style="clear:both"></div>
                            <div class="textbox_error" id="txtEmailErrPre"></div>
                            <br>
                            <div class="textbox_label">Listing Type</div>    
                            <div id="pre_select_buttons" class="select_buttons">
                                <a href="#" class="button_select">Thing To Do</a>
                                <a href="#" class="button_select">Directory</a>
                                <a href="#" class="button_select">Classified</a>                                
                            </div>
                                              
                            <button class="button" id="btnPreGo" type="submit">Go >></button>                            
                            <div style="clear:both"></div>
                            <br>
                            <input id="listingtype" name="listingtype" type="text" style="display:none;">                       
                        </div>
                    </div>
                </div>  
            </form>
                     
       </div>
    
            <div id="footerLinks">
                <a href="about.php">About</a>
                <a href="contact.php">Contact</a>
                <a href="advertise.php">Advertise</a>
                <a href="termsofuse.php">Privacy Policy / Terms of Use</a>
                <a href="faq.php">FAQ</a>
            </div>
    </body>
</html>
