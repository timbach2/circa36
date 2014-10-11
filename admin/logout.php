<?php 
// include("prepend.php3");

// We use the following features:
//  sess   for session variables
//  auth   for user authentication (yes, you need to be logged in to log out :-)
page_open(array("sess" => "C36_Session",
		"auth" => "C36_Auth",
		"perm" => "C36_Perm"));

// admin header.php

$T = new template;
$T->set_file('header','admin_header.ihtml');

$T->set_var('body','');
$T->set_var('page_title','Logout');
$T->pparse('output','header');
?>
<html>
<body bgcolor="#ffffff">

  <h1>Logout</h1>
  
  You have been logged in as <b><?php print $auth->auth["uname"] ?></b> with
  <b><?php print $auth->auth["perm"] ?></b> permission. Your authentication
  was valid until <b><?php print date("d. M. Y, H:i:s", $auth->auth["exp"])
  ?></b>.<p>
  
  This is all over now. You have been logged out.
</body>
</html>
<?php
  $auth->logout();
  page_close();
 ?>
<!-- $Id: logout.php3,v 1.1.1.1 2000/04/17 16:40:06 kk Exp $ -->
