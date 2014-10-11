<?php
include('admin_header.php');
$T->set_var('page_title','Manage Lines List');
$db = new DB_C36;
$action= @$_GET['action'] ? $_GET['action']: 'list';

$T->set_file('list','manage_lines_list.ihtml');
$T->set_block('list','ItemList','IList');
$T->set_var('line','');
$T->set_var('self',$PHP_SELF);
$T->set_var('color','');
$T->set_var('id','');

$T->set_file('form','manage_lines_form.ihtml');
$T->set_var('shipping','');

// default action unless overridden;
$T->set_var('action','insert'); 

if($action=='insert') {
	$db->query(sprintf("insert into fiesta_lines (line) values('%s')",
					addslashes($_GET['line'])
					)
			);
}

if($action=='delete') {
	$db->query(sprintf("delete from fiesta_lines where id='%d'",$_GET['id']));
	//print "that would have deleted this line...";
	$action='list';
}

if($action=='modify') {
	$db->query(sprintf("select line from fiesta_lines where id='%d'",$_GET['id']));
	$db->next_record();
	$T->set_var('line',		htmlentities(stripslashes($db->f('line'))));
	$T->set_var('id',		$_GET['id']);
	$T->set_var('action','update');
	// fall through
}
	
if($action=='update') {
	$db->query(sprintf("update fiesta_lines set line='%s' where id='%d'",
					addslashes($_GET['line']),
					$_GET['id']
					)
			);
	$action='list';	
}	

if($action=='list') {
	$db->query("select * from fiesta_lines order by line");
	while($db->next_record()) {
		$class = $class=='darkRow' ? 'lightRow' : 'darkRow';
		$T->set_var('class',	$class);
		$T->set_var('id',	$db->f('id'));
		$T->set_var('line',	stripslashes($db->f('line')));
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
