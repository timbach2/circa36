<?php
include("admin_header.php");
$T->set_var('page_title','Enter Announcements');
$db = new DB_C36;
$action = $_POST['action'] ? $_POST['action'] : $_GET['action'];
$T->set_file('form','announce_form.ihtml');
$T->set_var('message','');
$T->set_var('action','');

$T->set_file('list','announce_list.ihtml');
$T->set_block('list','ListRow','LRow');
$T->set_var('LRow','');
$T->set_var('self',$PHP_SELF);

if($action=='new') {
	$T->set_var('action','insert');
	$T->set_var('body',$T->parse('output','form'));
	$T->pparse('output','header');
	page_close();
	die;
}

if($_POST['action']=='insert') {
	$db->query(sprintf("insert into announcements (timestamped,announcement) " . 
					"values('%s','%s')",
					date("Y-m-d H:i:s"),
					addslashes($_POST['message'])
					)
			);
}

if($action=='edit') {
	$db->query(sprintf("select * from announcements where id='%d'",$_GET['id']));
	$db->next_record();
	$T->set_var('id',(int)($_GET['id']));
	$T->set_var('action','update');
	$T->set_var('message',htmlentities(stripslashes($db->f('announcement'))));
	$T->set_var('body',$T->parse('output','form'));
	$T->pparse('output','header');
	page_close();
	die;
}

if($_POST['action']=='update') {
	$db->query(sprintf("update announcements set announcement='%s' where id='%d'",
					addslashes($_POST['message']),
					$_GET['id']
					)
			);
}

if($action=='delete') {
	$db->query(sprintf("delete from announcements where id='%d'",$_GET['id']));
	
}


// default screen is the list

$db->query("select * from announcements order by timestamped");
$empty = true;
while($db->next_record()) {
	$class = $class=='darkRow' ? 'lightRow' : 'darkRow';
	$T->set_var('class',$class);
	$T->set_var('id',$db->f('id'));
	$T->set_var('date',date("m/d/Y h:m:s",strtotime($db->f('timestamped'))));
	$T->set_var('sample_text',	substr(stripslashes($db->f('announcement')),0,50));
	$T->parse('LRow','ListRow',true);
	$empty = false;
}
if($empty) { $T->set_var('LRow','No Announcements in database...'); }

$T->set_var('body',$T->parse('output','list'));
$T->pparse('output','header');
page_close();
?>