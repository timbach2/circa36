<?php

$T->set_file('body','views/contact.ihtml');
$T->set_block('body','ContactForm','CForm');
$T->set_block('body','ContactThanks','CThanks');

$T->set_var('CForm','');
$T->set_var('CThanks','');

if(@$_POST['message']) {
	
	// BEWARE  SECURITY HOLE!!!!
	
	//$to = 'tim@timlyd.com';
	$to = 'george@circa36.com';
	
	$subject = stripslashes($_POST['subject']);
	$message = stripslashes($_POST['message']);
	
	// ensure only a valid email; no extraneous headers are allowed!
	// see http://www.securephpwiki.com/index.php/Email_Injection
	
	preg_match("/^([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})$/",$_POST['from'],$m);
	
	if(@$m[1]) {
		
		$from = 'from: '.$m[1];
		
		if(mail($to,$subject,$message,$from)) { 
			$output = $T->parse('CThanks','ContactThanks');
		} else {
			$output = '<br /><br />Sorry, email failed...  You may need to send an email manually.  To do this, send the email to george at this domain; ie, at circa36.com (email munged for spam protection)<br /><br />'; 
		}
	} else {
		$output = 'Sorry, your email address was not accepted as a valid email address.';
	}
	
} else {
	
	// default form
	$output = $T->parse('CForm','ContactForm');
}

?>