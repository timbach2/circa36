<?php

include('admin_header.php');
$T->set_var('page_title','Show Uploaded Pictures');

ob_start();
?>
<form name="form1" method="get">
Please select id for picture: <input type="text" name="id" size="5">
<input type="submit">
</form>
<script language="javascript">
document.form1.id.focus();
</script>
<div align="right"><a href="upload.php">Upload</a> a picture</div>
<hr>
<img src="image.php?id=<? echo $id; ?>">
<?php
$data = ob_get_contents();
ob_end_clean();

$T->set_var('body',$data);
$T->pparse('output','header');
page_close();
?>
