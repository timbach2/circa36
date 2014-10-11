<?php
include('admin_header.php'); 


$db = new DB_C36;

function item_link($m) {
	$out = array();
	foreach($m[1] as $k=>$v) {
		
		$out[] = sprintf(
			"<a href='/admin/index.php?action=modify&id=%d'>%s</a>",
			$m[1][$k],
			$m[2][$k]
			);
	}
	
	$out = implode('<br>',$out);
	return $out;
}

if($_GET['id'] && $_GET['action']=='reset') {
	
	// need to use a model!!!!
	
	$query=sprintf("update sale_items set sold_date='0000-00-00' WHERE id='%d'",
			$_GET['id']
			);

	print $query;
	// could also go ahead and delete the item from the database, since if it is being reset, it didn't sell?
	
	
} else if($_GET['id']) {
	$db->query(sprintf("select order_text from accounting where id='%d'",
					$_GET['id']
					)
			);
	while($db->next_record()) {
		$body .= "<tr><td>".nl2br($db->f('order_text'))."</td></td>";
	}

	$body = "<table border=1>" . $body . "</table>";

	$T->set_var('page_title','Review Orders');
	$T->set_var('body',$body);
	$T->pparse('output','header');
	page_close();
	die;
}

// default page
$net60 = date("Y-m-d 01:01:01",mktime(0,0,0,date("m")-2,date("d"),date("Y")));
$db->query(sprintf(
	"select id,unix_timestamp(order_date) as order_date,order_text " . 
	"from accounting " . 
	"where order_date>'%s' " . 
	"order by order_date DESC",
	$net60
	));

$body .= 
	'<h1>Orders for the last 60 days</h1>' . 
	'<table cellpadding="5" cellspacing="0" border="1"><tr class="turq"><th>View Order</th><th>View or Reset Item</th><th>Buyer</th><th>Total</th></tr>';

while($db->next_record()) {
	
	$class = $class=='darkRow' ? 'lightRow' : 'darkRow';
	
	// use preg_match_all, then (im)explode the match with <br>?
	preg_match_all("/ID:[^0-9]*([0-9]+)[^D]*Descr:\s+([^\n]+)/",$db->f('order_text'),$m);
	preg_match("/Personal Info[^a-zA-Z]+([^\n]+)/m",$db->f('order_text'),$n);
	preg_match("/Total Order[^0-9]*([0-9]+\.?[0-9]*)/",$db->f('order_text'),$total);
	
	$body .= sprintf(
		"<tr class='%s'><td valign='top'><a href='%s?id=%d'>%s</a></td>" . 
		"<td valign='top'>%s</td>" . 
		"<td valign='top'>%s</td>" . 
		"<td valign='top' align='right'>&#36;%s</td></tr>\n",
		$class,
		$_SERVER['PHP_SELF'],
		$db->f('id'),
		date("M d g:i a",$db->f('order_date')),
		item_link($m),
		$n[1],
		$total[1]
		);
}



$body .= '</table>';

$T->set_var('page_title','Review Orders');
$T->set_var('body',$body);
page_close();
$T->pparse('output','header');
?>