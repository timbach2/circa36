<?php
include('admin_header.php');
$T->set_var('page_title','Manage Item List');
$db = new DB_C36;
$action= $_GET['action'] ? $_GET['action'] : 'list';

$T->set_file('list','manage_item_list.ihtml');
$T->set_block('list','ItemList','IList');
$T->set_var('line','');
$T->set_var('self',$_SERVER['PHP_SELF']);
$T->set_var('item','');
$T->set_var('id','');

$T->set_file('form','manage_item_form.ihtml');
$T->set_var('shipping','');

$T->set_file('choose','manage_item_choose_line.ihtml');
$T->set_block('choose','LineOption','LOption');
$T->set_var('value','');
$T->set_var('option','');

// default action unless overridden;
$T->set_var('action','insert'); 

// register the line when chosen
if($_GET['new_line']) {
	$line = (int)($_GET['new_line']);
	$db->query("select * from fiesta_lines where id='$line'");
	$db->next_record();
	$line_name = stripslashes($db->f('line')); 
	$sess->register("line");
	$sess->register("line_name");
	$action='list';
}

// no line set? get one.
if($sess->is_registered('line')==false||isset($_GET['init'])) {
	$db->query("select id,line from fiesta_lines order by id");
	while($db->next_record()) {
		$T->set_var('value',	$db->f('id'));
		$T->set_var('option',	stripslashes($db->f('line')));
		$T->parse('LOption','LineOption',true);
	}

	$T->set_var('body',$T->parse('output','choose'));
	$T->pparse('output','header');
	page_close();
	die;
}

// note: addslashing makes it safe for db insert, but db strips it when it stores it.
// changed a line in 'admin/index.php' to compensate. 

if($action=='insert') {
	$query=sprintf("insert into fiesta_items (item,shipping,line) values('%s','%s','%d')",
					addslashes($_GET['item']),
					addslashes($_GET['shipping']),
					$line
					);
	//print $query;
	$db->query($query);
}

if($action=='delete') {
	$db->query(sprintf("delete from fiesta_items where id='%d'",$_GET['id']));
	$action='list';
}

if($action=='modify') {
	$db->query(sprintf("select item,shipping from fiesta_items where id='%d'",$_GET['id']));
	$db->next_record();
	$T->set_var('item',		htmlentities(stripslashes($db->f('item'))));
	$T->set_var('shipping',	htmlentities(stripslashes($db->f('shipping'))));
	$T->set_var('id',$_GET['id']);
	$T->set_var('action','update');
	// fall through
}
	
if($action=='update') {
	$db->query(sprintf("update fiesta_items set item='%s',shipping='%s',line='%d' where id='%d'",
					addslashes($_GET['item']),
					addslashes($_GET['shipping']),
					$line,
					$_GET['id']
					)
			);
	$action='list';	
}	

if($action=='list') {
	$T->set_var('line_name',	$line_name);
	$db->query("select * from fiesta_items where line='$line' order by item");
	while($db->next_record()) {
		$class = $class=='darkRow' ? 'lightRow' : 'darkRow';
		$T->set_var('class',	$class);
		$T->set_var('id',	$db->f('id'));
		$T->set_var('item',	stripslashes($db->f('item')));
		$T->parse('IList','ItemList',true);
	}
	$T->set_var('body',$T->parse('output','list'));
	$T->pparse('output','header');
	page_close();
	die;
}

// default is an insert
$T->set_var('line_name',$line_name);
$T->set_var('body',$T->parse('output','form'));
$T->pparse('output','header');
page_close();
?>
</body>
</html>
