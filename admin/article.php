<?php
include('admin_header.php');
$db = new DB_C36;


$T->set_file('form','article.ihtml');
$T->set_var('article','');
$T->set_var('message','');

if(isset($_POST['article'])) {
	$db->query(sprintf("UPDATE articles set article='%s' where id='1'",
					addslashes($_POST['article'])
					)
			);
	$T->set_var('message','Update successful');
}
$db->query("select * from articles where id='1'");
$db->next_record();
$T->set_var('article',htmlentities(stripslashes($db->f('article'))));
$T->set_var('page_title','Update Index Page Text');
$T->set_var('body',$T->parse('output','form'));
$T->pparse('output','header');
page_close();
?>