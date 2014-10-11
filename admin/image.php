<?php
$id = (int)($_GET['id']);

if($id) {
	$db = new DB_C36;
	$db->query(sprintf("select bin_data,filetype from sale_items where id='%d'",$id));
	$db->next_record();
	
	$data=$db->f('bin_data');
	$type=$db->f('filetype');
	
	header("Content-type: $type");
	echo $data;
}
?>