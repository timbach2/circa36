<?php
page_open(array("sess" => "C36_Session",
		"auth" => "C36_Auth",
		"perm" => "C36_Perm"));
$auth->login_if($auth->auth['uid']=='nobody');
$perm->check("admin");

// admin header.php

$T = new template;
$T->set_file('header','admin_header.ihtml');

error_reporting(E_ALL ^ E_NOTICE);
?>