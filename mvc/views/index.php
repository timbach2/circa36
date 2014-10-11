<?php

$sess->unregister("search_type");
$sess->unregister("color");
$sess->unregister("item");
$sess->unregister("line");
$sess->unregister("id");
$sess->unregister("count");
$sess->unregister("searchQuery");

$T->set_var('page_title','Circa 36');
$T->set_var('date',date("l F jS, Y"));
$T->set_file('body','views/index.ihtml');

$T->set_block('body','ColorOption','COption');
$T->set_block('body','ItemOption','IOption');
$T->set_block('body','LineOption','LOption');

$T->set_var('option','');
$T->set_var('description','');
$T->set_var('selected','');

// fill in colors
$db->query("select id,color from fiesta_colors where line='1' order by color");
while($db->next_record()) {
	$T->set_var('option',	$db->f('id'));
	$T->set_var('description',stripslashes($db->f('color')));
	$T->parse('COption','ColorOption',true);
}

// fill in all items fiesta offers
$db->query("select id,item from fiesta_items where line='1' order by item");
while($db->next_record()) {
	$T->set_var('option',	$db->f('id'));
	$T->set_var('description',stripslashes($db->f('item')));
	$T->parse('IOption','ItemOption',true);
}

// fill in lines of Fiesta
$db->query("select id,line from fiesta_lines order by id");
while($db->next_record()) {
	$T->set_var('option',	$db->f('id'));
	$T->set_var('description',stripslashes($db->f('line')));
	$T->parse('LOption','LineOption',true);
}

// how many items for sale?
$db->query("SELECT COUNT(*) AS total FROM sale_items where title!='' and sold_date='0000-00-00'");
$db->next_record();
$T->set_var('number_of_items',$db->f('total'));

// print article

$db->query("select * from articles where id='1'");
$db->next_record();
$T->set_var('article',nl2br(stripslashes($db->f('article'))));

$output = $T->parse('output','body');


?>