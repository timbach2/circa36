<?php

$T->set_file('review','views/review_info.ihtml');
$T->set_file('rev_plain','views/review_plain_text.ihtml');

$T->set_var(array(
	'name' 			=> $_SESSION['info']['name'],
	'ship_name'		=> $_SESSION['ship_to']['name'],
	'address' 		=> $_SESSION['info']['addr1'],
	'ship_address' 	=> $_SESSION['ship_to']['addr1'],
	'city' 			=> $_SESSION['info']['city'],
	'ship_city' 	=> $_SESSION['ship_to']['city'],
	'state' 		=> $_SESSION['info']['state'],
	'ship_state' 	=> $_SESSION['ship_to']['state'],
	'zip' 			=> $_SESSION['info']['zip'],
	'ship_zip' 		=> $_SESSION['ship_to']['zip'],
	'email' 		=> $_SESSION['info']['email']
	));
	
$output .= $T->parse('output','review');
$review_plain_text = $T->parse('output','rev_plain');
?>