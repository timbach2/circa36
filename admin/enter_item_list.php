<?php
include('admin_header.php');
$T->set_var('page_title','Enter List of Fiesta Items');
$db = new DB_C36;

$T->set_file('detail','enter_item_list.ihtml');
$T->set_var('self',$PHP_SELF);
$T->set_var('item','');
$T->set_var('action','insert');
$T->set_var('id','');
$T->set_var('shipping','');

if($_GET['action']=='insert') {
	$db->query(sprintf("insert into fiesta_items (item,shipping) values('%s','%s')",
					addslashes($_GET['item']),
					addslashes($_GET['shipping'])
					)
			);
}

if($_GET['action']=='list') {
	ob_start();
	print "<h3>Items already entered:</h3>\n<a href=\"$PHP_SELF\">Enter New Items</a><hr>";
	$db->query('select * from fiesta_items order by item');
	while($db->next_record()) {
		printf("<div><a href=\"%s?action=modify&id=%s\">%s</a></div>\n",
				$PHP_SELF,
				$db->f('id'),
				htmlentities(stripslashes($db->f('item')))
				);
	}
	$T->set_var('body',ob_get_contents());
	ob_end_clean();
	$T->pparse('output','header');
	die;
}

if($_GET['action']=='modify') {
	$db->query(sprintf("select item,shipping from fiesta_items where id='%d'",$_GET['id']));
	$db->next_record();
	$T->set_var('item',		htmlentities(stripslashes($db->f('item'))));
	$T->set_var('shipping',	htmlentities(stripslashes($db->f('shipping'))));
	$T->set_var('id',$_GET['id']);
	$T->set_var('action','update');
	// fall through
}
	
if($_GET['action']=='update') {
	$db->query(sprintf("update fiesta_items set item='%s',shipping='%s' where id='%d'",
					addslashes($_GET['item']),
					addslashes($_GET['shipping']),
					$_GET['id']
					)
			);
}	


$T->set_var('body',$T->parse('output','detail'));
$T->pparse('output','header');
?>
</body>
</html>
