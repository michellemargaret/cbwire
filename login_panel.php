    <img src="imgs/wait2.gif" id="login_wait">
    
        <div style="text-align:right;padding-bottom:5px;font-size:0.7em;">
            <a href="forgetpassword.php">Forgot your password?</a>
            <a href="register.php">Not registered?</a>        
        </div>
    <form id="login_form" method="post" action="yourinfo.php" enctype="multipart/form-data" autocomplete="off">
        Email 
        <input type="text" id="LoginEmailTextbox" name="LoginEmailTextbox" class="LoginPanelTextbox">
        <br><br>
        Password
        <input type="password" id="LoginPasswordTextbox" name="LoginPasswordTextbox" class="LoginPanelTextbox">
        <br><br>

        <a href="#" id="LoginPanelButton" name="LoginPanelButton" class="button">Login</a>
        <div id="LoginFailedMessage" class="alert" height="40px"></div>
    </form>
