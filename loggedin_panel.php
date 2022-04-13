<?php
    include_once "includes/func.php";
    
    if (isLoggedIn()) {
?>
<div style="display:block;padding:10px;">
    Hi <?php echo getUsersName(); ?>!
</div>
  <a href="yourinfo.php" class="loggedIn"><img src="imgs/home.gif" border="0"> Your listings</a><br>
  <a href="addnew.php?in=T" id="addNewT" class="loggedIn"><img src="imgs/personpodium.gif" border="0"> New Thing To Do</a><br>
  <a href="addnew.php?in=C" id="addNewC" class="loggedIn"><img src="imgs/dollar.gif" border="0"> New Classified</a><br>
  <a href="addnew.php?in=D" id="addNewD" class="loggedIn"><img src="imgs/filefolder.gif" border="0"> New Directory</a>
 
<?php
    }
?>
