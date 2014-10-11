<?php

$T->set_file('body','views/announce.ihtml');
$T->set_block('body','AnnounceList','AList');

$T->set_var('from','');
$T->set_var('date','');
$T->set_var('time','');
$T->set_var('announcement','');

$db->query("select * from announcements order by timestamped desc");
while($db->next_record()) {
	$myTime = strtotime($db->f('timestamped'));
	$T->set_var('from',	$db->f('from'));
	$T->set_var('date',	date("m/d/Y",$myTime));
	$T->set_var('time',	date("h:i:s A",$myTime));
	$T->set_var('announcement',stripslashes($db->f('announcement')));
	$T->parse('AList','AnnounceList',true);
}

$output = $T->parse('output','body');

?>