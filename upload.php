<?php
    include_once "editincludes/resize-class.php";
    include_once "includes/connect.php";
    $link = connect();
    $page = "upload.php";
    $userid = get_user_id();
?>
<script type="text/javascript" src="js/prototype.js"></script>

<script type="text/javascript">

<!--
// works in firefox var par = parent.content.document;
var par = window.parent.document;
var board = par.getElementById("pictureMessage");
var images = par.getElementById("pictureDisplayBox");

function removeChildrenOf(s) {
while (s.hasChildNodes())
s.removeChild(s.childNodes[0]);
}

function message(msg, color) {
var message = par.createTextNode(msg);
board.setAttribute("style", "color: " + color);
board.appendChild(message);
}

function upload() {
var loader = par.createElement("img");
loader.setAttribute("src", "imgs/progress.gif");
removeChildrenOf(board);
board.appendChild(loader);
document.forms['photoform'].submit();
}

function addPhoto(source, pictureID, inWidth, inHeight) {
var img = par.createElement("img");
img.setAttribute("src", "uploads/" + source);
removeChildrenOf(images);
images.appendChild(img);

images.style.width = inWidth;
images.style.height = inHeight;
par.getElementById("removeImage").style.display = "inline";
par.getElementById("txtPictureID").value = pictureID;
}

<?php
if(isset($_FILES['file'])) {
    sleep(1);
    echo "removeChildrenOf(board);";
    $ext = substr($_FILES['file']['name'], strrpos($_FILES['file']['name'], '.') + 1);

    if((strtoupper($ext) == "JPG" || strtoupper($ext) == "GIF") || (strtoupper($ext) == "PNG" || strtoupper($ext) == "BMP")) {
        $time = time();      
        
        copy($_FILES['file']['tmp_name'],'uploads/' . $time . "b." . $ext);

        // *** 1) Initialise / load image
        $resizeObj = new resize('uploads/' . $time . "b." . $ext);

        // *** 2) Resize image (options: exact, portrait, landscape, auto, crop)
        $resizeObj -> resizeImage(200, 200, 'auto');

        // *** 3) Save image
        $resizeObj -> saveImage('uploads/' . $time . "s." . $ext, 100);
        
        list($swidth, $sheight, $stype, $sattr) = getimagesize('uploads/' . $time . "s." . $ext);
        list($bwidth, $bheight, $btype, $battr) = getimagesize('uploads/' . $time . "b." . $ext);

         $sql_query = sprintf("INSERT INTO `cbwire`.`pictures` (
                                `thumbnail` ,
                                `large` ,
                                `userid` ,
                                `insertDate` ,
                                `smallWidth` ,
                                `smallHeight` ,
                                `bigWidth` ,
                                `bigHeight` 
                                )
                                VALUES (
                                '%s','%s','%s','%s','%s','%s','%s','%s'
                                );",
			mysql_real_escape_string($time . "s." . $ext),
			mysql_real_escape_string($time . "b." . $ext),
			mysql_real_escape_string($userid),
                        date("Y-m-d H:i:s"),
                        mysql_real_escape_string($swidth),
                        mysql_real_escape_string($sheight),
                        mysql_real_escape_string($bwidth),
                        mysql_real_escape_string($bheight));

        mysql_query($sql_query) or log_error($sql_query, mysql_error(), $page, false);	
        
        echo "message('The photo was uploaded successfully.', '#22AA44'); ";
        echo "addPhoto('" . $time . "s." . $ext . "', " . mysql_insert_id() . ", " . $swidth . ", " . $sheight . ");";
    } else {
        echo "message('Invalid format! The valid formats are: JPG, GIF, PNG and BMP.', '#ff4444'); ";
    }
}
?>

//-->
</script>

<form action="" method="post" id="photoform" enctype="multipart/form-data">
<input type="file" name="file" onchange="upload()"/>
</form>