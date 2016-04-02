
<html>
     <body>
		
	 <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
	 	<input type="file" name="image1">
	 	<input type="submit" name="upload_image" value="Upload">
	 </form>

     </body>
</html>


<?php
if(isset($_FILES['image1'])){
//echo"<pre>",print_r($_FILES);exit;
$upload_image = $_FILES["image1"]["name"];

$folder = $_SERVER['DOCUMENT_ROOT']."/images/";

move_uploaded_file($_FILES["image1"]["tmp_name"], "$folder".$_FILES["image1"]["name"]);

$file = $_SERVER['DOCUMENT_ROOT'].'/images/'.$_FILES["image1"]["name"];


// We have to save image in the directory first and then we do image manipulation. You can use any path of directory from where you want to save the image.

$uploadimage = $folder.$_FILES["image1"]["name"];
$newname = $_FILES["image1"]["name"];

// Set the resize_image name
 $resize_image = $folder.$newname."_resize.jpg"; 
$actual_image = $folder.$newname.".jpg";

// It gets the size of the image
list($width,$height) = getimagesize($uploadimage);


// It makes the new image width of 350
$newwidth = 350;


// It makes the new image height of 350
$newheight = 350;


// It loads the images we use jpeg function you can use any function like imagecreatefromjpeg
$thumb = imagecreatetruecolor($newwidth,$newheight);
$source = imagecreatefromjpeg($resize_image);


// Resize the $thumb image.
imagecopyresized($thumb,$source,0,0,0,0,$newwidth,$newheight,$width,$height);


// It then save the new image to the location specified by $resize_image variable

imagejpeg( $thumb,$resize_image,100 ); 

// 100 Represents the quality of an image you can set and ant number in place of 100.
 //   Default quality is 75


$out_image=addslashes(file_get_contents($resize_image));

// After that you can insert the path of the resized image into the database
/*
mysql_connect(' localhost ' , root ,' ' );
mysql_select_db(' image_database ');
$insertquery = " insert into resize_images values('1,$out_image') ";
$result = mysql_query( $insertquery );
*/

}else{
    echo "no files";
}
?>


You can learn from this site all the Image Manipulating functons in PHP.

That's all, this is how to resize the image before uploading with the help of PHP, GD Library HTML and MySQL. 