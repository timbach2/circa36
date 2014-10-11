<?php

include('admin_header.php');
$T->set_var('page_title','upload a picture');

$T->set_file('form','upload.ihtml');
$T->set_var('self',$PHP_SELF);

function mogrify($max,$tmpid) {
	$size = GetImageSize($_FILES['form_data']['tmp_name']);
	$image_width = $size[0];
	$image_height= $size[1];
	if($image_height>$max || $image_width>$max) {
		if($image_hight>$image_width) {
			$sizefactor = (double)($max/$image_height);
		} else {
			$sizefactor = (double)($max/$image_width);
		}
		$newwidth = (int)($image_width*$sizefactor);
		$newheight= (int)($image_height*$sizefactor);
		
		$newsize = $newwidth . "x" . $newheight;
		//$cmd = sprintf("/usr/X11R6/bin/convert -geometry %s %s %s 2>&1",
		$cmd = sprintf("/usr/local/bin/convert -geometry %s %s %s 2>&1",
			  		$newsize,
			  		$_FILES['form_data']['tmp_name'],
			  		'/tmp/'.$tmpid // note: save to a unique_id(), pass id along
			  		);
		exec($cmd,$exec_output,$exec_retval);
		if($exec_retval>0) {
			print "ERROR: exec() error: $exec_output[0]";
		} else {
			print "Image was resized from " . $image_width . "x" . 
				$image_height . " to $newsize :)<br>";
			$image_height = $newheight;
			$image_width  = $newwidth;
		}
	}
	$ret_val['height'] = $image_height;
	$ret_val['width']  = $image_width;
	
	return $ret_val;
}
	
if($_POST['action']=='upload') {
	$db = new DB_C36;
	$temp = uniqid("c36med");
	$image = mogrify(300,$temp);
	$idata = addslashes(fread(fopen('/tmp/'.$temp,"r"), filesize($form_data)));

	$temp = uniqid("c36thumb");
	$thumb = mogrify(50,$temp);
	$tdata = addslashes(fread(fopen('/tmp/'.$temp,"r"), filesize($form_data)));

	$db->query(sprintf("insert into sale_items " . 
				"(bin_data,filewidth,fileheight,filetype," . 
				"th_bin_data,th_filewidth,th_fileheight,th_filetype) " . 
				"values('%s','%d','%d','%s','%s','%d','%d','%s')",
				$idata,$image['width'],$image['height'],$form_data_type,
				$tdata,$thumb['width'],$thumb['height'],$form_data_type
				)
			);
	$id = mysql_insert_id();
	print "This file has the following Database ID: <b>$id</b>";
} 

$T->set_var('body',$T->parse('output','form'));

$T->pparse('output','header');
page_close();

?>
