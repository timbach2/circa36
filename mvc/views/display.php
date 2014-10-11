<?php

$id = (int)($argv[1]);

//$T->set_var('page_title','Circa 36 For Sale Items');

$T->set_file('forsale','views/display.ihtml');
$T->set_block('forsale','ShowAdd','SAdd');
$T->set_var('item_name','');
$T->set_var('item_number','');
$T->set_var('item_description','');
$T->set_var('price','');
$T->set_var('shipping','');
$T->set_var('total','');
$T->set_var('id','');
$T->set_var('height','');
$T->set_var('width','');

$db->query(sprintf("SELECT a.*,b.shipping FROM sale_items as a " . 
				"LEFT JOIN fiesta_items as b on a.item=b.id " . 
				"WHERE a.id='%d'",$id));
if(!$db->next_record()) {
	// send message to search engine that the page is no longer found.
	header("HTTP/1.0 404 Not Found");
    header("Status: 404 Not Found");
    $output = '<h2>Sorry, page not found.</h2>';
    return;
}

$temp_title = stripslashes($db->f('title'));
//preg_replace("Fiesta"," &#174;",$temp_title);

$T->set_var('page_title',	$temp_title);

$T->set_var('id',			$db->f('id'));
$T->set_var('item_name',	$temp_title);
$T->set_var('item_number',	'C36'.sprintf("%04d",$db->f('id')));
$T->set_var('item_description',stripslashes($db->f('description')));
$T->set_var('price',		sprintf("\$%7.2f",$db->f('price')));
$T->set_var('shipping',		sprintf("\$%7.2f",$db->f('shipping')));
$T->set_var('total',		sprintf("\$%7.2f",(float)($db->f('price')) + (float)($db->f('shipping'))));
$T->set_var('width',		$db->f('filewidth'));
$T->set_var('height',		$db->f('fileheight'));
$T->set_var('cart',			sizeof(@$cart));
if($db->f('sold_date')!='0000-00-00') {
	$T->set_var('SAdd','');
} else {
	$T->parse('SAdd','ShowAdd');
}

$replace = array(
	"/<.+>/",
	"/[^a-zA-Z0-9 ]/"
	);
$meta = preg_replace($replace,'',stripslashes($db->f('description')));
$meta = preg_replace('/  /',' ',$meta);
$T->set_var('meta_description',$meta);
$output = $T->parse('output','forsale');

?>